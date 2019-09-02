<?php
/*
Simple:Press
Buddypress Plugin notifications support components
$LastChangedDate: 2017-02-11 15:38:34 -0600 (Sat, 11 Feb 2017) $
$Rev: 15188 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sph_buddypress_do_notifications() {
   	SP()->user->get_current_user();

    bp_notifications_delete_all_notifications_by_type(999, 'forum');

    # bail if not using notifications - done here in case we needed to clear current ones
   	$bpdata = SP()->options->get('buddypress');
    if (!$bpdata['usenotifications']) return;

    if ($bpdata['newnotifications'] && !empty(SP()->user->thisUser->newposts['topics'])) bp_notifications_add_notification(array('item_id' => 999, 'user_id' => SP()->user->thisUser->ID, 'component_name' => 'forum', 'component_action' => 'newposts', 'secondary_item_id' => count(SP()->user->thisUser->newposts['topics'])));

	if (SP()->plugin->is_active('private-messaging/sp-pm-plugin.php')) {
		if ($bpdata['inboxnotifications'] && sp_pm_get_auth('use_pm')) {
			require_once PMLIBDIR.'sp-pm-database.php';
			$count = sp_pm_get_inbox_unread_count(SP()->user->thisUser->ID);
			if (!empty($count)) bp_notifications_add_notification(array('item_id' => 999, 'user_id' => SP()->user->thisUser->ID, 'component_name' => 'forum', 'component_action' => 'inbox', 'secondary_item_id' => $count));
		}
	}
    if ($bpdata['subsnotifications'] && SP()->auths->get('subscribe')) {
        $count = 0;
    	$list = SP()->user->thisUser->subscribe;
    	if (!empty($list)) {
    		foreach ($list as $topicid) {
    			if (sp_is_in_users_newposts($topicid)) $count++;
    		}
    	}

    	if (!empty($count)) bp_notifications_add_notification(array('item_id' => 999, 'user_id' => SP()->user->thisUser->ID, 'component_name' => 'forum', 'component_action' => 'subscriptions', 'secondary_item_id' => $count));
    }

    if ($bpdata['watchesnotifications'] && SP()->auths->get('watch')) {
    	$count = 0;
    	$list = SP()->user->thisUser->watches;
    	if (!empty($list)) {
    		foreach ($list as $topicid) {
    			if (sp_is_in_users_newposts($topicid)) $count++;
    		}
    	}
    	if (!empty($count)) bp_notifications_add_notification(array('item_id' => 999, 'user_id' => SP()->user->thisUser->ID, 'component_name' => 'forum', 'component_action' => 'watches', 'secondary_item_id' => $count));
    }
}

function sp_buddypress_format_notifications($action, $item_id, $secondary_item_id, $total_items, $format = 'string') {
	switch ($action) {
		case 'newposts':
			$text = sprintf(__('You have %1$d unread forum posts', 'sp-buddypress'), (int) $secondary_item_id);
            $title = __('Unread Posts', 'sp-buddypress');
            $url = SP()->spPermalinks->get_url('newposts');
            break;

		case 'inbox':
			$text = sprintf(__('You have %1$d new forum private messages', 'sp-buddypress'), (int) $secondary_item_id);
            $title = __('Inbox', 'sp-buddypress');
            $url = SP()->spPermalinks->get_url('private-messaging/inbox');
            break;

		case 'subscriptions':
			$text = sprintf(__('You have %1$d unread forum subscribed topics', 'sp-buddypress'), (int) $secondary_item_id);
            $title = __('Subscriptions', 'sp-buddypress');
            $url = SP()->spPermalinks->get_url('subscriptions');
            break;

		case 'watches':
			$text = sprintf(__('You have %1$d unread forum watched topics', 'sp-buddypress'), (int) $secondary_item_id);
            $title = __('Watches', 'sp-buddypress');
            $url = SP()->spPermalinks->get_url('watches');
            break;

        default:
            return '';
	}

	if ('string' == $format) {
        $return = '<a href="'.$url.'" title="'.$title.'">'.$text.'</a>';
	} else {
		$return = array('text' => $text, 'link' => $url);
	}

	return $return;
}