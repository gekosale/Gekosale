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
 * $Revision: 619 $
 * $Author: gekosale $
 * $Date: 2011-12-19 22:09:00 +0100 (Pn, 19 gru 2011) $
 * $Id: currencieslist.php 619 2011-12-19 21:09:00Z gekosale $ 
 */

namespace Gekosale;
use xajaxResponse;

class CurrenciesListModel extends Component\Model\Datagrid
{
	
	protected $currencyCodes = array(
		'AUD' => 'AUD',
		'CAD' => 'CAD',
		'EUR' => 'EUR',
		'GBP' => 'GBP',
		'LTL' => 'LTL',
		'JPY' => 'JPY',
		'USD' => 'USD',
		'NZD' => 'NZD',
		'CHF' => 'CHF',
		'HKD' => 'HKD',
		'SGD' => 'SGD',
		'SEK' => 'SEK',
		'DKK' => 'DKK',
		'PLN' => 'PLN',
		'NOK' => 'NOK',
		'HUF' => 'HUF',
		'CZK' => 'CZK',
		'ILS' => 'ILS',
		'MXN' => 'MXN',
		'BRL' => 'BRL',
		'MYR' => 'MYR',
		'PHP' => 'PHP',
		'TWD' => 'TWD',
		'THB' => 'THB'
	);

	public function getCurrenciesALLToSelect ()
	{
		$tmp = Array();
		foreach ($this->currencyCodes as $key){
			$tmp[$key] = $key;
		}
		return $tmp;
	}

	public function downloadExchangeRates ($basecurrency)
	{
		
		$xml_file = "http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml";
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $xml_file);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$rates = curl_exec($curl);
		curl_close($curl);
		
		preg_match_all("{<Cube\s*currency='(\w*)'\s*rate='([\d\.]*)'/>}is", $rates, $xml_rates);
		array_shift($xml_rates);
		
		$exchange_rate['EUR'] = 1;
		
		for ($i = 0; $i < count($xml_rates[0]); $i ++){
			$exchange_rate[$xml_rates[0][$i]] = $xml_rates[1][$i];
		}
		$Data = Array();
		
		foreach ($exchange_rate as $currency => $rate){
			if ((is_numeric($rate)) && ($rate != 0)){
				$Data[$currency] = $rate;
			}
		}
		$Rates = Array();
		if (isset($Data[$basecurrency])){
			foreach ($Data as $currency => $rate){
				$Rates[$currency] = number_format((1 / $Data[$basecurrency]) * $Data[$currency], 4, '.', '');
			}
		}
		
		return $Rates;
	}

	public function initDatagrid ($datagrid)
	{
		
		$datagrid->setTableData('currency', Array(
			'id' => Array(
				'source' => 'C.idcurrency'
			),
			'name' => Array(
				'source' => 'C.currencyname'
			),
			'currencysymbol' => Array(
				'source' => 'C.currencysymbol'
			),
			'currencyto' => Array(
				'source' => 'C2.currencysymbol',
				'prepareForSelect' => true
			),
			'exchangerate' => Array(
				'source' => 'GROUP_CONCAT(DISTINCT SUBSTRING(CONCAT(C2.currencysymbol,\': \', CR2.exchangerate), 1) SEPARATOR \'<br>\')'
			)
		));
		
		$datagrid->setFrom('
			currency C
			LEFT JOIN currencyrates CR ON CR.currencyfrom = C.idcurrency
			LEFT JOIN currency C2 ON CR.currencyto = C2.idcurrency
			LEFT JOIN currencyrates CR2 ON CR2.currencyfrom = C.idcurrency AND CR2.currencyto = CR.currencyto 
		');
		
		$datagrid->setGroupBy('
			C.idcurrency
		');
	
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getCurrencieslistForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteCurrencieslist ($datagrid, $id)
	{
		return $this->getDatagrid()->deleteRow($datagrid, $id, Array(
			$this,
			'deleteCurrency'
		), $this->getName());
	}

	public function doAJAXUpdateCurrencieslist ($datagridId, $id)
	{
		
		try{
			$this->refreshCurrency($id);
			return $this->getDatagrid()->refresh($datagridId);
		}
		catch (Exception $e){
			$objResponse = new xajaxResponse();
			$objResponse->script("GF_Alert('{$this->trans('ERR_UNABLE_TO_REFRESH_CURRENCY')}', '{$e->getMessage()}');");
			return $objResponse;
		}
	}

	public function doAJAXRefreshAllCurrencies ()
	{
		$objResponse = new xajaxResponse();
		
		$sql = 'SELECT idcurrency AS id	FROM currency';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$this->refreshCurrency($rs['id']);
		}
		$objResponse->script('theDatagrid.LoadData();');
		return $objResponse;
	}

	public function refreshCurrency ($id)
	{
		
		$sql = 'SELECT currencysymbol FROM currency WHERE idcurrency = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		
		if ($rs){
			
			DbTracker::deleteRows('currencyrates', 'currencyfrom', $id);
			
			$currencyfrom = trim($rs['currencysymbol']);
			
			$exchangerates = $this->downloadExchangeRates($currencyfrom);
			
			foreach ($exchangerates as $currency => $rate){
				
				$sql = 'SELECT idcurrency FROM currency	WHERE currencysymbol = :symbol';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('symbol', $currency);
				$stmt->execute();
				$rs = $stmt->fetch();
				
				if ($rs){
					$sql = 'INSERT INTO currencyrates SET
								currencyfrom = :currencyfrom, 
								currencyto = :currencyto, 
								exchangerate = :exchangerate';
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('currencyfrom', $id);
					$stmt->bindValue('currencyto', $rs['idcurrency']);
					$stmt->bindValue('exchangerate', $rate);
					$stmt->execute();
				}
			}
		}
	
	}

	public function getFirstnameForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('firstname', $request, $processFunction);
	}

	public function getSurnameForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('surname', $request, $processFunction);
	}

	public function getNameForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('name', $request, $processFunction);
	}

	public function getCodeForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('code', $request, $processFunction);
	}

	public function deleteCurrency ($id)
	{
		$sql = "SELECT COUNT(idproduct)	as total FROM product WHERE buycurrencyid = :id OR sellcurrencyid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs['total'] == 0){
			return DbTracker::deleteRows('currency', 'idcurrency', $id);
		}
		else{
			return Array(
				'error' => $this->trans('ERR_BIND_SELLCURRENCY_PRODUCT')
			);
		}
		
		$this->flushCache();
	
	}

	public function addCurrencieslist ($Data)
	{
		$sql = 'INSERT INTO currency SET
					currencyname = :name, 
					currencysymbol = :symbol,
					decimalseparator = :decimalseparator,
					decimalcount 	= :decimalcount,
					thousandseparator = :thousandseparator,
					positivepreffix = :positivepreffix,
					positivesuffix = :positivesuffix,
					negativepreffix = :negativepreffix,
					negativesuffix = :negativesuffix';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $Data['name']);
		$stmt->bindValue('symbol', $Data['symbol']);
		$stmt->bindValue('decimalseparator', $Data['decimalseparator']);
		$stmt->bindValue('decimalcount', $Data['decimalcount']);
		$stmt->bindValue('thousandseparator', $Data['thousandseparator']);
		$stmt->bindValue('positivepreffix', $Data['positivepreffix']);
		$stmt->bindValue('positivesuffix', $Data['positivesuffix']);
		$stmt->bindValue('negativepreffix', $Data['negativepreffix']);
		$stmt->bindValue('negativesuffix', $Data['negativesuffix']);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CURRENCY_ADD'), 4, $e->getMessage());
		}
		$id = Db::getInstance()->lastInsertId();
		
		$sql = 'INSERT INTO currencyrates SET
				currencyfrom = :currencyfrom,
				currencyto = :currencyto,
				exchangerate = :exchangerate';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('currencyfrom', $id);
		$stmt->bindValue('currencyto', $id);
		$stmt->bindValue('exchangerate', 1);
		$stmt->execute();
		
		foreach ($Data as $key => $val){
			if (substr($key, 0, 8) == 'currency'){
				$sql = 'INSERT INTO currencyrates SET
						currencyfrom = :currencyfrom,
						currencyto = :currencyto,
						exchangerate = :exchangerate';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('currencyfrom', $id);
				$stmt->bindValue('currencyto', substr($key, 9));
				$stmt->bindValue('exchangerate', $val);
				$stmt->execute();
			}
		}
		
		$this->addCurrencyView($Data['view'], $id);
		$this->flushCache();
		return true;
	}

	public function addCurrencyView ($Data, $id)
	{
		foreach ($Data as $value){
			$sql = 'INSERT INTO currencyview (currencyid, viewid)
						VALUES (:currencyid, :viewid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('currencyid', $id);
			$stmt->bindValue('viewid', $value);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CURRENCY_VIEW_ADD'), 4, $e->getMessage());
			}
		}
	}

	public function editCurrencieslist ($Data, $id)
	{
		
		$sql = 'UPDATE currency SET
						currencyname = :name, 
						currencysymbol = :symbol,
						decimalseparator = :decimalseparator,
						decimalcount = :decimalcount,
						thousandseparator = :thousandseparator,
						positivepreffix = :positivepreffix,
						positivesuffix = :positivesuffix,
						negativepreffix = :negativepreffix,
						negativesuffix = :negativesuffix
					WHERE idcurrency = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $Data['name']);
		$stmt->bindValue('symbol', $Data['symbol']);
		$stmt->bindValue('decimalseparator', $Data['decimalseparator']);
		$stmt->bindValue('decimalcount', $Data['decimalcount']);
		$stmt->bindValue('thousandseparator', $Data['thousandseparator']);
		$stmt->bindValue('positivepreffix', $Data['positivepreffix']);
		$stmt->bindValue('positivesuffix', $Data['positivesuffix']);
		$stmt->bindValue('negativepreffix', $Data['negativepreffix']);
		$stmt->bindValue('negativesuffix', $Data['negativesuffix']);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CURRENCIES_LIST_ADD'), 4, $e->getMessage());
		}
		$this->editCurrencyView($Data['view'], $id);
		
		DbTracker::deleteRows('currencyrates', 'currencyfrom', $id);
		
		foreach ($Data as $key => $val){
			if (substr($key, 0, 8) == 'currency'){
				
				$sql = 'INSERT INTO currencyrates SET
							currencyfrom = :currencyfrom, 
							currencyto = :currencyto, 
							exchangerate = :exchangerate';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('currencyfrom', $id);
				$stmt->bindValue('currencyto', substr($key, 9));
				$stmt->bindValue('exchangerate', $val);
				$stmt->execute();
			}
		}
		
		$this->flushCache();
		return true;
	}

	public function editCurrencyView ($Data, $id)
	{
		DbTracker::deleteRows('currencyview', 'currencyid', $id);
		
		if (! empty($Data)){
			foreach ($Data as $value){
				$sql = 'INSERT INTO currencyview (currencyid, viewid)
							VALUES (:currencyid, :viewid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('currencyid', $id);
				$stmt->bindValue('viewid', $value);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_CURRENCY_VIEW_EDIT'), 4, $e->getMessage());
				}
			}
		}
	}

	public function getCurrencieslistView ($id)
	{
		$sql = "SELECT 
					currencyname, 
					currencysymbol,
					decimalseparator,
					decimalcount,
					thousandseparator,
					positivepreffix,
					positivesuffix,
					negativepreffix,
					negativesuffix
				FROM currency 
				WHERE idcurrency =:id
		";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'name' => $rs['currencyname'],
				'symbol' => $rs['currencysymbol'],
				'decimalseparator' => $rs['decimalseparator'],
				'decimalcount' => $rs['decimalcount'],
				'thousandseparator' => $rs['thousandseparator'],
				'positivepreffix' => $rs['positivepreffix'],
				'positivesuffix' => $rs['positivesuffix'],
				'negativepreffix' => $rs['negativepreffix'],
				'negativesuffix' => $rs['negativesuffix'],
				'exchangerates' => $this->getExchangeRatesForCurrency($id),
				'view' => $this->getCurrencyView($id)
			);
		}
		return $Data;
	}

	public function getCurrencyView ($id)
	{
		$sql = "SELECT viewid
					FROM currencyview
					WHERE currencyid =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['viewid'];
		}
		return $Data;
	}

	public function getExchangeRatesForCurrency ($id)
	{
		$sql = "SELECT 
					CR.currencyto,
					CR.exchangerate 
				FROM currencyrates CR 
				WHERE CR.currencyfrom = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data['currency_' . $rs['currencyto']] = $rs['exchangerate'];
		}
		
		return $Data;
	}

	public function getCurrencies ()
	{
		$sql = 'SELECT CR.idcurrency, CR.currencyname, CR.currencysymbol
					FROM currency CR 
					ORDER BY currencysymbol ASC';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function getCurrencyForSelect ()
	{
		$results = $this->getCurrencies();
		$Data = Array();
		
		foreach ($results as $value){
			$Data[$value['idcurrency']] = $value['currencysymbol'] . " (" . $value['currencyname'] . ") ";
		}
		return $Data;
	}

	public function getCurrencyIds ()
	{
		$results = $this->getCurrencies();
		$Data = Array();
		
		foreach ($results as $value){
			$Data[$value['currencysymbol']] = $value['idcurrency'];
		}
		return $Data;
	}

	public function flushCache ()
	{
		App::getContainer()->get('cache')->delete('currencies');
	}
}