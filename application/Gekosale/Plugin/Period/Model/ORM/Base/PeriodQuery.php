<?php

namespace Gekosale\Plugin\Period\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Period\Model\ORM\Period as ChildPeriod;
use Gekosale\Plugin\Period\Model\ORM\PeriodQuery as ChildPeriodQuery;
use Gekosale\Plugin\Period\Model\ORM\Map\PeriodTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'period' table.
 *
 * 
 *
 * @method     ChildPeriodQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildPeriodQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildPeriodQuery orderByTimeInterval($order = Criteria::ASC) Order by the time_interval column
 * @method     ChildPeriodQuery orderByIntervalSql($order = Criteria::ASC) Order by the interval_sql column
 *
 * @method     ChildPeriodQuery groupById() Group by the id column
 * @method     ChildPeriodQuery groupByName() Group by the name column
 * @method     ChildPeriodQuery groupByTimeInterval() Group by the time_interval column
 * @method     ChildPeriodQuery groupByIntervalSql() Group by the interval_sql column
 *
 * @method     ChildPeriodQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildPeriodQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildPeriodQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildPeriod findOne(ConnectionInterface $con = null) Return the first ChildPeriod matching the query
 * @method     ChildPeriod findOneOrCreate(ConnectionInterface $con = null) Return the first ChildPeriod matching the query, or a new ChildPeriod object populated from the query conditions when no match is found
 *
 * @method     ChildPeriod findOneById(int $id) Return the first ChildPeriod filtered by the id column
 * @method     ChildPeriod findOneByName(string $name) Return the first ChildPeriod filtered by the name column
 * @method     ChildPeriod findOneByTimeInterval(string $time_interval) Return the first ChildPeriod filtered by the time_interval column
 * @method     ChildPeriod findOneByIntervalSql(string $interval_sql) Return the first ChildPeriod filtered by the interval_sql column
 *
 * @method     array findById(int $id) Return ChildPeriod objects filtered by the id column
 * @method     array findByName(string $name) Return ChildPeriod objects filtered by the name column
 * @method     array findByTimeInterval(string $time_interval) Return ChildPeriod objects filtered by the time_interval column
 * @method     array findByIntervalSql(string $interval_sql) Return ChildPeriod objects filtered by the interval_sql column
 *
 */
abstract class PeriodQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Period\Model\ORM\Base\PeriodQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Period\\Model\\ORM\\Period', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildPeriodQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildPeriodQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Period\Model\ORM\PeriodQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Period\Model\ORM\PeriodQuery();
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
     * @return ChildPeriod|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = PeriodTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(PeriodTableMap::DATABASE_NAME);
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
     * @return   ChildPeriod A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, NAME, TIME_INTERVAL, INTERVAL_SQL FROM period WHERE ID = :p0';
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
            $obj = new ChildPeriod();
            $obj->hydrate($row);
            PeriodTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildPeriod|array|mixed the result, formatted by the current formatter
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
     * @return ChildPeriodQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(PeriodTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildPeriodQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(PeriodTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildPeriodQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(PeriodTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(PeriodTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PeriodTableMap::COL_ID, $id, $comparison);
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
     * @return ChildPeriodQuery The current query, for fluid interface
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

        return $this->addUsingAlias(PeriodTableMap::COL_NAME, $name, $comparison);
    }

    /**
     * Filter the query on the time_interval column
     *
     * Example usage:
     * <code>
     * $query->filterByTimeInterval('fooValue');   // WHERE time_interval = 'fooValue'
     * $query->filterByTimeInterval('%fooValue%'); // WHERE time_interval LIKE '%fooValue%'
     * </code>
     *
     * @param     string $timeInterval The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPeriodQuery The current query, for fluid interface
     */
    public function filterByTimeInterval($timeInterval = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($timeInterval)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $timeInterval)) {
                $timeInterval = str_replace('*', '%', $timeInterval);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PeriodTableMap::COL_TIME_INTERVAL, $timeInterval, $comparison);
    }

    /**
     * Filter the query on the interval_sql column
     *
     * Example usage:
     * <code>
     * $query->filterByIntervalSql('fooValue');   // WHERE interval_sql = 'fooValue'
     * $query->filterByIntervalSql('%fooValue%'); // WHERE interval_sql LIKE '%fooValue%'
     * </code>
     *
     * @param     string $intervalSql The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPeriodQuery The current query, for fluid interface
     */
    public function filterByIntervalSql($intervalSql = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($intervalSql)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $intervalSql)) {
                $intervalSql = str_replace('*', '%', $intervalSql);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PeriodTableMap::COL_INTERVAL_SQL, $intervalSql, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildPeriod $period Object to remove from the list of results
     *
     * @return ChildPeriodQuery The current query, for fluid interface
     */
    public function prune($period = null)
    {
        if ($period) {
            $this->addUsingAlias(PeriodTableMap::COL_ID, $period->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the period table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(PeriodTableMap::DATABASE_NAME);
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
            PeriodTableMap::clearInstancePool();
            PeriodTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildPeriod or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildPeriod object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(PeriodTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(PeriodTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        PeriodTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            PeriodTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // PeriodQuery
