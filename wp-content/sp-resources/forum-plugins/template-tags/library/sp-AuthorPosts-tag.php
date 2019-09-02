<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/* 	============================================================================================
	sp_AuthorPostsTag()

	displays all the posts for the specified author id - forum visability rules apply

	parameters:
		name			description								type			default
		----------------------------------------------------------------------------------------
		tagId			unique id to use for div or list		text			spAuthorPostsTag
		tagClass		class to be applied for styling			text			spLinkTag
		authorId		author to show the posts for			number			Required
		showForum		show the forum name						true/false		true
		showDate		show the date of the latest post		true/false		true
		limit			number of posts to return 0 = all		number			5
		listTags		wrap all in ul and items in li tags		true/false		false
		echo			echo content or return content			true/false		true

	NOTE: True must be expressed as a 1 and False as a zero

==============================================================================================*/

function sp_do_sp_AuthorPostsTag($args='') {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	$defs = array('tagId'    	=> 'spAuthorPostsTag',
				  'tagClass' 	=> 'spLinkTag',
				  'authorId'	=> '',
				  'showForum'	=> 1,
				  'showDate'	=> 1,
				  'limit'		=> 5,
				  'listTags'	=> 0,
				  'echo'		=> 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_AuthorPostsTag_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$authorId	= (int) $authorId;
	$showForum	= (int) $showForum;
	$showDate	= (int) $showDate;
	$limit		= (int) $limit;
	$listTags	= (int) $listTags;
	$echo		= (int) $echo;

    if (empty($authorId)) return;
	sp_check_api_support();

	$out = '';

	if ($limit == 0) {
		$limit = '';
	}

	# limit to viewable forums based on permissions
	$where = SPPOSTS.'.user_id = '.$authorId.' AND '.SPPOSTS.'.post_status=0 ';
	$forum_ids = SP()->user->get_forum_memberships();
	# create where clause based on forums that current user can view
	if ($forum_ids != '') {
		$where .= "AND ".SPPOSTS.".forum_id IN (".implode(",", $forum_ids).")";
	} else {
		return '';
	}

	$query = new stdClass();
		$query->table		= SPPOSTS;
		$query->distinct		= true;
		$query->fields		= SPPOSTS.'.post_id, '.SPPOSTS.'.forum_id, '.SPPOSTS.'.topic_id, '.SP()->DB->timezone('post_date').',
							  post_index, forum_slug, forum_name, topic_slug, topic_name';
		$query->join			= array(SPTOPICS.' ON '.SPPOSTS.'.topic_id = '.SPTOPICS.'.topic_id',
									SPFORUMS.' ON '.SPPOSTS.'.forum_id = '.SPFORUMS.'.forum_id');
		$query->where		= $where;
		$query->orderby		= 'post_date DESC';
		$query->limits		= $limit;
	$sfposts = SP()->DB->select($query);

	if(!$listTags) {
		$out = "<div id='$tagId' class='$tagClass'>";
		$open = '<div>';
		$close = '</div>';
	} else {
		$out = "<ul id='$tagId' class='$tagClass'>";
		$open = '<li>';
		$close = '</li>';
	}

	if ($sfposts) {
		foreach ($sfposts as $sfpost) {
			$out.= $open;
			if ($showForum) {
				$out .= SP()->displayFilters->title($sfpost->forum_name).'<br />';
			}
			$out .= '<a href="'.SP()->spPermalinks->build_url($sfpost->forum_slug, $sfpost->topic_slug, 0, $sfpost->post_id, $sfpost->post_index).'">'.SP()->displayFilters->title($sfpost->topic_name).'</a><br />'."\n";
			if ($showDate) {
				$out .= SP()->dateTime->format_date('d', $sfpost->post_date).'<br />';
			}
			$out.= $close;
		}
	} else {
		$out .= $open.__('No posts by this author', 'sp-ttags').$close;
	}
	if(!$listTags) {
		$out .= '</div>';
	} else {
		$out.= '</ul>';
	}

	$out = apply_filters('sph_AuthorPostsTag', $out);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_do_AuthorPostsShortcode($atts) {
    $args = array();
    if (isset($atts['tagid']))          $args['tagId']          = $atts['tagid'];
    if (isset($atts['tagclass']))       $args['tagClass']       = $atts['tagclass'];
    if (isset($atts['authorid']))       $args['authorId']       = $atts['authorid'];
    if (isset($atts['showforum']))      $args['showForum']      = $atts['showforum'];
    if (isset($atts['showdate']))       $args['showDate']       = $atts['showdate'];
    if (isset($atts['limit']))          $args['limit']          = $atts['limit'];
    if (isset($atts['listtags']))       $args['listTags']       = $atts['listtags'];

    $args['echo'] = 0;
    return sp_do_sp_AuthorPostsTag($args);
}
