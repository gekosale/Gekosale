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
     * Deletes DataGrid row
     *
     * @param $datagrid
     * @param $id
     *
     * @return xajaxResponse
     */
    public function deleteRow($request)
    {
        return $this->repository->delete($request['id']);
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
     * Redirects user from list to edit form
     *
     * @param $id
     *
     * @return mixed
     */
    public function editRow($request)
    {
        return [
            'processFunction' => DataGridInterface::REDIRECT,
            'data'            => $this->generateUrl($this->getEditActionRoute(), ['id' => $request['id']], true)
        ];
    }

    /**
     * Updates DataGrid row
     *
     * @param $request
     *
     * @return mixed
     */
    public function updateRow($request)
    {
        return $this->repository->updateDataGridRow($request);
    }

    /**
     * Returns datagrid data
     *
     * @param $request
     * @param $processFunction
     *
     * @return xajaxResponse
     */
    public function loadData($request)
    {
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
        $total = count($result);

        return [
            'data_id'       => $request['id'],
            'rows_num'      => $total,
            'starting_from' => $request['starting_from'],
            'total'         => $total,
            'filtered'      => $total,
            'rows'          => $result
        ];
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
     * Adds datagrid column
     *
     * @param       $id
     * @param array $options
     */
    public function addColumn($id, array $options)
    {
        $options = array_replace_recursive([
            'editable'   => false,
            'selectable' => false,
            'sorting'    => [
                'default_order' => DataGridInterface::SORT_DIR_DESC
            ],
            'appearance' => [
                'visible' => true,
                'width'   => DataGridInterface::WIDTH_AUTO,
                'align'   => DataGridInterface::ALIGN_RIGHT
            ],
            'filter'     => [
                'type' => DataGridInterface::FILTER_NONE
            ]
        ], $options);

        $this->columns[$id] = $options;
    }

    /**
     * Returns DataGrid columns
     *
     * @return mixed
     */
    public function getColumns()
    {
        return $this->columns;
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
     * Sets DataGrid options
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $options = array_replace_recursive([
            'appearance'     => [
                'column_select' => false
            ],
            'mechanics'      => [
                'rows_per_page' => 25
            ],
            'event_handlers' => [
                'load'         => false,
                'process'      => false,
                'delete_row'   => false,
                'loaded'       => false,
                'edit_row'     => false,
                'delete_group' => false,
                'update_row'   => false,
            ],
            'row_actions'    => [
                DataGridInterface::ACTION_EDIT,
                DataGridInterface::ACTION_DELETE
            ]
        ], $options);

        $this->options = $options;
    }

    /**
     * Returns DataGrid options
     *
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
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