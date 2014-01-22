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
 * $Id: pagescheme.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale\Plugin;

use PclZip;

class ExportModel extends Component\Model
{

	public function exportPagescheme ($id)
	{
		$this->id = $id;
		$this->pageScheme = App::getModel('pagescheme')->getTemplateNameToEdit($id);
		
		if (empty($this->pageScheme)){
			return FALSE;
		}
		
		$this->templateFolder = $this->pageScheme['templatefolder'];
		$pageschemeCss = App::getModel('pagescheme')->getTemplateCssToEdit($id);
		
		$Data = Array(
			'pagescheme' => $pageschemeCss,
			'layouts' => $this->getSubPageLayoutAll(),
			'boxes' => $this->getLayoutBox()
		);
		
		$filename = ROOTPATH . 'themes' . DS . $this->templateFolder . DS . 'settings' . DS . 'export.json';
		@file_put_contents($filename, json_encode($Data));
		$path = 'themes' . DS . $this->templateFolder . DS;
		
		$date = date('YmdHis');
		require_once (ROOTPATH . 'lib' . DS . 'zip' . DS . 'zip.php');
		$archive = new PclZip(ROOTPATH . 'themes' . DS . $this->templateFolder . '.zip');
		$list = $archive->create($path, PCLZIP_OPT_REMOVE_PATH, 'themes' . DS . $this->templateFolder . DS);
		return $this->templateFolder . '.zip';
	}

	public function getSubPageLayoutAll ()
	{
		$Data = Array();
		$sql = "SELECT
					DISTINCT(SL.idsubpagelayout) AS id,
					S.idsubpage,
					S.name AS name,
					COUNT(idsubpagelayoutcolumn) AS columns,
					COUNT(idsubpagelayoutcolumnbox) AS boxes
				FROM subpagelayout SL
				LEFT JOIN subpage S ON S.idsubpage = SL.subpageid
				LEFT JOIN subpagelayoutcolumn SLC ON SL.idsubpagelayout = SLC.subpagelayoutid
				LEFT JOIN subpagelayoutcolumnbox SLCB ON SLCB.subpagelayoutcolumnid = SLC.idsubpagelayoutcolumn
				WHERE SL.pageschemeid = :id
				GROUP BY S.idsubpage
				ORDER BY S.name
		";
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $this->id);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[$rs['idsubpage']] = Array(
				'name' => $rs['name'],
				'layout' => App::getModel('subpagelayout')->getSubPageLayoutColumn($rs['id'])
			);
		}
		return $Data;
	}

	public function getLayoutBox ()
	{
		$sql = "SELECT
					idlayoutbox,
					name,
					title,
					controller
				FROM layoutbox
				WHERE pageschemeid = :id";
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $this->id);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$layoutboxid = $rs['idlayoutbox'];
			$Data[$layoutboxid] = Array(
				'name' => $rs['name'],
				'title' => $rs['title'],
				'controller' => $rs['controller'],
				'params' => $this->getLayoutBoxSettings($layoutboxid),
				'behaviour' => $this->getLayoutBoxJS($layoutboxid),
				'css' => $this->getLayoutBoxCSS($layoutboxid)
			);
		}
		return $Data;
	}

	public function getLayoutBoxCSS ($id)
	{
		$sql = 'SELECT
					idlayoutboxcss,
					layoutboxid,
					selector,
					attribute
				FROM layoutboxcss
				WHERE layoutboxid = :id';
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[] = Array(
				'idlayoutboxschemecss' => $rs['idlayoutboxcss'],
				'selector' => $rs['selector'],
				'attribute' => $rs['attribute'],
				'value' => $this->getLayoutBoxCssValue($rs['idlayoutboxcss'])
			);
		}
		return $Data;
	}

	protected function getLayoutBoxCssValue ($id)
	{
		$sql = 'SELECT
					layoutboxid,
					layoutboxcssid,
					name,
					value,
					secondvalue
				FROM layoutboxcssvalue
				WHERE layoutboxcssid = :id
		';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();
		$Data = Array();
		while ($rs = $stmt->fetch()){
			if ($rs['secondvalue'] != NULL){
				$Data[$rs['name']][$rs['value']] = $rs['secondvalue'];
			}
			else{
				$Data[$rs['name']] = $rs['value'];
			}
		}
		return $Data;
	}

	public function getLayoutBoxSettings ($id)
	{
		$sql = "SELECT
					idlayoutboxcontentspecificvalue,
					variable,
					languageid,
					value
				FROM layoutboxcontentspecificvalue
				WHERE layoutboxid = :idlayoutbox";
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idlayoutbox', $id);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[$rs['variable']] = Array(
				'languageid' => $rs['languageid'],
				'value' => $rs['value']
			);
		}
		return $Data;
	}

	public function getLayoutBoxJS ($id)
	{
		$sql = "SELECT
					idlayoutboxjsvalue,
					variable,
					value
				FROM layoutboxjsvalue
				WHERE layoutboxid = :idlayoutbox";
		$Data = Array();
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('idlayoutbox', $id);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data[$rs['variable']] = $rs['value'];
		}
		return $Data;
	}
}