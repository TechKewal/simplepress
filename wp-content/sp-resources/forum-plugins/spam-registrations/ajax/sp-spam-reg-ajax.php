<?php
/*
Simple:Press
Policy Docs plugin ajax routine for management functions
$LastChangedDate: 2018-08-05 15:22:16 -0500 (Sun, 05 Aug 2018) $
$Rev: 15688 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

spa_admin_ajax_support();

if (!sp_nonce('spam-reg')) die();

require_once SP_PLUGIN_DIR.'/admin/library/spa-tab-support.php';

# Check Whether User Can Manage Toolbox
if (!SP()->auths->current_user_can('SPF Manage Toolbox')) die();

?>
	<script>
		(function(spj, $, undefined) {
			$(document).ready(function() {
				spj.loadAjaxForm('sfspamreg', 'spamreg');
				$('#sfmaincontainer').trigger('adminformloaded');
			});
		}(window.spj = window.spj || {}, jQuery));
    </script>
<?php
    $ajaxURL = wp_nonce_url(SPAJAXURL.'plugins-loader&amp;saveform=plugin&amp;func=sp_spam_reg_admin_list_save', 'plugins-loader');
?>
	<form action='<?php echo $ajaxURL; ?>' method='post' id='sfspamreg' name='sfspamreg'>
	<?php echo sp_create_nonce('forum-adminform_userplugin'); ?>
<?php
	spa_paint_options_init();

		spa_paint_open_tab(__('Users', 'sp-spam').' - '.__('Spam Registrations List', 'sp-spam'), true);
			spa_paint_open_panel();
				spa_paint_open_fieldset(__('Current Spam Registrations', 'sp-spam'), false);
?>
					<div id='spam-reg'>
						<table style='width:auto;text-align:center;padding:0;border-spacing: 0;border-collapse:separate;' class='sfsubtable'>
							<tr>
								<th scope='col' style='text-align:center;padding:5px 5px;'><?php _e('User ID', 'sp-spam'); ?></th>
								<th style='padding:5px 5px;'><?php _e('User Name', 'sp-spam'); ?></th>
								<th style='text-align:center;padding:5px 5px;'><?php _e('Delete', 'sp-spam'); ?></th>
								<th style='text-align:center;width:30px'></th>
								<th scope='col' style='text-align:center;padding:5px 5px;'><?php _e('User ID', 'sp-spam'); ?></th>
								<th style='padding:5px 5px;'><?php _e('User Name', 'sp-spam'); ?></th>
								<th style='text-align:center;padding:5px 5px;'><?php _e('Delete', 'sp-spam'); ?></th>
								<th style='text-align:center;width:30px'></th>
								<th scope='col' style='text-align:center;padding:5px 5px;'><?php _e('User ID', 'sp-spam'); ?></th>
								<th style='padding:5px 5px;'><?php _e('User Name', 'sp-spam'); ?></th>
								<th style='text-align:center;padding:5px 5px;'><?php _e('Delete', 'sp-spam'); ?></th>
							</tr>
<?php
							# first out select users registered more than X days ago
							$numspam = 0;
							$days = isset($_GET['days']) ? max(SP()->filters->integer($_GET['days']), 0) : 7;
							$status = ($_GET['visit'] == 'true') ? '-1' : '0';
							$limit = ($_GET['max'] > 0) ? $_GET['max'] : '50';
							If (empty($limit) || $limit > 100) $limit = 100;
							# lets get the data
							$sql = "SELECT user_id, ".SPMEMBERS.".display_name, posts
									FROM ".SPMEMBERS."
									JOIN ".SPUSERS." ON ".SPMEMBERS.".user_id = ".SPUSERS.".ID
									WHERE user_registered < DATE_SUB(CURDATE(), INTERVAL ".$days." DAY)
									AND posts = ".$status."
									ORDER BY display_name;";
							$registrations = SP()->DB->select($sql);

							echo '<strong>'.__('Total found', 'sp-spam').': '.count($registrations).'</strong><br /><br />';

							$curcol = 0;
							if ($registrations) {
								$cnt = 0;
								foreach ($registrations as $baduser) {
									if($cnt == $limit) break;
									$candelete = true;
									$found = false;

									# have they ever authored a post?
									$found = SP()->DB->table(SPWPPOSTS, 'post_author = '.$baduser->user_id);
									if ($found) {
										$candelete = false;
									} else {
										# if no - what about left a comment?
										$found = SP()->DB->table(SPWPCOMMENTS, 'user_id = '.$baduser->user_id);
										if ($found) $candelete = false;
									}
									# so? can we delete them?
									if ($candelete) {
										# do NOT remove an admin that does not post
										if (!SP()->auths->forum_admin($baduser->user_id)) {
											if ($curcol == 0) echo '<tr>';
?>
											<td style='text-align:center'><?php echo $baduser->user_id; ?></td>
											<td><?php echo SP()->displayFilters->name($baduser->display_name); ?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
											<td style='text-align:center'>
											<input type='checkbox' name='kill[<?php echo $baduser->user_id; ?>]' id='sfkill-<?php echo $baduser->user_id; ?>' checked='checked' />
											<label for='sfkill-<?php echo $baduser->user_id; ?>'></label>
											</td>
<?php
											$curcol++;
											if ($curcol == 3) {
												$curcol = 0;
												echo '</tr>';
											} else {
												echo '<td></td>';
											}
											$numspam++;
											$cnt++;
										}
									}
								}
							}

							if ($curcol != 0) {
								if ($curcol == 1) echo '<td></td>';
								echo '<td></td></tr>';
							}
						echo '</table>';
						echo '</div>';
						echo '<br />';

						if ($numspam != 0) {
?>
							<table>
							<tr>
							<td>
							<input type='button' class='button button-highlighted spPruneCheckAll' value='<?php esc_attr_e(__('Check All', 'sp-spam')); ?>' data-target='#spam-reg' />
							</td>
							<td />
							<td>
							<input type='button' class='button button-highlighted spPruneUncheckAll' value='<?php esc_attr_e(__('Uncheck All', 'sp-spam')); ?>' data-target='#spam-reg' />
							</td>
							</tr>
							</table>
<?php
						}
						echo '<br />';
						echo '<p>'.$numspam.__(' registered users eligible for removal', 'sp-spam').'</p><br />';
						if ($numspam > 0 && count($registrations) > $numspam) {
							echo '<p>'.__('There are more registrants who can be removed - after removal please repeat the process', 'sp-spam').'</p><br />';
						} else {
							echo '<br />';
						}
					    if ($numspam != 0) echo '<p><strong>'.__('Warning: This cannot be reversed! Use at your own risk!', 'sp-spam').'</strong></p>';

					echo '</div>';
				spa_paint_close_fieldset();
			spa_paint_close_panel();

			if ($numspam != 0) {
				spa_paint_open_panel();
				spa_paint_open_fieldset(__('Retain and Move', 'sp-spam'), false);
					if ($numspam != 0) echo '<p>'.__('Instead of complete removal - select a forum User Group to move these users into', 'sp-spam').'</p>';
					spa_display_usergroup_select();
				spa_paint_close_fieldset();
				spa_paint_close_panel();
			}

            spa_paint_close_container();

    		if ($numspam != 0) {
	?>
    			<div class='sfform-submit-bar'>
    			<input type='submit' class='button-primary' id='sfspamreg' name='sfspamreg' value='<?php esc_attr_e(__('Remove Spam Registrations', 'sp-spam')); ?>' />
    			<input type='button' class='button-primary spSpamCancel' data-target='#spam-reg' id='sfspamregcancel' name='sfspamregcancel' value='<?php esc_attr_e(__('Cancel', 'sp-spam')); ?>' />
    			</div>
	<?php
                spa_paint_close_tab();
            }
?>
	</form>
	<div class='sfform-panel-spacer'></div>
<?php

die();
