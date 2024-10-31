<?php
/**
 * BookingCfg
 *
 * PHP version 5
 *
 * @category Class
 * @package  Listae\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * listae API 2.0
 *
 * Documentación de los servicios REST de listae
 *
 * OpenAPI spec version: 2.0.1
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 3.0.23
 */
/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace Listae\Client\Model;

use \ArrayAccess;
use \Listae\Client\ObjectSerializer;

/**
 * BookingCfg Class Doc Comment
 *
 * @category Class
 * @package  Listae\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class BookingCfg implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'BookingCfg';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'dining_areas' => '\Listae\Client\Model\DiningAreas',
'booking_times' => '\Listae\Client\Model\SelectField',
'bookers' => '\Listae\Client\Model\SelectField',
'booking_dates' => '\DateTime[]',
'vip_club' => 'string',
'vip_club_description' => 'string',
'description_information' => 'string',
'min_booking_date' => '\DateTime',
'max_booking_date' => '\DateTime',
'booking_date' => '\DateTime',
'min_bookers' => 'int',
'max_bookers' => 'int',
'booking_time' => 'string',
'current_bookers' => 'int',
'current_dining_area' => '\Listae\Client\Model\ItemField',
'current_menu' => '\Listae\Client\Model\ItemField',
'min_order' => 'float',
'currency' => 'string'    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'dining_areas' => null,
'booking_times' => null,
'bookers' => null,
'booking_dates' => 'date',
'vip_club' => null,
'vip_club_description' => null,
'description_information' => null,
'min_booking_date' => 'date',
'max_booking_date' => 'date',
'booking_date' => 'date',
'min_bookers' => 'int32',
'max_bookers' => 'int32',
'booking_time' => null,
'current_bookers' => 'int32',
'current_dining_area' => null,
'current_menu' => null,
'min_order' => 'float',
'currency' => null    ];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerFormats()
    {
        return self::$swaggerFormats;
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'dining_areas' => 'diningAreas',
'booking_times' => 'bookingTimes',
'bookers' => 'bookers',
'booking_dates' => 'bookingDates',
'vip_club' => 'vipClub',
'vip_club_description' => 'vipClubDescription',
'description_information' => 'descriptionInformation',
'min_booking_date' => 'min-booking-date',
'max_booking_date' => 'max-booking-date',
'booking_date' => 'booking-date',
'min_bookers' => 'min-bookers',
'max_bookers' => 'max-bookers',
'booking_time' => 'booking-time',
'current_bookers' => 'current-bookers',
'current_dining_area' => 'current-dining-area',
'current_menu' => 'current-menu',
'min_order' => 'minOrder',
'currency' => 'currency'    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'dining_areas' => 'setDiningAreas',
'booking_times' => 'setBookingTimes',
'bookers' => 'setBookers',
'booking_dates' => 'setBookingDates',
'vip_club' => 'setVipClub',
'vip_club_description' => 'setVipClubDescription',
'description_information' => 'setDescriptionInformation',
'min_booking_date' => 'setMinBookingDate',
'max_booking_date' => 'setMaxBookingDate',
'booking_date' => 'setBookingDate',
'min_bookers' => 'setMinBookers',
'max_bookers' => 'setMaxBookers',
'booking_time' => 'setBookingTime',
'current_bookers' => 'setCurrentBookers',
'current_dining_area' => 'setCurrentDiningArea',
'current_menu' => 'setCurrentMenu',
'min_order' => 'setMinOrder',
'currency' => 'setCurrency'    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'dining_areas' => 'getDiningAreas',
'booking_times' => 'getBookingTimes',
'bookers' => 'getBookers',
'booking_dates' => 'getBookingDates',
'vip_club' => 'getVipClub',
'vip_club_description' => 'getVipClubDescription',
'description_information' => 'getDescriptionInformation',
'min_booking_date' => 'getMinBookingDate',
'max_booking_date' => 'getMaxBookingDate',
'booking_date' => 'getBookingDate',
'min_bookers' => 'getMinBookers',
'max_bookers' => 'getMaxBookers',
'booking_time' => 'getBookingTime',
'current_bookers' => 'getCurrentBookers',
'current_dining_area' => 'getCurrentDiningArea',
'current_menu' => 'getCurrentMenu',
'min_order' => 'getMinOrder',
'currency' => 'getCurrency'    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$swaggerModelName;
    }

    

    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['dining_areas'] = isset($data['dining_areas']) ? $data['dining_areas'] : null;
        $this->container['booking_times'] = isset($data['booking_times']) ? $data['booking_times'] : null;
        $this->container['bookers'] = isset($data['bookers']) ? $data['bookers'] : null;
        $this->container['booking_dates'] = isset($data['booking_dates']) ? $data['booking_dates'] : null;
        $this->container['vip_club'] = isset($data['vip_club']) ? $data['vip_club'] : null;
        $this->container['vip_club_description'] = isset($data['vip_club_description']) ? $data['vip_club_description'] : null;
        $this->container['description_information'] = isset($data['description_information']) ? $data['description_information'] : null;
        $this->container['min_booking_date'] = isset($data['min_booking_date']) ? $data['min_booking_date'] : null;
        $this->container['max_booking_date'] = isset($data['max_booking_date']) ? $data['max_booking_date'] : null;
        $this->container['booking_date'] = isset($data['booking_date']) ? $data['booking_date'] : null;
        $this->container['min_bookers'] = isset($data['min_bookers']) ? $data['min_bookers'] : null;
        $this->container['max_bookers'] = isset($data['max_bookers']) ? $data['max_bookers'] : null;
        $this->container['booking_time'] = isset($data['booking_time']) ? $data['booking_time'] : null;
        $this->container['current_bookers'] = isset($data['current_bookers']) ? $data['current_bookers'] : null;
        $this->container['current_dining_area'] = isset($data['current_dining_area']) ? $data['current_dining_area'] : null;
        $this->container['current_menu'] = isset($data['current_menu']) ? $data['current_menu'] : null;
        $this->container['min_order'] = isset($data['min_order']) ? $data['min_order'] : null;
        $this->container['currency'] = isset($data['currency']) ? $data['currency'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets dining_areas
     *
     * @return \Listae\Client\Model\DiningAreas
     */
    public function getDiningAreas()
    {
        return $this->container['dining_areas'];
    }

    /**
     * Sets dining_areas
     *
     * @param \Listae\Client\Model\DiningAreas $dining_areas dining_areas
     *
     * @return $this
     */
    public function setDiningAreas($dining_areas)
    {
        $this->container['dining_areas'] = $dining_areas;

        return $this;
    }

    /**
     * Gets booking_times
     *
     * @return \Listae\Client\Model\SelectField
     */
    public function getBookingTimes()
    {
        return $this->container['booking_times'];
    }

    /**
     * Sets booking_times
     *
     * @param \Listae\Client\Model\SelectField $booking_times booking_times
     *
     * @return $this
     */
    public function setBookingTimes($booking_times)
    {
        $this->container['booking_times'] = $booking_times;

        return $this;
    }

    /**
     * Gets bookers
     *
     * @return \Listae\Client\Model\SelectField
     */
    public function getBookers()
    {
        return $this->container['bookers'];
    }

    /**
     * Sets bookers
     *
     * @param \Listae\Client\Model\SelectField $bookers bookers
     *
     * @return $this
     */
    public function setBookers($bookers)
    {
        $this->container['bookers'] = $bookers;

        return $this;
    }

    /**
     * Gets booking_dates
     *
     * @return \DateTime[]
     */
    public function getBookingDates()
    {
        return $this->container['booking_dates'];
    }

    /**
     * Sets booking_dates
     *
     * @param \DateTime[] $booking_dates Fechas disponibles para los datos de reserva consultados (formato yyyy-mm-dd)
     *
     * @return $this
     */
    public function setBookingDates($booking_dates)
    {
        $this->container['booking_dates'] = $booking_dates;

        return $this;
    }

    /**
     * Gets vip_club
     *
     * @return string
     */
    public function getVipClub()
    {
        return $this->container['vip_club'];
    }

    /**
     * Sets vip_club
     *
     * @param string $vip_club Nombre del club VIP del restaurante (si lo tiene)
     *
     * @return $this
     */
    public function setVipClub($vip_club)
    {
        $this->container['vip_club'] = $vip_club;

        return $this;
    }

    /**
     * Gets vip_club_description
     *
     * @return string
     */
    public function getVipClubDescription()
    {
        return $this->container['vip_club_description'];
    }

    /**
     * Sets vip_club_description
     *
     * @param string $vip_club_description Breve descripcion del club VIP y las ofertas de aderirse
     *
     * @return $this
     */
    public function setVipClubDescription($vip_club_description)
    {
        $this->container['vip_club_description'] = $vip_club_description;

        return $this;
    }

    /**
     * Gets description_information
     *
     * @return string
     */
    public function getDescriptionInformation()
    {
        return $this->container['description_information'];
    }

    /**
     * Sets description_information
     *
     * @param string $description_information Observaciones del restaurante a tener en cuenta en referencia a las reservas
     *
     * @return $this
     */
    public function setDescriptionInformation($description_information)
    {
        $this->container['description_information'] = $description_information;

        return $this;
    }

    /**
     * Gets min_booking_date
     *
     * @return \DateTime
     */
    public function getMinBookingDate()
    {
        return $this->container['min_booking_date'];
    }

    /**
     * Sets min_booking_date
     *
     * @param \DateTime $min_booking_date Fecha mínima en la cual se puede hacer una reserva
     *
     * @return $this
     */
    public function setMinBookingDate($min_booking_date)
    {
        $this->container['min_booking_date'] = $min_booking_date;

        return $this;
    }

    /**
     * Gets max_booking_date
     *
     * @return \DateTime
     */
    public function getMaxBookingDate()
    {
        return $this->container['max_booking_date'];
    }

    /**
     * Sets max_booking_date
     *
     * @param \DateTime $max_booking_date Fecha máxima en la cual se puede hacer una reserva
     *
     * @return $this
     */
    public function setMaxBookingDate($max_booking_date)
    {
        $this->container['max_booking_date'] = $max_booking_date;

        return $this;
    }

    /**
     * Gets booking_date
     *
     * @return \DateTime
     */
    public function getBookingDate()
    {
        return $this->container['booking_date'];
    }

    /**
     * Sets booking_date
     *
     * @param \DateTime $booking_date Fecha recomendada para la reserva
     *
     * @return $this
     */
    public function setBookingDate($booking_date)
    {
        $this->container['booking_date'] = $booking_date;

        return $this;
    }

    /**
     * Gets min_bookers
     *
     * @return int
     */
    public function getMinBookers()
    {
        return $this->container['min_bookers'];
    }

    /**
     * Sets min_bookers
     *
     * @param int $min_bookers Número de comensales mínimos para reservar
     *
     * @return $this
     */
    public function setMinBookers($min_bookers)
    {
        $this->container['min_bookers'] = $min_bookers;

        return $this;
    }

    /**
     * Gets max_bookers
     *
     * @return int
     */
    public function getMaxBookers()
    {
        return $this->container['max_bookers'];
    }

    /**
     * Sets max_bookers
     *
     * @param int $max_bookers Número de comensales recomendados para reservar
     *
     * @return $this
     */
    public function setMaxBookers($max_bookers)
    {
        $this->container['max_bookers'] = $max_bookers;

        return $this;
    }

    /**
     * Gets booking_time
     *
     * @return string
     */
    public function getBookingTime()
    {
        return $this->container['booking_time'];
    }

    /**
     * Sets booking_time
     *
     * @param string $booking_time Hora recomendada para la reserva
     *
     * @return $this
     */
    public function setBookingTime($booking_time)
    {
        $this->container['booking_time'] = $booking_time;

        return $this;
    }

    /**
     * Gets current_bookers
     *
     * @return int
     */
    public function getCurrentBookers()
    {
        return $this->container['current_bookers'];
    }

    /**
     * Sets current_bookers
     *
     * @param int $current_bookers Número de comensales recomendados para reservar
     *
     * @return $this
     */
    public function setCurrentBookers($current_bookers)
    {
        $this->container['current_bookers'] = $current_bookers;

        return $this;
    }

    /**
     * Gets current_dining_area
     *
     * @return \Listae\Client\Model\ItemField
     */
    public function getCurrentDiningArea()
    {
        return $this->container['current_dining_area'];
    }

    /**
     * Sets current_dining_area
     *
     * @param \Listae\Client\Model\ItemField $current_dining_area current_dining_area
     *
     * @return $this
     */
    public function setCurrentDiningArea($current_dining_area)
    {
        $this->container['current_dining_area'] = $current_dining_area;

        return $this;
    }

    /**
     * Gets current_menu
     *
     * @return \Listae\Client\Model\ItemField
     */
    public function getCurrentMenu()
    {
        return $this->container['current_menu'];
    }

    /**
     * Sets current_menu
     *
     * @param \Listae\Client\Model\ItemField $current_menu current_menu
     *
     * @return $this
     */
    public function setCurrentMenu($current_menu)
    {
        $this->container['current_menu'] = $current_menu;

        return $this;
    }

    /**
     * Gets min_order
     *
     * @return float
     */
    public function getMinOrder()
    {
        return $this->container['min_order'];
    }

    /**
     * Sets min_order
     *
     * @param float $min_order Pedido mínimo
     *
     * @return $this
     */
    public function setMinOrder($min_order)
    {
        $this->container['min_order'] = $min_order;

        return $this;
    }

    /**
     * Gets currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->container['currency'];
    }

    /**
     * Sets currency
     *
     * @param string $currency Moneda del pedido mínimo
     *
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->container['currency'] = $currency;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     *
     * @param integer $offset Offset
     * @param mixed   $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(
                ObjectSerializer::sanitizeForSerialization($this),
                JSON_PRETTY_PRINT
            );
        }

        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}
