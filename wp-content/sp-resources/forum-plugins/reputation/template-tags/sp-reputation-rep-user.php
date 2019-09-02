<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_reputation_rep_user($args='', $label='', $toolTip='') {
	# bail if guest or guest post or admin post
	if (SP()->user->thisUser->guest || SP()->forum->view->thisPostUser->guest || SP()->forum->view->thisPostUser->admin) return;

	# bail if post user is current user since cant rate self
	if (SP()->forum->view->thisPostUser->ID == SP()->user->thisUser->ID) return;

	# bail if no permission for current user to give/take rep
	if (!SP()->auths->get('use_reputation', SP()->forum->view->thisTopic->forum_id, SP()->user->thisUser->ID)) return;

	# bail if no permission for post user to gain/lose rep
	if (!SP()->auths->get('get_reputation', SP()->forum->view->thisTopic->forum_id, SP()->forum->view->thisPostUser->ID)) return;

    # bail if user has already rated this user/post
    if (isset(SP()->user->thisUser->reputation_posts[SP()->forum->view->thisPost->post_id])) return;

    # bail if max daily give exceeded
    if (SP()->user->thisUser->reputation_daily >= SP()->user->thisUser->reputation_level->maxday) return;

	$defs = array('tagId' 		=> 'spRepUserButton%ID%',
                  'tagClass' 	=> 'spRepUserButton',
				  'labelClass'	=> 'spInRowLabel',
				  'iconClass'	=> 'spIcon',
				  'icon'		=> 'sp_RepUserButton.png',
                  'useDiv'      => 1,
				  );

	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PostIndexRepUser_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$labelClass		= esc_attr($labelClass);
	$iconClass		= esc_attr($iconClass);
	$icon			= SP()->theme->paint_icon($iconClass, SPREPIMAGES, sanitize_file_name($icon));
	$useDiv		    = (int) $useDiv;
	$label			= SP()->displayFilters->title($label);
	$toolTip		= esc_attr($toolTip);

	$tagId = str_ireplace('%ID%', SP()->forum->view->thisPost->post_id, $tagId);

	$out = '';

   	$url = '#';
	if ($useDiv) $out.= "<div class='$tagClass'>";
	$site = wp_nonce_url(SPAJAXURL."reputation-manage&amp;targetaction=rep-popup&amp;user=".SP()->forum->view->thisPost->user_id."&amp;post=".SP()->forum->view->thisPost->post_id, 'reputation-manage');
	$out.= "<a id='$tagId' rel='nofollow' class='$tagClass spOpenDialog' title='$toolTip' data-site='$site' data-label='$toolTip' data-width='300' data-height='0' data-align='center'>";
	$out.= $icon;
	$out.= "$label";
	$out.= "</a>\n";
	if ($useDiv) $out.= '</div>';

	$out = apply_filters('sph_PostIndexRepUser', $out, $a);
	echo $out;
}
