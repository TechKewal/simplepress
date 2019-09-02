<?php
/*
Simple:Press
Post Multiple Forums Plugin Admin Options Form
$LastChangedDate: 2018-08-12 14:05:34 -0500 (Sun, 12 Aug 2018) $
$Rev: 15701 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_post_multiple_admin_options_form() {
	$data = SP()->options->get('post-multiple');

	spa_paint_options_init();
	spa_paint_open_tab(__('Post Multiple Forums', 'sp-announce'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Options', 'sp-post-multiple'), true, 'post-multiple-options');

			$forums = spa_get_forums_all();
			if ($forums) {
				echo '<p>'.__('Select any forums that you DO NOT want included in the list of forums for posting multiple', 'sp-post-multiple').'</p>';
				$thisgroup = 0;
				foreach ($forums as $forum) {
					# if new group, display group name
					if ($thisgroup != $forum->group_id) {
						echo '<h4 style="clear:left;padding-top:20px;">'.__('Group', 'sp-post-multiple').': '.SP()->displayFilters->title($forum->group_name).'</h4>';
						$thisgroup = $forum->group_id;
					}

					$checked = (in_array($forum->forum_id, $data['exclude'])) ? ' checked="checked"' : '';

					# add checkbox for this forum
					echo "<div class='sp-form-row'>\n";
					echo "<input$checked type='checkbox' name='exclude[$forum->forum_id]' id='sfforum-$forum->forum_id' />\n";
					echo "<label class='wp-core-ui' for='sfforum-$forum->forum_id'>".SP()->displayFilters->title($forum->forum_name)."</label>\n";
					echo '</div>';
				}
			}

			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
}
