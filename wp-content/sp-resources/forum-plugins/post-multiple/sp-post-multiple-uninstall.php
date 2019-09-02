<?php
/*
Simple:Press
Post Multiple Forums plugin uninstall routine
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for this plugin uninstall only
function sp_post_multiple_do_uninstall() {
    # delete our option
    SP()->options->delete('post-multiple');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-multiple');

	# remove the auth
	if (!empty(SP()->core->forumData['auths_map']['post_multiple'])) SP()->auths->delete('post_multiple');
}

function sp_post_multiple_do_deactivate() {
	# remove glossary entries
	sp_remove_glossary_plugin('sp-multiple');

	SP()->auths->deactivate('post_multiple');
}

function sp_post_multiple_do_sp_deactivate() {
}

function sp_post_multiple_do_sp_uninstall() {
}

function sp_post_multiple_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'post-multiple/sp-post-multiple-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-post-multiple')."'>".__('Uninstall', 'sp-post-multiple').'</a>';
        $url = SPADMINCOMPONENTS.'&amp;tab=plugin&amp;admin=sp_post_multiple_admin_options&amp;save=sp_post_multiple_admin_save_options&amp;form=1';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-post-multiple')."'>".__('Options', 'sp-post-multiple').'</a>';
    }
	return $actionlink;
}
