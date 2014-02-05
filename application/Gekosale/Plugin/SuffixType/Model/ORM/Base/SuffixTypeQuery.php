<?php

namespace Gekosale\Plugin\SuffixType\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\CartRule\Model\ORM\CartRule;
use Gekosale\Plugin\CartRule\Model\ORM\CartRuleClientGroup;
use Gekosale\Plugin\SuffixType\Model\ORM\SuffixType as ChildSuffixType;
use Gekosale\Plugin\SuffixType\Model\ORM\SuffixTypeQuery as ChildSuffixTypeQuery;
use Gekosale\Plugin\SuffixType\Model\ORM\Map\SuffixTypeTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'suffix_type' table.
 *
 * 
 *
 * @method     ChildSuffixTypeQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildSuffixTypeQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildSuffixTypeQuery orderBySymbol($order = Criteria::ASC) Order by the symbol column
 *
 * @method     ChildSuffixTypeQuery groupById() Group by the id column
 * @method     ChildSuffixTypeQuery groupByName() Group by the name column
 * @method     ChildSuffixTypeQuery groupBySymbol() Group by the symbol column
 *
 * @method     ChildSuffixTypeQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSuffixTypeQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSuffixTypeQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSuffixTypeQuery leftJoinCartRule($relationAlias = null) Adds a LEFT JOIN clause to the query using the CartRule relation
 * @method     ChildSuffixTypeQuery rightJoinCartRule($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CartRule relation
 * @method     ChildSuffixTypeQuery innerJoinCartRule($relationAlias = null) Adds a INNER JOIN clause to the query using the CartRule relation
 *
 * @method     ChildSuffixTypeQuery leftJoinCartRuleClientGroup($relationAlias = null) Adds a LEFT JOIN clause to the query using the CartRuleClientGroup relation
 * @method     ChildSuffixTypeQuery rightJoinCartRuleClientGroup($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CartRuleClientGroup relation
 * @method     ChildSuffixTypeQuery innerJoinCartRuleClientGroup($relationAlias = null) Adds a INNER JOIN clause to the query using the CartRuleClientGroup relation
 *
 * @method     ChildSuffixType findOne(ConnectionInterface $con = null) Return the first ChildSuffixType matching the query
 * @method     ChildSuffixType findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSuffixType matching the query, or a new ChildSuffixType object populated from the query conditions when no match is found
 *
 * @method     ChildSuffixType findOneById(int $id) Return the first ChildSuffixType filtered by the id column
 * @method     ChildSuffixType findOneByName(string $name) Return the first ChildSuffixType filtered by the name column
 * @method     ChildSuffixType findOneBySymbol(string $symbol) Return the first ChildSuffixType filtered by the symbol column
 *
 * @method     array findById(int $id) Return ChildSuffixType objects filtered by the id column
 * @method     array findByName(string $name) Return ChildSuffixType objects filtered by the name column
 * @method     array findBySymbol(string $symbol) Return ChildSuffixType objects filtered by the symbol column
 *
 */
abstract class SuffixTypeQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\SuffixType\Model\ORM\Base\SuffixTypeQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\SuffixType\\Model\\ORM\\SuffixType', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildSuffixTypeQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildSuffixTypeQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\SuffixType\Model\ORM\SuffixTypeQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\SuffixType\Model\ORM\SuffixTypeQuery();
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
     * @return ChildSuffixType|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SuffixTypeTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SuffixTypeTableMap::DATABASE_NAME);
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
     * @return   ChildSuffixType A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, NAME, SYMBOL FROM suffix_type WHERE ID = :p0';
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
            $obj = new ChildSuffixType();
            $obj->hydrate($row);
            SuffixTypeTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildSuffixType|array|mixed the result, formatted by the current formatter
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
     * @return ChildSuffixTypeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SuffixTypeTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildSuffixTypeQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SuffixTypeTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildSuffixTypeQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(SuffixTypeTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(SuffixTypeTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SuffixTypeTableMap::COL_ID, $id, $comparison);
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
     * @return ChildSuffixTypeQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SuffixTypeTableMap::COL_NAME, $name, $comparison);
    }

    /**
     * Filter the query on the symbol column
     *
     * Example usage:
     * <code>
     * $query->filterBySymbol('fooValue');   // WHERE symbol = 'fooValue'
     * $query->filterBySymbol('%fooValue%'); // WHERE symbol LIKE '%fooValue%'
     * </code>
     *
     * @param     string $symbol The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSuffixTypeQuery The current query, for fluid interface
     */
    public function filterBySymbol($symbol = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($symbol)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $symbol)) {
                $symbol = str_replace('*', '%', $symbol);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SuffixTypeTableMap::COL_SYMBOL, $symbol, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\CartRule\Model\ORM\CartRule object
     *
     * @param \Gekosale\Plugin\CartRule\Model\ORM\CartRule|ObjectCollection $cartRule  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSuffixTypeQuery The current query, for fluid interface
     */
    public function filterByCartRule($cartRule, $comparison = null)
    {
        if ($cartRule instanceof \Gekosale\Plugin\CartRule\Model\ORM\CartRule) {
            return $this
                ->addUsingAlias(SuffixTypeTableMap::COL_ID, $cartRule->getSuffixTypeId(), $comparison);
        } elseif ($cartRule instanceof ObjectCollection) {
            return $this
                ->useCartRuleQuery()
                ->filterByPrimaryKeys($cartRule->getPrimaryKeys())
                ->endUse();
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
     * @return ChildSuffixTypeQuery The current query, for fluid interface
     */
    public function joinCartRule($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useCartRuleQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCartRule($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CartRule', '\Gekosale\Plugin\CartRule\Model\ORM\CartRuleQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\CartRule\Model\ORM\CartRuleClientGroup object
     *
     * @param \Gekosale\Plugin\CartRule\Model\ORM\CartRuleClientGroup|ObjectCollection $cartRuleClientGroup  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSuffixTypeQuery The current query, for fluid interface
     */
    public function filterByCartRuleClientGroup($cartRuleClientGroup, $comparison = null)
    {
        if ($cartRuleClientGroup instanceof \Gekosale\Plugin\CartRule\Model\ORM\CartRuleClientGroup) {
            return $this
                ->addUsingAlias(SuffixTypeTableMap::COL_ID, $cartRuleClientGroup->getSuffixTypeId(), $comparison);
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
     * @return ChildSuffixTypeQuery The current query, for fluid interface
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
     * Exclude object from result
     *
     * @param   ChildSuffixType $suffixType Object to remove from the list of results
     *
     * @return ChildSuffixTypeQuery The current query, for fluid interface
     */
    public function prune($suffixType = null)
    {
        if ($suffixType) {
            $this->addUsingAlias(SuffixTypeTableMap::COL_ID, $suffixType->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the suffix_type table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SuffixTypeTableMap::DATABASE_NAME);
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
            SuffixTypeTableMap::clearInstancePool();
            SuffixTypeTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildSuffixType or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildSuffixType object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(SuffixTypeTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(SuffixTypeTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        SuffixTypeTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            SuffixTypeTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // SuffixTypeQuery
