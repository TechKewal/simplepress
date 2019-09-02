<?php
/*
Simple:Press
File Uploader plugin ajax routine for management functions
$LastChangedDate: 2017-12-28 11:38:04 -0600 (Thu, 28 Dec 2017) $
$Rev: 15602 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

if (!sp_nonce('uploads-viewer-view')) die();

echo '<div id="spFileTreeContainer">';
echo '<div id="spFileTree"></div>';
echo '<div id="spFileThumb" style="background: #ffffff; border: 1px solid gray; padding: 5px; display:none;"></div>';
echo '</div>';

if (empty(SP()->user->thisUser->ID)) die();
$user_slug = sp_create_slug(SP()->user->thisUser->user_login, false);

$sfconfig = SP()->options->get('sfconfig');
$uploads = SP()->options->get('spPlupload');

$type = SP()->filters->str($_GET['type']);
switch ($type) {
    case 'images':
        $uploads_link = $sfconfig['image-uploads'].'/'.$user_slug.'/';
        $width = 0;
        $height = 0;
        break;

    case 'media':
        $uploads_link = $sfconfig['media-uploads'].'/'.$user_slug.'/';
        $width = (!empty($uploads['mediawidth'])) ? $uploads['mediawidth'] : 320;
        $height = (!empty($uploads['mediaheight'])) ? $uploads['mediaheight'] : 240;
        break;

    case 'files':
        $uploads_link = $sfconfig['file-uploads'].'/'.$user_slug.'/';
        $width = 0;
        $height = 0;
        break;

    default:
        die();
}
$uploads_path = str_replace('\\', '/', SP_STORE_DIR.'/'.$uploads_link);
$fileScript = str_replace('\\', '/', SPUVLIBURL.'sp-uploads-viewer-display.php')

?>
<script>
	(function(spj, $, undefined) {
		$(document).ready( function() {
			$('#spFileTree').fileTree({ root: '<?php echo $uploads_path; ?>', url: '<?php echo SP_STORE_URL.'/'.$uploads_link; ?>', script: '<?php echo $fileScript; ?>', type: '<?php echo $type; ?>', width : '<?php echo $width; ?>', height : '<?php echo $height; ?>', multiFolder: false, mobile : '<?php echo SP()->core->mobile; ?>' }, function(file) {});
		});
	}(window.spj = window.spj || {}, jQuery));
</script>
<?php
die();
