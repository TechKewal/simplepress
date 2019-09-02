<?php
/*
Simple:Press
PM Message Class
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# ==========================================================================================
#
#	Version: 5.3
#
# ==========================================================================================

# --------------------------------------------------------------------------------------
#
#	sp_pm_messagelist()
#	sp_pm_loop_messagelist()
#	sp_pm_the_messagelist()
#
#	Returns rich data object of messages using the passed Thread ID.
#
#	Instantiate spPmMessageList - The threadId argument is required:
#
#	$spPmMessageList = new spPmMessageList($threadId);
#
#	Pass:	$threadId:	The PM thread to display
#
#	Returns a data object based upon the PM thread messages
#	exposes globals $spPmMessageList and $spThisPmMessageList
#
# --------------------------------------------------------------------------------------

function sp_pm_messagelist() {
	global $list, $spPmMessageList;
	return $spPmMessageList->sp_pm_messagelist();
}

function sp_pm_loop_messagelist() {
	global $spPmMessageList;
	return $spPmMessageList->sp_pm_loop_messagelist();
}

function sp_pm_the_messagelist() {
	global $spPmMessageList, $spThisPmMessageList;
	$spThisPmMessageList = $spPmMessageList->sp_pm_the_messagelist();
}

# --------------------------------------------------------------------------------------

# ==========================================================================================
#
#	PM Thread List (for user)
#
# ==========================================================================================

class spPmMessageList {
	# DB query result set
	var $listData = array();

	# Status: 'data', 'no data', 'no access', 'opt out', 'missing thread'
	var $viewStatus = '';

	# Pm single row object
	var $pmData = '';

	# Internal counter
	var $currentPm = 0;

	# Count of pm records
	var $listCount = 0;

	# title, slug and message count
	var $pm_thread_id = 0;
	var $pm_title = '';
	var $pm_slug = '';
	var $pm_count = 0;
	var $pm_unread_count = 0;

	# Run in class instantiation - populates data
	function __construct($threadId=0) {
		$this->listData = $this->sp_pm_MessageListView_query($threadId);
	}

	# True if there are pm records
	function sp_pm_messagelist() {
		if (!empty($this->listData)) {
			$this->listCount = count($this->listData);
			reset($this->listData);
			return true;
		} else {
			return false;
		}
	}

	# Loop control on Post records
	function sp_pm_loop_messagelist() {
		if ($this->currentPm > 0) do_action_ref_array('sph_after_pmmessage_list', array(&$this));
		$this->currentPm++;
		if ($this->currentPm <= $this->listCount) {
			do_action_ref_array('sph_before_pmmessage_list', array(&$this));
			return true;
		} else {
			$this->currentPm = 0;
			$this->listCount = 0;
			unset($this->listData);
			return false;
		}
	}

	# Sets array pointer and returns current Post data
	function sp_pm_the_messagelist() {
		$this->pmData = current($this->listData);
		next($this->listData);
		return $this->pmData;
	}

	# --------------------------------------------------------------------------------------
	#
	#	sp_pm_MessageListView_query()
	#	Builds the data structure for the PM Thread View data object
	#
	# --------------------------------------------------------------------------------------
	function sp_pm_MessageListView_query($threadId=0) {
		# check user being requested can use PM
		if (!sp_pm_get_auth('use_pm')) {
			$this->viewStatus = 'no access';
			return '';
		}

		# check if user has opted out of PM
		if (isset(SP()->user->thisUser->pmoptout) && SP()->user->thisUser->pmoptout) {
			$this->viewStatus = 'opt out';
			return;
		}

		# If no Thread ID then return missing status
		if (empty($threadId)) {
			$this->viewStatus = 'missing thread';
			return '';
		}

		# get user specified sort order
		if (isset(SP()->user->thisUser->pmsortorder) && SP()->user->thisUser->pmsortorder) {
			$SORT = 'ASC';
		} else {
			$SORT = 'DESC';
		}

		$pm = SP()->options->get('pm');

		# check for paging count
		$this->paging = $pm['messagepaging'];
		if (empty($this->paging)) $this->paging = 10;

		# sort out the page
		$page = (isset($_GET['page'])) ? SP()->filters->integer($_GET['page']) : SP()->rewrites->pageData['page'];
		if (empty($page)) {
			$page = 1;
			SP()->rewrites->pageData['page'] = 1;
		}
		$startLimit = 0;
		if ($page != 1) $startLimit = ((($page - 1) * $this->paging));
		$limit = $startLimit.', '.$this->paging;

		$query = new stdClass();
			$query->table		= SPPMRECIPIENTS;
			$query->distinct		= true;
			$query->fields		= 'message_id';
			$query->where		= 'thread_id = '.$threadId.' AND user_id = '.SP()->user->thisUser->ID;
			$query->orderby		= "message_id $SORT";
			$query->type			= 'col';
		$query = apply_filters('sph_pmmessage_list_query', $query, $this);

		$messageIds = SP()->DB->select($query);

		if ($messageIds) {

			$idSet = implode(',', $this->sp_create_id_list($messageIds, $startLimit, $this->paging));

			$query = new stdClass();
				$query->table		= SPPMRECIPIENTS;
				$query->fields		= SPPMRECIPIENTS.'.thread_id, '.SPPMMESSAGES.'.user_id AS sender, mem1.display_name AS sender_display_name, '
									  .SPPMRECIPIENTS.'.user_id AS recipient, mem2.display_name AS recipient_display_name, '
									  .SPPMRECIPIENTS.'.message_id, read_status, pm_type, '.SP()->DB->timezone('sent_date').', attachment_id, title, message_count,
									  thread_slug, message';
				$query->join			= array(SPPMMESSAGES.' ON '.SPPMRECIPIENTS.'.message_id = '.SPPMMESSAGES.'.message_id',
											SPPMTHREADS.' ON '.SPPMRECIPIENTS.'.thread_id = '.SPPMTHREADS.'.thread_id',
											SPMEMBERS.' AS mem1 ON '.SPPMMESSAGES.'.user_id = mem1.user_id',
											SPMEMBERS.' AS mem2 ON '.SPPMRECIPIENTS.'.user_id = mem2.user_id');
				$query->where		= SPPMRECIPIENTS.'.message_id IN ('.$idSet.')';
				$query->orderby		= "message_id $SORT";
			$query = apply_filters('sph_pmmessage_select_query', $query, $this);
			$records = SP()->DB->select($query);

			$list = array();

			foreach ($records as $r) {
				# new Message for current user
				$this->pm_thread_id					=	$r->thread_id;
				$this->pm_title						=	SP()->displayFilters->title($r->title);
				$this->pm_slug						=	$r->thread_slug;
				$this->pm_count						=	count($messageIds);

				if (!isset($list[$r->message_id])) $list[$r->message_id] = new stdClass();
				$list[$r->message_id]->message_id				=	$r->message_id;
				$list[$r->message_id]->sender					=	$r->sender;
				$list[$r->message_id]->sender_display_name		=	SP()->displayFilters->name($r->sender_display_name);
				$list[$r->message_id]->read_status				=	($r->sender != SP()->user->thisUser->ID) ? $r->read_status : 1;
				$list[$r->message_id]->pm_type					=	$r->pm_type;
				$list[$r->message_id]->sent_date				=	$r->sent_date;
				$list[$r->message_id]->attachment_id			=	$r->attachment_id;
				$list[$r->message_id]->message					=	SP()->displayFilters->content($r->message);

				$list[$r->message_id]->recipients[$r->recipient] = new stdClass();
				$list[$r->message_id]->recipients[$r->recipient]->recipient_id			   =	$r->recipient;
				$list[$r->message_id]->recipients[$r->recipient]->recipient_display_name   =	SP()->displayFilters->name($r->recipient_display_name);
				$list[$r->message_id]->recipients[$r->recipient]->pm_type				   =	SP()->displayFilters->name($r->pm_type);

				if (!$r->read_status) $this->pm_unread_count++;

				$list[$r->message_id] = apply_filters('sph_pmmessage_list_record', $list[$r->message_id], $r);
			}

			if ($this->pm_count > 0) {
				$this->viewStatus = 'data';
			} else {
				$this->viewStatus = 'no data';
			}
		} else {
			$this->viewStatus = 'no data';
		}

		return $list;
	}

	function sp_create_id_list($ids, $start, $page) {
		if ($start-1 > count($ids)) return '';
		$list = array();
		for ($x = ($start); $x < ($start + $page); $x++) {
			if (isset($ids[$x])) $list[] = $ids[$x];
		}
		return $list;
	}
}
