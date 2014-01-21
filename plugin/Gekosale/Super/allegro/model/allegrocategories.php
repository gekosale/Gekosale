<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 */
namespace Gekosale;

class AllegroCategoriesModel extends Component\Model
{

    public function __construct ($registry, $modelFile)
    {
        parent::__construct($registry, $modelFile);
        $this->allegro = new AllegroApi($registry);
    }

    public function getDefaultAllegroCategoriesForProduct ($productId)
    {
        $Data = Array();
        $countryId = App::getContainer()->get('session')->getActiveAllegroCountryId();
        
        $sql = "SELECT
					AC.name,
					AC.originalcategoryid
				FROM product P
				LEFT JOIN productcategory PC ON P.idproduct = PC.productid
				LEFT JOIN category C ON PC.categoryid = C.idcategory
				LEFT JOIN allegrorelatedcategories ARC ON C.idcategory = ARC.categoryid
				LEFT JOIN allegrocategories AC ON ARC.allegrocategoriesid = AC.originalcategoryid
				WHERE P.idproduct = :productid";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('productid', $productId);
        $stmt->execute();
        while ($rs = $stmt->fetch()){
            $Data[] = Array(
                $rs['originalcategoryid'] => $rs['name']
            );
        }
        return $Data;
    }

    public function getAllegroFavouriteCategoriesALLToSelect ()
    {
        $Data = $this->doGetFavouriteCategoriesFromAllegro();
        $tmp = Array();
        foreach ($Data as $key){
            $allegroCategoryName = $this->getFavCatName($key['id']);
            if ($allegroCategoryName !== NULL){
                $tmp[$key['id']] = $allegroCategoryName;
            }
            else{
                $tmp[$key['id']] = $key['id'];
            }
        }
        return $tmp;
    }

    public function getFavCatName ($originalcategoryid)
    {
        $name = '';
        $sql = "SELECT
					AC.name
				FROM allegrocategories AC
				WHERE originalcategoryid= :originalcategoryid";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('originalcategoryid', $originalcategoryid);
        try{
            $stmt->execute();
            $rs = $stmt->fetch();
            if ($rs){
                $name = $rs['name'];
            }
        }
        catch (Exception $e){
            throw new Exception($e->getMessage());
        }
        return $name;
    }

    public function doGetFavouriteCategoriesFromAllegro ()
    {
        $categories = Array();
        
        $responseFavouriteCats = $this->allegro->doGetFavouriteCategories();
        
        if ($responseFavouriteCats > 0){
            foreach ($responseFavouriteCats as $entry){
                $categories[$entry['s-category-id']] = Array(
                    'id' => $entry['s-category-id'],
                    'position' => $entry['s-position'],
                    'onlybuynow' => $entry['s-buy-now-only']
                );
            }
        }
        
        $localAllegroFavCategories = $this->doGetLocalFavouriteCategories($categories);
        if (count($localAllegroFavCategories) > 0){
            foreach ($localAllegroFavCategories as $localFavCat){
                $categories[$localFavCat['id']] = Array(
                    'id' => $localFavCat['id'],
                    'position' => $localFavCat['position'],
                    'onlybuynow' => $localFavCat['onlybuynow']
                );
            }
        }
        
        return $categories;
    }

    public function doGetLocalFavouriteCategories ($allegroFavCatsArray = 0)
    {
        $categories = Array();
        if (is_array($allegroFavCatsArray) && count($allegroFavCatsArray) > 0){
            $sql = 'SELECT
						AFC.allegrooriginalcategoryid,
						AC.categoryposition
					FROM allegrofavouritecategory AFC
					LEFT JOIN allegrocategories AC ON AFC.allegrooriginalcategoryid = AC.originalcategoryid
					WHERE AFC.allegrooriginalcategoryid NOT IN (' . implode(',', array_keys($allegroFavCatsArray)) . ') AND AC.countryid = :countryid';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('countryid', $this->allegro->getCountryCode());
            $stmt->execute();
            try{
                while ($rs = $stmt->fetch()){
                    $categories[$rs['allegrooriginalcategoryid']] = Array(
                        'id' => $rs['allegrooriginalcategoryid'],
                        'position' => $rs['categoryposition'],
                        'onlybuynow' => 0
                    );
                }
            }
            catch (Exception $e){
                throw new Exception($e->getMessage());
            }
        }
        else{
            
            $sql = "SELECT
						AFC.allegrooriginalcategoryid, AC.categoryposition
					FROM allegrofavouritecategory AFC
					LEFT JOIN allegrocategories AC ON AFC.allegrooriginalcategoryid = AC.originalcategoryid
					WHERE AC.countryid = :countryid";
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('countryid', $this->allegro->getCountryCode());
            $stmt->execute();
            try{
                while ($rs = $stmt->fetch()){
                    $categories[$rs['allegrooriginalcategoryid']] = Array(
                        'id' => $rs['allegrooriginalcategoryid'],
                        'position' => $rs['categoryposition'],
                        'onlybuynow' => 0
                    );
                }
            }
            catch (Exception $e){
                throw new Exception($e->getMessage());
            }
        }
        return $categories;
    }

    public function getRelatedAllegroCatsToSelect ()
    {
        $Data = $this->getRelatedAllegroCategories();
        $tmp = Array();
        foreach ($Data as $key){
            $tmp[$key['originalcategoryid']] = $key['idcategory'];
        }
        return $tmp;
    }

    public function getRelatedAllegroCategories ()
    {
        $Data = Array();
        $countryId = App::getContainer()->get('session')->getActiveAllegroCountryId();
        
        $sql = "SELECT
					ARC.idallegrorelatedcategories,
	     			AC.originalcategoryid,
	     			AC.name as allegroname,
	      			C.idcategory,
	      			CT.name
				FROM allegrorelatedcategories ARC
				LEFT JOIN allegrocategories AC ON ARC.allegrocategoriesid = AC.originalcategoryid
				LEFT JOIN category C  ON ARC.categoryid = C.idcategory
				LEFT JOIN categorytranslation CT ON CT.categoryid = C.idcategory AND CT.languageid = :languageid";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $Data[] = Array(
                'originalcategoryid' => $rs['originalcategoryid'],
                'allegroname' => $rs['allegroname'],
                'idcategory' => $rs['idcategory'],
                'name' => $rs['name']
            );
        }
        return $Data;
    }

    public function getLocalChildAllegroCategories ($parentCategory = 0)
    {
        $Data = Array();
        $sql = "SELECT
					AC.originalcategoryid,
					AC.name,
					COUNT(B.originalcategoryid) AS has_children
				FROM allegrocategories AC
				LEFT JOIN allegrocategories B ON AC.originalcategoryid = B.parentcategoryid
				WHERE AC.parentcategoryid = :parent AND AC.countryid = :countryid
				GROUP BY AC.originalcategoryid";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('parent', $parentCategory);
        $stmt->bindValue('countryid', $this->allegro->getCountryCode());
        $stmt->execute();
        while ($rs = $stmt->fetch()){
            $Data[$rs['originalcategoryid']] = Array(
                'name' => $rs['name'],
                'hasChildren' => ($rs['has_children'] > 0) ? true : false
            );
        }
        return $Data;
    }

    public function getCategoryPath ($categoryId)
    {
        $categoryPath = Array();
        $parentCategory = $this->getParentCategory($categoryId);
        while (isset($parentCategory['parent'])){
            $name = $parentCategory['name'];
            $parent = $parentCategory['parent'];
            array_push($categoryPath, $name);
            $parentCategory = $this->getParentCategory($parent);
        }
        if (count($categoryPath) > 1){
            return array_reverse($categoryPath);
        }
        else{
            return $categoryPath;
        }
    }

    public function getParentCategory ($categoryId)
    {
        $Data = Array();
        $sql = "SELECT
					AC.originalcategoryid,
					AC.name,
					AC.parentcategoryid
				FROM allegrocategories AC
				WHERE AC.originalcategoryid = :categoryid";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('categoryid', $categoryId);
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs){
            $Data = Array(
                'name' => $rs['name'],
                'parent' => $rs['parentcategoryid']
            );
        }
        return $Data;
    }

    public function editAllegroCategories ($submitedData)
    {
        if (is_array($submitedData)){
            $allegroEditRelatedCategories = $this->editAllegroRelatedCategories($submitedData['relatedcategories']);
            $allegroEditFavouriteCategories = $this->editAllegroFavouriteCategories($submitedData['favouritecategories']);
        }
        return true;
    }

    public function getRelatedCategoryPath ($allegroCategoryId, $shopCategoryId)
    {
        $categoryPath = Array();
        if ($allegroCategoryId != NULL && $shopCategoryId != NULL){
            $categoryPath = Array(
                'allegro' => $this->getCategoryPath($allegroCategoryId),
                'shop' => $this->getCategoryPathForLocalCategories($shopCategoryId)
            );
        }
        return $categoryPath;
    }

    public function getCategoryPathForLocalCategories ($shopCategoryId)
    {
        $sql = 'SELECT
					GROUP_CONCAT(SUBSTRING(IF(CT.categoryid = :id, CONCAT(\'<strong>\',CT.name,\'</strong>\'), CT.name), 1) ORDER BY C.order DESC SEPARATOR \' / \') AS path
				FROM categorytranslation CT
				LEFT JOIN categorypath C ON C.ancestorcategoryid = CT.categoryid
				WHERE C.categoryid = :id AND CT.languageid = :languageid
				GROUP BY C.categoryid, CT.languageid
		';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $shopCategoryId);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs){
            return $rs['path'];
        }
    }

    public function editAllegroRelatedCategories ($relatedcategories)
    {
        if (is_array($relatedcategories)){
            
            DbTracker::truncate('allegrorelatedcategories');
            
            foreach ($relatedcategories as $related => $key){
                $sql = "INSERT INTO allegrorelatedcategories (allegrocategoriesid, categoryid) VALUES (:allegrocategoriesid, :categoryid)";
                $stmt = Db::getInstance()->prepare($sql);
                $stmt->bindValue('allegrocategoriesid', $related);
                $stmt->bindValue('categoryid', $key);
                $stmt->execute();
            }
        }
    }

    public function editAllegroFavouriteCategories ($favouritecategories)
    {
        if (is_array($favouritecategories)){
            
            DbTracker::truncate('allegrofavouritecategory');
            
            foreach ($favouritecategories as $favourite => $key){
                $sql = "INSERT INTO allegrofavouritecategory
								(allegrooriginalcategoryid,
								countryid)
							VALUES (
								:allegrooriginalcategoryid,
								:countryid)";
                $stmt = Db::getInstance()->prepare($sql);
                $stmt->bindValue('countryid', $this->allegro->getCountryCode());
                $stmt->bindValue('allegrooriginalcategoryid', $key);
                $stmt->execute();
            }
            return true;
        }
    }

    public function updateLocalAllegroCategories ()
    {
        $sql = "SELECT
					ARC.idallegrorelatedcategories,
	     			AC.originalcategoryid,
	     			AC.name as allegroname,
	      			C.idcategory,
	      			CT.name
				FROM allegrorelatedcategories ARC
				LEFT JOIN allegrocategories AC ON ARC.allegrocategoriesid = AC.originalcategoryid
				LEFT JOIN category C  ON ARC.categoryid = C.idcategory
				LEFT JOIN categorytranslation CT ON CT.categoryid = C.idcategory AND CT.languageid = :languageid";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            if ((int) ($rs['originalcategoryid']) > 0){
                
                echo $rs['name'] . PHP_EOL;
                
                $sellFormFields = $this->allegro->doGetSellFormFieldsForCategory($rs['originalcategoryid']);
                
                $sql2 = 'UPDATE allegrorelatedcategories SET sellformfields = :sellformfields WHERE allegrocategoriesid = :allegrocategoriesid';
                $stmt2 = Db::getInstance()->prepare($sql2);
                $stmt2->bindValue('allegrocategoriesid', $rs['originalcategoryid']);
                $stmt2->bindValue('sellformfields', serialize($sellFormFields));
                $stmt2->execute();
            }
        }
    }

    public function addAllegroCategories ()
    {
        $sql = 'SELECT COUNT(*) AS total FROM allegrocategories';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs){
            if ($rs['total'] == 0){
                $this->updateAllegroCategories();
            }
        }
    }

    public function updateAllegroCategories ()
    {
        set_time_limit(0);
        $categories = $this->allegro->doGetCatsData();
        Db::getInstance()->beginTransaction();
        foreach ($categories['cats-list'] as $key => $category){
            
            $sql = 'INSERT INTO allegrocategories SET
						name = :name,
						parentcategoryid = :parentcategoryid,
						originalcategoryid = :originalcategoryid,
						categoryposition = :categoryposition,
						countryid = :countryid
					ON DUPLICATE KEY UPDATE
						name = :name,
						parentcategoryid = :parentcategoryid';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('name', $category['cat-name']);
            $stmt->bindValue('originalcategoryid', $category['cat-id']);
            $stmt->bindValue('countryid', $this->allegro->getCountryCode());
            $stmt->bindValue('categoryposition', $category['cat-position']);
            $stmt->bindValue('parentcategoryid', $category['cat-parent']);
            $stmt->execute();
        }
        Db::getInstance()->commit();
    }

    public function checkNewFields ($id)
    {
        return ! in_array($id, Array(
            1,
            2,
            3,
            4,
            5,
            8,
            9,
            10,
            11,
            12,
            13,
            14,
            15,
            16,
            24,
            27,
            29,
            32,
            35,
            36,
            37,
            38,
            39,
            40,
            41,
            42,
            43,
            44,
            45,
            46,
            47,
            49,
            50,
            51,
            52
        ));
    }

    public function loadLocalAllegroCategoriesFormFields ()
    {
        $sql = "SELECT
					ARC.sellformfields
				FROM allegrorelatedcategories ARC
        		WHERE sellformfields IS NOT NULL";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->execute();
        while ($rs = $stmt->fetch()){
            $Data = json_decode(json_encode(unserialize($rs['sellformfields'])), true);
            foreach ($Data['sell-form-fields-list'] as $field){
                if ($field['sell-form-opt'] == 1 && $this->checkNewFields($field['sell-form-id']) && $field['sell-form-title'] != 'Stan'){
                    \Gekosale\Arr::debug($field);
                }
            }
        }
    }

    public function loadRemoteAllegroCategoriesFormFields ()
    {
        $categories = $this->allegro->doGetCatsData();
        foreach ($categories['cats-list'] as $key => $category){
            echo $category['cat-id'] . PHP_EOL;
            $Data = json_decode(json_encode($this->allegro->doGetSellFormFieldsForCategory($category['cat-id'])), true);
            foreach ($Data['sell-form-fields-list'] as $field){
                if ($field['sell-form-opt'] == 1 && $this->checkNewFields($field['sell-form-id']) && $field['sell-form-title'] != 'Stan'){
                    \Gekosale\Arr::debug($field);
                }
            }
        }
    }
}
