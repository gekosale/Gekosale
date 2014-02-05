<?php

namespace Gekosale\Plugin\Order\Model\ORM\Map;

use Gekosale\Plugin\Order\Model\ORM\Order;
use Gekosale\Plugin\Order\Model\ORM\OrderQuery;
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
 * This class defines the structure of the 'order' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class OrderTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Gekosale.Plugin.Order.Model.ORM.Map.OrderTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'order';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Gekosale\\Plugin\\Order\\Model\\ORM\\Order';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Gekosale.Plugin.Order.Model.ORM.Order';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 21;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 21;

    /**
     * the column name for the ID field
     */
    const COL_ID = 'order.ID';

    /**
     * the column name for the PRICE field
     */
    const COL_PRICE = 'order.PRICE';

    /**
     * the column name for the DISPATCH_METHOD_PRICE field
     */
    const COL_DISPATCH_METHOD_PRICE = 'order.DISPATCH_METHOD_PRICE';

    /**
     * the column name for the GLOBAL_PRICE field
     */
    const COL_GLOBAL_PRICE = 'order.GLOBAL_PRICE';

    /**
     * the column name for the ORDER_STATUS_ID field
     */
    const COL_ORDER_STATUS_ID = 'order.ORDER_STATUS_ID';

    /**
     * the column name for the DISPATCH_METHOD_NAME field
     */
    const COL_DISPATCH_METHOD_NAME = 'order.DISPATCH_METHOD_NAME';

    /**
     * the column name for the PAYMENT_METHOD_NAME field
     */
    const COL_PAYMENT_METHOD_NAME = 'order.PAYMENT_METHOD_NAME';

    /**
     * the column name for the GLOBAL_QTY field
     */
    const COL_GLOBAL_QTY = 'order.GLOBAL_QTY';

    /**
     * the column name for the DISPATCH_METHOD_ID field
     */
    const COL_DISPATCH_METHOD_ID = 'order.DISPATCH_METHOD_ID';

    /**
     * the column name for the PAYMENT_METHOD_ID field
     */
    const COL_PAYMENT_METHOD_ID = 'order.PAYMENT_METHOD_ID';

    /**
     * the column name for the CLIENT_ID field
     */
    const COL_CLIENT_ID = 'order.CLIENT_ID';

    /**
     * the column name for the GLOBAL_PRICE_NETTO field
     */
    const COL_GLOBAL_PRICE_NETTO = 'order.GLOBAL_PRICE_NETTO';

    /**
     * the column name for the ACTIVE_LINK field
     */
    const COL_ACTIVE_LINK = 'order.ACTIVE_LINK';

    /**
     * the column name for the COMMENT field
     */
    const COL_COMMENT = 'order.COMMENT';

    /**
     * the column name for the SHOP_ID field
     */
    const COL_SHOP_ID = 'order.SHOP_ID';

    /**
     * the column name for the PRICE_BEFORE_PROMOTION field
     */
    const COL_PRICE_BEFORE_PROMOTION = 'order.PRICE_BEFORE_PROMOTION';

    /**
     * the column name for the CURRENCY_ID field
     */
    const COL_CURRENCY_ID = 'order.CURRENCY_ID';

    /**
     * the column name for the CURRENCY_SYMBOL field
     */
    const COL_CURRENCY_SYMBOL = 'order.CURRENCY_SYMBOL';

    /**
     * the column name for the CURRENCY_RATE field
     */
    const COL_CURRENCY_RATE = 'order.CURRENCY_RATE';

    /**
     * the column name for the CART_RULE_ID field
     */
    const COL_CART_RULE_ID = 'order.CART_RULE_ID';

    /**
     * the column name for the SESSION_ID field
     */
    const COL_SESSION_ID = 'order.SESSION_ID';

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
        self::TYPE_PHPNAME       => array('Id', 'Price', 'DispatchMethodPrice', 'GlobalPrice', 'OrderStatusId', 'DispatchMethodName', 'PaymentMethodName', 'GlobalQty', 'DispatchMethodId', 'PaymentMethodId', 'ClientId', 'GlobalPriceNetto', 'ActiveLink', 'Comment', 'ShopId', 'PriceBeforePromotion', 'CurrencyId', 'CurrencySymbol', 'CurrencyRate', 'CartRuleId', 'SessionId', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'price', 'dispatchMethodPrice', 'globalPrice', 'orderStatusId', 'dispatchMethodName', 'paymentMethodName', 'globalQty', 'dispatchMethodId', 'paymentMethodId', 'clientId', 'globalPriceNetto', 'activeLink', 'comment', 'shopId', 'priceBeforePromotion', 'currencyId', 'currencySymbol', 'currencyRate', 'cartRuleId', 'sessionId', ),
        self::TYPE_COLNAME       => array(OrderTableMap::COL_ID, OrderTableMap::COL_PRICE, OrderTableMap::COL_DISPATCH_METHOD_PRICE, OrderTableMap::COL_GLOBAL_PRICE, OrderTableMap::COL_ORDER_STATUS_ID, OrderTableMap::COL_DISPATCH_METHOD_NAME, OrderTableMap::COL_PAYMENT_METHOD_NAME, OrderTableMap::COL_GLOBAL_QTY, OrderTableMap::COL_DISPATCH_METHOD_ID, OrderTableMap::COL_PAYMENT_METHOD_ID, OrderTableMap::COL_CLIENT_ID, OrderTableMap::COL_GLOBAL_PRICE_NETTO, OrderTableMap::COL_ACTIVE_LINK, OrderTableMap::COL_COMMENT, OrderTableMap::COL_SHOP_ID, OrderTableMap::COL_PRICE_BEFORE_PROMOTION, OrderTableMap::COL_CURRENCY_ID, OrderTableMap::COL_CURRENCY_SYMBOL, OrderTableMap::COL_CURRENCY_RATE, OrderTableMap::COL_CART_RULE_ID, OrderTableMap::COL_SESSION_ID, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID', 'COL_PRICE', 'COL_DISPATCH_METHOD_PRICE', 'COL_GLOBAL_PRICE', 'COL_ORDER_STATUS_ID', 'COL_DISPATCH_METHOD_NAME', 'COL_PAYMENT_METHOD_NAME', 'COL_GLOBAL_QTY', 'COL_DISPATCH_METHOD_ID', 'COL_PAYMENT_METHOD_ID', 'COL_CLIENT_ID', 'COL_GLOBAL_PRICE_NETTO', 'COL_ACTIVE_LINK', 'COL_COMMENT', 'COL_SHOP_ID', 'COL_PRICE_BEFORE_PROMOTION', 'COL_CURRENCY_ID', 'COL_CURRENCY_SYMBOL', 'COL_CURRENCY_RATE', 'COL_CART_RULE_ID', 'COL_SESSION_ID', ),
        self::TYPE_FIELDNAME     => array('id', 'price', 'dispatch_method_price', 'global_price', 'order_status_id', 'dispatch_method_name', 'payment_method_name', 'global_qty', 'dispatch_method_id', 'payment_method_id', 'client_id', 'global_price_netto', 'active_link', 'comment', 'shop_id', 'price_before_promotion', 'currency_id', 'currency_symbol', 'currency_rate', 'cart_rule_id', 'session_id', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Price' => 1, 'DispatchMethodPrice' => 2, 'GlobalPrice' => 3, 'OrderStatusId' => 4, 'DispatchMethodName' => 5, 'PaymentMethodName' => 6, 'GlobalQty' => 7, 'DispatchMethodId' => 8, 'PaymentMethodId' => 9, 'ClientId' => 10, 'GlobalPriceNetto' => 11, 'ActiveLink' => 12, 'Comment' => 13, 'ShopId' => 14, 'PriceBeforePromotion' => 15, 'CurrencyId' => 16, 'CurrencySymbol' => 17, 'CurrencyRate' => 18, 'CartRuleId' => 19, 'SessionId' => 20, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'price' => 1, 'dispatchMethodPrice' => 2, 'globalPrice' => 3, 'orderStatusId' => 4, 'dispatchMethodName' => 5, 'paymentMethodName' => 6, 'globalQty' => 7, 'dispatchMethodId' => 8, 'paymentMethodId' => 9, 'clientId' => 10, 'globalPriceNetto' => 11, 'activeLink' => 12, 'comment' => 13, 'shopId' => 14, 'priceBeforePromotion' => 15, 'currencyId' => 16, 'currencySymbol' => 17, 'currencyRate' => 18, 'cartRuleId' => 19, 'sessionId' => 20, ),
        self::TYPE_COLNAME       => array(OrderTableMap::COL_ID => 0, OrderTableMap::COL_PRICE => 1, OrderTableMap::COL_DISPATCH_METHOD_PRICE => 2, OrderTableMap::COL_GLOBAL_PRICE => 3, OrderTableMap::COL_ORDER_STATUS_ID => 4, OrderTableMap::COL_DISPATCH_METHOD_NAME => 5, OrderTableMap::COL_PAYMENT_METHOD_NAME => 6, OrderTableMap::COL_GLOBAL_QTY => 7, OrderTableMap::COL_DISPATCH_METHOD_ID => 8, OrderTableMap::COL_PAYMENT_METHOD_ID => 9, OrderTableMap::COL_CLIENT_ID => 10, OrderTableMap::COL_GLOBAL_PRICE_NETTO => 11, OrderTableMap::COL_ACTIVE_LINK => 12, OrderTableMap::COL_COMMENT => 13, OrderTableMap::COL_SHOP_ID => 14, OrderTableMap::COL_PRICE_BEFORE_PROMOTION => 15, OrderTableMap::COL_CURRENCY_ID => 16, OrderTableMap::COL_CURRENCY_SYMBOL => 17, OrderTableMap::COL_CURRENCY_RATE => 18, OrderTableMap::COL_CART_RULE_ID => 19, OrderTableMap::COL_SESSION_ID => 20, ),
        self::TYPE_RAW_COLNAME   => array('COL_ID' => 0, 'COL_PRICE' => 1, 'COL_DISPATCH_METHOD_PRICE' => 2, 'COL_GLOBAL_PRICE' => 3, 'COL_ORDER_STATUS_ID' => 4, 'COL_DISPATCH_METHOD_NAME' => 5, 'COL_PAYMENT_METHOD_NAME' => 6, 'COL_GLOBAL_QTY' => 7, 'COL_DISPATCH_METHOD_ID' => 8, 'COL_PAYMENT_METHOD_ID' => 9, 'COL_CLIENT_ID' => 10, 'COL_GLOBAL_PRICE_NETTO' => 11, 'COL_ACTIVE_LINK' => 12, 'COL_COMMENT' => 13, 'COL_SHOP_ID' => 14, 'COL_PRICE_BEFORE_PROMOTION' => 15, 'COL_CURRENCY_ID' => 16, 'COL_CURRENCY_SYMBOL' => 17, 'COL_CURRENCY_RATE' => 18, 'COL_CART_RULE_ID' => 19, 'COL_SESSION_ID' => 20, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'price' => 1, 'dispatch_method_price' => 2, 'global_price' => 3, 'order_status_id' => 4, 'dispatch_method_name' => 5, 'payment_method_name' => 6, 'global_qty' => 7, 'dispatch_method_id' => 8, 'payment_method_id' => 9, 'client_id' => 10, 'global_price_netto' => 11, 'active_link' => 12, 'comment' => 13, 'shop_id' => 14, 'price_before_promotion' => 15, 'currency_id' => 16, 'currency_symbol' => 17, 'currency_rate' => 18, 'cart_rule_id' => 19, 'session_id' => 20, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, )
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
        $this->setName('order');
        $this->setPhpName('Order');
        $this->setClassName('\\Gekosale\\Plugin\\Order\\Model\\ORM\\Order');
        $this->setPackage('Gekosale.Plugin.Order.Model.ORM');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addColumn('PRICE', 'Price', 'DECIMAL', true, 16, null);
        $this->addColumn('DISPATCH_METHOD_PRICE', 'DispatchMethodPrice', 'DECIMAL', true, 16, null);
        $this->addColumn('GLOBAL_PRICE', 'GlobalPrice', 'DECIMAL', true, 16, null);
        $this->addColumn('ORDER_STATUS_ID', 'OrderStatusId', 'INTEGER', true, 10, null);
        $this->addColumn('DISPATCH_METHOD_NAME', 'DispatchMethodName', 'VARCHAR', false, 64, null);
        $this->addColumn('PAYMENT_METHOD_NAME', 'PaymentMethodName', 'VARCHAR', false, 64, null);
        $this->addColumn('GLOBAL_QTY', 'GlobalQty', 'INTEGER', true, 10, null);
        $this->addColumn('DISPATCH_METHOD_ID', 'DispatchMethodId', 'INTEGER', true, 10, null);
        $this->addColumn('PAYMENT_METHOD_ID', 'PaymentMethodId', 'INTEGER', true, 10, null);
        $this->addColumn('CLIENT_ID', 'ClientId', 'INTEGER', false, 10, null);
        $this->addColumn('GLOBAL_PRICE_NETTO', 'GlobalPriceNetto', 'DECIMAL', true, 16, null);
        $this->addColumn('ACTIVE_LINK', 'ActiveLink', 'VARCHAR', false, 100, null);
        $this->addColumn('COMMENT', 'Comment', 'VARCHAR', false, 5000, null);
        $this->addForeignKey('SHOP_ID', 'ShopId', 'INTEGER', 'shop', 'ID', false, 10, null);
        $this->addColumn('PRICE_BEFORE_PROMOTION', 'PriceBeforePromotion', 'DECIMAL', false, null, null);
        $this->addColumn('CURRENCY_ID', 'CurrencyId', 'INTEGER', true, 10, null);
        $this->addColumn('CURRENCY_SYMBOL', 'CurrencySymbol', 'VARCHAR', true, 5, null);
        $this->addColumn('CURRENCY_RATE', 'CurrencyRate', 'DECIMAL', false, null, null);
        $this->addColumn('CART_RULE_ID', 'CartRuleId', 'INTEGER', false, 10, null);
        $this->addColumn('SESSION_ID', 'SessionId', 'VARCHAR', true, 255, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Shop', '\\Gekosale\\Plugin\\Shop\\Model\\ORM\\Shop', RelationMap::MANY_TO_ONE, array('shop_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('OrderClientData', '\\Gekosale\\Plugin\\Order\\Model\\ORM\\OrderClientData', RelationMap::ONE_TO_MANY, array('id' => 'order_id', ), 'CASCADE', null, 'OrderClientDatas');
        $this->addRelation('OrderClientDeliveryData', '\\Gekosale\\Plugin\\Order\\Model\\ORM\\OrderClientDeliveryData', RelationMap::ONE_TO_MANY, array('id' => 'order_id', ), 'CASCADE', null, 'OrderClientDeliveryDatas');
        $this->addRelation('OrderHistory', '\\Gekosale\\Plugin\\Order\\Model\\ORM\\OrderHistory', RelationMap::ONE_TO_MANY, array('id' => 'order_id', ), 'CASCADE', null, 'OrderHistories');
        $this->addRelation('OrderNotes', '\\Gekosale\\Plugin\\Order\\Model\\ORM\\OrderNotes', RelationMap::ONE_TO_MANY, array('id' => 'order_id', ), 'CASCADE', null, 'OrderNotess');
        $this->addRelation('OrderProduct', '\\Gekosale\\Plugin\\Order\\Model\\ORM\\OrderProduct', RelationMap::ONE_TO_MANY, array('id' => 'order_id', ), 'CASCADE', null, 'OrderProducts');
    } // buildRelations()
    /**
     * Method to invalidate the instance pool of all tables related to order     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in ".$this->getClassNameFromBuilder($joinedTableTableMapBuilder)." instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
                OrderClientDataTableMap::clearInstancePool();
                OrderClientDeliveryDataTableMap::clearInstancePool();
                OrderHistoryTableMap::clearInstancePool();
                OrderNotesTableMap::clearInstancePool();
                OrderProductTableMap::clearInstancePool();
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
        return $withPrefix ? OrderTableMap::CLASS_DEFAULT : OrderTableMap::OM_CLASS;
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
     * @return array (Order object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = OrderTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = OrderTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + OrderTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = OrderTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            OrderTableMap::addInstanceToPool($obj, $key);
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
            $key = OrderTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = OrderTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                OrderTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(OrderTableMap::COL_ID);
            $criteria->addSelectColumn(OrderTableMap::COL_PRICE);
            $criteria->addSelectColumn(OrderTableMap::COL_DISPATCH_METHOD_PRICE);
            $criteria->addSelectColumn(OrderTableMap::COL_GLOBAL_PRICE);
            $criteria->addSelectColumn(OrderTableMap::COL_ORDER_STATUS_ID);
            $criteria->addSelectColumn(OrderTableMap::COL_DISPATCH_METHOD_NAME);
            $criteria->addSelectColumn(OrderTableMap::COL_PAYMENT_METHOD_NAME);
            $criteria->addSelectColumn(OrderTableMap::COL_GLOBAL_QTY);
            $criteria->addSelectColumn(OrderTableMap::COL_DISPATCH_METHOD_ID);
            $criteria->addSelectColumn(OrderTableMap::COL_PAYMENT_METHOD_ID);
            $criteria->addSelectColumn(OrderTableMap::COL_CLIENT_ID);
            $criteria->addSelectColumn(OrderTableMap::COL_GLOBAL_PRICE_NETTO);
            $criteria->addSelectColumn(OrderTableMap::COL_ACTIVE_LINK);
            $criteria->addSelectColumn(OrderTableMap::COL_COMMENT);
            $criteria->addSelectColumn(OrderTableMap::COL_SHOP_ID);
            $criteria->addSelectColumn(OrderTableMap::COL_PRICE_BEFORE_PROMOTION);
            $criteria->addSelectColumn(OrderTableMap::COL_CURRENCY_ID);
            $criteria->addSelectColumn(OrderTableMap::COL_CURRENCY_SYMBOL);
            $criteria->addSelectColumn(OrderTableMap::COL_CURRENCY_RATE);
            $criteria->addSelectColumn(OrderTableMap::COL_CART_RULE_ID);
            $criteria->addSelectColumn(OrderTableMap::COL_SESSION_ID);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.PRICE');
            $criteria->addSelectColumn($alias . '.DISPATCH_METHOD_PRICE');
            $criteria->addSelectColumn($alias . '.GLOBAL_PRICE');
            $criteria->addSelectColumn($alias . '.ORDER_STATUS_ID');
            $criteria->addSelectColumn($alias . '.DISPATCH_METHOD_NAME');
            $criteria->addSelectColumn($alias . '.PAYMENT_METHOD_NAME');
            $criteria->addSelectColumn($alias . '.GLOBAL_QTY');
            $criteria->addSelectColumn($alias . '.DISPATCH_METHOD_ID');
            $criteria->addSelectColumn($alias . '.PAYMENT_METHOD_ID');
            $criteria->addSelectColumn($alias . '.CLIENT_ID');
            $criteria->addSelectColumn($alias . '.GLOBAL_PRICE_NETTO');
            $criteria->addSelectColumn($alias . '.ACTIVE_LINK');
            $criteria->addSelectColumn($alias . '.COMMENT');
            $criteria->addSelectColumn($alias . '.SHOP_ID');
            $criteria->addSelectColumn($alias . '.PRICE_BEFORE_PROMOTION');
            $criteria->addSelectColumn($alias . '.CURRENCY_ID');
            $criteria->addSelectColumn($alias . '.CURRENCY_SYMBOL');
            $criteria->addSelectColumn($alias . '.CURRENCY_RATE');
            $criteria->addSelectColumn($alias . '.CART_RULE_ID');
            $criteria->addSelectColumn($alias . '.SESSION_ID');
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
        return Propel::getServiceContainer()->getDatabaseMap(OrderTableMap::DATABASE_NAME)->getTable(OrderTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(OrderTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(OrderTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new OrderTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a Order or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Order object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(OrderTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Gekosale\Plugin\Order\Model\ORM\Order) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(OrderTableMap::DATABASE_NAME);
            $criteria->add(OrderTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = OrderQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { OrderTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { OrderTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the order table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return OrderQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Order or Criteria object.
     *
     * @param mixed               $criteria Criteria or Order object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(OrderTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Order object
        }

        if ($criteria->containsKey(OrderTableMap::COL_ID) && $criteria->keyContainsValue(OrderTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.OrderTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = OrderQuery::create()->mergeWith($criteria);

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

} // OrderTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
OrderTableMap::buildTableMap();
