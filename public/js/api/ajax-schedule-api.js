var idcrmScheduleApi = (function($) {
    "use strict";
    return {
        callAjaxRefreshSchedule: function (allData) {
            $('#schedule-container').append(idcrmApi.ajaxSmallLoader).hide().fadeIn(300);
            $.ajax( {
                type: 'POST',
                url: wp_ajax_data.ajax_url,
                data: {
                    action: wp_ajax_schedule_data.action,
                    _ajax_nonce: wp_ajax_schedule_data.nonce,
                    post_id: allData.postId,
                },
                success: function( data ) {
                    $('#schedule-container').html( $( data ) );
                    idcrmScheduleManage.sortAndSlice();
                    idcrmEventManage.checkAndDelete();
                },
                error: function(xhr,textStatus,e) {
                    console.log('callAjaxEditNote xhr.responseText: ' + xhr.responseText);
                    toastr.error(wp_ajax_toastr.strings.idcrmError);
                },
                complete: function() {
                    $( "#ajax-small-loader" ).fadeOut(300, function() { $(this).remove(); });
                }
            });
        }
    }
})( jQuery );