<?php
/*
Simple:Press
Post Rating plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for when the parent SP plugin is uninstalled
function sp_rating_do_sp_uninstall() {
    # make sure sp uninstall initiated
	if (SP()->options->get('sfuninstall')) SP()->DB->execute("DROP TABLE IF EXISTS ".SPRATINGS);
}

# this uninstall function is for the post rating plugin uninstall only
function sp_rating_do_uninstall() {
    # remove our db stuff
	SP()->DB->execute('DROP TABLE IF EXISTS '.SPRATINGS);
	SP()->DB->execute('ALTER TABLE '.SPFORUMS.' DROP post_ratings');

	# remove the auth
	if (!empty(SP()->core->forumData['auths_map']['rate_posts'])) SP()->auths->delete('rate_posts');

 	# remove our user activity
    SP()->activity->delete('type='.SPACTIVITY_RATING);

	# remove our activity type
	SP()->activity->delete_type('posts rated');

	# delete our option table
    SP()->options->delete('postratings');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-rating');
}
