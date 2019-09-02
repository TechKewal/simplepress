<?php
/*
Simple:Press
Private Posts Plugin Admin Options Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_private_posts_do_admin_options() {
	$options = SP()->options->get('private-posts');

	spa_paint_open_panel();
		spa_paint_open_fieldset(__('Private Posts Options', 'sp-private-posts'), true, 'private-posts');
			spa_paint_textarea(__('Message to display for posts that have been marked as private', 'sp-private-posts'), 'private-text', SP()->editFilters->text($options['private-text']), '', 6);
   			$values = array(__('Delete private posts', 'sp-private-posts'), __('Set private post content to blank', 'sp-private-posts'));
   			spa_paint_radiogroup(__('Select how to handle private posts when uninstalling', 'sp-private-posts'), 'private_parts_uninstall', $values, $options['uninstall'], false, true);
		spa_paint_close_fieldset();
	spa_paint_close_panel();
}
