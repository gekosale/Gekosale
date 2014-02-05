<?php

namespace Gekosale\Plugin\Layout\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Layout\Model\ORM\LayoutScheme as ChildLayoutScheme;
use Gekosale\Plugin\Layout\Model\ORM\LayoutSchemeQuery as ChildLayoutSchemeQuery;
use Gekosale\Plugin\Layout\Model\ORM\LayoutSubpage as ChildLayoutSubpage;
use Gekosale\Plugin\Layout\Model\ORM\LayoutSubpageColumn as ChildLayoutSubpageColumn;
use Gekosale\Plugin\Layout\Model\ORM\LayoutSubpageColumnQuery as ChildLayoutSubpageColumnQuery;
use Gekosale\Plugin\Layout\Model\ORM\LayoutSubpageQuery as ChildLayoutSubpageQuery;
use Gekosale\Plugin\Layout\Model\ORM\Subpage as ChildSubpage;
use Gekosale\Plugin\Layout\Model\ORM\SubpageQuery as ChildSubpageQuery;
use Gekosale\Plugin\Layout\Model\ORM\Map\LayoutSubpageTableMap;
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

abstract class LayoutSubpage implements ActiveRecordInterface 
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Gekosale\\Plugin\\Layout\\Model\\ORM\\Map\\LayoutSubpageTableMap';


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
     * The value for the subpage_id field.
     * @var        int
     */
    protected $subpage_id;

    /**
     * The value for the layout_scheme_id field.
     * @var        int
     */
    protected $layout_scheme_id;

    /**
     * @var        LayoutScheme
     */
    protected $aLayoutScheme;

    /**
     * @var        Subpage
     */
    protected $aSubpage;

    /**
     * @var        ObjectCollection|ChildLayoutSubpageColumn[] Collection to store aggregation of ChildLayoutSubpageColumn objects.
     */
    protected $collLayoutSubpageColumns;
    protected $collLayoutSubpageColumnsPartial;

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
    protected $layoutSubpageColumnsScheduledForDeletion = null;

    /**
     * Initializes internal state of Gekosale\Plugin\Layout\Model\ORM\Base\LayoutSubpage object.
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
     * Compares this with another <code>LayoutSubpage</code> instance.  If
     * <code>obj</code> is an instance of <code>LayoutSubpage</code>, delegates to
     * <code>equals(LayoutSubpage)</code>.  Otherwise, returns <code>false</code>.
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
     * @return LayoutSubpage The current object, for fluid interface
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
     * @return LayoutSubpage The current object, for fluid interface
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
     * Get the [subpage_id] column value.
     * 
     * @return   int
     */
    public function getSubpageId()
    {

        return $this->subpage_id;
    }

    /**
     * Get the [layout_scheme_id] column value.
     * 
     * @return   int
     */
    public function getLayoutSchemeId()
    {

        return $this->layout_scheme_id;
    }

    /**
     * Set the value of [id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpage The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[LayoutSubpageTableMap::COL_ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [subpage_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpage The current object (for fluent API support)
     */
    public function setSubpageId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->subpage_id !== $v) {
            $this->subpage_id = $v;
            $this->modifiedColumns[LayoutSubpageTableMap::COL_SUBPAGE_ID] = true;
        }

        if ($this->aSubpage !== null && $this->aSubpage->getId() !== $v) {
            $this->aSubpage = null;
        }


        return $this;
    } // setSubpageId()

    /**
     * Set the value of [layout_scheme_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpage The current object (for fluent API support)
     */
    public function setLayoutSchemeId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->layout_scheme_id !== $v) {
            $this->layout_scheme_id = $v;
            $this->modifiedColumns[LayoutSubpageTableMap::COL_LAYOUT_SCHEME_ID] = true;
        }

        if ($this->aLayoutScheme !== null && $this->aLayoutScheme->getId() !== $v) {
            $this->aLayoutScheme = null;
        }


        return $this;
    } // setLayoutSchemeId()

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


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : LayoutSubpageTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : LayoutSubpageTableMap::translateFieldName('SubpageId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->subpage_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : LayoutSubpageTableMap::translateFieldName('LayoutSchemeId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->layout_scheme_id = (null !== $col) ? (int) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 3; // 3 = LayoutSubpageTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpage object", 0, $e);
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
        if ($this->aSubpage !== null && $this->subpage_id !== $this->aSubpage->getId()) {
            $this->aSubpage = null;
        }
        if ($this->aLayoutScheme !== null && $this->layout_scheme_id !== $this->aLayoutScheme->getId()) {
            $this->aLayoutScheme = null;
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
            $con = Propel::getServiceContainer()->getReadConnection(LayoutSubpageTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildLayoutSubpageQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aLayoutScheme = null;
            $this->aSubpage = null;
            $this->collLayoutSubpageColumns = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see LayoutSubpage::setDeleted()
     * @see LayoutSubpage::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(LayoutSubpageTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildLayoutSubpageQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(LayoutSubpageTableMap::DATABASE_NAME);
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
                LayoutSubpageTableMap::addInstanceToPool($this);
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

            if ($this->aLayoutScheme !== null) {
                if ($this->aLayoutScheme->isModified() || $this->aLayoutScheme->isNew()) {
                    $affectedRows += $this->aLayoutScheme->save($con);
                }
                $this->setLayoutScheme($this->aLayoutScheme);
            }

            if ($this->aSubpage !== null) {
                if ($this->aSubpage->isModified() || $this->aSubpage->isNew()) {
                    $affectedRows += $this->aSubpage->save($con);
                }
                $this->setSubpage($this->aSubpage);
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

            if ($this->layoutSubpageColumnsScheduledForDeletion !== null) {
                if (!$this->layoutSubpageColumnsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpageColumnQuery::create()
                        ->filterByPrimaryKeys($this->layoutSubpageColumnsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->layoutSubpageColumnsScheduledForDeletion = null;
                }
            }

                if ($this->collLayoutSubpageColumns !== null) {
            foreach ($this->collLayoutSubpageColumns as $referrerFK) {
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

        $this->modifiedColumns[LayoutSubpageTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . LayoutSubpageTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(LayoutSubpageTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(LayoutSubpageTableMap::COL_SUBPAGE_ID)) {
            $modifiedColumns[':p' . $index++]  = 'SUBPAGE_ID';
        }
        if ($this->isColumnModified(LayoutSubpageTableMap::COL_LAYOUT_SCHEME_ID)) {
            $modifiedColumns[':p' . $index++]  = 'LAYOUT_SCHEME_ID';
        }

        $sql = sprintf(
            'INSERT INTO layout_subpage (%s) VALUES (%s)',
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
                    case 'SUBPAGE_ID':                        
                        $stmt->bindValue($identifier, $this->subpage_id, PDO::PARAM_INT);
                        break;
                    case 'LAYOUT_SCHEME_ID':                        
                        $stmt->bindValue($identifier, $this->layout_scheme_id, PDO::PARAM_INT);
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
        $pos = LayoutSubpageTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getSubpageId();
                break;
            case 2:
                return $this->getLayoutSchemeId();
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
        if (isset($alreadyDumpedObjects['LayoutSubpage'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['LayoutSubpage'][$this->getPrimaryKey()] = true;
        $keys = LayoutSubpageTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getSubpageId(),
            $keys[2] => $this->getLayoutSchemeId(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }
        
        if ($includeForeignObjects) {
            if (null !== $this->aLayoutScheme) {
                $result['LayoutScheme'] = $this->aLayoutScheme->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aSubpage) {
                $result['Subpage'] = $this->aSubpage->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collLayoutSubpageColumns) {
                $result['LayoutSubpageColumns'] = $this->collLayoutSubpageColumns->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = LayoutSubpageTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

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
                $this->setSubpageId($value);
                break;
            case 2:
                $this->setLayoutSchemeId($value);
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
        $keys = LayoutSubpageTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setSubpageId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setLayoutSchemeId($arr[$keys[2]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(LayoutSubpageTableMap::DATABASE_NAME);

        if ($this->isColumnModified(LayoutSubpageTableMap::COL_ID)) $criteria->add(LayoutSubpageTableMap::COL_ID, $this->id);
        if ($this->isColumnModified(LayoutSubpageTableMap::COL_SUBPAGE_ID)) $criteria->add(LayoutSubpageTableMap::COL_SUBPAGE_ID, $this->subpage_id);
        if ($this->isColumnModified(LayoutSubpageTableMap::COL_LAYOUT_SCHEME_ID)) $criteria->add(LayoutSubpageTableMap::COL_LAYOUT_SCHEME_ID, $this->layout_scheme_id);

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
        $criteria = new Criteria(LayoutSubpageTableMap::DATABASE_NAME);
        $criteria->add(LayoutSubpageTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpage (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setSubpageId($this->getSubpageId());
        $copyObj->setLayoutSchemeId($this->getLayoutSchemeId());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getLayoutSubpageColumns() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addLayoutSubpageColumn($relObj->copy($deepCopy));
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
     * @return                 \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpage Clone of current object.
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
     * Declares an association between this object and a ChildLayoutScheme object.
     *
     * @param                  ChildLayoutScheme $v
     * @return                 \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpage The current object (for fluent API support)
     * @throws PropelException
     */
    public function setLayoutScheme(ChildLayoutScheme $v = null)
    {
        if ($v === null) {
            $this->setLayoutSchemeId(NULL);
        } else {
            $this->setLayoutSchemeId($v->getId());
        }

        $this->aLayoutScheme = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildLayoutScheme object, it will not be re-added.
        if ($v !== null) {
            $v->addLayoutSubpage($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildLayoutScheme object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildLayoutScheme The associated ChildLayoutScheme object.
     * @throws PropelException
     */
    public function getLayoutScheme(ConnectionInterface $con = null)
    {
        if ($this->aLayoutScheme === null && ($this->layout_scheme_id !== null)) {
            $this->aLayoutScheme = ChildLayoutSchemeQuery::create()->findPk($this->layout_scheme_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aLayoutScheme->addLayoutSubpages($this);
             */
        }

        return $this->aLayoutScheme;
    }

    /**
     * Declares an association between this object and a ChildSubpage object.
     *
     * @param                  ChildSubpage $v
     * @return                 \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpage The current object (for fluent API support)
     * @throws PropelException
     */
    public function setSubpage(ChildSubpage $v = null)
    {
        if ($v === null) {
            $this->setSubpageId(NULL);
        } else {
            $this->setSubpageId($v->getId());
        }

        $this->aSubpage = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildSubpage object, it will not be re-added.
        if ($v !== null) {
            $v->addLayoutSubpage($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildSubpage object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildSubpage The associated ChildSubpage object.
     * @throws PropelException
     */
    public function getSubpage(ConnectionInterface $con = null)
    {
        if ($this->aSubpage === null && ($this->subpage_id !== null)) {
            $this->aSubpage = ChildSubpageQuery::create()->findPk($this->subpage_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aSubpage->addLayoutSubpages($this);
             */
        }

        return $this->aSubpage;
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
        if ('LayoutSubpageColumn' == $relationName) {
            return $this->initLayoutSubpageColumns();
        }
    }

    /**
     * Clears out the collLayoutSubpageColumns collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addLayoutSubpageColumns()
     */
    public function clearLayoutSubpageColumns()
    {
        $this->collLayoutSubpageColumns = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collLayoutSubpageColumns collection loaded partially.
     */
    public function resetPartialLayoutSubpageColumns($v = true)
    {
        $this->collLayoutSubpageColumnsPartial = $v;
    }

    /**
     * Initializes the collLayoutSubpageColumns collection.
     *
     * By default this just sets the collLayoutSubpageColumns collection to an empty array (like clearcollLayoutSubpageColumns());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initLayoutSubpageColumns($overrideExisting = true)
    {
        if (null !== $this->collLayoutSubpageColumns && !$overrideExisting) {
            return;
        }
        $this->collLayoutSubpageColumns = new ObjectCollection();
        $this->collLayoutSubpageColumns->setModel('\Gekosale\Plugin\Layout\Model\ORM\LayoutSubpageColumn');
    }

    /**
     * Gets an array of ChildLayoutSubpageColumn objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildLayoutSubpage is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildLayoutSubpageColumn[] List of ChildLayoutSubpageColumn objects
     * @throws PropelException
     */
    public function getLayoutSubpageColumns($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collLayoutSubpageColumnsPartial && !$this->isNew();
        if (null === $this->collLayoutSubpageColumns || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collLayoutSubpageColumns) {
                // return empty collection
                $this->initLayoutSubpageColumns();
            } else {
                $collLayoutSubpageColumns = ChildLayoutSubpageColumnQuery::create(null, $criteria)
                    ->filterByLayoutSubpage($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collLayoutSubpageColumnsPartial && count($collLayoutSubpageColumns)) {
                        $this->initLayoutSubpageColumns(false);

                        foreach ($collLayoutSubpageColumns as $obj) {
                            if (false == $this->collLayoutSubpageColumns->contains($obj)) {
                                $this->collLayoutSubpageColumns->append($obj);
                            }
                        }

                        $this->collLayoutSubpageColumnsPartial = true;
                    }

                    reset($collLayoutSubpageColumns);

                    return $collLayoutSubpageColumns;
                }

                if ($partial && $this->collLayoutSubpageColumns) {
                    foreach ($this->collLayoutSubpageColumns as $obj) {
                        if ($obj->isNew()) {
                            $collLayoutSubpageColumns[] = $obj;
                        }
                    }
                }

                $this->collLayoutSubpageColumns = $collLayoutSubpageColumns;
                $this->collLayoutSubpageColumnsPartial = false;
            }
        }

        return $this->collLayoutSubpageColumns;
    }

    /**
     * Sets a collection of LayoutSubpageColumn objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $layoutSubpageColumns A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildLayoutSubpage The current object (for fluent API support)
     */
    public function setLayoutSubpageColumns(Collection $layoutSubpageColumns, ConnectionInterface $con = null)
    {
        $layoutSubpageColumnsToDelete = $this->getLayoutSubpageColumns(new Criteria(), $con)->diff($layoutSubpageColumns);

        
        $this->layoutSubpageColumnsScheduledForDeletion = $layoutSubpageColumnsToDelete;

        foreach ($layoutSubpageColumnsToDelete as $layoutSubpageColumnRemoved) {
            $layoutSubpageColumnRemoved->setLayoutSubpage(null);
        }

        $this->collLayoutSubpageColumns = null;
        foreach ($layoutSubpageColumns as $layoutSubpageColumn) {
            $this->addLayoutSubpageColumn($layoutSubpageColumn);
        }

        $this->collLayoutSubpageColumns = $layoutSubpageColumns;
        $this->collLayoutSubpageColumnsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related LayoutSubpageColumn objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related LayoutSubpageColumn objects.
     * @throws PropelException
     */
    public function countLayoutSubpageColumns(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collLayoutSubpageColumnsPartial && !$this->isNew();
        if (null === $this->collLayoutSubpageColumns || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collLayoutSubpageColumns) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getLayoutSubpageColumns());
            }

            $query = ChildLayoutSubpageColumnQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByLayoutSubpage($this)
                ->count($con);
        }

        return count($this->collLayoutSubpageColumns);
    }

    /**
     * Method called to associate a ChildLayoutSubpageColumn object to this object
     * through the ChildLayoutSubpageColumn foreign key attribute.
     *
     * @param    ChildLayoutSubpageColumn $l ChildLayoutSubpageColumn
     * @return   \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpage The current object (for fluent API support)
     */
    public function addLayoutSubpageColumn(ChildLayoutSubpageColumn $l)
    {
        if ($this->collLayoutSubpageColumns === null) {
            $this->initLayoutSubpageColumns();
            $this->collLayoutSubpageColumnsPartial = true;
        }

        if (!in_array($l, $this->collLayoutSubpageColumns->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddLayoutSubpageColumn($l);
        }

        return $this;
    }

    /**
     * @param LayoutSubpageColumn $layoutSubpageColumn The layoutSubpageColumn object to add.
     */
    protected function doAddLayoutSubpageColumn($layoutSubpageColumn)
    {
        $this->collLayoutSubpageColumns[]= $layoutSubpageColumn;
        $layoutSubpageColumn->setLayoutSubpage($this);
    }

    /**
     * @param  LayoutSubpageColumn $layoutSubpageColumn The layoutSubpageColumn object to remove.
     * @return ChildLayoutSubpage The current object (for fluent API support)
     */
    public function removeLayoutSubpageColumn($layoutSubpageColumn)
    {
        if ($this->getLayoutSubpageColumns()->contains($layoutSubpageColumn)) {
            $this->collLayoutSubpageColumns->remove($this->collLayoutSubpageColumns->search($layoutSubpageColumn));
            if (null === $this->layoutSubpageColumnsScheduledForDeletion) {
                $this->layoutSubpageColumnsScheduledForDeletion = clone $this->collLayoutSubpageColumns;
                $this->layoutSubpageColumnsScheduledForDeletion->clear();
            }
            $this->layoutSubpageColumnsScheduledForDeletion[]= clone $layoutSubpageColumn;
            $layoutSubpageColumn->setLayoutSubpage(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->subpage_id = null;
        $this->layout_scheme_id = null;
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
            if ($this->collLayoutSubpageColumns) {
                foreach ($this->collLayoutSubpageColumns as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collLayoutSubpageColumns = null;
        $this->aLayoutScheme = null;
        $this->aSubpage = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(LayoutSubpageTableMap::DEFAULT_STRING_FORMAT);
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
