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
 * $Id: mostsearch.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale\Plugin;

class MostSearchModel extends Component\Model\Datagrid
{

	protected function initDatagrid ($datagrid)
	{
		$datagrid->setTableData('mostsearch', Array(
			'idmostsearch' => Array(
				'source' => 'idmostsearch'
			),
			'name' => Array(
				'source' => 'name',
				'prepareForAutosuggest' => true
			),
			'textcount' => Array(
				'source' => 'textcount'
			)
		));
		$datagrid->setFrom('
				mostsearch
		');
		
		$datagrid->setGroupBy('
			name
		');
		
		$datagrid->setAdditionalWhere('
			IF(:viewid IS NULL, 1, viewid = :viewid)
		');
	}

	public function getDatagridFilterData ()
	{
		return $this->getDatagrid()->getFilterData();
	}

	public function getMostSearchForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getData($request, $processFunction);
	}

	public function getNameForAjax ($request, $processFunction)
	{
		return $this->getDatagrid()->getFilterSuggestions('name', $request, $processFunction);
	}

	public function doAJAXDeleteMostSearch ($id, $datagrid)
	{
		return $this->getDatagrid()->deleteRow($id, $datagrid, Array(
			$this,
			'deleteMostSearch'
		), $this->getName());
	}

	public function deleteMostSearch ($id)
	{
		DbTracker::deleteRows('mostsearch', 'idmostsearch', $id);
	}
}