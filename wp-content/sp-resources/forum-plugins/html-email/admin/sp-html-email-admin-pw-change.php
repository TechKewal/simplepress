 <?php
/*
Simple:Press
HTML Email Plugin Admin PW Change Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_html_email_do_admin_pw_change() {
	$option = SP()->options->get('html-email');

	spa_paint_options_init();
	spa_paint_open_tab(__('HTML Emails', 'sp-html-email').' - '.__('Password Changed', 'sp-html-email'), true);
    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Options', 'sp-html-email'), true, 'html-email-pw-change');
                spa_paint_checkbox(__('Enable password changed emails in HTML', 'sp-html-email'), 'pw-change', $option['pw-change']);
                spa_paint_checkbox(__('Use global CSS, header and footer', 'sp-html-email'), 'pw-change-globals', $option['pw-change-globals']);
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Format User Email', 'sp-html-email'), true, 'html-email-pw-changes');
				$submessage = '<br /><p><strong>'.__('The following placeholders are available: ', 'sp-html-email').'%USERNAME%, %BLOGNAME%</strong></p>';
				spa_paint_wide_textarea(__('Email subject line (no html allowed)', 'sp-html-email'), 'pw-change-subject', SP()->displayFilters->title($option['pw-change-subject']), $submessage, 1);
				$submessage = '<br /><p><strong>'.__('The following placeholders are available: ', 'sp-html-email').'%USERNAME%, %USEREMAIL%, %USERIP%, %BLOGNAME%, %SITEURL%, %RESETURL%</strong></p>';
				spa_paint_wide_textarea(__('Email message (html allowed)', 'sp-html-email'), 'pw-change-body', SP()->editFilters->text($option['pw-change-body']), $submessage, 10);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Format Admin Email', 'sp-html-email'), true, 'html-email-pw-changes-admin');
				$submessage = '<br /><p><strong>'.__('The following placeholders are available: ', 'sp-html-email').'%USERNAME%, %BLOGNAME%</strong></p>';
				spa_paint_wide_textarea(__('Email subject line (no html allowed)', 'sp-html-email'), 'pw-change-admin-subject', SP()->displayFilters->title($option['pw-change-admin-subject']), $submessage, 1);
				$submessage = '<br /><p><strong>'.__('The following placeholders are available: ', 'sp-html-email').'%USERNAME%, %USEREMAIL%, %USERIP%, %BLOGNAME%</strong></p>';
				spa_paint_wide_textarea(__('Email message (html allowed)', 'sp-html-email'), 'pw-change-admin-body', SP()->editFilters->text($option['pw-change-admin-body']), $submessage, 6);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_close_container();
}
