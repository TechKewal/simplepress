<?php
/*
Simple:Press
Subscription Plugin Admin Members Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_subscriptions_admin_members_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$subs = SP()->options->get('subscriptions');

    $save = $subs['forumsubs'];

	if (isset($_POST['autosub'])) { $subs['autosub'] = true; } else { $subs['autosub'] = false; }
	if (isset($_POST['forumsubs'])) { $subs['forumsubs'] = true; } else { $subs['forumsubs'] = false; }
	if (isset($_POST['defnewtopics'])) { $subs['defnewtopics'] = true; } else { $subs['defnewtopics'] = false; }
	if (isset($_POST['digestforce'])) { $subs['digestforce'] = true; } else { $subs['digestforce'] = false; }
	if (isset($_POST['digestcontent'])) { $subs['digestcontent'] = true; } else { $subs['digestcontent'] = false; }
	if (isset($_POST['includepost'])) { $subs['includepost'] = true; } else { $subs['includepost'] = false; }

    # handle digest enabling/disabling
    $oldDigest = $subs['digestsub'];
	if (isset($_POST['digestsub'])) { $subs['digestsub'] = true; } else { $subs['digestsub'] = false; }
    if ($oldDigest != $subs['digestsub']) {
        wp_clear_scheduled_hook('sph_subs_digest_cron');
    	wp_schedule_event(time(), 'sp_subs_digest_interval', 'sph_subs_digest_cron');
    }

	$subs['digesttype'] = (isset($_POST['digesttype'])) ? SP()->filters->integer($_POST['digesttype']) : 1;

	SP()->options->update('subscriptions', $subs);

    # do we need to add or remove the forum subscription menu
    if ($save != $subs['forumsubs']) {
        if ($subs['forumsubs']) {
        	SP()->profile->add_menu('Subscriptions', 'Forum Subscriptions', SFORMSDIR.'sp-subscriptions-forum-form.php');
        } else {
        	SP()->profile->delete_menu('Subscriptions', 'Forum Subscriptions');
        }
    }
	return __('Options updated', 'sp-subs');
}
