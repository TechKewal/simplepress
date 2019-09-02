<?php
/*
Simple:Press
Quicktags BBCODE Editor plugin content filters
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
function sp_editor_parse_codetags($content, $editor) {
	if ($editor == BBCODE) {
		$content = addslashes(sp_BBCode2Html(" ".stripslashes($content), false));
	}
	return $content;
}

# ----------------------------------------------
# Save Filter - Save codetags and callback
# ----------------------------------------------
function sp_editor_save_codetags($content, $editor) {
	return $content;
}

# ----------------------------------------------
# Save Filter - Save linebreaks filter
# ----------------------------------------------
function sp_editor_save_linebreaks($content, $editor) {
	if ($editor == BBCODE) 	{
		$content = SP()->saveFilters->linebreaks($content);
	}
	return $content;
}

# ----------------------------------------------
# Edit Filter - Prepare p and br tags for edit
# ----------------------------------------------
function sp_editor_format_paragraphs_edit($content, $editor) {
	return $content;
}

# ----------------------------------------------
# Edit Filter - Parse bbcode - to raw text
# ----------------------------------------------
function sp_editor_parse_for_edit($content, $editor) {
	if ($editor == BBCODE) {
		$content = sp_Html2BBCode($content);
	}
	return $content;
}

# ----------------------------------------------
# Parser: bbcode/html text
# ----------------------------------------------
function sp_BBCode2Html($text, $dobr=true) {
	$text = trim($text);

	# BBCode [code]
	if (!function_exists('bbtohtml_escape')) {
		function bbtohtml_escape($s) {
			global $text;
			$text = strip_tags($text);
			return '<code>'.htmlspecialchars($s[1], ENT_QUOTES, SPCHARSET).'</code>';
		}
	}
	$text = preg_replace_callback('/\[code\](.*?)\[\/code\]/ms', "bbtohtml_escape", $text);

	# BBCode to find...
	$in = array('/\[b\](.*?)\[\/b\]/ms',
				'/\[i\](.*?)\[\/i\]/ms',
				'/\[u\](.*?)\[\/u\]/ms',
                '/\[left\](.*?)\[\/left\]/ms',
                '/\[right\](.*?)\[\/right\]/ms',
                '/\[center\](.*?)\[\/center\]/ms',
				'/\[img\](.*?)\[\/img\]/ms',
				'/\[url\="?(.*?)"?\](.*?)\[\/url\]/is',
   			    '/\[url\](.*?)\[\/url\]/is',
				'/\[quote\](.*?)\[\/quote\]/ms',
				'/\[quote\="?(.*?)"?\](.*?)\[\/quote\]/ms',
				'/\[list\=(.*?)\](.*?)\[\/list\]/ms',
				'/\[list\](.*?)\[\/list\]/ms',
				'/\[B\](.*?)\[\/B\]/ms',
				'/\[I\](.*?)\[\/I\]/ms',
				'/\[U\](.*?)\[\/U\]/ms',
                '/\[LEFT\](.*?)\[\/LEFT\]/ms',
                '/\[RIGHT\](.*?)\[\/RIGHT\]/ms',
                '/\[CENTER\](.*?)\[\/CENTER\]/ms',
				'/\[IMG\](.*?)\[\/IMG\]/ms',
				'/\[COLOR=(.*?)](.*?)\[\/COLOR]/is',
				'/\[URL\="?(.*?)"?\](.*?)\[\/URL\]/is',
				'/\[QUOTE\](.*?)\[\/QUOTE\]/ms',
				'/\[QUOTE\="?(.*?)"?\](.*?)\[\/QUOTE\]/ms',
				'/\[POSTQUOTE\](.*?)\[\/POSTQUOTE\]/ms',
				'/\[LIST\=(.*?)\](.*?)\[\/LIST\]/ms',
				'/\[LIST\](.*?)\[\/LIST\]/ms',
				'/\[\*\]\s?(.*?)\n/ms'
	);

	# And replace them by...
	$out = array('<strong>\1</strong>',
				'<em>\1</em>',
				'<u>\1</u>',
                '<div style="text-align:left">\1</div>',
                '<div style="text-align:right">\1</div>',
                '<div style="text-align:center">\1</div>',
				'<img src="\1" alt="\1" />',
				'<a href="\1">\2</a>',
   	  		    '\1',
				'<blockquote>\1</blockquote>',
				'<blockquote>\1 said:<br />\2</blockquote>',
				'<ol start="\1">\2</ol>',
				'<ul>\1</ul>',
				'<strong>\1</strong>',
				'<em>\1</em>',
				'<u>\1</u>',
                '<div style="text-align:left">\1</div>',
                '<div style="text-align:right">\1</div>',
                '<div style="text-align:center">\1</div>',
				'<img src="\1" alt="\1" />',
				'<span style="color: \1">\2</span>',
				'<a href="\1">\2</a>',
				'<blockquote>\1</blockquote>',
				'<blockquote>\1 said:<br />\2</blockquote>',
				'<blockquote class="spPostEmbedQuote">\1</blockquote>',
				'<ol start="\1">\2</ol>',
				'<ul>\1</ul>',
				'<li>\1</li>'
	);
	$text = preg_replace($in, $out, $text);

	# special case for nested quotes
	$text = str_replace('[quote]', '<blockquote>', $text);
	$text = str_replace('[/quote]', '</blockquote>', $text);

	# paragraphs
	if ($dobr) {
		$text = str_replace("\r", "", $text);

		# clean some tags to remain strict
		if (!function_exists('bbtohtml_removeBr')) {
			function bbtohtml_removeBr($s) {
				return str_replace("<br />", "", $s[0]);
			}
		}

		$text = preg_replace_callback('/<pre>(.*?)<\/pre>/ms', "bbtohtml_removeBr", $text);
		$text = preg_replace('/<p><pre>(.*?)<\/pre><\/p>/ms', "<pre>\\1</pre>", $text);
		$text = preg_replace_callback('/<ul>(.*?)<\/ul>/ms', "bbtohtml_removeBr", $text);
		$text = preg_replace('/<p><ul>(.*?)<\/ul><\/p>/ms', "<ul>\\1</ul>", $text);
	}
	return $text;
}

function sp_Html2BBCode($text) {
	$text = trim($text);
	$text = str_replace("\n\n", "\n", $text);
	$text = str_replace ('<div class="sfcode">', "<code>", $text);
	$text = str_replace ('</div>', "</code>", $text);

	# BBCode [code]
	if (!function_exists('htmltobb_escape')) {
		function htmltobb_escape($s) {
			global $text;
			return '[code]'.htmlspecialchars_decode($s[1]).'[/code]';
		}
	}
	$text = preg_replace_callback('/\<code\>(.*?)\<\/code\>/ms', "htmltobb_escape", $text);

	# Tags to Find
	$htmltags = array(
		'/\<b\>(.*?)\<\/b\>/is',
		'/\<em\>(.*?)\<\/em\>/is',
		'/\<u\>(.*?)\<\/u\>/is',
		'/\<ul\>(.*?)\<\/ul\>/is',
		'/\<li\>(.*?)\<\/li\>/is',
		'/\<img(.*?) src=\"(.*?)\" (.*?)\>/is',
		'/\<blockquote\>(.*?)\<\/blockquote\>/is',
		'/\<strong\>(.*?)\<\/strong\>/is',
		'/\<a href=\"(.*?)\"(.*?)\>(.*?)\<\/a\>/is',
	);

	# Replace with
	$bbtags = array(
		'[b]$1[/b]',
		'[i]$1[/i]',
		'[u]$1[/u]',
		'[list]$1[/list]',
		'[*]$1',
		'[img]$2[/img]',
		'[quote]$1[/quote]',
		'[b]$1[/b]',
		'[url=$1]$3[/url]',
	);

	# Replace $htmltags in $text with $bbtags
	$text = preg_replace ($htmltags, $bbtags, $text);
	return $text;
}
