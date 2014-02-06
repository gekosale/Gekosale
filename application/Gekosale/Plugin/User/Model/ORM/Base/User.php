<?php

namespace Gekosale\Plugin\User\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\User\Model\ORM\User as ChildUser;
use Gekosale\Plugin\User\Model\ORM\UserData as ChildUserData;
use Gekosale\Plugin\User\Model\ORM\UserDataQuery as ChildUserDataQuery;
use Gekosale\Plugin\User\Model\ORM\UserGroupShop as ChildUserGroupShop;
use Gekosale\Plugin\User\Model\ORM\UserGroupShopQuery as ChildUserGroupShopQuery;
use Gekosale\Plugin\User\Model\ORM\UserGroupUser as ChildUserGroupUser;
use Gekosale\Plugin\User\Model\ORM\UserGroupUserQuery as ChildUserGroupUserQuery;
use Gekosale\Plugin\User\Model\ORM\UserQuery as ChildUserQuery;
use Gekosale\Plugin\User\Model\ORM\Map\UserTableMap;
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

abstract class User implements ActiveRecordInterface 
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Gekosale\\Plugin\\User\\Model\\ORM\\Map\\UserTableMap';


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
     * The value for the login field.
     * @var        string
     */
    protected $login;

    /**
     * The value for the password field.
     * @var        string
     */
    protected $password;

    /**
     * The value for the active field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $active;

    /**
     * The value for the global field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $global;

    /**
     * @var        ObjectCollection|ChildUserGroupShop[] Collection to store aggregation of ChildUserGroupShop objects.
     */
    protected $collUserGroupShops;
    protected $collUserGroupShopsPartial;

    /**
     * @var        ObjectCollection|ChildUserData[] Collection to store aggregation of ChildUserData objects.
     */
    protected $collUserDatas;
    protected $collUserDatasPartial;

    /**
     * @var        ObjectCollection|ChildUserGroupUser[] Collection to store aggregation of ChildUserGroupUser objects.
     */
    protected $collUserGroupUsers;
    protected $collUserGroupUsersPartial;

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
    protected $userGroupShopsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $userDatasScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $userGroupUsersScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->active = 0;
        $this->global = 1;
    }

    /**
     * Initializes internal state of Gekosale\Plugin\User\Model\ORM\Base\User object.
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
     * Compares this with another <code>User</code> instance.  If
     * <code>obj</code> is an instance of <code>User</code>, delegates to
     * <code>equals(User)</code>.  Otherwise, returns <code>false</code>.
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
     * @return User The current object, for fluid interface
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
     * @return User The current object, for fluid interface
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
     * Get the [login] column value.
     * 
     * @return   string
     */
    public function getLogin()
    {

        return $this->login;
    }

    /**
     * Get the [password] column value.
     * 
     * @return   string
     */
    public function getPassword()
    {

        return $this->password;
    }

    /**
     * Get the [active] column value.
     * 
     * @return   int
     */
    public function getIsActive()
    {

        return $this->active;
    }

    /**
     * Get the [global] column value.
     * 
     * @return   int
     */
    public function getIsGlobal()
    {

        return $this->global;
    }

    /**
     * Set the value of [id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\User\Model\ORM\User The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[UserTableMap::COL_ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [login] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\User\Model\ORM\User The current object (for fluent API support)
     */
    public function setLogin($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->login !== $v) {
            $this->login = $v;
            $this->modifiedColumns[UserTableMap::COL_LOGIN] = true;
        }


        return $this;
    } // setLogin()

    /**
     * Set the value of [password] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\User\Model\ORM\User The current object (for fluent API support)
     */
    public function setPassword($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->password !== $v) {
            $this->password = $v;
            $this->modifiedColumns[UserTableMap::COL_PASSWORD] = true;
        }


        return $this;
    } // setPassword()

    /**
     * Set the value of [active] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\User\Model\ORM\User The current object (for fluent API support)
     */
    public function setIsActive($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->active !== $v) {
            $this->active = $v;
            $this->modifiedColumns[UserTableMap::COL_ACTIVE] = true;
        }


        return $this;
    } // setIsActive()

    /**
     * Set the value of [global] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\User\Model\ORM\User The current object (for fluent API support)
     */
    public function setIsGlobal($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->global !== $v) {
            $this->global = $v;
            $this->modifiedColumns[UserTableMap::COL_GLOBAL] = true;
        }


        return $this;
    } // setIsGlobal()

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
            if ($this->active !== 0) {
                return false;
            }

            if ($this->global !== 1) {
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


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : UserTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : UserTableMap::translateFieldName('Login', TableMap::TYPE_PHPNAME, $indexType)];
            $this->login = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : UserTableMap::translateFieldName('Password', TableMap::TYPE_PHPNAME, $indexType)];
            $this->password = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : UserTableMap::translateFieldName('IsActive', TableMap::TYPE_PHPNAME, $indexType)];
            $this->active = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : UserTableMap::translateFieldName('IsGlobal', TableMap::TYPE_PHPNAME, $indexType)];
            $this->global = (null !== $col) ? (int) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 5; // 5 = UserTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Gekosale\Plugin\User\Model\ORM\User object", 0, $e);
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
            $con = Propel::getServiceContainer()->getReadConnection(UserTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildUserQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collUserGroupShops = null;

            $this->collUserDatas = null;

            $this->collUserGroupUsers = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see User::setDeleted()
     * @see User::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(UserTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildUserQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(UserTableMap::DATABASE_NAME);
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
                UserTableMap::addInstanceToPool($this);
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

            if ($this->userGroupShopsScheduledForDeletion !== null) {
                if (!$this->userGroupShopsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\User\Model\ORM\UserGroupShopQuery::create()
                        ->filterByPrimaryKeys($this->userGroupShopsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->userGroupShopsScheduledForDeletion = null;
                }
            }

                if ($this->collUserGroupShops !== null) {
            foreach ($this->collUserGroupShops as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->userDatasScheduledForDeletion !== null) {
                if (!$this->userDatasScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\User\Model\ORM\UserDataQuery::create()
                        ->filterByPrimaryKeys($this->userDatasScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->userDatasScheduledForDeletion = null;
                }
            }

                if ($this->collUserDatas !== null) {
            foreach ($this->collUserDatas as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->userGroupUsersScheduledForDeletion !== null) {
                if (!$this->userGroupUsersScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\User\Model\ORM\UserGroupUserQuery::create()
                        ->filterByPrimaryKeys($this->userGroupUsersScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->userGroupUsersScheduledForDeletion = null;
                }
            }

                if ($this->collUserGroupUsers !== null) {
            foreach ($this->collUserGroupUsers as $referrerFK) {
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

        $this->modifiedColumns[UserTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . UserTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(UserTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(UserTableMap::COL_LOGIN)) {
            $modifiedColumns[':p' . $index++]  = 'LOGIN';
        }
        if ($this->isColumnModified(UserTableMap::COL_PASSWORD)) {
            $modifiedColumns[':p' . $index++]  = 'PASSWORD';
        }
        if ($this->isColumnModified(UserTableMap::COL_ACTIVE)) {
            $modifiedColumns[':p' . $index++]  = 'ACTIVE';
        }
        if ($this->isColumnModified(UserTableMap::COL_GLOBAL)) {
            $modifiedColumns[':p' . $index++]  = 'GLOBAL';
        }

        $sql = sprintf(
            'INSERT INTO user (%s) VALUES (%s)',
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
                    case 'LOGIN':                        
                        $stmt->bindValue($identifier, $this->login, PDO::PARAM_STR);
                        break;
                    case 'PASSWORD':                        
                        $stmt->bindValue($identifier, $this->password, PDO::PARAM_STR);
                        break;
                    case 'ACTIVE':                        
                        $stmt->bindValue($identifier, $this->active, PDO::PARAM_INT);
                        break;
                    case 'GLOBAL':                        
                        $stmt->bindValue($identifier, $this->global, PDO::PARAM_INT);
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
        $pos = UserTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getLogin();
                break;
            case 2:
                return $this->getPassword();
                break;
            case 3:
                return $this->getIsActive();
                break;
            case 4:
                return $this->getIsGlobal();
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
        if (isset($alreadyDumpedObjects['User'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['User'][$this->getPrimaryKey()] = true;
        $keys = UserTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getLogin(),
            $keys[2] => $this->getPassword(),
            $keys[3] => $this->getIsActive(),
            $keys[4] => $this->getIsGlobal(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }
        
        if ($includeForeignObjects) {
            if (null !== $this->collUserGroupShops) {
                $result['UserGroupShops'] = $this->collUserGroupShops->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collUserDatas) {
                $result['UserDatas'] = $this->collUserDatas->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collUserGroupUsers) {
                $result['UserGroupUsers'] = $this->collUserGroupUsers->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = UserTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

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
                $this->setLogin($value);
                break;
            case 2:
                $this->setPassword($value);
                break;
            case 3:
                $this->setIsActive($value);
                break;
            case 4:
                $this->setIsGlobal($value);
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
        $keys = UserTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setLogin($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setPassword($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setIsActive($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setIsGlobal($arr[$keys[4]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(UserTableMap::DATABASE_NAME);

        if ($this->isColumnModified(UserTableMap::COL_ID)) $criteria->add(UserTableMap::COL_ID, $this->id);
        if ($this->isColumnModified(UserTableMap::COL_LOGIN)) $criteria->add(UserTableMap::COL_LOGIN, $this->login);
        if ($this->isColumnModified(UserTableMap::COL_PASSWORD)) $criteria->add(UserTableMap::COL_PASSWORD, $this->password);
        if ($this->isColumnModified(UserTableMap::COL_ACTIVE)) $criteria->add(UserTableMap::COL_ACTIVE, $this->active);
        if ($this->isColumnModified(UserTableMap::COL_GLOBAL)) $criteria->add(UserTableMap::COL_GLOBAL, $this->global);

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
        $criteria = new Criteria(UserTableMap::DATABASE_NAME);
        $criteria->add(UserTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \Gekosale\Plugin\User\Model\ORM\User (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setLogin($this->getLogin());
        $copyObj->setPassword($this->getPassword());
        $copyObj->setIsActive($this->getIsActive());
        $copyObj->setIsGlobal($this->getIsGlobal());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getUserGroupShops() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addUserGroupShop($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getUserDatas() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addUserData($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getUserGroupUsers() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addUserGroupUser($relObj->copy($deepCopy));
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
     * @return                 \Gekosale\Plugin\User\Model\ORM\User Clone of current object.
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
        if ('UserGroupShop' == $relationName) {
            return $this->initUserGroupShops();
        }
        if ('UserData' == $relationName) {
            return $this->initUserDatas();
        }
        if ('UserGroupUser' == $relationName) {
            return $this->initUserGroupUsers();
        }
    }

    /**
     * Clears out the collUserGroupShops collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addUserGroupShops()
     */
    public function clearUserGroupShops()
    {
        $this->collUserGroupShops = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collUserGroupShops collection loaded partially.
     */
    public function resetPartialUserGroupShops($v = true)
    {
        $this->collUserGroupShopsPartial = $v;
    }

    /**
     * Initializes the collUserGroupShops collection.
     *
     * By default this just sets the collUserGroupShops collection to an empty array (like clearcollUserGroupShops());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initUserGroupShops($overrideExisting = true)
    {
        if (null !== $this->collUserGroupShops && !$overrideExisting) {
            return;
        }
        $this->collUserGroupShops = new ObjectCollection();
        $this->collUserGroupShops->setModel('\Gekosale\Plugin\User\Model\ORM\UserGroupShop');
    }

    /**
     * Gets an array of ChildUserGroupShop objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildUser is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildUserGroupShop[] List of ChildUserGroupShop objects
     * @throws PropelException
     */
    public function getUserGroupShops($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collUserGroupShopsPartial && !$this->isNew();
        if (null === $this->collUserGroupShops || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collUserGroupShops) {
                // return empty collection
                $this->initUserGroupShops();
            } else {
                $collUserGroupShops = ChildUserGroupShopQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collUserGroupShopsPartial && count($collUserGroupShops)) {
                        $this->initUserGroupShops(false);

                        foreach ($collUserGroupShops as $obj) {
                            if (false == $this->collUserGroupShops->contains($obj)) {
                                $this->collUserGroupShops->append($obj);
                            }
                        }

                        $this->collUserGroupShopsPartial = true;
                    }

                    reset($collUserGroupShops);

                    return $collUserGroupShops;
                }

                if ($partial && $this->collUserGroupShops) {
                    foreach ($this->collUserGroupShops as $obj) {
                        if ($obj->isNew()) {
                            $collUserGroupShops[] = $obj;
                        }
                    }
                }

                $this->collUserGroupShops = $collUserGroupShops;
                $this->collUserGroupShopsPartial = false;
            }
        }

        return $this->collUserGroupShops;
    }

    /**
     * Sets a collection of UserGroupShop objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $userGroupShops A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildUser The current object (for fluent API support)
     */
    public function setUserGroupShops(Collection $userGroupShops, ConnectionInterface $con = null)
    {
        $userGroupShopsToDelete = $this->getUserGroupShops(new Criteria(), $con)->diff($userGroupShops);

        
        $this->userGroupShopsScheduledForDeletion = $userGroupShopsToDelete;

        foreach ($userGroupShopsToDelete as $userGroupShopRemoved) {
            $userGroupShopRemoved->setUser(null);
        }

        $this->collUserGroupShops = null;
        foreach ($userGroupShops as $userGroupShop) {
            $this->addUserGroupShop($userGroupShop);
        }

        $this->collUserGroupShops = $userGroupShops;
        $this->collUserGroupShopsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related UserGroupShop objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related UserGroupShop objects.
     * @throws PropelException
     */
    public function countUserGroupShops(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collUserGroupShopsPartial && !$this->isNew();
        if (null === $this->collUserGroupShops || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collUserGroupShops) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getUserGroupShops());
            }

            $query = ChildUserGroupShopQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collUserGroupShops);
    }

    /**
     * Method called to associate a ChildUserGroupShop object to this object
     * through the ChildUserGroupShop foreign key attribute.
     *
     * @param    ChildUserGroupShop $l ChildUserGroupShop
     * @return   \Gekosale\Plugin\User\Model\ORM\User The current object (for fluent API support)
     */
    public function addUserGroupShop(ChildUserGroupShop $l)
    {
        if ($this->collUserGroupShops === null) {
            $this->initUserGroupShops();
            $this->collUserGroupShopsPartial = true;
        }

        if (!in_array($l, $this->collUserGroupShops->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddUserGroupShop($l);
        }

        return $this;
    }

    /**
     * @param UserGroupShop $userGroupShop The userGroupShop object to add.
     */
    protected function doAddUserGroupShop($userGroupShop)
    {
        $this->collUserGroupShops[]= $userGroupShop;
        $userGroupShop->setUser($this);
    }

    /**
     * @param  UserGroupShop $userGroupShop The userGroupShop object to remove.
     * @return ChildUser The current object (for fluent API support)
     */
    public function removeUserGroupShop($userGroupShop)
    {
        if ($this->getUserGroupShops()->contains($userGroupShop)) {
            $this->collUserGroupShops->remove($this->collUserGroupShops->search($userGroupShop));
            if (null === $this->userGroupShopsScheduledForDeletion) {
                $this->userGroupShopsScheduledForDeletion = clone $this->collUserGroupShops;
                $this->userGroupShopsScheduledForDeletion->clear();
            }
            $this->userGroupShopsScheduledForDeletion[]= clone $userGroupShop;
            $userGroupShop->setUser(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related UserGroupShops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildUserGroupShop[] List of ChildUserGroupShop objects
     */
    public function getUserGroupShopsJoinUserGroup($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildUserGroupShopQuery::create(null, $criteria);
        $query->joinWith('UserGroup', $joinBehavior);

        return $this->getUserGroupShops($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related UserGroupShops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildUserGroupShop[] List of ChildUserGroupShop objects
     */
    public function getUserGroupShopsJoinShop($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildUserGroupShopQuery::create(null, $criteria);
        $query->joinWith('Shop', $joinBehavior);

        return $this->getUserGroupShops($query, $con);
    }

    /**
     * Clears out the collUserDatas collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addUserDatas()
     */
    public function clearUserDatas()
    {
        $this->collUserDatas = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collUserDatas collection loaded partially.
     */
    public function resetPartialUserDatas($v = true)
    {
        $this->collUserDatasPartial = $v;
    }

    /**
     * Initializes the collUserDatas collection.
     *
     * By default this just sets the collUserDatas collection to an empty array (like clearcollUserDatas());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initUserDatas($overrideExisting = true)
    {
        if (null !== $this->collUserDatas && !$overrideExisting) {
            return;
        }
        $this->collUserDatas = new ObjectCollection();
        $this->collUserDatas->setModel('\Gekosale\Plugin\User\Model\ORM\UserData');
    }

    /**
     * Gets an array of ChildUserData objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildUser is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildUserData[] List of ChildUserData objects
     * @throws PropelException
     */
    public function getUserDatas($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collUserDatasPartial && !$this->isNew();
        if (null === $this->collUserDatas || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collUserDatas) {
                // return empty collection
                $this->initUserDatas();
            } else {
                $collUserDatas = ChildUserDataQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collUserDatasPartial && count($collUserDatas)) {
                        $this->initUserDatas(false);

                        foreach ($collUserDatas as $obj) {
                            if (false == $this->collUserDatas->contains($obj)) {
                                $this->collUserDatas->append($obj);
                            }
                        }

                        $this->collUserDatasPartial = true;
                    }

                    reset($collUserDatas);

                    return $collUserDatas;
                }

                if ($partial && $this->collUserDatas) {
                    foreach ($this->collUserDatas as $obj) {
                        if ($obj->isNew()) {
                            $collUserDatas[] = $obj;
                        }
                    }
                }

                $this->collUserDatas = $collUserDatas;
                $this->collUserDatasPartial = false;
            }
        }

        return $this->collUserDatas;
    }

    /**
     * Sets a collection of UserData objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $userDatas A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildUser The current object (for fluent API support)
     */
    public function setUserDatas(Collection $userDatas, ConnectionInterface $con = null)
    {
        $userDatasToDelete = $this->getUserDatas(new Criteria(), $con)->diff($userDatas);

        
        $this->userDatasScheduledForDeletion = $userDatasToDelete;

        foreach ($userDatasToDelete as $userDataRemoved) {
            $userDataRemoved->setUser(null);
        }

        $this->collUserDatas = null;
        foreach ($userDatas as $userData) {
            $this->addUserData($userData);
        }

        $this->collUserDatas = $userDatas;
        $this->collUserDatasPartial = false;

        return $this;
    }

    /**
     * Returns the number of related UserData objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related UserData objects.
     * @throws PropelException
     */
    public function countUserDatas(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collUserDatasPartial && !$this->isNew();
        if (null === $this->collUserDatas || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collUserDatas) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getUserDatas());
            }

            $query = ChildUserDataQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collUserDatas);
    }

    /**
     * Method called to associate a ChildUserData object to this object
     * through the ChildUserData foreign key attribute.
     *
     * @param    ChildUserData $l ChildUserData
     * @return   \Gekosale\Plugin\User\Model\ORM\User The current object (for fluent API support)
     */
    public function addUserData(ChildUserData $l)
    {
        if ($this->collUserDatas === null) {
            $this->initUserDatas();
            $this->collUserDatasPartial = true;
        }

        if (!in_array($l, $this->collUserDatas->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddUserData($l);
        }

        return $this;
    }

    /**
     * @param UserData $userData The userData object to add.
     */
    protected function doAddUserData($userData)
    {
        $this->collUserDatas[]= $userData;
        $userData->setUser($this);
    }

    /**
     * @param  UserData $userData The userData object to remove.
     * @return ChildUser The current object (for fluent API support)
     */
    public function removeUserData($userData)
    {
        if ($this->getUserDatas()->contains($userData)) {
            $this->collUserDatas->remove($this->collUserDatas->search($userData));
            if (null === $this->userDatasScheduledForDeletion) {
                $this->userDatasScheduledForDeletion = clone $this->collUserDatas;
                $this->userDatasScheduledForDeletion->clear();
            }
            $this->userDatasScheduledForDeletion[]= clone $userData;
            $userData->setUser(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related UserDatas from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildUserData[] List of ChildUserData objects
     */
    public function getUserDatasJoinFile($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildUserDataQuery::create(null, $criteria);
        $query->joinWith('File', $joinBehavior);

        return $this->getUserDatas($query, $con);
    }

    /**
     * Clears out the collUserGroupUsers collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addUserGroupUsers()
     */
    public function clearUserGroupUsers()
    {
        $this->collUserGroupUsers = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collUserGroupUsers collection loaded partially.
     */
    public function resetPartialUserGroupUsers($v = true)
    {
        $this->collUserGroupUsersPartial = $v;
    }

    /**
     * Initializes the collUserGroupUsers collection.
     *
     * By default this just sets the collUserGroupUsers collection to an empty array (like clearcollUserGroupUsers());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initUserGroupUsers($overrideExisting = true)
    {
        if (null !== $this->collUserGroupUsers && !$overrideExisting) {
            return;
        }
        $this->collUserGroupUsers = new ObjectCollection();
        $this->collUserGroupUsers->setModel('\Gekosale\Plugin\User\Model\ORM\UserGroupUser');
    }

    /**
     * Gets an array of ChildUserGroupUser objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildUser is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildUserGroupUser[] List of ChildUserGroupUser objects
     * @throws PropelException
     */
    public function getUserGroupUsers($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collUserGroupUsersPartial && !$this->isNew();
        if (null === $this->collUserGroupUsers || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collUserGroupUsers) {
                // return empty collection
                $this->initUserGroupUsers();
            } else {
                $collUserGroupUsers = ChildUserGroupUserQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collUserGroupUsersPartial && count($collUserGroupUsers)) {
                        $this->initUserGroupUsers(false);

                        foreach ($collUserGroupUsers as $obj) {
                            if (false == $this->collUserGroupUsers->contains($obj)) {
                                $this->collUserGroupUsers->append($obj);
                            }
                        }

                        $this->collUserGroupUsersPartial = true;
                    }

                    reset($collUserGroupUsers);

                    return $collUserGroupUsers;
                }

                if ($partial && $this->collUserGroupUsers) {
                    foreach ($this->collUserGroupUsers as $obj) {
                        if ($obj->isNew()) {
                            $collUserGroupUsers[] = $obj;
                        }
                    }
                }

                $this->collUserGroupUsers = $collUserGroupUsers;
                $this->collUserGroupUsersPartial = false;
            }
        }

        return $this->collUserGroupUsers;
    }

    /**
     * Sets a collection of UserGroupUser objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $userGroupUsers A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildUser The current object (for fluent API support)
     */
    public function setUserGroupUsers(Collection $userGroupUsers, ConnectionInterface $con = null)
    {
        $userGroupUsersToDelete = $this->getUserGroupUsers(new Criteria(), $con)->diff($userGroupUsers);

        
        $this->userGroupUsersScheduledForDeletion = $userGroupUsersToDelete;

        foreach ($userGroupUsersToDelete as $userGroupUserRemoved) {
            $userGroupUserRemoved->setUser(null);
        }

        $this->collUserGroupUsers = null;
        foreach ($userGroupUsers as $userGroupUser) {
            $this->addUserGroupUser($userGroupUser);
        }

        $this->collUserGroupUsers = $userGroupUsers;
        $this->collUserGroupUsersPartial = false;

        return $this;
    }

    /**
     * Returns the number of related UserGroupUser objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related UserGroupUser objects.
     * @throws PropelException
     */
    public function countUserGroupUsers(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collUserGroupUsersPartial && !$this->isNew();
        if (null === $this->collUserGroupUsers || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collUserGroupUsers) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getUserGroupUsers());
            }

            $query = ChildUserGroupUserQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByUser($this)
                ->count($con);
        }

        return count($this->collUserGroupUsers);
    }

    /**
     * Method called to associate a ChildUserGroupUser object to this object
     * through the ChildUserGroupUser foreign key attribute.
     *
     * @param    ChildUserGroupUser $l ChildUserGroupUser
     * @return   \Gekosale\Plugin\User\Model\ORM\User The current object (for fluent API support)
     */
    public function addUserGroupUser(ChildUserGroupUser $l)
    {
        if ($this->collUserGroupUsers === null) {
            $this->initUserGroupUsers();
            $this->collUserGroupUsersPartial = true;
        }

        if (!in_array($l, $this->collUserGroupUsers->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddUserGroupUser($l);
        }

        return $this;
    }

    /**
     * @param UserGroupUser $userGroupUser The userGroupUser object to add.
     */
    protected function doAddUserGroupUser($userGroupUser)
    {
        $this->collUserGroupUsers[]= $userGroupUser;
        $userGroupUser->setUser($this);
    }

    /**
     * @param  UserGroupUser $userGroupUser The userGroupUser object to remove.
     * @return ChildUser The current object (for fluent API support)
     */
    public function removeUserGroupUser($userGroupUser)
    {
        if ($this->getUserGroupUsers()->contains($userGroupUser)) {
            $this->collUserGroupUsers->remove($this->collUserGroupUsers->search($userGroupUser));
            if (null === $this->userGroupUsersScheduledForDeletion) {
                $this->userGroupUsersScheduledForDeletion = clone $this->collUserGroupUsers;
                $this->userGroupUsersScheduledForDeletion->clear();
            }
            $this->userGroupUsersScheduledForDeletion[]= clone $userGroupUser;
            $userGroupUser->setUser(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related UserGroupUsers from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildUserGroupUser[] List of ChildUserGroupUser objects
     */
    public function getUserGroupUsersJoinUserGroup($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildUserGroupUserQuery::create(null, $criteria);
        $query->joinWith('UserGroup', $joinBehavior);

        return $this->getUserGroupUsers($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->login = null;
        $this->password = null;
        $this->active = null;
        $this->global = null;
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
            if ($this->collUserGroupShops) {
                foreach ($this->collUserGroupShops as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collUserDatas) {
                foreach ($this->collUserDatas as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collUserGroupUsers) {
                foreach ($this->collUserGroupUsers as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collUserGroupShops = null;
        $this->collUserDatas = null;
        $this->collUserGroupUsers = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(UserTableMap::DEFAULT_STRING_FORMAT);
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
