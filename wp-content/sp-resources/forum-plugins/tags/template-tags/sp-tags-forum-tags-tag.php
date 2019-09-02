<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# 5.5.1
# Added tagItemClass argument
# 		delimiter argument
#		delimiterClass argument

function sp_TopicIndexTagsListTag($args='', $label='', $toolTip='') {
	if (!SP()->forum->view->thisForum->use_tags || empty(SP()->forum->view->thisTopic->tags)) return;

	$defs = array('tagClass' 		=> 'spTopicIndexTags',
				  'iconClass'		=> 'spButton',
				  'icon'			=> 'sp_Tags.png',
				  'tagItemClass'	=> 'spButton',
				  'delimiter'		=> '',
				  'delimiterClass'	=> 'spTagsDelimiter',
				  'collapse'		=> 1
				  );

	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_TopicIndexTagsList_args', $a);
	extract($a, EXTR_SKIP);

	$p = (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? SPTIMAGESMOB : SPTIMAGES;

	# sanitize before use
	$tagClass		= esc_attr($tagClass);
	$iconClass		= esc_attr($iconClass);
	$icon			= SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($icon));
	$tagItemClass	= esc_attr($tagItemClass);
	$delimiter		= esc_attr($delimiter);
	$delimiterClass	= esc_attr($delimiterClass);
	$collapse		= (int) $collapse;
	$label			= SP()->displayFilters->title($label);
	$toolTip		= esc_attr($toolTip);

	$out = '';
	if ($collapse) {
		$out.= "<a class='$tagClass spTopicTagsShow' title='$toolTip' data-id='spTagsContainer-".SP()->forum->view->thisTopic->topic_id."'>";
		if (!empty($icon)) $out.= $icon;
		$out.= "$label</a>";
	}
	$inline = ($collapse) ? ' spInlineSection' : '';
	$out.= "<div id='spTagsContainer-".SP()->forum->view->thisTopic->topic_id."' class='$tagClass$inline'>";
	if (!$collapse) {
		$out.= "<span class='$tagClass'>$label</span>";
		if (!empty($icon)) $out.= $icon;
	}
	$numTags = count(SP()->forum->view->thisTopic->tags);
	$done = 0;
	foreach (SP()->forum->view->thisTopic->tags as $tag) {
        $url = esc_url(add_query_arg(array('forum'=>'all', 'value'=>urlencode($tag->tag_name), 'type'=>1, 'include'=>4, 'search'=>1, 'new'=>1), SP()->spPermalinks->get_url()));
        $title = esc_attr(__('Search for other topics with tag', 'sp-tags')).' '.$tag->tag_name;
		$out.= "<a rel='nofollow' class='$tagItemClass' title='$title' href='$url'>$tag->tag_name</a>";

		$done++;
		if($done < $numTags && !empty($delimiter)) {
			$out.= "<span class='$delimiterClass'>$delimiter</span>";
		}
	}
	$out.= "</div>\n";

	$out = apply_filters('sph_TopicIndexTagsList', $out, $a);
	echo $out;
}
