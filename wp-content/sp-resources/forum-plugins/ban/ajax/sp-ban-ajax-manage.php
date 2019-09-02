<?php
/*
Simple:Press
Ban plugin ajax routine for management functions
$LastChangedDate: 2018-10-17 15:17:49 -0500 (Wed, 17 Oct 2018) $
$Rev: 15756 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

spa_admin_ajax_support();
global $wpdb;

# autocomplete
if (isset($_GET['term'])) {
	$out = '[]';

	$query = SP()->filters->str($_GET['term']);
	$where = "display_name LIKE '%".SP()->filters->esc_sql($wpdb->esc_like($query))."%'";
	$users = SP()->DB->table(SPMEMBERS, $where, '', 'display_name DESC', 25);
	if ($users) {
		$primary = '';
		$secondary = '';
		foreach ($users as $user) {
            # dont show already banned
            $banned = SP()->options->get('banned_users');
            if (!empty($banned)) {
                foreach ($banned as $ban) {
                    if ($ban['id'] == $user->user_id) continue 2;
                }
            }

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

if (!isset($_GET['targetaction'])) die();
$action = SP()->filters->str($_GET['targetaction']);

if ($action == 'remove') {
    require_once SPBANLIBDIR.'sp-ban-components.php';

    $userid = SP()->filters->integer($_GET['id']);
    if (empty($userid)) die();

    $banned = SP()->options->get('banned_users');
    if (empty($banned)) die();
    foreach ($banned as $index => $ban) {
        if ($ban['id'] == $userid) {
            sp_ban_remove_user_ban($index, $userid);
            die();
        }
    }

    die();
}

die();
