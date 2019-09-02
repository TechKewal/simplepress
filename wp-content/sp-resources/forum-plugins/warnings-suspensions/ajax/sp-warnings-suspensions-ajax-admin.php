<?php
/*
Simple:Press
Warnings and Suspensions plugin ajax routine for management functions
$LastChangedDate: 2015-11-26 12:31:04 -0800 (Thu, 26 Nov 2015) $
$Rev: 13615 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

if (!SP()->auths->current_user_can('SPF Manage Warnings')) die();

sp_forum_ajax_support();

global $wpdb;

# autocomplete
if (isset($_GET['term'])) {
	$out = '[]';

    $table = SPMEMBERS;
    $fields = '*';
	$term = SP()->filters->str($_GET['term']);
	$where = "display_name LIKE '%".SP()->filters->esc_sql($wpdb->esc_like($term))."%' AND ".SPMEMBERS.".admin = 0";

	# get users that can be pm'ed'
	$query = new stdClass();
		$query->table      = $table;
		$query->fields 	  = $fields;
		$query->where 	  = $where;
		$query->limits     = 25;
	$query = apply_filters('sph_warnings_addresses_query', $query);
	$users = SP()->DB->select($query);

	if ($users) {
		$primary = '';
		$secondary = '';
		foreach ($users as $user) {
			$uname = SP()->displayFilters->name($user->display_name);
			$cUser = array ('id' => $user->user_id, 'value' => $uname);

			if (strcasecmp($term, substr($uname, 0, strlen($term))) == 0) {
				$primary.= json_encode($cUser).',';
			} else {
				$secondary.= json_encode($cUser).',';
			}
		}
		if ($primary != '' || $secondary != '') {
			if ($primary != '') $primary = trim($primary, ',').',';
			if ($secondary != '') $secondary = trim($secondary, ',');
			$out = '['.trim($primary.$secondary, ',').']';
		}
	}
	echo $out;
	die();
}

$action = (isset($_GET['targetaction'])) ? $_GET['targetaction'] : '';
if (empty($action)) die();

if (!sp_nonce('warnings-suspensions-admin')) die();

if ($action == 'delwarning') {
    $warning = SP()->filters->integer($_GET['wid']);
    if (empty($warning)) die();

    SP()->DB->execute("DELETE FROM ".SPWARNINGS." WHERE warn_id=$warning");
    die();
}

if ($action == 'newwarning') {
    sp_load_plugin_styles();

    $user = SP()->filters->integer($_GET['uid']);
    if (empty($user)) die();

    $msg = __('User warning added', 'sp-warnings-suspensions');

    $out = '
    <script>
		(function(spj, $, undefined) {
			$(document).ready(function() {
				$("#spAddWarn").ajaxForm(function(responseText, statusText, xhr, $form) {
					if (responseText == -1) {
						alert("'.__('Error Creating Warning.  Please check entries.', 'sp-warnings-suspensions').'");
					} else {
						$("#dialog").remove();
						spj.displayNotification(0, \''.esc_js($msg).'\');
					}
				});
				$("#sp-warn-date").datepicker({
					beforeShow: function(input, inst) {
						$("#ui-datepicker-div").addClass("sp-warnings-dp");
					},
					changeMonth: true,
					changeYear: true,
					dateFormat: "MM dd, yy",
					minDate: 0,
				});
			});
		}(window.spj = window.spj || {}, jQuery));
    </script>
    ';

    # output the warning creation form
    $url = wp_nonce_url(SPAJAXURL."warnings-suspensions-admin&targetaction=addwarning", 'warnings-suspensions-admin');
    $out.= '<div id="spMainContainer">';
    $out.= '<div id="sp_warn_create">';
    $out.= '<div id="sp_warn_body">';
    $out.= '<form method="post" action="'.$url.'" name="spAddWarn" id="spAddWarn">';
    $out.= '<input type="hidden" value="'.$user.'" name="sp_warning_user" />';
    $out.= '<div id="sp_warn_date">';
    $out.= '<label for "sp-warn-date" class="spLabel">'.__('Warning Expiration', 'sp-warnings-suspensions').'</label>';
    $out.= '<div class="sp_warn_input"><input id="sp-warn-date" class="spControl" type="text" value="" name="sp-warn-date"></div>';
    $out.= '</div>';
    $out.= '<div class="sp_warn_submit">';
    $out.= '<input id="sfsave" class="spSubmit" type="submit" value="'.esc_attr(__('Add Warning', 'sp-warnings-suspensions')).'" name="addwarning"> ';
    $out.= '<input id="sfcancel" class="spSubmit spCancelScript" type="button" value="'.esc_attr(__('Cancel', 'sp-warnings-suspensions')).'" name="cancel">';
    $out.= '</div>';
    $out.= '</form>';
    $out.= '</div>';
    $out.= '</div>';
    $out.= '<div>';

    echo $out;

    die();
}

if ($action == 'addwarning') {
    # validity checks
    $userid = SP()->filters->integer($_POST['sp_warning_user']);
    if (empty($userid) || empty($_POST['sp-warn-date'])) die(-1);

    $name = SP()->DB->table(SPMEMBERS, "user_id='$userid'", 'display_name');
    if (empty($name)) die(-1);

    $expire = date("Y-m-d H:i:s", strtotime(SP()->filters->str($_POST['sp-warn-date'])));

    # create db entry
	SP()->DB->execute("INSERT INTO ".SPWARNINGS." (warn_type, user_id, display_name, expiration) VALUES (".SPWARNWARNING.", $userid, '$name', '$expire')");

    die();
}

if ($action == 'delsuspension') {
    $userid = SP()->filters->integer($_GET['uid']);
    $suspension = SP()->filters->integer($_GET['wid']);
    if (empty($userid) || empty($suspension)) die();

    # remove suspension usergroup
    SP()->DB->execute('DELETE FROM '.SPMEMBERSHIPS." WHERE user_id=$userid");

    # get the saved memberships and restore
    $saved = SP()->DB->select("SELECT saved_memberships FROM ".SPWARNINGS." WHERE warn_id=$suspension", 'var');
    if ($saved) {
        $saved = unserialize($saved);
        foreach ($saved as $membership) {
            SP()->user->add_membership($membership['id'], $userid);
        }
    }

    SP()->DB->execute("DELETE FROM ".SPWARNINGS." WHERE warn_id=$suspension");
    die();
}

if ($action == 'newsuspension') {
    sp_load_plugin_styles();

    $user = SP()->filters->integer($_GET['uid']);
    if (empty($user)) die();

    $msg = __('User suspension added', 'sp-warnings-suspensions');

    $out = '
    <script>
		(function(spj, $, undefined) {
			$(document).ready(function() {
				$("#spAddSuspension").ajaxForm(function(responseText, statusText, xhr, $form) {
					if (responseText == -1) {
						alert("'.__('Error Creating Suspension.  Please check entries.', 'sp-warnings-suspensions').'");
					} else {
						$("#dialog").remove();
						spj.displayNotification(0, \''.esc_js($msg).'\');
					}
				});
				$("#sp-warn-date").datepicker({
					beforeShow: function(input, inst) {
						$("#ui-datepicker-div").addClass("sp-warnings-dp");
					},
					changeMonth: true,
					changeYear: true,
					dateFormat: "MM dd, yy",
					minDate: 0,
				});
			});
		}(window.spj = window.spj || {}, jQuery));
    </script>
    ';

    # output the suspension creation form
    $url = wp_nonce_url(SPAJAXURL."warnings-suspensions-admin&targetaction=addsuspension", 'warnings-suspensions-admin');
    $out.= '<div id="spMainContainer">';
    $out.= '<div id="sp_warn_create">';
    $out.= '<div id="sp_warn_body">';
    $out.= '<form method="post" action="'.$url.'" name="spAddSuspension" id="spAddSuspension">';
    $out.= '<input type="hidden" value="'.$user.'" name="sp_warning_user" />';
    $out.= '<div id="sp_warn_date">';
    $out.= '<label for "sp-warn-date" class="spLabel">'.__('Suspension Expiration', 'sp-warnings-suspensions').':</label>';
    $out.= '<div class="sp_warn_input"><input id="sp-warn-date" class="spControl" type="text" value="" name="sp-warn-date"></div>';
    $out.= '</div>';
    $out.= sp_warnings_suspensions_usergroup_select();
    $out.= '<div class="sp_warn_submit">';
    $out.= '<p><input id="sfsave" class="spSubmit" type="submit" value="'.esc_attr(__('Add Suspension', 'sp-warnings-suspensions')).'" name="addsuspension"> ';
    $out.= '<input id="sfcancel" class="spSubmit spCancelScript" type="button" value="'.esc_attr(__('Cancel', 'sp-warnings-suspensions')).'" name="cancel">';
    $out.= '<p></div>';
    $out.= '</form>';
    $out.= '</div>';
    $out.= '</div>';
	$out.= '</div>';

    echo $out;

    die();
}

if ($action == 'addsuspension') {
    # validity checks
    $userid = SP()->filters->integer($_POST['sp_warning_user']);
    if (empty($userid) || empty($_POST['sp-warn-date'])) die(-1);

    $name = SP()->DB->table(SPMEMBERS, "user_id='$userid'", 'display_name');
    if (empty($name)) die(-1);

    $ugid = SP()->filters->integer($_POST['usergroup_id']);

    $expire = date("Y-m-d H:i:s", strtotime(SP()->filters->str($_POST['sp-warn-date'])));

    $membership_list = array();
    $memberships = SP()->user->get_memberships($userid);
    if ($memberships) {
        foreach ($memberships as $index => $membership) {
            $membership_list[$index]['id'] = $membership['usergroup_id'];
            $membership_list[$index]['name'] = $membership['usergroup_name'];
        }
    }
    $membership_list = serialize($membership_list);

    require_once SP_PLUGIN_DIR.'/admin/library/spa-support.php';
    $ug = spa_get_usergroups_row($ugid);

    # create db entry
	SP()->DB->execute("INSERT INTO ".SPWARNINGS." (warn_type, user_id, display_name, expiration, usergroup, saved_memberships) VALUES (".SPWARNSUSPENSION.", $userid, '$name', '$expire', '$ug->usergroup_name', '$membership_list')");

    # remove current memberships
    SP()->DB->execute('DELETE FROM '.SPMEMBERSHIPS." WHERE user_id=$userid");

    # add new membership
    SP()->user->add_membership($ugid, $userid);

    sp_warnings_suspensions_notify_suspension($userid, $expire);

    die();
}

if ($action == 'delban') {
    $userid = SP()->filters->integer($_GET['uid']);
    $ban = SP()->filters->integer($_GET['wid']);
    if (empty($userid) || empty($ban)) die();

    # remove suspension usergroup
    SP()->DB->execute('DELETE FROM '.SPMEMBERSHIPS." WHERE user_id=$userid");

    # get the saved memberships and restore
    $saved = SP()->DB->select("SELECT saved_memberships FROM ".SPWARNINGS." WHERE warn_id=$ban", 'var');
    if ($saved) {
        $saved = unserialize($saved);
        foreach ($saved as $membership) {
            SP()->user->add_membership($membership['id'], $userid);
        }
    }

    SP()->DB->execute("DELETE FROM ".SPWARNINGS." WHERE warn_id=$ban");
    die();
}

if ($action == 'newban') {
    $user = SP()->filters->integer($_GET['uid']);
    if (empty($user)) die();

    $msg = __('User ban added', 'sp-warnings-suspensions');

    $out = '
    <script>
		(function(spj, $, undefined) {
			$(document).ready(function() {
				$("#spAddBan").ajaxForm(function(responseText, statusText, xhr, $form) {
					if (responseText == -1) {
						alert("'.__('Error Creating Ban.  Please check entries.', 'sp-warnings-suspensions').'");
					} else {
						$("#dialog").remove();
						spj.displayNotification(0, \''.esc_js($msg).'\');
					}
				});
			});
		}(window.spj = window.spj || {}, jQuery));
    </script>
    ';

    # output the ban creation form
    $url = wp_nonce_url(SPAJAXURL."warnings-suspensions-admin&targetaction=addban", 'warnings-suspensions-admin');
    $out.= '<div id="spMainContainer">';
    $out.= '<div id="sp_warn_create">';
    $out.= '<div id="sp_warn_body">';
    $out.= '<form method="post" action="'.$url.'" name="spAddBan" id="spAddBan">';
    $out.= '<input type="hidden" value="'.$user.'" name="sp_warning_user" />';
    $out.= sp_warnings_suspensions_usergroup_select();
    $out.= '<div class="sp_warn_submit">';
    $out.= '<p><input id="sfsave" class="spSubmit" type="submit" value="'.esc_attr(__('Add Ban', 'sp-warnings-suspensions')).'" name="addban"> ';
    $out.= '<input id="sfcancel" class="spSubmit spCancelScript" type="button" value="'.esc_attr(__('Cancel', 'sp-warnings-suspensions')).'" name="cancel">';
    $out.= '<p></div>';
    $out.= '</form>';
    $out.= '</div>';
    $out.= '</div>';
	$out.= '</div>';

    echo $out;

    die();
}

if ($action == 'addban') {
    # validity checks
    $userid = SP()->filters->integer($_POST['sp_warning_user']);
    if (empty($userid)) die(-1);

    $name = SP()->DB->table(SPMEMBERS, "user_id='$userid'", 'display_name');
    if (empty($name)) die(-1);

    $ugid = SP()->filters->integer($_POST['usergroup_id']);

    $membership_list = array();
    $memberships = SP()->user->get_memberships($userid);
    if ($memberships) {
        foreach ($memberships as $index => $membership) {
            $membership_list[$index]['id'] = $membership['usergroup_id'];
            $membership_list[$index]['name'] = $membership['usergroup_name'];
        }
    }
    $membership_list = serialize($membership_list);

    require_once SP_PLUGIN_DIR.'/admin/library/spa-support.php';
    $ug = spa_get_usergroups_row($ugid);

    # create db entry
	SP()->DB->execute("INSERT INTO ".SPWARNINGS." (warn_type, user_id, display_name, usergroup, saved_memberships) VALUES (".SPWARNBAN.", $userid, '$name', '$ug->usergroup_name', '$membership_list')");

    # remove current memberships
    SP()->DB->execute('DELETE FROM '.SPMEMBERSHIPS." WHERE user_id=$userid");

    # add new membership
    SP()->user->add_membership($ugid, $userid);

    sp_warnings_suspensions_notify_ban($userid);

    die();
}

die();
