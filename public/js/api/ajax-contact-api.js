var idcrmContactApi = (function($) {
    "use strict";
    return {

      callAjaxUpdateProfileButton: function (first_name, last_name) {
          $.ajax( {
              type: 'POST',
              url: wp_ajax_data.ajax_url,
              data: {
                  action: wp_ajax_contact_data.action_update_profile,
                  _ajax_nonce: wp_ajax_contact_data.nonce,
                  first_name: first_name,
                  last_name: last_name
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
                  // console.log('callAjaxAssignContact result: ' + JSON.stringify(result));
                  location.reload();
              },
              error: function(xhr,textStatus,e) {
                  console.log('callAjaxUpdateProfileButton xhr.responseText: ' + xhr.responseText);
                  // toastr.error(wp_ajax_toastr.strings.idcrmError);
              }
          });
      },

      callAjaxAssignContact: function (contact_id, post_id) {
          $.ajax( {
              type: 'POST',
              url: wp_ajax_data.ajax_url,
              data: {
                  action: wp_ajax_contact_data.action_assign_contact,
                  _ajax_nonce: wp_ajax_contact_data.nonce,
                  contact_id: contact_id,
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
                  // console.log('callAjaxAssignContact result: ' + JSON.stringify(result));
                  location.reload();
              },
              error: function(xhr,textStatus,e) {
                  console.log('callAjaxAssignContact xhr.responseText: ' + xhr.responseText);
                  toastr.error(wp_ajax_toastr.strings.idcrmError);
              }
          });
      },
        callAjaxDeleteContact: function (post_id = 0, user_id, data_url) {
            $.ajax( {
                type: 'POST',
                url: wp_ajax_data.ajax_url,
                data: {
                    action: wp_ajax_contact_data.action,
                    _ajax_nonce: wp_ajax_contact_data.nonce,
                    user_id: user_id,
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
                    // console.log('callAjaxDeleteContact result: ' + JSON.stringify(result));
                    window.location.href = data_url;
                },
                error: function(xhr,textStatus,e) {
                    console.log('callAjaxDeleteContact xhr.responseText: ' + xhr.responseText);
                    toastr.error(wp_ajax_toastr.strings.idcrmError);
                }
            });
        },

        callContactAjaxUseSurname: function(isChecked, contact_id) {
          $.ajax( {
              type: 'POST',
              url: wp_ajax_data.ajax_url,
              data: {
                  action: wp_ajax_contact_data.action_use_surname,
                  _ajax_nonce: wp_ajax_contact_data.nonce,
                  is_checked: isChecked,
                  contact_id: contact_id
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
                  // console.log('callContactAjaxUseSurname result: ' + JSON.stringify(result));
                  $('.ajax-checkbox.idcrm_use_surname').prop('disabled', false);
              },
              error: function(xhr,textStatus,e) {
                  console.log('callContactAjaxUseSurname xhr.responseText: ' + xhr.responseText);
                  toastr.error(wp_ajax_toastr.strings.idcrmError);
              }
          });
        },
    }
})( jQuery );
