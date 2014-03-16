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

use xajaxResponse;
use Gekosale\Core\DataGrid\DataGridInterface;

/**
 * Class DataGrid
 *
 * @package Gekosale\Core
 * @author  Adam Piotrowski
 */
class DataGrid extends Component
{

    /**
     * @var
     */
    protected $query;

    /**
     * @var
     */
    protected $columns;

    /**
     * @var
     */
    protected $warnings;

    /**
     * @var
     */
    protected $container;

    /**
     * @var
     */
    protected $repository;

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

    /**
     * Sets Repository service needed in datagrid
     *
     * @param Repository $repository
     */
    public function setRepository(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Refreshes datagrid instance by id
     *
     * @param $datagridId
     *
     * @return xajaxResponse
     */
    public function refresh($datagridId)
    {
        $objResponse = new xajaxResponse();
        $objResponse->script('' . 'try {' . 'GF_Datagrid.ReturnInstance(' . (int)$datagridId . ').LoadData();' . '}' . 'catch (xException) {' . 'GF_Debug.HandleException(xException);' . '}' . '');

        return $objResponse;
    }

    /**
     * Returns datagrid data
     *
     * @param $request
     * @param $processFunction
     *
     * @return xajaxResponse
     */
    public function getData($request, $processFunction)
    {
        $request = array_merge([
            'starting_from' => 0,
            'limit'         => 25,
            'order_by'      => current(array_keys($this->columns)),
            'order_dir'     => 'asc',
        ], $request);

        $objResponse = new xajaxResponse();

        $this->query->skip($request['starting_from']);
        $this->query->take($request['limit']);
        $this->query->orderBy($request['order_by'], $request['order_dir']);

        foreach ($this->columns as $key => $column) {
            $this->query->addSelect(sprintf('%s AS %s', $column['source'], $key));
        }
        foreach ($request['where'] as $where) {
            $column   = $this->columns[$where['column']]['source'];
            $operator = $this->getOperator($where['operator']);
            $value    = $where['value'];
            $this->query->where($column, $operator, $value);
        }

        $result = $this->query->get();
        $total  = count($result);

        return $objResponse->script($processFunction . '({' . 'data_id: "' . $request['id'] . '",' . 'rows_num: ' . $total . ',' . 'starting_from: ' . $request['starting_from'] . ',' . 'total: ' . $total . ',' . 'filtered: ' . $total . ',' . 'rows: ' . json_encode($result) . '' . '});' . '');
    }

    /**
     * Get real operator
     *
     * @param $operator
     *
     * @return string
     */
    private function getOperator($operator)
    {
        switch ($operator) {
            case 'NE':
                return '!=';
                break;
            case 'LE':
                return '<=';
                break;
            case 'GE':
                return '>=';
                break;
            case 'LIKE':
                return 'LIKE';
                break;
            case 'IN':
                return '=';
                break;
            default:
                return '=';
        }
    }

    /**
     * Deletes row using callback function
     *
     * @param $datagridId
     * @param $rowId
     * @param $deleteFunction
     *
     * @return xajaxResponse
     */
    public function deleteRow($datagridId, $rowId, $deleteFunction)
    {
        $objResponse = new xajaxResponse();
        $deleteFunction[0]->$deleteFunction[1]($rowId);
        $objResponse->script("try { GF_Datagrid.ReturnInstance({$datagridId}).LoadData(); GF_Datagrid.ReturnInstance({$datagridId}).ClearSelection(); GF_ConflictResolver.GetMain().Update(); } catch (x) { GF_Debug.HandleException(x); }");

        return $objResponse;
    }

    /**
     * Adds datagrid column
     *
     * @param       $id
     * @param array $options
     */
    public function addColumn($id, array $options)
    {
        $this->columns[$id] = $options;
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
                    $value = call_user_func($this->queryColumnsOptions[$param]['processFunction'], $value);
                }

                $columns[] = $param . ': "' . strtr(addslashes($value), $transform) . '"';
            }
            $rowData[] = '{' . implode(', ', $columns) . '}';
        }

        return $rowData;
    }

    /**
     * Sets new query
     *
     * @param $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * Returns query
     *
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;

    }
}