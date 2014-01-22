<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2012 Gekosale. Zabronione jest usuwanie informacji o licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 */
namespace Gekosale\Plugin;

use XMLReader;

class ExchangexmlModel extends Component\Model\Datagrid
{
	protected $lastPeriodId;
	protected $forceExport = FALSE;

	public function __construct ($registry, $modelFile)
	{
		parent::__construct($registry, $modelFile);
		register_shutdown_function(array($this, 'unlockAll'));
	}

	public function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('exchange', array(
			'idexchange' => array(
				'source' => 'idexchange'
			),
			'name' => array(
				'source' => 'name'
			),
			'datatype' => array(
				'source' => 'datatype',
				'processFunction' => array(
					$this,
					'getDataType'
				)
			),
			'ie' => array(
				'source' => 'type',
				'processFunction' => array(
					$this,
					'getType'
				)
			),
			'url' => array(
				'url'
			),
			'lastdate' => array(
				'source' => 'lastdate'
			),
			'status' => array(
				'source' => 'lastprocessed',
				'processFunction' => array(
					$this,
					'getStatus'
				)
			)
		));
		$datagrid->setFrom('
			exchange
		');
	}

	public function getEntityTypes ($type)
	{
		$tmp = array(
			1 => $this->trans('TXT_PRODUCTS'),
			2 => $this->trans('TXT_CATEGORIES'),
			3 => $this->trans('TXT_CLIENTS'),
			4 => $this->trans('TXT_ORDERS')
		);


		if ($type == 2){
			$tmp += array(
				5 => $this->trans('TXT_CLIENTS_INCREMENTALLY'),
				6 => $this->trans('TXT_ORDERS_INCREMENTALLY')
			);
		}
		return \FormEngine\Option::Make($tmp);
	}

	public function doAJAXgetProfile ($request)
	{
		$file = ROOTPATH . 'design' . DS . '_data_panel' . DS . basename($request['url']);

		if ( !is_file($file)) {
			$source = $this->trans('ERR_FILE_NOT_EXIST');
		}
		else {
			$source = file_get_contents($file);
		}

		return array(
			'source' => $source
		);
	}

	public function getType ($type)
	{
		if ($type == 1) {
			return $this->trans('TXT_IMPORT');
		}

		return $this->trans('TXT_EXPORT');
	}

	public function getStatus ($status)
	{
		if (empty($status) || $status < 0) {
			return '<img src="' . DESIGNPATH . '_images_panel/icons/datagrid/red_button.png" />';
		}

		return '<img src="' . DESIGNPATH . '_images_panel/icons/datagrid/green_button.png" /> <span style="margin: -18px 0 0 40px">' . $status . '</span>';
	}

	public function getDataType ($data)
	{
		switch ($data) {
			case 1:
				return $this->trans('TXT_PRODUCTS');
			case 2:
				return $this->trans('TXT_CATEGORIES');
			case 3:
				return $this->trans('TXT_CLIENTS');
			case 4:
				return $this->trans('TXT_ORDERS');
			case 5:
				return $this->trans('TXT_CLIENTS_INCREMENTALLY');
			case 6:
				return $this->trans('TXT_ORDERS_INCREMENTALLY');
		}
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getOperationsForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteOperation ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteOperation'
		), $this->getName());
	}

	public function deleteOperation ($id)
	{
		DbTracker::deleteRows('exchange', 'idexchange', $id);
	}

	public function addOperation ($data)
	{
		$sql = "INSERT INTO exchange SET
			name = :name,
			type = :type,
			datatype = :datatype,
			pattern = :pattern,
			url = :url,
			username = :username,
			password = :password,
			periodically = :periodically,
			categoryseparator = :categoryseparator,
			`interval` = :interval,
			status = 0";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $data['profile_name']);
		$stmt->bindValue('type', $data['profile_type']);
		$stmt->bindValue('datatype', $data['profile_datatype']);
		$stmt->bindValue('pattern', $data['profile_pattern']);
		$stmt->bindValue('url', !empty($data['files']['file']) ? URL . 'upload/' .$data['files']['file'] : $data['profile_url']);
		$stmt->bindValue('username', $data['profile_url_username']);
		$stmt->bindValue('password', $data['profile_url_password']);
		$stmt->bindValue('periodically', $data['profile_periodically']);
		$stmt->bindValue('categoryseparator', $data['profile_categoryseparator']);
		$stmt->bindValue('interval', $data['profile_interval']);
		try {
			$stmt->execute();
		}
		catch(Exception $e) {
			return FALSE;
		}

		return TRUE;
	}

	public function getOperationById ($id)
	{
		$sql = "SELECT * FROM exchange WHERE idexchange = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		return $stmt->fetch();
	}

	public function getOperations ()
	{
		$sql = "SELECT * FROM exchange";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public function getOperationBySha1 ($id)
	{
		$sql = "SELECT * FROM exchange WHERE SHA1(CONCAT(idexchange,':',name)) = :id LIMIT 1";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		return $stmt->fetch();
	}

	public function queueOperation ($id)
	{
		$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 0;
		$offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;

		if ($offset < 0 || $limit < 0) {
			App::getContainer()->get('session')->setVolatileMessage('Błędny limit lub offset');
			return FALSE;
		}

		$rs = $this->getOperationById($id);
		if ( !$rs) {
			App::getContainer()->get('session')->setVolatileMessage('Błędne ID operacji');
			return FALSE;
		}

		if ($limit ==! 0 && $rs['type'] == 2) {
			$this -> forceExport = TRUE;

			$rs['limit'] = $limit;
			$rs['offset'] = $offset;

			$this->export($rs);
			exit;
		}
		else {
			if ($rs['status'] == 2) {
				App::getContainer()->get('session')->setVolatileMessage('Operacja jest w trakcie wykonywania');
				return FALSE;
			}

			$this->log($rs['idexchange'], 'Operację dodano do kolejki', TRUE);
			$this->setStatus($rs['idexchange'], 1);
		}

		return TRUE;
	}

	public function runOperation ($id)
	{
		Db::getInstance()->beginTransaction();

		$rs = $this->getOperationById($id);
		if ( !$rs) {
			App::getContainer()->get('session')->setVolatileMessage('Błędne ID operacji');
			return FALSE;
		}

		if ($this->isLocked()) {
			$this->log($rs['idexchange'], 'Nie można uruchomić zadania. Inne operacje są w trakcie wykonywania', TRUE);
			App::getContainer()->get('session')->setVolatileMessage('Nie można uruchomić zadania. Inne operacje są w trakcie wykonywania');
			return FALSE;
		}

		if ($rs['locked'] != 0) {
			$this->log($rs['idexchange'], 'Nie można uruchomić zadania. Operacja jest zablokowana', TRUE);
			App::getContainer()->get('session')->setVolatileMessage('Operacja jest zablokowana');
			return FALSE;
		}

		$this->lock($rs['idexchange'], TRUE);
		$this->setStatus($rs['idexchange'], 2);

		if ($rs['type'] == 1) {
			$status = $this->import($rs);
		}
		else {
			$status = $this->export($rs);
		}

		if ($status) {
			$this->log($rs['idexchange'], 'Zakończone');
			$this->setStatus($rs['idexchange'], 3);
		}
		else {
			$this->setStatus($rs['idexchange'], -1);
		}

		$this->lock($rs['idexchange'], FALSE);

		Db::getInstance()->commit();

		return $status;
	}

	public function export ($data)
	{
		$limits = '';
		if ($data['limit'] != 0) {
			$limits = sprintf( ' LIMIT %d OFFSET %d', $data['limit'], $data['offset']);
		}

		switch ($data['datatype']) {
			case 1:
				$this->log($data['idexchange'], 'Eksportowanie produktów');

				$categories = $this->getCategoryPath();

				$sql = "SELECT
					P.idproduct as id,
					PT.name AS name,
					P.ean as ean,
					ROUND(P.sellprice,2) as sellprice,
					P.stock as stock,
					ROUND(P.weight,2) as weight,
					PP.photoid,
					PRT.name as producer,
					P.enable as avail,
					PT.description,
					PT.seo,
					PC.categoryid
						FROM producttranslation PT
						LEFT JOIN product P ON P.idproduct = PT.productid
						LEFT JOIN productcategory PC ON PC.productid = P.idproduct
						LEFT JOIN categorytranslation CT ON PC.categoryid = CT.categoryid AND CT.languageid = :languageid
						LEFT JOIN categorypath C ON C.ancestorcategoryid = CT.categoryid
						LEFT JOIN producertranslation PRT ON P.producerid = PRT.producerid AND PRT.languageid = :languageid
						LEFT JOIN productphoto PP ON PP.productid = P.idproduct AND PP.mainphoto = 1
						LEFT JOIN file F ON F.idfile = PP.photoid
						LEFT JOIN vat V ON P.vatid = V.idvat
						LEFT JOIN currency BUYCUR ON P.buycurrencyid = BUYCUR.idcurrency
						LEFT JOIN currency SELLCUR ON P.sellcurrencyid = SELLCUR.idcurrency
						WHERE PT.languageid = :languageid AND C.categoryid = PC.categoryid
						GROUP BY P.idproduct
						" . $limits;

				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('languageid', Helper::getLanguageId());

				try {
					$stmt->execute();
				}
				catch (Exception $e) {
					$this->log($data['idexchange'], 'Wystąpił problem podczas przetwarzania danych');
					return FALSE;
				}

				while ($rs = $stmt->fetch()){

					$Image = @App::getModel('gallery')->getOrginalImageById($rs['photoid']);

					$Data[] = Array(
						'id' => $rs['id'],
						'name' => $rs['name'],
						'url' => $this->registry->router->generate('frontend.productcart', true, Array(
							'param' => $rs['seo']
						)),
						'ean' => $rs['ean'],
						'description' => $rs['description'],
						'avail' => $rs['avail'],
						'price' => $rs['sellprice'],
						'stock' => $rs['stock'],
						'weight' => $rs['weight'],
						'photo' => App::getModel('gallery')->getImagePath($Image, App::getURLAdress()),
						'producer' => $rs['producer'],
						'category' => (isset($categories[$rs['categoryid']])) ? $categories[$rs['categoryid']] : '',
						'attributes' => $this->GetTechnicalDataForProduct($rs['id'])
					);
				}
				break;
			case 2:
				$this->log($data['idexchange'], 'Eksportowanie kategorii');

				$categories = $this->getCategoryPath();

				$sql = "SELECT
					CT.name,
					C.categoryid as parent,
					C.photoid AS photo,
					GROUP_CONCAT(DISTINCT V.name ORDER BY V.name ASC SEPARATOR ';') as shop
						FROM categorytranslation CT
						LEFT JOIN category C ON C.idcategory = CT.categoryid
						LEFT JOIN viewcategory VC ON VC.categoryid = C.idcategory
						LEFT JOIN view V ON VC.viewid = V.idview
						WHERE CT.languageid = :languageid
						GROUP BY
						CT.categoryid" . $limits;
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('languageid', Helper::getLanguageId());

				try {
					$stmt->execute();
				}
				catch (Exception $e) {
					$this->log($data['idexchange'], 'Wystąpił problem podczas przetwarzania danych');
					return FALSE;
				}

				$Data = Array();
				while ($rs = $stmt->fetch()){
					$Image = @App::getModel('gallery')->getOrginalImageById($rs['photoid']);

					$Data[] = Array(
						'name' => $rs['name'],
						'photo' => App::getModel('gallery')->getImagePath($Image, App::getURLAdress()),
						'parent' => (isset($categories[$rs['parent']])) ? $categories[$rs['parent']] : '',
						'shop' => $rs['shop']
					);
				}
				break;
			case 3:
				$this->log($data['idexchange'], 'Eksportowanie klientów');

				$sql = "SELECT
							C.idclient,
							AES_DECRYPT(CD.firstname, :encryptionkey) AS firstname,
							AES_DECRYPT(CD.surname, :encryptionkey) AS surname,
							AES_DECRYPT(CD.email, :encryptionkey) AS email,
							CGT.name AS groupname,
							AES_DECRYPT(CD.phone, :encryptionkey) AS phone,
							CD.adddate AS adddate,
							SUM(O.globalprice) AS ordertotal,
							V.name AS shop
						FROM
						client C
						LEFT JOIN clientdata CD ON CD.clientid = C.idclient
						LEFT JOIN clientgrouptranslation CGT ON CGT.clientgroupid = CD.clientgroupid AND CGT.languageid=1
						LEFT JOIN orderclientdata OCD ON OCD.clientid = CD.clientid
						LEFT JOIN `order` O ON O.idorder = OCD.orderid
						LEFT JOIN view V ON C.viewid = V.idview
						WHERE C.viewid IN (:views)
						GROUP BY C.idclient ORDER BY idclient ASC" . $limits;
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('views', implode(',', Helper::getViewIds()));
				$stmt->bindValue('encryptionkey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());

				try {
					$stmt->execute();
				}
				catch (Exception $e) {
					$this->log($data['idexchange'], 'Wystąpił problem podczas przetwarzania danych');
					return FALSE;
				}

				$Data = Array();
				while ($rs = $stmt->fetch()){
					$Data[] = Array(
						'idclient' => $rs['idclient'],
						'firstname' => $rs['firstname'],
						'surname' => $rs['surname'],
						'email' => $rs['email'],
						'phone' => $rs['phone'],
						'groupname' => $rs['groupname'],
						'adddate' => date('Y-m-d', strtotime($rs['adddate'])),
						'ordertotal' => $rs['ordertotal'],
						'shop' => $rs['shop']
					);
				}
				break;
			case 5:
				$this->log($data['idexchange'], 'Eksportowanie klientów przyrostowo');

				$sql = "SELECT
							C.idclient,
							AES_DECRYPT(CD.firstname, :encryptionkey) AS firstname,
							AES_DECRYPT(CD.surname, :encryptionkey) AS surname,
							AES_DECRYPT(CD.email, :encryptionkey) AS email,
							CGT.name AS groupname,
							AES_DECRYPT(CD.phone, :encryptionkey) AS phone,
							CD.adddate AS adddate,
							SUM(O.globalprice) AS ordertotal,
							V.name AS shop
						FROM
						client C
						LEFT JOIN clientdata CD ON CD.clientid = C.idclient
						LEFT JOIN clientgrouptranslation CGT ON CGT.clientgroupid = CD.clientgroupid AND CGT.languageid=1
						LEFT JOIN orderclientdata OCD ON OCD.clientid = CD.clientid
						LEFT JOIN `order` O ON O.idorder = OCD.orderid
						LEFT JOIN view V ON C.viewid = V.idview
						WHERE C.viewid IN (:views) AND CD.adddate >= :date
						GROUP BY C.idclient ORDER BY idclient ASC" . $limits;
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('views', implode(',', Helper::getViewIds()));
				$stmt->bindValue('encryptionkey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
				$stmt->bindValue('date', empty($data['lastdate']) ? '0000-00-00 00:00:00' : $data['lastdate']);

				try {
					$stmt->execute();
				}
				catch (Exception $e) {
					$this->log($data['idexchange'], 'Wystąpił problem podczas przetwarzania danych');
					return FALSE;
				}

				$Data = Array();
				while ($rs = $stmt->fetch()){
					$Data[] = Array(
						'idclient' => $rs['idclient'],
						'firstname' => $rs['firstname'],
						'surname' => $rs['surname'],
						'email' => $rs['email'],
						'phone' => $rs['phone'],
						'groupname' => $rs['groupname'],
						'adddate' => date('Y-m-d', strtotime($rs['adddate'])),
						'ordertotal' => $rs['ordertotal'],
						'shop' => $rs['shop']
					);
				}
				break;
			case 4:
				$this->log($data['idexchange'], 'Eksportowanie zamówień');

				$sql = 'SELECT
							O.idorder AS idorder,
							CONCAT(AES_DECRYPT(OC.surname,:encryptionkey)," ",AES_DECRYPT(OC.firstname,:encryptionkey)) AS client,
							CONCAT(O.globalprice," ",O.currencysymbol) AS globalprice,
							O.dispatchmethodprice AS dispatchmethodprice,
							OST.name AS orderstatusname,
							O.dispatchmethodname AS dispatchmethodname,
							O.paymentmethodname AS paymentmethodname,
							O.adddate AS adddate,
							V.name AS shop
						FROM `order` O
						LEFT JOIN orderstatus OS ON OS.idorderstatus=O.orderstatusid
						LEFT JOIN orderstatustranslation OST ON OS.idorderstatus = OST.orderstatusid AND OST.languageid = 1
						LEFT JOIN orderclientdata OC ON OC.orderid=O.idorder
						LEFT JOIN view V ON O.viewid = V.idview
						WHERE O.viewid IN (' . Helper::getViewIdsAsString() . ')
						ORDER BY idorder DESC' . $limits;
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('encryptionkey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());

				try {
					$stmt->execute();
				}
				catch (Exception $e) {
					$this->log($data['idexchange'], 'Wystąpił problem podczas przetwarzania danych');
					return FALSE;
				}

				$Data = Array();
				while ($rs = $stmt->fetch()){
					$Data[] = Array(
						'idorder' => $rs['idorder'],
						'client' => $rs['client'],
						'globalprice' => $rs['globalprice'],
						'dispatchmethodprice' => $rs['dispatchmethodprice'],
						'orderstatusname' => $rs['orderstatusname'],
						'dispatchmethodname' => $rs['dispatchmethodname'],
						'paymentmethodname' => $rs['paymentmethodname'],
						'adddate' => date('Y-m-d', strtotime($rs['adddate'])),
						'shop' => $rs['shop']
					);
				}
				break;
			case 6:
				$this->log($data['idexchange'], 'Eksportowanie zamówień przyrostowo');

				$sql = 'SELECT
							O.idorder AS idorder,
							CONCAT(AES_DECRYPT(OC.surname,:encryptionkey)," ",AES_DECRYPT(OC.firstname,:encryptionkey)) AS client,
							CONCAT(O.globalprice," ",O.currencysymbol) AS globalprice,
							O.dispatchmethodprice AS dispatchmethodprice,
							OST.name AS orderstatusname,
							O.dispatchmethodname AS dispatchmethodname,
							O.paymentmethodname AS paymentmethodname,
							O.adddate AS adddate,
							V.name AS shop
						FROM `order` O
						LEFT JOIN orderstatus OS ON OS.idorderstatus=O.orderstatusid
						LEFT JOIN orderstatustranslation OST ON OS.idorderstatus = OST.orderstatusid AND OST.languageid = 1
						LEFT JOIN orderclientdata OC ON OC.orderid=O.idorder
						LEFT JOIN view V ON O.viewid = V.idview
						LEFT JOIN orderhistory OH ON OH.orderid = O.idorder
						WHERE O.viewid IN (' . Helper::getViewIdsAsString() . ') AND IF(OH.adddate IS NULL, O.adddate > :date, OH.adddate > :date)
						GROUP BY idorder ORDER BY idorder DESC' . $limits;
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->bindValue('encryptionkey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
				$stmt->bindValue('date', empty($data['lastdate']) ? '0000-00-00 00:00:00' : $data['lastdate']);

				try {
					$stmt->execute();
				}
				catch (Exception $e) {
					$this->log($data['idexchange'], 'Wystąpił problem podczas przetwarzania danych');
					return FALSE;
				}

				$Data = Array();
				while ($rs = $stmt->fetch()){
					$Data[] = Array(
						'idorder' => $rs['idorder'],
						'client' => $rs['client'],
						'globalprice' => $rs['globalprice'],
						'dispatchmethodprice' => $rs['dispatchmethodprice'],
						'orderstatusname' => $rs['orderstatusname'],
						'dispatchmethodname' => $rs['dispatchmethodname'],
						'paymentmethodname' => $rs['paymentmethodname'],
						'adddate' => date('Y-m-d', strtotime($rs['adddate'])),
						'shop' => $rs['shop']
					);
				}
				break;
		}

		$pattern = 'export_pattern' . md5(microtime(1)) . '.tmp';
		$tmp = ROOTPATH . 'themes' . DS . $pattern;

		if ( !@file_put_contents($tmp, $data['pattern'])) {
			$this->log($data['idexchange'], 'Błąd podczas zapisu do pliku ' . $tmp);
			return FALSE;
		}

		$this->log($data['idexchange'], 'Ilość przetworzonych rekordów: ' . count($Data));

		if ( !$this->forceExport) {
			ob_start();
		}
		else {
			header('Content-Disposition: attachment;filename="' . basename($data['url']) . '"');
			header('Cache-Control: max-age=0');
		}

		$this->registry->template->assign('items', $Data);
		$this->registry->template->display($pattern);


		if ( !$this->forceExport) {
			$source = ob_get_contents();
			ob_clean();

			if (strncasecmp(URL . 'upload/', $data['url'], strlen(URL . 'upload/')) === 0) {
				$destination = ROOTPATH . DS . 'upload' . DS . substr($data['url'], strlen(URL . 'upload/'));
			}
			else {
				$destination = ROOTPATH . DS . 'upload' . DS . $data['url'];
			}

			if ( ! @file_put_contents($destination, $source)) {
				$this->log($data['idexchange'], 'Błąd podczas zapisu do pliku ' . $destination);
				return FALSE;
			}
		}

		$this->updateOperation($data['idexchange'], count($Data));

		@unlink($tmp);

		return TRUE;
	}

	public function import ($data)
	{
		@set_time_limit(0);

		if (strncasecmp(URL . 'upload/', $data['url'], strlen(URL . 'upload/')) === 0) {
			$tmpfile = ROOTPATH . 'upload' . DS . substr($data['url'], strlen(URL . 'upload/'));
		}
		else if (strncmp($data['url'], 'http://', 7) === 0 ) {
			$this->log($data['idexchange'], 'Pobieranie pliku: ' . $data['url']);

			$tmpfile = ROOTPATH . 'upload' . DS . 'tmp_' . microtime(1) . '.xml';
			$fp = fopen($tmpfile, 'w');

			$curl = curl_init();
			$headers = array(
				CURLOPT_URL => $data['url'],
				CURLOPT_ENCODING => 'deflate, gzip, zip',
				CURLOPT_FILE => $fp,
				CURLOPT_CONNECTTIMEOUT => 60,
			);

			// Basic authentication
			if ( !empty($data['profile_url_login'])) {
				$headers += array(
					CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
					CURLOPT_USERPWD => $data['username'] . ':' . $data['password']
				);
			}

			curl_setopt_array($curl, $headers);
			if ( @curl_exec($curl) === FALSE ) {
				App::getContainer()->get('session')->setVolatileMessage('Wystąpił problem podczas pobierania pliku XML');
				$this->log($data['idexchange'], 'Wystąpił problem podczas pobierania pliku XML');
				return FALSE;
			}
			curl_close($curl);
			fclose($fp);
		}
		else {
			$tmpfile = ROOTPATH . 'upload' . DS . $data['url'];

			if ( !is_file($tmpfile)) {
				App::getContainer()->get('session')->setVolatileMessage('Plik źródłowy nie istnieje');
				$this->log($data['idexchange'], 'Plik źródłowy ' . $tmpfile . ' nie istnieje');
				return FALSE;
			}
		}

		if ( ! $this->parseXml($data, $tmpfile, $data['limit'], $data['offset']) ) {
			$this->setStatus($data['idexchange'], -1);
			return FALSE;
		}
		$migrate = App::getModel('exchange/migrate');
		$chunks = 10;

		switch($data['datatype']) {
			case 1:
				// Categories
				$request = array(
					'iStartFrom' => 1
				);
				$migrate->doLoadQueueCategories($request);

				// Photos
				$total = $migrate->doLoadQuequePhotos();
				$this->log($data['idexchange'], 'Importowanie zdjęć');

				try {
					for($i = 0; $i < $total['iTotal']; $i = $i + $chunks) {
						// split into small chunks
						$request = array(
							'iStartFrom' => $i,
							'iChunks' => $chunks,
							'iTotal' => $total['iTotal']
						);
						$migrate->doProcessQuequePhotos($request);

						// time limit overrided by gallery.php
						@set_time_limit(0);
						$this->log($data['idexchange'], 'Zdjęcia, limit: ' . $chunks . ' offset: ' . $i);
					}

					// Producers
					$this->log($data['idexchange'], 'Importowanie producentów');
					$total = $migrate->doLoadQueueProducers();

					for($i = 0; $i < $total['iTotal']; $i = $i + $chunks) {
						// split into small chunks
						$request = array(
							'iStartFrom' => $i,
							'iChunks' => $chunks,
							'iTotal' => $total['iTotal']
						);
						$migrate->doProcessQuequeProducers($request);
						$this->log($data['idexchange'], 'Producenci, limit: ' . $chunks . ' offset: ' . $i);
					}

					// Products
					$total = $migrate->doLoadQuequeProducts();
					$this->log($data['idexchange'], 'Importowanie produktów');

					for($i = 0; $i < $total['iTotal']; $i = $i + $chunks) {
						// split into small chunks
						$request = array(
							'iStartFrom' => $i,
							'iChunks' => $chunks,
							'iTotal' => $total['iTotal']
						);
						$migrate->doProcessQuequeProducts($request);
						$this->log($data['idexchange'], 'Produkty, limit: ' . $chunks . ' offset: ' . $i);
					}

					$this->updateOperation($data['idexchange'], $total['iTotal']);
				}
				catch (Exception $e) {
					$this->setStatus($data['idexchange'], -1);
					$this-> unlockAll();
					throw new Exception($e->getMessage());
				}
				break;
			case 2:
				$categories = array_flip($this->getCategoryPath());

				$sql = "SELECT name, photo, parent, shop FROM importxml";
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->execute();
				$Data = $stmt->fetchAll();

				$this->log($data['idexchange'], 'Przetwarzanie kategorii');

				foreach ($Data as $key => $category){

					$name = $category['name'];
					$photo = $category['photo'];
					$parent = $category['parent'];
					$views = $this->getCategoryViewsByNames($category['shop']);

					if( empty($views)) {
						$views = array(
							Helper::getViewId()
						);
					}

					if ($parent != ''){
						$fullPath = implode('/', array_merge(explode('/', $parent), Array(
							$name
						)));
					}
					else{
						$fullPath = $name;
					}

					$categoryid = (isset($categories[$parent])) ? $categories[$parent] : NULL;

					if (isset($categories[$fullPath]) && $idcategory = $categories[$fullPath]){

						$sql = 'UPDATE categorytranslation SET
								seo	 		= :seo
							WHERE
								categoryid = :categoryid
							AND
								languageid = :languageid';
						$stmt = Db::getInstance()->prepare($sql);
						$stmt->bindValue('seo', strtolower(Core::clearSeoUTF($fullPath)));
						$stmt->bindValue('categoryid', $idcategory);
						$stmt->bindValue('languageid', Helper::getLanguageId());
						try{
							$stmt->execute();
						}
						catch (Exception $e){
							throw new CoreException($this->trans('ERR_CATEGORY_UPDATE'), 1, $e->getMessage());
						}

						$sql = 'UPDATE category SET
								photoid 	= :photoid,
								categoryid  = :categoryid
							WHERE
								idcategory = :idcategory';
						$stmt = Db::getInstance()->prepare($sql);
						$stmt->bindValue('idcategory', $idcategory);
						if ($categoryid == NULL){
							$stmt->bindValue('categoryid', NULL);
						}
						else{
							$stmt->bindValue('categoryid', $categoryid);
						}
						$stmt->bindValue('photoid', $this->getPhotoByName($photo));
						try{
							$stmt->execute();
						}
						catch (Exception $e){
							throw new CoreException($this->trans('ERR_CATEGORY_UPDATE'), 1, $e->getMessage());
						}

						$sql = 'DELETE FROM viewcategory WHERE categoryid =:id';
						$stmt = Db::getInstance()->prepare($sql);
						$stmt->bindValue('id', $idcategory);
						$stmt->execute();

						foreach ($views as $key => $val){
							$sql = 'INSERT INTO viewcategory (categoryid,viewid)
												VALUES (:categoryid, :viewid)';
							$stmt = Db::getInstance()->prepare($sql);

							$stmt->bindValue('categoryid', $idcategory);
							$stmt->bindValue('viewid', $val);
							try{
								$stmt->execute();
							}
							catch (Exception $e){
								throw new CoreException($this->trans('ERR_NEWS_ADD'), 4, $e->getMessage());
							}
						}
					}
					else{
						$sql = 'INSERT INTO category SET
									photoid = :photoid,
									categoryid  = :categoryid';
						$stmt = Db::getInstance()->prepare($sql);
						if ($categoryid == NULL){
							$stmt->bindValue('categoryid', NULL);
						}
						else{
							$stmt->bindValue('categoryid', $categoryid);
						}
						$stmt->bindValue('photoid', $this->getPhotoByName($photo));
						try{
							$stmt->execute();
						}
						catch (Exception $e){
							throw new CoreException($this->trans('ERR_CATEGORY_ADD'), 3003, $e->getMessage());
						}

						$idcategory = Db::getInstance()->lastInsertId();

						$sql = 'INSERT INTO categorytranslation (
									categoryid,
									name,
									seo,
									languageid
								)
								VALUES
								(
									:categoryid,
									:name,
									:seo,
									:languageid
								)';
						$stmt = Db::getInstance()->prepare($sql);
						$stmt->bindValue('categoryid', $idcategory);
						$stmt->bindValue('name', $name);
						$stmt->bindValue('seo', strtolower(Core::clearSeoUTF($fullPath)));
						$stmt->bindValue('languageid', Helper::getLanguageId());
						$stmt->execute();

						foreach ($views as $key => $val){
							$sql = 'INSERT INTO viewcategory (categoryid,viewid)
									VALUES (:categoryid, :viewid)';
							$stmt = Db::getInstance()->prepare($sql);

							$stmt->bindValue('categoryid', $idcategory);
							$stmt->bindValue('viewid', $val);
							try{
								$stmt->execute();
							}
							catch (Exception $e){
								throw new CoreException($this->trans('ERR_NEWS_ADD'), 4, $e->getMessage());
							}
						}
					}
				}

				$this->log($data['idexchange'], 'Zaimportowano ' . count($Data) . ' kategorii');

				App::getModel('category')->getCategoriesPathById();
				App::getModel('seo')->doRefreshSeoCategory();
				$this->updateOperation($data['idexchange'], count($Data));
				break;
			case 3:
				$sql = "SELECT email, phone, adddate, ordertotal, firstname, surname, groupname, shop FROM importxml";
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->execute();
				$Data = $stmt->fetchAll();

				$this->log($data['idexchange'], 'Importowanie klientów');

				foreach ($Data as $user) {
					$viewid = $this->getViewByName($user['shop']);
					$newClientId = $this->addClient($user['email'], Core::passwordGenerate(), $viewid);

					$userData = array(
						'clientgroupid' => $this->addEmptyClientGroup($user['groupname']),
						'firstname' => $user['firstname'],
						'surname' => $user['surname'],
						'email' => $user['email'],
						'phone' => $user['phone']
					);
					$this->addClientData($userData, $newClientId);
				}

				$this->log($data['idexchange'], 'Zaimportowano ' . count($Data) . ' klientów');
				$this->updateOperation($data['idexchange'], count($Data));
				break;
			case 4:
				$sql = 'SELECT globalprice, dispatchmethodprice, client, orderstatusname, dispatchmethodname, paymentmethodname, shop FROM importxml';
				$stmt = Db::getInstance()->prepare($sql);
				$stmt->execute();

				$i = 0;
				$Data = $stmt->fetchAll();
				foreach ($Data as $order) {
					list($surname, $firstname) = explode(' ', $order['client']);
					$clientId = $this->getClientByNameAndSurname($firstname, $surname);

					$sql2 = 'INSERT INTO `order` SET
								clientid = :clientid,
								orderstatusid = (SELECT orderstatusid FROM orderstatustranslation WHERE name = :orderstatusname AND languageid = :languageid LIMIT 1),
								price = :price,
								dispatchmethodprice = :dispatchmethodprice,
								globalprice = :globalprice,
								dispatchmethodname = :dispatchmethodname,
								dispatchmethodid := (SELECT iddispatchmethod FROM dispatchmethod WHERE name = :dispatchmethodname),
								paymentmethodname = :paymentmethodname,
								paymentmethodid = (SELECT idpaymentmethod FROM paymentmethod WHERE name = :paymentmethodname),
								viewid = :viewid,
								sessionid = :sessionid,
								currencyid = :currencyid,
								pricebeforepromotion = 0,
								coupondiscount = 0.0,
								currencysymbol = :currencysymbol,
								currencyrate = :currencyrate';
					$stmt2 = Db::getInstance()->prepare($sql2);
					$stmt2->bindValue('clientid', $clientId);
					$stmt2->bindValue('dispatchmethodprice', $order['dispatchmethodprice']);
					$stmt2->bindValue('price', $order['globalprice'] - $order['dispatchmethodprice']);
					$stmt2->bindValue('globalprice', $order['globalprice']);
					$stmt2->bindValue('dispatchmethodname', $order['dispatchmethodname']);
					$stmt2->bindValue('paymentmethodname', $order['paymentmethodname']);
					$stmt2->bindValue('viewid', $this->getViewByName($order['shop']));
					$stmt2->bindValue('sessionid', session_id());
					$stmt2->bindValue('currencyid', App::getContainer()->get('session')->getActiveCurrencyId());
					$stmt2->bindValue('currencysymbol', App::getContainer()->get('session')->getActiveCurrencySymbol());
					$stmt2->bindValue('currencyrate', App::getContainer()->get('session')->getActiveCurrencyRate());
					$stmt2->bindValue('orderstatusname', $order['orderstatusname']);
					$stmt2->bindValue('languageid', Helper::getLanguageId());

					try{
						$stmt2->execute();
					}
					catch (Exception $e){
						$this->log($data['idexchange'], 'Wystąpił problem podczas przetwarzania przetwarzania danych');
						App::getContainer()->get('session')->setVolatileMessage('Wystąpił problem podczas przetwarzania przetwarzania danych');
						return FALSE;
					}

					$lastId = Db::getInstance()->lastInsertId();

					$sql2 = 'INSERT INTO orderclientdata SET
								orderid = :orderid,
								firstname = AES_ENCRYPT(:firstname, :encryptionKey),
								surname = AES_ENCRYPT(:surname, :encryptionKey),
								email = (SELECT email FROM clientdata WHERE idclientdata = :clientid),
								clientid = :clientid,
								adddate = :adddate
							';
					$stmt2 = Db::getInstance()->prepare($sql2);
					$stmt2->bindValue('orderid', $lastId);
					$stmt2->bindValue('firstname', $firstname);
					$stmt2->bindValue('surname', $surname);
					$stmt2->bindValue('clientid', $clientId);
					$stmt2->bindValue('adddate', $order['adddate']);
					$stmt2->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
					try{
						$stmt2->execute();
					}
					catch (Exception $e){
						$this->log($data['idexchange'], 'Wystąpił problem podczas przetwarzania przetwarzania danych');
						App::getContainer()->get('session')->setVolatileMessage('Wystąpił problem podczas przetwarzania przetwarzania danych');
						return FALSE;
					}


					$sql2 = 'INSERT INTO orderclientdeliverydata SET
								orderid = :orderid,
								firstname = AES_ENCRYPT(:firstname, :encryptionKey),
								surname = AES_ENCRYPT(:surname, :encryptionKey),
								email = (SELECT email FROM clientdata WHERE idclientdata = :clientid),
								adddate = :adddate
							';
					$stmt2 = Db::getInstance()->prepare($sql2);
					$stmt2->bindValue('orderid', $lastId);
					$stmt2->bindValue('firstname', $firstname);
					$stmt2->bindValue('surname', $surname);
					$stmt2->bindValue('clientid', $clientId);
					$stmt2->bindValue('adddate', $order['adddate']);
					$stmt2->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
					try{
						$stmt2->execute();
					}
					catch (Exception $e){
						$this->log($data['idexchange'], 'Wystąpił problem podczas przetwarzania przetwarzania danych');
						App::getContainer()->get('session')->setVolatileMessage('Wystąpił problem podczas przetwarzania przetwarzania danych');
						return FALSE;
					}

					++$i;
				}
				$this->log($data['idexchange'], 'Zaimportowano ' . count($Data) . ' zamówień');
				$this->updateOperation($data['idexchange'], count($Data));
				break;
		}

		return TRUE;
	}

	public function updateOperation ($id, $count)
	{
		$sql = "UPDATE exchange SET lastdate = NOW(), lastprocessed = :lastprocessed WHERE idexchange = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('lastprocessed', $count);
		$stmt->execute();
	}

	public function parseXml ($rs, $file, $limit = 0, $offset = 0)
	{
		if ( !is_file($file)) {
			App::getContainer()->get('session')->setVolatileMessage('Podany plik ' . $file . ' nie istnieje');
			$this->log($rs['idexchange'], 'Podany plik ' . $file . ' nie istnieje');
			return FALSE;
		}

		if( empty($rs['categoryseparator'])) {
			$rs['categoryseparator'] = '/';
		}

		$patternSource = $rs['pattern'];
		$pattern = ROOTPATH . 'themes' . DS . 'import_pattern' . md5(microtime(1)) . '.tmp';
		file_put_contents($pattern, $patternSource);

		$data = array(
			'main' => NULL,
			'product.id' => NULL,
			'product.url' => NULL,
			'product.price' => NULL,
			'product.avail' => NULL,
			'product.weight' => NULL,
			'product.stock' => NULL,
			'product.categories' => NULL,
			'product.name' => NULL,
			'product.imageurl' => NULL,
			'product.description' => NULL,
			'product.producer' => NULL,
			'product.ean' => NULL,
			'product.vat' => NULL,
			'product.attribute.setname' => NULL,
			'product.attribute.group' => NULL,
			'product.attribute.name' => NULL,
			'product.attribute.value' => NULL,

			'category.name' => NULL,
			'category.photo' => NULL,
			'category.shop' => NULL,
			'category.parent' => NULL,

			'client.email' => NULL,
			'client.phone' => NULL,
			'client.adddate' => NULL,
			'client.ordertotal' => NULL,
			'client.firstname' => NULL,
			'client.surname' => NULL,
			'client.groupname' => NULL,
			'client.shop' => NULL,

			'order.globalprice' => NULL,
			'order.dispatchmethodprice' => NULL,
			'order.adddate' => NULL,
			'order.client' => NULL,
			'order.orderstatusname' => NULL,
			'order.dispatchmethodname' => NULL,
			'order.paymentmethodname' => NULL,
			'order.shop' => NULL,
		);

		$dataVal = array_splice(array_keys($data), 1);
		$cleanData = array_combine($dataVal, array_fill(0, count($dataVal), NULL));

		// {{ product.id }}
		$dataVal = array_map(function($str) {
				return '{{ ' . $str . ' }}';
			}, $dataVal);


		$xml = new XMLReader();
		$xml->open($pattern);

		if( !@$xml->read()) {
			$this->log($rs['idexchange'], 'Wystąpił błąd podczas przetwarzania pliku wzorca');
			$this->parseError($rs);
			return FALSE;
		}

		if( !@$xml->read()) {
			$this->parseError($rs);
			$this->log($rs['idexchange'], 'Wystąpił błąd podczas przetwarzania pliku wzorca');
			return FALSE;
		}

		$this->log($rs['idexchange'], 'Przetwarzanie pliku wzorca');

		$parent = array();
		$attrs = array();
		$firstElement = TRUE;
		while(@$xml->read())
		{
			if($xml->nodeType == XMLReader::ATTRIBUTE)
			{
				continue;
			}

			if($xml->nodeType == XMLReader::ELEMENT)
			{

				$attrs = array();
				if($firstElement)
				{
					$data['main'] = $xml->name;
					$firstElement = FALSE;
				}

				$parent[] = $xml->name;

				if($xml->hasAttributes)
				{
					while($xml->moveToNextAttribute())
					{
						if(in_array($xml->value, $dataVal))
						{
							$data[ substr($xml->value, 3, -3)] = array(
								'parent' => $parent,
								'attr' => $xml->name
							);

							$attrs[] = array($xml->name);
						}
						else
						{
							$attrs[$xml->value][] = $xml->name;
						}
					}
					array_pop($parent);
					continue;
				}
				asort($attrs);
			}


			if($xml->nodeType == XMLReader::END_ELEMENT)
			{
				array_pop($parent);
				$attrs=array();
			}

			if(in_array($xml->value, $dataVal))
			{
				$data[ substr($xml->value, 3, -3)] = array(
					'parent' => $parent,
					'attrs' => $attrs,
					'element' => $xml->name
				);
			}
		}

		$xml = new XMLReader();
		$xml->open($file);
		$parent = array();

		$item = array();
		$parent = array();
		$attrs = array();

		if( !@$xml->read()) {
			$this->log($rs['idexchange'], 'Wystąpił błąd podczas przetwarzania pliku XML');
			$this->parseError($rs);
			return FALSE;
		}

		if( !@$xml->read()) {
			$this->log($rs['idexchange'], 'Wystąpił błąd podczas przetwarzania pliku XML');
			$this->parseError($rs);
			return FALSE;
		}

		$this->log($rs['idexchange'], 'Przetwarzanie pliku XML');

		if ($limit != 0 || $offset != 0) {
			$this->log($rs['idexchange'], 'Limit ' . $limit . ', Offset ' . $offset);
		}

		$sql = 'TRUNCATE TABLE importxml';
		$stmt = Db::getInstance()->query($sql);

		// 1 - Products
		// 2 - Categories
		// 3 - Clients
		// 4 - Orders
		switch($rs['datatype'])
		{
			case 1:
				$sql = "INSERT INTO importxml SET
				migrateid = :migrateid,
				name = :name,
				url = :url,
				categories = :categories,
				price = :price,
				weight = :weight,
				stock = :stock,
				imageurl = :imageurl,
				description = :description,
				producer = :producer,
				ean = :ean,
				avail = :avail,
				attributes = :attributes,
				vat = :vat";
				break;
			case 2:
				$sql = "INSERT INTO importxml SET
				name = :name,
				photo = :photo,
				shop = :shop,
				parent = :parent";
				break;
			case 3:
			case 5:
				$sql = "INSERT INTO importxml SET
				email = :email,
				phone = :phone,
				adddate = :adddate,
				ordertotal = :ordertotal,
				firstname = :firstname,
				surname = :surname,
				groupname = :groupname,
				shop = :shop";
				break;
			case 4:
			case 6:
				$sql = "INSERT INTO importxml SET
				globalprice = :globalprice,
				dispatchmethodprice = :dispatchmethodprice,
				adddate = :adddate,
				client = :client,
				orderstatusname = :orderstatusname,
				dispatchmethodname = :dispatchmethodname,
				paymentmethodname = :paymentmethodname,
				shop = :shop";
				break;
		}

		$climit = 0;
		$coffset = 0;

		while(@$xml->read())
		{
			if($xml->nodeType == XMLReader::ATTRIBUTE)
			{
				continue;
			}

			if($xml->nodeType == XMLReader::ELEMENT && $xml->name == $data['main'])
			{
				$parent = array();
				$item = $cleanData;
			}

			if($xml->nodeType == XMLReader::ELEMENT)
			{

				$parent[] = $xml->name;

				$attrs = array();
				if($xml->hasAttributes)
				{
					while($xml->moveToNextAttribute())
					{
						$tmp = array(
							'parent' => $parent,
							'attr' => $xml->name,
						);

						if(in_array($tmp, $data))
						{
							$item[array_search($tmp, $data)][] = $xml->value;
							$attrs[] = array($xml->name);
						}
						else
						{
							$attrs[$xml->value][] = $xml->name;

						}
					}
					array_pop($parent);
					continue;
				}
				asort($attrs);
			}

			if($xml->nodeType == XMLReader::END_ELEMENT)
			{
				array_pop($parent);
				$attrs=array();
			}

			$tmp = array(
				'parent' => $parent,
				'attrs' => $attrs,
				'element' => $xml->name
			);

			if(in_array($tmp, $data))
			{
				$item[array_search($tmp, $data)][] = $xml->value;
			}

			if($xml->name == $data['main'] && $xml->nodeType == XMLReader::END_ELEMENT)
			{
				// skip
				if ($coffset < $offset) {
					++$coffset;
					continue;
				}

				try {
					$stmt = Db::getInstance()->prepare($sql);

					switch($rs['datatype']) {
						case 1:

							$attributes = array();
							foreach ($item as $key => $val) {
								if (strncmp($key, 'product.attribute.', 18) === 0) {
									$attributes[$key] = $val;
								}
							}

							$stmt->bindValue('attributes', serialize($attributes));
							$stmt->bindValue('migrateid', trim((string) $item['product.id'][0]));
							$stmt->bindValue('name', trim(ucfirst($item['product.name'][0])));
							$stmt->bindValue('url', trim((string) $item['product.url'][0]));

							if(strpos($item['product.categories'][0], $rs['categoryseparator'])) {
								$item['product.categories'] = explode($rs['categoryseparator'], $item['product.categories'][0]);
								$item['product.categories'][0] = array_map('trim', $item['product.categories']);
								$item['product.categories'][0] = array_map('ucfirst', $item['product.categories'][0]);
							}

							$stmt->bindValue('categories', serialize((array) $item['product.categories'][0]));

							$stmt->bindValue('price', (float) trim(($item['product.price'][0]) ?: 0));
							$stmt->bindValue('weight', (float) trim(($item['product.weight'][0]) ?: 0));
							$stmt->bindValue('stock', (int) ($item['product.stock'][0] === NULL ? 0 : trim($item['product.stock'][0])));
							$stmt->bindValue('imageurl', trim((string) $item['product.imageurl'][0]));
							$stmt->bindValue('description', trim((string) $item['product.description'][0]));
							$stmt->bindValue('producer', trim((string) $item['product.producer'][0]));
							$stmt->bindValue('ean', (string) trim(($item['product.ean'][0]) ?: ''));
							$stmt->bindValue('vat', (int) trim(($item['product.vat'][0]) ?: -1));
							$stmt->bindValue('avail', (int) ($item['product.avail'][0] === NULL ? 1 : trim($item['product.avail'][0])));
							break;
						case 2:
							$stmt->bindValue('name', trim($item['category.name'][0]));
							$stmt->bindValue('photo', trim($item['category.photo'][0]));
							$stmt->bindValue('parent', trim($item['category.parent'][0]));
							$stmt->bindValue('shop', trim($item['category.shop'][0]));
							break;
						case 3:
						case 5:
							$stmt->bindValue('email', trim($item['client.email'][0]));
							$stmt->bindValue('phone', trim($item['client.phone'][0]));
							$stmt->bindValue('adddate', trim($item['client.adddate'][0]));
							$stmt->bindValue('ordertotal', trim($item['client.ordertotal'][0]));
							$stmt->bindValue('firstname', trim($item['client.firstname'][0]));
							$stmt->bindValue('surname', trim($item['client.surname'][0]));
							$stmt->bindValue('groupname', trim($item['client.groupname'][0]));
							$stmt->bindValue('shop', trim($item['client.shop'][0]));
							break;
						case 4:
						case 6:
							$stmt->bindValue('globalprice', trim($item['order.globalprice'][0]));
							$stmt->bindValue('dispatchmethodprice', trim($item['order.dispatchmethodprice'][0]));
							$stmt->bindValue('adddate', trim($item['order.adddate'][0]));
							$stmt->bindValue('client', trim($item['order.client'][0]));
							$stmt->bindValue('orderstatusname', trim($item['order.orderstatusname'][0]));
							$stmt->bindValue('dispatchmethodname', trim($item['order.dispatchmethodname'][0]));
							$stmt->bindValue('paymentmethodname', trim($item['order.paymentmethodname'][0]));
							$stmt->bindValue('shop', trim($item['order.shop'][0]));
							break;
					}
					$stmt->execute();
				}
				catch(Exception $e) {
					$this->log($rs['idexchange'], 'Wystąpił problem podczas przetwarzania pliku XML');
					App::getContainer()->get('session')->setVolatileMessage('Wystąpił problem podczas przetwarzania pliku XML');
					return FALSE;
				}

				++$climit;

				if ($climit % 500 == 0) {
					$this->log($rs['idexchange'], 'Przetorzono ' . $climit . ' rekordów');
				}

				if ($limit != 0 && $climit >= $limit) {
					break;
				}
			}
		}

		$xml = NULL;

		//@unlink($file);
		@unlink($pattern);

		$sql = "SELECT COUNT(*) FROM importxml";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$nums = $stmt->fetchColumn();
		if($nums == 0) {
			App::getContainer()->get('session')->setVolatileMessage('Nie zaimportowano żadnych danych z pliku XML');
			$this->log($rs['idexchange'], 'Nie zaimportowano żadnych danych z pliku XML');
			$this->updateOperation($rs['idexchange'], 0);
			return FALSE;
		}

		$this->log($rs['idexchange'], 'Zaimportowano ' . $nums . ' rekordów');

		return TRUE;
	}

	public function edit($data)
	{
		$sql = "UPDATE exchange SET
				name = :name,
				type = :type,
				datatype = :datatype,
				pattern = :pattern,
				url = :url,
				username = :username,
				password = :password,
				periodically = :periodically,
				categoryseparator = :categoryseparator,
				`interval` = :interval
			WHERE
				idexchange = :idexchange";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $data['profile_name']);
		$stmt->bindValue('type', $data['profile_type']);
		$stmt->bindValue('datatype', $data['profile_datatype']);
		$stmt->bindValue('pattern', $data['profile_pattern']);
		$stmt->bindValue('url', !empty($data['files']['file']) ? URL . 'upload/' .$data['files']['file'] : $data['profile_url']);
		$stmt->bindValue('username', $data['profile_url_username']);
		$stmt->bindValue('password', $data['profile_url_password']);
		$stmt->bindValue('periodically', $data['profile_periodically']);
		$stmt->bindValue('categoryseparator', $data['profile_categoryseparator']);
		$stmt->bindValue('interval', $data['profile_interval']);
		$stmt->bindValue('idexchange', $this->registry->core->getParam(0));
		try {
			$stmt->execute();
		}
		catch(Exception $e) {
			return FALSE;
		}

		return TRUE;
	}

	public function getProductProducerByName ($producer)
	{
		$sql = 'SELECT producerid FROM producertranslation
				WHERE name = :producer GROUP BY producerid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('producer', $producer);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			return $rs['producerid'];
		}
		else{
			return null;
		}
	}

	public function getCategoryPath ()
	{
		$sql = 'SELECT
					C.categoryid,
					GROUP_CONCAT(SUBSTRING(CT.name, 1) ORDER BY C.order DESC SEPARATOR \'/\') AS path
				FROM categorytranslation CT
				LEFT JOIN categorypath C ON C.ancestorcategoryid = CT.categoryid
				WHERE CT.languageid = :languageid
				GROUP BY C.categoryid
				';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			$Data[$rs['categoryid']] = $rs['path'];
		}
		return $Data;
	}

	protected function getDownloadHeaders ($filename)
	{
		header('Content-Type: text/xml');
		header('Content-Disposition: attachment;filename="' . basename($filename) . '"');
		header('Cache-Control: max-age=0');
	}

	public function downloadFile ($id)
	{
		$data = $this->  getOperationById($id);
		if (empty($data)) {
			App::redirect(__ADMINPANE__ . '/exchangexml/index');
		}


		if (strncasecmp(URL, $data['url'], strlen(URL)) === 0) {
			$data['url'] = ROOTPATH . substr($data['url'], STRLEN(URL));
			if ( !is_file($data['url'])) {
				App::getContainer()->get('session')->setVolatileMessage('Podany plik nie istnieje');
				App::redirect(__ADMINPANE__ . '/exchangexml/index');
				return;
			}

			$this -> getDownloadHeaders($data['url']);
			$f = fopen($data['url'], 'r');
			fpassthru($f);

		}
		else if (strncasecmp($data['url'], 'http://', 7) === 0) {
			if (!empty($data['profile_url_login'])) {
				$url = 'http://' . $data['username'] . ':' . $data['password'] . '@' . substr($data['url'], 7);
			}
			else {
				$url = $data['url'];
			}

			$fp = tmpfile();

			$curl = curl_init();
			$headers = array(
				CURLOPT_URL => $data['url'],
				CURLOPT_ENCODING => 'deflate, gzip, zip',
				CURLOPT_FILE => $fp,
				CURLOPT_CONNECTTIMEOUT => 60,
			);

			// Basic authentication
			if ( !empty($data['profile_url_login'])) {
				$headers += array(
					CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
					CURLOPT_USERPWD => $data['username'] . ':' . $data['password']
				);
			}

			curl_setopt_array($curl, $headers);
			if ( @curl_exec($curl) === FALSE ) {
				App::getContainer()->get('session')->setVolatileMessage('Wystąpił problem podczas pobierania pliku XML');
				$this->log($data['idexchange'], 'Wystąpił problem podczas pobierania pliku XML');
				$this->lock($data['idexchange'], FALSE);
				return FALSE;
			}
			curl_close($curl);
			$this -> getDownloadHeaders($data['url']);
			fpassthru($fp);
			fclose($fp);
		}

		exit;
	}

	public function getCategoryViewsByNames ($views)
	{
		$views = explode(';', $views);

		$sql = 'SELECT idview FROM view
				WHERE FIND_IN_SET(CAST(name as CHAR), :views) GROUP BY idview';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('views', implode(',', $views));
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = $rs['idview'];
		}
		return $Data;
	}

	public function getPhotoByName ($name)
	{
		$sql = 'SELECT idfile FROM file WHERE name = :name';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $name);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = 1;
		if ($rs){
			$Data = $rs['idfile'];
		}
		return $Data;
	}

	protected function addClient ($email, $password, $viewid, $incrementally)
	{
		$hash = new \PasswordHash\PasswordHash();

		if ($email == ''){
			return 0;
		}

		if ($incrementally) {
			$sql = "SELECT idclient FROM client WHERE login = :login";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('login', $hash->HashLogin($email));
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs) {
				return $rs['idclient'];
			}
		}

		$sql = 'INSERT INTO client (login, password, disable, viewid)
				VALUES (:login, :password, 0, :viewid)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('login', $hash->HashLogin($email));
		$stmt->bindValue('password', $hash->HashPassword($password));

		if (Helper::getViewId() == 0){
			$stmt->bindValue('viewid', $viewid);
		}
		else{
			$stmt->bindValue('viewid', Helper::getViewId());
		}
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CLIENT_ADD'), 4, $e->getMessage());
		}
		return Db::getInstance()->lastInsertId();
	}

	protected function addClientData ($Data, $ClientId)
	{
		$sql = 'INSERT INTO clientdata(
					firstname,
					surname,
					email,
					phone,
					clientgroupid,
					clientid
				)VALUES (
					AES_ENCRYPT(:firstname, :encryptionKey),
					AES_ENCRYPT(:surname, :encryptionKey),
					AES_ENCRYPT(:email, :encryptionKey),
					AES_ENCRYPT(:phone, :encryptionKey),
					:clientgroupid,
					:clientid
				)';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('clientid', $ClientId);
		$stmt->bindValue('clientgroupid', $Data['clientgroupid']);
		$stmt->bindValue('firstname', $Data['firstname']);
		$stmt->bindValue('surname', $Data['surname']);
		$stmt->bindValue('email', $Data['email']);
		$stmt->bindValue('phone', $Data['phone']);
		$stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_CLIENTDATA_ADD'), 4, $e->getMessage());
		}
		return true;
	}

	public function addEmptyClientGroup ($name)
	{
		$sql = 'SELECT clientgroupid FROM clientgrouptranslation WHERE name = :name AND languageid = :languageid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $name);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		$rs = $stmt->fetch();

		if ($rs){
			$id = $rs['clientgroupid'];
		}
		else{

			$sql = 'INSERT INTO clientgroup (adddate) VALUES (NOW())';
			$stmt = Db::getInstance()->prepare($sql);

			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CLIENTGROUP_ADD'), 9, $e->getMessage());
			}

			$id = Db::getInstance()->lastInsertId();

			DbTracker::deleteRows('clientgrouptranslation', 'clientgroupid', $id);

			$sql = 'INSERT INTO clientgrouptranslation (clientgroupid, name, languageid)
					VALUES (:clientgroupid, :name, :languageid)';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('clientgroupid', $id);
			$stmt->bindValue('name', $name);
			$stmt->bindValue('languageid', Helper::getLanguageId());
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				throw new CoreException($this->trans('ERR_CLIENTGROUP_EDIT'), 10, $e->getMessage());
			}
		}

		return $id;
	}

	public function getViewByName ($name)
	{
		$sql = "SELECT idview FROM view WHERE name = :name";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('name', $name);

		$rs = $stmt->fetch();
		if ($rs) {
			return $rs['idview'];
		}

		return Helper::getViewId();
	}

	public function isLocked ()
	{
		$sql = "SELECT idexchange FROM exchange WHERE locked != 0 LIMIT 1";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();

		return (boolean) ($stmt->fetch());
	}

	protected function lock ($id, $lock = TRUE)
	{
		$sql = "UPDATE exchange SET locked = :lock WHERE idexchange = :id";
		$stmt = Db::getInstance()->prepare($sql);

		$stmt->bindValue('id', $id);
		$stmt->bindValue('lock', (int) (boolean) $lock);
		$stmt->execute();
	}

	public function unlockAll ()
	{
		$sql = "UPDATE exchange SET locked = 0";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
	}

	protected function log ($id, $message, $flush = FALSE)
	{
		if ($flush) {
			$sql = "UPDATE exchange SET log = '' WHERE idexchange = :id";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('id', $id);
			$stmt->execute();
		}

		if (!empty($message)) {
			$sql = "UPDATE exchange SET log = concat(log, :log) WHERE idexchange = :id";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('log', date('Y-m-d H:i:s') . ' - ' . $message . "\r\n");
			$stmt->bindValue('id', $id);
			$stmt->execute();
		}

		if (PHP_SAPI === 'cli') {
			fputs(STDOUT, $message . "\n");
		}
	}

	protected function parseError ($rs)
	{
		App::getContainer()->get('session')->setVolatileMessage('Wystąpił błąd podczas parsowania pliku XML');
		$this->log($rs['idexchange'], 'Wystąpił błąd podczas parsowania pliku XML');
		$this->lock($rs['idexchange'], FALSE);
		$this->updateOperation($rs['idexchange'], 0);
	}

	protected function getClientByNameAndSurname ($firstname, $surname)
	{
		$sql = 'SELECT idclientdata FROM clientdata WHERE AES_DECRYPT(firstname, :encryptionkey) = :firstname AND AES_DECRYPT(surname, :encryptionkey) = :surname LIMIT 1';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('surname', $surname);
		$stmt->bindValue('firstname', $firstname);
		$stmt->bindValue('encryptionkey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
		$stmt->execute();
		$rs = $stmt->fetch();

		if ( !$rs) {
			return NULL;
		}

		return $rs['idclientdata'];
	}

	protected function setStatus ($id, $status)
	{
		/**
		 * 0 - Nic nie rob
		 * 1 - Dodano do kolejki
		 * 2 - W trakcie wykonywania
		 * 3 - Zakonczono
		 * -1 - Error
		 */
		$sql = "UPDATE exchange SET status = :status WHERE idexchange = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('status', $status);
		$stmt->execute();
	}

	protected function setLimit ($id, $limit, $offset)
	{
		$sql = "UPDATE exchange SET `limit` = :limit, `offset` = :offset WHERE idexchange = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->bindValue('limit', $limit);
		$stmt->bindValue('offset', $offset);
		$stmt->execute();
	}

	public function GetTechnicalDataForProduct ($productId)
	{
		$languageId = Helper::getLanguageId();
		$sql = '
				SELECT
					TG.idtechnicaldatagroup AS id,
					TDS.name AS setname,
					TGT.name AS name
				FROM
					technicaldatagroup TG
					LEFT JOIN technicaldatagrouptranslation TGT ON TGT.technicaldatagroupid = TG.idtechnicaldatagroup AND TGT.languageid = :languageId
					LEFT JOIN producttechnicaldatagroup PTSG ON TG.idtechnicaldatagroup = PTSG.technicaldatagroupid
					LEFT JOIN technicaldatasetgroup TSG ON TSG.technicaldatagroupid = TG.idtechnicaldatagroup
					LEFT JOIN technicaldataset TDS ON TDS.idtechnicaldataset = TSG.technicaldatasetid
				WHERE
					PTSG.productid = :productId
				GROUP BY
					TG.idtechnicaldatagroup
				ORDER BY
					PTSG.order ASC
			';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('productId', $productId);
		$stmt->bindValue('languageId', $languageId);
		$stmt->execute();
		$groups = Array();
		$groupIndices = Array();
		while ($rs = $stmt->fetch()){
			$groupIndices[] = $rs['id'];
			$groups[] = Array(
				'id' => $rs['id'],
				'name' => $rs['name'],
				'setname' => $rs['setname'],
				'attributes' => Array()
			);
		}

		if (count($groups)){
			$sql = '
					SELECT
						TA.idtechnicaldataattribute AS id,
						TA.type AS type,
						IF (TA.type = 2, TAV.value, TGA.value) AS value,
						TSG.technicaldatagroupid AS group_id,
						TAT.name AS name
					FROM
						technicaldataattribute TA
						LEFT JOIN technicaldataattributetranslation TAT ON TAT.technicaldataattributeid = TA.idtechnicaldataattribute
						LEFT JOIN producttechnicaldatagroupattribute TGA ON TA.idtechnicaldataattribute = TGA.technicaldataattributeid
						LEFT JOIN producttechnicaldatagroupattributetranslation TAV ON TAV.producttechnicaldatagroupattributeid = TGA.idproducttechnicaldatagroupattribute
						LEFT JOIN producttechnicaldatagroup TSG ON TGA.producttechnicaldatagroupid = TSG.idproducttechnicaldatagroup

					WHERE
						TSG.productid = :productId
						AND TAT.languageId = :languageId
						AND ((TA.type <> 2) OR (TAV.languageid = :languageId))
					GROUP BY
						TA.idtechnicaldataattribute,
						TGA.idproducttechnicaldatagroupattribute
					ORDER BY
						TSG.order ASC,
						TGA.order ASC
				';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('productId', $productId);
			$stmt->bindValue('languageId', $languageId);
			$stmt->execute();
			$groupIndex = 0;
			$i = 0;
			while ($rs = $stmt->fetch()){
				$currentGroupIndex = $rs['group_id'];
				if ($currentGroupIndex != $groups[$groupIndex]['id']){
					if ($currentGroupIndex != $groups[++ $groupIndex]['id']){
						throw new CoreException('Something\'s wrong with the technical data indices...');
					}
				}

				$groups[$groupIndex]['attributes'][++$i] = array(
					'name' => $rs['name'],
					'value' => htmlspecialchars(str_replace("\n", "<br />", $rs['value']))
				);
			}
		}

		return $groups;
	}
}