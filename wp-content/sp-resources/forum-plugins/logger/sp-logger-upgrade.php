<?php
/*
Simple:Press
Event Logger plugin install/upgrade routine
$LastChangedDate: 2017-09-03 15:32:48 -0500 (Sun, 03 Sep 2017) $
$Rev: 15536 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_logger_do_upgrade_check() {
    if (!SP()->plugin->is_active('logger/sp-logger-plugin.php')) return;

    $options = SP()->options->get('logger');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPLOGGERDBVERSION ) return;

    # apply upgrades as needed
    if ($db < 1) {
        # give new manage pm cap to any admin with manage options cap since they already can manage pm
    	$admins = SP()->DB->table(SPMEMBERS, 'admin = 1');
    	if ($admins) {
    	   foreach ($admins as $admin) {
                $user = new WP_User($admin->user_id);
                if (user_can($user, 'SPF Manage Options')) {
                    $user->add_cap('SPF Manage Logger');
                }
            }
        }
    }

    if ($db < 2) {
       	global $wp_roles;
        $wp_roles->add_cap('administrator', 'SPF Manage Logger', false);
    }

    if ($db < 3) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

    # save data
    $options['dbversion'] = SPLOGGERDBVERSION;
    SP()->options->update('logger', $options);
}
