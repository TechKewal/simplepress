<?php

class SP_Chart_Top_Tags extends SP_Analytics_Chart {
    protected $id = 'top_tags';
	
	protected $date_chart = false;


	/**
	 * Return data points for the chart
	 * 
	 * @return array
	 */
    function dataPoints() {
		
		
		$sql = "SELECT * FROM " . SPTAGS . " ORDER BY tag_count DESC LIMIT 0, 10";
		
		$rows = SP()->DB->select( $sql );
		
        $data = array();
        foreach( $rows as $row ) {
            $data[] = array( 'label' => $row->tag_name, 'y' => $row->tag_count );
        }
		
		
        return $data;
    }
}