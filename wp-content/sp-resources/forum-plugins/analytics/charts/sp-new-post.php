<?php

class SP_Chart_New_Post extends SP_Analytics_Chart {
    protected $id = 'new_post';
	
	protected $date_chart = true;


	/**
	 * Return data points for the chart
	 * 
	 * @return array
	 */
    function dataPoints() {
		
		if( !isset( $this->cjs_options['axisX'] ) || !is_array( $this->cjs_options['axisX'] ) ) {
			$this->cjs_options['axisX'] = array();
		}
		
		
		if( empty( $this->active_range_dates ) ) {
			$this->setActiveRange( $this->default_range );
		}
		
		
		$start_date = $this->active_range_dates['start_date'];
		$end_date = $this->active_range_dates['end_date'];
		
		
		$where = "WHERE post_date BETWEEN '{$start_date}' AND '{$end_date}'";
		
		
		$intervalType = $this->intervalType;
		
		$date_format = $this->setDateFormat();
		
		$sql = "SELECT count(*) posts, post_date, DATE_FORMAT(post_date,'".$date_format."') `interval` FROM " . SPPOSTS . " {$where} group by `interval`";
		
		$rows = SP()->DB->select( $sql );
		
		
		
	
        $data = array();
        foreach( $rows as $post ) {
            $data[] = array( 'x' => "{$post->interval}", 'y' => (int) $post->posts );
        }
		

		$this->cjs_options['axisX']['intervalType'] = $intervalType;
		
        return $data;
    }
}