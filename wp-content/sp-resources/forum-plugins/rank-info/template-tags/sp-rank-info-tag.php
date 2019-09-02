<?php
/*
Simple:Press
Ranks Info Plugin Template Tag
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_RankInfoTag($args='', $label='', $toolTip='') {
	$defs = array('tagId'    	=> 'spRankInfo',
				  'tagClass' 	=> 'spButton',
				  'icon' 		=> 'sp_RankInfo.png',
				  'iconClass'	=> 'spIcon',
				  'linkId'      => 'spRankInfoLink',
				  'mobileMenu'	=> 0,
				  'echo'		=> 1
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_RankInfo_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId	 		= esc_attr($tagId);
	$tagClass 		= esc_attr($tagClass);
	$linkId     	= esc_attr($linkId);
	$iconClass 		= esc_attr($iconClass);
	$icon			= SP()->theme->paint_icon($iconClass, SPRANKSIMAGES, sanitize_file_name($icon));
	$mobileMenu		= (int) $mobileMenu;
	$toolTip	    = esc_attr($toolTip);
	$echo			= (int) $echo;

	$br = ($mobileMenu) ? '<br />' : '';
    $out = '';

    $options = SP()->options->get('rank-info');

	if ($mobileMenu) $out.= sp_open_grid_cell();
	$out.= "<a class='$tagClass' id='$tagId' title='$toolTip' rel='nofollow' href='".SP()->spPermalinks->get_url('rankinfo')."'>";
	if (!empty($icon)) $out.= $icon.$br;
	if (!empty($label)) $out.= SP()->displayFilters->title($label);
	$out.= "</a>\n";
	if ($mobileMenu) $out.= sp_close_grid_cell();

	$out = apply_filters('sph_RankInfoTag', $out);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}
