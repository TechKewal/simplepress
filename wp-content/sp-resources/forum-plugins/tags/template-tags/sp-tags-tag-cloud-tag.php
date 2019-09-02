<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/* 	=====================================================================================
	sp_TagCloud($limit=25, $sort='random', $size=true, $smallest=8, $largest=22, $unit='pt', $color=true, $mincolor='#000000', $maxcolor='#cccccc')

	displays tag cloud

	parameters:

		$limit			How many tags to show in the list			number			25 (default)
		$sort			How to sort the tags in the cloud			text			desc, asc or random (default)
		$size			change size of tags based on count			boolean			true (default) or false
		$smallest		size of least used tag ($size must be 1)	number			8 (default)
		$largest		size of most used tag ($size must be 1)		number			22 (default)
		$unit			Units for the smallest/larget sizes			text			pt (default) or px
		$color			change color of tags based on count			boolean			true (default) or false
		$mincolor		color of least used tag ($color must be 1)	text			#000000 (default)
		$maxcolor		color of least used tag ($color must be 1)	text			#cccccc (default)
		echo			write display (returns it if false)		    true/false		true
		sep			    separator between tags in the cloud		    text       		space
 	===================================================================================*/
function sp_TagCloudTag($limit=25, $sort='random', $size=true, $smallest=8, $largest=22, $unit='pt', $color=true, $mincolor='#000000', $maxcolor='#cccccc', $echo=true, $sep=' ') {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	$out = '';
	$format = '<a href="%tag_link%" id="sftaglink-%tag_id%" class="sftagcloud" title="%tag_count% '.__("topics", 'sp-ttags').'" style="%tag_size% %tag_color%">%tag_name%</a>';

	# remove size and color markers if not used
	if (!$size) $format = str_replace('%tag_size%', '', $format);
	if (!$color) $format = str_replace('%tag_color%', '', $format);

	if ($sort == 'random') {
		$sortby = 'RAND()';
	} else {
		$sortby = 'tag_count '.$sort;
	}
	$sftags = SP()->DB->table(SPTAGS, '', '', $sortby, $limit);

	# find min and max counts
	$minval = 999999;
	$maxval = -999999;
	foreach ($sftags as $sftag) {
		if ($sftag->tag_count < $minval) $minval = $sftag->tag_count;
		if ($sftag->tag_count > $maxval) $maxval = $sftag->tag_count;
	}

	# make sure smallest is not greater than largest
	if ($smallest > $largest) $smallest = $largest;

	# scaling
	$scale_min = 1;
	$scale_max = 10;
	$minout = max($scale_min, 0);
	$maxout = max($scale_max, $minout);
	$scale = ($maxval > $minval) ? (($maxout - $minout) / ($maxval - $minval)) : 0;

	if ($sftags) {
		foreach ($sftags as $sftag) {
			$tag_scale = (int) (($sftag->tag_count - $minval) * $scale + $minout);
			$tagout = $format;
			$tagout = str_replace('%tag_name%', esc_html($sftag->tag_name), $tagout);
			$tagout = str_replace('%tag_link%', esc_url(SP()->spPermalinks->get_query_url(SP()->spPermalinks->get_url()).'forum=all&amp;value='.urlencode($sftag->tag_name).'&amp;type=1&amp;include=4&amp;search=1&amp;new=1'), $tagout);
			$tagout = str_replace('%tag_id%', $sftag->tag_id, $tagout);
			$tagout = str_replace('%tag_count%', (int) $sftag->tag_count, $tagout);
			$tagout = str_replace('%tag_size%', 'font-size:'.round(($tag_scale - $scale_min)*($largest-$smallest)/($scale_max - $scale_min) + $smallest, 2).$unit.';', $tagout);
			$tagout = str_replace('%tag_color%', 'color:'.sp_get_color_scaled(round(($tag_scale - $scale_min)*(100)/($scale_max - $scale_min), 2),$mincolor,$maxcolor).';', $tagout);
			$out.= $tagout.$sep;
		}
	} else {
		$out.= __('No tags to display', 'sp-tags');
	}

	if ($echo) {
		echo $out;
		return;
	} else {
		return $out;
	}
}

# This is pretty filthy. Doing math in hex is much too weird. It's more likely to work, this way!
# Provided from UTW. Thanks.
function sp_get_color_scaled($scale_color, $min_color, $max_color) {
	$scale_color = $scale_color / 100;
	$minr = hexdec(substr($min_color, 1, 2));
	$ming = hexdec(substr($min_color, 3, 2));
	$minb = hexdec(substr($min_color, 5, 2));
	$maxr = hexdec(substr($max_color, 1, 2));
	$maxg = hexdec(substr($max_color, 3, 2));
	$maxb = hexdec(substr($max_color, 5, 2));
	$r = dechex(intval((($maxr - $minr) * $scale_color) + $minr));
	$g = dechex(intval((($maxg - $ming) * $scale_color) + $ming));
	$b = dechex(intval((($maxb - $minb) * $scale_color) + $minb));
	if (strlen($r) == 1) $r = '0'.$r;
	if (strlen($g) == 1) $g = '0'.$g;
	if (strlen($b) == 1) $b = '0'.$b;
	return '#'.$r.$g.$b;
}
