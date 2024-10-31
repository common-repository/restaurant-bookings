
(function() {
	var blocks = window.wp.blocks;
	var editor = window.wp.editor;
	var components = window.wp.components;
	var el = window.wp.element.createElement;
	var cloneElement = window.wp.element.cloneElement;
	var __ = window.wp.i18n.__;
	
	const iconEl = el('svg', { width: 24, height: 24, viewBox: "0 0 24 24", fill: "#9E2026"},
		el('path', { d: "M7.5,19.6V4.4h2.4v15.1H7.5z M11.8,13.4h-0.2v-2.1c0,0,0.1,0,0.1,0c0.4,0,2.7-0.2,2.7-2.6c0-1.8-1.7-2.2-2.7-2.2c0,0-0.1,0-0.1,0V4.4h0.3c2,0,5,0.8,5,4.2C16.9,12.6,13.1,13.4,11.8,13.4z M15.4,19.8l-2.9-4.6l2.4-0.5l3,4.6L15.4,19.8z" })
	);
	
	function AEShortcodes() {
		this.populate_shortcodes = function () {
			ae_global_data.shortcodes.splice(0, ae_global_data.shortcodes.length);
			
			Object.keys(RBK_GUTENBERG.SHORTCODES).forEach(function(key, index) {
				var match = false;
				
				if (ae_global_data.business_id && ae_global_data.business_cfg) {
					switch (key) {
						case "BOOKING_FORM":
							if (ae_global_data.business_cfg.booking) match = true;
						break;
						case "CONTACT_FORM":
						case "GROUP_FORM":
						case "REVIEW_FORM":
							if (ae_global_data.business_cfg.contact) match = true;
						break;
						case "ORDER_CART":
						case "ORDER_NAV":
						case "ORDER_CATALOG_FORM":
						case "ORDER_FORM":
						case "ORDER_ALL":
							if (ae_global_data.business_cfg.delivery || ae_global_data.business_cfg.takeaway || ae_global_data.business_cfg.booking) match = true;
						break;
						case "OPENING":
							if (ae_global_data.business_cfg.opening) match = true;
						break;
						case "MAP":
							if (ae_global_data.business_cfg.map) match = true;
						break;
						case "CARTE":
						case "CARTE_GROUP":
						case "CARTE_ALL":
							if (ae_global_data.business_cfg.cartes) match = true;
						break;
						case "MENU":
						case "MENU_GROUP":
						case "MENU_ALL":
						case "MENU_BOOKING":
							if (ae_global_data.business_cfg.menus) match = true;
						break;
						default:
							match = true;
						break;
					}
				}
				
				if (match) ae_global_data.shortcodes.push(RBK_GUTENBERG.SHORTCODES[key]);
			});
		};
		
		this.populate_bss_info = function (bss_id) {
			ae_global_data.business_cfg = null;
			ae_global_data.business_id = bss_id;
			for (var i in RBK_GUTENBERG.RESTAURANTS) {
				var r = RBK_GUTENBERG.RESTAURANTS[i];
				if (r.value == bss_id) {
					ae_global_data.business_cfg = r;
					break;
				}
			}
		};
		
		this.init = function (bss_id, props) {
			var fix_wrong_prop = false;
			
			if (!bss_id) {
				bss_id = RBK_GUTENBERG.RESTAURANTS[0]["value"];
				props.setAttributes( { business_id: bss_id } );
				fix_wrong_prop = true;
			}
			
			this.populate_bss_info(bss_id);
			
			this.populate_shortcodes();
			
			if (fix_wrong_prop) {
				var items = ae_global_data.shortcodes;
				if (items.length > 0) {
					props.setAttributes( { shortcode_name: items[0].value } );
				} else {
					props.setAttributes( { shortcode_name: "" } );
				}
			}
		}
		
		this.set_values = function (bss_id, props) {
			this.populate_bss_info(bss_id);
			this.populate_shortcodes();
			
			var items = ae_global_data.shortcodes;
			if (items.length > 0) {
				props.setAttributes( { shortcode_name: items[0].value } );
			} else {
				props.setAttributes( { shortcode_name: "" } );
			}
		}
	}
	
	function AEContent() {
		this.get_contents = function () {
			return new Promise(
				function ( resolve, reject ) {
					var ajax_action = "";
					
					switch (ae_global_data.shortcode_name) {
						case RBK_GUTENBERG.SHORTCODES["MENU_GROUP"]["value"]:
							ajax_action = "ae-get-menu-groups";
						break;
						case RBK_GUTENBERG.SHORTCODES["CARTE_GROUP"]["value"]:
							ajax_action = "ae-get-carte-groups";
						break;
						case RBK_GUTENBERG.SHORTCODES["MENU"]["value"]:
							ajax_action = "ae-get-menus";
						break;
						case RBK_GUTENBERG.SHORTCODES["CARTE"]["value"]:
							ajax_action = "ae-get-cartes";
						break;
						case RBK_GUTENBERG.SHORTCODES["COUPON"]["value"]:
							ajax_action = "ae-get-coupons";
						break;
					}
					
					ae_global_data.contents.splice(0, ae_global_data.contents.length);
					
					if (ajax_action) {
						var jqxhr = jQuery.getJSON( RBK_GUTENBERG.ADMIN_AJAX_URL + "?action=" + ajax_action + "&term=" + ae_global_data.business_id, function(data) {
							switch (ae_global_data.shortcode_name) {
								case RBK_GUTENBERG.SHORTCODES["CARTE_GROUP"]["value"]:
									jQuery(data).each(function (i, carte) {
										jQuery(carte.groups).each(function (j, group) {
											ae_global_data.contents.push({ 
												key: "carte_group_" + group.id, 
												label: carte.value + " - " + group.value, 
												value: group.id
											});
										});
									});
								break;
								case RBK_GUTENBERG.SHORTCODES["MENU"]["value"]:
									jQuery(data).each(function (i, group) {
										jQuery(group.items).each(function (j, item) {
											ae_global_data.contents.push({ 
												key: "menu_" + item.id, 
												label: group.value + " - " + item.value, 
												value: item.id
											});
										});
									});
								break;
								case RBK_GUTENBERG.SHORTCODES["CARTE"]["value"]:
								case RBK_GUTENBERG.SHORTCODES["MENU_GROUP"]["value"]:
								case RBK_GUTENBERG.SHORTCODES["COUPON"]["value"]:
									for ( var item of data ) {
										ae_global_data.contents.push({ 
											key: ae_global_data.shortcode_name + "_" + item.id, 
											label: item.value, 
											value: item.id
										});
									}
								break;
							}
							resolve(ae_global_data.contents);
						}).fail(function() {
							reject(RBK_GUTENBERG.ERROR_GET_CONTENT);
						});
					}
				}
			)
		};
		
		this.init = function (shortcode_name) {
			ae_global_data.shortcode_name = shortcode_name;
			// console.log(props.attributes.content_id = );
			this.get_contents().catch( function (msg) {
				alert(msg);
			});
		}
		
		this.set_values = function (shortcode_name, props) {
			ae_global_data.shortcode_name = shortcode_name;
			
			toggle_wrappers_chk(shortcode_name, ae_global_data.business_cfg);
			
			this.get_contents().then(function (items) {
				if (items.length > 0) {
					props.setAttributes( { content_id: items[0].value } );
				} else {
					props.setAttributes( { content_id: "" } );
				}
			}).catch( function (msg) {
				alert(msg);
			});
		}
	}
	
	var ae_global_data = { 
		business_id: null,
		business_cfg: null,
		shortcode_name: null,
		shortcodes: [],
		contents: []
	};
	
	var aescs = new AEShortcodes();
	var aecnts = new AEContent();

	blocks.registerBlockType( 'bthemattic/rbk-gutenberg', {
		title: __('Restaurant Bookings', 'restaurant-bookings'),
		icon: iconEl,
		category: 'widgets',
		
		attributes: {
			business_id: {
				type: 'string'
			},
			shortcode_name: {
				type: 'string'
			},
			content_id: {
				type: 'string'
			},
			booking: {
				type: 'boolean',
				default: false
			},
			takeaway: {
				type: 'boolean',
				default: false
			},
			delivery: {
				type: 'boolean',
				default: false
			},
			allways_mobile: {
				type: 'boolean',
				default: false
			},
			for_order: {
				type: 'boolean',
				default: false
			}
		},

		edit: function( props ) {
			var chk_booking = null, chk_takeaway = null, chk_delivery = null, 
			chk_for_order = null, chk_allways_mobile = null;
			
			if (props.isSelected) {
				aescs.init(props.attributes.business_id, props);
				aecnts.init(props.attributes.shortcode_name);
			}
			
			var sel_businesses = el( components.SelectControl, {
				key: "sel_rbk_businesses",
				label: __( 'Businesses', 'restaurant-bookings' ),
				value: props.attributes.business_id,
				options: RBK_GUTENBERG.RESTAURANTS,
				onChange: ( value ) => {
					props.setAttributes( { business_id: value } ); 
					ae_global_data.shortcodes.splice(0, ae_global_data.shortcodes.length);
					props.setAttributes( { shortcode_name: "" } );
					ae_global_data.contents.splice(0, ae_global_data.contents.length);
					props.setAttributes( { content_id: "" } ); 
					aescs.set_values(value, props);
				},
			} );
			
			var sel_shortcodes_names = el( components.SelectControl, {
				key: "sel_rbk_shortcodes_names",
				label: __( 'Content name', 'restaurant-bookings' ),
				value: props.attributes.shortcode_name,
				options: ae_global_data.shortcodes,
				onChange: ( value ) => {
					props.setAttributes( { shortcode_name: value } ); 
					ae_global_data.contents.splice(0, ae_global_data.contents.length);
					props.setAttributes( { content_id: "" } ); 
					aecnts.set_values(value, props);
				},
			} );
			
			var sel_contents_details = el( components.SelectControl, {
				key: "sel_rbk_contents_details",
				label: __( 'Content item', 'restaurant-bookings' ),
				value: props.attributes.content_id,
				options: ae_global_data.contents,
				onChange: ( value ) => {
					props.setAttributes( { content_id: value } ); 
				},
			} );
			
			if (props.isSelected) {
				var is_booking = ae_global_data.business_cfg && ae_global_data.business_cfg.booking;
				var is_takeaway = ae_global_data.business_cfg && ae_global_data.business_cfg.takeaway;
				var is_delivery = ae_global_data.business_cfg && ae_global_data.business_cfg.delivery;
				var is_order = is_booking || is_takeaway || is_delivery;
				var is_catalog = ae_global_data.shortcode_name && (
					ae_global_data.shortcode_name == RBK_GUTENBERG.SHORTCODES["CARTE"]["value"] ||
					ae_global_data.shortcode_name == RBK_GUTENBERG.SHORTCODES["CARTE_GROUP"]["value"] ||
					ae_global_data.shortcode_name == RBK_GUTENBERG.SHORTCODES["CARTE_ALL"]["value"] ||
					ae_global_data.shortcode_name == RBK_GUTENBERG.SHORTCODES["MENU"]["value"] ||
					ae_global_data.shortcode_name == RBK_GUTENBERG.SHORTCODES["MENU_GROUP"]["value"] ||
					ae_global_data.shortcode_name == RBK_GUTENBERG.SHORTCODES["MENU_ALL"]["value"]
				);
				var is_order_cart = ae_global_data.shortcode_name == RBK_GUTENBERG.SHORTCODES["ORDER_CART"]["value"];
				
				chk_for_order = el( components.CheckboxControl, {
					key: "chk_rbk_for_order",
					label: __( 'For order', 'restaurant-bookings' ),
					checked: props.attributes.for_order,
					className: "wrap_for_order" + (is_order && is_catalog ? "" : " wrap_hide"),
					onChange: ( value ) => {
						props.setAttributes( { for_order: value } ); 
					},
				} );
				
				chk_booking = el( components.CheckboxControl, {
					key: "chk_rbk_booking",
					label: __( 'For booking', 'restaurant-bookings' ),
					className: "wrap_order_type wrap_order_type_booking" + (is_booking && is_order_cart ? "" : " wrap_hide"),
					checked: props.attributes.booking,
					onChange: ( is_checked ) => {
						props.setAttributes( { booking: is_checked } );
					},
				} );
				
				chk_takeaway = el( components.CheckboxControl, {
					key: "chk_rbk_takeaway",
					label: __( 'For takeaway', 'restaurant-bookings' ),
					className: "wrap_order_type wrap_order_type_takeaway" + (is_takeaway && is_order_cart ? "" : " wrap_hide"),
					checked: props.attributes.takeaway,
					onChange: ( value ) => {
						props.setAttributes( { takeaway: value } ); 
					},
				} );
				
				chk_delivery = el( components.CheckboxControl, {
					key: "chk_rbk_delivery",
					label: __( 'For delivery', 'restaurant-bookings' ),
					className: "wrap_order_type wrap_order_type_delivery" + (is_delivery && is_order_cart ? "" : " wrap_hide"),
					checked: props.attributes.delivery,
					onChange: ( value ) => {
						props.setAttributes( { delivery: value } ); 
					},
				} );
	
				chk_allways_mobile = el( components.CheckboxControl, {
					key: "chk_rbk_allways_mobile",
					label: __( 'Allways mobile', 'restaurant-bookings' ),
					className: "wrap_allways_mobile" + (is_order && is_order_cart ? "" : " wrap_hide"),
					checked: props.attributes.allways_mobile,
					onChange: ( value ) => {
						props.setAttributes( { allways_mobile: value } ); 
					},
				} );
			}
			
			return [
				el( components.ServerSideRender, {
					key: "ssr_rbk_gutenberg",
					block: 'bthemattic/rbk-gutenberg',
					attributes: props.attributes,
				} ),
				el( editor.InspectorControls, {key: "ic_rbk_gutenberg"},
					sel_businesses,
					sel_shortcodes_names,
					sel_contents_details,
					chk_booking,
					chk_takeaway,
					chk_delivery,
					chk_for_order,
					chk_allways_mobile
				),
			];
		},

		save: function( props ) {
			return el(
				'div',
				null,
				props.attributes.content
			);
		},
	} );
	
	/**
	 * Funcion que oculta todos los wrappers de checkbox y quita la 
	 * clase especial wrap_hide de los mismos
	 * 
	 * @returns void
	 */
	function hide_all_wrappers_chk() {
		jQuery(".wrap_for_order").removeClass("wrap_hide");
		jQuery(".wrap_order_type").removeClass("wrap_hide");
		jQuery(".wrap_allways_mobile").removeClass("wrap_hide");
		jQuery(".wrap_for_order").hide();
		jQuery(".wrap_order_type").hide();
		jQuery(".wrap_allways_mobile").hide();
	}
	
	/**
	 * Funcion que oculta o muestra checkbox segun lo seleccionado
	 * con anterioridad (negocio y shortcode)
	 * @param shortcode_name
	 * @param business_cfg
	 * @returns void
	 */
	function toggle_wrappers_chk(shortcode_name, business_cfg) {
		hide_all_wrappers_chk();
		
		if (business_cfg.booking || business_cfg.takeaway || business_cfg.delivery) {
			switch (ae_global_data.shortcode_name) {
			case RBK_GUTENBERG.SHORTCODES["CARTE"]["value"]:
			case RBK_GUTENBERG.SHORTCODES["CARTE_GROUP"]["value"]:
			case RBK_GUTENBERG.SHORTCODES["CARTE_ALL"]["value"]:
			case RBK_GUTENBERG.SHORTCODES["MENU"]["value"]:
			case RBK_GUTENBERG.SHORTCODES["MENU_GROUP"]["value"]:
			case RBK_GUTENBERG.SHORTCODES["MENU_ALL"]["value"]:
				jQuery(".wrap_for_order").show();
			break;
			case RBK_GUTENBERG.SHORTCODES["ORDER_CART"]["value"]:
				if (business_cfg.booking) {
					jQuery(".wrap_order_type_booking").show();
				}
				if (business_cfg.takeaway) {
					jQuery(".wrap_order_type_takeaway").show();
				}
				if (business_cfg.delivery) {
					jQuery(".wrap_order_type_delivery").show();
				}
				jQuery(".wrap_allways_mobile").show();
			break;
			}
			
		}
	}
}());
