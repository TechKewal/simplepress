<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# 5.2 - mobileMenu arg added

function sp_WatchesWatchButtonTag($args='', $watchLabel='', $stopWatchLabel='', $watchToolTip='', $stopWatchToolTip='') {
	# can be empty if request is for a bogus topic slug
	if(empty(SP()->forum->view->thisTopic)) return;

	sp_forum_ajax_support();

	if (!SP()->user->thisUser->member || !SP()->auths->get('watch', SP()->rewrites->pageData['forumid']) || SP()->rewrites->pageData['pageview'] != 'topic' || SP()->core->forumData['lockdown']) return;

	$defs = array('tagClass' 		=> 'spWatchesWatchButton',
                  'tagId' 	     	=> 'spWatchesWatchButton',
				  'labelClass'		=> 'spInRowLabel',
				  'iconClass'		=> 'spIcon',
				  'watchIcon'		=> 'sp_WatchesWatchButton.png',
				  'stopWatchIcon'	=> 'sp_WatchesStopWatchButton.png',
				  'mobileMenu'		=> 0
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_WatchesWatchButton_args', $a);
	extract($a, EXTR_SKIP);

	$p = (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? WIMAGESMOB : WIMAGES;

	# sanitize before use
	$tagId			    = esc_attr($tagId);
	$tagClass			= esc_attr($tagClass);
	$labelClass			= esc_attr($labelClass);
	$iconClass			= esc_attr($iconClass);
	$watchIcon			= SP()->theme->paint_file_icon($p, sanitize_file_name($watchIcon));
	$stopWatchIcon		= SP()->theme->paint_file_icon($p, sanitize_file_name($stopWatchIcon));
	$watchLabel			= SP()->displayFilters->title($watchLabel);
	$stopWatchLabel		= SP()->displayFilters->title($stopWatchLabel);
	$watchToolTip		= esc_attr($watchToolTip);
	$stopWatchToolTip	= esc_attr($stopWatchToolTip);
	$mobileMenu			= (int) $mobileMenu;

	$out = '';
	if (sp_watches_is_watching(SP()->rewrites->pageData['topicid'])) {
        $url = htmlspecialchars_decode(wp_nonce_url(SPAJAXURL.'watches-manage&amp;targetaction=watch-del&amp;topic='.SP()->rewrites->pageData['topicid'], 'watches-manage'));
		if (!$mobileMenu) {
			$out.= '<a rel="nofollow" id="'.$tagId.'" class="'.$tagClass.' spWatchesStopWatching" title="'.$stopWatchToolTip.'" data-url="'.$url.'" data-target="'.$tagId.'" data-stopicon="'.$stopWatchIcon.'" data-watchicon="'.$watchIcon.'" data-stoplabel="'.$stopWatchLabel.'" data-watchlabel="'.$watchLabel.'" data-stoptip="'.$stopWatchToolTip.'" data-watchtip="'.$watchToolTip.'">';
			if (!empty($stopWatchIcon)) $out.= "<img class='$iconClass' src='".$stopWatchIcon."' alt='' />";
			$out.= '<span>'.$stopWatchLabel."</span></a>\n";
		} else {
			$out.= '<li><a rel="nofollow" class="spWatchesStopWatching" data-url="'.$url.'" data-target="'.$tagId.'" data-stopicon="'.$stopWatchIcon.'" data-watchicon="'.$watchIcon.'" data-stoplabel="'.$stopWatchLabel.'" data-watchlabel="'.$watchLabel.'" data-stoptip="'.$stopWatchToolTip.'" data-watchtip="'.$watchToolTip.'">'.$stopWatchLabel.'</li></a>'."\n";
		}
	} else {
        $url = htmlspecialchars_decode(wp_nonce_url(SPAJAXURL.'watches-manage&amp;targetaction=watch-add&amp;topic='.SP()->rewrites->pageData['topicid'], 'watches-manage'));
		if (!$mobileMenu) {
			$out.= '<a rel="nofollow" id="'.$tagId.'" class="'.$tagClass.' spWatchesStartWatching" title="'.$watchToolTip.'" data-url="'.$url.'" data-target="'.$tagId.'" data-stopicon="'.$stopWatchIcon.'" data-watchicon="'.$watchIcon.'" data-stoplabel="'.$stopWatchLabel.'" data-watchlabel="'.$watchLabel.'" data-stoptip="'.$stopWatchToolTip.'" data-watchtip="'.$watchToolTip.'">';
			if (!empty($watchIcon)) $out.= "<img class='$iconClass' src='".$watchIcon."' alt='' />";
			$out.= '<span>'.$watchLabel."</span></a>\n";
		} else {
			$out.= '<li><a rel="nofollow" class="spWatchesStartWatching" data-url="'.$url.'" data-target="'.$tagId.'" data-stopicon="'.$stopWatchIcon.'" data-watchicon="'.$watchIcon.'" data-stoplabel="'.$stopWatchLabel.'" data-watchlabel="'.$watchLabel.'" data-stoptip="'.$stopWatchToolTip.'" data-watchtip="'.$watchToolTip.'">'.$watchLabel.'</li></a>'."\n";
		}
	}

	$out = apply_filters('sph_WatchesWatch', $out, $a);
	echo $out;
}
