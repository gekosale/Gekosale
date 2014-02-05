<?php

namespace Gekosale\Plugin\Currency\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Currency\Model\ORM\Currency as ChildCurrency;
use Gekosale\Plugin\Currency\Model\ORM\CurrencyQuery as ChildCurrencyQuery;
use Gekosale\Plugin\Currency\Model\ORM\CurrencyShop as ChildCurrencyShop;
use Gekosale\Plugin\Currency\Model\ORM\CurrencyShopQuery as ChildCurrencyShopQuery;
use Gekosale\Plugin\Currency\Model\ORM\Map\CurrencyTableMap;
use Gekosale\Plugin\Locale\Model\ORM\Locale as ChildLocale;
use Gekosale\Plugin\Locale\Model\ORM\LocaleQuery;
use Gekosale\Plugin\Locale\Model\ORM\Base\Locale;
use Gekosale\Plugin\Product\Model\ORM\Product as ChildProduct;
use Gekosale\Plugin\Product\Model\ORM\ProductQuery;
use Gekosale\Plugin\Product\Model\ORM\Base\Product;
use Gekosale\Plugin\Shop\Model\ORM\Shop as ChildShop;
use Gekosale\Plugin\Shop\Model\ORM\ShopQuery;
use Gekosale\Plugin\Shop\Model\ORM\Base\Shop;
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

abstract class Currency implements ActiveRecordInterface 
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Gekosale\\Plugin\\Currency\\Model\\ORM\\Map\\CurrencyTableMap';


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
     * The value for the currency_symbol field.
     * @var        string
     */
    protected $currency_symbol;

    /**
     * The value for the decimal_separator field.
     * @var        string
     */
    protected $decimal_separator;

    /**
     * The value for the thousand_separator field.
     * @var        string
     */
    protected $thousand_separator;

    /**
     * The value for the positive_preffix field.
     * @var        string
     */
    protected $positive_preffix;

    /**
     * The value for the positive_suffix field.
     * @var        string
     */
    protected $positive_suffix;

    /**
     * The value for the negative_preffix field.
     * @var        string
     */
    protected $negative_preffix;

    /**
     * The value for the negative_suffix field.
     * @var        string
     */
    protected $negative_suffix;

    /**
     * The value for the decimal_count field.
     * @var        int
     */
    protected $decimal_count;

    /**
     * @var        ObjectCollection|ChildLocale[] Collection to store aggregation of ChildLocale objects.
     */
    protected $collLocales;
    protected $collLocalesPartial;

    /**
     * @var        ObjectCollection|ChildProduct[] Collection to store aggregation of ChildProduct objects.
     */
    protected $collProductsRelatedByBuyCurrencyId;
    protected $collProductsRelatedByBuyCurrencyIdPartial;

    /**
     * @var        ObjectCollection|ChildProduct[] Collection to store aggregation of ChildProduct objects.
     */
    protected $collProductsRelatedBySellCurrencyId;
    protected $collProductsRelatedBySellCurrencyIdPartial;

    /**
     * @var        ObjectCollection|ChildShop[] Collection to store aggregation of ChildShop objects.
     */
    protected $collShops;
    protected $collShopsPartial;

    /**
     * @var        ObjectCollection|ChildCurrencyShop[] Collection to store aggregation of ChildCurrencyShop objects.
     */
    protected $collCurrencyShops;
    protected $collCurrencyShopsPartial;

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
    protected $localesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $productsRelatedByBuyCurrencyIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $productsRelatedBySellCurrencyIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $shopsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $currencyShopsScheduledForDeletion = null;

    /**
     * Initializes internal state of Gekosale\Plugin\Currency\Model\ORM\Base\Currency object.
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
     * Compares this with another <code>Currency</code> instance.  If
     * <code>obj</code> is an instance of <code>Currency</code>, delegates to
     * <code>equals(Currency)</code>.  Otherwise, returns <code>false</code>.
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
     * @return Currency The current object, for fluid interface
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
     * @return Currency The current object, for fluid interface
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
     * Get the [currency_symbol] column value.
     * 
     * @return   string
     */
    public function getCurrencySymbol()
    {

        return $this->currency_symbol;
    }

    /**
     * Get the [decimal_separator] column value.
     * 
     * @return   string
     */
    public function getDecimalSeparator()
    {

        return $this->decimal_separator;
    }

    /**
     * Get the [thousand_separator] column value.
     * 
     * @return   string
     */
    public function getThousandSeparator()
    {

        return $this->thousand_separator;
    }

    /**
     * Get the [positive_preffix] column value.
     * 
     * @return   string
     */
    public function getPositivePreffix()
    {

        return $this->positive_preffix;
    }

    /**
     * Get the [positive_suffix] column value.
     * 
     * @return   string
     */
    public function getPositiveSuffix()
    {

        return $this->positive_suffix;
    }

    /**
     * Get the [negative_preffix] column value.
     * 
     * @return   string
     */
    public function getNegativePreffix()
    {

        return $this->negative_preffix;
    }

    /**
     * Get the [negative_suffix] column value.
     * 
     * @return   string
     */
    public function getNegativeSuffix()
    {

        return $this->negative_suffix;
    }

    /**
     * Get the [decimal_count] column value.
     * 
     * @return   int
     */
    public function getDecimalCount()
    {

        return $this->decimal_count;
    }

    /**
     * Set the value of [id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Currency\Model\ORM\Currency The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[CurrencyTableMap::COL_ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [name] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Currency\Model\ORM\Currency The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[CurrencyTableMap::COL_NAME] = true;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [currency_symbol] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Currency\Model\ORM\Currency The current object (for fluent API support)
     */
    public function setCurrencySymbol($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->currency_symbol !== $v) {
            $this->currency_symbol = $v;
            $this->modifiedColumns[CurrencyTableMap::COL_CURRENCY_SYMBOL] = true;
        }


        return $this;
    } // setCurrencySymbol()

    /**
     * Set the value of [decimal_separator] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Currency\Model\ORM\Currency The current object (for fluent API support)
     */
    public function setDecimalSeparator($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->decimal_separator !== $v) {
            $this->decimal_separator = $v;
            $this->modifiedColumns[CurrencyTableMap::COL_DECIMAL_SEPARATOR] = true;
        }


        return $this;
    } // setDecimalSeparator()

    /**
     * Set the value of [thousand_separator] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Currency\Model\ORM\Currency The current object (for fluent API support)
     */
    public function setThousandSeparator($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->thousand_separator !== $v) {
            $this->thousand_separator = $v;
            $this->modifiedColumns[CurrencyTableMap::COL_THOUSAND_SEPARATOR] = true;
        }


        return $this;
    } // setThousandSeparator()

    /**
     * Set the value of [positive_preffix] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Currency\Model\ORM\Currency The current object (for fluent API support)
     */
    public function setPositivePreffix($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->positive_preffix !== $v) {
            $this->positive_preffix = $v;
            $this->modifiedColumns[CurrencyTableMap::COL_POSITIVE_PREFFIX] = true;
        }


        return $this;
    } // setPositivePreffix()

    /**
     * Set the value of [positive_suffix] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Currency\Model\ORM\Currency The current object (for fluent API support)
     */
    public function setPositiveSuffix($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->positive_suffix !== $v) {
            $this->positive_suffix = $v;
            $this->modifiedColumns[CurrencyTableMap::COL_POSITIVE_SUFFIX] = true;
        }


        return $this;
    } // setPositiveSuffix()

    /**
     * Set the value of [negative_preffix] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Currency\Model\ORM\Currency The current object (for fluent API support)
     */
    public function setNegativePreffix($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->negative_preffix !== $v) {
            $this->negative_preffix = $v;
            $this->modifiedColumns[CurrencyTableMap::COL_NEGATIVE_PREFFIX] = true;
        }


        return $this;
    } // setNegativePreffix()

    /**
     * Set the value of [negative_suffix] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Currency\Model\ORM\Currency The current object (for fluent API support)
     */
    public function setNegativeSuffix($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->negative_suffix !== $v) {
            $this->negative_suffix = $v;
            $this->modifiedColumns[CurrencyTableMap::COL_NEGATIVE_SUFFIX] = true;
        }


        return $this;
    } // setNegativeSuffix()

    /**
     * Set the value of [decimal_count] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Currency\Model\ORM\Currency The current object (for fluent API support)
     */
    public function setDecimalCount($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->decimal_count !== $v) {
            $this->decimal_count = $v;
            $this->modifiedColumns[CurrencyTableMap::COL_DECIMAL_COUNT] = true;
        }


        return $this;
    } // setDecimalCount()

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


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : CurrencyTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : CurrencyTableMap::translateFieldName('Name', TableMap::TYPE_PHPNAME, $indexType)];
            $this->name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : CurrencyTableMap::translateFieldName('CurrencySymbol', TableMap::TYPE_PHPNAME, $indexType)];
            $this->currency_symbol = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : CurrencyTableMap::translateFieldName('DecimalSeparator', TableMap::TYPE_PHPNAME, $indexType)];
            $this->decimal_separator = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : CurrencyTableMap::translateFieldName('ThousandSeparator', TableMap::TYPE_PHPNAME, $indexType)];
            $this->thousand_separator = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : CurrencyTableMap::translateFieldName('PositivePreffix', TableMap::TYPE_PHPNAME, $indexType)];
            $this->positive_preffix = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : CurrencyTableMap::translateFieldName('PositiveSuffix', TableMap::TYPE_PHPNAME, $indexType)];
            $this->positive_suffix = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : CurrencyTableMap::translateFieldName('NegativePreffix', TableMap::TYPE_PHPNAME, $indexType)];
            $this->negative_preffix = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : CurrencyTableMap::translateFieldName('NegativeSuffix', TableMap::TYPE_PHPNAME, $indexType)];
            $this->negative_suffix = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 9 + $startcol : CurrencyTableMap::translateFieldName('DecimalCount', TableMap::TYPE_PHPNAME, $indexType)];
            $this->decimal_count = (null !== $col) ? (int) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 10; // 10 = CurrencyTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Gekosale\Plugin\Currency\Model\ORM\Currency object", 0, $e);
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
            $con = Propel::getServiceContainer()->getReadConnection(CurrencyTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildCurrencyQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collLocales = null;

            $this->collProductsRelatedByBuyCurrencyId = null;

            $this->collProductsRelatedBySellCurrencyId = null;

            $this->collShops = null;

            $this->collCurrencyShops = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Currency::setDeleted()
     * @see Currency::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(CurrencyTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildCurrencyQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(CurrencyTableMap::DATABASE_NAME);
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
                CurrencyTableMap::addInstanceToPool($this);
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

            if ($this->localesScheduledForDeletion !== null) {
                if (!$this->localesScheduledForDeletion->isEmpty()) {
                    foreach ($this->localesScheduledForDeletion as $locale) {
                        // need to save related object because we set the relation to null
                        $locale->save($con);
                    }
                    $this->localesScheduledForDeletion = null;
                }
            }

                if ($this->collLocales !== null) {
            foreach ($this->collLocales as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->productsRelatedByBuyCurrencyIdScheduledForDeletion !== null) {
                if (!$this->productsRelatedByBuyCurrencyIdScheduledForDeletion->isEmpty()) {
                    foreach ($this->productsRelatedByBuyCurrencyIdScheduledForDeletion as $productRelatedByBuyCurrencyId) {
                        // need to save related object because we set the relation to null
                        $productRelatedByBuyCurrencyId->save($con);
                    }
                    $this->productsRelatedByBuyCurrencyIdScheduledForDeletion = null;
                }
            }

                if ($this->collProductsRelatedByBuyCurrencyId !== null) {
            foreach ($this->collProductsRelatedByBuyCurrencyId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->productsRelatedBySellCurrencyIdScheduledForDeletion !== null) {
                if (!$this->productsRelatedBySellCurrencyIdScheduledForDeletion->isEmpty()) {
                    foreach ($this->productsRelatedBySellCurrencyIdScheduledForDeletion as $productRelatedBySellCurrencyId) {
                        // need to save related object because we set the relation to null
                        $productRelatedBySellCurrencyId->save($con);
                    }
                    $this->productsRelatedBySellCurrencyIdScheduledForDeletion = null;
                }
            }

                if ($this->collProductsRelatedBySellCurrencyId !== null) {
            foreach ($this->collProductsRelatedBySellCurrencyId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->shopsScheduledForDeletion !== null) {
                if (!$this->shopsScheduledForDeletion->isEmpty()) {
                    foreach ($this->shopsScheduledForDeletion as $shop) {
                        // need to save related object because we set the relation to null
                        $shop->save($con);
                    }
                    $this->shopsScheduledForDeletion = null;
                }
            }

                if ($this->collShops !== null) {
            foreach ($this->collShops as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->currencyShopsScheduledForDeletion !== null) {
                if (!$this->currencyShopsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Currency\Model\ORM\CurrencyShopQuery::create()
                        ->filterByPrimaryKeys($this->currencyShopsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->currencyShopsScheduledForDeletion = null;
                }
            }

                if ($this->collCurrencyShops !== null) {
            foreach ($this->collCurrencyShops as $referrerFK) {
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

        $this->modifiedColumns[CurrencyTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CurrencyTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CurrencyTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(CurrencyTableMap::COL_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'NAME';
        }
        if ($this->isColumnModified(CurrencyTableMap::COL_CURRENCY_SYMBOL)) {
            $modifiedColumns[':p' . $index++]  = 'CURRENCY_SYMBOL';
        }
        if ($this->isColumnModified(CurrencyTableMap::COL_DECIMAL_SEPARATOR)) {
            $modifiedColumns[':p' . $index++]  = 'DECIMAL_SEPARATOR';
        }
        if ($this->isColumnModified(CurrencyTableMap::COL_THOUSAND_SEPARATOR)) {
            $modifiedColumns[':p' . $index++]  = 'THOUSAND_SEPARATOR';
        }
        if ($this->isColumnModified(CurrencyTableMap::COL_POSITIVE_PREFFIX)) {
            $modifiedColumns[':p' . $index++]  = 'POSITIVE_PREFFIX';
        }
        if ($this->isColumnModified(CurrencyTableMap::COL_POSITIVE_SUFFIX)) {
            $modifiedColumns[':p' . $index++]  = 'POSITIVE_SUFFIX';
        }
        if ($this->isColumnModified(CurrencyTableMap::COL_NEGATIVE_PREFFIX)) {
            $modifiedColumns[':p' . $index++]  = 'NEGATIVE_PREFFIX';
        }
        if ($this->isColumnModified(CurrencyTableMap::COL_NEGATIVE_SUFFIX)) {
            $modifiedColumns[':p' . $index++]  = 'NEGATIVE_SUFFIX';
        }
        if ($this->isColumnModified(CurrencyTableMap::COL_DECIMAL_COUNT)) {
            $modifiedColumns[':p' . $index++]  = 'DECIMAL_COUNT';
        }

        $sql = sprintf(
            'INSERT INTO currency (%s) VALUES (%s)',
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
                    case 'CURRENCY_SYMBOL':                        
                        $stmt->bindValue($identifier, $this->currency_symbol, PDO::PARAM_STR);
                        break;
                    case 'DECIMAL_SEPARATOR':                        
                        $stmt->bindValue($identifier, $this->decimal_separator, PDO::PARAM_STR);
                        break;
                    case 'THOUSAND_SEPARATOR':                        
                        $stmt->bindValue($identifier, $this->thousand_separator, PDO::PARAM_STR);
                        break;
                    case 'POSITIVE_PREFFIX':                        
                        $stmt->bindValue($identifier, $this->positive_preffix, PDO::PARAM_STR);
                        break;
                    case 'POSITIVE_SUFFIX':                        
                        $stmt->bindValue($identifier, $this->positive_suffix, PDO::PARAM_STR);
                        break;
                    case 'NEGATIVE_PREFFIX':                        
                        $stmt->bindValue($identifier, $this->negative_preffix, PDO::PARAM_STR);
                        break;
                    case 'NEGATIVE_SUFFIX':                        
                        $stmt->bindValue($identifier, $this->negative_suffix, PDO::PARAM_STR);
                        break;
                    case 'DECIMAL_COUNT':                        
                        $stmt->bindValue($identifier, $this->decimal_count, PDO::PARAM_INT);
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
        $pos = CurrencyTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getCurrencySymbol();
                break;
            case 3:
                return $this->getDecimalSeparator();
                break;
            case 4:
                return $this->getThousandSeparator();
                break;
            case 5:
                return $this->getPositivePreffix();
                break;
            case 6:
                return $this->getPositiveSuffix();
                break;
            case 7:
                return $this->getNegativePreffix();
                break;
            case 8:
                return $this->getNegativeSuffix();
                break;
            case 9:
                return $this->getDecimalCount();
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
        if (isset($alreadyDumpedObjects['Currency'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Currency'][$this->getPrimaryKey()] = true;
        $keys = CurrencyTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getName(),
            $keys[2] => $this->getCurrencySymbol(),
            $keys[3] => $this->getDecimalSeparator(),
            $keys[4] => $this->getThousandSeparator(),
            $keys[5] => $this->getPositivePreffix(),
            $keys[6] => $this->getPositiveSuffix(),
            $keys[7] => $this->getNegativePreffix(),
            $keys[8] => $this->getNegativeSuffix(),
            $keys[9] => $this->getDecimalCount(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }
        
        if ($includeForeignObjects) {
            if (null !== $this->collLocales) {
                $result['Locales'] = $this->collLocales->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductsRelatedByBuyCurrencyId) {
                $result['ProductsRelatedByBuyCurrencyId'] = $this->collProductsRelatedByBuyCurrencyId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductsRelatedBySellCurrencyId) {
                $result['ProductsRelatedBySellCurrencyId'] = $this->collProductsRelatedBySellCurrencyId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collShops) {
                $result['Shops'] = $this->collShops->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCurrencyShops) {
                $result['CurrencyShops'] = $this->collCurrencyShops->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = CurrencyTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

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
                $this->setCurrencySymbol($value);
                break;
            case 3:
                $this->setDecimalSeparator($value);
                break;
            case 4:
                $this->setThousandSeparator($value);
                break;
            case 5:
                $this->setPositivePreffix($value);
                break;
            case 6:
                $this->setPositiveSuffix($value);
                break;
            case 7:
                $this->setNegativePreffix($value);
                break;
            case 8:
                $this->setNegativeSuffix($value);
                break;
            case 9:
                $this->setDecimalCount($value);
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
        $keys = CurrencyTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setName($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setCurrencySymbol($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setDecimalSeparator($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setThousandSeparator($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setPositivePreffix($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setPositiveSuffix($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setNegativePreffix($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setNegativeSuffix($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setDecimalCount($arr[$keys[9]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CurrencyTableMap::DATABASE_NAME);

        if ($this->isColumnModified(CurrencyTableMap::COL_ID)) $criteria->add(CurrencyTableMap::COL_ID, $this->id);
        if ($this->isColumnModified(CurrencyTableMap::COL_NAME)) $criteria->add(CurrencyTableMap::COL_NAME, $this->name);
        if ($this->isColumnModified(CurrencyTableMap::COL_CURRENCY_SYMBOL)) $criteria->add(CurrencyTableMap::COL_CURRENCY_SYMBOL, $this->currency_symbol);
        if ($this->isColumnModified(CurrencyTableMap::COL_DECIMAL_SEPARATOR)) $criteria->add(CurrencyTableMap::COL_DECIMAL_SEPARATOR, $this->decimal_separator);
        if ($this->isColumnModified(CurrencyTableMap::COL_THOUSAND_SEPARATOR)) $criteria->add(CurrencyTableMap::COL_THOUSAND_SEPARATOR, $this->thousand_separator);
        if ($this->isColumnModified(CurrencyTableMap::COL_POSITIVE_PREFFIX)) $criteria->add(CurrencyTableMap::COL_POSITIVE_PREFFIX, $this->positive_preffix);
        if ($this->isColumnModified(CurrencyTableMap::COL_POSITIVE_SUFFIX)) $criteria->add(CurrencyTableMap::COL_POSITIVE_SUFFIX, $this->positive_suffix);
        if ($this->isColumnModified(CurrencyTableMap::COL_NEGATIVE_PREFFIX)) $criteria->add(CurrencyTableMap::COL_NEGATIVE_PREFFIX, $this->negative_preffix);
        if ($this->isColumnModified(CurrencyTableMap::COL_NEGATIVE_SUFFIX)) $criteria->add(CurrencyTableMap::COL_NEGATIVE_SUFFIX, $this->negative_suffix);
        if ($this->isColumnModified(CurrencyTableMap::COL_DECIMAL_COUNT)) $criteria->add(CurrencyTableMap::COL_DECIMAL_COUNT, $this->decimal_count);

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
        $criteria = new Criteria(CurrencyTableMap::DATABASE_NAME);
        $criteria->add(CurrencyTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \Gekosale\Plugin\Currency\Model\ORM\Currency (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setName($this->getName());
        $copyObj->setCurrencySymbol($this->getCurrencySymbol());
        $copyObj->setDecimalSeparator($this->getDecimalSeparator());
        $copyObj->setThousandSeparator($this->getThousandSeparator());
        $copyObj->setPositivePreffix($this->getPositivePreffix());
        $copyObj->setPositiveSuffix($this->getPositiveSuffix());
        $copyObj->setNegativePreffix($this->getNegativePreffix());
        $copyObj->setNegativeSuffix($this->getNegativeSuffix());
        $copyObj->setDecimalCount($this->getDecimalCount());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getLocales() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addLocale($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductsRelatedByBuyCurrencyId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductRelatedByBuyCurrencyId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductsRelatedBySellCurrencyId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductRelatedBySellCurrencyId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getShops() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addShop($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCurrencyShops() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCurrencyShop($relObj->copy($deepCopy));
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
     * @return                 \Gekosale\Plugin\Currency\Model\ORM\Currency Clone of current object.
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
        if ('Locale' == $relationName) {
            return $this->initLocales();
        }
        if ('ProductRelatedByBuyCurrencyId' == $relationName) {
            return $this->initProductsRelatedByBuyCurrencyId();
        }
        if ('ProductRelatedBySellCurrencyId' == $relationName) {
            return $this->initProductsRelatedBySellCurrencyId();
        }
        if ('Shop' == $relationName) {
            return $this->initShops();
        }
        if ('CurrencyShop' == $relationName) {
            return $this->initCurrencyShops();
        }
    }

    /**
     * Clears out the collLocales collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addLocales()
     */
    public function clearLocales()
    {
        $this->collLocales = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collLocales collection loaded partially.
     */
    public function resetPartialLocales($v = true)
    {
        $this->collLocalesPartial = $v;
    }

    /**
     * Initializes the collLocales collection.
     *
     * By default this just sets the collLocales collection to an empty array (like clearcollLocales());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initLocales($overrideExisting = true)
    {
        if (null !== $this->collLocales && !$overrideExisting) {
            return;
        }
        $this->collLocales = new ObjectCollection();
        $this->collLocales->setModel('\Gekosale\Plugin\Locale\Model\ORM\Locale');
    }

    /**
     * Gets an array of ChildLocale objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCurrency is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildLocale[] List of ChildLocale objects
     * @throws PropelException
     */
    public function getLocales($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collLocalesPartial && !$this->isNew();
        if (null === $this->collLocales || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collLocales) {
                // return empty collection
                $this->initLocales();
            } else {
                $collLocales = LocaleQuery::create(null, $criteria)
                    ->filterByCurrency($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collLocalesPartial && count($collLocales)) {
                        $this->initLocales(false);

                        foreach ($collLocales as $obj) {
                            if (false == $this->collLocales->contains($obj)) {
                                $this->collLocales->append($obj);
                            }
                        }

                        $this->collLocalesPartial = true;
                    }

                    reset($collLocales);

                    return $collLocales;
                }

                if ($partial && $this->collLocales) {
                    foreach ($this->collLocales as $obj) {
                        if ($obj->isNew()) {
                            $collLocales[] = $obj;
                        }
                    }
                }

                $this->collLocales = $collLocales;
                $this->collLocalesPartial = false;
            }
        }

        return $this->collLocales;
    }

    /**
     * Sets a collection of Locale objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $locales A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildCurrency The current object (for fluent API support)
     */
    public function setLocales(Collection $locales, ConnectionInterface $con = null)
    {
        $localesToDelete = $this->getLocales(new Criteria(), $con)->diff($locales);

        
        $this->localesScheduledForDeletion = $localesToDelete;

        foreach ($localesToDelete as $localeRemoved) {
            $localeRemoved->setCurrency(null);
        }

        $this->collLocales = null;
        foreach ($locales as $locale) {
            $this->addLocale($locale);
        }

        $this->collLocales = $locales;
        $this->collLocalesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Locale objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Locale objects.
     * @throws PropelException
     */
    public function countLocales(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collLocalesPartial && !$this->isNew();
        if (null === $this->collLocales || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collLocales) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getLocales());
            }

            $query = LocaleQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCurrency($this)
                ->count($con);
        }

        return count($this->collLocales);
    }

    /**
     * Method called to associate a ChildLocale object to this object
     * through the ChildLocale foreign key attribute.
     *
     * @param    ChildLocale $l ChildLocale
     * @return   \Gekosale\Plugin\Currency\Model\ORM\Currency The current object (for fluent API support)
     */
    public function addLocale(ChildLocale $l)
    {
        if ($this->collLocales === null) {
            $this->initLocales();
            $this->collLocalesPartial = true;
        }

        if (!in_array($l, $this->collLocales->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddLocale($l);
        }

        return $this;
    }

    /**
     * @param Locale $locale The locale object to add.
     */
    protected function doAddLocale($locale)
    {
        $this->collLocales[]= $locale;
        $locale->setCurrency($this);
    }

    /**
     * @param  Locale $locale The locale object to remove.
     * @return ChildCurrency The current object (for fluent API support)
     */
    public function removeLocale($locale)
    {
        if ($this->getLocales()->contains($locale)) {
            $this->collLocales->remove($this->collLocales->search($locale));
            if (null === $this->localesScheduledForDeletion) {
                $this->localesScheduledForDeletion = clone $this->collLocales;
                $this->localesScheduledForDeletion->clear();
            }
            $this->localesScheduledForDeletion[]= $locale;
            $locale->setCurrency(null);
        }

        return $this;
    }

    /**
     * Clears out the collProductsRelatedByBuyCurrencyId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addProductsRelatedByBuyCurrencyId()
     */
    public function clearProductsRelatedByBuyCurrencyId()
    {
        $this->collProductsRelatedByBuyCurrencyId = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collProductsRelatedByBuyCurrencyId collection loaded partially.
     */
    public function resetPartialProductsRelatedByBuyCurrencyId($v = true)
    {
        $this->collProductsRelatedByBuyCurrencyIdPartial = $v;
    }

    /**
     * Initializes the collProductsRelatedByBuyCurrencyId collection.
     *
     * By default this just sets the collProductsRelatedByBuyCurrencyId collection to an empty array (like clearcollProductsRelatedByBuyCurrencyId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductsRelatedByBuyCurrencyId($overrideExisting = true)
    {
        if (null !== $this->collProductsRelatedByBuyCurrencyId && !$overrideExisting) {
            return;
        }
        $this->collProductsRelatedByBuyCurrencyId = new ObjectCollection();
        $this->collProductsRelatedByBuyCurrencyId->setModel('\Gekosale\Plugin\Product\Model\ORM\Product');
    }

    /**
     * Gets an array of ChildProduct objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCurrency is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildProduct[] List of ChildProduct objects
     * @throws PropelException
     */
    public function getProductsRelatedByBuyCurrencyId($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collProductsRelatedByBuyCurrencyIdPartial && !$this->isNew();
        if (null === $this->collProductsRelatedByBuyCurrencyId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductsRelatedByBuyCurrencyId) {
                // return empty collection
                $this->initProductsRelatedByBuyCurrencyId();
            } else {
                $collProductsRelatedByBuyCurrencyId = ProductQuery::create(null, $criteria)
                    ->filterByCurrencyRelatedByBuyCurrencyId($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collProductsRelatedByBuyCurrencyIdPartial && count($collProductsRelatedByBuyCurrencyId)) {
                        $this->initProductsRelatedByBuyCurrencyId(false);

                        foreach ($collProductsRelatedByBuyCurrencyId as $obj) {
                            if (false == $this->collProductsRelatedByBuyCurrencyId->contains($obj)) {
                                $this->collProductsRelatedByBuyCurrencyId->append($obj);
                            }
                        }

                        $this->collProductsRelatedByBuyCurrencyIdPartial = true;
                    }

                    reset($collProductsRelatedByBuyCurrencyId);

                    return $collProductsRelatedByBuyCurrencyId;
                }

                if ($partial && $this->collProductsRelatedByBuyCurrencyId) {
                    foreach ($this->collProductsRelatedByBuyCurrencyId as $obj) {
                        if ($obj->isNew()) {
                            $collProductsRelatedByBuyCurrencyId[] = $obj;
                        }
                    }
                }

                $this->collProductsRelatedByBuyCurrencyId = $collProductsRelatedByBuyCurrencyId;
                $this->collProductsRelatedByBuyCurrencyIdPartial = false;
            }
        }

        return $this->collProductsRelatedByBuyCurrencyId;
    }

    /**
     * Sets a collection of ProductRelatedByBuyCurrencyId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $productsRelatedByBuyCurrencyId A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildCurrency The current object (for fluent API support)
     */
    public function setProductsRelatedByBuyCurrencyId(Collection $productsRelatedByBuyCurrencyId, ConnectionInterface $con = null)
    {
        $productsRelatedByBuyCurrencyIdToDelete = $this->getProductsRelatedByBuyCurrencyId(new Criteria(), $con)->diff($productsRelatedByBuyCurrencyId);

        
        $this->productsRelatedByBuyCurrencyIdScheduledForDeletion = $productsRelatedByBuyCurrencyIdToDelete;

        foreach ($productsRelatedByBuyCurrencyIdToDelete as $productRelatedByBuyCurrencyIdRemoved) {
            $productRelatedByBuyCurrencyIdRemoved->setCurrencyRelatedByBuyCurrencyId(null);
        }

        $this->collProductsRelatedByBuyCurrencyId = null;
        foreach ($productsRelatedByBuyCurrencyId as $productRelatedByBuyCurrencyId) {
            $this->addProductRelatedByBuyCurrencyId($productRelatedByBuyCurrencyId);
        }

        $this->collProductsRelatedByBuyCurrencyId = $productsRelatedByBuyCurrencyId;
        $this->collProductsRelatedByBuyCurrencyIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Product objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Product objects.
     * @throws PropelException
     */
    public function countProductsRelatedByBuyCurrencyId(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collProductsRelatedByBuyCurrencyIdPartial && !$this->isNew();
        if (null === $this->collProductsRelatedByBuyCurrencyId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductsRelatedByBuyCurrencyId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProductsRelatedByBuyCurrencyId());
            }

            $query = ProductQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCurrencyRelatedByBuyCurrencyId($this)
                ->count($con);
        }

        return count($this->collProductsRelatedByBuyCurrencyId);
    }

    /**
     * Method called to associate a ChildProduct object to this object
     * through the ChildProduct foreign key attribute.
     *
     * @param    ChildProduct $l ChildProduct
     * @return   \Gekosale\Plugin\Currency\Model\ORM\Currency The current object (for fluent API support)
     */
    public function addProductRelatedByBuyCurrencyId(ChildProduct $l)
    {
        if ($this->collProductsRelatedByBuyCurrencyId === null) {
            $this->initProductsRelatedByBuyCurrencyId();
            $this->collProductsRelatedByBuyCurrencyIdPartial = true;
        }

        if (!in_array($l, $this->collProductsRelatedByBuyCurrencyId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductRelatedByBuyCurrencyId($l);
        }

        return $this;
    }

    /**
     * @param ProductRelatedByBuyCurrencyId $productRelatedByBuyCurrencyId The productRelatedByBuyCurrencyId object to add.
     */
    protected function doAddProductRelatedByBuyCurrencyId($productRelatedByBuyCurrencyId)
    {
        $this->collProductsRelatedByBuyCurrencyId[]= $productRelatedByBuyCurrencyId;
        $productRelatedByBuyCurrencyId->setCurrencyRelatedByBuyCurrencyId($this);
    }

    /**
     * @param  ProductRelatedByBuyCurrencyId $productRelatedByBuyCurrencyId The productRelatedByBuyCurrencyId object to remove.
     * @return ChildCurrency The current object (for fluent API support)
     */
    public function removeProductRelatedByBuyCurrencyId($productRelatedByBuyCurrencyId)
    {
        if ($this->getProductsRelatedByBuyCurrencyId()->contains($productRelatedByBuyCurrencyId)) {
            $this->collProductsRelatedByBuyCurrencyId->remove($this->collProductsRelatedByBuyCurrencyId->search($productRelatedByBuyCurrencyId));
            if (null === $this->productsRelatedByBuyCurrencyIdScheduledForDeletion) {
                $this->productsRelatedByBuyCurrencyIdScheduledForDeletion = clone $this->collProductsRelatedByBuyCurrencyId;
                $this->productsRelatedByBuyCurrencyIdScheduledForDeletion->clear();
            }
            $this->productsRelatedByBuyCurrencyIdScheduledForDeletion[]= $productRelatedByBuyCurrencyId;
            $productRelatedByBuyCurrencyId->setCurrencyRelatedByBuyCurrencyId(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Currency is new, it will return
     * an empty collection; or if this Currency has previously
     * been saved, it will retrieve related ProductsRelatedByBuyCurrencyId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Currency.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProduct[] List of ChildProduct objects
     */
    public function getProductsRelatedByBuyCurrencyIdJoinAvailability($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ProductQuery::create(null, $criteria);
        $query->joinWith('Availability', $joinBehavior);

        return $this->getProductsRelatedByBuyCurrencyId($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Currency is new, it will return
     * an empty collection; or if this Currency has previously
     * been saved, it will retrieve related ProductsRelatedByBuyCurrencyId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Currency.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProduct[] List of ChildProduct objects
     */
    public function getProductsRelatedByBuyCurrencyIdJoinProducer($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ProductQuery::create(null, $criteria);
        $query->joinWith('Producer', $joinBehavior);

        return $this->getProductsRelatedByBuyCurrencyId($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Currency is new, it will return
     * an empty collection; or if this Currency has previously
     * been saved, it will retrieve related ProductsRelatedByBuyCurrencyId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Currency.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProduct[] List of ChildProduct objects
     */
    public function getProductsRelatedByBuyCurrencyIdJoinUnitMeasure($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ProductQuery::create(null, $criteria);
        $query->joinWith('UnitMeasure', $joinBehavior);

        return $this->getProductsRelatedByBuyCurrencyId($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Currency is new, it will return
     * an empty collection; or if this Currency has previously
     * been saved, it will retrieve related ProductsRelatedByBuyCurrencyId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Currency.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProduct[] List of ChildProduct objects
     */
    public function getProductsRelatedByBuyCurrencyIdJoinVat($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ProductQuery::create(null, $criteria);
        $query->joinWith('Vat', $joinBehavior);

        return $this->getProductsRelatedByBuyCurrencyId($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Currency is new, it will return
     * an empty collection; or if this Currency has previously
     * been saved, it will retrieve related ProductsRelatedByBuyCurrencyId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Currency.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProduct[] List of ChildProduct objects
     */
    public function getProductsRelatedByBuyCurrencyIdJoinTechnicalDataSet($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ProductQuery::create(null, $criteria);
        $query->joinWith('TechnicalDataSet', $joinBehavior);

        return $this->getProductsRelatedByBuyCurrencyId($query, $con);
    }

    /**
     * Clears out the collProductsRelatedBySellCurrencyId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addProductsRelatedBySellCurrencyId()
     */
    public function clearProductsRelatedBySellCurrencyId()
    {
        $this->collProductsRelatedBySellCurrencyId = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collProductsRelatedBySellCurrencyId collection loaded partially.
     */
    public function resetPartialProductsRelatedBySellCurrencyId($v = true)
    {
        $this->collProductsRelatedBySellCurrencyIdPartial = $v;
    }

    /**
     * Initializes the collProductsRelatedBySellCurrencyId collection.
     *
     * By default this just sets the collProductsRelatedBySellCurrencyId collection to an empty array (like clearcollProductsRelatedBySellCurrencyId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductsRelatedBySellCurrencyId($overrideExisting = true)
    {
        if (null !== $this->collProductsRelatedBySellCurrencyId && !$overrideExisting) {
            return;
        }
        $this->collProductsRelatedBySellCurrencyId = new ObjectCollection();
        $this->collProductsRelatedBySellCurrencyId->setModel('\Gekosale\Plugin\Product\Model\ORM\Product');
    }

    /**
     * Gets an array of ChildProduct objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCurrency is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildProduct[] List of ChildProduct objects
     * @throws PropelException
     */
    public function getProductsRelatedBySellCurrencyId($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collProductsRelatedBySellCurrencyIdPartial && !$this->isNew();
        if (null === $this->collProductsRelatedBySellCurrencyId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductsRelatedBySellCurrencyId) {
                // return empty collection
                $this->initProductsRelatedBySellCurrencyId();
            } else {
                $collProductsRelatedBySellCurrencyId = ProductQuery::create(null, $criteria)
                    ->filterByCurrencyRelatedBySellCurrencyId($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collProductsRelatedBySellCurrencyIdPartial && count($collProductsRelatedBySellCurrencyId)) {
                        $this->initProductsRelatedBySellCurrencyId(false);

                        foreach ($collProductsRelatedBySellCurrencyId as $obj) {
                            if (false == $this->collProductsRelatedBySellCurrencyId->contains($obj)) {
                                $this->collProductsRelatedBySellCurrencyId->append($obj);
                            }
                        }

                        $this->collProductsRelatedBySellCurrencyIdPartial = true;
                    }

                    reset($collProductsRelatedBySellCurrencyId);

                    return $collProductsRelatedBySellCurrencyId;
                }

                if ($partial && $this->collProductsRelatedBySellCurrencyId) {
                    foreach ($this->collProductsRelatedBySellCurrencyId as $obj) {
                        if ($obj->isNew()) {
                            $collProductsRelatedBySellCurrencyId[] = $obj;
                        }
                    }
                }

                $this->collProductsRelatedBySellCurrencyId = $collProductsRelatedBySellCurrencyId;
                $this->collProductsRelatedBySellCurrencyIdPartial = false;
            }
        }

        return $this->collProductsRelatedBySellCurrencyId;
    }

    /**
     * Sets a collection of ProductRelatedBySellCurrencyId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $productsRelatedBySellCurrencyId A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildCurrency The current object (for fluent API support)
     */
    public function setProductsRelatedBySellCurrencyId(Collection $productsRelatedBySellCurrencyId, ConnectionInterface $con = null)
    {
        $productsRelatedBySellCurrencyIdToDelete = $this->getProductsRelatedBySellCurrencyId(new Criteria(), $con)->diff($productsRelatedBySellCurrencyId);

        
        $this->productsRelatedBySellCurrencyIdScheduledForDeletion = $productsRelatedBySellCurrencyIdToDelete;

        foreach ($productsRelatedBySellCurrencyIdToDelete as $productRelatedBySellCurrencyIdRemoved) {
            $productRelatedBySellCurrencyIdRemoved->setCurrencyRelatedBySellCurrencyId(null);
        }

        $this->collProductsRelatedBySellCurrencyId = null;
        foreach ($productsRelatedBySellCurrencyId as $productRelatedBySellCurrencyId) {
            $this->addProductRelatedBySellCurrencyId($productRelatedBySellCurrencyId);
        }

        $this->collProductsRelatedBySellCurrencyId = $productsRelatedBySellCurrencyId;
        $this->collProductsRelatedBySellCurrencyIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Product objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Product objects.
     * @throws PropelException
     */
    public function countProductsRelatedBySellCurrencyId(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collProductsRelatedBySellCurrencyIdPartial && !$this->isNew();
        if (null === $this->collProductsRelatedBySellCurrencyId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductsRelatedBySellCurrencyId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProductsRelatedBySellCurrencyId());
            }

            $query = ProductQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCurrencyRelatedBySellCurrencyId($this)
                ->count($con);
        }

        return count($this->collProductsRelatedBySellCurrencyId);
    }

    /**
     * Method called to associate a ChildProduct object to this object
     * through the ChildProduct foreign key attribute.
     *
     * @param    ChildProduct $l ChildProduct
     * @return   \Gekosale\Plugin\Currency\Model\ORM\Currency The current object (for fluent API support)
     */
    public function addProductRelatedBySellCurrencyId(ChildProduct $l)
    {
        if ($this->collProductsRelatedBySellCurrencyId === null) {
            $this->initProductsRelatedBySellCurrencyId();
            $this->collProductsRelatedBySellCurrencyIdPartial = true;
        }

        if (!in_array($l, $this->collProductsRelatedBySellCurrencyId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductRelatedBySellCurrencyId($l);
        }

        return $this;
    }

    /**
     * @param ProductRelatedBySellCurrencyId $productRelatedBySellCurrencyId The productRelatedBySellCurrencyId object to add.
     */
    protected function doAddProductRelatedBySellCurrencyId($productRelatedBySellCurrencyId)
    {
        $this->collProductsRelatedBySellCurrencyId[]= $productRelatedBySellCurrencyId;
        $productRelatedBySellCurrencyId->setCurrencyRelatedBySellCurrencyId($this);
    }

    /**
     * @param  ProductRelatedBySellCurrencyId $productRelatedBySellCurrencyId The productRelatedBySellCurrencyId object to remove.
     * @return ChildCurrency The current object (for fluent API support)
     */
    public function removeProductRelatedBySellCurrencyId($productRelatedBySellCurrencyId)
    {
        if ($this->getProductsRelatedBySellCurrencyId()->contains($productRelatedBySellCurrencyId)) {
            $this->collProductsRelatedBySellCurrencyId->remove($this->collProductsRelatedBySellCurrencyId->search($productRelatedBySellCurrencyId));
            if (null === $this->productsRelatedBySellCurrencyIdScheduledForDeletion) {
                $this->productsRelatedBySellCurrencyIdScheduledForDeletion = clone $this->collProductsRelatedBySellCurrencyId;
                $this->productsRelatedBySellCurrencyIdScheduledForDeletion->clear();
            }
            $this->productsRelatedBySellCurrencyIdScheduledForDeletion[]= $productRelatedBySellCurrencyId;
            $productRelatedBySellCurrencyId->setCurrencyRelatedBySellCurrencyId(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Currency is new, it will return
     * an empty collection; or if this Currency has previously
     * been saved, it will retrieve related ProductsRelatedBySellCurrencyId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Currency.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProduct[] List of ChildProduct objects
     */
    public function getProductsRelatedBySellCurrencyIdJoinAvailability($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ProductQuery::create(null, $criteria);
        $query->joinWith('Availability', $joinBehavior);

        return $this->getProductsRelatedBySellCurrencyId($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Currency is new, it will return
     * an empty collection; or if this Currency has previously
     * been saved, it will retrieve related ProductsRelatedBySellCurrencyId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Currency.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProduct[] List of ChildProduct objects
     */
    public function getProductsRelatedBySellCurrencyIdJoinProducer($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ProductQuery::create(null, $criteria);
        $query->joinWith('Producer', $joinBehavior);

        return $this->getProductsRelatedBySellCurrencyId($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Currency is new, it will return
     * an empty collection; or if this Currency has previously
     * been saved, it will retrieve related ProductsRelatedBySellCurrencyId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Currency.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProduct[] List of ChildProduct objects
     */
    public function getProductsRelatedBySellCurrencyIdJoinUnitMeasure($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ProductQuery::create(null, $criteria);
        $query->joinWith('UnitMeasure', $joinBehavior);

        return $this->getProductsRelatedBySellCurrencyId($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Currency is new, it will return
     * an empty collection; or if this Currency has previously
     * been saved, it will retrieve related ProductsRelatedBySellCurrencyId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Currency.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProduct[] List of ChildProduct objects
     */
    public function getProductsRelatedBySellCurrencyIdJoinVat($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ProductQuery::create(null, $criteria);
        $query->joinWith('Vat', $joinBehavior);

        return $this->getProductsRelatedBySellCurrencyId($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Currency is new, it will return
     * an empty collection; or if this Currency has previously
     * been saved, it will retrieve related ProductsRelatedBySellCurrencyId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Currency.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProduct[] List of ChildProduct objects
     */
    public function getProductsRelatedBySellCurrencyIdJoinTechnicalDataSet($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ProductQuery::create(null, $criteria);
        $query->joinWith('TechnicalDataSet', $joinBehavior);

        return $this->getProductsRelatedBySellCurrencyId($query, $con);
    }

    /**
     * Clears out the collShops collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addShops()
     */
    public function clearShops()
    {
        $this->collShops = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collShops collection loaded partially.
     */
    public function resetPartialShops($v = true)
    {
        $this->collShopsPartial = $v;
    }

    /**
     * Initializes the collShops collection.
     *
     * By default this just sets the collShops collection to an empty array (like clearcollShops());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initShops($overrideExisting = true)
    {
        if (null !== $this->collShops && !$overrideExisting) {
            return;
        }
        $this->collShops = new ObjectCollection();
        $this->collShops->setModel('\Gekosale\Plugin\Shop\Model\ORM\Shop');
    }

    /**
     * Gets an array of ChildShop objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCurrency is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildShop[] List of ChildShop objects
     * @throws PropelException
     */
    public function getShops($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collShopsPartial && !$this->isNew();
        if (null === $this->collShops || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collShops) {
                // return empty collection
                $this->initShops();
            } else {
                $collShops = ShopQuery::create(null, $criteria)
                    ->filterByCurrency($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collShopsPartial && count($collShops)) {
                        $this->initShops(false);

                        foreach ($collShops as $obj) {
                            if (false == $this->collShops->contains($obj)) {
                                $this->collShops->append($obj);
                            }
                        }

                        $this->collShopsPartial = true;
                    }

                    reset($collShops);

                    return $collShops;
                }

                if ($partial && $this->collShops) {
                    foreach ($this->collShops as $obj) {
                        if ($obj->isNew()) {
                            $collShops[] = $obj;
                        }
                    }
                }

                $this->collShops = $collShops;
                $this->collShopsPartial = false;
            }
        }

        return $this->collShops;
    }

    /**
     * Sets a collection of Shop objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $shops A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildCurrency The current object (for fluent API support)
     */
    public function setShops(Collection $shops, ConnectionInterface $con = null)
    {
        $shopsToDelete = $this->getShops(new Criteria(), $con)->diff($shops);

        
        $this->shopsScheduledForDeletion = $shopsToDelete;

        foreach ($shopsToDelete as $shopRemoved) {
            $shopRemoved->setCurrency(null);
        }

        $this->collShops = null;
        foreach ($shops as $shop) {
            $this->addShop($shop);
        }

        $this->collShops = $shops;
        $this->collShopsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Shop objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Shop objects.
     * @throws PropelException
     */
    public function countShops(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collShopsPartial && !$this->isNew();
        if (null === $this->collShops || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collShops) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getShops());
            }

            $query = ShopQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCurrency($this)
                ->count($con);
        }

        return count($this->collShops);
    }

    /**
     * Method called to associate a ChildShop object to this object
     * through the ChildShop foreign key attribute.
     *
     * @param    ChildShop $l ChildShop
     * @return   \Gekosale\Plugin\Currency\Model\ORM\Currency The current object (for fluent API support)
     */
    public function addShop(ChildShop $l)
    {
        if ($this->collShops === null) {
            $this->initShops();
            $this->collShopsPartial = true;
        }

        if (!in_array($l, $this->collShops->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddShop($l);
        }

        return $this;
    }

    /**
     * @param Shop $shop The shop object to add.
     */
    protected function doAddShop($shop)
    {
        $this->collShops[]= $shop;
        $shop->setCurrency($this);
    }

    /**
     * @param  Shop $shop The shop object to remove.
     * @return ChildCurrency The current object (for fluent API support)
     */
    public function removeShop($shop)
    {
        if ($this->getShops()->contains($shop)) {
            $this->collShops->remove($this->collShops->search($shop));
            if (null === $this->shopsScheduledForDeletion) {
                $this->shopsScheduledForDeletion = clone $this->collShops;
                $this->shopsScheduledForDeletion->clear();
            }
            $this->shopsScheduledForDeletion[]= $shop;
            $shop->setCurrency(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Currency is new, it will return
     * an empty collection; or if this Currency has previously
     * been saved, it will retrieve related Shops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Currency.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildShop[] List of ChildShop objects
     */
    public function getShopsJoinContact($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ShopQuery::create(null, $criteria);
        $query->joinWith('Contact', $joinBehavior);

        return $this->getShops($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Currency is new, it will return
     * an empty collection; or if this Currency has previously
     * been saved, it will retrieve related Shops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Currency.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildShop[] List of ChildShop objects
     */
    public function getShopsJoinVat($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ShopQuery::create(null, $criteria);
        $query->joinWith('Vat', $joinBehavior);

        return $this->getShops($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Currency is new, it will return
     * an empty collection; or if this Currency has previously
     * been saved, it will retrieve related Shops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Currency.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildShop[] List of ChildShop objects
     */
    public function getShopsJoinOrderStatusGroups($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ShopQuery::create(null, $criteria);
        $query->joinWith('OrderStatusGroups', $joinBehavior);

        return $this->getShops($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Currency is new, it will return
     * an empty collection; or if this Currency has previously
     * been saved, it will retrieve related Shops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Currency.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildShop[] List of ChildShop objects
     */
    public function getShopsJoinCompany($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ShopQuery::create(null, $criteria);
        $query->joinWith('Company', $joinBehavior);

        return $this->getShops($query, $con);
    }

    /**
     * Clears out the collCurrencyShops collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCurrencyShops()
     */
    public function clearCurrencyShops()
    {
        $this->collCurrencyShops = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCurrencyShops collection loaded partially.
     */
    public function resetPartialCurrencyShops($v = true)
    {
        $this->collCurrencyShopsPartial = $v;
    }

    /**
     * Initializes the collCurrencyShops collection.
     *
     * By default this just sets the collCurrencyShops collection to an empty array (like clearcollCurrencyShops());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCurrencyShops($overrideExisting = true)
    {
        if (null !== $this->collCurrencyShops && !$overrideExisting) {
            return;
        }
        $this->collCurrencyShops = new ObjectCollection();
        $this->collCurrencyShops->setModel('\Gekosale\Plugin\Currency\Model\ORM\CurrencyShop');
    }

    /**
     * Gets an array of ChildCurrencyShop objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCurrency is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildCurrencyShop[] List of ChildCurrencyShop objects
     * @throws PropelException
     */
    public function getCurrencyShops($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCurrencyShopsPartial && !$this->isNew();
        if (null === $this->collCurrencyShops || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCurrencyShops) {
                // return empty collection
                $this->initCurrencyShops();
            } else {
                $collCurrencyShops = ChildCurrencyShopQuery::create(null, $criteria)
                    ->filterByCurrency($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCurrencyShopsPartial && count($collCurrencyShops)) {
                        $this->initCurrencyShops(false);

                        foreach ($collCurrencyShops as $obj) {
                            if (false == $this->collCurrencyShops->contains($obj)) {
                                $this->collCurrencyShops->append($obj);
                            }
                        }

                        $this->collCurrencyShopsPartial = true;
                    }

                    reset($collCurrencyShops);

                    return $collCurrencyShops;
                }

                if ($partial && $this->collCurrencyShops) {
                    foreach ($this->collCurrencyShops as $obj) {
                        if ($obj->isNew()) {
                            $collCurrencyShops[] = $obj;
                        }
                    }
                }

                $this->collCurrencyShops = $collCurrencyShops;
                $this->collCurrencyShopsPartial = false;
            }
        }

        return $this->collCurrencyShops;
    }

    /**
     * Sets a collection of CurrencyShop objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $currencyShops A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildCurrency The current object (for fluent API support)
     */
    public function setCurrencyShops(Collection $currencyShops, ConnectionInterface $con = null)
    {
        $currencyShopsToDelete = $this->getCurrencyShops(new Criteria(), $con)->diff($currencyShops);

        
        $this->currencyShopsScheduledForDeletion = $currencyShopsToDelete;

        foreach ($currencyShopsToDelete as $currencyShopRemoved) {
            $currencyShopRemoved->setCurrency(null);
        }

        $this->collCurrencyShops = null;
        foreach ($currencyShops as $currencyShop) {
            $this->addCurrencyShop($currencyShop);
        }

        $this->collCurrencyShops = $currencyShops;
        $this->collCurrencyShopsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CurrencyShop objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related CurrencyShop objects.
     * @throws PropelException
     */
    public function countCurrencyShops(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCurrencyShopsPartial && !$this->isNew();
        if (null === $this->collCurrencyShops || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCurrencyShops) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCurrencyShops());
            }

            $query = ChildCurrencyShopQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCurrency($this)
                ->count($con);
        }

        return count($this->collCurrencyShops);
    }

    /**
     * Method called to associate a ChildCurrencyShop object to this object
     * through the ChildCurrencyShop foreign key attribute.
     *
     * @param    ChildCurrencyShop $l ChildCurrencyShop
     * @return   \Gekosale\Plugin\Currency\Model\ORM\Currency The current object (for fluent API support)
     */
    public function addCurrencyShop(ChildCurrencyShop $l)
    {
        if ($this->collCurrencyShops === null) {
            $this->initCurrencyShops();
            $this->collCurrencyShopsPartial = true;
        }

        if (!in_array($l, $this->collCurrencyShops->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCurrencyShop($l);
        }

        return $this;
    }

    /**
     * @param CurrencyShop $currencyShop The currencyShop object to add.
     */
    protected function doAddCurrencyShop($currencyShop)
    {
        $this->collCurrencyShops[]= $currencyShop;
        $currencyShop->setCurrency($this);
    }

    /**
     * @param  CurrencyShop $currencyShop The currencyShop object to remove.
     * @return ChildCurrency The current object (for fluent API support)
     */
    public function removeCurrencyShop($currencyShop)
    {
        if ($this->getCurrencyShops()->contains($currencyShop)) {
            $this->collCurrencyShops->remove($this->collCurrencyShops->search($currencyShop));
            if (null === $this->currencyShopsScheduledForDeletion) {
                $this->currencyShopsScheduledForDeletion = clone $this->collCurrencyShops;
                $this->currencyShopsScheduledForDeletion->clear();
            }
            $this->currencyShopsScheduledForDeletion[]= clone $currencyShop;
            $currencyShop->setCurrency(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Currency is new, it will return
     * an empty collection; or if this Currency has previously
     * been saved, it will retrieve related CurrencyShops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Currency.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCurrencyShop[] List of ChildCurrencyShop objects
     */
    public function getCurrencyShopsJoinShop($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCurrencyShopQuery::create(null, $criteria);
        $query->joinWith('Shop', $joinBehavior);

        return $this->getCurrencyShops($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->name = null;
        $this->currency_symbol = null;
        $this->decimal_separator = null;
        $this->thousand_separator = null;
        $this->positive_preffix = null;
        $this->positive_suffix = null;
        $this->negative_preffix = null;
        $this->negative_suffix = null;
        $this->decimal_count = null;
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
            if ($this->collLocales) {
                foreach ($this->collLocales as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductsRelatedByBuyCurrencyId) {
                foreach ($this->collProductsRelatedByBuyCurrencyId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductsRelatedBySellCurrencyId) {
                foreach ($this->collProductsRelatedBySellCurrencyId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collShops) {
                foreach ($this->collShops as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCurrencyShops) {
                foreach ($this->collCurrencyShops as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collLocales = null;
        $this->collProductsRelatedByBuyCurrencyId = null;
        $this->collProductsRelatedBySellCurrencyId = null;
        $this->collShops = null;
        $this->collCurrencyShops = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CurrencyTableMap::DEFAULT_STRING_FORMAT);
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
