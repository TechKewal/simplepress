<?php
/*
Simple:Press
Search By User library functions
$LastChangedDate: 2018-10-17 15:17:49 -0500 (Wed, 17 Oct 2018) $
$Rev: 15756 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# Add the user search checkbox to the search form
function sp_search_by_user_do_search_form($out) {
	$out.= '<hr /><input type="checkbox" id="spCheckUser" name="usersearchoption" value="0"'.(!empty(SP()->rewrites->pageData['usersearch']) ? ' checked="checked"' : '').' data-target="spHiddenUser" /><label class="spLabel spCheckBox" for="spCheckUser">'.__('Search forum by user', 'sp-search-by-user').'</label><br>';
	$out.= '<div'.(empty(SP()->rewrites->pageData['usersearch']) ? ' id="spHiddenUser"' : '').' class="spUserSearch">';
	$out.= "<input type='text' id='sp_search_user' class='spControl spSearchUser' name='sp_search_user' value='".(!empty(SP()->rewrites->pageData['usersearch']) ? urldecode(SP()->filters->str(SP()->rewrites->pageData['usersearch'])) : '')."' />";
	$out.= '<p class="spLabel">'.__("Start typing a member's name above and it will auto-complete", 'sp-search-by-user').'</p>';
	$out.= '</div>';

   	add_action('wp_footer', 'spSearchByUserFooter');
	return $out;
}

function spSearchByUserFooter() {
    define('SPSEARCHUSERAUTOCOMP', 	SPAJAXURL.'search-by-user-manage&rand='.rand());
?>
    <script>
		(function(spj, $, undefined) {
			$(document).ready(function() {
				$('#sp_search_user').autocomplete({
					create: function(input, inst) {
						$(".ui-autocomplete").addClass("sp-user-search-ac");
					},
					source : '<?php echo SPSEARCHUSERAUTOCOMP; ?>',
					disabled : false,
					delay : 200,
					minLength: 1,
				});
			});
		}(window.spj = window.spj || {}, jQuery));
    </script>
<?php
}

function sp_search_by_user_do_load_js($footer) {
    $script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPSEARCHUSERSCRIPT.'sp-search-by-user.js' : SPSEARCHUSERSCRIPT.'sp-search-by-user.min.js';
	SP()->plugin->enqueue_script('spsbu', $script, array('jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-autocomplete'), false, $footer);
}

function sp_search_by_user_do_header() {
	$css = SP()->theme->find_css(SPSEARCHUSERCSS, 'sp-search-by-user.css', 'sp-search-by-user.spcss');
    SP()->plugin->enqueue_style('sp-search-by-user', $css);
}

function sp_search_by_user_do_prepare_url($params) {
	if (isset($_REQUEST['usersearchoption']) && !empty($_POST['sp_search_user'])) {
        $name = urlencode($_POST['sp_search_user']);
        $params['user'] = $name;
    }
	return $params;
}

function sp_search_by_user_do_add_search_param($params) {
	if (isset($_GET['user'])) $params['user'] = SP()->filters->integer($_GET['user']);
	return $params;
}

function sp_search_by_user_do_add_page_data($data) {
	if (isset($_GET['user'])) $data['usersearch'] = urldecode(SP()->filters->str($_GET['user']));
	return $data;
}

function sp_search_by_user_do_query($query, $searchTerm, $searchType, $searchInclude) {
    if (isset(SP()->rewrites->pageData['usersearch'])) {
        if (($searchType == 1 || $searchType == 2 || $searchType == 3) &&
            ($searchInclude == 1 || $searchInclude == 2 || $searchInclude == 3)) {
        	$userid = SP()->DB->table(SPMEMBERS, 'display_name="'.SP()->rewrites->pageData['usersearch'].'"', 'user_id');
			if (!empty($userid)) {
				if ($searchInclude == 1 || $searchInclude == 3) {
					$query->where.= ' AND ('.SPPOSTS.".user_id = $userid)";
				} else {
					$query->where.= ' AND ('.SPTOPICS.".user_id = $userid)";
				}
			}
        }
    }

    return $query;
}

function sp_blog_search_by_user_do_query($query, $searchTerm, $searchType, $searchInclude) {
    if (isset(SP()->rewrites->pageData['usersearch'])) {
        if (($searchType == 1 || $searchType == 2 || $searchType == 3) &&
            ($searchInclude == 1 || $searchInclude == 2 || $searchInclude == 3)) {
        	$userid = SP()->DB->table(SPMEMBERS, 'display_name="'.SP()->rewrites->pageData['usersearch'].'"', 'user_id');
			if (!empty($userid)) {
				$query->where.= ' AND ('.SPWPPOSTS.".post_author = $userid)";
			}
        }
    }

    return $query;
}