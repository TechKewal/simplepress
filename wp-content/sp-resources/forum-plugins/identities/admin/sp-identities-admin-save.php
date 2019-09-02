<?php
/*
Simple:Press
Identities Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_identities_admin_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$mess = '';

	$sfconfig = SP()->options->get('sfconfig');
    $path = SP_STORE_DIR.'/'.$sfconfig['identities'].'/';

	$identities = array();

	$name = (isset($_POST['idname'])) ? $_POST['idname'] : '';

    $count = 0;
	for ($x=0; $x < count($name); $x++) {
		$file = SP()->saveFilters->filename($_POST['idfile'][$x]);
		if (file_exists($path.$file)) {
			if (empty($name[$x])) {
            	$path_info = pathinfo($path.$file);
        		$fn = strtolower($path_info['filename']);
                $name[$x] = $fn;
            }
			$identities[$count]['file'] = $file;
			$identities[$count]['name'] = SP()->saveFilters->name($name[$x]);
			$identities[$count]['slug'] = sp_create_slug($name[$x], false);
			$identities[$count]['base_url'] = SP()->saveFilters->url($_POST['idurl'][$x]);
            $count++;
		}
	}

	# load current saved smileys to get meta id
	$meta = SP()->meta->get('user_identities', 'user_identities');
	SP()->meta->add('user_identities', 'user_identities', $identities);

    do_action('sph_identities_save');

	$mess.= '<br />'.__('Identities updated', 'sp-identities');
	return $mess;
}
