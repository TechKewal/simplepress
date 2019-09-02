<?php
/*
Simple:Press
Tags plugin install/upgrade routine
$LastChangedDate: 2018-08-11 06:04:24 -0500 (Sat, 11 Aug 2018) $
$Rev: 15691 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_tags_do_install() {
	$tags = SP()->options->get('tags');
	if (empty($tags)) {
        $sql = '
            CREATE TABLE IF NOT EXISTS '.SPTAGS.' (
                tag_id bigint(20) NOT NULL auto_increment,
                tag_name varchar(50) default NULL,
                tag_slug varchar(50) default NULL,
                tag_count bigint(20) default "0",
                PRIMARY KEY  (tag_id)
            ) '.SP()->DB->charset();
        SP()->DB->execute($sql);

        $sql = '
            CREATE TABLE IF NOT EXISTS '.SPTAGSMETA.' (
                meta_id bigint(20) NOT NULL auto_increment,
                tag_id bigint(20) default "0",
                topic_id bigint(20) default "0",
                forum_id bigint(20) default "0",
                PRIMARY KEY  (meta_id),
                KEY tag_id_idx (tag_id),
                KEY topic_id_idx (topic_id),
                KEY forum_id_idx (forum_id)
            ) '.SP()->DB->charset();
        SP()->DB->execute($sql);

        # need new column
		SP()->DB->execute('ALTER TABLE '.SPFORUMS.' ADD (use_tags smallint(1) NOT NULL default "1")');

		$tags = array();

		$sfdisplay = SP()->options->get('sfdisplay');
		if (isset($sfdisplay['sfmaxtags'])) {
			$tags['maxtags'] = $sfdisplay['sfmaxtags'];
			unset($sfdisplay['sfmaxtags']);
		} else {
			$tags['maxtags'] = 0;
		}

		$metatags = SP()->options->get('sfmetatags');
		if (!empty($metatags['sftagwords'])) {
			$tags['tagwords'] = $metatags['sftagwords'];
			unset($metatags['sftagwords']);
			SP()->options->update('sfmetatags', $metatags);
		} else {
			$tags['tagwords'] = true;
		}

		if (isset($sfdisplay['topics']['topictags'])) unset($sfdisplay['topics']['topictags']);
		if (isset($sfdisplay['posts']['tagsabove'])) unset($sfdisplay['posts']['tagsabove']);
		if (isset($sfdisplay['posts']['tagsbelow'])) unset($sfdisplay['posts']['topictags']);
		SP()->options->update('sfdisplay', $sfdisplay);

 		SP()->options->add('tags', $tags);
    }

    # add our tables to installed list
   	$tables = SP()->options->get('installed_tables');
    if ($tables) {
        if (!in_array(SPTAGS, $tables)) $tables[] = SPTAGS;
        if (!in_array(SPTAGSMETA, $tables)) $tables[] = SPTAGSMETA;
        SP()->options->update('installed_tables', $tables);
    }

   	global $wp_roles;
    $wp_roles->add_cap('administrator', 'SPF Manage Tags', false);

    # do we need to give activater Manage Tags capability
    if (!SP()->auths->current_user_can('SPF Manage Tags')) {
		$user = new WP_User(SP()->user->thisUser->ID);
		$user->add_cap('SPF Manage Tags');
    }

   	SP()->auths->add('edit_tags', __('Can edit topic tags', 'sp-tags'), 1, 0, 0, 0, 4);
    SP()->auths->activate('edit_tags');

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';

	# flush rewrite rules for pretty permalinks
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

# sp reactivated.
function sp_tags_do_sp_activate() {
}

function sp_tags_do_reset_permissions() {
   	SP()->auths->add('edit_tags', __('Can edit topic tags', 'sp-tags'), 1, 0, 0, 0, 4);
}
