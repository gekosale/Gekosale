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
 * $Id: statsclients.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale\Plugin;

class StatsclientsModel extends Component\Model
{

	public function clientsGroupsChart ($request)
	{
		$Data = array();
		$sql = 'SELECT count(CD.clientid) as clients,CGT.name as groupsname  
				FROM clientdata CD
				LEFT JOIN clientgrouptranslation CGT ON CD.clientgroupid = CGT.clientgroupid AND CGT.languageid = :languageid 
				GROUP BY CGT.clientgroupid;';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('languageid', Helper::getLanguageId());
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data['values'][] = array(
				"value" => $rs['clients'],
				"label" => $rs['groupsname']
			);
		}
		$Data['colours'] = array(
			'#d01f3c',
			'#356aa0',
			'#C79810'
		);
		$Data['animate'][] = array(
			'type' => 'fade',
			'type' => 'bounce',
			'distance' => 4
		);
		$Data['oChartData']['bg_colour'] = "#ffffff";
		$Data['oChartData']['elements'][] = array(
			'type' => 'pie',
			'tip' => '#label#<br>#val# (#percent#)',
			'colours' => $Data['colours'],
			'gradient-fill' => true,
			'alpha' => 0.6,
			'border' => 2,
			'animate' => false,
			'start-angle' => 65,
			'values' => $Data['values']
		);
		$Data['oChartData']['title'] = array(
			'text' => ''
		);
		return $Data;
	}

	public function bestClientsChart ($request)
	{
		$Data = array();
		$Data['values'][] = array(
			'right' => 10
		);
		$Data['values'][] = array(
			'right' => 5
		);
		$Data['values'][] = array(
			'right' => 15
		);
		$Data['values'][] = array(
			'right' => 12
		);
		$Data['values'][] = array(
			'right' => 11
		);
		$Data['values'][] = array(
			'right' => 11
		);
		$Data['values'][] = array(
			'right' => 11
		);
		$Data['values'][] = array(
			'right' => 11
		);
		$Data['values'][] = array(
			'right' => 11
		);
		$Data['x_labels']['labels'] = array(
			"a",
			"b",
			"c",
			"d",
			"e",
			"f",
			"g",
			"h",
			"i",
			"j",
			"k",
			"l",
			"m",
			"n",
			"o",
			"p",
			"q",
			"r",
			"s",
			"t",
			"u",
			"v"
		);
		$Data['y_labels'] = array(
			"slashdot.org",
			"digg.com",
			"reddit.com",
			"reddit.com",
			"reddit.com",
			"reddit.com",
			"reddit.com",
			"reddit.com",
			"reddit.com"
		);
		$Data['oChartData']['title'] = array(
			'text' => 'Najlepsi klienci'
		);
		$Data['oChartData']['elements'][] = array(
			'type' => 'hbar',
			'tip' => '#val#<br>L:#left#, R:#right#',
			'text' => 'Klient',
			'colour' => '#000000',
			'values' => $Data['values']
		);
		$Data['oChartData']['x_axis'] = array(
			'min' => 0,
			'max' => 20,
			'offset' => false,
			'labels' => $Data['x_labels']
		);
		$Data['oChartData']['y_axis'] = array(
			'offset' => true,
			'labels' => $Data['y_labels']
		);
		$Data['oChartData']['tooltip'] = array(
			'mouse' => 1
		);
		return $Data;
	}

	public function getSummaryStats ()
	{
		$Data = Array();
		$period = date("Ym");
		$sql = 'SELECT COUNT(idclient) as clients
				FROM `client`
				WHERE IF(:viewid = 0, 1, viewid = :viewid) AND DATE_FORMAT(adddate,\'%Y-%m-%d\') = CURDATE()';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data['day'] = Array(
				'dayclients' => (int) $rs['clients']
			);
		}
		$sql = 'SELECT COUNT(idclient) as clients
				FROM `client`
				WHERE IF(:viewid =0,1,viewid = :viewid) AND DATE_FORMAT(adddate,\'%Y%m\') = :period';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('period', $period);
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data['month'] = Array(
				'monthclients' => (int) $rs['clients']
			);
		}
		$sql = 'SELECT COUNT(idclient) as clients
					FROM `client`
					WHERE IF(:viewid =0,1,viewid = :viewid) AND year(adddate) = :period';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('period', date("Y"));
		$stmt->bindValue('viewid', Helper::getViewId());
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data['year'] = Array(
				'yearclients' => (int) $rs['clients']
			);
		}
		$sql = 'SELECT COUNT(idclient) as totalclients FROM `client`';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->execute();
		while ($rs = $stmt->fetch()){
			$Data['total'] = Array(
				'totalclients' => (int) $rs['totalclients']
			);
		}
		return $Data;
	}
}