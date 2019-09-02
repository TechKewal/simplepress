<?php
/*
Simple:Press
Announce plugin template tag
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_do_AnnounceMessage($args) {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

    $data = SP()->options->get('announce');

    # check for message to dispaly
    if (empty($data['message'])) return;

    # check if user should see the announcement
    if ($data['showto'] == 2 && !is_user_logged_in()) return;
    if ($data['showto'] == 3 && is_user_logged_in()) return;

	$defs = array('tagId'          => 'spAnnounceMessage',
                  'tagClass'   	   => 'spAnnounceMessage',
				  'echo'		   => 1,
				  'get'			   => 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_AnnounceMessage_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId	           = esc_attr($tagId);
	$tagClass	       = esc_attr($tagClass);
	$echo		       = (int) $echo;
	$get		       = (int) $get;

	if ($get) return $data['message'];

	$out = "<div id='$tagId' class='$tagClass'>\n";
    $out.= SP()->displayFilters->text($data['message']);
    $out.= '</div>';

	$out = apply_filters('sph_AnnounceMessage', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}
