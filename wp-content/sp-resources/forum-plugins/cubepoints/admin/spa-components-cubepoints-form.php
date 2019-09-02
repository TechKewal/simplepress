<?php
/*
cubepoints Integration Admin Options Form
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# show options form
function spa_cubepoints_admin_options_form(){
	# fetch settings array
	$settings = SP()->options->get('cubepoints');
	# paint admin control panel
	spa_paint_options_init();
	spa_paint_open_tab(__('CubePoints Integration', 'sp-cube'), true);
		spa_paint_spacer();
		sp_cubepoints_show_alert();
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('CubePoints Options', 'sp-cube'), true, 'cubepoints');
				spa_paint_input(__('Points for new topic', 'sp-cube'), 'points_topic', $settings['points_topic']);
				spa_paint_input(__('Points for new post', 'sp-cube'), 'points_post', $settings['points_post']);
                if (SP()->plugin->is_active('post-rating/sp-rating-plugin.php')) {
                    spa_paint_input(__('Points for rating a post', 'sp-cube'), 'points_rate_post', $settings['points_rate_post']);
                    spa_paint_input(__('Points for user having post rated', 'sp-cube'), 'points_post_rated', $settings['points_post_rated']);
                }
                if (SP()->plugin->is_active('polls/sp-polls-plugin.php')) {
                    spa_paint_input(__('Points for creating a poll', 'sp-cube'), 'points_create_poll', $settings['points_create_poll']);
                    spa_paint_input(__('Points for voting in a poll', 'sp-cube'), 'points_vote_poll', $settings['points_vote_poll']);
                    spa_paint_input(__('Points for users poll getting a vote', 'sp-cube'), 'points_poll_voted', $settings['points_poll_voted']);
                }
                spa_paint_input(__('Max daily points that can be accrued (zero for no limit - logging must be enabled)', 'sp-cube'), 'points_cap', $settings['points_cap']);
				spa_paint_checkbox(__('Take points from users when posts/topics are deleted?', 'sp-cube'), 'points_delete', $settings['points_delete']);
				spa_paint_checkbox(__('CubePoints Logging Enabled?', 'sp-cube'), 'logging', $settings['logging']);

				spa_paint_checkbox(__('Disable SP Admins from gaining points', 'sp-cube'), 'admins', $settings['admins']);
				spa_paint_checkbox(__('Disable SP Moderators from gaining points', 'sp-cube'), 'moderators', $settings['moderators']);

			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
}

# save options
function spa_cubepoints_admin_options_save(){
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	# grab current options
	$settings = SP()->options->get('cubepoints');

	# deal with checkboxes
	if (isset($_POST['logging'])) { $settings['logging'] = true; } else { $settings['logging'] = false; }
	if (isset($_POST['points_delete'])) { $settings['points_delete'] = true; } else { $settings['points_delete'] = false; }

	if(isset($_POST['admins']) ? $settings['admins'] = true : $settings['admins'] = false);
	if(isset($_POST['moderators']) ? $settings['moderators'] = true : $settings['moderators'] = false);

	# deal with input boxes
	$settings['points_post'] = SP()->filters->integer($_POST['points_post']);
	$settings['points_topic'] = SP()->filters->integer($_POST['points_topic']);

	$settings['points_cap'] = SP()->filters->integer($_POST['points_cap']);

	if (SP()->plugin->is_active('post-rating/sp-rating-plugin.php')) {
        $settings['points_rate_post'] = SP()->filters->integer($_POST['points_rate_post']);
        $settings['points_post_rated'] = SP()->filters->integer($_POST['points_post_rated']);
    }

    if (SP()->plugin->is_active('polls/sp-polls-plugin.php')) {
    	$settings['points_create_poll'] = SP()->filters->integer($_POST['points_create_poll']);
    	$settings['points_vote_poll'] = SP()->filters->integer($_POST['points_vote_poll']);
    	$settings['points_poll_voted'] = SP()->filters->integer($_POST['points_poll_voted']);
    }

	# update options
	SP()->options->update('cubepoints', $settings);

	# display message and return
	$mess = __('CubePoints Integration Options Updated', 'sp-cube');
	return $mess;
}
