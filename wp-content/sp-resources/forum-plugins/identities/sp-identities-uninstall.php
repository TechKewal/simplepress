<?php
/*
Simple:Press
Identities plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_identities_do_uninstall() {
	# remove our storage locations
	SP()->plugin->remove_storage('identities');

    # delete our option
    SP()->options->delete('identities');

	$meta = SP()->meta->get('user_identities', 'user_identities');
	if (!empty($meta[0]['meta_value'])) {
		foreach ($meta[0]['meta_value'] as $identity) {
    		SP()->DB->execute('DELETE FROM '.SPUSERMETA.' WHERE meta_key="'.$identity['slug'].'"');
        }
	}
	# remove glossary entries
	sp_remove_glossary_plugin('sp-identities');
}

function sp_identities_do_deactivate() {
	# remove glossary entries
	sp_remove_glossary_plugin('sp-identities');
}

function sp_identities_do_sp_deactivate() {
}

function sp_identities_do_sp_uninstall() {
}

function sp_identities_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'identities/sp-identities-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-identities')."'>".__('Uninstall', 'sp-identities').'</a>';
        $url = SPADMINPROFILE.'&amp;tab=plugin&amp;admin=sp_identities_admin&amp;save=sp_identities_update&amp;form=0';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-identities')."'>".__('Options', 'sp-identities').'</a>';
    }
	return $actionlink;
}
