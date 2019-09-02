<?php
/*
$LastChangedDate: 2018-12-11 20:31:24 -0600 (Tue, 11 Dec 2018) $
$Rev: 15843 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_PostIndexSendPmTag($args='', $label='', $toolTip='') {
    # some perimssion checks
	if (!sp_pm_get_auth('use_pm')) return;

	# we need to check if this was an anonymous post and if an admin viewing they can send a pm
	$poster = SP()->forum->view->thisPost->user_id;
	if ($poster == 0 && SP()->forum->view->thisPostUser->guest_name == 'Anonymous') {
		if (SP()->plugin->is_active('anonymous/sp-anonymous-plugin.php')) {
			$poster = 0;
		    $data = SP()->activity->get_col('col=uid&type='.SPACTIVITY_ANON.'&item='.SP()->forum->view->thisPost->post_id);
			if (!empty($data)) $poster = $data[0];
			if (!SP()->user->thisUser->admin) return;
		}
	}

	if (($poster == 0) || !sp_pm_get_auth('use_pm', '', $poster)) return;
	if ($poster == SP()->user->thisUser->ID) return;

    # check adversaries and wjhether opted oput
    if (SP()->forum->view->thisPostUser->guest_name != 'Anonymous') {
	    if (in_array(SP()->forum->view->thisPostUser->user_id, (array) SP()->user->thisUser->adversaries)) return;
    	if (in_array(SP()->user->thisUser->ID, (array) SP()->forum->view->thisPostUser->adversaries)) return;
    	if (isset(SP()->forum->view->thisPostUser->pmoptout) && SP()->forum->view->thisPostUser->pmoptout) return;
	}

    # are we limiting pms by usergroup?
    $pm = SP()->options->get('pm');
    if ($pm['limitedug']) {
        $common = sp_pm_array_intersect_assoc(SP()->user->thisUser->memberships, SP()->forum->view->thisPostUser->memberships); # are they in same usergroup?
        if (!SP()->user->thisUser->admin && empty($common)) return;
    }

	$defs = array('tagId' 		=> 'spPmSendPmButton%ID%',
                  'tagClass' 	=> 'spPmSendPmButton',
				  'labelClass'	=> 'spInRowLabel',
				  'iconClass'	=> 'spIcon',
				  'icon'		=> 'sp_PmSendPmButton.png',
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmSendPmButton_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$labelClass		= esc_attr($labelClass);
	$iconClass		= esc_attr($iconClass);
	$icon			= SP()->theme->paint_icon($iconClass, PMIMAGES, sanitize_file_name($icon));
	$label			= SP()->displayFilters->title($label);
	$toolTip		= esc_attr($toolTip);
	$tagId = str_ireplace('%ID%', SP()->forum->view->thisPost->post_id, $tagId);

   	$url = SP()->spPermalinks->get_url('private-messaging/send/'.$poster);
	$out = "<a rel='nofollow' id='$tagId' class='$tagClass' title='$toolTip' href='$url' >";
	$out.= $icon;
	$out.= "$label";
	$out.= "</a>\n";

	$out = apply_filters('sph_PmSendPmButton', $out, $a);
	echo $out;
}
