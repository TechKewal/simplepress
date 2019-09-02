<?php
/*
Simple:Press
Desc: Privacy - Personal Data Export
$LastChangedDate: 2017-08-05 06:56:34 +0100 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_pm_privacy_do_message_listing($exportItems, $spUserData, $groupID, $groupLabel, $page, $number, $done) {
	$ops = SP()->options->get('pm');
	if($ops['pmexport'] == false) return $exportItems;

	$data = array();
	
	# Select forum posts
	$query				= new stdClass();
	$query->type		= 'set';
	$query->table		= SPPMMESSAGES;
	$query->fields		= 'sent_date, message, title';
	$query->join		= SPPMTHREADS.' ON '.SPPMMESSAGES.'.thread_id = '.SPPMTHREADS.'.thread_id';
	$query->where		= SPPMMESSAGES.".user_id = $spUserData->ID";
	$query->orderby		= SPPMMESSAGES.'.thread_id, '.SPPMMESSAGES.'.message_id';
	$query->limit		= $page.', '.$number;

	$messages = SP()->DB->select($query);

	if (empty($messages) && $page==0) {
		$data[] = array(
			'name'	=>	__('No private messages', 'sp-pm'),
			'value'	=>	'',
			'done'	=> true
		);
		$done = true;
	} elseif (empty($messages)) {
		$data[] = array(
			'name'	=>	'',
			'value'	=>	'',
			'done'	=> true
		);
		$done = true;
	} else {
		foreach($messages as $message) {
			$nameValue = SP()->displayFilters->title($message->title.' - '.$message->sent_date);
			$data[] = array(
				'name'	=> $nameValue,
				'value'	=> SP()->displayFilters->content($message->message)
			);
		}
	}
	
	# Now to export the forum post data
	$exportItems[] = array(
		'group_id'	=> __('Private Messages', 'sp-pm'),
		'group_label'	=> __('Private Messages', 'sp-pm'),
		'item_id' => __('Private Messages', 'sp-pm'),
		'data' => $data,
		'done' => $done
	);

	return $exportItems;
}

function sp_pm_privacy_do_option() {
	$ops = SP()->options->get('pm');
	$pmexport = $ops['pmexport'];
	return spa_paint_checkbox(__('Include Private Messages in Data Export', 'sp-pm'), 'pmexport', $pmexport);
}

function sp_pm_privacy_do_option_save() {
	$ops = SP()->options->get('pm');
	$ops['pmexport'] = isset($_POST['pmexport']);
	SP()->options->update('pm', $ops);
}
