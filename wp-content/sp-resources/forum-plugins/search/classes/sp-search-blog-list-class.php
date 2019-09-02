<?php
/*
Simple:Press
Search Post List Class
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# ==========================================================================================
#
#	Returns flat object of blog posts but with rich data
#	Intended for simple listings of blog posts in the blog post search plugin
#
#	Version: 5.3 +
#
# ==========================================================================================

# --------------------------------------------------------------------------------------
#
#	Returns rich data object of blog posts using the passed IDs.
#
#	Instantiate spSearchBlogPostList - The WHERE argument is required
#
#	Pass:	$IDs		Array of blog post id's
#
#	Returns a data object based upon the blog post ids
#
# --------------------------------------------------------------------------------------

function sp_has_blogPostlist() {
	global $list, $spSearchBlogPostList;
	return $spSearchBlogPostList->sp_has_blogPostlist();
}

function sp_loop_blogPostlist() {
	global $spSearchBlogPostList;
	return $spSearchBlogPostList->sp_loop_blogPostlist();
}

function sp_the_blogPostlist() {
	global $spSearchBlogPostList, $spSearchThisBlogPost;
	$spSearchThisBlogPost = $spSearchBlogPostList->sp_the_blogPostlist();
}

# ==========================================================================================
#
#	Post List. Post Listing Class
#
# ==========================================================================================

class spSearchBlogPostList {
	# DB query result set
	var $listData = array();

	# Post single row object
	var $postData = '';

	# Internal counter
	var $currentPost = 0;

	# Count of post records
	var $listCount = 0;

	# Run in class instantiation - populates data
	function __construct($IDs) {
		$this->listData = $this->sp_blogpostlistview_query($IDs);
	}

	# True if there are Post records
	function sp_has_blogPostlist() {
		if (!empty($this->listData)) {
			$this->listCount = count($this->listData);
			reset($this->listData);
			return true;
		} else {
			return false;
		}
	}

	# Loop control on Post records
	function sp_loop_blogPostlist() {
		if ($this->currentPost > 0) do_action_ref_array('sph_after_blog_post_list', array(&$this));
		$this->currentPost++;
		if ($this->currentPost <= $this->listCount) {
			do_action_ref_array('sph_before_blog_post_list', array(&$this));
			return true;
		} else {
			$this->currentPost = 0;
			$this->listCount = 0;
			unset($this->listData);
			return false;
		}
	}

	# Sets array pointer and returns current Post data
	function sp_the_blogPostlist() {
		$this->postData = current($this->listData);
		next($this->listData);
		return $this->postData;
	}

	# --------------------------------------------------------------------------------------
	#
	#	sp_blogpostlistview_query()
	#	Builds the data structure for the Listview data object
	#
	# --------------------------------------------------------------------------------------
	function sp_blogpostlistview_query($IDs) {
		# If no WHERE clause then return empty
		if (empty($IDs)) return;

		$query = new stdClass();
			$query->table		= SPWPPOSTS;
			$query->fields		= 'ID, post_content, '.SP()->DB->timezone('post_date').', post_title, post_author, guid, display_name ';
			$query->join			= array(SPMEMBERS.' ON '.SPWPPOSTS.'.post_author = '.SPMEMBERS.'.user_id');
			$query->where		= 'ID IN ('.implode(',', $IDs).') ';
			$query->orderby		= 'ID DESC';
		$query = apply_filters('sph_search_blog_post_list_query', $query, $this);
		$records = SP()->DB->select($query);

		# Now convert to our data onject
		$list = array();

		if ($records) {
			foreach ($records as $r) {
				$p = $r->ID;
				$list[$p] = new stdClass();
				$list[$p]->ID 				= $p;
				$list[$p]->post_date		= $r->post_date;
				$list[$p]->post_title		= SP()->displayFilters->title($r->post_title);
				$list[$p]->post_author		= $r->post_author;
				$list[$p]->display_name		= SP()->displayFilters->name($r->display_name);
				$list[$p]->permalink		= $r->guid;
				$list[$p]->post_tip 		= SP()->displayFilters->tooltip($r->post_content, 0);
			}
			unset($records);
		}

		return $list;
	}
}
