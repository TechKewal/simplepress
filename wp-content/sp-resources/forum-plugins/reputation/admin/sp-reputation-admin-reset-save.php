<?php
/*
Simple:Press
Reputation System plugin reset Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_reputation_do_admin_save_reset() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

    if (isset($_POST['resetreputation'])) {
        # make sure at least one reset option was selected
        if (!isset($_POST['resetlevels']) && !isset($_POST['resetoptions'])) {
        	return __('Reputation NOT reset - No options selected!', 'sp-reputation');
        }

        # if reset levels was selected, set set each user reputation level to specified value
        if (isset($_POST['resetlevels'])) {
            $rep = (int) $_POST['newlevel'];
            SP()->DB->execute('UPDATE '.SPMEMBERS." SET reputation=$rep");

            # remove all user ratings
		    SP()->activity->delete('type='.SPACTIVITY_REPUTATION);
        }

        # if reset options was selected, set reputation options back to default
        if (isset($_POST['resetoptions'])) {
            # reset the levels
            SP()->meta->delete('', '', 'reputation level');

            # reset options
            require_once SPREPDIR.'sp-reputation-install.php';
            sp_reputation_setup_options();
        }

    	return __('Reputation system reset!', 'sp-reputation');
    }

    if (isset($_POST['userreputation'])) {
        if (!isset($_POST['reputation_user']) && !isset($_POST['newrep'])) {
        	return __('Reputation NOT reset - invalid inputs!', 'sp-reputation');
        }

	    $display_name = SP()->saveFilters->name($_POST['reputation_user']);
    	$userid = SP()->DB->table(SPMEMBERS, "display_name='$display_name'", 'user_id');
        if (empty($userid)) {
        	return __('Reputation NOT reset - invalid user!', 'sp-reputation');
        }

        SP()->memberData->update($userid, 'reputation', (int) $_POST['newrep']);

    	return __('User reputation set!', 'sp-reputation');
    }
}
