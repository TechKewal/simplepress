<?php
/*
Simple:Press
PM Thread Class
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
#	sp_pm_has_threadlist()
#	sp_pm_loop_threadlist()
#	sp_pm_threadlist()
#
#	Returns rich data object of threads for current user.
#
#	Instantiate spPmThreadList:
#
#	$spPmThreadList = new spPmThreadList();
#
#	Returns a data object based upon the users PM Threads
#	exposes globals $spPmThreadList and $spThisPmThreadList
#
# --------------------------------------------------------------------------------------

function sp_pm_has_threadlist() {
	global $list, $spPmThreadList;
	return $spPmThreadList->sp_pm_has_threadlist();
}

function sp_pm_loop_threadlist() {
	global $spPmThreadList;
	return $spPmThreadList->sp_pm_loop_threadlist();
}

function sp_pm_threadlist() {
	global $spPmThreadList, $spThisPmThreadList;
	$spThisPmThreadList = $spPmThreadList->sp_pm_threadlist();
}

# --------------------------------------------------------------------------------------

# ==========================================================================================
#
#	PM Thread List (for user)
#
# ==========================================================================================

class spPmThreadList {
	# Status: 'data', 'no access', 'no data', 'opt out'
	var $viewStatus = '';

	# Status: 'exceeded', 'reached', 'approaching', 'okay'
	var $inboxStatus = '';

	# DB query result set
	var $listData = array();

	# used for overall count and quicklinks
	var $allThreads = array();

	# Pm single row object
	var $pmData = '';

	# Internal counter
	var $currentPm = 0;

	# Count of pm records
	var $listCount = 0;

	# Count of pm records
	var $inboxCount = 0;

	# User can send PMs (separate from permission, ie inbox size)
	var $canSendPm = true;

	# Run in class instantiation - populates data
	function __construct() {
		$this->listData = $this->sp_pm_ThreadListView_query();
	}

	# True if there are pm records
	function sp_pm_has_threadlist() {
		if (!empty($this->listData)) {
			$this->listCount = count($this->listData);
			reset($this->listData);
			return true;
		} else {
			return false;
		}
	}

	# Loop control on Post records
	function sp_pm_loop_threadlist() {
		if ($this->currentPm > 0) do_action_ref_array('sph_after_pmthread_list', array(&$this));
		$this->currentPm++;
		if ($this->currentPm <= $this->listCount) {
			do_action_ref_array('sph_before_pmthread_list', array(&$this));
			return true;
		} else {
			$this->currentPm = 0;
			$this->listCount = 0;
			unset($this->listData);
			return false;
		}
	}

	# Sets array pointer and returns current Post data
	function sp_pm_threadlist() {
		$this->pmData = current($this->listData);
		next($this->listData);
		return $this->pmData;
	}

	# --------------------------------------------------------------------------------------
	#
	#	sp_pm_ThreadListView_query()
	#	Builds the data structure for the PM Thread View data object
	#
	# --------------------------------------------------------------------------------------
	function sp_pm_ThreadListView_query() {
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

		$list = array();

		$pm = SP()->options->get('pm');

		# check for paging count
		$this->paging = $pm['threadpaging'];
		if (empty($this->paging)) $this->paging = 15;

		# sort out the page
		$page = (isset($_GET['page'])) ? SP()->filters->integer($_GET['page']) : SP()->rewrites->pageData['page'];
		$startLimit = 0;
		if ($page != 1) $startLimit = ((($page - 1) * $this->paging));
		$limit = $startLimit.', '.$this->paging;

		# query 1 - grab the IDs to be used on this page
		$query = new stdClass();
			$query->table		= SPPMRECIPIENTS;
			$query->fields		= SPPMRECIPIENTS.'.thread_id';
			$query->where		= SPPMRECIPIENTS.'.user_id = '.SP()->user->thisUser->ID;
			$query->groupby		= SPPMRECIPIENTS.'.thread_id';
			$query->orderby		= 'MAX(recipient_id) DESC';
			$query->limits		= $limit;
			$query->type		= 'col';
			$query = apply_filters('sph_pmthread_list_query', $query, $this);
		$threadIds = SP()->DB->select($query);

		if ($threadIds) {
			$idSet = implode(',', $threadIds);

			# do the main select query
			$query = new stdClass();
				$query->table		= SPPMRECIPIENTS;
				$query->fields		= SPPMRECIPIENTS.'.thread_id, '.SPPMRECIPIENTS.'.message_id, read_status, pm_type, '.SPPMMESSAGES.'.user_id as sender,
									  '.SP()->DB->timezone('sent_date').', attachment_id, title, message_count, thread_slug, display_name as sender_display_name';
				$query->join			= array(SPPMMESSAGES.' ON '.SPPMRECIPIENTS.'.message_id = '.SPPMMESSAGES.'.message_id',
											SPPMTHREADS.' ON '.SPPMRECIPIENTS.'.thread_id = '.SPPMTHREADS.'.thread_id',
											SPMEMBERS.' ON '.SPPMMESSAGES.'.user_id = '.SPMEMBERS.'.user_id');
				$query->where		= SPPMRECIPIENTS.'.thread_id IN ('.$idSet.') AND '.SPPMRECIPIENTS.'.user_id = '.SP()->user->thisUser->ID;
				$query->orderby		= 'recipient_id ASC';
				$query = apply_filters('sph_pmthread_select_query', $query, $this);
			$records = SP()->DB->select($query);

			$this->viewStatus = 'data';
			foreach ($threadIds as $thread) {
				$list[$thread]					= new stdClass();
				$list[$thread]->thread_id		= $thread;
				$list[$thread]->message_count 	= 0;
				if ($records) {
					$first = true;
					foreach ($records as $r) {
						if ($r->thread_id == $thread) {
							if ($first) {
								$list[$thread]->title						= SP()->displayFilters->title($r->title);
								$list[$thread]->thread_slug					= $r->thread_slug;
								$list[$thread]->first_message_id			= $r->message_id;
								$list[$thread]->first_sender_id				= $r->sender;
								$list[$thread]->first_sender_date			= $r->sent_date;
								$list[$thread]->first_sender_display_name	= SP()->displayFilters->name($r->sender_display_name);
								$first = false;
							}
							$list[$thread]->message_count++;
							$list[$thread]->read_status				= $r->read_status;
							$list[$thread]->sender_id				= $r->sender;
							$list[$thread]->sender_display_name		= SP()->displayFilters->name($r->sender_display_name);
							$list[$thread]->sent_date				= $r->sent_date;
							$list[$thread]->last_sender_id 			= $r->sender;
						}
					}
				}
			}
		} else {
			$this->viewStatus = 'no data';
		}

		# Need the overall total thread/messages for user
		$query = new stdClass();
			$query->table		= SPPMTHREADS;
			$query->fields		= SPPMTHREADS.'.thread_id, title, COUNT('.SPPMRECIPIENTS.'.user_id) AS message_count, read_status';
			$query->join			= array(SPPMRECIPIENTS.' ON '.SPPMRECIPIENTS.'.thread_id = '.SPPMTHREADS.'.thread_id');
			$query->where		= SPPMRECIPIENTS.'.user_id = '.SP()->user->thisUser->ID;
			$query->groupby		= 'title';
			$query->orderby		= 'recipient_id DESC, thread_id DESC';
			$query = apply_filters('sph_pmthread_count_query', $query, $this);
		$this->allThreads = SP()->DB->select($query);

		$this->inboxCount = count($this->allThreads);
		$this->messageCount = 0;
		foreach ($this->allThreads as $thread) {
			$this->messageCount+=$thread->message_count;
		}

		# check if max inbox size reached
		$this->inboxStatus = 'okay';
		$maxsize = $pm['max'];
		if (!SP()->user->thisUser->admin && $maxsize > 0) {
			if ($this->inboxCount > $maxsize) {
				$this->inboxStatus = 'exceeded';
				$this->canSendPm = false;
			} elseif ($this->inboxCount == $maxsize) {
				$this->inboxStatus = 'reached';
				$this->canSendPm = false;
			} elseif ($this->inboxCount > ($maxsize - 5)) {
				$this->inboxStatus = 'approaching';
			}
		}
		return $list;
	}
}
