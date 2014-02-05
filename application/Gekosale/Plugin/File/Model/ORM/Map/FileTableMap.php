<?php

namespace Gekosale\Plugin\File\Model\ORM\Map;

use Gekosale\Plugin\Attribute\Model\ORM\Map\ProductAttributeTableMap;
use Gekosale\Plugin\Blog\Model\ORM\Map\BlogPhotoTableMap;
use Gekosale\Plugin\Category\Model\ORM\Map\CategoryTableMap;
use Gekosale\Plugin\Company\Model\ORM\Map\CompanyTableMap;
use Gekosale\Plugin\Deliverer\Model\ORM\Map\DelivererTableMap;
use Gekosale\Plugin\File\Model\ORM\File;
use Gekosale\Plugin\File\Model\ORM\FileQuery;
use Gekosale\Plugin\Producer\Model\ORM\Map\ProducerTableMap;
use Gekosale\Plugin\Product\Model\ORM\Map\ProductFileTableMap;
use Gekosale\Plugin\Product\Model\ORM\Map\ProductPhotoTableMap;
use Gekosale\Plugin\User\Model\ORM\Map\UserDataTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;


/**
 * This class defines the structure of the 'file' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class FileTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.File.Model.ORM.Map.FileTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'file';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\File\\Model\\ORM\\File';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.File.Model.ORM.File';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 5;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 5;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'file.ID';

    /**
     * the column name for the NAME field
     */
    const COL_NAME = 'file.NAME';

    /**
     * the column name for the FILE_TYPE_ID field
     */
    const COL_FILE_TYPE_ID = 'file.FILE_TYPE_ID';

    /**
     * the column name for the FILE_EXTENSION_ID field
     */
    const COL_FILE_EXTENSION_ID = 'file.FILE_EXTENSION_ID';

    /**
     * the column name for the IS_VISIBLE field
     */
    const COL_IS_VISIBLE = 'file.IS_VISIBLE';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('Id', 'Name', 'FileTypeId', 'FileExtensionId', 'IsVisible', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'name', 'fileTypeId', 'fileExtensionId', 'isVisible', ),
        self::TYPE_COLNAME       => array(FileTableMap::COL_ID, FileTableMap::COL_NAME, FileTableMap::COL_FILE_TYPE_ID, FileTableMap::COL_FILE_EXTENSION_ID, FileTableMap::COL_IS_VISIBLE, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_NAME', 'COL_FILE_TYPE_ID', 'COL_FILE_EXTENSION_ID', 'COL_IS_VISIBLE', ),
        self::TYPE_FIELDNAME     => array('id', 'name', 'file_type_id', 'file_extension_id', 'is_visible', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Name' => 1, 'FileTypeId' => 2, 'FileExtensionId' => 3, 'IsVisible' => 4, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'name' => 1, 'fileTypeId' => 2, 'fileExtensionId' => 3, 'isVisible' => 4, ),
        self::TYPE_COLNAME       => array(FileTableMap::COL_ID => 0, FileTableMap::COL_NAME => 1, FileTableMap::COL_FILE_TYPE_ID => 2, FileTableMap::COL_FILE_EXTENSION_ID => 3, FileTableMap::COL_IS_VISIBLE => 4, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_NAME' => 1, 'COL_FILE_TYPE_ID' => 2, 'COL_FILE_EXTENSION_ID' => 3, 'COL_IS_VISIBLE' => 4, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'name' => 1, 'file_type_id' => 2, 'file_extension_id' => 3, 'is_visible' => 4, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, )
    );

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('file');
        $this->setPhpName('File');
        $this->setClassName('\\Gekosale\\Plugin\\File\\Model\\ORM\\File');
        $this->setPackage('Gekosale.Plugin.File.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addColumn('NAME', 'Name', 'VARCHAR', true, 128, null);
        $this->addColumn('FILE_TYPE_ID', 'FileTypeId', 'INTEGER', true, 10, null);
        $this->addColumn('FILE_EXTENSION_ID', 'FileExtensionId', 'INTEGER', true, 10, null);
        $this->addColumn('IS_VISIBLE', 'IsVisible', 'INTEGER', false, 10, 0);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('BlogPhoto', '\\Gekosale\\Plugin\\Blog\\Model\\ORM\\BlogPhoto', RelationMap::ONE_TO_MANY, array('id' => 'photo_id', ), 'CASCADE', null, 'BlogPhotos');
        $this->addRelation('Category', '\\Gekosale\\Plugin\\Category\\Model\\ORM\\Category', RelationMap::ONE_TO_MANY, array('id' => 'photo_id', ), 'SET NULL', null, 'Categories');
        $this->addRelation('Company', '\\Gekosale\\Plugin\\Company\\Model\\ORM\\Company', RelationMap::ONE_TO_MANY, array('id' => 'photo_id', ), 'SET NULL', null, 'Companies');
        $this->addRelation('Deliverer', '\\Gekosale\\Plugin\\Deliverer\\Model\\ORM\\Deliverer', RelationMap::ONE_TO_MANY, array('id' => 'photo_id', ), 'SET NULL', null, 'Deliverers');
        $this->addRelation('Producer', '\\Gekosale\\Plugin\\Producer\\Model\\ORM\\Producer', RelationMap::ONE_TO_MANY, array('id' => 'photo_id', ), 'SET NULL', null, 'Producers');
        $this->addRelation('ProductAttribute', '\\Gekosale\\Plugin\\Attribute\\Model\\ORM\\ProductAttribute', RelationMap::ONE_TO_MANY, array('id' => 'photo_id', ), 'SET NULL', null, 'ProductAttributes');
        $this->addRelation('ProductFile', '\\Gekosale\\Plugin\\Product\\Model\\ORM\\ProductFile', RelationMap::ONE_TO_MANY, array('id' => 'file_id', ), 'CASCADE', null, 'ProductFiles');
        $this->addRelation('ProductPhoto', '\\Gekosale\\Plugin\\Product\\Model\\ORM\\ProductPhoto', RelationMap::ONE_TO_MANY, array('id' => 'photo_id', ), 'CASCADE', null, 'ProductPhotos');
        $this->addRelation('UserData', '\\Gekosale\\Plugin\\User\\Model\\ORM\\UserData', RelationMap::ONE_TO_MANY, array('id' => 'photo_id', ), 'SET NULL', null, 'UserDatas');
    } // buildRelations()
    /**
     * Method to invalidate the instance pool of all tables related to file     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in ".$this->getClassNameFromBuilder($joinedTableTableMapBuilder)." instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
                BlogPhotoTableMap::clearInstancePool();
                CategoryTableMap::clearInstancePool();
                CompanyTableMap::clearInstancePool();
                DelivererTableMap::clearInstancePool();
                ProducerTableMap::clearInstancePool();
                ProductAttributeTableMap::clearInstancePool();
                ProductFileTableMap::clearInstancePool();
                ProductPhotoTableMap::clearInstancePool();
                UserDataTableMap::clearInstancePool();
            }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {

            return (int) $row[
                            $indexType == TableMap::TYPE_NUM
                            ? 0 + $offset
                            : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)
                        ];
    }
    
    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? FileTableMap::CLASS_DEFAULT : FileTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array  $row       row returned by DataFetcher->fetch().
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     * @return array (File object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = FileTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = FileTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + FileTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = FileTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            FileTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();
    
        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = FileTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = FileTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                FileTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @param string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(FileTableMap::COL_ID);
            $criteria->addSelectColumn(FileTableMap::COL_NAME);
            $criteria->addSelectColumn(FileTableMap::COL_FILE_TYPE_ID);
            $criteria->addSelectColumn(FileTableMap::COL_FILE_EXTENSION_ID);
            $criteria->addSelectColumn(FileTableMap::COL_IS_VISIBLE);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.NAME');
            $criteria->addSelectColumn($alias . '.FILE_TYPE_ID');
            $criteria->addSelectColumn($alias . '.FILE_EXTENSION_ID');
            $criteria->addSelectColumn($alias . '.IS_VISIBLE');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(FileTableMap::DATABASE_NAME)->getTable(FileTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(FileTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(FileTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new FileTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a File or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or File object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(FileTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\File\Model\ORM\File) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(FileTableMap::DATABASE_NAME);
            $criteria->add(FileTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = FileQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { FileTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { FileTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the file table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return FileQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a File or Criteria object.
     *
     * @param mixed               $criteria Criteria or File object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(FileTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from File object
        }

        if ($criteria->containsKey(FileTableMap::COL_ID) && $criteria->keyContainsValue(FileTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.FileTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = FileQuery::create()->mergeWith($criteria);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = $query->doInsert($con);
            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

} // FileTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
FileTableMap::buildTableMap();
