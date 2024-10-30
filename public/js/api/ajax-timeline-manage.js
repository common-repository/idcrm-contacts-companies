var idcrmTimelineManage = (function($) {
    "use strict";
    return {
        showMoreLess: function () {
            $('.show-mail-more').each( function() {
                $(this).on('click', function(event) {
                    event.preventDefault();
                    $(this).parent().parent().find('.timeline-mail-full').show();
                    $(this).parent().hide();
                });
            });
            $('.show-mail-less').each( function() {
                $(this).on('click', function(event) {
                    event.preventDefault();
                    $(this).parent().parent().find('.timeline-mail-short').show();
                    $(this).parent().hide();
                });
            });
        }
    }
})( jQuery );