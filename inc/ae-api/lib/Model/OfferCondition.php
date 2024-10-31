<?php
/**
 * OfferCondition
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
 * OfferCondition Class Doc Comment
 *
 * @category Class
 * @package  Listae\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class OfferCondition implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'OfferCondition';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'catalogs' => '\Listae\Client\Model\NameAndDescriptionEntity[]',
'catalog_item_groups' => '\Listae\Client\Model\NameAndDescriptionEntity[]',
'catalog_items' => '\Listae\Client\Model\NameAndDescriptionEntity[]',
'catalog_item_categories' => '\Listae\Client\Model\NameAndDescriptionEntity[]',
'date_range' => '\Listae\Client\Model\EasyRange',
'offer_condition_type' => 'string',
'offer_condition_minimum_type' => 'string',
'minimum_unit' => 'int',
'minimum_expense' => 'float',
'suscribe' => 'bool'    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'catalogs' => null,
'catalog_item_groups' => null,
'catalog_items' => null,
'catalog_item_categories' => null,
'date_range' => null,
'offer_condition_type' => null,
'offer_condition_minimum_type' => null,
'minimum_unit' => null,
'minimum_expense' => 'float',
'suscribe' => null    ];

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
        'catalogs' => 'catalogs',
'catalog_item_groups' => 'catalogItemGroups',
'catalog_items' => 'catalogItems',
'catalog_item_categories' => 'catalogItemCategories',
'date_range' => 'dateRange',
'offer_condition_type' => 'offerConditionType',
'offer_condition_minimum_type' => 'offerConditionMinimumType',
'minimum_unit' => 'minimumUnit',
'minimum_expense' => 'minimumExpense',
'suscribe' => 'suscribe'    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'catalogs' => 'setCatalogs',
'catalog_item_groups' => 'setCatalogItemGroups',
'catalog_items' => 'setCatalogItems',
'catalog_item_categories' => 'setCatalogItemCategories',
'date_range' => 'setDateRange',
'offer_condition_type' => 'setOfferConditionType',
'offer_condition_minimum_type' => 'setOfferConditionMinimumType',
'minimum_unit' => 'setMinimumUnit',
'minimum_expense' => 'setMinimumExpense',
'suscribe' => 'setSuscribe'    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'catalogs' => 'getCatalogs',
'catalog_item_groups' => 'getCatalogItemGroups',
'catalog_items' => 'getCatalogItems',
'catalog_item_categories' => 'getCatalogItemCategories',
'date_range' => 'getDateRange',
'offer_condition_type' => 'getOfferConditionType',
'offer_condition_minimum_type' => 'getOfferConditionMinimumType',
'minimum_unit' => 'getMinimumUnit',
'minimum_expense' => 'getMinimumExpense',
'suscribe' => 'getSuscribe'    ];

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

    const OFFER_CONDITION_TYPE_CATALOG_FILTER = 'CATALOG_FILTER';
const OFFER_CONDITION_TYPE_DATE_TIME_FILTER = 'DATE_TIME_FILTER';
const OFFER_CONDITION_TYPE_MINIMUN_FILTER = 'MINIMUN_FILTER';
const OFFER_CONDITION_TYPE_SUSCRIBE_FILTER = 'SUSCRIBE_FILTER';
const OFFER_CONDITION_MINIMUM_TYPE_UNIT = 'UNIT';
const OFFER_CONDITION_MINIMUM_TYPE_EXPENSE = 'EXPENSE';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getOfferConditionTypeAllowableValues()
    {
        return [
            self::OFFER_CONDITION_TYPE_CATALOG_FILTER,
self::OFFER_CONDITION_TYPE_DATE_TIME_FILTER,
self::OFFER_CONDITION_TYPE_MINIMUN_FILTER,
self::OFFER_CONDITION_TYPE_SUSCRIBE_FILTER,        ];
    }
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getOfferConditionMinimumTypeAllowableValues()
    {
        return [
            self::OFFER_CONDITION_MINIMUM_TYPE_UNIT,
self::OFFER_CONDITION_MINIMUM_TYPE_EXPENSE,        ];
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
        $this->container['catalogs'] = isset($data['catalogs']) ? $data['catalogs'] : null;
        $this->container['catalog_item_groups'] = isset($data['catalog_item_groups']) ? $data['catalog_item_groups'] : null;
        $this->container['catalog_items'] = isset($data['catalog_items']) ? $data['catalog_items'] : null;
        $this->container['catalog_item_categories'] = isset($data['catalog_item_categories']) ? $data['catalog_item_categories'] : null;
        $this->container['date_range'] = isset($data['date_range']) ? $data['date_range'] : null;
        $this->container['offer_condition_type'] = isset($data['offer_condition_type']) ? $data['offer_condition_type'] : null;
        $this->container['offer_condition_minimum_type'] = isset($data['offer_condition_minimum_type']) ? $data['offer_condition_minimum_type'] : null;
        $this->container['minimum_unit'] = isset($data['minimum_unit']) ? $data['minimum_unit'] : null;
        $this->container['minimum_expense'] = isset($data['minimum_expense']) ? $data['minimum_expense'] : null;
        $this->container['suscribe'] = isset($data['suscribe']) ? $data['suscribe'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        $allowedValues = $this->getOfferConditionTypeAllowableValues();
        if (!is_null($this->container['offer_condition_type']) && !in_array($this->container['offer_condition_type'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value for 'offer_condition_type', must be one of '%s'",
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getOfferConditionMinimumTypeAllowableValues();
        if (!is_null($this->container['offer_condition_minimum_type']) && !in_array($this->container['offer_condition_minimum_type'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value for 'offer_condition_minimum_type', must be one of '%s'",
                implode("', '", $allowedValues)
            );
        }

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
     * Gets catalogs
     *
     * @return \Listae\Client\Model\NameAndDescriptionEntity[]
     */
    public function getCatalogs()
    {
        return $this->container['catalogs'];
    }

    /**
     * Sets catalogs
     *
     * @param \Listae\Client\Model\NameAndDescriptionEntity[] $catalogs Lista de catalogos sobre los que se puede aplicar la oferta (opcional)
     *
     * @return $this
     */
    public function setCatalogs($catalogs)
    {
        $this->container['catalogs'] = $catalogs;

        return $this;
    }

    /**
     * Gets catalog_item_groups
     *
     * @return \Listae\Client\Model\NameAndDescriptionEntity[]
     */
    public function getCatalogItemGroups()
    {
        return $this->container['catalog_item_groups'];
    }

    /**
     * Sets catalog_item_groups
     *
     * @param \Listae\Client\Model\NameAndDescriptionEntity[] $catalog_item_groups Lista de grupos de catalogos sobre los que se puede aplicar la oferta (opcional)
     *
     * @return $this
     */
    public function setCatalogItemGroups($catalog_item_groups)
    {
        $this->container['catalog_item_groups'] = $catalog_item_groups;

        return $this;
    }

    /**
     * Gets catalog_items
     *
     * @return \Listae\Client\Model\NameAndDescriptionEntity[]
     */
    public function getCatalogItems()
    {
        return $this->container['catalog_items'];
    }

    /**
     * Sets catalog_items
     *
     * @param \Listae\Client\Model\NameAndDescriptionEntity[] $catalog_items Lista de items de catalogos sobre los que se puede aplicar la oferta (opcional)
     *
     * @return $this
     */
    public function setCatalogItems($catalog_items)
    {
        $this->container['catalog_items'] = $catalog_items;

        return $this;
    }

    /**
     * Gets catalog_item_categories
     *
     * @return \Listae\Client\Model\NameAndDescriptionEntity[]
     */
    public function getCatalogItemCategories()
    {
        return $this->container['catalog_item_categories'];
    }

    /**
     * Sets catalog_item_categories
     *
     * @param \Listae\Client\Model\NameAndDescriptionEntity[] $catalog_item_categories Lista de categorias de items de catalogos sobre los que se puede aplicar la oferta (opcional)
     *
     * @return $this
     */
    public function setCatalogItemCategories($catalog_item_categories)
    {
        $this->container['catalog_item_categories'] = $catalog_item_categories;

        return $this;
    }

    /**
     * Gets date_range
     *
     * @return \Listae\Client\Model\EasyRange
     */
    public function getDateRange()
    {
        return $this->container['date_range'];
    }

    /**
     * Sets date_range
     *
     * @param \Listae\Client\Model\EasyRange $date_range date_range
     *
     * @return $this
     */
    public function setDateRange($date_range)
    {
        $this->container['date_range'] = $date_range;

        return $this;
    }

    /**
     * Gets offer_condition_type
     *
     * @return string
     */
    public function getOfferConditionType()
    {
        return $this->container['offer_condition_type'];
    }

    /**
     * Sets offer_condition_type
     *
     * @param string $offer_condition_type Tipo de condicion
     *
     * @return $this
     */
    public function setOfferConditionType($offer_condition_type)
    {
        $allowedValues = $this->getOfferConditionTypeAllowableValues();
        if (!is_null($offer_condition_type) && !in_array($offer_condition_type, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value for 'offer_condition_type', must be one of '%s'",
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['offer_condition_type'] = $offer_condition_type;

        return $this;
    }

    /**
     * Gets offer_condition_minimum_type
     *
     * @return string
     */
    public function getOfferConditionMinimumType()
    {
        return $this->container['offer_condition_minimum_type'];
    }

    /**
     * Sets offer_condition_minimum_type
     *
     * @param string $offer_condition_minimum_type Tipo minimo de condicion
     *
     * @return $this
     */
    public function setOfferConditionMinimumType($offer_condition_minimum_type)
    {
        $allowedValues = $this->getOfferConditionMinimumTypeAllowableValues();
        if (!is_null($offer_condition_minimum_type) && !in_array($offer_condition_minimum_type, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value for 'offer_condition_minimum_type', must be one of '%s'",
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['offer_condition_minimum_type'] = $offer_condition_minimum_type;

        return $this;
    }

    /**
     * Gets minimum_unit
     *
     * @return int
     */
    public function getMinimumUnit()
    {
        return $this->container['minimum_unit'];
    }

    /**
     * Sets minimum_unit
     *
     * @param int $minimum_unit Cantidad minima de unidades para que se pueda aplicar el beneficio (opcional)
     *
     * @return $this
     */
    public function setMinimumUnit($minimum_unit)
    {
        $this->container['minimum_unit'] = $minimum_unit;

        return $this;
    }

    /**
     * Gets minimum_expense
     *
     * @return float
     */
    public function getMinimumExpense()
    {
        return $this->container['minimum_expense'];
    }

    /**
     * Sets minimum_expense
     *
     * @param float $minimum_expense Gasto minimo para que se pueda aplicar el beneficio (opcional)
     *
     * @return $this
     */
    public function setMinimumExpense($minimum_expense)
    {
        $this->container['minimum_expense'] = $minimum_expense;

        return $this;
    }

    /**
     * Gets suscribe
     *
     * @return bool
     */
    public function getSuscribe()
    {
        return $this->container['suscribe'];
    }

    /**
     * Sets suscribe
     *
     * @param bool $suscribe Si es obligatorio para el usuario de la oferta estar suscrito a novedades
     *
     * @return $this
     */
    public function setSuscribe($suscribe)
    {
        $this->container['suscribe'] = $suscribe;

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