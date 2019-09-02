<?php
/*
Simple:Press
Buddypress Plugin Admin Options Save Routine
$LastChangedDate: 2018-08-26 16:55:40 -0500 (Sun, 26 Aug 2018) $
$Rev: 15725 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_buddypress_admin_options_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	# Save Thanks plugin options
	$bpdata = SP()->options->get('buddypress');

	$bpdata['activity'] = SP()->filters->integer($_POST['activity']);
	$bpdata['avatar'] = SP()->filters->integer($_POST['avatar']);

	if (isset($_POST['bpavatarsize'])) { $bpdata['bpavatarsize'] = true; } else { $bpdata['bpavatarsize'] = false; }

	if (isset($_POST['integrateprofile'])) { $bpdata['integrateprofile'] = true; } else { $bpdata['integrateprofile'] = false; }
	if (isset($_POST['integratesubs'])) { $bpdata['integratesubs'] = true; } else { $bpdata['integratesubs'] = false; }
	if (isset($_POST['integratewatches'])) { $bpdata['integratewatches'] = true; } else { $bpdata['integratewatches'] = false; }

	if (isset($_POST['uselinks'])) { $bpdata['uselinks'] = true; } else { $bpdata['uselinks'] = false; }
	if (isset($_POST['newlink'])) { $bpdata['newlink'] = true; } else { $bpdata['newlink'] = false; }
	if (isset($_POST['inboxlink'])) { $bpdata['inboxlink'] = true; } else { $bpdata['inboxlink'] = false; }
	if (isset($_POST['subslink'])) { $bpdata['subslink'] = true; } else { $bpdata['subslink'] = false; }
	if (isset($_POST['watcheslink'])) { $bpdata['watcheslink'] = true; } else { $bpdata['watcheslink'] = false; }
	if (isset($_POST['profilelink'])) { $bpdata['profilelink'] = true; } else { $bpdata['profilelink'] = false; }
	if (isset($_POST['startedlink'])) { $bpdata['startedlink'] = true; } else { $bpdata['startedlink'] = false; }
	if (isset($_POST['postedlink'])) { $bpdata['postedlink'] = true; } else { $bpdata['postedlink'] = false; }

	if (isset($_POST['usenotifications'])) { $bpdata['usenotifications'] = true; } else { $bpdata['usenotifications'] = false; }
	if (isset($_POST['newnotifications'])) { $bpdata['newnotifications'] = true; } else { $bpdata['newnotifications'] = false; }
	if (isset($_POST['inboxnotifications'])) { $bpdata['inboxnotifications'] = true; } else { $bpdata['inboxnotifications'] = false; }
	if (isset($_POST['subsnotifications'])) { $bpdata['subsnotifications'] = true; } else { $bpdata['subsnotifications'] = false; }
	if (isset($_POST['watchesnotifications'])) { $bpdata['watchesnotifications'] = true; } else { $bpdata['watchesnotifications'] = false; }

	SP()->options->update('buddypress', $bpdata);

	$out = __('BuddyPress options updated', 'sp-buddypress');
	return $out;
}
