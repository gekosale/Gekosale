<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT License
 */

namespace Gekosale\Core\Behavior\Store;

use Propel\Generator\Exception\EngineException;
use Propel\Generator\Model\Behavior;
use Propel\Generator\Model\ForeignKey;
use Propel\Generator\Model\PropelTypes;
use Propel\Generator\Behavior\Validate\ValidateBehavior;

/**
 * Allows translation of text columns through transparent one-to-many
 * relationship.
 *
 * @author Francois Zaninotto
 */
class StoreBehavior extends Behavior
{
    const DEFAULT_LOCALE = 'en_US';

    // default parameters value
    protected $parameters = array(
        'store_table'        => '%TABLE%_store',
        'store_phpname'      => '%PHPNAME%Store',
        'store_columns'      => '',
        'store_id_column'     => 'store_id',
        'store_id_length'     => 5,
        'default_store_id'    => null,
        'store_id_alias'      => '',
    );

    protected $tableModificationOrder = 80;

    protected $objectBuilderModifier;

    protected $queryBuilderModifier;

    protected $storeTable;

    public function modifyDatabase()
    {
        foreach ($this->getDatabase()->getTables() as $table) {
            if ($table->hasBehavior('store') && !$table->getBehavior('store')->getParameter('default_store_id')) {
                $table->getBehavior('store')->addParameter(array(
                    'name'  => 'default_store_id',
                    'value' => $this->getParameter('default_store_id'),
                ));
            }
        }
    }

    public function getDefaultStoreId()
    {
        if (!$defaultStoreId = $this->getParameter('default_store_id')) {
            $defaultStoreId = self::DEFAULT_LOCALE;
        }

        return $defaultStoreId;
    }

    public function getStoreTable()
    {
        return $this->storeTable;
    }

    public function getStoreForeignKey()
    {
        foreach ($this->storeTable->getForeignKeys() as $fk) {
            if ($fk->getForeignTableName() == $this->table->getName()) {
                return $fk;
            }
        }
    }

    public function getStoreIdColumn()
    {
        return $this->getStoreTable()->getColumn($this->getStoreIdColumnName());
    }

    public function getStoreColumns()
    {
        $columns = array();
        $storeTable = $this->getStoreTable();
        if ($columnNames = $this->getStoreColumnNamesFromConfig()) {
            // Strategy 1: use the store_columns parameter
            foreach ($columnNames as $columnName) {
                $columns []= $storeTable->getColumn($columnName);
            }
        } else {
            // strategy 2: use the columns of the store table
            // warning: does not work when database behaviors add columns to all tables
            // (such as timestampable behavior)
            foreach ($storeTable->getColumns() as $column) {
                if (!$column->isPrimaryKey()) {
                    $columns []= $column;
                }
            }
        }

        return $columns;
    }

    public function replaceTokens($string)
    {
        $table = $this->getTable();

        return strtr($string, array(
            '%TABLE%'   => $table->getName(),
            '%PHPNAME%' => $table->getPhpName(),
        ));
    }

    public function getObjectBuilderModifier()
    {
        if (null === $this->objectBuilderModifier) {
            $this->objectBuilderModifier = new StoreBehaviorObjectBuilderModifier($this);
        }

        return $this->objectBuilderModifier;
    }

    public function getQueryBuilderModifier()
    {
        if (null === $this->queryBuilderModifier) {
            $this->queryBuilderModifier = new StoreBehaviorQueryBuilderModifier($this);
        }

        return $this->queryBuilderModifier;
    }

    public function staticAttributes($builder)
    {
        return $this->renderTemplate('staticAttributes', array(
            'defaultStoreId' => $this->getDefaultStoreId(),
        ));
    }

    public function modifyTable()
    {
        $this->addStoreTable();
        $this->relateStoreTableToMainTable();
        $this->addStoreIdColumnToStore();
        $this->moveStoreColumns();
    }

    protected function addStoreTable()
    {
        $table         = $this->getTable();
        $database      = $table->getDatabase();
        $storeTableName = $this->getStoreTableName();

        if ($database->hasTable($storeTableName)) {
            $this->storeTable = $database->getTable($storeTableName);
        } else {
            $this->storeTable = $database->addTable(array(
                'name'      => $storeTableName,
                'phpName'   => $this->getStoreTablePhpName(),
                'package'   => $table->getPackage(),
                'schema'    => $table->getSchema(),
                'namespace' => $table->getNamespace() ? '\\' . $table->getNamespace() : null,
                'skipSql'   => $table->isSkipSql()
            ));

            // every behavior adding a table should re-execute database behaviors
            foreach ($database->getBehaviors() as $behavior) {
                $behavior->modifyDatabase();
            }
        }
    }

    protected function relateStoreTableToMainTable()
    {
        $table     = $this->getTable();
        $storeTable = $this->storeTable;
        $pks       = $this->getTable()->getPrimaryKey();

        if (count($pks) > 1) {
            throw new EngineException('The store behavior does not support tables with composite primary keys');
        }

        foreach ($pks as $column) {
            if (!$storeTable->hasColumn($column->getName())) {
                $column = clone $column;
                $column->setAutoIncrement(false);
                $storeTable->addColumn($column);
            }
        }

        if (in_array($table->getName(), $storeTable->getForeignTableNames())) {
            return;
        }

        $fk = new ForeignKey();
        $fk->setForeignTableCommonName($table->getCommonName());
        $fk->setForeignSchemaName($table->getSchema());
        $fk->setDefaultJoin('LEFT JOIN');
        $fk->setOnDelete(ForeignKey::CASCADE);
        $fk->setOnUpdate(ForeignKey::NONE);

        foreach ($pks as $column) {
            $fk->addReference($column->getName(), $column->getName());
        }

        $storeTable->addForeignKey($fk);
    }

    protected function addStoreIdColumnToStore()
    {
        $store_idColumnName = $this->getStoreIdColumnName();

        if (! $this->storeTable->hasColumn($store_idColumnName)) {
            $this->storeTable->addColumn(array(
                'name'       => $store_idColumnName,
                'type'       => PropelTypes::VARCHAR,
                'size'       => $this->getParameter('store_id_length') ? (int) $this->getParameter('store_id_length') : 5,
                'default'    => $this->getDefaultStoreId(),
                'primaryKey' => 'true',
            ));
        }
    }

    /**
     * Moves store columns from the main table to the store table
     */
    protected function moveStoreColumns()
    {
        $table     = $this->getTable();
        $storeTable = $this->storeTable;

        $storeValidateParams = array();
        foreach ($this->getStoreColumnNamesFromConfig() as $columnName) {
            if (!$storeTable->hasColumn($columnName)) {
                if (!$table->hasColumn($columnName)) {
                    throw new EngineException(sprintf('No column named %s found in table %s', $columnName, $table->getName()));
                }

                $column = $table->getColumn($columnName);
                $storeTable->addColumn(clone $column);

                // validate behavior: move rules associated to the column
                if ($table->hasBehavior('validate')) {
                    $validateBehavior = $table->getBehavior('validate');
                    $params = $validateBehavior->getParametersFromColumnName($columnName);
                    $storeValidateParams = array_merge($storeValidateParams, $params);
                    $validateBehavior->removeParametersFromColumnName($columnName);
                }
                // FIXME: also move FKs, and indices on this column
            }

            if ($table->hasColumn($columnName)) {
                $table->removeColumn($columnName);
            }
        }

        // validate behavior
        if (count($storeValidateParams) > 0) {
            $storeVbehavior = new ValidateBehavior();
            $storeVbehavior->setName('validate');
            $storeVbehavior->setParameters($storeValidateParams);
            $storeTable->addBehavior($storeVbehavior);

            // current table must have almost 1 validation rule
            $validate = $table->getBehavior('validate');
            $validate->addRuleOnPk();
        }
    }

    protected function getStoreTableName()
    {
        return $this->replaceTokens($this->getParameter('store_table'));
    }

    protected function getStoreTablePhpName()
    {
        return $this->replaceTokens($this->getParameter('store_phpname'));
    }

    protected function getStoreIdColumnName()
    {
        return $this->replaceTokens($this->getParameter('store_id_column'));
    }

    protected function getStoreColumnNamesFromConfig()
    {
        $columnNames = explode(',', $this->getParameter('store_columns'));
        foreach ($columnNames as $key => $columnName) {
            if ($columnName = trim($columnName)) {
                $columnNames[$key] = $columnName;
            } else {
                unset($columnNames[$key]);
            }
        }

        return $columnNames;
    }
}
