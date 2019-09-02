<?php
/*
Simple:Press
Post Rating Ajax Stuff
$LastChangedDate: 2018-10-17 15:17:49 -0500 (Wed, 17 Oct 2018) $
$Rev: 15756 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

if (!sp_nonce('rating-manage')) die();

if (!SP()->auths->get('rate_posts')) die();

$fid = SP()->filters->integer($_GET['fid']);
$pid = SP()->filters->integer($_GET['pid']);
$tid = SP()->filters->integer($_GET['tid']);

if (empty($fid) || empty($pid) || empty($tid)) die();

if (!SP()->auths->get('rate_posts', $fid)) die();

require_once PRLIBDIR.'sp-rating-database.php';

$rating_data = SP()->DB->table(SPRATINGS, "post_id=$pid");
if (empty($rating_data)) {
	$rating_sum = 0;
	$votes = 0;
	$ips = array();
	$members = array();
} else {
	$rating_sum = $rating_data[0]->ratings_sum;
	$ips = unserialize($rating_data[0]->ips);
	$members = unserialize($rating_data[0]->members);
	$votes = $rating_data[0]->vote_count;
}
$votes++; # add the vote

$out = '';
$postratings = SP()->options->get('postratings');
if ($postratings['ratingsstyle'] == 1) { # thumb rating
	$rate = SP()->filters->str($_GET['rate']);
	if ($rate == 'up') $rating_sum++; else $rating_sum--;
	if ($rate == 'up') $add=true; else $add=false;
    if ($rating_sum > 0) $rating_sum = '+'.$rating_sum;
	$out.= '<div class="spPostRatingContainer spPostRatingThumbs">';
	$out.= "<span class='spPostRatingScore'>$rating_sum</span>";
	$text = __('Current post rating', 'sp-rating').': '.$rating_sum;
	$out.= '<span class="spPostRateDown">'.SP()->theme->paint_icon('spIcon', PRIMAGES, "sp_RateDownGrey.png", esc_attr($text)).'</span>';
	$out.= '<span class="spPostRateUp">'.SP()->theme->paint_icon('spIcon', PRIMAGES, "sp_RateUpGrey.png", esc_attr($text)).'</span>';
	$out.= '</div>';
} else { # start rating
	$add = true;
	$star_rating = SP()->filters->integer($_GET['rate']);
	$rating_sum = $rating_sum + $star_rating;
	$newrating = round($rating_sum / $votes, 1);
	$intrating = floor($newrating);
	$out.= '<div class="spPostRatingContainer spPostRatingStars">';
	$out.= "<span class='spPostRatingScore'>$newrating</span>";
	$out.= '<span class="spPostRateStars">';
	$text = __('Current post rating', 'sp-rating').': '.$newrating;
    for ($x = 0; $x < $intrating; $x++) {
		$out.= SP()->theme->paint_icon('spIcon', PRIMAGES, "sp_RateStarOn.png", esc_attr($text));
	}
    for ($x = 0; $x < (5 - $intrating); $x++) {
		$out.= SP()->theme->paint_icon('spIcon', PRIMAGES, "sp_RateStarOff.png", esc_attr($text));
	}
	$out.= '</span>';
	$out.= '</div>';
}

echo $out;

if (SP()->user->thisUser->member) {
	$members[] = SP()->user->thisUser->ID;
} else {
	$ips[] = sp_get_ip();
}

if ($members) $members = serialize($members); else $members = null;
if ($ips) $ips = serialize($ips); else $ips = null;

if ($votes == 1) {
	sp_add_postratings($pid, $votes, $rating_sum, $ips, $members);
} else {
	sp_update_postratings($pid, $votes, $rating_sum, $ips, $members);
}

#record the vote in users members profile
sp_add_postrating_vote($pid, $tid);

do_action('sph_post_rating_actions', $pid, $add);

die();
