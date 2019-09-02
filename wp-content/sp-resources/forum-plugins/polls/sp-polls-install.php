<?php
/*
Simple:Press
Polls plugin install routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_polls_do_install() {
	$polls = SP()->options->get('polls');
	if (empty($polls)) {
		$sql = "
			CREATE TABLE IF NOT EXISTS ".SPPOLLS." (
				poll_id bigint(20) NOT NULL auto_increment,
				poll_question varchar(255) NOT NULL,
				poll_maxanswers tinyint(4) default 1,
				poll_date datetime NOT NULL,
				poll_expiration datetime NOT NULL,
				user_id bigint(20) NOT NULL,
				poll_active tinyint(1) default 1,
				poll_votes int(16) default 0,
				poll_voters int(16) default 0,
				hide_results tinyint(1) default 0,
				PRIMARY KEY (poll_id),
				KEY user_id_idx (user_id),
				KEY poll_active_idx (poll_active)
			) ".SP()->DB->charset().";";
		SP()->DB->execute($sql);

		$sql = "
			CREATE TABLE IF NOT EXISTS ".SPPOLLSANSWERS." (
				answer_id bigint(20) NOT NULL auto_increment,
				poll_id bigint(20) NOT NULL,
				answer varchar(255) NOT NULL,
				answer_votes int(16) default 0,
				PRIMARY KEY (answer_id),
				KEY poll_id_idx (poll_id)
			) ".SP()->DB->charset().";";
		SP()->DB->execute($sql);

		$sql = "
			CREATE TABLE IF NOT EXISTS ".SPPOLLSVOTERS." (
				vote_id bigint(20) NOT NULL auto_increment,
				poll_id bigint(20) NOT NULL,
				answer_id bigint(20) NOT NULL,
				vote_date datetime NOT NULL,
				user_id bigint(20) NOT NULL,
				user_ip varchar(39) NOT NULL,
				PRIMARY KEY (vote_id)
			) ".SP()->DB->charset().";";
		SP()->DB->execute($sql);

        # need new columns
        SP()->DB->execute('ALTER TABLE '.SPFORUMS.' ADD (polls smallint(1) NOT NULL default 0)');
        SP()->DB->execute('ALTER TABLE '.SPPOSTS.' ADD (poll smallint(1) NOT NULL default 0)');

        $polls['topiccreate'] = true;
        $polls['track'] = 4; # cookie and IP
        $polls['poll-expire'] = 0; # cookie expiry
        $polls['cookie-expire'] = 0; # cookie expiry
    	$polls['bar-background'] = '7ca6d8';
    	$polls['bar-border'] = '003562';
        $polls['bar-height'] = '10';
    	$polls['answercriteria'] = 1; # order entry
    	$polls['answersort'] = 1; # ascending
    	$polls['resultcriteria'] = 3; # votes
    	$polls['resultsort'] = 2; # descending
    	$polls['hide-results'] = false;
        $polls['hide-message'] = __('Thank you for voting. Poll results will be available when the poll has closed.', 'sp-polls');

        $polls['dbversion'] = SPPOLLSDBVERSION;
		SP()->options->add('polls', $polls);
    }

    # add our tables to installed list
   	$tables = SP()->options->get('installed_tables');
    if ($tables) {
        if (!in_array(SPPOLLS, $tables)) $tables[] = SPPOLLS;
        if (!in_array(SPPOLLSANSWERS, $tables)) $tables[] = SPPOLLSANSWERS;
        if (!in_array(SPPOLLSVOTERS, $tables)) $tables[] = SPPOLLSVOTERS;
        SP()->options->update('installed_tables', $tables);
    }

   	global $wp_roles;
    $wp_roles->add_cap('administrator', 'SPF Manage Polls', false);

    # do we need to give activater Manage Polls capability
    if (!SP()->auths->current_user_can('SPF Manage Polls')) {
		$user = new WP_User(SP()->user->thisUser->ID);
		$user->add_cap('SPF Manage Polls');
    }

    # add a new permission into the auths table
	SP()->auths->add('create_poll', __('Can create a forum poll within a post', 'sp-polls'), 1, 0, 0, 0, 3);
	SP()->auths->add('vote_poll', __('Can vote in a forum poll within a post', 'sp-polls'), 1, 0, 0, 0, 1);

    # activation so make our auth active
    SP()->auths->activate('create_poll');
    SP()->auths->activate('vote_poll');

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# sp reactivated.
function sp_polls_do_sp_activate() {
}

function sp_polls_do_reset_permissions() {
	SP()->auths->add('create_poll', __('Can create a forum poll within a post', 'sp-polls'), 1, 0, 0, 0, 3);
	SP()->auths->add('vote_poll', __('Can vote in a forum poll within a post', 'sp-polls'), 1, 0, 0, 0, 1);
}
