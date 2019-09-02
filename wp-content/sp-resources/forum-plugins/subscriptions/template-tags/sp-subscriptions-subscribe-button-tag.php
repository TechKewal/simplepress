<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# This tag will only show up when on a topic view forum page.  It allows you to subscribe or unsubscribe to a topic

# 5.2 - mobileMenu arg added

function sp_SubscriptionsSubscribeButtonTag($args='', $subscribeLabel='', $unsubscribeLabel='', $subscribeToolTip='', $unsubscribeToolTip='') {
	# can be empty if request is for a bogus topic slug
	if(empty(SP()->forum->view->thisTopic)) return;

	sp_forum_ajax_support();

	if (!SP()->user->thisUser->member || !SP()->auths->get('subscribe', SP()->rewrites->pageData['forumid']) || SP()->rewrites->pageData['pageview'] != 'topic' || SP()->core->forumData['lockdown']) return;

	$defs = array('tagClass' 		=> 'spSubscriptionsSubscribeButton',
                  'tagId'           => 'spSubscriptionsSubscribeButton',
				  'labelClass'		=> 'spInRowLabel',
				  'iconClass'		=> 'spIcon',
				  'subscribeIcon'	=> 'sp_SubscriptionsSubscribeButton.png',
				  'unsubscribeIcon'	=> 'sp_SubscriptionsUnsubscribeButton.png',
				  'mobileMenu'		=> 0
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_SubscriptionsSubscribeButton_args', $a);
	extract($a, EXTR_SKIP);

	$p = (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? SIMAGESMOB : SIMAGES;

	# sanitize before use
	$tagId				= esc_attr($tagId);
	$tagClass			= esc_attr($tagClass);
	$labelClass			= esc_attr($labelClass);
	$iconClass			= esc_attr($iconClass);
	$subscribeIcon		= SP()->theme->paint_file_icon($p, sanitize_file_name($subscribeIcon));
	$unsubscribeIcon	= SP()->theme->paint_file_icon($p, sanitize_file_name($unsubscribeIcon));
	$subscribeLabel		= SP()->displayFilters->title($subscribeLabel);
	$unsubscribeLabel	= SP()->displayFilters->title($unsubscribeLabel);
	$subscribeToolTip	= esc_attr($subscribeToolTip);
	$unsubscribeToolTip	= esc_attr($unsubscribeToolTip);
	$mobileMenu			= (int) $mobileMenu;

	$out = '';
	if (sp_subscriptions_is_subscribed(SP()->user->thisUser->ID, SP()->rewrites->pageData['topicid'])) {
        $url = htmlspecialchars_decode(wp_nonce_url(SPAJAXURL.'subs-manage&amp;targetaction=del-sub&amp;topic='.SP()->rewrites->pageData['topicid'], 'subs-manage'));
		if (!$mobileMenu) {
			$out.= '<a rel="nofollow" id="'.$tagId.'" class="'.$tagClass.' spSubsUnsubscribe" title="'.$unsubscribeLabel.'" data-url="'.$url.'" data-target="'.$tagId.'" data-unsubicon="'.$unsubscribeIcon.'" data-subicon="'.$subscribeIcon.'" data-unsublabel="'.$unsubscribeLabel.'" data-sublabel="'.$subscribeLabel.'" data-unsubtip="'.$unsubscribeToolTip.'" data-subtip="'.$subscribeToolTip.'">';
			if (!empty($unsubscribeIcon)) $out.= "<img class='$iconClass' src='".$unsubscribeIcon."' alt='' />";
			$out.= '<span>'.$unsubscribeLabel."</span></a>\n";
		} else {
			$out.= '<li><a rel="nofollow" class="spSubsUnsubscribe" data-target="'.$tagId.'" data-unsubicon="'.$unsubscribeIcon.'" data-subicon="'.$subscribeIcon.'" data-unsublabel="'.$unsubscribeLabel.'" data-sublabel="'.$subscribeLabel.'" data-unsubtip="'.$unsubscribeToolTip.'" data-subtip="'.$subscribeToolTip.'">'.$unsubscribeLabel.'</li></a>'."\n";
		}
	} else {
        $url = htmlspecialchars_decode(wp_nonce_url(SPAJAXURL.'subs-manage&amp;targetaction=add-sub&amp;topic='.SP()->rewrites->pageData['topicid'], 'subs-manage'));
		if (!$mobileMenu) {
			$out.= '<a rel="nofollow" id="'.$tagId.'" class="'.$tagClass.' spSubsSubscribe" title="'.$subscribeToolTip.'" data-url="'.$url.'" data-target="'.$tagId.'" data-unsubicon="'.$unsubscribeIcon.'" data-subicon="'.$subscribeIcon.'" data-unsublabel="'.$unsubscribeLabel.'" data-sublabel="'.$subscribeLabel.'" data-unsubtip="'.$unsubscribeToolTip.'" data-subtip="'.$subscribeToolTip.'">';
			if (!empty($subscribeIcon)) $out.= "<img class='$iconClass' src='".$subscribeIcon."' alt='' />";
			$out.= '<span>'.$subscribeLabel."</span></a>\n";
		} else {
			$out.= '<li><a rel="nofollow" class="spSubsSubscribe" data-url="'.$url.'" data-target="'.$tagId.'" data-unsubicon="'.$unsubscribeIcon.'" data-subicon="'.$subscribeIcon.'" data-unsublabel="'.$unsubscribeLabel.'" data-sublabel="'.$subscribeLabel.'" data-unsubtip="'.$unsubscribeToolTip.'" data-subtip="'.$subscribeToolTip.'">'.$subscribeLabel.'</li></a>'."\n";
		}
	}

	$out = apply_filters('sph_SubscriptionsSubscribeButton', $out, $a);
	echo $out;
}
