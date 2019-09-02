<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_PostIndexRatePostTag($args='') {
	# are we using glyphs or images?
	$glyphs = current_theme_supports('sp-theme-glyphs');

	# bail if post rating not enabled for current forum
	if (!SP()->forum->view->thisTopic->post_ratings) return;

	$defs = array('tagId' 		=> 'spPostIndexPostRating%ID%',
				  'tagClass' 	=> 'spPostRating',
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PostIndexRatePost_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);

	$tagId = str_ireplace('%ID%', SP()->forum->view->thisPost->post_id, $tagId);

	$out = '';
	$postid = SP()->forum->view->thisPost->post_id;
	$postratings = SP()->options->get('postratings');
	$out.= "<div id='$tagId' class='$tagClass'>";
	if (!isset(SP()->forum->view->thisPost->rating_id)) {
		$ratings = 0;
		$votes = 0;
		$voted = false;
	} else {
		$ratings = SP()->forum->view->thisPost->ratings_sum;
		$votes = SP()->forum->view->thisPost->vote_count;
		if (SP()->user->thisUser->member) {
			$members = unserialize(SP()->forum->view->thisPost->members);
			if ($members) {
				$voted = array_search(SP()->user->thisUser->ID, $members);
			} else {
				$voted = -1;
			}
		} else {
			$ips = unserialize(SP()->forum->view->thisPost->ips);
			if ($ips) {
				$voted = array_search(sp_get_ip(), $ips);
			} else {
				$voted = -1;
			}
		}
	}

	if ($postratings['ratingsstyle'] == 1) { # thuumb rating
		$uptext = __('Rate this post up', 'sp-rating');
		$downtext = __('Rate this post down', 'sp-rating');
		$out.= '<div class="spPostRatingContainer spPostRatingThumbs">';
        $site = wp_nonce_url(SPAJAXURL.'rating-manage&amp;fid='.SP()->forum->view->thisTopic->forum_id.'&amp;tid='.SP()->forum->view->thisTopic->topic_id."&amp;pid=$postid&amp;rate=down", 'rating-manage');
		$downlink = 'style="background: transparent;cursor: pointer;" data-site="'.$site.'" data-postid="'.$postid.'" data-type="1" ';
        $site = wp_nonce_url(SPAJAXURL.'rating-manage&amp;fid='.SP()->forum->view->thisTopic->forum_id.'&amp;pid='.$postid.'&amp;rate=up&amp;tid='.SP()->forum->view->thisTopic->topic_id, 'rating-manage');
		$uplink = 'style="background: transparent;cursor: pointer;" data-site="'.$site.'" data-postid="'.$postid.'" data-type="1" ';
		$downimg = SP()->theme->paint_icon('', PRIMAGES, 'sp_RateDown.png', '', true);
		$upimg = SP()->theme->paint_icon('', PRIMAGES, 'sp_RateUp.png', '', true);

		if (is_numeric($voted) || !SP()->auths->get('rate_posts', SP()->forum->view->thisTopic->forum_id) || SP()->forum->view->thisPost->user_id == SP()->user->thisUser->ID) {
			$uptext = __('Current post rating', 'sp-rating').': '.$ratings;
			$downtext = $uptext;
			$downlink = 'style="background: transparent;"';
			$uplink = 'style="background: transparent;"';
			$downimg = SP()->theme->paint_icon('', PRIMAGES, 'sp_RateDownGrey.png', '', true);
			$upimg = SP()->theme->paint_icon('', PRIMAGES, 'sp_RateUpGrey.png', '', true);
		}

        if ($ratings > 0) $ratings = '+'.$ratings;
		$out.= "<span class='spPostRatingScore'>$ratings</span>";
		$out.= "<span class='spPostRateDown'>";
		if ($glyphs) {
			$out.= "<span class='spIcon $downimg spPostRatingThumbRate' title='$downtext' $downlink></span>";
		} else {
			$out.= "<img class='spIcon spPostRatingThumbRate' src='$downimg' alt='' title='".esc_attr($downtext)."' $downlink />";
		}
		$out.= "</span>";
		$out.= "<span class='spPostRateUp'>";
		if ($glyphs) {
			$out.= "<span class='spIcon $upimg spPostRatingThumbRate' title='$uptext' $uplink></span>";
		} else {
			$out.= "<img class='spIcon spPostRatingThumbRate' src='$upimg' alt='' title='".esc_attr($uptext)."' $uplink />";
		}
		$out.= "</span>";
	} else {
		$out.= '<div class="spPostRatingContainer spPostRatingStars">';
		$offimg = SP()->theme->paint_icon('', PRIMAGES, 'sp_RateStarOff.png', '', true);
		$onimg = SP()->theme->paint_icon('', PRIMAGES, 'sp_RateStarOn.png', '', true);
		$overimg = SP()->theme->paint_icon('', PRIMAGES, 'sp_RateStarOver.png', '', true);

		if ($votes) {
			$star_rating = round($ratings / $votes, 1);
		} else {
			$star_rating = 0;
		}
		$intrating = floor($star_rating);
		$out.= "<span class='spPostRatingScore'>$star_rating</span>";
		$out.= '<span class="spPostRateStars">';
	    for ($x = 1; $x <= $intrating; $x++) {
			$name = ' id="star-'.$postid.'-'.$x.'"';
			if (is_numeric($voted) || !SP()->auths->get('rate_posts', SP()->forum->view->thisTopic->forum_id) || SP()->forum->view->thisPost->user_id == SP()->user->thisUser->ID) {
				$link = 'style="background: transparent;"';
				$text = __('Current post rating', 'sp-rating').': '.$star_rating;
			} else {
				if ($x == 1) $text = __('Rate this post 1 star', 'sp-rating');
				if ($x == 2) $text = __('Rate this post 2 stars', 'sp-rating');
				if ($x == 3) $text = __('Rate this post 3 stars', 'sp-rating');
				if ($x == 4) $text = __('Rate this post 4 stars', 'sp-rating');
				if ($x == 5) $text = __('Rate this post 5 stars', 'sp-rating');
                $site = wp_nonce_url(SPAJAXURL.'rating-manage&fid='.SP()->forum->view->thisTopic->forum_id."&pid=$postid&rate=$x".'&tid='.SP()->forum->view->thisTopic->topic_id, 'rating-manage');
				$link = 'style="background:transparent;cursor: pointer;" data-site="'.$site.'" data-postid="'.$postid.'" data-type="2" data-stars="'.$x.'" data-img="'.$overimg.'" data-glyphs="'.$glyphs.'" data-cur="'.$intrating.'" data-on="'.$onimg.'" data-off="'.$offimg.'" ';
			}
			if ($glyphs) {
				$out.= "<span $name class='spIcon $onimg spPostRatingStarRate' title='$text' $link></span>";
			} else {
				$out.= '<img'.$name.' class="spIcon spPostRatingStarRate" src="'.$onimg.'" alt="" title="'.esc_attr($text).'" '.$link.'/>';
			}
		}

	    for ($x = ($intrating + 1); $x <= 5; $x++) {
			$name = ' id="star-'.$postid.'-'.$x.'"';
			if (is_numeric($voted)  || !SP()->auths->get('rate_posts', SP()->forum->view->thisTopic->forum_id) || SP()->forum->view->thisPost->user_id == SP()->user->thisUser->ID) {
				$link = 'style="background: transparent;"';
				$text = __("Current post rating: ", 'sp-rating').$star_rating;
			} else {
				if ($x == 1) $text = __('Rate this post 1 star', 'sp-rating');
				if ($x == 2) $text = __('Rate this post 2 stars', 'sp-rating');
				if ($x == 3) $text = __('Rate this post 3 stars', 'sp-rating');
				if ($x == 4) $text = __('Rate this post 4 stars', 'sp-rating');
				if ($x == 5) $text = __('Rate this post 5 stars', 'sp-rating');
                $site = wp_nonce_url(SPAJAXURL.'rating-manage&fid='.SP()->forum->view->thisTopic->forum_id.'&tid='.SP()->forum->view->thisTopic->topic_id."&pid=$postid&rate=$x", 'rating-manage');
				$link = 'style="background:transparent;cursor: pointer;" data-site="'.$site.'" data-postid="'.$postid.'" data-type="2" data-stars="'.$x.'" data-img="'.$overimg.'" data-glyphs="'.$glyphs.'" data-cur="'.$intrating.'" data-on="'.$onimg.'" data-off="'.$offimg.'" ';
			}
			if ($glyphs) {
				$out.= "<span $name class='spIcon $offimg spPostRatingStarRate' title='$text' $link></span>";
			} else {
				$out.= '<img'.$name.' class="spIcon spPostRatingStarRate" src="'.$offimg.'" alt="" title="'.esc_attr($text).'" '.$link.'/>';
			}
		}
		$out.= '</span>';
	}
	$out.= '</div>';
	$out.= '</div>';

	$out = apply_filters('sph_PostIndexRatePost', $out, $a);
	echo $out;
}
