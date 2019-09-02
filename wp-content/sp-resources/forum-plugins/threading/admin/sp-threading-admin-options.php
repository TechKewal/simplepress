<?php
/*
Simple:Press
Threading Plugin Admin Options Form
$LastChangedDate: 2013-02-17 20:33:06 +0000 (Sun, 17 Feb 2013) $
$Rev: 9858 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_threading_admin_options_form() {
	$thread = SP()->options->get('threading');

	spa_paint_open_panel();
		spa_paint_open_fieldset(__('Threading', 'sp-threading'), true, 'threading');
?>
		<div class="sp-form-row">
		<div class="wp-core-ui sflabel sp-label-60">
		<?php _e('Maximum threading level (default of 5)', 'sp-threading'); ?>:</div>
		<input type="number" min="2" max="10" value="5" name="maxlevel" tabindex="130" class="wp-core-ui sp-input-40">
		<div class="clearboth"></div></div>
<?php
		spa_paint_close_fieldset();
	spa_paint_close_panel();
}
