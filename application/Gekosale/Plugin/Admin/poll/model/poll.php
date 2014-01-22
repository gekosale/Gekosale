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
 * $Id: poll.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale\Plugin;

class PollModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('poll', Array(
			'idpoll' => Array(
				'source' => 'P.idpoll'
			),
			'questions' => Array(
				'source' => 'PT.name',
				'prepareForAutosuggest' => true
			),
			'votes' => Array(
				'source' => 'P.idpoll',
				'processFunction' => Array(
					$this,
					'getPollVotes'
				)
			),
			'publish' => Array(
				'source' => 'publish'
			)
		));
		
		$datagrid->setFrom('
			poll P
			LEFT JOIN answervolunteered AV ON P.idpoll = AV.pollid
			LEFT JOIN pollanswers PA ON AV.pollanswersid = PA.idpollanswers
			LEFT JOIN polltranslation PT ON PT.pollid = idpoll
			LEFT JOIN pollview PV ON PV.pollid = idpoll
		');
		
		$datagrid->setGroupBy('
			P.idpoll
		');
		
		$datagrid->setAdditionalWhere('
			PT.languageid=:languageid
		');
	}

	public function getPollVotes ($id)
	{
		return $this->getPollAnswersData($id);
	}
	
	public function getQuestionsForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('questions', $request, $processFunction);
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getPollForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeletePoll ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deletePoll'
		), $this->getName());
	}

	public function deletePoll ($id)
	{
		DbTracker::deleteRows('poll', 'idpoll', $id);
	}

	public function doAJAXEnablePoll ($datagridId, $id)
	{
		try{
			$this->enablePoll($id);
			return $this->getDatagrid()->refresh($datagridId);
		}
		catch (Exception $e){
			$objResponse = new xajaxResponse();
			$objResponse->script("GF_Alert('{$this->trans('ERR_UNABLE_TO_ENABLE_STATICBLOCKS')}', '{$e->getMessage()}');");
			return $objResponse;
		}
	}

	public function doAJAXDisablePoll ($datagridId, $id)
	{
		try{
			$this->disablePoll($id);
			return $this->getDatagrid()->refresh($datagridId);
		}
		catch (Exception $e){
			$objResponse = new xajaxResponse();
			$objResponse->script("GF_Alert('{$this->trans('ERR_UNABLE_TO_DISABLE_STATICBLOCKS')}', '{$e->getMessage()}');");
			return $objResponse;
		}
	}

	public function disablePoll ($id)
	{
		$sql = 'UPDATE poll SET publish = 0 WHERE idpoll = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function enablePoll ($id)
	{
		$sql = 'UPDATE poll SET publish = 1 WHERE idpoll = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function getPollView ($id)
	{
		$sql = "SELECT idpoll as id, publish FROM poll WHERE idpoll=:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'id' => $rs['id'],
				'publish' => $rs['publish'],
				'answers' => $this->getAnswersPoll($id),
				'view' => $this->getPollViews($id),
				'language' => $this->getPollTranslation($id)
			);
		}
		else{
			throw new CoreException($this->trans('ERR_POLL_NO_EXIST'));
		}
		return $Data;
	}

	public function getPollTranslation ($id)
	{
		$sql = "SELECT name as questions, languageid
					FROM polltranslation
					WHERE pollid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['languageid']] = Array(
				'questions' => $rs['questions']
			);
		}
		return $Data;
	}

	public function getPollViews ($id)
	{
		$sql = "SELECT viewid
					FROM pollview
					WHERE pollid =:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['viewid'];
		}
		return $Data;
	}

	public function getAnswersPoll ($id)
	{
		$sql = "SELECT name, languageid
					FROM pollanswers
					WHERE pollid=:id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$lnaguageId = $rs['languageid'];
			$Data['name_' . $lnaguageId][] = $rs['name'];
		}
		return $Data;
	}

	public function editPoll ($Data, $id)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->updatePoll($Data, $id);
			$this->editPollTranslation($Data, $id);
			$this->editPollAnswers($Data, $id);
			$this->editPollView($Data['view'], $id);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_POLL_EDIT'), 125, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function editPollTranslation ($Data, $id)
	{
		DbTracker::deleteRows('polltranslation', 'pollid', $id);
		
		foreach ($Data['questions'] as $key => $value){
			$sql = 'INSERT INTO polltranslation (name, languageid, pollid)
						VALUES (:name, :languageid, :pollid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('name', $value);
			$stmt->bindValue('languageid', $key);
			$stmt->bindValue('pollid', $id);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_POLL_TRANSLATION_ADD'), 4, $e->getMessage());
			}
		}
		return $Data;
	}

	public function editPollView ($array, $id)
	{
		DbTracker::deleteRows('pollview', 'pollid', $id);
		
		if (! empty($array)){
			foreach ($array as $value){
				$sql = 'INSERT INTO pollview (viewid, pollid)
							VALUES (:viewid, :pollid)';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('pollid', $id);
				$stmt->bindValue('viewid', $value);
				
				try{
					$stmt->execute();
				}
				catch (Exception $e){
					throw new CoreException($this->trans('ERR_POLL_VIEW_ADD'), 4, $e->getMessage());
				}
			}
		}
	}

	public function editPollAnswers ($Data, $id)
	{
		DbTracker::deleteRows('answervolunteered', 'pollid', $id);
		
		DbTracker::deleteRows('pollanswers', 'pollid', $id);
		
		foreach ($Data as $key => $value){
			if (is_array($value)){
				$check = substr($key, 0, 5);
				if ($check == 'name_'){
					$languageid = substr($key, 5, 5);
					foreach ($Data[$key] as $newkey => $trans){
						$sql = 'INSERT INTO pollanswers (name, pollid, languageid)
									VALUES (:name, :pollid, :languageid)';
						$stmt = Db::getInstance()->prepare($sql);
						$stmt->bindValue('name', $trans);
						$stmt->bindValue('pollid', $id);
						$stmt->bindValue('languageid', $languageid);
						
						try{
							$stmt->execute();
						}
						catch (Exception $e){
							throw new CoreException($this->trans('ERR_POLL_ANSWERS_ADD'), 1225, $e->getMessage());
						}
					}
				}
			}
		}
		return $Data;
	}

	public function updatePoll ($Data, $id)
	{
		$sql = 'UPDATE poll SET publish=:publish WHERE idpoll =:id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('publish', $Data['publish']);
		
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_POLL_EDIT'), 13, $e->getMessage());
		}
		return true;
	}

	public function addNewPoll ($Data)
	{
		Db::getInstance()->beginTransaction();
		try{
			$newPollId = $this->addPoll($Data);
			$this->addPollTranslation($Data, $newPollId);
			$this->addPollValue($Data, $newPollId);
			if (! empty($Data['view']) && is_array($Data['view'])){
				$this->addPollView($Data['view'], $newPollId);
			}
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_POLL_ADD'), 125, $e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	protected function addPollView ($array, $id)
	{
		foreach ($array as $value){
			$sql = 'INSERT INTO pollview (viewid, pollid)
						VALUES (:viewid, :pollid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('pollid', $id);
			$stmt->bindValue('viewid', $value);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_POLL_VIEW_ADD'), 125, $e->getMessage());
			}
		}
	}

	protected function addPollValue ($Data, $newPollId)
	{
		foreach ($Data as $key => $value){
			if (is_array($value)){
				$check = substr($key, 0, 5);
				if ($check == 'name_'){
					$languageid = substr($key, 5, 5);
					foreach ($Data[$key] as $newkey => $trans){
						$sql = 'INSERT INTO pollanswers (name, pollid, languageid)
									VALUES (:name, :pollid, :languageid)';
						$stmt = Db::getInstance()->prepare($sql);
						$stmt->bindValue('name', $trans);
						$stmt->bindValue('pollid', $newPollId);
						$stmt->bindValue('languageid', $languageid);
						
						try{
							$stmt->execute();
						}
						catch (Exception $e){
							throw new CoreException($this->trans('ERR_POLL_ANSWERS_ADD'), 1225, $e->getMessage());
						}
					}
				}
			}
		}
		return $Data;
	}

	public function addPoll ($Data)
	{
		$sql = 'INSERT INTO poll (publish)
					VALUES (:publish)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('publish', $Data['publish']);
		
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_POLL_ADD'), 4, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function addPollTranslation ($Data, $pollid)
	{
		foreach ($Data['questions'] as $key => $value){
			$sql = 'INSERT INTO polltranslation (name, languageid, pollid)
						VALUES (:name, :languageid, :pollid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('name', $value);
			$stmt->bindValue('languageid', $key);
			$stmt->bindValue('pollid', $pollid);
			
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_POLL_TRANSLATION_ADD'), 4, $e->getMessage());
			}
		}
		return Db::getInstance()->lastInsertId();
	}

	public function getPollAnswers ($id)
	{
		$sql = "SELECT PA.name, COUNT(DISTINCT AV.pollanswersid) AS votes, PA.idpollanswers as id
					FROM pollanswers PA
					LEFT JOIN answervolunteered AV ON AV.pollid = PA.pollid
					WHERE PA.pollid = :id AND PA.languageid = :languageid
					GROUP BY PA.pollid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['name'] . " ({$rs['votes']})";
		}
		return implode('<br />', $Data);
	}
	
	public function getPollAnswersData ($id)
	{
		$sql = "SELECT 
					name, 
					votes, 
					idpollanswers as id
				FROM pollanswers
				WHERE pollid = :id AND languageid = :languageid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'name' => $rs['name'],
				'qty' => $this->checkAnswersQty($rs['id'])
			);
		}
		$content = '';
		foreach ($Data as $poll){
			$content .= $poll['name'] . ' - ' . $poll['qty'] . '<br />';
		}
		return $content;
	}
	
	public function checkAnswersQty ($id)
	{
		$sql = "SELECT
					count(pollanswersid) as qty
				FROM answervolunteered
				WHERE pollanswersid = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		return $rs['qty'];
	}
	
}