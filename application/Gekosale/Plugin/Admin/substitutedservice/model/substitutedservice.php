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
 * $Id: substitutedservice.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale\Plugin;
use DateTime;
use Exception;

class SubstitutedserviceModel extends Component\Model\Datagrid
{

	public function __construct ($registry, $modelFile)
	{
		parent::__construct($registry, $modelFile);
		$this->layer = $this->registry->loader->getCurrentLayer();
	}

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('substitutedservice', Array(
			'idsubstitutedservice' => Array(
				'source' => 'S.idsubstitutedservice'
			),
			'name' => Array(
				'source' => 'S.name'
			)
		));
		$datagrid->setFrom('
			substitutedservice S
		');
		
		$datagrid->setAdditionalWhere('
			IF(:viewid IS NULL, 1, S.viewid= :viewid)
		');
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getSubstitutedserviceForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteSubstitutedservice ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteSubstitutedservice'
		), $this->getName());
	}

	public function deleteSubstitutedservice ($id)
	{
		DbTracker::deleteRows('substitutedservice', 'idsubstitutedservice', $id);
	}

	function replace ($input)
	{
		return $this->trans($input[1]);
	}

	public function getPeriodsAllToSelect ()
	{
		$sql = "SELECT 
					P.idperiod, 
					P.name 
				FROM period P
				ORDER BY P.timeinterval";
		$stmt = Db::getInstance()->prepare($sql);
		$Data = Array();
		try{
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				$idPeriod = $rs['idperiod'];
				$Data[$idPeriod] = $rs['name'];
			}
		}
		catch (Exception $e){
			throw new CoreException($e->getMessage());
		}
		return $Data;
	}

	public function addSubstitutedService ($submittedData)
	{
		Db::getInstance()->beginTransaction();
		try{
			$new = $this->insertSubstitutedService($submittedData);
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_INSERT_SUBSTITUDED_SERVICE'), 112, $e->getMessage());
		}
		Db::getInstance()->commit();
	}

	public function insertSubstitutedService ($submittedData)
	{
		$sql = 'INSERT INTO substitutedservice SET
					actionid= :actionid, 
					date= :date, 
					periodid= :periodid, 
					admin= :admin,
					name= :name,
					viewid= :viewid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('actionid', $submittedData['actionid']['value']);
		if ($submittedData['actionid']['value'] == 2 && isset($submittedData['actionid'][2])){
			$stmt->bindValue('date', $submittedData['actionid'][2]);
		}
		else{
			$stmt->bindValue('date', NULL);
		}
		if ($submittedData['actionid']['value'] != 2 && ! empty($submittedData['actionid'][$submittedData['actionid']['value']])){
			$stmt->bindValue('periodid', $submittedData['actionid'][$submittedData['actionid']['value']]);
		}
		else{
			$stmt->bindValue('periodid', NULL);
		}
		if (isset($submittedData['admin']) && $submittedData['admin'] > 0){
			$stmt->bindValue('admin', $submittedData['admin']);
		}
		else{
			$stmt->bindValue('admin', 0);
		}
		$stmt->bindValue('name', $submittedData['name']);
		if (Helper::getViewId() == 0){
			$stmt->bindValue('viewid', NULL);
		}
		else{
			$stmt->bindValue('viewid', Helper::getViewId());
		}
		try{
			$stmt->execute();
			return Db::getInstance()->lastInsertId();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_INSERT_SUBSTITUDED_SERVICE'), 112, $e->getMessage());
		}
	}

	public function getSubstitutedServiceToEdit ($idSubstitutedService)
	{
		$sql = "SELECT 
					S.idsubstitutedservice, 
					S.actionid, 
					S.date, 
					S.periodid, 
					S.admin, 
					S.name
				FROM substitutedservice S
				WHERE idsubstitutedservice = :idsubstitutedservice";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idsubstitutedservice', $idSubstitutedService);
		$Data = Array();
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$Data = Array(
					'idsubstitutedservice' => $rs['idsubstitutedservice'],
					'actionid' => $rs['actionid'],
					'date' => $rs['date'],
					'periodid' => $rs['periodid'],
					'admin' => $rs['admin'],
					'name' => $rs['name']
				);
			}
		}
		catch (Exception $e){
			throw new CoreException($e->getMessage());
		}
		return $Data;
	}

	public function editSubstitutedService ($submittedData, $idSubstitutedService)
	{
		Db::getInstance()->beginTransaction();
		try{
			$this->updateSubstitutedService($submittedData, $idSubstitutedService);
		}
		catch (Exception $e){
			throw new CoreException($e->getMessage());
		}
		
		Db::getInstance()->commit();
		return true;
	}

	public function updateSubstitutedService ($submittedData, $idSubstitutedService)
	{
		$sql = 'UPDATE substitutedservice 
					SET
						transmailid= :transmailid, 
						actionid= :actionid, 
						date= :date, 
						periodid= :periodid, 
						admin= :admin,
						name= :name,
						viewid= :viewid
					WHERE idsubstitutedservice= :idsubstitutedservice';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idsubstitutedservice', $idSubstitutedService);
		$stmt->bindValue('transmailid', $submittedData['transmailid']);
		$stmt->bindValue('actionid', $submittedData['actionid']['value']);
		if ($submittedData['actionid']['value'] == 2){
			$stmt->bindValue('date', $submittedData['actionid'][2]);
		}
		else{
			$stmt->bindValue('date', NULL);
		}
		if ($submittedData['actionid']['value'] != 2 && ! empty($submittedData['actionid'][$submittedData['actionid']['value']])){
			$stmt->bindValue('periodid', $submittedData['actionid'][$submittedData['actionid']['value']]);
		}
		else{
			$stmt->bindValue('periodid', NULL);
		}
		if (isset($submittedData['admin']) && ! empty($submittedData['admin'])){
			$stmt->bindValue('admin', $submittedData['admin']);
		}
		else{
			$stmt->bindValue('admin', 0);
		}
		$stmt->bindValue('name', $submittedData['name']);
		
		if (Helper::getViewId() == 0){
			$stmt->bindValue('viewid', NULL);
		}
		else{
			$stmt->bindValue('viewid', Helper::getViewId());
		}
		try{
			$stmt->execute();
			return true;
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_INSERT_SUBSTITUDED_SERVICE'), 112, $e->getMessage());
		}
	}

	public function getClientsForSubstitutedServicesSend ($substitutedServiceId)
	{
		
		$substitutedServiceInfo = $this->getSubstitutedService($substitutedServiceId);
		$Data = Array();
		if (! empty($substitutedServiceInfo) && isset($substitutedServiceInfo['actionid'])){
			$action = $substitutedServiceInfo['actionid'];
			switch ($action) {
				case 1:
					$sql = "SELECT DISTINCT(C.idclient),
					CD.firstname, CD.surname, CD.email
					FROM client C
					LEFT JOIN clientdata CD ON C.idclient = CD.clientid
					LEFT JOIN `order` O ON C.idclient = O.clientid
					WHERE O.adddate <= :newdate
					AND IF (:viewid IS NOT NULL, O.viewid= :viewid, 1)";
					$stmt = Db::getInstance()->prepare($sql);
					$viewid = Helper::getViewId();
					if ($viewid > 0){
						$stmt->bindValue('viewid', $viewid);
					}
					else{
						$stmt->bindValue('viewid', NULL);
					}
					$stmt->bindValue('newdate', $substitutedServiceInfo['newdate']);
					try{
						$stmt->execute();
						while ($rs = $stmt->fetch()){
							$Data[] = Array(
								'idclient' => $rs['idclient']
							);
						}
					}
					catch (Exception $e){
						throw new CoreException($e->getMessage());
					}
					break;
				
				case 2:
					$sql = "SELECT 
								DISTINCT(CD.clientid) AS id
							FROM clientdata CD
							WHERE CD.lastlogged <= :newdate";
					$stmt = Db::getInstance()->prepare($sql);
					$viewid = Helper::getViewId();
					$stmt->bindValue('newdate', $substitutedServiceInfo['newdate']);
					try{
						$stmt->execute();
						while ($rs = $stmt->fetch()){
							$Data[] = Array(
								'idclient' => $rs['id']
							);
						}
					}
					catch (Exception $e){
						throw new CoreException($e->getMessage());
					}
					break;
				
				case 3:
					$sql = "SELECT DISTINCT(C.idclient),
					CD.firstname, CD.surname, CD.email
					FROM client C
					LEFT JOIN clientdata CD ON C.idclient = CD.clientid
					WHERE CD.lastlogged <= :newdate
					AND IF (:viewid IS NOT NULL, C.viewid= :viewid, 1)";
					$stmt = Db::getInstance()->prepare($sql);
					$viewid = Helper::getViewId();
					if ($viewid > 0){
						$stmt->bindValue('viewid', $viewid);
					}
					else{
						$stmt->bindValue('viewid', NULL);
					}
					$stmt->bindValue('newdate', $substitutedServiceInfo['newdate']);
					try{
						$stmt->execute();
						while ($rs = $stmt->fetch()){
							$Data[] = Array(
								'idclient' => $rs['idclient']
							);
						}
					}
					catch (Exception $e){
						throw new CoreException($e->getMessage());
					}
					break;
				
				case 4:
					$sql = "SELECT DISTINCT(C.idclient),
					CD.firstname, CD.surname, CD.email
					FROM client C
					LEFT JOIN clientdata CD ON C.idclient = CD.clientid
					LEFT JOIN `order` O ON C.idclient = O.clientid
					WHERE O.adddate <= :newdate
					AND O.paymentmethodid IN (2, 4, 7)
					AND O.orderstatusid IN (7,9, 17, 18, 21, 22, 24)
					AND IF (:viewid IS NOT NULL, O.viewid= :viewid, 1)";
					$stmt = Db::getInstance()->prepare($sql);
					$viewid = Helper::getViewId();
					if ($viewid > 0){
						$stmt->bindValue('viewid', $viewid);
					}
					else{
						$stmt->bindValue('viewid', NULL);
					}
					$stmt->bindValue('newdate', $substitutedServiceInfo['newdate']);
					try{
						$stmt->execute();
						while ($rs = $stmt->fetch()){
							$Data[$idclient] = Array(
								'idclient' => $rs['idclient']
							);
						}
					}
					catch (Exception $e){
						throw new CoreException($e->getMessage());
					}
					break;
				
				case 5:
					$sql = "SELECT DISTINCT(C.idclient),
					CD.firstname, CD.surname, CD.email
					FROM client C
					LEFT JOIN clientdata CD ON C.idclient = CD.clientid
					LEFT JOIN `order` O ON C.idclient = O.clientid
					WHERE O.adddate <= :newdate
					AND O.orderstatusid = 6
					AND IF (:viewid IS NOT NULL, O.viewid= :viewid, 1)";
					$stmt = Db::getInstance()->prepare($sql);
					$viewid = Helper::getViewId();
					if ($viewid > 0){
						$stmt->bindValue('viewid', $viewid);
					}
					else{
						$stmt->bindValue('viewid', NULL);
					}
					$stmt->bindValue('newdate', $substitutedServiceInfo['newdate']);
					try{
						$stmt->execute();
						while ($rs = $stmt->fetch()){
							$Data[$idclient] = Array(
								'idclient' => $rs['idclient']
							);
						}
					}
					catch (Exception $e){
						throw new CoreException($e->getMessage());
					}
					break;
			}
		}
		return $Data;
	}

	public function getSubstitutedService ($idSubstitutedService)
	{
		$sql = "SELECT 
					S.idsubstitutedservice, 
					S.actionid, 
					S.`date`, 
					S.periodid, 
					S.admin, 
					S.name,
					P.idperiod, 
					P.name as pname, 
					P.timeinterval, 
					P.intervalsql
				FROM substitutedservice S
				LEFT JOIN period P ON S.periodid = P.idperiod
				WHERE idsubstitutedservice= :idsubstitutedservice";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idsubstitutedservice', $idSubstitutedService);
		$Data = Array();
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			$Data = Array(
				'idsubstitutedservice' => $rs['idsubstitutedservice'],
				'actionid' => $rs['actionid'],
				'date' => $rs['date'],
				'periodid' => $rs['periodid'],
				'admin' => $rs['admin'],
				'name' => $rs['name'],
				'idperiod' => $rs['idperiod'],
				'pname' => $rs['pname'],
				'timeinterval' => $rs['timeinterval'],
				'intervalsql' => $rs['intervalsql'],
				'newdate' => ''
			);
			$dateInterval = $rs['timeinterval'];
			if (! empty($dateInterval)){
				$date = new DateTime();
				$date->setDate(date("Y"), date("m"), date("d"));
				$date->modify($dateInterval);
				$Data['newdate'] = $date->format("Y-m-d");
			}
			else{
				$Data['newdate'] = $rs['date'];
			}
		}
		catch (Exception $e){
			throw new CoreException($e->getMessage());
		}
		return $Data;
	}

	public function saveSendingInfoNotification ($submittedData, $substitutedServiceId)
	{
		if (isset($submittedData['clients']) && ! empty($submittedData['clients']) && $substitutedServiceId > 0){
			Db::getInstance()->beginTransaction();
			$newId = 0;
			try{
				$newId = $this->addNewSubstitutedServiceSend($substitutedServiceId);
				if ($newId > 0){
					$this->addClientToSubstitutedServiceClient($newId, $submittedData['clients']);
				}
				else{
					return 0;
				}
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_NOTIFICATION_ADD'), 125, $e->getMessage());
			}
			
			Db::getInstance()->commit();
			return $newId;
		}
		else{
			return 0;
		}
	}

	public function addNewSubstitutedServiceSend ($substitutedServiceId)
	{
		$sql = 'INSERT INTO substitutedservicesend
				SET
				substitutedserviceid= :substitutedserviceid,
				senddate= NOW(),
				sendid= :userid,
				actionid= (SELECT actionid FROM substitutedservice WHERE idsubstitutedservice= :substitutedserviceid),
				viewid= :viewid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('substitutedserviceid', $substitutedServiceId);
		$stmt->bindValue('userid', App::getContainer()->get('session')->getActiveUserid());
		$stmt->bindValue('actionid', 0);
		if (Helper::getViewId() == 0){
			$stmt->bindValue('viewid', NULL);
		}
		else{
			$stmt->bindValue('viewid', Helper::getViewId());
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_INSERT_SUBSTITUTEDSERVICE_SEND'), 4, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	public function addClientToSubstitutedServiceClient ($newSubstitutedServiceSendId, $clientIds)
	{
		foreach ($clientIds as $clien => $clientId){
			$sql = 'INSERT INTO substitutedserviceclients
					SET
					substitutedservicesendid= :substitutedservicesendid,
					clientid= :clientid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('substitutedservicesendid', $newSubstitutedServiceSendId);
			$stmt->bindValue('clientid', $clientId);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_INSERT_SUBSTITUTEDSERVICE_SEND'), 4, $e->getMessage());
			}
		}
	}

	public function doLoadQueque ()
	{
		$total = $this->getCountClientsForNotification();
		if ($total > 0){
			return Array(
				'iTotal' => $total,
				'iCompleted' => 0
			);
		}
		else{
			return Array(
				'iTotal' => 0,
				'iCompleted' => 0
			);
		}
	}

	public function getCountClientsForNotification ()
	{
		$id = App::getContainer()->get('session')->getActiveQuequeParam();
		$count = 0;
		$sql = "SELECT COUNT(clientid) as count
				FROM substitutedserviceclients
				WHERE substitutedservicesendid= :id
				AND send=0
				AND error=0";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$count = $rs['count'];
			}
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		return $count;
	}

	public function getFileNameForTransMail ($actionid)
	{
		switch ($actionid) {
			case 1:
				return 'notifyRegisteredWithoutOrder';
				break;
			case 2:
				return 'notifyLoginDate';
				break;
			case 3:
				return 'notifyLoginPeriod';
				break;
			case 4:
				return 'notifyOrderWithoutPayment';
				break;
			case 5:
				return 'notifyOrderNotConfirmed';
				break;
		}
	}

	public function changeMailTagsData ($clienId, $newdate)
	{
		// utwórz tablicę dla wszystkich tagów
		$Data = Array(
		'firstname' => NULL,
		'surname' => NULL,
		'lastDateOrder' => NULL,
		'lastLogged' => NULL,
		'dateOfMaturity' => NULL,
		'termsOfPayment' => NULL,
		'orderDate' => NULL,
		'orderNo' => NULL,
		'orderPrice' => NULL
		);
		// Pobranie imienia i nazwiska
		$clientData = App::getModel('client')->getClientView($clienId);
		if (! empty($clientData)){
			$Data['firstname'] = $clientData['firstname'];
			$Data['surname'] = $clientData['surname'];
		}
		// Pobranie informacji o ostanim niezapłaconym lub niepotwierdzonym
		// zamówieniu klienta
		$sql = "SELECT O.`adddate`, O.idorder, O.paymentmethodname, O.globalprice
		FROM `order` O
		WHERE O.paymentmethodid IN (2, 4, 7)
		AND O.orderstatusid IN (6, 7, 9, 17, 18, 21, 22, 24)
		AND O.clientid= :clientid
		ORDER BY O.`adddate` DESC
		LIMIT 1";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('clientid', $clienId);
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$Data['orderDate'] = $rs['adddate'];
				$Data['orderNo'] = $rs['idorder'];
				$Data['termsOfPayment'] = $rs['paymentmethodname'];
				$Data['orderPrice'] = $rs['globalprice'];
				$Data['dateOfMaturity'] = $rs['adddate'];
			}
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		// Pobranie informacji o ostanim zamówieniu klienta
		$sql = "SELECT O.`adddate`
		FROM `order` O
		WHERE O.clientid= :clientid
		ORDER BY O.`adddate` DESC
		LIMIT 1";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('clientid', $clienId);
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$Data['lastDateOrder'] = $rs['adddate'];
			}
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		// Pobranie informacji o ostanim logowaniu klienta
		$sql = "SELECT CD.`lastlogged`
		FROM clientdata CD
		WHERE CD.clientid= :clientid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('clientid', $clienId);
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$Data['lastLogged'] = $rs['lastlogged'];
			}
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		return $Data;
	}
	
	public function doProcessQueque ($request)
	{
		
		$id = App::getContainer()->get('session')->getActiveQuequeParam();
		$total = $this->getLastRecordForNotification($id);
		
		$chunkSize = intval($request['iChunks']);
		$startFromReq = intval($request['iStartFrom']);
		$totalReq = intval($request['iTotal']);
		
		if ($totalReq > 0){
			// while ($startFromReq <= $total) {
			$i = ++$startFromReq;
			while ($startFromReq <= $totalReq){
				$startFrom = $this->getIdSubstitutedServiceClients($id);
				$clients = $this->getPartsOfClientsForNotification($startFromReq, $chunkSize);
				if (! empty($clients)){
					$transMailData = $this->getSubstitutedService($id);
					$fileName = $this->getFileNameForTransMail($transMailData['actionid']);
					$end = end($clients);
					foreach ($clients as $client){
						try{
							//$i ++;
							$mailsTags = $this->changeMailTagsData($client['clientid'], $transMailData['newdate']);
							$clientMail = App::getModel('client')->getClientMailAddress($client['clientid']);
							$this->registry->template->assign('active', $mailsTags);
							
							App::getModel('mailer')->sendEmail(Array(
								'template' => $fileName,
								'email' => Array(
									$clientMail
								),
								'bcc' => false,
								'subject' => $transMailData['name'],
								'viewid' => Helper::getViewId()
							));				
						}
						catch (Exception $e){
							$this->updateSendNotificationError($client['id']);
						}
						if ($client['id'] == $end['id']){
							return Array(
								'iStartFrom' => $i
							);
						}
					}
				}
				else{
					return Array(
						'iStartFrom' => 0,
						'bFinished' => true
					);
				}
			}
			return Array(
				'iStartFrom' => 0,
				'bFinished' => true
			);
		}
		else{
			return Array(
				'iStartFrom' => 0
			);
		}
	}

	public function doSuccessQueque ($request)
	{
		if ($request['bFinished']){
			return Array(
				'bCompleted' => true
			);
		}
	}

	public function getLastRecordForNotification ($id)
	{
		$rowId = 0;
		$sql = "SELECT idsubstitutedserviceclients as id
				FROM substitutedserviceclients
				WHERE substitutedservicesendid= :id
				AND send=0
				AND error=0
				ORDER BY idsubstitutedserviceclients DESC
				LIMIT 1";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$rowId = $rs['id'];
			}
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		return $rowId;
	}

	public function getIdSubstitutedServiceClients ($id)
	{
		$rowId = 0;
		$sql = "SELECT idsubstitutedserviceclients as id
				FROM substitutedserviceclients
				WHERE substitutedservicesendid= :id
				AND send=0
				AND error=0
				LIMIT 1";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$rowId = $rs['id'];
			}
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		return $rowId;
	}

	public function getPartsOfClientsForNotification ($startFrom, $chunkSize)
	{
		$id = App::getContainer()->get('session')->getActiveQuequeParam();
		$Data = Array();
		if ($id > 0){
			$sql = "SELECT idsubstitutedserviceclients as id, clientid
					FROM substitutedserviceclients
					WHERE substitutedservicesendid = :id
					AND idsubstitutedserviceclients >= :startFrom
					AND send=0
					AND error=0
					LIMIT {$chunkSize}";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $id);
			$stmt->bindValue('startFrom', $startFrom);
			
			try{
				$stmt->execute();
				while ($rs = $stmt->fetch()){
					$Data[] = Array(
						'id' => $rs['id'],
						'clientid' => $rs['clientid']
					);
				}
			}
			catch (Exception $e){
				throw new Exception($e->getMessage());
			}
			return $Data;
		}
		else{
			return 0;
		}
	}
	
	public function updateSendNotificationSuccess ($idsubstitutedserviceclients)
	{
		$sql = "UPDATE substitutedserviceclients
		SET
		send = 1
		WHERE idsubstitutedserviceclients= :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $idsubstitutedserviceclients);
		try{
			$rs = $stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		return true;
	}
	

}