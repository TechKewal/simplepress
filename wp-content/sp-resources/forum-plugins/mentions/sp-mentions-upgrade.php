<?php
/*
Simple:Press
Mentions plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_mentions_do_upgrade_check() {
    if (!SP()->plugin->is_active('mentions/sp-mentions-plugin.php')) return;

    $options = SP()->options->get('mentions');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPMENTIONSDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades
    if ($db < 1) {
		# remove mentions array from options record
		$options = SP()->options->get('mentions');
		$o = array();
		$o['notification'] = $options['notification'];
		$o['latest_number']	= $options['latest_number'];
		$o['dbversion'] = $options['dbversion'];

		$options = $o;

		# Move members mentions to activity table
		$sql = "SELECT mentions FROM ".SPMEMBERS." WHERE mentions IS NOT NULL";
		$mentions = SP()->DB->select($sql, 'col');
		if($mentions) {
			foreach($mentions as $mention) {
				$m = unserialize($mention);
				foreach($m as $thism) {
					SP()->activity->add($thism['mentioned'], SPACTIVITY_MENTIONED, $thism['who'], $thism['postid'], false);
				}
			}
		}

		# remove redundant menbers ciolumn
		SP()->DB->execute('ALTER TABLE '.SPMEMBERS.' DROP mentions');
	}

    # db version upgrades
    if ($db < 2) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

    # save data
    $options['dbversion'] = SPMENTIONSDBVERSION;
    SP()->options->update('mentions', $options);
}
