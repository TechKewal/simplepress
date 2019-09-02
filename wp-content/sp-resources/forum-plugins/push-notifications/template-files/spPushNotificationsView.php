<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Template
#	Template	:	Push Notifications View
#	Version		:	1.0
#	Author		:	Simple:Press
#
#	The 'push-notifications' template is used to display list of users unread topics they are subscribed to
#
# --------------------------------------------------------------------------------------
/*
$LastChangedDate: 2019-02-04 14:35:02 -0500 (Mon, 04 Fed 2019) $
$Rev: 15487 $
*/

	sp_SectionStart('tagClass=spHeadContainer', 'head');
		sp_load_template('spHead.php');
	sp_SectionEnd('', 'head');

	sp_SectionStart('tagClass=spBodyContainer', 'body');

		sp_SectionStart('tagClass=spPlainSection', 'subs');
            add_action('sph_ListViewBodyEnd', 'sp_push_notifications_list_button');
        	if (!empty(SP()->user->thisUser->subscribe)) {
            	echo '<div class="spUnsubscribeAll">';
            	echo '<form action="'.SP()->spPermalinks->get_url().'" method="get" name="endallsubs">';
            	echo '<input type="hidden" class="sfhiddeninput" name="userid" id="userid" value="'.SP()->user->thisUser->ID.'" />';
            	echo '<p class="spUnsubscribeAll"><input type="submit" class="spSubmit" name="endallsubs" value="'.esc_attr(__('Remove All Subscriptions', 'sp-pushnotifications')).'" /></p>';
            	echo '</form>';
                echo '</div>';

                $first = SP()->filters->integer($_GET['first']);
                SP()->forum->view->listTopics = new spcTopicList(SP()->user->thisUser->subscribe, 0, true, '', $first, 1, 'subscriptions');

                sp_load_template('spListView.php');
            } else {
        		echo '<div class="spMessage">';
        		echo '<p>'.__('You are not currently subscribed to any topics', 'sp-pushnotifications').'</p>';
        		echo '</div>';
            }
		sp_SectionEnd('', 'subs');

	sp_SectionEnd('', 'body');

	sp_SectionStart('tagClass=spFootContainer', 'foot');
		sp_load_template('spFoot.php');
	sp_SectionEnd('', 'foot');

##################################

    function sp_push_notifications_list_button() {
        $site = wp_nonce_url(SPAJAXURL.'subs-manage&amp;targetaction=remove-sub&amp;topic='.SP()->forum->view->thisListTopic->topic_id.'&amp;user='.SP()->user->thisUser->ID, 'subs-manage');
    	echo '<a rel="nofollow" class="spButton spRight spPushNotificationsRemoveButton spPushNotificationsEndButton" title="'.__('Unsubscribe from Topic', 'sp-pushnotifications').'" data-target="listtopic'.SP()->forum->view->thisListTopic->topic_id.'" data-site="'.$site.'">';
    	echo SP()->theme->paint_icon('spIcon', SPPNIMAGES,'sp_PushNotificationsUnsubscribe.png').__('Unsubscribe', 'sp-pushnotifications');
    	echo '</a>';
    }
