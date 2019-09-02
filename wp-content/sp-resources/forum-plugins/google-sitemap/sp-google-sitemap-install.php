<?php
/*
Simple:Press
Google XML Sitemap plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_google_sitemap_do_install() {
	$options = SP()->options->get('sfbuildsitemap');
	if (empty($options)) { # brand new install
        $options = array();
        $options['dbversion'] = GSMDBVERSION;
        SP()->options->update('sfbuildsitemap', $options);

    	# add new column for user memberships in sfmember
        SP()->DB->execute('ALTER TABLE '.SPFORUMS.' ADD (in_sitemap smallint(1) NOT NULL default "1")');
    }
}

# sp reactivated. do we need to fire up cron?
function sp_google_sitemap_do_sp_activate() {
    # schedule cron if rebuilding daily
	wp_clear_scheduled_hook('sph_cron_sitemap');
   	$sfbuildsitemap = SP()->options->get('sfbuildsitemap');
    if ($sfbuildsitemap == 3) wp_schedule_event(time(), 'daily', 'sph_cron_sitemap');

}
