<?php
/*
Simple:Press
Who's Online Plugin Support Routines
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_online_do_header() {
	$css = SP()->theme->find_css(SPWOCSS, 'sp-online.css', 'sp-online.spcss');
    SP()->plugin->enqueue_style('sp-online', $css);
}

function spOnlineTemplateName($name, $pageview) {
	if ($pageview != 'online') return $name;
	# locate template - check if in theme if not use plugin
	$tempName = SP()->theme->find_template(SPWOTEMPDIR,'spOnlineView.php');
	return $tempName;
}

function spOnlinePageTitle($title, $sep) {
	$sfseo = SP()->options->get('sfseo');
	if ($sfseo['sfseo_page'] && SP()->rewrites->pageData['pageview'] == 'online') $title = __("Who's Online", 'spwo').$sep.$title;
    return $title;
}

function spOnlineCanonicalUrl($url) {
    if (SP()->rewrites->pageData['pageview'] == 'online') $url = SP()->spPermalinks->get_url("online");
    return $url;
}

function spOnlineGetOnline() {
	return SP()->DB->select("SELECT trackuserid, pageview, display_name, user_options, ".SPFORUMS.".forum_id, forum_slug, forum_name, ".SPTOPICS.".topic_id, topic_slug, topic_name FROM ".SPTRACK."
			LEFT JOIN ".SPMEMBERS." ON ".SPTRACK.".trackuserid = ".SPMEMBERS.".user_id
			LEFT JOIN ".SPFORUMS." ON ".SPTRACK.".forum_id = ".SPFORUMS.".forum_id
			LEFT JOIN ".SPTOPICS." ON ".SPTRACK.".topic_id = ".SPTOPICS.".topic_id
			ORDER BY pageview, ".SPTRACK.".forum_id, ".SPTRACK.".topic_id, trackuserid");
}
