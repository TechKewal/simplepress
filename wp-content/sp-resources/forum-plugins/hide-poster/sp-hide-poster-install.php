<?php
/*
Simple:Press
Hide Posters plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_hide_poster_do_install() {
	$options = SP()->options->get('hide-poster');
	if (empty($options)) {
        SP()->DB->execute('ALTER TABLE '.SPFORUMS.' ADD (forum_hide_posters smallint(1) NOT NULL default 0)');
        SP()->DB->execute('ALTER TABLE '.SPTOPICS.' ADD (topic_hide_posters smallint(1) NOT NULL default 0)');

        $options['default_enable'] = true;

        $options['dbversion'] = SPHIDEDBVERSION;
        SP()->options->update('hide-poster', $options);

        # flush the cache as we are changing the forum table
        SP()->cache->flush('all');
    }

    $authslug = sp_create_slug(SP()->primitives->admin_text('Creating'), false, SPAUTHCATS, 'authcat_slug');
    $cat = SP()->DB->table(SPAUTHCATS, "authcat_slug='$authslug'", 'authcat_id');
	SP()->auths->add('hide_posters', __('Can enable/disable hide posters for a topic', 'sp-hide-poster'), 1, 1, 0, 0, $cat);
    SP()->auths->activate('hide_posters');
}

# sp reactivated.
function sp_hide_poster_do_sp_activate() {
}

# permissions reset
function sp_hide_poster_do_reset_permissions() {
    $authslug = sp_create_slug(SP()->primitives->admin_text('Creating'), false, SPAUTHCATS, 'authcat_slug');
    $cat = SP()->DB->table(SPAUTHCATS, "authcat_slug='$authslug'", 'authcat_id');
	SP()->auths->add('hide_posters', __('Can enable/disable hide posters for a topic', 'sp-hide-poster'), 1, 1, 0, 0, $cat);
}
