(function(spj, $, undefined) {
	function pmAdminDelPms(url, rowid) {
		$.ajax(url).done( function() {
			$('#' + rowid + ' td:nth-child(5)').html('0');
			$('#' + rowid + ' td:nth-child(6)').html('0');
			$('#' + rowid + ' td:nth-child(7)').html('0');
			$('#' + rowid + ' td:nth-child(8)').html('0');

			$('#' + rowid + ' td:nth-child(1) .row-actions').html('&nbsp;');
		});
	}

	/***********************************************
	event handlers
	***********************************************/

	sp_pm_admin_delete_pms = {
		init : function() {
			$('#pms-filter').on('click', '.spPMAdminDeletePms', function() {
				var mydata = $(this).data();
				if (confirm(mydata.msg)) {
					pmAdminDelPms(mydata.url, mydata.user);
				}
			});
		}
	};

	/***********************************************
	load the event handlers up on document ready
	***********************************************/

	$(document).ready(function() {
		$('#sfmaincontainer').on('adminformloaded', function() {
			sp_pm_admin_delete_pms.init();
		});
	});
}(window.spj = window.spj || {}, jQuery));
