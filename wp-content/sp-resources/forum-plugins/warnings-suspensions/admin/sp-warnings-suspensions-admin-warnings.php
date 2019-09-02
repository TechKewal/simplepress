<?php
/*
Simple:Press
Warning and Suspensions Plugin Warnings Form
$LastChangedDate: 2017-12-28 11:38:04 -0600 (Thu, 28 Dec 2017) $
$Rev: 15602 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_warnings_suspensions_admin_warnings() {
	spa_paint_options_init();

    $ajaxURL = wp_nonce_url(SPAJAXURL.'plugins-loader&amp;saveform=plugin&amp;func=sp_warnings_suspensions_warnings_save', 'plugins-loader');
   	echo "<form action='$ajaxURL' method='post' name='spaddwarn' id='spaddwarn'>";
   	echo sp_create_nonce('forum-adminform_userplugin');

	spa_paint_open_tab(__('Warnings and Suspensions', 'sp-warnings-suspensions').' - '.__('Add New Warning', 'sp-warnings-suspensions'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Add New Warning', 'sp-warnings-suspensions'), true, 'add-warnings');
                define('SPWARNAUTOCOMP', SPAJAXURL.'warnings-suspensions-admin&rand='.rand());
?>
                <script>
					(function(spj, $, undefined) {
						$(document).ready(function() {
							spj.loadAjaxForm('spaddwarn', 'add-warning');

							$('#warnuser').autocomplete({
								source : '<?php echo SPWARNAUTOCOMP; ?>',
								disabled : false,
								delay : 200,
								minLength: 1,
								select: function(event, ui){
									$('#warnuser').autocomplete('close');
								}
							});

							$("#warnexpire").datepicker({
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
                    <div class="sflabel sp-label-60"><?php echo __('User to warn', 'sp-warnings-suspensions'); ?>:</div>
                    <input class="sp-input-40" type="text" value="" id="warnuser" name="warnuser" tabindex="100" />
                    <div class="clearboth"></div>
                </div>
                <div class="sp-form-row">
                    <div class="sflabel sp-label-60"><?php echo __('Warning expiration', 'sp-warnings-suspensions'); ?>:</div>
                    <input class="sp-input-40" type="text" value="" id="warnexpire" name="warnexpire" tabindex="100" />
                    <div class="clearboth"></div>
                </div>
<?php
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
?>
	<div class='sfform-submit-bar'>
		<input type='submit' class='button-primary' id='spWarnAdd' name='spWarnAdd' value='<?php esc_attr_e(__('Add Warning', 'sp-warnings-suspensions')); ?>' />
	</div>
<?php	spa_paint_close_tab(); ?>
    </form>
	<div class="sfform-panel-spacer"></div>
<?php
	spa_paint_open_tab(__('Warnings and Suspensions', 'sp-warnings-suspensions').' - '.__('Current Warnings', 'sp-warnings-suspensions'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Current Warnings', 'sp-warnings-suspensions'), true, 'warning-list');
?>
                <table class="widefat fixed striped spMobileTable800">
                    <thead>
                        <tr>
                            <th style='text-align:center'><?php echo __('Warning ID', 'sp-warnings-suspensions'); ?></th>
                            <th style='text-align:center'><?php echo __('User ID', 'sp-warnings-suspensions'); ?></th>
                            <th style='text-align:center'><?php echo __('User Display Name', 'sp-warnings-suspensions'); ?></th>
                            <th style='text-align:center'><?php echo __('Warning Expiration', 'sp-warnings-suspensions'); ?></th>
                            <th style='text-align:center'><?php echo __('Manage', 'sp-warnings-suspensions'); ?></th>
                        </tr>
                    </thead>

                    <tbody>
<?php
                    $warnings = SP()->DB->select('SELECT * FROM '.SPWARNINGS.' WHERE warn_type='.SPWARNWARNING);
                    if ($warnings) {
                        foreach ($warnings as $warning) {
?>
                            <tr id='sp_warn<?php echo $warning->warn_id; ?>' class='spMobileTableData'>
                                <td data-label='<?php echo __('ID', 'sp-warnings-suspensions'); ?>' style='text-align:center'><?php echo $warning->warn_id; ?></td>
                                <td data-label='<?php echo __('User ID', 'sp-warnings-suspensions'); ?>' style='text-align:center'><?php echo $warning->user_id; ?></td>
                                <td data-label='<?php echo __('Name', 'sp-warnings-suspensions'); ?>'><?php echo $warning->display_name; ?></td>
                                <td data-label='<?php echo __('Expiration', 'sp-warnings-suspensions'); ?>' style='text-align:center'><?php echo date('F j, Y', strtotime($warning->expiration)); ?></td>
                                <td data-label='<?php echo __('Manage', 'sp-warnings-suspensions'); ?>' style='text-align:center'>
<?php
                                    $msg = esc_attr(__('Are you sure you want to remove this warning?'), 'sp-warnings-suspensions');
                                    $ajaxUrl = wp_nonce_url(SPAJAXURL."warnings-suspensions-admin&amp;targetaction=delwarning&amp;wid=$warning->warn_id", 'warnings-suspensions-admin');
?>
									<a class="spDeleteRow" data-msg="<?php echo $msg; ?>" data-url="<?php echo $ajaxUrl; ?>" data-target="sp_warn<?php echo $warning->warn_id; ?>">
                                        <img src="<?php echo SPWARNIMAGES; ?>sp_WarningsDelete.png" title="<?php _e("Remove Warning", 'sp-warnings-suspensions'); ?>" alt=""/>
                                    </a>
                                </td>
                            </tr>
<?php
                        }
                    } else {
                        echo '<tr><td colspan="5"><p>'.__('There are not currently any warnings in effect', 'sp-warnings-suspensions').'</p></td></tr>';
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
