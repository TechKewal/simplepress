<?php
/*
Simple:Press
Search Blog View Class
$LastChangedDate: 2018-10-17 15:17:49 -0500 (Wed, 17 Oct 2018) $
$Rev: 15756 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# ==========================================================================================
#	Version: 5.3 and above
#
#	Search Blog View Class
#	performs the sql work - passes off a blog posts list
#
# ==========================================================================================

class spSearchBlogView {
	# Search View DB query result set
	var $searchData = array();

	# Count of topic records
	var $searchCount = 0;

	# How many to show per page
	var $searchShow = 0;

	# Some search values from pageData
	var $searchTerm = array();

	# the original, raw term
	var $searchTermRaw = '';

	# Permalink
	var $searchPermalink = '';

	# Page
	var $searchPage = 0;

	# limit
	var $limit = 0;

	# Run in class instantiation - populates data
	function __construct($count=0) {
		$this->searchPermalink = $this->sp_build_search_url();
		$this->searchData = $this->sp_searchview_control($count);
	}

	# --------------------------------------------------------------------------------------
	#
	#	sp_searchview_control()
	#	Builds the data structure for the Searchview data object
	#
	# --------------------------------------------------------------------------------------
	function sp_searchview_control($count) {
		global $wpdb;

		$searchType	= SP()->rewrites->pageData['searchtype'];
		$searchInclude = SP()->rewrites->pageData['searchinclude'];
		$blogPage = SP()->filters->integer($_GET['blog']);
		$this->searchPage = $blogPage;

		# (LIMIT) how many topics per page?
		if (!$count) $count = 20;
		$this->searchShow = $count;
		if ($blogPage == 1) {
			$startlimit = 0;
		} else {
			$startlimit = (($blogPage - 1) * $count);
		}
		# For this page?
		$this->limit = $startlimit.', '.$count;

		if (empty(SP()->rewrites->pageData['searchvalue'])) return '';
		$this->searchTermRaw = SP()->rewrites->pageData['searchvalue'];

		$this->searchTerm = $this->sp_construct_search_term(SP()->filters->esc_sql($wpdb->esc_like(SP()->rewrites->pageData['searchvalue'])), $searchType, $searchInclude);

		# if search type is 1,2 or 3 (i.e., normal data searches) and we are looking for page 1 then we need to run
		# the query. Note - if posts and titles then we need to run it twice!
		# If we are not loading page 1 however then we can grab the results from the cache.
		# For all other searchtypes - just run the standard routine
		if ($searchType > 3) {
			$r = $this->sp_searchview_query($searchType, $searchInclude);
			return $r;
		}

		if ($blogPage == 1 && SP()->rewrites->pageData['newblogsearch'] == true) {
			$r = $this->sp_searchview_query($searchType, $searchInclude);
			# Remove dupes and re-sort
			if ($r) {
				$r = array_unique($r);
				rsort($r, SORT_NUMERIC);
				# Now hive off into a transient
				$d = array();
				$d['url'] = $this->searchPermalink;
				$d['page'] = SP()->rewrites->pageData['searchpage'];
				$t = array();
				$t[0] = $d;
				$t[1] = $r;
				set_transient(sp_get_ip().'blogsearch', $t, 3600);
			}
		} else {
			# Get the data from the cache if not page 1 for first time
			$r = get_transient(sp_get_ip().'blogsearch');
			if ($r) {
				$d = $r[0];
				$r = $r[1];
				$d['url']=$this->searchPermalink;
				$d['page'] = $blogPage;
				$t = array();
				$t[0] = $d;
				$t[1] = $r;
				# update the transient with the new url
				set_transient(sp_get_ip().'blogsearch', $t, 3600);
			}
		}

		# Now work out which part of the $r array to return
		if ($r) {
			SP()->rewrites->pageData['blogsearchresults'] = count($r);
			$this->searchCount = SP()->rewrites->pageData['blogsearchresults'];
			return array_slice($r, $startlimit, $count);
		}
	}

	function sp_searchview_query($searchType, $searchInclude) {
		$TABLE = SPWPPOSTS;
		$WHERE = '(';
		$fc = 'post_content';
		$ft = 'post_title';
		$o = ' OR ';
		if ($searchType == 2) $o = ' AND ';

		# Loop through array of search terms
		for ($x = 0; $x < count($this->searchTerm); $x++) {
			$t = $this->searchTerm[$x];
			$last = ($x == (count($this->searchTerm) - 1)) ? true : false;

			if ($searchInclude == 1) {
				# Include = 1 - posts
				$WHERE.= "$fc LIKE('$t')";
			}

			if ($searchInclude == 2) {
				# Include = 2 - titles
				$WHERE.= "$ft LIKE('$t')";
			}

			if ($searchInclude == 3) {
				# Include = 3 - both posts and titles
				$WHERE.= "$fc LIKE('$t') OR $ft LIKE('$t')";
			}

			if (!$last) $WHERE.= $o;
		}

		$WHERE.= ') ';

		# check if the WHERE clause is empty - probably comes from a legacy url
		if (empty($WHERE)) {
			SP()->notifications->message(1, SP()->primitives->front_text('Unable to complete this search request'));
			return;
		}

        $post_types = array();
    	$options = SP()->options->get('search');
        foreach ($options['searchposttypes'] as $key => $type) {
            if ($type) $post_types[] = $key;
        }
        $searchposttypes = (!empty($post_types)) ? implode("','", $post_types) : '';

		$WHERE.= "AND (post_type IN ('$searchposttypes')) AND (post_status = 'publish') ";

		# Query
		$query = new stdClass();
			$query->table = $TABLE;
			$query->fields = 'ID';
			$query->found_rows = true;
			$query->where = $WHERE;
			$query->type = 'col';
			# Plugins can alter the final SQL
			$query = apply_filters('sph_blog_search_query', $query, $this->searchTerm, $searchType, $searchInclude, $this);
		$records = SP()->DB->select($query);

		SP()->rewrites->pageData['blogsearchresults'] = SP()->DB->select('SELECT FOUND_ROWS()', 'var');
		$this->searchCount = SP()->rewrites->pageData['blogsearchresults'];

		return $records;
	}

	function sp_construct_search_term($term, $type, $include) {
		$w = array();

		# get the search terms(s) in format required
		$term = trim($term, '%');
		if ($type == 1 || $type == 2) {
			$w = explode(' ', $term);
			for($x = 0; $x < count($w); $x++) {
				$w[$x] = '%'.$w[$x].'%';
			}
		} else if ($type == 3) {
			$w[0] = '%'.$term.'%';
		} else {
			return $term;
		}

		$w = apply_filters('sph_blog_search_term', $w, $term, $type, $include);

		return $w;
	}

	# ------------------------------------------------------------------
	# sp_build_search_url()
	#
	# Builds a forum search url with the query vars
	# ------------------------------------------------------------------
	function sp_build_search_url() {
		$s = array();

		$s['forum'] = SP()->filters->str($_GET['forum']);
		$s['value'] = SP()->rewrites->pageData['searchvalue'];
		$s['type'] = SP()->rewrites->pageData['searchtype'];
		$s['include'] = SP()->rewrites->pageData['searchinclude'];

		$s = apply_filters('sph_build_search_url', $s);

		return add_query_arg($s, SP()->spPermalinks->get_url());
	}
}
