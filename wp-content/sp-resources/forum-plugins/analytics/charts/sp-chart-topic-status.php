<?php

class SP_Chart_Topic_Status extends SP_Analytics_Chart {
    
	
	protected $date_chart = false;


	/**
	 * Return chart label for topic status related charts
	 * 
	 * @param object $row
	 * 
	 * @return string
	 */
	function getRowLabel( $row ) {
		
		$topic_statuses = maybe_unserialize( $row->meta_value );
		
		$label = $row->meta_key;

		foreach( $topic_statuses as $topic_status ) {
			if( $topic_status['key'] == $row->topic_status_flag ) {
				$label = $label . ' : ' . $topic_status['status'];
				break;
			}
		}
		
		return $label;
	}
}