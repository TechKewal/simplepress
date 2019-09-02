<?php
/*
Simple:Press
Uploads Viewer plugin install/upgrade routine
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_uploads_viewer_do_upgrade_check() {
    if (!SP()->plugin->is_active('uploads_viewer/sp-uploads-viewer-plugin.php')) return;

    $uv = SP()->options->get('uploads-viewer');

    $db = $uv['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPUVDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades

    # save data
    $uv['dbversion'] = SPUVDBVERSION;
    SP()->options->update('uploads-viewer', $uv);
}
