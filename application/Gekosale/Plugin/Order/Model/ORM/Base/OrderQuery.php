<?php

namespace Gekosale\Plugin\Order\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Order\Model\ORM\Order as ChildOrder;
use Gekosale\Plugin\Order\Model\ORM\OrderQuery as ChildOrderQuery;
use Gekosale\Plugin\Order\Model\ORM\Map\OrderTableMap;
use Gekosale\Plugin\Shop\Model\ORM\Shop;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'order' table.
 *
 * 
 *
 * @method     ChildOrderQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildOrderQuery orderByPrice($order = Criteria::ASC) Order by the price column
 * @method     ChildOrderQuery orderByDispatchMethodPrice($order = Criteria::ASC) Order by the dispatch_method_price column
 * @method     ChildOrderQuery orderByGlobalPrice($order = Criteria::ASC) Order by the global_price column
 * @method     ChildOrderQuery orderByOrderStatusId($order = Criteria::ASC) Order by the order_status_id column
 * @method     ChildOrderQuery orderByDispatchMethodName($order = Criteria::ASC) Order by the dispatch_method_name column
 * @method     ChildOrderQuery orderByPaymentMethodName($order = Criteria::ASC) Order by the payment_method_name column
 * @method     ChildOrderQuery orderByGlobalQty($order = Criteria::ASC) Order by the global_qty column
 * @method     ChildOrderQuery orderByDispatchMethodId($order = Criteria::ASC) Order by the dispatch_method_id column
 * @method     ChildOrderQuery orderByPaymentMethodId($order = Criteria::ASC) Order by the payment_method_id column
 * @method     ChildOrderQuery orderByClientId($order = Criteria::ASC) Order by the client_id column
 * @method     ChildOrderQuery orderByGlobalPriceNetto($order = Criteria::ASC) Order by the global_price_netto column
 * @method     ChildOrderQuery orderByActiveLink($order = Criteria::ASC) Order by the active_link column
 * @method     ChildOrderQuery orderByComment($order = Criteria::ASC) Order by the comment column
 * @method     ChildOrderQuery orderByShopId($order = Criteria::ASC) Order by the shop_id column
 * @method     ChildOrderQuery orderByPriceBeforePromotion($order = Criteria::ASC) Order by the price_before_promotion column
 * @method     ChildOrderQuery orderByCurrencyId($order = Criteria::ASC) Order by the currency_id column
 * @method     ChildOrderQuery orderByCurrencySymbol($order = Criteria::ASC) Order by the currency_symbol column
 * @method     ChildOrderQuery orderByCurrencyRate($order = Criteria::ASC) Order by the currency_rate column
 * @method     ChildOrderQuery orderByCartRuleId($order = Criteria::ASC) Order by the cart_rule_id column
 * @method     ChildOrderQuery orderBySessionId($order = Criteria::ASC) Order by the session_id column
 *
 * @method     ChildOrderQuery groupById() Group by the id column
 * @method     ChildOrderQuery groupByPrice() Group by the price column
 * @method     ChildOrderQuery groupByDispatchMethodPrice() Group by the dispatch_method_price column
 * @method     ChildOrderQuery groupByGlobalPrice() Group by the global_price column
 * @method     ChildOrderQuery groupByOrderStatusId() Group by the order_status_id column
 * @method     ChildOrderQuery groupByDispatchMethodName() Group by the dispatch_method_name column
 * @method     ChildOrderQuery groupByPaymentMethodName() Group by the payment_method_name column
 * @method     ChildOrderQuery groupByGlobalQty() Group by the global_qty column
 * @method     ChildOrderQuery groupByDispatchMethodId() Group by the dispatch_method_id column
 * @method     ChildOrderQuery groupByPaymentMethodId() Group by the payment_method_id column
 * @method     ChildOrderQuery groupByClientId() Group by the client_id column
 * @method     ChildOrderQuery groupByGlobalPriceNetto() Group by the global_price_netto column
 * @method     ChildOrderQuery groupByActiveLink() Group by the active_link column
 * @method     ChildOrderQuery groupByComment() Group by the comment column
 * @method     ChildOrderQuery groupByShopId() Group by the shop_id column
 * @method     ChildOrderQuery groupByPriceBeforePromotion() Group by the price_before_promotion column
 * @method     ChildOrderQuery groupByCurrencyId() Group by the currency_id column
 * @method     ChildOrderQuery groupByCurrencySymbol() Group by the currency_symbol column
 * @method     ChildOrderQuery groupByCurrencyRate() Group by the currency_rate column
 * @method     ChildOrderQuery groupByCartRuleId() Group by the cart_rule_id column
 * @method     ChildOrderQuery groupBySessionId() Group by the session_id column
 *
 * @method     ChildOrderQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildOrderQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildOrderQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildOrderQuery leftJoinShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the Shop relation
 * @method     ChildOrderQuery rightJoinShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Shop relation
 * @method     ChildOrderQuery innerJoinShop($relationAlias = null) Adds a INNER JOIN clause to the query using the Shop relation
 *
 * @method     ChildOrderQuery leftJoinOrderClientData($relationAlias = null) Adds a LEFT JOIN clause to the query using the OrderClientData relation
 * @method     ChildOrderQuery rightJoinOrderClientData($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OrderClientData relation
 * @method     ChildOrderQuery innerJoinOrderClientData($relationAlias = null) Adds a INNER JOIN clause to the query using the OrderClientData relation
 *
 * @method     ChildOrderQuery leftJoinOrderClientDeliveryData($relationAlias = null) Adds a LEFT JOIN clause to the query using the OrderClientDeliveryData relation
 * @method     ChildOrderQuery rightJoinOrderClientDeliveryData($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OrderClientDeliveryData relation
 * @method     ChildOrderQuery innerJoinOrderClientDeliveryData($relationAlias = null) Adds a INNER JOIN clause to the query using the OrderClientDeliveryData relation
 *
 * @method     ChildOrderQuery leftJoinOrderHistory($relationAlias = null) Adds a LEFT JOIN clause to the query using the OrderHistory relation
 * @method     ChildOrderQuery rightJoinOrderHistory($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OrderHistory relation
 * @method     ChildOrderQuery innerJoinOrderHistory($relationAlias = null) Adds a INNER JOIN clause to the query using the OrderHistory relation
 *
 * @method     ChildOrderQuery leftJoinOrderNotes($relationAlias = null) Adds a LEFT JOIN clause to the query using the OrderNotes relation
 * @method     ChildOrderQuery rightJoinOrderNotes($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OrderNotes relation
 * @method     ChildOrderQuery innerJoinOrderNotes($relationAlias = null) Adds a INNER JOIN clause to the query using the OrderNotes relation
 *
 * @method     ChildOrderQuery leftJoinOrderProduct($relationAlias = null) Adds a LEFT JOIN clause to the query using the OrderProduct relation
 * @method     ChildOrderQuery rightJoinOrderProduct($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OrderProduct relation
 * @method     ChildOrderQuery innerJoinOrderProduct($relationAlias = null) Adds a INNER JOIN clause to the query using the OrderProduct relation
 *
 * @method     ChildOrder findOne(ConnectionInterface $con = null) Return the first ChildOrder matching the query
 * @method     ChildOrder findOneOrCreate(ConnectionInterface $con = null) Return the first ChildOrder matching the query, or a new ChildOrder object populated from the query conditions when no match is found
 *
 * @method     ChildOrder findOneById(int $id) Return the first ChildOrder filtered by the id column
 * @method     ChildOrder findOneByPrice(string $price) Return the first ChildOrder filtered by the price column
 * @method     ChildOrder findOneByDispatchMethodPrice(string $dispatch_method_price) Return the first ChildOrder filtered by the dispatch_method_price column
 * @method     ChildOrder findOneByGlobalPrice(string $global_price) Return the first ChildOrder filtered by the global_price column
 * @method     ChildOrder findOneByOrderStatusId(int $order_status_id) Return the first ChildOrder filtered by the order_status_id column
 * @method     ChildOrder findOneByDispatchMethodName(string $dispatch_method_name) Return the first ChildOrder filtered by the dispatch_method_name column
 * @method     ChildOrder findOneByPaymentMethodName(string $payment_method_name) Return the first ChildOrder filtered by the payment_method_name column
 * @method     ChildOrder findOneByGlobalQty(int $global_qty) Return the first ChildOrder filtered by the global_qty column
 * @method     ChildOrder findOneByDispatchMethodId(int $dispatch_method_id) Return the first ChildOrder filtered by the dispatch_method_id column
 * @method     ChildOrder findOneByPaymentMethodId(int $payment_method_id) Return the first ChildOrder filtered by the payment_method_id column
 * @method     ChildOrder findOneByClientId(int $client_id) Return the first ChildOrder filtered by the client_id column
 * @method     ChildOrder findOneByGlobalPriceNetto(string $global_price_netto) Return the first ChildOrder filtered by the global_price_netto column
 * @method     ChildOrder findOneByActiveLink(string $active_link) Return the first ChildOrder filtered by the active_link column
 * @method     ChildOrder findOneByComment(string $comment) Return the first ChildOrder filtered by the comment column
 * @method     ChildOrder findOneByShopId(int $shop_id) Return the first ChildOrder filtered by the shop_id column
 * @method     ChildOrder findOneByPriceBeforePromotion(string $price_before_promotion) Return the first ChildOrder filtered by the price_before_promotion column
 * @method     ChildOrder findOneByCurrencyId(int $currency_id) Return the first ChildOrder filtered by the currency_id column
 * @method     ChildOrder findOneByCurrencySymbol(string $currency_symbol) Return the first ChildOrder filtered by the currency_symbol column
 * @method     ChildOrder findOneByCurrencyRate(string $currency_rate) Return the first ChildOrder filtered by the currency_rate column
 * @method     ChildOrder findOneByCartRuleId(int $cart_rule_id) Return the first ChildOrder filtered by the cart_rule_id column
 * @method     ChildOrder findOneBySessionId(string $session_id) Return the first ChildOrder filtered by the session_id column
 *
 * @method     array findById(int $id) Return ChildOrder objects filtered by the id column
 * @method     array findByPrice(string $price) Return ChildOrder objects filtered by the price column
 * @method     array findByDispatchMethodPrice(string $dispatch_method_price) Return ChildOrder objects filtered by the dispatch_method_price column
 * @method     array findByGlobalPrice(string $global_price) Return ChildOrder objects filtered by the global_price column
 * @method     array findByOrderStatusId(int $order_status_id) Return ChildOrder objects filtered by the order_status_id column
 * @method     array findByDispatchMethodName(string $dispatch_method_name) Return ChildOrder objects filtered by the dispatch_method_name column
 * @method     array findByPaymentMethodName(string $payment_method_name) Return ChildOrder objects filtered by the payment_method_name column
 * @method     array findByGlobalQty(int $global_qty) Return ChildOrder objects filtered by the global_qty column
 * @method     array findByDispatchMethodId(int $dispatch_method_id) Return ChildOrder objects filtered by the dispatch_method_id column
 * @method     array findByPaymentMethodId(int $payment_method_id) Return ChildOrder objects filtered by the payment_method_id column
 * @method     array findByClientId(int $client_id) Return ChildOrder objects filtered by the client_id column
 * @method     array findByGlobalPriceNetto(string $global_price_netto) Return ChildOrder objects filtered by the global_price_netto column
 * @method     array findByActiveLink(string $active_link) Return ChildOrder objects filtered by the active_link column
 * @method     array findByComment(string $comment) Return ChildOrder objects filtered by the comment column
 * @method     array findByShopId(int $shop_id) Return ChildOrder objects filtered by the shop_id column
 * @method     array findByPriceBeforePromotion(string $price_before_promotion) Return ChildOrder objects filtered by the price_before_promotion column
 * @method     array findByCurrencyId(int $currency_id) Return ChildOrder objects filtered by the currency_id column
 * @method     array findByCurrencySymbol(string $currency_symbol) Return ChildOrder objects filtered by the currency_symbol column
 * @method     array findByCurrencyRate(string $currency_rate) Return ChildOrder objects filtered by the currency_rate column
 * @method     array findByCartRuleId(int $cart_rule_id) Return ChildOrder objects filtered by the cart_rule_id column
 * @method     array findBySessionId(string $session_id) Return ChildOrder objects filtered by the session_id column
 *
 */
abstract class OrderQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Order\Model\ORM\Base\OrderQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Order\\Model\\ORM\\Order', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildOrderQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildOrderQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Order\Model\ORM\OrderQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Order\Model\ORM\OrderQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildOrder|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = OrderTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(OrderTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return   ChildOrder A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, PRICE, DISPATCH_METHOD_PRICE, GLOBAL_PRICE, ORDER_STATUS_ID, DISPATCH_METHOD_NAME, PAYMENT_METHOD_NAME, GLOBAL_QTY, DISPATCH_METHOD_ID, PAYMENT_METHOD_ID, CLIENT_ID, GLOBAL_PRICE_NETTO, ACTIVE_LINK, COMMENT, SHOP_ID, PRICE_BEFORE_PROMOTION, CURRENCY_ID, CURRENCY_SYMBOL, CURRENCY_RATE, CART_RULE_ID, SESSION_ID FROM order WHERE ID = :p0';
        try {
            $stmt = $con->prepare($sql);            
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildOrder();
            $obj->hydrate($row);
            OrderTableMap::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildOrder|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(OrderTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(OrderTableMap::COL_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(OrderTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(OrderTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the price column
     *
     * Example usage:
     * <code>
     * $query->filterByPrice(1234); // WHERE price = 1234
     * $query->filterByPrice(array(12, 34)); // WHERE price IN (12, 34)
     * $query->filterByPrice(array('min' => 12)); // WHERE price > 12
     * </code>
     *
     * @param     mixed $price The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByPrice($price = null, $comparison = null)
    {
        if (is_array($price)) {
            $useMinMax = false;
            if (isset($price['min'])) {
                $this->addUsingAlias(OrderTableMap::COL_PRICE, $price['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($price['max'])) {
                $this->addUsingAlias(OrderTableMap::COL_PRICE, $price['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderTableMap::COL_PRICE, $price, $comparison);
    }

    /**
     * Filter the query on the dispatch_method_price column
     *
     * Example usage:
     * <code>
     * $query->filterByDispatchMethodPrice(1234); // WHERE dispatch_method_price = 1234
     * $query->filterByDispatchMethodPrice(array(12, 34)); // WHERE dispatch_method_price IN (12, 34)
     * $query->filterByDispatchMethodPrice(array('min' => 12)); // WHERE dispatch_method_price > 12
     * </code>
     *
     * @param     mixed $dispatchMethodPrice The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByDispatchMethodPrice($dispatchMethodPrice = null, $comparison = null)
    {
        if (is_array($dispatchMethodPrice)) {
            $useMinMax = false;
            if (isset($dispatchMethodPrice['min'])) {
                $this->addUsingAlias(OrderTableMap::COL_DISPATCH_METHOD_PRICE, $dispatchMethodPrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dispatchMethodPrice['max'])) {
                $this->addUsingAlias(OrderTableMap::COL_DISPATCH_METHOD_PRICE, $dispatchMethodPrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderTableMap::COL_DISPATCH_METHOD_PRICE, $dispatchMethodPrice, $comparison);
    }

    /**
     * Filter the query on the global_price column
     *
     * Example usage:
     * <code>
     * $query->filterByGlobalPrice(1234); // WHERE global_price = 1234
     * $query->filterByGlobalPrice(array(12, 34)); // WHERE global_price IN (12, 34)
     * $query->filterByGlobalPrice(array('min' => 12)); // WHERE global_price > 12
     * </code>
     *
     * @param     mixed $globalPrice The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByGlobalPrice($globalPrice = null, $comparison = null)
    {
        if (is_array($globalPrice)) {
            $useMinMax = false;
            if (isset($globalPrice['min'])) {
                $this->addUsingAlias(OrderTableMap::COL_GLOBAL_PRICE, $globalPrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($globalPrice['max'])) {
                $this->addUsingAlias(OrderTableMap::COL_GLOBAL_PRICE, $globalPrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderTableMap::COL_GLOBAL_PRICE, $globalPrice, $comparison);
    }

    /**
     * Filter the query on the order_status_id column
     *
     * Example usage:
     * <code>
     * $query->filterByOrderStatusId(1234); // WHERE order_status_id = 1234
     * $query->filterByOrderStatusId(array(12, 34)); // WHERE order_status_id IN (12, 34)
     * $query->filterByOrderStatusId(array('min' => 12)); // WHERE order_status_id > 12
     * </code>
     *
     * @param     mixed $orderStatusId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByOrderStatusId($orderStatusId = null, $comparison = null)
    {
        if (is_array($orderStatusId)) {
            $useMinMax = false;
            if (isset($orderStatusId['min'])) {
                $this->addUsingAlias(OrderTableMap::COL_ORDER_STATUS_ID, $orderStatusId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($orderStatusId['max'])) {
                $this->addUsingAlias(OrderTableMap::COL_ORDER_STATUS_ID, $orderStatusId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderTableMap::COL_ORDER_STATUS_ID, $orderStatusId, $comparison);
    }

    /**
     * Filter the query on the dispatch_method_name column
     *
     * Example usage:
     * <code>
     * $query->filterByDispatchMethodName('fooValue');   // WHERE dispatch_method_name = 'fooValue'
     * $query->filterByDispatchMethodName('%fooValue%'); // WHERE dispatch_method_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $dispatchMethodName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByDispatchMethodName($dispatchMethodName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($dispatchMethodName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $dispatchMethodName)) {
                $dispatchMethodName = str_replace('*', '%', $dispatchMethodName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrderTableMap::COL_DISPATCH_METHOD_NAME, $dispatchMethodName, $comparison);
    }

    /**
     * Filter the query on the payment_method_name column
     *
     * Example usage:
     * <code>
     * $query->filterByPaymentMethodName('fooValue');   // WHERE payment_method_name = 'fooValue'
     * $query->filterByPaymentMethodName('%fooValue%'); // WHERE payment_method_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $paymentMethodName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByPaymentMethodName($paymentMethodName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($paymentMethodName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $paymentMethodName)) {
                $paymentMethodName = str_replace('*', '%', $paymentMethodName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrderTableMap::COL_PAYMENT_METHOD_NAME, $paymentMethodName, $comparison);
    }

    /**
     * Filter the query on the global_qty column
     *
     * Example usage:
     * <code>
     * $query->filterByGlobalQty(1234); // WHERE global_qty = 1234
     * $query->filterByGlobalQty(array(12, 34)); // WHERE global_qty IN (12, 34)
     * $query->filterByGlobalQty(array('min' => 12)); // WHERE global_qty > 12
     * </code>
     *
     * @param     mixed $globalQty The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByGlobalQty($globalQty = null, $comparison = null)
    {
        if (is_array($globalQty)) {
            $useMinMax = false;
            if (isset($globalQty['min'])) {
                $this->addUsingAlias(OrderTableMap::COL_GLOBAL_QTY, $globalQty['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($globalQty['max'])) {
                $this->addUsingAlias(OrderTableMap::COL_GLOBAL_QTY, $globalQty['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderTableMap::COL_GLOBAL_QTY, $globalQty, $comparison);
    }

    /**
     * Filter the query on the dispatch_method_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDispatchMethodId(1234); // WHERE dispatch_method_id = 1234
     * $query->filterByDispatchMethodId(array(12, 34)); // WHERE dispatch_method_id IN (12, 34)
     * $query->filterByDispatchMethodId(array('min' => 12)); // WHERE dispatch_method_id > 12
     * </code>
     *
     * @param     mixed $dispatchMethodId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByDispatchMethodId($dispatchMethodId = null, $comparison = null)
    {
        if (is_array($dispatchMethodId)) {
            $useMinMax = false;
            if (isset($dispatchMethodId['min'])) {
                $this->addUsingAlias(OrderTableMap::COL_DISPATCH_METHOD_ID, $dispatchMethodId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dispatchMethodId['max'])) {
                $this->addUsingAlias(OrderTableMap::COL_DISPATCH_METHOD_ID, $dispatchMethodId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderTableMap::COL_DISPATCH_METHOD_ID, $dispatchMethodId, $comparison);
    }

    /**
     * Filter the query on the payment_method_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPaymentMethodId(1234); // WHERE payment_method_id = 1234
     * $query->filterByPaymentMethodId(array(12, 34)); // WHERE payment_method_id IN (12, 34)
     * $query->filterByPaymentMethodId(array('min' => 12)); // WHERE payment_method_id > 12
     * </code>
     *
     * @param     mixed $paymentMethodId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByPaymentMethodId($paymentMethodId = null, $comparison = null)
    {
        if (is_array($paymentMethodId)) {
            $useMinMax = false;
            if (isset($paymentMethodId['min'])) {
                $this->addUsingAlias(OrderTableMap::COL_PAYMENT_METHOD_ID, $paymentMethodId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($paymentMethodId['max'])) {
                $this->addUsingAlias(OrderTableMap::COL_PAYMENT_METHOD_ID, $paymentMethodId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderTableMap::COL_PAYMENT_METHOD_ID, $paymentMethodId, $comparison);
    }

    /**
     * Filter the query on the client_id column
     *
     * Example usage:
     * <code>
     * $query->filterByClientId(1234); // WHERE client_id = 1234
     * $query->filterByClientId(array(12, 34)); // WHERE client_id IN (12, 34)
     * $query->filterByClientId(array('min' => 12)); // WHERE client_id > 12
     * </code>
     *
     * @param     mixed $clientId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByClientId($clientId = null, $comparison = null)
    {
        if (is_array($clientId)) {
            $useMinMax = false;
            if (isset($clientId['min'])) {
                $this->addUsingAlias(OrderTableMap::COL_CLIENT_ID, $clientId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($clientId['max'])) {
                $this->addUsingAlias(OrderTableMap::COL_CLIENT_ID, $clientId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderTableMap::COL_CLIENT_ID, $clientId, $comparison);
    }

    /**
     * Filter the query on the global_price_netto column
     *
     * Example usage:
     * <code>
     * $query->filterByGlobalPriceNetto(1234); // WHERE global_price_netto = 1234
     * $query->filterByGlobalPriceNetto(array(12, 34)); // WHERE global_price_netto IN (12, 34)
     * $query->filterByGlobalPriceNetto(array('min' => 12)); // WHERE global_price_netto > 12
     * </code>
     *
     * @param     mixed $globalPriceNetto The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByGlobalPriceNetto($globalPriceNetto = null, $comparison = null)
    {
        if (is_array($globalPriceNetto)) {
            $useMinMax = false;
            if (isset($globalPriceNetto['min'])) {
                $this->addUsingAlias(OrderTableMap::COL_GLOBAL_PRICE_NETTO, $globalPriceNetto['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($globalPriceNetto['max'])) {
                $this->addUsingAlias(OrderTableMap::COL_GLOBAL_PRICE_NETTO, $globalPriceNetto['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderTableMap::COL_GLOBAL_PRICE_NETTO, $globalPriceNetto, $comparison);
    }

    /**
     * Filter the query on the active_link column
     *
     * Example usage:
     * <code>
     * $query->filterByActiveLink('fooValue');   // WHERE active_link = 'fooValue'
     * $query->filterByActiveLink('%fooValue%'); // WHERE active_link LIKE '%fooValue%'
     * </code>
     *
     * @param     string $activeLink The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByActiveLink($activeLink = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($activeLink)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $activeLink)) {
                $activeLink = str_replace('*', '%', $activeLink);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrderTableMap::COL_ACTIVE_LINK, $activeLink, $comparison);
    }

    /**
     * Filter the query on the comment column
     *
     * Example usage:
     * <code>
     * $query->filterByComment('fooValue');   // WHERE comment = 'fooValue'
     * $query->filterByComment('%fooValue%'); // WHERE comment LIKE '%fooValue%'
     * </code>
     *
     * @param     string $comment The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByComment($comment = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($comment)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $comment)) {
                $comment = str_replace('*', '%', $comment);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrderTableMap::COL_COMMENT, $comment, $comparison);
    }

    /**
     * Filter the query on the shop_id column
     *
     * Example usage:
     * <code>
     * $query->filterByShopId(1234); // WHERE shop_id = 1234
     * $query->filterByShopId(array(12, 34)); // WHERE shop_id IN (12, 34)
     * $query->filterByShopId(array('min' => 12)); // WHERE shop_id > 12
     * </code>
     *
     * @see       filterByShop()
     *
     * @param     mixed $shopId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByShopId($shopId = null, $comparison = null)
    {
        if (is_array($shopId)) {
            $useMinMax = false;
            if (isset($shopId['min'])) {
                $this->addUsingAlias(OrderTableMap::COL_SHOP_ID, $shopId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($shopId['max'])) {
                $this->addUsingAlias(OrderTableMap::COL_SHOP_ID, $shopId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderTableMap::COL_SHOP_ID, $shopId, $comparison);
    }

    /**
     * Filter the query on the price_before_promotion column
     *
     * Example usage:
     * <code>
     * $query->filterByPriceBeforePromotion(1234); // WHERE price_before_promotion = 1234
     * $query->filterByPriceBeforePromotion(array(12, 34)); // WHERE price_before_promotion IN (12, 34)
     * $query->filterByPriceBeforePromotion(array('min' => 12)); // WHERE price_before_promotion > 12
     * </code>
     *
     * @param     mixed $priceBeforePromotion The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByPriceBeforePromotion($priceBeforePromotion = null, $comparison = null)
    {
        if (is_array($priceBeforePromotion)) {
            $useMinMax = false;
            if (isset($priceBeforePromotion['min'])) {
                $this->addUsingAlias(OrderTableMap::COL_PRICE_BEFORE_PROMOTION, $priceBeforePromotion['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($priceBeforePromotion['max'])) {
                $this->addUsingAlias(OrderTableMap::COL_PRICE_BEFORE_PROMOTION, $priceBeforePromotion['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderTableMap::COL_PRICE_BEFORE_PROMOTION, $priceBeforePromotion, $comparison);
    }

    /**
     * Filter the query on the currency_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCurrencyId(1234); // WHERE currency_id = 1234
     * $query->filterByCurrencyId(array(12, 34)); // WHERE currency_id IN (12, 34)
     * $query->filterByCurrencyId(array('min' => 12)); // WHERE currency_id > 12
     * </code>
     *
     * @param     mixed $currencyId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByCurrencyId($currencyId = null, $comparison = null)
    {
        if (is_array($currencyId)) {
            $useMinMax = false;
            if (isset($currencyId['min'])) {
                $this->addUsingAlias(OrderTableMap::COL_CURRENCY_ID, $currencyId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($currencyId['max'])) {
                $this->addUsingAlias(OrderTableMap::COL_CURRENCY_ID, $currencyId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderTableMap::COL_CURRENCY_ID, $currencyId, $comparison);
    }

    /**
     * Filter the query on the currency_symbol column
     *
     * Example usage:
     * <code>
     * $query->filterByCurrencySymbol('fooValue');   // WHERE currency_symbol = 'fooValue'
     * $query->filterByCurrencySymbol('%fooValue%'); // WHERE currency_symbol LIKE '%fooValue%'
     * </code>
     *
     * @param     string $currencySymbol The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByCurrencySymbol($currencySymbol = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($currencySymbol)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $currencySymbol)) {
                $currencySymbol = str_replace('*', '%', $currencySymbol);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrderTableMap::COL_CURRENCY_SYMBOL, $currencySymbol, $comparison);
    }

    /**
     * Filter the query on the currency_rate column
     *
     * Example usage:
     * <code>
     * $query->filterByCurrencyRate(1234); // WHERE currency_rate = 1234
     * $query->filterByCurrencyRate(array(12, 34)); // WHERE currency_rate IN (12, 34)
     * $query->filterByCurrencyRate(array('min' => 12)); // WHERE currency_rate > 12
     * </code>
     *
     * @param     mixed $currencyRate The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByCurrencyRate($currencyRate = null, $comparison = null)
    {
        if (is_array($currencyRate)) {
            $useMinMax = false;
            if (isset($currencyRate['min'])) {
                $this->addUsingAlias(OrderTableMap::COL_CURRENCY_RATE, $currencyRate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($currencyRate['max'])) {
                $this->addUsingAlias(OrderTableMap::COL_CURRENCY_RATE, $currencyRate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderTableMap::COL_CURRENCY_RATE, $currencyRate, $comparison);
    }

    /**
     * Filter the query on the cart_rule_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCartRuleId(1234); // WHERE cart_rule_id = 1234
     * $query->filterByCartRuleId(array(12, 34)); // WHERE cart_rule_id IN (12, 34)
     * $query->filterByCartRuleId(array('min' => 12)); // WHERE cart_rule_id > 12
     * </code>
     *
     * @param     mixed $cartRuleId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByCartRuleId($cartRuleId = null, $comparison = null)
    {
        if (is_array($cartRuleId)) {
            $useMinMax = false;
            if (isset($cartRuleId['min'])) {
                $this->addUsingAlias(OrderTableMap::COL_CART_RULE_ID, $cartRuleId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($cartRuleId['max'])) {
                $this->addUsingAlias(OrderTableMap::COL_CART_RULE_ID, $cartRuleId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderTableMap::COL_CART_RULE_ID, $cartRuleId, $comparison);
    }

    /**
     * Filter the query on the session_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySessionId('fooValue');   // WHERE session_id = 'fooValue'
     * $query->filterBySessionId('%fooValue%'); // WHERE session_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $sessionId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterBySessionId($sessionId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($sessionId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $sessionId)) {
                $sessionId = str_replace('*', '%', $sessionId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrderTableMap::COL_SESSION_ID, $sessionId, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Shop\Model\ORM\Shop object
     *
     * @param \Gekosale\Plugin\Shop\Model\ORM\Shop|ObjectCollection $shop The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByShop($shop, $comparison = null)
    {
        if ($shop instanceof \Gekosale\Plugin\Shop\Model\ORM\Shop) {
            return $this
                ->addUsingAlias(OrderTableMap::COL_SHOP_ID, $shop->getId(), $comparison);
        } elseif ($shop instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(OrderTableMap::COL_SHOP_ID, $shop->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByShop() only accepts arguments of type \Gekosale\Plugin\Shop\Model\ORM\Shop or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Shop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function joinShop($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Shop');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Shop');
        }

        return $this;
    }

    /**
     * Use the Shop relation Shop object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Shop\Model\ORM\ShopQuery A secondary query class using the current class as primary query
     */
    public function useShopQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Shop', '\Gekosale\Plugin\Shop\Model\ORM\ShopQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Order\Model\ORM\OrderClientData object
     *
     * @param \Gekosale\Plugin\Order\Model\ORM\OrderClientData|ObjectCollection $orderClientData  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByOrderClientData($orderClientData, $comparison = null)
    {
        if ($orderClientData instanceof \Gekosale\Plugin\Order\Model\ORM\OrderClientData) {
            return $this
                ->addUsingAlias(OrderTableMap::COL_ID, $orderClientData->getOrderId(), $comparison);
        } elseif ($orderClientData instanceof ObjectCollection) {
            return $this
                ->useOrderClientDataQuery()
                ->filterByPrimaryKeys($orderClientData->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByOrderClientData() only accepts arguments of type \Gekosale\Plugin\Order\Model\ORM\OrderClientData or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the OrderClientData relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function joinOrderClientData($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('OrderClientData');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'OrderClientData');
        }

        return $this;
    }

    /**
     * Use the OrderClientData relation OrderClientData object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderClientDataQuery A secondary query class using the current class as primary query
     */
    public function useOrderClientDataQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOrderClientData($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'OrderClientData', '\Gekosale\Plugin\Order\Model\ORM\OrderClientDataQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Order\Model\ORM\OrderClientDeliveryData object
     *
     * @param \Gekosale\Plugin\Order\Model\ORM\OrderClientDeliveryData|ObjectCollection $orderClientDeliveryData  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByOrderClientDeliveryData($orderClientDeliveryData, $comparison = null)
    {
        if ($orderClientDeliveryData instanceof \Gekosale\Plugin\Order\Model\ORM\OrderClientDeliveryData) {
            return $this
                ->addUsingAlias(OrderTableMap::COL_ID, $orderClientDeliveryData->getOrderId(), $comparison);
        } elseif ($orderClientDeliveryData instanceof ObjectCollection) {
            return $this
                ->useOrderClientDeliveryDataQuery()
                ->filterByPrimaryKeys($orderClientDeliveryData->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByOrderClientDeliveryData() only accepts arguments of type \Gekosale\Plugin\Order\Model\ORM\OrderClientDeliveryData or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the OrderClientDeliveryData relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function joinOrderClientDeliveryData($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('OrderClientDeliveryData');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'OrderClientDeliveryData');
        }

        return $this;
    }

    /**
     * Use the OrderClientDeliveryData relation OrderClientDeliveryData object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderClientDeliveryDataQuery A secondary query class using the current class as primary query
     */
    public function useOrderClientDeliveryDataQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOrderClientDeliveryData($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'OrderClientDeliveryData', '\Gekosale\Plugin\Order\Model\ORM\OrderClientDeliveryDataQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Order\Model\ORM\OrderHistory object
     *
     * @param \Gekosale\Plugin\Order\Model\ORM\OrderHistory|ObjectCollection $orderHistory  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByOrderHistory($orderHistory, $comparison = null)
    {
        if ($orderHistory instanceof \Gekosale\Plugin\Order\Model\ORM\OrderHistory) {
            return $this
                ->addUsingAlias(OrderTableMap::COL_ID, $orderHistory->getOrderId(), $comparison);
        } elseif ($orderHistory instanceof ObjectCollection) {
            return $this
                ->useOrderHistoryQuery()
                ->filterByPrimaryKeys($orderHistory->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByOrderHistory() only accepts arguments of type \Gekosale\Plugin\Order\Model\ORM\OrderHistory or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the OrderHistory relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function joinOrderHistory($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('OrderHistory');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'OrderHistory');
        }

        return $this;
    }

    /**
     * Use the OrderHistory relation OrderHistory object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderHistoryQuery A secondary query class using the current class as primary query
     */
    public function useOrderHistoryQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOrderHistory($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'OrderHistory', '\Gekosale\Plugin\Order\Model\ORM\OrderHistoryQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Order\Model\ORM\OrderNotes object
     *
     * @param \Gekosale\Plugin\Order\Model\ORM\OrderNotes|ObjectCollection $orderNotes  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByOrderNotes($orderNotes, $comparison = null)
    {
        if ($orderNotes instanceof \Gekosale\Plugin\Order\Model\ORM\OrderNotes) {
            return $this
                ->addUsingAlias(OrderTableMap::COL_ID, $orderNotes->getOrderId(), $comparison);
        } elseif ($orderNotes instanceof ObjectCollection) {
            return $this
                ->useOrderNotesQuery()
                ->filterByPrimaryKeys($orderNotes->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByOrderNotes() only accepts arguments of type \Gekosale\Plugin\Order\Model\ORM\OrderNotes or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the OrderNotes relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function joinOrderNotes($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('OrderNotes');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'OrderNotes');
        }

        return $this;
    }

    /**
     * Use the OrderNotes relation OrderNotes object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderNotesQuery A secondary query class using the current class as primary query
     */
    public function useOrderNotesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOrderNotes($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'OrderNotes', '\Gekosale\Plugin\Order\Model\ORM\OrderNotesQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Order\Model\ORM\OrderProduct object
     *
     * @param \Gekosale\Plugin\Order\Model\ORM\OrderProduct|ObjectCollection $orderProduct  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function filterByOrderProduct($orderProduct, $comparison = null)
    {
        if ($orderProduct instanceof \Gekosale\Plugin\Order\Model\ORM\OrderProduct) {
            return $this
                ->addUsingAlias(OrderTableMap::COL_ID, $orderProduct->getOrderId(), $comparison);
        } elseif ($orderProduct instanceof ObjectCollection) {
            return $this
                ->useOrderProductQuery()
                ->filterByPrimaryKeys($orderProduct->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByOrderProduct() only accepts arguments of type \Gekosale\Plugin\Order\Model\ORM\OrderProduct or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the OrderProduct relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function joinOrderProduct($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('OrderProduct');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'OrderProduct');
        }

        return $this;
    }

    /**
     * Use the OrderProduct relation OrderProduct object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderProductQuery A secondary query class using the current class as primary query
     */
    public function useOrderProductQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOrderProduct($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'OrderProduct', '\Gekosale\Plugin\Order\Model\ORM\OrderProductQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildOrder $order Object to remove from the list of results
     *
     * @return ChildOrderQuery The current query, for fluid interface
     */
    public function prune($order = null)
    {
        if ($order) {
            $this->addUsingAlias(OrderTableMap::COL_ID, $order->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the order table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(OrderTableMap::DATABASE_NAME);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            OrderTableMap::clearInstancePool();
            OrderTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildOrder or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildOrder object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public function delete(ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(OrderTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(OrderTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        OrderTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            OrderTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // OrderQuery
