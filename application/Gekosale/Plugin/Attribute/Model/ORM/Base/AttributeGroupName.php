<?php

namespace Gekosale\Plugin\Attribute\Model\ORM\Base;

use \DateTime;
use \Exception;
use \PDO;
use Gekosale\Plugin\Attribute\Model\ORM\AttributeGroup as ChildAttributeGroup;
use Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupName as ChildAttributeGroupName;
use Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupNameI18n as ChildAttributeGroupNameI18n;
use Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupNameI18nQuery as ChildAttributeGroupNameI18nQuery;
use Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupNameQuery as ChildAttributeGroupNameQuery;
use Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupQuery as ChildAttributeGroupQuery;
use Gekosale\Plugin\Attribute\Model\ORM\CategoryAttributeProduct as ChildCategoryAttributeProduct;
use Gekosale\Plugin\Attribute\Model\ORM\CategoryAttributeProductQuery as ChildCategoryAttributeProductQuery;
use Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute as ChildProductAttribute;
use Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeQuery as ChildProductAttributeQuery;
use Gekosale\Plugin\Attribute\Model\ORM\Map\AttributeGroupNameTableMap;
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

abstract class AttributeGroupName implements ActiveRecordInterface 
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Gekosale\\Plugin\\Attribute\\Model\\ORM\\Map\\AttributeGroupNameTableMap';


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
     * @var        ObjectCollection|ChildAttributeGroup[] Collection to store aggregation of ChildAttributeGroup objects.
     */
    protected $collAttributeGroups;
    protected $collAttributeGroupsPartial;

    /**
     * @var        ObjectCollection|ChildCategoryAttributeProduct[] Collection to store aggregation of ChildCategoryAttributeProduct objects.
     */
    protected $collCategoryAttributeProducts;
    protected $collCategoryAttributeProductsPartial;

    /**
     * @var        ObjectCollection|ChildProductAttribute[] Collection to store aggregation of ChildProductAttribute objects.
     */
    protected $collProductAttributes;
    protected $collProductAttributesPartial;

    /**
     * @var        ObjectCollection|ChildAttributeGroupNameI18n[] Collection to store aggregation of ChildAttributeGroupNameI18n objects.
     */
    protected $collAttributeGroupNameI18ns;
    protected $collAttributeGroupNameI18nsPartial;

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
     * @var        array[ChildAttributeGroupNameI18n]
     */
    protected $currentTranslations;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $attributeGroupsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $categoryAttributeProductsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $productAttributesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $attributeGroupNameI18nsScheduledForDeletion = null;

    /**
     * Initializes internal state of Gekosale\Plugin\Attribute\Model\ORM\Base\AttributeGroupName object.
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
     * Compares this with another <code>AttributeGroupName</code> instance.  If
     * <code>obj</code> is an instance of <code>AttributeGroupName</code>, delegates to
     * <code>equals(AttributeGroupName)</code>.  Otherwise, returns <code>false</code>.
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
     * @return AttributeGroupName The current object, for fluid interface
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
     * @return AttributeGroupName The current object, for fluid interface
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
     * @return   \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupName The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[AttributeGroupNameTableMap::COL_ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     * 
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupName The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($dt !== $this->created_at) {
                $this->created_at = $dt;
                $this->modifiedColumns[AttributeGroupNameTableMap::COL_CREATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     * 
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupName The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($dt !== $this->updated_at) {
                $this->updated_at = $dt;
                $this->modifiedColumns[AttributeGroupNameTableMap::COL_UPDATED_AT] = true;
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


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : AttributeGroupNameTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : AttributeGroupNameTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : AttributeGroupNameTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 3; // 3 = AttributeGroupNameTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupName object", 0, $e);
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
            $con = Propel::getServiceContainer()->getReadConnection(AttributeGroupNameTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildAttributeGroupNameQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collAttributeGroups = null;

            $this->collCategoryAttributeProducts = null;

            $this->collProductAttributes = null;

            $this->collAttributeGroupNameI18ns = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see AttributeGroupName::setDeleted()
     * @see AttributeGroupName::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(AttributeGroupNameTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildAttributeGroupNameQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(AttributeGroupNameTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(AttributeGroupNameTableMap::COL_CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(AttributeGroupNameTableMap::COL_UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(AttributeGroupNameTableMap::COL_UPDATED_AT)) {
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
                AttributeGroupNameTableMap::addInstanceToPool($this);
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

            if ($this->attributeGroupsScheduledForDeletion !== null) {
                if (!$this->attributeGroupsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupQuery::create()
                        ->filterByPrimaryKeys($this->attributeGroupsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->attributeGroupsScheduledForDeletion = null;
                }
            }

                if ($this->collAttributeGroups !== null) {
            foreach ($this->collAttributeGroups as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->categoryAttributeProductsScheduledForDeletion !== null) {
                if (!$this->categoryAttributeProductsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Attribute\Model\ORM\CategoryAttributeProductQuery::create()
                        ->filterByPrimaryKeys($this->categoryAttributeProductsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->categoryAttributeProductsScheduledForDeletion = null;
                }
            }

                if ($this->collCategoryAttributeProducts !== null) {
            foreach ($this->collCategoryAttributeProducts as $referrerFK) {
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

            if ($this->attributeGroupNameI18nsScheduledForDeletion !== null) {
                if (!$this->attributeGroupNameI18nsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupNameI18nQuery::create()
                        ->filterByPrimaryKeys($this->attributeGroupNameI18nsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->attributeGroupNameI18nsScheduledForDeletion = null;
                }
            }

                if ($this->collAttributeGroupNameI18ns !== null) {
            foreach ($this->collAttributeGroupNameI18ns as $referrerFK) {
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

        $this->modifiedColumns[AttributeGroupNameTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . AttributeGroupNameTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(AttributeGroupNameTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(AttributeGroupNameTableMap::COL_CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'CREATED_AT';
        }
        if ($this->isColumnModified(AttributeGroupNameTableMap::COL_UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'UPDATED_AT';
        }

        $sql = sprintf(
            'INSERT INTO attribute_group_name (%s) VALUES (%s)',
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
        $pos = AttributeGroupNameTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getCreatedAt();
                break;
            case 2:
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
        if (isset($alreadyDumpedObjects['AttributeGroupName'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['AttributeGroupName'][$this->getPrimaryKey()] = true;
        $keys = AttributeGroupNameTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getCreatedAt(),
            $keys[2] => $this->getUpdatedAt(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }
        
        if ($includeForeignObjects) {
            if (null !== $this->collAttributeGroups) {
                $result['AttributeGroups'] = $this->collAttributeGroups->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCategoryAttributeProducts) {
                $result['CategoryAttributeProducts'] = $this->collCategoryAttributeProducts->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductAttributes) {
                $result['ProductAttributes'] = $this->collProductAttributes->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collAttributeGroupNameI18ns) {
                $result['AttributeGroupNameI18ns'] = $this->collAttributeGroupNameI18ns->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = AttributeGroupNameTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

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
                $this->setCreatedAt($value);
                break;
            case 2:
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
        $keys = AttributeGroupNameTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setCreatedAt($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setUpdatedAt($arr[$keys[2]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(AttributeGroupNameTableMap::DATABASE_NAME);

        if ($this->isColumnModified(AttributeGroupNameTableMap::COL_ID)) $criteria->add(AttributeGroupNameTableMap::COL_ID, $this->id);
        if ($this->isColumnModified(AttributeGroupNameTableMap::COL_CREATED_AT)) $criteria->add(AttributeGroupNameTableMap::COL_CREATED_AT, $this->created_at);
        if ($this->isColumnModified(AttributeGroupNameTableMap::COL_UPDATED_AT)) $criteria->add(AttributeGroupNameTableMap::COL_UPDATED_AT, $this->updated_at);

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
        $criteria = new Criteria(AttributeGroupNameTableMap::DATABASE_NAME);
        $criteria->add(AttributeGroupNameTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupName (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getAttributeGroups() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAttributeGroup($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCategoryAttributeProducts() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCategoryAttributeProduct($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductAttributes() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductAttribute($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getAttributeGroupNameI18ns() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAttributeGroupNameI18n($relObj->copy($deepCopy));
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
     * @return                 \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupName Clone of current object.
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
        if ('AttributeGroup' == $relationName) {
            return $this->initAttributeGroups();
        }
        if ('CategoryAttributeProduct' == $relationName) {
            return $this->initCategoryAttributeProducts();
        }
        if ('ProductAttribute' == $relationName) {
            return $this->initProductAttributes();
        }
        if ('AttributeGroupNameI18n' == $relationName) {
            return $this->initAttributeGroupNameI18ns();
        }
    }

    /**
     * Clears out the collAttributeGroups collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addAttributeGroups()
     */
    public function clearAttributeGroups()
    {
        $this->collAttributeGroups = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collAttributeGroups collection loaded partially.
     */
    public function resetPartialAttributeGroups($v = true)
    {
        $this->collAttributeGroupsPartial = $v;
    }

    /**
     * Initializes the collAttributeGroups collection.
     *
     * By default this just sets the collAttributeGroups collection to an empty array (like clearcollAttributeGroups());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initAttributeGroups($overrideExisting = true)
    {
        if (null !== $this->collAttributeGroups && !$overrideExisting) {
            return;
        }
        $this->collAttributeGroups = new ObjectCollection();
        $this->collAttributeGroups->setModel('\Gekosale\Plugin\Attribute\Model\ORM\AttributeGroup');
    }

    /**
     * Gets an array of ChildAttributeGroup objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildAttributeGroupName is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildAttributeGroup[] List of ChildAttributeGroup objects
     * @throws PropelException
     */
    public function getAttributeGroups($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collAttributeGroupsPartial && !$this->isNew();
        if (null === $this->collAttributeGroups || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collAttributeGroups) {
                // return empty collection
                $this->initAttributeGroups();
            } else {
                $collAttributeGroups = ChildAttributeGroupQuery::create(null, $criteria)
                    ->filterByAttributeGroupName($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collAttributeGroupsPartial && count($collAttributeGroups)) {
                        $this->initAttributeGroups(false);

                        foreach ($collAttributeGroups as $obj) {
                            if (false == $this->collAttributeGroups->contains($obj)) {
                                $this->collAttributeGroups->append($obj);
                            }
                        }

                        $this->collAttributeGroupsPartial = true;
                    }

                    reset($collAttributeGroups);

                    return $collAttributeGroups;
                }

                if ($partial && $this->collAttributeGroups) {
                    foreach ($this->collAttributeGroups as $obj) {
                        if ($obj->isNew()) {
                            $collAttributeGroups[] = $obj;
                        }
                    }
                }

                $this->collAttributeGroups = $collAttributeGroups;
                $this->collAttributeGroupsPartial = false;
            }
        }

        return $this->collAttributeGroups;
    }

    /**
     * Sets a collection of AttributeGroup objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $attributeGroups A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildAttributeGroupName The current object (for fluent API support)
     */
    public function setAttributeGroups(Collection $attributeGroups, ConnectionInterface $con = null)
    {
        $attributeGroupsToDelete = $this->getAttributeGroups(new Criteria(), $con)->diff($attributeGroups);

        
        $this->attributeGroupsScheduledForDeletion = $attributeGroupsToDelete;

        foreach ($attributeGroupsToDelete as $attributeGroupRemoved) {
            $attributeGroupRemoved->setAttributeGroupName(null);
        }

        $this->collAttributeGroups = null;
        foreach ($attributeGroups as $attributeGroup) {
            $this->addAttributeGroup($attributeGroup);
        }

        $this->collAttributeGroups = $attributeGroups;
        $this->collAttributeGroupsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related AttributeGroup objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related AttributeGroup objects.
     * @throws PropelException
     */
    public function countAttributeGroups(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collAttributeGroupsPartial && !$this->isNew();
        if (null === $this->collAttributeGroups || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAttributeGroups) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getAttributeGroups());
            }

            $query = ChildAttributeGroupQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByAttributeGroupName($this)
                ->count($con);
        }

        return count($this->collAttributeGroups);
    }

    /**
     * Method called to associate a ChildAttributeGroup object to this object
     * through the ChildAttributeGroup foreign key attribute.
     *
     * @param    ChildAttributeGroup $l ChildAttributeGroup
     * @return   \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupName The current object (for fluent API support)
     */
    public function addAttributeGroup(ChildAttributeGroup $l)
    {
        if ($this->collAttributeGroups === null) {
            $this->initAttributeGroups();
            $this->collAttributeGroupsPartial = true;
        }

        if (!in_array($l, $this->collAttributeGroups->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddAttributeGroup($l);
        }

        return $this;
    }

    /**
     * @param AttributeGroup $attributeGroup The attributeGroup object to add.
     */
    protected function doAddAttributeGroup($attributeGroup)
    {
        $this->collAttributeGroups[]= $attributeGroup;
        $attributeGroup->setAttributeGroupName($this);
    }

    /**
     * @param  AttributeGroup $attributeGroup The attributeGroup object to remove.
     * @return ChildAttributeGroupName The current object (for fluent API support)
     */
    public function removeAttributeGroup($attributeGroup)
    {
        if ($this->getAttributeGroups()->contains($attributeGroup)) {
            $this->collAttributeGroups->remove($this->collAttributeGroups->search($attributeGroup));
            if (null === $this->attributeGroupsScheduledForDeletion) {
                $this->attributeGroupsScheduledForDeletion = clone $this->collAttributeGroups;
                $this->attributeGroupsScheduledForDeletion->clear();
            }
            $this->attributeGroupsScheduledForDeletion[]= clone $attributeGroup;
            $attributeGroup->setAttributeGroupName(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this AttributeGroupName is new, it will return
     * an empty collection; or if this AttributeGroupName has previously
     * been saved, it will retrieve related AttributeGroups from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in AttributeGroupName.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildAttributeGroup[] List of ChildAttributeGroup objects
     */
    public function getAttributeGroupsJoinAttributeProduct($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildAttributeGroupQuery::create(null, $criteria);
        $query->joinWith('AttributeProduct', $joinBehavior);

        return $this->getAttributeGroups($query, $con);
    }

    /**
     * Clears out the collCategoryAttributeProducts collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCategoryAttributeProducts()
     */
    public function clearCategoryAttributeProducts()
    {
        $this->collCategoryAttributeProducts = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCategoryAttributeProducts collection loaded partially.
     */
    public function resetPartialCategoryAttributeProducts($v = true)
    {
        $this->collCategoryAttributeProductsPartial = $v;
    }

    /**
     * Initializes the collCategoryAttributeProducts collection.
     *
     * By default this just sets the collCategoryAttributeProducts collection to an empty array (like clearcollCategoryAttributeProducts());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCategoryAttributeProducts($overrideExisting = true)
    {
        if (null !== $this->collCategoryAttributeProducts && !$overrideExisting) {
            return;
        }
        $this->collCategoryAttributeProducts = new ObjectCollection();
        $this->collCategoryAttributeProducts->setModel('\Gekosale\Plugin\Attribute\Model\ORM\CategoryAttributeProduct');
    }

    /**
     * Gets an array of ChildCategoryAttributeProduct objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildAttributeGroupName is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildCategoryAttributeProduct[] List of ChildCategoryAttributeProduct objects
     * @throws PropelException
     */
    public function getCategoryAttributeProducts($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCategoryAttributeProductsPartial && !$this->isNew();
        if (null === $this->collCategoryAttributeProducts || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCategoryAttributeProducts) {
                // return empty collection
                $this->initCategoryAttributeProducts();
            } else {
                $collCategoryAttributeProducts = ChildCategoryAttributeProductQuery::create(null, $criteria)
                    ->filterByAttributeGroupName($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCategoryAttributeProductsPartial && count($collCategoryAttributeProducts)) {
                        $this->initCategoryAttributeProducts(false);

                        foreach ($collCategoryAttributeProducts as $obj) {
                            if (false == $this->collCategoryAttributeProducts->contains($obj)) {
                                $this->collCategoryAttributeProducts->append($obj);
                            }
                        }

                        $this->collCategoryAttributeProductsPartial = true;
                    }

                    reset($collCategoryAttributeProducts);

                    return $collCategoryAttributeProducts;
                }

                if ($partial && $this->collCategoryAttributeProducts) {
                    foreach ($this->collCategoryAttributeProducts as $obj) {
                        if ($obj->isNew()) {
                            $collCategoryAttributeProducts[] = $obj;
                        }
                    }
                }

                $this->collCategoryAttributeProducts = $collCategoryAttributeProducts;
                $this->collCategoryAttributeProductsPartial = false;
            }
        }

        return $this->collCategoryAttributeProducts;
    }

    /**
     * Sets a collection of CategoryAttributeProduct objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $categoryAttributeProducts A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildAttributeGroupName The current object (for fluent API support)
     */
    public function setCategoryAttributeProducts(Collection $categoryAttributeProducts, ConnectionInterface $con = null)
    {
        $categoryAttributeProductsToDelete = $this->getCategoryAttributeProducts(new Criteria(), $con)->diff($categoryAttributeProducts);

        
        $this->categoryAttributeProductsScheduledForDeletion = $categoryAttributeProductsToDelete;

        foreach ($categoryAttributeProductsToDelete as $categoryAttributeProductRemoved) {
            $categoryAttributeProductRemoved->setAttributeGroupName(null);
        }

        $this->collCategoryAttributeProducts = null;
        foreach ($categoryAttributeProducts as $categoryAttributeProduct) {
            $this->addCategoryAttributeProduct($categoryAttributeProduct);
        }

        $this->collCategoryAttributeProducts = $categoryAttributeProducts;
        $this->collCategoryAttributeProductsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CategoryAttributeProduct objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related CategoryAttributeProduct objects.
     * @throws PropelException
     */
    public function countCategoryAttributeProducts(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCategoryAttributeProductsPartial && !$this->isNew();
        if (null === $this->collCategoryAttributeProducts || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCategoryAttributeProducts) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCategoryAttributeProducts());
            }

            $query = ChildCategoryAttributeProductQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByAttributeGroupName($this)
                ->count($con);
        }

        return count($this->collCategoryAttributeProducts);
    }

    /**
     * Method called to associate a ChildCategoryAttributeProduct object to this object
     * through the ChildCategoryAttributeProduct foreign key attribute.
     *
     * @param    ChildCategoryAttributeProduct $l ChildCategoryAttributeProduct
     * @return   \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupName The current object (for fluent API support)
     */
    public function addCategoryAttributeProduct(ChildCategoryAttributeProduct $l)
    {
        if ($this->collCategoryAttributeProducts === null) {
            $this->initCategoryAttributeProducts();
            $this->collCategoryAttributeProductsPartial = true;
        }

        if (!in_array($l, $this->collCategoryAttributeProducts->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCategoryAttributeProduct($l);
        }

        return $this;
    }

    /**
     * @param CategoryAttributeProduct $categoryAttributeProduct The categoryAttributeProduct object to add.
     */
    protected function doAddCategoryAttributeProduct($categoryAttributeProduct)
    {
        $this->collCategoryAttributeProducts[]= $categoryAttributeProduct;
        $categoryAttributeProduct->setAttributeGroupName($this);
    }

    /**
     * @param  CategoryAttributeProduct $categoryAttributeProduct The categoryAttributeProduct object to remove.
     * @return ChildAttributeGroupName The current object (for fluent API support)
     */
    public function removeCategoryAttributeProduct($categoryAttributeProduct)
    {
        if ($this->getCategoryAttributeProducts()->contains($categoryAttributeProduct)) {
            $this->collCategoryAttributeProducts->remove($this->collCategoryAttributeProducts->search($categoryAttributeProduct));
            if (null === $this->categoryAttributeProductsScheduledForDeletion) {
                $this->categoryAttributeProductsScheduledForDeletion = clone $this->collCategoryAttributeProducts;
                $this->categoryAttributeProductsScheduledForDeletion->clear();
            }
            $this->categoryAttributeProductsScheduledForDeletion[]= clone $categoryAttributeProduct;
            $categoryAttributeProduct->setAttributeGroupName(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this AttributeGroupName is new, it will return
     * an empty collection; or if this AttributeGroupName has previously
     * been saved, it will retrieve related CategoryAttributeProducts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in AttributeGroupName.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCategoryAttributeProduct[] List of ChildCategoryAttributeProduct objects
     */
    public function getCategoryAttributeProductsJoinAttributeProduct($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCategoryAttributeProductQuery::create(null, $criteria);
        $query->joinWith('AttributeProduct', $joinBehavior);

        return $this->getCategoryAttributeProducts($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this AttributeGroupName is new, it will return
     * an empty collection; or if this AttributeGroupName has previously
     * been saved, it will retrieve related CategoryAttributeProducts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in AttributeGroupName.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCategoryAttributeProduct[] List of ChildCategoryAttributeProduct objects
     */
    public function getCategoryAttributeProductsJoinCategory($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCategoryAttributeProductQuery::create(null, $criteria);
        $query->joinWith('Category', $joinBehavior);

        return $this->getCategoryAttributeProducts($query, $con);
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
     * If this ChildAttributeGroupName is new, it will return
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
                $collProductAttributes = ChildProductAttributeQuery::create(null, $criteria)
                    ->filterByAttributeGroupName($this)
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
     * @return   ChildAttributeGroupName The current object (for fluent API support)
     */
    public function setProductAttributes(Collection $productAttributes, ConnectionInterface $con = null)
    {
        $productAttributesToDelete = $this->getProductAttributes(new Criteria(), $con)->diff($productAttributes);

        
        $this->productAttributesScheduledForDeletion = $productAttributesToDelete;

        foreach ($productAttributesToDelete as $productAttributeRemoved) {
            $productAttributeRemoved->setAttributeGroupName(null);
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

            $query = ChildProductAttributeQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByAttributeGroupName($this)
                ->count($con);
        }

        return count($this->collProductAttributes);
    }

    /**
     * Method called to associate a ChildProductAttribute object to this object
     * through the ChildProductAttribute foreign key attribute.
     *
     * @param    ChildProductAttribute $l ChildProductAttribute
     * @return   \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupName The current object (for fluent API support)
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
        $productAttribute->setAttributeGroupName($this);
    }

    /**
     * @param  ProductAttribute $productAttribute The productAttribute object to remove.
     * @return ChildAttributeGroupName The current object (for fluent API support)
     */
    public function removeProductAttribute($productAttribute)
    {
        if ($this->getProductAttributes()->contains($productAttribute)) {
            $this->collProductAttributes->remove($this->collProductAttributes->search($productAttribute));
            if (null === $this->productAttributesScheduledForDeletion) {
                $this->productAttributesScheduledForDeletion = clone $this->collProductAttributes;
                $this->productAttributesScheduledForDeletion->clear();
            }
            $this->productAttributesScheduledForDeletion[]= $productAttribute;
            $productAttribute->setAttributeGroupName(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this AttributeGroupName is new, it will return
     * an empty collection; or if this AttributeGroupName has previously
     * been saved, it will retrieve related ProductAttributes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in AttributeGroupName.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProductAttribute[] List of ChildProductAttribute objects
     */
    public function getProductAttributesJoinAvailability($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildProductAttributeQuery::create(null, $criteria);
        $query->joinWith('Availability', $joinBehavior);

        return $this->getProductAttributes($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this AttributeGroupName is new, it will return
     * an empty collection; or if this AttributeGroupName has previously
     * been saved, it will retrieve related ProductAttributes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in AttributeGroupName.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProductAttribute[] List of ChildProductAttribute objects
     */
    public function getProductAttributesJoinFile($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildProductAttributeQuery::create(null, $criteria);
        $query->joinWith('File', $joinBehavior);

        return $this->getProductAttributes($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this AttributeGroupName is new, it will return
     * an empty collection; or if this AttributeGroupName has previously
     * been saved, it will retrieve related ProductAttributes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in AttributeGroupName.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProductAttribute[] List of ChildProductAttribute objects
     */
    public function getProductAttributesJoinProduct($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildProductAttributeQuery::create(null, $criteria);
        $query->joinWith('Product', $joinBehavior);

        return $this->getProductAttributes($query, $con);
    }

    /**
     * Clears out the collAttributeGroupNameI18ns collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addAttributeGroupNameI18ns()
     */
    public function clearAttributeGroupNameI18ns()
    {
        $this->collAttributeGroupNameI18ns = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collAttributeGroupNameI18ns collection loaded partially.
     */
    public function resetPartialAttributeGroupNameI18ns($v = true)
    {
        $this->collAttributeGroupNameI18nsPartial = $v;
    }

    /**
     * Initializes the collAttributeGroupNameI18ns collection.
     *
     * By default this just sets the collAttributeGroupNameI18ns collection to an empty array (like clearcollAttributeGroupNameI18ns());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initAttributeGroupNameI18ns($overrideExisting = true)
    {
        if (null !== $this->collAttributeGroupNameI18ns && !$overrideExisting) {
            return;
        }
        $this->collAttributeGroupNameI18ns = new ObjectCollection();
        $this->collAttributeGroupNameI18ns->setModel('\Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupNameI18n');
    }

    /**
     * Gets an array of ChildAttributeGroupNameI18n objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildAttributeGroupName is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildAttributeGroupNameI18n[] List of ChildAttributeGroupNameI18n objects
     * @throws PropelException
     */
    public function getAttributeGroupNameI18ns($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collAttributeGroupNameI18nsPartial && !$this->isNew();
        if (null === $this->collAttributeGroupNameI18ns || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collAttributeGroupNameI18ns) {
                // return empty collection
                $this->initAttributeGroupNameI18ns();
            } else {
                $collAttributeGroupNameI18ns = ChildAttributeGroupNameI18nQuery::create(null, $criteria)
                    ->filterByAttributeGroupName($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collAttributeGroupNameI18nsPartial && count($collAttributeGroupNameI18ns)) {
                        $this->initAttributeGroupNameI18ns(false);

                        foreach ($collAttributeGroupNameI18ns as $obj) {
                            if (false == $this->collAttributeGroupNameI18ns->contains($obj)) {
                                $this->collAttributeGroupNameI18ns->append($obj);
                            }
                        }

                        $this->collAttributeGroupNameI18nsPartial = true;
                    }

                    reset($collAttributeGroupNameI18ns);

                    return $collAttributeGroupNameI18ns;
                }

                if ($partial && $this->collAttributeGroupNameI18ns) {
                    foreach ($this->collAttributeGroupNameI18ns as $obj) {
                        if ($obj->isNew()) {
                            $collAttributeGroupNameI18ns[] = $obj;
                        }
                    }
                }

                $this->collAttributeGroupNameI18ns = $collAttributeGroupNameI18ns;
                $this->collAttributeGroupNameI18nsPartial = false;
            }
        }

        return $this->collAttributeGroupNameI18ns;
    }

    /**
     * Sets a collection of AttributeGroupNameI18n objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $attributeGroupNameI18ns A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildAttributeGroupName The current object (for fluent API support)
     */
    public function setAttributeGroupNameI18ns(Collection $attributeGroupNameI18ns, ConnectionInterface $con = null)
    {
        $attributeGroupNameI18nsToDelete = $this->getAttributeGroupNameI18ns(new Criteria(), $con)->diff($attributeGroupNameI18ns);

        
        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->attributeGroupNameI18nsScheduledForDeletion = clone $attributeGroupNameI18nsToDelete;

        foreach ($attributeGroupNameI18nsToDelete as $attributeGroupNameI18nRemoved) {
            $attributeGroupNameI18nRemoved->setAttributeGroupName(null);
        }

        $this->collAttributeGroupNameI18ns = null;
        foreach ($attributeGroupNameI18ns as $attributeGroupNameI18n) {
            $this->addAttributeGroupNameI18n($attributeGroupNameI18n);
        }

        $this->collAttributeGroupNameI18ns = $attributeGroupNameI18ns;
        $this->collAttributeGroupNameI18nsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related AttributeGroupNameI18n objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related AttributeGroupNameI18n objects.
     * @throws PropelException
     */
    public function countAttributeGroupNameI18ns(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collAttributeGroupNameI18nsPartial && !$this->isNew();
        if (null === $this->collAttributeGroupNameI18ns || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAttributeGroupNameI18ns) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getAttributeGroupNameI18ns());
            }

            $query = ChildAttributeGroupNameI18nQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByAttributeGroupName($this)
                ->count($con);
        }

        return count($this->collAttributeGroupNameI18ns);
    }

    /**
     * Method called to associate a ChildAttributeGroupNameI18n object to this object
     * through the ChildAttributeGroupNameI18n foreign key attribute.
     *
     * @param    ChildAttributeGroupNameI18n $l ChildAttributeGroupNameI18n
     * @return   \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupName The current object (for fluent API support)
     */
    public function addAttributeGroupNameI18n(ChildAttributeGroupNameI18n $l)
    {
        if ($l && $locale = $l->getLocale()) {
            $this->setLocale($locale);
            $this->currentTranslations[$locale] = $l;
        }
        if ($this->collAttributeGroupNameI18ns === null) {
            $this->initAttributeGroupNameI18ns();
            $this->collAttributeGroupNameI18nsPartial = true;
        }

        if (!in_array($l, $this->collAttributeGroupNameI18ns->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddAttributeGroupNameI18n($l);
        }

        return $this;
    }

    /**
     * @param AttributeGroupNameI18n $attributeGroupNameI18n The attributeGroupNameI18n object to add.
     */
    protected function doAddAttributeGroupNameI18n($attributeGroupNameI18n)
    {
        $this->collAttributeGroupNameI18ns[]= $attributeGroupNameI18n;
        $attributeGroupNameI18n->setAttributeGroupName($this);
    }

    /**
     * @param  AttributeGroupNameI18n $attributeGroupNameI18n The attributeGroupNameI18n object to remove.
     * @return ChildAttributeGroupName The current object (for fluent API support)
     */
    public function removeAttributeGroupNameI18n($attributeGroupNameI18n)
    {
        if ($this->getAttributeGroupNameI18ns()->contains($attributeGroupNameI18n)) {
            $this->collAttributeGroupNameI18ns->remove($this->collAttributeGroupNameI18ns->search($attributeGroupNameI18n));
            if (null === $this->attributeGroupNameI18nsScheduledForDeletion) {
                $this->attributeGroupNameI18nsScheduledForDeletion = clone $this->collAttributeGroupNameI18ns;
                $this->attributeGroupNameI18nsScheduledForDeletion->clear();
            }
            $this->attributeGroupNameI18nsScheduledForDeletion[]= clone $attributeGroupNameI18n;
            $attributeGroupNameI18n->setAttributeGroupName(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->created_at = null;
        $this->updated_at = null;
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
            if ($this->collAttributeGroups) {
                foreach ($this->collAttributeGroups as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCategoryAttributeProducts) {
                foreach ($this->collCategoryAttributeProducts as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductAttributes) {
                foreach ($this->collProductAttributes as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collAttributeGroupNameI18ns) {
                foreach ($this->collAttributeGroupNameI18ns as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        // i18n behavior
        $this->currentLocale = 'en_US';
        $this->currentTranslations = null;

        $this->collAttributeGroups = null;
        $this->collCategoryAttributeProducts = null;
        $this->collProductAttributes = null;
        $this->collAttributeGroupNameI18ns = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(AttributeGroupNameTableMap::DEFAULT_STRING_FORMAT);
    }

    // i18n behavior
    
    /**
     * Sets the locale for translations
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     *
     * @return    ChildAttributeGroupName The current object (for fluent API support)
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
     * @return ChildAttributeGroupNameI18n */
    public function getTranslation($locale = 'en_US', ConnectionInterface $con = null)
    {
        if (!isset($this->currentTranslations[$locale])) {
            if (null !== $this->collAttributeGroupNameI18ns) {
                foreach ($this->collAttributeGroupNameI18ns as $translation) {
                    if ($translation->getLocale() == $locale) {
                        $this->currentTranslations[$locale] = $translation;
    
                        return $translation;
                    }
                }
            }
            if ($this->isNew()) {
                $translation = new ChildAttributeGroupNameI18n();
                $translation->setLocale($locale);
            } else {
                $translation = ChildAttributeGroupNameI18nQuery::create()
                    ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                    ->findOneOrCreate($con);
                $this->currentTranslations[$locale] = $translation;
            }
            $this->addAttributeGroupNameI18n($translation);
        }
    
        return $this->currentTranslations[$locale];
    }
    
    /**
     * Remove the translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return    ChildAttributeGroupName The current object (for fluent API support)
     */
    public function removeTranslation($locale = 'en_US', ConnectionInterface $con = null)
    {
        if (!$this->isNew()) {
            ChildAttributeGroupNameI18nQuery::create()
                ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                ->delete($con);
        }
        if (isset($this->currentTranslations[$locale])) {
            unset($this->currentTranslations[$locale]);
        }
        foreach ($this->collAttributeGroupNameI18ns as $key => $translation) {
            if ($translation->getLocale() == $locale) {
                unset($this->collAttributeGroupNameI18ns[$key]);
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
     * @return ChildAttributeGroupNameI18n */
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
         * @return   \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupNameI18n The current object (for fluent API support)
         */
        public function setName($v)
        {    $this->getCurrentTranslation()->setName($v);
    
        return $this;
    }

    // timestampable behavior
    
    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     ChildAttributeGroupName The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[AttributeGroupNameTableMap::COL_UPDATED_AT] = true;
    
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
