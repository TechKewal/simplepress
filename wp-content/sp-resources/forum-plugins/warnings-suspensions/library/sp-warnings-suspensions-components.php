<?php
/*
Simple:Press
Warning and Suspensions Plugin Support Routines
$LastChangedDate: 2018-08-04 11:37:53 -0500 (Sat, 04 Aug 2018) $
$Rev: 15677 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_warnings_suspensions_do_load_admin_js($footer) {
	$css = SP()->theme->find_css(SPWARNCSS, 'jquery-ui.css');
	wp_enqueue_style('sp-warnings-ui', $css);

	wp_enqueue_script('jquery-ui-datepicker', false, array('jquery', 'jquery-ui-core', 'jquery-ui-widget'), false, $footer);
    wp_enqueue_script('jquery-ui-autocomplete', false, array('jquery', 'jquery-ui-core', 'jquery-ui-widget'));
}

function sp_warnings_suspensions_do_admin_caps_update($still_admin, $remove_admin, $user) {
	$manage_warnings = (isset($_POST['manage-warnings'])) ? $_POST['manage-warnings'] : '';
	$old_warnings = (isset($_POST['old-warnings'])) ? $_POST['old-warnings'] : '';

	# was this admin removed?
	if (isset($remove_admin[$user->ID])) $manage_warnings = '';

	if (isset($manage_warnings[$user->ID])) {
		$user->add_cap('SPF Manage Warnings');
	} else {
		$user->remove_cap('SPF Manage Warnings');
	}
	$still_admin = $still_admin || isset($manage_warnings[$user->ID]);
	return $still_admin;
}

function sp_warnings_suspensions_do_admin_caps_new($newadmin, $user) {
	$warnings = (isset($_POST['add-warnings'])) ? $_POST['add-warnings'] : '';
	if ($warnings == 'on') $user->add_cap('SPF Manage Warnings');
	$newadmin = $newadmin || $warnings == 'on';
	return $newadmin;
}

function sp_warnings_suspensions_do_admin_cap_list($user) {
	$manage_warnings = user_can($user, 'SPF Manage Warnings');
	echo '<li>';
	spa_render_caps_checkbox(__('Manage Warnings', 'sp-warnings-suspensions'), "manage-warnings[$user->ID]", $manage_warnings, $user->ID);
	echo "<input type='hidden' name='old-warnings[$user->ID]' value='$manage_warnings' />";
	echo '</li>';
}

function sp_warnings_suspensions_do_admin_cap_form($user) {
	echo '<li>';
	spa_render_caps_checkbox(__('Manage Warnings', 'sp-warnings-suspensions'), 'add-warnings', 0);
	echo '</li>';
}

function sp_warnings_suspensions_do_profile_message($out, $userid, $thisSlug) {
	$data = SP()->options->get('warnings-suspensions');
	if (isset($data['profile']) && $data['profile']) {
		$flagged = SP()->DB->select('SELECT * FROM '.SPWARNINGS." WHERE user_id=$userid");
		if ($flagged) {
			$out.= '<div class="spMessage">';
			foreach ($flagged as $flag) {
				switch ($flag->warn_type) {
					case 1:
						$out.= '<p>'.sprintf(SP()->displayFilters->text($data['warn_profile']), date('F j, Y', strtotime($flag->expiration))).'</p>';
						break;

					case 2:
						$out.= '<p>'.sprintf(SP()->displayFilters->text($data['suspension_profile']), date('F j, Y', strtotime($flag->expiration))).'</p>';
						break;

					case 3:
						$out.= '<p>'.SP()->displayFilters->text($data['ban_profile']).'</p>';
						break;
				}
			}
			$out.= '</div>';
		}
	}
	return $out;
}

function sp_warnings_suspensions_do_cron_handler() {
	# grab any expired warnings and suspensions
	$today = date("Y-m-d 00:00:00");
	$expired = SP()->DB->table(SPWARNINGS, "DATE(now()) >= DATE(expiration) AND DATE(expiration) > DATE('2013-09-01')");
	if ($expired) {
		foreach ($expired as $expire) {
			# remove current membership
			SP()->DB->execute('DELETE FROM '.SPMEMBERSHIPS." WHERE user_id=$expire->user_id");

			# restore memberships if a suspension
			if ($expire->warn_type == SPWARNSUSPENSION) {
				if (!empty($expire->saved_memberships)) {
					$saved = unserialize($expire->saved_memberships);
					foreach ($saved as $membership) {
						SP()->user->add_membership($membership['id'], $expire->user_id);
					}
				}
			}

			# remove the warning/suspension/ban
			SP()->DB->execute("DELETE FROM ".SPWARNINGS." WHERE warn_id=$expire->warn_id");
		}
	}
}

function sp_warnings_suspensions_do_head() {
	$css = SP()->theme->find_css(SPWARNCSS, 'jquery-ui.css');
	SP()->plugin->enqueue_style('sp-warnings-ui', $css);
}

function sp_warnings_suspensions_do_load_js ($footer) {
	$css = SP()->theme->find_css(SPWARNCSS, 'jquery-ui.css');
	SP()->plugin->enqueue_style('sp-warnings-ui', $css);


    $script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPWARNSCRIPT.'sp-warnings-suspensions.js' : SPWARNSCRIPT.'sp-warnings-suspensions.min.js';
	SP()->plugin->enqueue_script('spwarnings', $script, array('jquery', 'jquery-form', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-datepicker'), false, $footer);
}

function sp_warnings_suspensions_notify_warning($userid, $expire) {
	# Let user know they have been warned
	$data = SP()->options->get('warnings-suspensions');
	if ($data['notify'] == 1) {
		# sp notifications
		$nData = array();
		$nData['user_id']		= $userid;
		$nData['guest_email']	= '';
		$nData['post_id']		= '';
		$nData['link']			= '';
		$nData['link_text']		= '';
		$nData['message']		= sprintf(SP()->displayFilters->text($data['warn_message']), date('F j, Y', strtotime($expire)));
		$nData['expires']		= time() + (30 * 24 * 60 * 60); # 30 days; 24 hours; 60 mins; 60secs
		SP()->notifications->add($nData);
	} else if (SP()->plugin->is_active('private-messaging/sp-pm-plugin.php')) {
		# pm notification
		$newpm = array();
		$newpm['title'] = $data['warn_title'];
		$newpm['slug'] = sp_create_slug($newpm['title'], true, SPPMTHREADS, 'thread_slug');
		$newpm['messagecontent'] = sprintf(SP()->displayFilters->text($data['warn_message']), date('F j, Y', strtotime($expire)));
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
		SP()->DB->execute("INSERT INTO ".SPPMRECIPIENTS." (thread_id, message_id, user_id, read_status, pm_type) VALUES ({$newpm['thread_id']}, {$newpm['message_id']}, $userid, 0, 1)");
		SP()->DB->execute("INSERT INTO ".SPPMRECIPIENTS." (thread_id, message_id, user_id, read_status, pm_type) VALUES ({$newpm['thread_id']}, {$newpm['message_id']}, ".SP()->user->thisUser->ID.", 1, 1)");
	}
}

function sp_warnings_suspensions_notify_suspension($userid, $expire) {
	# Let user know they have been suspended
	$data = SP()->options->get('warnings-suspensions');
	if ($data['notify'] == 1) {
		# sp notifications
		$nData = array();
		$nData['user_id']		= $userid;
		$nData['guest_email']	= '';
		$nData['post_id']		= '';
		$nData['link']			= '';
		$nData['link_text']		= '';
		$nData['message']		= sprintf(SP()->displayFilters->text($data['suspension_message']), date('F j, Y', strtotime($expire)));
		$nData['expires']		= time() + (30 * 24 * 60 * 60); # 30 days; 24 hours; 60 mins; 60secs
		SP()->notifications->add($nData);
	} else if (SP()->plugin->is_active('private-messaging/sp-pm-plugin.php')) {
		# pm notification
		$newpm = array();
		$newpm['title'] = $data['suspension_title'];
		$newpm['slug'] = sp_create_slug($newpm['title'], true, SPPMTHREADS, 'thread_slug');
		$newpm['messagecontent'] = sprintf(SP()->displayFilters->text($data['suspension_message']), date('F j, Y', strtotime($expire)));
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
		SP()->DB->execute("INSERT INTO ".SPPMRECIPIENTS." (thread_id, message_id, user_id, read_status, pm_type) VALUES ({$newpm['thread_id']}, {$newpm['message_id']}, $userid, 0, 1)");
		SP()->DB->execute("INSERT INTO ".SPPMRECIPIENTS." (thread_id, message_id, user_id, read_status, pm_type) VALUES ({$newpm['thread_id']}, {$newpm['message_id']}, ".SP()->user->thisUser->ID.", 1, 1)");
	}
}

function sp_warnings_suspensions_notify_ban($userid) {
	# Let user know they have been banned
	$data = SP()->options->get('warnings-suspensions');
	if ($data['notify'] == 1) {
		# sp notifications
		$nData = array();
		$nData['user_id']		= $userid;
		$nData['guest_email']	= '';
		$nData['post_id']		= '';
		$nData['link']			= '';
		$nData['link_text']		= '';
		$nData['message']		= SP()->displayFilters->text($data['ban_message']);
		$nData['expires']		= time() + (30 * 24 * 60 * 60); # 30 days; 24 hours; 60 mins; 60secs
		SP()->notifications->add($nData);
	} else if (SP()->plugin->is_active('private-messaging/sp-pm-plugin.php')) {
		# pm notification
		$newpm = array();
		$newpm['title'] = $data['ban_title'];
		$newpm['slug'] = sp_create_slug($newpm['title'], true, SPPMTHREADS, 'thread_slug');
		$newpm['messagecontent'] = SP()->displayFilters->text($data['ban_message']);
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
		SP()->DB->execute("INSERT INTO ".SPPMRECIPIENTS." (thread_id, message_id, user_id, read_status, pm_type) VALUES ({$newpm['thread_id']}, {$newpm['message_id']}, $userid, 0, 1)");
		SP()->DB->execute("INSERT INTO ".SPPMRECIPIENTS." (thread_id, message_id, user_id, read_status, pm_type) VALUES ({$newpm['thread_id']}, {$newpm['message_id']}, ".SP()->user->thisUser->ID.", 1, 1)");
	}
}

function sp_warnings_suspensions_do_forum_tools($out, $post, $forum, $topic, $page, $postnum, $useremail, $guestemail, $displayname, $br) {
    $tout = '';
	# cab the user manage warnings?	 And dont let them warn, suspend or ban admins
	if (!SP()->auths->forum_admin($post['user_id']) && SP()->auths->current_user_can('SPF Manage Warnings')) {
		$warned = SP()->DB->select('SELECT warn_id FROM '.SPWARNINGS.' WHERE user_id='.$post['user_id'].' AND warn_type='.SPWARNWARNING, 'var');
		if ($warned) {
			$tout = sp_open_grid_cell();
			$tout.= '<div class="spForumToolsWarn">';
			$site = wp_nonce_url(SPAJAXURL."warnings-suspensions-admin&targetaction=delwarning&wid=$warned&uid=".$post['user_id'], 'warnings-suspensions-admin');
			$title = __('Remove user warning', 'sp-warnings-suspensions');
			$msg = esc_attr(__('User warning removed', 'sp-warnings-suspensions'));
			$tout.= '<a rel="nofollow" class="spWarningsRemoveWarning" data-site="'.$site.'" data-msg="'.$msg.'">';
			$tout.= SP()->theme->paint_icon('spIcon', SPWARNIMAGES, 'sp_ToolsWarnOff.png').$br;
			$tout.= $title.'</a>';
			$tout.= '</div>';
			$tout.= sp_close_grid_cell();
		} else {
			$tout = sp_open_grid_cell();
			$tout.= '<div class="spForumToolsUnWarn">';
			$site = wp_nonce_url(SPAJAXURL."warnings-suspensions-admin&targetaction=newwarning&uid=".$post['user_id'], 'warnings-suspensions-admin');
			$title = esc_attr(__('Warn this user', 'sp-warnings-suspensions'));
			$tout.= '<a rel="nofollow" class="spOpenDialog" data-site="'.$site.'" data-label="'.$title.'" data-width="400" data-height="0" data-align="center">';
			$tout.= SP()->theme->paint_icon('spIcon', SPWARNIMAGES, 'sp_ToolsWarnOn.png').$br;
			$tout.= $title.'</a>';
			$tout.= '</div>';
			$tout.= sp_close_grid_cell();
		}
		$tout = apply_filters('sph_post_tool_warning', $tout);
		$out.= $tout;

		$suspended = SP()->DB->select('SELECT warn_id FROM '.SPWARNINGS.' WHERE user_id='.$post['user_id'].' AND warn_type='.SPWARNSUSPENSION, 'var');
		if ($suspended) {
			$tout = sp_open_grid_cell();
			$tout.= '<div class="spForumToolsSuspend">';
			$site = wp_nonce_url(SPAJAXURL."warnings-suspensions-admin&targetaction=delsuspension&wid=$suspended&uid=".$post['user_id'], 'warnings-suspensions-admin');
			$title = __('Remove user suspension', 'sp-warnings-suspensions');
			$msg = esc_attr(__('User suspension removed', 'sp-warnings-suspensions'));
			$tout.= '<a rel="nofollow" class="spWarningsRemoveSuspension" data-site="'.$site.'" data-msg="'.$msg.'">';
			$tout.= SP()->theme->paint_icon('spIcon', SPWARNIMAGES, 'sp_ToolsSuspendOff.png').$br;
			$tout.= $title.'</a>';
			$tout.= '</div>';
			$tout.= sp_close_grid_cell();
		} else {
			$tout = sp_open_grid_cell();
			$tout.= '<div class="spForumToolsUnSuspend">';
			$site = wp_nonce_url(SPAJAXURL."warnings-suspensions-admin&targetaction=newsuspension&uid=".$post['user_id'], 'warnings-suspensions-admin');
			$title = esc_attr(__('Suspend this user', 'sp-warnings-suspensions'));
			$tout.= '<a rel="nofollow" class="spOpenDialog" data-site="'.$site.'" data-label="'.$title.'" data-width="400" data-height="0" data-align="center">';
			$tout.= SP()->theme->paint_icon('spIcon', SPWARNIMAGES, 'sp_ToolsSuspendOn.png').$br;
			$tout.= $title.'</a>';
			$tout.= '</div>';
			$tout.= sp_close_grid_cell();
		}
		$tout = apply_filters('sph_post_tool_suspension', $tout);
		$out.= $tout;

		$banned = SP()->DB->select('SELECT warn_id FROM '.SPWARNINGS.' WHERE user_id='.$post['user_id'].' AND warn_type='.SPWARNBAN, 'var');
		if ($banned) {
			$tout = sp_open_grid_cell();
			$tout.= '<div class="spForumToolsBan">';
			$site = wp_nonce_url(SPAJAXURL."warnings-suspensions-admin&targetaction=delban&wid=$banned&uid=".$post['user_id'], 'warnings-suspensions-admin');
			$title = __('Remove user ban', 'sp-warnings-suspensions');
			$msg = esc_attr(__('User ban removed', 'sp-warnings-suspensions'));
			$tout.= '<a rel="nofollow" class="spWarningsRemoveBan" data-site="'.$site.'" data-msg="'.$msg.'">';
			$tout.= SP()->theme->paint_icon('spIcon', SPWARNIMAGES, 'sp_ToolsBanOff.png').$br;
			$tout.= $title.'</a>';
			$tout.= '</div>';
			$tout.= sp_close_grid_cell();
		} else {
			$tout = sp_open_grid_cell();
			$tout.= '<div class="spForumToolsUnBan">';
			$site = wp_nonce_url(SPAJAXURL."warnings-suspensions-admin&targetaction=newban&uid=".$post['user_id'], 'warnings-suspensions-admin');
			$title = esc_attr(__('Ban this user', 'sp-warnings-suspensions'));
			$tout.= '<a rel="nofollow" class="spOpenDialog" data-site="'.$site.'" data-label="'.$title.'" data-width="400" data-height="0" data-align="center">';
			$tout.= SP()->theme->paint_icon('spIcon', SPWARNIMAGES, 'sp_ToolsBanOn.png').$br;
			$tout.= $title.'</a>';
			$tout.= '</div>';
			$tout.= sp_close_grid_cell();
		}
	}
	$tout = apply_filters('sph_post_tool_ban', $tout);
	$out.= $tout;

	return $out;
}

function sp_warnings_suspensions_usergroup_select() {
	require_once SP_PLUGIN_DIR.'/admin/library/spa-support.php';
	$usergroups = spa_get_usergroups_all();
	$out = '';
	$out.= '<label for"usergroup_id" class="spLabel">'.__('Select usergroup to move user into', 'sp-warnings-suspensions').':</label>&nbsp;&nbsp;';
	$out.= "<select style='width:145px' class='spControl' name='usergroup_id'>";
	$out.= '<option value="-1">'.__('Select usergroup', 'sp-warnings-suspensions').'</option>';
	foreach ($usergroups as $usergroup) {
		$out.= '<option value="'.$usergroup->usergroup_id.'">'.SP()->displayFilters->title($usergroup->usergroup_name).'</option>'."\n";
	}
	$out.= '</select>';
	return $out;
}
