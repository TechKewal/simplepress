<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_PolicyDocShowTag($args='', $headerLabel='', $acceptLabel='') {
	$defs = array('tagId' 		=> 'spPolicyDocReg',
				  'tagClass' 	=> 'spPolicyDocReg',
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PolicyDocReg_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId		= esc_attr($tagId);
	$tagClass	= esc_attr($tagClass);

	$out = '';
	$out.= "<div id='$tagId'>";
	$out.= '<p class="spLabel">'.SP()->displayFilters->title($headerLabel).'</p>';
	$out.= "<div class='$tagClass'>";
	$out.= sp_policy_doc_retrieve('registration');
	$out.= '</div>';

	$out.= '<br />';
	$spPolicy = SP()->options->get('policy-doc');
	if ($spPolicy['regcheck']) {
		$out.= '<p><input type="checkbox" class="spControl" id="sf-accept" name="sf-accept" tabindex="1" /><label for="sf-accept">'.SP()->displayFilters->title($acceptLabel).'</label></p>';
		$enabled = ' disabled="disabled" ';
	} else {
        $enabled = '';
	}
   	$spLogin = SP()->options->get('sflogin');
    $url = site_url('wp-login.php?action=register&amp;redirect_to='.$spLogin['sfregisterurl'], 'login');
    $out.= '<input type="button" class="spSubmit spPolicyRedirect"'.$enabled.' tabindex="2" id="regbutton" name="regbutton" value="'.esc_attr(__('Register', 'sp-policy')).'" data-url="'.$url.'" />';
	$out.= '<input type="button" class="spSubmit spPolicyRedirect" tabindex="3" id="retbutton" name="retbutton" value="'.esc_attr(__('Return to Forum', "sp-policy")).'" data-url="'.SP()->spPermalinks->get_url().'" />';
	$out.= "</div>\n";

	$out = apply_filters('sph_PolicyDocReg', $out, $a);
	echo $out;
}
