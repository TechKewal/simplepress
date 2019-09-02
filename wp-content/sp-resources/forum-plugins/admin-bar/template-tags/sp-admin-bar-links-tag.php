<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# --------------------------------------------------------------------------------------
#
#	sp_AdminLinksTag()
#	Display the admin links
#	Scope:	site
#
#	class:			CSS styling class
#
#	label:
#
# --------------------------------------------------------------------------------------
function sp_AdminLinksTag($args='', $label='', $toolTip='') {
	# bail if not admin or moderator
	if (!SP()->user->thisUser->admin) return;

	# is this admin showing the admin bar?
	if (!isset(SP()->user->thisUser->sfadminbar) || SP()->user->thisUser->sfadminbar==false ) return;

	$defs = array('tagId' 		=> 'spAdminLinks',
				  'tagClass' 	=> 'spAdminLinks',
				  'icon' 		=> 'sp_AdminLinks.png',
				  'iconClass'	=> 'spAdminLinks'
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_AdminLinks_args', $a);
	extract($a, EXTR_SKIP);

	$p = (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? SPABIMAGESMOB : SPABIMAGES;

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$iconClass 	= esc_attr($iconClass);
	$icon		= SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($icon));
	$toolTip	= esc_attr($toolTip);
	$label 		= SP()->displayFilters->title($label);

	$site = wp_nonce_url(SPAJAXURL."admin-bar-links&amp;targetaction=manage", 'admin-bar-links');
	$out = "<a class='$tagClass spOpenDialog' id='$tagId' title='$toolTip' rel='nofollow' data-site='$site' data-label='$label' data-width='350' data-height='0' data-align='center'>";
	if (!empty($icon)) $out.= $icon;
	if (!empty($label)) $out.= $label;
	$out.= "</a>\n";

	$out = apply_filters('sph_AdminLinks', $out, $a);
	echo $out;
}
