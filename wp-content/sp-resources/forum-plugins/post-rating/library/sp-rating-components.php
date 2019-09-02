<?php
/*
Simple:Press
Post Rating Plugin Support Routines
$LastChangedDate: 2017-12-31 09:40:24 -0600 (Sun, 31 Dec 2017) $
$Rev: 15619 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_rating_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'post-rating/sp-rating-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-rating')."'>".__('Uninstall', 'sp-rating').'</a>';
        $url = SPADMINOPTION.'&amp;tab=display';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-rating')."'>".__('Options', 'sp-rating').'</a>';
    }
	return $actionlink;
}

function sp_rating_do_load_js($footer) {
    $script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? PRSCRIPT.'sp-rating.js' : PRSCRIPT.'sp-rating.min.js';
	SP()->plugin->enqueue_script('spratings', $script, array('jquery'), false, $footer);
}

function sp_rating_do_header() {
	$css = SP()->theme->find_css(PRCSS, 'sp-rating.css', 'sp-rating.spcss');
    SP()->plugin->enqueue_style('sp-rating', $css);
}

function sp_rating_do_create_forum() {
	spa_paint_checkbox(__('Enable post rating on this forum', 'sp-rating'), 'forum_ratings', 1);
}

function sp_rating_do_edit_forum($forum) {
	spa_paint_checkbox(__('Enable post rating on this forum', 'sp-rating'), 'forum_ratings', $forum->post_ratings);
}

function sp_rating_do_deactivate() {
    # deactivation so make our auth not active
    SP()->auths->deactivate('rate_posts');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-rating');
}

function sp_rating_do_topic_delete($posts) {
	# remove any post ratings in this topic
	$thisTopic = (is_object($posts)) ? $posts : $posts[0];
	$rated = SP()->DB->select('SELECT '.SPRATINGS.'.post_id, members FROM '.SPRATINGS.'
			 JOIN '.SPPOSTS.' ON '.SPRATINGS.'.post_id = '.SPPOSTS.'.post_id
			 WHERE '.SPPOSTS.".topic_id=$thisTopic->topic_id");
	if ($rated) {
		foreach ($rated as $post) {
			# remove the post rating
			SP()->DB->execute('DELETE FROM '.SPRATINGS." WHERE post_id=$post->post_id");
		}
	}
    SP()->activity->delete('type='.SPACTIVITY_RATING."&meta=$thisTopic->topic_id");
}

function sp_rating_do_post_delete($post) {
	# remove post ratings
	SP()->DB->execute('DELETE FROM '.SPRATINGS." WHERE post_id=$post->post_id");
	SP()->activity->delete('type='.SPACTIVITY_RATING."&item=$post->post_id");
}

function sp_rating_do_member_del($userid) {
    $ratings = SP()->activity->get('type='.SPACTIVITY_RATING."&uid=$userid");

	if (!empty($ratings)) {
		foreach ($ratings as $item) {
			sp_remove_postrated($item->item_id, $userid);
		}
	}
    SP()->activity->delete('type='.SPACTIVITY_RATING."&uid=$userid");
}
