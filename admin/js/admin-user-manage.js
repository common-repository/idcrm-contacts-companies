var idcrmAmdinUI = (function( $ ) {
	'use strict';
	return {
		userFormNotify: function () {
			$("#send_user_notification").removeAttr("checked");
		},

		customUserProfileFields: function () {
			$(document).on('click', '.avatar-image-upload', function (e) {
				e.preventDefault();
				var $button = $(this);
				var file_frame = wp.media.frames.file_frame = wp.media({
					title: 'Select or Upload an Custom Avatar',
					library: {
						type: 'image' // mime type
					},
					button: {
						text: 'Select Avatar'
					},
					multiple: false
				});
				file_frame.on('select', function() {
					var attachment = file_frame.state().get('selection').first().toJSON();
					const thumb = attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.sizes.full.url
					$button.siblings('#userimg').val( thumb );
					$button.siblings('.custom-avatar-preview').attr( 'src', thumb );
				});
				file_frame.open();
			});
		},

		adminMenuHide: function() {

			$('#toplevel_page_idcrm-contacts > ul > li:not(.wp-submenu-head)').show();

			$('.idcrm-third-level').each( function() {
				$(this).closest('li').hide();
			});

			$(document).on('click', function(event) {
			    if (!$(event.target).closest('.idcrm-menu-holder').length) {
			        $('.idcrm-menu-holder').hide();
			    }
			});

			$('.idcrm-menu-holder li').each( function() {
				if ($(this).hasClass('current')) {

					$('.idcrm-menu-holder').closest('li').addClass('current');
				}
			});
		},

		adminMenuShowContact: function() {
			idcrmAmdinUI.adminMenuShow('edit.php?post_type=user_contact', 'idcrm-contact-menu');
			idcrmAmdinUI.adminMenuShow('edit.php?post_type=company', 'idcrm-company-menu');
			idcrmAmdinUI.adminMenuShow('edit.php?post_type=contact_event', 'idcrm-schedule-menu');
		},

		adminMenuShow: function(pageUrl, menuClass) {
			$('li a[href*="' + pageUrl + '"]:not(.wp-has-submenu)').on('mouseover', function() {

				// const adminmenuWidth = parseInt($('#adminmenu').width(), 10);
				$('.idcrm-menu-holder').remove();

		    if ($('.idcrm-menu-holder').length === 0) {
		        $('<div class="idcrm-menu-holder"></div>').appendTo($(this).closest('li'));
		    }

				if (!$('.idcrm-menu-holder').hasClass('has-items')) {
					$('.' + menuClass).closest('li').each(function() {
							if (!$(this).hasClass('cloned')) {
									$(this).clone().appendTo('.idcrm-menu-holder').addClass('cloned');
							}
					});
					$('.idcrm-menu-holder').addClass('has-items');
					// $('.idcrm-menu-holder').css('right', -adminmenuWidth );
				}

		    $('.idcrm-menu-holder li').each(function() {
		        $(this).show();
		    });
			});

			// $(document).on('mouseout', '.idcrm-menu-holder', function() {
			// 	$('.idcrm-menu-holder').remove();
			// });
		},

		settingsRender: function() {
			// console.log(idcrm_settings.markup);

			const container = $('#custom-settings-block');
			const settingsTabs = $('#settings-tabs');
			const navTab = $('.nav-tab');

			const orderMap = {
			    "pro": 1,
			    "newsletter": 2,
			    "deals": 3,
			    "knowledge": 5,
			    "tasks": 4
			};

			let tabsArray = idcrm_settings.markup.flatMap(tabContent => tabContent.filter(item => item.hasOwnProperty('addtab')));

			if (tabsArray.length) {
				tabsArray.sort(function(a, b) {
						return orderMap[a.id] - orderMap[b.id];
				});

				tabsArray.forEach(item => {

						if (item.hasOwnProperty('addtab')) {
							$('#settings-tabs').append(`<a id="${item.id}" href="#" class="nav-tab no-href">${item.addtab}</a>`);
						}

				});

			}

			idcrm_settings.markup.forEach(tabContent => {
			  tabContent.forEach(item => {

					// if (item.hasOwnProperty('addtab')) {
					// 	$('#settings-tabs').append(`<a id="${item.id}" href="#" class="nav-tab no-href">${item.addtab}</a>`);
					// }

					if ($('#' + item.tab).hasClass('nav-tab-active') || idcrmAmdinUI.getURLParameter('tab') == item.tab) {
						$('#' + item.tab).addClass('nav-tab-active')
						if (item.tab == $('.nav-tab-active').attr('id')) {
							if (item.hasOwnProperty('heading')) {
								container.append('<h2>' + item.heading + '</h2>');
							} else {

								const appendElement = item.hasOwnProperty('title') ? `<div class="settings-element"><div class="settings-title">${item.title}</div><div class="settings-html" data-id="${item.id}">${item.html}</div></div>` : `<div class="settings-element"><div class="settings-html">${item.html}</div></div>`;

								container.append(appendElement);
							}
						}

					}
			  });
			});

			$('.nav-tab').on('click', function(e) {
				if ( $(this).hasClass('no-href')) {
					e.preventDefault();

					$('.nav-tab').removeClass('nav-tab-active');
					$(this).addClass('nav-tab-active');

					var currentUrl = window.location.href;
					var newTabValue = $(this).attr('id');
					var updatedUrl = idcrmAmdinUI.updateQueryStringParameter(currentUrl, 'tab', newTabValue);
					window.history.replaceState({}, '', updatedUrl);

					container.html('');

					idcrm_settings.markup.forEach(tabContent => {
					  tabContent.forEach(item => {
							if ($('#' + item.tab).hasClass('nav-tab-active')) {
								if (item.hasOwnProperty('heading')) {
									container.append('<h2>' + item.heading + '</h2>');
								} else {
									const appendElement = item.hasOwnProperty('title') ? `<div class="settings-element"><div class="settings-title">${item.title}</div><div class="settings-html" data-id="${item.id}">${item.html}</div></div>` : `<div class="settings-element"><div class="settings-html">${item.html}</div></div>`;
							    container.append(appendElement);
								}
							}
					  });
					});
				}
			});
		},

		updateQueryStringParameter: function(uri, key, value) {
			var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
	    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
	    if (uri.match(re)) {
	      return uri.replace(re, '$1' + key + "=" + value + '$2');
	    } else {
	      return uri + separator + key + "=" + value;
	    }
		},

		getURLParameter: function(name) {
		  const urlParams = new URLSearchParams(window.location.search);
		  return urlParams.get(name);
		},

		settingsSave: function() {
			$('#submit-settings').on('click', function(e) {
					e.preventDefault();

					$('.autosave').each(function() {
						const fieldId = $(this).attr('id');
						const fieldVal = $(this).val();
						// if (fieldVal) {
							idcrm_settings[fieldId] = fieldVal;
						// }

					});

					$(this).prop('disabled', true);

					// console.log(idcrm_settings);

					idcrmAmdinUI.settingsAjaxSave(idcrm_settings);
			});
		},

		settingsAjaxSave: function(settings) {
			const jsonString = JSON.stringify( settings );
				$.ajax( {
						type : 'POST',
						url: idcrm_admin_data.ajax_url,
						data : {
								action : idcrm_admin_data.save_settings,
								_ajax_nonce : idcrm_admin_data.nonce,
								idcrm_settings : jsonString,
						},

						success : function( response ) {
							// console.log(response);
							// toastr.error(mail_admin_strings.reset_success);
							//location.reload();

							setTimeout(() => {
								$('#submit-settings').prop('disabled', false);
							}, 2000);

						},
						error: function(jqXHR, textStatus, errorThrown) {
							console.error('Error:', textStatus, errorThrown);
								// return;
						}
				} );

		},

		openMedia: function() {
			$(document).on('click', '.media_manager', function(e) {

				const fieldId = $(this).attr('data-field-id');

             e.preventDefault();
             var image_frame;
             if(image_frame){
                 image_frame.open();
             }
             // Define image_frame as wp.media object
             image_frame = wp.media({
                           title: 'Select Media',
                           multiple : false,
                           library : {
                                type : 'image',
                            }
                       });

                       image_frame.on('close',function() {
                          // On close, get selections and save to the hidden input
                          // plus other AJAX stuff to refresh the image preview
                          var selection =  image_frame.state().get('selection');
                          var gallery_ids = new Array();
                          var my_index = 0;
                          selection.each(function(attachment) {
                             gallery_ids[my_index] = attachment['id'];
                             my_index++;
                          });
                          var ids = gallery_ids.join(",");
                          if(ids.length === 0) return true;//if closed withput selecting an image
                          $('#' + fieldId).val(ids);
                          idcrmAmdinUI.refreshImage(ids, fieldId);
                       });

                      image_frame.on('open',function() {
                        // On open, get the id from the hidden input
                        // and select the appropiate images in the media manager
                        var selection =  image_frame.state().get('selection');
                        var ids = $('#' + fieldId).val() ? [$('#' + fieldId).val()] : [];
                        ids.forEach(function(id) {
                          var attachment = wp.media.attachment(id);
                          attachment.fetch();
                          selection.add( attachment ? [ attachment ] : [] );
                        });

                      });

                    image_frame.open();
     });
	 },

	 refreshImage: function(the_id, field){
				$.ajax( {
						type : 'GET',
						url: idcrm_admin_data.ajax_url,
						data : {
								action : idcrm_admin_data.get_image,
								_ajax_nonce : idcrm_admin_data.nonce,
								idcrm_image_id: the_id,
		            idcrm_image_field: field,
						},

						success : function( response ) {
							// console.log(response);
							$('#image-' + field).replaceWith( response.data.image );
							$('#' + field + '_url').val( response.data.url );
							// toastr.error(mail_admin_strings.reset_success);
							//location.reload();

						},
						error: function(jqXHR, textStatus, errorThrown) {
							console.error('Error:', textStatus, errorThrown);
								// return;
						}
				} );
			},

			activateLicense: function() {

				// const $idcrmSettingsKey = $('#idcrm_pro_key');
				const $idcrmInput = $('.idcrm_license_input');
				const $idcrActivateButton = $('.idcrm_activate_license');
				const $idcrDeleteButton = $('.idcrm_delete_license');
				// const $idcrmDealsActivatePlugin = $('#idcrm_deals_activate_plugin');

				$idcrmInput.on('keyup', function() {
					if ($(this).val().length > 0) {
						const id = $(this).attr('id');
						$('.idcrm_activate_license[data-action="' + id + '"]').prop("disabled", false);
						//$(this).attr("type", "text");
						// console.log('keyup');
					}
				});

				// $idcrmDealsKey.on('keyup', function() {
				// 	if ($(this).val().length > 0) {
				// 		$idcrmDealsActivatePlugin.prop("disabled", false);
				// 		//$(this).attr("type", "text");
				// 	}
				// });

				$idcrActivateButton.on('click', function() {
					const action = $(this).attr('data-action');
					const $actionElement = $('#' + action);
					// const server = $(this).attr('data-server');
					const key = $actionElement.val().trim();

					if (key.length > 0) {
						$('.license-spinner.' + action).css({"visibility": 'visible'});
						$(this).prop("disabled", true);
						$actionElement.prop("readonly", true);
						// console.log(key);
						// const key = $('#idcrm_settings_key').val().replace(/[\s-]/g, '').toLowerCase();
						idcrmAmdinUI.getLicenseData(key, window.location.hostname, action);

					}
				});

				$idcrDeleteButton.on('click', function() {
					const action = $(this).attr('data-action');
					if (confirm(idcrm_admin_data.delete_license_confirm)) {
						idcrmAmdinUI.deleteLicense(action);
					}
				});

			},

			deleteLicense: function(action) {
					$.ajax( {
							type : 'POST',
							url: idcrm_admin_data.ajax_url,
							data : {
									action : idcrm_admin_data.delete_license,
									_ajax_nonce : idcrm_admin_data.nonce,
									data_action: action
							},
							success : function( response ) {
								// console.log(response);
								// toastr.error(mail_admin_strings.reset_success);
								location.reload();
							},
							error: function(xhr,textStatus,e) {
								toastr.error(idcrm_admin_data.delete_license_error);
									// return;
							}
					} );
			},

			saveLicense: function(licenseKey, action, expire) {
				if (licenseKey) {
					$.ajax( {
							type : 'POST',
							url: idcrm_admin_data.ajax_url,
							data : {
									action : idcrm_admin_data.save_license,
									_ajax_nonce : idcrm_admin_data.nonce,
									idcrm_license : licenseKey,
									data_action : action,
									idcrm_expire: expire
							},

							success : function( response ) {
								// console.log(response);
								// toastr.error(mail_admin_strings.reset_success);
								//location.reload();
							},
							error: function(jqXHR, textStatus, errorThrown) {
								console.error('Error:', textStatus, errorThrown);
									// return;
							}
					} );
				}
			},

			getLicenseData: function( licenseKey, domainName, action) {

					$.ajax( {
							type : 'POST',
							url: idcrm_admin_data.ajax_url,
							data : {
									action : idcrm_admin_data.get_license,
									_ajax_nonce : idcrm_admin_data.nonce,
									idrcm_license_key: licenseKey,
									idrcm_license_domain_name: domainName,
									idrcm_license_action: action,
							},

							success : function( response ) {
								// console.log(response);
								response = $.parseJSON(response);
								setTimeout(() => {
									$('.license-spinner').css({"visibility": 'hidden'});
								}, 2000);

								if (response && response !== null && response.error === 0 && !response.reason) {
									$('.activation-notice.' + action).text(idcrm_admin_data.license_registered);
									$('.activation-notice.' + action).removeClass('activation-error');
									//$('.activation-notice.' + action).css("visibility", "hidden");
									$('.idcrm_activate_license[data-action="' + action + '"]').prop("disabled", false);
									$('.idcrm_delete_license[data-action="' + action + '"]').prop("disabled", false);

									idcrmAmdinUI.saveLicense(response.key, action, response.supported_until);

								} else {
									$('.activation-notice.' + action).addClass('activation-error');
									$('.idcrm_activate_license[data-action="' + action + '"]').prop("disabled", false);
									$('#' + action).prop("readonly", false);
									$('.activation-notice.' + action).text(idcrm_admin_data.license_key_notexist);
									// console.log($('.activation-notice.' + action));
								}
								//location.reload();
							},
							error: function(xhr,textStatus,e) {
								$('.activation-notice.' + action).addClass('activation-error');
								$('.license-spinner').css({"visibility": 'hidden'});
								$('#' + action).prop("readonly", false);
								$('.activation-notice.' + action).text(idcrm_admin_data.license_server_error);
									// return;
							}
					} );

			},

			settingsHtml: function() {

				const idcrm_cf7_default_user = idcrm_settings.hasOwnProperty('idcrm_cf7_default_user') ? idcrm_settings.idcrm_cf7_default_user : '';
				const idcrm_start_page = idcrm_settings.hasOwnProperty('idcrm_start_page') ? idcrm_settings.idcrm_start_page : 'crm-contacts';

				// const idcrm_cf7_default_user_numbers = idcrm_cf7_default_user.map(item => parseInt(item, 10));
				const selectUserHtml = idcrm_admin_data.all_users.map(item => (
					`<option ${parseInt(idcrm_cf7_default_user) === parseInt(item[0]) ? 'selected' : ''} value="${item[0]}">${item[1]}</option>`
				));

				const selectStartPageHtml = idcrm_admin_data.all_pages.map(item => (
					`<option ${idcrm_start_page == item[0] ? 'selected' : ''} value="${item[0]}">${item[1]}</option>`
				));

				const freeSettings = [
					{
						tab: 'main',
						id: 'idcrm_cf7_default_user',
						title: idcrm_admin_data.default_cf7_user,
						html: `<select id="idcrm_cf7_default_user" class="autosave" name="idcrm_cf7_default_user"><option>${idcrm_admin_data.choose_user}</option>${selectUserHtml}</select>`,
					},
					{
						tab: 'main',
						id: 'idcrm_start_page',
						title: idcrm_admin_data.default_start_page,
						html: `<select id="idcrm_start_page" class="autosave" name="idcrm_start_page">${selectStartPageHtml}</select>`,
					}
				];

				idcrm_settings.markup.push(freeSettings);
			}
	}
})( jQuery );

var idcrmAdminDatetime = (function($) {
    "use strict";

    return {
        datePicker: function () {
            $("#idcrm_event_date").bootstrapMaterialDatePicker({
                weekStart: 1,
                time: false
            });
						$("#idcrm_contact_birthday").bootstrapMaterialDatePicker({
                weekStart: 1,
								format: "DD.MM.YYYY",
                time: false
            });
            $("#idcrm_event_time").bootstrapMaterialDatePicker({
                format: "HH:mm",
                time: true,
                date: false
            });
        }
    }
})( jQuery );
