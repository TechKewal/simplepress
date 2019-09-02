<?php
/*
Simple:Press
Uploads Viweer plugin install/upgrade routine
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_uploads_viewer_do_install() {
	$uv = SP()->options->get('uploads-viewer');
	if (empty($uv)) {
        $uv['dbversion'] = SPUVDBVERSION;
        SP()->options->update('uploads-viewer', $uv);
    }
}

# sp reactivated.
function sp_uploads_viewer_do_sp_activate() {
}
