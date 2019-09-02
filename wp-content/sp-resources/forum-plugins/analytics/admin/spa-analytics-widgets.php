<?php

/**
 * Add dashboard widget for each active chart
 */
function sp_analytics_add_dashboard_widgets() {


	$charts = sp_analytics_get_active_charts();

	foreach( $charts as $chart ) {
		
		if( !$chart->isDashboardActive() ) {
			continue;
		}
		
		$chart_id = $chart->getID();
		
		
		
		wp_add_dashboard_widget(
			"sp_chart_{$chart_id}_dashboard_widget",
			$chart->getTitle(),
			'sp_paint_dashboard_chart_widget',
			null,
			array( 'chart' => $chart )
		);
	}
}


/**
 * Render dashboard chart
 * 
 * @param string $var
 * @param array $args
 */
function sp_paint_dashboard_chart_widget( $var, $args ) {
	
	$chart = isset( $args['args']['chart'] ) ? $args['args']['chart'] : null;
	
	if( $chart ) {
		$chart->render();
	}
	
}