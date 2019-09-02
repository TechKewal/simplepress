<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/* 	====================================================================================================

	sp_AdminModeratorOnlineTag()

	displays online status of admins and moderators

	parameters:
		name			description								type			default
		-------------------------------------------------------------------------------------------------
		tagId			unique id to use for div or list		text			spAdminModeratorOnlineTag
		tagClass		class to be applied for styling			text			spListTag
		moderator		Display moderator status				true/false		true
		custom			Display custom status text if set		true/false		true
		customClass		class to be applied to message			text			spAdminMessageTag
		listTags		Wrap in <ul> and <li> tags				true/false		true
						If false a div will be used
		listClass		class to be applied to list item style	text			spListItemTag
		onToolTip		Tooltip to display if online			text			'Online'
		onIcon			Icon to display if online				filename		sp_UserOnlineSmall.png
		offToolTip		Tooltip to display if offline			text			'Offline'
		offIcon			Icon to display if offline				filename		sp_UserOfflineSmall.png
        useAvatar       Use Avatar in place of on/off icons     true/false		false
        avatarSize      size of avatar if used                  number          '25'
		echo			echo content or return content			true/false		true

	NOTE: True must be expressed as a 1 and False as a zero

========================================================================================================*/

function sp_do_sp_AdminModeratorOnlineTag($args='') {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	$defs = array('tagId'    	=> 'spAdminModeratorOnlineTag',
				  'tagClass' 	=> 'spListTag',
				  'moderator'	=> 1,
				  'custom'		=> 1,
				  'customClass'	=> 'spAdminMessageTag',
				  'listTags'	=> 1,
				  'listClass'	=> 'spListItemTag',
				  'onToolTip'	=> __('Online', 'sp-ttags'),
				  'onIcon'		=> 'sp_UserOnlineSmall.png',
				  'offIcon'		=> 'sp_UserOfflineSmall.png',
				  'offToolTip'	=> __('Offline', 'sp-ttags'),
				  'useAvatar'	=> 0,
                  'avatarSize'	=> 25,
                  'echo'		=> 1
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_AdminModeratorOnlineTag_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$moderator	= (int) $moderator;
	$custom		= (int) $custom;
	$customClass= esc_attr($customClass);
	$listTags	= (int) $listTags;
	$listClass	= esc_attr($listClass);
	$onToolTip	= SP()->displayFilters->title($onToolTip);
	$offToolTip	= SP()->displayFilters->title($offToolTip);
	$onIcon		= SP()->saveFilters->filename($onIcon);
	$offIcon	= SP()->saveFilters->filename($offIcon);
    $useAvatar	= (int) $useAvatar;
    $avatarSize	= (int) $avatarSize;
	$echo		= (int) $echo;

	sp_check_api_support();

	$where='admin=1';
	if ($moderator) $where.=' OR moderator = 1';

	$query = new stdClass();
		$query->table		= SPMEMBERS;
		$query->fields		= 'user_id AS ID, user_email, '.SPMEMBERS.'.display_name, admin, user_options, admin_options, '.SPTRACK.'.id AS online';
		$query->left_join	= array(SPTRACK.' ON '.SPMEMBERS.'.user_id = '.SPTRACK.'.trackuserid', SPUSERS.' ON '.SPMEMBERS.'.user_id = '.SPUSERS.'.ID');
		$query->where		= $where;
		$query->orderby		= 'online DESC';
	$admins = SP()->DB->select($query);

	$out = '';

	if ($admins) {
		$out.= ($listTags) ? "<ul id='$tagId' class='$tagClass'>" : "<div id='$tagId' class='$tagClass'>";
		foreach ($admins as $admin) {
			$noAvatar = '';
			$msg = '';
			$userOpts = unserialize($admin->user_options);
			if (!isset($userOpts['hidestatus']) || !$userOpts['hidestatus']) {
				$userName = SP()->user->name_display($admin->ID, SP()->displayFilters->name($admin->display_name));
				$icon = ($admin->online) ? $onIcon : $offIcon;
				$tip = ($admin->online) ? $onToolTip : $offToolTip;
				if (!$useAvatar) $noAvatar.= SP()->theme->paint_icon('', SPTHEMEICONSURL, $icon, $tip);
				if (!$admin->online && $custom) {
					$userOpts = unserialize($admin->admin_options);
					if (isset($userOpts['offline_message'])) {
						$msg = SP()->displayFilters->text($userOpts['offline_message']);
						if ($msg != '') $msg= "<div class='$customClass'>$msg</div>";
					}
				}

				# begin loop display
				if ($listTags ? $out.= "<li class='$listClass'>" : $out.= "<div class='$listClass'>");

				# Avatar or Icon
				if ($useAvatar) {
				    $admin->avatar = '';
                    $out.= sp_UserAvatar("tagClass=spAvatar&imgClass=spAvatar&size=$avatarSize&context=user&echo=0", $admin);
				} else {
					$out.= $noAvatar;
				}

				# User name and current online status
				$out.= "<span class='spOnlineAdmin'><span class='spOnlineUser'>$userName</span> is <span class='admin$tip'>$tip</span>";

                # display offline message is set
                $out.= $msg;
				$out.= '</span>';

				# end loop display
				if ($listTags ? $out.= '<div style="clear:both;"></div></li>' : $out.= '</div><div style="clear:both;"></div>');
            }
        }
		$out.= ($listTags) ? '</ul>' : '</div>';
	}

	$out = apply_filters('sph_AdminModeratorOnlineTag', $out);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_do_AdminModeratorOnlineShortcode($atts) {
    $args = array();
    if (isset($atts['tagid']))          $args['tagId']          = $atts['tagid'];
    if (isset($atts['tagclass']))       $args['tagClass']       = $atts['tagclass'];
    if (isset($atts['moderator']))      $args['moderator']      = $atts['moderator'];
    if (isset($atts['custom']))         $args['custom']         = $atts['custom'];
    if (isset($atts['customclass']))    $args['customClass']    = $atts['customclass'];
    if (isset($atts['listtags']))       $args['listTags']       = $atts['listtags'];
    if (isset($atts['listclass']))      $args['listClass']      = $atts['listclass'];
    if (isset($atts['ontooltip']))      $args['onToolTip']      = $atts['ontooltip'];
    if (isset($atts['onicon']))         $args['onIcon']         = $atts['onicon'];
    if (isset($atts['officon']))        $args['offIcon']        = $atts['officon'];
    if (isset($atts['offtooltip']))     $args['offToolTip']     = $atts['offtooltip'];
    if (isset($atts['useAvatar']))      $args['useAvatar']      = $atts['useAvatar'];
    if (isset($atts['avatarSize']))     $args['avatarSize']     = $atts['avatarSize'];

    $args['echo'] = 0;
    return sp_do_sp_AdminModeratorOnlineTag($args);
}
