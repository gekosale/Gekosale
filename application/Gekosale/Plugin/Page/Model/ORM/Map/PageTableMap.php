<?php

namespace Gekosale\Plugin\Page\Model\ORM\Map;

use Gekosale\Plugin\Page\Model\ORM\Page;
use Gekosale\Plugin\Page\Model\ORM\PageQuery;
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
 * This class defines the structure of the 'page' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class PageTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.Page.Model.ORM.Map.PageTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'page';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\Page\\Model\\ORM\\Page';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.Page.Model.ORM.Page';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 9;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 9;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'page.ID';

    /**
     * the column name for the PAGE_ID field
     */
    const COL_PAGE_ID = 'page.PAGE_ID';

    /**
     * the column name for the HIERARCHY field
     */
    const COL_HIERARCHY = 'page.HIERARCHY';

    /**
     * the column name for the FOOTER field
     */
    const COL_FOOTER = 'page.FOOTER';

    /**
     * the column name for the HEADER field
     */
    const COL_HEADER = 'page.HEADER';

    /**
     * the column name for the ALIAS field
     */
    const COL_ALIAS = 'page.ALIAS';

    /**
     * the column name for the REDIRECT field
     */
    const COL_REDIRECT = 'page.REDIRECT';

    /**
     * the column name for the REDIRECT_ROUTE field
     */
    const COL_REDIRECT_ROUTE = 'page.REDIRECT_ROUTE';

    /**
     * the column name for the REDIRECT_URL field
     */
    const COL_REDIRECT_URL = 'page.REDIRECT_URL';

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
        self::TYPE_PHPNAME       => array('Id', 'PageId', 'Hierarchy', 'Footer', 'Header', 'Alias', 'Redirect', 'RedirectRoute', 'RedirectUrl', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'pageId', 'hierarchy', 'footer', 'header', 'alias', 'redirect', 'redirectRoute', 'redirectUrl', ),
        self::TYPE_COLNAME       => array(PageTableMap::COL_ID, PageTableMap::COL_PAGE_ID, PageTableMap::COL_HIERARCHY, PageTableMap::COL_FOOTER, PageTableMap::COL_HEADER, PageTableMap::COL_ALIAS, PageTableMap::COL_REDIRECT, PageTableMap::COL_REDIRECT_ROUTE, PageTableMap::COL_REDIRECT_URL, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_PAGE_ID', 'COL_HIERARCHY', 'COL_FOOTER', 'COL_HEADER', 'COL_ALIAS', 'COL_REDIRECT', 'COL_REDIRECT_ROUTE', 'COL_REDIRECT_URL', ),
        self::TYPE_FIELDNAME     => array('id', 'page_id', 'hierarchy', 'footer', 'header', 'alias', 'redirect', 'redirect_route', 'redirect_url', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'PageId' => 1, 'Hierarchy' => 2, 'Footer' => 3, 'Header' => 4, 'Alias' => 5, 'Redirect' => 6, 'RedirectRoute' => 7, 'RedirectUrl' => 8, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'pageId' => 1, 'hierarchy' => 2, 'footer' => 3, 'header' => 4, 'alias' => 5, 'redirect' => 6, 'redirectRoute' => 7, 'redirectUrl' => 8, ),
        self::TYPE_COLNAME       => array(PageTableMap::COL_ID => 0, PageTableMap::COL_PAGE_ID => 1, PageTableMap::COL_HIERARCHY => 2, PageTableMap::COL_FOOTER => 3, PageTableMap::COL_HEADER => 4, PageTableMap::COL_ALIAS => 5, PageTableMap::COL_REDIRECT => 6, PageTableMap::COL_REDIRECT_ROUTE => 7, PageTableMap::COL_REDIRECT_URL => 8, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_PAGE_ID' => 1, 'COL_HIERARCHY' => 2, 'COL_FOOTER' => 3, 'COL_HEADER' => 4, 'COL_ALIAS' => 5, 'COL_REDIRECT' => 6, 'COL_REDIRECT_ROUTE' => 7, 'COL_REDIRECT_URL' => 8, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'page_id' => 1, 'hierarchy' => 2, 'footer' => 3, 'header' => 4, 'alias' => 5, 'redirect' => 6, 'redirect_route' => 7, 'redirect_url' => 8, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, )
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
        $this->setName('page');
        $this->setPhpName('Page');
        $this->setClassName('\\Gekosale\\Plugin\\Page\\Model\\ORM\\Page');
        $this->setPackage('Gekosale.Plugin.Page.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addForeignKey('PAGE_ID', 'PageId', 'INTEGER', 'page', 'ID', false, 10, null);
        $this->addColumn('HIERARCHY', 'Hierarchy', 'INTEGER', false, 10, 0);
        $this->addColumn('FOOTER', 'Footer', 'INTEGER', true, 10, 1);
        $this->addColumn('HEADER', 'Header', 'INTEGER', true, 10, 1);
        $this->addColumn('ALIAS', 'Alias', 'VARCHAR', false, 255, null);
        $this->addColumn('REDIRECT', 'Redirect', 'INTEGER', true, null, 0);
        $this->addColumn('REDIRECT_ROUTE', 'RedirectRoute', 'VARCHAR', false, 255, null);
        $this->addColumn('REDIRECT_URL', 'RedirectUrl', 'VARCHAR', false, 255, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('PageRelatedByPageId', '\\Gekosale\\Plugin\\Page\\Model\\ORM\\Page', RelationMap::MANY_TO_ONE, array('page_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('PageRelatedById', '\\Gekosale\\Plugin\\Page\\Model\\ORM\\Page', RelationMap::ONE_TO_MANY, array('id' => 'page_id', ), 'CASCADE', null, 'PagesRelatedById');
        $this->addRelation('PageShop', '\\Gekosale\\Plugin\\Page\\Model\\ORM\\PageShop', RelationMap::ONE_TO_MANY, array('id' => 'page_id', ), 'CASCADE', null, 'PageShops');
    } // buildRelations()
    /**
     * Method to invalidate the instance pool of all tables related to page     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in ".$this->getClassNameFromBuilder($joinedTableTableMapBuilder)." instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
                PageTableMap::clearInstancePool();
                PageShopTableMap::clearInstancePool();
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
        return $withPrefix ? PageTableMap::CLASS_DEFAULT : PageTableMap::OM_CLASS;
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
     * @return array (Page object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = PageTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = PageTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + PageTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = PageTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            PageTableMap::addInstanceToPool($obj, $key);
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
            $key = PageTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = PageTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                PageTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(PageTableMap::COL_ID);
            $criteria->addSelectColumn(PageTableMap::COL_PAGE_ID);
            $criteria->addSelectColumn(PageTableMap::COL_HIERARCHY);
            $criteria->addSelectColumn(PageTableMap::COL_FOOTER);
            $criteria->addSelectColumn(PageTableMap::COL_HEADER);
            $criteria->addSelectColumn(PageTableMap::COL_ALIAS);
            $criteria->addSelectColumn(PageTableMap::COL_REDIRECT);
            $criteria->addSelectColumn(PageTableMap::COL_REDIRECT_ROUTE);
            $criteria->addSelectColumn(PageTableMap::COL_REDIRECT_URL);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.PAGE_ID');
            $criteria->addSelectColumn($alias . '.HIERARCHY');
            $criteria->addSelectColumn($alias . '.FOOTER');
            $criteria->addSelectColumn($alias . '.HEADER');
            $criteria->addSelectColumn($alias . '.ALIAS');
            $criteria->addSelectColumn($alias . '.REDIRECT');
            $criteria->addSelectColumn($alias . '.REDIRECT_ROUTE');
            $criteria->addSelectColumn($alias . '.REDIRECT_URL');
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
        return Propel::getServiceContainer()->getDatabaseMap(PageTableMap::DATABASE_NAME)->getTable(PageTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(PageTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(PageTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new PageTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a Page or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Page object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(PageTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\Page\Model\ORM\Page) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(PageTableMap::DATABASE_NAME);
            $criteria->add(PageTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = PageQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { PageTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { PageTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the page table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return PageQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Page or Criteria object.
     *
     * @param mixed               $criteria Criteria or Page object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(PageTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Page object
        }

        if ($criteria->containsKey(PageTableMap::COL_ID) && $criteria->keyContainsValue(PageTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.PageTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = PageQuery::create()->mergeWith($criteria);

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

} // PageTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
PageTableMap::buildTableMap();
