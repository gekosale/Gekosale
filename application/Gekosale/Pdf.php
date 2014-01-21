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
 * $Id: pdf.class.php 438 2011-08-27 09:29:36Z gekosale $ 
 */
namespace Gekosale;

require_once (ROOTPATH . 'lib/tcpdf/config/lang/eng.php');
require_once (ROOTPATH . 'lib/tcpdf/tcpdf.php');
use TCPDF;

class Pdf extends TCPDF
{
	
	protected $InvoiceTitle = "";
	protected $registry;
	protected $footerMessage;
	
	const PAGE_ORIENTATION = 'P';
	const UNIT = 'mm';
	const PAGE_FORMAT = 'A4';
// 	const BACKGROUND = 'design/_images_common/invoice-background.png';

	public function __construct ()
	{
		parent::__construct(self::PAGE_ORIENTATION, self::UNIT, self::PAGE_FORMAT, true);
		$this->footerMessage = '';
	}

	public function SetInvoiceTitle ($title)
	{
		$this->InvoiceTitle = $title;
	}

	public function Header ()
	{
		$this->DisplayBackground();
		$this->SetFont('dejavusans', 'B', 10);
		$this->Cell(30);
		$this->Cell(25, 20, $this->InvoiceTitle, 0, 0, 'C');
		$this->Ln(15);
	
	}

	public function SetFooterMessage ($message)
	{
		$this->footerMessage = $message;
	}

	protected function DisplayBackground ()
	{
// 		$this->Image(self::BACKGROUND, 0, 0, 210, 0, 'png');
	}

	public function Footer ()
	{
		$this->SetFont('dejavusans', '', 6);
		if (strlen($this->footerMessage)){
			$this->SetTextColor(201);
			$this->SetXY(10, - 15);
			$this->Cell(160, 0, $this->footerMessage, 0, 1, 'L');
		}
		$this->SetTextColor(0);
		$this->SetXY((strlen($this->footerMessage) ? - 55 : 10), - 15);
		$this->Cell((strlen($this->footerMessage) ? 60 : 0), 0, _('TXT_PDF_PAGE_NUMBER')." {$this->getAliasNumPage()} "._('TXT_PDF_PAGE_OUT_OF')." {$this->getAliasNbPages()}", 0, 1, (strlen($this->footerMessage) ? 'R' : 'C'));
	}

	protected function UTF8ToLatin1 ($str)
	{
		if (! $this->isunicode){
			return $str;
		}
		$return = iconv("UTF-8", "ISO-8859-2", $str);
		return $return;
	}

	public function CheckForPageBreak ($h, $y, $addpage = true)
	{
		if ($y + $h > 270){
			$this->AddPage();
			return true;
		}
		return false;
	}

	public function WriteTableRow ($columns, $x, $y)
	{
		$this->SetDrawColor(192, 192, 192);
		$yAfterRow = $y;
		$width = 0;
		$this->setXY($x, $y);
		$columnsCount = count($columns);
		if (! $columnsCount){
			return $y;
		}
		foreach ($columns as $i => $column){
			$this->SetXY($x + $width, $y);
			$this->MultiCell($column['width'], 4, $column['content'], 0, $column['align'], 0, 2);
			$width += $column['width'];
			$yAfterRow = max($yAfterRow, $this->GetY());
		}
		$this->Rect($x, $y, $width, $yAfterRow - $y);
		$xOffset = $x + $columns[0]['width'];
		for ($i = 1; $i < $columnsCount; $i ++){
			$this->Line($xOffset, $y, $xOffset, $yAfterRow);
			$xOffset += $columns[$i]['width'];
		}
		return $yAfterRow;
	}

}