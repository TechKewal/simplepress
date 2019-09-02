<?php
/*
Simple:Press
Private Posts plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the this plugin uninstall only
function sp_private_posts_do_uninstall() {
    # handle private posts on uninstall
	$options = SP()->options->get('private-posts');
    if ($options['uninstall'] == 1) {
        SP()->DB->execute('DELETE FROM '.SPPOSTS.' WHERE private=1');
    } else {
        SP()->DB->execute('UPDATE '.SPPOSTS.' SET post_content="" WHERE private=1');
    }

    # delete our option
    SP()->options->delete('private-posts');

    SP()->auths->delete('view_private_posts');
    SP()->auths->delete('post_private');

	SP()->DB->execute('ALTER TABLE '.SPPOSTS.' DROP private');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-privposts');
}

function sp_private_posts_do_deactivate() {
    SP()->auths->deactivate('view_private_posts');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-privposts');
}

function sp_private_posts_do_sp_deactivate() {
}

function sp_private_posts_do_sp_uninstall() {
}

function sp_private_posts_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'private-posts/sp-private-posts-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-private-posts')."'>".__('Uninstall', 'sp-private-posts').'</a>';
        $url = SPADMINOPTION.'&amp;tab=content';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-private-posts')."'>".__('Options', 'sp-private-posts').'</a>';
    	$actionlink.= '<br />';
    	$actionlink.= __('Please check plugin options for how to handle private posts when uninstalling', 'sp-private-posts');
    }
	return $actionlink;
}

function sp_private_posts_do_uninstall_message($msg, $plugin) {
    if ($plugin == 'private-posts/sp-private-posts-plugin.php') {
        $msg.= '<br /><br /><b>'.__('WARNING: This will affect your current private posts.  Please make sure you have selected the desired option on how to handle private posts when uninstalling.', 'sp-private-posts').'</b>';
    }
    return $msg;
}
