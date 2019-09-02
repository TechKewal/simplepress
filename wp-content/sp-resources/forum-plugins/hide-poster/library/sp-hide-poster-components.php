<?php
/*
Simple:Press
Hide Posters Plugin Support Routines
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_hide_poster_do_create_forum() {
	spa_paint_checkbox(__('Enable ability to hide posters on this forum', 'sp-hide-poster'), 'forum_hide_posters', 1);
}

function sp_hide_poster_do_edit_forum($forum) {
	spa_paint_checkbox(__('Enable ability to hide posters on this forum', 'sp-hide-poster'), 'forum_hide_posters', $forum->forum_hide_posters);
}

function sp_hide_poster_do_topic_form_options($display, $thisForum) {
	global $tab;

	$out = '';
	if ($thisForum->forum_hide_posters && SP()->auths->get('hide_posters', $thisForum->forum_id)) {
    	$options = SP()->options->get('hide-poster');
        $checked = ($options['default_enable']) ? " checked='checked'" : '';
		$out.= "<input type='checkbox' tabindex='".$tab++."' class='spControl' name='hideposter' id='sfhideposter'$checked />";
        $label = apply_filters('sph_hide_poster_form_label', __('Hide posters in this topic', 'sp-hide-poster'));
		$out.= '<label class="spLabel spCheckbox" for="sfhideposter">'.$label.'</label><br />';
	}

	return $display.$out;
}

function sp_hide_poster_do_save_post($newpost) {
    # only interested in new topics - bail on replies
    if ($newpost['action'] == 'post') return;

    # verify user has permisson to enable hide posters in this forum
	if (!SP()->auths->get('hide_posters', $newpost['forumid'])) return;

    # if set enable hide posters in this topic
    if (isset($_POST['hideposter'])) SP()->DB->execute('UPDATE '.SPTOPICS." SET topic_hide_posters=1 WHERE topic_id=".$newpost['topicid']);
}

function sp_hide_poster_do_forum_tool($out, $forum, $topic, $page, $br) {
	if ($forum['forum_hide_posters'] && SP()->auths->get('hide_posters', $forum['forum_id'])) {
		$out.= sp_open_grid_cell();
		$out.= '<div class="spForumToolsHidePoster">';
		$hidetext = ($topic['topic_hide_posters']) ? __('Disable hiding posters', 'sp-hide-poster') : __('Enable hiding posters', 'sp-hide-poster');
		$out.= '<a href="javascript:document.hidepposters'.$topic['topic_id'].'.submit();">';
		$out.= SP()->theme->paint_icon('spIcon', SPHIDEIMAGES, 'sp_ToolsHidePoster.png').$br;
		$out.= $hidetext.'</a>';
        $topic_slug = (isset($topic['topic_slug'])) ? $topic['topic_slug'] : '';
		$out.= '<form action="'.SP()->spPermalinks->build_url($forum['forum_slug'], $topic_slug, $page, 0).'" method="post" name="hidepposters'.$topic['topic_id'].'">';
		$out.= '<input type="hidden" name="hideposter" value="'.$topic['topic_id'].'" />';
		$out.= '<input type="hidden" name="hideposteraction" value="'.esc_attr($hidetext).'" />';
		$out.= '</form>';
		$out.= '</div>';
		$out.= sp_close_grid_cell();
	}
	$out = apply_filters('sph_post_tool_hide_poster', $out);
    return $out;
}

function sp_hide_poster_do_process_actions() {
    if (!SP()->auths->get('hide_posters', SP()->rewrites->pageData['forumid'])) return;

    if (isset($_POST['hideposteraction'])) {
        $topic = SP()->filters->integer($_POST['hideposter']);
    	$status = SP()->DB->table(SPTOPICS, "topic_id=$topic", 'topic_hide_posters');
    	$status = ($status == 1) ? 0 : 1;
    	SP()->DB->execute('UPDATE '.SPTOPICS." SET topic_hide_posters=$status WHERE topic_id=".$topic);
    }
}

function sp_hide_poster_do_feeds($item, $postList) {
    if ($postList->topic_hide_posters) $item->title = $postList->topic_name;
    return $item;
}

function sp_hide_poster_do_name($a) {
    if (isset(SP()->forum->view->thisListPost)) {
        if (SP()->forum->view->thisListPost->topic_hide_posters) $a['user'] = 0;
    } else if (isset(SP()->forum->view->thisListTopic)) {
        if (SP()->forum->view->thisListTopic->topic_hide_posters) $a['user'] = 0;
    } else if (isset(SP()->forum->view->thisTopic)) {
        if (SP()->forum->view->thisTopic->topic_hide_posters) $a['user'] = 0;
    } else if (isset(SP()->forum->view->thisSubForum)) {
        if (SP()->forum->view->thisSubForum->topic_hide_posters) $a['order'] = str_replace('U', '', $a['order']);
    } else if (isset(SP()->forum->view->thisForum)) {
        if (SP()->forum->view->thisForum->topic_hide_posters) $a['user'] = 0;
    }
    return $a;
}

function sp_hide_poster_do_avatar($avatarData) {
    if (isset(SP()->forum->view->thisListPost)) {
        if (SP()->forum->view->thisListPost->topic_hide_posters) {
			$avatarData->url = SPAVATARURL.'userdefault.png';
			$avatarData->path = SPAVATARDIR.'userdefault.png';
        }
    } else if (isset(SP()->forum->view->thisListTopic)) {
        if (SP()->forum->view->thisListTopic->topic_hide_posters) {
			$avatarData->url = SPAVATARURL.'userdefault.png';
			$avatarData->path = SPAVATARDIR.'userdefault.png';
        }
    }
    return $avatarData;
}

function sp_hide_poster_do_topic_tags($out) {
    if (isset(SP()->forum->view->thisTopic) && SP()->forum->view->thisTopic->topic_hide_posters) $out = '';
    return $out;
}

function sp_hide_poster_do_bp_activity($do, $newpost) {
    $forum = SP()->DB->table(SPFORUMS, 'forum_id='.$newpost['forumid'], 'forum_hide_posters');
    $topic = SP()->DB->table(SPTOPICS, 'topic_id='.$newpost['topicid'], 'topic_hide_posters');
    if ($forum and $topic) $do = false;
    return $do;
}
