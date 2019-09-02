<?php
/*
Simple:Press Plugin Title: Syntax Highlighting
Version: 3.1.0
Item Id: 3948
Plugin URI: https://simple-press.com/downloads/syntax-highlighting-plugin/
Description:Add program code syntax highlight and colouring to forum and blog posts
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
A plugin for Simple:Press to add syntax highlight-colouring to forum and blog posts.
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# ------------------------------------------------------
# Global Variables
# ------------------------------------------------------

# IMPORTANT DB VERSION
define('SPSYNTAXDBVERSION', 1);

# ------------------------------------------------------
# Constants used by thus plugin
# ------------------------------------------------------
define('SPSHDIR', 		SPPLUGINDIR.'syntax-highlighting/');
define('SPSHADMINDIR',	SPPLUGINDIR.'syntax-highlighting/admin/');
define('SPSHSCRIPT',	SPPLUGINURL.'syntax-highlighting/resources/jscript/');
define('SPSHCSS',		SPPLUGINURL.'syntax-highlighting/resources/css/');

# ------------------------------------------------------
# Actions/Filters used by this plugin
# ------------------------------------------------------
add_action('sph_activate_syntax-highlighting/sp-syntax-plugin.php',		'sp_syntax_install');
add_action('sph_uninstall_syntax-highlighting/sp-syntax-plugin.php',	'sp_syntax_uninstall');
add_action('sph_deactivate_syntax-highlighting/sp-syntax-plugin.php',	'sp_syntax_deactivate');
add_action('sph_plugin_update_syntax-highlighting/sp-syntax-plugin.php','sp_syntax_upgrade_check');
add_action('admin_footer',                                  'sp_syntax_upgrade_check');
add_filter('sph_plugins_active_buttons', 					'sp_syntax_uninstall_option', 10, 2);
add_filter('sph_plugins_active_buttons', 					'sp_syntax_panel_redirect', 10, 2);
add_action('init', 											'sp_syntax_localisation');
add_action('sph_options_content_left_panel', 				'sp_syntax_admin_options');
add_action('sph_option_content_save', 						'sp_syntax_admin_save_options');
add_filter('sph_admin_help-admin-options', 					'sp_syntax_admin_help', 10, 3);
add_filter('sph_acknowledgements', 							'sp_syntax_acknowledgement');
add_action('sph_print_plugin_scripts', 						'sp_syntax_load_js', 9);
add_action('sph_footer_end',								'sp_syntax_initialise');
add_action('sph_post_pm_message',							'sp_syntax_initialise');
add_action('sph_preview_end',								'sp_syntax_initialise');
add_action('wp_enqueue_scripts',							'sp_syntax_load_blog');
add_filter('sph_tinymce_buttons_1',							'sp_syntax_add_tm_button');
add_filter('sph_add_tm_plugin',								'sp_syntax_add_tm_plugin');
add_filter('sph_tm_init',								    'sp_syntax_tm_plugin'); #
add_filter('sph_mce_external_plugins',                      'sp_syntax_setup_tm_plugin');
add_action('sph_load_editor',							    'sp_syntax_load_css');

# Syntax Highlighting - page content level filter
$sfsyntax = SP()->options->get('sfsyntax');
if ($sfsyntax['sfsyntaxforum'] == true && SP()->isForum == true) {
	add_filter('the_content', 								'sp_syntax_filter_display', 0);
}
if ($sfsyntax['sfsyntaxblog'] == true && SP()->isForum == false) {
	add_filter('the_content', 								'sp_syntax_filter_display', 0);
	add_filter('the_excerpt', 								'sp_syntax_filter_display', 0);
	add_filter('comment_text', 								'sp_syntax_filter_display', 0);
}

# --------------------------------------
# Control functions used by thus plugin
# --------------------------------------

# ------------------------------------------------------
# Activate and install
# ------------------------------------------------------
function sp_syntax_install() {
	require_once SPSHDIR.'sp-syntax-install.php';
	sp_syntax_do_install();
}

# ------------------------------------------------------
# Uninstall and Deactivate
# ------------------------------------------------------
function sp_syntax_uninstall() {
	require_once SPSHDIR.'sp-syntax-uninstall.php';
	sp_syntax_do_uninstall();
}

function sp_syntax_deactivate() {
	require_once SPSHDIR.'sp-syntax-uninstall.php';
	sp_syntax_do_deactivate();
}

function sp_syntax_upgrade_check() {
	require_once SPSHDIR.'sp-syntax-install.php';
	sp_syntax_do_upgrade();
}

# ------------------------------------------------------
# Add the 'Uninstall' option to plugins list
# ------------------------------------------------------
function sp_syntax_uninstall_option($actionlink, $plugin) {
	if ($plugin == 'syntax-highlighting/sp-syntax-plugin.php') {
		$url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
		$actionlink.= "&nbsp;&nbsp;<a href='".$url."' title='".__("Uninstall this plugin", "sp-syntax")."'>".__("Uninstall", "sp-syntax")."</a>";
	}
	return $actionlink;
}

# ------------------------------------------------------
# Create 'Options' link in plugin list
# ------------------------------------------------------
function sp_syntax_panel_redirect($actionlink, $plugin) {
	if ($plugin == 'syntax-highlighting/sp-syntax-plugin.php') {
		$url = SPADMINOPTION.'&amp;tab=content';
		$actionlink.= "&nbsp;&nbsp;<a href='".$url."' title='".__("Options", "sp-syntax")."'>".__("Options", "sp-syntax")."</a>";
	}
	return $actionlink;
}

# ------------------------------------------------------
# Set up language file
# ------------------------------------------------------
function sp_syntax_localisation() {
	sp_plugin_localisation('sp-syntax');
}

# ------------------------------------------------------
# Create Admin Options panel; or section
# ------------------------------------------------------
function sp_syntax_admin_options() {
	require_once SPSHADMINDIR.'sp-syntax-admin-options.php';
	sp_syntax_admin_options_form();
}

# ------------------------------------------------------
# Save routines for new admin options
# ------------------------------------------------------
function sp_syntax_admin_save_options() {
	require_once SPSHADMINDIR.'sp-syntax-admin-options-save.php';
	sp_syntax_admin_options_save();
}

# ------------------------------------------------------
# Create admin popup help for new options
# ------------------------------------------------------
function sp_syntax_admin_help($file, $tag, $lang) {
	if ($tag == '[syntax-highlighting]') $file = SPSHADMINDIR.'sp-syntax-admin-help.'.$lang;
	return $file;
}

# ------------------------------------------------------
# Add an entry to the Acknowledgements popup
# ------------------------------------------------------
function sp_syntax_acknowledgement($ack) {
	$ack[] = '<a href="http://www.oriontransfer.co.nz/software/jquery-syntax/">'.__("Program Code Syntax Highlighting by Samuel Williams", "sp-syntax").'</a>';
	return $ack;
}

# ------------------------------------------------------
# Add scripts to forum header if needed
# ------------------------------------------------------
function sp_syntax_load_js($footer) {
    $sfsyntax = SP()->options->get('sfsyntax');
    if ($sfsyntax['sfsyntaxforum'] == true || $sfsyntax['sfsyntaxblog'] == true) {
        $script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPSHSCRIPT.'jquery.syntax.js' : SPSHSCRIPT.'jquery.syntax.min.js';
        SP()->plugin->register_script('sp-syntax', $script, array('jquery'), false, $footer);
        SP()->plugin->register_script('sp-syntax-cache', SPSHSCRIPT.'jquery.syntax.cache.js', array('jquery', 'sp-syntax'), false, $footer);

    	SP()->plugin->enqueue_script('sp-syntax');
        SP()->plugin->enqueue_script('sp-syntax-cache');
    }
}

# ----------------------------------------------
# Load and Initialise this Editor if needed
# ----------------------------------------------
function sp_syntax_load_css($editor) {
	if ($editor == RICHTEXT) {
        $shcss = SP()->theme->find_css(SPSHCSS, 'sp-syntax.css');
        wp_enqueue_style('sp-syntax', $shcss, array());
	}
}
# ------------------------------------------------------
# Initialise syntax highlighting in forum
# ------------------------------------------------------
function sp_syntax_initialise() {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	$syntax_check = apply_filters('sph_syntax_check', 'topic');
	if (strpos($syntax_check, SP()->rewrites->pageData['pageview']) !== false) {
		# syntax Highlighting
		$sfsyntax = SP()->options->get('sfsyntax');
		if ($sfsyntax['sfsyntaxforum'] == true) { ?>
			<script>
				(function(spj, $, undefined) {
					$(document).ready(function($) {
						Syntax.root = "<?php echo SPSHSCRIPT; ?>";
						$.syntax({layout: 'table', replace: true});
					});
				}(window.spj = window.spj || {}, jQuery));
			</script>
		<?php }
	}
}

# ------------------------------------------------------
# Add scripts to blog header if needed
# ------------------------------------------------------
function sp_syntax_load_blog($footer) {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	if (is_admin() || SP()->isForum == true) return;

	# syntax Highlighting
	$sfsyntax = SP()->options->get('sfsyntax');
	if ($sfsyntax['sfsyntaxblog'] == true) {
        $script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPSHSCRIPT.'jquery.syntax.js' : SPSHSCRIPT.'jquery.syntax.min.js';
		wp_enqueue_script('sp-syntax', $script, array('jquery'), false, $footer);
		wp_enqueue_script('sp-syntax-cache', SPSHSCRIPT.'jquery.syntax.cache.js', array('jquery', 'sp-syntax'), false, $footer);

		# inline js to showsynyax hghlighting on blog post
		add_action( 'wp_footer', 'sp_syntax_initialise_blog' );
	}
}

# ------------------------------------------------------
# inline syntax hghlighting n blog post
# ------------------------------------------------------
function sp_syntax_initialise_blog() {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	?>
	<script>
		(function(spj, $, undefined) {
			$(document).ready(function($) {
				Syntax.root = "<?php echo SPSHSCRIPT; ?>";
				$.syntax({layout: 'table', replace: true})
			});
		}(window.spj = window.spj || {}, jQuery));
	</script>
	<?php
}

# ------------------------------------------------------------------
# Add the syntax button to the tm toolbar
# ------------------------------------------------------------------
function sp_syntax_add_tm_button($buttons) {
    # add the syntax button at end if user has not located it self
    if (strpos($buttons, 'syntax') === false) {
    	$buttons.= ' syntax';
    }
	return $buttons;
}

# ------------------------------------------------------------------
# Add the syntax plugin to the tm plugins list
# ------------------------------------------------------------------
function sp_syntax_add_tm_plugin($plugins) {
	# add syntax plugin
	$plugins.= ' syntax';
	return $plugins;
}

# ------------------------------------------------------------------
# Add the brushes list for tm processing
# ------------------------------------------------------------------
function sp_syntax_tm_plugin($tiny) {
	$sfsyntax = SP()->options->get('sfsyntax');
	if ($sfsyntax['sfsyntaxforum'] == true) $brushes = $sfsyntax['sfbrushes'];
	if (isset($brushes)) $tiny['brushes'] = $brushes;
    return $tiny;
}

function sp_syntax_setup_tm_plugin($plugins){
    $plugins['syntax'] = SPSHSCRIPT.'syntax/syntax_plugin.js';
    return $plugins;
}


# ------------------------------------------------------------------
# Syntax Highlighting display - page level filter
# ------------------------------------------------------------------
function sp_syntax_filter_display($content) {
	$result = preg_replace_callback('/<pre(.*?)>(.*?)<\/pre>/imsu', 'sp_syntax_htmlentities', $content);
	return $result;
}

function sp_syntax_htmlentities ($match) {
	$attrs = $match[1];
	if (preg_match("/escaped/", $attrs)) {
		$code = $match[2];
	} else {
		$code = htmlentities($match[2]);
	}
	return "<pre$attrs>$code</pre>";
}
