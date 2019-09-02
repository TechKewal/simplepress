<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_TopicIndexRatingTag($args='', $summaryLabel='') {
	# bail if post rating not enabled for current forum
	if (SP()->forum->view->thisForum) {
	   if (!SP()->forum->view->thisForum->post_ratings) return;
    } else if (SP()->forum->view->thisTopic && !SP()->forum->view->thisTopic->post_ratings) {
        return;
    }

    require_once PRLIBDIR.'sp-rating-database.php';

	$defs = array('tagId' 		=> 'spTopicIndexRating%ID%',
				  'tagClass' 	=> 'spTopicRating',
				  'statusClass'	=> 'spIconNoAction',
				  'thumbClass'	=> 'spTopicRatingThumbs',
				  'starClass'	=> 'spTopicRatingStars',
                  'skipZero'    => '0',
                  'stacked'     => '0',
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_spTopicIndexRating_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$statusClass	= esc_attr($statusClass);
	$thumbClass	    = esc_attr($thumbClass);
	$starClass	    = esc_attr($starClass);
	$skipZero       = (int) $skipZero;
	$stacked        = (int) $stacked;
	if (!empty($summaryLabel)) 	$summaryLabel = SP()->displayFilters->title($summaryLabel);

	$tagId = str_ireplace('%ID%', SP()->forum->view->thisTopic->topic_id, $tagId);

	$out = '';

	$postratings = SP()->options->get('postratings');
	$sum = 0;
	$count = 0;
	$ratings = sp_get_topic_ratings(SP()->forum->view->thisTopic->topic_id);
	if ($ratings) {
		# get overall topic rating
		foreach ($ratings as $rating) {
			$sum += $rating->ratings_sum;
			$count += $rating->vote_count;
		}
	}

    if ($skipZero && $count == 0) return;

	# display the topic rating
	if ($postratings['ratingsstyle'] == 1) { # thumb rating
        $sumtext = ($sum > 0) ? '+'.$sum : $sum;
        $text = "$summaryLabel $sumtext";

        # calculate aggregate rating
		$downimg = SP()->theme->paint_icon("spIcon $statusClass", PRIMAGES, 'sp_RateDown.png', $text);
		$upimg = SP()->theme->paint_icon("spIcon $statusClass", PRIMAGES, 'sp_RateUp.png', $text);
   		$out.= "<div id='$tagId' class='$tagClass $thumbClass'>";
        if (SP()->rewrites->pageData['pageview'] == 'topic') {
            # display the icons
    		$out.= '<span class="spPostRateThumbs">';
    		if ($sum < 0) {
				$out.= $downimg;
    		} else {
				$out.= $upimg;
    		}
    		$out.= '</span>';

            # aggregate summary
            if ($stacked) $out.= '<br />';
            $out.= '<span class="hreview-aggregate">';
            $out.= "<span class='rating'> $summaryLabel ";
            $out.= "<span class='average'> $sumtext </span>";
            $out.= '<span class="best">';
            $out.= "<span class='value-title' title='$count'></span>";
            $out.= '</span>';
            $out.= '<span class="worst">';
            $out.= "<span class='value-title' title='-$count'></span>";
            $out.= '</span>';
            $out.= "<span class='count'> ($count </span>".__('votes', 'sp-rating');
            $out.= ')&nbsp;</span>';
            $out.= '</span>';
        } else {
    		if ($sum < 0) {
				$out.= $downimg;
    		} else {
				$out.= $upimg;
    		}
    		$out.= "<span class='average'>$sumtext</span>";
        }
   		$out.= '</div>';
	} else {  # stars
        # calculate aggregate rating
		if ($count) {
			$star_rating = round($sum / $count, 1);
			$text = "$summaryLabel $star_rating";
			$img = SP()->theme->paint_icon('spIcon', PRIMAGES, 'sp_RateStarOn.png', $text);
		} else {
			$star_rating = 0;
			$text = "$summaryLabel $star_rating";
			$img = SP()->theme->paint_icon('spIcon', PRIMAGES, 'sp_RateStarOff.png', $text);
		}
		$out.= "<div id='$tagId' class='$tagClass $starClass'>";
        if (SP()->rewrites->pageData['pageview'] == 'topic') {
    		$offimg = SP()->theme->paint_icon('spIcon', PRIMAGES, 'sp_RateStarOff.png', $text);
	       	$onimg = SP()->theme->paint_icon('spIcon', PRIMAGES, 'sp_RateStarOn.png', $text);
    		$intrating = floor($star_rating);
    		$out.= '<span class="spPostRateStars">';

            # display the icons
    	    for ($x = 1; $x <= $intrating; $x++) {
				$out.= $onimg;
    		}
    	    for ($x = ($intrating+1); $x <= 5; $x++) {
				$out.= $offimg;
    		}
    		$out.= '</span>';

            # aggregate summary
            if ($stacked) $out.= '<br />';
            $out.= '<span class="hreview-aggregate">';
            $out.= "<span class='rating'> $summaryLabel ";
            $out.= "<span class='average'> $star_rating </span>";
            $out.= '</span>';
            $out.= "<span class='count'> ($count </span>".__('votes', 'sp-rating');
            $out.= ')&nbsp;</span>';
        } else {
			$out.= $img;
    		$out.= "<span class='average'>$star_rating</span>";
        }
		$out.= '</div>';
	}

	$out = apply_filters('sph_spTopicIndexRating', $out, $a);
	echo $out;
}
