<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# --------------------------------------------------------------------------------------
#
#	sp_AdminQueue()
#	Display the admin queue
#	Scope:	site
#
#	class:			CSS styling class
#
#	label:
#
# --------------------------------------------------------------------------------------
function sp_AdminQueueTag($args, $viewLabel='', $unreadLabel='', $modLabel='', $spamLabel='', $toolTip='') {
    require_once SPABLIBDIR.'sp-admin-bar-components.php';
	global $spNewPosts;

	# bail if not admin or moderator
	if (!SP()->user->thisUser->admin && !SP()->user->thisUser->moderator) return;

	# is this admin showing the admin bar?
	if (!isset(SP()->user->thisUser->sfadminbar) || SP()->user->thisUser->sfadminbar==false ) return;

	$defs = array('tagId'       => 'spAdminQueue',
                  'tagClass'    => 'spAdminQueue',
				  'buttonClass' => 'spButton',
				  'icon' 	    => 'sp_AdminQueue.png',
				  'iconClass'   => 'spIcon',
				  'countClass'  => 'spButtonAsLabel',
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_AdminQueue_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId 	    = esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);

	$out = '';
	$out.= "<div id='$tagId' class='$tagClass'>";
	$out.= '<div id="spAdminQueueCounts">';
	$out.= sp_GetWaitingUrl($spNewPosts, $a, $viewLabel, $unreadLabel, $modLabel, $spamLabel, $toolTip);
	$out.= '</div>';
	$out.= '<img class="spInlineSection" id="spBarSpinner" src="'.SPCOMMONIMAGES.'working.gif" alt="" />';
    $out.= '</div>';
    $out.= sp_InsertBreak('echo=0');
	$out.= '<div id="spAdminQueueList"></div>';

	$out = apply_filters('sph_AdminQueue', $out, $a);
	echo $out;
}
