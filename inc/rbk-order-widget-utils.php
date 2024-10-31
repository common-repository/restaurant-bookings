<?php 
// Para evitar llamadas directas
defined("ABSPATH") or exit();

class RBK_OrderCart_Widget_Utils {
	/**
	 * Encola los scripts de pedidos y devuelve la configuracion js
	 * que hemos calculado en base al $order_cfg
	 *
	 * @param \Listae\Client\Model\OrderCfg $order_cfg
	 * @param mixed[] $sc_args
	 * @return string[]|mixed[]
	 */
	public static function enqueue_scripts($order_cfg, $restaurant, $sc_args) {
		$min = (!defined("WP_DEBUG") || !WP_DEBUG) ? ".min" : "";
		
		wp_register_script("rbk-order",
			RBKScripts::plugin_url("js/rbk-order$min.js"),
			array("jquery"), "0.0.1");
		
		$config_js = self::get_order_cfg_js($order_cfg, $restaurant, $sc_args);
		
		$config_js["DISABLE_INIT"] = apply_filters("rbk_order_disable_init", false);
		
		$config_js["ALLWAYS_MOBILE"] = isset($sc_args["allways_mobile"]) && $sc_args["allways_mobile"] && $sc_args["allways_mobile"] != "false" ? true : false;
		
		$config_js["BUTTON_ADD"] = apply_filters("rbk_button_order_add", '<svg class="icon icon-add-circle-outline" aria-hidden="true" role="img"><use href="#icon-add-circle-outline" xlink:href="#icon-add-circle-outline"></use></svg>');
		$config_js["BUTTON_REMOVE"] = apply_filters("rbk_button_order_remove", '<svg class="icon icon-remove-circle-outline" aria-hidden="true" role="img"><use href="#icon-remove-circle-outline" xlink:href="#icon-remove-circle-outline"></use></svg>');
		
		$zones_html = "";
		
		if (isset($config_js["DELIVERY"]) && isset($config_js["DELIVERY"]["ZONES"])) {
			if (count($config_js["DELIVERY"]["ZONES"]["POSTAL_CODES"]) > 0) {
				$zones_html .= '<strong>' . 
					esc_html__("Check that the postal code of the address where you want to receive the order is among the following:", 'restaurant-bookings') . 
				'</strong><br/>';

				if (count($config_js["DELIVERY"]["ZONES"]["POSTAL_CODES"]) > 1) {
					foreach ($config_js["DELIVERY"]["ZONES"]["POSTAL_CODES"] as $country_code => $postal_codes) {
						$zones_html .= '<ul class="po-box-country">';
						$zones_html .= '<li class="po-box-country">';
						$zones_html .= '<strong>' . AEI18n::get_country_name($country_code) . '</strong>';
						
						$zones_html .= '<ul class="po-boxes">';
						foreach ($postal_codes as $postal_code) {
							$zones_html .= '<li class="po-box">' . esc_html($postal_code) . '</li>';
						}
						$zones_html .= '</ul>';

						$zones_html .= '</li>';
						$zones_html .= '</ul>';
					}
				} else {
					foreach ($config_js["DELIVERY"]["ZONES"]["POSTAL_CODES"] as $country_code => $postal_codes) {
						$zones_html .= '<ul class="po-boxes">';
						foreach ($postal_codes as $postal_code) {
							$zones_html .= '<li class="po-box">' . esc_html($postal_code) . '</li>';
						}
						$zones_html .= '</ul>';
					}
				}
			}

			if (count($config_js["DELIVERY"]["ZONES"]["LOCALITIES"]) > 0) {
				$zones_html .= '<strong>' . 
					esc_html__("Check that the locality of the address where you want to receive the order is among the following:", 'restaurant-bookings') . 
				'</strong><br/>';

				if (count($config_js["DELIVERY"]["ZONES"]["LOCALITIES"]) > 1) {
					foreach ($config_js["DELIVERY"]["ZONES"]["LOCALITIES"] as $country_code => $localities) {
						$zones_html .= '<ul class="locality-country">';
						$zones_html .= '<li class="locality-country">';
						$zones_html .= '<strong>' . AEI18n::get_country_name($country_code) . '</strong>';
						
						$zones_html .= '<ul class="localities">';
						foreach ($localities as $locality) {
							$zones_html .= '<li class="locality">' . esc_html($locality) . '</li>';
						}
						$zones_html .= '</ul>';

						$zones_html .= '</li>';
						$zones_html .= '</ul>';
					}
				} else {
					foreach ($config_js["DELIVERY"]["ZONES"]["LOCALITIES"] as $country_code => $localities) {
						$zones_html .= '<ul class="localities">';
						foreach ($localities as $locality) {
							$zones_html .= '<li class="locality">' . esc_html($locality) . '</li>';
						}
						$zones_html .= '</ul>';
					}
				}
			}
		}
		
		$config_js["MODALS"] = Array(
			"rbkor_modal_places_search" => Array(
				"title" => __("Search delivery place", 'restaurant-bookings'),
				"label_ok" => __("Ok", 'restaurant-bookings'),
				"label_cancel" => "",
				"body" =>
					'<input type="text" id="txt_mps_query" class="form-control modal-places-search-query" autocomplete="off" value="" placeholder="' . esc_html__("Insert your address, street number and city", 'restaurant-bookings') . '" />' .
					'<div id="wrap-mps-map" style="display: none;">' .
						'<div class="pc-error-msg alert alert-danger" role="alert" style="display: none;">' .
							'<h4 class="alert-heading">' . esc_html__("Postal code is not in the delivery zone.", 'restaurant-bookings') . '</h4>' . 
							'<p>' . esc_html__("If address postal code is mistaken you might correct it when submitting the order.", 'restaurant-bookings') . '</p>' . 
						'</div>' .
						'<div class="address-error-msg alert alert-danger" role="alert" style="display: none;">' .
							'<h4 class="alert-heading">' . esc_html__("Outside the permitted range:", 'restaurant-bookings') . '</h4>' .
							'<p>' . esc_html__("The delivery destination of the order is outside the delivery area.", 'restaurant-bookings') . '</p>' .
						'</div>' .
						'<div class="nn-error-msg alert alert-danger" role="alert" style="display: none;">' .
							'<h4 class="alert-heading">' . esc_html__("The address is not precise enough.", 'restaurant-bookings') . '</h4>' . 
							'<p>' . esc_html__("It looks like you haven't entered a street number. Drag the marker to the position where you would like to receive the order.", 'restaurant-bookings') . '</p>' . 
						'</div>' .
						'<div class="ll-error-msg alert alert-danger" role="alert" style="display: none;">' .
							'<p>' . esc_html__("Location not available for delivery.", 'restaurant-bookings') . '</p>' . 
						'</div>' .
						'<div class="ae-error-msg alert alert-danger" role="alert" style="display: none;">' .
							'<p>' . esc_html__("A delivery address was not found in that location.", 'restaurant-bookings') . '</p>' . 
						'</div>' .
						'<div class="not-precise-error-msg alert alert-danger" role="alert" style="display: none;">' .
							'<p>' . esc_html__("The direction is not precise enough, drag the marker to refine the geolocation.", 'restaurant-bookings') . '</p>' .
						'</div>' .
						'<div class="mps-description">' . esc_html__("You can correct the address if you want by dragging the marker on the map.", 'restaurant-bookings') . '</div>' .
						'<div id="mps-map" class="mps_map"></div>' . 
					'</div>'
			),
			"rbkor_modal_item_modifiers" => Array(
				"title" => __("Add to order", 'restaurant-bookings'),
				"label_ok" => __("Add", 'restaurant-bookings'),
				"label_cancel" => __("Cancel", 'restaurant-bookings'),
			),
			"rbkor_modal_order_type" => Array(
				"title" => __("Select type of order", 'restaurant-bookings'),
				"label_ok" => __("Select", 'restaurant-bookings'),
				"label_cancel" => "",
			),
			"rbkor_modal_zones" => Array(
				"title" => __("Delivery areas", 'restaurant-bookings'),
				"label_ok" => "",
				"label_cancel" => __("Close", 'restaurant-bookings'),
				"body" => $zones_html,
			),
			"rbkor_modal_add_offer" => Array(
				"title" => __("Opening", 'restaurant-bookings'),
				"label_ok" => __("Add Offer", 'restaurant-bookings'),
				"label_cancel" => __("Cancel", 'restaurant-bookings'),
				"body" => "",
				"extra_css" => "modal-lg offer-modal",
			),
			"rbkor_modal_del_offer" => Array(
				"title" => __("Offer", 'restaurant-bookings'),
				"label_ok" => __("Remove Offer", 'restaurant-bookings'),
				"label_cancel" => __("Cancel", 'restaurant-bookings'),
				"body" => "",
				"extra_css" => "modal-lg offer-modal",
			),
			"rbkor_modal_error" => Array(
				"title" => __("Error", 'restaurant-bookings'),
				"label_ok" => "",
				"label_cancel" => __("Close", 'restaurant-bookings'),
				"body" => "",
				"extra_css" => "modal-lg",
			),
		);
		
		$agendas_cfgs = array();

		if ( $restaurant->getAgendas()->getBooking() != null ) {
			$agendas_cfgs["bookings"] = array(
				"prefix"	=> "bookings",
				"title" 	=> __('Bookings', 'restaurant-bookings'),
				"agenda" 	=> $restaurant->getAgendas()->getBooking(),
				"show_closed" => true
			);
		}
		
		if ( $restaurant->getAgendas()->getDelivery() != null ) {
			$agendas_cfgs["delivery"] = array(
				"prefix"	=> "delivery",
				"title" 	=> __("Delivery at home", 'restaurant-bookings'),
				"agenda" 	=> $restaurant->getAgendas()->getDelivery(),
				"show_closed" => true
			);
			
			$delivery_cfg = $order_cfg ? $order_cfg->getDelivery() : null;
			
			if ($delivery_cfg) {
				$agendas_cfgs["delivery"]["min_time_in_advance"] = $delivery_cfg->getMinTimeInAdvance();
			}
		}
		
		if ( $restaurant->getAgendas()->getTakeaway() != null ) {
			$agendas_cfgs["takeaway"] = array(
				"prefix"	=> "takeaway",
				"title" 	=> __('Pick up', 'restaurant-bookings'),
				"agenda" 	=> $restaurant->getAgendas()->getTakeaway(),
				"show_closed" => true
			);
			
			$takeaway_cfg = $order_cfg ? $order_cfg->getTakeaway() : null;
			
			if ($takeaway_cfg) {
				$agendas_cfgs["takeaway"]["min_time_in_advance"] = $takeaway_cfg->getMinTimeInAdvance();
			}
		}
		
		if (isset($agendas_cfgs["bookings"])) {
			$config_js["MODALS"]["rbkor_modal_opening_booking"] = Array(
				"title" => $agendas_cfgs["bookings"]["title"],
				"label_ok" => '',
				"label_cancel" => __("Close", 'restaurant-bookings'),
				"body" => RBKTemplateTags::get_instance()->get_agenda_html($agendas_cfgs["bookings"], false),
				"extra_css" => 'modal-lg'
			);
		}
		
		if (isset($agendas_cfgs["delivery"])) {
			$config_js["MODALS"]["rbkor_modal_opening_delivery"] = Array(
				"title" => $agendas_cfgs["delivery"]["title"],
				"label_ok" => '',
				"label_cancel" => __("Close", 'restaurant-bookings'),
				"body" => RBKTemplateTags::get_instance()->get_agenda_html($agendas_cfgs["delivery"], false),
				"extra_css" => 'modal-lg'
			);
		}
		
		if (isset($agendas_cfgs["takeaway"])) {
			$config_js["MODALS"]["rbkor_modal_opening_takeaway"] = Array(
				"title" => $agendas_cfgs["takeaway"]["title"],
				"label_ok" => '',
				"label_cancel" => __("Close", 'restaurant-bookings'),
				"body" => RBKTemplateTags::get_instance()->get_agenda_html($agendas_cfgs["takeaway"], false),
				"extra_css" => 'modal-lg'
			);
		}
		
		if (!ListaeOptionsPage::get_exclude_bootstrap_js() && !wp_script_is("bootstrap")) {
			wp_enqueue_script( "bootstrap" );
		}
		
		wp_localize_script( 'rbk-order', 'RBKORDER', $config_js );
		wp_enqueue_script( "rbk-order" );
		
		if (wp_script_is( "google-maps", 'registered' )) {
			// Google maps es obligatorio para el tema de posicionamiento de delivery
			wp_enqueue_script( "google-maps" );
		}
		
		return $config_js;
	}
	
	/**
	 * Recupera los tipos posibles de tipos de pedido
	 * en base a la configuracion
	 *
	 * @param \Listae\Client\Model\OrderCfg $order_cfg
	 * @return string[]
	 */
	public static function get_order_types($order_cfg, $sc_args=array()) {
		$order_types = Array();
		
		if ((!isset($sc_args["delivery"]) || strtoupper($sc_args["delivery"]) == "TRUE" || $sc_args["delivery"] === true) &&
			!empty($order_cfg->getDelivery()) && $order_cfg->getDelivery()->getEnabled()) {
			
			$order_types["delivery"] = __("Delivery at home", 'restaurant-bookings');
		}
				
		if ((!isset($sc_args["takeaway"]) || strtoupper($sc_args["takeaway"]) == "TRUE" || $sc_args["takeaway"] === true) &&
			!empty($order_cfg->getTakeaway()) && $order_cfg->getTakeaway()->getEnabled()) {
			
			$order_types["takeaway"] = __("Pick up on local", 'restaurant-bookings');
		}
						
		if ((!isset($sc_args["booking"]) || strtoupper($sc_args["booking"]) == "TRUE" || $sc_args["booking"] === true) &&
			$order_cfg->getBooking()) {
			
			$order_types["booking"] = __("Consume in local", 'restaurant-bookings');
		}
		
		return $order_types;
	}
	
	/**
	 * Recupera la configuracion de pedidos para javascript
	 *
	 * @param \Listae\Client\Model\OrderCfg $order_cfg
	 * @param mixed[] $sc_args
	 * @return string[]|mixed[]
	 */
	private static function get_order_cfg_js($order_cfg, $restaurant, $sc_args) {
		$config_js = Array();
		$business_id = $sc_args['id'];
		
		$currency_config = null;
		
		if (!empty($order_cfg->getCartes())) {
			foreach ($order_cfg->getCartes()->getCarte() as $carte) {
				foreach ($carte->getGroup() as $group) {
					foreach ($group->getItem() as $item) {
						if (!empty($item->getCurrency())) {
							$currency_config = AEI18n::get_currency_config($item->getCurrency());
							break;
						}
					}
					if ($currency_config != null) break;
				}
				if ($currency_config != null) break;
			}
		}

		//  [booking] => [takeaway] => [delivery]
		// print_r($sc_args);
		$url_no_cookies = AEUrl::get_listae_url(AE_URLS::FORM_ORDER, array(
			"slug" => $business_id
		));

		if ($sc_args["booking"]) {
			$url_no_cookies = AEUrl::get_listae_url(AE_URLS::FORM_ORDER_BOOKING, array(
				"slug" => $business_id
			));
		} else if (!$sc_args["delivery"] || !$sc_args["takeaway"]) {
			if ($sc_args["delivery"]) {
				$url_no_cookies = AEUrl::get_listae_url(AE_URLS::FORM_ORDER_DELIVERY, array(
					"slug" => $business_id
				));
			} else if ($sc_args["takeaway"]) {
				$url_no_cookies = AEUrl::get_listae_url(AE_URLS::FORM_ORDER_TAKEAWAY, array(
					"slug" => $business_id
				));
			}
		}
		
		$currency_config = $currency_config != null ? $currency_config : AEI18n::get_currency_config();
		
		$global_config_js = array(
			'BUSINESS_ID' => $business_id,
			'LANGUAGE' => substr(get_locale(), 0, 2) == "es" ? "es" : "en",
			'GOOGLE_PLACE_KEY' => apply_filters("google-place-key", ""),
			'CURRENCY_SYMBOL' => $currency_config["symbol"],
			'THOUSAND_SYMBOL' => AEI18n::get_thousand_separator(),
			'DECIMAL_SYMBOL' => AEI18n::get_decimal_separator(),
			'DECIMAL_PLACES' => $currency_config["decimals"],
			'TOP_OFFSET_SCROLLED' => "0",
			'EP_ORDER_GET' => AEUrl::get_listae_url(AE_URLS::EP_ORDER_GET, null, Array("business-id" => $business_id)),
			'EP_ORDER_ADD' => AEUrl::get_listae_url(AE_URLS::EP_ORDER_ADD, null, Array("business-id" => $business_id)),
			'EP_ORDER_DEL' => AEUrl::get_listae_url(AE_URLS::EP_ORDER_DEL, null, Array("business-id" => $business_id)),
			'EP_ORDER_OFFER_ADD' => AEUrl::get_listae_url(AE_URLS::EP_ORDER_OFFER_ADD, null, Array("business-id" => $business_id)),
			'EP_ORDER_OFFER_DEL' => AEUrl::get_listae_url(AE_URLS::EP_ORDER_OFFER_DEL, null, Array("business-id" => $business_id)),
			'EP_ORDER_PING' => AEUrl::get_listae_url(AE_URLS::EP_ORDER_PING),
			'EP_DLV_SET_ADR' => AEUrl::get_listae_url(AE_URLS::EP_DLV_SET_ADR, null, Array("business-id" => $business_id)),
			'EP_OFFER_GET' => AEUrl::get_listae_url(AE_URLS::EP_OFFER_GET, null, Array("business-id" => $business_id)),
			'MAX_WIDTH_MOBILECART' => 800,
			'ERR_MIN_ORDER' => __('Min. order %s', 'restaurant-bookings'),
			'ERR_NO_ITEMS' => __('Add items to order', 'restaurant-bookings'),
			'ERR_AJAX' => __('Connection error, try again later.', 'restaurant-bookings'),
			'ERR_REQUIRED_MOD' => __('"{0}" is required.', 'restaurant-bookings'),
			'LABEL_QTY_GLOBAL' => __('Quantity', 'restaurant-bookings'),
			'LABEL_TOTAL_BTN' => __('Add to order {0}', 'restaurant-bookings'),
			'TITLE_ALLERGEN' => __('Information', 'restaurant-bookings'),
			'ERR_COOKIE_3RD_TITLE' => __('Error in browser settings', 'restaurant-bookings'),
			'ERR_COOKIE_3RD_BODY' => __('Your browser configuration restricts the insertion of cookies from third-party websites, which prevents the management of orders correctly.', 'restaurant-bookings'),
			'ERR_COOKIE_3RD_BTN' => __('Back', 'restaurant-bookings'),
			'ERR_COOKIE_3RD_ORDER_FORM' => $url_no_cookies,
			'ERR_AJAX_GEOCODING' =>  __('A delivery address was not found in that location.', 'restaurant-bookings'),
			'ERR_OFFER_NOT_FOUND' => __('No offers found with the code entered.', 'restaurant-bookings'),
			'ERR_OFFER_OTHER' => __('An error has occurred when consulting offers. Try again later and if the problem persists phone us.', 'restaurant-bookings'),
			'IS_MOBILE' => wp_is_mobile(),
			'URL_NO_IFRAME' => AEUrl::get_listae_url(AE_URLS::ORDER_REDIRECT, array(
				"slug" => $business_id,
				"no_header" => false,
				"back" => AEUrl::get_full_url()
			)),
			'NO_GOOGLE_PLACES' => isset($sc_args["no_google_places"]) ? $sc_args["no_google_places"] : false,
			'OFFER_MODAL_TITLE' => __('Offer detail with code', 'restaurant-bookings'),
			'OFFER_MODAL_TITLE_CONDITIONS' => __('Offer conditions:', 'restaurant-bookings'),
		);
		
		if (isset($sc_args['opening_page']) && !empty($sc_args['opening_page'])) {
			$global_config_js['URL_OPENING'] = get_permalink($sc_args['opening_page']);
		} else {
			$global_config_js['URL_OPENING'] = false;
		}
		
		if ($sc_args["delivery"] && !empty($order_cfg->getDelivery()) && $order_cfg->getDelivery()->getEnabled()) {
			$delivery_cfg = $order_cfg->getDelivery();
			
			$delivery_cfg_js = self::get_common_cfg_js($delivery_cfg);
			
			$postal_codes = Array();
			$localities = Array();
			
			if ($delivery_cfg->getZones()) {
				foreach ($delivery_cfg->getZones()->getZone() as $z) {
					if ($z->getPostalCode()) {
						if (!isset($postal_codes[$z->getCountry()])) {
							$postal_codes[$z->getCountry()] = Array();
						}
						$postal_codes[$z->getCountry()][] = $z->getPostalCode();
					} else if ($z->getLocality()) {
						if (!isset($localities[$z->getCountry()])) {
							$localities[$z->getCountry()] = Array();
						}
						$localities[$z->getCountry()][] = $z->getLocality();
					}
				}
			}
			
			$delivery_cfg_js["ZONES"] = [
				"POSTAL_CODES" => $postal_codes,
				"LOCALITIES" => $localities,
			];

			/*
			TODO: Esto tiene pinta que ya no se usa
			$opening_delivery = "";
			
			if ($global_config_js['URL_OPENING'] !== false) {
				$opening_delivery = '<br/><a href="' . esc_attr($global_config_js['URL_OPENING']) . '" '.
							'id="rbko_check_opening" class="delivery">' . __("Check opening hours", 'restaurant-bookings') . '</a>';
			}
			*/

			$check_delivery_time_html = "";
			
			// Existen algunos casos en los que es posible que esten activados los delivery
			// pero que no este disponible por la API (no se haya generado aun la agenda en el XML)... 
			// es por esto que tenemos que comprobar con la existencia de esta antes de pintar
			// el enlace que saca los detalles de la misma
			if ( $restaurant->getAgendas()->getDelivery() != null ) {
				$check_delivery_time_html = 
					'<p class="col col-6"><a href="javascript:void(0);" id="rbko_check_opening_delivery" class="btn btn-primary btn-block delivery">' .
						__("Delivery times", 'restaurant-bookings') . 
					'</a></p>';
			}
			
			$delivery_cfg_js['MSG_SUB_TITLE'] = '<p class="col col-6"><a href="javascript:void(0);" id="rbko_check_delivery_zones" class="btn btn-primary btn-block">' .
				__("Delivery areas", 'restaurant-bookings') . '</a></p>' . $check_delivery_time_html;
			
			if ($delivery_cfg->getDeliveryPrice() != null && $delivery_cfg->getDeliveryPrice() > 0) {
				$delivery_cfg_js["DELIVERY_PRICE"] = $delivery_cfg->getDeliveryPrice();
				$delivery_cfg_js["MSG_DELIVERY_PRICE"] = sprintf(__("Delivey cost: %s", 'restaurant-bookings'),
						"<strong>" . esc_html(AEI18n::format_price($delivery_cfg->getDeliveryPrice(), $delivery_cfg->getCurrency(), false)) . "</strong>" );
				$delivery_cfg_js["MSG_LINE_ORDER_DELIVERY_PRICE"] = __("Delivery cost", 'restaurant-bookings');
			}
			
			$config_js["DELIVERY"] = $delivery_cfg_js;
			
			$global_config_js['PLACE_SEARCH'] = apply_filters("rbk_google_place_search", $delivery_cfg->getGeocodeAddress());
		} else {
			$global_config_js['PLACE_SEARCH'] = false;
		}
		
		if ($sc_args["takeaway"] && !empty($order_cfg->getTakeaway()) && $order_cfg->getTakeaway()->getEnabled()) {
			$takeaway_cfg = $order_cfg->getTakeaway();
			$takeaway_cfg_js = self::get_common_cfg_js($takeaway_cfg);
			
			// Existen algunos casos en los que es posible que esten activados el takeaway
			// pero que no este disponible la agenda por la API (no se haya generado el XML)... 
			// es por esto que tenemos que comprobar con la existencia de esta antes de pintar
			// el enlace que saca los detalles de la misma
			if ( $restaurant->getAgendas()->getTakeaway() != null ) {
				$takeaway_cfg_js['MSG_SUB_TITLE'] = '<p class="col col-6"><a href="javascript:void(0);" id="rbko_check_opening_takeaway" class="btn btn-primary btn-block">' . __("Pick-up times", 'restaurant-bookings') . '</a></p>';
			} else {
				$takeaway_cfg_js['MSG_SUB_TITLE'] = '';
			}
			
			$config_js["TAKEAWAY"] = $takeaway_cfg_js;
		}
		
		if ($sc_args["booking"] && $order_cfg->getBooking()) {
			$booking_cfg = $order_cfg->getBooking();
			
			$booking_cfg_js= Array();
			
			// Existen algunos casos en los que es posible que esten activados las reservas
			// pero que no este disponible la agenda por la API (no se haya generado el XML)... 
			// es por esto que tenemos que comprobar con la existencia de esta antes de pintar
			// el enlace que saca los detalles de la misma
			if ( $restaurant->getAgendas()->getBooking() != null ) {
				$booking_cfg_js['MSG_SUB_TITLE'] = '<p class="col col-6"><a href="javascript:void(0);" id="rbko_check_opening_booking" class="btn btn-primary btn-block">' . __("Booking times", 'restaurant-bookings') . '</a></p>';
			} else {
				$takeaway_cfg_js['MSG_SUB_TITLE'] = '';
			}
			
			$booking_cfg_js["MIN_ORDER_DATE"] = $booking_cfg->getMinBookingDate();
			$booking_cfg_js["MAX_ORDER_DATE"] = $booking_cfg->getMaxBookingDate();
			
			$booking_cfg_js['MSG_SUB_TITLE'] = __("Make your reservation including in advance the order to be consumed", 'restaurant-bookings');
			$timestamp = new DateTime();
			
			$booking_cfg_js['AVAILABLE_NOW'] = $timestamp < $booking_cfg_js["MAX_ORDER_DATE"];
			$booking_cfg_js['AVAILABLE_FOR_TODAY'] = $booking_cfg_js['AVAILABLE_NOW'];
			$booking_cfg_js["MIN_ORDER"] = $booking_cfg->getMinOrder();
			
			if ($booking_cfg->getMinOrder() > 0) {
				$booking_cfg_js["MSG_MIN_ORDER"] = sprintf(__("Min. order %s", 'restaurant-bookings'), esc_html(AEI18n::format_price($booking_cfg_js["MIN_ORDER"], $booking_cfg->getCurrency(), false)) );
			} else {
				$booking_cfg_js["MSG_MIN_ORDER"] = "";
			}
			
			$config_js["BOOKING"] = $booking_cfg_js;
		}
		
		$order_types = RBK_OrderCart_Widget_Utils::get_order_types($order_cfg, $sc_args);
		
		$current_order_type =  isset($_GET["order_type"]) ? strtolower($_GET["order_type"]) : "";
		$load_order_type = false;
		
		if ($current_order_type && array_key_exists($current_order_type, $order_types)) {
			$load_order_type = true;
		} else {
			$current_order_type = array_key_first($order_types);
		}
		
		$global_config_js['CUR_ORDER_TYPE'] = $current_order_type;
		
		if (!empty($global_config_js['CUR_ORDER_TYPE']) && !isset($config_js[strtoupper($global_config_js['CUR_ORDER_TYPE'])])) {
			$global_config_js['CUR_ORDER_TYPE'] = "";
		}
		
		$global_config_js["LOAD_ORDER_TYPE"] = $load_order_type;
		$global_config_js["LOAD_CATALOG_ITEM"] = isset($_GET["item"]) ? $_GET["item"] : "";
		$global_config_js['CUR_OFFER'] = isset($_GET["offer"]) ? $_GET["offer"] : "";
		
		
		$config_js["GLOBAL"] = apply_filters("rbk-order-config", $global_config_js);
		
		return $config_js;
	}
	
	private static function get_common_cfg_js($cfg) {
		$cfg_js = Array();
		
		$cfg_js["MIN_TIME_IN_ADVANCE"] = $cfg->getMinTimeInAdvance();
		$cfg_js["MAX_TIME_IN_ADVANCE"] = $cfg->getMaxTimeInAdvance();
		$cfg_js["MIN_ORDER"] = $cfg->getMinOrder();
		$cfg_js["AVAILABLE_FOR_TODAY"] = $cfg->getAvailableForToday();
		$cfg_js["AVAILABLE_NOW"] = $cfg->getAvailableNow();
		$cfg_js["MIN_ORDER_DATE"] = $cfg->getMinOrderDate();
		
		if ($cfg->getMinOrder() > 0) {
			$cfg_js["MSG_MIN_ORDER"] = sprintf(__("Min. order %s", 'restaurant-bookings'), esc_html(AEI18n::format_price($cfg_js["MIN_ORDER"], $cfg->getCurrency(), false)) );
		} else {
			$cfg_js["MSG_MIN_ORDER"] = "";
		}
		
		if ( !$cfg->getAvailableForToday() ) {
			$cfg_js['MSG_NOT_ALLOW_TODAY'] = __("Orders are not processed for today, check the expected delivery date when you complete the order.", 'restaurant-bookings');
		}
		
		if ($cfg->getAvailableNow() && $cfg->getMinOrderDate() != null ) {
			$min_time_advance = $cfg->getMinTimeInAdvance();
			$min_time_advance = empty($min_time_advance) ? 0 : $min_time_advance;
			
			/* TODO: hay en determinados casos que no se puede realizar el pedido en este momento, cuando no tiene MIN_ORDER_DATE es uno
			 * de esos momentos, pues bien ahora vienen las curvas.... el problema es que la cache de varnish puede falsear las peticiones
			 * ya que al cargarse todo de forma estatica en la pagina... solucion, hay que cargar la config a traves de un servicio RESTFUL
			 * desde JS o calcular el min_order_date en base a las agendas, la hora actual y los tiempos extras de preparacion....
			echo "XXXX";
			print_r($cfg_js["MIN_ORDER_DATE"]);
			*/
			
			if ($min_time_advance > 0) {
				$description_min_time = $min_time_advance > 0 ? self::get_description_time($min_time_advance) : "";
				$cfg_js['MSG_MIN_TIME_IN_ADVANCE'] = sprintf(__("Minimum advance: %s", 'restaurant-bookings'),"<strong>" . $description_min_time. "</strong>" );
			}
		} else {
			$cfg_js['MSG_ALLOW_ONLY_ON_OPENING'] = __("Orders can only be made during opening hours. Thank you.", 'restaurant-bookings');
		}
		
		return $cfg_js;
	}
	
	private static function get_description_time($minutes) {
		$description_time = sprintf(__("%s minutes", "restaurant-bookings"), $minutes);
		
		if ($minutes> 60) {
			$hours = intval($minutes / 60);
			if ($hours > 1) {
				$description_time= sprintf(__("%s hours", "restaurant-bookings"), $hours);
			} else {
				$description_time= sprintf(__("one hour", "restaurant-bookings"), $hours);
			}
		}
		
		return $description_time;
	}
}
