 <?php
/*
Simple:Press
Reputation System plugin options save routine
$LastChangedDate: 2018-08-05 11:53:08 -0500 (Sun, 05 Aug 2018) $
$Rev: 15686 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_reputation_do_admin_options() {
    if (!SP()->auths->current_user_can('SPF Manage Reputation')) die();

	$option = SP()->options->get('reputation');

	spa_paint_options_init();
	spa_paint_open_tab(__('Reputation System', 'sp-reputation').' - '.__('Options', 'sp-reputation'));
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Reputation Options', 'sp-reputation'), true, 'reputation-options');
				spa_paint_input(__('Default reputation for new users', 'sp-reputation'), 'defrep', $option['defrep']);
				spa_paint_input(__('Users gain 10 reputation points for every X days registered (0 to disable)', 'sp-reputation'), 'regrep', $option['regrep']);
				spa_paint_input(__('Users gain 10 reputation points for every X posts made (0 to disable)', 'sp-reputation'), 'postrep', $option['postrep']);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Highlight Options', 'sp-reputation'), true, 'highlight-options');
				spa_paint_checkbox(__('Add highlight color to posts from high reputation users', 'sp-reputation'), 'highlight', $option['highlight']);
				spa_paint_input(__('CSS RGB color (without #) to highlight posts from high reputation users', 'sp-reputation'), 'highlightcss', $option['highlightcss']);
				spa_paint_input(__('Reputation value at which to highlight posts from high reputation users', 'sp-reputation'), 'highlightrep', $option['highlightrep']);
			spa_paint_close_fieldset();

			spa_paint_open_fieldset(__('Lowlight Options', 'sp-reputation'), true, 'lowlight-options');
				spa_paint_checkbox(__('Add highlight color to posts from low reputation users', 'sp-reputation'), 'lowlight', $option['lowlight']);
				spa_paint_input(__('CSS RGB color (without #) to highlight posts from low reputation users', 'sp-reputation'), 'lowlightcss', $option['lowlightcss']);
				spa_paint_input(__('Reputation value at which to highlight posts from low reputation users', 'sp-reputation'), 'lowlightrep', $option['lowlightrep']);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

    	spa_paint_tab_right_cell();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Reputation Strings', 'sp-reputation'), true, 'reputation-strings');
				$submessage = SP()->displayFilters->title($option['popupheader']);
				spa_paint_wide_textarea(__('Reputation popup header string', 'sp-reputation'), 'popupheader', $submessage);
				$submessage = SP()->displayFilters->title($option['popupgive']);
				spa_paint_wide_textarea(__('Reputation popup "give reputation" string', 'sp-reputation'), 'popupgive', $submessage);
				$submessage = SP()->displayFilters->title($option['popuptake']);
				spa_paint_wide_textarea(__('Reputation popup "take reputation" string', 'sp-reputation'), 'popuptake', $submessage);
				$submessage = SP()->displayFilters->title($option['popupamount']);
				spa_paint_wide_textarea(__('Reputation popup "amount of reputation" string', 'sp-reputation'), 'popupamount', $submessage);
				$submessage = SP()->displayFilters->title($option['popupsubmit']);
				spa_paint_wide_textarea(__('Reputation popup submit button string', 'sp-reputation'), 'popupsubmit', $submessage);
				$submessage = SP()->displayFilters->title($option['popupinvalid']);
				spa_paint_wide_textarea(__('Reputation popup "invalid input" string', 'sp-reputation'), 'popupinvalid', $submessage);
				$submessage = SP()->displayFilters->title($option['popupzero']);
				spa_paint_wide_textarea(__('Reputation popup "cannot give 0" string', 'sp-reputation'), 'popupzero', $submessage);
				$submessage = SP()->displayFilters->title($option['popuppositive']);
				spa_paint_wide_textarea(__('Reputation popup "must be postitive" string', 'sp-reputation'), 'popuppositive', $submessage);
				$submessage = SP()->displayFilters->title($option['popupmax']);
				spa_paint_wide_textarea(__('Reputation popup "exceeding max give" string', 'sp-reputation'), 'popupmax', $submessage);
				$submessage = SP()->displayFilters->title($option['popupupdated']);
				spa_paint_wide_textarea(__('Reputation popup "reputation updated" string', 'sp-reputation'), 'popupupdated', $submessage);
				$submessage = SP()->displayFilters->title($option['popupwrong']);
				spa_paint_wide_textarea(__('Reputation popup "something went wrong" string', 'sp-reputation'), 'popupwrong', $submessage);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

	spa_paint_close_container();
}
