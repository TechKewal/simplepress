 <?php
/*
Simple:Press
Profanity Filter Plugin Admin Options Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_report_post_admin_options_form()
{
	$option = SP()->options->get('report-post');
	$email_list = SP()->editFilters->text($option['email-list']);

	spa_paint_open_panel();
		spa_paint_open_fieldset(__('Report Post', 'sp-report'), true, 'report-post');
			$submessage=__('Enter a comma separated list of email addresses to receive a reported post.', 'sp-report');
			spa_paint_textarea(__('Report post email address list', 'sp-report'), 'email-list', $email_list, $submessage);
		spa_paint_close_fieldset();
	spa_paint_close_panel();
}
