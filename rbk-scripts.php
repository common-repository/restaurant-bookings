<?php 

class RBKScripts {
	public static function rbk_forms_scripts() {
		$min = (!defined("WP_DEBUG") || !WP_DEBUG) ? ".min" : "";
		
		wp_enqueue_script("rbk-forms", self::plugin_url("js/rbk-forms$min.js"), array("jquery",  "bootstrap"));

		wp_localize_script( 'rbk-forms', 'RBK_FORMS', array(
			"CLOSE_LABEL" 	=> __("Close", 'restaurant-bookings'),
		));
	}
	
	private static function rbk_widgets_scripts() {
		$min = (!defined("WP_DEBUG") || !WP_DEBUG) ? ".min" : "";
		
		wp_register_script( 'jquery-infinitescroll', self::plugin_url('js/third-party/jquery.infinitescroll.min.js'), array( 'jquery' ), '2.1.0' );
		wp_register_script( 'images-loaded', self::plugin_url('js/third-party/imagesloaded.pkgd.min.js'), array('jquery'), '3.1.8' );
		
		wp_enqueue_script("rbk-widgets", self::plugin_url("js/rbk-widgets$min.js"), array("jquery", 'jquery-infinitescroll', 'images-loaded'));
		
		wp_localize_script( 'rbk-widgets', 'RBK_WIDGETS', array(
			"EP_SLOTS_PARTY_SIZES" => AEUrl::get_listae_url(AE_URLS::EP_SLOTS_PARTY_SIZES),
			"EP_SLOTS_DATES" => AEUrl::get_listae_url(AE_URLS::EP_SLOTS_DATES),
			"EP_SLOTS_DINING_AREAS" => AEUrl::get_listae_url(AE_URLS::EP_SLOTS_DINING_AREAS),
			"EP_SLOTS_TIMES" => AEUrl::get_listae_url(AE_URLS::EP_SLOTS_TIMES),
			// TODO: Definir texto
			'ERR_SLOTS_NOT_FOUND' 	=> __('We are sorry but we have not found availability with these criteria.', 'restaurant-bookings'),
			'NO_MORE_REVIEWS_TXT' 	=> __("No more reviews found", 'restaurant-bookings'), // No se encontraron mas opiniones
			'MORE_REVIEWS_TXT'		=> __("Loading reviews", 'restaurant-bookings'), // Cargando opiniones
			'IMG_LOADING' 			=>  self::plugin_url('/img/loading.gif'),
			"MSG_SERVICE_BREAKFAST"	=> _x("Breakfast", "Restaurant services", 'restaurant-bookings'), 
			"MSG_SERVICE_BRUNCH"	=> _x("Brunch", "Restaurant services", 'restaurant-bookings'), 
			"MSG_SERVICE_LUNCH"		=> _x("Lunch", "Restaurant services", 'restaurant-bookings'), 
			"MSG_SERVICE_TEA"		=> _x("Tea", "Restaurant services", 'restaurant-bookings'), 
			"MSG_SERVICE_DINNER"	=> _x("Dinner", "Restaurant services", 'restaurant-bookings'), 
			"MSG_SERVICE_LATE"		=> _x("Late", "Restaurant services", 'restaurant-bookings'), 
		));
	}

	public static function wp_enqueue_scripts() {
		$min = (!defined("WP_DEBUG") || !WP_DEBUG) ? ".min" : "";
		
		if (wp_script_is( "google-maps", 'registered' )) {
			wp_register_script("rbk-map", self::plugin_url("js/rbk-map$min.js"), array("jquery",  "google-maps"));
		}
		
		wp_enqueue_style("restaurant-bookings", self::plugin_url("css/restaurant-booking.css"));
		
		if (!ListaeOptionsPage::get_exclude_bootstrap_css() && !wp_style_is("bootstrap")) {
			wp_enqueue_style("bootstrap", self::plugin_url("css/bootstrap.css"));
		}
		
		if (!ListaeOptionsPage::get_exclude_bootstrap_js() && !wp_script_is("bootstrap")) {
			wp_register_script("popper", self::plugin_url("js/third-party/popper.js"), array(), "1.12.9" );
			wp_register_script("bootstrap", self::plugin_url("js/third-party/bootstrap$min.js"), array("jquery", "popper"), "4.0.0");
		}
		
		self::rbk_forms_scripts();
		self::rbk_widgets_scripts();
	}
	
	public static function admin_enqueue_scripts($hook) {
		$min = (!defined("WP_DEBUG") || !WP_DEBUG) ? ".min" : "";
		
		if ($hook == "widgets.php") {
			wp_register_script("rbk-widget-admin",
				self::plugin_url("js/rbk-widget-admin$min.js"),
				array("jquery"), "0.0.1");
			
			wp_localize_script( 'rbk-widget-admin', 'RBK_WIDGET_ADMIN', array(
				"AJAX_URL" 	=> get_admin_url() . "/admin-ajax.php",
				"ERROR_GET_CONTENT" => __("Ops! We were unable to retrieve the data linked to the busines", 'restaurant-bookings'),
			));
			
			wp_enqueue_script("rbk-widget-admin");
		} else if ($hook == "settings_page_rbk-options") {
			wp_enqueue_script("rbk-admin-options", self::plugin_url("js/rbk-admin-options$min.js"), array("jquery",  "bootstrap"));
			
			wp_localize_script( 'rbk-admin-options', 'RBK_ADMIN_OPTIONS', array(
				"MSG_CONFIRM_DISCONNECT" => __("Are you sure?.\n\nThis action remove your credentials to access listae.com services.", 'restaurant-bookings'),
			));
		}
	}
	
	public static function plugin_url($path = "") {
		return plugins_url($path, __FILE__);
	}
}