<?php
# --------------------------------------------------------------------------------------
#
#	Simple:Press Theme custom function file
#	Theme		:	mytheme
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

# add support for child overlays
add_theme_support('sp-theme-child-overlays');
# register theme as mobile responsive
add_theme_support('sp-theme-responsive');
# add support for glyphs
add_theme_support('sp-theme-glyphs');
# add customiser support
switch (SP()->core->device) {
	case 'mobile':
		$t = SP()->options->get('sp_mobile_theme');
	break;
	case 'tablet':
		$t = SP()->options->get('sp_tablet_theme');
	break;
	case 'desktop':
		$t = SP()->options->get('sp_current_theme');
}
if($t['color'] == 'custom') {
	add_theme_support('sp-theme-customiser');
	# load customiser control
	if (!defined('SPBBADMIN')) define('SPBBADMIN', SPTHEMEBASEDIR.'barebones/admin/');
	require_once SPBBADMIN.'spAdmin.php';
}

# load the theme textdomain for tranlations
add_action('init', 'spmytheme_textdomain');
function spmytheme_textdomain() {
	sp_theme_localisation('spmytheme');
}

# only show a few editor toolbar buttons on phone
add_filter('sph_tinymce_buttons_1', 'spmytheme_editor_buttons_1', 999);
function spmytheme_editor_buttons_1($buttons) {
    if (SP()->core->device == 'mobile') $buttons = 'bold,italic,underline,blockquote,link,unlink,image,code,wp_adv';
    if (SP()->core->device == 'tablet') $buttons = 'bold,italic,underline,|,bullist,numlist,|,blockquote,|,link,unlink,|,image,media,|,spoiler,ddcode,|,code,spellchecker,|,wp_adv';
    return $buttons;
}

add_filter('sph_tinymce_buttons_2', 'spmytheme_editor_buttons_2');
function spmytheme_editor_buttons_2($buttons) {
    if (SP()->core->device == 'mobile') $buttons = 'formatselect,fontsizeselect,spellchecker';
    if (SP()->core->device == 'tablet') $buttons = 'formatselect,fontsizeselect,|,strikethrough,forecolor,justifyleft,justifycenter,justifyright,justifyfull,|,charmap,removeformat,selectall';
    return $buttons;
}

# remove font resizer on mobile display if in use on site
add_action('sph_BeforeDisplayStart', 'spmytheme_remove_resize', 5);
function spmytheme_remove_resize() {
    if (SP()->core->device == 'mobile') {
    	remove_action('sph_BeforeDisplayStart', 'sp_resize_show');
    }
}

add_filter('sph_ProfileDisplayOptionsForm', 'sp_add_switcher_to_profile', 999, 2);
function sp_add_switcher_to_profile($out) {

	$out.= sp_InsertBreak('echo=0');
	$out.= '<hr>';

	if (function_exists('sp_UserSelectOptions')) {
		$out.= sp_UserSelectOptions('tagClass=spCenter spLabelSmall&echo=0&get=1', __sp('Style:'), __sp('Language:'));
	}
	return $out;
}

# ------------------------------------
# AJAX call for working the customiser
# ------------------------------------

function sp_ajax_displayforumcustom() {
	include SPTHEMEBASEDIR.'barebones/admin/sp-barebones-options-ahah.php';
}
add_action('wp_ajax_display-forum-custom', 'sp_ajax_displayforumcustom');
add_action('wp_ajax_nopriv_display-forum-custom', 'sp_ajax_displayforumcustom');

function get_breadcrumb() {
	
	if( is_page() && is_front_page()){
	echo '<img src="'.site_url().'/wp-content/sp-resources/forum-themes/mytheme/images/Icons/Home.svg" alt="spHome" class="spHomeIcon"> &nbsp;&nbsp;&#187;&nbsp;&nbsp; <a href="'.home_url().'" rel="nofollow">Simple:Press Support Forum</a>';
	}else{

	}
	if (is_category() || is_single()) {
        echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
        the_category(' &bull; ');
            if (is_single()) {
                echo " &nbsp;&nbsp;&#187;&nbsp;&nbsp; ";
                the_title();
            }
    } elseif (is_page() && !is_front_page()) {
        echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
        echo the_title();
    } elseif (is_search()) {
        echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;Search Results for... ";
        echo '"<em>';
        echo the_search_query();
        echo '</em>"';
    }
}
function cutome_title(){
	$title = get_the_title();
	$ext_title = explode('|', $title);
	return '<h2 class="entry-title"><a href="'.esc_url( get_permalink()).'" rel="bookmark">'.$ext_title[0].'</a></h2>';
}
// jQuery('#sp_FontResizerCustom').on('change', function(){
// 	console.log(jQuery('#sp_FontResizerCustom').val());
// });

// function spFontSize_Change(){
// 	console.log(jQuery('#sp_FontResizerCustom').val());
// }