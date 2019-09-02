<?php
/*
Simple:Press
Subscriptions Plugin Admin Members Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_subscriptions_admin_members_form() {
	$subs = array();
	$subs = SP()->options->get('subscriptions');

	spa_paint_options_init();

	spa_paint_open_tab(__('Components', 'sp-subs').' - '.__('Subscriptions', 'sp-subs'));
    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Options', 'sp-subs'), true, 'subscriptions-options');
    			echo '<br /><div class="sfoptionerror">';
    			_e('Warning: auto subscribing members is the same as opt out (vs opt in) and is considered bad practice.  If you enable this option, be sure of what you are doing and consider a disclaimer or notice to your users so they know their options.  This option will affect the default setting for new users.', 'sp-subs');
    			echo '</div><br />';
    			spa_paint_checkbox(__('Auto subscribe members to <b>all</b> topics they post in', 'sp-subs'), 'autosub', $subs['autosub']);
    			echo '<br /><div class="sfoptionerror">';
    			_e('Warning: Allowing members to subscribe to forums may put a heavy strain on your server if its a busy forum.', 'sp-subs');
    			echo '</div><br />';
    			spa_paint_checkbox(__('Allow members to subscribe to forums in addition to topics', 'sp-subs'), 'forumsubs', $subs['forumsubs']);
    			spa_paint_checkbox(__('For forum subscriptions, default subscriptions to only new topics', 'sp-subs'), 'defnewtopics', $subs['defnewtopics']);
    			spa_paint_checkbox(__('Include post content in standard subscription emails', 'sp-subs'), 'includepost', $subs['includepost']);
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();

	   spa_paint_tab_right_cell();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Subscription Digests', 'sp-subs'), true, 'subscriptions-digest');
    			spa_paint_checkbox(__('Enable subscription digests', 'sp-subs'), 'digestsub', $subs['digestsub']);
				$values = array(__('Daily digest', 'sp-subs'), __('Weekly digest'));
				spa_paint_radiogroup(__('Frequency of digest (if enabled)', 'sp-subs'), 'digesttype', $values, $subs['digesttype'], false, true);
    			spa_paint_checkbox(__('Force digest emails (if enabled)', 'sp-subs'), 'digestforce', $subs['digestforce']);
    			spa_paint_checkbox(__('Include post content in digest emails', 'sp-subs'), 'digestcontent', $subs['digestcontent']);
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
}
