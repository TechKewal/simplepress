<?php
/*
Simple:Press
Prune Database Plugin Support Routines
$LastChangedDate: 2019-01-07 21:16:56 -0600 (Mon, 07 Jan 2019) $
$Rev: 15866 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_prune_db_get_database() {
	# retrieve group and forum records
	$records = SP()->DB->select('SELECT '.SPGROUPS.'.group_id, group_name, forum_id, forum_name, topic_count
			 FROM '.SPGROUPS.'
			 JOIN '.SPFORUMS.' ON '.SPGROUPS.'.group_id = '.SPFORUMS.'.group_id
			 ORDER BY group_seq, forum_seq');

	# rebuild into an array
	$groups=array();
	$gindex=-1;
	$findex=0;
	if ($records) {
		foreach ($records as $record) {
			$groupid=$record->group_id;
			$forumid=$record->forum_id;

			if ($gindex == -1 || $groups[$gindex]['group_id'] != $groupid) {
				$gindex++;
				$findex=0;
				$groups[$gindex]['group_id']=$record->group_id;
				$groups[$gindex]['group_name']=$record->group_name;
			}
			if (isset($record->forum_id)) {
				$groups[$gindex]['forums'][$findex]['forum_id']=$record->forum_id;
				$groups[$gindex]['forums'][$findex]['forum_name']=$record->forum_name;
				$groups[$gindex]['forums'][$findex]['topic_count']=$record->topic_count;
				$findex++;
			}
		}
	} else {
		$records = SP()->DB->table(SPGROUPS, '', '', 'group_seq');
		if ($records) {
			foreach ($records as $record) {
				$groups[$gindex]['group_id']=$record->group_id;
				$groups[$gindex]['group_name']=$record->group_name;
				$groups[$gindex]['group_desc']=$record->group_desc;
				$gindex++;
			}
		}
	}
	return $groups;
}

# function to create an sql query for a list of topics based on the filter criteria
# these topics then get displayed in another form for the admin to mark the topics for pruning
function sp_prune_db_prepare_filter() {
    check_admin_referer('forum-adminform_filtertopics', 'forum-adminform_filtertopics');

    $topicdata = array();

	$gcount = SP()->filters->integer($_POST['gcount']);
    $fcount = array_map('intval', array_unique($_POST['fcount']));

	$first = true;
	for ($x=0; $x<$gcount; $x++) {
		for( $y=0; $y<$fcount[$x]; $y++) {
			if (isset($_POST['group'.$x.'forum'.$y])) {
				if ($first) {
					$forum_ids = ' AND ('.SPTOPICS.'.forum_id='.SP()->filters->integer($_POST['group'.$x.'forum'.$y]);
					$first = false;
				} else {
					$forum_ids .= ' OR '.SPTOPICS.'.forum_id='.SP()->filters->integer($_POST['group'.$x.'forum'.$y]);
				}
			}
		}
	}

	$topicdata['message'] = '';
	if ($first) {
        $topicdata['message'] = __('Error - no forum(s) specified for filtering!', 'sp-prune');
	} else {
		$forum_ids .= ')';
	}
	$topicdata['id'] = $forum_ids;

	$xdate = getdate(strtotime(SP()->filters->str($_POST['date'])));
	$filterdate = $xdate['year'].'-'.$xdate['mon'].'-'.$xdate['mday'].' 23:59:59';
	$topicdata['date'] = $filterdate;

	return $topicdata;
}
