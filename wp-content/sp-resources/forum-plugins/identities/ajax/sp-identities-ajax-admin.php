<?php
/*
Simple:Press
Identities plugin ajax routine for admin functions
$LastChangedDate: 2018-10-17 15:17:49 -0500 (Wed, 17 Oct 2018) $
$Rev: 15756 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

if (!sp_nonce('identities-admin')) die();

if (!isset($_GET['targetaction'])) die();
$action = SP()->filters->str($_GET['targetaction']);

if ($action == 'delete-identity') {
	$file = SP()->filters->str($_GET['file']);
	$sfconfig = SP()->options->get('sfconfig');
    $path = SP_STORE_DIR.'/'.$sfconfig['identities'].'/'.$file;
	@unlink($path);

	# load identities from sfmeta
	$meta = SP()->meta->get('user_identities', 'user_identities');

	# now cycle through to remove this entry and resave
	if (!empty($meta[0]['meta_value'])) {
		$newidentities = array();
        $count = 0;
		foreach ($meta[0]['meta_value'] as $info) {
			if ($info['file'] != $file) {
				$newidentities[$count]['file'] = SP()->saveFilters->filename($info['file']);
				$newidentities[$count]['name'] = SP()->saveFilters->name($info['name']);
				$newidentities[$count]['slug'] = sp_create_slug($info['name'], false);
				$newidentities[$count]['base_url'] = SP()->saveFilters->url($info['base_url']);
                $count++;
			}
		}
		SP()->meta->update('user_identities', 'user_identities', $newidentities, $meta[0]['meta_id']);
	}

    die();
}

die();
