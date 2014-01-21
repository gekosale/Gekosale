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
 * $Revision: 6 $
 * $Author: gekosale $
 * $Date: 2011-03-27 21:01:27 +0200 (N, 27 mar 2011) $
 * $Id: productsincategorybox.php 6 2011-03-27 19:01:27Z gekosale $
 */
namespace Gekosale;

class CollectionModel extends Component\Model
{

	public function getCollectionBySeo ($seo)
	{
		$sql = "SELECT
					C.idcollection,
					C.photoid,
					C.producerid,
					CT.name,
					CT.seo,
					CT.description,
					CT.keyword_title,
					CT.keyword,
					CT.keyword_description
				FROM collectiontranslation CT
				LEFT JOIN collection C ON C.idcollection = CT.collectionid
				WHERE CT.seo =:seo AND CT.languageid = :languageid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->bindValue('seo', $seo);
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
		$Data = Array();
		if ($rs){
			$Data = Array(
				'id' => $rs['idcollection'],
				'name' => $rs['name'],
				'description' => $rs['description'],
				'seo' => $rs['seo'],
				'photo' => $this->getImagePath($rs['photoid']),
				'keyword_title' => ($rs['keyword_title'] == NULL || $rs['keyword_title'] == '') ? $rs['name'] : $rs['keyword_title'],
				'keyword' => $rs['keyword'],
				'keyword_description' => $rs['keyword_description']
			);
		}
		return $Data;
	}

	public function getCollectionsByProducerId ($id, $seo)
	{
		$sql = "SELECT
					CT.name,
					CT.seo
				FROM collectiontranslation CT
				LEFT JOIN collection C ON C.idcollection = CT.collectionid
				WHERE C.producerid = :producerid AND CT.languageid = :languageid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->bindValue('producerid', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'name' => $rs['name'],
				'active' => ($this->getParam('collection') == $rs['seo']) ? true : false,
				'link' => $this->registry->router->generate('frontend.producerlist', true, Array(
					'param' => $seo, 
					'collection' => $rs['seo']
				)),
				'seo' => $rs['seo']
			);
		}
		return $Data;
	}

	public function getImagePath ($id)
	{
		if ($id > 0){
			return App::getModel('gallery')->getImagePath(App::getModel('gallery')->getOrginalImageById($id));
		}
	}
}