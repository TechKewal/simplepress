<?php
/*
Simple:Press
Topic Description Plugin components Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

function sp_topic_description_do_head() {
   	$css = SP()->theme->find_css(SPTDCSS, 'sp-topic-description.css', 'sp-topic-description.spcss');
    SP()->plugin->enqueue_style('sp-topic_description', $css);
}

function sp_topic_description_do_topic_form($out, $a) {
	global $tab;
	extract($a, EXTR_SKIP);

    $label = apply_filters('sph_topic_description_label', __('Topic description', 'sp-topic-description'));
	$out.= "<br />$label: ";
	$out.= "<textarea id='spTopicDesc' rows='2' class='$controlInput spTopicDescription' name='newtopicdesc' tabindex='".$tab++."'></textarea>\n";

    return $out;
}

function sp_topic_description_do_create_topic($newpost) {
    if ($newpost['action'] == 'post') return;

	$description = (!empty($_POST['newtopicdesc'])) ? SP()->saveFilters->text(trim($_POST['newtopicdesc'])) : '';
	if (!empty($description)) SP()->DB->execute('UPDATE '.SPTOPICS." SET topic_desc='$description' WHERE topic_id=".$newpost['topicid']);
}

function sp_topic_description_do_display_desc($out, $a) {
    $temp = '';
    if (!empty(SP()->forum->view->thisTopic->topic_desc)) $temp.= "<div id='spTopicHeaderDescription' class='spHeaderDescription'>".SP()->forum->view->thisTopic->topic_desc."</div>";
    $temp = apply_filters('sph_topic_description', $temp);
    $out.= $temp;
    return $out;
}

function sp_topic_description_do_edit($out, $topic) {
	$out.= '<div class="spHeaderName">'.__('Topic Description', 'sp-topic-description').':</div>';
	$out.= '<div><textarea class="spControl" name="topicdesc" rows="2">'.esc_textarea($topic->topic_desc).'</textarea></div>';
    return $out;
}

function sp_topic_description_do_edit_topic_desc($topicid) {
	$description = SP()->saveFilters->text(trim($_POST['topicdesc']));
	SP()->DB->execute('UPDATE '.SPTOPICS." SET topic_desc='$description' WHERE topic_id=$topicid");
}
