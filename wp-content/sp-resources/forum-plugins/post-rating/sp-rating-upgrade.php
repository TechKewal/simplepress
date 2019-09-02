<?php
/*
Simple:Press
Name plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_rating_do_upgrade_check() {
    if (!SP()->plugin->is_active('post-rating/sp-rating-plugin.php')) return;

    $options = SP()->options->get('postratings');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPRATINGDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades
    if ($db < 1) {
	   SP()->DB->execute('UPDATE '.SPAUTHS." SET auth_cat=7 WHERE auth_name='rate_posts'");
    }

    if ($db < 2) {
		unset($options['summary']);
    }

    if ($db < 3) {
		# create activity data from members column
		$sql = "SELECT user_id, posts_rated FROM ".SPMEMBERS." WHERE posts_rated <> '';";
		$results = SP()->DB->select($sql);
		if($results) {
			foreach($results as $r) {
				$rateData = unserialize($r->posts_rated);
				$topicid = SP()->DB->table(SPPOSTS, 'post_id='.$rateData[0], 'topic_id');
				if($topicid) {
					SP()->activity->add($r->user_id, SPACTIVITY_RATING, $rateData[0], $topicid);
				}
			}
		}
		# and remove redundant members column
		SP()->DB->execute('ALTER TABLE '.SPMEMBERS.' DROP posts_rated');
	}

    if ($db < 4) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

    # save data
    $options['dbversion'] = SPRATINGDBVERSION;
    SP()->options->update('postratings', $options);
}
