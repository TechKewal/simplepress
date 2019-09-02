<?php
/*
Simple:Press
Subscriptions plugin admin user routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

require_once SLIBDIR.'sp-subscriptions-components.php';
require_once SLIBDIR.'sp-subscriptions-database.php';

function sp_subscriptions_admin_digest_form() {
	spa_paint_options_init();
	spa_paint_open_tab(__('Users', 'sp-subs').' - '.__('Digest Subscriptions By User', 'sp-subs'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('User Digest Subscriptions', 'sp-subs'), 'true', 'subscriptions-digest');
        		echo '<div id="subsdisplayspot">';
				sp_subscriptions_render_digest_subscriptions();
                echo '</div>';
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
	spa_paint_close_tab();
}
