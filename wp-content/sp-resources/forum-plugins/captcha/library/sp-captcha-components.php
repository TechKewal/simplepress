<?php
/*
Simple:Press
Captcha Plugin Admin Options Save Routine
$LastChangedDate: 2017-12-31 09:40:24 -0600 (Sun, 31 Dec 2017) $
$Rev: 15619 $
*/

function sp_captcha_do_deactivate() {
    SP()->auths->deactivate('bypass_captcha');
	# remove glossary entries
	sp_remove_glossary_plugin('sp-captcha');
}

function sp_captcha_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'captcha/sp-captcha-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-cap')."'>".__('Uninstall', 'sp-cap').'</a>';
        $url = SPADMINCOMPONENTS.'&amp;tab=login';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-cap')."'>".__('Options', 'sp-cap').'</a>';
    }
	return $actionlink;
}

function sp_captcha_do_login_load_js($footer) {
    $script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPCAPSCRIPT.'jquery.captcha.js' : SPCAPSCRIPT.'jquery.captcha.min.js';
    wp_enqueue_script('captcha', $script, array('jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-mouse', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-touch-punch'), false, false);
	if(isset($_GET['action']) && $_GET['action'] == 'register') {
		$css = SP()->theme->find_css(SPCAPCSS, 'sp-captcha.css');
		echo "<link rel='stylesheet' href='$css' />\n";
	}
}

function sp_captcha_do_load_js($footer) {
    $script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPCAPSCRIPT.'jquery.captcha.js' : SPCAPSCRIPT.'jquery.captcha.min.js';
    SP()->plugin->enqueue_script('captcha', $script, array('jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-mouse', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-touch-punch'), false, $footer);
}

function sp_captcha_do_registration_form($form='registerform', $errors='') {
	if (!empty($errors) && $errmsg = $errors->get_error_message('incorrect_captcha')) echo '<p class="error">'.$errmsg.'</p>';
    $text = esc_js(__('Verify that you are a human.', 'sp-cap')).'<br />'.esc_js(__('Drag the', 'sp-cap')).' <span>scissors</span> '.esc_js(__('into the circle.', 'sp-cap'));
?>
    <div class="ajax-fc-container"></div>
    <script>
		(function(spj, $, undefined) {
			$(document).ready(function() {
				$(".ajax-fc-container").captcha({
					borderColor: "#ffffff",
					formId: "<?php echo $form; ?>",
					items: Array("<?php echo esc_js(__('pencil', 'sp-cap')); ?>", "<?php echo esc_js(__('scissors', 'sp-cap')); ?>", "<?php echo esc_js(__('clock', 'sp-cap')); ?>", "<?php echo esc_js(__('heart', 'sp-cap')); ?>", "<?php echo __('note', 'sp-cap'); ?>"),
					style: '',
					captchaDir: "<?php echo SPCAPIMAGES; ?>",
					url: "<?php echo SPCAPLIBURL; ?>sp-captcha.php",
					text: "<?php echo $text; ?>",
				});
			});
		}(window.spj = window.spj || {}, jQuery));
    </script>
<?php
}

function sp_captcha_do_check_registration($errors, $sanitized_user_login, $user_email) {
    # dont show on multisite network add user panel
    if (isset($_REQUEST['add-user'])) return $errors;

	if (!session_id()) session_start();
	if (isset($_POST['postvalue']) && $_POST['postvalue'] == $_SESSION['postvalue']) {
		unset($_SESSION['postvalue']);
	} else {
		$errors->add('incorrect_captcha', __('<strong>ERROR</strong>: You must drag the proper image to the circle as verification', 'sp-cap'));
    }

	return $errors;
}

function sp_captcha_do_topic_form($out, $thisForum, $args) {
    if (!SP()->auths->get('bypass_captcha', $thisForum->forum_id)) {
       $text = esc_js(__('Verify that you are a human.', 'sp-cap')).'<br />'.esc_js(__('Drag the', 'sp-cap')).' <span>scissors</span> '.esc_js(__('into the circle.', 'sp-cap'));
        $out.= '
            <div class="ajax-fc-container"></div>
            <script>
				(function(spj, $, undefined) {
					$(document).ready(function() {
						$(".ajax-fc-container").captcha({
							borderColor: "#E5E5E5",
							formId: "addtopic",
							items: Array("'.esc_js(__('pencil', 'sp-cap')).'", "'.esc_js(__('scissors', 'sp-cap')).'", "'.esc_js(__('clock', 'sp-cap')).'", "'.esc_js(__('heart', 'sp-cap')).'", "'.esc_js(__('note', 'sp-cap')).'"),
							style: "",
							captchaDir: "'.SPCAPIMAGES.'",
							url: "'.SPCAPLIBURL.'sp-captcha.php",
							text: "'.$text.'",
						});
					});
				}(window.spj = window.spj || {}, jQuery));
            </script>
        ';
    }

    return $out;
}

function sp_captcha_do_topic_button_text($text, $args) {
  	extract($args, EXTR_SKIP); # get the topic form args
    return $labelPostButtonMath;
}

function sp_captcha_do_topic_button_enable($enable, $args) {
    return 'disabled="disabled"';
}

function sp_captcha_do_post_form($out, $thisTopic, $args) {
	if (!SP()->auths->get('bypass_captcha', $thisTopic->forum_id)) {
        $text = esc_js(__('Verify that you are a human.', 'sp-cap')).'<br />'.esc_js(__('Drag the', 'sp-cap')).' <span>scissors</span> '.esc_js(__('into the circle.', 'sp-cap'));
        $out.= '
            <div class="ajax-fc-container"></div>
            <script>
				(function(spj, $, undefined) {
					$(document).ready(function() {
						 $(".ajax-fc-container").captcha({
							 borderColor: "#E5E5E5",
							 formId: "addpost",
							 items: Array("'.esc_js(__('pencil', 'sp-cap')).'", "'.esc_js(__('scissors', 'sp-cap')).'", "'.esc_js(__('clock', 'sp-cap')).'", "'.esc_js(__('heart', 'sp-cap')).'", "'.esc_js(__('note', 'sp-cap')).'"),
							 style: "",
							 captchaDir: "'.SPCAPIMAGES.'",
							 url: "'.SPCAPLIBURL.'sp-captcha.php",
							 text: "'.$text.'",
						 });
					 });
 				}(window.spj = window.spj || {}, jQuery));
           </script>
        ';
	}
    return $out;
}

function sp_captcha_do_post_button_text($text, $args) {
  	extract($args, EXTR_SKIP); # get the post form args
    return $labelPostButtonMath;
}

function sp_captcha_do_post_button_enable($enable, $args) {
    return 'disabled="disabled"';
}

function sp_captcha_do_check_captcha($abort, $postVars) {
	if (!SP()->auths->get('bypass_captcha', $postVars['forumid'])) {
		if(isset($postVars['postvalue'])) {
			return $abort;
		} else {
			return true;
		}
	}
}

function sp_captcha_do_check_post($newpost) {
	if (!SP()->auths->get('bypass_captcha', $newpost['forumid'])) {
    	if (!session_id()) session_start();
    	if (isset($_POST['postvalue']) && $_POST['postvalue'] == $_SESSION['postvalue']) {
    		unset($_SESSION['postvalue']);
    	} else {
    		$newpost['error'] = __('Post cannot be saved - captcha not properly completed', 'sp-cap');
        }
    }
	return $newpost;
}
