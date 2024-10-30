(function( $ ) {
	'use strict';
	$(document).ready(function () {
		idcrmAmdinUI.userFormNotify();
		idcrmAmdinUI.customUserProfileFields();
		idcrmAdminDatetime.datePicker();
		idcrmAmdinUI.adminMenuHide();
		idcrmAmdinUI.adminMenuShowContact();

		if ($('#custom-settings-block').length) {
			idcrmAmdinUI.settingsHtml();
			idcrmAmdinUI.settingsRender();
			idcrmAmdinUI.settingsSave();
			idcrmAmdinUI.openMedia();
			idcrmAmdinUI.activateLicense();
		}
	});
})( jQuery );
