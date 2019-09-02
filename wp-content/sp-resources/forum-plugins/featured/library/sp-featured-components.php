<?php
/*
Simple:Press
Featured Topics and Posts Plugin Support Routines
$LastChangedDate: 2017-08-14 21:47:04 -0500 (Mon, 14 Aug 2017) $
$Rev: 15508 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_featured_do_topic_tool($out, $forum, $topic, $page, $br) {
	if (SP()->user->thisUser->admin || SP()->user->thisUser->moderator) {
		$out.= sp_open_grid_cell();
		$out.= '<div class="spForumToolsFeatured">';
        $featured = (!empty(SP()->meta->get_value('featured', 'topics'))) ? in_array($topic['topic_id'], SP()->meta->get_value('featured', 'topics')) : 0;
		$featuretext = ($featured) ? __('Unfeature this topic', 'sp-featured') : __('Feature this topic', 'sp-featured');
		$featureaction = ($featured) ? 'remove' : 'add';
		$out.= '<a href="javascript:document.featuretopic'.$topic['topic_id'].'.submit();">';
		$out.= SP()->theme->paint_icon('spIcon', SPFEATUREDIMAGES, 'sp_ToolsFeaturedAdd.png').$br;
		$out.= $featuretext.'</a>';
		$out.= '<form action="'.SP()->spPermalinks->build_url($forum['forum_slug'], '', $page, 0).'" method="post" name="featuretopic'.$topic['topic_id'].'">';
		$out.= '<input type="hidden" name="featuretopic" value="'.$topic['topic_id'].'" />';
		$out.= "<input type='hidden' name='featuretopicaction' value='$featureaction' />";
		$out.= '</form>';
		$out.= '</div>';
		$out.= sp_close_grid_cell();
	}
	$out = apply_filters('sph_topic_tool_featured', $out);
    return $out;
}

function sp_featured_do_post_tool($out, $forum, $topic, $post, $page, $postnum, $br) {
	if (SP()->user->thisUser->admin || SP()->user->thisUser->moderator) {
		$out.= sp_open_grid_cell();
		$out.= '<div class="spTopicToolsFeatured">';
        $featured = (!empty(SP()->meta->get_value('featured', 'posts'))) ? in_array($post['post_id'], SP()->meta->get_value('featured', 'posts')) : 0;
		$featuretext = ($featured) ? __('Unfeature this post', 'sp-featured') : __('Feature this post', 'sp-featured');
		$featureaction = ($featured) ? 'remove' : 'add';
		$out.= '<a href="javascript:document.featurepost'.$post['post_id'].'.submit();">';
		$out.= SP()->theme->paint_icon('spIcon', SPFEATUREDIMAGES, 'sp_ToolsFeaturedAdd.png').$br;
		$out.= $featuretext.'</a>';
		$out.= '<form action="'.SP()->spPermalinks->build_url($forum['forum_slug'], $topic['topic_slug'], $page, 0).'" method="post" name="featurepost'.$post['post_id'].'">';
		$out.= '<input type="hidden" name="featurepost" value="'.$post['post_id'].'" />';
		$out.= "<input type='hidden' name='featurepostaction' value='$featureaction' />";
		$out.= '</form>';
		$out.= '</div>';
		$out.= sp_close_grid_cell();
	}
	$out = apply_filters('sph_post_tool_featured', $out);
    return $out;
}

function sp_featured_do_process_actions() {
    # only admins and mods
    if (!SP()->user->thisUser->admin && !SP()->user->thisUser->moderator) return;

    if (isset($_POST['featuretopic']) && !empty($_POST['featuretopicaction'])) {
        $topic = SP()->filters->integer($_POST['featuretopic']);
        $featured = (empty(SP()->meta->get_value('featured', 'topics'))) ? array() : SP()->meta->get_value('featured', 'topics');
        if ($_POST['featuretopicaction'] == 'add') {
            $featured[] = $topic;
            $featured = array_unique($featured);
        } else {
            $key = array_search($topic, $featured);
            if ($key !== false) unset($featured[$key]);
        }
        SP()->meta->add('featured', 'topics', $featured);
//        SP()->core->forumData['featured']['topics'] = $featured;
    }

    if (isset($_POST['featurepost']) && !empty($_POST['featurepostaction'])) {
        $post = SP()->filters->integer($_POST['featurepost']);
        $featured = SP()->meta->get_value('featured', 'posts');
        if ($_POST['featurepostaction'] == 'add') {
            $featured[] = $post;
            $featured = array_unique($featured);
        } else {
            $key = array_search($post, $featured);
            if ($key !== false) unset($featured[$key]);
        }
        SP()->meta->add('featured', 'posts', $featured);
//        SP()->core->forumData['featured']['posts'] = $featured;
    }
}
