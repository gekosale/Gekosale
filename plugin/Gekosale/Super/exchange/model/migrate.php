<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2009-2011 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the
 * Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it
 * through the
 * world-wide-web, please send an email to license@verison.pl so we can send you
 * a copy immediately.
 *
 * $Revision: 309 $
 * $Author: gekosale $
 * $Date: 2011-08-01 21:10:16 +0200 (Pn, 01 sie 2011) $
 * $Id: invoice.php 309 2011-08-01 19:10:16Z gekosale $
 */
namespace Gekosale;

use FormEngine;
use XMLReader;
use xajaxResponse;

class MigrateModel extends Component\Model
{
	const FIELD_STRING = 1;
	const FIELD_MULTILINGUAL_STRING = 2;
	const FIELD_TEXT = 3;
	const FIELD_IMAGE = 4;
	const FIELD_BOOLEAN = 5;
	const FIELD_SELECT = 6;

	public function importCategories ()
	{
		@set_time_limit(0);

		$sql = 'SELECT idimportxml, categories FROM importxml GROUP BY categories';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();

		$i = 0;
		while($rs = $stmt->fetch()) {
			$cat = unserialize($rs['categories']);

			if (empty($cat)) {
				continue;
			}

			$this->addAllCategories($cat);
			++$i;
		}

		Db::getInstance()->beginTransaction();
		App::getModel('category')->getCategoriesPathById();
		App::getModel('seo')->doRefreshSeoCategory();
		Db::getInstance()->commit();
		return $i;
	}

	public function addUpdateProducers ($Data)
	{
		$viewid = (Helper::getViewId() > 0) ? Helper::getViewId() : $this->registry->loader->getLayerViewId();
		Db::getInstance()->beginTransaction();
		foreach ($Data as $key => $producer){
				if(empty($producer['name'])) {
					continue;
				}

				$sql = 'SELECT idproducertranslation FROM producertranslation WHERE name = :name';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('name',  $producer['name']);
				$stmt->execute();

				if($stmt->fetch()) {
					continue;
				}

				$sql = 'INSERT INTO producer (photoid) VALUES (NULL)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->execute();

				$producerid = Db::getInstance()->lastInsertId();

				$sql = 'INSERT INTO producertranslation SET
							producerid = :producerid,
							name = :name,
							seo = :seo,
							languageid = :languageid';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('producerid', $producerid);
				$stmt->bindValue('name', $producer['name']);
				$stmt->bindValue('seo', strtolower(App::getModel('seo')->clearSeoUTF($producer['name'])));
				$stmt->bindValue('languageid', Helper::getLanguageId());
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new Exception(nl2br($e->getMessage()) . '<br />' . "\n");
				}

				$sql = 'INSERT INTO producerview (producerid, viewid)
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
		Db::getInstance()->commit();
	}

	public function getCategoryIdByNameAndParent ($name, $parent)
	{
		if ($parent === NULL) {
			$sql = 'SELECT
					C.idcategory
				FROM
					category C
				INNER JOIN
					categorytranslation CT ON CT.categoryid = C.idcategory
				WHERE
					CT.name = :name
				AND
					CT.languageid = :languageid
				AND
					C.categoryid IS NULL
				';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('name',  $name);
			$stmt->bindValue('languageid',  Helper::getLanguageId());
			$stmt->execute();
			$rs = $stmt->fetch();
			if (!$rs) {
				return FALSE;
			}
			return $rs['idcategory'];
		}
		else {
			$sql = 'SELECT
					C.idcategory
				FROM
					category C
				INNER JOIN
					categorytranslation CT ON CT.categoryid = C.idcategory
				WHERE
					CT.name = :name
				AND
					CT.languageid = :languageid
				AND
					C.categoryid =
					(
						SELECT
							CC.idcategory
						FROM
							category CC
						INNER JOIN
							categorytranslation CCT ON CCT.categoryid = CC.idcategory
						WHERE
							CCT.name = :parent
						AND
							CCT.languageid = :languageid
						LIMIT 1
					)
				';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('name',  $name);
			$stmt->bindValue('languageid',  Helper::getLanguageId());
			$stmt->bindValue('parent',  $parent);
			$stmt->execute();
			$rs = $stmt->fetch();
			if (!$rs) {
				return FALSE;
			}

			return $rs['idcategory'];
		}

	}

	public function addCategory ($name, $parent)
	{
		$sql = 'SELECT
				C.idcategory
			FROM
				category C
			INNER JOIN
				categorytranslation CT ON CT.categoryid = C.idcategory
			WHERE
				CT.name = :name
			AND
				CT.languageid = :languageid
			AND
				C.categoryid ' . (($parent === NULL) ? ' IS NULL ' : '= :parent');

		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name',  $name);
		if ($parent !== NULL) {
			$stmt->bindValue('parent',  $parent);
		}
		$stmt->bindValue('languageid',  Helper::getLanguageId());
		$stmt->execute();

		$rs = $stmt->fetch();

		if ($rs) {
			return $rs['idcategory'];
		}

		$sql = 'INSERT INTO category SET categoryid = :categoryid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('categoryid', NULL);
		$stmt->execute();

		$idcategory = Db::getInstance()->lastInsertId();

		$sql = 'INSERT INTO categorytranslation SET
					categoryid = :categoryid,
					name = :name,
					seo = :seo,
					languageid = :languageid
		';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('categoryid', $idcategory);
		$stmt->bindValue('name', $name);
		$stmt->bindValue('seo', strtolower(App::getModel('seo')->clearSeoUTF($name)));
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();

		$sql = 'INSERT INTO viewcategory SET
					categoryid = :categoryid,
					viewid = :viewid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('categoryid', $idcategory);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->execute();

		return $idcategory;
	}

	public function updateCategory ($category, $parent)
	{
		$sql = 'UPDATE
				category
			SET
				categoryid = :categoryid
			WHERE
				idcategory = :idcategory
		';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('categoryid', $category);
		$stmt->bindValue('idcategory', $parent);
		$stmt->execute();
	}

	public function addAllCategories($cat)
	{
		$cat = array_values($cat);
		$ids = array();
		foreach ($cat as $key => $category) {
			$parent = ($key === 0 ? NULL : $ids[$key - 1]);
			if (($id = $this->getCategoryIdByNameAndParent($category, $parent)) === FALSE) {
				$parent = ($key === 0) ? NULL : $ids[$key - 1];
				$id = $this->addCategory($category, $parent);
			}

			$ids[] = $id;
		}

		for ($i = 0; $i < count($ids) -1; ++$i) {
			$this->updateCategory($ids[$i], $ids[$i +1]);
		}
	}

	public function addUpdateCategories ($Data)
	{
		$viewid = (Helper::getViewId() > 0) ? Helper::getViewId() : $this->registry->loader->getLayerViewId();

		foreach ($Data as $symbol => $Data){
				if(empty($Data['name'])) {
					continue;
				}

				$sql = 'SELECT categoryid FROM categorytranslation WHERE name = :name';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('name',  $Data['name']);
				$stmt->execute();

				if($stmt->fetch()) {
					continue;
				}

				$sql = 'INSERT INTO category SET
							photoid = :photoid,
							categoryid = :categoryid,
							actionsymbol = :actionsymbol,
							actionparentsymbol = :actionparentsymbol';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('categoryid', NULL);
				$stmt->bindValue('photoid', NULL);
				$stmt->bindValue('actionsymbol', $Data['id']);
				$stmt->bindValue('actionparentsymbol', $Data['parentsymbol']);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_CATEGORY_ADD'), 3003, $e->getMessage());
				}

				$idcategory = Db::getInstance()->lastInsertId();

				$sql = 'INSERT INTO categorytranslation SET
							categoryid = :categoryid,
							name = :name,
							seo = :seo,
							languageid = :languageid
				';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('categoryid', $idcategory);
				$stmt->bindValue('name', $Data['name']);
				$stmt->bindValue('seo', strtolower(App::getModel('seo')->clearSeoUTF($Data['name'])));
				$stmt->bindValue('languageid', Helper::getLanguageId());
				$stmt->execute();

				$sql = 'INSERT INTO viewcategory SET
							categoryid = :categoryid,
							viewid = :viewid';
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
		$this->updateParentCategories();
	}


	public function doLoadQueueCategories ($request)
	{
		$fileid = 0;
		$startFrom = intval($request['iStartFrom']);

		$this->importCategories();

		return Array(
			'iTotal' => 1,
			'iCompleted' => 0
		);
	}

	public function doProcessQuequeCategories ()
	{
		return Array(
			'iStartFrom' => 1,
			'bFinished' => true
		);

	}

	public function doLoadQueueProducers ()
	{
		$sql = 'SELECT COUNT(DISTINCT producer) AS total FROM importxml';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			return Array(
				'iTotal' => (int) $rs['total'],
				'iCompleted' => 0
			);
		}
		else{
			return Array(
				'iTotal' => 0,
				'iCompleted' => 0
			);
		}
	}

	public function doLoadQuequeProducts ()
	{
		$sql = 'SELECT COUNT(*) AS total FROM importxml';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			return Array(
				'iTotal' => (int) $rs['total'],
				'iCompleted' => 0
			);
		}
		else{
			return Array(
				'iTotal' => 0,
				'iCompleted' => 0
			);
		}
	}

	public function doProcessQuequeProducers ($request)
	{
		$startFrom = intval($request['iStartFrom']);
		$chunks = intval($request['iChunks']);

		$sql = "SELECT idimportxml, producer AS name FROM importxml GROUP BY producer LIMIT {$startFrom},{$chunks}";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$Data = $stmt->fetchAll();
		$this->addUpdateProducers($Data);

		if ($startFrom < intval($request['iTotal'])){
			return Array(
				'iStartFrom' => $startFrom + $chunks
			);
		}
		else{
			return Array(
				'iStartFrom' => $request['iTotal'],
				'bFinished' => true
			);
		}
	}

	public function doLoadQuequePhotos ()
	{
		$sql = 'TRUNCATE actionphoto';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();

		Db::getInstance()->beginTransaction();

		$sql = 'SELECT idimportxml, imageurl FROM importxml WHERE imageurl like \'%.jpg\' or  imageurl like \'%.png\' or  imageurl like \'%.gif\'';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();

		$i = 0;
		while($rs = $stmt->fetch()) {
			if (empty($rs['imageurl'])) {
				continue;
			}

			$sql = 'INSERT INTO actionphoto SET
					zdjecie = :zdjecie,
					actionsymbol = :actionsymbol
			';
			$stmt2 = Db::getInstance()->prepare($sql);
			$stmt2->bindValue('actionsymbol', $rs['idimportxml']);
			$stmt2->bindValue('zdjecie', $rs['imageurl']);
			try{
				$stmt2->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_PRODUCT_UPDATE'), 112, $e->getMessage());
			}
			++$i;
		}

		Db::getInstance()->commit();

		return Array(
			'iTotal' => $i,
			'iCompleted' => 0
		);
	}

	public function doProcessQuequePhotos ($request)
	{
		$fileid = 0;
		$startFrom = intval($request['iStartFrom']);
		$chunks = intval($request['iChunks']);

		$sql = "SELECT * FROM actionphoto LIMIT {$startFrom},{$chunks}";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$Data = $stmt->fetchAll();
		foreach ($Data as $key => $val){
			$sql = 'SELECT idfile FROM file WHERE name = :name';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('name', Core::clearUTF(basename($val['zdjecie'])));
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$fileid = $rs['idfile'];
			}
			else{
				$fileid = App::getModel('gallery')->getRemoteImage($val['zdjecie'], basename($val['zdjecie']));
			}
			if ($fileid > 0){
				$sql = 'UPDATE actionphoto SET fileid = :fileid WHERE zdjecie = :zdjecie AND actionsymbol = :actionsymbol';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('fileid', $fileid);
				$stmt->bindValue('zdjecie', $val['zdjecie']);
				$stmt->bindValue('actionsymbol', $val['actionsymbol']);
				$rs = $stmt->execute();
			}
		}

		if ($startFrom < intval($request['iTotal'])){
			return Array(
				'iStartFrom' => $startFrom + $chunks
			);
		}
		else{
			return Array(
				'iStartFrom' => $request['iTotal'],
				'bFinished' => true
			);
		}
	}

	public function doProcessQuequeProducts ($request)
	{
		$startFrom = intval($request['iStartFrom']);
		$chunks = intval($request['iChunks']);

		$sql = "SELECT * FROM importxml LIMIT {$startFrom},{$chunks}";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$Data = $stmt->fetchAll();
		foreach ($Data as $key => $val){
			$this->addUpdateProduct($val);
		}

		if ($startFrom < intval($request['iTotal'])){
			return Array(
				'iStartFrom' => $startFrom + $chunks
			);
		}
		else{
			return Array(
				'iStartFrom' => $request['iTotal'],
				'bFinished' => true
			);
		}
	}

	public function doSuccessQueque ($request)
	{
		if ($request['bFinished']){
			return Array(
				'bCompleted' => true
			);
		}
	}


	protected function addUpdateProduct ($Data)
	{
		$viewid = (Helper::getViewId() > 0) ? Helper::getViewId() : $this->registry->loader->getLayerViewId();
		$vatValues = array_flip(App::getModel('vat')->getVATValuesAll());

		$sql = "SELECT idproduct FROM product WHERE migrationid = :migrationid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('migrationid', $Data['migrateid']);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PRODUCT_CATEGORY_UPDATE'), 112, $e->getMessage());
		}

		$rs = $stmt -> fetch();
		if ($rs) {
			$idproduct = $rs['idproduct'];

			$sql = 'UPDATE product SET
					sellprice	= :sellprice,
					buycurrencyid   = :buycurrencyid,
					sellcurrencyid  = :sellcurrencyid,
					weight		= :weight,
					vatid		= :vatid,
					producerid	= (SELECT producerid FROM producertranslation WHERE name = :producer GROUP BY producerid LIMIT 1),
					stock		= :stock,
					enable		= :enable,
					trackstock	= :trackstock,
					migrationid	= :migrationid,
					ean		= :ean
				WHERE
					idproduct = :idproduct
			';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('sellprice', $Data['price']);
			$stmt->bindValue('buycurrencyid', App::getContainer()->get('session')->getActiveShopCurrencyId());
			$stmt->bindValue('sellcurrencyid', App::getContainer()->get('session')->getActiveShopCurrencyId());
			$stmt->bindValue('weight', $Data['weight']);
			$stmt->bindValue('vatid', 2);
			$stmt->bindValue('producer', $Data['producer']);
			$stmt->bindValue('stock', $Data['stock']);
			$stmt->bindValue('enable', (bool) $Data['avail']);
			$stmt->bindValue('migrationid', $Data['migrateid']);
			$stmt->bindValue('ean', $Data['ean']);
			$stmt->bindValue('trackstock', 1);
			$stmt->bindValue('idproduct', $idproduct);

			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_PRODUCT_UPDATE'), 112, $e->getMessage());
			}

			$sql = 'UPDATE producttranslation SET
					name = :name,
					description = :description,
					shortdescription = :shortdescription,
					seo = :seo
				WHERE
					productid = :productid
				AND
					languageid = :languageid
			';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('name', $Data['name']);
			$stmt->bindValue('description', $Data['description']);
			$stmt->bindValue('shortdescription', '');
			$stmt->bindValue('seo', strtolower(App::getModel('seo')->clearSeoUTF($Data['name'])));
			$stmt->bindValue('languageid', Helper::getLanguageId());
			$stmt->bindValue('productid', $idproduct);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_PRODUCT_UPDATE'), 112, $e->getMessage());
			}
		}
		else {
			$sql = 'INSERT INTO product SET
						sellprice	= :sellprice,
						buycurrencyid   = :buycurrencyid,
						sellcurrencyid  = :sellcurrencyid,
						weight		= :weight,
						vatid		= :vatid,
						producerid	= (SELECT producerid FROM producertranslation WHERE name = :producer GROUP BY producerid LIMIT 1),
						stock		= :stock,
						enable		= :enable,
						trackstock	= :trackstock,
						migrationid	= :migrationid,
						ean		= :ean
			';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('sellprice', $Data['price']);
			$stmt->bindValue('buycurrencyid', App::getContainer()->get('session')->getActiveShopCurrencyId());
			$stmt->bindValue('sellcurrencyid', App::getContainer()->get('session')->getActiveShopCurrencyId());
			$stmt->bindValue('weight', $Data['weight']);
			$stmt->bindValue('vatid', 2);
			$stmt->bindValue('producer', $Data['producer']);
			$stmt->bindValue('stock', $Data['stock']);
			$stmt->bindValue('enable', $Data['avail']);
			$stmt->bindValue('migrationid', $Data['migrateid']);
			$stmt->bindValue('ean', $Data['ean']);
			$stmt->bindValue('trackstock', 1);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_PRODUCT_UPDATE'), 112, $e->getMessage());
			}


			$idproduct = Db::getInstance()->lastInsertId();


			$sql = 'INSERT INTO producttranslation SET
						productid = :productid,
						name = :name,
						description = :description,
						shortdescription = :shortdescription,
						seo = :seo,
						languageid = :languageid
					';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productid', $idproduct);
			$stmt->bindValue('name', $Data['name']);
			$stmt->bindValue('description', $Data['description']);
			$stmt->bindValue('shortdescription', '');
			$stmt->bindValue('seo', strtolower(App::getModel('seo')->clearSeoUTF($Data['name'])));
			$stmt->bindValue('languageid', Helper::getLanguageId());
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_PRODUCT_UPDATE'), 112, $e->getMessage());
			}
		}

		$categories = unserialize($Data['categories']);

		$seo = array_map(function($str) {
			return strtolower(App::getModel('seo')->clearSeoUTF($str));
		}, $categories);

		$seo = implode( '/', $seo);

		$sql = 'INSERT INTO productcategory (productid, categoryid)
				SELECT :productid, categoryid FROM categorytranslation WHERE seo = :seo';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('productid', $idproduct);
		$stmt->bindValue('seo', $seo);

		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PRODUCT_CATEGORY_UPDATE'), 112, $e->getMessage());
		}

		$sql = 'DELETE FROM productphoto WHERE productid = :productid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('productid', $idproduct);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PRODUCT_CATEGORY_UPDATE'), 112, $e->getMessage());
		}

		$sql = 'INSERT INTO productphoto (productid, mainphoto, photoid)
				SELECT :productid, 1, fileid FROM actionphoto WHERE actionsymbol = :actionsymbol
		';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('productid', $idproduct);
		$stmt->bindValue('actionsymbol', $Data['idimportxml']);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PRODUCT_PHOTO_UPDATE'), 112, $e->getMessage());
		}

		if( isset($Data['attributes'])) {
			$attributes = unserialize($Data['attributes']);
		}

		$setId = 0;

		// SET
		if (empty($attributes['product.attribute.setname'][0])) {

			$sql = "SELECT idtechnicaldataset FROM technicaldataset WHERE name = 'Migration set'";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs) {
				$setId = $rs['idtechnicaldataset'];
			}
			else {
				$setId = $this->addEmptyGroup(array(
					'name' => 'Migration set'
				));
			}
		}
		else {
			$sql = "SELECT idtechnicaldataset FROM technicaldataset WHERE name = :name";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('name', $attributes['product.attribute.setname'][0]);
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs) {
				$setId = $rs['idtechnicaldataset'];
			}
			else {
				$setId = $this->addEmptySet(array(
					'name' => $attributes['product.attribute.setname'][0]
				));
			}
		}

		$groupId = 0;
		// GROUP
		if (empty($attributes['product.attribute.group'][0])) {
			$sql = "select
					TDGT.technicaldatagroupid
				FROM
					technicaldatasetgroup TSG
				LEFT JOIN technicaldatagrouptranslation TDGT ON TDGT.technicaldatagroupid = TSG.technicaldatagroupid
				WHERE
					TSG.technicaldatasetid = :technicaldatasetid
				AND
						TDGT.name = 'Ogólne'
				AND
					TDGT.languageid = :languageId";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('languageId', Helper::getLanguageId());
			$stmt->bindValue('technicaldatasetid', $setId);
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs) {
				$groupId = $rs['technicaldatagroupid'];
			}
			else {
				$groupId = $this->SaveGroup('new', array(
					Helper::getLanguageId() => 'Ogólne'
				));

				$sql = 'INSERT INTO
						technicaldatasetgroup
					SET
						technicaldatasetid = :technicaldatasetid,
						technicaldatagroupid = :technicaldatagroupid';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('technicaldatasetid', $setId);
				$stmt->bindValue('technicaldatagroupid', $groupId);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
			}
		}
		else {
			$sql = "SELECT
					TDGT.technicaldatagroupid
				FROM
					technicaldatasetgroup TSG
				LEFT JOIN
					technicaldatagrouptranslation TDGT ON TDGT.technicaldatagroupid = TSG.technicaldatagroupid
				WHERE
					TSG.technicaldatasetid = :technicaldatasetid
				AND
						TDGT.name = :name
				AND
					TDGT.languageid = :languageId";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('languageId', Helper::getLanguageId());
			$stmt->bindValue('technicaldatasetid', $setId);
			$stmt->bindValue('name', $attributes['product.attribute.group'][0]);
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs) {
				$groupId = $rs['technicaldatagroupid'];
			}
			else {
				$groupId = $this->SaveGroup('new', array(
					Helper::getLanguageId() => $attributes['product.attribute.group'][0]
				));

				$sql = 'INSERT INTO
						technicaldatasetgroup
					SET
						technicaldatasetid = :technicaldatasetid,
						technicaldatagroupid = :technicaldatagroupid';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('technicaldatasetid', $setId);
				$stmt->bindValue('technicaldatagroupid', $groupId);

				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
			}
		}

		//$setId
		//$groupId

		if (empty($attributes['product.attribute.value']) || empty($attributes['product.attribute.name'])) {
			return;
		}

		$a = array();

		foreach ($attributes['product.attribute.name'] as $k => $name) {

			if (empty($name)) {
				continue;
			}

			$sql = "SELECT
					TAT.technicaldataattributeid
				FROM
				technicaldatasetgroupattribute TGA
				LEFT JOIN technicaldatasetgroup TG ON TGA.technicaldatasetgroupid = TG.idtechnicaldatasetgroup
				LEFT JOIN technicaldataattributetranslation TAT ON TAT.technicaldataattributeid = TGA.technicaldataattributeid

				WHERE
					TG.technicaldatasetid = :setid
				AND
					TG.technicaldatagroupid = :groupid
				AND
					TAT.name = :name
				AND
					TAT.languageid = :languageId";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('setid', $setId);
			$stmt->bindValue('groupid', $groupId);
			$stmt->bindValue('name', $name);
			$stmt->bindValue('languageId', Helper::getLanguageId());
			$stmt->execute();

			$rs = $stmt->fetch();

			if($rs) {
				$a[$k] = $rs['technicaldataattributeid'];
			}
			else {
				$a[$k] = $this->SaveAttribute(array(
					Helper::getLanguageId() => $name
				), '3', $groupId);
			}
		}

		$groups = array(
			'id' => $groupId,
			'attributes' => array()
		);

		foreach ($attributes['product.attribute.value'] as $k => $val) {
			$groups['attributes'][] = array(
				'id' => $a[$k],
				'type' => '3',
				'value' => $val
			);
		};

		$technicalData = array(
			'set' => $setId,
			'groups' => array(
				$groups,
			)
		);

		$this->SaveValuesForProduct($idproduct, $technicalData);

		$sql = 'UPDATE  product SET technicaldatasetid = :technicaldatasetid WHERE idproduct = :idproduct';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('technicaldatasetid', $setId);
		$stmt->bindValue('idproduct', $idproduct);

		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}

	}


	protected function addEmptyGroup ($request)
	{
		if ( empty($request['name'])){
			return NULL;
		}
		$sql = 'INSERT INTO technicaldataset SET name = :name';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $request['name']);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}

		return Db::getInstance()->lastInsertId();
	}

	protected function addEmptySet ($request)
	{
		if ( empty($request['name'])){
			return NULL;
		}

		$sql = 'INSERT INTO technicaldataset SET name = :name';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $request['name']);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}

		return Db::getInstance()->lastInsertId();
	}

	protected function SaveGroup ($groupId, $groupName)
	{
		if (substr($groupId, 0, 3) == 'new'){
			$sql = 'INSERT INTO technicaldatagroup SET adddate = NOW()';
			$stmt = Db::getInstance()->prepare($sql);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
			$groupId = Db::getInstance()->lastInsertId();
		}
		else{
			DbTracker::deleteRows('technicaldatagrouptranslation', 'technicaldatagroupid', $groupId);
		}
		foreach ($groupName as $languageId => $name){
			$sql = 'INSERT INTO	technicaldatagrouptranslation SET
						technicaldatagroupid = :groupId,
						languageid = :languageId,
						name = :name';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('groupId', $groupId);
			$stmt->bindValue('languageId', $languageId);
			$stmt->bindValue('name', $name);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
		return $groupId;
	}

	protected function SaveAttribute ($attributeName, $attributeType, $groupId)
	{
		$sql = 'INSERT INTO technicaldataattribute SET type = :type';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('type', $attributeType);
		$stmt->execute();
		$attributeId = Db::getInstance()->lastInsertId();

		foreach ($attributeName as $languageId => $name){
			$sql = 'INSERT INTO
					technicaldataattributetranslation
				SET
					technicaldataattributeid = :attributeId,
					languageid = :languageId,
					name = :name';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('attributeId', $attributeId);
			$stmt->bindValue('languageId', $languageId);
			$stmt->bindValue('name', $name);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}

			$id = Db::getInstance()->lastInsertId();


			$sql = 'INSERT INTO
					technicaldatasetgroupattribute
				SET
					technicaldatasetgroupid = (SELECT idtechnicaldatasetgroup FROM technicaldatasetgroup WHERE technicaldatagroupid = :groupid),
					technicaldataattributeid = :technicaldataattributeid
					';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('groupid', $groupId);
			$stmt->bindValue('technicaldataattributeid', $attributeId);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
		return $attributeId;
	}

	protected function SaveValuesForProduct ($productId, $productData)
	{
		try{
			$this->DeleteValuesForProduct($productId);
			if (! isset($productData['groups']) or ! is_array($productData['groups'])){
				return;
			}
			foreach ($productData['groups'] as $groupOrder => $group){
				if (substr($group['id'], 0, 3) == 'new'){
					$group['id'] = $this->SaveGroup('new', $group['caption']);
				}
				$sql = 'INSERT INTO
							producttechnicaldatagroup
						SET
							productid = :productId,
							technicaldatagroupid = :groupId,
							`order` = :order';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('productId', $productId);
				$stmt->bindValue('groupId', $group['id']);
				$stmt->bindValue('order', $groupOrder);
				$stmt->execute();
				$productGroupId = Db::getInstance()->lastInsertId();
				if (isset($group['attributes']) and is_array($group['attributes'])){
					foreach ($group['attributes'] as $attributeOrder => $attribute){
						if (substr($attribute['id'], 0, 3) == 'new'){
							$attribute['id'] = $this->SaveAttribute('new', $attribute['caption'], $attribute['type']);
						}
						$sql = 'INSERT INTO
										producttechnicaldatagroupattribute
									SET
										producttechnicaldatagroupid = :productGroupId,
										technicaldataattributeid = :attributeId,
										`order` = :order,
										value = :value';
						$stmt = Db::getInstance()->prepare($sql);
						$stmt->bindValue('productGroupId', $productGroupId);
						$stmt->bindValue('attributeId', $attribute['id']);
						$stmt->bindValue('order', $attributeOrder);
						switch ($attribute['type']) {
							case self::FIELD_STRING:
								$stmt->bindValue('value', $attribute['value']);
								break;
							case self::FIELD_TEXT:
								$stmt->bindValue('value', $attribute['value']);
								break;
							case self::FIELD_BOOLEAN:
								$stmt->bindValue('value', $attribute['value'] ? '1' : '0');
								break;
							default:
								$stmt->bindValue('value', '');
						}
						$stmt->execute();
						$productGroupAttributeId = Db::getInstance()->lastInsertId();
						switch ($attribute['type']) {
							case self::FIELD_MULTILINGUAL_STRING:
								if (! is_array($attribute['value'])){
									break;
								}
								foreach ($attribute['value'] as $languageId => $value){
									$sql = 'INSERT INTO	producttechnicaldatagroupattributetranslation SET
												producttechnicaldatagroupattributeid = :productGroupAttributeId,
												languageid = :languageId,
												value = :value';
									$stmt = Db::getInstance()->prepare($sql);
									$stmt->bindValue('productGroupAttributeId', $productGroupAttributeId);
									$stmt->bindValue('languageId', $languageId);
									$stmt->bindValue('value', $value);
									$stmt->execute();
								}
								break;
						}
					}
				}
			}
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_PRODUCT_TECHNICAL_DATA_ADD'), 112, $e->getMessage());
		}
	}

	protected function DeleteValuesForProduct ($id)
	{
		$sql = 'DELETE FROM producttechnicaldatagroup WHERE productid = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
	}

}