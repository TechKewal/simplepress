<?php
/*
Simple:Press
Blog Linking plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_linking_do_uninstall() {
	# Remove all data
	SP()->DB->execute('DROP TABLE IF EXISTS '.SFLINKS);
	SP()->DB->execute('ALTER TABLE '.SPTOPICS.' DROP blog_post_id');
	SP()->options->delete('sfpostlinking');

	# remove the auths
	SP()->auths->delete('create_linked_topics');
	SP()->auths->delete('break_linked_topics');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-linking');
}

function sp_linking_do_deactivate() {
    # deactivation so make our auths not active
    SP()->auths->deactivate('create_linked_topics');
    SP()->auths->deactivate('break_linked_topics');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-linking');
}
