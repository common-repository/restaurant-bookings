<?php 
// Para evitar llamadas directas
defined("ABSPATH") or exit();

class RBKGutenberg {
	public static function init () {
		wp_register_script(
			'rbk-gutenberg',
			plugins_url( 'gutenberg/rbk-gutenberg.js', __FILE__ ),
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'gutenberg/rbk-gutenberg.js' )
		);
		
		$aeapi = aeAPIS::get_instance();
		
		$search = aeAPIS::search_my_restaurants();
		
		$restaurants = array();
		
		if (!aeAPIS::is_error() && $search->getCount() > 0) {
			foreach ($search->getRestaurantInfo() as $r) {
				$restaurants[] = array(
					"label" => $r->getName() . " - " . $r->getAddress(),
					"value" => $r->getUrl(),
					"booking" => $r->getBookingsR2() ? true : false,
					"takeaway" => $r->getTakeaway() ? true : false,
					"delivery" => $r->getDelivery() ? true : false,
					"coupon" => $r->getCoupon() ? true : false,
					"contact" => $r->getEmailContactEnabled() ? true : false,
					"opening" => $r->getOpening() == null ? false : true,
					"map" => $r->getMap() ? true : false,
					"cartes" => $r->getCartes(),
					"menus" => $r->getMenus(),
				);
			}
			
			if (count($restaurants) > 0) {
				wp_localize_script( 'rbk-gutenberg', 'RBK_GUTENBERG', array(
					"ADMIN_AJAX_URL" => get_admin_url() . "/admin-ajax.php",
					"RESTAURANTS" 	=> $restaurants,
					"SHORTCODES"	=> array(
						"BOOKING_FORM" => array("key" => RBKShortcodes::BOOKING_FORM, "value" => RBKShortcodes::BOOKING_FORM, "label" => __("Booking form", 'restaurant-bookings')),
						"CONTACT_FORM" => array("key" => RBKShortcodes::CONTACT_FORM, "value" => RBKShortcodes::CONTACT_FORM, "label" => __("Contact form", 'restaurant-bookings')),
						"GROUP_FORM" => array("key" => RBKShortcodes::GROUP_FORM, "value" => RBKShortcodes::GROUP_FORM, "label" => __("Group menu request form", 'restaurant-bookings')),
					    "ORDER_CATALOG_FORM" => array("key" => RBKShortcodes::ORDER_CATALOG_FORM, "value" => RBKShortcodes::ORDER_CATALOG_FORM, "label" => __("Order catalog form", 'restaurant-bookings')),
						"ORDER_FORM" => array("key" => RBKShortcodes::ORDER_FORM, "value" => RBKShortcodes::ORDER_FORM, "label" => __("Order form", 'restaurant-bookings')),
						"ORDER_ALL" => array("key" => RBKShortcodes::ORDER_ALL, "value" => RBKShortcodes::ORDER_ALL, "label" => __("All the items available for orders", 'restaurant-bookings')),
						"ORDER_CART" => array("key" => RBKShortcodes::ORDER_CART, "value" => RBKShortcodes::ORDER_CART, "label" => __("Cart for orders", 'restaurant-bookings')),
						"ORDER_NAV" => array("key" => RBKShortcodes::ORDER_NAV, "value" => RBKShortcodes::ORDER_NAV, "label" => __("Orders category navigation", 'restaurant-bookings')),
						"REVIEW_FORM" => array("key" => RBKShortcodes::REVIEW_FORM, "value" => RBKShortcodes::REVIEW_FORM, "label" => __("Reviews form", 'restaurant-bookings')),
						"OPENING" => array("key" => RBKShortcodes::OPENING, "value" => RBKShortcodes::OPENING, "label" => __("Schedulle / Opening", 'restaurant-bookings')),
						"MAP" => array("key" => RBKShortcodes::MAP, "value" => RBKShortcodes::MAP, "label" => __("Map / Address", 'restaurant-bookings')),
						"CARTE" => array("key" => RBKShortcodes::CARTE, "value" => RBKShortcodes::CARTE, "label" => __("Menu", 'restaurant-bookings')),
						"CARTE_GROUP" => array("key" => RBKShortcodes::CARTE_GROUP, "value" => RBKShortcodes::CARTE_GROUP, "label" => __("Menu group", 'restaurant-bookings')),
						"CARTE_ALL" => array("key" => RBKShortcodes::CARTE_ALL, "value" => RBKShortcodes::CARTE_ALL, "label" => __("All the menus", 'restaurant-bookings')),
						"MENU" => array("key" => RBKShortcodes::MENU, "value" => RBKShortcodes::MENU, "label" => __("Set Menu", 'restaurant-bookings')),
						"MENU_GROUP" => array("key" => RBKShortcodes::MENU_GROUP, "value" => RBKShortcodes::MENU_GROUP, "label" => __("Menu group", 'restaurant-bookings')),
						"MENU_ALL" => array("key" => RBKShortcodes::MENU_ALL, "value" => RBKShortcodes::MENU_ALL, "label" => __("All the set menus", 'restaurant-bookings')),
						"MENU_BOOKING" => array("key" => RBKShortcodes::MENU_BOOKING, "value" => RBKShortcodes::MENU_BOOKING, "label" => __("Set menus availables for reservation", 'restaurant-bookings')),
						"COUPON" => array("key" => RBKShortcodes::COUPON, "value" => RBKShortcodes::COUPON, "label" => __("Coupons", 'restaurant-bookings')),
						"SERVICES" => array("key" => RBKShortcodes::SERVICES, "value" => RBKShortcodes::SERVICES, "label" => __("Services", 'restaurant-bookings')),
						"REVIEWS" => array("key" => RBKShortcodes::REVIEWS, "value" => RBKShortcodes::REVIEWS, "label" => __("Reviews", 'restaurant-bookings')),
					),
					"ERROR_GET_CONTENT" => __("Ops! We were unable to retrieve the data linked to the busines", 'restaurant-bookings'),
				));
				
				wp_register_style(
					'rbk-gutenberg-editor',
					plugins_url( 'gutenberg/rbk-gutenberg.editor.css', __FILE__ ),
					array( 'wp-edit-blocks' ),
					filemtime( plugin_dir_path( __FILE__ ) . 'gutenberg/rbk-gutenberg.editor.css' )
				);
				
				wp_register_style(
					'rbk-gutenberg',
					plugins_url( 'gutenberg/rbk-gutenberg.css', __FILE__ ),
					array( ),
					filemtime( plugin_dir_path( __FILE__ ) . 'gutenberg/rbk-gutenberg.css' )
				);

				register_block_type( 'bthemattic/rbk-gutenberg', array(
					"attributes"=> array(
						'business_id' => array(
							'type' => 'string',
							'default' => $restaurants[0]["value"],
						),
						'shortcode_name' => array(
							'type' => 'string',
							'default' => RBKShortcodes::BOOKING_FORM,
						),
						'content_id' => array(
							'type' => 'string',
						),
						'booking' => array(
							'type' => 'boolean',
							'default' => true,
						),
						'takeaway' => array(
							'type' => 'boolean',
							'default' => true,
						),
						'delivery' => array(
							'type' => 'boolean',
							'default' => true,
						),
						'allways_mobile' => array(
							'type' => 'boolean',
							'default' => false,
						),
						'for_order' => array(
							'type' => 'boolean',
							'default' => true,
						),
					),
					'style' => 'rbk-gutenberg',
					'editor_style' => 'rbk-gutenberg-editor',
					'editor_script' => 'rbk-gutenberg',
					'render_callback' => 'RBKGutenberg::shortcode_render',
				) );
			}
		}
		
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'rbk-gutenberg', 'restaurant-bookings', plugin_dir_path(__FILE__) . "/languages");
		}
	}
	
	public static function shortcode_render($attributes) {
		if (isset($attributes["shortcode_name"]) && isset($attributes["business_id"]) && RBKShortcodes::is_valid_shortcode_name($attributes["shortcode_name"])) {
			$shortcode_name = $attributes["shortcode_name"];
			$business_id = esc_attr(sanitize_title_with_dashes($attributes["business_id"]));
			$content_id = isset($attributes["content_id"]) ? intval($attributes["content_id"]) : 0;
			
			$shortcode_code = "[$shortcode_name id=\"$business_id\"";
			
			switch ($attributes["shortcode_name"]) {
				case RBKShortcodes::CARTE:
					$shortcode_code .= " carteid=\"$content_id\"";
				break;
				case RBKShortcodes::MENU:
					$shortcode_code .= " menuid=\"$content_id\"";
				break;
				case RBKShortcodes::CATALOG_ITEM:
					$shortcode_code .= " catalogitemid=\"$content_id\"";
					break;
				case RBKShortcodes::COUPON:
					$shortcode_code .= " couponid=\"$content_id\"";
				break;
				case RBKShortcodes::CARTE_GROUP:
				case RBKShortcodes::MENU_GROUP:
					$shortcode_code .= " groupid=\"$content_id\"";
				break;
			}
			
			if (isset($attributes["booking"]) && $attributes["booking"] === true) {
				$shortcode_code .= " booking=\"true\"";
			}
			
			if (isset($attributes["takeaway"]) && $attributes["takeaway"] === true) {
				$shortcode_code .= " takeaway=\"true\"";
			}
			
			if (isset($attributes["delivery"]) && $attributes["delivery"] === true) {
				$shortcode_code .= " delivery=\"true\"";
			}
			
			if (isset($attributes["allways_mobile"]) && $attributes["allways_mobile"] === true) {
				$shortcode_code .= " allways_mobile=\"true\"";
			}
			
			if (isset($attributes["for_order"]) && $attributes["for_order"] === true) {
				$shortcode_code .= " allways_mobile=\"true\"";
			}
			
			$shortcode_code .= "/]";
			
			return '<div>' . do_shortcode($shortcode_code) . '</div>';
		} elseif (defined("WP_DEBUG") && WP_DEBUG) {
			return '<p>' . esc_html__("Attributes error generating shortcode!", 'restaurant-bookings') . '</p>';
		} else {
			return "";
		}
	}
}

/**
 * Our combined block and shortcode renderer.
 *
 * For more complex shortcodes, this would naturally be a much bigger function, but
 * I've kept it brief for the sake of focussing on how to use it for block rendering.
 *
 * @param array $attributes The attributes that were set on the block or shortcode.
 */
function php_block_render( $attributes ) {
	return '<p>' . print_r( $attributes, true ) . '</p>';
}