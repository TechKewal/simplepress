<?php
/*
Simple:Press
WooCommerce Plugin Support Routines
$LastChangedDate: 2016-05-15 12:17:52 -0500 (Sun, 15 May 2016) $
$Rev: 14188 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

add_filter( 'woocommerce_account_menu_items', 'sp_woocommerce_do_links' ) ;	


/**
 * Insert the new endpoint into the WC My Account menu.
 *
 * Filter hook: woocommerce_account_menu_items
 *
 * @param array $items
 *
 * @return array
 */
function sp_woocommerce_do_links( $items ) {
	
	// Remove the logout menu item (we'll add it back in later!)
	$logout = isset($items['customer-logout']) ? $items['customer-logout'] : false;
	if ($logout) {
		unset( $items['customer-logout'] );
	}		

	// Get the labels for the new WooCommerce MY ACCOUNT menu items...
	$wcoptions = SP()->options->get('woocommerce');
	$wclinktext = $wcoptions['wcuserprofilelinktext'];  		// maybe need to do an isset check here to avoid a warning 
	$wcoptionallinktext01 = $wcoptions['wccustomlinktext01'];  	// maybe need to do an isset check here to avoid a warning 
	$wcoptionallinktext02 = $wcoptions['wccustomlinktext02'];  	// maybe need to do an isset check here to avoid a warning 
	
	// Insert the new menu item (endpoint)
	// The endpoint will be tied to an actual URL in a filter later (see next function below this one)
	$items[ 'sp_profile_link' ] 	= apply_filters( 'sp_wc_account_tab_name_forum_user_link_text', $wclinktext );	
	if (!empty($wcoptionallinktext01)) {
		$items[ 'sp_optional_link01' ] 	= apply_filters( 'sp_wc_account_tab_name_optional_link01_text', $wcoptionallinktext01 );
	}
	if (!empty($wcoptionallinktext02)) {
		$items[ 'sp_optional_link02' ] 	= apply_filters( 'sp_wc_account_tab_name_optional_link01_text', $wcoptionallinktext02 );
	}

	// Insert back the logout item.
	if ($logout) {
		$items['customer-logout'] = $logout;
	}

	return $items;	
	
}

add_action( 'woocommerce_get_endpoint_url', 'sp_woocommerce_get_endpoint_url', 10, 4 );
/**
 * Return an endpoint URL based on the endpoint id
 *
 * Filter hook: woocommerce_get_endpoint_url
 *
 * @param string $url 		URL for the endpoint
 * @param string $endpoint	endpoint ID
 * @param string $value
 * @param string $permalink
 *
 * @return string	The endpoint url for the endpoing ID received
 */
function sp_woocommerce_get_endpoint_url( $url, $endpoint, $value, $permalink ) {

	if ( 'sp_profile_link' === $endpoint ) {
		
		// Construct the endpoint url using the global SF() function to get options
		 $sfpermalink = SP()->options->get('sfpermalink');
		 $url = trailingslashit( $sfpermalink ) . 'profile/' . (string) get_current_user_id();

	}
		
	if ( 'sp_optional_link01' === $endpoint ) {
		
		/* Set optional link #1 */
		$wcoptions = SP()->options->get('woocommerce');
		$url = $wcoptions['wccustomurl01'];
		
	}
	
	if ( 'sp_optional_link02' === $endpoint ) {
		
		/* Set optional link #2 */
		$wcoptions = SP()->options->get('woocommerce');
		$url = $wcoptions['wccustomurl02'];
		
	}
		
	
	return $url;
	
}

?>