<?php
/*
Simple:Press
WooCommerce Plugin Admin Options Save Routine
$LastChangedDate: 2015-04-15 22:09:47 -0500 (Wed, 15 Apr 2015) $
$Rev: 12722 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_woocommerce_do_admin_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$woocommerce = SP()->options->get('woocommerce');

    # save text for link
    $wclinktext = trim($_POST['wcuserprofilelinktext']);
    $wclinktext = SP()->saveFilters->nohtml($wclinktext);
    $wclinktext = SP()->saveFilters->escape($wclinktext);
	$woocommerce['wcuserprofilelinktext'] = $wclinktext;
	
	# save the first optional url link.
    $wclinktext = trim($_POST['wccustomurl01']);
    $wclinktext = SP()->saveFilters->nohtml($wclinktext);
    $wclinktext = SP()->saveFilters->escape($wclinktext);
	$woocommerce['wccustomurl01'] = $wclinktext;

	# save the first optional url link text/label.
    $wclinktext = trim($_POST['wccustomlinktext01']);
    $wclinktext = SP()->saveFilters->nohtml($wclinktext);
    $wclinktext = SP()->saveFilters->escape($wclinktext);
	$woocommerce['wccustomlinktext01'] = $wclinktext;	
	
	# save the second optional url link.
    $wclinktext = trim($_POST['wccustomurl02']);
    $wclinktext = SP()->saveFilters->nohtml($wclinktext);
    $wclinktext = SP()->saveFilters->escape($wclinktext);
	$woocommerce['wccustomurl02'] = $wclinktext;
	
	# save the second optional url link text/label.
    $wclinktext = trim($_POST['wccustomlinktext02']);
    $wclinktext = SP()->saveFilters->nohtml($wclinktext);
    $wclinktext = SP()->saveFilters->escape($wclinktext);
	$woocommerce['wccustomlinktext02'] = $wclinktext;		
	
 	SP()->options->update('woocommerce', $woocommerce);

	return __('WooCommerce options updated', 'sp-woocommerce');
}

?>