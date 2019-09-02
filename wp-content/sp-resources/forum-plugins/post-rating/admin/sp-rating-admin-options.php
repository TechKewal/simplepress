<?php
/*
Simple:Press
Post Rating Plugin Admin Options Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_rating_admin_options_form() {
	$postratings = SP()->options->get('postratings');

	spa_paint_open_panel();
		spa_paint_open_fieldset(__('Post Ratings', 'sp-rating'), true, 'post-ratings');
			$values = array(__('Thumbs up/down', 'sp-rating'), __('Stars', 'sp-rating'));
			$msg = '<p>'.__('WARNING: Changing the rating styles will reset all of the currently collected ratings data.  Please check the confirm box to indicate that you really want to do this.  The database tables will be reset when the options are saved.', 'sp-rating').'</p>';
			spa_paint_radiogroup_confirm(__('Select post rating style', 'sp-rating'), 'ratingsstyle', $values, $postratings['ratingsstyle'], $msg, false, true);
		spa_paint_close_fieldset();
	spa_paint_close_panel();
}

function spa_paint_radiogroup_confirm($label, $name, $values, $current, $msg, $large=false, $displayhelp=true) {
	global $tab;

	$pos = 1;

	echo "<table class='form-table'><tr style='vertical-align:top'>\n";
	echo "<td class='sflabel' style='width:100%'>\n";
	echo '<table class="form-table table-cbox"><tr><td class="td-cbox">';
	echo $label;
	echo ":\n</td>\n";
	echo "<td style='width:70%' class='td-cbox'>\n";
	foreach ($values as $value) {
		$check = '';
		$select = '';
		if ($current == $pos) {
			$check = " checked = 'checked' ";
		} else {
			$select = " data-target='confirm-$name'";
		}
		echo "<input type='radio' id='sfradio$pos' name='$name' class='spLayerToggle' tabindex='$tab' value='$pos'$check$select />";
		echo "<label class='sflabel radio' for='sfradio$pos'>".esc_html(SP()->primitives->admin_text($value)).'</label><br />';
		$pos++;
		$tab++;
	}
	echo '</td></tr></table>';
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr id='confirm-$name' class='inline_edit'>";
		echo '<td>';
			echo '<table class="form-table table-cbox">';
				echo '<tr>';
					echo "<td class='longmessage'>$msg</td>";
					echo '</tr><tr>';
					echo "<td class='sflabel' style='width:100%'>";
					echo '<table class="form-table table-cbox"><tr><td class="td-cbox">';
					echo "<input type='checkbox' name='confirm-box-$name' id='sfconfirm-box-$name' tabindex='$tab' />\n";
					echo "<label for='sfconfirm-box-$name'>".esc_html(SP()->primitives->admin_text('Confirm'))."</label>\n";
					echo '</td></tr></table>';
					echo '</td>';
				echo '</tr>';
			echo '</table>';
		echo '</td>';
	echo '</tr></table>';
	$tab++;
}
