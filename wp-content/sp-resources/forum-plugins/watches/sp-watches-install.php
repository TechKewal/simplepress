<?php
/*
Simple:Press
Watches plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_watches_do_install() {
	SP()->activity->add_type('watches');

	$watches = SP()->options->get('watches');
	if (empty($watches)) {
        # need new columns
		$watches = array();
        $watches['dbversion'] = SPWATCHESDBVERSION;
		SP()->options->add('watches', $watches);
    }

   	SP()->profile->add_tab('Watches');
	SP()->profile->add_menu('Watches', 'Manage Watches', WFORMSDIR.'sp-watches-manage-form.php');

	# add in auto update stuff
    $autoup = array('spj.watchesupdate', 'watches-manage&amp;target=watches');
    SP()->meta->add('autoupdate', 'watches', $autoup);

    # add a new permission into the auths table
	SP()->auths->add('watch', __('Can watch topics within a forum', 'sp-watches'), 1, 1, 0, 0, 1);

    # activation so make our auth active
    SP()->auths->activate('watch');

	# flush rewrite rules for pretty permalinks
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

function sp_watches_do_reset_permissions() {
	SP()->auths->add('watch', __('Can watch topics within a forum', 'sp-watches'), 1, 1, 0, 0, 1);
}
