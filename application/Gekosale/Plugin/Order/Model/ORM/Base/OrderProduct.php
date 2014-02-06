<?php

namespace Gekosale\Plugin\Order\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Order\Model\ORM\Order as ChildOrder;
use Gekosale\Plugin\Order\Model\ORM\OrderProduct as ChildOrderProduct;
use Gekosale\Plugin\Order\Model\ORM\OrderProductAttribute as ChildOrderProductAttribute;
use Gekosale\Plugin\Order\Model\ORM\OrderProductAttributeQuery as ChildOrderProductAttributeQuery;
use Gekosale\Plugin\Order\Model\ORM\OrderProductQuery as ChildOrderProductQuery;
use Gekosale\Plugin\Order\Model\ORM\OrderQuery as ChildOrderQuery;
use Gekosale\Plugin\Order\Model\ORM\Map\OrderProductTableMap;
use Gekosale\Plugin\Product\Model\ORM\Product as ChildProduct;
use Gekosale\Plugin\Product\Model\ORM\ProductQuery;
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

abstract class OrderProduct implements ActiveRecordInterface 
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Gekosale\\Plugin\\Order\\Model\\ORM\\Map\\OrderProductTableMap';


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
     * The value for the name field.
     * @var        string
     */
    protected $name;

    /**
     * The value for the price field.
     * @var        string
     */
    protected $price;

    /**
     * The value for the quantity field.
     * @var        string
     */
    protected $quantity;

    /**
     * The value for the quantity_price field.
     * @var        string
     */
    protected $quantity_price;

    /**
     * The value for the order_id field.
     * @var        int
     */
    protected $order_id;

    /**
     * The value for the product_id field.
     * @var        int
     */
    protected $product_id;

    /**
     * The value for the product_attribute_id field.
     * @var        int
     */
    protected $product_attribute_id;

    /**
     * The value for the variant field.
     * @var        string
     */
    protected $variant;

    /**
     * The value for the vat field.
     * @var        string
     */
    protected $vat;

    /**
     * The value for the price_netto field.
     * @var        string
     */
    protected $price_netto;

    /**
     * The value for the discount_price field.
     * @var        string
     */
    protected $discount_price;

    /**
     * The value for the discount_price_netto field.
     * @var        string
     */
    protected $discount_price_netto;

    /**
     * The value for the ean field.
     * @var        string
     */
    protected $ean;

    /**
     * The value for the photo_id field.
     * @var        int
     */
    protected $photo_id;

    /**
     * @var        Order
     */
    protected $aOrder;

    /**
     * @var        Product
     */
    protected $aProduct;

    /**
     * @var        ObjectCollection|ChildOrderProductAttribute[] Collection to store aggregation of ChildOrderProductAttribute objects.
     */
    protected $collOrderProductAttributes;
    protected $collOrderProductAttributesPartial;

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
    protected $orderProductAttributesScheduledForDeletion = null;

    /**
     * Initializes internal state of Gekosale\Plugin\Order\Model\ORM\Base\OrderProduct object.
     */
    public function __construct()
    {
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
     * Compares this with another <code>OrderProduct</code> instance.  If
     * <code>obj</code> is an instance of <code>OrderProduct</code>, delegates to
     * <code>equals(OrderProduct)</code>.  Otherwise, returns <code>false</code>.
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
     * @return OrderProduct The current object, for fluid interface
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
     * @return OrderProduct The current object, for fluid interface
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
     * Get the [name] column value.
     * 
     * @return   string
     */
    public function getName()
    {

        return $this->name;
    }

    /**
     * Get the [price] column value.
     * 
     * @return   string
     */
    public function getPrice()
    {

        return $this->price;
    }

    /**
     * Get the [quantity] column value.
     * 
     * @return   string
     */
    public function getQuantity()
    {

        return $this->quantity;
    }

    /**
     * Get the [quantity_price] column value.
     * 
     * @return   string
     */
    public function getQuantityPrice()
    {

        return $this->quantity_price;
    }

    /**
     * Get the [order_id] column value.
     * 
     * @return   int
     */
    public function getOrderId()
    {

        return $this->order_id;
    }

    /**
     * Get the [product_id] column value.
     * 
     * @return   int
     */
    public function getProductId()
    {

        return $this->product_id;
    }

    /**
     * Get the [product_attribute_id] column value.
     * 
     * @return   int
     */
    public function getProductAttributeId()
    {

        return $this->product_attribute_id;
    }

    /**
     * Get the [variant] column value.
     * 
     * @return   string
     */
    public function getVariant()
    {

        return $this->variant;
    }

    /**
     * Get the [vat] column value.
     * 
     * @return   string
     */
    public function getVat()
    {

        return $this->vat;
    }

    /**
     * Get the [price_netto] column value.
     * 
     * @return   string
     */
    public function getPriceNetto()
    {

        return $this->price_netto;
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
     * Get the [discount_price_netto] column value.
     * 
     * @return   string
     */
    public function getDiscountPriceNetto()
    {

        return $this->discount_price_netto;
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
     * Get the [photo_id] column value.
     * 
     * @return   int
     */
    public function getPhotoId()
    {

        return $this->photo_id;
    }

    /**
     * Set the value of [id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderProduct The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[OrderProductTableMap::COL_ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [name] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderProduct The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[OrderProductTableMap::COL_NAME] = true;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [price] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderProduct The current object (for fluent API support)
     */
    public function setPrice($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->price !== $v) {
            $this->price = $v;
            $this->modifiedColumns[OrderProductTableMap::COL_PRICE] = true;
        }


        return $this;
    } // setPrice()

    /**
     * Set the value of [quantity] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderProduct The current object (for fluent API support)
     */
    public function setQuantity($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->quantity !== $v) {
            $this->quantity = $v;
            $this->modifiedColumns[OrderProductTableMap::COL_QUANTITY] = true;
        }


        return $this;
    } // setQuantity()

    /**
     * Set the value of [quantity_price] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderProduct The current object (for fluent API support)
     */
    public function setQuantityPrice($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->quantity_price !== $v) {
            $this->quantity_price = $v;
            $this->modifiedColumns[OrderProductTableMap::COL_QUANTITY_PRICE] = true;
        }


        return $this;
    } // setQuantityPrice()

    /**
     * Set the value of [order_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderProduct The current object (for fluent API support)
     */
    public function setOrderId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->order_id !== $v) {
            $this->order_id = $v;
            $this->modifiedColumns[OrderProductTableMap::COL_ORDER_ID] = true;
        }

        if ($this->aOrder !== null && $this->aOrder->getId() !== $v) {
            $this->aOrder = null;
        }


        return $this;
    } // setOrderId()

    /**
     * Set the value of [product_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderProduct The current object (for fluent API support)
     */
    public function setProductId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->product_id !== $v) {
            $this->product_id = $v;
            $this->modifiedColumns[OrderProductTableMap::COL_PRODUCT_ID] = true;
        }

        if ($this->aProduct !== null && $this->aProduct->getId() !== $v) {
            $this->aProduct = null;
        }


        return $this;
    } // setProductId()

    /**
     * Set the value of [product_attribute_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderProduct The current object (for fluent API support)
     */
    public function setProductAttributeId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->product_attribute_id !== $v) {
            $this->product_attribute_id = $v;
            $this->modifiedColumns[OrderProductTableMap::COL_PRODUCT_ATTRIBUTE_ID] = true;
        }


        return $this;
    } // setProductAttributeId()

    /**
     * Set the value of [variant] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderProduct The current object (for fluent API support)
     */
    public function setVariant($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->variant !== $v) {
            $this->variant = $v;
            $this->modifiedColumns[OrderProductTableMap::COL_VARIANT] = true;
        }


        return $this;
    } // setVariant()

    /**
     * Set the value of [vat] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderProduct The current object (for fluent API support)
     */
    public function setVat($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->vat !== $v) {
            $this->vat = $v;
            $this->modifiedColumns[OrderProductTableMap::COL_VAT] = true;
        }


        return $this;
    } // setVat()

    /**
     * Set the value of [price_netto] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderProduct The current object (for fluent API support)
     */
    public function setPriceNetto($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->price_netto !== $v) {
            $this->price_netto = $v;
            $this->modifiedColumns[OrderProductTableMap::COL_PRICE_NETTO] = true;
        }


        return $this;
    } // setPriceNetto()

    /**
     * Set the value of [discount_price] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderProduct The current object (for fluent API support)
     */
    public function setDiscountPrice($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->discount_price !== $v) {
            $this->discount_price = $v;
            $this->modifiedColumns[OrderProductTableMap::COL_DISCOUNT_PRICE] = true;
        }


        return $this;
    } // setDiscountPrice()

    /**
     * Set the value of [discount_price_netto] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderProduct The current object (for fluent API support)
     */
    public function setDiscountPriceNetto($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->discount_price_netto !== $v) {
            $this->discount_price_netto = $v;
            $this->modifiedColumns[OrderProductTableMap::COL_DISCOUNT_PRICE_NETTO] = true;
        }


        return $this;
    } // setDiscountPriceNetto()

    /**
     * Set the value of [ean] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderProduct The current object (for fluent API support)
     */
    public function setEan($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->ean !== $v) {
            $this->ean = $v;
            $this->modifiedColumns[OrderProductTableMap::COL_EAN] = true;
        }


        return $this;
    } // setEan()

    /**
     * Set the value of [photo_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderProduct The current object (for fluent API support)
     */
    public function setPhotoId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->photo_id !== $v) {
            $this->photo_id = $v;
            $this->modifiedColumns[OrderProductTableMap::COL_PHOTO_ID] = true;
        }


        return $this;
    } // setPhotoId()

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


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : OrderProductTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : OrderProductTableMap::translateFieldName('Name', TableMap::TYPE_PHPNAME, $indexType)];
            $this->name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : OrderProductTableMap::translateFieldName('Price', TableMap::TYPE_PHPNAME, $indexType)];
            $this->price = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : OrderProductTableMap::translateFieldName('Quantity', TableMap::TYPE_PHPNAME, $indexType)];
            $this->quantity = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : OrderProductTableMap::translateFieldName('QuantityPrice', TableMap::TYPE_PHPNAME, $indexType)];
            $this->quantity_price = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : OrderProductTableMap::translateFieldName('OrderId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->order_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : OrderProductTableMap::translateFieldName('ProductId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->product_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : OrderProductTableMap::translateFieldName('ProductAttributeId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->product_attribute_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : OrderProductTableMap::translateFieldName('Variant', TableMap::TYPE_PHPNAME, $indexType)];
            $this->variant = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 9 + $startcol : OrderProductTableMap::translateFieldName('Vat', TableMap::TYPE_PHPNAME, $indexType)];
            $this->vat = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 10 + $startcol : OrderProductTableMap::translateFieldName('PriceNetto', TableMap::TYPE_PHPNAME, $indexType)];
            $this->price_netto = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 11 + $startcol : OrderProductTableMap::translateFieldName('DiscountPrice', TableMap::TYPE_PHPNAME, $indexType)];
            $this->discount_price = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 12 + $startcol : OrderProductTableMap::translateFieldName('DiscountPriceNetto', TableMap::TYPE_PHPNAME, $indexType)];
            $this->discount_price_netto = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 13 + $startcol : OrderProductTableMap::translateFieldName('Ean', TableMap::TYPE_PHPNAME, $indexType)];
            $this->ean = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 14 + $startcol : OrderProductTableMap::translateFieldName('PhotoId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->photo_id = (null !== $col) ? (int) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 15; // 15 = OrderProductTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Gekosale\Plugin\Order\Model\ORM\OrderProduct object", 0, $e);
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
        if ($this->aOrder !== null && $this->order_id !== $this->aOrder->getId()) {
            $this->aOrder = null;
        }
        if ($this->aProduct !== null && $this->product_id !== $this->aProduct->getId()) {
            $this->aProduct = null;
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
            $con = Propel::getServiceContainer()->getReadConnection(OrderProductTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildOrderProductQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aOrder = null;
            $this->aProduct = null;
            $this->collOrderProductAttributes = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see OrderProduct::setDeleted()
     * @see OrderProduct::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(OrderProductTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildOrderProductQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(OrderProductTableMap::DATABASE_NAME);
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
                OrderProductTableMap::addInstanceToPool($this);
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

            if ($this->aOrder !== null) {
                if ($this->aOrder->isModified() || $this->aOrder->isNew()) {
                    $affectedRows += $this->aOrder->save($con);
                }
                $this->setOrder($this->aOrder);
            }

            if ($this->aProduct !== null) {
                if ($this->aProduct->isModified() || $this->aProduct->isNew()) {
                    $affectedRows += $this->aProduct->save($con);
                }
                $this->setProduct($this->aProduct);
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

            if ($this->orderProductAttributesScheduledForDeletion !== null) {
                if (!$this->orderProductAttributesScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Order\Model\ORM\OrderProductAttributeQuery::create()
                        ->filterByPrimaryKeys($this->orderProductAttributesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->orderProductAttributesScheduledForDeletion = null;
                }
            }

                if ($this->collOrderProductAttributes !== null) {
            foreach ($this->collOrderProductAttributes as $referrerFK) {
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

        $this->modifiedColumns[OrderProductTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . OrderProductTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(OrderProductTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(OrderProductTableMap::COL_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'NAME';
        }
        if ($this->isColumnModified(OrderProductTableMap::COL_PRICE)) {
            $modifiedColumns[':p' . $index++]  = 'PRICE';
        }
        if ($this->isColumnModified(OrderProductTableMap::COL_QUANTITY)) {
            $modifiedColumns[':p' . $index++]  = 'QUANTITY';
        }
        if ($this->isColumnModified(OrderProductTableMap::COL_QUANTITY_PRICE)) {
            $modifiedColumns[':p' . $index++]  = 'QUANTITY_PRICE';
        }
        if ($this->isColumnModified(OrderProductTableMap::COL_ORDER_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ORDER_ID';
        }
        if ($this->isColumnModified(OrderProductTableMap::COL_PRODUCT_ID)) {
            $modifiedColumns[':p' . $index++]  = 'PRODUCT_ID';
        }
        if ($this->isColumnModified(OrderProductTableMap::COL_PRODUCT_ATTRIBUTE_ID)) {
            $modifiedColumns[':p' . $index++]  = 'PRODUCT_ATTRIBUTE_ID';
        }
        if ($this->isColumnModified(OrderProductTableMap::COL_VARIANT)) {
            $modifiedColumns[':p' . $index++]  = 'VARIANT';
        }
        if ($this->isColumnModified(OrderProductTableMap::COL_VAT)) {
            $modifiedColumns[':p' . $index++]  = 'VAT';
        }
        if ($this->isColumnModified(OrderProductTableMap::COL_PRICE_NETTO)) {
            $modifiedColumns[':p' . $index++]  = 'PRICE_NETTO';
        }
        if ($this->isColumnModified(OrderProductTableMap::COL_DISCOUNT_PRICE)) {
            $modifiedColumns[':p' . $index++]  = 'DISCOUNT_PRICE';
        }
        if ($this->isColumnModified(OrderProductTableMap::COL_DISCOUNT_PRICE_NETTO)) {
            $modifiedColumns[':p' . $index++]  = 'DISCOUNT_PRICE_NETTO';
        }
        if ($this->isColumnModified(OrderProductTableMap::COL_EAN)) {
            $modifiedColumns[':p' . $index++]  = 'EAN';
        }
        if ($this->isColumnModified(OrderProductTableMap::COL_PHOTO_ID)) {
            $modifiedColumns[':p' . $index++]  = 'PHOTO_ID';
        }

        $sql = sprintf(
            'INSERT INTO order_product (%s) VALUES (%s)',
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
                    case 'NAME':                        
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case 'PRICE':                        
                        $stmt->bindValue($identifier, $this->price, PDO::PARAM_STR);
                        break;
                    case 'QUANTITY':                        
                        $stmt->bindValue($identifier, $this->quantity, PDO::PARAM_STR);
                        break;
                    case 'QUANTITY_PRICE':                        
                        $stmt->bindValue($identifier, $this->quantity_price, PDO::PARAM_STR);
                        break;
                    case 'ORDER_ID':                        
                        $stmt->bindValue($identifier, $this->order_id, PDO::PARAM_INT);
                        break;
                    case 'PRODUCT_ID':                        
                        $stmt->bindValue($identifier, $this->product_id, PDO::PARAM_INT);
                        break;
                    case 'PRODUCT_ATTRIBUTE_ID':                        
                        $stmt->bindValue($identifier, $this->product_attribute_id, PDO::PARAM_INT);
                        break;
                    case 'VARIANT':                        
                        $stmt->bindValue($identifier, $this->variant, PDO::PARAM_STR);
                        break;
                    case 'VAT':                        
                        $stmt->bindValue($identifier, $this->vat, PDO::PARAM_STR);
                        break;
                    case 'PRICE_NETTO':                        
                        $stmt->bindValue($identifier, $this->price_netto, PDO::PARAM_STR);
                        break;
                    case 'DISCOUNT_PRICE':                        
                        $stmt->bindValue($identifier, $this->discount_price, PDO::PARAM_STR);
                        break;
                    case 'DISCOUNT_PRICE_NETTO':                        
                        $stmt->bindValue($identifier, $this->discount_price_netto, PDO::PARAM_STR);
                        break;
                    case 'EAN':                        
                        $stmt->bindValue($identifier, $this->ean, PDO::PARAM_STR);
                        break;
                    case 'PHOTO_ID':                        
                        $stmt->bindValue($identifier, $this->photo_id, PDO::PARAM_INT);
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
        $pos = OrderProductTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getName();
                break;
            case 2:
                return $this->getPrice();
                break;
            case 3:
                return $this->getQuantity();
                break;
            case 4:
                return $this->getQuantityPrice();
                break;
            case 5:
                return $this->getOrderId();
                break;
            case 6:
                return $this->getProductId();
                break;
            case 7:
                return $this->getProductAttributeId();
                break;
            case 8:
                return $this->getVariant();
                break;
            case 9:
                return $this->getVat();
                break;
            case 10:
                return $this->getPriceNetto();
                break;
            case 11:
                return $this->getDiscountPrice();
                break;
            case 12:
                return $this->getDiscountPriceNetto();
                break;
            case 13:
                return $this->getEan();
                break;
            case 14:
                return $this->getPhotoId();
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
        if (isset($alreadyDumpedObjects['OrderProduct'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['OrderProduct'][$this->getPrimaryKey()] = true;
        $keys = OrderProductTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getName(),
            $keys[2] => $this->getPrice(),
            $keys[3] => $this->getQuantity(),
            $keys[4] => $this->getQuantityPrice(),
            $keys[5] => $this->getOrderId(),
            $keys[6] => $this->getProductId(),
            $keys[7] => $this->getProductAttributeId(),
            $keys[8] => $this->getVariant(),
            $keys[9] => $this->getVat(),
            $keys[10] => $this->getPriceNetto(),
            $keys[11] => $this->getDiscountPrice(),
            $keys[12] => $this->getDiscountPriceNetto(),
            $keys[13] => $this->getEan(),
            $keys[14] => $this->getPhotoId(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }
        
        if ($includeForeignObjects) {
            if (null !== $this->aOrder) {
                $result['Order'] = $this->aOrder->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aProduct) {
                $result['Product'] = $this->aProduct->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collOrderProductAttributes) {
                $result['OrderProductAttributes'] = $this->collOrderProductAttributes->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = OrderProductTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

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
                $this->setName($value);
                break;
            case 2:
                $this->setPrice($value);
                break;
            case 3:
                $this->setQuantity($value);
                break;
            case 4:
                $this->setQuantityPrice($value);
                break;
            case 5:
                $this->setOrderId($value);
                break;
            case 6:
                $this->setProductId($value);
                break;
            case 7:
                $this->setProductAttributeId($value);
                break;
            case 8:
                $this->setVariant($value);
                break;
            case 9:
                $this->setVat($value);
                break;
            case 10:
                $this->setPriceNetto($value);
                break;
            case 11:
                $this->setDiscountPrice($value);
                break;
            case 12:
                $this->setDiscountPriceNetto($value);
                break;
            case 13:
                $this->setEan($value);
                break;
            case 14:
                $this->setPhotoId($value);
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
        $keys = OrderProductTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setName($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setPrice($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setQuantity($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setQuantityPrice($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setOrderId($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setProductId($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setProductAttributeId($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setVariant($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setVat($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setPriceNetto($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setDiscountPrice($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setDiscountPriceNetto($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setEan($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setPhotoId($arr[$keys[14]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(OrderProductTableMap::DATABASE_NAME);

        if ($this->isColumnModified(OrderProductTableMap::COL_ID)) $criteria->add(OrderProductTableMap::COL_ID, $this->id);
        if ($this->isColumnModified(OrderProductTableMap::COL_NAME)) $criteria->add(OrderProductTableMap::COL_NAME, $this->name);
        if ($this->isColumnModified(OrderProductTableMap::COL_PRICE)) $criteria->add(OrderProductTableMap::COL_PRICE, $this->price);
        if ($this->isColumnModified(OrderProductTableMap::COL_QUANTITY)) $criteria->add(OrderProductTableMap::COL_QUANTITY, $this->quantity);
        if ($this->isColumnModified(OrderProductTableMap::COL_QUANTITY_PRICE)) $criteria->add(OrderProductTableMap::COL_QUANTITY_PRICE, $this->quantity_price);
        if ($this->isColumnModified(OrderProductTableMap::COL_ORDER_ID)) $criteria->add(OrderProductTableMap::COL_ORDER_ID, $this->order_id);
        if ($this->isColumnModified(OrderProductTableMap::COL_PRODUCT_ID)) $criteria->add(OrderProductTableMap::COL_PRODUCT_ID, $this->product_id);
        if ($this->isColumnModified(OrderProductTableMap::COL_PRODUCT_ATTRIBUTE_ID)) $criteria->add(OrderProductTableMap::COL_PRODUCT_ATTRIBUTE_ID, $this->product_attribute_id);
        if ($this->isColumnModified(OrderProductTableMap::COL_VARIANT)) $criteria->add(OrderProductTableMap::COL_VARIANT, $this->variant);
        if ($this->isColumnModified(OrderProductTableMap::COL_VAT)) $criteria->add(OrderProductTableMap::COL_VAT, $this->vat);
        if ($this->isColumnModified(OrderProductTableMap::COL_PRICE_NETTO)) $criteria->add(OrderProductTableMap::COL_PRICE_NETTO, $this->price_netto);
        if ($this->isColumnModified(OrderProductTableMap::COL_DISCOUNT_PRICE)) $criteria->add(OrderProductTableMap::COL_DISCOUNT_PRICE, $this->discount_price);
        if ($this->isColumnModified(OrderProductTableMap::COL_DISCOUNT_PRICE_NETTO)) $criteria->add(OrderProductTableMap::COL_DISCOUNT_PRICE_NETTO, $this->discount_price_netto);
        if ($this->isColumnModified(OrderProductTableMap::COL_EAN)) $criteria->add(OrderProductTableMap::COL_EAN, $this->ean);
        if ($this->isColumnModified(OrderProductTableMap::COL_PHOTO_ID)) $criteria->add(OrderProductTableMap::COL_PHOTO_ID, $this->photo_id);

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
        $criteria = new Criteria(OrderProductTableMap::DATABASE_NAME);
        $criteria->add(OrderProductTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \Gekosale\Plugin\Order\Model\ORM\OrderProduct (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setName($this->getName());
        $copyObj->setPrice($this->getPrice());
        $copyObj->setQuantity($this->getQuantity());
        $copyObj->setQuantityPrice($this->getQuantityPrice());
        $copyObj->setOrderId($this->getOrderId());
        $copyObj->setProductId($this->getProductId());
        $copyObj->setProductAttributeId($this->getProductAttributeId());
        $copyObj->setVariant($this->getVariant());
        $copyObj->setVat($this->getVat());
        $copyObj->setPriceNetto($this->getPriceNetto());
        $copyObj->setDiscountPrice($this->getDiscountPrice());
        $copyObj->setDiscountPriceNetto($this->getDiscountPriceNetto());
        $copyObj->setEan($this->getEan());
        $copyObj->setPhotoId($this->getPhotoId());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getOrderProductAttributes() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addOrderProductAttribute($relObj->copy($deepCopy));
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
     * @return                 \Gekosale\Plugin\Order\Model\ORM\OrderProduct Clone of current object.
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
     * Declares an association between this object and a ChildOrder object.
     *
     * @param                  ChildOrder $v
     * @return                 \Gekosale\Plugin\Order\Model\ORM\OrderProduct The current object (for fluent API support)
     * @throws PropelException
     */
    public function setOrder(ChildOrder $v = null)
    {
        if ($v === null) {
            $this->setOrderId(NULL);
        } else {
            $this->setOrderId($v->getId());
        }

        $this->aOrder = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildOrder object, it will not be re-added.
        if ($v !== null) {
            $v->addOrderProduct($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildOrder object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildOrder The associated ChildOrder object.
     * @throws PropelException
     */
    public function getOrder(ConnectionInterface $con = null)
    {
        if ($this->aOrder === null && ($this->order_id !== null)) {
            $this->aOrder = ChildOrderQuery::create()->findPk($this->order_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aOrder->addOrderProducts($this);
             */
        }

        return $this->aOrder;
    }

    /**
     * Declares an association between this object and a ChildProduct object.
     *
     * @param                  ChildProduct $v
     * @return                 \Gekosale\Plugin\Order\Model\ORM\OrderProduct The current object (for fluent API support)
     * @throws PropelException
     */
    public function setProduct(ChildProduct $v = null)
    {
        if ($v === null) {
            $this->setProductId(NULL);
        } else {
            $this->setProductId($v->getId());
        }

        $this->aProduct = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildProduct object, it will not be re-added.
        if ($v !== null) {
            $v->addOrderProduct($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildProduct object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildProduct The associated ChildProduct object.
     * @throws PropelException
     */
    public function getProduct(ConnectionInterface $con = null)
    {
        if ($this->aProduct === null && ($this->product_id !== null)) {
            $this->aProduct = ProductQuery::create()->findPk($this->product_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aProduct->addOrderProducts($this);
             */
        }

        return $this->aProduct;
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
        if ('OrderProductAttribute' == $relationName) {
            return $this->initOrderProductAttributes();
        }
    }

    /**
     * Clears out the collOrderProductAttributes collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addOrderProductAttributes()
     */
    public function clearOrderProductAttributes()
    {
        $this->collOrderProductAttributes = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collOrderProductAttributes collection loaded partially.
     */
    public function resetPartialOrderProductAttributes($v = true)
    {
        $this->collOrderProductAttributesPartial = $v;
    }

    /**
     * Initializes the collOrderProductAttributes collection.
     *
     * By default this just sets the collOrderProductAttributes collection to an empty array (like clearcollOrderProductAttributes());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initOrderProductAttributes($overrideExisting = true)
    {
        if (null !== $this->collOrderProductAttributes && !$overrideExisting) {
            return;
        }
        $this->collOrderProductAttributes = new ObjectCollection();
        $this->collOrderProductAttributes->setModel('\Gekosale\Plugin\Order\Model\ORM\OrderProductAttribute');
    }

    /**
     * Gets an array of ChildOrderProductAttribute objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildOrderProduct is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildOrderProductAttribute[] List of ChildOrderProductAttribute objects
     * @throws PropelException
     */
    public function getOrderProductAttributes($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collOrderProductAttributesPartial && !$this->isNew();
        if (null === $this->collOrderProductAttributes || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collOrderProductAttributes) {
                // return empty collection
                $this->initOrderProductAttributes();
            } else {
                $collOrderProductAttributes = ChildOrderProductAttributeQuery::create(null, $criteria)
                    ->filterByOrderProduct($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collOrderProductAttributesPartial && count($collOrderProductAttributes)) {
                        $this->initOrderProductAttributes(false);

                        foreach ($collOrderProductAttributes as $obj) {
                            if (false == $this->collOrderProductAttributes->contains($obj)) {
                                $this->collOrderProductAttributes->append($obj);
                            }
                        }

                        $this->collOrderProductAttributesPartial = true;
                    }

                    reset($collOrderProductAttributes);

                    return $collOrderProductAttributes;
                }

                if ($partial && $this->collOrderProductAttributes) {
                    foreach ($this->collOrderProductAttributes as $obj) {
                        if ($obj->isNew()) {
                            $collOrderProductAttributes[] = $obj;
                        }
                    }
                }

                $this->collOrderProductAttributes = $collOrderProductAttributes;
                $this->collOrderProductAttributesPartial = false;
            }
        }

        return $this->collOrderProductAttributes;
    }

    /**
     * Sets a collection of OrderProductAttribute objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $orderProductAttributes A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildOrderProduct The current object (for fluent API support)
     */
    public function setOrderProductAttributes(Collection $orderProductAttributes, ConnectionInterface $con = null)
    {
        $orderProductAttributesToDelete = $this->getOrderProductAttributes(new Criteria(), $con)->diff($orderProductAttributes);

        
        $this->orderProductAttributesScheduledForDeletion = $orderProductAttributesToDelete;

        foreach ($orderProductAttributesToDelete as $orderProductAttributeRemoved) {
            $orderProductAttributeRemoved->setOrderProduct(null);
        }

        $this->collOrderProductAttributes = null;
        foreach ($orderProductAttributes as $orderProductAttribute) {
            $this->addOrderProductAttribute($orderProductAttribute);
        }

        $this->collOrderProductAttributes = $orderProductAttributes;
        $this->collOrderProductAttributesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related OrderProductAttribute objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related OrderProductAttribute objects.
     * @throws PropelException
     */
    public function countOrderProductAttributes(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collOrderProductAttributesPartial && !$this->isNew();
        if (null === $this->collOrderProductAttributes || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collOrderProductAttributes) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getOrderProductAttributes());
            }

            $query = ChildOrderProductAttributeQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByOrderProduct($this)
                ->count($con);
        }

        return count($this->collOrderProductAttributes);
    }

    /**
     * Method called to associate a ChildOrderProductAttribute object to this object
     * through the ChildOrderProductAttribute foreign key attribute.
     *
     * @param    ChildOrderProductAttribute $l ChildOrderProductAttribute
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderProduct The current object (for fluent API support)
     */
    public function addOrderProductAttribute(ChildOrderProductAttribute $l)
    {
        if ($this->collOrderProductAttributes === null) {
            $this->initOrderProductAttributes();
            $this->collOrderProductAttributesPartial = true;
        }

        if (!in_array($l, $this->collOrderProductAttributes->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddOrderProductAttribute($l);
        }

        return $this;
    }

    /**
     * @param OrderProductAttribute $orderProductAttribute The orderProductAttribute object to add.
     */
    protected function doAddOrderProductAttribute($orderProductAttribute)
    {
        $this->collOrderProductAttributes[]= $orderProductAttribute;
        $orderProductAttribute->setOrderProduct($this);
    }

    /**
     * @param  OrderProductAttribute $orderProductAttribute The orderProductAttribute object to remove.
     * @return ChildOrderProduct The current object (for fluent API support)
     */
    public function removeOrderProductAttribute($orderProductAttribute)
    {
        if ($this->getOrderProductAttributes()->contains($orderProductAttribute)) {
            $this->collOrderProductAttributes->remove($this->collOrderProductAttributes->search($orderProductAttribute));
            if (null === $this->orderProductAttributesScheduledForDeletion) {
                $this->orderProductAttributesScheduledForDeletion = clone $this->collOrderProductAttributes;
                $this->orderProductAttributesScheduledForDeletion->clear();
            }
            $this->orderProductAttributesScheduledForDeletion[]= clone $orderProductAttribute;
            $orderProductAttribute->setOrderProduct(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->name = null;
        $this->price = null;
        $this->quantity = null;
        $this->quantity_price = null;
        $this->order_id = null;
        $this->product_id = null;
        $this->product_attribute_id = null;
        $this->variant = null;
        $this->vat = null;
        $this->price_netto = null;
        $this->discount_price = null;
        $this->discount_price_netto = null;
        $this->ean = null;
        $this->photo_id = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
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
            if ($this->collOrderProductAttributes) {
                foreach ($this->collOrderProductAttributes as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collOrderProductAttributes = null;
        $this->aOrder = null;
        $this->aProduct = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(OrderProductTableMap::DEFAULT_STRING_FORMAT);
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