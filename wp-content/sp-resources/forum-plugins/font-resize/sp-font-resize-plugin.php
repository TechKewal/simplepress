<?php
/*
Simple:Press Plugin Title: Font Resizer
Version: 2.1.0
Item Id: 3961
Plugin URI: https://simple-press.com/downloads/font-resize-plugin/
Description: Add font resizing links to the forum display
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
A plugin for Simple:Press to add font resizing links to the forum display for users to change the size
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# ------------------------------------------------------
# Constants used by thus plugin
# ------------------------------------------------------
define('SPFRJS', 	SPPLUGINURL.'font-resize/resources/jscript/');
define('SPFRCSS', 	SPPLUGINURL.'font-resize/resources/css/');

# ------------------------------------------------------
# Actions/Filters used by this plugin
# ------------------------------------------------------
add_action('sph_print_plugin_styles',   'sp_resize_css');
add_action('sph_print_plugin_scripts', 	'sp_resize_load_js');
add_action('sph_footer_end',	        'sp_resize_initialise');
add_action('sph_BeforeDisplayStart',    'sp_resize_show');

function sp_resize_show() {
	$tipMinus = __sp('decrease forum font size');
	$tipReset = __sp('reset forum font size');
	$tipPlus  = __sp('increase font size');
	sp_FontResizer('tagClass=spFontSizeControl spRight', $tipMinus, $tipReset, $tipPlus);
	sp_InsertBreak('direction-right');
}

# ------------------------------------------------------
# Set up the css link
# ------------------------------------------------------
function sp_resize_css() {
	$css = SP()->theme->find_css(SPFRCSS, 'sp-fontresize.css');
    SP()->plugin->enqueue_style('sp-font-resize', $css);
}

# ------------------------------------------------------
# Add scripts to forum header if needed
# ------------------------------------------------------
function sp_resize_load_js($footer) {
    $script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPFRJS.'sp-fontresize.js' : SPFRJS.'sp-fontresize.min.js';
	SP()->plugin->enqueue_script('spfontsize', $script, array('jquery'), false, $footer);
}

# ------------------------------------------------------
# Initialise syntax highlighting in forum
# ------------------------------------------------------
function sp_resize_initialise() {
	if (SP()->isForum) {
?>
		<script>
			(function(spj, $, undefined) {
				$(document).ready(function(){
					$("#spFontSize").fontResize();
				});
			}(window.spj = window.spj || {}, jQuery));
		</script>
<?php
	}
}

# ------------------------------------------------------
# sp_FontResizer()
# Template Function
# id preset to 'spFontSize'
# ------------------------------------------------------
function sp_FontResizer($args='', $toolTipMinus='', $toolTipReset='', $toolTipPlus='', $label='') {
	$defs = array('tagClass'	=> 'spFontSizeControl',
				  'boxClass'	=> 'spButton',
				  'fontChar'	=> 'A',
				  'echo'		=> 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_FontResizer_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagClass	= esc_attr($tagClass);
	$boxClass	= esc_attr($boxClass);
	$fontChar	= esc_attr($fontChar);
	$echo		= (int) $echo;

	$toolTipMinus	= SP()->displayFilters->title($toolTipMinus);
	$toolTipReset	= SP()->displayFilters->title($toolTipReset);
	$toolTipPlus	= SP()->displayFilters->title($toolTipPlus);

	if (!empty($label)) $label = SP()->displayFilters->title($label);

    $out = '';
	$out.= "<span id='spFontSize' class='$tagClass'>\n";
	if (!empty($label)) $out.= "<span>".SP()->displayFilters->title($label).': </span>';
	$out.= "<span id='spFontSizeButtons' class='$tagClass'>\n";
	$out.= "<a id='spFontSize_minus' class='$boxClass' title='$toolTipMinus'>$fontChar</a>\n";
	$out.= "<a id='spFontSize_reset' class='$boxClass' title='$toolTipReset'>$fontChar</a>\n";
	$out.= "<a id='spFontSize_add' class='$boxClass' title='$toolTipPlus'>$fontChar</a>\n";
	$out.= "</span>\n";
	$out.= "</span>\n";

	$out = apply_filters('sph_FontResizer', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}
