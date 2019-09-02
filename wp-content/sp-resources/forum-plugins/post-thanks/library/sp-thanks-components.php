<?php
/*
Simple:Press
Thank and Points Plugin support components
$LastChangedDate: 2017-12-31 09:40:24 -0600 (Sun, 31 Dec 2017) $
$Rev: 15619 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_thanks_do_load_js($footer) {
    $script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? THANKSSCRIPT.'sp-thanks.js' : THANKSSCRIPT.'sp-thanks.min.js';
	SP()->plugin->enqueue_script('spthanks', $script, array('jquery'), false, $footer);
}

function sp_thanks_do_post_records($tData, $posts, $users) {
	if (!empty($posts)) {
		# Do data for each post
		$p = implode(',', $posts);
		$sql = "SELECT ".SPUSERACTIVITY.".user_id, item_id, display_name
				FROM ".SPUSERACTIVITY." JOIN ".SPMEMBERS." ON ".SPUSERACTIVITY.".user_id=".SPMEMBERS.".user_id
				WHERE type_id=".SPACTIVITY_THANKS."
				AND item_id IN (".$p.") ORDER BY id";
		$recs = SP()->DB->select($sql);

		# Init arrays
		foreach ($posts as $post) {
			$tData->posts[$post]->post_thanks = array();
		}
		if ($recs) {
			$idx = 0;
			foreach ($recs as $r) {
                $tData->posts[$r->item_id]->post_thanks[$idx] = new stdClass();
				$tData->posts[$r->item_id]->post_thanks[$idx]->user_id = $r->user_id;
				$tData->posts[$r->item_id]->post_thanks[$idx]->display_name = $r->display_name;
				$idx++;
			}
		}
		# Do data for each user
		if ($users) {
			$ulist = array();
			foreach ($users as $user) {
				if (!empty($user)) {
					$ulist[] = $user;
				}
			}

			$u = implode(',', $ulist);
            if (!empty($u)) {
    			$sql = "SELECT user_id, COUNT(*) AS thanks FROM ".SPUSERACTIVITY."
    					WHERE user_id IN (".$u.") AND type_id=".SPACTIVITY_THANKS." GROUP BY user_id";
    			$given = SP()->DB->select($sql);
    			$sql = "SELECT user_id, COUNT(*) AS thanked FROM ".SPUSERACTIVITY."
    					WHERE user_id IN (".$u.") AND type_id=".SPACTIVITY_THANKED." GROUP BY user_id";
    			$received = SP()->DB->select($sql);
            }

			# Init arrays
			foreach ($tData->posts as $post) {
				if ($post->user_id != SP()->user->thisUser->ID && $post->user_id > 0) {
					$found = false;
					foreach ($given as $thanks) {
						if ($thanks->user_id == $post->user_id) {
							$post->postUser->thanks = $thanks->thanks;
							$found = true;
							break;
						}
					}
					if (!$found) $post->postUser->thanks = 0;

					$found = false;
					foreach ($received as $thanked) {
						if ($thanked->user_id == $post->user_id) {
							$post->postUser->thanked = $thanked->thanked;
							$found = true;
							break;
						}
					}
					if (!$found) $post->postUser->thanked = 0;
				}
			}
		}
	}
	return $tData;
}
