<?php

namespace Gekosale\Plugin\Currency\Model\ORM\Map;

use Gekosale\Plugin\Currency\Model\ORM\Currency;
use Gekosale\Plugin\Currency\Model\ORM\CurrencyQuery;
use Gekosale\Plugin\Locale\Model\ORM\Map\LocaleTableMap;
use Gekosale\Plugin\Shop\Model\ORM\Map\ShopTableMap;
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
 * This class defines the structure of the 'currency' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class CurrencyTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.Currency.Model.ORM.Map.CurrencyTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'currency';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\Currency\\Model\\ORM\\Currency';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.Currency.Model.ORM.Currency';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 11;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 11;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'currency.ID';

    /**
     * the column name for the CURRENCY_SYMBOL field
     */
    const COL_CURRENCY_SYMBOL = 'currency.CURRENCY_SYMBOL';

    /**
     * the column name for the DECIMAL_SEPARATOR field
     */
    const COL_DECIMAL_SEPARATOR = 'currency.DECIMAL_SEPARATOR';

    /**
     * the column name for the THOUSAND_SEPARATOR field
     */
    const COL_THOUSAND_SEPARATOR = 'currency.THOUSAND_SEPARATOR';

    /**
     * the column name for the POSITIVE_PREFFIX field
     */
    const COL_POSITIVE_PREFFIX = 'currency.POSITIVE_PREFFIX';

    /**
     * the column name for the POSITIVE_SUFFIX field
     */
    const COL_POSITIVE_SUFFIX = 'currency.POSITIVE_SUFFIX';

    /**
     * the column name for the NEGATIVE_PREFFIX field
     */
    const COL_NEGATIVE_PREFFIX = 'currency.NEGATIVE_PREFFIX';

    /**
     * the column name for the NEGATIVE_SUFFIX field
     */
    const COL_NEGATIVE_SUFFIX = 'currency.NEGATIVE_SUFFIX';

    /**
     * the column name for the DECIMAL_COUNT field
     */
    const COL_DECIMAL_COUNT = 'currency.DECIMAL_COUNT';

    /**
     * the column name for the CREATED_AT field
     */
    const COL_CREATED_AT = 'currency.CREATED_AT';

    /**
     * the column name for the UPDATED_AT field
     */
    const COL_UPDATED_AT = 'currency.UPDATED_AT';

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
        self::TYPE_PHPNAME       => array('Id', 'CurrencySymbol', 'DecimalSeparator', 'ThousandSeparator', 'PositivePreffix', 'PositiveSuffix', 'NegativePreffix', 'NegativeSuffix', 'DecimalCount', 'CreatedAt', 'UpdatedAt', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'currencySymbol', 'decimalSeparator', 'thousandSeparator', 'positivePreffix', 'positiveSuffix', 'negativePreffix', 'negativeSuffix', 'decimalCount', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME       => array(CurrencyTableMap::COL_ID, CurrencyTableMap::COL_CURRENCY_SYMBOL, CurrencyTableMap::COL_DECIMAL_SEPARATOR, CurrencyTableMap::COL_THOUSAND_SEPARATOR, CurrencyTableMap::COL_POSITIVE_PREFFIX, CurrencyTableMap::COL_POSITIVE_SUFFIX, CurrencyTableMap::COL_NEGATIVE_PREFFIX, CurrencyTableMap::COL_NEGATIVE_SUFFIX, CurrencyTableMap::COL_DECIMAL_COUNT, CurrencyTableMap::COL_CREATED_AT, CurrencyTableMap::COL_UPDATED_AT, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_CURRENCY_SYMBOL', 'COL_DECIMAL_SEPARATOR', 'COL_THOUSAND_SEPARATOR', 'COL_POSITIVE_PREFFIX', 'COL_POSITIVE_SUFFIX', 'COL_NEGATIVE_PREFFIX', 'COL_NEGATIVE_SUFFIX', 'COL_DECIMAL_COUNT', 'COL_CREATED_AT', 'COL_UPDATED_AT', ),
        self::TYPE_FIELDNAME     => array('id', 'currency_symbol', 'decimal_separator', 'thousand_separator', 'positive_preffix', 'positive_suffix', 'negative_preffix', 'negative_suffix', 'decimal_count', 'created_at', 'updated_at', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'CurrencySymbol' => 1, 'DecimalSeparator' => 2, 'ThousandSeparator' => 3, 'PositivePreffix' => 4, 'PositiveSuffix' => 5, 'NegativePreffix' => 6, 'NegativeSuffix' => 7, 'DecimalCount' => 8, 'CreatedAt' => 9, 'UpdatedAt' => 10, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'currencySymbol' => 1, 'decimalSeparator' => 2, 'thousandSeparator' => 3, 'positivePreffix' => 4, 'positiveSuffix' => 5, 'negativePreffix' => 6, 'negativeSuffix' => 7, 'decimalCount' => 8, 'createdAt' => 9, 'updatedAt' => 10, ),
        self::TYPE_COLNAME       => array(CurrencyTableMap::COL_ID => 0, CurrencyTableMap::COL_CURRENCY_SYMBOL => 1, CurrencyTableMap::COL_DECIMAL_SEPARATOR => 2, CurrencyTableMap::COL_THOUSAND_SEPARATOR => 3, CurrencyTableMap::COL_POSITIVE_PREFFIX => 4, CurrencyTableMap::COL_POSITIVE_SUFFIX => 5, CurrencyTableMap::COL_NEGATIVE_PREFFIX => 6, CurrencyTableMap::COL_NEGATIVE_SUFFIX => 7, CurrencyTableMap::COL_DECIMAL_COUNT => 8, CurrencyTableMap::COL_CREATED_AT => 9, CurrencyTableMap::COL_UPDATED_AT => 10, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_CURRENCY_SYMBOL' => 1, 'COL_DECIMAL_SEPARATOR' => 2, 'COL_THOUSAND_SEPARATOR' => 3, 'COL_POSITIVE_PREFFIX' => 4, 'COL_POSITIVE_SUFFIX' => 5, 'COL_NEGATIVE_PREFFIX' => 6, 'COL_NEGATIVE_SUFFIX' => 7, 'COL_DECIMAL_COUNT' => 8, 'COL_CREATED_AT' => 9, 'COL_UPDATED_AT' => 10, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'currency_symbol' => 1, 'decimal_separator' => 2, 'thousand_separator' => 3, 'positive_preffix' => 4, 'positive_suffix' => 5, 'negative_preffix' => 6, 'negative_suffix' => 7, 'decimal_count' => 8, 'created_at' => 9, 'updated_at' => 10, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
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
        $this->setName('currency');
        $this->setPhpName('Currency');
        $this->setClassName('\\Gekosale\\Plugin\\Currency\\Model\\ORM\\Currency');
        $this->setPackage('Gekosale.Plugin.Currency.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addColumn('CURRENCY_SYMBOL', 'CurrencySymbol', 'VARCHAR', true, 15, null);
        $this->addColumn('DECIMAL_SEPARATOR', 'DecimalSeparator', 'VARCHAR', false, 10, null);
        $this->addColumn('THOUSAND_SEPARATOR', 'ThousandSeparator', 'VARCHAR', false, 10, null);
        $this->addColumn('POSITIVE_PREFFIX', 'PositivePreffix', 'VARCHAR', false, 10, null);
        $this->addColumn('POSITIVE_SUFFIX', 'PositiveSuffix', 'VARCHAR', false, 10, null);
        $this->addColumn('NEGATIVE_PREFFIX', 'NegativePreffix', 'VARCHAR', false, 10, null);
        $this->addColumn('NEGATIVE_SUFFIX', 'NegativeSuffix', 'VARCHAR', false, 10, null);
        $this->addColumn('DECIMAL_COUNT', 'DecimalCount', 'INTEGER', false, null, null);
        $this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Locale', '\\Gekosale\\Plugin\\Locale\\Model\\ORM\\Locale', RelationMap::ONE_TO_MANY, array('id' => 'currency_id', ), 'SET NULL', null, 'Locales');
        $this->addRelation('ProductRelatedByBuyCurrencyId', '\\Gekosale\\Plugin\\Product\\Model\\ORM\\Product', RelationMap::ONE_TO_MANY, array('id' => 'buy_currency_id', ), null, null, 'ProductsRelatedByBuyCurrencyId');
        $this->addRelation('ProductRelatedBySellCurrencyId', '\\Gekosale\\Plugin\\Product\\Model\\ORM\\Product', RelationMap::ONE_TO_MANY, array('id' => 'sell_currency_id', ), null, null, 'ProductsRelatedBySellCurrencyId');
        $this->addRelation('Shop', '\\Gekosale\\Plugin\\Shop\\Model\\ORM\\Shop', RelationMap::ONE_TO_MANY, array('id' => 'currency_id', ), 'SET NULL', null, 'Shops');
        $this->addRelation('CurrencyShop', '\\Gekosale\\Plugin\\Currency\\Model\\ORM\\CurrencyShop', RelationMap::ONE_TO_MANY, array('id' => 'currency_id', ), 'CASCADE', null, 'CurrencyShops');
        $this->addRelation('CurrencyI18n', '\\Gekosale\\Plugin\\Currency\\Model\\ORM\\CurrencyI18n', RelationMap::ONE_TO_MANY, array('id' => 'id', ), 'CASCADE', null, 'CurrencyI18ns');
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
     * Method to invalidate the instance pool of all tables related to currency     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in ".$this->getClassNameFromBuilder($joinedTableTableMapBuilder)." instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
                LocaleTableMap::clearInstancePool();
                ShopTableMap::clearInstancePool();
                CurrencyShopTableMap::clearInstancePool();
                CurrencyI18nTableMap::clearInstancePool();
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
        return $withPrefix ? CurrencyTableMap::CLASS_DEFAULT : CurrencyTableMap::OM_CLASS;
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
     * @return array (Currency object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = CurrencyTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = CurrencyTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + CurrencyTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = CurrencyTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            CurrencyTableMap::addInstanceToPool($obj, $key);
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
            $key = CurrencyTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = CurrencyTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                CurrencyTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(CurrencyTableMap::COL_ID);
            $criteria->addSelectColumn(CurrencyTableMap::COL_CURRENCY_SYMBOL);
            $criteria->addSelectColumn(CurrencyTableMap::COL_DECIMAL_SEPARATOR);
            $criteria->addSelectColumn(CurrencyTableMap::COL_THOUSAND_SEPARATOR);
            $criteria->addSelectColumn(CurrencyTableMap::COL_POSITIVE_PREFFIX);
            $criteria->addSelectColumn(CurrencyTableMap::COL_POSITIVE_SUFFIX);
            $criteria->addSelectColumn(CurrencyTableMap::COL_NEGATIVE_PREFFIX);
            $criteria->addSelectColumn(CurrencyTableMap::COL_NEGATIVE_SUFFIX);
            $criteria->addSelectColumn(CurrencyTableMap::COL_DECIMAL_COUNT);
            $criteria->addSelectColumn(CurrencyTableMap::COL_CREATED_AT);
            $criteria->addSelectColumn(CurrencyTableMap::COL_UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.CURRENCY_SYMBOL');
            $criteria->addSelectColumn($alias . '.DECIMAL_SEPARATOR');
            $criteria->addSelectColumn($alias . '.THOUSAND_SEPARATOR');
            $criteria->addSelectColumn($alias . '.POSITIVE_PREFFIX');
            $criteria->addSelectColumn($alias . '.POSITIVE_SUFFIX');
            $criteria->addSelectColumn($alias . '.NEGATIVE_PREFFIX');
            $criteria->addSelectColumn($alias . '.NEGATIVE_SUFFIX');
            $criteria->addSelectColumn($alias . '.DECIMAL_COUNT');
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
        return Propel::getServiceContainer()->getDatabaseMap(CurrencyTableMap::DATABASE_NAME)->getTable(CurrencyTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(CurrencyTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(CurrencyTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new CurrencyTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a Currency or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Currency object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CurrencyTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\Currency\Model\ORM\Currency) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(CurrencyTableMap::DATABASE_NAME);
            $criteria->add(CurrencyTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = CurrencyQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { CurrencyTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { CurrencyTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the currency table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return CurrencyQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Currency or Criteria object.
     *
     * @param mixed               $criteria Criteria or Currency object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CurrencyTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Currency object
        }

        if ($criteria->containsKey(CurrencyTableMap::COL_ID) && $criteria->keyContainsValue(CurrencyTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CurrencyTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = CurrencyQuery::create()->mergeWith($criteria);

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

} // CurrencyTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
CurrencyTableMap::buildTableMap();
