<?php

namespace Gekosale\Plugin\MissingCart\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProduct as ChildMissingCartProduct;
use Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProductQuery as ChildMissingCartProductQuery;
use Gekosale\Plugin\MissingCart\Model\ORM\Map\MissingCartProductTableMap;
use Gekosale\Plugin\Product\Model\ORM\Product;
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
 * Base class that represents a query for the 'missing_cart_product' table.
 *
 * 
 *
 * @method     ChildMissingCartProductQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildMissingCartProductQuery orderByMissingCartId($order = Criteria::ASC) Order by the missing_cart_id column
 * @method     ChildMissingCartProductQuery orderByProductId($order = Criteria::ASC) Order by the product_id column
 * @method     ChildMissingCartProductQuery orderByStock($order = Criteria::ASC) Order by the stock column
 * @method     ChildMissingCartProductQuery orderByQuantity($order = Criteria::ASC) Order by the quantity column
 * @method     ChildMissingCartProductQuery orderByProductAttributeId($order = Criteria::ASC) Order by the product_attribute_id column
 * @method     ChildMissingCartProductQuery orderByShopId($order = Criteria::ASC) Order by the shop_id column
 *
 * @method     ChildMissingCartProductQuery groupById() Group by the id column
 * @method     ChildMissingCartProductQuery groupByMissingCartId() Group by the missing_cart_id column
 * @method     ChildMissingCartProductQuery groupByProductId() Group by the product_id column
 * @method     ChildMissingCartProductQuery groupByStock() Group by the stock column
 * @method     ChildMissingCartProductQuery groupByQuantity() Group by the quantity column
 * @method     ChildMissingCartProductQuery groupByProductAttributeId() Group by the product_attribute_id column
 * @method     ChildMissingCartProductQuery groupByShopId() Group by the shop_id column
 *
 * @method     ChildMissingCartProductQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildMissingCartProductQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildMissingCartProductQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildMissingCartProductQuery leftJoinMissingCart($relationAlias = null) Adds a LEFT JOIN clause to the query using the MissingCart relation
 * @method     ChildMissingCartProductQuery rightJoinMissingCart($relationAlias = null) Adds a RIGHT JOIN clause to the query using the MissingCart relation
 * @method     ChildMissingCartProductQuery innerJoinMissingCart($relationAlias = null) Adds a INNER JOIN clause to the query using the MissingCart relation
 *
 * @method     ChildMissingCartProductQuery leftJoinShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the Shop relation
 * @method     ChildMissingCartProductQuery rightJoinShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Shop relation
 * @method     ChildMissingCartProductQuery innerJoinShop($relationAlias = null) Adds a INNER JOIN clause to the query using the Shop relation
 *
 * @method     ChildMissingCartProductQuery leftJoinProduct($relationAlias = null) Adds a LEFT JOIN clause to the query using the Product relation
 * @method     ChildMissingCartProductQuery rightJoinProduct($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Product relation
 * @method     ChildMissingCartProductQuery innerJoinProduct($relationAlias = null) Adds a INNER JOIN clause to the query using the Product relation
 *
 * @method     ChildMissingCartProduct findOne(ConnectionInterface $con = null) Return the first ChildMissingCartProduct matching the query
 * @method     ChildMissingCartProduct findOneOrCreate(ConnectionInterface $con = null) Return the first ChildMissingCartProduct matching the query, or a new ChildMissingCartProduct object populated from the query conditions when no match is found
 *
 * @method     ChildMissingCartProduct findOneById(int $id) Return the first ChildMissingCartProduct filtered by the id column
 * @method     ChildMissingCartProduct findOneByMissingCartId(int $missing_cart_id) Return the first ChildMissingCartProduct filtered by the missing_cart_id column
 * @method     ChildMissingCartProduct findOneByProductId(int $product_id) Return the first ChildMissingCartProduct filtered by the product_id column
 * @method     ChildMissingCartProduct findOneByStock(int $stock) Return the first ChildMissingCartProduct filtered by the stock column
 * @method     ChildMissingCartProduct findOneByQuantity(int $quantity) Return the first ChildMissingCartProduct filtered by the quantity column
 * @method     ChildMissingCartProduct findOneByProductAttributeId(int $product_attribute_id) Return the first ChildMissingCartProduct filtered by the product_attribute_id column
 * @method     ChildMissingCartProduct findOneByShopId(int $shop_id) Return the first ChildMissingCartProduct filtered by the shop_id column
 *
 * @method     array findById(int $id) Return ChildMissingCartProduct objects filtered by the id column
 * @method     array findByMissingCartId(int $missing_cart_id) Return ChildMissingCartProduct objects filtered by the missing_cart_id column
 * @method     array findByProductId(int $product_id) Return ChildMissingCartProduct objects filtered by the product_id column
 * @method     array findByStock(int $stock) Return ChildMissingCartProduct objects filtered by the stock column
 * @method     array findByQuantity(int $quantity) Return ChildMissingCartProduct objects filtered by the quantity column
 * @method     array findByProductAttributeId(int $product_attribute_id) Return ChildMissingCartProduct objects filtered by the product_attribute_id column
 * @method     array findByShopId(int $shop_id) Return ChildMissingCartProduct objects filtered by the shop_id column
 *
 */
abstract class MissingCartProductQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\MissingCart\Model\ORM\Base\MissingCartProductQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\MissingCart\\Model\\ORM\\MissingCartProduct', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildMissingCartProductQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildMissingCartProductQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProductQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProductQuery();
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
     * @return ChildMissingCartProduct|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = MissingCartProductTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(MissingCartProductTableMap::DATABASE_NAME);
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
     * @return   ChildMissingCartProduct A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, MISSING_CART_ID, PRODUCT_ID, STOCK, QUANTITY, PRODUCT_ATTRIBUTE_ID, SHOP_ID FROM missing_cart_product WHERE ID = :p0';
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
            $obj = new ChildMissingCartProduct();
            $obj->hydrate($row);
            MissingCartProductTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildMissingCartProduct|array|mixed the result, formatted by the current formatter
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
     * @return ChildMissingCartProductQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(MissingCartProductTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildMissingCartProductQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(MissingCartProductTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildMissingCartProductQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(MissingCartProductTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(MissingCartProductTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MissingCartProductTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the missing_cart_id column
     *
     * Example usage:
     * <code>
     * $query->filterByMissingCartId(1234); // WHERE missing_cart_id = 1234
     * $query->filterByMissingCartId(array(12, 34)); // WHERE missing_cart_id IN (12, 34)
     * $query->filterByMissingCartId(array('min' => 12)); // WHERE missing_cart_id > 12
     * </code>
     *
     * @see       filterByMissingCart()
     *
     * @param     mixed $missingCartId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildMissingCartProductQuery The current query, for fluid interface
     */
    public function filterByMissingCartId($missingCartId = null, $comparison = null)
    {
        if (is_array($missingCartId)) {
            $useMinMax = false;
            if (isset($missingCartId['min'])) {
                $this->addUsingAlias(MissingCartProductTableMap::COL_MISSING_CART_ID, $missingCartId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($missingCartId['max'])) {
                $this->addUsingAlias(MissingCartProductTableMap::COL_MISSING_CART_ID, $missingCartId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MissingCartProductTableMap::COL_MISSING_CART_ID, $missingCartId, $comparison);
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
     * @return ChildMissingCartProductQuery The current query, for fluid interface
     */
    public function filterByProductId($productId = null, $comparison = null)
    {
        if (is_array($productId)) {
            $useMinMax = false;
            if (isset($productId['min'])) {
                $this->addUsingAlias(MissingCartProductTableMap::COL_PRODUCT_ID, $productId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($productId['max'])) {
                $this->addUsingAlias(MissingCartProductTableMap::COL_PRODUCT_ID, $productId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MissingCartProductTableMap::COL_PRODUCT_ID, $productId, $comparison);
    }

    /**
     * Filter the query on the stock column
     *
     * Example usage:
     * <code>
     * $query->filterByStock(1234); // WHERE stock = 1234
     * $query->filterByStock(array(12, 34)); // WHERE stock IN (12, 34)
     * $query->filterByStock(array('min' => 12)); // WHERE stock > 12
     * </code>
     *
     * @param     mixed $stock The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildMissingCartProductQuery The current query, for fluid interface
     */
    public function filterByStock($stock = null, $comparison = null)
    {
        if (is_array($stock)) {
            $useMinMax = false;
            if (isset($stock['min'])) {
                $this->addUsingAlias(MissingCartProductTableMap::COL_STOCK, $stock['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($stock['max'])) {
                $this->addUsingAlias(MissingCartProductTableMap::COL_STOCK, $stock['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MissingCartProductTableMap::COL_STOCK, $stock, $comparison);
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
     * @return ChildMissingCartProductQuery The current query, for fluid interface
     */
    public function filterByQuantity($quantity = null, $comparison = null)
    {
        if (is_array($quantity)) {
            $useMinMax = false;
            if (isset($quantity['min'])) {
                $this->addUsingAlias(MissingCartProductTableMap::COL_QUANTITY, $quantity['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($quantity['max'])) {
                $this->addUsingAlias(MissingCartProductTableMap::COL_QUANTITY, $quantity['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MissingCartProductTableMap::COL_QUANTITY, $quantity, $comparison);
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
     * @return ChildMissingCartProductQuery The current query, for fluid interface
     */
    public function filterByProductAttributeId($productAttributeId = null, $comparison = null)
    {
        if (is_array($productAttributeId)) {
            $useMinMax = false;
            if (isset($productAttributeId['min'])) {
                $this->addUsingAlias(MissingCartProductTableMap::COL_PRODUCT_ATTRIBUTE_ID, $productAttributeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($productAttributeId['max'])) {
                $this->addUsingAlias(MissingCartProductTableMap::COL_PRODUCT_ATTRIBUTE_ID, $productAttributeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MissingCartProductTableMap::COL_PRODUCT_ATTRIBUTE_ID, $productAttributeId, $comparison);
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
     * @return ChildMissingCartProductQuery The current query, for fluid interface
     */
    public function filterByShopId($shopId = null, $comparison = null)
    {
        if (is_array($shopId)) {
            $useMinMax = false;
            if (isset($shopId['min'])) {
                $this->addUsingAlias(MissingCartProductTableMap::COL_SHOP_ID, $shopId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($shopId['max'])) {
                $this->addUsingAlias(MissingCartProductTableMap::COL_SHOP_ID, $shopId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MissingCartProductTableMap::COL_SHOP_ID, $shopId, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\MissingCart\Model\ORM\MissingCart object
     *
     * @param \Gekosale\Plugin\MissingCart\Model\ORM\MissingCart|ObjectCollection $missingCart The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildMissingCartProductQuery The current query, for fluid interface
     */
    public function filterByMissingCart($missingCart, $comparison = null)
    {
        if ($missingCart instanceof \Gekosale\Plugin\MissingCart\Model\ORM\MissingCart) {
            return $this
                ->addUsingAlias(MissingCartProductTableMap::COL_MISSING_CART_ID, $missingCart->getId(), $comparison);
        } elseif ($missingCart instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(MissingCartProductTableMap::COL_MISSING_CART_ID, $missingCart->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByMissingCart() only accepts arguments of type \Gekosale\Plugin\MissingCart\Model\ORM\MissingCart or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the MissingCart relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildMissingCartProductQuery The current query, for fluid interface
     */
    public function joinMissingCart($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('MissingCart');

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
            $this->addJoinObject($join, 'MissingCart');
        }

        return $this;
    }

    /**
     * Use the MissingCart relation MissingCart object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartQuery A secondary query class using the current class as primary query
     */
    public function useMissingCartQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinMissingCart($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'MissingCart', '\Gekosale\Plugin\MissingCart\Model\ORM\MissingCartQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Shop\Model\ORM\Shop object
     *
     * @param \Gekosale\Plugin\Shop\Model\ORM\Shop|ObjectCollection $shop The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildMissingCartProductQuery The current query, for fluid interface
     */
    public function filterByShop($shop, $comparison = null)
    {
        if ($shop instanceof \Gekosale\Plugin\Shop\Model\ORM\Shop) {
            return $this
                ->addUsingAlias(MissingCartProductTableMap::COL_SHOP_ID, $shop->getId(), $comparison);
        } elseif ($shop instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(MissingCartProductTableMap::COL_SHOP_ID, $shop->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildMissingCartProductQuery The current query, for fluid interface
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
     * Filter the query by a related \Gekosale\Plugin\Product\Model\ORM\Product object
     *
     * @param \Gekosale\Plugin\Product\Model\ORM\Product|ObjectCollection $product The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildMissingCartProductQuery The current query, for fluid interface
     */
    public function filterByProduct($product, $comparison = null)
    {
        if ($product instanceof \Gekosale\Plugin\Product\Model\ORM\Product) {
            return $this
                ->addUsingAlias(MissingCartProductTableMap::COL_PRODUCT_ID, $product->getId(), $comparison);
        } elseif ($product instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(MissingCartProductTableMap::COL_PRODUCT_ID, $product->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildMissingCartProductQuery The current query, for fluid interface
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
     * @param   ChildMissingCartProduct $missingCartProduct Object to remove from the list of results
     *
     * @return ChildMissingCartProductQuery The current query, for fluid interface
     */
    public function prune($missingCartProduct = null)
    {
        if ($missingCartProduct) {
            $this->addUsingAlias(MissingCartProductTableMap::COL_ID, $missingCartProduct->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the missing_cart_product table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(MissingCartProductTableMap::DATABASE_NAME);
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
            MissingCartProductTableMap::clearInstancePool();
            MissingCartProductTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildMissingCartProduct or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildMissingCartProduct object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(MissingCartProductTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(MissingCartProductTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        MissingCartProductTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            MissingCartProductTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // MissingCartProductQuery
