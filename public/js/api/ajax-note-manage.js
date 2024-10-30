var idcrmNoteManage = (function($) {
    "use strict";
    return {
        editNone: function () {
            $(document).on('click', '.edit-note', function(event) {
                event.preventDefault();
                const postID = $(this).attr('data-id');

                // const noteText = $( ".note-text" ).text() ? $( ".note-text" ).html().replace(/<p>/gi,"").replace(/<\/p>/gi,"").replace(/<br>/gi,"\n") : '';
                const noteText = $(".note-text").text() ? $(".note-text").html().replace(/<p>/gi, "").replace(/<\/p>/gi, "").replace(/<a\b[^>]*>(.*?)<\/a>/gi, "$1").replace(/<br>/gi, "\n").trim() : '';
                // const noteText = $(".note-text").text() ? $(".note-text").html().replace(/<p>/gi, "").replace(/<\/p>/gi, "").replace(/<br>/gi, "\n").trim() : '';

                $( ".note-edit-area" ).html( '<textarea class="form-control current-note-textarea" id="current-note-textarea" name="current-note" aria-required="true">'
                + noteText
                + '</textarea>'
                + '<i class="btn1 waves-effect waves-light btn-rounded btn-outline-info btn-comment wp-block-button__link waves-input-wrapper">'
                    + '<input name="submit" type="button" id="cancel-note-button" class="waves-button-input cancel-note-button" data-post-type="user_contact" value="' + wp_ajax_toastr.strings.idcrmCancel + '">'
                + '</i>' + ' '
                + '<i class="btn1 waves-effect waves-light btn-rounded btn-outline-info btn-comment wp-block-button__link waves-input-wrapper">'
                    + '<input name="submit" type="button" id="save-note-button" class="waves-button-input save-note-button" data-post-type="user_contact" value="' + wp_ajax_toastr.strings.idcrmSave + '">'
                + '</i>' );

                $('#current-note-textarea').focus();

                var textLength = $('#current-note-textarea').val().length;
                $('#current-note-textarea').prop('selectionStart', textLength);
                $('#current-note-textarea').prop('selectionEnd', textLength);

                $('#current-note-textarea').focusout(function() {
                    if ($(this).val().trim().length > 0) {
                        $(this).css({"height": 130});
                    }
                });

                $('.cancel-note-button').on('click', function(event) {
                    event.preventDefault();
                    $( ".note-edit-area" ).html('');
                });

                $('.save-note-button').on('click', function(event) {
                    event.preventDefault();
                    let newText = $( "#current-note-textarea").val().replace(/\r?\n/g, ' <br />');
                    // $('.note-panel').append(idcrmApi.ajaxSmallLoader);
                    let post_id = $('.edit-note').attr('data-id');
                    idcrmNoteApi.callAjaxEditNote( newText, post_id);
                });
            });
        }
    }
})( jQuery );
