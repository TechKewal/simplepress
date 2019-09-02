<?php
/*
Simple:Press
Name plugin uninstall routine
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the topic description plugin uninstall only
function sp_redirect_do_uninstall() {
    # delete our option
    SP()->options->delete('redirect');
	# remove topics column
	SP()->DB->execute('ALTER TABLE '.SPTOPICS.' DROP redirect');
	SP()->DB->execute('ALTER TABLE '.SPTOPICS.' DROP redirect_desc');
	# remove the auth
	SP()->auths->delete('create_topic_redirects');
}

function sp_redirect_do_deactivate() {
    SP()->auths->deactivate('create_topic_redirects');
}

function sp_redirect_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'topic-redirect/sp-redirect-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-redirect')."'>".__('Uninstall', 'sp-redirect').'</a>';
    }
	return $actionlink;
}
