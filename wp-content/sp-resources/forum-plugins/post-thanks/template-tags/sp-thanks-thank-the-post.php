<?php
/*
Simple:Press
Thanks Plugin - Function to create the button that makes thanking possible
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_thanks_do_thank_the_post($args='', $label='', $thankedLabel='', $toolTip='', $thankedToolTip='') {
   	if (!SP()->auths->get('thank_posts', SP()->forum->view->thisTopic->forum_id)) return;
    if (empty(SP()->forum->view->thisPost->user_id)) return;
    if (SP()->user->thisUser->ID == SP()->forum->view->thisPost->user_id) return;

	$defs = array('tagId' 	     => 'spThanks%ID%',
                  'tagClass' 	 => 'spButton',
                  'formClass' 	 => 'spThanks',
                  'thankedClass' => 'spThanked',
				  'iconClass'	 => 'spIcon',
				  'iconThanks'	 => 'sp_ThanksIcon.png',
				  'iconThanked'	 => 'sp_ThankedIcon.png',
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PostThank_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$thankedLabel	= SP()->displayFilters->title($thankedLabel);
	$toolTip		= esc_attr($toolTip);
	$thankedToolTip	= esc_attr($thankedToolTip);
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$formClass		= esc_attr($formClass);
	$thankedClass	= esc_attr($thankedClass);
	$iconClass		= esc_attr($iconClass);
	$iconThankedJS	= sanitize_file_name($iconThanked);
	$iconThanks		= SP()->theme->paint_icon($iconClass, THANKSIMAGES, sanitize_file_name($iconThanks));
	$iconThanked	= SP()->theme->paint_icon($iconClass, THANKSIMAGES, sanitize_file_name($iconThanked), $thankedToolTip);
	$label			= SP()->displayFilters->title($label);

 	$tagId = str_ireplace('%ID%', SP()->forum->view->thisPost->post_id, $tagId);

    $out = '';

	$thanked = false;
	if (!empty(SP()->forum->view->thisPost->post_thanks)) {
		foreach( SP()->forum->view->thisPost->post_thanks as $user) {
			if ($user->user_id == SP()->user->thisUser->ID) {
				$thanked = true;
				break;
			}
		}
    }

	if ($thanked) {
		  $out.= "<a id='$tagId' class='$tagClass $thankedClass' rel='nofollow' title='$thankedToolTip'>$iconThanked$thankedLabel</a>";
	} else {
	    $ajaxURL = wp_nonce_url(SPAJAXURL."thanks&fid=".SP()->forum->view->thisTopic->forum_id."&tid=".SP()->forum->view->thisTopic->topic_id."&pid=".SP()->forum->view->thisPost->post_id."&cuser=".SP()->user->thisUser->ID."&puser=".SP()->forum->view->thisPost->user_id, 'thanks');
        $out.= "<a rel='nofollow' id='$tagId' class='$tagClass spThankPost' title='$toolTip' data-url='$ajaxURL' data-postid='".SP()->forum->view->thisPost->post_id."' data-thanked='".esc_attr($thankedLabel)."' data-img='".esc_attr($iconThankedJS)."' data-iclass='".esc_attr($iconClass)."'>";
		$out.= $iconThanks.$label.'</a>';
    }

	$out = apply_filters('sph_PostThank', $out, $a);
	echo $out;
}
