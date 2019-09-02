<?php
/*
Simple:Press
PM Plugin Admin Options Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_pm_admin_options_form() {
    if (!SP()->auths->current_user_can('SPF Manage PM')) die();

	$pmdata = SP()->options->get('pm');
	$pmdata['sched'] = wp_get_schedule('sph_pm_cron');

	spa_paint_options_init();
	spa_paint_open_tab(__('Private Messaging', 'sp-pm').' - '.__('Private Messaging', 'sp-pm'));
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Private Messaging Options', 'sp-pm'), true, 'private-messaging');
				spa_paint_checkbox(__('Enable sending of email for PMs (If enabled, users can elect to receive or not in their profile)', 'sp-pm'), 'email', $pmdata['email']);
				spa_paint_input(__('Maximum inbox size (0 = no limit)', 'sp-pm'), 'max', $pmdata['max']);
				spa_paint_input(__('How many threads to list per page', 'sp-pm'), 'threadpaging', $pmdata['threadpaging']);
				spa_paint_input(__('How many messages to list per page', 'sp-pm'), 'messagepaging', $pmdata['messagepaging']);
				spa_paint_checkbox(__('Allow use of file uploads in PMs (if using file uploader plugin)', 'sp-pm'), 'uploads', $pmdata['uploads']);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Private Messaging User Access', 'sp-pm'), true, 'pm-access');
				spa_paint_input(__('Number of forum posts needed before PM access granted', 'sp-pm'), 'accessposts', $pmdata['accessposts']);
					$msg = __('Set to zero for no initial restriction', 'sp-pm');
					echo '<tr><td class="message" colspan="2" style="line-height:2em;">('.$msg.')</td></tr>';
			spa_paint_close_fieldset();
		spa_paint_close_panel();

	spa_paint_tab_right_cell();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Private Messaging Addressing Options', 'sp-pm'), true, 'pm-addressing');
				spa_paint_input(__('Maximum number of recipients (0=No Limit)', 'sp-pm'), 'maxrecipients', $pmdata['maxrecipients']);
				spa_paint_checkbox(__('Allow use of the Cc field', 'sp-pm'), 'cc', $pmdata['cc']);
				spa_paint_checkbox(__('Allow use of the Bcc field', 'sp-pm'), 'bcc', $pmdata['bcc']);
				spa_paint_checkbox(__('Only allow sending of PMs from Send PM button on posts.  You will not be able to address PMs from the PM compose panel.', 'sp-pm'), 'limitedsend', $pmdata['limitedsend']);
				spa_paint_checkbox(__('Only allow sending of PMs to users in the same usergroup.', 'sp-pm'), 'limitedug', $pmdata['limitedug']);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Private Messages Auto Removal', 'sp-pm'), true, 'pm-removal');
				spa_paint_checkbox(__("Enable auto removal of user's private messages", 'sp-pm'), 'remove', $pmdata['remove']);
				spa_paint_input(__('Maximum number of <b>days</b> to keep private messages (if auto removal enabled)', 'sp-pm'), 'keep', $pmdata['keep']);
				if ($pmdata['sched']) {
					$msg = __('Private messages auto removal cron job is scheduled to run daily.', 'sp-pm');
					echo '<tr><td class="message" colspan="2" style="line-height:2em;">&nbsp;<u>'.$msg.'</u></td></tr>';
				}
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
}
