<?php
/*
Simple:Press
search by user plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_search_by_user_do_upgrade_check() {
    if (!SP()->plugin->is_active('search-by-user/sp-search-by-user-plugin.php')) return;

    $options = SP()->options->get('search-by-user');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPSEARCHUSERDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades

    # save data
    $options['dbversion'] = SPSEARCHUSERDBVERSION;
    SP()->options->update('search-by-user', $options);
}
