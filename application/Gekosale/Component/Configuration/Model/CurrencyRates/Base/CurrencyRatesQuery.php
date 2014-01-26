<?php

namespace Gekosale\Component\Configuration\Model\CurrencyRates\Base;

use \Exception;
use \PDO;
use Gekosale\Component\Configuration\Model\CurrencyRates\CurrencyRates as ChildCurrencyRates;
use Gekosale\Component\Configuration\Model\CurrencyRates\CurrencyRatesQuery as ChildCurrencyRatesQuery;
use Gekosale\Component\Configuration\Model\CurrencyRates\Map\CurrencyRatesTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'currency_rates' table.
 *
 * 
 *
 * @method     ChildCurrencyRatesQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildCurrencyRatesQuery orderByCurrencyFrom($order = Criteria::ASC) Order by the currency_from column
 * @method     ChildCurrencyRatesQuery orderByCurrencyTo($order = Criteria::ASC) Order by the currency_to column
 * @method     ChildCurrencyRatesQuery orderByExchangeRate($order = Criteria::ASC) Order by the exchange_rate column
 *
 * @method     ChildCurrencyRatesQuery groupById() Group by the id column
 * @method     ChildCurrencyRatesQuery groupByCurrencyFrom() Group by the currency_from column
 * @method     ChildCurrencyRatesQuery groupByCurrencyTo() Group by the currency_to column
 * @method     ChildCurrencyRatesQuery groupByExchangeRate() Group by the exchange_rate column
 *
 * @method     ChildCurrencyRatesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildCurrencyRatesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildCurrencyRatesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildCurrencyRates findOne(ConnectionInterface $con = null) Return the first ChildCurrencyRates matching the query
 * @method     ChildCurrencyRates findOneOrCreate(ConnectionInterface $con = null) Return the first ChildCurrencyRates matching the query, or a new ChildCurrencyRates object populated from the query conditions when no match is found
 *
 * @method     ChildCurrencyRates findOneById(int $id) Return the first ChildCurrencyRates filtered by the id column
 * @method     ChildCurrencyRates findOneByCurrencyFrom(int $currency_from) Return the first ChildCurrencyRates filtered by the currency_from column
 * @method     ChildCurrencyRates findOneByCurrencyTo(int $currency_to) Return the first ChildCurrencyRates filtered by the currency_to column
 * @method     ChildCurrencyRates findOneByExchangeRate(string $exchange_rate) Return the first ChildCurrencyRates filtered by the exchange_rate column
 *
 * @method     array findById(int $id) Return ChildCurrencyRates objects filtered by the id column
 * @method     array findByCurrencyFrom(int $currency_from) Return ChildCurrencyRates objects filtered by the currency_from column
 * @method     array findByCurrencyTo(int $currency_to) Return ChildCurrencyRates objects filtered by the currency_to column
 * @method     array findByExchangeRate(string $exchange_rate) Return ChildCurrencyRates objects filtered by the exchange_rate column
 *
 */
abstract class CurrencyRatesQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Component\Configuration\Model\CurrencyRates\Base\CurrencyRatesQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Component\\Configuration\\Model\\CurrencyRates\\CurrencyRates', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildCurrencyRatesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildCurrencyRatesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Component\Configuration\Model\CurrencyRates\CurrencyRatesQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Component\Configuration\Model\CurrencyRates\CurrencyRatesQuery();
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
     * @return ChildCurrencyRates|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CurrencyRatesTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CurrencyRatesTableMap::DATABASE_NAME);
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
     * @return   ChildCurrencyRates A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, CURRENCY_FROM, CURRENCY_TO, EXCHANGE_RATE FROM currency_rates WHERE ID = :p0';
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
            $obj = new ChildCurrencyRates();
            $obj->hydrate($row);
            CurrencyRatesTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildCurrencyRates|array|mixed the result, formatted by the current formatter
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
     * @return ChildCurrencyRatesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CurrencyRatesTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildCurrencyRatesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CurrencyRatesTableMap::ID, $keys, Criteria::IN);
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
     * @return ChildCurrencyRatesQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CurrencyRatesTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CurrencyRatesTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CurrencyRatesTableMap::ID, $id, $comparison);
    }

    /**
     * Filter the query on the currency_from column
     *
     * Example usage:
     * <code>
     * $query->filterByCurrencyFrom(1234); // WHERE currency_from = 1234
     * $query->filterByCurrencyFrom(array(12, 34)); // WHERE currency_from IN (12, 34)
     * $query->filterByCurrencyFrom(array('min' => 12)); // WHERE currency_from > 12
     * </code>
     *
     * @param     mixed $currencyFrom The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyRatesQuery The current query, for fluid interface
     */
    public function filterByCurrencyFrom($currencyFrom = null, $comparison = null)
    {
        if (is_array($currencyFrom)) {
            $useMinMax = false;
            if (isset($currencyFrom['min'])) {
                $this->addUsingAlias(CurrencyRatesTableMap::CURRENCY_FROM, $currencyFrom['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($currencyFrom['max'])) {
                $this->addUsingAlias(CurrencyRatesTableMap::CURRENCY_FROM, $currencyFrom['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CurrencyRatesTableMap::CURRENCY_FROM, $currencyFrom, $comparison);
    }

    /**
     * Filter the query on the currency_to column
     *
     * Example usage:
     * <code>
     * $query->filterByCurrencyTo(1234); // WHERE currency_to = 1234
     * $query->filterByCurrencyTo(array(12, 34)); // WHERE currency_to IN (12, 34)
     * $query->filterByCurrencyTo(array('min' => 12)); // WHERE currency_to > 12
     * </code>
     *
     * @param     mixed $currencyTo The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyRatesQuery The current query, for fluid interface
     */
    public function filterByCurrencyTo($currencyTo = null, $comparison = null)
    {
        if (is_array($currencyTo)) {
            $useMinMax = false;
            if (isset($currencyTo['min'])) {
                $this->addUsingAlias(CurrencyRatesTableMap::CURRENCY_TO, $currencyTo['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($currencyTo['max'])) {
                $this->addUsingAlias(CurrencyRatesTableMap::CURRENCY_TO, $currencyTo['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CurrencyRatesTableMap::CURRENCY_TO, $currencyTo, $comparison);
    }

    /**
     * Filter the query on the exchange_rate column
     *
     * Example usage:
     * <code>
     * $query->filterByExchangeRate(1234); // WHERE exchange_rate = 1234
     * $query->filterByExchangeRate(array(12, 34)); // WHERE exchange_rate IN (12, 34)
     * $query->filterByExchangeRate(array('min' => 12)); // WHERE exchange_rate > 12
     * </code>
     *
     * @param     mixed $exchangeRate The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyRatesQuery The current query, for fluid interface
     */
    public function filterByExchangeRate($exchangeRate = null, $comparison = null)
    {
        if (is_array($exchangeRate)) {
            $useMinMax = false;
            if (isset($exchangeRate['min'])) {
                $this->addUsingAlias(CurrencyRatesTableMap::EXCHANGE_RATE, $exchangeRate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($exchangeRate['max'])) {
                $this->addUsingAlias(CurrencyRatesTableMap::EXCHANGE_RATE, $exchangeRate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CurrencyRatesTableMap::EXCHANGE_RATE, $exchangeRate, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildCurrencyRates $currencyRates Object to remove from the list of results
     *
     * @return ChildCurrencyRatesQuery The current query, for fluid interface
     */
    public function prune($currencyRates = null)
    {
        if ($currencyRates) {
            $this->addUsingAlias(CurrencyRatesTableMap::ID, $currencyRates->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the currency_rates table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CurrencyRatesTableMap::DATABASE_NAME);
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
            CurrencyRatesTableMap::clearInstancePool();
            CurrencyRatesTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildCurrencyRates or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildCurrencyRates object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CurrencyRatesTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(CurrencyRatesTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        CurrencyRatesTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            CurrencyRatesTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // CurrencyRatesQuery
