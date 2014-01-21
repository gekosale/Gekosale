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
 * $Id: pollbox.php 438 2011-08-27 09:29:36Z gekosale $
 */

namespace Gekosale;
use xajaxResponse;

class PollBoxModel extends Component\Model
{

	public function checkAnswers ($idpoll)
	{
		$sql = "SELECT idpollanswers, name
					FROM pollanswers
					WHERE pollid = :idpoll AND languageid = :languageid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->bindValue('idpoll', $idpoll);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'idpollanswers' => $rs['idpollanswers'],
				'name' => $rs['name'],
				'qty' => $this->checkAnswersQty($rs['idpollanswers'])
			);
		}
		
		return $Data;
	}

	public function checkAnswersQty ($id)
	{
		$sql = "SELECT 
					count(pollanswersid) as qty, 
					clientid
				FROM answervolunteered
				WHERE pollanswersid = :id
				GROUP BY pollid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array(
			'qty' => 0,
			'clientid' => null
		);
		if ($rs){
			$Data = Array(
				'qty' => $rs['qty'],
				'clientid' => $rs['clientid']
			);
		}
		return $Data;
	}

	public function getPoll ()
	{
		$sql = "SELECT idpoll, PT.name as questions, publish
					FROM poll
					LEFT JOIN polltranslation PT ON PT.pollid = idpoll AND languageid=:languageid
					LEFT JOIN pollview PV ON PV.pollid = idpoll
 					WHERE languageid = :languageid AND publish = 1 AND viewid = :viewid
 					ORDER BY RAND()";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$Data = Array(
				'questions' => $rs['questions'],
				'idpoll' => $rs['idpoll'],
				'answers' => $this->getPollAnswers($rs['idpoll'])
			);
		}
		return $Data;
	}

	public function getPollAnswers ($id)
	{
		$sql = "SELECT name, votes, idpollanswers as id
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
				'votes' => $rs['id']
			);
		}
		return $Data;
	}

	public function setAnswersMethodChecked ($vote, $pollid)
	{
		$sql = 'INSERT INTO answervolunteered (viewid, pollanswersid, clientid, pollid)
					VALUES(:viewid, :vote, :clientid, :pollid)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->bindValue('vote', $vote);
		$stmt->bindValue('clientid', App::getContainer()->get('session')->getActiveClientid());
		$stmt->bindValue('pollid', $pollid);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function setAJAXAnswersMethodChecked ($votes, $idpoll)
	{
		$objResponsePollMet = new xajaxResponse();
		try{
			$this->setAnswersMethodChecked($votes, $idpoll);
		}
		catch (Exception $e){
			$objResponsePollMet->alert($this->trans('ERR_WHILE_VOTING'));
			return $objResponsePollMet;
		}
		$answers = $this->checkAnswers($idpoll);
		$results = '';
		$maxQty = 0;
		foreach ($answers as $answer){
			$maxQty = max($maxQty, $answer['qty']['qty']);
		}
		foreach ($answers as $answer){
			$results .= "<p>{$answer['name']}</p><div class=\"progress progress-striped\"><div class=\"bar\" style=\"width: " . ceil(($answer['qty']['qty'] / $maxQty) * 100) . "%;\"></div></div>";
		}
		$objResponsePollMet->assign('poll-' . $idpoll, 'innerHTML', $results);
		return $objResponsePollMet;
	}

}