<?php

namespace Gekosale\Component\Configuration\Model\Availablity\Base;

use \Exception;
use \PDO;
use Gekosale\Component\Configuration\Model\Availablity\Availablity as ChildAvailablity;
use Gekosale\Component\Configuration\Model\Availablity\AvailablityI18nQuery as ChildAvailablityI18nQuery;
use Gekosale\Component\Configuration\Model\Availablity\AvailablityQuery as ChildAvailablityQuery;
use Gekosale\Component\Configuration\Model\Availablity\Map\AvailablityTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'availablity' table.
 *
 * 
 *
 * @method     ChildAvailablityQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildAvailablityQuery orderByAdddate($order = Criteria::ASC) Order by the add_date column
 * @method     ChildAvailablityQuery orderByValue($order = Criteria::ASC) Order by the value column
 *
 * @method     ChildAvailablityQuery groupById() Group by the id column
 * @method     ChildAvailablityQuery groupByAdddate() Group by the add_date column
 * @method     ChildAvailablityQuery groupByValue() Group by the value column
 *
 * @method     ChildAvailablityQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildAvailablityQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildAvailablityQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildAvailablityQuery leftJoinAvailablityI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the AvailablityI18n relation
 * @method     ChildAvailablityQuery rightJoinAvailablityI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AvailablityI18n relation
 * @method     ChildAvailablityQuery innerJoinAvailablityI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the AvailablityI18n relation
 *
 * @method     ChildAvailablity findOne(ConnectionInterface $con = null) Return the first ChildAvailablity matching the query
 * @method     ChildAvailablity findOneOrCreate(ConnectionInterface $con = null) Return the first ChildAvailablity matching the query, or a new ChildAvailablity object populated from the query conditions when no match is found
 *
 * @method     ChildAvailablity findOneById(int $id) Return the first ChildAvailablity filtered by the id column
 * @method     ChildAvailablity findOneByAdddate(string $add_date) Return the first ChildAvailablity filtered by the add_date column
 * @method     ChildAvailablity findOneByValue(int $value) Return the first ChildAvailablity filtered by the value column
 *
 * @method     array findById(int $id) Return ChildAvailablity objects filtered by the id column
 * @method     array findByAdddate(string $add_date) Return ChildAvailablity objects filtered by the add_date column
 * @method     array findByValue(int $value) Return ChildAvailablity objects filtered by the value column
 *
 */
abstract class AvailablityQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Component\Configuration\Model\Availablity\Base\AvailablityQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Component\\Configuration\\Model\\Availablity\\Availablity', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildAvailablityQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildAvailablityQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Component\Configuration\Model\Availablity\AvailablityQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Component\Configuration\Model\Availablity\AvailablityQuery();
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
     * @return ChildAvailablity|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = AvailablityTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(AvailablityTableMap::DATABASE_NAME);
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
     * @return   ChildAvailablity A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, ADD_DATE, VALUE FROM availablity WHERE ID = :p0';
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
            $obj = new ChildAvailablity();
            $obj->hydrate($row);
            AvailablityTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildAvailablity|array|mixed the result, formatted by the current formatter
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
     * @return ChildAvailablityQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(AvailablityTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildAvailablityQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(AvailablityTableMap::ID, $keys, Criteria::IN);
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
     * @return ChildAvailablityQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(AvailablityTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(AvailablityTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AvailablityTableMap::ID, $id, $comparison);
    }

    /**
     * Filter the query on the add_date column
     *
     * Example usage:
     * <code>
     * $query->filterByAdddate('2011-03-14'); // WHERE add_date = '2011-03-14'
     * $query->filterByAdddate('now'); // WHERE add_date = '2011-03-14'
     * $query->filterByAdddate(array('max' => 'yesterday')); // WHERE add_date > '2011-03-13'
     * </code>
     *
     * @param     mixed $adddate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildAvailablityQuery The current query, for fluid interface
     */
    public function filterByAdddate($adddate = null, $comparison = null)
    {
        if (is_array($adddate)) {
            $useMinMax = false;
            if (isset($adddate['min'])) {
                $this->addUsingAlias(AvailablityTableMap::ADD_DATE, $adddate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($adddate['max'])) {
                $this->addUsingAlias(AvailablityTableMap::ADD_DATE, $adddate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AvailablityTableMap::ADD_DATE, $adddate, $comparison);
    }

    /**
     * Filter the query on the value column
     *
     * Example usage:
     * <code>
     * $query->filterByValue(1234); // WHERE value = 1234
     * $query->filterByValue(array(12, 34)); // WHERE value IN (12, 34)
     * $query->filterByValue(array('min' => 12)); // WHERE value > 12
     * </code>
     *
     * @param     mixed $value The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildAvailablityQuery The current query, for fluid interface
     */
    public function filterByValue($value = null, $comparison = null)
    {
        if (is_array($value)) {
            $useMinMax = false;
            if (isset($value['min'])) {
                $this->addUsingAlias(AvailablityTableMap::VALUE, $value['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($value['max'])) {
                $this->addUsingAlias(AvailablityTableMap::VALUE, $value['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AvailablityTableMap::VALUE, $value, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Component\Configuration\Model\Availablity\AvailablityI18n object
     *
     * @param \Gekosale\Component\Configuration\Model\Availablity\AvailablityI18n|ObjectCollection $availablityI18n  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildAvailablityQuery The current query, for fluid interface
     */
    public function filterByAvailablityI18n($availablityI18n, $comparison = null)
    {
        if ($availablityI18n instanceof \Gekosale\Component\Configuration\Model\Availablity\AvailablityI18n) {
            return $this
                ->addUsingAlias(AvailablityTableMap::ID, $availablityI18n->getId(), $comparison);
        } elseif ($availablityI18n instanceof ObjectCollection) {
            return $this
                ->useAvailablityI18nQuery()
                ->filterByPrimaryKeys($availablityI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAvailablityI18n() only accepts arguments of type \Gekosale\Component\Configuration\Model\Availablity\AvailablityI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AvailablityI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildAvailablityQuery The current query, for fluid interface
     */
    public function joinAvailablityI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AvailablityI18n');

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
            $this->addJoinObject($join, 'AvailablityI18n');
        }

        return $this;
    }

    /**
     * Use the AvailablityI18n relation AvailablityI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Component\Configuration\Model\Availablity\AvailablityI18nQuery A secondary query class using the current class as primary query
     */
    public function useAvailablityI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinAvailablityI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AvailablityI18n', '\Gekosale\Component\Configuration\Model\Availablity\AvailablityI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildAvailablity $availablity Object to remove from the list of results
     *
     * @return ChildAvailablityQuery The current query, for fluid interface
     */
    public function prune($availablity = null)
    {
        if ($availablity) {
            $this->addUsingAlias(AvailablityTableMap::ID, $availablity->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the availablity table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(AvailablityTableMap::DATABASE_NAME);
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
            AvailablityTableMap::clearInstancePool();
            AvailablityTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildAvailablity or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildAvailablity object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(AvailablityTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(AvailablityTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        AvailablityTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            AvailablityTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    // i18n behavior
    
    /**
     * Adds a JOIN clause to the query using the i18n relation
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildAvailablityQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'AvailablityI18n';
    
        return $this
            ->joinAvailablityI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }
    
    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildAvailablityQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('AvailablityI18n');
        $this->with['AvailablityI18n']->setIsWithOneToMany(false);
    
        return $this;
    }
    
    /**
     * Use the I18n relation query object
     *
     * @see       useQuery()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildAvailablityI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AvailablityI18n', '\Gekosale\Component\Configuration\Model\Availablity\AvailablityI18nQuery');
    }

} // AvailablityQuery
