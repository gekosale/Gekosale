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
 * $Revision: 484 $
 * $Author: gekosale $
 * $Date: 2011-09-07 13:42:04 +0200 (Śr, 07 wrz 2011) $
 * $Id: substitutedservicesend.php 484 2011-09-07 11:42:04Z gekosale $ 
 */

namespace Gekosale\Plugin;

class SubstitutedservicesendModel extends Component\Model\Datagrid
{

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

	/**
	 * Metoda, na podstawie identyfikatora akcji
	 * pobiera identyfikatory klientów, spełniających kryteria
	 * do wysyłki powiadomienia.
	 *
	 * @param
	 *       	 integet substitutedServiceId (identyfikator wybranego
	 *       	 powiadomienia)
	 * @return array Data (tablica zawierająca identyfikatory klientów)
	 * @access public
	 */
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
					$sql = "SELECT DISTINCT(C.idclient),
	      							CD.firstname, CD.surname, CD.email
								FROM client C
									LEFT JOIN clientdata CD ON C.idclient = CD.clientid
									LEFT JOIN clienthistorylog CHL ON C.idclient = CHL.clientid
								WHERE CHL.`adddate` <= :newdate
									AND IF (:viewid IS NOT NULL, CHL.viewid= :viewid, 1)";
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
				
				case 3:
					$sql = "SELECT DISTINCT(C.idclient),
	      							CD.firstname, CD.surname, CD.email
								FROM client C
									LEFT JOIN clientdata CD ON C.idclient = CD.clientid
									LEFT JOIN clienthistorylog CHL ON C.idclient = CHL.clientid
								WHERE CHL.`adddate` <= :newdate
									AND IF (:viewid IS NOT NULL, CHL.viewid= :viewid, 1)";
					$stmt = Db::getInstance()->prepare($sql);
					$viewid = Helper::getViewId();
					if ($viewid > 0){
						$stmt->bindValue('viewid', $stmt);
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
		if (! empty($Data)){
			$filtered = $this->filterClientsArray($Data, $substitutedServiceInfo['timeinterval']);
		}
		if (! empty($filtered) && count($filtered) > 0){
			return $filtered;
		}
		else{
			return $Data;
		}
	}

	public function getSubstitutedService ($idSubstitutedService)
	{
		$sql = "SELECT S.idsubstitutedservice, S.transmailid, S.actionid, S.`date`, S.periodid, S.admin, S.name,
							P.idperiod, P.name as pname, P.timeinterval, P.intervalsql
						FROM substitutedservice S
	           				LEFT JOIN period P ON S.periodid = P.idperiod
						WHERE idsubstitutedservice= :idsubstitutedservice";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idsubstitutedservice', $idSubstitutedService);
		$Data = Array();
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$Data = Array(
					'idsubstitutedservice' => $rs['idsubstitutedservice'],
					'transmailid' => $rs['transmailid'],
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
					$date->setDate(("Y"), date("m"), date("d"));
					$date->modify($dateInterval);
					$Data['newdate'] = $date->format("Y-m-d");
				}
				else{
					$Data['newdate'] = $rs['date'];
				}
			}
		}
		catch (Exception $e){
			throw new CoreException($e->getMessage());
		}
		return $Data;
	}

	public function filterClientsArray ($clientsArray, $timeinterval)
	{
		$Data = Array();
		if (! empty($timeinterval)){
			$date = new DateTime();
			$date->setDate(("Y"), date("m"), date("d"));
			$date->modify($timeinterval);
			$newdate = $date->format("Y-m-d");
		}
		$clients = Array();
		foreach ($clientsArray as $client){
			array_push($clients, $client['idclient']);
		}
		$sql = "SELECT SSC.clientid
					FROM substitutedserviceclients SSC
						LEFT JOIN substitutedservicesend SSS ON SSC.substitutedservicesendid = SSS.idsubstitutedservicesend
					WHERE SSC.clientid NOT IN (:clients)
						AND IF(:newdate IS NOT NULL, SSS.senddate <= :newdate, 1)";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->setInInt('clients', $clients);
		if (isset($newdate) && $newdate != ''){
			$stmt->bindValue('newdate', $newdate);
		}
		else{
			$stmt->bindValue('newdate', NULL);
		}
		try{
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				$Data[] = $rs['clientid'];
			}
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		return $Data;
	}

	public function getClientsForMailSending ($submittedData, $idSubstitutedService)
	{
		$Data = Array();
		$Data['idsubstitutedservice'] = $idSubstitutedService;
		if (! empty($submittedData['clients'])){
			foreach ($submittedData['clients'] as $client => $clientId){
				$emailClient = App::getModel('client')->getClientMailAddress($clientId);
				if ($emailClient){
					$Data[$clientId] = $emailClient;
				}
			}
		}
		return $Data;
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
		$stmt->bindValue('newdate', $newdate);
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
		$stmt->bindValue('newdate', $newdate);
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
		$sql = "SELECT CHL.`adddate`
					FROM client C
						LEFT JOIN clientdata CD ON C.idclient = CD.clientid
						LEFT JOIN clienthistorylog CHL ON C.idclient = CHL.clientid
					WHERE CHL.clientid= :clientid
					ORDER BY CHL.`adddate` DESC
					LIMIT 1";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('clientid', $clienId);
		$stmt->bindValue('newdate', $newdate);
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$Data['lastLogged'] = $rs['adddate'];
			}
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		return $Data;
	}

	/**
	 * Zachowanie informacji o danej wysyłce oraz klientach do których mają
	 * zostać przesłane
	 * powiadomienia.
	 *
	 * @param $submittedData- array
	 *       	 dane przesłane postem z formularza
	 * @param $substitutedServiceId- int
	 *       	 identyfikator wybranego powiadomienia
	 * @return int $newId- zwraca nowy identyfikator wysyłki
	 * @access public
	 */
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

	/**
	 * Dodanie nowej wysyłki
	 *
	 * @param $substitutedServiceId- int
	 *       	 identyfikator wysyłki
	 * @return int idsubstitutedservicesend- nowy identyfikator wysyłki
	 * @access public
	 */
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

	/**
	 * Dołączenie klientów do danej wysyłki oraz przydzielenie im
	 * identyfikatorów wysyłki klienta
	 *
	 * @param $newSubstitutedServiceSendId- int
	 *       	 identyfikator wysyłki
	 * @param
	 *       	 array clientIds- tablica z identyfikatorami klientów
	 * @access public
	 */
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

	/**
	 * Pobranie liczby wszystkich klientów kwalifikujących się do wysłania
	 * powiadomienia
	 *
	 * @return int $count- suma wszystkich klientów
	 * @access public
	 */
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

	/**
	 * Pobranie pierwszego identyfikatora wysyłki klienta dla określonej wysyłki
	 * powiadomienia.
	 *
	 * @param
	 *       	 int substitutedservicesendid - identyfikator wysyłki
	 * @return int idsubstitutedserviceclients- identyfikator wysyłki klienta
	 * @access public
	 */
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

	/**
	 * Pobiera ostatni identyfikator wysyłki klienta dla określonej wysyłki.
	 *
	 *
	 * @param
	 *       	 int substitutedservicesendid - identyfikator wysyłki
	 * @return int idsubstitutedserviceclients- identyfikator wysyłki klienta
	 * @access public
	 */
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

	/**
	 * Pobranie porcji danych o klientach i identyfikatorach jego wysyłki
	 *
	 * @param $startFrom int
	 *       	 identyfikator wysyłki od którego należy rozpocząć pobieranie
	 *       	 kolejnych danych
	 * @param $chunkSize- int
	 *       	 ustwia limit pobieranych w porcji danych
	 * @return array $Data zawierająca listę kientów oraz id wysyłek klientów
	 * @access public
	 */
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
						LIMIT :chunkSize";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $id);
			$stmt->bindValue('startFrom', $startFrom);
			$stmt->bindValue('chunkSize', $chunkSize);
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

	/**
	 * Pobranie wszytkich informacji dotyczących wysyłanego powiadomienia o
	 * aktywności
	 *
	 * @param $idSubstitutedServicesend- int
	 *       	 identyfikator powiadomienia
	 * @return array $Data- dane dotyczące powiadomienia lub pusta tablica
	 * @access public
	 */
	public function getSubstitutedServiceForNotification ($idSubstitutedServicesend)
	{
		$sql = "SELECT S.idsubstitutedservice, S.transmailid, S.actionid, S.date, S.periodid, S.admin, S.name,
							P.idperiod, P.name as pname, P.timeinterval, P.intervalsql
					FROM substitutedservicesend SS
						LEFT JOIN substitutedservice S ON SS.substitutedserviceid = S.idsubstitutedservice
	           			LEFT JOIN period P ON S.periodid = P.idperiod
					WHERE SS.idsubstitutedservicesend= :idSubstitutedServicesend";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idSubstitutedServicesend', $idSubstitutedServicesend);
		$Data = Array();
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$Data = Array(
					'idsubstitutedservice' => $rs['idsubstitutedservice'],
					'transmailid' => $rs['transmailid'],
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
					$date->setDate(("Y"), date("m"), date("d"));
					$date->modify($dateInterval);
					$Data['newdate'] = $date->format("Y-m-d");
				}
				else{
					$Data['newdate'] = $rs['date'];
				}
			}
		}
		catch (Exception $e){
			throw new CoreException($e->getMessage());
		}
		return $Data;
	}

	/**
	 * Pobranie łącznej ilości elementów do przetworzenia
	 *
	 * @return array iTotal (łączna ilość maili), iCompleted = 0
	 * @access public
	 */
	public function doLoadQueque ()
	{
		$total = $this->getCountClientsForNotification(); // CALKOWITA ILOSC
		                                                  // ELEMENTOW DO
		                                                  // PRZETWORZENIA
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

	/**
	 * Metoda odpowiadająca za kolejkowanie i wysyłanie e-maili.
	 *
	 * @param
	 *       	 array request
	 * @return array iStartFrom- identyfikator rekordu od którego wysyłana
	 *         zostanie
	 *         kolejna paczka
	 * @return array iStartFrom =0, bFinished = true po wysłaniu wszystkich
	 *         e-maili
	 */
	public function doProcessQueque ($request)
	{
		
		$id = App::getContainer()->get('session')->getActiveQuequeParam();
		$total = $this->getLastRecordForNotification($id);
		
		$chunkSize = intval($request['iChunks']);
		$startFromReq = intval($request['iStartFrom']);
		$totalReq = intval($request['iTotal']);
		
		if ($totalReq > 0){
			// while ($startFromReq <= $total) {
			$i = 0;
			while ($startFromReq <= $totalReq){
				$startFrom = $this->getIdSubstitutedServiceClients($id);
				$clients = $this->getPartsOfClientsForNotification($startFromReq, $chunkSize);
				if (! empty($clients)){
					$transMailData = $this->getSubstitutedServiceForNotification($id);
					$fileName = App::getModel('transmailtemplates')->getFileNameForTransMail($transMailData['transmailid']);
					$end = end($clients);
					foreach ($clients as $client){
						try{
							$i ++;
							$mailsTags = $this->changeMailTagsData($client['clientid'], $transMailData['newdate']);
							$clientMail = App::getModel('client')->getClientMailAddress($client['clientid']);
							$this->registry->template->assign('active', $mailsTags);
							
							$mailer = new Mailer($this->registry);
							$mailer->AddEmbeddedImage('./design/_images_frontend/core/logos/' . $this->layer['photoid'], 'logo', $this->layer['photoid']);
							$mailer->loadContentToBody($fileName);
							$mailer->addAddress($clientMail);
							$mailer->setSubject($transMailData['name']);
							$mailer->FromName = App::getContainer()->get('session')->getActiveShopName();
							try{
								$mailer->Send();
							}
							catch (phpmailerException $e){
							
							}
							$mailer->ClearAddresses();
							$this->updateSendNotificationSuccess($client['id']);
						}
						catch (Exception $e){
							$this->updateSendNotificationError($client['id']);
						}
						if ($client['id'] == $end['id']){
							return Array(
								// 'iStartFrom'=> $end['id']
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

	public function updateSendNotificationError ($idsubstitutedserviceclients)
	{
		$sql = "UPDATE substitutedserviceclients
					SET
						error = 1
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

	public function getNotificationstAll ($substitutedserviceid)
	{
		$Data = Array();
		$sql = "SELECT SSS.idsubstitutedservicesend as id, SSS.senddate, SSS.sendid, SSS.actionid, SSS.viewid,
						CONCAT(UD.firstname, ' ', UD.surname) as senduser
					FROM substitutedservicesend SSS
						LEFT JOIN `user` U ON SSS.sendid = U.iduser
						LEFT JOIN `userdata` UD ON U.iduser = UD.userid
					WHERE IF(:substitutedserviceid>0, SSS.substitutedserviceid= :substitutedserviceid, 1)
						AND IF(:viewid >0, SSS.viewid= :viewid, 1)";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('substitutedserviceid', $substitutedserviceid);
		$stmt->bindValue('viewid', Helper::getViewId());
		try{
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				$Data[] = Array(
					'id' => $rs['id'],
					'senddate' => $rs['senddate'],
					'sendid' => $rs['sendid'],
					'senduser' => $rs['senduser'],
					'actionid' => $rs['actionid'],
					'viewid' => $rs['viewid']
				);
			}
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		return $Data;
	}

	public function getNotificationstAllToSelect ($substitutedserviceid)
	{
		$tmp = Array();
		$notifications = $this->getNotificationstAll($substitutedserviceid);
		if (is_array($notifications) && ! empty($notifications)){
			foreach ($notifications as $notification){
				$tmp[$notification['id']] = $notification['senddate'];
			}
		}
		return $tmp;
	}

	public function GetAllClientsForNotification ($request)
	{
		$Data = Array();
		if (isset($request['id']) && $request['id'] > 0){
			$sql = "SELECT idsubstitutedserviceclients, substitutedservicesendid, clientid, send, error, errorInfo
							FROM substitutedserviceclients
						WHERE  substitutedservicesendid = :substitutedservicesendid";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('substitutedservicesendid', $request['id']);
			try{
				$stmt->execute();
				while ($rs = $stmt->fetch()){
					if ($rs['send'] == 1 && $rs['error'] == 0){
						$send = 'Wysłano';
					}
					elseif ($rs['send'] == 0 && $rs['error'] == 1){
						$send = 'Błąd podczas wysyłania';
					}
					else{
						$send = 'Wiadomość nie została jeszcze wysłana';
					}
					$mail = App::getModel('client')->getClientMailAddress($rs['clientid']);
					if (empty($mail)){
						$mail = $rs['clientid'];
					}
					$cliens[$rs['idsubstitutedserviceclients']] = Array(
						$mail,
						$send
					);
				}
				if (! empty($cliens)){
					$Data = Array(
						'title' => 'Lista klientów do których wysłano wiadomość ',
						'data' => $cliens
					);
				}
				else{
					$Data = Array(
						'title' => 'Lista klientów jest pusta',
						'data' => Array()
					);
				}
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_NO_TEMPLATE_ACTION'));
			}
		}
		else{
			$Data = Array(
				'title' => '',
				'data' => Array()
			);
		}
		return $Data;
	}
}