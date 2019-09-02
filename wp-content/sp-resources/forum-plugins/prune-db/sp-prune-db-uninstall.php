<?php
/*
Simple:Press
Report Post plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the report post plugin uninstall only
function sp_prune_db_do_uninstall() {
    # delete our option table
    SP()->options->delete('prune-db');
}

function sp_prune_db_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'prune-db/sp-prune-db-plugin.php') {
        $url = SPADMINUSER.'&amp;tab=plugin&amp;admin=sp_prune_db_admin_filter&amp;save=sp_prune_db_admin_select&amp;form=0&amp;id=spprune';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-prune')."'>".__('Options', 'sp-prune').'</a>';
    }
	return $actionlink;
}
