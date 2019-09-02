<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# tag needs to be used in the forum view topic loop and is valid for each post/user combination
function sp_PostIndexCubePointsTag($args, $toolTip) {
	if (!function_exists('cp_getPoints')) return;

	if (SP()->forum->view->thisPostUser->guest) return;

	$defs = array('tagId'    	=> 'spPostIndexCubePoints%ID%',
				  'tagClass' 	=> 'spPostUserCubePoints',
				  'icon'		=> 'sp_CubePoints.png',
				  'echo'		=> 1,
				  'get'			=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PostIndexCubePoints_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$toolTip	= SP()->displayFilters->title($toolTip);
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$echo		= (int) $echo;
	$get		= (int) $get;
	$icon 		= SP()->theme->paint_icon('', SPCUBEICON, sanitize_file_name($icon), $toolTip);

	$tagId = str_ireplace('%ID%', SP()->forum->view->thisPost->post_id, $tagId);

	if ($get) return cp_getPoints(SP()->forum->view->thisPostUser->ID);

	$out = "<div id='$tagId' class='$tagClass'>";
	if (!empty($icon)) $out.= $icon;
	$out.= get_option('cp_prefix').cp_getPoints(SP()->forum->view->thisPostUser->ID).get_option('cp_suffix');
	$out.= '</div>';
	$out = apply_filters('sph_PostIndexCubePoints', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}
