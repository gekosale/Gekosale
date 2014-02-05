<?php

namespace Gekosale\Plugin\Controller\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Controller\Model\ORM\Controller as ChildController;
use Gekosale\Plugin\Controller\Model\ORM\ControllerQuery as ChildControllerQuery;
use Gekosale\Plugin\Controller\Model\ORM\Map\ControllerTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'controller' table.
 *
 * 
 *
 * @method     ChildControllerQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildControllerQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildControllerQuery orderByVersion($order = Criteria::ASC) Order by the version column
 * @method     ChildControllerQuery orderByDescription($order = Criteria::ASC) Order by the description column
 * @method     ChildControllerQuery orderByEnable($order = Criteria::ASC) Order by the enable column
 * @method     ChildControllerQuery orderByMode($order = Criteria::ASC) Order by the mode column
 *
 * @method     ChildControllerQuery groupById() Group by the id column
 * @method     ChildControllerQuery groupByName() Group by the name column
 * @method     ChildControllerQuery groupByVersion() Group by the version column
 * @method     ChildControllerQuery groupByDescription() Group by the description column
 * @method     ChildControllerQuery groupByEnable() Group by the enable column
 * @method     ChildControllerQuery groupByMode() Group by the mode column
 *
 * @method     ChildControllerQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildControllerQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildControllerQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildControllerQuery leftJoinControllerPermission($relationAlias = null) Adds a LEFT JOIN clause to the query using the ControllerPermission relation
 * @method     ChildControllerQuery rightJoinControllerPermission($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ControllerPermission relation
 * @method     ChildControllerQuery innerJoinControllerPermission($relationAlias = null) Adds a INNER JOIN clause to the query using the ControllerPermission relation
 *
 * @method     ChildController findOne(ConnectionInterface $con = null) Return the first ChildController matching the query
 * @method     ChildController findOneOrCreate(ConnectionInterface $con = null) Return the first ChildController matching the query, or a new ChildController object populated from the query conditions when no match is found
 *
 * @method     ChildController findOneById(int $id) Return the first ChildController filtered by the id column
 * @method     ChildController findOneByName(string $name) Return the first ChildController filtered by the name column
 * @method     ChildController findOneByVersion(string $version) Return the first ChildController filtered by the version column
 * @method     ChildController findOneByDescription(string $description) Return the first ChildController filtered by the description column
 * @method     ChildController findOneByEnable(int $enable) Return the first ChildController filtered by the enable column
 * @method     ChildController findOneByMode(int $mode) Return the first ChildController filtered by the mode column
 *
 * @method     array findById(int $id) Return ChildController objects filtered by the id column
 * @method     array findByName(string $name) Return ChildController objects filtered by the name column
 * @method     array findByVersion(string $version) Return ChildController objects filtered by the version column
 * @method     array findByDescription(string $description) Return ChildController objects filtered by the description column
 * @method     array findByEnable(int $enable) Return ChildController objects filtered by the enable column
 * @method     array findByMode(int $mode) Return ChildController objects filtered by the mode column
 *
 */
abstract class ControllerQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Controller\Model\ORM\Base\ControllerQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Controller\\Model\\ORM\\Controller', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildControllerQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildControllerQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Controller\Model\ORM\ControllerQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Controller\Model\ORM\ControllerQuery();
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
     * @return ChildController|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ControllerTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ControllerTableMap::DATABASE_NAME);
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
     * @return   ChildController A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, NAME, VERSION, DESCRIPTION, ENABLE, MODE FROM controller WHERE ID = :p0';
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
            $obj = new ChildController();
            $obj->hydrate($row);
            ControllerTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildController|array|mixed the result, formatted by the current formatter
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
     * @return ChildControllerQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ControllerTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildControllerQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ControllerTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildControllerQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ControllerTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ControllerTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ControllerTableMap::COL_ID, $id, $comparison);
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
     * @return ChildControllerQuery The current query, for fluid interface
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

        return $this->addUsingAlias(ControllerTableMap::COL_NAME, $name, $comparison);
    }

    /**
     * Filter the query on the version column
     *
     * Example usage:
     * <code>
     * $query->filterByVersion('fooValue');   // WHERE version = 'fooValue'
     * $query->filterByVersion('%fooValue%'); // WHERE version LIKE '%fooValue%'
     * </code>
     *
     * @param     string $version The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildControllerQuery The current query, for fluid interface
     */
    public function filterByVersion($version = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($version)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $version)) {
                $version = str_replace('*', '%', $version);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ControllerTableMap::COL_VERSION, $version, $comparison);
    }

    /**
     * Filter the query on the description column
     *
     * Example usage:
     * <code>
     * $query->filterByDescription('fooValue');   // WHERE description = 'fooValue'
     * $query->filterByDescription('%fooValue%'); // WHERE description LIKE '%fooValue%'
     * </code>
     *
     * @param     string $description The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildControllerQuery The current query, for fluid interface
     */
    public function filterByDescription($description = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($description)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $description)) {
                $description = str_replace('*', '%', $description);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ControllerTableMap::COL_DESCRIPTION, $description, $comparison);
    }

    /**
     * Filter the query on the enable column
     *
     * Example usage:
     * <code>
     * $query->filterByEnable(1234); // WHERE enable = 1234
     * $query->filterByEnable(array(12, 34)); // WHERE enable IN (12, 34)
     * $query->filterByEnable(array('min' => 12)); // WHERE enable > 12
     * </code>
     *
     * @param     mixed $enable The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildControllerQuery The current query, for fluid interface
     */
    public function filterByEnable($enable = null, $comparison = null)
    {
        if (is_array($enable)) {
            $useMinMax = false;
            if (isset($enable['min'])) {
                $this->addUsingAlias(ControllerTableMap::COL_ENABLE, $enable['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($enable['max'])) {
                $this->addUsingAlias(ControllerTableMap::COL_ENABLE, $enable['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ControllerTableMap::COL_ENABLE, $enable, $comparison);
    }

    /**
     * Filter the query on the mode column
     *
     * Example usage:
     * <code>
     * $query->filterByMode(1234); // WHERE mode = 1234
     * $query->filterByMode(array(12, 34)); // WHERE mode IN (12, 34)
     * $query->filterByMode(array('min' => 12)); // WHERE mode > 12
     * </code>
     *
     * @param     mixed $mode The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildControllerQuery The current query, for fluid interface
     */
    public function filterByMode($mode = null, $comparison = null)
    {
        if (is_array($mode)) {
            $useMinMax = false;
            if (isset($mode['min'])) {
                $this->addUsingAlias(ControllerTableMap::COL_MODE, $mode['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($mode['max'])) {
                $this->addUsingAlias(ControllerTableMap::COL_MODE, $mode['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ControllerTableMap::COL_MODE, $mode, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Controller\Model\ORM\ControllerPermission object
     *
     * @param \Gekosale\Plugin\Controller\Model\ORM\ControllerPermission|ObjectCollection $controllerPermission  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildControllerQuery The current query, for fluid interface
     */
    public function filterByControllerPermission($controllerPermission, $comparison = null)
    {
        if ($controllerPermission instanceof \Gekosale\Plugin\Controller\Model\ORM\ControllerPermission) {
            return $this
                ->addUsingAlias(ControllerTableMap::COL_ID, $controllerPermission->getControllerId(), $comparison);
        } elseif ($controllerPermission instanceof ObjectCollection) {
            return $this
                ->useControllerPermissionQuery()
                ->filterByPrimaryKeys($controllerPermission->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByControllerPermission() only accepts arguments of type \Gekosale\Plugin\Controller\Model\ORM\ControllerPermission or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ControllerPermission relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildControllerQuery The current query, for fluid interface
     */
    public function joinControllerPermission($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ControllerPermission');

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
            $this->addJoinObject($join, 'ControllerPermission');
        }

        return $this;
    }

    /**
     * Use the ControllerPermission relation ControllerPermission object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Controller\Model\ORM\ControllerPermissionQuery A secondary query class using the current class as primary query
     */
    public function useControllerPermissionQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinControllerPermission($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ControllerPermission', '\Gekosale\Plugin\Controller\Model\ORM\ControllerPermissionQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildController $controller Object to remove from the list of results
     *
     * @return ChildControllerQuery The current query, for fluid interface
     */
    public function prune($controller = null)
    {
        if ($controller) {
            $this->addUsingAlias(ControllerTableMap::COL_ID, $controller->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the controller table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ControllerTableMap::DATABASE_NAME);
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
            ControllerTableMap::clearInstancePool();
            ControllerTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildController or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildController object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(ControllerTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ControllerTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        ControllerTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            ControllerTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // ControllerQuery
