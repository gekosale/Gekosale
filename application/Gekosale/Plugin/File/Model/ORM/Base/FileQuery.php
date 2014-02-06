<?php

namespace Gekosale\Plugin\File\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute;
use Gekosale\Plugin\Blog\Model\ORM\BlogPhoto;
use Gekosale\Plugin\Category\Model\ORM\Category;
use Gekosale\Plugin\Company\Model\ORM\Company;
use Gekosale\Plugin\Deliverer\Model\ORM\Deliverer;
use Gekosale\Plugin\File\Model\ORM\File as ChildFile;
use Gekosale\Plugin\File\Model\ORM\FileQuery as ChildFileQuery;
use Gekosale\Plugin\File\Model\ORM\Map\FileTableMap;
use Gekosale\Plugin\Producer\Model\ORM\Producer;
use Gekosale\Plugin\Product\Model\ORM\ProductFile;
use Gekosale\Plugin\Product\Model\ORM\ProductPhoto;
use Gekosale\Plugin\User\Model\ORM\UserData;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'file' table.
 *
 * 
 *
 * @method     ChildFileQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildFileQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method     ChildFileQuery orderByFileTypeId($order = Criteria::ASC) Order by the file_type_id column
 * @method     ChildFileQuery orderByFileExtensionId($order = Criteria::ASC) Order by the file_extension_id column
 * @method     ChildFileQuery orderByIsVisible($order = Criteria::ASC) Order by the visible column
 *
 * @method     ChildFileQuery groupById() Group by the id column
 * @method     ChildFileQuery groupByName() Group by the name column
 * @method     ChildFileQuery groupByFileTypeId() Group by the file_type_id column
 * @method     ChildFileQuery groupByFileExtensionId() Group by the file_extension_id column
 * @method     ChildFileQuery groupByIsVisible() Group by the visible column
 *
 * @method     ChildFileQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildFileQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildFileQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildFileQuery leftJoinBlogPhoto($relationAlias = null) Adds a LEFT JOIN clause to the query using the BlogPhoto relation
 * @method     ChildFileQuery rightJoinBlogPhoto($relationAlias = null) Adds a RIGHT JOIN clause to the query using the BlogPhoto relation
 * @method     ChildFileQuery innerJoinBlogPhoto($relationAlias = null) Adds a INNER JOIN clause to the query using the BlogPhoto relation
 *
 * @method     ChildFileQuery leftJoinCategory($relationAlias = null) Adds a LEFT JOIN clause to the query using the Category relation
 * @method     ChildFileQuery rightJoinCategory($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Category relation
 * @method     ChildFileQuery innerJoinCategory($relationAlias = null) Adds a INNER JOIN clause to the query using the Category relation
 *
 * @method     ChildFileQuery leftJoinCompany($relationAlias = null) Adds a LEFT JOIN clause to the query using the Company relation
 * @method     ChildFileQuery rightJoinCompany($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Company relation
 * @method     ChildFileQuery innerJoinCompany($relationAlias = null) Adds a INNER JOIN clause to the query using the Company relation
 *
 * @method     ChildFileQuery leftJoinDeliverer($relationAlias = null) Adds a LEFT JOIN clause to the query using the Deliverer relation
 * @method     ChildFileQuery rightJoinDeliverer($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Deliverer relation
 * @method     ChildFileQuery innerJoinDeliverer($relationAlias = null) Adds a INNER JOIN clause to the query using the Deliverer relation
 *
 * @method     ChildFileQuery leftJoinProducer($relationAlias = null) Adds a LEFT JOIN clause to the query using the Producer relation
 * @method     ChildFileQuery rightJoinProducer($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Producer relation
 * @method     ChildFileQuery innerJoinProducer($relationAlias = null) Adds a INNER JOIN clause to the query using the Producer relation
 *
 * @method     ChildFileQuery leftJoinProductAttribute($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductAttribute relation
 * @method     ChildFileQuery rightJoinProductAttribute($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductAttribute relation
 * @method     ChildFileQuery innerJoinProductAttribute($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductAttribute relation
 *
 * @method     ChildFileQuery leftJoinProductFile($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductFile relation
 * @method     ChildFileQuery rightJoinProductFile($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductFile relation
 * @method     ChildFileQuery innerJoinProductFile($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductFile relation
 *
 * @method     ChildFileQuery leftJoinProductPhoto($relationAlias = null) Adds a LEFT JOIN clause to the query using the ProductPhoto relation
 * @method     ChildFileQuery rightJoinProductPhoto($relationAlias = null) Adds a RIGHT JOIN clause to the query using the ProductPhoto relation
 * @method     ChildFileQuery innerJoinProductPhoto($relationAlias = null) Adds a INNER JOIN clause to the query using the ProductPhoto relation
 *
 * @method     ChildFileQuery leftJoinUserData($relationAlias = null) Adds a LEFT JOIN clause to the query using the UserData relation
 * @method     ChildFileQuery rightJoinUserData($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UserData relation
 * @method     ChildFileQuery innerJoinUserData($relationAlias = null) Adds a INNER JOIN clause to the query using the UserData relation
 *
 * @method     ChildFile findOne(ConnectionInterface $con = null) Return the first ChildFile matching the query
 * @method     ChildFile findOneOrCreate(ConnectionInterface $con = null) Return the first ChildFile matching the query, or a new ChildFile object populated from the query conditions when no match is found
 *
 * @method     ChildFile findOneById(int $id) Return the first ChildFile filtered by the id column
 * @method     ChildFile findOneByName(string $name) Return the first ChildFile filtered by the name column
 * @method     ChildFile findOneByFileTypeId(int $file_type_id) Return the first ChildFile filtered by the file_type_id column
 * @method     ChildFile findOneByFileExtensionId(int $file_extension_id) Return the first ChildFile filtered by the file_extension_id column
 * @method     ChildFile findOneByIsVisible(int $visible) Return the first ChildFile filtered by the visible column
 *
 * @method     array findById(int $id) Return ChildFile objects filtered by the id column
 * @method     array findByName(string $name) Return ChildFile objects filtered by the name column
 * @method     array findByFileTypeId(int $file_type_id) Return ChildFile objects filtered by the file_type_id column
 * @method     array findByFileExtensionId(int $file_extension_id) Return ChildFile objects filtered by the file_extension_id column
 * @method     array findByIsVisible(int $visible) Return ChildFile objects filtered by the visible column
 *
 */
abstract class FileQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\File\Model\ORM\Base\FileQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\File\\Model\\ORM\\File', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildFileQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildFileQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\File\Model\ORM\FileQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\File\Model\ORM\FileQuery();
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
     * @return ChildFile|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = FileTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(FileTableMap::DATABASE_NAME);
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
     * @return   ChildFile A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, NAME, FILE_TYPE_ID, FILE_EXTENSION_ID, VISIBLE FROM file WHERE ID = :p0';
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
            $obj = new ChildFile();
            $obj->hydrate($row);
            FileTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildFile|array|mixed the result, formatted by the current formatter
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
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(FileTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(FileTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(FileTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(FileTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FileTableMap::COL_ID, $id, $comparison);
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
     * @return ChildFileQuery The current query, for fluid interface
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

        return $this->addUsingAlias(FileTableMap::COL_NAME, $name, $comparison);
    }

    /**
     * Filter the query on the file_type_id column
     *
     * Example usage:
     * <code>
     * $query->filterByFileTypeId(1234); // WHERE file_type_id = 1234
     * $query->filterByFileTypeId(array(12, 34)); // WHERE file_type_id IN (12, 34)
     * $query->filterByFileTypeId(array('min' => 12)); // WHERE file_type_id > 12
     * </code>
     *
     * @param     mixed $fileTypeId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function filterByFileTypeId($fileTypeId = null, $comparison = null)
    {
        if (is_array($fileTypeId)) {
            $useMinMax = false;
            if (isset($fileTypeId['min'])) {
                $this->addUsingAlias(FileTableMap::COL_FILE_TYPE_ID, $fileTypeId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($fileTypeId['max'])) {
                $this->addUsingAlias(FileTableMap::COL_FILE_TYPE_ID, $fileTypeId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FileTableMap::COL_FILE_TYPE_ID, $fileTypeId, $comparison);
    }

    /**
     * Filter the query on the file_extension_id column
     *
     * Example usage:
     * <code>
     * $query->filterByFileExtensionId(1234); // WHERE file_extension_id = 1234
     * $query->filterByFileExtensionId(array(12, 34)); // WHERE file_extension_id IN (12, 34)
     * $query->filterByFileExtensionId(array('min' => 12)); // WHERE file_extension_id > 12
     * </code>
     *
     * @param     mixed $fileExtensionId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function filterByFileExtensionId($fileExtensionId = null, $comparison = null)
    {
        if (is_array($fileExtensionId)) {
            $useMinMax = false;
            if (isset($fileExtensionId['min'])) {
                $this->addUsingAlias(FileTableMap::COL_FILE_EXTENSION_ID, $fileExtensionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($fileExtensionId['max'])) {
                $this->addUsingAlias(FileTableMap::COL_FILE_EXTENSION_ID, $fileExtensionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FileTableMap::COL_FILE_EXTENSION_ID, $fileExtensionId, $comparison);
    }

    /**
     * Filter the query on the visible column
     *
     * Example usage:
     * <code>
     * $query->filterByIsVisible(1234); // WHERE visible = 1234
     * $query->filterByIsVisible(array(12, 34)); // WHERE visible IN (12, 34)
     * $query->filterByIsVisible(array('min' => 12)); // WHERE visible > 12
     * </code>
     *
     * @param     mixed $isVisible The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function filterByIsVisible($isVisible = null, $comparison = null)
    {
        if (is_array($isVisible)) {
            $useMinMax = false;
            if (isset($isVisible['min'])) {
                $this->addUsingAlias(FileTableMap::COL_VISIBLE, $isVisible['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($isVisible['max'])) {
                $this->addUsingAlias(FileTableMap::COL_VISIBLE, $isVisible['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FileTableMap::COL_VISIBLE, $isVisible, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Blog\Model\ORM\BlogPhoto object
     *
     * @param \Gekosale\Plugin\Blog\Model\ORM\BlogPhoto|ObjectCollection $blogPhoto  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function filterByBlogPhoto($blogPhoto, $comparison = null)
    {
        if ($blogPhoto instanceof \Gekosale\Plugin\Blog\Model\ORM\BlogPhoto) {
            return $this
                ->addUsingAlias(FileTableMap::COL_ID, $blogPhoto->getPhotoId(), $comparison);
        } elseif ($blogPhoto instanceof ObjectCollection) {
            return $this
                ->useBlogPhotoQuery()
                ->filterByPrimaryKeys($blogPhoto->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByBlogPhoto() only accepts arguments of type \Gekosale\Plugin\Blog\Model\ORM\BlogPhoto or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the BlogPhoto relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function joinBlogPhoto($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('BlogPhoto');

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
            $this->addJoinObject($join, 'BlogPhoto');
        }

        return $this;
    }

    /**
     * Use the BlogPhoto relation BlogPhoto object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Blog\Model\ORM\BlogPhotoQuery A secondary query class using the current class as primary query
     */
    public function useBlogPhotoQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinBlogPhoto($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'BlogPhoto', '\Gekosale\Plugin\Blog\Model\ORM\BlogPhotoQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Category\Model\ORM\Category object
     *
     * @param \Gekosale\Plugin\Category\Model\ORM\Category|ObjectCollection $category  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function filterByCategory($category, $comparison = null)
    {
        if ($category instanceof \Gekosale\Plugin\Category\Model\ORM\Category) {
            return $this
                ->addUsingAlias(FileTableMap::COL_ID, $category->getPhotoId(), $comparison);
        } elseif ($category instanceof ObjectCollection) {
            return $this
                ->useCategoryQuery()
                ->filterByPrimaryKeys($category->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByCategory() only accepts arguments of type \Gekosale\Plugin\Category\Model\ORM\Category or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Category relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function joinCategory($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Category');

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
            $this->addJoinObject($join, 'Category');
        }

        return $this;
    }

    /**
     * Use the Category relation Category object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Category\Model\ORM\CategoryQuery A secondary query class using the current class as primary query
     */
    public function useCategoryQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCategory($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Category', '\Gekosale\Plugin\Category\Model\ORM\CategoryQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Company\Model\ORM\Company object
     *
     * @param \Gekosale\Plugin\Company\Model\ORM\Company|ObjectCollection $company  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function filterByCompany($company, $comparison = null)
    {
        if ($company instanceof \Gekosale\Plugin\Company\Model\ORM\Company) {
            return $this
                ->addUsingAlias(FileTableMap::COL_ID, $company->getPhotoId(), $comparison);
        } elseif ($company instanceof ObjectCollection) {
            return $this
                ->useCompanyQuery()
                ->filterByPrimaryKeys($company->getPrimaryKeys())
                ->endUse();
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
     * @return ChildFileQuery The current query, for fluid interface
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
     * Filter the query by a related \Gekosale\Plugin\Deliverer\Model\ORM\Deliverer object
     *
     * @param \Gekosale\Plugin\Deliverer\Model\ORM\Deliverer|ObjectCollection $deliverer  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function filterByDeliverer($deliverer, $comparison = null)
    {
        if ($deliverer instanceof \Gekosale\Plugin\Deliverer\Model\ORM\Deliverer) {
            return $this
                ->addUsingAlias(FileTableMap::COL_ID, $deliverer->getPhotoId(), $comparison);
        } elseif ($deliverer instanceof ObjectCollection) {
            return $this
                ->useDelivererQuery()
                ->filterByPrimaryKeys($deliverer->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDeliverer() only accepts arguments of type \Gekosale\Plugin\Deliverer\Model\ORM\Deliverer or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Deliverer relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function joinDeliverer($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Deliverer');

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
            $this->addJoinObject($join, 'Deliverer');
        }

        return $this;
    }

    /**
     * Use the Deliverer relation Deliverer object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Deliverer\Model\ORM\DelivererQuery A secondary query class using the current class as primary query
     */
    public function useDelivererQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinDeliverer($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Deliverer', '\Gekosale\Plugin\Deliverer\Model\ORM\DelivererQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Producer\Model\ORM\Producer object
     *
     * @param \Gekosale\Plugin\Producer\Model\ORM\Producer|ObjectCollection $producer  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function filterByProducer($producer, $comparison = null)
    {
        if ($producer instanceof \Gekosale\Plugin\Producer\Model\ORM\Producer) {
            return $this
                ->addUsingAlias(FileTableMap::COL_ID, $producer->getPhotoId(), $comparison);
        } elseif ($producer instanceof ObjectCollection) {
            return $this
                ->useProducerQuery()
                ->filterByPrimaryKeys($producer->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProducer() only accepts arguments of type \Gekosale\Plugin\Producer\Model\ORM\Producer or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Producer relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function joinProducer($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Producer');

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
            $this->addJoinObject($join, 'Producer');
        }

        return $this;
    }

    /**
     * Use the Producer relation Producer object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Producer\Model\ORM\ProducerQuery A secondary query class using the current class as primary query
     */
    public function useProducerQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinProducer($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Producer', '\Gekosale\Plugin\Producer\Model\ORM\ProducerQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute object
     *
     * @param \Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute|ObjectCollection $productAttribute  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function filterByProductAttribute($productAttribute, $comparison = null)
    {
        if ($productAttribute instanceof \Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute) {
            return $this
                ->addUsingAlias(FileTableMap::COL_ID, $productAttribute->getPhotoId(), $comparison);
        } elseif ($productAttribute instanceof ObjectCollection) {
            return $this
                ->useProductAttributeQuery()
                ->filterByPrimaryKeys($productAttribute->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductAttribute() only accepts arguments of type \Gekosale\Plugin\Attribute\Model\ORM\ProductAttribute or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductAttribute relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function joinProductAttribute($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductAttribute');

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
            $this->addJoinObject($join, 'ProductAttribute');
        }

        return $this;
    }

    /**
     * Use the ProductAttribute relation ProductAttribute object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeQuery A secondary query class using the current class as primary query
     */
    public function useProductAttributeQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinProductAttribute($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductAttribute', '\Gekosale\Plugin\Attribute\Model\ORM\ProductAttributeQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Product\Model\ORM\ProductFile object
     *
     * @param \Gekosale\Plugin\Product\Model\ORM\ProductFile|ObjectCollection $productFile  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function filterByProductFile($productFile, $comparison = null)
    {
        if ($productFile instanceof \Gekosale\Plugin\Product\Model\ORM\ProductFile) {
            return $this
                ->addUsingAlias(FileTableMap::COL_ID, $productFile->getFileId(), $comparison);
        } elseif ($productFile instanceof ObjectCollection) {
            return $this
                ->useProductFileQuery()
                ->filterByPrimaryKeys($productFile->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductFile() only accepts arguments of type \Gekosale\Plugin\Product\Model\ORM\ProductFile or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductFile relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function joinProductFile($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductFile');

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
            $this->addJoinObject($join, 'ProductFile');
        }

        return $this;
    }

    /**
     * Use the ProductFile relation ProductFile object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Product\Model\ORM\ProductFileQuery A secondary query class using the current class as primary query
     */
    public function useProductFileQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinProductFile($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductFile', '\Gekosale\Plugin\Product\Model\ORM\ProductFileQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Product\Model\ORM\ProductPhoto object
     *
     * @param \Gekosale\Plugin\Product\Model\ORM\ProductPhoto|ObjectCollection $productPhoto  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function filterByProductPhoto($productPhoto, $comparison = null)
    {
        if ($productPhoto instanceof \Gekosale\Plugin\Product\Model\ORM\ProductPhoto) {
            return $this
                ->addUsingAlias(FileTableMap::COL_ID, $productPhoto->getPhotoId(), $comparison);
        } elseif ($productPhoto instanceof ObjectCollection) {
            return $this
                ->useProductPhotoQuery()
                ->filterByPrimaryKeys($productPhoto->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByProductPhoto() only accepts arguments of type \Gekosale\Plugin\Product\Model\ORM\ProductPhoto or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the ProductPhoto relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function joinProductPhoto($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('ProductPhoto');

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
            $this->addJoinObject($join, 'ProductPhoto');
        }

        return $this;
    }

    /**
     * Use the ProductPhoto relation ProductPhoto object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Product\Model\ORM\ProductPhotoQuery A secondary query class using the current class as primary query
     */
    public function useProductPhotoQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinProductPhoto($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'ProductPhoto', '\Gekosale\Plugin\Product\Model\ORM\ProductPhotoQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\User\Model\ORM\UserData object
     *
     * @param \Gekosale\Plugin\User\Model\ORM\UserData|ObjectCollection $userData  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function filterByUserData($userData, $comparison = null)
    {
        if ($userData instanceof \Gekosale\Plugin\User\Model\ORM\UserData) {
            return $this
                ->addUsingAlias(FileTableMap::COL_ID, $userData->getPhotoId(), $comparison);
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
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function joinUserData($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useUserDataQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinUserData($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UserData', '\Gekosale\Plugin\User\Model\ORM\UserDataQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildFile $file Object to remove from the list of results
     *
     * @return ChildFileQuery The current query, for fluid interface
     */
    public function prune($file = null)
    {
        if ($file) {
            $this->addUsingAlias(FileTableMap::COL_ID, $file->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the file table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(FileTableMap::DATABASE_NAME);
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
            FileTableMap::clearInstancePool();
            FileTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildFile or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildFile object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(FileTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(FileTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        FileTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            FileTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // FileQuery
