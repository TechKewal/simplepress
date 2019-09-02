<?php
/*
Simple:Press
Polls plugin ajax routine for editing polls functions
$LastChangedDate: 2018-10-17 15:17:49 -0500 (Wed, 17 Oct 2018) $
$Rev: 15756 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

spa_admin_ajax_support();

if (!sp_nonce('polls-edit')) die();

if (!isset($_GET['targetaction'])) die();
$action = SP()->filters->str($_GET['targetaction']);

if ($action == 'edit-poll') {
    require_once SP_PLUGIN_DIR.'/admin/library/spa-tab-support.php';

    $poll_id = SP()->filters->integer($_GET['pid']);
    if (SP()->auths->current_user_can('SPF Manage Polls') && !empty($poll_id)) {
?>
        <script>
			(function(spj, $, undefined) {
				spj.loadAjaxForm('sp-poll-edit', 'pollmanage');
			}(window.spj = window.spj || {}, jQuery));
        </script>
<?php
        $ajaxURL = wp_nonce_url(SPAJAXURL.'plugins-loader&saveform=plugin&func=sp_polls_admin_save_poll', 'plugins-loader');
?>
    	<form action='<?php echo $ajaxURL; ?>' method='post' id='sp-poll-edit' name='sp-poll-edit'>
<?php
        	echo sp_create_nonce('forum-adminform_userplugin');

        	$polls = SP()->options->get('polls');

        	$poll = SP()->DB->table(SPPOLLS, "poll_id=$poll_id", 'row');
            $answers = SP()->DB->table(SPPOLLSANSWERS, "poll_id = $poll_id");

        	spa_paint_options_init();
			spa_paint_open_panel();
				spa_paint_open_fieldset(__('Poll Edit', 'sp-polls'), false);
			        spa_paint_input(__('Poll question', 'sp-polls'), 'sp-poll-question', SP()->displayFilters->title($poll->poll_question));
                    echo '<tr><td colspan="2">';
                    echo '<table id="sp_poll_answers" style="width:100%">';
                    foreach ($answers as $index => $answer) {
                        echo "<tr id='sp-answer-$answer->answer_id'>";
                        echo '<td class="sflabel" style="width:60%">';
                        $msg = esc_attr(__('Are you sure you want to delete this answer? This cannot be undone. Save after deleting to update vote counts.', 'sp-polls'));
                        $site = wp_nonce_url(SPAJAXURL."polls-edit&amp;targetaction=delete-question&amp;pid=$poll_id&amp;aid=$answer->answer_id", 'polls-edit');
                        echo '<span class="sfalignleft">';
                        echo __('Answer', 'sp-polls'). ' #'.($index+1).':&nbsp;&nbsp;&nbsp;&nbsp;';
                        echo '<a><img style="vertical-align:middle" src="'.SP()->theme->paint_file_icon(POLLSIMAGES, 'sp_PollsRemove.png').'" title="'.esc_attr('Delete Answer', 'sp-polls').'" class="spPollsRemoveAnswer" data-msg="'.$msg.'" data-url="'.$site.'" data-target="sp-answer-'.$answer->answer_id.'" data-target2="sp-answer-votes-'.$answer->answer_id.'" alt="" /></a>';
                        echo '</span>';
                        echo '</td>';
                        echo '<td>';
                        echo "<input type='hidden' value='$answer->answer_id' id='sp-poll-answer-id' name='sp-poll-answer-id' />";
                        echo "<input class='sfpostcontrol' type='text' value='".SP()->displayFilters->title($answer->answer)."' id='sp-poll-answer[$answer->answer_id]' name='sp-poll-answer[$answer->answer_id]' />";
                        echo '</td>';
                        echo '</tr>';
                        echo "<tr id='sp-answer-votes-$answer->answer_id'>";
                        echo '<td class="sflabel" style="width:60%"><span class="sfalignleft">'.__('Answer', 'sp-polls'). ' #'.($index+1).' votes'.'</span></td>';
                        echo "<td><input class='sfpostcontrol' type='text' value='".SP()->displayFilters->title($answer->answer_votes)."' id='sp-poll-answer-votes[$answer->answer_id]' name='sp-poll-answer-votes[$answer->answer_id]' /></td>";
                        echo '</tr>';
                    }
                    echo '</table>';
                    echo '</td></tr>';
                    echo "<input type='hidden' value='$poll_id' id='sp-poll-id' name='sp-poll-id' />";
                    echo '<input type="hidden" value="'.count($answers).'" id="sp-poll-answer-count" name="sp-poll-answer-count" />';
                    echo '<input type="hidden" value="0" id="sp-poll-answer-next" name="sp-poll-answer-next" />';
			        spa_paint_input(__('Poll voters', 'sp-polls'), 'sp-poll-voters', $poll->poll_voters);
			        spa_paint_input(__('Poll date', 'sp-polls'), 'sp-poll-date', SP()->displayFilters->title($poll->poll_date));
			        spa_paint_input(__('Poll expiration', 'sp-polls'), 'sp-poll-expiration', SP()->displayFilters->title($poll->poll_expiration));
			        spa_paint_input(__('Poll creator', 'sp-polls'), 'sp-poll-user', $poll->user_id);
			        spa_paint_input(__('Poll max answers', 'sp-polls'), 'sp-poll-max', $poll->poll_maxanswers);
			        if ($polls['hide-results']) spa_paint_checkbox(__('Hide results until poll closed', 'sp-polls'), 'sp-poll-hide', $poll->hide_results);
			        spa_paint_checkbox(__('Poll active', 'sp-polls'), 'sp-poll-active', $poll->poll_active);
				spa_paint_close_fieldset();
			spa_paint_close_panel();
?>
			<div class='sfform-submit-bar'>
    			<input type='submit' class='button-primary' id='spPollEdit' name='spPollEdit' value='<?php esc_attr_e(__('Save Poll', 'sp-polls')); ?>' />
                <input type='button' class='button-primary spPollsAddAnswer' name='spPollAddAnswer' value='<?php esc_attr_e(__('Add an Answer', 'sp-polls')); ?>' />
    			<input type='button' class='button-primary spPollsEditCancel' id='spPollEditCancel' name='spPollEditCancel' value='<?php esc_attr_e(__('Cancel', 'sp-polls')); ?>' />
			</div>
    	</form>
<?php
    }

    die();
}

if ($action == 'delete-question') {
    $poll_id = SP()->filters->integer($_GET['pid']);
    $answer_id = SP()->filters->integer($_GET['aid']);
    if (SP()->auths->current_user_can('SPF Manage Polls') && !empty($answer_id)) {
        SP()->DB->execute('DELETE FROM '.SPPOLLSANSWERS." WHERE poll_id=$poll_id AND answer_id=$answer_id");
        SP()->DB->execute('DELETE FROM '.SPPOLLSVOTERS." WHERE poll_id=$poll_id AND answer_id=$answer_id");
    }

    die();
}

die();
