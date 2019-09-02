<?php
/*
Simple:Press
Topic Watches plugin ajax routine for management functions
$LastChangedDate: 2015-11-26 12:31:04 -0800 (Thu, 26 Nov 2015) $
$Rev: 13615 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

require_once WLIBDIR.'sp-watches-database.php';
require_once SP_PLUGIN_DIR.'/forum/content/sp-profile-view-functions.php';

if (!SP()->auths->get('watch')) die();

$action = (isset($_GET['targetaction'])) ? $_GET['targetaction'] : '';

# Update the Watched Topic Count
if (isset($_GET['target']) && $_GET['target'] == 'watches') {
	if (SP()->auths->get('watch')) {
		$watchCount = 0;

		if (!property_exists(SP()->user->thisUser, 'watches') || empty(SP()->user->thisUser->watches)) {
		    SP()->user->thisUser->watches = SP()->activity->get_col('col=item&type='.SPACTIVITY_WATCH.'&uid='.SP()->user->thisUser->ID);
		}

		$list = SP()->user->thisUser->watches;
		if (!empty($list)) {
			foreach ($list as $topicid) {
				if (sp_is_in_users_newposts($topicid)) $watchCount++;
			}
		}
        $watchClass = ($watchCount > 0) ? 'spWatchCountUnread' : 'spWatchCountRead';
		echo "<span class='$watchClass'>$watchCount</span>";
	}
	die();
}

# NOW check the nonce!
if (!sp_nonce('watches-manage')) die();

if ($action == 'watch-add') {
    $topic = SP()->filters->integer($_GET['topic']);
    sp_watches_save_watch($topic, SP()->user->thisUser->ID, false);
	die();
}

if ($action == 'watch-del') {
    $topic = SP()->filters->integer($_GET['topic']);
    sp_watches_remove_watch($topic, SP()->user->thisUser->ID, false);
	die();
}

if ($action == 'remove-watch') {
    $topic = SP()->filters->integer($_GET['topic']);
    $user = SP()->filters->integer($_GET['user']);
    if ($user == SP()->user->thisUser->ID) sp_watches_remove_watch($topic, $user, false);
    die();
}

if ($action == 'add-watch') {
	if (!SP()->user->thisUser->admin && !SP()->user->thisUser->moderator) die();

    $forumid = SP()->filters->integer($_GET['fid']);
    $topicid = SP()->filters->integer($_GET['tid']);
    if (empty($forumid) || empty($topicid)) die();

	$thisforum = SP()->DB->table(SPFORUMS, "forum_id=$forumid", 'row');
?>
	<div id="spMainContainer" class="spForumToolsPopup">
		<div class="spForumToolsHeader">
			<div class="spForumToolsHeaderTitle"><?php echo __('Select user(s) you want to watch this topic', 'sp-watches').':'; ?></div>
		</div>
		<form action="<?php echo SP()->spPermalinks->build_url($thisforum->forum_slug, '', 1, 0); ?>" method="post" name="watchtopicform">
			<input type="hidden" name="currenttopicid" value="<?php echo $topicid; ?>" />
			<div class="spCenter">
				<br /><?php echo sp_render_admin_mod_select($forumid, __('Select user(s)', 'sp-watches'));	?><br /><br />
				<input type="submit" class="spSubmit" name="maketopicwatch" value="<?php echo esc_attr(__('Add watch to user(s)', 'sp-watches')) ?>" />
				<input type="button" class="spSubmit spCancelScript" name="cancel" value="<?php echo esc_attr(__('Cancel', 'sp-watches')) ?>" />
			</div>
		</form>
	</div>
<?php

    die();
}

if ($action == 'watch') {
	if (!property_exists(SP()->user->thisUser, 'watches') || empty(SP()->user->thisUser->watches)) {
	    SP()->user->thisUser->watches = SP()->activity->get_col('col=item&type='.SPACTIVITY_WATCH.'&uid='.SP()->user->thisUser->ID);
	}

    add_action('sph_ListNewPostButtonAlt', 'sp_watches_list_button');

	echo '<div id="spMainContainer">';
	if (!empty(SP()->user->thisUser->watches)) {
    	echo '<div class="spStopWatchingAll">';
    	echo '<form action="'.SP()->spPermalinks->get_url().'" method="get" name="endallwatches">';
    	echo '<input type="hidden" class="sfhiddeninput" name="userid" id="userid" value="'.SP()->user->thisUser->ID.'" />';
    	echo '<input type="submit" class="spSubmit" name="endallwatches" value="'.esc_attr(__('Remove All Watches', 'sp-watches')).'" />';
    	echo '</form>';
    	echo '</div>';
        $first = SP()->filters->integer($_GET['first']);
        SP()->forum->view->listTopics = new spcTopicList(SP()->user->thisUser->watches, 0, true, '', $first, 1, 'watches');

        sp_load_template('spListView.php');
    } else {
		echo '<div class="spMessage">';
		echo '<p>'.__('You are not currently watching any topics', 'sp-watches').'</p>';
		echo '</div>';
    }

    echo '</div>';
    die();
}

if ($action == 'update-topic-watches') {
    $userid = SP()->filters->integer($_GET['user']);
	if (empty($userid)) die();

	sp_SetupUserProfileData($userid);
    if (SP()->user->profileUser->watches) {
        $found = false;
    	foreach (SP()->user->profileUser->watches as $watch) {
        	$topic = SP()->DB->table(SPTOPICS, "topic_id=$watch", 'row');
            if ($topic) {
                $found = true;
            	echo '<div class="spColumnSection">';
                echo '<input type="checkbox" name="topic['.$topic->topic_id.']" id="sf-topic-'.$topic->topic_id.'" />';
                echo '<label for="sf-topic-'.$topic->topic_id.'">';
                echo $topic->topic_name.' (<a target="_blank" href="'.SP()->spPermalinks->permalink_from_postid($topic->post_id).'">'.__('view topic', 'sp-watches').')</a> ('.$topic->post_count.' '.__('posts', 'sp-watches').')';
                echo '</label><br />';
            	echo '</div>';
            }
    	}
        if (!$found) {
        	echo '</form>';
            echo '<p>'.__('You are not currently watching any topics', 'sp-watches').'</p><br />';
            echo "</div>\n";
        }
    } else {
    	echo '<p>'.__('You are not currently watching any topics', 'sp-watches').'</p><br />';
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

function sp_render_admin_mod_select($forumid, $default) {
    $out = '<select multiple="multiple" class="spSelect" name="spWatchesUsers[]">';
    $out.= '<option>'.$default.'</option>';
   	$users = SP()->DB->select('SELECT user_id, display_name FROM '.SPMEMBERS.' WHERE admin=1 OR moderator=1 ORDER BY admin DESC, moderator DESC');
    if ($users) {
        foreach ($users as $user) {
            if (SP()->auths->can_view($forumid, 'topic-title', $user->user_id)) {
                $out.= "<option value='$user->user_id'>$user->display_name</option>";
            }
        }
    }
    $out.='</select>';
    echo $out;
}

function sp_watches_list_button() {
    $site = wp_nonce_url(SPAJAXURL.'watches-manage&amp;targetaction=remove-watch&amp;topic='.SP()->forum->view->thisListTopic->topic_id.'&amp;user='.SP()->user->thisUser->ID, 'watches-manage');
   	echo '<a rel="nofollow" class="spButton spLeft spWatchEndButton spWatchesEndButton" title="'.__('Stop Watching Topic', 'sp-watches').'" data-target="listtopic'.SP()->forum->view->thisListTopic->topic_id.'" data-site="'.$site.'">';
	echo SP()->theme->paint_icon('spIcon', WIMAGES, 'sp_WatchesStopWatch.png').__('End', 'sp-watches');
	echo '</a>';
}

die();
