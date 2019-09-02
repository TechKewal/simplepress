<?php

/**
 * Add capability field while updating admin
 * 
 * @param object $user
 */
function sp_analytics_do_admin_cap_list($user) {
	$can_manage = user_can( $user, 'SPF Manage Analytics' );
	echo '<li>';
	spa_render_caps_checkbox( __('Manage Analytics', 'sp-analytics'), "manage-analytics[$user->ID]", $can_manage, $user->ID );
	echo "<input type='hidden' name='old-analytics[$user->ID]' value='$can_manage' />";
	echo '</li>';
}

/**
 * Add capability field while adding new admins
 * 
 * @param object $user
 */
function sp_analytics_do_admin_cap_form($user) {
	echo '<li>';
	spa_render_caps_checkbox(__('Manage Analytics', 'sp-analytics'), 'add-analytics', 0);
	echo '</li>';
}

/**
 * Manage admin capability while adding updating admins
 * 
 * @param string $still_admin
 * @param array $remove_admin
 * @param object $user
 * 
 * @return array
 */
function sp_analytics_do_admin_caps_update($still_admin, $remove_admin, $user) {
	$manage_analytics = (isset($_POST['manage-analytics'])) ? $_POST['manage-analytics'] : '';
	$old_analytics = (isset($_POST['old-analytics'])) ? $_POST['old-analytics'] : '';

	# was this admin removed?
	if (isset($remove_admin[$user->ID])) $manage_analytics = '';

	if (isset($manage_analytics[$user->ID])) {
		$user->add_cap('SPF Manage Analytics');
	} else {
		$user->remove_cap('SPF Manage Analytics');
	}
	$still_admin = $still_admin || isset($manage_analytics[$user->ID]);
	return $still_admin;
}


/**
 * Manage admin capability while adding new admins
 * 
 * @param string $newadmin
 * @param object $user
 * 
 * @return string
 */
function sp_analytics_do_admin_caps_new($newadmin, $user) {
	$analytics = (isset($_POST['add-analytics'])) ? $_POST['add-analytics'] : '';
	if ($analytics == 'on') $user->add_cap('SPF Manage Analytics');
	$newadmin = $newadmin || $analytics == 'on';
	return $newadmin;
}
