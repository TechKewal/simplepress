<?php

class SP_Chart_Top_Posters extends SP_Analytics_Chart {
    protected $id = 'top_postsers';


	/**
	 * Return data points for the chart
	 * 
	 * @return array
	 */
    function dataPoints() {
		
		
		require_once SP_PLUGIN_DIR.'/forum/database/sp-db-statistics.php';
	
		$topPosters = sp_get_top_poster_stats( 10 );

		$data = array();
		foreach( $topPosters as $poster ) {
			$data[] = array( 'label' => $poster->display_name, 'y' => $poster->posts );
		}
		
        return $data;
    }
}
