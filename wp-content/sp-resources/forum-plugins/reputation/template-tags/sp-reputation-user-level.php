<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_reputation_user_level($args='', $user) {
	$defs = array('tagClass'	=> 'spReputationLevel',
				  'titleClass'	=> 'spReputationLevel',
			 	  'badgeClass'	=> 'spReputationLevel',
				  'repClass'	=> 'spUserRep',
				  'showTitle'	=> 0,
				  'showBadge'	=> 1,
				  'showRep' 	=> 0,
                  'stack'       => 1,
				  'echo'		=> 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_ReputationLevel_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagClass	= esc_attr($tagClass);
	$titleClass	= esc_attr($titleClass);
	$badgeClass	= esc_attr($badgeClass);
	$repClass   = esc_attr($repClass);
	$showTitle	= (int) $showTitle;
	$showBadge	= (int) $showBadge;
	$showRep	= (int) $showRep;
	$stack	    = (int) $stack;
	$echo		= (int) $echo;

	if (!$showTitle && !$showBadge && !$showRep) return;

	# the forum rank and title based on specified options
	$out = '';
	$out.= "<div class='$tagClass'>";
	if (!empty($user->reputation_level)) {
    	if ($user->reputation_level->badge && $showBadge) {
            $rep = ($user->admin) ? '' :  " ($user->reputation)";
            $out.= "<img src='".$user->reputation_level->badge."' class='$badgeClass' alt='".esc_attr($user->reputation_level->name)."' title='".esc_attr($user->reputation_level->name).$rep."' />";
        }
    	if ($showTitle) {
    		if ($stack) $out.= "<br />";
    		$out.= "<span class='$titleClass'>".$user->reputation_level->name.'</span>';
    	}
    	if ($showRep && !$user->admin) {
    		if ($stack) $out.= "<br />";
    		$out.= "<span class='$repClass'>".$user->reputation.'</span>';
    	}
	}
	$out.= '</div>';

	$out = apply_filters('sph_ReputationLevel', $out, $user->reputation_level, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}
