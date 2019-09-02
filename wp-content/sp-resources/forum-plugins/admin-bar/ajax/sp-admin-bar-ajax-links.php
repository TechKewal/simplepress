<?php
/*
Simple:Press
Admin Bar plugin ajax routine for links
$LastChangedDate: 2018-10-17 15:17:49 -0500 (Wed, 17 Oct 2018) $
$Rev: 15756 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

if (empty($_GET['targetaction'])) die();
$action = SP()->filters->str($_GET['targetaction']);

if ($action == 'manage') {
	sp_forum_ajax_support();

	$br = (current_theme_supports('sp-theme-responsive')) ? '<br />' : '';

	$out = '';
	$out.= '<div id="spMainContainer" class="spAdminLinksPopup">';

	$out.= '<div class="spForumToolsHeader">';
	$out.= '<div class="spForumToolsHeaderTitle">'.__('Administration Links', 'spab').'</div>';
	$out.= '</div>';

	$out.= sp_open_grid();

	if (SP()->auths->current_user_can('SPF Manage Forums')) {
		$out.= sp_open_grid_cell();
		$out.= '<p><a href="'.SPADMINFORUM.'">';
		$out.= SP()->theme->paint_icon("spIcon", SPABIMAGES, "sp_ManageForums.png").$br;
		$out.= __('Forums', 'spab').'</a></p>';
		$out.= sp_close_grid_cell();
	}

	if (SP()->auths->current_user_can('SPF Manage Options')) {
		$out.= sp_open_grid_cell();
		$out.= '<p><a href="'.SPADMINOPTION.'">';
		$out.= SP()->theme->paint_icon("spIcon", SPABIMAGES, "sp_ManageOptions.png").$br;
		$out.= __('Options', 'spab').'</a></p>';
		$out.= sp_close_grid_cell();
	}

	if (SP()->auths->current_user_can('SPF Manage Components')) {
		$out.= sp_open_grid_cell();
		$out.= '<p><a href="'.SPADMINCOMPONENTS.'">';
		$out.= SP()->theme->paint_icon("spIcon", SPABIMAGES, "sp_ManageComponents.png").$br;
		$out.= __('Components', 'spab').'</a></p>';
		$out.= sp_close_grid_cell();
	}

	if (SP()->auths->current_user_can('SPF Manage User Groups')) {
		$out.= sp_open_grid_cell();
		$out.= '<p><a href="'.SPADMINUSERGROUP.'">';
		$out.= SP()->theme->paint_icon("spIcon" ,SPABIMAGES, "sp_ManageUsergroups.png").$br;
		$out.= __('Usergroups', 'spab').'</a></p>';
		$out.= sp_close_grid_cell();
	}

	if (SP()->auths->current_user_can('SPF Manage Permissions')) {
		$out.= sp_open_grid_cell();
		$out.= '<p><a href="'.SPADMINPERMISSION.'">';
		$out.= SP()->theme->paint_icon("spIcon" ,SPABIMAGES, "sp_ManagePermissions.png").$br;
		$out.= __('Permissions', 'spab').'</a></p>';
		$out.= sp_close_grid_cell();
	}

	if (SP()->auths->current_user_can('SPF Manage Options')) {
		$out.= sp_open_grid_cell();
		$out.= '<p><a href="'.SPADMININTEGRATION.'">';
		$out.= SP()->theme->paint_icon("spIcon" ,SPABIMAGES, "sp_ManageIntegration.png").$br;
		$out.= __('Integration', 'spab').'</a></p>';
		$out.= sp_close_grid_cell();
	}

	if (SP()->auths->current_user_can('SPF Manage Profiles')) {
		$out.= sp_open_grid_cell();
		$out.= '<p><a href="'.SPADMINPROFILE.'">';
		$out.= SP()->theme->paint_icon("spIcon" ,SPABIMAGES, "sp_ManageProfiles.png").$br;
		$out.= __('Profiles', 'spab').'</a></p>';
		$out.= sp_close_grid_cell();
	}

	if (SP()->auths->current_user_can('SPF Manage Admins')) {
		$out.= sp_open_grid_cell();
		$out.= '<p><a href="'.SPADMINADMIN.'">';
		$out.= SP()->theme->paint_icon("spIcon" ,SPABIMAGES, "sp_ManageAdmins.png").$br;
		$out.= __('Admins', 'spab').'</a></p>';
		$out.= sp_close_grid_cell();
	}

	if (SP()->auths->current_user_can('SPF Manage Users')) {
		$out.= sp_open_grid_cell();
		$out.= '<p><a href="'.SPADMINUSER.'">';
		$out.= SP()->theme->paint_icon("spIcon" ,SPABIMAGES, "sp_ManageUsers.png").$br;
		$out.= __('Users', 'spab').'</a></p>';
		$out.= sp_close_grid_cell();
	}

	if (SP()->auths->current_user_can('SPF Manage Plugins')) {
		$out.= sp_open_grid_cell();
		$out.= '<p><a href="'.SPADMINPLUGINS.'">';
		$out.= SP()->theme->paint_icon("spIcon" ,SPABIMAGES, "sp_ManagePlugins.png").$br;
		$out.= __('Plugins', 'spab').'</a></p>';
		$out.= sp_close_grid_cell();
	}

	if (SP()->auths->current_user_can('SPF Manage Themes')) {
		$out.= sp_open_grid_cell();
		$out.= '<p><a href="'.SPADMINTHEMES.'">';
		$out.= SP()->theme->paint_icon("spIcon" ,SPABIMAGES, "sp_ManageThemes.png").$br;
		$out.= __('Themes', 'spab').'</a></p>';
		$out.= sp_close_grid_cell();
	}

	if (SP()->auths->current_user_can('SPF Manage Toolbox')) {
		$out.= sp_open_grid_cell();
		$out.= '<p><a href="'.SPADMINTOOLBOX.'">';
		$out.= SP()->theme->paint_icon("spIcon" ,SPABIMAGES, "sp_ManageToolbox.png").$br;
		$out.= __('Toolbox', 'spab').'</a></p>';
		$out.= sp_close_grid_cell();
	}

	$out = apply_filters('sph_ShowAdminLinks', $out, $br);

	$out.= sp_close_grid();

	$out.= "</div>\n";

	echo $out;
}
die();
