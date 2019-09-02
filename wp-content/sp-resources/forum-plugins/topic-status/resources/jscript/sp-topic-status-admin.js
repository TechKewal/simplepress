jQuery(document).ready(function(){

		jQuery(document).on("click",".is_default_status", function(){

			var status_cls = jQuery(this).attr("class");
			var status_cls_1 = status_cls.replace("is_default_status","").trim();

			jQuery("."+status_cls_1).attr("checked", false);
			jQuery(this).attr("checked", true);
		});

	});