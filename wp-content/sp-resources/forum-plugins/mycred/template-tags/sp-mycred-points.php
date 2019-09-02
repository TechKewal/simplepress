<?php
/*
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# tag can be used anywhere
# for current logged in user (or guest), leave $userid blank
# for specific user, pass the $userid
function sp_MyCredTag($args, $userid, $toolTip) {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	if (!function_exists('MyCred_get_users_fcred')) return;

	sp_forum_ajax_support();

    if (empty($userid)) {
        $thisUser = SP()->user->thisUser;
    } else {
    	$thisUser = SP()->user->get($userid);
    }
	if ($thisUser->guest) return;

	$defs = array('tagId'    	=> 'spMyCred',
				  'tagClass' 	=> 'spMyCred',
				  'icon'		=> 'sp_MyCred.png',
				  'echo'		=> 1,
				  'get'			=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_MyCredTag_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$toolTip	= SP()->displayFilters->title($toolTip);
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$echo		= (int) $echo;
	$get		= (int) $get;
	$icon 		= SP()->theme->paint_icon('', SPCREDICON, sanitize_file_name($icon), $toolTip);

	if ($get) return MyCred_get_users_fcred($thisUser->ID);

	$out = "<div id='$tagId' class='$tagClass'>";
	if (!empty($icon)) $out.= $icon;
	$out.= MyCred_get_users_fcred($thisUser->ID);
	$out.= '</div>';
	$out = apply_filters('sph_MyCred', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}
