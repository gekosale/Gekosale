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
 * $Id: store.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale;

class StoreModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('store', Array(
			'idstore' => Array(
				'source' => 'idstore'
			),
			'name' => Array(
				'source' => 'shortcompanyname'
			)
		));
		
		$datagrid->setFrom('
			store
		');
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getStoreForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteStore ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteStore'
		), $this->getName());
	}

	public function deleteStore ($id)
	{
		$sql = "SELECT COUNT(idview) as total FROM `view` WHERE storeid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs['total'] == 0){
			DbTracker::deleteRows('store', 'idstore', $id);
		}
		else{
			return Array(
				'error' => 'Nie możesz skasować firmy mającej dodane sklepy.'
			); 
		}
	}

	public function addStore ($Data)
	{
		$sql = 'INSERT INTO store (
					countryid,
					defaultphotoid,
					bankname,
					banknr,
					krs,
					nip,
					companyname,
					shortcompanyname,
					placename,
					postcode,
					street,
					streetno,
					placeno,
					province,
					invoiceshopslogan,
					isinvoiceshopslogan,
					isinvoiceshopname)
				VALUES (
					:countryid,
					:defaultphotoid,
					:bankname,
					:banknr,
					:krs,
					:nip,
					:companyname,
					:shortcompanyname,
					:placename,
					:postcode,
					:street,
					:streetno,
					:placeno,
					:province,
					:invoiceshopslogan,
					:isinvoiceshopslogan,
					:isinvoiceshopname)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('countryid', $Data['countries']);
		$stmt->bindValue('bankname', $Data['bankname']);
		$stmt->bindValue('banknr', $Data['banknr']);
		$stmt->bindValue('krs', $Data['krs']);
		$stmt->bindValue('nip', $Data['nip']);
		$stmt->bindValue('companyname', $Data['companyname']);
		$stmt->bindValue('shortcompanyname', $Data['shortcompanyname']);
		$stmt->bindValue('placename', $Data['placename']);
		$stmt->bindValue('postcode', $Data['postcode']);
		$stmt->bindValue('street', $Data['street']);
		$stmt->bindValue('streetno', $Data['streetno']);
		$stmt->bindValue('placeno', $Data['placeno']);
		$stmt->bindValue('province', $Data['province']);
		if (isset($Data['isinvoiceshopslogan']['value']) && $Data['isinvoiceshopslogan']['value'] == 2){
			$stmt->bindValue('isinvoiceshopslogan', 1);
			$stmt->bindValue('isinvoiceshopname', 0);
			$stmt->bindValue('invoiceshopslogan', $Data['invoiceshopslogan']);
		}
		else{
			$stmt->bindValue('isinvoiceshopslogan', 0);
			$stmt->bindValue('isinvoiceshopname', 1);
			$stmt->bindValue('invoiceshopslogan', '');
		}
		
		if (($Data['photo'][0]) > 0){
			$stmt->bindValue('defaultphotoid', $Data['photo'][0]);
		}
		else{
			$stmt->bindValue('defaultphotoid', NULL);
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERROR_STORE_ADD'), 18, $e->getMessage());
		}
		$this->flushCache();
	}

	public function getStoreView ($id)
	{
		$sql = 'SELECT
					S.countryid,
					C.idcountry,
					C.name as countryname,
					S.defaultphotoid,
					S.bankname,
					S.banknr,
					S.krs,
					S.nip,
					S.companyname,
					S.shortcompanyname,
					S.placename,
					S.postcode,
					S.street,
					S.streetno,
					S.placeno,
					S.province,
					S.invoiceshopslogan,
					S.isinvoiceshopslogan,
					S.isinvoiceshopname
				FROM store S
				LEFT JOIN country C ON S.countryid = C.idcountry
				WHERE idstore =:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'countryid' => $rs['countryid'],
				'idcountry' => $rs['idcountry'],
				'countryname' => $rs['countryname'],
				'photo' => $rs['defaultphotoid'],
				'bankname' => $rs['bankname'],
				'banknr' => $rs['banknr'],
				'krs' => $rs['krs'],
				'nip' => $rs['nip'],
				'companyname' => $rs['companyname'],
				'shortcompanyname' => $rs['shortcompanyname'],
				'placename' => $rs['placename'],
				'postcode' => $rs['postcode'],
				'street' => $rs['street'],
				'streetno' => $rs['streetno'],
				'placeno' => $rs['placeno'],
				'province' => $rs['province'],
				'invoiceshopslogan' => $rs['invoiceshopslogan'],
				'isinvoiceshopslogan' => $rs['isinvoiceshopslogan'],
				'isinvoiceshopname' => $rs['isinvoiceshopname'],
				'gallerysettings' => $this->getGallerySettings()
			);
		}
		return $Data;
	}

	public function editStore ($Data, $id)
	{
		$sql = 'UPDATE store SET
					countryid=:countryid,
	  				defaultphotoid=:defaultphotoid,
	  				bankname=:bankname,
	  				banknr=:banknr,
	  				krs=:krs,
	  				nip=:nip,
					companyname=:companyname,
					shortcompanyname=:shortcompanyname,
					placename=:placename,
					postcode=:postcode,
					street=:street,
					streetno=:streetno,
					placeno=:placeno,
					province=:province,
					invoiceshopslogan=:invoiceshopslogan,
					isinvoiceshopslogan=:isinvoiceshopslogan,
					isinvoiceshopname=:isinvoiceshopname
				WHERE idstore = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('countryid', $Data['countries']);
		$stmt->bindValue('bankname', $Data['bankname']);
		$stmt->bindValue('banknr', $Data['banknr']);
		$stmt->bindValue('krs', $Data['krs']);
		$stmt->bindValue('nip', $Data['nip']);
		$stmt->bindValue('companyname', $Data['companyname']);
		$stmt->bindValue('shortcompanyname', $Data['shortcompanyname']);
		$stmt->bindValue('placename', $Data['placename']);
		$stmt->bindValue('postcode', $Data['postcode']);
		$stmt->bindValue('street', $Data['street']);
		$stmt->bindValue('streetno', $Data['streetno']);
		$stmt->bindValue('placeno', $Data['placeno']);
		$stmt->bindValue('province', $Data['province']);
		if (isset($Data['isinvoiceshopslogan']['value']) && $Data['isinvoiceshopslogan']['value'] == 2){
			// shop name with tag
			$stmt->bindValue('isinvoiceshopslogan', 1);
			$stmt->bindValue('isinvoiceshopname', 0);
			$stmt->bindValue('invoiceshopslogan', $Data['invoiceshopslogan']);
		}
		else{
			// only shop name
			$stmt->bindValue('isinvoiceshopslogan', 0);
			$stmt->bindValue('isinvoiceshopname', 1);
			$stmt->bindValue('invoiceshopslogan', '');
		}
		// $stmt->bindValue('storeid', $Data['storeid']);
		if (($Data['photo'][0]) > 0){
			$stmt->bindValue('defaultphotoid', $Data['photo'][0]);
		}
		else{
			$stmt->bindValue('defaultphotoid', NULL);
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_STORE_EDIT'), 18, $e->getMessage());
		}
		$this->flushCache();
	}

	public function getGallerySettings ()
	{
		$sql = 'SELECT width, height, keepproportion FROM gallerysettings WHERE width IS NOT NULL';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data['width'][] = $rs['width'];
			$Data['height'][] = $rs['height'];
			$Data['keepproportion'][] = $rs['keepproportion'];
		}
		return $Data;
	}

	public function getStoreAll ()
	{
		$sql = 'SELECT
					idstore AS id,
					shortcompanyname
				FROM store';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'name' => $rs['shortcompanyname']
			);
		}
		return $Data;
	}

	public function getStoreToSelect ()
	{
		$Data = $this->getStoreAll();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[$key['id']] = $key['name'];
		}
		return $tmp;
	}

	public function flushCache ()
	{
		App::getContainer()->get('cache')->delete('views');
	}
}