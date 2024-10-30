var idcrmMailManage = (function($) {
    "use strict";
    return {
        saveDraftIcon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-send feather-sm fill-white"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>',
        
        /*refreshIcon: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M1 4v6h6"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></g></svg>',*/

        recoverIcon: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8m-4-6l-4-4l-4 4m4-4v13"/></svg>',

        starIcon: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star feather-sm fill-white"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>',

        spamIcon: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-slash feather-sm fill-white"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>',

        trashIcon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 feather-sm fill-white"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>',

        /*notspamIcon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 feather-sm fill-white"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>',*/

        fullscreenIcon: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"></path></svg>',

        normalscreenIcon: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 14h6v6m10-10h-6V4m0 6l7-7M3 21l7-7"/></svg>',

        setCookie: function (name,value,days) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days*24*60*60*1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "")  + expires + "; path=/";
        },
        getCookie: function (name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for(var i=0;i < ca.length;i++) {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
            }
            return null;
        },
        /*clearCookie: function (name) {
            document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
        },*/
        callAjaxSendEmail: function(allData) {
            $.ajax( {
                type : 'POST',
                url : wp_ajax_data.ajax_url,
                data : {
                    action : 'idcrm_ajax_send_email',
                    security_send_email : security_send_email,
                    post_id : allData.postId,
                    subject : allData.subject,
                    email_text : allData.email_text,
                    user_id : allData.currentUserId,
                    post_type : allData.postType,
                    contact_user_id : allData.contactUserId,

                },
                success : function() {

                    toastr.success(wp_ajax_toastr.strings.idcrmEmailSent);

                    $('#comments').append(idcrmApi.commentsPlaceholder);

                    $('#subject').val('');
                    $('#email_text').val('');

                    setTimeout( function() {
                        idcrmCommentApi.callAjaxRefreshComments(allData.postId);
                        $('#send-email').prop("disabled", false);
                        if ($( "#profile2 #ajax-loader" )) {
                            $( "#profile2 #ajax-loader" ).remove();
                        }
                    },2000);

                },
                error: function(xhr,textStatus,e) {
                    console.log(xhr,textStatus,e);
                    toastr.error(wp_ajax_toastr.strings.idcrmError);
                    return;
                }
            });
        },
        sendEmailButton: function() {
            $('#send-email').on('click', function() {
                const allData = new Array();
                allData.subject = $('#subject').val() ? $('#subject').val() : false;
                allData.email_text = $('#email_text').val() ? $('#email_text').val() : false;
                allData.currentUserId = parseInt($(this).attr('data-current-user-id'),10);
                allData.postId = parseInt($(this).attr('data-post-id'),10);
                allData.postType = $(this).attr('data-post-type');
                allData.contactUserId = $(this).attr('data-contact-user-id');
    
                if (allData.subject && allData.email_text && allData.currentUserId) {
    
                        if ($('#send-email').attr('data-user-test-confirm') == "1") {
                            $('#send-email').prop("disabled", true);
                            $('#profile2').append(idcrmApi.ajaxLoader);
    
                            idcrmMailManage.callAjaxSendEmail(allData);
                        } else {
                            toastr.error(wp_ajax_toastr.strings.idcrmErrorTestConfirm);
                        }
    
                } else {
    
                    toastr.error(wp_ajax_toastr.strings.idcrmSendError);
    
                }
    
            });
        },
        refreshMail: function() {
            $('.button-refresh').on('click', function() {
                $(this).prop("disabled", true);
                $(this).html(idcrmMail.mailLoaderSpinner);
                location.reload();
            });
        },
        checkboxMailOnClick: function() {
            $('.checkable-div').each( function() {
                $(this).on('click', function(event) {
                    if ($(this).parent().find('.mail-item-checkbox').is(':checked')) {
                        $(this).parent().find('.mail-item-checkbox').prop('checked', false);
                    } else {
                        $(this).parent().find('.mail-item-checkbox').prop('checked', true);
                    }
                    return false;
                });
            });
        },
        bindMailFolderClick: function() {
            $('.show-mail-folder').each( function() {
                // console.log('bind');
                $(this).bind('click', openMailFolder(false, true) );
            });
        },
        openMailFolder: function(folder, stopAction) {
            if (!stopAction) {
                if (getCookie('folder')) {
                    $('.show-mail-folder').each( function() {
                        $(this).removeClass('active');
                    });
    
                    const allData = {};
                    allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                    allData.show_folder = getCookie('folder');
    
                    $('[data-folder="' + allData.show_folder + '"]').addClass('active');
                    $('[data-folder="' + allData.show_folder + '"]').children('.mail-loader-position').html(idcrmMail.mailLoaderSpinner);
    
                    $('#folder-mail-list').append(idcrmMail.emaillistPlaceholder);
    
                    callAjaxGetFolder(allData);
                } else {
                    const allData = {};
                    allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
    
                    $('#folder-mail-list').append(idcrmMail.emaillistPlaceholder);
    
                    callAjaxGetFolder(allData);
    
                }
            }
    
            $('.show-mail-folder').each( function() {
    
                $(this).on('click', function(event) {
    
                    if (!$(this).hasClass('lastcklicked')) {
    
                        $('.show-mail-folder').each( function() {
                            $(this).removeClass('active');
                            $(this).removeClass('lastcklicked');
                            $('#mail-folder-loader').remove();
                            $(this).unbind('click');
                        });
    
                        $(this).addClass('active');
                        $(this).addClass('lastcklicked');
    
                        $(this).children('.mail-loader-position').hide().html(idcrmMail.mailLoaderSpinner).fadeIn('slow');
    
                        event.preventDefault();
                        // $(this).off(event);
    
                        const allData = {};
                        allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                        allData.show_folder = $(this).attr('data-folder');
    
                        setCookie("folder", allData.show_folder, 10);
    
                        if (allData.current_user_id) {
    
                            // $('#folder-mail-list').hide().append(emaillistPlaceholder).fadeIn('slow');
    
                            callAjaxGetFolder(allData);
    
                        } else {
    
                            toastr.error(wp_ajax_toastr.strings.idcrmSendError);
    
                        }
    
                        return false;
    
                    }
    
                });
            });
    
            $('.choose-folder').each( function() {
    
                $(this).on('click', function(event) {
    
                    if (!$(this).hasClass('lastcklicked')) {
    
                        $('.choose-folder').each( function() {
                            $(this).removeClass('active');
                            $(this).removeClass('lastcklicked');
                            $('#mail-folder-loader').remove();
                            $(this).unbind('click');
                        });
    
                        $(this).addClass('active');
                        $(this).addClass('lastcklicked');
    
                        // $(this).children('.mail-loader-position').hide().html(mailLoaderSpinner).fadeIn('slow');
    
                        event.preventDefault();
                        // $(this).off(event);
    
                        const allData = {};
                        allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                        allData.show_folder = $(this).attr('data-dropdown-folder');
    
                        setCookie("folder", allData.show_folder, 10);
    
                        if (allData.current_user_id) {
    
                            // $('#folder-mail-list').hide().append(emaillistPlaceholder).fadeIn('slow');
                            // $('.folder-dropdown').toggleClass('show');
    
                                setTimeout(function() {callAjaxGetFolder(allData)}, 500 );
                                // location.reload();
    
                        } else {
    
                            toastr.error(wp_ajax_toastr.strings.idcrmSendError);
    
                        }
    
                        // return false;
    
                    }
    
                });
            });
    
        },
        changeMailAttr: function () {
            $('.mail-flagged').each( function() {
                $(this).on('click', function() {
                    const allData = {};
    
                    allData.flagged = parseInt($(this).parent().attr('data-flagged'), 10);
                    allData.msg_id = parseInt($(this).parent().attr('data-id'), 10);
                    allData.show_folder = $('#email-list-table').attr('data-current-folder');
                    allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                    allData.current_server = $('#email-list-table').attr('data-current-server');
                    allData.action = 'flagged';
                    allData.current_mailbox = $('#compose_mail').attr('data-mailbox');
    
                    $(this).html('<div>' + idcrmMail.mailLoaderSpinner + '</div>');
                    $(this).unbind("click");
    
                    callAjaxChangeMailAttr(allData);
    
                    // console.log(allData.flagged, allData.msg_id);
                    return false;
                });
            });
    
            $('.mail-seen').each( function() {
                $(this).on('click', function() {
                    const allData = {};
    
                    allData.flagged = parseInt($(this).parent().attr('data-seen'), 10);
                    allData.msg_id = parseInt($(this).parent().attr('data-id'), 10);
                    allData.show_folder = $('#email-list-table').attr('data-current-folder');
                    allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                    allData.current_server = $('#email-list-table').attr('data-current-server');
                    allData.action = 'seen';
                    allData.current_mailbox = $('#compose_mail').attr('data-mailbox');
    
                    $(this).html('<div>' + idcrmMail.mailLoaderSpinner + '</div>');
                    $(this).unbind("click");
    
                    callAjaxChangeMailAttr(allData);
    
                    // console.log(allData.flagged, allData.msg_id);
                    return false;
                });
            });
        },
        multiSpamRecoverMessage: function () {
            $('.mail-spamrecover-one').each( function() {
                $(this).unbind("click").on('click', function() {
    
                    const allData = {
                        message_ids: []
                    };
    
                    allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                    allData.current_server = $('#email-list-table').attr('data-current-server');
                    allData.show_folder = $('#email-list-table').attr('data-current-folder');
                    allData.inbox_server = $('#email-list-table').attr('data-inbox-server');
                    allData.message_ids.push($(this).attr('data-message-id'));
                    allData.current_mailbox = $('#compose_mail').attr('data-mailbox');
    
                    if (allData.message_ids.length > 0) {
                        $(this).addClass('text-light');
                        $(this).prop("disabled", true);
                        $(this).html(idcrmMail.mailLoaderSpinner);
                        console.log(allData);
                        callAjaxSpamRecoverMessage(allData);
                    }
    
                    return false;
                });
            });
    
            $('.mail-spamrecover-maillist').unbind("click").on('click', function() {
    
                    const allData = {
                        message_ids: []
                    };
    
                    allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                    allData.current_server = $('#email-list-table').attr('data-current-server');
                    allData.show_folder = $('#email-list-table').attr('data-current-folder');
                    allData.inbox_server = $('#email-list-table').attr('data-inbox-server');
                    allData.current_mailbox = $('#compose_mail').attr('data-mailbox');
    
                    $('.mail-item-checkbox').each( function() {
                        if ($(this).is(':checked')) {
                            allData.message_ids.push($(this).attr('data-id'));
                        }
                    });
    
                    if (allData.message_ids.length > 0) {
                        $(this).addClass('text-light');
                        $(this).prop("disabled", true);
                        $(this).html('<div>' + idcrmMail.mailLoaderSpinner + '</div>');
                        console.log(allData);
                        callAjaxSpamRecoverMessage(allData);
                        $(this).removeClass('text-light');
                        $(this).prop("disabled", false);
                    } else {
                        toastr.error(wp_ajax_toastr.strings.idcrmNothingChecked);
                    }
    
            });
    
        },
        callAjaxSpamRecoverMessage: function(allData) {
            const jsonString = JSON.stringify(allData);
    
            $.ajax( {
                type : 'POST',
                url : wp_ajax_data.ajax_url,
                data : {
                    action : 'idcrm_ajax_mail_spamrecover',
                    security_mail_spamrecover : security_mail_spamrecover,
                    data : jsonString,
    
                },
                success : function(response) {
    
                    console.log('spamrecover', response);
                    // toastr.success('Starred');
                    callAjaxGetFolder(allData);
                    // allData = {};
    
                },
                error: function(xhr,textStatus,e) {
    
                    console.log(xhr,textStatus,e);
                    toastr.error(wp_ajax_toastr.strings.error);
    
                    return;
                }
            });
        },
        multiSpamMessage: function () {
            $('.mail-spam-one').each( function() {
                $(this).unbind("click").on('click', function() {
    
                    const allData = {
                        message_ids: []
                    };
    
                    allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                    allData.current_server = $('#email-list-table').attr('data-current-server');
                    allData.show_folder = $('#email-list-table').attr('data-current-folder');
                    allData.spam_server = $('#email-list-table').attr('data-spam-server');
                    allData.message_ids.push($(this).attr('data-message-id'));
                    allData.current_mailbox = $('#compose_mail').attr('data-mailbox');
    
                    if (allData.message_ids.length > 0) {
                        $(this).addClass('text-light');
                        $(this).prop("disabled", true);
                        $(this).html(idcrmMail.mailLoaderSpinner);
                        console.log(allData);
                        callAjaxSpamMessage(allData);
                    }
    
                    return false;
                });
            });
    
            $('.mail-spam-maillist').unbind("click").on('click', function() {
    
                    const allData = {
                        message_ids: []
                    };
    
                    allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                    allData.current_server = $('#email-list-table').attr('data-current-server');
                    allData.show_folder = $('#email-list-table').attr('data-current-folder');
                    allData.spam_server = $('#email-list-table').attr('data-spam-server');
                    allData.current_mailbox = $('#compose_mail').attr('data-mailbox');
    
                    $('.mail-item-checkbox').each( function() {
                        if ($(this).is(':checked')) {
                            allData.message_ids.push($(this).attr('data-id'));
                        }
                    });
    
                    if (allData.message_ids.length > 0) {
                        $(this).addClass('text-light');
                        $(this).prop("disabled", true);
                        $(this).html('<div>' + idcrmMail.mailLoaderSpinner + '</div>');
                        console.log(allData);
                        callAjaxSpamMessage(allData);
                        $(this).removeClass('text-light');
                        $(this).prop("disabled", false);
                    } else {
                        toastr.error(idcrm_contacts.strings.idcrmNothingChecked);
                    }
    
            });
    
        },
        callAjaxSpamMessage: function(allData) {
            const jsonString = JSON.stringify(allData);
    
            $.ajax( {
                type : 'POST',
                url : wp_ajax_data.ajax_url,
                data : {
                    action : 'idcrm_ajax_mail_spam',
                    security_mail_spam : security_mail_spam,
                    data : jsonString,
    
                },
                success : function(response) {
    
                    console.log('spam', response);
                    // toastr.success('Starred');
                    callAjaxGetFolder(allData);
                    // allData = {};
    
                },
                error: function(xhr,textStatus,e) {
    
                    console.log(xhr,textStatus,e);
                    toastr.error(idcrm_contacts.strings.error);
    
                    return;
                }
            });
        },
        moveMessage: function () {
            $('.mail-trashrecover-one').find('.move-item').each( function() {
                $(this).on('click', function(event) {
    
                    event.preventDefault;
                    const allData = {
                        message_ids: [],
                        message_uids: []
                    };
    
                    allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                    allData.current_server = $('#email-list-table').attr('data-current-server');
                    allData.drafts_server = $('#email-list-table').attr('data-drafts-server');
                    allData.inbox_server = $('#email-list-table').attr('data-inbox-server');
                    allData.sent_server = $('#email-list-table').attr('data-sent-server');
                    allData.show_folder = $('#email-list-table').attr('data-current-folder');
                    allData.message_ids.push($(this).attr('data-message-id'));
                    allData.message_uids.push($(this).attr('data-message-uid'));
                    allData.action = $(this).attr('data-value');
                    allData.current_mailbox = $('#compose_mail').attr('data-mailbox');
    
                    // $(this).parent('.dropdown-menu').dropdown("hide");
    
                    $('.mail-trashrecover-one .move-item').each( function() {
                        $(this).unbind("click");
                    });
    
                    // $(this).unbind("click");
    
                    if (allData.message_ids.length > 0) {
                        // $(this).addClass('text-light');
                        // $(this).prop("disabled", true);
                        // $(this).html(mailLoaderSpinner);
                        console.log(allData);
                        callAjaxMoveMessage(allData);
                    }
    
                    return false;
                });
            });
    
            $('.mail-trashrecover-maillist').find('.move-item').unbind("click").on('click', function() {
    
                    const allData = {
                        message_ids: [],
                        message_uids: []
                    };
    
                    allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                    allData.current_server = $('#email-list-table').attr('data-current-server');
                    allData.drafts_server = $('#email-list-table').attr('data-drafts-server');
                    allData.inbox_server = $('#email-list-table').attr('data-inbox-server');
                    allData.sent_server = $('#email-list-table').attr('data-sent-server');
                    allData.show_folder = $('#email-list-table').attr('data-current-folder');
                    allData.action = $(this).attr('data-value');
                    allData.current_mailbox = $('#compose_mail').attr('data-mailbox');
    
                    $('.mail-item-checkbox').each( function() {
                        if ($(this).is(':checked')) {
                            allData.message_ids.push($(this).attr('data-id'));
                            allData.message_uids.push($(this).attr('data-uid'));
                        }
                    });
    
                    if (allData.message_ids.length > 0) {
                        // $(this).addClass('text-light');
                        $('.mail-trashrecover-button').prop("disabled", true);
                        // $(this).html('<div>' + mailLoaderSpinner + '</div>');
                        console.log(allData);
                        callAjaxMoveMessage(allData);
                        // $(this).removeClass('text-light');
                        // $(this).prop("disabled", false);
                    } else {
                        toastr.error(idcrm_contacts.strings.idcrmNothingChecked);
                    }
    
            });
    
        },
        callAjaxMoveMessage: function(allData) {
                const jsonString = JSON.stringify(allData);
    
                $.ajax( {
                    type : 'POST',
                    url : wp_ajax_data.ajax_url,
                    data : {
                        action : 'idcrm_ajax_move_message',
                        security_move_message : security_move_message,
                        data : jsonString,
    
                    },
                    success : function(response) {
    
                        console.log('move', response);
                        // toastr.success('Starred');
                        callAjaxGetFolder(allData);
                        // allData = {};
                        $('.mail-trashrecover-button').prop("disabled", false);
                        toastr.success(response);
    
                    },
                    error: function(xhr,textStatus,e) {
    
                        console.log(xhr,textStatus,e);
                        toastr.error(idcrm_contacts.strings.error);
    
                        return;
                    }
                });
        },
        multiStarMessage: function () {
            $('.mail-star-one').each( function() {
                $(this).unbind("click").on('click', function() {
    
                    const allData = {
                        message_ids: []
                    };
    
                    allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                    allData.current_server = $('#email-list-table').attr('data-current-server');
                    allData.show_folder = $('#email-list-table').attr('data-current-folder');
                    allData.message_ids.push($(this).attr('data-message-id'));
                    allData.current_mailbox = $('#compose_mail').attr('data-mailbox');
    
                    if (allData.message_ids.length > 0) {
                        $(this).addClass('text-light');
                        $(this).prop("disabled", true);
                        $(this).html(idcrmMail.mailLoaderSpinner);
                        console.log(allData);
                        callAjaxStarMessage(allData);
                    }
    
                    return false;
                });
            });
    
            $('.mail-star-maillist').unbind("click").on('click', function() {
    
                    const allData = {
                        message_ids: []
                    };
    
                    allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                    allData.current_server = $('#email-list-table').attr('data-current-server');
                    allData.show_folder = $('#email-list-table').attr('data-current-folder');
                    allData.current_mailbox = $('#compose_mail').attr('data-mailbox');
    
                    $('.mail-item-checkbox').each( function() {
                        if ($(this).is(':checked')) {
                            allData.message_ids.push($(this).attr('data-id'));
                        }
                    });
    
                    if (allData.message_ids.length > 0) {
                        $(this).addClass('text-light');
                        $(this).prop("disabled", true);
                        $(this).html('<div>' + idcrmMail.mailLoaderSpinner + '</div>');
                        console.log(allData);
                        callAjaxStarMessage(allData);
                        $(this).removeClass('text-light');
                        $(this).prop("disabled", false);
                    } else {
                        toastr.error(idcrm_contacts.strings.idcrmNothingChecked);
                    }
    
            });
    
        },
        callAjaxStarMessage: function (allData) {
            const jsonString = JSON.stringify(allData);
    
            $.ajax( {
                type : 'POST',
                url : wp_ajax_data.ajax_url,
                data : {
                    action : 'idcrm_ajax_mail_star',
                    security_mail_star : security_mail_star,
                    data : jsonString,
    
                },
                success : function(response) {
    
                    console.log('star', response);
                    // toastr.success('Starred');
                    callAjaxGetFolder(allData);
                    // allData = {};
    
                },
                error: function(xhr,textStatus,e) {
    
                    console.log(xhr,textStatus,e);
                    toastr.error(idcrm_contacts.strings.error);
    
                    return;
                }
            });
        },
        deleteMessage: function() {
    
            $('.delete-mail').unbind("click").on('click', function() {
                const allData = {
                    message_ids: []
                };
    
                allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                allData.current_server = $('#email-list-table').attr('data-current-server');
                allData.show_folder = $('#email-list-table').attr('data-current-folder');
                allData.trash_server = $('#email-list-table').attr('data-trash-server');
    
                allData.message_ids.push($("#message-id").val());
    
                // $(".mail-compose").fadeOut("fast");
    
                if (allData.message_ids.length > 0) {
    
                    console.log(allData);
                    callAjaxDeleteMessage(allData);
                }
    
                localStorage.removeItem("email");
                localStorage.removeItem("subject");
                localStorage.removeItem("textarea");
    
                $("#compose-email").val('');
                $("#compose-subject").val('');
                $("#compose-textarea").val('');
    
            });
    
    
            $('.mail-delete-one').each( function() {
                $(this).unbind("click").on('click', function() {
                    const allData = {
                        message_ids: []
                    };
    
                    allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                    allData.current_server = $('#email-list-table').attr('data-current-server');
                    allData.show_folder = $('#email-list-table').attr('data-current-folder');
                    allData.trash_server = $('#email-list-table').attr('data-trash-server');
                    allData.message_ids.push($(this).attr('data-message-id'));
                    allData.current_mailbox = $('#compose_mail').attr('data-mailbox');
    
                    if (allData.message_ids.length > 0) {
                        $(this).addClass('text-light');
                        $(this).prop("disabled", true);
                        $(this).html('<div>' + idcrmMail.mailLoaderSpinner + '</div>');
                        console.log('delete', allData);
                        callAjaxDeleteMessage(allData);
                    }
    
                    return false;
                });
            });
    
            $('.mail-delete-maillist').unbind("click").on('click', function() {
    
                    const allData = {
                        message_ids: []
                    };
    
                    allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                    allData.current_server = $('#email-list-table').attr('data-current-server');
                    allData.trash_server = $('#email-list-table').attr('data-trash-server');
                    allData.show_folder = $('#email-list-table').attr('data-current-folder');
                    allData.current_mailbox = $('#compose_mail').attr('data-mailbox');
    
                    $('.mail-item-checkbox').each( function() {
                        if ($(this).is(':checked')) {
                            allData.message_ids.push($(this).attr('data-id'));
                        }
                    });
    
                    if (allData.message_ids.length > 0) {
                        $(this).addClass('text-light');
                        $(this).prop("disabled", true);
                        $(this).html('<div>' + idcrmMail.mailLoaderSpinner + '</div>');
                        console.log('delete', allData);
                        callAjaxDeleteMessage(allData);
                        $(this).removeClass('text-light');
                        $(this).prop("disabled", false);
                    } else {
                        toastr.error(idcrm_contacts.strings.idcrmNothingChecked);
                    }
    
            });
    
    
        },
        callAjaxDeleteMessage: function (allData) {
            const jsonString = JSON.stringify(allData);
    
            $.ajax( {
                type : 'POST',
                url : wp_ajax_data.ajax_url,
                data : {
                    action : 'idcrm_ajax_mail_delete',
                    security_mail_delete : security_mail_delete,
                    data : jsonString,
    
                },
                success : function(response) {
    
                    console.log('data', response);
                    toastr.success(idcrm_contacts.strings.idcrmDeleted);
                    callAjaxGetFolder(allData);
                    // allData = {};
    
                },
                error: function(xhr,textStatus,e) {
    
                    console.log(xhr,textStatus,e);
                    toastr.error(idcrm_contacts.strings.error);
    
                    return;
                }
            });
        },
        replyMessage: function() {
            $('.reply-message').each( function() {
                $(this).unbind("click").on('click', function() {
    
                    $("#compose-email").val($('.mail-row-' + $(this).attr('data-message-id')).attr('data-reply-to'));
                    $("#compose-subject").val('Re: ' + $('.mail-row-' + $(this).attr('data-message-id')).attr('data-subject'));
                    $("#compose-textarea").val('');
    
                    const allData = {};
    
                    allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                    allData.message_id = $(this).attr('data-message-id');
                    allData.current_server = $('#email-list-table').attr('data-current-server');
                    allData.show_folder = $('#email-list-table').attr('data-current-folder');
                    allData.encode = $('.mail-row-' + $(this).attr('data-message-id')).attr('data-encode');
                    allData.current_mailbox = $('#compose_mail').attr('data-mailbox');
    
                    callAjaxLoadMessageToReply(allData);
    
                    $(".mail-compose").fadeIn("fast");
                });
            });
        },
        forwardMessage: function() {
            $('.forward-message').each( function() {
                $(this).unbind("click").on('click', function() {
    
                    // $("#compose-email").val($('.mail-row-' + $(this).attr('data-message-id')).attr('data-reply-to'));
                    $("#compose-subject").val('Fwd: ' + $('.mail-row-' + $(this).attr('data-message-id')).attr('data-subject'));
                    $("#compose-textarea").val('');
    
                    const allData = {};
    
                    allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                    allData.message_id = $(this).attr('data-message-id');
                    allData.current_server = $('#email-list-table').attr('data-current-server');
                    allData.show_folder = $('#email-list-table').attr('data-current-folder');
                    allData.action = 'forward';
                    allData.encode = $('.mail-row-' + $(this).attr('data-message-id')).attr('data-encode');
                    allData.current_mailbox = $('#compose_mail').attr('data-mailbox');
    
                    callAjaxLoadMessageToReply(allData);
    
                    $(".mail-compose").fadeIn("fast");
                });
            });
        },
        callAjaxLoadMessageToReply: function(allData) {
            const jsonString = JSON.stringify(allData);
    
            $.ajax( {
                type : 'POST',
                url : wp_ajax_data.ajax_url,
                data : {
                    action : 'idcrm_ajax_load_reply',
                    security_load_reply : security_load_reply,
                    data : jsonString,
    
                },
                success : function(response) {
    
                    function escapeHtml(str) {
                    return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
                    }
    
                    $('#compose-textarea').replaceWith('<div id="replaced-compose-textarea" class="p-2 border border-2" contenteditable="true"></div>');
                    if (allData.action === 'forward') {
    
                        const replyto = $('.mail-row-' + allData.message_id ).attr('data-reply-to');
                        const time = ' ' + $('.mail-row-' + allData.message_id ).attr('data-time') + '<br />';
    
                        $('#replaced-compose-textarea').html(escapeHtml(replyto) + time + response);
                        $("#compose-email").focus();
                    } else {
    
                        const signature = $('#compose_mail').attr('data-mailbox').length > 0 ? signatures[$('#compose_mail').attr('data-mailbox')] : signatures[0];
    
    
                        $('#replaced-compose-textarea').html('<p></p><br />--------<br />' + response + '<p></p><br />--------<br />' + signature);
                        $("#replaced-compose-textarea").focus();
                        $('#replaced-compose-textarea').setCursorPosition(3);
                    }
    
                    // $("#compose-textarea").val(response);
    
    
    
                    console.log(response);
                    // callAjaxGetFolder(allData);
                },
                error: function(xhr,textStatus,e) {
    
                    console.log(xhr,textStatus,e);
                    toastr.error(idcrm_contacts.strings.error);
    
                    return;
                }
            });
        },
        showMessageBody: function() {
    
            deleteMessage();
            multiStarMessage();
            multiSpamMessage();
            // multiSpamMessage();
            multiSpamRecoverMessage();
            moveMessage();
            replyMessage();
            forwardMessage();
    
            $('.show-message').each( function() {
                $(this).unbind("click").on('click', function() {
    
                const currentFolder =	$('#email-list-table').attr('data-current-folder');
    
                $this = $(this);
    
                $this.parent().removeClass('unread');
                $this.parent().find('.mail-seen-round').removeClass('text-bg-warning');
    
                const allData = {};
                allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                allData.drafts_server = $('#email-list-table').attr('data-drafts-server');
                allData.message_id = $(this).parent().attr('data-id');
                allData.current_server = $('#email-list-table').attr('data-current-server');
                allData.show_folder = $('#email-list-table').attr('data-current-folder');
                allData.current_mailbox = $('#compose_mail').attr('data-mailbox');
    
    
    
                if ($(this).parent().attr('data-seen') != 1) {
                    // console.log(allData);
                    callAjaxSeenMessage(allData);
                }
    
    
                    if (currentFolder === 'Drafts' ) {
    
                        // $this.parent().removeClass('unread');
                        // $this.parent().find('.mail-seen-round').removeClass('text-bg-warning');
                        $("#compose-email").val($(this).parent().find('.message-from').text());
                        $("#compose-subject").val($(this).parent().find('.message-subject').text());
                        $("#compose-textarea").val('');
    
                        $(".delete-button-holder").html('<button type="button" class="btn btn-danger mt-3 delete-mail">' + idcrm_contacts.strings.idcrmMailDelete + '</button>');
    
                        // const allData = {};
                        // allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                        // allData.drafts_server = $('#email-list-table').attr('data-drafts-server');
                        // allData.message_id = $(this).parent().attr('data-id');
                        // allData.current_server = $('#email-list-table').attr('data-current-server');
                        allData.show_folder = 'Drafts';
    
                        $("#message-id").val(allData.message_id);
    
                        $(".mail-compose").fadeIn("fast");
                        $(this).prop("disabled", false);
    
                        callAjaxLoadDraft(allData);
                        return false;
    
                    } else {
    
                        const iframe = document.getElementById('message-body-' + $(this).parent().attr('data-id'));
                        // const innerDoc = iframe.contentDocument || iframe.contentWindow.document;
                        // console.log(innerDoc);
    
                        // $('#message-holder').html($('.my_' + $(this).parent().attr('data-id')).html());
                        $('.mail-details-container').each( function() {
                            $(this).hide();
                        });
    
                        $('.my_' + $(this).parent().attr('data-id')).fadeIn();
                        closeMessageBody();
                        return false;
    
                    }
                });
            });
        },
        callAjaxSeenMessage: function (allData) {
            const jsonString = JSON.stringify(allData);
    
            $.ajax( {
                type : 'POST',
                url : wp_ajax_data.ajax_url,
                data : {
                    action : 'idcrm_ajax_seen_message',
                    security_seen_message : security_seen_message,
                    data : jsonString,
    
                },
                success : function(response) {
    
                    console.log(response);
                    // callAjaxGetFolder(allData);
                },
                error: function(xhr,textStatus,e) {
    
                    console.log(xhr,textStatus,e);
                    toastr.error(idcrm_contacts.strings.error);
    
                    return;
                }
            });
        },
        callAjaxLoadDraft: function (allData) {
            const jsonString = JSON.stringify(allData);
    
            $.ajax( {
                type : 'POST',
                url : wp_ajax_data.ajax_url,
                data : {
                    action : 'idcrm_ajax_load_draft',
                    security_load_draft : security_load_draft,
                    data : jsonString,
    
                },
                success : function(response) {
    
                    // $('#compose-textarea').replaceWith('<div id="replaced-compose-textarea" class="p-2 border border-2 overflow-scroll" contenteditable="true"></div>');
                    $('#replaced-compose-textarea').html(response);
                    // $("#compose-textarea").val(response);
                    $("#replaced-compose-textarea").focus();
                    // $('#replaced-compose-textarea').setCursorPosition(response.length);
    
                    // $("#compose-textarea").val(response);
                    // $("#compose-textarea").focus();
    
                    console.log(response);
                    // callAjaxGetFolder(allData);
                },
                error: function(xhr,textStatus,e) {
    
                    console.log(xhr,textStatus,e);
                    toastr.error(idcrm_contacts.strings.error);
    
                    return;
                }
            });
        },
        mailButtonsControl: function() {
    
            $('.mail-button-1, .maillist-button-1').html(idcrmMailManage.starIcon);
            $('.mail-button-1').attr('title', idcrm_contacts.strings.idcrmMailMarkStarred);
            $('.maillist-button-1').attr('title', idcrm_contacts.strings.idcrmMailMarkStarred);
    
            $('.mail-button-2, .maillist-button-2').html(idcrmMailManage.spamIcon);
            $('.mail-button-2').attr('title', idcrm_contacts.strings.idcrmMailMarkSpam);
            $('.maillist-button-2').attr('title', idcrm_contacts.strings.idcrmMailMarkSpam);
    
            $('.mail-button-3, .maillist-button-3').html(idcrmMailManage.trashIcon);
            $('.mail-button-3').attr('title', idcrm_contacts.strings.idcrmMailMoveToTrash);
            $('.maillist-button-3').attr('title', idcrm_contacts.strings.idcrmMailMoveToTrash);
    
            $('.mail-button-4, .maillist-button-4').html(idcrmMailManage.recoverIcon);
            $('.mail-button-4').attr('title', idcrm_contacts.strings.idcrmMailMarkNotSpam);
            $('.maillist-button-4').attr('title', idcrm_contacts.strings.idcrmMailMarkNotSpam);
    
            $('.mail-button-5, .maillist-button-5').html(idcrmMailManage.recoverIcon);
            $('.mail-button-5').attr('title', idcrm_contacts.strings.idcrmMailMoveTo);
            $('.maillist-button-5').attr('title', idcrm_contacts.strings.idcrmMailMoveTo);
    
            $('.mail-button-4, .maillist-button-4').hide();
            $('.mail-button-5, .maillist-button-5').hide();
    
            const currentFolder = $('#email-list-table').attr('data-current-folder');
    
            if (currentFolder !== 'Trash') {
                $('.mail-button-1, .mail-button-2, .mail-button-3, .maillist-button-1, .maillist-button-2, .maillist-button-3').hide().fadeIn();
            }
    
            if (currentFolder === 'Sent' || currentFolder === 'Drafts' || currentFolder === 'Flagged') {
                $('.mail-button-2, .maillist-button-2').hide();
            }
    
            if (currentFolder === 'Spam' || currentFolder === 'Junk') {
                $('.mail-button-2, .maillist-button-2').hide();
                $('.reply-message, .forward-message').hide();
                $('.mail-button-4, .maillist-button-4').fadeIn();
            }
    
            if (currentFolder === 'Trash') {
                $('.mail-button-2, .maillist-button-2').hide();
                $('.mail-button-3, .maillist-button-3').hide();
                $('.mail-button-5, .maillist-button-5').fadeIn();
            }
        },
        replyForwardLoopMail: function() {
            $(".reply-loop-mail, .forward-loop-mail").on("click", function (event) {
                event.preventDefault();
                $('.send-loop-message').prop("disabled", false);
    
                const replyto = $('.timeline-mail-' + $(this).attr("data-num") ).attr('data-replyto');
                const localtime = ' ' + $('.timeline-mail-' + $(this).attr("data-num") ).attr('data-localtime') + ' ';
                const subject = $('.timeline-mail-' + $(this).attr("data-num")  + ' .message-subject' ).text();
                const message = $('.timeline-mail-' + $(this).attr("data-num") + ' .timeline-mail-full' ).html();
                const clearMessage =  message.replace('<a class="show-mail-less" href="#"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal feather-sm"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg></a>', '');
    
                const signature = getEmailData.emaildata[0].custom_email_signature ? '<p></p><br />--------<br />' + getEmailData.emaildata[0].custom_email_signature : "";
    
                $("#message-id").val($(this).attr("data-num"));
                $("#replyorforward").val($(this).attr("class") === 'reply-loop-mail' ? "reply" : "forward");
    
                function escapeHtml(str) {
                return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
                }
    
                $("#compose-email").val($(this).attr("class") === 'reply-loop-mail' ? replyto : "");
                $("#compose-subject").val($(this).attr("class") === 'reply-loop-mail' ? 'Re: ' + subject :'Fwd: ' + subject );
                $("#replaced-compose-textarea").html($(this).attr("class") === 'reply-loop-mail' ? '<p></p><br />--------<br />' + clearMessage + signature : escapeHtml(replyto) + localtime + '<br />' + clearMessage);
    
                $(".mail-compose").fadeIn("fast");
    
                if ($(this).attr("class") === 'reply-loop-mail') {
                    $("#replaced-compose-textarea").focus();
                } else {
                    $("#compose-email").focus();
                }
            });
        },
        closeCompose: function() {
    
            $("#compose_mail, #compose_mail_mobile").on("click", function () {
                $("#message-id").val('');
                $(".mail-compose").fadeIn("fast");
                $('.mail-compose-low').fadeOut("fast");
                $('.save-compose').prop("disabled", false);
                const signature = $('#compose_mail').attr('data-mailbox').length > 0 ? signatures[$('#compose_mail').attr('data-mailbox')] : signatures[0];
                $("#replaced-compose-textarea").html('<p></p><br />--------<br />' + signature);
                $("#replaced-compose-textarea").focus();
            });
            $('.close-compose').each( function() {
                $(this).on('click', function() {
    
                    $(".mail-compose, .mail-compose-low").fadeOut("fast");
    
                    localStorage.removeItem("email");
                    localStorage.removeItem("subject");
                    localStorage.removeItem("textarea");
    
                    $("#compose-email").val('');
                    $("#compose-subject").val('');
                    $("#compose-textarea").val('');
                    $("#replaced-compose-textarea").html('');
                    $('.send-message').prop("disabled", false);
                    return false;
                });
            });
        },
        setCurrentFolder: function() {
            const dataAttr = $('#email-list-table').attr('data-current-folder');
            const currentFolder = (typeof dataAttr !== 'undefined' && dataAttr !== '') ? dataAttr : 'Inbox' ;
    
            $('.current-folder').text($('.choose-folder[data-dropdown-folder="' + currentFolder + '"] small').text());
    
        },
        searchMail: function() {
            $("#search-mail").on("keypress", function (e) {
                const key = e.keyCode || e.which;
                if (key === 13) {
                    $('#search-mail').prop("disabled", true);
    
                    const allData = {};
                    allData.search_value = $("#search-mail").val();
                    allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                    allData.current_server = $('#email-list-table').attr('data-current-server');
                    allData.show_folder = $('#email-list-table').attr('data-current-folder');
    
                    console.log(allData);
    
                    callAjaxGetFolder(allData);
    
                }
            });
        },
        sendMessage: function () {
    
            $(".message-label").on("click", function () {
                $( "#replaced-compose-textarea" ).focus();
            });
    
            $(".send-message").on("click", function () {
                $(this).prop("disabled", true);
    
                // $(".save-compose span").html(mailLoaderSpinner);
    
                const allData = {};
                allData.email = $("#compose-email").val();
                allData.subject = $("#compose-subject").val();
                allData.textarea = $("#replaced-compose-textarea").html();
                allData.files = filesArray;
    
                allData.message_id = $("#message-id").val();
                allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                allData.sent_server = $('#email-list-table').attr('data-sent-server');
                allData.show_folder = $('#email-list-table').attr('data-current-folder');
                allData.current_mailbox = $('#compose_mail').attr('data-mailbox');
    
                if (allData.email && allData.textarea) {
    
                    console.log(allData);
    
                    // $(this).find('span').html('<div>' + mailLoaderSpinner + '</div>');
    
                    callAjaxSendMessage(allData);
    
                } else {
                    toastr.error(idcrm_contacts.strings.idcrmSendError);
                }
    
            });
        },
        sendMessageLoop: function () {
    
            $(".send-loop-message").on("click", function () {
                $(this).prop("disabled", true);
    
                // $(".save-compose span").html(mailLoaderSpinner);
    
                const allData = {};
                allData.replyorforward = $("#replyorforward").val();
                allData.email = $("#compose-email").val();
                allData.subject = $("#compose-subject").val();
                allData.textarea = $("#replaced-compose-textarea").html();
                // allData.files = filesArray;
    
                allData.message_id = $("#message-id").val();
                allData.current_user_id = $("#user-id").val();
    
                if (allData.email && allData.textarea) {
    
                    console.log(allData);
    
                    // $(this).find('span').html('<div>' + mailLoaderSpinner + '</div>');
    
                    callAjaxSendMessageLoop(allData);
    
                } else {
                    toastr.error(idcrm_contacts.strings.idcrmSendError);
                }
    
            });
        },
        callAjaxSendMessageLoop: function (allData) {
            const jsonString = JSON.stringify(allData);
    
            $.ajax( {
                type : 'POST',
                url : wp_ajax_data.ajax_url,
                data : {
                    action : 'idcrm_ajax_send_loop_message',
                    security_send_message_loop : security_send_message_loop,
                    data : jsonString,
    
                },
                success : function(response) {
    
                    $('.mail-compose').fadeOut("fast");
    
                    console.log(response);
    
                    toastr.success(idcrm_contacts.strings.idcrmEmailSent);
    
                },
                error: function(xhr,textStatus,e) {
    
                    console.log(xhr,textStatus,e);
                    toastr.error(idcrm_contacts.strings.error);
    
                    return;
                }
            });
        },
        callAjaxSendMessage: function (allData) {
            const jsonString = JSON.stringify(allData);
    
            $.ajax( {
                type : 'POST',
                url : wp_ajax_data.ajax_url,
                data : {
                    action : 'idcrm_ajax_send_message',
                    security_send_message : security_send_message,
                    data : jsonString,
    
                },
                success : function(response) {
    
                    $('.mail-compose').fadeOut("fast");
    
                    localStorage.removeItem("email");
                    localStorage.removeItem("subject");
                    localStorage.removeItem("textarea");
    
                    $("#compose-email").val('');
                    $("#compose-subject").val('');
                    $("#replaced-compose-textarea").html('');
    
                    $('.send-message').prop("disabled", false);
                    console.log(response);
    
    
                    toastr.success(idcrm_contacts.strings.idcrmEmailSent);
    
                    callAjaxGetFolder(allData);
    
                },
                error: function(xhr,textStatus,e) {
    
                    console.log(xhr,textStatus,e);
                    toastr.error(idcrm_contacts.strings.error);
    
                    return;
                }
            });
        },
        saveComposeToDraft: function () {
            $(".save-compose").on("click", function () {
                $(this).prop("disabled", true);
                // $('.mail-compose').fadeOut("fast");
                // $(".save-compose").addClass('text-light');
                $(".save-compose span").html(idcrmMail.mailLoaderSpinner);
    
                const allData = {};
                allData.email = $("#compose-email").val();
                allData.subject = $("#compose-subject").val();
                // allData.textarea = $("#compose-textarea").val();
                allData.textarea = $("#replaced-compose-textarea").html();
    
                allData.message_id = $("#message-id").val();
                allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                allData.drafts_server = $('#email-list-table').attr('data-drafts-server');
                allData.show_folder = $('#email-list-table').attr('data-current-folder');
                allData.current_mailbox = $('#compose_mail').attr('data-mailbox');
    
                if (allData.email || allData.subject) {
    
                    console.log(allData);
    
                    // $(this).find('span').html('<div>' + mailLoaderSpinner + '</div>');
    
                    callAjaxSaveDraft(allData);
    
                } else {
                    toastr.success(idcrm_contacts.strings.idcrmMailnothingTosave);
                }
    
            });
        },
        composeWindowFullscreen: function () {
            $('.mail-compose-fullscreen').on('click', function() {
    
                if ($(this).hasClass('is_fullscreen')) {
                        $(this).removeClass('is_fullscreen');
                        $(this).html(idcrmMailManage.fullscreenIcon);
                        $('.mail-compose').width( $('#message-holder').width() - $('#message-holder').width()/5);
                        $('.mail-compose').css( {'bottom' : ''});
                } else {
                        $(this).addClass('is_fullscreen');
                        $(this).html(idcrmMailManage.normalscreenIcon);
                        $(".mail-compose").width($(window).width() - $('.left-sidebar').width());
                        $('.mail-compose').css( {'bottom' : 0});
                }
            });
        },
        composeWindowMinimize: function () {
            $('.mail-compose-minimize').on('click', function() {
                $(this).addClass('is_minimized');
                $('.mail-compose').fadeOut("fast");
                $('.mail-compose-low').fadeIn("fast");
            });
        },    
        composeWindowMaximize: function () {
            $('.mail-compose-maximize, .compose-title').on('click', function() {
                $('.mail-compose').fadeIn("fast");
                $('.mail-compose-low').fadeOut("fast");
            });
        },
        closeMessageBody: function () {
            $('.close-message').each( function() {
                $(this).on('click', function() {
                    // $('#message-holder').empty();
                    $('.my_' + $(this).attr('data-message-id')).fadeOut();
                    return false;
                });
            });
        },
        composeWindowSaveInput: function () {
            $("#compose-email, #compose-subject, #compose-textarea").focusout(function() {
                // console.log($("#compose-email").val(), $("#compose-subject").val(), $("#compose-textarea").val());
                // if ($("#compose-email").val() && !isEmail($("#compose-email").val())) {toastr.error('Your Email is incorrect')}
                localStorage.setItem("email", $("#compose-email").val());
                localStorage.setItem("subject", $("#compose-subject").val());
                localStorage.setItem("textarea", $("#compose-textarea").val());
    
            });
        },
        composeWindowLoadFields: function () {
            $("#compose-email").val(localStorage.getItem("email"));
            $("#compose-subject").val(localStorage.getItem("subject"));
            $("#compose-textarea").val(localStorage.getItem("textarea"));
    
            if (localStorage.getItem("email") || localStorage.getItem("subject") || localStorage.getItem("textarea")) {
                $('.mail-compose-low').fadeIn();
            }
        },
        isEmail: function (email) {
            const regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            return regex.test(email);
        },
        callAjaxSaveDraft: function (allData) {
            const jsonString = JSON.stringify(allData);
    
            $.ajax( {
                type : 'POST',
                url : wp_ajax_data.ajax_url,
                data : {
                    action : 'idcrm_ajax_save_draft',
                    security_save_draft : security_save_draft,
                    data : jsonString,
    
                },
                success : function(response) {
    
                    $('.mail-compose').fadeOut("fast");
                    $(".save-compose span").html(idcrmMailManage.saveDraftIcon);
    
                    localStorage.removeItem("email");
                    localStorage.removeItem("subject");
                    localStorage.removeItem("textarea");
    
                    $("#compose-email").val('');
                    $("#compose-subject").val('');
                    $("#compose-textarea").val('');
    
                    $('.send-message').prop("disabled", false);
                    // console.log(response);
    
                    callAjaxGetFolder(allData);
    
                },
                error: function(xhr,textStatus,e) {
    
                    console.log(xhr,textStatus,e);
                    toastr.error(idcrm_contacts.strings.error);
    
                    return;
                }
            });
        },
        callAjaxChangeMailAttr: function (allData) {
            const jsonString = JSON.stringify(allData);
    
            $.ajax( {
                type : 'POST',
                url : wp_ajax_data.ajax_url,
                data : {
                    action : 'idcrm_ajax_change_attr',
                    security_change_attr : security_change_attr,
                    data : jsonString,
    
                },
                success : function(response) {
    
                    console.log(response);
    
                    callAjaxGetFolder(allData);
    
                },
                error: function(xhr,textStatus,e) {
    
                    console.log(xhr,textStatus,e);
                    toastr.error(idcrm_contacts.strings.error);
    
                    return;
                }
            });
    
        },
        sortMail: function () {
            const $item = $('#email-list-table'),
            $itemli = $item.children('.mail-table-row');
    
            $itemli.sort(function(a,b){
                var an = a.getAttribute('data-udate'),
                    bn = b.getAttribute('data-udate');
    
                if(an > bn) {
                    return -1;
                }
                if(an < bn) {
                    return 1;
                }
                return 0;
            });
    
            $itemli.detach().appendTo($item);
    
            // if ($('.events-item').length > 10) {
            // 	$('.events-item').slice( 10 - $('.events-item').length).remove();
            // }
        },
        callAjaxGetFolder: function (allData) {
    
            if (!allData) {
                const allData = {};
                allData.current_user_id = $('#compose_mail').attr('data-current-user-id');
                // allData.current_mailbox = $('#compose_mail').attr('data-mailbox');
                allData.current_server = $('#email-list-table').attr('data-current-server');
    
            }
            // console.log(allData);
            allData.current_mailbox = $('#compose_mail').attr('data-mailbox');
    
            const jsonString = JSON.stringify(allData);
            $.ajax( {
                type : 'POST',
                url : wp_ajax_data.ajax_url,
                data : {
                    action : 'idcrm_ajax_get_folder',
                    security_get_folder : security_get_folder,
                    data : jsonString,
    
                },
                success : function(response) {
    
                    // console.log(allData);
    
    
                    // $('#search-mail').val('');
    
                    $('#search-mail').prop("disabled", false);
                    // $("#search-mail").val('');
                    $('#message-holder').empty();
                    $('.save-compose').prop("disabled", false);
                    // if (!response.toLowerCase().includes("error") ) {
                    // 	toastr.success(idcrm_contacts.strings.test_email_sent);
                    // 	$('.custom-email-spinner').css({"display": 'none'});
                    $('#folder-mail-list').hide().html(response);
                    // sortMail();
                    $('#folder-mail-list').fadeIn('fast');
                    // } else {
                    // 	$('#test-email-form-submit').prop("disabled", false);
                    // 	toastr.error(idcrm_contacts.strings.error);
                    // 	$('.custom-email-spinner').css({"display": 'none'});
                    // }
    
                    if ($('#email-list-table').attr('data-inbox-count') > 0) {
                        $('#inbox-counter').html($('#email-list-table').attr('data-inbox-count'));
                    }
    
                    if ($('#email-list-table').attr('data-drafts-count') > 0) {
                        $('#drafts-counter').html($('#email-list-table').attr('data-drafts-count'));
                    }
    
                    $('.show-mail-folder').each( function() {
                        $('#mail-folder-loader').fadeOut('slow')
                        // $('#mail-folder-loader').remove();
                    });
    
    
                    $('.check-all').prop('checked', false);
    
                    // openMailFolder();
    
                    checkboxMailOnClick();
                    changeMailAttr();
                    showMessageBody();
                    mailButtonsControl();
                    closeCompose();
                    idcrmUI.composeWindowWidth();
                    deleteMessage();
                    multiStarMessage();
                    // multiSpamMessage();
                    multiSpamMessage();
                    multiSpamRecoverMessage();
    
                    bindMailFolderClick();
                    // searchMail();
    
                    setCurrentFolder();
    
                    // $('#folder-mail-list').hide().append(emaillistPlaceholder).fadeIn('slow');
    
                },
                error: function(xhr,textStatus,e) {
                    // $('#test-email-form-submit').prop("disabled", false);
                    console.log(xhr,textStatus,e);
                    toastr.error(idcrm_contacts.strings.error);
                    $('.show-mail-folder').each( function() {
                        $('#mail-folder-loader').fadeOut('slow')
                        // $('#mail-folder-loader').remove();
                    });
                    // $('.custom-email-spinner').css({"display": 'none'});
                    return;
                }
            });
        }
    }
})( jQuery );
