 <?php
/*
Simple:Press
HTML Email Plugin Admin New PM Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_html_email_do_admin_newpm() {
	$option = SP()->options->get('html-email');

	spa_paint_options_init();
	spa_paint_open_tab(__('HTML Emails', 'sp-html-email').' - '.__('New PM Email', 'sp-html-email'), true);
    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Options', 'sp-html-email'), true, 'html-email-newpm');
                spa_paint_checkbox(__('Enable new private message emails in HTML', 'sp-html-email'), 'newpm', $option['newpm']);
                spa_paint_checkbox(__('Use global CSS, header and footer', 'sp-html-email'), 'newpm-globals', $option['newpm-globals']);
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Format User Email', 'sp-html-email'), true, 'html-email-newpms');
				$submessage = '<br /><p><strong>'.__('The following placeholders are available: ', 'sp-html-email').'%BLOGNAME%, %SENDER%</strong></p>';
				spa_paint_wide_textarea(__('Email subject line (no html allowed)', 'sp-html-email'), 'newpm-subject', SP()->displayFilters->title($option['newpm-subject']), $submessage, 1);
				$submessage = '<br /><p><strong>'.__('The following placeholders are available: ', 'sp-html-email').'%BLOGNAME%, %PMTITLE%, %PMCONTENT%, %INBOXURL%, %SENDER%, %SITEURL%</strong></p>';
				spa_paint_wide_textarea(__('Email message (html allowed)', 'sp-html-email'), 'newpm-body', SP()->editFilters->text($option['newpm-body']), $submessage, 6);
			spa_paint_close_fieldset();
		spa_paint_close_panel();
}
