var idcrmEventApi = (function($) {
    "use strict";
    return {
        callAjaxStatus: function (allData) {
            idcrmCommentManage.setRefreshCommentsPlaceholder();
            console.log('callAjaxStatus allData.id: ' + allData.id);
            console.log('callAjaxStatus allData.commentPostID: ' + allData.commentPostID);
            $.ajax( {
                type : 'POST',
                url : wp_ajax_data.ajax_url,
                data : {
                    action : wp_ajax_event_data.action_status,
                    _ajax_nonce : wp_ajax_event_data.nonce,
                    id : allData.id,
                    status : allData.status,
                    comment_post_ID : allData.commentPostID,
                    current_user_id : allData.currentUser,
                    current_time : allData.currentTime
                },
                success : function(data) {
                    let result = '';
                    if(data) {
                        try {
                            result = JSON.parse(data);
                        } catch(e) {
                            result = data;
                        }
                    }
                    console.log('callAjaxStatus result: ' + JSON.stringify(result));
                    const toastStatus = (allData.status == 'active') ? wp_ajax_toastr.strings.idcrmActivated : wp_ajax_toastr.strings.idcrmFinished;
                    toastr.success(wp_ajax_toastr.strings.idcrmEvent + ' ' + allData.title + ' ' + toastStatus);
                },
                error: function(xhr,textStatus,e) {
                    console.log('callAjaxStatus xhr.responseText: ' + xhr.responseText);
                    toastr.error(wp_ajax_toastr.strings.idcrmError);
                },
                complete : function() {
                    console.log('callAjaxStatus complete callAjaxRefreshComments');
                    idcrmCommentApi.callAjaxRefreshComments(allData.commentPostID);
                }
            });
        },

        callAjaxNewEvent: function (allData) {
            $('#schedule').append(idcrmApi.ajaxLoader).hide().fadeIn(300);
            console.log('callAjaxNewEvent allData: ' + JSON.stringify(allData));
            $.ajax( {
                type : 'POST',
                url : wp_ajax_data.ajax_url,
                data : {
                    action : wp_ajax_event_data.action_new,
                    _ajax_nonce : wp_ajax_event_data.nonce,
                    event_date : allData.eventDate,
                    event_time : allData.eventTime,
                    post_title : allData.eventTitle,
                    event_type : allData.dataTypeId,
                    post_author : allData.currentUserId,
                    idcrm_contact_user_id : allData.contactId,
                    idcrm_event_timestring : allData.eventTimeString,
                },
                success : function(data) {
                    let result = '';
                    if(data) {
                        try {
                            result =  JSON.parse(data);
                        } catch(e) {
                            result = data;
                        }
                    }
                    // console.log('callAjaxNewEvent result: ' + JSON.stringify(result));
                    $('#mdate').val('');
                    $('#timepicker').val('');
                    $('#event_topic').val('');
                    $('.event-type-radio').each( function() {
                        $(this).prop('checked', false);
                    });
                    toastr.success(wp_ajax_toastr.strings.idcrmEventAdded);
                    $('#add-event-button').prop("disabled", false);
                    $('.schedule-card').removeClass('d-none');
                },
                error: function(xhr,textStatus,e) {
                    toastr.error(wp_ajax_toastr.strings.idcrmError);
                },
                complete : function() {
                    $( "#schedule #ajax-loader" ).fadeOut(300, function() { $(this).remove(); });
                    idcrmScheduleApi.callAjaxRefreshSchedule(allData);
                }
            });
        },

        callAjaxEditEvent: function(allData) {
          const jsonString = JSON.stringify(allData);
          $.ajax( {
              type: 'POST',
              url: wp_ajax_data.ajax_url,
              data: {
                  action: wp_ajax_event_data.action_edit_event,
                  _ajax_nonce: wp_ajax_event_data.nonce,
                  data: jsonString
              },
              success: function(data) {

                console.log(data);

                $('.edit-save-event').prop('disabled', false);
                  // location.reload();
                // $('#schedule').append(idcrmApi.ajaxLoader).hide().fadeIn(300);
              },
              error: function(xhr,textStatus,e) {
                  console.log('callAjaxEditEvent xhr.responseText: ' + xhr.responseText);
                  toastr.error(wp_ajax_toastr.strings.idcrmError);
              },
              complete : function() {
                  // $( "#schedule #ajax-loader" ).fadeOut(300, function() { $(this).remove(); });
                  idcrmScheduleApi.callAjaxRefreshSchedule(allData);
              }
          });
        },
    }
})( jQuery );
