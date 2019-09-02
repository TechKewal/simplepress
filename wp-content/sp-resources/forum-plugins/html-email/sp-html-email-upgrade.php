<?php
/*
Simple:Press
Name plugin install/upgrade routine
$LastChangedDate: 2017-09-03 15:32:48 -0500 (Sun, 03 Sep 2017) $
$Rev: 15536 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_html_email_do_upgrade_check() {
    if (!SP()->plugin->is_active('html-email/sp-html-email-plugin.php')) return;

    $options = SP()->options->get('html-email');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPHTMLEMAILDBVERSION ) return;

    # apply upgrades as needed
    if ($db < 1) {
        # give new manage pm cap to any admin with manage options cap since they already can manage pm
    	$admins = SP()->DB->table(SPMEMBERS, 'admin = 1');
    	if ($admins) {
    	   foreach ($admins as $admin) {
                $user = new WP_User($admin->user_id);
                if (user_can($user, 'SPF Manage Options')) {
                    $user->add_cap('SPF Manage Emails');
                }
            }
        }
    }

    # db version upgrades
    if ($db < 2) {
       	global $wp_roles;
        $wp_roles->add_cap('administrator', 'SPF Manage Emails', false);
    }

    if ($db < 3) {
        $options['new-user-body'] = str_replace('<br />Username: %PASSWORD%', '', $options['new-user-body']);
        $options['new-user-body'] = str_replace('<br />Password: %PASSWORD%', '', $options['new-user-body']);
        $options['new-user-body'] = str_replace('%PASSWORD%', '', $options['new-user-body']);
        $options['new-user-body'].= '<p>You may retrieve your password here: %PWURL%</p>';
    }

    if ($db < 4) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

	# add moderation notification text
	if ($db < 5) {
		$options['admin-notification-modtext'] = 'This post is Awaiting Moderation';
	}

    # save data
    $options['dbversion'] = SPHTMLEMAILDBVERSION;
    SP()->options->update('html-email', $options);
}
