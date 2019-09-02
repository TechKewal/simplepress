<?php
/*
Simple:Press
Tags plugin install/upgrade routine
$LastChangedDate: 2018-08-11 06:04:24 -0500 (Sat, 11 Aug 2018) $
$Rev: 15691 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_tags_do_upgrade_check() {
	if (!SP()->plugin->is_active('tags/sp-tags-plugin.php')) return;

	$tags = SP()->options->get('tags');

	$db = $tags['dbversion'];
	if (empty($db)) $db = 0;

	# quick bail check
	if ($db == SPTAGSDBVERSION ) return;

	# apply upgrades as needed

	# db version upgrades
	if ($db < 1) {
		# add a new permission into the auths table
		SP()->auths->add('edit_tags', __('Can edit topic tags', 'sp-tags'), 1, 0, 0, 0, 4);
		SP()->auths->activate('edit_tags');
	}

	if ($db < 2) {
	   SP()->DB->execute('UPDATE '.SPAUTHS." SET auth_cat=4 WHERE auth_name='edit_tags'");
	}

	if ($db < 3) {
		global $wp_roles;
		$wp_roles->add_cap('administrator', 'SPF Manage Tags', false);
	}

	# db version upgrades
	if ($db < 4) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

	if ($db < 5) {
		# remove slug index from tags table
		$sql = 'DROP INDEX tag_slug_idx ON '.SPTAGS;
		$success = SP()->DB->execute($sql);
	}

	# save data
	$tags['dbversion'] = SPTAGSDBVERSION;
	SP()->options->update('tags', $tags);
}
