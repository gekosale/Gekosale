<?php

namespace Gekosale\Plugin\Client\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Client\Model\ORM\Client as ChildClient;
use Gekosale\Plugin\Client\Model\ORM\ClientQuery as ChildClientQuery;
use Gekosale\Plugin\Client\Model\ORM\Map\ClientTableMap;
use Gekosale\Plugin\MissingCart\Model\ORM\MissingCart;
use Gekosale\Plugin\Shop\Model\ORM\Shop;
use Gekosale\Plugin\Wishlist\Model\ORM\Wishlist;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'client' table.
 *
 * 
 *
 * @method     ChildClientQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildClientQuery orderByLogin($order = Criteria::ASC) Order by the login column
 * @method     ChildClientQuery orderByPassword($order = Criteria::ASC) Order by the password column
 * @method     ChildClientQuery orderByIsDisabled($order = Criteria::ASC) Order by the is_disabled column
 * @method     ChildClientQuery orderByShopId($order = Criteria::ASC) Order by the shop_id column
 * @method     ChildClientQuery orderByActiveLink($order = Criteria::ASC) Order by the active_link column
 * @method     ChildClientQuery orderByClientType($order = Criteria::ASC) Order by the client_type column
 * @method     ChildClientQuery orderByAutoAssign($order = Criteria::ASC) Order by the auto_assign column
 *
 * @method     ChildClientQuery groupById() Group by the id column
 * @method     ChildClientQuery groupByLogin() Group by the login column
 * @method     ChildClientQuery groupByPassword() Group by the password column
 * @method     ChildClientQuery groupByIsDisabled() Group by the is_disabled column
 * @method     ChildClientQuery groupByShopId() Group by the shop_id column
 * @method     ChildClientQuery groupByActiveLink() Group by the active_link column
 * @method     ChildClientQuery groupByClientType() Group by the client_type column
 * @method     ChildClientQuery groupByAutoAssign() Group by the auto_assign column
 *
 * @method     ChildClientQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildClientQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildClientQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildClientQuery leftJoinShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the Shop relation
 * @method     ChildClientQuery rightJoinShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Shop relation
 * @method     ChildClientQuery innerJoinShop($relationAlias = null) Adds a INNER JOIN clause to the query using the Shop relation
 *
 * @method     ChildClientQuery leftJoinClientAddress($relationAlias = null) Adds a LEFT JOIN clause to the query using the ClientAddress relation
 * @method     ChildClientQuery rightJoinClientAddress($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ClientAddress relation
 * @method     ChildClientQuery innerJoinClientAddress($relationAlias = null) Adds a INNER JOIN clause to the query using the ClientAddress relation
 *
 * @method     ChildClientQuery leftJoinClientData($relationAlias = null) Adds a LEFT JOIN clause to the query using the ClientData relation
 * @method     ChildClientQuery rightJoinClientData($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ClientData relation
 * @method     ChildClientQuery innerJoinClientData($relationAlias = null) Adds a INNER JOIN clause to the query using the ClientData relation
 *
 * @method     ChildClientQuery leftJoinMissingCart($relationAlias = null) Adds a LEFT JOIN clause to the query using the MissingCart relation
 * @method     ChildClientQuery rightJoinMissingCart($relationAlias = null) Adds a RIGHT JOIN clause to the query using the MissingCart relation
 * @method     ChildClientQuery innerJoinMissingCart($relationAlias = null) Adds a INNER JOIN clause to the query using the MissingCart relation
 *
 * @method     ChildClientQuery leftJoinWishlist($relationAlias = null) Adds a LEFT JOIN clause to the query using the Wishlist relation
 * @method     ChildClientQuery rightJoinWishlist($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Wishlist relation
 * @method     ChildClientQuery innerJoinWishlist($relationAlias = null) Adds a INNER JOIN clause to the query using the Wishlist relation
 *
 * @method     ChildClient findOne(ConnectionInterface $con = null) Return the first ChildClient matching the query
 * @method     ChildClient findOneOrCreate(ConnectionInterface $con = null) Return the first ChildClient matching the query, or a new ChildClient object populated from the query conditions when no match is found
 *
 * @method     ChildClient findOneById(int $id) Return the first ChildClient filtered by the id column
 * @method     ChildClient findOneByLogin(string $login) Return the first ChildClient filtered by the login column
 * @method     ChildClient findOneByPassword(string $password) Return the first ChildClient filtered by the password column
 * @method     ChildClient findOneByIsDisabled(int $is_disabled) Return the first ChildClient filtered by the is_disabled column
 * @method     ChildClient findOneByShopId(int $shop_id) Return the first ChildClient filtered by the shop_id column
 * @method     ChildClient findOneByActiveLink(string $active_link) Return the first ChildClient filtered by the active_link column
 * @method     ChildClient findOneByClientType(int $client_type) Return the first ChildClient filtered by the client_type column
 * @method     ChildClient findOneByAutoAssign(int $auto_assign) Return the first ChildClient filtered by the auto_assign column
 *
 * @method     array findById(int $id) Return ChildClient objects filtered by the id column
 * @method     array findByLogin(string $login) Return ChildClient objects filtered by the login column
 * @method     array findByPassword(string $password) Return ChildClient objects filtered by the password column
 * @method     array findByIsDisabled(int $is_disabled) Return ChildClient objects filtered by the is_disabled column
 * @method     array findByShopId(int $shop_id) Return ChildClient objects filtered by the shop_id column
 * @method     array findByActiveLink(string $active_link) Return ChildClient objects filtered by the active_link column
 * @method     array findByClientType(int $client_type) Return ChildClient objects filtered by the client_type column
 * @method     array findByAutoAssign(int $auto_assign) Return ChildClient objects filtered by the auto_assign column
 *
 */
abstract class ClientQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Client\Model\ORM\Base\ClientQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Client\\Model\\ORM\\Client', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildClientQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildClientQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Client\Model\ORM\ClientQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Client\Model\ORM\ClientQuery();
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
     * @return ChildClient|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ClientTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ClientTableMap::DATABASE_NAME);
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
     * @return   ChildClient A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, LOGIN, PASSWORD, IS_DISABLED, SHOP_ID, ACTIVE_LINK, CLIENT_TYPE, AUTO_ASSIGN FROM client WHERE ID = :p0';
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
            $obj = new ChildClient();
            $obj->hydrate($row);
            ClientTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildClient|array|mixed the result, formatted by the current formatter
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
     * @return ChildClientQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ClientTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildClientQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ClientTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildClientQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ClientTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ClientTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClientTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the login column
     *
     * Example usage:
     * <code>
     * $query->filterByLogin('fooValue');   // WHERE login = 'fooValue'
     * $query->filterByLogin('%fooValue%'); // WHERE login LIKE '%fooValue%'
     * </code>
     *
     * @param     string $login The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientQuery The current query, for fluid interface
     */
    public function filterByLogin($login = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($login)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $login)) {
                $login = str_replace('*', '%', $login);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ClientTableMap::COL_LOGIN, $login, $comparison);
    }

    /**
     * Filter the query on the password column
     *
     * Example usage:
     * <code>
     * $query->filterByPassword('fooValue');   // WHERE password = 'fooValue'
     * $query->filterByPassword('%fooValue%'); // WHERE password LIKE '%fooValue%'
     * </code>
     *
     * @param     string $password The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientQuery The current query, for fluid interface
     */
    public function filterByPassword($password = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($password)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $password)) {
                $password = str_replace('*', '%', $password);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ClientTableMap::COL_PASSWORD, $password, $comparison);
    }

    /**
     * Filter the query on the is_disabled column
     *
     * Example usage:
     * <code>
     * $query->filterByIsDisabled(1234); // WHERE is_disabled = 1234
     * $query->filterByIsDisabled(array(12, 34)); // WHERE is_disabled IN (12, 34)
     * $query->filterByIsDisabled(array('min' => 12)); // WHERE is_disabled > 12
     * </code>
     *
     * @param     mixed $isDisabled The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientQuery The current query, for fluid interface
     */
    public function filterByIsDisabled($isDisabled = null, $comparison = null)
    {
        if (is_array($isDisabled)) {
            $useMinMax = false;
            if (isset($isDisabled['min'])) {
                $this->addUsingAlias(ClientTableMap::COL_IS_DISABLED, $isDisabled['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($isDisabled['max'])) {
                $this->addUsingAlias(ClientTableMap::COL_IS_DISABLED, $isDisabled['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClientTableMap::COL_IS_DISABLED, $isDisabled, $comparison);
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
     * @return ChildClientQuery The current query, for fluid interface
     */
    public function filterByShopId($shopId = null, $comparison = null)
    {
        if (is_array($shopId)) {
            $useMinMax = false;
            if (isset($shopId['min'])) {
                $this->addUsingAlias(ClientTableMap::COL_SHOP_ID, $shopId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($shopId['max'])) {
                $this->addUsingAlias(ClientTableMap::COL_SHOP_ID, $shopId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClientTableMap::COL_SHOP_ID, $shopId, $comparison);
    }

    /**
     * Filter the query on the active_link column
     *
     * Example usage:
     * <code>
     * $query->filterByActiveLink('fooValue');   // WHERE active_link = 'fooValue'
     * $query->filterByActiveLink('%fooValue%'); // WHERE active_link LIKE '%fooValue%'
     * </code>
     *
     * @param     string $activeLink The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientQuery The current query, for fluid interface
     */
    public function filterByActiveLink($activeLink = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($activeLink)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $activeLink)) {
                $activeLink = str_replace('*', '%', $activeLink);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ClientTableMap::COL_ACTIVE_LINK, $activeLink, $comparison);
    }

    /**
     * Filter the query on the client_type column
     *
     * Example usage:
     * <code>
     * $query->filterByClientType(1234); // WHERE client_type = 1234
     * $query->filterByClientType(array(12, 34)); // WHERE client_type IN (12, 34)
     * $query->filterByClientType(array('min' => 12)); // WHERE client_type > 12
     * </code>
     *
     * @param     mixed $clientType The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientQuery The current query, for fluid interface
     */
    public function filterByClientType($clientType = null, $comparison = null)
    {
        if (is_array($clientType)) {
            $useMinMax = false;
            if (isset($clientType['min'])) {
                $this->addUsingAlias(ClientTableMap::COL_CLIENT_TYPE, $clientType['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($clientType['max'])) {
                $this->addUsingAlias(ClientTableMap::COL_CLIENT_TYPE, $clientType['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClientTableMap::COL_CLIENT_TYPE, $clientType, $comparison);
    }

    /**
     * Filter the query on the auto_assign column
     *
     * Example usage:
     * <code>
     * $query->filterByAutoAssign(1234); // WHERE auto_assign = 1234
     * $query->filterByAutoAssign(array(12, 34)); // WHERE auto_assign IN (12, 34)
     * $query->filterByAutoAssign(array('min' => 12)); // WHERE auto_assign > 12
     * </code>
     *
     * @param     mixed $autoAssign The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientQuery The current query, for fluid interface
     */
    public function filterByAutoAssign($autoAssign = null, $comparison = null)
    {
        if (is_array($autoAssign)) {
            $useMinMax = false;
            if (isset($autoAssign['min'])) {
                $this->addUsingAlias(ClientTableMap::COL_AUTO_ASSIGN, $autoAssign['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($autoAssign['max'])) {
                $this->addUsingAlias(ClientTableMap::COL_AUTO_ASSIGN, $autoAssign['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClientTableMap::COL_AUTO_ASSIGN, $autoAssign, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Shop\Model\ORM\Shop object
     *
     * @param \Gekosale\Plugin\Shop\Model\ORM\Shop|ObjectCollection $shop The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientQuery The current query, for fluid interface
     */
    public function filterByShop($shop, $comparison = null)
    {
        if ($shop instanceof \Gekosale\Plugin\Shop\Model\ORM\Shop) {
            return $this
                ->addUsingAlias(ClientTableMap::COL_SHOP_ID, $shop->getId(), $comparison);
        } elseif ($shop instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ClientTableMap::COL_SHOP_ID, $shop->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildClientQuery The current query, for fluid interface
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
     * Filter the query by a related \Gekosale\Plugin\Client\Model\ORM\ClientAddress object
     *
     * @param \Gekosale\Plugin\Client\Model\ORM\ClientAddress|ObjectCollection $clientAddress  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientQuery The current query, for fluid interface
     */
    public function filterByClientAddress($clientAddress, $comparison = null)
    {
        if ($clientAddress instanceof \Gekosale\Plugin\Client\Model\ORM\ClientAddress) {
            return $this
                ->addUsingAlias(ClientTableMap::COL_ID, $clientAddress->getClientId(), $comparison);
        } elseif ($clientAddress instanceof ObjectCollection) {
            return $this
                ->useClientAddressQuery()
                ->filterByPrimaryKeys($clientAddress->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByClientAddress() only accepts arguments of type \Gekosale\Plugin\Client\Model\ORM\ClientAddress or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ClientAddress relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildClientQuery The current query, for fluid interface
     */
    public function joinClientAddress($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ClientAddress');

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
            $this->addJoinObject($join, 'ClientAddress');
        }

        return $this;
    }

    /**
     * Use the ClientAddress relation ClientAddress object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Client\Model\ORM\ClientAddressQuery A secondary query class using the current class as primary query
     */
    public function useClientAddressQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinClientAddress($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ClientAddress', '\Gekosale\Plugin\Client\Model\ORM\ClientAddressQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Client\Model\ORM\ClientData object
     *
     * @param \Gekosale\Plugin\Client\Model\ORM\ClientData|ObjectCollection $clientData  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientQuery The current query, for fluid interface
     */
    public function filterByClientData($clientData, $comparison = null)
    {
        if ($clientData instanceof \Gekosale\Plugin\Client\Model\ORM\ClientData) {
            return $this
                ->addUsingAlias(ClientTableMap::COL_ID, $clientData->getClientId(), $comparison);
        } elseif ($clientData instanceof ObjectCollection) {
            return $this
                ->useClientDataQuery()
                ->filterByPrimaryKeys($clientData->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByClientData() only accepts arguments of type \Gekosale\Plugin\Client\Model\ORM\ClientData or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ClientData relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildClientQuery The current query, for fluid interface
     */
    public function joinClientData($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ClientData');

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
            $this->addJoinObject($join, 'ClientData');
        }

        return $this;
    }

    /**
     * Use the ClientData relation ClientData object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Client\Model\ORM\ClientDataQuery A secondary query class using the current class as primary query
     */
    public function useClientDataQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinClientData($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ClientData', '\Gekosale\Plugin\Client\Model\ORM\ClientDataQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\MissingCart\Model\ORM\MissingCart object
     *
     * @param \Gekosale\Plugin\MissingCart\Model\ORM\MissingCart|ObjectCollection $missingCart  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientQuery The current query, for fluid interface
     */
    public function filterByMissingCart($missingCart, $comparison = null)
    {
        if ($missingCart instanceof \Gekosale\Plugin\MissingCart\Model\ORM\MissingCart) {
            return $this
                ->addUsingAlias(ClientTableMap::COL_ID, $missingCart->getClientId(), $comparison);
        } elseif ($missingCart instanceof ObjectCollection) {
            return $this
                ->useMissingCartQuery()
                ->filterByPrimaryKeys($missingCart->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByMissingCart() only accepts arguments of type \Gekosale\Plugin\MissingCart\Model\ORM\MissingCart or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the MissingCart relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildClientQuery The current query, for fluid interface
     */
    public function joinMissingCart($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('MissingCart');

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
            $this->addJoinObject($join, 'MissingCart');
        }

        return $this;
    }

    /**
     * Use the MissingCart relation MissingCart object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\MissingCart\Model\ORM\MissingCartQuery A secondary query class using the current class as primary query
     */
    public function useMissingCartQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinMissingCart($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'MissingCart', '\Gekosale\Plugin\MissingCart\Model\ORM\MissingCartQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Wishlist\Model\ORM\Wishlist object
     *
     * @param \Gekosale\Plugin\Wishlist\Model\ORM\Wishlist|ObjectCollection $wishlist  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientQuery The current query, for fluid interface
     */
    public function filterByWishlist($wishlist, $comparison = null)
    {
        if ($wishlist instanceof \Gekosale\Plugin\Wishlist\Model\ORM\Wishlist) {
            return $this
                ->addUsingAlias(ClientTableMap::COL_ID, $wishlist->getClientId(), $comparison);
        } elseif ($wishlist instanceof ObjectCollection) {
            return $this
                ->useWishlistQuery()
                ->filterByPrimaryKeys($wishlist->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByWishlist() only accepts arguments of type \Gekosale\Plugin\Wishlist\Model\ORM\Wishlist or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Wishlist relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildClientQuery The current query, for fluid interface
     */
    public function joinWishlist($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Wishlist');

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
            $this->addJoinObject($join, 'Wishlist');
        }

        return $this;
    }

    /**
     * Use the Wishlist relation Wishlist object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Wishlist\Model\ORM\WishlistQuery A secondary query class using the current class as primary query
     */
    public function useWishlistQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinWishlist($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Wishlist', '\Gekosale\Plugin\Wishlist\Model\ORM\WishlistQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildClient $client Object to remove from the list of results
     *
     * @return ChildClientQuery The current query, for fluid interface
     */
    public function prune($client = null)
    {
        if ($client) {
            $this->addUsingAlias(ClientTableMap::COL_ID, $client->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the client table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ClientTableMap::DATABASE_NAME);
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
            ClientTableMap::clearInstancePool();
            ClientTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildClient or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildClient object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(ClientTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ClientTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        ClientTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            ClientTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // ClientQuery
