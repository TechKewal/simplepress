<?php
/*
Simple:Press
Thanks Plugin - Functions for displaying top thanked user in forum stats
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_thanks_do_stats_top_thanked($args='', $label='') {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	$defs = array('pTitleClass'		=> 'spTopPosterStatsTitle',
				  'pPosterClass'	=> 'spPosterStats',
                  'limit'           => 5,
                  'link_names'      => 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_ThanksStatsTopThanked_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$pTitleClass 	= esc_attr($pTitleClass);
	$pPosterClass 	= esc_attr($pPosterClass);
    $limit          = (int) $limit;
	$link_names		= (int) $link_names;
	$label          = SP()->displayFilters->title($label);

    $limit = ($limit > 0) ? "LIMIT $limit" : '';

	$sql = "SELECT ".SPUSERACTIVITY.".user_id, display_name, COUNT(item_id) AS thanked
			FROM ".SPUSERACTIVITY."
			JOIN ".SPMEMBERS." ON ".SPUSERACTIVITY.".user_id=".SPMEMBERS.".user_id
			WHERE type_id = ".SPACTIVITY_THANKED."
			GROUP BY ".SPUSERACTIVITY.".user_id
			ORDER BY thanked DESC, display_name ASC ".$limit;
	$top_thanked = SP()->DB->select($sql);

    $out = '';
	$out.= "<p class='$pTitleClass'>$label</p>";

    if ($top_thanked) {
    	foreach ($top_thanked as $user) {
            $out.= "<p class='$pPosterClass'>".SP()->user->name_display($user->user_id, SP()->displayFilters->name($user->display_name), $link_names).": $user->thanked</p>";
    	}
    }

	$out = apply_filters('sph_ThanksStatsTopThanked', $out, $a);
    echo $out;
}
