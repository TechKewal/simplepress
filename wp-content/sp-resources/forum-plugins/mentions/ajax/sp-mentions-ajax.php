<?php
/*
Simple:Press
Mentions plugin ajax routine for management functions
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

global $wpdb;

$response = array('ok' => 1, 'DATA' => array(), 'ERRORS' => array());
$out = '[]';

$table = SPMEMBERS;
$fields = SPMEMBERS.'.display_name, user_nicename';
$distinct = false;
$join = array(SPUSERS.' ON ID = user_id');
$query = SP()->filters->str($_GET['q']);
$where = SPMEMBERS.".display_name LIKE '%".SP()->filters->esc_sql($wpdb->esc_like($query))."%'";
$orderby = 'IF ('.SPMEMBERS.".display_name LIKE '".SP()->filters->esc_sql($wpdb->esc_like($query))."%', 0, IF (".SPMEMBERS.".display_name LIKE '%".SP()->filters->esc_sql($wpdb->esc_like($query))."%', 1, 2))";

$query = new stdClass();
	$query->table      = $table;
	$query->fields 	  = $fields;
	$query->left_join  = $join;
	$query->where 	  = $where;
	$query->orderby 	  = $orderby;
	$query->limits     = 25;
$query = apply_filters('sph_mentions_matches', $query);
$users = SP()->DB->select($query);

if ($users) {
    $count = 0;
	foreach ($users as $user) {
        $response['DATA'][$count]['user_nicename'] = $user->user_nicename;
        $response['DATA'][$count]['display_name'] = $user->display_name;
        $count++;
	}
}

print json_encode($response);
die();
