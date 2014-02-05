<?php

namespace Gekosale\Plugin\Page\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Page\Model\ORM\Page as ChildPage;
use Gekosale\Plugin\Page\Model\ORM\PageQuery as ChildPageQuery;
use Gekosale\Plugin\Page\Model\ORM\PageShop as ChildPageShop;
use Gekosale\Plugin\Page\Model\ORM\PageShopQuery as ChildPageShopQuery;
use Gekosale\Plugin\Page\Model\ORM\Map\PageTableMap;
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

abstract class Page implements ActiveRecordInterface 
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Gekosale\\Plugin\\Page\\Model\\ORM\\Map\\PageTableMap';


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
     * The value for the page_id field.
     * @var        int
     */
    protected $page_id;

    /**
     * The value for the hierarchy field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $hierarchy;

    /**
     * The value for the footer field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $footer;

    /**
     * The value for the header field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $header;

    /**
     * The value for the alias field.
     * @var        string
     */
    protected $alias;

    /**
     * The value for the redirect field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $redirect;

    /**
     * The value for the redirect_route field.
     * @var        string
     */
    protected $redirect_route;

    /**
     * The value for the redirect_url field.
     * @var        string
     */
    protected $redirect_url;

    /**
     * @var        Page
     */
    protected $aPageRelatedByPageId;

    /**
     * @var        ObjectCollection|ChildPage[] Collection to store aggregation of ChildPage objects.
     */
    protected $collPagesRelatedById;
    protected $collPagesRelatedByIdPartial;

    /**
     * @var        ObjectCollection|ChildPageShop[] Collection to store aggregation of ChildPageShop objects.
     */
    protected $collPageShops;
    protected $collPageShopsPartial;

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
    protected $pagesRelatedByIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $pageShopsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->hierarchy = 0;
        $this->footer = 1;
        $this->header = 1;
        $this->redirect = 0;
    }

    /**
     * Initializes internal state of Gekosale\Plugin\Page\Model\ORM\Base\Page object.
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
     * Compares this with another <code>Page</code> instance.  If
     * <code>obj</code> is an instance of <code>Page</code>, delegates to
     * <code>equals(Page)</code>.  Otherwise, returns <code>false</code>.
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
     * @return Page The current object, for fluid interface
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
     * @return Page The current object, for fluid interface
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
     * Get the [page_id] column value.
     * 
     * @return   int
     */
    public function getPageId()
    {

        return $this->page_id;
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
     * Get the [footer] column value.
     * 
     * @return   int
     */
    public function getFooter()
    {

        return $this->footer;
    }

    /**
     * Get the [header] column value.
     * 
     * @return   int
     */
    public function getHeader()
    {

        return $this->header;
    }

    /**
     * Get the [alias] column value.
     * 
     * @return   string
     */
    public function getAlias()
    {

        return $this->alias;
    }

    /**
     * Get the [redirect] column value.
     * 
     * @return   int
     */
    public function getRedirect()
    {

        return $this->redirect;
    }

    /**
     * Get the [redirect_route] column value.
     * 
     * @return   string
     */
    public function getRedirectRoute()
    {

        return $this->redirect_route;
    }

    /**
     * Get the [redirect_url] column value.
     * 
     * @return   string
     */
    public function getRedirectUrl()
    {

        return $this->redirect_url;
    }

    /**
     * Set the value of [id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Page\Model\ORM\Page The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[PageTableMap::COL_ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [page_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Page\Model\ORM\Page The current object (for fluent API support)
     */
    public function setPageId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->page_id !== $v) {
            $this->page_id = $v;
            $this->modifiedColumns[PageTableMap::COL_PAGE_ID] = true;
        }

        if ($this->aPageRelatedByPageId !== null && $this->aPageRelatedByPageId->getId() !== $v) {
            $this->aPageRelatedByPageId = null;
        }


        return $this;
    } // setPageId()

    /**
     * Set the value of [hierarchy] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Page\Model\ORM\Page The current object (for fluent API support)
     */
    public function setHierarchy($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->hierarchy !== $v) {
            $this->hierarchy = $v;
            $this->modifiedColumns[PageTableMap::COL_HIERARCHY] = true;
        }


        return $this;
    } // setHierarchy()

    /**
     * Set the value of [footer] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Page\Model\ORM\Page The current object (for fluent API support)
     */
    public function setFooter($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->footer !== $v) {
            $this->footer = $v;
            $this->modifiedColumns[PageTableMap::COL_FOOTER] = true;
        }


        return $this;
    } // setFooter()

    /**
     * Set the value of [header] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Page\Model\ORM\Page The current object (for fluent API support)
     */
    public function setHeader($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->header !== $v) {
            $this->header = $v;
            $this->modifiedColumns[PageTableMap::COL_HEADER] = true;
        }


        return $this;
    } // setHeader()

    /**
     * Set the value of [alias] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Page\Model\ORM\Page The current object (for fluent API support)
     */
    public function setAlias($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->alias !== $v) {
            $this->alias = $v;
            $this->modifiedColumns[PageTableMap::COL_ALIAS] = true;
        }


        return $this;
    } // setAlias()

    /**
     * Set the value of [redirect] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Page\Model\ORM\Page The current object (for fluent API support)
     */
    public function setRedirect($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->redirect !== $v) {
            $this->redirect = $v;
            $this->modifiedColumns[PageTableMap::COL_REDIRECT] = true;
        }


        return $this;
    } // setRedirect()

    /**
     * Set the value of [redirect_route] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Page\Model\ORM\Page The current object (for fluent API support)
     */
    public function setRedirectRoute($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->redirect_route !== $v) {
            $this->redirect_route = $v;
            $this->modifiedColumns[PageTableMap::COL_REDIRECT_ROUTE] = true;
        }


        return $this;
    } // setRedirectRoute()

    /**
     * Set the value of [redirect_url] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\Page\Model\ORM\Page The current object (for fluent API support)
     */
    public function setRedirectUrl($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->redirect_url !== $v) {
            $this->redirect_url = $v;
            $this->modifiedColumns[PageTableMap::COL_REDIRECT_URL] = true;
        }


        return $this;
    } // setRedirectUrl()

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
            if ($this->hierarchy !== 0) {
                return false;
            }

            if ($this->footer !== 1) {
                return false;
            }

            if ($this->header !== 1) {
                return false;
            }

            if ($this->redirect !== 0) {
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


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : PageTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : PageTableMap::translateFieldName('PageId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->page_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : PageTableMap::translateFieldName('Hierarchy', TableMap::TYPE_PHPNAME, $indexType)];
            $this->hierarchy = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : PageTableMap::translateFieldName('Footer', TableMap::TYPE_PHPNAME, $indexType)];
            $this->footer = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : PageTableMap::translateFieldName('Header', TableMap::TYPE_PHPNAME, $indexType)];
            $this->header = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : PageTableMap::translateFieldName('Alias', TableMap::TYPE_PHPNAME, $indexType)];
            $this->alias = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : PageTableMap::translateFieldName('Redirect', TableMap::TYPE_PHPNAME, $indexType)];
            $this->redirect = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : PageTableMap::translateFieldName('RedirectRoute', TableMap::TYPE_PHPNAME, $indexType)];
            $this->redirect_route = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : PageTableMap::translateFieldName('RedirectUrl', TableMap::TYPE_PHPNAME, $indexType)];
            $this->redirect_url = (null !== $col) ? (string) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 9; // 9 = PageTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Gekosale\Plugin\Page\Model\ORM\Page object", 0, $e);
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
        if ($this->aPageRelatedByPageId !== null && $this->page_id !== $this->aPageRelatedByPageId->getId()) {
            $this->aPageRelatedByPageId = null;
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
            $con = Propel::getServiceContainer()->getReadConnection(PageTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildPageQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aPageRelatedByPageId = null;
            $this->collPagesRelatedById = null;

            $this->collPageShops = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Page::setDeleted()
     * @see Page::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(PageTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildPageQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(PageTableMap::DATABASE_NAME);
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
                PageTableMap::addInstanceToPool($this);
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

            if ($this->aPageRelatedByPageId !== null) {
                if ($this->aPageRelatedByPageId->isModified() || $this->aPageRelatedByPageId->isNew()) {
                    $affectedRows += $this->aPageRelatedByPageId->save($con);
                }
                $this->setPageRelatedByPageId($this->aPageRelatedByPageId);
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

            if ($this->pagesRelatedByIdScheduledForDeletion !== null) {
                if (!$this->pagesRelatedByIdScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Page\Model\ORM\PageQuery::create()
                        ->filterByPrimaryKeys($this->pagesRelatedByIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->pagesRelatedByIdScheduledForDeletion = null;
                }
            }

                if ($this->collPagesRelatedById !== null) {
            foreach ($this->collPagesRelatedById as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->pageShopsScheduledForDeletion !== null) {
                if (!$this->pageShopsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Page\Model\ORM\PageShopQuery::create()
                        ->filterByPrimaryKeys($this->pageShopsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->pageShopsScheduledForDeletion = null;
                }
            }

                if ($this->collPageShops !== null) {
            foreach ($this->collPageShops as $referrerFK) {
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

        $this->modifiedColumns[PageTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . PageTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(PageTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(PageTableMap::COL_PAGE_ID)) {
            $modifiedColumns[':p' . $index++]  = 'PAGE_ID';
        }
        if ($this->isColumnModified(PageTableMap::COL_HIERARCHY)) {
            $modifiedColumns[':p' . $index++]  = 'HIERARCHY';
        }
        if ($this->isColumnModified(PageTableMap::COL_FOOTER)) {
            $modifiedColumns[':p' . $index++]  = 'FOOTER';
        }
        if ($this->isColumnModified(PageTableMap::COL_HEADER)) {
            $modifiedColumns[':p' . $index++]  = 'HEADER';
        }
        if ($this->isColumnModified(PageTableMap::COL_ALIAS)) {
            $modifiedColumns[':p' . $index++]  = 'ALIAS';
        }
        if ($this->isColumnModified(PageTableMap::COL_REDIRECT)) {
            $modifiedColumns[':p' . $index++]  = 'REDIRECT';
        }
        if ($this->isColumnModified(PageTableMap::COL_REDIRECT_ROUTE)) {
            $modifiedColumns[':p' . $index++]  = 'REDIRECT_ROUTE';
        }
        if ($this->isColumnModified(PageTableMap::COL_REDIRECT_URL)) {
            $modifiedColumns[':p' . $index++]  = 'REDIRECT_URL';
        }

        $sql = sprintf(
            'INSERT INTO page (%s) VALUES (%s)',
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
                    case 'PAGE_ID':                        
                        $stmt->bindValue($identifier, $this->page_id, PDO::PARAM_INT);
                        break;
                    case 'HIERARCHY':                        
                        $stmt->bindValue($identifier, $this->hierarchy, PDO::PARAM_INT);
                        break;
                    case 'FOOTER':                        
                        $stmt->bindValue($identifier, $this->footer, PDO::PARAM_INT);
                        break;
                    case 'HEADER':                        
                        $stmt->bindValue($identifier, $this->header, PDO::PARAM_INT);
                        break;
                    case 'ALIAS':                        
                        $stmt->bindValue($identifier, $this->alias, PDO::PARAM_STR);
                        break;
                    case 'REDIRECT':                        
                        $stmt->bindValue($identifier, $this->redirect, PDO::PARAM_INT);
                        break;
                    case 'REDIRECT_ROUTE':                        
                        $stmt->bindValue($identifier, $this->redirect_route, PDO::PARAM_STR);
                        break;
                    case 'REDIRECT_URL':                        
                        $stmt->bindValue($identifier, $this->redirect_url, PDO::PARAM_STR);
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
        $pos = PageTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getPageId();
                break;
            case 2:
                return $this->getHierarchy();
                break;
            case 3:
                return $this->getFooter();
                break;
            case 4:
                return $this->getHeader();
                break;
            case 5:
                return $this->getAlias();
                break;
            case 6:
                return $this->getRedirect();
                break;
            case 7:
                return $this->getRedirectRoute();
                break;
            case 8:
                return $this->getRedirectUrl();
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
        if (isset($alreadyDumpedObjects['Page'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Page'][$this->getPrimaryKey()] = true;
        $keys = PageTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getPageId(),
            $keys[2] => $this->getHierarchy(),
            $keys[3] => $this->getFooter(),
            $keys[4] => $this->getHeader(),
            $keys[5] => $this->getAlias(),
            $keys[6] => $this->getRedirect(),
            $keys[7] => $this->getRedirectRoute(),
            $keys[8] => $this->getRedirectUrl(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }
        
        if ($includeForeignObjects) {
            if (null !== $this->aPageRelatedByPageId) {
                $result['PageRelatedByPageId'] = $this->aPageRelatedByPageId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collPagesRelatedById) {
                $result['PagesRelatedById'] = $this->collPagesRelatedById->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collPageShops) {
                $result['PageShops'] = $this->collPageShops->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = PageTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

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
                $this->setPageId($value);
                break;
            case 2:
                $this->setHierarchy($value);
                break;
            case 3:
                $this->setFooter($value);
                break;
            case 4:
                $this->setHeader($value);
                break;
            case 5:
                $this->setAlias($value);
                break;
            case 6:
                $this->setRedirect($value);
                break;
            case 7:
                $this->setRedirectRoute($value);
                break;
            case 8:
                $this->setRedirectUrl($value);
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
        $keys = PageTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setPageId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setHierarchy($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setFooter($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setHeader($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setAlias($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setRedirect($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setRedirectRoute($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setRedirectUrl($arr[$keys[8]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(PageTableMap::DATABASE_NAME);

        if ($this->isColumnModified(PageTableMap::COL_ID)) $criteria->add(PageTableMap::COL_ID, $this->id);
        if ($this->isColumnModified(PageTableMap::COL_PAGE_ID)) $criteria->add(PageTableMap::COL_PAGE_ID, $this->page_id);
        if ($this->isColumnModified(PageTableMap::COL_HIERARCHY)) $criteria->add(PageTableMap::COL_HIERARCHY, $this->hierarchy);
        if ($this->isColumnModified(PageTableMap::COL_FOOTER)) $criteria->add(PageTableMap::COL_FOOTER, $this->footer);
        if ($this->isColumnModified(PageTableMap::COL_HEADER)) $criteria->add(PageTableMap::COL_HEADER, $this->header);
        if ($this->isColumnModified(PageTableMap::COL_ALIAS)) $criteria->add(PageTableMap::COL_ALIAS, $this->alias);
        if ($this->isColumnModified(PageTableMap::COL_REDIRECT)) $criteria->add(PageTableMap::COL_REDIRECT, $this->redirect);
        if ($this->isColumnModified(PageTableMap::COL_REDIRECT_ROUTE)) $criteria->add(PageTableMap::COL_REDIRECT_ROUTE, $this->redirect_route);
        if ($this->isColumnModified(PageTableMap::COL_REDIRECT_URL)) $criteria->add(PageTableMap::COL_REDIRECT_URL, $this->redirect_url);

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
        $criteria = new Criteria(PageTableMap::DATABASE_NAME);
        $criteria->add(PageTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \Gekosale\Plugin\Page\Model\ORM\Page (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setPageId($this->getPageId());
        $copyObj->setHierarchy($this->getHierarchy());
        $copyObj->setFooter($this->getFooter());
        $copyObj->setHeader($this->getHeader());
        $copyObj->setAlias($this->getAlias());
        $copyObj->setRedirect($this->getRedirect());
        $copyObj->setRedirectRoute($this->getRedirectRoute());
        $copyObj->setRedirectUrl($this->getRedirectUrl());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getPagesRelatedById() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPageRelatedById($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getPageShops() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPageShop($relObj->copy($deepCopy));
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
     * @return                 \Gekosale\Plugin\Page\Model\ORM\Page Clone of current object.
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
     * Declares an association between this object and a ChildPage object.
     *
     * @param                  ChildPage $v
     * @return                 \Gekosale\Plugin\Page\Model\ORM\Page The current object (for fluent API support)
     * @throws PropelException
     */
    public function setPageRelatedByPageId(ChildPage $v = null)
    {
        if ($v === null) {
            $this->setPageId(NULL);
        } else {
            $this->setPageId($v->getId());
        }

        $this->aPageRelatedByPageId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildPage object, it will not be re-added.
        if ($v !== null) {
            $v->addPageRelatedById($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildPage object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildPage The associated ChildPage object.
     * @throws PropelException
     */
    public function getPageRelatedByPageId(ConnectionInterface $con = null)
    {
        if ($this->aPageRelatedByPageId === null && ($this->page_id !== null)) {
            $this->aPageRelatedByPageId = ChildPageQuery::create()->findPk($this->page_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aPageRelatedByPageId->addPagesRelatedById($this);
             */
        }

        return $this->aPageRelatedByPageId;
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
        if ('PageRelatedById' == $relationName) {
            return $this->initPagesRelatedById();
        }
        if ('PageShop' == $relationName) {
            return $this->initPageShops();
        }
    }

    /**
     * Clears out the collPagesRelatedById collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addPagesRelatedById()
     */
    public function clearPagesRelatedById()
    {
        $this->collPagesRelatedById = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collPagesRelatedById collection loaded partially.
     */
    public function resetPartialPagesRelatedById($v = true)
    {
        $this->collPagesRelatedByIdPartial = $v;
    }

    /**
     * Initializes the collPagesRelatedById collection.
     *
     * By default this just sets the collPagesRelatedById collection to an empty array (like clearcollPagesRelatedById());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPagesRelatedById($overrideExisting = true)
    {
        if (null !== $this->collPagesRelatedById && !$overrideExisting) {
            return;
        }
        $this->collPagesRelatedById = new ObjectCollection();
        $this->collPagesRelatedById->setModel('\Gekosale\Plugin\Page\Model\ORM\Page');
    }

    /**
     * Gets an array of ChildPage objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildPage is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildPage[] List of ChildPage objects
     * @throws PropelException
     */
    public function getPagesRelatedById($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collPagesRelatedByIdPartial && !$this->isNew();
        if (null === $this->collPagesRelatedById || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPagesRelatedById) {
                // return empty collection
                $this->initPagesRelatedById();
            } else {
                $collPagesRelatedById = ChildPageQuery::create(null, $criteria)
                    ->filterByPageRelatedByPageId($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collPagesRelatedByIdPartial && count($collPagesRelatedById)) {
                        $this->initPagesRelatedById(false);

                        foreach ($collPagesRelatedById as $obj) {
                            if (false == $this->collPagesRelatedById->contains($obj)) {
                                $this->collPagesRelatedById->append($obj);
                            }
                        }

                        $this->collPagesRelatedByIdPartial = true;
                    }

                    reset($collPagesRelatedById);

                    return $collPagesRelatedById;
                }

                if ($partial && $this->collPagesRelatedById) {
                    foreach ($this->collPagesRelatedById as $obj) {
                        if ($obj->isNew()) {
                            $collPagesRelatedById[] = $obj;
                        }
                    }
                }

                $this->collPagesRelatedById = $collPagesRelatedById;
                $this->collPagesRelatedByIdPartial = false;
            }
        }

        return $this->collPagesRelatedById;
    }

    /**
     * Sets a collection of PageRelatedById objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $pagesRelatedById A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildPage The current object (for fluent API support)
     */
    public function setPagesRelatedById(Collection $pagesRelatedById, ConnectionInterface $con = null)
    {
        $pagesRelatedByIdToDelete = $this->getPagesRelatedById(new Criteria(), $con)->diff($pagesRelatedById);

        
        $this->pagesRelatedByIdScheduledForDeletion = $pagesRelatedByIdToDelete;

        foreach ($pagesRelatedByIdToDelete as $pageRelatedByIdRemoved) {
            $pageRelatedByIdRemoved->setPageRelatedByPageId(null);
        }

        $this->collPagesRelatedById = null;
        foreach ($pagesRelatedById as $pageRelatedById) {
            $this->addPageRelatedById($pageRelatedById);
        }

        $this->collPagesRelatedById = $pagesRelatedById;
        $this->collPagesRelatedByIdPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Page objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Page objects.
     * @throws PropelException
     */
    public function countPagesRelatedById(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collPagesRelatedByIdPartial && !$this->isNew();
        if (null === $this->collPagesRelatedById || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPagesRelatedById) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getPagesRelatedById());
            }

            $query = ChildPageQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPageRelatedByPageId($this)
                ->count($con);
        }

        return count($this->collPagesRelatedById);
    }

    /**
     * Method called to associate a ChildPage object to this object
     * through the ChildPage foreign key attribute.
     *
     * @param    ChildPage $l ChildPage
     * @return   \Gekosale\Plugin\Page\Model\ORM\Page The current object (for fluent API support)
     */
    public function addPageRelatedById(ChildPage $l)
    {
        if ($this->collPagesRelatedById === null) {
            $this->initPagesRelatedById();
            $this->collPagesRelatedByIdPartial = true;
        }

        if (!in_array($l, $this->collPagesRelatedById->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddPageRelatedById($l);
        }

        return $this;
    }

    /**
     * @param PageRelatedById $pageRelatedById The pageRelatedById object to add.
     */
    protected function doAddPageRelatedById($pageRelatedById)
    {
        $this->collPagesRelatedById[]= $pageRelatedById;
        $pageRelatedById->setPageRelatedByPageId($this);
    }

    /**
     * @param  PageRelatedById $pageRelatedById The pageRelatedById object to remove.
     * @return ChildPage The current object (for fluent API support)
     */
    public function removePageRelatedById($pageRelatedById)
    {
        if ($this->getPagesRelatedById()->contains($pageRelatedById)) {
            $this->collPagesRelatedById->remove($this->collPagesRelatedById->search($pageRelatedById));
            if (null === $this->pagesRelatedByIdScheduledForDeletion) {
                $this->pagesRelatedByIdScheduledForDeletion = clone $this->collPagesRelatedById;
                $this->pagesRelatedByIdScheduledForDeletion->clear();
            }
            $this->pagesRelatedByIdScheduledForDeletion[]= $pageRelatedById;
            $pageRelatedById->setPageRelatedByPageId(null);
        }

        return $this;
    }

    /**
     * Clears out the collPageShops collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addPageShops()
     */
    public function clearPageShops()
    {
        $this->collPageShops = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collPageShops collection loaded partially.
     */
    public function resetPartialPageShops($v = true)
    {
        $this->collPageShopsPartial = $v;
    }

    /**
     * Initializes the collPageShops collection.
     *
     * By default this just sets the collPageShops collection to an empty array (like clearcollPageShops());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPageShops($overrideExisting = true)
    {
        if (null !== $this->collPageShops && !$overrideExisting) {
            return;
        }
        $this->collPageShops = new ObjectCollection();
        $this->collPageShops->setModel('\Gekosale\Plugin\Page\Model\ORM\PageShop');
    }

    /**
     * Gets an array of ChildPageShop objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildPage is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildPageShop[] List of ChildPageShop objects
     * @throws PropelException
     */
    public function getPageShops($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collPageShopsPartial && !$this->isNew();
        if (null === $this->collPageShops || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPageShops) {
                // return empty collection
                $this->initPageShops();
            } else {
                $collPageShops = ChildPageShopQuery::create(null, $criteria)
                    ->filterByPage($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collPageShopsPartial && count($collPageShops)) {
                        $this->initPageShops(false);

                        foreach ($collPageShops as $obj) {
                            if (false == $this->collPageShops->contains($obj)) {
                                $this->collPageShops->append($obj);
                            }
                        }

                        $this->collPageShopsPartial = true;
                    }

                    reset($collPageShops);

                    return $collPageShops;
                }

                if ($partial && $this->collPageShops) {
                    foreach ($this->collPageShops as $obj) {
                        if ($obj->isNew()) {
                            $collPageShops[] = $obj;
                        }
                    }
                }

                $this->collPageShops = $collPageShops;
                $this->collPageShopsPartial = false;
            }
        }

        return $this->collPageShops;
    }

    /**
     * Sets a collection of PageShop objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $pageShops A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildPage The current object (for fluent API support)
     */
    public function setPageShops(Collection $pageShops, ConnectionInterface $con = null)
    {
        $pageShopsToDelete = $this->getPageShops(new Criteria(), $con)->diff($pageShops);

        
        $this->pageShopsScheduledForDeletion = $pageShopsToDelete;

        foreach ($pageShopsToDelete as $pageShopRemoved) {
            $pageShopRemoved->setPage(null);
        }

        $this->collPageShops = null;
        foreach ($pageShops as $pageShop) {
            $this->addPageShop($pageShop);
        }

        $this->collPageShops = $pageShops;
        $this->collPageShopsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related PageShop objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related PageShop objects.
     * @throws PropelException
     */
    public function countPageShops(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collPageShopsPartial && !$this->isNew();
        if (null === $this->collPageShops || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPageShops) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getPageShops());
            }

            $query = ChildPageShopQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPage($this)
                ->count($con);
        }

        return count($this->collPageShops);
    }

    /**
     * Method called to associate a ChildPageShop object to this object
     * through the ChildPageShop foreign key attribute.
     *
     * @param    ChildPageShop $l ChildPageShop
     * @return   \Gekosale\Plugin\Page\Model\ORM\Page The current object (for fluent API support)
     */
    public function addPageShop(ChildPageShop $l)
    {
        if ($this->collPageShops === null) {
            $this->initPageShops();
            $this->collPageShopsPartial = true;
        }

        if (!in_array($l, $this->collPageShops->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddPageShop($l);
        }

        return $this;
    }

    /**
     * @param PageShop $pageShop The pageShop object to add.
     */
    protected function doAddPageShop($pageShop)
    {
        $this->collPageShops[]= $pageShop;
        $pageShop->setPage($this);
    }

    /**
     * @param  PageShop $pageShop The pageShop object to remove.
     * @return ChildPage The current object (for fluent API support)
     */
    public function removePageShop($pageShop)
    {
        if ($this->getPageShops()->contains($pageShop)) {
            $this->collPageShops->remove($this->collPageShops->search($pageShop));
            if (null === $this->pageShopsScheduledForDeletion) {
                $this->pageShopsScheduledForDeletion = clone $this->collPageShops;
                $this->pageShopsScheduledForDeletion->clear();
            }
            $this->pageShopsScheduledForDeletion[]= clone $pageShop;
            $pageShop->setPage(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Page is new, it will return
     * an empty collection; or if this Page has previously
     * been saved, it will retrieve related PageShops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Page.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildPageShop[] List of ChildPageShop objects
     */
    public function getPageShopsJoinShop($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildPageShopQuery::create(null, $criteria);
        $query->joinWith('Shop', $joinBehavior);

        return $this->getPageShops($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->page_id = null;
        $this->hierarchy = null;
        $this->footer = null;
        $this->header = null;
        $this->alias = null;
        $this->redirect = null;
        $this->redirect_route = null;
        $this->redirect_url = null;
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
            if ($this->collPagesRelatedById) {
                foreach ($this->collPagesRelatedById as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collPageShops) {
                foreach ($this->collPageShops as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collPagesRelatedById = null;
        $this->collPageShops = null;
        $this->aPageRelatedByPageId = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(PageTableMap::DEFAULT_STRING_FORMAT);
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
