<?php
/*
Simple:Press
Custom Profile Fields plugin ajax routine for management functions
$LastChangedDate: 2018-10-17 15:17:49 -0500 (Wed, 17 Oct 2018) $
$Rev: 15756 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

if (!sp_nonce('cpf')) die();

# Check Whether User Can Manage Profiles
if (!SP()->auths->current_user_can('SPF Manage Profiles')) die();

$action = SP()->filters->str($_GET['targetaction']);

if ($action == 'delete-cfield') {
	$cfields = sp_custom_profile_fields_get_data();

	$slug = SP()->filters->str($_GET['slug']);

	if (!empty($cfields)) {
		foreach ($cfields as $x => $fields) {
			# check for this cfield
			if ($fields['slug'] == $slug) {
                unset($cfields[$x]);
				SP()->DB->execute('DELETE FROM '.SPUSERMETA." WHERE meta_key='$slug'");
                break;
            }
		}
	}

	# reduce the array down and save
	$cfields = array_values($cfields);
	SP()->meta->add('customProfileFields', 'data', $cfields);
}

die();
