<?php
/*
Simple:Press
Birthdays plugin template tag
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_do_ListBirthdays($args='', $headerLabel='', $todayLabel='', $upcomingLabel='') {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	$defs = array('tagId'          => 'spBirthdays',
                  'tagClass'   	   => 'spStatsSection',
                  'headerClass'    => 'spBirthdaysHeader',
                  'bodyClass' 	   => 'spBirthdaysBody',
                  'todayClass' 	   => 'spBirthdaysToday',
                  'upcomingClass'  => 'spBirthdaysUpcoming',
				  'iconClass'	   => 'spIcon',
				  'icon'	       => 'sp_BirthdayIcon.png',
                  'showToday'      => 1,
                  'showUpcoming'   => 1,
				  'echo'		   => 1,
				  'get'			   => 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_ListBirthdays_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId	           = esc_attr($tagId);
	$tagClass	       = esc_attr($tagClass);
	$headerClass       = esc_attr($headerClass);
	$bodyClass         = esc_attr($bodyClass);
	$todayClass	       = esc_attr($todayClass);
	$upcomingClass     = esc_attr($upcomingClass);
	$iconClass 	       = esc_attr($iconClass);
	$showToday         = (int) $showToday;
	$showUpcoming      = (int) $showUpcoming;
	$echo		       = (int) $echo;
	$get		       = (int) $get;

	$icon		       = (!empty($icon)) ?SP()->theme->paint_icon($iconClass, SPBDAYIMAGES, sanitize_file_name($icon)) : '';

	$headerLabel	   = SP()->displayFilters->title($headerLabel);
	$todayLabel	       = SP()->displayFilters->title($todayLabel);
	$upcomingLabel	   = SP()->displayFilters->title($upcomingLabel);

    $birthdays = SP()->options->get('upcoming_birthdays');
	if ($get) return $birthdays;

    $today = '';
    $upcoming = '';
    if ($birthdays) {
        $firstToday = true;
        $firstUpcoming = true;
        foreach ($birthdays as $birthday) {
            if ($birthday['days'] == 0) {
                if (!$firstToday) $today.= ', ';
                $today.= SP()->user->name_display($birthday['user_id'], $birthday['display_name']);
                $firstToday = false;
            } else {
                if (!$firstUpcoming) $upcoming.= ', ';
                $upcoming.= SP()->user->name_display($birthday['user_id'], $birthday['display_name']);
                $firstUpcoming = false;
            }
        }
    }

    if (empty($today)) $today = __('None', 'sp-birthdays');
    if (empty($upcoming)) $upcoming = __('None', 'sp-birthdays');

	$out = "<div id='$tagId' class='$tagClass'>\n";
    $out.= "<div class='$headerClass'>$headerLabel</div>";
    $out.= "<div class='$bodyClass'>";
    if (!empty($icon)) $out.= $icon;
    if ($showToday) $out.= "<div class='$todayClass'>$todayLabel <span>$today</span></div>";
    if ($showUpcoming) $out.= "<div class='$upcomingClass'>$upcomingLabel <span>$upcoming</span></div>";
    $out.= '</div>';
    $out.= '</div>';

	$out = apply_filters('sph_ListBirthdays', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}
