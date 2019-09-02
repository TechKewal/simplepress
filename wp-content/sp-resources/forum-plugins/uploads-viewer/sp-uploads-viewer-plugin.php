<?php
/*
Simple:Press Plugin Title: Uploads Viewer
Version: 2.1.0
Item Id: 3955
Plugin URI: https://simple-press.com/downloads/uploads-viewer-plugin/
Description: A Simple:Press plugin for viewing previous uploads and inserting into another post
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# we require the plupload file uploader to be active
if (!SP()->plugin->is_active('plupload/sp-plupload-plugin.php')) return;

# IMPORTANT DB VERSION
define('SPUVDBVERSION', 0);

define('SPUVDIR', 		SPPLUGINDIR.'uploads-viewer/');
define('SPUVADMINDIR', 	SPPLUGINDIR.'uploads-viewer/admin/');
define('SPUVAJAXDIR', 	SPPLUGINDIR.'uploads-viewer/ajax/');
define('SPUVLIBDIR', 	SPPLUGINDIR.'uploads-viewer/library/');
define('SPUVLIBURL', 	SPPLUGINURL.'uploads-viewer/library/');
define('SPUVCSS', 		SPPLUGINURL.'uploads-viewer/resources/css/');
define('SPUVSCRIPT', 	SPPLUGINURL.'uploads-viewer/resources/jscript/');
define('SPUVIMAGES', 	SPPLUGINURL.'uploads-viewer/resources/images/');

add_action('init', 												         'sp_uploads_viewer_localization');
add_action('sph_activate_uploads_viewer/sp-uploads-viewer-plugin.php', 	 'sp_uploads_viewer_install');
add_action('sph_uninstall_uploads_viewer/sp-uploads-viewer-plugin.php',  'sp_uploads_viewer_uninstall');
add_action('sph_deactivate_uploads_viewer/sp-uploads-viewer-plugin.php', 'sp_uploads_viewer_deactivate');
add_action('sph_print_plugin_scripts', 							         'sp_uploads_viewer_load_js');
add_action('sph_print_plugin_styles',									 'sp_uploads_viewer_head');
add_action('sph_post_create', 									         'sp_uploads_viewer_post_create');

add_filter('sph_acknowledgements',	         'sp_uploads_viewer_acknowledgement');
add_filter('sph_uploader_editor_section', 	 'sp_uploads_viewer_button', 10, 5);

# Ajax Handler
add_action('wp_ajax_uploads-viewer-view',		'sp_uploads_viewer_ajax_view');
add_action('wp_ajax_nopriv_uploads-viewer-view','sp_uploads_viewer_ajax_view');


function sp_uploads_viewer_localization() {
	sp_plugin_localisation('sp-uv');
}

function sp_uploads_viewer_install() {
    require_once SPUVDIR.'sp-uploads-viewe-install.php';
    sp_uploads_viewer_do_install();
}

function sp_uploads_viewer_uninstall() {
    require_once SPUVDIR.'sp-uploads-viewer-uninstall.php';
    sp_uploads_viewer_do_uninstall();
}

function sp_uploads_viewer_deactivate() {
    require_once SPUVDIR.'sp-uploads-viewer-uninstall.php';
    sp_uploads_viewer_do_deactivate();
}

function sp_uploads_viewer_sp_activate() {
	require_once SPUVDIR.'sp-uploads-viewer-install.php';
    sp_uploads_viewer_do_sp_activate();
}

function sp_uploads_viewer_sp_deactivate() {
	require_once SPUVDIR.'sp-uploads-viewer-uninstall.php';
    sp_uploads_viewer_do_sp_deactivate();
}

function sp_uploads_viewer_sp_uninstall() {
	require_once SPUVDIR.'sp-poluploads-viewerls-uninstall.php';
    sp_uploads_viewer_do_sp_uninstall();
}

function sp_uploads_viewer_load_js($footer) {
    require_once SPUVLIBDIR.'sp-uploads-viewer-components.php';
    sp_uploads_viewer_do_load_js($footer);
}

function sp_uploads_viewer_head() {
    require_once SPUVLIBDIR.'sp-uploads-viewer-components.php';
    sp_uploads_viewer_do_head();
}

function sp_uploads_viewer_ajax_view() {
    require_once SPUVAJAXDIR.'sp-uploads-viewer-ajax-view.php';
}

function sp_uploads_viewer_button($out, $type, $uploadImages, $uploadMedia, $uploadFiles) {
    require_once SPUVLIBDIR.'sp-uploads-viewer-components.php';
    $out = sp_uploads_viewer_do_button($out, $type, $uploadImages, $uploadMedia, $uploadFiles);
	return $out;
}

function sp_uploads_viewer_post_create($newpost) {
    require_once SPUVLIBDIR.'sp-uploads-viewer-components.php';
    sp_uploads_viewer_do_post_create($newpost);
}

function sp_uploads_viewer_acknowledgement($ack) {
	$ack[] = '<a href="http://www.abeautifulsite.net/blog/2008/03/jquery-file-tree/">'.__('File Tree by A Beautiful Site', 'sp-uv').'</a>';
	return $ack;
}
