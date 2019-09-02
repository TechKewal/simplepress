<?php
/*
Simple:Press
Identities Plugin Admin Form
$LastChangedDate: 2017-12-28 11:38:04 -0600 (Thu, 28 Dec 2017) $
$Rev: 15602 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_identities_admin_form() {
	$sfconfig = SP()->options->get('sfconfig');
    $loc = SP_STORE_DIR.'/'.$sfconfig['identities'].'/';
    $url = SP_STORE_URL.'/'.$sfconfig['identities'].'/';
	$ajaxurl = htmlspecialchars_decode(wp_nonce_url(SPAJAXURL.'uploader', 'uploader'));
?>
<script>
	(function(spj, $, undefined) {
		$(document).ready(function(){
			spj.loadAjaxForm('spIdentities', 'spident');

			var button = $('#sf-upload-button'), interval;
			new AjaxUpload(button,{
				action: '<?php echo $ajaxurl; ?>',
				name: 'uploadfile',
				data: {
					saveloc : '<?php echo addslashes($loc); ?>'
				},
				onSubmit : function(file, ext){
					/* check for valid extension */
					if (! (ext && /^(jpg|png|jpeg|gif|JPG|PNG|JPEG|GIF)$/.test(ext))){
						$('#sf-upload-status').html('<p class="sf-upload-status-text"><?php echo esc_js(__('Only JPG, PNG or GIF files are allowed!', 'sp-identities')); ?></p>');
						return false;
					}
					/* change button text, when user selects file */
					utext = '<?php echo esc_js(__('Uploading', 'sp-identities')); ?>';
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
					button.text('<?php echo esc_js(__('Browse', 'sp-identities')); ?>');
					window.clearInterval(interval);
					/* re-enable upload button */
					this.enable();
					/* add file to the list */
					if (response === "success"){
						msg = "<?php echo esc_attr(__('Are you sure you want to delete this identity?'), 'sp-identities'); ?>";
						site = "<?php echo SPAJAXURL; ?>identities-admin&amp;sfnonce=<?php echo wp_create_nonce('identities-admin'); ?>&amp;targetaction=delete-identity&amp;file=" + file;
						var count = document.getElementById('identity-count');
						var icount = parseInt(count.value) + 1;
						$('#sp-identities-table').append('<tr id="sp-identity-' + icount + '" class="spMobileTableData"><td data-label="<?php echo __('Icon', 'sp-identities'); ?>" style="text-align:center"><img class="spIdentity" src="<?php echo $url; ?>' + file + '" alt="" /></td><td data-label="<?php echo __('File', 'sp-identities'); ?>" class="sflabel" style="text-align:center"><input type="hidden" name="idfile[]" value="' + file + '" />' + file + '</td><td data-label="<?php echo __('Name', 'sp-identities'); ?>" style="text-align:center"><input type="text" name="idname[]" value="" /></td><td data-label="<?php echo __('Base URL', 'sp-identities'); ?>" style="text-align:center"><input type="text" name="idurl[]" value="" /></td><td data-label="<?php echo __('Manage', 'sp-identities'); ?>" class="sflabel" style="text-align:center"></td></tr>');
						$('#identity-count').val(icount);
						$('#sf-upload-status').html('<p class="sf-upload-status-success"><?php echo esc_js(__('Identity Icon uploaded!', 'sp-identities')); ?></p>');
					} else if (response==="invalid"){
						$('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__('Sorry, the file has an invalid format!', 'sp-identities')); ?></p>');
					} else if (response==="exists") {
						$('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__('Sorry, the file already exists!', 'sp-identities')); ?></p>');
					} else {
						$('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__('Error uploading file!', 'sp-identities')); ?></p>');
					}
				}
			});
		});
	}(window.spj = window.spj || {}, jQuery));
</script>
<?php
    $ajaxURL = wp_nonce_url(SPAJAXURL.'plugins-loader&amp;saveform=plugin&amp;func=sp_identities_update', 'plugins-loader');
?>
	<form action="<?php echo $ajaxURL; ?>" method="post" id="spIdentities" name="spIdentities" enctype="multipart/form-data">
<?php
   	echo sp_create_nonce('forum-adminform_userplugin');

	spa_paint_options_init();
	spa_paint_open_tab(__('Profiles', 'sp-identities').' - '.__('User Identities', 'sp-identities'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('User Identity Icon Upload', 'sp-identities'), true, 'identity-upload');
				spa_paint_file(__('Select user identity icon to upload', 'sp-identities'), 'newidentityicon', false, true, $loc);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Add User Identities', 'sp-identities'), true, 'identities');

        	# verify storage location
        	$dlist = @opendir($loc);
        	if (!$dlist) {
                echo '<p>'.__('The identities storage folder does not exist. Please create or config on forum - integration - storage locations', 'sp-identities').'</p>';
                return;
            }
?>
                <table id="sp-identities-table" class="widefat fixed striped spMobileTable800">
                    <thead>
                        <tr>
                            <th style='text-align:center'><?php echo __('Identity Icon', 'sp-identities'); ?></th>
                            <th style='text-align:center'><?php echo __('File', 'sp-identities'); ?></th>
                            <th style='text-align:center'><?php echo __('Name', 'sp-identities'); ?></th>
                            <th style='text-align:center'><?php echo __('Base URL', 'sp-identities'); ?></th>
                            <th style='text-align:center'><?php echo __('Manage', 'sp-identities'); ?></th>
                        </tr>
                    </thead>

                    <tbody>
<?php
                $count = 0;
            	$identities = SP()->meta->get('user_identities', 'user_identities');
                if ($identities) {
                    foreach ($identities[0]['meta_value'] as $identity) {
                        $count++;
?>
                        <tr id='sp-identity-<?php echo $count; ?>' class='spMobileTableData'>
                            <td data-label='<?php echo __('Icon', 'sp-identities'); ?>' class="sflabel" style='text-align:center'>
                                <img class="spIdentity" src="<?php echo $url.$identity['file']; ?>" alt="" />
                            </td>
                            <td data-label='<?php echo __('File', 'sp-identities'); ?>' class="sflabel" style='text-align:center'>
                                <input type="hidden" name="idfile[]" value="<?php echo $identity['file']; ?>" />
                                <?php echo $identity['file']; ?>
                            </td>
                            <td data-label='<?php echo __('Name', 'sp-identities'); ?>' class="sflabel" style='text-align:center'>
                                <input type="text" name="idname[]" value="<?php echo $identity['name']; ?>" />
                            </td>
                            <td data-label='<?php echo __('Base URL', 'sp-identities'); ?>' style='text-align:center'>
                                <input type="text" name="idurl[]" value="<?php echo $identity['base_url']; ?>" />
                            </td>
                            <td data-label='<?php echo __('Manage', 'sp-identities'); ?>' class="sflabel" style='text-align:center'>
                                <?php
                                    $msg = esc_attr(__('Are you sure you want to delete this identity?'), 'sp-identities');
                                    $site = wp_nonce_url(SPAJAXURL.'identities-admin&amp;targetaction=delete-identity&amp;file='.$identity['file'], 'identities-admin');
                                ?>
                                <img src="<?php echo SPIDENTIMAGES; ?>sp_IdentityDelete.png" title="<?php _e('Delete Identity', 'sp-identities'); ?>" class="spDeleteRow" data-msg="<?php echo $msg; ?>" data-url="<?php echo $site; ?>" data-target="sp-identity-<?php echo $count; ?>" alt="" />
                            </td>
                        </tr>
<?php
                    }
                }
?>
                </tbody>
                </table>
<?php

            	echo '<input type="hidden" id="identity-count" name="identity-count" value="'.$count.'" />';
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
?>
	<div class="sfform-submit-bar">
	<input type="submit" class="button-primary" id="updateidentities" name="saveit" value="<?php echo __('Update Identities', 'sp-identites'); ?>" />
	</div>
<?php
	spa_paint_close_tab();
?>
	</form>
<?php
}
