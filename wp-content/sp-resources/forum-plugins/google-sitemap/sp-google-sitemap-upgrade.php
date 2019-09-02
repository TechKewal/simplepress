<?php
/*
Simple:Press
Google XML Sitemap plugin install/upgrade routine
$LastChangedDate: 2013-02-17 12:52:14 -0700 (Sun, 17 Feb 2013) $
$Rev: 9854 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_google_sitemap_do_upgrade_check() {
    if (!SP()->plugin->is_active('google-sitemap/sp-google-sitemap-plugin.php')) return;

    $options = SP()->options->get('sfbuildsitemap');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == GSMDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades
    if ($db < 5) {
        # replace old option with array
        $options = array();

        # no longer need sitemap generate cron
    	wp_clear_scheduled_hook('sph_cron_sitemap');
    }

    # save data
    $options['dbversion'] = GSMDBVERSION;
    SP()->options->update('sfbuildsitemap', $options);
}
