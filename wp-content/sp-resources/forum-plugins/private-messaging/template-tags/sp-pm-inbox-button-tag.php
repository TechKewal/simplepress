<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# 5.2 - mobileMenu arg added

function sp_PmInboxButtonTag($args='', $label='', $toolTip='') {
    require_once PMLIBDIR.'sp-pm-database.php';

	if (!sp_pm_get_auth('use_pm') || (isset(SP()->user->thisUser->pmoptout) && SP()->user->thisUser->pmoptout)) return;

	$defs = array('tagId' 		=> 'spPmInboxButton',
                  'tagClass' 	=> 'spPmInboxButton',
				  'labelClass'	=> 'spInRowLabel',
				  'iconClass'	=> 'spIcon',
				  'icon'		=> 'sp_PmInboxButton.png',
				  'mobileMenu'	=> 0
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PmInboxButton_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$labelClass		= esc_attr($labelClass);
	$iconClass		= esc_attr($iconClass);
	$icon			= SP()->theme->paint_icon($iconClass, PMIMAGES, sanitize_file_name($icon));
	$label			= SP()->displayFilters->title($label);
	$toolTip		= esc_attr($toolTip);
	$mobileMenu		= (int) $mobileMenu;

    $newPM = sp_pm_get_inbox_unread_count(SP()->user->thisUser->ID);

	if (!$newPM) $newPM = 0;

	$url = SP()->spPermalinks->get_url('private-messaging/inbox');
	$br = ($mobileMenu) ? '<br />' : '';

	$out='';

	if ($mobileMenu) $out.= sp_open_grid_cell();
	$out.= "<a rel='nofollow' id='$tagId' class='$tagClass' title='$toolTip' href='$url' >";
	if (!empty($icon)) $out.= $icon.$br;
	$pmClass = ($newPM > 0) ? 'spPmCountUnread' : 'spPmCountRead';
	$out.= "$label  <span id='spPmCount'><span class='$pmClass'>$newPM</span></span>";
	$out.= "</a>\n";
	if ($mobileMenu) $out.= sp_close_grid_cell();

	$out = apply_filters('sph_PmInboxButton', $out, $a);
	echo $out;
}
