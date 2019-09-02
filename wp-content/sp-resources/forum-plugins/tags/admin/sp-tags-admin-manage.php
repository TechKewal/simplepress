<?php
/*
Simple:Press
Tags Plugin manage tags routine
$LastChangedDate: 2018-11-13 20:42:11 -0600 (Tue, 13 Nov 2018) $
$Rev: 15818 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_tags_admin_manage_form() {
?>
<script>
	(function(spj, $, undefined) {
		$(document).ready(function() {
			spj.loadAjaxForm('sfrenametags', 'sfreloadmb');
			spj.loadAjaxForm('sfdeletetags', 'sfreloadmb');
			spj.loadAjaxForm('sfaddtags', 'sfreloadmb');
			spj.loadAjaxForm('sfcleantags', 'sfreloadmb');

			/* Register initial event */
			spj.registerTagClick();
			spj.registerAjaxNav();
		});
	}(window.spj = window.spj || {}, jQuery));
</script>
<?php
    require_once SPTLIBDIR.'sp-tags-database.php';

	spa_paint_options_init();
	spa_paint_open_tab(__('Tags', 'sp-tags').' - '.__('Manage Tags', 'sp-tags'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Manage Tags', 'sp-tags'), true, 'tags-manage');
				# some URL settings
				$baseurl = SPADMINPLUGINS.'&amp;tab=plugin&amp;admin=sp_tags_admin_manage&amp;form=0';
				$sort_order = (isset($_GET['tag_sortorder'])) ? SP()->filters->str($_GET['tag_sortorder']) : 'desc';
				$search_url = (isset($_GET['search'])) ? '&amp;search='.SP()->filters->str($_GET['search']) : '';
				$page = '';
				if (isset($_GET['page'])) $page = intval($_GET['page']);
				$action_url = $baseurl.$page.'&amp;tag_sortorder='.$sort_order.$search_url;

				# possible ordering types
				$order_array = array(
					'desc' => __('Most popular', 'sp-tags'),
					'asc' => __('Least used', 'sp-tags'),
					'natural' => __('Alphabetical', 'sp-tags'));

				# get search terms
				if (!empty($_GET['search'])) {
					$search = SP()->filters->str($_GET['search']);
				} else {
					$search = '';
				}
?>
				<div class="wrap tag_wrap">
					<table>
						<tr>
							<td colspan="2">
								<form action="<?php echo SPADMINPLUGINS; ?>" method="get">
									<label for="search"><?php _e('Search tags', 'sp-tags'); ?></label><br />
									<input type="hidden" name="page" value="<?php echo SP_FOLDER_NAME; ?>/admin/panel-plugins/spa-plugins.php" />
									<input type="hidden" name="tab" value="plugin" />
									<input type="hidden" name="admin" value="sp_tags_admin_manage" />
									<input type="hidden" name="form" value="0" />
									<input type="hidden" name="tag_sortorder" value="<?php echo $sort_order; ?>" />
									<input class="sfpostcontrol" style="width:180px;" type="text" name="search" id="search" value="<?php echo esc_attr($search); ?>" />
									<input class="button button-highlighted" type="submit" value="<?php esc_attr_e(__('Go', 'sp-tags')); ?>" />
								</form>

							</td>
						</tr>
						<tr>
							<td class="list_tags">
								<fieldset class="options" id="taglist">
									<div class="sort_order">
										<h3><?php _e('Sort order:', 'sp-tags'); ?></h3>
	<?php
										$output = array();
										foreach ($order_array as $sort => $title) {
											$output[] = ($sort == $sort_order) ? '<span style="color: red;">'.$title.'</span>' : '<a href="'.$baseurl.'&amp;tag_sortorder='.$sort.$search_url.'">'.$title.'</a>';
										}
										echo implode('<br />', $output);
	?>
									</div>

									<div id="tagslist">
										<ul>
<?php
											$tags = sp_tags_get_tags($sort_order, $search, 0);
											if($tags) {
												foreach ($tags['tags'] as $tag) {
													echo '<li><span>'.$tag->tag_name.'</span>&nbsp;('.$tag->tag_count.')</li>';
												}
											}
?>
										</ul>

										<?php if (empty($_GET['search']) && $tags['count'] > SFMANAGETAGSNUM) : ?>
											<div class="navigation">
												<a href="<?php echo wp_nonce_url(SPAJAXURL."tags-admin&amp;pagination=1&amp;order=$sort_order", 'tags-admin'); ?>"><?php _e('Previous tags', 'sp-tags'); ?></a> | <?php _e('Next tags', 'sp-tags'); ?>
											</div>
										<?php endif; ?>
									</div>
								</fieldset>
							</td>

							<td class="forms_manage">
								<h3 style="padding-top:10px;"><?php _e('Rename tag', 'sp-tags'); ?>:</h3>
<?php
                                $ajaxURL = wp_nonce_url(SPAJAXURL.'tags-admin&amp;save=renametags', 'tags-admin');
?>
								<form action="<?php echo $ajaxURL; ?>" method="post" id="sfrenametags" name="sfrenametags">
									<?php echo sp_create_nonce('forum-adminform_sfrenametags'); ?>
									<input type="hidden" name="tag_sortorder" value="<?php echo $sort_order; ?>" />
									<table class="form-table">
										<tr>
											<td colspan="3">
												<?php _e('Enter the tag to rename and its new value.  You can use this feature to merge tags too. Click "Rename" and all topics which use this tag will be updated', 'sp-tags'); ?>
												<br />
												<?php _e('You can specify multiple tags to rename by separating them with commas', 'sp-tags'); ?>
												<p>&nbsp;</p>
											</td>
										</tr>
										<tr>
											<th style="vertical-align:middle" scope="row"><label for="renametag_old"><?php _e('Tag(s) to rename', 'sp-tags'); ?>:</label></th>
											<td style="width:10px"></td>
											<td><input class="sfpostcontrol" style="width:240px;" type="text" id="renametag_old" name="renametag_old" value="" /></td>
										</tr>
										<tr>
											<th style="vertical-align:middle" scope="row"><label for="renametag_new"><?php _e('New tag name(s)', 'sp-tags'); ?>:</label></th>
											<td style="width:10px"></td>
											<td>
												<input class="sfpostcontrol" style="width:240px;" type="text" id="renametag_new" name="renametag_new" value="" />
												<input class="button button-highlighted" type="submit" name="rename" value="<?php esc_attr_e(__('Rename', 'sp-tags')); ?>" />
											</td>
										</tr>
									</table>
								</form>

								<div class="sfform-panel-spacer"></div>
								<h3><?php _e('Delete tag', 'sp-tags'); ?>:</h3>
<?php
                                $ajaxURL = wp_nonce_url(SPAJAXURL.'tags-admin&amp;save=deletetags', 'tags-admin');
?>
								<form action="<?php echo $ajaxURL; ?>" method="post" id="sfdeletetags" name="sfdeletetags">
									<?php echo sp_create_nonce('forum-adminform_sfdeletetags'); ?>
									<input type="hidden" name="tag_sortorder" value="<?php echo $sort_order; ?>" />
									<table class="form-table">
										<tr>
											<td colspan="3">
												<?php _e('Enter the name of the tag to delete.  This tag will be removed from all topics', 'sp-tags'); ?>
												<br />
												<?php _e('You can specify multiple tags to delete by separating them with commas', 'sp-tags'); ?>.
												<p>&nbsp;</p>
											</td>
										</tr>
										<tr>
											<th style="vertical-align:middle" scope="row"><label for="deletetag_name"><?php _e('Tag(s) to delete', 'sp-tags'); ?>:</label></th>
											<td style="width:10px"></td>
											<td>
												<input class="sfpostcontrol" style="width:240px;" type="text" id="deletetag_name" name="deletetag_name" value="" />
												<input class="button button-highlighted" type="submit" name="delete" value="<?php esc_attr_e(__('Delete', 'sp-tags')); ?>" />
											</td>
										</tr>
									</table>
								</form>

								<div class="sfform-panel-spacer"></div>
								<h3><?php _e('Add tag', 'sp-tags'); ?>:</h3>
<?php
                                $ajaxURL = wp_nonce_url(SPAJAXURL.'tags-admin&amp;save=addtags', 'tags-admin');
?>
								<form action="<?php echo $ajaxURL; ?>" method="post" id="sfaddtags" name="sfaddtags">
									<?php echo sp_create_nonce('forum-adminform_sfaddtags'); ?>
									<input type="hidden" name="tag_sortorder" value="<?php echo $sort_order; ?>" />
									<table class="form-table">
										<tr>
											<td colspan="3">
												<?php _e('This feature lets you add one or more new tags to all topics which match any of the tags given', 'sp-tags'); ?>
												<br />
												<?php _e('You can specify multiple tags to add by separating them with commas.  If you want the tag(s) to be added to all topics, then don\'t specify any tags to match', 'sp-tags'); ?>
												<br />
												<?php _e('The tags being added will be subject to the maximum tags limit you have specified in the forum options', 'sp-tags'); ?>
												<p>&nbsp;</p>
											</td>
										</tr>
										<tr>
											<th style="vertical-align:middle" scope="row"><label for="addtag_match"><?php _e('Tag(s) to match', 'sp-tags'); ?>:</label></th>
											<td style="width:10px"></td>
											<td><input class="sfpostcontrol" style="width:240px;" type="text" id="addtag_match" name="addtag_match" value="" /></td>
										</tr>
										<tr>
											<th style="vertical-align:middle" scope="row"><label for="addtag_new"><?php _e('Tag(s) to add', 'sp-tags'); ?>:</label></th>
											<td style="width:10px"></td>
											<td>
												<input class="sfpostcontrol" style="width:240px;" type="text" id="addtag_new" name="addtag_new" value="" />
												<input class="button button-highlighted" type="submit" name="Add" value="<?php _e('Add', 'sp-tags'); ?>" />
											</td>
										</tr>
									</table>
								</form>

								<div class="sfform-panel-spacer"></div>
								<h3><?php _e('Clean up tags', 'sp-tags'); ?>:</h3>
<?php
                                $ajaxURL = wp_nonce_url(SPAJAXURL.'tags-admin&amp;save=cleanup', 'tags-admin');
?>
								<form action="<?php echo $ajaxURL; ?>" method="post" id="sfcleantags" name="sfcleantags">
									<?php echo sp_create_nonce('forum-adminform_sfcleanup'); ?>
									<input type="hidden" name="tag_sortorder" value="<?php echo $sort_order; ?>" />
									<table class="form-table">
										<tr>
											<td>
												<?php _e('This feature lets you clean up your tags database.  This will be useful should some tags become orphaned from topics', 'sp-tags'); ?>
												<p>&nbsp;</p>
											</td>
										</tr>
										<tr>
											<td><input class="button button-highlighted" type="submit" name="Clean" value="<?php esc_attr_e(__('Clean Up', 'sp-tags')); ?>" /></td>
										</tr>
									</table>
								</form>
							</td>
						</tr>
					</table>
				</div>

				<div class="sfform-panel-spacer"></div>
<?php
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		do_action('sph_tags_manage_panel');
		spa_paint_close_container();
        spa_paint_close_tab();
		echo '<div class="sfform-panel-spacer"></div>';
}
