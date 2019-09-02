<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_do_AnswersTopicAnswer($args='', $label='', $toolTip='') {
	$defs = array('tagId' 		=> 'spAnswersTopicAnswer',
                  'tagClass' 	=> 'spAnswersTopicAnswer',
				  'labelClass'	=> 'spInRowLabel',
				  'iconClass'	=> 'spIcon',
				  'icon'	    => 'sp_AnswersTopicAnswer.png',
                  'stacked'     => 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_AnswersTopicAnswer_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$labelClass		= esc_attr($labelClass);
	$iconClass		= esc_attr($iconClass);
	$icon	 	    = SP()->theme->paint_icon($iconClass, SPANSWERSIMAGES, sanitize_file_name($icon));
	$label		    = SP()->displayFilters->title($label);
	$toolTip	    = esc_attr($toolTip);
    $stacked        = SP()->filters->integer($stacked);

    $out = '';
    if (SP()->forum->view->thisTopic->answered == SP()->forum->view->thisPost->post_id) {
        $out.= "<div id='$tagId' class='$tagClass' title='$toolTip'>";
    	$out.= $icon;
        if ($stacked) $out.= '<br />';
    	if (!empty($label)) $out.= "<span class='$labelClass'>$label</span>";
        $out.= '</div>';
    }

	$out = apply_filters('sph_AnswersTopicAnswer', $out, $a);
	echo $out;
}
