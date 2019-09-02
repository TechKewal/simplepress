<?php
/*
Simple:Press
Analytics plugin uninstall routine
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the this plugin uninstall only
function sp_analytics_do_uninstall() {
    # handle analytics on uninstall
	$options = SP()->options->get('analytics');

    # delete our option
    SP()->options->delete('analytics');

    # remove any admin capabilities
	$admins = SP()->DB->table(SPMEMBERS, "admin=1");
	foreach ($admins as $admin) {
		$user = new WP_User($admin->user_id);
		$user->remove_cap('SPF Manage Analytics');
    }
    

    # remove glossary entries
	sp_remove_glossary_plugin('sp-analytics');
}

/**
 * Deactivate plugin
 */
function sp_analytics_do_deactivate() {
    
	# remove glossary entries
	sp_remove_glossary_plugin('sp-analytics');
}


/**
 * Call once any plugin deactivate
 */
function sp_analytics_do_sp_deactivate() {
}

/**
 * Call once a plugin uninstalled
 */
function sp_analytics_do_sp_uninstall() {

    # remove any admin capabilities
    foreach ($admins as $admin) {
		$user = new WP_User($admin->user_id);
		$user->remove_cap('SPF Manage Analytics');
	}
}

/**
 * Plugin active links
 * 
 * @param string $actionlink
 * @param string $plugin
 * 
 * @return string
 */
function sp_analytics_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'analytics/sp-analytics-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-analytics')."'>".__('Uninstall', 'sp-analytics').'</a>';
		$url = SPADMINPLUGINS.'&amp;tab=plugin&amp;admin=sp_analytics_main_view&amp;save=&amp;form=0';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-analytics')."'>".__('Options', 'sp-analytics').'</a>';
    }
	return $actionlink;
}