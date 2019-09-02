<?php
/*
Simple=>Press
tinymce init
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# Load up toolbars and plugins
$tm = SP()->options->get('tinymce');

$contentCSS = SPTMCONTENTSKIN.'/sp-content.css';

$focus = (isset(SP()->rewrites->pageData['pageview']) && isset(SP()->rewrites->pageData['hiddeneditor']) && SP()->rewrites->pageData['hiddeneditor'] && SP()->rewrites->pageData['pageview'] == 'topic') ? 'postitem' : '';

$plugins = apply_filters('sph_add_tm_plugin', $tm['plugins']);

# low priority on our button adding so it runs last and only our buttons are used
# other plugins buttons can still be added to our button list in the configuration settings
add_filter('mce_buttons',   'sph_tinymce_buttons_1', 999999, 2);
add_filter('mce_buttons_2', 'sph_tinymce_buttons_2', 999999, 2);
add_filter('mce_buttons_3', 'sph_tinymce_buttons_empty', 999999, 2);
add_filter('mce_buttons_4', 'sph_tinymce_buttons_empty', 999999, 2);

$tiny = array(
	'mode' => 'none',
	'elements' => 'postitem',
    'content_css' => $contentCSS,
    'skin_url' => SPTMCONTENTSKIN,
    'skin' => 'SPlightgray',
	'height'=> $tm['height'],
	'extended_valid_elements' => 'img[accesskey|class|contextmenu|data-upload|data-width|data-height|dir|draggable|dropzone|hidden|id|inert|itemid|itemprop|itemref|itemscope|itemtype|lang|spellcheck|style|tabindex|title|translate|item|role|subject|alt|src|srcset|crossorigin|usemap|ismap|width|height]',
	'plugins' => $plugins,
    'auto_focus' => $focus,
	'paste_block_drop' => true,
    );
$tiny = apply_filters('sph_tm_init', $tiny);

# add in external plugins
add_filter('mce_external_plugins', 'add_tinymce_plugins');
function add_tinymce_plugins($plugins) {
    # add in our core plugins
	$plugins['spoiler'] = SPTMPLUGINS.'spoiler/spoiler_plugin.js';
	$plugins['sphelp'] = SPTMPLUGINS.'sphelp/sphelp_plugin.js';
	$plugins['code'] = SPTMPLUGINS.'code/plugin.min.js';
	$plugins['link'] = SPTMPLUGINS.'link/plugin.min.js';

    # filter for users to add external tinyMCE plugin
    $external_plugins = apply_filters('sph_mce_external_plugins', array());
    foreach ($external_plugins as $name => $url) {
        $plugins[$name] = $url;
    }
    return $plugins;
}

add_filter('sph_tinymce_buttons_1', 'sp_check_spoiler_button');
function sp_check_spoiler_button($buttons) {
	if (empty(SP()->rewrites->pageData['forumid']) || !SP()->auths->get('use_spoilers', SP()->rewrites->pageData['forumid'])) {
		$buttons = str_replace('spoiler,', '', $buttons);
		$buttons = str_replace('spoiler', '', $buttons);
	}
	return $buttons;
}

function sph_tinymce_buttons_1($buttons, $editor) {
    if ($editor == 'postitem') {
        $tm = SP()->options->get('tinymce');
        $buttons = apply_filters('sph_tinymce_buttons_1', $tm['buttons1']);
        $buttons = (!empty($buttons)) ? explode(',', $buttons) : array();
    }

    # remove some buttons on profiles since space is tighter - there is filter if users wants different ones or add back in
    if (isset(SP()->user->profileUser)) {
        $removeButtons = apply_filters('sph_tinymce_profile_buttons_remove', array('spoiler', 'charmap', 'blockquote', 'pastetext'));
        $buttons = str_replace($removeButtons, '', $buttons);
    }

    return $buttons;
}

function sph_tinymce_buttons_2($buttons, $editor) {
    if ($editor == 'postitem') {
        $tm = SP()->options->get('tinymce');
        $buttons = apply_filters('sph_tinymce_buttons_2', $tm['buttons2']);
        $buttons = (!empty($buttons)) ? explode(',', $buttons) : array();
    }
    return $buttons;
}

# return empty button rows for 3 and 4
function sph_tinymce_buttons_empty($buttons, $editor) {
    if ($editor == 'postitem') $buttons = array();
    return $buttons;
}
