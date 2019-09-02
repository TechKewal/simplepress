<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_do_AnswersTopicSeeAnswer($args='', $label='', $toolTip='') {
    if (!SP()->forum->view->thisTopic->answered) return; # only display if post answered

	$defs = array('tagId' 		=> 'spAnswersTopicSeeAnswer',
                  'tagClass' 	=> 'spAnswersTopicSeeAnswer',
				  'iconClass'	=> 'spIcon',
				  'icon'	    => 'sp_AnswersTopicSeeAnswer.png',
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_AnswersTopicSeeAnswer_args', $a);
	extract($a, EXTR_SKIP);

	$p = (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? SPANSWERSIMAGESMOB : SPANSWERSIMAGES;

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$iconClass		= esc_attr($iconClass);
	$icon	 	    = SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($icon));
	$label		    = SP()->displayFilters->title($label);
	$toolTip	    = esc_attr($toolTip);

    $url = SP()->spPermalinks->permalink_from_postid(SP()->forum->view->thisTopic->answered);
	$out = "<a rel='nofollow' id='$tagId' class='$tagClass' title='$toolTip' href='$url' >";
	$out.= $icon;
	if (!empty($label)) $out.= "$label";
	$out.= "</a>\n";

	$out = apply_filters('sph_AnswersTopicSeeAnswer', $out, $a);
	echo $out;
}
