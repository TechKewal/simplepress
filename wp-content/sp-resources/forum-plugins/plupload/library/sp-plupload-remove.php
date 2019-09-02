<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

require_once SPPLUPLIBDIR.'sp-plupload-components.php';

sp_plupload_config(SP()->user->thisUser);
global $plup;

# make sure we have a file to delete
if (empty($_GET['name'])) die();
$name = SP()->filters->str($_GET['name']);

# get upload type and check permission
$nameparts = explode('.', $name);
$ext = end($nameparts);
if (!sp_plupload_validate_extension($ext, $plup['filetype']['image'])) {
	$typenow = 'image';
	if (!SP()->auths->get('upload_images', SP()->filters->integer($_GET['fid']))) die();
} else if (!sp_plupload_validate_extension($ext, $plup['filetype']['media'])) {
	$typenow = 'media';
	if (!SP()->auths->get('upload_media', SP()->filters->integer($_GET['fid']))) die();
} else if (!sp_plupload_validate_extension($ext, $plup['filetype']['file'])) {
	$typenow = 'file';
	if (!SP()->auths->get('upload_files', SP()->filters->integer($_GET['fid']))) die();
} else {
	die();
}

# clean the file name same as when uploaded
$file_name = stripslashes($name);
$file_name = sp_plupload_clean_filename($file_name);

# make sure file exists
$browsepath = $plup['path'][$typenow];
if (!file_exists($browsepath.$file_name)) die();

# okay now remove
@unlink($browsepath.$file_name);
