<?php
/*
Simple:Press Plugin Title: Editor TinyMCE Rich Text
Version: 4.1.0
Item Id: 3907
Plugin URI: https://simple-press.com/downloads/tinymce-editor-plugin/
Description: Rich Text Editor using WP TinyMCE
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

define('SPTMDB', 7);

# ======================================
# EDITOR CONSTANTS
#	Must be one of:
#	RICHTEXT	- 1
#	HTML		- 2
#	BBCODE		- 3
#	TINYMCE		- 4
# ======================================
define('RICHTEXT',		1);
define('RICHTEXTNAME',	'TinyMCE');

# ======================================
# CONSTANTS USED BY THIS PLUGIN
# ======================================
define('SPTMDIR',			SPPLUGINDIR.'tinymce-richtext/');
define('SPTMLIB',			SPPLUGINDIR.'tinymce-richtext/library/');
define('SPTMADMIN',			SPPLUGINDIR.'tinymce-richtext/admin/');
define('SPTMTMSCRIPT',		SPPLUGINURL.'tinymce-richtext/tinymce/');
define('SPTMSPSCRIPT',		SPPLUGINURL.'tinymce-richtext/resources/jscript/');
define('SPTMCSS',			SPPLUGINURL.'tinymce-richtext/resources/css/');
define('SPTMCONTENTSKIN',	SPPLUGINURL.'tinymce-richtext/resources/skins/SPlightgray');
define('SPTMPLUGINS',		SPPLUGINURL.'tinymce-richtext/resources/plugins/');

# ======================================
# ACTIONS/FILTERS USED BY THUS PLUGIN
# ======================================
add_action('sph_activate_tinymce-richtext/sp-tinymce-plugin.php',		'sp_tinymce_install');
add_action('sph_uninstall_tinymce-richtext/sp-tinymce-plugin.php',		'sp_tinymce_uninstall');
add_action('sph_deactivate_tinymce-richtext/sp-tinymce-plugin.php',		'sp_tinymce_deactivate');
add_action('init',														'sp_tinymce_localisation');
add_action('sph_load_editor_support',									'sp_tinymce_load_filters', 1, 1);
add_action('sph_load_editor',											'sp_tinymce_load');
add_action('sph_admin_menu',											'sp_tinymce_admin_menu');
add_action('sph_plugin_update_tinymce-richtext/sp-tinymce-plugin.php',	'sp_tinymce_upgrade');
add_action('admin_footer',												'sp_tinymce_upgrade');
add_action('sph_profile_edit_before',									'sp_tinymce_preload');
add_action('sph_ProfileStart',											'sp_tinymce_reload');

add_filter('sph_plugins_active_buttons',					'sp_tinymce_uninstall_option', 10, 2);
add_filter('sph_editor_textarea',							'sp_tinymce_textarea', 1, 5);
add_filter('sph_admin_help-admin-components',				'sp_tinymce_admin_help', 10, 3);
add_filter('sph_ProfilePostingOptionsFormEditors',			'sp_tinymce_profile_option', 1, 2);
add_filter('sph_acknowledgements',							'sp_tinymce_acknowledge');
add_filter('user_can_richedit',								'sp_tinymce_use_editor');
add_filter('sph_this_editor',								'sp_tinymce_editor', 9999);
add_filter('tiny_mce_before_init',							'sp_tinymce_formats');

# ======================================
# CONTROL FUNCTIONS USED BY THUS PLUGIN
# ======================================

# ----------------------------------------------
# Run Install Script on Activation action
# ----------------------------------------------
function sp_tinymce_install() {
	require_once SPTMDIR.'sp-tinymce-install.php';
	sp_tinymce_do_install();
}

# ----------------------------------------------
# Run Uninstall Script on Uninstall actiopn
# ----------------------------------------------
function sp_tinymce_uninstall() {
	require_once SPTMDIR.'sp-tinymce-uninstall.php';
	sp_tinymce_do_uninstall();
}

function sp_tinymce_deactivate() {
	require_once SPTMDIR.'sp-tinymce-uninstall.php';
	sp_tinymce_do_deactivate();
}

# ------------------------------------------------------
# Add the 'Uninstall' and 'Options' link to plugins list
# ------------------------------------------------------
function sp_tinymce_uninstall_option($actionlink, $plugin) {
	if ($plugin == 'tinymce-richtext/sp-tinymce-plugin.php') {
		$url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
		$actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-tinymce')."'>".__('Uninstall', 'sp-tinymce').'</a>';

		$url = SPADMINCOMPONENTS.'&amp;tab=plugin&amp;admin=sp_tinymce_form&amp;save=sp_tinymce_save&amp;form=1';
		$actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-tinymce')."'>".__('Options', 'sp-tinymce').'</a>';
	}
	return $actionlink;
}

function sp_tinymce_upgrade() {
	require_once SPTMDIR.'sp-tinymce-upgrade.php';
	sp_tinymce_do_upgrade();
}

# ----------------------------------------------
# Set GetText Localisation Domain
# ----------------------------------------------
function sp_tinymce_localisation() {
	sp_plugin_localisation('sp-tinymce');
}

function sp_tinymce_editor($editor) {
	if ($editor == RICHTEXT && !sp_tinymce_use_editor(false)) $editor = 4;
	return $editor;
}

# ----------------------------------------------
# Load the tinymce filter file
# ----------------------------------------------
function sp_tinymce_load_filters($editor) {
	if ($editor == RICHTEXT) require_once SPTMLIB.'sp-tinymce-filters.php';
}

# ----------------------------------------------
# Load and Initialise this Editor if needed
# ----------------------------------------------
function sp_tinymce_load($editor) {
	if ($editor == RICHTEXT) {
		$script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPTMSPSCRIPT.'sp-tinymce.js' : SPTMSPSCRIPT.'sp-tinymce.min.js';
		wp_enqueue_script('tinymce_richtext', $script, array('editor'), false, true);

		# enqueue css - tinymce css not really depenent on jquery ui css, but this forces it to load in head
		# as wp enqueues it when you instantiate the editor which we dont do until much later when needed
		$tinyCSS = SP()->theme->find_css(SPTMCSS, 'sp-tinymce.css');
		wp_enqueue_style('sp-tm', $tinyCSS, array());
	}
}

function sp_tinymce_use_editor($tinymce) {
	global $is_edge, $is_gecko, $is_opera, $is_safari, $is_chrome, $is_IE;

	$tinymce = false;
    if ($is_safari) {
        $tinymce = !wp_is_mobile() || (preg_match('!AppleWebKit/(\d+)!', $_SERVER['HTTP_USER_AGENT'], $match) && intval($match[1]) >= 534);
    } elseif ($is_edge || $is_gecko || $is_chrome || $is_IE || ($is_opera && !wp_is_mobile())) {
        $tinymce = true;
    }

	return $tinymce;
}

# ----------------------------------------------
# Pre Load the wp editor but dont display - forces wp to load some stuff
# ----------------------------------------------
function sp_tinymce_preload() {
	if (SP()->core->forumData['editor'] == RICHTEXT) $dummy = sp_SetupSigEditor(''); # load so tinymce stuff gets loaded
}

# ----------------------------------------------
# Profile signature saved, so need to remove the tinymce editor from DON since it will get added back in later
# ----------------------------------------------
function sp_tinymce_reload($action) {
	if ($action == 'update-sig') return;
?>
	<script>
		(function(spj, $, undefined) {
			if (typeof tinyMCE !== 'undefined') tinyMCE.EditorManager.execCommand('mceRemoveControl', true, 'postitem');
		}(window.spj = window.spj || {}, jQuery));
	</script>
<?php
}
# ----------------------------------------------
# Display Textarea Input control
# ----------------------------------------------
function sp_tinymce_textarea($out, $areaid, $content, $editor, $tab) {
	if ($editor == RICHTEXT) {
		ob_start();
		require_once SPTMLIB.'sp-tinymce-init.php';
		$tm = SP()->options->get('tinymce');
		$settings = array('wpautop' => false, 'media_buttons' => false, 'tinymce' => $tiny, 'dfw' => false, 'quicktags' => false, 'teeny' => false);
		wp_editor($content, $areaid, $settings);
		$out.= ob_get_contents();
		ob_end_clean();
	}
	return $out;
}

function sp_tinymce_formats($init) {
	# only set for profile - signature use
	if (isset(SP()->user->profileUser)) {
		$formats = $init['formats'];
		$additional = ',
			bold: {inline : "span", styles : {"font-weight" : "bold"}},
			italic: {inline : "span", styles : {"font-style" : "italic"}},
			underline: {inline : "span", styles : {"text-decoration" : "underline"}}
			}';
		$formats = substr_replace($formats, $additional, -1);
		$init['formats'] = $formats;
	}
	return $init;
}

# ----------------------------------------------
# Add TinyMCE Admin Panel to Components
# ----------------------------------------------
function sp_tinymce_admin_menu() {
	$subpanels = array(
		__('Editor (TinyMCE)', 'sp-tinymce') => array('admin' => 'sp_tinymce_form', 'save' => 'sp_tinymce_save', 'form' => 1, 'id' => 'sptm')
	);
	SP()->plugin->add_admin_subpanel('components', $subpanels);
}

# ----------------------------------------------
# Load the TinyMCE Options Admin Form
# ----------------------------------------------
function sp_tinymce_form() {
	require_once SPTMADMIN.'sp-tinymce-components-form.php';
	sp_tinymce_options_form();
}

# ----------------------------------------------
# Save the TinyMCE Options Admin Form Data
# ----------------------------------------------
function sp_tinymce_save() {
	require_once SPTMADMIN.'sp-tinymce-components-save.php';
	return sp_tinymce_options_save();
}

# ----------------------------------------------
# Action the Tinymce admin panel popup help
# ----------------------------------------------
function sp_tinymce_admin_help($file, $tag, $lang) {
	if ($tag == '[tinymce-options]' || $tag == '[tinymce-functions]' || $tag == '[tinymce-skins]') $file = SPTMDIR.'admin/sp-tinymce-admin-help.'.$lang;
	return $file;
}

# ----------------------------------------------
# Add tinymce to user profile editor option list
# ----------------------------------------------
function sp_tinymce_profile_option($edOpts, $userProfile) {
	$checked = ($userProfile->editor == RICHTEXT) ? $checked = 'checked="checked" ' : '';
	$edOpts.= '<p class="spProfileRadioLabel"><input type="radio" '.$checked.'name="editor" id="sf-richtext" value="'.RICHTEXT.'"/><label for="sf-richtext"><span>'.__('Rich text', 'sp-tinymce').' - '.RICHTEXTNAME.'</span></label></p>';
	return $edOpts;
}

function sp_tinymce_acknowledge($ack) {
	$ack[] = '<a href="http://www.moxiecode.com/">'.__('TinyMCE text editor by Moxiecode Systems', 'sp-tinymce').'</a>';
	return $ack;
}
