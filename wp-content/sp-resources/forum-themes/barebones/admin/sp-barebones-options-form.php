 <?php
/*
Simple:Press
Barebones SP Theme Custom Settings Form
$LastChangedDate: 2014-09-12 07:30:12 +0100 (Fri, 12 Sep 2014) $
$Rev: 11958 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_barebones_options_form() {

	if (!SP()->auths->current_user_can('SPF Manage Themes')) {
		SP()->primitives->admin_etext('Access denied - you do not have permission');
		die();
	}

	require_once SPBBADMIN.'sp-barebones-activate.php';
	sp_barebones_setup(true);
?>

<style>
	.color-picker { height: 50px; }
</style>

<script>
	jQuery(document).ready(function($) {
		var colorPickers = $('.color-picker');
		for (e in colorPickers) {
			if (colorPickers[e].id != undefined) {
				var colorPickerID = colorPickers[e].id;
				$('#' + colorPickerID + '-color').farbtastic('#' + colorPickerID);
			}
		}

		$('.fabox').hide();

		$('.color-picker').click(function() {
			$(this).parent().find('.fabox').fadeIn();
		});

		$(document).mousedown(function() {
			$('.fabox').each(function() {
				var display = $(this).css('display');
				if (display == 'block') $(this).fadeOut();
			});
		});
	});

	function spjLoadTestView(url, title) {
		var aWidth = (window.innerWidth-80);
		var aHeight = (window.innerHeight-80);
		spj.dialogAjax(this, url, title, aWidth, aHeight, 'center');
	}
</script>

<?php

	require_once SP_STORE_DIR.'/'.'sp-custom-settings/sp-barebones-test-settings.php';

	spa_paint_options_init();

	spa_paint_open_tab(__('Barebones Custom Theme Settings', 'spBarebones'), true);

		echo '<br /><div class="sfoptionerror" style="font-size: 13px;">';
		$url = wp_nonce_url(SPAJAXURL.'help&amp;file=admin-themes&amp;item=custom-options', 'help');
		echo "<input type='button' value='Help' class='button-primary' style='float:right;' onclick='spj.dialogAjax(this, \"$url\", \"Simple:Press Help\", 600, 0, 0);' />";
		echo "<span style='font-weight:bold';'>";
		SP()->primitives->admin_etext('Before using this customiser we strongly recommend you click on the help button and familiarise yourself with how it works to avoid inadvertently altering your live forum display');
		echo "</span>";
		echo '.<br />';
		echo '</div>';

		echo "</div>";
		echo '<div class="sp-half-form">';

		spa_paint_open_panel();
			spa_paint_open_fieldset('', false, '', false);
?>
			<div>
				<div style="width: 49.5%; float:left;">
					<p>Standard and general unlinked text</p>
					<input id="C1" class="color-picker" type="text" value="<?php echo $ops['C1']; ?>" name="C1" style="width:60%;font-weight:bold;float:left;" />
					<div class="clearleft"></div>
					<div id="C1-color" class="fabox" style="margin: 0px auto; width: 195px; float:left;"></div>
					<div class="clearboth"></div>
				</div>
			</div>
<?php
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset('', false, '', false);
?>
			<div>
				<div style="width: 49.5%; float:left;">
					<p>Main Headings and<br />Footer Background</p>
					<input id="C3" class="color-picker" type="text" value="<?php echo $ops['C3']; ?>" name="C3" style="width:60%;font-weight:bold;float:left;" />
					<div class="clearleft"></div>
					<div id="C3-color" class="fabox" style="margin: 0px auto; width: 195px; float:left;"></div>
				</div>

				<div style="width: 49.5%; float:left;">
					<p>Title rows in<br />index listings</p>
					<input id="C4" class="color-picker" type="text" value="<?php echo $ops['C4']; ?>" name="C4" style="width:60%;font-weight:bold;float:left;" />
					<div class="clearleft"></div>
					<div id="C4-color" class="fabox" style="margin: 0px auto; width: 195px; float:left;"></div>
				</div>
				<div class="clearboth"></div><hr>
			</div>

			<div>
				<div style="width: 49.5%; float:left;">
					<p>Background of odd rows<br />in index listings</p>
					<input id="C2" class="color-picker" type="text" value="<?php echo $ops['C2']; ?>" name="C2" style="width:60%;font-weight:bold;float:left;" />
					<div class="clearleft"></div>
					<div id="C2-color" class="fabox" style="margin: 0px auto; width: 195px; float:left;"></div>
				</div>

				<div style="width: 49.5%; float:right;">
					<p>Background of even rows<br />in index listings</p>
					<input id="C6" class="color-picker" type="text" value="<?php echo $ops['C6']; ?>" name="C6" style="width:60%;font-weight:bold;float:left;" />
					<div class="clearleft"></div>
					<div id="C6-color" class="fabox" style="margin: 0px auto; width: 195px; float:left;"></div>
				</div>
				<div class="clearboth"></div>
			</div>
<?php
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset('', false, '', false);

				spa_paint_input(__('Font Family(s) in CSS format', 'spBarebones'), 'FN', $ops['FN']);
				spa_paint_input(__('Base Font Size (as percentage value)', 'spBarebones'), 'F1', $ops['F1']);

			spa_paint_close_fieldset();
		spa_paint_close_panel();

	spa_paint_tab_right_cell();

		spa_paint_open_panel();
			spa_paint_open_fieldset('', false, '', false);
?>
			<div>
				<div style="width: 49.5%; float:left;">
					<p>Icon Glyphs</p>
					<input id="C7" class="color-picker" type="text" value="<?php echo $ops['C7']; ?>" name="C7" style="width:60%;font-weight:bold; float:left;" />
					<div class="clearleft"></div>
					<div id="C7-color" class="fabox" style="margin: 0px auto; width: 195px; float:left;"></div>
				</div>

				<div style="width: 49.5%; float:right;">
					<p>Icon Glyphs Hover</p>
					<input id="C8" class="color-picker" type="text" value="<?php echo $ops['C8']; ?>" name="C8" style="width:60%;font-weight:bold; float:left;" />
					<div class="clearleft"></div>
					<div id="C8-color" class="fabox" style="margin: 0px auto; width: 195px; float:left;"></div>
				</div>
				<div class="clearboth"></div>
			</div>
<?php
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset('', false, '', false);
?>
			<div>
				<div style="width: 49.5%; float:left;">
					<p>Primary <br />Link text labels</p>
					<input id="C5" class="color-picker" type="text" value="<?php echo $ops['C5']; ?>" name="C5" style="width:60%;font-weight:bold;float:left;" />
					<div class="clearleft"></div>
					<div id="C5-color" class="fabox" style="margin: 0px auto; width: 195px; float:left;"></div>
				</div>
				<div style="width: 49.5%; float:left;">
					<p>Primary <br />Link text hover</p>
					<input id="C9" class="color-picker" type="text" value="<?php echo $ops['C9']; ?>" name="C9" style="width:60%;font-weight:bold;float:left;" />
					<div class="clearleft"></div>
					<div id="C9-color" class="fabox" style="margin: 0px auto; width: 195px; float:left;"></div>
				</div>
				<div class="clearboth"></div><hr>
			</div>

			<div>
				<div style="width: 49.5%; float:left;">
					<p>Secondary <br />Link text labels</p>
					<input id="C10" class="color-picker" type="text" value="<?php echo $ops['C10']; ?>" name="C10" style="width:60%;font-weight:bold;float:left;" />
					<div class="clearleft"></div>
					<div id="C10-color" class="fabox" style="margin: 0px auto; width: 195px; float:left;"></div>
				</div>
				<div style="width: 49.5%; float:left;">
					<p>Secondary <br />Link text hover</p>
					<input id="C11" class="color-picker" type="text" value="<?php echo $ops['C11']; ?>" name="C11" style="width:60%;font-weight:bold;float:left;" />
					<div class="clearleft"></div>
					<div id="C11-color" class="fabox" style="margin: 0px auto; width: 195px; float:left;"></div>
				</div>
				<div class="clearboth"></div>
			</div>
<?php
			spa_paint_close_fieldset();
		spa_paint_close_panel();

	spa_paint_close_container();
}

function sp_barebones_update_bar_custom($bar) {

	$bar = '<input type="submit" name="commit-test" class="button-primary" value="'.__("Update For Test", "spBarebones").'" />';
	$url = SPAJAXURL.'display-forum-custom';
	$title = __("Testing Custom Theme Settings", "spBarebones");
	$bar.= "&nbsp;&nbsp;&nbsp;<input type='button' value='View Test' class='button-primary' id='df' onclick='spjLoadTestView(\"$url\", \"$title\");'/>";
	$bar.= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="commit-save" class="button-primary" value="'.__("Commit Custom Settings", "spBarebones").'" />';
	$bar.= '&nbsp;&nbsp;&nbsp;<input type="submit" name="commit-reset" class="button-primary" style="float:right;" value="'.__("Reset to Defaults", "spBarebones").'" />';

	return $bar;
}
