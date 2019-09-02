<?php
/*
Simple:Press
Post Rating plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_rating_do_install() {
	SP()->activity->add_type('posts rated');

	$oldrating = SP()->options->get('sfpostratings');
	$ratingdata = SP()->options->get('postratings');
	if (empty($oldrating) && empty($ratingdata)) {
        # brand new install
		$sql = '
			CREATE TABLE IF NOT EXISTS '.SPRATINGS.' (
				rating_id bigint(20) NOT NULL auto_increment,
				post_id bigint(20) NOT NULL,
				vote_count bigint(20) NOT NULL,
				ratings_sum bigint(20) NOT NULL,
				ips longtext,
				members longtext,
				PRIMARY KEY  (rating_id),
				KEY post_id_idx (post_id)
			) '.SP()->DB->charset();
		SP()->DB->execute($sql);

        # need new columns
        SP()->DB->execute('ALTER TABLE '.SPFORUMS.' ADD (post_ratings smallint(1) NOT NULL default 0)');
		$ratingdata = array();
        $ratingdata['ratingsstyle'] = 1;
		$ratingdata['dbversion'] = SPRATINGDBVERSION;
		SP()->options->add('postratings', $ratingdata);
    } elseif (empty($ratingdata)) {
        # upgrade from when it was part of core
   		$new_rating = array();
		$new_rating['ratingsstyle'] = $oldrating['ratingsstyle'];

		$sfdisplay = SP()->options->get('sfdisplay');
		if (!empty($sfdisplay['topics']['postrating'])) {
			unset($sfdisplay['topics']['postrating']);
			SP()->options->update('sfdisplay', $sfdisplay);
		}

		$new_rating['dbversion'] = SPRATINGDBVERSION;
		SP()->options->add('postratings', $new_rating);
		SP()->options->delete('sfpostratings');
    }

    # add our tables to installed list
   	$tables = SP()->options->get('installed_tables');
    if ($tables && !in_array(SPRATINGS, $tables)) {
        $tables[] = SPRATINGS;
        SP()->options->update('installed_tables', $tables);
    }

    # add a new permission into the auths table
	SP()->auths->add('rate_posts', __('Can rate a post', 'sp-rating'), 1, 1, 0, 0, 1);

    # activation so make our auth active
    SP()->auths->activate('rate_posts');

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

function sp_rating_do_permissions_reset() {
	SP()->auths->add('rate_posts', __('Can rate a post', 'sp-rating'), 1, 1, 0, 0, 1);
}
