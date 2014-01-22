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
 * $Revision: 309 $
 * $Author: gekosale $
 * $Date: 2011-08-01 21:10:16 +0200 (Pn, 01 sie 2011) $
 * $Id: exchange.php 309 2011-08-01 19:10:16Z gekosale $
 */
namespace Gekosale\Plugin;

@set_time_limit(0);
class MigrationModel extends Component\Model
{
	protected $url;
	protected $key;

	public function doLoadQueque ()
	{
		$params = json_decode(App::getContainer()->get('session')->getActiveMigrationData(), true);
		$this->url = $params['apiurl'];
		$this->key = $params['apikey'];

		switch ($params['entity']) {
			case 1:
				$request = $this->getProductIds();
				App::getContainer()->get('cache')->save('migration_products', $request['ids']);
				break;
			case 2:
				$request = $this->getCategoryIds();
				App::getContainer()->get('cache')->save('migration_categories', $request['ids']);
				break;
			case 3:
				$request = $this->getProducerIds();
				App::getContainer()->get('cache')->save('migration_producers', $request['ids']);
				break;
			case 4:
				$request = $this->getPhotosTotal();
				App::getContainer()->get('cache')->save('migration_photos', $request['ids']);
				break;
			case 5:
				$name = 'Migration Group' . mt_rand(1, 10000);
				$groupId = App::getModel('attributegroup')->addEmptyGroup(array(
					'name' => $name
				));
				App::getContainer()->get('session')->setActiveMigrationGroupName($name);
				App::getContainer()->get('session')->setActiveMigrationGroupId($groupId['id']);

				$request = $this->getAttributesIds();
				App::getContainer()->get('cache')->save('migration_attributes', $request['ids']);
				break;
			case 6:
				$request = $this->getOrderIds();
				App::getContainer()->get('cache')->save('migration_orders', $request['ids']);
				break;
			case 7:
				$request = $this->getSimilarProductsIds();
				App::getContainer()->get('cache')->save('migration_similarproducts', $request['ids']);
				break;
		}

		return Array(
			'iTotal' => count($request['ids']),
			'iCompleted' => 0
		);
	}

	public function doProcessQueque ($request)
	{
		$params = json_decode(App::getContainer()->get('session')->getActiveMigrationData(), true);
		$this->url = $params['apiurl'];
		$this->key = $params['apikey'];

		$startFrom = intval($request['iStartFrom']);

		$offset = Array(
			'offset' => $startFrom
		);

		switch ($params['entity']) {
			case 1:
				$Data = App::getContainer()->get('cache')->load('migration_products');
				$id = array_shift($Data);
				$response = $this->getProduct($id);
				if (isset($response['product'])){
					$this->addUpdateProduct($response['product']);
				}
				App::getContainer()->get('cache')->save('migration_products', $Data);
				break;
			case 2:
				$Data = App::getContainer()->get('cache')->load('migration_categories');
				$id = array_shift($Data);
				$response = $this->getCategory($id);
				if (isset($response['category'])){
					Db::getInstance()->beginTransaction();
					$this->addUpdateCategory($response['category']);
					Db::getInstance()->commit();
				}
				App::getContainer()->get('cache')->save('migration_categories', $Data);
				break;
			case 3:
				$Data = App::getContainer()->get('cache')->load('migration_producers');
				$id = array_shift($Data);
				$response = $this->getProducer($id);
				if (isset($response['producer'])){
					Db::getInstance()->beginTransaction();
					$this->addUpdateProducer($response['producer']);
					Db::getInstance()->commit();
				}
				App::getContainer()->get('cache')->save('migration_producers', $Data);
				break;
			case 4:
				$response = $this->getPhoto($offset);
				if (isset($response['photo'])){
					$sql = 'SELECT idfile FROM file WHERE name = :name';
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('name', Core::clearUTF($response['photo']['name']));
					$stmt->execute();
					$rs = $stmt->fetch();
					if ($rs){
					}
					else{
						App::getModel('gallery')->getRemoteImage($response['photo']['url'], $response['photo']['name']);
					}
				}
				break;
			case 5:
				$Data = App::getContainer()->get('cache')->load('migration_attributes');
				$id = array_shift($Data);
				$response = $this->getAttributes($id);
				if (isset($response['attributes'])){
					Db::getInstance()->beginTransaction();
					$this->addUpdateAttributes($response['attributes']);
					Db::getInstance()->commit();
				}
				App::getContainer()->get('cache')->save('migration_attributes', $Data);
				break;
			case 6:
				$Data = App::getContainer()->get('cache')->load('migration_orders');
				$id = array_shift($Data);
				$response = $this->getOrder($id);
				if (isset($response['order'])){
					$this->addOrder($response['order']);
				}
				App::getContainer()->get('cache')->save('migration_orders', $Data);
				break;
			case 7:
				$Data = App::getContainer()->get('cache')->load('migration_similarproducts');
				$id = array_shift($Data);
				$response = $this->getSimilarProducts($id);
				Db::getInstance()->beginTransaction();
				if (isset($response['similarproducts'])){
					$this->addUpdateSimilarProducts($id, $response['similarproducts']);
				}
				Db::getInstance()->commit();
				App::getContainer()->get('cache')->save('migration_similarproducts', $Data);
				break;
		}

		if ($startFrom + 1 <= intval($request['iTotal'])){
			return Array(
				'iStartFrom' => $startFrom + 1
			);
		}
		else{
			return Array(
				'iStartFrom' => $startFrom,
				'bFinished' => true
			);
		}
	}

	public function doSuccessQueque ($request)
	{
		$params = json_decode(App::getContainer()->get('session')->getActiveMigrationData(), true);
		if ($params['entity'] == 2){
			$this->updateParentCategories();
			App::getModel('category')->getCategoriesPathById();
			App::getModel('seo')->doRefreshSeoCategory();
		}
		else if ($params['entity'] == 1){
			App::getModel('product')->updateProductAttributesetPricesAll();
		}

		if ($request['bFinished']){
			return Array(
				'bCompleted' => true
			);
		}
	}

	public function __call ($method, $params)
	{
		if (is_array($params)){
			$params = array_values($params);
		}
		else{
			throw new Exception('Params must be given as array');
		}

		$request = array(
			'method' => $method,
			'params' => $params,
			'key' => $this->key
		);
		$request = json_encode($request);
		$curl = curl_init($this->url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Content-type: application/json'
		));
		$response = curl_exec($curl);
		curl_close($curl);
		$response = json_decode($response, true);
		if (isset($response['error']) && ! is_null($response['error'])){
			return Array();
		}
		return $response['result'];
	}

	protected function addOrder ($Data)
	{
		Db::getInstance()->beginTransaction();
		$email = $Data['email'];
		$password = Core::passwordGenerate();

		$hash = new \PasswordHash\PasswordHash();
		$sql = 'SELECT idclient FROM client WHERE login = :login';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('login', $hash->HashLogin($email));
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			// Update
		}
		else{
			$sql = 'INSERT INTO client (login, password, disable, viewid)
					VALUES (:login, :password, :disable, :viewid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('login', $hash->HashLogin($email));
			$stmt->bindValue('password', $hash->HashPassword($password));
			$stmt->bindValue('disable', isset($Data['disable']) ? $Data['disable'] : 0);
			$stmt->bindValue('viewid', Helper::getViewId());
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new FrontendException($e->getMessage());
			}

			$idClient = Db::getInstance()->lastInsertId();

			$sql = 'INSERT INTO clientdata SET
					firstname = AES_ENCRYPT(:firstname, :encryptionKey),
					surname = AES_ENCRYPT(:surname, :encryptionKey),
					email = AES_ENCRYPT(:email, :encryptionKey),
					phone = AES_ENCRYPT(:phone, :encryptionKey),
					phone2 = AES_ENCRYPT(:phone2, :encryptionKey),
					description = AES_ENCRYPT(:description, :encryptionKey),
					clientgroupid = 10,
					clientid = :clientid
			';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('clientid', $idClient);
			$stmt->bindValue('firstname', $Data['firstname']);
			$stmt->bindValue('surname', $Data['surname']);
			$stmt->bindValue('email', $Data['email']);
			$stmt->bindValue('phone', $Data['phone']);
			$stmt->bindValue('phone2', !empty($Data['phone2']) ? $Data['phone2'] : '');
			$stmt->bindValue('description', !empty($Data['description']) ? $Data['description'] : '');
			$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());

			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new FrontendException($e->getMessage());
			}

			$sql = 'INSERT INTO clientaddress SET
					clientid	= :clientid,
					main		= :main,
					firstname 	= AES_ENCRYPT(:firstname, :encryptionKey),
					surname   	= AES_ENCRYPT(:surname, :encryptionKey),
					companyname	= AES_ENCRYPT(:companyname, :encryptionKey),
					street		= AES_ENCRYPT(:street, :encryptionKey),
					streetno	= AES_ENCRYPT(:streetno, :encryptionKey),
					placeno		= AES_ENCRYPT(:placeno, :encryptionKey),
					postcode	= AES_ENCRYPT(:postcode, :encryptionKey),
					nip		= AES_ENCRYPT(:nip, :encryptionKey),
					placename	= AES_ENCRYPT(:placename, :encryptionKey),
					countryid	= :countryid
				ON DUPLICATE KEY UPDATE
					firstname 	= AES_ENCRYPT(:firstname, :encryptionKey),
					surname   	= AES_ENCRYPT(:surname, :encryptionKey),
					companyname	= AES_ENCRYPT(:companyname, :encryptionKey),
					street		= AES_ENCRYPT(:street, :encryptionKey),
					streetno	= AES_ENCRYPT(:streetno, :encryptionKey),
					placeno		= AES_ENCRYPT(:placeno, :encryptionKey),
					postcode	= AES_ENCRYPT(:postcode, :encryptionKey),
					nip		= AES_ENCRYPT(:nip, :encryptionKey),
					placename	= AES_ENCRYPT(:placename, :encryptionKey),
					countryid	= :countryid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
			$stmt->bindValue('clientid', $idClient);
			$stmt->bindValue('main', 1);
			$stmt->bindValue('firstname', $Data['firstname']);
			$stmt->bindValue('surname', $Data['surname']);
			$stmt->bindValue('companyname', $Data['companyname']);
			$stmt->bindValue('street', $Data['street']);
			$stmt->bindValue('streetno', $Data['streetno']);
			$stmt->bindValue('postcode', $Data['postcode']);
			$stmt->bindValue('placeno', $Data['placeno']);
			$stmt->bindValue('nip', $Data['nip']);
			$stmt->bindValue('placename', $Data['placename']);
			$stmt->bindValue('countryid', $this->getCountryByName($Data['country']));
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new FrontendException($e->getMessage());
			}


			$sql = 'INSERT INTO clientaddress SET
					clientid	= :clientid,
					main		= :main,
					firstname 	= AES_ENCRYPT(:firstname, :encryptionKey),
					surname   	= AES_ENCRYPT(:surname, :encryptionKey),
					companyname	= AES_ENCRYPT(:companyname, :encryptionKey),
					street		= AES_ENCRYPT(:street, :encryptionKey),
					streetno	= AES_ENCRYPT(:streetno, :encryptionKey),
					placeno		= AES_ENCRYPT(:placeno, :encryptionKey),
					postcode	= AES_ENCRYPT(:postcode, :encryptionKey),
					nip		= AES_ENCRYPT(:nip, :encryptionKey),
					placename	= AES_ENCRYPT(:placename, :encryptionKey),
					countryid	= :countryid
				ON DUPLICATE KEY UPDATE
					firstname 	= AES_ENCRYPT(:firstname, :encryptionKey),
					surname   	= AES_ENCRYPT(:surname, :encryptionKey),
					companyname	= AES_ENCRYPT(:companyname, :encryptionKey),
					street		= AES_ENCRYPT(:street, :encryptionKey),
					streetno	= AES_ENCRYPT(:streetno, :encryptionKey),
					placeno		= AES_ENCRYPT(:placeno, :encryptionKey),
					postcode	= AES_ENCRYPT(:postcode, :encryptionKey),
					nip		= AES_ENCRYPT(:nip, :encryptionKey),
					placename	= AES_ENCRYPT(:placename, :encryptionKey),
					countryid	= :countryid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
			$stmt->bindValue('clientid', $idClient);
			$stmt->bindValue('main', 0);
			$stmt->bindValue('firstname', $Data['firstname']);
			$stmt->bindValue('surname', $Data['surname']);
			$stmt->bindValue('companyname', $Data['companyname']);
			$stmt->bindValue('street', !empty($Data['street2']) ? $Data['street2'] : $Data['street']);
			$stmt->bindValue('streetno', !empty($Data['streetno2']) ? $Data['streetno2'] : $Data['streetno']);
			$stmt->bindValue('postcode', !empty($Data['postcode2']) ? $Data['postcode2'] : $Data['postcode']);
			$stmt->bindValue('placeno', !empty($Data['placeno2']) ? $Data['placeno2'] : $Data['placeno']);
			$stmt->bindValue('nip', $Data['nip']);
			$stmt->bindValue('placename', !empty($Data['placename2']) ? $Data['placename2'] : $Data['placename']);
			$stmt->bindValue('countryid', $this->getCountryByName(!empty($Data['country2']) ? $Data['country2'] : $Data['country']));
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new FrontendException($e->getMessage());
			}

		}
		Db::getInstance()->commit();
	}

	protected function addUpdateAttributes ($Data)
	{
		$model = App::getModel('attributegroup');

		list($name, $id) = explode(':', key($Data));

		$Data = $Data[key($Data)];

		$values = array();
		foreach ($Data as $val){
			$values[] = array(
				'id' => 'new-' . $val['id'],
				'name' => $val['name']
			);
		}

		$data = array(
			'attributegroupname' => App::getContainer()->get('session')->getActiveMigrationGroupName(),
			'category' => '',
			'attributes' => array(
				'editor' => array(
					0 => array(
						'id' => 'new-' . $id,
						'name' => $name,
						'values' => $values
					)
				),
				0 => 'new-' . $id
			)
		);

		$id = App::getContainer()->get('session')->getActiveMigrationGroupId();

		$model->editMigrationAttributeGroup($data, $id);
	}

	protected function addUpdateProducer ($Data)
	{
		$viewid = (Helper::getViewId() > 0) ? Helper::getViewId() : $this->registry->loader->getParam('idview');

		if ( !empty($Data['photo'])) {
			$sqlPhoto = 'SELECT idfile FROM file WHERE name = :name';
			$stmtPhoto = Db::getInstance()->prepare($sqlPhoto);
			$stmtPhoto->bindValue('name', Core::clearUTF($Data['photo']['name']));
			$stmtPhoto->execute();
			$rsPhoto = $stmtPhoto->fetch();
			if ($rsPhoto){
				$photoid = $rsPhoto['idfile'];
			}
			else{
				$photoid = App::getModel('gallery')->getRemoteImage($Data['photo']['url'], $Data['photo']['name']);
			}
		}
		else {
			$photoid = NULL;
		}

		$sql = 'SELECT idproducer FROM producer WHERE migrationid = :migrationid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('migrationid', $Data['id']);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$producerid = $rs['idproducer'];

			$sql = 'UPDATE producer SET photoid = :photoid WHERE migrationid = :migrationid';
			$stmt = Db::getInstance()->prepare($sql);

			$stmt->bindValue('photoid', $photoid);
			$stmt->bindValue('migrationid', $Data['id']);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception(nl2br($e->getMessage()) . '<br />' . "\n");
			}
		}

		$sql = 'SELECT producerid FROM producertranslation WHERE name = :name';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $Data['name']);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$producerid = $rs['producerid'];

			$sql = 'UPDATE producertranslation SET
					name = :name,
					seo = :seo,
					description = :description,
					keyword_title = :keyword_title,
					keyword = :keyword,
					keyword_description = :keyword_description,
					languageid = :languageid
				WHERE
					producerid = :producerid
				';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('producerid', $producerid);
			$stmt->bindValue('name', $Data['name']);
			$stmt->bindValue('description', isset($Data['description']) ? $Data['description'] : NULL);
			$stmt->bindValue('keyword_title', isset($Data['keyword_title']) ? $Data['keyword_title'] : NULL);
			$stmt->bindValue('keyword', isset($Data['keyword']) ? $Data['keyword'] : NULL);
			$stmt->bindValue('keyword_description', isset($Data['keyword_description']) ? $Data['keyword_description'] : NULL);
			$stmt->bindValue('seo', strtolower(Core::clearSeoUTF($Data['name'])));
			$stmt->bindValue('languageid', Helper::getLanguageId());
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception(nl2br($e->getMessage()) . '<br />' . "\n");
			}
		}
		else{
			$sql = 'INSERT INTO producer (photoid, migrationid) VALUES (:photoid, :migrationid)';
			$stmt = Db::getInstance()->prepare($sql);

			$stmt->bindValue('photoid', $photoid);
			$stmt->bindValue('migrationid', $Data['id']);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception(nl2br($e->getMessage()) . '<br />' . "\n");
			}

			$producerid = Db::getInstance()->lastInsertId();

			$sql = 'INSERT INTO producertranslation SET
					producerid = :producerid,
					name = :name,
					seo = :seo,
					description = :description,
					keyword_title = :keyword_title,
					keyword = :keyword,
					keyword_description = :keyword_description,
					languageid = :languageid
				';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('producerid', $producerid);
			$stmt->bindValue('name', $Data['name']);
			$stmt->bindValue('description', isset($Data['description']) ? $Data['description'] : NULL);
			$stmt->bindValue('keyword_title', isset($Data['keyword_title']) ? $Data['keyword_title'] : NULL);
			$stmt->bindValue('keyword', isset($Data['keyword']) ? $Data['keyword'] : NULL);
			$stmt->bindValue('keyword_description', isset($Data['keyword_description']) ? $Data['keyword_description'] : NULL);
			$stmt->bindValue('seo', strtolower(Core::clearSeoUTF($Data['name'])));
			$stmt->bindValue('languageid', Helper::getLanguageId());
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception(nl2br($e->getMessage()) . '<br />' . "\n");
			}

			$sql = 'INSERT INTO producerview (producerid,viewid)
					VALUES (:producerid, :viewid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('producerid', $producerid);
			$stmt->bindValue('viewid', $viewid);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception(nl2br($e->getMessage()) . '<br />' . "\n");
			}
		}
	}

	protected function addUpdateProduct ($Data)
	{
		Db::getInstance()->beginTransaction();

		$viewid = (Helper::getViewId() > 0) ? Helper::getViewId() : $this->registry->loader->getParam('idview');
		$sql = 'SELECT idproduct FROM product WHERE migrationid = :migrationid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('migrationid', $Data['id']);
		$stmt->execute();
		$rs = $stmt->fetch();

		$availablityid = NULL;
		if ( !empty($Data['availablity'])) {
			$sql = 'SELECT
					availablityid
				FROM
					availablitytranslation
				WHERE
					name = :name
				AND
					languageid = :languageid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('name', $Data['availablity']);
			$stmt->bindValue('languageid', Helper::getLanguageId());
			$stmt->execute();
			$availablityid = $stmt->fetchColumn();

			// Add
			if (empty($availablityid)) {
				$availablityid = App::getModel('availablity')->addAvailablity(array(
					'name' => array(
						Helper::getLanguageId() => $Data['availablity'],
					),
					'description' => array(
						Helper::getLanguageId() => '',
					)
				));
			}
		}

		if ($rs){
			$idproduct = $rs['idproduct'];
			$sql = 'UPDATE product SET
	    				ean				=	:ean,
	    				delivelercode	=	:ean,
	    				barcode			=	:ean,
						buyprice		=	:buyprice,
						sellprice		=	:sellprice,
						weight			=	:weight,
						enable			= 	:enable,
						availablityid		=	:availablityid,
						stock			= 	:stock,
						promotion		= 	:promotion,
						discountprice		= 	:discountprice
					WHERE idproduct = :id
			';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('ean', $Data['ean']);
			$stmt->bindValue('buyprice', $Data['buyprice']);
			$stmt->bindValue('sellprice', $Data['sellprice']);
			$stmt->bindValue('stock', $Data['stock']);
			$stmt->bindValue('weight', $Data['weight']);
			$stmt->bindValue('availablityid', $availablityid);
			$stmt->bindValue('enable', $Data['enable']);
			$stmt->bindValue('promotion', isset($Data['promotion']) ? $Data['promotion'] : 0);
			$stmt->bindValue('discountprice', isset($Data['discountprice']) ? $Data['discountprice'] : 0);
			$stmt->bindValue('id', $idproduct);
			$stmt->execute();

			$sql = 'UPDATE producttranslation SET
						name = :name,
						description = :description,
						shortdescription = :shortdescription,
						seo = :seo,
						keyword_title = :keyword_title,
						keyword = :keyword,
						keyword_description = :keyword_description
					WHERE productid = :productid AND languageid = :languageid
			';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productid', $idproduct);
			$stmt->bindValue('name', $Data['name']);
			$stmt->bindValue('description', $Data['description']);
			$stmt->bindValue('shortdescription', $Data['shortdescription']);
			$stmt->bindValue('keyword_title', $Data['keyword_title']);
			$stmt->bindValue('keyword', $Data['keyword']);
			$stmt->bindValue('keyword_description', $Data['keyword_description']);
			$stmt->bindValue('seo', str_replace('/', '', strtolower(Core::clearSeoUTF($Data['name']))));
			$stmt->bindValue('languageid', Helper::getLanguageId());
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_PRODUCT_UPDATE'), 112, $e->getMessage());
			}
			Db::getInstance()->commit();
		}
		else{
			$vatValues = array_flip(App::getModel('vat')->getVATValuesAll());
			$sql = 'INSERT INTO product SET
	    				ean				=	:ean,
	    				delivelercode	=	:ean,
	    				barcode			=	:ean,
						buyprice		=	:buyprice,
						sellprice		=	:sellprice,
						buycurrencyid   =	:buycurrencyid,
						sellcurrencyid  =	:sellcurrencyid,
						weight			=	:weight,
						availablityid		=	:availablityid,
						vatid			=	:vatid,
						producerid		=	(SELECT idproducer FROM producer WHERE migrationid = :producerid),
						enable			= 	:enable,
						stock			= 	:stock,
						promotion		= 	:promotion,
						discountprice		= 	:discountprice,
						migrationid		=   :migrationid
			';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('ean', $Data['ean']);
			$stmt->bindValue('buyprice', $Data['buyprice']);
			$stmt->bindValue('sellprice', $Data['sellprice']);
			$stmt->bindValue('buycurrencyid', App::getContainer()->get('session')->getActiveShopCurrencyId());
			$stmt->bindValue('sellcurrencyid', App::getContainer()->get('session')->getActiveShopCurrencyId());
			$stmt->bindValue('stock', $Data['stock']);
			$stmt->bindValue('weight', $Data['weight']);
			$stmt->bindValue('availablityid', $availablityid);
			if (isset($vatValues[number_format($Data['vatvalue'], 2)])){
				$stmt->bindValue('vatid', $vatValues[number_format($Data['vatvalue'], 2)]);
			}
			else{
				$stmt->bindValue('vatid', 2);
			}
			$stmt->bindValue('migrationid', $Data['id']);
			$stmt->bindValue('producerid', $Data['producerid']);
			$stmt->bindValue('enable', $Data['enable']);
			$stmt->bindValue('stock', $Data['stock']);
			$stmt->bindValue('promotion', isset($Data['promotion']) ? $Data['promotion'] : 0);
			$stmt->bindValue('discountprice', isset($Data['discountprice']) ? $Data['discountprice'] : 0);
			$stmt->execute();

			$idproduct = Db::getInstance()->lastInsertId();

			$sql = 'INSERT INTO producttranslation SET
						productid = :productid,
						name = :name,
						description = :description,
						shortdescription = :shortdescription,
						seo = :seo,
						keyword_title = :keyword_title,
						keyword = :keyword,
						keyword_description = :keyword_description,
						languageid = :languageid
			';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productid', $idproduct);
			$stmt->bindValue('name', $Data['name']);
			$stmt->bindValue('description', $Data['description']);
			$stmt->bindValue('shortdescription', $Data['shortdescription']);
			$stmt->bindValue('keyword_title', $Data['keyword_title']);
			$stmt->bindValue('keyword', $Data['keyword']);
			$stmt->bindValue('keyword_description', $Data['keyword_description']);
			$stmt->bindValue('seo', str_replace('/', '', strtolower(Core::clearSeoUTF($Data['name']))));
			$stmt->bindValue('languageid', Helper::getLanguageId());
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_PRODUCT_UPDATE'), 112, $e->getMessage());
			}

			foreach ($Data['categories'] as $category){
				$sql = 'INSERT INTO productcategory (productid, categoryid)
						SELECT :productid, idcategory FROM category WHERE migrationid = :categoryid';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('productid', $idproduct);
				$stmt->bindValue('categoryid', $category);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_PRODUCT_CATEGORY_UPDATE'), 112, $e->getMessage());
				}
			}

			if (isset($Data['recursivecategories'])){
				$sql = 'SELECT categoryid FROM productcategory WHERE productid = :productid';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('productid', $idproduct);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($e->getMessage());
				}
				while ($rs = $stmt->fetch()){
					$sql2 = 'SELECT ancestorcategoryid FROM categorypath WHERE categoryid = :categoryid';
					$stmt2 = Db::getInstance()->prepare($sql2);
					$stmt2->bindValue('categoryid', $rs['categoryid']);
					try{
						$stmt2->execute();
					}
					catch (Exception $e){
						throw new CoreException($e->getMessage());
					}
					while ($rs2 = $stmt2->fetch()){
						$sql3 = 'INSERT INTO productcategory SET productid = :productid, categoryid = :categoryid
								 ON DUPLICATE KEY UPDATE productid = :productid';
						$stmt3 = Db::getInstance()->prepare($sql3);
						$stmt3->bindValue('productid', $idproduct);
						$stmt3->bindValue('categoryid', $rs2['ancestorcategoryid']);
						try{
							$stmt3->execute();
						}
						catch (Exception $e){
							throw new CoreException($e->getMessage());
						}
					}
				}
			}
			Db::getInstance()->commit();

			$mainphoto = 1;
			foreach ($Data['photos'] as $name => $url){
				$sql = 'SELECT idfile FROM file WHERE name = :name';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('name', Core::clearUTF($name));
				$stmt->execute();
				$rs = $stmt->fetch();
				if ($rs){
					$photoid = $rs['idfile'];
				}
				else{
					$photoid = App::getModel('gallery')->getRemoteImage($url, $name);
				}

				$sql = 'INSERT INTO productphoto (productid, mainphoto, photoid)
						VALUES (:productid, :mainphoto, :photoid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('productid', $idproduct);
				$stmt->bindValue('mainphoto', $mainphoto);
				$stmt->bindValue('photoid', $photoid);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_PRODUCT_PHOTO_UPDATE'), 112, $e->getMessage());
				}
				$mainphoto = 0;
			}

			if (empty($Data['attributes'])){
				return;
			}

			$productModel = App::getModel('product');
			$attributes = array();
			$attributesData = array();
			foreach ($Data['attributes'] as $attributeName => $attributeValue){
				foreach ($attributeValue as $value){
					$sql = "
						SELECT
							APV.idattributeproductvalue,
							APV.attributeproductid
						FROM
							attributeproductvalue APV
						INNER JOIN
							attributeproduct AP ON AP.idattributeproduct = APV.attributeproductid
						INNER JOIN
							attributegroup AG ON AG.attributeproductid = AP.idattributeproduct
						WHERE
							AP.name = :attribute
						AND
							AG.attributegroupnameid = :groupid
						AND
							APV.name = :attributevaluename
					";
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('attribute', $attributeName);
					$stmt->bindValue('groupid', App::getContainer()->get('session')->getActiveMigrationGroupId());
					$stmt->bindValue('attributevaluename', $value['name']);
					try{
						$stmt->execute();
					}
					catch (Exception $e){
						throw new CoreException($e->getMessage());
					}

					$attributeData = $stmt->fetch();

					$attributes[$attributeData['attributeproductid']][] = $attributeData['idattributeproductvalue'];

					$attributesData[$attributeData['idattributeproductvalue']] = array(
						'modifier' => $value['price'],
						'attributes' => array(
							$attributeData['attributeproductid'] => $attributeData['idattributeproductvalue']
						),
						'photo' => isset($value['photo']) ? $value['photo'] : NULL
					);
				}
			}

			$i = 0;
			$allAttributes = array();
			if (! empty($attributes)){
				$c = $productModel->doCartesianProduct($attributes);

				if (! is_array($c[0])){
					$c = array_chunk($c, 1);
				}


				foreach ($c as $attribute){
					$sum = 0;
					$attrs = array();
					foreach ($attribute as $attr){
						$sum += $attributesData[$attr]['modifier'];
						$attrs += $attributesData[$attr]['attributes'];
					}

					$photoid = NULL;
					foreach($attribute as $photo) {
						if ($attributesData[$photo]['photo']) {
							$filename = key($attributesData[$photo]['photo']);
							$fileurl = current($attributesData[$photo]['photo']);

							if(empty($filename) || empty($fileurl)) {
								continue;
							}

							$sql = 'SELECT idfile FROM file WHERE name = :name';
							$stmt = Db::getInstance()->prepare($sql);
							$stmt->bindValue('name', Core::clearUTF($filename));
							try{
								$stmt->execute();
							}
							catch (Exception $e){
								throw new CoreException($e->getMessage());
							}
							$rs = $stmt->fetch();
							if ($rs){
								$photoid = $rs['idfile'];
							}
							else{
								$photoid = App::getModel('gallery')->getRemoteImage($fileurl, $filename);
							}
							break;
						}
					}

					$allAttributes['new-' . $i] = array(
						'suffix' => 2, // +
						'stock' => 0,
						'symbol' => '',
						'modifier' => $sum,
						'status' => 1,
						'deletable' => 0,
						'weight' => 0,
						'attributes' => $attrs,
						'availablity' => $availablityid,
						'photo' => $photoid
					);
					++ $i;
				}

				$attributes = array(
					'set' => App::getContainer()->get('session')->getActiveMigrationGroupId()
				) + $allAttributes;

				$productModel->addAttributesProducts($attributes, $idproduct);
			}
		}
	}

	protected function addUpdateCategory ($Data)
	{
		$viewid = (Helper::getViewId() > 0) ? Helper::getViewId() : $this->registry->loader->getParam('idview');

		$sql = 'SELECT idcategory FROM category WHERE migrationid = :migrationid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('migrationid', $Data['id']);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$idcategory = $rs['idcategory'];
			$sql = 'UPDATE category SET
						enable = :enable
					WHERE idcategory = :id';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $idcategory);
			$stmt->bindValue('enable', isset($Data['enable']) ? (int) $Data['enable'] : 0);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CATEGORY_ADD'), 3003, $e->getMessage());
			}


			$sql = 'UPDATE categorytranslation SET
					name = :name,
					shortdescription = :shortdescription,
					description = :description,
					keyword_title = :keyword_title,
					keyword = :keyword,
					keyword_description = :keyword_description,
					seo = :seo
				WHERE
					categoryid = :categoryid
			';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('categoryid', $idcategory);
			$stmt->bindValue('name', $Data['name']);
			$stmt->bindValue('shortdescription', $Data['shortdescription']);
			$stmt->bindValue('description', $Data['description']);
			$stmt->bindValue('keyword_title', $Data['keyword_title']);
			$stmt->bindValue('keyword', $Data['keyword']);
			$stmt->bindValue('keyword_description', $Data['keyword_description']);
			$stmt->bindValue('seo', strtolower(Core::clearSeoUTF($Data['name'])));
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CATEGORY_TRANSLATION_ADD'), 4, $e->getMessage());
			}
		}
		else{
			$sql = 'INSERT INTO category SET
						categoryid = :categoryid,
						photoid = :photoid,
						distinction = :distinction,
						enable = :enable,
						migrationid = :migrationid,
						migrationparentid = :migrationparentid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('categoryid', NULL);
			$stmt->bindValue('photoid', NULL);
			$stmt->bindValue('enable', isset($Data['enable']) ? (int) $Data['enable'] : 0);
			$stmt->bindValue('migrationid', $Data['id']);
			if ($Data['categoryid'] == 0){
				$stmt->bindValue('migrationparentid', NULL);
			}
			else{
				$stmt->bindValue('migrationparentid', $Data['categoryid']);
			}
			$stmt->bindValue('distinction', $Data['distinction']);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CATEGORY_ADD'), 3003, $e->getMessage());
			}

			$idcategory = Db::getInstance()->lastInsertId();

			$sql = 'INSERT INTO categorytranslation (
						categoryid,
						name,
						shortdescription,
						description,
						keyword_title,
						keyword,
						keyword_description,
						seo,
						languageid
					)VALUES(
						:categoryid,
						:name,
						:shortdescription,
						:description,
						:keyword_title,
						:keyword,
						:keyword_description,
						:seo,
						:languageid
			)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('categoryid', $idcategory);
			$stmt->bindValue('name', $Data['name']);
			$stmt->bindValue('shortdescription', $Data['shortdescription']);
			$stmt->bindValue('description', $Data['description']);
			$stmt->bindValue('keyword_title', $Data['keyword_title']);
			$stmt->bindValue('keyword', $Data['keyword']);
			$stmt->bindValue('keyword_description', $Data['keyword_description']);
			$stmt->bindValue('seo', strtolower(Core::clearSeoUTF($Data['name'])));
			$stmt->bindValue('languageid', Helper::getLanguageId());
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CATEGORY_TRANSLATION_ADD'), 4, $e->getMessage());
			}

			$sql = 'INSERT INTO viewcategory (categoryid,viewid) VALUES (:categoryid, :viewid)';
			$stmt = Db::getInstance()->prepare($sql);

			$stmt->bindValue('categoryid', $idcategory);
			$stmt->bindValue('viewid', $viewid);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_NEWS_ADD'), 4, $e->getMessage());
			}
		}
	}

	protected function addUpdateSimilarProducts ($mainProduct, $products)
	{
		$sql = "SELECT idproduct FROM product WHERE migrationid = :productid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('productid', $mainProduct);

		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_SIMILARPRODUCT_ADD'), $e->getMessage());
		}

		$rs = $stmt->fetch();
		if (!$rs) {
			return;
		}

		$mainProduct = $rs['idproduct'];


		$sql = "DELETE FROM similarproduct WHERE productid = :productid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('productid', $mainProduct);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_SIMILARPRODUCT_ADD'), $e->getMessage());
		}

		foreach ($products as $product) {
			$sql = "INSERT IGNORE INTO similarproduct SET
					productid = :productid,
				relatedproductid = (SELECT idproduct FROM product WHERE migrationid = :migrationid)";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productid', $mainProduct);
			$stmt->bindValue('migrationid', $product);

			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_SIMILARPRODUCT_ADD'), $e->getMessage());
			}
		}
	}

	protected function getCountryByName ($name)
	{
		$sql = 'SELECT idcountry FROM country WHERE name = :name LIMIT 1';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $name);
		$stmt->execute();

		$rs = $stmt->fetch();
		if (!$rs) {
			return NULL;
		}

		return $rs['idcountry'];

	}

	public function updateParentCategories ()
	{
		Db::getInstance()->beginTransaction();

		$sql = 'SELECT idcategory, migrationid FROM category WHERE migrationid IN (SELECT DISTINCT migrationparentid FROM category)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$sql2 = 'UPDATE category SET categoryid = :idcategory WHERE migrationparentid = :migrationid';
			$stmt2 = Db::getInstance()->prepare($sql2);
			$stmt2->bindValue('idcategory', $rs['idcategory']);
			$stmt2->bindValue('migrationid', $rs['migrationid']);
			$stmt2->execute();
		}

		Db::getInstance()->commit();
	}
}