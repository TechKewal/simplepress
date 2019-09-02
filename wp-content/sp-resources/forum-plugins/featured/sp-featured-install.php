<?php
/*
Simple:Press
Featured Topics and Posts Plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_featured_do_install() {
	$options = SP()->options->get('featured');
	if (empty($options)) {
        $options['dbversion'] = SPFEATUREDDBVERSION;
        SP()->options->update('featured', $options);
    }

    # set up our sfmeta if needed
    $check = SP()->meta->get('featured', 'topics');
	if (empty($check)) SP()->meta->add('featured', 'topics', array());
    $check = SP()->meta->get('featured', 'posts');
	if (empty($check)) SP()->meta->add('featured', 'posts', array());

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# sp reactivated.
function sp_featured_do_sp_activate() {
	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# permissions reset
function sp_featured_do_reset_permissions() {
}
