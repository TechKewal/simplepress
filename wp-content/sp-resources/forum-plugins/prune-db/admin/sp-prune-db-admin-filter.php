 <?php
/*
Simple:Press
Prune Database Plugin Admin Filter Form
$LastChangedDate: 2018-11-13 20:42:11 -0600 (Tue, 13 Nov 2018) $
$Rev: 15818 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_prune_db_admin_filter_form() {
	$url = admin_url('admin.php?page='.SP_FOLDER_NAME.'/admin/panel-toolbox/spa-toolbox.php&tab=plugin&admin=sp_prune_db_admin_prune&save=sp_prune_db_admin_select&form=1&reload=spprune');
?>
	<form action='<?php echo $url; ?>' method='post' id='sffiltertopics' name='sffiltertopics'>
	<?php echo sp_create_nonce('forum-adminform_filtertopics'); ?>
<?php
	spa_paint_options_init();

	spa_paint_open_tab(__('Toolbox', 'sp-prune').' - '.__('Prune Database - Filter Topics', 'sp-prune'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Filter Database Topics', 'sp-prune'), 'true', 'select-topic-filter-date');

			# make sure we have some groups/forums/topics in order to be able to prune
			$groups = sp_prune_db_get_database();
			if ($groups) {
?>
					<table style='width:100%;padding:2px;border-spacing:3px;border-collapse:separate;border:0'>
						<tr>
							<td style='vertical-align:top;width:20%'>
								<fieldset style='background:#eeeeee;' class='sffieldset'><legend><?php _e('Select topic filter date', 'sp-prune') ?></legend>
								    <!-- Display a popup calendar for pruning date entry -->
									<p style='text-align:center'>
									<input name='date' id='cal' type='text' class='sfpostcontrol' size='15' value='<?php echo date('M d Y'); ?>' />
									<a href='javascript:NewCssCal("cal", "MMMddyyyy");'>
										<img src='<?php echo PDBIMAGES; ?>sp_Calendar.png' width='32' height='32' style='border:0' alt='Pick a Filter Date' />
									</a>
									</p>
									<p><?php _e('Select topic filter date above.', 'sp-prune'); ?></p>
									<p><?php _e('All topics prior to the date selected above will be available for pruning. If no date is specified, todays date will be used.', 'sp-prune') ?></p>
								</fieldset>
							</td>
							<td></td>
						</tr>
						<tr>
							<td colspan='2' style='vertical-align:top'>
								<div class='sfform-panel-spacer'></div>
								<fieldset style='width:95%' class='sffieldset'><legend><?php _e('Select group(s) / forum(s) to prune', 'sp-prune') ?></legend>
									<?php echo spa_paint_help('select-group-forum-to-prune', 'admin-toolbox'); ?>
<?php
									$gcount = 0;
									foreach ($groups as $group) {
										# display separate fieldset for each group and forum within that group
?>
										<fieldset style='margin-left:15px;width:95%' class='sffieldset'><legend><?php echo SP()->displayFilters->title($group['group_name']); ?></legend><br />
										<div id='container<?php echo $group['group_id']; ?>'>
										<table class='sfsubtable' style="padding:0;border-spacing:0;border-collapse:separate">
										  	<tr>
										  		<th style='width:5%;text-align:center'><?php _e('Filter', 'sp-prune') ?></th>
										  		<th><?php _e('Forum Name', 'sp-prune') ?></th>
										  		<th style='width:10%;text-align:center'><?php _e('Topic Count', 'sp-prune') ?></th>
										  		<th style='width:20%;text-align:center'><?php _e('Earliest Topic', 'sp-prune') ?></th>
										  		<th style='width:20%;text-align:center'><?php _e('Latest Topic', 'sp-prune') ?></th>
										  	</tr>
<?php
										if ($group['forums']) {
											$fcount = 0;
											foreach($group['forums'] as $forum) {
												$id = 'group'.$gcount.'forum';
?>
												<tr>
													<td class='sflabel' colspan='2'>
														<input type='checkbox' name='<?php echo $id.$fcount; ?>' id='sf<?php echo $id.$fcount; ?>' value='<?php echo $forum['forum_id']; ?>' />
														<label for='sf<?php echo $id.$fcount; ?>'><?php echo SP()->displayFilters->title($forum['forum_name']); ?></label>
													</td>
													<td style='text-align:center'>
														<?php echo $forum['topic_count']; ?>
													</td>
													<td style='text-align:center'>
<?php
														$date = SP()->DB->table(SPTOPICS, 'forum_id='.$forum['forum_id'], 'topic_date', 'topic_date ASC', '1');
														echo SP()->dateTime->format_date('d', $date);
?>
													</td>
													<td style='text-align:center'>
<?php
														$date = SP()->DB->table(SPTOPICS, 'forum_id='.$forum['forum_id'], 'topic_date', 'topic_date DESC', '1');
														echo SP()->dateTime->format_date('d', $date);
?>
													</td>
												</tr>
<?php
												$fcount++;
											}
?>
											</table>
											</div>
<?php
											$checkcontainer = '#container'.$group['group_id'];
											echo '<br />';
?>
											<table>
											<tr>
											<td>
											<input type='button' class='button button-highlighted spPruneCheckAll' value='<?php esc_attr_e(__('Check All', 'sp-prune')); ?>' data-target='<?php echo $checkcontainer; ?>' />
											</td>
											<td></td>
											<td>
											<input type='button' class='button button-highlighted spPruneUncheckAll' value='<?php esc_attr_e(__('Uncheck All', 'sp-prune')); ?>' data-target='<?php echo $checkcontainer; ?>' />
											</td>
											</tr>
											</table>
<?php
										}
?>
										<input type='hidden' name='fcount[]' value='<?php echo $fcount; ?>' />
										</fieldset>
<?php
										$gcount++;
									}
?>
									<p><?php _e('<strong>Warning:</strong>  The filtering process can be cpu intensive.  It is recommended to select a minimal number of forums (based on number of posts) to filter at once, especially if you are on shared hosting.', 'sp-prune'); ?></p>
								</fieldset>
							</td>
						</tr>
					</table>
<?php
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
?>
			<div class='sfform-submit-bar'>
			<input type='hidden' name='gcount' value='<?php echo $gcount; ?>' />
			<input type='submit' class='button-primary' id='saveit' name='saveit' value='<?php esc_attr_e(__('Filter Topics', 'sp-prune')); ?>' />
			</div>
<?php
			} else {
				_e('There is nothing to prune as there are no topics.', 'sp-prune');
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
			}
	spa_paint_close_tab();
?>
	</form>
<?php
}
