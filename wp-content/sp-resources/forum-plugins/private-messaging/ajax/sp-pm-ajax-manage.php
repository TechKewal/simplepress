<?php
/*
Simple:Press
PM plugin ajax routine for management functions
$LastChangedDate: 2018-10-17 15:17:49 -0500 (Wed, 17 Oct 2018) $
$Rev: 15756 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

global $wpdb;

if (!sp_pm_get_auth('use_pm')) die();

$action = (isset($_GET['targetaction'])) ? $_GET['targetaction'] : '';

# autocomplete
if (isset($_GET['term'])) {
	$out = '[]';

    $table = SPMEMBERS;
    $fields = '*';
    $distinct = false;
    $join = '';
	$squery = SP()->filters->str($_GET['term']);
	$where = "display_name LIKE '%".SP()->filters->esc_sql($wpdb->esc_like($squery))."%' AND ".SPMEMBERS.".user_id != ".SP()->user->thisUser->ID;

	# dont include adversaries of current user or folks who have marked current user as adverary
	$adversaries = SP()->DB->select('SELECT adversary_id FROM '.SPPMADVERSARIES.' WHERE user_id='.SP()->user->thisUser->ID, 'col');
	$blocked = SP()->DB->select('SELECT user_id FROM '.SPPMADVERSARIES.' WHERE adversary_id='.SP()->user->thisUser->ID, 'col');
	$nopm = array_merge($adversaries, $blocked);
	$nopm = implode(',', $nopm);
	if (!empty($nopm)) $where.= ' AND '.SPMEMBERS.".user_id NOT IN ($nopm)";

    # are we limiting by usergroup?
    $pm = SP()->options->get('pm');
    if ($pm['limitedug'] && !SP()->user->thisUser->admin) {
        if (empty(SP()->user->thisUser->memberships)) die();
        $ugids = array();
        foreach (SP()->user->thisUser->memberships as $membership) {
            $ugids[] = $membership['usergroup_id'];
        }
        $table = SPMEMBERSHIPS;
        $distinct = true;
        $fields = SPMEMBERSHIPS.'.user_id, display_name';
        $join = array(SPMEMBERS.' ON '.SPMEMBERSHIPS.'.user_id = '.SPMEMBERS.'.user_id');
        $ugids = implode(',', $ugids);
        $where.= " AND (usergroup_id IN ($ugids) OR admin = 1)";
    }

	# get users that can be pm'ed'
	$query = new stdClass();
		$query->table      = $table;
		$query->fields 	   = $fields;
		$query->right_join = $join;
		$query->where 	   = $where;
		$query->orderby    = 'admin DESC, moderator DESC';
	$query = apply_filters('sph_pm_addresses_query', $query);
	$users = SP()->DB->select($query);

	if ($users) {
		$primary = '';
		$secondary = '';
		$count = 0;
		foreach ($users as $user) {
			$user_opts = SP()->memberData->get($user->user_id, 'user_options');
			if (sp_pm_get_auth('use_pm', 'global', $user->user_id) && ((isset($user_opts['pmoptout']) && !$user_opts['pmoptout']) || !isset($user_opts['pmoptout']))) {
				$uname = SP()->displayFilters->name($user->display_name);
				$cUser = array ('id' => $user->user_id, 'value' => $uname);

				if (strcasecmp($squery, substr($uname, 0, strlen($squery))) == 0) {
					$primary.= json_encode($cUser).',';
				} else {
					$secondary.= json_encode($cUser).',';
				}

				# we only want 25 results
				$count++;
				if ($count == 25) break;
			}
		}
		if ($primary != '' || $secondary != '') {
			if ($primary != '') $primary = trim($primary, ',').',';
			if ($secondary != '') $secondary = trim($secondary, ',');
			$out = '['.trim($primary.$secondary, ',').']';
		}
	}
	echo $out;
	die();
}

# Update the Inbox Count
if (isset($_GET['target']) && $_GET['target'] == 'inbox') {
	if (sp_pm_get_auth('use_pm')) {
        require_once PMLIBDIR.'sp-pm-database.php';
        $newPM = sp_pm_get_inbox_unread_count(SP()->user->thisUser->ID);
		if (!$newPM) $newPM = 0;
        $pmClass = ($newPM > 0) ? 'spPmCountUnread' : 'spPmCountRead';
		echo "<span class='$pmClass'>$newPM</span>";
	}
	die();
}

# check nonce now autocomplete and autoupdate finished with
if (!sp_nonce('pm-manage')) die();

if (isset($_GET['addbuddy'])) {
    $buddy = SP()->filters->str($_GET['addbuddy']);
    $userid = SP()->filters->integer($_GET['u']);
    sp_pm_add_buddy($buddy);
	die();
}

if (isset($_GET['delbuddy'])) {
    $buddy = SP()->filters->str($_GET['delbuddy']);
    $userid = SP()->filters->integer($_GET['u']);
    if (SP()->user->thisUser->ID == $userid) sp_pm_delete_buddy($userid, $buddy);
	die();
}

if (isset($_GET['deladversary'])) {
    $adversary = SP()->filters->str($_GET['deladversary']);
    $userid = SP()->filters->integer($_GET['u']);
    if (SP()->user->thisUser->ID == $userid) sp_pm_delete_adversary($userid, $adversary);
	die();
}

# Add recipients to users buddy list ------------------------------
if (isset($_GET['addbuddies'])) {
	$list = explode('-', SP()->filters->str($_GET['addbuddies']));
	if ($list) {
		foreach ($list as $buddy) {
			if ($buddy != 0) sp_pm_add_buddy($buddy);
		}
	}
	die();
}

if ($action == 'update-buddies') {
    $userid = SP()->filters->integer($_GET['user']);
	if (empty($userid)) die();

	sp_SetupUserProfileData($userid);
    if (SP()->user->profileUser->buddies) {
    	foreach (SP()->user->profileUser->buddies as $buddy) {
    		echo '<div id="buddy'.$buddy.'">';
    		echo '<div class="spColumnSection spProfileLeftCol">';
    		echo '<p class="spProfileLabel">'.SP()->displayFilters->name(SP()->memberData->get($buddy, 'display_name')).':</p>';
    		echo '</div>';
    		echo '<div class="spColumnSection spProfileSpacerCol"></div>';
    		echo '<div class="spColumnSection spProfileRightCol">';
            $site = wp_nonce_url(SPAJAXURL.'pm-manage&u='.SP()->user->profileUser->ID.'&delbuddy='.$buddy, 'pm-manage');
            $title = esc_attr(__('Remove this user from your buddy list', 'sp-pm'));
    		echo '<p class="spProfileLabel">';
    		echo "<a rel='nofollow' class='spButton spPMRemoveUser' title='$title' data-url='$site' data-target='#buddy$buddy'>";
    		echo SP()->theme->paint_icon('spIcon', PMIMAGES, 'sp_PmDeleteBuddyAdversary.png');
    		echo __('Remove', 'sp-pm');
    		echo '</a>';
            $tmp = sp_attach_user_profile_link(SP()->user->profileUser->ID, __('View Profile', 'sp-pm'));
            $tmp = str_replace("class='spLink", "class='spButton", $tmp);
    		echo preg_replace("/>/", ">".SP()->theme->paint_icon('spIcon', PMIMAGES, 'sp_PmProfileBuddyAdversary.png'), $tmp, 1);
            echo '</p>';
    		echo '</div>';
    		echo '</div>';
    		echo '<div class="spClear"></div>';
    	}

    	SP()->memberData->reset_plugin_data($userid);
    } else {
    	echo '<p>'.__('You currently do not have any buddies.', 'sp-pm').'</p><br />';
    }
?>
	<script>
		(function(spj, $, undefined) {
			$(document).ready(function() {
				$('#newbuddies').val('');
				spj.setProfileDataHeight();
			})
		}(window.spj = window.spj || {}, jQuery));
	</script>
<?php

	die();
}

if ($action == 'update-adversaries') {
    $userid = SP()->filters->integer($_GET['user']);
	if (empty($userid)) die();

	sp_SetupUserProfileData($userid);
    if (SP()->user->profileUser->adversaries) {
    	foreach (SP()->user->profileUser->adversaries as $adversary) {
    		echo '<div id="adversary'.$adversary.'">';
    		echo '<div class="spColumnSection spProfileLeftCol">';
    		echo '<p class="spProfileLabel">'.SP()->displayFilters->name(SP()->memberData->get($adversary, 'display_name')).':</p>';
    		echo '</div>';
    		echo '<div class="spColumnSection spProfileSpacerCol"></div>';
    		echo '<div class="spColumnSection spProfileRightCol">';
            $site = wp_nonce_url(SPAJAXURL.'pm-manage&u='.SP()->user->profileUser->ID.'&deladversary='.$adversary, 'pm-manage');
            $title = esc_attr(__('Remove this user from your adversary list', 'sp-pm'));
    		echo '<p class="spProfileLabel">';
            echo "<a rel='nofollow' class='spButton spPMRemoveUser' title='$title' data-url='$site' data-target='#adversary$adversary'>";
    		echo SP()->theme->paint_icon('spIcon', PMIMAGES, 'sp_PmDeleteBuddyAdversary.png');
    		echo __('Remove', 'sp-pm');
    		echo '</a>';
            $tmp = sp_attach_user_profile_link(SP()->user->profileUser->ID, __('View Profile', 'sp-pm'));
            $tmp = str_replace("class='spLink", "class='spButton", $tmp);
    		echo preg_replace("/>/", ">".SP()->theme->paint_icon('spIcon', PMIMAGES, 'sp_PmProfileBuddyAdversary.png'), $tmp, 1);
            echo '</p>';
    		echo '</div>';
    		echo '</div>';
    		echo '<div class="spClear"></div>';
    	}

    	SP()->memberData->reset_plugin_data($userid);
    } else {
    	echo '<p>'.__('You currently do not have any adversaries.', 'sp-pm').'</p><br />';
    }
?>
	<script>
		(function(spj, $, undefined) {
			$(document).ready(function() {
				$('#newadversaries').val('');
				spj.setProfileDataHeight();
			});
		}(window.spj = window.spj || {}, jQuery));
	</script>
<?php

	die();
}

function sp_pm_show_attachments($message) {
    $attachment = SP()->DB->table(SPPMATTACHMENTS, "attachment_id=$message->attachment_id", 'row');
    if (empty($attachment)) return '';

    require_once SPPLUPLIBDIR.'sp-plupload-components.php';

    $attachments = unserialize($attachment->attachments);
    if (empty($attachments)) return '';

    $temp = '';

	$uploads = SP()->options->get('spPlupload');
	$show = false;

	$temp.= '<div class="spPmAttachments spClear">';
	$temp.= '<fieldset>';
	$icon = SP()->theme->paint_icon('', SPPLUPIMAGES, "sp_PlupAttachmentsPM.png");
	$temp.= "<legend>$icon".__('Attachments', 'sp-pm').'</legend>';
	$temp.= '<ul>';
	foreach ($attachments as $attachment) {
	    $found = false;
		$temp2 = '<li>';
		if ($attachment['type'] == 'image') {
			if (!$uploads['showinserted']) continue;
			$icon = SP()->theme->paint_icon('', SPPLUPIMAGES, "sp_PlupImage.png");
    		$show = $found = true;
		} else if ($attachment['type'] == 'media') {
			if (!$uploads['showinserted']) continue;
			$icon = SP()->theme->paint_icon('', SPPLUPIMAGES, "sp_PlupMedia.png");
    		$show = $found = true;
		} else if (SP()->auths->get('download_attachments', SP()->forum->view->thisPost->forum_id)) {
			$icon = SP()->theme->paint_icon('', SPPLUPIMAGES, "sp_PlupFile.png");
    		$show = $found = true;
		}
		$temp2.= $icon;
        $url = apply_filters('sph_plup_attachment_url', $attachment['path'].$attachment['file']);
		$temp2.= "<a href='$url'>{$attachment['file']}</a> ";
		$temp2.= '<span>('.sp_plupload_format_size($attachment['size']).')</span>';
		$temp2.= '</li>';
        if ($found) $temp.= $temp2;
	}
	$temp.= '</ul>';
	$temp.= '</fieldset>';
	$temp.= "</div>\n";

	if ($show) $out.= $temp;
    return $out;
}

# empty the user inbox
if ($action == 'delpms') {
    $id = SP()->filters->integer($_GET['id']);
    sp_pm_delete_user_inbox($id);
	die();
}

# delete a message thread
if (isset($_GET['deletethread'])) {
    $id = SP()->filters->integer($_GET['deletethread']);
    sp_pm_delete_thread($id);
	die();
}

# empty the user inbox
if (isset($_GET['emptyinbox'])) {
    sp_pm_empty_inbox();
	die();
}

# mark all messages in user inbox as read
if (isset($_GET['markinbox'])) {
    sp_pm_mark_inbox_read();
	die();
}

if (isset($_GET['markunread'])) {
	$id = SP()->filters->integer($_GET['markunread']);
	sp_pm_mark_message_unread($id);
	die();
}

if (isset($_GET['deletemessage'])) {
	$mid = SP()->filters->integer($_GET['deletemessage']);
	$tid = SP()->filters->integer($_GET['thread']);
	sp_pm_delete_message($mid, $tid);
	die();
}

if (isset($_GET['markthreadread'])) {
	$tid = SP()->filters->integer($_GET['markthreadread']);
	sp_pm_mark_thread_read($tid);
	die();
}

die();
