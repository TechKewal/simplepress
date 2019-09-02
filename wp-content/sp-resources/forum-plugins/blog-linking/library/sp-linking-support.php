<?php
/*
Simple:Press
Blog Linking - general support routines
$LastChangedDate: 2018-12-14 15:17:56 -0600 (Fri, 14 Dec 2018) $
$Rev: 15849 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_linking_do_header() {
	$css = SP()->theme->find_css(SPBLCSS, 'sp-linking.css', 'sp-linking.spcss');
    SP()->plugin->enqueue_style('sp-linking', $css);
}

# ------------------------------------------------------------------
# sp_do_move_post_form()
#
# Set blog link id in move post popup (admin tools)
# ------------------------------------------------------------------
function sp_do_move_post_form($postid, $topicid) {
	$postIndex = SP()->DB->table(SPPOSTS, "post_id=$postid", 'post_index');
	$blogPostId = SP()->DB->table(SPTOPICS, "topic_id=$topicid", 'blog_post_id');
	$id = ($postIndex == 1) ? $blogPostId : 0;
	echo "<input type='hidden' name='blogpostid' value='$id' />";
}

# ------------------------------------------------------------------
# sp_do_move_post()
#
# Perform updates in moved post is blog linked
#	$topicid	original topic ID
#	$newtopicid new id if moved post
#	$newforumid new forum id
# ------------------------------------------------------------------
function sp_do_move_post($oldtopicid, $newtopicid, $newforumid) {
	$blogPostId = (!empty($_POST['blogpostid'])) ? SP()->filters->integer($_POST['blogpostid']) : '';
	if ($blogPostId) {
		$sql = 'UPDATE '.SPTOPICS.' SET blog_post_id='.$blogPostId.' WHERE topic_id='.$newtopicid;
		SP()->DB->execute($sql);
		sp_relink_topic($oldtopicid, $newtopicid, $newforumid);
	}
}

# ------------------------------------------------------------------
# sp_relink_topic()
#
# Relinks topic after a move
# ------------------------------------------------------------------
function sp_relink_topic($topicid, $newtopicid, $newforumid) {
	# Check if the target topic is a linked topic
	$link = SP()->DB->table(SFLINKS, "topic_id=$topicid", 'row');
	if ($link) sp_blog_links_control('update', $link->post_id, $newforumid, $newtopicid, $link->syncedit);
}

# ------------------------------------------------------------------
# sp_do_add_topic_option()
#
# Adds the option to create a blog post from a forum topic to the
# Add New Topic form Options box
# ------------------------------------------------------------------
function sp_do_add_topic_option($optionsBox, $forum) {
	global $tab;
	if (SP()->auths->get('create_linked_topics', $forum->forum_id) && current_user_can('publish_posts')) {
    	$gif = SPCOMMONIMAGES.'working.gif';
		$site = wp_nonce_url(SPAJAXURL."categories&forum=".$forum->forum_id, 'categories');
        $temp = '';
		$temp.= "<input type='checkbox' tabindex='".$tab++."' class='spControl' name='bloglink' id='sfbloglink' data-url='$site' data-img='$gif' />\n";
		$temp.= "<label class='spLabel spCheckbox' for='sfbloglink'>".esc_attr(__("Create and link blog post to this topic", "sp-linking"))."</label>\n";
        $temp.= "<div id='spCatList'></div>\n";
        $optionsBox.= apply_filters('sph_blog_link_form', $temp, $forum);
	}

	return $optionsBox;
}

# ------------------------------------------------------------------
# sp_break_blog_link()
#
# Breaks the link - removes nothing
#	$topicid	SPF Topic id of the link
#	$postid		WP Post id of the link
# ------------------------------------------------------------------
function sp_break_blog_link($topicid, $postid) {
	# dont update forum if its locked down
    if (SP()->core->forumData['lockdown']) {
		SP()->notifications->message(1, __('This forum is currently locked and access is read only and therefore cannot be updated', 'sp-linking'));
		return;
	}

	# remove from postmeta
	sp_blog_links_control('delete', $postid);

	# and set blog_oost_id to zero in topic record
	SP()->DB->execute("UPDATE ".SPTOPICS." SET blog_post_id = 0 WHERE topic_id = ".$topicid.";");
}

# ------------------------------------------------------------------
# sp_get_combined_groups_and_forums_bloglink()
#
# Grabs all groups and forums. Soecial cut down version for
# populating the blog link add post drop down
# ------------------------------------------------------------------
function sp_get_combined_groups_and_forums_bloglink() {
	# retrieve group and forum records
	$records = SP()->DB->select("SELECT ".SPGROUPS.".group_id, group_name,
			 forum_id, forum_name
			 FROM ".SPGROUPS."
			 JOIN ".SPFORUMS." ON ".SPGROUPS.".group_id = ".SPFORUMS.".group_id
			 ORDER BY group_seq, forum_seq;");
	# rebuild into an array grabbing permissions on the way
	$groups = array();
	$gindex = -1;
	$findex = 0;
	if ($records) {
		foreach($records as $record) {
			$groupid = $record->group_id;
			$forumid = $record->forum_id;
			if (SP()->auths->get('create_linked_topics', $forumid) && SP()->auths->get('start_topics', $forumid)) {
				if($gindex == -1 || $groups[$gindex]['group_id'] != $groupid) {
					$gindex++;
					$findex = 0;
					$groups[$gindex]['group_id']=$record->group_id;
					$groups[$gindex]['group_name']=$record->group_name;
				}
				if(isset($record->forum_id)) {
					$groups[$gindex]['forums'][$findex]['forum_id']=$record->forum_id;
					$groups[$gindex]['forums'][$findex]['forum_name']=$record->forum_name;
					$findex++;
				}
			}
		}
	}
	return $groups;
}

# ------------------------------------------------------------------
# sp_do_canonical_url()
#
# Set canonical url if needed
# ------------------------------------------------------------------
function sp_do_canonical_url($wpPost) {
	if (!empty(SP()->rewrites->pageData['topicslug']) && !empty($wpPost->ID) && (!isset(SP()->core->forumData['canonicalurl']) || SP()->core->forumData['canonicalurl'] == false)) {
		$sfpostlinking = SP()->options->get('sfpostlinking');
		$topic = sp_blog_links_control('read', $wpPost->ID);
		if (!empty($topic) && $sfpostlinking['sflinkurls'] == 2) {
			$forum_slug = SP()->DB->table(SPFORUMS, 'forum_id='.$topic->forum_id, 'forum_slug');
			$topic_slug = SP()->DB->table(SPTOPICS, 'topic_id='.$topic->topic_id, 'topic_slug');
			$url = SP()->spPermalinks->build_url($forum_slug, $topic_slug, 0, 0);
			echo '<link rel="canonical" href="'.$url.'" />';
			SP()->core->forumData['canonicalurl'] = true;
		}
	}
}

# ------------------------------------------------------------------
# sp_do_aioseo_canonical_url()
#
# Set canonical url for aioseo if needed
# ------------------------------------------------------------------
function sp_do_aioseo_canonical_url($url, $wpPost) {
	if (!empty(SP()->rewrites->pageData['topicslug']) && !empty($wpPost->ID) && SP()->core->forumData['canonicalurl'] == false) {
		$sfpostlinking = SP()->options->get('sfpostlinking');
		$topic = sp_blog_links_control('read', $wpPost->ID);
		if (!empty($topic) && $sfpostlinking['sflinkurls'] == 2) {
			$forum_slug = SP()->DB->table(SPFORUMS, 'forum_id='.$topic->forum_id, 'forum_slug');
			$topic_slug = SP()->DB->table(SPTOPICS, 'topic_id='.$topic->topic_id, 'topic_slug');
			$url = SP()->spPermalinks->build_url($forum_slug, $topic_slug, 0, 0);
		}
	}
	return $url;
}

# ------------------------------------------------------------------
# sp_do_switch_canonical_url()
#
# Set canonical url if being switched to blog url
# ------------------------------------------------------------------
function sp_do_switch_canonical_url($url) {
	if (!empty(SP()->rewrites->pageData['topicslug'])) {
		$sfpostlinking = SP()->options->get('sfpostlinking');
		$topic = SP()->DB->table(SPTOPICS, "topic_slug='".SP()->rewrites->pageData['topicslug']."'", 'row');
		# check for linked topic and pointing linke topic to blog post?
		if (!empty($topic) && $topic->blog_post_id && $sfpostlinking['sflinkurls'] == 3) {
			$url = get_permalink($topic->blog_post_id);
		}
	}
	return $url;
}

# ------------------------------------------------------------------
# sp_do_linking_topic_tool()
#
# Include a break link tool of appropriate and allowed
# ------------------------------------------------------------------
function sp_do_linking_topic_tool($topic, $forum, $page, $br) {
	$out = '';

	if (SP()->auths->get('break_linked_topics', $forum['forum_id']) && $topic['blog_post_id']) {
		$out.= sp_open_grid_cell();
		$out.= '<div class="spForumToolsLinking">';
		$msg = esc_js(__('Are you sure you want to break this link?', 'sp-linking'));
		$out.= '<a href="javascript: if(confirm(\''.$msg.'\')) {document.breaklink'.$topic['topic_id'].'.submit();}">';
		$out.= SP()->theme->paint_icon('spIcon', SPBLIMAGES, "sp_ToolsLink.png").$br;
		$out.= __('Break Blog Post Link', 'sp-linking').'</a>';
		$out.= '<form action="'.SP()->spPermalinks->build_url($forum['forum_slug'], $topic['topic_slug'], $page, 0).'" method="post" name="breaklink'.$topic['topic_id'].'">';
		$out.= '<input type="hidden" name="linktopic" value="'.$topic['topic_id'].'" />';
		$out.= '<input type="hidden" name="linkpost" value="'.$topic['blog_post_id'].'" />';
		$out.= '</form>';
		$out.= '</div>';
		$out.= sp_close_grid_cell();
	}
	$out = apply_filters('sph_topic_tool_linking', $out);
	return $out;
}

# ------------------------------------------------------------------
# sp_do_linking_post_edit_form()
#
# Add option to post edit submit to update blog post if linked
# ------------------------------------------------------------------
function sp_do_linking_post_edit_form($blogPostId) {
	$editchecked = '';
	$out = '';
	$links = sp_blog_links_control('read', $blogPostId);
	if ($links) {
		if($links->syncedit) $editchecked = "checked='checked'";
		$out.= "<input type='hidden' name='blogpostid' value='$blogPostId' />\n";
		$out.= "<div style='max-width: 180px;margin: auto;'><p style='text-align:left;'>";
		$out.= "<input type='checkbox' tabindex='4' id='blogpostedit' name='blogpostedit' $editchecked value='1' />\n";
		$out.= "<label for='blogpostedit'>".__('Update blog post', 'sp-linking')."</label>\n";
		$out.= "<br /><br /></p></div>";
	}
	return $out;
}

# ------------------------------------------------------------------
# sp_do_linking_update_blog_post()
#
# Update blog post after a linked topic post edit
# ------------------------------------------------------------------
function sp_do_linking_update_blog_post($newpost) {
	global $wpdb;
	$ID = SP()->filters->integer($newpost['blogpostid']);
	$postcontent = $newpost['postcontent']; # it comes here already filtered
	SP()->DB->execute("UPDATE ".$wpdb->prefix."posts SET post_content = '$postcontent' WHERE ID = $ID");
}

function sp_do_linking_merge_forums($source, $target) {
	SP()->DB->execute("UPDATE ".SFLINKS." SET forum_id=$target WHERE forum_id=$source");
	$linkOpts = SP()->options->get('sfpostlinking');
	if ($linkOpts['sfautoforum'] == $source) {
		$linkOpts['sfautoforum'] = $target;
		SP()->options->update('sfpostlinking', $linkOpts);
	}
}
