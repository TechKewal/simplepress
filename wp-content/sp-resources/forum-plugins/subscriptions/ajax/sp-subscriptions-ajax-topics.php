<?php
/*
Simple:Press
Topic Subscriptions plugin ajax routine for users management functions
$LastChangedDate: 2018-10-17 15:17:49 -0500 (Wed, 17 Oct 2018) $
$Rev: 15756 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

spa_admin_ajax_support();

if (!sp_nonce('subs-topics')) die();

require_once SLIBDIR.'sp-subscriptions-database.php';
require_once SLIBDIR.'sp-subscriptions-components.php';

# Check Whether User Can Manage Users
if (!SP()->auths->current_user_can('SPF Manage Users')) die();

$action = SP()->filters->str($_GET['targetaction']);

if ($action == 'topiclist') {
	$error = "";
	if (isset($_GET['groups']) && SP()->filters->str($_GET['groups']) == 'error') $error = 'Group';
	if (isset($_GET['forums']) && SP()->filters->str($_GET['forums']) == 'error') $error = 'Forums';
	if ($error) {
		echo sprintf(__('You elected to filter by %1$s but selected no %2$s items', 'sp-subs'), $error, $error);
		die();
	}

	sp_subscriptions_render_topic_subscriptions();
	die();
}

if ($action == 'del_subs') {
	$tid = SP()->filters->integer($_GET['id']);
	sp_subscriptions_remove_topic_subscriptions($tid);
}

if ($action == 'display-groups') {
	echo '<select style="width:220px;height:auto" multiple size="10" class="sfacontrol" id="grouplist" name="subsgroups[]">';
	$groups = SP()->DB->table(SPGROUPS, '', '', 'group_seq');
	if ($groups) {
		foreach ($groups as $group) {
			echo '<option value="'.$group->group_id.'">'.SP()->displayFilters->title($group->group_name).'</option>';
		}
	}
	echo '</select>';
	echo '<br />';
	echo '<input type="button" class="button button-highlighted spLayerToggle" value="'.__('Close', 'sp-subs').'" data-target="sub-select-group">';
}

if ($action == 'display-forums') {
	echo '<select style="width:220px;height:auto" multiple size="10" class="sfacontrol" id="forumlist" name="subsforums[]">';
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
	echo '<input type="button" class="button button-highlighted spLayerToggle" value="'.__("Close", "sp-subs").'" data-target="sub-select-forum">';
}

die();

function sp_subscriptions_render_topic_subscriptions() {
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
	$data = sp_subscriptions_get_topic_subscriptions($filter, $groups, $forums, $curpage, $search);
	$records = $data['data'];

	# paging
	$totalpages = ceil($data['count'] / 20);

	spa_paint_options_init();
	spa_paint_open_tab(__('Manage Users - Subscriptions by Topic', 'sp-subs'), true);
	spa_paint_open_panel();
	spa_paint_open_fieldset(__('Topic Subscriptions', 'sp-subs'));

	echo '<div class="tablenav">';
	echo '<div class="tablenav-pages">';
	echo '<strong>'.__('Page', 'sp-subs').':</strong>  ';
	echo sp_subscriptions_topics_pn_next($curpage, $totalpages, 3);
	echo '<span class="page-numbers current">'.$curpage.'</span>';
	echo sp_subscriptions_topics_pn_previous($curpage, $totalpages, 3);
	echo '</div>';

	echo '<div>';
	$site = wp_nonce_url(SPAJAXURL.'subs-topics&targetaction=topiclist&page=1', 'subs-topics');
	$gif = SPCOMMONIMAGES.'working.gif';
	echo '<form action="'.SPADMINUSER.'" method="post" name="sptopicsubs" id="sptopicsubs" data-target="sptopicsubs" data-site="'.$site.'" data-img="'.$gif.'">';
?>
	<input type="text" class="sfacontrol" id="post-search-input" name="swsearch" value="<?php echo esc_attr($search); ?>" />
	<input type="button" class="button-primary spSubsShowSubs" data-target="sptopicsubs" data-site="<?php echo $site; ?>" data-img="<?php echo $gif; ?>" value="<?php esc_attr_e(__('Topic Search', 'sp-subs')); ?>" />
	</form>
	</div>
<?php
	echo '</div>';

	# show data
	echo '<table class="widefat fixed striped spMobileTable800" style="padding:0;border-spacing:0;border-collapse:separate">';
	if ($records) {
		echo '<thead>';
		echo '<tr>';
		echo '<th style="text-align:center;">'.__("Group", "sp-subs").'</th>';
		echo '<th style="text-align:center;">'.__("Forum", "sp-subs").'</th>';
		echo '<th style="text-align:center;">'.__("Topic", "sp-subs").'</th>';
		echo '<th style="text-align:center;">'.__('Topic Subscriptions', 'sp-subs').'</th>';
		echo '<th style="text-align:center;">'.__('Manage', 'sp-subs').'</th>';
		echo '</tr>';
		echo '</thead>';

		echo '<tbody>';
		foreach ($records as $index => $record) {
			echo "<tr id='topic-subs$index' class='spMobileTableData'>";
			echo '<td data-label="'.__('Group', 'sp-subs').'" style="text-align:center;">'.SP()->displayFilters->title($record->group_name).'</td>';
			echo '<td data-label="'.__('Forum', 'sp-subs').'" style="text-align:center;">'.SP()->displayFilters->title($record->forum_name).'</td>';
			$url = SP()->spPermalinks->build_url($record->forum_slug, $record->topic_slug, 1, 0);
			echo '<td data-label="'.__('Topic', 'sp-subs').'" style="text-align:center;"><a href="'.$url.'">'.SP()->displayFilters->title($record->topic_name).'</a></td>';
			echo '<td data-label="'.__('Subscriptions', 'sp-subs').'" style="text-align:center;">';

			$members = SP()->activity->get_users(SPACTIVITY_SUBSTOPIC, $record->topic_id);
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
			echo '<td data-label="'.__('Manage', 'sp-subs').'" style="text-align:center;">';
			if ($members) {
                $msg = esc_attr(__('Are you sure you want to delete these topic subscriptions?'), 'sp-subs');
				$site = wp_nonce_url(SPAJAXURL.'subs-topics&targetaction=del_subs&id='.$record->topic_id, 'subs-topics');
    			$gif = SPCOMMONIMAGES.'working.gif';
?>
				<img class="spDeleteRow" data-msg="<?php echo $msg; ?>" data-url="<?php echo $site; ?>" data-target="topic-subs<?php echo $index; ?>" src="<?php echo SPCOMMONIMAGES; ?>delete.png" title="<?php esc_attr_e(__('Delete Subscriptions', 'sp-subs')); ?>" />&nbsp;
<?php
			}
			echo '</td>';
			echo '</tr>';
		}
	} else {
		echo '<tr>';
		echo '<td>';
		_e('No subscriptions found!', 'sp-subs');
		echo '</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';

	spa_paint_close_fieldset();
	spa_paint_close_panel();
	spa_paint_close_container();
}
