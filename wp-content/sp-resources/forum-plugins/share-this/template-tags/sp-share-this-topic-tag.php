<?php
/*
Simple:Press
Share This Plugin Topic Template Tag
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this tag is intended to be used on a topic view page
# if you want to use within the topic loop on per post basis, please see the sp_ShareThisTopicIndexTag()) template tag
function sp_do_ShareThisTopicTag($args='') {
	$defs = array('tagId'    	=> 'ShareThisTopic',
				  'tagClass' 	=> 'ShareThisTopic',
				  'echo'		=> 1
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_ShareThisTopicTag_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$echo			= (int) $echo;

    $out = '';
    $out.= "<div id='$tagId' class='$tagClass'>";

    $url = "st_url='".urlencode(SP()->spPermalinks->build_url(SP()->rewrites->pageData['forumslug'], SP()->rewrites->pageData['topicslug'], SP()->rewrites->pageData['page']))."'";
	$title = (empty(SP()->rewrites->pageData['topicname'])) ? '' : SP()->rewrites->pageData['topicname'];
    $title = str_replace('%', '%25', $title);
    $title = "st_title='".esc_attr($title)."'";

    $summary = (!empty(SP()->rewrites->pageData['topicdesc'])) ? "st_summary='".esc_attr(SP()->rewrites->pageData['topicdesc'])."'" : '';

    $out.= sp_share_this_do_sharing($url, $title, $summary);

    $out.= '</div>';

	$out = apply_filters('sph_ShareThisTopicTag', $out);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}
