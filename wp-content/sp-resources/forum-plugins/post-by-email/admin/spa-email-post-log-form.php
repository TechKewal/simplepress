<?php
/*
Simple:Press
Admin Toolbox Post by Email Log Panel
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function spa_emailpost_show_log() {
	$sflog = SP()->DB->table(SFMAILLOG, '', '', 'email_id DESC');

	spa_paint_options_init();
	spa_paint_open_tab(__('Toolbox', 'sp-pbe')." - ".__('Post by Email Log', 'sp-pbe'), true);
		spa_paint_open_panel();
		if (!$sflog) {
			echo '<p>&nbsp;&nbsp;&nbsp;'.__('There are no Email Log Entries', 'sp-pbe').'</p>';
		} else {
			echo '<p>&nbsp;&nbsp;&nbsp;'.__('Note - Only the latest 50 entries are retained', 'sp-pbe').'</p>';

			spa_paint_open_fieldset(__('Post by Email Log', 'sp-pbe'), false);
				echo "<table class='widefat fixed striped spMobileTable1280'>";
                echo '<thead>';
				echo "<tr>";
				echo "<th style='text-align:center;'>".__('Date', 'sp-pbe')."</th>";
				echo "<th style='text-align:center;'>".__('Forum', 'sp-pbe')."</th>";
				echo "<th style='text-align:center;'>".__('Topic', 'sp-pbe')."</th>";
				echo "<th style='text-align:center;'>".__('User', 'sp-pbe')."</th>";
				echo "<th style='text-align:center;'>".__('Status', 'sp-pbe')."</th>";
				echo "</tr>";
                echo '</thead>';

                echo '<tbody>';
				foreach ($sflog as $log) {
					echo "<tr class='spMobileTableData'>";
					echo "<td data-label='".__('Date', 'sp-pbe')."' class='sferror'>".SP()->dateTime->format_date('d', $log->email_date).' - '.SP()->dateTime->format_date('t', $log->email_date)."</td>";
					echo "<td data-label='".__('Forum', 'sp-pbe')."' class='sferror'>".$log->email_forum."</td>";
					echo "<td data-label='".__('Topic', 'sp-pbe')."' class='sferror'>".$log->email_topic."</td>";
					echo "<td data-label='".__('User', 'sp-pbe')."' class='sferror'>".$log->email_user."</td>";
					echo "<td data-label='".__('Status', 'sp-pbe')."' class='sferror'>".$log->email_log."</td>";
					echo "</tr>";
				}
                echo '</tbody>';
				echo '</table>';
			spa_paint_close_fieldset();
		}
		spa_paint_close_panel();
		spa_paint_close_container();
		echo '<div class="sfform-panel-spacer"></div>';
}
