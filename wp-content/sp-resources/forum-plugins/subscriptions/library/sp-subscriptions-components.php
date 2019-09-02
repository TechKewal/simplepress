<?php
/*
Simple:Press
Topic Subscriptions Plugin Support Routines
$LastChangedDate: 2017-12-31 09:40:24 -0600 (Sun, 31 Dec 2017) $
$Rev: 15619 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_subscriptions_do_records_forumview($fData, $topics) {
	if (!empty($topics)) {
		$t = implode(',', $topics);
		$sql = "SELECT user_id, item_id FROM ".SPUSERACTIVITY."
				WHERE type_id=".SPACTIVITY_SUBSTOPIC." AND
				item_id IN (".$t.")";
		$recs = SP()->DB->select($sql);
		# Init arrays
		foreach($topics as $topic) {
			$fData->topics[$topic]->subscriptions = array();
		}
		if($recs) {
			foreach($recs as $r) {
				$fData->topics[$r->item_id]->subscriptions[] = $r->user_id;
			}
		}
	}
	return $fData;
}

function sp_subscriptions_do_post_approved($posts) {
	if (empty($posts)) return;

	# take care of each post
	foreach ($posts as $post) {
		$postData = SP()->DB->select('SELECT group_name, '.SPFORUMS.'.group_id, '.SPFORUMS.'.forum_id, forum_name, forum_slug, '.SPTOPICS.'.topic_id, topic_slug, topic_name, '.SPPOSTS.'.post_id, '.SPPOSTS.'.user_id, post_status, post_index, guest_name, post_content
			 FROM '.SPPOSTS.'
			 JOIN '.SPFORUMS.' ON '.SPPOSTS.'.forum_id = '.SPFORUMS.'.forum_id
			 JOIN '.SPTOPICS.' ON '.SPPOSTS.'.topic_id = '.SPTOPICS.'.topic_id
			 JOIN '.SPGROUPS.' ON '.SPFORUMS.'.group_id = '.SPGROUPS.'.group_id
			 WHERE '.SPPOSTS.".post_id=$post", 'row');

		# make sure we found a post
		if (empty($postData)) return;

		# fill in needed data
		$newpost = array();
		$newpost['postid'] = $postData->post_id;
		$newpost['postcontent_unescaped'] = $postData->post_content;
		$newpost['forumid'] = $postData->forum_id;
		$newpost['groupname'] = $postData->group_name;
		$newpost['forumname'] = $postData->forum_name;
		$newpost['forumslug'] = $postData->forum_slug;
		$newpost['topicid'] = $postData->topic_id;
		$newpost['topicslug'] = $postData->topic_slug;
		$newpost['topicname'] = $postData->topic_name;
		$newpost['userid'] = $postData->user_id;
		$newpost['poststatus'] = $postData->post_status;
		$newpost['url'] = SP()->spPermalinks->build_url($postData->forum_slug, $postData->topic_slug, 0, $postData->post_id, $postData->post_index);
		if (empty($newpost['userid'])) {
			$newpost['postername'] = $postData->guest_name;
		} else {
			$newpost['postername'] = SP()->saveFilters->name(SP()->DB->table(SPUSERS, "ID={$newpost['userid']}", 'display_name'));
		}

		# check for digest entry
		require_once SLIBDIR.'sp-subscriptions-digest.php';
		sp_subscriptions_do_digest_entry($newpost);

		# check for regular subscription notification
		sp_subscriptions_post_notification('', $newpost);
	}
}

function sp_subscriptions_do_profile_options($message, $thisUser, $thisForm) {
	$update = apply_filters('sph_ProfileUserSubsAutoSubUpdate', true);
	if ($update) {
		$options = SP()->memberData->get($thisUser, 'user_options');
		if (isset($_POST['subpost'])) $options['autosubpost'] = true; else $options['autosubpost'] = false;
		if (isset($_POST['substart'])) $options['autosubstart'] = true; else $options['autosubstart'] = false;
		if (isset($_POST['subnewtopics'])) $options['subnewtopics'] = true; else $options['subnewtopics'] = false;
		SP()->memberData->update($thisUser, 'user_options', $options);

		$subs = SP()->options->get('subscriptions');
		if (!$subs['digestforce']) {
			$digest = (isset($_POST['subdigest'])) ? 1 : 0;
			SP()->memberData->update($thisUser, 'subscribe_digest', $digest);
		}

		$message['type'] = 'success';
		$message['text'] = __('Subscription options updated', 'sp-subs');
	}
	return $message;
}

function sp_subscriptions_do_profile_forums($message, $thisUser, $thisForm) {
	# first go through list of current subs to see if any dropped
    $curSubs = SP()->activity->get_col('col=item&type='.SPACTIVITY_SUBSFORUM."&uid=$thisUser");
	if ($curSubs) {
		foreach ($curSubs as $sub) {
			if (empty($_POST['forum']) || !array_key_exists($sub, $_POST['forum'])) sp_subscriptions_remove_forum_subscription($sub, $thisUser, false);
		}
	}

	# go through new list and add any new subs
	if (isset($_POST['forum'])) {
		foreach ($_POST['forum'] as $sub => $on) {
			if ((empty($curSubs) || !in_array($sub, $curSubs)) && SP()->auths->get('subscribe', $sub, $thisUser)) sp_subscriptions_save_forum_subscription($sub, $thisUser, false);
		}
	}
	$message['type'] = 'success';
	$message['text'] = __('Subscriptions updated', 'sp-subs');
	return $message;
}

function sp_subscriptions_do_profile_topics($message, $thisUser, $thisForm) {
	if (isset($_POST['formsubmitall'])) {
		sp_subscriptions_remove_user_subscriptions($thisUser);

		$message['type'] = 'success';
		$message['text'] = __('All topics unsubscribed', 'sp-subs');
		return $message;
	} else if (empty($_POST['topic'])) {
		$message['type'] = 'error';
		$message['text'] = __('No subscribed topics selected', 'sp-subs');
		return $message;
	} else {
		foreach ($_POST['topic'] as $topic_id => $topic) {
			sp_subscriptions_remove_subscription(SP()->filters->integer($topic_id), $thisUser, false);
		}
		$message['type'] = 'success';
		$message['text'] = __('Subscriptions updated', 'sp-subs');
		return $message;
	}
	return $message;
}

function sp_subscriptions_do_reset_profile_tabs() {
	SP()->profile->add_tab('Subscriptions');
	SP()->profile->add_menu('Subscriptions', 'Subscription Options', SFORMSDIR.'sp-subscriptions-options-form.php');
	SP()->profile->add_menu('Subscriptions', 'Topic Subscriptions', SFORMSDIR.'sp-subscriptions-manage-form.php');
	$subs = SP()->options->get('subscriptions');
	if ($subs['forumsubs']) {
		SP()->profile->add_menu('Subscriptions', 'Forum Subscriptions', SFORMSDIR.'sp-subscriptions-forum-form.php');
	}
}

function sp_subscriptions_do_post_footer($out, $topic, $a) {
    if (SP()->activity->count('type='.SPACTIVITY_SUBSTOPIC."&item=$topic->topic_id")) {
		$out.= '<div class="spEditorSection">';
		$icon = SP()->theme->paint_icon('', SIMAGES, 'sp_SubscriptionsPostEditor.png');
		$out.= '<p class="spSubscriptionsNotice">'.$icon.__('This topic has subscriptions', 'sp-subs').'</p>';
		$out.= '</div>';
	}
	return $out;
}

function sp_subscriptions_do_header() {
	$css = SP()->theme->find_css(SCSS, 'sp-subscriptions.css', 'sp-subscriptions.spcss');
	SP()->plugin->enqueue_style('sp-subs', $css);
}

function sp_subscriptions_do_forum_status($content) {
	$out = '';

	if (!empty(SP()->forum->view->thisTopic->subscriptions)) {
		$p = (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? SIMAGESMOB : SIMAGES;
		$out.= SP()->theme->paint_icon('spIcon  spIconNoAction', $p, 'sp_SubscriptionsForumStatus.png', esc_attr(__('This topic has subscriptions', 'sp-subs')));
	}
	return $content.$out;
}

function sp_subscriptions_do_post_form_options($display, $thisTopic) {
	global $tab;

	$out = '';
	if (SP()->auths->get('subscribe', $thisTopic->forum_id)) {
		$subscribed = sp_subscriptions_is_subscribed(SP()->user->thisUser->ID, $thisTopic->topic_id);
		if (!$subscribed) {
			$checked = (isset(SP()->user->thisUser->autosubpost) && SP()->user->thisUser->autosubpost) ? ' checked="checked"' : '';
			$label = apply_filters('sph_subs_subscribe_label', __('Subscribe to this topic', 'sp-subs'));
			$out.= '<input type="checkbox" tabindex="'.$tab++.'" class="spControl" name="topicsub" id="sftopicsub"'.$checked.' />';
			$out.= '<label class="spLabel spCheckbox" for="sftopicsub">'.$label.'</label><br />';
		} else {
			$label = apply_filters('sph_subs_unsubscribe_label', __('Unsubscribe from this topic', 'sp-subs'));
			$out.= '<input type="checkbox" tabindex="'.$tab++.'" class="spControl" name="topicsubend" id="sftopicsubend" />';
			$out.= '<label class="spLabel spCheckbox" for="sftopicsubend">'.$label.'</label><br />';
		}
	}

	return $display.$out;
}

function sp_subscriptions_do_topic_form_options($display, $thisForum) {
	global $tab;

	$out = '';
	if (SP()->auths->get('subscribe', $thisForum->forum_id)) {
		$checked = ((isset(SP()->user->thisUser->autosubpost) && SP()->user->thisUser->autosubpost) || (isset(SP()->user->thisUser->autosubstart) && SP()->user->thisUser->autosubstart)) ? ' checked="checked"' : '';
		$label = apply_filters('sph_subs_subscribe_label', __('Subscribe to this topic', 'sp-subs'));
		$out.= '<input type="checkbox" tabindex="'.$tab++.'" class="spControl" name="topicsub" id="sftopicsub"'.$checked.' />';
		$out.= '<label class="spLabel spCheckbox" for="sftopicsub">'.$label.'</label><br />';
	}

	return $display.$out;
}

function sp_subscriptions_do_post_notification($retmsg, $newpost) {
	# no notifications for posts in moderation
	if ($newpost['poststatus']) return $retmsg;

	# if forcing digests, just bail
	$subs = SP()->options->get('subscriptions');
	if ($subs['digestsub'] && $subs['digestforce']) return $retmsg;

	$users = array();
	$eol = "\r\n";

	# grab the users using digest when enabled
	$digestUsers = ($subs['digestsub']) ? SP()->DB->select('SELECT user_id FROM '.SPMEMBERS.' WHERE subscribe_digest=1', 'col') : '';

	# start gathering data for the email
	if ($subs['forumsubs']) {
		$forum = SP()->DB->table(SPFORUMS, "forum_id=".$newpost['forumid']);
	    $users = SP()->activity->get_col('col=uid&type='.SPACTIVITY_SUBSFORUM."&item=".$forum[0]->forum_id);
		$users = apply_filters('sph_subscriptions_forum_list', $users, $newpost);

        # for posts, see if only notifying new topics
        if (!empty($users) && $newpost['action'] == 'post') {
            foreach ($users as $id => $user) {
        		$options = SP()->memberData->get($user, 'user_options');
                if (isset($options['subnewtopics']) && $options['subnewtopics']) {
                    unset($users[$id]);
                }
            }
            $users = array_values($users);
        }
	}

	# check if any subscribers for new post
	$topic = SP()->DB->table(SPTOPICS, "topic_id=".$newpost['topicid']);
    $topicUsers = SP()->activity->get_col('col=uid&type='.SPACTIVITY_SUBSTOPIC."&item=".$topic[0]->topic_id);
	$topicUsers = apply_filters('sph_subscriptions_topic_list', $topicUsers, $newpost);

	# build the combined forum and topic subscribers list
	if (empty($users)) {
		$users = $topicUsers;
	} else if (!empty($topicUsers)) {
		$users = array_merge($users, $topicUsers);
	}
	if (is_array($users)) $users = array_unique($users);
	$users = apply_filters('sph_subscriptions_combined_list', $users, $newpost);

	# send email notifications to subscribers
	if ($users) {
		# do we include the post content?
		if ($subs['includepost']) {
			$post_content = SP()->filters->email_content($newpost['postcontent_unescaped']);
		} else {
			$post_content = '';
		}

		# subscribers message
		$msg = '';
		$msg.= __('New post on a forum or topic you are subscribed to at', 'sp-subs').' '.get_option('blogname').':'.$eol.$eol;
		$msg.= __('From', 'sp-subs').': '.$newpost['postername'].$eol;
		$msg.= __('Group', 'sp-subs').': '.$newpost['groupname'].$eol;
		$msg.= __('Forum', 'sp-subs').': '.$newpost['forumname'].$eol;
		$msg.= __('Topic', 'sp-subs').': '.$newpost['topicname'].$eol;
		$msg.= __('URL', 'sp-subs').': '.urldecode($newpost['url']).$eol.$eol;
		$post_content = apply_filters('sph_subs_post_content', $post_content, $newpost);
		if (!empty($post_content)) $msg.= __('Post', 'sp-subs').": ".$eol.$post_content.$eol.$eol;

		$sfprofile = SP()->options->get('sfprofile');
		if ($sfprofile['displaymode'] == 1 || $sfprofile['displaymode'] == 2) {
			$msg.= __('To unsubscribe, please visit your profile', 'sp-subs').': '.SP()->spPermalinks->get_query_url(SP()->spPermalinks->get_url('profile')).'ptab=subscriptions&pmenu=topic-subscriptions'.$eol;
		}

		# let plugins hook into this email
		$msg = apply_filters('sph_subscriptions_notification', $msg, $newpost);

		$sent = false;
		foreach ($users as $user) {
			# dont notify user if digest subscriber
			if ($subs['digestsub'] && is_array($digestUsers) && in_array($user, $digestUsers)) continue;

			# offer option to notify self, otherwise dont
			$notify_self = apply_filters('sph_subscriptions_notify_self', false, $newpost['userid']);
			if ($notify_self == false && $user == $newpost['userid']) continue;

			# dont notify user if they can no longer view the forum
			if (!SP()->auths->can_view($newpost['forumid'], 'post-content', $user, $newpost['userid'], $newpost['topicid'], $newpost['postid'])) continue;

			# we are good, send the email
			# get user email address
			$email = SP()->DB->table(SPUSERS, "ID=$user", 'user_email');
			$email = apply_filters('sph_subscriptions_email_to', $email, $newpost, $user);

			# let plugins hook into this email by  user
			$thisMsg = apply_filters('sph_subscriptions_notification_email', $msg, $newpost, $user, 'sub');

			# send the notification
			$replyto = apply_filters('sph_subscriptions_email_replyto', '', $newpost);
			$subject = apply_filters('sph_subscriptions_email_subject', __('Forum Post', 'sp-subs').' - '.get_option('blogname').': ['.$newpost['topicname'].']', $newpost, $user);
			$email_status = sp_send_email($email, $subject, $thisMsg, $replyto);
			if (!$sent && $email_status[0]) $sent = true;
		}

		if ($sent) {
			if (empty($retmsg)) {
				$retmsg = '- '.__('Notified: subscribers', 'sp-subs');
			} else {
				$retmsg.= ' '.__('and subscribers', 'sp-subs');
			}
		}
	}

	return $retmsg;
}

function sp_subscriptions_do_post_create($msg, $newpost) {
	require_once SLIBDIR.'sp-subscriptions-database.php';

	# subscribe?
	if (SP()->auths->get('subscribe', $newpost['forumid']) && !empty($newpost['topicsub'])) {
		sp_subscriptions_save_subscription($newpost['topicid'], $newpost['userid'], true);
		$msg.= ' '.__('and subscribed', 'sp-subs');
	}

	# unsubscribe?
	if (SP()->auths->get('subscribe', $newpost['forumid']) && !empty($newpost['topicsubend'])) {
		sp_subscriptions_remove_subscription($newpost['topicid'], $newpost['userid']);
		$msg.= ' '.__('and unsubscribed', 'sp-subs');
	}

	return $msg;
}

function sp_subscriptions_do_process_actions() {
	require_once SLIBDIR.'sp-subscriptions-database.php';

	if (isset($_GET['subforum'])) {
		$forumid = SP()->filters->integer($_GET['subforum']);
		if (SP()->auths->get('subscribe', $forumid)) {
            sp_subscriptions_save_forum_subscription($forumid, SP()->user->thisUser->ID);
            SP()->user->thisUser->forum_subscribe[] = $forumid;
        }
	}

	if (isset($_GET['unsubforum'])) {
		$forumid = SP()->filters->integer($_GET['unsubforum']);
		if (SP()->auths->get('subscribe', $forumid)) {
            sp_subscriptions_remove_forum_subscription($forumid, SP()->user->thisUser->ID);
            if (($key = array_search($forumid, SP()->user->thisUser->forum_subscribe)) !== false) unset(SP()->user->thisUser->forum_subscribe[$key]);
        }
	}

	if (isset($_GET['endallsubs'])) sp_subscriptions_remove_user_subscriptions(SP()->filters->integer($_GET['userid']));
}

function sp_subscriptions_do_load_js($footer) {
	$sfauto = SP()->options->get('sfauto');
	if ($sfauto['sfautoupdate']) SP()->plugin->enqueue_script('sfsubsupdate', SSCRIPT.'sp-subscriptions-update.min.js', array('jquery'), false, $footer);

	$script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SSCRIPT.'sp-subscriptions.js' : SSCRIPT.'sp-subscriptions.min.js';
	SP()->plugin->enqueue_script('sp-subscriptions', $script, array('jquery'), false, $footer);

	$strings = array(
		'addsubtopic'	   => __('Subscription added', 'sp-subs'),
		'delsubtopic'	   => __('Subscription removed', 'sp-subs'),
        'nosubs'	       => __('You are not currently subscribed to any topics', 'sp-subs')
	);
	SP()->plugin->localize_script('sp-subscriptions', 'sp_subs_vars', $strings);
}

function sp_subscriptions_do_load_admin_js($footer) {
	$script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SSCRIPT.'sp-subscriptions-admin.js' : SSCRIPT.'sp-subscriptions-admin.min.js';
	wp_enqueue_script('sp-subscriptions-admin', $script, array('jquery'), false, $footer);
}

function sp_subscriptions_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'subscriptions/sp-subscriptions-plugin.php') {
		$url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
		$actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-subs')."'>".__('Uninstall', 'sp-subs').'</a>';
		$url = SPADMINCOMPONENTS.'&amp;tab=plugin&amp;admin=sp_subscriptions_admin_members&amp;save=sp_subscriptions_admin_save_members&amp;form=1';
		$actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-subs')."'>".__('Options', 'sp-subs').'</a>';
	}
	return $actionlink;
}

function sp_subscriptions_is_forum_subscribed($userid, $forumid) {
	if (!$userid || !$forumid) return '';

	# see if we can use current user object to save queries
	if (SP()->user->thisUser->ID == $userid) {
		if (!property_exists(SP()->user->thisUser, 'forum_subscribe') || empty(SP()->user->thisUser->forum_subscribe)) {
		    SP()->user->thisUser->forum_subscribe = SP()->activity->get_col('col=item&type='.SPACTIVITY_SUBSFORUM.'&uid='.SP()->user->thisUser->ID);
		}
		$found = in_array($forumid, (array) SP()->user->thisUser->forum_subscribe);
	} else {
		$found = SP()->activity->exist('type='.SPACTIVITY_SUBSFORUM."&uid=$userid&item=$forumid");
	}
	return $found;
}

function sp_subscriptions_is_subscribed($userid, $topicid) {
	if (!$userid || !$topicid) return '';

	# see if we can use current user object to save queries
	if (SP()->user->thisUser->ID == $userid) {
		$found = in_array($topicid, (array) SP()->user->thisUser->subscribe);
	} else {
		$found = SP()->activity->exist('type='.SPACTIVITY_SUBSTOPIC."&uid=$userid&item=$topicid");
	}
	return $found;
}

function sp_subscriptions_topics_pn_next($cpage, $totalpages, $pnshow) {
	$start = ($cpage - $pnshow);
	if ($start < 1) $start = 1;
	$end = ($cpage - 1);

	$out = '';
	if ($start > 1) {
		$out.= sp_subscriptions_topics_pn_url(1);
		$out.= '<span class="page-numbers dota">...</span>';
	}

	if ($end > 0) {
		for ($i = $start; $i <= $end; $i++) {
			$out.= sp_subscriptions_topics_pn_url($i);
		}
	}

	return $out;
}

function sp_subscriptions_topics_pn_previous($cpage, $totalpages, $pnshow) {
	$start = ($cpage + 1);
	$end = ($cpage + $pnshow);
	if ($end > $totalpages) $end = $totalpages;

	$out = '';
	if ($start <= $totalpages) {
		for ($i = $start; $i <= $end; $i++) {
			$out.= sp_subscriptions_topics_pn_url($i);
		}
		if ($end < $totalpages) {
			$out.= '<span class="page-numbers dota">...</span>';
			$out.= sp_subscriptions_topics_pn_url($totalpages);
		}
	}

	return $out;
}

function sp_subscriptions_topics_pn_url($thispage) {
	$out = '';
	$site = wp_nonce_url(SPAJAXURL.'subs-topics&targetaction=topiclist&page='.$thispage, 'subs-topics');
	$gif = SPCOMMONIMAGES.'working.gif';
	$out.= '<a class="page-numbers spSubsShowSubs" data-target="sptopicsubs" data-site="'.$site.'" data-img="'.$gif.'">'.$thispage.'</a>';

	return $out;
}

function sp_subscriptions_render_forum_subscriptions($curpage=1, $search='') {
	$data = sp_subscriptions_get_forum_subscriptions($curpage, $search);
	$records = $data['data'];

	# paging
	$totalpages = ceil($data['count'] / 20);

	spa_paint_options_init();
	spa_paint_open_tab(__('Manage Users - Subscriptions by Forum', 'sp-subs'), true);
	spa_paint_open_panel();
	spa_paint_open_fieldset(__('Forum Subscriptions', 'sp-subs'), 'true', 'subscriptions-forums');

	echo '<div class="tablenav">';
	echo '<div class="tablenav-pages">';
	echo '<strong>'.__('Page', 'sp-subs').':</strong>  ';
	echo sp_subscriptions_forums_pn_next($curpage, $totalpages, 3);
	echo '<span class="page-numbers current">'.$curpage.'</span>';
	echo sp_subscriptions_forums_pn_previous($curpage, $totalpages, 3);
	echo '</div>';

	echo '<div>';
	$site = wp_nonce_url(SPAJAXURL.'subs-forums&targetaction=forumlist&page=1', 'subs-forums');
	$gif = SPCOMMONIMAGES.'working.gif';
	echo '<form action="'.SPADMINUSER.'" method="post" name="spforumsubs" id="spforumsubs" data-target="spforumsubs" data-site="'.$site.'" data-img="'.$gif.'">';
?>
	<input type="text" class="sfacontrol" id="post-search-input" name="swsearch" value="<?php echo esc_attr($search); ?>" />
	<input type="button" class="button-primary spSubsShowSubs" data-target="spforumsubs" data-site="<?php echo $site; ?>" data-img="<?php echo $gif; ?>" value="<?php esc_attr_e(__('Forum Search', 'sp-subs')); ?>" />
	</form>
	</div>
<?php
	echo '</div>';

	# show data
	echo '<table class="widefat fixed striped spMobileTable800" style="padding:0;border-spacing:0;border-collapse:separate">';
	if ($records) {
		echo '<thead>';
		echo '<tr>';
		echo '<th style="text-align:center;">'.__("Group", "sp-subs").'</th>';
		echo '<th style="text-align:center;">'.__("Forum", "sp-subs").'</th>';
		echo '<th style="text-align:center;">'.__('Forum Subscriptions', 'sp-subs').'</th>';
		echo '<th style="text-align:center;">'.__('Manage', 'sp-subs').'</th>';
		echo '</tr>';
		echo '</thead>';

		echo '<tbody>';
		foreach ($records as $index => $record) {
			echo "<tr id='forum-subs$index' class='spMobileTableData'>";
			echo '<td data-label="'.__('Group', 'sp-subs').'" style="text-align:center;">'.SP()->displayFilters->title($record->group_name).'</td>';
			echo '<td data-label="'.__('Forum', 'sp-subs').'" style="text-align:center;">'.SP()->displayFilters->title($record->forum_name).'</td>';
			echo '<td data-label="'.__('Subscriptions', 'sp-subs').'" style="text-align:center;" >';
			if ($record->members) {
				$first = true;
				$list = explode(',', $record->members);
				for ($x=0; $x<count($list); $x++) {
					if ($first) {
						echo SP()->displayFilters->name(SP()->memberData->get($list[$x], 'display_name'));
						$first = false;
					} else {
						echo ', '.SP()->displayFilters->name(SP()->memberData->get($list[$x], 'display_name'));
					}
				}
			}
			echo '</td>';
			echo '<td data-label="'.__('Manage', 'sp-subs').'" style="text-align:center;" >';
			if ($record->members) {
                $msg = esc_attr(__('Are you sure you want to delete these forum subscriptions?'), 'sp-subs');
				$site = wp_nonce_url(SPAJAXURL.'subs-forums&amp;targetaction=del_subs&amp;id='.$record->forum_id, 'subs-forums');
    			$gif = SPCOMMONIMAGES.'working.gif';
?>
				<img class="spDeleteRow" data-msg="<?php echo $msg; ?>" data-url="<?php echo $site; ?>" data-target="forum-subs<?php echo $index; ?>" src="<?php echo SPCOMMONIMAGES; ?>delete.png" title="<?php esc_attr_e(__('Delete Subscriptions', 'sp-subs')); ?>" alt="" />&nbsp;
<?php
			}
			echo '</td>';
			echo '</tr>';
		}
	} else {
		echo '<tr>';
		echo '<td>';
		_e('No subscriptions found', 'sp-subs');
		echo '</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';

	spa_paint_close_fieldset();
	spa_paint_close_panel();
	spa_paint_close_container();
	spa_paint_close_tab();
}

function sp_subscriptions_forums_pn_next($cpage, $totalpages, $pnshow) {
	$start = ($cpage - $pnshow);
	if ($start < 1) $start = 1;
	$end = ($cpage - 1);

	$out = '';
	if ($start > 1) {
		$out.= sp_subscriptions_forums_pn_url(1);
		$out.= '<span class="page-numbers dota">...</span>';
	}

	if ($end > 0) {
		for ($i = $start; $i <= $end; $i++) {
			$out.= sp_subscriptions_forums_pn_url($i);
		}
	}

	return $out;
}

function sp_subscriptions_forums_pn_previous($cpage, $totalpages, $pnshow) {
	$start = ($cpage + 1);
	$end = ($cpage + $pnshow);
	if ($end > $totalpages) $end = $totalpages;

	$out = '';
	if ($start <= $totalpages) {
		for ($i = $start; $i <= $end; $i++) {
			$out.= sp_subscriptions_forums_pn_url($i);
		}
		if ($end < $totalpages) {
			$out.= '<span class="page-numbers dota">...</span>';
			$out.= sp_subscriptions_forums_pn_url($totalpages);
		}
	}

	return $out;
}

function sp_subscriptions_forums_pn_url($thispage) {
	$out = '';
	$site = wp_nonce_url(SPAJAXURL.'subs-forums&targetaction=forumlist&page='.$thispage, 'subs-forums');
	$gif = SPCOMMONIMAGES.'working.gif';
	$out.= '<a class="page-numbers spSubsShowSubs" data-target="spforumsubs" data-site="'.$site.'" data-img="'.$gif.'">'.$thispage.'</a>';

	return $out;
}

function sp_subscriptions_render_user_subscriptions($curpage=1, $search='') {
	$data = sp_subscriptions_get_user_subscriptions($curpage, $search);
	$records = $data['data'];
	# paging
	$totalpages = ceil($data['count'] / 20);

	echo '<div class="tablenav">';
	echo '<div class="tablenav-pages">';
	echo '<strong>'.__('Page', 'sp-subs').':</strong>  ';
	echo sp_subscriptions_users_pn_next($curpage, $totalpages, 3);
	echo '<span class="page-numbers current">'.$curpage.'</span>';
	echo sp_subscriptions_users_pn_previous($curpage, $totalpages, 3);
	echo '</div>';

	echo '<div>';
	$site = wp_nonce_url(SPAJAXURL.'subs-users&targetaction=topiclist&page=1', 'subs-users');
	$gif = SPCOMMONIMAGES.'working.gif';
	echo '<form action="'.SPADMINUSER.'" method="post" name="sptopicusers" id="sptopicusers" data-target="sptopicusers" data-site="'.$site.'" data-img="'.$gif.'">';
?>
	<input type="text" class="sfacontrol" id="post-search-input" name="swsearch" value="<?php echo esc_attr($search); ?>" />
	<input type="button" class="button-primary spSubsShowSubs" data-target="subsdisplayspot" data-site="<?php echo $site; ?>" data-img="<?php echo $gif; ?>" value="<?php esc_attr_e(__('User Search', 'sp-subs')); ?>" />
	</form>
	</div>
<?php
	echo '</div>';

	# show data
	echo '<table class="widefat fixed striped spMobileTable800" style="padding:0;border-spacing:0;border-collapse:separate">';
	if ($records) {
		echo '<thead>';
		echo '<tr>';
		echo '<th style="text-align:center;">'.__('User ID', 'sp-subs').'</th>';
		echo '<th style="text-align:center;">'.__('Display Name', 'sp-subs').'</th>';
		echo '<th style="text-align:center;">'.__("Topic Subscriptions", "sp-subs").'</th>';
		echo '<th style="text-align:center;">'.__('Manage', 'sp-subs').'</th>';
		echo '</tr>';
		echo '</thead>';

		echo '<tbody>';
		foreach ($records as $index => $record) {
			echo "<tr id='user-subs$index' class='spMobileTableData'>";
			echo '<td data-label="'.__('User ID', 'sp-subs').'" style="text-align:center;">'.$record->user_id.'</td>';
			echo '<td data-label="'.__('Name', 'sp-subs').'" style="text-align:center;">'.SP()->displayFilters->name($record->display_name).'</td>';
			echo '<td data-label="'.__('Subscriptions', 'sp-subs').'" style="text-align:center;">';
			if($record->topics) {
				$topics = explode(',', $record->topics);
				foreach ($topics as $topic) {
					$forum = SP()->DB->select('SELECT topic_id, topic_slug, topic_name, forum_slug
							 FROM '.SPTOPICS.'
							 JOIN '.SPFORUMS.' ON '.SPTOPICS.'.forum_id = '.SPFORUMS.'.forum_id
							 WHERE topic_id = '.$topic, 'row');
					if(!empty($forum->topic_slug)) {
						$url = SP()->spPermalinks->build_url($forum->forum_slug, $forum->topic_slug, 1, 0);
						echo __('Topic ID', 'sp-subs').': '.$forum->topic_id.'&nbsp;&nbsp;&nbsp;'.__('Topic', 'sp-subs').': <a href="'.$url,'">'.SP()->displayFilters->title($forum->topic_name).'</a><br />';
					}
				}
			}
			echo '</td>';
			if ($record->topics) {
				echo '<td data-label="'.__('Manage', 'sp-subs').'" style="text-align:center;">';
                $msg = esc_attr(__('Are you sure you want to delete these user subscriptions?'), 'sp-subs');
				$site = wp_nonce_url(SPAJAXURL.'subs-users&amp;targetaction=del_subs&amp;id='.$record->user_id, 'subs-users');
				$gif = SPCOMMONIMAGES.'working.gif';
?>
				<img class="spDeleteRow" data-msg="<?php echo $msg; ?>" data-url="<?php echo $site; ?>" data-target="user-subs<?php echo $index; ?>" src="<?php echo SPCOMMONIMAGES; ?>delete.png" title="<?php esc_attr_e(__('Delete Subscriptions', 'sp-subs')); ?>" alt="" />&nbsp;
<?php
				echo '</td>';
			} else {
				echo '<td></td>';
			}
			echo '</tr>';
		}
	} else {
		echo '<tr>';
		echo '<td>';
		_e('No subscriptions found!', 'sp-subs');
		echo '</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
}

function sp_subscriptions_users_pn_next($cpage, $totalpages, $pnshow) {
	$start = ($cpage - $pnshow);
	if ($start < 1) $start = 1;
	$end = ($cpage - 1);

	$out = '';
	if ($start > 1) {
		$out.= sp_subscriptions_users_pn_url(1);
		$out.= '<span class="page-numbers dota">...</span>';
	}

	if ($end > 0) {
		for ($i = $start; $i <= $end; $i++) {
			$out.= sp_subscriptions_users_pn_url($i);
		}
	}

	return $out;
}

function sp_subscriptions_users_pn_previous($cpage, $totalpages, $pnshow) {
	$start = ($cpage + 1);
	$end = ($cpage + $pnshow);
	if ($end > $totalpages) $end = $totalpages;

	$out = '';
	if ($start <= $totalpages) {
		for ($i = $start; $i <= $end; $i++) {
			$out.= sp_subscriptions_users_pn_url($i);
		}
		if ($end < $totalpages) {
			$out.= '<span class="page-numbers dota">...</span>';
			$out.= sp_subscriptions_users_pn_url($totalpages);
		}
	}

	return $out;
}

function sp_subscriptions_users_pn_url($thispage) {
	$out = '';
	$site = wp_nonce_url(SPAJAXURL.'subs-users&targetaction=topiclist&page='.$thispage, 'subs-users');
	$gif = SPCOMMONIMAGES.'working.gif';
	$out.= '<a class="page-numbers spSubsShowSubs" data-target="sptopicusers" data-site="'.$site.'" data-img="'.$gif.'">'.$thispage.'</a>';

	return $out;
}

function sp_subscriptions_render_digest_subscriptions($curpage=1, $search='') {
	$data = sp_subscriptions_get_user_digests($curpage, $search);
	$records = $data['data'];
	# paging
	$totalpages = ceil($data['count'] / 20);

	echo '<div class="tablenav">';
	echo '<div class="tablenav-pages">';
	echo '<strong>'.__('Page', 'sp-subs').':</strong>  ';
	echo sp_subscriptions_digest_pn_next($curpage, $totalpages, 3);
	echo '<span class="page-numbers current">'.$curpage.'</span>';
	echo sp_subscriptions_digest_pn_previous($curpage, $totalpages, 3);
	echo '</div>';

	echo '<div>';
	$site = wp_nonce_url(SPAJAXURL.'subs-digest&targetaction=topiclist&page=1', 'subs-digest');
	$gif = SPCOMMONIMAGES.'working.gif';
	echo '<form action="'.SPADMINUSER.'" method="post" name="spdigestusers" id="spdigestusers" data-target="subsdisplayspot" data-site="'.$site.'" data-img="'.$gif.'">';
?>
	<input type="text" class="sfacontrol" id="post-search-input" name="swsearch" value="<?php echo esc_attr($search); ?>" />
	<input type="button" class="button-primary spSubsShowSubs" data-target="subsdisplayspot" data-site="<?php echo $site; ?>" data-img="<?php echo $gif; ?>" value="<?php esc_attr_e(__('User Search', 'sp-subs')); ?>" />
	</form>
	</div>
<?php
	echo '</div>';

	# show data
	echo '<table class="widefat fixed striped spMobileTable800" style="padding:0;border-spacing:0;border-collapse:separate">';
	if ($records) {
		echo '<thead>';
		echo '<tr>';
		echo '<th style="text-align:center;">'.__('User ID', 'sp-subs').'</th>';
		echo '<th style="text-align:center;">'.__('Display Name', 'sp-subs').'</th>';
		echo '<th style="text-align:center;">'.__('Manage', 'sp-subs').'</th>';
		echo '</tr>';
		echo '</thead>';

		echo '<tbody>';
		foreach ($records as $index => $record) {
			echo "<tr id='user-subs$index' class='spMobileTableData'>";
			echo '<td data-label="'.__('User ID', 'sp-subs').'" style="text-align:center;">'.$record->user_id.'</td>';
			echo '<td data-label="'.__('Name', 'sp-subs').'" style="text-align:center;">'.SP()->displayFilters->name($record->display_name).'</td>';
			echo '<td data-label="'.__('Manage', 'sp-subs').'" style="text-align:center;">';
            $msg = esc_attr(__('Are you sure you want to delete these digest subscriptions?'), 'sp-subs');
			$site = wp_nonce_url(SPAJAXURL.'subs-digest&amp;targetaction=del_digest&amp;id='.$record->user_id, 'subs-digest');
			$gif = SPCOMMONIMAGES.'working.gif';
?>
			<img class="spDeleteRow" data-msg="<?php echo $msg; ?>" data-url="<?php echo $site; ?>" data-target="user-subs<?php echo $index; ?>" src="<?php echo SPCOMMONIMAGES; ?>delete.png" title="<?php esc_attr_e(__('Delete Subscriptions', 'sp-subs')); ?>" alt="" />&nbsp;
<?php
			echo '</td>';
			echo '</tr>';
		}
	} else {
		echo '<tr>';
		echo '<td>';
		_e('No subscriptions found!', 'sp-subs');
		echo '</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
}

function sp_subscriptions_digest_pn_next($cpage, $totalpages, $pnshow) {
	$start = ($cpage - $pnshow);
	if ($start < 1) $start = 1;
	$end = ($cpage - 1);

	$out = '';
	if ($start > 1) {
		$out.= sp_subscriptions_digest_pn_url(1);
		$out.= '<span class="page-numbers dota">...</span>';
	}

	if ($end > 0) {
		for ($i = $start; $i <= $end; $i++) {
			$out.= sp_subscriptions_digest_pn_url($i);
		}
	}

	return $out;
}

function sp_subscriptions_digest_pn_previous($cpage, $totalpages, $pnshow) {
	$start = ($cpage + 1);
	$end = ($cpage + $pnshow);
	if ($end > $totalpages) $end = $totalpages;

	$out = '';
	if ($start <= $totalpages) {
		for ($i = $start; $i <= $end; $i++) {
			$out.= sp_subscriptions_digest_pn_url($i);
		}
		if ($end < $totalpages) {
			$out.= '<span class="page-numbers dota">...</span>';
			$out.= sp_subscriptions_digest_pn_url($totalpages);
		}
	}

	return $out;
}

function sp_subscriptions_digest_pn_url($thispage) {
	$out = '';
	$site = wp_nonce_url(SPAJAXURL.'subs-digest&targetaction=topiclist&page='.$thispage, 'subs-digest');
	$gif = SPCOMMONIMAGES.'working.gif';
	$out.= '<a class="page-numbers spSubsShowSubs" data-target="subsdisplayspot" data-site="'.$site.'" data-img="'.$gif.'">'.$thispage.'</a>';

	return $out;
}

function sp_subscriptions_do_group_status($out, $a) {
	$subs = SP()->options->get('subscriptions');
	if ($subs['forumsubs'] && SP()->user->thisUser->member) {
		if (SP()->auths->get('subscribe', SP()->forum->view->thisForum->forum_id) && ((!SP()->forum->view->thisForum->forum_status && !SP()->core->forumData['lockdown']) || SP()->user->thisUser->admin)) {
			if (sp_subscriptions_is_forum_subscribed(SP()->user->thisUser->ID, SP()->forum->view->thisForum->forum_id)) {
				$url = SP()->spPermalinks->build_url('', '', 1, 0).SP()->spPermalinks->get_query_char()."unsubforum=".SP()->forum->view->thisForum->forum_id;
				$out.= "<a href='$url' class='' title='".__('Unsubscribe from this forum', 'sp-subs')."'>\n";
				$out.= SP()->theme->paint_icon('', SIMAGES, 'sp_SubscriptionsUnsubscribeForum.png');
				$out.= "</a>\n";
			} else {
				$url = SP()->spPermalinks->build_url('', '', 1, 0).SP()->spPermalinks->get_query_char()."subforum=".SP()->forum->view->thisForum->forum_id;
				$out.= "<a href='$url' class='' title='".__('Subscribe to this forum', 'sp-subs')."'>\n";
				$out.= SP()->theme->paint_icon('', SIMAGES,'sp_SubscriptionsSubscribeForum.png');
				$out.= "</a>\n";
			}
			$out = apply_filters('sph_subs_group_status_icon', $out);
		}
	}

	return $out;
}

function sp_subscriptions_do_subforum_status($out) {
	$subs = SP()->options->get('subscriptions');
	if ($subs['forumsubs'] && SP()->user->thisUser->member) {
		if (SP()->auths->get('subscribe', SP()->forum->view->thisSubForum->forum_id) && ((!SP()->forum->view->thisSubForum->forum_status && !SP()->core->forumData['lockdown']) || SP()->user->thisUser->admin)) {
			if (sp_subscriptions_is_forum_subscribed(SP()->user->thisUser->ID, SP()->forum->view->thisSubForum->forum_id)) {
				$url = SP()->spPermalinks->build_url('', '', 1, 0).SP()->spPermalinks->get_query_char()."unsubforum=".SP()->forum->view->thisSubForum->forum_id;
				$out.= "<a href='$url' class='' title='".__('Unsubscribe from this forum', 'sp-subs')."'>\n";
				$out.= SP()->theme->paint_icon('', SIMAGES, 'sp_SubscriptionsUnsubscribeForum.png');
				$out.= "</a>\n";
			} else {
				$url = SP()->spPermalinks->build_url('', '', 1, 0).SP()->spPermalinks->get_query_char()."subforum=".SP()->forum->view->thisSubForum->forum_id;
				$out.= "<a href='$url' class='' title='".__('Subscribe to this forum', 'sp-subs')."'>\n";
				$out.= SP()->theme->paint_icon('', SIMAGES,'sp_SubscriptionsSubscribeForum.png');
				$out.= "</a>\n";
			}
			$out = apply_filters('sph_subs_group_status_icon', $out);
		}
	}

	return $out;
}
