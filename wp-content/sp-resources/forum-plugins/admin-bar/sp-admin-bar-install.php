<?php
/*
Simple:Press
Admin Bar plugin install routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_admin_bar_do_install() {
	$oldOptions = SP()->options->get('sfadminsettings');
	$newOptions = SP()->options->get('spAdminBar');
	if (!isset($oldOptions['sfqueue']) && empty($newOptions)) {
		$newOptions = array();
		$newOptions['dashboardposts'] = false;
        $newOptions['dbversion'] = SPABDBVERSION;
		SP()->options->add('spAdminBar', $newOptions);

		$options = SP()->memberData->get(SP()->user->thisUser->ID, 'admin_options');
		$options['sfadminbar'] = true;
		SP()->memberData->update(SP()->user->thisUser->ID, 'admin_options', $options);
	} else if (empty($newOptions)) {
		$newOptions = array();
		$newOptions['dashboardposts'] = $oldOptions['sfdashboardposts'];
        $newOptions['dbversion'] = SPABDBVERSION;
		SP()->options->add('spAdminBar', $newOptions);

		$options = SP()->memberData->get(SP()->user->thisUser->ID, 'admin_options');
		$options['sfadminbar'] = true;
		SP()->memberData->update(SP()->user->thisUser->ID, 'admin_options', $options);

        unset($oldOptions['sfqueue']);
        unset($oldOptions['sfmodasadmin']);
        unset($oldOptions['sfshowmodposts']);
        unset($oldOptions['sfbaronly']);
        unset($oldOptions['sfdashboardposts']);
    	SP()->options->update('sfadminsettings', $oldOptions);
	}

    # permission for bypassing akismet checks
   	SP()->auths->add('bypass_akismet', __('Can bypass akismet check on posts', 'spab'), 1, 0, 0, 0, 3);
    SP()->auths->activate('bypass_akismet');

	# create new Akismet setting
	$akismet = SP()->options->get('spAkismet');
	if (empty($akismet)) {
		SP()->options->add('spAkismet', 1);
	}

 	# get auto update running
    $autoup = array('spj.adminBarUpdate', 'admin-bar-update&amp;target=newposts');
	SP()->meta->add('autoupdate', 'admin', $autoup);

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# permissions reset
function sp_admin_bar_do_reset_permissions() {
   	SP()->auths->add('bypass_akismet', __('Can bypass akismet check on posts', 'spab'), 1, 0, 0, 0, 3);
}
