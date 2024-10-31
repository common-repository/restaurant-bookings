<?php
defined("ABSPATH") or exit();

require_once "inc/ae-api/utils.php";
require_once "templates.php";

class RBKShortcodes {
	const MENU 				= "ae-menu";
	const MENU_GROUP 		= "ae-menu-group";
	const MENU_BOOKING 		= "ae-menu-booking";
	const MENU_ALL 			= "ae-menu-all";
	const CATALOG_ITEM		= "ae-catalog-item";
	const CARTE 			= "ae-carte";
	const CARTE_GROUP 		= "ae-carte-group";
	const CARTE_ALL			= "ae-carte-all";
	const COUPON			= "ae-coupon";
	const BOOKING_FORM		= "ae-booking-form";
	const BOOKING_WIDGET	= "ae-booking-widget";
	const BOOKING_SLOT_WIDGET = "ae-booking-slot-widget";
	const ORDER_CART		= "ae-order-cart";
	const ORDER_NAV			= "ae-order-nav";
	const ORDER_HEADER	  = "ae-order-header";
	const ORDER_CATALOG_FORM= "ae-order-catalog-form";
	const ORDER_FORM		= "ae-order-form";
	const ORDER_ALL			= "ae-order-all";
	const CONTACT_FORM		= "ae-contact-form";
	const GROUP_FORM		= "ae-group-form";
	const REVIEW_FORM		= "ae-review-form";
	const REVIEWS			= "ae-reviews";
	const OPENING			= "ae-opening";
	const MAP				= "ae-map";
	const MAP_WIDGET		= "ae-map-widget";
	const SERVICES			= "ae-services";
	const PUBLIC_OFFERS		= "ae-public-offers";

	private static $instance = null;

	private static $widget_index = 1;

	private $ob_levels = array();
	private $ob_buffers = array();

	public static function get_instance() {
		if (self::$instance == null) {
			self::$instance = new RBKShortcodes();
		}

		return self::$instance;
	}

	public static function get_all_shortcodes() {
		$oClass = new ReflectionClass(__CLASS__);
		$shortcodes = array();

		foreach ($oClass->getConstants() as $value) {
			$shortcodes[$value] = $value;
		}

		return $shortcodes;
	}

	public static function is_valid_shortcode_name($shortcode) {
		return array_search($shortcode, self::get_all_shortcodes()) !== false;
	}

	public function init_buffer() {
		$ob_level = ob_get_level();

		$this->ob_levels[] = $ob_level;

		if ($ob_level > 0) {
			$this->ob_buffers[] = ob_get_clean();
		} else {
			$this->ob_buffers[] = null;
		}

		ob_start();

		return $ob_level;
	}

	public function finish_buffer() {
		$ret = ob_get_clean();

		$ob_level = array_pop($this->ob_levels);
		$old_ob = array_pop($this->ob_buffers);

		if ($ob_level > 0) {
			ob_start();
			echo $old_ob;
		}

		return $ret;
	}

	private function feedback_form($url, $iframe_atts=array(), $no_iframe_form_name, $no_iframe_button_label="") {
		if (wp_is_mobile()) {
			$url = $this->feedback_form_url_extra_args($url, False, True, True);
			?>
			<meta http-equiv="refresh" content="0; URL=<?php echo esc_attr($url); ?>" />
			<div class="wrap_aeform mobile wrap_<?php echo esc_attr($no_iframe_form_name); ?>">
				<form name="<?php echo esc_attr($no_iframe_form_name); ?>" target="_top" action="<?php echo esc_attr($url . $extra_args); ?>" method="post">
					<input type="submit" value="<?php echo esc_attr($no_iframe_button_label); ?>" class="btn btn-primary btn-block" />
				</form>
			</div>
			<?php
		} else {
			$s = "";
	
			foreach ($iframe_atts as $k => $v) {
				$s .= " $k=\"" . esc_attr($v) . "\"";
			}
		   
			$url = $this->feedback_form_url_extra_args($url, True, True, False);
	
			?>
			<div class="wrap_aeform">
				<iframe<?php echo $s; ?> frameborder="0" marginheight="0" marginwiddth="0" allowtransparency="true"	src="<?php echo esc_attr($url); ?>"></iframe>
			</div>
			<?php
		}
	}

	private function feedback_form_url_extra_args($url, $skin=True, $language=True, $back=True) {
		$extra_args = "";
		
		if ($skin) {
			$skin = get_option("rb_skin", "");
	
			if (!empty($skin)) {
				$extra_args .= "&css=" . urlencode($skin);
			}
		}
		
		if ($language) {
			$extra_args .= "&lang=" . urlencode(AEI18n::get_cur_lang());
		}
		
		
		if ($back) {
			$extra_args .= "&back=" . urlencode(get_home_url());
		}
		
		if (!empty($extra_args)) {
			if (strpos($url, "?") === False) {
			  $extra_args = ltrim($extra_args, '&');
			  $extra_args = '?' . $extra_args;
			}
			
			$url .= $extra_args;
		}
		
		return $url;
	}

	/**
	 * Aniadimos en el init de wordpress
	 * el registro de los shortcodes
	 */
	public function __construct() {
		$this->register_shortcodes();
	}

	/**
	 * Registra los shortcodes
	 */
	public function register_shortcodes() {
		add_shortcode(RBKShortcodes::MENU, 			array($this, "menu"));
		add_shortcode(RBKShortcodes::MENU_GROUP, 		array($this, "menu_group"));
		add_shortcode(RBKShortcodes::MENU_BOOKING, 	array($this, "menu_booking"));
		add_shortcode(RBKShortcodes::MENU_ALL,  		array($this, "menu_all"));
		add_shortcode(RBKShortcodes::CATALOG_ITEM,		array($this, "catalog_item"));
		add_shortcode(RBKShortcodes::CARTE,  			array($this, "carte"));
		add_shortcode(RBKShortcodes::CARTE_GROUP, 		array($this, "carte_group"));
		add_shortcode(RBKShortcodes::CARTE_ALL,  		array($this, "carte_all"));
		add_shortcode(RBKShortcodes::BOOKING_FORM, 	array($this, "booking_form"));
		add_shortcode(RBKShortcodes::ORDER_CART, 		array($this, "order_cart"));
		add_shortcode(RBKShortcodes::ORDER_NAV, 		array($this, "order_nav"));
		add_shortcode(RBKShortcodes::ORDER_HEADER,	  array($this, "order_header"));
		// Compatibilidad 0.0.1
		add_shortcode("rbkform", 						array($this, "booking_form"));
		add_shortcode(RBKShortcodes::ORDER_CATALOG_FORM,array($this, "order_catalog_form"));
		add_shortcode(RBKShortcodes::ORDER_FORM, 		array($this, "order_form"));
		add_shortcode(RBKShortcodes::ORDER_ALL, 		array($this, "order_all"));
		add_shortcode(RBKShortcodes::COUPON, 			array($this, "coupon"));
		add_shortcode(RBKShortcodes::BOOKING_WIDGET, 	array($this, "booking_widget"));
		add_shortcode(RBKShortcodes::BOOKING_SLOT_WIDGET, array($this, "booking_slot_widget"));
		add_shortcode(RBKShortcodes::CONTACT_FORM, 	array($this, "contact_form"));
		add_shortcode(RBKShortcodes::GROUP_FORM, 		array($this, "group_form"));
		add_shortcode(RBKShortcodes::REVIEW_FORM, 		array($this, "review_form"));
		add_shortcode(RBKShortcodes::REVIEWS, 			array($this, "reviews"));
		add_shortcode(RBKShortcodes::OPENING, 			array($this, "opening"));
		add_shortcode(RBKShortcodes::MAP, 				array($this, "map"));
		add_shortcode(RBKShortcodes::MAP_WIDGET,		array($this, "map_widget"));
		add_shortcode(RBKShortcodes::SERVICES, 			array($this, "services"));
		add_shortcode(RBKShortcodes::PUBLIC_OFFERS,	 array($this, "public_offers"));
	}


	/**
	 * Shortcode que pinta el formulario con el catalogo para realizar el pedido
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio
	 * 		@type boolean $booking, si admite reservas pedidos para
	 * 			tomar en el restaurante
	 * 		@type boolean $takeaway, si admite pedidos para recoger
	 * 			en el restaurante
	 * 		@type boolean $delivery, si admite pedidos para recibir
	 * 			en el domicilio del solicitante
	 *	 TODO: Comentar resto de $atts...
	 * }
	 * @param string $content, TODO: POR AHORA NO SE USA!!!
	 */
	public function order_catalog_form( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_order_catalog_form_defaults", array(
			"id" => null,
			"booking" => false,
			"takeaway" => false,
			"delivery" => false,
			"allways_mobile" => false,
			"action_page" => "",
			"opening_page" => "",
			"title" => "",
			"before_title" => "",
			"after_title" => "",
			"no_google_places" => false,
			"_data" => null,
			"_data_order_cfg" => null,
		)) );

		/**
		 * @var \Listae\Client\Model\Restaurant $restaurant
		 */
		$restaurant = false;

		if ($args["_data"] == null) {
			$restaurant = aeAPIS::get_restaurant(array(
				"restaurant_id" => $args["id"]
			));
		} else {
			$restaurant = $args["_data"];
		}

		if (aeAPIS::is_error() || $restaurant == null) {
			$restaurant = false;
		}
		
		/**
		 * @var $order_cfg \Listae\Client\Model\OrderCfg
		 */
		$order_cfg = false;
		
		if ($restaurant !== false) {
			if ($args["_data_order_cfg"] == null) {
				$order_cfg = aeAPIS::get_order_cfg(array(
					"restaurant_id" => $restaurant->getUrl(),
				));
			} else {
				$order_cfg = $args["_data_order_cfg"];
			}
	
			if (aeAPIS::is_error() || $order_cfg == null || ((empty($order_cfg->getDelivery()) || !$order_cfg->getDelivery()->getEnabled()) &&
					(empty($order_cfg->getTakeaway()) || !$order_cfg->getTakeaway()->getEnabled()) &&
					empty($order_cfg->getBooking()))) {
				$order_cfg = false;
			}
		}
		
		$this->init_buffer();
		
		if ($restaurant === false || $order_cfg === false) {
			require_once 'inc/order/order-disabled.php';
		} else {
			$config_js = RBK_OrderCart_Widget_Utils::enqueue_scripts($order_cfg, $restaurant, $args);
			
			$action_url = "";
			
			// TODO: No estoy seguro de esto... en cupones funciona en un iframe VVVVV
			/* TODO: Para SCA necesitamos que el pago se haga fuera del iframe, asi que
			 * hemos tenido que quitar esto...
			if (isset($args['action_page']) && !empty($args['action_page'])) {
				$action_url = get_permalink($args['action_page']);
			} else {
				$action_url = $config_js["GLOBAL"]["URL_NO_IFRAME"];
			}
			... y forzar que siempre mande al formulario directamente */
			$action_url = $config_js["GLOBAL"]["URL_NO_IFRAME"];
	
			$order_type = isset($_GET["order_type"]) ? strtolower($_GET["order_type"]) : "";
			// TODO: Chequear que $order_type esta disponible
			$order_types = RBK_OrderCart_Widget_Utils::get_order_types($order_cfg, $args);

			if (count($order_types) > 0) {
				$order_type = $order_type ? $order_type : array_key_first($order_types);
			}
			
			$html_catalogs = $this->get_html_catalogs($order_cfg, Array(
				"id" => $args["id"],
				"for_order" => true,
				"_parent_shortcode" => RBKShortcodes::ORDER_CATALOG_FORM,
			));
			
			require_once 'inc/order/order-catalog-form.php';
		}
		
		return $this->finish_buffer();
	}
	
	/**
	 * Shortcode que pinta el carrito de los pedidos
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio
	 * 		@type boolean $booking, si admite reservas pedidos para
	 * 			tomar en el restaurante
	 * 		@type boolean $takeaway, si admite pedidos para recoger
	 * 			en el restaurante
	 * 		@type boolean $delivery, si admite pedidos para recibir
	 * 			en el domicilio del solicitante
	 * }
	 * @param string $content, no se usa
	 */
	public function order_cart( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_order_cart_defaults", array(
			"id" => null,
			"booking" => false,
			"takeaway" => false,
			"delivery" => false,
			"allways_mobile" => false,
			"action_page" => "",
			"opening_page" => "",
			"title" => "",
			"before_title" => "",
			"after_title" => "",
			"no_google_places" => false,
			"_data" => null,
			"_data_restaurant" => null,
		)) );

		/**
		 * @var $order_cfg \Listae\Client\Model\OrderCfg
		 */
		$order_cfg = null;
		
		if ($args["_data"] == null) {
			$order_cfg = aeAPIS::get_order_cfg(array(
				"restaurant_id" => $args["id"],
			));
		} else {
			$order_cfg = $args["_data"];
		}
		
		if (aeAPIS::is_error() || $order_cfg == null || ((empty($order_cfg->getDelivery()) || !$order_cfg->getDelivery()->getEnabled()) &&
				(empty($order_cfg->getTakeaway()) || !$order_cfg->getTakeaway()->getEnabled()) &&
				empty($order_cfg->getBooking()))) {
			return "";
		}
		
		/**
		 * @var \Listae\Client\Model\Restaurant $restaurant
		 */
		$restaurant = false;

		if ($args["_data_restaurant"] == null) {
			$restaurant = aeAPIS::get_restaurant(array(
				"restaurant_id" => $args["id"]
			));
		} else {
			$restaurant = $args["_data_restaurant"];
		}
		
		if (aeAPIS::is_error() || $restaurant == null) {
			return "";
		}
		
		// TODO: Ojo, aqui no deberia hacer falta hacer ninguna llamada a la API, pero para encolar
		// Los js, lo creemos conveniente.
		$config_js = RBK_OrderCart_Widget_Utils::enqueue_scripts($order_cfg, $restaurant, $args);
		
		$this->init_buffer();
		
		require_once 'inc/order/cart-widget.php';
		
		return $this->finish_buffer();
	}

	/**
	 * Shortcode que pinta la lista de categorias de navegacion para pedidos
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio
	 * 		@type boolean $booking, si admite reservas pedidos para
	 * 			tomar en el restaurante
	 * 		@type boolean $takeaway, si admite pedidos para recoger
	 * 			en el restaurante
	 * 		@type boolean $delivery, si admite pedidos para recibir
	 * 			en el domicilio del solicitante
	 * }
	 * @param string $content, no se usa
	 */
	public function order_nav( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_order_nav_defaults", array(
			"id" => null,
			"booking" => false,
			"takeaway" => false,
			"delivery" => false,
			"title" => "",
			"before_title" => "",
			"after_title" => "",
			"_data" => null,
		)) );

		/**
		 * @var $order_cfg \Listae\Client\Model\OrderCfg
		 */
		$order_cfg = null;

		if ($args["_data"] == null) {
			$order_cfg = aeAPIS::get_order_cfg(array(
				"restaurant_id" => $args["id"],
			));
		} else {
			$order_cfg = $args["_data"];
		}

		if (aeAPIS::is_error() || $order_cfg == null || ((empty($order_cfg->getDelivery()) || !$order_cfg->getDelivery()->getEnabled()) &&
			(empty($order_cfg->getTakeaway()) || !$order_cfg->getTakeaway()->getEnabled()) &&
			empty($order_cfg->getBooking()))) {
			return "";
		}
		
		$this->init_buffer();
		
		require_once 'inc/order/nav-widget.php';
		
		return $this->finish_buffer();
	}
	
	
	public function order_header( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_order_header", array(
			"id" => null,
			"booking" => false,
			"takeaway" => false,
			"delivery" => false,
			"title" => "",
			"before_title" => "",
			"after_title" => "",
			"_data" => null,
			"_data_restaurant" => null,
		)) );

		/**
		 * @var $order_cfg \Listae\Client\Model\OrderCfg
		 */
		$order_cfg = null;

		if ($args["_data"] == null) {
			$order_cfg = aeAPIS::get_order_cfg(array(
				"restaurant_id" => $args["id"],
			));
		} else {
			$order_cfg = $args["_data"];
		}

		if (aeAPIS::is_error() || $order_cfg == null || ((empty($order_cfg->getDelivery()) || !$order_cfg->getDelivery()->getEnabled()) &&
			(empty($order_cfg->getTakeaway()) || !$order_cfg->getTakeaway()->getEnabled()) &&
			empty($order_cfg->getBooking()))) {
			return "";
		}
		
		/**
		 * @var \Listae\Client\Model\Restaurant $restaurant
		 */
		$restaurant = false;

		if ($args["_data_restaurant"] == null) {
			$restaurant = aeAPIS::get_restaurant(array(
				"restaurant_id" => $args["id"]
			));
		} else {
			$restaurant = $args["_data_restaurant"];
		}
		
		if (aeAPIS::is_error() || $restaurant == null) {
			return "";
		}
		
		$order_type = isset($_GET["order_type"]) ? strtolower($_GET["order_type"]) : "";
		// TODO: Chequear que $order_type esta disponible
		$order_types = RBK_OrderCart_Widget_Utils::get_order_types($order_cfg, $args);
		

		
		if (count($order_types) > 1) {
			$order_type = $order_type ? $order_type : array_key_first($order_types);
		} elseif (count($order_types) == 1) {
			reset($order_types);
			$order_type = key($order_types);
		} else {
			$this->init_buffer();
			
			require_once 'inc/order/order-disabled.php';
			
			return $this->finish_buffer();
		}
		
		$this->init_buffer();
		
		require_once 'inc/order/header-widget.php';
		
		return $this->finish_buffer();
	}

	/**
	 * Shortcode que pinta un item de catalogo que no es un menu
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio
	 * 		@type int $catalogitemid Identificador del item de catalogo
	 * }
	 * @param string $content
	 */
	public function catalog_item( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_catalog_item_defaults", array(
			"id" => null,
			"catalogitemid" => 0,
			"for_order" => false,
			"_parent_shortcode" => null,
			"_data" => null,
		)) );

		/**
		 *
		 * @var $item \Listae\Client\Model\CatalogItem
		 */
		$item = null;

		if ($args["_data"] == null) {
			// Apanio para no hacer el servicio por ahora,
			// buscamos entre todos los items de la carta
			// en principio no se deberia usar
			$restaurant = aeAPIS::get_restaurant(array(
				"restaurant_id" => $args["id"],
			));

			if (!aeAPIS::is_error()) {
				$item = AECatalog::find_catalog_item($restaurant, $args["catalogitemid"]);
			}
		} else {
			$item = $args["_data"];
		}

		if (aeAPIS::is_error() || $item == null) {
			return "";
		}

		$this->init_buffer();

		$cur_item_currency = $item->getCurrency();

		$i_name = $featured_img = $img_url = $thumbnail_url = '';
		$img_url = $item->getImageUrl();

		if (!empty($img_url) ) {
			$featured_img = 'has-media has-featured-image';
			$thumbnail_url = $item->getThumbnailUrl();
		}

		$i_name = AEI18n::__( $item->getName() );

		$is_order = $hasprice = false;

		$is_order = $args['for_order'];

		$hasprice = $item->getPrice() != null && $item->getPrice() > 0 ;
		?>
		<div id="catalog-item-<?php echo $item->getUrl(); ?>" class="catalog-item carte-item <?php echo $featured_img ; if($is_order) echo ' item-order';?>"<?php if ($is_order && $hasprice) echo " " . AECatalog::get_data_item_properties_on_item($item); ?>>
			<?php if ($featured_img) {?>
				<div class="catalog-item-media">
					<div class="featured-image">
					<?php if($is_order) { ?>
							<img <?php if (isset($args["_lazy_load"]) && $args["_lazy_load"]) {  echo 'src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-ratio="false" data-lazy-'; } ?>src="<?php echo !empty($thumbnail_url) ?
								$thumbnail_url : $img_url; ?>" alt="<?php echo esc_attr($i_name);?>" />
						<?php } else {?>
						<a href="<?php echo $img_url; ?>" title="<?php echo esc_attr( sprintf( __('Full size image of %s', 'restaurant-bookings' ), $i_name ) );?>" <?php do_action("rbk_link_img_attr", "catalog-item", $i_name); ?>>
							<img <?php if (isset($args["_lazy_load"]) && $args["_lazy_load"]) {  echo 'src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-ratio="false" data-lazy-'; } ?>src="<?php echo !empty($thumbnail_url) ?
								$thumbnail_url : $img_url; ?>" alt="<?php echo esc_attr($i_name);?>" />
						</a>
						<?php }?>
					</div>
				</div>
			<?php }?>

			<div class="catalog-item-main">

				<div class="catalog-item-header">
					<?php if ($hasprice) { ?>
					<span class="catalog-item-price">
						<?php AEI18n::format_price($item->getPrice(), $cur_item_currency); ?>
						
						<?php if ($item->getOrderLineMinQty() != null && $item->getOrderLineMinQty() > 1) { ?>
							<?php if ($item->getGuestPrice() == null || $item->getGuestPrice() == 1 ) { ?>
								/ pax
							<?php } ?>
							
							<span><?php printf( __('(Min. %s)', 'restaurant-bookings'), AEI18n::format_price($item->getMinOrderPrice(), $cur_item_currency, false) ); ?></span>
						<?php } ?>
						
						<?php if ($item->getGuestPrice() != null && $item->getGuestPrice() > 1 ) { ?>
							<span><?php printf( __('(%s pax.)', 'restaurant-bookings'), $item->getGuestPrice() );?></span>
						<?php } ?>
					</span>
					<?php } ?>
					<h5><?php echo esc_html($i_name); ?></h5>
					<span class="hr"></span>
				</div>

				<?php if (!empty($item->getDescription())) { ?>
					<div class="catalog-item-description">
						<?php AEI18n::_e($item->getDescription()); ?>
					</div>
				<?php }	?>

				<?php self::add_modifiers_meta($item, $cur_item_currency); ?>
				
				<?php self::add_allergens($item); ?>
				
				
				<?php if($is_order) { ?>
				
					<span class="wrap-icon icon-add-to-cart">
						<svg class="icon icon-add-circle-outline" aria-hidden="true" role="img"><use href="#icon-add-circle-outline" xlink:href="#icon-add-circle-outline"></use></svg>
					</span>
				
				<?php }?>
			</div>

		</div><!-- catalog-item -->
		<?php

		do_action("ae_sc_catalog_item_after", $item, $args["_parent_shortcode"]);

		return $this->finish_buffer();
	}

	/**
	 * Shortcode que pinta un menu
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio
	 * 		@type int $menuid Identificador del menu
	 * 		@type string $url_back, url a donde regresa una vez
	 * 			terminada la reserva de determinado menu
	 * }
	 * @param string $content
	 */
	public function menu( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_menu_defaults", array(
			"id" => null,
			"menuid" => 0,
			"url_back" => AEUrl::get_full_url(),
			"for_order" => false,
			"btn_booking" => true,
			// TODO: Arreglar esto
			// workarround para intentar apanar por ahora el tema del enlace del menu
			"booking_url" => false,
			"_parent_shortcode" => null,
			"_data" => null,
		)) );

		/**
		 *
		 * @var $item \Listae\Client\Model\Menu
		 */
		$item = false;

		if ($args["_data"] == null) {
			$item = aeAPIS::get_menu(array(
				"restaurant_id" => $args["id"],
				"menu_id" => $args["menuid"],
			));
		} else {
			$item = $args["_data"];
		}

		if (aeAPIS::is_error() || $item == null) {
			return "";
		}

		$this->init_buffer();

		$booking_url = $args["booking_url"];

		$is_simple_item = empty($item->getModifiers()) || count($item->getModifiers()) == 0;

		// Solo podemos reservar si tenemos el id de restaurante y
		// el objeto es reservable y no se trata de pintar un item para
		// pedidos
		if ($args["id"] != null && $args["btn_booking"] && $item->getBooking() && $args["for_order"] == false) {
			// En este caso ademas comprobamos que el restaurante tiene reservas activadas (no hay otra)
			$restaurant = aeAPIS::get_restaurant(array(
				"restaurant_id" => $args["id"]
			));

			if (!aeAPIS::is_error() && $restaurant != null) {
				$biz_closed = $restaurant->getClosed();
				$bookings_enabled = $restaurant->getContentStats()->getBookings();
				if (!$biz_closed && $bookings_enabled) {
					$base_url = "";
					
					if (!$is_simple_item) {
						$base_url = AEUrl::get_listae_url(AE_URLS::FORM_ORDER_BOOKING, 
							Array(
								"item" => $item->getUrl()
							), 
							Array(
								"slug" => $args["id"],
							)
						);
					} else {
						$base_url = AEUrl::get_listae_url(AE_URLS::FORM_BOOKING, array(
							"slug" => $args["id"],
						));

						$base_url = AEUrl::add_params($base_url, array(
							"menu" => "Menu:" . $item->getUrl(),
						));
					}

					// TODO: Arreglar esto
					// workarround para intentar apanar por ahora el tema del enlace del menu
					if ($booking_url) {
						$booking_url = AEUrl::add_params($booking_url, array(
							"origin" => AEUrl::get_full_url(),
							"back" => $args["url_back"]
						));
					} else {
						$booking_url = apply_filters("ae_booking_url", $base_url, $args["id"], $item);
						
						$booking_url = AEUrl::add_params($booking_url, array(
							"origin" => AEUrl::get_full_url(),
							"back" => $args["url_back"]
						));
					}
				}
			}
		}

		$cur_item_currency = $item->getCurrency();
		$i_name = $featured_img = $img_url = $thumbnail_url = '';

		$img_url = $item->getImageUrl();

		if (!empty($img_url) ) {
			$featured_img = 'has-media has-featured-image';
			$thumbnail_url = $item->getThumbnailUrl();
		}
		$i_name = AEI18n::__( $item->getName() );

		$is_order = $hasprice = false;

		$is_order = $args['for_order'];

		$hasprice = $item->getPrice() != null && $item->getPrice() > 0 ;

		?>
		<div id="catalog-item-<?php echo $item->getUrl(); ?>" class="catalog-item menu-item <?php echo $featured_img ; if($is_order) echo ' item-order'; ?>" <?php if ($is_order && $hasprice) echo " " . AECatalog::get_data_item_properties_on_item($item); ?>>

			<?php if ($featured_img) {?>
				<div class="catalog-item-media">
					<div class="featured-image">
					<?php if($is_order) { ?>
							<img <?php if (isset($args["_lazy_load"]) && $args["_lazy_load"]) {  echo 'src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-ratio="false" data-lazy-'; } ?>src="<?php echo !empty($thumbnail_url) ?
								$thumbnail_url : $img_url; ?>" alt="<?php echo esc_attr($i_name);?>" />
						<?php } else {?>
						<a href="<?php echo $img_url; ?>" title="<?php echo esc_attr( sprintf( __('Full size image of %s', 'restaurant-bookings' ), $i_name ) );?>" <?php do_action("rbk_link_img_attr", "menu", $i_name); ?>>
							<img <?php if (isset($args["_lazy_load"]) && $args["_lazy_load"]) {  echo 'src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-ratio="false" data-lazy-'; } ?>src="<?php echo !empty($thumbnail_url) ?
								$thumbnail_url : $img_url; ?>" alt="<?php echo esc_attr($i_name);?>" />
						</a>
						<?php }?>
					</div>
				</div>
			<?php }?>

			<div class="catalog-item-main">

				<div class="catalog-item-header">
					<?php if ($hasprice) { ?>
					<span class="catalog-item-price">
						<?php AEI18n::format_price($item->getPrice(), $cur_item_currency); ?>
						
						<?php if ($item->getOrderLineMinQty() != null && $item->getOrderLineMinQty() > 1) { ?>
							<?php if ($item->getGuestPrice() == null || $item->getGuestPrice() == 1 ) { ?>
								/ pax
							<?php } ?>
							
							<span><?php printf( __('(Min. %s)', 'restaurant-bookings'), AEI18n::format_price($item->getMinOrderPrice(), $cur_item_currency, false) ); ?></span>
						<?php } ?>
						
						<?php if ($item->getGuestPrice() != null && $item->getGuestPrice() > 1 ) { ?>
							<span><?php printf( __('(%s pax.)', 'restaurant-bookings'), $item->getGuestPrice() );?></span>
						<?php } ?>
					</span>
					<?php } ?>
					<h5><?php echo esc_html($i_name); ?></h5>
					<span class="hr"></span>

					<?php if ( false !== $booking_url ) {?>
						<div class="catalog-item-cta">
							<a class="book-now button btn btn-primary" href="<?php echo esc_attr($booking_url); ?>">
								<?php _e('Book this set menu now', 'restaurant-bookings'); ?>
							</a>
						</div>
					<?php } ?>
				</div>
				

			<?php if ($featured_img ) { // si hay media agregamos otro item main ?>
			</div>

			<div class="catalog-item-main">

			<?php }?>

				<?php if (!empty($item->getDescription())) { ?>
					<div class="catalog-item-description">
						<?php AEI18n::_e($item->getDescription()); ?>
					</div>
				<?php }	?>

				<?php if (!empty($item->getComment())) { ?>
					<div class="catalog-item-notes">
						<?php AEI18n::_e( $item->getComment() );?>
					</div>
				<?php } ?>

				<div class="catalog-item-meta">
					<?php
						$menu_from = $menu_to = false;
						if( $item->getPeopleFrom() > 0 ) {
							$menu_from = $item->getPeopleFrom();
						}

						if( $item->getPeopleTo() > 0 ) {
							$menu_to = $item->getPeopleTo();
						}

						if ( $menu_from > 0 || $menu_to > 0 ) { ?>
						<span class="item-pax">
							<?php
							if ( $menu_from && $menu_to && $menu_from == $menu_to ) {
								printf( __('Available for %s diners.', 'restaurant-bookings'), $menu_from);
							} elseif ( $menu_from && $menu_to ) {
								printf( __('Available from desde %1$s to %2$s diners.' , 'restaurant-bookings'), $menu_from , $menu_to);
							} elseif ($menu_from) {
								printf( __('Available from %s diners.', 'restaurant-bookings'), $menu_from);
							} elseif ($menu_to) {
								printf( __('Available to %s diners.', 'restaurant-bookings'), $menu_to);
							}
							?>
						</span>
					<?php } ?>

					<?php if ( $item->getAvailability() != null ) { ?>
						<div class="item-availability">
							<?php if ( $item->getAvailability()->getOpenings() != null ) { ?>
								<div class="caledar-available">
									<span class="info-label"><?php _e('Set menu available: ', 'restaurant-bookings'); ?></span>
									<?php RBKTemplateTags::get_instance()->print_easy_ranges($item->getAvailability()->getOpenings()->getOpening()); ?>
								</div>
							<?php } ?>

							<?php if ( $item->getAvailability()->getClosures() != null ) { ?>
								<div class="caledar-unavailable">
									<span class="info-label"><?php _e('Set menu not available ', 'restaurant-bookings'); ?></span>
									<?php RBKTemplateTags::get_instance()->print_easy_ranges($item->getAvailability()->getClosures()->getClosure()); ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>

					<?php if( $item->getPrice() != null && $item->getPrice() > 0 && !$item->getTaxIncluded()) {?>
						<span class="item-tax"><?php _e('Taxes NOT included.', 'restaurant-bookings');?></span>
					<?php }?>
				</div><!-- catalog-item-meta -->

				<?php self::add_modifiers_meta($item, $cur_item_currency); ?>
				<?php // TODO: por ahora no se pueden poner los alergenos del menu... hay que meter la propiedad en el swagger self::add_allergens($item); ?>
			
			
				<?php if($is_order) { ?>
				
					<span class="wrap-icon icon-add-to-cart">
						<svg class="icon icon-add-circle-outline" aria-hidden="true" role="img"><use href="#icon-add-circle-outline" xlink:href="#icon-add-circle-outline"></use></svg>
					</span>
				
				<?php }?>
			
			</div>


		</div><!-- catalog-item -->
		<?php

		do_action("ae_sc_menu_after", $item, $args["_parent_shortcode"]);

		return $this->finish_buffer();
	}

	/**
	 * Shortcode que pinta un grupo de menus
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio
	 * 		@type int $groupid Identificador del grupo de menus
	 * 		@type string $url_back, url a donde regresa una vez
	 * 			terminada la reserva de determinado menu
	 * }
	 * @param string $content
	 */
	public function menu_group( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_menu_group_defaults", array(
			"id" => null,
			"groupid" => 0,
			"url_back" => AEUrl::get_full_url(),
			"for_order" => false,
			"btn_booking" => true,
			"_parent_shortcode" => null,
			"_data" => null,
		)) );

		/**
		 * @var \Listae\Client\Model\CatalogItemGroup $group
		 */
		$group = false;

		if ($args["_data"] == null) {
			$group = aeAPIS::get_menu_group(array(
				"restaurant_id" => $args["id"],
				"group_id" => $args["groupid"],
			));
		} else {
			$group = $args["_data"];
		}

		if (aeAPIS::is_error() || $group == null || empty($group->getMenu())) {
			return "";
		}

		$this->init_buffer();

		$is_order = $args['for_order'];
		?>
		<div id="catalog-group-<?php echo $group->getUrl()?>" class="catalog-group"<?php if ($is_order) echo " " . AECatalog::get_data_item_properties_on_group($group); ?>>
			<div class="catalog-group-title">
				<h4><?php echo esc_html( AEI18n::__( $group->getName() ) ); ?></h4>
				<hr />
			</div>
			<?php if (!empty($group->getDescription())) { ?>
				<div class="catalog-group-desc"><?php AEI18n::_e( $group->getDescription() ); ?></div>
			<?php } ?>
			<div class="catalog-group-content">
				<?php
				foreach ($group->getMenu() as $item) {
					echo $this->menu(array(
						"id" => $args["id"],
						"url_back" => $args["url_back"],
						"for_order" => $is_order,
						"btn_booking" => $args["btn_booking"],
						"_parent_shortcode" => RBKShortcodes::MENU_GROUP,
						"_data" => $item
					));
				}
				?>
			</div><!-- catalog-group-content -->
		</div><!-- catalog-group -->
		<?php

		do_action("ae_sc_menu_group_after", $group, $args["_parent_shortcode"]);

		return $this->finish_buffer();
	}

	/**
	 * Shortcode que pinta todos los menus disponibles para
	 * reserva
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio
	 * 		@type string $url_back, url a donde regresa una vez
	 * 			terminada la reserva de determinado menu
	 * }
	 * @param string $content
	 */
	public function menu_booking( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_menu_booking_defaults", array(
			"id" => null,
			"_parent_shortcode" => null,
			"url_back" => AEUrl::get_full_url(),
		)) );

		$menus = aeAPIS::get_menus(array(
			"restaurant_id" => $args["id"]
		));

		if (aeAPIS::is_error() || $menus == null) {
			return "";
		}

		$this->init_buffer();

		$bookable_menus = array();

		?><div id="ae-sc-booking-menus-<?php echo esc_attr($args["id"]); ?>"><?php
		foreach ($menus->getMenu() as $catalog) {
			foreach ($catalog->getGroup() as $group) {
				foreach ($group->getMenu() as $menu) {
					if ($menu->getBooking()) {
						$bookable_menus[] = $menu;

						echo $this->menu(array(
							"id" => $args["id"],
							"url_back" => $args["url_back"],
							"_parent_shortcode" => RBKShortcodes::MENU_BOOKING,
							"_data" => $menu
						));
					}
				}
			}
		}
		?></div><?php

		do_action("ae_sc_menu_booking_after", $bookable_menus, $args["id"], $args["_parent_shortcode"]);

		return $this->finish_buffer();
	}

	/**
	 * Shortcode que pinta todos los menus
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio
	 * 		@type string $url_back, url a donde regresa una vez
	 * 			terminada la reserva de determinado menu
	 * }
	 * @param string $content
	 */
	public function menu_all( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_all_menus_defaults", array(
			"id" => null,
			"url_back" => AEUrl::get_full_url(),
			"for_order" => false,
			"btn_booking" => true,
			"_parent_shortcode" => null,
		)) );

		$menus = aeAPIS::get_menus(array(
			"restaurant_id" => $args["id"]
		));

		if (aeAPIS::is_error() || $menus == null) {
			return "";
		}

		$this->init_buffer();

		?><div id="ae-sc-all-menus-<?php echo esc_attr($args["id"]); ?>" class="catalog catalog-menu catalog-all-menus"><div class="catalog-content"><?php
		foreach ($menus->getMenu() as $catalog) {
			foreach ($catalog->getGroup() as $group) {
				echo $this->menu_group(array(
					"id" => $args["id"],
					"url_back" => $args["url_back"],
					"for_order" => $args["for_order"],
					"btn_booking" => $args["btn_booking"],
					"_parent_shortcode" => RBKShortcodes::MENU_ALL,
					"_data" => $group
				));
			}
		}
		?></div></div><?php

		do_action("ae_sc_menu_all_after", $menus, $args["id"], $args["_parent_shortcode"]);

		return $this->finish_buffer();
	}

	/**
	 * Shortcode que pinta una carta
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio
	 * 		@type int $carteid Identificador de la carta
	 * }
	 * @param string $content
	 */
	public function carte( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_carte_defaults", array(
			"id" => null,
			"carteid" => 0,
			"url_back" => AEUrl::get_full_url(),
			"for_order" => false,
			"btn_booking" => true,
			"_lazy_load" => false,
			"_parent_shortcode" => null,
			"_data" => null,
		)) );

		/**
		 * @var \Listae\Client\Model\Catalog $carte
		 */
		$carte = false;

		if ($args["_data"] == null) {
			$carte = aeAPIS::get_carte(array(
				"restaurant_id" => $args["id"],
				"carte_id" => $args["carteid"],
			));
		} else {
			$carte = $args["_data"];
		}

		if (aeAPIS::is_error() || $carte == null) {
			return "";
		}

		$this->init_buffer();

		$is_order = $args['for_order'];
		?>
		<div id="catalog-<?php echo $carte->getUrl(); ?>" class="catalog catalog-carte"<?php if ($is_order) echo " " . AECatalog::get_data_item_properties($carte); ?>>
			<h3 class="catalog-title"><?php echo esc_html( AEI18n::__( $carte->getName() ) ); ?></h3>
			<?php if (!empty($carte->getDescription())) { ?>
				<div class="catalog-desc"><?php AEI18n::_e( $carte->getDescription() ); ?></div>
			<?php } ?>

			<div class="catalog-content">
				<?php
				foreach ($carte->getGroup() as $group) {
					echo $this->carte_group(array(
						"id" => $args["id"],
						"groupid" => $group->getUrl(),
						"for_order" => $is_order,
						"url_back" => $args["url_back"],
						"btn_booking" => $args["btn_booking"],
						"_lazy_load" => $args["_lazy_load"],
						"_parent_shortcode" => RBKShortcodes::CARTE,
						"_data" => $group
					));
				}

				if(!$carte->getTaxIncluded()) {?>
					<div class="catalog-meta"><span class="catalog-tax"><?php _e('Taxes NOT included.', 'restaurant-bookings');?></span></div>
				<?php }?>
			</div><!-- catalog-content -->
		</div><!-- catalog-carte -->
		<?php

		do_action("ae_sc_carte_after", $carte, $args["_parent_shortcode"]);

		return $this->finish_buffer();
	}

	/**
	 * Shortcode que pinta un grupo de una carta
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio
	 * 		@type int $groupid Identificador del grupo de la carta
	 * }
	 * @param string $content
	 */
	public function carte_group( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_carte_group_defaults", array(
			"id" => null,
			"groupid" => null,
			"url_back" => AEUrl::get_full_url(),
			"for_order" => false,
			"btn_booking" => true,
			"_lazy_load" => false,
			"_parent_shortcode" => null,
			"_data" => null,
		)) );

		/**
		 * @var \Listae\Client\Model\CatalogItemGroup $group
		 */
		$group = false;

		if ($args["_data"] == null) {
			$group = aeAPIS::get_carte_group(array(
				"restaurant_id" => $args["id"],
				"group_id" => $args["groupid"],
			));
		} else {
			$group = $args["_data"];
		}

		if (aeAPIS::is_error() || $group == null) {
			return "";
		}

		$this->init_buffer();

		$is_order = $args['for_order'];
		?>
		<div id="catalog-group-<?php echo $group->getUrl()?>" class="catalog-group"<?php if ($is_order) echo " " . AECatalog::get_data_item_properties_on_group($group); ?>>
			<div class="catalog-group-title">
				<h4><?php echo esc_html( AEI18n::__( $group->getName() ) ); ?></h4>
				<hr />
			</div>

			<?php if (!empty($group->getDescription())) { ?>
				<div class="catalog-group-desc"><?php AEI18n::_e( $group->getDescription() ); ?></div>
			<?php } ?>

			<div class="catalog-group-content">
				<?php
				foreach (AECatalog::get_all_items($group) as $item) {
					if ($item instanceof Listae\Client\Model\Menu) {
						echo $this->menu(array(
							"id" => $args["id"],
							"url_back" => $args["url_back"],
							"for_order" => $is_order,
							"btn_booking" => $args["btn_booking"],
							"_parent_shortcode" => RBKShortcodes::CARTE_GROUP,
							"_data" => $item
						));
					} else {
						echo $this->catalog_item(array(
							"id" => $args["id"],
							"for_order" => $is_order,
							"_parent_shortcode" => RBKShortcodes::CARTE_GROUP,
							"_data" => $item
						));
					}
				}
				?>
			</div><!-- catalog-group-content -->
		</div><!-- catalog-group -->
		<?php

		do_action("ae_sc_carte_group_after", $group, $args["_parent_shortcode"]);

		return $this->finish_buffer();
	}

	/**
	 * Shortcode que pinta todas las cartas
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio
	 * }
	 * @param string $content
	 */
	public function carte_all( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_carte_all_defaults", array(
			"id" => null,
			"for_order" => false,
			"_parent_shortcode" => null,
			"_data" => null,
		)) );

		$cartes = false;

		if ($args["_data"] == null) {
			$cartes = aeAPIS::get_cartes(array(
				"restaurant_id" => $args["id"]
			));
		} else {
			$cartes = $args["_data"];
		}

		if (aeAPIS::is_error() || $cartes == null) {
			return "";
		}

		$this->init_buffer();

		?><div id="ae-sc-all-cartes-<?php echo esc_attr($args["id"]); ?>"><?php
		foreach ($cartes->getCarte() as $carte) {
			echo $this->carte(array(
				"id" => $args["id"],
				"for_order" => $args["for_order"],
				"carteid" => $carte->getUrl(),
				"_parent_shortcode" => RBKShortcodes::CARTE_ALL,
				"_data" => $carte
			));
		}
		?></div><?php

		do_action("ae_sc_carte_all_after", $cartes, $args["id"], $args["_parent_shortcode"]);

		return $this->finish_buffer();
	}

	/**
	 * Shortcode que pinta un cupón
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio
	 * 		@type int $couponid Identificador del cupon
	 * }
	 * @param string $content
	 */
	public function coupon( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_coupon_defaults", array(
			"id" => null,
			"couponid" => null,
			"_data" => null,
		)) );

		$coupon = false;

		if ($args["_data"] == null) {
			$coupon = aeAPIS::get_coupon(array(
				"restaurant_id" => $args["id"],
				"coupon_id" => $args["couponid"]
			));
		} else {
			$coupon = $args["_data"];
		}

		if (aeAPIS::is_error() || $coupon == null) {
			return "";
		}

		$this->init_buffer();

		$coupon_url = AEUrl::get_listae_url(AE_URLS::FORM_COUPON, array(
			"slug" => $args["id"],
			"cid" => $args["couponid"],
			"origin" => AEUrl::get_full_url(),
		));

		?>
		<div id="ae-sc-coupon-<?php echo esc_attr($args["couponid"]); ?>" class="coupon-item">


			<?php //TODO: Meta información, por defecto tiene que ir antes del bono, extraer y meter filtro a before content? ?>
			<div class="coupon-meta-info">

				<div class="coupon-info">

						<div class="coupon-price">
							<span class="info-label"><?php _e('Price: ', 'restaurant-bookings'); ?></span>
							<?php AEI18n::format_price( $coupon->getPrice(), $coupon->getCurrency() ); ?>
						</div>

						<?php if ( $coupon->getMinQuantity() != null ) { ?>
							<div class="min-qty">
								<span class="info-label"><?php _e('Minimum number per order: ', 'restaurant-bookings'); ?></span>
								<?php echo $coupon->getMinQuantity(); ?>
							</div>
						<?php } ?>

						<?php if ( $coupon->getMaxQuantity() != null ) { ?>
							<div class="max-qty">
								<span class="info-label"><?php _e('Maximum number per order: ', 'restaurant-bookings'); ?></span>
								<?php echo $coupon->getMaxQuantity(); ?>
							</div>
						<?php } ?>

						<?php if ( $coupon->getTotalQuantity() != null ) { ?>
						<div class="stock">
							<span class="info-label"><?php _e('Coupons available: ', 'restaurant-bookings'); ?></span>
							<?php echo $coupon->getStockQuantity(); ?> / <?php echo $coupon->getTotalQuantity(); ?>
						</div>
						<?php } ?>

						<?php if ( $coupon->getAvailability() != null ) { ?>
							<div class="item-availability">
								<?php if ( $coupon->getAvailability()->getOpenings() != null ) { ?>
									<div class="caledar-available">
										<span class="info-label"><?php _e('Coupon available: ', 'restaurant-bookings'); ?></span>
										<?php RBKTemplateTags::get_instance()->print_easy_ranges($coupon->getAvailability()->getOpenings()->getOpening()); ?>
									</div>
								<?php } ?>

								<?php if ( $coupon->getAvailability()->getClosures() != null ) { ?>
									<div class="caledar-unavailable">
										<span class="info-label"><?php _e('Coupon not available: ', 'restaurant-bookings'); ?></span>
										<?php RBKTemplateTags::get_instance()->print_easy_ranges($coupon->getAvailability()->getClosures()->getClosure()); ?>
									</div>
								<?php } ?>
							</div>
						<?php } ?>

				</div>

				<div class="coupon-cta">

					<a href="<?php echo esc_attr($coupon_url);?>&g=false"
						class="ae-modal-form btn btn-primary btn-lg btn-block mb-2"
						title="<?php echo esc_attr__('Purchase', 'restaurant-bookings') . ' ' . esc_html(AEI18n::__( $coupon->getName() )) ; ?>">
						<?php esc_html_e('Buy Now', 'restaurant-bookings'); ?>
					</a>

					<?php if ($coupon->getAllowAsGift()) { ?>
					<a href="<?php echo esc_attr($coupon_url);?>&g=true"
						class="ae-modal-form btn btn-primary btn-lg btn-block mb-2"
						title="<?php echo esc_attr__('Give away', 'restaurant-bookings'). ' ' . esc_html(AEI18n::__( $coupon->getName() )) ; ?>">
						<?php esc_html_e('Gift Now', 'restaurant-bookings'); ?>
					</a>
					<?php } ?>
				</div>

			</div><!--.coupon-meta  -->

			<h2><?php echo esc_html(AEI18n::__( $coupon->getName() )); ?></h2>

			<?php if (!empty($coupon->getDescription())) { ?>
				<div class="coupon-description">
					<?php echo AEI18n::__( $coupon->getDescription() ); ?>
				</div>
			<?php } ?>

			<?php
				if ($coupon->getItem() != null) {
					echo $this->catalog_item(array(
						"id" => $coupon->getItem()->getUrl(),
						"for_order" => false,
						"_parent_shortcode" => RBKShortcodes::COUPON,
						"_data" => $coupon->getItem()
					));
				} else if ($coupon->getMenu() != null) {
					echo $this->menu(array(
						"id" => $coupon->getMenu()->getUrl(),
						"for_order" => false,
						"btn_booking" => false,
						"_parent_shortcode" => RBKShortcodes::COUPON,
						"_data" => $coupon->getMenu()
					));
				}

			?>

			<div class="coupon-conditions">
				<?php if (!empty($coupon->getBuyCondition())) { ?>

					<h5><?php _e('Buy conditions:', 'restaurant-bookings'); ?></h5>

					<div class="buy-conditions conditions-description">

						<?php echo AEI18n::__( $coupon->getBuyCondition() ); ?>

					</div>
				<?php } ?>

				<?php if ($coupon->getAllowAsGift()) { ?>
					<?php if (!empty($coupon->getGiftCondition())) { ?>
						<div class="gift-conditions conditions-description">
							<span class="info-label"><?php _e('Gift conditions:', 'restaurant-bookings'); ?></span>
							<?php echo AEI18n::__( $coupon->getGiftCondition() ); ?>
						</div>
					<?php } ?>
				<?php } ?>

			</div>

			<div class="coupon-cta">
				<a href="<?php echo esc_attr($coupon_url);?>&g=false"
					class="ae-modal-form btn btn-primary btn-lg btn-block mb-2"
					title="<?php echo esc_attr('Comprar', 'restaurant-bookings') . ' ' . esc_html(AEI18n::__( $coupon->getName() )) ; ?>">
					<?php esc_html_e('Buy Now', 'restaurant-bookings'); ?>
				</a>

				<?php if ($coupon->getAllowAsGift()) { ?>
				<a href="<?php echo esc_attr($coupon_url);?>&g=true"
					class="ae-modal-form btn btn-primary btn-lg btn-block mb-2"
					title="<?php echo esc_attr('Regalar', 'restaurant-bookings'). ' ' . esc_html(AEI18n::__( $coupon->getName() )) ; ?>">
					<?php esc_html_e('Gift Now', 'restaurant-bookings'); ?>
				</a>
				<?php } ?>
			</div>

		</div>
		<?php

		return $this->finish_buffer();
	}

	/**
	 * Shortcode que pinta el formulario de reservas
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio
	 * }
	 * @param string $content
	 */
	public function booking_form( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_booking_form_defaults", array(
			"id" => null,
			"height" => "1100px",
			"width" => "100%",
			"date" => isset($_GET["date"]) ? $_GET["date"] : null,
			"time" => isset($_GET["time"]) ? $_GET["time"] : null,
			"bkrs" => isset($_GET["bkrs"]) ? $_GET["bkrs"] : null,
			"menu" => isset($_GET["menu"]) ? $_GET["menu"] : null,
			"cid" => isset($_GET["cid"]) ? $_GET["cid"] : null,
			"ctk" => isset($_GET["ctk"]) ? $_GET["ctk"] : null,
			"origin" => isset($_GET["origin"]) ? $_GET["origin"] : AEUrl::get_full_url(),
			"utm_source" => isset($_GET["utm_source"]) ? $_GET["utm_source"] : null,
			"utm_medium" => isset($_GET["utm_medium"]) ? $_GET["utm_medium"] : null,
			"utm_content" => isset($_GET["utm_content"]) ? $_GET["utm_content"] : null,
			"utm_campaign" => isset($_GET["utm_campaign"]) ? $_GET["utm_campaign"] : null,
			"dining-area" => isset($_GET["da"]) ? $_GET["da"] : null,
		)) );

		$base_booking_url = AEUrl::get_listae_url(AE_URLS::FORM_BOOKING, array(
			"slug" => $args["id"],
			"origin" => $args["origin"],
		));

		$extra_args = "";

		if ($args["dining-area"] != null) {
			$extra_args .= "&da=" . urlencode($args["dining-area"]);
		}

		if ($args["date"] != null) {
			$dp = explode("/", $args["date"]);

			if (count($dp) == 3 && checkdate(intval($dp[1]), intval($dp[0]), intval($dp[2]))) {
				$extra_args .= "&date=" . urlencode($args["date"]);
			}
		}

		foreach (array("time", "bkrs", "menu", "cid", "ctk", "utm_source", "utm_medium", "utm_content", "utm_campaign") as $key) {
			if ($args[$key] != null) {
				$extra_args .= "&$key=" . urlencode($args[$key]);
			}
		}

		$this->init_buffer();

		$this->feedback_form(
			$base_booking_url . $extra_args, 
			array(
				"id" => "rbkform",
				"class" => "rbkform",
				"height"=> $args["height"],
				"width"=> $args["width"],
			),
			"rbk-booking-form",
			__("Book", 'restaurant-bookings')
		);

		return $this->finish_buffer();
	}

	public function order_form( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_order_form_defaults", array(
			"id" => null,
			"height" => "2400px",
			"width" => "100%",
			"origin" => isset($_REQUEST["origin"]) ? $_REQUEST["origin"] : AEUrl::get_full_url(),
			"utm_source" => isset($_GET["utm_source"]) ? $_GET["utm_source"] : null,
			"utm_medium" => isset($_GET["utm_medium"]) ? $_GET["utm_medium"] : null,
			"utm_content" => isset($_GET["utm_content"]) ? $_GET["utm_content"] : null,
			"utm_campaign" => isset($_GET["utm_campaign"]) ? $_GET["utm_campaign"] : null,
			"back" => isset($_POST["origin"]) ? $_POST["origin"] : AEUrl::get_full_url(),
			"rbkor_order_type" => isset($_POST["rbkor_order_type"]) ? $_POST["rbkor_order_type"] : null,
		)) );


		$base_order_url = AEUrl::get_listae_url(AE_URLS::ORDER_REDIRECT, array(
			"slug" => $args["id"],
			"back" => $args["back"],
			"origin" => $args["origin"],
			"rbkor_order_type" => (isset($_POST["rbkor_order_type"]) ? $_POST["rbkor_order_type"] : null),
			"rbkor_asap" => (isset($_POST["rbkor_asap"]) && $_POST["rbkor_asap"] == "1" ? "true" : "false"),
		));

		$this->init_buffer();
		
		$this->feedback_form(
			$base_order_url, 
			array(
				"id" => "rbkorder",
				"class" => "rbkorder",
				"height"=> $args["height"],
				"width"=> $args["width"],
			),
			"rbk-order-form",
			__("Order", 'restaurant-bookings')
		);
		
		return $this->finish_buffer();
	}

	public function order_all( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_order_all_defaults", array(
			"id" => null,
		)) );
		
		$order_cfg = aeAPIS::get_order_cfg(array(
			"restaurant_id" => $args["id"],
		));
		
		if (aeAPIS::is_error() || $order_cfg == null || ((empty($order_cfg->getDelivery()) || !$order_cfg->getDelivery()->getEnabled()) &&
				(empty($order_cfg->getTakeaway()) || !$order_cfg->getTakeaway()->getEnabled()) &&
				empty($order_cfg->getBooking()))) {
			return "";
		}
		
		return $this->get_html_catalogs($order_cfg, Array(
			"id" => $args["id"],
			"for_order" => true,
			"_parent_shortcode" => RBKShortcodes::ORDER_ALL,
		));;
	}

	private function get_html_catalogs($order_cfg, $sc_args) {
		$s = "";

		if ($order_cfg->getCartes() != null) {
			foreach ($order_cfg->getCartes()->getCarte() as $carte) {
				$s .= $this->carte(wp_parse_args($sc_args, array(
					"carteid" => $carte->getUrl(),
					"_data" => $carte,
				)));
			}
		}
		
		return $s;
	}
	
	/*
	function widget( $args, $instance ) {
		if (!isset($instance["business_id"])) {
			return;
		}
		
		wp_enqueue_script("rbk-widgets");
		
		$cache = $this->get_cache($args);
		
		if ($cache != null) {
			echo $cache;
			return;
		}
		
		$sc_args = array("id" => $instance["business_id"]);
		
		if ( isset($instance['page']) && !empty($instance['page']) ) {
			$sc_args["booking_form_url"] = get_permalink($instance['page']);
		}
		
		$title = $this->get_widget_title($instance);
		
		$sc_args["title"] = "";
		$sc_args["before_title"] = "";
		$sc_args["after_title"] = "";
		
		if (!empty($title)) {
			$sc_args["title"] = $title;
			
			if (isset($args["before_title"])) {
				$sc_args["before_title"] = $args["before_title"];
			}
			
			if (isset($args["after_title"])) {
				$sc_args["after_title"] = $args["after_title"];
			}
		}
		
		$s = $args["before_widget"];
		
		ob_start();
		
		$s .= ob_get_clean();
		
		$s .= $this->render_widget($sc_args, $instance);
		
		$s .= $args["after_widget"];
		
		$this->set_cache($args, $s);
		echo $s;
	}
	*/
	
	/**
	 * Shortcode que pinta el widget del formulario de reservas CON SLOTS
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio
	 * 		@type string $title Identificador del negocio
	 * }
	 * @param string $content
	 */
	public function booking_slot_widget( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_booking_slots_widget_defaults", array(
			"id" => null,
			"title" => __("Bookings", 'restaurant-bookings'),
			"before_title" => '<h2 class="widget-title">',
			"after_title" => '</h2>',
			"booking_form_url" => AEUrl::get_listae_url(AE_URLS::FORM_BOOKING, Array(
				"slug" => isset($atts["id"]) ? $atts["id"] : (isset($atts["_data"]) && !empty($atts["_data"]) ? $atts["_data"]->getUrl() : ""),
			)),
			"booking_form_url_no_iframe" => AEUrl::get_listae_url(AE_URLS::FORM_BOOKING, Array(
				"slug" => isset($atts["id"]) ? $atts["id"] : (isset($atts["_data"]) && !empty($atts["_data"]) ? $atts["_data"]->getUrl() : ""),
			)),
			"url_back" => isset($_REQUEST["back"]) ? $_REQUEST["back"] : AEUrl::get_full_url(),
			"_data" => null,
		)) );
		
		if ($args["_data"] == null) {
			$restaurant = aeAPIS::get_restaurant(array(
				"restaurant_id" => $args["id"]
			));
		} else {
			$restaurant = $args["_data"];
		}

		if (aeAPIS::is_error() || $restaurant == null) {
			return "";
		}
		
		$this->init_buffer();
		
		$widget_id = "rbkw" . $args["id"] . "_" . (self::$widget_index++);
		$form_id = "form_$widget_id";
		
		$title = $args["title"];
		
		if (!$title) {
			$title = sprintf(__('Bookings at %s', 'restaurant-bookings'), $restaurant->getName());
		}
		
		if ($args["before_title"]) {
			echo $args["before_title"] . esc_html($title) . $args["after_title"];
		}
		
		$url_iframe = $this->feedback_form_url_extra_args($args["booking_form_url"], True, True, False);
		$url_no_iframe = $this->feedback_form_url_extra_args($args["booking_form_url_no_iframe"], False, True, True);
		?>

		<?php // TODO: Arreglar tema de idiomas para datepicker (bucar codigos similares y fusionarlos de alguna forma) ?>
		<script>
			jQuery(document).ready(function () {
				jQuery.datepicker.regional['<?php echo AEI18n::get_cur_lang(); ?>'] = {
					closeText: '<?php _e("Close", 'restaurant-bookings'); ?>',
					prevText: '<?php _e("&#x3c;Prev", 'restaurant-bookings'); ?>',
					nextText: '<?php _e("Next&#x3e;", 'restaurant-bookings'); ?>',
					currentText: '<?php _e("Today", 'restaurant-bookings'); ?>',
					monthNames: [
						'<?php _e("January", 'restaurant-bookings'); ?>',
						'<?php _e("February", 'restaurant-bookings'); ?>',
						'<?php _e("March", 'restaurant-bookings'); ?>',
						'<?php _e("April", 'restaurant-bookings'); ?>',
						'<?php _e("May", 'restaurant-bookings'); ?>',
						'<?php _e("June", 'restaurant-bookings'); ?>',
						'<?php _e("July", 'restaurant-bookings'); ?>',
						'<?php _e("August", 'restaurant-bookings'); ?>',
						'<?php _e("September", 'restaurant-bookings'); ?>',
						'<?php _e("October", 'restaurant-bookings'); ?>',
						'<?php _e("November", 'restaurant-bookings'); ?>',
						'<?php _e("December", 'restaurant-bookings'); ?>'
					],
					monthNamesShort: [
						'<?php _ex("Jan", "Abbreviature of month January", 'restaurant-bookings'); ?>',
						'<?php _ex("Feb", "Abbreviature of month February", 'restaurant-bookings'); ?>',
						'<?php _ex("Mar", "Abbreviature of month March", 'restaurant-bookings'); ?>',
						'<?php _ex("Apr", "Abbreviature of month April", 'restaurant-bookings'); ?>',
						'<?php _ex("May", "Abbreviature of month May", 'restaurant-bookings'); ?>',
						'<?php _ex("Jun", "Abbreviature of month June", 'restaurant-bookings'); ?>',
						'<?php _ex("Jul", "Abbreviature of month July", 'restaurant-bookings'); ?>',
						'<?php _ex("Aug", "Abbreviature of month August", 'restaurant-bookings'); ?>',
						'<?php _ex("Sep", "Abbreviature of month September", 'restaurant-bookings'); ?>',
						'<?php _ex("Oct", "Abbreviature of month October", 'restaurant-bookings'); ?>',
						'<?php _ex("Nov", "Abbreviature of month November", 'restaurant-bookings'); ?>',
						'<?php _ex("Dec", "Abbreviature of month December", 'restaurant-bookings'); ?>'
					],
					dayNames: [
						'<?php _e("Sunday", 'restaurant-bookings'); ?>',
						'<?php _e("Monday", 'restaurant-bookings'); ?>',
						'<?php _e("Tuesday", 'restaurant-bookings'); ?>',
						'<?php _e("Wednesday", 'restaurant-bookings'); ?>',
						'<?php _e("Thursday", 'restaurant-bookings'); ?>',
						'<?php _e("Friday", 'restaurant-bookings'); ?>',
						'<?php _e("Saturday", 'restaurant-bookings'); ?>'
					],
					dayNamesShort: [
						'<?php _ex("Sun", "Abbreviature of weekday Sunday", 'restaurant-bookings'); ?>',
						'<?php _ex("Mon", "Abbreviature of weekday Monday", 'restaurant-bookings'); ?>',
						'<?php _ex("Tue", "Abbreviature of weekday Tuesday", 'restaurant-bookings'); ?>',
						'<?php _ex("Wed", "Abbreviature of weekday Wednesday", 'restaurant-bookings'); ?>',
						'<?php _ex("Thu", "Abbreviature of weekday Thursday", 'restaurant-bookings'); ?>',
						'<?php _ex("Fri", "Abbreviature of weekday Friday", 'restaurant-bookings'); ?>',
						'<?php _ex("Sat", "Abbreviature of weekday Saturday", 'restaurant-bookings'); ?>'
					],
					dayNamesMin: [
						'<?php _ex("Su", "Abbreviature two letters of weekday Sunday", 'restaurant-bookings'); ?>',
						'<?php _ex("Mo", "Abbreviature two letters of weekday Monday", 'restaurant-bookings'); ?>',
						'<?php _ex("Tu", "Abbreviature two letters of weekday Tuesday", 'restaurant-bookings'); ?>',
						'<?php _ex("We", "Abbreviature two letters of weekday Wednesday", 'restaurant-bookings'); ?>',
						'<?php _ex("Th", "Abbreviature two letters of weekday Thursday", 'restaurant-bookings'); ?>',
						'<?php _ex("Fr", "Abbreviature two letters of weekday Friday", 'restaurant-bookings'); ?>',
						'<?php _ex("Sa", "Abbreviature two letters of weekday Saturday", 'restaurant-bookings'); ?>'
					],
					weekHeader: "Sm", <?php // TODO: calculara cabecera de semana para jquery-ui-calendar segun locale ?>
					dateFormat: "dd/mm/yy", <?php // TODO: calcular formato segun locale?>
					firstDay: 1 <?php // TODO: calcular el primer dia de la semana segun locale ?>,
					isRTL: false,
					showMonthAfterYear: false,
					yearSuffix: ''
				};
				jQuery.datepicker.setDefaults(jQuery.datepicker.regional['<?php echo AEI18n::get_cur_lang(); ?>']);
			});
		</script>

		<?php // TODO: jlgo hacer css, ahora tiene estos estilos para que se vea en el de viavelez ^^^^^^ ?>
		<button class="button btn btn-primary btn-slot-booking" data-title="<?php echo esc_attr($title); ?>">
			<?php esc_html_e("Made a reservation", "restaurant-bookings"); ?>
		</button>
		
		
		<form id="<?php echo esc_attr($form_id); ?>" data-id="<?php echo esc_attr($args["id"]); ?>" action="<?php echo esc_attr($url_iframe); ?>" data-action-no-iframe="<?php echo esc_attr($url_no_iframe); ?>" class="booking-slots-widget-form clear">
			<div class="container-fluid">
			<div class="row">
				<div class="col-12">
				
					<div class="row form-group party_size_wrap bkslotgroupfield" style="display:none;">
						<label class="col-md-3 col-form-label col-12"><?php esc_html_e("Party size", "restaurant-bookings"); ?></label>
						<div class="col-12 col-md-9 content-wrap">
							<div class="slot-form-content-wrap"></div>
							<?php /* 
							<small class="form-text text-muted">TODO: Ayuda...</small>
							*/?>
						</div>
					</div>
					
					<div class="row form-group date_wrap bkslotgroupfield ui-dp-ae-theme" style="display:none;">
						<label class="col-md-3 col-form-label col-12"><?php esc_html_e("Select a date", "restaurant-bookings"); ?></label>
						<div class="col-12 col-md-9 content-wrap">
							<div class="slot-form-content-wrap"></div>
						</div>
					</div>
					
					<div class="row form-group dining_area_wrap bkslotgroupfield" style="display:none;">
						<label class="col-md-3 col-form-label col-12"><?php esc_html_e("Select a space", "restaurant-bookings"); ?></label>
						<div class="col-12 col-md-9 content-wrap">
							<div class="slot-form-content-wrap"></div>
						</div>
					</div>
					
					<div class="row form-group time_wrap bkslotgroupfield" style="display:none;">
						<label class="col-md-3 col-form-label col-12"><?php esc_html_e("Select a time", "restaurant-bookings"); ?></label>
						<div class="col-12 col-md-9 content-wrap">
							<div class="slot-form-content-wrap"></div>
						</div>
					</div>
					
					<input type="hidden" name="origin" value="<?php echo esc_attr(isset($_REQUEST["origin"]) ? $_REQUEST["origin"] : AEUrl::get_full_url()); ?>">
					<input type="hidden" name="back" value="<?php echo esc_attr($args["url_back"]); ?>">
					
					<input type="hidden" name="bkrs" class="bkrs" id="bkrs<?php echo esc_attr($widget_id); ?>" value="">
					<input type="hidden" name="date" class="date" id="date<?php echo esc_attr($widget_id); ?>" value="">
					<input type="hidden" name="timestamp" class="timestamp" id="timestamp<?php echo esc_attr($widget_id); ?>" value="">
					<input type="hidden" name="da" class="da" id="da<?php echo esc_attr($widget_id); ?>" value="">
					<input type="hidden" name="time" class="time" id="time<?php echo esc_attr($widget_id); ?>" value="">
				</div>
			</div>
			</div>
		</form>
		<?php 
		
		return $this->finish_buffer();
	}
	
	/**
	 * Shortcode que pinta el widget del formulario de reservas
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio
	 * 		@type string $title Identificador del negocio
	 * }
	 * @param string $content
	 */
	public function booking_widget( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_booking_widget_defaults", array(
			"id" => null,
			"title" => __("Bookings", 'restaurant-bookings'),
			"before_title" => '<h2 class="widget-title">',
			"after_title" => '</h2>',
			"btn_label" => __("Reserve now", 'restaurant-bookings'),
			"booking_form_url" => AEUrl::get_listae_url(AE_URLS::FORM_BOOKING, Array(
				"slug" => isset($atts["id"]) ? $atts["id"] : (isset($atts["_data"]) && !empty($atts["_data"]) ? $atts["_data"]->getUrl() : ""),
			)),
			"booking_form_url_no_iframe" => AEUrl::get_listae_url(AE_URLS::FORM_BOOKING, Array(
				"slug" => isset($atts["id"]) ? $atts["id"] : (isset($atts["_data"]) && !empty($atts["_data"]) ? $atts["_data"]->getUrl() : ""),
			)),
			"url_back" => isset($_REQUEST["back"]) ? $_REQUEST["back"] : AEUrl::get_full_url(),
			"_data" => null,
		)) );

		$bkcfg = false;

		if ($args["_data"] == null) {
			$bkcfg = aeAPIS::get_booking_cfg(array(
				"restaurant_id" => $args["id"]
			));
		} else {
			$bkcfg = $args["_data"];
		}

		if (aeAPIS::is_error() || $bkcfg == null || $bkcfg->getMinBookingDate() == null) {
			return "";
		}

		$this->init_buffer();

		echo $args["before_title"] . esc_html($args["title"]) . $args["after_title"];
		
		// Nuevo boton de reservas mas sencillo
		?>
		<div class="booking-button">
			<a href="<?php echo esc_attr($args["booking_form_url"]); ?>" class="button btn btn-primary" type="submit">
				<?php echo esc_html($args["btn_label"]); ?>
			</a>
		</div>
		<?php 
		
		return $this->finish_buffer();
	}

	/**
	 * Shortcode que pinta el formulario de contacto
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio
	 * }
	 * @param string $content
	 */
	public function contact_form( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_contact_form_defaults", array(
			"id" => null,
			"height" => "1200px",
			"width" => "100%",
			"origin" => isset($_GET["origin"]) ? $_GET["origin"] : AEUrl::get_full_url(),
		)) );

		$this->init_buffer();

		$contact_form_url = AEUrl::get_listae_url(AE_URLS::FORM_CONTACT, array("slug" => $args["id"], "origin" => $args["origin"]));
		
		$this->feedback_form(
			$contact_form_url, 
			array(
				"id" => "rbkcontact",
				"class" => "rbkcontact",
				"height"=> $args["height"],
				"width"=> $args["width"],
			),
			"rbk-contact-form",
			__("Contact", 'restaurant-bookings')
		);
		
		return $this->finish_buffer();
	}

	/**
	 * Shortcode que pinta el formulario de solicitud de menu
	 * para grupos
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio
	 * }
	 * @param string $content
	 */
	public function group_form( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_group_form_defaults", array(
			"id" => null,
			"height" => "1100px",
			"width" => "100%",
			"origin" => isset($_GET["origin"]) ? $_GET["origin"] : AEUrl::get_full_url(),
		)) );

		$this->init_buffer();

		$this->feedback_form( 
			AEUrl::get_listae_url(AE_URLS::FORM_GROUP, array("slug" => $args["id"], "origin" => $args["origin"])), 
			array(
				"id" => "rbkgroup",
				"class" => "rbkgroup",
				"height"=> $args["height"],
				"width"=> $args["width"],
			),
			"rbk-query-group-form",
			__("Query a menu for groups", 'restaurant-bookings')
		);

		return $this->finish_buffer();
	}

	/**
	 * Shortcode que pinta el formulario de opinar
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio
	 * }
	 * @param string $content
	 */
	public function review_form( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_review_form_defaults", array(
			"id" => null,
			"height" => "950px",
			"width" => "100%",
			"origin" => isset($_GET["origin"]) ? $_GET["origin"] : AEUrl::get_full_url(),
		)) );

		$this->init_buffer();

		$this->feedback_form(
			AEUrl::get_listae_url(AE_URLS::FORM_REVIEW, array("slug" => $args["id"], "origin" => $args["origin"])), 
			array(
				"id" => "rbkreviewform",
				"class" => "rbkreviewform",
				"height"=> $args["height"],
				"width"=> $args["width"],
			),
			"rbk-review-form",
			__("Review form", 'restaurant-bookings')
		);

		return $this->finish_buffer();
	}

	/**
	 * Shortcode que pinta las opiniones del establecimiento
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio
	 * }
	 * @param string $content
	 */
	public function reviews( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_reviews_defaults", array(
			"id" => null,
			"url_back" => get_permalink(),
			"page" => isset($_GET["aerp"]) && is_numeric($_GET["aerp"]) ? intval($_GET["aerp"]) : 1,
			"summary" => true,
			"_data" => null,
		)) );

		$restaurant = false;
		$pagination_reviews = false;
		
		if ($args["_data"] == null) {
			$restaurant = aeAPIS::get_restaurant(array("restaurant_id" => $args["id"]));
		} else {
			$restaurant = $args["_data"];
		}
		
		$url_next_reviews = "";
		$url_back = $args["url_back"];
		
		if (isset($_GET["aerp"])) {
			$url_next_reviews = AEUrl::del_params($url_back, array("aerp" => $_GET["aerp"]) );
		} else {
			$url_next_reviews = $url_back;
		}
		
		$url_previous_reviews = $url_next_reviews;
		
		$current_page = isset($args["page"]) ? intval($_GET["aerp"]) : 1;
		
		if ($current_page > 1) {
			$url_next_reviews = AEUrl::add_params( $url_next_reviews,
				array("aerp" => $current_page + 1)
			);
			
			$url_previous_reviews = AEUrl::add_params( $url_previous_reviews,
				array("aerp" => $current_page - 1)
			);
		} else {
			$url_next_reviews = AEUrl::add_params( $url_next_reviews,
				array("aerp" => 2)
			);
		}
		
		if ($current_page > 1) {
			$pagination_reviews = aeAPIS::search_restaurant_reviews(array(
				"restaurant_id" => $args["id"],
				"page" => $current_page,
			));
		} else {
			$pagination_reviews = $restaurant->getReviews();
		}

		if (aeAPIS::is_error() || $restaurant == null) {
			return "";
		}

		$this->init_buffer();
		if ($args["summary"] && null != $restaurant->getReviews() && $restaurant->getReviews()->getCount() > 1) { ?>
			<div class="reviews-summary">
				<h2><?php echo esc_html__("Reviews summary", 'restaurant-bookings'); ?></h2>
				<?php 
				echo RBKTemplateTags::get_instance()->get_html_rating_aggregate($restaurant->getReviews()->getStats()->getGeneral(), $restaurant, __("General", 'restaurant-bookings'));
				echo RBKTemplateTags::get_instance()->get_html_rating_aggregate_no_meta($restaurant->getReviews()->getStats()->getService(), __("Service", 'restaurant-bookings'));
				echo RBKTemplateTags::get_instance()->get_html_rating_aggregate_no_meta($restaurant->getReviews()->getStats()->getFood(), __("Food", 'restaurant-bookings'));
				echo RBKTemplateTags::get_instance()->get_html_rating_aggregate_no_meta($restaurant->getReviews()->getStats()->getLocal(), __("Others", 'restaurant-bookings'));
				?>
			</div>
			
			<?php  if ($restaurant->getReviews()->getCountBooking() >= 1) { ?>
				<div class="biz-feed-back-verified">
					<a class="ae-seal" href="https://listae.com/para-restaurantes/verificacion-de-opiniones/" target="_blank" title="<?php esc_html_e("Verified Reviews by Listae", 'restaurant-bookings');?>">
						<span><?php esc_html_e("Verified Reviews by Listae", 'restaurant-bookings');?></span>
					</a>
					<span><?php esc_html_e("The reviews marked with the verification stamp come from effective reservations that the restaurant is obliged to publish without the possibility of modifying them", 'restaurant-bookings');?></span>
				</div>
			<?php } ?>
		
		<?php }
		
		if (!empty($pagination_reviews) && $pagination_reviews->getCount() > 0) {
			?>
			<div id="wrap-reviews" class="wrap-reviews">
				<?php
				foreach ($pagination_reviews->getReview() as $review) {
					echo RBKTemplateTags::get_instance()->get_html_review($review, $restaurant);
				} 
				?>
			</div><!-- wrap-reviews -->
			
			<nav class="navigation post-navigarion reviews-navigation" role="navigation">
				<h2 class="screen-reader-text"><?php _e("Review navigation", 'restaurant-bookings'); ?></h2>
				<div class="nav-links">
					
					<?php if ($pagination_reviews->getStart() > 0) { ?>
					<div class="nav-previous">
						<a href="<?php echo $url_previous_reviews;  ?>" class="nav-reviews nav-reviews-prev button btn btn-primary">
							<span class="meta-text"><?php _e("Previous", 'restaurant-bookings'); ?></span> <span class="meta-nav"><i class="icons-arrow right"><span></span></i></span>
						</a>
					</div>
					<?php }
																					
					if ($pagination_reviews->getTotal() > $pagination_reviews->getStart() + $pagination_reviews->getCount()) {?>
						<div class="nav-next">
							<a href="<?php echo $url_next_reviews;  ?>" class="nav-reviews nav-reviews-next button btn btn-primary">
								<span class="meta-nav"><i class="icons-arrow left"><span></span></i></span> <span class="meta-text"><?php _e("Next", 'restaurant-bookings'); ?></span>
							</a>
						</div>
					<?php } ?>
				</div>
			</nav>
			<?php 
		} else {
			?>
			<p><?php echo esc_html__("At this time we have no reviews. If you know us, we encourage you to tell us your experience.", 'restaurant-bookings'); ?></p>
			<?php 
			echo $this->review_form(array("id" => $args["id"]));
		}
		
		return apply_filters( 'rbk_shortcode_reviews', $this->finish_buffer(), $args, $content );
	}

	/**
	 * Shortcode que pinta el detalle de apertura
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio
	 * }
	 * @param string $content
	 */
	public function opening( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_opening_defaults", array(
			"id" => null,
			"_data" => null,
		)) );

		if ($args["_data"] == null) {
			$restaurant = aeAPIS::get_restaurant(array(
				"restaurant_id" => $args["id"]
			));
		} else {
			$restaurant = $args["_data"];
		}

		if (aeAPIS::is_error() || $restaurant == null) {
			return "";
		}

		$this->init_buffer();

		$agendas_cfgs = array();

		$agendas_cfgs[] = array(
			"prefix"	=> "opening",
			"title"		=> __('Opening hours', 'restaurant-bookings'),
			"agenda" 	=> $restaurant->getAgendas()->getOpening(),
			"show_closed" => true
		);

		if ( $restaurant->getAgendas()->getService() != null && ( $restaurant->getAgendas()->getService()->getTurns() != null || !empty($restaurant->getAgendas()->getService()->getDescription())) ) {
			$agendas_cfgs[] = array(
				"prefix"	=> "service",
				"title" 	=> __('Hours of service', 'restaurant-bookings'),
				"agenda" 	=> $restaurant->getAgendas()->getService(),
				"show_closed" => false
			);
		}
		
		/*
		 * TODO: Por ahora se oculta el horario de reservas... lo dejamos
		 * comentado por si cambiamos de idea
		if ( $restaurant->getAgendas()->getBooking() != null ) {
			$agendas_cfgs[] = array(
				"prefix"	=> "bookings",
				"title" 	=> __('Bookings', 'restaurant-bookings'),
				"agenda" 	=> $restaurant->getAgendas()->getBooking(),
				"show_closed" => true
			);
		}
		*/
		
		if ( $restaurant->getAgendas()->getDelivery() != null ) {
			$agendas_cfgs[] = array(
				"prefix"	=> "delivery",
				"title" 	=> __("Delivery at home", 'restaurant-bookings'),
				"agenda" 	=> $restaurant->getAgendas()->getDelivery(),
				"show_closed" => true
			);
		}
		
		if ( $restaurant->getAgendas()->getTakeaway() != null ) {
			$agendas_cfgs[] = array(
				"prefix"	=> "takeaway",
				"title" 	=> __('Pick up', 'restaurant-bookings'),
				"agenda" 	=> $restaurant->getAgendas()->getTakeaway(),
				"show_closed" => true
			);
		}

		if (count($agendas_cfgs) > 0) {
			?><div id="ae-sc-opening-<?php echo esc_attr($restaurant->getUrl()); ?>" class="ae-sc opening-detail"><?php
				?><ul class="nav nav-tabs" id="opening-tabs" role="tablist"><?php
				$is_active = true;
				foreach ($agendas_cfgs as $agenda_cfg) {
					?>
					<li class="nav-item">
						<a class="nav-link<?php if ($is_active) { ?> active<?php } ?>" id="opening-tab-<?php echo $agenda_cfg["prefix"]; ?>" 
							data-toggle="tab" href="#opening-<?php echo $agenda_cfg["prefix"]; ?>" 
							role="tab" aria-controls="opening-<?php echo $agenda_cfg["prefix"]; ?>" 
							aria-selected="<?php if ($is_active) { ?>true<?php } ?>"><?php echo esc_html($agenda_cfg["title"]); ?></a>
					</li>
					<?php 
					
					$is_active = false;
				}
				?></ul><?php
				
				?><div class="tab-content" id="opening-tab-content"><?php
				$is_active = true;
				foreach ($agendas_cfgs as $agenda_cfg) {
					?><div class="tab-pane fade<?php if ($is_active) { ?> show active<?php } ?>" 
						id="opening-<?php echo $agenda_cfg["prefix"]; ?>" role="tabpanel" 
						aria-labelledby="opening-tab-<?php echo $agenda_cfg["prefix"]; ?>"><?php 
					echo RBKTemplateTags::get_instance()->get_agenda_html($agenda_cfg, false);
					?></div><?php
					
					$is_active = false;
				}
				?></div><?php
			?></div><?php
		}
		
		return $this->finish_buffer();
	}

	/**
	 *
	 * @param string $slug
	 * @param string $title
	 * @param \Listae\Client\Model\ClosuresAndOpenings $closures_and_openings
	 */
	private function render_closures_and_openings($slug, $title, $closures_and_openings) {
		?>
		<div class="biz-agenda agenda-<?php echo esc_attr($slug); ?>">
			<h3><?php echo esc_html($title); ?></h3>

			<?php if ( $closures_and_openings->getClosures() != null ) { ?>
				<div class="agenda-closures">
					<h4 class="info-label"><?php _e('Special closures', 'restaurant-bookings'); ?></h4>
					<?php RBKTemplateTags::get_instance()->print_easy_ranges($closures_and_openings->getClosures()->getClosure()); ?>
				</div>
			<?php } ?>

			<?php if ( $closures_and_openings->getOpenings() != null ) { ?>
				<div class="agenda-opening">
					<h4 class="info-label"><?php _e('Special openings', 'restaurant-bookings'); ?></h4>
					<?php RBKTemplateTags::get_instance()->print_easy_ranges($closures_and_openings->getOpenings()->getOpening()); ?>
				</div>
			<?php } ?>

		</div><!-- agenda-<?php echo esc_html($slug); ?> -->
		<?php
	}

	/**
	 *
	 * @param \Listae\Client\Model\GeoPosition $address
	 */
	private function html_address($address) {
		$address2 = $address->getAddress2();
		$postal_code = $address->getPostalCode();
		$country = $address->getCountry();
		$country = empty($country) ? "ES" : $country;

		?>
		<address class="biz-address" itemprop="address" itemtype="http://schema.org/PostalAddress" itemscope="">
			<span itemprop="streetAddress" class="streetAddress">
				<?php echo esc_html($address->getAddress1()); ?>
				<?php if ( !empty($address2) ) { ?>
					<?php echo '. ' . esc_html($address2); ?>
				<?php } ?>
			</span>
			<span class="addressLocality">
				<?php if ( !empty($postal_code) ) { ?>
					<span itemprop="postalCode" class="postalCode"><?php echo esc_html( $postal_code );?> </span>
				<?php } ?>
				<span itemprop="addressLocality"><?php echo esc_html($address->getTown()) . '.';?></span>
			</span>
			<?php if ($address->getRegion()) { ?>
			<span itemprop="addressRegion" content="<?php echo esc_attr($address->getRegion()); ?>" style="<?php echo $address->getTown() == $address->getRegion() ? 'display : none;' :'';?>" class="addressRegion">
				<?php echo esc_html($address->getRegion()) . '.';?>
			</span>
			<?php } ?>
			<meta itemprop="addressCountry" content="<?php echo esc_attr($country); ?>" />
		</address>
		<?php
	}

	private function html_gps($lat, $lng) {
		?>
		<p class="biz-geo <?php if ( wp_is_mobile() ) { echo 'mobile'; } ?>"  itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
			<?php
				$latDeg = AEI18n::gps_dec_to_dms($lat);
				$lngDeg = AEI18n::gps_dec_to_dms($lng);
			?>
			<span class="lat"><span class="info-label"><?php _e('Latitude:', 'restaurant-bookings'); ?></span> <?php echo $latDeg["deg"];?>° <?php echo $latDeg["min"];?>' <?php echo number_format_i18n($latDeg["sec"], 3);?>" <?php _ex('N', 'Abbreviation of North.', 'restaurant-bookings' ); ?> </span>
			<span class="long"><span class="info-label"><?php _e('Longitude:', 'restaurant-bookings'); ?></span> <?php echo $lngDeg["deg"];?>° <?php echo $lngDeg["min"];?>' <?php echo number_format_i18n($lngDeg["sec"], 3);?>" <?php _ex('W', 'Abbreviation of West.', 'restaurant-bookings'); ?> </span>
			<meta itemprop="latitude" content="<?php echo $lat; ?>" />
			<meta itemprop="longitude" content="<?php echo $lng?>" />
		</p>
		<?php
	}

	/**
	 * @param \Listae\Client\Model\GeoPosition $address
	 */
	private function get_short_address($address) {
		$shortaddress = $address->getAddress1() . ". ";
		$address2 = $address->getAddress2();

		if ( !empty($address2) ) {
			$shortaddress .= $address2 . ". ";
		}

		$shortaddress .= $address->getPostalCode() . ", ". $address->getTown() . ".";

		return $shortaddress;
	}

	/**
	 * Shortcode que pinta el mapa y la direccion
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio
	 * }
	 * @param string $content
	 */
	public function map( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_map_defaults", array(
			"id" => null,
			"height" => "320px",
			"width" => "100%",
			"script" => true,
			"embed" => false,
			"only_map" => false,
			"_data" => null,
		)) );

		if (wp_script_is( "rbk-map", 'registered' ) && $args["script"]) {
			wp_enqueue_script("rbk-map");
		}

		/**
		 * @var \Listae\Client\Model\Restaurant $restaurant
		 */
		$restaurant = false;

		if ($args["_data"] == null) {
			$restaurant = aeAPIS::get_restaurant(array(
				"restaurant_id" => $args["id"]
			));
		} else {
			$restaurant = $args["_data"];
		}

		if (aeAPIS::is_error() || $restaurant == null) {
			return "";
		}


		$this->init_buffer();

		$business_name = $restaurant->getName();
		$address = $restaurant->getMainContact()->getAddress();

		$is_gps = $address->getMap() != null;

		$lat = $is_gps ? $address->getMap()->getLatitude() : null;
		$lng = $is_gps ? $address->getMap()->getLongitude() : null;
		$zoom =  $is_gps ? $address->getMap()->getZoom() : null;

		if ($is_gps) { ?>
		<div id="ae-sc-map-<?php echo esc_attr($restaurant->getUrl()); ?>" class="ae-sc map-detail">
			<?php if (wp_script_is( "google-maps", 'registered' ) && !$args["embed"]) { ?>
				<div id="simple-map-canvas" class="map-container" style="width:<?php echo esc_attr($args["width"]); ?>;height:<?php echo esc_attr($args["height"]); ?>; " data-lat="<?php echo $lat; ?>" data-lng="<?php echo $lng; ?>" data-zoom="<?php  echo empty($zoom) ? '16' : $zoom; ?>" data-name="<?php echo esc_attr($business_name);?>" data-address="<?php echo esc_attr($this->get_short_address($address)); ?>"></div>
			<?php } else {
				$map_link = $restaurant->getMainMapLink() ? $restaurant->getMainMapLink() : "";
				
				if ($map_link == "") {
					$query_address = $address->getAddress1();
					$query_address .= ". " . $address->getPostalCode();
					$query_address .= " " . $address->getTown() . ".";
					$query_address .= " España.";
					
					$map_link = "https://www.google.com/maps/embed/v1/place?key=AIzaSyBZ2iHRK2--TDcXil9301w3CfepNfMIvsU&q=" . $query_address . "&center=" . $lat . "," . $lng;
				}
				?>
				<div class="google-map-embed map-embed">
					<iframe class="map-container" width="<?php echo preg_replace("/[^0-9]%/", "", $args["width"]); ?>" height="<?php echo preg_replace("/[^0-9]%/", "", $args["height"]); ?>" frameborder="0" style="border: 0;width:<?php echo esc_attr($args["width"]); ?>;height:<?php echo esc_attr($args["height"]); ?>;"
					src="<?php echo esc_attr($map_link); ?>" allowfullscreen>
					</iframe>
				</div>
			<?php } ?>

			<?php if (!$args["only_map"]) { ?>
				<?php $this->html_address($address); ?>
	
				<p class="get-directions">
					<a href="//maps.google.com/maps?daddr=<?php echo esc_attr($lat); ?>,<?php echo esc_attr($lng); ?>&ll=" target="_blank">
						<?php esc_html_e( 'Get directions', 'restaurant-bookings' ); ?>
					</a>
				</p>
			<?php } ?>
			
			<?php $this->html_gps($lat, $lng); ?>
		</div>

		<?php
		}

		return $this->finish_buffer();
	}

	/**
	 * Shortcode que pinta una pequeña imagen de google-maps
	 * con la localizacion del negocio
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio,
	 * 		@type bool show_map, muestra la imagen del mapa
	 * 		@type string map_url, url al mapa en detalle
	 * 		@type bool show_address, indica si muestra la direccion
	 * 		@type bool show_phones, indica si muestra el telefono principal
	 * 		@type bool show_social, indica si muestra los enlaces sociales
	 * 		@type bool show_contact_btn, indica si muestra un enlace al formulario
	 * 			de contactar
	 * 		@type string contact_url, url al foemulario de contacto
	 * 		@type bool show_group_btn, indica si mostramos un enlace al formulario
	 * 			de solicitud de menus
	 * 		@type string group_url, url al formulario de solicitud de menus
	 * 		@type int img_width, anchura de la imagen del mapa
	 * 		@type int img_height, altura de la imagen del mapa
	 * }
	 * @param string $content
	 * @return string
	 */
	public function map_widget( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_map_widget_defaults", array(
			"id" => null,
			"show_map" => true,
			"map_url" => null,
			'show_address' => false,
			'show_phones' => false,
			'show_social' => false,
			"show_contact_btn" => false,
			"contact_url" => null,
			"show_group_btn" => false,
			"group_url" => null,
			"embed" => false,
			"img_width" =>  wp_is_mobile() ? "320" : "400",
			"img_height" => wp_is_mobile() ? "180" : "225",
			"style" => 'feature:poi|visibility:off',
			"_data" => null,
		)) );

		/**
		 * @var \Listae\Client\Model\Restaurant $restaurant
		 */
		$restaurant = false;

		if ($args["_data"] == null) {
			$restaurant = aeAPIS::get_restaurant(array(
				"restaurant_id" => $args["id"]
			));
		} else {
			$restaurant = $args["_data"];
		}

		if (aeAPIS::is_error() || $restaurant == null) {
			return "";
		}

		$this->init_buffer();

		$address = $restaurant->getMainContact()->getAddress();

		$is_gps = $address->getMap() != null;

		$lat = $is_gps ? $address->getMap()->getLatitude() : null;
		$lng = $is_gps ? $address->getMap()->getLongitude() : null;
		$zoom =  $is_gps ? $address->getMap()->getZoom() : null;

		if ($is_gps && $args["show_map"]) {
			$map_link = $restaurant->getMainMapLink() ? $restaurant->getMainMapLink() : "";
			
			if (wp_script_is( "google-maps", 'registered' ) && empty($map_link)) {
				$map_url = $args["map_url"];
				if (empty($map_url)) {
					$map_url = esc_url("https://www.google.com/maps?q=" . $this->get_short_address($address));
				}

				// Filtro para poder aniadirle la clave
				$img_map = apply_filters("ae_google_maps_img_url", "//maps.google.com/maps/api/staticmap?center=$lat,$lng&zoom=$zoom&style=" . $args["style"] . "&size=" . $args["img_width"] . "x" . $args["img_height"] . "&sensor=false&markers=$lat,$lng");

				?>
				<p class="biz-image-map">
					<a href="<?php echo esc_attr($map_url); ?>" title="<? _e("See situation map", 'restaurant-bookings'); ?>">
						<img src="<?php echo esc_attr($img_map); ?>" style="width: 100%;" />
					</a>
				</p>
				<?php
			} else {
				if ($map_link == "") {
					$query_address = $address->getAddress1();
					$query_address .= ". " . $address->getPostalCode();
					$query_address .= " " . $address->getTown() . ".";
					$query_address .= " España.";
					
					$map_link = "https://www.google.com/maps/embed/v1/place?key=AIzaSyBZ2iHRK2--TDcXil9301w3CfepNfMIvsU&q=" . $query_address . "&center=" . $lat . "," . $lng;
				}
				
				?>
					<div class="google-map-embed map-embed">
						<iframe class="map-container" width="100%" height="<?php echo preg_replace("/[^0-9]%/", "", $args["img_height"]); ?>" frameborder="0" style="border: 0;width:100%;height:<?php echo esc_attr($args["img_height"]); ?>px;"
						src="<?php echo esc_attr($map_link); ?>" allowfullscreen>
						</iframe>
					</div>
				<?php
			}
		}

		if ($args["show_address"]) {
			$this->html_address($address);
		}

		if ($is_gps) {
			$this->html_gps($lat, $lng);
		}

		$phone = $restaurant->getPhone();

		if ($args["show_phones"] && !empty($phone)) {
			?>
			<p class="biz-phone">
				<span class="info-label"><?php esc_html_e("Telephone:", 'restaurant-bookings'); ?> </span>
				<span itemprop="telephone" class="telephone">
				<?php
					$bizphone = '<span>' . esc_html($phone->getInternationalFormat()) . '</span>';
					if ( wp_is_mobile() ) {
						$bizphone = '<a href="tel:' . esc_attr($phone->getInternationalFormat()) . '">' . $bizphone . '</a>';
					}
					echo $bizphone;
				?>
				</span>
			</p>
			<?php

			foreach ($restaurant->getMainContact()->getPhones()->getPhone() as $phone) {
				if ($phone->getType() != "main") {
					?>
					<p class="biz-phone biz-phones-other <?php echo esc_attr($phone->getType()); ?>">
						<span class="telephone">
						<?php
							$bizphone = '<span>' . esc_html($phone->getInternationalFormat()) . '</span>';
							if ( wp_is_mobile() ) {
								$bizphone = '<a href="tel:' . esc_attr($phone->getInternationalFormat()) . '">' . $bizphone . '</a>';
							}
							echo $bizphone;
						?>
						</span>
					</p>
					<?php
				}
			}
		}

		if ($args["show_contact_btn"] && !empty($args["contact_url"])) {
			
			?>
			<p class="biz-contact-form-link">
				<a class="button btn btn-primary<?php echo strpos($args["contact_url"], AE_URL) === 0 ? " ae-modal-form" : ""; ?>" href="<?php echo esc_attr($args["contact_url"]); ?>" title="<?php echo esc_attr(__("Contact", 'restaurant-bookings')); ?>">
					<?php echo esc_html(__("Contact", 'restaurant-bookings')); ?>
				</a>
			</p>
			<?php
		}

		if ($args["show_group_btn"] && !empty($args["group_url"])) {
			?>
			<p class="biz-query-group-form-link">
				<a class="button btn btn-primary<?php echo strpos($args["group_url"], AE_URL) === 0 ? " ae-modal-form" : ""; ?>" href="<?php echo esc_attr($args["group_url"]); ?>" title="<?php echo esc_attr(__("Request set menu for groups", 'restaurant-bookings')); ?>">
					<?php echo esc_html(__("Groups & Events", 'restaurant-bookings')); ?>
				</a>
			</p>
			<?php
		}

		$social_links = $restaurant->getSocial();

		if ($args["show_social"] && !empty($social_links)) {
			?>
			<ul class="biz-social-links">
				<?php foreach ( $social_links->getLink() as $social) { ?>
					<li><a class="button link-social link-<?php echo esc_attr($social->getType()); ?>" href="<?php echo esc_attr($social->getHref()); ?>" target="_blank"><span><?php echo esc_html(AEI18n::get_link_type_name($social->getType())); ?></span></a></li>
				<?php } ?>
			</ul>
			<?php
		}

		return $this->finish_buffer();
	}

	/**
	 * Shortcode que pinta servicios del establecimiento
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio
	 * }
	 * @param string $content
	 */
	public function services( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_services_defaults", array(
			"id" => null,
			"_data" => null,
		)) );

		/**
		 * @var \Listae\Client\Model\Restaurant $restaurant
		 */
		$restaurant = false;

		if ($args["_data"] == null) {
			$restaurant = aeAPIS::get_restaurant(array(
				"restaurant_id" => $args["id"]
			));
		} else {
			$restaurant = $args["_data"];
		}

		if (aeAPIS::is_error() || $restaurant == null) {
			return "";
		}

		$this->init_buffer();

		if ( !empty($restaurant->getCategories()) && !empty($restaurant->getCategories()->getCategory()) ) {

			$categories_ambience = $categories_goodfor = $categories_local = $categories_payment = $categories_drink = $categories_services = '';

			foreach ($restaurant->getCategories()->getCategory() as $cat) {
				$cat_id = $cat->getIdentifier();

				if ( preg_match('/^ambience\./', $cat_id) ) {
					$categories_ambience .= '<span class="tag">' . esc_html( AEI18n::__( $cat->getTitle() ) ) . '</span>';
				}

				elseif ( preg_match('/^good-for\./', $cat_id) ) {
					$categories_goodfor .= '<span class="tag">' . esc_html( AEI18n::__( $cat->getTitle() ) ) . '</span>';
				}

				elseif ( preg_match('/^local\./', $cat_id) ) {
					$categories_local .= '<span class="tag">' . esc_html( AEI18n::__( $cat->getTitle() ) ) . '</span>';
				}

				elseif ( preg_match('/^pay-method\.(restaurant-check\.|card)/', $cat_id) ) {
					$categories_payment .= '<span class="tag">' . esc_html( AEI18n::__( $cat->getTitle() ) ) . '</span>';
				}

				elseif ( preg_match('/^drinks\./', $cat_id) ) {
					$categories_drink .= '<span class="tag">' . esc_html( AEI18n::__( $cat->getTitle() ) ) . '</span>';
				}

				// Resto: /^service-type\./ y todos los  /^service\  /^service\.cuisine\./ /^service\.show\./
				elseif ( preg_match('/^service/', $cat_id) ) {
					$categories_services .= '<span class="tag">' . esc_html( AEI18n::__( $cat->getTitle() ) ) . '</span>';
				}

			}
		}
		?>
		<div class="biz-services ">
			<?php

			$other_info = '';

			if( $categories_ambience ) {
				$other_info .= '<li><span class="info-label">' . __('Ambience:', 'restaurant-bookings') . '</span> '. $categories_ambience .'</li>';
			}

			if( $categories_goodfor ) {
				$other_info .= '<li><span class="info-label">' . __('Good for:', 'restaurant-bookings') . '</span> '. $categories_goodfor .'</li>';
			}

			if( $categories_services ) {
				$other_info .= '<li><span class="info-label">' . __('Services:', 'restaurant-bookings') . '</span> '. $categories_services .'</li>';
			}

			if( $categories_local ) {
				$other_info .= '<li><span class="info-label">' . __('Facilities:', 'restaurant-bookings') . '</span> '. $categories_local .'</li>';
			}

			if( $categories_drink ) {
				$other_info .= '<li><span class="info-label">' . __('Drinks:', 'restaurant-bookings') . '</span> '. $categories_drink .'</li>';
			}

			if( $categories_payment ) {
				$other_info .= '<li><span class="info-label">' . __('Payment types:', 'restaurant-bookings') . '</span> '. $categories_payment .'</li>';
			}

			if ( $restaurant->getDiningAreas() != null) {

				$other_info .= '<li><span class="info-label">' . __('Capacity:', 'restaurant-bookings') . '</span> ';

				$other_info .=	sprintf( _n('One lounge. ', '%d lounges. ', $restaurant->getDiningAreas()->getRooms(), 'restaurant-bookings'), $restaurant->getDiningAreas()->getRooms());

					if ( $restaurant->getDiningAreas()->getTable() > 1 ) {
					$other_info .= sprintf(__('%s tables. ', 'restaurant-bookings'), $restaurant->getDiningAreas()->getTable());
					}

					if ( $restaurant->getDiningAreas()->getMaxCapacity() > 1) {
					$other_info .= sprintf( __('Until %s diners.', 'restaurant-bookings'), $restaurant->getDiningAreas()->getMaxCapacity() );
					}
				$other_info .= '</li>';

		 	}

		 	if ( !empty($restaurant->getOtherService()) ) {
				$other_info .= '<li><span class="info-label">' . __('Others:', 'restaurant-bookings'). '</span> ';
				$other_info .= AEI18n::__( $restaurant->getOtherService() );
				$other_info .= '</li>';
			 }

			 if('' != $other_info) {
			 	echo '<ul>' . $other_info . '</ul>';
			 }
			 ?>

		</div>
		<?php

		return $this->finish_buffer();
	}

	/**
	 * Shortcode que pinta las ofertas publicas del establecimiento
	 *
	 * @param array $atts {
	 * 		@type string $id Identificador del negocio
	 * }
	 * @param string $content
	 */
	public function public_offers( $atts, $content = null ) {
		$args = wp_parse_args( $atts, apply_filters("ae_sc_public_offers_defaults", array(
			"id" => null,
			"_data" => null,
		)) );

		/**
		 * @var \Listae\Client\Model\Restaurant $restaurant
		 */
		$restaurant = false;

		if ($args["_data"] == null) {
			$restaurant = aeAPIS::get_restaurant(array(
				"restaurant_id" => $args["id"]
			));
		} else {
			$restaurant = $args["_data"];
		}

		if (aeAPIS::is_error() || $restaurant == null) {
			return "";
		}

		$this->init_buffer();

		if ( !empty($restaurant->getCategories()) && !empty($restaurant->getCategories()->getCategory()) ) {

			$categories_ambience = $categories_goodfor = $categories_local = $categories_payment = $categories_drink = $categories_services = '';

			foreach ($restaurant->getCategories()->getCategory() as $cat) {
				$cat_id = $cat->getIdentifier();

				if ( preg_match('/^ambience\./', $cat_id) ) {
					$categories_ambience .= '<span class="tag">' . esc_html( AEI18n::__( $cat->getTitle() ) ) . '</span>';
				}

				elseif ( preg_match('/^good-for\./', $cat_id) ) {
					$categories_goodfor .= '<span class="tag">' . esc_html( AEI18n::__( $cat->getTitle() ) ) . '</span>';
				}

				elseif ( preg_match('/^local\./', $cat_id) ) {
					$categories_local .= '<span class="tag">' . esc_html( AEI18n::__( $cat->getTitle() ) ) . '</span>';
				}

				elseif ( preg_match('/^pay-method\.(restaurant-check\.|card)/', $cat_id) ) {
					$categories_payment .= '<span class="tag">' . esc_html( AEI18n::__( $cat->getTitle() ) ) . '</span>';
				}

				elseif ( preg_match('/^drinks\./', $cat_id) ) {
					$categories_drink .= '<span class="tag">' . esc_html( AEI18n::__( $cat->getTitle() ) ) . '</span>';
				}

				// Resto: /^service-type\./ y todos los  /^service\  /^service\.cuisine\./ /^service\.show\./
				elseif ( preg_match('/^service/', $cat_id) ) {
					$categories_services .= '<span class="tag">' . esc_html( AEI18n::__( $cat->getTitle() ) ) . '</span>';
				}

			}
		}
		?>
		<div class="biz-services ">
			<?php

			$other_info = '';

			if( $categories_ambience ) {
				$other_info .= '<li><span class="info-label">' . __('Ambience:', 'restaurant-bookings') . '</span> '. $categories_ambience .'</li>';
			}

			if( $categories_goodfor ) {
				$other_info .= '<li><span class="info-label">' . __('Good for:', 'restaurant-bookings') . '</span> '. $categories_goodfor .'</li>';
			}

			if( $categories_services ) {
				$other_info .= '<li><span class="info-label">' . __('Services:', 'restaurant-bookings') . '</span> '. $categories_services .'</li>';
			}

			if( $categories_local ) {
				$other_info .= '<li><span class="info-label">' . __('Facilities:', 'restaurant-bookings') . '</span> '. $categories_local .'</li>';
			}

			if( $categories_drink ) {
				$other_info .= '<li><span class="info-label">' . __('Drinks:', 'restaurant-bookings') . '</span> '. $categories_drink .'</li>';
			}

			if( $categories_payment ) {
				$other_info .= '<li><span class="info-label">' . __('Payment types:', 'restaurant-bookings') . '</span> '. $categories_payment .'</li>';
			}

			if ( $restaurant->getDiningAreas() != null) {

				$other_info .= '<li><span class="info-label">' . __('Capacity:', 'restaurant-bookings') . '</span> ';

				$other_info .=	sprintf( _n('One lounge. ', '%d lounges. ', $restaurant->getDiningAreas()->getRooms(), 'restaurant-bookings'), $restaurant->getDiningAreas()->getRooms());

					if ( $restaurant->getDiningAreas()->getTable() > 1 ) {
					$other_info .= sprintf(__('%s tables. ', 'restaurant-bookings'), $restaurant->getDiningAreas()->getTable());
					}

					if ( $restaurant->getDiningAreas()->getMaxCapacity() > 1) {
					$other_info .= sprintf( __('Until %s diners.', 'restaurant-bookings'), $restaurant->getDiningAreas()->getMaxCapacity() );
					}
				$other_info .= '</li>';

		 	}

		 	if ( !empty($restaurant->getOtherService()) ) {
				$other_info .= '<li><span class="info-label">' . __('Others:', 'restaurant-bookings'). '</span> ';
				$other_info .= AEI18n::__( $restaurant->getOtherService() );
				$other_info .= '</li>';
			 }

			 if('' != $other_info) {
			 	echo '<ul>' . $other_info . '</ul>';
			 }
			 ?>

		</div>
		<?php

		return $this->finish_buffer();
	}
	
	/**
	 *
	 * @param \Listae\Client\Model\CatalogItem||\Listae\Client\Model\Menu $item
	 */
	private static function add_allergens($item) {
		if ($item->getAllergens() && count($item->getAllergens()) > 0) {
			?>
			<div class="catalog-allergens-list">
				<ul class="allergens">
					<?php foreach ($item->getAllergens() as $allergen) { ?>
						<li class="allergen allergen-<?php echo $allergen->getSlug(); ?>">
							<a tabindex="0" class="allergen-info" role="button" data-toggle="popover" data-trigger="focus" title="<?php _e('Allergen and other information', 'restaurant-bookings'); ?>: " data-content="<?php echo '<b>'. $allergen->getName() . '</b>: ' . $allergen->getDescription(); ?>">
								<svg class="icon icon-allergen" aria-hidden="true" role="img"><use href="#icon-allergen-<?php echo $allergen->getSlug(); ?>" xlink:href="#icon-allergen-<?php echo $allergen->getSlug(); ?>"></use></svg>
							</a>
						</li>
						
					<?php } ?>
				</ul>
			</div>
			<?php 
		}
	}
	
	/**
	 * @param \Listae\Client\Model\CatalogItem||\Listae\Client\Model\Menu $item
	 */
	private static function add_modifiers_meta($item, $currency) {
		if (!empty($item->getModifiers())) {?>
		
			<div class="catalog-modifiers-list" >
				<ul class="modifiers" >
				<?php foreach ($item->getModifiers() as $modifier) { ?>
					<li class="modifier" >
						<?php if (!empty($modifier->getOptions())) { ?>
								<?php 
								$count_options = count($modifier->getOptions());
								$mod_info = '';
								if ($modifier->getMandatory() && !$modifier->getMultiSelect()) { 
									$mod_info = sprintf(_n( 'Required the following option', 'Mandatory to choose one of the following %s options', $count_options, 'restaurant-bookings' ), $count_options);
								} elseif ($modifier->getMandatory() && $modifier->getMultiSelect()) {
									// Ojo tiene un espacio al final para diferenciarlo de la de arriba!!!
									$mod_info = sprintf(_n( 'Required the following option ', 'Mandatory to choose at least one of the following %s options', $count_options, 'restaurant-bookings' ), $count_options);
								} elseif ($modifier->getMultiSelect()) {
									$mod_info =  sprintf(_n( 'Optionally you can select the following option', 'Optionally you can select one or more of the following %s options', $count_options, 'restaurant-bookings' ), $count_options);
								} else {
									// Ojo tiene un espacio al final para diferenciarlo de la de arriba!!!
									$mod_info =  sprintf(_n( 'Optionally you can select the following option ', 'Optionally you can select one of the following %s options', $count_options, 'restaurant-bookings' ), $count_options);
								}
								?>
							<a tabindex="0" class="mod-info" role="button" data-toggle="popover" data-trigger="focus" title="<?php _e('Item Options', 'restaurant-bookings'); ?>" data-content="<?php echo $mod_info; ?>">
								<svg class="icon icon-info" aria-hidden="true" role="img"><use href="#icon-info" xlink:href="#icon-info"></use></svg>
							</a>
							<span class="mod-name"><?php echo esc_html($modifier->getName()); ?>: </span>
							<ul class="mod-options">
								<?php foreach ($modifier->getOptions() as $option) { ?>
									<li class="option" >
										<span class="opt-name"><?php echo esc_html($option->getName()); ?></span>
										<?php 
										// Normalmente son opciones que agregan algo de precio
										$calculated_price = (!empty($option->getPrice()) ? $option->getPrice() : 0);
										$calculated_price_pre = "";
										// Los obligatorios que no son multiselect definen el precio base del producto
										// como por ejemplo los tamaños
										if (!$modifier->getMultiSelect() && !empty($item->getPrice()) && $calculated_price <> 0) {
											$calculated_price += $item->getPrice();
										} else if ($calculated_price > 0) {
											$calculated_price_pre = " +";
										}
										
										?>
										<?php if ($calculated_price <> 0) { ?>
										<span class="opt-price">
										<?php
										echo $calculated_price_pre;
										AEI18n::format_price($calculated_price, $currency);
										?>
										</span>
										<?php } ?>

										<?php // No sacamos mensaje si no tiene cantidades minimas o maximas o si las cantidades minimas o maximas son 1 ?>
										<?php if (($option->getMinQuantity() || $option->getMaxQuantity()) && ($option->getMinQuantity() != 1 || $option->getMaxQuantity() != 1)) {
											
											  $opt_info = '';
											  
												if ($option->getMinQuantity() && $option->getMaxQuantity()) {
													$opt_info = sprintf(
														__( 'Supports from %d to %d inclusions of this option.', 'restaurant-bookings' ), 
														$option->getMinQuantity(), $option->getMaxQuantity()
													);
												} elseif ($option->getMinQuantity()) {
													$opt_info = sprintf(
														__( 'Supports from %d inclusions of this option.', 'restaurant-bookings' ), 
														$option->getMinQuantity()
													);
												} else {
													$opt_info = sprintf(
														__( 'Supports up to %d inclusions of this option.', 'restaurant-bookings' ), 
														$option->getMaxQuantity()
													);
												}
											?>
											<a tabindex="0" class="opt-info" role="button" data-toggle="popover" data-trigger="focus" title="<?php _e('Option information', 'restaurant-bookings'); ?>" data-content="<?php echo $opt_info; ?>">
												<svg class="icon icon-info-outline" aria-hidden="true" role="img"><use href="#icon-info-outline" xlink:href="#icon-info-outline"></use></svg>
											</a>
										
										<?php } ?>
										
									</li>
								<?php } ?>
							</ul>
						<?php } ?>
					</li>
				<?php } ?>
				</ul>
			</div>

			<?php  // no cambiar el id de este div ?>
			<div id="mod-item-meta-<?php echo $item->getUrl(); ?>" class="catalog-modifiers-meta" style="display:none;">
				{
					<?php echo esc_html( '"modifiers"'); ?> : [
					<?php
					if (!empty($item->getModifiers())) {
						foreach ($item->getModifiers() as $i => $modifier) {
							echo esc_html( $modifier->__toString() );
							echo ($i + 1) != count($item->getModifiers()) ? "," : "";
						}
					}
					?>
					]
				}
			</div>
			<?php
		}
	}
}
