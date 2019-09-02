<?php


class SP_Chart_New_Topic extends SP_Analytics_Chart {
    protected $id = 'new_topic';
	
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
		
		
		$where = "WHERE topic_date BETWEEN '{$start_date}' AND '{$end_date}'";
		
		
		$intervalType = $this->intervalType;
		
		$date_format = $this->setDateFormat();
		
		$sql = "SELECT count(*) topics, topic_date ,DATE_FORMAT(topic_date,'".$date_format."') `interval` FROM " . SPTOPICS . " {$where} group by `interval`";
		
		$rows = SP()->DB->select( $sql );
		
	
        $data = array();
        foreach( $rows as $topic ) {
            $data[] = array( 'x' => "{$topic->interval}", 'y' => (int) $topic->topics );
        }
		

		$this->cjs_options['axisX']['intervalType'] = $intervalType;
		
        return $data;
    }
	
}