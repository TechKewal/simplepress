<?php
/*
Simple:Press
slack integration Plugin Admin Options Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_slack_admin_options_form() {
	$options = SP()->options->get('slack');

	spa_paint_options_init();
	spa_paint_open_tab(__('Slack Integration Plugin', 'sp-alack'));
    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Slack Integration Options', 'sp-slack'), true, 'slack');
				spa_paint_input(__('Incoming WebHooks URL', 'sp-slack'), 'slack-weburl', SP()->displayFilters->title($options['slack-weburl']), false, true);
				spa_paint_input(__('Channel', 'sp-slack'), 'slack-channel', SP()->displayFilters->title($options['slack-channel']));
				spa_paint_input(__('Username', 'sp-slack'), 'slack-name', SP()->displayFilters->title($options['slack-name']));
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();

       	spa_paint_tab_right_cell();

    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Slack Integration Notifications', 'sp-slack'), true, 'slack-notify');
                spa_paint_checkbox(__('Enable new post notifications to Slack', 'sp-slack'), 'notifynewpost', $options['notifynewpost']);
                spa_paint_checkbox(__('Enable new user registrations notifications to Slack', 'sp-slack'), 'notifynewuser', $options['notifynewuser']);
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();
		spa_paint_close_container();
}
