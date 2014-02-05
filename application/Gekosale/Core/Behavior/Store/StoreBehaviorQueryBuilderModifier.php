<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 */

namespace Gekosale\Core\Behavior\Store;

/**
 * Allows translation of text columns through transparent one-to-many relationship.
 * Modifier for the query builder.
 *
 * @author François Zaninotto
 */
class StoreBehaviorQueryBuilderModifier
{
    protected $behavior;

    protected $table;

    protected $builder;

    public function __construct($behavior)
    {
        $this->behavior = $behavior;
        $this->table    = $behavior->getTable();
    }

    public function queryMethods($builder)
    {
        $this->builder = $builder;
        $script = '';
        $script .= $this->addJoinstore();
        $script .= $this->addJoinWithstore();
        $script .= $this->addUsestoreQuery();

        return $script;
    }

    protected function addJoinstore()
    {
        $fk = $this->behavior->getstoreForeignKey();

        return $this->behavior->renderTemplate('queryJoinstore', array(
            'queryClass'       => $this->builder->getQueryClassName(),
            'defaultstore_id'    => $this->behavior->getDefaultstore_id(),
            'storeRelationName' => $this->builder->getRefFKPhpNameAffix($fk),
            'store_idColumn'     => $this->behavior->getstore_idColumn()->getPhpName(),
        ));
    }

    protected function addJoinWithstore()
    {
        $fk = $this->behavior->getstoreForeignKey();

        return $this->behavior->renderTemplate('queryJoinWithstore', array(
            'queryClass'       => $this->builder->getQueryClassName(),
            'defaultstore_id'    => $this->behavior->getDefaultstore_id(),
            'storeRelationName' => $this->builder->getRefFKPhpNameAffix($fk),
        ));
    }

    protected function addUsestoreQuery()
    {
        $storeTable = $this->behavior->getstoreTable();
        $fk = $this->behavior->getstoreForeignKey();

        return $this->behavior->renderTemplate('queryUsestoreQuery', array(
            'queryClass'           => $this->builder->getClassNameFromBuilder($this->builder->getNewStubQueryBuilder($storeTable)),
            'namespacedQueryClass' => $this->builder->getNewStubQueryBuilder($storeTable)->getFullyQualifiedClassName(),
            'defaultstore_id'        => $this->behavior->getDefaultstore_id(),
            'storeRelationName'     => $this->builder->getRefFKPhpNameAffix($fk),
            'store_idColumn'         => $this->behavior->getstore_idColumn()->getPhpName(),
        ));
    }
}
