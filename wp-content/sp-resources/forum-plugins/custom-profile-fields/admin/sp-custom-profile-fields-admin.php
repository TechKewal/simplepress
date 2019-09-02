<?php
/*
Simple:Press
Custom Profile Fields Plugin Admin Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_custom_profile_fields_admin_form() {
	# grab the defined custom profile fields
	$cfields = sp_custom_profile_fields_get_data();

	# get the current profile tabs and menus
	$tabs = SP()->profile->get_tabs_menus();

	spa_paint_open_tab(__('Profiles', 'cpf').' - '.__('Custom Profile Fields', 'cpf'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Custom Profile Fields', 'cpf'), true, 'custom-fields');
			echo '<table class="widefat fixed striped spMobileTable800">';
            echo '<thead><tr>';
			echo '<th style="text-align:center">'.__('Name', 'cpf').'</th>';
			echo '<th style="text-align:center">'.__('Slug', 'cpf').'</th>';
			echo '<th style="text-align:center">'.__('Type', 'cpf').'</th>';
			echo '<th style="text-align:center">'.__('Values (select, list and radio only)', 'cpf').'</th>';
			echo '<th style="text-align:center">'.__('Profile Form For Display', 'cpf').'</th>';
			echo '<th style="text-align:center">'.__('Delete', 'cpf').'</th>';
			echo '</tr>';
            echo '</thead>';

            echo '<tbody>';

			# display custom field info
			if (!empty($cfields)) {
				foreach ($cfields as $x => $fields) {
					echo "<tr id='cfield$x' class='spMobileTableData'>";
					echo '<td data-label="'.__('Name', 'sp-cpf').'" style="text-align:center">';
					echo '<input type="text" name="cfieldname[]" value="'.esc_attr($fields['name']).'" />';
					echo '</td>';
					echo '<td data-label="'.__('Slug', 'sp-cpf').'" style="text-align:center">';
					echo '<input type="text" disabled="disabled" name="cfieldslug[]" value="'.esc_attr($fields['slug']).'" />';
					echo '</td>';
					echo '<td data-label="'.__('Type', 'sp-cpf').'" style="text-align:center">';
					echo '<select name="cfieldtype[]">';
					$cselected = $iselected = $tselected = $sselected = $lselected = $rselected ='';
					if ($fields['type'] == 'checkbox') $cselected = ' selected';
					if ($fields['type'] == 'input') $iselected = ' selected';
					if ($fields['type'] == 'textarea') $tselected = ' selected';
					if ($fields['type'] == 'select') $sselected = ' selected';
					if ($fields['type'] == 'list') $lselected = ' selected';
					if ($fields['type'] == 'radio') $rselected = ' selected';
					echo '<option value="checkbox"'.$cselected.'>'.__('Checkbox', 'cpf').'</option>';
					echo '<option value="input"'.$iselected.'>'.__('Input', 'cpf').'</option>';
					echo '<option value="textarea"'.$tselected.'>'.__('Textarea', 'cpf').'</option>';
					echo '<option value="select"'.$sselected.'>'.__('Select', 'cpf').'</option>';
					echo '<option value="list"'.$lselected.'>'.__('List', 'cpf').'</option>';
					echo '<option value="radio"'.$rselected.'>'.__('Radio', 'cpf').'</option>';
					if ($cselected == '' && $iselected == '' && $tselected == '' && $sselected == '' && $lselected == '' && $rselected == '') echo '<option value="error" selected>'.__('Error!', 'cpf').'</option>';
					echo '</select>';
					echo '</td>';
					echo '<td data-label="'.__('Values', 'sp-cpf').'" style="text-align:center">';
					$select = '';
					if (!empty($fields['values'])) $select = $fields['values'];
					echo '<input type="text" name="cfieldvalues[]" value="'.SP()->displayFilters->name($select).'" />';

					echo '</td>';
					echo '<td data-label="'.__('Display', 'sp-cpf').'" style="text-align:center">';
					sp_custom_profile_fields_menu_select($tabs, $fields['form']);
					echo '</td>';
					echo '<td data-label="'.__('Delete', 'sp-cpf').'" style="text-align:center">';
                    $msg = esc_attr(__('Are you sure you want to remove this custom profile field?'), 'sp-cpf');
	                $site = esc_url(wp_nonce_url(SPAJAXURL.'cpf&amp;targetaction=delete-cfield&amp;slug='.$fields['slug'], 'cpf'));
					?>
					<img class="spDeleteRow" data-msg="<?php echo $msg; ?>" data-url="<?php echo $site; ?>" data-target="cfield<?php echo $x; ?>" src="<?php echo SPCOMMONIMAGES; ?>delete.png" title="<?php esc_attr_e(__('Delete Custom Field', 'cpf')); ?>" alt="" />&nbsp;
					<?php
					echo '</td>';
					echo '</tr>';
				}
			}

			# always have one empty slot available for new custom field
			echo '<tr class="spMobileTableData">';
			echo '<td data-label="'.__('Name', 'sp-cpf').'" style="text-align:center">';
			echo '<input type="text" name="cfieldname[]" value="" />';
			echo '</td>';
			echo '<td data-label="'.__('Slug', 'sp-cpf').'" style="text-align:center">';
			echo '<input type="text" disabled="disabled" name="cfieldslug[]" value="'.esc_attr(__('slug will be auto generated', 'cpf')).'" />';
			echo '</td>';
			echo '<td data-label="'.__('Type', 'sp-cpf').'" style="text-align:center">';
			echo '<select name="cfieldtype[]">';
			echo '<option value="none">'.__('Custom Field Type', 'cpf').'</option>';
			echo '<option value="checkbox">'.__('Checkbox', 'cpf').'</option>';
			echo '<option value="input">'.__('Input', 'cpf').'</option>';
			echo '<option value="textarea">'.__('Textarea', 'cpf').'</option>';
			echo '<option value="select">'.__('Select', 'cpf').'</option>';
			echo '<option value="list">'.__('List', 'cpf').'</option>';
			echo '<option value="radio">'.__('Radio', 'cpf').'</option>';
			echo '</select>';
			echo '</td>';
			echo '<td data-label="'.__('Values', 'sp-cpf').'" style="text-align:center">';
			echo '<input type="text" name="cfieldvalues[]" value="" />';
			echo '</td>';
			echo '<td data-label="'.__('Display', 'sp-cpf').'" style="text-align:center">';
			sp_custom_profile_fields_menu_select($tabs, 'none');
			echo '</td>';
			echo '<td data-label="'.__('Delete', 'sp-cpf').'" style="text-align:center; min-height:38px;"></td>';
			echo '</tr>';

            echo '</tbody>';
            echo '</table>';
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
}
