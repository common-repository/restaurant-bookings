<?php
/**
 * DiningAreas
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
 * DiningAreas Class Doc Comment
 *
 * @category Class
 * @package  Listae\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class DiningAreas implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'DiningAreas';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'max_capacity' => 'int',
'table' => 'int',
'rooms' => 'int',
'dining_area' => '\Listae\Client\Model\DiningArea[]'    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'max_capacity' => 'int64',
'table' => 'int64',
'rooms' => 'int64',
'dining_area' => null    ];

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
        'max_capacity' => 'max-capacity',
'table' => 'table',
'rooms' => 'rooms',
'dining_area' => 'dining-area'    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'max_capacity' => 'setMaxCapacity',
'table' => 'setTable',
'rooms' => 'setRooms',
'dining_area' => 'setDiningArea'    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'max_capacity' => 'getMaxCapacity',
'table' => 'getTable',
'rooms' => 'getRooms',
'dining_area' => 'getDiningArea'    ];

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
        $this->container['max_capacity'] = isset($data['max_capacity']) ? $data['max_capacity'] : null;
        $this->container['table'] = isset($data['table']) ? $data['table'] : null;
        $this->container['rooms'] = isset($data['rooms']) ? $data['rooms'] : null;
        $this->container['dining_area'] = isset($data['dining_area']) ? $data['dining_area'] : null;
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
     * Gets max_capacity
     *
     * @return int
     */
    public function getMaxCapacity()
    {
        return $this->container['max_capacity'];
    }

    /**
     * Sets max_capacity
     *
     * @param int $max_capacity Número total de capacidad del negocio
     *
     * @return $this
     */
    public function setMaxCapacity($max_capacity)
    {
        $this->container['max_capacity'] = $max_capacity;

        return $this;
    }

    /**
     * Gets table
     *
     * @return int
     */
    public function getTable()
    {
        return $this->container['table'];
    }

    /**
     * Sets table
     *
     * @param int $table Número total de mesas del negocio
     *
     * @return $this
     */
    public function setTable($table)
    {
        $this->container['table'] = $table;

        return $this;
    }

    /**
     * Gets rooms
     *
     * @return int
     */
    public function getRooms()
    {
        return $this->container['rooms'];
    }

    /**
     * Sets rooms
     *
     * @param int $rooms Número total de salas del negocio
     *
     * @return $this
     */
    public function setRooms($rooms)
    {
        $this->container['rooms'] = $rooms;

        return $this;
    }

    /**
     * Gets dining_area
     *
     * @return \Listae\Client\Model\DiningArea[]
     */
    public function getDiningArea()
    {
        return $this->container['dining_area'];
    }

    /**
     * Sets dining_area
     *
     * @param \Listae\Client\Model\DiningArea[] $dining_area Lista de zonas/salas
     *
     * @return $this
     */
    public function setDiningArea($dining_area)
    {
        $this->container['dining_area'] = $dining_area;

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
