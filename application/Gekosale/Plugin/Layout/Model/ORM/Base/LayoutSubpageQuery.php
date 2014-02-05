<?php

namespace Gekosale\Plugin\Layout\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Layout\Model\ORM\LayoutSubpage as ChildLayoutSubpage;
use Gekosale\Plugin\Layout\Model\ORM\LayoutSubpageQuery as ChildLayoutSubpageQuery;
use Gekosale\Plugin\Layout\Model\ORM\Map\LayoutSubpageTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'layout_subpage' table.
 *
 * 
 *
 * @method     ChildLayoutSubpageQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildLayoutSubpageQuery orderBySubpageId($order = Criteria::ASC) Order by the subpage_id column
 * @method     ChildLayoutSubpageQuery orderByLayoutSchemeId($order = Criteria::ASC) Order by the layout_scheme_id column
 *
 * @method     ChildLayoutSubpageQuery groupById() Group by the id column
 * @method     ChildLayoutSubpageQuery groupBySubpageId() Group by the subpage_id column
 * @method     ChildLayoutSubpageQuery groupByLayoutSchemeId() Group by the layout_scheme_id column
 *
 * @method     ChildLayoutSubpageQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildLayoutSubpageQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildLayoutSubpageQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildLayoutSubpageQuery leftJoinLayoutScheme($relationAlias = null) Adds a LEFT JOIN clause to the query using the LayoutScheme relation
 * @method     ChildLayoutSubpageQuery rightJoinLayoutScheme($relationAlias = null) Adds a RIGHT JOIN clause to the query using the LayoutScheme relation
 * @method     ChildLayoutSubpageQuery innerJoinLayoutScheme($relationAlias = null) Adds a INNER JOIN clause to the query using the LayoutScheme relation
 *
 * @method     ChildLayoutSubpageQuery leftJoinSubpage($relationAlias = null) Adds a LEFT JOIN clause to the query using the Subpage relation
 * @method     ChildLayoutSubpageQuery rightJoinSubpage($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Subpage relation
 * @method     ChildLayoutSubpageQuery innerJoinSubpage($relationAlias = null) Adds a INNER JOIN clause to the query using the Subpage relation
 *
 * @method     ChildLayoutSubpageQuery leftJoinLayoutSubpageColumn($relationAlias = null) Adds a LEFT JOIN clause to the query using the LayoutSubpageColumn relation
 * @method     ChildLayoutSubpageQuery rightJoinLayoutSubpageColumn($relationAlias = null) Adds a RIGHT JOIN clause to the query using the LayoutSubpageColumn relation
 * @method     ChildLayoutSubpageQuery innerJoinLayoutSubpageColumn($relationAlias = null) Adds a INNER JOIN clause to the query using the LayoutSubpageColumn relation
 *
 * @method     ChildLayoutSubpage findOne(ConnectionInterface $con = null) Return the first ChildLayoutSubpage matching the query
 * @method     ChildLayoutSubpage findOneOrCreate(ConnectionInterface $con = null) Return the first ChildLayoutSubpage matching the query, or a new ChildLayoutSubpage object populated from the query conditions when no match is found
 *
 * @method     ChildLayoutSubpage findOneById(int $id) Return the first ChildLayoutSubpage filtered by the id column
 * @method     ChildLayoutSubpage findOneBySubpageId(int $subpage_id) Return the first ChildLayoutSubpage filtered by the subpage_id column
 * @method     ChildLayoutSubpage findOneByLayoutSchemeId(int $layout_scheme_id) Return the first ChildLayoutSubpage filtered by the layout_scheme_id column
 *
 * @method     array findById(int $id) Return ChildLayoutSubpage objects filtered by the id column
 * @method     array findBySubpageId(int $subpage_id) Return ChildLayoutSubpage objects filtered by the subpage_id column
 * @method     array findByLayoutSchemeId(int $layout_scheme_id) Return ChildLayoutSubpage objects filtered by the layout_scheme_id column
 *
 */
abstract class LayoutSubpageQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Layout\Model\ORM\Base\LayoutSubpageQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Layout\\Model\\ORM\\LayoutSubpage', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildLayoutSubpageQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildLayoutSubpageQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpageQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpageQuery();
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
     * @return ChildLayoutSubpage|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = LayoutSubpageTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(LayoutSubpageTableMap::DATABASE_NAME);
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
     * @return   ChildLayoutSubpage A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, SUBPAGE_ID, LAYOUT_SCHEME_ID FROM layout_subpage WHERE ID = :p0';
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
            $obj = new ChildLayoutSubpage();
            $obj->hydrate($row);
            LayoutSubpageTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildLayoutSubpage|array|mixed the result, formatted by the current formatter
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
     * @return ChildLayoutSubpageQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(LayoutSubpageTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildLayoutSubpageQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(LayoutSubpageTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildLayoutSubpageQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(LayoutSubpageTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(LayoutSubpageTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LayoutSubpageTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the subpage_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySubpageId(1234); // WHERE subpage_id = 1234
     * $query->filterBySubpageId(array(12, 34)); // WHERE subpage_id IN (12, 34)
     * $query->filterBySubpageId(array('min' => 12)); // WHERE subpage_id > 12
     * </code>
     *
     * @see       filterBySubpage()
     *
     * @param     mixed $subpageId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLayoutSubpageQuery The current query, for fluid interface
     */
    public function filterBySubpageId($subpageId = null, $comparison = null)
    {
        if (is_array($subpageId)) {
            $useMinMax = false;
            if (isset($subpageId['min'])) {
                $this->addUsingAlias(LayoutSubpageTableMap::COL_SUBPAGE_ID, $subpageId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($subpageId['max'])) {
                $this->addUsingAlias(LayoutSubpageTableMap::COL_SUBPAGE_ID, $subpageId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LayoutSubpageTableMap::COL_SUBPAGE_ID, $subpageId, $comparison);
    }

    /**
     * Filter the query on the layout_scheme_id column
     *
     * Example usage:
     * <code>
     * $query->filterByLayoutSchemeId(1234); // WHERE layout_scheme_id = 1234
     * $query->filterByLayoutSchemeId(array(12, 34)); // WHERE layout_scheme_id IN (12, 34)
     * $query->filterByLayoutSchemeId(array('min' => 12)); // WHERE layout_scheme_id > 12
     * </code>
     *
     * @see       filterByLayoutScheme()
     *
     * @param     mixed $layoutSchemeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLayoutSubpageQuery The current query, for fluid interface
     */
    public function filterByLayoutSchemeId($layoutSchemeId = null, $comparison = null)
    {
        if (is_array($layoutSchemeId)) {
            $useMinMax = false;
            if (isset($layoutSchemeId['min'])) {
                $this->addUsingAlias(LayoutSubpageTableMap::COL_LAYOUT_SCHEME_ID, $layoutSchemeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($layoutSchemeId['max'])) {
                $this->addUsingAlias(LayoutSubpageTableMap::COL_LAYOUT_SCHEME_ID, $layoutSchemeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LayoutSubpageTableMap::COL_LAYOUT_SCHEME_ID, $layoutSchemeId, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Layout\Model\ORM\LayoutScheme object
     *
     * @param \Gekosale\Plugin\Layout\Model\ORM\LayoutScheme|ObjectCollection $layoutScheme The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLayoutSubpageQuery The current query, for fluid interface
     */
    public function filterByLayoutScheme($layoutScheme, $comparison = null)
    {
        if ($layoutScheme instanceof \Gekosale\Plugin\Layout\Model\ORM\LayoutScheme) {
            return $this
                ->addUsingAlias(LayoutSubpageTableMap::COL_LAYOUT_SCHEME_ID, $layoutScheme->getId(), $comparison);
        } elseif ($layoutScheme instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(LayoutSubpageTableMap::COL_LAYOUT_SCHEME_ID, $layoutScheme->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByLayoutScheme() only accepts arguments of type \Gekosale\Plugin\Layout\Model\ORM\LayoutScheme or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the LayoutScheme relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildLayoutSubpageQuery The current query, for fluid interface
     */
    public function joinLayoutScheme($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('LayoutScheme');

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
            $this->addJoinObject($join, 'LayoutScheme');
        }

        return $this;
    }

    /**
     * Use the LayoutScheme relation LayoutScheme object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Layout\Model\ORM\LayoutSchemeQuery A secondary query class using the current class as primary query
     */
    public function useLayoutSchemeQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinLayoutScheme($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'LayoutScheme', '\Gekosale\Plugin\Layout\Model\ORM\LayoutSchemeQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Layout\Model\ORM\Subpage object
     *
     * @param \Gekosale\Plugin\Layout\Model\ORM\Subpage|ObjectCollection $subpage The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLayoutSubpageQuery The current query, for fluid interface
     */
    public function filterBySubpage($subpage, $comparison = null)
    {
        if ($subpage instanceof \Gekosale\Plugin\Layout\Model\ORM\Subpage) {
            return $this
                ->addUsingAlias(LayoutSubpageTableMap::COL_SUBPAGE_ID, $subpage->getId(), $comparison);
        } elseif ($subpage instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(LayoutSubpageTableMap::COL_SUBPAGE_ID, $subpage->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterBySubpage() only accepts arguments of type \Gekosale\Plugin\Layout\Model\ORM\Subpage or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Subpage relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildLayoutSubpageQuery The current query, for fluid interface
     */
    public function joinSubpage($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Subpage');

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
            $this->addJoinObject($join, 'Subpage');
        }

        return $this;
    }

    /**
     * Use the Subpage relation Subpage object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Layout\Model\ORM\SubpageQuery A secondary query class using the current class as primary query
     */
    public function useSubpageQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSubpage($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Subpage', '\Gekosale\Plugin\Layout\Model\ORM\SubpageQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpageColumn object
     *
     * @param \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpageColumn|ObjectCollection $layoutSubpageColumn  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLayoutSubpageQuery The current query, for fluid interface
     */
    public function filterByLayoutSubpageColumn($layoutSubpageColumn, $comparison = null)
    {
        if ($layoutSubpageColumn instanceof \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpageColumn) {
            return $this
                ->addUsingAlias(LayoutSubpageTableMap::COL_ID, $layoutSubpageColumn->getLayoutSubpageId(), $comparison);
        } elseif ($layoutSubpageColumn instanceof ObjectCollection) {
            return $this
                ->useLayoutSubpageColumnQuery()
                ->filterByPrimaryKeys($layoutSubpageColumn->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByLayoutSubpageColumn() only accepts arguments of type \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpageColumn or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the LayoutSubpageColumn relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildLayoutSubpageQuery The current query, for fluid interface
     */
    public function joinLayoutSubpageColumn($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('LayoutSubpageColumn');

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
            $this->addJoinObject($join, 'LayoutSubpageColumn');
        }

        return $this;
    }

    /**
     * Use the LayoutSubpageColumn relation LayoutSubpageColumn object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpageColumnQuery A secondary query class using the current class as primary query
     */
    public function useLayoutSubpageColumnQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinLayoutSubpageColumn($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'LayoutSubpageColumn', '\Gekosale\Plugin\Layout\Model\ORM\LayoutSubpageColumnQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildLayoutSubpage $layoutSubpage Object to remove from the list of results
     *
     * @return ChildLayoutSubpageQuery The current query, for fluid interface
     */
    public function prune($layoutSubpage = null)
    {
        if ($layoutSubpage) {
            $this->addUsingAlias(LayoutSubpageTableMap::COL_ID, $layoutSubpage->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the layout_subpage table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(LayoutSubpageTableMap::DATABASE_NAME);
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
            LayoutSubpageTableMap::clearInstancePool();
            LayoutSubpageTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildLayoutSubpage or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildLayoutSubpage object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(LayoutSubpageTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(LayoutSubpageTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        LayoutSubpageTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            LayoutSubpageTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // LayoutSubpageQuery
