<?php
/*
Simple:Press
Auto Link Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_autolink_do_admin_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$autolink = SP()->options->get('autolink');

    # save keywords
    $keywords = trim($_POST['keywords']);
    $keywords = SP()->saveFilters->nohtml($keywords);
    $keywords = SP()->saveFilters->escape($keywords);
	$autolink['keywords'] = $keywords;

    # save urls but have to loop since the filter removes newline chars
    if (!empty($_POST['urls'])) {
        $urls = explode("\n", trim($_POST['urls']));
        foreach ($urls as $index => $url) {
        	$url = SP()->saveFilters->nohtml($url);
        	$url = SP()->saveFilters->escape($url);
            $urls[$index] = $url;
        }
        $urls = implode("\n", $urls);
    } else {
        $urls = '';
    }
	$autolink['urls'] = $urls;

    $autolink['noboundary'] = isset($_POST['noboundary']);

	SP()->options->update('autolink', $autolink);

	return __('Auto linking options updated', 'sp-autolink');
}
