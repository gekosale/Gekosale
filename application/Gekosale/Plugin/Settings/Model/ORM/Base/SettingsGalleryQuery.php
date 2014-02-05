<?php

namespace Gekosale\Plugin\Settings\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Settings\Model\ORM\SettingsGallery as ChildSettingsGallery;
use Gekosale\Plugin\Settings\Model\ORM\SettingsGalleryQuery as ChildSettingsGalleryQuery;
use Gekosale\Plugin\Settings\Model\ORM\Map\SettingsGalleryTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'settings_gallery' table.
 *
 * 
 *
 * @method     ChildSettingsGalleryQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildSettingsGalleryQuery orderByWidth($order = Criteria::ASC) Order by the width column
 * @method     ChildSettingsGalleryQuery orderByHeight($order = Criteria::ASC) Order by the height column
 * @method     ChildSettingsGalleryQuery orderByMethod($order = Criteria::ASC) Order by the method column
 * @method     ChildSettingsGalleryQuery orderByKeepProportion($order = Criteria::ASC) Order by the keep_proportion column
 * @method     ChildSettingsGalleryQuery orderByStaticPath($order = Criteria::ASC) Order by the static_path column
 *
 * @method     ChildSettingsGalleryQuery groupById() Group by the id column
 * @method     ChildSettingsGalleryQuery groupByWidth() Group by the width column
 * @method     ChildSettingsGalleryQuery groupByHeight() Group by the height column
 * @method     ChildSettingsGalleryQuery groupByMethod() Group by the method column
 * @method     ChildSettingsGalleryQuery groupByKeepProportion() Group by the keep_proportion column
 * @method     ChildSettingsGalleryQuery groupByStaticPath() Group by the static_path column
 *
 * @method     ChildSettingsGalleryQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSettingsGalleryQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSettingsGalleryQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSettingsGallery findOne(ConnectionInterface $con = null) Return the first ChildSettingsGallery matching the query
 * @method     ChildSettingsGallery findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSettingsGallery matching the query, or a new ChildSettingsGallery object populated from the query conditions when no match is found
 *
 * @method     ChildSettingsGallery findOneById(int $id) Return the first ChildSettingsGallery filtered by the id column
 * @method     ChildSettingsGallery findOneByWidth(int $width) Return the first ChildSettingsGallery filtered by the width column
 * @method     ChildSettingsGallery findOneByHeight(int $height) Return the first ChildSettingsGallery filtered by the height column
 * @method     ChildSettingsGallery findOneByMethod(string $method) Return the first ChildSettingsGallery filtered by the method column
 * @method     ChildSettingsGallery findOneByKeepProportion(int $keep_proportion) Return the first ChildSettingsGallery filtered by the keep_proportion column
 * @method     ChildSettingsGallery findOneByStaticPath(string $static_path) Return the first ChildSettingsGallery filtered by the static_path column
 *
 * @method     array findById(int $id) Return ChildSettingsGallery objects filtered by the id column
 * @method     array findByWidth(int $width) Return ChildSettingsGallery objects filtered by the width column
 * @method     array findByHeight(int $height) Return ChildSettingsGallery objects filtered by the height column
 * @method     array findByMethod(string $method) Return ChildSettingsGallery objects filtered by the method column
 * @method     array findByKeepProportion(int $keep_proportion) Return ChildSettingsGallery objects filtered by the keep_proportion column
 * @method     array findByStaticPath(string $static_path) Return ChildSettingsGallery objects filtered by the static_path column
 *
 */
abstract class SettingsGalleryQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Settings\Model\ORM\Base\SettingsGalleryQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Settings\\Model\\ORM\\SettingsGallery', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildSettingsGalleryQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildSettingsGalleryQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Settings\Model\ORM\SettingsGalleryQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Settings\Model\ORM\SettingsGalleryQuery();
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
     * @return ChildSettingsGallery|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SettingsGalleryTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SettingsGalleryTableMap::DATABASE_NAME);
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
     * @return   ChildSettingsGallery A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, WIDTH, HEIGHT, METHOD, KEEP_PROPORTION, STATIC_PATH FROM settings_gallery WHERE ID = :p0';
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
            $obj = new ChildSettingsGallery();
            $obj->hydrate($row);
            SettingsGalleryTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildSettingsGallery|array|mixed the result, formatted by the current formatter
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
     * @return ChildSettingsGalleryQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SettingsGalleryTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildSettingsGalleryQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SettingsGalleryTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildSettingsGalleryQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(SettingsGalleryTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(SettingsGalleryTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SettingsGalleryTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the width column
     *
     * Example usage:
     * <code>
     * $query->filterByWidth(1234); // WHERE width = 1234
     * $query->filterByWidth(array(12, 34)); // WHERE width IN (12, 34)
     * $query->filterByWidth(array('min' => 12)); // WHERE width > 12
     * </code>
     *
     * @param     mixed $width The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSettingsGalleryQuery The current query, for fluid interface
     */
    public function filterByWidth($width = null, $comparison = null)
    {
        if (is_array($width)) {
            $useMinMax = false;
            if (isset($width['min'])) {
                $this->addUsingAlias(SettingsGalleryTableMap::COL_WIDTH, $width['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($width['max'])) {
                $this->addUsingAlias(SettingsGalleryTableMap::COL_WIDTH, $width['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SettingsGalleryTableMap::COL_WIDTH, $width, $comparison);
    }

    /**
     * Filter the query on the height column
     *
     * Example usage:
     * <code>
     * $query->filterByHeight(1234); // WHERE height = 1234
     * $query->filterByHeight(array(12, 34)); // WHERE height IN (12, 34)
     * $query->filterByHeight(array('min' => 12)); // WHERE height > 12
     * </code>
     *
     * @param     mixed $height The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSettingsGalleryQuery The current query, for fluid interface
     */
    public function filterByHeight($height = null, $comparison = null)
    {
        if (is_array($height)) {
            $useMinMax = false;
            if (isset($height['min'])) {
                $this->addUsingAlias(SettingsGalleryTableMap::COL_HEIGHT, $height['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($height['max'])) {
                $this->addUsingAlias(SettingsGalleryTableMap::COL_HEIGHT, $height['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SettingsGalleryTableMap::COL_HEIGHT, $height, $comparison);
    }

    /**
     * Filter the query on the method column
     *
     * Example usage:
     * <code>
     * $query->filterByMethod('fooValue');   // WHERE method = 'fooValue'
     * $query->filterByMethod('%fooValue%'); // WHERE method LIKE '%fooValue%'
     * </code>
     *
     * @param     string $method The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSettingsGalleryQuery The current query, for fluid interface
     */
    public function filterByMethod($method = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($method)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $method)) {
                $method = str_replace('*', '%', $method);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SettingsGalleryTableMap::COL_METHOD, $method, $comparison);
    }

    /**
     * Filter the query on the keep_proportion column
     *
     * Example usage:
     * <code>
     * $query->filterByKeepProportion(1234); // WHERE keep_proportion = 1234
     * $query->filterByKeepProportion(array(12, 34)); // WHERE keep_proportion IN (12, 34)
     * $query->filterByKeepProportion(array('min' => 12)); // WHERE keep_proportion > 12
     * </code>
     *
     * @param     mixed $keepProportion The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSettingsGalleryQuery The current query, for fluid interface
     */
    public function filterByKeepProportion($keepProportion = null, $comparison = null)
    {
        if (is_array($keepProportion)) {
            $useMinMax = false;
            if (isset($keepProportion['min'])) {
                $this->addUsingAlias(SettingsGalleryTableMap::COL_KEEP_PROPORTION, $keepProportion['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($keepProportion['max'])) {
                $this->addUsingAlias(SettingsGalleryTableMap::COL_KEEP_PROPORTION, $keepProportion['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SettingsGalleryTableMap::COL_KEEP_PROPORTION, $keepProportion, $comparison);
    }

    /**
     * Filter the query on the static_path column
     *
     * Example usage:
     * <code>
     * $query->filterByStaticPath('fooValue');   // WHERE static_path = 'fooValue'
     * $query->filterByStaticPath('%fooValue%'); // WHERE static_path LIKE '%fooValue%'
     * </code>
     *
     * @param     string $staticPath The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildSettingsGalleryQuery The current query, for fluid interface
     */
    public function filterByStaticPath($staticPath = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($staticPath)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $staticPath)) {
                $staticPath = str_replace('*', '%', $staticPath);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SettingsGalleryTableMap::COL_STATIC_PATH, $staticPath, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildSettingsGallery $settingsGallery Object to remove from the list of results
     *
     * @return ChildSettingsGalleryQuery The current query, for fluid interface
     */
    public function prune($settingsGallery = null)
    {
        if ($settingsGallery) {
            $this->addUsingAlias(SettingsGalleryTableMap::COL_ID, $settingsGallery->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the settings_gallery table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SettingsGalleryTableMap::DATABASE_NAME);
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
            SettingsGalleryTableMap::clearInstancePool();
            SettingsGalleryTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildSettingsGallery or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildSettingsGallery object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(SettingsGalleryTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(SettingsGalleryTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        SettingsGalleryTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            SettingsGalleryTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // SettingsGalleryQuery
