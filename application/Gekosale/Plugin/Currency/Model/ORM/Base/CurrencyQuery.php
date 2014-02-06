<?php

namespace Gekosale\Plugin\Currency\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Currency\Model\ORM\Currency as ChildCurrency;
use Gekosale\Plugin\Currency\Model\ORM\CurrencyI18nQuery as ChildCurrencyI18nQuery;
use Gekosale\Plugin\Currency\Model\ORM\CurrencyQuery as ChildCurrencyQuery;
use Gekosale\Plugin\Currency\Model\ORM\Map\CurrencyTableMap;
use Gekosale\Plugin\Locale\Model\ORM\Locale;
use Gekosale\Plugin\Product\Model\ORM\Product;
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
 * Base class that represents a query for the 'currency' table.
 *
 * 
 *
 * @method     ChildCurrencyQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildCurrencyQuery orderByCurrencySymbol($order = Criteria::ASC) Order by the currency_symbol column
 * @method     ChildCurrencyQuery orderByDecimalSeparator($order = Criteria::ASC) Order by the decimal_separator column
 * @method     ChildCurrencyQuery orderByThousandSeparator($order = Criteria::ASC) Order by the thousand_separator column
 * @method     ChildCurrencyQuery orderByPositivePreffix($order = Criteria::ASC) Order by the positive_preffix column
 * @method     ChildCurrencyQuery orderByPositiveSuffix($order = Criteria::ASC) Order by the positive_suffix column
 * @method     ChildCurrencyQuery orderByNegativePreffix($order = Criteria::ASC) Order by the negative_preffix column
 * @method     ChildCurrencyQuery orderByNegativeSuffix($order = Criteria::ASC) Order by the negative_suffix column
 * @method     ChildCurrencyQuery orderByDecimalCount($order = Criteria::ASC) Order by the decimal_count column
 * @method     ChildCurrencyQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildCurrencyQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildCurrencyQuery groupById() Group by the id column
 * @method     ChildCurrencyQuery groupByCurrencySymbol() Group by the currency_symbol column
 * @method     ChildCurrencyQuery groupByDecimalSeparator() Group by the decimal_separator column
 * @method     ChildCurrencyQuery groupByThousandSeparator() Group by the thousand_separator column
 * @method     ChildCurrencyQuery groupByPositivePreffix() Group by the positive_preffix column
 * @method     ChildCurrencyQuery groupByPositiveSuffix() Group by the positive_suffix column
 * @method     ChildCurrencyQuery groupByNegativePreffix() Group by the negative_preffix column
 * @method     ChildCurrencyQuery groupByNegativeSuffix() Group by the negative_suffix column
 * @method     ChildCurrencyQuery groupByDecimalCount() Group by the decimal_count column
 * @method     ChildCurrencyQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildCurrencyQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildCurrencyQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildCurrencyQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildCurrencyQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildCurrencyQuery leftJoinLocale($relationAlias = null) Adds a LEFT JOIN clause to the query using the Locale relation
 * @method     ChildCurrencyQuery rightJoinLocale($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Locale relation
 * @method     ChildCurrencyQuery innerJoinLocale($relationAlias = null) Adds a INNER JOIN clause to the query using the Locale relation
 *
 * @method     ChildCurrencyQuery leftJoinProductRelatedByBuyCurrencyId($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductRelatedByBuyCurrencyId relation
 * @method     ChildCurrencyQuery rightJoinProductRelatedByBuyCurrencyId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductRelatedByBuyCurrencyId relation
 * @method     ChildCurrencyQuery innerJoinProductRelatedByBuyCurrencyId($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductRelatedByBuyCurrencyId relation
 *
 * @method     ChildCurrencyQuery leftJoinProductRelatedBySellCurrencyId($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductRelatedBySellCurrencyId relation
 * @method     ChildCurrencyQuery rightJoinProductRelatedBySellCurrencyId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductRelatedBySellCurrencyId relation
 * @method     ChildCurrencyQuery innerJoinProductRelatedBySellCurrencyId($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductRelatedBySellCurrencyId relation
 *
 * @method     ChildCurrencyQuery leftJoinShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the Shop relation
 * @method     ChildCurrencyQuery rightJoinShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Shop relation
 * @method     ChildCurrencyQuery innerJoinShop($relationAlias = null) Adds a INNER JOIN clause to the query using the Shop relation
 *
 * @method     ChildCurrencyQuery leftJoinCurrencyShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the CurrencyShop relation
 * @method     ChildCurrencyQuery rightJoinCurrencyShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CurrencyShop relation
 * @method     ChildCurrencyQuery innerJoinCurrencyShop($relationAlias = null) Adds a INNER JOIN clause to the query using the CurrencyShop relation
 *
 * @method     ChildCurrencyQuery leftJoinCurrencyI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the CurrencyI18n relation
 * @method     ChildCurrencyQuery rightJoinCurrencyI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CurrencyI18n relation
 * @method     ChildCurrencyQuery innerJoinCurrencyI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the CurrencyI18n relation
 *
 * @method     ChildCurrency findOne(ConnectionInterface $con = null) Return the first ChildCurrency matching the query
 * @method     ChildCurrency findOneOrCreate(ConnectionInterface $con = null) Return the first ChildCurrency matching the query, or a new ChildCurrency object populated from the query conditions when no match is found
 *
 * @method     ChildCurrency findOneById(int $id) Return the first ChildCurrency filtered by the id column
 * @method     ChildCurrency findOneByCurrencySymbol(string $currency_symbol) Return the first ChildCurrency filtered by the currency_symbol column
 * @method     ChildCurrency findOneByDecimalSeparator(string $decimal_separator) Return the first ChildCurrency filtered by the decimal_separator column
 * @method     ChildCurrency findOneByThousandSeparator(string $thousand_separator) Return the first ChildCurrency filtered by the thousand_separator column
 * @method     ChildCurrency findOneByPositivePreffix(string $positive_preffix) Return the first ChildCurrency filtered by the positive_preffix column
 * @method     ChildCurrency findOneByPositiveSuffix(string $positive_suffix) Return the first ChildCurrency filtered by the positive_suffix column
 * @method     ChildCurrency findOneByNegativePreffix(string $negative_preffix) Return the first ChildCurrency filtered by the negative_preffix column
 * @method     ChildCurrency findOneByNegativeSuffix(string $negative_suffix) Return the first ChildCurrency filtered by the negative_suffix column
 * @method     ChildCurrency findOneByDecimalCount(int $decimal_count) Return the first ChildCurrency filtered by the decimal_count column
 * @method     ChildCurrency findOneByCreatedAt(string $created_at) Return the first ChildCurrency filtered by the created_at column
 * @method     ChildCurrency findOneByUpdatedAt(string $updated_at) Return the first ChildCurrency filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildCurrency objects filtered by the id column
 * @method     array findByCurrencySymbol(string $currency_symbol) Return ChildCurrency objects filtered by the currency_symbol column
 * @method     array findByDecimalSeparator(string $decimal_separator) Return ChildCurrency objects filtered by the decimal_separator column
 * @method     array findByThousandSeparator(string $thousand_separator) Return ChildCurrency objects filtered by the thousand_separator column
 * @method     array findByPositivePreffix(string $positive_preffix) Return ChildCurrency objects filtered by the positive_preffix column
 * @method     array findByPositiveSuffix(string $positive_suffix) Return ChildCurrency objects filtered by the positive_suffix column
 * @method     array findByNegativePreffix(string $negative_preffix) Return ChildCurrency objects filtered by the negative_preffix column
 * @method     array findByNegativeSuffix(string $negative_suffix) Return ChildCurrency objects filtered by the negative_suffix column
 * @method     array findByDecimalCount(int $decimal_count) Return ChildCurrency objects filtered by the decimal_count column
 * @method     array findByCreatedAt(string $created_at) Return ChildCurrency objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildCurrency objects filtered by the updated_at column
 *
 */
abstract class CurrencyQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Currency\Model\ORM\Base\CurrencyQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Currency\\Model\\ORM\\Currency', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildCurrencyQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildCurrencyQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Currency\Model\ORM\CurrencyQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Currency\Model\ORM\CurrencyQuery();
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
     * @return ChildCurrency|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CurrencyTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CurrencyTableMap::DATABASE_NAME);
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
     * @return   ChildCurrency A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, CURRENCY_SYMBOL, DECIMAL_SEPARATOR, THOUSAND_SEPARATOR, POSITIVE_PREFFIX, POSITIVE_SUFFIX, NEGATIVE_PREFFIX, NEGATIVE_SUFFIX, DECIMAL_COUNT, CREATED_AT, UPDATED_AT FROM currency WHERE ID = :p0';
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
            $obj = new ChildCurrency();
            $obj->hydrate($row);
            CurrencyTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildCurrency|array|mixed the result, formatted by the current formatter
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
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CurrencyTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CurrencyTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CurrencyTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CurrencyTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CurrencyTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the currency_symbol column
     *
     * Example usage:
     * <code>
     * $query->filterByCurrencySymbol('fooValue');   // WHERE currency_symbol = 'fooValue'
     * $query->filterByCurrencySymbol('%fooValue%'); // WHERE currency_symbol LIKE '%fooValue%'
     * </code>
     *
     * @param     string $currencySymbol The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByCurrencySymbol($currencySymbol = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($currencySymbol)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $currencySymbol)) {
                $currencySymbol = str_replace('*', '%', $currencySymbol);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CurrencyTableMap::COL_CURRENCY_SYMBOL, $currencySymbol, $comparison);
    }

    /**
     * Filter the query on the decimal_separator column
     *
     * Example usage:
     * <code>
     * $query->filterByDecimalSeparator('fooValue');   // WHERE decimal_separator = 'fooValue'
     * $query->filterByDecimalSeparator('%fooValue%'); // WHERE decimal_separator LIKE '%fooValue%'
     * </code>
     *
     * @param     string $decimalSeparator The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByDecimalSeparator($decimalSeparator = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($decimalSeparator)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $decimalSeparator)) {
                $decimalSeparator = str_replace('*', '%', $decimalSeparator);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CurrencyTableMap::COL_DECIMAL_SEPARATOR, $decimalSeparator, $comparison);
    }

    /**
     * Filter the query on the thousand_separator column
     *
     * Example usage:
     * <code>
     * $query->filterByThousandSeparator('fooValue');   // WHERE thousand_separator = 'fooValue'
     * $query->filterByThousandSeparator('%fooValue%'); // WHERE thousand_separator LIKE '%fooValue%'
     * </code>
     *
     * @param     string $thousandSeparator The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByThousandSeparator($thousandSeparator = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($thousandSeparator)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $thousandSeparator)) {
                $thousandSeparator = str_replace('*', '%', $thousandSeparator);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CurrencyTableMap::COL_THOUSAND_SEPARATOR, $thousandSeparator, $comparison);
    }

    /**
     * Filter the query on the positive_preffix column
     *
     * Example usage:
     * <code>
     * $query->filterByPositivePreffix('fooValue');   // WHERE positive_preffix = 'fooValue'
     * $query->filterByPositivePreffix('%fooValue%'); // WHERE positive_preffix LIKE '%fooValue%'
     * </code>
     *
     * @param     string $positivePreffix The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByPositivePreffix($positivePreffix = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($positivePreffix)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $positivePreffix)) {
                $positivePreffix = str_replace('*', '%', $positivePreffix);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CurrencyTableMap::COL_POSITIVE_PREFFIX, $positivePreffix, $comparison);
    }

    /**
     * Filter the query on the positive_suffix column
     *
     * Example usage:
     * <code>
     * $query->filterByPositiveSuffix('fooValue');   // WHERE positive_suffix = 'fooValue'
     * $query->filterByPositiveSuffix('%fooValue%'); // WHERE positive_suffix LIKE '%fooValue%'
     * </code>
     *
     * @param     string $positiveSuffix The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByPositiveSuffix($positiveSuffix = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($positiveSuffix)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $positiveSuffix)) {
                $positiveSuffix = str_replace('*', '%', $positiveSuffix);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CurrencyTableMap::COL_POSITIVE_SUFFIX, $positiveSuffix, $comparison);
    }

    /**
     * Filter the query on the negative_preffix column
     *
     * Example usage:
     * <code>
     * $query->filterByNegativePreffix('fooValue');   // WHERE negative_preffix = 'fooValue'
     * $query->filterByNegativePreffix('%fooValue%'); // WHERE negative_preffix LIKE '%fooValue%'
     * </code>
     *
     * @param     string $negativePreffix The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByNegativePreffix($negativePreffix = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($negativePreffix)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $negativePreffix)) {
                $negativePreffix = str_replace('*', '%', $negativePreffix);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CurrencyTableMap::COL_NEGATIVE_PREFFIX, $negativePreffix, $comparison);
    }

    /**
     * Filter the query on the negative_suffix column
     *
     * Example usage:
     * <code>
     * $query->filterByNegativeSuffix('fooValue');   // WHERE negative_suffix = 'fooValue'
     * $query->filterByNegativeSuffix('%fooValue%'); // WHERE negative_suffix LIKE '%fooValue%'
     * </code>
     *
     * @param     string $negativeSuffix The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByNegativeSuffix($negativeSuffix = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($negativeSuffix)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $negativeSuffix)) {
                $negativeSuffix = str_replace('*', '%', $negativeSuffix);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CurrencyTableMap::COL_NEGATIVE_SUFFIX, $negativeSuffix, $comparison);
    }

    /**
     * Filter the query on the decimal_count column
     *
     * Example usage:
     * <code>
     * $query->filterByDecimalCount(1234); // WHERE decimal_count = 1234
     * $query->filterByDecimalCount(array(12, 34)); // WHERE decimal_count IN (12, 34)
     * $query->filterByDecimalCount(array('min' => 12)); // WHERE decimal_count > 12
     * </code>
     *
     * @param     mixed $decimalCount The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByDecimalCount($decimalCount = null, $comparison = null)
    {
        if (is_array($decimalCount)) {
            $useMinMax = false;
            if (isset($decimalCount['min'])) {
                $this->addUsingAlias(CurrencyTableMap::COL_DECIMAL_COUNT, $decimalCount['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($decimalCount['max'])) {
                $this->addUsingAlias(CurrencyTableMap::COL_DECIMAL_COUNT, $decimalCount['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CurrencyTableMap::COL_DECIMAL_COUNT, $decimalCount, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(CurrencyTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(CurrencyTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CurrencyTableMap::COL_CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(CurrencyTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(CurrencyTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CurrencyTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Locale\Model\ORM\Locale object
     *
     * @param \Gekosale\Plugin\Locale\Model\ORM\Locale|ObjectCollection $locale  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByLocale($locale, $comparison = null)
    {
        if ($locale instanceof \Gekosale\Plugin\Locale\Model\ORM\Locale) {
            return $this
                ->addUsingAlias(CurrencyTableMap::COL_ID, $locale->getCurrencyId(), $comparison);
        } elseif ($locale instanceof ObjectCollection) {
            return $this
                ->useLocaleQuery()
                ->filterByPrimaryKeys($locale->getPrimaryKeys())
                ->endUse();
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
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function joinLocale($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useLocaleQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinLocale($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Locale', '\Gekosale\Plugin\Locale\Model\ORM\LocaleQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Product\Model\ORM\Product object
     *
     * @param \Gekosale\Plugin\Product\Model\ORM\Product|ObjectCollection $product  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByProductRelatedByBuyCurrencyId($product, $comparison = null)
    {
        if ($product instanceof \Gekosale\Plugin\Product\Model\ORM\Product) {
            return $this
                ->addUsingAlias(CurrencyTableMap::COL_ID, $product->getBuyCurrencyId(), $comparison);
        } elseif ($product instanceof ObjectCollection) {
            return $this
                ->useProductRelatedByBuyCurrencyIdQuery()
                ->filterByPrimaryKeys($product->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductRelatedByBuyCurrencyId() only accepts arguments of type \Gekosale\Plugin\Product\Model\ORM\Product or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductRelatedByBuyCurrencyId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function joinProductRelatedByBuyCurrencyId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductRelatedByBuyCurrencyId');

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
            $this->addJoinObject($join, 'ProductRelatedByBuyCurrencyId');
        }

        return $this;
    }

    /**
     * Use the ProductRelatedByBuyCurrencyId relation Product object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Product\Model\ORM\ProductQuery A secondary query class using the current class as primary query
     */
    public function useProductRelatedByBuyCurrencyIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinProductRelatedByBuyCurrencyId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductRelatedByBuyCurrencyId', '\Gekosale\Plugin\Product\Model\ORM\ProductQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Product\Model\ORM\Product object
     *
     * @param \Gekosale\Plugin\Product\Model\ORM\Product|ObjectCollection $product  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByProductRelatedBySellCurrencyId($product, $comparison = null)
    {
        if ($product instanceof \Gekosale\Plugin\Product\Model\ORM\Product) {
            return $this
                ->addUsingAlias(CurrencyTableMap::COL_ID, $product->getSellCurrencyId(), $comparison);
        } elseif ($product instanceof ObjectCollection) {
            return $this
                ->useProductRelatedBySellCurrencyIdQuery()
                ->filterByPrimaryKeys($product->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductRelatedBySellCurrencyId() only accepts arguments of type \Gekosale\Plugin\Product\Model\ORM\Product or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductRelatedBySellCurrencyId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function joinProductRelatedBySellCurrencyId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductRelatedBySellCurrencyId');

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
            $this->addJoinObject($join, 'ProductRelatedBySellCurrencyId');
        }

        return $this;
    }

    /**
     * Use the ProductRelatedBySellCurrencyId relation Product object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Product\Model\ORM\ProductQuery A secondary query class using the current class as primary query
     */
    public function useProductRelatedBySellCurrencyIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinProductRelatedBySellCurrencyId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductRelatedBySellCurrencyId', '\Gekosale\Plugin\Product\Model\ORM\ProductQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Shop\Model\ORM\Shop object
     *
     * @param \Gekosale\Plugin\Shop\Model\ORM\Shop|ObjectCollection $shop  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByShop($shop, $comparison = null)
    {
        if ($shop instanceof \Gekosale\Plugin\Shop\Model\ORM\Shop) {
            return $this
                ->addUsingAlias(CurrencyTableMap::COL_ID, $shop->getCurrencyId(), $comparison);
        } elseif ($shop instanceof ObjectCollection) {
            return $this
                ->useShopQuery()
                ->filterByPrimaryKeys($shop->getPrimaryKeys())
                ->endUse();
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
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function joinShop($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useShopQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Shop', '\Gekosale\Plugin\Shop\Model\ORM\ShopQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Currency\Model\ORM\CurrencyShop object
     *
     * @param \Gekosale\Plugin\Currency\Model\ORM\CurrencyShop|ObjectCollection $currencyShop  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByCurrencyShop($currencyShop, $comparison = null)
    {
        if ($currencyShop instanceof \Gekosale\Plugin\Currency\Model\ORM\CurrencyShop) {
            return $this
                ->addUsingAlias(CurrencyTableMap::COL_ID, $currencyShop->getCurrencyId(), $comparison);
        } elseif ($currencyShop instanceof ObjectCollection) {
            return $this
                ->useCurrencyShopQuery()
                ->filterByPrimaryKeys($currencyShop->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCurrencyShop() only accepts arguments of type \Gekosale\Plugin\Currency\Model\ORM\CurrencyShop or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CurrencyShop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function joinCurrencyShop($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CurrencyShop');

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
            $this->addJoinObject($join, 'CurrencyShop');
        }

        return $this;
    }

    /**
     * Use the CurrencyShop relation CurrencyShop object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Currency\Model\ORM\CurrencyShopQuery A secondary query class using the current class as primary query
     */
    public function useCurrencyShopQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCurrencyShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CurrencyShop', '\Gekosale\Plugin\Currency\Model\ORM\CurrencyShopQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Currency\Model\ORM\CurrencyI18n object
     *
     * @param \Gekosale\Plugin\Currency\Model\ORM\CurrencyI18n|ObjectCollection $currencyI18n  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function filterByCurrencyI18n($currencyI18n, $comparison = null)
    {
        if ($currencyI18n instanceof \Gekosale\Plugin\Currency\Model\ORM\CurrencyI18n) {
            return $this
                ->addUsingAlias(CurrencyTableMap::COL_ID, $currencyI18n->getId(), $comparison);
        } elseif ($currencyI18n instanceof ObjectCollection) {
            return $this
                ->useCurrencyI18nQuery()
                ->filterByPrimaryKeys($currencyI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCurrencyI18n() only accepts arguments of type \Gekosale\Plugin\Currency\Model\ORM\CurrencyI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CurrencyI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function joinCurrencyI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CurrencyI18n');

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
            $this->addJoinObject($join, 'CurrencyI18n');
        }

        return $this;
    }

    /**
     * Use the CurrencyI18n relation CurrencyI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Currency\Model\ORM\CurrencyI18nQuery A secondary query class using the current class as primary query
     */
    public function useCurrencyI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinCurrencyI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CurrencyI18n', '\Gekosale\Plugin\Currency\Model\ORM\CurrencyI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildCurrency $currency Object to remove from the list of results
     *
     * @return ChildCurrencyQuery The current query, for fluid interface
     */
    public function prune($currency = null)
    {
        if ($currency) {
            $this->addUsingAlias(CurrencyTableMap::COL_ID, $currency->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the currency table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CurrencyTableMap::DATABASE_NAME);
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
            CurrencyTableMap::clearInstancePool();
            CurrencyTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildCurrency or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildCurrency object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CurrencyTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(CurrencyTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        CurrencyTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            CurrencyTableMap::clearRelatedInstancePool();
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
     * @return    ChildCurrencyQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'CurrencyI18n';
    
        return $this
            ->joinCurrencyI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }
    
    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildCurrencyQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('CurrencyI18n');
        $this->with['CurrencyI18n']->setIsWithOneToMany(false);
    
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
     * @return    ChildCurrencyI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CurrencyI18n', '\Gekosale\Plugin\Currency\Model\ORM\CurrencyI18nQuery');
    }

    // timestampable behavior
    
    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     ChildCurrencyQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(CurrencyTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }
    
    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildCurrencyQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(CurrencyTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }
    
    /**
     * Order by update date desc
     *
     * @return     ChildCurrencyQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(CurrencyTableMap::COL_UPDATED_AT);
    }
    
    /**
     * Order by update date asc
     *
     * @return     ChildCurrencyQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(CurrencyTableMap::COL_UPDATED_AT);
    }
    
    /**
     * Order by create date desc
     *
     * @return     ChildCurrencyQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(CurrencyTableMap::COL_CREATED_AT);
    }
    
    /**
     * Order by create date asc
     *
     * @return     ChildCurrencyQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(CurrencyTableMap::COL_CREATED_AT);
    }

} // CurrencyQuery
