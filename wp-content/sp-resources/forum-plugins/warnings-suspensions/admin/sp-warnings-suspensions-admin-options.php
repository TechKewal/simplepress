<?php
/*
Simple:Press
Warning and Suspensions Plugin Admin Options Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_warnings_suspensions_admin_options_form() {
	$data = SP()->options->get('warnings-suspensions');

	spa_paint_options_init();
	spa_paint_open_tab(__('Warnings and Suspensions', 'sp-warnings-suspensions').' - '.__('Options', 'sp-warnings-suspensions'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Options', 'sp-warnings-suspensions'), true, 'warnings-suspensions-options');
				spa_paint_checkbox(__('Show warning, suspension and ban notification on user profile', 'sp-warnings-suspensions'), 'profile', $data['profile']);
				$values = array(__('Simple Press Notifications', 'sp-suspensions-options'),
                                __('Private Message (must have plugin active)', 'sp-suspensions-options'));
				spa_paint_radiogroup(__('Select method of notification to user', 'sp-warnings-suspensions'), 'notify', $values, $data['notify'], false, true);
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
		echo '<div class="sfform-panel-spacer"></div>';
	spa_paint_close_tab();

	spa_paint_open_tab(__('Warnings and Suspensions', 'sp-warnings-suspensions').' - '.__('User Messages', 'sp-warnings-suspensions'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Warning Messages', 'sp-warnings-suspensions'), true, 'warnings-messages');
				spa_paint_wide_textarea(__('Warning title used in notifications to users (if using PM for notification)', 'sp-warnings-suspensions'), 'warn_title', $data['warn_title']);
    			$submessage = __('HTML is allowed in warning message. Within text, use %s where you would like warning expiraton date to show.', 'sp-warnings-suspensions');
    			spa_paint_wide_textarea(__('Warning message used in notifications to users', 'sp-warnings-suspensions'), 'warn_message', SP()->editFilters->text($data['warn_message']), $submessage, 2);
    			$submessage = __('HTML is allowed in warning message. Within text, use %s where you would like warning expiraton date to show.', 'sp-warnings-suspensions');
    			spa_paint_wide_textarea(__('Warning message used on user profile (if enabled)', 'sp-warnings-suspensions'), 'warn_profile', SP()->editFilters->text($data['warn_profile']), $submessage, 2);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Suspension Messages', 'sp-warnings-suspensions'), true, 'suspension-messages');
				spa_paint_wide_textarea(__('Suspension title used in notifications to users (if using PM for notification)', 'sp-warnings-suspensions'), 'suspension_title', $data['suspension_title']);
    			$submessage = __('HTML is allowed in suspension message. Within text, use %s where you would like suspension expiraton date to show.', 'sp-warnings-suspensions');
    			spa_paint_wide_textarea(__('Suspension message used in notifications to users', 'sp-warnings-suspensions'), 'suspension_message', SP()->editFilters->text($data['suspension_message']), $submessage, 2);
    			$submessage = __('HTML is allowed in suspension message. Within text, use %s where you would like suspension expiraton date to show.', 'sp-warnings-suspensions');
    			spa_paint_wide_textarea(__('Suspension message used on user profile (if enabled)', 'sp-warnings-suspensions'), 'suspension_profile', SP()->editFilters->text($data['suspension_profile']), $submessage, 2);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Ban Messages', 'sp-warnings-suspensions'), true, 'ban-messages');
				spa_paint_wide_textarea(__('Ban title used in notifications to users (if using PM for notification)', 'sp-warnings-suspensions'), 'ban_title', $data['ban_title']);
    			$submessage = __('HTML is allowed in ban message', 'sp-warnings-suspensions');
    			spa_paint_wide_textarea(__('Ban message used in notifications to users', 'sp-warnings-suspensions'), 'ban_message', SP()->editFilters->text($data['ban_message']), $submessage, 2);
    			$submessage = __('HTML is allowed in ban message. Within text, use %s where you would like suspension expiraton date to show.', 'sp-warnings-suspensions');
    			spa_paint_wide_textarea(__('Ban message used on user profile (if enabled)', 'sp-warnings-suspensions'), 'ban_profile', SP()->editFilters->text($data['ban_profile']), $submessage, 2);
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
}
