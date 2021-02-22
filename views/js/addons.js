$( document ).ready(function() {

    $('.smartblog_addons_install').on('click', function (e) {
		e.preventDefault();
		var $addon = $(this).data("addon_name");
		var $installed = $(this).data("installed");
	  	$.ajax({
		    type: 'POST',
		    url: sblogaddons_ajaxurl,
		    data: {
		    	ajax: true,
			    controller: controller_name,
			    action: 'ActionAddon',
			   	addon: $addon,
			   	installed: $installed,
			 },
		    success: function (result) {
		    }
		});
	});
});
