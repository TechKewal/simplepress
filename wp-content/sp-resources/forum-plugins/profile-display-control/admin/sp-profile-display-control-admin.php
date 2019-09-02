<?php
/*
Simple:Press
Profile Display Control Plugin Admin Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_profile_display_control_admin_form() {
	spa_paint_open_tab(__('Profiles', 'sp-pdc').' - '.__('Display Control', 'sp-pdc'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Display Control Settings', 'sp-pdc'), true, 'profile-display-control');
            	$pdc = SP()->options->get('profile-display-control');
                if ($pdc) {
                    echo '<p>'.__('Turn off or on Profile edit form components as desired', 'sp-pdc').'</p>';
                    foreach ($pdc as $key => $option) {
        				spa_paint_checkbox($option['title'], $key, __($option['display'], 'sp-pdc'));
                    }
                }
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
}
