var idcrmScheduleManage = (function($) {
    "use strict";
    return {
        sortAndSlice: function () {
            const $item = $('#schedule-container'),
            $itemli = $item.children('.events-item');
            $itemli.sort(function(a,b){
                var an = a.getAttribute('data-timestring'),
                    bn = b.getAttribute('data-timestring');
                if(an > bn) {
                    return 1;
                }
                if(an < bn) {
                    return -1;
                }
                return 0;
            });
            $itemli.detach().appendTo($item);
            /*if ($('.events-item').length > 10) {
                $('.events-item').slice( 10 - $('.events-item').length).remove();
            }*/
        }
    }
})( jQuery );