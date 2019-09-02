<?php
/*
Simple:Press
Post by Email Support Functions
$LastChangedDate: 2018-11-05 10:19:32 -0600 (Mon, 05 Nov 2018) $
$Rev: 15810 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# ------------------------------------------------------
# Creates new email address field
# ------------------------------------------------------
function sp_emailpost_do_add_email_field($forumEmail) {
	spa_paint_input(__('Unqiue Post by Email Address for this forum', 'sp-pbe'), 'forum_email', $forumEmail, false, true);
}

# ----------------------------------------------
# Saves the new email field to the forum
# ----------------------------------------------
function sp_emailpost_do_save_forum($forumid) {
	if(!isset($_POST['forum_email'])) return;
	$data = SP()->saveFilters->email($_POST['forum_email']);
	SP()->DB->execute("UPDATE ".SPFORUMS." SET forum_email='$data' WHERE forum_id=$forumid");
}

# ----------------------------------------------
# Adds alt email to profile form
# ----------------------------------------------
function sp_emailpost_do_add_alt_email($out, $userid) {
	$alt_email = get_user_meta($userid, 'alt_user_email', true);
	if (empty($alt_email)) $alt_email = '';
	$out.= '<div class="spColumnSection spProfileLeftCol">';
	$out.= '<p class="spProfileLabel">'.__('Alternate Post by Email Address', 'sp-pbe').': </p>';
	$out.= '</div>';
	$out.= '<div class="spColumnSection spProfileSpacerCol"></div>';
	$out.= '<div class="spColumnSection spProfileRightCol">';
	$out.= '<input type="text" class="spControl" name="altemail" id="altemail" value="'.esc_attr($alt_email).'" />';
	$out.= '</div>';
	return $out;
}

# ----------------------------------------------
# Saves alt email from profile form
# ----------------------------------------------
function sp_emailpost_do_save_alt_email($message, $userid) {
	$try = true;

	# save alt email address if changed
	$email = SP()->saveFilters->email($_POST['altemail']);
	if (empty($email)) {
		$try = delete_user_meta($userid, 'alt_user_email', $email);
	} else {
		$try = update_user_meta($userid, 'alt_user_email', $email);
	}
	if ($try == false) {
		$message['type'] = 'error';
		$message['text'] = __('The alternate post by email address could not be updated', 'sp-pbe');
	}
	return $message;
}

# ----------------------------------------------
# Creates link to add to subscription email
# ----------------------------------------------
function sp_emailpost_do_add_email_link($m, $newpost, $userid, $type) {

	if (SP()->auths->get('reply_own_topics', $newpost['forumid'], $userid) && $newpost['started_by'] != $userid) return $m;
	if (!SP()->auths->get('reply_topics', $newpost['forumid'], $userid)) return $m;
	if (SP()->auths->get('post_by_email_reply', $newpost['forumid'], $userid)) {
		if (!isset($newpost['forumemail']) || empty($newpost['forumemail'])) return $m;

      	$option = SP()->options->get('html-email');
        if (SP()->plugin->is_active('html-email/sp-html-email-plugin.php') && isset($option) && (($type = 'admin' && $option['admin-notifications']) || ($type = 'sub' && $option['subs']))) {
    		$top = '<p>[-- >'.__('To reply by email insert your text above this line - instructions below', 'sp-pbe').' --]</p>';
    		$bottom = '<hr><p>'.__('* To post a reply by email * - please remove all old text from ABOVE the insert line at the top and enter your plain text reply above it. Do not remove ANY of this email text. Please remember that your reply may not appear immediately in the forum.', 'sp-pbe').'</p>';
    		if (SP()->auths->get('post_by_email_start', $newpost['forumid'], $userid)) {
    			$bottom.= '<p>'.sprintf(__('* To start a new topic in this forum * - send your email to %s. The subject name will become the new topic title.', 'sp-pbe'), ' mailto:'.$newpost['forumemail']).'</p>';
    		}
    		$bottom.= '<p>[--id=#'.$newpost['topicslug'].'#--]</p>';
    		if (sp_pbe_is_message_not_yet_tagged($m)) {
    			# tag it if it hasn't already been tagged. Otherwise, don't tag it twice.
    			$m = $top.$m.$bottom;
    		}
        } else {
    		$top = "\r\n[-- ".__('To reply by email insert your text above this line - instructions below', 'sp-pbe')." --]\r\n\r\n";
    		$bottom = "\r\n\r\n".__('* To post a reply by email * - please remove all old text from ABOVE the insert line at the top and enter your plain text reply above it. Do not remove ANY of this email text. Please remember that your reply may not appear immediately in the forum', 'sp-pbe');
    		if (SP()->auths->get('post_by_email_start', $newpost['forumid'], $userid)) {
    			$bottom.= "\r\n\r\n".sprintf(__('* To start a new topic in this forum * - send your email to %s. The subject name will become the new topic title', 'sp-pbe'), ' mailto:'.$newpost['forumemail']);
    		}
    		$bottom.= "\r\n\r\n[--id=#".$newpost['topicslug'].'#--]';
    		if (sp_pbe_is_message_not_yet_tagged($m)) {
    			# tag it if it hasn't already been tagged. Otherwise, don't tag it twice.
    			$m = $top.$m."\r\n\r\n".$bottom;
    		}
        }
    }
	return $m;
}

# -----------------------------------------------
# Email support functions - thanks Chris Crabtree
# -----------------------------------------------

function sp_pbe_is_message_already_tagged($m) {
	$result = true; # assume it has been tagged
	$marker = "\r\n[-- ".__('To reply by email insert your text above this line - instructions below', 'sp-pbe')." --]\r\n\r\n";
	$position = stripos($m, $marker);
	if ($position === false) $result = false; # marker was not found, so it must not be tagged already.
	return $result;
}

function sp_pbe_is_message_not_yet_tagged($m) {
	$result = !sp_pbe_is_message_already_tagged($m);
	return $result;
}

# personal data export
function sp_privacy_do_emailpost_profile($exportItems, $spUserData, $groupID, $groupLabel) {
	if (!empty($spUserData->alt_user_email)) {
		$data = array();
		$data[] = array(
				'name'	=>	__('Alternate Email', 'sp-pbe'),
				'value'	=>	$spUserData->alt_user_email
		);
		$exportItems[] = array(
			'group_id'		=> $groupID,
			'group_label' 	=> $groupLabel,
			'item_id' => 'Profile',
			'data' => $data,
		);
	}
	return $exportItems;
}
