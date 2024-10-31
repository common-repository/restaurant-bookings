(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
/*
TODO: Varios:
 - yo haria foco en el siguiente, por ejemplo al seleccionar un sitio que bajase 
 no se ve que ha cargado las horas
*/

jq2 = jQuery.noConflict();
jq2(function( $ ) {
	if ($(".widget_restaurant_booking_slots").length == 0) return;
	
	_append_modal_slots_template();
	_append_modal_error_template();
	
	var $msf = $("#ae_modal_slots_form");
	
	$(".widget_restaurant_booking_slots").each(function () {
		var $widget = $(this);
		var $widget_button = $widget.find(".btn-slot-booking");
		var $form = $widget.find(".booking-slots-widget-form"); 
		
		$widget_button.off("click").on("click", function () {
			$msf.find(".modal-title").text($widget_button.data("title"));
			
			$msf.find(".wrap_aeform_modal").append($form);
			
			$msf.off("hide.bs.modal").on("hide.bs.modal", function (event) {
				reset_party_size($form);
				$msf.find(".wrap_aeform_modal").removeClass("iframe-inserted").html("");
				$widget.append($form);
			});
			
			$msf.modal('show');
			
			search_party_sizes($widget, $form);
		});
	});
	
	function reset_party_size($form) {
		$form.find(".bkrs").val("");
		
		reset_dates($form);
		
		var $party_size_wrap = reset_field($form, ".party_size_wrap");
		
		$form.show();
		
		return $party_size_wrap;
	}
	
	function search_party_sizes($widget, $form) {
		_ajax_call($form, RBK_WIDGETS.EP_SLOTS_PARTY_SIZES, {}, function (data) {
			var $field_wrapper = $form.find(".party_size_wrap");
			var $content_wrap = $field_wrapper.find(".slot-form-content-wrap");
			$content_wrap.html("");
			
			var $cur_row = $('<div class="row" />');

			for (var i = 0; i < data.item.length; i++) {
				var value = data.item[i];
				
				var $current_btn = create_btn_option({
					css_class: "btn-party-size"
					,text: value
					,callback_click: function ($_btn, e) {
						// TODO: Comentar con jlgo, he puesto la clase disabled porque se nota mucho mas
						// la diferencia cuando esta seleccionado de cuando no lo esta... deberiamos usar
						// otra clase como inactive o algo asi y ponerla por defecto...
						// Si convence los estilos por defecto (sin usar el disabled) deberiamos quitar
						// esta opcion... si por el contrario vamos a usar el disabled, tendremos que 
						// hacerlo para todas (lo mismo se puede meter la rutina en create_btn_option)
						$form.find(".btn-party-size").removeClass("active").addClass("disabled");
						$_btn.addClass("active").removeClass("disabled");
						
						$form.find(".bkrs").val($_btn.data("value"));
						
						reset_dining_area($form);
						reset_times($form);
						
						search_dates($widget, $form);
					}
					,index: i
					,data_value: value
				});

				$cur_row.append(
					$('<div class="col-3 option-wrap" />').append(
						$current_btn
					)
				);
			}

			$content_wrap.append($cur_row);
			$field_wrapper.show();
			_scroll_to($field_wrapper);
		});
	}
	
	function reset_dates($form) {
		$form.find(".date").val("");
		$form.find(".timestamp").val("");
		
		reset_dining_area($form);
		
		// Caso especial para destruir el datepicker
		$form.find(".date_wrap .slot-form-content-wrap").datepicker( "destroy" )
		
		return reset_field($form, ".date_wrap");
	}

	function search_dates($widget, $form) {
		
		var request_args = {'p': $form.find(".bkrs").val()};

		_ajax_call($form, RBK_WIDGETS.EP_SLOTS_DATES, request_args, function (data) {
			var $field_wrapper = reset_dates($form);
			var $content_wrap = $field_wrapper.find(".slot-form-content-wrap");
			
			var more_dates = false;
			
			// TODO: Coger del html el locale
			var formatDate = new Intl.DateTimeFormat('es-ES', { weekday: 'long', month: 'long', day: 'numeric' });
			
			var $cur_row = $('<div class="row" />');

			for (var i = 0; i < data.item.length; i++) {
				var value = _parse_date(data.item[i]);

				var $current_btn = create_btn_option({
					css_class: "btn-date"
					,text: formatDate.format(value)
					,callback_click: function ($_btn, e) {
						$form.find(".btn-date").removeClass("active");
						$_btn.addClass("active");
						
						var date = new Date();
						date.setTime($_btn.data("value"));
						$form.find(".date").val(_format_date(date));
						$form.find(".timestamp").val($_btn.data("value"));
						
						reset_times($form);
						
						search_dining_areas($widget, $form);
					}
					,index: i
					,data_value: value
				});
				
				$cur_row.append(
					$('<div class="col-6 option-wrap" />').append(
						$current_btn
					)
				);
				
				if (i > 1) {
					more_dates = true;
					break;
				}
			}

			if (more_dates) {
				var $datepicker_wrap = $('<div class="col-6 option-wrap ae-slots-datepicker-wrap" />');
				
				var $datepicker_btn = create_btn_option({
					css_class: "btn-datepicker"
					,text: "Mostrar mas fechas" // TODO: i18n 
					,callback_click: function ($_btn, e) {
						$content_wrap.find(".option-wrap").hide();
						$datepicker_btn.hide();
						
						$datepicker_wrap.addClass("col-12").removeClass("col-6");
						$content_wrap.find(".ae-slots-datepicker-wrap").show();

						$datepicker_wrap.datepicker({
							numberOfMonths: 1,
							firstDay: 1,
							dateFormat: 'dd/mm/yy',
							minDate: new Date(data.item[0]),
							maxDate: new Date(data.item[data.item.length - 1]),
							beforeShowDay: function (date) {
								for (var i = 0; i < data.item.length; i++) {
									var date_parsed = new Date(Date.parse(date));
									var cur_data_parsed = new Date(_parse_date(data.item[i]));

									if (cur_data_parsed.getFullYear() == date_parsed.getFullYear() && 
										cur_data_parsed.getMonth() == date_parsed.getMonth() && 
										cur_data_parsed.getDate() == date_parsed.getDate()) {

									// if (Date.parse(date) == _parse_date(data.item[i])) {
										return [true];
									}
								}
								return [false];
							},
							onSelect: function (dateText, obj) {
								var cur_selected_date = new Date(obj.selectedYear, obj.selectedMonth, obj.selectedDay);
								var cur_selected_timestamp = Date.parse(cur_selected_date);
								
								$form.find(".date").val(dateText);
								$form.find(".timestamp").val(cur_selected_timestamp);
								
								reset_times($form);
								search_dining_areas($widget, $form);
							}
						});

						_scroll_to($datepicker_wrap);
					}
				});
				
				$datepicker_wrap.append($datepicker_btn);
				$cur_row.append($datepicker_wrap);
			}

			$content_wrap.append($cur_row);
			$field_wrapper.show();
			_scroll_to($field_wrapper);
		});
	}
	
	function reset_dining_area($form) {
		$form.find(".da").val("");
		
		reset_times($form);
		
		return reset_field($form, ".dining_area_wrap");
	}
	
	function search_dining_areas($widget, $form) {
		var request_args = {
			'p': $form.find(".bkrs").val(),
			'd': $form.find(".timestamp").val(),
			// TODO: Coger del html el locale
			'lang' : 'es'
		};
		
		_ajax_call($form, RBK_WIDGETS.EP_SLOTS_DINING_AREAS, request_args, function (data) {
			if (data.item.length == 1) {
				var item = data.item[0];
				$form.find(".da").val(item.id);
				search_times($widget, $form);
			} else {
				var $field_wrapper = reset_dining_area($form);
				var $content_wrap = $field_wrapper.find(".slot-form-content-wrap");
				
				var $cur_row = $('<div class="row" />');

				for (var i = 0; i < data.item.length; i++) {
					var item = data.item[i];
					
					var $current_btn = create_btn_option({
						css_class: "btn-dining-area"
						,text: item.label
						,callback_click: function ($_btn, e) {
							$form.find(".btn-dining-area").removeClass("active");
							$_btn.addClass("active");
							
							$form.find(".da").val($_btn.data("value"));
							
							search_times($widget, $form);
						}
						,index: i
						,data_value: item.id
					});

					$cur_row.append(
						$('<div class="col-12 col-md-6 option-wrap" />').append(
							$current_btn
						)
					);
				}

				$content_wrap.append($cur_row);
				$field_wrapper.show();
				_scroll_to($field_wrapper);
			}
		});
	}
	
	function reset_times($form) {
		$form.find(".time").val("");
		
		return reset_field($form, ".time_wrap");
	}
	
	function search_times($widget, $form) {
		var request_args = {
			'p': $form.find(".bkrs").val(),
			'd': $form.find(".timestamp").val(),
			'da': $form.find(".da").val(),
			'lang' : 'es'
		};

		_ajax_call($form, RBK_WIDGETS.EP_SLOTS_TIMES, request_args, function (data) {
			var $field_wrapper = reset_times($form);
			var $content_wrap = $field_wrapper.find(".slot-form-content-wrap");
			var $cur_row = $('<div class="service-wrap" />');
			var $nav_tabs = $('<ul class="nav nav-tabs" id="serviceNavTabs" role="tablist" />');
			var $content_tabs = $('<div class="tab-content" id="serviceContentTabs" />');

			for (var j = 0; j < data.group.length; j++) {
				var cur_group = data.group[j];

				$nav_tabs.append(
					$('<li class="nav-item" />').append(
						$('<a class="nav-link ' + (j == 0 ? ' active' : '') + '" id="service-' + j + '-tab" data-toggle="tab" href="#service-' + j + '" role="tab" aria-controls="service-' + j + '" aria-selected="' + (j == 0 ? 'true' : 'false') + '" />').text(
							RBK_WIDGETS["MSG_SERVICE_" + cur_group.name] ? RBK_WIDGETS["MSG_SERVICE_" + cur_group.name] : cur_group.name
						)
					)
				);

				var $currrent_group = $('<div class="tab-pane fade' + (j == 0 ? ' show active' : '') + '" id="service-' + j + '" role="tabpanel" aria-labelledby="service-' + j + '-tab" />');
				
				var $current_group_row = $('<div class="row group-service-row-wrap" />');

				for (var i = 0; i < cur_group.item.length; i++) {
					var item = cur_group.item[i];
	
					var $current_btn = create_btn_option({
						css_class: "btn-time"
						,text: item.label
						,callback_click: function ($_btn, e) {
							$form.find(".btn-time").removeClass("active");
							$_btn.addClass("active");
							
							$form.find(".time").val($_btn.data("value"));
							
							if (!_is_ios()) {
								var form_url = $form.attr("action");
								
								form_url = form_url.replace(/{slug}/, encodeURIComponent($form.find("[name='slug']").val()));
								form_url += (form_url.search(/\?/) > 0) ? "&" : "?";
								
								form_url += _add_query_arg($form, "origin");
								form_url += _add_query_arg($form, "back")
								form_url += _add_query_arg($form, "bkrs")
								form_url += _add_query_arg($form, "date")
								form_url += _add_query_arg($form, "timestamp")
								form_url += _add_query_arg($form, "da")
								form_url += _add_query_arg($form, "time")
								
								form_url += "&is_modal=true";
								
								$form.hide();
								$widget.append($form);
								
								$msf.find(".wrap_aeform_modal").html("");
								
								var $iframe = $('<iframe id="slots_form_target" src="' + form_url + '" />');
								
								$msf.find(".wrap_aeform_modal").addClass("iframe-inserted").append($iframe);
							} else {
								$form.submit();
							}
						}
						,index: i
						,data_value: item.value
					});
	
					$current_group_row.append(
						$('<div class="col col-3 col-md-2 option-wrap" />').append(
							$current_btn
						)
					);
				}

				$currrent_group.append(
					$current_group_row
				);

				$content_tabs.append(
					$currrent_group
				);
			}

			$cur_row.append($nav_tabs);
			$cur_row.append($content_tabs);

			$content_wrap.append($cur_row);
			
			$field_wrapper.show();

			_scroll_to($field_wrapper);
		});
	}
	
	function _add_query_arg($form, arg_name) {
		return "&" + arg_name + "=" + encodeURIComponent($form.find("[name='" + arg_name + "']").val());
	}
	
	function _format_date(date) {
		var dd = date.getDate(); 
		var mm = date.getMonth() + 1; 
		var yyyy = date.getFullYear(); 
		
		if (dd < 10) dd = '0' + dd; 
		if (mm < 10) mm = '0' + mm;
		
		return dd + '/' + mm + '/' + yyyy; 
	}
	
	function _ajax_call($form, url_template, request_args, callback) {
		var current_url = url_template.replace("{business-id}", $form.data("id"));
		
		$.ajax({
			url: current_url,
			jsonp: "callback",
			dataType: 'jsonp',
			crossDomain: true,
			type: 'GET',
			data: request_args
		}).done(function (data) {
			if (!data || data.code == 404) {
				var $modal_error = $("#ae_modal_err_slots");
				$modal_error.find('.modal-body').html('<div class="alert alert-danger" role="alert">' + RBK_WIDGETS.ERR_SLOTS_NOT_FOUND + '</div>');
				$modal_error.modal("show");
			} else {
				// $form.find(".bkslotgroupfield").hide();
				
				callback(data);
			}
		});
	}
	
	function reset_field($form, selector) {
		var $field_wrapper = $form.find(selector);
		
		$field_wrapper.find(".slot-form-content-wrap").html("");
		$field_wrapper.hide();
		
		return $field_wrapper;
	}
	
	function _append_modal_slots_template() {
		if ($("#ae_modal_slots_form").length > 0) return;

		var html = '<!-- ae Modal Slots Form -->';
		html += '<div class="modal fade modal-fullscreen" id="ae_modal_slots_form" tabindex="-1" role="dialog" aria-labelledby="ae_modal_slots_form" aria-hidden="true">';
		html += '<div class="modal-dialog" role="document">';
		html += '<div class="modal-content">';

		html += '<div class="modal-header">';
		// TODO: Conseguir la imagen featured del restaurante
		// Con la imagen funcionaba antes de hacer el append de botones... ahora no
		// html += '<img src="https://unrc.int.listae.com/wp-content/uploads/sites/3/2017/06/delivery.jpg">';
		html += '<h5 class="modal-title"></h5>';
		html += '<button type="button" class="close" data-dismiss="modal" aria-label="' + RBK_FORMS.CLOSE_LABEL + '">';
		html += '<span aria-hidden="true">&times;</span>';
		html += '</button>';
		html += '</div>';

		html += '<div class="modal-body">';
		html += '<div class="wrap_aeform_modal form-modal"></div>';
		html += '</div>';

		html += '</div>';
		html += '</div>';
		html += '</div>';
		
		$("body").append($(html));
	}
	
	function _append_modal_error_template() {
		if ($("#ae_modal_err_slots").length > 0) return;
		
		var html = '<div class="modal fade modal-error" id="modal_err_slots" tabindex="-1" role="dialog" aria-labelledby="modal_err_slots" aria-hidden="true">';
		html += '<div class="modal-dialog" role="document">';
		html += '<div class="modal-content">';
		html += '<div class="modal-header">';
		html += '<h5 class="modal-title" id="modal_err_slots_label">ERROR</h5>';
		html += '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
		html += '<span aria-hidden="true">&times;</span>';
		html += '</button>';
		html += '</div>';
		html += '<div class="modal-body">';
		html += '</div>';
		html += '<div class="modal-footer">';
		html += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
		html += '</div>';
		html += '</div>';
		html += '</div>';
		html += '</div>';
		html += '<!-- #ae_modal_err_slots -->';
		
		$("body").append($(html));
	}
	
	
	function _is_ios() {
	    return navigator && navigator.userAgent &&
	    	navigator.userAgent.match(/(iPod|iPhone|iPad)/);
	}

	function create_btn_option(_options) {
		// css_class, text, callback_click, index, data_value) {
		var oo = $.extend({
			css_class: null,
			text: null,
			callback_click: null,
			index: null,
			data_value: null
		}, _options );

		var css_btn_class = "btn btn-primary value-wrap";

		if (oo.css_class != null) {
			css_btn_class += " " + oo.css_class;
		}

		if (oo.index != null) {
			css_btn_class += " index-" + oo.index;
		}

		var $btn = $('<button type="button" class="' + css_btn_class + '" />');

		if (oo.text != null) {
			$btn.text(oo.text);
		}

		if (oo.data_value != null) {
			$btn.data("value", oo.data_value);
		}

		if (oo.callback_click != null) {
			$btn.off("click").on("click", function (e) {
				oo.callback_click($btn, e, oo);
			});
		}

		return $btn;
	}

	function _parse_date(_date) {
		// OSX e IOS no parsean bien esta fecha si viene con hora...
		// por lo que por defecto vamos a quitar la hora ya que en este caso
		// vienen fechas con hora 00:00
		var str_date = _date;
		if (typeof _date === 'string' && _date.split(" ").length == 2) {
			str_date = _date.split(" ")[0];
		}
		
		return Date.parse(str_date);
	}

	function _scroll_to($target) {
		$msf.animate({
			scrollTop: $target.offset().top
		}, 1000);
	}
});

},{}],2:[function(require,module,exports){
jq2 = jQuery.noConflict();
jq2(function( $ ) {
	// Slots widgets
	require("./inc/slots/init.js");
	
	if ($("#wrap-reviews").infinitescroll) {
		$("#wrap-reviews").infinitescroll({
			navSelector  : '.nav-reviews',	// selector for the paged navigation 
			nextSelector : 'a.nav-reviews-next',  // selector for the NEXT link (to page 2)
			itemSelector : '#wrap-reviews .ae-review',	 // selector for all items you'll retrieve
			state: {
				currPage: 1
			},
			loading: { 
				msgText: RBK_WIDGETS.MORE_REVIEWS_TXT,
				finishedMsg: RBK_WIDGETS.NO_MORE_REVIEWS_TXT,
				img: RBK_WIDGETS.IMG_LOADING
			} },
			// trigger Masonry as a callback
			function( newElements, opts, url ) {
				if ('object' === typeof _gaq) {
					_gaq.push(['_trackPageview', url]);
				}
				
				if ('function' === typeof ga) {
					ga('send', 'pageview', url);
				}
				nextPosts(nextPostsSelector);
				
				// hide new items while they are loading
				var $newElems = $( newElements ).css({ opacity: 0 });
				// ensure that images load before adding to masonry layout
				$newElems.imagesLoaded(function(){
					// show elems now they're ready
					$newElems.animate({ opacity: 1 }, function () {
						$("a.nav-reviews-next").show();
					});
				});
			}
		);
		
		$(window).unbind('.infscr');
		
		var nextPostsSelector = 'a.nav-reviews-next';
		var nextPosts = function(sSel) {
			$( sSel ).unbind('click');
			$( sSel ).click( function( e ) {
				e.preventDefault();
				$("#wrap-reviews").infinitescroll('retrieve');
				return false;
			} );
		};
		nextPosts(nextPostsSelector);
	}
});
},{"./inc/slots/init.js":1}]},{},[2])

//# sourceMappingURL=rbk-widgets.js.map
