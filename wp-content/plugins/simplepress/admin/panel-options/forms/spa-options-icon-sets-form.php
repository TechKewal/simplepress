<?php
/*
Simple:Press
Admin Options Global Display Form
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');


/**
 * Print iconsets listing and upload form
 */
function spa_options_iconsets_form() {
	$ajaxurl = htmlspecialchars_decode(wp_nonce_url(SPAJAXURL.'iconset_uploader', 'uploader'));
?>
<script>
	(function(spj, $, undefined) {
		$(document).ready(function(){
			
			/**
			 * Handle enable/disable iconset
			 */
			$('#sfmaincontainer').off('click', '.spToggleRowReload');
			$('#sfmaincontainer').on('click', '.spToggleRowReload', function() {
				var data = $(this).data();
				
				$('#sfmsgspot').load(data.url, function() {
					$('.ui-tooltip').hide();
					$('#' + data.reload).click();
				});
				
			});
			
			
			
			
			spj.loadAjaxForm('sficonsetsform', 'acciconsets');

			// Handle iconset upload
			var button = $('#sf-upload-button'), interval;
			new AjaxUpload(button,{
				action: '<?php echo $ajaxurl; ?>',
				name: 'uploadfile',
				onSubmit : function(file, ext){
					/* check for valid extension */
					if (! (ext && /^(zip)$/.test(ext))){
						$('#sf-upload-status').html('<p class="sf-upload-status-text"><?php echo esc_js(SP()->primitives->admin_text('Only Zip files are allowed!')); ?></p>');
						return false;
					}
					/* change button text, when user selects file */
					utext = '<?php echo esc_js(SP()->primitives->admin_text('Uploading')); ?>';
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
					button.text('<?php echo esc_js(SP()->primitives->admin_text('Browse')); ?>');
					window.clearInterval(interval);
					/* re-enable upload button */
					this.enable();
					/* add file to the list */
					if (response==="success"){
						$('#sfmsgspot').html('<p class="sf-upload-status-success"><?php echo esc_js(SP()->primitives->admin_text('Iconset uploaded!')); ?></p>');
						$('#sfmsgspot').fadeIn();
						$('#sfmsgspot').fadeOut(6000);
						$('#acciconsets').click();
					} else if (response==="invalid"){
						$('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(SP()->primitives->admin_text('Sorry, the file has an invalid format!')); ?></p>');
					} else if (response==="exists") {
						$('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(SP()->primitives->admin_text('Sorry, the file already exists!')); ?></p>');
					} else {
						$('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(SP()->primitives->admin_text('Error uploading file!')); ?></p>');
					}
				}
			});
		});
	}(window.spj = window.spj || {}, jQuery));
</script>
<?php
	$ajaxURL = wp_nonce_url(SPAJAXURL.'options-loader&amp;saveform=iconsets', 'options-loader');
?>
	<form action="<?php echo $ajaxURL; ?>" method="post" id="sficonsetsform" name="sficonsets" enctype="multipart/form-data">
	<?php echo sp_create_nonce('forum-adminform_iconsets'); ?>
<?php
	spa_paint_options_init();

	#== Iconsets Tab ============================================================

	spa_paint_open_tab(SP()->primitives->admin_text('Options').' - '.SP()->primitives->admin_text('Iconsets'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(SP()->primitives->admin_text('Custom Iconset Upload'), true, 'iconset-upload');
				$loc = SP_STORE_DIR.'/'.SP()->plugin->storage['iconsets'].'/';
				spa_paint_file(SP()->primitives->admin_text('Select iconset zip file to upload'), 'iconset', false, true, $loc);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_close_container();
		echo '<div class="sfform-panel-spacer"></div>';
	spa_paint_close_tab();

	spa_paint_open_nohead_tab(true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(SP()->primitives->admin_text('Installed Iconsets'), true, 'custom-iconsets');
				spa_paint_iconsets_table();
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_close_container();
?>
	<div class="sfform-submit-bar">
	<input type="submit" class="button-primary" id="updateiconsets" name="saveit" value="<?php SP()->primitives->admin_etext('Update Iconsets Options'); ?>" />
	</div>
<?php
	spa_paint_close_tab();
?>
	</form>
<?php
}