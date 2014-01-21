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
 * $Id: view.php 576 2011-10-22 08:23:55Z gekosale $
 */
namespace Gekosale;

use xajaxResponse;

class ViewModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('view', Array(
			'idview' => Array(
				'source' => 'idview'
			),
			'name' => Array(
				'source' => 'V.name'
			),
			'namespace' => Array(
				'source' => 'V.namespace'
			),
			'store' => Array(
				'source' => 'S.shortcompanyname'
			),
			'url' => Array(
				'source' => 'VU.url'
			)
		));
		
		$datagrid->setFrom('
			view V
			LEFT JOIN viewurl VU ON VU.viewid = idview
			LEFT JOIN store S ON S.idstore = storeid
		');
		
		$datagrid->setGroupBy('
			V.idview
		');
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getViewForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteView ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteView'
		), $this->getName());
	}

	public function deleteView ($id)
	{
		if ($id == 3){
			return Array(
				'error' => $this->trans('ERR_DELETE_ONLY_VIEW')
			);
		}
		
		DbTracker::deleteRows('view', 'idview', $id);
		App::getContainer()->get('session')->setActiveViewIds(NULL);
	}

	public function getView ($id)
	{
		$sql = "SELECT * FROM view V WHERE idview=:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'name' => $rs['name'],
				'namespace' => $rs['namespace'],
				'store' => $rs['storeid'],
				'pageschemeid' => $rs['pageschemeid'],
				'currencyid' => $rs['currencyid'],
				'contactid' => (int) $rs['contactid'],
				'googleappstag' => $rs['googleappstag'],
				'googleappsfile' => array(
					'file' => $rs['googleappsfile']
				),
				'gatransactions' => $rs['gatransactions'],
				'periodid' => $rs['periodid'],
				'orderstatusgroupsid' => $rs['orderstatusgroupsid'],
				'language' => $this->getViewTranslation($id),
				'taxes' => $rs['taxes'],
				'showtax' => $rs['showtax'],
				'defaultvatid' => $rs['defaultvatid'],
				'category' => $this->viewCategoryIds($rs['idview']),
				'dispatchmethods' => $this->getDispatchmethodView($id),
				'paymentmethods' => $this->getPaymentmethodView($id),
				'url' => $this->getViewUrl($id),
				'enablegiftwrap' => $rs['enablegiftwrap'],
				'giftwrapproduct' => $rs['giftwrapproduct'],
				'offline' => $rs['offline'],
				'offlinetext' => $rs['offlinetext'],
				'cartredirect' => $rs['cartredirect'],
				'invoicenumerationkind' => $rs['invoicenumerationkind'],
				'invoicedefaultpaymentdue' => $rs['invoicedefaultpaymentdue'],
				'forcelogin' => $rs['forcelogin'],
				'confirmregistration' => $rs['confirmregistration'],
				'enableregistration' => $rs['enableregistration'],
				'minimumordervalue' => $rs['minimumordervalue'],
				'photo' => Array(
					'file' => $rs['photoid']
				),
				'favicon' => Array(
					'file' => $rs['favicon']
				),
				'watermark' => Array(
					'file' => $rs['watermark']
				),
				'uploaderenabled' => $rs['uploaderenabled'],
				'uploaderenabled' => $rs['uploaderenabled'],
				'uploadmaxfilesize' => $rs['uploadmaxfilesize'],
				'uploadchunksize' => $rs['uploadchunksize'],
				'uploadextensions' => explode(',', $rs['uploadextensions']),
				'sendingo' => $rs['sendingo'],
				'mailer' => App::getModel('mailer')->getSettings($id),
				'wwwredirection' => $rs['wwwredirection']
			);
		}
		return $Data;
	}

	public function getDefaultVatId ()
	{
		$sql = "SELECT
					defaultvatid
				FROM view
				WHERE idview = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', Helper::getViewId());
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			return $rs['defaultvatid'];
		}
	}

	public function getViewUrl ($id)
	{
		$sql = "SELECT
					url
				FROM viewurl
				WHERE viewid=:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			return $rs['url'];
		}
	}

	public function getDispatchmethodView ($id)
	{
		$sql = "SELECT dispatchmethodid
					FROM dispatchmethodview
					WHERE viewid=:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['dispatchmethodid'];
		}
		return $Data;
	}

	public function getPaymentmethodView ($id)
	{
		$sql = "SELECT paymentmethodid
					FROM paymentmethodview
					WHERE viewid=:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['paymentmethodid'];
		}
		return $Data;
	}

	public function getViewTranslation ($id)
	{
		$sql = "SELECT
					keyword_title,
					keyword,
					keyword_description,
					additionalmeta,
					languageid
				FROM viewtranslation
				WHERE viewid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['languageid']] = Array(
				'keyword_title' => $rs['keyword_title'],
				'keyword' => $rs['keyword'],
				'keyword_description' => $rs['keyword_description'],
				'additionalmeta' => $rs['additionalmeta']
			);
		}
		return $Data;
	}

	public function addView ($Data)
	{
		Db::getInstance()->beginTransaction();
		try{
			$newViewId = $this->addViewData($Data);
			$this->updateViewUrl($Data['url'], $newViewId);
			$this->addViewToCategory($Data['category'], $newViewId);
			$this->addViewTranslation($Data, $newViewId);
			$this->addAssignToGroupPerView($Data['table']['ranges'], $newViewId);
			if (! is_array($Data['paymentmethod']) || count($Data['paymentmethod']) == 0){
				$Data['paymentmethod'] = array_keys(App::getModel('paymentmethod')->getPaymentmethodToSelect());
			}
			$this->addPaymentmethod($Data['paymentmethod'], $newViewId);
			
			if (! is_array($Data['dispatchmethod']) || count($Data['dispatchmethod']) == 0){
				$Data['dispatchmethod'] = array_keys(App::getModel('dispatchmethod')->getDispatchmethodToSelect());
			}
			$this->addDispatchethod($Data['dispatchmethod'], $newViewId);
			
			$languages = App::getModel('language')->getLanguages();
			
			foreach ($languages as $languageid => $language){
				$sql = 'INSERT INTO languageview (viewid, languageid)
						VALUES (:viewid, :languageid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('languageid', $languageid);
				$stmt->bindValue('viewid', $newViewId);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
			}
			
			App::getModel('mailer')->saveSettings($Data, $newViewId);
			
			Event::notify($this, 'admin.view.model.save', Array(
				'id' => $newViewId,
				'data' => $Data
			));
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_SHOP_ADD'), 11, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		$this->flushCache();
		return true;
	}

	public function addPaymentmethod ($Data, $id)
	{
		foreach ($Data as $value){
			$sql = 'INSERT INTO paymentmethodview (paymentmethodid, viewid)
					VALUES (:paymentmethodid, :viewid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('paymentmethodid', $value);
			$stmt->bindValue('viewid', $id);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
	}

	public function addDispatchethod ($Data, $id)
	{
		foreach ($Data as $value){
			$sql = 'INSERT INTO dispatchmethodview (dispatchmethodid, viewid)
					VALUES (:dispatchmethodid, :viewid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('dispatchmethodid', $value);
			$stmt->bindValue('viewid', $id);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
	}

	public function addViewTranslation ($Data, $id)
	{
		foreach ($Data['keyword'] as $key => $val){
			$sql = 'INSERT INTO viewtranslation (keyword_title, keyword,keyword_description, additionalmeta, viewid, languageid)
					VALUES (:keyword_title, :keyword,:keyword_description, :additionalmeta, :viewid, :languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('viewid', $id);
			$stmt->bindValue('keyword_title', $Data['keyword_title'][$key]);
			$stmt->bindValue('keyword', $Data['keyword'][$key]);
			$stmt->bindValue('keyword_description', $Data['keyword_description'][$key]);
			$stmt->bindValue('additionalmeta', $Data['additionalmeta'][$key]);
			$stmt->bindValue('languageid', $key);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_VIEW_TRANSLATION_ADD'), 4, $e->getMessage());
			}
		}
		return true;
	}

	public function addViewData ($Data)
	{
		$sql = 'INSERT INTO view (
					name,
					namespace,
					storeid,
					pageschemeid,
					currencyid,
					contactid,
					periodid,
					orderstatusgroupsid,
					taxes,
					showtax,
					defaultvatid,
					googleappstag,
					googleappsfile,
					enablegiftwrap,
					giftwrapproduct,
					offline,
					offlinetext,
					cartredirect,
					invoicenumerationkind,
					invoicedefaultpaymentdue,
					forcelogin,
					confirmregistration,
					enableregistration,
					minimumordervalue,
					photoid,
					favicon,
					watermark,
					uploaderenabled,
					uploadmaxfilesize,
					uploadchunksize,
					uploadextensions,
					wwwredirection)
				VALUES (
					:name,
					:namespace,
					:storeid,
					:pageschemeid,
					:currencyid,
					:contactid,
					:periodid,
					:orderstatusgroupsid,
					:taxes,
					:showtax,
					:defaultvatid,
					:googleappstag,
					:googleappsfile,
					:enablegiftwrap,
					:giftwrapproduct,
					:offline,
					:offlinetext,
					:cartredirect,
					:invoicenumerationkind,
					:invoicedefaultpaymentdue,
					:forcelogin,
					:confirmregistration,
					:enableregistration,
					:minimumordervalue,
					:photoid,
					:favicon,
					:watermark,
					:uploaderenabled,
					:uploadmaxfilesize,
					:uploadchunksize,
					:uploadextensions,
					:wwwredirection
				)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $Data['name']);
		$stmt->bindValue('wwwredirection', (int) $Data['wwwredirection']);
		$stmt->bindValue('namespace', $Data['namespace']);
		$stmt->bindValue('storeid', $Data['store']);
		$stmt->bindValue('pageschemeid', $Data['pageschemeid']);
		$stmt->bindValue('currencyid', $Data['currencyid']);
		$stmt->bindValue('contactid', ($Data['contactid'] == 0) ? NULL : $Data['contactid']);
		$stmt->bindValue('periodid', $Data['periodid']);
		$stmt->bindValue('orderstatusgroupsid', $Data['orderstatusgroupsid']);
		$stmt->bindValue('taxes', $Data['taxes']);
		$stmt->bindValue('showtax', $Data['showtax']);
		$stmt->bindValue('defaultvatid', $Data['defaultvatid']);
	
		if (isset($Data['googleappstag']) && preg_match('/^<meta name="google-site-verification" content="[a-zA-Z0-9-]+"\s?\/>$/i', $Data['googleappstag'])){
			$stmt->bindValue('googleappstag', $Data['googleappstag']);
		}
		else{
			$stmt->bindValue('googleappstag', '');
		}
		
		if (isset($Data['googleappsfile']['file']) && preg_match('/^google[a-f0-9]+\.html$/', $Data['googleappsfile']['file'])){
			copy(ROOTPATH . DS . 'upload' . DS . $Data['googleappsfile']['file'], ROOTPATH . DS . $Data['googleappsfile']['file']);
			$stmt->bindValue('googleappsfile', $Data['googleappsfile']['file']);
		}
		else{
			$stmt->bindValue('googleappsfile', '');
		}
		
		if (isset($Data['offline']) && ! is_null($Data['offline'])){
			$stmt->bindValue('offline', 1);
		}
		else{
			$stmt->bindValue('offline', 0);
		}
		$stmt->bindValue('enablegiftwrap', ($Data['enablegiftwrap'] == 1) ? 1 : 0);
		$stmt->bindValue('giftwrapproduct', ((int) $Data['giftwrapproduct'] > 0) ? $Data['giftwrapproduct'] : NULL);
		$stmt->bindValue('offlinetext', $Data['offlinetext']);
		if (isset($Data['cartredirect']) && ! is_null($Data['cartredirect'])){
			$stmt->bindValue('cartredirect', 1);
		}
		else{
			$stmt->bindValue('cartredirect', 0);
		}
		$stmt->bindValue('invoicenumerationkind', $Data['invoicenumerationkind']);
		$stmt->bindValue('invoicedefaultpaymentdue', $Data['invoicedefaultpaymentdue']);
		
		if (isset($Data['forcelogin']) && ! is_null($Data['forcelogin'])){
			$stmt->bindValue('forcelogin', 1);
		}
		else{
			$stmt->bindValue('forcelogin', 0);
		}
		if (isset($Data['confirmregistration']) && ! is_null($Data['confirmregistration'])){
			$stmt->bindValue('confirmregistration', 1);
		}
		else{
			$stmt->bindValue('confirmregistration', 0);
		}
		if (isset($Data['enableregistration']) && ! is_null($Data['enableregistration'])){
			$stmt->bindValue('enableregistration', 1);
		}
		else{
			$stmt->bindValue('enableregistration', 0);
		}
		$stmt->bindValue('minimumordervalue', $Data['minimumordervalue']);
		
		if (isset($Data['photo']['file'])){
			$stmt->bindValue('photoid', $Data['photo']['file']);
		}
		else{
			$stmt->bindValue('photoid', '');
		}
		if (isset($Data['favicon']['file'])){
			$stmt->bindValue('favicon', $Data['favicon']['file']);
		}
		else{
			$stmt->bindValue('favicon', '');
		}
		if (isset($Data['watermark']['file'])){
			$stmt->bindValue('watermark', $Data['watermark']['file']);
		}
		else{
			$stmt->bindValue('watermark', '');
		}
		if (isset($Data['uploaderenabled']) && ! is_null($Data['uploaderenabled'])){
			$stmt->bindValue('uploaderenabled', 1);
			$stmt->bindValue('uploadmaxfilesize', $Data['uploadmaxfilesize']);
			$stmt->bindValue('uploadchunksize', $Data['uploadchunksize']);
			$stmt->bindValue('uploadextensions', implode(',', $Data['uploadextensions']));
		}
		else{
			$stmt->bindValue('uploaderenabled', 0);
			$stmt->bindValue('uploadmaxfilesize', 0);
			$stmt->bindValue('uploadchunksize', 0);
			$stmt->bindValue('uploadextensions', '');
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_SHOP_ADD'), 11, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function addViewToCategory ($Data, $id)
	{
		if (is_array($Data) && ! empty($Data)){
			foreach ($Data as $category){
				$sql = 'INSERT INTO viewcategory (viewid, categoryid)
					VALUES (:viewid, :categoryid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('viewid', $id);
				$stmt->bindValue('categoryid', $category);
				
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_VIEW_CATEGORY_ADD'), 112, $e->getMessage());
				}
			}
		}
	}

	public function addAssignToGroupPerView ($array, $viewid)
	{
		foreach ($array as $key => $value){
			$sql = 'INSERT INTO assigntogroup (clientgroupid, `from`, `to`, viewid)
					VALUES (:clientgroupid, :from, :to, :viewid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('viewid', $viewid);
			if (isset($value['min'])){
				$stmt->bindValue('from', $value['min']);
			}
			else{
				$stmt->bindValue('from', 0.00);
			}
			if (isset($value['max'])){
				$stmt->bindValue('to', $value['max']);
			}
			else{
				$stmt->bindValue('to', 0.00);
			}
			$stmt->bindValue('clientgroupid', $value['price']);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
		}
	}

	public function editView ($Data, $id)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->updateView($Data, $id);
			$this->updateViewUrl($Data['url'], $id);
			$this->updateViewTranslation($Data, $id);
			$this->updateAssignToGroupPerView($Data['table']['ranges'], $id);
			$this->updateDispatchmethodView($Data['dispatchmethod'], $id);
			$this->updatePaymentmethodView($Data['paymentmethod'], $id);
			App::getModel('mailer')->saveSettings($Data, $id);
			Event::notify($this, 'admin.view.model.save', Array(
				'id' => $id,
				'data' => $Data
			));
			
			$this->registry->core->saveModuleSettings('privacy', Array(
				'privacypolicyenabled' => $Data['privacypolicyenabled'],
				'privacypolicyid' => $Data['privacypolicyid']
			), $id);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_SHOP_EDIT'), 125, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		$this->flushCache();
		return true;
	}

	public function updateViewUrl ($value, $id)
	{
		DbTracker::deleteRows('viewurl', 'viewid', $id);
		
		$sql = 'INSERT INTO viewurl (url, viewid)
				VALUES (:url, :viewid)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('url', $value);
		$stmt->bindValue('viewid', $id);
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function updatePaymentmethodView ($Data, $id)
	{
		DbTracker::deleteRows('paymentmethodview', 'viewid', $id);
		
		if (! empty($Data) && is_array($Data)){
			foreach ($Data as $value){
				$sql = 'INSERT INTO paymentmethodview (paymentmethodid, viewid)
							VALUES (:paymentmethodid, :viewid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('paymentmethodid', $value);
				$stmt->bindValue('viewid', $id);
				
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
			}
		}
	}

	public function updateDispatchmethodView ($Data, $id)
	{
		DbTracker::deleteRows('dispatchmethodview', 'viewid', $id);
		
		if (! empty($Data) && is_array($Data)){
			foreach ($Data as $value){
				$sql = 'INSERT INTO dispatchmethodview (dispatchmethodid, viewid)
							VALUES (:dispatchmethodid, :viewid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('dispatchmethodid', $value);
				$stmt->bindValue('viewid', $id);
				
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
			}
		}
	}

	public function updateViewTranslation ($Data, $id)
	{
		DbTracker::deleteRows('viewtranslation', 'viewid', $id);
		
		if (! empty($Data) && is_array($Data)){
			foreach ($Data['keyword'] as $key => $val){
				$sql = 'INSERT INTO viewtranslation (keyword_title, keyword,keyword_description, additionalmeta, viewid, languageid)
						VALUES (:keyword_title, :keyword,:keyword_description,:additionalmeta, :viewid, :languageid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('viewid', $id);
				$stmt->bindValue('keyword_title', $Data['keyword_title'][$key]);
				$stmt->bindValue('keyword', $Data['keyword'][$key]);
				$stmt->bindValue('keyword_description', $Data['keyword_description'][$key]);
				$stmt->bindValue('additionalmeta', $Data['additionalmeta'][$key]);
				$stmt->bindValue('languageid', $key);
				
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_VIEW_TRANSLATION_EDIT'), 4, $e->getMessage());
				}
			}
		}
		return true;
	}

	public function updateView ($Data, $id)
	{
		$sql = 'UPDATE view SET
					name=:name,
					namespace=:namespace,
					storeid=:storeid,
					pageschemeid=:pageschemeid,
					currencyid=:currencyid,
					contactid=:contactid,
					periodid=:periodid,
					orderstatusgroupsid=:orderstatusgroupsid,
					taxes=:taxes,
					showtax = :showtax,
					defaultvatid = :defaultvatid,
					googleappstag = :googleappstag,
					googleappsfile = :googleappsfile,
					offline = :offline,
					enablegiftwrap = :enablegiftwrap,
					giftwrapproduct = :giftwrapproduct,
					offlinetext = :offlinetext,
					forcelogin = :forcelogin,
					confirmregistration = :confirmregistration,
					enableregistration = :enableregistration,
					minimumordervalue = :minimumordervalue,
					cartredirect = :cartredirect,
                    invoicenumerationkind = :invoicenumerationkind,
					invoicedefaultpaymentdue = :invoicedefaultpaymentdue,
					photoid = :photoid,
					favicon = :favicon,
					watermark = :watermark,
					uploaderenabled = :uploaderenabled,
					uploadmaxfilesize = :uploadmaxfilesize,
					uploadchunksize = :uploadchunksize,
					uploadextensions = :uploadextensions,
					wwwredirection = :wwwredirection
				WHERE idview =:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $Data['name']);
		$stmt->bindValue('namespace', $Data['namespace']);
		
		
		if (isset($Data['googleappstag']) && preg_match('/^<meta name="google-site-verification" content="[a-zA-Z0-9-]+"\s?\/>$/i', $Data['googleappstag'])){
			$stmt->bindValue('googleappstag', $Data['googleappstag']);
		}
		else{
			$stmt->bindValue('googleappstag', '');
		}
		
		$oldData = $this->getView(Helper::getViewId());
		$file = basename($oldData['googleappsfile']['file']);
		
		if ($file != $Data['googleappsfile']['file']){
			if (! empty($file) && preg_match('/^google[a-f0-9]+\.html$/', $file) && is_file(ROOTPATH . DS . $file)){
				unlink(ROOTPATH . DS . $file);
			}
			
			if (! empty($Data['googleappsfile']['file']) && preg_match('/^google[a-f0-9]+\.html$/', $Data['googleappsfile']['file'])){
				copy(ROOTPATH . DS . 'upload' . DS . $Data['googleappsfile']['file'], ROOTPATH . DS . $Data['googleappsfile']['file']);
			}
		}
		$stmt->bindValue('googleappsfile', $Data['googleappsfile']['file']);
		
		$stmt->bindValue('storeid', $Data['store']);
		$stmt->bindValue('pageschemeid', $Data['pageschemeid']);
		$stmt->bindValue('currencyid', $Data['currencyid']);
		$stmt->bindValue('contactid', ($Data['contactid'] == 0) ? NULL : $Data['contactid']);
		$stmt->bindValue('periodid', $Data['periodid']);
		$stmt->bindValue('orderstatusgroupsid', $Data['orderstatusgroupsid']);
		$stmt->bindValue('taxes', $Data['taxes']);
		$stmt->bindValue('showtax', $Data['showtax']);
		$stmt->bindValue('defaultvatid', $Data['defaultvatid']);
		$stmt->bindValue('enablegiftwrap', ($Data['enablegiftwrap'] == 1) ? 1 : 0);
		$stmt->bindValue('giftwrapproduct', ((int) $Data['giftwrapproduct'] > 0) ? $Data['giftwrapproduct'] : NULL);
		$stmt->bindValue('id', $id);
		
		$stmt->bindValue('wwwredirection', (int) $Data['wwwredirection']);
		
		if (isset($Data['offline']) && $Data['offline'] == 1){
			$stmt->bindValue('offline', 1);
		}
		else{
			$stmt->bindValue('offline', 0);
		}
		$stmt->bindValue('offlinetext', $Data['offlinetext']);
		if (isset($Data['forcelogin']) && $Data['forcelogin'] == 1){
			$stmt->bindValue('forcelogin', 1);
		}
		else{
			$stmt->bindValue('forcelogin', 0);
		}
		if (isset($Data['confirmregistration']) && $Data['confirmregistration'] == 1){
			$stmt->bindValue('confirmregistration', 1);
		}
		else{
			$stmt->bindValue('confirmregistration', 0);
		}
		if (isset($Data['enableregistration']) && $Data['enableregistration'] == 1){
			$stmt->bindValue('enableregistration', 1);
		}
		else{
			$stmt->bindValue('enableregistration', 0);
		}
		$stmt->bindValue('minimumordervalue', $Data['minimumordervalue']);
		if (isset($Data['cartredirect']) && ! is_null($Data['cartredirect'])){
			$stmt->bindValue('cartredirect', 1);
		}
		else{
			$stmt->bindValue('cartredirect', 0);
		}
		$stmt->bindValue('invoicenumerationkind', $Data['invoicenumerationkind']);
		$stmt->bindValue('invoicedefaultpaymentdue', $Data['invoicedefaultpaymentdue']);
		if (isset($Data['photo']['file'])){
			$stmt->bindValue('photoid', $Data['photo']['file']);
		}
		else{
			$stmt->bindValue('photoid', '');
		}
		if (isset($Data['favicon']['file'])){
			$stmt->bindValue('favicon', $Data['favicon']['file']);
		}
		else{
			$stmt->bindValue('favicon', '');
		}
		if (isset($Data['watermark']['file'])){
			$stmt->bindValue('watermark', $Data['watermark']['file']);
		}
		else{
			$stmt->bindValue('watermark', '');
		}
		if (isset($Data['uploaderenabled']) && ! is_null($Data['uploaderenabled'])){
			$stmt->bindValue('uploaderenabled', 1);
			$stmt->bindValue('uploadmaxfilesize', $Data['uploadmaxfilesize']);
			$stmt->bindValue('uploadchunksize', $Data['uploadchunksize']);
			$stmt->bindValue('uploadextensions', implode(',', $Data['uploadextensions']));
		}
		else{
			$stmt->bindValue('uploaderenabled', 0);
			$stmt->bindValue('uploadmaxfilesize', 0);
			$stmt->bindValue('uploadchunksize', 0);
			$stmt->bindValue('uploadextensions', '');
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_SHOP_EDIT'), 13, $e->getMessage());
		}
		$this->updateViewCategory($Data['category'], $id);
		$this->flushCache();
		return true;
	}

	public function updateAssignToGroupPerView ($array, $id)
	{
		DbTracker::deleteRows('assigntogroup', 'viewid', $id);
		
		if (! empty($array) && is_array($array)){
			foreach ($array as $key => $value){
				$sql = 'INSERT INTO assigntogroup (clientgroupid, `from`, `to`, viewid)
							VALUES (:clientgroupid, :from, :to, :viewid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('viewid', $id);
				if (isset($value['min'])){
					$stmt->bindValue('from', $value['min']);
				}
				else{
					$stmt->bindValue('from', 0.00);
				}
				if (isset($value['max'])){
					$stmt->bindValue('to', $value['max']);
				}
				else{
					$stmt->bindValue('to', 0.00);
				}
				$stmt->bindValue('clientgroupid', $value['price']);
				
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new Exception($e->getMessage());
				}
			}
		}
		return $array;
	}

	public function viewCategoryIds ($id)
	{
		$Data = $this->viewCategory($id);
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[] = $key['id'];
		}
		return $tmp;
	}

	public function viewCategory ($id)
	{
		$sql = 'SELECT DISTINCT(categoryid) AS id
					FROM viewcategory
					WHERE viewid=:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function updateViewCategory ($Data, $id)
	{
		DbTracker::deleteRows('viewcategory', 'viewid', $id);
		
		if (! empty($Data)){
			foreach ($Data as $category){
				$sql = 'INSERT INTO viewcategory (viewid, categoryid)
							VALUES (:viewid, :categoryid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('viewid', $id);
				$stmt->bindValue('categoryid', $category);
				
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_PRODUCT_CATEGORY_UPDATE'), 112, $e->getMessage());
				}
			}
		}
	}

	public function changeActiveView ($view)
	{
		$objResponse = new xajaxResponse();
		Helper::setViewId($view);
		$sql = "SELECT storeid
					FROM view
					WHERE idview=:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $view);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		
		if ($rs){
			Helper::setStoreId($rs['storeid']);
		}
		else{
			Helper::setStoreId(0);
		}
		$objResponse->script('window.location.reload(true)');
		return $objResponse;
	}

	public function getViews ()
	{
		$globaluser = App::getContainer()->get('session')->getActiveUserIsGlobal();
		
		$Data = Array();
		
		$Data[] = Array(
			'id' => 0,
			'name' => $this->trans('TXT_GLOBAL_LAYER'),
			'active' => (Helper::getViewId() == 0) ? true : false
		);
		
		if ($globaluser == 1){
			
			$sql = 'SELECT
						V.idview AS id,
						V.name,
						V.storeid
					FROM view V
					ORDER BY
					V.name ASC
				';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				$Data[] = Array(
					'id' => $rs['id'],
					'name' => $rs['name'],
					'active' => (Helper::getViewId() == $rs['id']) ? true : false
				);
			}
		}
		else{
			
			$sql = 'SELECT
						UGV.viewid,
						V.name
					FROM usergroupview UGV
					LEFT JOIN view V ON UGV.viewid = V.idview
					WHERE UGV.userid = :userid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('userid', App::getContainer()->get('session')->getActiveUserid());
			$stmt->execute();
			
			while ($rs = $stmt->fetch()){
				
				$Data[] = Array(
					'id' => $rs['viewid'],
					'name' => $rs['name'],
					'active' => (Helper::getViewId() == $rs['viewid']) ? true : false
				);
			}
		}
		
		return $Data;
	}

	public function getCategoryAll ($id)
	{
		$sql = 'SELECT
					C.idcategory AS id,
					C.categoryid AS parent,
					CT.name as categoryname,
					C.distinction
					FROM category C
					LEFT JOIN categorytranslation CT ON CT.categoryid = C.idcategory AND CT.languageid = :languageid';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'categoryname' => $rs['categoryname'],
				'distinction' => $rs['distinction'],
				'parent' => $rs['parent'],
				'allow' => $this->getCategoryViewAllow($rs['id'], $id)
			);
		}
		return $Data;
	}

	public function getCategoryViewAllow ($categoryid, $parentid)
	{
		$sql = 'SELECT count(VC.idviewcategory) as allow
					FROM viewcategory VC,
					productcategory PC
					WHERE VC.categoryid = :categoryid AND VC.viewid = :viewid AND PC.productid = :productid AND VC.categoryid = PC.categoryid';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('categoryid', $categoryid);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->bindValue('productid', $parentid);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			return $rs['allow'];
		}
	}

	public function getViewsByStoreId ($id)
	{
		$sql = "SELECT idview AS id,name
					FROM view WHERE storeid = :storeid";
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('storeid', $id);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'name' => $rs['name']
			);
		}
		return $Data;
	}

	public function getViewAllSelect ()
	{
		$sql = 'SELECT idview AS id , name
					FROM view';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$id = $rs['id'];
			$Data[$id] = $rs['name'];
		}
		return $Data;
	}

	public function getViewAll ()
	{
		$sql = 'SELECT idview AS id , name
					FROM view';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'id' => $rs['id'],
				'name' => $rs['name']
			);
		}
		return $Data;
	}

	public function flushCache ()
	{
		App::getContainer()->get('session')->setActiveViewIds(NULL);
		App::getContainer()->get('cache')->delete('views');
		App::getContainer()->get('cache')->delete('categories');
	}

	public function getUrlAddressesForInstance ()
	{
		$this->instance = new Instance();
		$Data = Array();
		$result = $this->instance->getDomainsForInstance();
		foreach ($result['result'] as $domain){
			$Data[$domain['name']] = $domain['name'];
		}
		return $Data;
	}

	public function getChildCategories ($parentCategory = 0)
	{
		$Data = Array();
		
		$sql = 'SELECT
					C.idcategory AS id,
					C.distinction,
					C.categoryid AS parent,
					CT.name AS categoryname,
					(SELECT COUNT( idcategory )
						FROM category
						WHERE categoryid = C.idcategory
					) AS haschildren
				FROM category C
				LEFT JOIN categorytranslation CT ON CT.categoryid = C.idcategory AND CT.languageid = :languageid
				WHERE C.categoryid ' . (((int) $parentCategory == 0) ? 'IS NULL' : ' = ' . $parentCategory) . '
				GROUP BY C.idcategory
				ORDER BY C.distinction ASC
		';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['id']] = Array(
				'id' => $rs['id'],
				'name' => $rs['categoryname'],
				'hasChildren' => ($rs['haschildren'] > 0) ? true : false,
				'parent' => ($rs['parent'] == 0) ? NULL : $rs['parent'],
				'weight' => $rs['distinction']
			);
		}
		return $Data;
	}

	public function getDomainsAsExchangeOptions ()
	{
		$result = $this->instance->getDomainsForInstance();
		$tmp = Array();
		foreach ($result['result'] as $domain){
			$tmp[] = Array(
				'sValue' => $domain['name'],
				'sLabel' => $domain['name']
			);
		}
		
		return $tmp;
	}

	public function addDomain ($request)
	{
		$this->instance = new Instance();
		
		$check = $this->instance->domainCheck($request['name']);
		if (isset($check['result']['valid']) && $check['result']['valid'] == 1){
			$result = $this->instance->addDomain($request['name']);
			if ($result['result']['success'] == 1){
				return Array(
					'id' => $request['name'],
					'options' => $this->getDomainsAsExchangeOptions()
				);
			}
			else{
				return Array(
					'error' => 'Nie udało się dodać domeny. Skontaktuj się z obsługą techniczną.'
				);
			}
		}
		else{
			return Array(
				'error' => 'Ten adres jest niepoprawny lub zajęty.'
			);
		}
	}
}