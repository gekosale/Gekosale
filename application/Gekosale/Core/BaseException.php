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
 * $Id: baseexception.class.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale\Core;
use Exception;

abstract class BaseException extends Exception
{
	
	protected $fh;
	protected $dh;
	protected $directory = 'logs/';
	protected $errorFile = 'error.log';
	protected $path;
	protected $fileSizeLimit = 100000;
	protected $msgToLogFile;
	protected $errorDesignPath;
	protected $errorText;

	public function __construct ($message, $code = 0, $messageToLogFile = NULL)
	{
		$this->directory = ROOTPATH . $this->directory;
		$this->path = $this->directory . $this->errorFile;
		$this->msgToLogFile = $messageToLogFile;
		parent::__construct($message, $code);
		$this->dh = opendir($this->directory);
		if (! ($this->fh = @fopen($this->path, 'a'))){
			throw new Exception('Can\'t open log file.');
		}
		if (sprintf("%u", filesize($this->path)) >= $this->fileSizeLimit){
			$this->gzipCompress();
		}
		$this->saveError();
	}

	protected function saveError ()
	{
		$User = Array();
		$errorText = '';
		$message = '';
		if (App::getContainer()->get('session')->getActiveUserid() != NULL){
			$User['id'] = App::getContainer()->get('session')->getActiveUserid();
			$User['firstname'] = App::getContainer()->get('session')->getActiveUserFirstname();
			$User['surname'] = App::getContainer()->get('session')->getActiveUserSurname();
			$User['email'] = App::getContainer()->get('session')->getActiveUserEmail();
		}
		if (count($User) > 0){
			$errorText = '>>> User Info: ' . "\n" . 'SystemId: ' . $User['id'] . "\n" . 'Name: ' . $User['firstname'] . ' ' . $User['surname'] . "\n" . 'E-mail: ' . $User['email'] . " <<<\n";
		}
		$errorText .= 'Date: ' . date('Y-m-d H:i:s') . "\n" . 'File: ' . $this->getFile() . ' in line: ' . $this->getLine() . "\n" . 'User error message: ' . $this->getMessage() . "\n" . 'Orginal error message: ' . $this->msgToLogFile . "\n" . 'Trace: ' . "\n" . $this->getTraceAsString() . "\n" . '----------------------------------------END' . "\n";
		fwrite($this->fh, $errorText);
		$this->errorText = $errorText;
	}

	protected function gzipCompress ()
	{
		$fileNum = Array();
		$max = 0;
		while (($file = readdir($this->dh)) !== false){
			preg_match('/^error.log.(?<num>[0-9]*).gz$/', $file, $matches);
			if (isset($matches['num'])){
				$fileNum[] = $matches['num'];
			}
		}
		if (count($fileNum) > 0)
			$max = max($fileNum);
		file_put_contents($this->directory . $this->errorFile . '.' . ++ $max . '.gz', gzencode(file_get_contents($this->path), 9));
		file_put_contents($this->path, '');
	}
}