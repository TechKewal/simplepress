<?php
/*
Simple:Press
Announce Plugin Admin Options Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_announce_admin_options_form() {
	$data = SP()->options->get('announce');

	spa_paint_options_init();
	spa_paint_open_tab(__('Announcements and News', 'sp-announce'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Options', 'sp-announce'), true, 'announce-options');
				$values = array(__('Add before all forum display', 'sp-announce'),
                                __('Add before SP Header', 'sp-announce'),
                                __('Add after SP Header', 'sp-announce'),
                                __('Add before SP Footer', 'sp-announce'),
                                __('Add after SP Footer', 'sp-announce'),
                                __('Add after all forum display', 'sp-announce'),
                                __('Custom - I will use the display template function', 'sp-announce'));
				spa_paint_radiogroup(__('Location to display announcements', 'sp-announce'), 'location', $values, $data['location'], false, true);
				$values = array(__('Show to all users', 'sp-announce'),
                                __('Show only to logged in users', 'sp-announce'),
                                __('Show only to guests', 'sp-announce'));
				spa_paint_radiogroup(__('Who should see the announcements', 'sp-announce'), 'showto', $values, $data['showto'], false, true);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Message', 'sp-announce'), true, 'announce-message');
				$submessage = __('Enter the messge you would like to display in the textarea below.  HTML is allowed.', 'sp-announce');
				spa_paint_wide_textarea(__('Announcement or news message to display', 'sp-announce'), 'message', SP()->editFilters->text($data['message']), $submessage, 10);
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
}
