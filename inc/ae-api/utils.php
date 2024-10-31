<?php 

// Para evitar llamadas directas
defined("ABSPATH") or exit();

include_once 'currency-iso.php';

/**
 * Utilidades para trabajar con catalogos
 * 
 * @author moz667
 */
class AECatalog {
	/**
	 * Mezcla los items y los menus de un grupo en un solo array
	 * Primero pone los items simples antes que los menus
	 * 
	 * @param \Listae\Client\Model\CatalogItemGroup $group 
	 * @return array \Listae\Client\Model\CatalogItem|\Listae\Client\Model\Menu
	 */
	public static function get_all_items($group) {
		$items = array();
		
		if (!empty($group->getItem())) {
			$items = array_merge($items, $group->getItem());
		}
		
		if (!empty($group->getMenu())) {
			$items = array_merge($items, $group->getMenu());
		}
		
		return $items;
	}
	
	/**
	 * Busca entre los items y menus de los catalogos
	 * por un item por id
	 * 
	 * @param \Listae\Client\Model\Restaurant $restaurant
	 * @param string|int $item_id
	 * @return \Listae\Client\Model\CatalogItem|\Listae\Client\Model\Menu|NULL
	 */
	public static function find_catalog_item($restaurant, $item_id) {
		foreach ($restaurant->getCartes()->getCarte() as $carte) {
			foreach ($carte->getGroup() as $group) {
				foreach (self::get_all_items($group) as $item) {
					if ($item->getUrl() == $item_id) {
						return $item;
					}
				}
			}
		}
		
		return null;
	}
	
	/**
	 * Comprueba si un catalogo tiene algun item para
	 * takeaway
	 *
	 * @param \Listae\Client\Model\Catalog[] $catalogs
	 * @return boolean
	 */
	private static function match_takeaway_item_on_catalogs($catalogs) {
		foreach ($catalogs as $catalog) {
			if (self::match_takeaway_item($catalog)) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Comprueba si un catalogo tiene algun item para
	 * delivery
	 *
	 * @param \Listae\Client\Model\Catalog[] $catalogs
	 * @return boolean
	 */
	private static function match_delivery_item_on_catalogs($catalogs) {
		foreach ($catalogs as $catalog) {
			if (self::match_delivery_item($catalog)) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Comprueba si un catalogo tiene algun item para
	 * booking
	 *
	 * @param \Listae\Client\Model\Catalog[] $catalogs
	 * @return boolean
	 */
	private static function match_booking_item_on_catalogs($catalogs) {
		foreach ($catalogs as $catalog) {
			if (self::match_booking_item($catalog)) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Comprueba si un catalogo tiene algun item para
	 * takeaway
	 *
	 * @param \Listae\Client\Model\Catalog $catalog
	 * @return boolean
	 */
	private static function match_takeaway_item($catalog) {
		foreach ($catalog->getGroup() as $group) {
			if (self::match_takeaway_item_on_group($group)) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Comprueba si un catalogo tiene algun item para
	 * delivery
	 * 
	 * @param \Listae\Client\Model\Catalog $catalog
	 * @return boolean
	 */
	private static function match_delivery_item($catalog) {
		foreach ($catalog->getGroup() as $group) {
			if (self::match_delivery_item_on_group($group)) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Comprueba si un catalogo tiene algun item para
	 * booking
	 *
	 * @param \Listae\Client\Model\Catalog $catalog
	 * @return boolean
	 */
	public static function match_booking_item($catalog) {
		foreach ($catalog->getGroup() as $group) {
			if (self::match_booking_item_on_group($group)) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Comprueba si un grupo tiene algun item para
	 * takeaway
	 *
	 * @param \Listae\Client\Model\CatalogItemGroup $group
	 * @return boolean
	 */
	private static function match_takeaway_item_on_group($group) {
		if (!empty($group->getItem()) || !empty($group->getMenu())) {
			foreach(AECatalog::get_all_items($group) as $item) {
				if ($item->getTakeaway()) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Comprueba si un grupo tiene algun item para
	 * delivery
	 *
	 * @param \Listae\Client\Model\CatalogItemGroup $group
	 * @return boolean
	 */
	private static function match_delivery_item_on_group($group) {
		if (!empty($group->getItem()) || !empty($group->getMenu())) {
			foreach(AECatalog::get_all_items($group) as $item) {
				if ($item->getDelivery()) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Comprueba si un grupo tiene algun item para
	 * booking
	 *
	 * @param \Listae\Client\Model\CatalogItemGroup $group
	 * @return boolean
	 */
	private static function match_booking_item_on_group($group) {
		if (!empty($group->getItem()) || !empty($group->getMenu())) {
			foreach(AECatalog::get_all_items($group) as $item) {
				if ($item->getBooking()) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	public static function get_data_item_properties($catalog) {
		return self::_get_data_item_properties(
			self::match_delivery_item($catalog),
			self::match_takeaway_item($catalog),
			self::match_booking_item($catalog)
		);
	}
	
	public static function get_data_item_properties_on_catalogs($catalogs) {
		return self::_get_data_item_properties(
			self::match_delivery_item_on_catalogs($catalogs),
			self::match_takeaway_item_on_catalogs($catalogs),
			self::match_booking_item_on_catalogs($catalogs)
		);
	}
	
	public static function get_data_item_properties_on_group($group) {
		return self::_get_data_item_properties(
			self::match_delivery_item_on_group($group),
			self::match_takeaway_item_on_group($group),
			self::match_booking_item_on_group($group)
		);
	}
	
	public static function get_data_item_properties_on_item($item) {
		return ' data-id="' . esc_attr($item->getUrl()) . '" ' . 
			'data-name="' . esc_attr(AEI18n::__($item->getName())) . '" ' .
			'data-price="' . esc_attr($item->getPrice()) . '" ' .
			'data-currency="' . esc_attr($item->getCurrency()) . '" ' .
			'data-order-line-min-qty="' . esc_attr($item->getOrderLineMinQty()) . '" ' .
			'data-order-line-max-qty="' . esc_attr($item->getOrderLineMaxQty()) . '" ' .
			'data-min-order-price="' . esc_attr($item->getMinOrderPrice()) . '" ' .
			self::_get_data_item_properties(
			$item->getDelivery(),
			$item->getTakeaway(),
			$item->getBooking()
		);
	}
	
	private static function _get_data_item_properties($delivery=false, $takeaway=false, $booking=false) {
		return ' data-order="1" data-delivery="' . ($delivery ? '1' : '0'). '" ' .
				'data-takeaway="' . ($takeaway ? '1' : '0') . '" ' .
				'data-booking="' . ($booking ? '1' : '0') . '"';
	}
}

/**
 * Utilidades para trabajar con tema de idiomas e 
 * internacionalizacion, como por ejemplo,
 * con la fea clase \Listae\Client\Model\Text
 * @author moz667
 */
class AEI18n {
	public static function get_decimal_separator() {
		global $wp_locale;
		return $wp_locale->number_format['decimal_point'];
	}

	public static function get_thousand_separator() {
		global $wp_locale;
		return $wp_locale->number_format['thousands_sep'];
	}
	
	public static function get_countries() {
		return Array(
			'AD' => __('Andorra', 'restaurant-bookings'),
			'AE' => __('United Arab Emirates', 'restaurant-bookings'),
			'AF' => __('Afghanistan', 'restaurant-bookings'),
			'AG' => __('Antigua and Barbuda', 'restaurant-bookings'),
			'AI' => __('Anguilla', 'restaurant-bookings'),
			'AL' => __('Albania', 'restaurant-bookings'),
			'AM' => __('Armenia', 'restaurant-bookings'),
			'AO' => __('Angola', 'restaurant-bookings'),
			'AQ' => __('Antarctica', 'restaurant-bookings'),
			'AR' => __('Argentina', 'restaurant-bookings'),
			'AS' => __('American Samoa', 'restaurant-bookings'),
			'AT' => __('Austria', 'restaurant-bookings'),
			'AU' => __('Australia', 'restaurant-bookings'),
			'AW' => __('Aruba', 'restaurant-bookings'),
			'AX' => __('Åland Islands', 'restaurant-bookings'),
			'AZ' => __('Azerbaijan', 'restaurant-bookings'),
			'BA' => __('Bosnia and Herzegovina', 'restaurant-bookings'),
			'BB' => __('Barbados', 'restaurant-bookings'),
			'BD' => __('Bangladesh', 'restaurant-bookings'),
			'BE' => __('Belgium', 'restaurant-bookings'),
			'BF' => __('Burkina Faso', 'restaurant-bookings'),
			'BG' => __('Bulgaria', 'restaurant-bookings'),
			'BH' => __('Bahrain', 'restaurant-bookings'),
			'BI' => __('Burundi', 'restaurant-bookings'),
			'BJ' => __('Benin', 'restaurant-bookings'),
			'BL' => __('Saint Barthélemy', 'restaurant-bookings'),
			'BM' => __('Bermuda', 'restaurant-bookings'),
			'BN' => __('Brunei Darussalam', 'restaurant-bookings'),
			'BO' => __('Bolivia (Plurinational State of)', 'restaurant-bookings'),
			'BQ' => __('Bonaire, Sint Eustatius and Saba', 'restaurant-bookings'),
			'BR' => __('Brazil', 'restaurant-bookings'),
			'BS' => __('Bahamas', 'restaurant-bookings'),
			'BT' => __('Bhutan', 'restaurant-bookings'),
			'BV' => __('Bouvet Island', 'restaurant-bookings'),
			'BW' => __('Botswana', 'restaurant-bookings'),
			'BY' => __('Belarus', 'restaurant-bookings'),
			'BZ' => __('Belize', 'restaurant-bookings'),
			'CA' => __('Canada', 'restaurant-bookings'),
			'CC' => __('Cocos (Keeling) Islands', 'restaurant-bookings'),
			'CD' => __('Congo, Democratic Republic of the', 'restaurant-bookings'),
			'CF' => __('Central African Republic', 'restaurant-bookings'),
			'CG' => __('Congo', 'restaurant-bookings'),
			'CH' => __('Switzerland', 'restaurant-bookings'),
			'CI' => __('Côte d\'Ivoire', 'restaurant-bookings'),
			'CK' => __('Cook Islands', 'restaurant-bookings'),
			'CL' => __('Chile', 'restaurant-bookings'),
			'CM' => __('Cameroon', 'restaurant-bookings'),
			'CN' => __('China', 'restaurant-bookings'),
			'CO' => __('Colombia', 'restaurant-bookings'),
			'CR' => __('Costa Rica', 'restaurant-bookings'),
			'CU' => __('Cuba', 'restaurant-bookings'),
			'CV' => __('Cabo Verde', 'restaurant-bookings'),
			'CW' => __('Curaçao', 'restaurant-bookings'),
			'CX' => __('Christmas Island', 'restaurant-bookings'),
			'CY' => __('Cyprus', 'restaurant-bookings'),
			'CZ' => __('Czechia', 'restaurant-bookings'),
			'DE' => __('Germany', 'restaurant-bookings'),
			'DJ' => __('Djibouti', 'restaurant-bookings'),
			'DK' => __('Denmark', 'restaurant-bookings'),
			'DM' => __('Dominica', 'restaurant-bookings'),
			'DO' => __('Dominican Republic', 'restaurant-bookings'),
			'DZ' => __('Algeria', 'restaurant-bookings'),
			'EC' => __('Ecuador', 'restaurant-bookings'),
			'EE' => __('Estonia', 'restaurant-bookings'),
			'EG' => __('Egypt', 'restaurant-bookings'),
			'EH' => __('Western Sahara', 'restaurant-bookings'),
			'ER' => __('Eritrea', 'restaurant-bookings'),
			'ES' => __('Spain', 'restaurant-bookings'),
			'ET' => __('Ethiopia', 'restaurant-bookings'),
			'FI' => __('Finland', 'restaurant-bookings'),
			'FJ' => __('Fiji', 'restaurant-bookings'),
			'FK' => __('Falkland Islands (Malvinas)', 'restaurant-bookings'),
			'FM' => __('Micronesia (Federated States of)', 'restaurant-bookings'),
			'FO' => __('Faroe Islands', 'restaurant-bookings'),
			'FR' => __('France', 'restaurant-bookings'),
			'GA' => __('Gabon', 'restaurant-bookings'),
			'GB' => __('United Kingdom of Great Britain and Northern Ireland', 'restaurant-bookings'),
			'GD' => __('Grenada', 'restaurant-bookings'),
			'GE' => __('Georgia', 'restaurant-bookings'),
			'GF' => __('French Guiana', 'restaurant-bookings'),
			'GG' => __('Guernsey', 'restaurant-bookings'),
			'GH' => __('Ghana', 'restaurant-bookings'),
			'GI' => __('Gibraltar', 'restaurant-bookings'),
			'GL' => __('Greenland', 'restaurant-bookings'),
			'GM' => __('Gambia', 'restaurant-bookings'),
			'GN' => __('Guinea', 'restaurant-bookings'),
			'GP' => __('Guadeloupe', 'restaurant-bookings'),
			'GQ' => __('Equatorial Guinea', 'restaurant-bookings'),
			'GR' => __('Greece', 'restaurant-bookings'),
			'GS' => __('South Georgia and the South Sandwich Islands', 'restaurant-bookings'),
			'GT' => __('Guatemala', 'restaurant-bookings'),
			'GU' => __('Guam', 'restaurant-bookings'),
			'GW' => __('Guinea-Bissau', 'restaurant-bookings'),
			'GY' => __('Guyana', 'restaurant-bookings'),
			'HK' => __('Hong Kong', 'restaurant-bookings'),
			'HM' => __('Heard Island and McDonald Islands', 'restaurant-bookings'),
			'HN' => __('Honduras', 'restaurant-bookings'),
			'HR' => __('Croatia', 'restaurant-bookings'),
			'HT' => __('Haiti', 'restaurant-bookings'),
			'HU' => __('Hungary', 'restaurant-bookings'),
			'ID' => __('Indonesia', 'restaurant-bookings'),
			'IE' => __('Ireland', 'restaurant-bookings'),
			'IL' => __('Israel', 'restaurant-bookings'),
			'IM' => __('Isle of Man', 'restaurant-bookings'),
			'IN' => __('India', 'restaurant-bookings'),
			'IO' => __('British Indian Ocean Territory', 'restaurant-bookings'),
			'IQ' => __('Iraq', 'restaurant-bookings'),
			'IR' => __('Iran (Islamic Republic of)', 'restaurant-bookings'),
			'IS' => __('Iceland', 'restaurant-bookings'),
			'IT' => __('Italy', 'restaurant-bookings'),
			'JE' => __('Jersey', 'restaurant-bookings'),
			'JM' => __('Jamaica', 'restaurant-bookings'),
			'JO' => __('Jordan', 'restaurant-bookings'),
			'JP' => __('Japan', 'restaurant-bookings'),
			'KE' => __('Kenya', 'restaurant-bookings'),
			'KG' => __('Kyrgyzstan', 'restaurant-bookings'),
			'KH' => __('Cambodia', 'restaurant-bookings'),
			'KI' => __('Kiribati', 'restaurant-bookings'),
			'KM' => __('Comoros', 'restaurant-bookings'),
			'KN' => __('Saint Kitts and Nevis', 'restaurant-bookings'),
			'KP' => __('Korea (Democratic People\'s Republic of)', 'restaurant-bookings'),
			'KR' => __('Korea, Republic of', 'restaurant-bookings'),
			'KW' => __('Kuwait', 'restaurant-bookings'),
			'KY' => __('Cayman Islands', 'restaurant-bookings'),
			'KZ' => __('Kazakhstan', 'restaurant-bookings'),
			'LA' => __('Lao People\'s Democratic Republic', 'restaurant-bookings'),
			'LB' => __('Lebanon', 'restaurant-bookings'),
			'LC' => __('Saint Lucia', 'restaurant-bookings'),
			'LI' => __('Liechtenstein', 'restaurant-bookings'),
			'LK' => __('Sri Lanka', 'restaurant-bookings'),
			'LR' => __('Liberia', 'restaurant-bookings'),
			'LS' => __('Lesotho', 'restaurant-bookings'),
			'LT' => __('Lithuania', 'restaurant-bookings'),
			'LU' => __('Luxembourg', 'restaurant-bookings'),
			'LV' => __('Latvia', 'restaurant-bookings'),
			'LY' => __('Libya', 'restaurant-bookings'),
			'MA' => __('Morocco', 'restaurant-bookings'),
			'MC' => __('Monaco', 'restaurant-bookings'),
			'MD' => __('Moldova, Republic of', 'restaurant-bookings'),
			'ME' => __('Montenegro', 'restaurant-bookings'),
			'MF' => __('Saint Martin (French part)', 'restaurant-bookings'),
			'MG' => __('Madagascar', 'restaurant-bookings'),
			'MH' => __('Marshall Islands', 'restaurant-bookings'),
			'MK' => __('Macedonia, the former Yugoslav Republic of', 'restaurant-bookings'),
			'ML' => __('Mali', 'restaurant-bookings'),
			'MM' => __('Myanmar', 'restaurant-bookings'),
			'MN' => __('Mongolia', 'restaurant-bookings'),
			'MO' => __('Macao', 'restaurant-bookings'),
			'MP' => __('Northern Mariana Islands', 'restaurant-bookings'),
			'MQ' => __('Martinique', 'restaurant-bookings'),
			'MR' => __('Mauritania', 'restaurant-bookings'),
			'MS' => __('Montserrat', 'restaurant-bookings'),
			'MT' => __('Malta', 'restaurant-bookings'),
			'MU' => __('Mauritius', 'restaurant-bookings'),
			'MV' => __('Maldives', 'restaurant-bookings'),
			'MW' => __('Malawi', 'restaurant-bookings'),
			'MX' => __('Mexico', 'restaurant-bookings'),
			'MY' => __('Malaysia', 'restaurant-bookings'),
			'MZ' => __('Mozambique', 'restaurant-bookings'),
			'NA' => __('Namibia', 'restaurant-bookings'),
			'NC' => __('New Caledonia', 'restaurant-bookings'),
			'NE' => __('Niger', 'restaurant-bookings'),
			'NF' => __('Norfolk Island', 'restaurant-bookings'),
			'NG' => __('Nigeria', 'restaurant-bookings'),
			'NI' => __('Nicaragua', 'restaurant-bookings'),
			'NL' => __('Netherlands', 'restaurant-bookings'),
			'NO' => __('Norway', 'restaurant-bookings'),
			'NP' => __('Nepal', 'restaurant-bookings'),
			'NR' => __('Nauru', 'restaurant-bookings'),
			'NU' => __('Niue', 'restaurant-bookings'),
			'NZ' => __('New Zealand', 'restaurant-bookings'),
			'OM' => __('Oman', 'restaurant-bookings'),
			'PA' => __('Panama', 'restaurant-bookings'),
			'PE' => __('Peru', 'restaurant-bookings'),
			'PF' => __('French Polynesia', 'restaurant-bookings'),
			'PG' => __('Papua New Guinea', 'restaurant-bookings'),
			'PH' => __('Philippines', 'restaurant-bookings'),
			'PK' => __('Pakistan', 'restaurant-bookings'),
			'PL' => __('Poland', 'restaurant-bookings'),
			'PM' => __('Saint Pierre and Miquelon', 'restaurant-bookings'),
			'PN' => __('Pitcairn', 'restaurant-bookings'),
			'PR' => __('Puerto Rico', 'restaurant-bookings'),
			'PS' => __('Palestine, State of', 'restaurant-bookings'),
			'PT' => __('Portugal', 'restaurant-bookings'),
			'PW' => __('Palau', 'restaurant-bookings'),
			'PY' => __('Paraguay', 'restaurant-bookings'),
			'QA' => __('Qatar', 'restaurant-bookings'),
			'RE' => __('Réunion', 'restaurant-bookings'),
			'RO' => __('Romania', 'restaurant-bookings'),
			'RS' => __('Serbia', 'restaurant-bookings'),
			'RU' => __('Russian Federation', 'restaurant-bookings'),
			'RW' => __('Rwanda', 'restaurant-bookings'),
			'SA' => __('Saudi Arabia', 'restaurant-bookings'),
			'SB' => __('Solomon Islands', 'restaurant-bookings'),
			'SC' => __('Seychelles', 'restaurant-bookings'),
			'SD' => __('Sudan', 'restaurant-bookings'),
			'SE' => __('Sweden', 'restaurant-bookings'),
			'SG' => __('Singapore', 'restaurant-bookings'),
			'SH' => __('Saint Helena, Ascension and Tristan da Cunha', 'restaurant-bookings'),
			'SI' => __('Slovenia', 'restaurant-bookings'),
			'SJ' => __('Svalbard and Jan Mayen', 'restaurant-bookings'),
			'SK' => __('Slovakia', 'restaurant-bookings'),
			'SL' => __('Sierra Leone', 'restaurant-bookings'),
			'SM' => __('San Marino', 'restaurant-bookings'),
			'SN' => __('Senegal', 'restaurant-bookings'),
			'SO' => __('Somalia', 'restaurant-bookings'),
			'SR' => __('Suriname', 'restaurant-bookings'),
			'SS' => __('South Sudan', 'restaurant-bookings'),
			'ST' => __('Sao Tome and Principe', 'restaurant-bookings'),
			'SV' => __('El Salvador', 'restaurant-bookings'),
			'SX' => __('Sint Maarten (Dutch part)', 'restaurant-bookings'),
			'SY' => __('Syrian Arab Republic', 'restaurant-bookings'),
			'SZ' => __('Eswatini', 'restaurant-bookings'),
			'TC' => __('Turks and Caicos Islands', 'restaurant-bookings'),
			'TD' => __('Chad', 'restaurant-bookings'),
			'TF' => __('French Southern Territories', 'restaurant-bookings'),
			'TG' => __('Togo', 'restaurant-bookings'),
			'TH' => __('Thailand', 'restaurant-bookings'),
			'TJ' => __('Tajikistan', 'restaurant-bookings'),
			'TK' => __('Tokelau', 'restaurant-bookings'),
			'TL' => __('Timor-Leste', 'restaurant-bookings'),
			'TM' => __('Turkmenistan', 'restaurant-bookings'),
			'TN' => __('Tunisia', 'restaurant-bookings'),
			'TO' => __('Tonga', 'restaurant-bookings'),
			'TR' => __('Turkey', 'restaurant-bookings'),
			'TT' => __('Trinidad and Tobago', 'restaurant-bookings'),
			'TV' => __('Tuvalu', 'restaurant-bookings'),
			'TW' => __('Taiwan, Province of China', 'restaurant-bookings'),
			'TZ' => __('Tanzania, United Republic of', 'restaurant-bookings'),
			'UA' => __('Ukraine', 'restaurant-bookings'),
			'UG' => __('Uganda', 'restaurant-bookings'),
			'UM' => __('United States Minor Outlying Islands', 'restaurant-bookings'),
			'US' => __('United States of America', 'restaurant-bookings'),
			'UY' => __('Uruguay', 'restaurant-bookings'),
			'UZ' => __('Uzbekistan', 'restaurant-bookings'),
			'VA' => __('Holy See', 'restaurant-bookings'),
			'VC' => __('Saint Vincent and the Grenadines', 'restaurant-bookings'),
			'VE' => __('Venezuela (Bolivarian Republic of)', 'restaurant-bookings'),
			'VG' => __('Virgin Islands (British)', 'restaurant-bookings'),
			'VI' => __('Virgin Islands (U.S.)', 'restaurant-bookings'),
			'VN' => __('Viet Nam', 'restaurant-bookings'),
			'VU' => __('Vanuatu', 'restaurant-bookings'),
			'WF' => __('Wallis and Futuna', 'restaurant-bookings'),
			'WS' => __('Samoa', 'restaurant-bookings'),
			'YE' => __('Yemen', 'restaurant-bookings'),
			'YT' => __('Mayotte', 'restaurant-bookings'),
			'ZA' => __('South Africa', 'restaurant-bookings'),
			'ZM' => __('Zambia', 'restaurant-bookings'),
			'ZW' => __('Zimbabwe', 'restaurant-bookings'),
		);
	}
	
	public static function get_country_name($code) {
		$countries = self::get_countries();
		
		return $countries[$code];
	}
	
	/**
	 * Escribe la cadena traducida que mejor se
	 * adapte dentro de la lista de text que le pasamos
	 * como parametro
	 *
	 * @param \Listae\Client\Model\Text[] $texts
	 */
	public static function _e($texts) {
		echo AEI18n::__($texts);
	}

	/**
	 * Busca y devuelve la cadena traducida que mejor se
	 * adapte dentro de la lista de text que le pasamos
	 * como parametro
	 *
	 * @param \Listae\Client\Model\Text[] $texts
	 * @return
	 */
	public static function __($texts) {
		$text = AEI18n::_get_text($texts);
		return $text == null ? null : $text->getText();
	}

	/**
	 * busca el text que mejor se adapte
	 * para el idioma actual
	 *
	 * @param \Listae\Client\Model\Text[] $texts
	 * @return \Listae\Client\Model\Text
	 */
	public static function _get_text($texts) {
		if (!empty($texts)) {
			$cur_lang = AEI18n::get_cur_lang();
				
			foreach ($texts as $msg) {
				if ($msg->getLang() == $cur_lang) {
					return $msg;
				}
			}
				
			return $texts[0];
		}

		return null;
	}

	/**
	 * Recupera el idioma actual
	 * 
	 * @return string
	 */
	public static function get_cur_lang() {
		$cur_locale = strtolower(get_locale());

		if (false !== strpos($cur_locale, "_")) {
			$splited = explode("_", $cur_locale);
				
			return $splited[0];
		}

		return $cur_locale;
	}
	
	/**
	 * Funcion que devuelve los dias de la semana ordenados
	 * segun la localizacion. (requiere IntlCalendar)
	 *
	 * @return multitype:number
	 */
	public static function get_week_days() {
		// por defecto el primer dia de la semana va a ser el lunes
		$first_day_week = 1;
	
		if (class_exists("IntlCalendar")) {
			$i18ncal = IntlCalendar::createInstance(NULL, get_locale());
	
			switch ($i18ncal->getFirstDayOfWeek()) {
				case IntlCalendar::DOW_SUNDAY:
					$first_day_week = 0;
					break;
				case IntlCalendar::DOW_MONDAY:
					$first_day_week = 1;
					break;
				case IntlCalendar::DOW_TUESDAY:
					$first_day_week = 2;
					break;
				case IntlCalendar::DOW_WEDNESDAY;
					$first_day_week = 3;
					break;
				case IntlCalendar::DOW_THURSDAY:
					$first_day_week = 4;
					break;
				case IntlCalendar::DOW_FRIDAY:
					$first_day_week = 5;
					break;
				case IntlCalendar::DOW_SATURDAY:
					$first_day_week = 6;
					break;
			}
		}
	
		$ww = array();
	
		$i = 0;
		$cur_day = $first_day_week;
	
		while ($i++ < 7) {
			$ww[] = $cur_day;
			$cur_day++;
			if ($cur_day > 6) {
				$cur_day = 0;
			}
		}
	
		return $ww;
	}
	
	public static function get_week_days_name() {
		$ww = AEI18n::get_week_days();
	
		$weekdays = array("sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday");
	
		$ws = array();
	
		for ($i = 0; $i < 7; $i++) {
			$ws[] = $weekdays[$ww[$i]];
		}
	
		return $ws;
	}
	
	public static function get_week_day_name($timestamp) {
		return strtolower(date("l", $timestamp));
	}
	
	public static function get_month_name($timestamp) {
		return strtolower(date("F", $timestamp));
	}

	public static function get_currency_config($currency="") {
		// Puede que venga ya de una config
		if (is_array($currency) && isset($currency["iso_code"])) {
			return $currency;
		}
		
		$default_currency = apply_filters("ae-default-currency", "eur");
		
		$currencies = aei18n_get_currencies();
		$cc = !empty($currency_code) ? strtolower($currency_code) : $default_currency;
		
		$currency_config = isset($currencies[$cc]) ?
			$currencies[$cc] :
			$currencies[$default_currency];
		
		$currency_config["decimals"] = isset($currency_config["subunit_to_unit"]) && 
			$currency_config["subunit_to_unit"] > 0 ? 
			strlen($currency_config["subunit_to_unit"] - 1) : 0;
			
		return $currency_config;
	}
	
	public static function get_currency_symbol($currency) {
		$currency_config = self::get_currency_config($currency);
		
		return $currency_config["symbol"];
	}

	public static function format_price($price, $currency, $echo=true, $decimals=true, $esc_html=true ) {
		$currency_config = self::get_currency_config($currency);
		
		$symbol = AEI18n::get_currency_symbol($currency_config);
		$result = self::format_price_no_symbol( $price, $currency_config, $decimals) . ' ' . $symbol;
	
		if ($esc_html) {
			$result = esc_html($result);
		}
		
		if ($echo) {
			echo $result;
		}
	
		return $result;
	}
	
	public static function format_price_no_symbol($price, $currency, $decimals=true) {
		$price = floatval($price);
		$decimals_qty = 0;
		
		if ($decimals) {
			$currency_config = self::get_currency_config($currency);
			
			$decimals_qty = $currency_config["decimals"];
		}
		
		return number_format_i18n( $price, $decimals_qty );
	}
	
	/**
	 * Convierte un valor decimal de longitud o latitud
	 * a grados / minutos / segundos
	 * 
	 * @param string|float $dec valor decimal
	 */
	public static function gps_dec_to_dms($dec) {
		// Converts decimal longitude / latitude to DMS
		// ( Degrees / minutes / seconds )
	
		// This is the piece of code which may appear to
		// be inefficient, but to avoid issues with floating
		// point math we extract the integer part and the float
		// part by using a string function.
		$vars = explode(".",$dec);
		$deg = $vars[0];
		$tempma = "0.".$vars[1];
	
		$tempma = $tempma * 3600;
		$min = floor($tempma / 60);
		$sec = $tempma - ($min*60);
	
		return array("deg"=>$deg,"min"=>$min,"sec"=>$sec);
	}
	
	public static function get_review_reason_desc($review_reason) {
		switch ($review_reason) {
			case 'business': return __('Reason: business', 'restaurant-bookings');
			case 'family'  : return  __('Occasion: family', 'restaurant-bookings');
			case 'friends' : return  __('Occasion: friends', 'restaurant-bookings');
			case 'couple'  : return  __('Occasion: couple', 'restaurant-bookings');
			case 'single'  : return  __('Occasion: alone', 'restaurant-bookings');
		}
		
		// 'other'
		return __('Occasion: others', 'restaurant-bookings');
	}
	
	public static function get_link_type_name($link_type) {
		switch ($link_type) {
			case 'news' : return __("News", 'restaurant-bookings');
			case 'website' : return __("Web site", 'restaurant-bookings');
			case 'blog' : return "Blog";
			case 'twitter' : "Twitter";
			case 'facebook' : return "Facebook";
			case 'pinterest' : return "Pinterest";
			case 'logo' : return __("Logo", 'restaurant-bookings');
			case 'featured-image' : return __("Featured image", 'restaurant-bookings');
			case 'video' : return __("Video", 'restaurant-bookings');
			case 'post' : return __("Article", 'restaurant-bookings');
			case 'booking' : return __("Bookings", 'restaurant-bookings');
			case 'instagram' : return "Instagram";
			case 'tripadvisor' : return "Tripadvisor";
			case 'yelp' : return "Yelp";
			case 'youtube' : return "YouTube";
			case 'vimeo' : return "Vimeo";
			case 'foursquare' : return "Foursquare";
			case 'flickr' : return "Flickr";
		}
		
		return __("Others", 'restaurant-bookings');
	}
}

/**
 * Enum con los distintos formularios de listae
 * 
 * @author moz667
 */
abstract class AE_URLS {
	const FORM_CONTACT 		= "form-contact";
	const FORM_BOOKING 		= "form-booking";
	const FORM_GROUP 		= "form-group";
	const FORM_REVIEW 		= "form-review";
	const FORM_WRONG_DATA 	= "form-wrong-data";
	
	const ORDER_REDIRECT	= "order-redirect";
	const FORM_ORDER	   	= "form-order";
	const FORM_ORDER_BOOKING = "form-order-booking";
	const FORM_ORDER_DELIVERY = "form-order-delivery";
	const FORM_ORDER_TAKEAWAY = "form-order-takeaway";
	const FORM_COUPON 		= "form-coupon";
	const EP_ORDER_PING		= "api-order-ping";
	const EP_ORDER_GET 		= "api-order-get";
	const EP_ORDER_ADD 		= "api-order-add";
	const EP_ORDER_DEL 		= "api-order-del";
	const EP_ORDER_OFFER_ADD= "api-offer-offer-add";
	const EP_ORDER_OFFER_DEL= "api-offer-offer-del";
	const EP_DLV_SET_ADR	= "api-delivery-set-address";
	const EP_OFFER_GET 		= "api-offer-get";
	const EP_SLOTS_PARTY_SIZES = "api-slot-party-sizes";
	const EP_SLOTS_DATES 	= "api-slot-dates";
	const EP_SLOTS_DINING_AREAS = "api-slot-areas";
	const EP_SLOTS_TIMES 	= "api-slot-times";
}

/**
 * Distintas utilidades tipicas para trabajar con 
 * urls
 * @author moz667
 */
class AEUrl {
	private static $AE_URLS = array(
		AE_URLS::FORM_BOOKING		=> "[AE_BASE_URL]b/{slug}/booking/",
		AE_URLS::FORM_CONTACT		=> "[AE_BASE_URL]b/{slug}/contact/",
		AE_URLS::FORM_GROUP			=> "[AE_BASE_URL]b/{slug}/group/",
		AE_URLS::FORM_REVIEW		=> "[AE_BASE_URL]b/{slug}/review/",
		// TODO: No me convence esta url VVVV Revisar si se nos ocurre otro path mejor
		AE_URLS::ORDER_REDIRECT		=> "[AE_BASE_URL]b/{slug}/order/redirect/",
		AE_URLS::FORM_ORDER			=> "[AE_BASE_URL]b/{slug}/order/",
		AE_URLS::FORM_ORDER_BOOKING	=> "[AE_BASE_URL]b/{slug}/order/booking/",
		AE_URLS::FORM_ORDER_DELIVERY => "[AE_BASE_URL]b/{slug}/delivery/",
		AE_URLS::FORM_ORDER_TAKEAWAY => "[AE_BASE_URL]b/{slug}/takeaway/",

		AE_URLS::FORM_WRONG_DATA	=> "[AE_BASE_URL]b/{slug}/wrong-data/",
		AE_URLS::FORM_COUPON		=> "[AE_BASE_URL]b/{slug}/coupon/{cid}/",
		
		AE_URLS::EP_ORDER_PING		=> "api/order/ping/{type}",
		AE_URLS::EP_ORDER_GET		=> "api/order/{business-id}/{order-type}",
		AE_URLS::EP_ORDER_ADD		=> "api/order/{business-id}/{order-type}/add",
		AE_URLS::EP_ORDER_DEL		=> "api/order/{business-id}/{order-type}/remove",
		AE_URLS::EP_ORDER_OFFER_ADD	=> "api/order/{business-id}/{order-type}/add/offer",
		AE_URLS::EP_ORDER_OFFER_DEL	=> "api/order/{business-id}/{order-type}/del/offer",
		AE_URLS::EP_DLV_SET_ADR		=> "api/order/{business-id}/delivery/setAddress",
		AE_URLS::EP_OFFER_GET		=> "api/offer/{business-id}/get",
		AE_URLS::EP_SLOTS_PARTY_SIZES => "api/booking/slots/{business-id}/partysizes",
		AE_URLS::EP_SLOTS_DATES		=> "api/booking/slots/{business-id}/dates",
		AE_URLS::EP_SLOTS_DINING_AREAS => "api/booking/slots/{business-id}/areas",
		AE_URLS::EP_SLOTS_TIMES		=> "api/booking/slots/{business-id}/times",
	);
	
	public static function has_slug_arg($type) {
		$slug_arg_types = Array(
			AE_URLS::FORM_BOOKING,
			AE_URLS::FORM_CONTACT,
			AE_URLS::FORM_GROUP,
			AE_URLS::FORM_REVIEW,
			AE_URLS::FORM_WRONG_DATA,
		);
		
		return in_array($type, $slug_arg_types);
	}
	
	private static function get_url_origin( $use_forwarded_host = false ) {
		$ssl	  = ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' );
		$sp	   = strtolower( $_SERVER['SERVER_PROTOCOL'] );
		$protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
		$port	 = $_SERVER['SERVER_PORT'];
		$port	 = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
		$host	 = ( $use_forwarded_host && isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : ( isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : null );
		$host	 = isset( $host ) ? $host : $_SERVER['SERVER_NAME'] . $port;
		
		return $protocol . '://' . $host;
	}
	
	public static function get_full_url( $use_forwarded_host = false ) {
		return self::get_url_origin( $use_forwarded_host ) . $_SERVER['REQUEST_URI'];
	}
	
	/**
	 * Recupera la url al host de una url
	 * 
	 * @param string $url
	 * @return $url domain host, string
	 */
	public static function get_host_url($url) {
		$url_parts = @parse_url($url);
		
		foreach (array("user", "pass", "path", "query", "fragment") as $remove_part) {
			if (isset($url_parts[$remove_part])) {
				unset($url_parts[$remove_part]);
			}
		}
		
		$url = "";
		
		if (isset($url_parts["host"])) {
			$url_parts["scheme"] = isset($url_parts["scheme"]) ? $url_parts["scheme"] : "http";
				
			if (function_exists("http_build_url")) {
				$url = http_build_url($url_parts);
			} else {
				$url = $url_parts["scheme"] . "://" . $url_parts["host"];
					
				if (isset($url_parts["port"])) {
					$url .= ":" . $url_parts["port"];
				}
					
				$url .= "/";
			}
		}
		
		return $url;
	}
	
	/**
	 * Recupera el nombre del dominio de una url
	 * 
 	 * @param $url
 	 * @return $url domain host, string
	 * @param  $url
	 */
	public static function get_domain_name($url) {
		$hostname = @parse_url($url, PHP_URL_HOST);
	
		if (!$hostname) {
			$hostname = $url;
		}
	
		if (substr($hostname, 0, 4) == "www.") {
			$hostname = substr($hostname, 0);
		}
	
		return $hostname;
	}
	
	/**
	 * Funcion para aniadir parametros a una url
	 *
	 * @param string $url
	 * @param mixed $params[]
	 * @return string
	 */
	public static function add_params($url, $params=array(), $array_subfix = "[]") {
		$new_params = array();
	
		foreach ($params as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $v) {
					if (strpos($url, $key . $array_subfix . "=" . urlencode($v)) === false) {
						$new_params[] = $key . $array_subfix . "=" . urlencode($v);
					}
				}
			} else {
				if (strpos($url, $key . "=" . urlencode($value)) === false) {
					$new_params[] = $key . "=" . urlencode($value);
				}
			}
		}
	
		return count($new_params) ?
			$url . (strpos($url, "?") === false ? "?" : "&") . implode("&", $new_params) :
			$url;
	}
	/**
	 * Borra parametros de GET una url y devuelve la misma sin esos 
	 * parametros
	 * 
	 * @param string $url, url de la de donde vamos a borrar parametros
	 * @param array $params, parametros a borrar
	 * @return string
	 */
	public static function del_params($url, $params=array(), $array_subfix = "[]") {
		$pu = parse_url($url);
		
		// Si no hay nada en donde buscar no hay nada que borrar.
		if (!isset($pu["query"]) || empty($pu["query"])) return $url;
	
		$q = array();
	
		parse_str($pu["query"], $q);
	
		foreach ($params as $search_key => $search_values) {
			// Si existe el parametro que buscamos en la query
			if (key_exists($search_key, $q)) {
				// Si el parametro en la query es un array
				if (is_array($q[$search_key])) {
					// podemos hacer un array_diff tal cual
					$q[$search_key] = array_diff($q[$search_key], is_array($search_values) ? $search_values : array($search_values));
					// Sino y es un array los valores
				} elseif (is_array($search_values)) {
					// Uno a uno vamos comprobando hasta que encontremos el valor buscado y lo quitamos de la query
					foreach ($search_values as $search_value) {
						if ($search_value == $q[$search_key]) {
							unset($q[$search_key]);
							break;
						}
					}
					// Sino, sera buscar un valor en un parametro de la query que no es un array, asi que, si vale lo mismo lo quitamos
				} elseif ($search_values == $q[$search_key]) {
					unset($q[$search_key]);
				}
			}
		}
		
		// no podemos usar build query ya que mete los indices en el caso de los parametros de arrays, cosa que no nos interesa
		// $pu["query"] = http_build_query($q);
		
		$s = "";
		$first = true;
		
		foreach ($q as $k => $v) {
			if (is_array($v)) {
				foreach ($v as $arv) {
					if (!$first) {
						$s .= "&";
					} else {
						$first = false;
					}
					$s .= $k . $array_subfix . "=" . urlencode($arv);
				}
			} else {
				if (!$first) {
					$s .= "&";
				} else {
					$first = false;
				}
				$s .= $k . "=" . urlencode($v);
		
			}
		}
		
		$pu["query"] = $s;
		
		return self::build_url($pu);
	}
	
	/**
	 * 
	 * Inversa de parse_url requiere las partes de :
	 *
	 * $pu["host"]
	 * $pu["path"]
	 *
	 */
	public static function build_url($pu = array()) {
		
		$url = "";
	
		// Si las partes de la url no contiene host, se trata de una url relavita,
		// asi que no construimos la url
		if (!isset($pu["host"])) {
			return $pu["query"];
		} else {
			if (function_exists("http_build_url")) {
				return http_build_url($pu);
			}
			
			if (!isset($pu["scheme"])) {
				$pu["scheme"] = "http";
			}
			
			$url = $pu["scheme"] . "://" . $pu["host"];
			
		}
		
		if (isset($pu["path"])) {
			$url .= $pu["path"];
		}
	
		if (isset($pu["query"]) && !empty($pu["query"])) {
			$url .= "?" . $pu["query"];
		}
	
		if (isset($pu["fragment"]) && !empty($pu["fragment"])) {
			$url .= "#" . $pu["fragment"];
		}
	
		return $url;
	
	}
	
	/**
	 * Devuelve la url de listae.com
	 * 
	 * @param string $type cualquiera de las constantes de la clase AE_URLS
	 * @param array $args
	 * @return string
	 */
	public static function get_listae_url($type, $args=array(), $path_args=array()) {
		$path = isset(self::$AE_URLS[$type]) ? self::$AE_URLS[$type] : "";
		
		$args = apply_filters("ae_get_listae_url_defaults", $args, $type);
		
		$url = '';

		$last_fragment_is_lang = false;

		if (strpos($path, '[AE_BASE_URL]') !== FALSE) {
			$url = str_replace("[AE_BASE_URL]", AE_BASE_URL, $path);
			// Este caso es el de las urls nuevas, porlo que si no tiene lang lo podemos poner al final
			$last_fragment_is_lang = true;
		} else {
			$url = AE_URL . $path;
		}
		
		if (isset($path_args) && is_array($path_args) && count($path_args) > 0) {
			$path_args = apply_filters("ae_get_listae_url_path_args_defaults", $path_args, $type);
			foreach ($path_args as $key => $value) {
				$url = str_replace('{' . $key . '}', $value, $url);
			}
		}

		$query_string_args = [];
		
		if (isset($args)) {
			foreach ($args as $key => $value) {
				if (strpos($url, '{' . $key . '}') !== FALSE) {
					$url = str_replace('{' . $key . '}', $value, $url);
				} else {
					$query_string_args[$key] = $value;
				}
			}
		}

		if ($last_fragment_is_lang) {
			// Si se le pasa el lang, por qstring, lo ponemos al final y lo quitamos 
			// del array
			$cur_lang = AEI18n::get_cur_lang();

			if (isset($query_string_args["lang"])) {
				$cur_lang = $query_string_args["lang"];
				unset($query_string_args["lang"]);
			// Si no tiene, lo calculamos y lo ponemos
			}

			// Por ahora solo tiene spanish e english
			$url .= $cur_lang == "es" ? 'es/' : 'en/';
		}
		
		if (isset($query_string_args) && is_array($query_string_args) && count($query_string_args) > 0) {
			$url = self::add_params($url, $query_string_args, "");
		}
		
		return $url;
	}
}




class AETurn {
	public $type = null;
	public $closed = false;
	public $from = null;
	public $to = null;

	/**
	 * @param \Listae\Client\Model\TurnDay $turnDay
	 * @param string $type
	 */
	public function __construct($turnDay = null, $type = null) {
		$this->type = $type;
		if ($turnDay != null) {
			$this->from = $turnDay->getFrom();
			$this->to = $turnDay->getTo();
			$this->closed = $turnDay->getClosed();
		}
	}

	/**
	 * @param AETurn $turn1
	 * @param AETurn $turn2
	 */
	public function equals($turn) {
		return $this->type == $turn->type &&
		$this->from == $turn->from &&
		$this->to == $turn->to &&
		$this->closed == $turn->closed;
	}

	/**
	 * Devuelve el rango horario en modo texto
	 * @return string
	 */
	function get_range_text() {
		return $this->from . "-" . $this->to;
	}

	/**
	 * Devuelve si el turno esta cerrado o no
	 *
	 * @return boolean
	 */
	public function is_closed() {
		return $this->closed;
	}

	/**
	 * Devuelve si el turno tiene un
	 * rango vacio
	 *
	 * @return boolean
	 */
	public function is_range_empty() {
		return empty($this->from) || empty($this->to);
	}

	/**
	 * Devuelve si el turno tiene un
	 * rango vacio
	 *
	 * @return boolean
	 */
	public function is_type_empty() {
		return empty($this->type);
	}

	/**
	 * Devuelve el texto traducido del tipo de turno,
	 * si no tiene tipo de turno, devuelve null
	 *
	 * @return string|NULL
	 */
	public function get_type_text() {
		switch ($this->type) {
			case "breakfast":
				return __("Breakfast", "restaurant-bookings");
			case "brunch":
				return __("Lunch", "restaurant-bookings");
			case "lunch":
				return __("Food", "restaurant-bookings");
				break;
			case "tea":
				return __("Night tea", "restaurant-bookings");
			case "dinner":
				return __("Dinner", "restaurant-bookings");
			case "late":
				return __("Early morning", "restaurant-bookings");
		}

		return null;
	}
}


class AEDayRange {
	public $days = array();
	/**
	 * @var AETurn[]
	 */
	public $turns = array();

	/**
	 * @param \Listae\Client\Model\TurnDay $turnDay
	 * @param string $type
	*/
	public function __construct($turnDay = null, $type = null) {
		if ($turnDay != null) {
			$this->add_day($turnDay->getName());
			$this->add_turn($turnDay, $type);
		}
	}

	/**
	 * @return AETurn[]
	 */
	public function get_turns() {
		return $this->turns;
	}

	public function is_all_closed() {
		foreach ($this->turns as $t) {
			if (!$t->closed) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @param \Listae\Client\Model\TurnDay $turnDay
	 * @param string $type
	 */
	public function add_turn($turnDay, $type = null) {
		$this->turns[] = new AETurn($turnDay, $type);
	}

	/**
	 * @param string $dayName
	 */
	public function add_day($dayName) {
		$this->days[] = $dayName;
	}

	/**
	 * @param \Listae\Client\Model\TurnDay $turnDay
	 * @param string $type
	 */
	public function contains($turnDay, $type = null) {
		foreach ($this->turns as $t) {
			if ($t->equals(new AETurn($turnDay, $type))) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Compara este objeto con otro y devuelve true
	 * si los dias de la semana que tienen ambos son iguales.
	 * En cualquier otro caso, devuelve false
	 *
	 * @param AEDayRange $day_range
	 *
	 * @return boolean
	 */
	public function equals_days($day_range) {
		if (count($this->days) != count($day_range->days)) {
			return false;
		}
		
		foreach ($day_range->days as $day) {
			if (array_search($day, $this->days) === false) {
				return false;
			}
		}
		
		return true;
	}

	/**
	 * Construye un texto de resumen de los dias
	 * para el rango de dias actual
	 *
	 * @return string
	 */
	public function get_resume_days_text() {
		$s = "";
		$weekdays_indexes = array();
		foreach ( AEI18n::get_week_days_name() as $i => $wd ) {
			if ( array_search($wd, $this->days) !== false ) {
				$weekdays_indexes[] = $i;
			}
		}

		foreach ( $weekdays_indexes as $i => $weekday_index ) {
			$next_day_correlative = false;
				
			if ($i > 0 && count($weekdays_indexes) > $i + 1) {
				$next_day_correlative = (($weekdays_indexes[$i + 1] - $weekday_index) == 1);
			}
				
			if ($i > 0 && substr($s, -1) != "-") {
				if ($next_day_correlative) {
					$s .= "-";
				} elseif (!$next_day_correlative) {
					$s .= ",";
				}
			}
				
			if ($i == 0 || $next_day_correlative == false ) {
				$s .= date_i18n( "D", strtotime( $this->days[$i] ) );
			}
		}

		return $s;
	}
}

class AEDayRanges {
	/**
	 * @var AEDayRange[]
	 */
	public $dayRanges = array();

	/**
	 * @var \Listae\Client\Model\EasyRange[]
	*/
	public $closures = array();

	/**
	 * @var \Listae\Client\Model\EasyRange[]
	*/
	public $openings = array();

	/**
	 * @param \Listae\Client\Model\AgendaBase $agenda
	*/
	public function __construct($agenda) {
		foreach ($agenda->getTurns()->getTurn() as $i => $turn) {
			foreach (AEI18n::get_week_days() as $week_day) {
				$turnDay = $turn->getDay()[$week_day];
				$this->add_day($turnDay, $turn->getType());
			}
		}
		
		$new_day_ranges = array();
		foreach ($this->dayRanges as $dr) {
			$match = false;
			foreach ($new_day_ranges as $new_dr) {
				if ($dr->equals_days($new_dr)) {
					$match = true;
					foreach ($dr->turns as $turn) {
						$new_dr->turns[] = $turn;
					}
				}
			}
			
			if (!$match) {
				$new_day_ranges[] = $dr;
			}
		}
		
		$this->dayRanges = $new_day_ranges;
		$closures = $agenda->getClosures();
		
		if ( !empty( $closures ) ) {
			$this->closures = $closures->getClosure();
		}
		$opening = $agenda->getOpenings();
		
		if ( !empty( $opening ) ) {
			$this->openings = $opening->getOpening();
		}
	}

	/**
	 * Devuelve un AEDayRange para el dia de hoy
	 * 
	 * @return AEDayRange
	 */
	public function get_today() {
		$drToday = new AEDayRange();

		$now = time();
		$day = AEI18n::get_week_day_name($now);
		$drToday->add_day($day);

		foreach ($this->dayRanges as $dr) {
			if (array_search($day, $dr->days) !== false) {
				foreach ($dr->turns as $turn) {
					$drToday->turns[] = $turn;
				}
			}
		}

		$closures = $this->get_closures($now);
		$openings = $this->get_openings($now);

		$new_turns = array();

		foreach ($drToday->turns as $turn) {
			// Si es un cierre y tenemos aperturas...
			if ( $turn->closed && count($openings) > 0 ) {
				// Las aperturas simplemente las aniadimos
				foreach ($openings as $opening) {
					$timeranges = $opening->getTimeRanges();
					
					if (!empty($timeranges)) {
						foreach ($timeranges->getTimeRange() as $tr) {
							$new_turn = new AETurn();
							$new_turn->from = $tr->getFrom();
							$new_turn->to = $tr->getTo();
							$new_turns[] = $new_turn;
						}
					}
				}
				// Si es una apertura y tenemos cierres...
			} elseif ( !$turn->closed && count($closures) > 0 ) {
				// Los cierres son mas complicados ya que pueden restar tiempo al rango del turno...
				foreach ($closures as $closure) {
					$timeranges = $closure->getTimeRanges();
					
					// Solo si tiene rangos de horas el cierre no es completo de dia, asi que...
					if (!empty($timeranges)) {
						// para cada uno de los rangos horarios
						foreach ($timeranges->getTimeRange() as $tr) {
							if ( ($turn->from >= $tr->getFrom() && $turn->to > $tr->getTo()) ) {
								$new_turn = new AETurn();
								$new_turn->from = $tr->getTo();
								$new_turn->to = $turn->to;
								$new_turns[] = $new_turn;
							} elseif ($turn->from < $tr->getFrom() && $turn->to > $tr->getTo()) {
								$new_turn = new AETurn();
								$new_turn->from = $turn->from;
								$new_turn->to = $tr->getFrom();
								$new_turns[] = $new_turn;

								$new_turn = new AETurn();
								$new_turn->from = $tr->getTo();
								$new_turn->to = $turn->to;
								$new_turns[] = $new_turn;
							} elseif ($turn->from < $tr->getFrom() && $turn->to <= $tr->getTo()) {
								$new_turn = new AETurn();
								$new_turn->from = $turn->from;
								$new_turn->to = $tr->getFrom();
								$new_turns[] = $new_turn;
							}
						}
					}
				}
			} else {
				$new_turns[] = $turn;
			}
		}

		// Esta cerrado todo el dia
		if (count($new_turns) == 0) {
			$new_turn = new AETurn();
			$new_turn->closed = true;
			$new_turns[] = $new_turn;
		}

		$drToday->turns = $new_turns;

		return $drToday;
	}

	/**
	 * @param int $timestamp
	 * @return \Listae\Client\Model\EasyRange[]
	 */
	private function get_closures($timestamp) {
		return $this->find_easy_ranges($this->closures, $timestamp);
	}

	/**
	 * @param int $timestamp
	 * @return \Listae\Client\Model\EasyRange[]
	 */
	private function get_openings($timestamp) {
		return $this->find_easy_ranges($this->openings, $timestamp);
	}

	/**
	 * @param \Listae\Client\Model\EasyRange[] $ranges
	 * @param int $timestamp
	 *
	 * @return \Listae\Client\Model\EasyRange[]
	 */
	private function find_easy_ranges($ranges, $timestamp) {
		$match_ranges = array();
		$date = new DateTime("@" . $timestamp);
		$week_day_name = AEI18n::get_week_day_name($timestamp);
		$month_name = AEI18n::get_month_name($timestamp);

		foreach ($ranges as $r) {
			$matchs = array();
			$count_matches = 0;
			
			$from = $r->getFrom();
			$to = $r->getTo();
			$week_days = $r->getWeekdays();
			$months = $r->getMonths();
			
			if (!empty($from)) {
				$count_matches++;
				if ($from <= $date) {
					if (!empty($to)) {
						if ($to >= $date) {
							$matchs[] = $r;
						}
					} else {
						$matchs[] = $r;
					}
				}
			} elseif ( !empty($to) ) {
				$count_matches++;
				if ($to >= $date) {
					$matchs[] = $r;
				}
			}
				
			if ( !empty($week_days) ) {
				$count_matches++;
				foreach ($week_days->getWeekday() as $wd) {
					if ($wd == $week_day_name) {
						$matchs[] = $r;
						break;
					}
				}
			}
				
			if ( !empty($months) ) {
				$count_matches++;
				foreach ($months->getMonth() as $m) {
					if ($m == $month_name) {
						$matchs[] = $r;
						break;
					}
				}
			}
				
			if ($count_matches == count($matchs)) {
				$match_ranges[] = $r;
			}
		}

		return $match_ranges;
	}


	/**
	 * @param \Listae\Client\Model\TurnDay $turnDay
	 * @param string $type
	 */
	public function add_day($turnDay, $type = null) {
		$match_index = false;
		foreach ($this->dayRanges as $i => $dr) {
			if ($dr->contains($turnDay, $type)) {
				$match_index = $i;
				break;
			}
		}

		if ($match_index === false) {
			$this->dayRanges[] = new AEDayRange($turnDay, $type);
		} else {
			$this->dayRanges[$match_index]->add_day($turnDay->getName());
		}
	}

	/**
	 * @param AEDayRange $day_range
	 */
	public function add_day_range($day_range) {
		$match_index = false;

		foreach ($this->dayRanges as $i => $dr) {
			if ($dr->equals_days($day_range)) {
				$match_index = $i;
			}
		}

		if ($match_index === false) {
			$this->dayRanges[] = $day_range;
		} else {
			foreach ($day_range->turns as $t) {
				$this->dayRanges[$match_index]->turns[] = $t;
			}
		}
	}
}

