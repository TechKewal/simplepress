<?php
/*
Simple:Press
Identities - general support routines
$LastChangedDate: 2018-08-21 08:48:39 -0500 (Tue, 21 Aug 2018) $
$Rev: 15716 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_identities_do_storage_location() {
	$storage = SP()->options->get('sfconfig');
	$path = SP_STORE_DIR.'/'.$storage['identities'];
	spa_paint_storage_input(__('Identity icons folder', 'sp-plup'), 'identities', $storage['identities'], $path, false, false);
}

function sp_identities_do_storage_save() {
	$storage = SP()->options->get('sfconfig');
	if (!empty($_POST['identities'])) $storage['identities'] = trim(SP()->saveFilters->title(trim($_POST['identities'])), '/');
	SP()->options->update('sfconfig', $storage);
}

function sp_identities_do_profile_edit($out, $id) {
	$meta = SP()->meta->get('user_identities', 'user_identities');
	if (!empty($meta[0]['meta_value'])) {
		foreach ($meta[0]['meta_value'] as $identity) {
        	$out.= '<div class="spColumnSection spProfileLeftCol">';
        	$out.= '<p class="spProfileLabel">'.$identity['name'].': </p>';
        	$out.= '</div>';
        	$out.= '<div class="spColumnSection spProfileSpacerCol"></div>';
        	$out.= '<div class="spColumnSection spProfileRightCol">';
			$thisValue = (!empty(SP()->user->profileUser->{$identity['slug']})) ? SP()->user->profileUser->{$identity['slug']} : '';
        	$out.= '<input type="text" class="spControl" name="'.$identity['slug'].'" id="'.$identity['slug'].'" value="'.esc_attr($thisValue).'" />';
        	$out.= '</div>';
		}
	}
    return $out;
}

function sp_identities_do_profile_save($message, $thisUser) {
	$identities = sp_identities_get_data();
	if (!empty($identities)) {
		foreach ($identities as $identity) {
            update_user_meta($thisUser, $identity['slug'], SP()->saveFilters->title(trim($_POST[$identity['slug']])));
		}
	}
    return $message;
}

function sp_identities_do_load_meta($list) {
    $identities = sp_identities_get_data();
    if (!empty($identities)) {
        foreach ($identities as $identity) {
        	$list[$identity['slug']] = 'title';
        }
    }
    return $list;
}

function sp_identities_get_data() {
	$identities = SP()->meta->get_value('user_identities', 'user_identities');
	return $identities;
}

# personal data export
function sp_privacy_do_identities_profile($exportItems, $spUserData, $groupID, $groupLabel) {
	$data = array();
	$idents = sp_identities_get_data();
	if (!empty($idents)) {
		foreach($idents as $ident) {
			$s = $ident['slug'];
			if (!empty($spUserData->$s)) {
				$data[] = array(
					'name'	=> $ident['name'],
					'value' => $spUserData->$s
				);
			}
		}
		$exportItems[] = array(
			'group_id'		=> $groupID,
			'group_label' 	=> $groupLabel,
			'item_id' => 'Profile',
			'data' => $data,
		);
	}
	
	return $exportItems;
}
