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

# make sure we have valid data passed in
if (!isset($_GET['fid']) || !isset($_GET['type'])) die();

# 5 minutes execution time
@set_time_limit(5 * 60);

# Check file extension isn't prohibited
sp_plupload_check_prohibited($_FILES['file']['name']);

# Get upload type and double check permissions
$check = sp_plupload_check_permissions($_FILES['file']['name'], SP()->filters->integer($_GET['fid']));
$typenow = $check->type;

# check file max size
sp_plupload_check_filesize($typenow, $_FILES['file']['size']);

# move the upload to its storage location
$check = sp_plupload_move_upload($_FILES['file']['name'], $_FILES['file']['tmp_name'], $typenow);
$browsepath = $check->path;
$filename = $check->filename;

# now process the file (create thumb and rename)
$check = sp_plupload_process_upload($browsepath, $filename, $typenow, SP()->filters->str($_GET['type']));
$width = isset($check->width) ? $check->width : '';
$height = isset($check->height) ? $check->height : '';

# if usiing thumbnails, lets insert an approximate thumbnail instead of the full size image
$targetWidth = $targetHeight = '';
$sfimage = SP()->options->get('sfimage');
if ($sfimage['enlarge'] || $sfimage['process']) {
    if ($width > $sfimage['thumbsize'] || $height > $sfimage['thumbsize']) {
        $targetWidth = $targetHeight = min($sfimage['thumbsize'], max($width, $height));
        $ratio = $width / $height;
        if ($ratio < 1) {
            $targetWidth = floor($targetHeight * $ratio);
        } else {
            $targetHeight = floor($targetHeight / $ratio);
        }
    }
}

# success uploading
die('{"jsonrpc" : "2.0", "error" : {"code": "0", "message": "'.__("File successfully uploaded", "sp-plup").'"}, "type" : "'.$typenow.'", "file" : "'.$filename.'", "size" : "'.$_FILES['file']['size'].'", "width" : "'.$width.'", "height" : "'.$height.'", "twidth" : "'.$targetWidth.'", "theight" : "'.$targetHeight.'"}');
