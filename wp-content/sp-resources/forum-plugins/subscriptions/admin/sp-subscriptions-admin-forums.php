<?php
/*
Simple:Press
Subscriptions plugin admin forum routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

require_once SLIBDIR.'sp-subscriptions-components.php';
require_once SLIBDIR.'sp-subscriptions-database.php';

function sp_subscriptions_admin_forums_form() {
	spa_paint_options_init();
	echo '<div id="subsdisplayspot">';
	sp_subscriptions_render_forum_subscriptions();
    echo '</div>';
}
