 <?php
/*
Simple:Press
Reputation System plugin reset save routine
$LastChangedDate: 2017-12-28 11:38:04 -0600 (Thu, 28 Dec 2017) $
$Rev: 15602 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_reputation_do_admin_reset() {
    if (!SP()->auths->current_user_can('SPF Manage Reputation')) die();
?>
<script>
	(function(spj, $, undefined) {
		spj.loadAjaxForm('resetreputation', 'reputation-reset');
		spj.loadAjaxForm('userreputation', 'reputation-reset');
	}(window.spj = window.spj || {}, jQuery));
</script>
<?php
    $ajaxURL = wp_nonce_url(SPAJAXURL.'plugins-loader&amp;saveform=plugin&amp;func=sp_reputation_admin_save_reset', 'plugins-loader');
?>
	<form action='<?php echo $ajaxURL; ?>' method='post' id='resetreputation' name='resetreputation'>
	<?php echo sp_create_nonce('forum-adminform_userplugin'); ?>
<?php
	spa_paint_options_init();
	spa_paint_open_tab(__('Reputation System', 'sp-reputation').' - '.__('Reset', 'sp-reputation'));
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Reputation Reset', 'sp-reputation'), true, 'reputation-reset');
				spa_paint_checkbox(__('Reset ALL user reputation levels and user ratings', 'sp-reputation'), 'resetlevels', false);
				spa_paint_input(__('Amount of reputation to start each user with', 'sp-reputation'), 'newlevel', 0);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

    	spa_paint_tab_right_cell();

    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Options Reset', 'sp-reputation'), true, 'options-reset');
    			spa_paint_checkbox(__('Reset all options/levels to default', 'sp-reputation'), 'resetoptions', false);
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();

    	spa_paint_close_container();

        $base = wp_nonce_url(SPAJAXURL.'reputation-manage', 'reputation-manage');
    	$image = SPADMINIMAGES.'sp_WaitBox.gif';
?>
    	<div class='sfform-panel-spacer'></div>
    	<input type='button' class='button button-primary spLayerToggle' style='margin-left:20px' value='<?php echo esc_attr(__('Reset Reputation System', 'sp-reputation')); ?>' data-target="reputation-confirm" />
    	<div class='sfform-panel-spacer'></div>
    	<div id='reputation-confirm' class='sfhidden'>
			<div style='margin-left:20px'>
<?php
        		echo '<p>';
        		echo __('Warning! You are about to reset the reputation system.', 'sp-reputation');
        		echo '</p>';
        		echo '<p>';
        		echo sprintf(__('Please note that this action %s can NOT be reversed %s.', 'sp-reputation'), '<strong>', '</strong>');
        		echo '</p>';
        		echo '<p>';
        		echo __('Click on the Confirm Reset button below to proceed.', 'sp-reputation');
        		echo '</p>';
?>
    			<input type='submit' class='button button-primary' id='resetreputation' name='resetreputation' value='<?php esc_attr_e(__('Confirm Reset', 'sp-reputation')); ?>' />
    			<input type='button' class='button button-primary spLayerToggle' data-target='reputation-confirm' id='sfresetcancel' name='sfresetcancel' value='<?php esc_attr_e(__('Cancel', 'sp-reputation')); ?>' />
			</div>
        	<div class='sfform-panel-spacer'></div>
        </div>
<?php
    spa_paint_close_tab();
?>
	</form>

   	<div class='sfform-panel-spacer'></div>
<?php
    $site = SPAJAXURL.'reputation-manage&targetaction=user-search&rand='.rand();
    $ajaxURL = wp_nonce_url(SPAJAXURL.'plugins-loader&amp;saveform=plugin&amp;func=sp_reputation_admin_save_reset', 'plugins-loader');
?>
	<form action='<?php echo $ajaxURL; ?>' method='post' id='userreputation' name='userreputation'>
        <?php echo sp_create_nonce('forum-adminform_userplugin'); ?>
<?php
?>
        <script>
			(function(spj, $, undefined) {
				$(document).ready(function() {
					$('#reputation_user').autocomplete({
						source : '<?php echo $site; ?>',
						disabled : false,
						delay : 200,
						minLength: 1,
					});
				});
			}(window.spj = window.spj || {}, jQuery));
        </script>
<?php
    	spa_paint_open_tab(__('Reputation System', 'sp-reputation').' - '.__('Reset', 'sp-reputation'));
    		spa_paint_open_panel();
    			spa_paint_open_fieldset(__('User Reputation Reset', 'sp-reputation'), true, 'user-reputation');
?>
                    <div class="sp-form-row">
                        <div class="wp-core-ui sflabel sp-label-60">
                            <?php echo __("User to reset reputation (start typing for autocomplete)", 'sp-reputation'); ?>
                        </div>
                        <input type='text' class='wp-core-ui sp-input-40' id='reputation_user' name='reputation_user' />
                        <div class="clearboth"></div>
                    </div>
<?php
    				spa_paint_input(__('Amount of reputation to set user to', 'sp-reputation'), 'newrep', 0);
    			spa_paint_close_fieldset();
    		spa_paint_close_panel();
        	spa_paint_close_container();
?>
        	<div class='sfform-panel-spacer'></div>
            <input type='submit' class='button button-primary' style='margin-left:20px' id='userreputation' name='userreputation' value='<?php esc_attr_e(__('Set User Reputation', 'sp-reputation')); ?>' />
        	<div class='sfform-panel-spacer'></div>
<?php
        spa_paint_close_tab();
?>
	</form>
<?php
}
