<?php
/*
Simple:Press
Buddypress Plugin new component loader
$Rev: 15725 $
*/

# Exit if accessed directly
if (!defined('ABSPATH')) exit;

class BP_Simple_Press_Component extends BP_Component {
	/**
	 * Start the simple press component creation process
	 */
	function __construct() {
		global $bp;

		parent::start(
			'forum',
			__('Simple Press', 'sp-buddypress'),
			SPBUDDYPRESSLIBDIR
		);

        $this->includes();

		$bp->active_components[$this->id] = '1';
	}

	/**
	 * Include files
	 */
	function includes($includes = array()) {
        require_once $this->path.'sp-buddypress-component.php';
        require_once $this->path.'sp-buddypress-notifications.php';
        require_once $this->path.'sp-buddypress-screens.php';
	}

	/**
	 * Setup globals
	 */
	function setup_globals($args = array()) {
		if (!defined('BP_FORUM_SLUG')) define( 'BP_FORUM_SLUG', $this->id );

		# All globals for simple press component
		$args = array(
			'slug'                  => BP_FORUM_SLUG,
			'has_directory'         => false,
			'notification_callback' => 'sp_buddypress_format_notifications',
		);

		parent::setup_globals($args);
	}

	function setup_nav($main_nav = array(), $sub_nav = array()) {
		if (is_user_logged_in()) {
		    global $bp;

			$bpdata = SP()->options->get('buddypress');
			if ($bpdata['integrateprofile']) {
				bp_core_new_subnav_item(array(
					'name'					=> __('Forum Profile', 'sp-buddypress'),
					'slug'					=> 'forum',
					'parent_slug'			=> $bp->profile->slug,
					'parent_url'			=> trailingslashit(bp_loggedin_user_domain().'profile'),
					'screen_function'		=> 'sp_buddypress_profile_screen',
					'position'				=> 40,
					'user_has_access'		=> bp_is_my_profile(), # Only the logged in user can access this on his/her profile
				));
			}

			SP()->user->get_current_user();

			if (SP()->plugin->is_active('subscriptions/sp-subscriptions-plugin.php') && $bpdata['integratesubs'] && SP()->auths->get('subscribe')) {
				$subs = SP()->options->get('subscriptions');
				if ($subs['forumsubs']) {
					bp_core_new_subnav_item(array(
						'name' 		      => __('Forum Subscriptions', 'sp-buddypress'),
						'slug' 		      => 'forum-subscriptions',
						'parent_slug'     => $bp->profile->slug,
						'parent_url' 	  => trailingslashit(bp_loggedin_user_domain().'profile'),
						'screen_function' => 'sp_buddypress_forum_subs_screen',
						'position' 	      => 50,
						'user_has_access' => bp_is_my_profile() # Only the logged in user can access this on his/her profile
					));
				}

				bp_core_new_subnav_item(array(
					'name' 		      => __('Topic Subscriptions', 'sp-buddypress'),
					'slug' 		      => 'topic-subscriptions',
					'parent_slug'     => $bp->profile->slug,
					'parent_url' 	  => trailingslashit(bp_loggedin_user_domain().'profile'),
					'screen_function' => 'sp_buddypress_topic_subs_screen',
					'position' 	      => 60,
					'user_has_access' => bp_is_my_profile() # Only the logged in user can access this on his/her profile
				));
			}

			if (SP()->plugin->is_active('watches/sp-watches-plugin.php') && $bpdata['integratewatches'] && SP()->auths->get('watch')) {
				bp_core_new_subnav_item(array(
					'name' 		      => __('Watches', 'sp-buddypress'),
					'slug' 		      => 'watches',
					'parent_slug'     => $bp->profile->slug,
					'parent_url' 	  => trailingslashit(bp_loggedin_user_domain().'profile'),
					'screen_function' => 'sp_buddypress_watches_screen',
					'position' 	      => 70,
					'user_has_access' => bp_is_my_profile() # Only the logged in user can access this on his/her profile
				));
			}
		}
	}

	/**
	 * Set up the Toolbar
	 */
	function setup_admin_bar($wp_admin_nav = array()) {
		global $bp;

       	$bpdata = SP()->options->get('buddypress');
        if (!$bpdata['uselinks']) return;

		# Add the Simple Press sub menu
		$wp_admin_nav = array();
		$wp_admin_nav[] = array(
			'parent' => $bp->my_account_menu_id,
			'id'     => 'my-account-'.$this->id,
			'title'  => __('Forum', 'sp-buddypress'),
			'href'   => SP()->spPermalinks->get_url()
		);

		if (is_user_logged_in()) {
       		SP()->user->get_current_user();

			# New Posts
            if ($bpdata['newlink']) {
    			$count = (!empty(SP()->user->thisUser->newposts)) ? count(SP()->user->thisUser->newposts['topics']) : 0;
    			if (!empty($count)) {
    				$title = sprintf(__('New Posts <span class="count">%s</span>', 'sp-buddypress'), number_format_i18n($count));
    			} else {
    				$title = __('New Posts', 'sp-buddypress');
    			}

    			$wp_admin_nav[] = array(
    				'parent' => 'my-account-'.$this->id,
    				'id'     => 'my-account-'.$this->id.'-new',
    				'title'  => $title,
    				'href'   => SP()->spPermalinks->get_url('newposts')
    			);
            }


            if (SP()->plugin->is_active('private-messaging/sp-pm-plugin.php')) {
            	if ($bpdata['inboxlink'] && sp_pm_get_auth('use_pm')) {
					require_once PMLIBDIR.'sp-pm-database.php';
					# PM Inbox
					$count = sp_pm_get_inbox_unread_count(SP()->user->thisUser->ID);
					if (!empty($count)) {
						$title = sprintf(__('Inbox New PM<span class="count">%s</span>', 'sp-buddypress'), number_format_i18n($count));
					} else {
						$title = __('Inbox', 'sp-buddypress');
					}

					$wp_admin_nav[] = array(
						'parent' => 'my-account-'.$this->id,
						'id'     => 'my-account-'.$this->id.'-pm',
						'title'  => $title,
						'href'   => SP()->spPermalinks->get_url('private-messaging/inbox')
					);
				}
            }

            if (SP()->plugin->is_active('subscriptions/sp-subscriptions-plugin.php') && $bpdata['subslink'] && SP()->auths->get('subscribe')) {
    			# Unread Subscriptions
        		$count = 0;
        		$list = SP()->user->thisUser->subscribe;
        		if (!empty($list)) {
        			foreach ($list as $topicid) {
        				if (sp_is_in_users_newposts($topicid)) $count++;
        			}
        		}
    			if (!empty($count)) {
    				$title = sprintf(__('Subscriptions <span class="count">%s</span>', 'sp-buddypress'), number_format_i18n($count));
    			} else {
    				$title = __('Subscriptions', 'sp-buddypress');
                }

    			$wp_admin_nav[] = array(
    				'parent' => 'my-account-'.$this->id,
    				'id'     => 'my-account-'.$this->id.'-subs',
    				'title'  => $title,
    				'href'   => SP()->spPermalinks->get_url('subscriptions')
    			);
            }

            if (SP()->plugin->is_active('watches/sp-watches-plugin.php') && $bpdata['watcheslink'] && SP()->auths->get('watch')) {
    			# Unread Watches
        		$count = 0;
        		$list = SP()->user->thisUser->watches;
        		if (!empty($list)) {
        			foreach ($list as $topicid) {
        				if (sp_is_in_users_newposts($topicid)) $count++;
        			}
        		}
    			if (!empty($count)) {
    				$title = sprintf(__('Watches <span class="count">%s</span>', 'sp-buddypress'), number_format_i18n($count));
    			} else {
    				$title = __('Watches', 'sp-buddypress');
                }

    			$wp_admin_nav[] = array(
    				'parent' => 'my-account-'.$this->id,
    				'id'     => 'my-account-'.$this->id.'-watches',
    				'title'  => $title,
    				'href'   => SP()->spPermalinks->get_url('watches')
    			);
            }

            if ($bpdata['profilelink']) {
    			$wp_admin_nav[] = array(
    				'parent' => 'my-account-'.$this->id,
    				'id'     => 'my-account-'.$this->id.'-profile',
    				'title'  => __('Profile', 'sp-buddypress'),
    				'href'   => SP()->spPermalinks->get_url('profile')
    			);
            }

            if ($bpdata['startedlink']) {
    			$wp_admin_nav[] = array(
    				'parent' => 'my-account-'.$this->id,
    				'id'     => 'my-account-'.$this->id.'-started',
    				'title'  => __('Topics Started', 'sp-buddypress'),
    				'href'   => add_query_arg(array("search"=>1, "new"=>1, "forum"=>"all", "value"=>SP()->user->thisUser->ID, "type"=>5), SP()->spPermalinks->get_url())
    			);
            }

            if ($bpdata['postedlink']) {
    			$wp_admin_nav[] = array(
    				'parent' => 'my-account-'.$this->id,
    				'id'     => 'my-account-'.$this->id.'-posted',
    				'title'  => __('Topics Posted In', 'sp-buddypress'),
    				'href'   => add_query_arg(array("search"=>1, "new"=>1, "forum"=>"all", "value"=>SP()->user->thisUser->ID, "type"=>4), SP()->spPermalinks->get_url())
    			);
            }
		}

		parent::setup_admin_bar($wp_admin_nav);
	}

	/**
	 * Setup the actions
	 */
	function setup_actions() {
        add_action('bp_xprofile_setup_admin_bar',   'sp_buddypress_setup_admin_bar');

		parent::setup_actions();
	}
}

function sp_buddypress_setup_admin_bar() {
	global $bp, $wp_admin_bar;

    $admin_menu = array();

	$admin_menu[] = array(
		'parent' => 'my-account-'.$bp->profile->id,
		'id'     => 'my-account-'.$bp->profile->id.'-forum',
		'title'  => __('Forum Options', 'sp-buddypress'),
		'href'   => trailingslashit(bp_loggedin_user_domain().$bp->profile->slug.'/forum')
	);

   	$bpdata = SP()->options->get('buddypress');
    if (SP()->plugin->is_active('subscriptions/sp-subscriptions-plugin.php') && $bpdata['integratesubs'] && SP()->auths->get('subscribe')) {
    	$subs = SP()->options->get('subscriptions');
        if ($subs['forumsubs']) {
        	$admin_menu[] = array(
        		'parent' => 'my-account-'.$bp->profile->id,
        		'id'     => 'my-account-'.$bp->profile->id.'-forum-subscriptions',
        		'title'  => __('Forum Subscriptions', 'sp-buddypress'),
        		'href'   => trailingslashit(bp_loggedin_user_domain().$bp->profile->slug.'/forum-subscriptions')
        	);
        }

    	$admin_menu[] = array(
    		'parent' => 'my-account-'.$bp->profile->id,
    		'id'     => 'my-account-'.$bp->profile->id.'-topic-subscriptions',
    		'title'  => __('Topic Subscriptions', 'sp-buddypress'),
    		'href'   => trailingslashit(bp_loggedin_user_domain().$bp->profile->slug.'/topic-subscriptions')
    	);
    }

    if (SP()->plugin->is_active('watches/sp-watches-plugin.php') && $bpdata['integratewatches'] && SP()->auths->get('watch')) {
    	$admin_menu[] = array(
    		'parent' => 'my-account-'.$bp->profile->id,
    		'id'     => 'my-account-'.$bp->profile->id.'-watches',
    		'title'  => __('Watches', 'sp-buddypress'),
    		'href'   => trailingslashit(bp_loggedin_user_domain().$bp->profile->slug.'/watches')
    	);
    }

	foreach($admin_menu as $menu ) {
        $wp_admin_bar->add_menu($menu);
    }
}

function sp_buddypress_do_setup_component() {
	global $bp;
	$bp->forum = new BP_Simple_Press_Component();
}
