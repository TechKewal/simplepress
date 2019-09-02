<?php
/*
Simple:Press
PM Plugin messaging send routine
$LastChangedDate: 2018-10-28 17:54:52 -0500 (Sun, 28 Oct 2018) $
$Rev: 15780 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

if (!sp_nonce('pm-post')) die();

sp_load_editor(0,1);

require_once PMLIBDIR.'sp-pm-components.php';

if (!sp_pm_get_auth('use_pm')) die();

# new pm post creation
sp_pm_save_message();

$url = SP()->spPermalinks->get_url('private-messaging/inbox');

wp_redirect($url);

die();


function sp_pm_save_message() {
	check_admin_referer('forum-userform_addpm', 'forum-userform_addpm');

	if (SP()->user->thisUser->ID != SP()->filters->integer($_POST['pmuser'])) {
		SP()->notifications->message(1, __('Could not validate PM sender', 'sp-pm'));
		return;
	}

	$sppm = SP()->options->get('pm');

	$newpm = array();

	# Data Checks
	$newpm['title'] = SP()->saveFilters->title(trim($_POST['pmtitle']), SPPMTHREADS, 'title');
	$slugtitle = $newpm['title'];

	if (empty($newpm['title'])) $newpm['title'] = __('Untitled', 'sp-pm');

	$newpm['messagecontent'] = $_POST['postitem'];
	if (empty($newpm['messagecontent'])) {
		SP()->notifications->message(1, __('No message was entered', 'sp-pm'));
		return;
	}

	$newpm['messagecontent'] = SP()->saveFilters->content($newpm['messagecontent'], 'new', true, SPPMMESSAGES, 'message');
	$newpm['messagecontent_raw'] = SP()->saveFilters->content($newpm['messagecontent'], 'new', false, SPPMMESSAGES, 'message');

    $newpm['thread_id'] = SP()->filters->integer($_POST['threadid']);

	$newpm['tolist'] = $_POST['userid'];
	if (!$newpm['tolist']) {
		SP()->notifications->message(1, __('No message recipients were selected', 'sp-pm'));
		return;
	}
	$newpm['typelist'] = $_POST['type'];

    # handle pm all users and pm usergroups
	$all = false;
	if ($newpm['tolist'][0] == -1) { # are we pming all users?
		$newpm['tolist'] = SP()->DB->select('SELECT user_id FROM '.SPMEMBERS." WHERE user_id != ".SP()->user->thisUser->ID, 'col');
		$all = true;
	} else { # are we pming any usergroups?
        $newlist = array();
        $newtype = array();
		foreach ($newpm['tolist'] as $key => $recipient) {
            if ($recipient == -2) {
                $ugid = SP()->DB->table(SPUSERGROUPS, "usergroup_name='".SP()->filters->esc_sql($_POST['user'][$key])."'", 'usergroup_id');
            	$sql = "SELECT ".SPMEMBERSHIPS.".user_id FROM ".SPMEMBERSHIPS." WHERE ".SPMEMBERSHIPS.".usergroup_id=".$ugid;
            	$members = SP()->DB->select($sql, 'col');
                if (!empty($members)) {
                    foreach ($members as $member) {
                        $newlist[] = $member;
                        $newtype[] = $newpm['typelist'][$key];
                    }
                }
            } else {
                $newlist[] = $recipient;
                $newtype[] = $newpm['typelist'][$key];
            }
        }
        $newpm['tolist'] = $newlist;
        $newpm['typelist'] = $newtype;
	}

	# pre save
	do_action('sph_pre_pm_create', $newpm);
	$newpm = apply_filters('sph_new_pm', $newpm);
	if (!empty($newpm)) {

        # get count of messages in this pm
        $count = count($newpm['tolist']);

        # check for new thread or exisiting thread
    	if ($newpm['thread_id'] == 0) {
            # its a new thread so create it in db
            $newpm['slug'] = sp_create_slug($newpm['title'], true, SPPMTHREADS, 'thread_slug');

			$sql = "INSERT INTO ".SPPMTHREADS;
			$sql.= " (title, thread_slug, message_count) ";
			$sql.= "VALUES ('{$newpm['title']}', '{$newpm['slug']}', $count)";

			if (SP()->DB->execute($sql) == false) {
				SP()->notifications->message(1, __('Unable to create new PM thread', 'sp-pm'));
				return;
			}

            # grab the new thread id
            $newpm['thread_id'] = SP()->rewrites->pageData['insertid'];
        } else {
            # its an existing thread so update message count
			$sql = "UPDATE ".SPPMTHREADS." SET message_count = message_count + $count WHERE thread_id={$newpm['thread_id']}";

			if (SP()->DB->execute($sql) == false) {
				SP()->notifications->message(1, __('Unable to update the PM thread', 'sp-pm'));
				return;
			}
        }

    	# do we need to create an attachment record?
        $createAttachment = true;
        $attachment_id = 0;
    	if ($createAttachment && !empty($_POST['sp_file_uploader_count']) && $sppm['uploads']) {
            $createAttachment = false;
            $attachments = sp_pm_get_attachments();
            if (!empty($attachments)) {
                SP()->DB->execute('INSERT INTO '.SPPMATTACHMENTS." (attachments) VALUES ('".serialize($attachments)."')");
                $attachment_id = SP()->rewrites->pageData['insertid'];
            }
    	}
        $newpm['attachment_id'] = $attachment_id;

        # create the message record
		$sql = "INSERT INTO ".SPPMMESSAGES;
		$sql.= " (thread_id, user_id, sent_date, message, attachment_id) ";
		$sql.= "VALUES ({$newpm['thread_id']}, ".SP()->user->thisUser->ID.", '".current_time('mysql')."', '{$newpm['messagecontent']}', {$newpm['attachment_id']})";

		if (SP()->DB->execute($sql) == false) {
			SP()->notifications->message(1, __('Unable to save new PM message', 'sp-pm'));
			return;
		}

        # grab the new thread id
        $newpm['message_id'] = SP()->rewrites->pageData['insertid'];

        # lets bump up server in case pm to lots of users
        @set_time_limit(0); # take as long as needed
        @ini_set('memory_limit', '256M');

		# process recipient list
		foreach ($newpm['tolist'] as $key => $recipient) {
			$recipient = SP()->filters->integer($recipient);

            # dont allow pm to self
            if ($recipient == SP()->user->thisUser->ID) continue;

			# double check that recipient hasnt listed sender as adversary and recipient is allowed to PM
			$blocked = SP()->DB->select('SELECT user_id FROM '.SPPMADVERSARIES." WHERE user_id=$recipient AND adversary_id=".SP()->user->thisUser->ID, 'var');
			if ($blocked || !sp_pm_get_auth('use_pm', '', $recipient)) continue;

			# determine pm type
		    if ($all) {
			  $pmtype = 1;
		    } else {
			  $pmtype = SP()->filters->integer($newpm['typelist'][$key]);
		    }

            # now save the recipients
			$sql = "INSERT INTO ".SPPMRECIPIENTS;
			$sql.= " (thread_id, message_id, user_id, read_status, pm_type) ";
			$sql.= "VALUES ({$newpm['thread_id']}, {$newpm['message_id']}, $recipient, 0, $pmtype)";
            SP()->DB->execute($sql);

            # send the email notification to recipient
        	if ($sppm['email']) {
    			$uopts = SP()->memberData->get($recipient,'user_options');
    			if ($uopts['pmemail']) $emailmsg = sp_pm_send_email(SP()->displayFilters->name(SP()->user->thisUser->display_name), $recipient, $newpm);
         	}
		}

        # now save the sender copy
		$sql = "INSERT INTO ".SPPMRECIPIENTS;
		$sql.= " (thread_id, message_id, user_id, read_status, pm_type) ";
		$sql.= "VALUES ({$newpm['thread_id']}, {$newpm['message_id']}, ".SP()->user->thisUser->ID.", 1, 1)";
        SP()->DB->execute($sql);
	} else {
		SP()->notifications->message(1, __('This private message has been refused', 'sp-pm'));
		wp_redirect(SP()->spPermalinks->get_url('private-messaging/inbox'));
		die();
	}

	do_action('sph_pm_create', $newpm);

	SP()->notifications->message(0, __('Private Message sent', 'sp-pm'));
}

function sp_pm_send_email($sender, $recipient, $newpm) {
	$eol = "\r\n";

	# get user email address
	$msg = '';
	$email = SP()->DB->table(SPUSERS, "ID=$recipient", 'user_email');
    $title = SP()->displayFilters->title($newpm['title']);

	# recipient message
	$url = SP()->spPermalinks->get_url('private-messaging/inbox');
	$msg.= __('There is a new private message for you on the forum at', 'sp-pm').': '.$url.$eol.$eol;
	$msg.= __('From', 'sp-pm').': '.$sender.$eol;
	$msg.= __('Title', 'sp-pm').': '.$title.$eol.$eol;
	$msg.= SP()->spPermalinks->get_url().$eol;
    $msg = apply_filters('sph_pm_email_notification', $msg, $email, $title, $sender, $newpm);

	$replyto = apply_filters('sph_pm_email_replyto', '');
    $subject = get_option('blogname').' '.__('New private message', 'sp-pm');
    $subject = apply_filters('sph_pm_email_subject', $subject, $email, $sender, $newpm);
	$email_status = sp_send_email($email, $subject, $msg, $replyto);
	return $email_status[1];
}

function sp_pm_get_attachments() {
    require_once SPPLUPLIBDIR.'sp-plupload-components.php';

    sp_plupload_config(SP()->user->thisUser);
    global $plup;

    $attachments = array();
    $numAttachments = 0;

    for ($index = 0; $index < $_POST['sp_file_uploader_count']; $index++) {
    	# make sure the upload was completed
    	if ($_POST['sp_file_uploader_'.$index.'_status'] != 'done') continue;

    	# get the filename, upload type and verify permission to upload
    	$attachment = SP()->filters->str($_POST['sp_file_uploader_'.$index.'_name']);
    	$nameparts = explode('.', $attachment);
    	$ext = end($nameparts);
    	if (!sp_plupload_validate_extension($ext, $plup['filetype']['image'])) {
    		$typenow = 'image';
    		if (!SP()->auths->get('upload_images')) continue;
    	} else if (!sp_plupload_validate_extension($ext, $plup['filetype']['media'])) {
    		$typenow = 'media';
    		if (!SP()->auths->get('upload_media')) continue;
    	} else if (!sp_plupload_validate_extension($ext, $plup['filetype']['file'])) {
    		$typenow = 'file';
    		if (!SP()->auths->get('upload_files')) continue;
    	} else {
    		continue;
    	}
    	$file_name = stripslashes($attachment);
    	$file_name = sp_plupload_clean_filename($file_name);

    	# make sure the file exists
    	$file = $plup['path'][$typenow].$file_name;
    	if (!file_exists($file)) continue;

    	# should be good - prepare for storage in db attachments field
    	$attachments[$numAttachments]['type'] = $typenow;
    	$attachments[$numAttachments]['loc']  = $plup['path'][$typenow];
    	$attachments[$numAttachments]['path'] = $plup['link'][$typenow];
    	$attachments[$numAttachments]['file'] = $file_name;
    	$attachments[$numAttachments]['size'] = @filesize($file);
    	$numAttachments++;
    }

    return $attachments;
}
