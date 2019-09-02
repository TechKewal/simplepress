<?php
/*
Simple:Press Plugin Title: Analytics
Version: 1.0.0
Item Id: 79338
Plugin URI: https://simple-press.com
Description: A Simple:Press plugin for analytics and charts
Author: Simple:Press
Original Author: Tahir Nazir
Author URI: https://simple-press.com
Simple:Press Versions: 6.0.7 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SP_ANALYTICS_DB_VERSION', 1 );

define('SP_ANALYTICS_DIR',			SPPLUGINDIR.'analytics/' );
define('SP_ANALYTICS_ADMINDIR',		SPPLUGINDIR.'analytics/admin/' );
define('SP_ANALYTICS_LIBDIR',		SPPLUGINDIR.'analytics/library/' );
define('SP_ANALYTICS_LIBURL',		SPPLUGINURL.'analytics/library/' );
define('SP_ANALYTICS_CSS',			SPPLUGINURL.'analytics/resources/css/' );
define('SP_ANALYTICS_IMAGES',		SPPLUGINURL.'analytics/resources/images/' );
define('SP_ANALYTICS_SCRIPT',		SPPLUGINURL.'analytics/resources/js/' );


require_once SP_ANALYTICS_DIR .'charts/sp-chart.php';
require_once SP_ANALYTICS_DIR .'charts/sp-forum-posts.php';
require_once SP_ANALYTICS_DIR .'charts/sp-forum-topics.php';
require_once SP_ANALYTICS_DIR .'charts/sp-group-users.php';
require_once SP_ANALYTICS_DIR .'charts/sp-top-posters.php';
require_once SP_ANALYTICS_DIR .'charts/sp-top-posters-all.php';

require_once SP_ANALYTICS_DIR .'charts/sp-new-post.php';
require_once SP_ANALYTICS_DIR .'charts/sp-new-topic.php';

require_once SP_ANALYTICS_DIR . 'charts/sp-chart-topic-status.php';
require_once SP_ANALYTICS_DIR . 'charts/sp-new-topic-status-posts.php';
require_once SP_ANALYTICS_DIR . 'charts/sp-total-topic-status-posts.php';
require_once SP_ANALYTICS_DIR . 'charts/sp-new-topic-status-topics.php';
require_once SP_ANALYTICS_DIR . 'charts/sp-total-topic-status-topics.php';
require_once SP_ANALYTICS_DIR . 'charts/sp-top-tags.php';

require_once SP_ANALYTICS_ADMINDIR .'spa-analytics-widgets.php';

require_once SP_ANALYTICS_DIR . 'library/sp-analytics-components.php';


add_action('sph_admin_menu',										'sp_analytics_menu');
add_action('sph_admin_menu',                                        'sp_analytics_admin_menu' );

add_action('init', 										            'sp_analytics_localization' );
add_action('sph_activate_analytics/sp-analytics-plugin.php',        'sp_analytics_install');
add_action('sph_deactivate_analytics/sp-analytics-plugin.php',      'sp_analytics_deactivate');
add_action('sph_uninstall_analytics/sp-analytics-plugin.php',       'sp_analytics_uninstall');
add_action('sph_activated', 				                        'sp_analytics_sp_activate');
add_action('sph_deactivated', 				                        'sp_analytics_sp_deactivate');
add_action('sph_uninstalled', 								        'sp_analytics_sp_uninstall');
add_action('sph_plugin_update_analytics/sp-analytics-plugin.php',   'sp_analytics_upgrade_check');

add_action('sph_activate_tags/sp-tags-plugin.php',					'sp_analytics_tags_install', 20 );
add_action('sph_uninstall_tags/sp-tags-plugin.php',					'sp_analytics_tags_uninstall', 20 );
add_action('sph_deactivate_tags/sp-tags-plugin.php',				'sp_analytics_tags_deactivate', 20 );

add_action('sph_activate_topic-status/sp-topicstatus-plugin.php',	'sp_analytics_topicstatus_install', 20 );
add_action('sph_uninstall_topic-status/sp-topicstatus-plugin.php', 	'sp_analytics_topicstatus_uninstall', 20 );
add_action('sph_deactivate_topic-status/sp-topicstatus-plugin.php', 'sp_analytics_topicstatus_deactivate', 20 );


add_filter('sph_admin_help-admin-plugins', 				'sp_analytics_admin_help', 10, 3 );

add_action('sph_admin_caps_form', 					    'sp_analytics_admin_cap_form', 10, 2);
add_action('sph_admin_caps_list', 						'sp_analytics_admin_cap_list', 10, 2);
add_filter('sph_admin_caps_new', 			            'sp_analytics_admin_caps_new', 10, 2);
add_filter('sph_admin_caps_update', 		            'sp_analytics_admin_caps_update', 10, 3);


add_action('wp_ajax_analytics_chart_update',			'sp_analytics_ajax_update_chart' );

add_filter('sph_plugins_active_buttons',    'sp_analytics_uninstall_option', 10, 2);


add_action( 'wp_dashboard_setup', 'sp_analytics_add_dashboard_widgets' );

add_action( 'admin_enqueue_scripts', 		'sp_analytics_admin_load_resources' );


/**
 * Add localization for the plugin
 */
function sp_analytics_localization() {
	sp_plugin_localisation( 'sp-analytics' );
}

/**
 * Enqueue resources
 * 
 * @global object $screen
 */
function sp_analytics_admin_load_resources() {
	global $screen;
	
	wp_enqueue_script('spa-jquery-canvasjs', SP_ANALYTICS_SCRIPT.'jquery.canvasjs.min.js', array('jquery'), false, false);
	
	wp_enqueue_script('sp-analytics', SP_ANALYTICS_SCRIPT.'script.js', array('jquery', 'spa-jquery-canvasjs' ), false, false);
	
	wp_enqueue_style( 'sp-analytics', SP_ANALYTICS_CSS . 'sp-analytics.css' );
	
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );
	
	if( $screen && $screen->id === 'dashboard' ) {
		include_once SPBOOT.'admin/spa-admin-framework.php';
		spa_enqueue_datepicker();
	}
}

/**
 * Register menu items
 */
function sp_analytics_menu() {
	
	$panels = array();


	$charts = sp_analytics_get_charts();

	$panels[ __( 'Charts', 'sp-analytics') ] = array( 'admin' => 'sp_analytics_main_view', 'form' => 0, 'id' => 'sp_analytics_charts' );

	foreach( $charts as $chart_id => $chart ) {

		$form_cb = "sp_analytics_admin_options_{$chart_id}_form";
		$save_cb = "sp_analytics_chart_options_save";
		$id = "sp_analytics_{$chart_id}_options";

		$panels[ $chart['title'] ] = array('admin' => $form_cb, 'save' => $save_cb, 'form' => 1, 'id' => $id );
	}
	
    SP()->plugin->add_admin_panel( __( 'Analytics', 'sp-analytics' ), 'SPF Manage Analytics', __( 'Analytics', 'sp-analytics' ), 'icon-Analytics', $panels, 1 );
}


/**
 * Add main analytics menu in wp admin sidebar navigation
 * 
 * @param string $parent
 * 
 * @return void
 */
function sp_analytics_admin_menu( $parent ) {
	if (!SP()->auths->current_user_can('SPF Manage Analytics')) return;
	add_submenu_page($parent, esc_attr(__('Analytics', 'sp-analytics')), esc_attr(__('Analytics', 'sp-analytics')), 'read', SP_FOLDER_NAME.'/admin/panel-plugins/spa-plugins.php&tab=plugin&admin=sp_analytics_main_view&save=&form=0&panel='.urlencode(__('Analytics', 'sp-analytics')), 'dummy');
}

/**
 * Load options form for a chart
 * 
 * @param string $chart_id
 */
function sp_analytics_load_chart_options_form( $chart_id ) {

	require_once SP_ANALYTICS_ADMINDIR . 'spa-analytics-options-form.php';
	sp_analytics_do_admin_chart_options( $chart_id );
}

/**
 * Save chart settings
 */
function sp_analytics_chart_options_save() {

	require_once SP_ANALYTICS_ADMINDIR . 'spa-analytics-options-save.php';
	
	$chart_id = $_POST['chart_id'];
	echo sp_analytics_do_admin_save_options( $chart_id );
}

/**
 * Load 'Total Post Count by Forum' chart options
 */
function sp_analytics_admin_options_forum_posts_form() {
	sp_analytics_load_chart_options_form( 'forum_posts' );
}

/**
 * Load 'Total Topic Count by Forum' chart options
 */
function sp_analytics_admin_options_forum_topics_form() {

	sp_analytics_load_chart_options_form( 'forum_topics' );
}

/**
 * Load 'Top 10 Posters' chart options
 */
function sp_analytics_admin_options_top_posters_all_form() {
	sp_analytics_load_chart_options_form( 'top_posters_all' );
}

/**
 * Load 'Top 10 Posters excluding wp admins and moderators' chart options
 */
function sp_analytics_admin_options_top_posters_form() {
	sp_analytics_load_chart_options_form( 'top_posters' );
}

/**
 * Load 'Total users by user group' chart options
 */
function sp_analytics_admin_options_group_users_form() {
	sp_analytics_load_chart_options_form( 'group_users' );
}

/**
 * Load 'New Post Count' chart options
 */
function sp_analytics_admin_options_new_post_form() {
	sp_analytics_load_chart_options_form( 'new_post' );
}

/**
 * Load 'New Topic Count' chart options
 */
function sp_analytics_admin_options_new_topic_form() {
	sp_analytics_load_chart_options_form( 'new_topic' );
}

/**
 * Load 'New Post Count by Topic Status' chart options
 */
function sp_analytics_admin_options_new_topic_status_posts_form() {
	sp_analytics_load_chart_options_form( 'new_topic_status_posts' );
}

/**
 * Load 'Total Post Count By Topic Status' chart options
 */
function sp_analytics_admin_options_total_topic_status_posts_form() {
	sp_analytics_load_chart_options_form( 'total_topic_status_posts' );
}

/**
 * Load 'New Topic Count by Topic Status' chart options
 */
function sp_analytics_admin_options_new_topic_status_topics_form() {
	sp_analytics_load_chart_options_form( 'new_topic_status_topics' );
}

/**
 * Load 'Total Topic Count by Topic Status' chart options
 */
function sp_analytics_admin_options_total_topic_status_topics_form() {
	sp_analytics_load_chart_options_form( 'total_topic_status_topics' );
}

/**
 * Load 'Top 10 Tags' chart options
 */
function sp_analytics_admin_options_top_tags_form() {
	sp_analytics_load_chart_options_form( 'top_tags' );
}

/**
 * Return all registered charts
 * 
 * @return array
 */
function sp_analytics_get_charts() {
	$charts = array(
		'forum_posts'  => array(
			'title' => __( 'Total Post Count by Forum', 'sp-analytics' ),
			'active' => true,
			'full_row' => true,
			'dashboard' => true,
			'type' => 'column',
			'order' => 10
		),
		'forum_topics' => array(
			'title' => __( 'Total Topic Count by Forum', 'sp-analytics' ),
			'active' => true,
			'full_row' => false,
			'dashboard' => true,
			'type' => 'column',
			'order' => 20
		),
		'top_posters_all'  => array(
			'title' => __( 'Top 10 Posters', 'sp-analytics' ),
			'active' => true,
			'full_row' => false,
			'dashboard' => true,
			'type' => 'column',
			'order' => 30
		),
		'top_posters' => array(
			'title' => __( 'Top 10 Posters excluding wp admins and moderators', 'sp-analytics' ),
			'active' => true,
			'full_row' => false,
			'dashboard' => true,
			'type' => 'column',
			'order' => 40
		),
		'group_users'  => array(
			'title' => __( 'Total users by user group', 'sp-analytics' ),
			'active' => true,
			'full_row' => false,
			'dashboard' => true,
			'type' => 'column',
			'order' => 50
		),
		
		'new_post' => array(
			'title' => __( 'New Post Count', 'sp-analytics' ),
			'active' => true,
			'full_row' => true,
			'dashboard' => true,
			'type' => 'column',
			'order' => 60,
			'date_chart' => true,
			'default_range' => '1_day'
		),
		'new_topic'  => array(
			'title' => __( 'New Topic Count', 'sp-analytics' ),
			'active' => true,
			'full_row' => true,
			'dashboard' => true,
			'type' => 'column',
			'order' => 70,
			'date_chart' => true,
			'default_range' => '1_day'
		)
	);
	
	
	if( sp_analytics_topic_status_plugin_active() ) {
		
		$charts = array_merge($charts, sp_analytics_topic_status_charts() );
	}
	
	
	if( sp_analytics_tags_plugin_active() ) {
		
		$charts = array_merge( $charts, sp_analytics_tags_charts() );
		
	}
	
	
	$charts = apply_filters( 'sp_analytics_charts', $charts );

	return $charts;
}


/**
 * Check if topic status plugin is active
 * 
 * @return boolean
 */
function sp_analytics_topic_status_plugin_active() {
	
	$active = false;
	
	if( SP()->plugin->is_active('topic-status/sp-topicstatus-plugin.php') ) {
		$active = true;
	}
	
	return $active;
}

/**
 * Check if tags plugin is active
 * 
 * @return boolean
 */
function sp_analytics_tags_plugin_active() {
	$active = false;
	
	if( SP()->plugin->is_active('tags/sp-tags-plugin.php') ) {
		$active = true;
	}
	
	return $active;
}

/**
 * Return charts relative to topic status plugin
 * 
 * @return array
 */
function sp_analytics_topic_status_charts() {
	
	$charts = array();
	
	$charts['new_topic_status_posts']  = array(
		'title' => __( 'New Post Count by Topic Status', 'sp-analytics' ),
		'active' => true,
		'full_row' => true,
		'dashboard' => true,
		'type' => 'column',
		'order' => 100,
		'date_chart' => true,
		'default_range' => '30_day'
	);

	$charts['total_topic_status_posts']  = array(
		'title' => __( 'Total Post Count By Topic Status', 'sp-analytics' ),
		'active' => true,
		'full_row' => true,
		'dashboard' => true,
		'type' => 'column',
		'order' => 150
	);

	$charts['new_topic_status_topics']  = array(
		'title' => __( 'New Topic Count by Topic Status', 'sp-analytics' ),
		'active' => true,
		'full_row' => true,
		'dashboard' => true,
		'type' => 'column',
		'order' => 200,
		'date_chart' => true,
		'default_range' => '30_day'
	);


	$charts['total_topic_status_topics']  = array(
		'title' => __( 'Total Topic Count by Topic Status', 'sp-analytics' ),
		'active' => true,
		'full_row' => true,
		'dashboard' => true,
		'type' => 'column',
		'order' => 250,
	);

	return $charts;
}

/**
 * Return charts relative to tags plugin
 * 
 * @return array
 */
function sp_analytics_tags_charts() {
	
	$charts = array();
	
	$charts['top_tags']  = array(
		'title' => __( 'Top 10 Tags', 'sp-analytics' ),
		'active' => true,
		'full_row' => true,
		'dashboard' => true,
		'type' => 'line',
		'order' => 1,
	);
	
	
	return $charts;
}


/**
 * Return chart options based on chart id
 * 
 * @param string $chart_id
 * 
 * @return array
 */
function sp_analytics_get_chart_options( $chart_id ) {

	$charts = sp_analytics_get_charts();

	$options = array();
	if( array_key_exists( $chart_id, $charts ) ) {
		$options = $charts[ $chart_id ];
	}

	return $options;
}

/**
 * Return complete chart options based on chart id
 * 
 * @param string $chart_id
 * 
 * @return array
 */
function sp_analytics_chart_options_data( $chart_id ) {

	$default_options = sp_analytics_get_chart_options( $chart_id );

	$option_key = 'chart_' . $chart_id;
	
	$chart_options = SP()->options->get( $option_key );

	if( !is_array( $chart_options ) || empty( $chart_options ) ) {
		return $default_options;
	}

	return wp_parse_args( $chart_options, $default_options );
}

/**
 * Return all active charts
 * 
 * @return array
 */
function sp_analytics_get_active_charts() {

	$all_charts = sp_analytics_get_charts();

	$active_charts = array();
	foreach( $all_charts as $chart_id => $chart_options ) {

		$chart_options = sp_analytics_chart_options_data( $chart_id );
		
		$chart = SP_Analytics_Chart::get( $chart_id, $chart_options );
		
		if( $chart->isActive() ) {
			$active_charts[ $chart_id ] = $chart;
		}

	}
	
	usort( $active_charts, 'sp_analytics_sort_charts' );
	
	return $active_charts;
}


/**
 * Sort charts value compare function
 * 
 * @param object $a
 * @param object $b
 * 
 * @return int
 */
function sp_analytics_sort_charts( $a, $b ) {
	
	if ( $a->getOrder() == $b->getOrder() ) {
		return 0;
	}
	
	return ( $a->getOrder() < $b->getOrder() ) ? -1 : 1;
}

/**
 * Print charts page
 * 
 * @return void
 */
function sp_analytics_main_view() {
	
	
	$charts = sp_analytics_get_active_charts();

	if( empty( $charts ) ) {
		return;
	}

	
	spa_paint_open_tab( __( 'Analytics', 'sp-keywords' ), $charts[0]->isFullRow() );
	
	$half_count = 0;
	$last_view = null;

	$index = 0;
	

	foreach ( $charts as $chart ) {

		
		$half_count = $chart->isFullRow() ? 0 : $half_count + 1;
		
		
		if( $index != 0 ) {
			
			if( 0 === $half_count ) {
			
				if( 'half' === $last_view ) {
					echo '</div>';
				}
				
				echo '<div class="sp-full-form">';
				
			} elseif( 1 === $half_count ) {
				
				echo '<div class="sp-half-form">';
				
			} elseif( 2 === $half_count ) {
				spa_paint_tab_right_cell();
			}

		}


		spa_paint_open_panel();
			spa_paint_open_fieldset( __( $chart->getTitle(), 'sp-subs'), false );
				$chart->render();
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		
		$last_view = $chart->isFullRow() ? 'full' : 'half';

		if( $chart->isFullRow() || !$chart->isFullRow() && $half_count === 2 ) {
			
			spa_paint_close_container();
			
			$half_count = 0;
		}

		$index++;

	}

	spa_paint_close_tab();
	
	
	
}


/**
 * 
 * @param string $file
 * @param string $tag
 * @param string $lang
 * 
 * @return string
 */
function sp_analytics_admin_help( $file, $tag, $lang ) {
	
	
	$tags = array(
		'[analytics-options]',
		'[analytics-axisX]',
		'[analytics-axisY]',
		'[analytics-data]'
	);
	
	if( in_array( $tag, $tags ) ) {
		
		$file = SP_ANALYTICS_ADMINDIR.'sp-analytics-admin-help.'.$lang;
	}
	
	
	//if ($tag == '[private-messaging]' || $tag == '[pm-addressing]' || $tag == '[pm-access]' || $tag == '[pm-removal]' || $tag == '[pm-stats]' || $tag == '[pm-adversaries]') $file = PMADMINDIR.'sp-pm-admin-help.'.$lang;
    return $file;
}



/**
 * Update date relative chart via ajax
 */
function sp_analytics_ajax_update_chart() {
	require_once SP_ANALYTICS_DIR.'ajax/sp-analytics-ajax-chart.php';
}

/**
 * Plugin install function
 */
function sp_analytics_install() {
    require_once SP_ANALYTICS_DIR.'sp-analytics-install.php';
    sp_analytics_do_install();
}

/**
 * Plugin deactivate function
 */
function sp_analytics_deactivate() {
    require_once SP_ANALYTICS_DIR.'sp-analytics-uninstall.php';
    sp_analytics_do_deactivate();
}

/**
 * Plugin uninstall function
 */
function sp_analytics_uninstall() {
    require_once SP_ANALYTICS_DIR.'sp-analytics-uninstall.php';
    sp_analytics_do_uninstall();
}

/**
 * Run once a plugin activated
 */
function sp_analytics_sp_activate() {
	require_once SP_ANALYTICS_DIR.'sp-analytics-install.php';
    sp_analytics_do_sp_activate();
}

/**
 * Run once a plugin deactivated
 */
function sp_analytics_sp_deactivate() {
	require_once SP_ANALYTICS_DIR.'sp-analytics-uninstall.php';
    sp_analytics_do_sp_deactivate();
}

/**
 * Run one a plugin uninstall
 * 
 * @param array $admins
 */
function sp_analytics_sp_uninstall( $admins ) {
	require_once SP_ANALYTICS_DIR.'sp-analytics-uninstall.php';
    sp_analytics_do_sp_uninstall( $admins );
}

/**
 * Check plugin upgrade
 */
function sp_analytics_upgrade_check() {
    require_once SP_ANALYTICS_DIR.'sp-analytics-upgrade.php';
    sp_analytics_do_upgrade_check();
}

/**
 * return plugin active buttons
 * 
 * @param string $actionlink
 * @param string $plugin
 * 
 * @return string
 */
function sp_analytics_uninstall_option($actionlink, $plugin) {
    require_once SP_ANALYTICS_DIR.'sp-analytics-uninstall.php';
    $actionlink = sp_analytics_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

/**
 * Remove charts as glossary items
 * 
 * @param array $charts
 */
function sp_analytics_remove_plugin_glossary_items( $charts ) {
	
	$pluginId = 'sp-analytics';
	
	foreach( $charts as $chart_id => $chart ) {
		
		$form_cb = "sp_analytics_admin_options_{$chart_id}_form";
		$save_cb = "sp_analytics_chart_options_save";
		
		$url  = sprintf( 'panel-plugins/spa-plugins.php&tab=plugin&admin=%s&save=%s&form=%s', $form_cb, $save_cb, 1 );
		
		$sql = "DELETE FROM ".SPADMINTASKS." WHERE plugin='$pluginId' AND url='$url'";
		SP()->DB->execute($sql);
	}
	
}

/**
 * Add charts as glossary items
 * 
 * @param array $charts
 */
function sp_analytics_add_charts_glossary_items( $charts ) {
	
	$pluginId = 'sp-analytics';
	
	$id = sp_add_glossary_keyword( 'Analytics', 'sp-analytics' );
	
	$inserts = array();
	
	foreach( $charts as $chart_id => $chart ) {
	
		$form_cb = "sp_analytics_admin_options_{$chart_id}_form";
		$save_cb = "sp_analytics_chart_options_save";
		
		$url  = sprintf( 'panel-plugins/spa-plugins.php&tab=plugin&admin=%s&save=%s&form=%s', $form_cb, $save_cb, 1 );

		$inserts[] = "($id, \"Setup {$chart['title']} options\",'$url','$pluginId')";
	}
	
	$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES " . implode( ', ' , $inserts );
	SP()->DB->execute($sql);
	
}

/**
 * Add glossary items once tags plugin installed
 */
function sp_analytics_tags_install() {
	$charts = sp_analytics_tags_charts();
	sp_analytics_add_charts_glossary_items( $charts );
}

/**
 * Remove glossary items once tags plugin uninstalled 
 */
function sp_analytics_tags_uninstall() {
	sp_analytics_tags_deactivate();
}

/**
 * Remove glossary items once tags plugin deactivated
 */
function sp_analytics_tags_deactivate() {
	$charts = sp_analytics_tags_charts();
	sp_analytics_remove_plugin_glossary_items( $charts );
}

/**
 * Add glossary items once topic status plugin installed
 */
function sp_analytics_topicstatus_install() {
	$charts = sp_analytics_topic_status_charts();
	sp_analytics_add_charts_glossary_items( $charts );
}

/**
 * Remove glossary items once topic status plugin uninstalled 
 */
function sp_analytics_topicstatus_uninstall() {
	sp_analytics_topicstatus_deactivate();
}

/**
 * Remove glossary items once topic status plugin deactivated
 */
function sp_analytics_topicstatus_deactivate() {
	$charts = sp_analytics_topic_status_charts();
	sp_analytics_remove_plugin_glossary_items( $charts );
}

/**
 * Add capability field while adding new admins
 * 
 * @param object $user
 */
function sp_analytics_admin_cap_form( $user ) {
	sp_analytics_do_admin_cap_form( $user );
}

/**
 * Add capability field while updating admin
 * 
 * @param object $user
 */
function sp_analytics_admin_cap_list( $user ) {
	sp_analytics_do_admin_cap_list( $user );
}

/**
 * Manage admin capability while adding new admins
 * 
 * @param string $newadmin
 * @param object $user
 * 
 * @return string
 */
function sp_analytics_admin_caps_new( $newadmin, $user ) {
	$newadmin = sp_analytics_do_admin_caps_new( $newadmin, $user );
	return $newadmin;
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
function sp_analytics_admin_caps_update( $still_admin, $remove_admin, $user ) {
	$still_admin = sp_analytics_do_admin_caps_update( $still_admin, $remove_admin, $user );
	return $still_admin;
}