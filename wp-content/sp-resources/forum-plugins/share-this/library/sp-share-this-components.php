<?php
/*
Simple:Press
Share This Plugin Admin Options Form
$LastChangedDate: 2017-12-28 11:38:04 -0600 (Thu, 28 Dec 2017) $
$Rev: 15602 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_share_this_do_header() {
	$css = SP()->theme->find_css(SPSHARECSS, 'sp-share-this.css', 'sp-share-this.spcss');
	SP()->plugin->enqueue_style('sp-share-this', $css);
}

function sp_share_this_do_sharing($url, $title, $summary) {
	$options = SP()->options->get('share-this');

	$out = '';
	$style = '';

	if ($options['style'] == 1) $style = '_large';
	if ($options['style'] == 2) $style = '';
	if ($options['style'] == 3) $style = '_button';
	if ($options['style'] == 4) $style = '_hcount';
	if ($options['style'] == 5) $style = '_vcount';

	foreach ($options['buttons'] as $button) {
		if (!empty($button['enable'])) {
			if ($button['id'] == 'Facebook') {
				$text = ($options['style'] == 2 && !$options['labels']) ? '' : "displayText='Facebook'";
				$out.= "<span class='st_facebook$style' $url $title $summary $text></span>";
			}
			if ($button['id'] == 'Facebook Like') {
				$text = ($options['style'] == 2 && !$options['labels']) ? '' : "displayText='Facebook Like'";
				$out.= "<span class='st_fblike$style' $url $title $summary $text></span>";
			}
			if ($button['id'] == 'Twitter') {
				$text = ($options['style'] == 2 && !$options['labels']) ? '' : "displayText='Tweet'";
				$out.= "<span class='st_twitter$style' $url $title $summary $text></span>";
			}
			if ($button['id'] == __('Email', 'sp-share-this')) {
				$text = ($options['style'] == 2 && !$options['labels']) ? '' : "displayText='".__('Email', 'sp-share-this')."'";
				$out.= "<span class='st_email$style' $url $title $summary $text></span>";
			}
			if ($button['id'] == 'LinkedIn') {
				$text = ($options['style'] == 2 && !$options['labels']) ? '' : "displayText='LinkedIn'";
				$out.= "<span class='st_linkedin$style' $url $title $summary $text></span>";
			}
			if ($button['id'] == 'Tumblr') {
				$text = ($options['style'] == 2 && !$options['labels']) ? '' : "displayText='Tumblr'";
				$out.= "<span class='st_tumblr$style' $url $title $summary $text></span>";
			}
			if ($button['id'] == 'Stumble Upon') {
				$text = ($options['style'] == 2 && !$options['labels']) ? '' : "displayText='Stumble'";
				$out.= "<span class='st_stumbleupon$style' $url $title $summary $text></span>";
			}
			if ($button['id'] == 'Google Share') {
				$text = ($options['style'] == 2 && !$options['labels']) ? '' : "displayText='Google'";
				$out.= "<span class='st_googleplus$style' $url $title $summary $text></span>";
			}
			if ($button['id'] == 'Google Plus One') {
				$text = ($options['style'] == 2 && !$options['labels']) ? '' : "displayText='Google +1'";
				$out.= "<span class='st_plusone$style' $url $title $summary $text></span>";
			}
			if ($button['id'] == 'Share This') {
				$text = ($options['style'] == 2 && !$options['labels']) ? '' : "displayText='Share'";
				$out.= "<span class='st_sharethis$style' $url $title $summary $text></span>";
			}
		}
	}

	global $shareLoaded;
	if (!$shareLoaded) {
		$publisher = (empty($options['publisher'])) ? 'ur-47e79d8-f3fe-c2d3-b38b-4470c026e394' : $options['publisher'];
		$shorten = (empty($options['shorten'])) ? 'false' : 'true';
		$hover = (empty($options['hover'])) ? 'false' : 'true';
		$minor = (empty($options['minor'])) ? 'false' : 'true';

		$script = '';
		if ($options['local']) $script.= '<script>var switchTo5x=true;</script>';
		$url = is_ssl() ? 'https://ws.sharethis.com/button/buttons.js' : 'http://w.sharethis.com/button/buttons.js';
		$script.= "<script src='$url'></script>";
		$script.= '
			<script>
				stLight.options({
					publisher: "'.$publisher.'",
					shorten: '.$shorten.',
					minorServices: '.$minor.',
					onhover: '.$hover.',
					theme: "'.$options['theme'].'",
					newOrZero: "zero",
					doNotHash: true,
				});
			</script>';
		$shareLoaded = true;

		# allow modification of Share This script options
		$out.= apply_filters('sph_share_this_script', $script);
	}

	# let more icons be added that we dont have in admin
	$out = apply_filters('sph_share_this_chicklets', $out, $options, $style, $url, $title, $summary);

	return $out;
}

/********* Example filter use to add chicklet not included in admin
add_filter('sph_share_this_chicklets', 'my_share', 10, 6);
function my_share($out, $options, $style, $url, $title, $summary) {
	$out.= "<span class='st_allvoices$style' $url $title $summary displayText='Allvoices'></span>";
	return $out;
}
*/
