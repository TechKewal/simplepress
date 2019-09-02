<?php
/*
Simple:Press
Report Posts Plugin Support Routines
$LastChangedDate: 2018-10-19 03:14:00 -0500 (Fri, 19 Oct 2018) $
$Rev: 15759 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_report_post_do_header() {
	$css = SP()->theme->find_css(RPCSS, 'sp-report-post.css', 'sp-report-post.spcss');
    SP()->plugin->enqueue_style('sp-report', $css);
}

function sp_report_post_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'report-post/sp-report-post-plugin.php') {
	    $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
	    $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-report')."'>".__('Uninstall', 'sp-report').'</a>';
	    $url = SPADMINOPTION.'&amp;tab=email';
	    $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-report')."'>".__('Options', 'sp-report').'</a>';
 	}
	return $actionlink;
}

function spReportPostTemplateName($name, $pageview) {
	if ($pageview != 'report-post') return $name;
	# locate template - check if in theme if not use plugin
	$tempName = SP()->theme->find_template(RPTEMPDIR, 'spReportPostView.php');
	return $tempName;
}

function spReportPostPageTitle($title, $sep) {
	$sfseo = SP()->options->get('sfseo');
	if ($sfseo['sfseo_page'] && SP()->rewrites->pageData['pageview'] == 'report-post') $title = __('Report Post', 'sp-report').$sep.$title;
    return $title;
}

function spReportPostCanonicalUrl($url) {
    if (SP()->rewrites->pageData['pageview'] == 'report-post') $url = SP()->spPermalinks->get_url('report-post');
    return $url;
}

function sp_ReportPostFormTag() {
	$out = '';

	if(!isset($_POST['rppost'])) {
		$msg = __('No Post has been specified', 'sp-report');
		SP()->notifications->message(1, $msg);
		$out.= SP()->notifications->render_queued();
		return;
	}

	if (isset($_POST['rpurl'])) {
		$returnurl = SP()->spPermalinks->permalink_from_postid(SP()->filters->integer($_POST['rppost']));
		SP()->cache->add('url', $returnurl);
	} else {
		$returnurl = SP()->cache->get('url');
	}

	# Check and validate user
	if (SP()->user->thisUser->ID != SP()->filters->integer($_POST['rpuser'])) {
		$out.= SP()->notifications->render_queued();
		$out.= '<a href="'.$returnurl.'" />'.__('Return', 'sp-report').'</a>';
		echo $out;
		return;
	}

	$postcontent = SP()->DB->table(SPPOSTS, 'post_id='.SP()->filters->integer($_POST['rppost']), 'post_content');
	$postcontent = SP()->displayFilters->content($postcontent);

	$out.= '<div id="spReportPost">';
	$out.= '<br />';
	$out.= '<fieldset>';
	$out.= '<legend>'.__('Report Questionable Post', 'sp-report').'</legend>';
	$out.= '<span class="spLabel">'.__('Poster', 'sp-report').': </span><br />'.stripslashes($_POST['rpposter']).'<br /><br />';
	$out.= '<span class="spLabel">'.__('Post Content', 'sp-report').': </span><br />'.$postcontent;
	$out.= "<form method='post' action='$returnurl'>";
	$out.= '<input type="hidden" tabindex="0" name="posturl" id="posturl" value="'.esc_attr($returnurl).'" />';
	$out.= '<input type="hidden" tabindex="0" name="postauthor" id="postauthor" value="'.$_POST['rpposter'].'" />';
	$out.= '<input type="hidden" tabindex="0" name="postcontent" id="postcontent" value="'.esc_attr($postcontent).'" />';
	$out.= '<input type="hidden" tabindex="0" name="postid" id="postid" value="'.SP()->filters->integer($_POST['rppost']).'" />';
	$out.= '<br /><span class="spLabel">'.__('Your Comments On this Post', 'sp-report').':</span>';
	$out.= '<br /><textarea class="spControl" name="postreport" rows="10" ></textarea>';
	$out.= '<br /><br /><input type="submit" tabindex="100" class="spSubmit" name="sendrp" value="'.__('Send Post Report', 'sp-report').'" />';
	$out.= '<input type="Submit" class="spSubmit spReportPostReturn" name="button5" value="'.__('Return to Forum', 'sp-report').'" data-url="'.$returnurl.'" />';
	$out.= '</form>';
	$out.= '</fieldset>';
	$out.= '</div>';

	$out = apply_filters('sph_ReportPost', $out);
	echo $out;
}

function spReportPostSendEmail() {
	$eol = "\r\n";
	$msg = '';

	# if either the posturl or the comments are empty then just forget it
	if (empty($_POST['posturl']) || empty($_POST['postreport'])) return;

	# clean up the content for the plain text email
    $post_content = stripslashes($_POST['postcontent']);
	$post_content = html_entity_decode($post_content, ENT_QUOTES);
	$post_content = SP()->displayFilters->content($post_content);
	$post_content = str_replace('&nbsp;', ' ', $post_content);
	$post_content = strip_tags($post_content);

	if (SP()->user->thisUser->guest) {
		$reporter = __('A guest visitor', 'sp-report');
	} else {
		# if it got ths far but there is no display name then it's bogus - leave
		if (empty(SP()->user->thisUser->display_name)) return;
		$reporter = __('Member', 'sp-report').' '.SP()->displayFilters->name(SP()->user->thisUser->display_name);
	}

    $report = SP()->saveFilters->nohtml($_POST['postreport']);

	$msg.= sprintf(__('%s has reported the following post as questionable', 'sp-report'), $reporter).$eol.$eol;
	$msg.= SP()->filters->str($_POST['posturl']).$eol;
	$msg.= stripslashes($_POST['postauthor']).$eol;
	$msg.= $post_content.$eol.$eol;
	$msg.= __('Comments', 'sp-report').$eol;
	$msg.= SP()->saveFilters->nohtml($report).$eol;
    $msg = apply_filters('sph_report_post_email_msg', $msg, SP()->filters->integer($_POST['postid']), $report, $reporter);

	$option = SP()->options->get('report-post');
	$email_list = SP()->editFilters->text($option['email-list']);
    $subject = apply_filters('sph_report_post_email_subject', sprintf(__('[%s] questionable post report', 'sp-report'), get_option('blogname')), $report, $reporter);
	$email_sent = sp_send_email($email_list, $subject, $msg);

	if ($email_sent[0]) {
		$returnmsg = 0;
	} else {
		$returnmsg = 1;
	}
	SP()->notifications->message($returnmsg, $email_sent[1]);
}
