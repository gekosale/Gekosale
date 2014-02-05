<?php

namespace Gekosale\Plugin\CartRule\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\CartRule\Model\ORM\Rule as ChildRule;
use Gekosale\Plugin\CartRule\Model\ORM\RuleQuery as ChildRuleQuery;
use Gekosale\Plugin\CartRule\Model\ORM\Map\RuleTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'rule' table.
 *
 * 
 *
 * @method     ChildRuleQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildRuleQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildRuleQuery orderByTableReferer($order = Criteria::ASC) Order by the table_referer column
 * @method     ChildRuleQuery orderByPrimaryKeyReferer($order = Criteria::ASC) Order by the primary_key_referer column
 * @method     ChildRuleQuery orderByColumnReferer($order = Criteria::ASC) Order by the column_referer column
 * @method     ChildRuleQuery orderByRuleTypeId($order = Criteria::ASC) Order by the rule_type_id column
 * @method     ChildRuleQuery orderByField($order = Criteria::ASC) Order by the field column
 *
 * @method     ChildRuleQuery groupById() Group by the id column
 * @method     ChildRuleQuery groupByName() Group by the name column
 * @method     ChildRuleQuery groupByTableReferer() Group by the table_referer column
 * @method     ChildRuleQuery groupByPrimaryKeyReferer() Group by the primary_key_referer column
 * @method     ChildRuleQuery groupByColumnReferer() Group by the column_referer column
 * @method     ChildRuleQuery groupByRuleTypeId() Group by the rule_type_id column
 * @method     ChildRuleQuery groupByField() Group by the field column
 *
 * @method     ChildRuleQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildRuleQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildRuleQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildRuleQuery leftJoinCartRuleRule($relationAlias = null) Adds a LEFT JOIN clause to the query using the CartRuleRule relation
 * @method     ChildRuleQuery rightJoinCartRuleRule($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CartRuleRule relation
 * @method     ChildRuleQuery innerJoinCartRuleRule($relationAlias = null) Adds a INNER JOIN clause to the query using the CartRuleRule relation
 *
 * @method     ChildRule findOne(ConnectionInterface $con = null) Return the first ChildRule matching the query
 * @method     ChildRule findOneOrCreate(ConnectionInterface $con = null) Return the first ChildRule matching the query, or a new ChildRule object populated from the query conditions when no match is found
 *
 * @method     ChildRule findOneById(int $id) Return the first ChildRule filtered by the id column
 * @method     ChildRule findOneByName(string $name) Return the first ChildRule filtered by the name column
 * @method     ChildRule findOneByTableReferer(string $table_referer) Return the first ChildRule filtered by the table_referer column
 * @method     ChildRule findOneByPrimaryKeyReferer(string $primary_key_referer) Return the first ChildRule filtered by the primary_key_referer column
 * @method     ChildRule findOneByColumnReferer(string $column_referer) Return the first ChildRule filtered by the column_referer column
 * @method     ChildRule findOneByRuleTypeId(int $rule_type_id) Return the first ChildRule filtered by the rule_type_id column
 * @method     ChildRule findOneByField(string $field) Return the first ChildRule filtered by the field column
 *
 * @method     array findById(int $id) Return ChildRule objects filtered by the id column
 * @method     array findByName(string $name) Return ChildRule objects filtered by the name column
 * @method     array findByTableReferer(string $table_referer) Return ChildRule objects filtered by the table_referer column
 * @method     array findByPrimaryKeyReferer(string $primary_key_referer) Return ChildRule objects filtered by the primary_key_referer column
 * @method     array findByColumnReferer(string $column_referer) Return ChildRule objects filtered by the column_referer column
 * @method     array findByRuleTypeId(int $rule_type_id) Return ChildRule objects filtered by the rule_type_id column
 * @method     array findByField(string $field) Return ChildRule objects filtered by the field column
 *
 */
abstract class RuleQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\CartRule\Model\ORM\Base\RuleQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\CartRule\\Model\\ORM\\Rule', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildRuleQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildRuleQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\CartRule\Model\ORM\RuleQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\CartRule\Model\ORM\RuleQuery();
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
     * @return ChildRule|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = RuleTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(RuleTableMap::DATABASE_NAME);
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
     * @return   ChildRule A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, NAME, TABLE_REFERER, PRIMARY_KEY_REFERER, COLUMN_REFERER, RULE_TYPE_ID, FIELD FROM rule WHERE ID = :p0';
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
            $obj = new ChildRule();
            $obj->hydrate($row);
            RuleTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildRule|array|mixed the result, formatted by the current formatter
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
     * @return ChildRuleQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(RuleTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildRuleQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(RuleTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildRuleQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(RuleTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(RuleTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RuleTableMap::COL_ID, $id, $comparison);
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
     * @return ChildRuleQuery The current query, for fluid interface
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

        return $this->addUsingAlias(RuleTableMap::COL_NAME, $name, $comparison);
    }

    /**
     * Filter the query on the table_referer column
     *
     * Example usage:
     * <code>
     * $query->filterByTableReferer('fooValue');   // WHERE table_referer = 'fooValue'
     * $query->filterByTableReferer('%fooValue%'); // WHERE table_referer LIKE '%fooValue%'
     * </code>
     *
     * @param     string $tableReferer The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildRuleQuery The current query, for fluid interface
     */
    public function filterByTableReferer($tableReferer = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($tableReferer)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $tableReferer)) {
                $tableReferer = str_replace('*', '%', $tableReferer);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(RuleTableMap::COL_TABLE_REFERER, $tableReferer, $comparison);
    }

    /**
     * Filter the query on the primary_key_referer column
     *
     * Example usage:
     * <code>
     * $query->filterByPrimaryKeyReferer('fooValue');   // WHERE primary_key_referer = 'fooValue'
     * $query->filterByPrimaryKeyReferer('%fooValue%'); // WHERE primary_key_referer LIKE '%fooValue%'
     * </code>
     *
     * @param     string $primaryKeyReferer The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildRuleQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeyReferer($primaryKeyReferer = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($primaryKeyReferer)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $primaryKeyReferer)) {
                $primaryKeyReferer = str_replace('*', '%', $primaryKeyReferer);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(RuleTableMap::COL_PRIMARY_KEY_REFERER, $primaryKeyReferer, $comparison);
    }

    /**
     * Filter the query on the column_referer column
     *
     * Example usage:
     * <code>
     * $query->filterByColumnReferer('fooValue');   // WHERE column_referer = 'fooValue'
     * $query->filterByColumnReferer('%fooValue%'); // WHERE column_referer LIKE '%fooValue%'
     * </code>
     *
     * @param     string $columnReferer The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildRuleQuery The current query, for fluid interface
     */
    public function filterByColumnReferer($columnReferer = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($columnReferer)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $columnReferer)) {
                $columnReferer = str_replace('*', '%', $columnReferer);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(RuleTableMap::COL_COLUMN_REFERER, $columnReferer, $comparison);
    }

    /**
     * Filter the query on the rule_type_id column
     *
     * Example usage:
     * <code>
     * $query->filterByRuleTypeId(1234); // WHERE rule_type_id = 1234
     * $query->filterByRuleTypeId(array(12, 34)); // WHERE rule_type_id IN (12, 34)
     * $query->filterByRuleTypeId(array('min' => 12)); // WHERE rule_type_id > 12
     * </code>
     *
     * @param     mixed $ruleTypeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildRuleQuery The current query, for fluid interface
     */
    public function filterByRuleTypeId($ruleTypeId = null, $comparison = null)
    {
        if (is_array($ruleTypeId)) {
            $useMinMax = false;
            if (isset($ruleTypeId['min'])) {
                $this->addUsingAlias(RuleTableMap::COL_RULE_TYPE_ID, $ruleTypeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($ruleTypeId['max'])) {
                $this->addUsingAlias(RuleTableMap::COL_RULE_TYPE_ID, $ruleTypeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(RuleTableMap::COL_RULE_TYPE_ID, $ruleTypeId, $comparison);
    }

    /**
     * Filter the query on the field column
     *
     * Example usage:
     * <code>
     * $query->filterByField('fooValue');   // WHERE field = 'fooValue'
     * $query->filterByField('%fooValue%'); // WHERE field LIKE '%fooValue%'
     * </code>
     *
     * @param     string $field The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildRuleQuery The current query, for fluid interface
     */
    public function filterByField($field = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($field)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $field)) {
                $field = str_replace('*', '%', $field);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(RuleTableMap::COL_FIELD, $field, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\CartRule\Model\ORM\CartRuleRule object
     *
     * @param \Gekosale\Plugin\CartRule\Model\ORM\CartRuleRule|ObjectCollection $cartRuleRule  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildRuleQuery The current query, for fluid interface
     */
    public function filterByCartRuleRule($cartRuleRule, $comparison = null)
    {
        if ($cartRuleRule instanceof \Gekosale\Plugin\CartRule\Model\ORM\CartRuleRule) {
            return $this
                ->addUsingAlias(RuleTableMap::COL_ID, $cartRuleRule->getRuleId(), $comparison);
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
     * @return ChildRuleQuery The current query, for fluid interface
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
     * Exclude object from result
     *
     * @param   ChildRule $rule Object to remove from the list of results
     *
     * @return ChildRuleQuery The current query, for fluid interface
     */
    public function prune($rule = null)
    {
        if ($rule) {
            $this->addUsingAlias(RuleTableMap::COL_ID, $rule->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the rule table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(RuleTableMap::DATABASE_NAME);
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
            RuleTableMap::clearInstancePool();
            RuleTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildRule or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildRule object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(RuleTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(RuleTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        RuleTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            RuleTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // RuleQuery
