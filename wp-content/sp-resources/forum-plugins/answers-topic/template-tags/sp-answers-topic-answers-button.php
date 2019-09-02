<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_do_AnswersTopicPostIndexAnswer($args='', $markLabel='', $markToolTip='', $unmarkLabel='', $unmarkToolTip='') {
	if (SP()->user->thisUser->guest) return; # guests cannot mark topic answer
	if (!SP()->user->thisUser->admin && !SP()->user->thisUser->moderator && SP()->forum->view->thisTopic->topic_starter != SP()->user->thisUser->ID) return; # must be topic starter or admin/mod
    if (SP()->forum->view->thisPost->post_index == 1) return; # dont show mark answer on first post

	$defs = array('tagId' 		=> 'spAnswersTopicAnswersButton%ID%',
                  'tagClass' 	=> 'spAnswersTopicAnswersButton',
				  'iconClass'	=> 'spIcon',
				  'markIcon'	=> 'sp_AnswersTopicMarkButton.png',
				  'unmarkIcon'	=> 'sp_AnswersTopicUnmarkButton.png',
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_AnswersTopicAnswersButton_args', $a);
	extract($a, EXTR_SKIP);

	$p = (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? SPANSWERSIMAGESMOB : SPANSWERSIMAGES;

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$iconClass		= esc_attr($iconClass);
	$markIcon		= SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($markIcon));
	$unmarkIcon		= SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($unmarkIcon));
	$markLabel		= SP()->displayFilters->title($markLabel);
	$markToolTip	= esc_attr($markToolTip);
	$unmarkLabel	= SP()->displayFilters->title($unmarkLabel);
	$unmarkToolTip	= esc_attr($unmarkToolTip);

	$tagId = str_ireplace('%ID%', SP()->forum->view->thisPost->post_id, $tagId);

    $out = '';
    if (!SP()->forum->view->thisTopic->answered) {
        $url = SP()->spPermalinks->get_query_url(SP()->spPermalinks->build_url(SP()->forum->view->thisTopic->forum_slug, SP()->forum->view->thisTopic->topic_slug, SP()->rewrites->pageData['page']))."mark-answer=1&amp;topic=".SP()->forum->view->thisTopic->topic_id."&amp;post=".SP()->forum->view->thisPost->post_id;
    	$out.= "<a rel='nofollow' id='$tagId' class='$tagClass' title='$markToolTip' href='$url' >";
    	$out.= $markIcon;
    	if (!empty($markLabel)) $out.= "$markLabel";
    	$out.= "</a>\n";
    } elseif (SP()->forum->view->thisTopic->answered == SP()->forum->view->thisPost->post_id) {
        $url = SP()->spPermalinks->get_query_url(SP()->spPermalinks->build_url(SP()->forum->view->thisTopic->forum_slug, SP()->forum->view->thisTopic->topic_slug, SP()->rewrites->pageData['page']))."unmark-answer=1&amp;topic=".SP()->forum->view->thisTopic->topic_id."&amp;post=".SP()->forum->view->thisPost->post_id;
    	$out.= "<a rel='nofollow' id='$tagId' class='$tagClass' title='$unmarkToolTip' href='$url' >";
    	$out.= $unmarkIcon;
    	if (!empty($unmarkLabel)) $out.= "$unmarkLabel";
    	$out.= "</a>\n";
    }

	$out = apply_filters('sph_AnswersTopicAnswersButton', $out, $a);
	echo $out;
}
