<?php
/*
Simple:Press
Topic Watches plugin ajax routine for users management functions
$LastChangedDate: 2018-10-17 15:17:49 -0500 (Wed, 17 Oct 2018) $
$Rev: 15756 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

spa_admin_ajax_support();

if (!sp_nonce('watches-topics')) die();

require_once WLIBDIR.'sp-watches-database.php';
require_once WLIBDIR.'sp-watches-components.php';

# Check Whether User Can Manage Users
if (!SP()->auths->current_user_can('SPF Manage Users')) die();

$action = SP()->filters->str($_GET['targetaction']);

if ($action == 'topiclist') {
	$error = '';
	if (isset($_GET['groups']) && SP()->filters->str($_GET['groups']) == 'error') $error = 'Group';
	if (isset($_GET['forums']) && SP()->filters->str($_GET['forums']) == 'error') $error = 'Forums';
	if ($error) {
		echo sprintf(__('You elected to filter by %1$s but selected no %2$s items', 'sp-watches'), $error, $error);
		die();
	}

	sp_watches_render_topic_watches();
	die();
}

if ($action == 'del_watches') {
	$tid = SP()->filters->integer($_GET['id']);
	sp_watches_remove_topic_watches($tid);
}

if ($action == 'display-groups') {
	echo '<select style="width:auto;height:auto" multiple size="10" class="sfacontrol" id="grouplist" name="watchesgroups[]">';
	$groups = SP()->DB->table(SPGROUPS, '', '', 'group_seq');
	if ($groups) {
		foreach ($groups as $group) {
			echo '<option value="'.$group->group_id.'">'.SP()->displayFilters->title($group->group_name).'</option>';
		}
	}
	echo '</select>';
	echo '<br />';
	echo '<input type="button" class="button button-highlighted spLayerToggle" value="'.__('Close', 'sp-watches').'" data-target="select-group">';
}

if ($action == 'display-forums') {
	echo '<select style="width:auto;height:auto" multiple size="10" class="sfacontrol" id="forumlist" name="watchesforums[]">';
	$forums = spa_get_forums_all();
	if ($forums) {
		$thisgroup = 0;
		foreach ($forums as $forum) {
			if ($thisgroup != $forum->group_id) {
				if ($thisgroup != 0) {
					echo '</optgroup>';
				}
				echo '<optgroup label="'.SP()->displayFilters->title($forum->group_name).'">';
				$thisgroup = $forum->group_id;
			}
			echo '<option value="'.$forum->forum_id.'">'.SP()->displayFilters->title($forum->forum_name).'</option>';
		}
	}
	echo '</optgroup></select>';
	echo '<br />';
	echo '<input type="button" class="button button-highlighted spLayerToggle" value="'.__('Close', 'sp-watches').'" data-target="select-forum">';
}

die();

function sp_watches_render_topic_watches() {
	require_once SP_PLUGIN_DIR.'/admin/library/spa-tab-support.php';

	$filter = SP()->filters->str($_GET['filter']);
	if (isset($_GET['page'])) {
		$curpage = SP()->filters->integer($_GET['page']);
	} else {
		$curpage = 1;
	}
	if (isset($_GET['swsearch'])) {
		$search = SP()->filters->str($_GET['swsearch']);
	} else {
		$search = '';
	}
	if (isset($_GET['groups'])) {
		$groups = explode('-', SP()->filters->str($_GET['groups']));
	} else {
		$groups[0] = -1;
	}
	if (isset($_GET['forums'])) {
		$forums = explode('-', SP()->filters->str($_GET['forums']));
	} else {
		$forums[0] = -1;
	}
	$data = sp_watches_get_topic_watches($filter, $groups, $forums, $curpage, $search);
	$records = $data['data'];

	# paging
	$totalpages = ceil($data['count'] / 20);
	spa_paint_options_init();
	spa_paint_open_tab(__('Manage Users - Watches by Topic', 'sp-watches'), true);
	spa_paint_open_panel();
	spa_paint_open_fieldset(__('Topic Watches', 'sp-watches'));

	echo '<div class="tablenav">';
	echo '<div class="tablenav-pages">';
	echo '<strong>'.__('Page:', 'sp-watches').'</strong>  ';
	echo sp_watches_topics_pn_next($curpage, $totalpages, 3);
	echo '<span class="page-numbers current">'.$curpage.'</span>';
	echo sp_watches_topics_pn_previous($curpage, $totalpages, 3);
	echo '</div>';

	echo '<div>';
	$site = wp_nonce_url(SPAJAXURL.'watches-topics&targetaction=topiclist&page=1', 'watches-topics');;
	$gif = SPCOMMONIMAGES.'working.gif';
	echo '<form action="'.SPADMINUSER.'" method="post" name="sptopicwatches" id="sptopicwatches" data-target="sptopicwatches" data-site="'.$site.'" data-img="'.$gif.'">';
?>
	<input type="text" class="sfacontrol" id="post-search-input" name="swsearch" value="<?php echo esc_attr($search); ?>" />
	<input type="button" class="button-primary spWatchesShowWatches" data-target="sptopicwatches" data-site="<?php echo $site; ?>" data-img="<?php echo $gif; ?>" value="<?php esc_attr_e(__('Search', 'sp-watches')); ?>" />
	</form>
	</div>
<?php
	echo '</div>';

	# show data
	echo '<table class="widefat fixed striped spMobileTable800" style="padding:0;border-spacing:0;border-collapse:separate">';
	if ($records) {
		echo '<thead>';
		echo '<tr>';
		echo '<th style="text-align:center;">'.__('Group', 'sp-watches').'</th>';
		echo '<th style="text-align:center;">'.__('Forum', 'sp-watches').'</th>';
		echo '<th style="text-align:center;">'.__('Topic', 'sp-watches').'</th>';
		echo '<th style="text-align:center;">'.__('Watches', 'sp-watches').'</th>';
		echo '<th style="text-align:center;">'.__('Manage', 'sp-watches').'</th>';
		echo '</tr>';
		echo '</thead>';

		echo '<tbody>';
		foreach ($records as $index => $record) {
			echo "<tr id='topic-watches$index' class='spMobileTableData'>";
			echo '<td data-label="'.__('Group', 'sp-watches').'" style="text-align:center;">'.SP()->displayFilters->title($record->group_name).'</td>';
			echo '<td data-label="'.__('Forum', 'sp-watches').'" style="text-align:center;">'.SP()->displayFilters->title($record->forum_name).'</td>';
			$url = SP()->spPermalinks->build_url($record->forum_slug, $record->topic_slug, 1, 0);
			echo '<td data-label="'.__('Topic', 'sp-watches').'" style="text-align:center;"><a href="'.$url.'">'.SP()->displayFilters->title($record->topic_name).'</a></td>';
			echo '<td data-label="'.__('Watches', 'sp-watches').'" style="text-align:center;">';

			$members = SP()->activity->get_users(SPACTIVITY_WATCH, $record->topic_id);
			if ($members) {
				$first = true;
				foreach($members as $member) {
					if ($first) {
						echo SP()->displayFilters->name($member->display_name);
						$first = false;
					} else {
						echo ', '.SP()->displayFilters->name($member->display_name);
					}
				}
			}

			echo '</td>';
			echo '<td data-label="'.__('Manage', 'sp-watches').'" style="text-align:center;">';
			if ($members) {
                $msg = esc_attr(__('Are you sure you want to delete this watch?'), 'sp-watches');
				$site = wp_nonce_url(SPAJAXURL.'watches-topics&targetaction=del_watches&id='.$record->topic_id, 'watches-topics');
    			$gif = SPCOMMONIMAGES.'working.gif';
				?>
				<img class="spDeleteRow" data-msg="<?php echo $msg; ?>" data-url="<?php echo $site; ?>" data-target="topic-watches<?php echo $index; ?>" src="<?php echo SPCOMMONIMAGES; ?>delete.png" title="<?php esc_attr_e(__('Delete Watches', 'sp-watches')); ?>" />&nbsp;
				<?php
			}
			echo '</td>';
			echo '</tr>';
		}
	} else {
		echo '<tr>';
		echo '<td>';
		_e('No watches found!', 'sp-watches');
		echo '</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';

	spa_paint_close_fieldset();
	spa_paint_close_panel();
	spa_paint_close_container();
}
