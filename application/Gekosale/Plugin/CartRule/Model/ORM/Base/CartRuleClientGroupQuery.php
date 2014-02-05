<?php

namespace Gekosale\Plugin\CartRule\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\CartRule\Model\ORM\CartRuleClientGroup as ChildCartRuleClientGroup;
use Gekosale\Plugin\CartRule\Model\ORM\CartRuleClientGroupQuery as ChildCartRuleClientGroupQuery;
use Gekosale\Plugin\CartRule\Model\ORM\Map\CartRuleClientGroupTableMap;
use Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroup;
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
 * Base class that represents a query for the 'cart_rule_client_group' table.
 *
 * 
 *
 * @method     ChildCartRuleClientGroupQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildCartRuleClientGroupQuery orderByCartRuleId($order = Criteria::ASC) Order by the cart_rule_id column
 * @method     ChildCartRuleClientGroupQuery orderByClientGroupId($order = Criteria::ASC) Order by the client_group_id column
 * @method     ChildCartRuleClientGroupQuery orderBySuffixTypeId($order = Criteria::ASC) Order by the suffix_type_id column
 * @method     ChildCartRuleClientGroupQuery orderByDiscount($order = Criteria::ASC) Order by the discount column
 * @method     ChildCartRuleClientGroupQuery orderByFreeShipping($order = Criteria::ASC) Order by the free_shipping column
 *
 * @method     ChildCartRuleClientGroupQuery groupById() Group by the id column
 * @method     ChildCartRuleClientGroupQuery groupByCartRuleId() Group by the cart_rule_id column
 * @method     ChildCartRuleClientGroupQuery groupByClientGroupId() Group by the client_group_id column
 * @method     ChildCartRuleClientGroupQuery groupBySuffixTypeId() Group by the suffix_type_id column
 * @method     ChildCartRuleClientGroupQuery groupByDiscount() Group by the discount column
 * @method     ChildCartRuleClientGroupQuery groupByFreeShipping() Group by the free_shipping column
 *
 * @method     ChildCartRuleClientGroupQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildCartRuleClientGroupQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildCartRuleClientGroupQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildCartRuleClientGroupQuery leftJoinClientGroup($relationAlias = null) Adds a LEFT JOIN clause to the query using the ClientGroup relation
 * @method     ChildCartRuleClientGroupQuery rightJoinClientGroup($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ClientGroup relation
 * @method     ChildCartRuleClientGroupQuery innerJoinClientGroup($relationAlias = null) Adds a INNER JOIN clause to the query using the ClientGroup relation
 *
 * @method     ChildCartRuleClientGroupQuery leftJoinCartRule($relationAlias = null) Adds a LEFT JOIN clause to the query using the CartRule relation
 * @method     ChildCartRuleClientGroupQuery rightJoinCartRule($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CartRule relation
 * @method     ChildCartRuleClientGroupQuery innerJoinCartRule($relationAlias = null) Adds a INNER JOIN clause to the query using the CartRule relation
 *
 * @method     ChildCartRuleClientGroupQuery leftJoinSuffixType($relationAlias = null) Adds a LEFT JOIN clause to the query using the SuffixType relation
 * @method     ChildCartRuleClientGroupQuery rightJoinSuffixType($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SuffixType relation
 * @method     ChildCartRuleClientGroupQuery innerJoinSuffixType($relationAlias = null) Adds a INNER JOIN clause to the query using the SuffixType relation
 *
 * @method     ChildCartRuleClientGroup findOne(ConnectionInterface $con = null) Return the first ChildCartRuleClientGroup matching the query
 * @method     ChildCartRuleClientGroup findOneOrCreate(ConnectionInterface $con = null) Return the first ChildCartRuleClientGroup matching the query, or a new ChildCartRuleClientGroup object populated from the query conditions when no match is found
 *
 * @method     ChildCartRuleClientGroup findOneById(int $id) Return the first ChildCartRuleClientGroup filtered by the id column
 * @method     ChildCartRuleClientGroup findOneByCartRuleId(int $cart_rule_id) Return the first ChildCartRuleClientGroup filtered by the cart_rule_id column
 * @method     ChildCartRuleClientGroup findOneByClientGroupId(int $client_group_id) Return the first ChildCartRuleClientGroup filtered by the client_group_id column
 * @method     ChildCartRuleClientGroup findOneBySuffixTypeId(int $suffix_type_id) Return the first ChildCartRuleClientGroup filtered by the suffix_type_id column
 * @method     ChildCartRuleClientGroup findOneByDiscount(string $discount) Return the first ChildCartRuleClientGroup filtered by the discount column
 * @method     ChildCartRuleClientGroup findOneByFreeShipping(int $free_shipping) Return the first ChildCartRuleClientGroup filtered by the free_shipping column
 *
 * @method     array findById(int $id) Return ChildCartRuleClientGroup objects filtered by the id column
 * @method     array findByCartRuleId(int $cart_rule_id) Return ChildCartRuleClientGroup objects filtered by the cart_rule_id column
 * @method     array findByClientGroupId(int $client_group_id) Return ChildCartRuleClientGroup objects filtered by the client_group_id column
 * @method     array findBySuffixTypeId(int $suffix_type_id) Return ChildCartRuleClientGroup objects filtered by the suffix_type_id column
 * @method     array findByDiscount(string $discount) Return ChildCartRuleClientGroup objects filtered by the discount column
 * @method     array findByFreeShipping(int $free_shipping) Return ChildCartRuleClientGroup objects filtered by the free_shipping column
 *
 */
abstract class CartRuleClientGroupQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\CartRule\Model\ORM\Base\CartRuleClientGroupQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\CartRule\\Model\\ORM\\CartRuleClientGroup', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildCartRuleClientGroupQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildCartRuleClientGroupQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\CartRule\Model\ORM\CartRuleClientGroupQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\CartRule\Model\ORM\CartRuleClientGroupQuery();
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
     * @return ChildCartRuleClientGroup|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CartRuleClientGroupTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CartRuleClientGroupTableMap::DATABASE_NAME);
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
     * @return   ChildCartRuleClientGroup A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, CART_RULE_ID, CLIENT_GROUP_ID, SUFFIX_TYPE_ID, DISCOUNT, FREE_SHIPPING FROM cart_rule_client_group WHERE ID = :p0';
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
            $obj = new ChildCartRuleClientGroup();
            $obj->hydrate($row);
            CartRuleClientGroupTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildCartRuleClientGroup|array|mixed the result, formatted by the current formatter
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
     * @return ChildCartRuleClientGroupQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CartRuleClientGroupTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildCartRuleClientGroupQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CartRuleClientGroupTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildCartRuleClientGroupQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CartRuleClientGroupTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CartRuleClientGroupTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartRuleClientGroupTableMap::COL_ID, $id, $comparison);
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
     * @return ChildCartRuleClientGroupQuery The current query, for fluid interface
     */
    public function filterByCartRuleId($cartRuleId = null, $comparison = null)
    {
        if (is_array($cartRuleId)) {
            $useMinMax = false;
            if (isset($cartRuleId['min'])) {
                $this->addUsingAlias(CartRuleClientGroupTableMap::COL_CART_RULE_ID, $cartRuleId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($cartRuleId['max'])) {
                $this->addUsingAlias(CartRuleClientGroupTableMap::COL_CART_RULE_ID, $cartRuleId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartRuleClientGroupTableMap::COL_CART_RULE_ID, $cartRuleId, $comparison);
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
     * @return ChildCartRuleClientGroupQuery The current query, for fluid interface
     */
    public function filterByClientGroupId($clientGroupId = null, $comparison = null)
    {
        if (is_array($clientGroupId)) {
            $useMinMax = false;
            if (isset($clientGroupId['min'])) {
                $this->addUsingAlias(CartRuleClientGroupTableMap::COL_CLIENT_GROUP_ID, $clientGroupId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($clientGroupId['max'])) {
                $this->addUsingAlias(CartRuleClientGroupTableMap::COL_CLIENT_GROUP_ID, $clientGroupId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartRuleClientGroupTableMap::COL_CLIENT_GROUP_ID, $clientGroupId, $comparison);
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
     * @return ChildCartRuleClientGroupQuery The current query, for fluid interface
     */
    public function filterBySuffixTypeId($suffixTypeId = null, $comparison = null)
    {
        if (is_array($suffixTypeId)) {
            $useMinMax = false;
            if (isset($suffixTypeId['min'])) {
                $this->addUsingAlias(CartRuleClientGroupTableMap::COL_SUFFIX_TYPE_ID, $suffixTypeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($suffixTypeId['max'])) {
                $this->addUsingAlias(CartRuleClientGroupTableMap::COL_SUFFIX_TYPE_ID, $suffixTypeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartRuleClientGroupTableMap::COL_SUFFIX_TYPE_ID, $suffixTypeId, $comparison);
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
     * @return ChildCartRuleClientGroupQuery The current query, for fluid interface
     */
    public function filterByDiscount($discount = null, $comparison = null)
    {
        if (is_array($discount)) {
            $useMinMax = false;
            if (isset($discount['min'])) {
                $this->addUsingAlias(CartRuleClientGroupTableMap::COL_DISCOUNT, $discount['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($discount['max'])) {
                $this->addUsingAlias(CartRuleClientGroupTableMap::COL_DISCOUNT, $discount['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartRuleClientGroupTableMap::COL_DISCOUNT, $discount, $comparison);
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
     * @return ChildCartRuleClientGroupQuery The current query, for fluid interface
     */
    public function filterByFreeShipping($freeShipping = null, $comparison = null)
    {
        if (is_array($freeShipping)) {
            $useMinMax = false;
            if (isset($freeShipping['min'])) {
                $this->addUsingAlias(CartRuleClientGroupTableMap::COL_FREE_SHIPPING, $freeShipping['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($freeShipping['max'])) {
                $this->addUsingAlias(CartRuleClientGroupTableMap::COL_FREE_SHIPPING, $freeShipping['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CartRuleClientGroupTableMap::COL_FREE_SHIPPING, $freeShipping, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroup object
     *
     * @param \Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroup|ObjectCollection $clientGroup The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCartRuleClientGroupQuery The current query, for fluid interface
     */
    public function filterByClientGroup($clientGroup, $comparison = null)
    {
        if ($clientGroup instanceof \Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroup) {
            return $this
                ->addUsingAlias(CartRuleClientGroupTableMap::COL_CLIENT_GROUP_ID, $clientGroup->getId(), $comparison);
        } elseif ($clientGroup instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CartRuleClientGroupTableMap::COL_CLIENT_GROUP_ID, $clientGroup->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildCartRuleClientGroupQuery The current query, for fluid interface
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
     * Filter the query by a related \Gekosale\Plugin\CartRule\Model\ORM\CartRule object
     *
     * @param \Gekosale\Plugin\CartRule\Model\ORM\CartRule|ObjectCollection $cartRule The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCartRuleClientGroupQuery The current query, for fluid interface
     */
    public function filterByCartRule($cartRule, $comparison = null)
    {
        if ($cartRule instanceof \Gekosale\Plugin\CartRule\Model\ORM\CartRule) {
            return $this
                ->addUsingAlias(CartRuleClientGroupTableMap::COL_CART_RULE_ID, $cartRule->getId(), $comparison);
        } elseif ($cartRule instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CartRuleClientGroupTableMap::COL_CART_RULE_ID, $cartRule->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildCartRuleClientGroupQuery The current query, for fluid interface
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
     * Filter the query by a related \Gekosale\Plugin\SuffixType\Model\ORM\SuffixType object
     *
     * @param \Gekosale\Plugin\SuffixType\Model\ORM\SuffixType|ObjectCollection $suffixType The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCartRuleClientGroupQuery The current query, for fluid interface
     */
    public function filterBySuffixType($suffixType, $comparison = null)
    {
        if ($suffixType instanceof \Gekosale\Plugin\SuffixType\Model\ORM\SuffixType) {
            return $this
                ->addUsingAlias(CartRuleClientGroupTableMap::COL_SUFFIX_TYPE_ID, $suffixType->getId(), $comparison);
        } elseif ($suffixType instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CartRuleClientGroupTableMap::COL_SUFFIX_TYPE_ID, $suffixType->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildCartRuleClientGroupQuery The current query, for fluid interface
     */
    public function joinSuffixType($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
    public function useSuffixTypeQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSuffixType($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SuffixType', '\Gekosale\Plugin\SuffixType\Model\ORM\SuffixTypeQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildCartRuleClientGroup $cartRuleClientGroup Object to remove from the list of results
     *
     * @return ChildCartRuleClientGroupQuery The current query, for fluid interface
     */
    public function prune($cartRuleClientGroup = null)
    {
        if ($cartRuleClientGroup) {
            $this->addUsingAlias(CartRuleClientGroupTableMap::COL_ID, $cartRuleClientGroup->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the cart_rule_client_group table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CartRuleClientGroupTableMap::DATABASE_NAME);
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
            CartRuleClientGroupTableMap::clearInstancePool();
            CartRuleClientGroupTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildCartRuleClientGroup or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildCartRuleClientGroup object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CartRuleClientGroupTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(CartRuleClientGroupTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        CartRuleClientGroupTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            CartRuleClientGroupTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // CartRuleClientGroupQuery
