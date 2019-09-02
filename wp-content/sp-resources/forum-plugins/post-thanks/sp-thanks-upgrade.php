<?php
/*
Simple:Press
Post Thanks plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_thanks_do_upgrade_check() {
    if (!SP()->plugin->is_active('post-thanks/sp-thanks-plugin.php')) return;

    $options = SP()->options->get('thanks');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPTHANKSDBVERSION ) return;

    # apply upgrades as needed
    # db version upgrades

    if ($db < 1) {
		$sql = "SELECT post_id, user_id, thanks FROM ".SPPOSTS." WHERE thanks != ''";
		$list = SP()->DB->select($sql);
		foreach($list as $post) {
			$thanks = unserialize($post->thanks);
			foreach ($thanks['userids'] as $key => $id) {
				SP()->activity->add($id, SPACTIVITY_THANKS, $post->post_id);
				SP()->activity->add($post->user_id, SPACTIVITY_THANKED, $post->post_id, '', false);
			}
		}
		SP()->DB->execute('ALTER TABLE '.SPPOSTS.' DROP thanks');
		SP()->DB->execute('ALTER TABLE '.SPMEMBERS.' DROP thanks');
		SP()->DB->execute('ALTER TABLE '.SPMEMBERS.' DROP thanked');
	}

    if ($db < 2) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

    if ($db < 3) {
    	$options['thank-message-save'] = __('User Thanked', 'sp-thanks');
	}

    # save data
    $options['dbversion'] = SPTHANKSDBVERSION;
    SP()->options->update('thanks', $options);
}
