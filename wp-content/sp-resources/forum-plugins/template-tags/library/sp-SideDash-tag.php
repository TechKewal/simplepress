<?php
/*
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/* 	=====================================================================================
	Allows display of a common SPF dashboard on pages

	parameters:
		name			description								type			default
		----------------------------------------------------------------------------------------
		tagId			unique id to use for div or list		text			spSideDashTag
		tagClass		class to be applied for styling			text			spSideDashTag
		tagClass		class to be applied for styling			text			spSideDashTag
		showAvatar		show logged in users avatar	  		    int	     		1 (true)
		avatarSize		size of avatar if displaying			int 			25
		avatarClass		class to be applied for styling			text			spAvatar
		showAdminLink	show link to wp admin dashboard			int		 	    1 (true)
		showLogin	    show login form (1),
                        wp login link (2) or custom
                        login link (3)			                int		     	1 (login form)
		loginLink		custom login link for showLogin=3		text
		showLogout		show ling to logout           			int	    		1
		echo		    echo or return the markup   			int   			1
 	===================================================================================*/
function sp_do_sp_SideDashTag($args='') {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	$defs = array('tagId'    	    => 'spSideDashTag',
				  'tagClass' 	    => 'spSideDashTag',
				  'showAvatar'	    => 1,
				  'avatarSize'	    => 25,
				  'avatarClass'	    => 'spAvatar',
				  'showAdminLink'	=> 1,
				  'showLogin'	    => 1,
				  'loginLink'	    => esc_url(wp_login_url()),
				  'showLogout'	    => 1,
				  'echo'		    => 1
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_SideDashTag_args', $a);
	extract($a, EXTR_SKIP);

	$tagId			  = esc_attr($tagId);
	$tagClass		    = esc_attr($tagClass);
	$showAvatar		  = (int) $showAvatar;
	$avatarSize		  = (int) $avatarSize;
	$avatarClass	  = esc_attr($avatarClass);
	$showAdminLink	  = (int) $showAdminLink;
	$showLogin  	  = (int) $showLogin;
	$loginLink  	  = SP()->displayFilters->title($loginLink);
	$showLogout    	  = (int) $showLogout;
	$echo		      = (int) $echo;

	sp_check_api_support();

    $out = '';

    $sflogin = SP()->options->get('sflogin');
	if (SP()->user->thisUser->guest) {
		if (!empty($showLogin)) { # show any login links?
    		if ($showLogin == 1) { # showing login form
    			# display login form
    			$out.= '<form action="'.esc_url(wp_login_url()).'" method="post">';
    			$out.= '<p class="spSideDashUser"><label for="spTagLog">'.__('Username: ', 'sp-ttags').'<input type="text" name="log" id="spTagLog" value="" size="15" /></label></p>';
    			$out.= '<p class="spSideDashPw"><label for="spTagPwd">'.__('Password: ', 'sp-ttags').'<input type="password" name="pwd" id="spTagPwd" value="" size="15"  /></label></p>';
    			$out.= '<p class="spSideDashRemember"><input type="checkbox" id="rememberme" name="rememberme" value="forever" /><label for="rememberme">'.__('Remember me', 'sp-ttags').'</label></p>';
    			$out.= '<input type="hidden" name="redirect_to" value="'.esc_attr($sflogin['sfloginurl']).'" />';
    			$out.= '<p><input type="submit" name="submit" id="submit" value="'.esc_attr(__('Log in', 'sp-ttags')).'" /></p>';
    			$out.= '</form>';
    		} else if ($showLogin == 2) { # showing wp login link
    			$out.= '<a href="'.esc_url(wp_login_url($sflogin['sfloginurl'], 'login')).'">'.__('Log In', 'sp-ttags').'</a></p>';
    		} else if ($showLogin == 3) { # showing custom login link
    			$out.= '<a href="'.esc_attr($loginLink).'">'.__('Log In', 'sp-ttags').'</a></p>';
            }

		    # if registrations allowed, display register link
            $started = false;
			if (get_option('users_can_register') && !SP()->core->forumData['lockdown']) {
                $started = true;
   				$out.= '<p class="spSideDashLinks"><a href="'.esc_url(site_url('wp-login.php?action=register&amp;redirect_to='.$sflogin['sfregisterurl'], 'login')).'">'.__('Register', 'sp-ttags').'</a>';
            }
            if ($started) {
                $out.= ' | ';
            } else {
                $out.= '<p class="spSideDashGuest">';
            }

            # display lost password link
			$out.= '<a href="'.esc_url(wp_lostpassword_url()).'">'.__('Lost password', 'sp-ttags').'</a></p>';
		}
    	$out = apply_filters('sph_SideDashTagUser', $out);
	} else {
		if ($showAvatar) $out.= sp_UserAvatar("tagClass=$avatarClass&size=$avatarSize&echo=0");
		$out.= '<p class="spSideDashLoggedIn">'.__('Logged in as', 'sp-ttags').' <strong>'.SP()->displayFilters->name(SP()->user->thisUser->display_name).'</strong></p>';
		if ($showAdminLink) $out.= '<p class="spSideDashAdminLink"><a href="'.SPHOMEURL.'wp-admin'.'">'.__('Dashboard', 'sp-ttags').'</a></p>';
		if ($showLogout) $out.= '<p class="spSideDashLogout"><a href="'.esc_url(wp_logout_url($sflogin['sflogouturl'])).'">'.__('Log out', 'sp-ttags').'</a></p>';
    	$out = apply_filters('sph_SideDashTagGuest', $out);
	}

	$out = apply_filters('sph_SideDashTag', $out);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_do_SideDashShortcode($atts) {
    $args = array();
    if (isset($atts['tagid']))          $args['tagId']          = $atts['tagid'];
    if (isset($atts['tagclass']))       $args['tagClass']       = $atts['tagclass'];
    if (isset($atts['showavatar']))     $args['showAvatar']     = $atts['showavatar'];
    if (isset($atts['avatarsize']))     $args['avatarSize']     = $atts['avatarsize'];
    if (isset($atts['avatarclass']))    $args['avatarClass']    = $atts['avatarclass'];
    if (isset($atts['showadminlink']))  $args['showAdminLink']  = $atts['showadminlink'];
    if (isset($atts['showlogin']))      $args['showLogin']      = $atts['showlogin'];
    if (isset($atts['loginlink']))      $args['loginLink']      = $atts['loginlink'];
    if (isset($atts['showlogout']))     $args['showLogout']     = $atts['showlogout'];

    $args['echo'] = 0;
    return sp_do_sp_SideDashTag($args);
}
