<?php
/*
Simple:Press
Polls Plugin Admin Options Form
$LastChangedDate: 2018-11-13 20:42:11 -0600 (Tue, 13 Nov 2018) $
$Rev: 15818 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_polls_admin_manage_form() {
    global $wpdb;

    if (!SP()->auths->current_user_can('SPF Manage Polls')) die();

	spa_paint_options_init();
	spa_paint_open_tab(__('Polls', 'sp-polls').' - '.__('Poll Stats', 'sp-polls'), true);
    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Stats', 'sp-polls'), false);
            	$stats = SP()->DB->select('SELECT COUNT(*) AS count, SUM(poll_active) AS active, SUM(poll_votes) as votes, SUM(poll_voters) as voters FROM '.SPPOLLS, 'row');
?>
                <table class="widefat fiexed striped">
                    <tr>
                        <td><b><?php echo __('Number of polls', 'sp-polls'); ?>:</b></td>
                        <td><?php echo $stats->count; ?></td>
                    </tr>
                    <tr>
                        <td><b><?php echo __('Number of active polls', 'sp-polls'); ?>:</b></td>
                        <td><?php echo $stats->active ?></td>
                    </tr>
                    <tr>
                        <td><b><?php echo __('Number votes cast', 'sp-polls'); ?>:</b></td>
                        <td><?php echo $stats->votes; ?></td>
                    </tr>
                    <tr>
                        <td><b><?php echo __('Number of voters', 'sp-polls'); ?>:</b></td>
                        <td><?php echo $stats->voters ?></td>
                    </tr>
                </table>
<?php
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();

    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Polls', 'sp-polls'), true, 'polls-manage');
    			$paged = (isset($_GET['paged'])) ? SP()->filters->integer($_GET['paged']) : 1;
    			$polls_per_page = (isset($_GET['polls_per_page'])) ? SP()->filters->integer($_GET['polls_per_page']) : 10;
				$search = (isset($_GET['s'])) ? SP()->filters->str($_GET['s']) : '';

            	# how many polls per page?
            	$startlimit = 0;
            	if ($paged != 1) $startlimit = (($paged - 1) * $polls_per_page);
            	$limit = "LIMIT $startlimit, $polls_per_page";

            	# build the where clause for poll search term
            	$where = (empty($search)) ? '' : " WHERE poll_question LIKE '%".SP()->filters->esc_sql($wpdb->esc_like($search))."%'";

            	# retrieve topic records
            	$polls = SP()->DB->select('SELECT * FROM '.SPPOLLS."$where ORDER BY poll_id DESC $limit");
				$num_polls = SP()->DB->count(SPPOLLS);
?>
				<form id="posts-filter" action="<?php echo SPADMINPLUGINS; ?>" method="get">
					<input type="hidden" name="page" value="<?php echo SP_FOLDER_NAME; ?>/admin/panel-plugins/spa-plugins.php" />
					<input type="hidden" name="tab" value="plugin" />
					<input type="hidden" name="admin" value="sp_polls_admin_manage" />
					<input type="hidden" name="form" value="0" />
					<input type="hidden" name="polls_per_page" value="<?php echo $polls_per_page; ?>" />
					<ul class="subsubsub" style="padding-right:20px">
<?php
						echo '<li><a href="'.SPADMINPLUGINS.'&amp;tab=plugin&amp;admin=sp_polls_admin_manage&amp;form=0&amp;polls_per_page='.$polls_per_page.'">'.__('Show all polls', 'sp-polls').' ('.$num_polls.')</a></li>';
?>
					</ul>
					<p id="post-search">
						<input class="sfpostcontrol" style="width:250px;" type="text" id="post-search-input" name="s" value="<?php echo $search; ?>" />
						<input type="submit" value="<?php esc_attr_e(__('Search Poll Questions', 'sp-polls')); ?>" class="button" />
					</p>
					<div class="tablenav">
                        <?php esc_attr_e(__('Polls per page', 'sp-polls')); ?>:&nbsp;
    					<select name="polls_per_page" id="polls_per_page" style='font-weight:normal'>
        					<option <?php if ($polls_per_page == 5) echo 'selected="selected"'; ?> value="5">5</option>
        					<option <?php if ($polls_per_page == 10) echo 'selected="selected"'; ?> value="10">10</option>
        					<option <?php if ($polls_per_page == 20) echo 'selected="selected"'; ?> value="20">20</option>
        					<option <?php if ($polls_per_page == 25) echo 'selected="selected"'; ?> value="25">25</option>
        					<option <?php if ($polls_per_page == 50) echo 'selected="selected"'; ?> value="50">50</option>
        					<option <?php if ($polls_per_page == 100) echo 'selected="selected"'; ?> value="100">100</option>
    				    </select>
						<input type="submit" id="filter-submit" value="<?php esc_attr_e(__('Apply', 'sp-polls')); ?>" class="button-secondary" />
<?php
						$page_links = paginate_links(array(
							'total' => ceil($num_polls / $polls_per_page ),
							'current' => $paged,
							'base' => SPADMINPLUGINS.'&tab=plugin&admin=sp_polls_admin_manage&form=0&polls_per_page='.$polls_per_page.'&amp;%_%',
							'format' => 'paged=%#%',
							'add_args' => ''));

						if ($page_links) echo "<div class='tablenav-pages'>$page_links</div>";
?>
                        <br style="clear:both;" />
		   		    </div>
				</form>
<?php
                if ($polls) {
?>
                    <table class="widefat fixed striped spMobileTable800">
                        <thead>
                            <tr>
                                <th style='text-align:center'><?php echo __('Poll ID', 'sp-polls'); ?></th>
                                <th style='text-align:center'><?php echo __('Question', 'sp-polls'); ?></th>
                                <th style='text-align:center'><?php echo __('# Votes', 'sp-polls'); ?></th>
                                <th style='text-align:center'><?php echo __('# Voters', 'sp-polls'); ?></th>
                                <th style='text-align:center'><?php echo __('Start Date', 'sp-polls'); ?></th>
                                <th style='text-align:center'><?php echo __('Expiration Date', 'sp-polls'); ?></th>
                                <th style='text-align:center'><?php echo __('Hidden Results', 'sp-polls'); ?></th>
                                <th style='text-align:center'><?php echo __('Status', 'sp-polls'); ?></th>
                                <th style='text-align:center'><?php echo __('Manage', 'sp-polls'); ?></th>
                            </tr>
                        </thead>

                        <tbody>
<?php
                        foreach ($polls as $poll) {
?>
                        <tr id='sp-poll-<?php echo $poll->poll_id; ?>' class='spMobileTableData'>
                            <td data-label='<?php echo __('Poll ID', 'sp-polls'); ?>' style='text-align:center'><?php echo $poll->poll_id; ?></td>
                            <td data-label='<?php echo __('Question', 'sp-polls'); ?>' ><?php echo $poll->poll_question; ?></td>
                            <td data-label='<?php echo __('Votes', 'sp-polls'); ?>' style='text-align:center'><?php echo $poll->poll_votes; ?></td>
                            <td data-label='<?php echo __('Voters', 'sp-polls'); ?>' style='text-align:center'><?php echo $poll->poll_voters; ?></td>
                            <td data-label='<?php echo __('Start', 'sp-polls'); ?>' style='text-align:center'><?php echo $poll->poll_date; ?></td>
                            <td data-label='<?php echo __('Exiration', 'sp-polls'); ?>' style='text-align:center'><?php echo $poll->poll_expiration; ?></td>
                            <td data-label='<?php echo __('Hide', 'sp-polls'); ?>' style='text-align:center'><?php echo $poll->hide_results; ?></td>
                            <td data-label='<?php echo __('Status', 'sp-polls'); ?>' style='text-align:center'><?php echo ($poll->poll_active) ? __('Active', 'sp-polls') : __('Inactive', 'sp-polls'); ?></td>
                            <td data-label='<?php echo __('Manage', 'sp-polls'); ?>' style='text-align:center'>
                                <?php
                                    $site = wp_nonce_url(SPAJAXURL."polls-log&amp;targetaction=poll-log&amp;pid=$poll->poll_id", 'polls-log');
                                	$target = 'sp-poll-ajax';
                                	$gif = SPADMINIMAGES.'sp_WaitBox.gif';
                                ?>
                                <a>
        							<img src="<?php echo SP()->theme->paint_file_icon(POLLSIMAGES, 'sp_PollsLogs.png'); ?>" title="<?php _e('Poll Log', 'sp-polls'); ?>" class="spPollsAdminTool" data-url="<?php echo $site; ?>" data-target="<?php echo $target; ?>" data-img="<?php echo $gif; ?>" alt="" />&nbsp;&nbsp;
                                </a>
                                <?php
                                    $site = wp_nonce_url(SPAJAXURL."polls-edit&amp;targetaction=edit-poll&amp;pid=$poll->poll_id", 'polls-edit');
                                	$target = 'sp-poll-ajax';
                                	$gif = SPADMINIMAGES.'sp_WaitBox.gif';
                                ?>
                                <a>
        							<img src="<?php echo SP()->theme->paint_file_icon(POLLSIMAGES, 'sp_PollsEdit.png'); ?>" title="<?php _e('Edit Poll', 'sp-polls'); ?>" class="spPollsAdminTool" data-url="<?php echo $site; ?>" data-target="<?php echo $target; ?>" data-img="<?php echo $gif; ?>" alt="" />&nbsp;&nbsp;
                                </a>
                                <?php
                                    $msg = esc_attr(__('Are you sure you want to delete this poll?'), 'sp-polls');
                                    $site = wp_nonce_url(SPAJAXURL."polls-manage&amp;targetaction=delete-poll&amp;pid=$poll->poll_id", 'polls-manage');
                                ?>
                                <a>
                                    <img src="<?php echo SP()->theme->paint_file_icon(POLLSIMAGES, 'sp_PollsDelete.png'); ?>" title="<?php _e('Delete Poll', 'sp-polls'); ?>" class="spDeleteRow" data-msg="<?php echo $msg; ?>" data-url="<?php echo $site; ?>" data-target="sp-poll-<?php echo $poll->poll_id; ?>" alt="" />
                                </a>
                            </td>
                        </tr>
<?php
                    }
?>
                    </tbody>
                    </table>
<?php
                } else {
                    echo '<p>'.__('There are no polls in the database matching the criteria', 'sp-polls').'</p>';
                }
    		spa_paint_close_fieldset();
        	echo '<div class="sfform-panel-spacer"></div>';
    	spa_paint_close_panel();
		spa_paint_close_container();
    spa_paint_close_tab();

    # hidden area for displaying poll log
	echo '<div id="sp-poll-ajax" class="sfinline-form"></div>';
}
