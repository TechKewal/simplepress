 <?php
/*
Simple:Press
HTML Email Plugin Admin Report Post Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_html_email_do_admin_report() {
	$option = SP()->options->get('html-email');

	spa_paint_options_init();
	spa_paint_open_tab(__('HTML Emails', 'sp-html-email').' - '.__('Report Post Email', 'sp-html-email'), true);
    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Options', 'sp-html-email'), true, 'html-email-report');
                spa_paint_checkbox(__('Enable report post emails in HTML', 'sp-html-email'), 'report', $option['report']);
                spa_paint_checkbox(__('Use global CSS, header and footer', 'sp-html-email'), 'report-globals', $option['report-globals']);
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Format User Email', 'sp-html-email'), true, 'html-email-reports');
				$submessage = '<br /><p><strong>'.__('The following placeholders are available: ', 'sp-html-email').'%BLOGNAME%, %REPORTER%</strong></p>';
				spa_paint_wide_textarea(__('Email subject line (no html allowed)', 'sp-html-email'), 'report-subject', SP()->displayFilters->title($option['report-subject']), $submessage, 1);
				$submessage = '<br /><p><strong>'.__('The following placeholders are available: ', 'sp-html-email').'%BLOGNAME%, %GROUPNAME%, %FORUMNAME%, %TOPICNAME%, %POSTURL%, %POSTCONTENT%, %POSTREPORT%, %REPORTER%, %SITEURL%</strong></p>';
				spa_paint_wide_textarea(__('Email message (html allowed)', 'sp-html-email'), 'report-body', SP()->editFilters->text($option['report-body']), $submessage, 6);
			spa_paint_close_fieldset();
		spa_paint_close_panel();
}
