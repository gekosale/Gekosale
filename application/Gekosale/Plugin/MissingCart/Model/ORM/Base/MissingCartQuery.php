<?php

namespace Gekosale\Plugin\MissingCart\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Client\Model\ORM\Client;
use Gekosale\Plugin\MissingCart\Model\ORM\MissingCart as ChildMissingCart;
use Gekosale\Plugin\MissingCart\Model\ORM\MissingCartQuery as ChildMissingCartQuery;
use Gekosale\Plugin\MissingCart\Model\ORM\Map\MissingCartTableMap;
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
 * Base class that represents a query for the 'missing_cart' table.
 *
 * 
 *
 * @method     ChildMissingCartQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildMissingCartQuery orderByClientId($order = Criteria::ASC) Order by the client_id column
 * @method     ChildMissingCartQuery orderByClientMail($order = Criteria::ASC) Order by the client_mail column
 * @method     ChildMissingCartQuery orderBySessionId($order = Criteria::ASC) Order by the session_id column
 * @method     ChildMissingCartQuery orderByShopId($order = Criteria::ASC) Order by the shop_id column
 *
 * @method     ChildMissingCartQuery groupById() Group by the id column
 * @method     ChildMissingCartQuery groupByClientId() Group by the client_id column
 * @method     ChildMissingCartQuery groupByClientMail() Group by the client_mail column
 * @method     ChildMissingCartQuery groupBySessionId() Group by the session_id column
 * @method     ChildMissingCartQuery groupByShopId() Group by the shop_id column
 *
 * @method     ChildMissingCartQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildMissingCartQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildMissingCartQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildMissingCartQuery leftJoinClient($relationAlias = null) Adds a LEFT JOIN clause to the query using the Client relation
 * @method     ChildMissingCartQuery rightJoinClient($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Client relation
 * @method     ChildMissingCartQuery innerJoinClient($relationAlias = null) Adds a INNER JOIN clause to the query using the Client relation
 *
 * @method     ChildMissingCartQuery leftJoinShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the Shop relation
 * @method     ChildMissingCartQuery rightJoinShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Shop relation
 * @method     ChildMissingCartQuery innerJoinShop($relationAlias = null) Adds a INNER JOIN clause to the query using the Shop relation
 *
 * @method     ChildMissingCartQuery leftJoinMissingCartProduct($relationAlias = null) Adds a LEFT JOIN clause to the query using the MissingCartProduct relation
 * @method     ChildMissingCartQuery rightJoinMissingCartProduct($relationAlias = null) Adds a RIGHT JOIN clause to the query using the MissingCartProduct relation
 * @method     ChildMissingCartQuery innerJoinMissingCartProduct($relationAlias = null) Adds a INNER JOIN clause to the query using the MissingCartProduct relation
 *
 * @method     ChildMissingCart findOne(ConnectionInterface $con = null) Return the first ChildMissingCart matching the query
 * @method     ChildMissingCart findOneOrCreate(ConnectionInterface $con = null) Return the first ChildMissingCart matching the query, or a new ChildMissingCart object populated from the query conditions when no match is found
 *
 * @method     ChildMissingCart findOneById(int $id) Return the first ChildMissingCart filtered by the id column
 * @method     ChildMissingCart findOneByClientId(int $client_id) Return the first ChildMissingCart filtered by the client_id column
 * @method     ChildMissingCart findOneByClientMail(string $client_mail) Return the first ChildMissingCart filtered by the client_mail column
 * @method     ChildMissingCart findOneBySessionId(string $session_id) Return the first ChildMissingCart filtered by the session_id column
 * @method     ChildMissingCart findOneByShopId(int $shop_id) Return the first ChildMissingCart filtered by the shop_id column
 *
 * @method     array findById(int $id) Return ChildMissingCart objects filtered by the id column
 * @method     array findByClientId(int $client_id) Return ChildMissingCart objects filtered by the client_id column
 * @method     array findByClientMail(string $client_mail) Return ChildMissingCart objects filtered by the client_mail column
 * @method     array findBySessionId(string $session_id) Return ChildMissingCart objects filtered by the session_id column
 * @method     array findByShopId(int $shop_id) Return ChildMissingCart objects filtered by the shop_id column
 *
 */
abstract class MissingCartQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\MissingCart\Model\ORM\Base\MissingCartQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\MissingCart\\Model\\ORM\\MissingCart', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildMissingCartQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildMissingCartQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartQuery();
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
     * @return ChildMissingCart|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = MissingCartTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(MissingCartTableMap::DATABASE_NAME);
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
     * @return   ChildMissingCart A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, CLIENT_ID, CLIENT_MAIL, SESSION_ID, SHOP_ID FROM missing_cart WHERE ID = :p0';
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
            $obj = new ChildMissingCart();
            $obj->hydrate($row);
            MissingCartTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildMissingCart|array|mixed the result, formatted by the current formatter
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
     * @return ChildMissingCartQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(MissingCartTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildMissingCartQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(MissingCartTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildMissingCartQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(MissingCartTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(MissingCartTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MissingCartTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the client_id column
     *
     * Example usage:
     * <code>
     * $query->filterByClientId(1234); // WHERE client_id = 1234
     * $query->filterByClientId(array(12, 34)); // WHERE client_id IN (12, 34)
     * $query->filterByClientId(array('min' => 12)); // WHERE client_id > 12
     * </code>
     *
     * @see       filterByClient()
     *
     * @param     mixed $clientId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildMissingCartQuery The current query, for fluid interface
     */
    public function filterByClientId($clientId = null, $comparison = null)
    {
        if (is_array($clientId)) {
            $useMinMax = false;
            if (isset($clientId['min'])) {
                $this->addUsingAlias(MissingCartTableMap::COL_CLIENT_ID, $clientId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($clientId['max'])) {
                $this->addUsingAlias(MissingCartTableMap::COL_CLIENT_ID, $clientId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MissingCartTableMap::COL_CLIENT_ID, $clientId, $comparison);
    }

    /**
     * Filter the query on the client_mail column
     *
     * Example usage:
     * <code>
     * $query->filterByClientMail('fooValue');   // WHERE client_mail = 'fooValue'
     * $query->filterByClientMail('%fooValue%'); // WHERE client_mail LIKE '%fooValue%'
     * </code>
     *
     * @param     string $clientMail The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildMissingCartQuery The current query, for fluid interface
     */
    public function filterByClientMail($clientMail = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($clientMail)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $clientMail)) {
                $clientMail = str_replace('*', '%', $clientMail);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(MissingCartTableMap::COL_CLIENT_MAIL, $clientMail, $comparison);
    }

    /**
     * Filter the query on the session_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySessionId('fooValue');   // WHERE session_id = 'fooValue'
     * $query->filterBySessionId('%fooValue%'); // WHERE session_id LIKE '%fooValue%'
     * </code>
     *
     * @param     string $sessionId The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildMissingCartQuery The current query, for fluid interface
     */
    public function filterBySessionId($sessionId = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($sessionId)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $sessionId)) {
                $sessionId = str_replace('*', '%', $sessionId);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(MissingCartTableMap::COL_SESSION_ID, $sessionId, $comparison);
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
     * @return ChildMissingCartQuery The current query, for fluid interface
     */
    public function filterByShopId($shopId = null, $comparison = null)
    {
        if (is_array($shopId)) {
            $useMinMax = false;
            if (isset($shopId['min'])) {
                $this->addUsingAlias(MissingCartTableMap::COL_SHOP_ID, $shopId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($shopId['max'])) {
                $this->addUsingAlias(MissingCartTableMap::COL_SHOP_ID, $shopId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MissingCartTableMap::COL_SHOP_ID, $shopId, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Client\Model\ORM\Client object
     *
     * @param \Gekosale\Plugin\Client\Model\ORM\Client|ObjectCollection $client The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildMissingCartQuery The current query, for fluid interface
     */
    public function filterByClient($client, $comparison = null)
    {
        if ($client instanceof \Gekosale\Plugin\Client\Model\ORM\Client) {
            return $this
                ->addUsingAlias(MissingCartTableMap::COL_CLIENT_ID, $client->getId(), $comparison);
        } elseif ($client instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(MissingCartTableMap::COL_CLIENT_ID, $client->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByClient() only accepts arguments of type \Gekosale\Plugin\Client\Model\ORM\Client or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Client relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildMissingCartQuery The current query, for fluid interface
     */
    public function joinClient($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Client');

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
            $this->addJoinObject($join, 'Client');
        }

        return $this;
    }

    /**
     * Use the Client relation Client object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Client\Model\ORM\ClientQuery A secondary query class using the current class as primary query
     */
    public function useClientQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinClient($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Client', '\Gekosale\Plugin\Client\Model\ORM\ClientQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Shop\Model\ORM\Shop object
     *
     * @param \Gekosale\Plugin\Shop\Model\ORM\Shop|ObjectCollection $shop The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildMissingCartQuery The current query, for fluid interface
     */
    public function filterByShop($shop, $comparison = null)
    {
        if ($shop instanceof \Gekosale\Plugin\Shop\Model\ORM\Shop) {
            return $this
                ->addUsingAlias(MissingCartTableMap::COL_SHOP_ID, $shop->getId(), $comparison);
        } elseif ($shop instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(MissingCartTableMap::COL_SHOP_ID, $shop->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildMissingCartQuery The current query, for fluid interface
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
     * Filter the query by a related \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProduct object
     *
     * @param \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProduct|ObjectCollection $missingCartProduct  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildMissingCartQuery The current query, for fluid interface
     */
    public function filterByMissingCartProduct($missingCartProduct, $comparison = null)
    {
        if ($missingCartProduct instanceof \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProduct) {
            return $this
                ->addUsingAlias(MissingCartTableMap::COL_ID, $missingCartProduct->getMissingCartId(), $comparison);
        } elseif ($missingCartProduct instanceof ObjectCollection) {
            return $this
                ->useMissingCartProductQuery()
                ->filterByPrimaryKeys($missingCartProduct->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByMissingCartProduct() only accepts arguments of type \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProduct or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the MissingCartProduct relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildMissingCartQuery The current query, for fluid interface
     */
    public function joinMissingCartProduct($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('MissingCartProduct');

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
            $this->addJoinObject($join, 'MissingCartProduct');
        }

        return $this;
    }

    /**
     * Use the MissingCartProduct relation MissingCartProduct object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProductQuery A secondary query class using the current class as primary query
     */
    public function useMissingCartProductQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinMissingCartProduct($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'MissingCartProduct', '\Gekosale\Plugin\MissingCart\Model\ORM\MissingCartProductQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildMissingCart $missingCart Object to remove from the list of results
     *
     * @return ChildMissingCartQuery The current query, for fluid interface
     */
    public function prune($missingCart = null)
    {
        if ($missingCart) {
            $this->addUsingAlias(MissingCartTableMap::COL_ID, $missingCart->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the missing_cart table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(MissingCartTableMap::DATABASE_NAME);
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
            MissingCartTableMap::clearInstancePool();
            MissingCartTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildMissingCart or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildMissingCart object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(MissingCartTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(MissingCartTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        MissingCartTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            MissingCartTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // MissingCartQuery
