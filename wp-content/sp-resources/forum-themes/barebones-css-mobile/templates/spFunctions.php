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
add_action('init', 'spBarebones_mobile_textdomain');
function spBarebones_mobile_textdomain() {
	sp_theme_localisation('spBarebones');
}

# only show a few editor toolbar buttons on phone
add_filter('sph_tinymce_buttons_1', 'spBarebones_editor_buttons_1_mobile', 999);
function spBarebones_editor_buttons_1_mobile($buttons) {
    
    if (SP()->core->device == 'mobile') $buttons = 'bold,italic,underline,blockquote,link,unlink,image,code,wp_adv';
    if (SP()->core->device == 'tablet') $buttons = 'bold,italic,underline,|,bullist,numlist,|,blockquote,|,link,unlink,|,image,media,|,spoiler,ddcode,|,code,spellchecker,|,wp_adv';
    return $buttons;
}

add_filter('sph_tinymce_buttons_2', 'spBarebones_editor_buttons_2_mobile');
function spBarebones_editor_buttons_2_mobile($buttons) {
    
    if (SP()->core->device == 'mobile') $buttons = 'formatselect,fontsizeselect,spellchecker';
    if (SP()->core->device == 'tablet') $buttons = 'formatselect,fontsizeselect,|,strikethrough,forecolor,justifyleft,justifycenter,justifyright,justifyfull,|,charmap,removeformat,selectall';
    return $buttons;
}

# remove font resizer on mobile display if in use on site
add_action('sph_BeforeDisplayStart', 'spBarebones_remove_resize_mobile', 5);
function spBarebones_remove_resize_mobile() {
	remove_action('sph_BeforeDisplayStart', 'sp_resize_show');
}

add_filter('sph_ProfileDisplayOptionsForm', 'sp_add_switcher_to_profile_mobile', 999, 2);
function sp_add_switcher_to_profile_mobile($out) {

	$out.= sp_InsertBreak('echo=0');
	$out.= '<hr>';

	if (function_exists('sp_UserSelectOptions')) {
		$out.= sp_UserSelectOptions('tagClass=spCenter spLabelSmall&echo=0&get=1', __sp('Style:'), __sp('Language:'));
	}
	return $out;
}

?>
