<?php

namespace Gekosale\Plugin\CartRule\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\CartRule\Model\ORM\CartRule as ChildCartRule;
use Gekosale\Plugin\CartRule\Model\ORM\CartRuleQuery as ChildCartRuleQuery;
use Gekosale\Plugin\CartRule\Model\ORM\Map\CartRuleTableMap;
use Gekosale\Plugin\SuffixType\Model\ORM\SuffixType;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'cart_rule' table.
 *
 * 
 *
 * @method     ChildCartRuleQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildCartRuleQuery orderByHierarchy($order = Criteria::ASC) Order by the hierarchy column
 * @method     ChildCartRuleQuery orderBySuffixTypeId($order = Criteria::ASC) Order by the suffix_type_id column
 * @method     ChildCartRuleQuery orderByDiscount($order = Criteria::ASC) Order by the discount column
 * @method     ChildCartRuleQuery orderByFreeShipping($order = Criteria::ASC) Order by the free_shipping column
 * @method     ChildCartRuleQuery orderByDateFrom($order = Criteria::ASC) Order by the date_from column
 * @method     ChildCartRuleQuery orderByDateTo($order = Criteria::ASC) Order by the date_to column
 * @method     ChildCartRuleQuery orderByDiscountForAll($order = Criteria::ASC) Order by the discount_for_all column
 *
 * @method     ChildCartRuleQuery groupById() Group by the id column
 * @method     ChildCartRuleQuery groupByHierarchy() Group by the hierarchy column
 * @method     ChildCartRuleQuery groupBySuffixTypeId() Group by the suffix_type_id column
 * @method     ChildCartRuleQuery groupByDiscount() Group by the discount column
 * @method     ChildCartRuleQuery groupByFreeShipping() Group by the free_shipping column
 * @method     ChildCartRuleQuery groupByDateFrom() Group by the date_from column
 * @method     ChildCartRuleQuery groupByDateTo() Group by the date_to column
 * @method     ChildCartRuleQuery groupByDiscountForAll() Group by the discount_for_all column
 *
 * @method     ChildCartRuleQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildCartRuleQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildCartRuleQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildCartRuleQuery leftJoinSuffixType($relationAlias = null) Adds a LEFT JOIN clause to the query using the SuffixType relation
 * @method     ChildCartRuleQuery rightJoinSuffixType($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SuffixType relation
 * @method     ChildCartRuleQuery innerJoinSuffixType($relationAlias = null) Adds a INNER JOIN clause to the query using the SuffixType relation
 *
 * @method     ChildCartRuleQuery leftJoinCartRuleClientGroup($relationAlias = null) Adds a LEFT JOIN clause to the query using the CartRuleClientGroup relation
 * @method     ChildCartRuleQuery rightJoinCartRuleClientGroup($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CartRuleClientGroup relation
 * @method     ChildCartRuleQuery innerJoinCartRuleClientGroup($relationAlias = null) Adds a INNER JOIN clause to the query using the CartRuleClientGroup relation
 *
 * @method     ChildCartRuleQuery leftJoinCartRuleRule($relationAlias = null) Adds a LEFT JOIN clause to the query using the CartRuleRule relation
 * @method     ChildCartRuleQuery rightJoinCartRuleRule($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CartRuleRule relation
 * @method     ChildCartRuleQuery innerJoinCartRuleRule($relationAlias = null) Adds a INNER JOIN clause to the query using the CartRuleRule relation
 *
 * @method     ChildCartRuleQuery leftJoinCartRuleShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the CartRuleShop relation
 * @method     ChildCartRuleQuery rightJoinCartRuleShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CartRuleShop relation
 * @method     ChildCartRuleQuery innerJoinCartRuleShop($relationAlias = null) Adds a INNER JOIN clause to the query using the CartRuleShop relation
 *
 * @method     ChildCartRule findOne(ConnectionInterface $con = null) Return the first ChildCartRule matching the query
 * @method     ChildCartRule findOneOrCreate(ConnectionInterface $con = null) Return the first ChildCartRule matching the query, or a new ChildCartRule object populated from the query conditions when no match is found
 *
 * @method     ChildCartRule findOneById(int $id) Return the first ChildCartRule filtered by the id column
 * @method     ChildCartRule findOneByHierarchy(int $hierarchy) Return the first ChildCartRule filtered by the hierarchy column
 * @method     ChildCartRule findOneBySuffixTypeId(int $suffix_type_id) Return the first ChildCartRule filtered by the suffix_type_id column
 * @method     ChildCartRule findOneByDiscount(string $discount) Return the first ChildCartRule filtered by the discount column
 * @method     ChildCartRule findOneByFreeShipping(int $free_shipping) Return the first ChildCartRule filtered by the free_shipping column
 * @method     ChildCartRule findOneByDateFrom(string $date_from) Return the first ChildCartRule filtered by the date_from column
 * @method     ChildCartRule findOneByDateTo(string $date_to) Return the first ChildCartRule filtered by the date_to column
 * @method     ChildCartRule findOneByDiscountForAll(int $discount_for_all) Return the first ChildCartRule filtered by the discount_for_all column
 *
 * @method     array findById(int $id) Return ChildCartRule objects filtered by the id column
 * @method     array findByHierarchy(int $hierarchy) Return ChildCartRule objects filtered by the hierarchy column
 * @method     array findBySuffixTypeId(int $suffix_type_id) Return ChildCartRule objects filtered by the suffix_type_id column
 * @method     array findByDiscount(string $discount) Return ChildCartRule objects filtered by the discount column
 * @method     array findByFreeShipping(int $free_shipping) Return ChildCartRule objects filtered by the free_shipping column
 * @method     array findByDateFrom(string $date_from) Return ChildCartRule objects filtered by the date_from column
 * @method     array findByDateTo(string $date_to) Return ChildCartRule objects filtered by the date_to column
 * @method     array findByDiscountForAll(int $discount_for_all) Return ChildCartRule objects filtered by the discount_for_all column
 *
 */
abstract class CartRuleQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\CartRule\Model\ORM\Base\CartRuleQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\CartRule\\Model\\ORM\\CartRule', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildCartRuleQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildCartRuleQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\CartRule\Model\ORM\CartRuleQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\CartRule\Model\ORM\CartRuleQuery();
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
     * @return ChildCartRule|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CartRuleTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CartRuleTableMap::DATABASE_NAME);
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
     * @return   ChildCartRule A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, HIERARCHY, SUFFIX_TYPE_ID, DISCOUNT, FREE_SHIPPING, DATE_FROM, DATE_TO, DISCOUNT_FOR_ALL FROM cart_rule WHERE ID = :p0';
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
            $obj = new ChildCartRule();
            $obj->hydrate($row);
            CartRuleTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildCartRule|array|mixed the result, formatted by the current formatter
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
     * @return ChildCartRuleQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CartRuleTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildCartRuleQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CartRuleTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildCartRuleQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CartRuleTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CartRuleTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartRuleTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the hierarchy column
     *
     * Example usage:
     * <code>
     * $query->filterByHierarchy(1234); // WHERE hierarchy = 1234
     * $query->filterByHierarchy(array(12, 34)); // WHERE hierarchy IN (12, 34)
     * $query->filterByHierarchy(array('min' => 12)); // WHERE hierarchy > 12
     * </code>
     *
     * @param     mixed $hierarchy The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCartRuleQuery The current query, for fluid interface
     */
    public function filterByHierarchy($hierarchy = null, $comparison = null)
    {
        if (is_array($hierarchy)) {
            $useMinMax = false;
            if (isset($hierarchy['min'])) {
                $this->addUsingAlias(CartRuleTableMap::COL_HIERARCHY, $hierarchy['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($hierarchy['max'])) {
                $this->addUsingAlias(CartRuleTableMap::COL_HIERARCHY, $hierarchy['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartRuleTableMap::COL_HIERARCHY, $hierarchy, $comparison);
    }

    /**
     * Filter the query on the suffix_type_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySuffixTypeId(1234); // WHERE suffix_type_id = 1234
     * $query->filterBySuffixTypeId(array(12, 34)); // WHERE suffix_type_id IN (12, 34)
     * $query->filterBySuffixTypeId(array('min' => 12)); // WHERE suffix_type_id > 12
     * </code>
     *
     * @see       filterBySuffixType()
     *
     * @param     mixed $suffixTypeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCartRuleQuery The current query, for fluid interface
     */
    public function filterBySuffixTypeId($suffixTypeId = null, $comparison = null)
    {
        if (is_array($suffixTypeId)) {
            $useMinMax = false;
            if (isset($suffixTypeId['min'])) {
                $this->addUsingAlias(CartRuleTableMap::COL_SUFFIX_TYPE_ID, $suffixTypeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($suffixTypeId['max'])) {
                $this->addUsingAlias(CartRuleTableMap::COL_SUFFIX_TYPE_ID, $suffixTypeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartRuleTableMap::COL_SUFFIX_TYPE_ID, $suffixTypeId, $comparison);
    }

    /**
     * Filter the query on the discount column
     *
     * Example usage:
     * <code>
     * $query->filterByDiscount(1234); // WHERE discount = 1234
     * $query->filterByDiscount(array(12, 34)); // WHERE discount IN (12, 34)
     * $query->filterByDiscount(array('min' => 12)); // WHERE discount > 12
     * </code>
     *
     * @param     mixed $discount The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCartRuleQuery The current query, for fluid interface
     */
    public function filterByDiscount($discount = null, $comparison = null)
    {
        if (is_array($discount)) {
            $useMinMax = false;
            if (isset($discount['min'])) {
                $this->addUsingAlias(CartRuleTableMap::COL_DISCOUNT, $discount['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($discount['max'])) {
                $this->addUsingAlias(CartRuleTableMap::COL_DISCOUNT, $discount['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartRuleTableMap::COL_DISCOUNT, $discount, $comparison);
    }

    /**
     * Filter the query on the free_shipping column
     *
     * Example usage:
     * <code>
     * $query->filterByFreeShipping(1234); // WHERE free_shipping = 1234
     * $query->filterByFreeShipping(array(12, 34)); // WHERE free_shipping IN (12, 34)
     * $query->filterByFreeShipping(array('min' => 12)); // WHERE free_shipping > 12
     * </code>
     *
     * @param     mixed $freeShipping The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCartRuleQuery The current query, for fluid interface
     */
    public function filterByFreeShipping($freeShipping = null, $comparison = null)
    {
        if (is_array($freeShipping)) {
            $useMinMax = false;
            if (isset($freeShipping['min'])) {
                $this->addUsingAlias(CartRuleTableMap::COL_FREE_SHIPPING, $freeShipping['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($freeShipping['max'])) {
                $this->addUsingAlias(CartRuleTableMap::COL_FREE_SHIPPING, $freeShipping['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartRuleTableMap::COL_FREE_SHIPPING, $freeShipping, $comparison);
    }

    /**
     * Filter the query on the date_from column
     *
     * Example usage:
     * <code>
     * $query->filterByDateFrom('2011-03-14'); // WHERE date_from = '2011-03-14'
     * $query->filterByDateFrom('now'); // WHERE date_from = '2011-03-14'
     * $query->filterByDateFrom(array('max' => 'yesterday')); // WHERE date_from > '2011-03-13'
     * </code>
     *
     * @param     mixed $dateFrom The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCartRuleQuery The current query, for fluid interface
     */
    public function filterByDateFrom($dateFrom = null, $comparison = null)
    {
        if (is_array($dateFrom)) {
            $useMinMax = false;
            if (isset($dateFrom['min'])) {
                $this->addUsingAlias(CartRuleTableMap::COL_DATE_FROM, $dateFrom['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dateFrom['max'])) {
                $this->addUsingAlias(CartRuleTableMap::COL_DATE_FROM, $dateFrom['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartRuleTableMap::COL_DATE_FROM, $dateFrom, $comparison);
    }

    /**
     * Filter the query on the date_to column
     *
     * Example usage:
     * <code>
     * $query->filterByDateTo('2011-03-14'); // WHERE date_to = '2011-03-14'
     * $query->filterByDateTo('now'); // WHERE date_to = '2011-03-14'
     * $query->filterByDateTo(array('max' => 'yesterday')); // WHERE date_to > '2011-03-13'
     * </code>
     *
     * @param     mixed $dateTo The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCartRuleQuery The current query, for fluid interface
     */
    public function filterByDateTo($dateTo = null, $comparison = null)
    {
        if (is_array($dateTo)) {
            $useMinMax = false;
            if (isset($dateTo['min'])) {
                $this->addUsingAlias(CartRuleTableMap::COL_DATE_TO, $dateTo['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dateTo['max'])) {
                $this->addUsingAlias(CartRuleTableMap::COL_DATE_TO, $dateTo['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartRuleTableMap::COL_DATE_TO, $dateTo, $comparison);
    }

    /**
     * Filter the query on the discount_for_all column
     *
     * Example usage:
     * <code>
     * $query->filterByDiscountForAll(1234); // WHERE discount_for_all = 1234
     * $query->filterByDiscountForAll(array(12, 34)); // WHERE discount_for_all IN (12, 34)
     * $query->filterByDiscountForAll(array('min' => 12)); // WHERE discount_for_all > 12
     * </code>
     *
     * @param     mixed $discountForAll The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCartRuleQuery The current query, for fluid interface
     */
    public function filterByDiscountForAll($discountForAll = null, $comparison = null)
    {
        if (is_array($discountForAll)) {
            $useMinMax = false;
            if (isset($discountForAll['min'])) {
                $this->addUsingAlias(CartRuleTableMap::COL_DISCOUNT_FOR_ALL, $discountForAll['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($discountForAll['max'])) {
                $this->addUsingAlias(CartRuleTableMap::COL_DISCOUNT_FOR_ALL, $discountForAll['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartRuleTableMap::COL_DISCOUNT_FOR_ALL, $discountForAll, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\SuffixType\Model\ORM\SuffixType object
     *
     * @param \Gekosale\Plugin\SuffixType\Model\ORM\SuffixType|ObjectCollection $suffixType The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCartRuleQuery The current query, for fluid interface
     */
    public function filterBySuffixType($suffixType, $comparison = null)
    {
        if ($suffixType instanceof \Gekosale\Plugin\SuffixType\Model\ORM\SuffixType) {
            return $this
                ->addUsingAlias(CartRuleTableMap::COL_SUFFIX_TYPE_ID, $suffixType->getId(), $comparison);
        } elseif ($suffixType instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CartRuleTableMap::COL_SUFFIX_TYPE_ID, $suffixType->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterBySuffixType() only accepts arguments of type \Gekosale\Plugin\SuffixType\Model\ORM\SuffixType or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SuffixType relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCartRuleQuery The current query, for fluid interface
     */
    public function joinSuffixType($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SuffixType');

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
            $this->addJoinObject($join, 'SuffixType');
        }

        return $this;
    }

    /**
     * Use the SuffixType relation SuffixType object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\SuffixType\Model\ORM\SuffixTypeQuery A secondary query class using the current class as primary query
     */
    public function useSuffixTypeQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinSuffixType($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SuffixType', '\Gekosale\Plugin\SuffixType\Model\ORM\SuffixTypeQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\CartRule\Model\ORM\CartRuleClientGroup object
     *
     * @param \Gekosale\Plugin\CartRule\Model\ORM\CartRuleClientGroup|ObjectCollection $cartRuleClientGroup  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCartRuleQuery The current query, for fluid interface
     */
    public function filterByCartRuleClientGroup($cartRuleClientGroup, $comparison = null)
    {
        if ($cartRuleClientGroup instanceof \Gekosale\Plugin\CartRule\Model\ORM\CartRuleClientGroup) {
            return $this
                ->addUsingAlias(CartRuleTableMap::COL_ID, $cartRuleClientGroup->getCartRuleId(), $comparison);
        } elseif ($cartRuleClientGroup instanceof ObjectCollection) {
            return $this
                ->useCartRuleClientGroupQuery()
                ->filterByPrimaryKeys($cartRuleClientGroup->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCartRuleClientGroup() only accepts arguments of type \Gekosale\Plugin\CartRule\Model\ORM\CartRuleClientGroup or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CartRuleClientGroup relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCartRuleQuery The current query, for fluid interface
     */
    public function joinCartRuleClientGroup($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CartRuleClientGroup');

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
            $this->addJoinObject($join, 'CartRuleClientGroup');
        }

        return $this;
    }

    /**
     * Use the CartRuleClientGroup relation CartRuleClientGroup object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\CartRule\Model\ORM\CartRuleClientGroupQuery A secondary query class using the current class as primary query
     */
    public function useCartRuleClientGroupQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCartRuleClientGroup($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CartRuleClientGroup', '\Gekosale\Plugin\CartRule\Model\ORM\CartRuleClientGroupQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\CartRule\Model\ORM\CartRuleRule object
     *
     * @param \Gekosale\Plugin\CartRule\Model\ORM\CartRuleRule|ObjectCollection $cartRuleRule  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCartRuleQuery The current query, for fluid interface
     */
    public function filterByCartRuleRule($cartRuleRule, $comparison = null)
    {
        if ($cartRuleRule instanceof \Gekosale\Plugin\CartRule\Model\ORM\CartRuleRule) {
            return $this
                ->addUsingAlias(CartRuleTableMap::COL_ID, $cartRuleRule->getCartRuleId(), $comparison);
        } elseif ($cartRuleRule instanceof ObjectCollection) {
            return $this
                ->useCartRuleRuleQuery()
                ->filterByPrimaryKeys($cartRuleRule->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCartRuleRule() only accepts arguments of type \Gekosale\Plugin\CartRule\Model\ORM\CartRuleRule or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CartRuleRule relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCartRuleQuery The current query, for fluid interface
     */
    public function joinCartRuleRule($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CartRuleRule');

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
            $this->addJoinObject($join, 'CartRuleRule');
        }

        return $this;
    }

    /**
     * Use the CartRuleRule relation CartRuleRule object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\CartRule\Model\ORM\CartRuleRuleQuery A secondary query class using the current class as primary query
     */
    public function useCartRuleRuleQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCartRuleRule($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CartRuleRule', '\Gekosale\Plugin\CartRule\Model\ORM\CartRuleRuleQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\CartRule\Model\ORM\CartRuleShop object
     *
     * @param \Gekosale\Plugin\CartRule\Model\ORM\CartRuleShop|ObjectCollection $cartRuleShop  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCartRuleQuery The current query, for fluid interface
     */
    public function filterByCartRuleShop($cartRuleShop, $comparison = null)
    {
        if ($cartRuleShop instanceof \Gekosale\Plugin\CartRule\Model\ORM\CartRuleShop) {
            return $this
                ->addUsingAlias(CartRuleTableMap::COL_ID, $cartRuleShop->getCartRuleId(), $comparison);
        } elseif ($cartRuleShop instanceof ObjectCollection) {
            return $this
                ->useCartRuleShopQuery()
                ->filterByPrimaryKeys($cartRuleShop->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCartRuleShop() only accepts arguments of type \Gekosale\Plugin\CartRule\Model\ORM\CartRuleShop or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CartRuleShop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCartRuleQuery The current query, for fluid interface
     */
    public function joinCartRuleShop($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CartRuleShop');

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
            $this->addJoinObject($join, 'CartRuleShop');
        }

        return $this;
    }

    /**
     * Use the CartRuleShop relation CartRuleShop object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\CartRule\Model\ORM\CartRuleShopQuery A secondary query class using the current class as primary query
     */
    public function useCartRuleShopQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCartRuleShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CartRuleShop', '\Gekosale\Plugin\CartRule\Model\ORM\CartRuleShopQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildCartRule $cartRule Object to remove from the list of results
     *
     * @return ChildCartRuleQuery The current query, for fluid interface
     */
    public function prune($cartRule = null)
    {
        if ($cartRule) {
            $this->addUsingAlias(CartRuleTableMap::COL_ID, $cartRule->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the cart_rule table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CartRuleTableMap::DATABASE_NAME);
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
            CartRuleTableMap::clearInstancePool();
            CartRuleTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildCartRule or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildCartRule object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CartRuleTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(CartRuleTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        CartRuleTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            CartRuleTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // CartRuleQuery
