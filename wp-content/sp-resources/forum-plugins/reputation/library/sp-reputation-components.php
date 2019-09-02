<?php
/*
Simple:Press
Reputation System Plugin Support Routines
$LastChangedDate: 2018-10-27 16:41:49 -0500 (Sat, 27 Oct 2018) $
$Rev: 15775 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_reputation_do_admin_cap_list($user) {
	$manage_reputaton = user_can($user, 'SPF Manage Reputation');
	echo '<li>';
	spa_render_caps_checkbox(__('Manage Reputation', 'sp-reputation'), "manage-reputation[$user->ID]", $manage_reputaton, $user->ID);
	echo '</li>';
}

function sp_reputation_do_admin_cap_form($user) {
	echo '<li>';
	spa_render_caps_checkbox(__('Manage Reputation', 'sp-reputation'), 'add-reputation', 0);
	echo '</li>';
}

function sp_reputation_do_admin_caps_update($still_admin, $remove_admin, $user) {
    $manage_reputaton = (isset($_POST['manage-reputation'])) ? $_POST['manage-reputation'] : '';

    # was this admin removed?
    if (isset($remove_admin[$user->ID])) $manage_reputaton = '';

	if (isset($manage_reputaton[$user->ID])) {
		$user->add_cap('SPF Manage Reputation');
	} else {
		$user->remove_cap('SPF Manage Reputation');
	}
	$still_admin = $still_admin || isset($manage_reputaton[$user->ID]);
	return $still_admin;
}

function sp_reputation_do_admin_caps_new($newadmin, $user) {
    $reputation = (isset($_POST['add-reputation'])) ? $_POST['add-reputation'] : '';
	if ($reputation == 'on') $user->add_cap('SPF Manage Reputation');
	$newadmin = $newadmin || $reputation == 'on';
	return $newadmin;
}

function sp_reputation_do_member_add($userid) {
	$option = SP()->options->get('reputation');
    SP()->memberData->update($userid, 'reputation', (int) $option['defrep']);
}

function sp_reputation_do_member_del($userid) {
    if (empty($userid)) return;

    # user deleted, so remove any activity of his ratings
    SP()->activity->delete('type='.SPACTIVITY_REPUTATION."&meta=$userid");

    # user deleted, so remove any times he was rated
    SP()->activity->delete('type='.SPACTIVITY_REPUTATION."&uid=$userid");
}

function sp_reputation_do_register_check($user_login, $user) {
    # check and see if doing registration reputation - admins dont gain/lose reputation
	$option = SP()->options->get('reputation');
    if (SP()->auths->forum_admin($user->ID) || $option['regrep'] < 1) return;

    # get users current reputation gain for registration time
    $regrep = get_user_meta($user->ID, 'sp_reputation_registration', true);
	if ($regrep == '') $regrep = 0; # make sure WP doesnt give us empty string

    # calculate number of days since registration
    $register_date = strtotime($user->data->user_registered);
    $current_date = time();
    $datediff = $current_date - $register_date;
    $days = floor($datediff / (60 * 60 * 24));

    # calculate reputation gained
    $repgain = floor($days / $option['regrep']) * 10;

    # see if we need to add reputation
    if ($repgain > $regrep) {
        # save new registration reputation
        update_user_meta($user->ID, 'sp_reputation_registration', $repgain);

        # get the gained reputation and update overall reputation
        $gain = $repgain - $regrep;
        $newrep = SP()->memberData->get($user->ID, 'reputation') + $gain;
        SP()->memberData->update($user->ID, 'reputation', $newrep);
    }
}

function sp_reputation_do_post_check($newpost) {
    # check and see if doing post count reputation - guests/admins dont gain/lose reputation
	$option = SP()->options->get('reputation');
    if (SP()->user->thisUser->guest || SP()->user->thisUser->admin || $option['postrep'] < 1) return;

    # get users current reputation gain for post count
    $postrep = get_user_meta($newpost['userid'], 'sp_reputation_posts', true);

    # get user number of posts
    $posts = SP()->memberData->get(SP()->user->thisUser->ID, 'posts');

    # calculate reputation gained
    $repgain = floor($posts / $option['postrep']) * 10;

    # see if we need to add reputation
    if ($repgain > $postrep) {
        # save new post reputation
        update_user_meta(SP()->user->thisUser->ID, 'sp_reputation_posts', $repgain);

        # get the gained reputation and update overall reputation
        $gain = $repgain - $postrep;
        $newrep = SP()->memberData->get(SP()->user->thisUser->ID, 'reputation') + $gain;
        SP()->memberData->update(SP()->user->thisUser->ID, 'reputation', $newrep);
    }
}

function sp_reputation_do_add_post_class($rowClass, $sectionName, $a) {
    # only do for posts
    if (($sectionName == 'post' || $sectionName == 'eachPost') && !SP()->forum->view->thisPostUser->guest && !SP()->forum->view->thisPostUser->admin) {
    	$option = SP()->options->get('reputation');

        # do we need to highlight this reputable user?
        if ($option['highlight'] && SP()->forum->view->thisPostUser->reputation >= $option['highlightrep']) $rowClass.= ' spHighReputation';

        # do we need to lowlight this low reputable user?
        if ($option['lowlight'] && SP()->forum->view->thisPostUser->reputation <= $option['lowlightrep']) $rowClass.= ' spLowReputation';
    }

    return $rowClass;
}

function sp_reputation_do_add_css() {
    # enequeu our css file
	$css = SP()->theme->find_css(SPREPCSS, 'sp-reputation.css', 'sp-reputation.spcss');
	SP()->plugin->enqueue_style('sp-reputation', $css);

    # direct load css for high/low lighting if in use and on topic view
    if (SP()->rewrites->pageData['pageview'] == 'topic') {
    	$option = SP()->options->get('reputation');

        if ($option['highlight']) echo '<style>#spMainContainer .spTopicPostSection.spHighReputation .spPostSection .spPostContentSection {background-color: #'.$option['highlightcss'].';}</style>';
        if ($option['lowlight']) echo '<style>#spMainContainer .spTopicPostSection.spLowReputation .spPostSection .spPostContentSection {background-color: #'.$option['lowlightcss'].';}</style>';
    }
}

function sp_reputation_get_level($reputation=0, $admin=false) {
    $reputationLevel = new stdClass();

    # sort the reputation levels to find one for this user
	$reputation_levels = SP()->meta->get('reputation level');
	if ($reputation_levels) {
        $levels = array();
		foreach ($reputation_levels as $x => $level) {
			$levels['id'][$x] = $level['meta_id'];
			$levels['name'][$x] = $level['meta_key'];
			$levels['points'][$x] = $level['meta_value']['points'];
			$levels['maxgive'][$x] = $level['meta_value']['maxgive'];
			$levels['maxday'][$x] = $level['meta_value']['maxday'];
			$levels['badge'][$x] = (!empty($level['meta_value']['badge'])) ? $level['meta_value']['badge'] : '';
		}
		array_multisort($levels['points'], SORT_ASC, $levels['name'], $levels['maxgive'], $levels['maxday'], $levels['badge'], $levels['id']);

        # all users will get at least lowel level of reputation
        # admins will get max repuation
        if ($admin) {
            $high = count($levels['points']) - 1;
    		$reputationLevel->name = $levels['name'][$high];
    		$reputationLevel->maxgive = 10000;
    		$reputationLevel->maxday = 100000;
    		$reputationLevel->badge = esc_url(SP_STORE_URL.'/'.SP()->plugin->storage['reputation'].'/'.$levels['badge'][$high]);
        } else {
    		$reputationLevel->name = $levels['name'][0];
    		$reputationLevel->maxgive = $levels['maxgive'][0];
    		$reputationLevel->maxday = $levels['maxday'][0];
    		$reputationLevel->badge = esc_url(SP_STORE_URL.'/'.SP()->plugin->storage['reputation'].'/'.$levels['badge'][0]);

        	# find reputation level of current user
        	for ($x = 0; $x < count($levels['points']); $x++) {
        		if ($reputation <= $levels['points'][$x]) {
        			if ($levels['badge'][$x] && file_exists(SP_STORE_DIR.'/'.SP()->plugin->storage['reputation'].'/'.$levels['badge'][$x])) {
        				$reputationLevel->badge = esc_url(SP_STORE_URL.'/'.SP()->plugin->storage['reputation'].'/'.$levels['badge'][$x]);
        			}
        			$reputationLevel->name = $levels['name'][$x];
        			$reputationLevel->maxgive = $levels['maxgive'][$x];
        			$reputationLevel->maxday = $levels['maxday'][$x];
        			break;
        		}
        	}
        }
    } else {
		$reputationLevel->badge = '';
		$eputationLevel->name = '';
		$eputationLevel->maxgive = 0;
		$eputationLevel->maxday = 0;
    }

    return $reputationLevel;
}

function sp_reputation_get_daily_give($user_id) {
    $daily = 0;

    # handle admins - no max daily
    if (SP()->auths->forum_admin($user_id)) return $daily;

    $past_give = maybe_unserialize(get_user_meta($user_id, 'sp_reputation_daily', true));
    if (!empty($past_give)) {
        $day = time() - DAY_IN_SECONDS;
        $update = false;
        foreach ($past_give as $idx => $give) {
            if ($give['timestamp'] > $day) {
                $daily = $daily + $give['value'];
            } else {
                unset($past_give[$idx]);
                $update = true;
            }
        }

        # cleanup the daily log if some have expired
        if ($update) {
            $past_give = array_values($past_give);
            update_user_meta($user_id, 'sp_reputation_daily', maybe_serialize($past_give));
        }
    }

    return $daily;
}

function sp_reputation_update_daily_give($user_id, $new_give) {
    $past_give = (array) maybe_unserialize(get_user_meta($user_id, 'sp_reputation_daily', true));
    if (!empty($past_give)) {
        $day = time() - DAY_IN_SECONDS;
        foreach ($past_give as $idx => $give) {
            if (isset($give['timestamp']) && $give['timestamp'] < $day) unset($past_give[$idx]);
        }
        $past_give = array_values($past_give);
    }

    $this_give = array();
    $this_give['timestamp'] = time();
    $this_give['value'] = $new_give;
    array_push($past_give, $this_give);

    # handle admins - no max daily
    if (SP()->auths->forum_admin($user_id)) $past_give = array();

    update_user_meta($user_id, 'sp_reputation_daily', maybe_serialize($past_give));
}


function sp_reputation_do_add_user_class(&$user) {
    # get reputation level for user
	$repLevel = (isset($user->reputation)) ? sp_reputation_get_level($user->reputation, $user->admin) : new stdClass();
    $user->reputation_level = $repLevel;

    # get posts/users this user has rated
    $activities = SP()->activity->get_col('col=item&type='.SPACTIVITY_REPUTATION."&uid=$user->ID");
    $user->reputation_posts = (!empty($activities)) ? array_flip($activities) : $activities;

    # set up daily reputation
    $user->reputation_daily = ($user->admin) ? 0 : sp_reputation_get_daily_give($user->ID);
}

function sp_reputation_do_posts_deleted($post) {
    if (empty($post)) return;

    # post deleted, so remove any activity for those posts
    SP()->activity->delete('type='.SPACTIVITY_REPUTATION."&item=$post->post_id");
}

function sp_reputation_do_topic_deleted($posts) {
    if (empty($posts)) return;

    # topic deleted, so remove any activity for those posts
    if (is_object($posts)) {
    		SP()->activity->delete('type='.SPACTIVITY_REPUTATION."&item=$posts->post_id");
    } else {
        foreach ($posts as $post) {
		    SP()->activity->delete('type='.SPACTIVITY_REPUTATION."&item=$post->post_id");
        }
    }
}
