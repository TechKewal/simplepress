<?php
/*
$LastChangedDate: 2017-08-01 19:31:34 -0700 (Tue, 01 Aug 2017) $
$Rev: 15481 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_do_IdentitiesProfileDisplay($args, $tooltip, $identity, $label) {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	$slug = sp_create_slug($identity, false);

    # find the identity to use
    $identities = sp_identities_get_data();
    if (empty($identities)) return;

    $thisIdentity = '';
    foreach ($identities as $identity) {
        if ($identity['slug'] ==  $slug) {
            $thisIdentity = $identity;
            break;
        }
    }
    if (empty($thisIdentity)) return;

	$defs = array('targetNew'   => 1,
                  'noFollow'    => 0,
				  'iconClass'	=> 'spImg',
				  );
	$a = wp_parse_args($args, $defs);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$iconClass 	= esc_attr($iconClass);
	$toolTip	= esc_attr($toolTip);
	$targetNew  = (int) $targetNew;
	$noFollow   = (int) $noFollow;

	if (empty($label)) $label = $thisIdentity['name'];
	$label = SP()->displayFilters->title($label);

    $target = ($targetNew) ? ' target="_blank"' : '';
    $follow = ($noFollow) ? ' rel="nofollow"' : '';

	$sfconfig = SP()->options->get('sfconfig');

	$out = '<div class="spColumnSection spProfileLeftCol">';
	$out.= '<p class="spProfileShowIdentity">'.$label.':</p>';
	$out.= '</div>';
	$out.= '<div class="spColumnSection spProfileSpacerCol"></div>';
	$out.= '<div class="spColumnSection spProfileRightCol">';
	$out.= '<div class="spProfileShowIdentity">';
	$out.= (empty(SP()->user->profileUser->$slug)) ? '' : "<a  href='".$thisIdentity['base_url'].'/'.SP()->user->profileUser->$slug."' title='$toolTip'$target$follow><img class='$iconClass' src='".SF_STORE_URL.'/'.$sfconfig['identities'].'/'.$thisIdentity['file']."' alt='' /></a>";
	$out.= '</div>';
	$out.= '</div>';

	echo $out;
}
