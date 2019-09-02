<?php
/*
Simple:Press
Thanks Plugin Admin Options Form
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_thanks_admin_options_form() {
	$thanksdata = SP()->options->get('thanks');

	spa_paint_options_init();
	spa_paint_open_tab(__('Thanks/Points Plugin', 'sp-thanks'));
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Thanks Options', 'sp-thanks'), true, 'thanks-options');
				spa_paint_input(__('Thank you message before username', 'sp-thanks'), 'thank-message-before-name', SP()->displayFilters->title($thanksdata['thank-message-before-name']));
				spa_paint_input(__('Thank you message after username', 'sp-thanks'), 'thank-message-after-name', SP()->displayFilters->title($thanksdata['thank-message-after-name']));
				spa_paint_input(__('Thank you status message', 'sp-thanks'), 'thank-message-save', SP()->displayFilters->title($thanksdata['thank-message-save']));
			spa_paint_close_fieldset();
		spa_paint_close_panel();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Points System', 'sp-thanks'), true, 'thanks-points');
				spa_paint_input(__('Days/Point', 'sp-thanks'), 'points-for-day', $thanksdata['points-for-day']);
				spa_paint_input(__('Points for thanking', 'sp-thanks'), 'points-for-thank', $thanksdata['points-for-thank']);
				spa_paint_input(__('Points for being thanked', 'sp-thanks'), 'points-for-thanked', $thanksdata['points-for-thanked']);
				spa_paint_input(__('Points/Post', 'sp-thanks'), 'points-for-post', $thanksdata['points-for-post']);
			spa_paint_close_fieldset();
		spa_paint_close_panel();

	spa_paint_tab_right_cell();

		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Levels for Points System', 'sp-thanks'), true, 'thanks-levels');
				spa_paint_input(__('Level 1 name', 'sp-thanks'), 'level-1-name', SP()->displayFilters->title($thanksdata['level-1-name']));
				spa_paint_input(__('Level 1 if &lt; ? points', 'sp-thanks'), 'level-1-value', $thanksdata['level-1-value']);
				spa_paint_input(__('Level 2 name', 'sp-thanks'), 'level-2-name', SP()->displayFilters->title($thanksdata['level-2-name']));
				spa_paint_input(__('Level 2 if &lt; ? points', 'sp-thanks'), 'level-2-value', $thanksdata['level-2-value']);
				spa_paint_input(__('Level 3 name', 'sp-thanks'), 'level-3-name', SP()->displayFilters->title($thanksdata['level-3-name']));
				spa_paint_input(__('Level 3 if &lt; ? points', 'sp-thanks'), 'level-3-value', $thanksdata['level-3-value']);
				spa_paint_input(__('Level 4 name', 'sp-thanks'), 'level-4-name', SP()->displayFilters->title($thanksdata['level-4-name']));
				spa_paint_input(__('Level 4 if &lt; ? points', 'sp-thanks'), 'level-4-value', $thanksdata['level-4-value']);
				spa_paint_input(__('Level 5 name', 'sp-thanks'), 'level-5-name', SP()->displayFilters->title($thanksdata['level-5-name']));
				spa_paint_input(__('Level 5 if &lt; ? points', 'sp-thanks'), 'level-5-value', $thanksdata['level-5-value']);
				spa_paint_input(__('Level 6 name', 'sp-thanks'), 'level-6-name', SP()->displayFilters->title($thanksdata['level-6-name']));
				spa_paint_input(__('Level 6 if &lt; ? points', 'sp-thanks'), 'level-6-value', $thanksdata['level-6-value']);
				spa_paint_input(__('Level 7 name', 'sp-thanks'), 'level-7-name', SP()->displayFilters->title($thanksdata['level-7-name']));
				spa_paint_input(__('Level 7 if &gt; ? points (must be the same as level 6 value)', 'sp-thanks'), 'level-7-value', $thanksdata['level-7-value']);
			spa_paint_close_fieldset();
		spa_paint_close_panel();
		spa_paint_close_container();
}
