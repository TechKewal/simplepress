<?php
/*
Simple:Press
Name plugin install/upgrade routine
$LastChangedDate: 2016-01-04 14:44:34 -0600 (Mon, 04 Jan 2016) $
$Rev: 13763 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_woocommerce_do_upgrade_check() {
    if (!SP()->plugin->is_active('woocommerce/sp-woocommerce-plugin.php')) return;

    $options = SP()->options->get('woocommerce');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPWCDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades
    if ($db < 1) {
		# admin task glossary entries
		include_once('sp-admin-glossary.php');
	}

    # save data
    $options['dbversion'] = SPWCDBVERSION;
    SP()->options->update('woocommerce', $options);
}

?>