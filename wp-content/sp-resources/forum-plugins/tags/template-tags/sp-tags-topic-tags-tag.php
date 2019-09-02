<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# 5.5.1
# Added delimiter argument
#		delimiterClass argument

function sp_TopicTagsListTag($args='', $label='') {
	if (!SP()->forum->view->thisTopic->use_tags || empty(SP()->forum->view->thisTopic->tags)) return;

	$defs = array('tagClass' 		=> 'spTopicTagsList',
				  'iconClass'		=> 'spButton',
				  'delimiter'		=> '',
				  'delimiterClass'	=> 'spTagsDelimiter'
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_TopicTagsList_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagClass		= esc_attr($tagClass);
	$iconClass		= esc_attr($iconClass);
	$delimiter		= esc_attr($delimiter);
	$delimiterClass	= esc_attr($delimiterClass);
	$label			= SP()->displayFilters->title($label);

	$numTags = count(SP()->forum->view->thisTopic->tags);
	$done = 0;

	$out = '';
	$out.= "<div class='$tagClass'>";
	$out.= "<span>$label</span>";
	foreach (SP()->forum->view->thisTopic->tags as $tag) {
        $url = esc_url(add_query_arg(array('forum'=>'all', 'value'=>urlencode($tag->tag_name), 'type'=>1, 'include'=>4, 'search'=>1, 'new'=>1), SP()->spPermalinks->get_url()));
        $title = esc_attr(__('Search for other topics with tag', 'sp-tags')).' '.$tag->tag_name;
		$out.= "<a rel='nofollow' class='$iconClass' title='$title' href='$url'>$tag->tag_name</a>";

		$done++;
		if($done < $numTags && !empty($delimiter)) {
			$out.= "<span class='$delimiterClass'>$delimiter</span>";
		}
	}
	$out.= "</div>\n";

	$out = apply_filters('sph_TopicTagsList', $out, $a);
	echo $out;
}
