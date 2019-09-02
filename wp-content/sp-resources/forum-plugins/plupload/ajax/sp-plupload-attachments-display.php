<?php
/*
Simple:Press
Plupload Plugin file tree dipslay ajax routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

sp_forum_ajax_support();

if (!sp_nonce('plupload-attachments')) die();

$userid = SP()->filters->integer($_GET['uid']);
if (empty($userid)) die();
$thisUser = SP()->user->get($userid);

global $plup;
sp_plupload_config($thisUser);

$sfconfig = SP()->options->get('sfconfig');

$id = ($_POST['id'] == '#') ? '' : SP()->filters->str($_POST['id']).'/';

$type = SP()->filters->str($_POST['type']);

$path = $plup['basepath'][$type].$id;

$response = array();

$files = array();
if (is_dir($path)) $files = scandir($path);
if (!empty($files) && count($files) > 0) {
    $dirlist = array();
    $filelist = array();
	foreach ($files as $file) {
        if (!file_exists($path.$file) || $file == '.' || $file == '..' || $file == '_thumbs') continue;
        $thisResponse = array();

        $name = sanitize_title($id.$file);
        $url = htmlentities($plup['baseurl'][$type].$id.$file);
        $thisResponse['id'] = $id.$file;
        $thisResponse['text'] = $file;
        if (is_dir($path.$file)) {
            # see if directory is empty
            $empty = true;
            $subfiles = scandir($path.$file);
            if (count($subfiles) > 0) {
            	foreach ($subfiles as $subfile) {
                    if ($subfile != '.' && $subfile != '..' && $subfile != '_thumbs') {
                        $empty = false;
                        break;
                    }
            	}
            }
            # if empty directory, dont show it
            if ($empty) continue;

            $thisResponse['icon'] = SPPLUPIMAGES.'directory.png';
            $thisResponse['type'] = 'directory';
            $thisResponse['children'] = true;
            $thisResponse['state']['disabled'] = true;

            $dirlist[] = $thisResponse;
        } else {
            if ($type == 'image') {
                $thisResponse['icon'] = SPPLUPIMAGES.'image.png';
                $thisResponse['a_attr'] = array('id' => $name, 'onmouseover' => "spj.profileViewThumb('$name', '$url');", 'onmouseout' => 'spj.profileCloseThumb();');
            } else if ($type = 'media') {
                $thisResponse['icon'] = SPPLUPIMAGES.'media.png';
            } else {
                $thisResponse['icon'] = SPPLUPIMAGES.'file.png';
            }
            $thisResponse['type'] = 'file';
            $thisResponse['children'] = false;

            $filelist[] = $thisResponse;
        }
	}

    # sort dirs first then files - both ascending alpha order
    $response = array_merge($dirlist, $filelist);
}

print json_encode($response);

die();
