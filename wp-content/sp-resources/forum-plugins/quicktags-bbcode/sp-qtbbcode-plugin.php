<?php
/*
Simple:Press Plugin Title: Editor QuickTags BBCode
Version: 2.1.0
Item Id: 3942
Plugin URI: https://simple-press.com/downloads/bbcode-editor-plugin/
Description: BBCode Editor using QuickTags
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-10-23 11:00:44 -0500 (Tue, 23 Oct 2018) $
$Rev: 15764 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# ======================================
# EDITOR CONSTANTS
#	Must be one of:
#	RICHTEXT 	- 1
#	BBCODE		- 2
#	BBCODE		- 3
# ======================================
define('BBCODE',			3);
define('BBCODENAME',		'QuickTags');

# ======================================
# CONSTANTS USED BY THIS PLUGIN
# ======================================
define('SPQTBDIR',		SPPLUGINDIR.'quicktags-bbcode/');
define('SPQTBEDSCRIPT',	SPPLUGINURL.'quicktags-bbcode/qtbbcode/');
define('SPQTBRESOURCES',SPPLUGINURL.'quicktags-bbcode/resources/');

# ======================================
# ACTIONS/FILTERS USED BY THUS PLUGIN
# ======================================
add_action('sph_activate_quicktags-bbcode/sp-qtbbcode-plugin.php',	'sp_qtbbcode_install');
add_action('init',													'sp_qtbbcode_localisation');
add_action('sph_load_editor_support', 								'sp_qtbbcode_load_filters', 1, 1);

add_action('sph_print_plugin_scripts', 							    'sp_qtbbcode_load_js');

add_filter('sph_editor_textarea',									'sp_qtbbcode_textarea', 1, 5);
add_filter('sph_ProfilePostingOptionsFormEditors',					'sp_qtbbcode_profile_option', 1, 2);

# ======================================
# CONTROL FUNCTIONS USED BY THUS PLUGIN
# ======================================

# ----------------------------------------------
# Run Install Script on Activation action
# ----------------------------------------------
function sp_qtbbcode_install() {
	require_once SPQTBDIR.'sp-qtbbcode-install.php';
	sp_qtbbcode_do_install();
}

# ----------------------------------------------
# Set GetText Localisation Domain
# ----------------------------------------------
function sp_qtbbcode_localisation() {
	sp_plugin_localisation('sp-qtbbcode');
}

# ----------------------------------------------
# Load the qt html filter file
# ----------------------------------------------
function sp_qtbbcode_load_filters($editor) {
	if ($editor == BBCODE) {
		require_once SPQTBDIR.'library/sp-qtbbcode-filters.php';
	}
}

# ----------------------------------------------
# Load and Initialise this Editor if needed
# ----------------------------------------------
function sp_qtbbcode_load_js($editor) {
	if ($editor == BBCODE) {
		require_once SPQTBDIR.'qtbbcode/sp-qtbbcode-init.php';
	}
}

# ----------------------------------------------
# Display Textarea Input control
# ----------------------------------------------
function sp_qtbbcode_textarea($out, $areaid, $content, $editor, $tab) {
	if ($editor == BBCODE) {
		$out.= '<div id="spQuickTags">'."\n";
		$out.= '<img class="spRight" src="'.SPQTBRESOURCES.'images/qtbbcode.gif" alt="" />';
		$out.= '<div id="ed_toolbar" class="editor_toolbar"></div>';
		$out.= '<script>edToolbar();</script><textarea class="spQtEditor spControl spLeft" name="'.$areaid.'" id="'.$areaid.'" rows="12">'.$content.'</textarea>';
		$out.= '<script>var edCanvas = document.getElementById("'.$areaid.'");</script>'."\n";
		$out.= '</div>'."\n";
		$out.= '<div class="spClear"></div>';
	}
	return $out;
}

# ----------------------------------------------
# Add bbCode to user profile editor option list
# ----------------------------------------------
function sp_qtbbcode_profile_option($edOpts, $userProfile) {
	$checked = ($userProfile->editor == BBCODE) ? $checked = 'checked="checked" ' : '';
	$edOpts.= '<p class="spProfileRadioLabel"><input type="radio" '.$checked.'name="editor" id="sf-bbcode" value="'.BBCODE.'"/><label for="sf-bbcode"><span>'.__("bbCode", 'sp-qtbbcode').' - '.BBCODENAME.'</span></label></p>';
	return $edOpts;
}
