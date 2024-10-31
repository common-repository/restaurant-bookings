<?php
// Para evitar llamadas directas
defined("ABSPATH") or exit();

// constantes globales con algunas caracteristicas por defecto, se pueden
// sobreescribir en wp-config.php
defined("AE_BASE_URL") or define("AE_BASE_URL", "https://listae.com/");
defined("AE_URL") or define("AE_URL", "https://listae.com/r2console/");
defined("AE_API_URL") or define("AE_API_URL", "https://api.listae.com/");
// Define el tiempo que dura la cache en segundos (para desabilitar poner a -1)
defined("AE_CACHE_EXPIRE") or define("AE_CACHE_EXPIRE", 120);
defined("AE_CACHE_KEY_SALT") or define("AE_CACHE_KEY_SALT", 'aemenw');

require 'vendor/autoload.php';

spl_autoload_register(function ($class) {
	// project-specific namespace prefix
	$prefix = 'Listae\\Client\\';

	// base directory for the namespace prefix
	$base_dir = __DIR__ . '/lib/';

	// does the class use the namespace prefix?
	$len = strlen($prefix);
	if (strncmp($prefix, $class, $len) !== 0) {
		// no, move to the next registered autoloader
		return;
	}

	// get the relative class name
	$relative_class = substr($class, $len);

	// replace the namespace prefix with the base directory, replace namespace
	// separators with directory separators in the relative class name, append
	// with .php
	$file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

	// if the file exists, require it
	if (file_exists($file)) {
		require $file;
	}
});

/**
 * Encapsula las instancias de api (AEAPI)
 *
 * @author moz667
 *
 */
class aeAPIS {
	/**
	 * @var AEAPI[] distintas instancias de la api
	 */
	private static $api_instances = array();

	/**
	 * devuelve una instancia para un indice determianado
	 * si el indice no existe, crea una nueva AEAPI
	 * la aniade al array de instancias ($api_instances)
	 * y la devuelve
	 *
	 * @param int $index
	 * @return AEAPI
	 */
	public static function get_instance($index=0) {
		if ( !isset(self::$api_instances[$index]) ) {
			self::$api_instances[$index] = new AEAPI();
		}

		return self::$api_instances[$index];
	}
	
	/**
	 * Compruba si huvo un error, en cuyo caso
	 * establece el codigo de cabecera del error 
	 * y pinta una plantilla con un header, sidebar y footer 
	 * 
	 * @param string $template_part_slug
	 */
	public static function die_on_error($template_part_slug, $header_name=null, $sidebar=true, $sidebar_name=null, $footer_name=null) {
		if (self::is_error()) {
			// Ponemos la cabecera http con el codigo de error que deberia
			status_header(self::get_last_error()->getCode());
				
			get_header($header_name);
			get_template_part($template_part_slug, self::get_last_error()->getCode());
			get_sidebar();
			get_footer();
				
			die();
		}
	}
	
	public static function is_error() {
		return self::get_instance()->is_error();
	}
	
	public static function is_not_found() {
		return self::get_instance()->is_not_found();
	}
	
	public static function is_fobidden() {
		return self::get_instance()->is_fobidden();
	}
	
	/**
	 * Ultima exception lanzada
	 *
	 * @return \Listae\Client\ApiException
	 */
	public static function get_last_error() {
		return self::get_instance()->get_last_exception();
	}
	
	/**
	 * Búsqueda de publicaciones
	 *
	 * @param array $args {
	 * 		@type string $query_text, Texto libre de búsqueda (optional)
	 * 		@type string $location_text, Texto libre de localización geográfica (optional)
	 * 		@type string $region, Filtro de región (Provincia / Región / Estado) (optional)
	 * 		@type string $town, Filtro de población (optional)
	 * 		@type string $country, Filtro de país (optional)
	 * 		@type string $with_business, Filtra publicaciones con negocio (valor True), sin negocio (valor False), sin filtrar (valor nulo) (optional)
	 * 		@type string $site, Filtro por blog de publicación, por ejemplo el valor “ http://academia-vasca-gastronomia.listae.me/ ” sacaría solo publicaciones de la academia vasca de gastronomía (optional)
	 * 		@type string[] $categories, Filtro por categoría/s, con los distintos identificadores de categoría para filtrar (optional)
	 * 		@type string[] $tags, Filtro por etiqueta/s, con los distintas etiquetas para filtrar (optional)
	 * 		@type float $latitude, Latitúd para buscar cerca de un punto gps (optional)
	 * 		@type float $longitude, Longitúd para buscar cerca de un punto gps (optional)
	 * 		@type int $distance, Distancia en metros, radio del punto gps (optional)
	 * 		@type int $page_start Indice del primer elemento de la pagina por el cual estamos consultando, por ejemplo, si se trata de una paginación de 10 en 10 valdría; 0 para la primera página, 10 para la segunda, 20 para la tercera, (n - 1)*10 para la página n…. (optional)
	 * 		@type int $page_size Número de elementos por página (optional)
	 * 		@type string $businesses slugs (identificadores) de negocios separados por comas, por ejemplo “etxanobe,zortiko” te sacaría solo publicaciones de los restaurantes Etxanobe y Zortziko (optional)
	 * }
	 *
	 * @return \Listae\Client\Model\SearchPostFilter
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public static function search_posts($args=array()) {
		return self::get_instance()->search_posts($args);
	}
	
	/**
	 * searchRestaurantsGet
	 *
	 * Búsqueda de restaurantes
	 *
	 * @param array $args {
	 * 		@type string $n Filtro por el nombre del restaurante (optional)
	 * 		@type string $q Texto libre de búsqueda (optional)
	 * 		@type string $l Texto libre de localización geográfica (optional)
	 * 		@type string $r Filtro de región (Provincia / Región / Estado) (optional)
	 * 		@type string $t Filtro de población (optional)
	 * 		@type string $c Filtro de país (optional)
	 * 		@type string[] $cat Filtro por categoría/s, con los distintos identificadores de categoría para filtrar (optional)
	 * 		@type string[] $tag Filtro por etiqueta/s, con los distintas etiquetas para filtrar (optional)
	 * 		@type float $lat Latitúd para buscar cerca de un punto gps (optional)
	 * 		@type float $lon Longitúd para buscar cerca de un punto gps (optional)
	 * 		@type int $dst Distancia en metros, radio del punto gps (optional)
	 * 		@type int $s Indice del primer elemento de la pagina por el cual estamos consultando, por ejemplo, si se trata de una paginación de 10 en 10 valdría; 0 para la primera página, 10 para la segunda, 20 para la tercera, (n - 1)*10 para la página n…. (optional)
	 * 		@type int $sc Número de elementos por página (optional)
	 * 		@type string $hcl Filtro de categorias separados por comas ocultos al usuario que realiza la busqueda (optional)
	 * 		@type string $slugs Filtro de identificadores de negocio separados por comas (optional)
	 * }
	 * @return \Listae\Client\Model\SearchFilter
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public static function search_restaurants($args=array()) {
		return self::get_instance()->search_restaurants($args);
	}
	
	/**
	 * Busca restaurantes propios
	 *
	 * @param array $args {
	 * 		@type string $n Filtro por nombre de los restaurantes (optional)
	 * }
	 *
	 * @return \Listae\Client\Model\SearchFilter
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public static function search_my_restaurants($args=array()) {
		return self::get_instance()->search_my_restaurants($args);
	}
	
	/**
	 * Recupera los filtros de categorias de busqueda
	 *
	 * @param array $args {
	 * 		@type string $n Filtro por el nombre del restaurante (optional)
	 * 		@type string $q Texto libre de búsqueda (optional)
	 * 		@type string $l Texto libre de localización geográfica (optional)
	 * 		@type string $r Filtro de región (Provincia / Región / Estado) (optional)
	 * 		@type string $t Filtro de población (optional)
	 * 		@type string $c Filtro de país (optional)
	 * 		@type string[] $cat Filtro por categoría/s, con los distintos identificadores de categoría para filtrar (optional)
	 * 		@type string[] $tag Filtro por etiqueta/s, con los distintas etiquetas para filtrar (optional)
	 * 		@type float $lat Latitúd para buscar cerca de un punto gps (optional)
	 * 		@type float $lon Longitúd para buscar cerca de un punto gps (optional)
	 * 		@type int $dst Distancia en metros, radio del punto gps (optional)
	 * 		@type int $s Indice del primer elemento de la pagina por el cual estamos consultando, por ejemplo, si se trata de una paginación de 10 en 10 valdría; 0 para la primera página, 10 para la segunda, 20 para la tercera, (n - 1)*10 para la página n…. (optional)
	 * 		@type int $sc Número de elementos por página (optional)
	 * 		@type string $hcl Filtro de categorias separados por comas ocultos al usuario que realiza la busqueda (optional)
	 * 		@type string $slugs Filtro de identificadores de negocio separados por comas (optional)
	 * }
	 * @return \Listae\Client\Model\CategoryFilterRoot
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public static function get_category_filter_root($args=array()) {
		return self::get_instance()->get_category_filter_root($args);
	}
	
	/**
	 * Busca categorias de restaurantes
	 *
	 * @param array $args {
	 * 		@type string $n Filtro de nombre de la categoria a buscar (optional)
	 * }
	 * @return \Listae\Client\Model\BasicList
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public static function search_categories($args=array()) {
		return self::get_instance()->search_categories($args);
	}
	
	/**
	 * Recupera un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante a recuperar
	 * }
	 * @return \Listae\Client\Model\Restaurant
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public static function get_restaurant($args=array()) {
		return self::get_instance()->get_restaurant($args);
	}

	/**
	 * Recupera la configuracion de pedidos de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante a recuperar
	 * }
	 * @return \Listae\Client\Model\OrderCfg
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public static function get_order_cfg($args=array()) {
		return self::get_instance()->get_order_cfg($args);
	}
	
	/**
	 * Recupera la configuracion de takeaway de un restaurante 
	 * 
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante a recuperar
	 * }
	 * @return \Listae\Client\Model\TakeawayCfg
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public static function get_takeaway_cfg($args=array()) {
		return self::get_instance()->get_takeaway_cfg($args);
	}
	
	/**
	 * Recupera la configuracion de delivery de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante a recuperar
	 * }
	 * @return \Listae\Client\Model\DeliveryCfg
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public static function get_delivery_cfg($args=array()) {
		return self::get_instance()->get_delivery_cfg($args);
	}
	
	
	/**
	 * Recupera las cartas de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante
	 * }
	 * @return \Listae\Client\Model\Cartes
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public static function get_cartes($args=array()) {
		return self::get_instance()->get_cartes($args);
	}
	
	/**
	 * Recupera una carta de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante
	 *		@type long $carte_id, identificador de la carta a recuperar
	 * }
	 * @return \Listae\Client\Model\Catalog
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public static function get_carte($args=array()) {
		return self::get_instance()->get_carte($args);
	}
	
	/**
	 * Recupera un apartado dentro de una carta de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante
	 *		@type long $group_id, identificador del grupo de la carta a recuperar
	 * }
	 * @return \Listae\Client\Model\CatalogItemGroup
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public static function get_carte_group($args=array()) {
		return self::get_instance()->get_carte_group($args);
	}
	
	/**
	 * Recupera los menús de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante
	 * }
	 * @return \Listae\Client\Model\Menus
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public static function get_menus($args=array()) {
		return self::get_instance()->get_menus($args);
	}
	
	/**
	 * Recupera un menú de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante
	 *		@type long $menu_id, identificador del menú a recuperar
	 * }
	 * @return \Listae\Client\Model\Menu
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public static function get_menu($args=array()) {
		return self::get_instance()->get_menu($args);
	}
	
	/**
	 * Recupera un grupo de menús de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante
	 *		@type long $group_id, identificador del grupo de menús a recuperar
	 * }
	 * @return \Listae\Client\Model\CatalogItemGroup
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public static function get_menu_group($args=array()) {
		return self::get_instance()->get_menu_group($args);
	}
	
	/**
	 * Recupera los cupones de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante
	 * }
	 * @return \Listae\Client\Model\CouponList
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public static function get_coupons($args=array()) {
		return self::get_instance()->get_coupons($args);
	}
	
	/**
	 * Recupera un cupon de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante
	 * 		@type long $coupon_id, identificador del cupon
	 * }
	 * @return \Listae\Client\Model\Coupon
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public static function get_coupon($args=array()) {
		return self::get_instance()->get_coupon($args);
	}
	
	/**
	 * Busca en las opiniones de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante a recuperar
	 * 		@type string $filter, Filtro de la paginacion (opcional, por defecto "all")
	 * 			"all", todos;
	 * 			"booking", vinculados con reservas
	 * 			"other", no vinculados con reservas,
	 * 		@type string $oficial,  Filtro por la propiedad de la opinion (opcional, por defecto null)
	 * 			"true", realizado en el sitio web del negocio
	 * 			"false", realizado fuera del sitio web del negocio;
	 * 			null, sin filtrar
	 * 		@type string $page,  Numero de la pagina a obtener (opcional, por defecto 1)
	 * }
	 *
	 * @return \Listae\Client\Model\PaginationReviewList
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public static function search_restaurant_reviews($args=array()) {
		return self::get_instance()->search_restaurant_reviews($args);
	}
	
	/**
	 * Recupera la configuracion de reservas de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante a recuperar
	 * }
	 * @return \Listae\Client\Model\BookingCfg
	 */
	public static function get_booking_cfg($args=array()) {
		return self::get_instance()->get_booking_cfg($args);
	}
}

class AEAPI {
	const VERSION = "1.0.1";

	/**
	 * @var Listae\Client\Api\ListaeApi
	 */
	private $apiClient;

	/**
	 * Servicios de busqueda
	 *
	 * @var Listae\Client\Api\ListaeApi
	 */
	private $apiService = null;
	
	/**
	 * Ultima clave de cache recuperada
	 * 
	 * @var string
	 */
	private $_last_cache_key = null;
	
	/**
	 * Ultima exception lanzada
	 * 
	 * @var \Listae\Client\ApiException
	 */
	private $_last_api_exception = null;
	
	const SEARCH_POSTS				= "searchPosts";
	const SEARCH_RESTAURANTS 		= "searchRestaurants";
	const SEARCH_CATEGORIES		 	= "searchCategories";
	const SEARCH_RESTAURANT_REVIEWS = "searchRestaurantReviews";
	const SEARCH_MY_RESTAURANTS		= "searchMyRestaurants";
	
	const GET_CATEGORY_FILTER_ROOT 	= "getCategoryFilterRoot";
	const GET_RESTAURANT			= "getRestaurant";
	const GET_ORDER					= "getOrder";
	const GET_TAKEAWAY				= "getTakeaway";
	const GET_DELIVERY				= "getDelivery";
	const GET_CARTES 				= "getCartes";
	const GET_CARTE 				= "getCarte";
	const GET_CARTE_GROUP			= "getCarteGroup";
	const GET_MENUS 				= "getMenus";
	const GET_MENU			 		= "getMenu";
	const GET_MENU_GROUP			= "getMenuGroup";
	const GET_COUPONS 				= "getCoupons";
	const GET_COUPON 				= "getCoupon";

	const SIMULATE_BOOKING			= "simulateBooking";
	
	public function __construct() {
		$this->init_auth();

		$config = Listae\Client\Configuration::getDefaultConfiguration();
		$config->setApiKey('x-listae-key', get_option("ae_access_token"));
		$config->setHost(self::get_api_base_url());
		
		// $http_client = new GuzzleHttp\Client(['headers' => ['x-listae-from' => self::get_host_url()]]);
		$http_client = new GuzzleHttp\Client();
		
		$this->apiClient =  new Listae\Client\Api\ListaeApi(
			// If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
			// This is optional, `GuzzleHttp\Client` will be used as default.
			$http_client,
			$config
		);
	}

	/**
	 * devuelve una instancia para el indice determianado
	 * 
	 * @param int $index
	 * @return AEAPI
	 */
	public static function get_instance($index=0) {
		return aeAPIS::get_instance($index);
	}
	
	/**
	 * Búsqueda de publicaciones
	 *
	 * @param array $args {
	 * 		@type string $query_text, Texto libre de búsqueda (optional)
	 * 		@type string $location_text, Texto libre de localización geográfica (optional)
	 * 		@type string $region, Filtro de región (Provincia / Región / Estado) (optional)
	 * 		@type string $town, Filtro de población (optional)
	 * 		@type string $country, Filtro de país (optional)
	 * 		@type string $with_business, Filtra publicaciones con negocio (valor True), sin negocio (valor False), sin filtrar (valor nulo) (optional)
	 * 		@type string $site, Filtro por blog de publicación, por ejemplo el valor “ http://academia-vasca-gastronomia.listae.me/ ” sacaría solo publicaciones de la academia vasca de gastronomía (optional)
	 * 		@type string[] $categories, Filtro por categoría/s, con los distintos identificadores de categoría para filtrar (optional)
	 * 		@type string[] $tags, Filtro por etiqueta/s, con los distintas etiquetas para filtrar (optional)
	 * 		@type float $latitude, Latitúd para buscar cerca de un punto gps (optional)
	 * 		@type float $longitude, Longitúd para buscar cerca de un punto gps (optional)
	 * 		@type int $distance, Distancia en metros, radio del punto gps (optional)
	 * 		@type int $page_start Indice del primer elemento de la pagina por el cual estamos consultando, por ejemplo, si se trata de una paginación de 10 en 10 valdría; 0 para la primera página, 10 para la segunda, 20 para la tercera, (n - 1)*10 para la página n…. (optional)
	 * 		@type int $page_size Número de elementos por página (optional)
	 * 		@type string $businesses slugs (identificadores) de negocios separados por comas, por ejemplo “etxanobe,zortiko” te sacaría solo publicaciones de los restaurantes Etxanobe y Zortziko (optional)
	 * }
	 *
	 * @return \Listae\Client\Model\SearchPostFilter
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public function search_posts($args=array()) {
		$args = wp_parse_args( $args, apply_filters("ae_search_post_defaults", array(
			"lang"	=> AEI18n::get_cur_lang(),
			"r2q" 	=> null,
			"r2l"	=> null,
			"r2r" 	=> null,
			"r2t" 	=> null,
			"r2c" 	=> null,
			"r2b"	=> null,
			"r2blog"=> null,
			"r2cat" => array(),
			"r2tag" => array(),
			"r2lat"	=> "",
			"r2lon" => "",
			"r2dst" => "",
			"r2s"	=> 0,
			"r2sc"	=> 10,
			"r2bss" => null
		)) );

		$obj = $this->get_cache( AEAPI::SEARCH_POSTS, $args );
		
		if ($obj !== false) {
			return $obj;
		}
		
		try {
			$obj = $this->apiClient->searchPosts(
				$args["lang"],
				$args["r2q"], $args["r2l"], $args["r2r"], $args["r2t"], $args["r2c"],
				$args["r2b"], $args["r2blog"], $args["r2cat"], $args["r2tag"], $args["r2lat"],
				$args["r2lon"], $args["r2dst"], $args["r2s"], $args["r2sc"], $args["r2bss"]
			);
			
			return $this->add_cache( $obj );
		}
		catch (\Listae\Client\ApiException $e) {
			$this->_last_api_exception = $e;
		}
		catch (Exception $e) {
			throw $e;
		}

		return false;
	}

	/**
	 * searchRestaurantsGet
	 *
	 * Búsqueda de restaurantes
	 *
	 * @param array $args {
	 * 		@type string $n Filtro por el nombre del restaurante (optional)
	 * 		@type string $q Texto libre de búsqueda (optional)
	 * 		@type string $l Texto libre de localización geográfica (optional)
	 * 		@type string $r Filtro de región (Provincia / Región / Estado) (optional)
	 * 		@type string $t Filtro de población (optional)
	 * 		@type string $c Filtro de país (optional)
	 * 		@type string[] $cat Filtro por categoría/s, con los distintos identificadores de categoría para filtrar (optional)
	 * 		@type string[] $tag Filtro por etiqueta/s, con los distintas etiquetas para filtrar (optional)
	 * 		@type float $lat Latitúd para buscar cerca de un punto gps (optional)
	 * 		@type float $lon Longitúd para buscar cerca de un punto gps (optional)
	 * 		@type int $dst Distancia en metros, radio del punto gps (optional)
	 * 		@type int $s Indice del primer elemento de la pagina por el cual estamos consultando, por ejemplo, si se trata de una paginación de 10 en 10 valdría; 0 para la primera página, 10 para la segunda, 20 para la tercera, (n - 1)*10 para la página n…. (optional)
	 * 		@type int $sc Número de elementos por página (optional)
	 * 		@type string $hcl Filtro de categorias separados por comas ocultos al usuario que realiza la busqueda (optional)
	 * 		@type string $slugs Filtro de identificadores de negocio separados por comas (optional)
	 * }
	 * @return \Listae\Client\Model\SearchFilter
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public function search_restaurants($args=array()) {
		$args = wp_parse_args( $args, apply_filters("ae_search_restaurants_defaults", array(
			"lang"	=> AEI18n::get_cur_lang(),
			"n" 	=> isset($args["r2n"]) ? $args["r2n"] : null,
			"q" 	=> isset($args["r2q"]) ? $args["r2q"] : null,
			"l"		=> isset($args["r2l"]) ? $args["r2l"] : null,
			"r" 	=> isset($args["r2r"]) ? $args["r2r"] : null,
			"t" 	=> isset($args["r2t"]) ? $args["r2t"] : null,
			"c" 	=> isset($args["r2c"]) ? $args["r2c"] : null,
			"cat" 	=> isset($args["r2cat"]) ? $args["r2cat"] : array(),
			"tag" 	=> isset($args["r2tag"]) ? $args["r2tag"] : array(),
			"lat"	=> isset($args["r2lat"]) ? $args["r2lat"] : "",
			"lon" 	=> isset($args["r2lon"]) ? $args["r2lon"] : "",
			"dst" 	=> isset($args["r2dst"]) ? $args["r2dst"] : "",
			"s"		=> isset($args["r2s"]) ? $args["r2s"] : 0,
			"sc"	=> isset($args["r2sc"]) ? $args["r2sc"] : 10,
			"hcl"	=> isset($args["r2hcl"]) ? $args["r2hcl"] : null,
			"iac"	=> isset($args["r2iac"]) ? $args["r2iac"] : null,
			"slugs" => isset($args["r2bss"]) ? $args["r2bss"] : null,
			"geocodes" => isset($args["r2geo"]) ? $args["r2geo"] : null,
		)) );
		
		$obj = $this->get_cache( AEAPI::SEARCH_RESTAURANTS, $args );

		if ($obj !== false) {
			return $obj;
		}
		
		try {
			// $n=null, $q=null, $l=null, $r=null, $t=null, $c=null, $cat=null, $tag=null, $lat=null, $lon=null, $dst=null, $s=null, $sc=null, $hcl=null, $slugs=null
			$obj = $this->apiClient->searchRestaurants(
				$args["lang"],
				$args["n"], $args["q"], $args["l"], $args["r"],
				$args["t"], $args["c"], $args["cat"], $args["tag"],
				$args["lat"], $args["lon"], $args["dst"], $args["s"],
				$args["sc"], $args["hcl"], $args["iac"], $args["slugs"],
				$args["geocodes"]
			);
	
			return $this->add_cache( $obj );
		}
		catch (\Listae\Client\ApiException $e) {
			$this->_last_api_exception = $e;
		}
		catch (Exception $e) {
			throw $e;
		}
		
		return false;
	}
	
	/**
	 * Busca restaurantes propios
	 *
	 * @param array $args {
	 * 		@type string $n Filtro por nombre de los restaurantes (optional)
	 * }
	 * 
	 * @return \Listae\Client\Model\SearchFilter
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public function search_my_restaurants($args=array()) {
		$args = wp_parse_args($args, array("lang" => AEI18n::get_cur_lang(), "n" => null));
		
		$obj = $this->get_cache( AEAPI::SEARCH_MY_RESTAURANTS, $args );
		
		if ($obj !== false) {
			return $obj;
		}
		
		try {
			$obj = $this->apiClient->searchMyRestaurants( $args["lang"], $args["n"] );
			
			return $this->add_cache( $obj );
		}
		catch (\Listae\Client\ApiException $e) {
			$this->_last_api_exception = $e;
		}
		catch (Exception $e) {
			throw $e;
		}
		
		return false;
	}

	/**
	 * Recupera los filtros de categorias de busqueda
	 *
	 * @param array $args {
	 * 		@type string $n Filtro por el nombre del restaurante (optional)
	 * 		@type string $q Texto libre de búsqueda (optional)
	 * 		@type string $l Texto libre de localización geográfica (optional)
	 * 		@type string $r Filtro de región (Provincia / Región / Estado) (optional)
	 * 		@type string $t Filtro de población (optional)
	 * 		@type string $c Filtro de país (optional)
	 * 		@type string[] $cat Filtro por categoría/s, con los distintos identificadores de categoría para filtrar (optional)
	 * 		@type string[] $tag Filtro por etiqueta/s, con los distintas etiquetas para filtrar (optional)
	 * 		@type float $lat Latitúd para buscar cerca de un punto gps (optional)
	 * 		@type float $lon Longitúd para buscar cerca de un punto gps (optional)
	 * 		@type int $dst Distancia en metros, radio del punto gps (optional)
	 * 		@type int $s Indice del primer elemento de la pagina por el cual estamos consultando, por ejemplo, si se trata de una paginación de 10 en 10 valdría; 0 para la primera página, 10 para la segunda, 20 para la tercera, (n - 1)*10 para la página n…. (optional)
	 * 		@type int $sc Número de elementos por página (optional)
	 * 		@type string $hcl Filtro de categorias separados por comas ocultos al usuario que realiza la busqueda (optional)
	 * 		@type string $slugs Filtro de identificadores de negocio separados por comas (optional)
	 * }
	 * @return \Listae\Client\Model\CategoryFilterRoot
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public function get_category_filter_root($args=array()) {
		$args = wp_parse_args( $args, apply_filters("ae_get_category_filter_root_defaults", array(
			"lang"	=> AEI18n::get_cur_lang(),
			"n" 	=> isset($args["r2n"]) ? $args["r2n"] : null,
			"q" 	=> isset($args["r2q"]) ? $args["r2q"] : null,
			"l"		=> isset($args["r2l"]) ? $args["r2l"] : null,
			"r" 	=> isset($args["r2r"]) ? $args["r2r"] : null,
			"t" 	=> isset($args["r2t"]) ? $args["r2t"] : null,
			"c" 	=> isset($args["r2c"]) ? $args["r2c"] : null,
			"cat" 	=> isset($args["r2cat"]) ? $args["r2cat"] : array(),
			"tag" 	=> isset($args["r2tag"]) ? $args["r2tag"] : array(),
			"lat"	=> isset($args["r2lat"]) ? $args["r2lat"] : "",
			"lon" 	=> isset($args["r2lon"]) ? $args["r2lon"] : "",
			"dst" 	=> isset($args["r2dst"]) ? $args["r2dst"] : "",
			"s"		=> isset($args["r2s"]) ? $args["r2s"] : 0,
			"sc"	=> isset($args["r2sc"]) ? $args["r2sc"] : 10,
			"hcl"	=> isset($args["r2hcl"]) ? $args["r2hcl"] : null,
			"slugs" => isset($args["r2bss"]) ? $args["r2bss"] : null,
			// "geocodes" => isset($args["r2geo"]) ? $args["r2geo"] : null,
		)) );

		$obj = $this->get_cache( AEAPI::GET_CATEGORY_FILTER_ROOT, $args );

		if ($obj !== false) {
			return $obj;
		}
		
		try {
			$obj = $this->apiClient->getCategoryFilterRoot(
				$args["lang"],
				$args["n"], $args["q"], $args["l"], $args["r"],
				$args["t"], $args["c"], $args["cat"], $args["tag"],
				$args["lat"], $args["lon"], $args["dst"], $args["s"],
				$args["sc"], $args["hcl"], $args["slugs"], isset($args["geocodes"]) ? $args["geocodes"] : null
			);
	
			return $this->add_cache( $obj );
		}
		catch (\Listae\Client\ApiException $e) {
			$this->_last_api_exception = $e;
		}
		catch (Exception $e) {
			throw $e;
		}
		
		return false;
	}

	/**
	 * Busca categorias de restaurantes
	 *
	 * @param array $args {
	 * 		@type string $n Filtro de nombre de la categoria a buscar (optional)
	 * }
	 * @return \Listae\Client\Model\BasicList
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public function search_categories($args=array()) {
		$args = wp_parse_args( $args, apply_filters("ae_search_categories_defaults", array(
			"lang"	=> AEI18n::get_cur_lang(),
			"n" 	=> isset($args["n"]) ? $args["n"] : null
		)) );

		$obj = $this->get_cache( AEAPI::SEARCH_CATEGORIES, $args );

		if ($obj !== false) {
			return $obj;
		}
		
		try {
			$obj = $this->apiClient->searchCategories(
				$args["lang"],
				$args["n"]
			);
	
			return $this->add_cache( $obj );
		}
		catch (\Listae\Client\ApiException $e) {
			$this->_last_api_exception = $e;
		}
		catch (Exception $e) {
			throw $e;
		}
		
		return false;
	}

	/**
	 * Recupera un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante a recuperar
	 * }
	 * @return \Listae\Client\Model\Restaurant
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public function get_restaurant($args=array()) {
		$args = wp_parse_args( $args, apply_filters("ae_get_restaurant_defaults", array(
			"restaurant_id"	=> null,
			"lang"	=> AEI18n::get_cur_lang(),
		)) );

		$obj = $this->get_cache( AEAPI::GET_RESTAURANT, $args );

		if ($obj !== false) {
			return $obj;
		}
		
		try {
			$obj = $this->apiClient->getRestaurant(
				$args["restaurant_id"],
				$args["lang"]
			);
	
			return $this->add_cache( $obj );
		}
		catch (\Listae\Client\ApiException $e) {
			$this->_last_api_exception = $e;
		}
		catch (Exception $e) {
			throw $e;
		}
		
		return false;
	}
	
	/**
	 * Recupera la configuracion de pedidos de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante a recuperar
	 * }
	 * @return \Listae\Client\Model\OrderCfg
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public function get_order_cfg($args=array()) {
		$args = wp_parse_args( $args, apply_filters("ae_get_order_cfg_defaults", array(
			"restaurant_id"	=> null,
			"lang"	=> AEI18n::get_cur_lang(),
		)) );
		
		$obj = $this->get_cache( AEAPI::GET_ORDER, $args );
		
		if ($obj !== false) {
			return $obj;
		}
		
		try {
			$obj = $this->apiClient->getOrderCfg(
				$args["restaurant_id"],
				$args["lang"]
			);
			
			return $this->add_cache( $obj );
		}
		catch (\Listae\Client\ApiException $e) {
			$this->_last_api_exception = $e;
		}
		catch (Exception $e) {
			throw $e;
		}
		
		return false;
	}
	
	/**
	 * Recupera la configuracion de takeaway de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante a recuperar
	 * }
	 * @return \Listae\Client\Model\TakeawayCfg
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public function get_takeaway_cfg($args=array()) {
		$args = wp_parse_args( $args, apply_filters("ae_get_takeaway_cfg_defaults", array(
			"restaurant_id"	=> null,
			"lang"	=> AEI18n::get_cur_lang(),
		)) );
	
		$obj = $this->get_cache( AEAPI::GET_TAKEAWAY, $args );
	
		if ($obj !== false) {
			return $obj;
		}
	
		try {
			$obj = $this->apiClient->getTakeawayCfg(
				$args["restaurant_id"],
				$args["lang"]
			);
	
			return $this->add_cache( $obj );
		}
		catch (\Listae\Client\ApiException $e) {
			$this->_last_api_exception = $e;
		}
		catch (Exception $e) {
			throw $e;
		}
	
		return false;
	}
	
	/**
	 * Recupera la configuracion de delivery de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante a recuperar
	 * }
	 * @return \Listae\Client\Model\DeliveryCfg
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public function get_delivery_cfg($args=array()) {
		$args = wp_parse_args( $args, apply_filters("ae_get_delivery_cfg_defaults", array(
			"restaurant_id"	=> null,
			"lang"	=> AEI18n::get_cur_lang(),
		)) );
	
		$obj = $this->get_cache( AEAPI::GET_DELIVERY, $args );
	
		if ($obj !== false) {
			return $obj;
		}
	
		try {
			$obj = $this->apiClient->getDeliveryCfg(
				$args["restaurant_id"],
				$args["lang"]
			);
	
			return $this->add_cache( $obj );
		}
		catch (\Listae\Client\ApiException $e) {
			$this->_last_api_exception = $e;
		}
		catch (Exception $e) {
			throw $e;
		}
	
		return false;
	}

	/**
	 * Recupera las cartas de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante
	 * }
	 * @return \Listae\Client\Model\Cartes
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public function get_cartes($args=array()) {
		$args = wp_parse_args( $args, apply_filters("ae_get_cartes_defaults", array(
			"restaurant_id"	=> null,
			"lang"	=> AEI18n::get_cur_lang(),
		)) );
		
		$obj = $this->get_cache( AEAPI::GET_CARTES, $args );
	
		if ($obj !== false) {
			return $obj;
		}
	
		try {
			$obj = $this->apiClient->getCartes(
				$args["restaurant_id"],
				$args["lang"]
			);
	
			return $this->add_cache( $obj );
		}
		catch (\Listae\Client\ApiException $e) {
			$this->_last_api_exception = $e;
		}
		catch (Exception $e) {
			throw $e;
		}
	
		return false;
	}
	
	/**
	 * Recupera una carta de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante
	 *		@type long $carte_id, identificador de la carta a recuperar
	 * }
	 * @return \Listae\Client\Model\Catalog
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public function get_carte($args=array()) {
		$args = wp_parse_args( $args, apply_filters("ae_get_carte_defaults", array(
			"restaurant_id"	=> null,
			"carte_id"	=> 0,
			"lang"	=> AEI18n::get_cur_lang(),
		)) );
		
		$obj = $this->get_cache( AEAPI::GET_CARTE, $args );

		if ($obj !== false) {
			return $obj;
		}
		
		try {
			$obj = $this->apiClient->getCarte(
				$args["restaurant_id"],
				$args["carte_id"],
				$args["lang"]
			);
	
			return $this->add_cache( $obj );
		}
		catch (\Listae\Client\ApiException $e) {
			$this->_last_api_exception = $e;
		}
		catch (Exception $e) {
			throw $e;
		}
		
		return false;
	}
	
	/**
	 * Recupera un apartado dentro de una carta de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante
	 *		@type long $group_id, identificador del grupo de la carta a recuperar
	 * }
	 * @return \Listae\Client\Model\CatalogItemGroup
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public function get_carte_group($args=array()) {
		$args = wp_parse_args( $args, apply_filters("ae_get_carte_group_defaults", array(
			"restaurant_id"	=> null,
			"group_id"	=> 0,
			"lang"	=> AEI18n::get_cur_lang(),
		)) );
		
		$obj = $this->get_cache( AEAPI::GET_CARTE_GROUP, $args );
	
		if ($obj !== false) {
			return $obj;
		}
	
		try {
			$obj = $this->apiClient->getCarteGroup(
				$args["restaurant_id"],
				$args["group_id"],
				$args["lang"]
			);
	
			return $this->add_cache( $obj );
		}
		catch (\Listae\Client\ApiException $e) {
			$this->_last_api_exception = $e;
		}
		catch (Exception $e) {
			throw $e;
		}
	
		return false;
	}

	/**
	 * Recupera los menús de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante
	 * }
	 * @return \Listae\Client\Model\Menus
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public function get_menus($args=array()) {
		$args = wp_parse_args( $args, apply_filters("ae_get_menus_defaults", array(
			"restaurant_id"	=> null,
			"lang"	=> AEI18n::get_cur_lang(),
		)) );
	
		$obj = $this->get_cache( AEAPI::GET_MENUS, $args );
	
		if ($obj !== false) {
			return $obj;
		}
	
		try {
			$obj = $this->apiClient->getMenus(
				$args["restaurant_id"],
				$args["lang"]
			);
	
			return $this->add_cache( $obj );
		}
		catch (\Listae\Client\ApiException $e) {
			$this->_last_api_exception = $e;
		}
		catch (Exception $e) {
			throw $e;
		}
	
		return false;
	}
	
	/**
	 * Recupera un menú de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante
	 *		@type long $menu_id, identificador del menú a recuperar
	 * }
	 * @return \Listae\Client\Model\Menu
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public function get_menu($args=array()) {
		$args = wp_parse_args( $args, apply_filters("ae_get_menu_defaults", array(
			"restaurant_id"	=> null,
			"menu_id"	=> 0,
			"lang"	=> AEI18n::get_cur_lang(),
		)) );
		
		$obj = $this->get_cache( AEAPI::GET_MENU, $args );

		if ($obj !== false) {
			return $obj;
		}
		
		try {
			$obj = $this->apiClient->getMenu(
				$args["restaurant_id"],
				$args["menu_id"],
				$args["lang"]
			);
	
			return $this->add_cache( $obj );
		}
		catch (\Listae\Client\ApiException $e) {
			$this->_last_api_exception = $e;
		}
		catch (Exception $e) {
			throw $e;
		}
		
		return false;
	}

	/**
	 * Recupera un grupo de menús de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante
	 *		@type long $group_id, identificador del grupo de menús a recuperar
	 * }
	 * @return \Listae\Client\Model\CatalogItemGroup
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public function get_menu_group($args=array()) {
		$args = wp_parse_args( $args, apply_filters("ae_get_menu_group_defaults", array(
			"restaurant_id"	=> null,
			"group_id"	=> 0,
			"lang"	=> AEI18n::get_cur_lang(),
		)) );
		
		$obj = $this->get_cache( AEAPI::GET_MENU_GROUP, $args );
	
		if ($obj !== false) {
			return $obj;
		}
	
		try {
			$obj = $this->apiClient->getMenuGroup(
				$args["restaurant_id"],
				$args["group_id"],
				$args["lang"]
			);
	
			return $this->add_cache( $obj );
		}
		catch (\Listae\Client\ApiException $e) {
			$this->_last_api_exception = $e;
		}
		catch (Exception $e) {
			throw $e;
		}
	
		return false;
	}

	/**
	 * Recupera los cupones de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante
	 * }
	 * @return \Listae\Client\Model\CouponList
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public function get_coupons($args=array()) {
		$args = wp_parse_args( $args, apply_filters("ae_get_coupons_defaults", array(
				"restaurant_id"	=> null,
				"lang"	=> AEI18n::get_cur_lang(),
		)) );
		
		$obj = $this->get_cache( AEAPI::GET_COUPONS, $args );
		
		if ($obj !== false) {
			return $obj;
		}
		
		try {
			$obj = $this->apiClient->getCoupons(
					$args["restaurant_id"],
					$args["lang"]
					);
			
			return $this->add_cache( $obj );
		}
		catch (\Listae\Client\ApiException $e) {
			$this->_last_api_exception = $e;
		}
		catch (Exception $e) {
			throw $e;
		}
		
		return false;
	}
	
	/**
	 * Recupera un cupon de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante
	 * 		@type long $coupon_id, identificador del cupon
	 * }
	 * @return \Listae\Client\Model\Coupon
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public function get_coupon($args=array()) {
		$args = wp_parse_args( $args, apply_filters("ae_get_coupon_defaults", array(
				"restaurant_id"	=> null,
				"coupon_id"	=> 0,
				"lang"	=> AEI18n::get_cur_lang(),
		)) );
		
		$obj = $this->get_cache( AEAPI::GET_COUPON, $args );
		
		if ($obj !== false) {
			return $obj;
		}
		
		try {
			$obj = $this->apiClient->getCoupon(
					$args["restaurant_id"],
					$args["coupon_id"],
					$args["lang"]
					);
			
			return $this->add_cache( $obj );
		}
		catch (\Listae\Client\ApiException $e) {
			$this->_last_api_exception = $e;
		}
		catch (Exception $e) {
			throw $e;
		}
		
		return false;
	}
	
	/**
	 * Busca en las opiniones de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante a recuperar
	 * 		@type string $filter, Filtro de la paginacion (opcional, por defecto "all")
	 * 			"all", todos;
	 * 			"booking", vinculados con reservas
	 * 			"other", no vinculados con reservas,
	 * 		@type string $oficial,  Filtro por la propiedad de la opinion (opcional, por defecto null)
	 * 			"true", realizado en el sitio web del negocio
	 * 			"false", realizado fuera del sitio web del negocio;
	 * 			null, sin filtrar
	 * 		@type string $page,  Numero de la pagina a obtener (opcional, por defecto 1)
	 * }
	 *
	 * @return \Listae\Client\Model\PaginationReviewList
	 * @throws \Listae\Client\ApiException on non-2xx response
	 */
	public function search_restaurant_reviews($args=array()) {
		$args = wp_parse_args( $args, apply_filters("ae_search_restaurant_reviews_defaults", array(
			"restaurant_id"	=> null,
			"lang"	=> AEI18n::get_cur_lang(),
			"filter" => "all",
			"oficial" => null,
			"page" => 1
		)) );
		
		$obj = $this->get_cache( AEAPI::SEARCH_RESTAURANT_REVIEWS, $args );

		if ($obj !== false) {
			return $obj;
		}
		
		try {
			$obj = $this->apiClient->searchRestaurantReviews(
				$args["restaurant_id"], $args["lang"], $args["filter"],
				$args["oficial"], $args["page"]
			);
	
			return $this->add_cache( $obj );
		}
		catch (\Listae\Client\ApiException $e) {
			$this->_last_api_exception = $e;
		}
		catch (Exception $e) {
			throw $e;
		}
		
		return false;
	}

	/**
	 * Recupera la configuracion de reservas de un restaurante
	 *
	 * @param array $args {
	 * 		@type string $restaurant_id, slug del restaurante a recuperar
	 * }
	 * @return \Listae\Client\Model\BookingCfg
	 */
	public function get_booking_cfg($args=array()) {
		$args = wp_parse_args( $args, apply_filters("ae_get_booking_cfg_defaults", array(
			"restaurant_id"	=> null,
			"lang"	=> AEI18n::get_cur_lang(),
		)) );
		
		$obj = $this->get_cache( AEAPI::SIMULATE_BOOKING, $args );

		if ($obj !== false) {
			return $obj;
		}
		
		try {
			$obj = $this->apiClient->simulateBooking(
				$args["restaurant_id"], 
				$args["lang"]
			);
	
			return $this->add_cache( $obj );
		}
		catch (\Listae\Client\ApiException $e) {
			$this->_last_api_exception = $e;
		}
		catch (Exception $e) {
			throw $e;
		}
		
		return false;
	}
	
	public function get_exception_code() {
		return $this->_last_api_exception != null ? 
			$this->_last_api_exception->getCode() :
			"200";
	}
	
	public function is_fobidden() {
		return $this->get_exception_code() == "403";
	}
	
	public function is_not_found() {
		return $this->get_exception_code() == "404";
	}
	
	public function is_error() {
		return $this->_last_api_exception !== null;
	}
	
	/**
	 * Ultima exception lanzada
	 *
	 * @return \Listae\Client\ApiException
	 */
	public function get_last_exception() {
		return $this->_last_api_exception;
	}
	
	private function get_cache($method, $args=array()) {
		$this->_last_cache_key = $this->get_cache_key( $method, $args );
		$this->_last_api_exception = null;
		
		return apply_filters("ae-get-cache", AECache::get_cache($this->_last_cache_key), $this->_last_cache_key, $method, $args);
	}

	private function add_cache( $obj ) {
		return apply_filters("ae-add-cache", AECache::add_cache($this->_last_cache_key, $obj), $this->_last_cache_key);
	}

	private function get_cache_key( $method, $args=array() ) {
		// FIXME: ver como hacemos mejor esto...
		// para poder quitar los argumentos que no se usan por que 
		// lleva los valores por defecto deberiamos hacerlo desde donde se recupera esta variable
		// tampoco estaria mal ordenar los mismos para evitar varias caches
		// con el mismo contenido y distinto identificador
		$args = array_filter($args, function($value) {
			return $value !== '' && $value !== null && $value !== array();
		});
		
		return AE_CACHE_KEY_SALT . ":" . md5(
			get_option("ae_access_token") . ":" . 
			self::VERSION . ":" . 
			$method . "(" . http_build_query($args) . ")"
		);
	}

	/**
	 * Inicializa la configuracion de la auth
	 * con listae
	 *
	 * @param string $access_token
	 */
	private function init_auth() {
		$this->apiClient = self::get_auth_client();
	}
	
	/**
	 * Recupera el valor de la url del host del sitio
	 * actual
	 *
	 * @return string
	 */
	public static function get_host_url() {
		// Para los sites que usan domainmapping es necesario consultar
		// la funcion get_original_url
		$host_url =
			function_exists("get_original_url") &&
			get_site_option( 'dm_redirect_admin' ) == "1" ?
			get_original_url("siteurl") :
			home_url();
	
		return  $host_url;
	}
	
	public static function get_api_base_url() {
		return AE_API_URL . 'v2';
	}
	
	private static function is_cache_disabled() {
		return (AE_CACHE_EXPIRE == -1 || is_user_logged_in());
	}
	
	/**
	 * Recupera un cliente de la API con los parametros de autenticacion
	 *
	 * @param string $access_token
	 * 
	 * @return \Listae\Client\ApiClient 
	 */
	private static function get_auth_client() {
		$apiClient = new Listae\Client\ApiClient();
		$apiClient->getConfig()->setHost(self::get_api_base_url());
		$apiClient->getConfig()->setApiKey("x-listae-key", get_option("ae_access_token"));
	
		return $apiClient;
	}
}

class AECache {
	private static $intance = null;
	private static $cached = array();

	private static function get_intance() {
		if (self::$intance == null) {
			self::$intance = new AECache();
		}
		
		return self::$intance;
	}
	
	private function _get_cache($key) {
	    /* TODO: No tengo npi de porque estaba esto pero afectaba NOTABLEMENTE AL RENDIMIENTO
	     * asi que lo he comentado... si nos parece bien lo dejamos asi
		if (self::is_cache_disabled()) {
			// borramos lo que hubiera de cache
			$this->_remove_cache( $key );
			
			// FIXME: Tener en cuenta que por esto no podemos guardar
			// false en la cache... :P
			// Devolvemos el valor false para que sepamos que no hay cache
			return false;
		}
		*/
	    
		$obj = null;
	
		if (isset(self::$cached[$key])) {
			return self::$cached[$key];
		}
		
		return wp_cache_get( $key );
	}
	
	private function _add_cache( $key, $obj ) {
		self::$cached[$key] = $obj;
		
		if (self::is_cache_disabled()) return $obj;
	
		wp_cache_add( $key, $obj, '', AE_CACHE_EXPIRE);

		return $obj;
	}
	
	private function _remove_cache( $key ) {
		if (isset(self::$cached[$key])) {
			unset(self::$cached[$key]);
		}
		
		wp_cache_delete( $key );
		
		return null;
	}
	
	public static function add_cache( $key, $obj ) {
		return self::get_intance()->_add_cache($key, $obj);
	}
	
	public static function get_cache( $key ) {
		return self::get_intance()->_get_cache($key);
	}
	
	public static function remove_cache( $key ) {
		return self::get_intance()->_remove_cache($key);
	}
	
	public static function is_cache_disabled() {
		return apply_filters("ae_cache_disabled", (AE_CACHE_EXPIRE == -1 || is_user_logged_in()));
	}
}
