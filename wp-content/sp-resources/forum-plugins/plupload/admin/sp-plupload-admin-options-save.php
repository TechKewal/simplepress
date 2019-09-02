<?php
/*
Simple:Press
File Uploader Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_plupload_admin_options_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

    # get php max settings
    $max_upload = sp_plupload_return_bytes(ini_get('upload_max_filesize'));
    $max_post = sp_plupload_return_bytes(ini_get('post_max_size'));

	$uploads = SP()->options->get('spPlupload');

    $uploads['showinserted'] = isset($_POST['showinserted']);
	$quality = SP()->filters->integer($_POST['imgquality']);
    if ($quality == '' || $quality > 100) $quality = 100;
    if ($quality < 0) $quality = 0;
	$uploads['imgquality'] = $quality;
	$quality = SP()->filters->integer($_POST['thumbquality']);
    if ($quality == '' || $quality > 100) $quality = 100;
    if ($quality < 0) $quality = 0;
	$uploads['thumbquality'] = $quality;
	$uploads['imagetypes'] = SP()->saveFilters->title(trim($_POST['imagetypes']));
    $uploads['imageinsert'] = isset($_POST['imageinsert']);

    # limit image max file size to what php allows
    $uploads['imagemaxsize'] = min($max_upload, $max_post, SP()->filters->integer($_POST['imagemaxsize']));

	$uploads['imagemaxwidth'] = SP()->filters->integer($_POST['imagemaxwidth']);
	$uploads['imagemaxheight'] = SP()->filters->integer($_POST['imagemaxheight']);
    $uploads['mediainsert'] = isset($_POST['mediainsert']);
	$uploads['mediatypes'] = SP()->saveFilters->title(trim($_POST['mediatypes']));

    # limit media max file size to what php allows
    $uploads['mediamaxsize'] = min($max_upload, $max_post, SP()->filters->integer($_POST['mediamaxsize']));
	$uploads['mediawidth'] = SP()->filters->integer($_POST['mediawidth']);
	$uploads['mediaheight'] = SP()->filters->integer($_POST['mediaheight']);

    $uploads['fileinsert'] = isset($_POST['fileinsert']);
	$uploads['filetypes'] = SP()->saveFilters->title(trim($_POST['filetypes']));

    # limit other max file size to what php allows
    $uploads['filemaxsize'] = min($max_upload, $max_post, SP()->filters->integer($_POST['filemaxsize']));

	$uploads['prohibited'] = SP()->saveFilters->title(trim($_POST['prohibited']));
    $uploads['useforphotos'] = isset($_POST['useforphotos']);
	$uploads['lang'] = SP()->saveFilters->title(trim($_POST['lang']));
    $uploads['showthumbs'] = isset($_POST['showthumbs']);

	SP()->options->update('spPlupload', $uploads);

    do_action('sph_plupload_uploads_save');

	return __('File uploader options updated!', 'sp-plup');
}
