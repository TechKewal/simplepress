<?php
/*
Simple:Press
Report Post plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the report post plugin uninstall only
function sp_report_post_do_uninstall() {
    # delete our option table
    SP()->options->delete('report-post');

	# delete our auth
	SP()->auths->delete('report_posts');

    # make sure permalink includes our stuff
    SP()->spPermalinks->update_permalink(true);

	# remove glossary entries
	sp_remove_glossary_plugin('sp-reportpost');
}
