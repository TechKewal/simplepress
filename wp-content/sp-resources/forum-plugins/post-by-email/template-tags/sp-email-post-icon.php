<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_PostIndexPostByEmailTag($args, $toolTip) {
	if (SP()->forum->view->thisPost->source != 1) return;

	$defs = array('tagId'    	=> 'spPostIndexPostByEmail%ID%',
				  'tagClass' 	=> 'spStatusIcon',
				  'iconSource'	=> 'sp_PostByEmail.png',
				  'echo'		=> 1,
				  'get'			=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PostIndexPostByEmail_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$toolTip	= SP()->displayFilters->title($toolTip);
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$echo		= (int) $echo;
	$get		= (int) $get;
	$icon 		= SP()->theme->paint_icon('', SPEPICON, sanitize_file_name($iconSource), $toolTip);

	$tagId = str_ireplace('%ID%', SP()->forum->view->thisPost->post_id, $tagId);

	if ($get) return SP()->forum->view->thisPost->source;

	$out = "<span id='$tagId' class='$tagClass'>";
	$out.= $icon;
	$out.= "</span>";
	$out = apply_filters('sph_PostIndexPostByEmail', $out, $a);

	if($echo) {
		echo $out;
	} else {
		return $out;
	}
}
