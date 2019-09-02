<?php
/*
Simple:Press
TinyMCE Plugin Admin Components Toolbar Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_tinymce_options_form()
{
	# Load tinymce toolbar options
	$tm = SP()->options->get('tinymce');

	spa_paint_options_init();
	spa_paint_open_tab(__("Components", 'sp-tinymce')." - ".__("Editor - TinyMCE Settings", "sp-tinymce"), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__("TinyMCE Options", "sp-tinymce"), true, 'tinymce-options');
				spa_paint_input(__("Editor window height (in pixels)", "sp-tinymce"), "tmheight", $tm['height'], false, false);
				spa_paint_checkbox(__("Reject posts with embedded formatting and force correct use of paste options", "sp-tinymce"), "tmrejectformat", $tm['rejectformat']);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__("TinyMCE Toolbars and Plugins", "sp-tinymce"), true, 'tinymce-functions');
				spa_paint_wide_textarea(__("TinyMCE plugins", "sp-tinymce"), "tmplugins", $tm['plugins'], '', 2);
				spa_paint_wide_textarea(__("Toolbar 1", "sp-tinymce"), "tmbuttons1", $tm['buttons1'], '', 2);
				spa_paint_wide_textarea(__("Toolbar 2", "sp-tinymce"), "tmbuttons2", $tm['buttons2'], '', 2);
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
}
