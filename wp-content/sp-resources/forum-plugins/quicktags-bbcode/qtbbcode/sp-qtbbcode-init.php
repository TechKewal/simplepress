<?php
/*
Simple:Press
Quicktags - BBCode init
$LastChangedDate: 2018-10-23 11:00:44 -0500 (Tue, 23 Oct 2018) $
$Rev: 15764 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

	# load editor CSS
	$css = SP()->theme->find_css(SPQTBRESOURCES, 'css/qtbbcode.css', 'css/qtbbcode.spcss');
	SP()->plugin->enqueue_style('sp-html-ed', $css);

	# load editor script
	$script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPQTBEDSCRIPT.'qtbbcode.js' : SPQTBEDSCRIPT.'qtbbcode.min.js';
	SP()->plugin->enqueue_script('spEditor', $script, array('jquery'), false, false);

	# load editor SP support script
	$script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPQTBRESOURCES.'jscript/sp-qtbbcode.js' : SPQTBRESOURCES.'jscript/sp-qtbbcode.min.js';
	SP()->plugin->enqueue_script('spEditorSupp', $script, array('jquery'), false, false);

?>
	<script>
/* <![CDATA[ */
	quicktagsL10n = {
		quickLinks: "<?php echo esc_js(__("Quick links", "sp-qtbbcode")); ?>",
		closeAllOpenTags: "<?php echo esc_js(__("Close all open tags", "sp-qtbbcode")); ?>",
		closeTags: "<?php echo esc_js(__("close tags", "sp-qtbbcode")); ?>",
		enterURL: "<?php echo esc_js(__("Enter the URL", "sp-qtbbcode")); ?>",
		enterImageURL: "<?php echo esc_js(__("Enter the URL of the image", "sp-qtbbcode")); ?>",
		enterImageDescription: "<?php echo esc_js(__("Enter a description of the image", "sp-qtbbcode")); ?>"
	}
	/* ]]> */
	</script>
