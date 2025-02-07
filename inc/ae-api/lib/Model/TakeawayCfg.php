<?php
/**
 * TakeawayCfg
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
 * TakeawayCfg Class Doc Comment
 *
 * @category Class
 * @package  Listae\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class TakeawayCfg implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'TakeawayCfg';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'cartes' => '\Listae\Client\Model\Cartes',
'agendas' => '\Listae\Client\Model\RestaurantAgendas',
'enabled' => 'bool',
'min_order_date' => '\DateTime',
'min_order_time' => 'string',
'available_now' => 'bool',
'available_for_today' => 'bool',
'elaborate_right_now' => 'bool',
'min_time_right_now' => 'int',
'max_time_right_now' => 'int',
'days_to_elaborate' => 'int',
'min_order' => 'float',
'elaboration_time' => 'int',
'time_interval' => 'int',
'min_time_in_advance' => 'int',
'max_time_in_advance' => 'int',
'currency' => 'string'    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'cartes' => null,
'agendas' => null,
'enabled' => null,
'min_order_date' => 'date',
'min_order_time' => null,
'available_now' => null,
'available_for_today' => null,
'elaborate_right_now' => null,
'min_time_right_now' => 'int64',
'max_time_right_now' => 'int64',
'days_to_elaborate' => 'int64',
'min_order' => 'float',
'elaboration_time' => 'int64',
'time_interval' => 'int64',
'min_time_in_advance' => 'int64',
'max_time_in_advance' => 'int64',
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
        'cartes' => 'cartes',
'agendas' => 'agendas',
'enabled' => 'enabled',
'min_order_date' => 'minOrderDate',
'min_order_time' => 'minOrderTime',
'available_now' => 'availableNow',
'available_for_today' => 'availableForToday',
'elaborate_right_now' => 'elaborateRightNow',
'min_time_right_now' => 'minTimeRightNow',
'max_time_right_now' => 'maxTimeRightNow',
'days_to_elaborate' => 'daysToElaborate',
'min_order' => 'minOrder',
'elaboration_time' => 'elaborationTime',
'time_interval' => 'timeInterval',
'min_time_in_advance' => 'minTimeInAdvance',
'max_time_in_advance' => 'maxTimeInAdvance',
'currency' => 'currency'    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'cartes' => 'setCartes',
'agendas' => 'setAgendas',
'enabled' => 'setEnabled',
'min_order_date' => 'setMinOrderDate',
'min_order_time' => 'setMinOrderTime',
'available_now' => 'setAvailableNow',
'available_for_today' => 'setAvailableForToday',
'elaborate_right_now' => 'setElaborateRightNow',
'min_time_right_now' => 'setMinTimeRightNow',
'max_time_right_now' => 'setMaxTimeRightNow',
'days_to_elaborate' => 'setDaysToElaborate',
'min_order' => 'setMinOrder',
'elaboration_time' => 'setElaborationTime',
'time_interval' => 'setTimeInterval',
'min_time_in_advance' => 'setMinTimeInAdvance',
'max_time_in_advance' => 'setMaxTimeInAdvance',
'currency' => 'setCurrency'    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'cartes' => 'getCartes',
'agendas' => 'getAgendas',
'enabled' => 'getEnabled',
'min_order_date' => 'getMinOrderDate',
'min_order_time' => 'getMinOrderTime',
'available_now' => 'getAvailableNow',
'available_for_today' => 'getAvailableForToday',
'elaborate_right_now' => 'getElaborateRightNow',
'min_time_right_now' => 'getMinTimeRightNow',
'max_time_right_now' => 'getMaxTimeRightNow',
'days_to_elaborate' => 'getDaysToElaborate',
'min_order' => 'getMinOrder',
'elaboration_time' => 'getElaborationTime',
'time_interval' => 'getTimeInterval',
'min_time_in_advance' => 'getMinTimeInAdvance',
'max_time_in_advance' => 'getMaxTimeInAdvance',
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
        $this->container['cartes'] = isset($data['cartes']) ? $data['cartes'] : null;
        $this->container['agendas'] = isset($data['agendas']) ? $data['agendas'] : null;
        $this->container['enabled'] = isset($data['enabled']) ? $data['enabled'] : null;
        $this->container['min_order_date'] = isset($data['min_order_date']) ? $data['min_order_date'] : null;
        $this->container['min_order_time'] = isset($data['min_order_time']) ? $data['min_order_time'] : null;
        $this->container['available_now'] = isset($data['available_now']) ? $data['available_now'] : null;
        $this->container['available_for_today'] = isset($data['available_for_today']) ? $data['available_for_today'] : null;
        $this->container['elaborate_right_now'] = isset($data['elaborate_right_now']) ? $data['elaborate_right_now'] : null;
        $this->container['min_time_right_now'] = isset($data['min_time_right_now']) ? $data['min_time_right_now'] : null;
        $this->container['max_time_right_now'] = isset($data['max_time_right_now']) ? $data['max_time_right_now'] : null;
        $this->container['days_to_elaborate'] = isset($data['days_to_elaborate']) ? $data['days_to_elaborate'] : null;
        $this->container['min_order'] = isset($data['min_order']) ? $data['min_order'] : null;
        $this->container['elaboration_time'] = isset($data['elaboration_time']) ? $data['elaboration_time'] : null;
        $this->container['time_interval'] = isset($data['time_interval']) ? $data['time_interval'] : null;
        $this->container['min_time_in_advance'] = isset($data['min_time_in_advance']) ? $data['min_time_in_advance'] : null;
        $this->container['max_time_in_advance'] = isset($data['max_time_in_advance']) ? $data['max_time_in_advance'] : null;
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
     * Gets cartes
     *
     * @return \Listae\Client\Model\Cartes
     */
    public function getCartes()
    {
        return $this->container['cartes'];
    }

    /**
     * Sets cartes
     *
     * @param \Listae\Client\Model\Cartes $cartes cartes
     *
     * @return $this
     */
    public function setCartes($cartes)
    {
        $this->container['cartes'] = $cartes;

        return $this;
    }

    /**
     * Gets agendas
     *
     * @return \Listae\Client\Model\RestaurantAgendas
     */
    public function getAgendas()
    {
        return $this->container['agendas'];
    }

    /**
     * Sets agendas
     *
     * @param \Listae\Client\Model\RestaurantAgendas $agendas agendas
     *
     * @return $this
     */
    public function setAgendas($agendas)
    {
        $this->container['agendas'] = $agendas;

        return $this;
    }

    /**
     * Gets enabled
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->container['enabled'];
    }

    /**
     * Sets enabled
     *
     * @param bool $enabled Takeaway activado o no
     *
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->container['enabled'] = $enabled;

        return $this;
    }

    /**
     * Gets min_order_date
     *
     * @return \DateTime
     */
    public function getMinOrderDate()
    {
        return $this->container['min_order_date'];
    }

    /**
     * Sets min_order_date
     *
     * @param \DateTime $min_order_date Fecha minima por defecto del pedido
     *
     * @return $this
     */
    public function setMinOrderDate($min_order_date)
    {
        $this->container['min_order_date'] = $min_order_date;

        return $this;
    }

    /**
     * Gets min_order_time
     *
     * @return string
     */
    public function getMinOrderTime()
    {
        return $this->container['min_order_time'];
    }

    /**
     * Sets min_order_time
     *
     * @param string $min_order_time Hora minima por defecto del pedido
     *
     * @return $this
     */
    public function setMinOrderTime($min_order_time)
    {
        $this->container['min_order_time'] = $min_order_time;

        return $this;
    }

    /**
     * Gets available_now
     *
     * @return bool
     */
    public function getAvailableNow()
    {
        return $this->container['available_now'];
    }

    /**
     * Sets available_now
     *
     * @param bool $available_now Takeaway disponible ahora o no
     *
     * @return $this
     */
    public function setAvailableNow($available_now)
    {
        $this->container['available_now'] = $available_now;

        return $this;
    }

    /**
     * Gets available_for_today
     *
     * @return bool
     */
    public function getAvailableForToday()
    {
        return $this->container['available_for_today'];
    }

    /**
     * Sets available_for_today
     *
     * @param bool $available_for_today Takeaway disponible para hoy o no
     *
     * @return $this
     */
    public function setAvailableForToday($available_for_today)
    {
        $this->container['available_for_today'] = $available_for_today;

        return $this;
    }

    /**
     * Gets elaborate_right_now
     *
     * @return bool
     */
    public function getElaborateRightNow()
    {
        return $this->container['elaborate_right_now'];
    }

    /**
     * Sets elaborate_right_now
     *
     * @param bool $elaborate_right_now Indica si el pedido se va a empezar a preparar en el momento de solicitarlo.
     *
     * @return $this
     */
    public function setElaborateRightNow($elaborate_right_now)
    {
        $this->container['elaborate_right_now'] = $elaborate_right_now;

        return $this;
    }

    /**
     * Gets min_time_right_now
     *
     * @return int
     */
    public function getMinTimeRightNow()
    {
        return $this->container['min_time_right_now'];
    }

    /**
     * Sets min_time_right_now
     *
     * @param int $min_time_right_now Tiempo minimo dedicado a la elaboracion del pedido y termino del mismo si se va a empezar a preparar al momento de solicitarlo.
     *
     * @return $this
     */
    public function setMinTimeRightNow($min_time_right_now)
    {
        $this->container['min_time_right_now'] = $min_time_right_now;

        return $this;
    }

    /**
     * Gets max_time_right_now
     *
     * @return int
     */
    public function getMaxTimeRightNow()
    {
        return $this->container['max_time_right_now'];
    }

    /**
     * Sets max_time_right_now
     *
     * @param int $max_time_right_now Tiempo maximo dedicado a la elaboracion del pedido y termino del mismo si se va a empezar a preparar al momento de solicitarlo.
     *
     * @return $this
     */
    public function setMaxTimeRightNow($max_time_right_now)
    {
        $this->container['max_time_right_now'] = $max_time_right_now;

        return $this;
    }

    /**
     * Gets days_to_elaborate
     *
     * @return int
     */
    public function getDaysToElaborate()
    {
        return $this->container['days_to_elaborate'];
    }

    /**
     * Sets days_to_elaborate
     *
     * @param int $days_to_elaborate Numero de dias hasta que se haga el reparto o complete el pedido para recoger.
     *
     * @return $this
     */
    public function setDaysToElaborate($days_to_elaborate)
    {
        $this->container['days_to_elaborate'] = $days_to_elaborate;

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
     * Gets elaboration_time
     *
     * @return int
     */
    public function getElaborationTime()
    {
        return $this->container['elaboration_time'];
    }

    /**
     * Sets elaboration_time
     *
     * @param int $elaboration_time Tiempo dedicado a la elaboracion del pedido. Necesario para calcular lo que tarda el pedido cuando se solicita asap.
     *
     * @return $this
     */
    public function setElaborationTime($elaboration_time)
    {
        $this->container['elaboration_time'] = $elaboration_time;

        return $this;
    }

    /**
     * Gets time_interval
     *
     * @return int
     */
    public function getTimeInterval()
    {
        return $this->container['time_interval'];
    }

    /**
     * Sets time_interval
     *
     * @param int $time_interval Intervalo de tiempo expresado en minutos en los que se divide las distintos turnos horarios para realizar los pedidos
     *
     * @return $this
     */
    public function setTimeInterval($time_interval)
    {
        $this->container['time_interval'] = $time_interval;

        return $this;
    }

    /**
     * Gets min_time_in_advance
     *
     * @return int
     */
    public function getMinTimeInAdvance()
    {
        return $this->container['min_time_in_advance'];
    }

    /**
     * Sets min_time_in_advance
     *
     * @param int $min_time_in_advance Tiempo minimo para poder hacer el pedido para recoger
     *
     * @return $this
     */
    public function setMinTimeInAdvance($min_time_in_advance)
    {
        $this->container['min_time_in_advance'] = $min_time_in_advance;

        return $this;
    }

    /**
     * Gets max_time_in_advance
     *
     * @return int
     */
    public function getMaxTimeInAdvance()
    {
        return $this->container['max_time_in_advance'];
    }

    /**
     * Sets max_time_in_advance
     *
     * @param int $max_time_in_advance Tiempo máximo para poder hacer el pedido para recoger
     *
     * @return $this
     */
    public function setMaxTimeInAdvance($max_time_in_advance)
    {
        $this->container['max_time_in_advance'] = $max_time_in_advance;

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
