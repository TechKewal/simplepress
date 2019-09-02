<?php

class SP_Chart_Total_Topic_Status_Posts extends SP_Chart_Topic_Status {
    protected $id = 'total_topic_status_posts';
	
	protected $date_chart = false;


	/**
	 * Return data points for the chart
	 * 
	 * @return array
	 */
    function dataPoints() {
		
		$sql = "SELECT COUNT(*) `count`,
					t.topic_id, 
					t.topic_name, 
					f.topic_status_set,
					t.topic_status_flag,
					m.*
				FROM " . SPPOSTS . " p
				INNER JOIN " . SPTOPICS . " t ON p.topic_id = t.topic_id AND t.topic_status_flag IS NOT NULL
				INNER JOIN " . SPFORUMS . " f ON t.forum_id = f.forum_id AND f.topic_status_set > 0 
				LEFT JOIN " . SPMETA . " m ON f.topic_status_set = m.meta_id
				GROUP BY f.topic_status_set, t.topic_status_flag";
		
		
		$rows = SP()->DB->select( $sql );
		
		
	
        $data = array();
        foreach( $rows as $row ) {
			$label = $this->getRowLabel( $row );
			$data[] = array( 'label' => $label, 'y' => $row->count );
        }


		return $data;
		
		

    }
	
	
}