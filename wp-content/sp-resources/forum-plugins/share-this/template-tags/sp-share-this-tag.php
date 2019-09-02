<?php
/*
Simple:Press
Share This Plugin Template Tag
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this tag is intended to be used on any wp page or post
# it will use the permalink of the wp page to share. Must be used with the loop.
# Do NOT use on any forum page except for Group View or it will share the main forum page
function sp_do_ShareThisTag($args='') {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	$defs = array('tagId'    	=> 'ShareThisTag',
				  'tagClass' 	=> 'ShareThisTag',
				  'echo'		=> 1
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_ShareThisTag_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$echo			= (int) $echo;

    $out = '';
    $out.= "<div id='$tagId' class='$tagClass'>";

    $post = get_post();
    $url = "st_url='".urlencode(get_permalink())."'";
    $title = str_replace('%', '%25', $post->title);
    $title = "st_title='".esc_attr($title)."'";
    $summary = (empty($post->post_excerpt)) ? SP()->displayFilters->tooltip($post->post_content, 0) : $post->post_excerpt;
    $summary = "st_summary='".esc_attr($summary)."'";

    $out.= sp_share_this_do_sharing($url, $title, $summary);

    $out.= '</div>';

	$out = apply_filters('sph_ShareThisTag', $out);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}
