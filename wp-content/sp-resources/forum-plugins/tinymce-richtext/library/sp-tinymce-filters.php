<?php
/*
Simple:Press
TinyMCE Editor plugin content filters
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# ----------------------------------------------
# Prepare content for an edit action
# ----------------------------------------------
function sp_editor_prepare_edit_content($content, $editor) {
	return $content;
}

# ----------------------------------------------
# Save Filter - Parse for codetags
# ----------------------------------------------
function sp_editor_parse_codetags($content, $editor, $action) {
	if ($editor == RICHTEXT) {
	   $content = sp_RTE2Html(' '.$content);
    }
   	return $content;
}

# ----------------------------------------------
# Save Filter - Save codetags and callback
# ----------------------------------------------
function sp_editor_save_codetags($content, $editor) {
	if ($editor == RICHTEXT) {
    	$content = preg_replace_callback('/\<div class=\"sfcode\"\>(.*?)\<\/div\>/ms', 'sp_codetag_callback', stripslashes($content));
    }
	return $content;
}

function sp_codetag_callback($s) {
   	$content = str_replace('<br />', '', $s[1]);
   	$content = str_replace("\n", '', $content);
   	$content = '<div class="sfcode">'.$content.'</div>';
   	return $content;
}

# ----------------------------------------------
# Save Filter - Save linebreaks filter
# ----------------------------------------------
function sp_editor_save_linebreaks($content, $editor) {
	return $content;
}

# ----------------------------------------------
# Edit Filter - Prepare p and br tags for edit
# ----------------------------------------------
function sp_editor_format_paragraphs_edit($content, $editor) {
	if ($editor == RICHTEXT) {
    	$content = SP()->displayFilters->paragraphs($content);
    }
	return $content;
}

# ----------------------------------------------
# Edit Filter - Not needed for tinymce
# ----------------------------------------------
function sp_editor_parse_for_edit($content, $editor) {
	return $content;
}

# ----------------------------------------------
# Parser: Rich Text to HTML
# ----------------------------------------------
function sp_RTE2Html($text) {
	$text = trim($text);

	# RTE-TM-Code
	if (!function_exists('rtetohtml_escape')) {
		function rtetohtml_escape($s) {
			global $text;
			return '<div class="sfcode">'.str_replace('"', '&quot;', $s[1]).'</div>';
		}
	}
	$text = preg_replace_callback('/\<div class=\"sfcode\"\>(.*?)\<\/div\>/ms', 'rtetohtml_escape', $text);
	return $text;
}
