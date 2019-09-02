<?php
/*
Simple:Press
Threading plugin uninstall routine
$LastChangedDate: 2013-02-17 20:50:25 +0000 (Sun, 17 Feb 2013) $
$Rev: 9859 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the topic description plugin uninstall only
function sp_threading_do_uninstall() {
	# delete our option
	SP()->options->delete('threading');
	SP()->DB->execute('ALTER TABLE '.SPPOSTS.' DROP thread_index');
	SP()->DB->execute('ALTER TABLE '.SPPOSTS.' DROP thread_parent');
	SP()->DB->execute('ALTER TABLE '.SPPOSTS.' DROP control_index');
}

function sp_threading_do_deactivate() {
}

function sp_threading_do_sp_deactivate() {
}

function sp_threading_do_sp_uninstall() {
}

function sp_threading_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'threading/sp-threading-plugin.php') {
		$url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
		$actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-threading')."'>".__('Uninstall', 'sp-threading').'</a>';
        $url = SPADMINOPTION.'&amp;tab=global';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-threading')."'>".__('Options', 'sp-threading').'</a>';
	}
	return $actionlink;
}
