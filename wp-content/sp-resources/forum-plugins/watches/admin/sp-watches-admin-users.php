<?php
/*
Simple:Press
Watches plugin admin user watches routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

require_once WLIBDIR.'sp-watches-components.php';
require_once WLIBDIR.'sp-watches-database.php';

function sp_watches_admin_users_form() {
	spa_paint_options_init();
	spa_paint_open_tab(__('Users', 'sp-watches').' - '.__('User Watches', 'sp-watches') ,true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('User Watches', 'sp-watches'), 'true', 'watches-users');
        		echo '<div id="watchesdisplayspot">';
                    sp_watches_render_user_watches();
                echo '</div>';
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
    spa_paint_close_tab();
}
