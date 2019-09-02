<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/* 	=====================================================================================
	sp_rating_do_highest_rated_posts_tag($limit, $forum, $user, $postdate, $listtags, $forumids)

	displays the highest rated posts

	parameters:

		$limit			How many items to show in the list		number			10
		$forum			Not used             					true/false		false
		$user			Show the Users Name						true/false		true
		$postdate		Show date of posting					true/false		false
		$listtags		Wrap in <li> tags (li only)				true/false		true
		$forumids		comma delimited list of forum id's		optional		0
 	===================================================================================*/
function sp_rating_do_highest_rated_posts_tag($limit=10, $forum=true, $user=true, $postdate=true, $listtags=true, $forumids=0, $rating=true, $count=true) {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

    $limit = SP()->filters->integer($limit);
    if (empty($limit)) return;

	$out = '';

	# are we passing forum ID's?
	if ($forumids == 0) {
		$where = '';
	} else {
		$flist = explode(',', $forumids);
		$where = ' WHERE (';
		$x = 0;
		for ($x; $x<count($flist); $x++) {
			$where.= SPPOSTS.'.forum_id='.$flist[$x];
			if ($x != count($flist)-1)
                $where.= ' OR ';
            else
                $where.= ')';
		}
	}

	# limit to viewable forums based on permissions
	$forum_ids = SP()->user->get_forum_memberships(SP()->user->thisUser->ID);

	# create where clause based on forums that current user can view
	if ($forum_ids != '') {
		if ($where == '') {
			$where = ' WHERE ';
		} else {
			$where.= ' AND ';
		}
		$where .= SPPOSTS.'.forum_id IN ('.implode(',', $forum_ids).') = 1';
	} else {
		return '';
	}

	# how to order
	$postratings = SP()->options->get('postratings');
	if ($postratings['ratingsstyle'] == 1) { # thumb rating
		$order = 'ORDER BY ratings_sum DESC';
	} else {
		$order = 'ORDER BY (ratings_sum / vote_count) DESC';
	}

	$sfposts = SP()->DB->select('SELECT '.SPRATINGS.'.post_id, ratings_sum, vote_count, '.SPPOSTS.'.topic_id, '.SPPOSTS.'.forum_id, '.SPPOSTS.'.user_id, post_date, post_index, topic_slug, topic_name, forum_slug, forum_name, display_name, guest_name
		FROM '.SPRATINGS.'
		JOIN '.SPPOSTS.' ON '.SPRATINGS.'.post_id = '.SPPOSTS.'.post_id
		JOIN '.SPTOPICS.' ON '.SPPOSTS.'.topic_id = '.SPTOPICS.'.topic_id
		JOIN '.SPFORUMS.' ON '.SPPOSTS.'.forum_id = '.SPFORUMS.'.forum_id
		LEFT JOIN '.SPMEMBERS.' ON '.SPPOSTS.'.user_id = '.SPMEMBERS.'.user_id
		'.$where.'
		'.$order.'
		LIMIT '.$limit);

	if ($sfposts) {
		foreach ($sfposts as $sfpost) {
			if (SP()->auths->can_view($sfpost->forum_id, 'topic-title')) {
			# Start contruction
			if ($listtags) $out.= '<li class="sftagli">';
            $out.= ' '.__('In forum', 'sp-rating').' '.SP()->displayFilters->title($sfpost->forum_name).', '.__('topic', 'sp-rating').' ';

			$out .= '<a href="'.SP()->spPermalinks->build_url($sfpost->forum_slug, $sfpost->topic_slug, 0, $sfpost->post_id, $sfpost->post_index).'">';
			$out.= SP()->displayFilters->title($sfpost->topic_name);

			$out.='</a>';
			if ($postdate) {
				$out.= ' '.__('created', 'sp-rating').' '.SP()->dateTime->format_date('d', $sfpost->post_date);
			}
			if ($user) {
				$out.= ' '.__('by', 'sp-rating').' ';
				$poster = SP()->user->name_display($sfpost->user_id, SP()->displayFilters->name($sfpost->display_name));
				if (empty($poster)) $poster = SP()->displayFilters->name($sfpost->guest_name);
				$out.= $poster;
   				if ($rating) {
                	if ($postratings['ratingsstyle'] == 1) { # thumb rating
                		$value = $sfpost->ratings_sum;
                	} else {
                		$value = round($sfpost->ratings_sum / $sfpost->vote_count, 2);
                	}
                    $out.= ' (';
                    $out.= __('rating of', 'sp-rating').' '.$value;
                    if ($count) $out.= ' '.__('with', 'sp-rating').' '.$sfpost->vote_count.' '.__('ratings', 'sp-rating');
                    $out.= ')';
                }
			}
			if ($listtags) $out.= '</li>';
		}
		}
	} else {
		if ($listtags) $out.= '<li class="sftagli">';
		$out.= __('No rated posts to display', 'sp-rating');
		if ($listtags) $out.= '</li>';
	}
    $out = apply_filters('sph_highest_rated_post_tag', $out, $sfpost);
	echo $out;
}
