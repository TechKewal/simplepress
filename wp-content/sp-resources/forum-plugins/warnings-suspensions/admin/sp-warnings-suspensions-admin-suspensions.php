<?php
/*
Simple:Press
Warning and Suspensions Plugin Suspensions Form
$LastChangedDate: 2017-12-28 11:38:04 -0600 (Thu, 28 Dec 2017) $
$Rev: 15602 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_warnings_suspensions_admin_suspensions() {
	spa_paint_options_init();

    $ajaxURL = wp_nonce_url(SPAJAXURL.'plugins-loader&amp;saveform=plugin&amp;func=sp_warnings_suspensions_suspensions_save', 'plugins-loader');
   	echo "<form action='$ajaxURL' method='post' name='spaddsuspension' id='spaddsuspension'>";
   	echo sp_create_nonce('forum-adminform_userplugin');

	spa_paint_open_tab(__('Warnings and Suspensions', 'sp-warnings-suspensions').' - '.__('Add New Suspension', 'sp-warnings-suspensions'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Add New Suspension', 'sp-warnings-suspensions'), true, 'add-suspensions');
                define('SPWARNAUTOCOMP', SPAJAXURL.'warnings-suspensions-admin&rand='.rand());
?>
                <script>
					(function(spj, $, undefined) {
						$(document).ready(function() {
							spj.loadAjaxForm('spaddsuspension', 'add-suspension');

							$('#suspenduser').autocomplete({
								source : '<?php echo SPWARNAUTOCOMP; ?>',
								disabled : false,
								delay : 200,
								minLength: 1,
								select: function(event, ui){
									$('#suspenduser').autocomplete('close');
								}
							});

							$("#suspendexpire").datepicker({
								beforeShow: function(input, inst) {
									$("#ui-datepicker-div").addClass("sp-warnings-dp");
								},
								changeMonth: true,
								changeYear: true,
								dateFormat: "MM dd, yy",
								minDate: 0,
							});
						});
					}(window.spj = window.spj || {}, jQuery));
                </script>

                <div class="sp-form-row">
                    <div class="sflabel sp-label-60"><?php echo __('User to suspend', 'sp-warnings-suspensions'); ?>:</div>
                    <input class="sp-input-40" type="text" value="" id="suspenduser" name="suspenduser" tabindex="100" />
                    <div class="clearboth"></div>
                </div>
                <div class="sp-form-row">
                    <div class="sflabel sp-label-60"><?php echo __('Warning expiration', 'sp-warnings-suspensions'); ?>:</div>
                    <input class="sp-input-40" type="text" value="" id="suspendexpire" name="suspendexpire" tabindex="100" />
                    <div class="clearboth"></div>
                </div>
                <div class="sp-form-row">
                    <?php spa_display_usergroup_select(); ?>
                </div>
<?php
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
?>
	<div class='sfform-submit-bar'>
		<input type='submit' class='button-primary' id='spSuspendAdd' name='spSuspendAdd' value='<?php esc_attr_e(__('Add Suspension', 'sp-warnings-suspensions')); ?>' />
	</div>
    <?php	spa_paint_close_tab(); ?>
    </form>
	<div class="sfform-panel-spacer"></div>
<?php
	spa_paint_open_tab(__('Warnings and Suspensions', 'sp-warnings-suspensions').' - '.__('Current Suspensions', 'sp-warnings-suspensions'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Current Suspensions', 'sp-warnings-suspensions'), true, 'suspension-list');
?>
                <table class="widefat fixed striped spMobileTable800">
                    <thead>
                        <tr>
                            <th style='text-align:center'><?php echo __('Suspension ID', 'sp-warnings-suspensions'); ?></th>
                            <th style='text-align:center'><?php echo __('User ID', 'sp-warnings-suspensions'); ?></th>
                            <th style='text-align:center'><?php echo __('User Display Name', 'sp-warnings-suspensions'); ?></th>
                            <th style='text-align:center'><?php echo __('Suspension Expiration', 'sp-warnings-suspensions'); ?></th>
                            <th style='text-align:center'><?php echo __('Suspension Usergroup', 'sp-warnings-suspensions'); ?></th>
                            <th style='text-align:center'><?php echo __('Saved Usergroups', 'sp-warnings-suspensions'); ?></th>
                            <th style='text-align:center'><?php echo __('Manage', 'sp-warnings-suspensions'); ?></th>
                        </tr>
                    </thead>

                    <tbody>
<?php
                    $suspensions = SP()->DB->select('SELECT * FROM '.SPWARNINGS.' WHERE warn_type='.SPWARNSUSPENSION);
                    if ($suspensions) {
                        foreach ($suspensions as $suspension) {
?>
                            <tr id='sp_suspend<?php echo $suspension->warn_id; ?>' class='spMobileTableData'>
                                <td data-label='<?php echo __('ID', 'sp-warnings-suspensions'); ?>' style='text-align:center'><?php echo $suspension->warn_id; ?></td>
                                <td data-label='<?php echo __('User ID', 'sp-warnings-suspensions'); ?>' style='text-align:center'><?php echo $suspension->user_id; ?></td>
                                <td data-label='<?php echo __('Name', 'sp-warnings-suspensions'); ?>'><?php echo $suspension->display_name; ?></td>
                                <td data-label='<?php echo __('Expiration', 'sp-warnings-suspensions'); ?>' style='text-align:center'><?php echo date('F j, Y', strtotime($suspension->expiration)); ?></td>
                                <td data-label='<?php echo __('Usergroup', 'sp-warnings-suspensions'); ?>' style='text-align:center'><?php echo $suspension->usergroup; ?></td>
<?php
                                    $saved = '';
                                    $memberships = unserialize($suspension->saved_memberships);
                                    if ($memberships) {
                                        foreach ($memberships as $membership) {
                                            if (!empty($saved)) $saved.= ', ';
                                            $saved.= $membership['name'];
                                        }
                                    }
?>
                                <td data-label='<?php echo __('Saved', 'sp-warnings-suspensions'); ?>' style='text-align:center'><?php echo $saved; ?></td>
                                <td data-label='<?php echo __('Manage', 'sp-warnings-suspensions'); ?>' style='text-align:center'>
<?php
                                    $msg = esc_attr(__('Are you sure you want to remove this suspension?'), 'sp-warnings-suspensions');
                                    $ajaxUrl = wp_nonce_url(SPAJAXURL."warnings-suspensions-admin&amp;targetaction=delsuspension&amp;wid=$suspension->warn_id&amp;uid=$suspension->user_id", 'warnings-suspensions-admin');
?>
									<a class="spDeleteRow" data-msg="<?php echo $msg; ?>" data-url="<?php echo $ajaxUrl; ?>" data-target="sp_suspend<?php echo $suspension->warn_id; ?>">
                                        <img src="<?php echo SPWARNIMAGES; ?>sp_WarningsDelete.png" title="<?php _e("Remove Suspension", 'sp-warnings-suspensions'); ?>" alt=""/>
                                    </a>
                                </td>
                            </tr>
<?php
                        }
                    } else {
                        echo '<tr><td colspan="7"><p>'.__('There are not currently any suspensions in effect', 'sp-warnings-suspensions').'</p></td></tr>';
                    }
?>
                    </tbody>
                </table>
<?php
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		echo '<div class="sfform-panel-spacer"></div>';
		spa_paint_close_container();
	spa_paint_close_tab();
}
