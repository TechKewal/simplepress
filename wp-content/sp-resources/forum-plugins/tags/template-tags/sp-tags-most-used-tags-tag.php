<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/* 	=====================================================================================
	sp_most_used_tags($limit)

	displays the most used tags

	parameters:

		$limit			How many tags to show in the list		number			10 (default)
		echo			write display (returns it if false)		true/false		true
 	===================================================================================*/
function sp_TagsMostUsedTag($limit=10, $echo=true) {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	$out = '';

	$sftags = SP()->DB->table(SPTAGS, '', '', 'tag_count DESC', $limit);
	if ($sftags) {
		foreach ($sftags as $sftag) {
			$out.= '<li class="sftaglist">';
			$out.= '<a href="'.esc_url(SP()->spPermalinks->get_query_url(SP()->spPermalinks->get_url()).'forum=all&amp;value='.$sftag->tag_slug.'&amp;type=1&amp;include=4&amp;search=1&amp;new=1').'">'.$sftag->tag_name.'</a> ('.$sftag->tag_count.')';
    		$out.= '</li>';
		}
	} else {
		$out.= '<li class="sftaglist">';
		$out.= __('No tags to display', 'sp-tags');
		$out.= '</li>';
	}

	if ($echo) {
		echo $out;
		return;
	} else {
		return $out;
	}
}
