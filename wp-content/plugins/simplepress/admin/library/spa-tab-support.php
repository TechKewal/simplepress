<?php
/*
Simple:Press
Admin Panels - Options/Components Tab Rendering Support
$LastChangedDate: 2017-02-11 15:35:37 -0600 (Sat, 11 Feb 2017) $
$Rev: 15187 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');


# == PAINT ROUTINES

# ------------------------------------------------------------------
# spa_paint_options_init()
# Initializes the tab index sequence starting with 100
# ------------------------------------------------------------------
function spa_paint_options_init() {
	global $tab;
	$tab = 100;
}

# ------------------------------------------------------------------
# spa_paint_open_tab()
# Creates the containing block around a form or main section
# ------------------------------------------------------------------
function spa_paint_open_tab($tabname, $full=false) {
	echo "<div class='sfform-panel'>";
	echo "<div class='sfform-panel-head'><span class='sftitlebar'>$tabname</span></div>\n";

	if ($full) {
		echo '<div class="sp-full-form">';
	} else {
		echo '<div class="sp-half-form">';
	}
}

# ------------------------------------------------------------------
# spa_paint_close_container();
# Closes the containing block around a form or main section
# ------------------------------------------------------------------
function spa_paint_close_container() {
	echo '</div>';
}

# ------------------------------------------------------------------
# spa_paint_close_tab()
# Closes the whole containing block
# ------------------------------------------------------------------
function spa_paint_close_tab() {
	echo '</div>';
}

# ------------------------------------------------------------------
# spa_paint_open_nohead_tab()
# Creates the containing block around a form or main section/no heading
# ------------------------------------------------------------------
function spa_paint_open_nohead_tab($full=false) {
	echo "<div class='sfform-panel-nohead'>";

	if ($full) {
		echo '<div class="sp-full-form">';
	} else {
		echo '<div class="sp-half-form">';
	}
}

function spa_paint_tab_right_cell() {
	echo '</div>';
	echo '<div class="sp-half-form">';
}

function spa_paint_open_panel() {
	echo '<div>';
}

function spa_paint_close_panel() {
	echo '</div>';
}

function spa_paint_open_fieldset($legend, $displayhelp=false, $helpname='', $displaylegend=true) {
	global $adminhelpfile;

	echo "<fieldset class='sffieldset'>\n";
	if($displaylegend) {
		echo "<legend><strong>$legend</strong></legend>\n";
	}
	if ($displayhelp) echo spa_paint_help($helpname, $adminhelpfile);
}

function spa_paint_close_fieldset() {
	echo "</fieldset>\n";
}

function spa_paint_input($label, $name, $value, $disabled=false, $large=false, $css_classes = '' ) {
	global $tab;
	
	$field_classes = 'sp-form-row' . " {$css_classes}";
	echo "<div class='{$field_classes}'>\n";
	if ($large) {
		echo "<div class='wp-core-ui sflabel sp-label-40'>\n";
	} else {
		echo "<div class='wp-core-ui sflabel sp-label-60'>\n";
	}
	echo "$label:</div>";
	$c = ($large) ? 'sp-input-60' : 'sp-input-40';
	echo "<input type='text' class='wp-core-ui $c' tabindex='$tab' name='$name' value='".esc_attr($value)."' ";
	if ($disabled == true) echo "disabled='disabled' ";
	echo "/>\n";
	echo '<div class="clearboth"></div>';
	echo '</div>';
	$tab++;
}

function spa_paint_single_input($name, $value, $disabled=false, $css_classes = '' ) {
	global $tab;
	
	$field_classes = "{$css_classes}";
	echo "<input type='text' class='wp-core-ui $field_classes' tabindex='$tab' name='$name' value='".esc_attr($value)."' ";
	if ($disabled == true) echo "disabled='disabled' ";
	echo "/>";
	$tab++;
}

function spa_paint_date($label, $name, $value, $disabled=false, $large=false) {
	global $tab;

	echo "<div class='sp-form-row'>\n";
	if ($large) {
		echo "<div class='wp-core-ui sflabel sp-label-40'>\n";
	} else {
		echo "<div class='wp-core-ui sflabel sp-label-60'>\n";
	}
	echo "$label:</div>";
	$c = ($large) ? 'sp-input-60' : 'sp-input-40';

	echo "<input type='date' class='wp-core-ui $c' tabindex='$tab' name='$name' value='".esc_attr($value)."' ";
	if ($disabled == true) echo "disabled='disabled' ";
	echo "/>\n";
	echo '<div class="clearboth"></div>';
	echo '</div>';
	$tab++;
}

function spa_paint_number($label, $name, $value, $disabled=false, $large=false) {
	global $tab;

	echo "<div class='sp-form-row'>\n";
	if ($large) {
		echo "<div class='wp-core-ui sflabel sp-label-40'>\n";
	} else {
		echo "<div class='wp-core-ui sflabel sp-label-60'>\n";
	}
	echo "$label:</div>";
	$c = ($large) ? 'sp-input-60' : 'sp-input-40';

	echo "<input type='number' class='wp-core-ui $c' tabindex='$tab' name='$name' value='".esc_attr($value)."' ";
	if ($disabled == true) echo "disabled='disabled' ";
	echo "/>\n";
	echo '<div class="clearboth"></div>';
	echo '</div>';
	$tab++;
}

function spa_paint_textarea($label, $name, $value, $submessage='', $rows=1) {
	global $tab;

	echo "<div class='sp-form-row'>\n";
	echo "<div class='wp-core-ui sflabel sp-label-50'>\n";
	echo "$label:";
	if (!empty($submessage)) echo "<br /><small><strong>".esc_html($submessage)."</strong></small>\n";
	echo '</div>';
	echo "<textarea rows='$rows' cols='80' class='wp-core-ui sp-textarea-50' tabindex='$tab' name='$name'>".esc_html($value)."</textarea>\n";
	echo '<div class="clearboth"></div>';
	echo '</div>';

	$tab++;
}

function spa_paint_wide_textarea($label, $name, $value, $submessage='', $xrows=1) {
	global $tab;

	echo "<div class='sp-form-row'>\n";
	echo "<div class='wp-core-ui sflabel sp-label'>\n";
	echo "$label:";
	if (!empty($submessage)) echo "<small><br /><strong>$submessage</strong><br /><br /></small>\n";
	echo '</div>';
	echo "<textarea rows='$xrows' cols='80' class='wp-core-ui sp-textarea' tabindex='$tab' name='$name'>".esc_attr($value)."</textarea>\n";
	echo '<div class="clearboth"></div>';
	echo '</div>';

	$tab++;
}

function spa_paint_thin_textarea($label, $name, $value, $submessage='', $xrows=1) {
	global $tab;

	echo "<div class='sp-form-row'>\n";
	echo "<div class='wp-core-ui sflabel sp-label-66'>\n";
	echo "$label:";
	if (!empty($submessage)) echo "<small><br /><strong>$submessage</strong><br /><br /></small>\n";
	echo '</div>';
	echo "<textarea rows='$xrows' cols='80' class='wp-core-ui sp-textarea-33' tabindex='$tab' name='$name'>".esc_attr($value)."</textarea>\n";
	echo '<div class="clearboth"></div>';
	echo '</div>';

	$tab++;
}

/**
 * Print wp editor
 * 
 * @global int $tab
 * 
 * @param string $label
 * @param string $name
 * @param string $value
 * @param string $submessage
 * @param int $xrows
 */
function spa_paint_editor($label, $name, $value, $submessage='', $xrows=1) {
	global $tab;

	echo "<div class='sp-form-row'>\n";
	echo "<div class='wp-core-ui sflabel sp-label-50'>\n";
	echo "$label:";
	if (!empty($submessage)) echo "<br /><small><strong>".esc_html($submessage)."</strong></small>\n";
	echo '</div>';
	echo '<div class="clearboth"></div>';
	wp_editor( html_entity_decode($value), $name, array(
					'media_buttons' => false,
					'quicktags'     => true,
					'textarea_rows' => $xrows
				));
	echo '<div class="clearboth"></div>';
	echo '</div>';

	$tab++;
}

/**
 * Print wide wp editor
 * 
 * @global int $tab
 * 
 * @param string $label
 * @param string $name
 * @param string $value
 * @param string $submessage
 * @param int $rows
 * @param boolean $mediaButtons [optional]
 */
function spa_paint_wide_editor($label, $name, $value, $submessage='', $xrows=1, $mediaButtons = false) {
	global $tab;

	add_filter( 'tiny_mce_before_init', 'spa_cache_ajax_editor_settings', 11, 2 );
	
	echo "<div class='sp-form-row'>\n";
	echo "<div class='wp-core-ui sflabel sp-label'>\n";
	echo "$label:";
	if (!empty($submessage)) echo "<small><br /><strong>$submessage</strong><br /><br /></small>\n";
	echo '</div>';
	echo '<div class="clearboth"></div>';
	wp_editor( html_entity_decode( $value ), $name, array(
					'media_buttons' => (bool) $mediaButtons,
					'quicktags'     => true,
					'textarea_rows' => $xrows
				));
	
	echo '<div class="clearboth"></div>';
	echo '</div>';

	$tab++;
}

/**
 * Print thin wp editor
 * 
 * @global int $tab
 * 
 * @param string $label
 * @param string $name
 * @param string $value
 * @param string $submessage
 * @param int $rows
 */
function spa_paint_thin_editor($label, $name, $value, $submessage='', $xrows=1) {
	global $tab;

	echo "<div class='sp-form-row'>\n";
	echo "<div class='wp-core-ui sflabel sp-label-66'>\n";
	echo "$label:";
	if (!empty($submessage)) echo "<small><br /><strong>$submessage</strong><br /><br /></small>\n";
	echo '</div>';
	echo '<div class="clearboth"></div>';
	wp_editor( html_entity_decode($value), $name, array(
					'media_buttons' => false,
					'quicktags'     => true,
					'textarea_rows' => $xrows
				));
	echo '<div class="clearboth"></div>';
	echo '</div>';

	$tab++;
}

/**
 * Print settings for ajax wp editors
 * 
 * @global array $spa_cache_ajax_editor_settings
 * 
 * @return void
 */
function spa_print_ajax_editor_settings() {
	global $spa_cache_ajax_editor_settings;
	
	if( !$spa_cache_ajax_editor_settings || !is_array( $spa_cache_ajax_editor_settings ) || empty( $spa_cache_ajax_editor_settings ) ) {
		return;
	}
	
	?>
	<script type="text/javascript">
			
			var spa_mceInit = <?php echo json_encode( $spa_cache_ajax_editor_settings ); ?>;
			
			<?php foreach( $spa_cache_ajax_editor_settings as $editor_id => $editor_setting ) { ?>
				
				var editor_id = '<?php echo $editor_id; ?>';
				
				if( !tinyMCEPreInit.mceInit.hasOwnProperty( editor_id ) ) {
					tinyMCEPreInit.mceInit[ editor_id ] = spa_mceInit[ editor_id ];
					tinyMCEPreInit.mceInit[ editor_id ].formats = <?php echo $editor_setting['formats']; ?>;
                } 
				
			<?php } ?>
			
			spa_mceInit = null;
			
	</script>
	<?php
}


/**
 * Cache wp editor settings
 * 
 * @global array $spa_cache_ajax_editor_settings
 * 
 * @param array $mceInit
 * @param string $editor_id
 * 
 * @return array
 */
function spa_cache_ajax_editor_settings( $mceInit, $editor_id ) {
	
	global $spa_cache_ajax_editor_settings;
	
	$spa_cache_ajax_editor_settings = isset( $spa_cache_ajax_editor_settings ) && is_array( $spa_cache_ajax_editor_settings ) ? $spa_cache_ajax_editor_settings : array();

	$spa_cache_ajax_editor_settings[ $editor_id ] = $mceInit;
	
	return $mceInit;
}

/**
 * Print thin code css editor
 * 
 * @param string $label
 * @param string $name
 * @param string $value
 * @param string $submessage [optional]
 * @param int $rows [optional]
 */
function spa_paint_css_editor($label, $name, $value, $submessage='', $rows=10) {
	if(floatval(get_bloginfo('version')) >= 4.9) {
error_log( 'blog version passed ok');		
		spa_paint_code_editor('text/css', $label, $name, $value, $submessage, $rows);
	} else {
		spa_paint_wide_textarea($label, $name, $value, $submessage, $rows);
	}
}

/**
 * Print thin code javascript editor
 * 
 * @param string $label
 * @param string $name
 * @param string $value
 * @param string $submessage [optional]
 * @param int $rows [optional]
 */
function spa_paint_js_editor($label, $name, $value, $submessage='', $rows=10) {
	if(floatval(get_bloginfo('version')) >= 4.9) {
		spa_paint_code_editor('text/javascript', $label, $name, $value, $submessage, $rows);
	} else {
		spa_paint_wide_textarea($label, $name, $value, $submessage, $rows);
	}
}

/**
 * Print thin code html editor
 * 
 * @param string $label
 * @param string $name
 * @param string $value
 * @param string $submessage [optional]
 * @param int $rows [optional]
 */
function spa_paint_html_editor($label, $name, $value, $submessage='', $rows=10) {
	if(floatval(get_bloginfo('version')) >= 4.9) {
		spa_paint_code_editor('text/html', $label, $name, $value, $submessage, $rows);
	} else {
		spa_paint_wide_textarea($label, $name, $value, $submessage, $rows);
	}	
}

/**
 * Print thin WP Code Editor
 * 
 * @global int $tab
 * 
 * @param string $type CodeMirror type: "text/html", "text/css", "text/javascript"
 * @param string $label
 * @param string $name
 * @param string $value
 * @param string $submessage [optional]
 * @param int $rows [optional]
 */
function spa_paint_code_editor($type, $label, $name, $value, $submessage='', $rows=10) {
    
	global $tab;
	
	// Make sure that the codeditor scripts are enqueued.
	// @TODO: However, because this is being called via ajax, this functiona actually does nothing.  
	//        Leaving it here though to note that something like this is needed.
	//		  Right now the scripts are loaded globally at the bottom of this file. ugg!
	spa_enqueue_codemirror();
	
    echo "<div class='sp-form-row'>\n";
    echo "<div class='wp-core-ui sflabel sp-label'>\n";
    if(mb_strlen($label)) {
        echo "$label:";
    }
    if (mb_strlen($submessage)) {
        echo "<small><br /><strong>$submessage</strong><br /><br /></small>\n";
    }
    $id = sprintf("sp-%s-editor-%d", str_replace('/', '-', $type), $tab);
    echo '</div>';
    echo '<div class="clearboth"></div>';
    echo "<textarea id=\"$id\" class=\"wp-core-ui sp-textarea\" rows=\"{$rows}\" name=\"{$name}\" tabindex=\"{$tab}\">{$value}</textarea>";
    if(floatval(get_bloginfo('version')) >= 4.9) {
        echo "<script>";
        echo sprintf( "jQuery( function() { 
                        var instance = wp.codeEditor.initialize( '{$id}', %s );
                        instance.codemirror.on('blur', function() {instance.codemirror.save();});                         
                    });", wp_json_encode(wp_enqueue_code_editor(array('type' => $type))) ) ;
        echo "</script>";
    }
    echo '<div class="clearboth"></div>';
    echo '</div>';
    $tab++;
}


function spa_paint_checkbox($label, $name, $value, $disabled=false, $large=false, $displayhelp=true, $msg='', $indent=false) {
	global $tab;

	echo "<div class='sp-form-row'>\n";
	if ($indent) echo str_repeat('&nbsp;', 7);
	echo "<input type='checkbox' tabindex='$tab' name='$name' id='sf-$name' ";
	if ($value == true) echo "checked='checked' ";
	if ($disabled == true) echo "disabled='disabled' ";
	echo "/>\n";
	echo "<label for='sf-$name' class='wp-core-ui'>$label</label>\n";
	echo '<div class="clearboth"></div>';
	if ($msg) echo $msg;
	echo '</div>';
	$tab++;
}

function spa_paint_select_start($label, $name, $helpname) {
	global $tab;

	echo "<div class='sp-form-row'>\n";
	echo "<div class='wp-core-ui sflabel sp-label-40'>$label:</div>\n";
	echo "<select class='wp-core-ui  sp-input-60' tabindex='$tab' name='$name'>";
	$tab++;
}

function spa_paint_select_end($msg='') {
	echo "</select>\n";
	echo '<div class="clearboth"></div>';
	if ($msg) echo $msg;
	echo '</div>';
}

function spa_paint_file($label, $name, $disabled, $large, $path) {
	global $tab;

	echo "<div class='sp-form-row'>\n";
	if ($large) {
		echo "<div class='wp-core-ui sflabel sp-label-40'>\n";
	} else {
		echo "<div class='wp-core-ui sflabel sp-label-60'>\n";
	}
	echo "$label:</div>";

	if (is_writable($path)) {
		echo '<div id="sf-upload-button" class="button-primary">'.SP()->primitives->admin_text('Browse').'</div>';
		echo '<div id="sf-upload-status"></div>';
	} else {
		echo '<div id="sf-upload-button" class="button-primary sfhidden"></div>';
		echo '<div id="sf-upload-status">';
		echo '<p class="sf-upload-status-fail">'.SP()->primitives->admin_text('Sorry, uploads disabled! Storage location does not exist or is not writable. Please see forum - integration - storage locations to correct').'</p>';
		echo '</div>';
	}
	echo '<div class="clearboth"></div>';
	echo '</div>';
	$tab++;
}

function spa_paint_hidden_input($name, $value) {
	echo '<div class="sfhidden">';
	echo "<input type='hidden' name='$name' value='".esc_attr($value)."' />";
	echo '</div>';
}


function spa_paint_link($link, $label) {
	echo "<span class='wp-core-ui sp-label'>";
	echo "<a href='".esc_url($link)."'>$label</a>\n";
	echo '</span>';
	echo '<div class="clearboth"></div>';
}

function spa_paint_radiogroup($label, $name, $values, $current, $large=false, $displayhelp=true, $class='') {
	global $tab;

	if ($class != '') $class=' class="'.$class.'" ';

	echo "<div class='sp-form-row'>\n";
	echo "<div class='wp-core-ui'><b>$label</b>:</div>\n";
	echo "<div class='wp-core-ui sp-radio'>";

	foreach ($values as $key => $value) {
	    $pos = $key + 1;
		$check = '';
		if ($current == $pos) $check = ' checked="checked" ';
		echo '<input type="radio" '.$class.'name="'.$name.'" id="sfradio-'.$tab.'"  tabindex="'.$tab.'" value="'.$pos.'" '.$check.' />'."\n";
		echo '<label for="sfradio-'.$tab.'" class="wp-core-ui">'.esc_html(SP()->primitives->admin_text($value)).'</label>'."\n<br />";
		$tab++;
	}
	echo '</div>';
	echo '<div class="clearboth"></div>';
	echo '</div>';
	$tab++;
}

function spa_paint_spacer() {
	echo '<br /><div class="clearboth"></div>';
}

function spa_paint_help($name, $helpfile, $show=true) {
	$site = wp_nonce_url(SPAJAXURL."help&amp;file=$helpfile&amp;item=$name", 'help');
	$title = SP()->primitives->admin_text('Simple:Press Help');
	$out = '';

	$out.= '<div class="sfhelplink">';
	if ($show) {
		$out.= '<a id="'.$name.'" class="button-secondary sfhelplink spHelpLink" data-site="'.$site.'" data-label="'.$title.'" data-width="600" data-height="0" data-align="center">';
		$out.= SP()->primitives->admin_text('Help').'</a>';
	}
	$out.= '</div>';
	return $out;
}

/**
 * Load style and scripts for WP Code Mirror
 * 
 * @return void
 */
function spa_enqueue_codemirror() {
	if(floatval(get_bloginfo('version')) >= 4.9) {
		wp_enqueue_style( 'code-editor' );
		wp_enqueue_script( 'code-editor' );
		wp_enqueue_script( 'htmlhint' );
		wp_enqueue_script( 'csslint' );
		wp_enqueue_script( 'jshint' );
	}
}
spa_enqueue_codemirror();  // @TODO: This loads the script globally which is not really what we want - ideally this would load only when its needed.