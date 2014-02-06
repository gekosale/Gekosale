<?php

namespace Gekosale\Plugin\PaymentMethod\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodpaymentMethod;
use Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethod as ChildPaymentMethod;
use Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodI18nQuery as ChildPaymentMethodI18nQuery;
use Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodQuery as ChildPaymentMethodQuery;
use Gekosale\Plugin\PaymentMethod\Model\ORM\Map\PaymentMethodTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'payment_method' table.
 *
 * 
 *
 * @method     ChildPaymentMethodQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildPaymentMethodQuery orderByController($order = Criteria::ASC) Order by the controller column
 * @method     ChildPaymentMethodQuery orderByIsOnline($order = Criteria::ASC) Order by the is_online column
 * @method     ChildPaymentMethodQuery orderByIsActive($order = Criteria::ASC) Order by the is_active column
 * @method     ChildPaymentMethodQuery orderByMaximumAmount($order = Criteria::ASC) Order by the maximum_amount column
 * @method     ChildPaymentMethodQuery orderByHierarchy($order = Criteria::ASC) Order by the hierarchy column
 * @method     ChildPaymentMethodQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildPaymentMethodQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildPaymentMethodQuery groupById() Group by the id column
 * @method     ChildPaymentMethodQuery groupByController() Group by the controller column
 * @method     ChildPaymentMethodQuery groupByIsOnline() Group by the is_online column
 * @method     ChildPaymentMethodQuery groupByIsActive() Group by the is_active column
 * @method     ChildPaymentMethodQuery groupByMaximumAmount() Group by the maximum_amount column
 * @method     ChildPaymentMethodQuery groupByHierarchy() Group by the hierarchy column
 * @method     ChildPaymentMethodQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildPaymentMethodQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildPaymentMethodQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildPaymentMethodQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildPaymentMethodQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildPaymentMethodQuery leftJoinDispatchMethodpaymentMethod($relationAlias = null) Adds a LEFT JOIN clause to the query using the DispatchMethodpaymentMethod relation
 * @method     ChildPaymentMethodQuery rightJoinDispatchMethodpaymentMethod($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DispatchMethodpaymentMethod relation
 * @method     ChildPaymentMethodQuery innerJoinDispatchMethodpaymentMethod($relationAlias = null) Adds a INNER JOIN clause to the query using the DispatchMethodpaymentMethod relation
 *
 * @method     ChildPaymentMethodQuery leftJoinPaymentMethodShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the PaymentMethodShop relation
 * @method     ChildPaymentMethodQuery rightJoinPaymentMethodShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PaymentMethodShop relation
 * @method     ChildPaymentMethodQuery innerJoinPaymentMethodShop($relationAlias = null) Adds a INNER JOIN clause to the query using the PaymentMethodShop relation
 *
 * @method     ChildPaymentMethodQuery leftJoinPaymentMethodI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the PaymentMethodI18n relation
 * @method     ChildPaymentMethodQuery rightJoinPaymentMethodI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PaymentMethodI18n relation
 * @method     ChildPaymentMethodQuery innerJoinPaymentMethodI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the PaymentMethodI18n relation
 *
 * @method     ChildPaymentMethod findOne(ConnectionInterface $con = null) Return the first ChildPaymentMethod matching the query
 * @method     ChildPaymentMethod findOneOrCreate(ConnectionInterface $con = null) Return the first ChildPaymentMethod matching the query, or a new ChildPaymentMethod object populated from the query conditions when no match is found
 *
 * @method     ChildPaymentMethod findOneById(int $id) Return the first ChildPaymentMethod filtered by the id column
 * @method     ChildPaymentMethod findOneByController(string $controller) Return the first ChildPaymentMethod filtered by the controller column
 * @method     ChildPaymentMethod findOneByIsOnline(boolean $is_online) Return the first ChildPaymentMethod filtered by the is_online column
 * @method     ChildPaymentMethod findOneByIsActive(boolean $is_active) Return the first ChildPaymentMethod filtered by the is_active column
 * @method     ChildPaymentMethod findOneByMaximumAmount(string $maximum_amount) Return the first ChildPaymentMethod filtered by the maximum_amount column
 * @method     ChildPaymentMethod findOneByHierarchy(int $hierarchy) Return the first ChildPaymentMethod filtered by the hierarchy column
 * @method     ChildPaymentMethod findOneByCreatedAt(string $created_at) Return the first ChildPaymentMethod filtered by the created_at column
 * @method     ChildPaymentMethod findOneByUpdatedAt(string $updated_at) Return the first ChildPaymentMethod filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildPaymentMethod objects filtered by the id column
 * @method     array findByController(string $controller) Return ChildPaymentMethod objects filtered by the controller column
 * @method     array findByIsOnline(boolean $is_online) Return ChildPaymentMethod objects filtered by the is_online column
 * @method     array findByIsActive(boolean $is_active) Return ChildPaymentMethod objects filtered by the is_active column
 * @method     array findByMaximumAmount(string $maximum_amount) Return ChildPaymentMethod objects filtered by the maximum_amount column
 * @method     array findByHierarchy(int $hierarchy) Return ChildPaymentMethod objects filtered by the hierarchy column
 * @method     array findByCreatedAt(string $created_at) Return ChildPaymentMethod objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildPaymentMethod objects filtered by the updated_at column
 *
 */
abstract class PaymentMethodQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\PaymentMethod\Model\ORM\Base\PaymentMethodQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\PaymentMethod\\Model\\ORM\\PaymentMethod', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildPaymentMethodQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildPaymentMethodQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodQuery();
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
     * @return ChildPaymentMethod|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = PaymentMethodTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(PaymentMethodTableMap::DATABASE_NAME);
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
     * @return   ChildPaymentMethod A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, CONTROLLER, IS_ONLINE, IS_ACTIVE, MAXIMUM_AMOUNT, HIERARCHY, CREATED_AT, UPDATED_AT FROM payment_method WHERE ID = :p0';
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
            $obj = new ChildPaymentMethod();
            $obj->hydrate($row);
            PaymentMethodTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildPaymentMethod|array|mixed the result, formatted by the current formatter
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
     * @return ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(PaymentMethodTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(PaymentMethodTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(PaymentMethodTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(PaymentMethodTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PaymentMethodTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the controller column
     *
     * Example usage:
     * <code>
     * $query->filterByController('fooValue');   // WHERE controller = 'fooValue'
     * $query->filterByController('%fooValue%'); // WHERE controller LIKE '%fooValue%'
     * </code>
     *
     * @param     string $controller The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function filterByController($controller = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($controller)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $controller)) {
                $controller = str_replace('*', '%', $controller);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PaymentMethodTableMap::COL_CONTROLLER, $controller, $comparison);
    }

    /**
     * Filter the query on the is_online column
     *
     * Example usage:
     * <code>
     * $query->filterByIsOnline(true); // WHERE is_online = true
     * $query->filterByIsOnline('yes'); // WHERE is_online = true
     * </code>
     *
     * @param     boolean|string $isOnline The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function filterByIsOnline($isOnline = null, $comparison = null)
    {
        if (is_string($isOnline)) {
            $is_online = in_array(strtolower($isOnline), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(PaymentMethodTableMap::COL_IS_ONLINE, $isOnline, $comparison);
    }

    /**
     * Filter the query on the is_active column
     *
     * Example usage:
     * <code>
     * $query->filterByIsActive(true); // WHERE is_active = true
     * $query->filterByIsActive('yes'); // WHERE is_active = true
     * </code>
     *
     * @param     boolean|string $isActive The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function filterByIsActive($isActive = null, $comparison = null)
    {
        if (is_string($isActive)) {
            $is_active = in_array(strtolower($isActive), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(PaymentMethodTableMap::COL_IS_ACTIVE, $isActive, $comparison);
    }

    /**
     * Filter the query on the maximum_amount column
     *
     * Example usage:
     * <code>
     * $query->filterByMaximumAmount(1234); // WHERE maximum_amount = 1234
     * $query->filterByMaximumAmount(array(12, 34)); // WHERE maximum_amount IN (12, 34)
     * $query->filterByMaximumAmount(array('min' => 12)); // WHERE maximum_amount > 12
     * </code>
     *
     * @param     mixed $maximumAmount The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function filterByMaximumAmount($maximumAmount = null, $comparison = null)
    {
        if (is_array($maximumAmount)) {
            $useMinMax = false;
            if (isset($maximumAmount['min'])) {
                $this->addUsingAlias(PaymentMethodTableMap::COL_MAXIMUM_AMOUNT, $maximumAmount['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($maximumAmount['max'])) {
                $this->addUsingAlias(PaymentMethodTableMap::COL_MAXIMUM_AMOUNT, $maximumAmount['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PaymentMethodTableMap::COL_MAXIMUM_AMOUNT, $maximumAmount, $comparison);
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
     * @return ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function filterByHierarchy($hierarchy = null, $comparison = null)
    {
        if (is_array($hierarchy)) {
            $useMinMax = false;
            if (isset($hierarchy['min'])) {
                $this->addUsingAlias(PaymentMethodTableMap::COL_HIERARCHY, $hierarchy['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($hierarchy['max'])) {
                $this->addUsingAlias(PaymentMethodTableMap::COL_HIERARCHY, $hierarchy['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PaymentMethodTableMap::COL_HIERARCHY, $hierarchy, $comparison);
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
     * @return ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(PaymentMethodTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(PaymentMethodTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PaymentMethodTableMap::COL_CREATED_AT, $createdAt, $comparison);
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
     * @return ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(PaymentMethodTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(PaymentMethodTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PaymentMethodTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodpaymentMethod object
     *
     * @param \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodpaymentMethod|ObjectCollection $dispatchMethodpaymentMethod  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function filterByDispatchMethodpaymentMethod($dispatchMethodpaymentMethod, $comparison = null)
    {
        if ($dispatchMethodpaymentMethod instanceof \Gekosale\Plugin\DispatchMethod\Model\ORM\DispatchMethodpaymentMethod) {
            return $this
                ->addUsingAlias(PaymentMethodTableMap::COL_ID, $dispatchMethodpaymentMethod->getPaymentMethodId(), $comparison);
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
     * @return ChildPaymentMethodQuery The current query, for fluid interface
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
     * Filter the query by a related \Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodShop object
     *
     * @param \Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodShop|ObjectCollection $paymentMethodShop  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function filterByPaymentMethodShop($paymentMethodShop, $comparison = null)
    {
        if ($paymentMethodShop instanceof \Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodShop) {
            return $this
                ->addUsingAlias(PaymentMethodTableMap::COL_ID, $paymentMethodShop->getPaymentMethodId(), $comparison);
        } elseif ($paymentMethodShop instanceof ObjectCollection) {
            return $this
                ->usePaymentMethodShopQuery()
                ->filterByPrimaryKeys($paymentMethodShop->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPaymentMethodShop() only accepts arguments of type \Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodShop or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PaymentMethodShop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function joinPaymentMethodShop($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PaymentMethodShop');

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
            $this->addJoinObject($join, 'PaymentMethodShop');
        }

        return $this;
    }

    /**
     * Use the PaymentMethodShop relation PaymentMethodShop object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodShopQuery A secondary query class using the current class as primary query
     */
    public function usePaymentMethodShopQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPaymentMethodShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PaymentMethodShop', '\Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodShopQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodI18n object
     *
     * @param \Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodI18n|ObjectCollection $paymentMethodI18n  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function filterByPaymentMethodI18n($paymentMethodI18n, $comparison = null)
    {
        if ($paymentMethodI18n instanceof \Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodI18n) {
            return $this
                ->addUsingAlias(PaymentMethodTableMap::COL_ID, $paymentMethodI18n->getId(), $comparison);
        } elseif ($paymentMethodI18n instanceof ObjectCollection) {
            return $this
                ->usePaymentMethodI18nQuery()
                ->filterByPrimaryKeys($paymentMethodI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPaymentMethodI18n() only accepts arguments of type \Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PaymentMethodI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function joinPaymentMethodI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PaymentMethodI18n');

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
            $this->addJoinObject($join, 'PaymentMethodI18n');
        }

        return $this;
    }

    /**
     * Use the PaymentMethodI18n relation PaymentMethodI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodI18nQuery A secondary query class using the current class as primary query
     */
    public function usePaymentMethodI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinPaymentMethodI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PaymentMethodI18n', '\Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildPaymentMethod $paymentMethod Object to remove from the list of results
     *
     * @return ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function prune($paymentMethod = null)
    {
        if ($paymentMethod) {
            $this->addUsingAlias(PaymentMethodTableMap::COL_ID, $paymentMethod->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the payment_method table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(PaymentMethodTableMap::DATABASE_NAME);
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
            PaymentMethodTableMap::clearInstancePool();
            PaymentMethodTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildPaymentMethod or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildPaymentMethod object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(PaymentMethodTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(PaymentMethodTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        PaymentMethodTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            PaymentMethodTableMap::clearRelatedInstancePool();
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
     * @return    ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'PaymentMethodI18n';
    
        return $this
            ->joinPaymentMethodI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }
    
    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('PaymentMethodI18n');
        $this->with['PaymentMethodI18n']->setIsWithOneToMany(false);
    
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
     * @return    ChildPaymentMethodI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PaymentMethodI18n', '\Gekosale\Plugin\PaymentMethod\Model\ORM\PaymentMethodI18nQuery');
    }

    // timestampable behavior
    
    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(PaymentMethodTableMap::COL_UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }
    
    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(PaymentMethodTableMap::COL_CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }
    
    /**
     * Order by update date desc
     *
     * @return     ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(PaymentMethodTableMap::COL_UPDATED_AT);
    }
    
    /**
     * Order by update date asc
     *
     * @return     ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(PaymentMethodTableMap::COL_UPDATED_AT);
    }
    
    /**
     * Order by create date desc
     *
     * @return     ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(PaymentMethodTableMap::COL_CREATED_AT);
    }
    
    /**
     * Order by create date asc
     *
     * @return     ChildPaymentMethodQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(PaymentMethodTableMap::COL_CREATED_AT);
    }

} // PaymentMethodQuery
