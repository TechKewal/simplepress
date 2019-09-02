<?php
/*
Simple:Press
Ranks Inof Plugin Support Routines
$LastChangedDate: 2017-09-13 23:23:14 -0500 (Wed, 13 Sep 2017) $
$Rev: 15552 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_do_DisplayRankInfo() {
    $options = SP()->options->get('rank-info');

	$ranks_data = SP()->meta->get_values('forum_rank');
    $ranks = SP()->primitives->array_msort($ranks_data, array('posts' => SORT_ASC));
    $usergroups = SP()->DB->table(SPUSERGROUPS, '', '', '', '', ARRAY_A);

    # forum ranks
    echo '<table id="spRankInfo">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>'.__('Rank Name', 'sp-rank-info').'</th>';
    echo '<th style="width:20%">'.__('Posts to Achieve Rank', 'sp-rank-info').'</th>';
    if ($options['membership']) echo '<th style="width:20%">'.__('Membership Attained', 'sp-rank-info').'</th>';
    if ($options['badge']) echo '<th style="width:20%">'.__('Badge', 'sp-rank-info').'</th>';
    if ($options['users']) echo '<th style="width:20%">'.__('Members', 'sp-rank-info').'</th>';
    echo '</tr>';
    echo '</thead>';

    echo '<tbody>';
    if (!empty($ranks)) {
        $prev = 0;
        $show = true;
        foreach ($ranks as $name => $rank) {
            echo '<tr class="spRankData">';
            echo "<td data-label='".esc_attr(__('Rank Name', 'sp-rank-info'))."'>$name</td>";
            echo "<td data-label='".esc_attr(__('Posts Needed', 'sp-rank-info'))."'>$prev</td>";
            if ($options['membership']) {
                $usergroup = SP()->primitives->array_search_multi($usergroups, 'usergroup_id', $rank['usergroup']);
                $ugname = (!empty($usergroup)) ? $usergroup[0]['usergroup_name'] : __('None', 'sp-rank-info');
                echo "<td data-label='".esc_attr(__('Membership', 'sp-rank-info'))."'>$ugname</td>";
            }
            if ($options['badge']) {
                $badge = (!empty($rank['badge'])) ? '<img src="'.esc_url(SPRANKS.$rank['badge']).'" alt="" />' : __('None', 'sp-rank-info');
                echo "<td data-label='".esc_attr(__('Badge', 'sp-rank-info'))."'>$badge</td>";
            }
            if ($options['same_rank'] && !SP()->user->thisUser->admin) $show = ($name == SP()->user->thisUser->rank[0]['name']); # are we limiting to same rank?
            if ($options['users']) {
                if ($show) {
                    echo '<td data-label="'.esc_attr(__('Members', 'sp-rank-info')).'"><a class="spRankInfoView" data-id="spRankMembers'.$prev.'">'.__('View', 'sp-rank-info').'</a></td>';
                } else {
                    echo '<td>'.__('Not Available', 'sp-rank-info').'</td>';
                }
            }
            echo '</tr>';
            if ($options['users'] && $show) {
                $members = SP()->DB->table(SPMEMBERS, '(posts >= '.$prev.' AND posts < '.$rank['posts'].') AND admin=0');
                echo "<tr id='spRankMembers$prev' class='spRankMembers spInlineSection'>";
                echo '<td colspan="5">';
                echo '<table class="spMemberData">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>'.__('Members', 'sp-rank-info').'</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                echo '<tr>';
                echo '<td class="spRankMembers">';
                if (!empty($members)) {
                	$first = true;
                	foreach ($members as $user) {
                        if (!$first) {
                            echo ', ';
                        } else {
                            $first = false;
                        }
                		echo SP()->user->name_display($user->user_id, SP()->displayFilters->name($user->display_name));
                	}
                } else {
                    echo __('No members', 'sp-rank-info');
                }
                echo '</td>';
                echo '</tr>';
                echo '</tbody>';
                echo '</table>';
                echo '</td>';
                echo '</tr>';
            }
            $prev = $rank['posts'] + 1;
        }
    } else {
        echo '<tr>';
        echo '<td colspan="4">'.__('No forum ranks found', 'sp-rank-info').'</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo "</table>\n";

    # special ranks
    if ($options['special_ranks']) {
        echo '<table id="spRankInfoSpecial">';
        echo '<thead>';
        echo '<tr>';
        echo '<th style="width:25%">'.__('Special Rank Name', 'sp-rank-info').'</th>';
        echo '<th style="width:25%">'.__('Badge', 'sp-rank-info').'</th>';
        if ($options['special_users']) echo '<th style="width:20%">'.__('Members', 'sp-rank-info').'</th>';
        echo '</tr>';
        echo '</thead>';

        echo '<tbody>';
		$ranks_data = SP()->meta->get_values('special_rank');
        if (!empty($ranks_data)) {
            $count = 0;
            $show = true;
            foreach ($ranks_data as $name => $rank) {
                echo '<tr class="spRankDataSpecial">';
                echo "<td data-label='".esc_attr(__('Rank Name', 'sp-rank-info'))."'>$name</td>";
                $badge = (!empty($rank['badge'])) ? '<img src="'.esc_url(SPRANKS.$rank['badge']).'" alt="" />' : __('None', 'sp-rank-info');
                echo "<td data-label='".esc_attr(__('Badge', 'sp-rank-info'))."'>$badge</td>";
                if ($options['same_special_rank'] && !SP()->user->thisUser->admin) {
                    if (SP()->user->thisUser->special_rank) {
                        foreach (SP()->user->thisUser->special_rank as $thisrank) {
                            $show = ($name == $thisrank['name']); # are we limiting to same rank?
                            if ($show) break;
                        }
                    }
                }
                if ($options['special_users']) {
                    if ($show) {
                        echo '<td data-label="'.esc_attr(__('Members', 'sp-rank-info')).'"><a class="spRankInfoView" data-id="spRankMembersSpecial'.$count.'">'.__('View', 'sp-rank-info').'</a></td>';
                    } else {
                        echo '<td>'.__('Not Available', 'sp-rank-info').'</td>';
                    }
                }
                echo '</tr>';
                if ($options['special_users'] && $show) {
                    $members = array();
					$members = SP()->DB->select('SELECT DISTINCT '.SPMEMBERS.'.user_id, '.SPMEMBERS.'.display_name
									FROM '.SPSPECIALRANKS.'
									RIGHT JOIN '.SPMEMBERS.' ON '.SPMEMBERS.'.user_id = '.SPSPECIALRANKS.'.user_id
									WHERE (special_rank = "'.$name.'")
									ORDER BY display_name');

                    echo "<tr id='spRankMembersSpecial$count' class='spRankMembersSpecial spInlineSection'>";
                    echo '<td colspan="3">';
                    echo '<table class="spMemberData">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>'.__('Members', 'sp-rank-info').'</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    echo '<tr>';
                    echo '<td class="spRankMembersSpecial">';
                    if (!empty($members)) {
                    	$first = true;
                    	foreach ($members as $user) {
                            if (!$first) {
                                echo ', ';
                            } else {
                                $first = false;
                            }
                    		echo SP()->user->name_display($user->user_id, SP()->displayFilters->name($user->display_name));
                    	}
                    } else {
                        echo __('No members', 'sp-rank-info');
                    }
                    echo '</td>';
                    echo '</tr>';
                    echo '</tbody>';
                    echo '</table>';
                    echo '</td>';
                    echo '</tr>';
                }
                $count++;
            }
        } else {
            echo '<tr>';
            echo '<td colspan="2">'.__('No special ranks found', 'sp-rank-info').'</td>';
            echo '</tr>';

        }
        echo '</tbody>';
        echo "</table>\n";
    }
}
