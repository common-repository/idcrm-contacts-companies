var idcrmUI = (function($) {
    "use strict";

    return {

      updateAvatar: function() {
        $(".avatar-image").on("click",function (e) {
          const fileDialog = $('<input type="file">');
          fileDialog.click();
          fileDialog.on("change",onFileSelected);
            return false;
        });
        var onFileSelected = function(e){

          if ( $(this)[0].files.length ) {
            const file = $(this)[0].files[0];
            const formData = new FormData();
      			formData.append( 'image', file );
            formData.append('action', wp_ajax_contact_data.action_update_avatar);
            formData.append('_ajax_nonce', wp_ajax_contact_data.nonce);

            $.ajax({
      				url: wp_ajax_data.ajax_url,
      				type: 'POST',
      				data: formData,
      				contentType: false,
      				enctype: 'multipart/form-data',
      				processData: false,
      				success: function ( response ) {
                // console.log( response );
                $('.profile-avatar').attr('src', response);
                $('.profile-pic').attr('src', response);

                // window.location = document.location.href;
      				}
      			});
          }
        };
      },

      editUserImage: function (post_id) {

        if (!$('#post-image').hasClass('clicked')) {

          $('#post-image').addClass('clicked');

          $('#post-image').trigger('click');

          $('#post-image').on('change', function() {

              var form_data = new FormData();
              var file = $('#post-image')[0].files[0];
              var individual_file = file[0];
              form_data.append("image", file);
              form_data.append("post_id", post_id);
              form_data.append('action', wp_ajax_contact_data.action_upload_postimage);
              form_data.append('_ajax_nonce', wp_ajax_contact_data.nonce);


              // var files = $('#post-image').prop('files'); console.log(files[0]);
              // var individual_file = files[0];
              // var formdata = new FormData();

                $.ajax( {
                    type: 'POST',
                    url: wp_ajax_data.ajax_url,
                    data: form_data,
                    // {
                    //     action : wp_ajax_contact_data.action_upload_postimage,
                    //     image : individual_file,
                    //     // _ajax_nonce : wp_ajax_note_data.nonce,
                    //     post_id : post_id,
                    // },
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        let result = '';
                        if(data) {
                            try {
                                result = JSON.parse(data);
                            } catch(e) {
                                result = data;
                            }
                        }
                        console.log('editUserImage result: ' + JSON.stringify(result));

                        if (result) {
                          $('.wp-post-image').attr('src', result);
                          $('.wp-post-image').attr('srcset', '');
                        }

                        $('#post-image').removeClass('clicked');

                        // window.location.href = data_url;
                    },
                    error: function(xhr,textStatus,e) {
                        console.log('editUserImage xhr.responseText: ' + xhr.responseText);
                        // toastr.error(wp_ajax_toastr.strings.idcrmError);
                    }
                });

          });
        }

  		},

      getDevicePlatform: function(string) {

        var platform = navigator.platform.toLowerCase();
        var detected_platform = '';

        if (platform.includes('win')) {
          detected_platform = "windows";
        } else if (platform.includes('mac')) {
          detected_platform = "macos";
        } else if (/(android|webos|iphone|ipad|ipod|blackberry|windows phone)/i.test(navigator.userAgent)) {
          detected_platform = "mobile";
        } else {
          detected_platform = "other";
        }

        return idcrmcontact_ui_shortcuts[detected_platform][string];
      },

      shortcuts: function() {
        $('.edit-shortcut').text(idcrmUI.getDevicePlatform('edit'));
      },

      editContactFields: function() {
        $('.edit-contact-fields').on('click', function(event) {
          idcrmUI.editAction(event);
        });

        $('.edit-contact-cancel').on('click', function(event) {
          idcrmUI.cancelEditAction(event);
        });

        $('.edit-contact-apply').on('click', function(event) {

          if (!$(this).hasClass('disabled')) {
            $(this).addClass('disabled');
            const allData = {};
            allData.contact_first = $('#contact-first-name').text();
            allData.contact_last = $('#contact-second-name').text();
            allData.contact_surname = $('#contact-surname').text();
            allData.contact_email = $('#user-email').text();
            allData.contact_phone = $('#user-phone').text();
            // allData.contact_website = $('#user-website').text();
            allData.contact_position = $('#position').text();
            allData.user_status = $('#user_status :selected').val();
            allData.user_source = $('#user_source :selected').val();
            allData.user_company = $('#task_company :selected').val();
            allData.user_gender = $('#gender :selected').val();
            allData.user_birthday = $('#hidden-birthday').val();
            allData.user_lead_exclude = $('#idcrm_contact_lead_exclude').is(":checked");
            allData.contact_id = $('#single-contact').attr('data-contact-id');
            // console.log(allData);

            idcrmUI.updateContact(allData);
          }
        });

        $(document).on("keydown", function(event) {
          if ((event.metaKey && event.key === "e") || (event.ctrlKey && event.key === "e") || (event.metaKey && event.key === "у") || (event.ctrlKey && event.key === "у")) {
            idcrmUI.editAction(event);
          }

          if (event.key === "Escape") {
            idcrmUI.cancelEditAction(event);
          }
        });

        if ($('#single-contact').length) {
          const params = idcrmUI.getURLParams($(location).attr('href'));
          if (typeof params['idcrm_action'] !== 'undefined' && params['idcrm_action'] === 'edit') {
            $('.edit-contact-fields').click();
          }
        }
      },

      editCompanyFields: function() {
        $('.edit-company-fields').on('click', function(event) {
          idcrmUI.editAction(event);
        });

        $('.edit-company-cancel').on('click', function(event) {
          idcrmUI.cancelEditAction(event);
        });

        $('.edit-company-apply').on('click', function(event) {

          if (!$(this).hasClass('disabled')) {
            $(this).addClass('disabled');
            const allData = {};

            allData.company_title = $('#company-title').text();
            allData.company_website = $('#company-website').text();
            allData.company_facebook = $('#company-facebook').text();
            allData.company_twitter = $('#company-twitter').text();
            allData.company_youtube = $('#company-youtube').text();
            allData.company_inn = $('#company-inn').text();
            allData.company_kpp = $('#company-kpp').text();
            allData.company_ogrn = $('#company-ogrn').text();
            allData.comp_status = $('#comp_status :selected').val();
            allData.company_id = $('#single-company').attr('data-company-id');
            // console.log(allData);

            idcrmUI.updateCompany(allData);
          }
        });

        $(document).on("keydown", function(event) {
          if ((event.metaKey && event.key === "e") || (event.ctrlKey && event.key === "e") || (event.metaKey && event.key === "у") || (event.ctrlKey && event.key === "у")) {
            idcrmUI.editAction(event);
          }

          if (event.key === "Escape") {
            idcrmUI.cancelEditAction(event);
          }
        });

        if ($('#single-contact').length) {
          const params = idcrmUI.getURLParams($(location).attr('href'));
          if (typeof params['idcrm_action'] !== 'undefined' && params['idcrm_action'] === 'edit') {
            $('.edit-contact-fields').click();
          }
        }
      },

      getURLParams: function(url) {
        return Object.fromEntries(new URL(url).searchParams.entries());
      },

      editAction: function(event) {
        event.preventDefault();

        $('.editable-card').addClass('enable-edit-card');

        $('.editable').each(function() {
          $(this).addClass('enable-edit');
          $(this).attr("contenteditable", true);
        });

        $('.editable-date').each(function() {
          idcrmUI.editableDatePicker();
        });

        $('.hidden-input').each(function() {
          $(this).attr('type', 'text');
        });

        $('#single-contact img.editable').on("click", function(e) {
          e.preventDefault();
          idcrmUI.editUserImage($('#single-contact').attr('data-contact-id'));
        });

        $('#single-company img.editable').on("click", function(e) {
          e.preventDefault();
          idcrmUI.editUserImage($('#single-company').attr('data-company-id'));
        });

        // $('.edit-select').each(function() {
        //   $(this).find('.visible-container').addClass('d-none');
        // });

        $('.visible-container').addClass('d-none');

        $('.hidden-edit-select').each(function() {
          $(this).removeClass('d-none');
          $(this).addClass('d-block');
        });

        $('.hidden-edit').each(function() {
          $(this).removeClass('d-none');
          $(this).addClass('d-flex');
        });

        $('.hidden-input').on("change", function() {
          $('#' + $(this).attr('data-target') + ' span').text($(this).val());
        });
      },

      cancelEditAction: function(event) {
        event.preventDefault();

        $('img.editable').unbind('click');

        $('.editable-card').removeClass('enable-edit-card');

        $('.editable').each(function() {
          $(this).removeClass('enable-edit');
          $(this).attr("contenteditable", false);
        });

        $('input[type="hidden"]').each(function() {
          $('#' + $(this).attr('data-target')).text($(this).attr('data-old-value'));
        });

        $('.hidden-input').each(function() {
          $(this).attr('type', 'hidden');
          $('#' + $(this).attr('data-target') + ' span').text($(this).attr('data-old-value'));
        });

        $('#task-title').text($('#hidden-task-title').val());
        $('#deal-amount').text($('#hidden-deal-amount').val());

        // $('.edit-select').each(function() {
          $('.visible-container').removeClass('d-none');
        // });

        $('.hidden-edit-select').each(function() {
          $(this).addClass('d-none');
          $(this).removeClass('d-block');
        });

        $('.hidden-edit').each(function() {
          $(this).removeClass('d-flex');
          $(this).addClass('d-none');
        });

        var currentURL = window.location.href;
        var newURL = currentURL.replace(/(\?|&)idcrm_action=edit/g, '');
        if (currentURL !== newURL) {
          window.history.replaceState({}, document.title, newURL);
        }
      },

      updateContact: function(allData) {
        const jsonString = JSON.stringify(allData);
        $.ajax( {
            type: 'POST',
            url: wp_ajax_data.ajax_url,
            data: {
                action: wp_ajax_contact_data.action_update_contact,
                _ajax_nonce: wp_ajax_contact_data.nonce,
                data: jsonString
            },
            success: function(data) {

              // console.log(data);

                var currentURL = window.location.href;
                var newURL = currentURL.replace(/(\?|&)idcrm_action=edit/g, '');
                if (currentURL !== newURL) {
                  window.history.replaceState({}, document.title, newURL);
                }

                location.reload();
            },
            error: function(xhr,textStatus,e) {
                console.log('updateContact xhr.responseText: ' + xhr.responseText);
                toastr.error(wp_ajax_toastr.strings.idcrmError);
            }
        });
      },

      updateCompany: function(allData) {
        const jsonString = JSON.stringify(allData);
        $.ajax( {
            type: 'POST',
            url: wp_ajax_data.ajax_url,
            data: {
                action: wp_ajax_company_data.action_update_company,
                _ajax_nonce: wp_ajax_company_data.nonce,
                data: jsonString
            },
            success: function(data) {

              // console.log(data);

                var currentURL = window.location.href;
                var newURL = currentURL.replace(/(\?|&)idcrm_action=edit/g, '');
                if (currentURL !== newURL) {
                  window.history.replaceState({}, document.title, newURL);
                }

                location.reload();
            },
            error: function(xhr,textStatus,e) {
                console.log('updateCompany xhr.responseText: ' + xhr.responseText);
                toastr.error(wp_ajax_toastr.strings.idcrmError);
            }
        });
      },

      editableDatePicker: function () {
          $("#hidden-birthday").bootstrapMaterialDatePicker({
              format: "DD.MM.YYYY",
              time: false,
              date: true,
              weekStart: 1
          });

          // $("#checklist_time").bootstrapMaterialDatePicker({
          //   format: "HH:mm",
          //   time: true,
          //   date: false
          // });
      },

        composeWindowWidth: function () {
            if ($(window).width() <= 959) {
                $('.mail-compose').width('100%');
                $('.mail-compose').css( {'top' : $('.topbar').height()});
            } else {
                if ($('#message-holder').length > 0) {
                    $('.mail-compose, .mail-compose-low').width( $('#message-holder').width() - $('#message-holder').width()/5);
                } else {
                    $('.mail-compose').width( $(window).width()/2.5);
                }
                $('.mail-compose').css( {'top' : $('.topbar').height()});
                $('.mail-compose-low').css( {'bottom' : $('.footer').height()});
            }
        },

        datePicker: function () {

            $("#mdate").bootstrapMaterialDatePicker({
                weekStart: 1,
                time: false
            });

            $("#edit_event_date").bootstrapMaterialDatePicker({
                weekStart: 1,
                format: "DD.MM.YYYY",
                time: false
            });

            $("#timepicker").bootstrapMaterialDatePicker({
                format: "HH:mm",
                time: true,
                date: false
            });

            $("#edit_event_time").bootstrapMaterialDatePicker({
                format: "HH:mm",
                time: true,
                date: false
            });

            $("#contact_birthday").bootstrapMaterialDatePicker({
                format: "DD.MM.YYYY",
                time: false,
                date: true,
                weekStart: 1
            });
        },

        addSelect2: function () {
          $("#contact_source").select2({
            placeholder: wp_datetimepicker_data.source,
            theme: "bootstrap-5",
             // containerCssClass: "select2--small",
            dropdownParent: $("#contact_source").parent(),
            tags: true,
            tokenSeparators: [',', ' '],
            selectOnClose: false,
            language: {
              noResults: function () {
                return wp_datetimepicker_data.noresults;
              }
            }
          });

          $("#contact_source_zadarma").select2({
            placeholder: wp_datetimepicker_data.source,
            theme: "bootstrap-5",
             // containerCssClass: "select2--small",
            dropdownParent: $("#contact_source_zadarma").parent(),
            tags: true,
            tokenSeparators: [',', ' '],
            selectOnClose: false,
            language: {
              noResults: function () {
                return wp_datetimepicker_data.noresults;
              }
            }
          });

          $("#contact_company").select2({
            placeholder: wp_datetimepicker_data.company,
            theme: "bootstrap-5",
            allowClear: true,
            dropdownParent: $("#contact_company").parent(),
            selectOnClose: false,
            tags: true,
            language: {
              noResults: function () {
                return wp_datetimepicker_data.noresults;
              }
            }
          });

          $("#assign_contact_zadarma").select2({
            placeholder: wp_datetimepicker_data.contact,
            theme: "bootstrap-5",
            allowClear: true,
            dropdownParent: $("#assign_contact_zadarma").parent(),
            selectOnClose: false,
            tags: true,
            language: {
              noResults: function () {
                return wp_datetimepicker_data.noresults;
              }
            }
          });

          $("#contact_company_zadarma").select2({
            placeholder: wp_datetimepicker_data.company,
            theme: "bootstrap-5",
            allowClear: true,
            dropdownParent: $("#contact_company_zadarma").parent(),
            selectOnClose: false,
            tags: true,
            language: {
              noResults: function () {
                return wp_datetimepicker_data.noresults;
              }
            }
          });
        },

        sidebarActiveLink: function () {
          if ($('#current_term_id').length) {
            const section = $('#current_term_id').attr('data-section');
            const term_id = $('#current_term_id').attr('data-term-id');

            if (term_id) { //console.log(section, term_id);
              $('.sidebar-' + section + ' .cat-item-' + term_id + ' a').addClass('active');
            }
          }
        },
    }
})( jQuery );
