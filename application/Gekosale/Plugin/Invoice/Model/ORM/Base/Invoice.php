<?php

namespace Gekosale\Plugin\Invoice\Model\ORM\Base;

use \DateTime;
use \Exception;
use \PDO;
use Gekosale\Plugin\Invoice\Model\ORM\InvoiceQuery as ChildInvoiceQuery;
use Gekosale\Plugin\Invoice\Model\ORM\Map\InvoiceTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;

abstract class Invoice implements ActiveRecordInterface 
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Gekosale\\Plugin\\Invoice\\Model\\ORM\\Map\\InvoiceTableMap';


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
     * The value for the symbol field.
     * @var        string
     */
    protected $symbol;

    /**
     * The value for the invoice_date field.
     * @var        string
     */
    protected $invoice_date;

    /**
     * The value for the sales_date field.
     * @var        string
     */
    protected $sales_date;

    /**
     * The value for the payment_due_date field.
     * @var        string
     */
    protected $payment_due_date;

    /**
     * The value for the sales_person field.
     * @var        string
     */
    protected $sales_person;

    /**
     * The value for the invoice_type field.
     * @var        int
     */
    protected $invoice_type;

    /**
     * The value for the comment field.
     * @var        string
     */
    protected $comment;

    /**
     * The value for the content_original field.
     * @var        resource
     */
    protected $content_original;

    /**
     * The value for the content_copy field.
     * @var        resource
     */
    protected $content_copy;

    /**
     * The value for the order_id field.
     * @var        int
     */
    protected $order_id;

    /**
     * The value for the total_payed field.
     * Note: this column has a database default value of: '0.00'
     * @var        string
     */
    protected $total_payed;

    /**
     * The value for the shop_id field.
     * @var        int
     */
    protected $shop_id;

    /**
     * The value for the external_id field.
     * @var        int
     */
    protected $external_id;

    /**
     * The value for the content_type field.
     * Note: this column has a database default value of: 'html'
     * @var        string
     */
    protected $content_type;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->total_payed = '0.00';
        $this->content_type = 'html';
    }

    /**
     * Initializes internal state of Gekosale\Plugin\Invoice\Model\ORM\Base\Invoice object.
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
     * Compares this with another <code>Invoice</code> instance.  If
     * <code>obj</code> is an instance of <code>Invoice</code>, delegates to
     * <code>equals(Invoice)</code>.  Otherwise, returns <code>false</code>.
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
     * @return Invoice The current object, for fluid interface
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
     * @return Invoice The current object, for fluid interface
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
     * Get the [symbol] column value.
     * 
     * @return   string
     */
    public function getSymbol()
    {

        return $this->symbol;
    }

    /**
     * Get the [optionally formatted] temporal [invoice_date] column value.
     * 
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getInvoiceDate($format = NULL)
    {
        if ($format === null) {
            return $this->invoice_date;
        } else {
            return $this->invoice_date instanceof \DateTime ? $this->invoice_date->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [sales_date] column value.
     * 
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getSalesDate($format = NULL)
    {
        if ($format === null) {
            return $this->sales_date;
        } else {
            return $this->sales_date instanceof \DateTime ? $this->sales_date->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [payment_due_date] column value.
     * 
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getPaymentDueDate($format = NULL)
    {
        if ($format === null) {
            return $this->payment_due_date;
        } else {
            return $this->payment_due_date instanceof \DateTime ? $this->payment_due_date->format($format) : null;
        }
    }

    /**
     * Get the [sales_person] column value.
     * 
     * @return   string
     */
    public function getSalesPerson()
    {

        return $this->sales_person;
    }

    /**
     * Get the [invoice_type] column value.
     * 
     * @return   int
     */
    public function getInvoiceType()
    {

        return $this->invoice_type;
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
     * Get the [content_original] column value.
     * 
     * @return   resource
     */
    public function getContentOriginal()
    {

        return $this->content_original;
    }

    /**
     * Get the [content_copy] column value.
     * 
     * @return   resource
     */
    public function getContentCopy()
    {

        return $this->content_copy;
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
     * Get the [total_payed] column value.
     * 
     * @return   string
     */
    public function getTotalPayed()
    {

        return $this->total_payed;
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
     * Get the [external_id] column value.
     * 
     * @return   int
     */
    public function getExternalId()
    {

        return $this->external_id;
    }

    /**
     * Get the [content_type] column value.
     * 
     * @return   string
     */
    public function getContentType()
    {

        return $this->content_type;
    }

    /**
     * Set the value of [id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Invoice\Model\ORM\Invoice The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[InvoiceTableMap::COL_ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [symbol] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Invoice\Model\ORM\Invoice The current object (for fluent API support)
     */
    public function setSymbol($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->symbol !== $v) {
            $this->symbol = $v;
            $this->modifiedColumns[InvoiceTableMap::COL_SYMBOL] = true;
        }


        return $this;
    } // setSymbol()

    /**
     * Sets the value of [invoice_date] column to a normalized version of the date/time value specified.
     * 
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Gekosale\Plugin\Invoice\Model\ORM\Invoice The current object (for fluent API support)
     */
    public function setInvoiceDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->invoice_date !== null || $dt !== null) {
            if ($dt !== $this->invoice_date) {
                $this->invoice_date = $dt;
                $this->modifiedColumns[InvoiceTableMap::COL_INVOICE_DATE] = true;
            }
        } // if either are not null


        return $this;
    } // setInvoiceDate()

    /**
     * Sets the value of [sales_date] column to a normalized version of the date/time value specified.
     * 
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Gekosale\Plugin\Invoice\Model\ORM\Invoice The current object (for fluent API support)
     */
    public function setSalesDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->sales_date !== null || $dt !== null) {
            if ($dt !== $this->sales_date) {
                $this->sales_date = $dt;
                $this->modifiedColumns[InvoiceTableMap::COL_SALES_DATE] = true;
            }
        } // if either are not null


        return $this;
    } // setSalesDate()

    /**
     * Sets the value of [payment_due_date] column to a normalized version of the date/time value specified.
     * 
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Gekosale\Plugin\Invoice\Model\ORM\Invoice The current object (for fluent API support)
     */
    public function setPaymentDueDate($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->payment_due_date !== null || $dt !== null) {
            if ($dt !== $this->payment_due_date) {
                $this->payment_due_date = $dt;
                $this->modifiedColumns[InvoiceTableMap::COL_PAYMENT_DUE_DATE] = true;
            }
        } // if either are not null


        return $this;
    } // setPaymentDueDate()

    /**
     * Set the value of [sales_person] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Invoice\Model\ORM\Invoice The current object (for fluent API support)
     */
    public function setSalesPerson($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->sales_person !== $v) {
            $this->sales_person = $v;
            $this->modifiedColumns[InvoiceTableMap::COL_SALES_PERSON] = true;
        }


        return $this;
    } // setSalesPerson()

    /**
     * Set the value of [invoice_type] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Invoice\Model\ORM\Invoice The current object (for fluent API support)
     */
    public function setInvoiceType($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->invoice_type !== $v) {
            $this->invoice_type = $v;
            $this->modifiedColumns[InvoiceTableMap::COL_INVOICE_TYPE] = true;
        }


        return $this;
    } // setInvoiceType()

    /**
     * Set the value of [comment] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Invoice\Model\ORM\Invoice The current object (for fluent API support)
     */
    public function setComment($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->comment !== $v) {
            $this->comment = $v;
            $this->modifiedColumns[InvoiceTableMap::COL_COMMENT] = true;
        }


        return $this;
    } // setComment()

    /**
     * Set the value of [content_original] column.
     * 
     * @param      resource $v new value
     * @return   \Gekosale\Plugin\Invoice\Model\ORM\Invoice The current object (for fluent API support)
     */
    public function setContentOriginal($v)
    {
        // Because BLOB columns are streams in PDO we have to assume that they are
        // always modified when a new value is passed in.  For example, the contents
        // of the stream itself may have changed externally.
        if (!is_resource($v) && $v !== null) {
            $this->content_original = fopen('php://memory', 'r+');
            fwrite($this->content_original, $v);
            rewind($this->content_original);
        } else { // it's already a stream
            $this->content_original = $v;
        }
        $this->modifiedColumns[InvoiceTableMap::COL_CONTENT_ORIGINAL] = true;


        return $this;
    } // setContentOriginal()

    /**
     * Set the value of [content_copy] column.
     * 
     * @param      resource $v new value
     * @return   \Gekosale\Plugin\Invoice\Model\ORM\Invoice The current object (for fluent API support)
     */
    public function setContentCopy($v)
    {
        // Because BLOB columns are streams in PDO we have to assume that they are
        // always modified when a new value is passed in.  For example, the contents
        // of the stream itself may have changed externally.
        if (!is_resource($v) && $v !== null) {
            $this->content_copy = fopen('php://memory', 'r+');
            fwrite($this->content_copy, $v);
            rewind($this->content_copy);
        } else { // it's already a stream
            $this->content_copy = $v;
        }
        $this->modifiedColumns[InvoiceTableMap::COL_CONTENT_COPY] = true;


        return $this;
    } // setContentCopy()

    /**
     * Set the value of [order_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Invoice\Model\ORM\Invoice The current object (for fluent API support)
     */
    public function setOrderId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->order_id !== $v) {
            $this->order_id = $v;
            $this->modifiedColumns[InvoiceTableMap::COL_ORDER_ID] = true;
        }


        return $this;
    } // setOrderId()

    /**
     * Set the value of [total_payed] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Invoice\Model\ORM\Invoice The current object (for fluent API support)
     */
    public function setTotalPayed($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->total_payed !== $v) {
            $this->total_payed = $v;
            $this->modifiedColumns[InvoiceTableMap::COL_TOTAL_PAYED] = true;
        }


        return $this;
    } // setTotalPayed()

    /**
     * Set the value of [shop_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Invoice\Model\ORM\Invoice The current object (for fluent API support)
     */
    public function setShopId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->shop_id !== $v) {
            $this->shop_id = $v;
            $this->modifiedColumns[InvoiceTableMap::COL_SHOP_ID] = true;
        }


        return $this;
    } // setShopId()

    /**
     * Set the value of [external_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Invoice\Model\ORM\Invoice The current object (for fluent API support)
     */
    public function setExternalId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->external_id !== $v) {
            $this->external_id = $v;
            $this->modifiedColumns[InvoiceTableMap::COL_EXTERNAL_ID] = true;
        }


        return $this;
    } // setExternalId()

    /**
     * Set the value of [content_type] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Invoice\Model\ORM\Invoice The current object (for fluent API support)
     */
    public function setContentType($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->content_type !== $v) {
            $this->content_type = $v;
            $this->modifiedColumns[InvoiceTableMap::COL_CONTENT_TYPE] = true;
        }


        return $this;
    } // setContentType()

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
            if ($this->total_payed !== '0.00') {
                return false;
            }

            if ($this->content_type !== 'html') {
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


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : InvoiceTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : InvoiceTableMap::translateFieldName('Symbol', TableMap::TYPE_PHPNAME, $indexType)];
            $this->symbol = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : InvoiceTableMap::translateFieldName('InvoiceDate', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00') {
                $col = null;
            }
            $this->invoice_date = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : InvoiceTableMap::translateFieldName('SalesDate', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00') {
                $col = null;
            }
            $this->sales_date = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : InvoiceTableMap::translateFieldName('PaymentDueDate', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00') {
                $col = null;
            }
            $this->payment_due_date = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : InvoiceTableMap::translateFieldName('SalesPerson', TableMap::TYPE_PHPNAME, $indexType)];
            $this->sales_person = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : InvoiceTableMap::translateFieldName('InvoiceType', TableMap::TYPE_PHPNAME, $indexType)];
            $this->invoice_type = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : InvoiceTableMap::translateFieldName('Comment', TableMap::TYPE_PHPNAME, $indexType)];
            $this->comment = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : InvoiceTableMap::translateFieldName('ContentOriginal', TableMap::TYPE_PHPNAME, $indexType)];
            if (null !== $col) {
                $this->content_original = fopen('php://memory', 'r+');
                fwrite($this->content_original, $col);
                rewind($this->content_original);
            } else {
                $this->content_original = null;
            }

            $col = $row[TableMap::TYPE_NUM == $indexType ? 9 + $startcol : InvoiceTableMap::translateFieldName('ContentCopy', TableMap::TYPE_PHPNAME, $indexType)];
            if (null !== $col) {
                $this->content_copy = fopen('php://memory', 'r+');
                fwrite($this->content_copy, $col);
                rewind($this->content_copy);
            } else {
                $this->content_copy = null;
            }

            $col = $row[TableMap::TYPE_NUM == $indexType ? 10 + $startcol : InvoiceTableMap::translateFieldName('OrderId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->order_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 11 + $startcol : InvoiceTableMap::translateFieldName('TotalPayed', TableMap::TYPE_PHPNAME, $indexType)];
            $this->total_payed = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 12 + $startcol : InvoiceTableMap::translateFieldName('ShopId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->shop_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 13 + $startcol : InvoiceTableMap::translateFieldName('ExternalId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->external_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 14 + $startcol : InvoiceTableMap::translateFieldName('ContentType', TableMap::TYPE_PHPNAME, $indexType)];
            $this->content_type = (null !== $col) ? (string) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 15; // 15 = InvoiceTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Gekosale\Plugin\Invoice\Model\ORM\Invoice object", 0, $e);
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
            $con = Propel::getServiceContainer()->getReadConnection(InvoiceTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildInvoiceQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Invoice::setDeleted()
     * @see Invoice::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(InvoiceTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildInvoiceQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(InvoiceTableMap::DATABASE_NAME);
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
                InvoiceTableMap::addInstanceToPool($this);
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

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                // Rewind the content_original LOB column, since PDO does not rewind after inserting value.
                if ($this->content_original !== null && is_resource($this->content_original)) {
                    rewind($this->content_original);
                }

                // Rewind the content_copy LOB column, since PDO does not rewind after inserting value.
                if ($this->content_copy !== null && is_resource($this->content_copy)) {
                    rewind($this->content_copy);
                }

                $this->resetModified();
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

        $this->modifiedColumns[InvoiceTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . InvoiceTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(InvoiceTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(InvoiceTableMap::COL_SYMBOL)) {
            $modifiedColumns[':p' . $index++]  = 'SYMBOL';
        }
        if ($this->isColumnModified(InvoiceTableMap::COL_INVOICE_DATE)) {
            $modifiedColumns[':p' . $index++]  = 'INVOICE_DATE';
        }
        if ($this->isColumnModified(InvoiceTableMap::COL_SALES_DATE)) {
            $modifiedColumns[':p' . $index++]  = 'SALES_DATE';
        }
        if ($this->isColumnModified(InvoiceTableMap::COL_PAYMENT_DUE_DATE)) {
            $modifiedColumns[':p' . $index++]  = 'PAYMENT_DUE_DATE';
        }
        if ($this->isColumnModified(InvoiceTableMap::COL_SALES_PERSON)) {
            $modifiedColumns[':p' . $index++]  = 'SALES_PERSON';
        }
        if ($this->isColumnModified(InvoiceTableMap::COL_INVOICE_TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'INVOICE_TYPE';
        }
        if ($this->isColumnModified(InvoiceTableMap::COL_COMMENT)) {
            $modifiedColumns[':p' . $index++]  = 'COMMENT';
        }
        if ($this->isColumnModified(InvoiceTableMap::COL_CONTENT_ORIGINAL)) {
            $modifiedColumns[':p' . $index++]  = 'CONTENT_ORIGINAL';
        }
        if ($this->isColumnModified(InvoiceTableMap::COL_CONTENT_COPY)) {
            $modifiedColumns[':p' . $index++]  = 'CONTENT_COPY';
        }
        if ($this->isColumnModified(InvoiceTableMap::COL_ORDER_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ORDER_ID';
        }
        if ($this->isColumnModified(InvoiceTableMap::COL_TOTAL_PAYED)) {
            $modifiedColumns[':p' . $index++]  = 'TOTAL_PAYED';
        }
        if ($this->isColumnModified(InvoiceTableMap::COL_SHOP_ID)) {
            $modifiedColumns[':p' . $index++]  = 'SHOP_ID';
        }
        if ($this->isColumnModified(InvoiceTableMap::COL_EXTERNAL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'EXTERNAL_ID';
        }
        if ($this->isColumnModified(InvoiceTableMap::COL_CONTENT_TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'CONTENT_TYPE';
        }

        $sql = sprintf(
            'INSERT INTO invoice (%s) VALUES (%s)',
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
                    case 'SYMBOL':                        
                        $stmt->bindValue($identifier, $this->symbol, PDO::PARAM_STR);
                        break;
                    case 'INVOICE_DATE':                        
                        $stmt->bindValue($identifier, $this->invoice_date ? $this->invoice_date->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'SALES_DATE':                        
                        $stmt->bindValue($identifier, $this->sales_date ? $this->sales_date->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'PAYMENT_DUE_DATE':                        
                        $stmt->bindValue($identifier, $this->payment_due_date ? $this->payment_due_date->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'SALES_PERSON':                        
                        $stmt->bindValue($identifier, $this->sales_person, PDO::PARAM_STR);
                        break;
                    case 'INVOICE_TYPE':                        
                        $stmt->bindValue($identifier, $this->invoice_type, PDO::PARAM_INT);
                        break;
                    case 'COMMENT':                        
                        $stmt->bindValue($identifier, $this->comment, PDO::PARAM_STR);
                        break;
                    case 'CONTENT_ORIGINAL':                        
                        if (is_resource($this->content_original)) {
                            rewind($this->content_original);
                        }
                        $stmt->bindValue($identifier, $this->content_original, PDO::PARAM_LOB);
                        break;
                    case 'CONTENT_COPY':                        
                        if (is_resource($this->content_copy)) {
                            rewind($this->content_copy);
                        }
                        $stmt->bindValue($identifier, $this->content_copy, PDO::PARAM_LOB);
                        break;
                    case 'ORDER_ID':                        
                        $stmt->bindValue($identifier, $this->order_id, PDO::PARAM_INT);
                        break;
                    case 'TOTAL_PAYED':                        
                        $stmt->bindValue($identifier, $this->total_payed, PDO::PARAM_STR);
                        break;
                    case 'SHOP_ID':                        
                        $stmt->bindValue($identifier, $this->shop_id, PDO::PARAM_INT);
                        break;
                    case 'EXTERNAL_ID':                        
                        $stmt->bindValue($identifier, $this->external_id, PDO::PARAM_INT);
                        break;
                    case 'CONTENT_TYPE':                        
                        $stmt->bindValue($identifier, $this->content_type, PDO::PARAM_STR);
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
        $pos = InvoiceTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getSymbol();
                break;
            case 2:
                return $this->getInvoiceDate();
                break;
            case 3:
                return $this->getSalesDate();
                break;
            case 4:
                return $this->getPaymentDueDate();
                break;
            case 5:
                return $this->getSalesPerson();
                break;
            case 6:
                return $this->getInvoiceType();
                break;
            case 7:
                return $this->getComment();
                break;
            case 8:
                return $this->getContentOriginal();
                break;
            case 9:
                return $this->getContentCopy();
                break;
            case 10:
                return $this->getOrderId();
                break;
            case 11:
                return $this->getTotalPayed();
                break;
            case 12:
                return $this->getShopId();
                break;
            case 13:
                return $this->getExternalId();
                break;
            case 14:
                return $this->getContentType();
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
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array())
    {
        if (isset($alreadyDumpedObjects['Invoice'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Invoice'][$this->getPrimaryKey()] = true;
        $keys = InvoiceTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getSymbol(),
            $keys[2] => $this->getInvoiceDate(),
            $keys[3] => $this->getSalesDate(),
            $keys[4] => $this->getPaymentDueDate(),
            $keys[5] => $this->getSalesPerson(),
            $keys[6] => $this->getInvoiceType(),
            $keys[7] => $this->getComment(),
            $keys[8] => $this->getContentOriginal(),
            $keys[9] => $this->getContentCopy(),
            $keys[10] => $this->getOrderId(),
            $keys[11] => $this->getTotalPayed(),
            $keys[12] => $this->getShopId(),
            $keys[13] => $this->getExternalId(),
            $keys[14] => $this->getContentType(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
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
        $pos = InvoiceTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

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
                $this->setSymbol($value);
                break;
            case 2:
                $this->setInvoiceDate($value);
                break;
            case 3:
                $this->setSalesDate($value);
                break;
            case 4:
                $this->setPaymentDueDate($value);
                break;
            case 5:
                $this->setSalesPerson($value);
                break;
            case 6:
                $this->setInvoiceType($value);
                break;
            case 7:
                $this->setComment($value);
                break;
            case 8:
                $this->setContentOriginal($value);
                break;
            case 9:
                $this->setContentCopy($value);
                break;
            case 10:
                $this->setOrderId($value);
                break;
            case 11:
                $this->setTotalPayed($value);
                break;
            case 12:
                $this->setShopId($value);
                break;
            case 13:
                $this->setExternalId($value);
                break;
            case 14:
                $this->setContentType($value);
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
        $keys = InvoiceTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setSymbol($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setInvoiceDate($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setSalesDate($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setPaymentDueDate($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setSalesPerson($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setInvoiceType($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setComment($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setContentOriginal($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setContentCopy($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setOrderId($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setTotalPayed($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setShopId($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setExternalId($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setContentType($arr[$keys[14]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(InvoiceTableMap::DATABASE_NAME);

        if ($this->isColumnModified(InvoiceTableMap::COL_ID)) $criteria->add(InvoiceTableMap::COL_ID, $this->id);
        if ($this->isColumnModified(InvoiceTableMap::COL_SYMBOL)) $criteria->add(InvoiceTableMap::COL_SYMBOL, $this->symbol);
        if ($this->isColumnModified(InvoiceTableMap::COL_INVOICE_DATE)) $criteria->add(InvoiceTableMap::COL_INVOICE_DATE, $this->invoice_date);
        if ($this->isColumnModified(InvoiceTableMap::COL_SALES_DATE)) $criteria->add(InvoiceTableMap::COL_SALES_DATE, $this->sales_date);
        if ($this->isColumnModified(InvoiceTableMap::COL_PAYMENT_DUE_DATE)) $criteria->add(InvoiceTableMap::COL_PAYMENT_DUE_DATE, $this->payment_due_date);
        if ($this->isColumnModified(InvoiceTableMap::COL_SALES_PERSON)) $criteria->add(InvoiceTableMap::COL_SALES_PERSON, $this->sales_person);
        if ($this->isColumnModified(InvoiceTableMap::COL_INVOICE_TYPE)) $criteria->add(InvoiceTableMap::COL_INVOICE_TYPE, $this->invoice_type);
        if ($this->isColumnModified(InvoiceTableMap::COL_COMMENT)) $criteria->add(InvoiceTableMap::COL_COMMENT, $this->comment);
        if ($this->isColumnModified(InvoiceTableMap::COL_CONTENT_ORIGINAL)) $criteria->add(InvoiceTableMap::COL_CONTENT_ORIGINAL, $this->content_original);
        if ($this->isColumnModified(InvoiceTableMap::COL_CONTENT_COPY)) $criteria->add(InvoiceTableMap::COL_CONTENT_COPY, $this->content_copy);
        if ($this->isColumnModified(InvoiceTableMap::COL_ORDER_ID)) $criteria->add(InvoiceTableMap::COL_ORDER_ID, $this->order_id);
        if ($this->isColumnModified(InvoiceTableMap::COL_TOTAL_PAYED)) $criteria->add(InvoiceTableMap::COL_TOTAL_PAYED, $this->total_payed);
        if ($this->isColumnModified(InvoiceTableMap::COL_SHOP_ID)) $criteria->add(InvoiceTableMap::COL_SHOP_ID, $this->shop_id);
        if ($this->isColumnModified(InvoiceTableMap::COL_EXTERNAL_ID)) $criteria->add(InvoiceTableMap::COL_EXTERNAL_ID, $this->external_id);
        if ($this->isColumnModified(InvoiceTableMap::COL_CONTENT_TYPE)) $criteria->add(InvoiceTableMap::COL_CONTENT_TYPE, $this->content_type);

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
        $criteria = new Criteria(InvoiceTableMap::DATABASE_NAME);
        $criteria->add(InvoiceTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \Gekosale\Plugin\Invoice\Model\ORM\Invoice (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setSymbol($this->getSymbol());
        $copyObj->setInvoiceDate($this->getInvoiceDate());
        $copyObj->setSalesDate($this->getSalesDate());
        $copyObj->setPaymentDueDate($this->getPaymentDueDate());
        $copyObj->setSalesPerson($this->getSalesPerson());
        $copyObj->setInvoiceType($this->getInvoiceType());
        $copyObj->setComment($this->getComment());
        $copyObj->setContentOriginal($this->getContentOriginal());
        $copyObj->setContentCopy($this->getContentCopy());
        $copyObj->setOrderId($this->getOrderId());
        $copyObj->setTotalPayed($this->getTotalPayed());
        $copyObj->setShopId($this->getShopId());
        $copyObj->setExternalId($this->getExternalId());
        $copyObj->setContentType($this->getContentType());
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
     * @return                 \Gekosale\Plugin\Invoice\Model\ORM\Invoice Clone of current object.
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
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->symbol = null;
        $this->invoice_date = null;
        $this->sales_date = null;
        $this->payment_due_date = null;
        $this->sales_person = null;
        $this->invoice_type = null;
        $this->comment = null;
        $this->content_original = null;
        $this->content_copy = null;
        $this->order_id = null;
        $this->total_payed = null;
        $this->shop_id = null;
        $this->external_id = null;
        $this->content_type = null;
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
        } // if ($deep)

    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(InvoiceTableMap::DEFAULT_STRING_FORMAT);
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
