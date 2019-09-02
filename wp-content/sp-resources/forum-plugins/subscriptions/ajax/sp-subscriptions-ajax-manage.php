<?php
/*
Simple:Press
Topic Subscriptions plugin ajax routine for management functions
$LastChangedDate: 2017-12-28 11:38:04 -0600 (Thu, 28 Dec 2017) $
$Rev: 15602 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

require_once SLIBDIR.'sp-subscriptions-database.php';
require_once SP_PLUGIN_DIR.'/forum/content/sp-profile-view-functions.php';

if (!SP()->auths->get('subscribe')) die();

# Update the Subscribe Topic Count
if (isset($_GET['target']) && $_GET['target'] == 'subs') {
	if (SP()->auths->get('subscribe')) {
		$subCount = 0;
		if (!property_exists(SP()->user->thisUser, 'subscribe') || empty(SP()->user->thisUser->subscribe)) {
		    SP()->user->thisUser->subscribe = SP()->activity->get_col('col=item&type='.SPACTIVITY_SUBSTOPIC.'&uid='.SP()->user->thisUser->ID);
		}
		$list = SP()->user->thisUser->subscribe;
		if (!empty($list)) {
			foreach ($list as $topicid) {
				if (sp_is_in_users_newposts($topicid)) $subCount++;
			}
		}
        $subClass = ($subCount > 0) ? 'spSubCountUnread' : 'spSubCountRead';
		echo "<span class='$subClass'>$subCount</span>";
	}
	die();
}

if (!sp_nonce('subs-manage')) die();

$action = (isset($_GET['targetaction'])) ? $_GET['targetaction'] : '';

if ($action == 'add-sub') {
    $topic = SP()->filters->integer($_GET['topic']);
    sp_subscriptions_save_subscription($topic, SP()->user->thisUser->ID, false);
	die();
}

if ($action == 'del-sub') {
    $topic = SP()->filters->integer($_GET['topic']);
    sp_subscriptions_remove_subscription($topic, SP()->user->thisUser->ID, false);
	die();
}

if ($action == 'remove-sub') {
    $topic = SP()->filters->integer($_GET['topic']);
    $user = SP()->filters->integer($_GET['user']);
    if ($user == SP()->user->thisUser->ID) sp_subscriptions_remove_subscription($topic, $user);
	die();
}

if ($action == 'sub') {
	if (!property_exists(SP()->user->thisUser, 'subscribe') || empty(SP()->user->thisUser->subscribe)) {
	    SP()->user->thisUser->subscribe = SP()->activity->get_col('col=item&type='.SPACTIVITY_SUBSTOPIC.'&uid='.SP()->user->thisUser->ID);
	}

    add_action('sph_ListNewPostButtonAlt', 'sp_subscriptions_list_button');
	echo '<div id="spMainContainer">';
	if (!empty(SP()->user->thisUser->subscribe)) {
    	echo '<div class="spUnsubscribeAll">';
    	echo '<form action="'.SP()->spPermalinks->get_url().'" method="get" name="endallsubs">';
    	echo '<input type="hidden" class="sfhiddeninput" name="userid" id="userid" value="'.SP()->user->thisUser->ID.'" />';
    	echo '<p class="spUnsubscribeAll"><input type="submit" class="spSubmit" name="endallsubs" value="'.esc_attr(__('Remove All Subscriptions', 'sp-subs')).'" /></p>';
    	echo '</form>';
        echo '</div>';

        $first = SP()->filters->integer($_GET['first']);
        SP()->forum->view->listTopics = new spcTopicList(SP()->user->thisUser->subscribe, 0, true, '', $first, 1, 'subscriptions');

        sp_load_template('spListView.php');
    } else {
		echo '<div class="spMessage">';
		echo '<p>'.__('You are not currently subscribed to any topics', 'sp-subs').'</p>';
		echo '</div>';
    }

    echo '</div>';
    die();
}

if ($action == 'update-topic-subs') {
    $userid = SP()->filters->integer($_GET['user']);
	if (empty($userid)) die();

	sp_SetupUserProfileData($userid);
    if (SP()->user->profileUser->subscribe) {
        $found = false;
    	foreach (SP()->user->profileUser->subscribe as $sub) {
        	$topic = SP()->DB->table(SPTOPICS, "topic_id=$sub", 'row');
            if ($topic) {
                $found = true;
            	echo '<div class="spColumnSection">';
                echo '<input type="checkbox" name="topic['.$topic->topic_id.']" id="sf-topic-'.$topic->topic_id.'" />';
                echo '<label for="sf-topic-'.$topic->topic_id.'">';
                echo $topic->topic_name.' (<a href="'.SP()->spPermalinks->permalink_from_postid($topic->post_id).'">'.__('view topic', 'sp-subs').')</a> ('.$topic->post_count.' '.__('posts', 'sp-subs').')';
                echo '</label>';
            	echo '</div>';
            }
    	}
        if (!$found) {
        	echo '</form>';
            echo '<p>'.__('You are not currently subscribed to any topics', 'sp-subs').'</p><br />';
            echo "</div>\n";
        }
    } else {
    	echo '<p>'.__('You are not currently subscribed to any topics', 'sp-subs').'</p><br />';
    }
?>
	<script>
		(function(spj, $, undefined) {
			$(document).ready(function() {
				spj.setProfileDataHeight();
			});
		}(window.spj = window.spj || {}, jQuery));
	</script>
<?php

	die();
}

if ($action == 'update-forum-subs') {
    $userid = SP()->filters->integer($_GET['user']);
	if (empty($userid)) die();

	sp_SetupUserProfileData($userid);
    require_once SP_PLUGIN_DIR.'/admin/library/spa-support.php';
    $forums = spa_get_forums_all();
    if ($forums) {
    	$thisgroup = 0;
    	foreach ($forums as $forum) {
            if (SP()->auths->get('subscribe', $forum->forum_id, $userid) && !$forum->forum_disable) {
    			if ($thisgroup != $forum->group_id) {
    				echo '<p class="spProfileLabel">'.__('Group', 'sp-subs').': '.SP()->displayFilters->title($forum->group_name).'</p>';
    				$thisgroup = $forum->group_id;
    			}
            	echo '<div class="spColumnSection">';
                $checked = (!empty(SP()->user->profileUser->forum_subscribe) && in_array($forum->forum_id, SP()->user->profileUser->forum_subscribe)) ? 'checked="checked" ' : '';
                echo '<input type="checkbox" '.$checked.'name="forum['.$forum->forum_id.']" id="sf-forum-'.$forum->forum_id.'" /><label for="sf-forum-'.$forum->forum_id.'">'.$forum->forum_name.'</label>';
            	echo '</div>';
            }
    	}
    }
?>
	<script>
		(function(spj, $, undefined) {
			$(document).ready(function() {
				spj.setProfileDataHeight();
			});
		}(window.spj = window.spj || {}, jQuery));
	</script>
<?php

	die();
}

function sp_subscriptions_list_button() {
    $site = wp_nonce_url(SPAJAXURL.'subs-manage&amp;targetaction=remove-sub&amp;topic='.SP()->forum->view->thisListTopic->topic_id.'&amp;user='.SP()->user->thisUser->ID, 'subs-manage');
	echo '<a rel="nofollow" class="spButton spLeft spSubRemoveButton spSubsEndButton" title="'.__('Unsubscribe from Topic', 'sp-subs').'" data-target="listtopic'.SP()->forum->view->thisListTopic->topic_id.'" data-site="'.$site.'">';
	echo SP()->theme->paint_icon('spIcon', SIMAGES,'sp_SubscriptionsUnsubscribe.png').__('End', 'sp-subs');
	echo '</a>';
}

die();
