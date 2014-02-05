<?php

namespace Gekosale\Plugin\Settings\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Settings\Model\ORM\SettingsGlobal as ChildSettingsGlobal;
use Gekosale\Plugin\Settings\Model\ORM\SettingsGlobalQuery as ChildSettingsGlobalQuery;
use Gekosale\Plugin\Settings\Model\ORM\Map\SettingsGlobalTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'settings_global' table.
 *
 * 
 *
 * @method     ChildSettingsGlobalQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildSettingsGlobalQuery orderByParam($order = Criteria::ASC) Order by the param column
 * @method     ChildSettingsGlobalQuery orderByValue($order = Criteria::ASC) Order by the value column
 * @method     ChildSettingsGlobalQuery orderByType($order = Criteria::ASC) Order by the type column
 *
 * @method     ChildSettingsGlobalQuery groupById() Group by the id column
 * @method     ChildSettingsGlobalQuery groupByParam() Group by the param column
 * @method     ChildSettingsGlobalQuery groupByValue() Group by the value column
 * @method     ChildSettingsGlobalQuery groupByType() Group by the type column
 *
 * @method     ChildSettingsGlobalQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSettingsGlobalQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSettingsGlobalQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSettingsGlobal findOne(ConnectionInterface $con = null) Return the first ChildSettingsGlobal matching the query
 * @method     ChildSettingsGlobal findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSettingsGlobal matching the query, or a new ChildSettingsGlobal object populated from the query conditions when no match is found
 *
 * @method     ChildSettingsGlobal findOneById(int $id) Return the first ChildSettingsGlobal filtered by the id column
 * @method     ChildSettingsGlobal findOneByParam(string $param) Return the first ChildSettingsGlobal filtered by the param column
 * @method     ChildSettingsGlobal findOneByValue(string $value) Return the first ChildSettingsGlobal filtered by the value column
 * @method     ChildSettingsGlobal findOneByType(string $type) Return the first ChildSettingsGlobal filtered by the type column
 *
 * @method     array findById(int $id) Return ChildSettingsGlobal objects filtered by the id column
 * @method     array findByParam(string $param) Return ChildSettingsGlobal objects filtered by the param column
 * @method     array findByValue(string $value) Return ChildSettingsGlobal objects filtered by the value column
 * @method     array findByType(string $type) Return ChildSettingsGlobal objects filtered by the type column
 *
 */
abstract class SettingsGlobalQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Settings\Model\ORM\Base\SettingsGlobalQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Settings\\Model\\ORM\\SettingsGlobal', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildSettingsGlobalQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildSettingsGlobalQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Settings\Model\ORM\SettingsGlobalQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Settings\Model\ORM\SettingsGlobalQuery();
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
     * @return ChildSettingsGlobal|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SettingsGlobalTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SettingsGlobalTableMap::DATABASE_NAME);
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
     * @return   ChildSettingsGlobal A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, PARAM, VALUE, TYPE FROM settings_global WHERE ID = :p0';
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
            $obj = new ChildSettingsGlobal();
            $obj->hydrate($row);
            SettingsGlobalTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildSettingsGlobal|array|mixed the result, formatted by the current formatter
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
     * @return ChildSettingsGlobalQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SettingsGlobalTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildSettingsGlobalQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SettingsGlobalTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildSettingsGlobalQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(SettingsGlobalTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(SettingsGlobalTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SettingsGlobalTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the param column
     *
     * Example usage:
     * <code>
     * $query->filterByParam('fooValue');   // WHERE param = 'fooValue'
     * $query->filterByParam('%fooValue%'); // WHERE param LIKE '%fooValue%'
     * </code>
     *
     * @param     string $param The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSettingsGlobalQuery The current query, for fluid interface
     */
    public function filterByParam($param = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($param)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $param)) {
                $param = str_replace('*', '%', $param);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SettingsGlobalTableMap::COL_PARAM, $param, $comparison);
    }

    /**
     * Filter the query on the value column
     *
     * Example usage:
     * <code>
     * $query->filterByValue('fooValue');   // WHERE value = 'fooValue'
     * $query->filterByValue('%fooValue%'); // WHERE value LIKE '%fooValue%'
     * </code>
     *
     * @param     string $value The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSettingsGlobalQuery The current query, for fluid interface
     */
    public function filterByValue($value = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($value)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $value)) {
                $value = str_replace('*', '%', $value);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SettingsGlobalTableMap::COL_VALUE, $value, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * Example usage:
     * <code>
     * $query->filterByType('fooValue');   // WHERE type = 'fooValue'
     * $query->filterByType('%fooValue%'); // WHERE type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $type The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSettingsGlobalQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($type)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $type)) {
                $type = str_replace('*', '%', $type);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SettingsGlobalTableMap::COL_TYPE, $type, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildSettingsGlobal $settingsGlobal Object to remove from the list of results
     *
     * @return ChildSettingsGlobalQuery The current query, for fluid interface
     */
    public function prune($settingsGlobal = null)
    {
        if ($settingsGlobal) {
            $this->addUsingAlias(SettingsGlobalTableMap::COL_ID, $settingsGlobal->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the settings_global table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SettingsGlobalTableMap::DATABASE_NAME);
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
            SettingsGlobalTableMap::clearInstancePool();
            SettingsGlobalTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildSettingsGlobal or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildSettingsGlobal object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(SettingsGlobalTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(SettingsGlobalTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        SettingsGlobalTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            SettingsGlobalTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // SettingsGlobalQuery
