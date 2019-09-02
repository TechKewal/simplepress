<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/* 	=====================================================================================
	displays most reputable users in the forum

	parameters:
		name			description								type			default
		----------------------------------------------------------------------------------------
		tagId			unique id to use for div or list		text			spMostReputableTag
		tagClass		class to be applied for styling			text			spMostReputableListTag
		listClass		class to be applied to list item style	text			spMostReputableListItemTag
		textClass		class to be applied to text labels		text			spTextTag
		listTags		Wrap in <ul> and <li> tags				int       		1
						- If false a div will be used
		limit			How many users to show in the list		int  			10
		includeMods		Should we include moderators in list?   int  			0
		minRep			Minimum reputation to be included       int  			0
		echo			echo content or return content			int   		    1

	itemOrder - description
	=======================
	This parameter controls both which components are displayed and also the order in which they
	are displayed. Use the following codes to construct this parameter. No spaces or other
	characters can be used:

			A	-	Displays the users Avatar
			U	-	Displays the Users display name
			B	-	Displays the users reputation level badge
			L	-	Displays the users reputation level name
            R   -   Displays the user reputation value

 	===================================================================================*/
function sp_do_MostReputable($args='') {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	$defs = array('tagId'    	=> 'spMostReputable',
				  'tagClass' 	=> 'spMostReputable',
				  'listClass'	=> 'spListItemTag',
				  'textClass'	=> 'spMostReputableText',
				  'userClass'	=> 'spMostReputableName',
				  'avatarClass'	=> 'spMostReputableAvatar',
				  'badgeClass'	=> 'spMostReputableBadge',
				  'levelClass'	=> 'spMostReputableLevel',
				  'repClass'	=> 'spMostReputableAmount',
				  'listTags'	=> 1,
				  'limit'		=> 10,
				  'includeMods'	=> 0,
				  'minRep'	    => 0,
				  'itemOrder'	=> 'AURB',
				  'avatarSize'	=> 30,
				  'echo'		=> 1
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_MostReputable_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$listClass		= esc_attr($listClass);
	$textClass		= esc_attr($textClass);
	$userClass		= esc_attr($userClass);
	$avatarClass	= esc_attr($avatarClass);
	$badgeClass 	= esc_attr($badgeClass);
	$levelClass 	= esc_attr($levelClass);
	$repClass	    = esc_attr($repClass);
	$listTags		= (int) $listTags;
	$limit			= (int) $limit;
	$includeMods	= (int) $includeMods;
	$minRep	        = (int) $minRep;
	$itemOrder		= esc_attr($itemOrder);
	$avatarSize		= (int) $avatarSize;
	$echo			= (int) $echo;

	sp_check_api_support();

    $where = ($includeMods) ? "(admin=0 AND reputation >= $minRep)" : "(admin=0 AND moderator=0 AND reputation >= $minRep)";

	$query = new stdClass();
		$query->table  = SPMEMBERS;
		$query->fields = 'user_id, display_name, reputation';
		$query->limits = $limit;
		$query->orderby = 'reputation DESC, user_id ASC';
		$query->where  = $where;
		$query = apply_filters('sph_MostReputableQuery', $query);
	$users = SP()->DB->select($query);

    $out = '';

	$out.= ($listTags) ? "<ul id='$tagId' class='$tagClass'>" : "<div id='$tagId' class='$tagClass'>";
	if ($users) {
        foreach ($users as $user) {
            $level = sp_reputation_get_level($user->reputation, false);

			$out.= ($listTags) ? "<li class='$listClass'>" : "<div class='$textClass'>";

    		for ($x=0; $x<strlen($itemOrder); $x++) {
    			switch (substr($itemOrder, $x, 1)) {
    				case 'A':
    					# Avatar
    					$spx = ($avatarSize + 10).'px';
    					$out.= sp_UserAvatar("tagClass=$avatarClass&size=$avatarSize&link=none&context=user&echo=0", $user->user_id);
    					break;

    				case 'U':
    					# User
    					$out.= "<span class='$userClass'>$user->display_name</span>";
    					break;

    				case 'B':
    					# Reputation Badge
                   		$out.= "<img class='$badgeClass' src='$level->badge' alt='".esc_attr($level->name)."' title='".esc_attr($level->name)." ($user->reputation)' />";
    					break;

    				case 'L':
    					# Reputation Level Name
    					$out.= "<span class='$levelClass'>$level->name</span>";
    					break;

    				case 'R':
    					# Reputation Amount
    					$out.= "<span class='$repClass'>($user->reputation)</span>";
    					break;

    				default:
    					# Invalid code
    					$out.= '<br />'.__('Invalid Item Order Code Found', 'sp-reputation').'<br />';
    					break;
    			}
    		}

    		$out.= ($listTags) ? '</li>' : '</div>';
        }
	} else {
		$out.= "<div class='$textClass'>".__('You do not have any users', 'sp-reputation').'</div>';
	}
	$out.= ($listTags) ? '</ul>' : '</div>';

	$out = apply_filters('sph_MostReputable', $out);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}
