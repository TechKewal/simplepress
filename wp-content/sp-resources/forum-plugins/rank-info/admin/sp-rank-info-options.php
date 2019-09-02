<?php
/*
Simple:Press
Ranks Info Plugin Admin Form
$LastChangedDate: 2017-12-28 11:38:04 -0600 (Thu, 28 Dec 2017) $
$Rev: 15602 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_rank_info_options_form() {
    $options = SP()->options->get('rank-info');

    $ajaxURL = wp_nonce_url(SPAJAXURL.'plugins-loader&amp;saveform=plugin&amp;func=sp_rank_info_options_save', 'plugins-loader');
?>
	<div class="sfform-panel-spacer"></div>

<script>
	(function(spj, $, undefined) {
		spj.loadAjaxForm('spRankInfo', 'sfreloadfr');
	}(window.spj = window.spj || {}, jQuery));
</script>

	<form action="<?php echo $ajaxURL; ?>" method="post" id="spRankInfo" name="spRankInfo">
<?php
   	echo sp_create_nonce('forum-adminform_userplugin');

	spa_paint_open_tab(SP()->primitives->admin_text('Components')." - ".SP()->primitives->admin_text('Forum Ranks'), true);
    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Rank Info Options', 'sp-rank-info'), 'true', 'rank-info-options');
    			spa_paint_checkbox(__('Display auto membership info for ranks', 'sp-rank-info'), 'membership', $options['membership']);
    			spa_paint_checkbox(__('Display badge with info for ranks', 'sp-rank-info'), 'badge', $options['badge']);
    			spa_paint_checkbox(__('Display members of each rank', 'sp-rank-info'), 'users', $options['users']);
    			spa_paint_checkbox(__('Display members of same rank only', 'sp-rank-info'), 'same_rank', $options['same_rank']);
    			spa_paint_checkbox(__('Display special ranks info', 'sp-rank-info'), 'special_ranks', $options['special_ranks']);
    			spa_paint_checkbox(__('Display members of each special rank (if displaying special ranks)', 'sp-rank-info'), 'special_users', $options['special_users']);
    			spa_paint_checkbox(__('Display members of same special rank only (if displaying special ranks)', 'sp-rank-info'), 'same_special_rank', $options['same_special_rank']);
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();
		spa_paint_close_container();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="button-primary" id="updaterankinfo" name="saveit" value="<?php SP()->primitives->admin_etext('Update Rank Info Options'); ?>" />
	</div>
<?php
	spa_paint_close_tab();
?>
	</form>
<?php
}
