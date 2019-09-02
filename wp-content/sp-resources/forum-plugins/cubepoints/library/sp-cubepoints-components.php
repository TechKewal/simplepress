<?php
/*
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# === check valid for points ===
function sp_cubepoints_valid($userid) {
	$cp = SP()->options->get('cubepoints');
	if($cp['admins'] && SP()->auths->forum_admin($userid)) return false;
	if($cp['moderators'] && SP()->auths->forum_mod($userid)) return false;
	return true;
}

# === new post/topic ===
function sp_cubepoints_do_add_post($newpost) {
	# are we logged in? (otherwise guests can get points and the world will implode)
	if ($newpost['userid'] > 0) {
		if(!sp_cubepoints_valid($newpost['userid'])) return;
		# get configured points from settings
		$cp = SP()->options->get('cubepoints');
		# add points if not currently in moderation
		if ($newpost['poststatus'] == 0) {
			if ($newpost['action'] == "post") {
				# === New Post Points ===
				sp_set_cubepoints($newpost['userid'], $cp['points_post'], $cp['logging'], 'Forum_Post');
			} elseif ($newpost['action'] == "topic") {
				# === New Topic Points ===
				sp_set_cubepoints($newpost['userid'], $cp['points_topic'], $cp['logging'], 'Forum_Topic');
			}
		}
	}
}

# === delete post ===
function sp_cubepoints_do_delete_post($oldpost) {
	$cp = SP()->options->get('cubepoints');
	if ($cp['points_delete']) {
		# check user is a registered user
		if ($oldpost->user_id > 0) {
			# negate points
			if(!sp_cubepoints_valid($oldpost->user_id)) return;
			sp_set_cubepoints($oldpost->user_id, -$cp['points_post'], $cp['logging'], 'Forum_Post_delete');
		}
	}
}

# === delete topic ===
function sp_cubepoints_do_delete_topic($posts) {
	$cp = SP()->options->get('cubepoints');
	if ($cp['points_delete']) {
		# loop through posts
    	$thisTopic = (is_array($posts)) ? $posts : array(0 => $posts);
		foreach($thisTopic as $k => $post) {
			# check user is registered
			if ($post->user_id > 0) {
				if(sp_cubepoints_valid($post->user_id)) {
					# negate points
					if ($k == 0) {
						# first post of topic (i.e, start of topic)
						sp_set_cubepoints($post->user_id, -$cp['points_topic'], $cp['logging'], 'Forum_Topic_delete');
					} else {
						# topic replies
						sp_set_cubepoints($post->user_id, -$cp['points_post'], $cp['logging'], 'Forum_Post_delete');
					}
				}
			}
		}
	}
}

# === rate user post (requires rating plugin active) ===
function sp_cubepoints_do_rate_post($postid, $count, $sum, $user_id) {
	$cp = SP()->options->get('cubepoints');
	if(sp_cubepoints_valid($user_id)) {
		if (SP()->plugin->is_active('post-rating/sp-rating-plugin.php') && $cp['points_rate_post'] && !empty($user_id)) {
			# TODO check to see if this is an updated rating or new rating - might not be used
			sp_set_cubepoints($user_id, $cp['points_rate_post'], $cp['logging'], 'Forum_Post_rating');
		}
	}

	if (SP()->plugin->is_active('post-rating/sp-rating-plugin.php') && $cp['points_post_rated'] && !empty($post->user_id)) {
		# TODO check to see if this is an updated rating or new rating - might not be used
		if(sp_cubepoints_valid($post->user_id)) {
	       	$post = SP()->DB->table(SPPOSTS, "post_id=$postid", 'row');
			sp_set_cubepoints($post->user_id, $cp['points_post_rated'], $cp['logging'], 'Forum_Post_rated');
		}
	}
}

# === create a poll (requires polls plugin active) ===
function sp_cubepoints_do_poll_created($pollid, $userid) {
	if(!sp_cubepoints_valid($userid)) return;
	$cp = SP()->options->get('cubepoints');
	if (SP()->plugin->is_active('polls/sp-polls-plugin.php') && $cp['points_create_poll'] && !empty($userid)) {
		sp_set_cubepoints($userid, $cp['points_create_poll'], $cp['logging'], 'Forum_Poll_create');
    }
}

# === vote in a poll (requires polls plugin active) ===
function sp_cubepoints_do_poll_voted($pollid, $userid, $creator) {
	$cp = SP()->options->get('cubepoints');
	if(sp_cubepoints_valid($userid)) {
		if (SP()->plugin->is_active('polls/sp-polls-plugin.php') && $cp['points_vote_poll'] && !empty($userid)) {
			sp_set_cubepoints($userid, $cp['points_vote_poll'], $cp['logging'], 'Forum_Poll_vote');
    	}
	}

	if(sp_cubepoints_valid($creator)) {
		if (SP()->plugin->is_active('polls/sp-polls-plugin.php') && $cp['points_poll_voted'] && !empty($creator)) {
			sp_set_cubepoints($creator, $cp['points_poll_voted'], $cp['logging'], 'Forum_Poll_voted');
    	}
	}
}

# shortcut function for adding points (skips logging if user chose to
function sp_set_cubepoints($userid, $points, $logging=true, $type="") {
    # make sure daily cap not exceeded
    $points = sp_cubepoints_cap_limit($userid, $points);
    if ( $points == 0) return;

	if (($logging) && ($type != "")) {
		cp_points($type, $userid, $points, '');
	} else {
		cp_alterPoints($userid, $points);
	}
}

function sp_cubepoints_do_logs_desc($type, $uid, $points, $data) {
	# check log type and display nice text
	if ($type=='Forum_Post') {
		_e('Forum Post Added', 'sp-cube');
	} elseif ($type == 'Forum_Topic') {
		_e('Forum Topic Created', 'sp-cube');
	} elseif ($type == 'Forum_Post_delete') {
		_e('Forum Post Deleted', 'sp-cube');
	} elseif ($type == 'Forum_Topic_delete') {
		_e('Forum Topic Deleted', 'sp-cube');
	} elseif ($type == 'Forum_Post_rating') {
		_e('Forum Post Rating', 'sp-cube');
	} elseif ($type == 'Forum_Post_rated') {
		_e('Forum Post Rated', 'sp-cube');
	} elseif ($type == 'Forum_Post_rating_delete') {
		_e('Forum Post Rating Deleted', 'sp-cube');
	} elseif ($type == 'Forum_Post_rated_delete') {
		_e('Forum Post Rated Deleted', 'sp-cube');
	} elseif ($type == 'Forum_Poll_create') {
		_e('Forum Poll Created', 'sp-cube');
	} elseif ($type == 'Forum_Poll_vote') {
		_e('Forum Poll Vote Cast', 'sp-cube');
	} elseif ($type == 'Forum_Poll_voted') {
		_e('Forum Poll Voted In', 'sp-cube');
	}
}

function sp_cubepoints_do_header() {
	$css = SP()->theme->find_css(SPCUBECSS, 'sp-cubepoints.css', 'sp-cubepoints.spcss');
    SP()->plugin->enqueue_style('sp-cubepoints', $css);
}

function sp_cubepoints_cap_limit($uid, $points) {
    # make sure logging enabled and that we are capping - bail if not
	$cp = SP()->options->get('cubepoints');
    if (!$cp['logging'] || $cp['points_cap'] == 0) return $points;

    # grab users points for today
    global $wpdb;
    $type = "'Forum_Post', 'Forum_Topic', 'Forum_Post_rating', 'Forum_Post_rated', 'Forum_Poll_create', 'Forum_Poll_vote', 'Forum_Poll_voted'";
    $today = strtotime('Today 00:00');
    $sql = "SELECT SUM(points) FROM ".CP_DB." WHERE type IN ($type) AND uid=$uid AND timestamp >= $today";
    $todays_points = $wpdb->get_var($sql);
    if (empty($todays_points)) $todays_points = 0;

    # calculate points that can be awarded to stay within cap
    $points_left = $cp['points_cap'] - $todays_points;
    $points = min($points, $points_left);

    return $points;
}
