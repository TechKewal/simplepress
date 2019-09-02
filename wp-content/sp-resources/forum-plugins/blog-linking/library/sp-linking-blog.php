<?php
/*
Simple:Press
Blog Linking - Blog side support routines
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

require_once SPBLLIB.'sp-linking-support.php';

# ------------------------------------------------------------------
# sp_save_blog_link()
#
# Filter call
# Called on  a Post Save to create the blog/forum Link
#	$postid		id of the blog post to link to
# ------------------------------------------------------------------
function sp_save_blog_link($postid) {
	# can the user do this?
	if (isset($_POST['sflink']) && $_POST['sflink']) {
		$blogpost = sp_get_postrecord($postid);
		if ($blogpost) {
			# if revision or autosave go get parent post id
			if ($blogpost->post_type == 'revision' && $blogpost->post_parent > 0) $postid = $blogpost->post_parent;

			# Prepare data
			$forumid = SP()->filters->integer($_POST['sfforum']);

			# Allow the target forum ID to be changed
			$forumid = apply_filters('sph_blog_linked_forum', $forumid);

			if (empty($forumid) || $forumid == 0) return;
			$editmode = (isset($_POST['sfedit'])) ? 1 : 0;

			# Check if we already have a topic
			$topicid = '0';
			$checktopic = sp_get_linkedtopic($postid);
			if (!empty($checktopic) && $checktopic > 0) $topicid = $checktopic;

			# go get link record if already saved before
			$links = sp_blog_links_control('read', $postid);
			if ($links) {
				# if all links fields have value no need to linger
				if (($links->forum_id == $forumid) && ($links->topic_id == $topicid) && ($links->syncedit == $editmode)) return;
			}

			# Save links record - will update of already exists
			sp_blog_links_control('save', $postid, $forumid, $topicid, $editmode);
		}
	}
}

# ------------------------------------------------------------------
# sp_publish_blog_link()
#
# Filter call
# Called on  a Post Publish to create the blog/forum Link
#	$post		The Actual Post Object
# ------------------------------------------------------------------
function sp_publish_blog_link($post) {
	global $published;

	if ($published) return;

	# Check post status for published as it calls this hook regardless of state
	if ($post->post_status != 'publish') return;

	# go get link record if already saved before
	$links = sp_blog_links_control('read', $post->ID);

	if (!$links) return;

	# Check if we already have a topic
	$topicid = '0';
	$checktopic = sp_get_linkedtopic($post->ID);
	if (!empty($checktopic) && $checktopic > 0) $topicid = $checktopic;
	if ($links) {
		if ($links->topic_id == $topicid && $topicid > 0) return;
	}

	# Prepare data
	$forumid = 0;
	if (isset($_POST['sfforum'])) {
		$forumid = SP()->filters->integer($_POST['sfforum']);
		# Allow the target forum ID to be changed
		$forumid = apply_filters('sph_blog_linked_forum', $forumid);
	} else {
		if ($links) $forumid = $links->forum_id;
	}

	if (empty($forumid) || $forumid == 0) return;

	$published = true;

	$forumslug = SP()->DB->table(SPFORUMS, 'forum_id='.$forumid, 'forum_slug');

	$sfpostlinking = array();
	$sfpostlinking = SP()->options->get('sfpostlinking');

	require_once SP_PLUGIN_DIR.'/forum/library/sp-post-support.php';
	require_once SP_PLUGIN_DIR.'/forum/database/sp-db-newposts.php';
	require_once SP_PLUGIN_DIR.'/forum/database/sp-db-management.php';

	# Initialise the class -------------------------------------------------------------
	$p = new spcPost;

	# Set up curret user details needed to keep class user agnostic
	$p->userid		= $post->post_author;
	$p->admin 		= true;
	$p->moderator	= true;
	$p->member		= true;
	$p->guest		= false;

	$p->action		= 'topic';

	$p->newpost['forumid'] 		= $forumid;
	$p->newpost['forumslug'] 	= $forumslug;
	$p->newpost['topicname']	= SP()->saveFilters->title($post->post_title, SPTOPICS, 'topic_name');

	# Permission checks on forum data
	$p->validatePermission();
	if (!$p->abort) {
		$p->newpost['postcontent']	= sp_prepare_linked_topic_content($post->post_content, $post->post_excerpt, $sfpostlinking, $post->post_type);
		$p->newpost['userid']		= $post->post_author;
    	$p->newpost['postername'] 	= SP()->user->thisUser->display_name;
    	$p->newpost['posteremail'] 	= SP()->user->thisUser->user_email;
		$p->newpost['posterip']		= sp_get_ip();

		$p->validateData();
		if (!$p->abort) {

			$p->saveData();
			if (!$p->abort) {
				$editmode = '0';
				if ((isset($_POST['sfedit']) && $_POST['sfedit'] == true) || $links->syncedit == '1') $editmode = '1';

				# and then update links table with forum AND topic
				sp_blog_links_control('save', $post->ID, $forumid, $p->newpost['topicid'], $editmode);

				# sync blog and forum tags
				sp_sync_blog_tags($post->ID, $forumid, $topicid);

				# Add the blog post id into the topic record
				$sql = "UPDATE ".SPTOPICS." SET blog_post_id = ".$post->ID." WHERE topic_id = ".$p->newpost['topicid'];
				SP()->DB->execute($sql);
			} else {
				trigger_error('Linking - Saving'.': '.$p->message, E_USER_WARNING);
			}
		} else {
			trigger_error('Linking - Validation'.': '.$p->message, E_USER_WARNING);
		}
	} else {
		trigger_error('Linking - Permission'.': '.$p->message, E_USER_WARNING);
	}
}

# ------------------------------------------------------------------
# sp_prepare_linked_topic_content()
#
# prepares blog post content for the topic post
# 	$content		full content o the blog post
#	$excerpt		excerpt of the blog post
#	$sfpostlinking	link options
# ------------------------------------------------------------------
function sp_prepare_linked_topic_content($content, $excerpt, $sfpostlinking, $postType) {
	$content = SP()->saveFilters->content($content, 'new', true, SPPOSTS, 'post_content');

	# if type page and excerpt is selected it needs to be content as pages have no excerpt
	if($sfpostlinking['sflinkexcerpt'] == 3 && $postType == 'page') $sfpostlinking['sflinkexcerpt'] = 1;

	switch($sfpostlinking['sflinkexcerpt']) {
		case 3:
    		$postcontent = SP()->saveFilters->content($excerpt, 'new', true, SPPOSTS, 'post_content');
    		break;

		case 2:
    		$postcontent = sp_make_excerpt($content, $sfpostlinking['sflinkwords']);
    		break;

		default:
    		$postcontent = $content;
    		break;
	}

	return apply_filters('sph_add_custom_post_content', $postcontent);
}

# ------------------------------------------------------------------
# sp_update_blog_link()
#
# Filter call
# Called on a Post Edit to update the blog/forum Link
#	$postid		id of the blog post to link to
# ------------------------------------------------------------------
function sp_update_blog_link($postid) {
	# This could be an update to post content OR a new link on existing post so check
	# If new link on exisiting post this gets run before the 'save_post' hook so we need to force a save post first
	sp_save_blog_link($postid);

	$links = sp_blog_links_control('read', $postid);

	if ($links && $links->topic_id == 0) {
		# then a new link on existing blog post so get post object
		$postrecord = sp_get_postrecord($postid);
		if ($postrecord) {
			sp_publish_blog_link($postrecord);
			return;
		}
	}

	# probably an edit to content then
	if ((isset($_POST['sfedit']) && $_POST['sfedit'] == true) || ($links && $links->syncedit == true)) {
		$post = SP()->DB->table(SPWPPOSTS, 'ID='.$postid, 'row');
		if ($post) {
			# first - get the options
			$sfpostlinking = array();
			$sfpostlinking = SP()->options->get('sfpostlinking');

			$postcontent = sp_prepare_linked_topic_content($post->post_content, $post->post_excerpt, $sfpostlinking, $post->post_type);

			$sql = "UPDATE ".SPPOSTS." SET post_content='".$postcontent."' WHERE topic_id=".$links->topic_id." AND post_index=1";
			SP()->DB->execute($sql);
		}
	}
}

# ------------------------------------------------------------------
# sp_show_blog_link()
#
# Filter call
# Adds the user-defined link text to a blog post
#	$content	The content of the target post
# ------------------------------------------------------------------
function sp_show_blog_link($content) {
	global $wp_query;

	if (!isset($wp_query->post->ID)) return $content;

	$postid = $wp_query->post->ID;
	if ($postid == SP()->options->get('sfpage')) return $content;

	$links = sp_blog_links_control('read', $postid);
	if (!$links) return $content;

	#show only on single pages?
	$sfpostlinking = SP()->options->get('sfpostlinking');
	if ($sfpostlinking['sflinksingle'] && !is_single()) return $content;

	$out = sp_transform_bloglink_label($postid, $links, true);

	if ($sfpostlinking['sflinkabove']) {
		return $out.$content;
	} else {
		return $content.$out;
	}
}

# ------------------------------------------------------------------
# sp_delete_blog_link()
#
# Action call
# Removes forum link if blog post is deleted
#	$postid		ID of the post being deleted
# ------------------------------------------------------------------
function sp_delete_blog_link($postid) {
	$links = sp_blog_links_control('read', $postid);
	if ($links) {
		if (!SP()->auths->get('break_linked_topics', $links->forum_id)) return;
		# Check - this might be a Revision record
		if ($links->forum_id != 0) sp_break_blog_link($links->topic_id, $postid);
	}
}

# ------------------------------------------------------------------
# sp_add_admin_link_column()
#
# Filter call
# Adds link column to edit posts/pages
# ------------------------------------------------------------------
function sp_add_admin_link_column($defaults) {
    $defaults['spflinked'] = __('Forum Linked', 'sp-linking');
    return $defaults;
}

# ------------------------------------------------------------------
# sp_add_admin_link_column()
#
# Action call
# Displays link column info in edit posts/pages
# ------------------------------------------------------------------
function sp_show_admin_link_column($column, $postid) {
	if ($column == 'spflinked') {
		$links = sp_blog_links_control('read', $postid);
		if ($links) echo(__("Linked to forum", 'sp-linking').':<br />'.SP()->DB->table(SPFORUMS, 'forum_id='.$links->forum_id, 'forum_name'));
	}
}

function sp_get_postrecord($postid) {
	return SP()->DB->table(SPWPPOSTS, 'ID='.$postid, 'row');
}

function sp_get_linkedtopic($blogpostid) {
	return SP()->DB->table(SPTOPICS, "blog_post_id=$blogpostid", 'topic_id');
}

# ------------------------------------------------------------------
# sp_make_excerpt()
#
# Creates an excerpt of x number of words from post content
#	$content	The text of the post
#	$words		Word count required (defaults to 50)
# ------------------------------------------------------------------
function sp_make_excerpt($content, $words) {
	if ((empty($words)) || ($words == 0)) $words = 50;
	if ($content != '') {
		$length = $words;
		$content = str_replace(']]>', ']]&gt;', $content);
		if ($length > count(preg_split('/[\s]+/', strip_tags($content), -1))) return $content;
		$text_bits = preg_split('/([\s]+)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
		$in_tag = false;
		$n_words = 0;
		$content = '';
		foreach ($text_bits as $chunk) {
			if (0 < preg_match('/<[^>]*$/s', $chunk)) {
				$in_tag = true;
			} elseif (0 < preg_match('/>[^<]*$/s', $chunk)) {
				$in_tag = false;
			}
			if (!$in_tag && '' != trim($chunk) && substr($chunk, -1, 1) != '>') $n_words++;
			$content.= $chunk;
			if ($n_words >= $length && !$in_tag) break;
		}
		$content = $content.'&#8230;';
		$content = force_balance_tags($content);
	}
	return $content;
}

# ------------------------------------------------------------------
# sp_transform_bloglink_label()
#
# Formats the label to display on blog posts
#	$postid		WP Post id of the link
#	$links		forum/topic postmeta data
#	$show_img	Support template tag no image option
# ------------------------------------------------------------------
function sp_transform_bloglink_label($postid, $links, $show_img=true) {
	$out = '';
	$sfpostlinking = array();
	$sfpostlinking = SP()->options->get('sfpostlinking');
	$text = SP()->displayFilters->title($sfpostlinking['sflinkblogtext']);
	$icon = SP()->theme->paint_icon('', SPBLIMAGES, "sp_BlogLink.png");
	$postcount = sp_get_posts_count_in_linked_topic($links->topic_id, false);
	$href = '<a href="'.SP()->spPermalinks->build_url(SP()->DB->table(SPFORUMS, 'forum_id='.$links->forum_id, 'forum_slug'), SP()->DB->table(SPTOPICS, 'topic_id='.$links->topic_id, 'topic_slug'), 1, 0).'">';
	if (!$postcount) {
		# break the link
		sp_blog_links_control('delete', $postid);
		return $out;
	}
    if ($show_img) {
		if(strpos($text, '%ICON%') !== false) $text = str_replace('%ICON%', $icon, $text);
	} else {
		$text = str_replace('%ICON%', '', $text);
	}
	if (strpos($text, '%FORUMNAME%') !== false) $text = str_replace('%FORUMNAME%', SP()->DB->table(SPFORUMS, 'forum_id='.$links->forum_id, 'forum_name'), $text);
	if (strpos($text, '%TOPICNAME%') !== false) $text = str_replace('%TOPICNAME%', SP()->DB->table(SPTOPICS, 'topic_id='.$links->topic_id, 'topic_name'), $text);
	if (strpos($text, '%POSTCOUNT%') !== false) $text = str_replace('%POSTCOUNT%', $postcount, $text);
	if (strpos($text, '%LINKSTART%') !== false) {
		$text = str_replace('%LINKSTART%', $href, $text);
	} else {
		$text = $href.$text;
	}
	if (strpos($text, '%LINKEND%') !== false) {
		$text = str_replace('%LINKEND%', '</a>', $text);
	} else {
		$text = $text.'</a>';
	}
	$out = '<div class="spForumLink"><span>'.$text.'</span></div>';
    $out = apply_filters('sph_blog_link_label', $out, $postid);
	return $out;
}

# ------------------------------------------------------------------
# sp_get_posts_count_in_linked_topic()
#
# Returns the post count of approved posts in topic
#	$topicid:		Topic to lookup
#	$hidedupes		Hide duplicate comments
# ------------------------------------------------------------------
function sp_get_posts_count_in_linked_topic($topicid, $hidedupes) {
	if (empty($topicid)) return 0;
	$hide='';
	if ($hidedupes) $hide = ' AND comment_id IS NULL';
	$c = SP()->DB->count(SPPOSTS, "topic_id=$topicid AND post_status=0 $hide");
	if (!$c) $c = 0;
	return $c;
}

#------------------------------------------------------------------
# sp_sync_blog_tags()
#
# Creates topic tags based on blog post tags if used
#	$postid		WP Post id of the link
#	$data		forum/topic postmeta data
#	REQUIRES THE TAGS PLUGIN TO BE ACTIVE
# ------------------------------------------------------------------
function sp_sync_blog_tags($postid, $forumid, $topicid) {
	if (SP()->plugin->is_active('tags/sp-tags-plugin.php')) {
		# get tags for wp blog post
		$terms = apply_filters('get_the_tags', wp_get_object_terms($postid, 'post_tag'));
		$terms = apply_filters('sph_add_tags_to_topic', $terms, $postid);

		# get the forum id and topic id ($data => forumid@topicid)
		$forum = SP()->DB->table(SPFORUMS, "forum_id=$forumid", 'row');

		# only do tags if the forum is setup for tags and the blog post has tags
		if ($forum->use_tags && $terms) {
			$tags = array();
			foreach ($terms as $term) {
				$tags[] = $term->name;
			}
			# need tags in a list
			$tags = implode(",", $tags);

		    require_once SPTLIBDIR.'sp-tags-database.php';

			# now save the topic tags but in case its an update use change routine
			sp_change_topic_tags($topicid, $tags);
		}
	}
}
