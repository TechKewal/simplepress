<?php


class SP_Chart_Top_Posters_All extends SP_Analytics_Chart {
	
    protected $id = 'top_postsers_all';


	/**
	 * Return data points for the chart
	 * 
	 * @return array
	 */
    function dataPoints() {
		
		
		$topPosters = $this->sp_get_top_all_poster_stats( 10 );
	
		$data = array();
		foreach( $topPosters as $poster ) {
			$data[] = array( 'label' => $poster->display_name, 'y' => $poster->posts );
		}
		
        return $data;
    }
	
	/**
	 * Return poster stats
	 * 
	 * @param int $count
	 * 
	 * @return array
	 */
	public function sp_get_top_all_poster_stats( $count ) {

		require_once SP_PLUGIN_DIR.'/forum/database/sp-db-statistics.php';

		add_filter( 'sph_top_poster_stats_query', [ $this, 'sp_get_top_all_poster_stats_query_where_clause' ] );

		$topPosters = sp_get_top_poster_stats( $count );

		remove_filter( 'sph_top_poster_stats_query', [ $this, 'sp_get_top_all_poster_stats_query_where_clause' ] );

		return $topPosters;
	}

	/**
	 * Set where clause for all poster stats
	 * 
	 * @param type $query
	 * 
	 * @return type
	 */
	public function sp_get_top_all_poster_stats_query_where_clause( $query ) {

		$query->where      = 'hide_stats = 0 AND posts > -1';

		return $query;

	}
	
}