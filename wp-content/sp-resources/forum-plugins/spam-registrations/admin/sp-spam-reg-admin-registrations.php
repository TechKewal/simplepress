 <?php
/*
Simple:Press
Remove Spam Registraion Admin Spam Registrations Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_spam_reg_admin_registrations_form() {
	spa_paint_options_init();

		spa_paint_open_tab(__('Users', 'sp-spam').' - '.__('Spam Registrations', 'sp-spam'), true);
			spa_paint_open_panel();
				spa_paint_open_fieldset(__('Remove Spam Registrations', 'sp-spam'), 'true', 'remove-spam-registrations');
?>
					<div class="sfoptionerror">
					<p class='subhead'><?php _e('This option should be used with great care!', 'sp-spam'); ?><br />
					<?php _e('It will delete or optionally move to a designated User Group, user registrations that meet the following criteria', 'sp-spam') ?>:</p>
					</div><br />
<?php
					spa_paint_input(__('All users who registered longer ago than (days)', 'sp-spam'), 'sfdays', 30);
					spa_paint_input(__('Maximum users to process at one time (a maximum of 100 will be imposed)', 'sp-spam'), 'maxusers', 50);

					$values = array(__('Never visited the forums', 'sp-spam'), __('Visited but never posted in the forums', 'sp-spam'));
					spa_paint_radiogroup(__('Who have', 'sp-spam'), 'sfvisit', $values, 1, false, false);
?>
					<ul>
						<li><?php _e('where the user has never authored a blog post', 'sp-spam') ?></li>
						<li><?php _e('where the user has never left a comment to a blog post', 'sp-spam') ?></li>
					</ul>

					<p class='subhead'><?php _e('Note:  You will be given a chance to review the users and/or make changes before the deletion occurs.', 'sp-spam') ?><br /><br /></p>
<?php
                    $base = wp_nonce_url(SPAJAXURL.'spam-reg', 'spam-reg');
					$image = SPADMINIMAGES.'sp_WaitBox.gif';
?>
					<input type='button' class='button button-highlighted spSpamRegShow' value='<?php echo esc_attr(__('Show Spam Registrations', 'sp-spam')); ?>' data-url='<?php echo $base; ?>' data-img='<?php echo $image; ?>' />

					<div class='sfform-panel-spacer'></div>
<?php
				spa_paint_close_fieldset();
			spa_paint_close_panel();
		spa_paint_close_container();
?>
	<div class='sfform-panel-spacer'></div>
	<div id='spam-reg' class='sfinline-form'></div>
<?php
	spa_paint_close_tab();
}
