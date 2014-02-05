<?php

namespace Gekosale\Plugin\Order\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Order\Model\ORM\Order as ChildOrder;
use Gekosale\Plugin\Order\Model\ORM\OrderClientData as ChildOrderClientData;
use Gekosale\Plugin\Order\Model\ORM\OrderClientDataQuery as ChildOrderClientDataQuery;
use Gekosale\Plugin\Order\Model\ORM\OrderClientDeliveryData as ChildOrderClientDeliveryData;
use Gekosale\Plugin\Order\Model\ORM\OrderClientDeliveryDataQuery as ChildOrderClientDeliveryDataQuery;
use Gekosale\Plugin\Order\Model\ORM\OrderHistory as ChildOrderHistory;
use Gekosale\Plugin\Order\Model\ORM\OrderHistoryQuery as ChildOrderHistoryQuery;
use Gekosale\Plugin\Order\Model\ORM\OrderNotes as ChildOrderNotes;
use Gekosale\Plugin\Order\Model\ORM\OrderNotesQuery as ChildOrderNotesQuery;
use Gekosale\Plugin\Order\Model\ORM\OrderProduct as ChildOrderProduct;
use Gekosale\Plugin\Order\Model\ORM\OrderProductQuery as ChildOrderProductQuery;
use Gekosale\Plugin\Order\Model\ORM\OrderQuery as ChildOrderQuery;
use Gekosale\Plugin\Order\Model\ORM\Map\OrderTableMap;
use Gekosale\Plugin\Shop\Model\ORM\Shop as ChildShop;
use Gekosale\Plugin\Shop\Model\ORM\ShopQuery;
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

abstract class Order implements ActiveRecordInterface 
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Gekosale\\Plugin\\Order\\Model\\ORM\\Map\\OrderTableMap';


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
     * The value for the price field.
     * @var        string
     */
    protected $price;

    /**
     * The value for the dispatch_method_price field.
     * @var        string
     */
    protected $dispatch_method_price;

    /**
     * The value for the global_price field.
     * @var        string
     */
    protected $global_price;

    /**
     * The value for the order_status_id field.
     * @var        int
     */
    protected $order_status_id;

    /**
     * The value for the dispatch_method_name field.
     * @var        string
     */
    protected $dispatch_method_name;

    /**
     * The value for the payment_method_name field.
     * @var        string
     */
    protected $payment_method_name;

    /**
     * The value for the global_qty field.
     * @var        int
     */
    protected $global_qty;

    /**
     * The value for the dispatch_method_id field.
     * @var        int
     */
    protected $dispatch_method_id;

    /**
     * The value for the payment_method_id field.
     * @var        int
     */
    protected $payment_method_id;

    /**
     * The value for the client_id field.
     * @var        int
     */
    protected $client_id;

    /**
     * The value for the global_price_netto field.
     * @var        string
     */
    protected $global_price_netto;

    /**
     * The value for the active_link field.
     * @var        string
     */
    protected $active_link;

    /**
     * The value for the comment field.
     * @var        string
     */
    protected $comment;

    /**
     * The value for the shop_id field.
     * @var        int
     */
    protected $shop_id;

    /**
     * The value for the price_before_promotion field.
     * @var        string
     */
    protected $price_before_promotion;

    /**
     * The value for the currency_id field.
     * @var        int
     */
    protected $currency_id;

    /**
     * The value for the currency_symbol field.
     * @var        string
     */
    protected $currency_symbol;

    /**
     * The value for the currency_rate field.
     * @var        string
     */
    protected $currency_rate;

    /**
     * The value for the cart_rule_id field.
     * @var        int
     */
    protected $cart_rule_id;

    /**
     * The value for the session_id field.
     * @var        string
     */
    protected $session_id;

    /**
     * @var        Shop
     */
    protected $aShop;

    /**
     * @var        ObjectCollection|ChildOrderClientData[] Collection to store aggregation of ChildOrderClientData objects.
     */
    protected $collOrderClientDatas;
    protected $collOrderClientDatasPartial;

    /**
     * @var        ObjectCollection|ChildOrderClientDeliveryData[] Collection to store aggregation of ChildOrderClientDeliveryData objects.
     */
    protected $collOrderClientDeliveryDatas;
    protected $collOrderClientDeliveryDatasPartial;

    /**
     * @var        ObjectCollection|ChildOrderHistory[] Collection to store aggregation of ChildOrderHistory objects.
     */
    protected $collOrderHistories;
    protected $collOrderHistoriesPartial;

    /**
     * @var        ObjectCollection|ChildOrderNotes[] Collection to store aggregation of ChildOrderNotes objects.
     */
    protected $collOrderNotess;
    protected $collOrderNotessPartial;

    /**
     * @var        ObjectCollection|ChildOrderProduct[] Collection to store aggregation of ChildOrderProduct objects.
     */
    protected $collOrderProducts;
    protected $collOrderProductsPartial;

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
    protected $orderClientDatasScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $orderClientDeliveryDatasScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $orderHistoriesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $orderNotessScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $orderProductsScheduledForDeletion = null;

    /**
     * Initializes internal state of Gekosale\Plugin\Order\Model\ORM\Base\Order object.
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
     * Compares this with another <code>Order</code> instance.  If
     * <code>obj</code> is an instance of <code>Order</code>, delegates to
     * <code>equals(Order)</code>.  Otherwise, returns <code>false</code>.
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
     * @return Order The current object, for fluid interface
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
     * @return Order The current object, for fluid interface
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
     * Get the [price] column value.
     * 
     * @return   string
     */
    public function getPrice()
    {

        return $this->price;
    }

    /**
     * Get the [dispatch_method_price] column value.
     * 
     * @return   string
     */
    public function getDispatchMethodPrice()
    {

        return $this->dispatch_method_price;
    }

    /**
     * Get the [global_price] column value.
     * 
     * @return   string
     */
    public function getGlobalPrice()
    {

        return $this->global_price;
    }

    /**
     * Get the [order_status_id] column value.
     * 
     * @return   int
     */
    public function getOrderStatusId()
    {

        return $this->order_status_id;
    }

    /**
     * Get the [dispatch_method_name] column value.
     * 
     * @return   string
     */
    public function getDispatchMethodName()
    {

        return $this->dispatch_method_name;
    }

    /**
     * Get the [payment_method_name] column value.
     * 
     * @return   string
     */
    public function getPaymentMethodName()
    {

        return $this->payment_method_name;
    }

    /**
     * Get the [global_qty] column value.
     * 
     * @return   int
     */
    public function getGlobalQty()
    {

        return $this->global_qty;
    }

    /**
     * Get the [dispatch_method_id] column value.
     * 
     * @return   int
     */
    public function getDispatchMethodId()
    {

        return $this->dispatch_method_id;
    }

    /**
     * Get the [payment_method_id] column value.
     * 
     * @return   int
     */
    public function getPaymentMethodId()
    {

        return $this->payment_method_id;
    }

    /**
     * Get the [client_id] column value.
     * 
     * @return   int
     */
    public function getClientId()
    {

        return $this->client_id;
    }

    /**
     * Get the [global_price_netto] column value.
     * 
     * @return   string
     */
    public function getGlobalPriceNetto()
    {

        return $this->global_price_netto;
    }

    /**
     * Get the [active_link] column value.
     * 
     * @return   string
     */
    public function getActiveLink()
    {

        return $this->active_link;
    }

    /**
     * Get the [comment] column value.
     * 
     * @return   string
     */
    public function getComment()
    {

        return $this->comment;
    }

    /**
     * Get the [shop_id] column value.
     * 
     * @return   int
     */
    public function getShopId()
    {

        return $this->shop_id;
    }

    /**
     * Get the [price_before_promotion] column value.
     * 
     * @return   string
     */
    public function getPriceBeforePromotion()
    {

        return $this->price_before_promotion;
    }

    /**
     * Get the [currency_id] column value.
     * 
     * @return   int
     */
    public function getCurrencyId()
    {

        return $this->currency_id;
    }

    /**
     * Get the [currency_symbol] column value.
     * 
     * @return   string
     */
    public function getCurrencySymbol()
    {

        return $this->currency_symbol;
    }

    /**
     * Get the [currency_rate] column value.
     * 
     * @return   string
     */
    public function getCurrencyRate()
    {

        return $this->currency_rate;
    }

    /**
     * Get the [cart_rule_id] column value.
     * 
     * @return   int
     */
    public function getCartRuleId()
    {

        return $this->cart_rule_id;
    }

    /**
     * Get the [session_id] column value.
     * 
     * @return   string
     */
    public function getSessionId()
    {

        return $this->session_id;
    }

    /**
     * Set the value of [id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[OrderTableMap::COL_ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [price] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function setPrice($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->price !== $v) {
            $this->price = $v;
            $this->modifiedColumns[OrderTableMap::COL_PRICE] = true;
        }


        return $this;
    } // setPrice()

    /**
     * Set the value of [dispatch_method_price] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function setDispatchMethodPrice($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->dispatch_method_price !== $v) {
            $this->dispatch_method_price = $v;
            $this->modifiedColumns[OrderTableMap::COL_DISPATCH_METHOD_PRICE] = true;
        }


        return $this;
    } // setDispatchMethodPrice()

    /**
     * Set the value of [global_price] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function setGlobalPrice($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->global_price !== $v) {
            $this->global_price = $v;
            $this->modifiedColumns[OrderTableMap::COL_GLOBAL_PRICE] = true;
        }


        return $this;
    } // setGlobalPrice()

    /**
     * Set the value of [order_status_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function setOrderStatusId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->order_status_id !== $v) {
            $this->order_status_id = $v;
            $this->modifiedColumns[OrderTableMap::COL_ORDER_STATUS_ID] = true;
        }


        return $this;
    } // setOrderStatusId()

    /**
     * Set the value of [dispatch_method_name] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function setDispatchMethodName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->dispatch_method_name !== $v) {
            $this->dispatch_method_name = $v;
            $this->modifiedColumns[OrderTableMap::COL_DISPATCH_METHOD_NAME] = true;
        }


        return $this;
    } // setDispatchMethodName()

    /**
     * Set the value of [payment_method_name] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function setPaymentMethodName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->payment_method_name !== $v) {
            $this->payment_method_name = $v;
            $this->modifiedColumns[OrderTableMap::COL_PAYMENT_METHOD_NAME] = true;
        }


        return $this;
    } // setPaymentMethodName()

    /**
     * Set the value of [global_qty] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function setGlobalQty($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->global_qty !== $v) {
            $this->global_qty = $v;
            $this->modifiedColumns[OrderTableMap::COL_GLOBAL_QTY] = true;
        }


        return $this;
    } // setGlobalQty()

    /**
     * Set the value of [dispatch_method_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function setDispatchMethodId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->dispatch_method_id !== $v) {
            $this->dispatch_method_id = $v;
            $this->modifiedColumns[OrderTableMap::COL_DISPATCH_METHOD_ID] = true;
        }


        return $this;
    } // setDispatchMethodId()

    /**
     * Set the value of [payment_method_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function setPaymentMethodId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->payment_method_id !== $v) {
            $this->payment_method_id = $v;
            $this->modifiedColumns[OrderTableMap::COL_PAYMENT_METHOD_ID] = true;
        }


        return $this;
    } // setPaymentMethodId()

    /**
     * Set the value of [client_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function setClientId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->client_id !== $v) {
            $this->client_id = $v;
            $this->modifiedColumns[OrderTableMap::COL_CLIENT_ID] = true;
        }


        return $this;
    } // setClientId()

    /**
     * Set the value of [global_price_netto] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function setGlobalPriceNetto($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->global_price_netto !== $v) {
            $this->global_price_netto = $v;
            $this->modifiedColumns[OrderTableMap::COL_GLOBAL_PRICE_NETTO] = true;
        }


        return $this;
    } // setGlobalPriceNetto()

    /**
     * Set the value of [active_link] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function setActiveLink($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->active_link !== $v) {
            $this->active_link = $v;
            $this->modifiedColumns[OrderTableMap::COL_ACTIVE_LINK] = true;
        }


        return $this;
    } // setActiveLink()

    /**
     * Set the value of [comment] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function setComment($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->comment !== $v) {
            $this->comment = $v;
            $this->modifiedColumns[OrderTableMap::COL_COMMENT] = true;
        }


        return $this;
    } // setComment()

    /**
     * Set the value of [shop_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function setShopId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->shop_id !== $v) {
            $this->shop_id = $v;
            $this->modifiedColumns[OrderTableMap::COL_SHOP_ID] = true;
        }

        if ($this->aShop !== null && $this->aShop->getId() !== $v) {
            $this->aShop = null;
        }


        return $this;
    } // setShopId()

    /**
     * Set the value of [price_before_promotion] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function setPriceBeforePromotion($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->price_before_promotion !== $v) {
            $this->price_before_promotion = $v;
            $this->modifiedColumns[OrderTableMap::COL_PRICE_BEFORE_PROMOTION] = true;
        }


        return $this;
    } // setPriceBeforePromotion()

    /**
     * Set the value of [currency_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function setCurrencyId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->currency_id !== $v) {
            $this->currency_id = $v;
            $this->modifiedColumns[OrderTableMap::COL_CURRENCY_ID] = true;
        }


        return $this;
    } // setCurrencyId()

    /**
     * Set the value of [currency_symbol] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function setCurrencySymbol($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->currency_symbol !== $v) {
            $this->currency_symbol = $v;
            $this->modifiedColumns[OrderTableMap::COL_CURRENCY_SYMBOL] = true;
        }


        return $this;
    } // setCurrencySymbol()

    /**
     * Set the value of [currency_rate] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function setCurrencyRate($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->currency_rate !== $v) {
            $this->currency_rate = $v;
            $this->modifiedColumns[OrderTableMap::COL_CURRENCY_RATE] = true;
        }


        return $this;
    } // setCurrencyRate()

    /**
     * Set the value of [cart_rule_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function setCartRuleId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->cart_rule_id !== $v) {
            $this->cart_rule_id = $v;
            $this->modifiedColumns[OrderTableMap::COL_CART_RULE_ID] = true;
        }


        return $this;
    } // setCartRuleId()

    /**
     * Set the value of [session_id] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function setSessionId($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->session_id !== $v) {
            $this->session_id = $v;
            $this->modifiedColumns[OrderTableMap::COL_SESSION_ID] = true;
        }


        return $this;
    } // setSessionId()

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


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : OrderTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : OrderTableMap::translateFieldName('Price', TableMap::TYPE_PHPNAME, $indexType)];
            $this->price = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : OrderTableMap::translateFieldName('DispatchMethodPrice', TableMap::TYPE_PHPNAME, $indexType)];
            $this->dispatch_method_price = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : OrderTableMap::translateFieldName('GlobalPrice', TableMap::TYPE_PHPNAME, $indexType)];
            $this->global_price = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : OrderTableMap::translateFieldName('OrderStatusId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->order_status_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : OrderTableMap::translateFieldName('DispatchMethodName', TableMap::TYPE_PHPNAME, $indexType)];
            $this->dispatch_method_name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : OrderTableMap::translateFieldName('PaymentMethodName', TableMap::TYPE_PHPNAME, $indexType)];
            $this->payment_method_name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : OrderTableMap::translateFieldName('GlobalQty', TableMap::TYPE_PHPNAME, $indexType)];
            $this->global_qty = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : OrderTableMap::translateFieldName('DispatchMethodId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->dispatch_method_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 9 + $startcol : OrderTableMap::translateFieldName('PaymentMethodId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->payment_method_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 10 + $startcol : OrderTableMap::translateFieldName('ClientId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->client_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 11 + $startcol : OrderTableMap::translateFieldName('GlobalPriceNetto', TableMap::TYPE_PHPNAME, $indexType)];
            $this->global_price_netto = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 12 + $startcol : OrderTableMap::translateFieldName('ActiveLink', TableMap::TYPE_PHPNAME, $indexType)];
            $this->active_link = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 13 + $startcol : OrderTableMap::translateFieldName('Comment', TableMap::TYPE_PHPNAME, $indexType)];
            $this->comment = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 14 + $startcol : OrderTableMap::translateFieldName('ShopId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->shop_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 15 + $startcol : OrderTableMap::translateFieldName('PriceBeforePromotion', TableMap::TYPE_PHPNAME, $indexType)];
            $this->price_before_promotion = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 16 + $startcol : OrderTableMap::translateFieldName('CurrencyId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->currency_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 17 + $startcol : OrderTableMap::translateFieldName('CurrencySymbol', TableMap::TYPE_PHPNAME, $indexType)];
            $this->currency_symbol = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 18 + $startcol : OrderTableMap::translateFieldName('CurrencyRate', TableMap::TYPE_PHPNAME, $indexType)];
            $this->currency_rate = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 19 + $startcol : OrderTableMap::translateFieldName('CartRuleId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->cart_rule_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 20 + $startcol : OrderTableMap::translateFieldName('SessionId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->session_id = (null !== $col) ? (string) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 21; // 21 = OrderTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Gekosale\Plugin\Order\Model\ORM\Order object", 0, $e);
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
        if ($this->aShop !== null && $this->shop_id !== $this->aShop->getId()) {
            $this->aShop = null;
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
            $con = Propel::getServiceContainer()->getReadConnection(OrderTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildOrderQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aShop = null;
            $this->collOrderClientDatas = null;

            $this->collOrderClientDeliveryDatas = null;

            $this->collOrderHistories = null;

            $this->collOrderNotess = null;

            $this->collOrderProducts = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Order::setDeleted()
     * @see Order::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(OrderTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildOrderQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(OrderTableMap::DATABASE_NAME);
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
                OrderTableMap::addInstanceToPool($this);
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

            if ($this->aShop !== null) {
                if ($this->aShop->isModified() || $this->aShop->isNew()) {
                    $affectedRows += $this->aShop->save($con);
                }
                $this->setShop($this->aShop);
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

            if ($this->orderClientDatasScheduledForDeletion !== null) {
                if (!$this->orderClientDatasScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Order\Model\ORM\OrderClientDataQuery::create()
                        ->filterByPrimaryKeys($this->orderClientDatasScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->orderClientDatasScheduledForDeletion = null;
                }
            }

                if ($this->collOrderClientDatas !== null) {
            foreach ($this->collOrderClientDatas as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->orderClientDeliveryDatasScheduledForDeletion !== null) {
                if (!$this->orderClientDeliveryDatasScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Order\Model\ORM\OrderClientDeliveryDataQuery::create()
                        ->filterByPrimaryKeys($this->orderClientDeliveryDatasScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->orderClientDeliveryDatasScheduledForDeletion = null;
                }
            }

                if ($this->collOrderClientDeliveryDatas !== null) {
            foreach ($this->collOrderClientDeliveryDatas as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->orderHistoriesScheduledForDeletion !== null) {
                if (!$this->orderHistoriesScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Order\Model\ORM\OrderHistoryQuery::create()
                        ->filterByPrimaryKeys($this->orderHistoriesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->orderHistoriesScheduledForDeletion = null;
                }
            }

                if ($this->collOrderHistories !== null) {
            foreach ($this->collOrderHistories as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->orderNotessScheduledForDeletion !== null) {
                if (!$this->orderNotessScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Order\Model\ORM\OrderNotesQuery::create()
                        ->filterByPrimaryKeys($this->orderNotessScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->orderNotessScheduledForDeletion = null;
                }
            }

                if ($this->collOrderNotess !== null) {
            foreach ($this->collOrderNotess as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->orderProductsScheduledForDeletion !== null) {
                if (!$this->orderProductsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Order\Model\ORM\OrderProductQuery::create()
                        ->filterByPrimaryKeys($this->orderProductsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
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

        $this->modifiedColumns[OrderTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . OrderTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(OrderTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(OrderTableMap::COL_PRICE)) {
            $modifiedColumns[':p' . $index++]  = 'PRICE';
        }
        if ($this->isColumnModified(OrderTableMap::COL_DISPATCH_METHOD_PRICE)) {
            $modifiedColumns[':p' . $index++]  = 'DISPATCH_METHOD_PRICE';
        }
        if ($this->isColumnModified(OrderTableMap::COL_GLOBAL_PRICE)) {
            $modifiedColumns[':p' . $index++]  = 'GLOBAL_PRICE';
        }
        if ($this->isColumnModified(OrderTableMap::COL_ORDER_STATUS_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ORDER_STATUS_ID';
        }
        if ($this->isColumnModified(OrderTableMap::COL_DISPATCH_METHOD_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'DISPATCH_METHOD_NAME';
        }
        if ($this->isColumnModified(OrderTableMap::COL_PAYMENT_METHOD_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'PAYMENT_METHOD_NAME';
        }
        if ($this->isColumnModified(OrderTableMap::COL_GLOBAL_QTY)) {
            $modifiedColumns[':p' . $index++]  = 'GLOBAL_QTY';
        }
        if ($this->isColumnModified(OrderTableMap::COL_DISPATCH_METHOD_ID)) {
            $modifiedColumns[':p' . $index++]  = 'DISPATCH_METHOD_ID';
        }
        if ($this->isColumnModified(OrderTableMap::COL_PAYMENT_METHOD_ID)) {
            $modifiedColumns[':p' . $index++]  = 'PAYMENT_METHOD_ID';
        }
        if ($this->isColumnModified(OrderTableMap::COL_CLIENT_ID)) {
            $modifiedColumns[':p' . $index++]  = 'CLIENT_ID';
        }
        if ($this->isColumnModified(OrderTableMap::COL_GLOBAL_PRICE_NETTO)) {
            $modifiedColumns[':p' . $index++]  = 'GLOBAL_PRICE_NETTO';
        }
        if ($this->isColumnModified(OrderTableMap::COL_ACTIVE_LINK)) {
            $modifiedColumns[':p' . $index++]  = 'ACTIVE_LINK';
        }
        if ($this->isColumnModified(OrderTableMap::COL_COMMENT)) {
            $modifiedColumns[':p' . $index++]  = 'COMMENT';
        }
        if ($this->isColumnModified(OrderTableMap::COL_SHOP_ID)) {
            $modifiedColumns[':p' . $index++]  = 'SHOP_ID';
        }
        if ($this->isColumnModified(OrderTableMap::COL_PRICE_BEFORE_PROMOTION)) {
            $modifiedColumns[':p' . $index++]  = 'PRICE_BEFORE_PROMOTION';
        }
        if ($this->isColumnModified(OrderTableMap::COL_CURRENCY_ID)) {
            $modifiedColumns[':p' . $index++]  = 'CURRENCY_ID';
        }
        if ($this->isColumnModified(OrderTableMap::COL_CURRENCY_SYMBOL)) {
            $modifiedColumns[':p' . $index++]  = 'CURRENCY_SYMBOL';
        }
        if ($this->isColumnModified(OrderTableMap::COL_CURRENCY_RATE)) {
            $modifiedColumns[':p' . $index++]  = 'CURRENCY_RATE';
        }
        if ($this->isColumnModified(OrderTableMap::COL_CART_RULE_ID)) {
            $modifiedColumns[':p' . $index++]  = 'CART_RULE_ID';
        }
        if ($this->isColumnModified(OrderTableMap::COL_SESSION_ID)) {
            $modifiedColumns[':p' . $index++]  = 'SESSION_ID';
        }

        $sql = sprintf(
            'INSERT INTO order (%s) VALUES (%s)',
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
                    case 'PRICE':                        
                        $stmt->bindValue($identifier, $this->price, PDO::PARAM_STR);
                        break;
                    case 'DISPATCH_METHOD_PRICE':                        
                        $stmt->bindValue($identifier, $this->dispatch_method_price, PDO::PARAM_STR);
                        break;
                    case 'GLOBAL_PRICE':                        
                        $stmt->bindValue($identifier, $this->global_price, PDO::PARAM_STR);
                        break;
                    case 'ORDER_STATUS_ID':                        
                        $stmt->bindValue($identifier, $this->order_status_id, PDO::PARAM_INT);
                        break;
                    case 'DISPATCH_METHOD_NAME':                        
                        $stmt->bindValue($identifier, $this->dispatch_method_name, PDO::PARAM_STR);
                        break;
                    case 'PAYMENT_METHOD_NAME':                        
                        $stmt->bindValue($identifier, $this->payment_method_name, PDO::PARAM_STR);
                        break;
                    case 'GLOBAL_QTY':                        
                        $stmt->bindValue($identifier, $this->global_qty, PDO::PARAM_INT);
                        break;
                    case 'DISPATCH_METHOD_ID':                        
                        $stmt->bindValue($identifier, $this->dispatch_method_id, PDO::PARAM_INT);
                        break;
                    case 'PAYMENT_METHOD_ID':                        
                        $stmt->bindValue($identifier, $this->payment_method_id, PDO::PARAM_INT);
                        break;
                    case 'CLIENT_ID':                        
                        $stmt->bindValue($identifier, $this->client_id, PDO::PARAM_INT);
                        break;
                    case 'GLOBAL_PRICE_NETTO':                        
                        $stmt->bindValue($identifier, $this->global_price_netto, PDO::PARAM_STR);
                        break;
                    case 'ACTIVE_LINK':                        
                        $stmt->bindValue($identifier, $this->active_link, PDO::PARAM_STR);
                        break;
                    case 'COMMENT':                        
                        $stmt->bindValue($identifier, $this->comment, PDO::PARAM_STR);
                        break;
                    case 'SHOP_ID':                        
                        $stmt->bindValue($identifier, $this->shop_id, PDO::PARAM_INT);
                        break;
                    case 'PRICE_BEFORE_PROMOTION':                        
                        $stmt->bindValue($identifier, $this->price_before_promotion, PDO::PARAM_STR);
                        break;
                    case 'CURRENCY_ID':                        
                        $stmt->bindValue($identifier, $this->currency_id, PDO::PARAM_INT);
                        break;
                    case 'CURRENCY_SYMBOL':                        
                        $stmt->bindValue($identifier, $this->currency_symbol, PDO::PARAM_STR);
                        break;
                    case 'CURRENCY_RATE':                        
                        $stmt->bindValue($identifier, $this->currency_rate, PDO::PARAM_STR);
                        break;
                    case 'CART_RULE_ID':                        
                        $stmt->bindValue($identifier, $this->cart_rule_id, PDO::PARAM_INT);
                        break;
                    case 'SESSION_ID':                        
                        $stmt->bindValue($identifier, $this->session_id, PDO::PARAM_STR);
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
        $pos = OrderTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getPrice();
                break;
            case 2:
                return $this->getDispatchMethodPrice();
                break;
            case 3:
                return $this->getGlobalPrice();
                break;
            case 4:
                return $this->getOrderStatusId();
                break;
            case 5:
                return $this->getDispatchMethodName();
                break;
            case 6:
                return $this->getPaymentMethodName();
                break;
            case 7:
                return $this->getGlobalQty();
                break;
            case 8:
                return $this->getDispatchMethodId();
                break;
            case 9:
                return $this->getPaymentMethodId();
                break;
            case 10:
                return $this->getClientId();
                break;
            case 11:
                return $this->getGlobalPriceNetto();
                break;
            case 12:
                return $this->getActiveLink();
                break;
            case 13:
                return $this->getComment();
                break;
            case 14:
                return $this->getShopId();
                break;
            case 15:
                return $this->getPriceBeforePromotion();
                break;
            case 16:
                return $this->getCurrencyId();
                break;
            case 17:
                return $this->getCurrencySymbol();
                break;
            case 18:
                return $this->getCurrencyRate();
                break;
            case 19:
                return $this->getCartRuleId();
                break;
            case 20:
                return $this->getSessionId();
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
        if (isset($alreadyDumpedObjects['Order'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Order'][$this->getPrimaryKey()] = true;
        $keys = OrderTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getPrice(),
            $keys[2] => $this->getDispatchMethodPrice(),
            $keys[3] => $this->getGlobalPrice(),
            $keys[4] => $this->getOrderStatusId(),
            $keys[5] => $this->getDispatchMethodName(),
            $keys[6] => $this->getPaymentMethodName(),
            $keys[7] => $this->getGlobalQty(),
            $keys[8] => $this->getDispatchMethodId(),
            $keys[9] => $this->getPaymentMethodId(),
            $keys[10] => $this->getClientId(),
            $keys[11] => $this->getGlobalPriceNetto(),
            $keys[12] => $this->getActiveLink(),
            $keys[13] => $this->getComment(),
            $keys[14] => $this->getShopId(),
            $keys[15] => $this->getPriceBeforePromotion(),
            $keys[16] => $this->getCurrencyId(),
            $keys[17] => $this->getCurrencySymbol(),
            $keys[18] => $this->getCurrencyRate(),
            $keys[19] => $this->getCartRuleId(),
            $keys[20] => $this->getSessionId(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }
        
        if ($includeForeignObjects) {
            if (null !== $this->aShop) {
                $result['Shop'] = $this->aShop->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collOrderClientDatas) {
                $result['OrderClientDatas'] = $this->collOrderClientDatas->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collOrderClientDeliveryDatas) {
                $result['OrderClientDeliveryDatas'] = $this->collOrderClientDeliveryDatas->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collOrderHistories) {
                $result['OrderHistories'] = $this->collOrderHistories->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collOrderNotess) {
                $result['OrderNotess'] = $this->collOrderNotess->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collOrderProducts) {
                $result['OrderProducts'] = $this->collOrderProducts->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = OrderTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

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
                $this->setPrice($value);
                break;
            case 2:
                $this->setDispatchMethodPrice($value);
                break;
            case 3:
                $this->setGlobalPrice($value);
                break;
            case 4:
                $this->setOrderStatusId($value);
                break;
            case 5:
                $this->setDispatchMethodName($value);
                break;
            case 6:
                $this->setPaymentMethodName($value);
                break;
            case 7:
                $this->setGlobalQty($value);
                break;
            case 8:
                $this->setDispatchMethodId($value);
                break;
            case 9:
                $this->setPaymentMethodId($value);
                break;
            case 10:
                $this->setClientId($value);
                break;
            case 11:
                $this->setGlobalPriceNetto($value);
                break;
            case 12:
                $this->setActiveLink($value);
                break;
            case 13:
                $this->setComment($value);
                break;
            case 14:
                $this->setShopId($value);
                break;
            case 15:
                $this->setPriceBeforePromotion($value);
                break;
            case 16:
                $this->setCurrencyId($value);
                break;
            case 17:
                $this->setCurrencySymbol($value);
                break;
            case 18:
                $this->setCurrencyRate($value);
                break;
            case 19:
                $this->setCartRuleId($value);
                break;
            case 20:
                $this->setSessionId($value);
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
        $keys = OrderTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setPrice($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setDispatchMethodPrice($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setGlobalPrice($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setOrderStatusId($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setDispatchMethodName($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setPaymentMethodName($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setGlobalQty($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setDispatchMethodId($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setPaymentMethodId($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setClientId($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setGlobalPriceNetto($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setActiveLink($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setComment($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setShopId($arr[$keys[14]]);
        if (array_key_exists($keys[15], $arr)) $this->setPriceBeforePromotion($arr[$keys[15]]);
        if (array_key_exists($keys[16], $arr)) $this->setCurrencyId($arr[$keys[16]]);
        if (array_key_exists($keys[17], $arr)) $this->setCurrencySymbol($arr[$keys[17]]);
        if (array_key_exists($keys[18], $arr)) $this->setCurrencyRate($arr[$keys[18]]);
        if (array_key_exists($keys[19], $arr)) $this->setCartRuleId($arr[$keys[19]]);
        if (array_key_exists($keys[20], $arr)) $this->setSessionId($arr[$keys[20]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(OrderTableMap::DATABASE_NAME);

        if ($this->isColumnModified(OrderTableMap::COL_ID)) $criteria->add(OrderTableMap::COL_ID, $this->id);
        if ($this->isColumnModified(OrderTableMap::COL_PRICE)) $criteria->add(OrderTableMap::COL_PRICE, $this->price);
        if ($this->isColumnModified(OrderTableMap::COL_DISPATCH_METHOD_PRICE)) $criteria->add(OrderTableMap::COL_DISPATCH_METHOD_PRICE, $this->dispatch_method_price);
        if ($this->isColumnModified(OrderTableMap::COL_GLOBAL_PRICE)) $criteria->add(OrderTableMap::COL_GLOBAL_PRICE, $this->global_price);
        if ($this->isColumnModified(OrderTableMap::COL_ORDER_STATUS_ID)) $criteria->add(OrderTableMap::COL_ORDER_STATUS_ID, $this->order_status_id);
        if ($this->isColumnModified(OrderTableMap::COL_DISPATCH_METHOD_NAME)) $criteria->add(OrderTableMap::COL_DISPATCH_METHOD_NAME, $this->dispatch_method_name);
        if ($this->isColumnModified(OrderTableMap::COL_PAYMENT_METHOD_NAME)) $criteria->add(OrderTableMap::COL_PAYMENT_METHOD_NAME, $this->payment_method_name);
        if ($this->isColumnModified(OrderTableMap::COL_GLOBAL_QTY)) $criteria->add(OrderTableMap::COL_GLOBAL_QTY, $this->global_qty);
        if ($this->isColumnModified(OrderTableMap::COL_DISPATCH_METHOD_ID)) $criteria->add(OrderTableMap::COL_DISPATCH_METHOD_ID, $this->dispatch_method_id);
        if ($this->isColumnModified(OrderTableMap::COL_PAYMENT_METHOD_ID)) $criteria->add(OrderTableMap::COL_PAYMENT_METHOD_ID, $this->payment_method_id);
        if ($this->isColumnModified(OrderTableMap::COL_CLIENT_ID)) $criteria->add(OrderTableMap::COL_CLIENT_ID, $this->client_id);
        if ($this->isColumnModified(OrderTableMap::COL_GLOBAL_PRICE_NETTO)) $criteria->add(OrderTableMap::COL_GLOBAL_PRICE_NETTO, $this->global_price_netto);
        if ($this->isColumnModified(OrderTableMap::COL_ACTIVE_LINK)) $criteria->add(OrderTableMap::COL_ACTIVE_LINK, $this->active_link);
        if ($this->isColumnModified(OrderTableMap::COL_COMMENT)) $criteria->add(OrderTableMap::COL_COMMENT, $this->comment);
        if ($this->isColumnModified(OrderTableMap::COL_SHOP_ID)) $criteria->add(OrderTableMap::COL_SHOP_ID, $this->shop_id);
        if ($this->isColumnModified(OrderTableMap::COL_PRICE_BEFORE_PROMOTION)) $criteria->add(OrderTableMap::COL_PRICE_BEFORE_PROMOTION, $this->price_before_promotion);
        if ($this->isColumnModified(OrderTableMap::COL_CURRENCY_ID)) $criteria->add(OrderTableMap::COL_CURRENCY_ID, $this->currency_id);
        if ($this->isColumnModified(OrderTableMap::COL_CURRENCY_SYMBOL)) $criteria->add(OrderTableMap::COL_CURRENCY_SYMBOL, $this->currency_symbol);
        if ($this->isColumnModified(OrderTableMap::COL_CURRENCY_RATE)) $criteria->add(OrderTableMap::COL_CURRENCY_RATE, $this->currency_rate);
        if ($this->isColumnModified(OrderTableMap::COL_CART_RULE_ID)) $criteria->add(OrderTableMap::COL_CART_RULE_ID, $this->cart_rule_id);
        if ($this->isColumnModified(OrderTableMap::COL_SESSION_ID)) $criteria->add(OrderTableMap::COL_SESSION_ID, $this->session_id);

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
        $criteria = new Criteria(OrderTableMap::DATABASE_NAME);
        $criteria->add(OrderTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \Gekosale\Plugin\Order\Model\ORM\Order (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setPrice($this->getPrice());
        $copyObj->setDispatchMethodPrice($this->getDispatchMethodPrice());
        $copyObj->setGlobalPrice($this->getGlobalPrice());
        $copyObj->setOrderStatusId($this->getOrderStatusId());
        $copyObj->setDispatchMethodName($this->getDispatchMethodName());
        $copyObj->setPaymentMethodName($this->getPaymentMethodName());
        $copyObj->setGlobalQty($this->getGlobalQty());
        $copyObj->setDispatchMethodId($this->getDispatchMethodId());
        $copyObj->setPaymentMethodId($this->getPaymentMethodId());
        $copyObj->setClientId($this->getClientId());
        $copyObj->setGlobalPriceNetto($this->getGlobalPriceNetto());
        $copyObj->setActiveLink($this->getActiveLink());
        $copyObj->setComment($this->getComment());
        $copyObj->setShopId($this->getShopId());
        $copyObj->setPriceBeforePromotion($this->getPriceBeforePromotion());
        $copyObj->setCurrencyId($this->getCurrencyId());
        $copyObj->setCurrencySymbol($this->getCurrencySymbol());
        $copyObj->setCurrencyRate($this->getCurrencyRate());
        $copyObj->setCartRuleId($this->getCartRuleId());
        $copyObj->setSessionId($this->getSessionId());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getOrderClientDatas() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addOrderClientData($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getOrderClientDeliveryDatas() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addOrderClientDeliveryData($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getOrderHistories() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addOrderHistory($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getOrderNotess() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addOrderNotes($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getOrderProducts() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addOrderProduct($relObj->copy($deepCopy));
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
     * @return                 \Gekosale\Plugin\Order\Model\ORM\Order Clone of current object.
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
     * Declares an association between this object and a ChildShop object.
     *
     * @param                  ChildShop $v
     * @return                 \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     * @throws PropelException
     */
    public function setShop(ChildShop $v = null)
    {
        if ($v === null) {
            $this->setShopId(NULL);
        } else {
            $this->setShopId($v->getId());
        }

        $this->aShop = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildShop object, it will not be re-added.
        if ($v !== null) {
            $v->addOrder($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildShop object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildShop The associated ChildShop object.
     * @throws PropelException
     */
    public function getShop(ConnectionInterface $con = null)
    {
        if ($this->aShop === null && ($this->shop_id !== null)) {
            $this->aShop = ShopQuery::create()->findPk($this->shop_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aShop->addOrders($this);
             */
        }

        return $this->aShop;
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
        if ('OrderClientData' == $relationName) {
            return $this->initOrderClientDatas();
        }
        if ('OrderClientDeliveryData' == $relationName) {
            return $this->initOrderClientDeliveryDatas();
        }
        if ('OrderHistory' == $relationName) {
            return $this->initOrderHistories();
        }
        if ('OrderNotes' == $relationName) {
            return $this->initOrderNotess();
        }
        if ('OrderProduct' == $relationName) {
            return $this->initOrderProducts();
        }
    }

    /**
     * Clears out the collOrderClientDatas collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addOrderClientDatas()
     */
    public function clearOrderClientDatas()
    {
        $this->collOrderClientDatas = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collOrderClientDatas collection loaded partially.
     */
    public function resetPartialOrderClientDatas($v = true)
    {
        $this->collOrderClientDatasPartial = $v;
    }

    /**
     * Initializes the collOrderClientDatas collection.
     *
     * By default this just sets the collOrderClientDatas collection to an empty array (like clearcollOrderClientDatas());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initOrderClientDatas($overrideExisting = true)
    {
        if (null !== $this->collOrderClientDatas && !$overrideExisting) {
            return;
        }
        $this->collOrderClientDatas = new ObjectCollection();
        $this->collOrderClientDatas->setModel('\Gekosale\Plugin\Order\Model\ORM\OrderClientData');
    }

    /**
     * Gets an array of ChildOrderClientData objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildOrder is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildOrderClientData[] List of ChildOrderClientData objects
     * @throws PropelException
     */
    public function getOrderClientDatas($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collOrderClientDatasPartial && !$this->isNew();
        if (null === $this->collOrderClientDatas || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collOrderClientDatas) {
                // return empty collection
                $this->initOrderClientDatas();
            } else {
                $collOrderClientDatas = ChildOrderClientDataQuery::create(null, $criteria)
                    ->filterByOrder($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collOrderClientDatasPartial && count($collOrderClientDatas)) {
                        $this->initOrderClientDatas(false);

                        foreach ($collOrderClientDatas as $obj) {
                            if (false == $this->collOrderClientDatas->contains($obj)) {
                                $this->collOrderClientDatas->append($obj);
                            }
                        }

                        $this->collOrderClientDatasPartial = true;
                    }

                    reset($collOrderClientDatas);

                    return $collOrderClientDatas;
                }

                if ($partial && $this->collOrderClientDatas) {
                    foreach ($this->collOrderClientDatas as $obj) {
                        if ($obj->isNew()) {
                            $collOrderClientDatas[] = $obj;
                        }
                    }
                }

                $this->collOrderClientDatas = $collOrderClientDatas;
                $this->collOrderClientDatasPartial = false;
            }
        }

        return $this->collOrderClientDatas;
    }

    /**
     * Sets a collection of OrderClientData objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $orderClientDatas A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildOrder The current object (for fluent API support)
     */
    public function setOrderClientDatas(Collection $orderClientDatas, ConnectionInterface $con = null)
    {
        $orderClientDatasToDelete = $this->getOrderClientDatas(new Criteria(), $con)->diff($orderClientDatas);

        
        $this->orderClientDatasScheduledForDeletion = $orderClientDatasToDelete;

        foreach ($orderClientDatasToDelete as $orderClientDataRemoved) {
            $orderClientDataRemoved->setOrder(null);
        }

        $this->collOrderClientDatas = null;
        foreach ($orderClientDatas as $orderClientData) {
            $this->addOrderClientData($orderClientData);
        }

        $this->collOrderClientDatas = $orderClientDatas;
        $this->collOrderClientDatasPartial = false;

        return $this;
    }

    /**
     * Returns the number of related OrderClientData objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related OrderClientData objects.
     * @throws PropelException
     */
    public function countOrderClientDatas(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collOrderClientDatasPartial && !$this->isNew();
        if (null === $this->collOrderClientDatas || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collOrderClientDatas) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getOrderClientDatas());
            }

            $query = ChildOrderClientDataQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByOrder($this)
                ->count($con);
        }

        return count($this->collOrderClientDatas);
    }

    /**
     * Method called to associate a ChildOrderClientData object to this object
     * through the ChildOrderClientData foreign key attribute.
     *
     * @param    ChildOrderClientData $l ChildOrderClientData
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function addOrderClientData(ChildOrderClientData $l)
    {
        if ($this->collOrderClientDatas === null) {
            $this->initOrderClientDatas();
            $this->collOrderClientDatasPartial = true;
        }

        if (!in_array($l, $this->collOrderClientDatas->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddOrderClientData($l);
        }

        return $this;
    }

    /**
     * @param OrderClientData $orderClientData The orderClientData object to add.
     */
    protected function doAddOrderClientData($orderClientData)
    {
        $this->collOrderClientDatas[]= $orderClientData;
        $orderClientData->setOrder($this);
    }

    /**
     * @param  OrderClientData $orderClientData The orderClientData object to remove.
     * @return ChildOrder The current object (for fluent API support)
     */
    public function removeOrderClientData($orderClientData)
    {
        if ($this->getOrderClientDatas()->contains($orderClientData)) {
            $this->collOrderClientDatas->remove($this->collOrderClientDatas->search($orderClientData));
            if (null === $this->orderClientDatasScheduledForDeletion) {
                $this->orderClientDatasScheduledForDeletion = clone $this->collOrderClientDatas;
                $this->orderClientDatasScheduledForDeletion->clear();
            }
            $this->orderClientDatasScheduledForDeletion[]= clone $orderClientData;
            $orderClientData->setOrder(null);
        }

        return $this;
    }

    /**
     * Clears out the collOrderClientDeliveryDatas collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addOrderClientDeliveryDatas()
     */
    public function clearOrderClientDeliveryDatas()
    {
        $this->collOrderClientDeliveryDatas = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collOrderClientDeliveryDatas collection loaded partially.
     */
    public function resetPartialOrderClientDeliveryDatas($v = true)
    {
        $this->collOrderClientDeliveryDatasPartial = $v;
    }

    /**
     * Initializes the collOrderClientDeliveryDatas collection.
     *
     * By default this just sets the collOrderClientDeliveryDatas collection to an empty array (like clearcollOrderClientDeliveryDatas());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initOrderClientDeliveryDatas($overrideExisting = true)
    {
        if (null !== $this->collOrderClientDeliveryDatas && !$overrideExisting) {
            return;
        }
        $this->collOrderClientDeliveryDatas = new ObjectCollection();
        $this->collOrderClientDeliveryDatas->setModel('\Gekosale\Plugin\Order\Model\ORM\OrderClientDeliveryData');
    }

    /**
     * Gets an array of ChildOrderClientDeliveryData objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildOrder is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildOrderClientDeliveryData[] List of ChildOrderClientDeliveryData objects
     * @throws PropelException
     */
    public function getOrderClientDeliveryDatas($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collOrderClientDeliveryDatasPartial && !$this->isNew();
        if (null === $this->collOrderClientDeliveryDatas || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collOrderClientDeliveryDatas) {
                // return empty collection
                $this->initOrderClientDeliveryDatas();
            } else {
                $collOrderClientDeliveryDatas = ChildOrderClientDeliveryDataQuery::create(null, $criteria)
                    ->filterByOrder($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collOrderClientDeliveryDatasPartial && count($collOrderClientDeliveryDatas)) {
                        $this->initOrderClientDeliveryDatas(false);

                        foreach ($collOrderClientDeliveryDatas as $obj) {
                            if (false == $this->collOrderClientDeliveryDatas->contains($obj)) {
                                $this->collOrderClientDeliveryDatas->append($obj);
                            }
                        }

                        $this->collOrderClientDeliveryDatasPartial = true;
                    }

                    reset($collOrderClientDeliveryDatas);

                    return $collOrderClientDeliveryDatas;
                }

                if ($partial && $this->collOrderClientDeliveryDatas) {
                    foreach ($this->collOrderClientDeliveryDatas as $obj) {
                        if ($obj->isNew()) {
                            $collOrderClientDeliveryDatas[] = $obj;
                        }
                    }
                }

                $this->collOrderClientDeliveryDatas = $collOrderClientDeliveryDatas;
                $this->collOrderClientDeliveryDatasPartial = false;
            }
        }

        return $this->collOrderClientDeliveryDatas;
    }

    /**
     * Sets a collection of OrderClientDeliveryData objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $orderClientDeliveryDatas A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildOrder The current object (for fluent API support)
     */
    public function setOrderClientDeliveryDatas(Collection $orderClientDeliveryDatas, ConnectionInterface $con = null)
    {
        $orderClientDeliveryDatasToDelete = $this->getOrderClientDeliveryDatas(new Criteria(), $con)->diff($orderClientDeliveryDatas);

        
        $this->orderClientDeliveryDatasScheduledForDeletion = $orderClientDeliveryDatasToDelete;

        foreach ($orderClientDeliveryDatasToDelete as $orderClientDeliveryDataRemoved) {
            $orderClientDeliveryDataRemoved->setOrder(null);
        }

        $this->collOrderClientDeliveryDatas = null;
        foreach ($orderClientDeliveryDatas as $orderClientDeliveryData) {
            $this->addOrderClientDeliveryData($orderClientDeliveryData);
        }

        $this->collOrderClientDeliveryDatas = $orderClientDeliveryDatas;
        $this->collOrderClientDeliveryDatasPartial = false;

        return $this;
    }

    /**
     * Returns the number of related OrderClientDeliveryData objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related OrderClientDeliveryData objects.
     * @throws PropelException
     */
    public function countOrderClientDeliveryDatas(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collOrderClientDeliveryDatasPartial && !$this->isNew();
        if (null === $this->collOrderClientDeliveryDatas || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collOrderClientDeliveryDatas) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getOrderClientDeliveryDatas());
            }

            $query = ChildOrderClientDeliveryDataQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByOrder($this)
                ->count($con);
        }

        return count($this->collOrderClientDeliveryDatas);
    }

    /**
     * Method called to associate a ChildOrderClientDeliveryData object to this object
     * through the ChildOrderClientDeliveryData foreign key attribute.
     *
     * @param    ChildOrderClientDeliveryData $l ChildOrderClientDeliveryData
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function addOrderClientDeliveryData(ChildOrderClientDeliveryData $l)
    {
        if ($this->collOrderClientDeliveryDatas === null) {
            $this->initOrderClientDeliveryDatas();
            $this->collOrderClientDeliveryDatasPartial = true;
        }

        if (!in_array($l, $this->collOrderClientDeliveryDatas->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddOrderClientDeliveryData($l);
        }

        return $this;
    }

    /**
     * @param OrderClientDeliveryData $orderClientDeliveryData The orderClientDeliveryData object to add.
     */
    protected function doAddOrderClientDeliveryData($orderClientDeliveryData)
    {
        $this->collOrderClientDeliveryDatas[]= $orderClientDeliveryData;
        $orderClientDeliveryData->setOrder($this);
    }

    /**
     * @param  OrderClientDeliveryData $orderClientDeliveryData The orderClientDeliveryData object to remove.
     * @return ChildOrder The current object (for fluent API support)
     */
    public function removeOrderClientDeliveryData($orderClientDeliveryData)
    {
        if ($this->getOrderClientDeliveryDatas()->contains($orderClientDeliveryData)) {
            $this->collOrderClientDeliveryDatas->remove($this->collOrderClientDeliveryDatas->search($orderClientDeliveryData));
            if (null === $this->orderClientDeliveryDatasScheduledForDeletion) {
                $this->orderClientDeliveryDatasScheduledForDeletion = clone $this->collOrderClientDeliveryDatas;
                $this->orderClientDeliveryDatasScheduledForDeletion->clear();
            }
            $this->orderClientDeliveryDatasScheduledForDeletion[]= clone $orderClientDeliveryData;
            $orderClientDeliveryData->setOrder(null);
        }

        return $this;
    }

    /**
     * Clears out the collOrderHistories collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addOrderHistories()
     */
    public function clearOrderHistories()
    {
        $this->collOrderHistories = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collOrderHistories collection loaded partially.
     */
    public function resetPartialOrderHistories($v = true)
    {
        $this->collOrderHistoriesPartial = $v;
    }

    /**
     * Initializes the collOrderHistories collection.
     *
     * By default this just sets the collOrderHistories collection to an empty array (like clearcollOrderHistories());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initOrderHistories($overrideExisting = true)
    {
        if (null !== $this->collOrderHistories && !$overrideExisting) {
            return;
        }
        $this->collOrderHistories = new ObjectCollection();
        $this->collOrderHistories->setModel('\Gekosale\Plugin\Order\Model\ORM\OrderHistory');
    }

    /**
     * Gets an array of ChildOrderHistory objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildOrder is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildOrderHistory[] List of ChildOrderHistory objects
     * @throws PropelException
     */
    public function getOrderHistories($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collOrderHistoriesPartial && !$this->isNew();
        if (null === $this->collOrderHistories || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collOrderHistories) {
                // return empty collection
                $this->initOrderHistories();
            } else {
                $collOrderHistories = ChildOrderHistoryQuery::create(null, $criteria)
                    ->filterByOrder($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collOrderHistoriesPartial && count($collOrderHistories)) {
                        $this->initOrderHistories(false);

                        foreach ($collOrderHistories as $obj) {
                            if (false == $this->collOrderHistories->contains($obj)) {
                                $this->collOrderHistories->append($obj);
                            }
                        }

                        $this->collOrderHistoriesPartial = true;
                    }

                    reset($collOrderHistories);

                    return $collOrderHistories;
                }

                if ($partial && $this->collOrderHistories) {
                    foreach ($this->collOrderHistories as $obj) {
                        if ($obj->isNew()) {
                            $collOrderHistories[] = $obj;
                        }
                    }
                }

                $this->collOrderHistories = $collOrderHistories;
                $this->collOrderHistoriesPartial = false;
            }
        }

        return $this->collOrderHistories;
    }

    /**
     * Sets a collection of OrderHistory objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $orderHistories A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildOrder The current object (for fluent API support)
     */
    public function setOrderHistories(Collection $orderHistories, ConnectionInterface $con = null)
    {
        $orderHistoriesToDelete = $this->getOrderHistories(new Criteria(), $con)->diff($orderHistories);

        
        $this->orderHistoriesScheduledForDeletion = $orderHistoriesToDelete;

        foreach ($orderHistoriesToDelete as $orderHistoryRemoved) {
            $orderHistoryRemoved->setOrder(null);
        }

        $this->collOrderHistories = null;
        foreach ($orderHistories as $orderHistory) {
            $this->addOrderHistory($orderHistory);
        }

        $this->collOrderHistories = $orderHistories;
        $this->collOrderHistoriesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related OrderHistory objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related OrderHistory objects.
     * @throws PropelException
     */
    public function countOrderHistories(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collOrderHistoriesPartial && !$this->isNew();
        if (null === $this->collOrderHistories || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collOrderHistories) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getOrderHistories());
            }

            $query = ChildOrderHistoryQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByOrder($this)
                ->count($con);
        }

        return count($this->collOrderHistories);
    }

    /**
     * Method called to associate a ChildOrderHistory object to this object
     * through the ChildOrderHistory foreign key attribute.
     *
     * @param    ChildOrderHistory $l ChildOrderHistory
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function addOrderHistory(ChildOrderHistory $l)
    {
        if ($this->collOrderHistories === null) {
            $this->initOrderHistories();
            $this->collOrderHistoriesPartial = true;
        }

        if (!in_array($l, $this->collOrderHistories->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddOrderHistory($l);
        }

        return $this;
    }

    /**
     * @param OrderHistory $orderHistory The orderHistory object to add.
     */
    protected function doAddOrderHistory($orderHistory)
    {
        $this->collOrderHistories[]= $orderHistory;
        $orderHistory->setOrder($this);
    }

    /**
     * @param  OrderHistory $orderHistory The orderHistory object to remove.
     * @return ChildOrder The current object (for fluent API support)
     */
    public function removeOrderHistory($orderHistory)
    {
        if ($this->getOrderHistories()->contains($orderHistory)) {
            $this->collOrderHistories->remove($this->collOrderHistories->search($orderHistory));
            if (null === $this->orderHistoriesScheduledForDeletion) {
                $this->orderHistoriesScheduledForDeletion = clone $this->collOrderHistories;
                $this->orderHistoriesScheduledForDeletion->clear();
            }
            $this->orderHistoriesScheduledForDeletion[]= clone $orderHistory;
            $orderHistory->setOrder(null);
        }

        return $this;
    }

    /**
     * Clears out the collOrderNotess collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addOrderNotess()
     */
    public function clearOrderNotess()
    {
        $this->collOrderNotess = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collOrderNotess collection loaded partially.
     */
    public function resetPartialOrderNotess($v = true)
    {
        $this->collOrderNotessPartial = $v;
    }

    /**
     * Initializes the collOrderNotess collection.
     *
     * By default this just sets the collOrderNotess collection to an empty array (like clearcollOrderNotess());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initOrderNotess($overrideExisting = true)
    {
        if (null !== $this->collOrderNotess && !$overrideExisting) {
            return;
        }
        $this->collOrderNotess = new ObjectCollection();
        $this->collOrderNotess->setModel('\Gekosale\Plugin\Order\Model\ORM\OrderNotes');
    }

    /**
     * Gets an array of ChildOrderNotes objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildOrder is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildOrderNotes[] List of ChildOrderNotes objects
     * @throws PropelException
     */
    public function getOrderNotess($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collOrderNotessPartial && !$this->isNew();
        if (null === $this->collOrderNotess || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collOrderNotess) {
                // return empty collection
                $this->initOrderNotess();
            } else {
                $collOrderNotess = ChildOrderNotesQuery::create(null, $criteria)
                    ->filterByOrder($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collOrderNotessPartial && count($collOrderNotess)) {
                        $this->initOrderNotess(false);

                        foreach ($collOrderNotess as $obj) {
                            if (false == $this->collOrderNotess->contains($obj)) {
                                $this->collOrderNotess->append($obj);
                            }
                        }

                        $this->collOrderNotessPartial = true;
                    }

                    reset($collOrderNotess);

                    return $collOrderNotess;
                }

                if ($partial && $this->collOrderNotess) {
                    foreach ($this->collOrderNotess as $obj) {
                        if ($obj->isNew()) {
                            $collOrderNotess[] = $obj;
                        }
                    }
                }

                $this->collOrderNotess = $collOrderNotess;
                $this->collOrderNotessPartial = false;
            }
        }

        return $this->collOrderNotess;
    }

    /**
     * Sets a collection of OrderNotes objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $orderNotess A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildOrder The current object (for fluent API support)
     */
    public function setOrderNotess(Collection $orderNotess, ConnectionInterface $con = null)
    {
        $orderNotessToDelete = $this->getOrderNotess(new Criteria(), $con)->diff($orderNotess);

        
        $this->orderNotessScheduledForDeletion = $orderNotessToDelete;

        foreach ($orderNotessToDelete as $orderNotesRemoved) {
            $orderNotesRemoved->setOrder(null);
        }

        $this->collOrderNotess = null;
        foreach ($orderNotess as $orderNotes) {
            $this->addOrderNotes($orderNotes);
        }

        $this->collOrderNotess = $orderNotess;
        $this->collOrderNotessPartial = false;

        return $this;
    }

    /**
     * Returns the number of related OrderNotes objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related OrderNotes objects.
     * @throws PropelException
     */
    public function countOrderNotess(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collOrderNotessPartial && !$this->isNew();
        if (null === $this->collOrderNotess || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collOrderNotess) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getOrderNotess());
            }

            $query = ChildOrderNotesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByOrder($this)
                ->count($con);
        }

        return count($this->collOrderNotess);
    }

    /**
     * Method called to associate a ChildOrderNotes object to this object
     * through the ChildOrderNotes foreign key attribute.
     *
     * @param    ChildOrderNotes $l ChildOrderNotes
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
     */
    public function addOrderNotes(ChildOrderNotes $l)
    {
        if ($this->collOrderNotess === null) {
            $this->initOrderNotess();
            $this->collOrderNotessPartial = true;
        }

        if (!in_array($l, $this->collOrderNotess->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddOrderNotes($l);
        }

        return $this;
    }

    /**
     * @param OrderNotes $orderNotes The orderNotes object to add.
     */
    protected function doAddOrderNotes($orderNotes)
    {
        $this->collOrderNotess[]= $orderNotes;
        $orderNotes->setOrder($this);
    }

    /**
     * @param  OrderNotes $orderNotes The orderNotes object to remove.
     * @return ChildOrder The current object (for fluent API support)
     */
    public function removeOrderNotes($orderNotes)
    {
        if ($this->getOrderNotess()->contains($orderNotes)) {
            $this->collOrderNotess->remove($this->collOrderNotess->search($orderNotes));
            if (null === $this->orderNotessScheduledForDeletion) {
                $this->orderNotessScheduledForDeletion = clone $this->collOrderNotess;
                $this->orderNotessScheduledForDeletion->clear();
            }
            $this->orderNotessScheduledForDeletion[]= clone $orderNotes;
            $orderNotes->setOrder(null);
        }

        return $this;
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
     * If this ChildOrder is new, it will return
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
                $collOrderProducts = ChildOrderProductQuery::create(null, $criteria)
                    ->filterByOrder($this)
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
     * @return   ChildOrder The current object (for fluent API support)
     */
    public function setOrderProducts(Collection $orderProducts, ConnectionInterface $con = null)
    {
        $orderProductsToDelete = $this->getOrderProducts(new Criteria(), $con)->diff($orderProducts);

        
        $this->orderProductsScheduledForDeletion = $orderProductsToDelete;

        foreach ($orderProductsToDelete as $orderProductRemoved) {
            $orderProductRemoved->setOrder(null);
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

            $query = ChildOrderProductQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByOrder($this)
                ->count($con);
        }

        return count($this->collOrderProducts);
    }

    /**
     * Method called to associate a ChildOrderProduct object to this object
     * through the ChildOrderProduct foreign key attribute.
     *
     * @param    ChildOrderProduct $l ChildOrderProduct
     * @return   \Gekosale\Plugin\Order\Model\ORM\Order The current object (for fluent API support)
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
        $orderProduct->setOrder($this);
    }

    /**
     * @param  OrderProduct $orderProduct The orderProduct object to remove.
     * @return ChildOrder The current object (for fluent API support)
     */
    public function removeOrderProduct($orderProduct)
    {
        if ($this->getOrderProducts()->contains($orderProduct)) {
            $this->collOrderProducts->remove($this->collOrderProducts->search($orderProduct));
            if (null === $this->orderProductsScheduledForDeletion) {
                $this->orderProductsScheduledForDeletion = clone $this->collOrderProducts;
                $this->orderProductsScheduledForDeletion->clear();
            }
            $this->orderProductsScheduledForDeletion[]= clone $orderProduct;
            $orderProduct->setOrder(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Order is new, it will return
     * an empty collection; or if this Order has previously
     * been saved, it will retrieve related OrderProducts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Order.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildOrderProduct[] List of ChildOrderProduct objects
     */
    public function getOrderProductsJoinProduct($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildOrderProductQuery::create(null, $criteria);
        $query->joinWith('Product', $joinBehavior);

        return $this->getOrderProducts($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->price = null;
        $this->dispatch_method_price = null;
        $this->global_price = null;
        $this->order_status_id = null;
        $this->dispatch_method_name = null;
        $this->payment_method_name = null;
        $this->global_qty = null;
        $this->dispatch_method_id = null;
        $this->payment_method_id = null;
        $this->client_id = null;
        $this->global_price_netto = null;
        $this->active_link = null;
        $this->comment = null;
        $this->shop_id = null;
        $this->price_before_promotion = null;
        $this->currency_id = null;
        $this->currency_symbol = null;
        $this->currency_rate = null;
        $this->cart_rule_id = null;
        $this->session_id = null;
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
            if ($this->collOrderClientDatas) {
                foreach ($this->collOrderClientDatas as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collOrderClientDeliveryDatas) {
                foreach ($this->collOrderClientDeliveryDatas as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collOrderHistories) {
                foreach ($this->collOrderHistories as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collOrderNotess) {
                foreach ($this->collOrderNotess as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collOrderProducts) {
                foreach ($this->collOrderProducts as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collOrderClientDatas = null;
        $this->collOrderClientDeliveryDatas = null;
        $this->collOrderHistories = null;
        $this->collOrderNotess = null;
        $this->collOrderProducts = null;
        $this->aShop = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(OrderTableMap::DEFAULT_STRING_FORMAT);
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
