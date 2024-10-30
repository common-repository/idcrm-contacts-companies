var idcrmNoteApi = (function($) {
    "use strict";
    return {
        callAjaxEditNote: function (newText, post_id) {
            $.ajax( {
                type : 'POST',
                url : wp_ajax_data.ajax_url,
                data : {
                    action : wp_ajax_note_data.action,
                    _ajax_nonce : wp_ajax_note_data.nonce,
                    post_id : post_id,
                    note_text : newText,
                },
                beforeSend: function() {
          				$('#edit-note-panel').append(idcrmApi.ajaxSmallLoader);
          			},
                success : function(data) {
                    let result = '';
                    if(data) {
                        try {
                            result =  JSON.parse(data);
                            if (result.code == 0) {
                                $( ".note-edit-area" ).html('');
                                newText = newText.replace(/(https?:\/\/[^\s]+)/g, '<a target="_blank" href="$1">$1</a>'); // Обработчик ссылок
                                $( ".note-text" ).html(newText);
                            }
                        } catch(e) {
                            result = data;
                        }
                    }
                    // console.log('callAjaxEditNote result: ' + JSON.stringify(result));
                    $( ".ajax-small-loader" ).remove();
                },
                error: function(xhr,textStatus,e) {
                    console.log('callAjaxEditNote xhr.responseText: ' + xhr.responseText);
                    toastr.error(wp_ajax_toastr.strings.idcrmError);
                }
            });
        }
    }
})( jQuery );
