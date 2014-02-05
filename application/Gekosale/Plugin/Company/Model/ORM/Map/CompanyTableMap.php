<?php

namespace Gekosale\Plugin\Company\Model\ORM\Map;

use Gekosale\Plugin\Company\Model\ORM\Company;
use Gekosale\Plugin\Company\Model\ORM\CompanyQuery;
use Gekosale\Plugin\Controller\Model\ORM\Map\ControllerPermissionTableMap;
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
 * This class defines the structure of the 'company' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class CompanyTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.Company.Model.ORM.Map.CompanyTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'company';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\Company\\Model\\ORM\\Company';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.Company.Model.ORM.Company';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 13;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 13;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'company.ID';

    /**
     * the column name for the COUNTRY_ID field
     */
    const COL_COUNTRY_ID = 'company.COUNTRY_ID';

    /**
     * the column name for the PHOTO_ID field
     */
    const COL_PHOTO_ID = 'company.PHOTO_ID';

    /**
     * the column name for the BANK_NAME field
     */
    const COL_BANK_NAME = 'company.BANK_NAME';

    /**
     * the column name for the BANK_ACCOUNT_NO field
     */
    const COL_BANK_ACCOUNT_NO = 'company.BANK_ACCOUNT_NO';

    /**
     * the column name for the TAX_ID field
     */
    const COL_TAX_ID = 'company.TAX_ID';

    /**
     * the column name for the COMPANY_NAME field
     */
    const COL_COMPANY_NAME = 'company.COMPANY_NAME';

    /**
     * the column name for the SHORT_COMPANY_NAME field
     */
    const COL_SHORT_COMPANY_NAME = 'company.SHORT_COMPANY_NAME';

    /**
     * the column name for the POST_CODE field
     */
    const COL_POST_CODE = 'company.POST_CODE';

    /**
     * the column name for the CITY field
     */
    const COL_CITY = 'company.CITY';

    /**
     * the column name for the STREET field
     */
    const COL_STREET = 'company.STREET';

    /**
     * the column name for the STREET_NO field
     */
    const COL_STREET_NO = 'company.STREET_NO';

    /**
     * the column name for the PLACE_NO field
     */
    const COL_PLACE_NO = 'company.PLACE_NO';

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
        self::TYPE_PHPNAME       => array('Id', 'CountryId', 'PhotoId', 'BankName', 'BankAccountNo', 'TaxId', 'CompanyName', 'ShortCompanyName', 'PostCode', 'City', 'Street', 'StreetNo', 'PlaceNo', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'countryId', 'photoId', 'bankName', 'bankAccountNo', 'taxId', 'companyName', 'shortCompanyName', 'postCode', 'city', 'street', 'streetNo', 'placeNo', ),
        self::TYPE_COLNAME       => array(CompanyTableMap::COL_ID, CompanyTableMap::COL_COUNTRY_ID, CompanyTableMap::COL_PHOTO_ID, CompanyTableMap::COL_BANK_NAME, CompanyTableMap::COL_BANK_ACCOUNT_NO, CompanyTableMap::COL_TAX_ID, CompanyTableMap::COL_COMPANY_NAME, CompanyTableMap::COL_SHORT_COMPANY_NAME, CompanyTableMap::COL_POST_CODE, CompanyTableMap::COL_CITY, CompanyTableMap::COL_STREET, CompanyTableMap::COL_STREET_NO, CompanyTableMap::COL_PLACE_NO, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_COUNTRY_ID', 'COL_PHOTO_ID', 'COL_BANK_NAME', 'COL_BANK_ACCOUNT_NO', 'COL_TAX_ID', 'COL_COMPANY_NAME', 'COL_SHORT_COMPANY_NAME', 'COL_POST_CODE', 'COL_CITY', 'COL_STREET', 'COL_STREET_NO', 'COL_PLACE_NO', ),
        self::TYPE_FIELDNAME     => array('id', 'country_id', 'photo_id', 'bank_name', 'bank_account_no', 'tax_id', 'company_name', 'short_company_name', 'post_code', 'city', 'street', 'street_no', 'place_no', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'CountryId' => 1, 'PhotoId' => 2, 'BankName' => 3, 'BankAccountNo' => 4, 'TaxId' => 5, 'CompanyName' => 6, 'ShortCompanyName' => 7, 'PostCode' => 8, 'City' => 9, 'Street' => 10, 'StreetNo' => 11, 'PlaceNo' => 12, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'countryId' => 1, 'photoId' => 2, 'bankName' => 3, 'bankAccountNo' => 4, 'taxId' => 5, 'companyName' => 6, 'shortCompanyName' => 7, 'postCode' => 8, 'city' => 9, 'street' => 10, 'streetNo' => 11, 'placeNo' => 12, ),
        self::TYPE_COLNAME       => array(CompanyTableMap::COL_ID => 0, CompanyTableMap::COL_COUNTRY_ID => 1, CompanyTableMap::COL_PHOTO_ID => 2, CompanyTableMap::COL_BANK_NAME => 3, CompanyTableMap::COL_BANK_ACCOUNT_NO => 4, CompanyTableMap::COL_TAX_ID => 5, CompanyTableMap::COL_COMPANY_NAME => 6, CompanyTableMap::COL_SHORT_COMPANY_NAME => 7, CompanyTableMap::COL_POST_CODE => 8, CompanyTableMap::COL_CITY => 9, CompanyTableMap::COL_STREET => 10, CompanyTableMap::COL_STREET_NO => 11, CompanyTableMap::COL_PLACE_NO => 12, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_COUNTRY_ID' => 1, 'COL_PHOTO_ID' => 2, 'COL_BANK_NAME' => 3, 'COL_BANK_ACCOUNT_NO' => 4, 'COL_TAX_ID' => 5, 'COL_COMPANY_NAME' => 6, 'COL_SHORT_COMPANY_NAME' => 7, 'COL_POST_CODE' => 8, 'COL_CITY' => 9, 'COL_STREET' => 10, 'COL_STREET_NO' => 11, 'COL_PLACE_NO' => 12, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'country_id' => 1, 'photo_id' => 2, 'bank_name' => 3, 'bank_account_no' => 4, 'tax_id' => 5, 'company_name' => 6, 'short_company_name' => 7, 'post_code' => 8, 'city' => 9, 'street' => 10, 'street_no' => 11, 'place_no' => 12, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, )
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
        $this->setName('company');
        $this->setPhpName('Company');
        $this->setClassName('\\Gekosale\\Plugin\\Company\\Model\\ORM\\Company');
        $this->setPackage('Gekosale.Plugin.Company.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addForeignKey('COUNTRY_ID', 'CountryId', 'INTEGER', 'country', 'ID', false, 10, null);
        $this->addForeignKey('PHOTO_ID', 'PhotoId', 'INTEGER', 'file', 'ID', false, 10, 1);
        $this->addColumn('BANK_NAME', 'BankName', 'VARCHAR', false, 500, null);
        $this->addColumn('BANK_ACCOUNT_NO', 'BankAccountNo', 'VARCHAR', false, 50, null);
        $this->addColumn('TAX_ID', 'TaxId', 'VARCHAR', false, 45, null);
        $this->addColumn('COMPANY_NAME', 'CompanyName', 'VARCHAR', false, 255, null);
        $this->addColumn('SHORT_COMPANY_NAME', 'ShortCompanyName', 'VARCHAR', false, 255, null);
        $this->addColumn('POST_CODE', 'PostCode', 'VARCHAR', false, 45, null);
        $this->addColumn('CITY', 'City', 'VARCHAR', false, 45, null);
        $this->addColumn('STREET', 'Street', 'VARCHAR', false, 45, null);
        $this->addColumn('STREET_NO', 'StreetNo', 'VARCHAR', false, 45, null);
        $this->addColumn('PLACE_NO', 'PlaceNo', 'VARCHAR', false, 45, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Country', '\\Gekosale\\Plugin\\Country\\Model\\ORM\\Country', RelationMap::MANY_TO_ONE, array('country_id' => 'id', ), null, null);
        $this->addRelation('File', '\\Gekosale\\Plugin\\File\\Model\\ORM\\File', RelationMap::MANY_TO_ONE, array('photo_id' => 'id', ), 'SET NULL', null);
        $this->addRelation('ControllerPermission', '\\Gekosale\\Plugin\\Controller\\Model\\ORM\\ControllerPermission', RelationMap::ONE_TO_MANY, array('id' => 'company_id', ), 'CASCADE', null, 'ControllerPermissions');
        $this->addRelation('Shop', '\\Gekosale\\Plugin\\Shop\\Model\\ORM\\Shop', RelationMap::ONE_TO_MANY, array('id' => 'company_id', ), 'CASCADE', null, 'Shops');
    } // buildRelations()
    /**
     * Method to invalidate the instance pool of all tables related to company     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in ".$this->getClassNameFromBuilder($joinedTableTableMapBuilder)." instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
                ControllerPermissionTableMap::clearInstancePool();
                ShopTableMap::clearInstancePool();
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
        return $withPrefix ? CompanyTableMap::CLASS_DEFAULT : CompanyTableMap::OM_CLASS;
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
     * @return array (Company object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = CompanyTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = CompanyTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + CompanyTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = CompanyTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            CompanyTableMap::addInstanceToPool($obj, $key);
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
            $key = CompanyTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = CompanyTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                CompanyTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(CompanyTableMap::COL_ID);
            $criteria->addSelectColumn(CompanyTableMap::COL_COUNTRY_ID);
            $criteria->addSelectColumn(CompanyTableMap::COL_PHOTO_ID);
            $criteria->addSelectColumn(CompanyTableMap::COL_BANK_NAME);
            $criteria->addSelectColumn(CompanyTableMap::COL_BANK_ACCOUNT_NO);
            $criteria->addSelectColumn(CompanyTableMap::COL_TAX_ID);
            $criteria->addSelectColumn(CompanyTableMap::COL_COMPANY_NAME);
            $criteria->addSelectColumn(CompanyTableMap::COL_SHORT_COMPANY_NAME);
            $criteria->addSelectColumn(CompanyTableMap::COL_POST_CODE);
            $criteria->addSelectColumn(CompanyTableMap::COL_CITY);
            $criteria->addSelectColumn(CompanyTableMap::COL_STREET);
            $criteria->addSelectColumn(CompanyTableMap::COL_STREET_NO);
            $criteria->addSelectColumn(CompanyTableMap::COL_PLACE_NO);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.COUNTRY_ID');
            $criteria->addSelectColumn($alias . '.PHOTO_ID');
            $criteria->addSelectColumn($alias . '.BANK_NAME');
            $criteria->addSelectColumn($alias . '.BANK_ACCOUNT_NO');
            $criteria->addSelectColumn($alias . '.TAX_ID');
            $criteria->addSelectColumn($alias . '.COMPANY_NAME');
            $criteria->addSelectColumn($alias . '.SHORT_COMPANY_NAME');
            $criteria->addSelectColumn($alias . '.POST_CODE');
            $criteria->addSelectColumn($alias . '.CITY');
            $criteria->addSelectColumn($alias . '.STREET');
            $criteria->addSelectColumn($alias . '.STREET_NO');
            $criteria->addSelectColumn($alias . '.PLACE_NO');
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
        return Propel::getServiceContainer()->getDatabaseMap(CompanyTableMap::DATABASE_NAME)->getTable(CompanyTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(CompanyTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(CompanyTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new CompanyTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a Company or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Company object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CompanyTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\Company\Model\ORM\Company) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(CompanyTableMap::DATABASE_NAME);
            $criteria->add(CompanyTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = CompanyQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { CompanyTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { CompanyTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the company table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return CompanyQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Company or Criteria object.
     *
     * @param mixed               $criteria Criteria or Company object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CompanyTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Company object
        }

        if ($criteria->containsKey(CompanyTableMap::COL_ID) && $criteria->keyContainsValue(CompanyTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.CompanyTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = CompanyQuery::create()->mergeWith($criteria);

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

} // CompanyTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
CompanyTableMap::buildTableMap();
