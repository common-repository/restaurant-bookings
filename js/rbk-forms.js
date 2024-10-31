(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
jq2 = jQuery.noConflict();
jq2(function( $ ) {
	_append_modal_html_template();

	var $amf = $("#ae_modal_form");

	$("a.ae-modal-form").click(function (e) {
		e.preventDefault();
		var $a = $(this);

		if (_is_ios()) {
			var url = $a.attr("href");
			var back = _get_url_param_value(url, "back");

			if (!back) {
				var origin = _get_url_param_value(url, "origin");

				if (origin) {
					url += "&back=" + encodeURIComponent(origin);
				}
			}
			
			document.location = url;
		} else {
			$amf.find(".modal-title").text($a.attr("title"));
			$amf.find(".modal-body").html('<div class="wrap_aeform_modal"><iframe src="' + $a.attr("href") + '&is_modal=true" /></div>');
			$amf.modal('show');
		}
	});

	function _append_modal_html_template() {
		if ($("#ae_modal_form").length > 0) return;

		var html = '<!-- ae Modal Form -->';
		html += '<div class="modal fade modal-fullscreen" id="ae_modal_form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">';
		html += '<div class="modal-dialog" role="document">';
		html += '<div class="modal-content">';

		html += '<div class="modal-header">';
		html += '<h5 class="modal-title"></h5>';
		html += '<button type="button" class="close" data-dismiss="modal" aria-label="' + RBK_FORMS.CLOSE_LABEL + '">';
		html += '<span aria-hidden="true">&times;</span>';
		html += '</button>';
		html += '</div>';

		html += '<div class="modal-body">';
		html += '';
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

},{}]},{},[1])

//# sourceMappingURL=rbk-forms.js.map
