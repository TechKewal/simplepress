<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_PostIndexReportPostTag($args='', $label='', $toolTip='') {
	if (!SP()->auths->get('report_posts', SP()->forum->view->thisTopic->forum_id)) return;
	if (SP()->forum->view->thisPostUser->admin && !SP()->auths->get('view_admin_posts', SP()->forum->view->thisTopic->forum_id)) return;
	if (SP()->auths->get('view_own_admin_posts', SP()->forum->view->thisTopic->forum_id) && !SP()->auths->forum_admin(SP()->forum->view->thisPostUser->ID) && !SP()->auths->forum_mod(SP()->forum->view->thisPostUser->ID) && SP()->user->thisUser->ID != SP()->forum->view->thisPostUser->ID) return;

	$defs = array('tagId' 		=> 'spPostIndexReport%ID%',
				  'tagClass' 	=> 'spPostReportPost',
				  'icon' 		=> 'sp_ReportPost.png',
				  'iconClass'	=> 'spIcon'
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PostIndexReportPost_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);
	$p = (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? RPIMAGESMOB : RPIMAGES;
	$iconClass 	= esc_attr($iconClass);
	$icon		= SP()->theme->paint_icon($iconClass, $p, sanitize_file_name($icon));
	$toolTip	= esc_attr($toolTip);

	$tagId = str_ireplace('%ID%', SP()->forum->view->thisPost->post_id, $tagId);

	$out = '';
	$out.= '<form class="sfhiddenform" action="'.SP()->spPermalinks->get_url('report-post').'" method="post" name="report'.SP()->forum->view->thisPost->post_id.'">';
	$out.= '<input type="hidden" class="sfhiddeninput" name="rpurl" value="'.esc_attr(SP()->forum->view->thisPost->post_permalink).'" />';
	$out.= '<input type="hidden" class="sfhiddeninput" name="rpuser" value="'.SP()->user->thisUser->ID.'" />';
	$out.= '<input type="hidden" class="sfhiddeninput" name="rppost" value="'.SP()->forum->view->thisPost->post_id.'" />';
	$out.= '<input type="hidden" class="sfhiddeninput" name="rpposter" value="'.esc_attr(SP()->forum->view->thisPostUser->display_name).'" />';
	$out.= "<a class='$tagClass' id='$tagId' title='$toolTip' rel='nofollow' href='javascript:document.report".SP()->forum->view->thisPost->post_id.".submit()'>";
	if (!empty($icon)) $out.= $icon;
	$out.= SP()->displayFilters->title($label);
	$out.= '</a>';
	$out.= "</form>\n";

	$out = apply_filters('sph_PostIndexReportPost', $out, $a);
	echo $out;
}
