 <?php
/*
Simple:Press
File Uploader Plugin Admin Options Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_plupload_admin_options_form() {
	$uploads = SP()->options->get('spPlupload');

	spa_paint_options_init();
	spa_paint_open_tab(__('File Uploader', 'sp-plup').' - '.__('Settings', 'sp-plup'));
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Image Files', 'sp-plup'), true, 'image-files');
				spa_paint_checkbox(__('Auto insert images into post content', 'sp-plup'), "imageinsert", $uploads['imageinsert']);
				spa_paint_input(__('Image quality of resized images (0-100)', 'sp-plup'), 'imgquality', $uploads['imgquality']);
				spa_paint_input(__('Image quality of generated thumbnails (0-100)', 'sp-plup'), 'thumbquality', $uploads['thumbquality']);
                $text = __('Maximum filesize (in bytes) of image files (0 = no limit)', 'sp-plup');
                $text.= '<br /><small>'.__('PHP UPLOAD_MAX_SIZE is', 'sp-plup').' '.sp_plupload_return_bytes(ini_get('upload_max_filesize'));
                $text.= ' '.__('and PHP POST_MAX_SIZE is', 'sp-plup').' '.sp_plupload_return_bytes(ini_get('post_max_size')).'</small>';
				spa_paint_input($text, 'imagemaxsize', $uploads['imagemaxsize']);
				spa_paint_input(__('Maximum width (in pixels) of image files (0 = no limit)', 'sp-plup'), 'imagemaxwidth', $uploads['imagemaxwidth']);
				spa_paint_input(__('Maximum height (in pixels) of image files (0 = no limit)', 'sp-plup'), 'imagemaxheight', $uploads['imagemaxheight']);
				spa_paint_wide_textarea(__('Allowed File Types', 'sp-plup'), 'imagetypes', $uploads['imagetypes'], __('Separate each file type with a comma', 'sp-plup'));
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Media Files', 'sp-plup'), true, 'media-files');
				spa_paint_checkbox(__('Auto insert media into post content', 'sp-plup'), "mediainsert", $uploads['mediainsert']);
                $text = __('Maximum filesize (in bytes) of media files (0 = no limit)', 'sp-plup');
                $text.= '<br /><small>'.__('PHP UPLOAD_MAX_SIZE is', 'sp-plup').' '.sp_plupload_return_bytes(ini_get('upload_max_filesize'));
                $text.= ' '.__('and PHP POST_MAX_SIZE is', 'sp-plup').' '.sp_plupload_return_bytes(ini_get('post_max_size')).'</small>';
				spa_paint_input($text, 'mediamaxsize', $uploads['mediamaxsize']);
				spa_paint_wide_textarea(__('Allowed file types', 'sp-plup'), 'mediatypes', $uploads['mediatypes'], __('Separate each file type with a comma', 'sp-plup'));
				spa_paint_input(__('Width (in pixels) of media files', 'sp-plup'), 'mediawidth', $uploads['mediawidth']);
				spa_paint_input(__('Height (in pixels) of media files', 'sp-plup'), 'mediaheight', $uploads['mediaheight']);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

	spa_paint_tab_right_cell();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Other File Types', 'sp-plup'), true, 'other-files');
				spa_paint_checkbox(__('Auto insert file attachments into post content as a link', 'sp-plup'), "fileinsert", $uploads['fileinsert']);
                $text = __('Maximum filesize (in bytes) of other files (0 = no limit)', 'sp-plup');
                $text.= '<br /><small>'.__('PHP UPLOAD_MAX_SIZE is', 'sp-plup').' '.sp_plupload_return_bytes(ini_get('upload_max_filesize'));
                $text.= ' '.__('and PHP POST_MAX_SIZE is', 'sp-plup').' '.sp_plupload_return_bytes(ini_get('post_max_size')).'</small>';
				spa_paint_input($text, 'filemaxsize', $uploads['filemaxsize']);
				spa_paint_wide_textarea(__('Allowed other file types', 'sp-plup'), 'filetypes', $uploads['filetypes'], __('Separate each file type with a comma', 'sp-plup'));
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		echo '<br />&nbsp;&nbsp;&nbsp;&nbsp;'.__('Note: maximum upload limits are not applied to forum administrators', 'sp-plup');


		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Settings', 'sp-plup'), true, 'upload-settings');
				spa_paint_checkbox(__('Replace the SP profile photo urls with uploaded photos', 'sp-plup'), "useforphotos", $uploads['useforphotos']);
				spa_paint_input(__('Plupload language code', 'sp-plup'), 'lang', $uploads['lang']);
				spa_paint_checkbox(__('Show attachments uploaded to post content in the attachments section below post content', 'sp-plup'), "showinserted", $uploads['showinserted']);
				spa_paint_checkbox(__('Show uploaded files list as thumbnails', 'sp-plup'), "showthumbs", $uploads['showthumbs']);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Prohibited Files', 'sp-plup'), true, 'prohibited-files');
				spa_paint_wide_textarea(__('Prohibited file types', 'sp-plup'), 'prohibited', $uploads['prohibited'], __('Separate each file type with a comma', 'sp-plup'));
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_close_container();
}
