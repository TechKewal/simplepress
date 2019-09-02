<?php
/*
$LastChangedDate: 2014-07-12 21:17:17 +0100 (Sat, 12 Jul 2014) $
$Rev: 11743 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# Add a reply button to post tools
# Add indent to threaded post display

# -- Reply Button on post toolbar --------------------------------------------------------
function sp_PostIndexThreadedReplyTag($args, $label, $toolTip) {
	# check within threading levels
	$threading = SP()->options->get('threading');
	$maxlevel = (empty($threading['maxlevel'])) ? 5 : $threading['maxlevel'];
	if(SP()->forum->view->thisPost->thread_level >= $maxlevel) return;

    # checks for displaying button
	if (SP()->forum->view->thisTopic->editmode) return;
	if (SP()->forum->view->thisPost->post_status != 0 && !SP()->user->thisUser->admin) return;
	if (!SP()->auths->get('reply_topics', SP()->forum->view->thisTopic->forum_id)) return;
	if ((SP()->core->forumData['lockdown'] || SP()->forum->view->thisTopic->forum_status || SP()->forum->view->thisTopic->topic_status) && !SP()->user->thisUser->admin) return;
    if (!SP()->auths->get('view_admin_posts', SP()->forum->view->thisTopic->forum_id) && SP()->auths->forum_admin(SP()->forum->view->thisPost->user_id)) return;
    if (SP()->auths->get('view_own_admin_posts', SP()->forum->view->thisTopic->forum_id) && !SP()->auths->forum_admin(SP()->forum->view->thisPost->user_id) && !SP()->auths->forum_mod(SP()->forum->view->thisPost->user_id) && SP()->user->thisUser->ID != SP()->forum->view->thisPost->user_id) return;

	$defs = array('tagId' 		=> 'spPostIndexThreadedReply%ID%',
				  'tagClass' 	=> 'spButton',
				  'icon' 		=> 'sp_ReplyThreaded.png',
				  'iconClass'	=> 'spIcon',
				  'echo'		=> 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PostIndexThreadedReply_args', $a);
	extract($a, EXTR_SKIP);


	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$p = (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? SPTHREADIMAGESMOB : SPTHREADIMAGES;
	$icon		= SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($icon));
	$iconClass 	= esc_attr($iconClass);
	$toolTip	= esc_attr($toolTip);
	$echo		= (int) $echo;

	$tagId = str_ireplace('%ID%', SP()->forum->view->thisPost->post_id, $tagId);

	$postId = SP()->forum->view->thisPost->post_id;
	$postIndex = SP()->forum->view->thisPost->post_index;
	$threadIndex = SP()->forum->view->thisPost->thread_index;
	$displayName = SP()->forum->view->thisPostUser->display_name;
	$extract = htmlspecialchars(SP()->displayFilters->tooltip(SP()->forum->view->thisPost->post_content, SP()->forum->view->thisPost->post_status));

	$out = "<a class='$tagClass spNewThreadButton' id='$tagId' title='$toolTip' rel='nofollow' ";
	$out.= 'data-form="spPostForm" data-type="post" data-postid="'.$postId.'" data-postindex="'.$postIndex.'" data-threadindex="'.$threadIndex.'" data-displayname="'.$displayName.'" data-extract="'.$extract.'" >';

	if (!empty($icon)) $out.= $icon;
	if (!empty($label)) $out.= SP()->displayFilters->title($label);
	$out.= "</a>\n";

	$out = apply_filters('sph_PostIndexThreadedReply', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

# -- Delete button on post toolbar - can switch to normal delete tag ---------------------
function sp_PostIndexDeleteThreadTag($args, $threadLabel, $standardLabel, $threadToolTip, $standardToolTip) {
	# check if a post in a thread. If not then go back to standard call
	$useStandard = true;
	if (count(explode('.', SP()->forum->view->thisPost->thread_index)) > 1) $useStandard = false;
	if (SP()->forum->view->thisPost->thread_parent) $useStandard = false;

	if ($useStandard) {
		return sp_PostIndexDelete($args, $standardLabel, $standardToolTip);
	}

	# now for the alternate template function
	if (SP()->forum->view->thisTopic->editmode) return;
	if (SP()->core->forumData['lockdown']) return;

	if (!SP()->auths->get('delete_any_post', SP()->forum->view->thisTopic->forum_id) && !(SP()->auths->get('delete_own_posts', SP()->forum->view->thisTopic->forum_id) && SP()->user->thisUser->ID == SP()->forum->view->thisPost->user_id)) return;

	$defs = array('tagId' 		=> 'spPostIndexDelete%ID%',
				  'tagClass' 	=> 'spButton',
				  'icon' 		=> 'sp_DeletePost.png',
				  'iconClass'	=> 'spIcon',
				  'echo'		=> 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PostIndexDeleteThread_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId				= esc_attr($tagId);
	$tagClass			= esc_attr($tagClass);
	$icon				= sanitize_file_name($icon);
	$iconClass			= esc_attr($iconClass);
	$threadToolTip		= esc_attr($threadToolTip);
	$threadLabel		= SP()->displayFilters->title($threadLabel);
	$standardToolTip	= esc_attr($standardToolTip);
	$standardLabel		= SP()->displayFilters->title($standardLabel);
	$echo				= (int) $echo;

	$tagId = str_ireplace('%ID%', SP()->forum->view->thisPost->post_id, $tagId);

	# ascertain thread parent of this post
	$parent = 0;
	$parts = explode('.', SP()->forum->view->thisPost->thread_index);
	if (count($parts) == 1) {
		$parent = -1;
		$children = 1;
	} else {
		$pThread = '';
		$children = 0;
		for($x=0;$x < (count($parts)-1); $x++) {
			$pThread.= $parts[$x].'.';
		}
		$pThread = trim($pThread, '.');
		foreach (SP()->forum->view->thisTopic->posts as $thisPost) {
			if ($thisPost->thread_index == $pThread) $parent = $thisPost->post_id;
			if ($thisPost->thread_index == SP()->forum->view->thisPost->thread_index.'.0001') $children = 1;
		}
	}

	if($parent == -1) {
		# it is the maoin, main parent of the whole thread...
		$msg = esc_js(__('Are you sure you want to delete this post AND all threaded replies?', 'sp-threading'));
		$label = $threadLabel;
		$toolTip = $threadToolTip;
	} else {
		$msg = esc_js(__('Are you sure you want to delete this post?', 'sp-threading'));
		$label = $standardLabel;
		$toolTip = $standardToolTip;
	}

    $out = '';

	$out.= '<form class="spButtonForm" action="'.SP()->spPermalinks->build_url(SP()->rewrites->pageData['forumslug'], SP()->rewrites->pageData['topicslug'], SP()->rewrites->pageData['page']).'" method="post" name="deletethread'.SP()->forum->view->thisPost->post_id.'">';
	$out.= "<a class='$tagClass' id='$tagId' title='$toolTip' rel='nofollow' href='javascript: if(confirm(\"".$msg."\")) {document.deletethread".SP()->forum->view->thisPost->post_id.".submit();}'>";
	if (!empty($icon)) $out.= SP()->theme->paint_icon($iconClass, SPTHEMEICONSURL, $icon);
	if (!empty($label)) $out.= $label;
	$out.= "</a>\n";
	$out.= '<input type="hidden" name="delthread" value="'.SP()->forum->view->thisPost->thread_index.'" />';
	$out.= '<input type="hidden" name="thepost" value="'.SP()->forum->view->thisPost->post_id.'" />';
	$out.= '<input type="hidden" name="thetopic" value="'.SP()->forum->view->thisTopic->topic_id.'" />';
	$out.= '<input type="hidden" name="theforum" value="'.SP()->forum->view->thisTopic->forum_id.'" />';
	$out.= '<input type="hidden" name="parent" value="'.$parent.'" />';
	$out.= '<input type="hidden" name="children" value="'.$children.'" />';
	$out.= '</form>';

	$out = apply_filters('sph_PostIndexDeleteThread', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}
