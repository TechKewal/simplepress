<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# This tag allows you to subscribe or unsubscribe to a forum

# 5.2 - mobileMenu arg added

function sp_SubscriptionsSubscribeForumButtonTag($args='', $subscribeLabel='', $unsubscribeLabel='', $subscribeToolTip='', $unsubscribeToolTip='') {
	sp_forum_ajax_support();
	$subs = SP()->options->get('subscriptions');

    if (!$subs['forumsubs']) return;
	if (!SP()->user->thisUser->member || empty(SP()->rewrites->pageData['forumid']) || !SP()->auths->get('subscribe', SP()->rewrites->pageData['forumid']) || SP()->core->forumData['lockdown']) return;

	$defs = array('tagClass' 		=> 'spSubscriptionsSubscribeForumButton',
                  'tagId'           => 'spSubscriptionsSubscribeForumButton',
				  'labelClass'		=> 'spInRowLabel',
				  'iconClass'		=> 'spIcon',
				  'subscribeIcon'	=> 'sp_SubscriptionsSubscribeForumButton.png',
				  'unsubscribeIcon'	=> 'sp_SubscriptionsUnsubscribeForumButton.png',
				  'mobileMenu'		=> 0
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_SubscriptionsSubscribeForumButton_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass			= esc_attr($tagClass);
	$labelClass			= esc_attr($labelClass);
	$iconClass			= esc_attr($iconClass);
	$subscribeIcon		= SP()->theme->paint_icon($iconClass, SIMAGES, sanitize_file_name($subscribeIcon));
	$unsubscribeIcon	= SP()->theme->paint_icon($iconClass, SIMAGES, sanitize_file_name($unsubscribeIcon));
	$subscribeLabel		= SP()->displayFilters->title($subscribeLabel);
	$unsubscribeLabel	= SP()->displayFilters->title($unsubscribeLabel);
	$subscribeToolTip	= esc_attr($subscribeToolTip);
	$unsubscribeToolTip	= esc_attr($unsubscribeToolTip);
	$mobileMenu			= (int) $mobileMenu;

	$out = '';
	if (sp_subscriptions_is_forum_subscribed(SP()->user->thisUser->ID, SP()->rewrites->pageData['forumid'])) {
		$url = SP()->spPermalinks->build_url(SP()->rewrites->pageData['forumslug'], '', SP()->rewrites->pageData['page'], 0).SP()->spPermalinks->get_query_char().'unsubforum='.SP()->rewrites->pageData['forumid'];
		if (!$mobileMenu) {
			$out.= "<a rel='nofollow' id='$tagId' class='$tagClass' title='$unsubscribeToolTip' href='$url' >";
			if (!empty($unsubscribeIcon)) $out.= $unsubscribeIcon;
			$out.= $unsubscribeLabel."</a>\n";
		} else {
			$out.= "<li><a rel='nofollow' href='$url'>$unsubscribeLabel</li></a>\n";
		}
	} else {
		$url = SP()->spPermalinks->build_url(SP()->rewrites->pageData['forumslug'], '', SP()->rewrites->pageData['page'], 0).SP()->spPermalinks->get_query_char().'subforum='.SP()->rewrites->pageData['forumid'];
		if (!$mobileMenu) {
			$out.= "<a rel='nofollow' id='$tagId' class='$tagClass' title='$subscribeToolTip' href='$url' >";
			if (!empty($subscribeIcon)) $out.= $subscribeIcon;
			$out.= $subscribeLabel."</a>\n";
		} else {
			$out.= "<li><a rel='nofollow' href='$url'>$subscribeLabel</li></a>\n";
		}
	}

	$out = apply_filters('sph_SubscriptionsSubscribeButton', $out, $a);
	echo $out;
}
