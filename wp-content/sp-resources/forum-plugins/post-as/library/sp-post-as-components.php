<?php
/*
Simple:Press
Post As Plugin Support Routines
$LastChangedDate: 2017-12-31 09:40:24 -0600 (Sun, 31 Dec 2017) $
$Rev: 15619 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_post_as_do_form_options($content, $thisObject) {
	global $tab;

	$out = '';
	if (SP()->auths->get('post_as_user', $thisObject->forum_id)) {
    	add_action('wp_footer', 'spPostAsFooter');

		$out.= "<input type='checkbox' tabindex='".$tab++."' class='spControl' id='sfPostAs' name='sfPostAs' data-target='spPostAs' />\n";
		$out.= "<label class='spLabel spCheckbox' for='sfPostAs'>".__('Change post author', 'sp-post-as')."</label><br>\n";
        $out.= "<div id='spPostAs'>\n";
		$out.= "<label class='spLabel spPostAsLabel' for='sp_post_as'>".__('Post as', 'sp-post-as').": </label>\n";
		$out.= "<input type='text' id='sp_post_as' tabindex='".$tab++."' class='spControl spPostAs' name='sp_post_as' />\n";
		$out.= '<p class="spLabel">'.__("Start typing a member's name above and it will auto-complete", 'sp-post-as').'</p>';
        $out.= "</div>\n";
	}

	return $content.$out;
}

function spPostAsFooter() {
    define('SPPOSTASAUTOCOMP', 	SPAJAXURL.'post-as-manage&rand='.rand());
?>
    <script>
		(function(spj, $, undefined) {
			$(document).ready(function() {
				$('#sp_post_as').autocomplete({
					create: function(input, inst) {
						$(".ui-autocomplete").addClass("sp-post-as-ac");
					},
					source : '<?php echo SPPOSTASAUTOCOMP; ?>',
					disabled : false,
					delay : 200,
					minLength: 1,
				});
			});
		}(window.spj = window.spj || {}, jQuery));
    </script>
<?php
}

function sp_post_as_do_save_post($newpost) {
	if (!empty($_POST['sp_post_as']) && SP()->auths->get('post_as_user', $newpost['forumid'])) {
	    $display_name = SP()->saveFilters->name($_POST['sp_post_as']);
    	$userid = SP()->DB->table(SPMEMBERS, "display_name='$display_name'", 'user_id');
        if (!empty($userid)) {
        	$newpost['userid'] = $userid;
        	$newpost['postername'] = $display_name;
        	$newpost['posteremail'] = SP()->saveFilters->email(SP()->DB->table(SPUSERS, "ID=$userid", 'user_email'));
        } else {
            $newpost['error'] = __('Invalid author', 'sp-post-as').': '.$display_name;
        }
    }

    return $newpost;
}

function sp_post_as_do_load_js($footer) {
    $script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPPOSTASSCRIPT.'sp-post-as.js' : SPPOSTASSCRIPT.'sp-post-as.min.js';
	SP()->plugin->enqueue_script('sppostas', $script, array('jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-autocomplete'), false, $footer);
}

function sp_post_as_do_header() {
	$css = SP()->theme->find_css(SPPOSTASCSS, 'sp-post-as.css', 'sp-post-as.spcss');
    SP()->plugin->enqueue_style('sp-post-as', $css);
}
