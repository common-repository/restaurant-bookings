(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
jq2 = jQuery.noConflict();
jq2(function( $ ) {
	widget_admin_catalog_init("restaurant_catalog_detail");
	widget_admin_catalog_init("restaurant_catalog_resume");

	function widget_admin_catalog_init(id_base) {
		var wrap_css_widget_selector = ".wrap-" + id_base + "-admin";

		$(document).on('widget-added', function(event, widget){
			var $widget = $(widget);
		    var widget_id = $widget.attr('id');
		    if ($widget.find(wrap_css_widget_selector).length > 0) {
		    	_init(widget_id);
			}
		});

		$(wrap_css_widget_selector).each(function () {
			_init($(this).parent().parent().parent().parent().attr('id'));
		});

		function _init(widget_id) {
			var $business = $("#" + widget_id + " .sel-business");

			if ($business.length == 0) return;

			var $section = $("#" + widget_id + " .sel-section");
			var $content = $("#" + widget_id + " .sel-content");
			var $wrap_content = $("#" + widget_id + " .wrap-content");

			$business.change(function (e) {
				$section.val("ae-carte-all");

				$wrap_content.hide();

				$content.find("option").remove();
				$content.find("optGroup").remove();
			});

			$section.change(function (e) {
				var current_business = $business.val();
				var current_section = $section.val();
				$wrap_content.hide();

				var ajax_action = "";

				$content.find("option").remove();
				$content.find("optGroup").remove();

				switch (current_section) {
					case "ae-menu-group":
						ajax_action = "ae-get-menu-groups";
					break;
					case "ae-carte-group":
						ajax_action = "ae-get-carte-groups";
					break;
					case "ae-menu":
						ajax_action = "ae-get-menus";
					break;
					case "ae-carte":
						ajax_action = "ae-get-cartes";
					break;
				}

				if (ajax_action == "") {
					return;
				}

				var jqxhr = $.getJSON( RBK_WIDGET_ADMIN.AJAX_URL + "?action=" + ajax_action + "&term=" + current_business, function(data) {
					switch (current_section) {
						case "ae-carte-group":
							$(data).each(function (i, carte) {
								var $carte = $('<optGroup/>').attr("label", carte.value);
								$(carte.groups).each(function (j, group) {
									$carte.append($('<option>', {
										value: group.id,
										text: group.value
									}));
								});

								$content.append($carte);
							});
						break;
						case "ae-menu":
							$(data).each(function (i, group) {
								var $group = $('<optGroup/>').attr("label", group.value);
								$(group.items).each(function (j, item) {
									$group.append($('<option>', {
										value: item.id,
										text: item.value
									}));
								});

								$content.append($group);
							});
						break;
						case "ae-carte":
						case "ae-menu-group":
							$(data).each(function (i, item) {
								$content.append($('<option>', {
									value: item.id,
									text: item.value
								}));
							});
						break;
					}

					if ($content.find("option").length > 0) {
						$wrap_content.show();
					}
				}).fail(function() {
					alert(RBK_WIDGET_ADMIN.ERROR_GET_CONTENT);
				});
			});
		}
	}
});

},{}]},{},[1])

//# sourceMappingURL=rbk-widget-admin.js.map
