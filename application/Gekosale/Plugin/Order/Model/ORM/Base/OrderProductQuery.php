<?php

namespace Gekosale\Plugin\Order\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Order\Model\ORM\OrderProduct as ChildOrderProduct;
use Gekosale\Plugin\Order\Model\ORM\OrderProductQuery as ChildOrderProductQuery;
use Gekosale\Plugin\Order\Model\ORM\Map\OrderProductTableMap;
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
 * Base class that represents a query for the 'order_product' table.
 *
 * 
 *
 * @method     ChildOrderProductQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildOrderProductQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildOrderProductQuery orderByPrice($order = Criteria::ASC) Order by the price column
 * @method     ChildOrderProductQuery orderByQuantity($order = Criteria::ASC) Order by the quantity column
 * @method     ChildOrderProductQuery orderByQuantityPrice($order = Criteria::ASC) Order by the quantity_price column
 * @method     ChildOrderProductQuery orderByOrderId($order = Criteria::ASC) Order by the order_id column
 * @method     ChildOrderProductQuery orderByProductId($order = Criteria::ASC) Order by the product_id column
 * @method     ChildOrderProductQuery orderByProductAttributeId($order = Criteria::ASC) Order by the product_attribute_id column
 * @method     ChildOrderProductQuery orderByVariant($order = Criteria::ASC) Order by the variant column
 * @method     ChildOrderProductQuery orderByVat($order = Criteria::ASC) Order by the vat column
 * @method     ChildOrderProductQuery orderByPriceNetto($order = Criteria::ASC) Order by the price_netto column
 * @method     ChildOrderProductQuery orderByDiscountPrice($order = Criteria::ASC) Order by the discount_price column
 * @method     ChildOrderProductQuery orderByDiscountPriceNetto($order = Criteria::ASC) Order by the discount_price_netto column
 * @method     ChildOrderProductQuery orderByEan($order = Criteria::ASC) Order by the ean column
 * @method     ChildOrderProductQuery orderByPhotoId($order = Criteria::ASC) Order by the photo_id column
 *
 * @method     ChildOrderProductQuery groupById() Group by the id column
 * @method     ChildOrderProductQuery groupByName() Group by the name column
 * @method     ChildOrderProductQuery groupByPrice() Group by the price column
 * @method     ChildOrderProductQuery groupByQuantity() Group by the quantity column
 * @method     ChildOrderProductQuery groupByQuantityPrice() Group by the quantity_price column
 * @method     ChildOrderProductQuery groupByOrderId() Group by the order_id column
 * @method     ChildOrderProductQuery groupByProductId() Group by the product_id column
 * @method     ChildOrderProductQuery groupByProductAttributeId() Group by the product_attribute_id column
 * @method     ChildOrderProductQuery groupByVariant() Group by the variant column
 * @method     ChildOrderProductQuery groupByVat() Group by the vat column
 * @method     ChildOrderProductQuery groupByPriceNetto() Group by the price_netto column
 * @method     ChildOrderProductQuery groupByDiscountPrice() Group by the discount_price column
 * @method     ChildOrderProductQuery groupByDiscountPriceNetto() Group by the discount_price_netto column
 * @method     ChildOrderProductQuery groupByEan() Group by the ean column
 * @method     ChildOrderProductQuery groupByPhotoId() Group by the photo_id column
 *
 * @method     ChildOrderProductQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildOrderProductQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildOrderProductQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildOrderProductQuery leftJoinOrder($relationAlias = null) Adds a LEFT JOIN clause to the query using the Order relation
 * @method     ChildOrderProductQuery rightJoinOrder($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Order relation
 * @method     ChildOrderProductQuery innerJoinOrder($relationAlias = null) Adds a INNER JOIN clause to the query using the Order relation
 *
 * @method     ChildOrderProductQuery leftJoinProduct($relationAlias = null) Adds a LEFT JOIN clause to the query using the Product relation
 * @method     ChildOrderProductQuery rightJoinProduct($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Product relation
 * @method     ChildOrderProductQuery innerJoinProduct($relationAlias = null) Adds a INNER JOIN clause to the query using the Product relation
 *
 * @method     ChildOrderProductQuery leftJoinOrderProductAttribute($relationAlias = null) Adds a LEFT JOIN clause to the query using the OrderProductAttribute relation
 * @method     ChildOrderProductQuery rightJoinOrderProductAttribute($relationAlias = null) Adds a RIGHT JOIN clause to the query using the OrderProductAttribute relation
 * @method     ChildOrderProductQuery innerJoinOrderProductAttribute($relationAlias = null) Adds a INNER JOIN clause to the query using the OrderProductAttribute relation
 *
 * @method     ChildOrderProduct findOne(ConnectionInterface $con = null) Return the first ChildOrderProduct matching the query
 * @method     ChildOrderProduct findOneOrCreate(ConnectionInterface $con = null) Return the first ChildOrderProduct matching the query, or a new ChildOrderProduct object populated from the query conditions when no match is found
 *
 * @method     ChildOrderProduct findOneById(int $id) Return the first ChildOrderProduct filtered by the id column
 * @method     ChildOrderProduct findOneByName(string $name) Return the first ChildOrderProduct filtered by the name column
 * @method     ChildOrderProduct findOneByPrice(string $price) Return the first ChildOrderProduct filtered by the price column
 * @method     ChildOrderProduct findOneByQuantity(string $quantity) Return the first ChildOrderProduct filtered by the quantity column
 * @method     ChildOrderProduct findOneByQuantityPrice(string $quantity_price) Return the first ChildOrderProduct filtered by the quantity_price column
 * @method     ChildOrderProduct findOneByOrderId(int $order_id) Return the first ChildOrderProduct filtered by the order_id column
 * @method     ChildOrderProduct findOneByProductId(int $product_id) Return the first ChildOrderProduct filtered by the product_id column
 * @method     ChildOrderProduct findOneByProductAttributeId(int $product_attribute_id) Return the first ChildOrderProduct filtered by the product_attribute_id column
 * @method     ChildOrderProduct findOneByVariant(string $variant) Return the first ChildOrderProduct filtered by the variant column
 * @method     ChildOrderProduct findOneByVat(string $vat) Return the first ChildOrderProduct filtered by the vat column
 * @method     ChildOrderProduct findOneByPriceNetto(string $price_netto) Return the first ChildOrderProduct filtered by the price_netto column
 * @method     ChildOrderProduct findOneByDiscountPrice(string $discount_price) Return the first ChildOrderProduct filtered by the discount_price column
 * @method     ChildOrderProduct findOneByDiscountPriceNetto(string $discount_price_netto) Return the first ChildOrderProduct filtered by the discount_price_netto column
 * @method     ChildOrderProduct findOneByEan(string $ean) Return the first ChildOrderProduct filtered by the ean column
 * @method     ChildOrderProduct findOneByPhotoId(int $photo_id) Return the first ChildOrderProduct filtered by the photo_id column
 *
 * @method     array findById(int $id) Return ChildOrderProduct objects filtered by the id column
 * @method     array findByName(string $name) Return ChildOrderProduct objects filtered by the name column
 * @method     array findByPrice(string $price) Return ChildOrderProduct objects filtered by the price column
 * @method     array findByQuantity(string $quantity) Return ChildOrderProduct objects filtered by the quantity column
 * @method     array findByQuantityPrice(string $quantity_price) Return ChildOrderProduct objects filtered by the quantity_price column
 * @method     array findByOrderId(int $order_id) Return ChildOrderProduct objects filtered by the order_id column
 * @method     array findByProductId(int $product_id) Return ChildOrderProduct objects filtered by the product_id column
 * @method     array findByProductAttributeId(int $product_attribute_id) Return ChildOrderProduct objects filtered by the product_attribute_id column
 * @method     array findByVariant(string $variant) Return ChildOrderProduct objects filtered by the variant column
 * @method     array findByVat(string $vat) Return ChildOrderProduct objects filtered by the vat column
 * @method     array findByPriceNetto(string $price_netto) Return ChildOrderProduct objects filtered by the price_netto column
 * @method     array findByDiscountPrice(string $discount_price) Return ChildOrderProduct objects filtered by the discount_price column
 * @method     array findByDiscountPriceNetto(string $discount_price_netto) Return ChildOrderProduct objects filtered by the discount_price_netto column
 * @method     array findByEan(string $ean) Return ChildOrderProduct objects filtered by the ean column
 * @method     array findByPhotoId(int $photo_id) Return ChildOrderProduct objects filtered by the photo_id column
 *
 */
abstract class OrderProductQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Order\Model\ORM\Base\OrderProductQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Order\\Model\\ORM\\OrderProduct', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildOrderProductQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildOrderProductQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Order\Model\ORM\OrderProductQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Order\Model\ORM\OrderProductQuery();
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
     * @return ChildOrderProduct|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = OrderProductTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(OrderProductTableMap::DATABASE_NAME);
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
     * @return   ChildOrderProduct A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, NAME, PRICE, QUANTITY, QUANTITY_PRICE, ORDER_ID, PRODUCT_ID, PRODUCT_ATTRIBUTE_ID, VARIANT, VAT, PRICE_NETTO, DISCOUNT_PRICE, DISCOUNT_PRICE_NETTO, EAN, PHOTO_ID FROM order_product WHERE ID = :p0';
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
            $obj = new ChildOrderProduct();
            $obj->hydrate($row);
            OrderProductTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildOrderProduct|array|mixed the result, formatted by the current formatter
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
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(OrderProductTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(OrderProductTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderProductTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the name column
     *
     * Example usage:
     * <code>
     * $query->filterByName('fooValue');   // WHERE name = 'fooValue'
     * $query->filterByName('%fooValue%'); // WHERE name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $name The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function filterByName($name = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($name)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $name)) {
                $name = str_replace('*', '%', $name);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrderProductTableMap::COL_NAME, $name, $comparison);
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
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function filterByPrice($price = null, $comparison = null)
    {
        if (is_array($price)) {
            $useMinMax = false;
            if (isset($price['min'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_PRICE, $price['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($price['max'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_PRICE, $price['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderProductTableMap::COL_PRICE, $price, $comparison);
    }

    /**
     * Filter the query on the quantity column
     *
     * Example usage:
     * <code>
     * $query->filterByQuantity(1234); // WHERE quantity = 1234
     * $query->filterByQuantity(array(12, 34)); // WHERE quantity IN (12, 34)
     * $query->filterByQuantity(array('min' => 12)); // WHERE quantity > 12
     * </code>
     *
     * @param     mixed $quantity The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function filterByQuantity($quantity = null, $comparison = null)
    {
        if (is_array($quantity)) {
            $useMinMax = false;
            if (isset($quantity['min'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_QUANTITY, $quantity['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($quantity['max'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_QUANTITY, $quantity['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderProductTableMap::COL_QUANTITY, $quantity, $comparison);
    }

    /**
     * Filter the query on the quantity_price column
     *
     * Example usage:
     * <code>
     * $query->filterByQuantityPrice(1234); // WHERE quantity_price = 1234
     * $query->filterByQuantityPrice(array(12, 34)); // WHERE quantity_price IN (12, 34)
     * $query->filterByQuantityPrice(array('min' => 12)); // WHERE quantity_price > 12
     * </code>
     *
     * @param     mixed $quantityPrice The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function filterByQuantityPrice($quantityPrice = null, $comparison = null)
    {
        if (is_array($quantityPrice)) {
            $useMinMax = false;
            if (isset($quantityPrice['min'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_QUANTITY_PRICE, $quantityPrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($quantityPrice['max'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_QUANTITY_PRICE, $quantityPrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderProductTableMap::COL_QUANTITY_PRICE, $quantityPrice, $comparison);
    }

    /**
     * Filter the query on the order_id column
     *
     * Example usage:
     * <code>
     * $query->filterByOrderId(1234); // WHERE order_id = 1234
     * $query->filterByOrderId(array(12, 34)); // WHERE order_id IN (12, 34)
     * $query->filterByOrderId(array('min' => 12)); // WHERE order_id > 12
     * </code>
     *
     * @see       filterByOrder()
     *
     * @param     mixed $orderId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function filterByOrderId($orderId = null, $comparison = null)
    {
        if (is_array($orderId)) {
            $useMinMax = false;
            if (isset($orderId['min'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_ORDER_ID, $orderId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($orderId['max'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_ORDER_ID, $orderId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderProductTableMap::COL_ORDER_ID, $orderId, $comparison);
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
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function filterByProductId($productId = null, $comparison = null)
    {
        if (is_array($productId)) {
            $useMinMax = false;
            if (isset($productId['min'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_PRODUCT_ID, $productId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($productId['max'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_PRODUCT_ID, $productId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderProductTableMap::COL_PRODUCT_ID, $productId, $comparison);
    }

    /**
     * Filter the query on the product_attribute_id column
     *
     * Example usage:
     * <code>
     * $query->filterByProductAttributeId(1234); // WHERE product_attribute_id = 1234
     * $query->filterByProductAttributeId(array(12, 34)); // WHERE product_attribute_id IN (12, 34)
     * $query->filterByProductAttributeId(array('min' => 12)); // WHERE product_attribute_id > 12
     * </code>
     *
     * @param     mixed $productAttributeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function filterByProductAttributeId($productAttributeId = null, $comparison = null)
    {
        if (is_array($productAttributeId)) {
            $useMinMax = false;
            if (isset($productAttributeId['min'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_PRODUCT_ATTRIBUTE_ID, $productAttributeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($productAttributeId['max'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_PRODUCT_ATTRIBUTE_ID, $productAttributeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderProductTableMap::COL_PRODUCT_ATTRIBUTE_ID, $productAttributeId, $comparison);
    }

    /**
     * Filter the query on the variant column
     *
     * Example usage:
     * <code>
     * $query->filterByVariant('fooValue');   // WHERE variant = 'fooValue'
     * $query->filterByVariant('%fooValue%'); // WHERE variant LIKE '%fooValue%'
     * </code>
     *
     * @param     string $variant The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function filterByVariant($variant = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($variant)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $variant)) {
                $variant = str_replace('*', '%', $variant);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrderProductTableMap::COL_VARIANT, $variant, $comparison);
    }

    /**
     * Filter the query on the vat column
     *
     * Example usage:
     * <code>
     * $query->filterByVat(1234); // WHERE vat = 1234
     * $query->filterByVat(array(12, 34)); // WHERE vat IN (12, 34)
     * $query->filterByVat(array('min' => 12)); // WHERE vat > 12
     * </code>
     *
     * @param     mixed $vat The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function filterByVat($vat = null, $comparison = null)
    {
        if (is_array($vat)) {
            $useMinMax = false;
            if (isset($vat['min'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_VAT, $vat['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($vat['max'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_VAT, $vat['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderProductTableMap::COL_VAT, $vat, $comparison);
    }

    /**
     * Filter the query on the price_netto column
     *
     * Example usage:
     * <code>
     * $query->filterByPriceNetto(1234); // WHERE price_netto = 1234
     * $query->filterByPriceNetto(array(12, 34)); // WHERE price_netto IN (12, 34)
     * $query->filterByPriceNetto(array('min' => 12)); // WHERE price_netto > 12
     * </code>
     *
     * @param     mixed $priceNetto The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function filterByPriceNetto($priceNetto = null, $comparison = null)
    {
        if (is_array($priceNetto)) {
            $useMinMax = false;
            if (isset($priceNetto['min'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_PRICE_NETTO, $priceNetto['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($priceNetto['max'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_PRICE_NETTO, $priceNetto['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderProductTableMap::COL_PRICE_NETTO, $priceNetto, $comparison);
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
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function filterByDiscountPrice($discountPrice = null, $comparison = null)
    {
        if (is_array($discountPrice)) {
            $useMinMax = false;
            if (isset($discountPrice['min'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_DISCOUNT_PRICE, $discountPrice['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($discountPrice['max'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_DISCOUNT_PRICE, $discountPrice['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderProductTableMap::COL_DISCOUNT_PRICE, $discountPrice, $comparison);
    }

    /**
     * Filter the query on the discount_price_netto column
     *
     * Example usage:
     * <code>
     * $query->filterByDiscountPriceNetto(1234); // WHERE discount_price_netto = 1234
     * $query->filterByDiscountPriceNetto(array(12, 34)); // WHERE discount_price_netto IN (12, 34)
     * $query->filterByDiscountPriceNetto(array('min' => 12)); // WHERE discount_price_netto > 12
     * </code>
     *
     * @param     mixed $discountPriceNetto The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function filterByDiscountPriceNetto($discountPriceNetto = null, $comparison = null)
    {
        if (is_array($discountPriceNetto)) {
            $useMinMax = false;
            if (isset($discountPriceNetto['min'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_DISCOUNT_PRICE_NETTO, $discountPriceNetto['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($discountPriceNetto['max'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_DISCOUNT_PRICE_NETTO, $discountPriceNetto['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderProductTableMap::COL_DISCOUNT_PRICE_NETTO, $discountPriceNetto, $comparison);
    }

    /**
     * Filter the query on the ean column
     *
     * Example usage:
     * <code>
     * $query->filterByEan('fooValue');   // WHERE ean = 'fooValue'
     * $query->filterByEan('%fooValue%'); // WHERE ean LIKE '%fooValue%'
     * </code>
     *
     * @param     string $ean The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function filterByEan($ean = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($ean)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $ean)) {
                $ean = str_replace('*', '%', $ean);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(OrderProductTableMap::COL_EAN, $ean, $comparison);
    }

    /**
     * Filter the query on the photo_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPhotoId(1234); // WHERE photo_id = 1234
     * $query->filterByPhotoId(array(12, 34)); // WHERE photo_id IN (12, 34)
     * $query->filterByPhotoId(array('min' => 12)); // WHERE photo_id > 12
     * </code>
     *
     * @param     mixed $photoId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function filterByPhotoId($photoId = null, $comparison = null)
    {
        if (is_array($photoId)) {
            $useMinMax = false;
            if (isset($photoId['min'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_PHOTO_ID, $photoId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($photoId['max'])) {
                $this->addUsingAlias(OrderProductTableMap::COL_PHOTO_ID, $photoId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(OrderProductTableMap::COL_PHOTO_ID, $photoId, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Order\Model\ORM\Order object
     *
     * @param \Gekosale\Plugin\Order\Model\ORM\Order|ObjectCollection $order The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function filterByOrder($order, $comparison = null)
    {
        if ($order instanceof \Gekosale\Plugin\Order\Model\ORM\Order) {
            return $this
                ->addUsingAlias(OrderProductTableMap::COL_ORDER_ID, $order->getId(), $comparison);
        } elseif ($order instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(OrderProductTableMap::COL_ORDER_ID, $order->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByOrder() only accepts arguments of type \Gekosale\Plugin\Order\Model\ORM\Order or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Order relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function joinOrder($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Order');

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
            $this->addJoinObject($join, 'Order');
        }

        return $this;
    }

    /**
     * Use the Order relation Order object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderQuery A secondary query class using the current class as primary query
     */
    public function useOrderQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOrder($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Order', '\Gekosale\Plugin\Order\Model\ORM\OrderQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Product\Model\ORM\Product object
     *
     * @param \Gekosale\Plugin\Product\Model\ORM\Product|ObjectCollection $product The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function filterByProduct($product, $comparison = null)
    {
        if ($product instanceof \Gekosale\Plugin\Product\Model\ORM\Product) {
            return $this
                ->addUsingAlias(OrderProductTableMap::COL_PRODUCT_ID, $product->getId(), $comparison);
        } elseif ($product instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(OrderProductTableMap::COL_PRODUCT_ID, $product->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function joinProduct($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useProductQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinProduct($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Product', '\Gekosale\Plugin\Product\Model\ORM\ProductQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Order\Model\ORM\OrderProductAttribute object
     *
     * @param \Gekosale\Plugin\Order\Model\ORM\OrderProductAttribute|ObjectCollection $orderProductAttribute  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function filterByOrderProductAttribute($orderProductAttribute, $comparison = null)
    {
        if ($orderProductAttribute instanceof \Gekosale\Plugin\Order\Model\ORM\OrderProductAttribute) {
            return $this
                ->addUsingAlias(OrderProductTableMap::COL_ID, $orderProductAttribute->getOrderProductId(), $comparison);
        } elseif ($orderProductAttribute instanceof ObjectCollection) {
            return $this
                ->useOrderProductAttributeQuery()
                ->filterByPrimaryKeys($orderProductAttribute->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByOrderProductAttribute() only accepts arguments of type \Gekosale\Plugin\Order\Model\ORM\OrderProductAttribute or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the OrderProductAttribute relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function joinOrderProductAttribute($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('OrderProductAttribute');

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
            $this->addJoinObject($join, 'OrderProductAttribute');
        }

        return $this;
    }

    /**
     * Use the OrderProductAttribute relation OrderProductAttribute object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Order\Model\ORM\OrderProductAttributeQuery A secondary query class using the current class as primary query
     */
    public function useOrderProductAttributeQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinOrderProductAttribute($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'OrderProductAttribute', '\Gekosale\Plugin\Order\Model\ORM\OrderProductAttributeQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildOrderProduct $orderProduct Object to remove from the list of results
     *
     * @return ChildOrderProductQuery The current query, for fluid interface
     */
    public function prune($orderProduct = null)
    {
        if ($orderProduct) {
            $this->addUsingAlias(OrderProductTableMap::COL_ID, $orderProduct->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the order_product table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(OrderProductTableMap::DATABASE_NAME);
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
            OrderProductTableMap::clearInstancePool();
            OrderProductTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildOrderProduct or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildOrderProduct object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(OrderProductTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(OrderProductTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        OrderProductTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            OrderProductTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // OrderProductQuery
