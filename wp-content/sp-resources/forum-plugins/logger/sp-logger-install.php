<?php
/*
Simple:Press
Event Logger plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_logger_do_install() {
	$options = SP()->options->get('logger');
	if (empty($options)) {
        $options['logentries'] = 100;

        $options['postedited'] = false;
        $options['topicedited'] = false;
        $options['postdeleted'] = false;
        $options['topicdeleted'] = false;
        $options['postmoved'] = false;
        $options['topicmoved'] = false;
        $options['postapproved'] = false;
        $options['postunapproved'] = false;
        $options['postreassigned'] = false;
        $options['postcreated'] = false;

        $options['hooks'] = array();

        $options['dbversion'] = SPLOGGERDBVERSION;
        SP()->options->update('logger', $options);

        $sql = '
            CREATE TABLE IF NOT EXISTS '.SPEVENTLOG.' (
                log_id BIGINT(20) NOT NULL AUTO_INCREMENT,
                log_event VARCHAR(50) NOT NULL,
                log_date DATETIME NULL,
                log_data TEXT NULL,
                PRIMARY KEY (log_id)
            ) '.SP()->DB->charset();
		SP()->DB->execute($sql);
    }

    # add our tables to installed list
   	$tables = SP()->options->get('installed_tables');
    if ($tables) {
        if (!in_array(SPEVENTLOG, $tables)) $tables[] = SPEVENTLOG;
        SP()->options->update('installed_tables', $tables);
    }

   	global $wp_roles;
    $wp_roles->add_cap('administrator', 'SPF Manage Logger', false);

    # do we need to give activater Manage Logging capability
    if (!SP()->auths->current_user_can('SPF Manage Logger')) {
		$user = new WP_User(SP()->user->thisUser->ID);
		$user->add_cap('SPF Manage Logger');
    }

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# sp reactivated.
function sp_logger_do_sp_activate() {
	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# permissions reset
function sp_logger_do_reset_permissions() {
}
