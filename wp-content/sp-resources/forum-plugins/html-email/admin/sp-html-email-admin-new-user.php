 <?php
/*
Simple:Press
HTML Email Plugin Admin New User Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_html_email_admin_new_user_form() {
	$option = SP()->options->get('html-email');

	spa_paint_options_init();
	spa_paint_open_tab(__('HTML Emails', 'sp-html-email').' - '.__('New User Email', 'sp-html-email'), true);
    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Options', 'sp-html-email'), true, 'html-email-new-user');
                spa_paint_checkbox(__('Enable new user emails in HTML', 'sp-html-email'), 'new-users', $option['new-users']);
                spa_paint_checkbox(__('Use global CSS, header and footer', 'sp-html-email'), 'new-users-globals', $option['new-users-globals']);
                echo '<br /><div class="sfoptionerror">'.__('Please note, enabling this will override new user email settings on Forum - Options - Email Settings', 'sp-html-email').'</div><br />';
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Format User Email', 'sp-html-email'), true, 'html-email-new-users');
				$submessage = '<br /><p><strong>'.__('The following placeholders are available: ', 'sp-html-email').'%USERNAME%, %BLOGNAME%</strong></p>';
				spa_paint_wide_textarea(__('Email subject line (no html allowed)', 'sp-html-email'), 'new-user-subject', SP()->displayFilters->title($option['new-user-subject']), $submessage, 1);
				$submessage = '<br /><p><strong>'.__('The following placeholders are available: ', 'sp-html-email').'%USERNAME%, %BLOGNAME%, %SITEURL%, %LOGINURL%, %PWURL%</strong></p>';
				spa_paint_wide_textarea(__('Email message (html allowed)', 'sp-html-email'), 'new-user-body', SP()->editFilters->text($option['new-user-body']), $submessage, 6);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Format Admin Email', 'sp-html-email'), true, 'html-email-new-users-admin');
				$submessage = '<br /><p><strong>'.__('The following placeholders are available: ', 'sp-html-email').'%USERNAME%, %BLOGNAME%</strong></p>';
				spa_paint_wide_textarea(__('Email subject line (no html allowed)', 'sp-html-email'), 'new-user-admin-subject', SP()->displayFilters->title($option['new-user-admin-subject']), $submessage, 1);
				$submessage = '<br /><p><strong>'.__('The following placeholders are available: ', 'sp-html-email').'%USERNAME%, %USEREMAIL%, %USERIP%, %BLOGNAME%</strong></p>';
				spa_paint_wide_textarea(__('Email message (html allowed)', 'sp-html-email'), 'new-user-admin-body', SP()->editFilters->text($option['new-user-admin-body']), $submessage, 6);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_close_container();
}
