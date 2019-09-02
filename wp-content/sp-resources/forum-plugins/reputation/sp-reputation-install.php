<?php
/*
Simple:Press
Reputation System plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_reputation_do_install() {
	SP()->activity->add_type('reputation');

	$options = SP()->options->get('reputation');
	if (empty($options)) {
        sp_reputation_setup_options();

        # add reputation column to sfmembers
    	SP()->DB->execute('ALTER TABLE '.SPMEMBERS.' ADD (reputation int(4) NOT NULL default "0")');

		# create reputation badge storage location
		$newpath = SP()->plugin->add_storage('forum-reputation', 'reputation');

        # copy over default reputation badges
        @copy(SPREPIMAGESDIR.'reputation_level_0.png', $newpath.'/reputation_level_0.png');
        @copy(SPREPIMAGESDIR.'reputation_level_1.png', $newpath.'/reputation_level_1.png');
        @copy(SPREPIMAGESDIR.'reputation_level_2.png', $newpath.'/reputation_level_2.png');
        @copy(SPREPIMAGESDIR.'reputation_level_3.png', $newpath.'/reputation_level_3.png');
        @copy(SPREPIMAGESDIR.'reputation_level_4.png', $newpath.'/reputation_level_4.png');
        @copy(SPREPIMAGESDIR.'reputation_level_5.png', $newpath.'/reputation_level_5.png');
        @copy(SPREPIMAGESDIR.'reputation_level_6.png', $newpath.'/reputation_level_6.png');
        @copy(SPREPIMAGESDIR.'reputation_level_7.png', $newpath.'/reputation_level_7.png');
        @copy(SPREPIMAGESDIR.'reputation_level_8.png', $newpath.'/reputation_level_8.png');
        @copy(SPREPIMAGESDIR.'reputation_level_9.png', $newpath.'/reputation_level_9.png');
        @copy(SPREPIMAGESDIR.'reputation_level_10.png', $newpath.'/reputation_level_10.png');
    }

   	global $wp_roles;
    $wp_roles->add_cap('administrator', 'SPF Manage Reputation', false);

    # do we need to give activater Manage Reputation capability
    if (!SP()->auths->current_user_can('SPF Manage Reputation')) {
		$user = new WP_User(SP()->user->thisUser->ID);
		$user->add_cap('SPF Manage Reputation');
    }

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';

	SP()->auths->add('use_reputation', __('Can use reputation to rate other users', 'sp-reputation'), 1, 1, 0, 0, 2);
	SP()->auths->add('get_reputation', __('Can get reputation from other users', 'sp-reputation'), 1, 1, 0, 0, 2);
}

# sp reactivated.
function sp_reputation_do_sp_activate() {
}

# permissions reset
function sp_reputation_do_reset_permissions() {
	SP()->auths->add('use_reputation', __('Can use reputation to rate other users', 'sp-reputation'), 1, 1, 0, 0, 2);
	SP()->auths->add('get_reputation', __('Can get reputation from other users', 'sp-reputation'), 1, 1, 0, 0, 2);
}

function sp_reputation_setup_options() {
    $options = array();

    $options['highlight'] = false;
    $options['highlightcss'] = 'f8f3ee';
    $options['highlightrep'] = 1000;

    $options['lowlight'] = false;
    $options['lowlightcss'] = 'cccccc';
    $options['lowlightrep'] = -200;

    $options['regrep'] = 365;
    $options['postrep'] = 50;
    $options['defrep'] = 0;

    $options['popupheader'] = 'Adjust Reputation of this User:';
    $options['popupgive'] = 'Give Reputation';
    $options['popuptake'] = 'Take Reputation';
    $options['popupamount'] = 'Amount:';
    $options['popupsubmit'] = 'Give/Take Reputation';
    $options['popupinvalid'] = 'You have some invalid input';
    $options['popupzero'] = 'You cannot give/take 0 reputation';
    $options['popuppositive'] = 'Amount of reputation must be positive';
    $options['popupmax'] = 'You cannot give/take more reputation than your max amount';
    $options['popupupdated'] = 'User reputation updated';
    $options['popupwrong'] = 'Something went wrong';

    $options['dbversion'] = SPREPDBVERSION;
    SP()->options->update('reputation', $options);

    for ($x=0; $x<=10; $x++) {
        $leveldata = array();
    	$leveldata['points'] = $x * 250;
    	$leveldata['maxgive'] = $x * 5;
    	$leveldata['maxday'] = $leveldata['maxgive'] * 3;
    	$leveldata['badge'] = "reputation_level_$x.png";
        if ($x == 0) {
            $leveldata['points'] = 100;
        	$leveldata['maxgive'] = 0;
        	$leveldata['maxday'] = 0;
        }
        if ($x == 10) $leveldata['points'] = 999999;
    	SP()->meta->add('reputation level', "Level $x", $leveldata);
    }
}
