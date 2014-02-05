<?php

namespace Gekosale\Plugin\Blog\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Blog\Model\ORM\BlogPhoto as ChildBlogPhoto;
use Gekosale\Plugin\Blog\Model\ORM\BlogPhotoQuery as ChildBlogPhotoQuery;
use Gekosale\Plugin\Blog\Model\ORM\Map\BlogPhotoTableMap;
use Gekosale\Plugin\File\Model\ORM\File;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'blog_photo' table.
 *
 * 
 *
 * @method     ChildBlogPhotoQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildBlogPhotoQuery orderByBlogId($order = Criteria::ASC) Order by the blog_id column
 * @method     ChildBlogPhotoQuery orderByPhotoId($order = Criteria::ASC) Order by the photo_id column
 * @method     ChildBlogPhotoQuery orderByIsMainPhoto($order = Criteria::ASC) Order by the is_main_photo column
 *
 * @method     ChildBlogPhotoQuery groupById() Group by the id column
 * @method     ChildBlogPhotoQuery groupByBlogId() Group by the blog_id column
 * @method     ChildBlogPhotoQuery groupByPhotoId() Group by the photo_id column
 * @method     ChildBlogPhotoQuery groupByIsMainPhoto() Group by the is_main_photo column
 *
 * @method     ChildBlogPhotoQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildBlogPhotoQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildBlogPhotoQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildBlogPhotoQuery leftJoinBlog($relationAlias = null) Adds a LEFT JOIN clause to the query using the Blog relation
 * @method     ChildBlogPhotoQuery rightJoinBlog($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Blog relation
 * @method     ChildBlogPhotoQuery innerJoinBlog($relationAlias = null) Adds a INNER JOIN clause to the query using the Blog relation
 *
 * @method     ChildBlogPhotoQuery leftJoinFile($relationAlias = null) Adds a LEFT JOIN clause to the query using the File relation
 * @method     ChildBlogPhotoQuery rightJoinFile($relationAlias = null) Adds a RIGHT JOIN clause to the query using the File relation
 * @method     ChildBlogPhotoQuery innerJoinFile($relationAlias = null) Adds a INNER JOIN clause to the query using the File relation
 *
 * @method     ChildBlogPhoto findOne(ConnectionInterface $con = null) Return the first ChildBlogPhoto matching the query
 * @method     ChildBlogPhoto findOneOrCreate(ConnectionInterface $con = null) Return the first ChildBlogPhoto matching the query, or a new ChildBlogPhoto object populated from the query conditions when no match is found
 *
 * @method     ChildBlogPhoto findOneById(int $id) Return the first ChildBlogPhoto filtered by the id column
 * @method     ChildBlogPhoto findOneByBlogId(int $blog_id) Return the first ChildBlogPhoto filtered by the blog_id column
 * @method     ChildBlogPhoto findOneByPhotoId(int $photo_id) Return the first ChildBlogPhoto filtered by the photo_id column
 * @method     ChildBlogPhoto findOneByIsMainPhoto(int $is_main_photo) Return the first ChildBlogPhoto filtered by the is_main_photo column
 *
 * @method     array findById(int $id) Return ChildBlogPhoto objects filtered by the id column
 * @method     array findByBlogId(int $blog_id) Return ChildBlogPhoto objects filtered by the blog_id column
 * @method     array findByPhotoId(int $photo_id) Return ChildBlogPhoto objects filtered by the photo_id column
 * @method     array findByIsMainPhoto(int $is_main_photo) Return ChildBlogPhoto objects filtered by the is_main_photo column
 *
 */
abstract class BlogPhotoQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Blog\Model\ORM\Base\BlogPhotoQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Blog\\Model\\ORM\\BlogPhoto', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildBlogPhotoQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildBlogPhotoQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Blog\Model\ORM\BlogPhotoQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Blog\Model\ORM\BlogPhotoQuery();
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
     * @return ChildBlogPhoto|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = BlogPhotoTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(BlogPhotoTableMap::DATABASE_NAME);
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
     * @return   ChildBlogPhoto A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, BLOG_ID, PHOTO_ID, IS_MAIN_PHOTO FROM blog_photo WHERE ID = :p0';
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
            $obj = new ChildBlogPhoto();
            $obj->hydrate($row);
            BlogPhotoTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildBlogPhoto|array|mixed the result, formatted by the current formatter
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
     * @return ChildBlogPhotoQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(BlogPhotoTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildBlogPhotoQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(BlogPhotoTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildBlogPhotoQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(BlogPhotoTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(BlogPhotoTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BlogPhotoTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the blog_id column
     *
     * Example usage:
     * <code>
     * $query->filterByBlogId(1234); // WHERE blog_id = 1234
     * $query->filterByBlogId(array(12, 34)); // WHERE blog_id IN (12, 34)
     * $query->filterByBlogId(array('min' => 12)); // WHERE blog_id > 12
     * </code>
     *
     * @see       filterByBlog()
     *
     * @param     mixed $blogId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBlogPhotoQuery The current query, for fluid interface
     */
    public function filterByBlogId($blogId = null, $comparison = null)
    {
        if (is_array($blogId)) {
            $useMinMax = false;
            if (isset($blogId['min'])) {
                $this->addUsingAlias(BlogPhotoTableMap::COL_BLOG_ID, $blogId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($blogId['max'])) {
                $this->addUsingAlias(BlogPhotoTableMap::COL_BLOG_ID, $blogId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BlogPhotoTableMap::COL_BLOG_ID, $blogId, $comparison);
    }

    /**
     * Filter the query on the photo_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPhotoId(1234); // WHERE photo_id = 1234
     * $query->filterByPhotoId(array(12, 34)); // WHERE photo_id IN (12, 34)
     * $query->filterByPhotoId(array('min' => 12)); // WHERE photo_id > 12
     * </code>
     *
     * @see       filterByFile()
     *
     * @param     mixed $photoId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBlogPhotoQuery The current query, for fluid interface
     */
    public function filterByPhotoId($photoId = null, $comparison = null)
    {
        if (is_array($photoId)) {
            $useMinMax = false;
            if (isset($photoId['min'])) {
                $this->addUsingAlias(BlogPhotoTableMap::COL_PHOTO_ID, $photoId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($photoId['max'])) {
                $this->addUsingAlias(BlogPhotoTableMap::COL_PHOTO_ID, $photoId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BlogPhotoTableMap::COL_PHOTO_ID, $photoId, $comparison);
    }

    /**
     * Filter the query on the is_main_photo column
     *
     * Example usage:
     * <code>
     * $query->filterByIsMainPhoto(1234); // WHERE is_main_photo = 1234
     * $query->filterByIsMainPhoto(array(12, 34)); // WHERE is_main_photo IN (12, 34)
     * $query->filterByIsMainPhoto(array('min' => 12)); // WHERE is_main_photo > 12
     * </code>
     *
     * @param     mixed $isMainPhoto The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBlogPhotoQuery The current query, for fluid interface
     */
    public function filterByIsMainPhoto($isMainPhoto = null, $comparison = null)
    {
        if (is_array($isMainPhoto)) {
            $useMinMax = false;
            if (isset($isMainPhoto['min'])) {
                $this->addUsingAlias(BlogPhotoTableMap::COL_IS_MAIN_PHOTO, $isMainPhoto['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($isMainPhoto['max'])) {
                $this->addUsingAlias(BlogPhotoTableMap::COL_IS_MAIN_PHOTO, $isMainPhoto['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(BlogPhotoTableMap::COL_IS_MAIN_PHOTO, $isMainPhoto, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Blog\Model\ORM\Blog object
     *
     * @param \Gekosale\Plugin\Blog\Model\ORM\Blog|ObjectCollection $blog The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBlogPhotoQuery The current query, for fluid interface
     */
    public function filterByBlog($blog, $comparison = null)
    {
        if ($blog instanceof \Gekosale\Plugin\Blog\Model\ORM\Blog) {
            return $this
                ->addUsingAlias(BlogPhotoTableMap::COL_BLOG_ID, $blog->getId(), $comparison);
        } elseif ($blog instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(BlogPhotoTableMap::COL_BLOG_ID, $blog->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByBlog() only accepts arguments of type \Gekosale\Plugin\Blog\Model\ORM\Blog or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Blog relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildBlogPhotoQuery The current query, for fluid interface
     */
    public function joinBlog($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Blog');

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
            $this->addJoinObject($join, 'Blog');
        }

        return $this;
    }

    /**
     * Use the Blog relation Blog object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Blog\Model\ORM\BlogQuery A secondary query class using the current class as primary query
     */
    public function useBlogQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinBlog($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Blog', '\Gekosale\Plugin\Blog\Model\ORM\BlogQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\File\Model\ORM\File object
     *
     * @param \Gekosale\Plugin\File\Model\ORM\File|ObjectCollection $file The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildBlogPhotoQuery The current query, for fluid interface
     */
    public function filterByFile($file, $comparison = null)
    {
        if ($file instanceof \Gekosale\Plugin\File\Model\ORM\File) {
            return $this
                ->addUsingAlias(BlogPhotoTableMap::COL_PHOTO_ID, $file->getId(), $comparison);
        } elseif ($file instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(BlogPhotoTableMap::COL_PHOTO_ID, $file->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByFile() only accepts arguments of type \Gekosale\Plugin\File\Model\ORM\File or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the File relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildBlogPhotoQuery The current query, for fluid interface
     */
    public function joinFile($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('File');

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
            $this->addJoinObject($join, 'File');
        }

        return $this;
    }

    /**
     * Use the File relation File object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\File\Model\ORM\FileQuery A secondary query class using the current class as primary query
     */
    public function useFileQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinFile($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'File', '\Gekosale\Plugin\File\Model\ORM\FileQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildBlogPhoto $blogPhoto Object to remove from the list of results
     *
     * @return ChildBlogPhotoQuery The current query, for fluid interface
     */
    public function prune($blogPhoto = null)
    {
        if ($blogPhoto) {
            $this->addUsingAlias(BlogPhotoTableMap::COL_ID, $blogPhoto->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the blog_photo table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(BlogPhotoTableMap::DATABASE_NAME);
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
            BlogPhotoTableMap::clearInstancePool();
            BlogPhotoTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildBlogPhoto or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildBlogPhoto object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(BlogPhotoTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(BlogPhotoTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        BlogPhotoTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            BlogPhotoTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // BlogPhotoQuery
