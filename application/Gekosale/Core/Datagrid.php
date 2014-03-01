<?php
/*
 * Gekosale Open-Source E-Commerce Platform
 *
 * This file is part of the Gekosale package.
 *
 * (c) Adam Piotrowski <adam@gekosale.com>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */
namespace Gekosale\Core;

use Gekosale\Core\Repository;
use xajaxResponse;
use Exception;

/**
 * Class DataGrid
 *
 * @package Gekosale\Core
 * @author  Adam Piotrowski
 */
class DataGrid extends Component
{

    protected $datagrid;

    protected $db;

    protected $columns;

    protected $columnsOptions;

    protected $queryFrom;

    protected $queryGroupBy;

    protected $queryAdditionalWhere;

    protected $encryptionKey;

    protected $languageId;

    protected $sqlParams = Array();

    protected $viewId;

    protected $viewIds;

    protected $autosuggests;

    protected $warnings;

    protected $container;

    protected $repository;

    public function getFilterData()
    {
        $filters = Array();
        foreach ($this->queryColumnsOptions as $name => $options) {
            if (isset($options['prepareForSelect']) && $options['prepareForSelect']) {
                $possibilities = Array(
                    "{id: '', caption: ''}"
                );
                $sql           = 'SELECT DISTINCT ';
                if (isset($options['source'])) {
                    $sql .= $options['source'];
                } else {
                    $sql .= $name;
                }
                $sql .= ' AS possibility FROM ' . $this->queryFrom . ' ORDER BY possibility';
                $stmt = $this->db->prepare($sql);

                foreach ($this->sqlParams as $key => $val) {

                    if (is_array($val)) {
                        $stmt->bindValue($key, implode(',', $val));
                    } else {
                        $stmt->bindValue($key, $val);
                    }
                }

                $stmt->execute();
                while ($rs = $stmt->fetch()) {
                    $caption = addslashes($rs['possibility']);
                    if (isset($options['processLanguage']) && $options['processLanguage']) {
                        $caption = addslashes(_($caption));
                    }
                    $id              = addslashes($rs['possibility']);
                    $possibilities[] = "{id: '{$id}', caption: '{$caption}'}";
                }
                $filters[$name] = implode(', ', $possibilities);
            } else {
                if (isset($options['prepareForTree']) && $options['prepareForTree']) {
                    $filters[$name] = json_encode($options['first_level']);
                }
            }
        }

        return $filters;
    }

    /**
     * @param $datagrid
     * @param $id
     *
     * @return xajaxResponse
     */
    public function delete($datagrid, $id)
    {
        return $this->deleteRow($datagrid, $id, [$this->repository, 'delete']);
    }

    public function setRepository(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function getRepository()
    {
        return $this->repository;
    }

    public function setTableData($columns)
    {
        $this->queryColumnsOptions = $columns;
        $this->queryColumns        = array_keys($columns);
    }

    public function setSQLParams($params)
    {
        $this->sqlParams = $params;
    }

    public function setFrom($from)
    {
        $this->queryFrom = $from;
    }

    public function setGroupBy($groupBy)
    {
        $this->queryGroupBy = $groupBy;
    }

    public function setAdditionalWhere($additionalWhere)
    {
        $this->queryAdditionalWhere = $additionalWhere;
    }

    public function refresh($datagridId)
    {
        $objResponse = new xajaxResponse();
        $objResponse->script('' . 'try {' . 'GF_Datagrid.ReturnInstance(' . (int)$datagridId . ').LoadData();' . '}' . 'catch (xException) {' . 'GF_Debug.HandleException(xException);' . '}' . '');

        return $objResponse;
    }

    public function getData($request, $processFunction)
    {
        $objResponse = new xajaxResponse();

        $rows = $this->getSelectedRows($request);

        $rowsTotal = $this->getTotalRows();

        $rowData = $this->processRows($rows);

        $objResponse->script($processFunction . '({' . 'data_id: "' . $request['id'] . '",' . 'rows_num: ' . count($rows) . ',' . 'starting_from: ' . $request['starting_from'] . ',' . 'total: ' . $rowsTotal . ',' . 'filtered: ' . $this->getFilteredRows($request) . ',' . 'rows: [' . implode(', ', $rowData) . ']' . '});' . '');

        return $objResponse;
    }

    public function deleteRow($datagridId, $rowId, $deleteFunction)
    {
        $objResponse = new xajaxResponse();
        $deleteFunction[0]->$deleteFunction[1]($rowId);
        $objResponse->script("try { GF_Datagrid.ReturnInstance({$datagridId}).LoadData(); GF_Datagrid.ReturnInstance({$datagridId}).ClearSelection(); GF_ConflictResolver.GetMain().Update(); } catch (x) { GF_Debug.HandleException(x); }");

        return $objResponse;
    }

    protected function getSelectedRows($request)
    {
        $offset = (int)$request['starting_from'];
        $limit  = (int)$request['limit'];

        list(
            $idColumn,
            $groupBy,
            $orderBy,
            $orderDir,
            $conditionString,
            $conditions,
            $additionalConditionString,
            $havingString,
            $having
            )
            = $this->getQueryData($request);
        $sql
              = "SELECT SQL_CALC_FOUND_ROWS {$this->getColumnsString()} FROM {$this->queryFrom}{$conditionString}{$additionalConditionString}{$groupBy}{$havingString} ORDER BY {$orderBy} {$orderDir} LIMIT {$offset},{$limit}";
        $stmt = $this->getPdo()->prepare($sql);
        foreach ($conditions as $i => &$part) {
            if (isset($part['value']) && is_array($part['value'])) {
                foreach ($part['value'] as $j => &$subpart) {
                    $stmt->bindValue('value' . $i . '_' . $j, $subpart);
                }
            } else {
                $stmt->bindValue('value' . $i, $part['value']);
            }
        }
        foreach ($this->sqlParams as $key => $val) {
            if (preg_match('/:' . $key . '/', $sql)) {
                if (is_array($val)) {
                    $stmt->bindValue($key, implode(',', $val));
                } else {
                    $stmt->bindValue($key, $val);
                }
            }
        }
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    protected function getTotalRows()
    {
        $sql  = "SELECT FOUND_ROWS() as total";
        $stmt = $this->getPdo()->prepare($sql);
        try {
            $stmt->execute();
            $rs = $stmt->fetch();
        } catch (Exception $e) {
            throw new \RuntimeException('ERR_DATASET_GET_TOTAL', 12, $e->getMessage());
        }

        return $rs['total'];
    }

    protected function getFilteredRows($request)
    {
        list($idColumn, $groupBy, $orderBy, $orderDir, $conditionString, $conditions, $additionalConditionString,
            $havingString, $having)
            = $this->getQueryData($request);
        if (empty($groupBy)) {
            $sqlTotal
                = "SELECT count({$idColumn}) AS total FROM {$this->queryFrom}{$conditionString}{$additionalConditionString}{$groupBy}{$havingString}";
        } else {
            $sqlTotal
                = "SELECT count(*) as total FROM (SELECT count({$idColumn}) AS total FROM {$this->queryFrom}{$conditionString}{$additionalConditionString}{$groupBy}{$havingString}) AS a";
        }
        $stmtTotal = $this->getPdo()->prepare($sqlTotal);
        foreach ($conditions as $i => &$part) {
            if (is_array($part['value'])) {
                foreach ($part['value'] as $j => &$subpart) {
                    $stmtTotal->bindValue('value' . $i . '_' . $j, $subpart);
                }
            } else {
                $stmtTotal->bindValue('value' . $i, $part['value']);
            }
        }

        foreach ($this->sqlParams as $key => $val) {

            if (preg_match("/:{$key}/", $sqlTotal)) {
                if (is_array($val)) {
                    $stmtTotal->bindValue($key, implode(',', $val));
                } else {
                    $stmtTotal->bindValue($key, $val);
                }
            }
        }

        $stmtTotal->execute();

        $totalRows = 0;
        while ($rs = $stmtTotal->fetch()) {
            $totalRows = $rs['total'];
        }

        return $totalRows;
    }

    protected function processRows($rows)
    {
        static $transform
        = array(
            "\r" => '\r',
            "\n" => '\n'
        );

        $rowData = Array();
        foreach ($rows as $row) {
            $columns = Array();
            foreach ($row as $param => $value) {
                if (isset($this->queryColumnsOptions[$param]) && isset($this->queryColumnsOptions[$param]['processLanguage']) && $this->queryColumnsOptions[$param]['processLanguage']) {
                    $value = _($value);
                } elseif (isset($this->queryColumnsOptions[$param]) && isset($this->queryColumnsOptions[$param]['processFunction']) && $this->queryColumnsOptions[$param]['processFunction']) {
                    try {
                        $value = call_user_func($this->queryColumnsOptions[$param]['processFunction'], $value);
                    } catch (Exception $e) {
                        if (!in_array($e->getMessage(), $this->warnings)) {
                            $this->warnings[] = $e->getMessage();
                        }
                    }
                }

                $columns[] = $param . ': "' . strtr(addslashes($value), $transform) . '"';
            }
            $rowData[] = '{' . implode(', ', $columns) . '}';
        }

        return $rowData;
    }

    protected function getQueryData($request)
    {
        $idColumn
                 = isset($this->queryColumnsOptions[$this->queryColumns[0]]['source']) ? $this->queryColumnsOptions[$this->queryColumns[0]]['source'] : $this->queryColumns[0];
        $groupBy = !empty($this->queryGroupBy) ? ' GROUP BY ' . $this->queryGroupBy : '';
        $orderBy
                          = (isset($request['order_by']) && in_array($request['order_by'], $this->queryColumns)) ? $request['order_by'] : $this->queryColumns[0];
        $orderDir         = (isset($request['order_dir']) && ($request['order_dir'] == 'desc')) ? 'DESC' : 'ASC';
        $conditionsString = '';
        $conditions       = Array();
        if (isset($request['where']) && is_array($request['where'])) {
            $conditions       = $request['where'];
            $conditionsString = $this->getConditionsString($conditions);
        }

        $additionalConditionString = $this->getAdditionalConditionsString($conditionsString);
        $havingString              = '';
        $having                    = Array();
        if (isset($request['where']) && is_array($request['where'])) {
            $having       = $request['where'];
            $havingString = $this->getHavingString($conditions);
        }

        return Array(
            $idColumn,
            $groupBy,
            $orderBy,
            $orderDir,
            $conditionsString,
            $conditions,
            $additionalConditionString,
            $havingString,
            $having
        );
    }

    protected function getColumnsString()
    {
        $columns = Array();
        foreach ($this->queryColumnsOptions as $name => $options) {
            $columns[] = $options['source'] . ' AS ' . $name;
        }

        return implode(', ', $columns);
    }

    protected function getConditionsString($conditions)
    {
        $condition = '';
        $parts     = Array();
        foreach ($conditions as $i => &$part) {
            if (!in_array($part['column'], $this->queryColumns)) {
                unset($part);
                continue;
            }
            if (isset($this->queryColumnsOptions[$part['column']]['filter']) && ($this->queryColumnsOptions[$part['column']]['filter'] == 'having')) {
                unset($part);
                continue;
            }
            $suffix = '';
            switch ($part['operator']) {
                case 'NE':
                    $operator = '!=';
                    break;
                case 'LE':
                    $operator = '<=';
                    break;
                case 'GE':
                    $operator = '>=';
                    break;
                case 'LIKE':
                    $operator = 'LIKE';
                    break;
                case 'IN':
                    $operator = '=';
                    break;
                default:
                    $operator = '=';
            }
            if (isset($this->queryColumnsOptions[$part['column']]['source'])) {
                if (isset($this->queryColumnsOptions[$part['column']]['encrypted']) && $this->queryColumnsOptions[$part['column']]['encrypted']) {
                    $columnSource
                        = 'AES_DECRYPT(' . $this->queryColumnsOptions[$part['column']]['source'] . ', :encryptionkey)';
                } else {
                    $columnSource = $this->queryColumnsOptions[$part['column']]['source'];
                }
            } else {
                $columnSource = $part['column'];
            }
            if (isset($part['value']) && is_array($part['value'])) {
                $subparts = Array();
                foreach ($part['value'] as $j => &$subpart) {
                    $subparts[] = '(' . $columnSource . ' ' . $operator . ' :value' . $i . '_' . $j . $suffix . ')';
                }
                if (count($subparts)) {
                    $parts[] = '(' . implode(' OR ', $subparts) . ')';
                } else {
                    $parts[] = '(0)';
                }
            } else {
                $parts[] = '(' . $columnSource . ' ' . $operator . ' :value' . $i . $suffix . ')';
            }
        }
        if (count($parts) && ($parts[0] != '()')) {
            $condition = ' WHERE ' . implode(' AND ', $parts);
        }

        return $condition;
    }

    protected function getAdditionalConditionsString($conditionsString)
    {
        $condition = '';
        if ($this->queryAdditionalWhere != '') {
            if ($conditionsString != '') {
                $condition .= ' AND ' . $this->queryAdditionalWhere;
            } else {
                $condition = ' WHERE ' . $this->queryAdditionalWhere;
            }
        }

        return $condition;
    }

    protected function getHavingString($conditions)
    {
        $condition = '';
        $parts     = Array();
        foreach ($conditions as $i => &$part) {
            if (!in_array($part['column'], $this->queryColumns)) {
                unset($part);
                continue;
            }
            if (!isset($this->queryColumnsOptions[$part['column']]['filter']) || ($this->queryColumnsOptions[$part['column']]['filter'] != 'having')) {
                unset($part);
                continue;
            }
            switch ($part['operator']) {
                case 'LE':
                    $operator = '<=';
                    break;
                case 'GE':
                    $operator = '>=';
                    break;
                case 'LIKE':
                    $operator = 'LIKE';
                    break;
                case 'IN':
                    $operator = '=';
                    break;
                default:
                    $operator = '=';
            }

            if (isset($this->queryColumnsOptions[$part['column']]['source'])) {
                if (isset($this->queryColumnsOptions[$part['column']]['encrypted']) && $this->queryColumnsOptions[$part['column']]['encrypted']) {
                    $columnSource
                        = 'AES_DECRYPT(' . $this->queryColumnsOptions[$part['column']]['source'] . ', :encryptionkey)';
                } else {
                    $columnSource = $this->queryColumnsOptions[$part['column']]['source'];
                }
            } else {
                $columnSource = $part['column'];
            }
            if (is_array($part['value'])) {
                $subparts = Array();
                foreach ($part['value'] as $j => &$subpart) {
                    $subparts[] = '(' . $columnSource . ' ' . $operator . ' :value' . $i . '_' . $j . ')';
                }
                $parts[] = '(' . implode(' OR ', $subparts) . ')';
            } else {
                $parts[] = '(' . $columnSource . ' ' . $operator . ' :value' . $i . ')';
            }
        }
        if (count($parts)) {
            $condition = ' HAVING ' . implode(' AND ', $parts);
        }

        return $condition;
    }
}