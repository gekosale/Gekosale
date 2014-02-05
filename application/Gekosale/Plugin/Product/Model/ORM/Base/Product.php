<?php

namespace Gekosale\Plugin\Product\Model\ORM\Base;

use \DateTime;
use \Exception;
use \PDO;
use Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute as ChildProductAttribute;
use Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeQuery;
use Gekosale\Plugin\Attribute\Model\ORM\Base\ProductAttribute;
use Gekosale\Plugin\Availability\Model\ORM\AvailabilityQuery;
use Gekosale\Plugin\Availability\Model\ORM\Availability as ChildAvailability;
use Gekosale\Plugin\Crosssell\Model\ORM\Crosssell as ChildCrosssell;
use Gekosale\Plugin\Crosssell\Model\ORM\CrosssellQuery;
use Gekosale\Plugin\Crosssell\Model\ORM\Base\Crosssell;
use Gekosale\Plugin\Currency\Model\ORM\Currency as ChildCurrency;
use Gekosale\Plugin\Currency\Model\ORM\CurrencyQuery;
use Gekosale\Plugin\Deliverer\Model\ORM\DelivererProduct as ChildDelivererProduct;
use Gekosale\Plugin\Deliverer\Model\ORM\DelivererProductQuery;
use Gekosale\Plugin\Deliverer\Model\ORM\Base\DelivererProduct;
use Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProduct as ChildMissingCartProduct;
use Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProductQuery;
use Gekosale\Plugin\MissingCart\Model\ORM\Base\MissingCartProduct;
use Gekosale\Plugin\Order\Model\ORM\OrderProduct as ChildOrderProduct;
use Gekosale\Plugin\Order\Model\ORM\OrderProductQuery;
use Gekosale\Plugin\Order\Model\ORM\Base\OrderProduct;
use Gekosale\Plugin\Producer\Model\ORM\Producer as ChildProducer;
use Gekosale\Plugin\Producer\Model\ORM\ProducerQuery;
use Gekosale\Plugin\ProductGroupPrice\Model\ORM\ProductGroupPrice as ChildProductGroupPrice;
use Gekosale\Plugin\ProductGroupPrice\Model\ORM\ProductGroupPriceQuery;
use Gekosale\Plugin\ProductGroupPrice\Model\ORM\Base\ProductGroupPrice;
use Gekosale\Plugin\ProductNew\Model\ORM\ProductNew as ChildProductNew;
use Gekosale\Plugin\ProductNew\Model\ORM\ProductNewQuery;
use Gekosale\Plugin\ProductNew\Model\ORM\Base\ProductNew;
use Gekosale\Plugin\Product\Model\ORM\Product as ChildProduct;
use Gekosale\Plugin\Product\Model\ORM\ProductCategory as ChildProductCategory;
use Gekosale\Plugin\Product\Model\ORM\ProductCategoryQuery as ChildProductCategoryQuery;
use Gekosale\Plugin\Product\Model\ORM\ProductFile as ChildProductFile;
use Gekosale\Plugin\Product\Model\ORM\ProductFileQuery as ChildProductFileQuery;
use Gekosale\Plugin\Product\Model\ORM\ProductPhoto as ChildProductPhoto;
use Gekosale\Plugin\Product\Model\ORM\ProductPhotoQuery as ChildProductPhotoQuery;
use Gekosale\Plugin\Product\Model\ORM\ProductQuery as ChildProductQuery;
use Gekosale\Plugin\Product\Model\ORM\Map\ProductTableMap;
use Gekosale\Plugin\Similar\Model\ORM\Similar as ChildSimilar;
use Gekosale\Plugin\Similar\Model\ORM\SimilarQuery;
use Gekosale\Plugin\Similar\Model\ORM\Base\Similar;
use Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroup as ChildProductTechnicalDataGroup;
use Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSet as ChildTechnicalDataSet;
use Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroupQuery;
use Gekosale\Plugin\TechnicalData\Model\ORM\TechnicalDataSetQuery;
use Gekosale\Plugin\TechnicalData\Model\ORM\Base\ProductTechnicalDataGroup;
use Gekosale\Plugin\UnitMeasure\Model\ORM\UnitMeasure as ChildUnitMeasure;
use Gekosale\Plugin\UnitMeasure\Model\ORM\UnitMeasureQuery;
use Gekosale\Plugin\Upsell\Model\ORM\Upsell as ChildUpsell;
use Gekosale\Plugin\Upsell\Model\ORM\UpsellQuery;
use Gekosale\Plugin\Upsell\Model\ORM\Base\Upsell;
use Gekosale\Plugin\Vat\Model\ORM\Vat as ChildVat;
use Gekosale\Plugin\Vat\Model\ORM\VatQuery;
use Gekosale\Plugin\Wishlist\Model\ORM\Wishlist as ChildWishlist;
use Gekosale\Plugin\Wishlist\Model\ORM\WishlistQuery;
use Gekosale\Plugin\Wishlist\Model\ORM\Base\Wishlist;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;

abstract class Product implements ActiveRecordInterface 
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Gekosale\\Plugin\\Product\\Model\\ORM\\Map\\ProductTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the deliveler_code field.
     * @var        string
     */
    protected $deliveler_code;

    /**
     * The value for the ean field.
     * @var        string
     */
    protected $ean;

    /**
     * The value for the barcode field.
     * @var        string
     */
    protected $barcode;

    /**
     * The value for the buy_price field.
     * Note: this column has a database default value of: '0.0000'
     * @var        string
     */
    protected $buy_price;

    /**
     * The value for the sell_price field.
     * Note: this column has a database default value of: '0.0000'
     * @var        string
     */
    protected $sell_price;

    /**
     * The value for the producer_id field.
     * @var        int
     */
    protected $producer_id;

    /**
     * The value for the vat_id field.
     * @var        int
     */
    protected $vat_id;

    /**
     * The value for the stock field.
     * @var        int
     */
    protected $stock;

    /**
     * The value for the add_date field.
     * Note: this column has a database default value of: (expression) CURRENT_TIMESTAMP
     * @var        string
     */
    protected $add_date;

    /**
     * The value for the weight field.
     * @var        string
     */
    protected $weight;

    /**
     * The value for the buy_currency_id field.
     * @var        int
     */
    protected $buy_currency_id;

    /**
     * The value for the sell_currency_id field.
     * @var        int
     */
    protected $sell_currency_id;

    /**
     * The value for the technical_data_set_id field.
     * @var        int
     */
    protected $technical_data_set_id;

    /**
     * The value for the track_stock field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $track_stock;

    /**
     * The value for the enable field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $enable;

    /**
     * The value for the promotion field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $promotion;

    /**
     * The value for the discount_price field.
     * Note: this column has a database default value of: '0.0000'
     * @var        string
     */
    protected $discount_price;

    /**
     * The value for the promotion_start field.
     * @var        string
     */
    protected $promotion_start;

    /**
     * The value for the promotion_end field.
     * @var        string
     */
    protected $promotion_end;

    /**
     * The value for the shoped field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $shoped;

    /**
     * The value for the width field.
     * @var        string
     */
    protected $width;

    /**
     * The value for the height field.
     * @var        string
     */
    protected $height;

    /**
     * The value for the deepth field.
     * @var        string
     */
    protected $deepth;

    /**
     * The value for the unit_measure_id field.
     * @var        int
     */
    protected $unit_measure_id;

    /**
     * The value for the disable_at_stock field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $disable_at_stock;

    /**
     * The value for the availability_id field.
     * @var        int
     */
    protected $availability_id;

    /**
     * The value for the hierarchy field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $hierarchy;

    /**
     * The value for the package_size field.
     * Note: this column has a database default value of: '1.0000'
     * @var        string
     */
    protected $package_size;

    /**
     * The value for the disable_at_stock_enabled field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $disable_at_stock_enabled;

    /**
     * @var        Availability
     */
    protected $aAvailability;

    /**
     * @var        Currency
     */
    protected $aCurrencyRelatedByBuyCurrencyId;

    /**
     * @var        Producer
     */
    protected $aProducer;

    /**
     * @var        Currency
     */
    protected $aCurrencyRelatedBySellCurrencyId;

    /**
     * @var        UnitMeasure
     */
    protected $aUnitMeasure;

    /**
     * @var        Vat
     */
    protected $aVat;

    /**
     * @var        TechnicalDataSet
     */
    protected $aTechnicalDataSet;

    /**
     * @var        ObjectCollection|ChildMissingCartProduct[] Collection to store aggregation of ChildMissingCartProduct objects.
     */
    protected $collMissingCartProducts;
    protected $collMissingCartProductsPartial;

    /**
     * @var        ObjectCollection|ChildOrderProduct[] Collection to store aggregation of ChildOrderProduct objects.
     */
    protected $collOrderProducts;
    protected $collOrderProductsPartial;

    /**
     * @var        ObjectCollection|ChildProductAttribute[] Collection to store aggregation of ChildProductAttribute objects.
     */
    protected $collProductAttributes;
    protected $collProductAttributesPartial;

    /**
     * @var        ObjectCollection|ChildProductCategory[] Collection to store aggregation of ChildProductCategory objects.
     */
    protected $collProductCategories;
    protected $collProductCategoriesPartial;

    /**
     * @var        ObjectCollection|ChildDelivererProduct[] Collection to store aggregation of ChildDelivererProduct objects.
     */
    protected $collDelivererProducts;
    protected $collDelivererProductsPartial;

    /**
     * @var        ObjectCollection|ChildProductFile[] Collection to store aggregation of ChildProductFile objects.
     */
    protected $collProductFiles;
    protected $collProductFilesPartial;

    /**
     * @var        ObjectCollection|ChildProductGroupPrice[] Collection to store aggregation of ChildProductGroupPrice objects.
     */
    protected $collProductGroupPrices;
    protected $collProductGroupPricesPartial;

    /**
     * @var        ObjectCollection|ChildProductNew[] Collection to store aggregation of ChildProductNew objects.
     */
    protected $collProductNews;
    protected $collProductNewsPartial;

    /**
     * @var        ObjectCollection|ChildProductPhoto[] Collection to store aggregation of ChildProductPhoto objects.
     */
    protected $collProductPhotos;
    protected $collProductPhotosPartial;

    /**
     * @var        ObjectCollection|ChildCrosssell[] Collection to store aggregation of ChildCrosssell objects.
     */
    protected $collCrosssellsRelatedByProductId;
    protected $collCrosssellsRelatedByProductIdPartial;

    /**
     * @var        ObjectCollection|ChildCrosssell[] Collection to store aggregation of ChildCrosssell objects.
     */
    protected $collCrosssellsRelatedByRelatedProductId;
    protected $collCrosssellsRelatedByRelatedProductIdPartial;

    /**
     * @var        ObjectCollection|ChildSimilar[] Collection to store aggregation of ChildSimilar objects.
     */
    protected $collSimilarsRelatedByProductId;
    protected $collSimilarsRelatedByProductIdPartial;

    /**
     * @var        ObjectCollection|ChildSimilar[] Collection to store aggregation of ChildSimilar objects.
     */
    protected $collSimilarsRelatedByRelatedProductId;
    protected $collSimilarsRelatedByRelatedProductIdPartial;

    /**
     * @var        ObjectCollection|ChildUpsell[] Collection to store aggregation of ChildUpsell objects.
     */
    protected $collUpsellsRelatedByProductId;
    protected $collUpsellsRelatedByProductIdPartial;

    /**
     * @var        ObjectCollection|ChildUpsell[] Collection to store aggregation of ChildUpsell objects.
     */
    protected $collUpsellsRelatedByRelatedProductId;
    protected $collUpsellsRelatedByRelatedProductIdPartial;

    /**
     * @var        ObjectCollection|ChildProductTechnicalDataGroup[] Collection to store aggregation of ChildProductTechnicalDataGroup objects.
     */
    protected $collProductTechnicalDataGroups;
    protected $collProductTechnicalDataGroupsPartial;

    /**
     * @var        ObjectCollection|ChildWishlist[] Collection to store aggregation of ChildWishlist objects.
     */
    protected $collWishlists;
    protected $collWishlistsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $missingCartProductsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $orderProductsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $productAttributesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $productCategoriesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $delivererProductsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $productFilesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $productGroupPricesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $productNewsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $productPhotosScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $crosssellsRelatedByProductIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $crosssellsRelatedByRelatedProductIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $similarsRelatedByProductIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $similarsRelatedByRelatedProductIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $upsellsRelatedByProductIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $upsellsRelatedByRelatedProductIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $productTechnicalDataGroupsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $wishlistsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->buy_price = '0.0000';
        $this->sell_price = '0.0000';
        $this->track_stock = 1;
        $this->enable = 1;
        $this->promotion = 0;
        $this->discount_price = '0.0000';
        $this->shoped = 0;
        $this->disable_at_stock = 0;
        $this->hierarchy = 0;
        $this->package_size = '1.0000';
        $this->disable_at_stock_enabled = 0;
    }

    /**
     * Initializes internal state of Gekosale\Plugin\Product\Model\ORM\Base\Product object.
     * @see applyDefaults()
     */
    public function __construct()
    {
        $this->applyDefaultValues();
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (Boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (Boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>Product</code> instance.  If
     * <code>obj</code> is an instance of <code>Product</code>, delegates to
     * <code>equals(Product)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        $thisclazz = get_class($this);
        if (!is_object($obj) || !($obj instanceof $thisclazz)) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey()
            || null === $obj->getPrimaryKey())  {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        if (null !== $this->getPrimaryKey()) {
            return crc32(serialize($this->getPrimaryKey()));
        }

        return crc32(serialize(clone $this));
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return Product The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     *
     * @return Product The current object, for fluid interface
     */
    public function importFrom($parser, $data)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), TableMap::TYPE_PHPNAME);

        return $this;
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        return array_keys(get_object_vars($this));
    }

    /**
     * Get the [id] column value.
     * 
     * @return   int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [deliveler_code] column value.
     * 
     * @return   string
     */
    public function getDelivelerCode()
    {

        return $this->deliveler_code;
    }

    /**
     * Get the [ean] column value.
     * 
     * @return   string
     */
    public function getEan()
    {

        return $this->ean;
    }

    /**
     * Get the [barcode] column value.
     * 
     * @return   string
     */
    public function getBarcode()
    {

        return $this->barcode;
    }

    /**
     * Get the [buy_price] column value.
     * 
     * @return   string
     */
    public function getBuyPrice()
    {

        return $this->buy_price;
    }

    /**
     * Get the [sell_price] column value.
     * 
     * @return   string
     */
    public function getSellPrice()
    {

        return $this->sell_price;
    }

    /**
     * Get the [producer_id] column value.
     * 
     * @return   int
     */
    public function getProducerId()
    {

        return $this->producer_id;
    }

    /**
     * Get the [vat_id] column value.
     * 
     * @return   int
     */
    public function getVatId()
    {

        return $this->vat_id;
    }

    /**
     * Get the [stock] column value.
     * 
     * @return   int
     */
    public function getStock()
    {

        return $this->stock;
    }

    /**
     * Get the [optionally formatted] temporal [add_date] column value.
     * 
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getAddDate($format = NULL)
    {
        if ($format === null) {
            return $this->add_date;
        } else {
            return $this->add_date instanceof \DateTime ? $this->add_date->format($format) : null;
        }
    }

    /**
     * Get the [weight] column value.
     * 
     * @return   string
     */
    public function getWeight()
    {

        return $this->weight;
    }

    /**
     * Get the [buy_currency_id] column value.
     * 
     * @return   int
     */
    public function getBuyCurrencyId()
    {

        return $this->buy_currency_id;
    }

    /**
     * Get the [sell_currency_id] column value.
     * 
     * @return   int
     */
    public function getSellCurrencyId()
    {

        return $this->sell_currency_id;
    }

    /**
     * Get the [technical_data_set_id] column value.
     * 
     * @return   int
     */
    public function getTechnicalDataSetId()
    {

        return $this->technical_data_set_id;
    }

    /**
     * Get the [track_stock] column value.
     * 
     * @return   int
     */
    public function getTrackStock()
    {

        return $this->track_stock;
    }

    /**
     * Get the [enable] column value.
     * 
     * @return   int
     */
    public function getEnable()
    {

        return $this->enable;
    }

    /**
     * Get the [promotion] column value.
     * 
     * @return   int
     */
    public function getPromotion()
    {

        return $this->promotion;
    }

    /**
     * Get the [discount_price] column value.
     * 
     * @return   string
     */
    public function getDiscountPrice()
    {

        return $this->discount_price;
    }

    /**
     * Get the [optionally formatted] temporal [promotion_start] column value.
     * 
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getPromotionStart($format = NULL)
    {
        if ($format === null) {
            return $this->promotion_start;
        } else {
            return $this->promotion_start instanceof \DateTime ? $this->promotion_start->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [promotion_end] column value.
     * 
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getPromotionEnd($format = NULL)
    {
        if ($format === null) {
            return $this->promotion_end;
        } else {
            return $this->promotion_end instanceof \DateTime ? $this->promotion_end->format($format) : null;
        }
    }

    /**
     * Get the [shoped] column value.
     * 
     * @return   int
     */
    public function getShoped()
    {

        return $this->shoped;
    }

    /**
     * Get the [width] column value.
     * 
     * @return   string
     */
    public function getWidth()
    {

        return $this->width;
    }

    /**
     * Get the [height] column value.
     * 
     * @return   string
     */
    public function getHeight()
    {

        return $this->height;
    }

    /**
     * Get the [deepth] column value.
     * 
     * @return   string
     */
    public function getDeepth()
    {

        return $this->deepth;
    }

    /**
     * Get the [unit_measure_id] column value.
     * 
     * @return   int
     */
    public function getUnitMeasureId()
    {

        return $this->unit_measure_id;
    }

    /**
     * Get the [disable_at_stock] column value.
     * 
     * @return   int
     */
    public function getDisableAtStock()
    {

        return $this->disable_at_stock;
    }

    /**
     * Get the [availability_id] column value.
     * 
     * @return   int
     */
    public function getAvailabilityId()
    {

        return $this->availability_id;
    }

    /**
     * Get the [hierarchy] column value.
     * 
     * @return   int
     */
    public function getHierarchy()
    {

        return $this->hierarchy;
    }

    /**
     * Get the [package_size] column value.
     * 
     * @return   string
     */
    public function getPackageSize()
    {

        return $this->package_size;
    }

    /**
     * Get the [disable_at_stock_enabled] column value.
     * 
     * @return   int
     */
    public function getDisableAtStockEnabled()
    {

        return $this->disable_at_stock_enabled;
    }

    /**
     * Set the value of [id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[ProductTableMap::COL_ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [deliveler_code] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setDelivelerCode($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->deliveler_code !== $v) {
            $this->deliveler_code = $v;
            $this->modifiedColumns[ProductTableMap::COL_DELIVELER_CODE] = true;
        }


        return $this;
    } // setDelivelerCode()

    /**
     * Set the value of [ean] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setEan($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->ean !== $v) {
            $this->ean = $v;
            $this->modifiedColumns[ProductTableMap::COL_EAN] = true;
        }


        return $this;
    } // setEan()

    /**
     * Set the value of [barcode] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setBarcode($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->barcode !== $v) {
            $this->barcode = $v;
            $this->modifiedColumns[ProductTableMap::COL_BARCODE] = true;
        }


        return $this;
    } // setBarcode()

    /**
     * Set the value of [buy_price] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setBuyPrice($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->buy_price !== $v) {
            $this->buy_price = $v;
            $this->modifiedColumns[ProductTableMap::COL_BUY_PRICE] = true;
        }


        return $this;
    } // setBuyPrice()

    /**
     * Set the value of [sell_price] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setSellPrice($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->sell_price !== $v) {
            $this->sell_price = $v;
            $this->modifiedColumns[ProductTableMap::COL_SELL_PRICE] = true;
        }


        return $this;
    } // setSellPrice()

    /**
     * Set the value of [producer_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setProducerId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->producer_id !== $v) {
            $this->producer_id = $v;
            $this->modifiedColumns[ProductTableMap::COL_PRODUCER_ID] = true;
        }

        if ($this->aProducer !== null && $this->aProducer->getId() !== $v) {
            $this->aProducer = null;
        }


        return $this;
    } // setProducerId()

    /**
     * Set the value of [vat_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setVatId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->vat_id !== $v) {
            $this->vat_id = $v;
            $this->modifiedColumns[ProductTableMap::COL_VAT_ID] = true;
        }

        if ($this->aVat !== null && $this->aVat->getId() !== $v) {
            $this->aVat = null;
        }


        return $this;
    } // setVatId()

    /**
     * Set the value of [stock] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setStock($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->stock !== $v) {
            $this->stock = $v;
            $this->modifiedColumns[ProductTableMap::COL_STOCK] = true;
        }


        return $this;
    } // setStock()

    /**
     * Sets the value of [add_date] column to a normalized version of the date/time value specified.
     * 
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setAddDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->add_date !== null || $dt !== null) {
            if ($dt !== $this->add_date) {
                $this->add_date = $dt;
                $this->modifiedColumns[ProductTableMap::COL_ADD_DATE] = true;
            }
        } // if either are not null


        return $this;
    } // setAddDate()

    /**
     * Set the value of [weight] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setWeight($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->weight !== $v) {
            $this->weight = $v;
            $this->modifiedColumns[ProductTableMap::COL_WEIGHT] = true;
        }


        return $this;
    } // setWeight()

    /**
     * Set the value of [buy_currency_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setBuyCurrencyId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->buy_currency_id !== $v) {
            $this->buy_currency_id = $v;
            $this->modifiedColumns[ProductTableMap::COL_BUY_CURRENCY_ID] = true;
        }

        if ($this->aCurrencyRelatedByBuyCurrencyId !== null && $this->aCurrencyRelatedByBuyCurrencyId->getId() !== $v) {
            $this->aCurrencyRelatedByBuyCurrencyId = null;
        }


        return $this;
    } // setBuyCurrencyId()

    /**
     * Set the value of [sell_currency_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setSellCurrencyId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->sell_currency_id !== $v) {
            $this->sell_currency_id = $v;
            $this->modifiedColumns[ProductTableMap::COL_SELL_CURRENCY_ID] = true;
        }

        if ($this->aCurrencyRelatedBySellCurrencyId !== null && $this->aCurrencyRelatedBySellCurrencyId->getId() !== $v) {
            $this->aCurrencyRelatedBySellCurrencyId = null;
        }


        return $this;
    } // setSellCurrencyId()

    /**
     * Set the value of [technical_data_set_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setTechnicalDataSetId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->technical_data_set_id !== $v) {
            $this->technical_data_set_id = $v;
            $this->modifiedColumns[ProductTableMap::COL_TECHNICAL_DATA_SET_ID] = true;
        }

        if ($this->aTechnicalDataSet !== null && $this->aTechnicalDataSet->getId() !== $v) {
            $this->aTechnicalDataSet = null;
        }


        return $this;
    } // setTechnicalDataSetId()

    /**
     * Set the value of [track_stock] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setTrackStock($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->track_stock !== $v) {
            $this->track_stock = $v;
            $this->modifiedColumns[ProductTableMap::COL_TRACK_STOCK] = true;
        }


        return $this;
    } // setTrackStock()

    /**
     * Set the value of [enable] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setEnable($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->enable !== $v) {
            $this->enable = $v;
            $this->modifiedColumns[ProductTableMap::COL_ENABLE] = true;
        }


        return $this;
    } // setEnable()

    /**
     * Set the value of [promotion] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setPromotion($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->promotion !== $v) {
            $this->promotion = $v;
            $this->modifiedColumns[ProductTableMap::COL_PROMOTION] = true;
        }


        return $this;
    } // setPromotion()

    /**
     * Set the value of [discount_price] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setDiscountPrice($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->discount_price !== $v) {
            $this->discount_price = $v;
            $this->modifiedColumns[ProductTableMap::COL_DISCOUNT_PRICE] = true;
        }


        return $this;
    } // setDiscountPrice()

    /**
     * Sets the value of [promotion_start] column to a normalized version of the date/time value specified.
     * 
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setPromotionStart($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->promotion_start !== null || $dt !== null) {
            if ($dt !== $this->promotion_start) {
                $this->promotion_start = $dt;
                $this->modifiedColumns[ProductTableMap::COL_PROMOTION_START] = true;
            }
        } // if either are not null


        return $this;
    } // setPromotionStart()

    /**
     * Sets the value of [promotion_end] column to a normalized version of the date/time value specified.
     * 
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setPromotionEnd($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->promotion_end !== null || $dt !== null) {
            if ($dt !== $this->promotion_end) {
                $this->promotion_end = $dt;
                $this->modifiedColumns[ProductTableMap::COL_PROMOTION_END] = true;
            }
        } // if either are not null


        return $this;
    } // setPromotionEnd()

    /**
     * Set the value of [shoped] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setShoped($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->shoped !== $v) {
            $this->shoped = $v;
            $this->modifiedColumns[ProductTableMap::COL_SHOPED] = true;
        }


        return $this;
    } // setShoped()

    /**
     * Set the value of [width] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setWidth($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->width !== $v) {
            $this->width = $v;
            $this->modifiedColumns[ProductTableMap::COL_WIDTH] = true;
        }


        return $this;
    } // setWidth()

    /**
     * Set the value of [height] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setHeight($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->height !== $v) {
            $this->height = $v;
            $this->modifiedColumns[ProductTableMap::COL_HEIGHT] = true;
        }


        return $this;
    } // setHeight()

    /**
     * Set the value of [deepth] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setDeepth($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->deepth !== $v) {
            $this->deepth = $v;
            $this->modifiedColumns[ProductTableMap::COL_DEEPTH] = true;
        }


        return $this;
    } // setDeepth()

    /**
     * Set the value of [unit_measure_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setUnitMeasureId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->unit_measure_id !== $v) {
            $this->unit_measure_id = $v;
            $this->modifiedColumns[ProductTableMap::COL_UNIT_MEASURE_ID] = true;
        }

        if ($this->aUnitMeasure !== null && $this->aUnitMeasure->getId() !== $v) {
            $this->aUnitMeasure = null;
        }


        return $this;
    } // setUnitMeasureId()

    /**
     * Set the value of [disable_at_stock] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setDisableAtStock($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->disable_at_stock !== $v) {
            $this->disable_at_stock = $v;
            $this->modifiedColumns[ProductTableMap::COL_DISABLE_AT_STOCK] = true;
        }


        return $this;
    } // setDisableAtStock()

    /**
     * Set the value of [availability_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setAvailabilityId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->availability_id !== $v) {
            $this->availability_id = $v;
            $this->modifiedColumns[ProductTableMap::COL_AVAILABILITY_ID] = true;
        }

        if ($this->aAvailability !== null && $this->aAvailability->getId() !== $v) {
            $this->aAvailability = null;
        }


        return $this;
    } // setAvailabilityId()

    /**
     * Set the value of [hierarchy] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setHierarchy($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->hierarchy !== $v) {
            $this->hierarchy = $v;
            $this->modifiedColumns[ProductTableMap::COL_HIERARCHY] = true;
        }


        return $this;
    } // setHierarchy()

    /**
     * Set the value of [package_size] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setPackageSize($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->package_size !== $v) {
            $this->package_size = $v;
            $this->modifiedColumns[ProductTableMap::COL_PACKAGE_SIZE] = true;
        }


        return $this;
    } // setPackageSize()

    /**
     * Set the value of [disable_at_stock_enabled] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function setDisableAtStockEnabled($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->disable_at_stock_enabled !== $v) {
            $this->disable_at_stock_enabled = $v;
            $this->modifiedColumns[ProductTableMap::COL_DISABLE_AT_STOCK_ENABLED] = true;
        }


        return $this;
    } // setDisableAtStockEnabled()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
            if ($this->buy_price !== '0.0000') {
                return false;
            }

            if ($this->sell_price !== '0.0000') {
                return false;
            }

            if ($this->track_stock !== 1) {
                return false;
            }

            if ($this->enable !== 1) {
                return false;
            }

            if ($this->promotion !== 0) {
                return false;
            }

            if ($this->discount_price !== '0.0000') {
                return false;
            }

            if ($this->shoped !== 0) {
                return false;
            }

            if ($this->disable_at_stock !== 0) {
                return false;
            }

            if ($this->hierarchy !== 0) {
                return false;
            }

            if ($this->package_size !== '1.0000') {
                return false;
            }

            if ($this->disable_at_stock_enabled !== 0) {
                return false;
            }

        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : ProductTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : ProductTableMap::translateFieldName('DelivelerCode', TableMap::TYPE_PHPNAME, $indexType)];
            $this->deliveler_code = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : ProductTableMap::translateFieldName('Ean', TableMap::TYPE_PHPNAME, $indexType)];
            $this->ean = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : ProductTableMap::translateFieldName('Barcode', TableMap::TYPE_PHPNAME, $indexType)];
            $this->barcode = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : ProductTableMap::translateFieldName('BuyPrice', TableMap::TYPE_PHPNAME, $indexType)];
            $this->buy_price = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : ProductTableMap::translateFieldName('SellPrice', TableMap::TYPE_PHPNAME, $indexType)];
            $this->sell_price = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : ProductTableMap::translateFieldName('ProducerId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->producer_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : ProductTableMap::translateFieldName('VatId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->vat_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : ProductTableMap::translateFieldName('Stock', TableMap::TYPE_PHPNAME, $indexType)];
            $this->stock = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 9 + $startcol : ProductTableMap::translateFieldName('AddDate', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->add_date = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 10 + $startcol : ProductTableMap::translateFieldName('Weight', TableMap::TYPE_PHPNAME, $indexType)];
            $this->weight = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 11 + $startcol : ProductTableMap::translateFieldName('BuyCurrencyId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->buy_currency_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 12 + $startcol : ProductTableMap::translateFieldName('SellCurrencyId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->sell_currency_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 13 + $startcol : ProductTableMap::translateFieldName('TechnicalDataSetId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->technical_data_set_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 14 + $startcol : ProductTableMap::translateFieldName('TrackStock', TableMap::TYPE_PHPNAME, $indexType)];
            $this->track_stock = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 15 + $startcol : ProductTableMap::translateFieldName('Enable', TableMap::TYPE_PHPNAME, $indexType)];
            $this->enable = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 16 + $startcol : ProductTableMap::translateFieldName('Promotion', TableMap::TYPE_PHPNAME, $indexType)];
            $this->promotion = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 17 + $startcol : ProductTableMap::translateFieldName('DiscountPrice', TableMap::TYPE_PHPNAME, $indexType)];
            $this->discount_price = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 18 + $startcol : ProductTableMap::translateFieldName('PromotionStart', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00') {
                $col = null;
            }
            $this->promotion_start = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 19 + $startcol : ProductTableMap::translateFieldName('PromotionEnd', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00') {
                $col = null;
            }
            $this->promotion_end = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 20 + $startcol : ProductTableMap::translateFieldName('Shoped', TableMap::TYPE_PHPNAME, $indexType)];
            $this->shoped = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 21 + $startcol : ProductTableMap::translateFieldName('Width', TableMap::TYPE_PHPNAME, $indexType)];
            $this->width = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 22 + $startcol : ProductTableMap::translateFieldName('Height', TableMap::TYPE_PHPNAME, $indexType)];
            $this->height = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 23 + $startcol : ProductTableMap::translateFieldName('Deepth', TableMap::TYPE_PHPNAME, $indexType)];
            $this->deepth = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 24 + $startcol : ProductTableMap::translateFieldName('UnitMeasureId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->unit_measure_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 25 + $startcol : ProductTableMap::translateFieldName('DisableAtStock', TableMap::TYPE_PHPNAME, $indexType)];
            $this->disable_at_stock = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 26 + $startcol : ProductTableMap::translateFieldName('AvailabilityId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->availability_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 27 + $startcol : ProductTableMap::translateFieldName('Hierarchy', TableMap::TYPE_PHPNAME, $indexType)];
            $this->hierarchy = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 28 + $startcol : ProductTableMap::translateFieldName('PackageSize', TableMap::TYPE_PHPNAME, $indexType)];
            $this->package_size = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 29 + $startcol : ProductTableMap::translateFieldName('DisableAtStockEnabled', TableMap::TYPE_PHPNAME, $indexType)];
            $this->disable_at_stock_enabled = (null !== $col) ? (int) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 30; // 30 = ProductTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Gekosale\Plugin\Product\Model\ORM\Product object", 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
        if ($this->aProducer !== null && $this->producer_id !== $this->aProducer->getId()) {
            $this->aProducer = null;
        }
        if ($this->aVat !== null && $this->vat_id !== $this->aVat->getId()) {
            $this->aVat = null;
        }
        if ($this->aCurrencyRelatedByBuyCurrencyId !== null && $this->buy_currency_id !== $this->aCurrencyRelatedByBuyCurrencyId->getId()) {
            $this->aCurrencyRelatedByBuyCurrencyId = null;
        }
        if ($this->aCurrencyRelatedBySellCurrencyId !== null && $this->sell_currency_id !== $this->aCurrencyRelatedBySellCurrencyId->getId()) {
            $this->aCurrencyRelatedBySellCurrencyId = null;
        }
        if ($this->aTechnicalDataSet !== null && $this->technical_data_set_id !== $this->aTechnicalDataSet->getId()) {
            $this->aTechnicalDataSet = null;
        }
        if ($this->aUnitMeasure !== null && $this->unit_measure_id !== $this->aUnitMeasure->getId()) {
            $this->aUnitMeasure = null;
        }
        if ($this->aAvailability !== null && $this->availability_id !== $this->aAvailability->getId()) {
            $this->aAvailability = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ProductTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildProductQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aAvailability = null;
            $this->aCurrencyRelatedByBuyCurrencyId = null;
            $this->aProducer = null;
            $this->aCurrencyRelatedBySellCurrencyId = null;
            $this->aUnitMeasure = null;
            $this->aVat = null;
            $this->aTechnicalDataSet = null;
            $this->collMissingCartProducts = null;

            $this->collOrderProducts = null;

            $this->collProductAttributes = null;

            $this->collProductCategories = null;

            $this->collDelivererProducts = null;

            $this->collProductFiles = null;

            $this->collProductGroupPrices = null;

            $this->collProductNews = null;

            $this->collProductPhotos = null;

            $this->collCrosssellsRelatedByProductId = null;

            $this->collCrosssellsRelatedByRelatedProductId = null;

            $this->collSimilarsRelatedByProductId = null;

            $this->collSimilarsRelatedByRelatedProductId = null;

            $this->collUpsellsRelatedByProductId = null;

            $this->collUpsellsRelatedByRelatedProductId = null;

            $this->collProductTechnicalDataGroups = null;

            $this->collWishlists = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Product::setDeleted()
     * @see Product::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProductTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildProductQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProductTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                ProductTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aAvailability !== null) {
                if ($this->aAvailability->isModified() || $this->aAvailability->isNew()) {
                    $affectedRows += $this->aAvailability->save($con);
                }
                $this->setAvailability($this->aAvailability);
            }

            if ($this->aCurrencyRelatedByBuyCurrencyId !== null) {
                if ($this->aCurrencyRelatedByBuyCurrencyId->isModified() || $this->aCurrencyRelatedByBuyCurrencyId->isNew()) {
                    $affectedRows += $this->aCurrencyRelatedByBuyCurrencyId->save($con);
                }
                $this->setCurrencyRelatedByBuyCurrencyId($this->aCurrencyRelatedByBuyCurrencyId);
            }

            if ($this->aProducer !== null) {
                if ($this->aProducer->isModified() || $this->aProducer->isNew()) {
                    $affectedRows += $this->aProducer->save($con);
                }
                $this->setProducer($this->aProducer);
            }

            if ($this->aCurrencyRelatedBySellCurrencyId !== null) {
                if ($this->aCurrencyRelatedBySellCurrencyId->isModified() || $this->aCurrencyRelatedBySellCurrencyId->isNew()) {
                    $affectedRows += $this->aCurrencyRelatedBySellCurrencyId->save($con);
                }
                $this->setCurrencyRelatedBySellCurrencyId($this->aCurrencyRelatedBySellCurrencyId);
            }

            if ($this->aUnitMeasure !== null) {
                if ($this->aUnitMeasure->isModified() || $this->aUnitMeasure->isNew()) {
                    $affectedRows += $this->aUnitMeasure->save($con);
                }
                $this->setUnitMeasure($this->aUnitMeasure);
            }

            if ($this->aVat !== null) {
                if ($this->aVat->isModified() || $this->aVat->isNew()) {
                    $affectedRows += $this->aVat->save($con);
                }
                $this->setVat($this->aVat);
            }

            if ($this->aTechnicalDataSet !== null) {
                if ($this->aTechnicalDataSet->isModified() || $this->aTechnicalDataSet->isNew()) {
                    $affectedRows += $this->aTechnicalDataSet->save($con);
                }
                $this->setTechnicalDataSet($this->aTechnicalDataSet);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->missingCartProductsScheduledForDeletion !== null) {
                if (!$this->missingCartProductsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProductQuery::create()
                        ->filterByPrimaryKeys($this->missingCartProductsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->missingCartProductsScheduledForDeletion = null;
                }
            }

                if ($this->collMissingCartProducts !== null) {
            foreach ($this->collMissingCartProducts as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->orderProductsScheduledForDeletion !== null) {
                if (!$this->orderProductsScheduledForDeletion->isEmpty()) {
                    foreach ($this->orderProductsScheduledForDeletion as $orderProduct) {
                        // need to save related object because we set the relation to null
                        $orderProduct->save($con);
                    }
                    $this->orderProductsScheduledForDeletion = null;
                }
            }

                if ($this->collOrderProducts !== null) {
            foreach ($this->collOrderProducts as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->productAttributesScheduledForDeletion !== null) {
                if (!$this->productAttributesScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeQuery::create()
                        ->filterByPrimaryKeys($this->productAttributesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->productAttributesScheduledForDeletion = null;
                }
            }

                if ($this->collProductAttributes !== null) {
            foreach ($this->collProductAttributes as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->productCategoriesScheduledForDeletion !== null) {
                if (!$this->productCategoriesScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Product\Model\ORM\ProductCategoryQuery::create()
                        ->filterByPrimaryKeys($this->productCategoriesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->productCategoriesScheduledForDeletion = null;
                }
            }

                if ($this->collProductCategories !== null) {
            foreach ($this->collProductCategories as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->delivererProductsScheduledForDeletion !== null) {
                if (!$this->delivererProductsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Deliverer\Model\ORM\DelivererProductQuery::create()
                        ->filterByPrimaryKeys($this->delivererProductsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->delivererProductsScheduledForDeletion = null;
                }
            }

                if ($this->collDelivererProducts !== null) {
            foreach ($this->collDelivererProducts as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->productFilesScheduledForDeletion !== null) {
                if (!$this->productFilesScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Product\Model\ORM\ProductFileQuery::create()
                        ->filterByPrimaryKeys($this->productFilesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->productFilesScheduledForDeletion = null;
                }
            }

                if ($this->collProductFiles !== null) {
            foreach ($this->collProductFiles as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->productGroupPricesScheduledForDeletion !== null) {
                if (!$this->productGroupPricesScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\ProductGroupPrice\Model\ORM\ProductGroupPriceQuery::create()
                        ->filterByPrimaryKeys($this->productGroupPricesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->productGroupPricesScheduledForDeletion = null;
                }
            }

                if ($this->collProductGroupPrices !== null) {
            foreach ($this->collProductGroupPrices as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->productNewsScheduledForDeletion !== null) {
                if (!$this->productNewsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\ProductNew\Model\ORM\ProductNewQuery::create()
                        ->filterByPrimaryKeys($this->productNewsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->productNewsScheduledForDeletion = null;
                }
            }

                if ($this->collProductNews !== null) {
            foreach ($this->collProductNews as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->productPhotosScheduledForDeletion !== null) {
                if (!$this->productPhotosScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Product\Model\ORM\ProductPhotoQuery::create()
                        ->filterByPrimaryKeys($this->productPhotosScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->productPhotosScheduledForDeletion = null;
                }
            }

                if ($this->collProductPhotos !== null) {
            foreach ($this->collProductPhotos as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->crosssellsRelatedByProductIdScheduledForDeletion !== null) {
                if (!$this->crosssellsRelatedByProductIdScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Crosssell\Model\ORM\CrosssellQuery::create()
                        ->filterByPrimaryKeys($this->crosssellsRelatedByProductIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->crosssellsRelatedByProductIdScheduledForDeletion = null;
                }
            }

                if ($this->collCrosssellsRelatedByProductId !== null) {
            foreach ($this->collCrosssellsRelatedByProductId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->crosssellsRelatedByRelatedProductIdScheduledForDeletion !== null) {
                if (!$this->crosssellsRelatedByRelatedProductIdScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Crosssell\Model\ORM\CrosssellQuery::create()
                        ->filterByPrimaryKeys($this->crosssellsRelatedByRelatedProductIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->crosssellsRelatedByRelatedProductIdScheduledForDeletion = null;
                }
            }

                if ($this->collCrosssellsRelatedByRelatedProductId !== null) {
            foreach ($this->collCrosssellsRelatedByRelatedProductId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->similarsRelatedByProductIdScheduledForDeletion !== null) {
                if (!$this->similarsRelatedByProductIdScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Similar\Model\ORM\SimilarQuery::create()
                        ->filterByPrimaryKeys($this->similarsRelatedByProductIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->similarsRelatedByProductIdScheduledForDeletion = null;
                }
            }

                if ($this->collSimilarsRelatedByProductId !== null) {
            foreach ($this->collSimilarsRelatedByProductId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->similarsRelatedByRelatedProductIdScheduledForDeletion !== null) {
                if (!$this->similarsRelatedByRelatedProductIdScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Similar\Model\ORM\SimilarQuery::create()
                        ->filterByPrimaryKeys($this->similarsRelatedByRelatedProductIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->similarsRelatedByRelatedProductIdScheduledForDeletion = null;
                }
            }

                if ($this->collSimilarsRelatedByRelatedProductId !== null) {
            foreach ($this->collSimilarsRelatedByRelatedProductId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->upsellsRelatedByProductIdScheduledForDeletion !== null) {
                if (!$this->upsellsRelatedByProductIdScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Upsell\Model\ORM\UpsellQuery::create()
                        ->filterByPrimaryKeys($this->upsellsRelatedByProductIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->upsellsRelatedByProductIdScheduledForDeletion = null;
                }
            }

                if ($this->collUpsellsRelatedByProductId !== null) {
            foreach ($this->collUpsellsRelatedByProductId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->upsellsRelatedByRelatedProductIdScheduledForDeletion !== null) {
                if (!$this->upsellsRelatedByRelatedProductIdScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Upsell\Model\ORM\UpsellQuery::create()
                        ->filterByPrimaryKeys($this->upsellsRelatedByRelatedProductIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->upsellsRelatedByRelatedProductIdScheduledForDeletion = null;
                }
            }

                if ($this->collUpsellsRelatedByRelatedProductId !== null) {
            foreach ($this->collUpsellsRelatedByRelatedProductId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->productTechnicalDataGroupsScheduledForDeletion !== null) {
                if (!$this->productTechnicalDataGroupsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroupQuery::create()
                        ->filterByPrimaryKeys($this->productTechnicalDataGroupsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->productTechnicalDataGroupsScheduledForDeletion = null;
                }
            }

                if ($this->collProductTechnicalDataGroups !== null) {
            foreach ($this->collProductTechnicalDataGroups as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->wishlistsScheduledForDeletion !== null) {
                if (!$this->wishlistsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Wishlist\Model\ORM\WishlistQuery::create()
                        ->filterByPrimaryKeys($this->wishlistsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->wishlistsScheduledForDeletion = null;
                }
            }

                if ($this->collWishlists !== null) {
            foreach ($this->collWishlists as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[ProductTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . ProductTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ProductTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(ProductTableMap::COL_DELIVELER_CODE)) {
            $modifiedColumns[':p' . $index++]  = 'DELIVELER_CODE';
        }
        if ($this->isColumnModified(ProductTableMap::COL_EAN)) {
            $modifiedColumns[':p' . $index++]  = 'EAN';
        }
        if ($this->isColumnModified(ProductTableMap::COL_BARCODE)) {
            $modifiedColumns[':p' . $index++]  = 'BARCODE';
        }
        if ($this->isColumnModified(ProductTableMap::COL_BUY_PRICE)) {
            $modifiedColumns[':p' . $index++]  = 'BUY_PRICE';
        }
        if ($this->isColumnModified(ProductTableMap::COL_SELL_PRICE)) {
            $modifiedColumns[':p' . $index++]  = 'SELL_PRICE';
        }
        if ($this->isColumnModified(ProductTableMap::COL_PRODUCER_ID)) {
            $modifiedColumns[':p' . $index++]  = 'PRODUCER_ID';
        }
        if ($this->isColumnModified(ProductTableMap::COL_VAT_ID)) {
            $modifiedColumns[':p' . $index++]  = 'VAT_ID';
        }
        if ($this->isColumnModified(ProductTableMap::COL_STOCK)) {
            $modifiedColumns[':p' . $index++]  = 'STOCK';
        }
        if ($this->isColumnModified(ProductTableMap::COL_ADD_DATE)) {
            $modifiedColumns[':p' . $index++]  = 'ADD_DATE';
        }
        if ($this->isColumnModified(ProductTableMap::COL_WEIGHT)) {
            $modifiedColumns[':p' . $index++]  = 'WEIGHT';
        }
        if ($this->isColumnModified(ProductTableMap::COL_BUY_CURRENCY_ID)) {
            $modifiedColumns[':p' . $index++]  = 'BUY_CURRENCY_ID';
        }
        if ($this->isColumnModified(ProductTableMap::COL_SELL_CURRENCY_ID)) {
            $modifiedColumns[':p' . $index++]  = 'SELL_CURRENCY_ID';
        }
        if ($this->isColumnModified(ProductTableMap::COL_TECHNICAL_DATA_SET_ID)) {
            $modifiedColumns[':p' . $index++]  = 'TECHNICAL_DATA_SET_ID';
        }
        if ($this->isColumnModified(ProductTableMap::COL_TRACK_STOCK)) {
            $modifiedColumns[':p' . $index++]  = 'TRACK_STOCK';
        }
        if ($this->isColumnModified(ProductTableMap::COL_ENABLE)) {
            $modifiedColumns[':p' . $index++]  = 'ENABLE';
        }
        if ($this->isColumnModified(ProductTableMap::COL_PROMOTION)) {
            $modifiedColumns[':p' . $index++]  = 'PROMOTION';
        }
        if ($this->isColumnModified(ProductTableMap::COL_DISCOUNT_PRICE)) {
            $modifiedColumns[':p' . $index++]  = 'DISCOUNT_PRICE';
        }
        if ($this->isColumnModified(ProductTableMap::COL_PROMOTION_START)) {
            $modifiedColumns[':p' . $index++]  = 'PROMOTION_START';
        }
        if ($this->isColumnModified(ProductTableMap::COL_PROMOTION_END)) {
            $modifiedColumns[':p' . $index++]  = 'PROMOTION_END';
        }
        if ($this->isColumnModified(ProductTableMap::COL_SHOPED)) {
            $modifiedColumns[':p' . $index++]  = 'SHOPED';
        }
        if ($this->isColumnModified(ProductTableMap::COL_WIDTH)) {
            $modifiedColumns[':p' . $index++]  = 'WIDTH';
        }
        if ($this->isColumnModified(ProductTableMap::COL_HEIGHT)) {
            $modifiedColumns[':p' . $index++]  = 'HEIGHT';
        }
        if ($this->isColumnModified(ProductTableMap::COL_DEEPTH)) {
            $modifiedColumns[':p' . $index++]  = 'DEEPTH';
        }
        if ($this->isColumnModified(ProductTableMap::COL_UNIT_MEASURE_ID)) {
            $modifiedColumns[':p' . $index++]  = 'UNIT_MEASURE_ID';
        }
        if ($this->isColumnModified(ProductTableMap::COL_DISABLE_AT_STOCK)) {
            $modifiedColumns[':p' . $index++]  = 'DISABLE_AT_STOCK';
        }
        if ($this->isColumnModified(ProductTableMap::COL_AVAILABILITY_ID)) {
            $modifiedColumns[':p' . $index++]  = 'AVAILABILITY_ID';
        }
        if ($this->isColumnModified(ProductTableMap::COL_HIERARCHY)) {
            $modifiedColumns[':p' . $index++]  = 'HIERARCHY';
        }
        if ($this->isColumnModified(ProductTableMap::COL_PACKAGE_SIZE)) {
            $modifiedColumns[':p' . $index++]  = 'PACKAGE_SIZE';
        }
        if ($this->isColumnModified(ProductTableMap::COL_DISABLE_AT_STOCK_ENABLED)) {
            $modifiedColumns[':p' . $index++]  = 'DISABLE_AT_STOCK_ENABLED';
        }

        $sql = sprintf(
            'INSERT INTO product (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'ID':                        
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'DELIVELER_CODE':                        
                        $stmt->bindValue($identifier, $this->deliveler_code, PDO::PARAM_STR);
                        break;
                    case 'EAN':                        
                        $stmt->bindValue($identifier, $this->ean, PDO::PARAM_STR);
                        break;
                    case 'BARCODE':                        
                        $stmt->bindValue($identifier, $this->barcode, PDO::PARAM_STR);
                        break;
                    case 'BUY_PRICE':                        
                        $stmt->bindValue($identifier, $this->buy_price, PDO::PARAM_STR);
                        break;
                    case 'SELL_PRICE':                        
                        $stmt->bindValue($identifier, $this->sell_price, PDO::PARAM_STR);
                        break;
                    case 'PRODUCER_ID':                        
                        $stmt->bindValue($identifier, $this->producer_id, PDO::PARAM_INT);
                        break;
                    case 'VAT_ID':                        
                        $stmt->bindValue($identifier, $this->vat_id, PDO::PARAM_INT);
                        break;
                    case 'STOCK':                        
                        $stmt->bindValue($identifier, $this->stock, PDO::PARAM_INT);
                        break;
                    case 'ADD_DATE':                        
                        $stmt->bindValue($identifier, $this->add_date ? $this->add_date->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'WEIGHT':                        
                        $stmt->bindValue($identifier, $this->weight, PDO::PARAM_STR);
                        break;
                    case 'BUY_CURRENCY_ID':                        
                        $stmt->bindValue($identifier, $this->buy_currency_id, PDO::PARAM_INT);
                        break;
                    case 'SELL_CURRENCY_ID':                        
                        $stmt->bindValue($identifier, $this->sell_currency_id, PDO::PARAM_INT);
                        break;
                    case 'TECHNICAL_DATA_SET_ID':                        
                        $stmt->bindValue($identifier, $this->technical_data_set_id, PDO::PARAM_INT);
                        break;
                    case 'TRACK_STOCK':                        
                        $stmt->bindValue($identifier, $this->track_stock, PDO::PARAM_INT);
                        break;
                    case 'ENABLE':                        
                        $stmt->bindValue($identifier, $this->enable, PDO::PARAM_INT);
                        break;
                    case 'PROMOTION':                        
                        $stmt->bindValue($identifier, $this->promotion, PDO::PARAM_INT);
                        break;
                    case 'DISCOUNT_PRICE':                        
                        $stmt->bindValue($identifier, $this->discount_price, PDO::PARAM_STR);
                        break;
                    case 'PROMOTION_START':                        
                        $stmt->bindValue($identifier, $this->promotion_start ? $this->promotion_start->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'PROMOTION_END':                        
                        $stmt->bindValue($identifier, $this->promotion_end ? $this->promotion_end->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'SHOPED':                        
                        $stmt->bindValue($identifier, $this->shoped, PDO::PARAM_INT);
                        break;
                    case 'WIDTH':                        
                        $stmt->bindValue($identifier, $this->width, PDO::PARAM_STR);
                        break;
                    case 'HEIGHT':                        
                        $stmt->bindValue($identifier, $this->height, PDO::PARAM_STR);
                        break;
                    case 'DEEPTH':                        
                        $stmt->bindValue($identifier, $this->deepth, PDO::PARAM_STR);
                        break;
                    case 'UNIT_MEASURE_ID':                        
                        $stmt->bindValue($identifier, $this->unit_measure_id, PDO::PARAM_INT);
                        break;
                    case 'DISABLE_AT_STOCK':                        
                        $stmt->bindValue($identifier, $this->disable_at_stock, PDO::PARAM_INT);
                        break;
                    case 'AVAILABILITY_ID':                        
                        $stmt->bindValue($identifier, $this->availability_id, PDO::PARAM_INT);
                        break;
                    case 'HIERARCHY':                        
                        $stmt->bindValue($identifier, $this->hierarchy, PDO::PARAM_INT);
                        break;
                    case 'PACKAGE_SIZE':                        
                        $stmt->bindValue($identifier, $this->package_size, PDO::PARAM_STR);
                        break;
                    case 'DISABLE_AT_STOCK_ENABLED':                        
                        $stmt->bindValue($identifier, $this->disable_at_stock_enabled, PDO::PARAM_INT);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = ProductTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getDelivelerCode();
                break;
            case 2:
                return $this->getEan();
                break;
            case 3:
                return $this->getBarcode();
                break;
            case 4:
                return $this->getBuyPrice();
                break;
            case 5:
                return $this->getSellPrice();
                break;
            case 6:
                return $this->getProducerId();
                break;
            case 7:
                return $this->getVatId();
                break;
            case 8:
                return $this->getStock();
                break;
            case 9:
                return $this->getAddDate();
                break;
            case 10:
                return $this->getWeight();
                break;
            case 11:
                return $this->getBuyCurrencyId();
                break;
            case 12:
                return $this->getSellCurrencyId();
                break;
            case 13:
                return $this->getTechnicalDataSetId();
                break;
            case 14:
                return $this->getTrackStock();
                break;
            case 15:
                return $this->getEnable();
                break;
            case 16:
                return $this->getPromotion();
                break;
            case 17:
                return $this->getDiscountPrice();
                break;
            case 18:
                return $this->getPromotionStart();
                break;
            case 19:
                return $this->getPromotionEnd();
                break;
            case 20:
                return $this->getShoped();
                break;
            case 21:
                return $this->getWidth();
                break;
            case 22:
                return $this->getHeight();
                break;
            case 23:
                return $this->getDeepth();
                break;
            case 24:
                return $this->getUnitMeasureId();
                break;
            case 25:
                return $this->getDisableAtStock();
                break;
            case 26:
                return $this->getAvailabilityId();
                break;
            case 27:
                return $this->getHierarchy();
                break;
            case 28:
                return $this->getPackageSize();
                break;
            case 29:
                return $this->getDisableAtStockEnabled();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['Product'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Product'][$this->getPrimaryKey()] = true;
        $keys = ProductTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getDelivelerCode(),
            $keys[2] => $this->getEan(),
            $keys[3] => $this->getBarcode(),
            $keys[4] => $this->getBuyPrice(),
            $keys[5] => $this->getSellPrice(),
            $keys[6] => $this->getProducerId(),
            $keys[7] => $this->getVatId(),
            $keys[8] => $this->getStock(),
            $keys[9] => $this->getAddDate(),
            $keys[10] => $this->getWeight(),
            $keys[11] => $this->getBuyCurrencyId(),
            $keys[12] => $this->getSellCurrencyId(),
            $keys[13] => $this->getTechnicalDataSetId(),
            $keys[14] => $this->getTrackStock(),
            $keys[15] => $this->getEnable(),
            $keys[16] => $this->getPromotion(),
            $keys[17] => $this->getDiscountPrice(),
            $keys[18] => $this->getPromotionStart(),
            $keys[19] => $this->getPromotionEnd(),
            $keys[20] => $this->getShoped(),
            $keys[21] => $this->getWidth(),
            $keys[22] => $this->getHeight(),
            $keys[23] => $this->getDeepth(),
            $keys[24] => $this->getUnitMeasureId(),
            $keys[25] => $this->getDisableAtStock(),
            $keys[26] => $this->getAvailabilityId(),
            $keys[27] => $this->getHierarchy(),
            $keys[28] => $this->getPackageSize(),
            $keys[29] => $this->getDisableAtStockEnabled(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }
        
        if ($includeForeignObjects) {
            if (null !== $this->aAvailability) {
                $result['Availability'] = $this->aAvailability->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCurrencyRelatedByBuyCurrencyId) {
                $result['CurrencyRelatedByBuyCurrencyId'] = $this->aCurrencyRelatedByBuyCurrencyId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aProducer) {
                $result['Producer'] = $this->aProducer->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCurrencyRelatedBySellCurrencyId) {
                $result['CurrencyRelatedBySellCurrencyId'] = $this->aCurrencyRelatedBySellCurrencyId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aUnitMeasure) {
                $result['UnitMeasure'] = $this->aUnitMeasure->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aVat) {
                $result['Vat'] = $this->aVat->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aTechnicalDataSet) {
                $result['TechnicalDataSet'] = $this->aTechnicalDataSet->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collMissingCartProducts) {
                $result['MissingCartProducts'] = $this->collMissingCartProducts->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collOrderProducts) {
                $result['OrderProducts'] = $this->collOrderProducts->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductAttributes) {
                $result['ProductAttributes'] = $this->collProductAttributes->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductCategories) {
                $result['ProductCategories'] = $this->collProductCategories->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collDelivererProducts) {
                $result['DelivererProducts'] = $this->collDelivererProducts->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductFiles) {
                $result['ProductFiles'] = $this->collProductFiles->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductGroupPrices) {
                $result['ProductGroupPrices'] = $this->collProductGroupPrices->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductNews) {
                $result['ProductNews'] = $this->collProductNews->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductPhotos) {
                $result['ProductPhotos'] = $this->collProductPhotos->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCrosssellsRelatedByProductId) {
                $result['CrosssellsRelatedByProductId'] = $this->collCrosssellsRelatedByProductId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCrosssellsRelatedByRelatedProductId) {
                $result['CrosssellsRelatedByRelatedProductId'] = $this->collCrosssellsRelatedByRelatedProductId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collSimilarsRelatedByProductId) {
                $result['SimilarsRelatedByProductId'] = $this->collSimilarsRelatedByProductId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collSimilarsRelatedByRelatedProductId) {
                $result['SimilarsRelatedByRelatedProductId'] = $this->collSimilarsRelatedByRelatedProductId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collUpsellsRelatedByProductId) {
                $result['UpsellsRelatedByProductId'] = $this->collUpsellsRelatedByProductId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collUpsellsRelatedByRelatedProductId) {
                $result['UpsellsRelatedByRelatedProductId'] = $this->collUpsellsRelatedByRelatedProductId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductTechnicalDataGroups) {
                $result['ProductTechnicalDataGroups'] = $this->collProductTechnicalDataGroups->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collWishlists) {
                $result['Wishlists'] = $this->collWishlists->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param      string $name
     * @param      mixed  $value field value
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return void
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = ProductTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @param      mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setDelivelerCode($value);
                break;
            case 2:
                $this->setEan($value);
                break;
            case 3:
                $this->setBarcode($value);
                break;
            case 4:
                $this->setBuyPrice($value);
                break;
            case 5:
                $this->setSellPrice($value);
                break;
            case 6:
                $this->setProducerId($value);
                break;
            case 7:
                $this->setVatId($value);
                break;
            case 8:
                $this->setStock($value);
                break;
            case 9:
                $this->setAddDate($value);
                break;
            case 10:
                $this->setWeight($value);
                break;
            case 11:
                $this->setBuyCurrencyId($value);
                break;
            case 12:
                $this->setSellCurrencyId($value);
                break;
            case 13:
                $this->setTechnicalDataSetId($value);
                break;
            case 14:
                $this->setTrackStock($value);
                break;
            case 15:
                $this->setEnable($value);
                break;
            case 16:
                $this->setPromotion($value);
                break;
            case 17:
                $this->setDiscountPrice($value);
                break;
            case 18:
                $this->setPromotionStart($value);
                break;
            case 19:
                $this->setPromotionEnd($value);
                break;
            case 20:
                $this->setShoped($value);
                break;
            case 21:
                $this->setWidth($value);
                break;
            case 22:
                $this->setHeight($value);
                break;
            case 23:
                $this->setDeepth($value);
                break;
            case 24:
                $this->setUnitMeasureId($value);
                break;
            case 25:
                $this->setDisableAtStock($value);
                break;
            case 26:
                $this->setAvailabilityId($value);
                break;
            case 27:
                $this->setHierarchy($value);
                break;
            case 28:
                $this->setPackageSize($value);
                break;
            case 29:
                $this->setDisableAtStockEnabled($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = ProductTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setDelivelerCode($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setEan($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setBarcode($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setBuyPrice($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setSellPrice($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setProducerId($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setVatId($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setStock($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setAddDate($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setWeight($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setBuyCurrencyId($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setSellCurrencyId($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setTechnicalDataSetId($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setTrackStock($arr[$keys[14]]);
        if (array_key_exists($keys[15], $arr)) $this->setEnable($arr[$keys[15]]);
        if (array_key_exists($keys[16], $arr)) $this->setPromotion($arr[$keys[16]]);
        if (array_key_exists($keys[17], $arr)) $this->setDiscountPrice($arr[$keys[17]]);
        if (array_key_exists($keys[18], $arr)) $this->setPromotionStart($arr[$keys[18]]);
        if (array_key_exists($keys[19], $arr)) $this->setPromotionEnd($arr[$keys[19]]);
        if (array_key_exists($keys[20], $arr)) $this->setShoped($arr[$keys[20]]);
        if (array_key_exists($keys[21], $arr)) $this->setWidth($arr[$keys[21]]);
        if (array_key_exists($keys[22], $arr)) $this->setHeight($arr[$keys[22]]);
        if (array_key_exists($keys[23], $arr)) $this->setDeepth($arr[$keys[23]]);
        if (array_key_exists($keys[24], $arr)) $this->setUnitMeasureId($arr[$keys[24]]);
        if (array_key_exists($keys[25], $arr)) $this->setDisableAtStock($arr[$keys[25]]);
        if (array_key_exists($keys[26], $arr)) $this->setAvailabilityId($arr[$keys[26]]);
        if (array_key_exists($keys[27], $arr)) $this->setHierarchy($arr[$keys[27]]);
        if (array_key_exists($keys[28], $arr)) $this->setPackageSize($arr[$keys[28]]);
        if (array_key_exists($keys[29], $arr)) $this->setDisableAtStockEnabled($arr[$keys[29]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ProductTableMap::DATABASE_NAME);

        if ($this->isColumnModified(ProductTableMap::COL_ID)) $criteria->add(ProductTableMap::COL_ID, $this->id);
        if ($this->isColumnModified(ProductTableMap::COL_DELIVELER_CODE)) $criteria->add(ProductTableMap::COL_DELIVELER_CODE, $this->deliveler_code);
        if ($this->isColumnModified(ProductTableMap::COL_EAN)) $criteria->add(ProductTableMap::COL_EAN, $this->ean);
        if ($this->isColumnModified(ProductTableMap::COL_BARCODE)) $criteria->add(ProductTableMap::COL_BARCODE, $this->barcode);
        if ($this->isColumnModified(ProductTableMap::COL_BUY_PRICE)) $criteria->add(ProductTableMap::COL_BUY_PRICE, $this->buy_price);
        if ($this->isColumnModified(ProductTableMap::COL_SELL_PRICE)) $criteria->add(ProductTableMap::COL_SELL_PRICE, $this->sell_price);
        if ($this->isColumnModified(ProductTableMap::COL_PRODUCER_ID)) $criteria->add(ProductTableMap::COL_PRODUCER_ID, $this->producer_id);
        if ($this->isColumnModified(ProductTableMap::COL_VAT_ID)) $criteria->add(ProductTableMap::COL_VAT_ID, $this->vat_id);
        if ($this->isColumnModified(ProductTableMap::COL_STOCK)) $criteria->add(ProductTableMap::COL_STOCK, $this->stock);
        if ($this->isColumnModified(ProductTableMap::COL_ADD_DATE)) $criteria->add(ProductTableMap::COL_ADD_DATE, $this->add_date);
        if ($this->isColumnModified(ProductTableMap::COL_WEIGHT)) $criteria->add(ProductTableMap::COL_WEIGHT, $this->weight);
        if ($this->isColumnModified(ProductTableMap::COL_BUY_CURRENCY_ID)) $criteria->add(ProductTableMap::COL_BUY_CURRENCY_ID, $this->buy_currency_id);
        if ($this->isColumnModified(ProductTableMap::COL_SELL_CURRENCY_ID)) $criteria->add(ProductTableMap::COL_SELL_CURRENCY_ID, $this->sell_currency_id);
        if ($this->isColumnModified(ProductTableMap::COL_TECHNICAL_DATA_SET_ID)) $criteria->add(ProductTableMap::COL_TECHNICAL_DATA_SET_ID, $this->technical_data_set_id);
        if ($this->isColumnModified(ProductTableMap::COL_TRACK_STOCK)) $criteria->add(ProductTableMap::COL_TRACK_STOCK, $this->track_stock);
        if ($this->isColumnModified(ProductTableMap::COL_ENABLE)) $criteria->add(ProductTableMap::COL_ENABLE, $this->enable);
        if ($this->isColumnModified(ProductTableMap::COL_PROMOTION)) $criteria->add(ProductTableMap::COL_PROMOTION, $this->promotion);
        if ($this->isColumnModified(ProductTableMap::COL_DISCOUNT_PRICE)) $criteria->add(ProductTableMap::COL_DISCOUNT_PRICE, $this->discount_price);
        if ($this->isColumnModified(ProductTableMap::COL_PROMOTION_START)) $criteria->add(ProductTableMap::COL_PROMOTION_START, $this->promotion_start);
        if ($this->isColumnModified(ProductTableMap::COL_PROMOTION_END)) $criteria->add(ProductTableMap::COL_PROMOTION_END, $this->promotion_end);
        if ($this->isColumnModified(ProductTableMap::COL_SHOPED)) $criteria->add(ProductTableMap::COL_SHOPED, $this->shoped);
        if ($this->isColumnModified(ProductTableMap::COL_WIDTH)) $criteria->add(ProductTableMap::COL_WIDTH, $this->width);
        if ($this->isColumnModified(ProductTableMap::COL_HEIGHT)) $criteria->add(ProductTableMap::COL_HEIGHT, $this->height);
        if ($this->isColumnModified(ProductTableMap::COL_DEEPTH)) $criteria->add(ProductTableMap::COL_DEEPTH, $this->deepth);
        if ($this->isColumnModified(ProductTableMap::COL_UNIT_MEASURE_ID)) $criteria->add(ProductTableMap::COL_UNIT_MEASURE_ID, $this->unit_measure_id);
        if ($this->isColumnModified(ProductTableMap::COL_DISABLE_AT_STOCK)) $criteria->add(ProductTableMap::COL_DISABLE_AT_STOCK, $this->disable_at_stock);
        if ($this->isColumnModified(ProductTableMap::COL_AVAILABILITY_ID)) $criteria->add(ProductTableMap::COL_AVAILABILITY_ID, $this->availability_id);
        if ($this->isColumnModified(ProductTableMap::COL_HIERARCHY)) $criteria->add(ProductTableMap::COL_HIERARCHY, $this->hierarchy);
        if ($this->isColumnModified(ProductTableMap::COL_PACKAGE_SIZE)) $criteria->add(ProductTableMap::COL_PACKAGE_SIZE, $this->package_size);
        if ($this->isColumnModified(ProductTableMap::COL_DISABLE_AT_STOCK_ENABLED)) $criteria->add(ProductTableMap::COL_DISABLE_AT_STOCK_ENABLED, $this->disable_at_stock_enabled);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @throws LogicException if no primary key is defined
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(ProductTableMap::DATABASE_NAME);
        $criteria->add(ProductTableMap::COL_ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return   int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \Gekosale\Plugin\Product\Model\ORM\Product (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDelivelerCode($this->getDelivelerCode());
        $copyObj->setEan($this->getEan());
        $copyObj->setBarcode($this->getBarcode());
        $copyObj->setBuyPrice($this->getBuyPrice());
        $copyObj->setSellPrice($this->getSellPrice());
        $copyObj->setProducerId($this->getProducerId());
        $copyObj->setVatId($this->getVatId());
        $copyObj->setStock($this->getStock());
        $copyObj->setAddDate($this->getAddDate());
        $copyObj->setWeight($this->getWeight());
        $copyObj->setBuyCurrencyId($this->getBuyCurrencyId());
        $copyObj->setSellCurrencyId($this->getSellCurrencyId());
        $copyObj->setTechnicalDataSetId($this->getTechnicalDataSetId());
        $copyObj->setTrackStock($this->getTrackStock());
        $copyObj->setEnable($this->getEnable());
        $copyObj->setPromotion($this->getPromotion());
        $copyObj->setDiscountPrice($this->getDiscountPrice());
        $copyObj->setPromotionStart($this->getPromotionStart());
        $copyObj->setPromotionEnd($this->getPromotionEnd());
        $copyObj->setShoped($this->getShoped());
        $copyObj->setWidth($this->getWidth());
        $copyObj->setHeight($this->getHeight());
        $copyObj->setDeepth($this->getDeepth());
        $copyObj->setUnitMeasureId($this->getUnitMeasureId());
        $copyObj->setDisableAtStock($this->getDisableAtStock());
        $copyObj->setAvailabilityId($this->getAvailabilityId());
        $copyObj->setHierarchy($this->getHierarchy());
        $copyObj->setPackageSize($this->getPackageSize());
        $copyObj->setDisableAtStockEnabled($this->getDisableAtStockEnabled());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getMissingCartProducts() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addMissingCartProduct($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getOrderProducts() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addOrderProduct($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductAttributes() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductAttribute($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductCategories() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductCategory($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getDelivererProducts() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDelivererProduct($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductFiles() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductFile($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductGroupPrices() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductGroupPrice($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductNews() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductNew($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductPhotos() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductPhoto($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCrosssellsRelatedByProductId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCrosssellRelatedByProductId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCrosssellsRelatedByRelatedProductId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCrosssellRelatedByRelatedProductId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getSimilarsRelatedByProductId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addSimilarRelatedByProductId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getSimilarsRelatedByRelatedProductId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addSimilarRelatedByRelatedProductId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getUpsellsRelatedByProductId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addUpsellRelatedByProductId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getUpsellsRelatedByRelatedProductId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addUpsellRelatedByRelatedProductId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductTechnicalDataGroups() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductTechnicalDataGroup($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getWishlists() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addWishlist($relObj->copy($deepCopy));
                }
            }

        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return                 \Gekosale\Plugin\Product\Model\ORM\Product Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Declares an association between this object and a ChildAvailability object.
     *
     * @param                  ChildAvailability $v
     * @return                 \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     * @throws PropelException
     */
    public function setAvailability(ChildAvailability $v = null)
    {
        if ($v === null) {
            $this->setAvailabilityId(NULL);
        } else {
            $this->setAvailabilityId($v->getId());
        }

        $this->aAvailability = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildAvailability object, it will not be re-added.
        if ($v !== null) {
            $v->addProduct($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildAvailability object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildAvailability The associated ChildAvailability object.
     * @throws PropelException
     */
    public function getAvailability(ConnectionInterface $con = null)
    {
        if ($this->aAvailability === null && ($this->availability_id !== null)) {
            $this->aAvailability = AvailabilityQuery::create()->findPk($this->availability_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aAvailability->addProducts($this);
             */
        }

        return $this->aAvailability;
    }

    /**
     * Declares an association between this object and a ChildCurrency object.
     *
     * @param                  ChildCurrency $v
     * @return                 \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCurrencyRelatedByBuyCurrencyId(ChildCurrency $v = null)
    {
        if ($v === null) {
            $this->setBuyCurrencyId(NULL);
        } else {
            $this->setBuyCurrencyId($v->getId());
        }

        $this->aCurrencyRelatedByBuyCurrencyId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildCurrency object, it will not be re-added.
        if ($v !== null) {
            $v->addProductRelatedByBuyCurrencyId($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildCurrency object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildCurrency The associated ChildCurrency object.
     * @throws PropelException
     */
    public function getCurrencyRelatedByBuyCurrencyId(ConnectionInterface $con = null)
    {
        if ($this->aCurrencyRelatedByBuyCurrencyId === null && ($this->buy_currency_id !== null)) {
            $this->aCurrencyRelatedByBuyCurrencyId = CurrencyQuery::create()->findPk($this->buy_currency_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCurrencyRelatedByBuyCurrencyId->addProductsRelatedByBuyCurrencyId($this);
             */
        }

        return $this->aCurrencyRelatedByBuyCurrencyId;
    }

    /**
     * Declares an association between this object and a ChildProducer object.
     *
     * @param                  ChildProducer $v
     * @return                 \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     * @throws PropelException
     */
    public function setProducer(ChildProducer $v = null)
    {
        if ($v === null) {
            $this->setProducerId(NULL);
        } else {
            $this->setProducerId($v->getId());
        }

        $this->aProducer = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildProducer object, it will not be re-added.
        if ($v !== null) {
            $v->addProduct($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildProducer object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildProducer The associated ChildProducer object.
     * @throws PropelException
     */
    public function getProducer(ConnectionInterface $con = null)
    {
        if ($this->aProducer === null && ($this->producer_id !== null)) {
            $this->aProducer = ProducerQuery::create()->findPk($this->producer_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aProducer->addProducts($this);
             */
        }

        return $this->aProducer;
    }

    /**
     * Declares an association between this object and a ChildCurrency object.
     *
     * @param                  ChildCurrency $v
     * @return                 \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCurrencyRelatedBySellCurrencyId(ChildCurrency $v = null)
    {
        if ($v === null) {
            $this->setSellCurrencyId(NULL);
        } else {
            $this->setSellCurrencyId($v->getId());
        }

        $this->aCurrencyRelatedBySellCurrencyId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildCurrency object, it will not be re-added.
        if ($v !== null) {
            $v->addProductRelatedBySellCurrencyId($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildCurrency object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildCurrency The associated ChildCurrency object.
     * @throws PropelException
     */
    public function getCurrencyRelatedBySellCurrencyId(ConnectionInterface $con = null)
    {
        if ($this->aCurrencyRelatedBySellCurrencyId === null && ($this->sell_currency_id !== null)) {
            $this->aCurrencyRelatedBySellCurrencyId = CurrencyQuery::create()->findPk($this->sell_currency_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCurrencyRelatedBySellCurrencyId->addProductsRelatedBySellCurrencyId($this);
             */
        }

        return $this->aCurrencyRelatedBySellCurrencyId;
    }

    /**
     * Declares an association between this object and a ChildUnitMeasure object.
     *
     * @param                  ChildUnitMeasure $v
     * @return                 \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     * @throws PropelException
     */
    public function setUnitMeasure(ChildUnitMeasure $v = null)
    {
        if ($v === null) {
            $this->setUnitMeasureId(NULL);
        } else {
            $this->setUnitMeasureId($v->getId());
        }

        $this->aUnitMeasure = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildUnitMeasure object, it will not be re-added.
        if ($v !== null) {
            $v->addProduct($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildUnitMeasure object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildUnitMeasure The associated ChildUnitMeasure object.
     * @throws PropelException
     */
    public function getUnitMeasure(ConnectionInterface $con = null)
    {
        if ($this->aUnitMeasure === null && ($this->unit_measure_id !== null)) {
            $this->aUnitMeasure = UnitMeasureQuery::create()->findPk($this->unit_measure_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aUnitMeasure->addProducts($this);
             */
        }

        return $this->aUnitMeasure;
    }

    /**
     * Declares an association between this object and a ChildVat object.
     *
     * @param                  ChildVat $v
     * @return                 \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     * @throws PropelException
     */
    public function setVat(ChildVat $v = null)
    {
        if ($v === null) {
            $this->setVatId(NULL);
        } else {
            $this->setVatId($v->getId());
        }

        $this->aVat = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildVat object, it will not be re-added.
        if ($v !== null) {
            $v->addProduct($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildVat object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildVat The associated ChildVat object.
     * @throws PropelException
     */
    public function getVat(ConnectionInterface $con = null)
    {
        if ($this->aVat === null && ($this->vat_id !== null)) {
            $this->aVat = VatQuery::create()->findPk($this->vat_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aVat->addProducts($this);
             */
        }

        return $this->aVat;
    }

    /**
     * Declares an association between this object and a ChildTechnicalDataSet object.
     *
     * @param                  ChildTechnicalDataSet $v
     * @return                 \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     * @throws PropelException
     */
    public function setTechnicalDataSet(ChildTechnicalDataSet $v = null)
    {
        if ($v === null) {
            $this->setTechnicalDataSetId(NULL);
        } else {
            $this->setTechnicalDataSetId($v->getId());
        }

        $this->aTechnicalDataSet = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildTechnicalDataSet object, it will not be re-added.
        if ($v !== null) {
            $v->addProduct($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildTechnicalDataSet object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildTechnicalDataSet The associated ChildTechnicalDataSet object.
     * @throws PropelException
     */
    public function getTechnicalDataSet(ConnectionInterface $con = null)
    {
        if ($this->aTechnicalDataSet === null && ($this->technical_data_set_id !== null)) {
            $this->aTechnicalDataSet = TechnicalDataSetQuery::create()->findPk($this->technical_data_set_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aTechnicalDataSet->addProducts($this);
             */
        }

        return $this->aTechnicalDataSet;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('MissingCartProduct' == $relationName) {
            return $this->initMissingCartProducts();
        }
        if ('OrderProduct' == $relationName) {
            return $this->initOrderProducts();
        }
        if ('ProductAttribute' == $relationName) {
            return $this->initProductAttributes();
        }
        if ('ProductCategory' == $relationName) {
            return $this->initProductCategories();
        }
        if ('DelivererProduct' == $relationName) {
            return $this->initDelivererProducts();
        }
        if ('ProductFile' == $relationName) {
            return $this->initProductFiles();
        }
        if ('ProductGroupPrice' == $relationName) {
            return $this->initProductGroupPrices();
        }
        if ('ProductNew' == $relationName) {
            return $this->initProductNews();
        }
        if ('ProductPhoto' == $relationName) {
            return $this->initProductPhotos();
        }
        if ('CrosssellRelatedByProductId' == $relationName) {
            return $this->initCrosssellsRelatedByProductId();
        }
        if ('CrosssellRelatedByRelatedProductId' == $relationName) {
            return $this->initCrosssellsRelatedByRelatedProductId();
        }
        if ('SimilarRelatedByProductId' == $relationName) {
            return $this->initSimilarsRelatedByProductId();
        }
        if ('SimilarRelatedByRelatedProductId' == $relationName) {
            return $this->initSimilarsRelatedByRelatedProductId();
        }
        if ('UpsellRelatedByProductId' == $relationName) {
            return $this->initUpsellsRelatedByProductId();
        }
        if ('UpsellRelatedByRelatedProductId' == $relationName) {
            return $this->initUpsellsRelatedByRelatedProductId();
        }
        if ('ProductTechnicalDataGroup' == $relationName) {
            return $this->initProductTechnicalDataGroups();
        }
        if ('Wishlist' == $relationName) {
            return $this->initWishlists();
        }
    }

    /**
     * Clears out the collMissingCartProducts collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addMissingCartProducts()
     */
    public function clearMissingCartProducts()
    {
        $this->collMissingCartProducts = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collMissingCartProducts collection loaded partially.
     */
    public function resetPartialMissingCartProducts($v = true)
    {
        $this->collMissingCartProductsPartial = $v;
    }

    /**
     * Initializes the collMissingCartProducts collection.
     *
     * By default this just sets the collMissingCartProducts collection to an empty array (like clearcollMissingCartProducts());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initMissingCartProducts($overrideExisting = true)
    {
        if (null !== $this->collMissingCartProducts && !$overrideExisting) {
            return;
        }
        $this->collMissingCartProducts = new ObjectCollection();
        $this->collMissingCartProducts->setModel('\Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProduct');
    }

    /**
     * Gets an array of ChildMissingCartProduct objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProduct is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildMissingCartProduct[] List of ChildMissingCartProduct objects
     * @throws PropelException
     */
    public function getMissingCartProducts($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collMissingCartProductsPartial && !$this->isNew();
        if (null === $this->collMissingCartProducts || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collMissingCartProducts) {
                // return empty collection
                $this->initMissingCartProducts();
            } else {
                $collMissingCartProducts = MissingCartProductQuery::create(null, $criteria)
                    ->filterByProduct($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collMissingCartProductsPartial && count($collMissingCartProducts)) {
                        $this->initMissingCartProducts(false);

                        foreach ($collMissingCartProducts as $obj) {
                            if (false == $this->collMissingCartProducts->contains($obj)) {
                                $this->collMissingCartProducts->append($obj);
                            }
                        }

                        $this->collMissingCartProductsPartial = true;
                    }

                    reset($collMissingCartProducts);

                    return $collMissingCartProducts;
                }

                if ($partial && $this->collMissingCartProducts) {
                    foreach ($this->collMissingCartProducts as $obj) {
                        if ($obj->isNew()) {
                            $collMissingCartProducts[] = $obj;
                        }
                    }
                }

                $this->collMissingCartProducts = $collMissingCartProducts;
                $this->collMissingCartProductsPartial = false;
            }
        }

        return $this->collMissingCartProducts;
    }

    /**
     * Sets a collection of MissingCartProduct objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $missingCartProducts A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildProduct The current object (for fluent API support)
     */
    public function setMissingCartProducts(Collection $missingCartProducts, ConnectionInterface $con = null)
    {
        $missingCartProductsToDelete = $this->getMissingCartProducts(new Criteria(), $con)->diff($missingCartProducts);

        
        $this->missingCartProductsScheduledForDeletion = $missingCartProductsToDelete;

        foreach ($missingCartProductsToDelete as $missingCartProductRemoved) {
            $missingCartProductRemoved->setProduct(null);
        }

        $this->collMissingCartProducts = null;
        foreach ($missingCartProducts as $missingCartProduct) {
            $this->addMissingCartProduct($missingCartProduct);
        }

        $this->collMissingCartProducts = $missingCartProducts;
        $this->collMissingCartProductsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related MissingCartProduct objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related MissingCartProduct objects.
     * @throws PropelException
     */
    public function countMissingCartProducts(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collMissingCartProductsPartial && !$this->isNew();
        if (null === $this->collMissingCartProducts || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collMissingCartProducts) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getMissingCartProducts());
            }

            $query = MissingCartProductQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProduct($this)
                ->count($con);
        }

        return count($this->collMissingCartProducts);
    }

    /**
     * Method called to associate a ChildMissingCartProduct object to this object
     * through the ChildMissingCartProduct foreign key attribute.
     *
     * @param    ChildMissingCartProduct $l ChildMissingCartProduct
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function addMissingCartProduct(ChildMissingCartProduct $l)
    {
        if ($this->collMissingCartProducts === null) {
            $this->initMissingCartProducts();
            $this->collMissingCartProductsPartial = true;
        }

        if (!in_array($l, $this->collMissingCartProducts->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddMissingCartProduct($l);
        }

        return $this;
    }

    /**
     * @param MissingCartProduct $missingCartProduct The missingCartProduct object to add.
     */
    protected function doAddMissingCartProduct($missingCartProduct)
    {
        $this->collMissingCartProducts[]= $missingCartProduct;
        $missingCartProduct->setProduct($this);
    }

    /**
     * @param  MissingCartProduct $missingCartProduct The missingCartProduct object to remove.
     * @return ChildProduct The current object (for fluent API support)
     */
    public function removeMissingCartProduct($missingCartProduct)
    {
        if ($this->getMissingCartProducts()->contains($missingCartProduct)) {
            $this->collMissingCartProducts->remove($this->collMissingCartProducts->search($missingCartProduct));
            if (null === $this->missingCartProductsScheduledForDeletion) {
                $this->missingCartProductsScheduledForDeletion = clone $this->collMissingCartProducts;
                $this->missingCartProductsScheduledForDeletion->clear();
            }
            $this->missingCartProductsScheduledForDeletion[]= clone $missingCartProduct;
            $missingCartProduct->setProduct(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Product is new, it will return
     * an empty collection; or if this Product has previously
     * been saved, it will retrieve related MissingCartProducts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Product.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildMissingCartProduct[] List of ChildMissingCartProduct objects
     */
    public function getMissingCartProductsJoinMissingCart($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = MissingCartProductQuery::create(null, $criteria);
        $query->joinWith('MissingCart', $joinBehavior);

        return $this->getMissingCartProducts($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Product is new, it will return
     * an empty collection; or if this Product has previously
     * been saved, it will retrieve related MissingCartProducts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Product.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildMissingCartProduct[] List of ChildMissingCartProduct objects
     */
    public function getMissingCartProductsJoinShop($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = MissingCartProductQuery::create(null, $criteria);
        $query->joinWith('Shop', $joinBehavior);

        return $this->getMissingCartProducts($query, $con);
    }

    /**
     * Clears out the collOrderProducts collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addOrderProducts()
     */
    public function clearOrderProducts()
    {
        $this->collOrderProducts = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collOrderProducts collection loaded partially.
     */
    public function resetPartialOrderProducts($v = true)
    {
        $this->collOrderProductsPartial = $v;
    }

    /**
     * Initializes the collOrderProducts collection.
     *
     * By default this just sets the collOrderProducts collection to an empty array (like clearcollOrderProducts());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initOrderProducts($overrideExisting = true)
    {
        if (null !== $this->collOrderProducts && !$overrideExisting) {
            return;
        }
        $this->collOrderProducts = new ObjectCollection();
        $this->collOrderProducts->setModel('\Gekosale\Plugin\Order\Model\ORM\OrderProduct');
    }

    /**
     * Gets an array of ChildOrderProduct objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProduct is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildOrderProduct[] List of ChildOrderProduct objects
     * @throws PropelException
     */
    public function getOrderProducts($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collOrderProductsPartial && !$this->isNew();
        if (null === $this->collOrderProducts || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collOrderProducts) {
                // return empty collection
                $this->initOrderProducts();
            } else {
                $collOrderProducts = OrderProductQuery::create(null, $criteria)
                    ->filterByProduct($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collOrderProductsPartial && count($collOrderProducts)) {
                        $this->initOrderProducts(false);

                        foreach ($collOrderProducts as $obj) {
                            if (false == $this->collOrderProducts->contains($obj)) {
                                $this->collOrderProducts->append($obj);
                            }
                        }

                        $this->collOrderProductsPartial = true;
                    }

                    reset($collOrderProducts);

                    return $collOrderProducts;
                }

                if ($partial && $this->collOrderProducts) {
                    foreach ($this->collOrderProducts as $obj) {
                        if ($obj->isNew()) {
                            $collOrderProducts[] = $obj;
                        }
                    }
                }

                $this->collOrderProducts = $collOrderProducts;
                $this->collOrderProductsPartial = false;
            }
        }

        return $this->collOrderProducts;
    }

    /**
     * Sets a collection of OrderProduct objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $orderProducts A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildProduct The current object (for fluent API support)
     */
    public function setOrderProducts(Collection $orderProducts, ConnectionInterface $con = null)
    {
        $orderProductsToDelete = $this->getOrderProducts(new Criteria(), $con)->diff($orderProducts);

        
        $this->orderProductsScheduledForDeletion = $orderProductsToDelete;

        foreach ($orderProductsToDelete as $orderProductRemoved) {
            $orderProductRemoved->setProduct(null);
        }

        $this->collOrderProducts = null;
        foreach ($orderProducts as $orderProduct) {
            $this->addOrderProduct($orderProduct);
        }

        $this->collOrderProducts = $orderProducts;
        $this->collOrderProductsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related OrderProduct objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related OrderProduct objects.
     * @throws PropelException
     */
    public function countOrderProducts(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collOrderProductsPartial && !$this->isNew();
        if (null === $this->collOrderProducts || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collOrderProducts) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getOrderProducts());
            }

            $query = OrderProductQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProduct($this)
                ->count($con);
        }

        return count($this->collOrderProducts);
    }

    /**
     * Method called to associate a ChildOrderProduct object to this object
     * through the ChildOrderProduct foreign key attribute.
     *
     * @param    ChildOrderProduct $l ChildOrderProduct
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function addOrderProduct(ChildOrderProduct $l)
    {
        if ($this->collOrderProducts === null) {
            $this->initOrderProducts();
            $this->collOrderProductsPartial = true;
        }

        if (!in_array($l, $this->collOrderProducts->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddOrderProduct($l);
        }

        return $this;
    }

    /**
     * @param OrderProduct $orderProduct The orderProduct object to add.
     */
    protected function doAddOrderProduct($orderProduct)
    {
        $this->collOrderProducts[]= $orderProduct;
        $orderProduct->setProduct($this);
    }

    /**
     * @param  OrderProduct $orderProduct The orderProduct object to remove.
     * @return ChildProduct The current object (for fluent API support)
     */
    public function removeOrderProduct($orderProduct)
    {
        if ($this->getOrderProducts()->contains($orderProduct)) {
            $this->collOrderProducts->remove($this->collOrderProducts->search($orderProduct));
            if (null === $this->orderProductsScheduledForDeletion) {
                $this->orderProductsScheduledForDeletion = clone $this->collOrderProducts;
                $this->orderProductsScheduledForDeletion->clear();
            }
            $this->orderProductsScheduledForDeletion[]= $orderProduct;
            $orderProduct->setProduct(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Product is new, it will return
     * an empty collection; or if this Product has previously
     * been saved, it will retrieve related OrderProducts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Product.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildOrderProduct[] List of ChildOrderProduct objects
     */
    public function getOrderProductsJoinOrder($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = OrderProductQuery::create(null, $criteria);
        $query->joinWith('Order', $joinBehavior);

        return $this->getOrderProducts($query, $con);
    }

    /**
     * Clears out the collProductAttributes collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addProductAttributes()
     */
    public function clearProductAttributes()
    {
        $this->collProductAttributes = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collProductAttributes collection loaded partially.
     */
    public function resetPartialProductAttributes($v = true)
    {
        $this->collProductAttributesPartial = $v;
    }

    /**
     * Initializes the collProductAttributes collection.
     *
     * By default this just sets the collProductAttributes collection to an empty array (like clearcollProductAttributes());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductAttributes($overrideExisting = true)
    {
        if (null !== $this->collProductAttributes && !$overrideExisting) {
            return;
        }
        $this->collProductAttributes = new ObjectCollection();
        $this->collProductAttributes->setModel('\Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute');
    }

    /**
     * Gets an array of ChildProductAttribute objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProduct is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildProductAttribute[] List of ChildProductAttribute objects
     * @throws PropelException
     */
    public function getProductAttributes($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collProductAttributesPartial && !$this->isNew();
        if (null === $this->collProductAttributes || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductAttributes) {
                // return empty collection
                $this->initProductAttributes();
            } else {
                $collProductAttributes = ProductAttributeQuery::create(null, $criteria)
                    ->filterByProduct($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collProductAttributesPartial && count($collProductAttributes)) {
                        $this->initProductAttributes(false);

                        foreach ($collProductAttributes as $obj) {
                            if (false == $this->collProductAttributes->contains($obj)) {
                                $this->collProductAttributes->append($obj);
                            }
                        }

                        $this->collProductAttributesPartial = true;
                    }

                    reset($collProductAttributes);

                    return $collProductAttributes;
                }

                if ($partial && $this->collProductAttributes) {
                    foreach ($this->collProductAttributes as $obj) {
                        if ($obj->isNew()) {
                            $collProductAttributes[] = $obj;
                        }
                    }
                }

                $this->collProductAttributes = $collProductAttributes;
                $this->collProductAttributesPartial = false;
            }
        }

        return $this->collProductAttributes;
    }

    /**
     * Sets a collection of ProductAttribute objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $productAttributes A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildProduct The current object (for fluent API support)
     */
    public function setProductAttributes(Collection $productAttributes, ConnectionInterface $con = null)
    {
        $productAttributesToDelete = $this->getProductAttributes(new Criteria(), $con)->diff($productAttributes);

        
        $this->productAttributesScheduledForDeletion = $productAttributesToDelete;

        foreach ($productAttributesToDelete as $productAttributeRemoved) {
            $productAttributeRemoved->setProduct(null);
        }

        $this->collProductAttributes = null;
        foreach ($productAttributes as $productAttribute) {
            $this->addProductAttribute($productAttribute);
        }

        $this->collProductAttributes = $productAttributes;
        $this->collProductAttributesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProductAttribute objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ProductAttribute objects.
     * @throws PropelException
     */
    public function countProductAttributes(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collProductAttributesPartial && !$this->isNew();
        if (null === $this->collProductAttributes || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductAttributes) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProductAttributes());
            }

            $query = ProductAttributeQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProduct($this)
                ->count($con);
        }

        return count($this->collProductAttributes);
    }

    /**
     * Method called to associate a ChildProductAttribute object to this object
     * through the ChildProductAttribute foreign key attribute.
     *
     * @param    ChildProductAttribute $l ChildProductAttribute
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function addProductAttribute(ChildProductAttribute $l)
    {
        if ($this->collProductAttributes === null) {
            $this->initProductAttributes();
            $this->collProductAttributesPartial = true;
        }

        if (!in_array($l, $this->collProductAttributes->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductAttribute($l);
        }

        return $this;
    }

    /**
     * @param ProductAttribute $productAttribute The productAttribute object to add.
     */
    protected function doAddProductAttribute($productAttribute)
    {
        $this->collProductAttributes[]= $productAttribute;
        $productAttribute->setProduct($this);
    }

    /**
     * @param  ProductAttribute $productAttribute The productAttribute object to remove.
     * @return ChildProduct The current object (for fluent API support)
     */
    public function removeProductAttribute($productAttribute)
    {
        if ($this->getProductAttributes()->contains($productAttribute)) {
            $this->collProductAttributes->remove($this->collProductAttributes->search($productAttribute));
            if (null === $this->productAttributesScheduledForDeletion) {
                $this->productAttributesScheduledForDeletion = clone $this->collProductAttributes;
                $this->productAttributesScheduledForDeletion->clear();
            }
            $this->productAttributesScheduledForDeletion[]= clone $productAttribute;
            $productAttribute->setProduct(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Product is new, it will return
     * an empty collection; or if this Product has previously
     * been saved, it will retrieve related ProductAttributes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Product.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProductAttribute[] List of ChildProductAttribute objects
     */
    public function getProductAttributesJoinAttributeGroupName($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ProductAttributeQuery::create(null, $criteria);
        $query->joinWith('AttributeGroupName', $joinBehavior);

        return $this->getProductAttributes($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Product is new, it will return
     * an empty collection; or if this Product has previously
     * been saved, it will retrieve related ProductAttributes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Product.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProductAttribute[] List of ChildProductAttribute objects
     */
    public function getProductAttributesJoinAvailability($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ProductAttributeQuery::create(null, $criteria);
        $query->joinWith('Availability', $joinBehavior);

        return $this->getProductAttributes($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Product is new, it will return
     * an empty collection; or if this Product has previously
     * been saved, it will retrieve related ProductAttributes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Product.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProductAttribute[] List of ChildProductAttribute objects
     */
    public function getProductAttributesJoinFile($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ProductAttributeQuery::create(null, $criteria);
        $query->joinWith('File', $joinBehavior);

        return $this->getProductAttributes($query, $con);
    }

    /**
     * Clears out the collProductCategories collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addProductCategories()
     */
    public function clearProductCategories()
    {
        $this->collProductCategories = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collProductCategories collection loaded partially.
     */
    public function resetPartialProductCategories($v = true)
    {
        $this->collProductCategoriesPartial = $v;
    }

    /**
     * Initializes the collProductCategories collection.
     *
     * By default this just sets the collProductCategories collection to an empty array (like clearcollProductCategories());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductCategories($overrideExisting = true)
    {
        if (null !== $this->collProductCategories && !$overrideExisting) {
            return;
        }
        $this->collProductCategories = new ObjectCollection();
        $this->collProductCategories->setModel('\Gekosale\Plugin\Product\Model\ORM\ProductCategory');
    }

    /**
     * Gets an array of ChildProductCategory objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProduct is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildProductCategory[] List of ChildProductCategory objects
     * @throws PropelException
     */
    public function getProductCategories($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collProductCategoriesPartial && !$this->isNew();
        if (null === $this->collProductCategories || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductCategories) {
                // return empty collection
                $this->initProductCategories();
            } else {
                $collProductCategories = ChildProductCategoryQuery::create(null, $criteria)
                    ->filterByProduct($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collProductCategoriesPartial && count($collProductCategories)) {
                        $this->initProductCategories(false);

                        foreach ($collProductCategories as $obj) {
                            if (false == $this->collProductCategories->contains($obj)) {
                                $this->collProductCategories->append($obj);
                            }
                        }

                        $this->collProductCategoriesPartial = true;
                    }

                    reset($collProductCategories);

                    return $collProductCategories;
                }

                if ($partial && $this->collProductCategories) {
                    foreach ($this->collProductCategories as $obj) {
                        if ($obj->isNew()) {
                            $collProductCategories[] = $obj;
                        }
                    }
                }

                $this->collProductCategories = $collProductCategories;
                $this->collProductCategoriesPartial = false;
            }
        }

        return $this->collProductCategories;
    }

    /**
     * Sets a collection of ProductCategory objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $productCategories A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildProduct The current object (for fluent API support)
     */
    public function setProductCategories(Collection $productCategories, ConnectionInterface $con = null)
    {
        $productCategoriesToDelete = $this->getProductCategories(new Criteria(), $con)->diff($productCategories);

        
        $this->productCategoriesScheduledForDeletion = $productCategoriesToDelete;

        foreach ($productCategoriesToDelete as $productCategoryRemoved) {
            $productCategoryRemoved->setProduct(null);
        }

        $this->collProductCategories = null;
        foreach ($productCategories as $productCategory) {
            $this->addProductCategory($productCategory);
        }

        $this->collProductCategories = $productCategories;
        $this->collProductCategoriesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProductCategory objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ProductCategory objects.
     * @throws PropelException
     */
    public function countProductCategories(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collProductCategoriesPartial && !$this->isNew();
        if (null === $this->collProductCategories || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductCategories) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProductCategories());
            }

            $query = ChildProductCategoryQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProduct($this)
                ->count($con);
        }

        return count($this->collProductCategories);
    }

    /**
     * Method called to associate a ChildProductCategory object to this object
     * through the ChildProductCategory foreign key attribute.
     *
     * @param    ChildProductCategory $l ChildProductCategory
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function addProductCategory(ChildProductCategory $l)
    {
        if ($this->collProductCategories === null) {
            $this->initProductCategories();
            $this->collProductCategoriesPartial = true;
        }

        if (!in_array($l, $this->collProductCategories->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductCategory($l);
        }

        return $this;
    }

    /**
     * @param ProductCategory $productCategory The productCategory object to add.
     */
    protected function doAddProductCategory($productCategory)
    {
        $this->collProductCategories[]= $productCategory;
        $productCategory->setProduct($this);
    }

    /**
     * @param  ProductCategory $productCategory The productCategory object to remove.
     * @return ChildProduct The current object (for fluent API support)
     */
    public function removeProductCategory($productCategory)
    {
        if ($this->getProductCategories()->contains($productCategory)) {
            $this->collProductCategories->remove($this->collProductCategories->search($productCategory));
            if (null === $this->productCategoriesScheduledForDeletion) {
                $this->productCategoriesScheduledForDeletion = clone $this->collProductCategories;
                $this->productCategoriesScheduledForDeletion->clear();
            }
            $this->productCategoriesScheduledForDeletion[]= clone $productCategory;
            $productCategory->setProduct(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Product is new, it will return
     * an empty collection; or if this Product has previously
     * been saved, it will retrieve related ProductCategories from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Product.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProductCategory[] List of ChildProductCategory objects
     */
    public function getProductCategoriesJoinCategory($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildProductCategoryQuery::create(null, $criteria);
        $query->joinWith('Category', $joinBehavior);

        return $this->getProductCategories($query, $con);
    }

    /**
     * Clears out the collDelivererProducts collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDelivererProducts()
     */
    public function clearDelivererProducts()
    {
        $this->collDelivererProducts = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDelivererProducts collection loaded partially.
     */
    public function resetPartialDelivererProducts($v = true)
    {
        $this->collDelivererProductsPartial = $v;
    }

    /**
     * Initializes the collDelivererProducts collection.
     *
     * By default this just sets the collDelivererProducts collection to an empty array (like clearcollDelivererProducts());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDelivererProducts($overrideExisting = true)
    {
        if (null !== $this->collDelivererProducts && !$overrideExisting) {
            return;
        }
        $this->collDelivererProducts = new ObjectCollection();
        $this->collDelivererProducts->setModel('\Gekosale\Plugin\Deliverer\Model\ORM\DelivererProduct');
    }

    /**
     * Gets an array of ChildDelivererProduct objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProduct is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildDelivererProduct[] List of ChildDelivererProduct objects
     * @throws PropelException
     */
    public function getDelivererProducts($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDelivererProductsPartial && !$this->isNew();
        if (null === $this->collDelivererProducts || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDelivererProducts) {
                // return empty collection
                $this->initDelivererProducts();
            } else {
                $collDelivererProducts = DelivererProductQuery::create(null, $criteria)
                    ->filterByProduct($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDelivererProductsPartial && count($collDelivererProducts)) {
                        $this->initDelivererProducts(false);

                        foreach ($collDelivererProducts as $obj) {
                            if (false == $this->collDelivererProducts->contains($obj)) {
                                $this->collDelivererProducts->append($obj);
                            }
                        }

                        $this->collDelivererProductsPartial = true;
                    }

                    reset($collDelivererProducts);

                    return $collDelivererProducts;
                }

                if ($partial && $this->collDelivererProducts) {
                    foreach ($this->collDelivererProducts as $obj) {
                        if ($obj->isNew()) {
                            $collDelivererProducts[] = $obj;
                        }
                    }
                }

                $this->collDelivererProducts = $collDelivererProducts;
                $this->collDelivererProductsPartial = false;
            }
        }

        return $this->collDelivererProducts;
    }

    /**
     * Sets a collection of DelivererProduct objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $delivererProducts A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildProduct The current object (for fluent API support)
     */
    public function setDelivererProducts(Collection $delivererProducts, ConnectionInterface $con = null)
    {
        $delivererProductsToDelete = $this->getDelivererProducts(new Criteria(), $con)->diff($delivererProducts);

        
        $this->delivererProductsScheduledForDeletion = $delivererProductsToDelete;

        foreach ($delivererProductsToDelete as $delivererProductRemoved) {
            $delivererProductRemoved->setProduct(null);
        }

        $this->collDelivererProducts = null;
        foreach ($delivererProducts as $delivererProduct) {
            $this->addDelivererProduct($delivererProduct);
        }

        $this->collDelivererProducts = $delivererProducts;
        $this->collDelivererProductsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DelivererProduct objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DelivererProduct objects.
     * @throws PropelException
     */
    public function countDelivererProducts(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDelivererProductsPartial && !$this->isNew();
        if (null === $this->collDelivererProducts || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDelivererProducts) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDelivererProducts());
            }

            $query = DelivererProductQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProduct($this)
                ->count($con);
        }

        return count($this->collDelivererProducts);
    }

    /**
     * Method called to associate a ChildDelivererProduct object to this object
     * through the ChildDelivererProduct foreign key attribute.
     *
     * @param    ChildDelivererProduct $l ChildDelivererProduct
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function addDelivererProduct(ChildDelivererProduct $l)
    {
        if ($this->collDelivererProducts === null) {
            $this->initDelivererProducts();
            $this->collDelivererProductsPartial = true;
        }

        if (!in_array($l, $this->collDelivererProducts->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddDelivererProduct($l);
        }

        return $this;
    }

    /**
     * @param DelivererProduct $delivererProduct The delivererProduct object to add.
     */
    protected function doAddDelivererProduct($delivererProduct)
    {
        $this->collDelivererProducts[]= $delivererProduct;
        $delivererProduct->setProduct($this);
    }

    /**
     * @param  DelivererProduct $delivererProduct The delivererProduct object to remove.
     * @return ChildProduct The current object (for fluent API support)
     */
    public function removeDelivererProduct($delivererProduct)
    {
        if ($this->getDelivererProducts()->contains($delivererProduct)) {
            $this->collDelivererProducts->remove($this->collDelivererProducts->search($delivererProduct));
            if (null === $this->delivererProductsScheduledForDeletion) {
                $this->delivererProductsScheduledForDeletion = clone $this->collDelivererProducts;
                $this->delivererProductsScheduledForDeletion->clear();
            }
            $this->delivererProductsScheduledForDeletion[]= clone $delivererProduct;
            $delivererProduct->setProduct(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Product is new, it will return
     * an empty collection; or if this Product has previously
     * been saved, it will retrieve related DelivererProducts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Product.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildDelivererProduct[] List of ChildDelivererProduct objects
     */
    public function getDelivererProductsJoinDeliverer($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = DelivererProductQuery::create(null, $criteria);
        $query->joinWith('Deliverer', $joinBehavior);

        return $this->getDelivererProducts($query, $con);
    }

    /**
     * Clears out the collProductFiles collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addProductFiles()
     */
    public function clearProductFiles()
    {
        $this->collProductFiles = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collProductFiles collection loaded partially.
     */
    public function resetPartialProductFiles($v = true)
    {
        $this->collProductFilesPartial = $v;
    }

    /**
     * Initializes the collProductFiles collection.
     *
     * By default this just sets the collProductFiles collection to an empty array (like clearcollProductFiles());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductFiles($overrideExisting = true)
    {
        if (null !== $this->collProductFiles && !$overrideExisting) {
            return;
        }
        $this->collProductFiles = new ObjectCollection();
        $this->collProductFiles->setModel('\Gekosale\Plugin\Product\Model\ORM\ProductFile');
    }

    /**
     * Gets an array of ChildProductFile objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProduct is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildProductFile[] List of ChildProductFile objects
     * @throws PropelException
     */
    public function getProductFiles($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collProductFilesPartial && !$this->isNew();
        if (null === $this->collProductFiles || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductFiles) {
                // return empty collection
                $this->initProductFiles();
            } else {
                $collProductFiles = ChildProductFileQuery::create(null, $criteria)
                    ->filterByProduct($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collProductFilesPartial && count($collProductFiles)) {
                        $this->initProductFiles(false);

                        foreach ($collProductFiles as $obj) {
                            if (false == $this->collProductFiles->contains($obj)) {
                                $this->collProductFiles->append($obj);
                            }
                        }

                        $this->collProductFilesPartial = true;
                    }

                    reset($collProductFiles);

                    return $collProductFiles;
                }

                if ($partial && $this->collProductFiles) {
                    foreach ($this->collProductFiles as $obj) {
                        if ($obj->isNew()) {
                            $collProductFiles[] = $obj;
                        }
                    }
                }

                $this->collProductFiles = $collProductFiles;
                $this->collProductFilesPartial = false;
            }
        }

        return $this->collProductFiles;
    }

    /**
     * Sets a collection of ProductFile objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $productFiles A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildProduct The current object (for fluent API support)
     */
    public function setProductFiles(Collection $productFiles, ConnectionInterface $con = null)
    {
        $productFilesToDelete = $this->getProductFiles(new Criteria(), $con)->diff($productFiles);

        
        $this->productFilesScheduledForDeletion = $productFilesToDelete;

        foreach ($productFilesToDelete as $productFileRemoved) {
            $productFileRemoved->setProduct(null);
        }

        $this->collProductFiles = null;
        foreach ($productFiles as $productFile) {
            $this->addProductFile($productFile);
        }

        $this->collProductFiles = $productFiles;
        $this->collProductFilesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProductFile objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ProductFile objects.
     * @throws PropelException
     */
    public function countProductFiles(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collProductFilesPartial && !$this->isNew();
        if (null === $this->collProductFiles || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductFiles) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProductFiles());
            }

            $query = ChildProductFileQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProduct($this)
                ->count($con);
        }

        return count($this->collProductFiles);
    }

    /**
     * Method called to associate a ChildProductFile object to this object
     * through the ChildProductFile foreign key attribute.
     *
     * @param    ChildProductFile $l ChildProductFile
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function addProductFile(ChildProductFile $l)
    {
        if ($this->collProductFiles === null) {
            $this->initProductFiles();
            $this->collProductFilesPartial = true;
        }

        if (!in_array($l, $this->collProductFiles->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductFile($l);
        }

        return $this;
    }

    /**
     * @param ProductFile $productFile The productFile object to add.
     */
    protected function doAddProductFile($productFile)
    {
        $this->collProductFiles[]= $productFile;
        $productFile->setProduct($this);
    }

    /**
     * @param  ProductFile $productFile The productFile object to remove.
     * @return ChildProduct The current object (for fluent API support)
     */
    public function removeProductFile($productFile)
    {
        if ($this->getProductFiles()->contains($productFile)) {
            $this->collProductFiles->remove($this->collProductFiles->search($productFile));
            if (null === $this->productFilesScheduledForDeletion) {
                $this->productFilesScheduledForDeletion = clone $this->collProductFiles;
                $this->productFilesScheduledForDeletion->clear();
            }
            $this->productFilesScheduledForDeletion[]= clone $productFile;
            $productFile->setProduct(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Product is new, it will return
     * an empty collection; or if this Product has previously
     * been saved, it will retrieve related ProductFiles from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Product.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProductFile[] List of ChildProductFile objects
     */
    public function getProductFilesJoinFile($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildProductFileQuery::create(null, $criteria);
        $query->joinWith('File', $joinBehavior);

        return $this->getProductFiles($query, $con);
    }

    /**
     * Clears out the collProductGroupPrices collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addProductGroupPrices()
     */
    public function clearProductGroupPrices()
    {
        $this->collProductGroupPrices = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collProductGroupPrices collection loaded partially.
     */
    public function resetPartialProductGroupPrices($v = true)
    {
        $this->collProductGroupPricesPartial = $v;
    }

    /**
     * Initializes the collProductGroupPrices collection.
     *
     * By default this just sets the collProductGroupPrices collection to an empty array (like clearcollProductGroupPrices());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductGroupPrices($overrideExisting = true)
    {
        if (null !== $this->collProductGroupPrices && !$overrideExisting) {
            return;
        }
        $this->collProductGroupPrices = new ObjectCollection();
        $this->collProductGroupPrices->setModel('\Gekosale\Plugin\ProductGroupPrice\Model\ORM\ProductGroupPrice');
    }

    /**
     * Gets an array of ChildProductGroupPrice objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProduct is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildProductGroupPrice[] List of ChildProductGroupPrice objects
     * @throws PropelException
     */
    public function getProductGroupPrices($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collProductGroupPricesPartial && !$this->isNew();
        if (null === $this->collProductGroupPrices || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductGroupPrices) {
                // return empty collection
                $this->initProductGroupPrices();
            } else {
                $collProductGroupPrices = ProductGroupPriceQuery::create(null, $criteria)
                    ->filterByProduct($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collProductGroupPricesPartial && count($collProductGroupPrices)) {
                        $this->initProductGroupPrices(false);

                        foreach ($collProductGroupPrices as $obj) {
                            if (false == $this->collProductGroupPrices->contains($obj)) {
                                $this->collProductGroupPrices->append($obj);
                            }
                        }

                        $this->collProductGroupPricesPartial = true;
                    }

                    reset($collProductGroupPrices);

                    return $collProductGroupPrices;
                }

                if ($partial && $this->collProductGroupPrices) {
                    foreach ($this->collProductGroupPrices as $obj) {
                        if ($obj->isNew()) {
                            $collProductGroupPrices[] = $obj;
                        }
                    }
                }

                $this->collProductGroupPrices = $collProductGroupPrices;
                $this->collProductGroupPricesPartial = false;
            }
        }

        return $this->collProductGroupPrices;
    }

    /**
     * Sets a collection of ProductGroupPrice objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $productGroupPrices A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildProduct The current object (for fluent API support)
     */
    public function setProductGroupPrices(Collection $productGroupPrices, ConnectionInterface $con = null)
    {
        $productGroupPricesToDelete = $this->getProductGroupPrices(new Criteria(), $con)->diff($productGroupPrices);

        
        $this->productGroupPricesScheduledForDeletion = $productGroupPricesToDelete;

        foreach ($productGroupPricesToDelete as $productGroupPriceRemoved) {
            $productGroupPriceRemoved->setProduct(null);
        }

        $this->collProductGroupPrices = null;
        foreach ($productGroupPrices as $productGroupPrice) {
            $this->addProductGroupPrice($productGroupPrice);
        }

        $this->collProductGroupPrices = $productGroupPrices;
        $this->collProductGroupPricesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProductGroupPrice objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ProductGroupPrice objects.
     * @throws PropelException
     */
    public function countProductGroupPrices(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collProductGroupPricesPartial && !$this->isNew();
        if (null === $this->collProductGroupPrices || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductGroupPrices) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProductGroupPrices());
            }

            $query = ProductGroupPriceQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProduct($this)
                ->count($con);
        }

        return count($this->collProductGroupPrices);
    }

    /**
     * Method called to associate a ChildProductGroupPrice object to this object
     * through the ChildProductGroupPrice foreign key attribute.
     *
     * @param    ChildProductGroupPrice $l ChildProductGroupPrice
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function addProductGroupPrice(ChildProductGroupPrice $l)
    {
        if ($this->collProductGroupPrices === null) {
            $this->initProductGroupPrices();
            $this->collProductGroupPricesPartial = true;
        }

        if (!in_array($l, $this->collProductGroupPrices->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductGroupPrice($l);
        }

        return $this;
    }

    /**
     * @param ProductGroupPrice $productGroupPrice The productGroupPrice object to add.
     */
    protected function doAddProductGroupPrice($productGroupPrice)
    {
        $this->collProductGroupPrices[]= $productGroupPrice;
        $productGroupPrice->setProduct($this);
    }

    /**
     * @param  ProductGroupPrice $productGroupPrice The productGroupPrice object to remove.
     * @return ChildProduct The current object (for fluent API support)
     */
    public function removeProductGroupPrice($productGroupPrice)
    {
        if ($this->getProductGroupPrices()->contains($productGroupPrice)) {
            $this->collProductGroupPrices->remove($this->collProductGroupPrices->search($productGroupPrice));
            if (null === $this->productGroupPricesScheduledForDeletion) {
                $this->productGroupPricesScheduledForDeletion = clone $this->collProductGroupPrices;
                $this->productGroupPricesScheduledForDeletion->clear();
            }
            $this->productGroupPricesScheduledForDeletion[]= clone $productGroupPrice;
            $productGroupPrice->setProduct(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Product is new, it will return
     * an empty collection; or if this Product has previously
     * been saved, it will retrieve related ProductGroupPrices from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Product.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProductGroupPrice[] List of ChildProductGroupPrice objects
     */
    public function getProductGroupPricesJoinClientGroup($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ProductGroupPriceQuery::create(null, $criteria);
        $query->joinWith('ClientGroup', $joinBehavior);

        return $this->getProductGroupPrices($query, $con);
    }

    /**
     * Clears out the collProductNews collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addProductNews()
     */
    public function clearProductNews()
    {
        $this->collProductNews = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collProductNews collection loaded partially.
     */
    public function resetPartialProductNews($v = true)
    {
        $this->collProductNewsPartial = $v;
    }

    /**
     * Initializes the collProductNews collection.
     *
     * By default this just sets the collProductNews collection to an empty array (like clearcollProductNews());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductNews($overrideExisting = true)
    {
        if (null !== $this->collProductNews && !$overrideExisting) {
            return;
        }
        $this->collProductNews = new ObjectCollection();
        $this->collProductNews->setModel('\Gekosale\Plugin\ProductNew\Model\ORM\ProductNew');
    }

    /**
     * Gets an array of ChildProductNew objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProduct is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildProductNew[] List of ChildProductNew objects
     * @throws PropelException
     */
    public function getProductNews($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collProductNewsPartial && !$this->isNew();
        if (null === $this->collProductNews || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductNews) {
                // return empty collection
                $this->initProductNews();
            } else {
                $collProductNews = ProductNewQuery::create(null, $criteria)
                    ->filterByProduct($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collProductNewsPartial && count($collProductNews)) {
                        $this->initProductNews(false);

                        foreach ($collProductNews as $obj) {
                            if (false == $this->collProductNews->contains($obj)) {
                                $this->collProductNews->append($obj);
                            }
                        }

                        $this->collProductNewsPartial = true;
                    }

                    reset($collProductNews);

                    return $collProductNews;
                }

                if ($partial && $this->collProductNews) {
                    foreach ($this->collProductNews as $obj) {
                        if ($obj->isNew()) {
                            $collProductNews[] = $obj;
                        }
                    }
                }

                $this->collProductNews = $collProductNews;
                $this->collProductNewsPartial = false;
            }
        }

        return $this->collProductNews;
    }

    /**
     * Sets a collection of ProductNew objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $productNews A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildProduct The current object (for fluent API support)
     */
    public function setProductNews(Collection $productNews, ConnectionInterface $con = null)
    {
        $productNewsToDelete = $this->getProductNews(new Criteria(), $con)->diff($productNews);

        
        $this->productNewsScheduledForDeletion = $productNewsToDelete;

        foreach ($productNewsToDelete as $productNewRemoved) {
            $productNewRemoved->setProduct(null);
        }

        $this->collProductNews = null;
        foreach ($productNews as $productNew) {
            $this->addProductNew($productNew);
        }

        $this->collProductNews = $productNews;
        $this->collProductNewsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProductNew objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ProductNew objects.
     * @throws PropelException
     */
    public function countProductNews(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collProductNewsPartial && !$this->isNew();
        if (null === $this->collProductNews || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductNews) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProductNews());
            }

            $query = ProductNewQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProduct($this)
                ->count($con);
        }

        return count($this->collProductNews);
    }

    /**
     * Method called to associate a ChildProductNew object to this object
     * through the ChildProductNew foreign key attribute.
     *
     * @param    ChildProductNew $l ChildProductNew
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function addProductNew(ChildProductNew $l)
    {
        if ($this->collProductNews === null) {
            $this->initProductNews();
            $this->collProductNewsPartial = true;
        }

        if (!in_array($l, $this->collProductNews->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductNew($l);
        }

        return $this;
    }

    /**
     * @param ProductNew $productNew The productNew object to add.
     */
    protected function doAddProductNew($productNew)
    {
        $this->collProductNews[]= $productNew;
        $productNew->setProduct($this);
    }

    /**
     * @param  ProductNew $productNew The productNew object to remove.
     * @return ChildProduct The current object (for fluent API support)
     */
    public function removeProductNew($productNew)
    {
        if ($this->getProductNews()->contains($productNew)) {
            $this->collProductNews->remove($this->collProductNews->search($productNew));
            if (null === $this->productNewsScheduledForDeletion) {
                $this->productNewsScheduledForDeletion = clone $this->collProductNews;
                $this->productNewsScheduledForDeletion->clear();
            }
            $this->productNewsScheduledForDeletion[]= clone $productNew;
            $productNew->setProduct(null);
        }

        return $this;
    }

    /**
     * Clears out the collProductPhotos collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addProductPhotos()
     */
    public function clearProductPhotos()
    {
        $this->collProductPhotos = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collProductPhotos collection loaded partially.
     */
    public function resetPartialProductPhotos($v = true)
    {
        $this->collProductPhotosPartial = $v;
    }

    /**
     * Initializes the collProductPhotos collection.
     *
     * By default this just sets the collProductPhotos collection to an empty array (like clearcollProductPhotos());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductPhotos($overrideExisting = true)
    {
        if (null !== $this->collProductPhotos && !$overrideExisting) {
            return;
        }
        $this->collProductPhotos = new ObjectCollection();
        $this->collProductPhotos->setModel('\Gekosale\Plugin\Product\Model\ORM\ProductPhoto');
    }

    /**
     * Gets an array of ChildProductPhoto objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProduct is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildProductPhoto[] List of ChildProductPhoto objects
     * @throws PropelException
     */
    public function getProductPhotos($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collProductPhotosPartial && !$this->isNew();
        if (null === $this->collProductPhotos || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductPhotos) {
                // return empty collection
                $this->initProductPhotos();
            } else {
                $collProductPhotos = ChildProductPhotoQuery::create(null, $criteria)
                    ->filterByProduct($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collProductPhotosPartial && count($collProductPhotos)) {
                        $this->initProductPhotos(false);

                        foreach ($collProductPhotos as $obj) {
                            if (false == $this->collProductPhotos->contains($obj)) {
                                $this->collProductPhotos->append($obj);
                            }
                        }

                        $this->collProductPhotosPartial = true;
                    }

                    reset($collProductPhotos);

                    return $collProductPhotos;
                }

                if ($partial && $this->collProductPhotos) {
                    foreach ($this->collProductPhotos as $obj) {
                        if ($obj->isNew()) {
                            $collProductPhotos[] = $obj;
                        }
                    }
                }

                $this->collProductPhotos = $collProductPhotos;
                $this->collProductPhotosPartial = false;
            }
        }

        return $this->collProductPhotos;
    }

    /**
     * Sets a collection of ProductPhoto objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $productPhotos A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildProduct The current object (for fluent API support)
     */
    public function setProductPhotos(Collection $productPhotos, ConnectionInterface $con = null)
    {
        $productPhotosToDelete = $this->getProductPhotos(new Criteria(), $con)->diff($productPhotos);

        
        $this->productPhotosScheduledForDeletion = $productPhotosToDelete;

        foreach ($productPhotosToDelete as $productPhotoRemoved) {
            $productPhotoRemoved->setProduct(null);
        }

        $this->collProductPhotos = null;
        foreach ($productPhotos as $productPhoto) {
            $this->addProductPhoto($productPhoto);
        }

        $this->collProductPhotos = $productPhotos;
        $this->collProductPhotosPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProductPhoto objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ProductPhoto objects.
     * @throws PropelException
     */
    public function countProductPhotos(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collProductPhotosPartial && !$this->isNew();
        if (null === $this->collProductPhotos || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductPhotos) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProductPhotos());
            }

            $query = ChildProductPhotoQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProduct($this)
                ->count($con);
        }

        return count($this->collProductPhotos);
    }

    /**
     * Method called to associate a ChildProductPhoto object to this object
     * through the ChildProductPhoto foreign key attribute.
     *
     * @param    ChildProductPhoto $l ChildProductPhoto
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function addProductPhoto(ChildProductPhoto $l)
    {
        if ($this->collProductPhotos === null) {
            $this->initProductPhotos();
            $this->collProductPhotosPartial = true;
        }

        if (!in_array($l, $this->collProductPhotos->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductPhoto($l);
        }

        return $this;
    }

    /**
     * @param ProductPhoto $productPhoto The productPhoto object to add.
     */
    protected function doAddProductPhoto($productPhoto)
    {
        $this->collProductPhotos[]= $productPhoto;
        $productPhoto->setProduct($this);
    }

    /**
     * @param  ProductPhoto $productPhoto The productPhoto object to remove.
     * @return ChildProduct The current object (for fluent API support)
     */
    public function removeProductPhoto($productPhoto)
    {
        if ($this->getProductPhotos()->contains($productPhoto)) {
            $this->collProductPhotos->remove($this->collProductPhotos->search($productPhoto));
            if (null === $this->productPhotosScheduledForDeletion) {
                $this->productPhotosScheduledForDeletion = clone $this->collProductPhotos;
                $this->productPhotosScheduledForDeletion->clear();
            }
            $this->productPhotosScheduledForDeletion[]= clone $productPhoto;
            $productPhoto->setProduct(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Product is new, it will return
     * an empty collection; or if this Product has previously
     * been saved, it will retrieve related ProductPhotos from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Product.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProductPhoto[] List of ChildProductPhoto objects
     */
    public function getProductPhotosJoinFile($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildProductPhotoQuery::create(null, $criteria);
        $query->joinWith('File', $joinBehavior);

        return $this->getProductPhotos($query, $con);
    }

    /**
     * Clears out the collCrosssellsRelatedByProductId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCrosssellsRelatedByProductId()
     */
    public function clearCrosssellsRelatedByProductId()
    {
        $this->collCrosssellsRelatedByProductId = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCrosssellsRelatedByProductId collection loaded partially.
     */
    public function resetPartialCrosssellsRelatedByProductId($v = true)
    {
        $this->collCrosssellsRelatedByProductIdPartial = $v;
    }

    /**
     * Initializes the collCrosssellsRelatedByProductId collection.
     *
     * By default this just sets the collCrosssellsRelatedByProductId collection to an empty array (like clearcollCrosssellsRelatedByProductId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCrosssellsRelatedByProductId($overrideExisting = true)
    {
        if (null !== $this->collCrosssellsRelatedByProductId && !$overrideExisting) {
            return;
        }
        $this->collCrosssellsRelatedByProductId = new ObjectCollection();
        $this->collCrosssellsRelatedByProductId->setModel('\Gekosale\Plugin\Crosssell\Model\ORM\Crosssell');
    }

    /**
     * Gets an array of ChildCrosssell objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProduct is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildCrosssell[] List of ChildCrosssell objects
     * @throws PropelException
     */
    public function getCrosssellsRelatedByProductId($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCrosssellsRelatedByProductIdPartial && !$this->isNew();
        if (null === $this->collCrosssellsRelatedByProductId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCrosssellsRelatedByProductId) {
                // return empty collection
                $this->initCrosssellsRelatedByProductId();
            } else {
                $collCrosssellsRelatedByProductId = CrosssellQuery::create(null, $criteria)
                    ->filterByProductRelatedByProductId($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCrosssellsRelatedByProductIdPartial && count($collCrosssellsRelatedByProductId)) {
                        $this->initCrosssellsRelatedByProductId(false);

                        foreach ($collCrosssellsRelatedByProductId as $obj) {
                            if (false == $this->collCrosssellsRelatedByProductId->contains($obj)) {
                                $this->collCrosssellsRelatedByProductId->append($obj);
                            }
                        }

                        $this->collCrosssellsRelatedByProductIdPartial = true;
                    }

                    reset($collCrosssellsRelatedByProductId);

                    return $collCrosssellsRelatedByProductId;
                }

                if ($partial && $this->collCrosssellsRelatedByProductId) {
                    foreach ($this->collCrosssellsRelatedByProductId as $obj) {
                        if ($obj->isNew()) {
                            $collCrosssellsRelatedByProductId[] = $obj;
                        }
                    }
                }

                $this->collCrosssellsRelatedByProductId = $collCrosssellsRelatedByProductId;
                $this->collCrosssellsRelatedByProductIdPartial = false;
            }
        }

        return $this->collCrosssellsRelatedByProductId;
    }

    /**
     * Sets a collection of CrosssellRelatedByProductId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $crosssellsRelatedByProductId A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildProduct The current object (for fluent API support)
     */
    public function setCrosssellsRelatedByProductId(Collection $crosssellsRelatedByProductId, ConnectionInterface $con = null)
    {
        $crosssellsRelatedByProductIdToDelete = $this->getCrosssellsRelatedByProductId(new Criteria(), $con)->diff($crosssellsRelatedByProductId);

        
        $this->crosssellsRelatedByProductIdScheduledForDeletion = $crosssellsRelatedByProductIdToDelete;

        foreach ($crosssellsRelatedByProductIdToDelete as $crosssellRelatedByProductIdRemoved) {
            $crosssellRelatedByProductIdRemoved->setProductRelatedByProductId(null);
        }

        $this->collCrosssellsRelatedByProductId = null;
        foreach ($crosssellsRelatedByProductId as $crosssellRelatedByProductId) {
            $this->addCrosssellRelatedByProductId($crosssellRelatedByProductId);
        }

        $this->collCrosssellsRelatedByProductId = $crosssellsRelatedByProductId;
        $this->collCrosssellsRelatedByProductIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Crosssell objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Crosssell objects.
     * @throws PropelException
     */
    public function countCrosssellsRelatedByProductId(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCrosssellsRelatedByProductIdPartial && !$this->isNew();
        if (null === $this->collCrosssellsRelatedByProductId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCrosssellsRelatedByProductId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCrosssellsRelatedByProductId());
            }

            $query = CrosssellQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProductRelatedByProductId($this)
                ->count($con);
        }

        return count($this->collCrosssellsRelatedByProductId);
    }

    /**
     * Method called to associate a ChildCrosssell object to this object
     * through the ChildCrosssell foreign key attribute.
     *
     * @param    ChildCrosssell $l ChildCrosssell
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function addCrosssellRelatedByProductId(ChildCrosssell $l)
    {
        if ($this->collCrosssellsRelatedByProductId === null) {
            $this->initCrosssellsRelatedByProductId();
            $this->collCrosssellsRelatedByProductIdPartial = true;
        }

        if (!in_array($l, $this->collCrosssellsRelatedByProductId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCrosssellRelatedByProductId($l);
        }

        return $this;
    }

    /**
     * @param CrosssellRelatedByProductId $crosssellRelatedByProductId The crosssellRelatedByProductId object to add.
     */
    protected function doAddCrosssellRelatedByProductId($crosssellRelatedByProductId)
    {
        $this->collCrosssellsRelatedByProductId[]= $crosssellRelatedByProductId;
        $crosssellRelatedByProductId->setProductRelatedByProductId($this);
    }

    /**
     * @param  CrosssellRelatedByProductId $crosssellRelatedByProductId The crosssellRelatedByProductId object to remove.
     * @return ChildProduct The current object (for fluent API support)
     */
    public function removeCrosssellRelatedByProductId($crosssellRelatedByProductId)
    {
        if ($this->getCrosssellsRelatedByProductId()->contains($crosssellRelatedByProductId)) {
            $this->collCrosssellsRelatedByProductId->remove($this->collCrosssellsRelatedByProductId->search($crosssellRelatedByProductId));
            if (null === $this->crosssellsRelatedByProductIdScheduledForDeletion) {
                $this->crosssellsRelatedByProductIdScheduledForDeletion = clone $this->collCrosssellsRelatedByProductId;
                $this->crosssellsRelatedByProductIdScheduledForDeletion->clear();
            }
            $this->crosssellsRelatedByProductIdScheduledForDeletion[]= clone $crosssellRelatedByProductId;
            $crosssellRelatedByProductId->setProductRelatedByProductId(null);
        }

        return $this;
    }

    /**
     * Clears out the collCrosssellsRelatedByRelatedProductId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCrosssellsRelatedByRelatedProductId()
     */
    public function clearCrosssellsRelatedByRelatedProductId()
    {
        $this->collCrosssellsRelatedByRelatedProductId = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCrosssellsRelatedByRelatedProductId collection loaded partially.
     */
    public function resetPartialCrosssellsRelatedByRelatedProductId($v = true)
    {
        $this->collCrosssellsRelatedByRelatedProductIdPartial = $v;
    }

    /**
     * Initializes the collCrosssellsRelatedByRelatedProductId collection.
     *
     * By default this just sets the collCrosssellsRelatedByRelatedProductId collection to an empty array (like clearcollCrosssellsRelatedByRelatedProductId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCrosssellsRelatedByRelatedProductId($overrideExisting = true)
    {
        if (null !== $this->collCrosssellsRelatedByRelatedProductId && !$overrideExisting) {
            return;
        }
        $this->collCrosssellsRelatedByRelatedProductId = new ObjectCollection();
        $this->collCrosssellsRelatedByRelatedProductId->setModel('\Gekosale\Plugin\Crosssell\Model\ORM\Crosssell');
    }

    /**
     * Gets an array of ChildCrosssell objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProduct is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildCrosssell[] List of ChildCrosssell objects
     * @throws PropelException
     */
    public function getCrosssellsRelatedByRelatedProductId($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCrosssellsRelatedByRelatedProductIdPartial && !$this->isNew();
        if (null === $this->collCrosssellsRelatedByRelatedProductId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCrosssellsRelatedByRelatedProductId) {
                // return empty collection
                $this->initCrosssellsRelatedByRelatedProductId();
            } else {
                $collCrosssellsRelatedByRelatedProductId = CrosssellQuery::create(null, $criteria)
                    ->filterByProductRelatedByRelatedProductId($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCrosssellsRelatedByRelatedProductIdPartial && count($collCrosssellsRelatedByRelatedProductId)) {
                        $this->initCrosssellsRelatedByRelatedProductId(false);

                        foreach ($collCrosssellsRelatedByRelatedProductId as $obj) {
                            if (false == $this->collCrosssellsRelatedByRelatedProductId->contains($obj)) {
                                $this->collCrosssellsRelatedByRelatedProductId->append($obj);
                            }
                        }

                        $this->collCrosssellsRelatedByRelatedProductIdPartial = true;
                    }

                    reset($collCrosssellsRelatedByRelatedProductId);

                    return $collCrosssellsRelatedByRelatedProductId;
                }

                if ($partial && $this->collCrosssellsRelatedByRelatedProductId) {
                    foreach ($this->collCrosssellsRelatedByRelatedProductId as $obj) {
                        if ($obj->isNew()) {
                            $collCrosssellsRelatedByRelatedProductId[] = $obj;
                        }
                    }
                }

                $this->collCrosssellsRelatedByRelatedProductId = $collCrosssellsRelatedByRelatedProductId;
                $this->collCrosssellsRelatedByRelatedProductIdPartial = false;
            }
        }

        return $this->collCrosssellsRelatedByRelatedProductId;
    }

    /**
     * Sets a collection of CrosssellRelatedByRelatedProductId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $crosssellsRelatedByRelatedProductId A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildProduct The current object (for fluent API support)
     */
    public function setCrosssellsRelatedByRelatedProductId(Collection $crosssellsRelatedByRelatedProductId, ConnectionInterface $con = null)
    {
        $crosssellsRelatedByRelatedProductIdToDelete = $this->getCrosssellsRelatedByRelatedProductId(new Criteria(), $con)->diff($crosssellsRelatedByRelatedProductId);

        
        $this->crosssellsRelatedByRelatedProductIdScheduledForDeletion = $crosssellsRelatedByRelatedProductIdToDelete;

        foreach ($crosssellsRelatedByRelatedProductIdToDelete as $crosssellRelatedByRelatedProductIdRemoved) {
            $crosssellRelatedByRelatedProductIdRemoved->setProductRelatedByRelatedProductId(null);
        }

        $this->collCrosssellsRelatedByRelatedProductId = null;
        foreach ($crosssellsRelatedByRelatedProductId as $crosssellRelatedByRelatedProductId) {
            $this->addCrosssellRelatedByRelatedProductId($crosssellRelatedByRelatedProductId);
        }

        $this->collCrosssellsRelatedByRelatedProductId = $crosssellsRelatedByRelatedProductId;
        $this->collCrosssellsRelatedByRelatedProductIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Crosssell objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Crosssell objects.
     * @throws PropelException
     */
    public function countCrosssellsRelatedByRelatedProductId(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCrosssellsRelatedByRelatedProductIdPartial && !$this->isNew();
        if (null === $this->collCrosssellsRelatedByRelatedProductId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCrosssellsRelatedByRelatedProductId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCrosssellsRelatedByRelatedProductId());
            }

            $query = CrosssellQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProductRelatedByRelatedProductId($this)
                ->count($con);
        }

        return count($this->collCrosssellsRelatedByRelatedProductId);
    }

    /**
     * Method called to associate a ChildCrosssell object to this object
     * through the ChildCrosssell foreign key attribute.
     *
     * @param    ChildCrosssell $l ChildCrosssell
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function addCrosssellRelatedByRelatedProductId(ChildCrosssell $l)
    {
        if ($this->collCrosssellsRelatedByRelatedProductId === null) {
            $this->initCrosssellsRelatedByRelatedProductId();
            $this->collCrosssellsRelatedByRelatedProductIdPartial = true;
        }

        if (!in_array($l, $this->collCrosssellsRelatedByRelatedProductId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCrosssellRelatedByRelatedProductId($l);
        }

        return $this;
    }

    /**
     * @param CrosssellRelatedByRelatedProductId $crosssellRelatedByRelatedProductId The crosssellRelatedByRelatedProductId object to add.
     */
    protected function doAddCrosssellRelatedByRelatedProductId($crosssellRelatedByRelatedProductId)
    {
        $this->collCrosssellsRelatedByRelatedProductId[]= $crosssellRelatedByRelatedProductId;
        $crosssellRelatedByRelatedProductId->setProductRelatedByRelatedProductId($this);
    }

    /**
     * @param  CrosssellRelatedByRelatedProductId $crosssellRelatedByRelatedProductId The crosssellRelatedByRelatedProductId object to remove.
     * @return ChildProduct The current object (for fluent API support)
     */
    public function removeCrosssellRelatedByRelatedProductId($crosssellRelatedByRelatedProductId)
    {
        if ($this->getCrosssellsRelatedByRelatedProductId()->contains($crosssellRelatedByRelatedProductId)) {
            $this->collCrosssellsRelatedByRelatedProductId->remove($this->collCrosssellsRelatedByRelatedProductId->search($crosssellRelatedByRelatedProductId));
            if (null === $this->crosssellsRelatedByRelatedProductIdScheduledForDeletion) {
                $this->crosssellsRelatedByRelatedProductIdScheduledForDeletion = clone $this->collCrosssellsRelatedByRelatedProductId;
                $this->crosssellsRelatedByRelatedProductIdScheduledForDeletion->clear();
            }
            $this->crosssellsRelatedByRelatedProductIdScheduledForDeletion[]= clone $crosssellRelatedByRelatedProductId;
            $crosssellRelatedByRelatedProductId->setProductRelatedByRelatedProductId(null);
        }

        return $this;
    }

    /**
     * Clears out the collSimilarsRelatedByProductId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addSimilarsRelatedByProductId()
     */
    public function clearSimilarsRelatedByProductId()
    {
        $this->collSimilarsRelatedByProductId = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collSimilarsRelatedByProductId collection loaded partially.
     */
    public function resetPartialSimilarsRelatedByProductId($v = true)
    {
        $this->collSimilarsRelatedByProductIdPartial = $v;
    }

    /**
     * Initializes the collSimilarsRelatedByProductId collection.
     *
     * By default this just sets the collSimilarsRelatedByProductId collection to an empty array (like clearcollSimilarsRelatedByProductId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initSimilarsRelatedByProductId($overrideExisting = true)
    {
        if (null !== $this->collSimilarsRelatedByProductId && !$overrideExisting) {
            return;
        }
        $this->collSimilarsRelatedByProductId = new ObjectCollection();
        $this->collSimilarsRelatedByProductId->setModel('\Gekosale\Plugin\Similar\Model\ORM\Similar');
    }

    /**
     * Gets an array of ChildSimilar objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProduct is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildSimilar[] List of ChildSimilar objects
     * @throws PropelException
     */
    public function getSimilarsRelatedByProductId($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collSimilarsRelatedByProductIdPartial && !$this->isNew();
        if (null === $this->collSimilarsRelatedByProductId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collSimilarsRelatedByProductId) {
                // return empty collection
                $this->initSimilarsRelatedByProductId();
            } else {
                $collSimilarsRelatedByProductId = SimilarQuery::create(null, $criteria)
                    ->filterByProductRelatedByProductId($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collSimilarsRelatedByProductIdPartial && count($collSimilarsRelatedByProductId)) {
                        $this->initSimilarsRelatedByProductId(false);

                        foreach ($collSimilarsRelatedByProductId as $obj) {
                            if (false == $this->collSimilarsRelatedByProductId->contains($obj)) {
                                $this->collSimilarsRelatedByProductId->append($obj);
                            }
                        }

                        $this->collSimilarsRelatedByProductIdPartial = true;
                    }

                    reset($collSimilarsRelatedByProductId);

                    return $collSimilarsRelatedByProductId;
                }

                if ($partial && $this->collSimilarsRelatedByProductId) {
                    foreach ($this->collSimilarsRelatedByProductId as $obj) {
                        if ($obj->isNew()) {
                            $collSimilarsRelatedByProductId[] = $obj;
                        }
                    }
                }

                $this->collSimilarsRelatedByProductId = $collSimilarsRelatedByProductId;
                $this->collSimilarsRelatedByProductIdPartial = false;
            }
        }

        return $this->collSimilarsRelatedByProductId;
    }

    /**
     * Sets a collection of SimilarRelatedByProductId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $similarsRelatedByProductId A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildProduct The current object (for fluent API support)
     */
    public function setSimilarsRelatedByProductId(Collection $similarsRelatedByProductId, ConnectionInterface $con = null)
    {
        $similarsRelatedByProductIdToDelete = $this->getSimilarsRelatedByProductId(new Criteria(), $con)->diff($similarsRelatedByProductId);

        
        $this->similarsRelatedByProductIdScheduledForDeletion = $similarsRelatedByProductIdToDelete;

        foreach ($similarsRelatedByProductIdToDelete as $similarRelatedByProductIdRemoved) {
            $similarRelatedByProductIdRemoved->setProductRelatedByProductId(null);
        }

        $this->collSimilarsRelatedByProductId = null;
        foreach ($similarsRelatedByProductId as $similarRelatedByProductId) {
            $this->addSimilarRelatedByProductId($similarRelatedByProductId);
        }

        $this->collSimilarsRelatedByProductId = $similarsRelatedByProductId;
        $this->collSimilarsRelatedByProductIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Similar objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Similar objects.
     * @throws PropelException
     */
    public function countSimilarsRelatedByProductId(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collSimilarsRelatedByProductIdPartial && !$this->isNew();
        if (null === $this->collSimilarsRelatedByProductId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collSimilarsRelatedByProductId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getSimilarsRelatedByProductId());
            }

            $query = SimilarQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProductRelatedByProductId($this)
                ->count($con);
        }

        return count($this->collSimilarsRelatedByProductId);
    }

    /**
     * Method called to associate a ChildSimilar object to this object
     * through the ChildSimilar foreign key attribute.
     *
     * @param    ChildSimilar $l ChildSimilar
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function addSimilarRelatedByProductId(ChildSimilar $l)
    {
        if ($this->collSimilarsRelatedByProductId === null) {
            $this->initSimilarsRelatedByProductId();
            $this->collSimilarsRelatedByProductIdPartial = true;
        }

        if (!in_array($l, $this->collSimilarsRelatedByProductId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddSimilarRelatedByProductId($l);
        }

        return $this;
    }

    /**
     * @param SimilarRelatedByProductId $similarRelatedByProductId The similarRelatedByProductId object to add.
     */
    protected function doAddSimilarRelatedByProductId($similarRelatedByProductId)
    {
        $this->collSimilarsRelatedByProductId[]= $similarRelatedByProductId;
        $similarRelatedByProductId->setProductRelatedByProductId($this);
    }

    /**
     * @param  SimilarRelatedByProductId $similarRelatedByProductId The similarRelatedByProductId object to remove.
     * @return ChildProduct The current object (for fluent API support)
     */
    public function removeSimilarRelatedByProductId($similarRelatedByProductId)
    {
        if ($this->getSimilarsRelatedByProductId()->contains($similarRelatedByProductId)) {
            $this->collSimilarsRelatedByProductId->remove($this->collSimilarsRelatedByProductId->search($similarRelatedByProductId));
            if (null === $this->similarsRelatedByProductIdScheduledForDeletion) {
                $this->similarsRelatedByProductIdScheduledForDeletion = clone $this->collSimilarsRelatedByProductId;
                $this->similarsRelatedByProductIdScheduledForDeletion->clear();
            }
            $this->similarsRelatedByProductIdScheduledForDeletion[]= clone $similarRelatedByProductId;
            $similarRelatedByProductId->setProductRelatedByProductId(null);
        }

        return $this;
    }

    /**
     * Clears out the collSimilarsRelatedByRelatedProductId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addSimilarsRelatedByRelatedProductId()
     */
    public function clearSimilarsRelatedByRelatedProductId()
    {
        $this->collSimilarsRelatedByRelatedProductId = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collSimilarsRelatedByRelatedProductId collection loaded partially.
     */
    public function resetPartialSimilarsRelatedByRelatedProductId($v = true)
    {
        $this->collSimilarsRelatedByRelatedProductIdPartial = $v;
    }

    /**
     * Initializes the collSimilarsRelatedByRelatedProductId collection.
     *
     * By default this just sets the collSimilarsRelatedByRelatedProductId collection to an empty array (like clearcollSimilarsRelatedByRelatedProductId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initSimilarsRelatedByRelatedProductId($overrideExisting = true)
    {
        if (null !== $this->collSimilarsRelatedByRelatedProductId && !$overrideExisting) {
            return;
        }
        $this->collSimilarsRelatedByRelatedProductId = new ObjectCollection();
        $this->collSimilarsRelatedByRelatedProductId->setModel('\Gekosale\Plugin\Similar\Model\ORM\Similar');
    }

    /**
     * Gets an array of ChildSimilar objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProduct is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildSimilar[] List of ChildSimilar objects
     * @throws PropelException
     */
    public function getSimilarsRelatedByRelatedProductId($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collSimilarsRelatedByRelatedProductIdPartial && !$this->isNew();
        if (null === $this->collSimilarsRelatedByRelatedProductId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collSimilarsRelatedByRelatedProductId) {
                // return empty collection
                $this->initSimilarsRelatedByRelatedProductId();
            } else {
                $collSimilarsRelatedByRelatedProductId = SimilarQuery::create(null, $criteria)
                    ->filterByProductRelatedByRelatedProductId($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collSimilarsRelatedByRelatedProductIdPartial && count($collSimilarsRelatedByRelatedProductId)) {
                        $this->initSimilarsRelatedByRelatedProductId(false);

                        foreach ($collSimilarsRelatedByRelatedProductId as $obj) {
                            if (false == $this->collSimilarsRelatedByRelatedProductId->contains($obj)) {
                                $this->collSimilarsRelatedByRelatedProductId->append($obj);
                            }
                        }

                        $this->collSimilarsRelatedByRelatedProductIdPartial = true;
                    }

                    reset($collSimilarsRelatedByRelatedProductId);

                    return $collSimilarsRelatedByRelatedProductId;
                }

                if ($partial && $this->collSimilarsRelatedByRelatedProductId) {
                    foreach ($this->collSimilarsRelatedByRelatedProductId as $obj) {
                        if ($obj->isNew()) {
                            $collSimilarsRelatedByRelatedProductId[] = $obj;
                        }
                    }
                }

                $this->collSimilarsRelatedByRelatedProductId = $collSimilarsRelatedByRelatedProductId;
                $this->collSimilarsRelatedByRelatedProductIdPartial = false;
            }
        }

        return $this->collSimilarsRelatedByRelatedProductId;
    }

    /**
     * Sets a collection of SimilarRelatedByRelatedProductId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $similarsRelatedByRelatedProductId A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildProduct The current object (for fluent API support)
     */
    public function setSimilarsRelatedByRelatedProductId(Collection $similarsRelatedByRelatedProductId, ConnectionInterface $con = null)
    {
        $similarsRelatedByRelatedProductIdToDelete = $this->getSimilarsRelatedByRelatedProductId(new Criteria(), $con)->diff($similarsRelatedByRelatedProductId);

        
        $this->similarsRelatedByRelatedProductIdScheduledForDeletion = $similarsRelatedByRelatedProductIdToDelete;

        foreach ($similarsRelatedByRelatedProductIdToDelete as $similarRelatedByRelatedProductIdRemoved) {
            $similarRelatedByRelatedProductIdRemoved->setProductRelatedByRelatedProductId(null);
        }

        $this->collSimilarsRelatedByRelatedProductId = null;
        foreach ($similarsRelatedByRelatedProductId as $similarRelatedByRelatedProductId) {
            $this->addSimilarRelatedByRelatedProductId($similarRelatedByRelatedProductId);
        }

        $this->collSimilarsRelatedByRelatedProductId = $similarsRelatedByRelatedProductId;
        $this->collSimilarsRelatedByRelatedProductIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Similar objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Similar objects.
     * @throws PropelException
     */
    public function countSimilarsRelatedByRelatedProductId(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collSimilarsRelatedByRelatedProductIdPartial && !$this->isNew();
        if (null === $this->collSimilarsRelatedByRelatedProductId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collSimilarsRelatedByRelatedProductId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getSimilarsRelatedByRelatedProductId());
            }

            $query = SimilarQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProductRelatedByRelatedProductId($this)
                ->count($con);
        }

        return count($this->collSimilarsRelatedByRelatedProductId);
    }

    /**
     * Method called to associate a ChildSimilar object to this object
     * through the ChildSimilar foreign key attribute.
     *
     * @param    ChildSimilar $l ChildSimilar
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function addSimilarRelatedByRelatedProductId(ChildSimilar $l)
    {
        if ($this->collSimilarsRelatedByRelatedProductId === null) {
            $this->initSimilarsRelatedByRelatedProductId();
            $this->collSimilarsRelatedByRelatedProductIdPartial = true;
        }

        if (!in_array($l, $this->collSimilarsRelatedByRelatedProductId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddSimilarRelatedByRelatedProductId($l);
        }

        return $this;
    }

    /**
     * @param SimilarRelatedByRelatedProductId $similarRelatedByRelatedProductId The similarRelatedByRelatedProductId object to add.
     */
    protected function doAddSimilarRelatedByRelatedProductId($similarRelatedByRelatedProductId)
    {
        $this->collSimilarsRelatedByRelatedProductId[]= $similarRelatedByRelatedProductId;
        $similarRelatedByRelatedProductId->setProductRelatedByRelatedProductId($this);
    }

    /**
     * @param  SimilarRelatedByRelatedProductId $similarRelatedByRelatedProductId The similarRelatedByRelatedProductId object to remove.
     * @return ChildProduct The current object (for fluent API support)
     */
    public function removeSimilarRelatedByRelatedProductId($similarRelatedByRelatedProductId)
    {
        if ($this->getSimilarsRelatedByRelatedProductId()->contains($similarRelatedByRelatedProductId)) {
            $this->collSimilarsRelatedByRelatedProductId->remove($this->collSimilarsRelatedByRelatedProductId->search($similarRelatedByRelatedProductId));
            if (null === $this->similarsRelatedByRelatedProductIdScheduledForDeletion) {
                $this->similarsRelatedByRelatedProductIdScheduledForDeletion = clone $this->collSimilarsRelatedByRelatedProductId;
                $this->similarsRelatedByRelatedProductIdScheduledForDeletion->clear();
            }
            $this->similarsRelatedByRelatedProductIdScheduledForDeletion[]= clone $similarRelatedByRelatedProductId;
            $similarRelatedByRelatedProductId->setProductRelatedByRelatedProductId(null);
        }

        return $this;
    }

    /**
     * Clears out the collUpsellsRelatedByProductId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addUpsellsRelatedByProductId()
     */
    public function clearUpsellsRelatedByProductId()
    {
        $this->collUpsellsRelatedByProductId = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collUpsellsRelatedByProductId collection loaded partially.
     */
    public function resetPartialUpsellsRelatedByProductId($v = true)
    {
        $this->collUpsellsRelatedByProductIdPartial = $v;
    }

    /**
     * Initializes the collUpsellsRelatedByProductId collection.
     *
     * By default this just sets the collUpsellsRelatedByProductId collection to an empty array (like clearcollUpsellsRelatedByProductId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initUpsellsRelatedByProductId($overrideExisting = true)
    {
        if (null !== $this->collUpsellsRelatedByProductId && !$overrideExisting) {
            return;
        }
        $this->collUpsellsRelatedByProductId = new ObjectCollection();
        $this->collUpsellsRelatedByProductId->setModel('\Gekosale\Plugin\Upsell\Model\ORM\Upsell');
    }

    /**
     * Gets an array of ChildUpsell objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProduct is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildUpsell[] List of ChildUpsell objects
     * @throws PropelException
     */
    public function getUpsellsRelatedByProductId($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collUpsellsRelatedByProductIdPartial && !$this->isNew();
        if (null === $this->collUpsellsRelatedByProductId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collUpsellsRelatedByProductId) {
                // return empty collection
                $this->initUpsellsRelatedByProductId();
            } else {
                $collUpsellsRelatedByProductId = UpsellQuery::create(null, $criteria)
                    ->filterByProductRelatedByProductId($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collUpsellsRelatedByProductIdPartial && count($collUpsellsRelatedByProductId)) {
                        $this->initUpsellsRelatedByProductId(false);

                        foreach ($collUpsellsRelatedByProductId as $obj) {
                            if (false == $this->collUpsellsRelatedByProductId->contains($obj)) {
                                $this->collUpsellsRelatedByProductId->append($obj);
                            }
                        }

                        $this->collUpsellsRelatedByProductIdPartial = true;
                    }

                    reset($collUpsellsRelatedByProductId);

                    return $collUpsellsRelatedByProductId;
                }

                if ($partial && $this->collUpsellsRelatedByProductId) {
                    foreach ($this->collUpsellsRelatedByProductId as $obj) {
                        if ($obj->isNew()) {
                            $collUpsellsRelatedByProductId[] = $obj;
                        }
                    }
                }

                $this->collUpsellsRelatedByProductId = $collUpsellsRelatedByProductId;
                $this->collUpsellsRelatedByProductIdPartial = false;
            }
        }

        return $this->collUpsellsRelatedByProductId;
    }

    /**
     * Sets a collection of UpsellRelatedByProductId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $upsellsRelatedByProductId A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildProduct The current object (for fluent API support)
     */
    public function setUpsellsRelatedByProductId(Collection $upsellsRelatedByProductId, ConnectionInterface $con = null)
    {
        $upsellsRelatedByProductIdToDelete = $this->getUpsellsRelatedByProductId(new Criteria(), $con)->diff($upsellsRelatedByProductId);

        
        $this->upsellsRelatedByProductIdScheduledForDeletion = $upsellsRelatedByProductIdToDelete;

        foreach ($upsellsRelatedByProductIdToDelete as $upsellRelatedByProductIdRemoved) {
            $upsellRelatedByProductIdRemoved->setProductRelatedByProductId(null);
        }

        $this->collUpsellsRelatedByProductId = null;
        foreach ($upsellsRelatedByProductId as $upsellRelatedByProductId) {
            $this->addUpsellRelatedByProductId($upsellRelatedByProductId);
        }

        $this->collUpsellsRelatedByProductId = $upsellsRelatedByProductId;
        $this->collUpsellsRelatedByProductIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Upsell objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Upsell objects.
     * @throws PropelException
     */
    public function countUpsellsRelatedByProductId(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collUpsellsRelatedByProductIdPartial && !$this->isNew();
        if (null === $this->collUpsellsRelatedByProductId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collUpsellsRelatedByProductId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getUpsellsRelatedByProductId());
            }

            $query = UpsellQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProductRelatedByProductId($this)
                ->count($con);
        }

        return count($this->collUpsellsRelatedByProductId);
    }

    /**
     * Method called to associate a ChildUpsell object to this object
     * through the ChildUpsell foreign key attribute.
     *
     * @param    ChildUpsell $l ChildUpsell
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function addUpsellRelatedByProductId(ChildUpsell $l)
    {
        if ($this->collUpsellsRelatedByProductId === null) {
            $this->initUpsellsRelatedByProductId();
            $this->collUpsellsRelatedByProductIdPartial = true;
        }

        if (!in_array($l, $this->collUpsellsRelatedByProductId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddUpsellRelatedByProductId($l);
        }

        return $this;
    }

    /**
     * @param UpsellRelatedByProductId $upsellRelatedByProductId The upsellRelatedByProductId object to add.
     */
    protected function doAddUpsellRelatedByProductId($upsellRelatedByProductId)
    {
        $this->collUpsellsRelatedByProductId[]= $upsellRelatedByProductId;
        $upsellRelatedByProductId->setProductRelatedByProductId($this);
    }

    /**
     * @param  UpsellRelatedByProductId $upsellRelatedByProductId The upsellRelatedByProductId object to remove.
     * @return ChildProduct The current object (for fluent API support)
     */
    public function removeUpsellRelatedByProductId($upsellRelatedByProductId)
    {
        if ($this->getUpsellsRelatedByProductId()->contains($upsellRelatedByProductId)) {
            $this->collUpsellsRelatedByProductId->remove($this->collUpsellsRelatedByProductId->search($upsellRelatedByProductId));
            if (null === $this->upsellsRelatedByProductIdScheduledForDeletion) {
                $this->upsellsRelatedByProductIdScheduledForDeletion = clone $this->collUpsellsRelatedByProductId;
                $this->upsellsRelatedByProductIdScheduledForDeletion->clear();
            }
            $this->upsellsRelatedByProductIdScheduledForDeletion[]= clone $upsellRelatedByProductId;
            $upsellRelatedByProductId->setProductRelatedByProductId(null);
        }

        return $this;
    }

    /**
     * Clears out the collUpsellsRelatedByRelatedProductId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addUpsellsRelatedByRelatedProductId()
     */
    public function clearUpsellsRelatedByRelatedProductId()
    {
        $this->collUpsellsRelatedByRelatedProductId = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collUpsellsRelatedByRelatedProductId collection loaded partially.
     */
    public function resetPartialUpsellsRelatedByRelatedProductId($v = true)
    {
        $this->collUpsellsRelatedByRelatedProductIdPartial = $v;
    }

    /**
     * Initializes the collUpsellsRelatedByRelatedProductId collection.
     *
     * By default this just sets the collUpsellsRelatedByRelatedProductId collection to an empty array (like clearcollUpsellsRelatedByRelatedProductId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initUpsellsRelatedByRelatedProductId($overrideExisting = true)
    {
        if (null !== $this->collUpsellsRelatedByRelatedProductId && !$overrideExisting) {
            return;
        }
        $this->collUpsellsRelatedByRelatedProductId = new ObjectCollection();
        $this->collUpsellsRelatedByRelatedProductId->setModel('\Gekosale\Plugin\Upsell\Model\ORM\Upsell');
    }

    /**
     * Gets an array of ChildUpsell objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProduct is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildUpsell[] List of ChildUpsell objects
     * @throws PropelException
     */
    public function getUpsellsRelatedByRelatedProductId($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collUpsellsRelatedByRelatedProductIdPartial && !$this->isNew();
        if (null === $this->collUpsellsRelatedByRelatedProductId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collUpsellsRelatedByRelatedProductId) {
                // return empty collection
                $this->initUpsellsRelatedByRelatedProductId();
            } else {
                $collUpsellsRelatedByRelatedProductId = UpsellQuery::create(null, $criteria)
                    ->filterByProductRelatedByRelatedProductId($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collUpsellsRelatedByRelatedProductIdPartial && count($collUpsellsRelatedByRelatedProductId)) {
                        $this->initUpsellsRelatedByRelatedProductId(false);

                        foreach ($collUpsellsRelatedByRelatedProductId as $obj) {
                            if (false == $this->collUpsellsRelatedByRelatedProductId->contains($obj)) {
                                $this->collUpsellsRelatedByRelatedProductId->append($obj);
                            }
                        }

                        $this->collUpsellsRelatedByRelatedProductIdPartial = true;
                    }

                    reset($collUpsellsRelatedByRelatedProductId);

                    return $collUpsellsRelatedByRelatedProductId;
                }

                if ($partial && $this->collUpsellsRelatedByRelatedProductId) {
                    foreach ($this->collUpsellsRelatedByRelatedProductId as $obj) {
                        if ($obj->isNew()) {
                            $collUpsellsRelatedByRelatedProductId[] = $obj;
                        }
                    }
                }

                $this->collUpsellsRelatedByRelatedProductId = $collUpsellsRelatedByRelatedProductId;
                $this->collUpsellsRelatedByRelatedProductIdPartial = false;
            }
        }

        return $this->collUpsellsRelatedByRelatedProductId;
    }

    /**
     * Sets a collection of UpsellRelatedByRelatedProductId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $upsellsRelatedByRelatedProductId A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildProduct The current object (for fluent API support)
     */
    public function setUpsellsRelatedByRelatedProductId(Collection $upsellsRelatedByRelatedProductId, ConnectionInterface $con = null)
    {
        $upsellsRelatedByRelatedProductIdToDelete = $this->getUpsellsRelatedByRelatedProductId(new Criteria(), $con)->diff($upsellsRelatedByRelatedProductId);

        
        $this->upsellsRelatedByRelatedProductIdScheduledForDeletion = $upsellsRelatedByRelatedProductIdToDelete;

        foreach ($upsellsRelatedByRelatedProductIdToDelete as $upsellRelatedByRelatedProductIdRemoved) {
            $upsellRelatedByRelatedProductIdRemoved->setProductRelatedByRelatedProductId(null);
        }

        $this->collUpsellsRelatedByRelatedProductId = null;
        foreach ($upsellsRelatedByRelatedProductId as $upsellRelatedByRelatedProductId) {
            $this->addUpsellRelatedByRelatedProductId($upsellRelatedByRelatedProductId);
        }

        $this->collUpsellsRelatedByRelatedProductId = $upsellsRelatedByRelatedProductId;
        $this->collUpsellsRelatedByRelatedProductIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Upsell objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Upsell objects.
     * @throws PropelException
     */
    public function countUpsellsRelatedByRelatedProductId(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collUpsellsRelatedByRelatedProductIdPartial && !$this->isNew();
        if (null === $this->collUpsellsRelatedByRelatedProductId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collUpsellsRelatedByRelatedProductId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getUpsellsRelatedByRelatedProductId());
            }

            $query = UpsellQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProductRelatedByRelatedProductId($this)
                ->count($con);
        }

        return count($this->collUpsellsRelatedByRelatedProductId);
    }

    /**
     * Method called to associate a ChildUpsell object to this object
     * through the ChildUpsell foreign key attribute.
     *
     * @param    ChildUpsell $l ChildUpsell
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function addUpsellRelatedByRelatedProductId(ChildUpsell $l)
    {
        if ($this->collUpsellsRelatedByRelatedProductId === null) {
            $this->initUpsellsRelatedByRelatedProductId();
            $this->collUpsellsRelatedByRelatedProductIdPartial = true;
        }

        if (!in_array($l, $this->collUpsellsRelatedByRelatedProductId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddUpsellRelatedByRelatedProductId($l);
        }

        return $this;
    }

    /**
     * @param UpsellRelatedByRelatedProductId $upsellRelatedByRelatedProductId The upsellRelatedByRelatedProductId object to add.
     */
    protected function doAddUpsellRelatedByRelatedProductId($upsellRelatedByRelatedProductId)
    {
        $this->collUpsellsRelatedByRelatedProductId[]= $upsellRelatedByRelatedProductId;
        $upsellRelatedByRelatedProductId->setProductRelatedByRelatedProductId($this);
    }

    /**
     * @param  UpsellRelatedByRelatedProductId $upsellRelatedByRelatedProductId The upsellRelatedByRelatedProductId object to remove.
     * @return ChildProduct The current object (for fluent API support)
     */
    public function removeUpsellRelatedByRelatedProductId($upsellRelatedByRelatedProductId)
    {
        if ($this->getUpsellsRelatedByRelatedProductId()->contains($upsellRelatedByRelatedProductId)) {
            $this->collUpsellsRelatedByRelatedProductId->remove($this->collUpsellsRelatedByRelatedProductId->search($upsellRelatedByRelatedProductId));
            if (null === $this->upsellsRelatedByRelatedProductIdScheduledForDeletion) {
                $this->upsellsRelatedByRelatedProductIdScheduledForDeletion = clone $this->collUpsellsRelatedByRelatedProductId;
                $this->upsellsRelatedByRelatedProductIdScheduledForDeletion->clear();
            }
            $this->upsellsRelatedByRelatedProductIdScheduledForDeletion[]= clone $upsellRelatedByRelatedProductId;
            $upsellRelatedByRelatedProductId->setProductRelatedByRelatedProductId(null);
        }

        return $this;
    }

    /**
     * Clears out the collProductTechnicalDataGroups collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addProductTechnicalDataGroups()
     */
    public function clearProductTechnicalDataGroups()
    {
        $this->collProductTechnicalDataGroups = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collProductTechnicalDataGroups collection loaded partially.
     */
    public function resetPartialProductTechnicalDataGroups($v = true)
    {
        $this->collProductTechnicalDataGroupsPartial = $v;
    }

    /**
     * Initializes the collProductTechnicalDataGroups collection.
     *
     * By default this just sets the collProductTechnicalDataGroups collection to an empty array (like clearcollProductTechnicalDataGroups());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductTechnicalDataGroups($overrideExisting = true)
    {
        if (null !== $this->collProductTechnicalDataGroups && !$overrideExisting) {
            return;
        }
        $this->collProductTechnicalDataGroups = new ObjectCollection();
        $this->collProductTechnicalDataGroups->setModel('\Gekosale\Plugin\TechnicalData\Model\ORM\ProductTechnicalDataGroup');
    }

    /**
     * Gets an array of ChildProductTechnicalDataGroup objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProduct is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildProductTechnicalDataGroup[] List of ChildProductTechnicalDataGroup objects
     * @throws PropelException
     */
    public function getProductTechnicalDataGroups($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collProductTechnicalDataGroupsPartial && !$this->isNew();
        if (null === $this->collProductTechnicalDataGroups || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductTechnicalDataGroups) {
                // return empty collection
                $this->initProductTechnicalDataGroups();
            } else {
                $collProductTechnicalDataGroups = ProductTechnicalDataGroupQuery::create(null, $criteria)
                    ->filterByProduct($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collProductTechnicalDataGroupsPartial && count($collProductTechnicalDataGroups)) {
                        $this->initProductTechnicalDataGroups(false);

                        foreach ($collProductTechnicalDataGroups as $obj) {
                            if (false == $this->collProductTechnicalDataGroups->contains($obj)) {
                                $this->collProductTechnicalDataGroups->append($obj);
                            }
                        }

                        $this->collProductTechnicalDataGroupsPartial = true;
                    }

                    reset($collProductTechnicalDataGroups);

                    return $collProductTechnicalDataGroups;
                }

                if ($partial && $this->collProductTechnicalDataGroups) {
                    foreach ($this->collProductTechnicalDataGroups as $obj) {
                        if ($obj->isNew()) {
                            $collProductTechnicalDataGroups[] = $obj;
                        }
                    }
                }

                $this->collProductTechnicalDataGroups = $collProductTechnicalDataGroups;
                $this->collProductTechnicalDataGroupsPartial = false;
            }
        }

        return $this->collProductTechnicalDataGroups;
    }

    /**
     * Sets a collection of ProductTechnicalDataGroup objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $productTechnicalDataGroups A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildProduct The current object (for fluent API support)
     */
    public function setProductTechnicalDataGroups(Collection $productTechnicalDataGroups, ConnectionInterface $con = null)
    {
        $productTechnicalDataGroupsToDelete = $this->getProductTechnicalDataGroups(new Criteria(), $con)->diff($productTechnicalDataGroups);

        
        $this->productTechnicalDataGroupsScheduledForDeletion = $productTechnicalDataGroupsToDelete;

        foreach ($productTechnicalDataGroupsToDelete as $productTechnicalDataGroupRemoved) {
            $productTechnicalDataGroupRemoved->setProduct(null);
        }

        $this->collProductTechnicalDataGroups = null;
        foreach ($productTechnicalDataGroups as $productTechnicalDataGroup) {
            $this->addProductTechnicalDataGroup($productTechnicalDataGroup);
        }

        $this->collProductTechnicalDataGroups = $productTechnicalDataGroups;
        $this->collProductTechnicalDataGroupsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProductTechnicalDataGroup objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ProductTechnicalDataGroup objects.
     * @throws PropelException
     */
    public function countProductTechnicalDataGroups(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collProductTechnicalDataGroupsPartial && !$this->isNew();
        if (null === $this->collProductTechnicalDataGroups || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductTechnicalDataGroups) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProductTechnicalDataGroups());
            }

            $query = ProductTechnicalDataGroupQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProduct($this)
                ->count($con);
        }

        return count($this->collProductTechnicalDataGroups);
    }

    /**
     * Method called to associate a ChildProductTechnicalDataGroup object to this object
     * through the ChildProductTechnicalDataGroup foreign key attribute.
     *
     * @param    ChildProductTechnicalDataGroup $l ChildProductTechnicalDataGroup
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function addProductTechnicalDataGroup(ChildProductTechnicalDataGroup $l)
    {
        if ($this->collProductTechnicalDataGroups === null) {
            $this->initProductTechnicalDataGroups();
            $this->collProductTechnicalDataGroupsPartial = true;
        }

        if (!in_array($l, $this->collProductTechnicalDataGroups->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductTechnicalDataGroup($l);
        }

        return $this;
    }

    /**
     * @param ProductTechnicalDataGroup $productTechnicalDataGroup The productTechnicalDataGroup object to add.
     */
    protected function doAddProductTechnicalDataGroup($productTechnicalDataGroup)
    {
        $this->collProductTechnicalDataGroups[]= $productTechnicalDataGroup;
        $productTechnicalDataGroup->setProduct($this);
    }

    /**
     * @param  ProductTechnicalDataGroup $productTechnicalDataGroup The productTechnicalDataGroup object to remove.
     * @return ChildProduct The current object (for fluent API support)
     */
    public function removeProductTechnicalDataGroup($productTechnicalDataGroup)
    {
        if ($this->getProductTechnicalDataGroups()->contains($productTechnicalDataGroup)) {
            $this->collProductTechnicalDataGroups->remove($this->collProductTechnicalDataGroups->search($productTechnicalDataGroup));
            if (null === $this->productTechnicalDataGroupsScheduledForDeletion) {
                $this->productTechnicalDataGroupsScheduledForDeletion = clone $this->collProductTechnicalDataGroups;
                $this->productTechnicalDataGroupsScheduledForDeletion->clear();
            }
            $this->productTechnicalDataGroupsScheduledForDeletion[]= clone $productTechnicalDataGroup;
            $productTechnicalDataGroup->setProduct(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Product is new, it will return
     * an empty collection; or if this Product has previously
     * been saved, it will retrieve related ProductTechnicalDataGroups from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Product.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProductTechnicalDataGroup[] List of ChildProductTechnicalDataGroup objects
     */
    public function getProductTechnicalDataGroupsJoinTechnicalDataGroup($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ProductTechnicalDataGroupQuery::create(null, $criteria);
        $query->joinWith('TechnicalDataGroup', $joinBehavior);

        return $this->getProductTechnicalDataGroups($query, $con);
    }

    /**
     * Clears out the collWishlists collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addWishlists()
     */
    public function clearWishlists()
    {
        $this->collWishlists = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collWishlists collection loaded partially.
     */
    public function resetPartialWishlists($v = true)
    {
        $this->collWishlistsPartial = $v;
    }

    /**
     * Initializes the collWishlists collection.
     *
     * By default this just sets the collWishlists collection to an empty array (like clearcollWishlists());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initWishlists($overrideExisting = true)
    {
        if (null !== $this->collWishlists && !$overrideExisting) {
            return;
        }
        $this->collWishlists = new ObjectCollection();
        $this->collWishlists->setModel('\Gekosale\Plugin\Wishlist\Model\ORM\Wishlist');
    }

    /**
     * Gets an array of ChildWishlist objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildProduct is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildWishlist[] List of ChildWishlist objects
     * @throws PropelException
     */
    public function getWishlists($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collWishlistsPartial && !$this->isNew();
        if (null === $this->collWishlists || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collWishlists) {
                // return empty collection
                $this->initWishlists();
            } else {
                $collWishlists = WishlistQuery::create(null, $criteria)
                    ->filterByProduct($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collWishlistsPartial && count($collWishlists)) {
                        $this->initWishlists(false);

                        foreach ($collWishlists as $obj) {
                            if (false == $this->collWishlists->contains($obj)) {
                                $this->collWishlists->append($obj);
                            }
                        }

                        $this->collWishlistsPartial = true;
                    }

                    reset($collWishlists);

                    return $collWishlists;
                }

                if ($partial && $this->collWishlists) {
                    foreach ($this->collWishlists as $obj) {
                        if ($obj->isNew()) {
                            $collWishlists[] = $obj;
                        }
                    }
                }

                $this->collWishlists = $collWishlists;
                $this->collWishlistsPartial = false;
            }
        }

        return $this->collWishlists;
    }

    /**
     * Sets a collection of Wishlist objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $wishlists A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildProduct The current object (for fluent API support)
     */
    public function setWishlists(Collection $wishlists, ConnectionInterface $con = null)
    {
        $wishlistsToDelete = $this->getWishlists(new Criteria(), $con)->diff($wishlists);

        
        $this->wishlistsScheduledForDeletion = $wishlistsToDelete;

        foreach ($wishlistsToDelete as $wishlistRemoved) {
            $wishlistRemoved->setProduct(null);
        }

        $this->collWishlists = null;
        foreach ($wishlists as $wishlist) {
            $this->addWishlist($wishlist);
        }

        $this->collWishlists = $wishlists;
        $this->collWishlistsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Wishlist objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Wishlist objects.
     * @throws PropelException
     */
    public function countWishlists(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collWishlistsPartial && !$this->isNew();
        if (null === $this->collWishlists || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collWishlists) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getWishlists());
            }

            $query = WishlistQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByProduct($this)
                ->count($con);
        }

        return count($this->collWishlists);
    }

    /**
     * Method called to associate a ChildWishlist object to this object
     * through the ChildWishlist foreign key attribute.
     *
     * @param    ChildWishlist $l ChildWishlist
     * @return   \Gekosale\Plugin\Product\Model\ORM\Product The current object (for fluent API support)
     */
    public function addWishlist(ChildWishlist $l)
    {
        if ($this->collWishlists === null) {
            $this->initWishlists();
            $this->collWishlistsPartial = true;
        }

        if (!in_array($l, $this->collWishlists->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddWishlist($l);
        }

        return $this;
    }

    /**
     * @param Wishlist $wishlist The wishlist object to add.
     */
    protected function doAddWishlist($wishlist)
    {
        $this->collWishlists[]= $wishlist;
        $wishlist->setProduct($this);
    }

    /**
     * @param  Wishlist $wishlist The wishlist object to remove.
     * @return ChildProduct The current object (for fluent API support)
     */
    public function removeWishlist($wishlist)
    {
        if ($this->getWishlists()->contains($wishlist)) {
            $this->collWishlists->remove($this->collWishlists->search($wishlist));
            if (null === $this->wishlistsScheduledForDeletion) {
                $this->wishlistsScheduledForDeletion = clone $this->collWishlists;
                $this->wishlistsScheduledForDeletion->clear();
            }
            $this->wishlistsScheduledForDeletion[]= clone $wishlist;
            $wishlist->setProduct(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Product is new, it will return
     * an empty collection; or if this Product has previously
     * been saved, it will retrieve related Wishlists from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Product.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildWishlist[] List of ChildWishlist objects
     */
    public function getWishlistsJoinClient($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = WishlistQuery::create(null, $criteria);
        $query->joinWith('Client', $joinBehavior);

        return $this->getWishlists($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Product is new, it will return
     * an empty collection; or if this Product has previously
     * been saved, it will retrieve related Wishlists from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Product.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildWishlist[] List of ChildWishlist objects
     */
    public function getWishlistsJoinShop($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = WishlistQuery::create(null, $criteria);
        $query->joinWith('Shop', $joinBehavior);

        return $this->getWishlists($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->deliveler_code = null;
        $this->ean = null;
        $this->barcode = null;
        $this->buy_price = null;
        $this->sell_price = null;
        $this->producer_id = null;
        $this->vat_id = null;
        $this->stock = null;
        $this->add_date = null;
        $this->weight = null;
        $this->buy_currency_id = null;
        $this->sell_currency_id = null;
        $this->technical_data_set_id = null;
        $this->track_stock = null;
        $this->enable = null;
        $this->promotion = null;
        $this->discount_price = null;
        $this->promotion_start = null;
        $this->promotion_end = null;
        $this->shoped = null;
        $this->width = null;
        $this->height = null;
        $this->deepth = null;
        $this->unit_measure_id = null;
        $this->disable_at_stock = null;
        $this->availability_id = null;
        $this->hierarchy = null;
        $this->package_size = null;
        $this->disable_at_stock_enabled = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collMissingCartProducts) {
                foreach ($this->collMissingCartProducts as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collOrderProducts) {
                foreach ($this->collOrderProducts as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductAttributes) {
                foreach ($this->collProductAttributes as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductCategories) {
                foreach ($this->collProductCategories as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collDelivererProducts) {
                foreach ($this->collDelivererProducts as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductFiles) {
                foreach ($this->collProductFiles as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductGroupPrices) {
                foreach ($this->collProductGroupPrices as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductNews) {
                foreach ($this->collProductNews as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductPhotos) {
                foreach ($this->collProductPhotos as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCrosssellsRelatedByProductId) {
                foreach ($this->collCrosssellsRelatedByProductId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCrosssellsRelatedByRelatedProductId) {
                foreach ($this->collCrosssellsRelatedByRelatedProductId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collSimilarsRelatedByProductId) {
                foreach ($this->collSimilarsRelatedByProductId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collSimilarsRelatedByRelatedProductId) {
                foreach ($this->collSimilarsRelatedByRelatedProductId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collUpsellsRelatedByProductId) {
                foreach ($this->collUpsellsRelatedByProductId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collUpsellsRelatedByRelatedProductId) {
                foreach ($this->collUpsellsRelatedByRelatedProductId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductTechnicalDataGroups) {
                foreach ($this->collProductTechnicalDataGroups as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collWishlists) {
                foreach ($this->collWishlists as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collMissingCartProducts = null;
        $this->collOrderProducts = null;
        $this->collProductAttributes = null;
        $this->collProductCategories = null;
        $this->collDelivererProducts = null;
        $this->collProductFiles = null;
        $this->collProductGroupPrices = null;
        $this->collProductNews = null;
        $this->collProductPhotos = null;
        $this->collCrosssellsRelatedByProductId = null;
        $this->collCrosssellsRelatedByRelatedProductId = null;
        $this->collSimilarsRelatedByProductId = null;
        $this->collSimilarsRelatedByRelatedProductId = null;
        $this->collUpsellsRelatedByProductId = null;
        $this->collUpsellsRelatedByRelatedProductId = null;
        $this->collProductTechnicalDataGroups = null;
        $this->collWishlists = null;
        $this->aAvailability = null;
        $this->aCurrencyRelatedByBuyCurrencyId = null;
        $this->aProducer = null;
        $this->aCurrencyRelatedBySellCurrencyId = null;
        $this->aUnitMeasure = null;
        $this->aVat = null;
        $this->aTechnicalDataSet = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ProductTableMap::DEFAULT_STRING_FORMAT);
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {

    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
