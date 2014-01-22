<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2009-2011 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it through the
 * world-wide-web, please send an email to license@verison.pl so we can send you a copy immediately.
 *
 * $Revision: 309 $
 * $Author: gekosale $
 * $Date: 2011-08-01 21:10:16 +0200 (Pn, 01 sie 2011) $
 * $Id: invoice.php 309 2011-08-01 19:10:16Z gekosale $
 */
namespace Gekosale\Plugin;
use FormEngine;

class CodesModel extends Component\Model\Datagrid
{

    public function __construct ($registry, $modelFile)
    {
        parent::__construct($registry, $modelFile);
    }

    protected function initDatagrid ($datagrid)
    {
        $datagrid->setTableData('productcode', Array(
            'productid' => Array(
                'source' => 'P.idproduct'
            ),
            'name' => Array(
                'source' => 'PT.name'
            ),
            'ean' => Array(
                'source' => 'P.ean'
            ),
            'keystotal' => Array(
                'source' => 'COUNT(DISTINCT PC.idproductcode)'
            ),
            'keyssold' => Array(
                'source' => 'COUNT(DISTINCT OPC.code)'
            )
        ));
        
        $datagrid->setFrom('
			product P
			LEFT JOIN productcode PC ON PC.productid = P.idproduct
			LEFT JOIN orderproduct OP ON OP.productid = P.idproduct
			LEFT JOIN orderproductcode OPC ON OPC.orderproductid = OP.idorderproduct
			LEFT JOIN producttranslation PT ON PT.productid = P.idproduct AND PT.languageid = :languageid
		');
        
        $datagrid->setGroupBy('
			P.idproduct
		');
    }

    public function getDatagridFilterData ()
    {
        return $this->getDatagrid()->getFilterData();
    }

    public function getLicenceForAjax ($request, $processFunction)
    {
        return $this->getDatagrid()->getData($request, $processFunction);
    }

    public function doAJAXDeleteLicence ($id, $datagrid)
    {
        $this->deleteLicence($id);
        $this->refreshStock($id);
        return $this->getDatagrid()->refresh($datagrid);
    }

    public function deleteLicence ($id)
    {
        try{
            $sql = 'DELETE FROM productcode WHERE productid = :id';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('id', $id);
            $stmt->execute();
        }
        catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function getCodesForProduct ($id)
    {
        $sql = "SELECT
					code
				FROM productcode
				WHERE productid = :id";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $Data[] = $rs['code'];
        }
        return $Data;
    }

    public function addFields ($event, $request)
    {
        $form = &$request['form'];
        $codes = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'productcodes',
            'label' => 'Klucze licencyjne'
        )));
        
        $codes->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p>Wprowadź klucze licencyjne dotyczące tego produktu. Zawsze możesz zaimportować listę kluczy na stronie <a href="' . $this->registry->router->generate('admin', true, Array(
                'controller' => 'codes'
            )) . '" target="_blank">Katalog &raquo; Klucze licencyjne</a>.</p>'
        )));
        
        $keysData = $codes->AddChild(new FormEngine\Elements\FieldsetRepeatable(Array(
            'name' => 'productcode',
            'repeat_min' => 1,
            'repeat_max' => FormEngine\FE::INFINITE
        )));
        
        $keysData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'code'
        )));
        
        $populate = Array(
            'productcodes' => Array(
                'productcode' => Array(
                    'code' => $this->getCodesForProduct((int) $this->registry->core->getParam())
                )
            )
        );
        
        $event->setReturnValues($populate);
    }

    public function checkCode ($code)
    {
        $sql = 'SELECT count(idorderproductcode) AS total FROM orderproductcode WHERE code = :code';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('code', $code);
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs){
            return $rs['total'];
        }
        return 0;
    }

    public function importFromFile ($file)
    {
        if (($handle = fopen(ROOTPATH . 'upload' . DS . 'keys' . DS . $file, "r")) === FALSE)
            return;
        while (($cols = fgetcsv($handle, 1000, ";")) !== FALSE){
            if ($cols[0] != 'ean'){
                $Data[] = $cols;
            }
        }
        Db::getInstance()->beginTransaction();
        foreach ($Data as $key => $val){
            $sql = 'SELECT idproduct FROM product WHERE ean = :ean';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('ean', $val[0]);
            $stmt->execute();
            $rs = $stmt->fetch();
            if ($rs){
                $idproduct = $rs['idproduct'];
                $code = $val[1];
                if ($this->checkCode($code) == 0){
                    $sql = "INSERT INTO productcode SET
								productid = :id,
								code = :code
							ON DUPLICATE KEY UPDATE
								code = :code";
                    $stmt = Db::getInstance()->prepare($sql);
                    $stmt->bindValue('id', $idproduct);
                    $stmt->bindValue('code', $code);
                    try{
                        $stmt->execute();
                        $this->refreshStock($idproduct);
                    }
                    catch (Exception $e){
                        throw new CoreException($this->trans('ERR_LICENCE_KEY_ADD'), 4, $e->getMessage());
                    }
                }
            }
        }
        Db::getInstance()->commit();
    }

    public function refreshStock ($id)
    {
        $sql = "UPDATE product SET
					trackstock = 1,
					stock = (SELECT COUNT(idproductcode) FROM productcode WHERE productid = idproduct)
				WHERE product.idproduct = :id";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->execute();
    }

    public function saveSettings ($request)
    {
        $Data = $request['data'];
        
        if (isset($Data['code'])){
            $sql = "DELETE FROM productcode WHERE productid = :id";
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('id', $request['id']);
            $rs = $stmt->execute();
            
            foreach ($Data['code'] as $key => $code){
                if (strlen($code) > 1 && $this->checkCode($code) == 0){
                    $sql = "INSERT INTO productcode SET
							productid = :id,
							code = :code";
                    $stmt = Db::getInstance()->prepare($sql);
                    $stmt->bindValue('id', $request['id']);
                    $stmt->bindValue('code', $code);
                    $stmt->execute();
                    $this->refreshStock($request['id']);
                }
            }
        }
    }

    public function addLicence ($orderId)
    {
        /*
		 * Pobranie danych zamowienia
		 */
        $keys = Array();
        $sql1 = 'SELECT
					idorderproduct,
					name,
					qty,
					productid
				FROM orderproduct
				WHERE orderid = :orderid';
        $stmt1 = Db::getInstance()->prepare($sql1);
        $stmt1->bindValue('orderid', $orderId);
        $stmt1->execute();
        
        while ($rs1 = $stmt1->fetch()){
            $qty = $rs1['qty'];
            
            for ($i = 0; $i < $qty; $i ++){
                $sql2 = 'SELECT
							PC.idproductcode,
							OP.orderid,
							PC.code,
							OP.idorderproduct,
							AES_DECRYPT(OCD.email, :encryptionKey) AS email
						FROM orderproduct OP
						LEFT JOIN productcode PC ON PC.productid = OP.productid
						LEFT JOIN orderclientdata OCD ON OCD.orderid = OP.orderid
						WHERE OP.orderid = :orderid AND OP.idorderproduct = :idorderproduct
						LIMIT 1
				';
                $stmt2 = Db::getInstance()->prepare($sql2);
                $stmt2->bindValue('orderid', $orderId);
                $stmt2->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
                $stmt2->bindValue('idorderproduct', $rs1['idorderproduct']);
                
                try{
                    $stmt2->execute();
                    $rs2 = $stmt2->fetch();
                    if ($rs2){
                        if ($rs2['code'] != ''){
                            $sql3 = 'INSERT INTO orderproductcode SET
										orderid = :orderid,
										code = :code,
										orderproductid = :orderproductid';
                            $stmt3 = Db::getInstance()->prepare($sql3);
                            $stmt3->bindValue('orderid', $orderId);
                            $stmt3->bindValue('code', $rs2['code']);
                            $stmt3->bindValue('orderproductid', $rs2['idorderproduct']);
                            $stmt3->execute();
                            
                            $sql4 = 'DELETE FROM productcode WHERE idproductcode = :idproductcode';
                            $stmt4 = Db::getInstance()->prepare($sql4);
                            $stmt4->bindValue('idproductcode', $rs2['idproductcode']);
                            $stmt4->execute();
                            
                            $this->refreshStock($rs1['productid']);
                            
                            $keys[] = Array(
                                'name' => $rs1['name'],
                                'key' => $rs2['code']
                            );
                            
                            $email = $rs2['email'];
                        }
                    }
                }
                catch (Exception $e){
                    throw new Exception($e->getMessage());
                }
            }
        }
        
        $comment = 'Numery licencji:<br />';
        foreach ($keys as $k => $key){
            $comment .= '<strong>' . $key['name'] . '</strong>: ' . $key['key'] . '<br />';
        }
        
        $sql = 'INSERT INTO ordernotes (content, orderid)
				VALUES (:content, :orderid)';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('content', $comment);
        $stmt->bindValue('orderid', $orderId);
        try{
            $stmt->execute();
        }
        catch (Exception $e){
            throw new CoreException($this->trans('ERR_ORDER_NOTES_ADD'), 112, $e->getMessage());
        }
        
        $this->registry->template->assign('keys', $keys);
        
        App::getModel('mailer')->sendEmail(Array(
            'template' => 'codeSent',
            'email' => Array(
                $email
            ),
            'bcc' => false,
            'subject' => 'Klucze aktywacyjne do zamówienia: ' . $orderId,
            'viewid' => Helper::getViewId()
        ));
    }
}