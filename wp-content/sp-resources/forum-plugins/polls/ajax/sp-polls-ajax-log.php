<?php
/*
Simple:Press
Polls plugin ajax routine for polls log functions
$LastChangedDate: 2018-10-17 15:17:49 -0500 (Wed, 17 Oct 2018) $
$Rev: 15756 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

spa_admin_ajax_support();

if (!sp_nonce('polls-log')) die();

if (!isset($_GET['targetaction'])) die();
$action = SP()->filters->str($_GET['targetaction']);

if ($action == 'poll-log') {
    require_once SP_PLUGIN_DIR.'/admin/library/spa-tab-support.php';

    $poll_id = SP()->filters->integer($_GET['pid']);
    if (SP()->auths->current_user_can('SPF Manage Polls') && !empty($poll_id)) {
    	spa_paint_options_init();
    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Poll Logs', 'sp-polls'), false);
                $answers = SP()->DB->select('SELECT answer FROM '.SPPOLLSANSWERS." WHERE poll_id = $poll_id", 'col');
                $answer_ids = SP()->DB->select('SELECT answer_id FROM '.SPPOLLSANSWERS." WHERE poll_id = $poll_id", 'col');

        		$paged = (!empty($_GET['paged'])) ? SP()->filters->integer($_GET['paged']) : 1;
        		$voters_per_page = (isset($_GET['voters_per_page'])) ? SP()->filters->integer($_GET['voters_per_page']) : 10;
        		$search = (!empty($_GET['answer'])) ? SP()->filters->integer($_GET['answer']) : '';

               	$target = 'sp-poll-ajax';
               	$gif = SPADMINIMAGES.'sp_WaitBox.gif';

            	# how many polls per page?
            	$startlimit = 0;
            	if ($paged != 1) $startlimit = (($paged - 1) * $voters_per_page);
            	$limit = "LIMIT $startlimit, $voters_per_page";

            	# build the where clause for poll search term
                $where = "poll_id = $poll_id";
            	$where.= (empty($search)) ? '' : " AND answer_id = '$search'";

            	# retrieve topic records
            	$voters = SP()->DB->select('SELECT * FROM '.SPPOLLSVOTERS." WHERE $where ORDER BY answer_id DESC $limit");
        		$num_voters = SP()->DB->count(SPPOLLSVOTERS, "$where");
?>
					<div class="tablenav">
                        <?php esc_attr_e(__('Filter by answer', 'sp-polls')); ?>:&nbsp;
    					<select name="sp-poll-answer" id="sp-poll-answer">
<?php
                            echo "<option value=''>".__('All Answers', 'sp-polls').'</option>';
                            foreach ($answers as $index => $answer) {
                                $selected = ($search == $answer_ids[$index]) ? " selected='selected'" : "";
                                echo "<option value='$answer_ids[$index]'$selected>$answer</option>";
                            }
?>
    				    </select>
                        <?php $site = wp_nonce_url(SPAJAXURL."polls-log&targetaction=poll-log&pid=$poll_id&paged=$paged&voters_per_page=$voters_per_page", 'polls-log'); ?>
						<input type="button" id="search-submit" value="<?php esc_attr_e(__('Apply', 'sp-polls')); ?>" class="button-secondary spPollsLogSearch" data-url="<?php echo $site; ?>" data-target="<?php echo $target; ?>" data-img="<?php echo $gif; ?>" />

                        <?php esc_attr_e(__('Log entries per page', 'sp-polls')); ?>:&nbsp;
    					<select name="voters_per_page" id="voters_per_page">
        					<option <?php if ($voters_per_page == 5) echo 'selected="selected"'; ?> value="5">5</option>
        					<option <?php if ($voters_per_page == 10) echo 'selected="selected"'; ?> value="10">10</option>
        					<option <?php if ($voters_per_page == 20) echo 'selected="selected"'; ?> value="20">20</option>
        					<option <?php if ($voters_per_page == 25) echo 'selected="selected"'; ?> value="25">25</option>
        					<option <?php if ($voters_per_page == 50) echo 'selected="selected"'; ?> value="50">50</option>
        					<option <?php if ($voters_per_page == 100) echo 'selected="selected"'; ?> value="100">100</option>
    				    </select>
                        <?php $site = wp_nonce_url(SPAJAXURL."polls-log&targetaction=poll-log&pid=$poll_id&paged=$paged&answer=$search", 'polls-log'); ?>
						<input type="button" id="limit-submit" value="<?php esc_attr_e(__('Apply', 'sp-polls')); ?>" class="button-secondary spPollsLogLimit" data-url="<?php echo $site; ?>" data-target="<?php echo $target; ?>" data-img="<?php echo $gif; ?>" />
<?php
						$page_links = paginate_links(array(
							'total' => ceil($num_voters / $voters_per_page ),
							'current' => $paged,
							'base' => SPADMINPLUGINS.'&amp;tab=plugin&amp;admin=sp_polls_admin_manage&amp;form=0&amp;polls_per_page='.$voters_per_page.'&amp;%_%',
							'format' => 'paged=%#%',
                            'type' => 'array',
							'add_args' => ''));

                        # we need to convert the page links to use ajax
                        if ($page_links) {
					        echo "<div class='tablenav-pages'>";
                            foreach ($page_links as $page) {
                                $page = str_replace('#038;', '&', $page);

                                # get the paged param
                                if (strpos($page, 'current') !== false) {
                                    $thisPage = 1;
                                } else {
                                    parse_str($page, $args);
                                    $thisPage = $args['paged'];
                                }

                                # now fix up the url to use our ajax stuff instead of admin url
                                $site = wp_nonce_url(SPAJAXURL."polls-log&targetaction=poll-log&pid=$poll_id&voters_per_page=$voters_per_page&paged=$thisPage&answer=$search", 'polls-log');
                                if (strpos($page, 'prev') !== false) {
                                    $page = str_replace('a class="prev page-numbers"', 'a class="prev page-numbers spPollsLogPage" data-url="'.$site.'" data-target="'.$target.'" data-img="'.$gif.'"', $page);
                                    $page = preg_replace('/href=".*?"/', '', $page);
                                } else if (strpos($page, 'next') !== false) {
                                    $page = str_replace('a class="next page-numbers"', 'a class="next page-numbers spPollsLogPage" data-url="'.$site.'" data-target="'.$target.'" data-img="'.$gif.'"', $page);
                                    $page = preg_replace('/href=".*?"/', '', $page);
                                } else {
                                    $page = str_replace("a class='page-numbers'", "a class='page-numbers spPollsLogPage' data-url='$site' data-target='$target' data-img='$gif'", $page);
                                    $page = preg_replace("/href='.*?'/", '', $page);
                                }

                                # write out the page link
                                echo $page.' ';
                            }
                            echo "</div>";
                        }
?>
                        <br style="clear:both;" />
		   		    </div>
<?php
                if ($voters) {
?>
                    <table id="sp-poll-log-table" class="widefat fixed spMobileTable800">
                        <thead>
                            <tr>
                                <th><?php echo __('Answer', 'sp-polls'); ?></th>
                                <th style='text-align:center'><?php echo __('User ID', 'sp-polls'); ?></th>
                                <th style='text-align:center'><?php echo __('Date', 'sp-polls'); ?></th>
                                <th style='text-align:center'><?php echo __('IP', 'sp-polls'); ?></th>
                                <th style='text-align:center'><?php echo __('Manage', 'sp-polls'); ?></th>
                            </tr>
                        </thead>

                        <tbody>
<?php
                    $answer_ids = array_flip($answer_ids);
                    foreach ($voters as $voter) {
?>
                        <tr id="sp-poll-log-<?php echo $voter->vote_id; ?>" class="spMobileTableData">
                            <td data-label='<?php echo __('Answer', 'sp-polls'); ?>' ><?php echo $answers[$answer_ids[$voter->answer_id]]; ?></td>
                            <td data-label='<?php echo __('User', 'sp-polls'); ?>' style='text-align:center'>
<?php
                                if (empty($voter->user_id)) {
                                    $name = 'Guest';
                                } else {
                                    $name = SP()->memberData->get($voter->user_id, 'display_name');
                                }
                                echo $name;
?>
                            </td>
                            <td data-label='<?php echo __('Date', 'sp-polls'); ?>' style='text-align:center'><?php echo $voter->vote_date; ?></td>
                            <td data-label='<?php echo __('IP', 'sp-polls'); ?>' style='text-align:center'><?php echo $voter->user_ip ?></td>
                            <td data-label='<?php echo __('Manage', 'sp-polls'); ?>' style='text-align:center'>
<?php
                                $msg = esc_attr(__('Are you sure you want to delete this log entry?'), 'sp-polls');
                                $site = wp_nonce_url(SPAJAXURL."polls-log&amp;targetaction=delete-log&amp;vid=$voter->vote_id", 'polls-log');
?>
                                <a>
                                    <img src="<?php echo SP()->theme->paint_file_icon(POLLSIMAGES, 'sp_PollsLogDelete.png'); ?>" title="<?php esc_attr_e(__('Delete Log Entry', 'sp-polls')); ?>" class="spPollsLogDelete" data-msg="<?php echo $msg; ?>" data-url="<?php echo $site; ?>" data-target="sp-poll-log-<?php echo $voter->vote_id; ?>" alt="" />
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
                    echo '<p>'.__('There are no voter logs for this poll matching the criteria', 'sp-polls').'</p>';
                }

                if ($voters) {
                    $msg = esc_attr(__('Are you sure you want to delete ALL log entries for this Poll?'), 'sp-polls');
                    $site = wp_nonce_url(SPAJAXURL."polls-log&amp;targetaction=delete-all&amp;pid=$poll_id", 'polls-log');
?>
               	    <p style="text-align:center"><input type="button" class="button button-highlighted spPollsLogDeleteAll" value="<?php esc_attr_e(__('Delete ALL Logs for this Poll', 'sp-polls')); ?>" data-msg="<?php echo $msg; ?>" data-url="<?php echo $site; ?>" data-target="sp-poll-log-table" /></p>
<?php
                }
    		spa_paint_close_fieldset();
    	spa_paint_close_panel();
    }

    die();
}

if ($action == 'delete-log') {
    $vote_id = SP()->filters->integer($_GET['vid']);
    if (SP()->auths->current_user_can('SPF Manage Polls') && !empty($vote_id)) {
        SP()->DB->execute('DELETE FROM '.SPPOLLSVOTERS." WHERE vote_id=$vote_id");
    }

    die();
}

if ($action == 'delete-all') {
    $poll_id = SP()->filters->integer($_GET['pid']);
    if (SP()->auths->current_user_can('SPF Manage Polls') && !empty($poll_id)) {
        SP()->DB->execute('DELETE FROM '.SPPOLLSVOTERS." WHERE poll_id=$poll_id");
    }

    die();
}

die();
