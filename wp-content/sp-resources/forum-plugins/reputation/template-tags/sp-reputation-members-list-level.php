<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_reputation_members_list_level($args='', $label='') {
    # make sure members list is viewable
	if (!SP()->auths->get('view_members_list')) return;

	$defs = array('tagId'		=> 'spMembersListReputation%ID%',
				  'tagClass'	=> 'spInRowCount',
				  'labelClass'	=> 'spInRowLabel',
				  'level'		=> 0,
				  'levelClass'	=> 'spInRowRank',
				  'badge'		=> 1,
				  'badgeClass'	=> 'spImg',
				  'showRep' 	=> 0,
				  'repClass'	=> 'spUserRep',
				  'stack'		=> 1,
				  'echo'		=> 1,
				  'get'			=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_MemberListReputation_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$labelClass		= esc_attr($labelClass);
	$levelClass		= esc_attr($levelClass);
	$badgeClass		= esc_attr($badgeClass);
	$repClass		= esc_attr($repClass);
	$level			= (int) $level;
	$badge			= (int) $badge;
	$showRep	    = (int) $showRep;
	$stack			= (int) $stack;
	$echo			= (int) $echo;
	$get			= (int) $get;

	$tagId = str_ireplace('%ID%', SP()->forum->view->thisMember->user_id, $tagId);
	$att = ($stack) ? '<br />' : ': ';

    $reputation_level = sp_reputation_get_level(SP()->forum->view->thisMember->reputation, SP()->forum->view->thisMember->admin);

	# grab the user rank info
	if ($get) return $reputation_level;

	if (!$level && !$badge && !$showRep) return;

	# now render it
    $out = '';
	if (!empty($reputation_level)) {
    	$out.= "<div id='$tagId' class='$tagClass'>";
    	if (!empty($label)) $out.= "<span class='$labelClass'>".SP()->displayFilters->title($label)."$att</span>";
    	if ($badge && !empty($reputation_level->badge)) {
            $rep = (SP()->forum->view->thisMember->admin) ? '' :  " (".SP()->forum->view->thisMember->reputation.")";
            $out.= "<img class='$badgeClass' src='".$reputation_level->badge."' alt='".esc_attr($reputation_level->name)."' title='".esc_attr($reputation_level->name).$rep."' />$att";
        }
    	if ($level) $out.= "<span class='$levelClass'>".$reputation_level->name."</span>$att";
    	if ($showRep && !SP()->forum->view->thisMember->admin) $out.= "<span class='$repClass'>".SP()->forum->view->thisMember->reputation."</span>";
    	$out.= "</div>\n";
    }

	$out = apply_filters('sph_MemberListReputation', $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}
