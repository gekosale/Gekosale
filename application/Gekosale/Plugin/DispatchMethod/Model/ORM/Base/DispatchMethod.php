<?php

namespace Gekosale\Plugin\DispatchMethod\Model\ORM\Base;

use \DateTime;
use \Exception;
use \PDO;
use Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod as ChildDispatchMethod;
use Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodI18n as ChildDispatchMethodI18n;
use Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodI18nQuery as ChildDispatchMethodI18nQuery;
use Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodPrice as ChildDispatchMethodPrice;
use Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodPriceQuery as ChildDispatchMethodPriceQuery;
use Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodQuery as ChildDispatchMethodQuery;
use Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodShop as ChildDispatchMethodShop;
use Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodShopQuery as ChildDispatchMethodShopQuery;
use Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodWeight as ChildDispatchMethodWeight;
use Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodWeightQuery as ChildDispatchMethodWeightQuery;
use Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodpaymentMethod as ChildDispatchMethodpaymentMethod;
use Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodpaymentMethodQuery as ChildDispatchMethodpaymentMethodQuery;
use Gekosale\Plugin\DispatchMethod\Model\ORM\Map\DispatchMethodTableMap;
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

abstract class DispatchMethod implements ActiveRecordInterface 
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Gekosale\\Plugin\\DispatchMethod\\Model\\ORM\\Map\\DispatchMethodTableMap';


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
     * The value for the type field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $type;

    /**
     * The value for the maximum_weight field.
     * @var        string
     */
    protected $maximum_weight;

    /**
     * The value for the free_delivery field.
     * @var        string
     */
    protected $free_delivery;

    /**
     * The value for the country_ids field.
     * @var        string
     */
    protected $country_ids;

    /**
     * The value for the currency_id field.
     * Note: this column has a database default value of: 28
     * @var        int
     */
    protected $currency_id;

    /**
     * The value for the hierarchy field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $hierarchy;

    /**
     * The value for the created_at field.
     * @var        string
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     * @var        string
     */
    protected $updated_at;

    /**
     * @var        ObjectCollection|ChildDispatchMethodPrice[] Collection to store aggregation of ChildDispatchMethodPrice objects.
     */
    protected $collDispatchMethodPrices;
    protected $collDispatchMethodPricesPartial;

    /**
     * @var        ObjectCollection|ChildDispatchMethodWeight[] Collection to store aggregation of ChildDispatchMethodWeight objects.
     */
    protected $collDispatchMethodWeights;
    protected $collDispatchMethodWeightsPartial;

    /**
     * @var        ObjectCollection|ChildDispatchMethodpaymentMethod[] Collection to store aggregation of ChildDispatchMethodpaymentMethod objects.
     */
    protected $collDispatchMethodpaymentMethods;
    protected $collDispatchMethodpaymentMethodsPartial;

    /**
     * @var        ObjectCollection|ChildDispatchMethodShop[] Collection to store aggregation of ChildDispatchMethodShop objects.
     */
    protected $collDispatchMethodShops;
    protected $collDispatchMethodShopsPartial;

    /**
     * @var        ObjectCollection|ChildDispatchMethodI18n[] Collection to store aggregation of ChildDispatchMethodI18n objects.
     */
    protected $collDispatchMethodI18ns;
    protected $collDispatchMethodI18nsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    // i18n behavior
    
    /**
     * Current locale
     * @var        string
     */
    protected $currentLocale = 'en_US';
    
    /**
     * Current translation objects
     * @var        array[ChildDispatchMethodI18n]
     */
    protected $currentTranslations;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $dispatchMethodPricesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $dispatchMethodWeightsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $dispatchMethodpaymentMethodsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $dispatchMethodShopsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $dispatchMethodI18nsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->type = 1;
        $this->currency_id = 28;
        $this->hierarchy = 0;
    }

    /**
     * Initializes internal state of Gekosale\Plugin\DispatchMethod\Model\ORM\Base\DispatchMethod object.
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
     * Compares this with another <code>DispatchMethod</code> instance.  If
     * <code>obj</code> is an instance of <code>DispatchMethod</code>, delegates to
     * <code>equals(DispatchMethod)</code>.  Otherwise, returns <code>false</code>.
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
     * @return DispatchMethod The current object, for fluid interface
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
     * @return DispatchMethod The current object, for fluid interface
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
     * Get the [type] column value.
     * 
     * @return   int
     */
    public function getType()
    {

        return $this->type;
    }

    /**
     * Get the [maximum_weight] column value.
     * 
     * @return   string
     */
    public function getMaximumWeight()
    {

        return $this->maximum_weight;
    }

    /**
     * Get the [free_delivery] column value.
     * 
     * @return   string
     */
    public function getFreeDelivery()
    {

        return $this->free_delivery;
    }

    /**
     * Get the [country_ids] column value.
     * 
     * @return   string
     */
    public function getCountryIds()
    {

        return $this->country_ids;
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
     * Get the [hierarchy] column value.
     * 
     * @return   int
     */
    public function getHierarchy()
    {

        return $this->hierarchy;
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     * 
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->created_at;
        } else {
            return $this->created_at instanceof \DateTime ? $this->created_at->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     * 
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->updated_at;
        } else {
            return $this->updated_at instanceof \DateTime ? $this->updated_at->format($format) : null;
        }
    }

    /**
     * Set the value of [id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[DispatchMethodTableMap::COL_ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [type] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod The current object (for fluent API support)
     */
    public function setType($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[DispatchMethodTableMap::COL_TYPE] = true;
        }


        return $this;
    } // setType()

    /**
     * Set the value of [maximum_weight] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod The current object (for fluent API support)
     */
    public function setMaximumWeight($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->maximum_weight !== $v) {
            $this->maximum_weight = $v;
            $this->modifiedColumns[DispatchMethodTableMap::COL_MAXIMUM_WEIGHT] = true;
        }


        return $this;
    } // setMaximumWeight()

    /**
     * Set the value of [free_delivery] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod The current object (for fluent API support)
     */
    public function setFreeDelivery($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->free_delivery !== $v) {
            $this->free_delivery = $v;
            $this->modifiedColumns[DispatchMethodTableMap::COL_FREE_DELIVERY] = true;
        }


        return $this;
    } // setFreeDelivery()

    /**
     * Set the value of [country_ids] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod The current object (for fluent API support)
     */
    public function setCountryIds($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->country_ids !== $v) {
            $this->country_ids = $v;
            $this->modifiedColumns[DispatchMethodTableMap::COL_COUNTRY_IDS] = true;
        }


        return $this;
    } // setCountryIds()

    /**
     * Set the value of [currency_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod The current object (for fluent API support)
     */
    public function setCurrencyId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->currency_id !== $v) {
            $this->currency_id = $v;
            $this->modifiedColumns[DispatchMethodTableMap::COL_CURRENCY_ID] = true;
        }


        return $this;
    } // setCurrencyId()

    /**
     * Set the value of [hierarchy] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod The current object (for fluent API support)
     */
    public function setHierarchy($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->hierarchy !== $v) {
            $this->hierarchy = $v;
            $this->modifiedColumns[DispatchMethodTableMap::COL_HIERARCHY] = true;
        }


        return $this;
    } // setHierarchy()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     * 
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($dt !== $this->created_at) {
                $this->created_at = $dt;
                $this->modifiedColumns[DispatchMethodTableMap::COL_CREATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     * 
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($dt !== $this->updated_at) {
                $this->updated_at = $dt;
                $this->modifiedColumns[DispatchMethodTableMap::COL_UPDATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setUpdatedAt()

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
            if ($this->type !== 1) {
                return false;
            }

            if ($this->currency_id !== 28) {
                return false;
            }

            if ($this->hierarchy !== 0) {
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


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : DispatchMethodTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : DispatchMethodTableMap::translateFieldName('Type', TableMap::TYPE_PHPNAME, $indexType)];
            $this->type = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : DispatchMethodTableMap::translateFieldName('MaximumWeight', TableMap::TYPE_PHPNAME, $indexType)];
            $this->maximum_weight = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : DispatchMethodTableMap::translateFieldName('FreeDelivery', TableMap::TYPE_PHPNAME, $indexType)];
            $this->free_delivery = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : DispatchMethodTableMap::translateFieldName('CountryIds', TableMap::TYPE_PHPNAME, $indexType)];
            $this->country_ids = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : DispatchMethodTableMap::translateFieldName('CurrencyId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->currency_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : DispatchMethodTableMap::translateFieldName('Hierarchy', TableMap::TYPE_PHPNAME, $indexType)];
            $this->hierarchy = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : DispatchMethodTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : DispatchMethodTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 9; // 9 = DispatchMethodTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod object", 0, $e);
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
            $con = Propel::getServiceContainer()->getReadConnection(DispatchMethodTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildDispatchMethodQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collDispatchMethodPrices = null;

            $this->collDispatchMethodWeights = null;

            $this->collDispatchMethodpaymentMethods = null;

            $this->collDispatchMethodShops = null;

            $this->collDispatchMethodI18ns = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see DispatchMethod::setDeleted()
     * @see DispatchMethod::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(DispatchMethodTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildDispatchMethodQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(DispatchMethodTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(DispatchMethodTableMap::COL_CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(DispatchMethodTableMap::COL_UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(DispatchMethodTableMap::COL_UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                DispatchMethodTableMap::addInstanceToPool($this);
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
                $this->resetModified();
            }

            if ($this->dispatchMethodPricesScheduledForDeletion !== null) {
                if (!$this->dispatchMethodPricesScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodPriceQuery::create()
                        ->filterByPrimaryKeys($this->dispatchMethodPricesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->dispatchMethodPricesScheduledForDeletion = null;
                }
            }

                if ($this->collDispatchMethodPrices !== null) {
            foreach ($this->collDispatchMethodPrices as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->dispatchMethodWeightsScheduledForDeletion !== null) {
                if (!$this->dispatchMethodWeightsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodWeightQuery::create()
                        ->filterByPrimaryKeys($this->dispatchMethodWeightsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->dispatchMethodWeightsScheduledForDeletion = null;
                }
            }

                if ($this->collDispatchMethodWeights !== null) {
            foreach ($this->collDispatchMethodWeights as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->dispatchMethodpaymentMethodsScheduledForDeletion !== null) {
                if (!$this->dispatchMethodpaymentMethodsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodpaymentMethodQuery::create()
                        ->filterByPrimaryKeys($this->dispatchMethodpaymentMethodsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->dispatchMethodpaymentMethodsScheduledForDeletion = null;
                }
            }

                if ($this->collDispatchMethodpaymentMethods !== null) {
            foreach ($this->collDispatchMethodpaymentMethods as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->dispatchMethodShopsScheduledForDeletion !== null) {
                if (!$this->dispatchMethodShopsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodShopQuery::create()
                        ->filterByPrimaryKeys($this->dispatchMethodShopsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->dispatchMethodShopsScheduledForDeletion = null;
                }
            }

                if ($this->collDispatchMethodShops !== null) {
            foreach ($this->collDispatchMethodShops as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->dispatchMethodI18nsScheduledForDeletion !== null) {
                if (!$this->dispatchMethodI18nsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodI18nQuery::create()
                        ->filterByPrimaryKeys($this->dispatchMethodI18nsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->dispatchMethodI18nsScheduledForDeletion = null;
                }
            }

                if ($this->collDispatchMethodI18ns !== null) {
            foreach ($this->collDispatchMethodI18ns as $referrerFK) {
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

        $this->modifiedColumns[DispatchMethodTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . DispatchMethodTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(DispatchMethodTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(DispatchMethodTableMap::COL_TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'TYPE';
        }
        if ($this->isColumnModified(DispatchMethodTableMap::COL_MAXIMUM_WEIGHT)) {
            $modifiedColumns[':p' . $index++]  = 'MAXIMUM_WEIGHT';
        }
        if ($this->isColumnModified(DispatchMethodTableMap::COL_FREE_DELIVERY)) {
            $modifiedColumns[':p' . $index++]  = 'FREE_DELIVERY';
        }
        if ($this->isColumnModified(DispatchMethodTableMap::COL_COUNTRY_IDS)) {
            $modifiedColumns[':p' . $index++]  = 'COUNTRY_IDS';
        }
        if ($this->isColumnModified(DispatchMethodTableMap::COL_CURRENCY_ID)) {
            $modifiedColumns[':p' . $index++]  = 'CURRENCY_ID';
        }
        if ($this->isColumnModified(DispatchMethodTableMap::COL_HIERARCHY)) {
            $modifiedColumns[':p' . $index++]  = 'HIERARCHY';
        }
        if ($this->isColumnModified(DispatchMethodTableMap::COL_CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'CREATED_AT';
        }
        if ($this->isColumnModified(DispatchMethodTableMap::COL_UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'UPDATED_AT';
        }

        $sql = sprintf(
            'INSERT INTO dispatch_method (%s) VALUES (%s)',
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
                    case 'TYPE':                        
                        $stmt->bindValue($identifier, $this->type, PDO::PARAM_INT);
                        break;
                    case 'MAXIMUM_WEIGHT':                        
                        $stmt->bindValue($identifier, $this->maximum_weight, PDO::PARAM_STR);
                        break;
                    case 'FREE_DELIVERY':                        
                        $stmt->bindValue($identifier, $this->free_delivery, PDO::PARAM_STR);
                        break;
                    case 'COUNTRY_IDS':                        
                        $stmt->bindValue($identifier, $this->country_ids, PDO::PARAM_STR);
                        break;
                    case 'CURRENCY_ID':                        
                        $stmt->bindValue($identifier, $this->currency_id, PDO::PARAM_INT);
                        break;
                    case 'HIERARCHY':                        
                        $stmt->bindValue($identifier, $this->hierarchy, PDO::PARAM_INT);
                        break;
                    case 'CREATED_AT':                        
                        $stmt->bindValue($identifier, $this->created_at ? $this->created_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'UPDATED_AT':                        
                        $stmt->bindValue($identifier, $this->updated_at ? $this->updated_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
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
        $pos = DispatchMethodTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getType();
                break;
            case 2:
                return $this->getMaximumWeight();
                break;
            case 3:
                return $this->getFreeDelivery();
                break;
            case 4:
                return $this->getCountryIds();
                break;
            case 5:
                return $this->getCurrencyId();
                break;
            case 6:
                return $this->getHierarchy();
                break;
            case 7:
                return $this->getCreatedAt();
                break;
            case 8:
                return $this->getUpdatedAt();
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
        if (isset($alreadyDumpedObjects['DispatchMethod'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['DispatchMethod'][$this->getPrimaryKey()] = true;
        $keys = DispatchMethodTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getType(),
            $keys[2] => $this->getMaximumWeight(),
            $keys[3] => $this->getFreeDelivery(),
            $keys[4] => $this->getCountryIds(),
            $keys[5] => $this->getCurrencyId(),
            $keys[6] => $this->getHierarchy(),
            $keys[7] => $this->getCreatedAt(),
            $keys[8] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }
        
        if ($includeForeignObjects) {
            if (null !== $this->collDispatchMethodPrices) {
                $result['DispatchMethodPrices'] = $this->collDispatchMethodPrices->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collDispatchMethodWeights) {
                $result['DispatchMethodWeights'] = $this->collDispatchMethodWeights->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collDispatchMethodpaymentMethods) {
                $result['DispatchMethodpaymentMethods'] = $this->collDispatchMethodpaymentMethods->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collDispatchMethodShops) {
                $result['DispatchMethodShops'] = $this->collDispatchMethodShops->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collDispatchMethodI18ns) {
                $result['DispatchMethodI18ns'] = $this->collDispatchMethodI18ns->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = DispatchMethodTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

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
                $this->setType($value);
                break;
            case 2:
                $this->setMaximumWeight($value);
                break;
            case 3:
                $this->setFreeDelivery($value);
                break;
            case 4:
                $this->setCountryIds($value);
                break;
            case 5:
                $this->setCurrencyId($value);
                break;
            case 6:
                $this->setHierarchy($value);
                break;
            case 7:
                $this->setCreatedAt($value);
                break;
            case 8:
                $this->setUpdatedAt($value);
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
        $keys = DispatchMethodTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setType($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setMaximumWeight($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setFreeDelivery($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setCountryIds($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setCurrencyId($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setHierarchy($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setCreatedAt($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setUpdatedAt($arr[$keys[8]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(DispatchMethodTableMap::DATABASE_NAME);

        if ($this->isColumnModified(DispatchMethodTableMap::COL_ID)) $criteria->add(DispatchMethodTableMap::COL_ID, $this->id);
        if ($this->isColumnModified(DispatchMethodTableMap::COL_TYPE)) $criteria->add(DispatchMethodTableMap::COL_TYPE, $this->type);
        if ($this->isColumnModified(DispatchMethodTableMap::COL_MAXIMUM_WEIGHT)) $criteria->add(DispatchMethodTableMap::COL_MAXIMUM_WEIGHT, $this->maximum_weight);
        if ($this->isColumnModified(DispatchMethodTableMap::COL_FREE_DELIVERY)) $criteria->add(DispatchMethodTableMap::COL_FREE_DELIVERY, $this->free_delivery);
        if ($this->isColumnModified(DispatchMethodTableMap::COL_COUNTRY_IDS)) $criteria->add(DispatchMethodTableMap::COL_COUNTRY_IDS, $this->country_ids);
        if ($this->isColumnModified(DispatchMethodTableMap::COL_CURRENCY_ID)) $criteria->add(DispatchMethodTableMap::COL_CURRENCY_ID, $this->currency_id);
        if ($this->isColumnModified(DispatchMethodTableMap::COL_HIERARCHY)) $criteria->add(DispatchMethodTableMap::COL_HIERARCHY, $this->hierarchy);
        if ($this->isColumnModified(DispatchMethodTableMap::COL_CREATED_AT)) $criteria->add(DispatchMethodTableMap::COL_CREATED_AT, $this->created_at);
        if ($this->isColumnModified(DispatchMethodTableMap::COL_UPDATED_AT)) $criteria->add(DispatchMethodTableMap::COL_UPDATED_AT, $this->updated_at);

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
        $criteria = new Criteria(DispatchMethodTableMap::DATABASE_NAME);
        $criteria->add(DispatchMethodTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setType($this->getType());
        $copyObj->setMaximumWeight($this->getMaximumWeight());
        $copyObj->setFreeDelivery($this->getFreeDelivery());
        $copyObj->setCountryIds($this->getCountryIds());
        $copyObj->setCurrencyId($this->getCurrencyId());
        $copyObj->setHierarchy($this->getHierarchy());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getDispatchMethodPrices() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDispatchMethodPrice($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getDispatchMethodWeights() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDispatchMethodWeight($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getDispatchMethodpaymentMethods() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDispatchMethodpaymentMethod($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getDispatchMethodShops() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDispatchMethodShop($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getDispatchMethodI18ns() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDispatchMethodI18n($relObj->copy($deepCopy));
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
     * @return                 \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod Clone of current object.
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
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('DispatchMethodPrice' == $relationName) {
            return $this->initDispatchMethodPrices();
        }
        if ('DispatchMethodWeight' == $relationName) {
            return $this->initDispatchMethodWeights();
        }
        if ('DispatchMethodpaymentMethod' == $relationName) {
            return $this->initDispatchMethodpaymentMethods();
        }
        if ('DispatchMethodShop' == $relationName) {
            return $this->initDispatchMethodShops();
        }
        if ('DispatchMethodI18n' == $relationName) {
            return $this->initDispatchMethodI18ns();
        }
    }

    /**
     * Clears out the collDispatchMethodPrices collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDispatchMethodPrices()
     */
    public function clearDispatchMethodPrices()
    {
        $this->collDispatchMethodPrices = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDispatchMethodPrices collection loaded partially.
     */
    public function resetPartialDispatchMethodPrices($v = true)
    {
        $this->collDispatchMethodPricesPartial = $v;
    }

    /**
     * Initializes the collDispatchMethodPrices collection.
     *
     * By default this just sets the collDispatchMethodPrices collection to an empty array (like clearcollDispatchMethodPrices());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDispatchMethodPrices($overrideExisting = true)
    {
        if (null !== $this->collDispatchMethodPrices && !$overrideExisting) {
            return;
        }
        $this->collDispatchMethodPrices = new ObjectCollection();
        $this->collDispatchMethodPrices->setModel('\Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodPrice');
    }

    /**
     * Gets an array of ChildDispatchMethodPrice objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDispatchMethod is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildDispatchMethodPrice[] List of ChildDispatchMethodPrice objects
     * @throws PropelException
     */
    public function getDispatchMethodPrices($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDispatchMethodPricesPartial && !$this->isNew();
        if (null === $this->collDispatchMethodPrices || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDispatchMethodPrices) {
                // return empty collection
                $this->initDispatchMethodPrices();
            } else {
                $collDispatchMethodPrices = ChildDispatchMethodPriceQuery::create(null, $criteria)
                    ->filterByDispatchMethod($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDispatchMethodPricesPartial && count($collDispatchMethodPrices)) {
                        $this->initDispatchMethodPrices(false);

                        foreach ($collDispatchMethodPrices as $obj) {
                            if (false == $this->collDispatchMethodPrices->contains($obj)) {
                                $this->collDispatchMethodPrices->append($obj);
                            }
                        }

                        $this->collDispatchMethodPricesPartial = true;
                    }

                    reset($collDispatchMethodPrices);

                    return $collDispatchMethodPrices;
                }

                if ($partial && $this->collDispatchMethodPrices) {
                    foreach ($this->collDispatchMethodPrices as $obj) {
                        if ($obj->isNew()) {
                            $collDispatchMethodPrices[] = $obj;
                        }
                    }
                }

                $this->collDispatchMethodPrices = $collDispatchMethodPrices;
                $this->collDispatchMethodPricesPartial = false;
            }
        }

        return $this->collDispatchMethodPrices;
    }

    /**
     * Sets a collection of DispatchMethodPrice objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $dispatchMethodPrices A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildDispatchMethod The current object (for fluent API support)
     */
    public function setDispatchMethodPrices(Collection $dispatchMethodPrices, ConnectionInterface $con = null)
    {
        $dispatchMethodPricesToDelete = $this->getDispatchMethodPrices(new Criteria(), $con)->diff($dispatchMethodPrices);

        
        $this->dispatchMethodPricesScheduledForDeletion = $dispatchMethodPricesToDelete;

        foreach ($dispatchMethodPricesToDelete as $dispatchMethodPriceRemoved) {
            $dispatchMethodPriceRemoved->setDispatchMethod(null);
        }

        $this->collDispatchMethodPrices = null;
        foreach ($dispatchMethodPrices as $dispatchMethodPrice) {
            $this->addDispatchMethodPrice($dispatchMethodPrice);
        }

        $this->collDispatchMethodPrices = $dispatchMethodPrices;
        $this->collDispatchMethodPricesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DispatchMethodPrice objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DispatchMethodPrice objects.
     * @throws PropelException
     */
    public function countDispatchMethodPrices(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDispatchMethodPricesPartial && !$this->isNew();
        if (null === $this->collDispatchMethodPrices || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDispatchMethodPrices) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDispatchMethodPrices());
            }

            $query = ChildDispatchMethodPriceQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDispatchMethod($this)
                ->count($con);
        }

        return count($this->collDispatchMethodPrices);
    }

    /**
     * Method called to associate a ChildDispatchMethodPrice object to this object
     * through the ChildDispatchMethodPrice foreign key attribute.
     *
     * @param    ChildDispatchMethodPrice $l ChildDispatchMethodPrice
     * @return   \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod The current object (for fluent API support)
     */
    public function addDispatchMethodPrice(ChildDispatchMethodPrice $l)
    {
        if ($this->collDispatchMethodPrices === null) {
            $this->initDispatchMethodPrices();
            $this->collDispatchMethodPricesPartial = true;
        }

        if (!in_array($l, $this->collDispatchMethodPrices->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddDispatchMethodPrice($l);
        }

        return $this;
    }

    /**
     * @param DispatchMethodPrice $dispatchMethodPrice The dispatchMethodPrice object to add.
     */
    protected function doAddDispatchMethodPrice($dispatchMethodPrice)
    {
        $this->collDispatchMethodPrices[]= $dispatchMethodPrice;
        $dispatchMethodPrice->setDispatchMethod($this);
    }

    /**
     * @param  DispatchMethodPrice $dispatchMethodPrice The dispatchMethodPrice object to remove.
     * @return ChildDispatchMethod The current object (for fluent API support)
     */
    public function removeDispatchMethodPrice($dispatchMethodPrice)
    {
        if ($this->getDispatchMethodPrices()->contains($dispatchMethodPrice)) {
            $this->collDispatchMethodPrices->remove($this->collDispatchMethodPrices->search($dispatchMethodPrice));
            if (null === $this->dispatchMethodPricesScheduledForDeletion) {
                $this->dispatchMethodPricesScheduledForDeletion = clone $this->collDispatchMethodPrices;
                $this->dispatchMethodPricesScheduledForDeletion->clear();
            }
            $this->dispatchMethodPricesScheduledForDeletion[]= $dispatchMethodPrice;
            $dispatchMethodPrice->setDispatchMethod(null);
        }

        return $this;
    }

    /**
     * Clears out the collDispatchMethodWeights collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDispatchMethodWeights()
     */
    public function clearDispatchMethodWeights()
    {
        $this->collDispatchMethodWeights = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDispatchMethodWeights collection loaded partially.
     */
    public function resetPartialDispatchMethodWeights($v = true)
    {
        $this->collDispatchMethodWeightsPartial = $v;
    }

    /**
     * Initializes the collDispatchMethodWeights collection.
     *
     * By default this just sets the collDispatchMethodWeights collection to an empty array (like clearcollDispatchMethodWeights());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDispatchMethodWeights($overrideExisting = true)
    {
        if (null !== $this->collDispatchMethodWeights && !$overrideExisting) {
            return;
        }
        $this->collDispatchMethodWeights = new ObjectCollection();
        $this->collDispatchMethodWeights->setModel('\Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodWeight');
    }

    /**
     * Gets an array of ChildDispatchMethodWeight objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDispatchMethod is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildDispatchMethodWeight[] List of ChildDispatchMethodWeight objects
     * @throws PropelException
     */
    public function getDispatchMethodWeights($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDispatchMethodWeightsPartial && !$this->isNew();
        if (null === $this->collDispatchMethodWeights || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDispatchMethodWeights) {
                // return empty collection
                $this->initDispatchMethodWeights();
            } else {
                $collDispatchMethodWeights = ChildDispatchMethodWeightQuery::create(null, $criteria)
                    ->filterByDispatchMethod($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDispatchMethodWeightsPartial && count($collDispatchMethodWeights)) {
                        $this->initDispatchMethodWeights(false);

                        foreach ($collDispatchMethodWeights as $obj) {
                            if (false == $this->collDispatchMethodWeights->contains($obj)) {
                                $this->collDispatchMethodWeights->append($obj);
                            }
                        }

                        $this->collDispatchMethodWeightsPartial = true;
                    }

                    reset($collDispatchMethodWeights);

                    return $collDispatchMethodWeights;
                }

                if ($partial && $this->collDispatchMethodWeights) {
                    foreach ($this->collDispatchMethodWeights as $obj) {
                        if ($obj->isNew()) {
                            $collDispatchMethodWeights[] = $obj;
                        }
                    }
                }

                $this->collDispatchMethodWeights = $collDispatchMethodWeights;
                $this->collDispatchMethodWeightsPartial = false;
            }
        }

        return $this->collDispatchMethodWeights;
    }

    /**
     * Sets a collection of DispatchMethodWeight objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $dispatchMethodWeights A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildDispatchMethod The current object (for fluent API support)
     */
    public function setDispatchMethodWeights(Collection $dispatchMethodWeights, ConnectionInterface $con = null)
    {
        $dispatchMethodWeightsToDelete = $this->getDispatchMethodWeights(new Criteria(), $con)->diff($dispatchMethodWeights);

        
        $this->dispatchMethodWeightsScheduledForDeletion = $dispatchMethodWeightsToDelete;

        foreach ($dispatchMethodWeightsToDelete as $dispatchMethodWeightRemoved) {
            $dispatchMethodWeightRemoved->setDispatchMethod(null);
        }

        $this->collDispatchMethodWeights = null;
        foreach ($dispatchMethodWeights as $dispatchMethodWeight) {
            $this->addDispatchMethodWeight($dispatchMethodWeight);
        }

        $this->collDispatchMethodWeights = $dispatchMethodWeights;
        $this->collDispatchMethodWeightsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DispatchMethodWeight objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DispatchMethodWeight objects.
     * @throws PropelException
     */
    public function countDispatchMethodWeights(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDispatchMethodWeightsPartial && !$this->isNew();
        if (null === $this->collDispatchMethodWeights || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDispatchMethodWeights) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDispatchMethodWeights());
            }

            $query = ChildDispatchMethodWeightQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDispatchMethod($this)
                ->count($con);
        }

        return count($this->collDispatchMethodWeights);
    }

    /**
     * Method called to associate a ChildDispatchMethodWeight object to this object
     * through the ChildDispatchMethodWeight foreign key attribute.
     *
     * @param    ChildDispatchMethodWeight $l ChildDispatchMethodWeight
     * @return   \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod The current object (for fluent API support)
     */
    public function addDispatchMethodWeight(ChildDispatchMethodWeight $l)
    {
        if ($this->collDispatchMethodWeights === null) {
            $this->initDispatchMethodWeights();
            $this->collDispatchMethodWeightsPartial = true;
        }

        if (!in_array($l, $this->collDispatchMethodWeights->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddDispatchMethodWeight($l);
        }

        return $this;
    }

    /**
     * @param DispatchMethodWeight $dispatchMethodWeight The dispatchMethodWeight object to add.
     */
    protected function doAddDispatchMethodWeight($dispatchMethodWeight)
    {
        $this->collDispatchMethodWeights[]= $dispatchMethodWeight;
        $dispatchMethodWeight->setDispatchMethod($this);
    }

    /**
     * @param  DispatchMethodWeight $dispatchMethodWeight The dispatchMethodWeight object to remove.
     * @return ChildDispatchMethod The current object (for fluent API support)
     */
    public function removeDispatchMethodWeight($dispatchMethodWeight)
    {
        if ($this->getDispatchMethodWeights()->contains($dispatchMethodWeight)) {
            $this->collDispatchMethodWeights->remove($this->collDispatchMethodWeights->search($dispatchMethodWeight));
            if (null === $this->dispatchMethodWeightsScheduledForDeletion) {
                $this->dispatchMethodWeightsScheduledForDeletion = clone $this->collDispatchMethodWeights;
                $this->dispatchMethodWeightsScheduledForDeletion->clear();
            }
            $this->dispatchMethodWeightsScheduledForDeletion[]= clone $dispatchMethodWeight;
            $dispatchMethodWeight->setDispatchMethod(null);
        }

        return $this;
    }

    /**
     * Clears out the collDispatchMethodpaymentMethods collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDispatchMethodpaymentMethods()
     */
    public function clearDispatchMethodpaymentMethods()
    {
        $this->collDispatchMethodpaymentMethods = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDispatchMethodpaymentMethods collection loaded partially.
     */
    public function resetPartialDispatchMethodpaymentMethods($v = true)
    {
        $this->collDispatchMethodpaymentMethodsPartial = $v;
    }

    /**
     * Initializes the collDispatchMethodpaymentMethods collection.
     *
     * By default this just sets the collDispatchMethodpaymentMethods collection to an empty array (like clearcollDispatchMethodpaymentMethods());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDispatchMethodpaymentMethods($overrideExisting = true)
    {
        if (null !== $this->collDispatchMethodpaymentMethods && !$overrideExisting) {
            return;
        }
        $this->collDispatchMethodpaymentMethods = new ObjectCollection();
        $this->collDispatchMethodpaymentMethods->setModel('\Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodpaymentMethod');
    }

    /**
     * Gets an array of ChildDispatchMethodpaymentMethod objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDispatchMethod is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildDispatchMethodpaymentMethod[] List of ChildDispatchMethodpaymentMethod objects
     * @throws PropelException
     */
    public function getDispatchMethodpaymentMethods($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDispatchMethodpaymentMethodsPartial && !$this->isNew();
        if (null === $this->collDispatchMethodpaymentMethods || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDispatchMethodpaymentMethods) {
                // return empty collection
                $this->initDispatchMethodpaymentMethods();
            } else {
                $collDispatchMethodpaymentMethods = ChildDispatchMethodpaymentMethodQuery::create(null, $criteria)
                    ->filterByDispatchMethod($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDispatchMethodpaymentMethodsPartial && count($collDispatchMethodpaymentMethods)) {
                        $this->initDispatchMethodpaymentMethods(false);

                        foreach ($collDispatchMethodpaymentMethods as $obj) {
                            if (false == $this->collDispatchMethodpaymentMethods->contains($obj)) {
                                $this->collDispatchMethodpaymentMethods->append($obj);
                            }
                        }

                        $this->collDispatchMethodpaymentMethodsPartial = true;
                    }

                    reset($collDispatchMethodpaymentMethods);

                    return $collDispatchMethodpaymentMethods;
                }

                if ($partial && $this->collDispatchMethodpaymentMethods) {
                    foreach ($this->collDispatchMethodpaymentMethods as $obj) {
                        if ($obj->isNew()) {
                            $collDispatchMethodpaymentMethods[] = $obj;
                        }
                    }
                }

                $this->collDispatchMethodpaymentMethods = $collDispatchMethodpaymentMethods;
                $this->collDispatchMethodpaymentMethodsPartial = false;
            }
        }

        return $this->collDispatchMethodpaymentMethods;
    }

    /**
     * Sets a collection of DispatchMethodpaymentMethod objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $dispatchMethodpaymentMethods A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildDispatchMethod The current object (for fluent API support)
     */
    public function setDispatchMethodpaymentMethods(Collection $dispatchMethodpaymentMethods, ConnectionInterface $con = null)
    {
        $dispatchMethodpaymentMethodsToDelete = $this->getDispatchMethodpaymentMethods(new Criteria(), $con)->diff($dispatchMethodpaymentMethods);

        
        $this->dispatchMethodpaymentMethodsScheduledForDeletion = $dispatchMethodpaymentMethodsToDelete;

        foreach ($dispatchMethodpaymentMethodsToDelete as $dispatchMethodpaymentMethodRemoved) {
            $dispatchMethodpaymentMethodRemoved->setDispatchMethod(null);
        }

        $this->collDispatchMethodpaymentMethods = null;
        foreach ($dispatchMethodpaymentMethods as $dispatchMethodpaymentMethod) {
            $this->addDispatchMethodpaymentMethod($dispatchMethodpaymentMethod);
        }

        $this->collDispatchMethodpaymentMethods = $dispatchMethodpaymentMethods;
        $this->collDispatchMethodpaymentMethodsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DispatchMethodpaymentMethod objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DispatchMethodpaymentMethod objects.
     * @throws PropelException
     */
    public function countDispatchMethodpaymentMethods(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDispatchMethodpaymentMethodsPartial && !$this->isNew();
        if (null === $this->collDispatchMethodpaymentMethods || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDispatchMethodpaymentMethods) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDispatchMethodpaymentMethods());
            }

            $query = ChildDispatchMethodpaymentMethodQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDispatchMethod($this)
                ->count($con);
        }

        return count($this->collDispatchMethodpaymentMethods);
    }

    /**
     * Method called to associate a ChildDispatchMethodpaymentMethod object to this object
     * through the ChildDispatchMethodpaymentMethod foreign key attribute.
     *
     * @param    ChildDispatchMethodpaymentMethod $l ChildDispatchMethodpaymentMethod
     * @return   \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod The current object (for fluent API support)
     */
    public function addDispatchMethodpaymentMethod(ChildDispatchMethodpaymentMethod $l)
    {
        if ($this->collDispatchMethodpaymentMethods === null) {
            $this->initDispatchMethodpaymentMethods();
            $this->collDispatchMethodpaymentMethodsPartial = true;
        }

        if (!in_array($l, $this->collDispatchMethodpaymentMethods->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddDispatchMethodpaymentMethod($l);
        }

        return $this;
    }

    /**
     * @param DispatchMethodpaymentMethod $dispatchMethodpaymentMethod The dispatchMethodpaymentMethod object to add.
     */
    protected function doAddDispatchMethodpaymentMethod($dispatchMethodpaymentMethod)
    {
        $this->collDispatchMethodpaymentMethods[]= $dispatchMethodpaymentMethod;
        $dispatchMethodpaymentMethod->setDispatchMethod($this);
    }

    /**
     * @param  DispatchMethodpaymentMethod $dispatchMethodpaymentMethod The dispatchMethodpaymentMethod object to remove.
     * @return ChildDispatchMethod The current object (for fluent API support)
     */
    public function removeDispatchMethodpaymentMethod($dispatchMethodpaymentMethod)
    {
        if ($this->getDispatchMethodpaymentMethods()->contains($dispatchMethodpaymentMethod)) {
            $this->collDispatchMethodpaymentMethods->remove($this->collDispatchMethodpaymentMethods->search($dispatchMethodpaymentMethod));
            if (null === $this->dispatchMethodpaymentMethodsScheduledForDeletion) {
                $this->dispatchMethodpaymentMethodsScheduledForDeletion = clone $this->collDispatchMethodpaymentMethods;
                $this->dispatchMethodpaymentMethodsScheduledForDeletion->clear();
            }
            $this->dispatchMethodpaymentMethodsScheduledForDeletion[]= clone $dispatchMethodpaymentMethod;
            $dispatchMethodpaymentMethod->setDispatchMethod(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this DispatchMethod is new, it will return
     * an empty collection; or if this DispatchMethod has previously
     * been saved, it will retrieve related DispatchMethodpaymentMethods from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in DispatchMethod.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildDispatchMethodpaymentMethod[] List of ChildDispatchMethodpaymentMethod objects
     */
    public function getDispatchMethodpaymentMethodsJoinPaymentMethod($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildDispatchMethodpaymentMethodQuery::create(null, $criteria);
        $query->joinWith('PaymentMethod', $joinBehavior);

        return $this->getDispatchMethodpaymentMethods($query, $con);
    }

    /**
     * Clears out the collDispatchMethodShops collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDispatchMethodShops()
     */
    public function clearDispatchMethodShops()
    {
        $this->collDispatchMethodShops = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDispatchMethodShops collection loaded partially.
     */
    public function resetPartialDispatchMethodShops($v = true)
    {
        $this->collDispatchMethodShopsPartial = $v;
    }

    /**
     * Initializes the collDispatchMethodShops collection.
     *
     * By default this just sets the collDispatchMethodShops collection to an empty array (like clearcollDispatchMethodShops());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDispatchMethodShops($overrideExisting = true)
    {
        if (null !== $this->collDispatchMethodShops && !$overrideExisting) {
            return;
        }
        $this->collDispatchMethodShops = new ObjectCollection();
        $this->collDispatchMethodShops->setModel('\Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodShop');
    }

    /**
     * Gets an array of ChildDispatchMethodShop objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDispatchMethod is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildDispatchMethodShop[] List of ChildDispatchMethodShop objects
     * @throws PropelException
     */
    public function getDispatchMethodShops($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDispatchMethodShopsPartial && !$this->isNew();
        if (null === $this->collDispatchMethodShops || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDispatchMethodShops) {
                // return empty collection
                $this->initDispatchMethodShops();
            } else {
                $collDispatchMethodShops = ChildDispatchMethodShopQuery::create(null, $criteria)
                    ->filterByDispatchMethod($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDispatchMethodShopsPartial && count($collDispatchMethodShops)) {
                        $this->initDispatchMethodShops(false);

                        foreach ($collDispatchMethodShops as $obj) {
                            if (false == $this->collDispatchMethodShops->contains($obj)) {
                                $this->collDispatchMethodShops->append($obj);
                            }
                        }

                        $this->collDispatchMethodShopsPartial = true;
                    }

                    reset($collDispatchMethodShops);

                    return $collDispatchMethodShops;
                }

                if ($partial && $this->collDispatchMethodShops) {
                    foreach ($this->collDispatchMethodShops as $obj) {
                        if ($obj->isNew()) {
                            $collDispatchMethodShops[] = $obj;
                        }
                    }
                }

                $this->collDispatchMethodShops = $collDispatchMethodShops;
                $this->collDispatchMethodShopsPartial = false;
            }
        }

        return $this->collDispatchMethodShops;
    }

    /**
     * Sets a collection of DispatchMethodShop objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $dispatchMethodShops A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildDispatchMethod The current object (for fluent API support)
     */
    public function setDispatchMethodShops(Collection $dispatchMethodShops, ConnectionInterface $con = null)
    {
        $dispatchMethodShopsToDelete = $this->getDispatchMethodShops(new Criteria(), $con)->diff($dispatchMethodShops);

        
        $this->dispatchMethodShopsScheduledForDeletion = $dispatchMethodShopsToDelete;

        foreach ($dispatchMethodShopsToDelete as $dispatchMethodShopRemoved) {
            $dispatchMethodShopRemoved->setDispatchMethod(null);
        }

        $this->collDispatchMethodShops = null;
        foreach ($dispatchMethodShops as $dispatchMethodShop) {
            $this->addDispatchMethodShop($dispatchMethodShop);
        }

        $this->collDispatchMethodShops = $dispatchMethodShops;
        $this->collDispatchMethodShopsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DispatchMethodShop objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DispatchMethodShop objects.
     * @throws PropelException
     */
    public function countDispatchMethodShops(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDispatchMethodShopsPartial && !$this->isNew();
        if (null === $this->collDispatchMethodShops || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDispatchMethodShops) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDispatchMethodShops());
            }

            $query = ChildDispatchMethodShopQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDispatchMethod($this)
                ->count($con);
        }

        return count($this->collDispatchMethodShops);
    }

    /**
     * Method called to associate a ChildDispatchMethodShop object to this object
     * through the ChildDispatchMethodShop foreign key attribute.
     *
     * @param    ChildDispatchMethodShop $l ChildDispatchMethodShop
     * @return   \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod The current object (for fluent API support)
     */
    public function addDispatchMethodShop(ChildDispatchMethodShop $l)
    {
        if ($this->collDispatchMethodShops === null) {
            $this->initDispatchMethodShops();
            $this->collDispatchMethodShopsPartial = true;
        }

        if (!in_array($l, $this->collDispatchMethodShops->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddDispatchMethodShop($l);
        }

        return $this;
    }

    /**
     * @param DispatchMethodShop $dispatchMethodShop The dispatchMethodShop object to add.
     */
    protected function doAddDispatchMethodShop($dispatchMethodShop)
    {
        $this->collDispatchMethodShops[]= $dispatchMethodShop;
        $dispatchMethodShop->setDispatchMethod($this);
    }

    /**
     * @param  DispatchMethodShop $dispatchMethodShop The dispatchMethodShop object to remove.
     * @return ChildDispatchMethod The current object (for fluent API support)
     */
    public function removeDispatchMethodShop($dispatchMethodShop)
    {
        if ($this->getDispatchMethodShops()->contains($dispatchMethodShop)) {
            $this->collDispatchMethodShops->remove($this->collDispatchMethodShops->search($dispatchMethodShop));
            if (null === $this->dispatchMethodShopsScheduledForDeletion) {
                $this->dispatchMethodShopsScheduledForDeletion = clone $this->collDispatchMethodShops;
                $this->dispatchMethodShopsScheduledForDeletion->clear();
            }
            $this->dispatchMethodShopsScheduledForDeletion[]= clone $dispatchMethodShop;
            $dispatchMethodShop->setDispatchMethod(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this DispatchMethod is new, it will return
     * an empty collection; or if this DispatchMethod has previously
     * been saved, it will retrieve related DispatchMethodShops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in DispatchMethod.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildDispatchMethodShop[] List of ChildDispatchMethodShop objects
     */
    public function getDispatchMethodShopsJoinShop($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildDispatchMethodShopQuery::create(null, $criteria);
        $query->joinWith('Shop', $joinBehavior);

        return $this->getDispatchMethodShops($query, $con);
    }

    /**
     * Clears out the collDispatchMethodI18ns collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDispatchMethodI18ns()
     */
    public function clearDispatchMethodI18ns()
    {
        $this->collDispatchMethodI18ns = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDispatchMethodI18ns collection loaded partially.
     */
    public function resetPartialDispatchMethodI18ns($v = true)
    {
        $this->collDispatchMethodI18nsPartial = $v;
    }

    /**
     * Initializes the collDispatchMethodI18ns collection.
     *
     * By default this just sets the collDispatchMethodI18ns collection to an empty array (like clearcollDispatchMethodI18ns());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDispatchMethodI18ns($overrideExisting = true)
    {
        if (null !== $this->collDispatchMethodI18ns && !$overrideExisting) {
            return;
        }
        $this->collDispatchMethodI18ns = new ObjectCollection();
        $this->collDispatchMethodI18ns->setModel('\Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodI18n');
    }

    /**
     * Gets an array of ChildDispatchMethodI18n objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDispatchMethod is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildDispatchMethodI18n[] List of ChildDispatchMethodI18n objects
     * @throws PropelException
     */
    public function getDispatchMethodI18ns($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDispatchMethodI18nsPartial && !$this->isNew();
        if (null === $this->collDispatchMethodI18ns || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDispatchMethodI18ns) {
                // return empty collection
                $this->initDispatchMethodI18ns();
            } else {
                $collDispatchMethodI18ns = ChildDispatchMethodI18nQuery::create(null, $criteria)
                    ->filterByDispatchMethod($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDispatchMethodI18nsPartial && count($collDispatchMethodI18ns)) {
                        $this->initDispatchMethodI18ns(false);

                        foreach ($collDispatchMethodI18ns as $obj) {
                            if (false == $this->collDispatchMethodI18ns->contains($obj)) {
                                $this->collDispatchMethodI18ns->append($obj);
                            }
                        }

                        $this->collDispatchMethodI18nsPartial = true;
                    }

                    reset($collDispatchMethodI18ns);

                    return $collDispatchMethodI18ns;
                }

                if ($partial && $this->collDispatchMethodI18ns) {
                    foreach ($this->collDispatchMethodI18ns as $obj) {
                        if ($obj->isNew()) {
                            $collDispatchMethodI18ns[] = $obj;
                        }
                    }
                }

                $this->collDispatchMethodI18ns = $collDispatchMethodI18ns;
                $this->collDispatchMethodI18nsPartial = false;
            }
        }

        return $this->collDispatchMethodI18ns;
    }

    /**
     * Sets a collection of DispatchMethodI18n objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $dispatchMethodI18ns A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildDispatchMethod The current object (for fluent API support)
     */
    public function setDispatchMethodI18ns(Collection $dispatchMethodI18ns, ConnectionInterface $con = null)
    {
        $dispatchMethodI18nsToDelete = $this->getDispatchMethodI18ns(new Criteria(), $con)->diff($dispatchMethodI18ns);

        
        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->dispatchMethodI18nsScheduledForDeletion = clone $dispatchMethodI18nsToDelete;

        foreach ($dispatchMethodI18nsToDelete as $dispatchMethodI18nRemoved) {
            $dispatchMethodI18nRemoved->setDispatchMethod(null);
        }

        $this->collDispatchMethodI18ns = null;
        foreach ($dispatchMethodI18ns as $dispatchMethodI18n) {
            $this->addDispatchMethodI18n($dispatchMethodI18n);
        }

        $this->collDispatchMethodI18ns = $dispatchMethodI18ns;
        $this->collDispatchMethodI18nsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DispatchMethodI18n objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DispatchMethodI18n objects.
     * @throws PropelException
     */
    public function countDispatchMethodI18ns(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDispatchMethodI18nsPartial && !$this->isNew();
        if (null === $this->collDispatchMethodI18ns || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDispatchMethodI18ns) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDispatchMethodI18ns());
            }

            $query = ChildDispatchMethodI18nQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDispatchMethod($this)
                ->count($con);
        }

        return count($this->collDispatchMethodI18ns);
    }

    /**
     * Method called to associate a ChildDispatchMethodI18n object to this object
     * through the ChildDispatchMethodI18n foreign key attribute.
     *
     * @param    ChildDispatchMethodI18n $l ChildDispatchMethodI18n
     * @return   \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod The current object (for fluent API support)
     */
    public function addDispatchMethodI18n(ChildDispatchMethodI18n $l)
    {
        if ($l && $locale = $l->getLocale()) {
            $this->setLocale($locale);
            $this->currentTranslations[$locale] = $l;
        }
        if ($this->collDispatchMethodI18ns === null) {
            $this->initDispatchMethodI18ns();
            $this->collDispatchMethodI18nsPartial = true;
        }

        if (!in_array($l, $this->collDispatchMethodI18ns->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddDispatchMethodI18n($l);
        }

        return $this;
    }

    /**
     * @param DispatchMethodI18n $dispatchMethodI18n The dispatchMethodI18n object to add.
     */
    protected function doAddDispatchMethodI18n($dispatchMethodI18n)
    {
        $this->collDispatchMethodI18ns[]= $dispatchMethodI18n;
        $dispatchMethodI18n->setDispatchMethod($this);
    }

    /**
     * @param  DispatchMethodI18n $dispatchMethodI18n The dispatchMethodI18n object to remove.
     * @return ChildDispatchMethod The current object (for fluent API support)
     */
    public function removeDispatchMethodI18n($dispatchMethodI18n)
    {
        if ($this->getDispatchMethodI18ns()->contains($dispatchMethodI18n)) {
            $this->collDispatchMethodI18ns->remove($this->collDispatchMethodI18ns->search($dispatchMethodI18n));
            if (null === $this->dispatchMethodI18nsScheduledForDeletion) {
                $this->dispatchMethodI18nsScheduledForDeletion = clone $this->collDispatchMethodI18ns;
                $this->dispatchMethodI18nsScheduledForDeletion->clear();
            }
            $this->dispatchMethodI18nsScheduledForDeletion[]= clone $dispatchMethodI18n;
            $dispatchMethodI18n->setDispatchMethod(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->type = null;
        $this->maximum_weight = null;
        $this->free_delivery = null;
        $this->country_ids = null;
        $this->currency_id = null;
        $this->hierarchy = null;
        $this->created_at = null;
        $this->updated_at = null;
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
            if ($this->collDispatchMethodPrices) {
                foreach ($this->collDispatchMethodPrices as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collDispatchMethodWeights) {
                foreach ($this->collDispatchMethodWeights as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collDispatchMethodpaymentMethods) {
                foreach ($this->collDispatchMethodpaymentMethods as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collDispatchMethodShops) {
                foreach ($this->collDispatchMethodShops as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collDispatchMethodI18ns) {
                foreach ($this->collDispatchMethodI18ns as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        // i18n behavior
        $this->currentLocale = 'en_US';
        $this->currentTranslations = null;

        $this->collDispatchMethodPrices = null;
        $this->collDispatchMethodWeights = null;
        $this->collDispatchMethodpaymentMethods = null;
        $this->collDispatchMethodShops = null;
        $this->collDispatchMethodI18ns = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(DispatchMethodTableMap::DEFAULT_STRING_FORMAT);
    }

    // i18n behavior
    
    /**
     * Sets the locale for translations
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     *
     * @return    ChildDispatchMethod The current object (for fluent API support)
     */
    public function setLocale($locale = 'en_US')
    {
        $this->currentLocale = $locale;
    
        return $this;
    }
    
    /**
     * Gets the locale for translations
     *
     * @return    string $locale Locale to use for the translation, e.g. 'fr_FR'
     */
    public function getLocale()
    {
        return $this->currentLocale;
    }
    
    /**
     * Returns the current translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ChildDispatchMethodI18n */
    public function getTranslation($locale = 'en_US', ConnectionInterface $con = null)
    {
        if (!isset($this->currentTranslations[$locale])) {
            if (null !== $this->collDispatchMethodI18ns) {
                foreach ($this->collDispatchMethodI18ns as $translation) {
                    if ($translation->getLocale() == $locale) {
                        $this->currentTranslations[$locale] = $translation;
    
                        return $translation;
                    }
                }
            }
            if ($this->isNew()) {
                $translation = new ChildDispatchMethodI18n();
                $translation->setLocale($locale);
            } else {
                $translation = ChildDispatchMethodI18nQuery::create()
                    ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                    ->findOneOrCreate($con);
                $this->currentTranslations[$locale] = $translation;
            }
            $this->addDispatchMethodI18n($translation);
        }
    
        return $this->currentTranslations[$locale];
    }
    
    /**
     * Remove the translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return    ChildDispatchMethod The current object (for fluent API support)
     */
    public function removeTranslation($locale = 'en_US', ConnectionInterface $con = null)
    {
        if (!$this->isNew()) {
            ChildDispatchMethodI18nQuery::create()
                ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                ->delete($con);
        }
        if (isset($this->currentTranslations[$locale])) {
            unset($this->currentTranslations[$locale]);
        }
        foreach ($this->collDispatchMethodI18ns as $key => $translation) {
            if ($translation->getLocale() == $locale) {
                unset($this->collDispatchMethodI18ns[$key]);
                break;
            }
        }
    
        return $this;
    }
    
    /**
     * Returns the current translation
     *
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ChildDispatchMethodI18n */
    public function getCurrentTranslation(ConnectionInterface $con = null)
    {
        return $this->getTranslation($this->getLocale(), $con);
    }
    
    
        /**
         * Get the [name] column value.
         * 
         * @return   string
         */
        public function getName()
        {
        return $this->getCurrentTranslation()->getName();
    }
    
    
        /**
         * Set the value of [name] column.
         * 
         * @param      string $v new value
         * @return   \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodI18n The current object (for fluent API support)
         */
        public function setName($v)
        {    $this->getCurrentTranslation()->setName($v);
    
        return $this;
    }
    
    
        /**
         * Get the [description] column value.
         * 
         * @return   string
         */
        public function getDescription()
        {
        return $this->getCurrentTranslation()->getDescription();
    }
    
    
        /**
         * Set the value of [description] column.
         * 
         * @param      string $v new value
         * @return   \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodI18n The current object (for fluent API support)
         */
        public function setDescription($v)
        {    $this->getCurrentTranslation()->setDescription($v);
    
        return $this;
    }

    // timestampable behavior
    
    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     ChildDispatchMethod The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[DispatchMethodTableMap::COL_UPDATED_AT] = true;
    
        return $this;
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
