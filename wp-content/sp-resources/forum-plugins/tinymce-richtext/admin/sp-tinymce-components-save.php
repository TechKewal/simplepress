<?php
/*
Simple:Press
TinyMCE Save Options Support Functions
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

#= Save Toolbar Settings ===============================
function sp_tinymce_options_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	# Save the tinymce data
	$tinymce = SP()->options->get('tinymce');

	$tinymce['height']		= SP()->filters->integer($_POST['tmheight']);
	$tinymce['plugins']		= SP()->filters->str($_POST['tmplugins']);
	$tinymce['buttons1']	= SP()->filters->str($_POST['tmbuttons1']);
	$tinymce['buttons2']	= SP()->filters->str($_POST['tmbuttons2']);

	if (isset($_POST['tmrejectformat'])) { $tinymce['rejectformat'] = true; } else { $tinymce['rejectformat']=false; }

	SP()->options->update('tinymce', $tinymce);

	return __("TinyMCE editor options updated!", "sp-tinymce");
}
