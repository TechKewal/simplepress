<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_reputation_post_index_level($args='') {
    # no rep or badges for admins or guests
    if (SP()->forum->view->thisPostUser->guest) return;

	$defs = array('tagId'    	      => 'spPostIndexReputationLevel%ID%',
				  'tagClass' 	      => 'spPostReputationLevel',
				  'imgClass'	      => 'spReputationBadge',
				  'showBadge'	      => 1,
				  'showTitle'	      => 0,
				  'showRep' 	      => 0,
                  'stack'             => 1,
				  'echo'		      => 1,
				  'get'			      => 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PostIndexReputationLevel_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		        = esc_attr($tagId);
	$tagClass	        = esc_attr($tagClass);
	$imgClass	        = esc_attr($imgClass);
	$showBadge	        = (int) $showBadge;
	$showTitle	        = (int) $showTitle;
	$showRep	        = (int) $showRep;
	$stack	            = (int) $stack;
	$echo		        = (int) $echo;
	$get		        = (int) $get;

	$tagId = str_ireplace('%ID%', SP()->forum->view->thisPost->post_id, $tagId);

	if ($get) return SP()->forum->view->thisPostUser->reputation_level;

    $show = false;
	$tout = "<div id='$tagId' class='$tagClass'>";
	if ($showBadge && !empty(SP()->forum->view->thisPostUser->reputation_level->badge)) {
	    $show = true;
        $rep = (SP()->forum->view->thisPostUser->admin) ? '' :  " (".SP()->forum->view->thisPostUser->reputation.")";
		$tout.= "<img class='$imgClass vtip' src='".SP()->forum->view->thisPostUser->reputation_level->badge."' alt='".esc_attr(SP()->forum->view->thisPostUser->reputation_level->name)."' title='".esc_attr(SP()->forum->view->thisPostUser->reputation_level->name).$rep."' />";
	}
	if ($showTitle && !empty(SP()->forum->view->thisPostUser->reputation_level->name)) {
	    $show = true;
		if ($stack) $tout.= "<br />";
		$tout.= '<span class="spReputationLevel-'.sp_create_slug(SP()->forum->view->thisPost->postUser->reputation_level->name, false).'">'.SP()->forum->view->thisPostUser->reputation_level->name.'</span>';
  	}
	if ($showRep && !SP()->forum->view->thisPostUser->admin) {
	    $show = true;
		if ($stack) $tout.= "<br />";
		$tout.= '<span class="spUserRep">'.SP()->forum->view->thisPostUser->reputation.'</span>';
	}
	$tout.= "</div>\n";

    $out = ($show) ? $tout : '';
	$out = apply_filters('sph_PostIndexReputationLevel', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}
