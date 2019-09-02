<?php
/*
Simple:Press
Uploads Viewer Plugin file tree dipslay routine
$LastChangedDate: 2018-10-25 04:18:59 -0500 (Thu, 25 Oct 2018) $
$Rev: 15771 $
*/

$path 	= urldecode($_POST['dir']);
$url 	= filter_var($_POST['url'], FILTER_SANITIZE_URL);

$mobile = filter_var($_POST['mobile'], FILTER_SANITIZE_NUMBER_INT);
$type 	= filter_var($_POST['type'], FILTER_SANITIZE_STRING);

$slug = explode('/', $url);
$pieceCount = count($slug);
$user = $slug[$pieceCount-2].'/';

$add = explode($user, $path);
$url = $url.$add[1];
$dir = str_replace('/', '-', $add[1]);

if (file_exists($path)) {
	$files = scandir($path);
	natcasesort($files);
	if (count($files) > 0) { /* The 2 accounts for . and .. */
		echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
		# All dirs
		foreach ($files as $file) {
			if (file_exists($path.$file) && $file != '.' && $file != '..' && $file != '_thumbs' && is_dir($path.$file)) {
				echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"".htmlentities($path.$file)."/\">".htmlentities($file)."</a></li>";
			}
		}
		# All files
		foreach ($files as $x => $file) {
			if (file_exists($path.$file) && $file != '.' && $file != '..' && !is_dir($path.$file)) {
				$ext = preg_replace('/^.*\./', '', $file);
				switch ($type) {
					case 'images':
						$thumb = $url.'_thumbs/_'.$file;
						$thumbInfo = @getimagesize(htmlentities($path.'_thumbs/_'.$file));
						$imgInfo = @getimagesize(htmlentities($path.$file));
						$out = "<li class='file ext_$ext'><a id='file$dir$x' ";
						$out.= "class='spUploadsEditorInsertImage' data-file='".htmlentities($file)."' data-path='".htmlentities($path)."' data-url='".htmlentities($url)."' data-width='".$imgInfo[0]."' data-height='".$imgInfo[1]."' data-twidth='".$thumbInfo[0]."' data-theight='".$thumbInfo[1]."' data-mobile='".$mobile."' data-thumbfile='file".$dir.$x."' data-thumb='".$thumb."'>".htmlentities($file)."</a></li>";
						echo $out;
						break;

					case 'media':
						$width = filter_var($_POST['width'], FILTER_SANITIZE_NUMBER_INT);
						$height = filter_var($_POST['height'], FILTER_SANITIZE_NUMBER_INT);
						echo "<li class='file ext_$ext'><a class='spUploadsEditorInsertMedia' data-path='".htmlentities($path)."' data-file='".htmlentities($file)."' data-url='".htmlentities($url)."' data-width='".$width."' data-height='".$height."'>".htmlentities($file)."</a></li>";
						break;

					case 'files':
						echo '<li class="file ext_'.$ext.'"><a class="spUploadsEditorInsertText" data-path="'.htmlentities($path).'" data-url="'.htmlentities($url.$file).'" data-file="'.$file.'">'.htmlentities($file).'</a></li>';
						break;
				}
			}
		}
		echo "</ul>";
	}
}
