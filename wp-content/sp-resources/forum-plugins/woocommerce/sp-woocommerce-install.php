<?php
/*
Simple:Press
Name plugin install/upgrade routine
$LastChangedDate: 2016-01-04 14:44:34 -0600 (Mon, 04 Jan 2016) $
$Rev: 13763 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_woocommerce_do_install() {
	$options = SP()->options->get('woocommerce');
	if (empty($options)) {
        $options = array();
        $options['userprofilelinktext'] = __('Forums User Profile', 'sp-woocommerce');
        $options['dbversion'] = SPWCDBVERSION;
        SP()->options->update('woocommerce', $options);
	}
	# admin task glossary entries
	include_once('sp-admin-glossary.php');
}

# sp reactivated.
function sp_woocommerce_do_sp_activate() {
}

# permissions reset
function sp_woocommerce_do_reset_permissions() {
}

?>