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
 * $Id: subpagelayout.php 619 2011-12-19 21:09:00Z gekosale $ 
 */
namespace Gekosale\Plugin;

class SubpagelayoutModel extends Component\Model
{

	public function getValueForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('name', $request, $processFunction);
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getSubpageLayoutForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function getSubPageLayoutAll ($idsubpagelayout = NULL)
	{
		$Data = Array();
		$sql = "
				SELECT
					DISTINCT(SL.idsubpagelayout) AS id,
					S.name AS name,
					COUNT(idsubpagelayoutcolumn) AS columns,
					COUNT(idsubpagelayoutcolumnbox) AS boxes
				FROM
					subpagelayout SL
					LEFT JOIN subpage S ON S.idsubpage = SL.subpageid
					LEFT JOIN subpagelayoutcolumn SLC ON SL.idsubpagelayout = SLC.subpagelayoutid
					LEFT JOIN subpagelayoutcolumnbox SLCB ON SLCB.subpagelayoutcolumnid = SLC.idsubpagelayoutcolumn
				WHERE
					SL.idsubpagelayout = :idsubpagelayout
				GROUP BY S.idsubpage
				ORDER BY S.name
			";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idsubpagelayout', $idsubpagelayout);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function getSubPageLayoutAllToSelect ($idsubpagelayout = NULL, $restrict = false)
	{
		$Data = $this->getSubPageLayoutAll($idsubpagelayout);
		$tmp = Array();
		foreach ($Data as $key){
			if ($restrict == true){
				if ($key['columns'] == 0 && $key['boxes'] == 0){
					$tmp[$key['id']] = $key['name'];
				}
			}
			else{
				$tmp[$key['id']] = $key['name'];
			}
		}
		return $tmp;
	}

	public function getBoxesAll ($subpage, $pageschemeid)
	{
		$Data = Array();
		$sql = '
				SELECT
					LB.idlayoutbox AS id,
					LB.name,
					LB.controller,
					LBT.title
				FROM
					layoutbox LB
					LEFT JOIN layoutboxtranslation LBT ON LBT.layoutboxid = LB.idlayoutbox
				WHERE
					LBT.languageid = :languageid AND
					LB.pageschemeid = :pageschemeid
				ORDER BY
					LB.name ASC
		';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->bindValue('pageschemeid', $pageschemeid);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			if ($subpage != '' && $this->checkBoxForSubpage($rs['controller'], $subpage) == 1){
				$Data[] = Array(
					'id' => $rs['id'],
					'name' => $rs['name'] . ' - ' . $rs['title']
				);
			}
		}
		return $Data;
	}

	public function checkBoxForSubpage ($controller, $subpage)
	{
		if ($controller == 'CartBox' && $subpage != 'Cart'){
			return 0;
		}
		if ($controller == 'CheckoutBox' && $subpage != 'Checkout'){
			return 0;
		}
		if ($controller == 'FinalizationBox' && $subpage != 'Finalization'){
			return 0;
		}
		if ($controller == 'ProductBox' && $subpage != 'Product'){
			return 0;
		}
		if ($controller == 'ProductDescriptionBox' && $subpage != 'Product'){
			return 0;
		}
		if ($controller == 'ProductsCrossSellBox' && ! in_array($subpage, Array(
			'Product',
			'Cart'
		))){
			return 0;
		}
		if ($controller == 'ProductsSimilarBox' && ! in_array($subpage, Array(
			'Product',
			'Cart'
		))){
			return 0;
		}
		if ($controller == 'ProductsUpSellBox' && ! in_array($subpage, Array(
			'Product',
			'Cart'
		))){
			return 0;
		}
		if ($controller == 'ProductBuyAlsoBox' && $subpage != 'Product'){
			return 0;
		}
		if ($controller == 'ProductsInCategoryBox' && $subpage != 'ProductInCategory'){
			return 0;
		}
		if ($controller == 'LayeredNavigationBox' && ! in_array($subpage, Array(
			'ProductInCategory',
			'ProductSearchList'
		))){
			return 0;
		}
		if ($controller == 'ClientSettingsBox' && $subpage != 'ClientSettings'){
			return 0;
		}
		if ($controller == 'ClientAddressBox' && $subpage != 'ClientAddress'){
			return 0;
		}
		if ($controller == 'ClientOrderBox' && $subpage != 'ClientOrder'){
			return 0;
		}
		if ($controller == 'ProductSearchListBox' && $subpage != 'ProductSearchList'){
			return 0;
		}
		if ($controller == 'CmsBox' && $subpage != 'Staticcms'){
			return 0;
		}
		if ($controller == 'SitemapBox' && $subpage != 'Sitemap'){
			return 0;
		}
		if ($controller == 'PaymentBox' && $subpage != 'Payment'){
			return 0;
		}
		if ($controller == 'ProducerListBox' && $subpage != 'Producerlist'){
			return 0;
		}
		if ($controller == 'ClientLoginBox' && ! in_array($subpage, Array(
			'Registration',
			'Clientlogin'
		))){
			return 0;
		}
		if ($controller == 'RegistrationBox' && ! in_array($subpage, Array(
			'Registration',
			'Clientlogin'
		))){
			return 0;
		}
		if ($controller == 'ForgotPasswordBox' && $subpage != 'Forgotpassword'){
			return 0;
		}
		return 1;
	}

	public function getBoxesAllToSelect ($subpage = '', $pageschemeid)
	{
		$Data = $this->getBoxesAll($subpage, $pageschemeid);
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[$key['id']] = $key['name'];
		}
		return $tmp;
	}

	public function editSubpageLayout ($submitedData, $subpagelayoutid)
	{
		Db::getInstance()->beginTransaction();
		$columncounter = 0;
		$key = 0;
		$oldColumn = $this->getSubPageLayoutAllColumn($subpagelayoutid);
		if (isset($submitedData['columns']['columns_data']) && $submitedData['columns']['columns_data'] != NULL){
			foreach ($submitedData['columns']['columns_data'] as $column => $value){
				$ColumnNewId = 0;
				$ColumnId = 0;
				$columncounter = $columncounter + 1;
				if (is_numeric($column)){
					$ColumnId = $this->editSubpageLayoutColumn($column, $value['columns_width'], $columncounter);
					if (in_array($ColumnId, $oldColumn) == TRUE){
						$keyOld = array_search($ColumnId, $oldColumn);
						unset($oldColumn[$keyOld]);
					}
				}
				else{
					$ColumnNewId = $this->addSubpageLayoutColumn($submitedData['columns']['subpagelayoutid'], $value['columns_width'], $columncounter);
				}
				if ($ColumnId > 0){
					$this->deleteSubpageLayoutColumnBox($ColumnId);
					$boxOrder = 0;
					foreach ($value['layout_boxes'] as $box => $boxvalue){
						$this->addSubpageLayoutcolumnBox($ColumnId, $boxvalue, $boxOrder);
						$boxOrder ++;
					}
				}
				if ($ColumnNewId > 0){
					$boxOrder = 0;
					foreach ($value['layout_boxes'] as $box => $boxvalue){
						$this->addSubpageLayoutcolumnBox($ColumnNewId, $boxvalue, $boxOrder);
						$boxOrder ++;
					}
				}
			}
			if (count($oldColumn) > 0){
				foreach ($oldColumn as $old){
					$this->deleteSubpageLayoutColumn($old);
				}
			}
			
			Db::getInstance()->commit();
			return true;
		}
		else{
			return false;
		}
	}

	public function addSubpageLayoutForView ($submitedData)
	{
		Db::getInstance()->beginTransaction();
		$sql = '
				SELECT
					subpageid
				FROM
					subpagelayout
				WHERE
					idsubpagelayout = :idsubpagelayout
			';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idsubpagelayout', $submitedData['columns']['subpagelayoutid']);
		$stmt->execute();
		$rs = $stmt->fetch();
		$subpageid = $rs['subpageid'];
		$sql = '
				INSERT INTO
					subpagelayout
					(subpageid, viewid)
				VALUES
					(:subpageid, :viewid)
			';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('subpageid', $subpageid);
		if (Helper::getViewId() == 0){
			$stmt->bindValue('viewid', NULL);
		}
		else{
			$stmt->bindValue('viewid', Helper::getViewId());
		}
		$stmt->execute();
		$subpageLayoutId = Db::getInstance()->lastInsertId();
		$columnOrder = 0;
		if (isset($submitedData['columns']['columns_data']) && $submitedData['columns']['columns_data'] != NULL){
			foreach ($submitedData['columns']['columns_data'] as $column => $value){
				$columnId = $this->addSubpageLayoutColumn($subpageLayoutId, $value['columns_width'], ++ $columnOrder);
				if ($columnId > 0){
					$boxOrder = 0;
					foreach ($value['layout_boxes'] as $box => $boxvalue){
						$this->addSubpageLayoutcolumnBox($columnId, $boxvalue, ++ $boxOrder);
					}
				}
			}
			
			Db::getInstance()->commit();
		}
	}

	public function addSubpageLayout ($submitedData)
	{
		Db::getInstance()->beginTransaction();
		$columncounter = 0;
		if (isset($submitedData['columns']['columns_data']) && $submitedData['columns']['columns_data'] != NULL){
			foreach ($submitedData['columns']['columns_data'] as $column => $value){
				$columncounter = $columncounter + 1;
				$ColumnId = $this->addSubpageLayoutColumn($submitedData['columns']['subpagelayoutid'], $value['columns_width'], $columncounter);
				if ($ColumnId > 0){
					$boxOrder = 0;
					foreach ($value['layout_boxes'] as $box => $boxvalue){
						$boxOrder = $boxOrder + 1;
						$this->addSubpageLayoutcolumnBox($ColumnId, $boxvalue, $boxOrder);
					}
				}
			}
			
			Db::getInstance()->commit();
			return true;
		}
		else{
			return false;
		}
	}

	public function addSubpageLayoutColumn ($subpagelayoutid, $columnWidth, $order)
	{
		$sql = 'INSERT INTO subpagelayoutcolumn (subpagelayoutid, `order`, width)
						VALUES (:subpagelayoutid, :order, :width)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('subpagelayoutid', $subpagelayoutid);
		$stmt->bindValue('order', $order);
		$stmt->bindValue('width', $columnWidth);
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function editSubpageLayoutColumn ($columnId, $columnWidth, $order)
	{
		$sql = "UPDATE subpagelayoutcolumn 
					SET 
						`order`= :order,
						width= :width
					WHERE idsubpagelayoutcolumn= :columnId";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('columnId', $columnId);
		$stmt->bindValue('width', $columnWidth);
		$stmt->bindValue('order', $order);
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($e->getMessage());
		}
		return $columnId;
	}

	public function editSubpageLayoutcolumnBox ($subpageLayoutColumnId, $box, $boxvalue, $boxOrder)
	{
		$sql = "UPDATE subpagelayoutcolumnbox 
					SET 
						subpagelayoutcolumnid= :subpagelayoutcolumnid,
						layoutboxid= :layoutboxid,
						`order`= :order,
						colspan= :colspan,
						collapsed= :collapsed
					WHERE idsubpagelayoutcolumnbox= :subpagelayoutcolumnid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idsubpagelayoutcolumnbox', $box);
		$stmt->bindValue('subpagelayoutcolumnid', $subpageLayoutColumnId);
		$stmt->bindValue('layoutboxid', $boxvalue['layoutbox']);
		$stmt->bindValue('order', $boxOrder);
		$stmt->bindValue('colspan', $boxvalue['span']);
		if (isset($boxvalue['collapsed'])){
			$stmt->bindValue('collapsed', 1);
		}
		else{
			$stmt->bindValue('collapsed', 0);
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($e->getMessage());
		}
		return $box;
	}

	public function addSubpageLayoutcolumnBox ($subpageLayoutColumnId, $boxvalues, $boxOrder)
	{
		$sql = 'INSERT INTO subpagelayoutcolumnbox (subpagelayoutcolumnid, layoutboxid, `order`, colspan, collapsed)
					VALUES (:subpagelayoutcolumnid, :layoutboxid, :order, :colspan, :collapsed)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('subpagelayoutcolumnid', $subpageLayoutColumnId);
		$stmt->bindValue('layoutboxid', $boxvalues['layoutbox']);
		$stmt->bindValue('order', $boxOrder);
		$stmt->bindValue('colspan', $boxvalues['span']);
		if (isset($boxvalues['collapsed'])){
			$stmt->bindValue('collapsed', 1);
		}
		else{
			$stmt->bindValue('collapsed', 0);
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function getSubPageLayoutAllColumn ($subpagelayoutid)
	{
		$sql = "SELECT 
					SLC.idsubpagelayoutcolumn
				FROM subpagelayoutcolumn SLC
				LEFT JOIN subpagelayout SL ON SL.idsubpagelayout = SLC.subpagelayoutid
				WHERE SLC.subpagelayoutid= :subpagelayoutid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('subpagelayoutid', $subpagelayoutid);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			array_push($Data, $rs['idsubpagelayoutcolumn']);
		}
		return $Data;
	}

	public function getSubPageLayoutAllColumnBoxes ($subpagelayoutid)
	{
		$Data = Array();
		$sql = "SELECT SLCB.idsubpagelayoutcolumnbox
					FROM subpagelayoutcolumnbox SLCB
						LEFT JOIN subpagelayoutcolumn SLC ON SLCB.subpagelayoutcolumnid = SLC.idsubpagelayoutcolumn
						LEFT JOIN subpagelayout SL ON SL.idsubpagelayout = SLC.subpagelayoutid
					WHERE SL.idsubpagelayout= :subpagelayoutid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('subpagelayoutid', $subpagelayoutid);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			array_push($Data, $rs['idsubpagelayoutcolumnbox']);
		}
		return $Data;
	}

	public function getSubPageLayoutColumn ($subpagelayoutid)
	{
		$Data = Array();
		$sql = "SELECT SLC.idsubpagelayoutcolumn, SLC.subpagelayoutid, SLC.`order`, SLC.width, SLC.viewid
					FROM subpagelayoutcolumn SLC
						LEFT JOIN subpagelayout SL ON SL.idsubpagelayout = SLC.subpagelayoutid
					WHERE SL.idsubpagelayout= :subpagelayoutid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('subpagelayoutid', $subpagelayoutid);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data['subpagelayoutid'] = $rs['subpagelayoutid'];
			$Data['columns'][] = Array(
				'idsubpagelayoutcolumn' => $rs['idsubpagelayoutcolumn'],
				'subpagelayoutid' => $rs['subpagelayoutid'],
				'order' => $rs['order'],
				'width' => $rs['width'],
				'viewid' => $rs['viewid'],
				'subpagelayoutcolumnbox' => $this->getSubPageLayoutColumnBox($rs['idsubpagelayoutcolumn'])
			);
		}
		return $Data;
	}

	public function getSubPageLayoutColumnBox ($subpagelayoutcolumnid)
	{
		$Data = Array();
		$sql = "SELECT 
					SLCB.idsubpagelayoutcolumnbox, 
					SLCB.subpagelayoutcolumnid, 
					SLCB.layoutboxid, 
					LB.controller,
					SLCB.`order`, 
					SLCB.colspan, 
					SLCB.collapsed
				FROM subpagelayoutcolumnbox SLCB
				LEFT JOIN layoutbox LB ON LB.idlayoutbox = SLCB.layoutboxid
				LEFT JOIN subpagelayoutcolumn SLC ON SLC.idsubpagelayoutcolumn = SLCB.subpagelayoutcolumnid
				WHERE SLC.idsubpagelayoutcolumn = :subpagelayoutcolumnid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('subpagelayoutcolumnid', $subpagelayoutcolumnid);
		try{
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				$Data[] = Array(
					'idsubpagelayoutcolumnbox' => $rs['idsubpagelayoutcolumnbox'],
					'subpagelayoutcolumnid' => $rs['subpagelayoutcolumnid'],
					'layoutboxid' => $rs['layoutboxid'],
					'controller' => $rs['controller'],
					'order' => $rs['order'],
					'colspan' => $rs['colspan'],
					'collapsed' => $rs['collapsed']
				);
			}
			return $Data;
		}
		catch (Exception $e){
			return false;
		}
	}

	public function deleteSubpageLayoutColumn ($id)
	{
		$this->deleteSubpageLayoutColumnBox($id);
		
		DbTracker::deleteRows('subpagelayoutcolumn', 'idsubpagelayoutcolumn', $id);
	}

	public function deleteSubpageLayoutColumnBox ($id)
	{
		DbTracker::deleteRows('subpagelayoutcolumnbox', 'subpagelayoutcolumnid', $id);
	}

	public function DeleteSubpageLayout ($id)
	{
		DbTracker::deleteRows('subpagelayout', 'idsubpagelayout', $id);
	}

	public function flushCache ($subpageName)
	{
		App::getContainer()->get('cache')->delete('columns' . $subpageName);
	}

	public function getFirstPageScheme ($id)
	{
		$sql = 'SELECT 
					SL.idsubpagelayout 
				FROM subpagelayout SL
				LEFT JOIN subpage S ON SL.subpageid = S.idsubpage
		 		WHERE SL.pageschemeid = :pageschemeid ORDER BY S.name ASC LIMIT 1';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('pageschemeid', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		return $rs['idsubpagelayout'];
	}

	public function getSubpageTree ()
	{
		$sql = 'SELECT
					PS.idpagescheme AS id,
					PS.name
				FROM pagescheme PS
				LEFT JOIN view V ON V.pageschemeid = PS.idpagescheme
				WHERE IF(:viewid > 0, V.idview = :viewid, 1)
				ORDER BY name ASC';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('viewid', (int) Helper::getViewId());
		$stmt->execute();
		$Data = Array();
		$i = 0;
		while ($rs = $stmt->fetch()){
			$Data[$rs['id']] = Array(
				'name' => $rs['name'],
				'parent' => NULL,
				'weight' => $i ++
			);
			
			$sql2 = 'SELECT
						S.name,
						S.description,
						SL.idsubpagelayout 
					FROM subpagelayout SL
					LEFT JOIN subpage S ON SL.subpageid = S.idsubpage
					WHERE SL.pageschemeid = :pageschemeid
					GROUP BY SL.subpageid
					ORDER BY name ASC';
			$stmt2 = Db::getInstance()->prepare($sql2);
			$stmt2->bindValue('pageschemeid', $rs['id']);
			$stmt2->execute();
			$j = 0;
			while ($rs2 = $stmt2->fetch()){
				$Data[$rs['id'] . ',' . $rs2['idsubpagelayout']] = Array(
					'name' => $rs2['description'] . ' <small>' . $rs2['name'] . '</small>',
					'parent' => $rs['id'],
					'weight' => $j ++
				);
			}
		}
		
		return $Data;
	}
}