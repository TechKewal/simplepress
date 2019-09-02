<?php
/*
Simple:Press
Template Tag(s) - Print Topic Specific
$LastChangedDate: 2013-04-19 06:21:03 +0100 (Fri, 19 Apr 2013) $
$Rev: 10187 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# --------------------------------------------------------------------------------------
#
#	sp_PrintTopicView()
#	Display View Topic Print button on topic views
#	Scope:	Topic Template
#	Version: 1.0.0
#
# --------------------------------------------------------------------------------------
function sp_do_PrintTopicView($args, $label, $toolTip) {
	$defs = array('tagId' 		=> 'spPrintTopicView',
				  'tagClass' 	=> 'spButton',
				  'icon' 		=> 'sp_Print.png',
				  'iconClass'	=> 'spIcon',
				  'echo'		=> 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PrintTopicView_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$icon		= sanitize_file_name($icon);
	$iconClass 	= esc_attr($iconClass);
	$toolTip	= esc_attr($toolTip);
	$echo		= (int) $echo;

	$site = wp_nonce_url(SPAJAXURL.'print&amp;url='.SP()->forum->view->thisTopic->topic_permalink.'&amp;id='.SP()->forum->view->thisTopic->topic_id.'&amp;page='.SP()->forum->view->thisTopic->display_page.'&amp;totalpages='.SP()->forum->view->thisTopic->total_pages, 'print');
	$title = esc_attr(__('Print Options', 'sp-print'));
	$out = "<a class='$tagClass spOpenDialog' id='$tagId' title='$toolTip' data-site='$site' data-label='$title' data-width='400' data-height='0' data-align='center'>";

	if (!empty($icon)) $out.= SP()->theme->paint_icon($iconClass, SPTHEMEICONSURL, $icon);
	if (!empty($label)) $out.= SP()->displayFilters->title($label);
	$out.= "</a>\n";
	$out = apply_filters('sph_PrintTopicView', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

# --------------------------------------------------------------------------------------
#
#	sp_PrintTopic()
#	Display Topic Print button on print topic template
#	Scope:	Print Topic Template
#	Version: 1.0.0
#
# --------------------------------------------------------------------------------------
function sp_do_PrintTopic($args, $label, $toolTip) {
	$defs = array('tagId' 		=> 'spPrintTopic',
				  'tagClass' 	=> 'spButton',
				  'icon' 		=> 'sp_Print.png',
				  'iconClass'	=> 'spIcon',
				  'echo'		=> 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PrintTopic_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$icon		= sanitize_file_name($icon);
	$iconClass 	= esc_attr($iconClass);
	$toolTip	= esc_attr($toolTip);
	$echo		= (int) $echo;

	$out = "<a class='$tagClass spPrintTopicPrint' id='$tagId' title='$toolTip' rel='nofollow'>";

	if (!empty($icon)) $out.= SP()->theme->paint_icon($iconClass, SPTHEMEICONSURL, $icon);
	if (!empty($label)) $out.= SP()->displayFilters->title($label);
	$out.= "</a>\n";
	$out = apply_filters('sph_PrintTopic', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

# --------------------------------------------------------------------------------------
#
#	sp_GoBack()
#	Display 'Go Back' button on print topic template
#	Scope:	Print Topic Template
#	Version: 1.0.0
#
# --------------------------------------------------------------------------------------
function sp_do_GoBack($args, $label, $toolTip) {
	$defs = array('tagId' 		=> 'spGoBack',
				  'tagClass' 	=> 'spButton',
				  'icon'		=> 'sp_ArrowLeft.png',
				  'iconClass'	=> 'spIcon',
				  'echo'		=> 1
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_GoBack_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$icon		= sanitize_file_name($icon);
	$iconClass 	= esc_attr($iconClass);
	$toolTip	= esc_attr($toolTip);
	$echo		= (int) $echo;

	$out = "<a class='$tagClass spPrintTopicGoBack' id='$tagId' title='$toolTip' rel='nofollow' >";

	if (!empty($icon)) $out.= SP()->theme->paint_icon($iconClass, SPTHEMEICONSURL, $icon);
	if (!empty($label)) $out.= SP()->displayFilters->title($label);
	$out.= "</a>\n";
	$out = apply_filters('sph_GoBack', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

# --------------------------------------------------------------------------------------
#
#	sp_print_topic_single_post_tag()
#	Replace the event handler in the standard print post tag
#	Scope:	Display Topic
#	Version: 1.0.0
#
# --------------------------------------------------------------------------------------
function sp_do_print_topic_single_post_tag($out, $a) {
	$start	= strpos($out, 'data-postid');
	$end 	= strpos($out, '>', $start);

	$site = wp_nonce_url(SPAJAXURL.'print&url='.SP()->forum->view->thisTopic->topic_permalink.'&id='.SP()->forum->view->thisTopic->topic_id.'&page='.SP()->forum->view->thisTopic->display_page.'&totalpages='.SP()->forum->view->thisTopic->total_pages.'&index='.SP()->forum->view->thisPost->post_index, 'print');
	$title = esc_attr(__('Print Options', 'sp-print'));
	$replace = "data-site='$site' data-label='$title' data-width='400' data-height='0' data-align='center'";

	$out = substr_replace($out, $replace, $start, ($end - $start));
	$out = str_replace(' spPrintThisPost', ' spOpenDialog', $out);

	return $out;
}
