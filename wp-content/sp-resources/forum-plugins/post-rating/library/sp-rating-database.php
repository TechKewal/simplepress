<?php
/*
Simple:Press
Post Rating Plugin Database Routines
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# ------------------------------------------------------------------
# sp_update_postratings()
#
# Upates post ratings
#	$postid:		post_id
#	$count:			number of votes
#	$sum:			ratings sum
#	$ips:			array of ips voted for guests
#	$members:		members that have voted
# Note: No permission checking is performed
# ------------------------------------------------------------------
function sp_update_postratings($postid, $count, $sum, $ips, $members) {
	if (!$postid) return '';
	$sql = ('UPDATE '.SPRATINGS." SET vote_count=$count, ratings_sum=$sum, ips='$ips', members='$members' WHERE post_id=$postid");
	SP()->DB->execute($sql);

	do_action('sph_post_rating_add', $postid, $count, $sum, SP()->user->thisUser->ID);
}

# ------------------------------------------------------------------
# Add post ratings
#	$postid:		post_id
#	$count:			number of votes
#	$sum:			ratings sum
#	$ips:			array of ips voted for guests
#	$members:		members that have voted
# Note: No permission checking is performed
# ------------------------------------------------------------------
function sp_add_postratings($postid, $count, $sum, $ips, $members) {
	if (!$postid) return '';

	$sql = ('INSERT INTO '.SPRATINGS." (post_id, vote_count, ratings_sum, ips, members) VALUES ($postid, $count, $sum, '$ips', '$members')");
	SP()->DB->execute($sql);

	do_action('sph_post_rating_add', $postid, $count, $sum, SP()->user->thisUser->ID);
}

# ------------------------------------------------------------------
# sp_get_topic_ratings()
#
# Returns post ratings
#	$topicid:		post_id of post to return
# Note: No permission checking is performed
# ------------------------------------------------------------------
function sp_get_topic_ratings($topicid) {
	if (!$topicid) return '';

	$sql = ('SELECT vote_count, ratings_sum	FROM '.SPRATINGS.' JOIN '.SPPOSTS.' ON '.SPPOSTS.".topic_id=$topicid WHERE ".SPRATINGS.'.post_id = '.SPPOSTS.'.post_id');
	return SP()->DB->select($sql);
}

# ------------------------------------------------------------------
# sp_add_postrating_vote()
#
# adds a post rating for user
#	$postid:		The post being voted on
# ------------------------------------------------------------------
function sp_add_postrating_vote($postid, $topicid) {
	if (!$postid || !$topicid) return '';

	# record the post as voted (in members table)
	SP()->activity->add(SP()->user->thisUser->ID, SPACTIVITY_RATING, $postid, $topicid);
}

# ------------------------------------------------------------------
# sp_remove_postrated($topic, $userid)
#
# removes the post rated id for the specified user
# $postid			postid to be removed
# $userid			user to have post rated removed
# ------------------------------------------------------------------
function sp_remove_postrated($postid, $userid) {
	if (!$userid || !$postid) return '';

	#remove the member id from post rated
	$list = SP()->DB->table(SPRATINGS, "post_id=$postid", 'members');
	if (!empty($list)) {
		$newlist = null;
		$list = unserialize($list);
		foreach ($list as $user) {
			if ($userid != $user) $newlist[] = $user;
		}
		if ($newlist) $newlist = serialize($newlist);
		$list = SP()->DB->execute('UPDATE '.SPRATINGS." SET members='$newlist' WHERE post_id=$postid");

		do_action('sph_post_rating_removed', $postid, $userid);
	}
}
