<?php

namespace Gekosale\Plugin\File\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute as ChildProductAttribute;
use Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeQuery;
use Gekosale\Plugin\Attribute\Model\ORM\Base\ProductAttribute;
use Gekosale\Plugin\Blog\Model\ORM\BlogPhotoQuery;
use Gekosale\Plugin\Blog\Model\ORM\BlogPhoto as ChildBlogPhoto;
use Gekosale\Plugin\Blog\Model\ORM\Base\BlogPhoto;
use Gekosale\Plugin\Category\Model\ORM\CategoryQuery;
use Gekosale\Plugin\Category\Model\ORM\Category as ChildCategory;
use Gekosale\Plugin\Category\Model\ORM\Base\Category;
use Gekosale\Plugin\Company\Model\ORM\Company as ChildCompany;
use Gekosale\Plugin\Company\Model\ORM\CompanyQuery;
use Gekosale\Plugin\Company\Model\ORM\Base\Company;
use Gekosale\Plugin\Deliverer\Model\ORM\Deliverer as ChildDeliverer;
use Gekosale\Plugin\Deliverer\Model\ORM\DelivererQuery;
use Gekosale\Plugin\Deliverer\Model\ORM\Base\Deliverer;
use Gekosale\Plugin\File\Model\ORM\File as ChildFile;
use Gekosale\Plugin\File\Model\ORM\FileQuery as ChildFileQuery;
use Gekosale\Plugin\File\Model\ORM\Map\FileTableMap;
use Gekosale\Plugin\Producer\Model\ORM\Producer as ChildProducer;
use Gekosale\Plugin\Producer\Model\ORM\ProducerQuery;
use Gekosale\Plugin\Producer\Model\ORM\Base\Producer;
use Gekosale\Plugin\Product\Model\ORM\ProductFile as ChildProductFile;
use Gekosale\Plugin\Product\Model\ORM\ProductPhoto as ChildProductPhoto;
use Gekosale\Plugin\Product\Model\ORM\ProductFileQuery;
use Gekosale\Plugin\Product\Model\ORM\ProductPhotoQuery;
use Gekosale\Plugin\Product\Model\ORM\Base\ProductFile;
use Gekosale\Plugin\Product\Model\ORM\Base\ProductPhoto;
use Gekosale\Plugin\User\Model\ORM\UserData as ChildUserData;
use Gekosale\Plugin\User\Model\ORM\UserDataQuery;
use Gekosale\Plugin\User\Model\ORM\Base\UserData;
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

abstract class File implements ActiveRecordInterface 
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Gekosale\\Plugin\\File\\Model\\ORM\\Map\\FileTableMap';


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
     * The value for the file_type_id field.
     * @var        int
     */
    protected $file_type_id;

    /**
     * The value for the file_extension_id field.
     * @var        int
     */
    protected $file_extension_id;

    /**
     * The value for the is_visible field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $is_visible;

    /**
     * @var        ObjectCollection|ChildBlogPhoto[] Collection to store aggregation of ChildBlogPhoto objects.
     */
    protected $collBlogPhotos;
    protected $collBlogPhotosPartial;

    /**
     * @var        ObjectCollection|ChildCategory[] Collection to store aggregation of ChildCategory objects.
     */
    protected $collCategories;
    protected $collCategoriesPartial;

    /**
     * @var        ObjectCollection|ChildCompany[] Collection to store aggregation of ChildCompany objects.
     */
    protected $collCompanies;
    protected $collCompaniesPartial;

    /**
     * @var        ObjectCollection|ChildDeliverer[] Collection to store aggregation of ChildDeliverer objects.
     */
    protected $collDeliverers;
    protected $collDeliverersPartial;

    /**
     * @var        ObjectCollection|ChildProducer[] Collection to store aggregation of ChildProducer objects.
     */
    protected $collProducers;
    protected $collProducersPartial;

    /**
     * @var        ObjectCollection|ChildProductAttribute[] Collection to store aggregation of ChildProductAttribute objects.
     */
    protected $collProductAttributes;
    protected $collProductAttributesPartial;

    /**
     * @var        ObjectCollection|ChildProductFile[] Collection to store aggregation of ChildProductFile objects.
     */
    protected $collProductFiles;
    protected $collProductFilesPartial;

    /**
     * @var        ObjectCollection|ChildProductPhoto[] Collection to store aggregation of ChildProductPhoto objects.
     */
    protected $collProductPhotos;
    protected $collProductPhotosPartial;

    /**
     * @var        ObjectCollection|ChildUserData[] Collection to store aggregation of ChildUserData objects.
     */
    protected $collUserDatas;
    protected $collUserDatasPartial;

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
    protected $blogPhotosScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $categoriesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $companiesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $deliverersScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $producersScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $productAttributesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $productFilesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $productPhotosScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $userDatasScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->is_visible = 0;
    }

    /**
     * Initializes internal state of Gekosale\Plugin\File\Model\ORM\Base\File object.
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
     * Compares this with another <code>File</code> instance.  If
     * <code>obj</code> is an instance of <code>File</code>, delegates to
     * <code>equals(File)</code>.  Otherwise, returns <code>false</code>.
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
     * @return File The current object, for fluid interface
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
     * @return File The current object, for fluid interface
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
     * Get the [file_type_id] column value.
     * 
     * @return   int
     */
    public function getFileTypeId()
    {

        return $this->file_type_id;
    }

    /**
     * Get the [file_extension_id] column value.
     * 
     * @return   int
     */
    public function getFileExtensionId()
    {

        return $this->file_extension_id;
    }

    /**
     * Get the [is_visible] column value.
     * 
     * @return   int
     */
    public function getIsVisible()
    {

        return $this->is_visible;
    }

    /**
     * Set the value of [id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\File\Model\ORM\File The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[FileTableMap::COL_ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [name] column.
     * 
     * @param      string $v new value
     * @return   \Gekosale\Plugin\File\Model\ORM\File The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[FileTableMap::COL_NAME] = true;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [file_type_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\File\Model\ORM\File The current object (for fluent API support)
     */
    public function setFileTypeId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->file_type_id !== $v) {
            $this->file_type_id = $v;
            $this->modifiedColumns[FileTableMap::COL_FILE_TYPE_ID] = true;
        }


        return $this;
    } // setFileTypeId()

    /**
     * Set the value of [file_extension_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\File\Model\ORM\File The current object (for fluent API support)
     */
    public function setFileExtensionId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->file_extension_id !== $v) {
            $this->file_extension_id = $v;
            $this->modifiedColumns[FileTableMap::COL_FILE_EXTENSION_ID] = true;
        }


        return $this;
    } // setFileExtensionId()

    /**
     * Set the value of [is_visible] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\File\Model\ORM\File The current object (for fluent API support)
     */
    public function setIsVisible($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->is_visible !== $v) {
            $this->is_visible = $v;
            $this->modifiedColumns[FileTableMap::COL_IS_VISIBLE] = true;
        }


        return $this;
    } // setIsVisible()

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
            if ($this->is_visible !== 0) {
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


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : FileTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : FileTableMap::translateFieldName('Name', TableMap::TYPE_PHPNAME, $indexType)];
            $this->name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : FileTableMap::translateFieldName('FileTypeId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->file_type_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : FileTableMap::translateFieldName('FileExtensionId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->file_extension_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : FileTableMap::translateFieldName('IsVisible', TableMap::TYPE_PHPNAME, $indexType)];
            $this->is_visible = (null !== $col) ? (int) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 5; // 5 = FileTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Gekosale\Plugin\File\Model\ORM\File object", 0, $e);
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
            $con = Propel::getServiceContainer()->getReadConnection(FileTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildFileQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collBlogPhotos = null;

            $this->collCategories = null;

            $this->collCompanies = null;

            $this->collDeliverers = null;

            $this->collProducers = null;

            $this->collProductAttributes = null;

            $this->collProductFiles = null;

            $this->collProductPhotos = null;

            $this->collUserDatas = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see File::setDeleted()
     * @see File::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(FileTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildFileQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(FileTableMap::DATABASE_NAME);
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
                FileTableMap::addInstanceToPool($this);
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

            if ($this->blogPhotosScheduledForDeletion !== null) {
                if (!$this->blogPhotosScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Blog\Model\ORM\BlogPhotoQuery::create()
                        ->filterByPrimaryKeys($this->blogPhotosScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->blogPhotosScheduledForDeletion = null;
                }
            }

                if ($this->collBlogPhotos !== null) {
            foreach ($this->collBlogPhotos as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->categoriesScheduledForDeletion !== null) {
                if (!$this->categoriesScheduledForDeletion->isEmpty()) {
                    foreach ($this->categoriesScheduledForDeletion as $category) {
                        // need to save related object because we set the relation to null
                        $category->save($con);
                    }
                    $this->categoriesScheduledForDeletion = null;
                }
            }

                if ($this->collCategories !== null) {
            foreach ($this->collCategories as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->companiesScheduledForDeletion !== null) {
                if (!$this->companiesScheduledForDeletion->isEmpty()) {
                    foreach ($this->companiesScheduledForDeletion as $company) {
                        // need to save related object because we set the relation to null
                        $company->save($con);
                    }
                    $this->companiesScheduledForDeletion = null;
                }
            }

                if ($this->collCompanies !== null) {
            foreach ($this->collCompanies as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->deliverersScheduledForDeletion !== null) {
                if (!$this->deliverersScheduledForDeletion->isEmpty()) {
                    foreach ($this->deliverersScheduledForDeletion as $deliverer) {
                        // need to save related object because we set the relation to null
                        $deliverer->save($con);
                    }
                    $this->deliverersScheduledForDeletion = null;
                }
            }

                if ($this->collDeliverers !== null) {
            foreach ($this->collDeliverers as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->producersScheduledForDeletion !== null) {
                if (!$this->producersScheduledForDeletion->isEmpty()) {
                    foreach ($this->producersScheduledForDeletion as $producer) {
                        // need to save related object because we set the relation to null
                        $producer->save($con);
                    }
                    $this->producersScheduledForDeletion = null;
                }
            }

                if ($this->collProducers !== null) {
            foreach ($this->collProducers as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->productAttributesScheduledForDeletion !== null) {
                if (!$this->productAttributesScheduledForDeletion->isEmpty()) {
                    foreach ($this->productAttributesScheduledForDeletion as $productAttribute) {
                        // need to save related object because we set the relation to null
                        $productAttribute->save($con);
                    }
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

            if ($this->productFilesScheduledForDeletion !== null) {
                if (!$this->productFilesScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Product\Model\ORM\ProductFileQuery::create()
                        ->filterByPrimaryKeys($this->productFilesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->productFilesScheduledForDeletion = null;
                }
            }

                if ($this->collProductFiles !== null) {
            foreach ($this->collProductFiles as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->productPhotosScheduledForDeletion !== null) {
                if (!$this->productPhotosScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Product\Model\ORM\ProductPhotoQuery::create()
                        ->filterByPrimaryKeys($this->productPhotosScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->productPhotosScheduledForDeletion = null;
                }
            }

                if ($this->collProductPhotos !== null) {
            foreach ($this->collProductPhotos as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->userDatasScheduledForDeletion !== null) {
                if (!$this->userDatasScheduledForDeletion->isEmpty()) {
                    foreach ($this->userDatasScheduledForDeletion as $userData) {
                        // need to save related object because we set the relation to null
                        $userData->save($con);
                    }
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

        $this->modifiedColumns[FileTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . FileTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(FileTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(FileTableMap::COL_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'NAME';
        }
        if ($this->isColumnModified(FileTableMap::COL_FILE_TYPE_ID)) {
            $modifiedColumns[':p' . $index++]  = 'FILE_TYPE_ID';
        }
        if ($this->isColumnModified(FileTableMap::COL_FILE_EXTENSION_ID)) {
            $modifiedColumns[':p' . $index++]  = 'FILE_EXTENSION_ID';
        }
        if ($this->isColumnModified(FileTableMap::COL_IS_VISIBLE)) {
            $modifiedColumns[':p' . $index++]  = 'IS_VISIBLE';
        }

        $sql = sprintf(
            'INSERT INTO file (%s) VALUES (%s)',
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
                    case 'FILE_TYPE_ID':                        
                        $stmt->bindValue($identifier, $this->file_type_id, PDO::PARAM_INT);
                        break;
                    case 'FILE_EXTENSION_ID':                        
                        $stmt->bindValue($identifier, $this->file_extension_id, PDO::PARAM_INT);
                        break;
                    case 'IS_VISIBLE':                        
                        $stmt->bindValue($identifier, $this->is_visible, PDO::PARAM_INT);
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
        $pos = FileTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getFileTypeId();
                break;
            case 3:
                return $this->getFileExtensionId();
                break;
            case 4:
                return $this->getIsVisible();
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
        if (isset($alreadyDumpedObjects['File'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['File'][$this->getPrimaryKey()] = true;
        $keys = FileTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getName(),
            $keys[2] => $this->getFileTypeId(),
            $keys[3] => $this->getFileExtensionId(),
            $keys[4] => $this->getIsVisible(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }
        
        if ($includeForeignObjects) {
            if (null !== $this->collBlogPhotos) {
                $result['BlogPhotos'] = $this->collBlogPhotos->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCategories) {
                $result['Categories'] = $this->collCategories->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCompanies) {
                $result['Companies'] = $this->collCompanies->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collDeliverers) {
                $result['Deliverers'] = $this->collDeliverers->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProducers) {
                $result['Producers'] = $this->collProducers->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductAttributes) {
                $result['ProductAttributes'] = $this->collProductAttributes->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductFiles) {
                $result['ProductFiles'] = $this->collProductFiles->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductPhotos) {
                $result['ProductPhotos'] = $this->collProductPhotos->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collUserDatas) {
                $result['UserDatas'] = $this->collUserDatas->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = FileTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

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
                $this->setFileTypeId($value);
                break;
            case 3:
                $this->setFileExtensionId($value);
                break;
            case 4:
                $this->setIsVisible($value);
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
        $keys = FileTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setName($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setFileTypeId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setFileExtensionId($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setIsVisible($arr[$keys[4]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(FileTableMap::DATABASE_NAME);

        if ($this->isColumnModified(FileTableMap::COL_ID)) $criteria->add(FileTableMap::COL_ID, $this->id);
        if ($this->isColumnModified(FileTableMap::COL_NAME)) $criteria->add(FileTableMap::COL_NAME, $this->name);
        if ($this->isColumnModified(FileTableMap::COL_FILE_TYPE_ID)) $criteria->add(FileTableMap::COL_FILE_TYPE_ID, $this->file_type_id);
        if ($this->isColumnModified(FileTableMap::COL_FILE_EXTENSION_ID)) $criteria->add(FileTableMap::COL_FILE_EXTENSION_ID, $this->file_extension_id);
        if ($this->isColumnModified(FileTableMap::COL_IS_VISIBLE)) $criteria->add(FileTableMap::COL_IS_VISIBLE, $this->is_visible);

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
        $criteria = new Criteria(FileTableMap::DATABASE_NAME);
        $criteria->add(FileTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \Gekosale\Plugin\File\Model\ORM\File (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setName($this->getName());
        $copyObj->setFileTypeId($this->getFileTypeId());
        $copyObj->setFileExtensionId($this->getFileExtensionId());
        $copyObj->setIsVisible($this->getIsVisible());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getBlogPhotos() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addBlogPhoto($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCategories() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCategory($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCompanies() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCompany($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getDeliverers() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDeliverer($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProducers() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProducer($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductAttributes() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductAttribute($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductFiles() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductFile($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductPhotos() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductPhoto($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getUserDatas() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addUserData($relObj->copy($deepCopy));
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
     * @return                 \Gekosale\Plugin\File\Model\ORM\File Clone of current object.
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
        if ('BlogPhoto' == $relationName) {
            return $this->initBlogPhotos();
        }
        if ('Category' == $relationName) {
            return $this->initCategories();
        }
        if ('Company' == $relationName) {
            return $this->initCompanies();
        }
        if ('Deliverer' == $relationName) {
            return $this->initDeliverers();
        }
        if ('Producer' == $relationName) {
            return $this->initProducers();
        }
        if ('ProductAttribute' == $relationName) {
            return $this->initProductAttributes();
        }
        if ('ProductFile' == $relationName) {
            return $this->initProductFiles();
        }
        if ('ProductPhoto' == $relationName) {
            return $this->initProductPhotos();
        }
        if ('UserData' == $relationName) {
            return $this->initUserDatas();
        }
    }

    /**
     * Clears out the collBlogPhotos collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addBlogPhotos()
     */
    public function clearBlogPhotos()
    {
        $this->collBlogPhotos = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collBlogPhotos collection loaded partially.
     */
    public function resetPartialBlogPhotos($v = true)
    {
        $this->collBlogPhotosPartial = $v;
    }

    /**
     * Initializes the collBlogPhotos collection.
     *
     * By default this just sets the collBlogPhotos collection to an empty array (like clearcollBlogPhotos());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initBlogPhotos($overrideExisting = true)
    {
        if (null !== $this->collBlogPhotos && !$overrideExisting) {
            return;
        }
        $this->collBlogPhotos = new ObjectCollection();
        $this->collBlogPhotos->setModel('\Gekosale\Plugin\Blog\Model\ORM\BlogPhoto');
    }

    /**
     * Gets an array of ChildBlogPhoto objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildFile is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildBlogPhoto[] List of ChildBlogPhoto objects
     * @throws PropelException
     */
    public function getBlogPhotos($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collBlogPhotosPartial && !$this->isNew();
        if (null === $this->collBlogPhotos || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collBlogPhotos) {
                // return empty collection
                $this->initBlogPhotos();
            } else {
                $collBlogPhotos = BlogPhotoQuery::create(null, $criteria)
                    ->filterByFile($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collBlogPhotosPartial && count($collBlogPhotos)) {
                        $this->initBlogPhotos(false);

                        foreach ($collBlogPhotos as $obj) {
                            if (false == $this->collBlogPhotos->contains($obj)) {
                                $this->collBlogPhotos->append($obj);
                            }
                        }

                        $this->collBlogPhotosPartial = true;
                    }

                    reset($collBlogPhotos);

                    return $collBlogPhotos;
                }

                if ($partial && $this->collBlogPhotos) {
                    foreach ($this->collBlogPhotos as $obj) {
                        if ($obj->isNew()) {
                            $collBlogPhotos[] = $obj;
                        }
                    }
                }

                $this->collBlogPhotos = $collBlogPhotos;
                $this->collBlogPhotosPartial = false;
            }
        }

        return $this->collBlogPhotos;
    }

    /**
     * Sets a collection of BlogPhoto objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $blogPhotos A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildFile The current object (for fluent API support)
     */
    public function setBlogPhotos(Collection $blogPhotos, ConnectionInterface $con = null)
    {
        $blogPhotosToDelete = $this->getBlogPhotos(new Criteria(), $con)->diff($blogPhotos);

        
        $this->blogPhotosScheduledForDeletion = $blogPhotosToDelete;

        foreach ($blogPhotosToDelete as $blogPhotoRemoved) {
            $blogPhotoRemoved->setFile(null);
        }

        $this->collBlogPhotos = null;
        foreach ($blogPhotos as $blogPhoto) {
            $this->addBlogPhoto($blogPhoto);
        }

        $this->collBlogPhotos = $blogPhotos;
        $this->collBlogPhotosPartial = false;

        return $this;
    }

    /**
     * Returns the number of related BlogPhoto objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related BlogPhoto objects.
     * @throws PropelException
     */
    public function countBlogPhotos(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collBlogPhotosPartial && !$this->isNew();
        if (null === $this->collBlogPhotos || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collBlogPhotos) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getBlogPhotos());
            }

            $query = BlogPhotoQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByFile($this)
                ->count($con);
        }

        return count($this->collBlogPhotos);
    }

    /**
     * Method called to associate a ChildBlogPhoto object to this object
     * through the ChildBlogPhoto foreign key attribute.
     *
     * @param    ChildBlogPhoto $l ChildBlogPhoto
     * @return   \Gekosale\Plugin\File\Model\ORM\File The current object (for fluent API support)
     */
    public function addBlogPhoto(ChildBlogPhoto $l)
    {
        if ($this->collBlogPhotos === null) {
            $this->initBlogPhotos();
            $this->collBlogPhotosPartial = true;
        }

        if (!in_array($l, $this->collBlogPhotos->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddBlogPhoto($l);
        }

        return $this;
    }

    /**
     * @param BlogPhoto $blogPhoto The blogPhoto object to add.
     */
    protected function doAddBlogPhoto($blogPhoto)
    {
        $this->collBlogPhotos[]= $blogPhoto;
        $blogPhoto->setFile($this);
    }

    /**
     * @param  BlogPhoto $blogPhoto The blogPhoto object to remove.
     * @return ChildFile The current object (for fluent API support)
     */
    public function removeBlogPhoto($blogPhoto)
    {
        if ($this->getBlogPhotos()->contains($blogPhoto)) {
            $this->collBlogPhotos->remove($this->collBlogPhotos->search($blogPhoto));
            if (null === $this->blogPhotosScheduledForDeletion) {
                $this->blogPhotosScheduledForDeletion = clone $this->collBlogPhotos;
                $this->blogPhotosScheduledForDeletion->clear();
            }
            $this->blogPhotosScheduledForDeletion[]= $blogPhoto;
            $blogPhoto->setFile(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this File is new, it will return
     * an empty collection; or if this File has previously
     * been saved, it will retrieve related BlogPhotos from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in File.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildBlogPhoto[] List of ChildBlogPhoto objects
     */
    public function getBlogPhotosJoinBlog($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = BlogPhotoQuery::create(null, $criteria);
        $query->joinWith('Blog', $joinBehavior);

        return $this->getBlogPhotos($query, $con);
    }

    /**
     * Clears out the collCategories collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCategories()
     */
    public function clearCategories()
    {
        $this->collCategories = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCategories collection loaded partially.
     */
    public function resetPartialCategories($v = true)
    {
        $this->collCategoriesPartial = $v;
    }

    /**
     * Initializes the collCategories collection.
     *
     * By default this just sets the collCategories collection to an empty array (like clearcollCategories());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCategories($overrideExisting = true)
    {
        if (null !== $this->collCategories && !$overrideExisting) {
            return;
        }
        $this->collCategories = new ObjectCollection();
        $this->collCategories->setModel('\Gekosale\Plugin\Category\Model\ORM\Category');
    }

    /**
     * Gets an array of ChildCategory objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildFile is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildCategory[] List of ChildCategory objects
     * @throws PropelException
     */
    public function getCategories($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCategoriesPartial && !$this->isNew();
        if (null === $this->collCategories || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCategories) {
                // return empty collection
                $this->initCategories();
            } else {
                $collCategories = CategoryQuery::create(null, $criteria)
                    ->filterByFile($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCategoriesPartial && count($collCategories)) {
                        $this->initCategories(false);

                        foreach ($collCategories as $obj) {
                            if (false == $this->collCategories->contains($obj)) {
                                $this->collCategories->append($obj);
                            }
                        }

                        $this->collCategoriesPartial = true;
                    }

                    reset($collCategories);

                    return $collCategories;
                }

                if ($partial && $this->collCategories) {
                    foreach ($this->collCategories as $obj) {
                        if ($obj->isNew()) {
                            $collCategories[] = $obj;
                        }
                    }
                }

                $this->collCategories = $collCategories;
                $this->collCategoriesPartial = false;
            }
        }

        return $this->collCategories;
    }

    /**
     * Sets a collection of Category objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $categories A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildFile The current object (for fluent API support)
     */
    public function setCategories(Collection $categories, ConnectionInterface $con = null)
    {
        $categoriesToDelete = $this->getCategories(new Criteria(), $con)->diff($categories);

        
        $this->categoriesScheduledForDeletion = $categoriesToDelete;

        foreach ($categoriesToDelete as $categoryRemoved) {
            $categoryRemoved->setFile(null);
        }

        $this->collCategories = null;
        foreach ($categories as $category) {
            $this->addCategory($category);
        }

        $this->collCategories = $categories;
        $this->collCategoriesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Category objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Category objects.
     * @throws PropelException
     */
    public function countCategories(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCategoriesPartial && !$this->isNew();
        if (null === $this->collCategories || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCategories) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCategories());
            }

            $query = CategoryQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByFile($this)
                ->count($con);
        }

        return count($this->collCategories);
    }

    /**
     * Method called to associate a ChildCategory object to this object
     * through the ChildCategory foreign key attribute.
     *
     * @param    ChildCategory $l ChildCategory
     * @return   \Gekosale\Plugin\File\Model\ORM\File The current object (for fluent API support)
     */
    public function addCategory(ChildCategory $l)
    {
        if ($this->collCategories === null) {
            $this->initCategories();
            $this->collCategoriesPartial = true;
        }

        if (!in_array($l, $this->collCategories->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCategory($l);
        }

        return $this;
    }

    /**
     * @param Category $category The category object to add.
     */
    protected function doAddCategory($category)
    {
        $this->collCategories[]= $category;
        $category->setFile($this);
    }

    /**
     * @param  Category $category The category object to remove.
     * @return ChildFile The current object (for fluent API support)
     */
    public function removeCategory($category)
    {
        if ($this->getCategories()->contains($category)) {
            $this->collCategories->remove($this->collCategories->search($category));
            if (null === $this->categoriesScheduledForDeletion) {
                $this->categoriesScheduledForDeletion = clone $this->collCategories;
                $this->categoriesScheduledForDeletion->clear();
            }
            $this->categoriesScheduledForDeletion[]= $category;
            $category->setFile(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this File is new, it will return
     * an empty collection; or if this File has previously
     * been saved, it will retrieve related Categories from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in File.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCategory[] List of ChildCategory objects
     */
    public function getCategoriesJoinCategoryRelatedByCategoryId($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = CategoryQuery::create(null, $criteria);
        $query->joinWith('CategoryRelatedByCategoryId', $joinBehavior);

        return $this->getCategories($query, $con);
    }

    /**
     * Clears out the collCompanies collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCompanies()
     */
    public function clearCompanies()
    {
        $this->collCompanies = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCompanies collection loaded partially.
     */
    public function resetPartialCompanies($v = true)
    {
        $this->collCompaniesPartial = $v;
    }

    /**
     * Initializes the collCompanies collection.
     *
     * By default this just sets the collCompanies collection to an empty array (like clearcollCompanies());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCompanies($overrideExisting = true)
    {
        if (null !== $this->collCompanies && !$overrideExisting) {
            return;
        }
        $this->collCompanies = new ObjectCollection();
        $this->collCompanies->setModel('\Gekosale\Plugin\Company\Model\ORM\Company');
    }

    /**
     * Gets an array of ChildCompany objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildFile is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildCompany[] List of ChildCompany objects
     * @throws PropelException
     */
    public function getCompanies($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCompaniesPartial && !$this->isNew();
        if (null === $this->collCompanies || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCompanies) {
                // return empty collection
                $this->initCompanies();
            } else {
                $collCompanies = CompanyQuery::create(null, $criteria)
                    ->filterByFile($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCompaniesPartial && count($collCompanies)) {
                        $this->initCompanies(false);

                        foreach ($collCompanies as $obj) {
                            if (false == $this->collCompanies->contains($obj)) {
                                $this->collCompanies->append($obj);
                            }
                        }

                        $this->collCompaniesPartial = true;
                    }

                    reset($collCompanies);

                    return $collCompanies;
                }

                if ($partial && $this->collCompanies) {
                    foreach ($this->collCompanies as $obj) {
                        if ($obj->isNew()) {
                            $collCompanies[] = $obj;
                        }
                    }
                }

                $this->collCompanies = $collCompanies;
                $this->collCompaniesPartial = false;
            }
        }

        return $this->collCompanies;
    }

    /**
     * Sets a collection of Company objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $companies A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildFile The current object (for fluent API support)
     */
    public function setCompanies(Collection $companies, ConnectionInterface $con = null)
    {
        $companiesToDelete = $this->getCompanies(new Criteria(), $con)->diff($companies);

        
        $this->companiesScheduledForDeletion = $companiesToDelete;

        foreach ($companiesToDelete as $companyRemoved) {
            $companyRemoved->setFile(null);
        }

        $this->collCompanies = null;
        foreach ($companies as $company) {
            $this->addCompany($company);
        }

        $this->collCompanies = $companies;
        $this->collCompaniesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Company objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Company objects.
     * @throws PropelException
     */
    public function countCompanies(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCompaniesPartial && !$this->isNew();
        if (null === $this->collCompanies || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCompanies) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCompanies());
            }

            $query = CompanyQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByFile($this)
                ->count($con);
        }

        return count($this->collCompanies);
    }

    /**
     * Method called to associate a ChildCompany object to this object
     * through the ChildCompany foreign key attribute.
     *
     * @param    ChildCompany $l ChildCompany
     * @return   \Gekosale\Plugin\File\Model\ORM\File The current object (for fluent API support)
     */
    public function addCompany(ChildCompany $l)
    {
        if ($this->collCompanies === null) {
            $this->initCompanies();
            $this->collCompaniesPartial = true;
        }

        if (!in_array($l, $this->collCompanies->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCompany($l);
        }

        return $this;
    }

    /**
     * @param Company $company The company object to add.
     */
    protected function doAddCompany($company)
    {
        $this->collCompanies[]= $company;
        $company->setFile($this);
    }

    /**
     * @param  Company $company The company object to remove.
     * @return ChildFile The current object (for fluent API support)
     */
    public function removeCompany($company)
    {
        if ($this->getCompanies()->contains($company)) {
            $this->collCompanies->remove($this->collCompanies->search($company));
            if (null === $this->companiesScheduledForDeletion) {
                $this->companiesScheduledForDeletion = clone $this->collCompanies;
                $this->companiesScheduledForDeletion->clear();
            }
            $this->companiesScheduledForDeletion[]= $company;
            $company->setFile(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this File is new, it will return
     * an empty collection; or if this File has previously
     * been saved, it will retrieve related Companies from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in File.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCompany[] List of ChildCompany objects
     */
    public function getCompaniesJoinCountry($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = CompanyQuery::create(null, $criteria);
        $query->joinWith('Country', $joinBehavior);

        return $this->getCompanies($query, $con);
    }

    /**
     * Clears out the collDeliverers collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDeliverers()
     */
    public function clearDeliverers()
    {
        $this->collDeliverers = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDeliverers collection loaded partially.
     */
    public function resetPartialDeliverers($v = true)
    {
        $this->collDeliverersPartial = $v;
    }

    /**
     * Initializes the collDeliverers collection.
     *
     * By default this just sets the collDeliverers collection to an empty array (like clearcollDeliverers());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDeliverers($overrideExisting = true)
    {
        if (null !== $this->collDeliverers && !$overrideExisting) {
            return;
        }
        $this->collDeliverers = new ObjectCollection();
        $this->collDeliverers->setModel('\Gekosale\Plugin\Deliverer\Model\ORM\Deliverer');
    }

    /**
     * Gets an array of ChildDeliverer objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildFile is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildDeliverer[] List of ChildDeliverer objects
     * @throws PropelException
     */
    public function getDeliverers($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDeliverersPartial && !$this->isNew();
        if (null === $this->collDeliverers || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDeliverers) {
                // return empty collection
                $this->initDeliverers();
            } else {
                $collDeliverers = DelivererQuery::create(null, $criteria)
                    ->filterByFile($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDeliverersPartial && count($collDeliverers)) {
                        $this->initDeliverers(false);

                        foreach ($collDeliverers as $obj) {
                            if (false == $this->collDeliverers->contains($obj)) {
                                $this->collDeliverers->append($obj);
                            }
                        }

                        $this->collDeliverersPartial = true;
                    }

                    reset($collDeliverers);

                    return $collDeliverers;
                }

                if ($partial && $this->collDeliverers) {
                    foreach ($this->collDeliverers as $obj) {
                        if ($obj->isNew()) {
                            $collDeliverers[] = $obj;
                        }
                    }
                }

                $this->collDeliverers = $collDeliverers;
                $this->collDeliverersPartial = false;
            }
        }

        return $this->collDeliverers;
    }

    /**
     * Sets a collection of Deliverer objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $deliverers A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildFile The current object (for fluent API support)
     */
    public function setDeliverers(Collection $deliverers, ConnectionInterface $con = null)
    {
        $deliverersToDelete = $this->getDeliverers(new Criteria(), $con)->diff($deliverers);

        
        $this->deliverersScheduledForDeletion = $deliverersToDelete;

        foreach ($deliverersToDelete as $delivererRemoved) {
            $delivererRemoved->setFile(null);
        }

        $this->collDeliverers = null;
        foreach ($deliverers as $deliverer) {
            $this->addDeliverer($deliverer);
        }

        $this->collDeliverers = $deliverers;
        $this->collDeliverersPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Deliverer objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Deliverer objects.
     * @throws PropelException
     */
    public function countDeliverers(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDeliverersPartial && !$this->isNew();
        if (null === $this->collDeliverers || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDeliverers) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDeliverers());
            }

            $query = DelivererQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByFile($this)
                ->count($con);
        }

        return count($this->collDeliverers);
    }

    /**
     * Method called to associate a ChildDeliverer object to this object
     * through the ChildDeliverer foreign key attribute.
     *
     * @param    ChildDeliverer $l ChildDeliverer
     * @return   \Gekosale\Plugin\File\Model\ORM\File The current object (for fluent API support)
     */
    public function addDeliverer(ChildDeliverer $l)
    {
        if ($this->collDeliverers === null) {
            $this->initDeliverers();
            $this->collDeliverersPartial = true;
        }

        if (!in_array($l, $this->collDeliverers->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddDeliverer($l);
        }

        return $this;
    }

    /**
     * @param Deliverer $deliverer The deliverer object to add.
     */
    protected function doAddDeliverer($deliverer)
    {
        $this->collDeliverers[]= $deliverer;
        $deliverer->setFile($this);
    }

    /**
     * @param  Deliverer $deliverer The deliverer object to remove.
     * @return ChildFile The current object (for fluent API support)
     */
    public function removeDeliverer($deliverer)
    {
        if ($this->getDeliverers()->contains($deliverer)) {
            $this->collDeliverers->remove($this->collDeliverers->search($deliverer));
            if (null === $this->deliverersScheduledForDeletion) {
                $this->deliverersScheduledForDeletion = clone $this->collDeliverers;
                $this->deliverersScheduledForDeletion->clear();
            }
            $this->deliverersScheduledForDeletion[]= $deliverer;
            $deliverer->setFile(null);
        }

        return $this;
    }

    /**
     * Clears out the collProducers collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addProducers()
     */
    public function clearProducers()
    {
        $this->collProducers = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collProducers collection loaded partially.
     */
    public function resetPartialProducers($v = true)
    {
        $this->collProducersPartial = $v;
    }

    /**
     * Initializes the collProducers collection.
     *
     * By default this just sets the collProducers collection to an empty array (like clearcollProducers());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProducers($overrideExisting = true)
    {
        if (null !== $this->collProducers && !$overrideExisting) {
            return;
        }
        $this->collProducers = new ObjectCollection();
        $this->collProducers->setModel('\Gekosale\Plugin\Producer\Model\ORM\Producer');
    }

    /**
     * Gets an array of ChildProducer objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildFile is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildProducer[] List of ChildProducer objects
     * @throws PropelException
     */
    public function getProducers($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collProducersPartial && !$this->isNew();
        if (null === $this->collProducers || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProducers) {
                // return empty collection
                $this->initProducers();
            } else {
                $collProducers = ProducerQuery::create(null, $criteria)
                    ->filterByFile($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collProducersPartial && count($collProducers)) {
                        $this->initProducers(false);

                        foreach ($collProducers as $obj) {
                            if (false == $this->collProducers->contains($obj)) {
                                $this->collProducers->append($obj);
                            }
                        }

                        $this->collProducersPartial = true;
                    }

                    reset($collProducers);

                    return $collProducers;
                }

                if ($partial && $this->collProducers) {
                    foreach ($this->collProducers as $obj) {
                        if ($obj->isNew()) {
                            $collProducers[] = $obj;
                        }
                    }
                }

                $this->collProducers = $collProducers;
                $this->collProducersPartial = false;
            }
        }

        return $this->collProducers;
    }

    /**
     * Sets a collection of Producer objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $producers A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildFile The current object (for fluent API support)
     */
    public function setProducers(Collection $producers, ConnectionInterface $con = null)
    {
        $producersToDelete = $this->getProducers(new Criteria(), $con)->diff($producers);

        
        $this->producersScheduledForDeletion = $producersToDelete;

        foreach ($producersToDelete as $producerRemoved) {
            $producerRemoved->setFile(null);
        }

        $this->collProducers = null;
        foreach ($producers as $producer) {
            $this->addProducer($producer);
        }

        $this->collProducers = $producers;
        $this->collProducersPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Producer objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Producer objects.
     * @throws PropelException
     */
    public function countProducers(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collProducersPartial && !$this->isNew();
        if (null === $this->collProducers || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProducers) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProducers());
            }

            $query = ProducerQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByFile($this)
                ->count($con);
        }

        return count($this->collProducers);
    }

    /**
     * Method called to associate a ChildProducer object to this object
     * through the ChildProducer foreign key attribute.
     *
     * @param    ChildProducer $l ChildProducer
     * @return   \Gekosale\Plugin\File\Model\ORM\File The current object (for fluent API support)
     */
    public function addProducer(ChildProducer $l)
    {
        if ($this->collProducers === null) {
            $this->initProducers();
            $this->collProducersPartial = true;
        }

        if (!in_array($l, $this->collProducers->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProducer($l);
        }

        return $this;
    }

    /**
     * @param Producer $producer The producer object to add.
     */
    protected function doAddProducer($producer)
    {
        $this->collProducers[]= $producer;
        $producer->setFile($this);
    }

    /**
     * @param  Producer $producer The producer object to remove.
     * @return ChildFile The current object (for fluent API support)
     */
    public function removeProducer($producer)
    {
        if ($this->getProducers()->contains($producer)) {
            $this->collProducers->remove($this->collProducers->search($producer));
            if (null === $this->producersScheduledForDeletion) {
                $this->producersScheduledForDeletion = clone $this->collProducers;
                $this->producersScheduledForDeletion->clear();
            }
            $this->producersScheduledForDeletion[]= $producer;
            $producer->setFile(null);
        }

        return $this;
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
     * If this ChildFile is new, it will return
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
                $collProductAttributes = ProductAttributeQuery::create(null, $criteria)
                    ->filterByFile($this)
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
     * @return   ChildFile The current object (for fluent API support)
     */
    public function setProductAttributes(Collection $productAttributes, ConnectionInterface $con = null)
    {
        $productAttributesToDelete = $this->getProductAttributes(new Criteria(), $con)->diff($productAttributes);

        
        $this->productAttributesScheduledForDeletion = $productAttributesToDelete;

        foreach ($productAttributesToDelete as $productAttributeRemoved) {
            $productAttributeRemoved->setFile(null);
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

            $query = ProductAttributeQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByFile($this)
                ->count($con);
        }

        return count($this->collProductAttributes);
    }

    /**
     * Method called to associate a ChildProductAttribute object to this object
     * through the ChildProductAttribute foreign key attribute.
     *
     * @param    ChildProductAttribute $l ChildProductAttribute
     * @return   \Gekosale\Plugin\File\Model\ORM\File The current object (for fluent API support)
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
        $productAttribute->setFile($this);
    }

    /**
     * @param  ProductAttribute $productAttribute The productAttribute object to remove.
     * @return ChildFile The current object (for fluent API support)
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
            $productAttribute->setFile(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this File is new, it will return
     * an empty collection; or if this File has previously
     * been saved, it will retrieve related ProductAttributes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in File.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProductAttribute[] List of ChildProductAttribute objects
     */
    public function getProductAttributesJoinAttributeGroupName($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ProductAttributeQuery::create(null, $criteria);
        $query->joinWith('AttributeGroupName', $joinBehavior);

        return $this->getProductAttributes($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this File is new, it will return
     * an empty collection; or if this File has previously
     * been saved, it will retrieve related ProductAttributes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in File.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProductAttribute[] List of ChildProductAttribute objects
     */
    public function getProductAttributesJoinAvailability($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ProductAttributeQuery::create(null, $criteria);
        $query->joinWith('Availability', $joinBehavior);

        return $this->getProductAttributes($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this File is new, it will return
     * an empty collection; or if this File has previously
     * been saved, it will retrieve related ProductAttributes from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in File.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProductAttribute[] List of ChildProductAttribute objects
     */
    public function getProductAttributesJoinProduct($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ProductAttributeQuery::create(null, $criteria);
        $query->joinWith('Product', $joinBehavior);

        return $this->getProductAttributes($query, $con);
    }

    /**
     * Clears out the collProductFiles collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addProductFiles()
     */
    public function clearProductFiles()
    {
        $this->collProductFiles = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collProductFiles collection loaded partially.
     */
    public function resetPartialProductFiles($v = true)
    {
        $this->collProductFilesPartial = $v;
    }

    /**
     * Initializes the collProductFiles collection.
     *
     * By default this just sets the collProductFiles collection to an empty array (like clearcollProductFiles());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductFiles($overrideExisting = true)
    {
        if (null !== $this->collProductFiles && !$overrideExisting) {
            return;
        }
        $this->collProductFiles = new ObjectCollection();
        $this->collProductFiles->setModel('\Gekosale\Plugin\Product\Model\ORM\ProductFile');
    }

    /**
     * Gets an array of ChildProductFile objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildFile is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildProductFile[] List of ChildProductFile objects
     * @throws PropelException
     */
    public function getProductFiles($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collProductFilesPartial && !$this->isNew();
        if (null === $this->collProductFiles || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductFiles) {
                // return empty collection
                $this->initProductFiles();
            } else {
                $collProductFiles = ProductFileQuery::create(null, $criteria)
                    ->filterByFile($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collProductFilesPartial && count($collProductFiles)) {
                        $this->initProductFiles(false);

                        foreach ($collProductFiles as $obj) {
                            if (false == $this->collProductFiles->contains($obj)) {
                                $this->collProductFiles->append($obj);
                            }
                        }

                        $this->collProductFilesPartial = true;
                    }

                    reset($collProductFiles);

                    return $collProductFiles;
                }

                if ($partial && $this->collProductFiles) {
                    foreach ($this->collProductFiles as $obj) {
                        if ($obj->isNew()) {
                            $collProductFiles[] = $obj;
                        }
                    }
                }

                $this->collProductFiles = $collProductFiles;
                $this->collProductFilesPartial = false;
            }
        }

        return $this->collProductFiles;
    }

    /**
     * Sets a collection of ProductFile objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $productFiles A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildFile The current object (for fluent API support)
     */
    public function setProductFiles(Collection $productFiles, ConnectionInterface $con = null)
    {
        $productFilesToDelete = $this->getProductFiles(new Criteria(), $con)->diff($productFiles);

        
        $this->productFilesScheduledForDeletion = $productFilesToDelete;

        foreach ($productFilesToDelete as $productFileRemoved) {
            $productFileRemoved->setFile(null);
        }

        $this->collProductFiles = null;
        foreach ($productFiles as $productFile) {
            $this->addProductFile($productFile);
        }

        $this->collProductFiles = $productFiles;
        $this->collProductFilesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProductFile objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ProductFile objects.
     * @throws PropelException
     */
    public function countProductFiles(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collProductFilesPartial && !$this->isNew();
        if (null === $this->collProductFiles || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductFiles) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProductFiles());
            }

            $query = ProductFileQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByFile($this)
                ->count($con);
        }

        return count($this->collProductFiles);
    }

    /**
     * Method called to associate a ChildProductFile object to this object
     * through the ChildProductFile foreign key attribute.
     *
     * @param    ChildProductFile $l ChildProductFile
     * @return   \Gekosale\Plugin\File\Model\ORM\File The current object (for fluent API support)
     */
    public function addProductFile(ChildProductFile $l)
    {
        if ($this->collProductFiles === null) {
            $this->initProductFiles();
            $this->collProductFilesPartial = true;
        }

        if (!in_array($l, $this->collProductFiles->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductFile($l);
        }

        return $this;
    }

    /**
     * @param ProductFile $productFile The productFile object to add.
     */
    protected function doAddProductFile($productFile)
    {
        $this->collProductFiles[]= $productFile;
        $productFile->setFile($this);
    }

    /**
     * @param  ProductFile $productFile The productFile object to remove.
     * @return ChildFile The current object (for fluent API support)
     */
    public function removeProductFile($productFile)
    {
        if ($this->getProductFiles()->contains($productFile)) {
            $this->collProductFiles->remove($this->collProductFiles->search($productFile));
            if (null === $this->productFilesScheduledForDeletion) {
                $this->productFilesScheduledForDeletion = clone $this->collProductFiles;
                $this->productFilesScheduledForDeletion->clear();
            }
            $this->productFilesScheduledForDeletion[]= clone $productFile;
            $productFile->setFile(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this File is new, it will return
     * an empty collection; or if this File has previously
     * been saved, it will retrieve related ProductFiles from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in File.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProductFile[] List of ChildProductFile objects
     */
    public function getProductFilesJoinProduct($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ProductFileQuery::create(null, $criteria);
        $query->joinWith('Product', $joinBehavior);

        return $this->getProductFiles($query, $con);
    }

    /**
     * Clears out the collProductPhotos collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addProductPhotos()
     */
    public function clearProductPhotos()
    {
        $this->collProductPhotos = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collProductPhotos collection loaded partially.
     */
    public function resetPartialProductPhotos($v = true)
    {
        $this->collProductPhotosPartial = $v;
    }

    /**
     * Initializes the collProductPhotos collection.
     *
     * By default this just sets the collProductPhotos collection to an empty array (like clearcollProductPhotos());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductPhotos($overrideExisting = true)
    {
        if (null !== $this->collProductPhotos && !$overrideExisting) {
            return;
        }
        $this->collProductPhotos = new ObjectCollection();
        $this->collProductPhotos->setModel('\Gekosale\Plugin\Product\Model\ORM\ProductPhoto');
    }

    /**
     * Gets an array of ChildProductPhoto objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildFile is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildProductPhoto[] List of ChildProductPhoto objects
     * @throws PropelException
     */
    public function getProductPhotos($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collProductPhotosPartial && !$this->isNew();
        if (null === $this->collProductPhotos || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductPhotos) {
                // return empty collection
                $this->initProductPhotos();
            } else {
                $collProductPhotos = ProductPhotoQuery::create(null, $criteria)
                    ->filterByFile($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collProductPhotosPartial && count($collProductPhotos)) {
                        $this->initProductPhotos(false);

                        foreach ($collProductPhotos as $obj) {
                            if (false == $this->collProductPhotos->contains($obj)) {
                                $this->collProductPhotos->append($obj);
                            }
                        }

                        $this->collProductPhotosPartial = true;
                    }

                    reset($collProductPhotos);

                    return $collProductPhotos;
                }

                if ($partial && $this->collProductPhotos) {
                    foreach ($this->collProductPhotos as $obj) {
                        if ($obj->isNew()) {
                            $collProductPhotos[] = $obj;
                        }
                    }
                }

                $this->collProductPhotos = $collProductPhotos;
                $this->collProductPhotosPartial = false;
            }
        }

        return $this->collProductPhotos;
    }

    /**
     * Sets a collection of ProductPhoto objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $productPhotos A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildFile The current object (for fluent API support)
     */
    public function setProductPhotos(Collection $productPhotos, ConnectionInterface $con = null)
    {
        $productPhotosToDelete = $this->getProductPhotos(new Criteria(), $con)->diff($productPhotos);

        
        $this->productPhotosScheduledForDeletion = $productPhotosToDelete;

        foreach ($productPhotosToDelete as $productPhotoRemoved) {
            $productPhotoRemoved->setFile(null);
        }

        $this->collProductPhotos = null;
        foreach ($productPhotos as $productPhoto) {
            $this->addProductPhoto($productPhoto);
        }

        $this->collProductPhotos = $productPhotos;
        $this->collProductPhotosPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProductPhoto objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ProductPhoto objects.
     * @throws PropelException
     */
    public function countProductPhotos(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collProductPhotosPartial && !$this->isNew();
        if (null === $this->collProductPhotos || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductPhotos) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProductPhotos());
            }

            $query = ProductPhotoQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByFile($this)
                ->count($con);
        }

        return count($this->collProductPhotos);
    }

    /**
     * Method called to associate a ChildProductPhoto object to this object
     * through the ChildProductPhoto foreign key attribute.
     *
     * @param    ChildProductPhoto $l ChildProductPhoto
     * @return   \Gekosale\Plugin\File\Model\ORM\File The current object (for fluent API support)
     */
    public function addProductPhoto(ChildProductPhoto $l)
    {
        if ($this->collProductPhotos === null) {
            $this->initProductPhotos();
            $this->collProductPhotosPartial = true;
        }

        if (!in_array($l, $this->collProductPhotos->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductPhoto($l);
        }

        return $this;
    }

    /**
     * @param ProductPhoto $productPhoto The productPhoto object to add.
     */
    protected function doAddProductPhoto($productPhoto)
    {
        $this->collProductPhotos[]= $productPhoto;
        $productPhoto->setFile($this);
    }

    /**
     * @param  ProductPhoto $productPhoto The productPhoto object to remove.
     * @return ChildFile The current object (for fluent API support)
     */
    public function removeProductPhoto($productPhoto)
    {
        if ($this->getProductPhotos()->contains($productPhoto)) {
            $this->collProductPhotos->remove($this->collProductPhotos->search($productPhoto));
            if (null === $this->productPhotosScheduledForDeletion) {
                $this->productPhotosScheduledForDeletion = clone $this->collProductPhotos;
                $this->productPhotosScheduledForDeletion->clear();
            }
            $this->productPhotosScheduledForDeletion[]= $productPhoto;
            $productPhoto->setFile(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this File is new, it will return
     * an empty collection; or if this File has previously
     * been saved, it will retrieve related ProductPhotos from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in File.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProductPhoto[] List of ChildProductPhoto objects
     */
    public function getProductPhotosJoinProduct($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ProductPhotoQuery::create(null, $criteria);
        $query->joinWith('Product', $joinBehavior);

        return $this->getProductPhotos($query, $con);
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
     * If this ChildFile is new, it will return
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
                $collUserDatas = UserDataQuery::create(null, $criteria)
                    ->filterByFile($this)
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
     * @return   ChildFile The current object (for fluent API support)
     */
    public function setUserDatas(Collection $userDatas, ConnectionInterface $con = null)
    {
        $userDatasToDelete = $this->getUserDatas(new Criteria(), $con)->diff($userDatas);

        
        $this->userDatasScheduledForDeletion = $userDatasToDelete;

        foreach ($userDatasToDelete as $userDataRemoved) {
            $userDataRemoved->setFile(null);
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

            $query = UserDataQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByFile($this)
                ->count($con);
        }

        return count($this->collUserDatas);
    }

    /**
     * Method called to associate a ChildUserData object to this object
     * through the ChildUserData foreign key attribute.
     *
     * @param    ChildUserData $l ChildUserData
     * @return   \Gekosale\Plugin\File\Model\ORM\File The current object (for fluent API support)
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
        $userData->setFile($this);
    }

    /**
     * @param  UserData $userData The userData object to remove.
     * @return ChildFile The current object (for fluent API support)
     */
    public function removeUserData($userData)
    {
        if ($this->getUserDatas()->contains($userData)) {
            $this->collUserDatas->remove($this->collUserDatas->search($userData));
            if (null === $this->userDatasScheduledForDeletion) {
                $this->userDatasScheduledForDeletion = clone $this->collUserDatas;
                $this->userDatasScheduledForDeletion->clear();
            }
            $this->userDatasScheduledForDeletion[]= $userData;
            $userData->setFile(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this File is new, it will return
     * an empty collection; or if this File has previously
     * been saved, it will retrieve related UserDatas from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in File.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildUserData[] List of ChildUserData objects
     */
    public function getUserDatasJoinUser($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = UserDataQuery::create(null, $criteria);
        $query->joinWith('User', $joinBehavior);

        return $this->getUserDatas($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->name = null;
        $this->file_type_id = null;
        $this->file_extension_id = null;
        $this->is_visible = null;
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
            if ($this->collBlogPhotos) {
                foreach ($this->collBlogPhotos as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCategories) {
                foreach ($this->collCategories as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCompanies) {
                foreach ($this->collCompanies as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collDeliverers) {
                foreach ($this->collDeliverers as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProducers) {
                foreach ($this->collProducers as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductAttributes) {
                foreach ($this->collProductAttributes as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductFiles) {
                foreach ($this->collProductFiles as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductPhotos) {
                foreach ($this->collProductPhotos as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collUserDatas) {
                foreach ($this->collUserDatas as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collBlogPhotos = null;
        $this->collCategories = null;
        $this->collCompanies = null;
        $this->collDeliverers = null;
        $this->collProducers = null;
        $this->collProductAttributes = null;
        $this->collProductFiles = null;
        $this->collProductPhotos = null;
        $this->collUserDatas = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(FileTableMap::DEFAULT_STRING_FORMAT);
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
