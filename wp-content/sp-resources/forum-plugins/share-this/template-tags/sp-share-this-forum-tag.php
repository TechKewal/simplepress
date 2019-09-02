<?php
/*
Simple:Press
Share This Plugin Forum Template Tag
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this tag is intended to be used on a forum view page
function sp_do_ShareThisForumTag($args='') {
	$defs = array('tagId'    	=> 'ShareThisForum',
				  'tagClass' 	=> 'ShareThisForum',
				  'echo'		=> 1
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_ShareThisForumTag_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$echo			= (int) $echo;

    $out = '';
    $out.= "<div id='$tagId' class='$tagClass'>";

    $page = (isset(SP()->rewrites->pageData['page'])) ? SP()->rewrites->pageData['page'] : 0;
    $url = "st_url='".urlencode(SP()->spPermalinks->build_url(SP()->rewrites->pageData['forumslug'], '', $page))."'";
    $title = (empty(SP()->rewrites->pageData['forumname'])) ? '' : SP()->rewrites->pageData['forumname'];
    $title = str_replace('%', '%25', $title);
    $title = "st_title='".esc_attr($title)."'";
    $summary = (empty(SP()->rewrites->pageData['forumdesc'])) ? '' : "st_summary='".esc_attr(SP()->rewrites->pageData['forumdesc'])."'";

    $out.= sp_share_this_do_sharing($url, $title, $summary);

    $out.= '</div>';

	$out = apply_filters('sph_ShareThisForumTag', $out);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}
