<?php
/*
Simple:Press
Template Tag(s) - Linked Topics as Comments
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/* 	=====================================================================================

	spCommentsNumberTag($no_comment="0 Comments", $one_comment="1 Comment", $many_comment="% Comments", $blogcomments)

	Replaces the WP Template Tag: comments_number()
	Supplies Count of topic posts and can optionally include the standard blog comments in the total

	parameters:

		$no_comment		Used for zero comments									text
		$one_comment	Used for one comment									text
		$many_comment	Used for multiple comments								text
		$blogcomments	Include Standard Blog Comments 							(true or false)
		$postid			Option to specify postid, otherwise $wp_query is used   integer
 	===================================================================================*/

function spCommentsNumberTag($no_comment="0 Comments", $one_comment="1 Comment", $many_comment="% Comments", $blogcomments=false, $postid=0) {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	global $wp_query;
	$result = $no_comment;
	$total = 0;
	if (empty($postid)) $postid = $wp_query->post->ID;
	$links = sp_blog_links_control('read', $postid);

	# If linked get the post count (-1 of course)
    if($links) sp_forum_ajax_support();
    if ($links && SP()->auths->can_view($links->forum_id, 'forum-title')) {
		$sfpostlinking = SP()->options->get('sfpostlinking');

		# link found for this post
		$total = (sp_get_posts_count_in_linked_topic($links->topic_id, $sfpostlinking['sfhideduplicate'])-1);
	}

	# If to include standard blog comments add that number
	if($blogcomments) {
		$total += get_comments_number($postid);
	}
	if($total >= 0) {
		if($total == 1 ? $result=$one_comment : $result=str_replace('%', number_format_i18n($total), $many_comment));
	}
	echo $result;

	return;
}

function sp_do_comments_count_filter($count, $postid) {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

    $total = 0;
    $links = sp_blog_links_control('read', $postid);
    if ($links) sp_forum_ajax_support();
    if ($links && SP()->auths->can_view($links->forum_id, 'forum-title')) {
        $sfpostlinking = SP()->options->get('sfpostlinking');

        # link found for this post
        $total = sp_get_posts_count_in_linked_topic($links->topic_id, $sfpostlinking['sfhideduplicate']) - 1;
    }
    $count = $count + $total;
    return $count;
}
