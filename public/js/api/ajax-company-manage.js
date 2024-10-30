var idcrmCompanyManage = (function($) {
    "use strict";
    return {
        manageCompanyButton: function () {
            $('.dropdown-menu .assign-company').on("click", function(e) {
                let company_id = $(this).attr('data-company-id');
                let post_id = $(this).attr('data-post-id');
                idcrmCompanyApi.callAjaxAssignCompany(company_id, post_id);
            });
        }
    }
})( jQuery );