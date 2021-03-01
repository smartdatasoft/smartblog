$(document).ready(function() {

    $("#classy_update_bt").click(function() {
        $(".update-ajax-loader").show();
        var $this = $(this);
        $.ajax({
            url: sblogdown_ajaxurl,
            type: 'POST',
            data: {
                ajax: true,
                controller: 'AdminSmartblogAddons',
                action: 'DownNow',
                down_url: $this.data('down_url'),
                down_v: $this.data('down_vs'),
            },
            success: function(result) {
                var result_obj = JSON.parse(result)
                console.log(result_obj)
                if(result_obj.status=="2"){
                    $(".update-ajax-loader").hide();
                    $this.attr('data-down_url', result_obj.msg);
                    $this.text("")
                    $this.text("Update to Version "+ $this.data('down_vs'))

                }else if(result_obj.status=="1"){
                    $("#classy_update_bt").hide();
                    $(".update-ajax-loader").hide();
                    $(".update_msg").html("Your Version is Up to Date");
                    $(".update_vsn").html(result.msg);
                }else{
                    $("#classy_update_bt").hide();
                    $(".update-ajax-loader").hide();
                    $(".update_vsn").html(result.msg);
                }
            },
        });
    });

});