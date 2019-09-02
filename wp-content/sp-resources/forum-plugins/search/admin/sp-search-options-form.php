<?php
/*
Simple:Press
Search Options Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_search_do_admin_options_panel() {
	$options = SP()->options->get('search');

	spa_paint_open_panel();
		spa_paint_open_fieldset(__('Site Integrated Search', 'sp-search'), 'true', 'search');
    		spa_paint_input(__('Label text for search form', 'sp-search'), 'form', $options['form'], false, false);
            spa_paint_input(__('Label text for forum search tab', 'sp-search'), 'ftab', $options['ftab'], false, false);
    		spa_paint_input(__('Label text for blog search tab', 'sp-search'), 'btab', $options['btab'], false, false);

        	$list = array();
            $post_types = get_post_types(array('public' => true));
            $ignore = array('attachment', 'revision', 'nav_menu_item');
            foreach ($post_types as $key => $value) {
            	if (!in_array($key, $ignore)) {
            		if (!empty($options['searchposttypes']) && in_array($key, $options['searchposttypes'])) {
            			$list[$key] = $options['searchposttypes'][$key];
            		} else {
            			$list[$key] = false;
            		}
            	}
            }
    		foreach ($list as $key => $value) {
    			spa_paint_checkbox(sprintf(__('Integrated Search results includes post type: %s', 'sp-search'), '<strong>'.$key.'</strong>'), 'searchposttype_'.$key, $value);
    		}
		spa_paint_close_fieldset();
	spa_paint_close_panel();
}
