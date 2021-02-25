$(document).ready(function() {

    $("#classy_update_bt").click(function() {
        $(".update-ajax-loader").show();
        $.ajax({
            url: sblogdown_ajaxurl,
            type: 'POST',
            data: {
                ajax: true,
                controller: 'AdminSmartblogAddons',
                action: 'DownNow',
                down_url: $(this).data('down_url'),
                down_v: $(this).data('down_vs'),
            },
            success: function(result) {
                $("#classy_update_bt").hide();
                $(".update-ajax-loader").hide();
                $(".update_msg").html("Your Version is Up to Date");
                $(".update_vsn").html(result);
            },
        });
    });

});