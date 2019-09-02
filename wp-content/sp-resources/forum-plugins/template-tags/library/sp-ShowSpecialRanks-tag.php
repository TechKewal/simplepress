<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/* 	=====================================================================================
	sp_do_sp_ShowSpecialRanksTag($userid, $args)

	displays special forum rank and badge for the specified user

	parameters:
		name			description								type			default
		----------------------------------------------------------------------------------------
		tagClass		class to be applied for styling			text			spForumRankTag
		titleClass		class to be applied to list item style	text			spForumRankTag
		badgeClass		class to be applied to text labels		text			spForumRankTag
		showTitle		show the rank title     				int    			1
		showBadge		show the rank badge     				int 	   		1
		echo			echo content or return content			int   		    1
 	===================================================================================*/
function sp_do_sp_ShowSpecialRanksTag($userid, $args='') {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	if (empty($userid)) return;

	$defs = array('tagClass'	=> 'spSpecialRankTag',
				  'titleClass'	=> 'spSpecialRankTag',
			 	  'badgeClass'	=> 'spSpecialRankTag',
				  'showTitle'	=> 1,
				  'showBadge'	=> 1,
				  'echo'		=> 1,
				  );
	$a = wp_parse_args($args, $defs);
	extract($a, EXTR_SKIP);

	$echo = (int) $echo;

	$thisUser = SP()->user->get($userid);
    require_once SP_PLUGIN_DIR.'/forum/content/sp-common-view-functions.php';
    $data = sp_UserSpecialRank($a, $thisUser->special_rank);

    if ($echo) {
        echo $data;
    } else {
        return $data;
    }
}

function sp_do_ShowSpecialRanksShortcode($atts) {
    $args = array();
    $userid = $atts['userid'];
    if (isset($atts['tagclass']))   $args['tagClass']   = $atts['tagclass'];
    if (isset($atts['titleclass'])) $args['titleClass'] = $atts['titleclass'];
    if (isset($atts['badgeclass'])) $args['badgeClass'] = $atts['badgeclass'];
    if (isset($atts['showtitle']))  $args['showTitle']  = $atts['showtitle'];
    if (isset($atts['showbadge']))  $args['showBadge']  = $atts['showbadge'];

    $args['echo'] = 0;
    return sp_do_sp_ShowSpecialRanksTag($userid, $args);
}
