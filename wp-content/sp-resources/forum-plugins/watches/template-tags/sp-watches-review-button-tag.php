<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_WatchesReviewButtonTag($args='', $label='', $toolTip='') {
	sp_forum_ajax_support();

	if (!SP()->auths->get('watch', SP()->rewrites->pageData['forumid'])) return;

	$defs = array('tagId'		=> 'spWatchesReviewButton',
				  'tagClass'	=> 'spWatchesReviewButton',
				  'labelClass'	=> 'spInRowLabel',
				  'iconClass'	=> 'spIcon',
				  'icon'		=> 'sp_WatchesReviewButton.png',
				  'first'		=> 0,
				  'popup'		=> 1,
				  'linkId'		=> 'spWatchesLink',
				  'mobileMenu'	=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_WatchesReviewButton_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$labelClass		= esc_attr($labelClass);
	$iconClass		= esc_attr($iconClass);
	$icon			= SP()->theme->paint_icon($iconClass, WIMAGES, sanitize_file_name($icon));
	$first			= (int) $first;
	$popup			= (int) $popup;
	$linkId			= esc_attr($linkId);
	$mobileMenu		= (int) $mobileMenu;
	$label			= SP()->displayFilters->title($label);
	$toolTip		= esc_attr($toolTip);

	$watchCount = 0;
	$br = ($mobileMenu) ? '<br />' : '';

	if (!property_exists(SP()->user->thisUser, 'watches') || empty(SP()->user->thisUser->watches)) {
	    SP()->user->thisUser->watches = SP()->activity->get_col('col=item&type='.SPACTIVITY_WATCH.'&uid='.SP()->user->thisUser->ID);
	}
	$list = SP()->user->thisUser->watches;
	if (!empty($list)) {
		foreach ($list as $topicid) {
			if (sp_is_in_users_newposts($topicid)) $watchCount++;
		}
	}

	if ($mobileMenu) {
		$label = str_ireplace('%COUNT%', $watchCount, $label);
		$label=str_replace(' (', '<br />(', $label);
	}

	$out = '';
	if ($mobileMenu) $out.= sp_open_grid_cell();
	if ($popup) {
		$site = wp_nonce_url(SPAJAXURL."watches-manage&amp;targetaction=watch&amp;first=$first", 'watches-manage');
		$out.= "<a rel='nofollow' id='$tagId' class='$tagClass spWatchesShowTopics' title='$toolTip' data-site='$site' data-label='$toolTip' data-width='700' data-height='500' data-align='center'>";
	} else {
		$out.= "<a rel='nofollow' id='$tagId' class='$tagClass' title='$toolTip' href='".SP()->spPermalinks->get_query_url(SP()->spPermalinks->get_url('watches'))."first=$first"."'>";
	}
	if (!empty($icon)) $out.= $icon.$br;
	$watchClass = ($watchCount > 0) ? 'spWatchCountUnread' : 'spWatchCountRead';
	$out.= "$label	<span id='spWatchCount'><span class='$watchClass'>$watchCount</span></span>";
	$out.= "</a>\n";
	if ($mobileMenu) $out.= sp_close_grid_cell();

	$out = apply_filters('sph_WatchesReviewButton', $out, $a);
	echo $out;
}
