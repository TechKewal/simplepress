<?php


class SP_Chart_Forum_Topics extends SP_Analytics_Chart {
    protected $id = 'forum_topics';


	/**
	 * Return data points for the chart
	 * 
	 * @return array
	 */
    function dataPoints() {
        $forums = SP()->DB->table(SPFORUMS, '', '', 'group_id');
	
        $data = array();
        foreach( $forums as $forum ) {
            $data[] = array( 'label' => $forum->forum_name, 'y' => $forum->topic_count );
        }

        return $data;
    }
}