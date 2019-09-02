<?php
/*
Simple:Press
WooCommerce Plugin Admin Options Form
$LastChangedDate: 2014-09-12 01:30:12 -0500 (Fri, 12 Sep 2014) $
$Rev: 11958 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_woocommerce_do_admin_form() {
	
	// Get the existing option values from the database
 	$woocommerce = SP()->options->get('woocommerce');

	spa_paint_open_tab(__('Components').' - '.__('Woocommerce', 'sp-woocommerce'));
	
		// Show or get the text that will show up in the WC dashboard for the link to the forum's user profile
		spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Woocommerce Labels', 'sp-woocommerce'), true, 'woocommerce-labels');
    			$submessage = __('What text would you like to use for the user profile link in the WooCommerce dashboard? ', 'sp-woocommerce');
    			spa_paint_input(__('User Profile Link Label Text', 'sp-woocommerce'), 'wcuserprofilelinktext', SP()->displayFilters->title($woocommerce['wcuserprofilelinktext']));
    		spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_tab_right_cell();
		
		spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Optional Links', 'sp-woocommerce'), true, 'woocommerce-optional-links');
				
				// Show or get the url and text that will show up in the WC dashboard for the first optional link 
				$submessage = __('What is the link to your main forum page? ', 'sp-woocommerce');
				spa_paint_input(__('URL #1 - Generally used to hold the path to your forum page', 'sp-woocommerce'), 'wccustomurl01', SP()->displayFilters->title($woocommerce['wccustomurl01']));
				$submessage = __('What text would you like the user to see for the main forum link inside the WooCommerce dashboard? ', 'sp-woocommerce');
    			spa_paint_input(__('Link Label Text for the above URL', 'sp-woocommerce'), 'wccustomlinktext01', SP()->displayFilters->title($woocommerce['wccustomlinktext01']));
				
				spa_paint_spacer();
				
				// Show or get the url and text that will show up in the WC dashboard for the second optional link 
				$submessage = __('What is the link to your main forum page? ', 'sp-woocommerce');
				spa_paint_input(__('URL #2', 'sp-woocommerce'), 'wccustomurl02', SP()->displayFilters->title($woocommerce['wccustomurl02']));
				$submessage = __('What text would you like the user to see for the main forum link inside the WooCommerce dashboard? ', 'sp-woocommerce');
    			spa_paint_input(__('Link Label Text for the above URL', 'sp-woocommerce'), 'wccustomlinktext02', SP()->displayFilters->title($woocommerce['wccustomlinktext02']));
				
    		spa_paint_close_fieldset();
		spa_paint_close_panel();		

		//spa_paint_close_container();

	spa_paint_close_tab();
}
?>