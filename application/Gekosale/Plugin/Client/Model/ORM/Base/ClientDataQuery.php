<?php

namespace Gekosale\Plugin\Client\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroup;
use Gekosale\Plugin\Client\Model\ORM\ClientData as ChildClientData;
use Gekosale\Plugin\Client\Model\ORM\ClientDataQuery as ChildClientDataQuery;
use Gekosale\Plugin\Client\Model\ORM\Map\ClientDataTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'client_data' table.
 *
 * 
 *
 * @method     ChildClientDataQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildClientDataQuery orderByFirstname($order = Criteria::ASC) Order by the firstname column
 * @method     ChildClientDataQuery orderBySurname($order = Criteria::ASC) Order by the surname column
 * @method     ChildClientDataQuery orderByEmail($order = Criteria::ASC) Order by the email column
 * @method     ChildClientDataQuery orderByDescription($order = Criteria::ASC) Order by the description column
 * @method     ChildClientDataQuery orderByPhone($order = Criteria::ASC) Order by the phone column
 * @method     ChildClientDataQuery orderByPhone2($order = Criteria::ASC) Order by the phone2 column
 * @method     ChildClientDataQuery orderByClientGroupId($order = Criteria::ASC) Order by the client_group_id column
 * @method     ChildClientDataQuery orderByClientId($order = Criteria::ASC) Order by the client_id column
 * @method     ChildClientDataQuery orderByLastLogged($order = Criteria::ASC) Order by the last_logged column
 *
 * @method     ChildClientDataQuery groupById() Group by the id column
 * @method     ChildClientDataQuery groupByFirstname() Group by the firstname column
 * @method     ChildClientDataQuery groupBySurname() Group by the surname column
 * @method     ChildClientDataQuery groupByEmail() Group by the email column
 * @method     ChildClientDataQuery groupByDescription() Group by the description column
 * @method     ChildClientDataQuery groupByPhone() Group by the phone column
 * @method     ChildClientDataQuery groupByPhone2() Group by the phone2 column
 * @method     ChildClientDataQuery groupByClientGroupId() Group by the client_group_id column
 * @method     ChildClientDataQuery groupByClientId() Group by the client_id column
 * @method     ChildClientDataQuery groupByLastLogged() Group by the last_logged column
 *
 * @method     ChildClientDataQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildClientDataQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildClientDataQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildClientDataQuery leftJoinClientGroup($relationAlias = null) Adds a LEFT JOIN clause to the query using the ClientGroup relation
 * @method     ChildClientDataQuery rightJoinClientGroup($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ClientGroup relation
 * @method     ChildClientDataQuery innerJoinClientGroup($relationAlias = null) Adds a INNER JOIN clause to the query using the ClientGroup relation
 *
 * @method     ChildClientDataQuery leftJoinClient($relationAlias = null) Adds a LEFT JOIN clause to the query using the Client relation
 * @method     ChildClientDataQuery rightJoinClient($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Client relation
 * @method     ChildClientDataQuery innerJoinClient($relationAlias = null) Adds a INNER JOIN clause to the query using the Client relation
 *
 * @method     ChildClientData findOne(ConnectionInterface $con = null) Return the first ChildClientData matching the query
 * @method     ChildClientData findOneOrCreate(ConnectionInterface $con = null) Return the first ChildClientData matching the query, or a new ChildClientData object populated from the query conditions when no match is found
 *
 * @method     ChildClientData findOneById(int $id) Return the first ChildClientData filtered by the id column
 * @method     ChildClientData findOneByFirstname(resource $firstname) Return the first ChildClientData filtered by the firstname column
 * @method     ChildClientData findOneBySurname(resource $surname) Return the first ChildClientData filtered by the surname column
 * @method     ChildClientData findOneByEmail(resource $email) Return the first ChildClientData filtered by the email column
 * @method     ChildClientData findOneByDescription(resource $description) Return the first ChildClientData filtered by the description column
 * @method     ChildClientData findOneByPhone(resource $phone) Return the first ChildClientData filtered by the phone column
 * @method     ChildClientData findOneByPhone2(resource $phone2) Return the first ChildClientData filtered by the phone2 column
 * @method     ChildClientData findOneByClientGroupId(int $client_group_id) Return the first ChildClientData filtered by the client_group_id column
 * @method     ChildClientData findOneByClientId(int $client_id) Return the first ChildClientData filtered by the client_id column
 * @method     ChildClientData findOneByLastLogged(string $last_logged) Return the first ChildClientData filtered by the last_logged column
 *
 * @method     array findById(int $id) Return ChildClientData objects filtered by the id column
 * @method     array findByFirstname(resource $firstname) Return ChildClientData objects filtered by the firstname column
 * @method     array findBySurname(resource $surname) Return ChildClientData objects filtered by the surname column
 * @method     array findByEmail(resource $email) Return ChildClientData objects filtered by the email column
 * @method     array findByDescription(resource $description) Return ChildClientData objects filtered by the description column
 * @method     array findByPhone(resource $phone) Return ChildClientData objects filtered by the phone column
 * @method     array findByPhone2(resource $phone2) Return ChildClientData objects filtered by the phone2 column
 * @method     array findByClientGroupId(int $client_group_id) Return ChildClientData objects filtered by the client_group_id column
 * @method     array findByClientId(int $client_id) Return ChildClientData objects filtered by the client_id column
 * @method     array findByLastLogged(string $last_logged) Return ChildClientData objects filtered by the last_logged column
 *
 */
abstract class ClientDataQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Client\Model\ORM\Base\ClientDataQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Client\\Model\\ORM\\ClientData', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildClientDataQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildClientDataQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Client\Model\ORM\ClientDataQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Client\Model\ORM\ClientDataQuery();
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
     * @return ChildClientData|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ClientDataTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ClientDataTableMap::DATABASE_NAME);
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
     * @return   ChildClientData A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, FIRSTNAME, SURNAME, EMAIL, DESCRIPTION, PHONE, PHONE2, CLIENT_GROUP_ID, CLIENT_ID, LAST_LOGGED FROM client_data WHERE ID = :p0';
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
            $obj = new ChildClientData();
            $obj->hydrate($row);
            ClientDataTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildClientData|array|mixed the result, formatted by the current formatter
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
     * @return ChildClientDataQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ClientDataTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildClientDataQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ClientDataTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildClientDataQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ClientDataTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ClientDataTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClientDataTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the firstname column
     *
     * @param     mixed $firstname The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientDataQuery The current query, for fluid interface
     */
    public function filterByFirstname($firstname = null, $comparison = null)
    {

        return $this->addUsingAlias(ClientDataTableMap::COL_FIRSTNAME, $firstname, $comparison);
    }

    /**
     * Filter the query on the surname column
     *
     * @param     mixed $surname The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientDataQuery The current query, for fluid interface
     */
    public function filterBySurname($surname = null, $comparison = null)
    {

        return $this->addUsingAlias(ClientDataTableMap::COL_SURNAME, $surname, $comparison);
    }

    /**
     * Filter the query on the email column
     *
     * @param     mixed $email The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientDataQuery The current query, for fluid interface
     */
    public function filterByEmail($email = null, $comparison = null)
    {

        return $this->addUsingAlias(ClientDataTableMap::COL_EMAIL, $email, $comparison);
    }

    /**
     * Filter the query on the description column
     *
     * @param     mixed $description The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientDataQuery The current query, for fluid interface
     */
    public function filterByDescription($description = null, $comparison = null)
    {

        return $this->addUsingAlias(ClientDataTableMap::COL_DESCRIPTION, $description, $comparison);
    }

    /**
     * Filter the query on the phone column
     *
     * @param     mixed $phone The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientDataQuery The current query, for fluid interface
     */
    public function filterByPhone($phone = null, $comparison = null)
    {

        return $this->addUsingAlias(ClientDataTableMap::COL_PHONE, $phone, $comparison);
    }

    /**
     * Filter the query on the phone2 column
     *
     * @param     mixed $phone2 The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientDataQuery The current query, for fluid interface
     */
    public function filterByPhone2($phone2 = null, $comparison = null)
    {

        return $this->addUsingAlias(ClientDataTableMap::COL_PHONE2, $phone2, $comparison);
    }

    /**
     * Filter the query on the client_group_id column
     *
     * Example usage:
     * <code>
     * $query->filterByClientGroupId(1234); // WHERE client_group_id = 1234
     * $query->filterByClientGroupId(array(12, 34)); // WHERE client_group_id IN (12, 34)
     * $query->filterByClientGroupId(array('min' => 12)); // WHERE client_group_id > 12
     * </code>
     *
     * @see       filterByClientGroup()
     *
     * @param     mixed $clientGroupId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientDataQuery The current query, for fluid interface
     */
    public function filterByClientGroupId($clientGroupId = null, $comparison = null)
    {
        if (is_array($clientGroupId)) {
            $useMinMax = false;
            if (isset($clientGroupId['min'])) {
                $this->addUsingAlias(ClientDataTableMap::COL_CLIENT_GROUP_ID, $clientGroupId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($clientGroupId['max'])) {
                $this->addUsingAlias(ClientDataTableMap::COL_CLIENT_GROUP_ID, $clientGroupId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClientDataTableMap::COL_CLIENT_GROUP_ID, $clientGroupId, $comparison);
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
     * @return ChildClientDataQuery The current query, for fluid interface
     */
    public function filterByClientId($clientId = null, $comparison = null)
    {
        if (is_array($clientId)) {
            $useMinMax = false;
            if (isset($clientId['min'])) {
                $this->addUsingAlias(ClientDataTableMap::COL_CLIENT_ID, $clientId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($clientId['max'])) {
                $this->addUsingAlias(ClientDataTableMap::COL_CLIENT_ID, $clientId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClientDataTableMap::COL_CLIENT_ID, $clientId, $comparison);
    }

    /**
     * Filter the query on the last_logged column
     *
     * Example usage:
     * <code>
     * $query->filterByLastLogged('2011-03-14'); // WHERE last_logged = '2011-03-14'
     * $query->filterByLastLogged('now'); // WHERE last_logged = '2011-03-14'
     * $query->filterByLastLogged(array('max' => 'yesterday')); // WHERE last_logged > '2011-03-13'
     * </code>
     *
     * @param     mixed $lastLogged The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientDataQuery The current query, for fluid interface
     */
    public function filterByLastLogged($lastLogged = null, $comparison = null)
    {
        if (is_array($lastLogged)) {
            $useMinMax = false;
            if (isset($lastLogged['min'])) {
                $this->addUsingAlias(ClientDataTableMap::COL_LAST_LOGGED, $lastLogged['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lastLogged['max'])) {
                $this->addUsingAlias(ClientDataTableMap::COL_LAST_LOGGED, $lastLogged['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClientDataTableMap::COL_LAST_LOGGED, $lastLogged, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroup object
     *
     * @param \Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroup|ObjectCollection $clientGroup The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientDataQuery The current query, for fluid interface
     */
    public function filterByClientGroup($clientGroup, $comparison = null)
    {
        if ($clientGroup instanceof \Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroup) {
            return $this
                ->addUsingAlias(ClientDataTableMap::COL_CLIENT_GROUP_ID, $clientGroup->getId(), $comparison);
        } elseif ($clientGroup instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ClientDataTableMap::COL_CLIENT_GROUP_ID, $clientGroup->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByClientGroup() only accepts arguments of type \Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroup or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ClientGroup relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildClientDataQuery The current query, for fluid interface
     */
    public function joinClientGroup($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ClientGroup');

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
            $this->addJoinObject($join, 'ClientGroup');
        }

        return $this;
    }

    /**
     * Use the ClientGroup relation ClientGroup object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroupQuery A secondary query class using the current class as primary query
     */
    public function useClientGroupQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinClientGroup($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ClientGroup', '\Gekosale\Plugin\ClientGroup\Model\ORM\ClientGroupQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Client\Model\ORM\Client object
     *
     * @param \Gekosale\Plugin\Client\Model\ORM\Client|ObjectCollection $client The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClientDataQuery The current query, for fluid interface
     */
    public function filterByClient($client, $comparison = null)
    {
        if ($client instanceof \Gekosale\Plugin\Client\Model\ORM\Client) {
            return $this
                ->addUsingAlias(ClientDataTableMap::COL_CLIENT_ID, $client->getId(), $comparison);
        } elseif ($client instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ClientDataTableMap::COL_CLIENT_ID, $client->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildClientDataQuery The current query, for fluid interface
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
     * Exclude object from result
     *
     * @param   ChildClientData $clientData Object to remove from the list of results
     *
     * @return ChildClientDataQuery The current query, for fluid interface
     */
    public function prune($clientData = null)
    {
        if ($clientData) {
            $this->addUsingAlias(ClientDataTableMap::COL_ID, $clientData->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the client_data table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ClientDataTableMap::DATABASE_NAME);
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
            ClientDataTableMap::clearInstancePool();
            ClientDataTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildClientData or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildClientData object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(ClientDataTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ClientDataTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        ClientDataTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            ClientDataTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // ClientDataQuery
