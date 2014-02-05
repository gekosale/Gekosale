<?php

namespace Gekosale\Plugin\ProductGroupPrice\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroup;
use Gekosale\Plugin\ProductGroupPrice\Model\ORM\ProductGroupPrice as ChildProductGroupPrice;
use Gekosale\Plugin\ProductGroupPrice\Model\ORM\ProductGroupPriceQuery as ChildProductGroupPriceQuery;
use Gekosale\Plugin\ProductGroupPrice\Model\ORM\Map\ProductGroupPriceTableMap;
use Gekosale\Plugin\Product\Model\ORM\Product;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'product_group_price' table.
 *
 * 
 *
 * @method     ChildProductGroupPriceQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildProductGroupPriceQuery orderByClientGroupId($order = Criteria::ASC) Order by the client_group_id column
 * @method     ChildProductGroupPriceQuery orderByProductId($order = Criteria::ASC) Order by the product_id column
 * @method     ChildProductGroupPriceQuery orderByGroupPrice($order = Criteria::ASC) Order by the group_price column
 * @method     ChildProductGroupPriceQuery orderBySellPrice($order = Criteria::ASC) Order by the sell_price column
 * @method     ChildProductGroupPriceQuery orderByPromotion($order = Criteria::ASC) Order by the promotion column
 * @method     ChildProductGroupPriceQuery orderByDiscountPrice($order = Criteria::ASC) Order by the discount_price column
 * @method     ChildProductGroupPriceQuery orderByPromotionStart($order = Criteria::ASC) Order by the promotion_start column
 * @method     ChildProductGroupPriceQuery orderByPromotionEnd($order = Criteria::ASC) Order by the promotion_end column
 *
 * @method     ChildProductGroupPriceQuery groupById() Group by the id column
 * @method     ChildProductGroupPriceQuery groupByClientGroupId() Group by the client_group_id column
 * @method     ChildProductGroupPriceQuery groupByProductId() Group by the product_id column
 * @method     ChildProductGroupPriceQuery groupByGroupPrice() Group by the group_price column
 * @method     ChildProductGroupPriceQuery groupBySellPrice() Group by the sell_price column
 * @method     ChildProductGroupPriceQuery groupByPromotion() Group by the promotion column
 * @method     ChildProductGroupPriceQuery groupByDiscountPrice() Group by the discount_price column
 * @method     ChildProductGroupPriceQuery groupByPromotionStart() Group by the promotion_start column
 * @method     ChildProductGroupPriceQuery groupByPromotionEnd() Group by the promotion_end column
 *
 * @method     ChildProductGroupPriceQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildProductGroupPriceQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildProductGroupPriceQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildProductGroupPriceQuery leftJoinClientGroup($relationAlias = null) Adds a LEFT JOIN clause to the query using the ClientGroup relation
 * @method     ChildProductGroupPriceQuery rightJoinClientGroup($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ClientGroup relation
 * @method     ChildProductGroupPriceQuery innerJoinClientGroup($relationAlias = null) Adds a INNER JOIN clause to the query using the ClientGroup relation
 *
 * @method     ChildProductGroupPriceQuery leftJoinProduct($relationAlias = null) Adds a LEFT JOIN clause to the query using the Product relation
 * @method     ChildProductGroupPriceQuery rightJoinProduct($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Product relation
 * @method     ChildProductGroupPriceQuery innerJoinProduct($relationAlias = null) Adds a INNER JOIN clause to the query using the Product relation
 *
 * @method     ChildProductGroupPrice findOne(ConnectionInterface $con = null) Return the first ChildProductGroupPrice matching the query
 * @method     ChildProductGroupPrice findOneOrCreate(ConnectionInterface $con = null) Return the first ChildProductGroupPrice matching the query, or a new ChildProductGroupPrice object populated from the query conditions when no match is found
 *
 * @method     ChildProductGroupPrice findOneById(int $id) Return the first ChildProductGroupPrice filtered by the id column
 * @method     ChildProductGroupPrice findOneByClientGroupId(int $client_group_id) Return the first ChildProductGroupPrice filtered by the client_group_id column
 * @method     ChildProductGroupPrice findOneByProductId(int $product_id) Return the first ChildProductGroupPrice filtered by the product_id column
 * @method     ChildProductGroupPrice findOneByGroupPrice(int $group_price) Return the first ChildProductGroupPrice filtered by the group_price column
 * @method     ChildProductGroupPrice findOneBySellPrice(string $sell_price) Return the first ChildProductGroupPrice filtered by the sell_price column
 * @method     ChildProductGroupPrice findOneByPromotion(int $promotion) Return the first ChildProductGroupPrice filtered by the promotion column
 * @method     ChildProductGroupPrice findOneByDiscountPrice(string $discount_price) Return the first ChildProductGroupPrice filtered by the discount_price column
 * @method     ChildProductGroupPrice findOneByPromotionStart(string $promotion_start) Return the first ChildProductGroupPrice filtered by the promotion_start column
 * @method     ChildProductGroupPrice findOneByPromotionEnd(string $promotion_end) Return the first ChildProductGroupPrice filtered by the promotion_end column
 *
 * @method     array findById(int $id) Return ChildProductGroupPrice objects filtered by the id column
 * @method     array findByClientGroupId(int $client_group_id) Return ChildProductGroupPrice objects filtered by the client_group_id column
 * @method     array findByProductId(int $product_id) Return ChildProductGroupPrice objects filtered by the product_id column
 * @method     array findByGroupPrice(int $group_price) Return ChildProductGroupPrice objects filtered by the group_price column
 * @method     array findBySellPrice(string $sell_price) Return ChildProductGroupPrice objects filtered by the sell_price column
 * @method     array findByPromotion(int $promotion) Return ChildProductGroupPrice objects filtered by the promotion column
 * @method     array findByDiscountPrice(string $discount_price) Return ChildProductGroupPrice objects filtered by the discount_price column
 * @method     array findByPromotionStart(string $promotion_start) Return ChildProductGroupPrice objects filtered by the promotion_start column
 * @method     array findByPromotionEnd(string $promotion_end) Return ChildProductGroupPrice objects filtered by the promotion_end column
 *
 */
abstract class ProductGroupPriceQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\ProductGroupPrice\Model\ORM\Base\ProductGroupPriceQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\ProductGroupPrice\\Model\\ORM\\ProductGroupPrice', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildProductGroupPriceQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildProductGroupPriceQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\ProductGroupPrice\Model\ORM\ProductGroupPriceQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\ProductGroupPrice\Model\ORM\ProductGroupPriceQuery();
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
     * @return ChildProductGroupPrice|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ProductGroupPriceTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ProductGroupPriceTableMap::DATABASE_NAME);
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
     * @return   ChildProductGroupPrice A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, CLIENT_GROUP_ID, PRODUCT_ID, GROUP_PRICE, SELL_PRICE, PROMOTION, DISCOUNT_PRICE, PROMOTION_START, PROMOTION_END FROM product_group_price WHERE ID = :p0';
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
            $obj = new ChildProductGroupPrice();
            $obj->hydrate($row);
            ProductGroupPriceTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildProductGroupPrice|array|mixed the result, formatted by the current formatter
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
     * @return ChildProductGroupPriceQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ProductGroupPriceTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildProductGroupPriceQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ProductGroupPriceTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildProductGroupPriceQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ProductGroupPriceTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ProductGroupPriceTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductGroupPriceTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the client_group_id column
     *
     * Example usage:
     * <code>
     * $query->filterByClientGroupId(1234); // WHERE client_group_id = 1234
     * $query->filterByClientGroupId(array(12, 34)); // WHERE client_group_id IN (12, 34)
     * $query->filterByClientGroupId(array('min' => 12)); // WHERE client_group_id > 12
     * </code>
     *
     * @see       filterByClientGroup()
     *
     * @param     mixed $clientGroupId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductGroupPriceQuery The current query, for fluid interface
     */
    public function filterByClientGroupId($clientGroupId = null, $comparison = null)
    {
        if (is_array($clientGroupId)) {
            $useMinMax = false;
            if (isset($clientGroupId['min'])) {
                $this->addUsingAlias(ProductGroupPriceTableMap::COL_CLIENT_GROUP_ID, $clientGroupId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($clientGroupId['max'])) {
                $this->addUsingAlias(ProductGroupPriceTableMap::COL_CLIENT_GROUP_ID, $clientGroupId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductGroupPriceTableMap::COL_CLIENT_GROUP_ID, $clientGroupId, $comparison);
    }

    /**
     * Filter the query on the product_id column
     *
     * Example usage:
     * <code>
     * $query->filterByProductId(1234); // WHERE product_id = 1234
     * $query->filterByProductId(array(12, 34)); // WHERE product_id IN (12, 34)
     * $query->filterByProductId(array('min' => 12)); // WHERE product_id > 12
     * </code>
     *
     * @see       filterByProduct()
     *
     * @param     mixed $productId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductGroupPriceQuery The current query, for fluid interface
     */
    public function filterByProductId($productId = null, $comparison = null)
    {
        if (is_array($productId)) {
            $useMinMax = false;
            if (isset($productId['min'])) {
                $this->addUsingAlias(ProductGroupPriceTableMap::COL_PRODUCT_ID, $productId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($productId['max'])) {
                $this->addUsingAlias(ProductGroupPriceTableMap::COL_PRODUCT_ID, $productId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductGroupPriceTableMap::COL_PRODUCT_ID, $productId, $comparison);
    }

    /**
     * Filter the query on the group_price column
     *
     * Example usage:
     * <code>
     * $query->filterByGroupPrice(1234); // WHERE group_price = 1234
     * $query->filterByGroupPrice(array(12, 34)); // WHERE group_price IN (12, 34)
     * $query->filterByGroupPrice(array('min' => 12)); // WHERE group_price > 12
     * </code>
     *
     * @param     mixed $groupPrice The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductGroupPriceQuery The current query, for fluid interface
     */
    public function filterByGroupPrice($groupPrice = null, $comparison = null)
    {
        if (is_array($groupPrice)) {
            $useMinMax = false;
            if (isset($groupPrice['min'])) {
                $this->addUsingAlias(ProductGroupPriceTableMap::COL_GROUP_PRICE, $groupPrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($groupPrice['max'])) {
                $this->addUsingAlias(ProductGroupPriceTableMap::COL_GROUP_PRICE, $groupPrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductGroupPriceTableMap::COL_GROUP_PRICE, $groupPrice, $comparison);
    }

    /**
     * Filter the query on the sell_price column
     *
     * Example usage:
     * <code>
     * $query->filterBySellPrice(1234); // WHERE sell_price = 1234
     * $query->filterBySellPrice(array(12, 34)); // WHERE sell_price IN (12, 34)
     * $query->filterBySellPrice(array('min' => 12)); // WHERE sell_price > 12
     * </code>
     *
     * @param     mixed $sellPrice The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductGroupPriceQuery The current query, for fluid interface
     */
    public function filterBySellPrice($sellPrice = null, $comparison = null)
    {
        if (is_array($sellPrice)) {
            $useMinMax = false;
            if (isset($sellPrice['min'])) {
                $this->addUsingAlias(ProductGroupPriceTableMap::COL_SELL_PRICE, $sellPrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sellPrice['max'])) {
                $this->addUsingAlias(ProductGroupPriceTableMap::COL_SELL_PRICE, $sellPrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductGroupPriceTableMap::COL_SELL_PRICE, $sellPrice, $comparison);
    }

    /**
     * Filter the query on the promotion column
     *
     * Example usage:
     * <code>
     * $query->filterByPromotion(1234); // WHERE promotion = 1234
     * $query->filterByPromotion(array(12, 34)); // WHERE promotion IN (12, 34)
     * $query->filterByPromotion(array('min' => 12)); // WHERE promotion > 12
     * </code>
     *
     * @param     mixed $promotion The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductGroupPriceQuery The current query, for fluid interface
     */
    public function filterByPromotion($promotion = null, $comparison = null)
    {
        if (is_array($promotion)) {
            $useMinMax = false;
            if (isset($promotion['min'])) {
                $this->addUsingAlias(ProductGroupPriceTableMap::COL_PROMOTION, $promotion['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($promotion['max'])) {
                $this->addUsingAlias(ProductGroupPriceTableMap::COL_PROMOTION, $promotion['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductGroupPriceTableMap::COL_PROMOTION, $promotion, $comparison);
    }

    /**
     * Filter the query on the discount_price column
     *
     * Example usage:
     * <code>
     * $query->filterByDiscountPrice(1234); // WHERE discount_price = 1234
     * $query->filterByDiscountPrice(array(12, 34)); // WHERE discount_price IN (12, 34)
     * $query->filterByDiscountPrice(array('min' => 12)); // WHERE discount_price > 12
     * </code>
     *
     * @param     mixed $discountPrice The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductGroupPriceQuery The current query, for fluid interface
     */
    public function filterByDiscountPrice($discountPrice = null, $comparison = null)
    {
        if (is_array($discountPrice)) {
            $useMinMax = false;
            if (isset($discountPrice['min'])) {
                $this->addUsingAlias(ProductGroupPriceTableMap::COL_DISCOUNT_PRICE, $discountPrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($discountPrice['max'])) {
                $this->addUsingAlias(ProductGroupPriceTableMap::COL_DISCOUNT_PRICE, $discountPrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductGroupPriceTableMap::COL_DISCOUNT_PRICE, $discountPrice, $comparison);
    }

    /**
     * Filter the query on the promotion_start column
     *
     * Example usage:
     * <code>
     * $query->filterByPromotionStart('2011-03-14'); // WHERE promotion_start = '2011-03-14'
     * $query->filterByPromotionStart('now'); // WHERE promotion_start = '2011-03-14'
     * $query->filterByPromotionStart(array('max' => 'yesterday')); // WHERE promotion_start > '2011-03-13'
     * </code>
     *
     * @param     mixed $promotionStart The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductGroupPriceQuery The current query, for fluid interface
     */
    public function filterByPromotionStart($promotionStart = null, $comparison = null)
    {
        if (is_array($promotionStart)) {
            $useMinMax = false;
            if (isset($promotionStart['min'])) {
                $this->addUsingAlias(ProductGroupPriceTableMap::COL_PROMOTION_START, $promotionStart['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($promotionStart['max'])) {
                $this->addUsingAlias(ProductGroupPriceTableMap::COL_PROMOTION_START, $promotionStart['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductGroupPriceTableMap::COL_PROMOTION_START, $promotionStart, $comparison);
    }

    /**
     * Filter the query on the promotion_end column
     *
     * Example usage:
     * <code>
     * $query->filterByPromotionEnd('2011-03-14'); // WHERE promotion_end = '2011-03-14'
     * $query->filterByPromotionEnd('now'); // WHERE promotion_end = '2011-03-14'
     * $query->filterByPromotionEnd(array('max' => 'yesterday')); // WHERE promotion_end > '2011-03-13'
     * </code>
     *
     * @param     mixed $promotionEnd The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductGroupPriceQuery The current query, for fluid interface
     */
    public function filterByPromotionEnd($promotionEnd = null, $comparison = null)
    {
        if (is_array($promotionEnd)) {
            $useMinMax = false;
            if (isset($promotionEnd['min'])) {
                $this->addUsingAlias(ProductGroupPriceTableMap::COL_PROMOTION_END, $promotionEnd['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($promotionEnd['max'])) {
                $this->addUsingAlias(ProductGroupPriceTableMap::COL_PROMOTION_END, $promotionEnd['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ProductGroupPriceTableMap::COL_PROMOTION_END, $promotionEnd, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroup object
     *
     * @param \Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroup|ObjectCollection $clientGroup The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductGroupPriceQuery The current query, for fluid interface
     */
    public function filterByClientGroup($clientGroup, $comparison = null)
    {
        if ($clientGroup instanceof \Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroup) {
            return $this
                ->addUsingAlias(ProductGroupPriceTableMap::COL_CLIENT_GROUP_ID, $clientGroup->getId(), $comparison);
        } elseif ($clientGroup instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductGroupPriceTableMap::COL_CLIENT_GROUP_ID, $clientGroup->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByClientGroup() only accepts arguments of type \Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroup or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ClientGroup relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductGroupPriceQuery The current query, for fluid interface
     */
    public function joinClientGroup($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ClientGroup');

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
            $this->addJoinObject($join, 'ClientGroup');
        }

        return $this;
    }

    /**
     * Use the ClientGroup relation ClientGroup object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroupQuery A secondary query class using the current class as primary query
     */
    public function useClientGroupQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinClientGroup($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ClientGroup', '\Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroupQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Product\Model\ORM\Product object
     *
     * @param \Gekosale\Plugin\Product\Model\ORM\Product|ObjectCollection $product The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildProductGroupPriceQuery The current query, for fluid interface
     */
    public function filterByProduct($product, $comparison = null)
    {
        if ($product instanceof \Gekosale\Plugin\Product\Model\ORM\Product) {
            return $this
                ->addUsingAlias(ProductGroupPriceTableMap::COL_PRODUCT_ID, $product->getId(), $comparison);
        } elseif ($product instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ProductGroupPriceTableMap::COL_PRODUCT_ID, $product->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByProduct() only accepts arguments of type \Gekosale\Plugin\Product\Model\ORM\Product or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Product relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildProductGroupPriceQuery The current query, for fluid interface
     */
    public function joinProduct($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Product');

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
            $this->addJoinObject($join, 'Product');
        }

        return $this;
    }

    /**
     * Use the Product relation Product object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Product\Model\ORM\ProductQuery A secondary query class using the current class as primary query
     */
    public function useProductQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProduct($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Product', '\Gekosale\Plugin\Product\Model\ORM\ProductQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildProductGroupPrice $productGroupPrice Object to remove from the list of results
     *
     * @return ChildProductGroupPriceQuery The current query, for fluid interface
     */
    public function prune($productGroupPrice = null)
    {
        if ($productGroupPrice) {
            $this->addUsingAlias(ProductGroupPriceTableMap::COL_ID, $productGroupPrice->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the product_group_price table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ProductGroupPriceTableMap::DATABASE_NAME);
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
            ProductGroupPriceTableMap::clearInstancePool();
            ProductGroupPriceTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildProductGroupPrice or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildProductGroupPrice object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(ProductGroupPriceTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ProductGroupPriceTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        ProductGroupPriceTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            ProductGroupPriceTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // ProductGroupPriceQuery
