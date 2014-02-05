<?php

namespace Gekosale\Plugin\Attribute\Model\ORM\Base;

use \Exception;
use \PDO;
use Gekosale\Plugin\Attribute\Model\ORM\CategoryAttributeProduct as ChildCategoryAttributeProduct;
use Gekosale\Plugin\Attribute\Model\ORM\CategoryAttributeProductQuery as ChildCategoryAttributeProductQuery;
use Gekosale\Plugin\Attribute\Model\ORM\Map\CategoryAttributeProductTableMap;
use Gekosale\Plugin\Category\Model\ORM\Category;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'category_attribute_product' table.
 *
 * 
 *
 * @method     ChildCategoryAttributeProductQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildCategoryAttributeProductQuery orderByCategoryId($order = Criteria::ASC) Order by the category_id column
 * @method     ChildCategoryAttributeProductQuery orderByAttributeProductId($order = Criteria::ASC) Order by the attribute_product_id column
 * @method     ChildCategoryAttributeProductQuery orderByAttributeGroupNameId($order = Criteria::ASC) Order by the attribute_group_name_id column
 *
 * @method     ChildCategoryAttributeProductQuery groupById() Group by the id column
 * @method     ChildCategoryAttributeProductQuery groupByCategoryId() Group by the category_id column
 * @method     ChildCategoryAttributeProductQuery groupByAttributeProductId() Group by the attribute_product_id column
 * @method     ChildCategoryAttributeProductQuery groupByAttributeGroupNameId() Group by the attribute_group_name_id column
 *
 * @method     ChildCategoryAttributeProductQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildCategoryAttributeProductQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildCategoryAttributeProductQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildCategoryAttributeProductQuery leftJoinAttributeGroupName($relationAlias = null) Adds a LEFT JOIN clause to the query using the AttributeGroupName relation
 * @method     ChildCategoryAttributeProductQuery rightJoinAttributeGroupName($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AttributeGroupName relation
 * @method     ChildCategoryAttributeProductQuery innerJoinAttributeGroupName($relationAlias = null) Adds a INNER JOIN clause to the query using the AttributeGroupName relation
 *
 * @method     ChildCategoryAttributeProductQuery leftJoinAttributeProduct($relationAlias = null) Adds a LEFT JOIN clause to the query using the AttributeProduct relation
 * @method     ChildCategoryAttributeProductQuery rightJoinAttributeProduct($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AttributeProduct relation
 * @method     ChildCategoryAttributeProductQuery innerJoinAttributeProduct($relationAlias = null) Adds a INNER JOIN clause to the query using the AttributeProduct relation
 *
 * @method     ChildCategoryAttributeProductQuery leftJoinCategory($relationAlias = null) Adds a LEFT JOIN clause to the query using the Category relation
 * @method     ChildCategoryAttributeProductQuery rightJoinCategory($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Category relation
 * @method     ChildCategoryAttributeProductQuery innerJoinCategory($relationAlias = null) Adds a INNER JOIN clause to the query using the Category relation
 *
 * @method     ChildCategoryAttributeProduct findOne(ConnectionInterface $con = null) Return the first ChildCategoryAttributeProduct matching the query
 * @method     ChildCategoryAttributeProduct findOneOrCreate(ConnectionInterface $con = null) Return the first ChildCategoryAttributeProduct matching the query, or a new ChildCategoryAttributeProduct object populated from the query conditions when no match is found
 *
 * @method     ChildCategoryAttributeProduct findOneById(int $id) Return the first ChildCategoryAttributeProduct filtered by the id column
 * @method     ChildCategoryAttributeProduct findOneByCategoryId(int $category_id) Return the first ChildCategoryAttributeProduct filtered by the category_id column
 * @method     ChildCategoryAttributeProduct findOneByAttributeProductId(int $attribute_product_id) Return the first ChildCategoryAttributeProduct filtered by the attribute_product_id column
 * @method     ChildCategoryAttributeProduct findOneByAttributeGroupNameId(int $attribute_group_name_id) Return the first ChildCategoryAttributeProduct filtered by the attribute_group_name_id column
 *
 * @method     array findById(int $id) Return ChildCategoryAttributeProduct objects filtered by the id column
 * @method     array findByCategoryId(int $category_id) Return ChildCategoryAttributeProduct objects filtered by the category_id column
 * @method     array findByAttributeProductId(int $attribute_product_id) Return ChildCategoryAttributeProduct objects filtered by the attribute_product_id column
 * @method     array findByAttributeGroupNameId(int $attribute_group_name_id) Return ChildCategoryAttributeProduct objects filtered by the attribute_group_name_id column
 *
 */
abstract class CategoryAttributeProductQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of \Gekosale\Plugin\Attribute\Model\ORM\Base\CategoryAttributeProductQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Gekosale\\Plugin\\Attribute\\Model\\ORM\\CategoryAttributeProduct', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildCategoryAttributeProductQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildCategoryAttributeProductQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Gekosale\Plugin\Attribute\Model\ORM\CategoryAttributeProductQuery) {
            return $criteria;
        }
        $query = new \Gekosale\Plugin\Attribute\Model\ORM\CategoryAttributeProductQuery();
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
     * @return ChildCategoryAttributeProduct|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CategoryAttributeProductTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(CategoryAttributeProductTableMap::DATABASE_NAME);
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
     * @return   ChildCategoryAttributeProduct A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, CATEGORY_ID, ATTRIBUTE_PRODUCT_ID, ATTRIBUTE_GROUP_NAME_ID FROM category_attribute_product WHERE ID = :p0';
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
            $obj = new ChildCategoryAttributeProduct();
            $obj->hydrate($row);
            CategoryAttributeProductTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildCategoryAttributeProduct|array|mixed the result, formatted by the current formatter
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
     * @return ChildCategoryAttributeProductQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CategoryAttributeProductTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildCategoryAttributeProductQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CategoryAttributeProductTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return ChildCategoryAttributeProductQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CategoryAttributeProductTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CategoryAttributeProductTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CategoryAttributeProductTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the category_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCategoryId(1234); // WHERE category_id = 1234
     * $query->filterByCategoryId(array(12, 34)); // WHERE category_id IN (12, 34)
     * $query->filterByCategoryId(array('min' => 12)); // WHERE category_id > 12
     * </code>
     *
     * @see       filterByCategory()
     *
     * @param     mixed $categoryId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCategoryAttributeProductQuery The current query, for fluid interface
     */
    public function filterByCategoryId($categoryId = null, $comparison = null)
    {
        if (is_array($categoryId)) {
            $useMinMax = false;
            if (isset($categoryId['min'])) {
                $this->addUsingAlias(CategoryAttributeProductTableMap::COL_CATEGORY_ID, $categoryId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($categoryId['max'])) {
                $this->addUsingAlias(CategoryAttributeProductTableMap::COL_CATEGORY_ID, $categoryId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CategoryAttributeProductTableMap::COL_CATEGORY_ID, $categoryId, $comparison);
    }

    /**
     * Filter the query on the attribute_product_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAttributeProductId(1234); // WHERE attribute_product_id = 1234
     * $query->filterByAttributeProductId(array(12, 34)); // WHERE attribute_product_id IN (12, 34)
     * $query->filterByAttributeProductId(array('min' => 12)); // WHERE attribute_product_id > 12
     * </code>
     *
     * @see       filterByAttributeProduct()
     *
     * @param     mixed $attributeProductId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCategoryAttributeProductQuery The current query, for fluid interface
     */
    public function filterByAttributeProductId($attributeProductId = null, $comparison = null)
    {
        if (is_array($attributeProductId)) {
            $useMinMax = false;
            if (isset($attributeProductId['min'])) {
                $this->addUsingAlias(CategoryAttributeProductTableMap::COL_ATTRIBUTE_PRODUCT_ID, $attributeProductId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($attributeProductId['max'])) {
                $this->addUsingAlias(CategoryAttributeProductTableMap::COL_ATTRIBUTE_PRODUCT_ID, $attributeProductId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CategoryAttributeProductTableMap::COL_ATTRIBUTE_PRODUCT_ID, $attributeProductId, $comparison);
    }

    /**
     * Filter the query on the attribute_group_name_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAttributeGroupNameId(1234); // WHERE attribute_group_name_id = 1234
     * $query->filterByAttributeGroupNameId(array(12, 34)); // WHERE attribute_group_name_id IN (12, 34)
     * $query->filterByAttributeGroupNameId(array('min' => 12)); // WHERE attribute_group_name_id > 12
     * </code>
     *
     * @see       filterByAttributeGroupName()
     *
     * @param     mixed $attributeGroupNameId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCategoryAttributeProductQuery The current query, for fluid interface
     */
    public function filterByAttributeGroupNameId($attributeGroupNameId = null, $comparison = null)
    {
        if (is_array($attributeGroupNameId)) {
            $useMinMax = false;
            if (isset($attributeGroupNameId['min'])) {
                $this->addUsingAlias(CategoryAttributeProductTableMap::COL_ATTRIBUTE_GROUP_NAME_ID, $attributeGroupNameId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($attributeGroupNameId['max'])) {
                $this->addUsingAlias(CategoryAttributeProductTableMap::COL_ATTRIBUTE_GROUP_NAME_ID, $attributeGroupNameId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CategoryAttributeProductTableMap::COL_ATTRIBUTE_GROUP_NAME_ID, $attributeGroupNameId, $comparison);
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupName object
     *
     * @param \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupName|ObjectCollection $attributeGroupName The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCategoryAttributeProductQuery The current query, for fluid interface
     */
    public function filterByAttributeGroupName($attributeGroupName, $comparison = null)
    {
        if ($attributeGroupName instanceof \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupName) {
            return $this
                ->addUsingAlias(CategoryAttributeProductTableMap::COL_ATTRIBUTE_GROUP_NAME_ID, $attributeGroupName->getId(), $comparison);
        } elseif ($attributeGroupName instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CategoryAttributeProductTableMap::COL_ATTRIBUTE_GROUP_NAME_ID, $attributeGroupName->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByAttributeGroupName() only accepts arguments of type \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupName or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AttributeGroupName relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCategoryAttributeProductQuery The current query, for fluid interface
     */
    public function joinAttributeGroupName($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AttributeGroupName');

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
            $this->addJoinObject($join, 'AttributeGroupName');
        }

        return $this;
    }

    /**
     * Use the AttributeGroupName relation AttributeGroupName object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupNameQuery A secondary query class using the current class as primary query
     */
    public function useAttributeGroupNameQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAttributeGroupName($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AttributeGroupName', '\Gekosale\Plugin\Attribute\Model\ORM\AttributeGroupNameQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Attribute\Model\ORM\AttributeProduct object
     *
     * @param \Gekosale\Plugin\Attribute\Model\ORM\AttributeProduct|ObjectCollection $attributeProduct The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCategoryAttributeProductQuery The current query, for fluid interface
     */
    public function filterByAttributeProduct($attributeProduct, $comparison = null)
    {
        if ($attributeProduct instanceof \Gekosale\Plugin\Attribute\Model\ORM\AttributeProduct) {
            return $this
                ->addUsingAlias(CategoryAttributeProductTableMap::COL_ATTRIBUTE_PRODUCT_ID, $attributeProduct->getId(), $comparison);
        } elseif ($attributeProduct instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CategoryAttributeProductTableMap::COL_ATTRIBUTE_PRODUCT_ID, $attributeProduct->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByAttributeProduct() only accepts arguments of type \Gekosale\Plugin\Attribute\Model\ORM\AttributeProduct or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AttributeProduct relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildCategoryAttributeProductQuery The current query, for fluid interface
     */
    public function joinAttributeProduct($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AttributeProduct');

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
            $this->addJoinObject($join, 'AttributeProduct');
        }

        return $this;
    }

    /**
     * Use the AttributeProduct relation AttributeProduct object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Gekosale\Plugin\Attribute\Model\ORM\AttributeProductQuery A secondary query class using the current class as primary query
     */
    public function useAttributeProductQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAttributeProduct($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AttributeProduct', '\Gekosale\Plugin\Attribute\Model\ORM\AttributeProductQuery');
    }

    /**
     * Filter the query by a related \Gekosale\Plugin\Category\Model\ORM\Category object
     *
     * @param \Gekosale\Plugin\Category\Model\ORM\Category|ObjectCollection $category The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildCategoryAttributeProductQuery The current query, for fluid interface
     */
    public function filterByCategory($category, $comparison = null)
    {
        if ($category instanceof \Gekosale\Plugin\Category\Model\ORM\Category) {
            return $this
                ->addUsingAlias(CategoryAttributeProductTableMap::COL_CATEGORY_ID, $category->getId(), $comparison);
        } elseif ($category instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(CategoryAttributeProductTableMap::COL_CATEGORY_ID, $category->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildCategoryAttributeProductQuery The current query, for fluid interface
     */
    public function joinCategory($relationAlias = null, $joinType = Criteria::INNER_JOIN)
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
    public function useCategoryQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCategory($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Category', '\Gekosale\Plugin\Category\Model\ORM\CategoryQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildCategoryAttributeProduct $categoryAttributeProduct Object to remove from the list of results
     *
     * @return ChildCategoryAttributeProductQuery The current query, for fluid interface
     */
    public function prune($categoryAttributeProduct = null)
    {
        if ($categoryAttributeProduct) {
            $this->addUsingAlias(CategoryAttributeProductTableMap::COL_ID, $categoryAttributeProduct->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the category_attribute_product table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(CategoryAttributeProductTableMap::DATABASE_NAME);
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
            CategoryAttributeProductTableMap::clearInstancePool();
            CategoryAttributeProductTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildCategoryAttributeProduct or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildCategoryAttributeProduct object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(CategoryAttributeProductTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(CategoryAttributeProductTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            

        CategoryAttributeProductTableMap::removeInstanceFromPool($criteria);
        
            $affectedRows += ModelCriteria::delete($con);
            CategoryAttributeProductTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // CategoryAttributeProductQuery
