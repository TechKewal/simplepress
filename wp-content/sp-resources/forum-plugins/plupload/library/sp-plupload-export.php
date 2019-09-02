<?php
/*
Simple:Press
Desc: Privacy - Personal Data Export
$LastChangedDate: 2017-08-05 06:56:34 +0100 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_privacy_do_plupload_listing($exportItems, $spUserData) {
	# start by compiling the lists if there are any uploads...
	$image_files = array();
	$media_files = array();
	$file_files  = array();
	
	$sfconfig = SP()->options->get('sfconfig');
	$user_slug = (!empty($spUserData->ID)) ? sp_create_slug($spUserData->user_login, false) : '';

	$image_uploads_basepath = str_replace('\\', '/', SP_STORE_DIR.'/'.$sfconfig['image-uploads'].'/'.$user_slug);
	$image_uploads_baseurl = str_replace('\\', '/', SP_STORE_URL.'/'.$sfconfig['image-uploads'].'/'.$user_slug);

	$media_uploads_basepath = str_replace('\\', '/', SP_STORE_DIR.'/'.$sfconfig['media-uploads'].'/'.$user_slug);
	$media_uploads_baseurl = str_replace('\\', '/', SP_STORE_URL.'/'.$sfconfig['media-uploads'].'/'.$user_slug);

	$file_uploads_basepath = str_replace('\\', '/', SP_STORE_DIR.'/'.$sfconfig['file-uploads'].'/'.$user_slug);
	$file_uploads_baseurl = str_replace('\\', '/', SP_STORE_URL.'/'.$sfconfig['file-uploads'].'/'.$user_slug);

	$files = array();
	sp_list_plupload_files($files, $image_uploads_basepath, $image_uploads_basepath, $image_uploads_baseurl);
	$image_files = $files;

	$files = array();
	sp_list_plupload_files($files, $media_uploads_basepath, $media_uploads_basepath, $media_uploads_baseurl);
	$media_files = $files;

	$files = array();
	sp_list_plupload_files($files, $file_uploads_basepath, $file_uploads_basepath, $file_uploads_baseurl);
	$file_files = $files;

	# now to prep to send back
	$data = array();
	$idx = 1;

	# images
	if (!empty($image_files)) {
		foreach($image_files as $image_file) {
			$data[] = array(
				'name'	=> __('URL', 'sp-plup'),
				'value' => $image_file
			);
		}
		$exportItems[] = array(
			'group_id'		=> __('Image-Uploads', 'sp-plup'),
			'group_label' 	=> __('Forum Image Uploads', 'sp-plup'),
			'item_id' => 'uploads-'.$idx,
			'data' => $data
		);
		$idx++;
		unset($image_files);
	}

	# media
	$data = array();

	if (!empty($media_files)) {
		foreach($media_files as $media_file) {
			$data[] = array(
				'name'	=> __('URL', 'sp-plup'),
				'value' => $media_file
			);
		}
		$exportItems[] = array(
				'group_id'		=> __('Media-Uploads', 'sp-plup'),
				'group_label' 	=> __('Forum Media Uploads', 'sp-plup'),
				'item_id' => 'uploads-'.$idx,
				'data' => $data
			);
		$idx++;
		unset($media_files);
	}

	# files
	$data = array();

	if (!empty($file_files)) {
		foreach($file_files as $file_file) {
			$data[] = array(
				'name'	=> __('URL', 'sp-plup'),
				'value' => $file_file
			);
		}
		$exportItems[] = array(
			'group_id'		=> __('File-Uploads', 'sp-plup'),
			'group_label' 	=> __('File Uploads', 'sp-plup'),
			'item_id' => 'uploads-'.$idx,
			'data' => $data
		);
		$idx++;
		unset($file_files);
	}
	
	return $exportItems;
}

function sp_list_plupload_files(&$files, $dir, $path, $url){

	if (file_exists($dir)) {	
		$all_files = scandir($dir);

		unset($all_files[array_search('.', $all_files, true)]);
		unset($all_files[array_search('..', $all_files, true)]);
		unset($all_files[array_search('_thumbs', $all_files, true)]);

		# prevent empty ordered elements
		if (count($all_files) < 1)
			return;

		foreach($all_files as $each_file) {
			if(is_dir($dir.'/'.$each_file)) {
				sp_list_plupload_files($files, $dir.'/'.$each_file, $path, $url);
			} else {
				$thisFile =  $dir .'/'. $each_file;
				$files[] = str_replace($path, $url, $thisFile);
			}
		}
	}
}
