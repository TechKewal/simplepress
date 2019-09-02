 <?php
/*
Simple:Press
Moderation Email Plugin Admin Options Form
$LastChangedDate: 2013-02-17 12:33:06 -0800 (Sun, 17 Feb 2013) $
$Rev: 9858 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_moderation_email_admin_options_form() {
	$options = SP()->options->get('moderation-email');

	spa_paint_open_panel();
		spa_paint_open_fieldset(__('Moderation Emails', 'sp-moderation-email'), true, 'moderation-email');
			spa_paint_checkbox(__('Send Emails to users when their posts are approved from moderation', 'sp-moderation-email'), 'modemail', $options['modemail']);
			echo '<p><strong>'.__('The following placeholders are available: %USERNAME%, %BLOGNAME%, %SITEURL%, %POSTURL%, %POSTDATE%', 'sp-moderation-email').'</strong></p>';
			spa_paint_input(__('Email subject line', 'sp-moderation-email'), 'modemailsubject', SP()->displayFilters->title($options['modemailsubject']), false, true);
			spa_paint_wide_textarea(__('Email message (no html)', 'sp-moderation-email'), 'modemailtext', SP()->displayFilters->title($options['modemailtext']), $submessage = '', 4);
		spa_paint_close_fieldset();
	spa_paint_close_panel();
}
