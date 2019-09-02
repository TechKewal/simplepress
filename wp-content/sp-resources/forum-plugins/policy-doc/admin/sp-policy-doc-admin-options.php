 <?php
/*
Simple:Press
Policy Docs Plugin Admin Options Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_policy_doc_admin_options_form() {
	$policy = SP()->options->get('policy-doc');

	$doc = SP()->meta->get('registration', 'policy');
	$policy['regpolicy'] = '';
	if (!empty($doc[0])) $policy['regpolicy'] = SP()->editFilters->text($doc[0]['meta_value']);

	$doc = SP()->meta->get('privacy', 'policy');
	$policy['privpolicy'] = '';
	if (!empty($doc[0])) $policy['privpolicy'] = SP()->editFilters->text($doc[0]['meta_value']);

	spa_paint_options_init();

	spa_paint_open_tab(__('Components', 'sp-policy').' - '.__('Policy Documents', 'sp-policy'), true);
			spa_paint_open_panel();
				spa_paint_open_fieldset(__('Registration Policy', 'sp-policy'), false);
					spa_paint_checkbox(__('Show registration policy on registration form', 'sp-policy'), 'regform', $policy['regform']);
					spa_paint_checkbox(__('Force policy acceptance on registration (checkbox)', 'sp-policy'), 'regcheck', $policy['regcheck']);
				spa_paint_close_fieldset();
			spa_paint_close_panel();

			spa_paint_open_panel();
				spa_paint_open_fieldset(__('Registration Policy Statement', 'sp-policy'), true, 'registration-policy');
					spa_paint_input(__('Optional policy text file name', 'sp-policy'), 'regfile', $policy['regfile']);
					$submessage = __('If not using a text file you can use the area below for your statement:', 'sp-policy');
					$submessage.= '<br />'.__('Enter the text of the site registration policy for display (and optional acceptance) prior to the user registration form being displayed.', 'sp-policy');
					spa_paint_wide_textarea(__('Policy statement', 'sp-policy'), 'regpolicy', $policy['regpolicy'], $submessage, 5);
				spa_paint_close_fieldset();
			spa_paint_close_panel();

			spa_paint_open_panel();
				spa_paint_open_fieldset(__('Privacy Policy Statement', 'sp-policy'), true, 'privacy-policy');
					spa_paint_input(__('Optional policy text file name', 'sp-policy'), 'privfile', $policy['privfile']);
					$submessage = __('If not using a text file you can use the area below for your statement:', 'sp-policy');
					$submessage.= '<br />'.__('Enter the text of the site privacy policy for display.', 'sp-policy');
					spa_paint_wide_textarea(__('Policy statement', 'sp-policy'), 'privpolicy', $policy['privpolicy'], $submessage, 5);
				spa_paint_close_fieldset();
			spa_paint_close_panel();

			$sfconfig = SP()->options->get('sfconfig');
			echo '<p><b>&nbsp;&nbsp;&nbsp;&nbsp;'.__('Based on your storage location settings, policy text files must be located at', 'sp-policy').':<br />&nbsp;&nbsp;&nbsp;&nbsp;'.SP_STORE_DIR.'/'.$sfconfig['policies'].'</b></p>';
		spa_paint_close_container();
}
