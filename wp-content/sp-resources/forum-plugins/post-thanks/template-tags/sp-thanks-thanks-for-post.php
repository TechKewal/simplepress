<?php
/*
Simple:Press
Thanks Plugin - Function to display the people who have thanked the post
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_thanks_do_thanks_for_post($args='') {
	$defs = array('tagId' 	     => 'spThanksList%ID%',
                  'tagClass' 	 => 'spThanksList',
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PostThanksList_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);

 	$tagId = str_ireplace('%ID%', SP()->forum->view->thisPost->post_id, $tagId);

    $out = '';

	#Get the list of user id's that have thanked this post
	$thanksdata = SP()->options->get('thanks');
	if (empty(SP()->forum->view->thisPost->post_thanks)) {
    	$out.= "<div id='$tagId' class='$tagClass spInlineSection'>";
    } else {
    	$out.= "<div id='$tagId' class='$tagClass'>";
        $out.= "<p>".$thanksdata['thank-message-before-name'].' <span>'.SP()->forum->view->thisPostUser->display_name.'</span> '.$thanksdata['thank-message-after-name'].': </p>';
    	$first = true;
		foreach(SP()->forum->view->thisPost->post_thanks as $user) {
			$name = SP()->user->name_display($user->user_id, $user->display_name);
            if (!$first) $out.= ', ';
            $out.= $name;
            $first = false;
    	}
    }
	$out.= '</div>';

	$out = apply_filters('sph_PostThanksList', $out, $a);

	echo $out;
}
