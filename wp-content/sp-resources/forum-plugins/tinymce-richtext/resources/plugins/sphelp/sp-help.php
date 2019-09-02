<?php
/**
 * @package TinyMCE
 * @author Moxiecode
 * @copyright Copyright © 2005-2006, Moxiecode Systems AB, All rights reserved.
 */

/** @ignore */
require_once '../../../../../../../wp-load.php';
header('Content-Type: text/html; charset=' . get_bloginfo('charset'));
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title><?php __('Richtext Editor Help', 'sp-tinymce'); ?></title>
<script src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
<?php
wp_admin_css( 'wp-admin', true );
?>
<style>
	body {
		min-width: 0;
	}
	#wphead {
		font-size: 80%;
		border-top: 0;
		color: #555;
		background-color: #f1f1f1;
	}
	#wphead h1 {
		font-size: 24px;
		color: #555;
		margin: 0;
		padding: 10px;
	}
	#tabs {
		padding: 15px 15px 3px;
		background-color: #f1f1f1;
		border-bottom: 1px solid #dfdfdf;
		margin: 0;
	}
	#tabs li {
		display: inline;
	}
	#tabs a.current {
		background-color: #fff;
		border-color: #dfdfdf;
		border-bottom-color: #fff;
		color: #d54e21;
	}
	#tabs a {
		color: #2583AD;
		padding: 6px;
		border-width: 1px 1px 0;
		border-style: solid solid none;
		border-color: #f1f1f1;
		text-decoration: none;
	}
	#tabs a:hover {
		color: #d54e21;
	}
	.wrap h2 {
		border-bottom-color: #dfdfdf;
		color: #555;
		margin: 5px 0;
		padding: 0;
		font-size: 18px;
	}
	#user_info {
		right: 5%;
		top: 5px;
	}
	h3 {
		font-size: 1.1em;
		margin-top: 10px;
		margin-bottom: 0px;
	}
	#flipper {
		margin: 0;
		padding: 5px 20px 10px;
		background-color: #fff;
		border-left: 1px solid #dfdfdf;
		border-bottom: 1px solid #dfdfdf;
	}
	* html {
        overflow-x: hidden;
        overflow-y: scroll;
    }
	#flipper div p {
		margin-top: 0.4em;
		margin-bottom: 0.8em;
		text-align: justify;
	}
	th {
		text-align: center;
	}
	.top th {
		text-decoration: underline;
	}
	.top .key {
		text-align: center;
		width: 5em;
	}
	.top .action {
		text-align: left;
	}
	.align {
		border-left: 3px double #333;
		border-right: 3px double #333;
	}
	.keys {
		margin-bottom: 15px;
		width: 100%;
		border: 0 none;
	}
	.keys p {
		display: inline-block;
		margin: 0px;
		padding: 0px;
	}
	.keys .left { text-align: left; }
	.keys .center { text-align: center; }
	.keys .right { text-align: right; }
	td b {
		font-family: "Times New Roman" Times serif;
	}
	#buttoncontainer {
		text-align: center;
		margin-bottom: 20px;
	}
	#buttoncontainer a, #buttoncontainer a:hover {
		border-bottom: 0px;
	}
	.macos .win,
	.windows .mac {
		display: none;
	}
</style>
<?php if ( is_rtl() ) : ?>
<style>
	#wphead, #tabs {
		padding-left: auto;
		padding-right: 15px;
	}
	#flipper {
		margin: 5px 0 3px 10px;
	}
	.keys .left, .top, .action { text-align: right; }
	.keys .right { text-align: left; }
	td b { font-family: Tahoma, "Times New Roman", Times, serif }
</style>
<?php endif; ?>
</head>
<body class="windows">
<script>
	(function(spj, $, undefined) {
		if (tinymce.isMac) document.body.className = document.body.className.replace(/windows/, 'macos');
	}(window.spj = window.spj || {}, jQuery));
</script>

<ul id="tabs">
	<li><a id="tab1" href="javascript:spj.flipTab(1)" title="<?php esc_attr_e('Basics of Rich Editing', 'sp-tinymce'); ?>" accesskey="1" class="current"><?php _e('Basics', 'sp-tinymce'); ?></a></li>
	<li><a id="tab2" href="javascript:spj.flipTab(2)" title="<?php esc_attr_e('Advanced use of the Rich Editor', 'sp-tinymce'); ?>" accesskey="2"><?php _e('Advanced', 'sp-tinymce'); ?></a></li>
	<li><a id="tab3" href="javascript:spj.flipTab(3)" title="<?php esc_attr_e('Hotkeys', 'sp-tinymce'); ?>" accesskey="3"><?php _e('Hotkeys', 'sp-tinymce'); ?></a></li>
	<li><a id="tab4" href="javascript:spj.flipTab(4)" title="<?php esc_attr_e('About the software', 'sp-tinymce'); ?>" accesskey="4"><?php _e('About', 'sp-tinymce'); ?></a></li>
</ul>

<div id="flipper" class="wrap">

<div id="content1">
	<h2><?php _e('Rich Editing Basics', 'sp-tinymce'); ?></h2>
	<p><?php _e('<em>Rich editing</em>, also called WYSIWYG for What You See Is What You Get, means your text is formatted as you type. The rich editor creates HTML code behind the scenes while you concentrate on writing. Font styles, links and images all appear approximately as they will when the post is published.', 'sp-tinymce'); ?></p>
	<p><?php _e('This editor is provided to remove the need to use HTML or bbCode tags - in fact any such tags that are entered will simply either be stripped when saved or will form a part of the displayed post. Instead of inserting tags, just use the toolbar as you would in a word processor. Most of the toolbar options are self-explanatory but for some of the more advanced options please see the Advanced panel.', 'sp-tinymce'); ?></p>
	<p><?php _e('While using the editor, most basic keyboard shortcuts work like in any other text editor. For example: Shift+Enter inserts line break, Ctrl+C = copy, Ctrl+X = cut, Ctrl+Z = undo, Ctrl+Y = redo, Ctrl+A = select all, etc. (on Mac use the Command key instead of Ctrl). See the Hotkeys tab for all available keyboard shortcuts.', 'sp-tinymce'); ?></p>
    <p><?php _e('If you do not like the way the rich editor works, you may turn it off from your forum <em>Profile > Options > Posting Options</em>. Here - alternative editor choices may be available.', 'sp-tinymce'); ?></p>
</div>

<div id="content2" class="hidden">
	<h2><?php _e('Advanced Rich Editing', 'sp-tinymce'); ?></h2>
	<h3><?php _e('Images and Attachments', 'sp-tinymce'); ?></h3>
	<p><?php _e('There is a button on the editor toolbar for inserting images that are already hosted somewhere on the internet. If you have a URL for an image, click this button and enter the URL in the popup box which appears.', 'sp-tinymce'); ?></p>
	<p><?php _e('If you want to upload your own images or other media and your forum administrator has granted you permission to do so, then use the <b>Attachments</b> button beneath the editor window instead.', 'sp-tinymce'); ?></p>
	<h3><?php _e('Inserting YouTube and Other Videos', 'sp-tinymce'); ?></h3>
	<p><?php _e('The forum suports what is known as the <em>oEmbed</em> protocol. This means that from a large nunber of popular video hosting sites - such as YouTube - all you need to insert to embed the video is the URL where it can be found. <b>Please Note</b> that for security reasons, iFrames can NOT be used in forum posts and will be stripped when the post is saved.', 'sp-tinymce'); ?></p>
	<h3><?php _e('Inserting Formatted Links', 'sp-tinymce'); ?></h3>
	<p><?php _e('Note that the Insert Link toolbar button will only become active when your text is selected. So create the link text first, then select it, click on the Link toolbar button and supply the link target URL in the popup dialog box.', 'sp-tinymce'); ?></p>
	<h3><?php _e('HTML in the Rich Editor', 'sp-tinymce'); ?></h3>
	<p><?php _e('Any HTML entered directly into the rich editor will show up as text when the post is viewed or be stripped. If you need to include HTML elements that cannot be generated from the toolbar options, click on the HTML toolbar button where you can enter it by hand. When done, click on Update and it should then appear in the editor window.', 'sp-tinymce'); ?></p>
	<h3><?php _e('Pasting in the Rich Editor', 'sp-tinymce'); ?></h3>
	<p><?php _e('When pasting content from another web page the results can be inconsistent and depend on your browser and on the web page you are pasting from. The editor tries to correct any invalid HTML code that was pasted, but for best results try using one of the paste toolbar buttons. Doing so can help clean up any invisible formatting or embedded HTML tags that were pasted along with the text.', 'sp-tinymce'); ?></p>
	<p><?php _e('Pasting content from another application, like Word or Excel, MUST be done with the Paste from Word button as such applications often contain a large volume of invisible formatting that can seriously disrupt the normal display of your post.', 'sp-tinymce'); ?></p>
</div>

<div id="content3" class="hidden">
	<h2><?php _e('Writing at Full Speed', 'sp-tinymce'); ?></h2>
    <p><?php _e('Rather than reaching for your mouse to click on the toolbar, use these access keys. Windows and Linux use Ctrl + letter. Macintosh uses Command + letter.', 'sp-tinymce'); ?></p>

	<table class="keys">
		<tr class="top"><th class="key center"><?php _e('Letter', 'sp-tinymce'); ?></th><th class="left"><?php _e('Action', 'sp-tinymce'); ?></th><th class="key center"><?php _e('Letter', 'sp-tinymce'); ?></th><th class="left"><?php _e('Action', 'sp-tinymce'); ?></th></tr>
		<tr><th>c</th><td><?php _e('Copy', 'sp-tinymce'); ?></td><th>v</th><td><?php _e('Paste', 'sp-tinymce'); ?></td></tr>
		<tr><th>a</th><td><?php _e('Select all', 'sp-tinymce'); ?></td><th>x</th><td><?php _e('Cut', 'sp-tinymce'); ?></td></tr>
		<tr><th>z</th><td><?php _e('Undo', 'sp-tinymce'); ?></td><th>y</th><td><?php _e('Redo', 'sp-tinymce'); ?></td></tr>

		<tr><th>b</th><td><?php _e('Bold', 'sp-tinymce'); ?></td><th>i</th><td><?php _e('Italic', 'sp-tinymce'); ?></td></tr>
		<tr><th>u</th><td><?php _e('Underline', 'sp-tinymce'); ?></td><th>1</th><td><?php _e('Heading 1', 'sp-tinymce'); ?></td></tr>
		<tr><th>2</th><td><?php _e('Heading 2', 'sp-tinymce'); ?></td><th>3</th><td><?php _e('Heading 3', 'sp-tinymce'); ?></td></tr>
		<tr><th>4</th><td><?php _e('Heading 4', 'sp-tinymce'); ?></td><th>5</th><td><?php _e('Heading 5', 'sp-tinymce'); ?></td></tr>
		<tr><th>6</th><td><?php _e('Heading 6', 'sp-tinymce'); ?></td><th>9</th><td><?php _e('Address', 'sp-tinymce'); ?></td></tr>
	</table>

	<p><?php _e('The following shortcuts use different access keys: Alt + Shift + letter.', 'sp-tinymce'); ?></p>
	<table class="keys">
		<tr class="top"><th class="key center"><?php _e('Letter', 'sp-tinymce'); ?></th><th class="left"><?php _e('Action', 'sp-tinymce'); ?></th><th class="key center"><?php _e('Letter', 'sp-tinymce'); ?></th><th class="left"><?php _e('Action', 'sp-tinymce'); ?></th></tr>
		<tr><th>n</th><td><?php _e('Check Spelling', 'sp-tinymce'); ?></td><th>l</th><td><?php _e('Align Left', 'sp-tinymce'); ?></td></tr>
		<tr><th>j</th><td><?php _e('Justify Text', 'sp-tinymce'); ?></td><th>c</th><td><?php _e('Align Center', 'sp-tinymce'); ?></td></tr>
		<tr><th>d</th><td><span style="text-decoration: line-through;"><?php _e('Strikethrough', 'sp-tinymce'); ?></span></td><th>r</th><td><?php _e('Align Right', 'sp-tinymce'); ?></td></tr>
		<tr><th>u</th><td><strong>&bull;</strong> <?php _e('List'); ?></td><th>a</th><td><?php _e('Insert link', 'sp-tinymce'); ?></td></tr>
		<tr><th>o</th><td>1. <?php _e('List', 'sp-tinymce'); ?></td><th>s</th><td><?php _e('Remove link', 'sp-tinymce'); ?></td></tr>
		<tr><th>q</th><td><?php _e('Quote', 'sp-tinymce'); ?></td><th>m</th><td><?php _e('Insert Image', 'sp-tinymce'); ?></td></tr>
		<tr><th>w</th><td><?php _e('Distraction Free Writing mode', 'sp-tinymce'); ?></td><th>t</th><td><?php _e('Insert More Tag', 'sp-tinymce'); ?></td></tr>
		<tr><th>p</th><td><?php _e('Insert Page Break tag', 'sp-tinymce'); ?></td><th>h</th><td><?php _e('Help', 'sp-tinymce'); ?></td></tr>
	</table>

	<p style="padding: 15px 10px 10px;"><?php _e('Editor width in Distraction Free Writing mode:', 'sp-tinymce'); ?></p>
	<table class="keys">
		<tr><th><span class="win">Alt +</span><span class="mac">Ctrl +</span></th><td><?php _e('Wider', 'sp-tinymce'); ?></td>
			<th><span class="win">Alt -</span><span class="mac">Ctrl -</span></th><td><?php _e('Narrower', 'sp-tinymce'); ?></td></tr>
		<tr><th><span class="win">Alt 0</span><span class="mac">Ctrl 0</span></th><td><?php _e('Default width', 'sp-tinymce'); ?></td><th></th><td></td></tr>
	</table>
</div>

<div id="content4" class="hidden">
	<h2><?php _e('About TinyMCE', 'sp-tinymce'); ?></h2>

	<p><?php printf(__('TinyMCE is a platform independent web based Javascript HTML WYSIWYG editor released as Open Source under %sLGPL</a>	by Moxiecode Systems AB. It has the ability to convert HTML TEXTAREA fields or other HTML elements to editor instances.', 'sp-tinymce'), '<a href="'.home_url('/wp-includes/js/tinymce-richtext/license.txt').'" target="_blank" title="'.esc_attr__('GNU Library General Public License', 'sp-tinymce').'">'); ?></p>
	<p>&copy; 2003-<?php echo date('Y'); ?> <a href="http://www.moxiecode.com" target="_blank">Moxiecode Systems AB</a> <?php _e('All rights reserved.', 'sp-tinymce'); ?></p>
	<p><?php _e('For more information about this software visit the <a href="http://tinymce.com" target="_blank">TinyMCE website</a>.', 'sp-tinymce'); ?></p>

	<div id="buttoncontainer">
		<a href="http://www.moxiecode.com" target="_blank"><img src="img/gotmoxie.png" alt="<?php esc_attr_e('Got Moxie?', 'sp-tinymce'); ?>" style="border: 0" /></a>
	</div>

</div>
</div>

<div class="mceActionPanel">
	<div style="margin: 8px auto; text-align: center;padding-bottom: 10px;">
		<input type="button" id="cancel" name="cancel" class="mce-ico mce-i-remove" value="<?php esc_attr_e('Close', 'sp-tinymce'); ?>" title="<?php esc_attr_e('Close', 'sp-tinymce'); ?>" onclick="tinyMCEPopup.close();" />
	</div>
</div>
<script>
	(function(spj, $, undefined) {
		function d(id) {
			return document.getElementById(id);
		}

		spj.flipTab = function(n) {
			var i, c, t;

			for ( i = 1; i <= 4; i++ ) {
				c = d('content'+i.toString());
				t = d('tab'+i.toString());
				if ( n == i ) {
					c.className = '';
					t.className = 'current';
				} else {
					c.className = 'hidden';
					t.className = '';
				}
			}
		};
	}(window.spj = window.spj || {}, jQuery));
</script>
</body>
</html>
