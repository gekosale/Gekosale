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
use Gekosale\Core\DataGrid\Column;
use Gekosale\Core\DataGrid\Renderer;
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

    /**
     * Set DataGrid name
     * 
     * @param string $name
     */
    public function setName ($name)
    {
        $this->name = $name;
    }

    protected function addColumn (Column $column)
    {
        $this->columns[] = $column;
    }

    public function getFilterSuggestions ($field, $request, $processFunction)
    {
        if (! isset($this->autosuggests[$field])){
            $objResponse = new xajaxResponse();
            $objResponse->script('
                                                        ' . $processFunction . '({
						data_id: ""
					});
				');
            
            return $objResponse;
        }
        
        return $this->autosuggests[$field]->getSuggestions($request, $processFunction);
    }

    public function getFilterData ()
    {
        $filters = Array();
        foreach ($this->queryColumnsOptions as $name => $options){
            if (isset($options['prepareForSelect']) && $options['prepareForSelect']){
                $possibilities = Array(
                    "{id: '', caption: ''}"
                );
                $sql = 'SELECT DISTINCT ';
                if (isset($options['source'])){
                    $sql .= $options['source'];
                }
                else{
                    $sql .= $name;
                }
                $sql .= ' AS possibility FROM ' . $this->queryFrom . ' ORDER BY possibility';
                $stmt = $this->db->prepare($sql);
                if (preg_match('/:languageid/', $sql)){
                    $stmt->bindValue('languageid', $this->languageId);
                }
                if (preg_match('/:viewid/', $sql)){
                    $stmt->bindValue('viewid', ($this->viewId > 0) ? $this->viewId : null);
                }
                if (preg_match('/:views/', $sql)){
                    $stmt->bindValue('views', implode(',', $this->viewIds));
                }
                
                foreach ($this->sqlParams as $key => $val){
                    
                    if (is_array($val)){
                        $stmt->bindValue($key, implode(',', $val));
                    }
                    else{
                        $stmt->bindValue($key, $val);
                    }
                }
                
                $stmt->execute();
                while ($rs = $stmt->fetch()){
                    $caption = addslashes($rs['possibility']);
                    if (isset($options['processLanguage']) && $options['processLanguage']){
                        $caption = addslashes(_($caption));
                    }
                    $id = addslashes($rs['possibility']);
                    $possibilities[] = "{id: '{$id}', caption: '{$caption}'}";
                }
                $filters[$name] = implode(', ', $possibilities);
            }
            else{
                if (isset($options['prepareForTree']) && $options['prepareForTree']){
                    $filters[$name] = json_encode($options['first_level']);
                }
            }
        }
        
        return $filters;
    }

    public function setTableData ($columns)
    {
        $this->queryColumnsOptions = $columns;
        $this->queryColumns = array_keys($columns);
    }

    public function setLanguageId ($languageId)
    {
        $this->languageId = $languageId;
        $this->processFilters();
    }

    public function setSQLParams ($params)
    {
        $this->sqlParams = $params;
        $this->processFilters();
    }

    public function setViewId ($viewId)
    {
        $this->viewId = $viewId;
        $this->processFilters();
    }

    public function setViewIds ($viewIds)
    {
        $this->viewIds = $viewIds;
        $this->processFilters();
    }

    public function setFrom ($from)
    {
        $this->queryFrom = $from;
        $this->processFilters();
    }

    public function setGroupBy ($groupBy)
    {
        $this->queryGroupBy = $groupBy;
        $this->processFilters();
    }

    public function setAdditionalWhere ($additionalWhere)
    {
        $this->queryAdditionalWhere = $additionalWhere;
        $this->processFilters();
    }

    public function refresh ($datagridId)
    {
        $objResponse = new xajaxResponse();
        $objResponse->script('' . 'try {' . 'GF_Datagrid.ReturnInstance(' . (int) $datagridId . ').LoadData();' . '}' . 'catch (xException) {' . 'GF_Debug.HandleException(xException);' . '}' . '');
        
        return $objResponse;
    }

    public function getData ($request, $processFunction)
    {
        $this->warnings = Array();
        try{
            $objResponse = new xajaxResponse();
            try{
                $rows = $this->getSelectedRows($request);
                $rowsTotal = $this->getTotalRows();
            }
            catch (Exception $e){
                $rows = Array();
                $this->warnings[] = $e->getMessage();
            }
            $rowData = $this->processRows($rows);
            
            $objResponse->script('' . '' . $processFunction . '({' . 'data_id: "' . (isset($request['id']) ? $request['id'] : '') . '",' . 'rows_num: ' . count($rows) . ',' . 'starting_from: ' . (isset($request['starting_from']) ? $request['starting_from'] : 0) . ',' . 'total: ' . $rowsTotal . ',' . 'filtered: ' . $this->getFilteredRows($request) . ',' . 'rows: [' . implode(', ', $rowData) . ']' . '});' . '');
            foreach ($this->warnings as $warning){
                $objResponse->script("GWarning('" . _('ERR_PROBLEM_DURING_AJAX_EXECUTION') . "', '" . preg_replace('/(\n|\r)+/', '\n', nl2br(addslashes($warning))) . "');");
            }
        }
        catch (Exception $e){
            $objResponse = new xajaxResponse();
            $objResponse->script("GError('" . _('ERR_PROBLEM_DURING_AJAX_EXECUTION') . "', '" . preg_replace('/(\n|\r)+/', '\n', nl2br(addslashes($e->getMessage()))) . "');");
        }
        
        return $objResponse;
    }

    public function deleteRow ($datagridId, $rowId, $deleteFunction, $controllerName)
    {
        $objResponse = new xajaxResponse();
        if ($this->registry->right->checkDeletePermission($controllerName) === false){
            $objResponse->alert('Nie masz uprawnień');
            
            return $objResponse;
        }
        
        try{
            if (is_array($deleteFunction)){
                $state = $deleteFunction[0]->$deleteFunction[1]($rowId);
            }
            else{
                $state = $deleteFunction($rowId);
            }
            
            if (isset($state['error'])){
                $objResponse->script("GError('" . _('ERR_PROBLEM_DURING_AJAX_EXECUTION') . "', '" . $state['error'] . "');");
            }
            else{
                
                $objResponse->script("try { GF_Datagrid.ReturnInstance({$datagridId}).LoadData(); GF_Datagrid.ReturnInstance({$datagridId}).ClearSelection(); GF_ConflictResolver.GetMain().Update(); } catch (x) { GF_Debug.HandleException(x); }");
            }
        }
        catch (Exception $e){
            $objResponse->script("GWarning('" . _('ERR_PROBLEM_DURING_AJAX_EXECUTION') . "', '" . preg_replace('/(\n|\r)+/', '\n', nl2br(addslashes($e->getMessage()))) . "');");
        }
        
        return $objResponse;
    }

    protected function getSelectedRows ($request)
    {
        $offset = isset($request['starting_from']) ? $request['starting_from'] : 0;
        $limit = isset($request['limit']) ? $request['limit'] : 10;
        list($idColumn, $groupBy, $orderBy, $orderDir, $conditionString, $conditions, $additionalConditionString, $havingString, $having) = $this->getQueryData($request);
        $sql = "SELECT SQL_CALC_FOUND_ROWS {$this->getColumnsString(
        )} FROM {$this->queryFrom}{$conditionString}{$additionalConditionString}{$groupBy}{$havingString} ORDER BY {$orderBy} {$orderDir} LIMIT {$offset},{$limit}";
        $stmt = $this->db->prepare($sql);
        foreach ($conditions as $i => &$part){
            if (isset($part['value']) && is_array($part['value'])){
                foreach ($part['value'] as $j => &$subpart){
                    $stmt->bindValue('value' . $i . '_' . $j, $subpart);
                }
            }
            else{
                $stmt->bindValue('value' . $i, $part['value']);
            }
        }
        if (preg_match('/:encryptionkey/', $sql)){
            $stmt->bindValue('encryptionkey', $this->encryptionKey);
        }
        if (preg_match('/:languageid/', $sql)){
            $stmt->bindValue('languageid', $this->languageId);
        }
        foreach ($this->sqlParams as $key => $val){
            if (preg_match('/:' . $key . '/', $sql)){
                if (is_array($val)){
                    $stmt->bindValue($key, implode(',', $val));
                }
                else{
                    $stmt->bindValue($key, $val);
                }
            }
        }
        if (preg_match('/:viewid/', $sql)){
            $stmt->bindValue('viewid', ($this->viewId > 0) ? $this->viewId : null);
        }
        if (preg_match('/:views/', $sql)){
            $stmt->bindValue('views', implode(',', $this->viewIds));
        }
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    protected function getTotalRows ()
    {
        $sql = "SELECT FOUND_ROWS() as total";
        $stmt = $this->db->prepare($sql);
        try{
            $stmt->execute();
            $rs = $stmt->fetch();
        }
        catch (Exception $e){
            throw new \RuntimeException('ERR_DATASET_GET_TOTAL', 12, $e->getMessage());
        }
        
        return $rs['total'];
    }

    protected function getFilteredRows ($request)
    {
        list($idColumn, $groupBy, $orderBy, $orderDir, $conditionString, $conditions, $additionalConditionString, $havingString, $having) = $this->getQueryData($request);
        if (empty($groupBy)){
            $sqlTotal = "SELECT count({$idColumn}) AS total FROM {$this->queryFrom}{$conditionString}{$additionalConditionString}{$groupBy}{$havingString}";
        }
        else{
            $sqlTotal = "SELECT count(*) as total FROM (SELECT count({$idColumn}) AS total FROM {$this->queryFrom}{$conditionString}{$additionalConditionString}{$groupBy}{$havingString}) AS a";
        }
        $stmtTotal = $this->db->prepare($sqlTotal);
        foreach ($conditions as $i => &$part){
            if (is_array($part['value'])){
                foreach ($part['value'] as $j => &$subpart){
                    $stmtTotal->bindValue('value' . $i . '_' . $j, $subpart);
                }
            }
            else{
                $stmtTotal->bindValue('value' . $i, $part['value']);
            }
        }
        
        if (preg_match('/:encryptionkey/', $sqlTotal)){
            $stmtTotal->bindValue('encryptionkey', $this->encryptionKey);
        }
        if (preg_match('/:languageid/', $sqlTotal)){
            $stmtTotal->bindValue('languageid', $this->languageId);
        }
        
        foreach ($this->sqlParams as $key => $val){
            
            if (preg_match("/:{$key}/", $sqlTotal)){
                if (is_array($val)){
                    $stmtTotal->bindValue($key, implode(',', $val));
                }
                else{
                    $stmtTotal->bindValue($key, $val);
                }
            }
        }
        
        if (preg_match("/:viewid/", $sqlTotal)){
            $stmtTotal->bindValue('viewid', ($this->viewId > 0) ? $this->viewId : null);
        }
        if (preg_match('/:views/', $sqlTotal)){
            $stmtTotal->bindValue('views', implode(',', $this->viewIds));
        }
        $stmtTotal->execute();
        
        $totalRows = 0;
        while ($rs = $stmtTotal->fetch()){
            $totalRows = $rs['total'];
        }
        
        return $totalRows;
    }

    protected function processRows ($rows)
    {
        static $transform = array(
            "\r" => '\r',
            "\n" => '\n'
        );
        
        $rowData = Array();
        foreach ($rows as $row){
            $columns = Array();
            foreach ($row as $param => $value){
                if (isset($this->queryColumnsOptions[$param]) && isset($this->queryColumnsOptions[$param]['processLanguage']) && $this->queryColumnsOptions[$param]['processLanguage']){
                    $value = _($value);
                }
                elseif (isset($this->queryColumnsOptions[$param]) && isset($this->queryColumnsOptions[$param]['processFunction']) && $this->queryColumnsOptions[$param]['processFunction']){
                    try{
                        $value = call_user_func($this->queryColumnsOptions[$param]['processFunction'], $value);
                    }
                    catch (Exception $e){
                        if (! in_array($e->getMessage(), $this->warnings)){
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

    protected function getQueryData ($request)
    {
        $idColumn = isset($this->queryColumnsOptions[$this->queryColumns[0]]['source']) ? $this->queryColumnsOptions[$this->queryColumns[0]]['source'] : $this->queryColumns[0];
        $groupBy = ! empty($this->queryGroupBy) ? ' GROUP BY ' . $this->queryGroupBy : '';
        $orderBy = (isset($request['order_by']) && in_array($request['order_by'], $this->queryColumns)) ? $request['order_by'] : $this->queryColumns[0];
        $orderDir = (isset($request['order_dir']) && ($request['order_dir'] == 'desc')) ? 'DESC' : 'ASC';
        $conditionsString = '';
        $conditions = Array();
        if (isset($request['where']) && is_array($request['where'])){
            $conditions = $request['where'];
            $conditionsString = $this->getConditionsString($conditions);
        }
        
        $additionalConditionString = $this->getAdditionalConditionsString($conditionsString);
        $havingString = '';
        $having = Array();
        if (isset($request['where']) && is_array($request['where'])){
            $having = $request['where'];
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

    protected function getColumnsString ($limit = 0)
    {
        $string = '';
        foreach ($this->queryColumnsOptions as $name => $options){
            if (isset($options['source'])){
                if (isset($options['encrypted']) && $options['encrypted']){
                    $string .= 'AES_DECRYPT(' . $options['source'] . ', :encryptionkey) AS ' . $name;
                }
                else{
                    $string .= $options['source'] . ' AS ' . $name;
                }
            }
            else{
                if (isset($options['encrypted']) && $options['encrypted']){
                    $string .= 'AES_DECRYPT(' . $name . ', :encryptionkey) AS ' . $name;
                }
                else{
                    $string .= $name;
                }
            }
            $string .= ', ';
            if (-- $limit == 0){
                break;
            }
        }
        
        return substr($string, 0, - 2);
    }

    protected function getConditionsString ($conditions)
    {
        $condition = '';
        $parts = Array();
        foreach ($conditions as $i => &$part){
            if (! in_array($part['column'], $this->queryColumns)){
                unset($part);
                continue;
            }
            if (isset($this->queryColumnsOptions[$part['column']]['filter']) && ($this->queryColumnsOptions[$part['column']]['filter'] == 'having')){
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
            if (isset($this->queryColumnsOptions[$part['column']]['source'])){
                if (isset($this->queryColumnsOptions[$part['column']]['encrypted']) && $this->queryColumnsOptions[$part['column']]['encrypted']){
                    $columnSource = 'AES_DECRYPT(' . $this->queryColumnsOptions[$part['column']]['source'] . ', :encryptionkey)';
                }
                else{
                    $columnSource = $this->queryColumnsOptions[$part['column']]['source'];
                }
            }
            else{
                $columnSource = $part['column'];
            }
            if (isset($part['value']) && is_array($part['value'])){
                $subparts = Array();
                foreach ($part['value'] as $j => &$subpart){
                    $subparts[] = '(' . $columnSource . ' ' . $operator . ' :value' . $i . '_' . $j . $suffix . ')';
                }
                if (count($subparts)){
                    $parts[] = '(' . implode(' OR ', $subparts) . ')';
                }
                else{
                    $parts[] = '(0)';
                }
            }
            else{
                $parts[] = '(' . $columnSource . ' ' . $operator . ' :value' . $i . $suffix . ')';
            }
        }
        if (count($parts) && ($parts[0] != '()')){
            $condition = ' WHERE ' . implode(' AND ', $parts);
        }
        
        return $condition;
    }

    protected function getAdditionalConditionsString ($conditionsString)
    {
        $condition = '';
        if ($this->queryAdditionalWhere != ''){
            if ($conditionsString != ''){
                $condition .= ' AND ' . $this->queryAdditionalWhere;
            }
            else{
                $condition = ' WHERE ' . $this->queryAdditionalWhere;
            }
        }
        
        return $condition;
    }

    protected function getHavingString ($conditions)
    {
        $condition = '';
        $parts = Array();
        foreach ($conditions as $i => &$part){
            if (! in_array($part['column'], $this->queryColumns)){
                unset($part);
                continue;
            }
            if (! isset($this->queryColumnsOptions[$part['column']]['filter']) || ($this->queryColumnsOptions[$part['column']]['filter'] != 'having')){
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
            
            if (isset($this->queryColumnsOptions[$part['column']]['source'])){
                if (isset($this->queryColumnsOptions[$part['column']]['encrypted']) && $this->queryColumnsOptions[$part['column']]['encrypted']){
                    $columnSource = 'AES_DECRYPT(' . $this->queryColumnsOptions[$part['column']]['source'] . ', :encryptionkey)';
                }
                else{
                    $columnSource = $this->queryColumnsOptions[$part['column']]['source'];
                }
            }
            else{
                $columnSource = $part['column'];
            }
            if (is_array($part['value'])){
                $subparts = Array();
                foreach ($part['value'] as $j => &$subpart){
                    $subparts[] = '(' . $columnSource . ' ' . $operator . ' :value' . $i . '_' . $j . ')';
                }
                $parts[] = '(' . implode(' OR ', $subparts) . ')';
            }
            else{
                $parts[] = '(' . $columnSource . ' ' . $operator . ' :value' . $i . ')';
            }
        }
        if (count($parts)){
            $condition = ' HAVING ' . implode(' AND ', $parts);
        }
        
        return $condition;
    }
}