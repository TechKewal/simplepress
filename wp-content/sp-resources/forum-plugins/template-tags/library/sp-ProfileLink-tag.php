<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/* 	==========================================================================================
	sp_do_sp_ProfileLinkTag()

	displays a standard link to the current users profile form.
	If its not a user (ie a guest), nothing will be displayed
	Will use your theme styling.

	parameters:
		name			description								type			default
		--------------------------------------------------------------------------------------
		linkText		Text for link							text			'Your Profile'
		beforeLink		before link text/HTML					text			''
		afterLink		after link text/html					text			''
		echo			echo content or return content			true/false		true

	NOTE: True must be expressed as a 1 and False as a zero

 	========================================================================================*/

function sp_do_sp_ProfileLinkTag($args = '') {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	sp_check_api_support();

	if (SP()->user->thisUser->ID == 0 && SP()->user->thisUser->ID == '') return;

	$defs = array('linkText'	=> __('Your Profile', 'sp-ttags'),
				  'beforeLink'	=> '',
				  'afterLink'	=> '',
			 	  'echo'		=> 1
			 	  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_ProfileLinkTag_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$linkText	= SP()->displayFilters->title($linkText);
	$beforeLink	= SP()->displayFilters->title($beforeLink);
	$afterLink	= SP()->displayFilters->title($afterLink);
	$echo		= (int) $echo;

	if(!empty($beforeLink)) $beforeLink = trim($beforeLink).' ';
	if(!empty($afterLink)) $afterLink = ' '.trim($afterLink);

	$out = '';

	$out.= "<span>$beforeLink<a href='".SP()->spPermalinks->get_url('profile')."'>$linkText</a>$afterLink</span>\n";
	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_do_ProfileLinkShortcode($atts) {
    $args = array();
    if (isset($atts['linktext']))   $args['linkText']   = $atts['linktext'];
    if (isset($atts['beforelink'])) $args['beforeLink'] = $atts['beforelink'];
    if (isset($atts['afterlink']))  $args['afterLink']  = $atts['afterlink'];

    $args['echo'] = 0;
    return sp_do_sp_ProfileLinkTag($args);
}
