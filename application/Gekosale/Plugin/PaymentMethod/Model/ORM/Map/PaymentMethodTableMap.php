<?php

namespace Gekosale\Plugin\PaymentMethod\Model\ORM\Map;

use Gekosale\Plugin\DispatchMethod\Model\ORM\Map\DispatchMethodpaymentMethodTableMap;
use Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethod;
use Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodQuery;
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
 * This class defines the structure of the 'payment_method' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class PaymentMethodTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.PaymentMethod.Model.ORM.Map.PaymentMethodTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'payment_method';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\PaymentMethod\\Model\\ORM\\PaymentMethod';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.PaymentMethod.Model.ORM.PaymentMethod';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 8;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 8;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'payment_method.ID';

    /**
     * the column name for the CONTROLLER field
     */
    const COL_CONTROLLER = 'payment_method.CONTROLLER';

    /**
     * the column name for the ONLINE field
     */
    const COL_ONLINE = 'payment_method.ONLINE';

    /**
     * the column name for the ACTIVE field
     */
    const COL_ACTIVE = 'payment_method.ACTIVE';

    /**
     * the column name for the MAXIMUM_AMOUNT field
     */
    const COL_MAXIMUM_AMOUNT = 'payment_method.MAXIMUM_AMOUNT';

    /**
     * the column name for the HIERARCHY field
     */
    const COL_HIERARCHY = 'payment_method.HIERARCHY';

    /**
     * the column name for the CREATED_AT field
     */
    const COL_CREATED_AT = 'payment_method.CREATED_AT';

    /**
     * the column name for the UPDATED_AT field
     */
    const COL_UPDATED_AT = 'payment_method.UPDATED_AT';

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
        self::TYPE_PHPNAME       => array('Id', 'Controller', 'IsOnline', 'IsActive', 'MaximumAmount', 'Hierarchy', 'CreatedAt', 'UpdatedAt', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'controller', 'isOnline', 'isActive', 'maximumAmount', 'hierarchy', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME       => array(PaymentMethodTableMap::COL_ID, PaymentMethodTableMap::COL_CONTROLLER, PaymentMethodTableMap::COL_ONLINE, PaymentMethodTableMap::COL_ACTIVE, PaymentMethodTableMap::COL_MAXIMUM_AMOUNT, PaymentMethodTableMap::COL_HIERARCHY, PaymentMethodTableMap::COL_CREATED_AT, PaymentMethodTableMap::COL_UPDATED_AT, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_CONTROLLER', 'COL_ONLINE', 'COL_ACTIVE', 'COL_MAXIMUM_AMOUNT', 'COL_HIERARCHY', 'COL_CREATED_AT', 'COL_UPDATED_AT', ),
        self::TYPE_FIELDNAME     => array('id', 'controller', 'online', 'active', 'maximum_amount', 'hierarchy', 'created_at', 'updated_at', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Controller' => 1, 'IsOnline' => 2, 'IsActive' => 3, 'MaximumAmount' => 4, 'Hierarchy' => 5, 'CreatedAt' => 6, 'UpdatedAt' => 7, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'controller' => 1, 'isOnline' => 2, 'isActive' => 3, 'maximumAmount' => 4, 'hierarchy' => 5, 'createdAt' => 6, 'updatedAt' => 7, ),
        self::TYPE_COLNAME       => array(PaymentMethodTableMap::COL_ID => 0, PaymentMethodTableMap::COL_CONTROLLER => 1, PaymentMethodTableMap::COL_ONLINE => 2, PaymentMethodTableMap::COL_ACTIVE => 3, PaymentMethodTableMap::COL_MAXIMUM_AMOUNT => 4, PaymentMethodTableMap::COL_HIERARCHY => 5, PaymentMethodTableMap::COL_CREATED_AT => 6, PaymentMethodTableMap::COL_UPDATED_AT => 7, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_CONTROLLER' => 1, 'COL_ONLINE' => 2, 'COL_ACTIVE' => 3, 'COL_MAXIMUM_AMOUNT' => 4, 'COL_HIERARCHY' => 5, 'COL_CREATED_AT' => 6, 'COL_UPDATED_AT' => 7, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'controller' => 1, 'online' => 2, 'active' => 3, 'maximum_amount' => 4, 'hierarchy' => 5, 'created_at' => 6, 'updated_at' => 7, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, )
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
        $this->setName('payment_method');
        $this->setPhpName('PaymentMethod');
        $this->setClassName('\\Gekosale\\Plugin\\PaymentMethod\\Model\\ORM\\PaymentMethod');
        $this->setPackage('Gekosale.Plugin.PaymentMethod.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addColumn('CONTROLLER', 'Controller', 'VARCHAR', true, 64, null);
        $this->addColumn('ONLINE', 'IsOnline', 'BOOLEAN', true, 1, true);
        $this->addColumn('ACTIVE', 'IsActive', 'BOOLEAN', true, 1, true);
        $this->addColumn('MAXIMUM_AMOUNT', 'MaximumAmount', 'DECIMAL', false, 15, null);
        $this->addColumn('HIERARCHY', 'Hierarchy', 'INTEGER', false, null, 0);
        $this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('DispatchMethodpaymentMethod', '\\Gekosale\\Plugin\\DispatchMethod\\Model\\ORM\\DispatchMethodpaymentMethod', RelationMap::ONE_TO_MANY, array('id' => 'payment_method_id', ), 'CASCADE', null, 'DispatchMethodpaymentMethods');
        $this->addRelation('PaymentMethodShop', '\\Gekosale\\Plugin\\PaymentMethod\\Model\\ORM\\PaymentMethodShop', RelationMap::ONE_TO_MANY, array('id' => 'payment_method_id', ), 'CASCADE', null, 'PaymentMethodShops');
        $this->addRelation('PaymentMethodI18n', '\\Gekosale\\Plugin\\PaymentMethod\\Model\\ORM\\PaymentMethodI18n', RelationMap::ONE_TO_MANY, array('id' => 'id', ), 'CASCADE', null, 'PaymentMethodI18ns');
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
            'i18n' => array('i18n_table' => '%TABLE%_i18n', 'i18n_phpname' => '%PHPNAME%I18n', 'i18n_columns' => 'name', 'locale_column' => 'locale', 'locale_length' => '5', 'default_locale' => '', 'locale_alias' => '', ),
            'timestampable' => array('create_column' => 'created_at', 'update_column' => 'updated_at', ),
        );
    } // getBehaviors()
    /**
     * Method to invalidate the instance pool of all tables related to payment_method     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in ".$this->getClassNameFromBuilder($joinedTableTableMapBuilder)." instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
                DispatchMethodpaymentMethodTableMap::clearInstancePool();
                PaymentMethodShopTableMap::clearInstancePool();
                PaymentMethodI18nTableMap::clearInstancePool();
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
        return $withPrefix ? PaymentMethodTableMap::CLASS_DEFAULT : PaymentMethodTableMap::OM_CLASS;
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
     * @return array (PaymentMethod object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = PaymentMethodTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = PaymentMethodTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + PaymentMethodTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = PaymentMethodTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            PaymentMethodTableMap::addInstanceToPool($obj, $key);
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
            $key = PaymentMethodTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = PaymentMethodTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                PaymentMethodTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(PaymentMethodTableMap::COL_ID);
            $criteria->addSelectColumn(PaymentMethodTableMap::COL_CONTROLLER);
            $criteria->addSelectColumn(PaymentMethodTableMap::COL_ONLINE);
            $criteria->addSelectColumn(PaymentMethodTableMap::COL_ACTIVE);
            $criteria->addSelectColumn(PaymentMethodTableMap::COL_MAXIMUM_AMOUNT);
            $criteria->addSelectColumn(PaymentMethodTableMap::COL_HIERARCHY);
            $criteria->addSelectColumn(PaymentMethodTableMap::COL_CREATED_AT);
            $criteria->addSelectColumn(PaymentMethodTableMap::COL_UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.CONTROLLER');
            $criteria->addSelectColumn($alias . '.ONLINE');
            $criteria->addSelectColumn($alias . '.ACTIVE');
            $criteria->addSelectColumn($alias . '.MAXIMUM_AMOUNT');
            $criteria->addSelectColumn($alias . '.HIERARCHY');
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
        return Propel::getServiceContainer()->getDatabaseMap(PaymentMethodTableMap::DATABASE_NAME)->getTable(PaymentMethodTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(PaymentMethodTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(PaymentMethodTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new PaymentMethodTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a PaymentMethod or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or PaymentMethod object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(PaymentMethodTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethod) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(PaymentMethodTableMap::DATABASE_NAME);
            $criteria->add(PaymentMethodTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = PaymentMethodQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { PaymentMethodTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { PaymentMethodTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the payment_method table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return PaymentMethodQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a PaymentMethod or Criteria object.
     *
     * @param mixed               $criteria Criteria or PaymentMethod object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(PaymentMethodTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from PaymentMethod object
        }

        if ($criteria->containsKey(PaymentMethodTableMap::COL_ID) && $criteria->keyContainsValue(PaymentMethodTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.PaymentMethodTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = PaymentMethodQuery::create()->mergeWith($criteria);

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

} // PaymentMethodTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
PaymentMethodTableMap::buildTableMap();
