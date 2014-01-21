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

namespace Gekosale;

class ShipmentModel extends Component\Model\Datagrid {

    const SHIPMENT_DEFAULT_WEIGHT = 0;
    const SHIPMENT_DEFAULT_WIDTH = 0;
    const SHIPMENT_DEFAULT_HEIGHT = 0;
    const SHIPMENT_DEFAULT_DEEP = 0;

    public function __construct($registry, $modelFile) {
        parent::__construct($registry, $modelFile);
    }

    protected function initDatagrid($datagrid) {
        $datagrid->setTableData('shipment', Array(
            'idshipment' => Array(
                'source' => 'I.idshipments'
            ),
            'orderid' => Array(
                'source' => 'I.orderid'
            ),
            'guid' => Array(
                'source' => 'I.guid'
            ),
            'packagenumber' => Array(
                'source' => 'I.packagenumber'
            ),
            'adddate' => Array(
                'source' => 'I.adddate'
            ),
            'model' => Array(
                'source' => 'I.model',
                'prepareForSelect' => true
            )
        ));

        $datagrid->setFrom('
			shipments I
		');

        $datagrid->setAdditionalWhere('
			I.model = :model
		');

        $datagrid->setSQLParams(Array(
            'model' => $this->registry->core->getParam()
        ));
    }

    public function getDatagridFilterData() {
        return $this->getDatagrid()->getFilterData();
    }

    public function getShipmentForAjax($request, $processFunction) {
        return $this->getDatagrid()->getData($request, $processFunction);
    }

    public function doAJAXDeleteShipment($id, $datagrid) {
        $this->deleteShipment($id);
        return $this->getDatagrid()->refresh($datagrid);
    }

    public function deleteShipment($id) {
        DbTracker::deleteRows('shipments', 'idshipments', $id);
    }

    public function getCountryName($id) {
        $sql = 'SELECT
					name
				FROM country WHERE idcountry = :id';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->execute();
        $rs = $stmt->fetch();
        return $rs['name'];
    }

    public function getPdfContentByGuid($guid) {
        $sql = 'SELECT
					label
				FROM shipments WHERE guid = :id';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindParam('id', $guid);
        $stmt->execute();
        $rs = $stmt->fetch();
        return base64_decode($rs['label']);
    }

    public function getGuid() {
        mt_srand((double) microtime() * 10000);
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $retval = substr($charid, 0, 32);
        return $retval;
    }

    public function parseNumber($number) {
        // dodamy parsowanie i weryfikowanie numeru
        $chars = array(
            '-',
            ',',
            ' ',
            '+'
        );
        $number = str_replace($chars, '', $number);
        $number = trim($number);
        return $number;
    }

    public function exportShipment($ids) {
        $sql = 'SELECT
					*
				FROM shipments WHERE idshipments IN (' . implode(',', $ids) . ')';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindParam('id', $guid);
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()) {
            $Data[$rs['model']][] = $rs['guid'];
        }
        
        foreach ($Data as $model => $references) {
            App::getModel('shipment/' . $model)->getProtocol(array_values($references));
        }
    }

}
