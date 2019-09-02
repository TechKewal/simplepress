<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_RelatedTopicsButtonTag($args='', $label='', $toolTip='') {
	if (!SP()->forum->view->thisTopic->use_tags || empty(SP()->forum->view->thisTopic->tags)) return;

	$defs = array('tagClass' 	=> 'spRelated',
				  'icon' 		=> 'sp_Related.png',
				  'iconClass'	=> 'spIcon',
				  'popup'		=> 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_RelatedTopicsButton_args', $a);
	extract($a, EXTR_SKIP);

	$p = (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? SPTIMAGESMOB : SPTIMAGES;

	# sanitize before use
	$tagClass	= esc_attr($tagClass);
	$iconClass 	= esc_attr($iconClass);
	$icon		= SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($icon));
	$popup		= (int) $popup;
	$toolTip	= esc_attr($toolTip);
	$label		= SP()->displayFilters->title($label);

    $out = '';
    if ($popup) {
    	$site = wp_nonce_url(SPAJAXURL."tags-ajax&amp;targetaction=related&amp;topicid=".SP()->forum->view->thisTopic->topic_id, 'tags-ajax');
    	$out.= "<a rel='nofollow' class='$tagClass spOpenDialog' title='$toolTip' data-site='$site' data-label='$toolTip' data-width='700' data-height='0' data-align='center'>";
    } else {
		$out.= "<a rel='nofollow' class='$tagClass' title='$toolTip' href='".SP()->spPermalinks->get_query_url(SP()->spPermalinks->get_url('related-tags'))."topic=".SP()->forum->view->thisTopic->topic_id."'>";
    }
	if (!empty($icon)) $out.= $icon;
	$out.= $label;
	$out.= "</a>\n";

	$out = apply_filters('sph_RelatedTopicsButton', $out, $a);
	echo $out;
}
