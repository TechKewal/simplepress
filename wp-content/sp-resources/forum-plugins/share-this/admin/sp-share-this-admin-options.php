<?php
/*
Simple:Press
Share This Plugin Admin Options Form
$LastChangedDate: 2017-12-28 11:38:04 -0600 (Thu, 28 Dec 2017) $
$Rev: 15602 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_share_this_admin_options_form() {
?>
<script>
	(function(spj, $, undefined) {
		$(document).ready(function() {
			$("#button_order").sortable({
				placeholder: 'sortable-placeholder',
				update: function () {
					$("input#button_opts").val($("#button_order").sortable('serialize'));
				}
			});
		});
	}(window.spj = window.spj || {}, jQuery));
</script>
<?php
	$options = SP()->options->get('share-this');

	spa_paint_options_init();
	spa_paint_open_tab(__('Share This Plugin', 'sp-share-this'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Share This Options', 'sp-share-this'), true, 'share-this-options');
				spa_paint_input(__('Share This publisher ID (Recommended)', 'sp-share-this'), 'publisher', $options['publisher']);
				spa_paint_checkbox(__('Use URL Shortening on shared URLs', 'sp-share-this'), 'shorten', $options['shorten']);
				spa_paint_checkbox(__('Use minor services in popup widget', 'sp-share-this'), 'minor', $options['minor']);
				spa_paint_checkbox(__('Show popup widget on hover', 'sp-share-this'), 'hover', $options['hover']);
				spa_paint_checkbox(__('Perform sharing without visiting the social network site', 'sp-share-this'), 'local', $options['local']);
				spa_paint_checkbox(__('Show text labels for small icons (if using)', 'sp-share-this'), 'labels', $options['labels']);
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
?>
	<div class="sfform-submit-bar">
	<input type="submit" class="button-primary" id="saveit1" name="saveit1" value="<?php SP()->primitives->admin_etext('Update'); ?>" />
	</div>
<?php   spa_paint_close_tab(); ?>
	<div class="sfform-panel-spacer"></div>
<?php
	spa_paint_open_tab(__('Share This Plugin', 'sp-share-this'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Share This Buttons', 'sp-share-this'), true, 'share-this-buttons');
				echo '<table>';
				echo '<tr>';
				echo '<td class="sflabel">';
				echo '<table class="form-table table-cbox">';
				echo '<tr>';
				echo '<td class="td-cbox">';
				echo '<ul id="button_order" class="menu" style="padding-top:0">';
				foreach ($options['buttons'] as $priority => $button) {
                    $class = ($button['enable']) ? '' : ' menu-item-disabled';
					echo "<li id='button_item_$priority' class='menu-item menu-item-depth-0$class' style='width:200px;margin-left:10px;margin-bottom: 5px;padding-top: 5px;padding-right:22px'>";
                    echo '<img src="'.SPSHAREIMAGES.$button['icon'].'" style="margin-bottom:-5px;" alt="" />&nbsp;&nbsp;&nbsp;<span class="item-name">'.$button['id'].'</span>';
						echo '<span class="item-controls" style="top: 5px;">';
						echo '<a class="item-edit spLayerToggle" data-target="button-edit-'.$priority.'">Edit Menu</a>';
						echo '</span>';

						echo '<div id="button-edit-'.$priority.'" class="menu-item-settings inline_edit" style="width:200px;margin-top:2px;margin-bottom:5px;border-radius: 6px 6px 6px 6px;">';
						$checked = ($button['enable']) ? $checked = 'checked="checked" ' : '';
                        echo '<input type="checkbox" '.$checked.'name="button-enable['.$priority.']" id="sf-button-display-'.$priority.'" />';
						echo '<label for="sf-button-display-'.$priority.'">'.__('Enable Icon', 'sp-share-this').'</label>';
						echo '</div>';
                    echo '</li>';
				}
				echo '</ul>';
				echo '<input type="text" class="inline_edit" size="70" id="button_opts" name="button_opts" />';
				echo '</td>';
				echo '<td class="td-cbox"><p>';
				echo __('Select the social network icon display order by dragging and dropping the buttons in the column to the left.', 'sp-share-this').' ';
                echo __('If you don\'t want to show an icon, open the button and disable the icon.', 'sp-share-this').' ';
                echo __('Other social networks will be available in the popup widget by selecting the \'more\' link.', 'sp-share-this');
				echo '</p></td>';
				echo '</tr>';
				echo '</table>';
				echo '</td>';
				echo '</tr>';
				echo '</table>';
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
?>
	<div class="sfform-submit-bar">
	<input type="submit" class="button-primary" id="saveit2" name="saveit2" value="<?php SP()->primitives->admin_etext('Update'); ?>" />
	</div>
<?php    spa_paint_close_tab(); ?>
	<div class="sfform-panel-spacer"></div>
<?php
	spa_paint_open_tab(__('Share This Plugin', 'sp-share-this'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Share This Icon Style', 'sp-share-this'), true, 'share-this-style');
?>
            <div style="margin:10px 0 50px 0;">
                <img src="<?php echo SPSHAREIMAGES.'sp_ShareThisIcons.png'; ?>" style="margin-bottom: 10px" alt="" />
                <input type="radio" name="style" id="sfshare-thisstyle1" value="1" <?php if ($options['style'] == 1) echo 'checked="checked"'; ?> />
                <label for="sfshare-thisstyle1" class="radio" style="border: none;">Icons</label>
            </div>
            <div style="margin:50px 0;">
                <img src="<?php echo SPSHAREIMAGES.'sp_ShareThisIconsSmall.png'; ?>" style="margin-bottom: 10px" alt="" />
                <input type="radio" name="style" id="sfshare-thisstyle2" value="2" <?php if ($options['style'] == 2) echo 'checked="checked"'; ?> />
                <label for="sfshare-thisstyle2" class="radio" style="border: none;">Small Icons</label>
            </div>
            <div style="margin:50px 0;">
                <img src="<?php echo SPSHAREIMAGES.'sp_ShareThisButtons.png'; ?>"  style="margin-bottom: 10px" alt="" />
                <input type="radio" name="style" id="sfshare-thisstyle3"  value="3" <?php if ($options['style'] == 3) echo 'checked="checked"'; ?> />
                <label for="sfshare-thisstyle3" class="radio" style="border: none;">Buttons</label>
            </div>
            <div style="margin:50px 0;">
                <img src="<?php echo SPSHAREIMAGES.'sp_ShareThisHorizontal.png'; ?>"  style="margin-bottom: 10px" alt="" />
                <input type="radio" name="style" id="sfshare-thisstyle4"  value="4" <?php if ($options['style'] == 4) echo 'checked="checked"'; ?> />
                <label for="sfshare-thisstyle4" class="radio" style="border: none;">Horizontal Counts</label>
            </div>
            <div style="margin:50px 0 0;">
                <img src="<?php echo SPSHAREIMAGES.'sp_ShareThisVertical.png'; ?>"  style="margin-bottom: 10px" alt="" />
                <input type="radio" name="style" id="sfshare-thisstyle5"  value="5" <?php if ($options['style'] == 5) echo 'checked="checked"'; ?> />
                <label for="sfshare-thisstyle5" class="radio" style="border: none;">Vertical Counts</label>
            </div>
<?php
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
?>
	<div class="sfform-submit-bar">
	<input type="submit" class="button-primary" id="saveit3" name="saveit3" value="<?php SP()->primitives->admin_etext('Update'); ?>" />
	</div>
<?php    spa_paint_close_tab(); ?>
	<div class="sfform-panel-spacer"></div>
<?php
	spa_paint_open_tab(__('Share This Plugin', 'sp-share-this'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Share This Widget Theme', 'sp-share-this'), true, 'share-this-theme');
?>
            <div style="margin:10px 0; float: left;">
                <img src="<?php echo SPSHAREIMAGES.'sp_ShareThisTheme2.png'; ?>" width="200" style="float:left;margin-bottom: 10px" alt="" />
                <input type="radio" name="theme" id="sfshare-thistheme2"  value="2" <?php if ($options['theme'] == 2) echo 'checked="checked"'; ?> />
                <label for="sfshare-thistheme2" class="radio" style="margin-left:20px;border: none;">Ice</label>
            </div>
            <div style="margin:10px 0; float: left;">
                <img src="<?php echo SPSHAREIMAGES.'sp_ShareThisTheme3.png'; ?>" width="200" style="float:left;margin-bottom: 10px" alt="" />
                <input type="radio" name="theme" id="sfshare-thistheme3"  value="3" <?php if ($options['theme'] == 3) echo 'checked="checked"'; ?> />
                <label for="sfshare-thistheme3" class="radio" style="margin-left:20px;border: none;">Dust</label>
            </div>
            <div style="margin:10px 0; float: left;">
                <img src="<?php echo SPSHAREIMAGES.'sp_ShareThisTheme4.png'; ?>" width="200" style="float:left;margin-bottom: 10px" alt="" />
                <input type="radio" name="theme" id="sfshare-thistheme4"  value="4" <?php if ($options['theme'] == 4) echo 'checked="checked"'; ?> />
                <label for="sfshare-thistheme4" class="radio" style="margin-left:20px;border: none;">Pine</label>
            </div>
            <div style="margin:10px 0; float: left;">
                <img src="<?php echo SPSHAREIMAGES.'sp_ShareThisTheme5.png'; ?>" width="200" style="float:left;margin-bottom: 10px" alt="" />
                <input type="radio" name="theme" id="sfshare-thistheme5"  value="5" <?php if ($options['theme'] == 5) echo 'checked="checked"'; ?> />
                <label for="sfshare-thistheme5" class="radio" style="margin-left:20px;border: none;">Da' Bears</label>
            </div>
            <div style="margin:10px 0; float: left;">
                <img src="<?php echo SPSHAREIMAGES.'sp_ShareThisTheme6.png'; ?>" width="200" style="float:left;margin-bottom: 10px" alt="" />
                <input type="radio" name="theme" id="sfshare-thistheme6"  value="6" <?php if ($options['theme'] == 6) echo 'checked="checked"'; ?> />
                <label for="sfshare-thistheme6" class="radio" style="margin-left:20px;border: none;">Cosmopolitan</label>
            </div>
            <div style="margin:10px 0; float: left;">
                <img src="<?php echo SPSHAREIMAGES.'sp_ShareThisTheme7.png'; ?>" width="200" style="float:left;margin-bottom: 10px" alt="" />
                <input type="radio" name="theme" id="sfshare-thistheme7"  value="7" <?php if ($options['theme'] == 7) echo 'checked="checked"'; ?> />
                <label for="sfshare-thistheme7" class="radio" style="margin-left:20px;border: none;">L' Orange</label>
            </div>
            <div style="margin:10px 0; float: left;">
                <img src="<?php echo SPSHAREIMAGES.'sp_ShareThisTheme8.png'; ?>" width="200" style="float:left;margin-bottom: 10px" alt="" />
                <input type="radio" name="theme" id="sfshare-thistheme8"  value="8" <?php if ($options['theme'] == 8) echo 'checked="checked"'; ?> />
                <label for="sfshare-thistheme8" class="radio" style="margin-left:20px;border: none;">Silent Movie</label>
            </div>
            <div style="margin:10px 0; float: left;">
                <img src="<?php echo SPSHAREIMAGES.'sp_ShareThisTheme1.png'; ?>" width="200" style="float:left;margin-bottom: 10px" alt="" />
                <input type="radio" name="theme" id="sfshare-thistheme1"  value="1" <?php if ($options['theme'] == 1) echo 'checked="checked"'; ?> />
                <label for="sfshare-thistheme1" class="radio" style="margin-left:20px;border: none;">Default</label>
            </div>
<?php
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
}
