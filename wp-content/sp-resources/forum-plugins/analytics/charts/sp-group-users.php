<?php


class SP_Chart_Group_Users extends SP_Analytics_Chart {
    protected $id = 'group_users';


	/**
	 * Return data points for the chart
	 * 
	 * @return array
	 */
    function dataPoints() {
		
		$group_users = $this->get_group_users();
		
		$data = array();
		foreach( $group_users as $group ) {
			$data[] = array( 'label' => $group->group_name, 'y' => $group->users_count );
		}
		
        return $data;
    }
	
	/**
	 * Return users by group
	 * 
	 * @return array
	 */
	function get_group_users() {
		
		$query = "SELECT msh.usergroup_id, count( msh.usergroup_id ) users_count, ug.usergroup_name group_name
				FROM " . SPMEMBERS . " m
				INNER JOIN " . SPMEMBERSHIPS . " msh ON m.user_id = msh.user_id 
				INNER JOIN " . SPUSERGROUPS . " ug ON ug.usergroup_id = msh.usergroup_id
				WHERE msh.usergroup_id > 0
                GROUP BY msh.usergroup_id
				ORDER BY display_name";
		
		$group_users = SP()->DB->select( $query );
		
		return $group_users;
		
	}
}