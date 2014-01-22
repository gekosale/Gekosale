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
 * $Id: sitemaps.php 438 2011-08-27 09:29:36Z gekosale $ 
 */
namespace Gekosale\Plugin;

class SitemapsModel extends Component\Model\Datagrid
{

    protected function initDatagrid ($datagrid)
    {
        $datagrid->setTableData('sitemaps', Array(
            'idsitemaps' => Array(
                'source' => 'S.idsitemaps'
            ),
            'name' => Array(
                'source' => 'S.name'
            ),
            'pingserver' => Array(
                'source' => 'S.pingserver'
            ),
            'lastupdate' => Array(
                'source' => 'S.lastupdate'
            ),
            'adddate' => Array(
                'source' => 'S.adddate'
            ),
            'link' => Array(
                'source' => 'S.idsitemaps',
                'processFunction' => Array(
                    $this,
                    'getLink'
                )
            )
        ));
        
        $datagrid->setFrom('
			sitemaps S
		');
        
        $datagrid->setGroupBy('
			S.idsitemaps
		');
    }

    public function getLink ($id)
    {
        return App::getURLAdress() . Seo::getSeo('sitemap') . '/' . $id;
    }

    public function doAJAXRefreshSitemaps ($datagridId, $id)
    {
        try{
            $this->refreshSitemaps($id);
            return $this->getDatagrid()->refresh($datagridId);
        }
        catch (Exception $e){
            $objResponse = new xajaxResponse();
            $objResponse->script("GF_Alert('{$this->trans('ERR_UNABLE_TO_REFRESH_SITEMAPS')}', '{$e->getMessage()}');");
            return $objResponse;
        }
    }

    public function refreshSitemaps ($id)
    {
        $sql = 'SELECT REPLACE(pingserver,\'{SITEMAP_URL}\',CONCAT(:url,\'sitemap/\',:id)) as pingserver
				FROM sitemaps 
				WHERE idsitemaps = :id';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->bindValue('url', URL);
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $rs['pingserver']);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_NOBODY, 1);
            curl_exec($ch);
            curl_close($ch);
        }
        
        $sql = 'UPDATE sitemaps SET lastupdate = now() WHERE idsitemaps = :id';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        try{
            $stmt->execute();
        }
        catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function getTopicForAjax ($request, $processFunction)
    {
        return $this->getDatagrid()->getFilterSuggestions('topic', $request, $processFunction);
    }

    public function getDatagridFilterData ()
    {
        return $this->getDatagrid()->getFilterData();
    }

    public function getSitemapsForAjax ($request, $processFunction)
    {
        return $this->getDatagrid()->getData($request, $processFunction);
    }

    public function doAJAXDeleteSitemaps ($id, $datagrid)
    {
        return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
            $this,
            'deleteSitemaps'
        ), $this->getName());
    }

    public function deleteSitemaps ($id)
    {
        DbTracker::deleteRows('sitemaps', 'idsitemaps', $id);
    }

    public function getDataById ($id)
    {
        $Data = $this->getSitemapsView($id);
        
        return Array(
            'required_data' => Array(
                'name' => $Data['name'],
                'pingserver' => $Data['pingserver']
            ),
            'settings_data' => Array(
                'publishforcategories' => $Data['publishforcategories'],
                'priorityforcategories' => $Data['priorityforcategories'],
                'publishforproducts' => $Data['publishforproducts'],
                'priorityforproducts' => $Data['priorityforproducts'],
                'publishforproducers' => $Data['publishforproducers'],
                'priorityforproducers' => $Data['priorityforproducers'],
                'publishfornews' => $Data['publishfornews'],
                'priorityfornews' => $Data['priorityfornews'],
                'publishforpages' => $Data['publishforpages'],
                'priorityforpages' => $Data['priorityforpages'],
                'changefreqforcategories' => $Data['changefreqforcategories'],
                'changefreqforproducts' => $Data['changefreqforproducts'],
                'changefreqforproducers' => $Data['changefreqforproducers'],
                'changefreqfornews' => $Data['changefreqfornews'],
                'changefreqforpages' => $Data['changefreqforpages']
            )
        );
    }

    public function getSitemapsView ($id)
    {
        $sql = "SELECT 
					*
				FROM sitemaps
				WHERE idsitemaps =:id";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $Data = Array(
                'name' => $rs['name'],
                'pingserver' => $rs['pingserver'],
                'publishforcategories' => $rs['publishforcategories'],
                'priorityforcategories' => $rs['priorityforcategories'],
                'publishforproducts' => $rs['publishforproducts'],
                'priorityforproducts' => $rs['priorityforproducts'],
                'publishforproducers' => $rs['publishforproducers'],
                'priorityforproducers' => $rs['priorityforproducers'],
                'publishfornews' => $rs['publishfornews'],
                'priorityfornews' => $rs['priorityfornews'],
                'publishforpages' => $rs['publishforpages'],
                'priorityforpages' => $rs['priorityforpages'],
                'changefreqforcategories' => $rs['changefreqforcategories'],
                'changefreqforproducts' => $rs['changefreqforproducts'],
                'changefreqforproducers' => $rs['changefreqforproducers'],
                'changefreqfornews' => $rs['changefreqfornews'],
                'changefreqforpages' => $rs['changefreqforpages']
            );
        }
        return $Data;
    }

    public function addSitemaps ($Data)
    {
        $sql = 'INSERT INTO sitemaps SET
						name = :name, 
						publishforcategories = :publishforcategories, 
						priorityforcategories = :priorityforcategories, 
						publishforproducts = :publishforproducts, 
						priorityforproducts = :priorityforproducts, 
						publishforproducers = :publishforproducers, 
						priorityforproducers = :priorityforproducers, 
						publishfornews = :publishfornews, 
						priorityfornews = :priorityfornews, 
						publishforpages = :publishforpages, 
						priorityforpages = :priorityforpages, 
						pingserver = :pingserver,
        				changefreqforcategories = :changefreqforcategories,
                		changefreqforproducts = :changefreqforproducts,
		                changefreqforproducers = :changefreqforproducers,
		                changefreqfornews = :changefreqfornews,
		                changefreqforpages = :changefreqforpages';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('name', $Data['name']);
        $stmt->bindValue('pingserver', $Data['pingserver']);
        
        if (isset($Data['publishforcategories']) && $Data['publishforcategories'] == 1){
            $stmt->bindValue('publishforcategories', 1);
            $stmt->bindValue('priorityforcategories', $Data['priorityforcategories']);
            $stmt->bindValue('changefreqforcategories', $Data['changefreqforcategories']);
        }
        else{
            $stmt->bindValue('publishforcategories', 0);
            $stmt->bindValue('priorityforcategories', NULL);
            $stmt->bindValue('changefreqforcategories', 'always');
        }
        
        if (isset($Data['publishforproducts']) && $Data['publishforproducts'] == 1){
            $stmt->bindValue('publishforproducts', 1);
            $stmt->bindValue('priorityforproducts', $Data['priorityforproducts']);
            $stmt->bindValue('changefreqforproducts', $Data['changefreqforproducts']);
        }
        else{
            $stmt->bindValue('publishforproducts', 0);
            $stmt->bindValue('priorityforproducts', NULL);
            $stmt->bindValue('changefreqforproducts', 'always');
        }
        
        if (isset($Data['publishforproducers'])){
            $stmt->bindValue('publishforproducers', 1);
            $stmt->bindValue('priorityforproducers', $Data['priorityforproducers']);
            $stmt->bindValue('changefreqforproducers', $Data['changefreqforproducers']);
        }
        else{
            $stmt->bindValue('publishforproducers', 0);
            $stmt->bindValue('priorityforproducers', NULL);
            $stmt->bindValue('changefreqforproducers', 'always');
        }
        
        if (isset($Data['publishfornews']) && $Data['publishfornews'] == 1){
            $stmt->bindValue('publishfornews', 1);
            $stmt->bindValue('priorityfornews', $Data['priorityfornews']);
            $stmt->bindValue('changefreqfornews', $Data['changefreqfornews']);
        }
        else{
            $stmt->bindValue('publishfornews', 0);
            $stmt->bindValue('priorityfornews', NULL);
            $stmt->bindValue('changefreqfornews', 'always');
        }
        
        if (isset($Data['publishforpages']) && $Data['publishforpages'] == 1){
            $stmt->bindValue('publishforpages', 1);
            $stmt->bindValue('priorityforpages', $Data['priorityforpages']);
            $stmt->bindValue('changefreqforpages', $Data['changefreqforpages']);
        }
        else{
            $stmt->bindValue('publishforpages', 0);
            $stmt->bindValue('priorityforpages', NULL);
            $stmt->bindValue('changefreqforpages', 'always');
        }
        
        try{
            $stmt->execute();
        }
        catch (Exception $e){
            throw new CoreException($this->trans('ERR_SITEMAPS_ADD'), 4, $e->getMessage());
        }
        
        return true;
    }

    public function editSitemaps ($Data, $id)
    {
        $sql = 'UPDATE sitemaps SET
						name = :name, 
						publishforcategories = :publishforcategories, 
						priorityforcategories = :priorityforcategories, 
						publishforproducts = :publishforproducts, 
						priorityforproducts = :priorityforproducts, 
						publishforproducers = :publishforproducers, 
						priorityforproducers = :priorityforproducers, 
						publishfornews = :publishfornews, 
						priorityfornews = :priorityfornews, 
						publishforpages = :publishforpages, 
						priorityforpages = :priorityforpages, 
						pingserver = :pingserver,
        				changefreqforcategories = :changefreqforcategories,
                		changefreqforproducts = :changefreqforproducts,
		                changefreqforproducers = :changefreqforproducers,
		                changefreqfornews = :changefreqfornews,
		                changefreqforpages = :changefreqforpages
					WHERE idsitemaps = :id';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('name', $Data['name']);
        $stmt->bindValue('pingserver', $Data['pingserver']);
        
        if (isset($Data['publishforcategories']) && $Data['publishforcategories'] == 1){
            $stmt->bindValue('publishforcategories', 1);
            $stmt->bindValue('priorityforcategories', $Data['priorityforcategories']);
            $stmt->bindValue('changefreqforcategories', $Data['changefreqforcategories']);
        }
        else{
            $stmt->bindValue('publishforcategories', 0);
            $stmt->bindValue('priorityforcategories', NULL);
            $stmt->bindValue('changefreqforcategories', 'always');
        }
        
        if (isset($Data['publishforproducts']) && $Data['publishforproducts'] == 1){
            $stmt->bindValue('publishforproducts', 1);
            $stmt->bindValue('priorityforproducts', $Data['priorityforproducts']);
            $stmt->bindValue('changefreqforproducts', $Data['changefreqforproducts']);
        }
        else{
            $stmt->bindValue('publishforproducts', 0);
            $stmt->bindValue('priorityforproducts', NULL);
            $stmt->bindValue('changefreqforproducts', 'always');
        }
        
        if (isset($Data['publishforproducers'])){
            $stmt->bindValue('publishforproducers', 1);
            $stmt->bindValue('priorityforproducers', $Data['priorityforproducers']);
            $stmt->bindValue('changefreqforproducers', $Data['changefreqforproducers']);
        }
        else{
            $stmt->bindValue('publishforproducers', 0);
            $stmt->bindValue('priorityforproducers', NULL);
            $stmt->bindValue('changefreqforproducers', 'always');
        }
        
        if (isset($Data['publishfornews']) && $Data['publishfornews'] == 1){
            $stmt->bindValue('publishfornews', 1);
            $stmt->bindValue('priorityfornews', $Data['priorityfornews']);
            $stmt->bindValue('changefreqfornews', $Data['changefreqfornews']);
        }
        else{
            $stmt->bindValue('publishfornews', 0);
            $stmt->bindValue('priorityfornews', NULL);
            $stmt->bindValue('changefreqfornews', 'always');
        }
        
        if (isset($Data['publishforpages']) && $Data['publishforpages'] == 1){
            $stmt->bindValue('publishforpages', 1);
            $stmt->bindValue('priorityforpages', $Data['priorityforpages']);
            $stmt->bindValue('changefreqforpages', $Data['changefreqforpages']);
        }
        else{
            $stmt->bindValue('publishforpages', 0);
            $stmt->bindValue('priorityforpages', NULL);
            $stmt->bindValue('changefreqforpages', 'always');
        }
        
        $stmt->bindValue('id', $id);
        try{
            $stmt->execute();
        }
        catch (Exception $e){
            throw new CoreException($this->trans('ERR_SITEMAPS_EDIT'), 4, $e->getMessage());
        }
        
        return true;
    }
}