var idcrmContactManage = (function($) {
    "use strict";
    return {

      updateProfileButton: function () {
          $('.update_profile').on("click", function(e) {
              e.preventDefault();
              let first_name = $('#first_name').val();
              let last_name = $('#last_name').val();

              if (first_name && last_name) {
                idcrmContactApi.callAjaxUpdateProfileButton(first_name, last_name);
              }

          });
      },

      assignContactButton: function () {
          $('.dropdown-menu .assign-contact').on("click", function(e) {
              e.preventDefault();
              let contact_id = $(this).attr('data-contact-id');
              let post_id = $(this).attr('data-post-id');
              idcrmContactApi.callAjaxAssignContact(contact_id, post_id);
          });
      },

        deleteContactButton: function () {
            $('body').on("click", '.delete-contact', function(e) {
                e.preventDefault();
                const data_id = $(this).attr('data-id');
                const data_url = $(this).attr('data-url');
                const post_id = $(this).attr('data-post-id');
                idcrmContactApi.callAjaxDeleteContact(post_id, data_id, data_url);
            });
        },

        useSurname: function () {
            $('.ajax-checkbox.idcrm_use_surname').on("change", function() {
            const contact_id = $(this).attr('data-contact-id');
            const isChecked = $(this).is(':checked') ? 1 : 0;
            $(this).prop('disabled', true);
                idcrmContactApi.callContactAjaxUseSurname(isChecked, contact_id);
            });
        },

        addContactSaveCookies: function() {

          const add_contact = $('#add-contact');

          const fields = [
            'contact_first_name',
            'contact_last_name',
            'contact_surname',
            'contact_email',
            'contact_phone',
            'contact_company',
            'contact_position',
            'contact_gender',
            'contact_birthday',
            'contact_source',
            'contact_status',
          ];

          if (add_contact.length) {
            fields.forEach(fieldName => {
              let inputField = $(`#${fieldName}`);
              let storedValue = idcrmContactManage.getCookie(`idcrm_${fieldName}`);

              if (storedValue) {
                inputField.val(storedValue);
              }
            });

            $('#add-contact input[type="submit"]').on('click', function() {
              fields.forEach(fieldName => {
                let inputField = $(`#${fieldName}`);
                let enteredValue = fieldName === 'contact_company' ? inputField.find(":selected").val() : inputField.val();
                enteredValue = fieldName === 'contact_gender' ? inputField.find(":selected").val() : inputField.val();
                enteredValue = fieldName === 'contact_source' ? inputField.find(":selected").val() : inputField.val();
                enteredValue = fieldName === 'contact_status' ? inputField.find(":selected").val() : inputField.val();

                if (enteredValue) {
                  idcrmContactManage.setCookie(`idcrm_${fieldName}`, enteredValue, 7);
                }
              });
            });

            $('#add-contact button[data-bs-dismiss="modal"]').on('click', function() {
              fields.forEach(fieldName => {
                idcrmContactManage.clearCookie(`idcrm_${fieldName}`);

                $(`#${fieldName}`).val('');
              });
            });
          }

    },

    clearAllData: function() {
      const fields = [
        'contact_first_name',
        'contact_last_name',
        'contact_surname',
        'contact_email',
        'contact_phone',
        'contact_company',
        'contact_position',
        'contact_gender',
        'contact_birthday',
        'contact_source',
        'contact_status',
      ];

      fields.forEach(fieldName => {
        idcrmContactManage.clearCookie(`idcrm_${fieldName}`);

        $(`#${fieldName}`).val('');
      });
    },

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

    clearCookie: function (name) {
        document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    },
  }
})( jQuery );
