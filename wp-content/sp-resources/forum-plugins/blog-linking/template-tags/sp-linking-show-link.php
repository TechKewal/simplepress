<?php
/*
Simple:Press
Blog Linking - Forum side support routines
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# --------------------------------------------------------------------------------------
#
#	sp_TopicHeaderShowBlogLink()
#	Display Topic View
#	Scope:	Topic Header Level
#
#	class:		CSS styling class
#	icon:		name of optional icon to use as button
#	iconClass:
#
#	label:		Text to display on button
#	toolTip:	For the tooltip title attribute
#
# --------------------------------------------------------------------------------------
function sp_do_TopicHeaderShowBlogLink($args='', $label='', $toolTip='') {
	if(!SP()->forum->view->thisTopic->blog_post_id) return;

	$defs = array('tagId' 		=> 'spTopicHeaderShowBlogLink',
				  'tagClass' 	=> 'spButton',
				  'icon' 		=> 'sp_BlogLink.png',
				  'iconClass'	=> 'spIcon',
				  'echo'		=> 1,
				  'get'			=> 0
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_TopicHeaderShowBlogLink_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$iconClass 	= esc_attr($iconClass);
	$icon		= SP()->theme->paint_icon($iconClass, SPBLIMAGES, sanitize_file_name($icon));
	$toolTip	= SP()->displayFilters->title($toolTip);
	$label		= esc_attr($label);
	$echo		= (int) $echo;
	$get		= (int) $get;

	if ($get) return $label;

	$out = "<a class='$tagClass' id='$tagId' title='$toolTip' rel='nofollow' href='".get_permalink(SP()->forum->view->thisTopic->blog_post_id)."'>";
	if (!empty($icon)) $out.= $icon;
	if (!empty($label)) $out.= $label;
	$out.= "</a>\n";
	$out = apply_filters('sph_TopicHeaderShowBlogLink', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}
