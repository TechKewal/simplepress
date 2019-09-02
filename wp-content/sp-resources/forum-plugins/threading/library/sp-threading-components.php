<?php
/*
Simple:Press
Threading Plugin components Routine
$LastChangedDate: 2015-03-14 17:51:36 +0000 (Sat, 14 Mar 2015) $
$Rev: 12582 $
*/

# ----------------------------------------------------------------------------------------
# Adds the CSS to each post to indent
function sp_threading_do_postRowClass($rowClass, $sectionName, $a) {
	$rowClass.= ' spThread'.SP()->forum->view->thisPost->thread_level;
	return $rowClass;
}

# ----------------------------------------------------------------------------------------
# Displays the indent markers
function sp_threading_do_postRowIndent() {
	if (SP()->forum->view->thisPost->thread_level) {
		$out = '';
		$class = (SP()->core->device == 'mobile') ? 'spIndentMobile' : 'spIndent';
		for ($x=0; $x<SP()->forum->view->thisPost->thread_level; $x++) {
			$id = 'spThread'.SP()->forum->view->thisPost->post_id.$x;
			$out.= "<div id='$id' class='$class'></div>";
		}
		return $out;
	}
}
