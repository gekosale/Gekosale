<?php

namespace Gekosale\Plugin\Locale\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Currency\Model\ORM\Currency;
use Gekosale\Plugin\Locale\Model\ORM\Locale as ChildLocale;
use Gekosale\Plugin\Locale\Model\ORM\LocaleQuery as ChildLocaleQuery;
use Gekosale\Plugin\Locale\Model\ORM\Map\LocaleTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'locale' table.
 *
 * 
 *
 * @method     ChildLocaleQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildLocaleQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildLocaleQuery orderByTranslation($order = Criteria::ASC) Order by the translation column
 * @method     ChildLocaleQuery orderByCurrencyId($order = Criteria::ASC) Order by the currency_id column
 * @method     ChildLocaleQuery orderByFlag($order = Criteria::ASC) Order by the flag column
 *
 * @method     ChildLocaleQuery groupById() Group by the id column
 * @method     ChildLocaleQuery groupByName() Group by the name column
 * @method     ChildLocaleQuery groupByTranslation() Group by the translation column
 * @method     ChildLocaleQuery groupByCurrencyId() Group by the currency_id column
 * @method     ChildLocaleQuery groupByFlag() Group by the flag column
 *
 * @method     ChildLocaleQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildLocaleQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildLocaleQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildLocaleQuery leftJoinCurrency($relationAlias = null) Adds a LEFT JOIN clause to the query using the Currency relation
 * @method     ChildLocaleQuery rightJoinCurrency($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Currency relation
 * @method     ChildLocaleQuery innerJoinCurrency($relationAlias = null) Adds a INNER JOIN clause to the query using the Currency relation
 *
 * @method     ChildLocaleQuery leftJoinLocaleShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the LocaleShop relation
 * @method     ChildLocaleQuery rightJoinLocaleShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the LocaleShop relation
 * @method     ChildLocaleQuery innerJoinLocaleShop($relationAlias = null) Adds a INNER JOIN clause to the query using the LocaleShop relation
 *
 * @method     ChildLocale findOne(ConnectionInterface $con = null) Return the first ChildLocale matching the query
 * @method     ChildLocale findOneOrCreate(ConnectionInterface $con = null) Return the first ChildLocale matching the query, or a new ChildLocale object populated from the query conditions when no match is found
 *
 * @method     ChildLocale findOneById(int $id) Return the first ChildLocale filtered by the id column
 * @method     ChildLocale findOneByName(string $name) Return the first ChildLocale filtered by the name column
 * @method     ChildLocale findOneByTranslation(string $translation) Return the first ChildLocale filtered by the translation column
 * @method     ChildLocale findOneByCurrencyId(int $currency_id) Return the first ChildLocale filtered by the currency_id column
 * @method     ChildLocale findOneByFlag(string $flag) Return the first ChildLocale filtered by the flag column
 *
 * @method     array findById(int $id) Return ChildLocale objects filtered by the id column
 * @method     array findByName(string $name) Return ChildLocale objects filtered by the name column
 * @method     array findByTranslation(string $translation) Return ChildLocale objects filtered by the translation column
 * @method     array findByCurrencyId(int $currency_id) Return ChildLocale objects filtered by the currency_id column
 * @method     array findByFlag(string $flag) Return ChildLocale objects filtered by the flag column
 *
 */
abstract class LocaleQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Locale\Model\ORM\Base\LocaleQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Locale\\Model\\ORM\\Locale', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildLocaleQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildLocaleQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Locale\Model\ORM\LocaleQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Locale\Model\ORM\LocaleQuery();
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
     * @return ChildLocale|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = LocaleTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(LocaleTableMap::DATABASE_NAME);
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
     * @return   ChildLocale A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, NAME, TRANSLATION, CURRENCY_ID, FLAG FROM locale WHERE ID = :p0';
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
            $obj = new ChildLocale();
            $obj->hydrate($row);
            LocaleTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildLocale|array|mixed the result, formatted by the current formatter
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
     * @return ChildLocaleQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(LocaleTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildLocaleQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(LocaleTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildLocaleQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(LocaleTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(LocaleTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LocaleTableMap::COL_ID, $id, $comparison);
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
     * @return ChildLocaleQuery The current query, for fluid interface
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

        return $this->addUsingAlias(LocaleTableMap::COL_NAME, $name, $comparison);
    }

    /**
     * Filter the query on the translation column
     *
     * Example usage:
     * <code>
     * $query->filterByTranslation('fooValue');   // WHERE translation = 'fooValue'
     * $query->filterByTranslation('%fooValue%'); // WHERE translation LIKE '%fooValue%'
     * </code>
     *
     * @param     string $translation The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLocaleQuery The current query, for fluid interface
     */
    public function filterByTranslation($translation = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($translation)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $translation)) {
                $translation = str_replace('*', '%', $translation);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(LocaleTableMap::COL_TRANSLATION, $translation, $comparison);
    }

    /**
     * Filter the query on the currency_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCurrencyId(1234); // WHERE currency_id = 1234
     * $query->filterByCurrencyId(array(12, 34)); // WHERE currency_id IN (12, 34)
     * $query->filterByCurrencyId(array('min' => 12)); // WHERE currency_id > 12
     * </code>
     *
     * @see       filterByCurrency()
     *
     * @param     mixed $currencyId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLocaleQuery The current query, for fluid interface
     */
    public function filterByCurrencyId($currencyId = null, $comparison = null)
    {
        if (is_array($currencyId)) {
            $useMinMax = false;
            if (isset($currencyId['min'])) {
                $this->addUsingAlias(LocaleTableMap::COL_CURRENCY_ID, $currencyId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($currencyId['max'])) {
                $this->addUsingAlias(LocaleTableMap::COL_CURRENCY_ID, $currencyId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LocaleTableMap::COL_CURRENCY_ID, $currencyId, $comparison);
    }

    /**
     * Filter the query on the flag column
     *
     * Example usage:
     * <code>
     * $query->filterByFlag('fooValue');   // WHERE flag = 'fooValue'
     * $query->filterByFlag('%fooValue%'); // WHERE flag LIKE '%fooValue%'
     * </code>
     *
     * @param     string $flag The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLocaleQuery The current query, for fluid interface
     */
    public function filterByFlag($flag = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($flag)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $flag)) {
                $flag = str_replace('*', '%', $flag);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(LocaleTableMap::COL_FLAG, $flag, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Currency\Model\ORM\Currency object
     *
     * @param \Gekosale\Plugin\Currency\Model\ORM\Currency|ObjectCollection $currency The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLocaleQuery The current query, for fluid interface
     */
    public function filterByCurrency($currency, $comparison = null)
    {
        if ($currency instanceof \Gekosale\Plugin\Currency\Model\ORM\Currency) {
            return $this
                ->addUsingAlias(LocaleTableMap::COL_CURRENCY_ID, $currency->getId(), $comparison);
        } elseif ($currency instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(LocaleTableMap::COL_CURRENCY_ID, $currency->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCurrency() only accepts arguments of type \Gekosale\Plugin\Currency\Model\ORM\Currency or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Currency relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildLocaleQuery The current query, for fluid interface
     */
    public function joinCurrency($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Currency');

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
            $this->addJoinObject($join, 'Currency');
        }

        return $this;
    }

    /**
     * Use the Currency relation Currency object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Currency\Model\ORM\CurrencyQuery A secondary query class using the current class as primary query
     */
    public function useCurrencyQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCurrency($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Currency', '\Gekosale\Plugin\Currency\Model\ORM\CurrencyQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Locale\Model\ORM\LocaleShop object
     *
     * @param \Gekosale\Plugin\Locale\Model\ORM\LocaleShop|ObjectCollection $localeShop  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildLocaleQuery The current query, for fluid interface
     */
    public function filterByLocaleShop($localeShop, $comparison = null)
    {
        if ($localeShop instanceof \Gekosale\Plugin\Locale\Model\ORM\LocaleShop) {
            return $this
                ->addUsingAlias(LocaleTableMap::COL_ID, $localeShop->getLocaleId(), $comparison);
        } elseif ($localeShop instanceof ObjectCollection) {
            return $this
                ->useLocaleShopQuery()
                ->filterByPrimaryKeys($localeShop->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByLocaleShop() only accepts arguments of type \Gekosale\Plugin\Locale\Model\ORM\LocaleShop or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the LocaleShop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildLocaleQuery The current query, for fluid interface
     */
    public function joinLocaleShop($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('LocaleShop');

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
            $this->addJoinObject($join, 'LocaleShop');
        }

        return $this;
    }

    /**
     * Use the LocaleShop relation LocaleShop object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Locale\Model\ORM\LocaleShopQuery A secondary query class using the current class as primary query
     */
    public function useLocaleShopQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinLocaleShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'LocaleShop', '\Gekosale\Plugin\Locale\Model\ORM\LocaleShopQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildLocale $locale Object to remove from the list of results
     *
     * @return ChildLocaleQuery The current query, for fluid interface
     */
    public function prune($locale = null)
    {
        if ($locale) {
            $this->addUsingAlias(LocaleTableMap::COL_ID, $locale->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the locale table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(LocaleTableMap::DATABASE_NAME);
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
            LocaleTableMap::clearInstancePool();
            LocaleTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildLocale or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildLocale object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(LocaleTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(LocaleTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        LocaleTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            LocaleTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // LocaleQuery
