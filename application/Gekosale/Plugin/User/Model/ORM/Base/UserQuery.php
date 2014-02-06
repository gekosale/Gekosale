<?php

namespace Gekosale\Plugin\User\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\User\Model\ORM\User as ChildUser;
use Gekosale\Plugin\User\Model\ORM\UserQuery as ChildUserQuery;
use Gekosale\Plugin\User\Model\ORM\Map\UserTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'user' table.
 *
 * 
 *
 * @method     ChildUserQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildUserQuery orderByLogin($order = Criteria::ASC) Order by the login column
 * @method     ChildUserQuery orderByPassword($order = Criteria::ASC) Order by the password column
 * @method     ChildUserQuery orderByIsActive($order = Criteria::ASC) Order by the active column
 * @method     ChildUserQuery orderByIsGlobal($order = Criteria::ASC) Order by the global column
 *
 * @method     ChildUserQuery groupById() Group by the id column
 * @method     ChildUserQuery groupByLogin() Group by the login column
 * @method     ChildUserQuery groupByPassword() Group by the password column
 * @method     ChildUserQuery groupByIsActive() Group by the active column
 * @method     ChildUserQuery groupByIsGlobal() Group by the global column
 *
 * @method     ChildUserQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildUserQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildUserQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildUserQuery leftJoinUserGroupShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the UserGroupShop relation
 * @method     ChildUserQuery rightJoinUserGroupShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UserGroupShop relation
 * @method     ChildUserQuery innerJoinUserGroupShop($relationAlias = null) Adds a INNER JOIN clause to the query using the UserGroupShop relation
 *
 * @method     ChildUserQuery leftJoinUserData($relationAlias = null) Adds a LEFT JOIN clause to the query using the UserData relation
 * @method     ChildUserQuery rightJoinUserData($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UserData relation
 * @method     ChildUserQuery innerJoinUserData($relationAlias = null) Adds a INNER JOIN clause to the query using the UserData relation
 *
 * @method     ChildUserQuery leftJoinUserGroupUser($relationAlias = null) Adds a LEFT JOIN clause to the query using the UserGroupUser relation
 * @method     ChildUserQuery rightJoinUserGroupUser($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UserGroupUser relation
 * @method     ChildUserQuery innerJoinUserGroupUser($relationAlias = null) Adds a INNER JOIN clause to the query using the UserGroupUser relation
 *
 * @method     ChildUser findOne(ConnectionInterface $con = null) Return the first ChildUser matching the query
 * @method     ChildUser findOneOrCreate(ConnectionInterface $con = null) Return the first ChildUser matching the query, or a new ChildUser object populated from the query conditions when no match is found
 *
 * @method     ChildUser findOneById(int $id) Return the first ChildUser filtered by the id column
 * @method     ChildUser findOneByLogin(string $login) Return the first ChildUser filtered by the login column
 * @method     ChildUser findOneByPassword(string $password) Return the first ChildUser filtered by the password column
 * @method     ChildUser findOneByIsActive(int $active) Return the first ChildUser filtered by the active column
 * @method     ChildUser findOneByIsGlobal(int $global) Return the first ChildUser filtered by the global column
 *
 * @method     array findById(int $id) Return ChildUser objects filtered by the id column
 * @method     array findByLogin(string $login) Return ChildUser objects filtered by the login column
 * @method     array findByPassword(string $password) Return ChildUser objects filtered by the password column
 * @method     array findByIsActive(int $active) Return ChildUser objects filtered by the active column
 * @method     array findByIsGlobal(int $global) Return ChildUser objects filtered by the global column
 *
 */
abstract class UserQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\User\Model\ORM\Base\UserQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\User\\Model\\ORM\\User', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildUserQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildUserQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\User\Model\ORM\UserQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\User\Model\ORM\UserQuery();
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
     * @return ChildUser|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = UserTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(UserTableMap::DATABASE_NAME);
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
     * @return   ChildUser A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, LOGIN, PASSWORD, ACTIVE, GLOBAL FROM user WHERE ID = :p0';
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
            $obj = new ChildUser();
            $obj->hydrate($row);
            UserTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildUser|array|mixed the result, formatted by the current formatter
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
     * @return ChildUserQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(UserTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildUserQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(UserTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildUserQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(UserTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(UserTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserTableMap::COL_ID, $id, $comparison);
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
     * @return ChildUserQuery The current query, for fluid interface
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

        return $this->addUsingAlias(UserTableMap::COL_LOGIN, $login, $comparison);
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
     * @return ChildUserQuery The current query, for fluid interface
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

        return $this->addUsingAlias(UserTableMap::COL_PASSWORD, $password, $comparison);
    }

    /**
     * Filter the query on the active column
     *
     * Example usage:
     * <code>
     * $query->filterByIsActive(1234); // WHERE active = 1234
     * $query->filterByIsActive(array(12, 34)); // WHERE active IN (12, 34)
     * $query->filterByIsActive(array('min' => 12)); // WHERE active > 12
     * </code>
     *
     * @param     mixed $isActive The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserQuery The current query, for fluid interface
     */
    public function filterByIsActive($isActive = null, $comparison = null)
    {
        if (is_array($isActive)) {
            $useMinMax = false;
            if (isset($isActive['min'])) {
                $this->addUsingAlias(UserTableMap::COL_ACTIVE, $isActive['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($isActive['max'])) {
                $this->addUsingAlias(UserTableMap::COL_ACTIVE, $isActive['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserTableMap::COL_ACTIVE, $isActive, $comparison);
    }

    /**
     * Filter the query on the global column
     *
     * Example usage:
     * <code>
     * $query->filterByIsGlobal(1234); // WHERE global = 1234
     * $query->filterByIsGlobal(array(12, 34)); // WHERE global IN (12, 34)
     * $query->filterByIsGlobal(array('min' => 12)); // WHERE global > 12
     * </code>
     *
     * @param     mixed $isGlobal The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserQuery The current query, for fluid interface
     */
    public function filterByIsGlobal($isGlobal = null, $comparison = null)
    {
        if (is_array($isGlobal)) {
            $useMinMax = false;
            if (isset($isGlobal['min'])) {
                $this->addUsingAlias(UserTableMap::COL_GLOBAL, $isGlobal['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($isGlobal['max'])) {
                $this->addUsingAlias(UserTableMap::COL_GLOBAL, $isGlobal['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserTableMap::COL_GLOBAL, $isGlobal, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\User\Model\ORM\UserGroupShop object
     *
     * @param \Gekosale\Plugin\User\Model\ORM\UserGroupShop|ObjectCollection $userGroupShop  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserQuery The current query, for fluid interface
     */
    public function filterByUserGroupShop($userGroupShop, $comparison = null)
    {
        if ($userGroupShop instanceof \Gekosale\Plugin\User\Model\ORM\UserGroupShop) {
            return $this
                ->addUsingAlias(UserTableMap::COL_ID, $userGroupShop->getUserId(), $comparison);
        } elseif ($userGroupShop instanceof ObjectCollection) {
            return $this
                ->useUserGroupShopQuery()
                ->filterByPrimaryKeys($userGroupShop->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByUserGroupShop() only accepts arguments of type \Gekosale\Plugin\User\Model\ORM\UserGroupShop or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the UserGroupShop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildUserQuery The current query, for fluid interface
     */
    public function joinUserGroupShop($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('UserGroupShop');

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
            $this->addJoinObject($join, 'UserGroupShop');
        }

        return $this;
    }

    /**
     * Use the UserGroupShop relation UserGroupShop object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\User\Model\ORM\UserGroupShopQuery A secondary query class using the current class as primary query
     */
    public function useUserGroupShopQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUserGroupShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UserGroupShop', '\Gekosale\Plugin\User\Model\ORM\UserGroupShopQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\User\Model\ORM\UserData object
     *
     * @param \Gekosale\Plugin\User\Model\ORM\UserData|ObjectCollection $userData  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserQuery The current query, for fluid interface
     */
    public function filterByUserData($userData, $comparison = null)
    {
        if ($userData instanceof \Gekosale\Plugin\User\Model\ORM\UserData) {
            return $this
                ->addUsingAlias(UserTableMap::COL_ID, $userData->getUserId(), $comparison);
        } elseif ($userData instanceof ObjectCollection) {
            return $this
                ->useUserDataQuery()
                ->filterByPrimaryKeys($userData->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByUserData() only accepts arguments of type \Gekosale\Plugin\User\Model\ORM\UserData or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the UserData relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildUserQuery The current query, for fluid interface
     */
    public function joinUserData($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('UserData');

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
            $this->addJoinObject($join, 'UserData');
        }

        return $this;
    }

    /**
     * Use the UserData relation UserData object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\User\Model\ORM\UserDataQuery A secondary query class using the current class as primary query
     */
    public function useUserDataQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUserData($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UserData', '\Gekosale\Plugin\User\Model\ORM\UserDataQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\User\Model\ORM\UserGroupUser object
     *
     * @param \Gekosale\Plugin\User\Model\ORM\UserGroupUser|ObjectCollection $userGroupUser  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildUserQuery The current query, for fluid interface
     */
    public function filterByUserGroupUser($userGroupUser, $comparison = null)
    {
        if ($userGroupUser instanceof \Gekosale\Plugin\User\Model\ORM\UserGroupUser) {
            return $this
                ->addUsingAlias(UserTableMap::COL_ID, $userGroupUser->getUserId(), $comparison);
        } elseif ($userGroupUser instanceof ObjectCollection) {
            return $this
                ->useUserGroupUserQuery()
                ->filterByPrimaryKeys($userGroupUser->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByUserGroupUser() only accepts arguments of type \Gekosale\Plugin\User\Model\ORM\UserGroupUser or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the UserGroupUser relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildUserQuery The current query, for fluid interface
     */
    public function joinUserGroupUser($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('UserGroupUser');

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
            $this->addJoinObject($join, 'UserGroupUser');
        }

        return $this;
    }

    /**
     * Use the UserGroupUser relation UserGroupUser object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\User\Model\ORM\UserGroupUserQuery A secondary query class using the current class as primary query
     */
    public function useUserGroupUserQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUserGroupUser($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UserGroupUser', '\Gekosale\Plugin\User\Model\ORM\UserGroupUserQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildUser $user Object to remove from the list of results
     *
     * @return ChildUserQuery The current query, for fluid interface
     */
    public function prune($user = null)
    {
        if ($user) {
            $this->addUsingAlias(UserTableMap::COL_ID, $user->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the user table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(UserTableMap::DATABASE_NAME);
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
            UserTableMap::clearInstancePool();
            UserTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildUser or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildUser object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(UserTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(UserTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        UserTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            UserTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // UserQuery
