<?php
/*
$LastChangedDate: 2018-10-23 03:32:51 -0500 (Tue, 23 Oct 2018) $
$Rev: 15763 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_PolicyDocPolicyLinkTag($args='', $label='', $toolTip='') {
	$spPolicy = SP()->options->get('policy-doc');
	if (empty($spPolicy['regfile']) && empty(SP()->meta->get_value('registration', 'policy'))) return;

	$defs = array('tagId'    	=> 'spPolicyDoc',
				  'tagClass' 	=> 'spPolicyDoc',
				  'icon'        => 'sp_PolicyDoc.png',
				  'iconClass'	=> 'spIcon',
				  'linkClass'	=> 'spLink',
				  'popup'		=> 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PolicyDoc_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId	 	= esc_attr($tagId);
	$tagClass 	= esc_attr($tagClass);
	$iconClass 	= esc_attr($iconClass);
	$icon		= SP()->theme->paint_icon($iconClass, PDIMAGES, sanitize_file_name($icon));
	$popup   	= (int) $popup;
	if (!empty($label)) 	$label = SP()->displayFilters->title($label);
	if (!empty($toolTip)) 	$toolTip = esc_attr($toolTip);

	# build acknowledgements url and render link to SP and popup
	$out = "<div id='$tagId' class='$tagClass'>";
    if ($popup) {
    	$site = wp_nonce_url(SPAJAXURL.'policy-doc&amp;popup=reg', 'policy-doc');
    	$out.= "<a rel='nofollow' class='$linkClass spOpenDialog' title='$toolTip' data-site='$site' data-label='$toolTip' data-width='600' data-height='0' data-align='center'>";
    } else {
		$out.= "<a rel='nofollow' class='$tagClass' title='$toolTip' href='".SP()->spPermalinks->get_query_url(SP()->spPermalinks->get_url('policy')).'popup=reg'."'>";
    }
	if (!empty($icon)) $out.= $icon;
	$out.= "$label</a></div>\n";

	$out = apply_filters('sph_PolicyDoc', $out, $a);
	echo $out;
}
