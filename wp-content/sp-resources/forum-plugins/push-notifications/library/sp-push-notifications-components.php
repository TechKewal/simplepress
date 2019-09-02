<?php
/*
Simple:Press
Topic Push Notifications Plugin Support Routines
$LastChangedDate: 2019-02-04 14:35:02 -0500 (Mon, 04 Fed 2019) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_push_notifications_do_reset_profile_tabs() {

	SP()->profile->add_tab('Push Notifications');

	
	if(SP()->auths->get('pushover', '', get_current_user_id())){
		SP()->profile->add_menu('Push Notifications', 'Topic Pushover Subscriptions', SPPNFORMSDIR.'sp-push-notifications-pushover-form.php');
	}
	
	if(SP()->auths->get('pushbullet', '', get_current_user_id())){
		SP()->profile->add_menu('Push Notifications', 'Topic Pushbullet Subscriptions', SPPNFORMSDIR.'sp-push-notifications-pushbullet-form.php');
	}
	
	if(SP()->auths->get('onesignal', '', get_current_user_id())){
		SP()->profile->add_menu('Push Notifications', 'Topic Onesignal Subscriptions', SPPNFORMSDIR.'sp-push-notifications-onesignal-form.php');
	}

}

function sp_push_notifications_do_header() {
	$css = SP()->theme->find_css(SPPNCSS, 'sp-pushnotifications.css', 'sp-pushnotifications.css');
	SP()->plugin->enqueue_style('sp-pushnotifications', $css);
}

function sp_push_notifications_do_topic_form_options($display, $thisForum) {

	$last_topic_id = 0;
	foreach($thisForum->topics as $topic)
		if($topic->topic_id >= $last_topic_id)
			$last_topic_id = $topic->topic_id + 1;

	global $tab;

	$out = '';

	if (SP()->auths->get('pushover', '', SP()->user->thisUser->ID)) {
			$label = apply_filters('sph_subs_subscribe_label', __('(Pushover) Subscribe to this topic', 'push-notifications'));
			$out.= '<input type="checkbox" tabindex="'.$tab++.'" class="spControl" name="pushover_topicsub" id="pushover_sftopicsub" />';
			$out.= '<label class="spLabel spCheckbox" for="pushover_sftopicsub">'.$label.'</label><br>';
	}

	if (SP()->auths->get('pushbullet', '', SP()->user->thisUser->ID)) {
			$label = apply_filters('sph_subs_subscribe_label', __('(Pushbullet) Subscribe to this topic', 'push-notifications'));
			$out.= '<input type="checkbox" tabindex="'.$tab++.'" class="spControl" name="pushbullet_topicsub" id="pushbullet_sftopicsub" />';
			$out.= '<label class="spLabel spCheckbox" for="pushbullet_sftopicsub">'.$label.'</label><br>';
	}
	
	if (SP()->auths->get('onesignal', '', SP()->user->thisUser->ID)) {
			$label = apply_filters('sph_subs_subscribe_label', __('(Onesignal) Subscribe to this topic', 'push-notifications'));
			$out.= '<input type="checkbox" tabindex="'.$tab++.'" class="spControl" name="onesignal_topicsub" id="onesignal_sftopicsub" />';
			$out.= '<label class="spLabel spCheckbox" for="onesignal_sftopicsub">'.$label.'</label><br>';
	}

	$out.= '<input type="hidden" name="topicid" value="'.$last_topic_id.'" />';

	return $display.$out;
}

function sp_push_notifications_do_post_form_options($display, $thisTopic) {

	global $tab;

	$out = '';

	if (SP()->auths->get('pushover', '', SP()->user->thisUser->ID)) {

		$pushover_subscribed = sp_push_notifications_is_subscribed(SP()->user->thisUser->ID, $thisTopic->topic_id, SPACTIVITY_SUBS_PUSHOVER_TOPIC);
		
		if (!$pushover_subscribed) {
			$label = apply_filters('sph_subs_subscribe_label', __('(Pushover) Subscribe to this topic', 'push-notifications'));
			$out.= '<input type="checkbox" tabindex="'.$tab++.'" class="spControl" name="pushover_topicsub" id="pushover_sftopicsub" />';
			$out.= '<label class="spLabel spCheckbox" for="pushover_sftopicsub">'.$label.'</label><br>';
		} else {
			$label = apply_filters('sph_subs_unsubscribe_label', __('(Pushover) Unsubscribe from this topic', 'push-notifications'));
			$out.= '<input type="checkbox" tabindex="'.$tab++.'" class="spControl" name="pushover_topicsubend" id="pushover_sftopicsubend" />';
			$out.= '<label class="spLabel spCheckbox" for="pushover_sftopicsubend">'.$label.'</label><br>';
		}

	}

	if (SP()->auths->get('pushbullet', '', SP()->user->thisUser->ID)) {
		
		$pushbullet_subscribed = sp_push_notifications_is_subscribed(SP()->user->thisUser->ID, $thisTopic->topic_id, SPACTIVITY_SUBS_PUSHBULLET_TOPIC);
		
		if (!$pushbullet_subscribed) {
			$label = apply_filters('sph_subs_subscribe_label', __('(Pushbullet) Subscribe to this topic', 'push-notifications'));
			$out.= '<input type="checkbox" tabindex="'.$tab++.'" class="spControl" name="pushbullet_topicsub" id="pushbullet_sftopicsub" />';
			$out.= '<label class="spLabel spCheckbox" for="pushbullet_sftopicsub">'.$label.'</label><br>';
		} else {
			$label = apply_filters('sph_subs_unsubscribe_label', __('(Pushbullet) Unsubscribe from this topic', 'push-notifications'));
			$out.= '<input type="checkbox" tabindex="'.$tab++.'" class="spControl" name="pushbullet_topicsubend" id="pushbullet_sftopicsubend" />';
			$out.= '<label class="spLabel spCheckbox" for="pushbullet_sftopicsubend">'.$label.'</label><br>';
		}

	}
	
	if (SP()->auths->get('onesignal', '', SP()->user->thisUser->ID)) {

		$onesignal_subscribed = sp_push_notifications_is_subscribed(SP()->user->thisUser->ID, $thisTopic->topic_id, SPACTIVITY_SUBS_ONESIGNAL_TOPIC);

		if (!$onesignal_subscribed) {
			$label = apply_filters('sph_subs_subscribe_label', __('(Onesignal) Subscribe to this topic', 'push-notifications'));
			$out.= '<input type="checkbox" tabindex="'.$tab++.'" class="spControl" name="onesignal_topicsub" id="onesignal_sftopicsub" />';
			$out.= '<label class="spLabel spCheckbox" for="onesignal_sftopicsub">'.$label.'</label><br>';
		} else {
			$label = apply_filters('sph_subs_unsubscribe_label', __('(Onesignal) Unsubscribe from this topic', 'push-notifications'));
			$out.= '<input type="checkbox" tabindex="'.$tab++.'" class="spControl" name="onesignal_topicsubend" id="onesignal_sftopicsubend" />';
			$out.= '<label class="spLabel spCheckbox" for="onesignal_sftopicsubend">'.$label.'</label><br>';
		}
	}

	return $display.$out;
}

function sp_push_notifications_do_post_notification($retmsg, $newpost) {

	$nots = SP()->options->get('push-notifications');

	$users_pushover = array();
	$eol = "\r\n";
	$uid = get_current_user_id();

	# start gathering data for the email
	$forum = SP()->DB->table(SPFORUMS, "forum_id=".$newpost['forumid']);

	$users_pushover_topic = SP()->activity->get_col('col=uid&type='.SPACTIVITY_SUBS_PUSHOVER_TOPIC."&item=".$newpost['topicid']);
	$users_pushover_forum = SP()->activity->get_col('col=uid&type='.SPACTIVITY_SUBS_PUSHOVER_FORUM."&item=".$forum[0]->forum_id);
	$users_pushover = array_unique(array_merge($users_pushover_topic, $users_pushover_forum), SORT_REGULAR);
	$users_pushover = apply_filters('sph_subscriptions_forum_list', $users_pushover, $newpost);

	$users_pushbullet_topic = SP()->activity->get_col('col=uid&type='.SPACTIVITY_SUBS_PUSHBULLET_TOPIC."&item=".$newpost['topicid']);
	$users_pushbullet_forum = SP()->activity->get_col('col=uid&type='.SPACTIVITY_SUBS_PUSHBULLET_FORUM."&item=".$forum[0]->forum_id);
	$users_pushbullet = array_unique(array_merge($users_pushbullet_topic, $users_pushbullet_forum), SORT_REGULAR);
	$users_pushbullet = apply_filters('sph_subscriptions_forum_list', $users_pushbullet, $newpost);

	$users_onesignal_topic = SP()->activity->get_col('col=uid&type='.SPACTIVITY_SUBS_ONESIGNAL_TOPIC."&item=".$newpost['topicid']);
	$users_onesignal_forum = SP()->activity->get_col('col=uid&type='.SPACTIVITY_SUBS_ONESIGNAL_FORUM."&item=".$forum[0]->forum_id);
	$users_onesignal = array_unique(array_merge($users_onesignal_topic, $users_onesignal_forum), SORT_REGULAR);
	$users_onesignal = apply_filters('sph_subscriptions_forum_list', $users_onesignal, $newpost);



	foreach($users_pushover as $user){


		// Pushover
		if($nots['pushover'] != ''){

			$body = array(
				'token'	=> $nots['pushover'],
				'user' => get_user_meta($user, 'pushover_key')['0'],
				'message' => $newpost['postcontent'].' '.$newpost["url"],
				'title'	=> $newpost['topicname']
			);

			$req_args = array('body' => $body);

			// Where the magic happens
			$response = wp_remote_post( 'https://api.pushover.net/1/messages.json', $req_args );
			
			if($response['response']['code'] != '200'){

				$error_report = array(
					'Date'	  => date("m.d.y"),
					'Service' => 'pushover',
					'Code'	  => $response['response']['code'],
					'Message' => $response['response']['message'].' ',
					'UserID'  => $user,
					'UserAPI' => get_user_meta($user, 'pushover_key')['0'],
					'Topic'   => $newpost['topicname']
				);

				error_log(implode(', ', $error_report));
			
			}
		}
	}



	foreach($users_pushbullet as $user){

		// Pushbullet
		if($nots['pushbullet'] != ''){

			$ch =   curl_init('https://api.pushbullet.com/v2/users/me');
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(
						'Content-Type: application/json',
						'Access-Token: '.get_user_meta($user, 'pushbullet_key')['0'],                                                                         
						'Content-Length: 0' )
					);
 
			$user_info = curl_exec($ch);
			curl_close($ch);
			$user_email = json_decode($user_info)->email;

			$req_args = array(
				'headers' => array(
					 'Authorization' => 'Basic ' . base64_encode($nots['pushbullet'].':')
					 ),
				'timeout' => 50,
				'sslverify' => FALSE,
				'method' => 'post',
				'body'=>array(
					'type' => 'note',
					'title' => $newpost['topicname'],
					'body'=>$newpost['postcontent'],
					'email'=>$user_email,
					'url'=>$newpost['url']
				)
			);

			// Where the magic happens
			if (get_user_meta($user, 'pushbullet_key') != '') {
				// Api v2
				$response = wp_remote_post( 'https://api.pushbullet.com/v2/pushes', $req_args );
			} else {
				// Api v1
				$response = wp_remote_post( 'https://api.pushbullet.com/api/pushes', $req_args );
			}

			if($response['response']['code'] != '200'){

				$error_report = array(
					'Date'	  => date("m.d.y"),
					'Service' => 'pushbullet',
					'Code'	  => $response['response']['code'],
					'Message' => $response['response']['message'].' | '.json_decode($response['body'])->errors['0'],
					'UserID'  => $user,
					'Topic'   => $newpost['topicname']
				);

				error_log(implode(', ', $error_report));

			}
		}
	}

	foreach($users_onesignal as $user){




		// Onesignal
		if($nots['onesignal'] != '' && !empty($users_onesignal) &&
		   get_user_meta($user, 'onesignal_key')['0'] != ''){

			$content = array(
				"en" => $newpost['postcontent']
				);
			
			$fields = array(
				'app_id' => $nots['onesignal'],
				'headings' => array("en" => $newpost['topicname']),
				'include_player_ids' => explode(', ',get_user_meta($user, 'onesignal_key')['0']),
				'contents' => $content,
				'url' => $newpost['url']
			);
			
			$fields = json_encode($fields);
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	
			$response = curl_exec($ch);
			curl_close($ch);

			if($response['response']['code'] != '200' && isset($response['response'])){

				$error_report = array(
					'Date'	  => date("m.d.y"),
					'Service' => 'onesignal',
					'Code'	  => $response['response']['code'],
					'Message' => $response['response']['message'].' | '.json_decode($response['body'])->errors['0'],
					'UserID'  => $user,
					'Topic'   => $newpost['topicname'],
				);

				error_log(implode(', ', $error_report));

			}

		}

	}

}

function sp_push_notifications_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'push-notifications/sp-push-notifications-plugin.php') {
		
		$url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
		$actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'push-notifications')."'>".__('Uninstall', 'push-notifications').'</a>';

		$url = SPADMINCOMPONENTS.'&amp;tab=plugin&amp;admin=sp_push_notifications_admin_members&amp;save=sp_push_notifications_admin_save_members&amp;form=1';
		$actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'push-notifications')."'>".__('Options', 'push-notifications').'</a>';
	}
	return $actionlink;
}

function sp_push_notifications_do_process_actions() {

	require_once SPPNLIBDIR.'sp-push-notifications-database.php';

	// PUSHOVER
	if (isset($_GET['subforumpushover'])) {
		$forumid = SP()->filters->integer($_GET['subforumpushover']);
		if (SP()->auths->get('pushover', $forumid)) {
            sp_push_notifications_save_forum_subscription($forumid, SP()->user->thisUser->ID, true, 'pushover', SPACTIVITY_SUBS_PUSHOVER_FORUM);
            SP()->user->thisUser->forum_subscribe_pushover[] = $forumid;
        }
	}

	if (isset($_GET['unsubforumpushover'])) {
		$forumid = SP()->filters->integer($_GET['unsubforumpushover']);
		if (SP()->auths->get('pushover', $forumid)) {
            sp_push_notifications_remove_forum_subscription($forumid, SP()->user->thisUser->ID, true, SPACTIVITY_SUBS_PUSHOVER_FORUM);
            if (($key = array_search($forumid, SP()->user->thisUser->forum_subscribe_pushover)) !== false) unset(SP()->user->thisUser->forum_subscribe_pushover[$key]);
        }
	}

	// PUSHBULLET
	if (isset($_GET['subforumpushbullet'])) {
		$forumid = SP()->filters->integer($_GET['subforumpushbullet']);
		if (SP()->auths->get('pushbullet', $forumid)) {
            sp_push_notifications_save_forum_subscription($forumid, SP()->user->thisUser->ID, true, 'pushbullet', SPACTIVITY_SUBS_PUSHBULLET_FORUM);
            SP()->user->thisUser->forum_subscribe_pushbullet[] = $forumid;
		}
		
	}

	if (isset($_GET['unsubforumpushbullet'])) {
		$forumid = SP()->filters->integer($_GET['unsubforumpushbullet']);
		if (SP()->auths->get('pushbullet', $forumid)) {
            sp_push_notifications_remove_forum_subscription($forumid, SP()->user->thisUser->ID, true, SPACTIVITY_SUBS_PUSHBULLET_FORUM);
            if (($key = array_search($forumid, SP()->user->thisUser->forum_subscribe_pushbullet)) !== false) unset(SP()->user->thisUser->forum_subscribe_pushbullet[$key]);
        }
	}

	// ONESIGNAL
	if (isset($_GET['subforumonesignal'])) {
		$forumid = SP()->filters->integer($_GET['subforumonesignal']);
		if (SP()->auths->get('onesignal', $forumid)) {
            sp_push_notifications_save_forum_subscription($forumid, SP()->user->thisUser->ID, true, 'onesignal', SPACTIVITY_SUBS_ONESIGNAL_FORUM);
            SP()->user->thisUser->forum_subscribe_onesignal[] = $forumid;
        }
	}

	if (isset($_GET['unsubforumonesignal'])) {
		$forumid = SP()->filters->integer($_GET['unsubforumonesignal']);
		if (SP()->auths->get('onesignal', $forumid)) {
            sp_push_notifications_remove_forum_subscription($forumid, SP()->user->thisUser->ID, true, SPACTIVITY_SUBS_ONESIGNAL_FORUM);
            if (($key = array_search($forumid, SP()->user->thisUser->forum_subscribe_onesignal)) !== false) unset(SP()->user->thisUser->forum_subscribe_onesignal[$key]);
        }
	}

}

function sp_push_notifications_do_load_js($footer) {
	$sfauto = SP()->options->get('sfauto');
	if ($sfauto['sfautoupdate']) SP()->plugin->enqueue_script(
		'sp-pushnotificationscriptions-update', SPPNSCRIPT.'sp-pushnotificationscriptions-update.min.js', array('jquery'), false, $footer
	);

	$script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPPNSCRIPT.'sp-pushnotificationscriptions.js' : SPPNSCRIPT.'sp-pushnotificationscriptions.min.js';
	SP()->plugin->enqueue_script('sp-pushnotificationscriptions', $script, array('jquery'), false, $footer);

	$strings = array(
		'addsubtopic'	   => __('Subscription added', 'sp-pushnotifications'),
		'delsubtopic'	   => __('Subscription removed', 'sp-pushnotifications'),
        'nosubs'	       => __('You are not currently subscribed to any topics', 'sp-pushnotifications')
	);
	SP()->plugin->localize_script('sp-pushnotificationscriptions', 'sp_subs_vars', $strings);
}

function sp_push_notificarions_do_load_admin_js($footer) {
	$script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPPNSCRIPT.'sp-pushnotificationscriptions-admin.js' : SPPNSCRIPT.'sp-pushnotificationscriptions-admin.min.js';
	wp_enqueue_script('sp-pushnotificationscriptions-admin', $script, array('jquery'), false, $footer);
}

function sp_push_notifications_is_forum_subscribed($userid, $forumid, $service='', $forumType='') {

	if($service == 'pushover')
		$userSabscribe = SP()->user->thisUser->forum_subscribe_pushover;
	if($service == 'pushbullet')
		$userSabscribe = SP()->user->thisUser->forum_subscribe_pushbullet;
	if($service == 'onesignal')
		$userSabscribe = SP()->user->thisUser->forum_subscribe_onesignal;

	if (!$userid || !$forumid) return '';
	# see if we can use current user object to save queries
	if (SP()->user->thisUser->ID == $userid) {
		if (!property_exists(SP()->user->thisUser, 'forum_subscribe_'.$service) ||
			 empty($userSabscribe)
		) {
			$userSabscribe = SP()->activity->get_col('col=item&type='.$forumType.'&uid='.SP()->user->thisUser->ID);
		}
		$found = in_array($forumid, (array) $userSabscribe );
	} else {
		$found = SP()->activity->exist('type='.$forumType.'&uid=$userid&item=$forumid');
	}

	return $found;
}

function sp_push_notifications_is_subscribed($userid, $topicid, $activetype = false) {
	if (!$userid || !$topicid) return '';
	$found = SP()->activity->exist("type=$activetype&uid=$userid&item=$topicid");
	return $found;
}

function sp_push_notifications_topics_pn_next($cpage, $totalpages, $pnshow) {
	$start = ($cpage - $pnshow);
	if ($start < 1) $start = 1;
	$end = ($cpage - 1);

	$out = '';
	if ($start > 1) {
		$out.= sp_push_notifications_topics_pn_url(1);
		$out.= '<span class="page-numbers dota">...</span>';
	}

	if ($end > 0) {
		for ($i = $start; $i <= $end; $i++) {
			$out.= sp_push_notifications_topics_pn_url($i);
		}
	}

	return $out;
}

function sp_push_notifications_topics_pn_previous($cpage, $totalpages, $pnshow) {
	$start = ($cpage + 1);
	$end = ($cpage + $pnshow);
	if ($end > $totalpages) $end = $totalpages;

	$out = '';
	if ($start <= $totalpages) {
		for ($i = $start; $i <= $end; $i++) {
			$out.= sp_push_notifications_topics_pn_url($i);
		}
		if ($end < $totalpages) {
			$out.= '<span class="page-numbers dota">...</span>';
			$out.= sp_push_notifications_topics_pn_url($totalpages);
		}
	}

	return $out;
}

function sp_push_notifications_topics_pn_url($thispage) {
	$out = '';
	$site = wp_nonce_url(SPAJAXURL.'subs-topics&targetaction=topiclist&page='.$thispage, 'subs-topics');
	$gif = SPCOMMONIMAGES.'working.gif';
	$out.= '<a class="page-numbers spPushNotificationsShowSubs" data-target="sptopicsubs" data-site="'.$site.'" data-img="'.$gif.'">'.$thispage.'</a>';

	return $out;
}

function sp_push_notifications_forums_pn_next($cpage, $totalpages, $pnshow) {
	$start = ($cpage - $pnshow);
	if ($start < 1) $start = 1;
	$end = ($cpage - 1);

	$out = '';
	if ($start > 1) {
		$out.= sp_push_notifications_forums_pn_url(1);
		$out.= '<span class="page-numbers dota">...</span>';
	}
}
function sp_push_notifications_forums_pn_previous($cpage, $totalpages, $pnshow) {
	$start = ($cpage + 1);
	$end = ($cpage + $pnshow);
	if ($end > $totalpages) $end = $totalpages;

	$out = '';
	if ($start <= $totalpages) {
		for ($i = $start; $i <= $end; $i++) {
			$out.= sp_push_notifications_forums_pn_url($i);
		}
		if ($end < $totalpages) {
			$out.= '<span class="page-numbers dota">...</span>';
			$out.= sp_push_notifications_forums_pn_url($totalpages);
		}
	}

	return $out;
}

function sp_push_notifications_forums_pn_url($thispage) {
	$out = '';
	$site = wp_nonce_url(SPAJAXURL.'subs-forums&targetaction=forumlist&page='.$thispage, 'subs-forums');
	$gif = SPCOMMONIMAGES.'working.gif';
	$out.= '<a class="page-numbers spPushNotificationsShowSubs" data-target="spforumsubs" data-site="'.$site.'" data-img="'.$gif.'">'.$thispage.'</a>';

	return $out;
}

function sp_push_notifications_users_pn_next($cpage, $totalpages, $pnshow) {
	$start = ($cpage - $pnshow);
	if ($start < 1) $start = 1;
	$end = ($cpage - 1);

	$out = '';
	if ($start > 1) {
		$out.= sp_push_notifications_users_pn_url(1);
		$out.= '<span class="page-numbers dota">...</span>';
	}

	if ($end > 0) {
		for ($i = $start; $i <= $end; $i++) {
			$out.= sp_push_notifications_users_pn_url($i);
		}
	}

	return $out;
}

function sp_push_notifications_users_pn_previous($cpage, $totalpages, $pnshow) {
	$start = ($cpage + 1);
	$end = ($cpage + $pnshow);
	if ($end > $totalpages) $end = $totalpages;

	$out = '';
	if ($start <= $totalpages) {
		for ($i = $start; $i <= $end; $i++) {
			$out.= sp_push_notifications_users_pn_url($i);
		}
		if ($end < $totalpages) {
			$out.= '<span class="page-numbers dota">...</span>';
			$out.= sp_push_notifications_users_pn_url($totalpages);
		}
	}

	return $out;
}

function sp_push_notifications_users_pn_url($thispage) {
	$out = '';
	$site = wp_nonce_url(SPAJAXURL.'subs-users&targetaction=topiclist&page='.$thispage, 'subs-users');
	$gif = SPCOMMONIMAGES.'working.gif';
	$out.= '<a class="page-numbers spPushNotificationsShowSubs" data-target="sptopicusers" data-site="'.$site.'" data-img="'.$gif.'">'.$thispage.'</a>';

	return $out;
}