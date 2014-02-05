<?php

namespace Gekosale\Plugin\Locale\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Locale\Model\ORM\LocaleShop as ChildLocaleShop;
use Gekosale\Plugin\Locale\Model\ORM\LocaleShopQuery as ChildLocaleShopQuery;
use Gekosale\Plugin\Locale\Model\ORM\Map\LocaleShopTableMap;
use Gekosale\Plugin\Shop\Model\ORM\Shop;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'locale_shop' table.
 *
 * 
 *
 * @method     ChildLocaleShopQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildLocaleShopQuery orderByLocaleId($order = Criteria::ASC) Order by the locale_id column
 * @method     ChildLocaleShopQuery orderByShopId($order = Criteria::ASC) Order by the shop_id column
 *
 * @method     ChildLocaleShopQuery groupById() Group by the id column
 * @method     ChildLocaleShopQuery groupByLocaleId() Group by the locale_id column
 * @method     ChildLocaleShopQuery groupByShopId() Group by the shop_id column
 *
 * @method     ChildLocaleShopQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildLocaleShopQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildLocaleShopQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildLocaleShopQuery leftJoinLocale($relationAlias = null) Adds a LEFT JOIN clause to the query using the Locale relation
 * @method     ChildLocaleShopQuery rightJoinLocale($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Locale relation
 * @method     ChildLocaleShopQuery innerJoinLocale($relationAlias = null) Adds a INNER JOIN clause to the query using the Locale relation
 *
 * @method     ChildLocaleShopQuery leftJoinShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the Shop relation
 * @method     ChildLocaleShopQuery rightJoinShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Shop relation
 * @method     ChildLocaleShopQuery innerJoinShop($relationAlias = null) Adds a INNER JOIN clause to the query using the Shop relation
 *
 * @method     ChildLocaleShop findOne(ConnectionInterface $con = null) Return the first ChildLocaleShop matching the query
 * @method     ChildLocaleShop findOneOrCreate(ConnectionInterface $con = null) Return the first ChildLocaleShop matching the query, or a new ChildLocaleShop object populated from the query conditions when no match is found
 *
 * @method     ChildLocaleShop findOneById(int $id) Return the first ChildLocaleShop filtered by the id column
 * @method     ChildLocaleShop findOneByLocaleId(int $locale_id) Return the first ChildLocaleShop filtered by the locale_id column
 * @method     ChildLocaleShop findOneByShopId(int $shop_id) Return the first ChildLocaleShop filtered by the shop_id column
 *
 * @method     array findById(int $id) Return ChildLocaleShop objects filtered by the id column
 * @method     array findByLocaleId(int $locale_id) Return ChildLocaleShop objects filtered by the locale_id column
 * @method     array findByShopId(int $shop_id) Return ChildLocaleShop objects filtered by the shop_id column
 *
 */
abstract class LocaleShopQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Locale\Model\ORM\Base\LocaleShopQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Locale\\Model\\ORM\\LocaleShop', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildLocaleShopQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildLocaleShopQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Locale\Model\ORM\LocaleShopQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Locale\Model\ORM\LocaleShopQuery();
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
     * @return ChildLocaleShop|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = LocaleShopTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(LocaleShopTableMap::DATABASE_NAME);
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
     * @return   ChildLocaleShop A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, LOCALE_ID, SHOP_ID FROM locale_shop WHERE ID = :p0';
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
            $obj = new ChildLocaleShop();
            $obj->hydrate($row);
            LocaleShopTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildLocaleShop|array|mixed the result, formatted by the current formatter
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
     * @return ChildLocaleShopQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(LocaleShopTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildLocaleShopQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(LocaleShopTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildLocaleShopQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(LocaleShopTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(LocaleShopTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LocaleShopTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the locale_id column
     *
     * Example usage:
     * <code>
     * $query->filterByLocaleId(1234); // WHERE locale_id = 1234
     * $query->filterByLocaleId(array(12, 34)); // WHERE locale_id IN (12, 34)
     * $query->filterByLocaleId(array('min' => 12)); // WHERE locale_id > 12
     * </code>
     *
     * @see       filterByLocale()
     *
     * @param     mixed $localeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLocaleShopQuery The current query, for fluid interface
     */
    public function filterByLocaleId($localeId = null, $comparison = null)
    {
        if (is_array($localeId)) {
            $useMinMax = false;
            if (isset($localeId['min'])) {
                $this->addUsingAlias(LocaleShopTableMap::COL_LOCALE_ID, $localeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($localeId['max'])) {
                $this->addUsingAlias(LocaleShopTableMap::COL_LOCALE_ID, $localeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LocaleShopTableMap::COL_LOCALE_ID, $localeId, $comparison);
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
     * @see       filterByShop()
     *
     * @param     mixed $shopId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLocaleShopQuery The current query, for fluid interface
     */
    public function filterByShopId($shopId = null, $comparison = null)
    {
        if (is_array($shopId)) {
            $useMinMax = false;
            if (isset($shopId['min'])) {
                $this->addUsingAlias(LocaleShopTableMap::COL_SHOP_ID, $shopId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($shopId['max'])) {
                $this->addUsingAlias(LocaleShopTableMap::COL_SHOP_ID, $shopId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LocaleShopTableMap::COL_SHOP_ID, $shopId, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Locale\Model\ORM\Locale object
     *
     * @param \Gekosale\Plugin\Locale\Model\ORM\Locale|ObjectCollection $locale The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLocaleShopQuery The current query, for fluid interface
     */
    public function filterByLocale($locale, $comparison = null)
    {
        if ($locale instanceof \Gekosale\Plugin\Locale\Model\ORM\Locale) {
            return $this
                ->addUsingAlias(LocaleShopTableMap::COL_LOCALE_ID, $locale->getId(), $comparison);
        } elseif ($locale instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(LocaleShopTableMap::COL_LOCALE_ID, $locale->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByLocale() only accepts arguments of type \Gekosale\Plugin\Locale\Model\ORM\Locale or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Locale relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildLocaleShopQuery The current query, for fluid interface
     */
    public function joinLocale($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Locale');

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
            $this->addJoinObject($join, 'Locale');
        }

        return $this;
    }

    /**
     * Use the Locale relation Locale object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Locale\Model\ORM\LocaleQuery A secondary query class using the current class as primary query
     */
    public function useLocaleQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinLocale($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Locale', '\Gekosale\Plugin\Locale\Model\ORM\LocaleQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Shop\Model\ORM\Shop object
     *
     * @param \Gekosale\Plugin\Shop\Model\ORM\Shop|ObjectCollection $shop The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLocaleShopQuery The current query, for fluid interface
     */
    public function filterByShop($shop, $comparison = null)
    {
        if ($shop instanceof \Gekosale\Plugin\Shop\Model\ORM\Shop) {
            return $this
                ->addUsingAlias(LocaleShopTableMap::COL_SHOP_ID, $shop->getId(), $comparison);
        } elseif ($shop instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(LocaleShopTableMap::COL_SHOP_ID, $shop->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByShop() only accepts arguments of type \Gekosale\Plugin\Shop\Model\ORM\Shop or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Shop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildLocaleShopQuery The current query, for fluid interface
     */
    public function joinShop($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Shop');

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
            $this->addJoinObject($join, 'Shop');
        }

        return $this;
    }

    /**
     * Use the Shop relation Shop object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Shop\Model\ORM\ShopQuery A secondary query class using the current class as primary query
     */
    public function useShopQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Shop', '\Gekosale\Plugin\Shop\Model\ORM\ShopQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildLocaleShop $localeShop Object to remove from the list of results
     *
     * @return ChildLocaleShopQuery The current query, for fluid interface
     */
    public function prune($localeShop = null)
    {
        if ($localeShop) {
            $this->addUsingAlias(LocaleShopTableMap::COL_ID, $localeShop->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the locale_shop table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(LocaleShopTableMap::DATABASE_NAME);
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
            LocaleShopTableMap::clearInstancePool();
            LocaleShopTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildLocaleShop or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildLocaleShop object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(LocaleShopTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(LocaleShopTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        LocaleShopTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            LocaleShopTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // LocaleShopQuery
