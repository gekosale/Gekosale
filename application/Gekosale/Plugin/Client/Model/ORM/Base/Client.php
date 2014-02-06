<?php

namespace Gekosale\Plugin\Client\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Client\Model\ORM\Client as ChildClient;
use Gekosale\Plugin\Client\Model\ORM\ClientAddress as ChildClientAddress;
use Gekosale\Plugin\Client\Model\ORM\ClientAddressQuery as ChildClientAddressQuery;
use Gekosale\Plugin\Client\Model\ORM\ClientData as ChildClientData;
use Gekosale\Plugin\Client\Model\ORM\ClientDataQuery as ChildClientDataQuery;
use Gekosale\Plugin\Client\Model\ORM\ClientQuery as ChildClientQuery;
use Gekosale\Plugin\Client\Model\ORM\Map\ClientTableMap;
use Gekosale\Plugin\MissingCart\Model\ORM\MissingCart as ChildMissingCart;
use Gekosale\Plugin\MissingCart\Model\ORM\MissingCartQuery;
use Gekosale\Plugin\MissingCart\Model\ORM\Base\MissingCart;
use Gekosale\Plugin\Shop\Model\ORM\Shop as ChildShop;
use Gekosale\Plugin\Shop\Model\ORM\ShopQuery;
use Gekosale\Plugin\Wishlist\Model\ORM\Wishlist as ChildWishlist;
use Gekosale\Plugin\Wishlist\Model\ORM\WishlistQuery;
use Gekosale\Plugin\Wishlist\Model\ORM\Base\Wishlist;
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

abstract class Client implements ActiveRecordInterface 
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Gekosale\\Plugin\\Client\\Model\\ORM\\Map\\ClientTableMap';


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
     * The value for the disabled field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $disabled;

    /**
     * The value for the shop_id field.
     * @var        int
     */
    protected $shop_id;

    /**
     * The value for the active_link field.
     * @var        string
     */
    protected $active_link;

    /**
     * The value for the client_type field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $client_type;

    /**
     * The value for the auto_assign field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $auto_assign;

    /**
     * @var        Shop
     */
    protected $aShop;

    /**
     * @var        ObjectCollection|ChildClientAddress[] Collection to store aggregation of ChildClientAddress objects.
     */
    protected $collClientAddresses;
    protected $collClientAddressesPartial;

    /**
     * @var        ObjectCollection|ChildClientData[] Collection to store aggregation of ChildClientData objects.
     */
    protected $collClientDatas;
    protected $collClientDatasPartial;

    /**
     * @var        ObjectCollection|ChildMissingCart[] Collection to store aggregation of ChildMissingCart objects.
     */
    protected $collMissingCarts;
    protected $collMissingCartsPartial;

    /**
     * @var        ObjectCollection|ChildWishlist[] Collection to store aggregation of ChildWishlist objects.
     */
    protected $collWishlists;
    protected $collWishlistsPartial;

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
    protected $clientAddressesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $clientDatasScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $missingCartsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $wishlistsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->disabled = 1;
        $this->client_type = 1;
        $this->auto_assign = 1;
    }

    /**
     * Initializes internal state of Gekosale\Plugin\Client\Model\ORM\Base\Client object.
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
     * Compares this with another <code>Client</code> instance.  If
     * <code>obj</code> is an instance of <code>Client</code>, delegates to
     * <code>equals(Client)</code>.  Otherwise, returns <code>false</code>.
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
     * @return Client The current object, for fluid interface
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
     * @return Client The current object, for fluid interface
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
     * Get the [disabled] column value.
     * 
     * @return   int
     */
    public function getIsDisabled()
    {

        return $this->disabled;
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
     * Get the [active_link] column value.
     * 
     * @return   string
     */
    public function getActiveLink()
    {

        return $this->active_link;
    }

    /**
     * Get the [client_type] column value.
     * 
     * @return   int
     */
    public function getClientType()
    {

        return $this->client_type;
    }

    /**
     * Get the [auto_assign] column value.
     * 
     * @return   int
     */
    public function getAutoAssign()
    {

        return $this->auto_assign;
    }

    /**
     * Set the value of [id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Client\Model\ORM\Client The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[ClientTableMap::COL_ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [login] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Client\Model\ORM\Client The current object (for fluent API support)
     */
    public function setLogin($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->login !== $v) {
            $this->login = $v;
            $this->modifiedColumns[ClientTableMap::COL_LOGIN] = true;
        }


        return $this;
    } // setLogin()

    /**
     * Set the value of [password] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Client\Model\ORM\Client The current object (for fluent API support)
     */
    public function setPassword($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->password !== $v) {
            $this->password = $v;
            $this->modifiedColumns[ClientTableMap::COL_PASSWORD] = true;
        }


        return $this;
    } // setPassword()

    /**
     * Set the value of [disabled] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Client\Model\ORM\Client The current object (for fluent API support)
     */
    public function setIsDisabled($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->disabled !== $v) {
            $this->disabled = $v;
            $this->modifiedColumns[ClientTableMap::COL_DISABLED] = true;
        }


        return $this;
    } // setIsDisabled()

    /**
     * Set the value of [shop_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Client\Model\ORM\Client The current object (for fluent API support)
     */
    public function setShopId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->shop_id !== $v) {
            $this->shop_id = $v;
            $this->modifiedColumns[ClientTableMap::COL_SHOP_ID] = true;
        }

        if ($this->aShop !== null && $this->aShop->getId() !== $v) {
            $this->aShop = null;
        }


        return $this;
    } // setShopId()

    /**
     * Set the value of [active_link] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Client\Model\ORM\Client The current object (for fluent API support)
     */
    public function setActiveLink($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->active_link !== $v) {
            $this->active_link = $v;
            $this->modifiedColumns[ClientTableMap::COL_ACTIVE_LINK] = true;
        }


        return $this;
    } // setActiveLink()

    /**
     * Set the value of [client_type] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Client\Model\ORM\Client The current object (for fluent API support)
     */
    public function setClientType($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->client_type !== $v) {
            $this->client_type = $v;
            $this->modifiedColumns[ClientTableMap::COL_CLIENT_TYPE] = true;
        }


        return $this;
    } // setClientType()

    /**
     * Set the value of [auto_assign] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Client\Model\ORM\Client The current object (for fluent API support)
     */
    public function setAutoAssign($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->auto_assign !== $v) {
            $this->auto_assign = $v;
            $this->modifiedColumns[ClientTableMap::COL_AUTO_ASSIGN] = true;
        }


        return $this;
    } // setAutoAssign()

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
            if ($this->disabled !== 1) {
                return false;
            }

            if ($this->client_type !== 1) {
                return false;
            }

            if ($this->auto_assign !== 1) {
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


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : ClientTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : ClientTableMap::translateFieldName('Login', TableMap::TYPE_PHPNAME, $indexType)];
            $this->login = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : ClientTableMap::translateFieldName('Password', TableMap::TYPE_PHPNAME, $indexType)];
            $this->password = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : ClientTableMap::translateFieldName('IsDisabled', TableMap::TYPE_PHPNAME, $indexType)];
            $this->disabled = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : ClientTableMap::translateFieldName('ShopId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->shop_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : ClientTableMap::translateFieldName('ActiveLink', TableMap::TYPE_PHPNAME, $indexType)];
            $this->active_link = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : ClientTableMap::translateFieldName('ClientType', TableMap::TYPE_PHPNAME, $indexType)];
            $this->client_type = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : ClientTableMap::translateFieldName('AutoAssign', TableMap::TYPE_PHPNAME, $indexType)];
            $this->auto_assign = (null !== $col) ? (int) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 8; // 8 = ClientTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Gekosale\Plugin\Client\Model\ORM\Client object", 0, $e);
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
        if ($this->aShop !== null && $this->shop_id !== $this->aShop->getId()) {
            $this->aShop = null;
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
            $con = Propel::getServiceContainer()->getReadConnection(ClientTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildClientQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aShop = null;
            $this->collClientAddresses = null;

            $this->collClientDatas = null;

            $this->collMissingCarts = null;

            $this->collWishlists = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Client::setDeleted()
     * @see Client::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(ClientTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildClientQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(ClientTableMap::DATABASE_NAME);
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
                ClientTableMap::addInstanceToPool($this);
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

            if ($this->aShop !== null) {
                if ($this->aShop->isModified() || $this->aShop->isNew()) {
                    $affectedRows += $this->aShop->save($con);
                }
                $this->setShop($this->aShop);
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

            if ($this->clientAddressesScheduledForDeletion !== null) {
                if (!$this->clientAddressesScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Client\Model\ORM\ClientAddressQuery::create()
                        ->filterByPrimaryKeys($this->clientAddressesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->clientAddressesScheduledForDeletion = null;
                }
            }

                if ($this->collClientAddresses !== null) {
            foreach ($this->collClientAddresses as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->clientDatasScheduledForDeletion !== null) {
                if (!$this->clientDatasScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Client\Model\ORM\ClientDataQuery::create()
                        ->filterByPrimaryKeys($this->clientDatasScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
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

            if ($this->missingCartsScheduledForDeletion !== null) {
                if (!$this->missingCartsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartQuery::create()
                        ->filterByPrimaryKeys($this->missingCartsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->missingCartsScheduledForDeletion = null;
                }
            }

                if ($this->collMissingCarts !== null) {
            foreach ($this->collMissingCarts as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->wishlistsScheduledForDeletion !== null) {
                if (!$this->wishlistsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Wishlist\Model\ORM\WishlistQuery::create()
                        ->filterByPrimaryKeys($this->wishlistsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->wishlistsScheduledForDeletion = null;
                }
            }

                if ($this->collWishlists !== null) {
            foreach ($this->collWishlists as $referrerFK) {
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

        $this->modifiedColumns[ClientTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . ClientTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ClientTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(ClientTableMap::COL_LOGIN)) {
            $modifiedColumns[':p' . $index++]  = 'LOGIN';
        }
        if ($this->isColumnModified(ClientTableMap::COL_PASSWORD)) {
            $modifiedColumns[':p' . $index++]  = 'PASSWORD';
        }
        if ($this->isColumnModified(ClientTableMap::COL_DISABLED)) {
            $modifiedColumns[':p' . $index++]  = 'DISABLED';
        }
        if ($this->isColumnModified(ClientTableMap::COL_SHOP_ID)) {
            $modifiedColumns[':p' . $index++]  = 'SHOP_ID';
        }
        if ($this->isColumnModified(ClientTableMap::COL_ACTIVE_LINK)) {
            $modifiedColumns[':p' . $index++]  = 'ACTIVE_LINK';
        }
        if ($this->isColumnModified(ClientTableMap::COL_CLIENT_TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'CLIENT_TYPE';
        }
        if ($this->isColumnModified(ClientTableMap::COL_AUTO_ASSIGN)) {
            $modifiedColumns[':p' . $index++]  = 'AUTO_ASSIGN';
        }

        $sql = sprintf(
            'INSERT INTO client (%s) VALUES (%s)',
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
                    case 'DISABLED':                        
                        $stmt->bindValue($identifier, $this->disabled, PDO::PARAM_INT);
                        break;
                    case 'SHOP_ID':                        
                        $stmt->bindValue($identifier, $this->shop_id, PDO::PARAM_INT);
                        break;
                    case 'ACTIVE_LINK':                        
                        $stmt->bindValue($identifier, $this->active_link, PDO::PARAM_STR);
                        break;
                    case 'CLIENT_TYPE':                        
                        $stmt->bindValue($identifier, $this->client_type, PDO::PARAM_INT);
                        break;
                    case 'AUTO_ASSIGN':                        
                        $stmt->bindValue($identifier, $this->auto_assign, PDO::PARAM_INT);
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
        $pos = ClientTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getIsDisabled();
                break;
            case 4:
                return $this->getShopId();
                break;
            case 5:
                return $this->getActiveLink();
                break;
            case 6:
                return $this->getClientType();
                break;
            case 7:
                return $this->getAutoAssign();
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
        if (isset($alreadyDumpedObjects['Client'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Client'][$this->getPrimaryKey()] = true;
        $keys = ClientTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getLogin(),
            $keys[2] => $this->getPassword(),
            $keys[3] => $this->getIsDisabled(),
            $keys[4] => $this->getShopId(),
            $keys[5] => $this->getActiveLink(),
            $keys[6] => $this->getClientType(),
            $keys[7] => $this->getAutoAssign(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }
        
        if ($includeForeignObjects) {
            if (null !== $this->aShop) {
                $result['Shop'] = $this->aShop->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collClientAddresses) {
                $result['ClientAddresses'] = $this->collClientAddresses->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collClientDatas) {
                $result['ClientDatas'] = $this->collClientDatas->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collMissingCarts) {
                $result['MissingCarts'] = $this->collMissingCarts->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collWishlists) {
                $result['Wishlists'] = $this->collWishlists->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = ClientTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

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
                $this->setIsDisabled($value);
                break;
            case 4:
                $this->setShopId($value);
                break;
            case 5:
                $this->setActiveLink($value);
                break;
            case 6:
                $this->setClientType($value);
                break;
            case 7:
                $this->setAutoAssign($value);
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
        $keys = ClientTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setLogin($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setPassword($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setIsDisabled($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setShopId($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setActiveLink($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setClientType($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setAutoAssign($arr[$keys[7]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ClientTableMap::DATABASE_NAME);

        if ($this->isColumnModified(ClientTableMap::COL_ID)) $criteria->add(ClientTableMap::COL_ID, $this->id);
        if ($this->isColumnModified(ClientTableMap::COL_LOGIN)) $criteria->add(ClientTableMap::COL_LOGIN, $this->login);
        if ($this->isColumnModified(ClientTableMap::COL_PASSWORD)) $criteria->add(ClientTableMap::COL_PASSWORD, $this->password);
        if ($this->isColumnModified(ClientTableMap::COL_DISABLED)) $criteria->add(ClientTableMap::COL_DISABLED, $this->disabled);
        if ($this->isColumnModified(ClientTableMap::COL_SHOP_ID)) $criteria->add(ClientTableMap::COL_SHOP_ID, $this->shop_id);
        if ($this->isColumnModified(ClientTableMap::COL_ACTIVE_LINK)) $criteria->add(ClientTableMap::COL_ACTIVE_LINK, $this->active_link);
        if ($this->isColumnModified(ClientTableMap::COL_CLIENT_TYPE)) $criteria->add(ClientTableMap::COL_CLIENT_TYPE, $this->client_type);
        if ($this->isColumnModified(ClientTableMap::COL_AUTO_ASSIGN)) $criteria->add(ClientTableMap::COL_AUTO_ASSIGN, $this->auto_assign);

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
        $criteria = new Criteria(ClientTableMap::DATABASE_NAME);
        $criteria->add(ClientTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \Gekosale\Plugin\Client\Model\ORM\Client (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setLogin($this->getLogin());
        $copyObj->setPassword($this->getPassword());
        $copyObj->setIsDisabled($this->getIsDisabled());
        $copyObj->setShopId($this->getShopId());
        $copyObj->setActiveLink($this->getActiveLink());
        $copyObj->setClientType($this->getClientType());
        $copyObj->setAutoAssign($this->getAutoAssign());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getClientAddresses() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addClientAddress($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getClientDatas() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addClientData($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getMissingCarts() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addMissingCart($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getWishlists() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addWishlist($relObj->copy($deepCopy));
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
     * @return                 \Gekosale\Plugin\Client\Model\ORM\Client Clone of current object.
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
     * Declares an association between this object and a ChildShop object.
     *
     * @param                  ChildShop $v
     * @return                 \Gekosale\Plugin\Client\Model\ORM\Client The current object (for fluent API support)
     * @throws PropelException
     */
    public function setShop(ChildShop $v = null)
    {
        if ($v === null) {
            $this->setShopId(NULL);
        } else {
            $this->setShopId($v->getId());
        }

        $this->aShop = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildShop object, it will not be re-added.
        if ($v !== null) {
            $v->addClient($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildShop object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildShop The associated ChildShop object.
     * @throws PropelException
     */
    public function getShop(ConnectionInterface $con = null)
    {
        if ($this->aShop === null && ($this->shop_id !== null)) {
            $this->aShop = ShopQuery::create()->findPk($this->shop_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aShop->addClients($this);
             */
        }

        return $this->aShop;
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
        if ('ClientAddress' == $relationName) {
            return $this->initClientAddresses();
        }
        if ('ClientData' == $relationName) {
            return $this->initClientDatas();
        }
        if ('MissingCart' == $relationName) {
            return $this->initMissingCarts();
        }
        if ('Wishlist' == $relationName) {
            return $this->initWishlists();
        }
    }

    /**
     * Clears out the collClientAddresses collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addClientAddresses()
     */
    public function clearClientAddresses()
    {
        $this->collClientAddresses = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collClientAddresses collection loaded partially.
     */
    public function resetPartialClientAddresses($v = true)
    {
        $this->collClientAddressesPartial = $v;
    }

    /**
     * Initializes the collClientAddresses collection.
     *
     * By default this just sets the collClientAddresses collection to an empty array (like clearcollClientAddresses());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initClientAddresses($overrideExisting = true)
    {
        if (null !== $this->collClientAddresses && !$overrideExisting) {
            return;
        }
        $this->collClientAddresses = new ObjectCollection();
        $this->collClientAddresses->setModel('\Gekosale\Plugin\Client\Model\ORM\ClientAddress');
    }

    /**
     * Gets an array of ChildClientAddress objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildClient is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildClientAddress[] List of ChildClientAddress objects
     * @throws PropelException
     */
    public function getClientAddresses($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collClientAddressesPartial && !$this->isNew();
        if (null === $this->collClientAddresses || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collClientAddresses) {
                // return empty collection
                $this->initClientAddresses();
            } else {
                $collClientAddresses = ChildClientAddressQuery::create(null, $criteria)
                    ->filterByClient($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collClientAddressesPartial && count($collClientAddresses)) {
                        $this->initClientAddresses(false);

                        foreach ($collClientAddresses as $obj) {
                            if (false == $this->collClientAddresses->contains($obj)) {
                                $this->collClientAddresses->append($obj);
                            }
                        }

                        $this->collClientAddressesPartial = true;
                    }

                    reset($collClientAddresses);

                    return $collClientAddresses;
                }

                if ($partial && $this->collClientAddresses) {
                    foreach ($this->collClientAddresses as $obj) {
                        if ($obj->isNew()) {
                            $collClientAddresses[] = $obj;
                        }
                    }
                }

                $this->collClientAddresses = $collClientAddresses;
                $this->collClientAddressesPartial = false;
            }
        }

        return $this->collClientAddresses;
    }

    /**
     * Sets a collection of ClientAddress objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $clientAddresses A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildClient The current object (for fluent API support)
     */
    public function setClientAddresses(Collection $clientAddresses, ConnectionInterface $con = null)
    {
        $clientAddressesToDelete = $this->getClientAddresses(new Criteria(), $con)->diff($clientAddresses);

        
        $this->clientAddressesScheduledForDeletion = $clientAddressesToDelete;

        foreach ($clientAddressesToDelete as $clientAddressRemoved) {
            $clientAddressRemoved->setClient(null);
        }

        $this->collClientAddresses = null;
        foreach ($clientAddresses as $clientAddress) {
            $this->addClientAddress($clientAddress);
        }

        $this->collClientAddresses = $clientAddresses;
        $this->collClientAddressesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ClientAddress objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ClientAddress objects.
     * @throws PropelException
     */
    public function countClientAddresses(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collClientAddressesPartial && !$this->isNew();
        if (null === $this->collClientAddresses || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collClientAddresses) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getClientAddresses());
            }

            $query = ChildClientAddressQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByClient($this)
                ->count($con);
        }

        return count($this->collClientAddresses);
    }

    /**
     * Method called to associate a ChildClientAddress object to this object
     * through the ChildClientAddress foreign key attribute.
     *
     * @param    ChildClientAddress $l ChildClientAddress
     * @return   \Gekosale\Plugin\Client\Model\ORM\Client The current object (for fluent API support)
     */
    public function addClientAddress(ChildClientAddress $l)
    {
        if ($this->collClientAddresses === null) {
            $this->initClientAddresses();
            $this->collClientAddressesPartial = true;
        }

        if (!in_array($l, $this->collClientAddresses->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddClientAddress($l);
        }

        return $this;
    }

    /**
     * @param ClientAddress $clientAddress The clientAddress object to add.
     */
    protected function doAddClientAddress($clientAddress)
    {
        $this->collClientAddresses[]= $clientAddress;
        $clientAddress->setClient($this);
    }

    /**
     * @param  ClientAddress $clientAddress The clientAddress object to remove.
     * @return ChildClient The current object (for fluent API support)
     */
    public function removeClientAddress($clientAddress)
    {
        if ($this->getClientAddresses()->contains($clientAddress)) {
            $this->collClientAddresses->remove($this->collClientAddresses->search($clientAddress));
            if (null === $this->clientAddressesScheduledForDeletion) {
                $this->clientAddressesScheduledForDeletion = clone $this->collClientAddresses;
                $this->clientAddressesScheduledForDeletion->clear();
            }
            $this->clientAddressesScheduledForDeletion[]= clone $clientAddress;
            $clientAddress->setClient(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Client is new, it will return
     * an empty collection; or if this Client has previously
     * been saved, it will retrieve related ClientAddresses from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Client.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildClientAddress[] List of ChildClientAddress objects
     */
    public function getClientAddressesJoinCountry($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildClientAddressQuery::create(null, $criteria);
        $query->joinWith('Country', $joinBehavior);

        return $this->getClientAddresses($query, $con);
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
     * If this ChildClient is new, it will return
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
                $collClientDatas = ChildClientDataQuery::create(null, $criteria)
                    ->filterByClient($this)
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
     * @return   ChildClient The current object (for fluent API support)
     */
    public function setClientDatas(Collection $clientDatas, ConnectionInterface $con = null)
    {
        $clientDatasToDelete = $this->getClientDatas(new Criteria(), $con)->diff($clientDatas);

        
        $this->clientDatasScheduledForDeletion = $clientDatasToDelete;

        foreach ($clientDatasToDelete as $clientDataRemoved) {
            $clientDataRemoved->setClient(null);
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

            $query = ChildClientDataQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByClient($this)
                ->count($con);
        }

        return count($this->collClientDatas);
    }

    /**
     * Method called to associate a ChildClientData object to this object
     * through the ChildClientData foreign key attribute.
     *
     * @param    ChildClientData $l ChildClientData
     * @return   \Gekosale\Plugin\Client\Model\ORM\Client The current object (for fluent API support)
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
        $clientData->setClient($this);
    }

    /**
     * @param  ClientData $clientData The clientData object to remove.
     * @return ChildClient The current object (for fluent API support)
     */
    public function removeClientData($clientData)
    {
        if ($this->getClientDatas()->contains($clientData)) {
            $this->collClientDatas->remove($this->collClientDatas->search($clientData));
            if (null === $this->clientDatasScheduledForDeletion) {
                $this->clientDatasScheduledForDeletion = clone $this->collClientDatas;
                $this->clientDatasScheduledForDeletion->clear();
            }
            $this->clientDatasScheduledForDeletion[]= clone $clientData;
            $clientData->setClient(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Client is new, it will return
     * an empty collection; or if this Client has previously
     * been saved, it will retrieve related ClientDatas from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Client.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildClientData[] List of ChildClientData objects
     */
    public function getClientDatasJoinClientGroup($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildClientDataQuery::create(null, $criteria);
        $query->joinWith('ClientGroup', $joinBehavior);

        return $this->getClientDatas($query, $con);
    }

    /**
     * Clears out the collMissingCarts collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addMissingCarts()
     */
    public function clearMissingCarts()
    {
        $this->collMissingCarts = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collMissingCarts collection loaded partially.
     */
    public function resetPartialMissingCarts($v = true)
    {
        $this->collMissingCartsPartial = $v;
    }

    /**
     * Initializes the collMissingCarts collection.
     *
     * By default this just sets the collMissingCarts collection to an empty array (like clearcollMissingCarts());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initMissingCarts($overrideExisting = true)
    {
        if (null !== $this->collMissingCarts && !$overrideExisting) {
            return;
        }
        $this->collMissingCarts = new ObjectCollection();
        $this->collMissingCarts->setModel('\Gekosale\Plugin\MissingCart\Model\ORM\MissingCart');
    }

    /**
     * Gets an array of ChildMissingCart objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildClient is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildMissingCart[] List of ChildMissingCart objects
     * @throws PropelException
     */
    public function getMissingCarts($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collMissingCartsPartial && !$this->isNew();
        if (null === $this->collMissingCarts || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collMissingCarts) {
                // return empty collection
                $this->initMissingCarts();
            } else {
                $collMissingCarts = MissingCartQuery::create(null, $criteria)
                    ->filterByClient($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collMissingCartsPartial && count($collMissingCarts)) {
                        $this->initMissingCarts(false);

                        foreach ($collMissingCarts as $obj) {
                            if (false == $this->collMissingCarts->contains($obj)) {
                                $this->collMissingCarts->append($obj);
                            }
                        }

                        $this->collMissingCartsPartial = true;
                    }

                    reset($collMissingCarts);

                    return $collMissingCarts;
                }

                if ($partial && $this->collMissingCarts) {
                    foreach ($this->collMissingCarts as $obj) {
                        if ($obj->isNew()) {
                            $collMissingCarts[] = $obj;
                        }
                    }
                }

                $this->collMissingCarts = $collMissingCarts;
                $this->collMissingCartsPartial = false;
            }
        }

        return $this->collMissingCarts;
    }

    /**
     * Sets a collection of MissingCart objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $missingCarts A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildClient The current object (for fluent API support)
     */
    public function setMissingCarts(Collection $missingCarts, ConnectionInterface $con = null)
    {
        $missingCartsToDelete = $this->getMissingCarts(new Criteria(), $con)->diff($missingCarts);

        
        $this->missingCartsScheduledForDeletion = $missingCartsToDelete;

        foreach ($missingCartsToDelete as $missingCartRemoved) {
            $missingCartRemoved->setClient(null);
        }

        $this->collMissingCarts = null;
        foreach ($missingCarts as $missingCart) {
            $this->addMissingCart($missingCart);
        }

        $this->collMissingCarts = $missingCarts;
        $this->collMissingCartsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related MissingCart objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related MissingCart objects.
     * @throws PropelException
     */
    public function countMissingCarts(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collMissingCartsPartial && !$this->isNew();
        if (null === $this->collMissingCarts || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collMissingCarts) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getMissingCarts());
            }

            $query = MissingCartQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByClient($this)
                ->count($con);
        }

        return count($this->collMissingCarts);
    }

    /**
     * Method called to associate a ChildMissingCart object to this object
     * through the ChildMissingCart foreign key attribute.
     *
     * @param    ChildMissingCart $l ChildMissingCart
     * @return   \Gekosale\Plugin\Client\Model\ORM\Client The current object (for fluent API support)
     */
    public function addMissingCart(ChildMissingCart $l)
    {
        if ($this->collMissingCarts === null) {
            $this->initMissingCarts();
            $this->collMissingCartsPartial = true;
        }

        if (!in_array($l, $this->collMissingCarts->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddMissingCart($l);
        }

        return $this;
    }

    /**
     * @param MissingCart $missingCart The missingCart object to add.
     */
    protected function doAddMissingCart($missingCart)
    {
        $this->collMissingCarts[]= $missingCart;
        $missingCart->setClient($this);
    }

    /**
     * @param  MissingCart $missingCart The missingCart object to remove.
     * @return ChildClient The current object (for fluent API support)
     */
    public function removeMissingCart($missingCart)
    {
        if ($this->getMissingCarts()->contains($missingCart)) {
            $this->collMissingCarts->remove($this->collMissingCarts->search($missingCart));
            if (null === $this->missingCartsScheduledForDeletion) {
                $this->missingCartsScheduledForDeletion = clone $this->collMissingCarts;
                $this->missingCartsScheduledForDeletion->clear();
            }
            $this->missingCartsScheduledForDeletion[]= clone $missingCart;
            $missingCart->setClient(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Client is new, it will return
     * an empty collection; or if this Client has previously
     * been saved, it will retrieve related MissingCarts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Client.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildMissingCart[] List of ChildMissingCart objects
     */
    public function getMissingCartsJoinShop($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = MissingCartQuery::create(null, $criteria);
        $query->joinWith('Shop', $joinBehavior);

        return $this->getMissingCarts($query, $con);
    }

    /**
     * Clears out the collWishlists collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addWishlists()
     */
    public function clearWishlists()
    {
        $this->collWishlists = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collWishlists collection loaded partially.
     */
    public function resetPartialWishlists($v = true)
    {
        $this->collWishlistsPartial = $v;
    }

    /**
     * Initializes the collWishlists collection.
     *
     * By default this just sets the collWishlists collection to an empty array (like clearcollWishlists());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initWishlists($overrideExisting = true)
    {
        if (null !== $this->collWishlists && !$overrideExisting) {
            return;
        }
        $this->collWishlists = new ObjectCollection();
        $this->collWishlists->setModel('\Gekosale\Plugin\Wishlist\Model\ORM\Wishlist');
    }

    /**
     * Gets an array of ChildWishlist objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildClient is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildWishlist[] List of ChildWishlist objects
     * @throws PropelException
     */
    public function getWishlists($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collWishlistsPartial && !$this->isNew();
        if (null === $this->collWishlists || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collWishlists) {
                // return empty collection
                $this->initWishlists();
            } else {
                $collWishlists = WishlistQuery::create(null, $criteria)
                    ->filterByClient($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collWishlistsPartial && count($collWishlists)) {
                        $this->initWishlists(false);

                        foreach ($collWishlists as $obj) {
                            if (false == $this->collWishlists->contains($obj)) {
                                $this->collWishlists->append($obj);
                            }
                        }

                        $this->collWishlistsPartial = true;
                    }

                    reset($collWishlists);

                    return $collWishlists;
                }

                if ($partial && $this->collWishlists) {
                    foreach ($this->collWishlists as $obj) {
                        if ($obj->isNew()) {
                            $collWishlists[] = $obj;
                        }
                    }
                }

                $this->collWishlists = $collWishlists;
                $this->collWishlistsPartial = false;
            }
        }

        return $this->collWishlists;
    }

    /**
     * Sets a collection of Wishlist objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $wishlists A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildClient The current object (for fluent API support)
     */
    public function setWishlists(Collection $wishlists, ConnectionInterface $con = null)
    {
        $wishlistsToDelete = $this->getWishlists(new Criteria(), $con)->diff($wishlists);

        
        $this->wishlistsScheduledForDeletion = $wishlistsToDelete;

        foreach ($wishlistsToDelete as $wishlistRemoved) {
            $wishlistRemoved->setClient(null);
        }

        $this->collWishlists = null;
        foreach ($wishlists as $wishlist) {
            $this->addWishlist($wishlist);
        }

        $this->collWishlists = $wishlists;
        $this->collWishlistsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Wishlist objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Wishlist objects.
     * @throws PropelException
     */
    public function countWishlists(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collWishlistsPartial && !$this->isNew();
        if (null === $this->collWishlists || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collWishlists) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getWishlists());
            }

            $query = WishlistQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByClient($this)
                ->count($con);
        }

        return count($this->collWishlists);
    }

    /**
     * Method called to associate a ChildWishlist object to this object
     * through the ChildWishlist foreign key attribute.
     *
     * @param    ChildWishlist $l ChildWishlist
     * @return   \Gekosale\Plugin\Client\Model\ORM\Client The current object (for fluent API support)
     */
    public function addWishlist(ChildWishlist $l)
    {
        if ($this->collWishlists === null) {
            $this->initWishlists();
            $this->collWishlistsPartial = true;
        }

        if (!in_array($l, $this->collWishlists->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddWishlist($l);
        }

        return $this;
    }

    /**
     * @param Wishlist $wishlist The wishlist object to add.
     */
    protected function doAddWishlist($wishlist)
    {
        $this->collWishlists[]= $wishlist;
        $wishlist->setClient($this);
    }

    /**
     * @param  Wishlist $wishlist The wishlist object to remove.
     * @return ChildClient The current object (for fluent API support)
     */
    public function removeWishlist($wishlist)
    {
        if ($this->getWishlists()->contains($wishlist)) {
            $this->collWishlists->remove($this->collWishlists->search($wishlist));
            if (null === $this->wishlistsScheduledForDeletion) {
                $this->wishlistsScheduledForDeletion = clone $this->collWishlists;
                $this->wishlistsScheduledForDeletion->clear();
            }
            $this->wishlistsScheduledForDeletion[]= clone $wishlist;
            $wishlist->setClient(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Client is new, it will return
     * an empty collection; or if this Client has previously
     * been saved, it will retrieve related Wishlists from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Client.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildWishlist[] List of ChildWishlist objects
     */
    public function getWishlistsJoinProduct($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = WishlistQuery::create(null, $criteria);
        $query->joinWith('Product', $joinBehavior);

        return $this->getWishlists($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Client is new, it will return
     * an empty collection; or if this Client has previously
     * been saved, it will retrieve related Wishlists from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Client.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildWishlist[] List of ChildWishlist objects
     */
    public function getWishlistsJoinShop($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = WishlistQuery::create(null, $criteria);
        $query->joinWith('Shop', $joinBehavior);

        return $this->getWishlists($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->login = null;
        $this->password = null;
        $this->disabled = null;
        $this->shop_id = null;
        $this->active_link = null;
        $this->client_type = null;
        $this->auto_assign = null;
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
            if ($this->collClientAddresses) {
                foreach ($this->collClientAddresses as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collClientDatas) {
                foreach ($this->collClientDatas as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collMissingCarts) {
                foreach ($this->collMissingCarts as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collWishlists) {
                foreach ($this->collWishlists as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collClientAddresses = null;
        $this->collClientDatas = null;
        $this->collMissingCarts = null;
        $this->collWishlists = null;
        $this->aShop = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ClientTableMap::DEFAULT_STRING_FORMAT);
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
