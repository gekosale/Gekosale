<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 */
namespace Gekosale\Core\Behavior\Store;

use Propel\Generator\Model\Column;
use Propel\Generator\Model\PropelTypes;

/**
 * Allows translation of text columns through transparent one-to-many relationship.
 * Modifier for the object builder.
 *
 * @author François Zaninotto
 */
class StoreBehaviorObjectBuilderModifier
{

    protected $behavior;

    protected $table;

    protected $builder;

    public function __construct ($behavior)
    {
        $this->behavior = $behavior;
        $this->table = $behavior->getTable();
    }

    public function postDelete ($builder)
    {
        $this->builder = $builder;
        if (! $builder->getPlatform()->supportsNativeDeleteTrigger() && ! $builder->getBuildProperty('emulateForeignKeyConstraints')) {
            $storeTable = $this->behavior->getStoreTable();
            
            return $this->behavior->renderTemplate('objectPostDelete', array(
                'storeQueryName' => $builder->getClassNameFromBuilder($builder->getNewStubQueryBuilder($storeTable)),
                'objectClassName' => $builder->getNewStubObjectBuilder($this->behavior->getTable())
                    ->getUnqualifiedClassName()
            ));
        }
    }

    public function objectAttributes ($builder)
    {
        return $this->behavior->renderTemplate('objectAttributes', array(
            'defaultStoreId' => $this->behavior->getDefaultStoreId(),
            'objectClassName' => $builder->getClassNameFromBuilder($builder->getNewStubObjectBuilder($this->behavior->getStoreTable()))
        ));
    }

    public function objectClearReferences ($builder)
    {
        return $this->behavior->renderTemplate('objectClearReferences', array(
            'defaultStoreId' => $this->behavior->getDefaultStoreId()
        ));
    }

    public function objectMethods ($builder)
    {
        $this->builder = $builder;
        
        $script = '';
        $script .= $this->addSetStoreId();
        $script .= $this->addGetStoreId();
        
        if ($alias = $this->behavior->getParameter('store_id_alias')) {
            $script .= $this->addGetStoreIdAlias($alias);
            $script .= $this->addSetStoreIdAlias($alias);
        }
        
        $script .= $this->addGetTranslation();
        $script .= $this->addRemoveTranslation();
        $script .= $this->addGetCurrentTranslation();
        
        foreach ($this->behavior->getStoreColumns() as $column) {
            $script .= $this->addTranslatedColumnGetter($column);
            $script .= $this->addTranslatedColumnSetter($column);
        }
        
        return $script;
    }

    protected function addSetStoreId ()
    {
        return $this->behavior->renderTemplate('objectSetStoreId', array(
            'objectClassName' => $this->builder->getClassNameFromBuilder($this->builder->getStubObjectBuilder($this->table)),
            'defaultStoreId' => $this->behavior->getDefaultStoreId(),
            'store_idColumnName' => $this->behavior->getStoreIdColumn()->getPhpName()
        ));
    }

    protected function addGetStoreId ()
    {
        return $this->behavior->renderTemplate('objectGetStoreId', array(
            'store_idColumnName' => $this->behavior->getStoreIdColumn()->getPhpName()
        ));
    }

    protected function addSetStoreIdAlias ($alias)
    {
        return $this->behavior->renderTemplate('objectSetStoreIdAlias', array(
            'objectClassName' => $this->builder->getClassNameFromBuilder($this->builder->getStubObjectBuilder($this->table)),
            'defaultStoreId' => $this->behavior->getDefaultStoreId(),
            'alias' => ucfirst($alias)
        ));
    }

    protected function addGetStoreIdAlias ($alias)
    {
        return $this->behavior->renderTemplate('objectGetStoreIdAlias', array(
            'alias' => ucfirst($alias)
        ));
    }

    protected function addGetTranslation ()
    {
        $plural = false;
        $storeTable = $this->behavior->getStoreTable();
        $fk = $this->behavior->getStoreForeignKey();
        
        return $this->behavior->renderTemplate('objectGetTranslation', array(
            'storeTablePhpName' => $this->builder->getClassNameFromBuilder($this->builder->getNewStubObjectBuilder($storeTable)),
            'defaultStoreId' => $this->behavior->getDefaultStoreId(),
            'storeListVariable' => $this->builder->getRefFKCollVarName($fk),
            'store_idColumnName' => $this->behavior->getStoreIdColumn()->getPhpName(),
            'storeQueryName' => $this->builder->getClassNameFromBuilder($this->builder->getNewStubQueryBuilder($storeTable)),
            'storeSetterMethod' => $this->builder->getRefFKPhpNameAffix($fk, $plural)
        ));
    }

    protected function addRemoveTranslation ()
    {
        $storeTable = $this->behavior->getStoreTable();
        $fk = $this->behavior->getStoreForeignKey();
        
        return $this->behavior->renderTemplate('objectRemoveTranslation', array(
            'objectClassName' => $this->builder->getClassNameFromBuilder($this->builder->getStubObjectBuilder($this->table)),
            'defaultStoreId' => $this->behavior->getDefaultStoreId(),
            'storeQueryName' => $this->builder->getClassNameFromBuilder($this->builder->getNewStubQueryBuilder($storeTable)),
            'storeCollection' => $this->builder->getRefFKCollVarName($fk),
            'store_idColumnName' => $this->behavior->getStoreIdColumn()->getPhpName()
        ));
    }

    protected function addGetCurrentTranslation ()
    {
        return $this->behavior->renderTemplate('objectGetCurrentTranslation', array(
            'storeTablePhpName' => $this->builder->getClassNameFromBuilder($this->builder->getNewStubObjectBuilder($this->behavior->getStoreTable()))
        ));
    }
    
    // FIXME: the connection used by getCurrentTranslation in the generated code
    // cannot be specified by the user
    protected function addTranslatedColumnGetter (Column $column)
    {
        $objectBuilder = $this->builder->getNewObjectBuilder($this->behavior->getStoreTable());
        $comment = '';
        $functionStatement = '';
        if ($this->isDateType($column->getType())) {
            $objectBuilder->addTemporalAccessorComment($comment, $column);
            $objectBuilder->addTemporalAccessorOpen($functionStatement, $column);
        }
        else {
            $objectBuilder->addDefaultAccessorComment($comment, $column);
            $objectBuilder->addDefaultAccessorOpen($functionStatement, $column);
        }
        $comment = preg_replace('/^\t/m', '', $comment);
        $functionStatement = preg_replace('/^\t/m', '', $functionStatement);
        preg_match_all('/\$[a-z]+/i', $functionStatement, $params);
        
        return $this->behavior->renderTemplate('objectTranslatedColumnGetter', array(
            'comment' => $comment,
            'functionStatement' => $functionStatement,
            'columnPhpName' => $column->getPhpName(),
            'params' => implode(', ', $params[0])
        ));
    }
    
    // FIXME: the connection used by getCurrentTranslation in the generated code
    // cannot be specified by the user
    protected function addTranslatedColumnSetter (Column $column)
    {
        $storeTablePhpName = $this->builder->getClassNameFromBuilder($this->builder->getNewStubObjectBuilder($this->behavior->getStoreTable()));
        $tablePhpName = $this->builder->getObjectClassName();
        $objectBuilder = $this->builder->getNewObjectBuilder($this->behavior->getStoreTable());
        $comment = '';
        $functionStatement = '';
        if ($this->isDateType($column->getType())) {
            $objectBuilder->addTemporalMutatorComment($comment, $column);
            $objectBuilder->addMutatorOpenOpen($functionStatement, $column);
        }
        else {
            $objectBuilder->addMutatorComment($comment, $column);
            $objectBuilder->addMutatorOpenOpen($functionStatement, $column);
        }
        $comment = preg_replace('/^\t/m', '', $comment);
        $comment = str_replace('@return     ' . $storeTablePhpName, '@return     ' . $tablePhpName, $comment);
        $functionStatement = preg_replace('/^\t/m', '', $functionStatement);
        preg_match_all('/\$[a-z]+/i', $functionStatement, $params);
        
        return $this->behavior->renderTemplate('objectTranslatedColumnSetter', array(
            'comment' => $comment,
            'functionStatement' => $functionStatement,
            'columnPhpName' => $column->getPhpName(),
            'params' => implode(', ', $params[0])
        ));
    }

    public function objectFilter (&$script, $builder)
    {
        $storeTable = $this->behavior->getStoreTable();
        $storeTablePhpName = $this->builder->getNewStubObjectBuilder($storeTable)->getUnprefixedClassName();
        $store_idColumnName = $this->behavior->getStoreIdColumn()->getPhpName();
        $pattern = '/public function add' . $storeTablePhpName . '.*[\r\n]\s*\{/';
        $addition = "
        if (\$l && \$store_id = \$l->get$store_idColumnName()) {
            \$this->set{$store_idColumnName}(\$store_id);
            \$this->currentTranslations[\$store_id] = \$l;
        }";
        $replacement = "\$0$addition";
        $script = preg_replace($pattern, $replacement, $script);
    }

    protected function isDateType ($columnType)
    {
        return in_array($columnType, array(
            PropelTypes::DATE,
            PropelTypes::TIME,
            PropelTypes::TIMESTAMP
        ));
    }
}
