<?php

namespace Gekosale\Plugin\Category\Model\ORM\Map;

use Gekosale\Plugin\Attribute\Model\ORM\Map\CategoryAttributeProductTableMap;
use Gekosale\Plugin\Category\Model\ORM\Category;
use Gekosale\Plugin\Category\Model\ORM\CategoryQuery;
use Gekosale\Plugin\Product\Model\ORM\Map\ProductCategoryTableMap;
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
 * This class defines the structure of the 'category' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class CategoryTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.Category.Model.ORM.Map.CategoryTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'category';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\Category\\Model\\ORM\\Category';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.Category.Model.ORM.Category';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 7;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 7;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'category.ID';

    /**
     * the column name for the PHOTO_ID field
     */
    const COL_PHOTO_ID = 'category.PHOTO_ID';

    /**
     * the column name for the HIERARCHY field
     */
    const COL_HIERARCHY = 'category.HIERARCHY';

    /**
     * the column name for the CATEGORY_ID field
     */
    const COL_CATEGORY_ID = 'category.CATEGORY_ID';

    /**
     * the column name for the ENABLED field
     */
    const COL_ENABLED = 'category.ENABLED';

    /**
     * the column name for the CREATED_AT field
     */
    const COL_CREATED_AT = 'category.CREATED_AT';

    /**
     * the column name for the UPDATED_AT field
     */
    const COL_UPDATED_AT = 'category.UPDATED_AT';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    // i18n behavior
    
    /**
     * The default locale to use for translations.
     *
     * @var string
     */
    const DEFAULT_LOCALE = 'en_US';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('Id', 'PhotoId', 'Hierarchy', 'CategoryId', 'IsEnabled', 'CreatedAt', 'UpdatedAt', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'photoId', 'hierarchy', 'categoryId', 'isEnabled', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME       => array(CategoryTableMap::COL_ID, CategoryTableMap::COL_PHOTO_ID, CategoryTableMap::COL_HIERARCHY, CategoryTableMap::COL_CATEGORY_ID, CategoryTableMap::COL_ENABLED, CategoryTableMap::COL_CREATED_AT, CategoryTableMap::COL_UPDATED_AT, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_PHOTO_ID', 'COL_HIERARCHY', 'COL_CATEGORY_ID', 'COL_ENABLED', 'COL_CREATED_AT', 'COL_UPDATED_AT', ),
        self::TYPE_FIELDNAME     => array('id', 'photo_id', 'hierarchy', 'category_id', 'enabled', 'created_at', 'updated_at', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'PhotoId' => 1, 'Hierarchy' => 2, 'CategoryId' => 3, 'IsEnabled' => 4, 'CreatedAt' => 5, 'UpdatedAt' => 6, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'photoId' => 1, 'hierarchy' => 2, 'categoryId' => 3, 'isEnabled' => 4, 'createdAt' => 5, 'updatedAt' => 6, ),
        self::TYPE_COLNAME       => array(CategoryTableMap::COL_ID => 0, CategoryTableMap::COL_PHOTO_ID => 1, CategoryTableMap::COL_HIERARCHY => 2, CategoryTableMap::COL_CATEGORY_ID => 3, CategoryTableMap::COL_ENABLED => 4, CategoryTableMap::COL_CREATED_AT => 5, CategoryTableMap::COL_UPDATED_AT => 6, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_PHOTO_ID' => 1, 'COL_HIERARCHY' => 2, 'COL_CATEGORY_ID' => 3, 'COL_ENABLED' => 4, 'COL_CREATED_AT' => 5, 'COL_UPDATED_AT' => 6, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'photo_id' => 1, 'hierarchy' => 2, 'category_id' => 3, 'enabled' => 4, 'created_at' => 5, 'updated_at' => 6, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, )
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
        $this->setName('category');
        $this->setPhpName('Category');
        $this->setClassName('\\Gekosale\\Plugin\\Category\\Model\\ORM\\Category');
        $this->setPackage('Gekosale.Plugin.Category.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addForeignKey('PHOTO_ID', 'PhotoId', 'INTEGER', 'file', 'ID', false, 10, null);
        $this->addColumn('HIERARCHY', 'Hierarchy', 'TINYINT', false, 3, 0);
        $this->addForeignKey('CATEGORY_ID', 'CategoryId', 'INTEGER', 'category', 'ID', false, 10, null);
        $this->addColumn('ENABLED', 'IsEnabled', 'INTEGER', true, 10, 1);
        $this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CategoryRelatedByCategoryId', '\\Gekosale\\Plugin\\Category\\Model\\ORM\\Category', RelationMap::MANY_TO_ONE, array('category_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('File', '\\Gekosale\\Plugin\\File\\Model\\ORM\\File', RelationMap::MANY_TO_ONE, array('photo_id' => 'id', ), 'SET NULL', null);
        $this->addRelation('CategoryRelatedById', '\\Gekosale\\Plugin\\Category\\Model\\ORM\\Category', RelationMap::ONE_TO_MANY, array('id' => 'category_id', ), 'CASCADE', null, 'CategoriesRelatedById');
        $this->addRelation('CategoryAttributeProduct', '\\Gekosale\\Plugin\\Attribute\\Model\\ORM\\CategoryAttributeProduct', RelationMap::ONE_TO_MANY, array('id' => 'category_id', ), 'CASCADE', null, 'CategoryAttributeProducts');
        $this->addRelation('CategoryPath', '\\Gekosale\\Plugin\\Category\\Model\\ORM\\CategoryPath', RelationMap::ONE_TO_MANY, array('id' => 'category_id', ), 'CASCADE', 'CASCADE', 'CategoryPaths');
        $this->addRelation('ProductCategory', '\\Gekosale\\Plugin\\Product\\Model\\ORM\\ProductCategory', RelationMap::ONE_TO_MANY, array('id' => 'category_id', ), 'CASCADE', null, 'ProductCategories');
        $this->addRelation('CategoryShop', '\\Gekosale\\Plugin\\Category\\Model\\ORM\\CategoryShop', RelationMap::ONE_TO_MANY, array('id' => 'category_id', ), 'CASCADE', null, 'CategoryShops');
        $this->addRelation('CategoryI18n', '\\Gekosale\\Plugin\\Category\\Model\\ORM\\CategoryI18n', RelationMap::ONE_TO_MANY, array('id' => 'id', ), 'CASCADE', null, 'CategoryI18ns');
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'i18n' => array('i18n_table' => '%TABLE%_i18n', 'i18n_phpname' => '%PHPNAME%I18n', 'i18n_columns' => 'name, short_description, description, meta_title, meta_keyword, meta_description', 'locale_column' => 'locale', 'locale_length' => '5', 'default_locale' => '', 'locale_alias' => '', ),
            'timestampable' => array('create_column' => 'created_at', 'update_column' => 'updated_at', ),
        );
    } // getBehaviors()
    /**
     * Method to invalidate the instance pool of all tables related to category     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in ".$this->getClassNameFromBuilder($joinedTableTableMapBuilder)." instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
                CategoryTableMap::clearInstancePool();
                CategoryAttributeProductTableMap::clearInstancePool();
                CategoryPathTableMap::clearInstancePool();
                ProductCategoryTableMap::clearInstancePool();
                CategoryShopTableMap::clearInstancePool();
                CategoryI18nTableMap::clearInstancePool();
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
        return $withPrefix ? CategoryTableMap::CLASS_DEFAULT : CategoryTableMap::OM_CLASS;
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
     * @return array (Category object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = CategoryTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = CategoryTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + CategoryTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = CategoryTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            CategoryTableMap::addInstanceToPool($obj, $key);
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
            $key = CategoryTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = CategoryTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                CategoryTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(CategoryTableMap::COL_ID);
            $criteria->addSelectColumn(CategoryTableMap::COL_PHOTO_ID);
            $criteria->addSelectColumn(CategoryTableMap::COL_HIERARCHY);
            $criteria->addSelectColumn(CategoryTableMap::COL_CATEGORY_ID);
            $criteria->addSelectColumn(CategoryTableMap::COL_ENABLED);
            $criteria->addSelectColumn(CategoryTableMap::COL_CREATED_AT);
            $criteria->addSelectColumn(CategoryTableMap::COL_UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.PHOTO_ID');
            $criteria->addSelectColumn($alias . '.HIERARCHY');
            $criteria->addSelectColumn($alias . '.CATEGORY_ID');
            $criteria->addSelectColumn($alias . '.ENABLED');
            $criteria->addSelectColumn($alias . '.CREATED_AT');
            $criteria->addSelectColumn($alias . '.UPDATED_AT');
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
        return Propel::getServiceContainer()->getDatabaseMap(CategoryTableMap::DATABASE_NAME)->getTable(CategoryTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(CategoryTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(CategoryTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new CategoryTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a Category or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Category object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CategoryTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\Category\Model\ORM\Category) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(CategoryTableMap::DATABASE_NAME);
            $criteria->add(CategoryTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = CategoryQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { CategoryTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { CategoryTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the category table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return CategoryQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Category or Criteria object.
     *
     * @param mixed               $criteria Criteria or Category object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CategoryTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Category object
        }

        if ($criteria->containsKey(CategoryTableMap::COL_ID) && $criteria->keyContainsValue(CategoryTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CategoryTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = CategoryQuery::create()->mergeWith($criteria);

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

} // CategoryTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
CategoryTableMap::buildTableMap();
