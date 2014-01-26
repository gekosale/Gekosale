<?php

namespace Gekosale\Component\Configuration\Model\Currency\Base;

use \Exception;
use \PDO;
use Gekosale\Component\Configuration\Model\Currency\CurrencyQuery as ChildCurrencyQuery;
use Gekosale\Component\Configuration\Model\Currency\Map\CurrencyTableMap;
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

abstract class Currency implements ActiveRecordInterface 
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Gekosale\\Component\\Configuration\\Model\\Currency\\Map\\CurrencyTableMap';


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
     * The value for the symbol field.
     * @var        string
     */
    protected $symbol;

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
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * Initializes internal state of Gekosale\Component\Configuration\Model\Currency\Base\Currency object.
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
     * Get the [symbol] column value.
     * 
     * @return   string
     */
    public function getSymbol()
    {

        return $this->symbol;
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
     * @return   \Gekosale\Component\Configuration\Model\Currency\Currency The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[CurrencyTableMap::ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [name] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Component\Configuration\Model\Currency\Currency The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[CurrencyTableMap::NAME] = true;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [symbol] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Component\Configuration\Model\Currency\Currency The current object (for fluent API support)
     */
    public function setSymbol($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->symbol !== $v) {
            $this->symbol = $v;
            $this->modifiedColumns[CurrencyTableMap::SYMBOL] = true;
        }


        return $this;
    } // setSymbol()

    /**
     * Set the value of [decimal_separator] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Component\Configuration\Model\Currency\Currency The current object (for fluent API support)
     */
    public function setDecimalSeparator($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->decimal_separator !== $v) {
            $this->decimal_separator = $v;
            $this->modifiedColumns[CurrencyTableMap::DECIMAL_SEPARATOR] = true;
        }


        return $this;
    } // setDecimalSeparator()

    /**
     * Set the value of [thousand_separator] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Component\Configuration\Model\Currency\Currency The current object (for fluent API support)
     */
    public function setThousandSeparator($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->thousand_separator !== $v) {
            $this->thousand_separator = $v;
            $this->modifiedColumns[CurrencyTableMap::THOUSAND_SEPARATOR] = true;
        }


        return $this;
    } // setThousandSeparator()

    /**
     * Set the value of [positive_preffix] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Component\Configuration\Model\Currency\Currency The current object (for fluent API support)
     */
    public function setPositivePreffix($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->positive_preffix !== $v) {
            $this->positive_preffix = $v;
            $this->modifiedColumns[CurrencyTableMap::POSITIVE_PREFFIX] = true;
        }


        return $this;
    } // setPositivePreffix()

    /**
     * Set the value of [positive_suffix] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Component\Configuration\Model\Currency\Currency The current object (for fluent API support)
     */
    public function setPositiveSuffix($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->positive_suffix !== $v) {
            $this->positive_suffix = $v;
            $this->modifiedColumns[CurrencyTableMap::POSITIVE_SUFFIX] = true;
        }


        return $this;
    } // setPositiveSuffix()

    /**
     * Set the value of [negative_preffix] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Component\Configuration\Model\Currency\Currency The current object (for fluent API support)
     */
    public function setNegativePreffix($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->negative_preffix !== $v) {
            $this->negative_preffix = $v;
            $this->modifiedColumns[CurrencyTableMap::NEGATIVE_PREFFIX] = true;
        }


        return $this;
    } // setNegativePreffix()

    /**
     * Set the value of [negative_suffix] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Component\Configuration\Model\Currency\Currency The current object (for fluent API support)
     */
    public function setNegativeSuffix($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->negative_suffix !== $v) {
            $this->negative_suffix = $v;
            $this->modifiedColumns[CurrencyTableMap::NEGATIVE_SUFFIX] = true;
        }


        return $this;
    } // setNegativeSuffix()

    /**
     * Set the value of [decimal_count] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Component\Configuration\Model\Currency\Currency The current object (for fluent API support)
     */
    public function setDecimalCount($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->decimal_count !== $v) {
            $this->decimal_count = $v;
            $this->modifiedColumns[CurrencyTableMap::DECIMAL_COUNT] = true;
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

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : CurrencyTableMap::translateFieldName('Symbol', TableMap::TYPE_PHPNAME, $indexType)];
            $this->symbol = (null !== $col) ? (string) $col : null;

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
            throw new PropelException("Error populating \Gekosale\Component\Configuration\Model\Currency\Currency object", 0, $e);
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

        $this->modifiedColumns[CurrencyTableMap::ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CurrencyTableMap::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CurrencyTableMap::ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(CurrencyTableMap::NAME)) {
            $modifiedColumns[':p' . $index++]  = 'NAME';
        }
        if ($this->isColumnModified(CurrencyTableMap::SYMBOL)) {
            $modifiedColumns[':p' . $index++]  = 'SYMBOL';
        }
        if ($this->isColumnModified(CurrencyTableMap::DECIMAL_SEPARATOR)) {
            $modifiedColumns[':p' . $index++]  = 'DECIMAL_SEPARATOR';
        }
        if ($this->isColumnModified(CurrencyTableMap::THOUSAND_SEPARATOR)) {
            $modifiedColumns[':p' . $index++]  = 'THOUSAND_SEPARATOR';
        }
        if ($this->isColumnModified(CurrencyTableMap::POSITIVE_PREFFIX)) {
            $modifiedColumns[':p' . $index++]  = 'POSITIVE_PREFFIX';
        }
        if ($this->isColumnModified(CurrencyTableMap::POSITIVE_SUFFIX)) {
            $modifiedColumns[':p' . $index++]  = 'POSITIVE_SUFFIX';
        }
        if ($this->isColumnModified(CurrencyTableMap::NEGATIVE_PREFFIX)) {
            $modifiedColumns[':p' . $index++]  = 'NEGATIVE_PREFFIX';
        }
        if ($this->isColumnModified(CurrencyTableMap::NEGATIVE_SUFFIX)) {
            $modifiedColumns[':p' . $index++]  = 'NEGATIVE_SUFFIX';
        }
        if ($this->isColumnModified(CurrencyTableMap::DECIMAL_COUNT)) {
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
                    case 'SYMBOL':                        
                        $stmt->bindValue($identifier, $this->symbol, PDO::PARAM_STR);
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
                return $this->getSymbol();
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
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array())
    {
        if (isset($alreadyDumpedObjects['Currency'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Currency'][$this->getPrimaryKey()] = true;
        $keys = CurrencyTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getName(),
            $keys[2] => $this->getSymbol(),
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
                $this->setSymbol($value);
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
        if (array_key_exists($keys[2], $arr)) $this->setSymbol($arr[$keys[2]]);
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

        if ($this->isColumnModified(CurrencyTableMap::ID)) $criteria->add(CurrencyTableMap::ID, $this->id);
        if ($this->isColumnModified(CurrencyTableMap::NAME)) $criteria->add(CurrencyTableMap::NAME, $this->name);
        if ($this->isColumnModified(CurrencyTableMap::SYMBOL)) $criteria->add(CurrencyTableMap::SYMBOL, $this->symbol);
        if ($this->isColumnModified(CurrencyTableMap::DECIMAL_SEPARATOR)) $criteria->add(CurrencyTableMap::DECIMAL_SEPARATOR, $this->decimal_separator);
        if ($this->isColumnModified(CurrencyTableMap::THOUSAND_SEPARATOR)) $criteria->add(CurrencyTableMap::THOUSAND_SEPARATOR, $this->thousand_separator);
        if ($this->isColumnModified(CurrencyTableMap::POSITIVE_PREFFIX)) $criteria->add(CurrencyTableMap::POSITIVE_PREFFIX, $this->positive_preffix);
        if ($this->isColumnModified(CurrencyTableMap::POSITIVE_SUFFIX)) $criteria->add(CurrencyTableMap::POSITIVE_SUFFIX, $this->positive_suffix);
        if ($this->isColumnModified(CurrencyTableMap::NEGATIVE_PREFFIX)) $criteria->add(CurrencyTableMap::NEGATIVE_PREFFIX, $this->negative_preffix);
        if ($this->isColumnModified(CurrencyTableMap::NEGATIVE_SUFFIX)) $criteria->add(CurrencyTableMap::NEGATIVE_SUFFIX, $this->negative_suffix);
        if ($this->isColumnModified(CurrencyTableMap::DECIMAL_COUNT)) $criteria->add(CurrencyTableMap::DECIMAL_COUNT, $this->decimal_count);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(CurrencyTableMap::DATABASE_NAME);
        $criteria->add(CurrencyTableMap::ID, $this->id);

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
     * @param      object $copyObj An object of \Gekosale\Component\Configuration\Model\Currency\Currency (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setName($this->getName());
        $copyObj->setSymbol($this->getSymbol());
        $copyObj->setDecimalSeparator($this->getDecimalSeparator());
        $copyObj->setThousandSeparator($this->getThousandSeparator());
        $copyObj->setPositivePreffix($this->getPositivePreffix());
        $copyObj->setPositiveSuffix($this->getPositiveSuffix());
        $copyObj->setNegativePreffix($this->getNegativePreffix());
        $copyObj->setNegativeSuffix($this->getNegativeSuffix());
        $copyObj->setDecimalCount($this->getDecimalCount());
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
     * @return                 \Gekosale\Component\Configuration\Model\Currency\Currency Clone of current object.
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
        $this->name = null;
        $this->symbol = null;
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
        } // if ($deep)

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
