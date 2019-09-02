<?php
/*
Simple:Press
Tags Plugin edit tags routine
$LastChangedDate: 2018-11-13 20:42:11 -0600 (Tue, 13 Nov 2018) $
$Rev: 15818 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_tags_admin_edit_form() {
?>
<script>
	(function(spj, $, undefined) {
		spj.loadAjaxForm('sfedittags', '');
	}(window.spj = window.spj || {}, jQuery));
</script>
<?php
    require_once SPTLIBDIR.'sp-tags-database.php';

	global $wp_locale;
	spa_paint_options_init();
	spa_paint_open_tab(__('Tags', 'sp-tags').' - '.__('Search Topics', 'sp-tags'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Search Topics', 'sp-tags'), false);
				if (!isset($_GET['paged'])) $_GET['paged'] = 1;
				$topics_per_page = 20;
				if (isset($_GET['topics_per_page'])) $topics_per_page = SP()->filters->integer($_GET['topics_per_page']);

				# get the topics and tags to display
				$month = '';
				if (isset($_GET['m'])) $month = SP()->filters->str($_GET['m']);
				$forum = '';
				if (isset($_GET['forum'])) $forum = SP()->filters->str($_GET['forum']);
				$search = '';
				if (isset($_GET['s'])) $search = SP()->filters->str($_GET['s']);
				$topics = sp_tags_get_topics(SP()->filters->integer($_GET['paged']), $topics_per_page, $month, $forum, $search);
?>
				<form id="posts-filter" action="<?php echo SPADMINPLUGINS; ?>" method="get">
					<input type="hidden" name="page" value="<?php echo SP_FOLDER_NAME; ?>/admin/panel-plugins/spa-plugins.php" />
					<input type="hidden" name="tab" value="plugin" />
					<input type="hidden" name="admin" value="sp_tags_admin_edit" />
					<input type="hidden" name="form" value="0" />
					<ul class="subsubsub" style="padding-right:40px">
<?php
						$num_topics = SP()->DB->count(SPTOPICS);
						echo '<li><a href="'.SPADMINPLUGINS.'&amp;tab=plugin&amp;admin=sp_tags_admin_edit&amp;form=1'.'">'.__('All topics', 'sp-tags').' ('.$num_topics.')</a></li>';
?>
					</ul>

					<p id="post-search">
						<input class="sfpostcontrol" style="width:275px;" type="text" id="post-search-input" name="s" value="<?php echo $search; ?>" />
						<input type="submit" value="<?php _e('Search topic titles', 'sp-tags'); ?>" class="button" />
					</p>

					<div class="tablenav">
<?php
						$page_links = paginate_links(array(
							'total' => ceil($topics['count'] / $topics_per_page ),
							'current' => SP()->filters->integer($_GET['paged']),
							'base' => SPADMINPLUGINS.'&tab=plugin&admin=sp_tags_admin_edit&form=1'.'&amp;%_%',
							'format' => 'paged=%#%',
							'add_args' => ''));

						if ( $page_links )
							echo "<div class='tablenav-pages'>$page_links</div>";
						?>

						<div class="sfalignleft">
							<?php
							$arc_query = 'SELECT DISTINCT YEAR(topic_date) AS yyear, MONTH(topic_date) AS mmonth FROM '.SPTOPICS.' ORDER BY topic_date DESC';
							$arc_result = SP()->DB->select($arc_query);

							$month_count = count($arc_result);
							if ( $month_count && !( 1 == $month_count && 0 == $arc_result[0]->mmonth ) ) { ?>
								<select name='m' style='font-weight:normal'>
								<option<?php if (empty($_GET['m'])) echo ' selected'; ?> value='0'><?php _e('Show all dates', 'sp-tags'); ?></option>
								<?php
								foreach ($arc_result as $arc_row) {
									if ( $arc_row->yyear == 0 )
										continue;
									$arc_row->mmonth = zeroise( $arc_row->mmonth, 2 );

									if (isset($_GET['m']) && $_GET['m'] == $arc_row->yyear.$arc_row->mmonth)
										$default = ' selected="selected"';
									else
										$default = '';

									echo "<option$default value='$arc_row->yyear$arc_row->mmonth'>";
									echo $wp_locale->get_month($arc_row->mmonth)." $arc_row->yyear";
									echo '</option>';
								}
?>
								</select>
<?php
							}

							$forums = spa_get_forums_all();
							$selected = '';
							if (empty($_GET['forum'])) $selected=' selected';
							echo '<select name="forum" class="sfcontrol" style="font-weight:normal">';
							echo '<option'.$selected.' value="0">'.__('View all forums', 'sp-tags').'</option>';
							foreach ($forums as $forum) {
								$selected = '';
								if (isset($_GET['forum']) && $_GET['forum'] == $forum->forum_id) $selected=' selected';
								echo '<option'.$selected.' value="'.$forum->forum_id.'">'.SP()->displayFilters->title($forum->forum_name).'</option>';
							}
							echo '</select>';
?>
							<select name="topics_per_page" id="topics_per_page" style='font-weight:normal'>
								<option <?php if ( $topics_per_page == 10 ) echo 'selected="selected"'; ?> value="10">10</option>
								<option <?php if ( $topics_per_page == 15 ) echo 'selected="selected"'; ?> value="15">15</option>
								<option <?php if ( $topics_per_page == 20 ) echo 'selected="selected"'; ?> value="20">20</option>
								<option <?php if ( $topics_per_page == 30 ) echo 'selected="selected"'; ?> value="30">30</option>
								<option <?php if ( $topics_per_page == 40 ) echo 'selected="selected"'; ?> value="40">40</option>
								<option <?php if ( $topics_per_page == 50 ) echo 'selected="selected"'; ?> value="50">50</option>
								<option <?php if ( $topics_per_page == 100 ) echo 'selected="selected"'; ?> value="100">100</option>
								<option <?php if ( $topics_per_page == 200 ) echo 'selected="selected"'; ?> value="200">200</option>
							</select>

							<input type="submit" id="filter-submit" value="<?php esc_attr_e(__('Filter', 'sp-tags')); ?>" class="button-secondary" />
						</div>

						<br style="clear:both;" />
					</div>
				</form>

				<br style="clear:both;" />
<?php
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();

	echo '<div class="sfform-panel-spacer"></div>';
	spa_paint_close_tab();

   	echo '<div class="sfform-panel-spacer"></div>';

	spa_paint_open_tab(__('Tags', 'sp-tags').' - '.__('Mass Edit Tags', 'sp-tags'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Mass Edit Tags', 'sp-tags'), true, 'tags-edit');

                $ajaxURL = wp_nonce_url(SPAJAXURL.'tags-admin&save=edittags', 'tags-admin');
            	echo '<form action="'.$ajaxURL.'" method="post" id="sfedittags" name="sfedittags">';
            	echo sp_create_nonce('forum-adminform_sfedittags');
?>
				<?php if ($topics['count'] > 0) : ?>
						<table class="widefat fixed striped">
                            <thead>
							<tr>
								<th class="manage-column"><?php _e('Topic Title', 'sp-tags'); ?></th>
								<th style="text-align:center" class="manage-column"><?php _e('Tags', 'sp-tags'); ?></th>
							</tr>
                            </thead>
                            <tbody>
<?php
							$x = -1;
							foreach ($topics['topic'] as $topic) {
								$x++;
?>
								<tr style="vertical-align:top">
									<td>
										<?php echo SP()->spPermalinks->get_topic_url($topic['forum_slug'], $topic['topic_slug'], SP()->displayFilters->title($topic['topic_name'])); echo '<br/>('.SP()->displayFilters->title($topic['forum_name']).')'; ?>
									</td>
									<td>
<?php
										$ttags = '';
										if (isset($topic['tags']['list'])) $ttags = $topic['tags']['list'];
?>
										<input class="tags_input sfpostcontrol" style="width:100%" type="text" name="tags[<?php echo $x; ?>]" value="<?php echo SP()->displayFilters->title($ttags); ?>" />
<?php
                                        $topic_id = (isset($topic['topic_id'])) ? $topic['topic_id'] : '';
                                        $tag_id = (isset($topic['tags']['ids'])) ? $topic['tags']['ids'] : '';
?>
										<input type="hidden" name="topic_id[<?php echo $x; ?>]" value="<?php echo $topic_id; ?>" />
										<input type="hidden" name="tag_id[<?php echo $x; ?>]" value="<?php echo $tag_id; ?>" />
									</td>
								</tr>
							<?php } ?>
                            </tbody>
						</table>
				<?php else: ?>
					<p><?php _e('No topics match the search criteria', 'sp-tags'); ?>
				<?php endif; ?>

                <div class="sfform-submit-bar">
                    <input class="button-primary" type="submit" value="<?php _e('Update', 'sp-tags'); ?>" />
                </div>
                </form>
<?php
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		do_action('sph_tags_edit_panel');
		spa_paint_close_container();
    	echo '<div class="sfform-panel-spacer"></div>';
    spa_paint_close_tab();
}
