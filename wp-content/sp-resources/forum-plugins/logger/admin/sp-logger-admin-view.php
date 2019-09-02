<?php
/*
Simple:Press
Event Logger Plugin Admin Options Form
$LastChangedDate: 2017-12-28 11:38:04 -0600 (Thu, 28 Dec 2017) $
$Rev: 15602 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_logger_admin_view_form() {
	global $wpdb;

	if (!SP()->auths->current_user_can('SPF Manage Logger')) die();
?>
    <style>
        .spEventLog {overflow:auto;text-align:left;white-space:pre-wrap;white-space:-moz-pre-wrap;white-space:-pre-wrap;white-space:-o-pre-wrap;word-wrap:break-word;}
    </style>
<?php

	spa_paint_options_init();
	spa_paint_open_tab(__('Event Log', 'sp-logger').' - '.__('Event Log', 'sp-logger'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Event Log', 'sp-logger'), false);
				$logs = SP()->DB->table(SPEVENTLOG);
				if ($logs) {
					$ajaxURL = wp_nonce_url(SPAJAXURL.'logger-manage&amp;targetaction=clearlog', 'logger-manage');
					$msg = '<p>'.__('There no events/actions currently logged in the database', 'sp-logger').'</p>'
?>
					<div id="spEventLogList">
    					<p style="font-weight:bold;font-size:14px;padding:10px;"><?php echo __('Number of log entries', 'sp-logger').': '.count($logs); ?></p>
    					<input type="submit" class="button-primary spLoggerClearLog" id="saveit" name="sfclearlog" value="<?php echo __('Clear event/action log'); ?>" style="margin:0px 10px 15px;" data-url="<?php echo $ajaxURL; ?>" />

    					<table class="widefat fixed striped spMobileTable800">
    						<thead>
    							<tr>
    								<th style='text-align:center; width:10%'><?php echo __('Log ID', 'sp-logger'); ?></th>
    								<th style='text-align:center; width:10%'><?php echo __('Event/Action', 'sp-logger'); ?></th>
    								<th style='text-align:center; width:20%'><?php echo __('Date', 'sp-logger'); ?></th>
    								<th style='text-align:center; width:60%'><?php echo __('Data', 'sp-logger'); ?></th>
    							</tr>
    						</thead>

    						<tbody>
<?php
							foreach ($logs as $log) {
?>
								<tr class='spMobileTableData'>
									<td data-label='<?php echo __('Log ID', 'sp-logger'); ?>' style='text-align:center; width:10%'><?php echo $log->log_id; ?></td>
									<td data-label='<?php echo __('Event/Action', 'sp-logger'); ?>' style='text-align:center; width:10%'><?php echo $log->log_event; ?></td>
									<td data-label='<?php echo __('Date', 'sp-logger'); ?>' style='text-align:center; width:20%'><?php echo $log->log_date; ?></td>
									<td data-label='<?php echo __('Data', 'sp-logger'); ?>' style='text-align:center; width:60%'>
										<div class="spEventLog">
<?php
											$data = wp_unslash(maybe_unserialize($log->log_data));
											echo sp_logger_array2table($data, 0, 0);
?>
										</div>
									</td>
								</tr>
<?php
							}
?>
    						</tbody>
    					</table>

    					<p style="font-weight:bold;font-size:14px;padding:10px;"><?php echo __('Number of log entries', 'sp-logger').': '.count($logs); ?></p>
    					<input type="submit" class="button-primary spLoggerClearLog" id="saveit2" name="sfclearlog2" value="<?php echo __('Clear event/action log'); ?>" style="margin:0px 10px 15px;" data-url="<?php echo $ajaxURL; ?>" />
					</div>
<?php
				} else {
					echo '<p>'.__('There are no events/actions currently logged in the database', 'sp-logger').'</p>';
				}
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
		echo '<div class="sfform-panel-spacer"></div>';
	spa_paint_close_tab();
}

function sp_logger_do_offset($level) {
    $offset = '';
    for ($i=1; $i<$level; $i++) {
        $offset.= '<td></td>';
    }
    return $offset;
}

function sp_logger_array2table($array, $level, $sub) {
    $array = (array) $array;
    $table = '';
    if (is_array($array) == 1) {
        $table.= '<table class="form-table">';
        foreach ($array as $key => $value) {
            $offset = '';
            if (is_array($value) || is_object($value)) {
                $table.= '<tr>';
                $offset = sp_logger_do_offset($level);
                $table.= $offset."<td colspan='2' style='vertical-align:middle'><strong>$key</strong></td>";
                $table.= '</tr>';
                $table.= '<tr>';
                $table.= '<td>'.sp_logger_array2table($value, $level + 1, 0).'</td>';
            } else {
                if ($sub != 1) {
                    $table.= '<tr>';
                    $offset = sp_logger_do_offset($level);
                }
                $sub = 0;
                $table.= $offset."<td style='vertical-align:middle'>$key</td><td style='vertical-align:middle'>$value</td>";
                $table.= '</tr>';
            }
        }
        $table.= '</table>';
    }
    return $table;
}
