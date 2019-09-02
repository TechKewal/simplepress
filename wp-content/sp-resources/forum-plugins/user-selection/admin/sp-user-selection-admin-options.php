<?php
/*
Simple:Press
User Selection Plugin Admin Options Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_user_selection_admin_options_form() {
	$sfstorage = SP()->options->get('sfconfig');
    $data = SP()->options->get('user-selection');

	spa_paint_options_init();
	spa_paint_open_tab(__('Language Files', 'sp-usel'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Language Options', 'sp-usel'), true, 'language-options');
				spa_paint_checkbox(__('Display default English as language option', "sp-usel"), 'usedefault', $data['usedefault']);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Language File Naming', 'sp-usel'), true, 'language-names');
                $langs = get_available_languages(SP_STORE_DIR.'/'.$sfstorage['language-sp']);
    			if ($data['usedefault']) $langs[] = 'en';
    			sort($langs);
?>
				<table class="widefat fixed spMobileTable800" style="padding:0;border-spacing:0;border-collapse:separate">
                    <thead>
					<tr>
						<th style="text-align:center;"><?php _e('Translation File', 'sp-usel'); ?></th>
						<th style="text-align:center;"><?php _e('Display Name', 'sp-usel'); ?></th>
					</tr>
                    </thead>

                    <tbody>
<?php
                    if ($langs) {
						foreach ($langs as $lang) {
							if (substr($lang, 0, 4) == 'spa-') continue;
?>
							<tr class='spMobileTableData'>
<?php
                                $thislang = SP()->saveFilters->filename($lang);
                                $name = (!empty($data['names'][$lang]) ? SP()->displayFilters->title($data['names'][$lang]) : '');
?>
								<td data-label="<?php echo __('Translation', 'sp-usel'); ?>" style="text-align:center;"><?php echo $thislang; ?></td>
								<td data-label="<?php echo __('Name', 'sp-usel'); ?>" style="text-align:center;">
       	                            <input type="text" name="names[<?php echo $thislang; ?>]" value="<?php echo $name; ?>" />
                                </td>
							</tr>
<?php
                        }
                    } else {
?>
						<tr>
							<td colspan="2"><?php _e('There are no language translations available.', 'sp-usel'); ?></td>
						</tr>
                    <?php }?>
                </tbody>
				</table>
<?php
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
}
