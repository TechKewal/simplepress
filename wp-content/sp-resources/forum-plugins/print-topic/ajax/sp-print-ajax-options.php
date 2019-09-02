<?php
/*
Simple:Press
Print Topic plugin ajax Options popup
$LastChangedDate: 2013-03-12 03:54:45 +0000 (Tue, 12 Mar 2013) $
$Rev: 10061 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

if (!sp_nonce('print')) die();

# get out of here if no action specified
$url = SP()->filters->str($_GET['url']);
$page = SP()->filters->integer($_GET['page']);
$tPage = SP()->filters->integer($_GET['totalpages']);
$id = SP()->filters->integer($_GET['id']);
$pSize = (isset($_COOKIE['spPSize'])) ? $_COOKIE['spPSize'] : 65;
$spIndex = (isset($_GET['index'])) ? SP()->filters->integer($_GET['index']) : 0;
$permalink = trailingslashit(SP()->filters->str($url)).'topicprint';

$bLabel = ($spIndex) ? __('Post', 'sp-print') : __('Topic', 'sp-print');

$out = '<div id="spMainContainer" class="spForumToolsPopup">';
$out.= '<form action="'.$permalink.'" method="post" id ="printopts" name="printopts">';

$out.= '<input type="hidden" name="spPageNo" id="spPageNo" value="'.$page.'" />';
$out.= '<input type="hidden" name="spTopicId" id="spTopicId" value="'.$id.'" />';
$out.= '<input type="hidden" name="spIndex" id="spIndex" value="'.$spIndex.'" />';
$out.= '<div style="font-size:18px;font-weight:bold;">'.sprintf(__('Print %s', 'sp-print'), $bLabel).'</div>';

if ($tPage == 1 || $spIndex > 0) {
	$out.= '<input type="hidden" name="spThisPage" id="spThisPage" value="1" />';
} else {
	$out.= '<fieldset style="border:1px solid gray;"><legend>'.__('Printing Scope', 'sp-print').'</legend>';
	$out.= '<input type="radio" id="spPrint1" name="spThisPage" value="1" checked="checked" />';
	$out.= '<label class="spRadio" for="spPrint1">'.__('Print entire topic', 'sp-print').'</label><br />';
	$out.= '<input type="radio" id="spPrint2" name="spThisPage" value="2" />';
	$out.= '<label class="spRadio" for="spPrint2">'.__('Print this page only', 'sp-print').'</label>';
	$out.= '</fieldset>';
}

$out.= '<fieldset style="border:1px solid gray;"><legend>'.__('Image Printing', 'sp-print').'</legend>';
$out.= '<input type="radio" id="spImage1" name="spEnlarge" value="1" checked="checked" />';
$out.= '<label class="spRadio" for="spImage1">'.__('Print images as thumbnails', 'sp-print').'</label><br />';
$out.= '<input type="radio" id="spImage2" name="spEnlarge" value="2" />';
$out.= '<label class="spRadio" for="spImage2">'.__('Enlarge images to available width', 'sp-print').'</label>';
$out.= '</fieldset>';

$out.= '<fieldset style="border:1px solid gray;"><legend>'.__('Print Size', 'sp-print').'</legend>';
$out.= '<span>'.__('Print font size as percentage', 'sp-print');
$out.= '&nbsp;&nbsp;<input class="spControl spCenter" type="number" name="spPSize" id="spPSize" max="100" min="50" value="'.$pSize.'" style="width: 20%;border:1px solid #555;"/></span>';
$out.= '</fieldset>';

$out.= '<div class="spCenter"><br />';
$out.= '<input class="spSubmit" type="submit" id="spPrint" name="spPrint" value="'.sprintf(__('Open this %s in Print View', 'sp-print'), $bLabel).'" />';
$out.= '<input type="button" class="spSubmit spCancelScript" name="cancel" value="'.SP()->primitives->front_text('Cancel').'" />';
$out.= '</div>';

$out.= '</form>';
$out.= '</div>';

echo $out;

die();
