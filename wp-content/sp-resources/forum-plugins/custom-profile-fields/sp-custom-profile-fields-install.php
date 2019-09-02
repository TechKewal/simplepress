<?php
/*
Simple:Press
Custom Profile Fields plugin install routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_custom_profile_fields_do_install() {
	# if any old style custom profile fields, convert to new format
	$customFields = SP()->meta->get('custom_field');
	if (!empty($customFields)) {
		$newCustomFields = array();
		foreach ($customFields as $index => $custom) {
			# move existing values over - make name same as slug
			$newCustomFields[$index]['name'] = $custom['meta_key'];
			$newCustomFields[$index]['slug'] = $custom['meta_key'];
			$newCustomFields[$index]['type'] = $custom['meta_value']['type'];
			$newCustomFields[$index]['values'] = $custom['meta_value']['selectvalues'];
			$newCustomFields[$index]['form'] = '';

			# delete the old style custom profile meta
			SP()->meta->delete($custom['meta_id']);
		}

		SP()->meta->add('customProfileFields', 'data', $newCustomFields);
	}
	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}
