<?php
/*
Simple:Press
search by user plugin ajax routine for management functions
$LastChangedDate: 2018-10-17 15:17:49 -0500 (Wed, 17 Oct 2018) $
$Rev: 15756 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();
global $wpdb;

# autocomplete
if (isset($_GET['term'])) {
	$out = '[]';

	$query = SP()->filters->str($_GET['term']);
	$where = "display_name LIKE '%".SP()->filters->esc_sql($wpdb->esc_like($query))."%'";
    $orderby = 'IF ('.SPMEMBERS.".display_name LIKE '".SP()->filters->esc_sql($wpdb->esc_like($query))."%', 0, IF (".SPMEMBERS.".display_name LIKE '%".SP()->filters->esc_sql($wpdb->esc_like($query))."%', 1, 2))";
	$users = SP()->DB->table(SPMEMBERS, $where, '', $orderby, 25);
	if ($users) {
		$primary = '';
		$secondary = '';
		foreach ($users as $user) {
			$uname = SP()->displayFilters->name($user->display_name);
			$cUser = array ('id' => $user->user_id, 'value' => $uname);
			if (strcasecmp($query, substr($uname, 0, strlen($query))) == 0) {
				$primary.= json_encode($cUser).',';
			} else {
				$secondary.= json_encode($cUser).',';
			}
		}
		if ($primary != '' || $secondary != '') {
			if ($primary != '') $primary = trim($primary, ',').',';
			if ($secondary != '') $secondary = trim($secondary, ',');
			$out = '['.trim($primary.$secondary, ',').']';
		}
	}
	echo $out;
	die();
}

die();
