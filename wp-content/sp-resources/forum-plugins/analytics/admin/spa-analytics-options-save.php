<?php
/*
Simple:Press
Analytics Plugin Admin Options Save Routine
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/**
 * Save chart options for a chart
 * 
 * @param string $chart_id
 * 
 * @return string
 */
function sp_analytics_do_admin_save_options( $chart_id ) {
    
    check_admin_referer( 'forum-adminform_userplugin', 'forum-adminform_userplugin' );
    
    $option_key = 'chart_' . $chart_id;

	# Save options
	$options = SP()->options->get( $option_key );

    
    $active = filter_input( INPUT_POST, 'active', FILTER_SANITIZE_STRING );
    $full_row = filter_input( INPUT_POST, 'full_row', FILTER_SANITIZE_STRING );
    $dashboard = filter_input( INPUT_POST, 'dashboard', FILTER_SANITIZE_STRING );
    $order = filter_input( INPUT_POST, 'order', FILTER_SANITIZE_NUMBER_INT );
    $chart_type = filter_input( INPUT_POST, 'chart_type', FILTER_SANITIZE_STRING );


    $chart_design_options = filter_input( INPUT_POST, 'cjs', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );




    $options['active'] = 'on' === $active ? '1' : '0';
    $options['full_row'] = 'on' === $full_row ? '1' : '0';
    $options['dashboard'] = 'on' === $dashboard ? '1' : '0';
    $options['order'] = $order;
    $options['type'] = $chart_type;
    $options['cjs_options'] = $chart_design_options;
    
    SP()->options->update( $option_key, $options);
	
	return __( "Chart options updated!", "sp-tinymce" );
}
