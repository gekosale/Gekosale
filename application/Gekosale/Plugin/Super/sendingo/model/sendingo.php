<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2009-2011 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the
 * Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it
 * through the
 * world-wide-web, please send an email to license@verison.pl so we can send you
 * a copy immediately.
 */
namespace Gekosale\Plugin;

use FormEngine;
use Exception;
use xajaxResponse;

class SendingoModel extends Component\Model\Datagrid
{
	protected $settings;
	protected $isEnabled = FALSE;

	const SENDINGO_API_URL = 'https://app.sendingo.pl/';

	public function __construct ($registry, $modelFile)
	{
		parent::__construct($registry, $modelFile);

		$this->loadConfig();
	}

	public function loadConfig ()
	{
		$this->settings = $this->registry->core->loadModuleSettings('sendingo');

		if (empty($this->settings)) {
			$this->settings['groups'] = '';
		}
		else {
			// load groups from Sendingo
			if(empty($this->settings['groups'])) {
				$this->settings['groups'] = $this->getSendingoGroups();

				if (empty($this->settings['groups'])) {
					$this->addGroup(array('name'=>'Lista WellCommerce'));
					$this->settings = $this->registry->core->loadModuleSettings('sendingo');
					$this->isEnabled = TRUE;
					return;
				}
				$settings = $this->settings;
				$settings['groups'] = serialize($settings['groups']);

				$this->registry->core->saveModuleSettings('sendingo', $settings);
			}

			$this->settings['groups'] = @unserialize((string) $this->settings['groups']);
			$this->isEnabled = TRUE;

			if(empty($this->settings['auth_token'])) {
				$this->isEnabled = FALSE;
			}
		}
	}

	public function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('clientnewsletter', Array(
			'idclientnewsletter' => Array(
				'source' => 'CN.idclientnewsletter'
			),
			'email' => Array(
				'source' => 'CN.email'
			),
			'adddate' => Array(
				'source' => 'CN.adddate'
			),
			'active' => Array(
				'source' => 'IF( CN.active = 1, \'TXT_ACTIVE\', \'TXT_INACTIVE\')',
				'processLanguage' => true
			),
			'client' => array(
				'source' => 'IF( CD.idclientdata IS NOT NULL, \'TXT_NO\', \'TXT_YES\')',
				'processLanguage' => true
			),
			'sendingoid' => array(
				'source' => 'IF( CN.sendingoid IS NULL, \'TXT_NO\', \'TXT_YES\')',
				'processLanguage' => true
			)
		));
		$datagrid->setFrom('
				clientnewsletter CN
				LEFT JOIN clientdata CD ON CN.email = AES_DECRYPT(CD.email, :encryptionkey)
		');
	}

	public function getClientNewsletterForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXEnableClientNewsletter ($datagridId, $id)
	{
		try{
			$this->enableClientNewsletter($id);
			return $this->getDatagrid()->refresh($datagridId);
		}
		catch (Exception $e){
			$objResponse = new xajaxResponse();
			$objResponse->script("GF_Alert('{$this->trans('ERR_UNABLE_TO_ENABLE_USER')}', '{$e->getMessage()}');");
			return $objResponse;
		}
	}

	public function doAJAXDisableClientNewsletter ($datagridId, $id)
	{
		try{
			$this->disableClientNewsletter($id);
			return $this->getDatagrid()->refresh($datagridId);
		}
		catch (Exception $e){
			$objResponse = new xajaxResponse();
			$objResponse->script("GF_Alert('{$this->trans('ERR_UNABLE_TO_DISABLE_USER')}', '{$e->getMessage()}');");
			return $objResponse;
		}
	}

	public function disableClientNewsletter ($id)
	{
		$rs = $this->getEmailById($id);
		$this->sendingoDeleteEmail($rs['email'], $rs['viewid'], $rs['sendingoid']);

		$sql = 'UPDATE clientnewsletter SET active = 0, sendingoid = NULL WHERE idclientnewsletter = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
	}

	public function enableClientNewsletter ($id)
	{
		$sql = 'UPDATE clientnewsletter SET active = 1 WHERE idclientnewsletter = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}


		$rs = $this->getEmailById($id);
		$sendingoId = $this->sendingoAddEmail($rs['email'], $rs['viewid']);
		$this->updateSendingoId($rs['email'], $rs['viewid'], $sendingoId);
	}

	public function deleteClientNewsletter ($id)
	{
		$rs = $this->getEmailById($id);
		$this->sendingoDeleteEmail($rs['email'], $rs['viewid']);

		DbTracker::deleteRows('clientnewsletter', 'idclientnewsletter', $id);
	}

	public function doAJAXDeleteClientNewsletter ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteClientNewsletter'
		), $this->getName());
	}

	public function doRequest ($data, $action, $method = 'post', $useToken = 1)
	{
		if ($useToken) {
			$data['auth_token'] = @ $this->settings['auth_token'];
		}

		$ci = curl_init();

		curl_setopt($ci, CURLOPT_USERAGENT, 'WellCommerce API');
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);

		if ($method !== 'post') {
			$action .= '?auth_token=' . urlencode(@ $this->settings['auth_token']);
			unset($data['auth_token']);
		}
		else {
			$query = http_build_query($data);

			if (strpos($query, 'list_ids%5B') !== FALSE) {
				$query = preg_replace('~list_ids%5B\d+%5D~', 'list_ids%5B%5D', $query);
			}

			curl_setopt($ci, CURLOPT_POST, TRUE);
			curl_setopt($ci, CURLOPT_POSTFIELDS, $query);
		}
		curl_setopt($ci, CURLOPT_URL, self::SENDINGO_API_URL . $action);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);

		// DEBUG
		//$f = fopen( __DIR__ . '/debug.txt', 'a+');
		//curl_setopt($ci, CURLOPT_VERBOSE, TRUE);
		//if(!empty($query)) {
		//	fwrite($f, $query . "\n");
		//}
		//curl_setopt($ci, CURLOPT_STDERR, $f);

		$response = curl_exec($ci);
		curl_close($ci);

		// DEBUG
		//fwrite($f, $response . "\n" . str_repeat('-', 100));

		return json_decode($response, TRUE);
	}

	public function addFields ($event, $request)
	{
		$this->registry->xajaxInterface->registerFunction(array(
			'AddSendingoGroup',
			App::getModel('sendingo'),
			'addGroup'
		));

		$form = &$request['form'];
		
		$sendingo = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'sendingo_data',
			'label' => 'Integracja z Sendingo'
		)));

		if(empty($this->settings)) {
			$sendingo->AddChild(new FormEngine\Elements\Tip(Array(
				'tip' => '<p>Przejdź do <a href="' . App::getURLAdressWithAdminPane() . 'instancemanager/view/sendingo" target="_blank">Zarządzaj usługą &gt; Integracja Sendingo</a>, aby utworzyć konto w serwisie Sendingo.</p>'
			)));
		}
		else {
			$sendingo->AddChild(new FormEngine\Elements\Tip(Array(
				'tip' => '<p>Wybierz listy, do których ma zostać zapisany subskrybent.</p>',
				'direction' => FormEngine\Elements\Tip::DOWN
			)));

			$sendingo->AddChild(new FormEngine\Elements\MultiSelect(Array(
				'name' => 'sendingo_groups',
				'label' => $this->trans('TXT_CLIENTGROUPS'),
				'addable' => true,
				'onAdd' => 'xajax_AddSendingoGroup',
				'add_item_prompt' => 'Podaj nazwę grupy',
				'options' => FormEngine\Option::Make((array) $this->settings['groups'])
			)));

			$this->layer = $this->registry->loader->getCurrentLayer();

			$event->setReturnValues(array(
				'sendingo_data' => array(
					'sendingo_groups' => unserialize($this->layer['sendingo'])
				)
			));
		}
	}

	public function saveSettings ($request)
	{
		$sql = "UPDATE view SET sendingo = :sendingo WHERE idview = :idview";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('sendingo', serialize($request['data']['sendingo_groups']));
		$stmt->bindValue('idview', Helper::getViewId());
		$stmt->execute();
	}

	public function getSendingoGroups () {
		$groups = (array) $this->doRequest(array(), 'api/v1/lists.json', 'get');

		$grp = array();
		foreach ($groups as $group) {
			if (isset($group['id'])) {
				$grp[$group['id']] = $group['name'];
			}
		}

		return $grp;
	}

	public function getGroupsOptions ()
	{
		$Data = $this->settings['groups'];
		$tmp = Array();
		foreach ($Data as $key => $val){
			$tmp[] = Array(
				'sValue' => $key,
				'sLabel' => $val
			);
		}
		return $tmp;
	}

	public function addGroup ($request)
	{
		if (in_array($request['name'], (array) $this->settings['groups'])){
			$id = array_search($request['name'], $this->settings['groups']);
		}
		else {
			$this->doRequest(array(
				'name' => $request['name']
				), 'api/v1/lists'
			);

			// update structure
			$this->settings['groups'] = $this->getSendingoGroups();
			$settings = $this->settings;
			$settings['groups'] = serialize($settings['groups']);

			$this->registry->core->saveModuleSettings('sendingo', $settings);
		}

		return array(
			'id' => array_search($request['name'], $this->settings['groups']),
			'options' => $this->getGroupsOptions()
		);
	}

	public function getGroupsByViewId ($viewId)
	{
		static $views = array();

		if ($views === array()) {
			$sql = "SELECT idview, sendingo FROM view";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->execute();
			while ($rs = $stmt->fetch()) {
				$views[$rs['idview']] = unserialize($rs['sendingo']);
			}
		}

		if (empty($views[$viewId])) {
			reset($this->settings['groups']);
			$views[$viewId] = array(
				key($this->settings['groups'])
			);
		}

		return isset($views[$viewId]) ? $views[$viewId] : array();
	}

	public function sendingoAddEmail ($email, $viewId, $verify = FALSE)
	{
		if ( !$this->isEnabled) {
			return ;
		}

		$request = array(
			'name' => preg_replace('~[^a-zA-Z0-9\.\-_]+~', '_', $email),
			'email' => $email,
			'list_ids' => $this->getGroupsByViewId($viewId)
		);

		if ($verify) {
			$request['opt'] = 'true';
		}

		$data = $this->doRequest($request, 'api/v1/subscriber.json');

		return isset($data['id']) ? $data['id'] : NULL;
	}

	public function sendingoDeleteEmail ($email, $viewId, $sendingoId = 0)
	{
		if ($sendingoId !== 0) {
			$rs['sendingoid'] = $sendingoId;
		}
		else {
			$sql = "SELECT sendingoid FROM clientnewsletter WHERE email = :email AND viewid = :viewid AND sendingoid IS NOT NULL";
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('email', $email);
			$stmt->bindValue('viewid', $viewId);
			try {
				$stmt->execute();
			}
			catch (Exception $fe){
				throw new FrontendException($fe->getMessage());
			}

			$rs = $stmt->fetch();

			$sql = 'DELETE FROM clientnewsletter WHERE email = :email AND viewid = :viewid';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('email', $email);
			$stmt->bindValue('viewid', $viewId);
			try {
				$stmt->execute();
			}
			catch (Exception $fe){
				throw new FrontendException($fe->getMessage());
			}
		}



		if ($rs && $this->isEnabled) {
			$this->doRequest(array(
					'_method' => 'delete'
				),
				'api/v1/subscriber/' . $rs['sendingoid'] . '.json'
			);
		}
	}

	public function sendingoSyncEmails ()
	{
		$sql = "SELECT email, viewid FROM clientnewsletter WHERE sendingoid IS NULL AND active = 1";
		$stmt = Db::getInstance()->prepare($sql);
		try {
			$stmt->execute();
		}
		catch (Exception $fe){
			throw new FrontendException($fe->getMessage());
		}

		$sync = false;
		while ($rs = $stmt->fetch()) {
			$sendingoId = $this->sendingoAddEmail($rs['email'], $rs['viewid']);
			if ( $sendingoId !== FALSE) {
				$this->updateSendingoId($rs['email'], $rs['viewid'], $sendingoId);
				$sync = true;
			}
		}

		return $sync;
	}

	public function updateSendingoId ($email, $viewId, $sendingoId)
	{
		$sql = 'UPDATE clientnewsletter SET sendingoid = :sendingoid, active = 1 WHERE email = :email AND viewid = :viewid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('sendingoid', $sendingoId);
		$stmt->bindValue('email', $email);
		$stmt->bindValue('viewid', $viewId);
		try {
			$stmt->execute();
		}
		catch (Exception $fe){
			throw new FrontendException($fe->getMessage());
		}
	}

	public function activeEmail ($email, $viewId)
	{
		$sql = "UPDATE clientnewsletter	SET activelink = NULL, active = 1 WHERE email = :email AND viewid = :viewid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('email', $email);
		$stmt->bindValue('viewid', $viewId);
		try {
			$stmt->execute();
		}
		catch (Exception $fe){
			throw new FrontendException($fe->getMessage());
		}

		$sendingoId = $this->sendingoAddEmail($email, $viewId);
		$this->updateSendingoId($email, $viewId, $sendingoId);
	}

	public function deactiveEmail ($email, $viewId)
	{
		$rs = $this->getEmailById($id);
		$this->sendingoDeleteEmail($rs['email'], $rs['viewid'], $rs['sendingoid']);

		$sql = "UPDATE clientnewsletter	SET active = 0, sendingoid = NULL WHERE email = :email AND viewid = :viewid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('email', $email);
		$stmt->bindValue('viewid', $viewId);
		try {
			$stmt->execute();
		}
		catch (Exception $fe){
			throw new FrontendException($fe->getMessage());
		}
	}

	public function emailExists ($email, $viewId)
	{
		$sql = "SELECT idclientnewsletter FROM clientnewsletter WHERE email = :email AND viewid = :viewid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('email', $email);
		$stmt->bindValue('viewid', $viewId);
		$stmt->execute();

		$rs = $stmt->fetch();
		if($rs) {
			return $rs['idclientnewsletter'];
		}

		return 0;
	}

	protected function getEmailById ($id)
	{
		$sql = "SELECT email, viewid, sendingoid FROM clientnewsletter WHERE idclientnewsletter = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		return $stmt->fetch();
	}

}
