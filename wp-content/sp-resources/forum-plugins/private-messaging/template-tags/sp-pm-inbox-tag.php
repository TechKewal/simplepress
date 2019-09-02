<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/* 	=====================================================================================
	sp_pm_do_inbox($display)

	template tag to display number of new PMs in the current user inbox.  This tag includes
	default text that is output with the pm count data and inbox hyperlink.   This text can
	be supressed by setting $display to false. 	If supressed, the new PM count and hyperlink
	are returned to the call in an array.  A -1 count and empty url will be returned for
	guests or user that do not have PM permissions.  Additionally, if the default text is used,
	the no permissions for pm default text can be supressed or those without permissions.

	parameters:

		$display		Determines whether to display pm count plus informational text
		$usersonly		If $display is true, only display pm text for users with pm permissions
 	===================================================================================*/
function sp_pm_do_inbox($display=true, $usersonly=false) {
    require_once PMLIBDIR.'sp-pm-database.php';

    #check if forum displayed
    if (sp_abort_display_forum()) return;

    sp_forum_ajax_support();

	if (!sp_pm_get_auth('use_pm') || (isset(SP()->user->thisUser->pmoptout) && SP()->user->thisUser->pmoptout)) return;

	$pm = array();
	if (sp_pm_get_auth('use_pm')) {
		$pm['count'] = sp_pm_get_inbox_unread_count(SP()->user->thisUser->ID);
		$pm['url'] = SP()->spPermalinks->get_url('private-messaging/inbox');
	} else {
		$pm['count'] = -1;
		$pm['url'] = '';
	}

	if ($display) {
		$out = '';
		if (sp_pm_get_auth('use_pm')) {
			$out.= '<p class="sfpmcount">';
			$out.= __('You have', 'sp-pm').' '.$pm['count'].' '.__('PM(s) in your', 'sp-pm').' '.'<a href="'.$pm['url'].'">'.__('inbox', 'sp-pm').'</a>.';
			$out.= '</p>';
		} elseif (!$usersonly) {
			$out.= '<p class="sfpmcount">';
			$out.= __('You do not have permissions to use private messaging', 'sp-pm');
			$out.= '</p>';
		}
		echo $out;
	}
	return $pm;
}
