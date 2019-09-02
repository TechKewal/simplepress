<?php
/*
Simple:Press
Membership Subscribe Plugin Admin Options Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_membership_subscribe_admin_options_form() {
	$subs = SP()->options->get('subscriptions');
    if (!$subs['forumsubs']) return;

	spa_paint_open_panel();
		spa_paint_open_fieldset(__('Auto Subscribe Membership to Forum', 'sp-membership-subscribe'), true, 'membership_subscribe');
?>
			<table class="form-table">
				<tr>
				<td class="sflabel">
				<input type="checkbox" id="sfmembership_subscribe" name="membership_subscribe" />
				<label for="sfmembership_subscribe"><?php echo SP()->primitives->admin_text('Auto subscribe selected user group to this forum when adding permission'); ?></label>
				</td>
				</tr>
			</table>
<?php
		spa_paint_close_fieldset();
	spa_paint_close_panel();
}
