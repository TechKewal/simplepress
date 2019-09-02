<?php
/*
Simple:Press
Template Tag(s) - Blog Linking Specific
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/* 	=====================================================================================

	spBlogTopicLink($postid, $show_img)

	Allows display of forum topic link for blog linked post outside of the post content

	parameters:

		$postid			id of the blog post					number			required
		$show_img		display blog linked image			true/fase		default true
 	===================================================================================*/

function spBlogTopicLinkTag($postid, $show_img) {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

    $postid = SP()->filters->integer($postid);
    if (empty($postid)) return;
    $links = sp_blog_links_control('read', $postid);

    if($links) sp_forum_ajax_support();
    if ($links && SP()->auths->can_view($links->forum_id, 'topic-title')) {
		echo sp_transform_bloglink_label($postid, $links, $show_img);
	}
}

/* 	=====================================================================================

	spLinkedTopicPostCountTag()

	displays the number of topic posts in the currently displayed linked blog post

	parameters: None

	For use with in the wp loop

 	===================================================================================*/

function spLinkedTopicPostCountTag() {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	global $wp_query;
	$result = '';
	$postid = $wp_query->post->ID;
	$links = sp_blog_links_control('read', $postid);
    if($links) sp_forum_ajax_support();
    if ($links && SP()->auths->can_view($links->forum_id, 'topic-title')) {
		# link found for this post
		$result = sp_get_posts_count_in_linked_topic($links->topic_id, false);
		echo $result;
	}
}

/* 	=====================================================================================

	spFirstTopicPostLinkTag($blog_post_id, $link_text

	Creates a link to the first topic post in a blog post/topic linked thread

	parameters:

		$blog_post_id		The ID pof the blog post ($post->ID in Post Loop)
		$link text			What text to display as the link
 	===================================================================================*/

function spFirstTopicPostLinkTag($blogpostid, $linktext) {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	$topiclink='';
    $blogpostid = SP()->filters->integer($blogpostid);
	if($blogpostid) {
		$links = sp_blog_links_control('read', $blogpostid);
	    if($links) sp_forum_ajax_support();
	    if ($links && SP()->auths->can_view($links->forum_id, 'topic-title')) {
			$postid = SP()->DB->table(SPPOSTS, "topic_id=".$links->topic_id." AND post_index=1", 'post_id');
			$topiclink = '<a href="'.SP()->spPermalinks->build_url(SP()->DB->table(SPFORUMS, "forum_id=".$links->forum_id, 'forum_slug'), SP()->DB->table(SPTOPICS, "topic_id=".$links->topic_id, 'topic_slug'), 1, $postid, 1).'">'.$linktext.'</a>';
			echo $topiclink;
		}
	}
}

/* 	=====================================================================================

	spLastTopicPostLinkTag($blog_post_id, $link_text

	Creates a link to the last topic post in a blog post/topic linked thread

	parameters:

		$blog_post_id		The ID pof the blog post ($post->ID in Post Loop)
		$link text			What text to display as the link
 	===================================================================================*/

function spLastTopicPostLinkTag($blogpostid, $linktext) {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	$topiclink='';
    $blogpostid = SP()->filters->integer($blogpostid);
	if($blogpostid) {
		$links = sp_blog_links_control('read', $blogpostid);
	    if($links) sp_forum_ajax_support();
	    if ($links && SP()->auths->can_view($links->forum_id, 'topic-title')) {
			$postid = SP()->DB->table(SPPOSTS, "topic_id=".$links->topic_id, 'post_id', 'post_index DESC', '1');
			$topiclink = '<a href="'.SP()->spPermalinks->build_url(SP()->DB->table(SPFORUMS, "forum_id=".$links->forum_id, 'forum_slug'), SP()->DB->table(SPTOPICS, "topic_id=".$links->topic_id, 'topic_slug'), 1, $postid, 1).'">'.$linktext.'</a>';
			echo $topiclink;
		}
	}
}
