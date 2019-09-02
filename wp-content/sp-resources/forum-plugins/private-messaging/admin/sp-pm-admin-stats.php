<?php
/*
Simple:Press
PM Plugin Admin Stats Form
$LastChangedDate: 2018-11-13 20:42:11 -0600 (Tue, 13 Nov 2018) $
$Rev: 15818 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_pm_admin_stats_form() {
    if (!SP()->auths->current_user_can('SPF Manage PM')) die();

	spa_paint_options_init();
	spa_paint_open_tab(__('Private Messaging', 'sp-pm').' - '.__('Member PM Stats', 'sp-pm'), true);
		spa_paint_open_panel();
			spa_paint_open_fieldset(__('Member PM Stats', 'sp-pm'), 'true', 'pm-stats');
                require_once ABSPATH.'wp-admin/includes/admin.php';

                class SP_PM_Table extends WP_List_Table {
                    function __construct() {
                        parent::__construct( array(
                            'singular'=> __('private message', 'sp-pm'),
                            'plural' => __('private messages', 'sp-pm'),
                            'ajax'   => false
                        ));
                    }

                    function get_columns(){
                        $columns = array(
                            'user_id'           => __('ID', 'sp-pm'),
                            'user_login'        => __('User Login', 'sp-pm'),
                            'display_name'      => __('Display Name', 'sp-pm'),
                            'can_pm'            => __('Can PM', 'sp-pm'),
                            'threads'           => __('Threads', 'sp-pm'),
                            'received'          => __('Received', 'sp-pm'),
                            'sent'              => __('Sent', 'sp-pm'),
                            'unread'            => __('Unread', 'sp-pm'),
                        );
                        return $columns;
                    }

                    public function get_sortable_columns() {
                        $sortable_columns = array(
                            'user_id'           => array('user_id', true),
                            'user_login'        => array('user_login', false),
                            'display_name'      => array('display_name', false),
                        );
                        return $sortable_columns;
                    }

                    function no_items() {
                        SP()->primitives->admin_etext('No members found');
                    }

                	function get_table_classes() {
                		return array('widefat', 'fixed', 'striped', $this->_args['plural'], 'spMobileTable1280');
                	}

                    function display_rows() {
                        $records = $this->items;
                        if (!empty($records)) {
                    		list($columns, $hidden, $sortable, $primary) = $this->get_column_info();
                            foreach ($records as $rec) {
                                echo '<tr id="pmdata'.$rec['user_id'].'" class="spMobileTableData">';

                                foreach ($columns as $column_name => $column_display_name) {
                        			$classes = "$column_name column-$column_name";
                        			$data = 'data-label="'.wp_strip_all_tags($column_display_name).'"';
                        			$attributes = "class='$classes' $data";

                                    echo "<td $attributes>";
                                    switch ($column_name) {
                                        case 'user_id':
                                            echo $rec['user_id'];
                                            if ($rec['messages'] > 0) {
                                                $msg = esc_attr(__("Are you sure you want to delete this user's PMs?"), 'sp-pm');
                        						$ajaxUrl = wp_nonce_url(SPAJAXURL.'pm-manage&targetaction=delpms&id='.$rec['user_id'], 'pm-manage');
                                                $actions = array(
                                                    'delete'   => '<a class="spPMAdminDeletePms" data-msg="'.$msg.'" data-url="'.$ajaxUrl.'" data-user="pmdata'.$rec['user_id'].'">'.__('Delete PMs', 'sp-pm').'</a>',
                                                );
                                            } else {
                                                $actions = array(
                                                    'delete'   => '&nbsp',
                                                );
                                            }
                                            echo $this->row_actions($actions);
                                            break;

                                        case 'display_name':
                                            echo SP()->displayFilters->name($rec['display_name']);
                                            break;

                                        case 'can_pm':
                                            $text = ($rec['can_pm']) ? __('Yes', 'sp-pm') : __('No', 'sp-pm');
                                            echo $text;
                                            break;

                                        default:
                                            echo $rec[$column_name];
                                    }
                                    echo '</td>';
                                }

                                echo '</tr>';
                            }
                        }
                    }

                    function prepare_items() {
                        # init the class
                        $columns = $this->get_columns();
                        $hidden = array();
                        $sortable = $this->get_sortable_columns();
                        $this->_column_headers = array($columns, $hidden, $sortable);

                        # set up some globals to handle our bastardized urls
                        $bits = parse_url(admin_url('admin.php?page='.SP_FOLDER_NAME.'/admin/panel-plugins/spa-plugins.php&amp;tab=plugin&amp;admin=sp_pm_admin_stats&amp;save=&amp;form=0'));
                        $_SERVER['REQUEST_URI'] = $bits['path'].'?'.$bits['query'];

                        # start the query
                       	$query = new stdClass;
                        $query->table        = SPMEMBERS;
                        $query->found_rows   = true;
                        $query->fields       = SPMEMBERS.'.user_id, '.SPMEMBERS.'.display_name, user_login';
        				$query->join         = array(SPUSERS.' ON '.SPMEMBERS.'.user_id = '.SPUSERS.'.ID');

                        # handle sort ordering
                        $orderby = (!empty($_GET['orderby'])) ? SP()->filters->esc_sql($_GET['orderby']) : 'ASC';
                        $order = (!empty($_GET['order'])) ? SP()->filters->esc_sql($_GET['order']) : '';
                        $query->orderby = (!empty($orderby) && !empty($order)) ? "$orderby $order" : '';

                        # pagination
                        $per_page = 50;
                        $current_page = $this->get_pagenum();
                        $offset = ($current_page - 1) * $per_page;
            			$query->limits = "$offset, $per_page";

                        # searching
                		$search_term = isset($_GET['s']) ? SP()->saveFilters->title(trim($_GET['s'])) : '';
                        if ($search_term) {
                			$searches = array();
                			foreach (array('user_login', SPMEMBERS.'.display_name') as $col) {
                				$searches[] = $col." LIKE '%$search_term%'";
                            }
                			$where = implode(' OR ', $searches);
                            $query->where = (!empty($query->where)) ? " AND ($where)" : $where;

                            # if no ordering, list matches that start with the serch term first
                            global $wpdb;
                            if (empty($query->orderby)) $query->orderby = 'IF ('.SPMEMBERS.".display_name LIKE '".SP()->filters->esc_sql($wpdb->esc_like($search_term))."%', 0, IF (".SPMEMBERS.".display_name LIKE '%".SP()->filters->esc_sql($wpdb->esc_like($search_term))."%', 1, 2))";

                            # need to fool wp on request uri since our admin urls are wrong
                            $_SERVER['REQUEST_URI'].= '&s='.$search_term;
                        }

                        # do our members query
                        $query = apply_filters('sph_admin_pm_stats_query', $query);
                        $records = SP()->DB->select($query);

                        # set up page links
                        $total_items = SP()->DB->select('SELECT FOUND_ROWS()', 'var');
                        $this->set_pagination_args(array(
                            'total_items' => $total_items,
                            'per_page'    => $per_page
                        ));

                        # fill the rest of the results with needed data
                        $stats = array();
                        if ($records) {
                            foreach ($records as $idx => $data) {
                            	$pmdata = SP()->DB->select('SELECT count(distinct('.SPPMRECIPIENTS.'.thread_id)) AS thread_count, count(distinct('.SPPMRECIPIENTS.'.message_id)) AS message_count, sum(read_status) AS read_count, count('.SPPMMESSAGES.'.message_id) AS sent_count
                            			FROM '.SPPMRECIPIENTS.'
                            			LEFT JOIN '.SPPMMESSAGES.' ON '.SPPMRECIPIENTS.'.message_id = '.SPPMMESSAGES.'.message_id AND '.SPPMRECIPIENTS.'.user_id = '.SPPMMESSAGES.'.user_id
                            			WHERE '.SPPMRECIPIENTS.".user_id=$data->user_id", 'row');
                                if (empty($pmdata)) {
                                    $pmdata = new stdClass;
                                    $pmdata->message_count = 0;
                                    $pmdata->thread_count = 0;
                                    $pmdata->read_count = 0;
                                    $pmdata->sent_count = 0;
                                }

                                # now fill in the members array
            					$stats[$idx]['user_id']        = $data->user_id;
            					$stats[$idx]['user_login']     = $data->user_login;
            					$stats[$idx]['display_name']   = $data->display_name;
            					$stats[$idx]['can_pm']         = sp_pm_get_auth('use_pm', '', $data->user_id);
            					$stats[$idx]['threads']        = $pmdata->thread_count;
            					$stats[$idx]['received']       = $pmdata->message_count - $pmdata->sent_count;
            					$stats[$idx]['sent']           = $pmdata->sent_count;
            					$stats[$idx]['unread']         = $pmdata->message_count - $pmdata->read_count;
            					$stats[$idx]['messages']       = $pmdata->message_count;
                            }
                        }

                        # fill class items
                        $this->items = $stats;
                    }
                }

                # build the class
                $pmTable = new SP_PM_Table();

                # going to display, lets prep items
                $pmTable->prepare_items();
?>
                <form id="pms-filter" method="get" action="<?php echo admin_url('admin.php?page='.SP_FOLDER_NAME.'/admin/panel-plugins/spa-plugins.php'); ?>">
                    <input type="hidden" name="page" value="<?php echo SP_FOLDER_NAME.'/admin/panel-plugins/spa-plugins.php'; ?>" />
                    <input type="hidden" name="tab" value="<?php echo 'plugin'; ?>" />
                    <input type="hidden" name="admin" value="<?php echo 'sp_pm_admin_stats'; ?>" />
                    <input type="hidden" name="save" value="<?php echo SP_FOLDER_NAME.'/admin/panel-plugins/spa-plugins.php&tab=plugin&admin=sp_pm_admin_stats&save=&form=0'; ?>" />
                    <input type="hidden" name="form" value="0" />
<?php
                    # dispaly the search box
                    $pmTable->search_box(__('Search Members', 'sp-pm'), 'search_id');

                    # display the members list table
                    $pmTable->display();
?>
                </form>
<?php
			spa_paint_close_fieldset();
           	echo '<div class="sfform-panel-spacer"></div>';
		spa_paint_close_panel();

		do_action('sph_users_pm_panel');
		spa_paint_close_container();
	spa_paint_close_tab();
}
