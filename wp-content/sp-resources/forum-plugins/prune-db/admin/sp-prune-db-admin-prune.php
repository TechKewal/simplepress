<?php
/*
Simple:Press
Prune Database Plugin Admin Prune Form
$LastChangedDate: 2017-12-28 11:38:04 -0600 (Thu, 28 Dec 2017) $
$Rev: 15602 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_prune_db_admin_prune_form() {

?>
<script>
	(function(spj, $, undefined) {
		spj.loadAjaxForm('sfprunetopics', 'sfreloaddb');
	}(window.spj = window.spj || {}, jQuery));
</script>
<?php
	$topicdata = sp_prune_db_prepare_filter();

	spa_paint_options_init();
	spa_paint_open_tab(__('Toolbox', 'sp-prune').' - '.__('Prune Database - Delete Topics', 'sp-prune'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Prune Database Topics', 'sp-prune'), 'true', 'select-topics-to-prune');
				if ($topicdata['message'] != '') {
					echo $topicdata['message'];
				} else {
					# grab the topics that meet the filter critera
					$date = $topicdata['date'];
					$forum_id = $topicdata['id'];
					$sql = "SELECT * FROM ".SPTOPICS."
							JOIN ".SPPOSTS." ON ".SPTOPICS.".post_id = ".SPPOSTS.".post_id
							WHERE ".SPPOSTS.".post_date <= '".$date."'".$forum_id.
							" ORDER BY ".SPPOSTS.".post_date, ".SPTOPICS.".forum_id ASC";
					$topics = SP()->DB->select($sql);

					# display the list of topics if any met the criteria
					if ($topics) {
?>
							<h4><?php _e('Select topics to prune', 'sp-prune') ?></h4>
							<div class='sfform-panel-spacer'></div>
							<div id='checkboxset'>
								<table class='sfsubtable' style="padding:0;border-spacing:0;border-collapse:separate">
									<tr>
										<th style='width:5%;text-align:right'><?php _e('Delete', 'sp-prune') ?></th>
										<th style='width:5%;text-align:left'><?php _e('Topic ID', 'sp-prune') ?></th>
										<th style='width:20%;text-align:center'><?php _e('Topic Date', 'sp-prune') ?></th>
										<th style='width:20%;text-align:center'><?php _e('Forum', 'sp-prune') ?></th>
										<th><?php _e('Topic Title', 'sp-prune') ?></th>
									</tr>
<?php
									$tcount = 0;
									foreach ($topics as $topic) {
?>
										<tr>
											<td class='sflabel' style='text-align:center' colspan='2'>
												<input type='checkbox' id='sftopic<?php echo $tcount; ?>' name='topic<?php echo $tcount; ?>' value='<?php echo $topic->forum_id.':'.$topic->topic_id; ?>' />
												<label for='sftopic<?php echo $tcount; ?>'><?php echo $topic->topic_id; ?></label>
											</td>
											<td style='text-align:center'><?php echo SP()->dateTime->format_date('d', $topic->topic_date); ?></td>
											<td>
<?php
												$forum_name = SP()->DB->table(SPFORUMS, 'forum_id='.$topic->forum_id, 'forum_name');
												echo SP()->displayFilters->title($forum_name);
?>
											</td>
											<td><?php echo SP()->displayFilters->title($topic->topic_name); ?></td>
										</tr>
<?php
										$tcount++;
									}
?>
								</table>
								<input type='hidden' name='tcount' value='<?php echo $tcount; ?>' />
							</div>
							<br />
							<table>
								<tr>
									<td><input type='button' class='button button-highlighted spPruneCheckAll' value='<?php esc_attr_e(__('Check All', 'sp-prune')); ?>' data-target='#checkboxset' /></td>
									<td></td>
									<td><input type='button' class='button button-highlighted spPruneUncheckAll' value='<?php esc_attr_e(__('Uncheck All', 'sp-prune')); ?>' data-target='#checkboxset' /></td>
								</tr>
							</table>
							<div class='clearboth'></div>
							<div class='sfform-panel-spacer'></div>
<?php
					} else {
			    		_e('No topics found using the specified filter criteria.', 'sp-prune');
					}
				}
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
}
