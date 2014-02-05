<?php

namespace Gekosale\Plugin\Layout\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Layout\Model\ORM\LayoutSubpageColumn as ChildLayoutSubpageColumn;
use Gekosale\Plugin\Layout\Model\ORM\LayoutSubpageColumnQuery as ChildLayoutSubpageColumnQuery;
use Gekosale\Plugin\Layout\Model\ORM\Map\LayoutSubpageColumnTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'layout_subpage_column' table.
 *
 * 
 *
 * @method     ChildLayoutSubpageColumnQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildLayoutSubpageColumnQuery orderByLayoutSubpageId($order = Criteria::ASC) Order by the layout_subpage_id column
 * @method     ChildLayoutSubpageColumnQuery orderByOrder($order = Criteria::ASC) Order by the order column
 * @method     ChildLayoutSubpageColumnQuery orderByWidth($order = Criteria::ASC) Order by the width column
 * @method     ChildLayoutSubpageColumnQuery orderByShopId($order = Criteria::ASC) Order by the shop_id column
 *
 * @method     ChildLayoutSubpageColumnQuery groupById() Group by the id column
 * @method     ChildLayoutSubpageColumnQuery groupByLayoutSubpageId() Group by the layout_subpage_id column
 * @method     ChildLayoutSubpageColumnQuery groupByOrder() Group by the order column
 * @method     ChildLayoutSubpageColumnQuery groupByWidth() Group by the width column
 * @method     ChildLayoutSubpageColumnQuery groupByShopId() Group by the shop_id column
 *
 * @method     ChildLayoutSubpageColumnQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildLayoutSubpageColumnQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildLayoutSubpageColumnQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildLayoutSubpageColumnQuery leftJoinLayoutSubpage($relationAlias = null) Adds a LEFT JOIN clause to the query using the LayoutSubpage relation
 * @method     ChildLayoutSubpageColumnQuery rightJoinLayoutSubpage($relationAlias = null) Adds a RIGHT JOIN clause to the query using the LayoutSubpage relation
 * @method     ChildLayoutSubpageColumnQuery innerJoinLayoutSubpage($relationAlias = null) Adds a INNER JOIN clause to the query using the LayoutSubpage relation
 *
 * @method     ChildLayoutSubpageColumn findOne(ConnectionInterface $con = null) Return the first ChildLayoutSubpageColumn matching the query
 * @method     ChildLayoutSubpageColumn findOneOrCreate(ConnectionInterface $con = null) Return the first ChildLayoutSubpageColumn matching the query, or a new ChildLayoutSubpageColumn object populated from the query conditions when no match is found
 *
 * @method     ChildLayoutSubpageColumn findOneById(int $id) Return the first ChildLayoutSubpageColumn filtered by the id column
 * @method     ChildLayoutSubpageColumn findOneByLayoutSubpageId(int $layout_subpage_id) Return the first ChildLayoutSubpageColumn filtered by the layout_subpage_id column
 * @method     ChildLayoutSubpageColumn findOneByOrder(int $order) Return the first ChildLayoutSubpageColumn filtered by the order column
 * @method     ChildLayoutSubpageColumn findOneByWidth(int $width) Return the first ChildLayoutSubpageColumn filtered by the width column
 * @method     ChildLayoutSubpageColumn findOneByShopId(int $shop_id) Return the first ChildLayoutSubpageColumn filtered by the shop_id column
 *
 * @method     array findById(int $id) Return ChildLayoutSubpageColumn objects filtered by the id column
 * @method     array findByLayoutSubpageId(int $layout_subpage_id) Return ChildLayoutSubpageColumn objects filtered by the layout_subpage_id column
 * @method     array findByOrder(int $order) Return ChildLayoutSubpageColumn objects filtered by the order column
 * @method     array findByWidth(int $width) Return ChildLayoutSubpageColumn objects filtered by the width column
 * @method     array findByShopId(int $shop_id) Return ChildLayoutSubpageColumn objects filtered by the shop_id column
 *
 */
abstract class LayoutSubpageColumnQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Layout\Model\ORM\Base\LayoutSubpageColumnQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Layout\\Model\\ORM\\LayoutSubpageColumn', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildLayoutSubpageColumnQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildLayoutSubpageColumnQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpageColumnQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpageColumnQuery();
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
     * @return ChildLayoutSubpageColumn|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = LayoutSubpageColumnTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(LayoutSubpageColumnTableMap::DATABASE_NAME);
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
     * @return   ChildLayoutSubpageColumn A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, LAYOUT_SUBPAGE_ID, ORDER, WIDTH, SHOP_ID FROM layout_subpage_column WHERE ID = :p0';
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
            $obj = new ChildLayoutSubpageColumn();
            $obj->hydrate($row);
            LayoutSubpageColumnTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildLayoutSubpageColumn|array|mixed the result, formatted by the current formatter
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
     * @return ChildLayoutSubpageColumnQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(LayoutSubpageColumnTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildLayoutSubpageColumnQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(LayoutSubpageColumnTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildLayoutSubpageColumnQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(LayoutSubpageColumnTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(LayoutSubpageColumnTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LayoutSubpageColumnTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the layout_subpage_id column
     *
     * Example usage:
     * <code>
     * $query->filterByLayoutSubpageId(1234); // WHERE layout_subpage_id = 1234
     * $query->filterByLayoutSubpageId(array(12, 34)); // WHERE layout_subpage_id IN (12, 34)
     * $query->filterByLayoutSubpageId(array('min' => 12)); // WHERE layout_subpage_id > 12
     * </code>
     *
     * @see       filterByLayoutSubpage()
     *
     * @param     mixed $layoutSubpageId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLayoutSubpageColumnQuery The current query, for fluid interface
     */
    public function filterByLayoutSubpageId($layoutSubpageId = null, $comparison = null)
    {
        if (is_array($layoutSubpageId)) {
            $useMinMax = false;
            if (isset($layoutSubpageId['min'])) {
                $this->addUsingAlias(LayoutSubpageColumnTableMap::COL_LAYOUT_SUBPAGE_ID, $layoutSubpageId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($layoutSubpageId['max'])) {
                $this->addUsingAlias(LayoutSubpageColumnTableMap::COL_LAYOUT_SUBPAGE_ID, $layoutSubpageId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LayoutSubpageColumnTableMap::COL_LAYOUT_SUBPAGE_ID, $layoutSubpageId, $comparison);
    }

    /**
     * Filter the query on the order column
     *
     * Example usage:
     * <code>
     * $query->filterByOrder(1234); // WHERE order = 1234
     * $query->filterByOrder(array(12, 34)); // WHERE order IN (12, 34)
     * $query->filterByOrder(array('min' => 12)); // WHERE order > 12
     * </code>
     *
     * @param     mixed $order The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLayoutSubpageColumnQuery The current query, for fluid interface
     */
    public function filterByOrder($order = null, $comparison = null)
    {
        if (is_array($order)) {
            $useMinMax = false;
            if (isset($order['min'])) {
                $this->addUsingAlias(LayoutSubpageColumnTableMap::COL_ORDER, $order['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($order['max'])) {
                $this->addUsingAlias(LayoutSubpageColumnTableMap::COL_ORDER, $order['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LayoutSubpageColumnTableMap::COL_ORDER, $order, $comparison);
    }

    /**
     * Filter the query on the width column
     *
     * Example usage:
     * <code>
     * $query->filterByWidth(1234); // WHERE width = 1234
     * $query->filterByWidth(array(12, 34)); // WHERE width IN (12, 34)
     * $query->filterByWidth(array('min' => 12)); // WHERE width > 12
     * </code>
     *
     * @param     mixed $width The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLayoutSubpageColumnQuery The current query, for fluid interface
     */
    public function filterByWidth($width = null, $comparison = null)
    {
        if (is_array($width)) {
            $useMinMax = false;
            if (isset($width['min'])) {
                $this->addUsingAlias(LayoutSubpageColumnTableMap::COL_WIDTH, $width['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($width['max'])) {
                $this->addUsingAlias(LayoutSubpageColumnTableMap::COL_WIDTH, $width['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LayoutSubpageColumnTableMap::COL_WIDTH, $width, $comparison);
    }

    /**
     * Filter the query on the shop_id column
     *
     * Example usage:
     * <code>
     * $query->filterByShopId(1234); // WHERE shop_id = 1234
     * $query->filterByShopId(array(12, 34)); // WHERE shop_id IN (12, 34)
     * $query->filterByShopId(array('min' => 12)); // WHERE shop_id > 12
     * </code>
     *
     * @param     mixed $shopId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLayoutSubpageColumnQuery The current query, for fluid interface
     */
    public function filterByShopId($shopId = null, $comparison = null)
    {
        if (is_array($shopId)) {
            $useMinMax = false;
            if (isset($shopId['min'])) {
                $this->addUsingAlias(LayoutSubpageColumnTableMap::COL_SHOP_ID, $shopId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($shopId['max'])) {
                $this->addUsingAlias(LayoutSubpageColumnTableMap::COL_SHOP_ID, $shopId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LayoutSubpageColumnTableMap::COL_SHOP_ID, $shopId, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpage object
     *
     * @param \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpage|ObjectCollection $layoutSubpage The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLayoutSubpageColumnQuery The current query, for fluid interface
     */
    public function filterByLayoutSubpage($layoutSubpage, $comparison = null)
    {
        if ($layoutSubpage instanceof \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpage) {
            return $this
                ->addUsingAlias(LayoutSubpageColumnTableMap::COL_LAYOUT_SUBPAGE_ID, $layoutSubpage->getId(), $comparison);
        } elseif ($layoutSubpage instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(LayoutSubpageColumnTableMap::COL_LAYOUT_SUBPAGE_ID, $layoutSubpage->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByLayoutSubpage() only accepts arguments of type \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpage or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the LayoutSubpage relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildLayoutSubpageColumnQuery The current query, for fluid interface
     */
    public function joinLayoutSubpage($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('LayoutSubpage');

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
            $this->addJoinObject($join, 'LayoutSubpage');
        }

        return $this;
    }

    /**
     * Use the LayoutSubpage relation LayoutSubpage object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Layout\Model\ORM\LayoutSubpageQuery A secondary query class using the current class as primary query
     */
    public function useLayoutSubpageQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinLayoutSubpage($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'LayoutSubpage', '\Gekosale\Plugin\Layout\Model\ORM\LayoutSubpageQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildLayoutSubpageColumn $layoutSubpageColumn Object to remove from the list of results
     *
     * @return ChildLayoutSubpageColumnQuery The current query, for fluid interface
     */
    public function prune($layoutSubpageColumn = null)
    {
        if ($layoutSubpageColumn) {
            $this->addUsingAlias(LayoutSubpageColumnTableMap::COL_ID, $layoutSubpageColumn->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the layout_subpage_column table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(LayoutSubpageColumnTableMap::DATABASE_NAME);
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
            LayoutSubpageColumnTableMap::clearInstancePool();
            LayoutSubpageColumnTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildLayoutSubpageColumn or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildLayoutSubpageColumn object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(LayoutSubpageColumnTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(LayoutSubpageColumnTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        LayoutSubpageColumnTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            LayoutSubpageColumnTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // LayoutSubpageColumnQuery
