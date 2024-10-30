(function( $ ) {
	'use strict';

	$(document).ready(function () {
		idcrmScheduleManage.sortAndSlice();
		idcrmCompanyManage.manageCompanyButton();
		idcrmContactManage.deleteContactButton();
		idcrmContactManage.useSurname();
		idcrmContactManage.addContactSaveCookies();
		idcrmContactManage.assignContactButton();
		idcrmContactManage.updateProfileButton();
		idcrmCommentManage.sortComments();
		idcrmEventManage.checkAndDelete();
		idcrmEventManage.addEventButton();
		idcrmEventManage.editEventsButton();
		idcrmEventManage.editEventButton();

		setTimeout(() => {
      idcrmCommentManage.showComments();
    }, 100);

		idcrmEventManage.editEventSaveButton();
		idcrmCommentManage.sendCommentButton();
		idcrmCommentManage.editComment();
		idcrmCommentManage.deleteComment();
		idcrmCommentManage.showFullComment();
		idcrmCommentManage.addLike();
		idcrmNoteManage.editNone();
		idcrmTimelineManage.showMoreLess();
		idcrmUICustom.customInit();
		idcrmUI.datePicker();
		idcrmUI.addSelect2();
		idcrmUI.sidebarActiveLink();

		idcrmCommentManage.getUnseenComments();
		idcrmCommentManage.getUnseenCommentsTab();
		idcrmCommentManage.setUnseenCommentsCounter();
		idcrmCommentManage.checkCommentsTimer();
		idcrmCommentManage.refreshNotificationsTimer();
		// idcrmCommentManage.playNotificationSound();

		if ($("#single-contact").length || $("#single-company").length) {
			if ($("#enable-edit-permission").length) {
				idcrmUI.shortcuts();
				idcrmUI.editContactFields();
				idcrmUI.editCompanyFields();
			}
		}

		if ($("#user-profile").length) {
			idcrmUI.updateAvatar();
		}

	});

	$(window).resize(function(){
		idcrmUI.composeWindowWidth();
	});

	toastr.options = {
		"closeButton": true,
		"debug": false,
		"newestOnTop": false,
		"progressBar": false,
		"positionClass": "toast-bottom-right",
		"preventDuplicates": false,
		"onclick": null,
		"showDuration": "300",
		"hideDuration": "1000",
		"timeOut": "5000",
		"extendedTimeOut": "1000",
		"showEasing": "swing",
		"hideEasing": "linear",
		"showMethod": "fadeIn",
		"hideMethod": "fadeOut"
	};
})( jQuery );
