(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){

/**
* Funciones comunes que se usan en varios sitios
*/

( function( window, factory ) {
  // universal module definition
  /*jshint strict: false */ /* globals define, module, require */
  if ( typeof define == 'function' && define.amd ) {
    // AMD
      return factory( window );
  } else if ( typeof module == 'object' && module.exports ) {
    // CommonJS
    module.exports = factory(
      window
    );
  } else {
    // browser global
    window.GMCUtils = factory(
      window
    );
  }
}( window, function factory( window ) {
var GMCUtils;

GMCUtils = {
	ScaleImage2: function (srcwidth, srcheight, targetwidth, targetheight, fLetterBox) {
		var result = { width: 0, height: 0, left : 0, top : 0 };

		if ((srcwidth <= 0) || (srcheight <= 0) || (targetwidth <= 0) || (targetheight <= 0)) {
			return result;
		}

		// scale to the target width
		var scaleX1 = targetwidth;
		var scaleY1 = (srcheight * targetwidth) / srcwidth;

		// scale to the target height
		var scaleX2 = (srcwidth * targetheight) / srcheight;
		var scaleY2 = targetheight;

		// now figure out which one we should use
		var fScaleOnWidth = (scaleX2 > targetwidth);
		if (fScaleOnWidth) {
			fScaleOnWidth = fLetterBox;
		}
		else {
			fScaleOnWidth = !fLetterBox;
		}

		if (fScaleOnWidth) {
			result.width = Math.floor(scaleX1);
			result.height = Math.floor(scaleY1);
		}
		else {
			result.width = Math.floor(scaleX2);
			result.height = Math.floor(scaleY2);
		}
		result.left = Math.floor((targetwidth - result.width) / 2);
		result.top = Math.floor((targetheight - result.height) / 2);

		return result;
	},
	setParam: function(uri, key, val) {
		return uri
			.replace(new RegExp("([?&]"+key+"(?=[=&#]|$)[^#&]*|(?=#|$))"), "&"+key+"="+encodeURIComponent(val))
			.replace(/^([^?&]+)&/, "$1?");
	},
	ResizeWindow: function (callback, delta) {
		var win_time;
		var win_timeout = false;
		var win_delta = delta ? delta : 200;

		if (typeof window.onorientationchange != 'undefined') {
			window.addEventListener("orientationchange", _resize_window, false);
		} else {
			jQuery( window ).resize( _resize_window );
		}

		function _resize_window() {
			win_time = new Date();

			if (win_timeout === false) {
				win_timeout = true;
				setTimeout(_resize_window_end, win_delta);
			}
		}

		function _resize_window_end() {
			if (new Date() - win_time < win_delta) {
				setTimeout(_resize_window_end, win_delta);
			} else {
				if (win_timeout) {
					win_timeout = false;
					callback();
				}
			}
		}

	}
};
return GMCUtils;
}));

},{}],2:[function(require,module,exports){
//COOKIES con jQuery
if (!jQuery.cookie) {
	jQuery.cookie = function(name, value, options) {
	    if (typeof value != 'undefined') { // name and value given, set cookie
	        options = options || {};
	        if (value === null) {
	            value = '';
	            options.expires = -1;
	        }
	        var expires = '';
	        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
	            var date;
	            if (typeof options.expires == 'number') {
	                date = new Date();
	                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
	            } else {
	                date = options.expires;
	            }
	            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
	        }
	        // CAUTION: Needed to parenthesize options.path and options.domain
	        // in the following expressions, otherwise they evaluate to undefined
	        // in the packed version for some reason...
	        var path = options.path ? '; path=' + (options.path) : '; path=/';
	        var domain = options.domain ? '; domain=' + (options.domain) : '';
	        var same_site = '; SameSite=' + (options.same_site ? options.same_site : 'None');
	        var secure = options.secure ? '; secure' : ''; 
	        
	        if (location.protocol != 'https:') {
	        	same_site = '; SameSite=Strict';
	        	secure = '';
	        } else {
	        	secure = '; secure'; 
	        }
	        
	        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, same_site, secure].join('');
	    } else { // only name given, get cookie
	        var cookieValue = null;
	        if (document.cookie && document.cookie != '') {
	            var cookies = document.cookie.split(';');
	            for (var i = 0; i < cookies.length; i++) {
	                var cookie = jQuery.trim(cookies[i]);
	                // Does this cookie string begin with the name we want?
	                if (cookie.substring(0, name.length + 1) == (name + '=')) {
	                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
	                    break;
	                }
	            }
	        }
	        return cookieValue;
	    }
	};

	var CookieList = function(cookieName, options) {
		//So we will split the cookie by comma to get the original array
		var cookie = jQuery.cookie(cookieName);
		//Load the items or a new array if null.
		var items = cookie ? cookie.split(",") : new Array();

		//Return a object that we can use to access the array.
		return {
		    "add": function(val) {
		        items.push(val);
		        jQuery.cookie(cookieName, items.join(','), options);
		    },
		    "remove": function(val) {
			    var pos = items.indexOf(val);
			    if (pos != -1) {
			        items.splice(pos, 1);
			        jQuery.cookie(cookieName, items.join(','), options);
			    }
		    },
		    "clear": function() {
		        items = new Array();
		        //clear the cookie.
		        jQuery.cookie(cookieName, "", options);
		    },
		    "items": function() {
		        //Get all the items.
		        return items;
		    },
			"contains": function(val) {
				for (var i=0; i<items.length; i++) {
					if (val == items[i]) {
						return true;
					}
				}
				return false;
			}
		};
	};
	
	window.CookieList = CookieList;
}
},{}],3:[function(require,module,exports){
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

},{}],4:[function(require,module,exports){
( function( window, factory ) {
	// universal module definition
	/*jshint strict: false */ /* globals define, module, require */
	if ( typeof define == 'function' && define.amd ) {
		// AMD
		return factory( window );
	} else if ( typeof module == 'object' && module.exports ) {
		// CommonJS
		module.exports = factory(
			window
		);
	} else {
		// browser global
		window.call_ajax_ep = factory(
			window
		);
	}
}( window, function factory( window ) {
	/**
	 * Helper para hacer llamadas ajax que producen jsonp
	 * 
	 * @param ep_url
	 * @param args
	 * @param callback_success
	 * @returns
	 */
	return function (ep_url, args, callback_success, callback_error) {
		var call_ep_finish = false;

		var ajax_args = {
			jsonp: "callback",
			dataType: "jsonp",
			url: ep_url,
			success: function( response ) {
				if (!call_ep_finish) {
					callback_success(response);
				}
			},
			complete: function(jqXHR, textStatus) {
				if (!call_ep_finish) {
					call_ep_finish = true;
					//-- waitingDialog.hide();
				}
			},
			// Al utilizar JSONP Esto no sirve para nada
			error: function( jqXHR, textStatus, errorThrown ) {
				if (!call_ep_finish) {
					call_ep_finish = true;
					//-- waitingDialog.hide();
					if (callback_error) {
						callback_error(jqXHR, textStatus, errorThrown);
					} else {
						// TODO: Hacer menos dependiente de la variable RBKORDER y asi podremos usar este
						// helper en otros sitios
						alert(RBKORDER.GLOBAL.ERR_AJAX);
					}
				}
			}
		};

		if (args != null) {
			ajax_args["data"] = args;
		}

		setTimeout(function () {
			if (!call_ep_finish) {
				call_ep_finish = true;
				/*-- waitingDialog.hide(function () {
					alert(RBKORDER.GLOBAL.ERR_AJAX);
				}); */
				if (callback_error) {
					callback_error(null, "Timeout", "Timeout");
				} else {
					// TODO: Hacer menos dependiente de la variable RBKORDER y asi podremos usar este
					// helper en otros sitios
					alert(RBKORDER.GLOBAL.ERR_AJAX);
				}
			}
		}, 10000);
		
		//-- waitingDialog.show();
		$.ajax(ajax_args);
	}
}));
},{}],5:[function(require,module,exports){
( function( window, factory ) {
	// universal module definition
	/*jshint strict: false */ /* globals define, module, require */
	if ( typeof define == 'function' && define.amd ) {
		// AMD
		return factory( window );
	} else if ( typeof module == 'object' && module.exports ) {
		// CommonJS
		module.exports = factory(
			window
		);
	} else {
		// browser global
		window.RBKFixedWidgets = factory(
			window
		);
	}
}( window, function factory( window ) {
	// TODO: Por algun motivo no pilla bien la ruta de gmc-utils de node_modules... seguramente por la forma que tenemos de construir
	// los js
	var GMCUtils = require("../../../../../../../r2wp-themes-tasks/node_modules/gmc-utils/gmc-utils");
	
	return function() {
		var $body = $( 'body' );
		
		// var _window = $(window);

		var _cart_widget_area = $(".rbkor_shooping_cart_widget_wrap");
		
		/* order-nav-widget-area ya no existe (era un wrap de #rbkor_navigator_widget) 
		 * ya que siempre va como un sticky
		var _nav_widget_area = $(".order-nav-widget-area");
		var _nav_widget_area_enabled = _nav_widget_area.length > 0;
		*/
		
		var _allways_mobile = RBKORDER.ALLWAYS_MOBILE;

		var _top_offset_scrolled = RBKORDER.GLOBAL.TOP_OFFSET_SCROLLED;
		var _css_cart_body_fixed = "order-fixed-cart";
		var _max_width_cart_mobile = RBKORDER.GLOBAL.MAX_WIDTH_MOBILECART;
		// TODO: Se puede borrar por el paso a 2 cols
		// var _css_nav_body_fixed = "order-fixed-nav";
		// var _max_width_nav_mobile = RBKORDER.GLOBAL.MAX_WIDTH_MOBILENAV;

		var _menu = $('.btn-show');
		var _cart_show = $('#rbkor_shooping_cart_widget');

		if (!_cart_widget_area.length > 0) {
			return {"init": function() {}};
		}

		return {
			"init": function() {
				var observer = new IntersectionObserver(function(entries) {
					if(entries[0].intersectionRatio === 0) {
						// document.querySelector("#nav-container").classList.add("nav-container-sticky");
						jQuery("body").addClass("secondary-fixed");
					} else if(entries[0].intersectionRatio === 1) {
						// document.querySelector("#nav-container").classList.remove("nav-container-sticky");
						jQuery("body").removeClass("secondary-fixed");
					}
				}, { threshold: [0,1] });

				observer.observe(document.querySelector("#nav-container-top"));
				
				// Si fuerza siempre movil... hacemos que nunca este fixed
				if (_allways_mobile) {
					_unset_fixed_config(_cart_widget_area, _css_cart_body_fixed);
					_unset_fixed_order_detail();
					
					// TODO: Se puede borrar por el paso a 2 cols
					/* order-nav-widget-area ya no existe
					if (_nav_widget_area_enabled) {
						_unset_fixed_config(_nav_widget_area, _css_nav_body_fixed);
					}
					*/
				} else {
					_set_fixed();

					$(window).on("scroll", function() {
						if($("body").width() > _max_width_cart_mobile ) {
							_fixed_scrolled(_cart_widget_area);
							// TODO: Se puede borrar por el paso a 2 cols
							/* order-nav-widget-area ya no existe
							if (_nav_widget_area_enabled) {
								_fixed_scrolled(_nav_widget_area);
							}
							*/
						} else {
							_unfixed_scrolled(_cart_widget_area);
							// TODO: Se puede borrar por el paso a 2 cols
							/* order-nav-widget-area ya no existe
							if (_nav_widget_area_enabled) {
								_unfixed_scrolled(_nav_widget_area);
							}
							*/
						}
					});

					GMCUtils.ResizeWindow(function () {
						$('html, body').scrollTop();
						_cart_widget_area.removeData("fixed-width");
						// TODO: Se puede borrar por el paso a 2 cols
						/* order-nav-widget-area ya no existe
						_nav_widget_area.removeData("fixed-width"); 
						*/
						_set_fixed();
					});
				}
				
				$('.btn-show').off('click');
				$('.btn-show').on('click', function() {
					_set_fixed_order_detail();
					_switchClass($('.btn-show'),'active');
					_switchClass(_cart_show,'rbkor_shooping_cart_mobile_show');
					_switchClass($body,'rbkor_shooping_cart_mobile_toggled');
				});
				
				/*
				_menu.each(function () {
					var $menu_btn = $(this);
					$menu_btn.on('click', function() {
						_switchClass(_menu,'active');
						_switchClass(_cart_show,'rbkor_shooping_cart_mobile_show');
						_switchClass($body,'rbkor_shooping_cart_mobile_toggled');
					});
				});
				*/
			}
		};
		
		function _switchClass($myvar, $myclass) {
			if ($myvar.hasClass($myclass)) {
				$myvar.removeClass($myclass);
			} else {
				$myvar.addClass($myclass);
			}
		}
		
		function _set_fixed_config(widget_area, body_class) {
			$body.addClass(body_class);

			widget_area.attr( "style", "" );

			if(!widget_area.data( "offset-top")) {
				widget_area.data( "offset-top", widget_area.offset().top );
			}

			if(!widget_area.data( "fixed-width") ) {
				widget_area.data( "fixed-width", widget_area.outerWidth() );
			}

			widget_area.css({
				'width': widget_area.data( "fixed-width")
			});

			_fixed_scrolled(widget_area);
		}

		function _unset_fixed_config(widget_area, body_class) {
			$body.removeClass(body_class);
			widget_area.removeData("offset-top");
			widget_area.removeData("fixed-width");
			widget_area.attr( "style", "" );
			_unfixed_scrolled(widget_area);
		}

		function _set_fixed_order_detail() {
			var _height_1 = _cart_widget_area.find(".rbkor_order_header").outerHeight();
			var _height_2 = _cart_widget_area.find(".rbkor_order_footer").outerHeight();
			var _height_4 = RBKORDER.GLOBAL.TOP_OFFSET_SCROLLED ?
				RBKORDER.GLOBAL.TOP_OFFSET_SCROLLED * 2 : 0;

			var _max_height = $(window).height() - (_height_1 + _height_2 + _height_4);
			_max_height = _max_height > 100 ? _max_height : 100;
			_max_height += "px";

			$('.rbkor_order_detail').css({
				'max-height': _max_height
			});
		}

		function _unset_fixed_order_detail() {
			$('.rbkor_order_detail').removeAttr("style");
		}

		function _set_fixed() {
			if($("body").width() > _max_width_cart_mobile ) {
				_set_fixed_config(_cart_widget_area, _css_cart_body_fixed);
				_set_fixed_order_detail();
			} else {
				_unset_fixed_config(_cart_widget_area, _css_cart_body_fixed);
				_unset_fixed_order_detail();
			}
			
			/* order-nav-widget-area ya no existe
			if (_nav_widget_area_enabled) {
				if($(window).width() > _max_width_nav_mobile) {
					_set_fixed_config(_nav_widget_area, _css_nav_body_fixed);
				} else {
					_unset_fixed_config(_nav_widget_area, _css_nav_body_fixed);
				}
			}
			*/
		}

		function _fixed_scrolled(widget_area) {
			widget_area.css({
				'position': 'fixed'
			});

			if($(window).scrollTop() > widget_area.data( "offset-top") ) {
				widget_area.css({ "top": _top_offset_scrolled + "px" } );
			} else {
				widget_area.css({ "top": widget_area.data( "offset-top") - $(window).scrollTop() + "px" });
			}
		}

		function _unfixed_scrolled(widget_area) {
			widget_area.css({
				'position': 'relative',
				'width': '100%'
			});

			widget_area.css({ "top": "0px" } );
		}
	};
}));
},{"../../../../../../../r2wp-themes-tasks/node_modules/gmc-utils/gmc-utils":1}],6:[function(require,module,exports){
( function( window, factory ) {
	// universal module definition
	/*jshint strict: false */ /* globals define, module, require */
	if ( typeof define == 'function' && define.amd ) {
		// AMD
		return factory( window );
	} else if ( typeof module == 'object' && module.exports ) {
		// CommonJS
		module.exports = factory(
			window
		);
	} else {
		// browser global
		window.GTMUtils = factory(
			window
		);
	}
}( window, function factory( window ) {
	function GTMUtils() {
		this.push_order = function(event, order_type, order_api) {
			if (!window.dataLayer || !order_api || !order_api.orderLines) return;
			
			var items = new Array();
			
			for (var i = 0; i < order_api.orderLines.length; i++) {
				var ol = order_api.orderLines[i];
				
				$item = $("#catalog-item-" + ol.id);
				
				if ($item.length > 0) {
					items.push({
						'item_name': $item.data("name"), // Name or ID is required.
						'item_id': ol.id,
						'price': $item.data("price"),
						'quantity': ol.qty
					});
				}
			}
			
			if (items.length > 0) {
				// Measure when an event occur with all order
				window.dataLayer.push({
					'event': event
					,'ae_order_type': order_type
					,'ecommerce': {
						'items': items
					}
				});
			}
		};
		
		this.push_item = function(event, order_type, item_id, quantity) {
			if (!window.dataLayer) return;
			
			var $item = $("#catalog-item-" + item_id);
			// Measure when a product is added/view/removed to a shopping cart
			window.dataLayer.push({
				'event': event
				,'ae_order_type': order_type
				,'ecommerce': {
					'items': [{
						'item_name': $item.data("name"), // Name or ID is required.
						'item_id': item_id,
						'price': $item.data("price"),
						'quantity': quantity
					}]
				}
			});
		};
		
		this.catalog_first_view = function(order_type) {
			if (!window.dataLayer) return;
			
			window.dataLayer.push({
				'event': "ae_catalog_first_view"
				,'ae_order_type': order_type
			});
		}
	}
	
	return new GTMUtils();
}));
},{}],7:[function(require,module,exports){
( function( window, factory ) {
	// universal module definition
	/*jshint strict: false */ /* globals define, module, require */
	if ( typeof define == 'function' && define.amd ) {
		// AMD
		return factory( window );
	} else if ( typeof module == 'object' && module.exports ) {
		// CommonJS
		module.exports = factory(
			window
		);
	} else {
		// browser global
		window.modal_select_cot = factory(
			window
		);
	}
}( window, function factory( window ) {
	/**
	 * Crea un modal con las opciones de tipos de pedido disponibles para obligar a seleccionar alguna.
	 * 
	 * @param current_order_type, valor actual del tipo de pedido antes de llamar al modal
	 * @param $order_type_options_wrap, Contenedor de donde clonar las opciones disponibles de tipo de pedido 
	 * @param callback_on_hidden(_current_order_type), evento lanzado al ocultar el modal y que recibe como parametro
	 * 		el tipo de pedido seleccionado
	 */
	return function(current_order_type, $order_type_options_wrap, callback_on_hidden) {
		var _current_order_type = current_order_type;
		
		var $modal_select_cot = $('#rbkor_modal_order_type');
		var $modal_cot_body = $modal_select_cot.find('.modal-body');
		
		if ($modal_cot_body.find("input[name=rbkor_order_type]").length < 1) {
			var $order_type_options = $order_type_options_wrap.clone();
			
			// cambiamos los ids de los radio...
			$order_type_options.find("input").each(function () {
				var $input = $(this);
				$input.prop("id", $input.prop("id") + "-modal");
			});
			// ...y de los for de label antes de insertar los nodos clonados
			$order_type_options.find("label").each(function () {
				var $label = $(this);
				$label.prop("for", $label.prop("for") + "-modal");
			});
			
			$modal_cot_body.append($order_type_options);
			
			$modal_select_cot.on("hidden.bs.modal", function () {
				if (callback_on_hidden) {
					callback_on_hidden(_current_order_type);
				}
			});
			
			$modal_select_cot.find(".modal-footer .btn-primary").on("click", function () {
				_current_order_type = $modal_cot_body.find('input[name=rbkor_order_type]:checked').val();
				$modal_select_cot.modal("hide");
			});
		}
		
		$modal_cot_body.find('input:radio[name=rbkor_order_type]').removeAttr('checked').parent().removeClass("active");
		$modal_cot_body.find('input:radio[name=rbkor_order_type][value=' + current_order_type + ']').prop('checked', true).parent().addClass("active");
		
		$modal_select_cot.modal("show");
	};
}));

},{}],8:[function(require,module,exports){
( function( window, factory ) {
	// universal module definition
	/*jshint strict: false */ /* globals define, module, require */
	if ( typeof define == 'function' && define.amd ) {
		// AMD
		return factory( window );
	} else if ( typeof module == 'object' && module.exports ) {
		// CommonJS
		module.exports = factory(
			window
		);
	} else {
		// browser global
		window.RBKOrderLine = factory(
			window
		);
	}
}( window, function factory( window ) {
	require("./money.js");
	
	var Base64 = require("./base64.js");
	
	function __serialize_JSON(data) {
		return window.JSON && window.JSON.stringify ? window.JSON.stringify( data ) : (new Function("return " + data))();
	}
	
	function __unserialize_JSON(data) {
		return window.JSON && window.JSON.parse ? window.JSON.parse( data ) : (new Function("return " + data))();
	}
	
	return function() {
		this.aol = null;
		this.id = "";
		this.price = 0;
		this.currency = "EUR";
		this.qty = 0;
		this.name = "";
		this.description = "";
		this.delivery = false;
		this.takeaway = false;
		this.booking = false;
		this.options = [];
		this.offers = [];

		this.load_data = function (_aol) {
			this.aol = _aol;
			this.id = this.aol.id;
			this.options = this.aol.options && Array.isArray(this.aol.options) ? this.aol.options : [];
			this.qty = this.aol.qty;
			this.offers = this.aol.offers && Array.isArray(this.aol.offers) ? this.aol.offers : [];

			var $ci = $("#catalog-item-" + this.id);

			if ($ci.length == 1) {
				this.price = parseFloat($ci.data("price"));
				this.currency = $ci.data("currency");
				this.name = $ci.find(".catalog-item-header h5").html();
				this.description = $ci.find(".catalog-item-description").html();
				this.delivery = $ci.data("delivery") == 1;
				this.takeaway = $ci.data("takeaway") == 1;
				this.booking = $ci.data("booking") == 1;
				return true;
			}

			return false;
		}

		this.get_html = function() {
			var s = '<tr class="rbkor_oitem_line' +
				(this.delivery ? ' delivery' : '') +
				(this.takeaway ? ' takeaway' : '') +
				(this.booking ? ' booking' : '') +
				'">\n';

			s += '<td>\n';
			s += '<div class="rbkor_odesc"><span>' + this.name + '</span>';

			if (this.has_options() || this.has_offers()) {
				s += '<div class="rbkor_options"><ul>';
				
				if (this.has_options()) {
					var mh = new ModifierHelper(this.id);
					
					for (var i = 0; i < this.options.length; i++ ) {
						var o = this.options[i];
						var opt = mh.get_option(o.id);
						
						s += '<li><span class="opt">';
						s += '<span class="desc">' + opt.name + '</span>';
						
						if (opt.quantifiable) {
							s += '<span class="qty">' + o.qty + '</span>';
						}
						
						if (opt.price) {
							s += '<span class="price">' + rbk_format_money(opt.price, this.currency, true) + '</span>';
						}
						s += '</span></li>';
					}
				}

				if (this.has_offers()) {
					for (var i = 0; i < this.offers.length; i++ ) {
						s += '<li class="rbkor_offers">';
						s += '<span class="desc">' + this.offers[i].offerCode + '</span>';
						s += '</li>';
					}
				}

				s += '</ul></div>';
			}

			s += '</div>\n';
			s += '<div class="rbkor_ovalue">\n';

			var ser_opts = (this.has_options()) ? Base64.encode(__serialize_JSON(this.options)) : "";

			s += '<div class="rbkor_ocontrols">';
			s += '<input name="rbkor_id[]" value="' + __esc_html(this.id) + '" type="hidden" />\n' +
				 '<input name="rbkor_options[]" value="' + ser_opts + '" type="hidden" />\n' +
				 '<input name="rbkor_qty[]" value="' + __esc_html(this.qty) + '" type="hidden" />\n';

			s +='<a class="rbkor_btn rbkor_btn_del" href="javascript:void(0);" ' +
			'data-id="' + __esc_html(this.id) + '" ' +
			'data-options="' + ser_opts + '">' +
			RBKORDER.BUTTON_REMOVE +
			'</a>' +
				'<span class="rbkor_oqty">' + this.qty + '</span>' +
				'<a class="rbkor_btn rbkor_btn_add" href="javascript:void(0);" ' +
			'data-id="' + __esc_html(this.id) + '" ' +
			'data-options="' + ser_opts + '">' +
			RBKORDER.BUTTON_ADD  +
			'</a>\n';
			
			s += '</div>\n';
			s += '<span class="rbkor_oprice">' + rbk_format_money(this.calcTotal(), this.currency, true) + '</span>\n';
			s += '</td>\n';
			s += '</tr>\n';

			return s;

			function __esc_html(text) {
				return $("<div>").text(text).html();
			}

			function _get_modifiers(item_id) {
				var meta_json = __unserialize_JSON($("#mod-item-meta-" + item_id).text());

				return meta_json.modifiers;
			}
			
			function ModifierHelper(item_id) {
				this.item_id = item_id;
				this.modifiers = _get_modifiers(this.item_id);

				this.get_modifier_by_opt = function (opt_id) {
					for (var i = 0; i < this.modifiers.length; i++) {
						for (var j = 0; i < this.modifiers[i].options; j++) {
							var opt = this.modifiers[i].options[j];
							if (opt.url == opt_id) {
								return this.modifiers[i];
							}
						}
					}

					return false;
				};

				this.get_option = function (opt_id) {
					for (var i = 0; i < this.modifiers.length; i++) {
						for (var j = 0; j < this.modifiers[i].options.length; j++) {
							var opt = this.modifiers[i].options[j];
							if (opt.url == opt_id) {
								return opt;
							}
						}
					}

					return false;
				};

				function _get_modifiers(_item_id) {
					var meta_json = __unserialize_JSON($("#mod-item-meta-" + _item_id).text());

					return meta_json.modifiers;
				}
			}
		};

		this.has_options = function () {
			return (this.options.length > 0);
		};

		this.has_offers = function () {
			return (this.offers.length > 0);
		};

		this.addQty = function() {
			this.qty += 1;
		};

		this.delQty = function() {
			this.qty -= 1;
		};

		this.calcTotalWithDiscount = function() {
			return this.aol.totalLine - this.aol.discount;
		}

		this.calcTotal = function() {
			return this.aol.totalLine;
		};
	};
}));
},{"./base64.js":10,"./money.js":13}],9:[function(require,module,exports){
( function( window, factory ) {
	// universal module definition
	/*jshint strict: false */ /* globals define, module, require */
	if ( typeof define == 'function' && define.amd ) {
		// AMD
		return factory( window );
	} else if ( typeof module == 'object' && module.exports ) {
		// CommonJS
		module.exports = factory(
			window
		);
	} else {
		// browser global
		window.validation_cookie_ajax = factory(
			window
		);
	}
}( window, function factory( window ) {
	var call_ajax_ep = require("./_call-ajax-ep.js");
	
	/**
	 * Comprueba que puede grabar y acceder a cookies via ajax
	 * Para ello, hace PUT a una url y luego intenta recuperar el valor haciendo un GET
	 * 
	 * NOTA: El motivo por el cual hace 2 PUT en vez de uno es que en el pasado, al 
	 * intentar hacer la primera llamada, en algun navegador, no funcionaba, pero curiosamente
	 * al hacer la segunda ya si que funcionaba...
	 * 
	 * @param _options: {
	 *      callback_success: cuando correctamente puede grabar y acceder a cookies
	 *      callback_error: cuando no ha completado alguno 
	 * }
	 * @returns
	 */
	return function (_options) {
		var self = this;
		
		self.options = $.extend({
			callback_success: false,
			callback_error: false
		}, _options );
		
		// Hay que poner el put dos veces o casca
		call_ajax_ep(_get_ping_url("put"), {}, function (responsePut1) {
			call_ajax_ep(_get_ping_url("put"), {}, function (responsePut2) {
				// TODO: Lo suyo aqui seria comprobar que el resultado da OK y si no sacar el error... 
				// pero npi de porque se hizo asi
				// console.log(responsePut2);
				call_ajax_ep(_get_ping_url("get"), {}, function (responseGet) {
					if (responseGet.value != "OK") {
						if (self.options.callback_error) {
							self.options.callback_error();
						} else {
							console.log("ERROR: No se puede acceder a cookies a traves de ajax.");
						}
					} else {
						self.options.callback_success();
					}
				});
			});
		});
		
		function _get_ping_url(ping_type) {
			return RBKORDER.GLOBAL.EP_ORDER_PING.replace("{type}", ping_type);
		}
	}
}));
},{"./_call-ajax-ep.js":4}],10:[function(require,module,exports){

/**
* Funciones comunes que se usan en varios sitios
*/

( function( window, factory ) {
  // universal module definition
  /*jshint strict: false */ /* globals define, module, require */
  if ( typeof define == 'function' && define.amd ) {
    // AMD
      return factory( window );
  } else if ( typeof module == 'object' && module.exports ) {
    // CommonJS
    module.exports = factory(
      window
    );
  } else {
    // browser global
    window.Base64 = factory(
      window
    );
  }
}( window, function factory( window ) {
var Base64 = {
	// private property
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

	// public method for encoding
	encode : function (input) {
		var output = "";
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		var i = 0;

		input = Base64._utf8_encode(input);

		while (i < input.length) {

			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);

			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;

			if (isNaN(chr2)) {
				enc3 = enc4 = 64;
			} else if (isNaN(chr3)) {
				enc4 = 64;
			}

			output = output +
			this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
			this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

		}

		return output;
	},

	// public method for decoding
	decode : function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;

		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

		while (i < input.length) {

			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));

			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;

			output = output + String.fromCharCode(chr1);

			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3);
			}

		}

		output = Base64._utf8_decode(output);

		return output;

	},

	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";

		for (var n = 0; n < string.length; n++) {

			var c = string.charCodeAt(n);

			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}

		}

		return utftext;
	},

	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;

		while ( i < utftext.length ) {

			c = utftext.charCodeAt(i);

			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}

		}

		return string;
	}

};
return Base64;
}));

},{}],11:[function(require,module,exports){
/**
 * Module for displaying "Waiting for..." dialog using Bootstrap
 *
 * @author Eugene Maslovich <ehpc@em42.ru>
 */

window.errorDialog = (function ($) {
    'use strict';

	// Creating modal dialog's DOM
    /* 

*/
	var $dialog = $(
		'<div class="modal fade" id="errorModalDialog" tabindex="-1" role="dialog" aria-labelledby="errorModalDialogTitle" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="display: none;">' +
		'<div class="modal-dialog error-dialog" role="document">' +
			'<div class="modal-content">' +
				'<div class="modal-header">' +
					'<h5 class="modal-title" id="errorModalDialogTitle">ERROR</h5>' +
				'</div>' +
				'<div class="modal-body">ERROR</div>' +
				'<div class="modal-footer">' +
					'<button type="button" class="btn btn-primary">OK</button>' +
				'</div>' +
			'</div>' +
		'</div>' +
		'</div>');

	return {
		show: function (options) {
			
			if (typeof options === 'undefined') {
				options = {};
			}

			var settings = $.extend({
				title: 'ERROR',
				message: 'ERROR',
				label_btn: 'OK',
				dialogSize: 'm',
				onClick: function () {errorDialog.hide();},
				onHide: null // This callback runs after the dialog was hidden
			}, options);

			// Configuring dialog
			$dialog.find('.modal-dialog').attr('class', 'modal-dialog').addClass('modal-' + settings.dialogSize);
			$dialog.find('.modal-title').text(settings.title);
			$dialog.find('.modal-body').html(settings.message);
			$dialog.find('.btn-primary').text(settings.label_btn);
			
			// Adding callbacks
			if (typeof settings.onHide === 'function') {
				$dialog.off('hidden.bs.modal').on('hidden.bs.modal', function (e) {
					settings.onHide.call($dialog);
				});
			}
			
			$dialog.find(".btn-primary").one("click", function () {
				settings.onClick();
			});

			// Opening dialog
			$dialog.modal('show');
		},
		/**
		 * Closes dialog
		 */
		hide: function (callback) {
			$dialog.modal('hide');
		}
	};

})(jQuery);

},{}],12:[function(require,module,exports){

/**
* Funciones comunes que se usan en varios sitios
*/

( function( window, factory ) {
	// universal module definition
	/*jshint strict: false */ /* globals define, module, require */
	if ( typeof define == 'function' && define.amd ) {
		// AMD
		return factory( window );
	} else if ( typeof module == 'object' && module.exports ) {
		// CommonJS
		module.exports = factory(
			window
		);
	} else {
		// browser global
		window.RBKModalOrder = factory(
			window
		);
	}
}( window, function factory( window ) {
require("./money.js");

var $ = jQuery;
function RBKModalOrder() {
	var $modal = null;
	var item_id = null;
	var MIN_QTY_DEFAULT = 1;
	var MAX_QTY_DEFAULT = 100;
	var global_min_qty = MIN_QTY_DEFAULT;
	var global_max_qty = MAX_QTY_DEFAULT;
	var modifiers;
	var money_config;
	var disabled_qty_global = false;

	this.show = function($btn, _money_config, _on_add_btn) {
		if ($modal == null) {
			$modal = $('#rbkor_modal_item_modifiers');

			$modal.on("hide.bs.modal", function (event) {
				$modal.find('.modal-body').html("");
			});

			$modal.find(".btn-primary").one("click", function () {
				_on_click();
			});
		}
		
		var order_line_min_qty = $btn.data("order-line-min-qty");
		var order_line_max_qty = $btn.data("order-line-max-qty");
		
		if (order_line_min_qty) {
			global_min_qty = order_line_min_qty;
		}
		
		if (order_line_max_qty) {
			global_max_qty = order_line_max_qty;
		}

		money_config = _money_config;
		item_id = $btn.data("id");
		item_id = $btn.data("id");
		modifiers = _get_modifiers(item_id);
		var $item = $("#catalog-item-" + item_id);

		var $modal_body = $modal.find('.modal-body');
		var $modal_form = $('<form class="brko_modal_form" id="rbko_modal_form" />');

		var $image_wrap = $item.find(".featured-image");
		var $description_wrap = $item.find(".catalog-item-description");
		
		if ($image_wrap.length > 0 || $description_wrap.length > 0) {
			var $header_item = $('<div class="header-item-modal" />');
			
			if ($image_wrap.length > 0) {
				$header_item.addClass("with-image").append(
					$('<div class="featured-image" />').html(
						$image_wrap.html()
					)
				);
			}

			if ($description_wrap.length > 0) {
				$header_item.addClass("with-description").append(
					$('<div class="catalog-item-description" />').html(
						$description_wrap.html()
					)
				);
			}
			
			$modal_body.append($header_item);
		}

		_set_title(
			$item.find(".catalog-item-header h5").html() 
		);

		_add_modifier_nodes($modal_form, modifiers);
		
		var $allergens_wrap = $item.find(".catalog-allergens-list");
		
		if ($allergens_wrap.length > 0) {
			var $wrap_allergen = $('<div class="wrap-allergen wrap-modal" />');
			$wrap_allergen.append($('<h6 />').text(RBKORDER.GLOBAL.TITLE_ALLERGEN));
			var $wrap_allergen_list = $('<div class="catalog-allergens-list" />');
			$wrap_allergen_list.html($allergens_wrap.html());
			
			$wrap_allergen_list.find('[data-toggle="popover"]').popover({html: true});
			
			$wrap_allergen.append($wrap_allergen_list);
			
			$modal_form.append($wrap_allergen);
		}
		
		if (!disabled_qty_global) {
			$modal_form.append(_get_global_qty_node($btn));
		}

		$modal_body.append($modal_form);

		_recalculate_total();

		$modal.modal("show");

		$modal_body.find(".wrap-qty-bundle").each(function () {
			var $this = $(this);
			
			var is_option = $this.hasClass("opt-qty-wrap");

			// Cantidades de opciones
			var min_qty = !is_option || isNaN($this.data("min-qty")) ? null : parseInt($this.data("min-qty"), 10);
			var max_qty = !is_option || isNaN($this.data("max-qty")) ? null : parseInt($this.data("max-qty"), 10);

			if (min_qty == null) {
				min_qty = MIN_QTY_DEFAULT;
			}

			if (max_qty == null) {
				max_qty = MAX_QTY_DEFAULT;
			}

			var $field = $this.find(".wrap-qty-field input");
			var $label_field = $this.find(".wrap-qty-field .wrap-qty-label");
			
			$this.find(".wrap-qty-btn-del a").click(function (e) {
				e.preventDefault();
				if (!isNaN($field.val())) {
					var cur_qty = parseInt($field.val(), 10);
					
					if ((is_option && cur_qty > min_qty) || (!is_option && cur_qty > global_min_qty)) {
						$field.val(cur_qty - 1);
						$label_field.text($field.val());
						
						_recalculate_total();
					}
				}
			});

			$this.find(".wrap-qty-btn-add a").click(function (e) {
				e.preventDefault();
				if (!isNaN($field.val())) {
					var cur_qty = parseInt($field.val(), 10);
					
					if ((is_option && cur_qty < max_qty) || (!is_option && cur_qty < global_max_qty)) {
						var cur_input_qty_id = $field.attr("id");
						var cur_check_input_id = cur_input_qty_id.replace(/_qty$/, "");
						
						if (!$("#" + cur_check_input_id).is(":checked")) {
							$("#" + cur_check_input_id).prop("checked", true);
						}

						$field.val(cur_qty + 1);
						$label_field.text($field.val());
						
						_recalculate_total();
					}
				}
			});
		});

		$modal_body.find("input.form-check-input").change(function (el) {
			_recalculate_total();
		});
		
		function _recalculate_total() {
			var $add_button = $modal.find(".btn-primary");
			var $form = $modal.find('form#rbko_modal_form');
			var total = parseFloat($("#catalog-item-" + item_id).data("price"));
			
			var opts = [];
			for (var i = 0; i < modifiers.length; i++) {
				var m = modifiers[i];
				for (var j = 0; j < m.options.length; j++) {
					var o = m.options[j];
					if (o.price != undefined && parseFloat(o.price) != 0) {
						var field_id = _get_option_field_id(m, o);

						if ($form.find('#' + field_id).is(':checked')) {
							if (o.quantifiable) {
								var qty_field_id = _get_option_qty_field_id(m, o);
								var qty = parseInt($form.find('#' + qty_field_id).val(), 10);
								total += (parseFloat(o.price) * qty);
							} else {
								total += parseFloat(o.price);
							}
						}
					}
				}
			}

			total = total * $form.find("#txt-global-qty").val();

			$add_button.html(RBKORDER.GLOBAL.LABEL_TOTAL_BTN.replace(
				'{0}',
				rbk_format_money( total, money_config )
			));
			
			// disabled/enabled qty buttons:
			_disabled_qty_buttons();
		}
		
		function _disabled_qty_buttons() {
			$modal_body.find(".wrap-qty-bundle").each(function () {
				var $this = $(this);
				
				var is_option = $this.hasClass("opt-qty-wrap");
				
				var min_qty = !is_option || isNaN($this.data("min-qty")) ? null : parseInt($this.data("min-qty"), 10);
				var max_qty = !is_option || isNaN($this.data("max-qty")) ? null : parseInt($this.data("max-qty"), 10);

				if (min_qty == null) {
					if (!is_option) {
						min_qty = global_min_qty;
					} else {
						min_qty = MIN_QTY_DEFAULT;
					}
				}

				if (max_qty == null) {
					if (!is_option) {
						max_qty = global_max_qty;
					} else {
						max_qty = MAX_QTY_DEFAULT;
					}
				}

				var $qty_input = $this.find(".wrap-qty-field input");
			
				if (!isNaN($qty_input.val())) {
					var cur_qty = parseInt($qty_input.val(), 10);
					
					var $add_btn = $this.find(".wrap-qty-btn-add a");
					var $del_btn = $this.find(".wrap-qty-btn-del a");
					
					if (cur_qty > min_qty) {
						$del_btn.prop('disabled', false);
						$del_btn.removeClass("ae-disabled");
					} else if (!$add_btn.hasClass("ae-disabled")) {
						$del_btn.prop('disabled', true);
						$del_btn.addClass("ae-disabled");
					}
					
					if (cur_qty < max_qty) {
						$add_btn.prop('disabled', false);
						$add_btn.removeClass("ae-disabled");
					} else if (!$add_btn.hasClass("ae-disabled")) {
						$add_btn.prop('disabled', true);
						$add_btn.addClass("ae-disabled");
					}
				}
			});
		}

		function _on_click() {
			$modal.find(".btn-primary").one("click", function () {
				_on_click();
			});

			var opts = [];

			var $form = $modal.find('form#rbko_modal_form');

			for (var i = 0; i < modifiers.length; i++) {
				var m = modifiers[i];

				if (m.mandatory) {
					var selected = false;
					for (var j = 0; j < m.options.length; j++) {
						var o = m.options[j];
						var field_id = _get_option_field_id(m, o);
						if ($form.find('#' + field_id).is(':checked')) {
							selected = true;
							break;
						}
					}

					if (!selected) {
						if ($form.find(".alert-danger").length == 0) {
							$form.append($('<div class="atert alert-danger" style="padding: 1rem;"></div>'));
						}
						$form.find(".alert-danger").html(RBKORDER.GLOBAL.ERR_REQUIRED_MOD.replace(/\{0\}/, m.name));
						return;
					}
				}
				for (var j = 0; j < m.options.length; j++) {
					var o = m.options[j];
					var field_id = _get_option_field_id(m, o);
					if ($form.find('#' + field_id).is(':checked')) {
						var field_id_qty = _get_option_qty_field_id(m, o);
						var $field_qty = $form.find('#' + field_id_qty);
						var qty = o.minQuantity != undefined ? o.minQuantity : 1;
						if ($field_qty.length > 0) {
							qty = $field_qty.val();
						}

						opts[opts.length] = {id: o.url, qty: parseInt(qty, 10)}
					}
				}
			}

			var qty_item = disabled_qty_global ? 1 : $form.find('#txt-global-qty').val();
			qty_item = isNaN(qty_item) ? 1 : qty_item;

			_on_add_btn(item_id, opts, qty_item);

			$modal.modal("hide");
		}
	}

	this.hide = function() {
		if ($modal) {
			$modal.modal("hide");
		}
	}

	function _get_modifiers(item_id) {
		if ($("#mod-item-meta-" + item_id).length == 0) {
			return [];
		}
		
		var meta_json = _parse_JSON($("#mod-item-meta-" + item_id).text());
		return meta_json.modifiers;

		function _parse_JSON(data) {
			return window.JSON && window.JSON.parse ? window.JSON.parse( data ) : (new Function("return " + data))();
		}
	}

	function _set_title(title) {
		var $title = $modal.find("h5.modal-title");
		$title.html(title);
	}

	function _add_modifier_nodes($node, mm) {
		for (var i = 0; i < mm.length; i++) {
			$node.append(_get_modifier_node(mm[i], mm));
		}
	}

	function _get_global_qty_node($btn) {
		var $wrap = $("<div/>").attr("id", "wrap-global-qty");
		$wrap.addClass('wrap-modal');
		$wrap.append($("<h6/>").html(RBKORDER.GLOBAL.LABEL_QTY_GLOBAL));
		var $fieldset = $('<fieldset class="form-group" />').addClass("global-qty");
		var $form_row = $('<div class="global-qty-row" />').attr("id", "wrap-global-qty-row");
		
		var order_line_min_qty = $btn.data("order-line-min-qty");
		var order_line_max_qty = $btn.data("order-line-max-qty");
		
		if (order_line_min_qty) {
			global_min_qty = order_line_min_qty;
		} else {
			global_min_qty = MIN_QTY_DEFAULT;
		}
		
		if (order_line_max_qty) {
			global_max_qty = order_line_max_qty;
		} else {
			global_max_qty = MAX_QTY_DEFAULT;
		}
		
		
		$form_row.append(_get_dom_field_qty("txt-global-qty", "txt-global-qty", global_min_qty, global_max_qty, "global-qty-wrap"));
		$fieldset.append($form_row);
		$wrap.append($fieldset);
		return $wrap;
	}

	function _get_modifier_node(m, all_modes) {
		var some_selected_by_default = true;
		var optional_price = true;
		var item_base_price = parseFloat($("#catalog-item-" + item_id).data("price"));

		if (all_modes.length == 1 && m.multiSelect == false && some_selected_by_default) {
			optional_price = false;
		}
		
		var $wrap = $("<div/>").attr("id", "wrap-mod-" + m.url);
		$wrap.addClass('wrap-modal');

		$wrap.append($("<h6/>").html(m.name).addClass(m.mandatory ? "required" : "not-required"));
		if (m.descrption) {
			$wrap.append($("<p/>").html(m.description));
		}
		var $fieldset = $('<fieldset class="form-group" />').addClass("options");

		for (var i = 0; i < m.options.length; i++) {
			var opt = m.options[i];
			var $wrap_qty = null;
			var $form_row = $('<div class="addon-row" />').attr("id", "wrap-opt-" + opt.url);
			var field_name = _get_option_field_name(m, opt);
			var field_id = _get_option_field_id(m, opt);

			if (opt.quantifiable) {
				$wrap_qty = $('<span class="quantity" />');
				var field_id_qty = _get_option_qty_field_id(m, opt);
				var field_name_qty = _get_option_qty_field_name(m, opt);

				$wrap_qty.append(_get_dom_field_qty(field_id_qty, field_name_qty, opt.minQuantity, opt.maxQuantity, "opt-qty-wrap"));

				if (opt.maxQuantity == opt.minQuantity) {
					$wrap_qty.append($('<span class="qty-value" />').text(opt.minQuantity));
				}
			}

			var $wrap_field_chk = $('<div class="addon-desc" />').append(
				$('<div class="form-check" />').append(
					$('<label class="form-check-label"/>').attr("for", field_id).append(
						_get_dom_field_chk(
							m.multiSelect, field_id, field_name,
							opt.url, opt.selectedByDefault
						)
					).append(opt.name)
				)

			);

			$form_row.append($wrap_field_chk);
			var $price_and_qty = $('<div class="addon-price" />');

			if ($wrap_qty != null) {
				$price_and_qty.append($wrap_qty);
			}

			if (optional_price && !isNaN(opt.price) && opt.price != 0) {
				$price_and_qty.append(
					(opt.price > 0 ? "+" : "") +
					rbk_format_money(opt.price, money_config) +
					(opt.quantifiable ? "/u." : "")
				);
			} else {
				var total_option_price = item_base_price + opt.price;

				$price_and_qty.append(
					rbk_format_money(total_option_price, money_config)
				);
			}

			$form_row.append($price_and_qty);

			$fieldset.append($form_row);
		}
		$wrap.append($fieldset);
		return $wrap;
	}

	function _get_option_qty_field_name(modifier, option) {
		return _get_option_qty_field_id(modifier, option);
	}

	function _get_option_qty_field_id(modifier, option) {
		return _get_option_field_id(modifier, option) + "_qty";
	}

	function _get_option_field_id(modifier, option) {
		return "rbko_opt_field_" + modifier.url + "_" + option.url;
	}

	function _get_option_field_name(modifier, option) {
		if (modifier.multiSelect) {
			return _get_option_field_id(modifier, option);
		}

		return "rbko_opt_field_" + modifier.url;
	}

	function _add_field_attr_base($input, field_id, field_name, field_value) {
		$input.attr("id", field_id).
			attr("name", field_name).
			attr("value", field_value);
	}

	function _get_dom_base_input(type) {
		return $('<input type="' + type + '"/>');
	}

	function _get_dom_field_chk(multi_select, field_id, field_name, field_value, checked) {
		var $input = _get_dom_base_input(multi_select ? 'checkbox' : 'radio');
		$input.addClass('form-check-input');

		_add_field_attr_base($input, field_id, field_name, field_value);

		if (checked) {
			$input.prop("checked", true);
		}

		return $input;
	}

	function _get_dom_field_qty(field_id, field_name, min_qty, max_qty, extra_class) {
		var $wrap = $("<span/>").attr({
			"class": "wrap-qty-bundle " + extra_class,
			"data-target-id": field_id,
			"data-min-qty": min_qty,
			"data-max-qty": max_qty
		});

		$wrap.append($("<span/>").attr("class", "wrap-qty-btn-del").append(
			$("<a class=\"rbkor_btn rbkor_btn_del\" href=\"javascript:void(0);\"/>").append(
				$(RBKORDER.BUTTON_REMOVE)
			)
		));

		var $input = _get_dom_base_input('hidden');

		_add_field_attr_base($input, field_id, field_name, min_qty);

		if (min_qty != max_qty) {
			$input.attr("class", "form-control form-control-sm").
				attr("min", min_qty).
				attr("max", max_qty);
		}

		$wrap.append(
			$("<span/>").attr("class", "wrap-qty-field").append(
				$input
			).append(
				$("<span/>").attr("class", "wrap-qty-label").text(min_qty)
			)
		);

		$wrap.append($("<span/>").attr("class", "wrap-qty-btn-add").append(
			$("<a class=\"rbkor_btn rbkor_btn_add\" href=\"javascript:void(0);\"/>").append(
				$(RBKORDER.BUTTON_ADD)
			)
		));

		return $wrap;
	}
}

return new RBKModalOrder();
}));

},{"./money.js":13}],13:[function(require,module,exports){
Number.prototype.formatMoney = function(c, d, t){
	var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

function rbk_formated_money2float(formated, decimal_symbol) {
	var parts = formated.split(" ");
	
	if (parts.length && parts[0]) {
		var new_float = parts[0];
		
		if (decimal_symbol == ",") {
			new_float = new_float.replace(".", "");
			new_float = new_float.replace(",", ".");
		} else {
			new_float = new_float.replace(",", "");			
		}
		
		return parseFloat(new_float);
	}
	
	return null;
}

function rbk_format_money(value, config, escape_html) {
	if ( (typeof config) == "string" ) {
		var currency_code = config;
		config = {
			decimal_places: 2,
			decimal_symbol: ',',
			thousand_symbol: '.',
			currency_symbol: '$'
		};

		switch (currency_code) {
			case "EUR":
				config.currency_symbol = escape_html ? "&euro;" : "€";
			break;
			case "PHP":
				config.currency_symbol = escape_html ? "P" : "₱";
			break;
			case "GBP":
				config.currency_symbol = escape_html ? "&pound;" : "£";
			break;
			case "COP":
				// Aunque tiene centimos no se suele usar
				currency_config.decimal_places = 0;
			break;
		}
	}
	
	if (!isNaN(value)) {
		return parseFloat(value).formatMoney(config.decimal_places, config.decimal_symbol, config.thousand_symbol) + " " + config.currency_symbol;
	}

	return "";
}

window.rbk_formated_money2float = rbk_formated_money2float;
window.rbk_format_money = rbk_format_money;
},{}],14:[function(require,module,exports){
$("body").scrollspy({
	target: "#order-navbar",
	offset: $("#order-navbar").parent().height()
});

$('#order-navbar').mousedown(function (event) {
	$(this)
		.data('down', true)
		.data('x', event.clientX)
		.data('scrollLeft', this.scrollLeft)
		.addClass("dragging");

	return false;
}).mouseup(function (event) {
	$(this)
		.data('down', false)
		.removeClass("dragging");
}).mousemove(function (event) {
	if ($(this).data('down') == true) {
		this.scrollLeft = $(this).data('scrollLeft') + $(this).data('x') - event.clientX;
	}
})/* TODO: Esto no funciona...:
.mousewheel(function (event, delta) {
	this.scrollLeft -= (delta * 30);
})*/ .css({
	'cursor' : '-moz-grab'
});

$('#order-navbar').hScroll(); // You can pass (optionally) scrolling amount

// ---- ANCHOR LINK ANIMATION WITH OFFSET ----
$("#order-navbar a[href^='#']").off('click').on('click', function(event) {
	var target = this.hash;
	
	event.preventDefault();
	
	// Si ponemos el parent() hace cosas raras :
	// navOffset = $('#order-navbar').parent().height();
	var navOffset = $('#order-navbar').parent().height();
	
	// Este +1 es un poco tricky... el tema es que la animacion no estaba funcionando bien y seleccionaba
	// el item anterior en vez de al que te estabas moviendo. Aunque no he investigado, en el caso
	// en el que probe la altura del offset ($('#order-navbar').parent().height()) daba un valor con decimales...
	// Lo mismo es por esto por lo que calculaba mal el offset y tenemos que aniadir un pixel
	return $('html, body').animate({
		scrollTop: $(this.hash).offset().top - navOffset + 1
	}, 300, function() {
		return window.history.pushState(null, null, target);
	});
});

// OJO: Hay un comportamiento raro en scrollSpy por el cual aunque hagas 
// referencia a body, para que lance estos eventos, tienes que llamarlo en
// window: https://stackoverflow.com/questions/48693913/bootstrap-4-activate-bs-scrollspy-event-is-not-firing
$(window).off('activate.bs.scrollspy').on('activate.bs.scrollspy', function () {
	centerLI($(".nav-link.active").parent(), '#order-navbar');
});

/* TODO: Esto tiene pinta que no es necesario
// Dropdown Close on Body click
$(document).mouseup(function(e) {
	var container = $("#order-navbar");
	
	// if the target of the click isn't the container nor a descendant of the container
	if (!container.is(e.target) && container.has(e.target).length === 0) {
		$('.nav-link').removeClass('visible');
		$('.submenu').removeClass('visible');
	}
});
*/

// Rutina para centrar target en el display de outer. Se usa en la version mobile que solo
// tiene una linea con overflow hidden
// http://stackoverflow.com/a/33296765/350421
function centerLI(target, outer) {
	var out = $(outer);
	var tar = $(target);
	var x = out.width() - 50; //TODO: variable igual que scrollspy
	var y = tar.outerWidth(true);
	var z = tar.index();
	var q = 0;
	var m = out.find('li');
	
	for (var i = 0; i < z; i++) {
		q += $(m[i]).outerWidth(true);
	}
	
	//out.scrollLeft(Math.max(0, q - (x - y)/2));
	out.animate({
		scrollLeft: Math.max(0, q - (x - y) / 2)
	}, 100);
}
},{}],15:[function(require,module,exports){

/**
* Funciones comunes que se usan en varios sitios
*/

( function( window, factory ) {
  // universal module definition
  /*jshint strict: false */ /* globals define, module, require */
  if ( typeof define == 'function' && define.amd ) {
    // AMD
      return factory( window );
  } else if ( typeof module == 'object' && module.exports ) {
    // CommonJS
    module.exports = factory(
      window
    );
  } else {
    // browser global
    window.RBKOffers = factory(
      window
    );
  }
}( window, function factory( window ) {

var $ = jQuery;

function RBKOffers() {
	var $modal_error = null;
	this.search = _search_offer;

    this.init = function(options) {
    	/*
    	 * options {
    	 * 		"action": "add|del",
    	 * 		"btn_selector": "",
    	 * 		"query_selector": "",
    	 * 		"callback": function,
    	 * }
    	 * 
    	 * */
        $(options["btn_selector"]).click(function (e) {
			e.preventDefault();
            _search_offer(options);
		});

        if ($modal_error == null) {
            $modal_error = $('#rbkor_modal_error');

            $modal_error.on("hide.bs.modal", function (event) {
        		$modal_error.find('.modal-body').html("");
        	});
        }
	}

    function _search_offer(options) {
        var $modal_detail = options["action"] == "add" ? 
    		$('#rbkor_modal_add_offer') : 
    		$('#rbkor_modal_del_offer');

		var $query = options["query_selector"] ? $(options["query_selector"]) : $(options["btn_selector"]);
		var query = options["offer_code"] ? options["offer_code"] : ($query.data("offer-code") ? $query.data("offer-code") : $query.val()); 
        var callback = options["callback"];
    	
        if (query) {
            $modal_detail.off("hide.bs.modal").on("hide.bs.modal", function (event) {
        		$modal_detail.find('.modal-body').html("");
        	});
            var get_data = {
                'oc': query
            };

            $.ajax({
                url: RBKORDER.GLOBAL.EP_OFFER_GET,
                jsonp: "callback",
                dataType: 'jsonp',
                crossDomain: true,
                type: 'GET',
                data: get_data
            }).done(function (data) {
                if (!data || data.code == 404) {
                    $modal_error.find('.modal-body').html('<div class="alert alert-danger" role="alert">' + RBKORDER.GLOBAL.ERR_OFFER_NOT_FOUND + '</div>');
                    $modal_error.modal("show");
                } else {
                	$modal_detail.find(".modal-title").text(RBKORDER.GLOBAL.OFFER_MODAL_TITLE + ' ' + data.offerCode);
                    
                    var $wrap = $("<div/>").attr("class", "wrap-mod-" + data.offerCode).data("code", data.offerCode);
                    $wrap.addClass('wrap-modal');
                    

                    $wrap.append($("<h4/>").text(data.title ? data.title : data.offerCode));
                    
                    if (data.description) {
                        $wrap.append($('<div class="offer-description"/>').html(data.description));
                    }
                    
                    if (data.descriptionCondition) {
                    	$wrap.append($("<h6/>").text(RBKORDER.GLOBAL.OFFER_MODAL_TITLE_CONDITIONS));	
                        $wrap.append($('<div class="offer-description offer-conditions"/>').html(data.descriptionCondition));
                    }
                    
                    $modal_detail.find('.modal-body').append($wrap);
                    $modal_detail.modal("show");
                    
                    $modal_detail.find(".btn-primary").off("click").click(function (e) {
            			e.preventDefault();
            			callback(data, function () {
                            $modal_detail.modal("hide");
                        });
            		});
                }
            });
        }
    }

}

return new RBKOffers();
}));


},{}],16:[function(require,module,exports){

var Base64 = require("./base64.js");
require("../jquery.cookie.js");
require("../listae/waiting-dialog.js");
require("./error-dialog.js");
require("./money.js");

var RBKModalOrder = require("./modal-order.js");
var RBKModalPlacesSearch = RBKORDER.GLOBAL.PLACE_SEARCH ? require("./places-search.js") : RBKORDER.GLOBAL.PLACE_SEARCH;
var RBKOffers = require("./offer.js");

var GTMUtils = require("./_gtm-utils.js");
var modal_select_cot = require("./_modal-select-cot.js");
var call_ajax_ep = require("./_call-ajax-ep.js");
var validation_cookie_ajax = require("./_validation-cookie-ajax.js");

var RBKFixedWidgets = require("./_fixed-widget-bar.js");
var RBKOrderLine = require("./_order-line.js");

// TODO: money_config deberiamos de eliminarlo... en vez de eso, cada vez que usemos un tipo moneda, deberiamos llamar a la funcion 
// rbk_format_money con el valor que queremos formatear y el codigo ISO de la moneda actual...
var _money_config = {
	decimal_places: RBKORDER.GLOBAL.DECIMAL_PLACES,
	decimal_symbol: RBKORDER.GLOBAL.DECIMAL_SYMBOL,
	thousand_symbol: RBKORDER.GLOBAL.THOUSAND_SYMBOL,
	currency_symbol: RBKORDER.GLOBAL.CURRENCY_SYMBOL
};

/**
 * Plugin de jquery para hacer el horizontal scroll (ahora mismo se esta usando en nav-group-catalog-scroll.js)
 * No lo metemos en nav-group-catalog-scroll.js, ya que se tiene que inicializar antes
 * @param $
 */
jQuery(function ($) {
	$.fn.hScroll = function (amount) {
		amount = amount || 120;
		
		$(this).bind("DOMMouseScroll mousewheel", function (event) {
			var oEvent = event.originalEvent, 
				direction = oEvent.detail ? oEvent.detail * -amount : oEvent.wheelDelta, 
				position = $(this).scrollLeft();
			
			position += direction > 0 ? -amount : amount;
			$(this).scrollLeft(position);
			event.preventDefault();
		})
	};
});

window.RBKOrders = function() {
	var _first_time = true;

	var order_api = null;
	var order_lines = new Array();
	var offers = new Array();
	var html_empty = $("#rbkor_oitems").html();
	var cl_orders = null;

	var _fixed_widgets;

	var _cot_ = RBKORDER.GLOBAL.CUR_ORDER_TYPE;
	
	var _ot_radio_selector = '#rbkor_order_type_out_modal_wrap input:radio[name=rbkor_order_type]';

	_cot_ = _is_order_type_available(_cot_) ? _cot_ : $.cookie("rbkor_type");

	if (_first_time) {
		validation_cookie_ajax({
			callback_success: _pre_init,
			callback_error: function () {
				// Ocultamos todos los modales... aunque no deberia tener ninguno mostrado en este momento...
				$('#rbkor_modal_order_type').modal("hide");
				$("#rbkor_modal_zones").modal("hide");
				$("#rbkor_modal_opening_booking").modal("hide");
				$("#rbkor_modal_opening_delivery").modal("hide");
				$("#rbkor_modal_opening_takeaway").modal("hide");
				
				RBKModalOrder.hide();
				//-- waitingDialog.hide();
				
				// Esto es una cosa que solo tiene la version de wp... si da error el tema de 
				// cookies por ajax, tenemos que redirigirlo a la version jsp
				document.location = RBKORDER.GLOBAL.ERR_COOKIE_3RD_ORDER_FORM;
				/*
				// Mostramos el error y en cuanto le de al boton salimos a RBKORDER.GLOBAL.ERR_COOKIE_3RD_HOME_URL
				errorDialog.show({
					title: RBKORDER.GLOBAL.ERR_COOKIE_3RD_TITLE,
					message: RBKORDER.GLOBAL.ERR_COOKIE_3RD_BODY,
					label_btn: RBKORDER.GLOBAL.ERR_COOKIE_3RD_BTN,
					dialogSize: 'm',
					onClick: function () {
						document.location = RBKORDER.GLOBAL.ERR_COOKIE_3RD_HOME_URL;
					},
				});
				*/
			}
		});
	}
	
	/**
	 * Helper para acceder a la variable global del tipo de pedido actual
	 */
	function _ot_cfg() {
		return RBKORDER[_cot_.toUpperCase()];
	}

	/**
	 * Para llamar antes de la inicializacion de todo el tema de pedidos, _pre_init, se encarga de 
	 * establecer unas cosas antes de la inicializacion:
	 * 1- El tipo de pedido
	 * 2- Manda stats a GTM sobre la visita inicial
	 */
	function _pre_init() {
		// Si hay varias opciones disponibles de tipo de pedido y no estamos forzando un tipo de pedido en particular...
		if (_is_order_type_variable() && !RBKORDER.GLOBAL.LOAD_ORDER_TYPE) {
			// Sacamos el modal de seleccion de tipos de pedido, posponiendo el init para cuando se haya ocultado
			modal_select_cot(_cot_, $("#rbkor_order_type_out_modal_wrap #rbkor_order_type_wrap"), function (_current_order_type) {
				// Almacenamos el tipo de pedido seleccionado
				_cot_ = _current_order_type;
				
				// Activamos la opcion seleccionada
				$(_ot_radio_selector).removeAttr('checked').parent().removeClass("active");
				$(_ot_radio_selector + '[value=' + _cot_ + ']').prop('checked', true).parent().addClass("active");
				
				// Almacenamos como cookie el tipo de pedido seleccionado
				$.cookie("rbkor_type", _cot_);
				
				// Mandamos a GTM que hemos visitado el catalogo
				GTMUtils.catalog_first_view(_cot_);
				
				// Iniciamos
				_init();
			});
		// Si no hay opciones disponibles o se trata de un tipo de pedido forzado
		} else {
			// Almacenamos el tipo de pedido seleccionado
			_cot_ = _get_ot_field_value();
			
			// Mandamos a GTM que hemos visitado el catalogo
			GTMUtils.catalog_first_view(_cot_);
			
			// Iniciamos
			_init();
		}
	}
	
	/**
	 * El tipo de pedido seleccionado, puede venir de un radio o de un hidden.
	 * Esta funcion simplifica el acceso a esa informacion
	 */
	function _get_ot_field_value() {
		return _is_order_type_variable() ? $(_ot_radio_selector + ':checked').val() : $('#rbkor_order_type').val();
	}
	
	function _init() {
		// Si tiene varias opciones disponibles (radio) ...
		if (_is_order_type_variable()) {
			$(_ot_radio_selector).removeAttr('checked');
			$(_ot_radio_selector + '[value=' + _cot_ + ']').prop('checked', true);
			
			$(_ot_radio_selector).off("change").on("change", function () {
				_cot_ = $(_ot_radio_selector + ':checked').val();
				$.cookie("rbkor_type", _cot_);
				_order_type_change();
			});
		// ... sino, se trata solo de una opcion posible (hidden)
		} else {
			$("#rbkor_order_type").val(_cot_);
		}
		
		// Lanzamos las rutinas que posicionan los elementos segun si se trata de navegacion
		// movil o desktop
		_fixed_widgets = new RBKFixedWidgets();
		
		// lanzamos el evento de cuando cambia de tipo de pedido que es el que se encarga
		// de hacer practicamente toda la logica del formulario
		_order_type_change();
	}

	function _order_type_change() {
		// waitingDialog.show();
		
		// Si no se trata de delivery ocultamos y borramos la direccion de destino
		if (_cot_ != "delivery") {
			$("#rbkor_delivery_address").hide().find(".wrap-text").text("");
		}
		
		// Cogemos el pedido nuevo en base al tipo
		call_ajax_ep(_get_order_type_url(RBKORDER.GLOBAL.EP_ORDER_GET), null, function (response) {
			// Si no encuentra el pedido
			if (response.code == "404") {
				// Solo reseteamos el pedido
				_reset_order();
			} else {
				// Cargamos los datos del pedido recuperado
				_populate_order(response);
			}
			
			// waitingDialog.hide();
			
			// Mostramos solo los items de catalogo correspondientes al tipo de pedido actual:
			var $widget_nav = $(".order-navbar");
			
			$widget_nav.find("li[data-order]").hide();
			$widget_nav.find("a[data-order]").hide();
			$("div.catalog").hide();
			$("div.catalog-group").hide();
			$("div.catalog-item").hide();

			$widget_nav.find("li[data-" + _cot_ + "=1]").show();
			$widget_nav.find("a[data-" + _cot_ + "=1]").show();
			$("div.catalog[data-" + _cot_ + "=1]").show();
			$("div.catalog-group[data-" + _cot_ + "=1]").show();
			$("div.catalog-item[data-" + _cot_ + "=1]").show();
			
			// Si se trata de delivery y cumplimos los requisitos para la busqueda de direccion ...
			if (_cot_ == "delivery" && RBKModalPlacesSearch != false) {
				var _onhide_place_search = function () {
					RBKORDER.GLOBAL.LOAD_ORDER_TYPE = false;
					_pre_init();
				};
				
				$("#rbkor_modal_places_search .btn-primary").prop('disabled', true);
				var $delivery_address_wrap = $("#rbkor_delivery_address");
				
				// Si tenemos ya la direccion en la sesion actual... (seguramente se trate de un refresco de la pagina)
				if (response.deliveryClientAddress) {
					// Mostramos la direccion.
					$delivery_address_wrap.show().find(".wrap-text").text(response.deliveryClientAddress);
					
					// Si ademas tiene precio de reparto ... 
					if (response.deliveryPrice) {
						// Mostramos el precio del reparto
						$delivery_address_wrap.data("delivery-price", response.deliveryPrice);
					}
				} else {
					// Si NO tenemos la direccion en la sesion actual, buscamos en las cookies
					RBKModalPlacesSearch.load_cookie_data(
						function (cookie_data) {
							// Si tenemos datos de las cookies, pintamos la direccion
							$delivery_address_wrap.show().find(".wrap-text").text(cookie_data.adr);
						},
						function () {
							// Si no tenemos datos de las cookies pero esta disponible el pedido
							if (!_is_disabled()) {
								// Mostramos el modal de busqueda de sitio
								RBKModalPlacesSearch.show(_render, _onhide_place_search);
							}
						}
					);
				}
				
				// Tambien ponemos el click de la direccion para que muestre el modal de busqueda de sitio
				$delivery_address_wrap.find(".rbkor_show_address").on("click", function (e) {
					RBKModalPlacesSearch.show(_render, _onhide_place_search);
				});
			}
			
			// Scroll de navegacion en los grupos de catalogo (scroll horizontal)
			require("./nav-group-catalog-scroll.js");
			
			// Dibujamos el resto de datos de pedido
			_render();

			if (RBKORDER.GLOBAL.LOAD_CATALOG_ITEM) {
				$("#catalog-item-" + RBKORDER.GLOBAL.LOAD_CATALOG_ITEM).trigger("click");
			}
		});
	}
	
	/**
	 * Reinicializa unas variables del pedido en su estado original asumiendo un nuevo pedido.
	 */
	function _reset_order() {
		order_api = null;
		order_lines = new Array();
		offers = new Array();
	}
	
	/**
	 * Carga y organiza en memoria los datos de un pedido
	 * 
	 * @param aeobj, JSON con los datos del pedido.
	 */
	function _populate_order(aeobj) {
		// Si tiene error, lo mostramos y salimos
		if (aeobj.code) {
			// TODO: Cambiar a modales estos mensajes de error hechos con alert
			if (aeobj.descrption) {
				alert(aeobj.description);
			} else {
				alert("Error: " + aeobj.code);
			}

			return;
		}
		
		// Reiniciamos el pedido
		_reset_order();
		
		// Establecemos el JSON de datos en una variable global
		order_api = aeobj;
		
		// Recorremos todas las lineas de pedido
		var aeoo = aeobj.orderLines;

		if (Array.isArray(aeoo)) {
			for (var i = 0; i < aeoo.length; i++) {
				var aeo = aeoo[i];
				var o = new RBKOrderLine();

				if (o.load_data(aeo)) {
					order_lines[order_lines.length] = o;
				}
			}
		}

		if (Array.isArray(aeobj.offers)) {
			offers = aeobj.offers;
		}
	}
	
	function _is_order_type_variable() {
		return $('input[name=rbkor_order_type]').attr("type") != "hidden";
	}

	function _is_order_type_available(order_type) {
		if (!order_type) {
			return false;
		}

		if (_is_order_type_variable()) {
			return $(_ot_radio_selector + "[value=" + order_type + "]").length != 0;
		}

		return $('#rbkor_order_type').val() == order_type;
	}

	function _is_delivery() {
		return _cot_ == "delivery";
	}
	
	function _is_takeaway() {
		return _cot_ == "takeaway";
	}
	
	function _add_order(id, options, quantity) {
		_action_order(RBKORDER.GLOBAL.EP_ORDER_ADD, id, options, quantity, "add_to_cart");
	};
	
	function _del_order(id, options, quantity) {
		_action_order(RBKORDER.GLOBAL.EP_ORDER_DEL, id, options, quantity, "remove_from_cart");
	};
	
	function _action_order(ep_url, id, options, quantity, gtm_action) {
		if (_is_disabled()) return;
		
		var args = {
			ci: id,
			qty: quantity
		};

		if (options != null && Array.isArray(options) && options.length > 0) {
			args["oo"] = [];
			args["oq"] = [];

			for (var i = 0; i < options.length; i++) {
				args["oo"][args["oo"].length] = options[i].id;
				args["oq"][args["oq"].length] = options[i].qty;
			}
		}

		call_ajax_ep(_get_order_type_url(ep_url), args, function (response) {
			_populate_order(response);
			_render();
			
			GTMUtils.push_item(gtm_action, _cot_, id, quantity);
		});
	};
	
	function _get_order_type_url(base_url) {
		return base_url.replace("{order-type}", _cot_);
	}

	function _update_cookies() {
		cl_orders.clear();

		for (var i in order_lines) {
			cl_orders.add(order_lines[i].encode());
		}
	};

	function _pre_checkout() {
		// Valida minimos para des/activar el boton de realizar pedido
		if (_is_disabled()) {
			$("#rbkor_ordernow").attr('disabled','disabled');
			$("#rbkor_mini_cart_ordernow").attr('disabled','disabled');
			$("#rbkor_shooping_cart_widget").addClass('rbkor_notavailable');
			return;
		} else {
			$("#rbkor_shooping_cart_widget").removeClass('rbkor_notavailable');
		}
		
		// Solo permitimos pedir si cumple el minimo de importe sin gastos de reparto o en caso de no tener minimo al menos ha pedido un item y vale algo
		if ( (_get_min_order() > 0 && _calc_total_without_delivery() >= _get_min_order()) || 
			(!_get_min_order() && order_api.orderLines && order_api.orderLines.length > 0 && _calc_total_without_delivery() > 0) ) {
			$("#rbkor_ordernow").removeAttr('disabled');
			$("#rbkor_mini_cart_ordernow").removeAttr('disabled');
			$("#rbkor_shooping_cart_widget").toggleClass('rbkor_disabled');
		} else {
			$("#rbkor_ordernow").attr('disabled','disabled');
			$("#rbkor_mini_cart_ordernow").attr('disabled','disabled');
			$("#rbkor_shooping_cart_widget").toggleClass('rbkor_disabled');
		}
	};

	function _checkout() {
		// TODO: Sacar modales en vez de los feo alerts
		if (( _get_min_order() && _calc_total_without_delivery() < _get_min_order() )) {
			alert(RBKORDER.GLOBAL.ERR_MIN_ORDER.replace('%s', _get_min_order()));
		} else if (_calc_total_without_delivery() == 0) {
			alert(RBKORDER.GLOBAL.ERR_NO_ITEMS);
		} else {
			GTMUtils.push_order("begin_checkout", _cot_, order_api);
			$("#ot").val(_get_ot_field_value());
			$("#rbkor_form").submit();
		}
	};


	function _calc_total_without_delivery() {
		var total = order_api && order_api.totalOrder ? order_api.totalOrder : 0;
		total += _calc_neg_discount();
		return total;
	};
	
	function _calc_total() {
		var total =_calc_total_without_delivery();

		if (_get_delivery_price() > 0) {
			total += _get_delivery_price();
		}

		return total;
	};

	function _calc_neg_discount() {
		return order_api && order_api.totalDiscount ? -1 * order_api.totalDiscount : 0;
	};

	function _get_delivery_price() {
		if (_is_delivery()) {
			var $delivery_address_wrap = $("#rbkor_delivery_address");
			if ($delivery_address_wrap.data("delivery-price")) {
				return parseFloat($delivery_address_wrap.data("delivery-price"));
			} else {
				return _ot_cfg().DELIVERY_PRICE;
			}
		}

		return 0;
	};

	function _get_min_order() {
		return _ot_cfg().MIN_ORDER;
	};

	function _is_disabled() {
		return !_ot_cfg().AVAILABLE_NOW;
	};

	function _contain_error() {
		return $("tr.rbkor_oitem_line:not(." + _cot_ + ")").length > 0;
	}

	function _get_count_qty_items() {
		var totalItems = 0;

		for (var i in order_lines) {
			totalItems += order_lines[i].qty;
		}

		return totalItems;
	};

	function _get_html() {
		var s = "";

		if (offers.length > 0) {
			s += '<tr class="rbkor_oitem_line offers ' + _cot_ + '">\n';
			s += '<td> \n';
			s += '<div class="rbkor_offers-wrap">\n';
			for (var i in offers) {
				var offer = offers[i];
				s += '<div class="rbkor_offer">\n';
				s += '<a class="rbkor_offer_link" href="javascript:void(0)" data-offer-code="' + offer.offerCode + '">\n';
				s += '<span class="wrap-icon"><svg class="icon icon-local-offer-outline" aria-hidden="true" role="img"><use href="#icon-local-offer-outline" xlink:href="#icon-local-offer-outline"></use></svg></span>\n';
				s += '<span class="wrap-text">' + offer.offerCode + '</span>';
				s += '</a>\n';
				s += '</div>\n';
			}
			s += '</div>\n';
			s += '</td>\n';
			s += '</tr>\n';
		}

		for (var i in order_lines) {
			s += order_lines[i].get_html();
		}

		if (s == "") {
			// TODO: el boton del widget en movil cuando no tiene articulos se pone aqui,
			// Deberiamos sacarlo antes y ponerle el evento y a posteriori meterlo...
			// es por esto que hacemos un init extra de widgets (que se podria evitar)
			s = html_empty;
		}

		if (_get_delivery_price() > 0) {
			s += '<tr class="rbkor_oitem_line delivery delivery-price">\n';
			s += '<td> \n';
			s += '<div class="rbkor_ovalue"><span class="rbkor_odesc">' + _ot_cfg().MSG_LINE_ORDER_DELIVERY_PRICE + '</span>';
			s += '<span class="rbkor_oprice">' + rbk_format_money(_get_delivery_price(), _money_config) + '</span>\n';
			s += '</div\n';
			s += '</td>\n';
			s += '</tr>\n';
		}

		return s;
	};

	function _render() {
		$("#rbkor_oitems").html(_get_html());

		$(".rbkor_offer a.rbkor_offer_link").each(function () {
			RBKOffers.init({
				"action": "del",
				"btn_selector": $(this),
				"callback": function (offer, callback) {
					call_ajax_ep(_get_order_type_url(RBKORDER.GLOBAL.EP_ORDER_OFFER_DEL), {oc: offer.offerCode}, function (response) {
						_populate_order(response);
						_render();
						callback();
					});
				}
			});

		});

		// Los items que no esten dentro del tipo los marcamos como erroneos
		$("tr.rbkor_oitem_line:not(." + _cot_ + ")").addClass("not-" + _cot_ + " error");

		$("#rbkor_oitems .rbkor_btn_add").click(function (e) {
			e.preventDefault();
			var $this = $(this);

			var opts = $this.data("options");
			if (opts) {
				opts = __unserialize_JSON(Base64.decode($this.data("options")));
			} else {
				opts = null;
			}

			_add_order($this.data("id"), opts, 1);
		});

		$("#rbkor_oitems .rbkor_btn_del").click(function (e) {
			e.preventDefault();
			var $this = $(this);

			var opts = $this.data("options");
			if (opts) {
				opts = __unserialize_JSON(Base64.decode($this.data("options")));
			} else {
				opts = null;
			}

			_del_order($this.data("id"), opts, 1);
			// _del_qty($(this).data("id"), Base64.decode($(this).data("size")));
		});

		var discount = _calc_neg_discount();

		if (discount < 0) {
			$(".rbkor_discount_otl").show();
		} else {
			$(".rbkor_discount_otl").hide();
		}

		$("#rbkor_discount_otl_value").html(rbk_format_money(discount, _money_config));
		$("#rbkor_otl_value").html(rbk_format_money(_calc_total(), _money_config));
		$(".rbkor_mini_cart_oqty").html(_get_count_qty_items());
		$(".rbkor_mini_cart_otl").html($("#rbkor_otl_value").html());


		var $widget_cart = $("#rbkor_shooping_cart_widget");
		
		/* TODO: Borrar ya no se usa y esta fuera del carro
		if (_is_delivery()) {
			$widget_cart.find(".rbkor_default_date_time").show();
		} else {
			$widget_cart.find(".rbkor_default_date_time").hide();
		}
		*/

		$(".rbkor_order_type_msg").html(_ot_cfg().MSG_SUB_TITLE);

		if (_cot_ == "delivery" && RBKORDER.DELIVERY.ZONES) {
			$("#rbko_check_delivery_zones").click(function (e) {
				e.preventDefault();
				$("#rbkor_modal_zones").modal("show");
			});
		}

		$("#rbko_check_opening_booking").click(function (e) {
			e.preventDefault();
			$("#rbkor_modal_opening_booking").modal("show");
		});
		
		$("#rbko_check_opening_delivery").click(function (e) {
			e.preventDefault();
			$("#rbkor_modal_opening_delivery").modal("show");
		});
		
		$("#rbko_check_opening_takeaway").click(function (e) {
			e.preventDefault();
			$("#rbkor_modal_opening_takeaway").modal("show");
		});
		
		var msgs_data_precontent = Array();

		if (_is_disabled()) {
			msgs_data_precontent[msgs_data_precontent.length] = {
				css : "error error-only-opening",
				msg : _ot_cfg().MSG_ALLOW_ONLY_ON_OPENING
			};
		} else if (!_ot_cfg().AVAILABLE_FOR_TODAY) {
			msgs_data_precontent[msgs_data_precontent.length] = {
				css : "error error-not-today",
				msg : _ot_cfg().MSG_NOT_ALLOW_TODAY
			};
		}
		
		// Tambien ocultamos el texto de la disponibilidad
		$(".rbkor_available_order").hide();
		
		// Sacamos el texto de la disponibilidad segun el tipo de pedido 
		if (_is_delivery()) {
			$(".rbkor_available_delivery").show();
		} else if (_is_takeaway()) {
			$(".rbkor_available_takeaway").show();
		}
		
		/*
		var $msgs_primary = $("#primary .rbkor_msgs");
		var $msgs_extra = $(".layout-catalog-navigation .rbkor_msgs");
		
		$msgs_primary.html("");
		$msgs_extra.html("");
		*/
		
		if (_get_min_order() && _calc_total_without_delivery() < _get_min_order()) {
			$("#rbkor_ordernow").find(".disabled-info .min-order-msg").text(_ot_cfg().MSG_MIN_ORDER);
			$("#rbkor_ordernow").find(".disabled-info").show();
			$("#rbkor_mini_cart_ordernow").find(".disabled-info .min-order-msg").text(_ot_cfg().MSG_MIN_ORDER)
			$("#rbkor_mini_cart_ordernow").find(".disabled-info").show();
			
			if (_get_delivery_price() > 0) {
				$("#rbkor_ordernow").find(".plus-dlv").show();
				$("#rbkor_mini_cart_ordernow").find(".plus-dlv").show();
			} else {
				$("#rbkor_ordernow").find(".plus-dlv").hide();
				$("#rbkor_mini_cart_ordernow").find(".plus-dlv").hide();
			}

			/*
			var $msg = $('<p>').addClass("info info-min-order");
			$msg.html(_ot_cfg().MSG_MIN_ORDER);
			$msgs_primary.append($msg);
			$msgs_primary.show();
			
			$msgs_extra.append($msg.clone());
			$msgs_extra.show();
			*/
		} else {
			$("#rbkor_ordernow").find(".disabled-info").hide();
			$("#rbkor_mini_cart_ordernow").find(".disabled-info").hide();
			/*
			$msgs_primary.hide();
			$msgs_extra.hide();
			*/
		}
		
		/* TODO: Esto no es necesario
		if (_get_delivery_price() > 0) {
			msgs_data_precontent[msgs_data_precontent.length] = {
				css : "info info-delivery-price",
				msg :_ot_cfg().MSG_DELIVERY_PRICE
			};
		}
		*/
		
		/* TODO: Se puede borrar... se ha movido a horarios
		if (_ot_cfg().AVAILABLE_NOW) {
			if (_ot_cfg().MIN_TIME_IN_ADVANCE) {
				msgs_data_precontent[msgs_data_precontent.length] = {
					css : "info info-time-advance",
					msg : _ot_cfg().MSG_MIN_TIME_IN_ADVANCE
				};
			}
		}
		*/
		
		var $msgs_precontent = $("#precontent .rbkor_msgs");

		$msgs_precontent.html("");

		if (msgs_data_precontent.length > 0) {
			for (var i = 0; i < msgs_data_precontent.length; i++) {
				var $msg = $('<p>').addClass(msgs_data_precontent[i].css);
				$msg.html(msgs_data_precontent[i].msg);
				$msgs_precontent.append($msg);
			}
			
			$msgs_precontent.show();
		} else {
			$msgs_precontent.hide();
		}

		if (_first_time) {
			_first_time = false;

			$("#rbkor_mini_cart_ordernow").click(function (e) {
				e.preventDefault();
				_checkout();
			});

			$("#rbkor_ordernow").click(function (e) {
				e.preventDefault();
				_checkout();
			});

			// Chequeamos cuales de los grupos existen para mostrar en el navegador
			$("#rbkor_navigator_widget a").each(function () {
				if ($($(this).attr("href")).length == 0) {
					$(this).parent().remove();
				}
			});

			$("#rbkor_navigator_widget li ul").each(function () {
				if ($(this).html() == "") {
					$(this).parent().remove();
				}
			});

			_fixed_widgets.init();

			$("div.catalog-item.carte-item, div.catalog-item.menu-item").click(function (e) {
				e.preventDefault();

				var $this = $(this);

				// Si tiene modificadores sacamos el modal, en otro caso lo aniadimos directamente
				RBKModalOrder.show($this, _money_config, function (item_id, opts, qty) {
					_add_order(item_id, opts, qty);
				});
				
				GTMUtils.push_item("view_item", _cot_, $this.data("id"), 1);
				
				/* TODO: Borrar cuando proceda, ahora mismo se meten todos como modales
				if ($("#mod-item-meta-" + $this.data("id")).length > 0) {
					RBKModalOrder.show($this, _money_config, function (item_id, opts, qty) {
						_add_order(item_id, opts, qty);
					});
				} else {
					_add_order($this.data("id"), null, 1);
				}
				*/
			});

			$('#txt_rbkor_offer_search').keydown(function (e) {
				if (e.keyCode == 13) {
					e.preventDefault();
					return false;
				}
			});

			RBKOffers.init({
				"action": "add",
				"btn_selector": "#rbkor_btn_search_offer",
				"query_selector": "#txt_rbkor_offer_search",
				"callback": function (offer, callback) {
					call_ajax_ep(_get_order_type_url(RBKORDER.GLOBAL.EP_ORDER_OFFER_ADD), {oc: offer.offerCode}, function (response) {
						_populate_order(response);
						$("#txt_rbkor_offer_search").val("");
						_render();
						callback();
					});
				}
			});
			
			if (RBKORDER.GLOBAL.CUR_OFFER && !is_offer_selected(RBKORDER.GLOBAL.CUR_OFFER)) {
				RBKOffers.search({
					"action": "add",
					"btn_selector": "#rbkor_btn_search_offer",
					"query_selector": "#txt_rbkor_offer_search",
					"offer_code": RBKORDER.GLOBAL.CUR_OFFER,
					"callback": function (offer, callback) {
						call_ajax_ep(_get_order_type_url(RBKORDER.GLOBAL.EP_ORDER_OFFER_ADD), {oc: RBKORDER.GLOBAL.CUR_OFFER}, function (response) {
							_populate_order(response);
							_render();
							callback();
						});
					}
				});
			}
		}
		
		// Despues de refrescar el contenido hay que vincular de nuevo
		// los eventos de fragmentos dentro del widget de carrito
		_fixed_widgets.init();
		
		_pre_checkout();
	};
	
	function is_offer_selected(offer_code) {
		for (var i = 0; i < offers.length; i++) {
			var o = offers[i];
			if (o.offerCode.toLowerCase() == offer_code.toLowerCase()) {
				return true;
			}
		}
		
		return false;
	}

	return this;
};

// TODO: Esta funcion la tenemos duplicada en _order-line.js
function __unserialize_JSON(data) {
	return window.JSON && window.JSON.parse ? window.JSON.parse( data ) : (new Function("return " + data))();
}

},{"../jquery.cookie.js":2,"../listae/waiting-dialog.js":3,"./_call-ajax-ep.js":4,"./_fixed-widget-bar.js":5,"./_gtm-utils.js":6,"./_modal-select-cot.js":7,"./_order-line.js":8,"./_validation-cookie-ajax.js":9,"./base64.js":10,"./error-dialog.js":11,"./modal-order.js":12,"./money.js":13,"./nav-group-catalog-scroll.js":14,"./offer.js":15,"./places-search.js":17}],17:[function(require,module,exports){

/**
* Funciones comunes que se usan en varios sitios
*/

( function( window, factory ) {
	// universal module definition
	/*jshint strict: false */ /* globals define, module, require */
	if ( typeof define == 'function' && define.amd ) {
		// AMD
		return factory( window );
	} else if ( typeof module == 'object' && module.exports ) {
		// CommonJS
		module.exports = factory(
			window
		);
	} else {
		// browser global
		window.RBKModalPlacesSearch = factory(
			window
		);
	}
}( window, function factory( window ) {

var $ = jQuery;

if (typeof google == "undefined" || typeof google.maps == "undefined" || RBKORDER.GLOBAL.NO_GOOGLE_PLACES) {
	console.log("No se ha encontrado la libreria de google maps!");
	return false;
}

function RBKModalPlacesSearch() {
	var $modal = null;
	
	var last_result = null;

	var autocomplete;
	var on_set_address = null;
	
	this.show = function(_on_set_address, _on_hidden) {
		on_set_address = _on_set_address;
		
		if ($modal == null) {
			$modal = $('#rbkor_modal_places_search');
			
			autocomplete = new google.maps.places.Autocomplete(
				document.getElementById("txt_mps_query")
			);

			// TODO: Parametrizar con el pais del restaurante
			autocomplete.setComponentRestrictions({
				country: ["es"],
			});
			
			autocomplete.setFields(['place_id', 'address_components', 'geometry', 'icon', 'name']);

			// Para que seleccione el primer sitio si pulsa enter
			_select_first_on_enter(document.getElementById("txt_mps_query"));

			autocomplete.addListener('place_changed', function() {
				var place = autocomplete.getPlace();
				
				// Ocultamos todos los errores:
				$("#wrap-mps-map .alert").hide();

				if (!place.place_id) {
					_show_error("ae-error");
				} else {
					var geocoder = new google.maps.Geocoder;
					
					geocoder.geocode({'placeId': place.place_id}, function(results, status) {
						if (status === 'OK') {
							var result = results[0] ? results[0] : false;
							if (result != false) {
								// Perfect match ???
								show_map(geocoder, result);
								
								if (_is_valid_delivery_address(result)) {
									last_result = result;
									$("#txt_mps_query").val(result.formatted_address);
									$("#rbkor_modal_places_search .btn-primary").prop('disabled', false);
								} else {
									_show_errors(result);
								}
							}
						} else {
							_show_error("ae-error");
						}
					});
				}
			});
		}

		$modal.find(".btn-primary").one("click", function () {
			_disable_controls();
			
			var post_data = {
				'adr': last_result.formatted_address,
				'pid': last_result.place_id,
				'lat': last_result.geometry.location.lat,
				'lng': last_result.geometry.location.lng
			};

			__add_type_post_data("street_number");
			__add_type_post_data("route");
			__add_type_post_data("locality");
			__add_type_post_data("postal_code");

			_set_ae_address_on_order({
				post_data: post_data, 
				callback_done : function (_post_data) {
					var $delivery_address = $("#rbkor_delivery_address");
				
					$delivery_address.show().find(".wrap-text").text(post_data.adr);
					
					$modal.modal('handleUpdate');
					
					$.cookie("places_search", JSON.stringify(_post_data), {expires: 30});

					$modal.off("hidden.bs.modal");
					$modal.modal("hide");
					_enable_controls();
				}, 
				callback_on_not_valid: function () {
					last_result.address_not_valid = true;
					_show_errors(last_result);
				}
			});

			function __add_type_post_data(addr_com_type) {
				post_data[addr_com_type] = __search_address_component(last_result.address_components, addr_com_type);
			}
		});
		
		$modal.on("hidden.bs.modal", function (event) {
			if (_on_hidden) {
				_on_hidden();
			}
		});
		
		$modal.modal({
			backdrop: 'static',
			keyboard: false
		}).modal("show");
		
		function _select_first_on_enter(input){
			// store the original event binding function
			var _addEventListener = (input.addEventListener) ? input.addEventListener : input.attachEvent;

			// Simulate a 'down arrow' keypress on hitting 'return' when no pac suggestion is selected, and then trigger the original listener.
			function addEventListenerWrapper(type, listener) {
				if (type == "keydown") { 
				var orig_listener = listener;
				listener = function (event) {
					var suggestion_selected = $(".pac-item-selected").length > 0;
						if (event.which == 13 && !suggestion_selected) {
							var simulated_downarrow = $.Event("keydown", { keyCode:40, which:40 });
							orig_listener.apply(input, [simulated_downarrow]);
						}
						orig_listener.apply(input, [event]);
					};
				}
				// add the modified listener
				_addEventListener.apply(input, [type, listener]);
			}

			if (input.addEventListener) {
				input.addEventListener = addEventListenerWrapper;
			} else if (input.attachEvent) {
				input.attachEvent = addEventListenerWrapper;
			}
		}

		function show_map(geocoder, result) {
			var map = new google.maps.Map(document.getElementById('mps-map'), {
				zoom: 18,
				center: result.geometry.location
			});

			var marker = new google.maps.Marker({
				map: map,
				position: result.geometry.location,
				animation: google.maps.Animation.DROP,
				draggable: true
			});

			google.maps.event.addListener(marker, 'dragend',  function(m) {
				_disable_controls();
				// TODO: i18n
				$("#txt_mps_query").val("Geolocalizando posicion...");

				// Ocultamos todos los errores:
				$("#wrap-mps-map .alert").hide();
				
				geocoder.geocode({'location': m.latLng}, function(results, status) {
					$("#txt_mps_query").prop('disabled', false);

					if (status === 'OK' && results && results.length) {
						var result = _find_best_result(results);
						if (result) {
							last_result = result;
							$("#txt_mps_query").val(result.formatted_address);
							$("#rbkor_modal_places_search .btn-primary").prop('disabled', false);

							return true;
						} else {
							$("#txt_mps_query").val(results[0].formatted_address);
						}
					}
					
					_show_error("ae-error");

					return false;
				});
			});

			$("#wrap-mps-map").show();
			
			$modal.modal('handleUpdate');
		}

		function _find_best_result(results) {
			for (var i = 0; i < results.length; i++) {
				var result = results[i];
				if (_is_valid_delivery_address(result)) {
					return result;
				}
			}

			return false;
		}

		function _is_valid_delivery_address(result) {
			// Validamos que es un rooftop y validamos que tenga numero
			if (result && result.geometry && result.geometry.location_type && 
				result.geometry.location_type == "ROOFTOP" && 
				result.types && (result.types[0] == "street_address" || result.types[0] == "premise")) {
				
				var cur_postal_code = __search_address_component(result.address_components, "postal_code");

				// Validamos el cp
				if (__valid_postal_code(cur_postal_code)) {
					// var cur_town = 
					// Si es stuart... validamos la poblacion
					var cur_locality = __search_address_component(result.address_components, "locality");
					
					if (__valid_locality(cur_locality)) {
						return true;
					}
				}
			}

			return false;
		}
	}

	function _show_errors(result) {
		$("#rbkor_modal_places_search .btn-primary").prop('disabled', true);

		var post_data = {
			'adr': result.formatted_address,
			'pid': result.place_id,
			'lat': result.geometry.location.lat,
			'lng': result.geometry.location.lng
		};

		__add_type_post_data("street_number");
		__add_type_post_data("route");
		__add_type_post_data("locality");
		__add_type_post_data("postal_code");
		
		_hide_error("address-error");
		_hide_error("nn-error");
		_hide_error("pc-error");
		_hide_error("ll-error");
		_hide_error("not-precise-error");
		
		if (result.address_not_valid) {
			_show_error("address-error");
		} else {
			if (post_data.street_number == undefined || !post_data.street_number) {
				_show_error("nn-error");
			} else {
				if (post_data.postal_code == undefined || !post_data.postal_code || !__valid_postal_code(post_data.postal_code)) {
					_show_error("pc-error");
				} else {
					if (post_data.locality == undefined || !post_data.locality || !__valid_locality(post_data.locality)) {
						_show_error("ll-error");
					} else {
						_show_error("not-precise-error");
					}
				}
			}
		}
		

		function __add_type_post_data(addr_com_type) {
			post_data[addr_com_type] = __search_address_component(result.address_components, addr_com_type);
		}
	}

	function _show_error(css_class) {
		$("." + css_class + "-msg").show();
	}

	function _hide_error(css_class) {
		$("." + css_class + "-msg").hide();
	}

	this.hide = function() {
		if ($modal) {
			$modal.modal("hide");
		}
	}
	
	this.load_cookie_data = function (_callback_on_success, _callback_on_empty) {
		var cookie_data = $.cookie("places_search");
		
		if (cookie_data) {
			try {
				var post_data = JSON.parse(cookie_data);
				
				_set_ae_address_on_order({
					post_data: post_data,
					callback_done: function () {
						_callback_on_success(post_data);
					},
					callback_on_not_valid: function () {
						$.cookie("places_search", false);
						_callback_on_empty();
					}
				});
				
				return post_data;
			}
			catch (e) {
				$.cookie("places_search", false);
				_callback_on_empty();
			}
		} else {
			_callback_on_empty();
		}
		
		return false;
	}
	
	function _set_ae_address_on_order(_options) {
		var opts = $.extend({
			post_data: null,
			callback_done: null,
			callback_fail: null, 
			callback_on_not_valid: null
		}, _options);
		
		$.ajax({
			url: RBKORDER.GLOBAL.EP_DLV_SET_ADR,
			jsonp: "callback",
			dataType: 'jsonp',
			crossDomain: true,
			type: 'GET',
			data: opts.post_data
		}).done(function (data) {
			if (data.deliveryClientAddressValid) {
				var $delivery_address = $("#rbkor_delivery_address");
				
				if (data && data.deliveryPrice) {
					$delivery_address.data("delivery-price", data.deliveryPrice);
				}
				
				if (on_set_address != null) {
					on_set_address(data);
				}
				
				if (opts.callback_done) {
					opts.callback_done(opts.post_data);
				}
			} else if (opts.callback_on_not_valid) {
				opts.callback_on_not_valid();
			}
		}).fail(function( jqXHR, textStatus ) {
			if (opts.callback_fail) {
				opts.callback_fail(jqXHR, textStatus);
			}
		});
	}
	
	function __search_address_component(address_components, addcom_type) {
		for (var i = 0; i < address_components.length; i++ ) {
			var addr_com = address_components[i];
			for (var j = 0; j < addr_com.types.length; j++) {
				var cur_type = addr_com.types[j];
				if (cur_type == addcom_type) {
					return addr_com.long_name;
				}
			}
		}

		return null;
	}

	// TODO: Esto se podria quitar en un futuro (se hace la validacion en java)
	// por ahora lo mantenemos ya que nos ayuda a decir que error tiene
	function __valid_postal_code(_postal_code) {
		var pc_zones = [];

		$("#rbkor_modal_zones .po-box").each(function () {
			pc_zones[pc_zones.length] = $(this).text();
		});

		for (var i = 0; i < pc_zones.length; i++) {
			if (pc_zones[i] == _postal_code) {
				return true;
			}
		}

		return pc_zones.length ? false : true;
	}

	function __valid_locality(_locality) {
		var localities = [];

		$("#rbkor_modal_zones .localities .locality").each(function () {
			localities[localities.length] = $(this).text();
		});

		for (var i = 0; i < localities.length; i++) {
			if (localities[i].toLowerCase() == _locality.toLowerCase()) {
				return true;
			}
		}

		return localities.length ? false : true;
	}
	
	function _disable_controls() {
		$("#rbkor_modal_places_search .btn-primary").prop('disabled', true);
		$("#txt_mps_query").prop('disabled', true);
		$("#rbkor_modal_places_search .btn-secondary").prop('disabled', true);
		$("#rbkor_modal_places_search .close").prop('disabled', true);
	}
	
	function _enable_controls() {
		$("#rbkor_modal_places_search .btn-primary").prop('disabled', false);
		$("#txt_mps_query").prop('disabled', false);
		$("#rbkor_modal_places_search .btn-secondary").prop('disabled', false);
		$("#rbkor_modal_places_search .close").prop('disabled', false);
	}
}

return new RBKModalPlacesSearch();
}));

},{}],18:[function(require,module,exports){
// FIXME: Esto lo tenemos que hacer ya que si no no funcionan ni este script ni el resto... 
// hay que tenerlo en cuenta por si modifica el comportamiento del resto de scripts
window.$ = jQuery;

// FIXME: Este init no funciona en wp (pero si en jsp)... revisar que podemos hacer con esto
// $(function() {
	window.rbk_orders_init = function () {
		// FIXME: Al quitar lo anterior, las llamadas desde gtm no funciona porque no pilla jquery en la variable $
		window.$ = window.$ ? window.$ : jQuery;
		
		require("./inc/order/order.js");
	
		function _append_modal_html(modal_id, modal_cfg) {
			var title = modal_cfg["title"] == undefined ? "" : modal_cfg["title"];
			var label_ok = modal_cfg["label_ok"] == undefined ? "Ok" : modal_cfg["label_ok"];
			var label_cancel = modal_cfg["label_cancel"] == undefined ? "Cancel" : modal_cfg["label_cancel"];
			var body = modal_cfg["body"] == undefined ? "" : modal_cfg["body"];
			var extra_css = modal_cfg["extra_css"] == undefined ? "" : modal_cfg["extra_css"];
			var modal_html = "<!-- Modal -->";
	
			modal_html += '<div class="modal fade" id="' + modal_id + '" tabindex="-1" role="dialog" aria-labelledby="' + modal_id + 'Title" aria-hidden="true"';
	
			if (label_cancel == "") {
				modal_html += ' data-backdrop="static" data-keyboard="false"';
			}
	
			modal_html += ' style="display: none;">';
	
			modal_html += '<div class="modal-dialog ' + extra_css + '" role="document">';
			modal_html += '<div class="modal-content">';
			modal_html += '<div class="modal-header">';
			modal_html += '<h5 class="modal-title" id="' + modal_id + 'Title">' + title + '</h5>';
	
			if (label_cancel != "") {
				modal_html += '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
				modal_html += '<span aria-hidden="true">&times;</span>';
				modal_html += '</button>';
			}
	
			modal_html += '</div>';
	
			modal_html += '<div class="modal-body">' + body + '</div>';
	
			modal_html += '<div class="modal-footer">';
			if (label_cancel != "") {
				modal_html += '<button type="button" class="btn btn-secondary" data-dismiss="modal">' + label_cancel + '</button>';
			}
			if (label_ok != "") {
				modal_html += '<button type="button" class="btn btn-primary">' + label_ok + '</button>';
			}
	
			modal_html += '</div>';
			modal_html += '</div>';
			modal_html += '</div>';
			modal_html += '</div>';
	
			$('body').append(modal_html);
		}
		
		if (window.Popper != undefined) {
			var rbk_orders;
			for (var modal_id in RBKORDER.MODALS) {
				_append_modal_html(modal_id, RBKORDER.MODALS[modal_id]);
			}
	
			if ($("#rbkor_oitems").length > 0) {
				rbk_orders = new RBKOrders();
			}
		} else {
			$(".rbkor_shooping_cart_widget_wrap").hide();
			console.log("Para poder tener pedidos en funcionamiento necesitas tener las rutinas de BOOTSTRAP incluidas en la pagina actual!");
		}
	};
	
	if (!RBKORDER.DISABLE_INIT) {
		window.rbk_orders_init();
	}
// FIXME: Este init no funciona en wp (pero si en jsp)... revisar que podemos hacer con esto
// });

},{"./inc/order/order.js":16}]},{},[18])

//# sourceMappingURL=rbk-order.js.map
