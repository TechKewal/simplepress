<?php
/*
Simple:Press
Topic Subscriptions Plugin Digest Routines
$LastChangedDate: 2017-11-12 17:27:20 -0600 (Sun, 12 Nov 2017) $
$Rev: 15584 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_subscriptions_do_digest_entry($newpost) {
	# no notifications for posts in moderation
	if ($newpost['poststatus']) return;

	# save off a new digest report if enabled
	$subs = SP()->options->get('subscriptions');
	if ($subs['digestsub']) {
		# abort if no digest subscribers
		$digestUsers = ($subs['digestforce']) ? array() : SP()->DB->select('SELECT user_id FROM '.SPMEMBERS.' WHERE subscribe_digest=1', 'col');
		if (empty($digestUsers) && !$subs['digestforce']) return;

		# get forum subscriptions
    	$forumSubs = SP()->activity->get_col('col=uid&type='.SPACTIVITY_SUBSFORUM.'&item='.$newpost['forumid']);

        # for posts, see if only notifying new topics
        if (!empty($forumSubs) && $newpost['action'] == 'post') {
            foreach ($forumSubs as $id => $user) {
        		$options = SP()->memberData->get($user, 'user_options');
                if (isset($options['subnewtopics']) && $options['subnewtopics']) {
                    unset($forumSubs[$id]);
                }
            }
            $forumSubs = array_values($forumSubs);
        }

		# get topic subscriptions
  	    $topicSubs = SP()->activity->get_col('col=uid&type='.SPACTIVITY_SUBSTOPIC.'&item='.$newpost['topicid']);

		if (empty($forumSubs) && empty($topicSubs)) return;

		# offer option to notify self
		$notify_self = apply_filters('sph_subscriptions_digest_notify_self', false, $newpost['userid']);

		# abort if no digest users subscribed to this topic
		$digestSubscribed = array();
		if ($forumSubs) {
			foreach ($forumSubs as $sub) {
				if (($sub != $newpost['userid'] || $notify_self == true) && ($subs['digestforce'] || in_array($sub, $digestUsers))) $digestSubscribed[] = $sub;
			}
		}
		if ($topicSubs) {
			foreach ($topicSubs as $sub) {
				if (($sub != $newpost['userid'] || $notify_self == true) && ($subs['digestforce'] || in_array($sub, $digestUsers))) $digestSubscribed[] = $sub;
			}
		}
		$digestSubscribed = array_unique($digestSubscribed);
		if (empty($digestSubscribed)) return;

		# save for database
		$digestSubscribed = serialize($digestSubscribed);

		# get the forum/topic name for display
		$forumname = SP()->filters->esc_sql(SP()->DB->table(SPFORUMS, "forum_id = {$newpost['forumid']}", 'forum_name'));
		$topicname = SP()->filters->esc_sql($newpost['topicname']);

		# we have digest subscribers for this topic so create digest entry
		$sql = 'INSERT INTO '.SPDIGEST.' ';
		$sql.= '(forum_id, forum_name, topic_id, topic_name, post_id, subscriptions, permalink) ';
		$sql.= "VALUES ({$newpost['forumid']}, '$forumname', {$newpost['topicid']}, '$topicname', {$newpost['postid']}, '$digestSubscribed', '{$newpost['url']}')";
		SP()->DB->execute($sql);
	}
}

function sp_subscriptions_do_cron_digest() {
	# just abort if not doing digests
	$options = SP()->options->get('subscriptions');
	if (!$options['digestsub']) return;

	ini_set('memory_limit', '512M');
	set_time_limit(0);

	# lock the digest table while grab the data and clean up
	$digests = SP()->DB->table(SPDIGEST);

	# bail if no digests
	if (empty($digests)) return;

	# reset the digest table
	SP()->DB->truncate(SPDIGEST);

	# now build a user object of their new posts
	$users = array();
	$usedtopics = array();
	foreach ($digests as $digest) {
		$subs = unserialize($digest->subscriptions);
		foreach ($subs as $id) {
			if (!empty($usedtopics[$id][$digest->topic_id]) && !$options['digestcontent']) {
				$usedtopics[$id][$digest->topic_id]++;
			} else {
				$usedtopics[$id][$digest->topic_id] = 1;
				$users[$id][$digest->post_id]['forum'] = $digest->forum_name;
				$users[$id][$digest->post_id]['topic'] = $digest->topic_name;
				$users[$id][$digest->post_id]['postid'] = $digest->post_id;
				$users[$id][$digest->post_id]['topicid'] = $digest->topic_id;
				$users[$id][$digest->post_id]['forumid'] = $digest->forum_id;
				$users[$id][$digest->post_id]['permalink'] = $digest->permalink;
			}
		}
	}
	$users = apply_filters('sph_subscriptions_digest_list', $users);

	# lets send the emails
	$eol = "\r\n";
	$type = ($options['digesttype'] == 1) ? __('daily', 'sp-subs') : __('weekly', 'sp-subs');
	foreach ($users as $id => $user) {
		# set up some data on user
		$userData = SP()->memberData->get($id);
		if (empty($userData)) continue; # user must not exist so skip
	    $userTopics = SP()->activity->get_col('col=item&type='.SPACTIVITY_SUBSTOPIC."&uid=$id");
	    $userForums = SP()->activity->get_col('col=item&type='.SPACTIVITY_SUBSFORUM."&uid=$id");
		$subs = SP()->options->get('subscriptions');

		# prep the message
		$send = false;
		$msg = '';
		$msg.= __('This is your', 'sp-subs').' '.$type.' '.__('subscription digest report.', 'sp-subs').$eol.$eol;
		$msg.= __('The following topics received new posts since your last digest report', 'sp-subs').': '.$eol.$eol;
		$msg.= '--------------------------------------------------------------------------------'.$eol.$eol;
		$msg = apply_filters('sph_subscriptions_digest_header', $msg, $id);
		foreach ($user as $topic) {
			# verify this topic is still in play for user
			$poster = SP()->DB->table(SPPOSTS, 'post_id='.$topic['postid'], 'user_id');
			if (SP()->auths->get('subscribe', $topic['forumid'], $id) && # user still allowed to subscribe?
				SP()->auths->can_view($topic['forumid'], 'post-content', $id, $poster, $topic['topicid'], $topic['postid']) && # user still view forum?
				(!empty($userForums) && in_array($topic['forumid'], $userForums) || !empty($userTopics) && in_array($topic['topicid'], $userTopics)) && # user still subscribed?
				($options['digestforce'] || $userData['subscribe_digest']) # user still want digest?
			   ) {
				$send = true;
				$thismsg = '';
				$post = ($usedtopics[$id][$topic['topicid']] > 1) ? __('new posts', 'sp-subs') : __('new post', 'sp-subs');
				$thismsg.= __('Forum', 'sp-subs').': '.apply_filters('sph_subscriptions_digest_entry_forum', stripslashes($topic['forum']), $topic).$eol;
				$thismsg.= __('Topic', 'sp-subs').': '.apply_filters('sph_subscriptions_digest_entry_topic', stripslashes($topic['topic']), $topic).' ('.$usedtopics[$id][$topic['topicid']].' '.$post.') '.$eol;
				$thismsg.= __('URL', 'sp-subs').': '.apply_filters('sph_subscriptions_digest_entry_url', urldecode($topic['permalink']), $topic).$eol.$eol;

				# are we adding in post content?
				if ($options['digestcontent']) {
					$post_content = SP()->DB->table(SPPOSTS, 'post_id='.$topic['postid'], 'post_content');
					$post_content = SP()->filters->email_content($post_content);
					$divider = apply_filters('sph_subscriptions_digest_entry_divider', '--------------------------------------------------------------------------------', $topic);
					$post_content = apply_filters('sph_subscriptions_digest_entry_content', $post_content, $topic);
					$thismsg.= __('Post', 'sp-subs').": ".$eol.$post_content.$eol.$eol.$divider.$eol.$eol;
				}
				$msg.= apply_filters('sph_subscriptions_digest_entry', $thismsg, $topic, $id, $usedtopics[$id][$topic['topicid']]);
			}
		}

		$sfprofile = SP()->options->get('sfprofile');
		if ($sfprofile['displaymode'] == 1 || $sfprofile['displaymode'] == 2) {
			$footer = __('To unsubscribe, please visit your profile', 'sp-subs').': '.SP()->spPermalinks->get_query_url(SP()->spPermalinks->get_url('profile')).'ptab=subscriptions&pmenu=topic-subscriptions'.$eol;
			$footer = apply_filters('sph_subscriptions_digest_footer', $footer, $id);
			$msg.= $footer;
		}

		# so if anything to send, email the digest
		if ($send) {
			$email = SP()->DB->table(SPUSERS, "ID=$id", 'user_email');
			$email = apply_filters('sph_subscriptions_digest_email_to', $email, $id, $topic);

			# let plugins hook into this email by  user
			$msg = apply_filters('sph_subscriptions_digest_email', $msg, $id, $topic);

			# send the notification
			$replyto = apply_filters('sph_subscriptions_digest_email_replyto', '', $id, $topic);
			$subject = apply_filters('sph_subscriptions_digest_email_subject', get_option('blogname').': '.__('Subscription Digest', 'sp-subs'), $id, $topic);
			$email_status = sp_send_email($email, $subject, $msg, $replyto);
		}
	}
}
