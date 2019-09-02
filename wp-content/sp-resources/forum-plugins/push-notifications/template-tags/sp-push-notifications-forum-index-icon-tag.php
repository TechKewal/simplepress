<?php
/*
$LastChangedDate: 2019-02-04 14:35:02 -0500 (Mon, 04 Fed 2019) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# --------------------------------------------------------------------------------------
#
#	sp_ForumIndexSubscriptionIconTag()
#	Display Forum Subscription Icon (In status icons area - standalone version)
#	Scope:	Forum sub Loop
#	Version: 5.4.2
#
# --------------------------------------------------------------------------------------
function sp_ForumIndexSubscriptionIconTag($args='', $subToolTip='', $unSubToolTip='') {
	$defs = array('tagId' 			=> 'spForumIndexSubscriptionIcon%ID%',
				  'tagClass' 		=> 'spIcon',
				  'subscribeIcon'	=> 'sp_PushNotificationsSubscribeForum.png',
				  'unSubscribeIcon'	=> 'sp_PushNotificationsUnsubscribeForum.png',
				  'echo'			=> 1
				  );

	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_ForumIndexLockIcon_args', $a);
	extract($a, EXTR_SKIP);

	$p = (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? SPPNIMAGESMOB : SPPNIMAGES;

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$subscribeIcon		= SP()->theme->paint_icon('', $p, $subscribeIcon);
	$unSubscribeIcon	= SP()->theme->paint_icon('', $p, $unSubscribeIcon);
	$echo			= (int) $echo;

	$subToolTip		= esc_attr($subToolTip);
    $unSubToolTip	= esc_attr($unSubToolTip);

	$tagId = str_ireplace('%ID%', SP()->forum->view->thisForum->forum_id, $tagId);

	$out = '';

	$subs = SP()->options->get('subscriptions');
    if ($subs['forumsubs'] && SP()->user->thisUser->member) {

    	$out.= "<div id='$tagId' class='$tagClass'>\n";

    	if (SP()->auths->get('subscribe', SP()->forum->view->thisForum->forum_id) && ((!SP()->forum->view->thisForum->forum_status && !SP()->core->forumData['lockdown']) || SP()->user->thisUser->admin)) {
            if (sp_push_notifications_is_forum_subscribed(SP()->user->thisUser->ID, SP()->forum->view->thisForum->forum_id)) {
    			$url = SP()->spPermalinks->build_url('', '', 1, 0).SP()->spPermalinks->get_query_char()."unsubforum=".SP()->forum->view->thisForum->forum_id;
    			$out.= "<a href='$url' class='' title='$unSubToolTip'>\n";
    			$out.= $unSubscribeIcon;
    			$out.= "</a>\n";
    		} else {
    			$url = SP()->spPermalinks->build_url('', '', 1, 0).SP()->spPermalinks->get_query_char()."subforum=".SP()->forum->view->thisForum->forum_id;
    			$out.= "<a href='$url' class='' title='$subToolTip'>\n";
    			$out.= $subscribeIcon;
    			$out.= "</a>\n";
    		}
    	}

		$out.= "</div>\n";
    }

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}
