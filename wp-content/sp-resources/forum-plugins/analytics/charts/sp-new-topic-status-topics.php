<?php

class SP_Chart_New_Topic_Status_Topics extends SP_Chart_Topic_Status {
    protected $id = 'new_topic_status_topics';
	
	protected $date_chart = true;


	/**
	 * Making this chart not date dependent so we don't group results by date interval
	 * 
	 * @return boolean
	 */
	function isChartDateDependent() {
		return false;
	}
	
	/**
	 * Return data points for the chart
	 * 
	 * @return array
	 */
    function dataPoints() {
		
//		if( !isset( $this->cjs_options['axisX'] ) || !is_array( $this->cjs_options['axisX'] ) ) {
//			$this->cjs_options['axisX'] = array();
//		}
		
		
		if( empty( $this->active_range_dates ) ) {
			$this->setActiveRange( $this->default_range );
		}
		
		
		$start_date = $this->active_range_dates['start_date'];
		$end_date = $this->active_range_dates['end_date'];
		
		
		$where = " WHERE f.topic_status_set > 0 AND topic_date BETWEEN '{$start_date}' AND '{$end_date}' ";
		
		
		$intervalType = $this->intervalType;
		
		$date_format = $this->setDateFormat();
		
		
		/*
		
		$sql = "SELECT COUNT(*) `count`,
					t.topic_id, 
					t.topic_name, 
					f.topic_status_set,
					t.topic_status_flag,
					t.topic_date,
                    DATE_FORMAT(t.topic_date,'".$date_format."') `interval`,
					m.*
				FROM " . SPTOPICS . " t
				INNER JOIN " . SPFORUMS . " f ON t.forum_id = f.forum_id AND f.topic_status_set > 0
				LEFT JOIN " . SPMETA . " m ON f.topic_status_set = m.meta_id
				$where
				GROUP BY f.topic_status_set, t.topic_status_flag, `interval`";
		 
		*/
			
		
		$sql = "SELECT COUNT(*) `count`,
					t.topic_id, 
					t.topic_name, 
					f.topic_status_set,
					t.topic_status_flag,
					m.*
				FROM " . SPTOPICS . " t
				INNER JOIN " . SPFORUMS . " f ON t.forum_id = f.forum_id AND t.topic_status_flag IS NOT NULL
				LEFT JOIN " . SPMETA . " m ON f.topic_status_set = m.meta_id
				$where 
				GROUP BY f.topic_status_set, t.topic_status_flag";
		
		
		
		$rows = SP()->DB->select( $sql );
		
	
        $data = array();
        foreach( $rows as $row ) {
			$label = $this->getRowLabel( $row );
			$data[] = array( 'label' => $label, 'y' => (int) $row->count );
        }
		

		//$this->cjs_options['axisX']['intervalType'] = $intervalType;
		
        return $data;
    }
	
}