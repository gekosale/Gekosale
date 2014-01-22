<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale. Zabronione jest usuwanie informacji o licencji i autorach.
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
 * $Id: categoriesbox.php 619 2011-12-19 21:09:00Z gekosale $
 */
namespace Gekosale\Plugin;

class CategoriesBoxModel extends Component\Model
{
	protected $_rawData;
	protected $_currentCategoryId;

	public function getCategoriesTree ()
	{
		if (($categories = App::getContainer()->get('cache')->load('categories')) === FALSE){
			if (! isset($this->_rawData) || ! is_array($this->_rawData)){
				$this->_loadRawCategoriesFromDatabase();
			}
			$categories = $this->_parseCategorySubtree();
			App::getContainer()->get('cache')->save('categories', $categories);
		}
		return $categories;
	}

	protected function _loadRawCategoriesFromDatabase ()
	{
		$sql = '
				SELECT
					C.idcategory AS id,
					C.categoryid AS parent,
					C.photoid,
					CT.name AS label,
					CT.description,
					CT.shortdescription,
					CT.seo,
					COUNT(DISTINCT PC.productid) AS totalproducts
				FROM
					category C
					LEFT JOIN viewcategory CV ON CV.categoryid = idcategory
					LEFT JOIN categorytranslation CT ON C.idcategory = CT.categoryid AND CT.languageid = :languageid
          			LEFT JOIN categorypath CP ON CP.ancestorcategoryid = C.idcategory
          			LEFT JOIN productcategory PC ON CP.categoryid = PC.categoryid
				WHERE
					CV.viewid = :viewid AND C.enable = 1
				GROUP BY id ORDER BY C.distinction ASC
			';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->execute();
		$this->_rawData = $stmt->fetchAll();
	}

	protected function getProducers ($id)
	{
		$sql = 'SELECT
					PT.name,
					PT.seo,
					PC.categoryid
				FROM `producertranslation` PT
				LEFT JOIN product P ON P.producerid = PT.producerid
				LEFT JOIN productcategory PC ON P.idproduct = PC.productid
				LEFT JOIN categorypath CP ON CP.categoryid = PC.categoryid
				WHERE PT.languageid = :languageid AND CP.ancestorcategoryid = :id
				GROUP BY PT.producerid
		';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'name' => $rs['name'],
				'seo' => $rs['seo']
			);
		}
		return $Data;
	}

	protected function _parseCategorySubtree ($parent = null)
	{
		$categories = Array();
		foreach ($this->_rawData as $category){
			if ($parent == null){
				if ($category['parent'] != ''){
					continue;
				}
			}
			elseif ($category['parent'] != $parent){
				continue;
			}
			$producers = Array();
			if ($parent == null){
				$producers = $this->getProducers($category['id']);
			}
			$categories[] = Array(
				'id' => $category['id'],
				'label' => $category['label'],
				'seo' => $category['seo'],
				'link' => $this->generateUrl($category['seo']),
				'producers' => $producers,
				'shortdescription' => $category['shortdescription'],
				'description' => $category['description'],
				'totalproducts' => $category['totalproducts'],
				'photo' => App::getModel('categorylist')->getImagePath($category['photoid']),
				'photos' => Array(
					'small' => ((int) $category['photoid'] > 0) ? App::getModel('gallery')->getImagePath(App::getModel('gallery')->getSmallImageById($category['photoid'])) : '',
					'normal' => ((int) $category['photoid'] > 0) ? App::getModel('gallery')->getImagePath(App::getModel('gallery')->getNormalImageById($category['photoid'])) : '',
					'large' => ((int) $category['photoid'] > 0) ? App::getModel('gallery')->getImagePath(App::getModel('gallery')->getLargeImageById($category['photoid'])) : '',
					'orginal' => ((int) $category['photoid'] > 0) ? App::getModel('gallery')->getImagePath(App::getModel('gallery')->getOrginalImageById($category['photoid'])) : ''
				),
				'children' => $this->_parseCategorySubtree($category['id'])
			);
		}
		return $categories;
	}

	protected function generateUrl ($seo)
	{
		return $this->registry->router->generate('frontend.categorylist', true, Array(
			'param' => $seo
		));
	}

	public function getCategoryPathForProductById ($name)
	{
		$sql = 'SELECT
					CP.ancestorcategoryid
				FROM categorypath CP
				LEFT JOIN productcategory PC ON PC.categoryid = CP.categoryid
				LEFT JOIN producttranslation PT ON PT.productid = PC.productid AND PT.languageid = :languageid
				WHERE PT.seo = :name
				';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $name);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			$Data = Array();
			while ($rs = $stmt->fetch()){
				$Data[] = $rs['ancestorcategoryid'];
			}
			return $Data;
		}
		catch (Exception $e){
			throw new FrontendException($e->getMessage());
		}
	}

	public function getCurrentCategoryPath ($seo = NULL)
	{
		if ($seo != NULL){
			$sql = 'SELECT
					CP.ancestorcategoryid
				FROM categorypath CP
				LEFT JOIN categorytranslation CT ON CT.categoryid = CP.categoryid AND CT.languageid = :languageid
				WHERE CT.seo = :seo
				';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('seo', $seo);
			$stmt->bindValue('languageid', Helper::getLanguageId());
			try{
				$stmt->execute();
				$Data = Array();
				while ($rs = $stmt->fetch()){
					$Data[] = $rs['ancestorcategoryid'];
				}
				return $Data;
			}
			catch (Exception $e){
				throw new FrontendException($e->getMessage());
			}
		}
	}
}