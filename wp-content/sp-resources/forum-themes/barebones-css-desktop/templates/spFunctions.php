<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Theme custom function file
#	Theme		:	Barebones
#	File		:	custom functions
#	Author		:	Simple:Press
#
#	The 'functions' file can be used for custom functions & is loaded with each template
#
# --------------------------------------------------------------------------------------

# ------------------------------------------------------------------------------------------
# A small javascript routine has been used to replace standard browser tooltips with
# more appealing graphics. You can turn this off by setting SP_TOOLTIPS to false.

if (!defined('SP_TOOLTIPS')) define('SP_TOOLTIPS', true);

# ------------------------------------------------------------------------------------------

# add version 2 theme flag
add_theme_support('level-2-theme');
# register theme as mobile responsive
add_theme_support('sp-theme-responsive');
# add support for glyphs
add_theme_support('sp-theme-glyphs');

# load the theme textdomain for tranlations
add_action('init', 'spBarebones_desktop_textdomain');
function spBarebones_desktop_textdomain() {
	sp_theme_localisation('spBarebones');
}

add_filter('sph_ProfileDisplayOptionsForm', 'sp_add_switcher_to_profile_desktop', 999, 2);
function sp_add_switcher_to_profile_desktop($out) {

	$out.= sp_InsertBreak('echo=0');
	$out.= '<hr>';

	if (function_exists('sp_UserSelectOptions')) {
		$out.= sp_UserSelectOptions('tagClass=spCenter spLabelSmall&echo=0&get=1', __sp('Style:'), __sp('Language:'));
	}
	return $out;
}

?>
