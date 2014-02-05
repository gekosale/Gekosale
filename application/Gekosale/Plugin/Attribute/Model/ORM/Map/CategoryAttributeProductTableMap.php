<?php

namespace Gekosale\Plugin\Attribute\Model\ORM\Map;

use Gekosale\Plugin\Attribute\Model\ORM\CategoryAttributeProduct;
use Gekosale\Plugin\Attribute\Model\ORM\CategoryAttributeProductQuery;
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
 * This class defines the structure of the 'category_attribute_product' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class CategoryAttributeProductTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.Attribute.Model.ORM.Map.CategoryAttributeProductTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'category_attribute_product';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\Attribute\\Model\\ORM\\CategoryAttributeProduct';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.Attribute.Model.ORM.CategoryAttributeProduct';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 4;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 4;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'category_attribute_product.ID';

    /**
     * the column name for the CATEGORY_ID field
     */
    const COL_CATEGORY_ID = 'category_attribute_product.CATEGORY_ID';

    /**
     * the column name for the ATTRIBUTE_PRODUCT_ID field
     */
    const COL_ATTRIBUTE_PRODUCT_ID = 'category_attribute_product.ATTRIBUTE_PRODUCT_ID';

    /**
     * the column name for the ATTRIBUTE_GROUP_NAME_ID field
     */
    const COL_ATTRIBUTE_GROUP_NAME_ID = 'category_attribute_product.ATTRIBUTE_GROUP_NAME_ID';

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
        self::TYPE_PHPNAME       => array('Id', 'CategoryId', 'AttributeProductId', 'AttributeGroupNameId', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'categoryId', 'attributeProductId', 'attributeGroupNameId', ),
        self::TYPE_COLNAME       => array(CategoryAttributeProductTableMap::COL_ID, CategoryAttributeProductTableMap::COL_CATEGORY_ID, CategoryAttributeProductTableMap::COL_ATTRIBUTE_PRODUCT_ID, CategoryAttributeProductTableMap::COL_ATTRIBUTE_GROUP_NAME_ID, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_CATEGORY_ID', 'COL_ATTRIBUTE_PRODUCT_ID', 'COL_ATTRIBUTE_GROUP_NAME_ID', ),
        self::TYPE_FIELDNAME     => array('id', 'category_id', 'attribute_product_id', 'attribute_group_name_id', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'CategoryId' => 1, 'AttributeProductId' => 2, 'AttributeGroupNameId' => 3, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'categoryId' => 1, 'attributeProductId' => 2, 'attributeGroupNameId' => 3, ),
        self::TYPE_COLNAME       => array(CategoryAttributeProductTableMap::COL_ID => 0, CategoryAttributeProductTableMap::COL_CATEGORY_ID => 1, CategoryAttributeProductTableMap::COL_ATTRIBUTE_PRODUCT_ID => 2, CategoryAttributeProductTableMap::COL_ATTRIBUTE_GROUP_NAME_ID => 3, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_CATEGORY_ID' => 1, 'COL_ATTRIBUTE_PRODUCT_ID' => 2, 'COL_ATTRIBUTE_GROUP_NAME_ID' => 3, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'category_id' => 1, 'attribute_product_id' => 2, 'attribute_group_name_id' => 3, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, )
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
        $this->setName('category_attribute_product');
        $this->setPhpName('CategoryAttributeProduct');
        $this->setClassName('\\Gekosale\\Plugin\\Attribute\\Model\\ORM\\CategoryAttributeProduct');
        $this->setPackage('Gekosale.Plugin.Attribute.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addForeignKey('CATEGORY_ID', 'CategoryId', 'INTEGER', 'category', 'ID', true, 10, null);
        $this->addForeignKey('ATTRIBUTE_PRODUCT_ID', 'AttributeProductId', 'INTEGER', 'attribute_product', 'ID', true, 10, null);
        $this->addForeignKey('ATTRIBUTE_GROUP_NAME_ID', 'AttributeGroupNameId', 'INTEGER', 'attribute_group_name', 'ID', true, 10, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('AttributeGroupName', '\\Gekosale\\Plugin\\Attribute\\Model\\ORM\\AttributeGroupName', RelationMap::MANY_TO_ONE, array('attribute_group_name_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('AttributeProduct', '\\Gekosale\\Plugin\\Attribute\\Model\\ORM\\AttributeProduct', RelationMap::MANY_TO_ONE, array('attribute_product_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Category', '\\Gekosale\\Plugin\\Category\\Model\\ORM\\Category', RelationMap::MANY_TO_ONE, array('category_id' => 'id', ), 'CASCADE', null);
    } // buildRelations()

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
        return $withPrefix ? CategoryAttributeProductTableMap::CLASS_DEFAULT : CategoryAttributeProductTableMap::OM_CLASS;
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
     * @return array (CategoryAttributeProduct object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = CategoryAttributeProductTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = CategoryAttributeProductTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + CategoryAttributeProductTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = CategoryAttributeProductTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            CategoryAttributeProductTableMap::addInstanceToPool($obj, $key);
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
            $key = CategoryAttributeProductTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = CategoryAttributeProductTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                CategoryAttributeProductTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(CategoryAttributeProductTableMap::COL_ID);
            $criteria->addSelectColumn(CategoryAttributeProductTableMap::COL_CATEGORY_ID);
            $criteria->addSelectColumn(CategoryAttributeProductTableMap::COL_ATTRIBUTE_PRODUCT_ID);
            $criteria->addSelectColumn(CategoryAttributeProductTableMap::COL_ATTRIBUTE_GROUP_NAME_ID);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.CATEGORY_ID');
            $criteria->addSelectColumn($alias . '.ATTRIBUTE_PRODUCT_ID');
            $criteria->addSelectColumn($alias . '.ATTRIBUTE_GROUP_NAME_ID');
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
        return Propel::getServiceContainer()->getDatabaseMap(CategoryAttributeProductTableMap::DATABASE_NAME)->getTable(CategoryAttributeProductTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(CategoryAttributeProductTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(CategoryAttributeProductTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new CategoryAttributeProductTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a CategoryAttributeProduct or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or CategoryAttributeProduct object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CategoryAttributeProductTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\Attribute\Model\ORM\CategoryAttributeProduct) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(CategoryAttributeProductTableMap::DATABASE_NAME);
            $criteria->add(CategoryAttributeProductTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = CategoryAttributeProductQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { CategoryAttributeProductTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { CategoryAttributeProductTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the category_attribute_product table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return CategoryAttributeProductQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a CategoryAttributeProduct or Criteria object.
     *
     * @param mixed               $criteria Criteria or CategoryAttributeProduct object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CategoryAttributeProductTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from CategoryAttributeProduct object
        }

        if ($criteria->containsKey(CategoryAttributeProductTableMap::COL_ID) && $criteria->keyContainsValue(CategoryAttributeProductTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CategoryAttributeProductTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = CategoryAttributeProductQuery::create()->mergeWith($criteria);

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

} // CategoryAttributeProductTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
CategoryAttributeProductTableMap::buildTableMap();
