<?php
/*
Simple:Press
Polls Plugin Admin Options Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_logger_admin_options_form() {
    if (!SP()->auths->current_user_can('SPF Manage Logger')) die();

	$logger = SP()->options->get('logger');

	spa_paint_options_init();
	spa_paint_open_tab(__('Event Logger', 'sp-logger').' - '.__('Options', 'sp-logger'));
    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Options', 'sp-logger'), true, 'logger-options');
				spa_paint_input(__('How many entries to store in DB (0 = no limit)', 'sp-logger'), 'logentries', $logger['logentries']);
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();

    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Core Events Being Logged', 'sp-logger'), true, 'logger-events');
				spa_paint_checkbox(__('Post edited', 'sp-logger'), 'postedited', $logger['postedited']);
				spa_paint_checkbox(__('Topic edited', 'sp-logger'), 'topicedited', $logger['topicedited']);
				spa_paint_checkbox(__('Post deleted', 'sp-logger'), 'postdeleted', $logger['postdeleted']);
				spa_paint_checkbox(__('Topic deleted', 'sp-logger'), 'topicdeleted', $logger['topicdeleted']);
				spa_paint_checkbox(__('Post moved', 'sp-logger'), 'postmoved', $logger['postmoved']);
				spa_paint_checkbox(__('Topic moved', 'sp-logger'), 'topicmoved', $logger['topicmoved']);
				spa_paint_checkbox(__('Post Approved', 'sp-logger'), 'postapproved', $logger['postapproved']);
				spa_paint_checkbox(__('Post Unapproved', 'sp-logger'), 'postunapproved', $logger['postunapproved']);
				spa_paint_checkbox(__('Post Reassigned', 'sp-logger'), 'postreassigned', $logger['postreassigned']);
				spa_paint_checkbox(__('Post Created', 'sp-logger'), 'postcreated', $logger['postcreated']);
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();

        do_action('sph_logger_admin_left_panel');
    	spa_paint_tab_right_cell();

    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Add Action Log', 'sp-logger'), true, 'logger-hook');
				spa_paint_input(__('Name of the action to log'), 'hookname', '');
				spa_paint_input(__('Optional callback on action execution'), 'hookcallback', '');
				spa_paint_input(__('Action priority'), 'hookpri', 9999);
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();

    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Actions Being Logged', 'sp-logger'), true, 'logger-actions');
                if (!empty($logger['hooks'])) {
?>
                    <table class="widefat fixed striped spMobileTable800">
                        <thead>
                            <tr>
                                <th style='text-align:center'><?php echo __('Action Name', 'sp-logger'); ?></th>
                                <th style='text-align:center'><?php echo __('Callback', 'sp-logger'); ?></th>
                                <th style='text-align:center'><?php echo __('Priority', 'sp-logger'); ?></th>
                                <th style='text-align:center'><?php echo __('Delete', 'sp-logger'); ?></th>
                            </tr>
                        </thead>

                        <tbody>
<?php
                            foreach ($logger['hooks'] as $index => $hook) {
                                $msg = esc_attr(__('Are you sure you want to delete this action logging?'), 'sp-logger');
                                $site = wp_nonce_url(SPAJAXURL.'logger-manage&amp;targetaction=delete-hook&amp;name='.$hook['name'], 'logger-manage');
?>
                                <tr id='sp-hook-<?php echo $index; ?>' class='spMobileTableData'>
                                    <td data-label='<?php echo __('Action Name', 'sp-logger'); ?>' style='text-align:center'><?php echo $hook['name']; ?></td>
                                    <td data-label='<?php echo __('Callback', 'sp-logger'); ?>' ><?php echo $hook['callback']; ?></td>
                                    <td data-label='<?php echo __('Priority', 'sp-logger'); ?>' style='text-align:center'><?php echo $hook['pri']; ?></td>
                                    <td data-label='<?php echo __('Manage', 'sp-logger'); ?>' style='text-align:center'>
										<span class="spDeleteRow" style="cursor:pointer;" data-msg="<?php echo $msg; ?>" data-url="<?php echo $site; ?>" data-target="sp-hook-<?php echo $index; ?>">
											<?php echo SP()->theme->paint_icon('', SPLOGGERIMAGES, 'sp_LoggerHookDelete.png', __('Delete Action Logging', 'sp-logger')); ?>
										</span>
                                    </td>
                                </tr>
<?php
                        }
?>
                        </tbody>
                    </table>
<?php
                } else {
                    echo '<p>'.__('No actions being logged', 'sp-logger').'</p>';
                }
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();

    	do_action('sph_logger_admin_right_panel');
	spa_paint_close_tab();
}
