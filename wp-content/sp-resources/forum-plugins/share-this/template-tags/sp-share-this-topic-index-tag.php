<?php
/*
Simple:Press
Share This Plugin Topic Index Template Tag
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this tag is intended to be used on a topic view page within the topic loop where SP()->forum->view->thisTopic and SP()->forum->view->thisPost are set up
function sp_do_ShareThisTopicIndexTag($args='') {
	$defs = array('tagId'    	=> 'ShareThisTopicIndex%ID%',
				  'tagClass' 	=> 'ShareThisTopicIndex',
				  'echo'		=> 1
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_ShareThisTopicIndexTag_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$echo			= (int) $echo;

	$tagId = str_ireplace('%ID%', SP()->forum->view->thisPost->post_id, $tagId);

    $out = '';
    $out.= "<div id='$tagId' class='$tagClass'>";

    $url = "st_url='".urlencode(SP()->forum->view->thisPost->post_permalink)."'";
    $title = str_replace('%', '%25', SP()->forum->view->thisTopic->topic_name);
    $title = "st_title='".esc_attr($title)."'";
    $summary = "st_summary='".esc_attr(SP()->displayFilters->tooltip(SP()->forum->view->thisPost->post_content, 0))."'";

    $out.= sp_share_this_do_sharing($url, $title, $summary);

    $out.= '</div>';

	$out = apply_filters('sph_ShareThisTopicIndexTag', $out);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}
