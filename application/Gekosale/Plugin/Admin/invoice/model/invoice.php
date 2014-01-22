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
 * $Revision: 612 $
 * $Author: gekosale $
 * $Date: 2011-11-28 21:02:10 +0100 (Pn, 28 lis 2011) $
 * $Id: invoice.php 612 2011-11-28 20:02:10Z gekosale $ 
 */
namespace Gekosale\Plugin;

class InvoiceModel extends Component\Model\Datagrid
{

	public function __construct ($registry, $modelFile)
	{
		parent::__construct($registry, $modelFile);
	}

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('invoice', Array(
			'idinvoice' => Array(
				'source' => 'I.idinvoice'
			),
			'symbol' => Array(
				'source' => 'I.symbol'
			),
			'invoicedate' => Array(
				'source' => 'I.invoicedate'
			),
			'orderid' => Array(
				'source' => 'I.orderid'
			)
		));
		
		$datagrid->setFrom('
			invoice I
		');
		
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getInvoiceForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function doAJAXDeleteInvoice ($id, $datagrid)
	{
		$this->deleteInvoice($id);
		return $this->getDatagrid()->refresh($datagrid);
	}

	public function deleteInvoice ($id)
	{
		DbTracker::deleteRows('invoice', 'idinvoice', $id);
	}

	public function generateInvoiceNumber ($invoicenumerationkind, $invoiceType, $orderDate, $viewId)
	{
		$sql = 'SELECT 
					COUNT(idinvoice) + 1 AS nextnumber
				FROM invoice
				WHERE 
					invoicetype = :invoicetype AND 
					YEAR(invoicedate) = YEAR(:invoicedate) AND
					viewid = :viewid';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('invoicetype', $invoiceType);
		$stmt->bindValue('invoicedate', $orderDate);
		$stmt->bindValue('viewid', $viewId);
		$stmt->execute();
		$rs = $stmt->fetch();
		$Data = Array();
		if ($rs){
			$nextNumber = $rs['nextnumber'];
		}
		switch ($invoiceType) {
			case 1:
				$invoiceTypeName = $this->trans('TXT_INVOICE_TYPE_PRO');
				break;
			case 2:
				$invoiceTypeName = $this->trans('TXT_INVOICE_TYPE_VAT');
				break;
			case 3:
				$invoiceTypeName = $this->trans('TXT_INVOICE_TYPE_COR');
				break;
		}
		
		$numerationDateMonth = date('m', strtotime($orderDate));
		$numerationDateYear = date('Y', strtotime($orderDate));
		
		switch ($invoicenumerationkind) {
			case 'ntmr':
				$invoiceNumber = Array(
					$nextNumber,
					$invoiceTypeName,
					$numerationDateMonth,
					$numerationDateYear
				);
				break;
			case 'trmn':
				$invoiceNumber = Array(
					$invoiceTypeName,
					$numerationDateYear,
					$numerationDateMonth,
					$nextNumber
				);
				break;
			case 'tmnr':
				$invoiceNumber = Array(
					$invoiceTypeName,
					$numerationDateMonth,
					$nextNumber,
					$numerationDateYear
				);
				break;
			case 'tnr':
				$invoiceNumber = Array(
					$invoiceTypeName,
					$nextNumber,
					$numerationDateYear
				);
				break;
			case 'trn':
				$invoiceNumber = Array(
					$invoiceTypeName,
					$numerationDateYear,
					$nextNumber
				);
				break;
			case 'rnt':
				$invoiceNumber = Array(
					$numerationDateYear,
					$nextNumber,
					$invoiceTypeName
				);
				break;
			case 'rtn':
				$invoiceNumber = Array(
					$numerationDateYear,
					$invoiceTypeName,
					$nextNumber
				);
				break;
		}
		return implode(' / ', $invoiceNumber);
	}

	public function getInvoiceNumberFormat ($date)
	{
		$orderData = App::getModel('order')->getOrderById((int) $this->registry->core->getParam());
		$viewData = App::getModel('view')->getView($orderData['viewid']);
		$invoiceType = (int) $this->registry->core->getParam(1);
		$invoiceNumber = $this->generateInvoiceNumber($viewData['invoicenumerationkind'], $invoiceType, $date, $orderData['viewid']);
		return $invoiceNumber;
	}

	public function addInvoice ($Data, $orderId, $invoiceTypeId, $orderData)
	{
		$content = '';
		$fileHandler = '';
		switch ($invoiceTypeId) {
			case 1:
				$file = 'pro.tpl';
				$invoiceTypeName = $this->trans('TXT_INVOICE_TYPE_PRO');
				break;
			case 2:
				$file = 'vat.tpl';
				$invoiceTypeName = $this->trans('TXT_INVOICE_TYPE_VAT');
				break;
			case 3:
				$file = 'cor.tpl';
				$invoiceTypeName = $this->trans('TXT_INVOICE_TYPE_COR');
				break;
		}
		
		$theme = $this->registry->loader->getParam('theme');
		$fh = ($theme !== '') ? $theme . '/templates/invoice/' . $file : 'invoice/' . $file;
		
		$lp = 1;
		foreach ($orderData['products'] as $key => $val){
			$orderData['products'][$key]['lp'] = $lp;
			$orderData['products'][$key]['net_price'] = sprintf('%01.2f', $orderData['products'][$key]['net_price']);
			$orderData['products'][$key]['subtotal'] = sprintf('%01.2f', $orderData['products'][$key]['subtotal']);
			$orderData['products'][$key]['net_subtotal'] = sprintf('%01.2f', $orderData['products'][$key]['net_subtotal']);
			$lp ++;
		}
		if ($orderData['pricebeforepromotion'] > 0 && ($orderData['pricebeforepromotion'] < $orderData['total'])){
			$rulesCostGross = $orderData['total'] - $orderData['pricebeforepromotion'];
			$rulesCostNet = ($orderData['total'] - $orderData['pricebeforepromotion']) / (1 + ($orderData['delivery_method']['deliverervat'] / 100));
			$rulesVat = $rulesCostGross - $rulesCostNet;
			$orderData['products'][] = Array(
				'name' => $orderData['delivery_method']['deliverername'],
				'net_price' => sprintf('%01.2f', $orderData['delivery_method']['delivererpricenetto'] + $rulesCostNet),
				'quantity' => 1,
				'net_subtotal' => sprintf('%01.2f', $orderData['delivery_method']['delivererpricenetto'] + $rulesCostNet),
				'vat' => sprintf('%01.2f', $orderData['delivery_method']['deliverervat']),
				'vat_value' => sprintf('%01.2f', $orderData['delivery_method']['deliverervatvalue'] + $rulesVat),
				'subtotal' => sprintf('%01.2f', $orderData['delivery_method']['delivererprice'] + $rulesCostGross),
				'lp' => $lp
			);
		}
		else{
			$orderData['products'][] = Array(
				'name' => $orderData['delivery_method']['deliverername'],
				'net_price' => sprintf('%01.2f', $orderData['delivery_method']['delivererpricenetto']),
				'quantity' => 1,
				'net_subtotal' => sprintf('%01.2f', $orderData['delivery_method']['delivererpricenetto']),
				'vat' => sprintf('%01.2f', $orderData['delivery_method']['deliverervat']),
				'vat_value' => sprintf('%01.2f', $orderData['delivery_method']['deliverervatvalue']),
				'subtotal' => sprintf('%01.2f', $orderData['delivery_method']['delivererprice']),
				'lp' => $lp
			);
		}
		
		$orderData['order_date'] = date('Y-m-d', strtotime($orderData['order_date']));
		$Data['invoiceTypeName'] = $invoiceTypeName;
		$Data['symbol'] = $Data['invoicenumber'];
		$allpricebrutto = sprintf('%01.2f', $orderData['total']);
		$stringAllPriceBrutto = (string) $allpricebrutto;
		$explodePrice = explode('.', $stringAllPriceBrutto);
		$zl = $explodePrice[0];
		if (isset($explodePrice[1]) && $explodePrice[1] != NULL){
			$gr = $explodePrice[1];
		}
		else{
			$gr = '0';
		}
		$InWordsZl = Amountinwords::slownie($zl);
		$amountInWords = $InWordsZl . ' ' . $orderData['currencysymbol'] . ' ' . (int) $explodePrice[1] . '/100';
		
		$slogan = $this->checkLogoShopNameTag($orderData['viewid']);
		if ($slogan['isinvoiceshopslogan'] == 1){
			$this->registry->template->assign('invoiceshopslogan', $slogan['invoiceshopslogan']);
		}
		else{
			$this->registry->template->assign('invoiceshopslogan', '');
		}
		$summary = $this->getOrderSummary($orderId);
		$bDelivererVatExists = false;
		foreach ($summary as $key => $group){
			if ($group['vat'] == $orderData['delivery_method']['deliverervat']){
				$summary[$key]['netto'] = $group['netto'] + $orderData['delivery_method']['delivererpricenetto'];
				$summary[$key]['brutto'] = $group['brutto'] + $orderData['delivery_method']['delivererprice'];
				$summary[$key]['vatvalue'] = $group['vatvalue'] + $orderData['delivery_method']['deliverervatvalue'];
				$bDelivererVatExists = true;
				break;
			}
		}
		if ($bDelivererVatExists == false){
			$summary[] = Array(
				'vat' => $orderData['delivery_method']['deliverervat'],
				'netto' => $orderData['delivery_method']['delivererpricenetto'],
				'brutto' => $orderData['delivery_method']['delivererprice'],
				'vatvalue' => $orderData['delivery_method']['deliverervatvalue']
			);
		}
		$companyaddress = $this->getMainCompanyAddress($orderData['viewid']);
		$Total = Array(
			'netto' => 0,
			'brutto' => 0,
			'vatvalue' => 0
		);
		foreach ($summary as $key => $group){
			$Total['netto'] += $group['netto'];
			$Total['brutto'] += $group['brutto'];
			$Total['vatvalue'] += $group['vatvalue'];
		}
		
		$this->registry->template->assign('invoiceData', $Data);
		$this->registry->template->assign('order', $orderData);
		$this->registry->template->assign('comment', $Data['comment']);
		$this->registry->template->assign('amountInWords', $amountInWords);
		$this->registry->template->assign('companyaddress', $companyaddress);
		$this->registry->template->assign('summary', $summary);
		$this->registry->template->assign('total', $Total);
		$this->registry->template->assign('originalCopy', $this->trans('TXT_ORIGINAL'));
		$contentOriginalHtml = $this->registry->template->fetch($fh);
		
		$this->registry->template->assign('invoiceData', $Data);
		$this->registry->template->assign('order', $orderData);
		$this->registry->template->assign('amountPayed', $Data['totalpayed']);
		$this->registry->template->assign('amountToPay', $orderData['total'] - $Data['totalpayed']);
		$this->registry->template->assign('amountInWords', $amountInWords);
		$this->registry->template->assign('companyaddress', $companyaddress);
		$this->registry->template->assign('summary', $summary);
		$this->registry->template->assign('total', $Total);
		$this->registry->template->assign('originalCopy', $this->trans('TXT_COPY'));
		$contentCopyHtml = $this->registry->template->fetch($fh);
		
		$sql = "INSERT INTO invoice SET
					symbol = :symbol,
					invoicedate = :invoicedate,
					salesdate = :salesdate,
					paymentduedate = :paymentduedate,
					salesperson = :salesperson,
					invoicetype = :invoicetype,
					comment = :comment,
					contentoriginal = :contentoriginal,
					contentcopy = :contentcopy,
					orderid = :orderid,
					totalpayed = :totalpayed,
					viewid = :viewid";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('symbol', $Data['invoicenumber']);
		$stmt->bindValue('invoicedate', $Data['invoicedate']);
		$stmt->bindValue('salesdate', $orderData['order_date']);
		$stmt->bindValue('paymentduedate', $Data['duedate']);
		$stmt->bindValue('salesperson', $Data['salesperson']);
		$stmt->bindValue('invoicetype', $invoiceTypeId);
		$stmt->bindValue('comment', $Data['comment']);
		$stmt->bindValue('contentoriginal', $contentOriginalHtml);
		$stmt->bindValue('contentcopy', $contentCopyHtml);
		$stmt->bindValue('orderid', $orderId);
		$stmt->bindValue('totalpayed', $Data['totalpayed']);
		$stmt->bindValue('viewid', $orderData['viewid']);
		try{
			$stmt->execute();
		}
		catch (Exception $e){
			throw new Exception($e->getMessage());
		}
		
		return $Data['invoicenumber'];
	}

	public function getMainCompanyAddress ($viewid)
	{
		$sql = "SELECT
					S.companyname AS shopname, 
					S.postcode, 
					S.street, 
					S.streetno, 
					S.placeno, 
					S.placename, 
					S.province, 
					S.nip,
					S.bankname, 
					S.banknr
				FROM store S
				LEFT JOIN view V ON V.storeid = S.idstore
				WHERE V.idview = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $viewid);
		
		$Data = Array();
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$Data = Array(
					'shopname' => $rs['shopname'],
					'postcode' => $rs['postcode'],
					'street' => $rs['street'],
					'streetno' => $rs['streetno'],
					'placeno' => $rs['placeno'],
					'placeno' => $rs['placeno'],
					'placename' => $rs['placename'],
					'province' => $rs['province'],
					'nip' => $rs['nip'],
					'bankname' => $rs['bankname'],
					'banknr' => $rs['banknr']
				);
			}
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_GET_COMPANYADDRESS'));
		}
		return $Data;
	}

	public function getOrderSummary ($idorder)
	{
		$sql = "SELECT 
					ROUND(OP.vat,2) AS vat,
					ROUND(SUM(OP.pricenetto * OP.qty) * (1 + (OP.vat / 100)),2) as brutto,
            		ROUND(SUM(OP.pricenetto * OP.qty),2) as netto
				FROM `order` O
				LEFT JOIN orderclientdata OCD ON OCD.orderid=O.idorder
				LEFT JOIN orderproduct OP ON OP.orderid=O.idorder
				WHERE idorder=:idorder
				GROUP BY OP.vat";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idorder', $idorder);
		$Data = Array();
		try{
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				$Data[] = Array(
					'vat' => $rs['vat'],
					'netto' => $rs['netto'],
					'brutto' => $rs['brutto'],
					'vatvalue' => ($rs['brutto'] - $rs['netto'])
				);
			}
		}
		catch (Exception $e){
			throw new CoreException($this->trans('ERR_GET_COMPANYADDRESS'));
		}
		return $Data;
	}

	public function checkLogoShopNameTag ($viewid)
	{
		$sql = "SELECT
					S.shortcompanyname as shopname, 
					S.invoicephotoid, 
					S.invoiceshopslogan, 
					S.isinvoiceshopslogan, 
					S.isinvoiceshopname
				FROM store S 
				LEFT JOIN view V ON V.storeid = S.idstore
				WHERE V.idview = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $viewid);
		$Data = Array();
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				$Data = Array(
					'shopname' => $rs['shopname'],
					'invoicephotoid' => $rs['invoicephotoid'],
					'isinvoiceshopslogan' => $rs['isinvoiceshopslogan'],
					'isinvoiceshopname' => $rs['isinvoiceshopname'],
					'invoiceshopslogan' => $rs['invoiceshopslogan']
				);
			}
		}
		catch (Exception $e){
			throw new CoreException($this->trans('Error while doing sql query to invoice'));
		}
		return $Data;
	}

	public function getInvoiceById ($id, $type)
	{
		$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8');
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Gekosale');
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
		$pdf->setHeaderFont(Array(
			PDF_FONT_NAME_MAIN,
			'',
			PDF_FONT_SIZE_MAIN
		));
		$pdf->setFooterFont(Array(
			PDF_FONT_NAME_DATA,
			'',
			PDF_FONT_SIZE_DATA
		));
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->setLanguageArray(1);
		$pdf->SetFont('dejavusans', '', 10);
		
		$sql = 'SELECT 
					*
				FROM invoice
				WHERE 
					idinvoice = :id';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$rs = $stmt->fetch();
		
		$Data = Array();
		if ($rs){
			
			switch ($rs['contenttype']) {
				case 'html':
					if ($type == 0){
						$data = $rs['contentoriginal'];
					}
					else{
						$data = $rs['contentcopy'];
					}
					$pdf->AddPage();
					$pdf->writeHTML($data, true, 0, true, 0);
					ob_clean();
					$pdf->Output($rs['symbol'], 'D');
					break;
				case 'pdf':
					if ($type == 0){
						$data = base64_decode($rs['contentoriginal']);
					}
					else{
						$data = base64_decode($rs['contentcopy']);
					}
					header('Content-Type: application/pdf');
					header('Content-Description: File Transfer');
					header('Content-Transfer-Encoding: binary');
					header('Content-Disposition: attachment; filename="' . $rs['symbol'] . '.pdf"');
					header('Content-Length: ' . strlen($data));
					header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0');
					header('Expires: 0');
					echo $data;
					exit();
					break;
			}
		}
	}

	public function exportInvoice ($ids)
	{
		$pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8');
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Gekosale');
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
		$pdf->setHeaderFont(Array(
			PDF_FONT_NAME_MAIN,
			'',
			PDF_FONT_SIZE_MAIN
		));
		$pdf->setFooterFont(Array(
			PDF_FONT_NAME_DATA,
			'',
			PDF_FONT_SIZE_DATA
		));
		
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->setLanguageArray(1);
		$pdf->SetFont('dejavusans', '', 10);
		
		$sql = 'SELECT 
					*
				FROM invoice
				WHERE 
					idinvoice IN (' . implode(',', $ids) . ')';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			
			switch ($rs['contenttype']) {
				case 'html':
					$data = $rs['contentcopy'];
					$pdf->AddPage();
					$pdf->writeHTML($data, true, 0, true, 0);
					break;
			}
		}
		ob_clean();
		$pdf->Output('Faktury z ' . date('Y-m-d'), 'D');
	}

	public function getInvoiceNumerationTypes ()
	{
		return Array(
			'ntmr' => 'numer faktury w roku / typ faktury / miesiąc / rok',
			'trmn' => 'typ faktury / rok / miesiąc / numer faktury w roku',
			'tmnr' => 'typ faktury / miesiąc / numer faktury w roku / rok',
			'tnr' => 'typ faktury / numer faktury w roku / rok',
			'trn' => 'typ faktury / rok / numer faktury w roku',
			'rnt' => 'rok / numer faktury w roku / typ faktury',
			'rtn' => 'rok / typ faktury / numer faktury w roku'
		);
	}
}