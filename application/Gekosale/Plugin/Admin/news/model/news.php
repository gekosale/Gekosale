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
 * $Id: news.php 619 2011-12-19 21:09:00Z gekosale $ 
 */
namespace Gekosale\Plugin;

class NewsModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('news', Array(
			'idnews' => Array(
				'source' => 'N.idnews'
			),
			'topic' => Array(
				'source' => 'NT.topic'
			),
			'summary' => Array(
				'source' => 'NT.summary',
				'processFunction' => Array(
					$this,
					'processNewsSummary'
				)
			),
			'publish' => Array(
				'source' => 'N.publish'
			),
			'adddate' => Array(
				'source' => 'N.adddate'
			),
			'startdate' => Array(
				'source' => 'N.startdate'
			),
			'enddate' => Array(
				'source' => 'N.enddate'
			)
		));
		
		$datagrid->setFrom('
			news N
			LEFT JOIN newsview NV ON NV.newsid = N.idnews
			LEFT JOIN newstranslation NT ON N.idnews = NT.newsid AND NT.languageid = :languageid
			LEFT JOIN language L ON L.idlanguage=NT.languageid
		');
		
		$datagrid->setGroupBy('
			NT.newsid
		');
		
		$datagrid->setAdditionalWhere('
			NV.viewid IN (' . Helper::getViewIdsAsString() . ')
		');
	}

	public function processNewsSummary ($summary)
	{
		$summary = str_replace(Array(
			"\n",
			"\r",
			"\r\n",
			"\n\r",
			"\t"
		), '', $summary);
		return $summary;
	}

	public function doAJAXEnableNews ($datagridId, $id)
	{
		try{
			$this->enableNews($id);
			$this->flushCache();
			return $this->getDatagrid()->refresh($datagridId);
		}
		catch (Exception $e){
			$objResponse = new xajaxResponse();
			$objResponse->script("GF_Alert('{$this->trans('ERR_UNABLE_TO_ENABLE_STATICBLOCKS')}', '{$e->getMessage()}');");
			return $objResponse;
		}
	}

	public function doAJAXDisableNews ($datagridId, $id)
	{
		try{
			$this->disableNews($id);
			$this->flushCache();
			return $this->getDatagrid()->refresh($datagridId);
		}
		catch (Exception $e){
			$objResponse = new xajaxResponse();
			$objResponse->script("GF_Alert('{$this->trans('ERR_UNABLE_TO_DISABLE_STATICBLOCKS')}', '{$e->getMessage()}');");
			return $objResponse;
		}
	}

	public function disableNews ($id)
	{
		$sql = 'UPDATE news SET publish = 0	WHERE idnews = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function enableNews ($id)
	{
		$sql = 'UPDATE news SET publish = 1	WHERE idnews = :id';
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

	public function getNewsForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteNews ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($datagrid, $id, Array(
			$this,
			'deleteNews'
		), $this->getName());
	}

	public function deleteNews ($id)
	{
		DbTracker::deleteRows('news', 'idnews', $id);
		$this->flushCache();
	}

	public function getNewsView ($id)
	{
		$sql = "SELECT 
					N.idnews, 
					N.publish,
					N.startdate,
					N.enddate,
					N.featured,
					NP.photoid AS mainphotoid
				FROM news N
				LEFT JOIN newsphoto NP ON N.idnews = NP.newsid AND NP.mainphoto = 1
				WHERE N.idnews =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data = Array(
				'mainphotoid' => $rs['mainphotoid'],
				'publish' => $rs['publish'],
				'startdate' => $rs['startdate'],
				'enddate' => $rs['enddate'],
				'featured' => $rs['featured'],
				'language' => $this->getNewsTranslation($id),
				'id' => $rs['idnews'],
				'photo' => $this->newsPhotoIds($rs['idnews']),
				'view' => $this->getStoreViews($id)
			);
		}
		return $Data;
	}

	public function newsPhoto ($id)
	{
		$sql = 'SELECT 
					photoid AS id FROM newsphoto WHERE newsid=:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function newsPhotoIds ($id)
	{
		$Data = $this->newsPhoto($id);
		$tmp = Array();
		foreach ($Data as $key){
			$tmp[] = $key['id'];
		}
		return $tmp;
	}

	public function newsPhotoUpdate ($Data, $id)
	{
		if (isset($Data['photo']['unmodified']) && $Data['photo']['unmodified']){
			return;
		}
		
		DbTracker::deleteRows('newsphoto', 'newsid', $id);
		
		if (isset($Data['photo']['main'])){
			$mainphoto = $Data['photo']['main'];
			foreach ($Data['photo'] as $key => $photo){
				if (! is_array($photo) && is_int($key) && ($photo > 0)){
					$sql = 'INSERT INTO newsphoto (newsid, mainphoto, photoid)
							VALUES (:newsid, :mainphoto, :photoid)';
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('newsid', $id);
					$stmt->bindValue('mainphoto', ($photo == $mainphoto) ? 1 : 0);
					$stmt->bindValue('photoid', $photo);
					try{
						$stmt->execute();
					}
					catch (Exception $e){
						throw new CoreException($this->trans('ERR_NEWS_PHOTO_UPDATE'), 112, $e->getMessage());
					}
				}
			}
		}
	}

	public function getStoreViews ($id)
	{
		$sql = "SELECT viewid
				FROM newsview
				WHERE newsid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['viewid'];
		}
		return $Data;
	}

	public function getNewsTranslation ($id)
	{
		$sql = "SELECT 
					topic, 
					summary,
					content, 
					seo, 
					keyword_title, 
					keyword,
					keyword_description, 
					languageid
				FROM newstranslation
				WHERE newsid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['languageid']] = Array(
				'topic' => $rs['topic'],
				'seo' => $rs['seo'],
				'summary' => $rs['summary'],
				'content' => $rs['content'],
				'keyword_title' => $rs['keyword_title'],
				'keyword' => $rs['keyword'],
				'keyword_description' => $rs['keyword_description']
			);
		}
		return $Data;
	}

	public function addNewNews ($Data)
	{
		Db::getInstance()->beginTransaction();
		try{
			if ($Data['startdate'] == '0000-00-00 00:00:00'){
				$Data['startdate'] = '';
			}
			if ($Data['enddate'] == '0000-00-00 00:00:00'){
				$Data['enddate'] = '';
			}
			$newNewsId = $this->addNews($Data['publish'], $Data['featured'], $Data['startdate'], $Data['enddate']);
			if (is_array($Data['view']) && ! empty($Data['view'])){
				$this->addNewsView($Data['view'], $newNewsId);
			}
			$this->addNewsTranslation($Data, $newNewsId);
			$this->addPhotoNews($Data['photo'], $newNewsId);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CONTACT_ADD'), 11, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		$this->flushCache();
		return true;
	}

	public function addPhotoNews ($array, $newsId)
	{
		if ($array['unmodified'] == 0 && isset($array['main'])){
			$mainphoto = $array['main'];
			foreach ($array as $key => $photo){
				if (! is_array($photo) && is_int($key)){
					$sql = 'INSERT INTO newsphoto (newsid, mainphoto, photoid)
							VALUES (:newsid, :mainphoto, :photoid)';
					$stmt = Db::getInstance()->prepare($sql);
					$stmt->bindValue('newsid', $newsId);
					$stmt->bindValue('mainphoto', ($photo == $mainphoto) ? 1 : 0);
					$stmt->bindValue('photoid', $photo);
					try{
						$stmt->execute();
					}
					catch (Exception $e){
						throw new CoreException($this->trans('ERR_news_PHOTO_ADD'), 112, $e->getMessage());
					}
				}
			}
		}
	}

	public function addNews ($publish, $featured, $startdate, $enddate)
	{
		$sql = 'INSERT INTO news SET
					publish = :publish, 
					featured = :featured,
					startdate = :startdate,
					enddate = :enddate';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('publish', $publish);
		$stmt->bindValue('featured', $featured);
		if ($startdate == '' || $startdate == '0000-00-00 00:00:00'){
			$stmt->bindValue('startdate', NULL);
		}
		else{
			$stmt->bindValue('startdate', $startdate);
		}
		if ($enddate == '' || $enddate == '0000-00-00 00:00:00'){
			$stmt->bindValue('enddate', NULL);
		}
		else{
			$stmt->bindValue('enddate', $enddate);
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_NEWS_ADD'), 4, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function addNewsTranslation ($Data, $id)
	{
		foreach ($Data['topic'] as $key => $val){
			$sql = 'INSERT INTO newstranslation SET
						newsid = :newsid,
						topic = :topic, 
						summary = :summary, 
						content = :content, 
						seo = :seo,
						keyword_title = :keyword_title,
						keyword = :keyword,
						keyword_description = :keyword_description,
						languageid = :languageid
			';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('newsid', $id);
			$stmt->bindValue('topic', $Data['topic'][$key]);
			$stmt->bindValue('seo', $Data['seo'][$key]);
			$stmt->bindValue('summary', $Data['summary'][$key]);
			$stmt->bindValue('content', $Data['content'][$key]);
			$stmt->bindValue('keyword_title', $Data['keyword_title'][$key]);
			$stmt->bindValue('keyword', $Data['keyword'][$key]);
			$stmt->bindValue('keyword_description', $Data['keyword_description'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_NEWS_TRANSLATION_ADD'), 4, $e->getMessage());
			}
		}
	}

	public function addNewsView ($Data, $id)
	{
		foreach ($Data as $value){
			$sql = 'INSERT INTO newsview SET
					newsid = :newsid,
					viewid = :viewid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('newsid', $id);
			$stmt->bindValue('viewid', $value);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_NEWS_VIEW_ADD'), 4, $e->getMessage());
			}
		}
	}

	public function editNews ($Data, $id)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->updateNews($Data['publish'], $Data['featured'], $id, $Data['startdate'], $Data['enddate']);
			$this->updateNewsTranslation($Data, $id);
			$this->updateNewsView($Data['view'], $id);
			$this->newsPhotoUpdate($Data, $id);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CONTACT_EDIT'), 125, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		$this->flushCache();
		return true;
	}

	public function updateNews ($publish, $featured, $id, $startdate, $enddate)
	{
		$sql = 'UPDATE news SET 
					publish=:publish,
					featured=:featured,
					startdate = :startdate,
					enddate = :enddate
				WHERE idnews =:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('publish', $publish);
		$stmt->bindValue('featured', $featured);
		if ($startdate == '' || $startdate == '0000-00-00 00:00:00'){
			$stmt->bindValue('startdate', NULL);
		}
		else{
			$stmt->bindValue('startdate', $startdate);
		}
		if ($enddate == '' || $enddate == '0000-00-00 00:00:00'){
			$stmt->bindValue('enddate', NULL);
		}
		else{
			$stmt->bindValue('enddate', $enddate);
		}
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_NEWS_EDIT'), 13, $e->getMessage());
		}
	}

	public function updateNewsView ($Data, $id)
	{
		DbTracker::deleteRows('newsview', 'newsid', $id);
		
		if (! empty($Data)){
			foreach ($Data as $value){
				$sql = 'INSERT INTO newsview SET
						newsid = :newsid,
						viewid = :viewid';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('newsid', $id);
				$stmt->bindValue('viewid', $value);
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_NEWS_VIEW_ADD'), 4, $e->getMessage());
				}
			}
		}
	}

	public function updateNewsTranslation ($Data, $id)
	{
		DbTracker::deleteRows('newstranslation', 'newsid', $id);
		
		foreach ($Data['topic'] as $key => $val){
			$sql = 'INSERT INTO newstranslation SET
					newsid = :newsid,
					topic = :topic, 
					summary = :summary, 
					content = :content, 
					seo = :seo,
					keyword_title = :keyword_title,
					keyword = :keyword,
					keyword_description = :keyword_description,
					languageid = :languageid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('newsid', $id);
			$stmt->bindValue('topic', $Data['topic'][$key]);
			$stmt->bindValue('summary', $Data['summary'][$key]);
			$stmt->bindValue('content', $Data['content'][$key]);
			$stmt->bindValue('seo', $Data['seo'][$key]);
			$stmt->bindValue('keyword_title', $Data['keyword_title'][$key]);
			$stmt->bindValue('keyword', $Data['keyword'][$key]);
			$stmt->bindValue('keyword_description', $Data['keyword_description'][$key]);
			$stmt->bindValue('languageid', $key);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_NEWS_TRANSLATION_EDIT'), 4, $e->getMessage());
			}
		}
	}

	public function flushCache ()
	{
		App::getContainer()->get('cache')->delete('news');
	}
}