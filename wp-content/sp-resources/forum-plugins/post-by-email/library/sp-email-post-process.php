<?php
/*
Simple:Press
Post by Email - Processing
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# --------------------------------------------------
# sp_emailpost_process_emails()
#
# Checks the proscribed email inbox for emails and
# creates them as posts if all is correct
# --------------------------------------------------
function sp_emailpost_process_emails() {
	# grab the settings in case we just need to abandon
	# Go get the PBE settings
	$spemailpost = SP()->options->get('spEmailPost');
	if (empty($spemailpost)) return;

	extract($spemailpost);
	if (empty($server) || empty($port) || empty($pass)) return;

	# next grab all of the available email addresses if any
	$query = new stdClass();
		$query->table 	= SPFORUMS;
		$query->fields	= 'forum_id, forum_slug, forum_email';
		$query->where	= "forum_email <> ''";
	$r = SP()->DB->select($query);
	if (!$r) return;

	# game on so check and load the protocol we are using
	$imap = function_exists('imap_open');
	if ($imap) {
		require_once SPEPLIB.'sp-email-post-imap.php';
	} else {
		require_once ABSPATH.WPINC.'/class-pop3.php';
		require_once SPEPLIB.'sp-email-post-pop.php';
	}

	# create log array
	$log = array('eDate'=>current_time('mysql'), 'eForum'=>'-', 'eTopic'=>'-', 'eUser'=>'-', 'eLog'=>'-');

	# process one forum address at a time
	foreach($r as $f) {
		$log['eForum'] = $f->forum_email;

		if ($imap) {
			# create an imap connection if we can
			$opts = "/pop3";
			$opts.= (isset($tls) && $tls) ? "/tls" : "/notls";
			if (isset($ssl) && $ssl) $opts.= "/ssl";

			$mBox = @imap_open("{".$server.":".$port.$opts."}INBOX", $f->forum_email, $pass);
			if ($mBox) {
				$mCount = imap_num_msg($mBox);
				if ($mCount) {
					sp_process_inbox_imap($mBox, $f, $mCount, $log);
				} else {
				    # clear the imap empty inbox eror so it doesnt generate php error and then close mailbox
                    $foo = imap_errors();
                    @imap_close($mBox);
				}
			} else {
				$log['eLog'] = __('Connection to mailbox failed', 'sp-pbe');
				sp_pbe_log($log);
			}
		} else {
			# create a new POP3 class and connect if we can
			$mBox = new POP3();
			if ($ssl) $server = 'ssl://'.$server;
			if ($mBox->connect($server, $port) && $mBox->user($f->forum_email)) {
				$mCount = $mBox->pass($pass);
				if (false === $mCount) {
					$log['eLog']=__('Password to mailbox failed', 'sp-pbe');
					sp_pbe_log($log);
				} else {
					if ($mCount) {
						sp_process_inbox_pop($mBox, $f, $mCount, $log);
					}
				}
			} else {
				$log['eLog']=__('Connection to mailbox failed', 'sp-pbe');
				sp_pbe_log($log);
			}
			$mBox->quit();
		}
	}

    # let others know what we have done
	do_action('sph_emailpost_cron_done');
}

# --------------------------------------------------
# sp_emailpost_save()
#
# Save each email to a forum post
# --------------------------------------------------
function sp_emailpost_save($f, $subject, $slug, $user, $author, $content, $log, $attachments = '') {
	if (empty($slug)) {
		$slug = sp_create_slug($subject, false);
	}
	$topic = SP()->DB->table(SPTOPICS, "topic_slug='".$slug."' AND forum_id=".$f->forum_id, 'row', '', '', ARRAY_A);

	$action = 'deny';
	if (empty($topic) && SP()->auths->get('post_by_email_start', $f->forum_id, $user['user_id'])) {
		 $action='topic';
	} elseif(!empty($topic) && SP()->auths->get('post_by_email_reply', $f->forum_id, $user['user_id'])) {
		 $action='post';
	}

	if ($action == 'deny') {
		$log['eLog']=__('Failed Permissions Check', 'sp-pbe');
		sp_pbe_log($log);
	}

	if ($action != 'deny') {
		require_once SP_PLUGIN_DIR.'/forum/library/sp-post-support.php';
		require_once SP_PLUGIN_DIR.'/forum/database/sp-db-newposts.php';
		require_once SP_PLUGIN_DIR.'/forum/database/sp-db-management.php';

		# Initialise the class -------------------------------------------------------------
		$p = new spcPost;

		# Set up curret user details needed to keep class user agnostic
		$p->userid		= $user['user_id'];
		$p->admin 		= $user['admin'];
		$p->moderator	= $user['moderator'];
		$p->member		= true;
		$p->guest		= false;

		$p->action		= $action;
		$p->call		= 'email';

		$p->newpost['forumid'] 		= $f->forum_id;
		$p->newpost['forumslug'] 	= $f->forum_slug;

		if ($action == 'topic') {
			$p->newpost['topicname']	= $subject;
		} else {
			$p->newpost['topicid'] 		= $topic['topic_id'];
			$p->newpost['topicslug'] 	= $topic['topic_slug'];
			$p->newpost['topicname']	= $topic['topic_name'];
		}

		# Permission checks on forum data
		$p->validatePermission();
		if (!$p->abort) {
			$p->newpost['postcontent']	= stripslashes($content);
			$p->newpost['userid']		= $p->userid;
			$p->newpost['postername']	= $user['display_name'];
			$p->newpost['posteremail']	= $author;
			$p->newpost['posterip']		= '';
			$p->newpost['source']		= SPEPSOURCE;

            # disable flood control for multiple posts
            SP()->cache->delete('floodcontrol');

			$p->validateData();
			if (!$p->abort || (empty($p->newpost['postcontent']) && !empty($attachments))) {
				if (empty($p->newpost['postcontent'])) {
					$p->newpost['postcontent'] = '<p>&nbsp;</p>';
				}
				$p->saveData();
				if ($p->abort) {
					$log['eLog']=$p->message;
					sp_pbe_log($log);
				} else {
					$log['eLog']=__('Posted Successfully', 'sp-pbe');
					sp_pbe_log($log);

					# try and save attachments if any and file uploader plugin is active
					if (!empty($attachments) && SP()->plugin->is_active('plupload/sp-plupload-plugin.php')) {
                        require_once SPPLUGINDIR.'plupload/sp-plupload-plugin.php';
                        require_once SPPLUGINDIR.'plupload/library/sp-plupload-components.php';
						sp_emailpost_process_attachments($p->newpost['userid'], $p->newpost['forumid'], $p->newpost['postid'], $attachments, $log);
					}
				}
			} else {
				$log['eLog']=$p->message;
				sp_pbe_log($log);
			}
		} else {
			$log['eLog']=$p->message;
			sp_pbe_log($log);
		}
	}
}

# ------------------------------------------------------------------
# sp_pbe_alt_email()
#
# Looks up the alt_email address to try and find user
# ------------------------------------------------------------------
function sp_pbe_alt_email($email) {
	$userid = SP()->DB->table(SPUSERMETA, "meta_key='alt_user_email' AND meta_value='$email'", 'user_id');
	if (!empty($userid)) {
		return new WP_User($userid);
	} else {
		return '';
	}
}

# ------------------------------------------------------------------
# sp_pbe_log()
#
# Creates entry in table sfmaillog
# ------------------------------------------------------------------
function sp_pbe_log($log) {
	extract($log);
	if (SP()->core->status != 'ok') return;
	if (SP()->DB->connectionExists() == false) return;

	$eTopic = SP()->saveFilters->title($eTopic, SPTOPICS, 'topic_name');

	$sql = "INSERT INTO ".SFMAILLOG;
	$sql.= " (email_date, email_forum, email_topic, email_user, email_log) ";
	$sql.= "VALUES ('$eDate', '$eForum', '$eTopic', '$eUser', '$eLog')";
	SP()->DB->execute($sql);

	# leave just last 50 entries
	if (SP()->rewrites->pageData['insertid'] > 51) {
		$sql = 'DELETE FROM '.SFMAILLOG.' WHERE email_id < '.(SP()->rewrites->pageData['insertid']-50);
		SP()->DB->execute($sql);
	}
}

# ------------------------------------------------------------------
# sp_emailpost_process_attachments()
#
# Attempt to save any attachments to the email
# ------------------------------------------------------------------
function sp_emailpost_process_attachments($userid, $forumid, $postid, $attachments, $log) {
	# set up the user
	$thisUser = SP()->user->get($userid);
	sp_plupload_config($thisUser);

	# loop through the attachments
	foreach ($attachments as $a) {
		$content = '';
		$uName = $a['filename'];

		#2 do the write to the php tmp folder
//		$uPath = sys_get_temp_dir().'/'.$uName;
		$uPath = SP_STORE_DIR.'/'.$uName;

		$handle = @fopen($uPath, "wb");
		if ($handle) $uSize = @fwrite($handle, $a['stream']);
		@fclose($handle);
		if (!$uSize) {
			# do unable to write to temp folder
			$log['eLog'] = __('Unable to save attachment to temp folder', 'sp-pbe');
			sp_pbe_log($log);
			break;
		} else {
			# hive off temp folder path so we can remove later
			$tPath = $uPath;
		}

		#3 check file type is valid and allowed
		$status = sp_plupload_check_prohibited($uName, false);
		if (!$status) {
			# do bad file type log message
			$log['eLog'] = __('Attachment type disallowed', 'sp-pbe');
			sp_pbe_log($log);
			break;
		}

		#4 check user has permission to do this
		$status = sp_plupload_check_permissions($uName, $forumid, false);
		if (!empty($status->error)) {
			# do no permision log message
			$log['eLog'] = __('Attachment use disallowed', 'sp-pbe');
			sp_pbe_log($log);
			break;
		} else {
			$uType = $status->type;
		}

		#5 check the filesize fits within the allowed maximum
		$status = sp_plupload_check_filesize($uType, $uSize, false);
		if (!$status) {
			# do file too big
			$log['eLog'] = __('Attachment exceeded maximum size', 'sp-pbe');
			sp_pbe_log($log);
			break;
		}

		#6 move the file to it's new home
		$status = sp_plupload_move_upload($uName, $uPath, $uType, false, true);
		# and remove it from the tmp folder if still there
		if(file_exists($uPath)) @unlink($uPath);

		if (!empty($status->error)) {
			# unable to move to new location
			$log['eLog'] = __('Unable to move attachment', 'sp-pbe');
			sp_pbe_log($log);
			break;
		} else {
			$uPath = $status->path;
			$uName = $status->filename;
		}

		#7 process the file
		$status = sp_plupload_process_upload($uPath, $uName, $uType, 'default', false);
		if (!empty($status->error)) {
			# unable to process file
			$log['eLog'] = __('Unable to save attachment', 'sp-pbe');
			sp_pbe_log($log);
			break;
		} else {
			if ($uType == 'image') {
				$uWidth = $status->width;
				$uHeight = $status->height;
			}
		}

		#8 add attachment record to post
		$status = sp_plupload_add_attachment($uName, $uType, $postid);

		#9 we need to add any images to the post content
		if ($uType == 'image') {
			$uUrl = stristr ($uPath, '/wp-content');
			$content.= "<img src='$uUrl/$uName' alt='$uName' width='$uWidth' height='$uHeight' />";
		}
	}
	if ($content != '') {
		#10 add the content to the post
		$c = SP()->DB->select('SELECT post_content FROM '.SPPOSTS.' WHERE post_id='.$postid, 'var');
		$c = SP()->editFilters->content($c);
		$c.= $content;
		$c = SP()->saveFilters->content($c, 'edit', true, SPPOSTS, 'post_content');
		SP()->DB->execute('UPDATE '.SPPOSTS." SET post_content='".$c."' WHERE post_id=".$postid);
	}
}
