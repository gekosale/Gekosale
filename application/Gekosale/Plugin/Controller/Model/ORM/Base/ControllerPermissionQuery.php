<?php

namespace Gekosale\Plugin\Controller\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Company\Model\ORM\Company;
use Gekosale\Plugin\Controller\Model\ORM\ControllerPermission as ChildControllerPermission;
use Gekosale\Plugin\Controller\Model\ORM\ControllerPermissionQuery as ChildControllerPermissionQuery;
use Gekosale\Plugin\Controller\Model\ORM\Map\ControllerPermissionTableMap;
use Gekosale\Plugin\User\Model\ORM\UserGroup;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'controller_permission' table.
 *
 * 
 *
 * @method     ChildControllerPermissionQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildControllerPermissionQuery orderByControllerId($order = Criteria::ASC) Order by the controller_id column
 * @method     ChildControllerPermissionQuery orderByUserGroupId($order = Criteria::ASC) Order by the user_group_id column
 * @method     ChildControllerPermissionQuery orderByPermission($order = Criteria::ASC) Order by the permission column
 * @method     ChildControllerPermissionQuery orderByCompanyId($order = Criteria::ASC) Order by the company_id column
 *
 * @method     ChildControllerPermissionQuery groupById() Group by the id column
 * @method     ChildControllerPermissionQuery groupByControllerId() Group by the controller_id column
 * @method     ChildControllerPermissionQuery groupByUserGroupId() Group by the user_group_id column
 * @method     ChildControllerPermissionQuery groupByPermission() Group by the permission column
 * @method     ChildControllerPermissionQuery groupByCompanyId() Group by the company_id column
 *
 * @method     ChildControllerPermissionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildControllerPermissionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildControllerPermissionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildControllerPermissionQuery leftJoinController($relationAlias = null) Adds a LEFT JOIN clause to the query using the Controller relation
 * @method     ChildControllerPermissionQuery rightJoinController($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Controller relation
 * @method     ChildControllerPermissionQuery innerJoinController($relationAlias = null) Adds a INNER JOIN clause to the query using the Controller relation
 *
 * @method     ChildControllerPermissionQuery leftJoinUserGroup($relationAlias = null) Adds a LEFT JOIN clause to the query using the UserGroup relation
 * @method     ChildControllerPermissionQuery rightJoinUserGroup($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UserGroup relation
 * @method     ChildControllerPermissionQuery innerJoinUserGroup($relationAlias = null) Adds a INNER JOIN clause to the query using the UserGroup relation
 *
 * @method     ChildControllerPermissionQuery leftJoinCompany($relationAlias = null) Adds a LEFT JOIN clause to the query using the Company relation
 * @method     ChildControllerPermissionQuery rightJoinCompany($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Company relation
 * @method     ChildControllerPermissionQuery innerJoinCompany($relationAlias = null) Adds a INNER JOIN clause to the query using the Company relation
 *
 * @method     ChildControllerPermission findOne(ConnectionInterface $con = null) Return the first ChildControllerPermission matching the query
 * @method     ChildControllerPermission findOneOrCreate(ConnectionInterface $con = null) Return the first ChildControllerPermission matching the query, or a new ChildControllerPermission object populated from the query conditions when no match is found
 *
 * @method     ChildControllerPermission findOneById(int $id) Return the first ChildControllerPermission filtered by the id column
 * @method     ChildControllerPermission findOneByControllerId(int $controller_id) Return the first ChildControllerPermission filtered by the controller_id column
 * @method     ChildControllerPermission findOneByUserGroupId(int $user_group_id) Return the first ChildControllerPermission filtered by the user_group_id column
 * @method     ChildControllerPermission findOneByPermission(int $permission) Return the first ChildControllerPermission filtered by the permission column
 * @method     ChildControllerPermission findOneByCompanyId(int $company_id) Return the first ChildControllerPermission filtered by the company_id column
 *
 * @method     array findById(int $id) Return ChildControllerPermission objects filtered by the id column
 * @method     array findByControllerId(int $controller_id) Return ChildControllerPermission objects filtered by the controller_id column
 * @method     array findByUserGroupId(int $user_group_id) Return ChildControllerPermission objects filtered by the user_group_id column
 * @method     array findByPermission(int $permission) Return ChildControllerPermission objects filtered by the permission column
 * @method     array findByCompanyId(int $company_id) Return ChildControllerPermission objects filtered by the company_id column
 *
 */
abstract class ControllerPermissionQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Controller\Model\ORM\Base\ControllerPermissionQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Controller\\Model\\ORM\\ControllerPermission', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildControllerPermissionQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildControllerPermissionQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Controller\Model\ORM\ControllerPermissionQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Controller\Model\ORM\ControllerPermissionQuery();
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
     * @return ChildControllerPermission|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ControllerPermissionTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ControllerPermissionTableMap::DATABASE_NAME);
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
     * @return   ChildControllerPermission A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, CONTROLLER_ID, USER_GROUP_ID, PERMISSION, COMPANY_ID FROM controller_permission WHERE ID = :p0';
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
            $obj = new ChildControllerPermission();
            $obj->hydrate($row);
            ControllerPermissionTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildControllerPermission|array|mixed the result, formatted by the current formatter
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
     * @return ChildControllerPermissionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ControllerPermissionTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildControllerPermissionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ControllerPermissionTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildControllerPermissionQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ControllerPermissionTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ControllerPermissionTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ControllerPermissionTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the controller_id column
     *
     * Example usage:
     * <code>
     * $query->filterByControllerId(1234); // WHERE controller_id = 1234
     * $query->filterByControllerId(array(12, 34)); // WHERE controller_id IN (12, 34)
     * $query->filterByControllerId(array('min' => 12)); // WHERE controller_id > 12
     * </code>
     *
     * @see       filterByController()
     *
     * @param     mixed $controllerId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildControllerPermissionQuery The current query, for fluid interface
     */
    public function filterByControllerId($controllerId = null, $comparison = null)
    {
        if (is_array($controllerId)) {
            $useMinMax = false;
            if (isset($controllerId['min'])) {
                $this->addUsingAlias(ControllerPermissionTableMap::COL_CONTROLLER_ID, $controllerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($controllerId['max'])) {
                $this->addUsingAlias(ControllerPermissionTableMap::COL_CONTROLLER_ID, $controllerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ControllerPermissionTableMap::COL_CONTROLLER_ID, $controllerId, $comparison);
    }

    /**
     * Filter the query on the user_group_id column
     *
     * Example usage:
     * <code>
     * $query->filterByUserGroupId(1234); // WHERE user_group_id = 1234
     * $query->filterByUserGroupId(array(12, 34)); // WHERE user_group_id IN (12, 34)
     * $query->filterByUserGroupId(array('min' => 12)); // WHERE user_group_id > 12
     * </code>
     *
     * @see       filterByUserGroup()
     *
     * @param     mixed $userGroupId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildControllerPermissionQuery The current query, for fluid interface
     */
    public function filterByUserGroupId($userGroupId = null, $comparison = null)
    {
        if (is_array($userGroupId)) {
            $useMinMax = false;
            if (isset($userGroupId['min'])) {
                $this->addUsingAlias(ControllerPermissionTableMap::COL_USER_GROUP_ID, $userGroupId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($userGroupId['max'])) {
                $this->addUsingAlias(ControllerPermissionTableMap::COL_USER_GROUP_ID, $userGroupId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ControllerPermissionTableMap::COL_USER_GROUP_ID, $userGroupId, $comparison);
    }

    /**
     * Filter the query on the permission column
     *
     * Example usage:
     * <code>
     * $query->filterByPermission(1234); // WHERE permission = 1234
     * $query->filterByPermission(array(12, 34)); // WHERE permission IN (12, 34)
     * $query->filterByPermission(array('min' => 12)); // WHERE permission > 12
     * </code>
     *
     * @param     mixed $permission The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildControllerPermissionQuery The current query, for fluid interface
     */
    public function filterByPermission($permission = null, $comparison = null)
    {
        if (is_array($permission)) {
            $useMinMax = false;
            if (isset($permission['min'])) {
                $this->addUsingAlias(ControllerPermissionTableMap::COL_PERMISSION, $permission['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($permission['max'])) {
                $this->addUsingAlias(ControllerPermissionTableMap::COL_PERMISSION, $permission['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ControllerPermissionTableMap::COL_PERMISSION, $permission, $comparison);
    }

    /**
     * Filter the query on the company_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCompanyId(1234); // WHERE company_id = 1234
     * $query->filterByCompanyId(array(12, 34)); // WHERE company_id IN (12, 34)
     * $query->filterByCompanyId(array('min' => 12)); // WHERE company_id > 12
     * </code>
     *
     * @see       filterByCompany()
     *
     * @param     mixed $companyId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildControllerPermissionQuery The current query, for fluid interface
     */
    public function filterByCompanyId($companyId = null, $comparison = null)
    {
        if (is_array($companyId)) {
            $useMinMax = false;
            if (isset($companyId['min'])) {
                $this->addUsingAlias(ControllerPermissionTableMap::COL_COMPANY_ID, $companyId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($companyId['max'])) {
                $this->addUsingAlias(ControllerPermissionTableMap::COL_COMPANY_ID, $companyId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ControllerPermissionTableMap::COL_COMPANY_ID, $companyId, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Controller\Model\ORM\Controller object
     *
     * @param \Gekosale\Plugin\Controller\Model\ORM\Controller|ObjectCollection $controller The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildControllerPermissionQuery The current query, for fluid interface
     */
    public function filterByController($controller, $comparison = null)
    {
        if ($controller instanceof \Gekosale\Plugin\Controller\Model\ORM\Controller) {
            return $this
                ->addUsingAlias(ControllerPermissionTableMap::COL_CONTROLLER_ID, $controller->getId(), $comparison);
        } elseif ($controller instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ControllerPermissionTableMap::COL_CONTROLLER_ID, $controller->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByController() only accepts arguments of type \Gekosale\Plugin\Controller\Model\ORM\Controller or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Controller relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildControllerPermissionQuery The current query, for fluid interface
     */
    public function joinController($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Controller');

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
            $this->addJoinObject($join, 'Controller');
        }

        return $this;
    }

    /**
     * Use the Controller relation Controller object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Controller\Model\ORM\ControllerQuery A secondary query class using the current class as primary query
     */
    public function useControllerQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinController($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Controller', '\Gekosale\Plugin\Controller\Model\ORM\ControllerQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\User\Model\ORM\UserGroup object
     *
     * @param \Gekosale\Plugin\User\Model\ORM\UserGroup|ObjectCollection $userGroup The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildControllerPermissionQuery The current query, for fluid interface
     */
    public function filterByUserGroup($userGroup, $comparison = null)
    {
        if ($userGroup instanceof \Gekosale\Plugin\User\Model\ORM\UserGroup) {
            return $this
                ->addUsingAlias(ControllerPermissionTableMap::COL_USER_GROUP_ID, $userGroup->getId(), $comparison);
        } elseif ($userGroup instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ControllerPermissionTableMap::COL_USER_GROUP_ID, $userGroup->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByUserGroup() only accepts arguments of type \Gekosale\Plugin\User\Model\ORM\UserGroup or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the UserGroup relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildControllerPermissionQuery The current query, for fluid interface
     */
    public function joinUserGroup($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('UserGroup');

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
            $this->addJoinObject($join, 'UserGroup');
        }

        return $this;
    }

    /**
     * Use the UserGroup relation UserGroup object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\User\Model\ORM\UserGroupQuery A secondary query class using the current class as primary query
     */
    public function useUserGroupQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUserGroup($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UserGroup', '\Gekosale\Plugin\User\Model\ORM\UserGroupQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Company\Model\ORM\Company object
     *
     * @param \Gekosale\Plugin\Company\Model\ORM\Company|ObjectCollection $company The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildControllerPermissionQuery The current query, for fluid interface
     */
    public function filterByCompany($company, $comparison = null)
    {
        if ($company instanceof \Gekosale\Plugin\Company\Model\ORM\Company) {
            return $this
                ->addUsingAlias(ControllerPermissionTableMap::COL_COMPANY_ID, $company->getId(), $comparison);
        } elseif ($company instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ControllerPermissionTableMap::COL_COMPANY_ID, $company->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCompany() only accepts arguments of type \Gekosale\Plugin\Company\Model\ORM\Company or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Company relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildControllerPermissionQuery The current query, for fluid interface
     */
    public function joinCompany($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Company');

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
            $this->addJoinObject($join, 'Company');
        }

        return $this;
    }

    /**
     * Use the Company relation Company object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Company\Model\ORM\CompanyQuery A secondary query class using the current class as primary query
     */
    public function useCompanyQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCompany($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Company', '\Gekosale\Plugin\Company\Model\ORM\CompanyQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildControllerPermission $controllerPermission Object to remove from the list of results
     *
     * @return ChildControllerPermissionQuery The current query, for fluid interface
     */
    public function prune($controllerPermission = null)
    {
        if ($controllerPermission) {
            $this->addUsingAlias(ControllerPermissionTableMap::COL_ID, $controllerPermission->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the controller_permission table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ControllerPermissionTableMap::DATABASE_NAME);
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
            ControllerPermissionTableMap::clearInstancePool();
            ControllerPermissionTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildControllerPermission or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildControllerPermission object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(ControllerPermissionTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ControllerPermissionTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        ControllerPermissionTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            ControllerPermissionTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // ControllerPermissionQuery
