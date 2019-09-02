<?php
/*
Simple:Press
custom profile fields plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the custom profile fields plugin uninstall only
function sp_custom_profile_fields_do_uninstall() {
    # remove all saved custom profile usemeta
	$cfields = sp_custom_profile_fields_get_data();
	if (!empty($cfields)) {
		foreach ($cfields as $x => $fields) {
			SP()->DB->execute('DELETE FROM '.SPUSERMETA." WHERE meta_key='".$fields['slug']."'");
		}
	}

    # remove the custom fields
	$customFields = SP()->meta->get('custom_field');
	SP()->meta->delete($customFields[0]['meta_id']);

	# remove glossary entries
	sp_remove_glossary_plugin('sp-customprofile');
}

function sp_custom_profile_fields_do_deactivate() {
	# remove glossary entries
	sp_remove_glossary_plugin('sp-customprofile');
}

function sp_custom_profile_fields_do_sp_uninstall() {
    # remove all saved custom profile usemeta
	$cfields = sp_custom_profile_fields_get_data();
	if (!empty($cfields)) {
		foreach ($cfields as $x => $fields) {
			SP()->DB->execute('DELETE FROM '.SPUSERMETA." WHERE meta_key='".$fields['slug']."'");
		}
	}
}
