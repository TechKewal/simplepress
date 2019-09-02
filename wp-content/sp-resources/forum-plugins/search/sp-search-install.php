<?php
/*
Simple:Press
Name plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_search_do_install() {
	$options = SP()->options->get('search');
	if (empty($options)) {
        $options['dbversion'] = SPSEARCHDBVERSION;

		$options['form'] = __('Include blog posts', 'sp-search');
		$options['ftab'] = __('Forum search results', 'sp-search');
		$options['btab'] = __('Blog search results', 'sp-search');

		$options['searchposttypes']['post'] = true;
		$options['searchposttypes']['page'] = true;

        SP()->options->update('search', $options);
    }
	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# sp reactivated.
function sp_search_do_sp_activate() {
}

# permissions reset
function sp_search_do_reset_permissions() {
}
