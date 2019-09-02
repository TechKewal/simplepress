<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_OnlinePageLinkTag($args='', $label='') {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	if (!SP()->auths->get('view_online_activity')) return;

	$defs = array('linkClass'	=> 'spOnlinePageLink',
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_OnlinePageLink_args', $a);
	extract($a, EXTR_SKIP);

	$linkClass 	= esc_attr($linkClass);
	if (!empty($label)) $label = SP()->displayFilters->title($label);

	$out = "<a href='".SP()->spPermalinks->get_url('online')."' class='$linkClass'>$label</a>";

	$out = apply_filters('sph_OnlinePageLink', $out, $a);
	echo $out;
}
