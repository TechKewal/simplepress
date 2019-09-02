<?php
/*
Simple:Press
Buddypress Plugin screens support components
$LastChangedDate: 2018-08-26 16:55:40 -0500 (Sun, 26 Aug 2018) $
$Rev: 15725 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

#
# forum profile
#

function sp_buddypress_profile_screen() {
	if (isset($_POST['submit'])) {
	    global $bp;

		# Check the nonce
		check_admin_referer('sp-buddypress-profile');

        # now parse the form
    	$errors = sp_buddypress_profile_submit();

        # allow others to interact
		$errors = apply_filters('sph_buddypress_updated_forum_profile', $errors, bp_displayed_user_id());

		# Check for errors
		if (!empty($errors)) {
			bp_core_add_message($errors, 'error');
		} else {
			bp_core_add_message(__('Forum profile changes saved.', 'sp-buddypress'));
        }

		# Redirect back to the profile screen to display the updates and message
		bp_core_redirect(trailingslashit(bp_displayed_user_domain().$bp->profile->slug.'/forum'));
	}

	add_action('bp_template_title', 'sp_buddypress_profile_screen_title');
	add_action('bp_template_content', 'sp_buddypress_profile_screen_content');
	bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
}

function sp_buddypress_profile_screen_title() {
	_e('Forum Profile Options', 'sp-buddypress');
}

function sp_buddypress_profile_screen_content() {
    global $bp;

	if (!bp_is_my_profile() && !bp_current_user_can('bp_moderate')) return false;

    require_once (SP_PLUGIN_DIR.'/forum/content/sp-profile-view-functions.php');

    SP()->user->get_current_user();
	sp_SetupUserProfileData(SP()->user->thisUser->ID);
?>
	<form action="<?php echo $bp->loggedin_user->domain.'profile/forum'; ?>" name="sp_buddypress_profile_form" id="sp_buddypress_profile_form" method="post">
<?php
        do_action('sph_buddypress_forum_profile_start');

        # timezone setting
        $tout = '';
        $tout.= '<p class="spProfileLabel">'.__('Select your timezone', 'sp-buddypress').': ';
        $tz = get_option('timezone_string');
        if (empty($tz) || substr($tz, 0, 3) == 'UTC') $tz = 'UTC';
        $tzUser = (!empty(SP()->user->profileUser->timezone_string)) ? SP()->user->profileUser->timezone_string : $tz;
        if (substr($tzUser, 0, 3) == 'UTC') $tzUser = 'UTC';
        $tout.= '<span class="spProfileData"><select class="spControl" id="timezone" name="timezone">';
        $wptz = explode('<optgroup label=', wp_timezone_choice($tzUser));
        unset($wptz[count($wptz)-1]);
        $tout.= implode('<optgroup label=', $wptz);
        $tout.= '</select></span></p>';
        $tout.= '<p><small>';
        $tout.= __('Server timezone set to', 'sp-buddypress').': <b>'.$tz.'</b><br />';

        date_default_timezone_set($tz);
        $now = localtime(time(), true);
        if ($now['tm_isdst']) {
        	$tout.= __('This timezone is currently in daylight savings time', 'sp-buddypress').'<br/>';
        } else {
        	$tout.= __('This timezone is currently in standard time', 'sp-buddypress').'<br/>';
        }
        $tout.= __('Server time is', 'sp-buddypress').': <b>'.date('Y-m-d G:i:s').'</b><br />';
        date_default_timezone_set($tzUser);
        $tout.= __('Local time is', 'sp-buddypress').': <b>'.date('Y-m-d G:i:s').'</b><br />';
        date_default_timezone_set('UTC');
        $tout.= __('UTC time is', 'sp-buddypress').': <b>'.date('Y-m-d G:i:s').'</b><br />';
        $tout.= '<a href="http://en.wikipedia.org/wiki/Time_zone">'.__('Help and explanation of timezones', 'sp-buddypress').'</a>';
        $tout.= '</small></p>';
        $out = apply_filters('sph_ProfileUserTimezone', $tout, SP()->user->profileUser->ID);

        # wp display name sync setting
        $spProfileOptions = SP()->options->get('sfprofile');
        if ($spProfileOptions['nameformat']) {
        	$tout = '';
        	$tout.= '<p class="spProfileLabel">'.__('Sync forum and WP display name', 'sp-buddypress').': ';
        	$checked = (SP()->user->profileUser->namesync) ? $checked = 'checked="checked" ' : '';
        	$tout.= '<span class="spProfileData"><input type="checkbox" '.$checked.'name="namesync" id="namesync" /></span></p>';
        	$out.= apply_filters('sph_ProfileUserSyncName', $tout, SP()->user->profileUser->ID);
        }

        # hide online status setting
        $opts = SP()->options->get('sfmemberopts');
        if ($opts['sfhidestatus']) {
        	$tout = '';
        	$tout.= '<p class="spProfileLabel">'.__('Hide online status', 'sp-buddypress').': ';
        	$checked = (SP()->user->profileUser->hidestatus) ? 'checked="checked" ' : '';
        	$tout.= '<span class="spProfileData"><input type="checkbox" '.$checked.'name="hidestatus" id="hidestatus" /></span></p>';
        	$out.= apply_filters('sph_ProfileUserOnlineStatus', $tout, SP()->user->profileUser->ID);
        }

        # number unread posts setting
        $sfcontrols = SP()->options->get('sfcontrols');
        if (isset($sfcontrols['sfusersunread']) && $sfcontrols['sfusersunread']) {
            $tout = '';
        	$tout.= '<p class="spProfileLabel">'.__('Max number of unread posts to display', 'sp-buddypress').' ('.__('max allowed is', 'sp-buddypress').' '.$sfcontrols['sfmaxunreadposts'].')'.': ';
            $number = (is_numeric(SP()->user->profileUser->unreadposts)) ? SP()->user->profileUser->unreadposts : $sfcontrols['sfdefunreadposts'];
        	$tout.= '<span class="spProfileData"><input class="spControl" type="text" name="unreadposts" id="unreadposts" value="'.$number.'" /></span</p>';
        	$out.= apply_filters('sph_ProfileUserUnread', $tout, SP()->user->profileUser->ID);
        }

        # prefered editor setting
        $tout = '';
        $tout.= '<p class="spProfileLabel">'.__('Preferred editor', 'sp-buddypress').':</p>';
        $checked = (SP()->user->profileUser->editor == PLAINTEXT) ? $checked = 'checked="checked" ' : '';
        $tout.= '<p class="spProfileRadioLabel">'.__('Plain Textarea', 'sp-buddypress').'<input type="radio" '.$checked.'name="editor" id="plaintext" value="'.PLAINTEXT.'"/></p>';
        $tout = apply_filters('sph_ProfilePostingOptionsFormEditors', $tout, SP()->user->profileUser);
        $tout.= '<p></p>';
        $out.= apply_filters('sph_ProfileUserEditor', $tout, SP()->user->profileUser->ID);

        # signature
        $tout = '';
        $tout.= '<p class="spProfileLabel">'.__('Set up your signature', 'sp-buddypress').':</p>';
		$tout.= '<textarea rows="10" cols="60" name="signature" id="signature">'.esc_html(SP()->user->profileUser->signature).'</textarea>';
        $tout.= '<p></p>';
        $out.= apply_filters('sph_ProfileSignatureForm', $tout, SP()->user->profileUser->ID);

        # subscription settings
        if (SP()->plugin->is_active('subscriptions/sp-subscriptions-plugin.php')) {
            if (SP()->auths->get('subscribe')) {
                $subs = SP()->options->get('subscriptions');
                $tout = '';
            	$tout.= '<p class="spProfileLabel">'.__('Auto subscribe to topics I post in', 'sp-buddypress').': ';
            	$checked = (isset(SP()->user->profileUser->autosubpost) && SP()->user->profileUser->autosubpost) ? $checked = 'checked="checked" ' : '';
            	$tout.= '<span class="spProfileData"><input type="checkbox" '.$checked.'name="subpost" id="subpost" /></span></p>';
            	$out.= apply_filters('sph_ProfileUserSubsAutoSub', $tout, SP()->user->profileUser->ID);

                $tout = '';
            	$tout.= '<p class="spProfileLabel">'.__('Auto subscribe to topics I start', 'sp-buddypress').': ';
            	$checked = (isset(SP()->user->profileUser->autosubstart) && SP()->user->profileUser->autosubstart) ? $checked = 'checked="checked" ' : '';
            	$tout.= '<span class="spProfileData"><input type="checkbox" '.$checked.'name="substart" id="substart" /></span></p>';
            	$out.= apply_filters('sph_ProfileUserSubsAutoStart', $tout, SP()->user->profileUser->ID);

            	if ($subs['digestsub']) {
                    $tout = '';
                    $digest = ($subs['digesttype'] == 1) ? __('daily', 'sp-buddypress') : __('weekly', 'sp-buddypress');
            		$tout.= '<p class="spProfileLabel">'.__('Receive subscription notifications in digest form', 'sp-buddypress').' ('.$digest.'): ';
            		$checked = (!empty(SP()->user->profileUser->subscribe_digest) && SP()->user->profileUser->subscribe_digest) ? $checked = 'checked="checked" ' : '';
            		$tout.= '<span class="spProfileData"><input type="checkbox" '.$checked.'name="subdigest" id="subdigest" /></span></p>';
                	$out.= apply_filters('sph_ProfileUserSubsDigest', $tout, SP()->user->profileUser->ID);
                }
            }
        }

        # mentions settings
        if (SP()->plugin->is_active('mentions/sp-mentions-plugin.php')) {
            $tout = '';
        	$tout.= '<p class="spProfileLabel">'.__('Opt out of receiving forum mentions notifications', 'sp-mentions').': ';
        	$checked = (isset(SP()->user->profileUser->mentionsoptout) && SP()->user->profileUser->mentionsoptout) ? $checked = 'checked="checked" ' : '';
        	$tout.= '<span class="spProfileData"><input type="checkbox" '.$checked.'name="mentionsoptout" id="mentionsoptout" /></span></p>';
        	$out.= apply_filters('sph_ProfileUserMentionsOptOut', $tout, SP()->user->profileUser->ID);
        }

        # private messaging
        if (SP()->plugin->is_active('private-messaging/sp-pm-plugin.php')) {
            $pm = SP()->options->get('pm');
        	if (sp_pm_get_auth('use_pm') && $pm['email']) {
                $tout = '';
        		$tout.= '<p class="spProfileLabel">'.__('Receive an email when someone sends you a forum private message', 'sp-pm').': ';
        		$checked = (SP()->user->profileUser->pmemail) ? $checked = 'checked="checked" ' : '';
        		$tout.= '<span class="spProfileData"><input type="checkbox" '.$checked.'name="pmemail" id="pmemail" /></span></p>';
            	$out.= apply_filters('sph_ProfileUserPMEmail', $tout, SP()->user->profileUser->ID);
        	}
        	if (sp_pm_get_auth('use_pm')) {
                $tout = '';
        		$tout.= '<p class="spProfileLabel">'.__('Opt out of forum private messaging', 'sp-pm').': ';
        		$checked = (isset(SP()->user->profileUser->pmoptout) && SP()->user->profileUser->pmoptout) ? $checked = 'checked="checked" ' : '';
        		$tout.= '<span class="spProfileData"><input type="checkbox" '.$checked.'name="pmoptout" id="pmoptout" /></span></p>';
            	$out.= apply_filters('sph_ProfileUserPMOptOut', $tout, SP()->user->profileUser->ID);
        	}
        }

        echo $out;
?>
        <?php do_action('sph_buddypress_forum_profile_end'); ?>
		<p class="submit"><input type="submit" value="<?php _e('Save Profile', 'sp-buddypress') ?>" id="submit" name="submit" /></p>

		<?php wp_nonce_field('sp-buddypress-profile'); ?>
	</form>
<?php
}

function sp_buddypress_profile_submit() {
    global $bp;

	if (!bp_is_my_profile() && !bp_current_user_can('bp_moderate')) bp_core_redirect(trailingslashit(bp_displayed_user_domain().$bp->profile->slug.'/profile/forum/'));

    SP()->user->get_current_user();

	$options = SP()->memberData->get(SP()->user->thisUser->ID, 'user_options');

    # save timezone
	if (isset($_POST['timezone'])) {
		if (preg_match('/^UTC[+-]/', $_POST['timezone']) ) {
			# correct for manual UTC offets
			$userOffset = preg_replace('/UTC\+?/', '', $_POST['timezone']) * 3600;
		} else {
			# get timezone offset for user
			$date_time_zone_selected = new DateTimeZone(SP()->filters->str($_POST['timezone']));
			$userOffset = timezone_offset_get($date_time_zone_selected, date_create());
		}

		# get timezone offset for server based on wp settings
		$wptz = get_option('timezone_string');
		if (empty($wptz)) {
			$serverOffset = get_option('gmt_offset');
		} else {
			$date_time_zone_selected = new DateTimeZone($wptz);
			$serverOffset = timezone_offset_get($date_time_zone_selected, date_create());
		}

		# calculate time offset between user and server
		$options['timezone'] = (int) round(($userOffset - $serverOffset) / 3600, 2);
		$options['timezone_string'] = SP()->filters->str($_POST['timezone']);
	} else {
		$options['timezone'] = 0;
		$options['timezone_string'] = 'UTC';
	}

    # save name sync
    $options['namesync'] = (isset($_POST['namesync'])) ? true : false;

    # save hide status
	$options['hidestatus'] = (isset($_POST['hidestatus'])) ? true : false;

    # save unread post count limit
	if (isset($_POST['unreadposts'])) {
        $sfcontrols = SP()->options->get('sfcontrols');
		$options['unreadposts'] = is_numeric($_POST['unreadposts']) ? max(min(SP()->filters->integer($_POST['unreadposts']), $sfcontrols['sfmaxunreadposts']), 0) : $sfcontrols['sfdefunreadposts'];
    }

    # save editor
    if (isset($_POST['editor'])) $options['editor'] = SP()->filters->integer($_POST['editor']);

    # save signature
	# Check if maxmium links has been exceeded
    $numLinks = substr_count($_POST['signature'], '</a>');
	$spFilters = SP()->options->get('sffilters');
	if (!SP()->auths->get('create_links', 'global', SP()->user->thisUser->ID) && $numLinks > 0 && !SP()->user->thisUser->admin) {
		    $errors = __('You are not allowed to put links in signatures', 'sp-buddypress');
            return $errors;
    }
	if (SP()->auths->get('create_links', 'global', SP()->user->thisUser->ID) && $spFilters['sfmaxlinks'] != 0 && $numLinks > $spFilters['sfmaxlinks'] && !SP()->user->thisUser->admin) {
			$errors = __('Maximum number of allowed links exceeded in signature', 'sp-buddypress').': '.$spFilters['sfmaxlinks'].' '.__('allowed', 'sp-buddypress');
			return $errors;
    }
	$sig = SP()->filters->esc_sql(SP()->saveFilters->kses(trim($_POST['signature'])));
	SP()->memberData->update(SP()->user->thisUser->ID, 'signature', $sig);

    # subscription options
    if (SP()->plugin->is_active('subscriptions/sp-subscriptions-plugin.php')) {
        if (isset($_POST['subpost'])) $options['autosubpost'] = true; else $options['autosubpost'] = false;
        if (isset($_POST['substart'])) $options['autosubstart'] = true; else $options['autosubstart'] = false;

        $digest = (isset($_POST['subdigest'])) ? 1 : 0;
        SP()->memberData->update(SP()->user->thisUser->ID, 'subscribe_digest', $digest);
    }

    # mentions
    if (SP()->plugin->is_active('mentions/sp-mentions-plugin.php')) {
	   if (isset($_POST['mentionsoptout'])) $options['mentionsoptout'] = true; else $options['mentionsoptout'] = false;
    }

    # private messaging
    if (SP()->plugin->is_active('private-messaging/sp-pm-plugin.php')) {
        if (isset($_POST['pmemail'])) $options['pmemail'] = true; else $options['pmemail'] = false;
    	if (isset($_POST['pmoptout'])) $options['pmoptout'] = true; else $options['pmoptout'] = false;
    }

    # save the options
	SP()->memberData->update(SP()->user->thisUser->ID, 'user_options', $options);

    # return success (no errors)
    return false;
}

#
# topic subscriptions in profile
#

function sp_buddypress_topic_subs_screen() {
    global $bp;

    if (isset($_POST['submit']) || isset($_POST['submitall'])) {
		# Check the nonce
		check_admin_referer('sp-buddypress-topic-subs');

        # now parse the form
    	$errors = sp_buddypress_topic_subs_submit();

        # allow others to interact
		$errors = apply_filters('sph_buddypress_updated_topic_subs', $errors, bp_displayed_user_id());

		# Check for errors
		if (!empty($errors)) {
			bp_core_add_message($errors, 'error');
		} else {
			bp_core_add_message(__('Topic subscriptions updated.', 'sp-buddypress'));
        }

		# Redirect back to the profile screen to display the updates and message
		bp_core_redirect(trailingslashit(bp_displayed_user_domain().$bp->profile->slug.'/topic-subscriptions'));
	}

	add_action('bp_template_title', 'sp_buddypress_topic_subs_screen_title');
	add_action('bp_template_content', 'sp_buddypress_topic_subs_screen_content');
	bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
}

function sp_buddypress_topic_subs_screen_title() {
	_e('Topic Subscriptions', 'sp-buddypress');
}

function sp_buddypress_topic_subs_screen_content() {
    global $bp;

	if (!bp_is_my_profile() && !bp_current_user_can('bp_moderate')) return false;

    require_once (SP_PLUGIN_DIR.'/forum/content/sp-profile-view-functions.php');

    SP()->user->get_current_user();
	sp_SetupUserProfileData(SP()->user->thisUser->ID);

    $out = '';
    if (SP()->user->profileUser->subscribe) {
?>
        <form action="<?php echo $bp->loggedin_user->domain.'profile/topic-subscriptions'; ?>" name="sp_buddypress_topic_subs_form" id="sp_buddypress_topic_subs_form" method="post">
<?php
        do_action('sph_buddypress_topic_subs_before');
        $found = false;
    	foreach (SP()->user->profileUser->subscribe as $sub) {
        	$topic = SP()->DB->table(SPTOPICS, "topic_id=$sub", 'row');
            if ($topic) {
                $found = true;
                $out.= '<p class="spProfileLabel">';
                $out.= $topic->topic_name.' (<a href="'.SP()->spPermalinks->permalink_from_postid($topic->post_id).'">'.__('view topic', 'sp-buddypress').')</a> ('.$topic->post_count.' '.__('posts', 'sp-buddypress').')';
                $out.= '<span class="spProfileData"><input type="checkbox" name="topic['.$topic->topic_id.']" id="topic-'.$topic->topic_id.'" /></span></p>';
            }
    	}

        if (!$found) {
        	$out.= '</form>';
            $out.= '<p>'.__('You are not currently subscribed to any topics.', 'sp-buddypress').'</p><br />';
            echo $out;
            return;
        }

        do_action('sph_buddypress_topic_subs_after');

        $out.= '<p class="submit">';
    	$out.= '<input type="submit" class="spSubmit" name="submit" value="'.esc_attr(__('Unsubscribe Checked', 'sp-buddypress')).'" />';
    	$out.= '<input type="submit" class="spSubmit" name="submitall" value="'.esc_attr(__('Unsubscribe All', 'sp-buddypress')).'" />';
        $out.= '</p>';

		wp_nonce_field('sp-buddypress-topic-subs');
    	$out.= '</form>';
    } else {
    	$out.= '<p>'.__('You are not currently subscribed to any topics.', 'sp-buddypress').'</p><br />';
    }

    echo $out;
}

function sp_buddypress_topic_subs_submit() {
    global $bp;

	if (!bp_is_my_profile() && !bp_current_user_can('bp_moderate')) bp_core_redirect(trailingslashit(bp_displayed_user_domain().$bp->profile->slug.'/profile/topic-subscriptions/'));

    SP()->user->get_current_user();

    $errors = false;

    require_once SLIBDIR.'sp-subscriptions-database.php';
    if (isset($_POST['submitall'])) {
        sp_subscriptions_remove_user_subscriptions(SP()->user->thisUser->ID);
    } else if (empty($_POST['topic'])) {
        $errors = __('No subscribed topics selected', 'sp-buddypress');
    } else {
        foreach ($_POST['topic'] as $topic_id => $topic) {
            sp_subscriptions_remove_subscription(SP()->filters->integer($topic_id), SP()->user->thisUser->ID, false);
        }
    }

    # return any errors
    return $errors;
}

#
# forum subscriptions in profile
#

function sp_buddypress_forum_subs_screen() {
    global $bp;

	if (isset($_POST['submit']) || isset($_POST['submitall'])) {
		# Check the nonce
		check_admin_referer('sp-buddypress-forum-subs');

        # now parse the form
    	$errors = sp_buddypress_forum_subs_submit();

        # allow others to interact
		$errors = apply_filters('sph_buddypress_updated_forum_subs', $errors, bp_displayed_user_id());

		# Check for errors
		if (!empty($errors)) {
			bp_core_add_message($errors, 'error');
		} else {
			bp_core_add_message(__('Forum subscriptions updated.', 'sp-buddypress'));
        }

		# Redirect back to the profile screen to display the updates and message
		bp_core_redirect(trailingslashit(bp_displayed_user_domain().$bp->profile->slug.'/forum-subscriptions'));
	}

	add_action('bp_template_title', 'sp_buddypress_forum_subs_screen_title');
	add_action('bp_template_content', 'sp_buddypress_forum_subs_screen_content');
	bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
}

function sp_buddypress_forum_subs_screen_title() {
	_e('Forum Subscriptions', 'sp-buddypress');
}

function sp_buddypress_forum_subs_screen_content() {
    global $bp;

	if (!bp_is_my_profile() && !bp_current_user_can('bp_moderate')) return false;

    require_once (SP_PLUGIN_DIR.'/forum/content/sp-profile-view-functions.php');

    SP()->user->get_current_user();
	sp_SetupUserProfileData(SP()->user->thisUser->ID);
?>
    <form action="<?php echo $bp->loggedin_user->domain.'profile/forum-subscriptions'; ?>" name="sp_buddypress_forum_subs_form" id="sp_buddypress_forum_subs_form" method="post">
<?php
    do_action('sph_buddypress_forum_subs_before');

    $out = '';

    require_once SP_PLUGIN_DIR.'/admin/library/spa-support.php';
    $forums = spa_get_forums_all();
    if ($forums) {
    	$thisgroup = 0;
    	foreach ($forums as $forum) {
            if (SP()->auths->get('subscribe', $forum->forum_id) && !$forum->forum_disabled) {
    			if ($thisgroup != $forum->group_id) {
    				$out.= '<p class="spProfileHeader">'.__('Group', 'sp-subs').': '.SP()->displayFilters->title($forum->group_name).'</p>';
    				$thisgroup = $forum->group_id;
    			}
                $checked = (!empty(SP()->user->profileUser->forum_subscribe) && in_array($forum->forum_id, SP()->user->profileUser->forum_subscribe)) ? 'checked="checked" ' : '';
                $out.= '<p class="spProfileLabel">'.$forum->forum_name.'<span class="spProfileData"><input type="checkbox" '.$checked.'name="forum['.$forum->forum_id.']" id="forum-'.$forum->forum_id.'" /></span></p>';
            }
    	}
    }

    do_action('sph_buddypress_forum_subs_after');

    $out.= '<p class="submit">';
	$out.= '<input type="submit" class="spSubmit" name="submit" value="'.esc_attr(__('Update Forum Subscriptions', 'sp-buddypress')).'" />';
    $out.= '</p>';

	wp_nonce_field('sp-buddypress-forum-subs');
	$out.= '</form>';

    echo $out;
}

function sp_buddypress_forum_subs_submit() {
    global $bp;

	if (!bp_is_my_profile() && !bp_current_user_can('bp_moderate')) bp_core_redirect(trailingslashit(bp_displayed_user_domain().$bp->profile->slug.'/profile/forum-subscriptions/'));

    SP()->user->get_current_user();

    $errors = false;

    require_once SLIBDIR.'sp-subscriptions-database.php';
    if (SP()->user->thisUser->forum_subscribe) {
        foreach (SP()->user->thisUser->forum_subscribe as $sub) {
			if (empty($_POST['forum']) || !array_key_exists($sub, $_POST['forum'])) sp_subscriptions_remove_forum_subscription($sub, SP()->user->thisUser->ID, false);
        }
    }

    # go through new list and add any new subs
    if (isset($_POST['forum'])) {
        foreach ($_POST['forum'] as $sub => $on) {
            if ((empty(SP()->user->thisUser->forum_subscribe) || !in_array($sub, SP()->user->thisUser->forum_subscribe)) && SP()->auths->get('subscribe', $sub, SP()->user->thisUser->ID)) sp_subscriptions_save_forum_subscription($sub, SP()->user->thisUser->ID, false);
        }
    }

    # return success (no errors)
    return false;
}

#
# watches in profile
#

function sp_buddypress_watches_screen() {
    global $bp;

	if (isset($_POST['submit']) || isset($_POST['submitall'])) {
		# Check the nonce
		check_admin_referer('sp-buddypress-watches');

        # now parse the form
    	$errors = sp_buddypress_watches_submit();

        # allow others to interact
		$errors = apply_filters('sph_buddypress_updated_watches', $errors, bp_displayed_user_id());

		# Check for errors
		if (!empty($errors)) {
			bp_core_add_message($errors, 'error');
		} else {
			bp_core_add_message(__('Watches updated.', 'sp-buddypress'));
        }

		# Redirect back to the profile screen to display the updates and message
		bp_core_redirect(trailingslashit(bp_displayed_user_domain().$bp->profile->slug.'/watches'));
	}

	add_action('bp_template_title', 'sp_buddypress_watches_screen_title');
	add_action('bp_template_content', 'sp_buddypress_watches_screen_content');
	bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
}

function sp_buddypress_watches_screen_title() {
	_e('Watches', 'sp-buddypress');
}

function sp_buddypress_watches_screen_content() {
    global $bp;

	if (!bp_is_my_profile() && !bp_current_user_can('bp_moderate')) return false;

    require_once (SP_PLUGIN_DIR.'/forum/content/sp-profile-view-functions.php');

    SP()->user->get_current_user();
	sp_SetupUserProfileData(SP()->user->thisUser->ID);

    $out = '';
    if (SP()->user->profileUser->watches) {
?>
        <form action="<?php echo $bp->loggedin_user->domain.'profile/watches'; ?>" name="sp_buddypress_watches_form" id="sp_buddypress_watches_form" method="post">
<?php
        do_action('sph_buddypress_watches_before');

        $found = false;
    	foreach (SP()->user->profileUser->watches as $watch) {
        	$topic = SP()->DB->table(SPTOPICS, "topic_id=$watch", 'row');
            if ($topic) {
                $found = true;
                $out.= '<p class="spProfileLabel">';
                $out.= $topic->topic_name.' (<a href="'.SP()->spPermalinks->permalink_from_postid($topic->post_id).'">'.__('view topic', 'sp-buddypress').')</a> ('.$topic->post_count.' '.__('posts', 'sp-buddypress').')';
                $out.= '<span class="spProfileData"><input type="checkbox" name="topic['.$topic->topic_id.']" id="topic-'.$topic->topic_id.'" /></span></p>';
            }
    	}

        if (!$found) {
        	$out.= '</form>';
            $out.= '<p>'.__('You are not currently watching any topics.', 'sp-buddypress').'</p><br />';
            echo $out;
            return;
        }

        do_action('sph_buddypress_watches_after');

        $out.= '<p class="submit">';
    	$out.= '<input type="submit" class="spSubmit" name="submit" value="'.esc_attr(__('Stop Watching Checked', 'sp-buddypress')).'" />';
    	$out.= '<input type="submit" class="spSubmit" name="submitall" value="'.esc_attr(__('Stop Wathcing All', 'sp-buddypress')).'" />';
        $out.= '</p>';

		wp_nonce_field('sp-buddypress-watches');
    	$out.= '</form>';
    } else {
    	$out.= '<p>'.__('You are not currently watching any topics.', 'sp-buddypress').'</p><br />';
    }

    echo $out;
}

function sp_buddypress_watches_submit() {
    global $bp;

	if (!bp_is_my_profile() && !bp_current_user_can('bp_moderate')) bp_core_redirect(trailingslashit(bp_displayed_user_domain().$bp->profile->slug.'/profile/watches'));

    SP()->user->get_current_user();

    $errors = false;

    require_once WLIBDIR.'sp-watches-database.php';
    if (isset($_POST['submitall'])) {
        sp_watches_remove_user_watches(SP()->user->thisUser->ID);
    } else if (empty($_POST['topic'])) {
        $errors = __('No watched topics selected', 'sp-buddypress');
    } else {
        foreach ($_POST['topic'] as $topic_id => $topic) {
            sp_watches_remove_watch($topic_id, SP()->user->thisUser->ID, false);
        }
    }

    # return errors
    return $errors;
}
