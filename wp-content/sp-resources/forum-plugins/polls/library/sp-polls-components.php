<?php
/*
Simple:Press
Polls Plugin Support Routines
$LastChangedDate: 2018-10-19 03:14:00 -0500 (Fri, 19 Oct 2018) $
$Rev: 15759 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_polls_do_create_forum() {
	spa_paint_checkbox(__('Enable polls on this forum', 'sp-polls'), 'forum_polls', 1);
}

function sp_polls_do_edit_forum($forum) {
	spa_paint_checkbox(__('Enable polls on this forum', 'sp-polls'), 'forum_polls', $forum->polls);
}

function sp_polls_do_head() {
	$css = SP()->theme->find_css(POLLSCSS, 'sp-polls.css', 'sp-polls.spcss');
    SP()->plugin->enqueue_style('sp-polls', $css);

	$css = SP()->theme->find_css(POLLSCSS, 'jquery-ui.css');
    SP()->plugin->enqueue_style('sp-polls-ui', $css);
}

function sp_polls_do_load_admin_js () {
    wp_enqueue_style('farbtastic');
	$script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? POLLSSCRIPT.'sp-polls-admin.js' : POLLSSCRIPT.'sp-polls-admin.min.js';
    wp_enqueue_script('sp-polls-admin', $script, array('farbtastic'), false, false);
	$strings = array(
		'answer' 	=> esc_js(__('Answer', 'sp-polls')),
        'votes'     => esc_js(__('votes', 'sp-polls')),
        'verify'    => esc_js(__('Are you sure you want to delete this question? This cannot be undone. Save after deleting to update vote counts.','sp-polls')),
        'question'  => esc_js(__('Delete question','sp-polls')),
        'images'    => POLLSIMAGES,
	);
    wp_localize_script('sp-polls-admin', 'sp_polls_admin_vars', $strings);
}

function sp_polls_do_load_js($footer) {
    $script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? POLLSSCRIPT.'sp-polls.js' : POLLSSCRIPT.'sp-polls.min.js';
	SP()->plugin->enqueue_script('sp-polls', $script, array('jquery', 'jquery-form', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-datepicker'), false, $footer);
	$strings = array(
		'answer' 		=> esc_js(__('Answer', 'sp-polls')),
		'remove' 		=> esc_js(__('Remove Answer', 'sp-polls')),
        'toomany'       => esc_js(__('You have entered more than the max allowed votes', 'sp-polls')),
        'missing'       => esc_js(__('You did not enter a vote', 'sp-polls')),
	);
    SP()->plugin->localize_script('sp-polls', 'sp_polls_vars', $strings);
}

function sp_polls_do_create_poll_form($out) {
	global $tab;

	$polls = SP()->options->get('polls');

    # bail if this is a post and can only add polls on new topics
    if (!empty(SP()->forum->view->thisPost) && $polls['topiccreate']) return $out;

    # make sure polls are enabled in this forum and user can create a poll
    $thisPage = (!empty(SP()->forum->view->thisPost)) ? SP()->forum->view->thisTopic : SP()->forum->view->thisForum;
    if (empty($thisPage->polls) || !$thisPage->polls || !SP()->auths->get('create_poll', $thisPage->forum_id)) return $out;

    $toolTip = esc_attr(__('Add A Poll', 'sp-polls'));
	$site = wp_nonce_url(SPAJAXURL.'polls-create&amp;fid='.$thisPage->forum_id, 'polls-create');

	if (SP()->core->forumData['display']['editor']['toolbar']) {
    	if ((SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive'))) {

			# display mobile icon
			$out.= "<button type='button' tabindex='".$tab++."' style='background:transparent;' class='spIcon spPollsOpenDialog' name='spPolls' id='spPolls' data-site='$site' data-label='$toolTip' data-width='600' data-height='0' data-align='center'>\n";
			$out.= SP()->theme->paint_icon('spIcon', POLLSIMAGESMOB, "sp_PollToolbar.png", '');
			$out.= "</button>";

		} else {
			$out.= "<input type='button' tabindex='".$tab++."' class='spSubmit spLeft spPollsOpenDialog' title='$toolTip' id='spPollsButton' value='".__('Poll', 'sp-polls')."' data-site='$site' data-label='$toolTip' data-width='600' data-height='0' data-align='center' />";
		}
	} else {
		$out.= "<div class='spEditorTitle sp_poll_create'><a rel='nofollow' tabindex='".$tab++."' class='spButton spPollsOpenDialog' title='$toolTip' data-site='$site' data-label='$toolTip' data-width='600' data-height='0' data-align='center'>".__('Poll', 'sp-polls')."</a></div>";
	}

    return $out;
}

function sp_polls_do_PollsShortcode($atts) {
    $pollOutput = '';

    # we must have valid forum id
    if (empty(SP()->forum->view->thisForum) && empty(SP()->forum->view->thisTopic)) return '';

    extract(shortcode_atts(array('id' => ''), $atts));

    # make sure the shortcode appears in a forum post
    if (!SP()->isForum || !isset(SP()->forum->view->topics) || !SP()->forum->view->topics->inPostLoop) {
        $pollOutput.= '<div class="spPollError">'.__('A Simple Press Poll can only appear in a forum post', 'sp-polls').'</div>';
        return $pollOutput;
    }

    # verify we have a poll and grab the data
    if (empty($id)) return '<div class="spPollError">'.__('Poll ID missing', 'sp-polls').'</div>';

	$poll = SP()->DB->table(SPPOLLS, "poll_id=$id", 'row');
    if (empty($poll)) return '<div class="spPollError">'.__('Poll ID invalid', 'sp-polls').'</div>';

    # should we show poll voting or results?
    $userVotes = sp_polls_user_voted($poll);
    $fid = (empty(SP()->forum->view->thisForum)) ? SP()->forum->view->thisTopic->forum_id : SP()->forum->view->thisForum->forum_id;
    if ($poll->poll_active) {
        # check if time to expire the poll
        $time = date("Y-m-d H:i:s", time());
        if ($time > $poll->poll_expiration) {
            SP()->DB->execute('UPDATE '.SPPOLLS.' SET poll_active=0 WHERE poll_id='.$poll->poll_id);
            $poll->poll_active = 0;
            $pollOutput.= sp_polls_show_result($poll, true, $userVotes, $fid);
        } else {
            if ($userVotes || !SP()->auths->get('vote_poll', $fid)) {
                $pollOutput.= sp_polls_show_result($poll, true, $userVotes, $fid);
            } else {
                $pollOutput.= sp_polls_show_vote($poll, true);
            }
        }
    } else {
        $pollOutput.= sp_polls_show_result($poll, true, $userVotes, $fid);
    }

    return $pollOutput;
}

function sp_polls_user_voted($poll) {
    $polls = SP()->options->get('polls');

	switch ($polls['track']) {
		case 1: # none
			$userVotes = 0;
			break;

		case 2: # cookie
			$userVotes = sp_polls_check_cookie_vote($poll);
			break;

		case 3: # IP
			$userVotes = sp_polls_check_ip_vote($poll);
			break;

		case 4: # cookie and IP
			$checkCookie = sp_polls_check_cookie_vote($poll);
			if (!empty($checkCookie)) {
				$userVotes = $checkCookie;
			} else {
				$userVotes = sp_polls_check_ip_vote($poll);
			}
			break;

		case 5: # User ID
			$userVotes = sp_polls_check_user_vote($poll);
			break;
	}
    return $userVotes;
}

function sp_polls_check_cookie_vote($poll) {
    $userVotes = 0;
	if (!empty($_COOKIE["sp_voted_poll_$poll->poll_id"])) $userVotes = explode(',', $_COOKIE["sp_voted_poll_$poll->poll_id"]);
	return $userVotes;
}

function sp_polls_check_ip_vote($poll) {
    $polls_options = SP()->options->get('polls');
	$userVotes = SP()->DB->select('SELECT answer_id FROM '.SPPOLLSVOTERS." WHERE poll_id = $poll->poll_id AND user_ip = '".sp_polls_get_ip()."'", 'col');
    if (!$userVotes) $userVotes = 0;
	return $userVotes;
}

function sp_polls_check_user_vote($poll) {
	if (SP()->user->thisUser->guest) return 0;
	$userVotes = SP()->DB->select('SELECT answer_id FROM '.SPPOLLSVOTERS." WHERE poll_id = $poll->poll_id AND user_id = ".SP()->user->thisUser->ID, 'col');
    if (!$userVotes) $userVotes = 0;
	return $userVotes;
}

function sp_polls_show_vote($poll, $container=false) {
    $fid = (empty(SP()->forum->view->thisForum)) ? SP()->forum->view->thisTopic->forum_id : SP()->forum->view->thisForum->forum_id;

    $out = '';

   # determine answer sort order from options
    $polls_options = SP()->options->get('polls');
    $sort = ($polls_options['answersort'] == 1) ? ' ASC' : ' DESC';
    if ($polls_options['answercriteria'] == 1) {
        $order = 'answer_id'.$sort;
    } else if ($polls_options['answercriteria'] == 2) {
        $order = 'answer'.$sort;
    } else {
        $order = 'rand()';
    }

    # get all the possible answers for poll
    $answers = SP()->DB->table(SPPOLLSANSWERS, "poll_id = $poll->poll_id", '', "$order");
    if (empty($answers)) return $out;

   	$img = SPCOMMONIMAGES.'working.gif';
?>
    <script>
		(function(spj, $, undefined) {
			$(document).ready(function() {
				var options = {
					target:         '#sp-poll-<?php echo $poll->poll_id; ?>',
					data:           { image: '<?php echo SPCOMMONIMAGES."working.gif"; ?>',
									  maxAnswers: '<?php echo $poll->poll_maxanswers; ?>' },
					beforeSubmit:   spj.pollValidate,
				};
				$("#sp-poll-form-<?php echo $poll->poll_id; ?>").ajaxForm(options);
			});
		}(window.spj = window.spj || {}, jQuery));
    </script>
<?php
    # display the poll
    if ($container) $out.= '<div id="sp-poll-'.$poll->poll_id.'" class="sp-poll">';
    $out.= '<p class="sp-poll-title">'.SP()->displayFilters->title($poll->poll_question).'</p>';
    $out.= '<div id="sp-poll-answers-'.$poll->poll_id.'" class="sp-poll-answers">';
    $url = wp_nonce_url(SPAJAXURL.'polls-manage&amp;targetaction=vote-poll&amp;fid='.$fid, 'polls-manage');
    $out.= '<form method="post" action="'.$url.'" id="sp-poll-form-'.$poll->poll_id.'" class="sp-poll-form">';
    $out.= '<input type="hidden" value="'.$poll->poll_id.'" name="poll-id" />';
    $out.= '<ul id="sp-poll-block-'.$poll->poll_id.'" class="sp-poll-block">';
    foreach ($answers as $answer) {
        $out.= '<li id="sp-poll-answer-'.$answer->answer_id.'" class="sp-poll-answer">';
        if ($poll->poll_maxanswers == 1) {
            $out.= '<input class="spControl" type="radio" value="'.$answer->answer_id.'" id="sfpollanswer-'.$answer->answer_id.'" name="sp-poll-answer" />';
            $out.= '<label class="sp-poll-label" for="sfpollanswer-'.$answer->answer_id.'">';
            $out.= SP()->displayFilters->title($answer->answer);
            $out.= "</label>\n";
        } else {
			$out.= "<input type='checkbox' class='spControl' name='sp-poll-answer[]' value='$answer->answer_id' id='sfpollanswer-$answer->answer_id' />\n";
 			$out.= "<label class='spLabel spCheckbox' for='sfpollanswer-$answer->answer_id'>";
            $out.= SP()->displayFilters->title($answer->answer);
            $out.= '</label>';
        }
        $out.= '</li>';
    }
    $out.= '</ul>';
    $out.= '<p class="sp-poll-submit"><input id="sfsave" class="spSubmit" type="submit" value="'.esc_attr(__('Vote', 'sp-polls')).'" name="votepoll">';
    if ($poll->poll_maxanswers > 1) $out.= '<br />'.sprintf(__('You may vote for up to %d answers', 'sp-polls'), $poll->poll_maxanswers);
    $out.= '</p>';
    if (!$poll->hide_results) {
        $url = wp_nonce_url(SPAJAXURL.'polls-manage&amp;targetaction=results-poll&amp;pid='.$poll->poll_id.'&amp;fid='.$fid, 'polls-manage');
        $out.= '<p class="sp-poll-show"><a class="sp-poll-show spPollsShowPoll" rel="nofollow" data-url="'.$url.'" data-target="sp-poll-'.$poll->poll_id.'" data-img="'.$img.'">'.esc_attr(__('View Poll Results', 'sp-polls')).'</a></p>';
    }
    $out.= '</form>';
    $out.= '</div>';
    if ($container) $out.= '</div>';
    return $out;
}

function sp_polls_show_result($poll, $container=false, $userVotes='', $fid) {
    $out = '';

	$userVotes = (array) $userVotes;
    $polls_options = SP()->options->get('polls');

    if ($container) $out.= '<div id="sp-poll-'.$poll->poll_id.'" class="sp-poll">';

    if ($poll->hide_results && $poll->poll_active) {
        $out.= '<p class="sp-poll-title">'.SP()->displayFilters->title($poll->poll_question).'</p>';
        $out.= '<div id="sp-poll-results-'.$poll->poll_id.'" class="sp-poll-results">';
        $out.= '<ul id="sp-poll-block-'.$poll->poll_id.'" class="sp-poll-block">';
        $out.= '<li class="sp-poll-hide">'.$polls_options['hide-message'].'</li>';
        $out.= '</ul>';
        $out.= '</div>';
    } else {
        # determine results sort order from options
        $polls_options = SP()->options->get('polls');
        $sort = ($polls_options['resultsort'] == 1) ? ' ASC' : ' DESC';
        if ($polls_options['resultcriteria'] == 1) {
            $order = 'answer_id'.$sort;
        } else if ($polls_options['resultcriteria'] == 2) {
            $order = 'answer'.$sort;
        } else if ($polls_options['resultcriteria'] == 3) {
            $order = 'answer_votes'.$sort;
        } else {
            $order = 'rand()';
        }

        # get all the possible answers for poll
        $answers = SP()->DB->table(SPPOLLSANSWERS, "poll_id = $poll->poll_id", '', "$order");
        if (empty($answers)) return $out;

        # display the poll
        $out.= '<p class="sp-poll-title">'.SP()->displayFilters->title($poll->poll_question).'</p>';
        $out.= '<div id="sp-poll-results-'.$poll->poll_id.'" class="sp-poll-results">';
        $out.= '<ul id="sp-poll-block-'.$poll->poll_id.'" class="sp-poll-block">';
        foreach ($answers as $answer) {
            $user_class = (in_array($answer->answer_id, $userVotes)) ? 'class="sp-poll-result sp-user-voted"' : 'class="sp-poll-result"';
            $out.= '<li id="sp-poll-result-'.$answer->answer_id.'" '.$user_class.'>';
            $out.= SP()->displayFilters->title($answer->answer);
            $out.= '<span class="sp-poll-meta">';
            $percent = 0;
            $bar_width = 1;
            if ($poll->poll_voters > 0) {
                if ($answer->answer_votes > 0) {
                    $percent = round((($answer->answer_votes / $poll->poll_voters) * 100));
                    $percent = apply_filters('sph_polls_vote_percent', $percent, $poll, $answer);
                    $bar_width = $percent;
                }
            }
            $vote = ($answer->answer_votes == 0 || $answer->answer_votes > 1) ? __('votes', 'sp-polls') : __('vote', 'sp-polls');
            $out.= "($percent% : $answer->answer_votes $vote)";
            $out.= '</span>';
            $out.= '<div class="sp-poll-bar" style="width: '.$bar_width.'%; background-color: #'.$polls_options['bar-background'].'; border: 1px solid #'.$polls_options['bar-border'].'; height: '.$polls_options['bar-height'].'px;">';
            $out.= '</div>';
            $out.= '</li>';
        }
        $out.= '</ul>';

        $out.= '<div class="sp-poll-count">';
        $out.= __('Total Voters', 'sp-polls').': '.$poll->poll_voters;
        $out.= '</div>';

        # should we show poll voting or results?
        if ($poll->poll_active && empty($userVotes[0])) {
            if (SP()->auths->get('vote_poll', $fid)) {
               	$img = SPCOMMONIMAGES.'working.gif';
                $url = wp_nonce_url(SPAJAXURL.'polls-manage&amp;targetaction=show-poll&amp;pid='.$poll->poll_id, 'polls-manage');
                $out.= '<p class="sp-poll-show"><a class="sp-poll-show spPollsShowPoll" rel="nofollow" data-url="'.$url.'" data-target="sp-poll-'.$poll->poll_id.'" data-img="'.$img.'">'.esc_attr(__('Vote in Poll', 'sp-polls')).'</a></p>';
            }
        }
        $out.= '</div>';
    }

    if ($container) $out.= '</div>';
    return $out;
}

function sp_polls_get_ip() {
	if (empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		$ip = htmlspecialchars($_SERVER['REMOTE_ADDR'], ENT_QUOTES, 'UTF-8');
	} else {
		$ip = htmlspecialchars($_SERVER['HTTP_X_FORWARDED_FOR'], ENT_QUOTES, 'UTF-8');
	}

	if (strpos($ip, ',') !== false) {
		$ip = explode(',', $ip);
		$ip = $ip[0];
	}

    $ip = SP()->filters->str($ip);
	return $ip;
}

function sp_polls_do_admin_save_poll() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

    if (!SP()->auths->current_user_can('SPF Manage Polls') && !empty($poll_id)) return;

    if (empty($_POST['sp-poll-answer']) || empty($_POST['sp-poll-answer-count'])) return __('A poll must have answers', 'sp-polls');

    $pid = SP()->filters->integer($_POST['sp-poll-id']);

    # save the questions
    $totalVotes = 0;
    foreach ($_POST['sp-poll-answer'] as $answer_id => $answer) {
        $answer = SP()->filters->str($answer);
        if (!empty($answer)) {
            $votes = SP()->filters->integer($_POST['sp-poll-answer-votes'][$answer_id]);
            if ($votes < 0) $votes = 0;
            $totalVotes = $totalVotes + $votes;

            # is it edit of existing answer or a new one?
            if ($answer_id < 0) {
                SP()->DB->execute('INSERT INTO '.SPPOLLSANSWERS." (poll_id, answer, answer_votes) VALUES ($pid, '$answer', $votes)"); # new answer
            } else {
                SP()->DB->execute('UPDATE '.SPPOLLSANSWERS." SET
                            answer='$answer',
                            answer_votes=$votes
                            WHERE poll_id = $pid AND answer_id = $answer_id");
            }
        }
    }

    # check data
    $question = SP()->saveFilters->title($_POST['sp-poll-question']);
    if (empty($question)) return __('A poll must have a question', 'sp-polls');

    $user = SP()->filters->integer($_POST['sp-poll-user']);
    if (empty($user)) return __('A poll must have a user as creator', 'sp-polls');

    $max = SP()->filters->integer($_POST['sp-poll-max']);
    if ($max < 1) $max = 1;
    $date = SP()->filters->str($_POST['sp-poll-date']);
    $expire = SP()->filters->str($_POST['sp-poll-expiration']);
    $active = isset($_POST['sp-poll-active']) ? 1 : 0;
    $voters = SP()->filters->integer($_POST['sp-poll-voters']);
    if ($voters < 0) $voters = 0;

    # are we hiding results?
    $polls = SP()->options->get('polls');
    $hide = ($polls['hide-results'] && !empty($_POST['sp-poll-hide'])) ? 1 : 0;

    # save poll data
    SP()->DB->execute('UPDATE '.SPPOLLS." SET
                poll_question='$question',
                poll_maxanswers=$max,
                poll_date='$date',
                poll_expiration='$expire',
                user_id=$user,
                poll_active=$active,
                poll_votes=$totalVotes,
                poll_voters=$voters,
                hide_results=$hide
                WHERE poll_id = $pid");

    return __('Poll updated', 'sp-polls');
}

function sp_poll_do_admin_cap_list($user) {
	$manage_polls = user_can($user, 'SPF Manage Polls');
	echo '<li>';
	spa_render_caps_checkbox(__('Manage Polls', 'sp-polls'), "manage-polls[$user->ID]", $manage_polls, $user->ID);
	echo "<input type='hidden' name='old-polls[$user->ID]' value='$manage_polls' />";
	echo '</li>';
}

function sp_poll_do_admin_cap_form($user) {
	echo '<li>';
	spa_render_caps_checkbox(__('Manage Polls', 'sp-polls'), 'add-polls', 0);
	echo '</li>';
}

function sp_poll_do_admin_caps_update($still_admin, $remove_admin, $user) {
    $manage_polls = (isset($_POST['manage-polls'])) ? $_POST['manage-polls'] : '';
    $old_polls = (isset($_POST['old-polls'])) ? $_POST['old-polls'] : '';

    # was this admin removed?
    if (isset($remove_admin[$user->ID])) $manage_polls = '';

	if (isset($manage_polls[$user->ID])) {
		$user->add_cap('SPF Manage Polls');
	} else {
		$user->remove_cap('SPF Manage Polls');
	}
	$still_admin = $still_admin || isset($manage_polls[$user->ID]);
	return $still_admin;
}

function sp_poll_do_admin_caps_new($newadmin, $user) {
    $polls = (isset($_POST['add-polls'])) ? $_POST['add-polls'] : '';
	if ($polls == 'on') $user->add_cap('SPF Manage Polls');
	$newadmin = $newadmin || $polls == 'on';
	return $newadmin;
}
