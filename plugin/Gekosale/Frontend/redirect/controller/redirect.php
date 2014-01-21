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
 * $Id: redirect.php 438 2011-08-27 09:29:36Z gekosale $
 */
namespace Gekosale;

class RedirectController extends Component\Controller\Frontend
{

	public function index ()
	{
		$url = $this->getParam();
		if (preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url) == true){
			header('Location: ' . $url);
		}
		else{
			$url = 'http://' . $url;
			header('Location: ' . $url);
		}
	}

	public function view ()
	{
		$sql = "SELECT 
					F.name AS filename,
					FE.name AS fileextension
				FROM file F
				LEFT JOIN fileextension FE ON F.fileextensionid = FE.idfileextension
				WHERE F.idfile = :id";
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('id', (int) $this->registry->core->getParam());
		try{
			$stmt->execute();
			$rs = $stmt->fetch();
			if ($rs){
				switch ($rs['fileextension']) {
					case "pdf":
						$ctype = "application/pdf";
						break;
					case "zip":
						$ctype = "application/zip";
						break;
					case "doc":
						$ctype = "application/msword";
						break;
					case "xls":
						$ctype = "application/vnd.ms-excel";
						break;
					default:
						$ctype = "application/force-download";
				}
				$fullPath = ROOTPATH . 'design' . DS . '_virtualproduct' . DS . (int) $this->registry->core->getParam() . '.' . $rs['fileextension'];
				if (is_file($fullPath)){
					$fsize = filesize($fullPath);
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Cache-Control: private", false);
					header("Content-Type: $ctype");
					header("Content-Disposition: attachment; filename=\"" . $rs['filename'] . '.' . $rs['fileextension'] . "\";");
					header("Content-Transfer-Encoding: binary");
					header("Content-Length: " . $fsize);
					readfile($fullPath);
				}
			}
		}
		catch (Exception $e){
			throw new FrontendException('Error while doing sql query.', 11, $e->getMessage());
		}
	}
}
?>