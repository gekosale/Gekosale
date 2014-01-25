<?php

namespace Gekosale\Core\Session\Model\Base;

use \Exception;
use \PDO;
use Gekosale\Core\Session\Model\Session as ChildSession;
use Gekosale\Core\Session\Model\SessionQuery as ChildSessionQuery;
use Gekosale\Core\Session\Model\Map\SessionTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'session' table.
 *
 *
 *
 * @method     ChildSessionQuery orderBySessId($order = Criteria::ASC) Order by the sess_id column
 * @method     ChildSessionQuery orderBySessData($order = Criteria::ASC) Order by the sess_data column
 * @method     ChildSessionQuery orderBySessTime($order = Criteria::ASC) Order by the sess_time column
 *
 * @method     ChildSessionQuery groupBySessId() Group by the sess_id column
 * @method     ChildSessionQuery groupBySessData() Group by the sess_data column
 * @method     ChildSessionQuery groupBySessTime() Group by the sess_time column
 *
 * @method     ChildSessionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSessionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSessionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSession findOne(ConnectionInterface $con = null) Return the first ChildSession matching the query
 * @method     ChildSession findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSession matching the query, or a new ChildSession object populated from the query conditions when no match is found
 *
 * @method     ChildSession findOneBySessId(string $sess_id) Return the first ChildSession filtered by the sess_id column
 * @method     ChildSession findOneBySessData(string $sess_data) Return the first ChildSession filtered by the sess_data column
 * @method     ChildSession findOneBySessTime(int $sess_time) Return the first ChildSession filtered by the sess_time column
 *
 * @method     array findBySessId(string $sess_id) Return ChildSession objects filtered by the sess_id column
 * @method     array findBySessData(string $sess_data) Return ChildSession objects filtered by the sess_data column
 * @method     array findBySessTime(int $sess_time) Return ChildSession objects filtered by the sess_time column
 *
 */
abstract class SessionQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Gekosale\Core\Session\Model\Base\SessionQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Core\\Session\\Model\\Session', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildSessionQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildSessionQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Core\Session\Model\SessionQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Core\Session\Model\SessionQuery();
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
     * @return ChildSession|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SessionTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SessionTableMap::DATABASE_NAME);
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
     * @return   ChildSession A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT SESS_ID, SESS_DATA, SESS_TIME FROM session WHERE SESS_ID = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildSession();
            $obj->hydrate($row);
            SessionTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildSession|array|mixed the result, formatted by the current formatter
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
     * @return ChildSessionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SessionTableMap::SESS_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildSessionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SessionTableMap::SESS_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the sess_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySessId('fooValue');   // WHERE sess_id = 'fooValue'
     * $query->filterBySessId('%fooValue%'); // WHERE sess_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $sessId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSessionQuery The current query, for fluid interface
     */
    public function filterBySessId($sessId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($sessId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $sessId)) {
                $sessId = str_replace('*', '%', $sessId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SessionTableMap::SESS_ID, $sessId, $comparison);
    }

    /**
     * Filter the query on the sess_data column
     *
     * Example usage:
     * <code>
     * $query->filterBySessData('fooValue');   // WHERE sess_data = 'fooValue'
     * $query->filterBySessData('%fooValue%'); // WHERE sess_data LIKE '%fooValue%'
     * </code>
     *
     * @param     string $sessData The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSessionQuery The current query, for fluid interface
     */
    public function filterBySessData($sessData = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($sessData)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $sessData)) {
                $sessData = str_replace('*', '%', $sessData);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SessionTableMap::SESS_DATA, $sessData, $comparison);
    }

    /**
     * Filter the query on the sess_time column
     *
     * Example usage:
     * <code>
     * $query->filterBySessTime(1234); // WHERE sess_time = 1234
     * $query->filterBySessTime(array(12, 34)); // WHERE sess_time IN (12, 34)
     * $query->filterBySessTime(array('min' => 12)); // WHERE sess_time > 12
     * </code>
     *
     * @param     mixed $sessTime The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSessionQuery The current query, for fluid interface
     */
    public function filterBySessTime($sessTime = null, $comparison = null)
    {
        if (is_array($sessTime)) {
            $useMinMax = false;
            if (isset($sessTime['min'])) {
                $this->addUsingAlias(SessionTableMap::SESS_TIME, $sessTime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sessTime['max'])) {
                $this->addUsingAlias(SessionTableMap::SESS_TIME, $sessTime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SessionTableMap::SESS_TIME, $sessTime, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildSession $session Object to remove from the list of results
     *
     * @return ChildSessionQuery The current query, for fluid interface
     */
    public function prune($session = null)
    {
        if ($session) {
            $this->addUsingAlias(SessionTableMap::SESS_ID, $session->getSessId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the session table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SessionTableMap::DATABASE_NAME);
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
            SessionTableMap::clearInstancePool();
            SessionTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildSession or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildSession object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(SessionTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(SessionTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        SessionTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            SessionTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // SessionQuery
