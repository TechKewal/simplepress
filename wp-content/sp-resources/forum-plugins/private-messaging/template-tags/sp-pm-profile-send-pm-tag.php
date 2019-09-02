<?php
/*
$LastChangedDate: 2018-12-11 20:31:24 -0600 (Tue, 11 Dec 2018) $
$Rev: 15843 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_ProfileSendPmTag($args='', $label='', $button='') {
    # some permission checks
	if (SP()->user->profileUser->user_id == SP()->user->thisUser->ID) return;
	if (!sp_pm_get_auth('use_pm') || !sp_pm_get_auth('use_pm', '', SP()->user->profileUser)) return;
	if ((isset(SP()->user->profileUser->pmoptout) && SP()->user->profileUser->pmoptout) || (isset(SP()->user->thisUser->pmoptout) && SP()->user->thisUser->pmoptout)) return;

    # check adversaries
    if (!empty(SP()->user->thisUser->adversaries) && in_array(SP()->user->profileUser->user_id, SP()->user->thisUser->adversaries)) return;
    if (!empty(SP()->user->thisUser->adversaries) && in_array(SP()->user->thisUser->ID, SP()->user->profileUser->adversaries)) return;

    # are we limiting pms by usergroup?
    $pm = SP()->options->get('pm');
    if ($pm['limitedug']) {
        $common = sp_pm_array_intersect_assoc(SP()->user->thisUser->memberships, SP()->user->profileUser->memberships); # are they in same usergroup?
        if (!SP()->user->profileUser->admin && empty($common)) return;
    }

	$defs = array('tagClass'	=> 'spProfileShowLink',
				  'leftClass'	=> 'spColumnSection spProfileLeftCol',
				  'middleClass'	=> 'spColumnSection spProfileSpacerCol',
				  'rightClass'	=> 'spColumnSection spProfileRightCol',
				  'buttonClass'	=> 'spButton',
				  'iconClass'	=> 'spIcon',
				  'icon'		=> 'sp_PmSendPmButton.png',
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_ProfileSendPm_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagClass		= esc_attr($tagClass);
	$leftClass		= esc_attr($leftClass);
	$middleClass	= esc_attr($middleClass);
	$rightClass		= esc_attr($rightClass);
	$buttonClass	= esc_attr($buttonClass);
	$iconClass		= esc_attr($iconClass);
	$icon			= SP()->theme->paint_icon($iconClass, PMIMAGES, sanitize_file_name($icon));
	$label			= SP()->displayFilters->title($label);
	$button			= SP()->displayFilters->title($button);

	$out = '';
	$out.= "<div class='$leftClass'>";
	$out.= "<p class='$tagClass'>$label:</p>";
	$out.= '</div>';
	$out.= "<div class='$middleClass'></div>";
	$out.= "<div class='$rightClass'>";
	$out.= "<p class='$tagClass'>";
	$username = SP()->DB->table(SPUSERS, "ID=".SP()->user->profileUser->user_id, 'user_login');
   	$url = SP()->spPermalinks->get_url('private-messaging/send/'.SP()->user->profileUser->user_id);
	$out.= "<a rel='nofollow' class='$buttonClass' href='$url' >";
	if (!empty($icon)) $out.= $icon;
	$out.= $button;
	$out.= '</a>';
	$out.= '</p>';
	$out.= "</div>\n";

	$out = apply_filters('sph_ProfileSendPm', $out, SP()->user->profileUser, $a);
	echo $out;
}
