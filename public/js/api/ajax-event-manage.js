var idcrmEventManage = (function($) {
    "use strict";
    return {

        checkAndDelete: function () {
            $('.check-delete-event').on('click', function() {
              if (!$(this).hasClass('editing')) {
                const allData = new Array();
                // const checkId = $(this).attr('id');
                allData.id = $(this).attr('data-id');
                allData.title = $('.title-' + allData.id).text();
                allData.author = $(this).attr('data-author');
                allData.commentPostID = $(this).attr('data-comment-post-id');
                allData.currentUser = $(this).attr('data-current-user-id');
                allData.currentTime = moment().format("YYYY-MM-DD HH:mm:ss");

                $('#wrapper-event-' + allData.id).toggleClass('deleted');
                // $('#edit-author-events').attr('href', adminUrl + 'edit.php?post_type=contact_event&idcrm_contact_user_id=' + author);
                allData.status = $('#wrapper-event-' + allData.id).hasClass('deleted') ? 'finished' : 'active';
                // if (status === 'finished') {
                // console.log(allData);
                idcrmEventApi.callAjaxStatus(allData);
                //setTimeout( function() {
                //if (allData.status === 'finished' || allData.status === 'active') {
                    //$('#comments').append(idcrmApi.commentsPlaceholder);
                    //console.log('checkAndDelete complete callAjaxRefreshComments');
                    //idcrmCommentApi.callAjaxRefreshComments(allData.commentPostID);
                //}
                //},2000);
              }

            });
        },

        addEventButton: function () {
            $('#add-event-button').on('click', function() {
                const allData = new Array();
                allData.eventDate = $('#mdate').val() ? $('#mdate').val() : false;
                allData.eventTime = $('#timepicker').val() ? $('#timepicker').val() : false;
                const [year, month, day] = $('#mdate').val().split('-');
                const [hours, minutes] = $('#timepicker').val().split(':');
                const dateTime = (allData.eventDate && allData.eventTime) ? new Date(+year, month - 1, +day, +hours, +minutes) : false;
                allData.eventTimeString = dateTime ? moment(dateTime).utc(moment().utcOffset()).valueOf() / 1000 : false;

                // let eventTime = new Date(allData.eventDate + 'T' + allData.eventTime);
                // allData.eventTimeString = eventTime.getTime() / 1000;

                allData.eventTitle = $('#event_topic').val() ? $('#event_topic').val() : false;
                allData.contactId = $(this).attr('data-contact-id');
                allData.postId = parseInt($(this).attr('data-post-id'),10);
                allData.currentUserId = parseInt($(this).attr('data-current-user-id'),10);
                let dataTypeId = $('input[name=event_type]:checked', '#add_event').attr('data-type-id');
                allData.dataTypeId = parseInt(dataTypeId,10);
                if (allData.eventDate && allData.eventTime && allData.eventTitle && allData.dataTypeId) {
                    $('#add-event-button').prop("disabled", true);
                    idcrmEventApi.callAjaxNewEvent(allData);
                } else {
                    toastr.error(wp_ajax_toastr.strings.idcrmSendError);
                }
            });
        },

        editEventsButton: function () {
          $('#edit-author-events').on('click', function(e) {
            e.preventDefault();
            $('.check-delete-event').addClass('editing');
            $('.check-delete-event').addClass('d-none');
            $('.edit-event-icon').removeClass('d-none');
          });
        },

        editEventButton: function () {
          $('.edit-event-icon').on('click', function(e) {
            const timestring = $(this).attr('data-timestring');
            // const momentDate = moment.unix(timestring);
            // const formattedDate = momentDate.format('DD.MM.YYYY');
            // const formattedTime = momentDate.format('HH:mm');

            $('#edit_event_topic').val($(this).attr('data-title'));
            $('#edit_contact_events').val($(this).attr('data-type'));
            $('#edit_event_date').val($(this).attr('data-date'));
            $('#edit_event_time').val($(this).attr('data-time'));
            $('.edit-save-event').attr('data-event-id', $(this).attr('data-event-id'));
          });
        },

        editEventSaveButton: function () {
          $('.edit-save-event').on('click', function(e) {

            e.preventDefault();
            $(this).prop('disabled', true);

            const momentDate = moment($('#edit_event_date').val() + ' ' + $('#edit_event_time').val(), 'DD.MM.YYYY HH:mm');
            const timestamp = momentDate.unix();

            const allData = {};
            allData.edit_event_topic = $('#edit_event_topic').val();
            allData.edit_event_type = $('#edit_contact_events').val();
            allData.edit_event_date = $('#edit_event_date').val();
            allData.edit_event_time = $('#edit_event_time').val();
            allData.edit_event_timestring = timestamp;
            allData.event_id = $(this).attr('data-event-id');
            allData.postId = parseInt($(this).attr('data-post-id'),10);

            if (allData.edit_event_topic && allData.edit_event_timestring && allData.event_id) {
                $('#editEventModal').modal('toggle');
                idcrmEventApi.callAjaxEditEvent(allData);
            } else {
                toastr.error(wp_ajax_toastr.strings.idcrmSendError);
                $(this).prop('disabled', false);
            }

          });
        },
    }
})( jQuery );
