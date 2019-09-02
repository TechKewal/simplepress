<?php
/*
Simple:Press
PM plugin database routines
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_pm_delete_buddy($uid, $bid) {
	SP()->DB->execute('DELETE FROM '.SPPMBUDDIES." WHERE user_id=$uid AND buddy_id=$bid");
	SP()->user->thisUser->buddies = SP()->DB->select("SELECT buddy_id FROM ".SPPMBUDDIES." WHERE user_id=$uid", 'col');
	SP()->memberData->reset_plugin_data(SP()->user->thisUser->ID);
	SP()->memberData->reset_plugin_data($uid);
}

function sp_pm_delete_adversary($uid, $aid) {
	SP()->DB->execute('DELETE FROM '.SPPMADVERSARIES." WHERE user_id=$uid AND adversary_id=$aid");
	SP()->user->thisUser->adversaries = SP()->DB->select("SELECT adversary_id FROM ".SPPMADVERSARIES." WHERE user_id=$uid", 'col');
	SP()->memberData->reset_plugin_data(SP()->user->thisUser->ID);
	SP()->memberData->reset_plugin_data($uid);
}

function sp_pm_add_buddy($id) {
	# Put member into buddy list if not there
	$buddies = SP()->user->thisUser->buddies;
	if (!in_array($id, $buddies)) {
		SP()->DB->execute("INSERT INTO ".SPPMBUDDIES." (user_id, buddy_id) VALUES (SP()->user->thisUser->ID, $id)");
		SP()->user->thisUser->buddies[] = $id;
	}
	SP()->memberData->reset_plugin_data(SP()->user->thisUser->ID);
	SP()->memberData->reset_plugin_data($id);
}

function sp_pm_create_user_select($users) {
	$out = '';
	if ($users) {
		foreach ($users as $user) {
			if ($user->ID != SP()->user->thisUser->ID && (sp_pm_get_auth('use_pm', '', $user->ID) || SP()->auths->forum_admin($user->ID))) {
				$out.= '<option value="'.$user->ID.'">'.SP()->displayFilters->name($user->display_name).'</option>';
				$default = '';
			}
		}
	}
	return $out;
}

function sp_pm_get_buddies() {
	$buddylist = array();

	$buddies = 	SP()->user->thisUser->buddies;
	if ($buddies) {
		$x = 0;
		foreach ($buddies as $buddy) {
            $buddylist[$x] = new stdClass();
			$buddylist[$x]->ID = $buddy;
			$buddylist[$x]->display_name = SP()->displayFilters->name(SP()->memberData->get($buddy, 'display_name'));
			$x++;
		}
	}
	return $buddylist;
}

function sp_pm_delete_user_inbox($userid) {
    # only admins
    if (empty($userid) || !SP()->user->thisUser->admin) return;

    # grab the thread ids we are going to remove messages from
    $threads = SP()->DB->select("SELECT thread_id FROM ".SPPMRECIPIENTS." WHERE user_id=$userid", 'col');
    if ($threads) {
        foreach ($threads as $thread) {
            sp_pm_delete_thread($thread, $userid);
        }
    }
}

function sp_pm_delete_thread($thread_id, $userid='') {
    if (empty($userid)) $userid = SP()->user->thisUser->ID;
    if (empty($userid)) return;

    # delete any attachments
    $attachments = SP()->DB->select("SELECT attachment_id FROM ".SPPMMESSAGES." WHERE attachment_id IS NOT NULL AND attachment_id != 0 AND thread_id=$thread_id AND user_id=$userid", 'col');
    $attachments = implode(',', $attachments);
	if (!empty($attachments)) SP()->DB->execute('DELETE FROM '.SPPMATTACHMENTS." WHERE attachment_id IN ($attachments)");

    # delete all user received messages
	SP()->DB->execute('DELETE FROM '.SPPMRECIPIENTS." WHERE thread_id=$thread_id AND user_id=$userid");

    # can the messages be deleted?
    $recipients = SP()->DB->table(SPPMRECIPIENTS, "thread_id=$thread_id", 'recipient_id');
   	if (empty($recipients)) {
   	    SP()->DB->execute('DELETE FROM '.SPPMMESSAGES." WHERE thread_id=$thread_id");
        SP()->DB->execute('DELETE FROM '.SPPMTHREADS." WHERE thread_id=$thread_id");
    }
}

function sp_pm_mark_inbox_read() {
    # mark unread messages as read for this user
	SP()->DB->execute('UPDATE '.SPPMRECIPIENTS.' SET read_status=1 WHERE user_id='.SP()->user->thisUser->ID);
}

function sp_pm_empty_inbox() {
    # grab the thread ids we are going to remove messages from
    $threads = SP()->DB->select("SELECT thread_id FROM ".SPPMRECIPIENTS.' WHERE user_id='.SP()->user->thisUser->ID, 'col');
    if ($threads) {
        foreach ($threads as $thread) {
            sp_pm_delete_thread($thread);
        }
    }
}

function sp_pm_get_inbox_unread_count($user_id) {
	return SP()->DB->count(SPPMRECIPIENTS, "user_id=$user_id AND read_status=0");
}

function sp_pm_get_inbox_count($user_id) {
	return SP()->DB->count(SPPMRECIPIENTS, "user_id=$user_id");
}

function sp_pm_mark_message_unread($message_id) {
	SP()->DB->execute('UPDATE '.SPPMRECIPIENTS." SET read_status=0 WHERE message_id=$message_id AND user_id=".SP()->user->thisUser->ID);
}

function sp_pm_mark_thread_read($thread_id) {
	SP()->DB->execute('UPDATE '.SPPMRECIPIENTS." SET read_status=1 WHERE thread_id=$thread_id AND user_id=".SP()->user->thisUser->ID);
}

function sp_pm_delete_message($message_id, $thread_id) {
    # delete any attachments
    $attachment = SP()->DB->select("SELECT attachment_id FROM ".SPPMMESSAGES." WHERE attachment_id IS NOT NULL AND attachment_id != 0 AND message_id=$message_id", 'var');
	if (!empty($attachments)) SP()->DB->execute('DELETE FROM '.SPPMATTACHMENTS." WHERE attachment_id=$attachment");

    # delete all user received messages
	SP()->DB->execute('DELETE FROM '.SPPMRECIPIENTS." WHERE thread_id=$thread_id AND message_id=$message_id");

    # can the messages be deleted?
    $recipients = SP()->DB->table(SPPMRECIPIENTS, "thread_id=$thread_id", 'recipient_id');
   	if (empty($recipients)) {
   	    SP()->DB->execute('DELETE FROM '.SPPMMESSAGES." WHERE thread_id=$thread_id");
        SP()->DB->execute('DELETE FROM '.SPPMTHREADS." WHERE thread_id=$thread_id");
    }
}
