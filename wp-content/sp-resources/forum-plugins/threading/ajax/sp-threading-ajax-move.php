<?php
/*
Simple:Press
Threading Ajax Routines - Move
$LastChangedDate: 2016-06-25 14:15:02 +0100 (Sat, 25 Jun 2016) $
$Rev: 14332 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# load up support
sp_forum_ajax_support();

# check ajax nonce
if (!sp_nonce('sp-thread-move')) die();

# check move button was clicked on tools form
if (!isset($_POST['makethreadmove'])) die();

# extract data from POST
$postid			= SP()->filters->integer($_POST['postid']);
$oldtopicid 	= SP()->filters->integer($_POST['oldtopicid']);
$oldforumid 	= SP()->filters->integer($_POST['oldforumid']);
$oldthreadindex = SP()->filters->str($_POST['oldthreadindex']);
$action 		= SP()->filters->str($_POST['moveaction']);

$newforumid		= SP()->filters->integer($_POST['newforumid']);
$newtopicname	= SP()->filters->str($_POST['newtopicname']);

# check user permissions
if (!SP()->auths->get('move_posts', $oldforumid) || !SP()->auths->get('move_posts', $newforumid)) {
	wp_redirect(htmlspecialchars($_SERVER['HTTP_REFERER'], ENT_QUOTES, 'UTF-8'));
	die();
}

# make sure we have some other values as well
if (empty($newforumid) || empty($newtopicname)) {
    echo($newforumid. ' --- ' . $newtopicname);
    die();
}

# grab the post count and post IDs in an array
$postlist = array();
if ($action == 1) {
	# single post move
	$postlist[] = $postid;
} else {
	# thread move
	$p = explode('.', $oldthreadindex);
	$postlist = SP()->DB->select('SELECT post_id
							FROM '.SPPOSTS.' WHERE topic_id = '.$oldtopicid.'
							AND LEFT(thread_index, 4) = "'.$p[0].'"
							ORDER BY post_index', 'col');
}

# now to make new topic
$newtopicname  = SP()->saveFilters->title($newtopicname);
$newtopicslug = sp_create_slug($newtopicname, true, SPTOPICS, 'topic_slug');
$c = count($powtlist);
$sql = 'INSERT INTO '.SPTOPICS."
		(topic_name, topic_slug, topic_date, forum_id, post_count, post_id, post_count_held, post_id_held)
		VALUES
		('$newtopicname', '$newtopicslug', now(), $newforumid, $c, $postid, $c, $postid);";
if (SP()->DB->execute($sql) == false) {
	SP()->notifications->message(SPFAILURE, SP()->primitives->front_text('Post move failed'));
	wp_redirect(htmlspecialchars($_SERVER['HTTP_REFERER'], ENT_QUOTES, 'UTF-8'));
	die();
}
$newtopicid = SP()->rewrites->pageData['insertid'];

# check the topic slug and if empty use the topic id
if (empty($newtopicslug)) {
	$newtopicslug = 'topic-'.$newtopicid;
	$thistopic = SP()->DB->execute('UPDATE '.SPTOPICS." SET
							topic_slug='$newtopicslug'
							WHERE topic_id=$newtopicid");
}

# loop through and update post records
foreach ($postlist as $post) {
	# update post record
	$sql = 'UPDATE '.SPPOSTS." SET
			topic_id=$newtopicid,
			forum_id=$newforumid,
			post_status=0
			WHERE post_id=$post";
	SP()->DB->execute($sql);

	# update post if in sfwaiting
	SP()->DB->execute("UPDATE ".SPWAITING." SET forum_id=$newforumid, topic_id=$newtopicid WHERE post_id=$post");

	# notify author of move
	$thisPost = SP()->DB->table(SPPOSTS, "post_id=$post", 'row');
	$sfadminsettings = SP()->options->get('sfadminsettings');
	if ($sfadminsettings['movenotice'] && SP()->user->thisUser->ID != $thisPost->user_id) {
		$nData = array();
		$nData['user_id']		= $thisPost->user_id;
		$nData['guest_email']	= $thisPost->guest_email;
		$nData['post_id']		= $post;
		$nData['link']			= SP()->spPermalinks->permalink_from_postid($post);
		$nData['link_text']		= SP()->DB->table(SPTOPICS, "topic_id=$thisPost->topic_id", 'topic_name');
		$nData['message']		= SP()->primitives->front_text('A post of yours was moved to');
		$nData['expires']		= time() + (30 * 24 * 60 * 60); # 30 days; 24 hours; 60 mins; 60secs
		SP()->notifications->add($nData);
	}
}

# some housekeeping

# flush and rebuild topic cache (since one or more posts approved)
SP()->meta->rebuild_topic_cache();

# rebuild indexing on target topic and forum
sp_build_post_index($newtopicid);
sp_build_forum_index($newforumid);

# determine if any posts left in old topic - just in case - delete or reindex
$sql = "SELECT post_id FROM ".SPPOSTS." WHERE topic_id = $oldtopicid";
$posts = SP()->DB->select($sql, 'col');
if (empty($posts)) {
	SP()->DB->execute("DELETE FROM ".SPTOPICS." WHERE topic_id=".$oldtopicid);
} else {
	sp_build_post_index($oldtopicid);
	sp_build_forum_index($oldforumid);
}

do_action('sph_move_post', $oldtopicid, $newtopicid, $newforumid, $oldforumid, $postid, SP()->user->thisUser->ID);

# get forum slug for redirect
$newforumslug = SP()->DB->table(SPFORUMS, "forum_id=$newforumid", 'forum_slug');

SP()->notifications->message(SPSUCCESS, SP()->primitives->front_text('Moved completed'));

wp_redirect(SP()->spPermalinks->build_url($newforumslug, $newtopicslug));

die();
