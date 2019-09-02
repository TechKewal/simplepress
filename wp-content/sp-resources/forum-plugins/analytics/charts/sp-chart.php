<?php



class SP_Analytics_Chart {

    protected 
        $id,
        $title,
        $active = true,
        $full_row = true,
        $dashboard = true,
        $type = 'column',
        $order = 1,
		$cjs_options = array(),
		$active_range = '',
		$intervalType = '',
		$date_chart = false,
		$default_range = '30_day';
	
	public $active_range_dates = array();


	/**
	 * Set chart properties
	 * 
	 * @param array $args
	 */
	function __construct( $args = array() ) {

		if( empty( $args ) ) {

			$args = sp_analytics_chart_options_data( $this->id );

		}


		$this->title = $args['title'];
		$this->active = $args['active'];
		$this->full_row = $args['full_row'];
		$this->dashboard = $args['dashboard'];
		$this->type = $args['type'];
		$this->order = $args['order'];

		$this->cjs_options = $args['cjs_options'];
		
		$this->active_range = $this->default_range;

	}

	/**
	 * Return chart object by chart id
	 * 
	 * @param string $name
	 * @param array $args
	 * 
	 * @return SP_Analytics_Chart
	 */
	public static function get( $name, $args = array() ) {


		if( !$name ) {
			return;
		}

		if( $args && isset( $args['class'] ) ) {
			$class = $args['class'];
		} else {
			$name_parts = explode( '_', $name );
			$name_parts = array_map( 'ucfirst', $name_parts );

			$class = "SP_Chart_" . implode( '_', $name_parts );
		}



		if( class_exists( $class ) ) {
			return new $class( $args );
		}

	}

	/**
	 * Return chart id
	 * 
	 * @return string
	 */
	public function getID() {
		return $this->id;
	}

	/**
	 * Return chart active status
	 * 
	 * @return boolean
	 */
	public function isActive() {
		return $this->active ? true : false;
	}

	/**
	 * Return chart active status for dashboard
	 * 
	 * @return boolean
	 */
	public function isDashboardActive() {
		return $this->dashboard ? true : false;
	}

	/**
	 * Return is chart full row
	 * 
	 * @return boolean
	 */
	function isFullRow() {
		return $this->full_row ? true : false;
	}

	/**
	 * Return chart title
	 * 
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Return chart type ie, bar, line, column etc
	 */
	function chartType() {
		$this->type;
	}

	/**
	 * Return chart order
	 * 
	 * @return int
	 */
	function getOrder() {
		return $this->order;
	}

	/**
	 * Return data points for a chart, override it in child class
	 * 
	 * @return array
	 */
	function dataPoints() {
		return array();

	}
	
	/**
	 * Filter empty value, so we remove that from canvasJS settings
	 * 
	 * @param string|int $value
	 * 
	 * @return boolean
	 */
	function filter_empty( $value ) {
		return !empty($value) || $value === 0;
	}
	
	/**
	 * Set date interval type based on date range
	 */
	function setIntervalType() {
		
		$start_date = new DateTime( $this->active_range_dates['start_date'] );
		$end_date = new DateTime( $this->active_range_dates['end_date'] );
		$diff = $start_date->diff( $end_date );


		$years = $diff->y;
		$months = ( $years * 12 ) + $diff->m;
		$days = $diff->days;
		
		
		$type = 'hour';
		
		if( $years > 0 ) {
			$type = $years === 1 ? 'month' : 'year';
		} elseif( $months > 0 ) {
			$type = $months === 1 ? 'day' : 'month';
		} elseif( $days > 1 ) {
			$type = 'day';
		}
		
		
		$this->intervalType = $type;
		
	}
	
	/**
	 * Set date format for interval
	 * 
	 * @return string
	 */
	protected function setDateFormat() {
		
		$date_format = '%Y, %m, %d';
		
		switch ( $this->intervalType ) {
			case 'hour':
				$date_format = '%Y, %m, %d, %h';
				break;
			case 'month':
				$date_format = '%Y, %m';
				break;
			case 'year':
				$date_format = '%Y';
				break;
		}
		
		
		return $date_format;
	}
	
	/**
	 * Preset date range option
	 * 
	 * @return array
	 */
	function range_options() {
		
		return array(
			'1_day'		=> __( '24 Hours', 'sp-analytics' ),
			'7_days'	=> __( '7 Days', 'sp-analytics' ),
			'30_days'	=> __( '30 days', 'sp-analytics' ),
			'90_days'	=> __( '90 Days', 'sp-analytics' ),
			'1_year'	=> __( '1 Year', 'sp-analytics' )
		);
	}
	
	/**
	 * Return if we should group chart date by date interval
	 * 
	 * @return boolean
	 */
	public function isChartDateDependent() {
		
		return ( $this->date_chart ) ? true : false;
		
	}
	
	/**
	 * Set date range for a preset option
	 * 
	 * @param string $option
	 */
	function setActiveRange( $option ) {
		
		$format = 'Y-m-d h:i:s';
		
		
		if( 'cr_' === substr( $option, 0, 3 ) ) {
			
			$option_parts = explode( '_', $option );
			
			$start_date = date( $format , strtotime( $option_parts[1] ) );
			$end_date	= date( $format , strtotime( $option_parts[2] ) ) ;
			
			$option = 'custom';
		} else {

			$filter_type = str_replace( '_', ' ', $option );

			$end_date = date( $format );
			$start_date = date( $format , strtotime( "-{$filter_type}" ) ) ;
		}
		
		
		$this->active_range = $option;
		
		$this->active_range_dates = array(
			'start_date'	=> $start_date,
			'end_date'		=> $end_date,
			'start_date_dp' => date( 'F d, Y', strtotime( $start_date ) ),
			'end_date_dp'	=> date( 'F d, Y', strtotime( $end_date ) )
		);
		
		
		$this->setIntervalType();
		
	}
	

	/**
	 * Paint preset date range options
	 */
	function paint_range_options() {
		
		$range_options = $this->range_options();
		
		foreach ( $range_options as $option_name => $option_label ) {

			$class = $this->active_range === $option_name ? 'chart_range_option_active' : '';

			printf( '<a href="" data-option="%s" class="chart_range_option %s">%s</a>', $option_name, $class ,$option_label );
		} 
		
	}


	/**
	 * Generate options for canvasJS
	 * 
	 * @return array
	 */
	function canvasJSOptions() {
		
		$options = $this->cjs_options;


		$data = $options['data'];

		unset( $options['data'] );



		$data['type'] = $this->type;

		$options['data'][0] = array_filter( $data, [ $this, 'filter_empty' ] );
		
		$axisX = isset( $options['axisX'] ) && is_array( $options['axisX'] ) ? $options['axisX'] : array();
		$axisY = isset( $options['axisY'] ) && is_array( $options['axisY'] ) ? $options['axisY'] : array();
		
		
		$options['axisX'] = array_filter( $axisX, [ $this, 'filter_empty' ] );
		$options['axisY'] = array_filter( $axisY, [ $this, 'filter_empty' ] );
		

		return $options;
	}

	/**
	 * Should we force int for Y Axis
	 * 
	 * @return boolean
	 */
	function is_y_axis_int() {
		return true;
	}

	/**
	 * Render chart
	 */
	function render() {

		$container = "sp_chart_{$this->id}_container";

		$url = wp_nonce_url( SPAJAXURL.'analytics_chart_update&amp;chart_id=' . $this->id, 'chart-update' );
		
		?>

	<div class="sp_chart" data-url="<?php echo $url; ?>" data-chart_id="<?php echo $this->id; ?>">
			
		<?php
		
		if( $this->date_chart ) { ?>
			
			<div class="chart_controls">
				<div class="range_options">
					<?php
						$this->paint_range_options();
					?>
				</div>
					
				<div class="date_range">

					<?php _e( 'From', 'sp-analytics' ); ?>:<input type="text" class="sp-datepicker-field date_start sp-analytics-chart-date" value="" />
					<?php _e( 'To', 'sp-analytics' ); ?>:<input type="text" class="sp-datepicker-field date_end sp-analytics-chart-date" value="" />
					<button type="button" class="filter_cr_chart"><?php _e( 'Filter' ); ?></button>

				</div>
				<div class="clear"></div>
			</div>
			
		<?php } ?>

		
		<div class="sp_chart_container" id="<?php echo $container; ?>" style="height: 400px; margin-bottom: 40px;"></div>
	</div>

		
		
	<script type="text/javascript">
		
		<?php
		$dps_var_name = "{$this->id}_dps";
		$options_var_name = "{$this->id}_chart_options";
		
		printf( "var %s = %s; \n", $dps_var_name, json_encode( $this->dataPoints(), JSON_NUMERIC_CHECK ) );
		printf( "var %s = %s; \n", $options_var_name, json_encode( $this->canvasJSOptions() ) );
		
		
		if( $this->isChartDateDependent() ) {
			printf( "%s = spj.analytics_prepare_date_chart_data( %s, %s ); \n", $dps_var_name, $dps_var_name, true );
		}
		
		
		printf( "%s.data[0].dataPoints = %s; \n", $options_var_name, $dps_var_name );
		printf( "var %s = new CanvasJS.Chart( '%s', %s ); \n", $this->id, $container, $options_var_name );
		printf( "%s.render(); \n", $this->id );
		
		
		if( !empty( $this->active_range_dates ) ) {
			
			printf( "spj.update_date_range('%s', %s); \n", $this->id, json_encode( $this->active_range_dates ) );
			
		}
		
		?>	
			
			
			
	</script>

	<?php


	}

}

?>