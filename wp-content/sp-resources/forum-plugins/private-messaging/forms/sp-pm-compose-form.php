<?php
/*
Simple:Press
PM plugin message composition form rendering
$LastChangedDate: 2017-11-12 12:02:13 -0600 (Sun, 12 Nov 2017) $
$Rev: 15580 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_render_compose_pm_form($addPmForm='') {
	extract($addPmForm, EXTR_SKIP);

	# sanitize labels
	$labelSelect			  = SP()->displayFilters->title($labelSelect);
	$labelTo				  = SP()->displayFilters->title($labelTo);
	$labelSelectHelp		  = SP()->displayFilters->title($labelSelectHelp);
	$labelPmAllUsers		  = esc_attr($labelPmAllUsers);
	$labelPmAllUsersHelp	  = SP()->displayFilters->title($labelPmAllUsersHelp);
	$labelPmUserGroupHelp	  = SP()->displayFilters->title($labelPmUserGroupHelp);
	$labelPmUserGroupSelect	  = SP()->displayFilters->title($labelPmUserGroupSelect);
	$labelPmBuddyList		  = SP()->displayFilters->title($labelPmBuddyList);
	$labelPmAddBuddy		  = esc_attr($labelPmAddBuddy);
	$labelTitle				  = SP()->displayFilters->title($labelTitle);
	$labelSendButton		  = esc_attr($labelSendButton);
	$labelCancelButton		  = esc_attr($labelCancelButton);
	$labelSmileysButton		  = esc_attr($labelSmileysButton);
	$labelSmileys			  = esc_attr($labelSmileys);
	$tipSubmitButton		  = esc_attr($tipSubmitButton);
	$tipCancelButton		  = esc_attr($tipCancelButton);
	$tipSmileysButton		  = esc_attr($tipSmileysButton);

	$iconMobileSmileys		= sanitize_file_name($iconMobileSmileys);
	$iconMobileCancel		= sanitize_file_name($iconMobileCancel);
	$iconMobileSubmit		= sanitize_file_name($iconMobileSubmit);

	$out = '';

	$out.= '<a id="spEditFormAnchor"></a>'."\n";

	# form showing or hide?
	if ($hide ? $hide = ' style="display:none;"' : $hide = '');

	# OUTER CONTAINER DIV
	$out.= '<div id="spPostForm"'.$hide.'>'."\n";

	# start the form
	$out.= "<form id='sendpm' class='$tagClass' action='".wp_nonce_url(SPAJAXURL."pm-post", "pm-post")."' method='post' name='addpost' onsubmit='return spj.pmValidateForm(this)'>";
	$out.= sp_create_nonce('forum-userform_addpm');

    $out.= '<div class="spEditor">';

	# use inputs to pass data to js
	$sppm = SP()->options->get('pm');

	$out = apply_filters('sph_pm_recipients_top', $out, $sppm, $addPmForm);

	# dont limit # of recipients for admins
	if (SP()->user->thisUser->admin) $sppm['maxrecipients'] = 0;
	$out.= "<input type='hidden' name='pmmax' id='pmmax' value='".$sppm['maxrecipients']."' />";

	# set up pm types
	$cc = $bcc = 0;
	if ($sppm['cc'] && !$sppm['limitedsend']) $cc = 1;
	if ($sppm['bcc'] && !$sppm['limitedsend']) $bcc = 1;
	$out.= "<input type='hidden' name='pmcc' id='pmcc' value='$cc' />";
	$out.= "<input type='hidden' name='pmbcc' id='pmbcc' value='$bcc' />";

	# set up some pm data
	$out.= '<input type="hidden" name="threadid" id="threadid" value="0" />';
	$out.= '<input type="hidden" name="pmcount" id="pmcount" value="0" />';
	$out.= '<input type="hidden" id="uid" value="1" />';
	$out.= '<input type="hidden" name="pmaction" id="pmaction" value="savepm" />';
	$out.= "<input type='hidden' name='pmuser' id='pmuser' value='".SP()->user->thisUser->ID."' />";
	$out.= "<input type='hidden' name='pmlimited' id='pmlimited' value='".$sppm['limitedsend']."' />";
	$out.= '<input type="hidden" name="pmall" id="pmall" value="0" />';

	# Start of recipient selection form display
	$out.= "<fieldset id='$controlFieldset'>";

		# SELECT RECIPIENTS & RECIPIENT LIST --------------------------------

		$out.= '<div class="spEditorSection">';
			if (!$sppm['limitedsend'] || SP()->user->thisUser->admin) {

				# admins can email all users or specific usergroups
				$pmSpecial = apply_filters('sph_pm_email_special', SP()->user->thisUser->admin);
				if ($pmSpecial) {

					# SEND TO ALL
					# allow admins to pm all users
					$out.= "<div class='spPmAllUsers spPm3Col'>";
						$out.= "<input type='button' class='$controlInput spPMAllUsers' id='sfpmall' value='$labelPmAllUsers' /><br />";
						$out.= "<span class='spLabelSmall'>$labelPmAllUsersHelp</span>";
					$out.= "</div>";

					# SEND TO USERGROUP
					# allow admins to pm specific usergroup
					$out.= "<div class='spPmUsergroup spPm3Col'>\n";
						$out.= "<select class='$controlInput' name='sp_pm_usergroup_select' id='sp_pm_usergroup_select'>\n";
						$out.= "<option value='-1'>$labelPmUserGroupSelect</option>\n";
						$usergroups = SP()->DB->table(SPUSERGROUPS);
						foreach ($usergroups as $usergroup) {
							$out.= "<option value='$usergroup->usergroup_id'>".SP()->displayFilters->title($usergroup->usergroup_name)."</option>\n";
						}
						$out.= "</select><br />\n";
						$out.= "<span class='spLabelSmall'>$labelPmUserGroupHelp</span>\n";
					$out.= "</div>\n";
				}

				# SEND TO A BUDDY
				$users = sp_pm_get_buddies();
				$out.= "<div class='spPmBuddies spPm3Col'>\n";
					$out.= "<div id='pmbuddies'>\n";
						$out.= "<select class='$controlInput spPMComposeSendToBuddy' name='pmbudlist' id='pmbudlist' data-target='pmbudlist'>\n";
						$out.= "<option value='-1'>$labelPmBuddyList</option>\n";
						$out.= sp_pm_create_user_select($users);
						$out.= "</select>\n";
					$out.= "</div>\n";
				$out.= "</div>\n";

				$out.= sp_InsertBreak('echo=false&spacer=10px');
				$out.= '<hr />';
			}

			$out = apply_filters('sph_pm_editor_title_top', $out, $sppm, $addPmForm);

			# SELECT MEMBERS ONE BY ONE
			$out.= '<div class="spPm2Col spLeft">';
				if (!$sppm['limitedsend'] || SP()->user->thisUser->admin) {
					$out.= "<div class='spEditorTitle'>$labelSelect<br />";
						$out.= "<span class='spLabelSmall'>$labelSelectHelp</span><br />";
						$out.= "<label for='pmusers' class='spEditorTitle'>$labelTo</label>";
						$out.= "<input type='text' id='pmusers' class='$controlInput spPmUsers' name='pmusers' />";
					$out.= "</div>";
				}
			$out.= '</div>';

			# RECIPIENT LIST
			$out.= '<div class="spPm1Col spRight">';
				$out.= '<div id="spPmComposeNameList" class="spPmComposeNameList">';
					if (!$sppm['limitedsend'] || SP()->user->thisUser->admin) {
						if ($sppm['maxrecipients'] > 0) $out.= '<span class="spLabelSmall spPmMaxRecipients">'.__('Maximum of', 'sp-pm').' '.$sppm['maxrecipients'].' '.__('Recipients Allowed', 'sp-pm').'</span>';
						$site = wp_nonce_url(SPAJAXURL.'pm-manage', 'pm-manage');
						$out.= '<input type="hidden" name="pmsite" id="pmsite" value="'.$site.'" />';
					}
				$out.= '</div>';

				$out.= "<div class='spPmAddToBuddies'><input type='button' class='$controlSubmit spPMAddAllToBuddies' name='addbuddy' id='addbuddies' style='display:none;' value='$labelPmAddBuddy' /></div>";

			$out.= '</div>';

			$out.= sp_InsertBreak('echo=0');

			# SUBJECT
			$out.= "<div class='spEditorTitle'>$labelTitle<br />";
				$out.= "<input type='text' class='$controlInput' maxlength='180' name='pmtitle' id='pmtitle' value='' />";
				$out.= '<input type="hidden" name="pmtoidlist" id="pmtoidlist" value="" />';
				$out = apply_filters('sph_pm_editor_title_bottom', $out, $sppm, $addPmForm);
			$out.= '</div>';

		$out.= '</div>';

	$out.= '</fieldset>';

	$out = apply_filters('sph_pm_editor_top', $out, $sppm, $addPmForm);

	# EDITOR & TOOLS

	$out.= '<div id="spEditorContent">';
	$out.= sp_setup_editor(5);
	$out.= '</div>';

	$out = apply_filters('sph_pm_editor_footer_top', $out, $sppm, $addPmForm);

	add_filter('sph_pm_editor_toolbar_submit',		 'sp_pm_editor_submit_buttons', 1, 2);
	add_filter('sph_pm_editor_toolbar_buttons',		 'sp_pm_editor_default_buttons', 1, 2);
	add_filter('sph_pm_editor_toolbar',				 'sp_pm_editor_smileys_options', 1, 3);

	$toolbarRight = apply_filters('sph_pm_editor_toolbar_submit', '', $addPmForm);
	$toolbarLeft = apply_filters('sph_pm_editor_toolbar_buttons', '', $addPmForm);

	if (!empty($toolbarRight) || !empty($toolbarLeft)) {
		# Submit section
		$tout = '';
		$tout.= '<div class="spEditorSection spEditorToolbar">';
		$tout.= $toolbarRight;

	   # toolbar for plugins to add buttons
		$tout.= $toolbarLeft;
		$out.= apply_filters('sph_pm_editor_toolbar', $tout, $sppm, $addPmForm);
		$out.= '<div style="clear:both"></div>';
		$out.= '</div>'."\n";
	}

	$out = apply_filters('sph_pm_editor_bottom', $out, $sppm, $addPmForm);

	$out.= '</div>';
	$out.= '</form>';
	$out.= '</div>';

	$out = apply_filters('sph_pm_editor_beneath', $out, $sppm, $addPmForm);
	return $out;
}

function sp_pm_editor_submit_buttons($out, $addPmForm) {
	$sppm = SP()->options->get('pm');

	extract($addPmForm, EXTR_SKIP);
	$cOrder = (isset($controlOrder)) ? explode('|', $controlOrder) : array('cancel', 'save');

	$out.= "<div class='spEditorSubmitButton spRight'>\n";

	# let plugins add stuff to editor controls
	$out = apply_filters('sph_pm_editor_controls', $out, $sppm, $addPmForm);

	foreach ($cOrder as $c) {
		switch ($c) {
			case 'save':
				if (SP()->core->device == 'mobile' && array_key_exists('iconMobileSubmit', $addPmForm) && !empty($addPmForm['iconMobileSubmit'])) {

					# display mobile icon
					$out.= "<button type='submit' style='background:transparent;' class='spIcon' name='newpost' id='sfsave' />";
					$out.= SP()->theme->paint_icon('spIcon', PMIMAGESMOB, $iconMobileSubmit, '');
					$out.= "</button>";

				} else {
					# display default button
					$out.= "<input type='submit' tabindex='106' class='$controlSubmit' title='$tipSubmitButton' name='newpost' id='sfsave' value='$labelSendButton' />\n";
				}
				break;

			case 'cancel':
				$msg = esc_attr(SP()->primitives->admin_text('Are you sure you want to cancel composing this PM?'));
				if (SP()->core->device == 'mobile' && array_key_exists('iconMobileCancel', $addPmForm) && !empty($addPmForm['iconMobileCancel'])) {

					# display mobile icon
					$out.= "<button type='button' style='background:transparent;' class='spIcon spCancelEditor' name='cancel' id='sfcancel' data-msg='$msg'>\n";
					$out.= SP()->theme->paint_icon('spIcon', SPTHEMEICONSURL, $iconMobileCancel, '');
					$out.= "</button>";

				} else {
					# display default button
					$out.= "<input type='button' tabindex='107' class='$controlSubmit spCancelEditor' title='$tipCancelButton' id='sfcancel' name='cancel' value='$labelCancelButton' data-msg='$msg' />\n";
				}
				break;

			default:
				break;
		}
	}

	$out.= '</div>'."\n";

	return $out;
}

function sp_pm_editor_default_buttons($out, $addPmForm) {
	extract($addPmForm, EXTR_SKIP);

	if (SP()->auths->get('can_use_smileys', '')) {
		if (SP()->core->device == 'mobile' && array_key_exists('iconMobileSmileys', $addPmForm) && !empty($addPmForm['iconMobileSmileys'])) {

			# display mobile icon
			$out.= "<button type='button' style='background:transparent;' class='spIcon spEditorBoxOpen' name='spSmileysButton' id='spSmileysButton' data-box='spSmileysBox'>\n";
			$out.= SP()->theme->paint_icon('spIcon', SPTHEMEICONSURL, $iconMobileSmileys, '');
			$out.= "</button>";

		} else {
			# display default button
			$out.= "<input type='button' class='spSubmit spLeft spEditorBoxOpen' title='$tipSmileysButton' id='spSmileysButton' value='$labelSmileysButton' data-box='spSmileysBox' />";
		}
	}

	return $out;
}

function sp_pm_editor_smileys_options($out, $sppm, $addPmForm) {
	extract($addPmForm, EXTR_SKIP);

	# Now start the displays
	if (SP()->auths->get('can_use_smileys', '')) {
		$out.= sp_InsertBreak('echo=0')."<div>\n";

		$smileysBox = apply_filters('sph_pm_smileys_display', '', $addPmForm);
		$smileysBox.= "<div id='spSmileysBox' class='spEditorSection spInlineSection'>\n";
		$smileysBox.= "<div class='spEditorHeading'>$labelSmileys\n";
		$smileysBox = apply_filters('sph_pm_smileys_header_add', $smileysBox, $addPmForm);
		$smileysBox.= '</div>';
		$smileysBox.= '<div class="spEditorSmileys">'."\n";
		$smileysBox.= sp_render_smileys();
		$smileysBox.= '</div>';
		$smileysBox = apply_filters('sph_pm_smileys_add', $smileysBox, $addPmForm);
		$smileysBox.= sp_InsertBreak('direction=both&spacer=6px&echo=0');
		$smileysBox.= '</div>'."\n";

		$out.= $smileysBox;
		$out.= sp_InsertBreak('echo=0');
		$out.= '</div>';
	}

	# are we allowing uploads?
	$pm = SP()->options->get('pm');
	if (SP()->plugin->is_active('plupload/sp-plupload-plugin.php') && $pm['uploads']) {
		require_once SPPLUPLIBDIR.'sp-plupload-components.php';
		require_once SPPLUPLIBDIR.'sp-plupload-forms.php';
		$out = sp_plupload_do_uploader_form($out, '');
	}

	return $out;
}
