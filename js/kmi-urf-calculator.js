jQuery(document).ready(function($){
    var urf_calc_ticks_per_sec_field = $('#kmi_urf_calc_ticks_per_sec');
    var urf_calc_ticks_delay_field = $('#kmi_urf_calc_ticks_delay');
    var urf_calc_result_field = $('#kmi_urf_calc_result');
    
    $('#kmi_calculate_urf_btn').click(function(){
        var ticks_per_sec = urf_calc_ticks_per_sec_field.val();
        var ticks_delay = urf_calc_ticks_delay_field.val();
        
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {action: 'calculate_urf', kmi_urf_calculator: {ticks_per_second: ticks_per_sec, ticks_delay: ticks_delay}},
            success: function(response){
                if(response.urf_calc_result) {
                    urf_calc_result_field.val(response.urf_calc_result);
                } else {
                    urf_calc_result_field.val('');
                }
            },
            error: function(xhr, error) {
                
            }
        });
        // No need to submit the form. Cancel the event.
        return false;
    });
    
    $('#kmi_reset_urf_btn').click(function(){
        urf_calc_ticks_per_sec_field.val('');
        urf_calc_ticks_delay_field.val('');
        urf_calc_result_field.val('');
        // No need to submit the form. Cancel the event.
        return false;
    });
});