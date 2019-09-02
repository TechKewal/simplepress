<?php
/*
Simple:Press
Watches plugin admin user watches routine
$LastChangedDate: 2018-10-24 06:19:24 -0500 (Wed, 24 Oct 2018) $
$Rev: 15767 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_watches_admin_topics_form() {
    $site = wp_nonce_url(SPAJAXURL.'watches-topics&amp;targetaction=topiclist&amp;page=1', 'watches-topics');
	$gif = SPCOMMONIMAGES.'working.gif';
	echo '<form action="'.SPADMINUSER.'" method="post" name="sptopicwatches" id="sptopicwatches" data-target="sptopicwatches" data-site="'.$site.'" data-img="'.$gif.'">';
		spa_paint_options_init();
		spa_paint_open_tab(__('Users', 'sp-watches').' - '.__('Topic Watches', 'sp-watches'), true);
			spa_paint_open_panel();
				spa_paint_open_fieldset(__('Topic Watches', 'sp-watches'), 'true', 'watches-topics');
					spa_paint_open_fieldset(__('Select filters', 'sp-watches'), false);
                        echo '<div>';
							_e('Filter by all, groups or forums', 'sp-watches');
							if (isset($_POST['watchesfilter'])) $filter = SP()->filters->str($_POST['watchesfilter']);
							if (!isset($filter)) $filter = 'All';
							$check = '';
							if ($filter == 'All') $check = ' checked="checked"';
							echo '<div><input type="radio" id="sffilterall" name="watchesfilter" value="All"'.$check.' />';
							echo '<label class="sfradio" for="sffilterall">&nbsp;'.__('All', 'sp-watches').'</label></div>';
							$check = '';
							if ($filter == 'Groups') $check = ' checked="checked"';
							$site = wp_nonce_url(SPAJAXURL.'watches-topics&amp;targetaction=display-groups', 'watches-topics');
							$gif = SPCOMMONIMAGES.'working.gif';
							echo '<div style="clear:left"><input type="radio" id="sffiltergroups" name="watchesfilter" value="Groups"'.$check.' data-site="'.$site.'" data-img="'.$gif.'" />';
							echo '<label class="sfradio" for="sffiltergroups">&nbsp;'.__('Groups', 'sp-watches').'</label></div>';
							$check = '';
							if ($filter == 'Forums') $check = ' checked="checked"';
							$site = wp_nonce_url(SPAJAXURL.'watches-topics&amp;targetaction=display-forums', 'watches-topics');
							$gif = SPCOMMONIMAGES.'working.gif';
							echo '<div style="clear:left"><input type="radio" id="sffilterforums" name="watchesfilter" value="Forums"'.$check.' data-site="'.$site.'" data-img="'.$gif.'" />';
							echo '<label class="sfradio" for="sffilterforums">&nbsp;'.__('Forums', 'sp-watches').'</label></div>';
						echo '</div>';
						echo '<div class="clearboth"></div>';

						echo '<div id="select-group" class="inline_edit">';
							echo '<p>'.__('Select groups', 'sp-watches').'</p>';
							echo '<div id="selectgroup"></div>';
							echo '<div class="clearboth"></div>';
							echo '</div>';

						echo '<div id="select-forum" class="inline_edit">';
							echo '<p>'.__('Select forums', 'sp-watches').'</p>';
							echo '<div id="selectforum"></div>';
							echo '<div class="clearboth"></div>';
							echo '</div>';
					spa_paint_close_fieldset();
				spa_paint_close_fieldset();
			spa_paint_close_panel();
		spa_paint_close_container();

        $site = wp_nonce_url(SPAJAXURL.'watches-topics&amp;targetaction=topiclist&amp;page=1', 'watches-topics');
	?>
		<input type="hidden" class="sfhiddeninput" name="topicwatches" value="submit" />
		<div class="sfform-submit-bar">
			<input type="button" class="button-primary spWatchesShowWatches" value="<?php esc_attr_e(__('Show Watches', 'sp-watches')); ?>" data-target="sptopicwatches" data-site="<?php echo $site; ?>" data-img="<?php echo $gif; ?>" />
		</div>
<?php	spa_paint_close_tab(); ?>
		<div class="sfform-panel-spacer"></div>
		<div id="watchesdisplayspot"></div>
	</form>
<?php
}
