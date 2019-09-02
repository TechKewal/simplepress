<?php
/*
Simple:Press
Uploads Viweer plugin uninstall routine
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the uploads viewer plugin uninstall only
function sp_uploads_viewer_do_uninstall() {
    # delete our option
    SP()->options->delete('uploads-viewer');
}

function sp_uploads_viewer_do_deactivate() {
}

function sp_uploads_viewer_do_sp_deactivate() {
}

function sp_uploads_viewer_do_sp_uninstall() {
}
