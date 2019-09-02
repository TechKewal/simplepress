<?php
/*
Simple:Press
Analytics plugin ajax routine for chart filter
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# get out of here if no chart specified
$chart_id = (isset($_GET['chart_id'])) ? $_GET['chart_id'] : '';
if (empty($chart_id)) die();

$option = filter_input( INPUT_POST, 'option', FILTER_SANITIZE_STRING );

$chart = SP_Analytics_Chart::get( $chart_id );

$chart->setActiveRange( $option );

if( $chart ) {
	
	$data['dps'] = $chart->dataPoints();
	$data['options'] = $chart->canvasJSOptions();
	$data['y_int'] = $chart->is_y_axis_int();
	$data['is_date_dependent'] = $chart->isChartDateDependent();
	$data['date_range'] = $chart->active_range_dates;
	
	wp_send_json_success( $data );
	die();
}
?>