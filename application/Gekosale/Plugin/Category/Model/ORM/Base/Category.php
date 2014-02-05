<?php

namespace Gekosale\Plugin\Category\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Attribute\Model\ORM\CategoryAttributeProductQuery;
use Gekosale\Plugin\Attribute\Model\ORM\CategoryAttributeProduct as ChildCategoryAttributeProduct;
use Gekosale\Plugin\Attribute\Model\ORM\Base\CategoryAttributeProduct;
use Gekosale\Plugin\Category\Model\ORM\Category as ChildCategory;
use Gekosale\Plugin\Category\Model\ORM\CategoryPath as ChildCategoryPath;
use Gekosale\Plugin\Category\Model\ORM\CategoryPathQuery as ChildCategoryPathQuery;
use Gekosale\Plugin\Category\Model\ORM\CategoryQuery as ChildCategoryQuery;
use Gekosale\Plugin\Category\Model\ORM\CategoryShop as ChildCategoryShop;
use Gekosale\Plugin\Category\Model\ORM\CategoryShopQuery as ChildCategoryShopQuery;
use Gekosale\Plugin\Category\Model\ORM\Map\CategoryTableMap;
use Gekosale\Plugin\File\Model\ORM\File as ChildFile;
use Gekosale\Plugin\File\Model\ORM\FileQuery;
use Gekosale\Plugin\Product\Model\ORM\ProductCategory as ChildProductCategory;
use Gekosale\Plugin\Product\Model\ORM\ProductCategoryQuery;
use Gekosale\Plugin\Product\Model\ORM\Base\ProductCategory;
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

abstract class Category implements ActiveRecordInterface 
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Gekosale\\Plugin\\Category\\Model\\ORM\\Map\\CategoryTableMap';


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
     * The value for the photo_id field.
     * @var        int
     */
    protected $photo_id;

    /**
     * The value for the hierarchy field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $hierarchy;

    /**
     * The value for the category_id field.
     * @var        int
     */
    protected $category_id;

    /**
     * The value for the is_enabled field.
     * Note: this column has a database default value of: 1
     * @var        int
     */
    protected $is_enabled;

    /**
     * @var        Category
     */
    protected $aCategoryRelatedByCategoryId;

    /**
     * @var        File
     */
    protected $aFile;

    /**
     * @var        ObjectCollection|ChildCategory[] Collection to store aggregation of ChildCategory objects.
     */
    protected $collCategoriesRelatedById;
    protected $collCategoriesRelatedByIdPartial;

    /**
     * @var        ObjectCollection|ChildCategoryAttributeProduct[] Collection to store aggregation of ChildCategoryAttributeProduct objects.
     */
    protected $collCategoryAttributeProducts;
    protected $collCategoryAttributeProductsPartial;

    /**
     * @var        ObjectCollection|ChildCategoryPath[] Collection to store aggregation of ChildCategoryPath objects.
     */
    protected $collCategoryPaths;
    protected $collCategoryPathsPartial;

    /**
     * @var        ObjectCollection|ChildProductCategory[] Collection to store aggregation of ChildProductCategory objects.
     */
    protected $collProductCategories;
    protected $collProductCategoriesPartial;

    /**
     * @var        ObjectCollection|ChildCategoryShop[] Collection to store aggregation of ChildCategoryShop objects.
     */
    protected $collCategoryShops;
    protected $collCategoryShopsPartial;

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
    protected $categoriesRelatedByIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $categoryAttributeProductsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $categoryPathsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $productCategoriesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $categoryShopsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->hierarchy = 0;
        $this->is_enabled = 1;
    }

    /**
     * Initializes internal state of Gekosale\Plugin\Category\Model\ORM\Base\Category object.
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
     * Compares this with another <code>Category</code> instance.  If
     * <code>obj</code> is an instance of <code>Category</code>, delegates to
     * <code>equals(Category)</code>.  Otherwise, returns <code>false</code>.
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
     * @return Category The current object, for fluid interface
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
     * @return Category The current object, for fluid interface
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
     * Get the [photo_id] column value.
     * 
     * @return   int
     */
    public function getPhotoId()
    {

        return $this->photo_id;
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
     * Get the [category_id] column value.
     * 
     * @return   int
     */
    public function getCategoryId()
    {

        return $this->category_id;
    }

    /**
     * Get the [is_enabled] column value.
     * 
     * @return   int
     */
    public function getIsEnabled()
    {

        return $this->is_enabled;
    }

    /**
     * Set the value of [id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Category\Model\ORM\Category The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[CategoryTableMap::COL_ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [photo_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Category\Model\ORM\Category The current object (for fluent API support)
     */
    public function setPhotoId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->photo_id !== $v) {
            $this->photo_id = $v;
            $this->modifiedColumns[CategoryTableMap::COL_PHOTO_ID] = true;
        }

        if ($this->aFile !== null && $this->aFile->getId() !== $v) {
            $this->aFile = null;
        }


        return $this;
    } // setPhotoId()

    /**
     * Set the value of [hierarchy] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Category\Model\ORM\Category The current object (for fluent API support)
     */
    public function setHierarchy($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->hierarchy !== $v) {
            $this->hierarchy = $v;
            $this->modifiedColumns[CategoryTableMap::COL_HIERARCHY] = true;
        }


        return $this;
    } // setHierarchy()

    /**
     * Set the value of [category_id] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Category\Model\ORM\Category The current object (for fluent API support)
     */
    public function setCategoryId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->category_id !== $v) {
            $this->category_id = $v;
            $this->modifiedColumns[CategoryTableMap::COL_CATEGORY_ID] = true;
        }

        if ($this->aCategoryRelatedByCategoryId !== null && $this->aCategoryRelatedByCategoryId->getId() !== $v) {
            $this->aCategoryRelatedByCategoryId = null;
        }


        return $this;
    } // setCategoryId()

    /**
     * Set the value of [is_enabled] column.
     * 
     * @param      int $v new value
     * @return   \Gekosale\Plugin\Category\Model\ORM\Category The current object (for fluent API support)
     */
    public function setIsEnabled($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->is_enabled !== $v) {
            $this->is_enabled = $v;
            $this->modifiedColumns[CategoryTableMap::COL_IS_ENABLED] = true;
        }


        return $this;
    } // setIsEnabled()

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

            if ($this->is_enabled !== 1) {
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


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : CategoryTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : CategoryTableMap::translateFieldName('PhotoId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->photo_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : CategoryTableMap::translateFieldName('Hierarchy', TableMap::TYPE_PHPNAME, $indexType)];
            $this->hierarchy = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : CategoryTableMap::translateFieldName('CategoryId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->category_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : CategoryTableMap::translateFieldName('IsEnabled', TableMap::TYPE_PHPNAME, $indexType)];
            $this->is_enabled = (null !== $col) ? (int) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 5; // 5 = CategoryTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Gekosale\Plugin\Category\Model\ORM\Category object", 0, $e);
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
        if ($this->aFile !== null && $this->photo_id !== $this->aFile->getId()) {
            $this->aFile = null;
        }
        if ($this->aCategoryRelatedByCategoryId !== null && $this->category_id !== $this->aCategoryRelatedByCategoryId->getId()) {
            $this->aCategoryRelatedByCategoryId = null;
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
            $con = Propel::getServiceContainer()->getReadConnection(CategoryTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildCategoryQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCategoryRelatedByCategoryId = null;
            $this->aFile = null;
            $this->collCategoriesRelatedById = null;

            $this->collCategoryAttributeProducts = null;

            $this->collCategoryPaths = null;

            $this->collProductCategories = null;

            $this->collCategoryShops = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Category::setDeleted()
     * @see Category::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(CategoryTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildCategoryQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(CategoryTableMap::DATABASE_NAME);
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
                CategoryTableMap::addInstanceToPool($this);
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

            if ($this->aCategoryRelatedByCategoryId !== null) {
                if ($this->aCategoryRelatedByCategoryId->isModified() || $this->aCategoryRelatedByCategoryId->isNew()) {
                    $affectedRows += $this->aCategoryRelatedByCategoryId->save($con);
                }
                $this->setCategoryRelatedByCategoryId($this->aCategoryRelatedByCategoryId);
            }

            if ($this->aFile !== null) {
                if ($this->aFile->isModified() || $this->aFile->isNew()) {
                    $affectedRows += $this->aFile->save($con);
                }
                $this->setFile($this->aFile);
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

            if ($this->categoriesRelatedByIdScheduledForDeletion !== null) {
                if (!$this->categoriesRelatedByIdScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Category\Model\ORM\CategoryQuery::create()
                        ->filterByPrimaryKeys($this->categoriesRelatedByIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->categoriesRelatedByIdScheduledForDeletion = null;
                }
            }

                if ($this->collCategoriesRelatedById !== null) {
            foreach ($this->collCategoriesRelatedById as $referrerFK) {
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

            if ($this->categoryPathsScheduledForDeletion !== null) {
                if (!$this->categoryPathsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Category\Model\ORM\CategoryPathQuery::create()
                        ->filterByPrimaryKeys($this->categoryPathsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->categoryPathsScheduledForDeletion = null;
                }
            }

                if ($this->collCategoryPaths !== null) {
            foreach ($this->collCategoryPaths as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->productCategoriesScheduledForDeletion !== null) {
                if (!$this->productCategoriesScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Product\Model\ORM\ProductCategoryQuery::create()
                        ->filterByPrimaryKeys($this->productCategoriesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->productCategoriesScheduledForDeletion = null;
                }
            }

                if ($this->collProductCategories !== null) {
            foreach ($this->collProductCategories as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->categoryShopsScheduledForDeletion !== null) {
                if (!$this->categoryShopsScheduledForDeletion->isEmpty()) {
                    \Gekosale\Plugin\Category\Model\ORM\CategoryShopQuery::create()
                        ->filterByPrimaryKeys($this->categoryShopsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->categoryShopsScheduledForDeletion = null;
                }
            }

                if ($this->collCategoryShops !== null) {
            foreach ($this->collCategoryShops as $referrerFK) {
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

        $this->modifiedColumns[CategoryTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CategoryTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CategoryTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(CategoryTableMap::COL_PHOTO_ID)) {
            $modifiedColumns[':p' . $index++]  = 'PHOTO_ID';
        }
        if ($this->isColumnModified(CategoryTableMap::COL_HIERARCHY)) {
            $modifiedColumns[':p' . $index++]  = 'HIERARCHY';
        }
        if ($this->isColumnModified(CategoryTableMap::COL_CATEGORY_ID)) {
            $modifiedColumns[':p' . $index++]  = 'CATEGORY_ID';
        }
        if ($this->isColumnModified(CategoryTableMap::COL_IS_ENABLED)) {
            $modifiedColumns[':p' . $index++]  = 'IS_ENABLED';
        }

        $sql = sprintf(
            'INSERT INTO category (%s) VALUES (%s)',
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
                    case 'PHOTO_ID':                        
                        $stmt->bindValue($identifier, $this->photo_id, PDO::PARAM_INT);
                        break;
                    case 'HIERARCHY':                        
                        $stmt->bindValue($identifier, $this->hierarchy, PDO::PARAM_INT);
                        break;
                    case 'CATEGORY_ID':                        
                        $stmt->bindValue($identifier, $this->category_id, PDO::PARAM_INT);
                        break;
                    case 'IS_ENABLED':                        
                        $stmt->bindValue($identifier, $this->is_enabled, PDO::PARAM_INT);
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
        $pos = CategoryTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getPhotoId();
                break;
            case 2:
                return $this->getHierarchy();
                break;
            case 3:
                return $this->getCategoryId();
                break;
            case 4:
                return $this->getIsEnabled();
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
        if (isset($alreadyDumpedObjects['Category'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Category'][$this->getPrimaryKey()] = true;
        $keys = CategoryTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getPhotoId(),
            $keys[2] => $this->getHierarchy(),
            $keys[3] => $this->getCategoryId(),
            $keys[4] => $this->getIsEnabled(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }
        
        if ($includeForeignObjects) {
            if (null !== $this->aCategoryRelatedByCategoryId) {
                $result['CategoryRelatedByCategoryId'] = $this->aCategoryRelatedByCategoryId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aFile) {
                $result['File'] = $this->aFile->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collCategoriesRelatedById) {
                $result['CategoriesRelatedById'] = $this->collCategoriesRelatedById->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCategoryAttributeProducts) {
                $result['CategoryAttributeProducts'] = $this->collCategoryAttributeProducts->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCategoryPaths) {
                $result['CategoryPaths'] = $this->collCategoryPaths->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collProductCategories) {
                $result['ProductCategories'] = $this->collProductCategories->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collCategoryShops) {
                $result['CategoryShops'] = $this->collCategoryShops->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = CategoryTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

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
                $this->setPhotoId($value);
                break;
            case 2:
                $this->setHierarchy($value);
                break;
            case 3:
                $this->setCategoryId($value);
                break;
            case 4:
                $this->setIsEnabled($value);
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
        $keys = CategoryTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setPhotoId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setHierarchy($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setCategoryId($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setIsEnabled($arr[$keys[4]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CategoryTableMap::DATABASE_NAME);

        if ($this->isColumnModified(CategoryTableMap::COL_ID)) $criteria->add(CategoryTableMap::COL_ID, $this->id);
        if ($this->isColumnModified(CategoryTableMap::COL_PHOTO_ID)) $criteria->add(CategoryTableMap::COL_PHOTO_ID, $this->photo_id);
        if ($this->isColumnModified(CategoryTableMap::COL_HIERARCHY)) $criteria->add(CategoryTableMap::COL_HIERARCHY, $this->hierarchy);
        if ($this->isColumnModified(CategoryTableMap::COL_CATEGORY_ID)) $criteria->add(CategoryTableMap::COL_CATEGORY_ID, $this->category_id);
        if ($this->isColumnModified(CategoryTableMap::COL_IS_ENABLED)) $criteria->add(CategoryTableMap::COL_IS_ENABLED, $this->is_enabled);

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
        $criteria = new Criteria(CategoryTableMap::DATABASE_NAME);
        $criteria->add(CategoryTableMap::COL_ID, $this->id);

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
     * @param      object $copyObj An object of \Gekosale\Plugin\Category\Model\ORM\Category (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setPhotoId($this->getPhotoId());
        $copyObj->setHierarchy($this->getHierarchy());
        $copyObj->setCategoryId($this->getCategoryId());
        $copyObj->setIsEnabled($this->getIsEnabled());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getCategoriesRelatedById() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCategoryRelatedById($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCategoryAttributeProducts() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCategoryAttributeProduct($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCategoryPaths() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCategoryPath($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getProductCategories() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addProductCategory($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getCategoryShops() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCategoryShop($relObj->copy($deepCopy));
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
     * @return                 \Gekosale\Plugin\Category\Model\ORM\Category Clone of current object.
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
     * Declares an association between this object and a ChildCategory object.
     *
     * @param                  ChildCategory $v
     * @return                 \Gekosale\Plugin\Category\Model\ORM\Category The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCategoryRelatedByCategoryId(ChildCategory $v = null)
    {
        if ($v === null) {
            $this->setCategoryId(NULL);
        } else {
            $this->setCategoryId($v->getId());
        }

        $this->aCategoryRelatedByCategoryId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildCategory object, it will not be re-added.
        if ($v !== null) {
            $v->addCategoryRelatedById($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildCategory object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildCategory The associated ChildCategory object.
     * @throws PropelException
     */
    public function getCategoryRelatedByCategoryId(ConnectionInterface $con = null)
    {
        if ($this->aCategoryRelatedByCategoryId === null && ($this->category_id !== null)) {
            $this->aCategoryRelatedByCategoryId = ChildCategoryQuery::create()->findPk($this->category_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCategoryRelatedByCategoryId->addCategoriesRelatedById($this);
             */
        }

        return $this->aCategoryRelatedByCategoryId;
    }

    /**
     * Declares an association between this object and a ChildFile object.
     *
     * @param                  ChildFile $v
     * @return                 \Gekosale\Plugin\Category\Model\ORM\Category The current object (for fluent API support)
     * @throws PropelException
     */
    public function setFile(ChildFile $v = null)
    {
        if ($v === null) {
            $this->setPhotoId(NULL);
        } else {
            $this->setPhotoId($v->getId());
        }

        $this->aFile = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildFile object, it will not be re-added.
        if ($v !== null) {
            $v->addCategory($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildFile object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildFile The associated ChildFile object.
     * @throws PropelException
     */
    public function getFile(ConnectionInterface $con = null)
    {
        if ($this->aFile === null && ($this->photo_id !== null)) {
            $this->aFile = FileQuery::create()->findPk($this->photo_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aFile->addCategories($this);
             */
        }

        return $this->aFile;
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
        if ('CategoryRelatedById' == $relationName) {
            return $this->initCategoriesRelatedById();
        }
        if ('CategoryAttributeProduct' == $relationName) {
            return $this->initCategoryAttributeProducts();
        }
        if ('CategoryPath' == $relationName) {
            return $this->initCategoryPaths();
        }
        if ('ProductCategory' == $relationName) {
            return $this->initProductCategories();
        }
        if ('CategoryShop' == $relationName) {
            return $this->initCategoryShops();
        }
    }

    /**
     * Clears out the collCategoriesRelatedById collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCategoriesRelatedById()
     */
    public function clearCategoriesRelatedById()
    {
        $this->collCategoriesRelatedById = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCategoriesRelatedById collection loaded partially.
     */
    public function resetPartialCategoriesRelatedById($v = true)
    {
        $this->collCategoriesRelatedByIdPartial = $v;
    }

    /**
     * Initializes the collCategoriesRelatedById collection.
     *
     * By default this just sets the collCategoriesRelatedById collection to an empty array (like clearcollCategoriesRelatedById());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCategoriesRelatedById($overrideExisting = true)
    {
        if (null !== $this->collCategoriesRelatedById && !$overrideExisting) {
            return;
        }
        $this->collCategoriesRelatedById = new ObjectCollection();
        $this->collCategoriesRelatedById->setModel('\Gekosale\Plugin\Category\Model\ORM\Category');
    }

    /**
     * Gets an array of ChildCategory objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCategory is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildCategory[] List of ChildCategory objects
     * @throws PropelException
     */
    public function getCategoriesRelatedById($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCategoriesRelatedByIdPartial && !$this->isNew();
        if (null === $this->collCategoriesRelatedById || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCategoriesRelatedById) {
                // return empty collection
                $this->initCategoriesRelatedById();
            } else {
                $collCategoriesRelatedById = ChildCategoryQuery::create(null, $criteria)
                    ->filterByCategoryRelatedByCategoryId($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCategoriesRelatedByIdPartial && count($collCategoriesRelatedById)) {
                        $this->initCategoriesRelatedById(false);

                        foreach ($collCategoriesRelatedById as $obj) {
                            if (false == $this->collCategoriesRelatedById->contains($obj)) {
                                $this->collCategoriesRelatedById->append($obj);
                            }
                        }

                        $this->collCategoriesRelatedByIdPartial = true;
                    }

                    reset($collCategoriesRelatedById);

                    return $collCategoriesRelatedById;
                }

                if ($partial && $this->collCategoriesRelatedById) {
                    foreach ($this->collCategoriesRelatedById as $obj) {
                        if ($obj->isNew()) {
                            $collCategoriesRelatedById[] = $obj;
                        }
                    }
                }

                $this->collCategoriesRelatedById = $collCategoriesRelatedById;
                $this->collCategoriesRelatedByIdPartial = false;
            }
        }

        return $this->collCategoriesRelatedById;
    }

    /**
     * Sets a collection of CategoryRelatedById objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $categoriesRelatedById A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildCategory The current object (for fluent API support)
     */
    public function setCategoriesRelatedById(Collection $categoriesRelatedById, ConnectionInterface $con = null)
    {
        $categoriesRelatedByIdToDelete = $this->getCategoriesRelatedById(new Criteria(), $con)->diff($categoriesRelatedById);

        
        $this->categoriesRelatedByIdScheduledForDeletion = $categoriesRelatedByIdToDelete;

        foreach ($categoriesRelatedByIdToDelete as $categoryRelatedByIdRemoved) {
            $categoryRelatedByIdRemoved->setCategoryRelatedByCategoryId(null);
        }

        $this->collCategoriesRelatedById = null;
        foreach ($categoriesRelatedById as $categoryRelatedById) {
            $this->addCategoryRelatedById($categoryRelatedById);
        }

        $this->collCategoriesRelatedById = $categoriesRelatedById;
        $this->collCategoriesRelatedByIdPartial = false;

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
    public function countCategoriesRelatedById(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCategoriesRelatedByIdPartial && !$this->isNew();
        if (null === $this->collCategoriesRelatedById || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCategoriesRelatedById) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCategoriesRelatedById());
            }

            $query = ChildCategoryQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCategoryRelatedByCategoryId($this)
                ->count($con);
        }

        return count($this->collCategoriesRelatedById);
    }

    /**
     * Method called to associate a ChildCategory object to this object
     * through the ChildCategory foreign key attribute.
     *
     * @param    ChildCategory $l ChildCategory
     * @return   \Gekosale\Plugin\Category\Model\ORM\Category The current object (for fluent API support)
     */
    public function addCategoryRelatedById(ChildCategory $l)
    {
        if ($this->collCategoriesRelatedById === null) {
            $this->initCategoriesRelatedById();
            $this->collCategoriesRelatedByIdPartial = true;
        }

        if (!in_array($l, $this->collCategoriesRelatedById->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCategoryRelatedById($l);
        }

        return $this;
    }

    /**
     * @param CategoryRelatedById $categoryRelatedById The categoryRelatedById object to add.
     */
    protected function doAddCategoryRelatedById($categoryRelatedById)
    {
        $this->collCategoriesRelatedById[]= $categoryRelatedById;
        $categoryRelatedById->setCategoryRelatedByCategoryId($this);
    }

    /**
     * @param  CategoryRelatedById $categoryRelatedById The categoryRelatedById object to remove.
     * @return ChildCategory The current object (for fluent API support)
     */
    public function removeCategoryRelatedById($categoryRelatedById)
    {
        if ($this->getCategoriesRelatedById()->contains($categoryRelatedById)) {
            $this->collCategoriesRelatedById->remove($this->collCategoriesRelatedById->search($categoryRelatedById));
            if (null === $this->categoriesRelatedByIdScheduledForDeletion) {
                $this->categoriesRelatedByIdScheduledForDeletion = clone $this->collCategoriesRelatedById;
                $this->categoriesRelatedByIdScheduledForDeletion->clear();
            }
            $this->categoriesRelatedByIdScheduledForDeletion[]= $categoryRelatedById;
            $categoryRelatedById->setCategoryRelatedByCategoryId(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Category is new, it will return
     * an empty collection; or if this Category has previously
     * been saved, it will retrieve related CategoriesRelatedById from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Category.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCategory[] List of ChildCategory objects
     */
    public function getCategoriesRelatedByIdJoinFile($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCategoryQuery::create(null, $criteria);
        $query->joinWith('File', $joinBehavior);

        return $this->getCategoriesRelatedById($query, $con);
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
     * If this ChildCategory is new, it will return
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
                $collCategoryAttributeProducts = CategoryAttributeProductQuery::create(null, $criteria)
                    ->filterByCategory($this)
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
     * @return   ChildCategory The current object (for fluent API support)
     */
    public function setCategoryAttributeProducts(Collection $categoryAttributeProducts, ConnectionInterface $con = null)
    {
        $categoryAttributeProductsToDelete = $this->getCategoryAttributeProducts(new Criteria(), $con)->diff($categoryAttributeProducts);

        
        $this->categoryAttributeProductsScheduledForDeletion = $categoryAttributeProductsToDelete;

        foreach ($categoryAttributeProductsToDelete as $categoryAttributeProductRemoved) {
            $categoryAttributeProductRemoved->setCategory(null);
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

            $query = CategoryAttributeProductQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCategory($this)
                ->count($con);
        }

        return count($this->collCategoryAttributeProducts);
    }

    /**
     * Method called to associate a ChildCategoryAttributeProduct object to this object
     * through the ChildCategoryAttributeProduct foreign key attribute.
     *
     * @param    ChildCategoryAttributeProduct $l ChildCategoryAttributeProduct
     * @return   \Gekosale\Plugin\Category\Model\ORM\Category The current object (for fluent API support)
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
        $categoryAttributeProduct->setCategory($this);
    }

    /**
     * @param  CategoryAttributeProduct $categoryAttributeProduct The categoryAttributeProduct object to remove.
     * @return ChildCategory The current object (for fluent API support)
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
            $categoryAttributeProduct->setCategory(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Category is new, it will return
     * an empty collection; or if this Category has previously
     * been saved, it will retrieve related CategoryAttributeProducts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Category.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCategoryAttributeProduct[] List of ChildCategoryAttributeProduct objects
     */
    public function getCategoryAttributeProductsJoinAttributeGroupName($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = CategoryAttributeProductQuery::create(null, $criteria);
        $query->joinWith('AttributeGroupName', $joinBehavior);

        return $this->getCategoryAttributeProducts($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Category is new, it will return
     * an empty collection; or if this Category has previously
     * been saved, it will retrieve related CategoryAttributeProducts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Category.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCategoryAttributeProduct[] List of ChildCategoryAttributeProduct objects
     */
    public function getCategoryAttributeProductsJoinAttributeProduct($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = CategoryAttributeProductQuery::create(null, $criteria);
        $query->joinWith('AttributeProduct', $joinBehavior);

        return $this->getCategoryAttributeProducts($query, $con);
    }

    /**
     * Clears out the collCategoryPaths collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCategoryPaths()
     */
    public function clearCategoryPaths()
    {
        $this->collCategoryPaths = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCategoryPaths collection loaded partially.
     */
    public function resetPartialCategoryPaths($v = true)
    {
        $this->collCategoryPathsPartial = $v;
    }

    /**
     * Initializes the collCategoryPaths collection.
     *
     * By default this just sets the collCategoryPaths collection to an empty array (like clearcollCategoryPaths());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCategoryPaths($overrideExisting = true)
    {
        if (null !== $this->collCategoryPaths && !$overrideExisting) {
            return;
        }
        $this->collCategoryPaths = new ObjectCollection();
        $this->collCategoryPaths->setModel('\Gekosale\Plugin\Category\Model\ORM\CategoryPath');
    }

    /**
     * Gets an array of ChildCategoryPath objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCategory is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildCategoryPath[] List of ChildCategoryPath objects
     * @throws PropelException
     */
    public function getCategoryPaths($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCategoryPathsPartial && !$this->isNew();
        if (null === $this->collCategoryPaths || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCategoryPaths) {
                // return empty collection
                $this->initCategoryPaths();
            } else {
                $collCategoryPaths = ChildCategoryPathQuery::create(null, $criteria)
                    ->filterByCategory($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCategoryPathsPartial && count($collCategoryPaths)) {
                        $this->initCategoryPaths(false);

                        foreach ($collCategoryPaths as $obj) {
                            if (false == $this->collCategoryPaths->contains($obj)) {
                                $this->collCategoryPaths->append($obj);
                            }
                        }

                        $this->collCategoryPathsPartial = true;
                    }

                    reset($collCategoryPaths);

                    return $collCategoryPaths;
                }

                if ($partial && $this->collCategoryPaths) {
                    foreach ($this->collCategoryPaths as $obj) {
                        if ($obj->isNew()) {
                            $collCategoryPaths[] = $obj;
                        }
                    }
                }

                $this->collCategoryPaths = $collCategoryPaths;
                $this->collCategoryPathsPartial = false;
            }
        }

        return $this->collCategoryPaths;
    }

    /**
     * Sets a collection of CategoryPath objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $categoryPaths A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildCategory The current object (for fluent API support)
     */
    public function setCategoryPaths(Collection $categoryPaths, ConnectionInterface $con = null)
    {
        $categoryPathsToDelete = $this->getCategoryPaths(new Criteria(), $con)->diff($categoryPaths);

        
        $this->categoryPathsScheduledForDeletion = $categoryPathsToDelete;

        foreach ($categoryPathsToDelete as $categoryPathRemoved) {
            $categoryPathRemoved->setCategory(null);
        }

        $this->collCategoryPaths = null;
        foreach ($categoryPaths as $categoryPath) {
            $this->addCategoryPath($categoryPath);
        }

        $this->collCategoryPaths = $categoryPaths;
        $this->collCategoryPathsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CategoryPath objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related CategoryPath objects.
     * @throws PropelException
     */
    public function countCategoryPaths(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCategoryPathsPartial && !$this->isNew();
        if (null === $this->collCategoryPaths || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCategoryPaths) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCategoryPaths());
            }

            $query = ChildCategoryPathQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCategory($this)
                ->count($con);
        }

        return count($this->collCategoryPaths);
    }

    /**
     * Method called to associate a ChildCategoryPath object to this object
     * through the ChildCategoryPath foreign key attribute.
     *
     * @param    ChildCategoryPath $l ChildCategoryPath
     * @return   \Gekosale\Plugin\Category\Model\ORM\Category The current object (for fluent API support)
     */
    public function addCategoryPath(ChildCategoryPath $l)
    {
        if ($this->collCategoryPaths === null) {
            $this->initCategoryPaths();
            $this->collCategoryPathsPartial = true;
        }

        if (!in_array($l, $this->collCategoryPaths->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCategoryPath($l);
        }

        return $this;
    }

    /**
     * @param CategoryPath $categoryPath The categoryPath object to add.
     */
    protected function doAddCategoryPath($categoryPath)
    {
        $this->collCategoryPaths[]= $categoryPath;
        $categoryPath->setCategory($this);
    }

    /**
     * @param  CategoryPath $categoryPath The categoryPath object to remove.
     * @return ChildCategory The current object (for fluent API support)
     */
    public function removeCategoryPath($categoryPath)
    {
        if ($this->getCategoryPaths()->contains($categoryPath)) {
            $this->collCategoryPaths->remove($this->collCategoryPaths->search($categoryPath));
            if (null === $this->categoryPathsScheduledForDeletion) {
                $this->categoryPathsScheduledForDeletion = clone $this->collCategoryPaths;
                $this->categoryPathsScheduledForDeletion->clear();
            }
            $this->categoryPathsScheduledForDeletion[]= clone $categoryPath;
            $categoryPath->setCategory(null);
        }

        return $this;
    }

    /**
     * Clears out the collProductCategories collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addProductCategories()
     */
    public function clearProductCategories()
    {
        $this->collProductCategories = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collProductCategories collection loaded partially.
     */
    public function resetPartialProductCategories($v = true)
    {
        $this->collProductCategoriesPartial = $v;
    }

    /**
     * Initializes the collProductCategories collection.
     *
     * By default this just sets the collProductCategories collection to an empty array (like clearcollProductCategories());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initProductCategories($overrideExisting = true)
    {
        if (null !== $this->collProductCategories && !$overrideExisting) {
            return;
        }
        $this->collProductCategories = new ObjectCollection();
        $this->collProductCategories->setModel('\Gekosale\Plugin\Product\Model\ORM\ProductCategory');
    }

    /**
     * Gets an array of ChildProductCategory objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCategory is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildProductCategory[] List of ChildProductCategory objects
     * @throws PropelException
     */
    public function getProductCategories($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collProductCategoriesPartial && !$this->isNew();
        if (null === $this->collProductCategories || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collProductCategories) {
                // return empty collection
                $this->initProductCategories();
            } else {
                $collProductCategories = ProductCategoryQuery::create(null, $criteria)
                    ->filterByCategory($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collProductCategoriesPartial && count($collProductCategories)) {
                        $this->initProductCategories(false);

                        foreach ($collProductCategories as $obj) {
                            if (false == $this->collProductCategories->contains($obj)) {
                                $this->collProductCategories->append($obj);
                            }
                        }

                        $this->collProductCategoriesPartial = true;
                    }

                    reset($collProductCategories);

                    return $collProductCategories;
                }

                if ($partial && $this->collProductCategories) {
                    foreach ($this->collProductCategories as $obj) {
                        if ($obj->isNew()) {
                            $collProductCategories[] = $obj;
                        }
                    }
                }

                $this->collProductCategories = $collProductCategories;
                $this->collProductCategoriesPartial = false;
            }
        }

        return $this->collProductCategories;
    }

    /**
     * Sets a collection of ProductCategory objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $productCategories A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildCategory The current object (for fluent API support)
     */
    public function setProductCategories(Collection $productCategories, ConnectionInterface $con = null)
    {
        $productCategoriesToDelete = $this->getProductCategories(new Criteria(), $con)->diff($productCategories);

        
        $this->productCategoriesScheduledForDeletion = $productCategoriesToDelete;

        foreach ($productCategoriesToDelete as $productCategoryRemoved) {
            $productCategoryRemoved->setCategory(null);
        }

        $this->collProductCategories = null;
        foreach ($productCategories as $productCategory) {
            $this->addProductCategory($productCategory);
        }

        $this->collProductCategories = $productCategories;
        $this->collProductCategoriesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ProductCategory objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ProductCategory objects.
     * @throws PropelException
     */
    public function countProductCategories(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collProductCategoriesPartial && !$this->isNew();
        if (null === $this->collProductCategories || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collProductCategories) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getProductCategories());
            }

            $query = ProductCategoryQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCategory($this)
                ->count($con);
        }

        return count($this->collProductCategories);
    }

    /**
     * Method called to associate a ChildProductCategory object to this object
     * through the ChildProductCategory foreign key attribute.
     *
     * @param    ChildProductCategory $l ChildProductCategory
     * @return   \Gekosale\Plugin\Category\Model\ORM\Category The current object (for fluent API support)
     */
    public function addProductCategory(ChildProductCategory $l)
    {
        if ($this->collProductCategories === null) {
            $this->initProductCategories();
            $this->collProductCategoriesPartial = true;
        }

        if (!in_array($l, $this->collProductCategories->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddProductCategory($l);
        }

        return $this;
    }

    /**
     * @param ProductCategory $productCategory The productCategory object to add.
     */
    protected function doAddProductCategory($productCategory)
    {
        $this->collProductCategories[]= $productCategory;
        $productCategory->setCategory($this);
    }

    /**
     * @param  ProductCategory $productCategory The productCategory object to remove.
     * @return ChildCategory The current object (for fluent API support)
     */
    public function removeProductCategory($productCategory)
    {
        if ($this->getProductCategories()->contains($productCategory)) {
            $this->collProductCategories->remove($this->collProductCategories->search($productCategory));
            if (null === $this->productCategoriesScheduledForDeletion) {
                $this->productCategoriesScheduledForDeletion = clone $this->collProductCategories;
                $this->productCategoriesScheduledForDeletion->clear();
            }
            $this->productCategoriesScheduledForDeletion[]= clone $productCategory;
            $productCategory->setCategory(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Category is new, it will return
     * an empty collection; or if this Category has previously
     * been saved, it will retrieve related ProductCategories from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Category.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildProductCategory[] List of ChildProductCategory objects
     */
    public function getProductCategoriesJoinProduct($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ProductCategoryQuery::create(null, $criteria);
        $query->joinWith('Product', $joinBehavior);

        return $this->getProductCategories($query, $con);
    }

    /**
     * Clears out the collCategoryShops collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addCategoryShops()
     */
    public function clearCategoryShops()
    {
        $this->collCategoryShops = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collCategoryShops collection loaded partially.
     */
    public function resetPartialCategoryShops($v = true)
    {
        $this->collCategoryShopsPartial = $v;
    }

    /**
     * Initializes the collCategoryShops collection.
     *
     * By default this just sets the collCategoryShops collection to an empty array (like clearcollCategoryShops());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCategoryShops($overrideExisting = true)
    {
        if (null !== $this->collCategoryShops && !$overrideExisting) {
            return;
        }
        $this->collCategoryShops = new ObjectCollection();
        $this->collCategoryShops->setModel('\Gekosale\Plugin\Category\Model\ORM\CategoryShop');
    }

    /**
     * Gets an array of ChildCategoryShop objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildCategory is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildCategoryShop[] List of ChildCategoryShop objects
     * @throws PropelException
     */
    public function getCategoryShops($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collCategoryShopsPartial && !$this->isNew();
        if (null === $this->collCategoryShops || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCategoryShops) {
                // return empty collection
                $this->initCategoryShops();
            } else {
                $collCategoryShops = ChildCategoryShopQuery::create(null, $criteria)
                    ->filterByCategory($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collCategoryShopsPartial && count($collCategoryShops)) {
                        $this->initCategoryShops(false);

                        foreach ($collCategoryShops as $obj) {
                            if (false == $this->collCategoryShops->contains($obj)) {
                                $this->collCategoryShops->append($obj);
                            }
                        }

                        $this->collCategoryShopsPartial = true;
                    }

                    reset($collCategoryShops);

                    return $collCategoryShops;
                }

                if ($partial && $this->collCategoryShops) {
                    foreach ($this->collCategoryShops as $obj) {
                        if ($obj->isNew()) {
                            $collCategoryShops[] = $obj;
                        }
                    }
                }

                $this->collCategoryShops = $collCategoryShops;
                $this->collCategoryShopsPartial = false;
            }
        }

        return $this->collCategoryShops;
    }

    /**
     * Sets a collection of CategoryShop objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $categoryShops A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildCategory The current object (for fluent API support)
     */
    public function setCategoryShops(Collection $categoryShops, ConnectionInterface $con = null)
    {
        $categoryShopsToDelete = $this->getCategoryShops(new Criteria(), $con)->diff($categoryShops);

        
        $this->categoryShopsScheduledForDeletion = $categoryShopsToDelete;

        foreach ($categoryShopsToDelete as $categoryShopRemoved) {
            $categoryShopRemoved->setCategory(null);
        }

        $this->collCategoryShops = null;
        foreach ($categoryShops as $categoryShop) {
            $this->addCategoryShop($categoryShop);
        }

        $this->collCategoryShops = $categoryShops;
        $this->collCategoryShopsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CategoryShop objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related CategoryShop objects.
     * @throws PropelException
     */
    public function countCategoryShops(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collCategoryShopsPartial && !$this->isNew();
        if (null === $this->collCategoryShops || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCategoryShops) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getCategoryShops());
            }

            $query = ChildCategoryShopQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCategory($this)
                ->count($con);
        }

        return count($this->collCategoryShops);
    }

    /**
     * Method called to associate a ChildCategoryShop object to this object
     * through the ChildCategoryShop foreign key attribute.
     *
     * @param    ChildCategoryShop $l ChildCategoryShop
     * @return   \Gekosale\Plugin\Category\Model\ORM\Category The current object (for fluent API support)
     */
    public function addCategoryShop(ChildCategoryShop $l)
    {
        if ($this->collCategoryShops === null) {
            $this->initCategoryShops();
            $this->collCategoryShopsPartial = true;
        }

        if (!in_array($l, $this->collCategoryShops->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCategoryShop($l);
        }

        return $this;
    }

    /**
     * @param CategoryShop $categoryShop The categoryShop object to add.
     */
    protected function doAddCategoryShop($categoryShop)
    {
        $this->collCategoryShops[]= $categoryShop;
        $categoryShop->setCategory($this);
    }

    /**
     * @param  CategoryShop $categoryShop The categoryShop object to remove.
     * @return ChildCategory The current object (for fluent API support)
     */
    public function removeCategoryShop($categoryShop)
    {
        if ($this->getCategoryShops()->contains($categoryShop)) {
            $this->collCategoryShops->remove($this->collCategoryShops->search($categoryShop));
            if (null === $this->categoryShopsScheduledForDeletion) {
                $this->categoryShopsScheduledForDeletion = clone $this->collCategoryShops;
                $this->categoryShopsScheduledForDeletion->clear();
            }
            $this->categoryShopsScheduledForDeletion[]= clone $categoryShop;
            $categoryShop->setCategory(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Category is new, it will return
     * an empty collection; or if this Category has previously
     * been saved, it will retrieve related CategoryShops from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Category.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildCategoryShop[] List of ChildCategoryShop objects
     */
    public function getCategoryShopsJoinShop($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildCategoryShopQuery::create(null, $criteria);
        $query->joinWith('Shop', $joinBehavior);

        return $this->getCategoryShops($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->photo_id = null;
        $this->hierarchy = null;
        $this->category_id = null;
        $this->is_enabled = null;
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
            if ($this->collCategoriesRelatedById) {
                foreach ($this->collCategoriesRelatedById as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCategoryAttributeProducts) {
                foreach ($this->collCategoryAttributeProducts as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCategoryPaths) {
                foreach ($this->collCategoryPaths as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collProductCategories) {
                foreach ($this->collProductCategories as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collCategoryShops) {
                foreach ($this->collCategoryShops as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collCategoriesRelatedById = null;
        $this->collCategoryAttributeProducts = null;
        $this->collCategoryPaths = null;
        $this->collProductCategories = null;
        $this->collCategoryShops = null;
        $this->aCategoryRelatedByCategoryId = null;
        $this->aFile = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CategoryTableMap::DEFAULT_STRING_FORMAT);
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
