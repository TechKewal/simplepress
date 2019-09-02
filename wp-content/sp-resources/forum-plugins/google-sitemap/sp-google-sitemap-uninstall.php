<?php
/*
Simple:Press
Google XML Sitemap plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_google_sitemap_do_uninstall() {
    # remove our option
    SP()->options->delete('sfbuildsitemap');

    # remove any scheduled cron jobs
	wp_clear_scheduled_hook('sph_cron_sitemap');

	# Remove sitemap flag for forums
	SP()->DB->execute('ALTER TABLE '.SPFORUMS.' DROP in_sitemap');
}

function sp_google_sitemap_do_sp_deactivate() {
    # remove any scheduled cron jobs
	wp_clear_scheduled_hook('sph_cron_sitemap');
}

function sp_google_sitemap_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'google-sitemap/sp-google-sitemap-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-gsm')."'>".__('Uninstall', 'sp-gsm').'</a>';
    }
	return $actionlink;
}
