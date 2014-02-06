<?php

namespace Gekosale\Plugin\Contact\Model\ORM\Base;

use \DateTime;
use \Exception;
use \PDO;
use Gekosale\Plugin\Contact\Model\ORM\Contact as ChildContact;
use Gekosale\Plugin\Contact\Model\ORM\ContactI18n as ChildContactI18n;
use Gekosale\Plugin\Contact\Model\ORM\ContactI18nQuery as ChildContactI18nQuery;
use Gekosale\Plugin\Contact\Model\ORM\ContactQuery as ChildContactQuery;
use Gekosale\Plugin\Contact\Model\ORM\ContactShop as ChildContactShop;
use Gekosale\Plugin\Contact\Model\ORM\ContactShopQuery as ChildContactShopQuery;
use Gekosale\Plugin\Contact\Model\ORM\Map\ContactTableMap;
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
use Propel\Runtime\Util\PropelDateTime;

abstract class Contact implements ActiveRecordInterface 
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Gekosale\\Plugin\\Contact\\Model\\ORM\\Map\\ContactTableMap';


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
     * The value for the published field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $published;

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
     * @var        ObjectCollection|ChildShop[] Collection to store aggregation of ChildShop objects.
     */
    protected $collShops;
    protected $collShopsPartial;

    /**
     * @var        ObjectCollection|ChildContactShop[] Collection to store aggregation of ChildContactShop objects.
     */
    protected $collContactShops;
    protected $collContactShopsPartial;

    /**
     * @var        ObjectCollection|ChildContactI18n[] Collection to store aggregation of ChildContactI18n objects.
     */
    protected $collContactI18ns;
    protected $collContactI18nsPartial;

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
     * @var        array[ChildContactI18n]
     */
    protected $currentTranslations;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $shopsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $contactShopsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $contactI18nsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->published = 1;
    }

    /**
     * Initializes internal state of Gekosale\Plugin\Contact\Model\ORM\Base\Contact object.
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
     * Compares this with another <code>Contact</code> instance.  If
     * <code>obj</code> is an instance of <code>Contact</code>, delegates to
     * <code>equals(Contact)</code>.  Otherwise, returns <code>false</code>.
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
     * @return Contact The current object, for fluid interface
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
     * @return Contact The current object, for fluid interface
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
     * Get the [published] column value.
     * 
     * @return   int
     */
    public function getIsPublished()
    {

        return $this->published;
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
     * @return   \Gekosale\Plugin\Contact\Model\ORM\Contact The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[ContactTableMap::COL_ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [published] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Contact\Model\ORM\Contact The current object (for fluent API support)
     */
    public function setIsPublished($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->published !== $v) {
            $this->published = $v;
            $this->modifiedColumns[ContactTableMap::COL_PUBLISHED] = true;
        }


        return $this;
    } // setIsPublished()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     * 
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Gekosale\Plugin\Contact\Model\ORM\Contact The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($dt !== $this->created_at) {
                $this->created_at = $dt;
                $this->modifiedColumns[ContactTableMap::COL_CREATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     * 
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Gekosale\Plugin\Contact\Model\ORM\Contact The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($dt !== $this->updated_at) {
                $this->updated_at = $dt;
                $this->modifiedColumns[ContactTableMap::COL_UPDATED_AT] = true;
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
            if ($this->published !== 1) {
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


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : ContactTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : ContactTableMap::translateFieldName('IsPublished', TableMap::TYPE_PHPNAME, $indexType)];
            $this->published = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : ContactTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : ContactTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 4; // 4 = ContactTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Gekosale\Plugin\Contact\Model\ORM\Contact object", 0, $e);
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
            $con = Propel::getServiceContainer()->getReadConnection(ContactTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildContactQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collShops = null;

            $this->collContactShops = null;

            $this->collContactI18ns = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Contact::setDeleted()
     * @see Contact::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(ContactTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildContactQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(ContactTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(ContactTableMap::COL_CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(ContactTableMap::COL_UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(ContactTableMap::COL_UPDATED_AT)) {
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
                ContactTableMap::addInstanceToPool($this);
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

            if ($this->contactShopsScheduledForDeletion !== null) {
                if (!$this->contactShopsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Contact\Model\ORM\ContactShopQuery::create()
                        ->filterByPrimaryKeys($this->contactShopsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->contactShopsScheduledForDeletion = null;
                }
            }

                if ($this->collContactShops !== null) {
            foreach ($this->collContactShops as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->contactI18nsScheduledForDeletion !== null) {
                if (!$this->contactI18nsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Contact\Model\ORM\ContactI18nQuery::create()
                        ->filterByPrimaryKeys($this->contactI18nsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->contactI18nsScheduledForDeletion = null;
                }
            }

                if ($this->collContactI18ns !== null) {
            foreach ($this->collContactI18ns as $referrerFK) {
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

        $this->modifiedColumns[ContactTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . ContactTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ContactTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(ContactTableMap::COL_PUBLISHED)) {
            $modifiedColumns[':p' . $index++]  = 'PUBLISHED';
        }
        if ($this->isColumnModified(ContactTableMap::COL_CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'CREATED_AT';
        }
        if ($this->isColumnModified(ContactTableMap::COL_UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'UPDATED_AT';
        }

        $sql = sprintf(
            'INSERT INTO contact (%s) VALUES (%s)',
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
                    case 'PUBLISHED':                        
                        $stmt->bindValue($identifier, $this->published, PDO::PARAM_INT);
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
        $pos = ContactTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getIsPublished();
                break;
            case 2:
                return $this->getCreatedAt();
                break;
            case 3:
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
        if (isset($alreadyDumpedObjects['Contact'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Contact'][$this->getPrimaryKey()] = true;
        $keys = ContactTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getIsPublished(),
            $keys[2] => $this->getCreatedAt(),
            $keys[3] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }
        
        if ($includeForeignObjects) {
            if (null !== $this->collShops) {
                $result['Shops'] = $this->collShops->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collContactShops) {
                $result['ContactShops'] = $this->collContactShops->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collContactI18ns) {
                $result['ContactI18ns'] = $this->collContactI18ns->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = ContactTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

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
                $this->setIsPublished($value);
                break;
            case 2:
                $this->setCreatedAt($value);
                break;
            case 3:
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
        $keys = ContactTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setIsPublished($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setCreatedAt($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setUpdatedAt($arr[$keys[3]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ContactTableMap::DATABASE_NAME);

        if ($this->isColumnModified(ContactTableMap::COL_ID)) $criteria->add(ContactTableMap::COL_ID, $this->id);
        if ($this->isColumnModified(ContactTableMap::COL_PUBLISHED)) $criteria->add(ContactTableMap::COL_PUBLISHED, $this->published);
        if ($this->isColumnModified(ContactTableMap::COL_CREATED_AT)) $criteria->add(ContactTableMap::COL_CREATED_AT, $this->created_at);
        if ($this->isColumnModified(ContactTableMap::COL_UPDATED_AT)) $criteria->add(ContactTableMap::COL_UPDATED_AT, $this->updated_at);

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
        $criteria = new Criteria(ContactTableMap::DATABASE_NAME);
        $criteria->add(ContactTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \Gekosale\Plugin\Contact\Model\ORM\Contact (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setIsPublished($this->getIsPublished());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getShops() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addShop($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getContactShops() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addContactShop($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getContactI18ns() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addContactI18n($relObj->copy($deepCopy));
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
     * @return                 \Gekosale\Plugin\Contact\Model\ORM\Contact Clone of current object.
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
        if ('Shop' == $relationName) {
            return $this->initShops();
        }
        if ('ContactShop' == $relationName) {
            return $this->initContactShops();
        }
        if ('ContactI18n' == $relationName) {
            return $this->initContactI18ns();
        }
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
     * If this ChildContact is new, it will return
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
                    ->filterByContact($this)
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
     * @return   ChildContact The current object (for fluent API support)
     */
    public function setShops(Collection $shops, ConnectionInterface $con = null)
    {
        $shopsToDelete = $this->getShops(new Criteria(), $con)->diff($shops);

        
        $this->shopsScheduledForDeletion = $shopsToDelete;

        foreach ($shopsToDelete as $shopRemoved) {
            $shopRemoved->setContact(null);
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
                ->filterByContact($this)
                ->count($con);
        }

        return count($this->collShops);
    }

    /**
     * Method called to associate a ChildShop object to this object
     * through the ChildShop foreign key attribute.
     *
     * @param    ChildShop $l ChildShop
     * @return   \Gekosale\Plugin\Contact\Model\ORM\Contact The current object (for fluent API support)
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
        $shop->setContact($this);
    }

    /**
     * @param  Shop $shop The shop object to remove.
     * @return ChildContact The current object (for fluent API support)
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
            $shop->setContact(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Contact is new, it will return
     * an empty collection; or if this Contact has previously
     * been saved, it will retrieve related Shops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Contact.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildShop[] List of ChildShop objects
     */
    public function getShopsJoinCurrency($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ShopQuery::create(null, $criteria);
        $query->joinWith('Currency', $joinBehavior);

        return $this->getShops($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Contact is new, it will return
     * an empty collection; or if this Contact has previously
     * been saved, it will retrieve related Shops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Contact.
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
     * Otherwise if this Contact is new, it will return
     * an empty collection; or if this Contact has previously
     * been saved, it will retrieve related Shops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Contact.
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
     * Otherwise if this Contact is new, it will return
     * an empty collection; or if this Contact has previously
     * been saved, it will retrieve related Shops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Contact.
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
     * Clears out the collContactShops collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addContactShops()
     */
    public function clearContactShops()
    {
        $this->collContactShops = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collContactShops collection loaded partially.
     */
    public function resetPartialContactShops($v = true)
    {
        $this->collContactShopsPartial = $v;
    }

    /**
     * Initializes the collContactShops collection.
     *
     * By default this just sets the collContactShops collection to an empty array (like clearcollContactShops());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initContactShops($overrideExisting = true)
    {
        if (null !== $this->collContactShops && !$overrideExisting) {
            return;
        }
        $this->collContactShops = new ObjectCollection();
        $this->collContactShops->setModel('\Gekosale\Plugin\Contact\Model\ORM\ContactShop');
    }

    /**
     * Gets an array of ChildContactShop objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildContact is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildContactShop[] List of ChildContactShop objects
     * @throws PropelException
     */
    public function getContactShops($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collContactShopsPartial && !$this->isNew();
        if (null === $this->collContactShops || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collContactShops) {
                // return empty collection
                $this->initContactShops();
            } else {
                $collContactShops = ChildContactShopQuery::create(null, $criteria)
                    ->filterByContact($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collContactShopsPartial && count($collContactShops)) {
                        $this->initContactShops(false);

                        foreach ($collContactShops as $obj) {
                            if (false == $this->collContactShops->contains($obj)) {
                                $this->collContactShops->append($obj);
                            }
                        }

                        $this->collContactShopsPartial = true;
                    }

                    reset($collContactShops);

                    return $collContactShops;
                }

                if ($partial && $this->collContactShops) {
                    foreach ($this->collContactShops as $obj) {
                        if ($obj->isNew()) {
                            $collContactShops[] = $obj;
                        }
                    }
                }

                $this->collContactShops = $collContactShops;
                $this->collContactShopsPartial = false;
            }
        }

        return $this->collContactShops;
    }

    /**
     * Sets a collection of ContactShop objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $contactShops A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildContact The current object (for fluent API support)
     */
    public function setContactShops(Collection $contactShops, ConnectionInterface $con = null)
    {
        $contactShopsToDelete = $this->getContactShops(new Criteria(), $con)->diff($contactShops);

        
        $this->contactShopsScheduledForDeletion = $contactShopsToDelete;

        foreach ($contactShopsToDelete as $contactShopRemoved) {
            $contactShopRemoved->setContact(null);
        }

        $this->collContactShops = null;
        foreach ($contactShops as $contactShop) {
            $this->addContactShop($contactShop);
        }

        $this->collContactShops = $contactShops;
        $this->collContactShopsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ContactShop objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ContactShop objects.
     * @throws PropelException
     */
    public function countContactShops(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collContactShopsPartial && !$this->isNew();
        if (null === $this->collContactShops || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collContactShops) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getContactShops());
            }

            $query = ChildContactShopQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByContact($this)
                ->count($con);
        }

        return count($this->collContactShops);
    }

    /**
     * Method called to associate a ChildContactShop object to this object
     * through the ChildContactShop foreign key attribute.
     *
     * @param    ChildContactShop $l ChildContactShop
     * @return   \Gekosale\Plugin\Contact\Model\ORM\Contact The current object (for fluent API support)
     */
    public function addContactShop(ChildContactShop $l)
    {
        if ($this->collContactShops === null) {
            $this->initContactShops();
            $this->collContactShopsPartial = true;
        }

        if (!in_array($l, $this->collContactShops->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddContactShop($l);
        }

        return $this;
    }

    /**
     * @param ContactShop $contactShop The contactShop object to add.
     */
    protected function doAddContactShop($contactShop)
    {
        $this->collContactShops[]= $contactShop;
        $contactShop->setContact($this);
    }

    /**
     * @param  ContactShop $contactShop The contactShop object to remove.
     * @return ChildContact The current object (for fluent API support)
     */
    public function removeContactShop($contactShop)
    {
        if ($this->getContactShops()->contains($contactShop)) {
            $this->collContactShops->remove($this->collContactShops->search($contactShop));
            if (null === $this->contactShopsScheduledForDeletion) {
                $this->contactShopsScheduledForDeletion = clone $this->collContactShops;
                $this->contactShopsScheduledForDeletion->clear();
            }
            $this->contactShopsScheduledForDeletion[]= clone $contactShop;
            $contactShop->setContact(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Contact is new, it will return
     * an empty collection; or if this Contact has previously
     * been saved, it will retrieve related ContactShops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Contact.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildContactShop[] List of ChildContactShop objects
     */
    public function getContactShopsJoinShop($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildContactShopQuery::create(null, $criteria);
        $query->joinWith('Shop', $joinBehavior);

        return $this->getContactShops($query, $con);
    }

    /**
     * Clears out the collContactI18ns collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addContactI18ns()
     */
    public function clearContactI18ns()
    {
        $this->collContactI18ns = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collContactI18ns collection loaded partially.
     */
    public function resetPartialContactI18ns($v = true)
    {
        $this->collContactI18nsPartial = $v;
    }

    /**
     * Initializes the collContactI18ns collection.
     *
     * By default this just sets the collContactI18ns collection to an empty array (like clearcollContactI18ns());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initContactI18ns($overrideExisting = true)
    {
        if (null !== $this->collContactI18ns && !$overrideExisting) {
            return;
        }
        $this->collContactI18ns = new ObjectCollection();
        $this->collContactI18ns->setModel('\Gekosale\Plugin\Contact\Model\ORM\ContactI18n');
    }

    /**
     * Gets an array of ChildContactI18n objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildContact is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildContactI18n[] List of ChildContactI18n objects
     * @throws PropelException
     */
    public function getContactI18ns($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collContactI18nsPartial && !$this->isNew();
        if (null === $this->collContactI18ns || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collContactI18ns) {
                // return empty collection
                $this->initContactI18ns();
            } else {
                $collContactI18ns = ChildContactI18nQuery::create(null, $criteria)
                    ->filterByContact($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collContactI18nsPartial && count($collContactI18ns)) {
                        $this->initContactI18ns(false);

                        foreach ($collContactI18ns as $obj) {
                            if (false == $this->collContactI18ns->contains($obj)) {
                                $this->collContactI18ns->append($obj);
                            }
                        }

                        $this->collContactI18nsPartial = true;
                    }

                    reset($collContactI18ns);

                    return $collContactI18ns;
                }

                if ($partial && $this->collContactI18ns) {
                    foreach ($this->collContactI18ns as $obj) {
                        if ($obj->isNew()) {
                            $collContactI18ns[] = $obj;
                        }
                    }
                }

                $this->collContactI18ns = $collContactI18ns;
                $this->collContactI18nsPartial = false;
            }
        }

        return $this->collContactI18ns;
    }

    /**
     * Sets a collection of ContactI18n objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $contactI18ns A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildContact The current object (for fluent API support)
     */
    public function setContactI18ns(Collection $contactI18ns, ConnectionInterface $con = null)
    {
        $contactI18nsToDelete = $this->getContactI18ns(new Criteria(), $con)->diff($contactI18ns);

        
        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->contactI18nsScheduledForDeletion = clone $contactI18nsToDelete;

        foreach ($contactI18nsToDelete as $contactI18nRemoved) {
            $contactI18nRemoved->setContact(null);
        }

        $this->collContactI18ns = null;
        foreach ($contactI18ns as $contactI18n) {
            $this->addContactI18n($contactI18n);
        }

        $this->collContactI18ns = $contactI18ns;
        $this->collContactI18nsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ContactI18n objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ContactI18n objects.
     * @throws PropelException
     */
    public function countContactI18ns(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collContactI18nsPartial && !$this->isNew();
        if (null === $this->collContactI18ns || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collContactI18ns) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getContactI18ns());
            }

            $query = ChildContactI18nQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByContact($this)
                ->count($con);
        }

        return count($this->collContactI18ns);
    }

    /**
     * Method called to associate a ChildContactI18n object to this object
     * through the ChildContactI18n foreign key attribute.
     *
     * @param    ChildContactI18n $l ChildContactI18n
     * @return   \Gekosale\Plugin\Contact\Model\ORM\Contact The current object (for fluent API support)
     */
    public function addContactI18n(ChildContactI18n $l)
    {
        if ($l && $locale = $l->getLocale()) {
            $this->setLocale($locale);
            $this->currentTranslations[$locale] = $l;
        }
        if ($this->collContactI18ns === null) {
            $this->initContactI18ns();
            $this->collContactI18nsPartial = true;
        }

        if (!in_array($l, $this->collContactI18ns->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddContactI18n($l);
        }

        return $this;
    }

    /**
     * @param ContactI18n $contactI18n The contactI18n object to add.
     */
    protected function doAddContactI18n($contactI18n)
    {
        $this->collContactI18ns[]= $contactI18n;
        $contactI18n->setContact($this);
    }

    /**
     * @param  ContactI18n $contactI18n The contactI18n object to remove.
     * @return ChildContact The current object (for fluent API support)
     */
    public function removeContactI18n($contactI18n)
    {
        if ($this->getContactI18ns()->contains($contactI18n)) {
            $this->collContactI18ns->remove($this->collContactI18ns->search($contactI18n));
            if (null === $this->contactI18nsScheduledForDeletion) {
                $this->contactI18nsScheduledForDeletion = clone $this->collContactI18ns;
                $this->contactI18nsScheduledForDeletion->clear();
            }
            $this->contactI18nsScheduledForDeletion[]= clone $contactI18n;
            $contactI18n->setContact(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->published = null;
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
            if ($this->collShops) {
                foreach ($this->collShops as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collContactShops) {
                foreach ($this->collContactShops as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collContactI18ns) {
                foreach ($this->collContactI18ns as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        // i18n behavior
        $this->currentLocale = 'en_US';
        $this->currentTranslations = null;

        $this->collShops = null;
        $this->collContactShops = null;
        $this->collContactI18ns = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ContactTableMap::DEFAULT_STRING_FORMAT);
    }

    // i18n behavior
    
    /**
     * Sets the locale for translations
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     *
     * @return    ChildContact The current object (for fluent API support)
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
     * @return ChildContactI18n */
    public function getTranslation($locale = 'en_US', ConnectionInterface $con = null)
    {
        if (!isset($this->currentTranslations[$locale])) {
            if (null !== $this->collContactI18ns) {
                foreach ($this->collContactI18ns as $translation) {
                    if ($translation->getLocale() == $locale) {
                        $this->currentTranslations[$locale] = $translation;
    
                        return $translation;
                    }
                }
            }
            if ($this->isNew()) {
                $translation = new ChildContactI18n();
                $translation->setLocale($locale);
            } else {
                $translation = ChildContactI18nQuery::create()
                    ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                    ->findOneOrCreate($con);
                $this->currentTranslations[$locale] = $translation;
            }
            $this->addContactI18n($translation);
        }
    
        return $this->currentTranslations[$locale];
    }
    
    /**
     * Remove the translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return    ChildContact The current object (for fluent API support)
     */
    public function removeTranslation($locale = 'en_US', ConnectionInterface $con = null)
    {
        if (!$this->isNew()) {
            ChildContactI18nQuery::create()
                ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                ->delete($con);
        }
        if (isset($this->currentTranslations[$locale])) {
            unset($this->currentTranslations[$locale]);
        }
        foreach ($this->collContactI18ns as $key => $translation) {
            if ($translation->getLocale() == $locale) {
                unset($this->collContactI18ns[$key]);
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
     * @return ChildContactI18n */
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
         * @return   \Gekosale\Plugin\Contact\Model\ORM\ContactI18n The current object (for fluent API support)
         */
        public function setName($v)
        {    $this->getCurrentTranslation()->setName($v);
    
        return $this;
    }

    // timestampable behavior
    
    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     ChildContact The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[ContactTableMap::COL_UPDATED_AT] = true;
    
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
