<?php
/*
Simple:Press
Topic Push Notifications (Pushover) plugin ajax routine for management functions
$LastChangedDate: 2019-02-04 14:35:02 -0500 (Mon, 04 Fed 2019) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');


sp_forum_ajax_support();

require_once SPPNLIBDIR.'sp-push-notifications-database.php';
require_once SP_PLUGIN_DIR.'/forum/content/sp-profile-view-functions.php';

if (!SP()->auths->get('pushover')) die();

if (!sp_nonce('subs-pushover')) die();

$action = (isset($_GET['targetaction'])) ? $_GET['targetaction'] : '';


if ($action == 'add-sub') {

	$topic = SP()->filters->integer($_GET['topic']);
	$sub_action = substr(strstr($_GET['action'], '-'), 1, strlen($_GET['action']));
    sp_push_notifications_save_subscription($topic, SP()->user->thisUser->ID, false, $sub_action, SPACTIVITY_SUBS_PUSHOVER_TOPIC);
	die();

}

if ($action == 'del-sub') {

	$topic = SP()->filters->integer($_GET['topic']);
	$sub_action = substr(strstr($_GET['action'], '-'), 1, strlen($_GET['action']));
    sp_push_notifications_remove_subscription($topic, SP()->user->thisUser->ID, false, $sub_action, SPACTIVITY_SUBS_PUSHOVER_TOPIC);
	die();
	
}

if ($action == 'update-topic-pushover') {

    $userid = SP()->filters->integer($_GET['user']);
	if (empty($userid)) die();

	sp_SetupUserProfileData($userid);
	


    if (SP()->activity->get_col('col=item&type='.SPACTIVITY_SUBS_PUSHOVER_TOPIC.'&uid='.SP()->user->profileUser->ID )) {
		
		$found = false;

		if(isset($_GET['checked']) && isset($_GET['topic'])){

			foreach($_GET['topic'] as $key=>$value){
				
				$sub_action = substr(strstr($_GET['action'], '-'), 1, strlen($_GET['action']));
				sp_push_notifications_remove_subscription(
					$key, 
					SP()->user->thisUser->ID, 
					false, 
					$sub_action, 
					SPACTIVITY_SUBS_PUSHOVER_TOPIC
				);

			}

		} else {

			foreach (SP()->activity->get_col('col=item&type='.SPACTIVITY_SUBS_PUSHOVER_TOPIC.'&uid='.SP()->user->profileUser->ID ) as $sub) {
				$sub_action = substr(strstr($_GET['action'], '-'), 1, strlen($_GET['action']));
				sp_push_notifications_remove_subscription(
					SP()->DB->table(SPTOPICS, "topic_id=$sub", 'row'), 
					SP()->user->thisUser->ID, 
					false, 
					$sub_action, 
					SPACTIVITY_SUBS_PUSHOVER_TOPIC
				);
			}

		}
		
		
		foreach (SP()->activity->get_col('col=item&type='.SPACTIVITY_SUBS_PUSHOVER_TOPIC.'&uid='.SP()->user->profileUser->ID ) as $sub) {

			$topic = SP()->DB->table(SPTOPICS, "topic_id=$sub", 'row');

            if ($topic) {

				$found = true;
				
            	echo '<div class="spColumnSection">';
                echo '<input type="checkbox" name="topic['.$topic->topic_id.']" id="sf-topic-'.$topic->topic_id.'" />';
                echo '<label for="sf-topic-'.$topic->topic_id.'">';
                echo $topic->topic_name.' (<a href="'.SP()->spPermalinks->permalink_from_postid($topic->post_id).'">'.__('view topic', 'push-notifications').')</a> ('.$topic->post_count.' '.__('posts', 'push-notifications').')';
                echo '</label>';
				echo '</div>';

			}

		}

        if (!$found) {
        	echo '</form>';
            echo '<p>'.__('You are not currently subscribed to any topics', 'sp-pushnotifications').'</p><br />';
            echo "</div>\n";
		}
    } else {
    	echo '<p>'.__('You are not currently subscribed to any topics', 'sp-pushnotifications').'</p><br />';
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

function sp_push_notifications_list_button() {
    $site = wp_nonce_url(SPAJAXURL.'subs-manage&amp;targetaction=remove-sub&amp;topic='.SP()->forum->view->thisListTopic->topic_id.'&amp;user='.SP()->user->thisUser->ID, 'subs-manage');
	echo '<a rel="nofollow" class="spButton spLeft spPushNotificationsRemoveButton spPushNotificationsEndButton" title="'.__('Unsubscribe from Topic', 'sp-pushnotifications').'" data-target="listtopic'.SP()->forum->view->thisListTopic->topic_id.'" data-site="'.$site.'">';
	echo SP()->theme->paint_icon('spIcon', SPPNIMAGES,'sp_PushNotificationsUnsubscribe.png').__('End', 'sp-pushnotifications');
	echo '</a>';
}

die();
