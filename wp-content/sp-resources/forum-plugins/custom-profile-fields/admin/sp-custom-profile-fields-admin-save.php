<?php
/*
Simple:Press
Custom Profile Fields Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_custom_profile_fields_admin_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$mess = '';

	$fields = array();
	for ($x=0; $x<count(array_unique($_POST['cfieldname'])); $x++) {
		if (!empty($_POST['cfieldname'][$x])) {
			$fields[$x]['name'] = SP()->filters->str($_POST['cfieldname'][$x]);
			$fieldName = sp_create_slug(SP()->saveFilters->title(trim($fields[$x]['name'])), false);
			$fieldName = preg_replace('|[^a-z0-9_]|i', '', $fieldName);
			$fields[$x]['slug'] = SP()->filters->str($fieldName);
			$fields[$x]['type'] = SP()->filters->str($_POST['cfieldtype'][$x]);
			$fields[$x]['values'] = SP()->saveFilters->name(trim($_POST['cfieldvalues'][$x]));
			$fields[$x]['form'] = SP()->saveFilters->title(trim($_POST['cfieldform'][$x]));

			# validation
			if ($fields[$x]['type'] != 'none') {
				if (($fields[$x]['type'] == 'select' || $fields[$x]['type'] == 'list' || $fields[$x]['type'] == 'radio') && empty($fields[$x]['values'])) {
					$mess.= __('A custom profile field was missing the select/radio values. It was discarded!', 'cpf').'<br />';
					unset($fields[$x]);
				}
			} else {
				$mess.= __('A custom profile field was missing the type.  It was discarded!', 'cpf').'<br />';
				unset($fields[$x]);
			}
		}
	}

	$fields = array_values($fields);
	SP()->meta->add('customProfileFields', 'data', $fields);

	$mess.= '<br />'.__('Custom profile fields updated!', 'cpf');
	return $mess;
}
