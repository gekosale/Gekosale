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
class AllegrousertemplateModel extends ModelWithDatagrid
{

	public function __construct ($registry, $modelFile)
	{
		parent::__construct($registry, $modelFile);
	}

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('allegrousertemplate', Array(
			'idallegrousertemplate' => Array(
				'source' => 'idallegrousertemplate'
			),
			'name' => Array(
				'source' => 'name',
				'prepareForAutosuggest' => true
			),
			'title' => Array(
				'source' => 'title'
			)
		));
		$datagrid->setFrom('
				allegrousertemplate
			');
		if (Helper::getViewId() > 0){
			$datagrid->setAdditionalWhere('
			viewid IN (:viewids)
		');
		}
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getAllegrousertemplateForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteAllegrousertemplate ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteAllegrousertemplate'
		), $this->getName());
	}

	public function getAllegrousertemplateAll ()
	{
		$sql = 'SELECT AUT.idallegrousertemplate as id, AUT.name, AUT.title, AUT.htmlform 
					FROM allegrousertemplate AUT';
		$Data = Array();
		$stmt = $this->registry->db->prepareStatement($sql);
		$rs = $stmt->executeQuery();
		while ($rs->next()){
			$Data[] = Array(
				'id' => $rs->getInt('id'),
				'name' => $rs->getString('name'),
				'title' => $rs->getString('title'),
				'htmlform' => $rs->getString('htmlform')
			);
		}
		return $Data;
	}

	public function getAllegrousertemplatesToSelect ()
	{
		$Data = $this->getAllegrousertemplatesAll();
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[$key['id']] = $key['name'];
		}
		return $tmp;
	}

	public function addNewAllegroUserTemplate ($submitedData)
	{
		$sql = 'INSERT INTO allegrousertemplate(
						name, 
						title,
						htmlform,
						editid)
					VALUES (
						:name, 
						:title,
						:htmlform,
						:addid)';
		$stmt = $this->registry->db->prepareStatement($sql);
		$stmt->setString('name', $submitedData['templatename']);
		$stmt->setString('title', $submitedData['templatetitle']);
		$stmt->setString('htmlform', $submitedData['htmlform']);
		$stmt->setInt('addid', App::getContainer()->get('session')->getActiveUserid());
		try{
			$stmt->executeQuery();
		}
		catch (Exception $e){
			throw new CoreException($this->registry->core->getMessage('ERR_ALLEGRO_USER_TEMPLATE_EDIT'), $e->getMessage());
		}
		return true;
	}

	public function getAllegroUserTemplateView ($idTemplates)
	{
		$sql = "SELECT AUT.name, AUT.title, AUT.htmlform 
					FROM allegrousertemplate AUT
					WHERE AUT.idallegrousertemplate = :idTemplates";
		$stmt = $this->registry->db->prepareStatement($sql);
		$stmt->setInt('idTemplates', $idTemplates);
		$Data = Array();
		try{
			$rs = $stmt->executeQuery();
			if ($rs->first()){
				$Data = Array(
					'name' => $rs->getString('name'),
					'title' => $rs->getString('title'),
					'htmlform' => $rs->getString('htmlform')
				);
			}
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		return $Data;
	}

	public function editAllegroUserTemplate ($submitedData, $idTemplates)
	{
		$sql = 'UPDATE allegrousertemplate 
					SET name= :name, title= :title, htmlform= :htmlform, editid= :editid 
					WHERE idallegrousertemplate= :idTemplates';
		$stmt = $this->registry->db->prepareStatement($sql);
		$stmt->setString('name', $submitedData['templatename']);
		$stmt->setString('title', $submitedData['templatetitle']);
		$stmt->setString('htmlform', $submitedData['htmlform']);
		$stmt->setInt('editid', App::getContainer()->get('session')->getActiveUserid());
		$stmt->setInt('idTemplates', $idTemplates);
		try{
			$stmt->executeUpdate();
		}
		catch (Exception $e){
			throw new CoreException($this->registry->core->getMessage('ERR_ALLEGRO_USER_TEMPLATE_EDIT'), 13, $e->getMessage());
		}
		return true;
	}

}
?>