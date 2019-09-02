<?php
/*
Simple:Press
Moderation Email plugin install/upgrade routine
$LastChangedDate: 2013-04-17 19:24:03 -0700 (Wed, 17 Apr 2013) $
$Rev: 10182 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_moderation_email_do_upgrade_check() {
    if (!SP()->plugin->is_active('moderation-email/sp-moderation-email-plugin.php')) return;

    $options = SP()->options->get('moderation-email');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPMODEMAILDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades

    # save data
    $options['dbversion'] = SPMODEMAILDBVERSION;
    SP()->options->update('moderation-email', $options);
}
