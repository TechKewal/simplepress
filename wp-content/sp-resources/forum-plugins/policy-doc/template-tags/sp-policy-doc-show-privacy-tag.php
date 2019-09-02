<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_PolicyDocShowPrivacyTag($args='', $label='') {
	$defs = array('tagId' 		=> 'spPolicyDocPrivacy',
				  'tagClass' 	=> 'spPolicyDocPrivacy',
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PolicyDocPrivacy_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
    $label      = SP()->displayFilters->title($label);

	$out = '';
	$out.= "<div id='$tagId'>";
	if (!empty($label)) $out.= "<p class='spLabel'>$label</p>";
	$out.= "<div class='$tagClass'>";
	$out.= sp_policy_doc_retrieve('privacy');
	$out.= '</div>';
	$out.= "</div>\n";

	$out = apply_filters('sph_PolicyDocPrivacy', $out, $a);
	echo $out;
}
