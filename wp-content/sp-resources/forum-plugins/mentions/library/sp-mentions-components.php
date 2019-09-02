<?php
/*
Simple:Press
Mentions plugin install/upgrade routine
$LastChangedDate: 2018-08-12 13:33:58 -0500 (Sun, 12 Aug 2018) $
$Rev: 15699 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');


function sp_mentions_do_header() {
	$css = SP()->theme->find_css(SPMENTIONSCSS, 'sp-mentions.css', 'sp-mentions.spcss');
	SP()->plugin->enqueue_style('sp-mentions', $css);
}

function sp_mentions_find_mentions($content) {
	preg_match_all('/\B@([\w-\.]+)\b/', $content, $matches);
	if ($matches) {
		$names = $matches[1];
	} else {
		$names = array();
	}

	return $names;
}

function sp_mentions_do_content_filter($content) {
	# stop buddypress from dorking our mentions
	remove_filter('the_content', 'bp_activity_at_name_filter');

	$names = sp_mentions_find_mentions($content);

	# replace mentions with link to send pm
	if (!empty($names)) {
		foreach ($names as $name) {
			$id = SP()->DB->select('SELECT ID FROM '.SPUSERS." WHERE user_nicename = '$name'", 'var');
			if ($id) {
				include_once SP_PLUGIN_DIR.'/forum/content/sp-common-view-functions.php';

				$display_name = SP()->memberData->get($id, 'display_name');
				$replacement = sp_attach_user_profile_link($id, "@$display_name");
				$content = preg_replace("/@$name/i", $replacement, $content);
			}
		}
	}

	return $content;
}

function sp_mentions_do_new_post($newpost) {
	$options = SP()->options->get('mentions');
	if ($options['notification'] == 1) return; # notifications turned off

	# search for mentions
	$names = sp_mentions_find_mentions($newpost['postcontent']);
	if (empty($names)) return; # no mentions

	# send notifications to mentioned users
	foreach ($names as $name) {
		$user = SP()->DB->select('SELECT * FROM '.SPUSERS." WHERE user_nicename = '$name'", 'row');
		if ($user && $user->ID &&  $user->ID != SP()->user->thisUser->ID) {
			# update latest mentions for user
			SP()->activity->add($user->ID, SPACTIVITY_MENTIONED, SP()->user->thisUser->ID, $newpost['postid']);

			# now ensure there are no more than limit option setting
		    $recs = SP()->activity->get_col('col=id&type='.SPACTIVITY_MENTIONED."&uid=.$user->ID");
			if (count($recs) > $options['latest_number']) {
				$c = 0;
				foreach ($recs as $r) {
					$c++;
					if ($c > $options['latest_number']) SP()->activity->delete("id=$r");
				}
			}

			# check if user is opting out of mentions notifications
			$userOpts = SP()->memberData->get($user->ID, 'user_options');
			if (isset($userOpts['mentionsoptout']) && $userOpts['mentionsoptout']) continue;

			$who = (SP()->user->thisUser->ID) ? SP()->user->thisUser->display_name : __('A guest', 'sp-mentions');
			# send the desired notification
			switch ($options['notification']) {
				case 2: # notification
					$msg = apply_filters('sph_mentions_notification_msg', $who.' '.__('mentioned you in the post', 'sp-mentions'), $newpost, $user->ID);
					$nData = array();
					$nData['user_id']		= $user->ID;
					$nData['guest_email']	= '';
					$nData['post_id']		= $newpost['postid'];
					$nData['link']			= $newpost['url'];
					$nData['link_text']		= $newpost['topicname'];
					$nData['message']		= SP()->saveFilters->title($msg);
					$nData['expires']		= time() + (30 * 24 * 60 * 60); # 30 days; 24 hours; 60 mins; 60secs
					SP()->notifications->add($nData);
					break;

				case 3: # private message
					# build the pm
					$newpm = array();
					$title = apply_filters('sph_mentions_pm_title', __('You were mentioned in a post', 'sp-mentions'), $newpost, $user->ID);
					$newpm['title'] = SP()->saveFilters->title($title, SPPMTHREADS, 'title');
					$newpm['slug'] = sp_create_slug($newpm['title'], true, SPPMTHREADS, 'thread_slug');
					$content = apply_filters('sph_mentions_pm_message', $who.' '.__('mentioned you in the forum post', 'sp-mentions').': '.$newpost['url'], $newpost, $user->ID);
					$newpm['messagecontent'] = SP()->saveFilters->content($content, 'new', true, SPPMMESSAGES, 'message');
					$newpm['attachment_id'] = 0;

					# send the pm
					# create thread
					SP()->DB->execute("INSERT INTO ".SPPMTHREADS." (title, thread_slug, message_count) VALUES ('{$newpm['title']}', '{$newpm['slug']}', 1)");
					$newpm['thread_id'] = SP()->rewrites->pageData['insertid'];
					# create message
					SP()->DB->execute("INSERT INTO ".SPPMMESSAGES."
							   (thread_id, user_id, sent_date, message, attachment_id) VALUES
							   ({$newpm['thread_id']}, ".SP()->user->thisUser->ID.", '".current_time('mysql')."', '{$newpm['messagecontent']}', {$newpm['attachment_id']})");
					$newpm['message_id'] = SP()->rewrites->pageData['insertid'];
					# create recipients
					SP()->DB->execute("INSERT INTO ".SPPMRECIPIENTS." (thread_id, message_id, user_id, read_status, pm_type) VALUES ({$newpm['thread_id']}, {$newpm['message_id']}, $user->ID, 0, 1)");
					break;

				case 4: # email
					$eol = "\r\n";
					$tab = "\t";
					$msg  = $who.' '.__('mentioned you in a forum post on', 'sp-mentions').': '.get_option('blogname').$eol.$eol;
					$msg.= __('Group', 'sp-mentions').':'.$tab.SP()->displayFilters->title($newpost['groupname']).$eol;
					$msg.= __('Forum', 'sp-mentions').':'.$tab.SP()->displayFilters->title($newpost['forumname']).$eol;
					$msg.= __('Topic', 'sp-mentions').':'.$tab.SP()->displayFilters->title($newpost['topicname']).$eol;
					$msg.= urldecode($newpost['url']).$eol.$eol;
					$msg = apply_filters('sph_mentions_email_msg', $msg, $newpost, $user->ID, $who);
					$replyto = apply_filters('sph_mentions_email_replyto', '', $newpost, $user->ID);
					$subject = apply_filters('sph_mentions_email_subject', __('You were mentioned', 'sp-mentions'), $newpost, $user->ID, $who);
					sp_send_email($user->user_email, $subject, $msg, $replyto);
					break;

				default:
					return;
		   }
	   }
	}
}

function sp_mentions_do_member_add($userid) {
	$opts = SP()->memberData->get($userid, 'user_options');
	$opts['mentionsoptout'] = 0;
	SP()->memberData->update($userid, 'user_options', $opts);
}

function sp_mentions_do_profile_options($content, $userid) {
	$out = '';
	$out.= '<div class="spColumnSection spProfileLeftCol">';
	$out.= '<p class="spProfileLabel">'.__('Opt out of receiving Mentions notifications', 'sp-mentions').':</p>';
	$out.= '</div>';
	$out.= '<div class="spColumnSection spProfileSpacerCol"></div>';
	$out.= '<div class="spColumnSection spProfileRightCol">';
	$checked = (isset(SP()->user->profileUser->mentionsoptout) && SP()->user->profileUser->mentionsoptout) ? $checked = 'checked="checked" ' : '';
	$out.= '<p class="spProfileLabel"><input type="checkbox" '.$checked.'name="mentionsoptout" id="sf-mentionsoptout" /><label for="sf-mentionsoptout" /></p>';
	$out.= '</div>';
	$content.= apply_filters('sph_ProfileUserMentionsOptOut', $out);

	return $content;
}
