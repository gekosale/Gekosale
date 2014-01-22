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
 * $Revision: 576 $
 * $Author: gekosale $
 * $Date: 2011-10-22 10:23:55 +0200 (So, 22 paź 2011) $
 * $Id: similarproduct.php 576 2011-10-22 08:23:55Z gekosale $ 
 */
namespace Gekosale\Plugin;

class SimilarProductModel extends Component\Model\Datagrid
{

    protected function initDatagrid ($datagrid)
    {
        $datagrid->setTableData('similarproduct', Array(
            'idsimilarproduct' => Array(
                'source' => 'SP.productid'
            ),
            'adddate' => Array(
                'source' => 'SP.adddate'
            ),
            'name' => Array(
                'source' => 'PT.name',
                'prepareForAutosuggest' => true
            ),
            'productcount' => Array(
                'source' => 'count(distinct SP.relatedproductid)',
                'filter' => 'having'
            ),
            'categoryname' => Array(
                'source' => 'CT.name'
            ),
            'categoryid' => Array(
                'source' => 'PC.categoryid',
                'prepareForTree' => true,
                'first_level' => App::getModel('product')->getCategories()
            ),
            'ancestorcategoryid' => Array(
                'source' => 'CP.ancestorcategoryid'
            ),
            'categoriesname' => Array(
                'source' => 'GROUP_CONCAT(DISTINCT SUBSTRING(CONCAT(\' \', CT.name), 1))',
                'filter' => 'having'
            )
        ));
        
        $datagrid->setFrom('
			similarproduct SP
			LEFT JOIN producttranslation PT ON SP.productid = PT.productid AND PT.languageid = :languageid
			LEFT JOIN productcategory PC ON PC.productid = PT.productid
			LEFT JOIN viewcategory VC ON PC.categoryid = VC.categoryid
			LEFT JOIN category C ON C.idcategory = PC.categoryid
			LEFT JOIN categorypath CP ON C.idcategory = CP.categoryid
			LEFT JOIN categorytranslation CT ON C.idcategory = CT.categoryid AND CT.languageid = :languageid
		');
        
        $datagrid->setGroupBy('
				SP.productid
		');
        
        if (Helper::getViewId() > 0){
            $datagrid->setAdditionalWhere('
				VC.viewid IN (' . Helper::getViewIdsAsString() . ')
			');
        }
    }

    public function getNameForAjax ($request, $processFunction)
    {
        return $this->getDatagrid()->getFilterSuggestions('name', $request, $processFunction);
    }

    public function getDatagridFilterData ()
    {
        return $this->getDatagrid()->getFilterData();
    }

    public function getSimilarProductForAjax ($request, $processFunction)
    {
        return $this->getDatagrid()->getData($request, $processFunction);
    }

    public function doAJAXDeleteSimilarProduct ($id, $datagrid)
    {
        return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
            $this,
            'deleteSimilarProduct'
        ), $this->getName());
    }

    public function deleteSimilarProduct ($id)
    {
        DbTracker::deleteRows('similarproduct', 'productid', $id);
    }

    public function getProductsDataGrid ($id)
    {
        $sql = "SELECT 
					SP.productid AS id, 
					SP.relatedproductid as idproduct, 
					SP.hierarchy AS hierarchy,
					PT.name
 				FROM similarproduct SP
 				LEFT JOIN product P ON P.idproduct = SP.relatedproductid
 				LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
				WHERE SP.productid =:id";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $Data[] = Array(
                'id' => $rs['idproduct'],
                'hierarchy' => $rs['hierarchy']
            );
        }
        return $Data;
    }

    public function getSimilarView ($id)
    {
        $sql = "SELECT 
					CS.productid AS id, 
					PT.name
				FROM similarproduct CS
				LEFT JOIN product P ON P.idproduct= CS.productid
				LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
				WHERE CS.productid=:id";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->execute();
        $rs = $stmt->fetch();
        $Data = Array();
        if ($rs){
            $Data = Array(
                'id' => $rs['id'],
                'name' => $rs['name'],
                'relatedproduct' => $this->getSimilarProductView($id)
            );
        }
        else{
            throw new CoreException($this->trans('ERR_SIMILARPRODUCT_NO_EXIST'));
        }
        return $Data;
    }

    public function getSimilarProductView ($id)
    {
        $sql = "SELECT 
					PT.name AS relatedproduct
				FROM similarproduct CS
				LEFT JOIN product P ON P.idproduct= CS.relatedproductid
				LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
				WHERE CS.productid=:id";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $Data[] = Array(
                'relatedproduct' => $rs['relatedproduct']
            );
        }
        return $Data;
    }

    public function addNewRelated ($Data)
    {
        Db::getInstance()->beginTransaction();
        try{
            $this->addSimilarProduct($Data['products'], $Data['productid']);
            foreach ($Data['products'] as $key => $product){
                $Product = Array();
                $Product[] = Array(
                    'id' => $Data['productid'],
                    'hierarchy' => 0
                );
                $this->addSimilarProduct($Product, $product['id']);
            }
        }
        catch (Exception $e){
            throw new CoreException($this->trans('ERR_SIMILAR_PRODUCT_NEW_ADD'), 12, $e->getMessage());
        }
        
        Db::getInstance()->commit();
        return true;
    }

    public function addSimilarProduct ($Data, $id)
    {
        foreach ($Data as $value){
            
            $sql = 'INSERT INTO similarproduct SET
						productid = :productid, 
						relatedproductid = :relatedproductid,
						hierarchy = :hierarchy
					ON DUPLICATE KEY UPDATE
						hierarchy = :hierarchy';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('productid', $id);
            $stmt->bindValue('relatedproductid', $value['id']);
            $stmt->bindValue('hierarchy', $value['hierarchy']);
            try{
                $stmt->execute();
            }
            catch (Exception $e){
                throw new CoreException($this->trans('ERR_SIMILAR_PRODUCT_ADD'), 11, $e->getMessage());
            }
        }
    }

    public function editRelated ($Data, $id)
    {
        Db::getInstance()->beginTransaction();
        try{
            $this->deleteSimilarProductById($id);
            $this->addSimilarProduct($Data['products'], $id);
            
            foreach ($Data['products'] as $key => $product){
                $Product = Array();
                $Product[] = Array(
                    'id' => $id,
                    'hierarchy' => 0
                );
                $this->addSimilarProduct($Product, $product['id']);
            }
        }
        catch (Exception $e){
            throw new CoreException($this->trans('ERR_SIMILAR_PRODUCT_EDIT'), 10, $e->getMessage());
        }
        
        Db::getInstance()->commit();
        return true;
    }

    public function deleteSimilarProductById ($id)
    {
        DbTracker::deleteRows('similarproduct', 'productid', $id);
    }
}