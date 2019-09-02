<?php
/*
$LastChangedDate: 2014-01-23 00:39:27 +0000 (Thu, 23 Jan 2014) $
$Rev: 10990 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# tag needs to be used in the forum view topic loop and is valid for each post/user combination
function sp_PostIndexMyCredTag($args, $label, $toolTip) {
	if (!function_exists('MyCred_get_users_fcred')) return;

	if (SP()->forum->view->thisPostUser->guest) return;

	$defs = array('tagId'    	=> 'spPostIndexMyCred%ID%',
				  'tagClass' 	=> 'spPostUserMyCred',
				  'icon'		=> 'sp_MyCred.png',
				  'echo'		=> 1,
				  'get'			=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PostIndexMyCred_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$toolTip	= SP()->displayFilters->title($toolTip);
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$echo		= (int) $echo;
	$get		= (int) $get;
	$icon 		= SP()->theme->paint_icon('', SPCREDICON, sanitize_file_name($icon), $toolTip);

	$tagId = str_ireplace('%ID%', SP()->forum->view->thisPost->post_id, $tagId);

	if ($get) return MyCred_get_users_fcred(SP()->forum->view->thisPostUser->ID);

	if(!empty($label)) $label = SP()->displayFilters->title($label);

	$out = "<div id='$tagId' class='$tagClass'>";
	$out.= $label;
	if (!empty($icon)) $out.= $icon;
	$out.= MyCred_get_users_fcred(SP()->forum->view->thisPostUser->ID);
	$out.= '</div>';
	$out = apply_filters('sph_PostIndexMyCred', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}
