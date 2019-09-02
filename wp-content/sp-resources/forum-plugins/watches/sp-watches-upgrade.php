<?php
/*
Simple:Press
Watches plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_watches_do_upgrade_check() {
    if (!SP()->plugin->is_active('watches/sp-watches-plugin.php')) return;

    $watches = SP()->options->get('watches');

    $db = $watches['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPWATCHESDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades
    if ($db < 1) {
       	SP()->profile->add_tab('Watches');
    	SP()->profile->add_menu('Watches', 'Manage Watches', WFORMSDIR.'sp-watches-manage-form.php');
    }

    if ($db < 2) {
	   SP()->DB->execute('UPDATE '.SPAUTHS." SET auth_cat=1 WHERE auth_name='watch'");
    }

	if ($db < 3) {
		# upgrade to new activity format
		$sql = "SELECT user_id, watches FROM ".SPMEMBERS." WHERE watches != ''";
		$r = SP()->DB->select($sql);
		if($r) {
			foreach($r as $watch) {
				$t = unserialize($watch->watches);
				if($t) {
					foreach($t as $topic) {
						SP()->activity->add($watch->user_id, SPACTIVITY_WATCH, $topic);
					}
				}
			}
		}
		SP()->DB->execute('ALTER TABLE '.SPTOPICS.' DROP topic_watches');
		SP()->DB->execute('ALTER TABLE '.SPMEMBERS.' DROP watches');
	}

    if ($db < 4) {
        # set autoload flag to true for autoupdates
    	$meta = SP()->meta->get('autoupdate', 'watches');
    	if (!empty($meta[0])) SP()->meta->update('autoupdate', 'watches', $meta[0]['meta_value'], $meta[0]['meta_id']);
    }

    if ($db < 5) {
		# in upgrade 3 we may have left some dead records behind so now we can remove them
		$sql = "SELECT id FROM ".SPUSERACTIVITY."
				LEFT JOIN ".SPTOPICS." ON ".SPUSERACTIVITY.".item_id = ".SPTOPICS.".topic_id
				WHERE type_id = ".SPACTIVITY_WATCH." AND topic_id IS NULL";
		$dead = SP()->DB->select($sql, 'col');
		if($dead) {
			foreach($dead as $id) {
				SP()->activity->delete("id=$id");
			}
		}
	}

    if ($db < 6) {
		# update to namespaced js
	    $up = SP()->meta->get('autoupdate', 'watches');
		$up[0]['meta_value'][0] = 'spj.watchesupdate';
	    SP()->meta->update('autoupdate', 'watches', $up[0]['meta_value'], $up[0]['meta_id']);
	}

	# save data
    $watches['dbversion'] = SPWATCHESDBVERSION;
    SP()->options->update('watches', $watches);
}
