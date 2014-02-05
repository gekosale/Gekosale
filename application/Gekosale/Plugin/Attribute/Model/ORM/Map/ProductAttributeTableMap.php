<?php

namespace Gekosale\Plugin\Attribute\Model\ORM\Map;

use Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute;
use Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeQuery;
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
 * This class defines the structure of the 'product_attribute' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class ProductAttributeTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.Attribute.Model.ORM.Map.ProductAttributeTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'product_attribute';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\Attribute\\Model\\ORM\\ProductAttribute';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.Attribute.Model.ORM.ProductAttribute';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 15;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 15;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'product_attribute.ID';

    /**
     * the column name for the PRODUCT_ID field
     */
    const COL_PRODUCT_ID = 'product_attribute.PRODUCT_ID';

    /**
     * the column name for the SUFFIX_TYPE_ID field
     */
    const COL_SUFFIX_TYPE_ID = 'product_attribute.SUFFIX_TYPE_ID';

    /**
     * the column name for the VALUE field
     */
    const COL_VALUE = 'product_attribute.VALUE';

    /**
     * the column name for the ADD_DATE field
     */
    const COL_ADD_DATE = 'product_attribute.ADD_DATE';

    /**
     * the column name for the STOCK field
     */
    const COL_STOCK = 'product_attribute.STOCK';

    /**
     * the column name for the ATTRIBUTE_GROUP_NAME_ID field
     */
    const COL_ATTRIBUTE_GROUP_NAME_ID = 'product_attribute.ATTRIBUTE_GROUP_NAME_ID';

    /**
     * the column name for the ATTRIBUTE_PRICE field
     */
    const COL_ATTRIBUTE_PRICE = 'product_attribute.ATTRIBUTE_PRICE';

    /**
     * the column name for the DISCOUNT_PRICE field
     */
    const COL_DISCOUNT_PRICE = 'product_attribute.DISCOUNT_PRICE';

    /**
     * the column name for the SYMBOL field
     */
    const COL_SYMBOL = 'product_attribute.SYMBOL';

    /**
     * the column name for the WEIGHT field
     */
    const COL_WEIGHT = 'product_attribute.WEIGHT';

    /**
     * the column name for the STATUS field
     */
    const COL_STATUS = 'product_attribute.STATUS';

    /**
     * the column name for the AVAILABILITY_ID field
     */
    const COL_AVAILABILITY_ID = 'product_attribute.AVAILABILITY_ID';

    /**
     * the column name for the PHOTO_ID field
     */
    const COL_PHOTO_ID = 'product_attribute.PHOTO_ID';

    /**
     * the column name for the FIRMES_ID field
     */
    const COL_FIRMES_ID = 'product_attribute.FIRMES_ID';

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
        self::TYPE_PHPNAME       => array('Id', 'ProductId', 'SuffixTypeId', 'Value', 'AddDate', 'Stock', 'AttributeGroupNameId', 'AttributePrice', 'DiscountPrice', 'Symbol', 'Weight', 'Status', 'AvailabilityId', 'PhotoId', 'FirmesId', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'productId', 'suffixTypeId', 'value', 'addDate', 'stock', 'attributeGroupNameId', 'attributePrice', 'discountPrice', 'symbol', 'weight', 'status', 'availabilityId', 'photoId', 'firmesId', ),
        self::TYPE_COLNAME       => array(ProductAttributeTableMap::COL_ID, ProductAttributeTableMap::COL_PRODUCT_ID, ProductAttributeTableMap::COL_SUFFIX_TYPE_ID, ProductAttributeTableMap::COL_VALUE, ProductAttributeTableMap::COL_ADD_DATE, ProductAttributeTableMap::COL_STOCK, ProductAttributeTableMap::COL_ATTRIBUTE_GROUP_NAME_ID, ProductAttributeTableMap::COL_ATTRIBUTE_PRICE, ProductAttributeTableMap::COL_DISCOUNT_PRICE, ProductAttributeTableMap::COL_SYMBOL, ProductAttributeTableMap::COL_WEIGHT, ProductAttributeTableMap::COL_STATUS, ProductAttributeTableMap::COL_AVAILABILITY_ID, ProductAttributeTableMap::COL_PHOTO_ID, ProductAttributeTableMap::COL_FIRMES_ID, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_PRODUCT_ID', 'COL_SUFFIX_TYPE_ID', 'COL_VALUE', 'COL_ADD_DATE', 'COL_STOCK', 'COL_ATTRIBUTE_GROUP_NAME_ID', 'COL_ATTRIBUTE_PRICE', 'COL_DISCOUNT_PRICE', 'COL_SYMBOL', 'COL_WEIGHT', 'COL_STATUS', 'COL_AVAILABILITY_ID', 'COL_PHOTO_ID', 'COL_FIRMES_ID', ),
        self::TYPE_FIELDNAME     => array('id', 'product_id', 'suffix_type_id', 'value', 'add_date', 'stock', 'attribute_group_name_id', 'attribute_price', 'discount_price', 'symbol', 'weight', 'status', 'availability_id', 'photo_id', 'firmes_id', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'ProductId' => 1, 'SuffixTypeId' => 2, 'Value' => 3, 'AddDate' => 4, 'Stock' => 5, 'AttributeGroupNameId' => 6, 'AttributePrice' => 7, 'DiscountPrice' => 8, 'Symbol' => 9, 'Weight' => 10, 'Status' => 11, 'AvailabilityId' => 12, 'PhotoId' => 13, 'FirmesId' => 14, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'productId' => 1, 'suffixTypeId' => 2, 'value' => 3, 'addDate' => 4, 'stock' => 5, 'attributeGroupNameId' => 6, 'attributePrice' => 7, 'discountPrice' => 8, 'symbol' => 9, 'weight' => 10, 'status' => 11, 'availabilityId' => 12, 'photoId' => 13, 'firmesId' => 14, ),
        self::TYPE_COLNAME       => array(ProductAttributeTableMap::COL_ID => 0, ProductAttributeTableMap::COL_PRODUCT_ID => 1, ProductAttributeTableMap::COL_SUFFIX_TYPE_ID => 2, ProductAttributeTableMap::COL_VALUE => 3, ProductAttributeTableMap::COL_ADD_DATE => 4, ProductAttributeTableMap::COL_STOCK => 5, ProductAttributeTableMap::COL_ATTRIBUTE_GROUP_NAME_ID => 6, ProductAttributeTableMap::COL_ATTRIBUTE_PRICE => 7, ProductAttributeTableMap::COL_DISCOUNT_PRICE => 8, ProductAttributeTableMap::COL_SYMBOL => 9, ProductAttributeTableMap::COL_WEIGHT => 10, ProductAttributeTableMap::COL_STATUS => 11, ProductAttributeTableMap::COL_AVAILABILITY_ID => 12, ProductAttributeTableMap::COL_PHOTO_ID => 13, ProductAttributeTableMap::COL_FIRMES_ID => 14, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_PRODUCT_ID' => 1, 'COL_SUFFIX_TYPE_ID' => 2, 'COL_VALUE' => 3, 'COL_ADD_DATE' => 4, 'COL_STOCK' => 5, 'COL_ATTRIBUTE_GROUP_NAME_ID' => 6, 'COL_ATTRIBUTE_PRICE' => 7, 'COL_DISCOUNT_PRICE' => 8, 'COL_SYMBOL' => 9, 'COL_WEIGHT' => 10, 'COL_STATUS' => 11, 'COL_AVAILABILITY_ID' => 12, 'COL_PHOTO_ID' => 13, 'COL_FIRMES_ID' => 14, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'product_id' => 1, 'suffix_type_id' => 2, 'value' => 3, 'add_date' => 4, 'stock' => 5, 'attribute_group_name_id' => 6, 'attribute_price' => 7, 'discount_price' => 8, 'symbol' => 9, 'weight' => 10, 'status' => 11, 'availability_id' => 12, 'photo_id' => 13, 'firmes_id' => 14, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
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
        $this->setName('product_attribute');
        $this->setPhpName('ProductAttribute');
        $this->setClassName('\\Gekosale\\Plugin\\Attribute\\Model\\ORM\\ProductAttribute');
        $this->setPackage('Gekosale.Plugin.Attribute.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addForeignKey('PRODUCT_ID', 'ProductId', 'INTEGER', 'product', 'ID', true, 10, null);
        $this->addColumn('SUFFIX_TYPE_ID', 'SuffixTypeId', 'INTEGER', true, 10, null);
        $this->addColumn('VALUE', 'Value', 'DECIMAL', true, 16, 0);
        $this->addColumn('ADD_DATE', 'AddDate', 'TIMESTAMP', true, null, 'CURRENT_TIMESTAMP');
        $this->addColumn('STOCK', 'Stock', 'INTEGER', true, 10, 0);
        $this->addForeignKey('ATTRIBUTE_GROUP_NAME_ID', 'AttributeGroupNameId', 'INTEGER', 'attribute_group_name', 'ID', false, 10, null);
        $this->addColumn('ATTRIBUTE_PRICE', 'AttributePrice', 'DECIMAL', false, 15, null);
        $this->addColumn('DISCOUNT_PRICE', 'DiscountPrice', 'DECIMAL', false, 15, null);
        $this->addColumn('SYMBOL', 'Symbol', 'VARCHAR', false, 128, null);
        $this->addColumn('WEIGHT', 'Weight', 'DECIMAL', false, 15, 0);
        $this->addColumn('STATUS', 'Status', 'BOOLEAN', true, 1, true);
        $this->addForeignKey('AVAILABILITY_ID', 'AvailabilityId', 'INTEGER', 'availability', 'ID', false, null, null);
        $this->addForeignKey('PHOTO_ID', 'PhotoId', 'INTEGER', 'file', 'ID', false, null, null);
        $this->addColumn('FIRMES_ID', 'FirmesId', 'INTEGER', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('AttributeGroupName', '\\Gekosale\\Plugin\\Attribute\\Model\\ORM\\AttributeGroupName', RelationMap::MANY_TO_ONE, array('attribute_group_name_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('Availability', '\\Gekosale\\Plugin\\Availability\\Model\\ORM\\Availability', RelationMap::MANY_TO_ONE, array('availability_id' => 'id', ), 'SET NULL', null);
        $this->addRelation('File', '\\Gekosale\\Plugin\\File\\Model\\ORM\\File', RelationMap::MANY_TO_ONE, array('photo_id' => 'id', ), 'SET NULL', null);
        $this->addRelation('Product', '\\Gekosale\\Plugin\\Product\\Model\\ORM\\Product', RelationMap::MANY_TO_ONE, array('product_id' => 'id', ), 'CASCADE', 'CASCADE');
        $this->addRelation('ProductAttributeValueSet', '\\Gekosale\\Plugin\\Attribute\\Model\\ORM\\ProductAttributeValueSet', RelationMap::ONE_TO_MANY, array('id' => 'product_attribute_id', ), 'CASCADE', null, 'ProductAttributeValueSets');
    } // buildRelations()
    /**
     * Method to invalidate the instance pool of all tables related to product_attribute     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in ".$this->getClassNameFromBuilder($joinedTableTableMapBuilder)." instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
                ProductAttributeValueSetTableMap::clearInstancePool();
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
        return $withPrefix ? ProductAttributeTableMap::CLASS_DEFAULT : ProductAttributeTableMap::OM_CLASS;
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
     * @return array (ProductAttribute object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = ProductAttributeTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = ProductAttributeTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + ProductAttributeTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = ProductAttributeTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            ProductAttributeTableMap::addInstanceToPool($obj, $key);
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
            $key = ProductAttributeTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = ProductAttributeTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                ProductAttributeTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(ProductAttributeTableMap::COL_ID);
            $criteria->addSelectColumn(ProductAttributeTableMap::COL_PRODUCT_ID);
            $criteria->addSelectColumn(ProductAttributeTableMap::COL_SUFFIX_TYPE_ID);
            $criteria->addSelectColumn(ProductAttributeTableMap::COL_VALUE);
            $criteria->addSelectColumn(ProductAttributeTableMap::COL_ADD_DATE);
            $criteria->addSelectColumn(ProductAttributeTableMap::COL_STOCK);
            $criteria->addSelectColumn(ProductAttributeTableMap::COL_ATTRIBUTE_GROUP_NAME_ID);
            $criteria->addSelectColumn(ProductAttributeTableMap::COL_ATTRIBUTE_PRICE);
            $criteria->addSelectColumn(ProductAttributeTableMap::COL_DISCOUNT_PRICE);
            $criteria->addSelectColumn(ProductAttributeTableMap::COL_SYMBOL);
            $criteria->addSelectColumn(ProductAttributeTableMap::COL_WEIGHT);
            $criteria->addSelectColumn(ProductAttributeTableMap::COL_STATUS);
            $criteria->addSelectColumn(ProductAttributeTableMap::COL_AVAILABILITY_ID);
            $criteria->addSelectColumn(ProductAttributeTableMap::COL_PHOTO_ID);
            $criteria->addSelectColumn(ProductAttributeTableMap::COL_FIRMES_ID);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.PRODUCT_ID');
            $criteria->addSelectColumn($alias . '.SUFFIX_TYPE_ID');
            $criteria->addSelectColumn($alias . '.VALUE');
            $criteria->addSelectColumn($alias . '.ADD_DATE');
            $criteria->addSelectColumn($alias . '.STOCK');
            $criteria->addSelectColumn($alias . '.ATTRIBUTE_GROUP_NAME_ID');
            $criteria->addSelectColumn($alias . '.ATTRIBUTE_PRICE');
            $criteria->addSelectColumn($alias . '.DISCOUNT_PRICE');
            $criteria->addSelectColumn($alias . '.SYMBOL');
            $criteria->addSelectColumn($alias . '.WEIGHT');
            $criteria->addSelectColumn($alias . '.STATUS');
            $criteria->addSelectColumn($alias . '.AVAILABILITY_ID');
            $criteria->addSelectColumn($alias . '.PHOTO_ID');
            $criteria->addSelectColumn($alias . '.FIRMES_ID');
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
        return Propel::getServiceContainer()->getDatabaseMap(ProductAttributeTableMap::DATABASE_NAME)->getTable(ProductAttributeTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(ProductAttributeTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(ProductAttributeTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new ProductAttributeTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a ProductAttribute or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ProductAttribute object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(ProductAttributeTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(ProductAttributeTableMap::DATABASE_NAME);
            $criteria->add(ProductAttributeTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = ProductAttributeQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { ProductAttributeTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { ProductAttributeTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the product_attribute table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return ProductAttributeQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a ProductAttribute or Criteria object.
     *
     * @param mixed               $criteria Criteria or ProductAttribute object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProductAttributeTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from ProductAttribute object
        }

        if ($criteria->containsKey(ProductAttributeTableMap::COL_ID) && $criteria->keyContainsValue(ProductAttributeTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.ProductAttributeTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = ProductAttributeQuery::create()->mergeWith($criteria);

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

} // ProductAttributeTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
ProductAttributeTableMap::buildTableMap();
