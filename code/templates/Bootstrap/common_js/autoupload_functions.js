/**
 * Created by JetBrains PhpStorm.
 * User: User
 * Date: 02.03.12
 * Time: 15:45
 * To change this template use File | Settings | File Templates.
 */


function getPreloaderCodeForFieldId(fieldId) {
	// window.SJB_GlobalSiteUrl defined in index.tpl
	return '<span id="preloader_image_circular_16_for_' + fieldId + '">&nbsp;<img src="' + window.SJB_UserSiteUrl + '/templates/_system/main/images/ajax_preloader_circular_16.gif" /></span>';
}


/**
 * Autoupload file to server (Common handler for ".autouploadField" class fields)
 */
$(document).on('change', '.autouploadField', function() {
	// gets params from input=file field
	var fieldAction = $(this).attr('field_action');
	var fieldId     = $(this).attr('field_id');
	var form        = $(this).parents('form');
	var targetName  = $(this).attr('field_target');
	var listingId   = $(this).attr('listing_id');
	// formToken will send in POST with form fields
	var targetElement = document.getElementById(targetName);
	var preloader   = $(this).after( getPreloaderCodeForFieldId(fieldId) );
	var listingTypeId = $("input[name=listing_type_id]").val();
	if (listingTypeId) {
		listingTypeId = '&listing_type_id=' + listingTypeId;
	}

	var browser = navigator.appName.toLowerCase();
	var options = {
		target: targetElement,
		url:  window.SJB_GlobalSiteUrl + '/system/miscellaneous/ajax_file_upload_handler/?ajax_submit=1&listing_id=' + listingId + listingTypeId +'&ajax_action=' + fieldAction + '&uploaded_field_name=' + fieldId,
		success: function(data) {
			if (browser == 'microsoft internet explorer') {
				$(targetName).load(url);
			}
		},
		error: function(data) {
			alert('error occured');
		},
		complete: function(data) {
			$(preloader).remove();
		}
	};
	$(form).ajaxSubmit(options);
	return false;
});

$(document).on('click', ".delete_profile_logo", function() {
	var url     = window.SJB_GlobalSiteUrl + '/system/miscellaneous/ajax_file_upload_handler/';
	var fileId  = $(this).attr('file_id');
	var fieldId = $(this).attr('field_id');
	var formToken = $(this).attr('form_token');
	var params  = {
		'ajax_action': 'delete_profile_logo',
		'field_id' : fieldId,
		'file_id' : fileId,
		'form_token' : formToken
	};

	// this value set in admin field templates
	var userSid = $(this).attr('user_sid');
	if (userSid) {
		params.user_sid = userSid;
	}


	var preloader = $(this).after( getPreloaderCodeForFieldId(fieldId) );
	$.get(url, params, function(data){
		if (data.result == 'success') {
			$("#autoloadFileSelect_" + fieldId).show();
			if ($("#extra_field_info_" + fieldId).length) {
				$("#extra_field_info_" + fieldId).show();
			}
			$("#profile_logo_" + fieldId).empty();
		}
		$(preloader).remove();
	}, 'json');
	// prevent link redirect
	return false;
});

function getClassifiedsLogoData(fieldId, formToken, listingId) {
	var url = window.SJB_GlobalSiteUrl + '/system/miscellaneous/ajax_file_upload_handler/';
	var params = {
		'ajax_action': 'get_file_field_data',
		'field_id' : fieldId,
        'listing_id' : listingId,
		'form_token' : formToken
	};
	var preloader = $(this).after( getPreloaderCodeForFieldId(fieldId) );
	// check uploaded files to display
	$.get(url, params, function(data) {
		$(preloader).remove();
		if (data.length == 0 || $.trim(data) == '') {
			return false;
		}
		$("#logo_field_content_" + fieldId).html(data);
	});

	// prevent link redirect
	return false;
}

$(document).on('click', ".delete_listing_logo", function() {
	var fieldId   = $(this).attr('field_id');
	var fileId    = $(this).attr('file_id');
	var listingId = $(this).attr('listing_id');
	var formToken = $(this).attr('form_token');

	// window.SJB_GlobalSiteUrl defined in index.tpl
	var url = window.SJB_GlobalSiteUrl + '/system/miscellaneous/ajax_file_upload_handler/';
	var params = {
		'ajax_action': 'delete-file',
		'field_id' : fieldId,
		'file_id' : fileId,
		'listing_id' : listingId,
		'form_token': formToken
	};

	var preloader = $(this).after( getPreloaderCodeForFieldId(fieldId) );
	$.get(url, params, function(data){
		if (data.result == 'success') {
			// remove errors block in field
			$("#logo_field_content_" + fieldId + " > p.error").remove();
			$("#listing_logo_" + fieldId).empty();
			if ($("#extra_field_info_" + fieldId).length) {
				$("#extra_field_info_" + fieldId).show();
			}
			$("#autoloadFileSelect_" + fieldId).show();
		} else if (data.result == 'error') {
			for (error in data.errors) {
				$("#logo_field_content_" + fieldId).prepend('<p class="error">' + error + '</p>');
			}
		}
		$(preloader).remove();
	}, 'json');
	// prevent link redirect
	return false;
});


/**
 * function will check file in temporary storage
 *
 * @param fieldId
 */
function getFileFieldData(fieldId, listingId, listingTypeId, formToken) {
	var url = window.SJB_GlobalSiteUrl + '/system/miscellaneous/ajax_file_upload_handler/';
	var params = {
		'ajax_action': 'get_file_field_data',
		'field_id' : fieldId,
		'listing_id' : listingId,
		'listing_type_id' : listingTypeId,
		'form_token': formToken
	};
	var preloader = $('[name="' + fieldId + '"]').after( getPreloaderCodeForFieldId(fieldId) );
	preloader = preloader.next();
	// check uploaded files to display
	$.get(url, params, function(data) {
		$(preloader).remove();
		if (data.length == 0 || $.trim(data) == '') {
			return false;
		}
		$("#file_field_content_" + fieldId).html(data);
	});

	// prevent link redirect
	return false;
}

$(document).on('click', ".delete-file", function() {
	var fieldId   = $(this).attr('field_id');
	var fileId    = $(this).attr('file_id');
	var listingId = $(this).attr('listing_id');
	var formToken = $(this).attr('form_token');

	// window.SJB_GlobalSiteUrl defined in index.tpl
	var url = window.SJB_GlobalSiteUrl + '/system/miscellaneous/ajax_file_upload_handler/';
	var params = {
		'ajax_action': 'delete-file',
		'field_id' : fieldId,
		'file_id' : fileId,
		'listing_id' : listingId,
		'form_token': formToken
	};

	var preloader = $(this).after( getPreloaderCodeForFieldId(fieldId) );
	$.get(url, params, function(data) {
		if (data.result == 'success') {
			$("#file_" + fieldId).empty();
			$("#input_file_" + fieldId).show();
			if ($("#extra_field_info_" + fieldId).length) {
				$("#extra_field_info_" + fieldId).show();
			}
			// remove errors block in field
			$("#file_field_content_" + fieldId + " > div.alert").remove();
		} else if (data.result == 'error') {
			for (error in data.errors) {
				$("#file_field_content_" + fieldId).prepend('<div class="alert alert-danger">' + error + '</div>');
			}
		}
		$(preloader).remove();
	}, 'json');
	// prevent link redirect
	return false;
});

function disableSubmitButton(buttonPostID) {
	setTimeout(function () {
		$("#" + buttonPostID).attr("disabled", true);
	}, 50);
	setTimeout(function () {
		$("#" + buttonPostID).attr("disabled", false);
	}, 7000);
}

/**
* Tool Tip Script
 */

$(document).ready(function() {
	var count = 0;
	$("[class^='longtext']").each(function() {
		var classParts = $(this).attr('class').split('-');
		var max_length = classParts[1];
		$("." + classParts[0] + "-" + max_length).each(function() {
			var long_tooltip = $(this).text();
			var span_class = $(this).addClass("tooltip-counter-" + count++);
			if (span_class.text().length > max_length) {
				var content = span_class.text();
				span_class.poshytip({
					className: 'tip-darkgray',
					showTimeout: 0.5,
					alignY: 'bottom',
					offsetX: 10,
					offsetY: 15,
					showTimeout: 100,
					followCursor: true,
					content: content
				});
				var val = long_tooltip.substring(0,max_length);
				var five_chars = val.substr(val.length - 5);
				var last_chars = five_chars.split("");
				$.each(last_chars, function(key, val) {
					last_chars[key] = val.replace(" ", "&nbsp;");
				});
				span_class.html(
					"<span style='opacity: 1.0'>" + val.substring(0,max_length-5) + "</span>" +
						"<span style='filter: progid:DXImageTransform.Microsoft.Alpha(opacity=50); filter: alpha(opacity=50); opacity: 0.5; display: inline-block;'>" + last_chars[0] + "</span>" +
						"<span style='filter: progid:DXImageTransform.Microsoft.Alpha(opacity=40); filter: alpha(opacity=40); opacity: 0.4; display: inline-block;'>" + last_chars[1] + "</span>" +
						"<span style='filter: progid:DXImageTransform.Microsoft.Alpha(opacity=30); filter: alpha(opacity=30); opacity: 0.3; display: inline-block;'>" + last_chars[2] + "</span>" +
						"<span style='filter: progid:DXImageTransform.Microsoft.Alpha(opacity=20); filter: alpha(opacity=20); opacity: 0.2; display: inline-block;'>" + last_chars[3] + "</span>" +
						"<span style='filter: progid:DXImageTransform.Microsoft.Alpha(opacity=10); filter: alpha(opacity=10); opacity: 0.1; display: inline-block;'>" + last_chars[4] + "</span>"
				);
			}
		});
	});
	
	$(".sortable-input").each(function() {
		var element = this;
		var firstInput = '';
		var inputs = $(this).children().map(function() {
			if ($(this).is('input')) {
				if ($(this).is(':hidden:first-child')) {
					firstInput = $(this);
				}
				$(this).wrap('<div>');
			} else {
				var isInput = $(this).next().is('input');
				var isLastChild = $(this).is(':last-child');
				$(this).appendTo($(this).prev('div'));
				if (isInput || isLastChild) {
					return $(this).parent('div');
				}
			}
		}).get().sort(function(a, b) { 
			if (a.children('input').next().text().toUpperCase() > b.children('input').next().text().toUpperCase()) {
				return 1;
			}
			else if (a.children('input').next().text().toUpperCase() < b.children('input').next().text().toUpperCase()) {
				return -1;
			}
			return 0;
		});
		$(element).empty().append(firstInput);
		$(inputs).each(function() {
			$(element).append($(this).children().unwrap());
		});
	});
});