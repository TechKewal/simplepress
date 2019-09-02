<?php
/*
Simple:Press
Subscriptions plugin admin topics routine
$LastChangedDate: 2018-10-24 06:19:24 -0500 (Wed, 24 Oct 2018) $
$Rev: 15767 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_subscriptions_admin_topics_form() {
    $site = wp_nonce_url(SPAJAXURL.'subs-topics&amp;targetaction=topiclist&amp;page=1', 'subs-topics');
	$gif = SPCOMMONIMAGES.'working.gif';
	echo '<form action="'.SPADMINUSER.'" method="post" name="sptopicsubs" id="sptopicsubs" data-target="sptopicsubs" data-site="'.$site.'" data-img="'.$gif.'" >';
		spa_paint_options_init();
		spa_paint_open_tab(__('Users', 'sp-subs').' - '.__('Topic Subscriptions By Topic', 'sp-subs'), true);
			spa_paint_open_panel();
				spa_paint_open_fieldset(__('Topic Subscriptions', 'sp-subs'), 'true', 'subscriptions-topics');
					spa_paint_open_fieldset(__('Select filters', 'sp-subs'), false);
                        echo '<div>';
						_e('Filter by all, groups or forums', 'sp-subs');
						if (isset($_POST['subsfilter'])) $filter = SP()->filters->str($_POST['subsfilter']);
						if (!isset($filter)) $filter = 'All';
						$check = '';
						if ($filter == 'All') $check = ' checked="checked"';
						echo '<div><input type="radio" id="sffilterall" name="subsfilter" value="All"'.$check.' />';
						echo '<label class="sfradio" for="sffilterall">&nbsp;'.__('All', 'sp-subs').'</label></div>';
						$check = '';
						if ($filter == 'Groups') $check = ' checked="checked"';
						$site = wp_nonce_url(SPAJAXURL.'subs-topics&amp;targetaction=display-groups', 'subs-topics');
						echo '<div style="clear:left"><input type="radio" id="sffiltergroups" name="subsfilter" value="Groups"'.$check.' data-site="'.$site.'" data-img="'.$gif.'" />';
						echo '<label class="sfradio" for="sffiltergroups">&nbsp;'.__('Groups', 'sp-subs').'</label></div>';
						$check = '';
						if ($filter == 'Forums') $check = ' checked="checked"';
						$site = wp_nonce_url(SPAJAXURL.'subs-topics&amp;targetaction=display-forums', 'subs-topics');
						echo '<div style="clear:left"><input type="radio" id="sffilterforums" name="subsfilter" value="Forums"'.$check.' data-site="'.$site.'" data-img="'.$gif.'" />';
						echo '<label class="sfradio" for="sffilterforums">&nbsp;'.__('Forums', 'sp-subs').'</label></div>';
						echo '</div>';
						echo '<div class="clearboth"></div>';

						echo '<div id="sub-select-group" class="inline_edit">';
						echo '<p>'.__('Select groups', 'sp-subs').'</p>';
						echo '<div id="selectgroup"></div>';
						echo '<div class="clearboth"></div>';
						echo '</div>';

						echo '<div id="sub-select-forum" class="inline_edit">';
						echo '<p>'.__("Select forums", "sp-subs").'</p>';
						echo '<div id="selectforum"></div>';
						echo '<div class="clearboth"></div>';
						echo '</div>';
					spa_paint_close_fieldset();
				spa_paint_close_fieldset();
			spa_paint_close_panel();
		spa_paint_close_container();

        $site = wp_nonce_url(SPAJAXURL.'subs-topics&amp;targetaction=topiclist&amp;page=1', 'subs-topics');
?>
		<input type="hidden" class="sfhiddeninput" name="topicsubs" value="submit" />
		<div class="sfform-submit-bar">
			<input type="button" class="button-primary spSubsShowSubs" value="<?php esc_attr_e(__('Show Subscriptions', 'sp-subs')); ?>" data-target="sptopicsubs" data-site="<?php echo $site; ?>" data-img="<?php echo $gif; ?>" />
		</div>
<?php	spa_paint_close_tab(); ?>
		<div class="sfform-panel-spacer"></div>
		<div id="subsdisplayspot"></div>
	</form>
<?php
}
