<?php
/*
Simple:Press
Policy Docs Plugin Support Routines
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_policy_doc_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'policy-doc/sp-policy-doc-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-policy')."'>".__('Uninstall', 'sp-policy').'</a>';
        $url = SPADMINCOMPONENTS.'&amp;tab=plugin&amp;admin=sp_policy_doc_admin_options&amp;save=sp_policy_doc_admin_save_options&amp;form=1';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-policy')."'>".__('Options', 'sp-policy').'</a>';
    }
	return $actionlink;
}

function spPolicyDocTemplateName($name, $pageview) {
	if ($pageview != 'policy') return $name;

	# locate template - check if in theme if not use plugin
	$action = (isset($_GET['popup'])) ? SP()->filters->str($_GET['popup']) : '';
    if ($action == 'reg') {
        $tempName = SP()->theme->find_template(PDTEMPDIR, 'spRegisterDocView.php');
    } elseif ($action == 'priv') {
        $tempName = SP()->theme->find_template(PDTEMPDIR, 'spPrivacyDocView.php');
    } else {
        $tempName = SP()->theme->find_template(PDTEMPDIR, 'spPolicyDocView.php');
    }
	return $tempName;
}

function sp_policy_doc_do_header() {
	# check for css in current theme first
	$css = SP()->theme->find_css(PDCSS, 'sp-policy-doc.css', 'sp-policy-doc.spcss');
    SP()->plugin->enqueue_style('sp-policy', $css);
}

function sp_policy_doc_do_tooltip($tooltips) {
	$tooltips['policies'] = 'The policy documents folder can optionally contain plain text files (which can include HTML tags) describing forum policy.
	If used, two documents can be defined directly used by Simple:Press. These are:
	user registration policy document and site privacy document.
	Note that in many countries, a statement of privacy policy is a legal requirement.';
	return $tooltips;
}

function sp_policy_doc_retrieve($policy) {
	$spPolicy = SP()->options->get('policy-doc');
	$item = ($policy == 'privacy') ? 'privfile' : 'regfile';
	if (!empty($spPolicy[$item])) {
		# text file option
		$sfconfig = SP()->options->get('sfconfig');
		$filename = SP_STORE_DIR.'/'.$sfconfig['policies'].'/'.$spPolicy[$item];
		if (file_exists($filename) == false) {
			return __('Policy Document Not Found', 'sp-policy');
		} else {
			$handle = fopen($filename, 'r');
			$contents = fread($handle, filesize($filename));
			fclose($handle);
			return $contents;
		}
	} else {
		# sfmeta option
		$policytext = SP()->meta->get($policy, 'policy');
		return SP()->displayFilters->text($policytext[0]['meta_value']);
	}
}
