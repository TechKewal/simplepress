<?php
/*
Simple:Press
Polls plugin ajax routine for creation functions
$LastChangedDate: 2018-10-17 15:17:49 -0500 (Wed, 17 Oct 2018) $
$Rev: 15756 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

if (!sp_nonce('polls-manage')) die();

if (!isset($_GET['targetaction'])) die();
$action = SP()->filters->str($_GET['targetaction']);

if ($action == 'create-poll') {
    $response = -1; # default to fail
    $qtest = implode('', $_POST['sp-poll-answer']);
    $fid = SP()->filters->integer($_POST['sp-poll-forum']);
    if (!empty($_POST['sp-poll-question']) && !empty($qtest) && !empty($fid) && SP()->auths->get('create_poll', $fid)) {
        # bit of error checking on max answers
        $max = SP()->filters->integer($_POST['sp-poll-maxq']);
        if ($max < 1) $max = 1;

        # create the main poll entry
        if (!empty($_POST['sp-poll-date'])) {
            $expire = date("Y-m-d H:i:s", strtotime(SP()->filters->str($_POST['sp-poll-date'])));
        } else {
            $newDate = new DateTime;
            $newDate->modify('+25 month');
            $expire = $newDate->format("Y-m-d H:i:s");
        }

        # are we hiding results?
        $polls = SP()->options->get('polls');
        $hide = ($polls['hide-results'] && isset($_POST['sp-poll-hide'])) ? 1 : 0;

        $result = SP()->DB->execute('INSERT INTO '.SPPOLLS."
                    (poll_question, poll_maxanswers, poll_date, poll_expiration, user_id, poll_active, poll_votes, poll_voters, hide_results) VALUES
                    ('".SP()->saveFilters->title($_POST['sp-poll-question'])."', $max, '".current_time('mysql')."', '$expire', ".SP()->user->thisUser->ID.", 1, 0, 0, $hide)");
        if (!$result) {
            echo -1;
            die();
        }
        $response = SP()->rewrites->pageData['insertid'];

        # now save the questions
        foreach ($_POST['sp-poll-answer'] as $answer) {
            $answer = SP()->saveFilters->title(SP()->filters->str($answer));
            if (!empty($answer)) SP()->DB->execute('INSERT INTO '.SPPOLLSANSWERS." (poll_id, answer, answer_votes) VALUES ($response, '$answer', 0)");
        }
    }

    if ($response != -1) do_action('sph_poll_created', $response, SP()->user->thisUser->ID);

    # return the response
    echo $response;
    die();
}

if ($action == 'vote-poll') {
    # make sure we have a poll and answer
    $poll_id = SP()->filters->integer($_POST['poll-id']);
    if (empty($poll_id)) {
        echo __('Sorry, the poll data was corrupted', 'sp-polls');
        die();
    }

    $fid = SP()->filters->str($_GET['fid']);
    if (!SP()->auths->get('vote_poll', $fid)) die();

    $answer = (array) $_POST['sp-poll-answer'];

    # grab the poll data
   	$poll = SP()->DB->table(SPPOLLS, "poll_id=$poll_id", 'row');

    # double check the poll is active
    if (!$poll->poll_active) {
        echo __('Sorry, this poll is not active', 'sp-polls');
        die();
    }

    # double check that user has not voted
    if (!sp_polls_user_voted($poll)) {
        # get some voter info
        $polls_options = SP()->options->get('polls');
        $ip = sp_polls_get_ip();
        $userid = (empty(SP()->user->thisUser->ID)) ? 0 : SP()->user->thisUser->ID;

        # process the vote
        for ($vote=0; $vote < $poll->poll_maxanswers; $vote++) {
        	SP()->DB->execute('UPDATE '.SPPOLLSANSWERS." SET answer_votes = (answer_votes + 1) WHERE poll_id = $poll_id AND answer_id = {$answer[$vote]}");
        	SP()->DB->execute('UPDATE '.SPPOLLS." SET poll_votes = (poll_votes + 1) WHERE poll_id = $poll_id");
            SP()->DB->execute('INSERT INTO '.SPPOLLSVOTERS." (poll_id, answer_id, vote_date, user_id, user_ip) VALUES ($poll_id, {$answer[$vote]}, '".current_time('mysql')."', $userid, '$ip')");
        }
       	SP()->DB->execute('UPDATE '.SPPOLLS." SET poll_voters = (poll_voters + 1) WHERE poll_id = $poll_id");

        # are we logging by cookie?
        if ($polls_options['track'] == 2 || $polls_options['track'] == 4) {
			$cookie_expire = ($polls_options['cookie-expire'] == 0) ? 31556926 : $polls_options['cookie-expire'];
            $timestamp = current_time('timestamp') + $cookie_expire;
			$cookie = setcookie("sp_voted_poll_$poll_id", implode(',', $answer), $timestamp, COOKIEPATH); # 1 year expiration
        }

        # show the poll results
       	$poll = SP()->DB->table(SPPOLLS, "poll_id=$poll_id", 'row'); # need to refresh the poll data
        echo sp_polls_show_result($poll, false, $answer, $fid);

        do_action('sph_poll_voted', $poll_id, SP()->user->thisUser->ID, $poll->user_id);
    } else {
        echo __('Sorry, you have already voted in this poll', 'sp-polls');
    }

    die();
}

if ($action == 'results-poll') {
    $poll_id = SP()->filters->integer($_GET['pid']);
    if (!empty($poll_id)) {
        $fid = SP()->filters->integer($_POST['fid']);

        # grab the poll data
       	$poll = SP()->DB->table(SPPOLLS, "poll_id=$poll_id", 'row');

        # show the poll results
        $user_votes = sp_polls_user_voted($poll);

        echo sp_polls_show_result($poll, false, $user_votes, $fid);
    } else {
        echo __('Sorry, there was a problem retrieving the poll information', 'sp-polls');
    }

    die();
}

if ($action == 'show-poll') {
    $poll_id = SP()->filters->integer($_GET['pid']);
    if (!empty($poll_id)) {
        # grab the poll data
       	$poll = SP()->DB->table(SPPOLLS, "poll_id=$poll_id", 'row');

        # show the poll results
        echo sp_polls_show_vote($poll);
    } else {
        echo __('Sorry, there was a problem retrieving the poll information', 'sp-polls');
    }

    die();
}

if ($action == 'delete-poll') {
    $poll_id = SP()->filters->integer($_GET['pid']);
    if (SP()->auths->current_user_can('SPF Manage Polls') && !empty($poll_id)) {
        SP()->DB->execute('DELETE FROM '.SPPOLLS." WHERE poll_id=$poll_id");
        SP()->DB->execute('DELETE FROM '.SPPOLLSANSWERS." WHERE poll_id=$poll_id");
        SP()->DB->execute('DELETE FROM '.SPPOLLSVOTERS." WHERE poll_id=$poll_id");
    }

    die();
}

die();
