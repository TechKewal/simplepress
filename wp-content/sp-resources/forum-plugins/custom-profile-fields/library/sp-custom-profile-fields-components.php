<?php
/*
Simple:Press
Custom Profile Fields Plugin Support Routines
$LastChangedDate: 2018-08-21 08:48:39 -0500 (Tue, 21 Aug 2018) $
$Rev: 15716 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_custom_profile_do_load_meta($list) {
    $cfields = sp_custom_profile_fields_get_data();
    if (!empty($cfields)) {
        foreach ($cfields as $c) {
        	$list[$c['slug']] = ($c['type'] == 'textarea') ? 'text' : 'title';
        }
    }
    return $list;
}

function sp_custom_profile_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'custom-profile-fields/sp-custom-profile-fields-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'cpf')."'>".__('Uninstall', 'cpf').'</a>';
        $url = SPADMINPROFILE.'&amp;tab=plugin&amp;admin=sp_custom_profile_fields_admin&amp;save=sp_custom_profile_fields_update&amp;form=1';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'cpf')."'>".__('Options', 'cpf').'</a>';
    }
	return $actionlink;
}

function sp_custom_profile_fields_get_data() {
	$fields = SP()->meta->get_value('customProfileFields');
	return $fields;
}

function sp_custom_profile_fields_menu_select($tabs, $form) {
	echo '<select class="sfacontrol" name="cfieldform[]">';
	$selected = ($form == 'none') ? ' selected="selected"' : ' ';
	echo "<option value='none'$selected>".__("Don't display", 'cpf').'</option>';
	if ($tabs) {
		foreach ($tabs as $tab) {
			if ($tab['menus']) {
				foreach ($tab['menus'] as $menu) {
					$selected = ($form == $menu['slug']) ? ' selected="selected"' : ' ';
					echo "<option value='".esc_attr($menu['slug'])."'$selected>".$menu['name'].'</option>';
				}
			}

		}
	}
	echo '</select>';
}

function sp_custom_profile_fields_output($out, $userid, $thisForm) {
	# see if we need to show any of our custom profile fields
	$cfields = sp_custom_profile_fields_get_data();
	if (!empty($cfields)) {
		# get the users profile data which will contain the custom profile data too
		sp_SetupUserProfileData($userid);

		foreach ($cfields as $fields) {
			if ($fields['form'] ==  $thisForm) {
				# form match - display the custom profile field
                $tout = '';
				switch($fields['type']) {
					case 'checkbox':
						$tout.= '<div class="spColumnSection spProfileLeftCol">';
						$tout.= '<p class="spProfileLabel">'.$fields['name'].'</p>';
						$tout.= '</div>';
						$tout.= '<div class="spColumnSection spProfileSpacerCol"></div>';
						$tout.= '<div class="spColumnSection spProfileRightCol">';
						$selected = (!empty(SP()->user->profileUser->{$fields['slug']}) && isset(SP()->user->profileUser->{$fields['slug']})) ? ' checked="checked"' : '';
						$tout.= '<p class="spProfileLabel"><input type="checkbox"'.$selected.' name="'.esc_attr($fields['slug']).'" id="sf-'.esc_attr($fields['slug']).'"><label for="sf-'.esc_attr($fields['slug']).'"></label></p>';
						$tout.= '</div>';
						break;

					case 'input':
						$tout.= '<div class="spColumnSection spProfileLeftCol">';
						$tout.= '<p class="spProfileLabel">'.$fields['name'].': </p>';
						$tout.= '</div>';
						$tout.= '<div class="spColumnSection spProfileSpacerCol"></div>';
						$tout.= '<div class="spColumnSection spProfileRightCol">';
                        $text = (!empty(SP()->user->profileUser->{$fields['slug']})) ? SP()->user->profileUser->{$fields['slug']} : '';
						$tout.= '<p class="spProfileLabel"><input class="spControl" type="text" name="'.esc_attr($fields['slug']).'" value="'.esc_attr($text).'" /></p>';
						$tout.= '</div>';
						break;

					case 'textarea':
						$tout.= '<div class="spColumnSection spProfileLeftCol">';
						$tout.= '<p class="spProfileLabel">'.$fields['name'].': </p>';
						$tout.= '</div>';
						$tout.= '<div class="spColumnSection spProfileSpacerCol"></div>';
						$tout.= '<div class="spColumnSection spProfileRightCol">';
                        $text = (!empty(SP()->user->profileUser->{$fields['slug']})) ? SP()->user->profileUser->{$fields['slug']} : '';
						$tout.= '<p class="spProfileLabel"><textarea name="'.esc_attr($fields['slug']).'">'.SP()->editFilters->text($text).'</textarea></p>';
						$tout.= '</div>';
						break;

					case 'select':
					case 'list':
						$tout.= '<div class="spColumnSection spProfileLeftCol">';
						$tout.= '<p class="spProfileLabel">'.$fields['name'].': </p>';
						$tout.= '</div>';
						$tout.= '<div class="spColumnSection spProfileSpacerCol"></div>';
						$tout.= '<div class="spColumnSection spProfileRightCol">';
						$tout.= '<p class="spProfileLabel">';

						if ($fields['type'] == 'select') {
							$tout.= '<select class="spControl" name="'.esc_attr($fields['slug']).'" id="'.esc_attr($fields['slug']).'" >';
							if (empty(SP()->user->profileUser->{$fields['slug']})) $tout.= '<option value="" selected="selected">'.__('Choose one', 'cpf').'</option>';
						} else {
							$tout.= '<select class="spControl" style="height:auto;" name="'.esc_attr($fields['slug']).'[]" id="'.esc_attr($fields['slug']).'" multiple="multiple" >';
							if (empty(SP()->user->profileUser->{$fields['slug']})) $tout.= '<option value="" selected="selected">'.__('Choose one or more', 'cpf').'</option>';
							$selList = array();
							if (!empty(SP()->user->profileUser->{$fields['slug']})) $selList = SP()->user->profileUser->{$fields['slug']};
						}
						$list = array_map('trim', explode(',', $fields['values']));
						if ($list) {
							foreach ($list as $option) {
								if ($fields['type'] == 'select') {
									$selected = (!empty(SP()->user->profileUser->{$fields['slug']}) && SP()->user->profileUser->{$fields['slug']} == $option) ? ' selected="selected"' : '';
								} else {
									$selected = (in_array($option, $selList)) ? ' selected="selected"' : '';
								}
								$tout.= '<option value="'.esc_attr($option).'"'.$selected.'>'.SP()->displayFilters->name($option).'</option>';
							}
						}
						$tout.= '</p></select>';
						$tout.= '</div>';
						break;

					case 'radio':
						$tout.= '<div class="spColumnSection spProfileLeftCol">';
						$tout.= '<p class="spProfileLabel">'.$fields['name'].': </p>';
						$tout.= '</div>';
						$tout.= '<div class="spColumnSection spProfileSpacerCol"></div>';
						$tout.= '<div class="spColumnSection spProfileRightCol">';
						$list = explode(',', $fields['values']);
						if ($list) {
							foreach ($list as $x => $option) {
								if(!isset($option)) $option = false;
								$check = '';
								if(isset(SP()->user->profileUser->{$fields['slug']})) {
									$check = (SP()->user->profileUser->{$fields['slug']} == trim($option)) ? ' checked="checked"' : '';
								}
								$tout.= '<p class="spProfileLabel"><input class="spControl" type="radio" name="'.esc_attr($fields['slug']).'" id="sf-'.esc_attr($fields['slug']).$x.'" value="'.trim(esc_attr($option)).'"'.$check.' /><label for="sf-'.esc_attr($fields['slug']).$x.'">'.trim(esc_attr($option)).'</label></p>';
								$tout.= '<br />';
							}
						}
						$tout.= '</div>';
						break;
				}

               $out.= apply_filters('sph_custom_profile_fields_'.$fields['slug'], $tout);
			}
		}
	}

	return $out;
}

function sp_custom_profile_fields_save($userid, $thisForm) {
	# see if we need to save any of our custom profile fields
	$cfields = sp_custom_profile_fields_get_data();
	if (!empty($cfields)) {
		foreach ($cfields as $fields) {
			if ($fields['form'] ==  $thisForm) {
				switch($fields['type']) {
					case 'list':
						$d = array();
						if (isset($_POST[$fields['slug']])) {
							foreach($_POST[$fields['slug']] as $item) {
								$d[] = SP()->saveFilters->name($item);
							}
						}
						update_user_meta($userid, $fields['slug'], $d);
						break;

					case 'select':
						update_user_meta($userid, $fields['slug'], SP()->saveFilters->name($_POST[$fields['slug']]));
						break;

					case 'checkbox':
						$d = isset($_POST[$fields['slug']]);
						update_user_meta($userid, $fields['slug'], $d);
						break;

					case 'radio':
						$d = (isset($_POST[$fields['slug']])) ? $_POST[$fields['slug']] : '';
						update_user_meta($userid, $fields['slug'], $d);
						break;

					case 'textarea':
						update_user_meta($userid, $fields['slug'], SP()->saveFilters->text($_POST[$fields['slug']]));
						break;

					case 'input':
					default:
						update_user_meta($userid, $fields['slug'], SP()->saveFilters->title($_POST[$fields['slug']]));
						break;
				}
			}
		}
	}
}

function sp_custom_profile_fields_do_bp_profile() {
    $bpdata = SP()->options->get('buddypress');
    if (!SP()->plugin->is_active('buddypress/sp-buddypress-plugin.php') || !$bpdata['integratecpf']) return;

	$cfields = sp_custom_profile_fields_get_data();
	if (!empty($cfields)) {
		sp_SetupUserProfileData(SP()->user->thisUser->ID);

		foreach ($cfields as $fields) {
            $out = '';
			switch($fields['type']) {
				case 'checkbox':
                	$out.= '<p class="spProfileLabel">'.$fields['name'].': ';
                	$checked = (SP()->user->profileUser->{$fields['slug']}) ? $checked = 'checked="checked" ' : '';
                	$out.= '<span class="spProfileData"><input type="checkbox" '.$checked.'name="'.esc_attr($fields['slug']).'" id="sf-'.esc_attr($fields['slug']).'" /></span></p>';
					break;

				case 'input':
                	$out.= '<p class="spProfileLabel">'.$fields['name'].': ';
                    $text = (!empty(SP()->user->profileUser->{$fields['slug']})) ? SP()->user->profileUser->{$fields['slug']} : '';
                	$out.= '<span class="spProfileData"><input type="text" name="'.esc_attr($fields['slug']).'" value="'.esc_attr($text).'" /></span></p>';
					break;

				case 'textarea':
                	$out.= '<p class="spProfileLabel">'.$fields['name'].': ';
                    $text = (!empty(SP()->user->profileUser->{$fields['slug']})) ? SP()->user->profileUser->{$fields['slug']} : '';
                	$out.= '<span class="spProfileData"><textarea name="'.esc_attr($fields['slug']).'" />'.SP()->editFilters->text($text).'</textarea></span></p>';
					break;

				case 'select':
				case 'list':
                	$out.= '<p class="spProfileLabel">'.$fields['name'].': ';
                    $text = (!empty(SP()->user->profileUser->{$fields['slug']})) ? SP()->user->profileUser->{$fields['slug']} : '';

					if ($fields['type'] == 'select') {
						$out.= '<select name="'.esc_attr($fields['slug']).'" id="'.esc_attr($fields['slug']).'" >';
						if (empty(SP()->user->profileUser->{$fields['slug']})) $tout.= '<option value="" selected="selected">'.__('Choose one', 'cpf').'</option>';
					} else {
						$out.= '<select style="height:auto;" name="'.esc_attr($fields['slug']).'[]" id="'.esc_attr($fields['slug']).'" multiple="multiple" >';
						if (empty(SP()->user->profileUser->{$fields['slug']})) $tout.= '<option value="" selected="selected">'.__('Choose one or more', 'cpf').'</option>';
						$selList = array();
						if (!empty(SP()->user->profileUser->{$fields['slug']})) $selList = SP()->user->profileUser->{$fields['slug']};
					}
					$list = explode(',', $fields['values']);
					if ($list) {
						foreach ($list as $option) {
							if ($fields['type'] == 'select') {
								$selected = (!empty(SP()->user->profileUser->{$fields['slug']}) && SP()->user->profileUser->{$fields['slug']} == $option) ? ' selected="selected"' : '';
							} else {
								$selected = (in_array(trim($option), $selList)) ? ' selected="selected"' : '';
							}
                        	$out.= '<span class="spProfileData"><option value="'.trim(esc_attr($option)).'"'.$selected.'>'.trim(SP()->displayFilters->name($option)).'</option></span>';
						}
					}
					$out.= '</p></select>';
					break;

				case 'radio':
                	$out.= '<p class="spProfileLabel">'.$fields['name'].': </p>';
					$list = explode(',', $fields['values']);
					if ($list) {
						foreach ($list as $x => $option) {
							if (!isset($option)) $option = false;
							$check = '';
							if (isset(SP()->user->profileUser->{$fields['slug']})) $check = (SP()->user->profileUser->{$fields['slug']} == trim($option)) ? ' checked="checked"' : '';
                        	$out.= '<span class="spProfileData"><input class="spControl" type="radio" name="'.esc_attr($fields['slug']).'" id="sf-'.esc_attr($fields['slug']).$x.'" value="'.trim(esc_attr($option)).'"'.$check.' /><label for="sf-'.esc_attr($fields['slug']).$x.'"> '.trim(esc_attr($option)).'</label></span>';
							$out.= '<br />';
						}
					}
					break;
			}

           echo $out;
        }
    }
}

function sp_custom_profile_fields_do_bp_profile_save($errors, $userid) {
    $bpdata = SP()->options->get('buddypress');
    if (!SP()->plugin->is_active('buddypress/sp-buddypress-plugin.php') || !$bpdata['integratecpf']) return;

	$cfields = sp_custom_profile_fields_get_data();
	if (!empty($cfields)) {
		foreach ($cfields as $fields) {
			switch($fields['type']) {
				case 'list':
					$d = array();
					if(isset($_POST[$fields['slug']])) {
						foreach($_POST[$fields['slug']] as $item) {
							$d[] = SP()->saveFilters->name($item);
						}
					}
					update_user_meta($userid, $fields['slug'], $d);
					break;

				case 'select':
					update_user_meta($userid, $fields['slug'], SP()->saveFilters->name($_POST[$fields['slug']]));
					break;

				case 'checkbox':
					$d = isset($_POST[$fields['slug']]);
					update_user_meta($userid, $fields['slug'], $d);
					break;

				case 'radio':
					$d = (isset($_POST[$fields['slug']])) ? $_POST[$fields['slug']] : '';
					update_user_meta($userid, $fields['slug'], $d);
					break;

				case 'textarea':
					update_user_meta($userid, $fields['slug'], SP()->saveFilters->text($_POST[$fields['slug']]));
					break;

				case 'input':
				default:
					update_user_meta($userid, $fields['slug'], SP()->saveFilters->title($_POST[$fields['slug']]));
					break;
			}
		}
	}

    return $errors;
}

# personal data export
function sp_privacy_do_custom_profile($exportItems, $spUserData, $groupID, $groupLabel) {
	$data = array();
	$cFields = sp_custom_profile_fields_get_data();
	if (!empty($cFields)) {
		foreach($cFields as $cField) {
			if ($cField['type'] == 'input' || $cField['type'] == 'textarea') {
				$s = $cField['slug'];
				if (!empty($spUserData->$s)) {
					$data[] = array(
						'name'	=> $cField['name'],
						'value' => $spUserData->$s
					);
				}
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
