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
 * $Revision: 438 $
 * $Author: gekosale $
 * $Date: 2011-08-27 11:29:36 +0200 (So, 27 sie 2011) $
 * $Id: vat.php 438 2011-08-27 09:29:36Z gekosale $ 
 */
namespace Gekosale\Plugin;

class VatModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('VAT', Array(
			'idvat' => Array(
				'source' => 'V.idvat'
			),
			'name' => Array(
				'source' => 'VT.name'
			),
			'value' => Array(
				'source' => 'V.value'
			),
			'productcount' => Array(
				'source' => 'COUNT(P.idproduct)',
				'filter' => 'having'
			),
			'adddate' => Array(
				'source' => 'V.adddate'
			)
		));
		
		$datagrid->setFrom('
			`vat` V
			LEFT JOIN vattranslation VT ON VT.vatid = V.idvat AND VT.languageid = :languageid
			LEFT JOIN `product` P ON P.vatid = V.idvat
		');
		
		$datagrid->setGroupBy('
			V.idvat
		');
	}

	public function getValueForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('value', $request, $processFunction);
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getVATForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteVAT ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteVAT'
		), $this->getName());
	}

	public function checkVatProduct ($id)
	{
		$sql = "SELECT COUNT(idproduct) as total FROM `product` WHERE vatid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		return $rs['total'];
	}

	public function deleteVAT ($id)
	{
		$sql = "SELECT COUNT(idvat) as total FROM `vat` WHERE idvat != :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs['total'] > 0){
			$productTotal = $this->checkVatProduct($id);
			if ($productTotal == 0){
				return DbTracker::deleteRows('vat', 'idvat', $id);
			}
			else{
				return Array(
					'error' => $this->trans('ERR_VAT_BIND_TO_PRODUCT')
				);
			}
		}
		else{
			return Array(
				'error' => $this->trans('ERR_DELETE_ONLY_VAT')
			);
		}
	}

	public function getVATValuesAll ()
	{
		$sql = 'SELECT V.idvat AS id, V.value FROM vat V';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[$rs['id']] = $rs['value'];
		}
		return $Data;
	}

	public function getVATAll ()
	{
		$sql = 'SELECT 
					V.idvat AS id,
					V.value,
					VT.name 
				FROM vat V
				LEFT JOIN vattranslation VT ON VT.vatid = V.idvat AND VT.languageid = :languageid
				ORDER BY V.value DESC';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[$rs['id']] = $rs['name'];
		}
		return $Data;
	}

	public function getVATAllForRangeEditor ()
	{
		$sql = 'SELECT V.idvat AS id, V.value,	VT.name 
					FROM vat V
					LEFT JOIN vattranslation VT ON VT.vatid = V.idvat AND VT.languageid = :languageid';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[$rs['id']] = $rs['value'];
		}
		return $Data;
	}

	public function getVATAllToSelect ()
	{
		$Data = $this->getVATAll();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[$key['id']] = $key['value'];
		}
		return $tmp;
	}

	public function getVatAsExchangeOptions ()
	{
		$Data = $this->getVATAll();
		$tmp = Array();
		foreach ($Data as $key => $val){
			$tmp[] = Array(
				'sValue' => $key,
				'sLabel' => $val
			);
		}
		return $tmp;
	}

	public function addEmptyVat ($request)
	{
		$request['name'] = number_format($request['name'], 2, '.', '');
		if (! is_numeric($request['name'])){
			return Array(
				'error' => 'Musisz podać wartość liczbową'
			);
		}
		$sql = 'SELECT idvat FROM vat WHERE value = :value';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('value', $request['name']);
		$stmt->execute();
		$rs = $stmt->fetch();
		
		if ($rs){
			$id = $rs['idvat'];
		}
		else{
			
			$id = $this->addVAT($request['name']);
			$sql = 'INSERT INTO vattranslation (vatid, name, languageid)
					VALUES (:vatid, :name, :languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('vatid', $id);
			$stmt->bindValue('name', 'VAT ' . $request['name']);
			$stmt->bindValue('languageid', Helper::getLanguageId());
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_DELIVERER_TRANSLATION_EDIT'), 10, $e->getMessage());
			}
		}
		
		return Array(
			'id' => $id,
			'options' => $this->getVatAsExchangeOptions(),
			'vat' => App::getModel('vat/vat')->getVATValuesAll()
		);
	}

	public function getVATTranslation ($id)
	{
		$sql = "SELECT name, languageid
					FROM vattranslation
					WHERE vatid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['languageid']] = Array(
				'name' => $rs['name']
			);
		}
		return $Data;
	}

	public function editVAT ($Data, $id)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->updateVAT($Data['value'], $id);
			$this->updateVatTranslation($Data['name'], $id);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_VAT_EDIT'), 125, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function updateVAT ($value, $id)
	{
		$sql = 'UPDATE vat SET value=:value WHERE idvat = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('value', $value);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($e->getMessage());
		}
	}

	public function updateVatTranslation ($Data, $id)
	{
		DbTracker::deleteRows('vattranslation', 'vatid', $id);
		
		foreach ($Data as $key => $value){
			$sql = 'INSERT INTO vattranslation SET
							vatid = :vatid,
							name = :name, 
							languageid = :languageid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('vatid', $id);
			$stmt->bindValue('name', $value);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_VAT_TRANSLATION_ADD'), 4, $e->getMessage());
			}
		}
	}

	public function getVATView ($id)
	{
		$sql = "SELECT idvat AS id, value
					FROM vat
					WHERE idvat = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data = Array(
				'value' => $rs['value'],
				'id' => $rs['id'],
				'language' => $this->getVATTranslation($id)
			);
		}
		return $Data;
	}

	public function addNewVAT ($Data)
	{
		Db::getInstance()->beginTransaction();
		try{
			$newvatid = $this->addVAT($Data['value']);
			if (is_array($Data['name']) && ! empty($Data['name'])){
				$this->addVatTranslation($Data['name'], $newvatid);
			}
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_VAT_ADD'), 11, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function addVAT ($value)
	{
		$sql = 'INSERT INTO `vat` (value) VALUES (:value)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('value', $value);
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_VAT_ADD'), 11, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function addVatTranslation ($Data, $id)
	{
		foreach ($Data as $key => $value){
			$sql = 'INSERT INTO vattranslation SET
						vatid = :vatid,
						name = :name, 
						languageid = :languageid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('vatid', $id);
			$stmt->bindValue('name', $value);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_VAT_TRANSLATION_ADD'), 4, $e->getMessage());
			}
		}
	}

	public function checkVAT ($value)
	{
		if ($value == ''){
			return true;
		}
		$value = trim($value);
		if (is_numeric(substr($value, 0, 1))){
			$vatNumber = $value;
		}
		else{
			if (substr($value, 0, 2) == 'PL'){
				$vatNumber = substr($value, 2);
			}
			else{
				$countryCode = substr($value, 0, 2);
				$countryNip = substr($value, 2);
				$client = new \SoapClient("http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl");
				$check_result = $client->checkVat(array(
					'countryCode' => $countryCode,
					'vatNumber' => (string) $countryNip
				));
				return $check_result->valid;
			}
		}
		
		$vatNumber = str_replace(array(
			' ',
			'-'
		), '', $vatNumber);
		if (strlen($vatNumber) != 10){
			return false;
		}
		$steps = array(
			6,
			5,
			7,
			2,
			3,
			4,
			5,
			6,
			7
		);
		$sum = 0;
		for ($i = 0; $i < 9; $i ++){
			$sum += $steps[$i] * $vatNumber[$i];
		}
		$tmp = $sum % 11;
		
		$control = ($tmp == 10) ? 0 : $tmp;
		if ($control == $vatNumber[9]){
			return true;
		}
		return false;
	}
}