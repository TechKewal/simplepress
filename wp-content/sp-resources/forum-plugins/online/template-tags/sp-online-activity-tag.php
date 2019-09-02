<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_OnlineSiteActivityTag($args) {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	if (!SP()->auths->get('view_online_activity')) return;

	$defs = array('pGeneralClass'	=> 'spOnlineActivity',
				  'pForumClass'		=> 'spOnlineActivity',
				  'pTopicClass'		=> 'spOnlineActivity',
				  'pTitleClass'		=> 'spOnlineActivity',
				  'pUserClass'		=> 'spOnlineActivity',
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_SiteActivity_args', $a);
	extract($a, EXTR_SKIP);

	$pGeneralClass 	= esc_attr($pGeneralClass);
	$pForumClass 	= esc_attr($pForumClass);
	$pTopicClass 	= esc_attr($pTopicClass);
	$pTitleClass 	= esc_attr($pTitleClass);
	$pUserClass 	= esc_attr($pUserClass);

	# members online
	$out = '';
	$members = spOnlineGetOnline();
	if ($members) {
		global $firstDisplay;

        $out = '';

		$spMemberOpts = SP()->options->get('sfmemberopts');
		$default = $online = $profile = $list = $report = $search = $group = false;
        $curPage = $curForum = $curTopic = -1;
        $curGuests = 0;
        $first = $firstDisplay = false;
		foreach ($members as $user) {
            # is the user hiding online status? admins can always see
 			if ($user->trackuserid) {
 			    $userOpts = unserialize($user->user_options);
                if (!SP()->user->thisUser->admin && $spMemberOpts['sfhidestatus'] && $userOpts['hidestatus']) continue;
            }

            # do we need to dump quests for previous section?
            if ($curPage != $user->pageview && $curPage != -1) {
				if ($curGuests > 0) {
					if (!$firstDisplay) $out.= ', ';
					$out.= $curGuests.' '.__('Guest(s)', 'spwo');
					$curGuests = 0;
				}
				$out.= '</span></p>';
                $firstDisplay = false;
            }
            $curPage = $user->pageview;

            # output pageview activity
            switch ($curPage) {
                case 'online':
                    if (!$online) {
        				$firstDisplay = true;
                        $online = true;
        				$out.= '<br />';
        				$out.= "<p class='$pGeneralClass'>".__('Viewing online activity', 'spwo').': </p>';
        				$out.= "<p class='$pGeneralClass'>".__('User(s)', 'spwo').": <span class='$pUserClass'>";
                    }
                    break;

                case 'profileedit':
                case 'profileshow':
                    if (!$profile) {
        				$firstDisplay = true;
                        $profile = true;
        				$out.= '<br />';
        				$out.= "<p class='$pGeneralClass'>".__('Viewing profile', 'spwo').': </p>';
        				$out.= "<p class='$pGeneralClass'>".__('User(s)', 'spwo').": <span class='$pUserClass'>";
                    }
                    break;

                case 'list':
                    if (!$list) {
        				$firstDisplay = true;
                        $list = true;
        				$out.= '<br />';
        				$out.= "<p class='$pGeneralClass'>".__('Viewing members list', 'spwo').': </p>';
        				$out.= "<p class='$pGeneralClass'>".__('User(s)', 'spwo').": <span class='$pUserClass'>";
                    }
                    break;

                case 'report-post':
                    if (!$report) {
        				$firstDisplay = true;
                        $report = true;
        				$out.= '<br />';
        				$out.= "<p class='$pGeneralClass'>".__('Reporting a post', 'spwo').': </p>';
        				$out.= "<p class='$pGeneralClass'>".__('User(s)', 'spwo').": <span class='$pUserClass'>";
                    }
                    break;

                case 'search':
                    if (!$search) {
        				$firstDisplay = true;
                        $search = true;
        				$out.= '<br />';
        				$out.= "<p class='$pGeneralClass'>".__('Viewing search results', 'spwo').': </p>';
        				$out.= "<p class='$pGeneralClass'>".__('User(s)', 'spwo').": <span class='$pUserClass'>";
                    }
                    break;

                case 'group':
                    if (!$group) {
        				$firstDisplay = true;
                        $group = true;
        				$out.= '<br />';
        				$out.= "<p class='$pGeneralClass'>".__('Viewing forum main page', 'spwo').': </p>';
        				$out.= "<p class='$pGeneralClass'>".__('User(s)', 'spwo').": <span class='$pUserClass'>";
                    }
                    break;

                case 'forum':
                    if (!SP()->auths->get('view_online_activity', $user->forum_id)) continue 2;
        			if ($first && $curForum != $user->forum_id && $user->forum_id != 0) {
        				if ($curGuests > 0) {
        					if (!$firstDisplay) $out.= ', ';
        					$out.= $curGuests.' '.__('Guest(s)', 'spwo');
        					$curGuests = 0;
        				}
        				$out.= '</span></p>';
        				$first = false;
        				$firstDisplay = true;
        			}
        			if ($curForum != $user->forum_id && $user->forum_id != 0) {
        				$firstDisplay = true;
        				$out.= '<br />';
        				$out.= "<p class='$pForumClass'>".__('Viewing forum', 'spwo')." <span class='$pTitleClass'><a href='".SP()->spPermalinks->build_url($user->forum_slug, '', 1, 0)."'>$user->forum_name</a></span></p>";
        				$out.= "<p class='$pGeneralClass'>".__('User(s)', 'spwo').": <span class='$pUserClass'>";
        				$first = true;
        				$curForum = $user->forum_id;
        			}
                    break;

                case 'topic':
                    if (!SP()->auths->get('view_online_activity', $user->forum_id)) continue 2;
        			if ($first && $curTopic != $user->topic_id && $curForum != 0 && $user->topic_id != 0) {
        				if ($curGuests > 0) {
        					if (!$firstDisplay) $out.= ', ';
        					$out.= $curGuests.' '.__('Guest(s)', 'spwo');
        					$curGuests = 0;
        				}
        				$out.= '</span></p>';
        				$first = false;
        				$firstDisplay = true;
        			}
        			if ($curTopic != $user->topic_id && $curForum != 0 && $user->topic_id != 0) {
        				$firstDisplay = true;
        				$out.= '<br />';
        				$out.= "<p class='$pTopicClass'>".__('Viewing topic', 'spwo')." <span class='$pTitleClass'><a href='".SP()->spPermalinks->build_url($user->forum_slug, $user->topic_slug, 1, 0)."'>$user->forum_name -> $user->topic_name</a></span></p>";
        				$out.= "<p class='$pGeneralClass'>".__('User(s)', 'spwo').": <span class='$pUserClass'>";
        				$first = true;
        				$curTopic = $user->topic_id;
        			}
                    break;

                default:
                	$tempOut = apply_filters('sph_OnlineActivityPageview', '', $user, $pGeneralClass, $pTitleClass, $pUserClass);
                    if (empty($tempOut)) {
                        if (!$default) {
            				$firstDisplay = true;
                            $default = true;
            				$tempOut.= '<br />';
            				$tempOut.= "<p class='$pGeneralClass'>".__('Viewing unspecified page', 'spwo').': </p>';
            				$tempOut.= "<p class='$pGeneralClass'>".__('User(s)', 'spwo').": <span class='$pUserClass'>";
                        }
                    }
                    $out.= $tempOut;
                    break;
            }

 			if ($user->trackuserid) {
				if (!$firstDisplay) $out.= ', ';
				$out.= SP()->user->name_display($user->trackuserid, SP()->displayFilters->name($user->display_name), true);
				$firstDisplay = false;
			} else {
				$curGuests++;
			}
		}
	}

	if ($curGuests > 0) {
		if (!$firstDisplay) $out.= ', ';
		$out.= $curGuests.' '.__('Guest(s)', 'spwo');
	}
	$out.= '</span></p>';

	$out = apply_filters('sph_SiteActivity', $out, $a);
	echo $out;
}
