 <?php
/*
Simple:Press
HTML Email Plugin Admin Subscriptions Digests Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_html_email_do_admin_digests() {
	$option = SP()->options->get('html-email');

	spa_paint_options_init();
	spa_paint_open_tab(__('HTML Emails', 'sp-html-email').' - '.__('Subscription Digest Email', 'sp-html-email'), true);
    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Options', 'sp-html-email'), true, 'html-email-digest');
                spa_paint_checkbox(__('Enable subscription digest emails in HTML', 'sp-html-email'), 'digests', $option['digests']);
                spa_paint_checkbox(__('Use global CSS, header and footer', 'sp-html-email'), 'digests-globals', $option['digests-globals']);
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Format User Email', 'sp-html-email'), true, 'html-email-digests');
				$submessage = '<br /><p><strong>'.__('The following placeholders are available: ', 'sp-html-email').'%BLOGNAME%, %TYPE%</strong></p>';
				spa_paint_wide_textarea(__('Email subject line (no html allowed)', 'sp-html-email'), 'digests-subject', SP()->displayFilters->title($option['digests-subject']), $submessage, 1);
				$submessage = '<br /><p><strong>'.__('The following placeholders are available: ', 'sp-html-email').'%BLOGNAME%, %TYPE%, %PROFILEURL%, %SITEURL%</strong></p>';
				spa_paint_wide_textarea(__('Email header (once at top - html allowed)', 'sp-html-email'), 'digests-header', SP()->editFilters->text($option['digests-header']), $submessage, 6);
				$submessage = '<br /><p><strong>'.__('The following placeholders are available: ', 'sp-html-email').'%BLOGNAME%, %FORUMNAME%, %TOPICNAME%, %POSTURL%, %POSTCONTENT%, %COUNT%, %SITEURL%</strong></p>';
				spa_paint_wide_textarea(__('Email message (once per topic - html allowed)', 'sp-html-email'), 'digests-body', SP()->editFilters->text($option['digests-body']), $submessage, 6);
				$submessage = '<br /><p><strong>'.__('The following placeholders are available: ', 'sp-html-email').'%BLOGNAME%, %TYPE%, %PROFILEURL%, %SITEURL%</strong></p>';
				spa_paint_wide_textarea(__('Email footer (once at bottom - html allowed)', 'sp-html-email'), 'digests-footer', SP()->editFilters->text($option['digests-footer']), $submessage, 6);
			spa_paint_close_fieldset();
		spa_paint_close_panel();
}
