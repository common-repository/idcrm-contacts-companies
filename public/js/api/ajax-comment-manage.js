var idcrmCommentManage = (function($) {
    "use strict";
    return {

      addLike: function () {
          $(document).on("click", '.comment-likes-add', function(e) {
              e.preventDefault();

              if (!$(this).hasClass('disabled')) {
                const comment_id = $(this).attr('data-comment-id');
                const post_id = $('#task-timer').attr('data-post-id');
                $(this).addClass('liked');

                if (comment_id) {
                  // console.log(comment_id);
                  $('.comment-likes-add').addClass('disabled');
                  idcrmCommentApi.callAjaxAddLike(comment_id, post_id);
                }
              }


          });
      },

        checkCommentsTimer: function () {

          // $(document).on('focus', function() {
            var timerInterval;
            clearInterval(timerInterval);

            timerInterval = setInterval(function() {
              // console.log('time');

                if ($('#comments').length) {
                  $('#comments .comment-item').each(function() {
                    if (!$(this).hasClass('invisible')) {
                      $(this).removeClass('not-seen');
                    }
                  });
                }

                idcrmCommentApi.setCommentsSeen();
              }, 30000);
          // });

        },

        playNotificationSound: function () {

          // $('.play-message-button').on('click', function() {
            const message_sound = $("#message-audio")[0];
            message_sound.play();
          // });

        },

        refreshNotificationsTimer: function () {

          if ($('#notifications-table').length) {

            var notifTimerInterval;
            clearInterval(notifTimerInterval);

            notifTimerInterval = setInterval(function() {
              idcrmCommentApi.updateNotifTable();
            }, 30000);
          }
        },

        setUnseenCommentsCounter: function () {
          if ($('#unread-message-counter').length) {
            if (wp_ajax_comment_data.unread_comments > 0) {
              $('#unread-message-counter').text(wp_ajax_comment_data.unread_comments);
              $('#unread-message-counter').fadeIn();
            }
          }
        },

        getUnseenComments: function () {
          if ($('#comments').length) {

            const ids = [];
            setTimeout(() => {
              $('#comments .comment-item').each(function() {
                if (!$(this).hasClass('invisible')) {
                  const comment_id = $(this).attr('data-id');
                  const is_seen = $(this).attr('data-is-seen');

                  $(this).removeClass('not-seen');

                  if (!is_seen) {
                    ids.push(comment_id);
                  }

                }
              });

              if (ids.length > 0) {
                idcrmCommentApi.setCommentsSeen(ids);
              }

            }, 2000);
          }
        },

        getUnseenCommentsTab: function () {
          if ($('#comments').length) {
            $('.nav-tabs .nav-item').on('click', function() {

              const ids = [];

              setTimeout(() => {
                $('#comments .comment-item').each(function() {
                  if (!$(this).hasClass('invisible')) {
                    const comment_id = $(this).attr('data-id');
                    const is_seen = $(this).attr('data-is-seen');

                    $(this).removeClass('not-seen');

                    if (!is_seen) {
                      ids.push(comment_id);
                    }

                  }
                });

                if (ids.length > 0) {
                  idcrmCommentApi.setCommentsSeen(ids);
                }

              }, 2000);
            });
          }
        },

        setRefreshCommentsPlaceholder: function () {
            if ($( '#comments-placeholder' ).length == 0) {
                $('#comments').append(idcrmApi.commentsPlaceholder).hide().fadeIn(300);
            }
        },

        unsetRefreshCommentsPlaceholder: function () {
            $( '#comments-placeholder' ).fadeOut(300, function() { $(this).remove(); });
        },

        sortComments: function () {
            const $item = $('#comments'),
            $itemli = $item.children('.timeline');
            $itemli.sort(function(a,b){
                var an = a.getAttribute('data-timestring'),
                    bn = b.getAttribute('data-timestring');
                if(an > bn) {
                    return -1;
                }
                if(an < bn) {
                    return 1;
                }
                return 0;
            });
            $itemli.detach().appendTo($item);

        },

        editComment: function () {
            $(document).on('click', '.edit-comment', function(event) {
                event.preventDefault();

                const commentID = $(this).attr('data-id');

                const eventId = $(this).attr('data-event-id') ? $(this).attr('data-event-id') : '';

                const isComment = $(this).attr('data-is-comment') == "1" ? true : false;

                const commentText = $( ".current-comment-" + commentID + " .comment-text" ).html() ? $( ".current-comment-" + commentID + " .comment-text" ).html() : '';

                $( ".current-comment-" + commentID + " .comment-edit-area" ).html(
                    '<textarea class="form-control current-comment-textarea" id="current-comment-textarea-' + commentID + '" name="current-comment" aria-required="true">'
                        + (isComment ? commentText : "")
                    + '</textarea>'
                    + '<i class="btn1 waves-effect waves-light btn-rounded btn-outline-info btn-comment wp-block-button__link waves-input-wrapper">'
                    + '<input name="submit" type="button" id="save-comment-button" class="waves-button-input cancel-button-' + commentID + '" data-post-type="user_contact" value="' + wp_ajax_toastr.strings.idcrmCancel + '">'
                    + '</i>' + ' '
                    + '<i class="btn1 waves-effect waves-light btn-rounded btn-outline-info btn-comment wp-block-button__link waves-input-wrapper">'
                    + '<input name="submit" type="button" id="save-comment-button" class="waves-button-input save-button-' + commentID + '" data-post-type="user_contact" value="' + wp_ajax_toastr.strings.idcrmSave + '">'
                    + '</i>'
                );

                $('#current-comment-textarea-' + commentID).css('height', $( ".current-comment-" + commentID + " .timeline-body" ).height());
                $('#current-comment-textarea-' + commentID).focus();

                var textLength = $('#current-comment-textarea-' + commentID).val().length;
                $('#current-comment-textarea-' + commentID).prop('selectionStart', textLength);
                $('#current-comment-textarea-' + commentID).prop('selectionEnd', textLength);

                $('.cancel-button-' + commentID).on('click', function(event) {
                    event.preventDefault();
                    $( ".current-comment-" + commentID + " .comment-edit-area" ).html('');
                });

                $('.save-button-' + commentID).on('click', function(event) {
                    event.preventDefault();
                    const newText = isComment ? $( "#current-comment-textarea-" + commentID).val() : commentText + "\n" + $( "#current-comment-textarea-" + commentID).val();

                    $('.current-comment-' + commentID + ' .timeline-panel').append(idcrmApi.ajaxSmallLoader);
                    const post_id = $('#comments-container-id').attr('data-post-id');
                    idcrmCommentApi.callAjaxEditComment( newText, post_id, eventId, isComment);
                });
            });
        },

        deleteComment: function () {
            $(document).on('click', '.delete-comment', function(event) {
                event.preventDefault();

                const commentID = $(this).attr('data-id');

                if (commentID) {
                    const post_id = $('#comments-container-id').attr('data-post-id');
                    idcrmCommentApi.callAjaxDeleteComment( commentID, post_id );
                }
            });
        },

        sendCommentButton: function () {
            $( '#comment-textarea' ).focus( function() {
                if ( !$( this ).hasClass( 'active' ) ) {
                    $( this ).addClass( 'active' );
                }
            } );

            $( '#send-comment-button' ).on( 'click', function() {
                const allData = new Array();

                allData.comment = $( '#comment-textarea' ).val() ? $( '#comment-textarea' ).val() : false;
                allData.postType = $( this ).data( 'post-type' );
                allData.postId = $( this ).data( 'post-id' );
                allData.currentUserId = $( this ).data( 'current-user-id' );
                allData.currentTime = moment().format("YYYY-MM-DD HH:mm:ss");

                if (
                    allData.comment &&
                    allData.postId &&
                    allData.currentUserId
                ) {
                    $( '#comment-textarea' ).removeClass( 'active' );
                    $( '#send-comment-button' ).prop( 'disabled', true );
                    $( '#respond' ).append( idcrmApi.ajaxLoader );

                    idcrmCommentApi.callAjaxSendComment( allData );
                } else {
                    toastr.error( wp_ajax_toastr.strings.idcrmSendError );
                }
            } );
        },

        showFullComment: function () {
          $( '.show-more-text' ).on( 'click', function() {
            const commentId = $(this).attr('data-id');
            // $('.cut-comment-text[data-id=' + commentId + ']').css({'maxHeight': 'unset'});

            const cutCommentText = $('.cut-comment-text[data-id="' + commentId + '"]');

            if (!cutCommentText.hasClass('expanded')) {
              cutCommentText.css({
                'maxHeight': 'unset',
                'marginBottom': '15px'
              });
              cutCommentText.addClass('expanded');
            } else {
              cutCommentText.css('maxHeight', '250px');
              cutCommentText.css({
                'maxHeight': '250px',
                'marginBottom': '0'
              });
              cutCommentText.removeClass('expanded');
            }

          });
        },

        showComments: function () {
          $('#comments .comment-item').each(function() {
            const showMoreText = $(this).find('.show-more-text');

              if ($(this).height() < 330) {
                showMoreText.css('display', 'none');
              } else {
                showMoreText.css('display', 'block');
              }
          });
        }
    }
})( jQuery );
