<?php
/*
Simple:Press
Template Tag(s) - User Selection (theme/language)
$LastChangedDate: 2012-04-05 20:55:10 +0100 (Thu, 05 Apr 2012) $
$Rev: 8267 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_UserSelectOptionsTag($args='', $labelTheme='', $labelLanguage='') {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	$defs = array('tagId' 			=> 'spUserSelectOptions',
				  'tagClass' 		=> 'spSelectOptions',
				  'themeClass'		=> 'spSelectTheme',
				  'languageClass'	=> 'spSelectLanguage',
				  'labelClass'		=> 'spLabel',
				  'selectClass'		=> 'spSelect',
				  'theme'			=> 1,
				  'language'		=> 1,
				  'spacer'			=> '&nbsp;&nbsp;&nbsp;&nbsp;',
				  'stacked'			=> 0,
				  'echo'			=> 1
				  );

	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_UserSelectOptions_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$themeClass		= esc_attr($themeClass);
	$languageClass	= esc_attr($languageClass);
	$labelClass	    = esc_attr($labelClass);
	$selectClass	= esc_attr($selectClass);
	$theme			= (int) $theme;
	$language		= (int) $language;
	$spacer			= esc_attr($spacer);
	$stacked		= (int) $stacked;
	$echo			= (int) $echo;

	if (!empty($labelTheme)) $labelTheme = SP()->displayFilters->title($labelTheme);
	if (!empty($labelLanguage)) $labelLanguage = SP()->displayFilters->title($labelLanguage);

	$sfstorage = array();
	$sfstorage = SP()->options->get('sfconfig');
	$overlays = '';

	if ($theme) {
		$currentTheme = SP()->core->forumData['theme'];
		if ($currentTheme['color']) {
            $overlays = SP()->theme->get_overlays(SPTHEMEBASEDIR.$currentTheme['theme'].'/styles/overlays');

            # pull in parent overlays if child theme
            if (!empty($currentTheme['parent'])) {
                $parent_overlays = SP()->theme->get_overlays(SPTHEMEBASEDIR.$currentTheme['parent'].'/styles/overlays');
                $overlays = array_merge($overlays, $parent_overlays);
                $overlays = array_unique($overlays);
            }
        } else {
            $overlays = '';
        }
	}
	if ($language ? $langs = get_available_languages(SP_STORE_DIR.'/'.$sfstorage['language-sp']) : $langs='');

    $out = '';

	if (($theme && $overlays) || ($language && $langs)) {
		$out.= "<div id='$tagId' class='$tagClass'>";

		if ($theme && $overlays) {
			if (isset($_COOKIE['overlay'])) $currentTheme['color'] = $_COOKIE['overlay'];
            $out.= "<span class='$themeClass'>";
            if (!empty($labelTheme)) $out.= "<label class='$labelClass'>$labelTheme</label>";
    		$out.= "<select name='overlay' class='$selectClass' onchange='spj.setUserOption(this);' >";
			$out.= '<optgroup label="'.__('Select Theme', 'sp-usel').':">';
			$out.= '<option value="ovdefault">'.esc_html(__('Site Default', 'sp-usel')).'</option>';
			foreach ($overlays as $overlay) {
				$overlay = trim($overlay);
				$selected = ($overlay == $currentTheme['color']) ? ' selected="selected" ' : '';
				$out.= '<option'.$selected.' value="'.esc_attr($overlay).'">'.esc_html($overlay).'</option>';
			}
			$out.= '</optgroup></select>';
            $out.= '</span>';
			if ($language && $langs) {
				$out.=  ($stacked) ? sp_InsertBreak('echo=0') : $spacer;
			}
		}

		if ($language && $langs) {
			$out.= "<span class='$languageClass'>";
			if (!empty($labelLanguage)) $out.= "<label class='$labelClass'>$labelLanguage</label>";
			$out.= "<select name='language' class='$selectClass' onchange='spj.setUserOption(this);' >";
			$out.= '<optgroup label="'.__('Select Language', 'sp-usel').':">';
			$selected = (!isset($_COOKIE['language']) || $_COOKIE['language'] == 'default') ? ' selected="selected" ' : '';
			$out.= '<option value="default">'.esc_html(__('Site Default', 'sp-usel')).'</option>';

            $data = SP()->options->get('user-selection');

			if ($data['usedefault']) $langs[] = 'en';
			sort($langs);
			foreach ($langs as $lang) {
				if(substr($lang, 0, 4) == 'spa-') continue;
                $thislang = SP()->saveFilters->filename($lang);
				$lang = str_replace('sp-', '', trim($thislang));
                $name = (!empty($data['names'][$thislang])) ? $data['names'][$thislang] : $lang;
				$name = apply_filters('sph_LanguageDisplay', $name);
				$selected = (isset($_COOKIE['language']) && $lang == $_COOKIE['language']) ? ' selected="selected" ' : '';
				$out.= '<option'.$selected.' value="'.esc_attr($lang).'">'.esc_html($name).'</option>';
			}
			$out.= '</optgroup></select>';
			$out.= '</span>';
		}

		$out.= "</div>";
	}

	$out = apply_filters('sph_UserSelectOptions', $out, $a);

	if ($echo) {
	    echo $out;
	} else {
		return $out;
	}
}
