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
 * 
 * 
 * $Revision: 438 $
 * $Author: gekosale $
 * $Date: 2011-08-27 11:29:36 +0200 (So, 27 sie 2011) $
 * $Id: controller.class.php 438 2011-08-27 09:29:36Z gekosale $ 
 */
namespace Gekosale\Component\Controller;

class Frontend extends \Gekosale\Component\Controller
{
	public function getMetadata ()
	{
		return \Gekosale\App::getModel('seo')->getMetadataForPage();
	}

	public function Render ($layout, $action = 'index')
	{
		$layer = $this->registry->loader->getCurrentLayer();
		
		$layoutBoxes = \Gekosale\App::getModel('layoutgenerator');
		$layoutBoxes->LoadLayout($layout);
		$metadata = $this->getMetadata();
		if (! isset($metadata['additionalmeta'])){
			$viewMetaData = \Gekosale\App::getModel('seo')->getMetadataForPage();
			$additionalmeta = $viewMetaData['additionalmeta'];
		}
		else{
			$additionalmeta = $metadata['additionalmeta'];
		}
		$this->registry->template->assign('metadata', $metadata);
		$this->registry->template->assign('additionalmeta', $additionalmeta);
		$this->registry->template->assign('googleappstag', isset($layer['googleappstag']) ? $layer['googleappstag'] : '');
		$this->registry->template->assign('pagescheme', $layoutBoxes->GetTemplateData('content', $action));
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->template->display($this->loadTemplate('layout.tpl'));
	}

	public function getName ($fullName = NULL)
	{
		return ($fullName === NULL) ? strtolower(str_replace('Controller', '', get_class($this))) : get_class($this);
	}
}