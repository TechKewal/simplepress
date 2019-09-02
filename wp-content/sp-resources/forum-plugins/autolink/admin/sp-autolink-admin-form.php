<?php
/*
Simple:Press
Auto Link Plugin Admin Options Form
$LastChangedDate: 2018-10-18 21:06:54 -0500 (Thu, 18 Oct 2018) $
$Rev: 15758 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_autolink_do_admin_form() {
	$autolink = SP()->options->get('autolink');

	spa_paint_open_tab(__('Components').' - '.__('Auto Linking', 'sp-autolink'));
		spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Keywords', 'sp-autolink'), true, 'autolink-keywords');
    			$submessage = __('Enter keywords one per line - there must be a corresponding entry in replacement urls to right. ', 'sp-autolink');
    			$submessage.= '<br />'.__('Keywords can be a single word or a multi-word string to be matched in post content.', 'sp-autolink');
    			$submessage.= '<br />'.__('Replacements are case-sensitive. For case insensitive replacements prefix word with a % symbol', 'sp_autolink');
    			spa_paint_wide_textarea(__('Keywords list - words to be replaced in post', 'sp-autolink'), 'keywords', SP()->displayFilters->title($autolink['keywords']), $submessage, 15);
    		spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_tab_right_cell();

		spa_paint_open_panel();
    		spa_paint_open_fieldset(__('URL Replacements', 'sp-autolink'), true, 'autolink-replacements');
    			$submessage = __('Enter replacement URL one entry per line - there must be a corresponding entry in keywords to left.', 'sp-autolink');
    			$submessage.= '<br />'.__('MUST be URL of form http://xxx.com as it will get hyperlinked.', 'sp-autolink');
    			$submessage.= '<br />';
    			spa_paint_wide_textarea(__('URL list - URLS to replace keywords in post', 'sp-autolink'), 'urls', SP()->displayFilters->title($autolink['urls']), $submessage, 15);
    		spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
    	echo '<div class="sfform-panel-spacer"></div>';
	spa_paint_close_tab();

	echo '<div class="sfform-panel-spacer"></div>';

	spa_paint_open_tab(__('Components').' - '.__('Auto Linking Options', 'sp-autolink'), true);
    		spa_paint_open_panel();
        		spa_paint_open_fieldset(__('Options', 'sp-autolink'), true, 'autolink-options');
			     spa_paint_checkbox(__("Do not use word boundary checking (recommended only for languages that don't separate words by blank characters)", 'sp-autolink'), 'noboundary', $autolink['noboundary']);
        		spa_paint_close_fieldset();
			spa_paint_close_panel();
		spa_paint_close_container();
}
