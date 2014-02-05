<?php

namespace Gekosale\Plugin\CartRule\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\CartRule\Model\ORM\CartRuleRule as ChildCartRuleRule;
use Gekosale\Plugin\CartRule\Model\ORM\CartRuleRuleQuery as ChildCartRuleRuleQuery;
use Gekosale\Plugin\CartRule\Model\ORM\Map\CartRuleRuleTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'cart_rule_rule' table.
 *
 * 
 *
 * @method     ChildCartRuleRuleQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildCartRuleRuleQuery orderByRuleId($order = Criteria::ASC) Order by the rule_id column
 * @method     ChildCartRuleRuleQuery orderByCartRuleId($order = Criteria::ASC) Order by the cart_rule_id column
 * @method     ChildCartRuleRuleQuery orderByPkid($order = Criteria::ASC) Order by the pkid column
 * @method     ChildCartRuleRuleQuery orderByPriceFrom($order = Criteria::ASC) Order by the price_from column
 * @method     ChildCartRuleRuleQuery orderByPriceTo($order = Criteria::ASC) Order by the price_to column
 *
 * @method     ChildCartRuleRuleQuery groupById() Group by the id column
 * @method     ChildCartRuleRuleQuery groupByRuleId() Group by the rule_id column
 * @method     ChildCartRuleRuleQuery groupByCartRuleId() Group by the cart_rule_id column
 * @method     ChildCartRuleRuleQuery groupByPkid() Group by the pkid column
 * @method     ChildCartRuleRuleQuery groupByPriceFrom() Group by the price_from column
 * @method     ChildCartRuleRuleQuery groupByPriceTo() Group by the price_to column
 *
 * @method     ChildCartRuleRuleQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildCartRuleRuleQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildCartRuleRuleQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildCartRuleRuleQuery leftJoinRule($relationAlias = null) Adds a LEFT JOIN clause to the query using the Rule relation
 * @method     ChildCartRuleRuleQuery rightJoinRule($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Rule relation
 * @method     ChildCartRuleRuleQuery innerJoinRule($relationAlias = null) Adds a INNER JOIN clause to the query using the Rule relation
 *
 * @method     ChildCartRuleRuleQuery leftJoinCartRule($relationAlias = null) Adds a LEFT JOIN clause to the query using the CartRule relation
 * @method     ChildCartRuleRuleQuery rightJoinCartRule($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CartRule relation
 * @method     ChildCartRuleRuleQuery innerJoinCartRule($relationAlias = null) Adds a INNER JOIN clause to the query using the CartRule relation
 *
 * @method     ChildCartRuleRule findOne(ConnectionInterface $con = null) Return the first ChildCartRuleRule matching the query
 * @method     ChildCartRuleRule findOneOrCreate(ConnectionInterface $con = null) Return the first ChildCartRuleRule matching the query, or a new ChildCartRuleRule object populated from the query conditions when no match is found
 *
 * @method     ChildCartRuleRule findOneById(int $id) Return the first ChildCartRuleRule filtered by the id column
 * @method     ChildCartRuleRule findOneByRuleId(int $rule_id) Return the first ChildCartRuleRule filtered by the rule_id column
 * @method     ChildCartRuleRule findOneByCartRuleId(int $cart_rule_id) Return the first ChildCartRuleRule filtered by the cart_rule_id column
 * @method     ChildCartRuleRule findOneByPkid(int $pkid) Return the first ChildCartRuleRule filtered by the pkid column
 * @method     ChildCartRuleRule findOneByPriceFrom(string $price_from) Return the first ChildCartRuleRule filtered by the price_from column
 * @method     ChildCartRuleRule findOneByPriceTo(string $price_to) Return the first ChildCartRuleRule filtered by the price_to column
 *
 * @method     array findById(int $id) Return ChildCartRuleRule objects filtered by the id column
 * @method     array findByRuleId(int $rule_id) Return ChildCartRuleRule objects filtered by the rule_id column
 * @method     array findByCartRuleId(int $cart_rule_id) Return ChildCartRuleRule objects filtered by the cart_rule_id column
 * @method     array findByPkid(int $pkid) Return ChildCartRuleRule objects filtered by the pkid column
 * @method     array findByPriceFrom(string $price_from) Return ChildCartRuleRule objects filtered by the price_from column
 * @method     array findByPriceTo(string $price_to) Return ChildCartRuleRule objects filtered by the price_to column
 *
 */
abstract class CartRuleRuleQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\CartRule\Model\ORM\Base\CartRuleRuleQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\CartRule\\Model\\ORM\\CartRuleRule', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildCartRuleRuleQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildCartRuleRuleQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\CartRule\Model\ORM\CartRuleRuleQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\CartRule\Model\ORM\CartRuleRuleQuery();
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
     * @return ChildCartRuleRule|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CartRuleRuleTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CartRuleRuleTableMap::DATABASE_NAME);
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
     * @return   ChildCartRuleRule A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, RULE_ID, CART_RULE_ID, PKID, PRICE_FROM, PRICE_TO FROM cart_rule_rule WHERE ID = :p0';
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
            $obj = new ChildCartRuleRule();
            $obj->hydrate($row);
            CartRuleRuleTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildCartRuleRule|array|mixed the result, formatted by the current formatter
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
     * @return ChildCartRuleRuleQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CartRuleRuleTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildCartRuleRuleQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CartRuleRuleTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildCartRuleRuleQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CartRuleRuleTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CartRuleRuleTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartRuleRuleTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the rule_id column
     *
     * Example usage:
     * <code>
     * $query->filterByRuleId(1234); // WHERE rule_id = 1234
     * $query->filterByRuleId(array(12, 34)); // WHERE rule_id IN (12, 34)
     * $query->filterByRuleId(array('min' => 12)); // WHERE rule_id > 12
     * </code>
     *
     * @see       filterByRule()
     *
     * @param     mixed $ruleId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCartRuleRuleQuery The current query, for fluid interface
     */
    public function filterByRuleId($ruleId = null, $comparison = null)
    {
        if (is_array($ruleId)) {
            $useMinMax = false;
            if (isset($ruleId['min'])) {
                $this->addUsingAlias(CartRuleRuleTableMap::COL_RULE_ID, $ruleId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($ruleId['max'])) {
                $this->addUsingAlias(CartRuleRuleTableMap::COL_RULE_ID, $ruleId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartRuleRuleTableMap::COL_RULE_ID, $ruleId, $comparison);
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
     * @see       filterByCartRule()
     *
     * @param     mixed $cartRuleId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCartRuleRuleQuery The current query, for fluid interface
     */
    public function filterByCartRuleId($cartRuleId = null, $comparison = null)
    {
        if (is_array($cartRuleId)) {
            $useMinMax = false;
            if (isset($cartRuleId['min'])) {
                $this->addUsingAlias(CartRuleRuleTableMap::COL_CART_RULE_ID, $cartRuleId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($cartRuleId['max'])) {
                $this->addUsingAlias(CartRuleRuleTableMap::COL_CART_RULE_ID, $cartRuleId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartRuleRuleTableMap::COL_CART_RULE_ID, $cartRuleId, $comparison);
    }

    /**
     * Filter the query on the pkid column
     *
     * Example usage:
     * <code>
     * $query->filterByPkid(1234); // WHERE pkid = 1234
     * $query->filterByPkid(array(12, 34)); // WHERE pkid IN (12, 34)
     * $query->filterByPkid(array('min' => 12)); // WHERE pkid > 12
     * </code>
     *
     * @param     mixed $pkid The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCartRuleRuleQuery The current query, for fluid interface
     */
    public function filterByPkid($pkid = null, $comparison = null)
    {
        if (is_array($pkid)) {
            $useMinMax = false;
            if (isset($pkid['min'])) {
                $this->addUsingAlias(CartRuleRuleTableMap::COL_PKID, $pkid['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($pkid['max'])) {
                $this->addUsingAlias(CartRuleRuleTableMap::COL_PKID, $pkid['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartRuleRuleTableMap::COL_PKID, $pkid, $comparison);
    }

    /**
     * Filter the query on the price_from column
     *
     * Example usage:
     * <code>
     * $query->filterByPriceFrom(1234); // WHERE price_from = 1234
     * $query->filterByPriceFrom(array(12, 34)); // WHERE price_from IN (12, 34)
     * $query->filterByPriceFrom(array('min' => 12)); // WHERE price_from > 12
     * </code>
     *
     * @param     mixed $priceFrom The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCartRuleRuleQuery The current query, for fluid interface
     */
    public function filterByPriceFrom($priceFrom = null, $comparison = null)
    {
        if (is_array($priceFrom)) {
            $useMinMax = false;
            if (isset($priceFrom['min'])) {
                $this->addUsingAlias(CartRuleRuleTableMap::COL_PRICE_FROM, $priceFrom['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($priceFrom['max'])) {
                $this->addUsingAlias(CartRuleRuleTableMap::COL_PRICE_FROM, $priceFrom['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartRuleRuleTableMap::COL_PRICE_FROM, $priceFrom, $comparison);
    }

    /**
     * Filter the query on the price_to column
     *
     * Example usage:
     * <code>
     * $query->filterByPriceTo(1234); // WHERE price_to = 1234
     * $query->filterByPriceTo(array(12, 34)); // WHERE price_to IN (12, 34)
     * $query->filterByPriceTo(array('min' => 12)); // WHERE price_to > 12
     * </code>
     *
     * @param     mixed $priceTo The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCartRuleRuleQuery The current query, for fluid interface
     */
    public function filterByPriceTo($priceTo = null, $comparison = null)
    {
        if (is_array($priceTo)) {
            $useMinMax = false;
            if (isset($priceTo['min'])) {
                $this->addUsingAlias(CartRuleRuleTableMap::COL_PRICE_TO, $priceTo['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($priceTo['max'])) {
                $this->addUsingAlias(CartRuleRuleTableMap::COL_PRICE_TO, $priceTo['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartRuleRuleTableMap::COL_PRICE_TO, $priceTo, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\CartRule\Model\ORM\Rule object
     *
     * @param \Gekosale\Plugin\CartRule\Model\ORM\Rule|ObjectCollection $rule The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCartRuleRuleQuery The current query, for fluid interface
     */
    public function filterByRule($rule, $comparison = null)
    {
        if ($rule instanceof \Gekosale\Plugin\CartRule\Model\ORM\Rule) {
            return $this
                ->addUsingAlias(CartRuleRuleTableMap::COL_RULE_ID, $rule->getId(), $comparison);
        } elseif ($rule instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CartRuleRuleTableMap::COL_RULE_ID, $rule->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByRule() only accepts arguments of type \Gekosale\Plugin\CartRule\Model\ORM\Rule or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Rule relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCartRuleRuleQuery The current query, for fluid interface
     */
    public function joinRule($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Rule');

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
            $this->addJoinObject($join, 'Rule');
        }

        return $this;
    }

    /**
     * Use the Rule relation Rule object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\CartRule\Model\ORM\RuleQuery A secondary query class using the current class as primary query
     */
    public function useRuleQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinRule($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Rule', '\Gekosale\Plugin\CartRule\Model\ORM\RuleQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\CartRule\Model\ORM\CartRule object
     *
     * @param \Gekosale\Plugin\CartRule\Model\ORM\CartRule|ObjectCollection $cartRule The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCartRuleRuleQuery The current query, for fluid interface
     */
    public function filterByCartRule($cartRule, $comparison = null)
    {
        if ($cartRule instanceof \Gekosale\Plugin\CartRule\Model\ORM\CartRule) {
            return $this
                ->addUsingAlias(CartRuleRuleTableMap::COL_CART_RULE_ID, $cartRule->getId(), $comparison);
        } elseif ($cartRule instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CartRuleRuleTableMap::COL_CART_RULE_ID, $cartRule->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCartRule() only accepts arguments of type \Gekosale\Plugin\CartRule\Model\ORM\CartRule or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CartRule relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCartRuleRuleQuery The current query, for fluid interface
     */
    public function joinCartRule($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CartRule');

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
            $this->addJoinObject($join, 'CartRule');
        }

        return $this;
    }

    /**
     * Use the CartRule relation CartRule object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\CartRule\Model\ORM\CartRuleQuery A secondary query class using the current class as primary query
     */
    public function useCartRuleQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCartRule($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CartRule', '\Gekosale\Plugin\CartRule\Model\ORM\CartRuleQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildCartRuleRule $cartRuleRule Object to remove from the list of results
     *
     * @return ChildCartRuleRuleQuery The current query, for fluid interface
     */
    public function prune($cartRuleRule = null)
    {
        if ($cartRuleRule) {
            $this->addUsingAlias(CartRuleRuleTableMap::COL_ID, $cartRuleRule->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the cart_rule_rule table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CartRuleRuleTableMap::DATABASE_NAME);
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
            CartRuleRuleTableMap::clearInstancePool();
            CartRuleRuleTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildCartRuleRule or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildCartRuleRule object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CartRuleRuleTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(CartRuleRuleTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        CartRuleRuleTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            CartRuleRuleTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // CartRuleRuleQuery
