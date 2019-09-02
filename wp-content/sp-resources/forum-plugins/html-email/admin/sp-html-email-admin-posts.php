 <?php
/*
Simple:Press
HTML Email Plugin Admin Notifications Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_html_email_admin_posts_form() {
	$option = SP()->options->get('html-email');

	spa_paint_options_init();
	spa_paint_open_tab(__('HTML Emails', 'sp-html-email').' - '.__('Admin New Post Notifications', 'sp-html-email'), true);
    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Options', 'sp-html-email'), true, 'html-email-admin-notification');
                spa_paint_checkbox(__('Enable admin notification emails in HTML', 'sp-html-email'), 'admin-notifications', $option['admin-notifications']);
                spa_paint_checkbox(__('Use global CSS, header and footer', 'sp-html-email'), 'admin-notifications-globals', $option['admin-notifications-globals']);
                spa_paint_input(__('Text to use when post is awaiting moderation'), 'admin-notification-modtext', $option['admin-notification-modtext']);
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Format Email', 'sp-html-email'), true, 'html-email-admin-notifications');
				$submessage = '<br /><p><strong>'.__('The following placeholders are available: ', 'sp-html-email').'%BLOGNAME%, %TOPICNAME%, %POSTERNAME%</strong></p>';
				spa_paint_wide_textarea(__('Email subject line (no html allowed)', 'sp-html-email'), 'admin-notification-subject', SP()->displayFilters->title($option['admin-notification-subject']), $submessage, 1);
				$submessage = '<br /><p><strong>'.__('The following placeholders are available: ', 'sp-html-email').'%BLOGNAME%, %GROUPNAME%, %FORUMNAME%, %TOPICNAME%, %POSTERNAME%, %POSTEREMAIL%, %POSTEREIP%, %POSTURL%, %POSTCONTENT%, %MODERATIONTEXT%</strong></p>';
				spa_paint_wide_textarea(__('Email message (html allowed)', 'sp-html-email'), 'admin-notification-body', SP()->editFilters->text($option['admin-notification-body']), $submessage, 10);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_close_container();
}
