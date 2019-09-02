<?php
/*
Simple:Press Plugin Title: Post Preview
Version: 2.1.0
Item Id: 3915 
Plugin URI: https://simple-press.com/downloads/post-preview-plugin/
Description: Adds a Post preview option to the editor controls
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# ======================================
# CONSTANTS USED BY THIS PLUGIN
# ======================================
define('SPPPDIR',		SPPLUGINDIR.'post-preview/');
define('SPPPJS',		SPPLUGINURL.'post-preview/resources/jscript/');

if (SP()->core->device == 'mobile' && file_exists(SPPLUGINDIR.'post-preview/resources/images/mobile/') && (current_theme_supports('sp-theme-responsive'))) {
	define('SPPPIMAGES',	SPPLUGINURL.'post-preview/resources/images/mobile/');
}

# ======================================
# ACTIONS/FILTERS USED BY THUS PLUGIN
# ======================================
# Plugin and admin
add_action('init', 													'sp_preview_localisation');
add_filter('sph_post_editor_controls',								'sp_preview_button', 1, 3);
add_filter('sph_topic_editor_controls',								'sp_preview_button', 1, 3);
add_filter('sph_pm_editor_controls',								'sp_preview_button', 1, 3);

if (isset(SP()->core->forumData['display']) && SP()->core->forumData['display']['editor']['toolbar']) {
	add_filter('sph_topic_editor_toolbar',							'sp_preview_container', 1, 3);
	add_filter('sph_post_editor_toolbar',							'sp_preview_container', 1, 3);
} else {
	add_filter('sph_topic_editor_beneath',							'sp_preview_container', 1, 3);
	add_filter('sph_post_editor_beneath',							'sp_preview_container', 1, 3);
}
add_filter('sph_pm_editor_toolbar',			          				'sp_preview_container', 1, 3);
add_action('sph_print_plugin_scripts', 								'sp_preview_load_js');

# Ajax Handler
add_action('wp_ajax_preview',				'sp_preview_ajax');
add_action('wp_ajax_nopriv_preview',		'sp_preview_ajax');


# ======================================
# PLUGIN AND ADMIN
# ======================================

# ------------------------------------------------------
# Set up language file
# ------------------------------------------------------
function sp_preview_localisation() {
	sp_plugin_localisation('sp-preview');
}

# ------------------------------------------------------
# Set up Ajax handler
# ------------------------------------------------------
function sp_preview_ajax() {
	require_once SPPPDIR.'ajax/sp-ajax-preview.php';
}

# ------------------------------------------------------
# Adds new preview button
# ------------------------------------------------------
function sp_preview_button($out, $data, $a) {
	global $tab;
	$site = wp_nonce_url(SPAJAXURL.'preview', 'preview');

	if (defined('SPPPIMAGES')) {
		# display mobile icon
		$out.= "<button type='button' tabiindex='".$tab++."' style='background:transparent;' class='spIcon' name='preview' id='sfpreview' data-url='".$site."'>\n";
		$out.= SP()->theme->paint_icon('spIcon', SPPPIMAGES, "sp_EditorPreview.png", '');
		$out.= "</button>";

	} else {
		$out.= "<input type='button' tabindex='".$tab++."' class='".$a['controlSubmit']."' title='".__('Preview your Post', 'sp-preview')."' id='sfpreview' name='preview' value='".__('Preview', 'sp-preview')."' data-url='".$site."' />\n";
	}
	return $out;
}

# ------------------------------------------------------
# Adds new preview div
# ------------------------------------------------------
function sp_preview_container($out, $data, $a) {
	$out.= sp_InsertBreak('spacer=3px&echo=false');
	$out.= "<div id='previewPost' style='display:none;'>";
	$out.= '<img src="'.SPCOMMONIMAGES.'working.gif" alt="" />';
	$out.= "</div>";
	return $out;
}

# ------------------------------------------------------
# Load the preview script
# ------------------------------------------------------
function sp_preview_load_js($footer) {
    $script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPPPJS.'sp-preview.js' : SPPPJS.'sp-preview.min.js';
	SP()->plugin->enqueue_script('spprev', $script, array('jquery'), false, $footer);
}
