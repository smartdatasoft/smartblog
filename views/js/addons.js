$( document ).ready(function() {

    $('.smartblog_addons_install').on('click', function (e) {
		e.preventDefault();
		var $addon = $(this).data("addon_name");
		var $installed = $(this).data("installed");
		var $this = $(this);
		$('.ajax-loader-wrapper').show()
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
				$this.text("");
				$this.attr("data-installed", result);
				if(result == '0'){
					$this.text("Install");
				}else{
					$this.text("Uninstall");
				}
				$('.ajax-loader-wrapper').hide()
		    }
		});
	});
});
