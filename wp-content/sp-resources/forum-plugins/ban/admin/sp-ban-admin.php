<?php
/*
Simple:Press
Ban Plugin Admin Form
$LastChangedDate: 2018-08-19 12:58:30 -0500 (Sun, 19 Aug 2018) $
$Rev: 15711 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_ban_admin_form() {
    if (!SP()->auths->current_user_can('SPF Manage Users')) die();
?>
    <script>
		(function(spj, $, undefined) {
			spj.loadAjaxForm('spaddban', 'banpanel');
		}(window.spj = window.spj || {}, jQuery));
    </script>
<?php
	spa_paint_options_init();

    $ajaxURL = wp_nonce_url(SPAJAXURL.'plugins-loader&amp;saveform=plugin&amp;func=sp_ban_admin_save_bans', 'plugins-loader');
   	echo "<form action='$ajaxURL' method='post' name='spaddban' id='spaddban'>";
   	echo sp_create_nonce('forum-adminform_userplugin');

	spa_paint_open_tab(__('Users', 'sp-ban').' - '.__('Add Ban', 'sp-ban'));
    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Add IP Ban', 'sp-ban'), true, 'ban-ip');
            	$bans = implode("\n", SP()->options->get('banned_ips'));
                $submessage = __('One entry per line - examples 123.456.34.55, 188.45.66.*', 'sp-ban');
				spa_paint_textarea(__('Enter IP addres to ban (wildcards permitted)', 'sp-ban'), 'ip_addr', $bans, $submessage, 10);
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();

    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Add IP Range Ban', 'sp-ban'), true, 'ban-ip-range');
            	$bans = implode("\n", SP()->options->get('banned_ip_ranges'));
				$submessage = __('One entry per line - example 92.16.17.111-92.16.17.125', 'sp-ban');
				spa_paint_textarea(__('Enter IP address range (no wildcards)', 'sp-ban'), 'ip_addr_range', $bans, $submessage, 10);
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();

		spa_paint_tab_right_cell();

    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Add Hostname Ban', 'sp-ban'), true, 'ban-hostname');
            	$bans = implode("\n", SP()->options->get('banned_hostnames'));
				$submessage = __('One entry per line - examples *.cn, *.ru', 'sp-ban');
				spa_paint_textarea(__('Enter Hostname to ban (wildcards permitted)', 'sp-ban'), 'hostname', $bans, $submessage, 10);
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();

    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Add User Agent Ban', 'sp-ban'), true, 'ban-user-agent');
            	$bans = implode("\n", SP()->options->get('banned_agents'));
				$submessage = __('One entry per line - example googlebot*', 'sp-ban');
				spa_paint_textarea(__('Enter User Agent to ban (wildcards permitted)', 'sp-ban'), 'user_agent', $bans, $submessage, 10);
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();
		spa_paint_close_container();


    define('SPBANAUTOCOMP', htmlspecialchars_decode(wp_nonce_url(SPAJAXURL.'ban-manage&amp;rand='.rand(), 'ban-manage')));
?>
	<div class='sfform-submit-bar'>
		<input type='submit' class='button-primary' id='spBanAdd' name='spBanAdd' value='<?php esc_attr_e(__('Update Bans', 'sp-ban')); ?>' />
	</div>
<?php	spa_paint_close_tab(); ?>
    </form>
	<div class="sfform-panel-spacer"></div>

    <script>
		(function(spj, $, undefined) {
			$(document).ready(function() {
				$('#spbanmsg').ajaxForm({
					target: '#sfmsgspot',
					success: function() {
						$('#banpanel').click();
						$('#sfmsgspot').fadeIn();
						$('#sfmsgspot').fadeOut(6000);
					}
				});

				$('#sp_ban_user').autocomplete({
					create: function(input, inst) {
						$(".ui-autocomplete").addClass("sp-ban-ac");
					},
					source : '<?php echo SPBANAUTOCOMP; ?>',
					disabled : false,
					delay : 200,
					minLength: 1,
				});
			});
		}(window.spj = window.spj || {}, jQuery));
    </script>
<?php
    $ajaxURL = wp_nonce_url(SPAJAXURL.'plugins-loader&amp;saveform=plugin&amp;func=sp_ban_admin_save_msgs', 'plugins-loader');
   	echo "<form action='$ajaxURL' method='post' name='spbanmsg' id='spbanmsg'>";
   	echo sp_create_nonce('forum-adminform_userplugin');

  	$ban = SP()->options->get('ban');
	spa_paint_open_tab(__('Users', 'sp-ban').' - '.__('Banned Messages', 'sp-ban'));
    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('General Banned Message', 'sp-ban'), true, 'ban-general-message');
				spa_paint_textarea(__('Enter message to display to users banned by IP, hostname or user agent', 'sp-ban'), 'genmsg', $ban['general-message'], '', 10);
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();

    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('User Restricted Message', 'sp-ban'), true, 'ban-restriction-message');
				spa_paint_textarea(__('Enter notice to display to users banned by ID and moved to a different usergroup', 'sp-ban'), 'ugmsg', $ban['ug-message'], '', 10);
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();

		spa_paint_tab_right_cell();

    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('User Banned Message', 'sp-ban'), true, 'ban-user-message');
				spa_paint_textarea(__('Enter message to display to users banned by ID', 'sp-ban'), 'usermsg', $ban['user-message'], '', 10);
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();

    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('User Restored Message', 'sp-ban'), true, 'ban-expire-message');
				spa_paint_textarea(__('Enter notice to display to users when their ban is removed or expired', 'sp-ban'), 'restoremsg', $ban['restore-message'], '', 10);
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();
		spa_paint_close_container();
?>
	<div class='sfform-submit-bar'>
		<input type='submit' class='button-primary' id='spBanMsg' name='spBanMsg' value='<?php esc_attr_e(__('Update Ban Messages', 'sp-ban')); ?>' />
	</div>
<?php	spa_paint_close_tab(); ?>
    </form>
	<div class="sfform-panel-spacer"></div>

    <script>
		(function(spj, $, undefined) {
			spj.loadAjaxForm('spbanuser', 'banpanel');
		}(window.spj = window.spj || {}, jQuery));
    </script>
<?php
    $ajaxURL = wp_nonce_url(SPAJAXURL.'plugins-loader&amp;saveform=plugin&amp;func=sp_ban_admin_save_user', 'plugins-loader');
   	echo "<form action='$ajaxURL' method='post' name='spbanuser' id='spbanuser'>";
   	echo sp_create_nonce('forum-adminform_userplugin');

	spa_paint_open_tab(__('Users', 'sp-ban').' - '.__('User Bans', 'sp-ban'));
    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Add User Ban', 'sp-ban'), true, 'ban-user');
?>
                <table class="form-table"><tr>
                <td class="sflabel" style="width:60%"><span class="sfalignleft"><?php echo __('Enter user to ban', 'sp-ban'); ?></span></td>
                <td><input class="sfpostcontrol" type="text" value="" id="sp_ban_user" name="sp_ban_user" /></td>
                </tr>
                <tr>
                <td class="sflabel" style="width:60%"><span class="sfalignleft"><?php echo __('Enter usergroup to move user to (optional)', 'sp-ban'); ?></span></td>
                <td>
                    <select style="width:145px" class='sfacontrol' name='usergroup_id'>
                    <option value="-1"><?php echo __('Do not move to usergroup', 'sp-ban'); ?></option>
<?php
                    $usergroups = spa_get_usergroups_all();
            		foreach ($usergroups as $usergroup) {
            			echo '<option value="'.$usergroup->usergroup_id.'">'.SP()->displayFilters->title($usergroup->usergroup_name).'</option>';
            		}
?>
                    </select>
                </td>
                </tr>
                <tr>
                <td class="sflabel" style="width:60%"><span class="sfalignleft"><?php echo __('Enter ban duration in hours (optional)', 'sp-ban'); ?></span></td>
                <td><input class="sfpostcontrol" type="text" value="" id="sp_ban_expire" name="sp_ban_expire" /></td>
                </tr></table>
<?php
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();

	   spa_paint_tab_right_cell();

    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Currently Banned Users', 'sp-ban'), true, 'current-banned');
                $banned = SP()->options->get('banned_users');
                if (!empty($banned)) {
                	    echo '<p><b>'.count($banned).' '.__('member(s) currently banned', 'sp-ban').'</b></p>';
?>
                        <table class="widefat fixed striped">
                            <thead>
                                <tr>
                                    <th style='text-align:center'><?php echo __('Member ID', 'sp-ban'); ?></th>
                                    <th><?php echo __('Member Name', 'sp-ban'); ?></th>
                                    <th style='text-align:center'><?php echo __('Expiration', 'sp-ban'); ?></th>
                                    <th style='text-align:center'><?php echo __('Manage', 'sp-ban'); ?></th>
                                </tr>
                            </thead>
<?php
                        $tz = get_option('timezone_string'); # set server time
                        if ($tz) date_default_timezone_set($tz);
                		foreach ($banned as $ban) {
?>
                            <tr id='sp-ban-<?php echo $ban['id']; ?>'>
                                <td style='text-align:center'><?php echo $ban['id']; ?></td>
                                <td><?php echo SP()->displayFilters->name($ban['name']); ?></td>
                                <td style='text-align:center'><?php if (!empty($ban['expire'])) echo date('F j, Y \a\t g:i a', $ban['expire']); ?></td>
                                <td style='text-align:center'>
                                    <?php
                                        $msg = esc_attr(__('Are you sure you want to remove this ban?'), 'sp-ban');
                                        $site = wp_nonce_url(SPAJAXURL.'ban-manage&amp;targetaction=remove&amp;id='.$ban['id'], 'ban-manage');
                                    ?>
                                    <a>
										<span class="spDeleteRow" data-msg="<?php echo $msg; ?>" data-url="<?php echo $site; ?>" data-target="sp-ban-<?php echo $ban['id']; ?>">
										<?php echo SP()->theme->paint_icon('', SPBANIMAGES, 'sp_DeletePost.png', __('Remove Ban', 'sp-ban')); ?>
										</span>
                                    </a>
                                </td>
                            </tr>
<?php
                		}
?>
                        </table>
<?php
            	} else {
            		echo '<p>'.__('No members are currently banned', 'sp-ban').'</p>';
            	}
   		spa_paint_close_fieldset();
    	spa_paint_close_panel();
		spa_paint_close_container();
?>
	<div class='sfform-submit-bar'>
		<input type='submit' class='button-primary' id='spBanUser' name='spBanUser' value='<?php esc_attr_e(__('Add User Ban', 'sp-ban')); ?>' />
	</div>
    <?php spa_paint_close_tab(); ?>
    </form>
<?php
}
