<?php

namespace Gekosale\Plugin\Invoice\Model\ORM\Map;

use Gekosale\Plugin\Invoice\Model\ORM\Invoice;
use Gekosale\Plugin\Invoice\Model\ORM\InvoiceQuery;
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
 * This class defines the structure of the 'invoice' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class InvoiceTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.Invoice.Model.ORM.Map.InvoiceTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'invoice';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\Invoice\\Model\\ORM\\Invoice';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.Invoice.Model.ORM.Invoice';

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
    const COL_ID = 'invoice.ID';

    /**
     * the column name for the SYMBOL field
     */
    const COL_SYMBOL = 'invoice.SYMBOL';

    /**
     * the column name for the INVOICE_DATE field
     */
    const COL_INVOICE_DATE = 'invoice.INVOICE_DATE';

    /**
     * the column name for the SALES_DATE field
     */
    const COL_SALES_DATE = 'invoice.SALES_DATE';

    /**
     * the column name for the PAYMENT_DUE_DATE field
     */
    const COL_PAYMENT_DUE_DATE = 'invoice.PAYMENT_DUE_DATE';

    /**
     * the column name for the SALES_PERSON field
     */
    const COL_SALES_PERSON = 'invoice.SALES_PERSON';

    /**
     * the column name for the INVOICE_TYPE field
     */
    const COL_INVOICE_TYPE = 'invoice.INVOICE_TYPE';

    /**
     * the column name for the COMMENT field
     */
    const COL_COMMENT = 'invoice.COMMENT';

    /**
     * the column name for the CONTENT_ORIGINAL field
     */
    const COL_CONTENT_ORIGINAL = 'invoice.CONTENT_ORIGINAL';

    /**
     * the column name for the CONTENT_COPY field
     */
    const COL_CONTENT_COPY = 'invoice.CONTENT_COPY';

    /**
     * the column name for the ORDER_ID field
     */
    const COL_ORDER_ID = 'invoice.ORDER_ID';

    /**
     * the column name for the TOTAL_PAYED field
     */
    const COL_TOTAL_PAYED = 'invoice.TOTAL_PAYED';

    /**
     * the column name for the SHOP_ID field
     */
    const COL_SHOP_ID = 'invoice.SHOP_ID';

    /**
     * the column name for the EXTERNAL_ID field
     */
    const COL_EXTERNAL_ID = 'invoice.EXTERNAL_ID';

    /**
     * the column name for the CONTENT_TYPE field
     */
    const COL_CONTENT_TYPE = 'invoice.CONTENT_TYPE';

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
        self::TYPE_PHPNAME       => array('Id', 'Symbol', 'InvoiceDate', 'SalesDate', 'PaymentDueDate', 'SalesPerson', 'InvoiceType', 'Comment', 'ContentOriginal', 'ContentCopy', 'OrderId', 'TotalPayed', 'ShopId', 'ExternalId', 'ContentType', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'symbol', 'invoiceDate', 'salesDate', 'paymentDueDate', 'salesPerson', 'invoiceType', 'comment', 'contentOriginal', 'contentCopy', 'orderId', 'totalPayed', 'shopId', 'externalId', 'contentType', ),
        self::TYPE_COLNAME       => array(InvoiceTableMap::COL_ID, InvoiceTableMap::COL_SYMBOL, InvoiceTableMap::COL_INVOICE_DATE, InvoiceTableMap::COL_SALES_DATE, InvoiceTableMap::COL_PAYMENT_DUE_DATE, InvoiceTableMap::COL_SALES_PERSON, InvoiceTableMap::COL_INVOICE_TYPE, InvoiceTableMap::COL_COMMENT, InvoiceTableMap::COL_CONTENT_ORIGINAL, InvoiceTableMap::COL_CONTENT_COPY, InvoiceTableMap::COL_ORDER_ID, InvoiceTableMap::COL_TOTAL_PAYED, InvoiceTableMap::COL_SHOP_ID, InvoiceTableMap::COL_EXTERNAL_ID, InvoiceTableMap::COL_CONTENT_TYPE, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_SYMBOL', 'COL_INVOICE_DATE', 'COL_SALES_DATE', 'COL_PAYMENT_DUE_DATE', 'COL_SALES_PERSON', 'COL_INVOICE_TYPE', 'COL_COMMENT', 'COL_CONTENT_ORIGINAL', 'COL_CONTENT_COPY', 'COL_ORDER_ID', 'COL_TOTAL_PAYED', 'COL_SHOP_ID', 'COL_EXTERNAL_ID', 'COL_CONTENT_TYPE', ),
        self::TYPE_FIELDNAME     => array('id', 'symbol', 'invoice_date', 'sales_date', 'payment_due_date', 'sales_person', 'invoice_type', 'comment', 'content_original', 'content_copy', 'order_id', 'total_payed', 'shop_id', 'external_id', 'content_type', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Symbol' => 1, 'InvoiceDate' => 2, 'SalesDate' => 3, 'PaymentDueDate' => 4, 'SalesPerson' => 5, 'InvoiceType' => 6, 'Comment' => 7, 'ContentOriginal' => 8, 'ContentCopy' => 9, 'OrderId' => 10, 'TotalPayed' => 11, 'ShopId' => 12, 'ExternalId' => 13, 'ContentType' => 14, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'symbol' => 1, 'invoiceDate' => 2, 'salesDate' => 3, 'paymentDueDate' => 4, 'salesPerson' => 5, 'invoiceType' => 6, 'comment' => 7, 'contentOriginal' => 8, 'contentCopy' => 9, 'orderId' => 10, 'totalPayed' => 11, 'shopId' => 12, 'externalId' => 13, 'contentType' => 14, ),
        self::TYPE_COLNAME       => array(InvoiceTableMap::COL_ID => 0, InvoiceTableMap::COL_SYMBOL => 1, InvoiceTableMap::COL_INVOICE_DATE => 2, InvoiceTableMap::COL_SALES_DATE => 3, InvoiceTableMap::COL_PAYMENT_DUE_DATE => 4, InvoiceTableMap::COL_SALES_PERSON => 5, InvoiceTableMap::COL_INVOICE_TYPE => 6, InvoiceTableMap::COL_COMMENT => 7, InvoiceTableMap::COL_CONTENT_ORIGINAL => 8, InvoiceTableMap::COL_CONTENT_COPY => 9, InvoiceTableMap::COL_ORDER_ID => 10, InvoiceTableMap::COL_TOTAL_PAYED => 11, InvoiceTableMap::COL_SHOP_ID => 12, InvoiceTableMap::COL_EXTERNAL_ID => 13, InvoiceTableMap::COL_CONTENT_TYPE => 14, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_SYMBOL' => 1, 'COL_INVOICE_DATE' => 2, 'COL_SALES_DATE' => 3, 'COL_PAYMENT_DUE_DATE' => 4, 'COL_SALES_PERSON' => 5, 'COL_INVOICE_TYPE' => 6, 'COL_COMMENT' => 7, 'COL_CONTENT_ORIGINAL' => 8, 'COL_CONTENT_COPY' => 9, 'COL_ORDER_ID' => 10, 'COL_TOTAL_PAYED' => 11, 'COL_SHOP_ID' => 12, 'COL_EXTERNAL_ID' => 13, 'COL_CONTENT_TYPE' => 14, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'symbol' => 1, 'invoice_date' => 2, 'sales_date' => 3, 'payment_due_date' => 4, 'sales_person' => 5, 'invoice_type' => 6, 'comment' => 7, 'content_original' => 8, 'content_copy' => 9, 'order_id' => 10, 'total_payed' => 11, 'shop_id' => 12, 'external_id' => 13, 'content_type' => 14, ),
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
        $this->setName('invoice');
        $this->setPhpName('Invoice');
        $this->setClassName('\\Gekosale\\Plugin\\Invoice\\Model\\ORM\\Invoice');
        $this->setPackage('Gekosale.Plugin.Invoice.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('SYMBOL', 'Symbol', 'VARCHAR', true, 128, null);
        $this->addColumn('INVOICE_DATE', 'InvoiceDate', 'DATE', true, null, null);
        $this->addColumn('SALES_DATE', 'SalesDate', 'DATE', true, null, null);
        $this->addColumn('PAYMENT_DUE_DATE', 'PaymentDueDate', 'DATE', true, null, null);
        $this->addColumn('SALES_PERSON', 'SalesPerson', 'VARCHAR', true, 128, null);
        $this->addColumn('INVOICE_TYPE', 'InvoiceType', 'INTEGER', true, null, null);
        $this->addColumn('COMMENT', 'Comment', 'LONGVARCHAR', false, null, null);
        $this->addColumn('CONTENT_ORIGINAL', 'ContentOriginal', 'BLOB', true, null, null);
        $this->addColumn('CONTENT_COPY', 'ContentCopy', 'BLOB', true, null, null);
        $this->addColumn('ORDER_ID', 'OrderId', 'INTEGER', true, null, null);
        $this->addColumn('TOTAL_PAYED', 'TotalPayed', 'DECIMAL', true, 15, 0);
        $this->addColumn('SHOP_ID', 'ShopId', 'INTEGER', false, null, null);
        $this->addColumn('EXTERNAL_ID', 'ExternalId', 'INTEGER', false, null, null);
        $this->addColumn('CONTENT_TYPE', 'ContentType', 'VARCHAR', false, 5, 'html');
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
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
        return $withPrefix ? InvoiceTableMap::CLASS_DEFAULT : InvoiceTableMap::OM_CLASS;
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
     * @return array (Invoice object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = InvoiceTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = InvoiceTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + InvoiceTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = InvoiceTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            InvoiceTableMap::addInstanceToPool($obj, $key);
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
            $key = InvoiceTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = InvoiceTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                InvoiceTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(InvoiceTableMap::COL_ID);
            $criteria->addSelectColumn(InvoiceTableMap::COL_SYMBOL);
            $criteria->addSelectColumn(InvoiceTableMap::COL_INVOICE_DATE);
            $criteria->addSelectColumn(InvoiceTableMap::COL_SALES_DATE);
            $criteria->addSelectColumn(InvoiceTableMap::COL_PAYMENT_DUE_DATE);
            $criteria->addSelectColumn(InvoiceTableMap::COL_SALES_PERSON);
            $criteria->addSelectColumn(InvoiceTableMap::COL_INVOICE_TYPE);
            $criteria->addSelectColumn(InvoiceTableMap::COL_COMMENT);
            $criteria->addSelectColumn(InvoiceTableMap::COL_CONTENT_ORIGINAL);
            $criteria->addSelectColumn(InvoiceTableMap::COL_CONTENT_COPY);
            $criteria->addSelectColumn(InvoiceTableMap::COL_ORDER_ID);
            $criteria->addSelectColumn(InvoiceTableMap::COL_TOTAL_PAYED);
            $criteria->addSelectColumn(InvoiceTableMap::COL_SHOP_ID);
            $criteria->addSelectColumn(InvoiceTableMap::COL_EXTERNAL_ID);
            $criteria->addSelectColumn(InvoiceTableMap::COL_CONTENT_TYPE);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.SYMBOL');
            $criteria->addSelectColumn($alias . '.INVOICE_DATE');
            $criteria->addSelectColumn($alias . '.SALES_DATE');
            $criteria->addSelectColumn($alias . '.PAYMENT_DUE_DATE');
            $criteria->addSelectColumn($alias . '.SALES_PERSON');
            $criteria->addSelectColumn($alias . '.INVOICE_TYPE');
            $criteria->addSelectColumn($alias . '.COMMENT');
            $criteria->addSelectColumn($alias . '.CONTENT_ORIGINAL');
            $criteria->addSelectColumn($alias . '.CONTENT_COPY');
            $criteria->addSelectColumn($alias . '.ORDER_ID');
            $criteria->addSelectColumn($alias . '.TOTAL_PAYED');
            $criteria->addSelectColumn($alias . '.SHOP_ID');
            $criteria->addSelectColumn($alias . '.EXTERNAL_ID');
            $criteria->addSelectColumn($alias . '.CONTENT_TYPE');
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
        return Propel::getServiceContainer()->getDatabaseMap(InvoiceTableMap::DATABASE_NAME)->getTable(InvoiceTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(InvoiceTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(InvoiceTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new InvoiceTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a Invoice or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Invoice object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(InvoiceTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\Invoice\Model\ORM\Invoice) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(InvoiceTableMap::DATABASE_NAME);
            $criteria->add(InvoiceTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = InvoiceQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { InvoiceTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { InvoiceTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the invoice table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return InvoiceQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Invoice or Criteria object.
     *
     * @param mixed               $criteria Criteria or Invoice object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(InvoiceTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Invoice object
        }

        if ($criteria->containsKey(InvoiceTableMap::COL_ID) && $criteria->keyContainsValue(InvoiceTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.InvoiceTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = InvoiceQuery::create()->mergeWith($criteria);

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

} // InvoiceTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
InvoiceTableMap::buildTableMap();
