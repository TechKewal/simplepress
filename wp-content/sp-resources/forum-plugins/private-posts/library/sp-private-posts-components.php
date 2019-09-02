<?php
/*
Simple:Press
Private Posts Plugin Support Routines
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_private_posts_do_form_options($content, $thisObject) {
	global $tab;

	$out = '';
	if (SP()->auths->get('post_private', $thisObject->forum_id)) {
		$out.= "<input type='checkbox' tabindex='".$tab++."' class='spControl' id='sp_private_post' name='sp_private_post' />\n";
		$out.= "<label class='spLabel spCheckbox' for='sp_private_post'>".__('Mark this post private', 'sp-private-posts')."</label><br>\n";
	}

	return $content.$out;
}

function sp_private_posts_do_save_post($newpost) {
	if (!empty($_POST['sp_private_post']) && SP()->auths->get('post_private', $newpost['forumid'])) {
       	SP()->DB->execute('UPDATE '.SPPOSTS.' SET private=1 WHERE post_id='.$newpost['postid']);
    }
}

function sp_private_posts_do_post_tool($out, $post, $forum, $topic, $page, $postnum, $useremail, $guestemail, $displayname, $br) {
	if (SP()->user->thisUser->admin || SP()->user->thisUser->moderator) {
		$out.= sp_open_grid_cell();
   		$out.= '<div class="spForumToolsPrivate">';
        if ($post['private']) {
    		$out.= '<a href="javascript:document.unmarkpostprivate'.$post['post_id'].'.submit();">';
   	    	$out.= SP()->theme->paint_icon('spIcon', SPPRIVATEPOSTSIMAGES, 'sp_ToolsNotPrivate.png').$br;
			$out.= __('Unmark post private', 'sp-private-posts').'</a>';
    		$out.= '<form action="'.SP()->spPermalinks->build_url($forum['forum_slug'], $topic['topic_slug'], $page, $post['post_id'], $post['post_index']).'" method="post" name="unmarkpostprivate'.$post['post_id'].'">';
    		$out.= '<input type="hidden" name="unmarkprivate" value="'.$post['post_id'].'" />';
        } else {
    		$out.= '<a href="javascript:document.markpostprivate'.$post['post_id'].'.submit();">';
       		$out.= SP()->theme->paint_icon('spIcon', SPPRIVATEPOSTSIMAGES, 'sp_ToolsPrivate.png').$br;
			$out.= __('Mark post private', 'sp-private-posts').'</a>';
    		$out.= '<form action="'.SP()->spPermalinks->build_url($forum['forum_slug'], $topic['topic_slug'], $page, $post['post_id'], $post['post_index']).'" method="post" name="markpostprivate'.$post['post_id'].'">';
    		$out.= '<input type="hidden" name="markprivate" value="'.$post['post_id'].'" />';
        }
   		$out.= '</form>';
   		$out.= '</div>';
		$out.= sp_close_grid_cell();
	}
	$out = apply_filters('sph_post_tool_private', $out);
    return $out;
}

function sp_private_posts_do_process_actions() {
	if (isset($_POST['unmarkprivate']) || isset($_POST['markprivate'])) {
    	if (SP()->auths->get('view_private_posts', SP()->rewrites->pageData['forumid'])) {
    		if (isset($_POST['unmarkprivate'])) {
                $postid = SP()->filters->integer($_POST['unmarkprivate']);
               	SP()->DB->execute('UPDATE '.SPPOSTS.' SET private=0 WHERE post_id='.$postid);
    		} else {
                $postid = SP()->filters->integer($_POST['markprivate']);
               	SP()->DB->execute('UPDATE '.SPPOSTS.' SET private=1 WHERE post_id='.$postid);
    		}
        }
	}
}

function sp_private_posts_do_post_records($data, $records) {
	$data->private = $records->private;

    # do we need to make this post private
	$options = SP()->options->get('private-posts');
    if ($records->private) {
        if (!SP()->auths->get('view_private_posts', $records->forum_id) && SP()->user->thisUser->ID != $records->user_id) {
            $data->post_content = $options['private-text'];

            # lets add a private class to post content container
            add_filter('sph_PostIndexContent_args', 'sp_prviate_posts_add_private_class');
        } else {
            $data->post_content = '<div class="spPrivateContent">'.$options['private-text'].'</div>'.$data->post_content;
        }
    }

	return $data;
}

function sp_prviate_posts_do_add_private_class($args) {
    if (SP()->forum->view->thisPost->private && !SP()->auths->get('view_private_posts', SP()->forum->view->thisTopic->forum_id) && SP()->user->thisUser->ID != SP()->forum->view->thisPost->user_id) {
        $args['tagClass'].= ' spPrivateContent';
    }

    return $args;
}

function sp_private_posts_do_view_content($auth, $forumid, $view, $userid, $posterid, $topicid, $postid) {
	$private = SP()->DB->table(SPPOSTS, 'post_id='.$postid, 'private');
    if ($private && !SP()->auths->get('view_private_posts', $forumid) && $userid != $posterid) $auth = false;

    return $auth;
}
