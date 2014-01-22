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
 * $Id: eraty.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale\Plugin;

use FormEngine;
use sfEvent;

class EratyModel extends Component\Model
{
	protected $_name = 'Zakupy ratalne Żagiel S.A.';

	public function getPaymentMethod ($event, $request)
	{
		$Data[$this->getName()] = $this->_name;
		$event->setReturnValues($Data);
	}

	public function getPaymentMethodConfigurationForm ($event, $request)
	{
		if ($request['data']['paymentmethodmodel'] != $this->getName()){
			return false;
		}

		$eraty = &$request['form']->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'eraty_data',
			'label' => 'Konfiguracja'
		)));

		$eraty->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'numersklepu',
			'label' => 'Numer sklepu',
			'comment' => 'Wprowadź numer sklepu. Integracja testowa 28019999',
		)));

		$eraty->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'wariantsklepu',
			'label' => 'Wariant sklepu',
			'comment' => 'Wprowadź wariant sklepu. Itnegracja testowya 1',
		)));


		$eraty->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'typproduktu',
			'label' => 'Typ produktu',
			'comment' => 'Zmienna używana przez system Żagiel. Dla sprzedaży intenretowej bezwzględnie posiada wartość 0',
			'options' => array(
				new FormEngine\Option(0, 0)
			)
		)));

		$eraty->AddChild(new FormEngine\Elements\Select(Array(
			'name' => 'char',
			'label' => 'Kodowanie znaków',
			'comment' => 'Kodowanie znaków. Możesz wybrać jedną z wartości: ISO (ISO-8859-2), UTF (UTF-8) lub WIN (WINDOWS-1250)',
			'options' => array(
				new FormEngine\Option('UTF', 'UTF-8'),
				new FormEngine\Option('ISO', 'ISO-8859-2'),
				new FormEngine\Option('WIN', 'WINDOWS-1250'),
			)
		)));

		$settings = $this->registry->core->loadModuleSettings('eraty', Helper::getViewId());

		if (! empty($settings)){
			$populate = Array(
				'eraty_data' => Array(
					'numersklepu' => $settings['numersklepu'],
					'wariantsklepu' => $settings['wariantsklepu'],
					'typproduktu' => $settings['typproduktu'],
					'char' => $settings['char'],
					'idpaymentmethod' => ''
				)
			);

			$event->setReturnValues($populate);
		}
	}

	public function saveSettings ($request)
	{
		if ($request['model'] != $this->getName()){
			return false;
		}

		$Settings = Array(
			'numersklepu' => $request['data']['numersklepu'],
			'wariantsklepu' => $request['data']['wariantsklepu'],
			'typproduktu' => $request['data']['typproduktu'],
			'char' => $request['data']['char'],
		);

		$this->registry->core->saveModuleSettings('eraty', $Settings, Helper::getViewId());
	}

	public function getPaymentMethodIdForZagiel ()
	{
		$settings = $this->registry->core->loadModuleSettings('eraty', Helper::getViewId());
		return  $settings['settings'];
	}

	public function updateOrderEratyBackAccept ($idorder, $proposal)
	{
		$sql = "UPDATE `order`
					SET
						orderstatusid = (SELECT orderstatusid FROM orderstatustranslation WHERE name LIKE 'Żagiel [Zapisany]'),
						eratyproposal = :eratyproposal,
						eratyaccept = 1
					WHERE idorder = :idorder";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idorder', $idorder);
		$stmt->bindValue('eratyproposal', $proposal);
		try{
			$stmt->execute();
		}
		catch (Exception $fe){
			throw new FrontendException($this->trans('ERR_CHANGE_ORDER_STATUS_ERATY'));
		}
	}

	public function updateOrderEratyBackCancel ($idorder)
	{
		$sql = "UPDATE `order`
					SET
						orderstatusid = (SELECT orderstatusid FROM orderstatustranslation WHERE name LIKE 'Żagiel [Anulowany]'),
						eratycancel = 1
					WHERE idorder = :idorder";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idorder', $idorder);
		try{
			$stmt->execute();
		}
		catch (Exception $fe){
			throw new FrontendException($this->trans('ERR_CHANGE_ORDER_STATUS_ERATY'));
		}
	}

	public function checkOrderidAcceptLink ($idorder)
	{
		$sql = "SELECT O.idorder
					FROM `order` O
					WHERE O.idorder = :idorder
						AND O.eratyproposal IS NULL
						AND O.eratycancel IS NULL
						AND O.eratyaccept IS NULL
						AND O.paymentmethodid = (SELECT idpaymentmethod FROM paymentmethod WHERE controller = 'eraty')";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idorder', $idorder);
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$orderid = $rs['idorder'];
				return $orderid;
			}
			return 0;
		}
		catch (Exception $fe){
			throw new FrontendException($this->trans('ERR_CHECK_ERATY_LINK'));
		}
	}

	public function checkOrderidCanceledLink ($idorder)
	{
		$sql = "SELECT O.idorder
					FROM `order` O
					WHERE O.idorder = :idorder
						AND O.eratyproposal IS NULL
						AND O.eratycancel IS NULL
						AND O.eratyaccept IS NULL
						AND O.paymentmethodid = (SELECT idpaymentmethod FROM paymentmethod WHERE controller = 'eraty')";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idorder', $idorder);
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$orderid = $rs['idorder'];
				return $orderid;
			}
			return 0;
		}
		catch (Exception $fe){
			throw new FrontendException($this->trans('ERR_CHECK_ERATY_LINK'));
		}
	}

	public function checkEraty ($idpaymentmethod)
	{
		$sql = "SELECT
					ES.wariantsklepu,
					ES.numersklepu,
					ES.`char`
				FROM eratysettings ES
				LEFT JOIN paymentmethodview PV ON  ES.paymentmethodid  = PV.paymentmethodid
				WHERE PV.viewid = :viewid
				AND ES.paymentmethodid = :idpaymentmethod";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->bindValue('idpaymentmethod', $idpaymentmethod);
		$stmt->execute();
		$rs = $stmt->fetch();
		if ($rs){
			$Data = Array(
				'wariantsklepu' => $rs['wariantsklepu'],
				'numersklepu' => $rs['numersklepu'],
				'char' => $rs['char']
			);
			return $Data;
		}
		return 0;
	}

	public function getPaymentData ($Data)
	{
		$eraty = $this->checkEraty($Data['orderData']['payment']['idpaymentmethod']);
		return Array(
			'eraty' => $eraty
		);
	}

	public function confirmPayment ($Data, $params)
	{
		$idorder = $Data['orderId'];
		if (! empty($params)){
			$param = preg_split('/&/i', $params);
			if (isset($param[0]) && isset($param[1])){
				$idzamowienie = preg_replace('/[a-zA-Z_=]/i', '', $param[1]);
				$idwniosku = preg_replace('/[a-zA-Z_=]/i', '', $param[2]);
				if (is_numeric($idzamowienie) && ! empty($idwniosku) && ($idzamowienie == $idorder)){
					$idorder = App::getModel('eraty')->checkOrderidAcceptLink($idzamowienie);
					if ($idorder > 0){
						App::getModel('eraty')->updateOrderEratyBackAccept($idzamowienie, $idwniosku);
						$this->registry->template->assign('idorder', $idorder);
						$clientOrder = App::getModel('order')->getOrderInfoForEraty($idorder);
						if (! empty($clientOrder) && $idorder > 0){
							$this->registry->template->assign('clientOrder', $clientOrder);

							App::getModel('mailer')->sendEmail(Array(
								'template' => 'eratyAccept',
								'email' => Array(
									$clientOrder['email']
								),
								'bcc' => false,
								'subject' => $this->trans('TXT_ZAGIEL_PROPOSAL_ACCEPT'),
								'viewid' => Helper::getViewId()
							));

						}
					}
					else{
						$this->registry->template->assign('errLink', 1);
					}
				}
				else{
					$this->registry->template->assign('errLink', 1);
				}
			}
			else{
				$this->registry->template->assign('error', 1);
			}
		}
		else{
			$this->registry->template->assign('error', 1);
		}
	}

	public function cancelPayment ($Data, $params)
	{

		$idorder = $Data['orderId'];
		$param = $this->registry->core->getParam();
		if (! empty($param)){
			$idzamowienie = preg_replace('/[a-zA-Z_=&]/i', '', $param);
			if (is_numeric($idzamowienie) && ($idzamowienie == $idorder)){
				$order = App::getModel('eraty')->checkOrderidCanceledLink($idzamowienie);
				if (! empty($order) && $order > 0){
					App::getModel('eraty')->updateOrderEratyBackCancel($idzamowienie);
					$this->registry->template->assign('idorder', $order);
					$clientOrder = App::getModel('order')->getOrderInfoForEraty($order);
					if (! empty($clientOrder) && $order > 0){
						$this->registry->template->assign('clientOrder', $clientOrder);

						App::getModel('mailer')->sendEmail(Array(
							'template' => 'eratyCancel',
							'email' => Array(
								$clientOrder['email']
							),
							'bcc' => false,
							'subject' => $this->trans('TXT_ZAGIEL_PROPOSAL_CANCEL'),
							'viewid' => Helper::getViewId()
						));
					}
				}
				else{
					$this->registry->template->assign('errLink', 1);
				}
			}
			else{
				$this->registry->template->assign('error', 1);
			}
			// błędnie wprowadzony url
		}
		else{
			$this->registry->template->assign('error', 1);
		}
	}
}