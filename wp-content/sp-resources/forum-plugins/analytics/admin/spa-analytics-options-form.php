<?php
/*
Simple:Press
Analytics Plugin Admin Options Form
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');


/**
 * Return all supported chart types
 * 
 * @return array
 */
function sp_analytics_chart_types() {
	
	$chart_types = array(
		'line'		=> __( 'Line Chart',		'sp-analytics' ), 
		'column'	=> __( 'Column Chart',		'sp-analytics' ), 
		'bar'		=> __( 'Bar Chart',			'sp-analytics' ), 
		'pie'		=> __( 'Pie Chart',			'sp-analytics' ), 
		'spline'	=> __( 'Spline Chart',		'sp-analytics' ), 
		'scatter'	=> __( 'Scatter Chart',		'sp-analytics' ), 
		'pyramid'	=> __( 'Pyramid Chart',		'sp-analytics' ), 
		'funnel'	=> __( 'Funnel Chart',		'sp-analytics' ), 
		'waterfall'	=> __( 'Waterfall Chart',	'sp-analytics' ), 
		'doughnut'	=> __( 'Doughnut Chart',	'sp-analytics' ), 
		'area'		=> __( 'Area Chart',		'sp-analytics' ), 
	);


	return apply_filters( 'sp_analytics_chart_types', $chart_types );
}



/**
 * Paint dropdown for weight options
 * 
 * @param string $label
 * @param string $name
 * @param string $selected
 */
function sp_analytics_paint_weight_dropdown( $label, $name, $selected = '' ) {
	
	$selected = $selected ? $selected : 'normal';
	
	sp_analytics_paint_options_dropdown( [ 'lighter', 'normal', 'bold' , 'bolder' ], $label, $name, $selected );
}

/**
 * Paint dropdown for font style options
 * 
 * @param string $label
 * @param string $name
 * @param string $selected
 */
function sp_analytics_paint_font_style_dropdown( $label, $name, $selected = '' ) {
	
	$selected = $selected ? $selected : 'normal';
	
	sp_analytics_paint_options_dropdown( [ 'normal', 'italic', 'oblique' ], $label, $name, $selected );
}


/**
 * Paint a dropdown based on options
 * 
 * @param array $options
 * @param string $label
 * @param string $name
 * @param string $selected
 * @param string $classes
 */
function sp_analytics_paint_options_dropdown( $options, $label, $name, $selected = '', $classes = '' ) {
	
	$selected = $selected && in_array( $selected, $options ) ? $selected : 'normal';
	
	
	spa_paint_select_start( $label, $name, '' );
	
	foreach ( $options as $option ) {
		$_selected = selected( $option , $selected, false );
		printf( '<option value="%s" %s>%s</option>', $option, $_selected, ucfirst( $option ) );
	}
	spa_paint_select_end();
	
}


/**
 * Paint canvas js design related options
 * 
 * @param array $selected_settings
 */
function spa_paint_canvasjs_options( $selected_settings = array() ) {
	
	
	$canvasJS_fields = array(
		'axisX' => array (
			'title' => array(
				'default' => ''
			),
			'titleFontColor' => array(
				'default' => ''
			),
			'titleFontSize' => array(
				'default' => ''
			),
			'titleFontFamily' => array(
				'default' => ''
			),
			'titleFontWeight' => array(
				'default' => ''
			),
			'titleFontStyle' => array(
				'default' => ''
			),
			'labelBackgroundColor' => array(
				'default' => ''
			),
			'labelFontFamily' => array(
				'default' => ''
			),
			'labelFontColor' => array(
				'default' => ''
			),
			'labelFontSize' => array(
				'default' => ''
			),
			'labelFontWeight' => array(
				'default' => ''
			),
			'labelFontStyle' => array(
				'default' => ''
			),
			'lineColor' => array(
				'default' => ''
			),
			'lineThickness' => array(
				'default' => ''
			),
			'interlacedColor' => array(
				'default' => ''
			),
			'gridThickness' => array(
				'default' => ''
			),
			'gridColor' => array(
				'default' => ''
			)

		)

	);

	$canvasJS_fields['axisY'] = $canvasJS_fields['axisX'];
	$canvasJS_fields['data'] = array(
		'color' => array(
			'default' => ''
		),
		'lineColor' => array(
			'default' => ''
		),
		'lineThickness' => array(
			'default' => ''
		),
		'indexLabel' => array(
			'default' => ''
		),
		'indexLabelFontStyle' => array(
			'default' => ''
		),
		'indexLabelFontColor' => array(
			'default' => ''
		),
		'indexLabelFontSize' => array(
			'default' => ''
		),
		'indexLabelFontFamily' => array(
			'default' => ''
		),
		'indexLabelFontWeight' => array(
			'default' => ''
		)
	);

	$panel_count = 1;


	foreach ($canvasJS_fields as $group_id => $groups ) { 


		if( 1 === $panel_count ) {
			spa_paint_tab_right_cell();
		}



		spa_paint_open_fieldset(SP()->primitives->admin_text( 'Design Chart ' . ucfirst( $group_id ) ), true, "analytics-{$group_id}" );


		$group_settings = isset( $selected_settings[ $group_id ] ) ? $selected_settings[ $group_id ] : array();



		foreach( $groups as $field_name => $field ) {

			$default = isset( $field['default'] ) ? $field['default'] : '';
			$value = isset( $group_settings[ $field_name ] ) ? $group_settings[ $field_name ] : $default;

			$label = ucfirst( isset( $field['label'] ) ? $field['label'] : preg_replace( '/(?<=[a-z])(?=[A-Z])/', ' ', $field_name ) );
			$name =  "cjs[{$group_id}][$field_name]";



			if( isset( $field['type'] ) ) {
				$type = $field['type'];
			} else {


				if( false !== strpos( $label, 'Color' ) ) {
					$type = 'color';
				}  elseif( false !== strpos( $label, 'Weight' ) ) {
					$type = 'weight';
				} elseif( false !== strpos( $label, 'Font Style' ) ) {
					$type = 'style';
				} elseif( preg_match(  '/Thickness|Size/', $label ) ) {
					$type = 'number';
				} 
				else {
					$type = 'text';	
				}
			}


			if( 'number' === $type ) {
				spa_paint_number( $label, $name, $value, false, true );
			} elseif( 'weight' === $type ) {
				sp_analytics_paint_weight_dropdown( $label, $name, $value );
			} elseif( 'style' === $type ) {
				sp_analytics_paint_font_style_dropdown( $label, $name, $value );
			} 

			else {
				$classes = 'color' === $type ? 'sp-chart-option-cp-field' : '';

				spa_paint_input( $label, $name,	$value , false, true, $classes );
			}



		}

		spa_paint_close_fieldset();




		$panel_count++;

		if( 0 === $panel_count%2 ) {
			spa_paint_tab_right_cell();
			
		}

	}

	
}

/**
 * Print chart options form based on chart id
 * 
 * @param string $chart_id
 */
function sp_analytics_do_admin_chart_options( $chart_id ) {
	
	$options = sp_analytics_chart_options_data( $chart_id );

	$cjs_options = isset( $options['cjs_options'] ) ? $options['cjs_options'] : array();

	$chart_types = sp_analytics_chart_types();

	
	spa_paint_options_init();

	spa_paint_open_tab(SP()->primitives->admin_text('Components').' - '.SP()->primitives->admin_text('Chart Options'), true );

	spa_paint_open_panel();
		spa_paint_open_fieldset(SP()->primitives->admin_text($options['title']), true, 'analytics-options' );
			spa_paint_checkbox( 'Active', "active", ( '1' == $options['active'] ? true : false ) );
			spa_paint_checkbox( 'Full Row', "full_row", ( '1' == $options['full_row'] ? true : false ) );
			spa_paint_checkbox( 'Show dashboard Widget', "dashboard", ( '1' == $options['dashboard'] ? true : false ) );

			spa_paint_number( 'Order',  'order', $options['order'] );
			spa_paint_hidden_input( 'chart_id', $chart_id );
			

			spa_paint_select_start( 'Chart Type', "chart_type", 'chart_type' );
			echo '<option value="-1">Select</option>';
			foreach ( $chart_types as $type => $type_label ) {
				$selected = selected( $type , $options['type'], false );
				printf( '<option value="%s" %s>%s</option>', $type, $selected, SP()->displayFilters->title($type_label) );
			}
			spa_paint_select_end();

		spa_paint_close_fieldset();

	spa_paint_close_panel();
	

	spa_paint_canvasjs_options( $cjs_options );

	?>
		
	<script type="text/javascript">
		(function($) {
			$(function() {
				$('.sp-chart-option-cp-field input').wpColorPicker();
			})
			
		}(jQuery))
	    
	</script>

	<?php
	spa_paint_close_tab();
}
