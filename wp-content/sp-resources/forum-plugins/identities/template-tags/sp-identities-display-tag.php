<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_do_PostIndexIdentityDisplay($args, $toolTip, $identity) {
	if (SP()->forum->view->thisPostUser->guest) return;

    $slug = sp_create_slug($identity, false);
	if (empty(SP()->forum->view->thisPostUser->$slug)) return;

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

	$defs = array('tagId'    	=> 'spPostIndexUser'.$slug.'%ID%',
				  'tagClass' 	=> 'spPostUser'.$slug,
				  'iconClass'	=> 'spImg',
                  'targetNew'   => 1,
                  'noFollow'    => 0,
				  'echo'		=> 1,
				  'get'			=> 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PostIndexUser'.$slug.'_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$iconClass 	= esc_attr($iconClass);
	$toolTip	= esc_attr($toolTip);
	$targetNew  = (int) $targetNew;
	$noFollow   = (int) $noFollow;
	$echo		= (int) $echo;
	$get		= (int) $get;

	$tagId = str_ireplace('%ID%', SP()->forum->view->thisPost->post_id, $tagId);

	if(filter_var(SP()->forum->view->thisPostUser->$slug, FILTER_VALIDATE_URL)) {
		$url = SP()->displayFilters->url(SP()->forum->view->thisPostUser->$slug);
	} else {
		$url = $thisIdentity['base_url'].'/'.SP()->forum->view->thisPostUser->$slug;
	}

	if ($get) return $url;

	$sfconfig = SP()->options->get('sfconfig');

    $target = ($targetNew) ? ' target="_blank"' : '';
    $follow = ($noFollow) ? ' rel="nofollow"' : '';
	$out = "<a id='$tagId' class='$tagClass' href='".$thisIdentity['base_url'].'/'.SP()->forum->view->thisPostUser->$slug."' title='$toolTip'$target$follow><img class='$iconClass' src='".SP_STORE_URL.'/'.$sfconfig['identities'].'/'.$thisIdentity['file']."' alt='' /></a>\n";
	$out = apply_filters('sph_PostIndexUser'.$slug, $out, $a);

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}
