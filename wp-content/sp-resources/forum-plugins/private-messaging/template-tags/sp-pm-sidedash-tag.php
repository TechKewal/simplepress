<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/* 	=====================================================================================
	sp_pm_do_sidedash()

	Allows display of a common SPF dashboard on pages with a send pm tag

	parameters:

		show_avatar		display user avatar						true/false								true
		show_pm			display pm template tag					true/false								true
		redirect		controls login/logout redirection		1=home, 2=admin, 3=cur page, 4=forum 	4
		show_admin_link	display link to admin dashboard			true/false								true
		show_login_link	display login form and lost pw link		true/false								true
 	===================================================================================*/
function sp_pm_do_sidedash($show_avatar=true, $show_pm=true, $redirect=4, $show_admin_link=true, $show_login_link=true) {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

    sp_forum_ajax_support();

	$sflogin = SP()->options->get('sflogin');
	if ($redirect == 1) {
		$redirect_to = user_trailingslashit(SPHOMEURL);
	} elseif ($redirect == 2) {
		$redirect_to = SPHOMEURL.'wp-admin';
	} elseif ($redirect == 3) {
		$redirect_to = htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8');
	} else {
		$redirect_to = SP()->spPermalinks->get_url();
	}

	if (SP()->user->thisUser->guest) {
	    # are we showing login form and lost password
		if ($show_login_link) {
			# display login form
			echo '<form action="'.esc_url(wp_login_url()).'" method="post">';
			echo '<div class="spPmUsername"><label for="sftaglog">'.__('Username: ', 'sp-pm').'<input type="text" name="log" id="sftaglog" value="" size="15" /></label></div>';
			echo '<div class="spPmPassword"><label for="sftagpwd">'.__('Password: ', 'sp-pm').'<input type="password" name="pwd" id="sftagpwd" value="" size="15"  /></label></div>';
			echo '<div class="spPmRemember"><input type="checkbox" id="rememberme" name="rememberme" value="forever" /><label for="rememberme">'.__('Remember me', 'sp-pm').'</label></div>';
			echo '<input type="submit" name="submit" id="submit" value="'.esc_attr(__('Login', 'sp-pm')).'" />';
			echo '<input type="hidden" name="redirect_to" value="'.esc_attr($redirect_to).'" />';
			echo '</form>';
			echo '<p class="spPmGuest"><a href="'.$sflogin['sflostpassurl'].'">'.__('Lost password', 'sp-pm').'</a>';

		    # if registrations allowed, display register link
			if (get_option('users_can_register') && !SP()->core->forumData['lockdown']) {
    			$sfpolicy = SP()->options->get('sfpolicy');
    			if ($sfpolicy['sfregtext']) {
    				echo '<br /><a href="'.SP()->spPermalinks->get_url('policy').'">'.__('Register', 'sp-pm').'</a></p>';
    			} else {
    				echo '<br /><a href="'.esc_url(wp_lostpassword_url()).'">'.__('Register', 'sp-pm').'</a></p>';
    			}
            }
		}
	} else {
		echo sp_UserAvatar();
		echo '<p class="spPmLoggedIn">'.__('Logged in as', 'sp-pm').' <strong>'.SP()->displayFilters->name(SP()->user->thisUser->display_name).'</strong></p>';
		if ($show_pm) sp_pm_inbox(true, false);
		if ($show_admin_link) echo '<p class="spPmAdmin"><a href="'.SPHOMEURL.'wp-admin'.'">'.__('Dashboard', 'sp-pm').'</a></p>';
		echo '<p class="spPmLogout"><a href="'.esc_url(wp_logout_url($redirect_to)).'">'.__('Log out', 'sp-pm').'</a></p>';
	}
}
