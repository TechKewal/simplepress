<?php
/*
Simple:Press
Blog Linking - Blog side comment support
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# ------------------------------------------------------------------
# sp_process_new_comment()
#
# NOT DIRECTLY CALLABLE - 'comment_post' & 'wp_set_comment_status'
# Fires when a new comment is added (Action hook)
#	$cid:		ID of new comment
#	$status:	"spam", "1' or "0" ("1" is approved)
#	-- OR --	"approve", "delete", "spam", "hold"
# ------------------------------------------------------------------
function sp_process_new_comment($cid, $commentstatus) {
	$commentstatus = trim($commentstatus);
	if ($commentstatus == '0' || $commentstatus == 'spam' || $commentstatus == 'hold') return;

	# First - if not approved then back out
	if ($commentstatus == '1' || $commentstatus == "approve") {
		# Next find the post ID to see if it is a linked post
		$crecord = SP()->DB->table(SPWPCOMMENTS, "comment_ID=$cid", 'row');
		if (!$crecord) return;
		# if a pingback or trackback then leave it alone
		if ($crecord->comment_type == 'pingback' || $crecord->comment_type == 'trackback') return;
		# Is it a linked post?
		$links = sp_blog_links_control('read', $crecord->comment_post_ID);
		if (!$links) return;

		# This means it is an approved comment with linking
		sp_create_post_from_comment($crecord, $links);

		# Do we delete original comment?
		$sfpostlinking = SP()->options->get('sfpostlinking');
		if ($sfpostlinking['sfkillcomment']) SP()->DB->execute("DELETE FROM ".SPWPCOMMENTS." WHERE comment_ID=".$cid);
	}

	# And just in case - was it a delete action?
	if ($commentstatus == 'delete') {
		# Check posts for thie comment ID
		$post = SP()->DB->table(SPPOSTS, "comment_ID=$cid", 'row');
		if ($post) {
			$sfpostlinking = SP()->options->get('sfpostlinking');
			if ($sfpostlinking['sfeditcomment']) sp_delete_post($post->post_id, $post->topic_id, $post->forum_id, false);
		}
	}
}

# ------------------------------------------------------------------
# sp_create_post_from_comment()
#
# Create new topic post from comment
#	$crecord:		The comment record
#	$links:			Blog Linking IDs
# ------------------------------------------------------------------
function sp_create_post_from_comment($crecord, $links) {
	require_once SP_PLUGIN_DIR.'/forum/library/sp-post-support.php';
	require_once SP_PLUGIN_DIR.'/forum/database/sp-db-newposts.php';
	require_once SP_PLUGIN_DIR.'/forum/database/sp-db-management.php';

    sp_forum_ajax_support();

	$p = new spcPost;
	$p->action = 'post';

    if (empty($crecord->user_id)) {
    	$p->userid = 0;
    	$p->admin = false;
    	$p->moderator = false;
    	$p->member = false;
    	$p->guest = true;
    	$p->newpost['guestname'] = SP()->saveFilters->name($crecord->comment_author);
        $p->newpost['guestemail'] = SP()->saveFilters->email($crecord->comment_author_email);
    } else {
        $user = SP()->user->get($crecord->user_id);
    	$p->userid	= $user->ID;
    	$p->admin = $user->admin;
    	$p->moderator = $user->moderator;
    	$p->member = $user->member;
    	$p->guest = $user->guest;
    	$p->newpost['postername'] = $user->display_name;
    	$p->newpost['posteremail'] 	= $user->user_email;
    }

	$topicid = SP()->filters->integer($links->topic_id);
	$forumid = SP()->filters->integer($links->forum_id);

	$p->newpost['forumid'] = $forumid;
	$p->newpost['topicid'] = $topicid;
	$p->newpost['forumslug'] = SP()->DB->table(SPFORUMS, "forum_id=$forumid", 'forum_slug');
	$p->newpost['topicslug'] = SP()->DB->table(SPTOPICS, "topic_id=$topicid", 'topic_slug');

	$p->validatePermission();
	if (!$p->abort) {
    	$p->newpost['topicname'] = SP()->DB->table(SPTOPICS, "topic_id=$topicid", 'topic_name');
    	$p->newpost['postcontent'] = $crecord->comment_content;
    	$p->newpost['userid'] = $p->userid;
    	$p->newpost['ip'] = $crecord->comment_author_ip;
		$p->validateData();
		if (!$p->abort) {
			$p->saveData();
			if ($p->abort) {
				trigger_error('Linking - Saving'.': '.$p->message, E_USER_WARNING);
			}

            # post saved - add in the wp comment id
			$sql = "UPDATE ".SPPOSTS." SET comment_id=$crecord->comment_ID WHERE post_id=".$p->newpost['postid'];
			SP()->DB->execute($sql);
		} else {
			trigger_error('Linking - Validation'.': '.$p->message, E_USER_WARNING);
		}
	} else {
		trigger_error('Linking - Permission'.': '.$p->message, E_USER_WARNING);
	}
}

# ------------------------------------------------------------------
# sp_update_comment_post()
#
# NOT DIRECTLY CALLABLE
# Updates topic post when comment is edited
#	$cid	Comment ID
# ------------------------------------------------------------------
function sp_update_comment_post($cid) {
	# find the post ID to see if it is a linked post
	$crecord = SP()->DB->table(SPWPCOMMENTS, "comment_ID=$cid", 'row');
	if (!$crecord) return;
	$links = sp_blog_links_control('read', $crecord->comment_post_ID);
	if (!$links) return;

	# So we need to perform an update
	$sfpostlinking = SP()->options->get('sfpostlinking');
	if ($sfpostlinking['sfeditcomment']) {
		$postcontent = $crecord->comment_content;
		if ($postcontent) {
			$postid = SP()->DB->table(SPPOSTS, "comment_id=$cid", 'post_id');
			if ($postid) {
				$postcontent = SP()->saveFilters->content($postcontent, 'edit', true, SPPOSTS, 'post_content');
				$sql = "UPDATE ".SPPOSTS." SET post_content='".$postcontent."' WHERE post_id=".$postid;
				SP()->DB->execute($sql);
			}
		}
	}
}

# ------------------------------------------------------------------
# sp_topic_as_comments()
#
# NOT DIRECTLY CALLABLE
# Adds the topic posts to the comments stream
#	$comments	Passed in by the comments_array filter
# ------------------------------------------------------------------
function sp_topic_as_comments($comments) {
	global $wp_query;

	$sfpostlinking = SP()->options->get('sfpostlinking');

	if ($comments) {
		$postid = $comments[0]->comment_post_ID;
	} else {
		$postid = $wp_query->post->ID;
	}
	$links = sp_blog_links_control('read', $postid);
	if (!$links) return $comments;

	sp_forum_ajax_support();

	$topicid = $links->topic_id;

	$thread = sp_get_thread_for_comments($topicid, $sfpostlinking['sfhideduplicate']);
	if ($thread) {
		$index = count($comments);

		foreach ($thread as $post) {
        	# quick permission check
            if (!SP()->auths->can_view($links->forum_id, 'post-content', SP()->user->thisUser->ID, $post['user_id'], $post['topic_id'], $post['post_id'])) continue;

            $comments[$index] = new stdClass();
			$comments[$index]->comment_ID = $links->forum_id.'@'.$links->topic_id.'@'.$post['post_id'];
			$comments[$index]->comment_post_ID = $postid;

			if (!$post['user_id']) {
				$comments[$index]->comment_author = SP()->displayFilters->name($post['guest_name']);
				$comments[$index]->comment_author_email = SP()->displayFilters->email($post['guest_email']);
				$comments[$index]->comment_author_url = "";
			} else {
				$comments[$index]->comment_author = SP()->displayFilters->name($post['display_name']);
				$comments[$index]->comment_author_email = SP()->displayFilters->email($post['user_email']);
				$comments[$index]->comment_author_url = SP()->primitives->check_url($post['user_url']);
			}
			$comments[$index]->comment_author_IP = $post['poster_ip'];
			$comments[$index]->comment_date = $post['post_date'];
			$comments[$index]->comment_date_gmt = $post['post_date'];

			if ($post['post_status'] != 0) {
				$comments[$index]->comment_content = '<b><em>'.__("Post Awaiting Approval by Forum Administrator", 'sp-linking').'</em></b>';
			} else {
				$comments[$index]->comment_content = SP()->displayFilters->content($post['post_content']);
			}

			$comments[$index]->comment_karma = 0;
			$comments[$index]->comment_approved = 1;
			$comments[$index]->comment_agent = "";

			if ($sfpostlinking['sflinkcomments'] == 2) {
				$comments[$index]->comment_type = "comment";
			} else {
				$comments[$index]->comment_type = "forum";
			}
			$comments[$index]->comment_parent = 0;
			$comments[$index]->user_id = $post['user_id'];
			$comments[$index]->comment_subscribe = "N";

			$index++;
		}
	}
	if ($sfpostlinking['sflinkcomments'] == 2) usort($comments, 'sp_sort_comments');

	return $comments;
}

function sp_sort_comments($a, $b) {
    if ($a->comment_date == $b->comment_date) return 0;

    $sort=get_option('comment_order');
    if ($sort == 'asc') {
	    return ($a->comment_date < $b->comment_date) ? -1 : 1;
	} else {
	    return ($a->comment_date > $b->comment_date) ? 1 : -1;
	}
}

function sp_get_thread_for_comments($topicid, $hidedupes) {
	$hide = '';
	if ($hidedupes) $hide = " AND comment_id IS NULL ";

	$records = SP()->DB->select("SELECT ".SPPOSTS.".post_id, ".SPPOSTS.".topic_id, post_content, post_date, ".SPPOSTS.".user_id, guest_name, guest_email, post_status, poster_ip,
			".SPMEMBERS.".display_name, user_url, user_email
			 FROM ".SPPOSTS."
			 LEFT JOIN ".SPUSERS." ON ".SPPOSTS.".user_id = ".SPUSERS.".ID
			 LEFT JOIN ".SPMEMBERS." ON ".SPPOSTS.".user_id = ".SPMEMBERS.".user_id
			 WHERE topic_id = ".$topicid." AND post_index > 1".$hide."
			 ORDER BY post_id ".strtoupper(get_option('comment_order')).";", 'set', ARRAY_A);

	return $records;
}

# ------------------------------------------------------------------
# sp_remove_edit_comment_link()
#
# NOT DIRECTLY CALLABLE
# Sets the forum post to the 'edit' link in cmments when viewed
# bya  site admin.
# ------------------------------------------------------------------
function sp_remove_edit_comment_link($link, $id) {
	if (strpos($id, '@')) {
		$link = '';
		$target = explode('@', $id);

		if ((isset($target[0]) && $target[0] != 0) && (isset($target[1]) && $target[1] != 0) && (isset($target[2]) && $target[2] != 0)) {
			$link = '<span><form action="'.SP()->spPermalinks->build_url(SP()->DB->table(SPFORUMS, 'forum_id='.$target[0], 'forum_slug'), SP()->DB->table(SPTOPICS, 'topic_id='.$target[1], 'topic_slug'), 0, $target[2]).'" method="post" name="admineditpost'.$target[2].'">'."\n";
			$link.= '<input type="hidden" name="adminedit" value="'.$target[2].'" />'."\n";
			$link.= '<a class="comment-edit-link" href="javascript:document.admineditpost'.$target[2].'.submit();">('.__("Edit", 'sp-linking').')</a>'."\n";
			$link.= '</form></span>'."\n";
		}
	}
	return $link;
}

# ------------------------------------------------------------------
# sp_add_comment_type()
#
# NOT DIRECTLY CALLABLE
# Sets up new comment typoe of 'forum; so topic posts as comments
# can show users avatar
# 	$list	Current list of commnent types
# ------------------------------------------------------------------
function sp_add_comment_type($list) {
	$list[] = 'forum';
	return $list;
}
