<?php
/*
Simple:Press
Polls plugin install/upgrade routine
$LastChangedDate: 2017-09-03 15:32:48 -0500 (Sun, 03 Sep 2017) $
$Rev: 15536 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_polls_do_upgrade_check() {
    if (!SP()->plugin->is_active('polls/sp-polls-plugin.php')) return;

    $polls = SP()->options->get('polls');

    $db = $polls['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPPOLLSDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades
    if ($db < 1) {
	   SP()->DB->execute('UPDATE '.SPAUTHS." SET auth_cat=3 WHERE auth_name='create_poll'");
	   SP()->DB->execute('UPDATE '.SPAUTHS." SET auth_cat=1 WHERE auth_name='vote_poll'");
    }

    if ($db < 2) {
    	SP()->auths->delete('create_poll');
    	SP()->auths->delete('vote_poll');
    	SP()->auths->add('create_poll', __('Can create a forum poll within a post', 'sp-polls'), 1, 0, 0, 0, 3);
    	SP()->auths->add('vote_poll', __('Can vote in a forum poll within a post', 'sp-polls'), 1, 0, 0, 0, 1);
    }

    if ($db < 3) {
        SP()->DB->execute('ALTER TABLE '.SPPOLLS.' ADD (hide_results tinyint(1) default 0)');
    	$polls['hide-results'] = false;
        $polls['hide-message'] = __('Thank you for voting. Poll results will be available when the poll has closed.', 'sp-polls');
    }

    if ($db < 4) {
        # give new manage polls cap to any admin with manage options cap since they already can manage pm
    	$admins = SP()->DB->table(SPMEMBERS, 'admin = 1');
    	if ($admins) {
    	   foreach ($admins as $admin) {
                $user = new WP_User($admin->user_id);
                if (user_can($user, 'SPF Manage Components')) {
                    $user->add_cap('SPF Manage Polls');
                }
            }
        }
    }

    if ($db < 5) {
       	global $wp_roles;
        $wp_roles->add_cap('administrator', 'SPF Manage Polls', false);
    }

    # db version upgrades
    if ($db < 6) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

    # save data
    $polls['dbversion'] = SPPOLLSDBVERSION;
    SP()->options->update('polls', $polls);
}
