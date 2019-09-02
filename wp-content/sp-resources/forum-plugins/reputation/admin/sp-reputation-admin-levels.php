 <?php
/*
Simple:Press
Reputation System plugin levels save routine
$LastChangedDate: 2017-12-28 11:38:04 -0600 (Thu, 28 Dec 2017) $
$Rev: 15602 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_reputation_do_admin_levels() {
    if (!SP()->auths->current_user_can('SPF Manage Reputation')) die();

	$ajaxurl = htmlspecialchars_decode(wp_nonce_url(SPAJAXURL.'uploader', 'uploader'));
?>
    <script>
		(function(spj, $, undefined) {
			$(document).ready(function(){
				spj.loadAjaxForm('reputationform', 'reputation-levels');

				var button = $('#sf-upload-button'), interval;
				new AjaxUpload(button,{
					action: '<?php echo $ajaxurl; ?>',
					name: 'uploadfile',
					data: {
						saveloc : '<?php echo addslashes(SP_STORE_DIR.'/'.SP()->plugin->storage['reputation'].'/'); ?>'
					},
					onSubmit : function(file, ext){
						/* check for valid extension */
						if (! (ext && /^(jpg|png|jpeg|gif|JPG|PNG|JPEG|GIF)$/.test(ext))){
							$('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__('Only JPG, PNG or GIF files are allowed!', 'sp-reputation')); ?></p>');
							return false;
						}
						/* change button text, when user selects file */
						utext = '<?php echo esc_js(__('Uploading', 'sp-reputation')); ?>';
						button.text(utext);
						/* If you want to allow uploading only 1 file at time, you can disable upload button */
						this.disable();
						/* Uploding -> Uploading. -> Uploading... */
						interval = window.setInterval(function(){
							var text = button.text();
							if (text.length < 13){
								button.text(text + '.');
							} else {
								button.text(utext);
							}
						}, 200);
					},
					onComplete: function(file, response){
						$('#sf-upload-status').html('');
						button.text('<?php echo esc_js(__('Browse', 'sp-reputation')); ?>');
						window.clearInterval(interval);
						/* re-enable upload button */
						this.enable();
						/* add file to the list */
						if (response === "success"){
							site = "<?php echo SPAJAXURL; ?>reputation-manage&amp;sfnonce=<?php echo wp_create_nonce('reputation-manage'); ?>&amp;targetaction=delbadge&amp;file=" + file;
							var count = document.getElementById('badge-count');
							var bcount = parseInt(count.value) + 1;
							$('#sf-reputaton-badges').append('<tr id="badge' + bcount + '" class="spMobileTableData"><td data-label="<?php __('Badge', 'sp-reputation'); ?>"><img class="sfrepbadge" src="<?php echo SP_STORE_URL.'/'.SP()->plugin->storage['reputation'].'/'; ?>/' + file + '" alt="" /></td><td data-label="<?php __('Filename', 'sp-reputation'); ?>">' + file + '</td><td data-label="<?php __('Remove', 'sp-reputation'); ?>"><img src="<?php echo SPCOMMONIMAGES; ?>' + 'delete.png' + '" title="<?php echo esc_js(__('Delete Reputation Badge', 'sp-reputation')); ?>" alt="" class="spDeleteRow" data-url="' + site + '" data-target="badge' + bcount + '" /></td></tr>');
							$('#sf-upload-status').html('<p class="sf-upload-status-success"><?php echo esc_js(__('Reputation badge uploaded!', 'sp-reputation')); ?></p>');
							$('.ui-tooltip').hide();
						} else if (response==="invalid"){
							$('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__('Sorry, the file has an invalid format!', 'sp-reputation')); ?></p>');
						} else if (response==="exists") {
							$('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__('Sorry, the file already exists!', 'sp-reputation')); ?></p>');
						} else {
							$('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__('Error uploading file!', 'sp-reputation')); ?></p>');
						}
					}
				});
			});
		}(window.spj = window.spj || {}, jQuery));
    </script>
<?php
    $ajaxURL = wp_nonce_url(SPAJAXURL.'plugins-loader&amp;saveform=plugin&amp;func=sp_reputation_admin_save_levels', 'plugins-loader');
?>
	<form action="<?php echo $ajaxURL; ?>" method="post" id="reputationform" name="reputationform">
	<?php echo sp_create_nonce('forum-adminform_userplugin'); ?>
<?php
	$option = SP()->options->get('reputation');

	spa_paint_options_init();
	spa_paint_open_tab(__('Reputation', 'sp-reputation').' - '.__('Levels', 'sp-reputation'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Reputation Levels', 'sp-reputation'), true, 'reputation-levels');
            	$reputation_levels = SP()->meta->get('reputation level');
            	if ($reputation_levels) {
                    $levels = array();
            		foreach ($reputation_levels as $x => $level) {
            			$levels['id'][$x] = $level['meta_id'];
            			$levels['name'][$x] = $level['meta_key'];
            			$levels['points'][$x] = $level['meta_value']['points'];
            			$levels['maxgive'][$x] = $level['meta_value']['maxgive'];
            			$levels['maxday'][$x] = $level['meta_value']['maxday'];
            			$levels['badge'][$x] = (!empty($level['meta_value']['badge'])) ? $level['meta_value']['badge'] : '';
            		}
            		array_multisort($levels['points'], SORT_ASC, $levels['name'], $levels['maxgive'], $levels['maxday'], $levels['badge'], $levels['id']);
?>
                    <table class="widefat fixed striped spMobileTable800">
                        <thead>
                            <tr>
                                <th style='text-align:center'><?php echo __('Level Name', 'sp-reputation'); ?></th>
                                <th style='text-align:center'><?php echo __('Max Points', 'sp-reputation'); ?></th>
                                <th style='text-align:center'><?php echo __('Max Give/Take', 'sp-reputation'); ?></th>
                                <th style='text-align:center'><?php echo __('Max Daily', 'sp-reputation'); ?></th>
                                <th style='text-align:center'><?php echo __('Badge', 'sp-reputation'); ?></th>
                                <th style='text-align:center'><?php echo __('Manage', 'sp-reputation'); ?></th>
                            </tr>
                        </thead>

                        <tbody>
<?php
                            for ($x = 0; $x < count($reputation_levels); $x++) {
?>
                        		<tr id="level<?php echo $x; ?>" class="spMobileTableData">
                                    <td data-label='<?php echo __('Level Name', 'sp-reputation'); ?>' style='text-align:center'>
                            			<input type='text' size="12" class='wp-core-ui' name='levelname[]' value='<?php echo esc_attr($levels['name'][$x]); ?>' />
                            			<input type='hidden' name='levelid[]' value='<?php echo esc_attr($levels['id'][$x]); ?>' />
                                    </td>
                                    <td data-label='<?php echo __('Max Points', 'sp-reputation'); ?>' >
                            			<input type='text' size="12" class='wp-core-ui' name='levelpoints[]' value='<?php echo esc_attr($levels['points'][$x]); ?>' />
                                    </td>
                                    <td data-label='<?php echo __('Max Give/Take', 'sp-reputation'); ?>' style='text-align:center'>
                            			<input type='text' size="12" class='wp-core-ui' name='levelmax[]' value='<?php echo esc_attr($levels['maxgive'][$x]); ?>' />
                                    </td>
                                    <td data-label='<?php echo __('Max Daily', 'sp-reputation'); ?>' style='text-align:center'>
                            			<input type='text' size="12" class='wp-core-ui' name='leveldaily[]' value='<?php echo esc_attr($levels['maxday'][$x]); ?>' />
                                    </td>
                                    <td data-label='<?php echo __('Badge', 'sp-reputation'); ?>' style='text-align:center'>
                                        <?php spa_select_icon_dropdown('levelbadge[]', __('Select Badge', 'sp-reputation'), SP_STORE_DIR.'/'.SP()->plugin->storage['reputation'].'/', $levels['badge'][$x], true, 175); ?>
                                    </td>
                                    <td data-label='<?php echo __('Manage', 'sp-reputation'); ?>' style='text-align:center'>
                                        <?php $site = wp_nonce_url(SPAJAXURL.'reputation-manage&amp;targetaction=del_level&amp;id='.$levels['id'][$x], 'reputation-manage'); ?>
                                        <img class="spDeleteRow" data-url="<?php echo $site; ?>" data-target="level<?php echo $x; ?>" src="<?php echo SPCOMMONIMAGES; ?>delete.png" title="<?php __('Delete Level', 'sp-reputation'); ?>" alt="" />
                                    </td>
                                </tr>
<?php
                            }
?>
                    		<!--empty row for new level-->
                    		<tr class="spMobileTableData">
                        		<td data-label='<?php __('Level Name', 'sp-reputation'); ?>'>
                        			<input type='text' size="12"  class='wp-core-ui' name='levelname[]' value='' />
                        			<input type='hidden' name='levelid[]' value='-1' />
                        		</td>
                        		<td data-label='<?php __('Max Points For Level', 'sp-reputation'); ?>'>
                        			<input type='text' class='wp-core-ui' size='5' name='levelpoints[]' value='' />
                        		</td>
                        		<td data-label='<?php __('Max Give/Take', 'sp-reputation'); ?>'>
                        			<input type='text' class='wp-core-ui' size='5' name='levelmax[]' value='' />
                        		</td>
                        		<td data-label='<?php __('Max Daily', 'sp-reputation'); ?>'>
                        			<input type='text' class='wp-core-ui' size='5' name='leveldaily[]' value='' />
                        		</td>
                        		<td data-label='<?php __('Badge', 'sp-reputation'); ?>'>
                                    <?php spa_select_icon_dropdown('levelbadge[]', __('Select Badge', 'sp-reputation'), SP_STORE_DIR.'/'.SP()->plugin->storage['reputation'].'/', '', true, 175); ?>
                        		</td>
                        		<td></td>
                    		</tr>
                        </tbody>
                    </table>
<?php
            	} else {
                    echo __('No reputation levels found', 'sp-reputation');
            	}
			spa_paint_close_fieldset();
		spa_paint_close_panel();
	spa_paint_close_container();
?>
	<div class="sfform-submit-bar">
	<input type="submit" class="button button-primary" id="reputationform" name="reputationform" value="<?php echo __('Update Reputation Levels', 'sp-reputation'); ?>" />
	</div>
<?php
	spa_paint_close_tab();
    echo '<div class="sfform-panel-spacer"></div>';

	spa_paint_open_tab(__('Reputation', 'sp-reputation').' - '.__('Badges', 'sp-reputation'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Reputation badge upload', 'sp-reputation'), true, 'reputation-upload');
				$loc = SP_STORE_DIR.'/'.SP()->plugin->storage['reputation'].'/';
				spa_paint_file(__('Select reputation level badge to upload', 'sp-reputation'), 'newlevelbadge', false, true, $loc);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Reputation Badges', 'sp-reputation'), true, 'reputation-badges');
                spa_paint_reputation_badges();
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_close_container();
	   echo '<div class="sfform-panel-spacer"></div>';
	spa_paint_close_tab();
}

function spa_paint_reputation_badges() {
	# Open badges folder and get cntents for matching
	$path = SP_STORE_DIR.'/'.SP()->plugin->storage['reputation'].'/';
	$dlist = @opendir($path);
	if (!$dlist) {
		echo '<table><tr><td class="sflabel"><strong>'.__('The reputation badges folder does not exist', 'sp-reputation').'</strong></td></tr></table>';
		return;
	}

	# start the table display
?>
	<table id="sf-reputaton-badges" class="widefat fixed striped spMobileTable800">
		<thead>
			<tr>
				<th style='text-align:center'><?php echo __('Badge', 'sp-reputation'); ?></th>
				<th style='text-align:center'><?php echo __('Filename', 'sp-reputation'); ?></th>
				<th style='text-align:center'><?php echo __('Remove', 'sp-reputation'); ?></th>
			</tr>
		</thead>
		<tbody>
<?php
    $row = 0;
	while (false !== ($file = readdir($dlist))) {
		$path_info = pathinfo($path.$file);
		$ext = strtolower($path_info['extension']);
		if (($file != '.' && $file != '..') && ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif' || $ext == 'bmp')) {
			$found = false;
?>
			<tr id="badge<?php echo $row; ?>" class="spMobileTableData">
				<td data-label='<?php echo __('Badge', 'sp-reputation'); ?>'>
					<img class="sfreputattionbadge" src="<?php echo esc_url(SP_STORE_URL.'/'.SP()->plugin->storage['reputation'].'/'.$file); ?>" alt="" />
				</td>
				<td data-label='<?php echo __('Filename', 'sp-reputation'); ?>'>
					<?php echo $file; ?>
				</td>
				<td data-label='<?php echo __('Remove', 'sp-reputation'); ?>'>
<?php
					$site = esc_url(wp_nonce_url(SPAJAXURL."reputation-manage&amp;targetaction=delbadge&amp;file=$file", 'reputation-manage'));
					echo '<img src="'.SPCOMMONIMAGES.'delete.png" title="'.__('Delete Reputation Badge', 'sp-reputation').'" alt="" class="spDeleteRow" data-url="'.$site.'" data-target="badge'.$row.'" />';
?>
				</td>
			</tr>
<?php
            $row++;
		}
	}
	echo '</table>';
  	echo '<input type="hidden" id="badge-count" name="badge-count" value="'.$row.'" />';
	closedir($dlist);
}
