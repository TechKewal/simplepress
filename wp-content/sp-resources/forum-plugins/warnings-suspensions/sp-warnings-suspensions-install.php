<?php
/*
Simple:Press
Name plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_warnings_suspensions_do_install() {
	$options = SP()->options->get('warnings-suspensions');
	if (empty($options)) {
        $options['profile'] = true;
        $options['notify'] = 2; # use SP notifications

        $options['warn_title'] =  __('You have been warned', 'sp-warnings-suspensions');
        $options['warn_message'] = __('An administrator has issued a warning to you.  The warning will expire on %s.', 'sp-warnings-suspensions');
        $options['warn_profile'] = __('You have a current warning. It expires on %s', 'sp-warnings-suspensions');
        $options['suspension_title'] = __('You have been suspended', 'sp-warnings-suspensions');
        $options['suspension_message'] = __('An administrator has issued a suspension to you.  The suspension will expire on %s.', 'sp-warnings-suspensions');
        $options['suspension_profile'] = __('You have a current suspension. It expires on %s', 'sp-warnings-suspensions');
        $options['ban_title'] =  __('You have been banned', 'sp-warnings-suspensions');
        $options['ban_message'] = __('An administrator has issued a ban to you.', 'sp-warnings-suspensions');
        $options['ban_profile'] = __('You have a current ban.', 'sp-warnings-suspensions');

        $options['dbversion'] = SPWARNDBVERSION;
        SP()->options->update('warnings-suspensions', $options);

        $sql = '
            CREATE TABLE IF NOT EXISTS '.SPWARNINGS.' (
                warn_id BIGINT(20) NOT NULL AUTO_INCREMENT,
                warn_type INT NULL,
                user_id BIGINT(20) NULL,
				display_name varchar(100) default NULL,
				expiration datetime NOT NULL,
                usergroup varchar(100) NULL,
                saved_memberships longtext NULL,
                PRIMARY KEY (warn_id),
				KEY user_id_idx (user_id),
				KEY warn_type_idx (warn_type)
            ) '.SP()->DB->charset();
		SP()->DB->execute($sql);
    }

    # add our tables to installed list
   	$tables = SP()->options->get('installed_tables');
    if ($tables && !in_array(SPWARNINGS, $tables)) {
        $tables[] = SPWARNINGS;
        SP()->options->update('installed_tables', $tables);
    }

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';

   	global $wp_roles;
    $wp_roles->add_cap('administrator', 'SPF Manage Warnings', false);

    # do we need to give activater Manage Tags capability
    if (!SP()->auths->current_user_can('SPF Manage Warnings')) {
		$user = new WP_User(SP()->user->thisUser->ID);
		$user->add_cap('SPF Manage Warnings');
    }
}

# sp reactivated.
function sp_warnings_suspensions_do_sp_activate() {
}

# permissions reset
function sp_warnings_suspensions_do_reset_permissions() {
}
