<?php

namespace Gekosale\Plugin\CartRule\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\CartRule\Model\ORM\CartRuleRule as ChildCartRuleRule;
use Gekosale\Plugin\CartRule\Model\ORM\CartRuleRuleQuery as ChildCartRuleRuleQuery;
use Gekosale\Plugin\CartRule\Model\ORM\Rule as ChildRule;
use Gekosale\Plugin\CartRule\Model\ORM\RuleQuery as ChildRuleQuery;
use Gekosale\Plugin\CartRule\Model\ORM\Map\RuleTableMap;
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

abstract class Rule implements ActiveRecordInterface 
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Gekosale\\Plugin\\CartRule\\Model\\ORM\\Map\\RuleTableMap';


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
     * The value for the table_referer field.
     * @var        string
     */
    protected $table_referer;

    /**
     * The value for the primary_key_referer field.
     * @var        string
     */
    protected $primary_key_referer;

    /**
     * The value for the column_referer field.
     * @var        string
     */
    protected $column_referer;

    /**
     * The value for the rule_type_id field.
     * @var        int
     */
    protected $rule_type_id;

    /**
     * The value for the field field.
     * @var        string
     */
    protected $field;

    /**
     * @var        ObjectCollection|ChildCartRuleRule[] Collection to store aggregation of ChildCartRuleRule objects.
     */
    protected $collCartRuleRules;
    protected $collCartRuleRulesPartial;

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
    protected $cartRuleRulesScheduledForDeletion = null;

    /**
     * Initializes internal state of Gekosale\Plugin\CartRule\Model\ORM\Base\Rule object.
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
     * Compares this with another <code>Rule</code> instance.  If
     * <code>obj</code> is an instance of <code>Rule</code>, delegates to
     * <code>equals(Rule)</code>.  Otherwise, returns <code>false</code>.
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
     * @return Rule The current object, for fluid interface
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
     * @return Rule The current object, for fluid interface
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
     * Get the [table_referer] column value.
     * 
     * @return   string
     */
    public function getTableReferer()
    {

        return $this->table_referer;
    }

    /**
     * Get the [primary_key_referer] column value.
     * 
     * @return   string
     */
    public function getPrimaryKeyReferer()
    {

        return $this->primary_key_referer;
    }

    /**
     * Get the [column_referer] column value.
     * 
     * @return   string
     */
    public function getColumnReferer()
    {

        return $this->column_referer;
    }

    /**
     * Get the [rule_type_id] column value.
     * 
     * @return   int
     */
    public function getRuleTypeId()
    {

        return $this->rule_type_id;
    }

    /**
     * Get the [field] column value.
     * 
     * @return   string
     */
    public function getField()
    {

        return $this->field;
    }

    /**
     * Set the value of [id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\CartRule\Model\ORM\Rule The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[RuleTableMap::COL_ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [name] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\CartRule\Model\ORM\Rule The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[RuleTableMap::COL_NAME] = true;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [table_referer] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\CartRule\Model\ORM\Rule The current object (for fluent API support)
     */
    public function setTableReferer($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->table_referer !== $v) {
            $this->table_referer = $v;
            $this->modifiedColumns[RuleTableMap::COL_TABLE_REFERER] = true;
        }


        return $this;
    } // setTableReferer()

    /**
     * Set the value of [primary_key_referer] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\CartRule\Model\ORM\Rule The current object (for fluent API support)
     */
    public function setPrimaryKeyReferer($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->primary_key_referer !== $v) {
            $this->primary_key_referer = $v;
            $this->modifiedColumns[RuleTableMap::COL_PRIMARY_KEY_REFERER] = true;
        }


        return $this;
    } // setPrimaryKeyReferer()

    /**
     * Set the value of [column_referer] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\CartRule\Model\ORM\Rule The current object (for fluent API support)
     */
    public function setColumnReferer($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->column_referer !== $v) {
            $this->column_referer = $v;
            $this->modifiedColumns[RuleTableMap::COL_COLUMN_REFERER] = true;
        }


        return $this;
    } // setColumnReferer()

    /**
     * Set the value of [rule_type_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\CartRule\Model\ORM\Rule The current object (for fluent API support)
     */
    public function setRuleTypeId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->rule_type_id !== $v) {
            $this->rule_type_id = $v;
            $this->modifiedColumns[RuleTableMap::COL_RULE_TYPE_ID] = true;
        }


        return $this;
    } // setRuleTypeId()

    /**
     * Set the value of [field] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\CartRule\Model\ORM\Rule The current object (for fluent API support)
     */
    public function setField($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->field !== $v) {
            $this->field = $v;
            $this->modifiedColumns[RuleTableMap::COL_FIELD] = true;
        }


        return $this;
    } // setField()

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


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : RuleTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : RuleTableMap::translateFieldName('Name', TableMap::TYPE_PHPNAME, $indexType)];
            $this->name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : RuleTableMap::translateFieldName('TableReferer', TableMap::TYPE_PHPNAME, $indexType)];
            $this->table_referer = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : RuleTableMap::translateFieldName('PrimaryKeyReferer', TableMap::TYPE_PHPNAME, $indexType)];
            $this->primary_key_referer = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : RuleTableMap::translateFieldName('ColumnReferer', TableMap::TYPE_PHPNAME, $indexType)];
            $this->column_referer = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : RuleTableMap::translateFieldName('RuleTypeId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->rule_type_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : RuleTableMap::translateFieldName('Field', TableMap::TYPE_PHPNAME, $indexType)];
            $this->field = (null !== $col) ? (string) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 7; // 7 = RuleTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Gekosale\Plugin\CartRule\Model\ORM\Rule object", 0, $e);
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
            $con = Propel::getServiceContainer()->getReadConnection(RuleTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildRuleQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collCartRuleRules = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Rule::setDeleted()
     * @see Rule::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(RuleTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildRuleQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(RuleTableMap::DATABASE_NAME);
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
                RuleTableMap::addInstanceToPool($this);
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

            if ($this->cartRuleRulesScheduledForDeletion !== null) {
                if (!$this->cartRuleRulesScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\CartRule\Model\ORM\CartRuleRuleQuery::create()
                        ->filterByPrimaryKeys($this->cartRuleRulesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->cartRuleRulesScheduledForDeletion = null;
                }
            }

                if ($this->collCartRuleRules !== null) {
            foreach ($this->collCartRuleRules as $referrerFK) {
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

        $this->modifiedColumns[RuleTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . RuleTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(RuleTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(RuleTableMap::COL_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'NAME';
        }
        if ($this->isColumnModified(RuleTableMap::COL_TABLE_REFERER)) {
            $modifiedColumns[':p' . $index++]  = 'TABLE_REFERER';
        }
        if ($this->isColumnModified(RuleTableMap::COL_PRIMARY_KEY_REFERER)) {
            $modifiedColumns[':p' . $index++]  = 'PRIMARY_KEY_REFERER';
        }
        if ($this->isColumnModified(RuleTableMap::COL_COLUMN_REFERER)) {
            $modifiedColumns[':p' . $index++]  = 'COLUMN_REFERER';
        }
        if ($this->isColumnModified(RuleTableMap::COL_RULE_TYPE_ID)) {
            $modifiedColumns[':p' . $index++]  = 'RULE_TYPE_ID';
        }
        if ($this->isColumnModified(RuleTableMap::COL_FIELD)) {
            $modifiedColumns[':p' . $index++]  = 'FIELD';
        }

        $sql = sprintf(
            'INSERT INTO rule (%s) VALUES (%s)',
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
                    case 'TABLE_REFERER':                        
                        $stmt->bindValue($identifier, $this->table_referer, PDO::PARAM_STR);
                        break;
                    case 'PRIMARY_KEY_REFERER':                        
                        $stmt->bindValue($identifier, $this->primary_key_referer, PDO::PARAM_STR);
                        break;
                    case 'COLUMN_REFERER':                        
                        $stmt->bindValue($identifier, $this->column_referer, PDO::PARAM_STR);
                        break;
                    case 'RULE_TYPE_ID':                        
                        $stmt->bindValue($identifier, $this->rule_type_id, PDO::PARAM_INT);
                        break;
                    case 'FIELD':                        
                        $stmt->bindValue($identifier, $this->field, PDO::PARAM_STR);
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
        $pos = RuleTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getTableReferer();
                break;
            case 3:
                return $this->getPrimaryKeyReferer();
                break;
            case 4:
                return $this->getColumnReferer();
                break;
            case 5:
                return $this->getRuleTypeId();
                break;
            case 6:
                return $this->getField();
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
        if (isset($alreadyDumpedObjects['Rule'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Rule'][$this->getPrimaryKey()] = true;
        $keys = RuleTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getName(),
            $keys[2] => $this->getTableReferer(),
            $keys[3] => $this->getPrimaryKeyReferer(),
            $keys[4] => $this->getColumnReferer(),
            $keys[5] => $this->getRuleTypeId(),
            $keys[6] => $this->getField(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }
        
        if ($includeForeignObjects) {
            if (null !== $this->collCartRuleRules) {
                $result['CartRuleRules'] = $this->collCartRuleRules->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = RuleTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

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
                $this->setTableReferer($value);
                break;
            case 3:
                $this->setPrimaryKeyReferer($value);
                break;
            case 4:
                $this->setColumnReferer($value);
                break;
            case 5:
                $this->setRuleTypeId($value);
                break;
            case 6:
                $this->setField($value);
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
        $keys = RuleTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setName($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setTableReferer($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setPrimaryKeyReferer($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setColumnReferer($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setRuleTypeId($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setField($arr[$keys[6]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(RuleTableMap::DATABASE_NAME);

        if ($this->isColumnModified(RuleTableMap::COL_ID)) $criteria->add(RuleTableMap::COL_ID, $this->id);
        if ($this->isColumnModified(RuleTableMap::COL_NAME)) $criteria->add(RuleTableMap::COL_NAME, $this->name);
        if ($this->isColumnModified(RuleTableMap::COL_TABLE_REFERER)) $criteria->add(RuleTableMap::COL_TABLE_REFERER, $this->table_referer);
        if ($this->isColumnModified(RuleTableMap::COL_PRIMARY_KEY_REFERER)) $criteria->add(RuleTableMap::COL_PRIMARY_KEY_REFERER, $this->primary_key_referer);
        if ($this->isColumnModified(RuleTableMap::COL_COLUMN_REFERER)) $criteria->add(RuleTableMap::COL_COLUMN_REFERER, $this->column_referer);
        if ($this->isColumnModified(RuleTableMap::COL_RULE_TYPE_ID)) $criteria->add(RuleTableMap::COL_RULE_TYPE_ID, $this->rule_type_id);
        if ($this->isColumnModified(RuleTableMap::COL_FIELD)) $criteria->add(RuleTableMap::COL_FIELD, $this->field);

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
        $criteria = new Criteria(RuleTableMap::DATABASE_NAME);
        $criteria->add(RuleTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \Gekosale\Plugin\CartRule\Model\ORM\Rule (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setName($this->getName());
        $copyObj->setTableReferer($this->getTableReferer());
        $copyObj->setPrimaryKeyReferer($this->getPrimaryKeyReferer());
        $copyObj->setColumnReferer($this->getColumnReferer());
        $copyObj->setRuleTypeId($this->getRuleTypeId());
        $copyObj->setField($this->getField());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getCartRuleRules() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCartRuleRule($relObj->copy($deepCopy));
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
     * @return                 \Gekosale\Plugin\CartRule\Model\ORM\Rule Clone of current object.
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
        if ('CartRuleRule' == $relationName) {
            return $this->initCartRuleRules();
        }
    }

    /**
     * Clears out the collCartRuleRules collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCartRuleRules()
     */
    public function clearCartRuleRules()
    {
        $this->collCartRuleRules = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCartRuleRules collection loaded partially.
     */
    public function resetPartialCartRuleRules($v = true)
    {
        $this->collCartRuleRulesPartial = $v;
    }

    /**
     * Initializes the collCartRuleRules collection.
     *
     * By default this just sets the collCartRuleRules collection to an empty array (like clearcollCartRuleRules());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCartRuleRules($overrideExisting = true)
    {
        if (null !== $this->collCartRuleRules && !$overrideExisting) {
            return;
        }
        $this->collCartRuleRules = new ObjectCollection();
        $this->collCartRuleRules->setModel('\Gekosale\Plugin\CartRule\Model\ORM\CartRuleRule');
    }

    /**
     * Gets an array of ChildCartRuleRule objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildRule is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildCartRuleRule[] List of ChildCartRuleRule objects
     * @throws PropelException
     */
    public function getCartRuleRules($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCartRuleRulesPartial && !$this->isNew();
        if (null === $this->collCartRuleRules || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCartRuleRules) {
                // return empty collection
                $this->initCartRuleRules();
            } else {
                $collCartRuleRules = ChildCartRuleRuleQuery::create(null, $criteria)
                    ->filterByRule($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCartRuleRulesPartial && count($collCartRuleRules)) {
                        $this->initCartRuleRules(false);

                        foreach ($collCartRuleRules as $obj) {
                            if (false == $this->collCartRuleRules->contains($obj)) {
                                $this->collCartRuleRules->append($obj);
                            }
                        }

                        $this->collCartRuleRulesPartial = true;
                    }

                    reset($collCartRuleRules);

                    return $collCartRuleRules;
                }

                if ($partial && $this->collCartRuleRules) {
                    foreach ($this->collCartRuleRules as $obj) {
                        if ($obj->isNew()) {
                            $collCartRuleRules[] = $obj;
                        }
                    }
                }

                $this->collCartRuleRules = $collCartRuleRules;
                $this->collCartRuleRulesPartial = false;
            }
        }

        return $this->collCartRuleRules;
    }

    /**
     * Sets a collection of CartRuleRule objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $cartRuleRules A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildRule The current object (for fluent API support)
     */
    public function setCartRuleRules(Collection $cartRuleRules, ConnectionInterface $con = null)
    {
        $cartRuleRulesToDelete = $this->getCartRuleRules(new Criteria(), $con)->diff($cartRuleRules);

        
        $this->cartRuleRulesScheduledForDeletion = $cartRuleRulesToDelete;

        foreach ($cartRuleRulesToDelete as $cartRuleRuleRemoved) {
            $cartRuleRuleRemoved->setRule(null);
        }

        $this->collCartRuleRules = null;
        foreach ($cartRuleRules as $cartRuleRule) {
            $this->addCartRuleRule($cartRuleRule);
        }

        $this->collCartRuleRules = $cartRuleRules;
        $this->collCartRuleRulesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CartRuleRule objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related CartRuleRule objects.
     * @throws PropelException
     */
    public function countCartRuleRules(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCartRuleRulesPartial && !$this->isNew();
        if (null === $this->collCartRuleRules || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCartRuleRules) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCartRuleRules());
            }

            $query = ChildCartRuleRuleQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByRule($this)
                ->count($con);
        }

        return count($this->collCartRuleRules);
    }

    /**
     * Method called to associate a ChildCartRuleRule object to this object
     * through the ChildCartRuleRule foreign key attribute.
     *
     * @param    ChildCartRuleRule $l ChildCartRuleRule
     * @return   \Gekosale\Plugin\CartRule\Model\ORM\Rule The current object (for fluent API support)
     */
    public function addCartRuleRule(ChildCartRuleRule $l)
    {
        if ($this->collCartRuleRules === null) {
            $this->initCartRuleRules();
            $this->collCartRuleRulesPartial = true;
        }

        if (!in_array($l, $this->collCartRuleRules->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCartRuleRule($l);
        }

        return $this;
    }

    /**
     * @param CartRuleRule $cartRuleRule The cartRuleRule object to add.
     */
    protected function doAddCartRuleRule($cartRuleRule)
    {
        $this->collCartRuleRules[]= $cartRuleRule;
        $cartRuleRule->setRule($this);
    }

    /**
     * @param  CartRuleRule $cartRuleRule The cartRuleRule object to remove.
     * @return ChildRule The current object (for fluent API support)
     */
    public function removeCartRuleRule($cartRuleRule)
    {
        if ($this->getCartRuleRules()->contains($cartRuleRule)) {
            $this->collCartRuleRules->remove($this->collCartRuleRules->search($cartRuleRule));
            if (null === $this->cartRuleRulesScheduledForDeletion) {
                $this->cartRuleRulesScheduledForDeletion = clone $this->collCartRuleRules;
                $this->cartRuleRulesScheduledForDeletion->clear();
            }
            $this->cartRuleRulesScheduledForDeletion[]= clone $cartRuleRule;
            $cartRuleRule->setRule(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Rule is new, it will return
     * an empty collection; or if this Rule has previously
     * been saved, it will retrieve related CartRuleRules from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Rule.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCartRuleRule[] List of ChildCartRuleRule objects
     */
    public function getCartRuleRulesJoinCartRule($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCartRuleRuleQuery::create(null, $criteria);
        $query->joinWith('CartRule', $joinBehavior);

        return $this->getCartRuleRules($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->name = null;
        $this->table_referer = null;
        $this->primary_key_referer = null;
        $this->column_referer = null;
        $this->rule_type_id = null;
        $this->field = null;
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
            if ($this->collCartRuleRules) {
                foreach ($this->collCartRuleRules as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collCartRuleRules = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(RuleTableMap::DEFAULT_STRING_FORMAT);
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
