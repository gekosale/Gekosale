<?php

namespace Gekosale\Plugin\DispatchMethod\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethod as ChildDispatchMethod;
use Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodI18nQuery as ChildDispatchMethodI18nQuery;
use Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodQuery as ChildDispatchMethodQuery;
use Gekosale\Plugin\DispatchMethod\Model\ORM\Map\DispatchMethodTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'dispatch_method' table.
 *
 * 
 *
 * @method     ChildDispatchMethodQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildDispatchMethodQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method     ChildDispatchMethodQuery orderByMaximumWeight($order = Criteria::ASC) Order by the maximum_weight column
 * @method     ChildDispatchMethodQuery orderByFreeDelivery($order = Criteria::ASC) Order by the free_delivery column
 * @method     ChildDispatchMethodQuery orderByCountryIds($order = Criteria::ASC) Order by the country_ids column
 * @method     ChildDispatchMethodQuery orderByCurrencyId($order = Criteria::ASC) Order by the currency_id column
 * @method     ChildDispatchMethodQuery orderByHierarchy($order = Criteria::ASC) Order by the hierarchy column
 * @method     ChildDispatchMethodQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildDispatchMethodQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildDispatchMethodQuery groupById() Group by the id column
 * @method     ChildDispatchMethodQuery groupByType() Group by the type column
 * @method     ChildDispatchMethodQuery groupByMaximumWeight() Group by the maximum_weight column
 * @method     ChildDispatchMethodQuery groupByFreeDelivery() Group by the free_delivery column
 * @method     ChildDispatchMethodQuery groupByCountryIds() Group by the country_ids column
 * @method     ChildDispatchMethodQuery groupByCurrencyId() Group by the currency_id column
 * @method     ChildDispatchMethodQuery groupByHierarchy() Group by the hierarchy column
 * @method     ChildDispatchMethodQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildDispatchMethodQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildDispatchMethodQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildDispatchMethodQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildDispatchMethodQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildDispatchMethodQuery leftJoinDispatchMethodPrice($relationAlias = null) Adds a LEFT JOIN clause to the query using the DispatchMethodPrice relation
 * @method     ChildDispatchMethodQuery rightJoinDispatchMethodPrice($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DispatchMethodPrice relation
 * @method     ChildDispatchMethodQuery innerJoinDispatchMethodPrice($relationAlias = null) Adds a INNER JOIN clause to the query using the DispatchMethodPrice relation
 *
 * @method     ChildDispatchMethodQuery leftJoinDispatchMethodWeight($relationAlias = null) Adds a LEFT JOIN clause to the query using the DispatchMethodWeight relation
 * @method     ChildDispatchMethodQuery rightJoinDispatchMethodWeight($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DispatchMethodWeight relation
 * @method     ChildDispatchMethodQuery innerJoinDispatchMethodWeight($relationAlias = null) Adds a INNER JOIN clause to the query using the DispatchMethodWeight relation
 *
 * @method     ChildDispatchMethodQuery leftJoinDispatchMethodpaymentMethod($relationAlias = null) Adds a LEFT JOIN clause to the query using the DispatchMethodpaymentMethod relation
 * @method     ChildDispatchMethodQuery rightJoinDispatchMethodpaymentMethod($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DispatchMethodpaymentMethod relation
 * @method     ChildDispatchMethodQuery innerJoinDispatchMethodpaymentMethod($relationAlias = null) Adds a INNER JOIN clause to the query using the DispatchMethodpaymentMethod relation
 *
 * @method     ChildDispatchMethodQuery leftJoinDispatchMethodShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the DispatchMethodShop relation
 * @method     ChildDispatchMethodQuery rightJoinDispatchMethodShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DispatchMethodShop relation
 * @method     ChildDispatchMethodQuery innerJoinDispatchMethodShop($relationAlias = null) Adds a INNER JOIN clause to the query using the DispatchMethodShop relation
 *
 * @method     ChildDispatchMethodQuery leftJoinDispatchMethodI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the DispatchMethodI18n relation
 * @method     ChildDispatchMethodQuery rightJoinDispatchMethodI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DispatchMethodI18n relation
 * @method     ChildDispatchMethodQuery innerJoinDispatchMethodI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the DispatchMethodI18n relation
 *
 * @method     ChildDispatchMethod findOne(ConnectionInterface $con = null) Return the first ChildDispatchMethod matching the query
 * @method     ChildDispatchMethod findOneOrCreate(ConnectionInterface $con = null) Return the first ChildDispatchMethod matching the query, or a new ChildDispatchMethod object populated from the query conditions when no match is found
 *
 * @method     ChildDispatchMethod findOneById(int $id) Return the first ChildDispatchMethod filtered by the id column
 * @method     ChildDispatchMethod findOneByType(int $type) Return the first ChildDispatchMethod filtered by the type column
 * @method     ChildDispatchMethod findOneByMaximumWeight(string $maximum_weight) Return the first ChildDispatchMethod filtered by the maximum_weight column
 * @method     ChildDispatchMethod findOneByFreeDelivery(string $free_delivery) Return the first ChildDispatchMethod filtered by the free_delivery column
 * @method     ChildDispatchMethod findOneByCountryIds(string $country_ids) Return the first ChildDispatchMethod filtered by the country_ids column
 * @method     ChildDispatchMethod findOneByCurrencyId(int $currency_id) Return the first ChildDispatchMethod filtered by the currency_id column
 * @method     ChildDispatchMethod findOneByHierarchy(int $hierarchy) Return the first ChildDispatchMethod filtered by the hierarchy column
 * @method     ChildDispatchMethod findOneByCreatedAt(string $created_at) Return the first ChildDispatchMethod filtered by the created_at column
 * @method     ChildDispatchMethod findOneByUpdatedAt(string $updated_at) Return the first ChildDispatchMethod filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildDispatchMethod objects filtered by the id column
 * @method     array findByType(int $type) Return ChildDispatchMethod objects filtered by the type column
 * @method     array findByMaximumWeight(string $maximum_weight) Return ChildDispatchMethod objects filtered by the maximum_weight column
 * @method     array findByFreeDelivery(string $free_delivery) Return ChildDispatchMethod objects filtered by the free_delivery column
 * @method     array findByCountryIds(string $country_ids) Return ChildDispatchMethod objects filtered by the country_ids column
 * @method     array findByCurrencyId(int $currency_id) Return ChildDispatchMethod objects filtered by the currency_id column
 * @method     array findByHierarchy(int $hierarchy) Return ChildDispatchMethod objects filtered by the hierarchy column
 * @method     array findByCreatedAt(string $created_at) Return ChildDispatchMethod objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildDispatchMethod objects filtered by the updated_at column
 *
 */
abstract class DispatchMethodQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\DispatchMethod\Model\ORM\Base\DispatchMethodQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\DispatchMethod\\Model\\ORM\\DispatchMethod', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildDispatchMethodQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildDispatchMethodQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodQuery();
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
     * @return ChildDispatchMethod|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = DispatchMethodTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(DispatchMethodTableMap::DATABASE_NAME);
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
     * @return   ChildDispatchMethod A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, TYPE, MAXIMUM_WEIGHT, FREE_DELIVERY, COUNTRY_IDS, CURRENCY_ID, HIERARCHY, CREATED_AT, UPDATED_AT FROM dispatch_method WHERE ID = :p0';
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
            $obj = new ChildDispatchMethod();
            $obj->hydrate($row);
            DispatchMethodTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildDispatchMethod|array|mixed the result, formatted by the current formatter
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
     * @return ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(DispatchMethodTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(DispatchMethodTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(DispatchMethodTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(DispatchMethodTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DispatchMethodTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * Example usage:
     * <code>
     * $query->filterByType(1234); // WHERE type = 1234
     * $query->filterByType(array(12, 34)); // WHERE type IN (12, 34)
     * $query->filterByType(array('min' => 12)); // WHERE type > 12
     * </code>
     *
     * @param     mixed $type The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        if (is_array($type)) {
            $useMinMax = false;
            if (isset($type['min'])) {
                $this->addUsingAlias(DispatchMethodTableMap::COL_TYPE, $type['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($type['max'])) {
                $this->addUsingAlias(DispatchMethodTableMap::COL_TYPE, $type['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DispatchMethodTableMap::COL_TYPE, $type, $comparison);
    }

    /**
     * Filter the query on the maximum_weight column
     *
     * Example usage:
     * <code>
     * $query->filterByMaximumWeight(1234); // WHERE maximum_weight = 1234
     * $query->filterByMaximumWeight(array(12, 34)); // WHERE maximum_weight IN (12, 34)
     * $query->filterByMaximumWeight(array('min' => 12)); // WHERE maximum_weight > 12
     * </code>
     *
     * @param     mixed $maximumWeight The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function filterByMaximumWeight($maximumWeight = null, $comparison = null)
    {
        if (is_array($maximumWeight)) {
            $useMinMax = false;
            if (isset($maximumWeight['min'])) {
                $this->addUsingAlias(DispatchMethodTableMap::COL_MAXIMUM_WEIGHT, $maximumWeight['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($maximumWeight['max'])) {
                $this->addUsingAlias(DispatchMethodTableMap::COL_MAXIMUM_WEIGHT, $maximumWeight['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DispatchMethodTableMap::COL_MAXIMUM_WEIGHT, $maximumWeight, $comparison);
    }

    /**
     * Filter the query on the free_delivery column
     *
     * Example usage:
     * <code>
     * $query->filterByFreeDelivery(1234); // WHERE free_delivery = 1234
     * $query->filterByFreeDelivery(array(12, 34)); // WHERE free_delivery IN (12, 34)
     * $query->filterByFreeDelivery(array('min' => 12)); // WHERE free_delivery > 12
     * </code>
     *
     * @param     mixed $freeDelivery The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function filterByFreeDelivery($freeDelivery = null, $comparison = null)
    {
        if (is_array($freeDelivery)) {
            $useMinMax = false;
            if (isset($freeDelivery['min'])) {
                $this->addUsingAlias(DispatchMethodTableMap::COL_FREE_DELIVERY, $freeDelivery['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($freeDelivery['max'])) {
                $this->addUsingAlias(DispatchMethodTableMap::COL_FREE_DELIVERY, $freeDelivery['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DispatchMethodTableMap::COL_FREE_DELIVERY, $freeDelivery, $comparison);
    }

    /**
     * Filter the query on the country_ids column
     *
     * Example usage:
     * <code>
     * $query->filterByCountryIds('fooValue');   // WHERE country_ids = 'fooValue'
     * $query->filterByCountryIds('%fooValue%'); // WHERE country_ids LIKE '%fooValue%'
     * </code>
     *
     * @param     string $countryIds The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function filterByCountryIds($countryIds = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($countryIds)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $countryIds)) {
                $countryIds = str_replace('*', '%', $countryIds);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(DispatchMethodTableMap::COL_COUNTRY_IDS, $countryIds, $comparison);
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
     * @param     mixed $currencyId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function filterByCurrencyId($currencyId = null, $comparison = null)
    {
        if (is_array($currencyId)) {
            $useMinMax = false;
            if (isset($currencyId['min'])) {
                $this->addUsingAlias(DispatchMethodTableMap::COL_CURRENCY_ID, $currencyId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($currencyId['max'])) {
                $this->addUsingAlias(DispatchMethodTableMap::COL_CURRENCY_ID, $currencyId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DispatchMethodTableMap::COL_CURRENCY_ID, $currencyId, $comparison);
    }

    /**
     * Filter the query on the hierarchy column
     *
     * Example usage:
     * <code>
     * $query->filterByHierarchy(1234); // WHERE hierarchy = 1234
     * $query->filterByHierarchy(array(12, 34)); // WHERE hierarchy IN (12, 34)
     * $query->filterByHierarchy(array('min' => 12)); // WHERE hierarchy > 12
     * </code>
     *
     * @param     mixed $hierarchy The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function filterByHierarchy($hierarchy = null, $comparison = null)
    {
        if (is_array($hierarchy)) {
            $useMinMax = false;
            if (isset($hierarchy['min'])) {
                $this->addUsingAlias(DispatchMethodTableMap::COL_HIERARCHY, $hierarchy['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($hierarchy['max'])) {
                $this->addUsingAlias(DispatchMethodTableMap::COL_HIERARCHY, $hierarchy['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DispatchMethodTableMap::COL_HIERARCHY, $hierarchy, $comparison);
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
     * @return ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(DispatchMethodTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(DispatchMethodTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DispatchMethodTableMap::COL_CREATED_AT, $createdAt, $comparison);
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
     * @return ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(DispatchMethodTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(DispatchMethodTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DispatchMethodTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodPrice object
     *
     * @param \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodPrice|ObjectCollection $dispatchMethodPrice  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function filterByDispatchMethodPrice($dispatchMethodPrice, $comparison = null)
    {
        if ($dispatchMethodPrice instanceof \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodPrice) {
            return $this
                ->addUsingAlias(DispatchMethodTableMap::COL_ID, $dispatchMethodPrice->getDispatchMethodId(), $comparison);
        } elseif ($dispatchMethodPrice instanceof ObjectCollection) {
            return $this
                ->useDispatchMethodPriceQuery()
                ->filterByPrimaryKeys($dispatchMethodPrice->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDispatchMethodPrice() only accepts arguments of type \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodPrice or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DispatchMethodPrice relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function joinDispatchMethodPrice($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DispatchMethodPrice');

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
            $this->addJoinObject($join, 'DispatchMethodPrice');
        }

        return $this;
    }

    /**
     * Use the DispatchMethodPrice relation DispatchMethodPrice object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodPriceQuery A secondary query class using the current class as primary query
     */
    public function useDispatchMethodPriceQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinDispatchMethodPrice($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DispatchMethodPrice', '\Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodPriceQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodWeight object
     *
     * @param \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodWeight|ObjectCollection $dispatchMethodWeight  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function filterByDispatchMethodWeight($dispatchMethodWeight, $comparison = null)
    {
        if ($dispatchMethodWeight instanceof \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodWeight) {
            return $this
                ->addUsingAlias(DispatchMethodTableMap::COL_ID, $dispatchMethodWeight->getDispatchMethodId(), $comparison);
        } elseif ($dispatchMethodWeight instanceof ObjectCollection) {
            return $this
                ->useDispatchMethodWeightQuery()
                ->filterByPrimaryKeys($dispatchMethodWeight->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDispatchMethodWeight() only accepts arguments of type \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodWeight or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DispatchMethodWeight relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function joinDispatchMethodWeight($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DispatchMethodWeight');

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
            $this->addJoinObject($join, 'DispatchMethodWeight');
        }

        return $this;
    }

    /**
     * Use the DispatchMethodWeight relation DispatchMethodWeight object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodWeightQuery A secondary query class using the current class as primary query
     */
    public function useDispatchMethodWeightQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDispatchMethodWeight($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DispatchMethodWeight', '\Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodWeightQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodpaymentMethod object
     *
     * @param \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodpaymentMethod|ObjectCollection $dispatchMethodpaymentMethod  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function filterByDispatchMethodpaymentMethod($dispatchMethodpaymentMethod, $comparison = null)
    {
        if ($dispatchMethodpaymentMethod instanceof \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodpaymentMethod) {
            return $this
                ->addUsingAlias(DispatchMethodTableMap::COL_ID, $dispatchMethodpaymentMethod->getDispatchMethodId(), $comparison);
        } elseif ($dispatchMethodpaymentMethod instanceof ObjectCollection) {
            return $this
                ->useDispatchMethodpaymentMethodQuery()
                ->filterByPrimaryKeys($dispatchMethodpaymentMethod->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDispatchMethodpaymentMethod() only accepts arguments of type \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodpaymentMethod or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DispatchMethodpaymentMethod relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function joinDispatchMethodpaymentMethod($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DispatchMethodpaymentMethod');

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
            $this->addJoinObject($join, 'DispatchMethodpaymentMethod');
        }

        return $this;
    }

    /**
     * Use the DispatchMethodpaymentMethod relation DispatchMethodpaymentMethod object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodpaymentMethodQuery A secondary query class using the current class as primary query
     */
    public function useDispatchMethodpaymentMethodQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDispatchMethodpaymentMethod($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DispatchMethodpaymentMethod', '\Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodpaymentMethodQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodShop object
     *
     * @param \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodShop|ObjectCollection $dispatchMethodShop  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function filterByDispatchMethodShop($dispatchMethodShop, $comparison = null)
    {
        if ($dispatchMethodShop instanceof \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodShop) {
            return $this
                ->addUsingAlias(DispatchMethodTableMap::COL_ID, $dispatchMethodShop->getDispatchMethodId(), $comparison);
        } elseif ($dispatchMethodShop instanceof ObjectCollection) {
            return $this
                ->useDispatchMethodShopQuery()
                ->filterByPrimaryKeys($dispatchMethodShop->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDispatchMethodShop() only accepts arguments of type \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodShop or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DispatchMethodShop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function joinDispatchMethodShop($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DispatchMethodShop');

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
            $this->addJoinObject($join, 'DispatchMethodShop');
        }

        return $this;
    }

    /**
     * Use the DispatchMethodShop relation DispatchMethodShop object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodShopQuery A secondary query class using the current class as primary query
     */
    public function useDispatchMethodShopQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDispatchMethodShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DispatchMethodShop', '\Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodShopQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodI18n object
     *
     * @param \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodI18n|ObjectCollection $dispatchMethodI18n  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function filterByDispatchMethodI18n($dispatchMethodI18n, $comparison = null)
    {
        if ($dispatchMethodI18n instanceof \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodI18n) {
            return $this
                ->addUsingAlias(DispatchMethodTableMap::COL_ID, $dispatchMethodI18n->getId(), $comparison);
        } elseif ($dispatchMethodI18n instanceof ObjectCollection) {
            return $this
                ->useDispatchMethodI18nQuery()
                ->filterByPrimaryKeys($dispatchMethodI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDispatchMethodI18n() only accepts arguments of type \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DispatchMethodI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function joinDispatchMethodI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DispatchMethodI18n');

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
            $this->addJoinObject($join, 'DispatchMethodI18n');
        }

        return $this;
    }

    /**
     * Use the DispatchMethodI18n relation DispatchMethodI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodI18nQuery A secondary query class using the current class as primary query
     */
    public function useDispatchMethodI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinDispatchMethodI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DispatchMethodI18n', '\Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildDispatchMethod $dispatchMethod Object to remove from the list of results
     *
     * @return ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function prune($dispatchMethod = null)
    {
        if ($dispatchMethod) {
            $this->addUsingAlias(DispatchMethodTableMap::COL_ID, $dispatchMethod->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the dispatch_method table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DispatchMethodTableMap::DATABASE_NAME);
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
            DispatchMethodTableMap::clearInstancePool();
            DispatchMethodTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildDispatchMethod or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildDispatchMethod object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(DispatchMethodTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(DispatchMethodTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        DispatchMethodTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            DispatchMethodTableMap::clearRelatedInstancePool();
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
     * @return    ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'DispatchMethodI18n';
    
        return $this
            ->joinDispatchMethodI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }
    
    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('DispatchMethodI18n');
        $this->with['DispatchMethodI18n']->setIsWithOneToMany(false);
    
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
     * @return    ChildDispatchMethodI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DispatchMethodI18n', '\Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodI18nQuery');
    }

    // timestampable behavior
    
    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(DispatchMethodTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }
    
    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(DispatchMethodTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }
    
    /**
     * Order by update date desc
     *
     * @return     ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(DispatchMethodTableMap::COL_UPDATED_AT);
    }
    
    /**
     * Order by update date asc
     *
     * @return     ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(DispatchMethodTableMap::COL_UPDATED_AT);
    }
    
    /**
     * Order by create date desc
     *
     * @return     ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(DispatchMethodTableMap::COL_CREATED_AT);
    }
    
    /**
     * Order by create date asc
     *
     * @return     ChildDispatchMethodQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(DispatchMethodTableMap::COL_CREATED_AT);
    }

} // DispatchMethodQuery
