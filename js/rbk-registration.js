(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
require("./listae/waiting-dialog.js");

var $ = jQuery;

var $frm_rbk_help = $("#frm_rbk_help");

$frm_rbk_help.on("submit", function (e) {
	e.preventDefault();

	var ajax_url = $frm_rbk_help.data("admin-ajax-url");

	if (!$('#help_email').val() || !$('#help_txt').val()) {
		alert(RBK_HELP_FEEDBACK.MSG_VALIDATION_EMPTY);
		return;
	}

	waitingDialog.show();

	$.post(ajax_url, {
		help_email: $('#help_email').val(),
		help_txt: $('#help_txt').val(),
		help_info: $('#help_xtra_i:checked').val(),
		_ajax_nonce: $frm_rbk_help.data("ajax-nonce"),
		action: 'rbk_submit_help_message_ajax'
	}, function(data) {
		if (data.success) {
			alert(RBK_HELP_FEEDBACK.MSG_SUCCESSFUL);
		} else {
			alert(data.data);
		}
	}).fail(function() {
		alert(RBK_HELP_FEEDBACK.MSG_ERROR);
	}).always(function() {
		waitingDialog.hide();
	});
});

},{"./listae/waiting-dialog.js":2}],2:[function(require,module,exports){
/**
 * Module for displaying "Waiting for..." dialog using Bootstrap
 *
 * @author Eugene Maslovich <ehpc@em42.ru>
 */

window.waitingDialog = (function ($) {
    'use strict';

	// Creating modal dialog's DOM
	var $dialog = $(
		'<div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true" style="padding-top:15%; overflow-y:visible;">' +
		'<div class="modal-dialog modal-m">' +
		'<div class="modal-content">' +
			'<div class="modal-header"><h3 style="margin:0;"></h3></div>' +
			'<div class="modal-body">' +
				'<div class="progress">' +
					'<div class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>' +
				'</div>' +
			'</div>' +
		'</div></div></div>');

	// Desde la version alpha-6 de bootstrap tenemos que meter
	// este switch para verificar que se termino la transicion
	// antes de ocultar el modal. Si no, da error.
	var finish_transitions = false;

	return {
		/**
		 * Opens our dialog
		 * @param options Custom options:
		 * 				  options.dialogSize - bootstrap postfix for dialog size, e.g. "sm", "m";
		 * 				  options.progressType - bootstrap postfix for progress bar type, e.g. "success", "warning".
		 */
		show: function (options) {
			// Assigning defaults
			if (typeof options === 'undefined') {
				options = {};
			}

			var settings = $.extend({
				message: 'Loading',
				dialogSize: 'm',
				progressType: '',
				onHide: null // This callback runs after the dialog was hidden
			}, options);

			// Configuring dialog
			$dialog.find('.modal-dialog').attr('class', 'modal-dialog').addClass('modal-' + settings.dialogSize);
			$dialog.find('.progress-bar').attr('class', 'progress-bar');

			if (settings.progressType) {
				$dialog.find('.progress-bar').addClass('progress-bar-' + settings.progressType);
			}

			$dialog.find('h3').text(settings.message);

			// Adding callbacks
			if (typeof settings.onHide === 'function') {
				$dialog.off('hidden.bs.modal').on('hidden.bs.modal', function (e) {
					settings.onHide.call($dialog);
				});
			}

			$dialog.on('shown.bs.modal', function (e) {
				finish_transitions = true;
			});

			// Opening dialog
			$dialog.modal('show');
		},
		/**
		 * Closes dialog
		 */
		hide: function (callback) {
			var hide_interval_id = setInterval(_try_hide, 100);

			function _try_hide() {
				// console.log("_try_hide");
				if (finish_transitions) {
					finish_transitions = false;
					clearInterval(hide_interval_id);

					if (typeof callback === 'function') {
						$dialog.off('hidden.bs.modal').on('hidden.bs.modal', function (e) {
							$dialog.off('hidden.bs.modal');
							callback();
						});
					}

					$dialog.modal('hide');
				}
			}
		}
	};

})(jQuery);

},{}],3:[function(require,module,exports){
jq2 = jQuery.noConflict();
jq2(function( $ ) {
	require("./inc/help-feedback.js");

	$("#btn_ae_register").click(function () {
		jQuery(document).ajaxSend(function (event, xhr, settings) {
		    settings.xhrFields = {
		        withCredentials: true
		    };
		});

		var url = RBKREG.AE_URL + "users/accessToken.html";
		var title = RBKREG.TITLE_AUTH_WIN;

		if (_is_ios()) {
			var win = window.open(url, title, "width=400, height=400");

			var timer = setInterval(function() {
				if (win.closed) {
					clearInterval(timer);
					try_get_auth();
				}
			}, 500);
		} else {
			_append_modal_html_template();

			var timer;
			var ret_access_token = "";
			var $amf = $("#ae_modal_form");
			var $ok_button = $amf.find(".btn-primary");
			$ok_button.off("click");
			$ok_button.attr('disabled', 'disabled');

			$amf.find(".modal-title").text(title);
			$amf.modal('show');

			$amf.on('shown.bs.modal', function() {
				$amf.find(".modal-body").html('<div class="wrap_aeform_modal"><iframe src="' + url + '" /></div>');

				timer = setInterval(function() {
					var jqxhr = jQuery.getJSON( RBKREG.AE_URL + "services/auth.html", {
						"from" : RBKREG.AE_FROM_URL
					}, function(data) {
						clearInterval(timer);
						ret_access_token = data.token;
						$ok_button.removeAttr('disabled');
						$ok_button.on("click", function () {
							register_acces_token(ret_access_token);
						});
					}).fail(function() {
						api_key_token = "";
					});
				}, 1000);
			}).on('hide.bs.modal', function() {
				clearInterval(timer);
			});
		}

		function try_get_auth() {
			var jqxhr = jQuery.getJSON( RBKREG.AE_URL + "services/auth.html", {
				"from" : RBKREG.AE_FROM_URL
			}, function(data) {
				register_acces_token(data.token);
			}).fail(function() {
				alert(RBKREG.AUTH_ERROR);
			});
		}

		function try_get_auth_iframe() {
			var jqxhr = jQuery.getJSON( RBKREG.AE_URL + "services/auth.html", {
				"from" : RBKREG.AE_FROM_URL
			}, function(data) {
				register_acces_token(data.token);
			});
		}


		function register_acces_token(access_token) {
			jQuery("#txt_ae_access_token").val(access_token);
			jQuery("#frm_ae_register").submit();
		}
	});

	function _append_modal_html_template() {
		if ($("#ae_modal_form").length > 0) return;

		var html = '<!-- ae Modal Form -->';
		html += '<div class="modal fade modal-fullscreen" id="ae_modal_form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">';
		html += '<div class="modal-dialog" role="document">';
		html += '<div class="modal-content">';

		//html += '<button type="button" class="close" data-dismiss="modal" aria-label="' + RBK_FORMS.CLOSE_LABEL + '">';
		//html += '<span aria-hidden="true">&times;</span>';
		//html += '</button>';


		html += '<div class="modal-header">';
		html += '<h5 class="modal-title"></h5>';
		html += '<button type="button" class="close" data-dismiss="modal" aria-label="' + RBKREG.CLOSE_LABEL + '">';
		html += '<span aria-hidden="true">&times;</span>';
		html += '</button>';
		html += '</div>';

		html += '<div class="modal-body">';
		html += '';
		html += '</div>';
		html += '<div class="modal-footer">';
		html += '<button type="button" class="btn btn-primary" disabled="disabled">' + RBKREG.OK_LABEL + '</button>';
		html += '<button type="button" class="btn btn-secondary" data-dismiss="modal">' + RBKREG.CLOSE_LABEL + '</button>';
		html += '</div>';
		html += '</div>';
		html += '</div>';
		html += '</div>';
		$("body").append($(html));
	}

	function _is_ios() {
		return navigator && navigator.userAgent &&
			navigator.userAgent.match(/(iPod|iPhone|iPad)/);
	}

	function _get_url_param_value(url, param_name) {

		if (url && URL) {
			var u = new URL(url);
			return u.searchParams.get(param_name);
		} else if (url) {
			var qs_index = url.indexOf('?');

			if (qs_index > -1) {
				console.log(url.substr(qs_index, url.length));
				var qs = _parse_query_string(url.substr(qs_index + 1, url.length));

				return qs[param_name];
			}
		}
		
		return "";
	}

	function _parse_query_string(query) {
		var vars = query.split("&");
		var query_string = {};
		for (var i = 0; i < vars.length; i++) {
			var pair = vars[i].split("=");
			// If first entry with this name
			if (typeof query_string[pair[0]] === "undefined") {
				query_string[pair[0]] = decodeURIComponent(pair[1]);
				// If second entry with this name
			} else if (typeof query_string[pair[0]] === "string") {
				var arr = [query_string[pair[0]], decodeURIComponent(pair[1])];
				query_string[pair[0]] = arr;
				// If third or later entry with this name
			} else {
				query_string[pair[0]].push(decodeURIComponent(pair[1]));
			}
		}

		return query_string;
	}
});

},{"./inc/help-feedback.js":1}]},{},[3])

//# sourceMappingURL=rbk-registration.js.map
