<?php
/**
 * PostInfo
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
 * PostInfo Class Doc Comment
 *
 * @category Class
 * @package  Listae\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class PostInfo implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'PostInfo';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'url' => 'string',
'created' => '\DateTime',
'updated' => '\DateTime',
'title' => 'string',
'excerpt' => 'string',
'tags' => 'string',
'businesses' => '\Listae\Client\Model\BusinessMiniList',
'display_name' => 'string',
'address' => '\Listae\Client\Model\GeoPosition',
'business_categories' => '\Listae\Client\Model\Categories',
'external' => 'bool',
'lang' => 'string',
'type' => 'string',
'href' => 'string',
'featured_image' => '\Listae\Client\Model\BasicLink',
'published_date' => '\DateTime'    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'url' => null,
'created' => 'date-time',
'updated' => 'date-time',
'title' => null,
'excerpt' => null,
'tags' => null,
'businesses' => null,
'display_name' => null,
'address' => null,
'business_categories' => null,
'external' => null,
'lang' => null,
'type' => null,
'href' => null,
'featured_image' => null,
'published_date' => 'date-time'    ];

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
        'url' => 'url',
'created' => 'created',
'updated' => 'updated',
'title' => 'title',
'excerpt' => 'excerpt',
'tags' => 'tags',
'businesses' => 'businesses',
'display_name' => 'displayName',
'address' => 'address',
'business_categories' => 'businessCategories',
'external' => 'external',
'lang' => 'lang',
'type' => 'type',
'href' => 'href',
'featured_image' => 'featuredImage',
'published_date' => 'publishedDate'    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'url' => 'setUrl',
'created' => 'setCreated',
'updated' => 'setUpdated',
'title' => 'setTitle',
'excerpt' => 'setExcerpt',
'tags' => 'setTags',
'businesses' => 'setBusinesses',
'display_name' => 'setDisplayName',
'address' => 'setAddress',
'business_categories' => 'setBusinessCategories',
'external' => 'setExternal',
'lang' => 'setLang',
'type' => 'setType',
'href' => 'setHref',
'featured_image' => 'setFeaturedImage',
'published_date' => 'setPublishedDate'    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'url' => 'getUrl',
'created' => 'getCreated',
'updated' => 'getUpdated',
'title' => 'getTitle',
'excerpt' => 'getExcerpt',
'tags' => 'getTags',
'businesses' => 'getBusinesses',
'display_name' => 'getDisplayName',
'address' => 'getAddress',
'business_categories' => 'getBusinessCategories',
'external' => 'getExternal',
'lang' => 'getLang',
'type' => 'getType',
'href' => 'getHref',
'featured_image' => 'getFeaturedImage',
'published_date' => 'getPublishedDate'    ];

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
        $this->container['url'] = isset($data['url']) ? $data['url'] : null;
        $this->container['created'] = isset($data['created']) ? $data['created'] : null;
        $this->container['updated'] = isset($data['updated']) ? $data['updated'] : null;
        $this->container['title'] = isset($data['title']) ? $data['title'] : null;
        $this->container['excerpt'] = isset($data['excerpt']) ? $data['excerpt'] : null;
        $this->container['tags'] = isset($data['tags']) ? $data['tags'] : null;
        $this->container['businesses'] = isset($data['businesses']) ? $data['businesses'] : null;
        $this->container['display_name'] = isset($data['display_name']) ? $data['display_name'] : null;
        $this->container['address'] = isset($data['address']) ? $data['address'] : null;
        $this->container['business_categories'] = isset($data['business_categories']) ? $data['business_categories'] : null;
        $this->container['external'] = isset($data['external']) ? $data['external'] : null;
        $this->container['lang'] = isset($data['lang']) ? $data['lang'] : null;
        $this->container['type'] = isset($data['type']) ? $data['type'] : null;
        $this->container['href'] = isset($data['href']) ? $data['href'] : null;
        $this->container['featured_image'] = isset($data['featured_image']) ? $data['featured_image'] : null;
        $this->container['published_date'] = isset($data['published_date']) ? $data['published_date'] : null;
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
     * Gets url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->container['url'];
    }

    /**
     * Sets url
     *
     * @param string $url Identificador único según tipo en ae
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->container['url'] = $url;

        return $this;
    }

    /**
     * Gets created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->container['created'];
    }

    /**
     * Sets created
     *
     * @param \DateTime $created Fecha de inserción/creación
     *
     * @return $this
     */
    public function setCreated($created)
    {
        $this->container['created'] = $created;

        return $this;
    }

    /**
     * Gets updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->container['updated'];
    }

    /**
     * Sets updated
     *
     * @param \DateTime $updated Fecha de actualización
     *
     * @return $this
     */
    public function setUpdated($updated)
    {
        $this->container['updated'] = $updated;

        return $this;
    }

    /**
     * Gets title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->container['title'];
    }

    /**
     * Sets title
     *
     * @param string $title Título de la publicación
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->container['title'] = $title;

        return $this;
    }

    /**
     * Gets excerpt
     *
     * @return string
     */
    public function getExcerpt()
    {
        return $this->container['excerpt'];
    }

    /**
     * Sets excerpt
     *
     * @param string $excerpt Resumen de la  publicación (texto crudo sin HTML)
     *
     * @return $this
     */
    public function setExcerpt($excerpt)
    {
        $this->container['excerpt'] = $excerpt;

        return $this;
    }

    /**
     * Gets tags
     *
     * @return string
     */
    public function getTags()
    {
        return $this->container['tags'];
    }

    /**
     * Sets tags
     *
     * @param string $tags Lista de etiquetas de la publicación
     *
     * @return $this
     */
    public function setTags($tags)
    {
        $this->container['tags'] = $tags;

        return $this;
    }

    /**
     * Gets businesses
     *
     * @return \Listae\Client\Model\BusinessMiniList
     */
    public function getBusinesses()
    {
        return $this->container['businesses'];
    }

    /**
     * Sets businesses
     *
     * @param \Listae\Client\Model\BusinessMiniList $businesses businesses
     *
     * @return $this
     */
    public function setBusinesses($businesses)
    {
        $this->container['businesses'] = $businesses;

        return $this;
    }

    /**
     * Gets display_name
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->container['display_name'];
    }

    /**
     * Sets display_name
     *
     * @param string $display_name Nombre del autor de la publicación
     *
     * @return $this
     */
    public function setDisplayName($display_name)
    {
        $this->container['display_name'] = $display_name;

        return $this;
    }

    /**
     * Gets address
     *
     * @return \Listae\Client\Model\GeoPosition
     */
    public function getAddress()
    {
        return $this->container['address'];
    }

    /**
     * Sets address
     *
     * @param \Listae\Client\Model\GeoPosition $address address
     *
     * @return $this
     */
    public function setAddress($address)
    {
        $this->container['address'] = $address;

        return $this;
    }

    /**
     * Gets business_categories
     *
     * @return \Listae\Client\Model\Categories
     */
    public function getBusinessCategories()
    {
        return $this->container['business_categories'];
    }

    /**
     * Sets business_categories
     *
     * @param \Listae\Client\Model\Categories $business_categories business_categories
     *
     * @return $this
     */
    public function setBusinessCategories($business_categories)
    {
        $this->container['business_categories'] = $business_categories;

        return $this;
    }

    /**
     * Gets external
     *
     * @return bool
     */
    public function getExternal()
    {
        return $this->container['external'];
    }

    /**
     * Sets external
     *
     * @param bool $external Indica si la publicación NO viene de una de una RSS del propio restaurante con el que este vinculado
     *
     * @return $this
     */
    public function setExternal($external)
    {
        $this->container['external'] = $external;

        return $this;
    }

    /**
     * Gets lang
     *
     * @return string
     */
    public function getLang()
    {
        return $this->container['lang'];
    }

    /**
     * Sets lang
     *
     * @param string $lang Indica el idioma en el que se escribió la publicación
     *
     * @return $this
     */
    public function setLang($lang)
    {
        $this->container['lang'] = $lang;

        return $this;
    }

    /**
     * Gets type
     *
     * @return string
     */
    public function getType()
    {
        return $this->container['type'];
    }

    /**
     * Sets type
     *
     * @param string $type Tipo de publicación (siempre post por ahora, ignorar)
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->container['type'] = $type;

        return $this;
    }

    /**
     * Gets href
     *
     * @return string
     */
    public function getHref()
    {
        return $this->container['href'];
    }

    /**
     * Sets href
     *
     * @param string $href URL donde esta publicada la publicación
     *
     * @return $this
     */
    public function setHref($href)
    {
        $this->container['href'] = $href;

        return $this;
    }

    /**
     * Gets featured_image
     *
     * @return \Listae\Client\Model\BasicLink
     */
    public function getFeaturedImage()
    {
        return $this->container['featured_image'];
    }

    /**
     * Sets featured_image
     *
     * @param \Listae\Client\Model\BasicLink $featured_image featured_image
     *
     * @return $this
     */
    public function setFeaturedImage($featured_image)
    {
        $this->container['featured_image'] = $featured_image;

        return $this;
    }

    /**
     * Gets published_date
     *
     * @return \DateTime
     */
    public function getPublishedDate()
    {
        return $this->container['published_date'];
    }

    /**
     * Sets published_date
     *
     * @param \DateTime $published_date Fecha de publicación de la publicación
     *
     * @return $this
     */
    public function setPublishedDate($published_date)
    {
        $this->container['published_date'] = $published_date;

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
