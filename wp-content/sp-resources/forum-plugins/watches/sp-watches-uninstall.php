<?php
/*
Simple:Press
Watches plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the post rating plugin uninstall only
function sp_watches_do_uninstall() {
	# remove our profile tab/meuns
    SP()->profile->delete_tab(__('Watches', 'sp-watches'));

	# remove our db stuff
    SP()->activity->delete('type='.SPACTIVITY_WATCH);

	# remove our activity type
	SP()->activity->delete_type('watches');

	# remove the auth
	if (!empty(SP()->core->forumData['auths_map']['watch'])) SP()->auths->delete('watch');

    # delete our option table
    SP()->options->delete('watches');

    # remove our auto update stuff
    $up = SP()->meta->get('autoupdate', 'watches');
    if ($up) SP()->meta->delete($up[0]['meta_id']);
}
