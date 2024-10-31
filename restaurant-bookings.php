<?php 
/*
Plugin Name: Restaurant Bookings
Plugin URI: https://wordpress.org/extend/plugins/restaurant-bookings/
Description: 
Version: 2.2.3
Author: listae
Author URI: https://listae.com/
Text Domain: restaurant-bookings
*/

// Para evitar llamadas directas
defined("ABSPATH") or exit();

require_once 'rbk-footer.php';

// commit de urgencia para comprobar si tiene php-curl
if (!function_exists("curl_init")) {
	add_action("admin_notices", function () {
		?>
		<div class="error fade">
			<h2><?php esc_html_e("System requirements not met", 'restaurant-bookings'); ?></h2>
			<p><?php esc_html_e("Restaurant Bookings needs the php-curl library.", 'restaurant-bookings'); ?></p>
			<p><a href="http://php.net/manual/es/book.curl.php"><?php esc_html_e("More information at http://php.net/manual/es/book.curl.php", 'restaurant-bookings'); ?></a></p>
		</div>
		<?php
	});
	
	return;
}

// intentamos incluir la API de listae
class_exists("Listae\Client\Api\ListaeApi") or require_once 'inc/ae-api/ae-api.php';

if (!defined("AE_OEMBED_URL")) define("AE_OEMBED_URL", "https://oembed.listae.com/");

require_once 'inc/rbk-order-widget-utils.php';
require_once 'shortcodes.php';
require_once 'rbk-widgets.php';
require_once 'option-page.php';
require_once 'rbk-admin-ajax.php';
require_once 'rbk-tinymce.php';
require_once 'rbk-gutenberg.php';
require_once 'rbk-scripts.php';

/**
 * Plugin LOADED!
 */
function rbk_plugins_loaded() {
	load_plugin_textdomain( 'restaurant-bookings', false, basename(dirname(__FILE__)) . "/languages" );
	
	add_filter("ae_access_token", array("RestaurantBooking", "get_access_token"));
}
add_action('plugins_loaded', 'rbk_plugins_loaded');

function rbk_plugin_locale($locale, $domain) {
	if ($domain == 'restaurant-bookings') {
		// En el caso de que no sea es, ponemos en_US
		if (empty($locale) || substr( $locale, 0, 2  ) !== "es") {
			return "en_US";
		} else {
			return "es_ES";
		}
	}

	return $locale;
}
add_filter("plugin_locale", "rbk_plugin_locale", 10, 2);

class RestaurantBooking {
	// Nombre de la carpeta del plugin
	const PLUGIN_SLUG = "restaurant-bookings";

	// Nombre de la pagina de opciones
	const PAGE_OPTIONS = "rbk-options";
	
	const VERSION = "2.2.0";
	
	/**
	 * Pagina de opciones, solo instanciada
	 * cuando estes en el admin
	 *
	 * @var ListaeOptionsPage $option_page
	 */
	private $option_page;

	function __construct() {
		if ( is_admin() ) {
			RBKAdminAjax::init();
			add_filter('plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'plugin_action_links' ) );
			
			if ( ! function_exists( 'register_block_type' ) || class_exists( 'Classic_Editor' ) ) {
				add_action("init", "RBKTinyMCE::init", 999);
			}
			
			$this->option_page = new ListaeOptionsPage();

			add_action( 'admin_enqueue_scripts', "RBKScripts::admin_enqueue_scripts" );
		} else {
			add_action( "wp_enqueue_scripts",  "RBKScripts::wp_enqueue_scripts" );
		}
		
		if ( function_exists( 'register_block_type' ) && !class_exists( 'Classic_Editor' ) ) {
			add_action("init", "RBKGutenberg::init", 999);
		}
		
		add_action("widgets_init", array ($this, "widgets_init"));
		
		wp_oembed_add_provider( 'http://listae.me/*', AE_OEMBED_URL, false );
		wp_oembed_add_provider( 'http://listae.com/*', AE_OEMBED_URL, false );
		wp_oembed_add_provider( 'https://listae.me/*', AE_OEMBED_URL, false );
		wp_oembed_add_provider( 'https://listae.com/*', AE_OEMBED_URL, false );
		
		add_action("init", "RBKShortcodes::get_instance");
	}
	
	public function widgets_init() {
		register_widget('Widget_Online_Booking');
		register_widget('Widget_Online_Booking_Slots');
		register_widget('Widget_Listae_Opening');
		register_widget('Widget_Listae_Map');
		register_widget('Widget_Listae_Reviews');
		register_widget('Widget_Listae_Catalog_Resume');
		
		if (apply_filters("rbk_show_order_widgets", true)) {
			register_widget('Widget_Order_Cart');
			register_widget('Widget_Order_Nav');
			register_widget('Widget_Order_Header');
		}
	}

	public function plugin_action_links($links) {
		$extra_links = array(
				'<a href="' . self::admin_url( self::PAGE_OPTIONS ) . '">' . __("Settings", 'restaurant-bookings') . '</a>',
				'<a href="http://listae.com/" target="_blank">listae.com</a>',
		);

		return array_merge( $links, $extra_links );
	}

	public static function plugin_dir($path = "") {
		return WP_PLUGIN_DIR . "/" . self::PLUGIN_SLUG . "/" . $path;
	}

	public static function admin_url( $option ) {
		$args = array( 'page' => $option );
		$url = add_query_arg( $args, admin_url( 'options-general.php' ) );
		return $url;
	}

	/**
	 * Comprueba si el sitio web esta registrado con listae.com
	 *
	 * @return boolean, true si esta registrado, false si no
	 */
	public static function is_registered() {
		if ( self::get_access_token() == "" ) {
			return false;
		}

		return true;
	}

	public static function get_access_token() {
		return get_option("ae_access_token", "");
	}
}

// Si no esta registrado no tenemos mucho mas que hacer a parte
// de decirle que registre el plugin
if (RestaurantBooking::is_registered()) {
	global $RBK;
	$RBK = new RestaurantBooking();
} else {
	require_once 'listae-register.php';
}
