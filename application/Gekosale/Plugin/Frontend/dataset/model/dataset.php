<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale sp. z o.o.. Zabronione jest usuwanie informacji o licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 *
 * $Revision: 602 $
 * $Author: gekosale $
 * $Date: 2011-11-07 22:45:33 +0100 (Pn, 07 lis 2011) $
 * $Id: dataset.php 602 2011-11-07 21:45:33Z gekosale $
 */
namespace Gekosale\Plugin;

class DatasetModel extends Component\Model
{
	protected $DataSet;

	public function __construct ($registry)
	{
		parent::__construct($registry);
		$this->queryColumns = Array();
		$this->queryFrom = '';
		$this->queryGroupBy = '';
		$this->queryOrderBy = '';
		$this->queryHaving = '';
		$this->queryLimit = 1000;
		$this->queryOffset = 0;
		$this->pagination = 1000;
		$this->currentPage = 0;
		$this->sqlParams = Array();
		$this->encryptionKey = App::getContainer()->get('session')->getActiveEncryptionKeyValue();
		$this->languageId = Helper::getLanguageId();
		$this->viewId = (! is_null(Helper::getViewId())) ? Helper::getViewId() : 0;
		$this->queryAdditionalWhere = '';
		$this->DataSet = Array();
		$this->cacheEnabled = Array(
			'enabled' => false,
			'lifetime' => 3600,
			'cacheid' => null
		);
		$this->layerData = $this->registry->loader->getCurrentLayer();
	}

	public function flushCache ()
	{
		$dir = ROOTPATH . 'serialization' . DS;
		$file = 'Cache.Dataset_';
		foreach (glob($dir . $file . '*') as $key => $fn){
			if (is_file($fn)){
				@unlink($fn);
			}
		}
	}

	public function processPrice ($price)
	{
		if ($price < 0){
			return ($this->layerData['negativepreffix'] . number_format(abs($price), $this->layerData['decimalcount'], $this->layerData['decimalseparator'], $this->layerData['thousandseparator']) . $this->layerData['negativesuffix']);
		}
		return ($this->layerData['positivepreffix'] . number_format($price, $this->layerData['decimalcount'], $this->layerData['decimalseparator'], $this->layerData['thousandseparator']) . $this->layerData['positivesuffix']);
	}

	public function setTableData ($columns)
	{
		$this->queryColumns = $columns;
	}

	public function setSQLParams ($params)
	{
		$this->sqlParams = $params;
	}

	public function setFrom ($from)
	{
		$this->queryFrom = $from;
	}

	public function setLimit ($limit)
	{
		$this->queryLimit = $limit;
	}

	public function setPagination ($items)
	{
		$this->pagination = $items;
	}

	public function setCurrentPage ($current)
	{
		if ($current){
			$this->currentPage = $current;
		}
		else{
			$this->currentPage = 1;
		}
	}

	public function setOffset ($offset)
	{
		$this->queryOffset = $offset;
	}

	public function setViewId ($viewId)
	{
		$this->viewId = $viewId;
	}

	public function setHavingString ($string)
	{
		$this->queryHaving = $string;
	}

	public function setOrderBy ($default, $order)
	{
		if ($order){
			$this->queryOrderBy = $order;
		}
		else{
			$this->queryOrderBy = $default;
		}
		
		if (strlen($this->queryOrderBy) == 0){
			$this->queryOrderBy = 'default';
		}
		
		$this->DataSet['orderBy'] = $this->queryOrderBy;
	}

	public function setOrderDir ($default, $dir)
	{
		if ($dir){
			$this->queryOrderDir = $dir;
		}
		else{
			$this->queryOrderDir = $default;
		}
		$this->DataSet['orderDir'] = $this->queryOrderDir;
	}

	public function setGroupBy ($groupby)
	{
		$this->queryGroupBy = $groupby;
	}

	public function setAdditionalWhere ($additionalWhere)
	{
		$this->queryAdditionalWhere = $additionalWhere;
	}

	public function setCache ($cache)
	{
		$this->cache = $cache;
	}

	protected function processRows ($rows)
	{
		while ($row = current($rows)){
			foreach ($row as $param => $value){
				if (isset($this->queryColumns[$param]['processPrice'])){
					$rows[key($rows)][$param] = $this->processPrice($value);
				}
				if (isset($this->queryColumns[$param]['processFunction'])){
					$rows[key($rows)][$param] = call_user_func($this->queryColumns[$param]['processFunction'], $value);
				}
			}
			next($rows);
		}
		$this->DataSet['rows'] = $rows;
	}

	public function getTotalRecords ()
	{
		$sql = "SELECT FOUND_ROWS() as total";
		$stmt = Db::getInstance()->prepare($sql);
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
		}
		catch (Exception $e){
			throw new FrontendException('ERR_DATASET_GET_TOTAL', 12, $e->getMessage());
		}
		$this->DataSet['total'] = $rs['total'];
	}

	public function getData ()
	{
		$this->queryLimit = $this->pagination;
		$this->queryOffset = $this->currentPage * $this->pagination - $this->pagination;
		
		$bFilteredOrderBy = false;
		foreach ($this->queryColumns as $column => $options){
			if ($this->queryOrderBy == $column || $this->queryOrderBy == 'random' || $this->queryOrderBy == 'default' || $this->queryOrderBy == 'related'){
				$bFilteredOrderBy = true;
			}
			if (isset($options['encrypted']) && ($options['encrypted']) && ($this->encryptionKey != '')){
				$columns[] = "AES_DECRYPT({$options['source']}, :encryptionkey) AS {$column}";
			}
			else{
				$columns[] = "{$options['source']} AS {$column}";
			}
		}
		if ($bFilteredOrderBy == false){
			throw new \Exception('Column not found: ' . $this->queryOrderBy);
		}
		$columns[0] = 'SQL_CALC_FOUND_ROWS ' . $columns[0];
		$sqlColumns = implode(",\n", $columns);
		$sqlFrom = $this->queryFrom;
		$sqlGroupBy = $this->queryGroupBy;
		
		$selectString = "SELECT {$sqlColumns}";
		$fromString = " FROM {$sqlFrom}";
		$whereString = "";
		$havingString = "";
		if ($this->queryAdditionalWhere != ''){
			$whereString = ' WHERE ' . $this->queryAdditionalWhere;
		}
		$groupString = " GROUP BY {$sqlGroupBy}";
		$orderString = ' ';
		if ($this->queryHaving != ''){
			$havingString = ' HAVING ' . $this->queryHaving;
		}
		$limitString = ' ';
		if ($this->queryOrderBy == 'random'){
			$orderString .= 'ORDER BY RAND()';
			$limitString .= 'LIMIT ' . $this->pagination;
		}
		elseif ($this->queryOrderBy == 'default'){
			$orderString .= 'ORDER BY hierarchy ASC, discountprice DESC, new DESC';
			$limitString .= 'LIMIT ' . $this->queryOffset . ',' . $this->pagination;
		}
		elseif ($this->queryOrderBy == 'related'){
			$orderString .= 'ORDER BY hierarchy ' . $this->queryOrderDir;
			$limitString .= 'LIMIT ' . $this->queryOffset . ',' . $this->pagination;
		}
		else{
			$orderString .= 'ORDER BY ' . $this->queryOrderBy . ' ' . $this->queryOrderDir;
			$limitString .= 'LIMIT ' . $this->queryOffset . ',' . $this->pagination;
		}
		
		$sql = $selectString . $fromString . $whereString . $groupString . $havingString . $orderString . $limitString;
		$stmt = Db::getInstance()->prepare($sql);
		if (preg_match('/:languageid/', $sql)){
			$stmt->bindValue('languageid', $this->languageId);
		}
		if (preg_match('/:encryptionkey/', $sql)){
			$stmt->bindValue('encryptionkey', $this->encryptionKey);
		}
		foreach ($this->sqlParams as $key => $val){
			
			if (is_array($val)){
				if (isset($val[0]) && is_numeric($val[0])){
					$stmt->bindValue($key, implode(',', $val));
				}
				elseif (isset($val[0]) && is_string($val[0])){
					$stmt->bindValue($key, implode(',', $val));
				}
				else{
					$stmt->bindValue($key, 0);
				}
			}
			else{
				if (is_int($val)){
					$stmt->bindValue($key, $val);
				}
				elseif (is_null($val)){
					$stmt->bindValue($key, NULL);
				}
				elseif (is_float($val)){
					$stmt->bindValue($key, $val);
				}
				elseif (is_string($val)){
					$stmt->bindValue($key, $val);
				}
				else{
					$stmt->bindValue($key, $val);
				}
			}
		}
		
		if (preg_match('/:viewid/', $sql)){
			$stmt->bindValue('viewid', $this->viewId);
		}
		if (preg_match('/:clientgroupid/', $sql)){
			$stmt->bindValue('clientgroupid', App::getContainer()->get('session')->getActiveClientGroupid());
		}
		if (preg_match('/:today/', $sql)){
			$stmt->bindValue('today', date("Y-m-d"));
		}
		if (preg_match('/:currencyto/', $sql)){
			$stmt->bindValue('currencyto', App::getContainer()->get('session')->getActiveCurrencyId());
		}
		
		try{
			$stmt->execute();
			$rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		}
		catch (Exception $e){
			throw new FrontendException('ERR_DATASET_GET_DATA', 12, $e->getMessage());
		}
		
		$this->getTotalRecords();
		$this->processRows($rows);
		
		$pages = ceil($this->DataSet['total'] / $this->pagination);
		if ($pages == 0){
			$this->DataSet['totalPages'] = range(1, 1, 1);
			$this->DataSet['activePage'] = 1;
			$this->DataSet['lastPage'] = 1;
			$this->DataSet['previousPage'] = 1;
			$this->DataSet['nextPage'] = 1;
		}
		else{
			$this->DataSet['totalPages'] = range(1, $pages, 1);
			$this->DataSet['activePage'] = $this->currentPage;
			$this->DataSet['lastPage'] = $pages;
			$this->DataSet['previousPage'] = $this->currentPage - 1;
			$this->DataSet['nextPage'] = $this->currentPage + 1;
		}
	}

	public function getDatasetRecords ()
	{
		$this->getData();
		return $this->DataSet;
	}
}