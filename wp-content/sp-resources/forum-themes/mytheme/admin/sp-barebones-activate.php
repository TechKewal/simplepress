<?php
/*
Simple:Press
Barebones Custom Options Setup Routine
$LastChangedDate: 2014-05-17 21:16:12 +0100 (Sat, 17 May 2014) $
$Rev: 11442 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_barebones_setup($check = true) {
    # install picks up wrong SF STORE DIR so lets recalculate it for installs
    if (is_multisite() && !get_site_option('ms_files_rewriting')) {
        $uploads = wp_get_upload_dir();
        if (!defined('STORE_DIR'))		define('STORE_DIR',   $uploads['basedir']);
    } else {
        if (!defined('STORE_DIR'))		define('STORE_DIR',   WP_CONTENT_DIR);
    }

	# if exists then get right out
	if ($check) {
		if (file_exists(STORE_DIR.'/'.'sp-custom-settings/sp-barebones-custom-settings.php')) return;
	}

	# create folder of not exists
	$perms = fileperms(STORE_DIR);
	if ($perms === false) $perms = 0755;

	if (!file_exists(STORE_DIR.'/'.'sp-custom-settings')) {
		@mkdir(STORE_DIR.'/'.'sp-custom-settings', $perms);
	}

	# compile default file contents
	$C1 = '#000000';
	$C2 = '#FFFFFF';
	$C3 = '#BFCBC5';
	$C4 = '#A5B1AB';
	$C5 = '#3D7157';
	$C6 = '#F5F5F5';
	$C7 = '#000000';
	$C8 = '#3D7157';
	$C9 = '#000000';
	$C10 = '#000000';
	$C11 = '#3D7157';
	$FN = 'Tahoma';
	$F1 = '100';

	$ops = "<?php\n";
	$ops.= "\$ops = array(\n";
	$ops.= "'C1' => '".$C1."',\n";
	$ops.= "'C2' => '".$C2."',\n";
	$ops.= "'C3' => '".$C3."',\n";
	$ops.= "'C4' => '".$C4."',\n";
	$ops.= "'C5' => '".$C5."',\n";
	$ops.= "'C6' => '".$C6."',\n";
	$ops.= "'C7' => '".$C7."',\n";
	$ops.= "'C8' => '".$C8."',\n";
	$ops.= "'C9' => '".$C9."',\n";
	$ops.= "'C10' => '".$C10."',\n";
	$ops.= "'C11' => '".$C11."',\n";
	$ops.= "'FN' => '".$FN."',\n";
	$ops.= "'F1' => '".$F1."',\n";
	$ops.= ");\n?>";

	$files = array();
	$files[] = STORE_DIR.'/'.'sp-custom-settings/sp-barebones-custom-settings.php';
	$files[] = STORE_DIR.'/'.'sp-custom-settings/sp-barebones-test-settings.php';

	foreach($files as $file) {
		$f = fopen($file, 'w');
		if ($f !== false) {
			fwrite($f, $ops);
			fclose($f);
		}
	}

	# Not sure of there is any real way to sned message of success or failure.
	return;
}
