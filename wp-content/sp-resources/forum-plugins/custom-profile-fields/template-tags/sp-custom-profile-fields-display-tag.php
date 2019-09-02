<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_do_CustomProfileFieldsDisplay($name, $userid=0) {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

    $cfields = sp_custom_profile_fields_get_data();
    if (!empty($cfields)) {
    	foreach ($cfields as $fields) {
    		if ($fields['name'] ==  $name) {
                # grab the data
    			if (empty($userid)) {
					echo SP()->user->thisUser->{$fields['slug']}; # user class handles filtering
    			} else {
    				$data = get_user_meta($userid, $fields['slug'], true);
					if (!empty($data)) {
						# now fitler based on type and output the data
						if ($fields['type'] == 'textarea' || $fields['type'] == 'input') {
							echo SP()->displayFilters->text($data);
						} elseif ($fields['type'] == 'list') {
							$d = implode(', ', $data);
							echo SP()->displayFilters->name($d);
						} else {
							echo SP()->displayFilters->title($data);
						}
					}
    			}
    		}
    	}
    }
}

/* 	============================================================================================
	sp_CustomProfileFieldsDisplayExtended

	displays a custom profile field

	added: version 1.2.0

	parameters:
		name			description								type			default
		----------------------------------------------------------------------------------------
		tagClass		class to be applied for styling			text			spCustomField
		labelClass		class to be applied to optional label	text			spCustomLabel
		labelSep		text to be used between label and data	text			': '
		before			text to display before data				text			''
		after			text to display after the data			text			''
		listSep			text to display between each item (list)text			', '
		echo			echo content or return content			true/false		true


	NOTES:	True must be expressed as a 1 and False as a zero
			All text items can include allowed html

==============================================================================================*/
function sp_do_CustomProfileFieldsDisplayExtended($args, $name, $userid=0, $label='') {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

    $cfields = sp_custom_profile_fields_get_data();
    if (empty($cfields) || empty($name)) return;

	$defs = array('tagClass'	=> 'spCustomField',
				  'labelClass'	=> 'spCustomLabel',
				  'labelSep'	=> ': ',
				  'before'		=> '',
				  'after'		=> '',
				  'listSep'		=> ', ',
				  'echo'		=> 1
				  );
	$a = wp_parse_args($args, $defs);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagClass	= esc_attr($tagClass);
	$labelClass	= esc_attr($labelClass);
	$before		= esc_attr($before);
	$after		= esc_attr($after);
	$listSep	= esc_attr($listSep);
	$echo		= (int) $echo;
	$out		= '';

	foreach ($cfields as $fields) {
		if ($fields['name'] ==  $name) {
			# grab the data
			if (empty($userid)) {
				$cfData = SP()->user->thisUser->{$fields['slug']}; # user class handles filtering
			} else {
				$cfData = get_user_meta($userid, $fields['slug'], true);
				# now fitler based on type and output the data
				if ($fields['type'] == 'textarea' || $fields['type'] == 'input') {
					$cfData = SP()->displayFilters->text($cfData);
				}
			}
			$cfType = $fields['type'];
			break;
		}
	}
	if (!empty($cfData)) {
		$out.= "<div class='$tagClass'>$before";
		if (!empty($label)) {
			$out.= "<span class='$labelClass'>$label$labelSep</span>";
		}
		if ($cfType == 'list') {
			$first = true;
			foreach($cfData as $item) {
				if (!$first) $out.= $listSep;
				$out.= SP()->displayFilters->name($item);
				$first = false;
			}
		} else {
			$out.= $cfData;
		}
		$out.= "$after</div>";
	}

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

/* 	============================================================================================
	sp_CustomProfileFieldsProfileDisplay()

	displays a custom profile field IN either the profile popup or profile page
	This display function performs all of the standard html creation to fit the pattern
	of the standard profile information display

	added: version 1.3.2
*/

function sp_do_CustomProfileFieldsProfileDisplay($name, $userid, $label) {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

    $cfields = sp_custom_profile_fields_get_data();
    if (empty($cfields) || empty($name)) return;

	foreach ($cfields as $fields) {
		if ($fields['name'] ==  $name) {
			# grab the data
			if (empty($userid)) {
				$cfData = SP()->user->thisUser->{$fields['slug']}; # user class handles filtering
			} else {
				$cfData = get_user_meta($userid, $fields['slug'], true);
				# now fitler based on type and output the data
				if ($fields['type'] == 'textarea' || $fields['type'] == 'input') {
					$cfData = SP()->displayFilters->text($cfData);
				}
			}
			$cfType = $fields['type'];
			break;
		}
	}

	if (!empty($cfData)) {
		if (empty($label)) $label = $name;
		if ($cfType == 'list') {
			$data = '';
			$first = true;
			foreach($cfData as $item) {
				if (!$first) $data.= ', ';
				$data.= SP()->displayFilters->name($item);
				$first = false;
			}
		} else {
			$data = $cfData;
		}

		$out = '<div class="spColumnSection spProfileLeftCol">';
		$out.= '<p class="spProfileShowWebsite">'.$label.':</p>';
		$out.= '</div>';
		$out.= '<div class="spColumnSection spProfileSpacerCol"></div>';
		$out.= '<div class="spColumnSection spProfileRightCol">';
		$out.= '<div class="spProfileShowWebsite">'.$data.'</div>';
		$out.= '</div>';
	}

	echo $out;
}
