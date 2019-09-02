<?php
/*
Simple:Press
Birthdays - general support routines
$LastChangedDate: 2018-11-04 17:10:36 -0600 (Sun, 04 Nov 2018) $
$Rev: 15807 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_birthdays_do_load_meta($list) {
    $list['sp_birthday'] = '';
    return $list;
}

function sp_birthdays_do_profile_form($out, $userid) {
	$out.= '<div class="spColumnSection spProfileLeftCol">';
	$out.= '<p class="spProfileLabel">'.__('Birthday', 'sp-birthdays').': </p>';
	$out.= '</div>';
	$out.= '<div class="spColumnSection spProfileSpacerCol"></div>';
	$out.= '<div class="spColumnSection spProfileRightCol">';
	$bVal = (empty(SP()->user->profileUser->sp_birthday)) ? '' : SP()->displayFilters->title(SP()->user->profileUser->sp_birthday);
	$out.= '<input type="text" class="spControl" name="birthday" id="birthday" value="'.$bVal.'" placeholder="YYYY-MM-DD" />';
    $out.= '</div>';

    $out.= '
    <script>
		(function(spj, $, undefined) {
			$(document).ready(function() {
				 $("#birthday").datepicker({
					 beforeShow: function(input, inst) {
						 $("#ui-datepicker-div").addClass("sp-birthdays-dp");
					 },
					 dateFormat: "yy-mm-dd",
					 maxDate: 0,
					 changeMonth: true,
					 changeYear: true,
					 yearRange: "-100:+0",
				 });
			 });
		}(window.spj = window.spj || {}, jQuery));
    </script>
    ';

    return $out;
}

function sp_birthdays_do_profile_save($message, $thisUser) {
    if (!empty($_POST['birthday'])) {
        $date = date_create($_POST['birthday']);
        if (!$date) {
			$message['type'] = 'error';
			$message['text'] = __('Invalid date entered for birthday', 'sp-birthdays');
        } else {
            update_user_meta($thisUser, 'sp_birthday', date_format($date, 'Y-m-d'));
        }
    } else {
        delete_user_meta($thisUser, 'sp_birthday');
    }
    return $message;
}

function sp_birthdays_do_header() {
	$css = SP()->theme->find_css(SPBDAYCSS, 'sp-birthdays.css', 'sp-birthdays.spcss');
    SP()->plugin->enqueue_style('sp-birthdays', $css);

	$css = SP()->theme->find_css(SPBDAYCSS, 'jquery-ui.css');
    SP()->plugin->enqueue_style('sp-birthdays-ui', $css);
}

function sp_birthdays_do_load_js($footer) {
	wp_enqueue_script('jquery-ui-datepicker', false, array('jquery', 'jquery-ui-core', 'jquery-ui-widget'), false, $footer);
}

function sp_birthdays_do_fill_cache() {
	$options = SP()->options->get('birthdays');
    $sql = '
        SELECT
            '.SPUSERMETA.'.user_id,
            FLOOR((UNIX_TIMESTAMP(CONCAT(((RIGHT(meta_value, 5) < RIGHT(CURRENT_DATE, 5)) + YEAR(CURRENT_DATE)), RIGHT(meta_value, 6))) - UNIX_TIMESTAMP(CURRENT_DATE)) / 86400) AS days,
            display_name
        FROM
            '.SPUSERMETA.'
        JOIN
            '.SPMEMBERS.' ON '.SPMEMBERS.'.user_id = '.SPUSERMETA.".user_id
        WHERE
            meta_key='sp_birthday' AND
            FLOOR((UNIX_TIMESTAMP(CONCAT(((RIGHT(meta_value, 5) < RIGHT(CURRENT_DATE, 5)) + YEAR(CURRENT_DATE)), RIGHT(meta_value, 6))) - UNIX_TIMESTAMP(CURRENT_DATE)) / 86400) <= ".$options['days'];
    $birthdays = SP()->DB->select($sql, 'set', ARRAY_A);

    SP()->options->update('upcoming_birthdays', $birthdays);

    do_action('sph_todays_birthday_list', $birthdays);
}

# personal data export
function sp_privacy_do_birthday_profile($exportItems, $spUserData, $groupID, $groupLabel) {
	if (!empty($spUserData->sp_birthday)) {
		$data = array();
		$data[] = array(
				'name'	=>	__('Birthday', 'sp-birthdays'),
				'value'	=>	$spUserData->sp_birthday
		);
		$exportItems[] = array(
			'group_id'		=> $groupID,
			'group_label' 	=> $groupLabel,
			'item_id' => 'Profile',
			'data' => $data,
		);
	}
	return $exportItems;
}

