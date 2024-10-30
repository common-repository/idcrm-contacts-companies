var idcrmCompanyApi = (function($) {
    "use strict";
    return {
        callAjaxAssignCompany: function (company_id, post_id) {
            $.ajax( {
                type: 'POST',
                url: wp_ajax_data.ajax_url,
                data: {
                    action: wp_ajax_company_data.action,
                    _ajax_nonce: wp_ajax_company_data.nonce,
                    company_id: company_id,
                    post_id: post_id
                },
                success: function(data) {
                    let result = '';
                    if(data) {
                        try {
                            result = JSON.parse(data);
                        } catch(e) {
                            result = data;
                        }
                    }
                    console.log('callAjaxAssignCompany result: ' + JSON.stringify(result));
                    location.reload();
                },
                error: function(xhr,textStatus,e) {
                    console.log('callAjaxAssignCompany xhr.responseText: ' + xhr.responseText);
                    toastr.error(wp_ajax_toastr.strings.idcrmError);
                }
            });
        }
    }
})( jQuery );
