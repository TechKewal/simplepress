<?php
/*
Simple:Press
Auto Linking Plugin Support Routines
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_autolink_do_filter($content) {
	$autolink = SP()->options->get('autolink');

	$keywords = (isset($autolink['keywords'])) ? explode("\n", stripslashes($autolink['keywords'])) : array();
	$urls = (isset($autolink['urls'])) ? explode("\n", $autolink['urls']) : array();

	# need to add in delimiter for preg replace
   	$sffilters = SP()->options->get('sffilters');
	foreach ($keywords as $index => $keyword) {
        if (!empty($keyword)) {
        	$case = '';
        	if(substr($keyword, 0, 1) == '%') {
        		$case = 'i';
        		$keyword = ltrim($keyword, '%');
        	}
            $regex = stripslashes(SP()->filters->regex($keyword));
            $key_regex = (isset($autolink['noboundary']) && $autolink['noboundary']) ? '/'.trim($regex) : '/\b('.trim($regex).')\b';
            $keywords[$index] = $key_regex.'(?!(?:(?!<\/?[ha].*?>).)*<\/[ha].*?>)(?![^<>]*>)/'.$case;
            $urls[$index] = '<a href="'.SP()->displayFilters->url($urls[$index]).'">'.$keyword.'</a>';
        	if ($sffilters['sfnofollow']) $urls[$index] = SP()->saveFilters->nofollow($urls[$index]);
        	if ($sffilters['sftarget']) $urls[$index] = SP()->saveFilters->target($urls[$index]);
        } else {
            unset($keywords[$index]);
        }
	}

	$content = preg_replace($keywords, $urls, $content);
	return $content;
}
