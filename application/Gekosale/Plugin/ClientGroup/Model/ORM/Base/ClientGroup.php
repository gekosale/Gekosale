<?php

namespace Gekosale\Plugin\ClientGroup\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\CartRule\Model\ORM\CartRuleClientGroupQuery;
use Gekosale\Plugin\CartRule\Model\ORM\CartRuleClientGroup as ChildCartRuleClientGroup;
use Gekosale\Plugin\CartRule\Model\ORM\Base\CartRuleClientGroup;
use Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroup as ChildClientGroup;
use Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroupQuery as ChildClientGroupQuery;
use Gekosale\Plugin\ClientGroup\Model\ORM\Map\ClientGroupTableMap;
use Gekosale\Plugin\Client\Model\ORM\ClientData as ChildClientData;
use Gekosale\Plugin\Client\Model\ORM\ClientDataQuery;
use Gekosale\Plugin\Client\Model\ORM\Base\ClientData;
use Gekosale\Plugin\ProductGroupPrice\Model\ORM\ProductGroupPrice as ChildProductGroupPrice;
use Gekosale\Plugin\ProductGroupPrice\Model\ORM\ProductGroupPriceQuery;
use Gekosale\Plugin\ProductGroupPrice\Model\ORM\Base\ProductGroupPrice;
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

abstract class ClientGroup implements ActiveRecordInterface 
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Gekosale\\Plugin\\ClientGroup\\Model\\ORM\\Map\\ClientGroupTableMap';


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
     * @var        ObjectCollection|ChildCartRuleClientGroup[] Collection to store aggregation of ChildCartRuleClientGroup objects.
     */
    protected $collCartRuleClientGroups;
    protected $collCartRuleClientGroupsPartial;

    /**
     * @var        ObjectCollection|ChildClientData[] Collection to store aggregation of ChildClientData objects.
     */
    protected $collClientDatas;
    protected $collClientDatasPartial;

    /**
     * @var        ObjectCollection|ChildProductGroupPrice[] Collection to store aggregation of ChildProductGroupPrice objects.
     */
    protected $collProductGroupPrices;
    protected $collProductGroupPricesPartial;

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
    protected $cartRuleClientGroupsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $clientDatasScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $productGroupPricesScheduledForDeletion = null;

    /**
     * Initializes internal state of Gekosale\Plugin\ClientGroup\Model\ORM\Base\ClientGroup object.
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
     * Compares this with another <code>ClientGroup</code> instance.  If
     * <code>obj</code> is an instance of <code>ClientGroup</code>, delegates to
     * <code>equals(ClientGroup)</code>.  Otherwise, returns <code>false</code>.
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
     * @return ClientGroup The current object, for fluid interface
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
     * @return ClientGroup The current object, for fluid interface
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
     * Set the value of [id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroup The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[ClientGroupTableMap::COL_ID] = true;
        }


        return $this;
    } // setId()

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


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : ClientGroupTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 1; // 1 = ClientGroupTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroup object", 0, $e);
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
            $con = Propel::getServiceContainer()->getReadConnection(ClientGroupTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildClientGroupQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collCartRuleClientGroups = null;

            $this->collClientDatas = null;

            $this->collProductGroupPrices = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see ClientGroup::setDeleted()
     * @see ClientGroup::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(ClientGroupTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildClientGroupQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(ClientGroupTableMap::DATABASE_NAME);
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
                ClientGroupTableMap::addInstanceToPool($this);
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

            if ($this->cartRuleClientGroupsScheduledForDeletion !== null) {
                if (!$this->cartRuleClientGroupsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\CartRule\Model\ORM\CartRuleClientGroupQuery::create()
                        ->filterByPrimaryKeys($this->cartRuleClientGroupsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->cartRuleClientGroupsScheduledForDeletion = null;
                }
            }

                if ($this->collCartRuleClientGroups !== null) {
            foreach ($this->collCartRuleClientGroups as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->clientDatasScheduledForDeletion !== null) {
                if (!$this->clientDatasScheduledForDeletion->isEmpty()) {
                    foreach ($this->clientDatasScheduledForDeletion as $clientData) {
                        // need to save related object because we set the relation to null
                        $clientData->save($con);
                    }
                    $this->clientDatasScheduledForDeletion = null;
                }
            }

                if ($this->collClientDatas !== null) {
            foreach ($this->collClientDatas as $referrerFK) {
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

        $this->modifiedColumns[ClientGroupTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . ClientGroupTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ClientGroupTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }

        $sql = sprintf(
            'INSERT INTO client_group (%s) VALUES (%s)',
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
        $pos = ClientGroupTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
        if (isset($alreadyDumpedObjects['ClientGroup'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['ClientGroup'][$this->getPrimaryKey()] = true;
        $keys = ClientGroupTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }
        
        if ($includeForeignObjects) {
            if (null !== $this->collCartRuleClientGroups) {
                $result['CartRuleClientGroups'] = $this->collCartRuleClientGroups->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collClientDatas) {
                $result['ClientDatas'] = $this->collClientDatas->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductGroupPrices) {
                $result['ProductGroupPrices'] = $this->collProductGroupPrices->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = ClientGroupTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

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
        $keys = ClientGroupTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ClientGroupTableMap::DATABASE_NAME);

        if ($this->isColumnModified(ClientGroupTableMap::COL_ID)) $criteria->add(ClientGroupTableMap::COL_ID, $this->id);

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
        $criteria = new Criteria(ClientGroupTableMap::DATABASE_NAME);
        $criteria->add(ClientGroupTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroup (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getCartRuleClientGroups() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCartRuleClientGroup($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getClientDatas() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addClientData($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductGroupPrices() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductGroupPrice($relObj->copy($deepCopy));
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
     * @return                 \Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroup Clone of current object.
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
        if ('CartRuleClientGroup' == $relationName) {
            return $this->initCartRuleClientGroups();
        }
        if ('ClientData' == $relationName) {
            return $this->initClientDatas();
        }
        if ('ProductGroupPrice' == $relationName) {
            return $this->initProductGroupPrices();
        }
    }

    /**
     * Clears out the collCartRuleClientGroups collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCartRuleClientGroups()
     */
    public function clearCartRuleClientGroups()
    {
        $this->collCartRuleClientGroups = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCartRuleClientGroups collection loaded partially.
     */
    public function resetPartialCartRuleClientGroups($v = true)
    {
        $this->collCartRuleClientGroupsPartial = $v;
    }

    /**
     * Initializes the collCartRuleClientGroups collection.
     *
     * By default this just sets the collCartRuleClientGroups collection to an empty array (like clearcollCartRuleClientGroups());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCartRuleClientGroups($overrideExisting = true)
    {
        if (null !== $this->collCartRuleClientGroups && !$overrideExisting) {
            return;
        }
        $this->collCartRuleClientGroups = new ObjectCollection();
        $this->collCartRuleClientGroups->setModel('\Gekosale\Plugin\CartRule\Model\ORM\CartRuleClientGroup');
    }

    /**
     * Gets an array of ChildCartRuleClientGroup objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildClientGroup is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildCartRuleClientGroup[] List of ChildCartRuleClientGroup objects
     * @throws PropelException
     */
    public function getCartRuleClientGroups($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCartRuleClientGroupsPartial && !$this->isNew();
        if (null === $this->collCartRuleClientGroups || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCartRuleClientGroups) {
                // return empty collection
                $this->initCartRuleClientGroups();
            } else {
                $collCartRuleClientGroups = CartRuleClientGroupQuery::create(null, $criteria)
                    ->filterByClientGroup($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCartRuleClientGroupsPartial && count($collCartRuleClientGroups)) {
                        $this->initCartRuleClientGroups(false);

                        foreach ($collCartRuleClientGroups as $obj) {
                            if (false == $this->collCartRuleClientGroups->contains($obj)) {
                                $this->collCartRuleClientGroups->append($obj);
                            }
                        }

                        $this->collCartRuleClientGroupsPartial = true;
                    }

                    reset($collCartRuleClientGroups);

                    return $collCartRuleClientGroups;
                }

                if ($partial && $this->collCartRuleClientGroups) {
                    foreach ($this->collCartRuleClientGroups as $obj) {
                        if ($obj->isNew()) {
                            $collCartRuleClientGroups[] = $obj;
                        }
                    }
                }

                $this->collCartRuleClientGroups = $collCartRuleClientGroups;
                $this->collCartRuleClientGroupsPartial = false;
            }
        }

        return $this->collCartRuleClientGroups;
    }

    /**
     * Sets a collection of CartRuleClientGroup objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $cartRuleClientGroups A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildClientGroup The current object (for fluent API support)
     */
    public function setCartRuleClientGroups(Collection $cartRuleClientGroups, ConnectionInterface $con = null)
    {
        $cartRuleClientGroupsToDelete = $this->getCartRuleClientGroups(new Criteria(), $con)->diff($cartRuleClientGroups);

        
        $this->cartRuleClientGroupsScheduledForDeletion = $cartRuleClientGroupsToDelete;

        foreach ($cartRuleClientGroupsToDelete as $cartRuleClientGroupRemoved) {
            $cartRuleClientGroupRemoved->setClientGroup(null);
        }

        $this->collCartRuleClientGroups = null;
        foreach ($cartRuleClientGroups as $cartRuleClientGroup) {
            $this->addCartRuleClientGroup($cartRuleClientGroup);
        }

        $this->collCartRuleClientGroups = $cartRuleClientGroups;
        $this->collCartRuleClientGroupsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CartRuleClientGroup objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related CartRuleClientGroup objects.
     * @throws PropelException
     */
    public function countCartRuleClientGroups(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCartRuleClientGroupsPartial && !$this->isNew();
        if (null === $this->collCartRuleClientGroups || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCartRuleClientGroups) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCartRuleClientGroups());
            }

            $query = CartRuleClientGroupQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByClientGroup($this)
                ->count($con);
        }

        return count($this->collCartRuleClientGroups);
    }

    /**
     * Method called to associate a ChildCartRuleClientGroup object to this object
     * through the ChildCartRuleClientGroup foreign key attribute.
     *
     * @param    ChildCartRuleClientGroup $l ChildCartRuleClientGroup
     * @return   \Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroup The current object (for fluent API support)
     */
    public function addCartRuleClientGroup(ChildCartRuleClientGroup $l)
    {
        if ($this->collCartRuleClientGroups === null) {
            $this->initCartRuleClientGroups();
            $this->collCartRuleClientGroupsPartial = true;
        }

        if (!in_array($l, $this->collCartRuleClientGroups->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCartRuleClientGroup($l);
        }

        return $this;
    }

    /**
     * @param CartRuleClientGroup $cartRuleClientGroup The cartRuleClientGroup object to add.
     */
    protected function doAddCartRuleClientGroup($cartRuleClientGroup)
    {
        $this->collCartRuleClientGroups[]= $cartRuleClientGroup;
        $cartRuleClientGroup->setClientGroup($this);
    }

    /**
     * @param  CartRuleClientGroup $cartRuleClientGroup The cartRuleClientGroup object to remove.
     * @return ChildClientGroup The current object (for fluent API support)
     */
    public function removeCartRuleClientGroup($cartRuleClientGroup)
    {
        if ($this->getCartRuleClientGroups()->contains($cartRuleClientGroup)) {
            $this->collCartRuleClientGroups->remove($this->collCartRuleClientGroups->search($cartRuleClientGroup));
            if (null === $this->cartRuleClientGroupsScheduledForDeletion) {
                $this->cartRuleClientGroupsScheduledForDeletion = clone $this->collCartRuleClientGroups;
                $this->cartRuleClientGroupsScheduledForDeletion->clear();
            }
            $this->cartRuleClientGroupsScheduledForDeletion[]= clone $cartRuleClientGroup;
            $cartRuleClientGroup->setClientGroup(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this ClientGroup is new, it will return
     * an empty collection; or if this ClientGroup has previously
     * been saved, it will retrieve related CartRuleClientGroups from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in ClientGroup.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCartRuleClientGroup[] List of ChildCartRuleClientGroup objects
     */
    public function getCartRuleClientGroupsJoinCartRule($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = CartRuleClientGroupQuery::create(null, $criteria);
        $query->joinWith('CartRule', $joinBehavior);

        return $this->getCartRuleClientGroups($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this ClientGroup is new, it will return
     * an empty collection; or if this ClientGroup has previously
     * been saved, it will retrieve related CartRuleClientGroups from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in ClientGroup.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCartRuleClientGroup[] List of ChildCartRuleClientGroup objects
     */
    public function getCartRuleClientGroupsJoinSuffixType($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = CartRuleClientGroupQuery::create(null, $criteria);
        $query->joinWith('SuffixType', $joinBehavior);

        return $this->getCartRuleClientGroups($query, $con);
    }

    /**
     * Clears out the collClientDatas collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addClientDatas()
     */
    public function clearClientDatas()
    {
        $this->collClientDatas = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collClientDatas collection loaded partially.
     */
    public function resetPartialClientDatas($v = true)
    {
        $this->collClientDatasPartial = $v;
    }

    /**
     * Initializes the collClientDatas collection.
     *
     * By default this just sets the collClientDatas collection to an empty array (like clearcollClientDatas());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initClientDatas($overrideExisting = true)
    {
        if (null !== $this->collClientDatas && !$overrideExisting) {
            return;
        }
        $this->collClientDatas = new ObjectCollection();
        $this->collClientDatas->setModel('\Gekosale\Plugin\Client\Model\ORM\ClientData');
    }

    /**
     * Gets an array of ChildClientData objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildClientGroup is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildClientData[] List of ChildClientData objects
     * @throws PropelException
     */
    public function getClientDatas($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collClientDatasPartial && !$this->isNew();
        if (null === $this->collClientDatas || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collClientDatas) {
                // return empty collection
                $this->initClientDatas();
            } else {
                $collClientDatas = ClientDataQuery::create(null, $criteria)
                    ->filterByClientGroup($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collClientDatasPartial && count($collClientDatas)) {
                        $this->initClientDatas(false);

                        foreach ($collClientDatas as $obj) {
                            if (false == $this->collClientDatas->contains($obj)) {
                                $this->collClientDatas->append($obj);
                            }
                        }

                        $this->collClientDatasPartial = true;
                    }

                    reset($collClientDatas);

                    return $collClientDatas;
                }

                if ($partial && $this->collClientDatas) {
                    foreach ($this->collClientDatas as $obj) {
                        if ($obj->isNew()) {
                            $collClientDatas[] = $obj;
                        }
                    }
                }

                $this->collClientDatas = $collClientDatas;
                $this->collClientDatasPartial = false;
            }
        }

        return $this->collClientDatas;
    }

    /**
     * Sets a collection of ClientData objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $clientDatas A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildClientGroup The current object (for fluent API support)
     */
    public function setClientDatas(Collection $clientDatas, ConnectionInterface $con = null)
    {
        $clientDatasToDelete = $this->getClientDatas(new Criteria(), $con)->diff($clientDatas);

        
        $this->clientDatasScheduledForDeletion = $clientDatasToDelete;

        foreach ($clientDatasToDelete as $clientDataRemoved) {
            $clientDataRemoved->setClientGroup(null);
        }

        $this->collClientDatas = null;
        foreach ($clientDatas as $clientData) {
            $this->addClientData($clientData);
        }

        $this->collClientDatas = $clientDatas;
        $this->collClientDatasPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ClientData objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ClientData objects.
     * @throws PropelException
     */
    public function countClientDatas(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collClientDatasPartial && !$this->isNew();
        if (null === $this->collClientDatas || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collClientDatas) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getClientDatas());
            }

            $query = ClientDataQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByClientGroup($this)
                ->count($con);
        }

        return count($this->collClientDatas);
    }

    /**
     * Method called to associate a ChildClientData object to this object
     * through the ChildClientData foreign key attribute.
     *
     * @param    ChildClientData $l ChildClientData
     * @return   \Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroup The current object (for fluent API support)
     */
    public function addClientData(ChildClientData $l)
    {
        if ($this->collClientDatas === null) {
            $this->initClientDatas();
            $this->collClientDatasPartial = true;
        }

        if (!in_array($l, $this->collClientDatas->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddClientData($l);
        }

        return $this;
    }

    /**
     * @param ClientData $clientData The clientData object to add.
     */
    protected function doAddClientData($clientData)
    {
        $this->collClientDatas[]= $clientData;
        $clientData->setClientGroup($this);
    }

    /**
     * @param  ClientData $clientData The clientData object to remove.
     * @return ChildClientGroup The current object (for fluent API support)
     */
    public function removeClientData($clientData)
    {
        if ($this->getClientDatas()->contains($clientData)) {
            $this->collClientDatas->remove($this->collClientDatas->search($clientData));
            if (null === $this->clientDatasScheduledForDeletion) {
                $this->clientDatasScheduledForDeletion = clone $this->collClientDatas;
                $this->clientDatasScheduledForDeletion->clear();
            }
            $this->clientDatasScheduledForDeletion[]= $clientData;
            $clientData->setClientGroup(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this ClientGroup is new, it will return
     * an empty collection; or if this ClientGroup has previously
     * been saved, it will retrieve related ClientDatas from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in ClientGroup.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildClientData[] List of ChildClientData objects
     */
    public function getClientDatasJoinClient($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ClientDataQuery::create(null, $criteria);
        $query->joinWith('Client', $joinBehavior);

        return $this->getClientDatas($query, $con);
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
     * If this ChildClientGroup is new, it will return
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
                    ->filterByClientGroup($this)
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
     * @return   ChildClientGroup The current object (for fluent API support)
     */
    public function setProductGroupPrices(Collection $productGroupPrices, ConnectionInterface $con = null)
    {
        $productGroupPricesToDelete = $this->getProductGroupPrices(new Criteria(), $con)->diff($productGroupPrices);

        
        $this->productGroupPricesScheduledForDeletion = $productGroupPricesToDelete;

        foreach ($productGroupPricesToDelete as $productGroupPriceRemoved) {
            $productGroupPriceRemoved->setClientGroup(null);
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
                ->filterByClientGroup($this)
                ->count($con);
        }

        return count($this->collProductGroupPrices);
    }

    /**
     * Method called to associate a ChildProductGroupPrice object to this object
     * through the ChildProductGroupPrice foreign key attribute.
     *
     * @param    ChildProductGroupPrice $l ChildProductGroupPrice
     * @return   \Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroup The current object (for fluent API support)
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
        $productGroupPrice->setClientGroup($this);
    }

    /**
     * @param  ProductGroupPrice $productGroupPrice The productGroupPrice object to remove.
     * @return ChildClientGroup The current object (for fluent API support)
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
            $productGroupPrice->setClientGroup(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this ClientGroup is new, it will return
     * an empty collection; or if this ClientGroup has previously
     * been saved, it will retrieve related ProductGroupPrices from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in ClientGroup.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProductGroupPrice[] List of ChildProductGroupPrice objects
     */
    public function getProductGroupPricesJoinProduct($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ProductGroupPriceQuery::create(null, $criteria);
        $query->joinWith('Product', $joinBehavior);

        return $this->getProductGroupPrices($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
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
            if ($this->collCartRuleClientGroups) {
                foreach ($this->collCartRuleClientGroups as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collClientDatas) {
                foreach ($this->collClientDatas as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductGroupPrices) {
                foreach ($this->collProductGroupPrices as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collCartRuleClientGroups = null;
        $this->collClientDatas = null;
        $this->collProductGroupPrices = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ClientGroupTableMap::DEFAULT_STRING_FORMAT);
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
