<?php
/*
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# tag can be used anywhere
# for current logged in user (or guest), leave $userid blank
# for specific user, pass the $userid
function sp_CubePointsTag($args, $userid, $toolTip) {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	if (!function_exists('cp_getPoints')) return;

	sp_forum_ajax_support();

    if (empty($userid)) {
        $thisUser = SP()->user->thisUser;
    } else {
    	$thisUser = SP()->user->get($userid);
    }
	if ($thisUser->guest) return;

	$defs = array('tagId'    	=> 'spCubePoints',
				  'tagClass' 	=> 'spCubePoints',
				  'icon'		=> 'sp_CubePoints.png',
				  'echo'		=> 1,
				  'get'			=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_CubePoints_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$toolTip	= SP()->displayFilters->title($toolTip);
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$echo		= (int) $echo;
	$get		= (int) $get;
	$icon 		= SP()->theme->paint_icon('', SPCUBEICON, sanitize_file_name($icon), $toolTip);

	if ($get) return cp_getPoints($thisUser->ID);

	$out = "<div id='$tagId' class='$tagClass'>";
	if (!empty($icon)) $out.= $icon;
	$out.= get_option('cp_prefix').cp_getPoints($thisUser->ID).get_option('cp_suffix');
	$out.= '</div>';
	$out = apply_filters('sph_CubePoints', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}
