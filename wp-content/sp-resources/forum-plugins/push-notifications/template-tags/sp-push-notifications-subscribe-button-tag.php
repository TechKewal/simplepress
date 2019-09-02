<?php
/*
$LastChangedDate: 2019-02-04 14:35:02 -0500 (Mon, 04 Fed 2019) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# This tag will only show up when on a topic view forum page.  It allows you to subscribe or unsubscribe to a topic

# 5.2 - mobileMenu arg added

function sp_PushNotificationsSubscribeButtonTag($args='', $subscribeLabel='', $unsubscribeLabel='', $subscribeToolTip='', $unsubscribeToolTip='', $services, $activeType) {

	# can be empty if request is for a bogus topic slug
	if(empty(SP()->forum->view->thisTopic)) return;

	sp_forum_ajax_support();

	if (!SP()->auths->get($services)) return;

	if (
		!SP()->user->thisUser->member || 
		!SP()->auths->get($services, SP()->rewrites->pageData['forumid']) || 
		 SP()->rewrites->pageData['pageview'] != 'topic' || 
		 SP()->core->forumData['lockdown']
	) return;

	$defs = array('tagClass' 		=> 'spPushNotifications'.$services.'Button',
                  'tagId'           => 'spPushNotifications'.$services.'Button',
				  'labelClass'		=> 'spInRowLabel'.$services,
				  'iconClass'		=> 'spIcon',
				  'subscribeIcon'	=> 'sp_PushNotificationsSubscribeButton.png',
				  'unsubscribeIcon'	=> 'sp_PushNotificationsUnsubscribeButton.png',
				  'mobileMenu'		=> 0
				  );

	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_Subscriptions'.$services.'Button_args', $a);

	extract($a, EXTR_SKIP);

	$p = (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? SPPNIMAGESMOB : SPPNIMAGES;

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
	$services			= esc_attr($services);
	$out = '';
	

	if (sp_push_notifications_is_subscribed(SP()->user->thisUser->ID, SP()->rewrites->pageData['topicid'], $activeType)) {


        $url = htmlspecialchars_decode(

			wp_nonce_url(
				SPAJAXURL.'subs-'.$services.'&amp;targetaction=del-sub&amp;topic='.SP()->rewrites->pageData['topicid'], 
				'subs-'.$services
			)

		);


		if (!$mobileMenu) {

			$out.= '<a rel="nofollow" id="'.$tagId.'" class="'.$tagClass.' spPushNotificationsUnsubscribe" title="'.$unsubscribeLabel.'" data-url="'.$url.'" data-target="'.$tagId.'" data-unsubicon="'.$unsubscribeIcon.'" data-subicon="'.$subscribeIcon.'" data-unsublabel="'.$unsubscribeLabel.'" data-sublabel="'.$subscribeLabel.'" data-unsubtip="'.$unsubscribeToolTip.'" data-subtip="'.$subscribeToolTip.'">';
			if (!empty($unsubscribeIcon)) $out.= "<img class='$iconClass' src='".$unsubscribeIcon."' alt='' />";
			$out.= '<span>'.$unsubscribeLabel."</span></a>\n";

		} else {

			$out.= '<li><a rel="nofollow" class="spPushNotificationsUnsubscribe" data-target="'.$tagId.'" data-unsubicon="'.$unsubscribeIcon.'" data-subicon="'.$subscribeIcon.'" data-unsublabel="'.$unsubscribeLabel.'" data-sublabel="'.$subscribeLabel.'" data-unsubtip="'.$unsubscribeToolTip.'" data-subtip="'.$subscribeToolTip.'">'.$unsubscribeLabel.'</li></a>'."\n";

		}


	} else {


        $url = htmlspecialchars_decode(

			wp_nonce_url(
				SPAJAXURL.'subs-'.$services.'&amp;targetaction=add-sub&amp;topic='.SP()->rewrites->pageData['topicid'], 
				'subs-'.$services
			)

		);
		
		if (!$mobileMenu) {

			$out.= '<a rel="nofollow" id="'.$tagId.'" class="'.$tagClass.' spPushNotificationsSubscribe" title="'.$subscribeToolTip.'" data-url="'.$url.'" data-target="'.$tagId.'" data-unsubicon="'.$unsubscribeIcon.'" data-subicon="'.$subscribeIcon.'" data-unsublabel="'.$unsubscribeLabel.'" data-sublabel="'.$subscribeLabel.'" data-unsubtip="'.$unsubscribeToolTip.'" data-subtip="'.$subscribeToolTip.'">';
			if (!empty($subscribeIcon)) $out.= "<img class='$iconClass' src='".$subscribeIcon."' alt='' />";
			$out.= '<span>'.$subscribeLabel."</span></a>\n";

		} else {

			$out.= '<li><a rel="nofollow" class="spPushNotificationsSubscribe" data-url="'.$url.'" data-target="'.$tagId.'" data-unsubicon="'.$unsubscribeIcon.'" data-subicon="'.$subscribeIcon.'" data-unsublabel="'.$unsubscribeLabel.'" data-sublabel="'.$subscribeLabel.'" data-unsubtip="'.$unsubscribeToolTip.'" data-subtip="'.$subscribeToolTip.'">'.$subscribeLabel.'</li></a>'."\n";

		}


	}

	$out = apply_filters('sph_SubscriptionsSubscribeButton', $out, $a);
	echo $out;
}
