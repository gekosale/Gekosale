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
 * $Id: modelwithdataset.class.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale\Core\Component\Model;

abstract class Dataset extends \Gekosale\Component\Model
{
	
	protected $dataset;

	public function __construct ($registry, $modelFile)
	{
		parent::__construct($registry, $modelFile);
		$this->dataset = NULL;
	}

	public function getDataset ()
	{
		if (($this->dataset == NULL)){
			$this->dataset = \Gekosale\App::getModel(get_class($this) . '/dataset');
			$this->initDataset($this->dataset);
		}
		return $this->dataset;
	}

	abstract protected function initDataset ($dataset);
}