<?php
/*
Simple:Press
Buddypress Plugin activity support components
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_buddypress_do_new_post($newpost) {
	$bpdata = SP()->options->get('buddypress');

    # bail if not suupposed to add new activity
	if (!bp_is_active('activity')) return;
    if ($bpdata['activity'] == 1 || ($bpdata['activity'] == 2 && $newpost['action'] == 'post')) return;

    # now add the new post to the buddypress activity stream
    $page = SP()->spPermalinks->get_page($newpost['forumslug'], $newpost['topicslug'], $newpost['postid'], $newpost['postindex']);
    $forum_link = '<a href="'.SP()->spPermalinks->build_url($newpost['forumslug'], '', $page).'">'.$newpost['forumname'].'</a>';
    $topic_link = '<a href="'.SP()->spPermalinks->build_url($newpost['forumslug'], $newpost['topicslug'], $page).'">'.$newpost['topicname'].'</a>';

	$activity_content = addslashes($newpost['postcontent_unescaped']);

	$primary_link = $newpost['url'];
    $secondary = $newpost['postid'];
    if ($newpost['action'] == 'post') {
		$activity_action = sprintf(__('%1$s posted in the topic %2$s in the forum %3$s', 'sp-buddypress'), bp_core_get_userlink(bp_loggedin_user_id()), $topic_link, $forum_link);
        $type = 'new_forum_post';
    } else {
		$activity_action = sprintf( __( '%1$s started the topic %2$s in the forum %3$s', 'sp-buddypress'), bp_core_get_userlink(bp_loggedin_user_id()), $topic_link, $forum_link);
        $type = 'new_forum_topic';
    }

    $do_activity = apply_filters('sph_buddypress_new_activity', true, $newpost);

    if ($do_activity) {
        bp_activity_add(array(
            'action'            => $activity_action,
            'component'         => 'forum',
    		'content'           => $activity_content,
            'primary_link'      => $primary_link,
            'type'              => $type,
    		'secondary_item_id' => $secondary,
        ));
    }
}

function sp_buddypress_do_activity_filter() {
?>
								<option value="new_forum_topic"><?php echo __('Forum Topics', 'sp-buddypress'); ?></option>
								<option value="new_forum_post"><?php echo __('Forum Posts', 'sp-buddypress'); ?></option>
<?php
}

function sp_buddypress_do_activity_permission_check($data) {
    if (!empty($data['activities'])) {
        # set up stuff in case we remove entries
        $removed = false;
        $total = $data['total'];

        # loop through the activities and check permissions on the forum ones
        foreach ($data['activities'] as $index => $activity) {
            # only care about our forum stuff
            if ($activity->component != 'forum') continue;

            # load up some forum stuff
            SP()->user->get_current_user();

            # check current user permission to view forum activity
            $post = SP()->DB->table(SPPOSTS, "post_id=$activity->secondary_item_id", 'row');
            if ($post) {
                $view = SP()->auths->can_view($post->forum_id, 'post-content', SP()->user->thisUser->ID, $post->user_id, $post->topic_id, $post->post_id);
                if (!$view || $post->post_status == 1) {
                    # no permission so remove the activity from this user's view
                    $removed = true; # set flag to reorder the array
                    unset($data['activities'][$index]);
                    $total--;
                }
            } else {
                $removed = true; # set flag to reorder the array
                unset($data['activities'][$index]);
                $total--;
            }
        }

        # if any were removed, reorder the list
        if ($removed) {
            $data['activities'] = array_values($data['activities']);
            $data['total'] = $total;
        }
    }

    return $data;
}
