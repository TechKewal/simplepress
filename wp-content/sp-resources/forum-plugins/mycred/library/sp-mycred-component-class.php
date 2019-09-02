<?php
/*
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# Register MyCred Hook
add_filter('mycred_setup_hooks', 'sp_register_extensions_hook' );
function sp_register_extensions_hook($installed) {
	$installed['hook_simplepress_extensions'] = array(
		'title'       => __('Simple:Press Extensions', 'sp-mycred'),
		'description' => __('Award/deduct points for various Simple:Press actions', 'sp-mycred'),
		'callback'    => array('SP_myCred_extensions')
	);
	return $installed;
}

# myCRED SP extensions hook class
if(!class_exists('SP_myCred_extensions') && class_exists('myCRED_Hook')) {

	class SP_myCred_extensions extends myCRED_Hook {
		# Constructor
		function __construct($hook_prefs) {
			parent::__construct(array(
				'id'       => 'hook_simplepress_extensions',
				'defaults' => array()
			), $hook_prefs);
			$this->defaults = apply_filters('add_sp_mycred_extension', $this->defaults);
		}

		public function preferences() {
			do_action('prefs_sp_mycred_extension', $this);
			if (empty($this->defaults)) {
				echo '<h3>'.__('No supported Simple:Press plugins currently active', 'sp-mycred').'</h3>';
			}
		}

		public function sanitise_preferences( $data ) {
			$new_data = $data;

			# Apply defaults if any field is left empty
			if (!empty($new_data)) {
				foreach ($new_data as $key => $this_data) {
					$new_data[$key]['creds'] = ( !empty( $this_data['creds'] ) ) ? $this_data['creds'] : $this->defaults[$key]['creds'];
					$new_data[$key]['log'] = ( !empty( $this_data['log'] ) ) ? sanitize_text_field( $this_data['log'] ) : $this->defaults[$key]['log'];
				}
			}

			$new_data = apply_filters('sanitise_sp_mycred_extension', $new_data, $data, $this);

			return $new_data;
		}

		public function run() {
		}
	}
}

# Support Functions
function sp_mycred_process_points($add, $myCredItem, $userid, $message) {
	$mc = sp_mycred_get_settings($myCredItem);
	if(empty($mc)) return;
	# check we have what we need
	if (!defined( 'myCRED_VERSION')) {
		define('myCRED_VERSION', '1.3.3.2');
		require_once WP_PLUGIN_DIR . '/mycred/includes/mycred-functions.php';
	}
	if (mycred_exclude_user($userid) == false) {
		if ($add) {
			mycred_add($myCredItem, $userid, $mc['creds'], $mc['log'], '', $message);
		} else {
			mycred_subtract($myCredItem, $userid, $mc['creds'], $mc['log'], '', $message);
		}
	}
}

function sp_mycred_get_settings($myCredItem) {
	$s = array();
	$mc = get_option('mycred_pref_hooks');
	if (in_array('hook_simplepress_extensions', $mc['active'])) {
		$s = $mc['hook_prefs']['hook_simplepress_extensions'][$myCredItem];
	}
	return $s;
}
