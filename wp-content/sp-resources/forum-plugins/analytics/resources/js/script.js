(function(spj, $, undefined) {
        
        
        $( function() {
                
                /**
                 * Init datepicker on dashboard
                 */
                if( pagenow === 'dashboard' ) {
                        $('.sp-datepicker-field').datepicker({
                                beforeShow: function(input, inst) {
                                        $("#ui-datepicker-div").addClass("sp-datepicker");
                                }
                        });
                }
                
                /**
                 * Update chart once any date range option selected
                 */
                $('body').delegate( '.sp_chart .range_options a', 'click', function( e ) {
                        
                        e.preventDefault();
                        
                        $(this).closest( '.range_options').find('a').removeClass('chart_range_option_active');
                        $(this).addClass('chart_range_option_active');
                        
                        var chart_id = $(this).closest('.sp_chart').data('chart_id');
                        var url = $(this).closest( '.sp_chart' ).data(url);
                        var data = new Array();
                        
                        data.push({name:'option', value : $(this).data('option')});
                        
                        update_chart( chart_id , url, data );
                        
                });
                
                /**
                 * Update chart by custom date range
                 */
                $('.filter_cr_chart').on( 'click', function(e) {
                        e.preventDefault();
                        
                        var range_ele = $(this).closest('.date_range');
                        
                        var range_start = range_ele.find( 'input.date_start').val();
                        var range_end = range_ele.find('input.date_end').val();
                        
                        if( !range_start || !range_end ) {
                                return;
                        }
                        
                        var chart_id = $(this).closest('.sp_chart').data('chart_id');
                        var url = $(this).closest( '.sp_chart' ).data(url);
                        var data = new Array();
                        data.push({name:'option', value : 'cr_'+prepare_date(range_start)+'_'+prepare_date(range_end) });
                        
                        update_chart( chart_id , url, data );
                })
                
        });
        
        /**
         * Format datepicker date for ajax data
         * 
         * @param string str
         * 
         * @returns String
         */
        function prepare_date( str ) {
                
                var date  = new Date( str );
                
                return date.getDate() + '-' + ( date.getMonth() + 1 ) + '-' + date.getFullYear();
        }
        
        /**
         * Update chart with new datapoints
         * 
         * @param string chart_id
         * @param string url
         * @param array data
         * 
         * @returns void
         */
        function update_chart( chart_id, url, data ) {
                $.post( url, data, function(res) {
                        
                        window[ chart_id ].options = res.data.options;
                        
                        var _data_points = res.data.is_date_dependent ? spj.analytics_prepare_date_chart_data( res.data.dps, res.data.y_int ) : res.data.dps;
                        window[ chart_id ].options.data[0]['dataPoints'] = _data_points;
                        
                        window[ chart_id ].render();
                        
                        if( res.data.date_range ) {
                                spj.update_date_range( chart_id, res.data.date_range );
                        }
                        
                });
                
                
        }
        
        /**
         * Update date range input fields once a date range option is selected or on page load
         * 
         * @param string chart_id
         * @param object range
         * 
         * @returns void
         */
        spj.update_date_range = function ( chart_id, range ) {
                
                var chart_ele = $('.sp_chart[data-chart_id='+chart_id+']');
                
                chart_ele.find('.date_start').val(range.start_date_dp);
                chart_ele.find('.date_end').val(range.end_date_dp);
        }
        
        /**
         * Prepare chart datapoints For date dependent charts
         * 
         * @param string data
         * @param boolean int
         * 
         * @returns Array
         */
        spj.analytics_prepare_date_chart_data = function( data, int ) {
                
                var _data = new Array();
                $.each( data , function() {
			_data.push({
                                x : new Date( this.x ),
                                y : int ? parseInt( this.y ) : this.y
                        });
                });
                
                return _data;
        }
        
}(window.spj = window.spj || {}, jQuery));
