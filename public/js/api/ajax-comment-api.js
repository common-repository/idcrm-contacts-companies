var idcrmCommentApi = (function($) {
    "use strict";
    return {

      callAjaxAddLike: function (comment_id, post_id) {
        $.ajax( {
            type : 'POST',
            url : wp_ajax_data.ajax_url,
            cache: false,
            data : {
                action: wp_ajax_comment_data.action_add_like,
                _ajax_nonce: wp_ajax_comment_data.nonce,
                comment_id : comment_id
            },
            success : function(data) {

              idcrmCommentApi.callAjaxRefreshComments(post_id);

            },
            error: function(xhr,textStatus,e) {
                console.log('callAjaxAddLike xhr.responseText: ' + xhr.responseText);
                // toastr.error(wp_ajax_toastr.strings.idcrmError);
            }
        });
      },

      updateNotifTable: function () {
        $.ajax( {
            type : 'POST',
            url : wp_ajax_data.ajax_url,
            cache: false,
            data : {
                action: wp_ajax_contact_data.action_update_table,
                _ajax_nonce: wp_ajax_contact_data.nonce
            },
            success : function(data) {

              $('#notifications-table').bootstrapTable('refreshOptions', {
                data: JSON.parse(data)
              });

            },
            error: function(xhr,textStatus,e) {
                console.log('updateNotifTable xhr.responseText: ' + xhr.responseText);
                // toastr.error(wp_ajax_toastr.strings.idcrmError);
            }
        });
      },

        setCommentsSeen: function (ids = []) {
          const jsonString = JSON.stringify(ids);
          $.ajax( {
              type : 'POST',
              url : wp_ajax_data.ajax_url,
              cache: false,
              data : {
                  action: wp_ajax_comment_data.action_set_comments_seen,
                  _ajax_nonce: wp_ajax_comment_data.nonce,
                  data: jsonString
              },
              success : function(data) {
                // console.log(data);

                  let result = '';
                  // if(data) {
                  //     try {
                  //         result = JSON.parse(data);
                  //     } catch(e) {
                  //         result = data;
                  //     }
                  // }

                  if (data == 0) {
                    // $('#unread-message-counter').text(data);
                    $('#unread-message-counter').fadeOut();
                  } else {
                    let old_value = parseInt($('#unread-message-counter').text());

                    if (old_value < data) {

                      idcrmCommentManage.playNotificationSound()

                      // $('.play-message-button').trigger('click');

                      // setTimeout(idcrmCommentManage.playNotificationSound(), 5000)

                    }
                    // $('#unread-message-counter').hide();
                    $('#unread-message-counter').fadeIn('fast');
                    $('#unread-message-counter').text(data);
                    // $('#unread-message-counter').fadeIn('fast');
                  }
                  // console.log('callAjaxEditComment result: ' + JSON.stringify(result));
              },
              error: function(xhr,textStatus,e) {
                  console.log('setCommentsSeen xhr.responseText: ' + xhr.responseText);
                  // toastr.error(wp_ajax_toastr.strings.idcrmError);
              },
              complete: function() {
                  // console.log('callAjaxEditComment complete callAjaxRefreshComments');
                  // idcrmCommentApi.callAjaxRefreshComments(post_id);
              }
          });
        },

        callAjaxSendComment: function (allData) {
            $.ajax( {
                type: 'POST',
                url: wp_ajax_data.ajax_url,
                cache: false,
                data: {
                    action: wp_ajax_comment_data.action_new,
                    _ajax_nonce: wp_ajax_comment_data.nonce,
                    post_id: allData.postId,
                    comment: allData.comment,
                    user_id: allData.currentUserId,
                    post_type: allData.postType,
                    current_time: allData.currentTime

                },
                success: function(data) {
                    let result = '';
                    if(data) {
                        try {
                            result =  JSON.stringify(JSON.parse(data));
                        } catch(e) {
                            result = data;
                        }
                    }
                    // console.log('callAjaxSendComment result: ' + result);
                    toastr.success(wp_ajax_toastr.strings.idcrmCommentSent);
                    $('#comment-textarea').val('');
                },
                error: function(xhr,textStatus,e) {
                    console.log('callAjaxSendComment xhr.responseText: ' + xhr.responseText);
                    toastr.error(wp_ajax_toastr.strings.idcrmError);
                    return;
                },
                complete: function() {
                    // console.log('callAjaxSendComment complete callAjaxRefreshComments');
                    idcrmCommentApi.callAjaxRefreshComments(allData.postId);
                    $('#send-comment-button').prop("disabled", false);
                    if ($( "#respond #ajax-loader" )) {
                        $( "#respond #ajax-loader" ).remove();
                    }
                }
            });
        },

        callAjaxRefreshComments: function (post_id, post_type = '') {

            if ($('.single-idcrm_task').length) {
              post_type = 'idcrm_task';
            } else {
              idcrmCommentManage.setRefreshCommentsPlaceholder();
            }
            // console.log('callAjaxRefreshComments');
            // console.log('callAjaxRefreshComments post_id: ' + post_id);
            $.ajax( {
                type: 'POST',
                url: wp_ajax_data.ajax_url,
                cache: false,
                data: {
                    action: wp_ajax_comment_data.action_refresh,
                    _ajax_nonce: wp_ajax_comment_data.nonce,
                    post_id: post_id,
                    post_type: post_type,
                },
                success : function( data ) {
                    $('#comments').html( $( data ) );
                    idcrmCommentManage.sortComments();
                    idcrmTimelineManage.showMoreLess();
                    idcrmCommentManage.showComments();
                    idcrmCommentManage.showFullComment();

                },
                error: function(xhr,textStatus,e) {
                    console.log('callAjaxAssignCompany xhr.responseText: ' + xhr.responseText);
                    toastr.error(wp_ajax_toastr.strings.idcrmError);
                },
                complete: function() {
                    idcrmCommentManage.unsetRefreshCommentsPlaceholder();

                    if ($('.single-idcrm_task').length) {
                        idcrmTasksUI.showhideChecklistComments();
                    }

                    if ($( "#comments #comments-placeholder" )) {
                        $( "#comments #comments-placeholder" ).remove();
                    }
                    //$( '#comments-placeholder' ).fadeOut(300, function() { $(this).remove(); });
                    //$('#comments').append(idcrmApi.commentsPlaceholder);
                }
            });
        },

        callAjaxEditComment: function (newText, post_id, eventId, isComment) {
            $.ajax( {
                type : 'POST',
                url : wp_ajax_data.ajax_url,
                cache: false,
                data : {
                    action: wp_ajax_comment_data.action_edit,
                    _ajax_nonce: wp_ajax_comment_data.nonce,
                    event_id: eventId,
                    //post_id : post_id,
                    comment_text: newText,
                    is_comment:isComment
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
                    // console.log('callAjaxEditComment result: ' + JSON.stringify(result));
                },
                error: function(xhr,textStatus,e) {
                    console.log('callAjaxEditComment xhr.responseText: ' + xhr.responseText);
                    toastr.error(wp_ajax_toastr.strings.idcrmError);
                },
                complete: function() {
                    // console.log('callAjaxEditComment complete callAjaxRefreshComments');
                    idcrmCommentApi.callAjaxRefreshComments(post_id);
                }
            });
        },

        callAjaxDeleteComment: function ( commentID, post_id ) {
            $.ajax( {
                type : 'POST',
                url : wp_ajax_data.ajax_url,
                cache: false,
                data : {
                    action: wp_ajax_comment_data.action_delete,
                    _ajax_nonce: wp_ajax_comment_data.nonce,
                    id: commentID,
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
                    // console.log('callAjaxDeleteComment result: ' + JSON.stringify(result));
                },
                error: function(xhr,textStatus,e) {
                    console.log('callAjaxDeleteComment xhr.responseText: ' + xhr.responseText);
                    toastr.error(wp_ajax_toastr.strings.idcrmError);
                },
                complete: function() {
                    // console.log('callAjaxDeleteComment complete callAjaxRefreshComments');
                    idcrmCommentApi.callAjaxRefreshComments(post_id);
                }
            });
        }

    }
})( jQuery );
