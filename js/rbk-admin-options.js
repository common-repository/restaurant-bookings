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
require("./inc/help-feedback.js");

jq2 = jQuery.noConflict();
jq2(function( $ ) {
    $("#chk_disconnect").click(function (e) {
        if (!$("#chk_disconnect").prop("checked") && confirm(RBK_ADMIN_OPTIONS.MSG_CONFIRM_DISCONNECT)) {
            waitingDialog.show();
            $("#frm_rbk_options_page").submit();
        } else {
            $("#chk_disconnect").prop("checked", true);
        }
    });
});

},{"./inc/help-feedback.js":1}]},{},[3])

//# sourceMappingURL=rbk-admin-options.js.map
