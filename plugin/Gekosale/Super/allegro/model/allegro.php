<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2009-2011 Gekosale
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms
 * of the GNU General Public License Version 3, 29 June 2007 as published by the
 * Free Software
 * Foundation (http://opensource.org/licenses/gpl-3.0.html).
 * If you did not receive a copy of the license and are unable to obtain it
 * through the
 * world-wide-web, please send an email to license@verison.pl so we can send you
 * a copy immediately.
 *
 * $Revision: 309 $
 * $Author: gekosale $
 * $Date: 2011-08-01 21:10:16 +0200 (Pn, 01 sie 2011) $
 * $Id: invoice.php 309 2011-08-01 19:10:16Z gekosale $
 */
namespace Gekosale;
use FormEngine;
use xajaxResponse;
use stdClass;
use DateTime;
use Monolog\Logger;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;

class AllegroModel extends Component\Model\Datagrid
{
    public function initDatagrid ($datagrid)
    {
        $datagrid->setTableData('clientdata', Array(
            'idauction' => Array(
                'source' => 'A.idauction'
            ),
            'itemid' => Array(
                'source' => 'A.itemid'
            ),
            'idproduct' => Array(
                'source' => 'A.idproduct'
            ),
            'title' => Array(
                'source' => 'A.title'
            ),
            'quantity' => Array(
                'source' => 'A.quantity'
            ),
            'minprice' => Array(
                'source' => 'A.minprice'
            ),
            'buyprice' => Array(
                'source' => 'A.buyprice'
            ),
            'startprice' => Array(
                'source' => 'A.startprice'
            ),
            'startdate' => Array(
                'source' => 'A.startdate'
            ),
            'enddate' => Array(
                'source' => 'A.enddate'
            ),
            'status' => Array(
                'source' => 'A.status'
            )
        ));
        $datagrid->setFrom('
			auction A
		');
    }

    public function getDatagridFilterData ()
    {
        return $this->getDatagrid()->getFilterData();
    }

    public function getAuctionForAjax ($request, $processFunction)
    {
        return $this->getDatagrid()->getData($request, $processFunction);
    }

    public function finishAuction ($datagrid, $id)
    {
        $this->allegro = new AllegroApi($this->registry);

        $response = $this->allegro->doFinishItem($id);

        return $this->getDatagrid()->refresh($datagrid);
    }

    protected function durationToDays ($date, $duration)
    {
        $map = Array(
            '0' => '3',
            '1' => '5',
            '2' => '7',
            '3' => '10',
            '4' => '14',
            '5' => '30'
        );

        $d = new DateTime($date);
        $d->modify('+' . $map[$duration] . ' day');
        return $d->format('Y-m-d');
    }

    public function addFields ($event, $request)
    {
        $form = &$request['form'];
        
        $allegro = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'allegro_data',
            'label' => 'Integracja z Allegro'
        )));

        $webapikey = $allegro->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'allegrowebapikey',
            'label' => 'Klucz WebApi'
        )));

        $allegro->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'allegrologin',
            'label' => $this->trans('TXT_LOG')
        )));

        $allegro->AddChild(new FormEngine\Elements\Password(Array(
            'name' => 'allegropassword',
            'label' => $this->trans('TXT_PASSWORD')
        )));

        $allegro->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'allegrocountry',
            'label' => 'Serwis Allegro',
            'options' => Array(
                new FormEngine\Option(1, 'Allegro.pl'),
                new FormEngine\Option(22, 'Allegro.by'),
                new FormEngine\Option(34, 'Aukro.bg'),
                new FormEngine\Option(56, 'Aukro.cz'),
                new FormEngine\Option(107, 'Allegro.kz'),
                new FormEngine\Option(168, 'Molotok.ru'),
                new FormEngine\Option(181, 'Aukro.sk'),
                new FormEngine\Option(209, 'Aukro.ua'),
                new FormEngine\Option(228, 'WebApi.pl (serwis testowy Allegro)')
            )
        )));

        $allegro->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'allegrodispatchmethod',
            'label' => 'Moduł wysyłki',
            'comment' => 'Zamówienia z Allegro będą importowane z tą formą wysyłki',
            'options' => FormEngine\Option::Make(App::getModel('dispatchmethod')->getDispatchmethodToSelect())
        )));

        $allegro->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'allegropaymentmethod',
            'label' => 'Moduł płatności',
            'comment' => 'Zamówienia z Allegro będą importowane z tą formą płatności',
            'options' => FormEngine\Option::Make(App::getModel('paymentmethod')->getPaymentmethodToSelect())
        )));

        $settings = $this->registry->core->loadModuleSettings('allegro', Helper::getViewId());

        if (! empty($settings)){
            $populate = Array(
                'allegro_data' => Array(
                    'allegrowebapikey' => $settings['allegrowebapikey'],
                    'allegrologin' => $settings['allegrologin'],
                    'allegropassword' => $settings['allegropassword'],
                    'allegrocountry' => $settings['allegrocountry'],
                    'allegrodispatchmethod' => $settings['allegrodispatchmethod'],
                    'allegropaymentmethod' => $settings['allegropaymentmethod']
                )
            );

            $event->setReturnValues($populate);
        }
    }

    public function saveSettings ($request)
    {
        if ($request['data']['allegrowebapikey'] != '' && $request['data']['allegrologin'] != '' && $request['data']['allegropassword'] != ''){
            $Settings = Array(
                'allegrowebapikey' => $request['data']['allegrowebapikey'],
                'allegrologin' => $request['data']['allegrologin'],
                'allegropassword' => $request['data']['allegropassword'],
                'allegrocountry' => $request['data']['allegrocountry'],
                'allegrodispatchmethod' => $request['data']['allegrodispatchmethod'],
                'allegropaymentmethod' => $request['data']['allegropaymentmethod']
            );
            $this->registry->core->saveModuleSettings('allegro', $Settings, $request['id']);
        }
    }

    public function checkCategory ($id)
    {
        if ($id > 0){
            $categories = $this->allegro->doGetCategoryPath($id);
            foreach ($categories as $category){
                if ($category['cat-id'] == $id){
                    if ($category['cat-is-leaf'] == 1){
                        return true;
                    }
                    else{
                        return false;
                    }
                }
            }
        }
        else{
            return false;
        }
    }

    protected function log ($Data, $newAuctionData)
    {
        $log = new Logger('allegro');
        $stream = new StreamHandler(ROOTPATH . 'logs' . DS . 'allegro.log', Logger::DEBUG);
        $log->pushHandler($stream);
        $log->addDebug('doNewAuction', Array(
            'FORMDATA' => $Data,
            'AUCTIONDATA' => $newAuctionData
        ));
    }

    public function doNewAuction ($Data, $newAuctionData)
    {
        $this->allegro = new AllegroApi($this->registry);

        $this->log($Data, $newAuctionData);

        foreach ($Data['products'] as $idRow => $product){
            $fieldsData = json_decode(json_encode($this->allegro->doGetSellFormFieldsForCategory($product['allegro_category'])), true);
            $checkCategory = $this->checkCategory($product['allegro_category']);

            if ($checkCategory){
                $fields = $this->getAuctionData($product, $newAuctionData, $fieldsData, $idRow);
                $checkAuction = $this->allegro->doCheckNewAuctionExt($fields);

                if (isset($checkAuction['item-price'])) {
                    $auction = $this->allegro->doNewAuctionExt($fields);
                    $this->saveAuction($auction, $product, $newAuctionData);
                }
            }
        }
    }

    public function saveAuction ($auction, $product, $newAuctionData)
    {
        $sql = 'INSERT INTO auction (itemid,idproduct,title,variant,description,quantity,category,minprice,buyprice,startprice,startdate,enddate, viewid)
				VALUES (:itemid,:idproduct,:title,:variant,:description,:quantity,:category,:minprice,:buyprice,:startprice,:startdate,:enddate, :viewid)';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('itemid', $auction['item-id']);
        $stmt->bindValue('idproduct', $product['idproduct']);
        $stmt->bindValue('title', $product['title']);
        $stmt->bindValue('variant', ($product['variant'] > 0) ? $product['variant'] : NULL);
        $stmt->bindValue('description', $product['description']);
        $stmt->bindValue('quantity', $product['quantity']);
        $stmt->bindValue('category', $product['allegro_category']);
        $stmt->bindValue('minprice', $product['allegro_min_price']);
        $stmt->bindValue('buyprice', $product['allegro_buy_price']);
        $stmt->bindValue('startprice', $product['allegro_start_price']);
        $stmt->bindValue('startdate', $newAuctionData['sell-form-id-3']);
        $stmt->bindValue('viewid', Helper::getViewId());
        $stmt->bindValue('enddate', $this->durationToDays($newAuctionData['sell-form-id-3'], $newAuctionData['sell-form-id-4']));
        $stmt->execute();
        if ($product['variant'] > 0){
            $this->decreaseProductAttributeStock($product['idproduct'], $product['variant'], $product['quantity']);
        }
        else{
            $this->decreaseProductStock($product['idproduct'], $product['quantity']);
        }
    }

    protected function decreaseProductAttributeStock ($productid, $idproductattribute, $qty)
    {
        $sql = 'UPDATE productattributeset SET stock = stock-:qty
				WHERE productid = :productid
				AND idproductattributeset = :idproductattribute';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('qty', $qty);
        $stmt->bindValue('productid', $productid);
        $stmt->bindValue('idproductattribute', $idproductattribute);
        try{
            $stmt->execute();
        }
        catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    protected function decreaseProductStock ($productid, $qty)
    {
        $sql = 'UPDATE product SET stock = stock-:qty
				WHERE idproduct = :idproduct';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('qty', $qty);
        $stmt->bindValue('idproduct', $productid);
        try{
            $stmt->execute();
        }
        catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function productSelectedPhotos ($id)
    {
        $sql = 'SELECT
					F.name,
					F.idfile
				FROM productphoto PP
				LEFT JOIN file F ON PP.photoid = F.idfile
				WHERE PP.productid = :id';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $normal = App::getModel('gallery')->getNormalImageById($rs['idfile']);
            $small = App::getModel('gallery')->getSmallImageById($rs['idfile']);
            $orginal = App::getModel('gallery')->getOrginalImageById($rs['idfile']);
            $Data[] = Array(
                'normal' => $normal['path'],
                'small' => $small['path'],
                'orginal' => $orginal['path']
            );
        }
        return $Data;
    }

    public function parseTemplate ($productData, $newAuctionData, $photos)
    {
        $content = '{% autoescape false %}' . $newAuctionData['content'] . '{% endautoescape %}';
        $product = Array(
            'name' => $productData['language'][Helper::getLanguageId()]['name'],
            'shortdescription' => $productData['language'][Helper::getLanguageId()]['shortdescription'],
            'description' => $productData['language'][Helper::getLanguageId()]['description'], //<-- descriptionallegro toma
            'longdescription' => $productData['language'][Helper::getLanguageId()]['longdescription'],
            'ean' => $productData['ean'],
            'photos' => $photos,
            'attributes' => $productData['attributes']
        );

        $this->registry->template->assign('product', $product);
        //\Gekosale\Arr::debug($content);
        $tpl = $this->registry->template->parse($content);

        //header('Content-Type: text/html; charset=utf-8');
        //echo $tpl;
        //die();
        //\Gekosale\Arr::Debug(Array());


        return $tpl;
    }

    public function GetTechnicalDataForProduct ($productId)
    {
        $languageId = Helper::getLanguageId();
        $sql = '
				SELECT
					TG.idtechnicaldatagroup AS id,
					TGT.name AS name
				FROM
					technicaldatagroup TG
					LEFT JOIN technicaldatagrouptranslation TGT ON TGT.technicaldatagroupid = TG.idtechnicaldatagroup AND TGT.languageid = :languageId
					LEFT JOIN producttechnicaldatagroup TSG ON TG.idtechnicaldatagroup = TSG.technicaldatagroupid
				WHERE
					TSG.productid = :productId
				GROUP BY
					TG.idtechnicaldatagroup
				ORDER BY
					TSG.order ASC
			';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('productId', $productId);
        $stmt->bindValue('languageId', $languageId);
        $stmt->execute();
        $groups = Array();
        $groupIndices = Array();
        while ($rs = $stmt->fetch()){
            $groupIndices[] = $rs['id'];
            $groups[] = Array(
                'id' => $rs['id'],
                'name' => $rs['name'],
                'attributes' => Array()
            );
        }
        if (count($groups)){
            $sql = '
					SELECT
						TA.idtechnicaldataattribute AS id,
						TA.type AS type,
						IF (TA.type = 2, TAV.value, TGA.value) AS value,
						TSG.technicaldatagroupid AS group_id,
						TAT.name AS name
					FROM
						technicaldataattribute TA
						LEFT JOIN technicaldataattributetranslation TAT ON TAT.technicaldataattributeid = TA.idtechnicaldataattribute
						LEFT JOIN producttechnicaldatagroupattribute TGA ON TA.idtechnicaldataattribute = TGA.technicaldataattributeid
						LEFT JOIN producttechnicaldatagroupattributetranslation TAV ON TAV.producttechnicaldatagroupattributeid = TGA.idproducttechnicaldatagroupattribute
						LEFT JOIN producttechnicaldatagroup TSG ON TGA.producttechnicaldatagroupid = TSG.idproducttechnicaldatagroup
					WHERE
						TSG.productid = :productId
						AND TAT.languageId = :languageId
						AND ((TA.type <> 2) OR (TAV.languageid = :languageId))
					GROUP BY
						TA.idtechnicaldataattribute,
						TGA.idproducttechnicaldatagroupattribute
					ORDER BY
						TSG.order ASC,
						TGA.order ASC
				';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('productId', $productId);
            $stmt->bindValue('languageId', $languageId);
            $stmt->execute();
            $groupIndex = 0;
            while ($rs = $stmt->fetch()){
                $currentGroupIndex = $rs['group_id'];
                if ($currentGroupIndex != $groups[$groupIndex]['id']){
                    if ($currentGroupIndex != $groups[++ $groupIndex]['id']){
                        throw new CoreException('Something\'s wrong with the technical data indices...');
                    }
                }
                $groups[$groupIndex]['attributes'][] = Array(
                    'id' => $rs['id'],
                    'type' => $rs['type'],
                    'value' => str_replace("\n", "<br />", $rs['value']),
                    'name' => $rs['name']
                );
            }
        }
        return $groups;
    }

    public function getAuctionData ($product, $newAuctionData, $fieldsData, $idRow = 0)
    {
        $productData = App::getModel('product')->getProductView($product['idproduct'], false);
        $productData['attributes'] = $this->GetTechnicalDataForProduct($product['idproduct']);
        $photos = $this->productSelectedPhotos($product['idproduct']);
        $description = $this->parseTemplate($productData, $newAuctionData, $photos);
        $photo = $this->productAllegroPhoto($product['idproduct']);

        $empty = new stdClass();
        $empty->{'fvalue-string'} = '';
        $empty->{'fvalue-int'} = 0;
        $empty->{'fvalue-float'} = 0;
        $empty->{'fvalue-image'} = ' ';
        $empty->{'fvalue-datetime'} = 0;
        $empty->{'fvalue-date'} = '';
        $empty->{'fvalue-boolean'} = false;
        $empty->{'fvalue-range-int'} = array(
            'fvalue-range-int-min' => 0,
            'fvalue-range-int-max' => 0
        );
        $empty->{'fvalue-range-float'} = array(
            'fvalue-range-float-min' => 0,
            'fvalue-range-float-max' => 0
        );
        $empty->{'fvalue-range-date'} = array(
            'fvalue-range-date-min' => '',
            'fvalue-range-date-max' => ''
        );

        $form = array();

        $field = clone $empty;
        $field->{'fid'} = 1;
        $field->{'fvalue-string'} = $product['title'];
        $form[] = $field;

        $field = clone $empty;
        $field->{'fid'} = 2;
        $field->{'fvalue-int'} = $product['allegro_category'];
        $form[] = $field;

        $field = clone $empty;
        $field->{'fid'} = 3;
        $field->{'fvalue-datetime'} = strtotime($newAuctionData['sell-form-id-3']);
        $form[] = $field;

        $field = clone $empty;
        $field->{'fid'} = 4;
        $field->{'fvalue-int'} = $newAuctionData['sell-form-id-4'];
        $form[] = $field;

        $field = clone $empty;
        $field->{'fid'} = 5;
        $field->{'fvalue-int'} = $product['quantity'];
        $form[] = $field;

        foreach ($fieldsData['sell-form-fields-list'] as $fieldData){
            if ($fieldData['sell-form-title'] == 'Stan'){
                $field = clone $empty;
                $field->{'fid'} = $fieldData['sell-form-id'];
                $field->{'fvalue-int'} = 1;
                $form[] = $field;
            }
        }

        foreach ($fieldsData['sell-form-fields-list'] as $fieldData){
            if ($fieldData['sell-form-opt'] == 1 && App::getModel('allegro/allegrocategories')->checkNewFields($fieldData['sell-form-id']) && $fieldData['sell-form-title'] != 'Stan'){
                $field = clone $empty;
                $field->{'fid'} = $fieldData['sell-form-id'];
                $field->{'fvalue-int'} = 1;
                $form[] = $field;
            }
        }

        $field = clone $empty;
        $field->{'fid'} = 8;
        $field->{'fvalue-float'} = $product['allegro_buy_price'];
        $form[] = $field;

        $field = clone $empty;
        $field->{'fid'} = 9;
        $field->{'fvalue-int'} = $newAuctionData['sell-form-id-9'];
        $form[] = $field;

        $field = clone $empty;
        $field->{'fid'} = 10;
        $field->{'fvalue-int'} = $newAuctionData['sell-form-id-10'];
        $form[] = $field;

        $field = clone $empty;
        $field->{'fid'} = 11;
        $field->{'fvalue-string'} = $newAuctionData['sell-form-id-11'];
        $form[] = $field;

        $field = clone $empty;
        $field->{'fid'} = 12;
        $field->{'fvalue-int'} = $newAuctionData['sell-form-id-12'];
        $form[] = $field;

        $field = clone $empty;
        $field->{'fid'} = 13;
        $field->{'fvalue-int'} = $newAuctionData['sell-form-id-13'];
        $form[] = $field;

        if (is_array($newAuctionData['sell-form-id-14'])){
            $field = clone $empty;
            $field->{'fid'} = 14;
            $field->{'fvalue-int'} = array_sum($newAuctionData['sell-form-id-14']);
            $form[] = $field;
        }

        if (is_array($newAuctionData['sell-form-id-15'])){
            $field = clone $empty;
            $field->{'fid'} = 15;
            $field->{'fvalue-int'} = array_sum($newAuctionData['sell-form-id-15']);
            $form[] = $field;
        }

        $field = clone $empty;
        $field->{'fid'} = 16;
        $field->{'fvalue-image'} = $photo;
        $form[] = $field;

        $field = clone $empty;
        $field->{'fid'} = 24;
        $field->{'fvalue-string'} = $description;
        $form[] = $field;

        $field = clone $empty;
        $field->{'fid'} = 27;
        $field->{'fvalue-string'} = ' ' . $newAuctionData['sell-form-id-27'];
        $form[] = $field;

        $field = clone $empty;
        $field->{'fid'} = 29;
        $field->{'fvalue-int'} = $newAuctionData['sell-form-id-29'];
        $form[] = $field;

        $field = clone $empty;
        $field->{'fid'} = 32;
        $field->{'fvalue-string'} = $newAuctionData['sell-form-id-32'];
        $form[] = $field;

        $field = clone $empty;
        $field->{'fid'} = 32;
        $field->{'fvalue-string'} = $newAuctionData['sell-form-id-32'];
        $form[] = $field;

        if (is_array($newAuctionData['sell-form-id-35'])){
            $field = clone $empty;
            $field->{'fid'} = 35;
            $field->{'fvalue-int'} = array_sum($newAuctionData['sell-form-id-35']);
            $form[] = $field;
        }

        if ($newAuctionData['sell-form-id-36'] == 1){
            $field = clone $empty;
            $field->{'fid'} = 36;
            $field->{'fvalue-float'} = $newAuctionData['sell-form-id-36-cost'];
            $form[] = $field;
        }

        if ($newAuctionData['sell-form-id-37'] == 1){
            $field = clone $empty;
            $field->{'fid'} = 37;
            $field->{'fvalue-float'} = $newAuctionData['sell-form-id-37-cost'];
            $form[] = $field;
        }

        if ($newAuctionData['sell-form-id-38'] == 1){
            $field = clone $empty;
            $field->{'fid'} = 38;
            $field->{'fvalue-float'} = $newAuctionData['sell-form-id-38-cost'];
            $form[] = $field;
        }

        if ($newAuctionData['sell-form-id-39'] == 1){
            $field = clone $empty;
            $field->{'fid'} = 39;
            $field->{'fvalue-float'} = $newAuctionData['sell-form-id-39-cost'];
            $form[] = $field;
        }

        if ($newAuctionData['sell-form-id-40'] == 1){
            $field = clone $empty;
            $field->{'fid'} = 40;
            $field->{'fvalue-float'} = $newAuctionData['sell-form-id-40-cost'];
            $form[] = $field;
        }

        if ($newAuctionData['sell-form-id-41'] == 1){
            $field = clone $empty;
            $field->{'fid'} = 41;
            $field->{'fvalue-float'} = $newAuctionData['sell-form-id-41-cost'];
            $form[] = $field;
        }

        if ($newAuctionData['sell-form-id-42'] == 1){
            $field = clone $empty;
            $field->{'fid'} = 42;
            $field->{'fvalue-float'} = $newAuctionData['sell-form-id-42-cost'];
            $form[] = $field;
        }

        if ($newAuctionData['sell-form-id-43'] == 1){
            $field = clone $empty;
            $field->{'fid'} = 43;
            $field->{'fvalue-float'} = $newAuctionData['sell-form-id-43-cost'];
            $form[] = $field;
        }

        if ($newAuctionData['sell-form-id-44'] == 1){
            $field = clone $empty;
            $field->{'fid'} = 44;
            $field->{'fvalue-float'} = $newAuctionData['sell-form-id-44-cost'];
            $form[] = $field;
        }

        if ($newAuctionData['sell-form-id-45'] == 1){
            $field = clone $empty;
            $field->{'fid'} = 45;
            $field->{'fvalue-float'} = $newAuctionData['sell-form-id-45-cost'];
            $form[] = $field;
        }

        if ($newAuctionData['sell-form-id-46'] == 1){
            $field = clone $empty;
            $field->{'fid'} = 46;
            $field->{'fvalue-float'} = $newAuctionData['sell-form-id-46-cost'];
            $form[] = $field;
        }

        if ($newAuctionData['sell-form-id-47'] == 1){
            $field = clone $empty;
            $field->{'fid'} = 47;
            $field->{'fvalue-float'} = $newAuctionData['sell-form-id-47-cost'];
            $form[] = $field;
        }

        if ($newAuctionData['sell-form-id-48'] == 1){
            $field = clone $empty;
            $field->{'fid'} = 48;
            $field->{'fvalue-float'} = $newAuctionData['sell-form-id-48-cost'];
            $form[] = $field;
        }

        if ($newAuctionData['sell-form-id-49'] == 1){
            $field = clone $empty;
            $field->{'fid'} = 49;
            $field->{'fvalue-float'} = $newAuctionData['sell-form-id-49-cost'];
            $form[] = $field;
        }

        if ($newAuctionData['sell-form-id-50'] == 1){
            $field = clone $empty;
            $field->{'fid'} = 50;
            $field->{'fvalue-float'} = $newAuctionData['sell-form-id-50-cost'];
            $form[] = $field;
        }

        if ($newAuctionData['sell-form-id-51'] == 1){
            $field = clone $empty;
            $field->{'fid'} = 51;
            $field->{'fvalue-float'} = $newAuctionData['sell-form-id-51-cost'];
            $form[] = $field;
        }

        if ($newAuctionData['sell-form-id-52'] == 1){
            $field = clone $empty;
            $field->{'fid'} = 52;
            $field->{'fvalue-float'} = $newAuctionData['sell-form-id-52-cost'];
            $form[] = $field;
        }

        //check fields
        $fields = array();
        foreach ($fieldsData['sell-form-fields-list'] as $value) {
            $fields[$value['sell-form-id']] = $value;

            //default
            if (($value['sell-form-opt'] == 1) &&
                ($value['sell-form-type'] == 4) &&
                ($value['sell-form-cat'] > 0)) { //select required params

                $values = explode('|', $value['sell-form-opts-values']);
                $newAuctionData['allegro-params'][$idRow][$value['sell-form-id']] = array(
                    'sell-form-id' => $value['sell-form-id'],
                    'sell-form-res-type' => $value['sell-form-res-type'],
                    'value' => $values[1]
                );
            }
        }

        if (is_array($newAuctionData['allegro-params'][$idRow])) {
            foreach ($newAuctionData['allegro-params'][$idRow] as $param) {
                if (isset($fields[$param['sell-form-id']])) {
                    $field = clone $empty;
                    $fieldAllegro = $fields[$param['sell-form-id']];
                    $field->{'fid'} = $param['sell-form-id'];
                    switch ($param['sell-form-res-type']) {
                        case 1:
                            $field->{'fvalue-string'} = $this->_prepareValue($param['value'], $fieldAllegro);
                            break;
                        case 2:
                            $field->{'fvalue-int'} = $this->_prepareValue($param['value'], $fieldAllegro);
                            break;
                        case 3:
                            $field->{'fvalue-float'} = $this->_prepareValue($param['value'], $fieldAllegro);
                            break;
                        default:
                            break;
                    }
                    $form[] = $field;
                }
            }
        }
        return $form;
    }

    protected function _prepareValue($value, $fieldAllegro) {
        //max length
        if ($fieldAllegro['sell-form-length'] > 0) {
            $value = substr(trim($value), 0, $fieldAllegro['sell-form-length']);
        }
        //cast
        switch ($fieldAllegro['sell-form-res-type']) {
            case 1: //string
                $value = strval($value);
                break;
            case 2: //int
                $value = intval($value);
                //min
                $fieldAllegro['sell-min-value'] = intval($fieldAllegro['sell-min-value']);
                $fieldAllegro['sell-max-value'] = intval($fieldAllegro['sell-max-value']);
                if (($fieldAllegro['sell-min-value'] > 0) &&
                    ($value < $fieldAllegro['sell-min-value'])) {

                    $value = $fieldAllegro['sell-min-value'];
                }
                //max
                if (($fieldAllegro['sell-max-value'] > 0) &&
                    ($value > $fieldAllegro['sell-max-value'])) {

                    $value = $fieldAllegro['sell-max-value'];
                }
                break;
            case 3: //float
                $value = floatval($value);
                //min
                $fieldAllegro['sell-min-value'] = floatval($fieldAllegro['sell-min-value']);
                $fieldAllegro['sell-max-value'] = floatval($fieldAllegro['sell-max-value']);
                if (($fieldAllegro['sell-min-value'] > 0) &&
                    ($value < $fieldAllegro['sell-min-value'])) {

                    $value = $fieldAllegro['sell-min-value'];
                }
                //max
                if (($fieldAllegro['sell-max-value'] > 0) &&
                    ($value > $fieldAllegro['sell-max-value'])) {

                    $value = $fieldAllegro['sell-max-value'];
                }
                break;
            default:
                break;
        }
        return $value;
    }

    public function productAllegroPhoto ($id)
    {
        $sql = 'SELECT
					PP.photoid AS id,
					FE.name AS filextensioname,
					CONCAT(PP.photoid,\'.\', FE.name) AS filediskname
				FROM productphoto PP
				LEFT JOIN file F ON F.idfile = PP.photoid
				LEFT JOIN fileextension FE ON FE.idfileextension = F.fileextensionid
				WHERE PP.productid = :id AND PP.mainphoto = 1';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs){
            $photo = ROOTPATH . 'design' . DS . '_gallery' . DS . '_orginal' . DS . $rs['filediskname'];
            return file_get_contents($photo);
        }
        return '';
    }

    protected function addOrderClientData ($Data, $clientId = 0, $orderId)
    {
        $sql = 'INSERT INTO orderclientdata SET
					firstname = AES_ENCRYPT(:firstname, :encryptionKey),
					surname = AES_ENCRYPT(:surname, :encryptionKey),
					street = AES_ENCRYPT(:street, :encryptionKey),
					streetno = AES_ENCRYPT(:streetno, :encryptionKey),
					placeno = AES_ENCRYPT(:placeno, :encryptionKey),
					postcode = AES_ENCRYPT(:postcode, :encryptionKey),
					place = AES_ENCRYPT(:place, :encryptionKey),
					phone = AES_ENCRYPT(:phone, :encryptionKey),
					phone2 = AES_ENCRYPT(:phone2, :encryptionKey),
					email = AES_ENCRYPT(:email, :encryptionKey),
					companyname = AES_ENCRYPT(:companyname, :encryptionKey),
					nip = AES_ENCRYPT(:nip, :encryptionKey),
					orderid = :orderid,
					clientid = :clientid,
					countryid = :country
		';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('firstname', $Data['firstname']);
        $stmt->bindValue('surname', $Data['surname']);
        $stmt->bindValue('street', $Data['street']);
        $stmt->bindValue('streetno', $Data['streetno']);
        $stmt->bindValue('placeno', $Data['placeno']);
        $stmt->bindValue('postcode', $Data['postcode']);
        $stmt->bindValue('place', $Data['placename']);
        $stmt->bindValue('phone', $Data['phone']);
        $stmt->bindValue('phone2', $Data['phone2']);
        $stmt->bindValue('email', $Data['email']);
        $stmt->bindValue('companyname', $Data['companyname']);
        $stmt->bindValue('nip', $Data['nip']);
        $stmt->bindValue('country', $Data['countryid']);
        $stmt->bindValue('orderid', $orderId);
        $stmt->bindValue('clientid', $clientId);
        $stmt->bindValue('viewid', Helper::getViewId());
        $stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
        try{
            $stmt->execute();
        }
        catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    protected function addOrderClientDeliveryData ($Data, $orderId)
    {
        $sql = 'INSERT INTO orderclientdeliverydata SET
					firstname = AES_ENCRYPT(:firstname, :encryptionKey),
					surname = AES_ENCRYPT(:surname, :encryptionKey),
					street = AES_ENCRYPT(:street, :encryptionKey),
					streetno = AES_ENCRYPT(:streetno, :encryptionKey),
					placeno = AES_ENCRYPT(:placeno, :encryptionKey),
					postcode = AES_ENCRYPT(:postcode, :encryptionKey),
					place = AES_ENCRYPT(:place, :encryptionKey),
					phone = AES_ENCRYPT(:phone, :encryptionKey),
					phone2 = AES_ENCRYPT(:phone2, :encryptionKey),
					companyname = AES_ENCRYPT(:companyname, :encryptionKey),
					nip = AES_ENCRYPT(:nip, :encryptionKey),
					email = AES_ENCRYPT(:email, :encryptionKey),
					orderid = :orderid,
					countryid = :country';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('firstname', $Data['firstname']);
        $stmt->bindValue('surname', $Data['surname']);
        $stmt->bindValue('street', $Data['street']);
        $stmt->bindValue('streetno', $Data['streetno']);
        $stmt->bindValue('placeno', $Data['placeno']);
        $stmt->bindValue('postcode', $Data['postcode']);
        $stmt->bindValue('place', $Data['placename']);
        $stmt->bindValue('phone', $Data['phone']);
        $stmt->bindValue('phone2', $Data['phone2']);
        $stmt->bindValue('email', $Data['email']);
        $stmt->bindValue('companyname', $Data['companyname']);
        $stmt->bindValue('nip', NULL);
        $stmt->bindValue('orderid', $orderId);
        $stmt->bindValue('country', $Data['countryid']);
        $stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
        try{
            $stmt->execute();
        }
        catch (Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function getAuctionByItemId ($id)
    {
        $sql = 'SELECT
					A.itemid,
					A.idproduct,
					A.title,
					A.variant,
					A.viewid,
					V.value AS vat,
					PT.name
				FROM auction A
				LEFT JOIN product P ON P.idproduct = A.idproduct
				LEFT JOIN producttranslation PT ON PT.productid = A.idproduct AND PT.languageid = :languageid
				LEFT JOIN vat V ON V.idvat = P.vatid
				WHERE itemid = :itemid
		';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('itemid', $id);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->execute();
        $rs = $stmt->fetch();
        $Data = Array();
        if ($rs){
            $Data = Array(
                'itemid' => $rs['itemid'],
                'idproduct' => $rs['idproduct'],
                'title' => $rs['title'],
                'name' => $rs['name'],
                'variant' => $rs['variant'],
                'viewid' => $rs['viewid'],
                'vatvalue' => $rs['vat']
            );
        }
        return $Data;
    }

    public function checkOrder ($id)
    {
        $sql = 'SELECT
					COUNT(orderid) AS total
				FROM allegroorder
				WHERE allegropostbuyformid = :allegropostbuyformid';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('allegropostbuyformid', $id);
        $stmt->execute();
        $rs = $stmt->fetch();
        $Data = Array();
        if ($rs){
            return $rs['total'];
        }
    }

    public function checkIsShopAuction ($id)
    {
        $sql = 'SELECT
					COUNT(idauction) AS total
				FROM auction
				WHERE itemid = :itemid';
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('itemid', $id);
        $stmt->execute();
        $rs = $stmt->fetch();
        $Data = Array();
        if ($rs){
            return $rs['total'];
        }
    }

    public function addAllegroOrder ($orderData, $contactData)
    {
        $this->allegro = new AllegroApi($this->registry);

        $globalPriceNetto = 0;
        $globalPriceGross = 0;

        $viewid = Helper::getViewId();
        $auctionIds = Array();
        foreach ($orderData as $order){
            $checkOrder = $this->checkOrder($order['post-buy-form-id']);
            if ($checkOrder == 0){
                $products = Array();
                foreach ($order['post-buy-form-items'] as $orderProduct){
                    $auctionData = $this->getAuctionByItemId($orderProduct['post-buy-form-it-id']);

                    if (! empty($auctionData)){
                        $viewid = $auctionData['viewid'];
                        $products[] = Array(
                            'auction' => $auctionData,
                            'product' => $orderProduct
                        );
                        $auctionIds[] = (float) $auctionData['itemid'];
                        $globalPriceNetto += $orderProduct['post-buy-form-it-price'] / (1 + ($auctionData['vatvalue'] / 100));
                        $globalPriceGross += $orderProduct['post-buy-form-it-price'];
                    }
                }

                $globalPriceNetto = round($globalPriceNetto, 4);
                $globalPriceGross = round($globalPriceGross, 4);

                if (! empty($products)){

                    $settings = $this->registry->core->loadModuleSettings('allegro', $viewid);
                    $dispatchmethodid = $settings['allegrodispatchmethod'];
                    $paymentmethodid = $settings['allegropaymentmethod'];

                    $sql = 'INSERT INTO `order` (
								price,
								dispatchmethodprice,
								globalprice,
								dispatchmethodname,
								paymentmethodname,
								orderstatusid,
								dispatchmethodid,
								paymentmethodid,
								clientid,
								globalpricenetto,
								viewid,
								pricebeforepromotion,
								currencyid,
								currencysymbol,
								currencyrate,
								rulescartid,
								sessionid,
								customeropinion,
								giftwrap,
								giftwrapmessage,
								paczkomat
							)
							VALUES
							(
								:price,
								:dispatchmethodprice,
								:globalprice,
								:dispatchmethodname,
								:paymentmethodname,
								(SELECT idorderstatus FROM orderstatus WHERE `default` = 1),
								:dispatchmethodid,
								:paymentmethodid,
								:clientid,
								:globalpricenetto,
								:viewid,
								:pricebeforepromotion,
								:currencyid,
								:currencysymbol,
								:currencyrate,
								:rulescartid,
								:sessionid,
								:customeropinion,
								:giftwrap,
								:giftwrapmessage,
								:paczkomat)';
                    $stmt = Db::getInstance()->prepare($sql);
                    $stmt->bindValue('dispatchmethodprice', $order['post-buy-form-postage-amount']);
                    $stmt->bindValue('dispatchmethodname', 'Forma wysyłki wybrana w Allegro');
                    $stmt->bindValue('dispatchmethodid', $dispatchmethodid);
                    $stmt->bindValue('paymentmethodname', 'Forma płatności wybrana w Allegro');
                    $stmt->bindValue('paymentmethodid', $paymentmethodid);
                    $stmt->bindValue('clientid', NULL);
                    $stmt->bindValue('sessionid', 213412756);
                    $stmt->bindValue('customeropinion', 'Import aukcji Allegro: ' . implode(',', $auctionIds));
                    $stmt->bindValue('paczkomat', NULL);
                    $stmt->bindValue('giftwrap', 0);
                    $stmt->bindValue('giftwrapmessage', '');
                    $stmt->bindValue('currencyid', App::getContainer()->get('session')->getActiveShopCurrencyId());
                    $stmt->bindValue('currencysymbol', 'PLN');
                    $stmt->bindValue('currencyrate', 1);
                    $stmt->bindValue('pricebeforepromotion', 0);
                    $stmt->bindValue('rulescartid', NULL);
                    $stmt->bindValue('globalprice', $order['post-buy-form-amount']);
                    $stmt->bindValue('globalpricenetto', $globalPriceNetto);
                    $stmt->bindValue('price', $globalPriceGross);
                    $stmt->bindValue('viewid', $viewid);
                    try{
                        $stmt->execute();
                    }
                    catch (Exception $e){
                        throw new Exception($e->getMessage());
                    }
                    $orderid = Db::getInstance()->lastInsertId();

                    $fullName = explode(' ', $order['post-buy-form-shipment-address']['post-buy-form-adr-full-name']);
                    $firstname = array_shift($fullName);

                    $firstname = '';
                    $surname = '';
                    $email = '';
                    $phone = '';
                    $phone2 = '';
                    foreach ($contactData as $contact){
                        if ($contact['contact-user-id'] == $order['post-buy-form-buyer-id']){
                            $firstname = $contact['contact-first-name'];
                            $surname = $contact['contact-last-name'];
                            $email = $contact['contact-email'];
                            $phone = $contact['contact-phone'];
                            $phone2 = $contact['contact-phone2'];
                            break;
                        }
                    }

                    $clientData = Array(
                        'firstname' => $firstname,
                        'surname' => $surname,
                        'street' => $order['post-buy-form-shipment-address']['post-buy-form-adr-street'],
                        'streetno' => '',
                        'placeno' => '',
                        'postcode' => $order['post-buy-form-shipment-address']['post-buy-form-adr-postcode'],
                        'placename' => $order['post-buy-form-shipment-address']['post-buy-form-adr-city'],
                        'companyname' => $order['post-buy-form-shipment-address']['post-buy-form-adr-company'],
                        'nip' => $order['post-buy-form-shipment-address']['post-buy-form-adr-nip'],
                        'phone' => $phone,
                        'phone2' => $phone2,
                        'email' => $email,
                        'countryid' => $this->registry->loader->getParam('countryid')
                    );

                    $this->addOrderClientData($clientData, NULL, $orderid);
                    $this->addOrderClientDeliveryData($clientData, $orderid);

                    /*
                     * Rejestracja w sklepie
                     */

                    $password = Core::passwordGenerate();

                    $hash = new \PasswordHash\PasswordHash();
                    $sql = 'SELECT idclient FROM client WHERE login = :login';
                    $stmt = Db::getInstance()->prepare($sql);
                    $stmt->bindValue('login', $hash->HashLogin($email));
                    $stmt->execute();
                    $rs = $stmt->fetch();
                    if ($rs){
                        $idClient = $rs['idclient'];
                    }
                    else{
                        $sql = 'INSERT INTO client (login, password, disable, viewid, isallegro)
								VALUES (:login, :password, :disable, :viewid, 1)';
                        $stmt = Db::getInstance()->prepare($sql);
                        $stmt->bindValue('login', $hash->HashLogin($email));
                        $stmt->bindValue('password', $hash->HashPassword($password));
                        $stmt->bindValue('disable', isset($Data['disable']) ? $Data['disable'] : 0);
                        $stmt->bindValue('viewid', Helper::getViewId());
                        try{
                            $stmt->execute();
                        }
                        catch (Exception $e){
                            throw new FrontendException($e->getMessage());
                        }

                        $idClient = Db::getInstance()->lastInsertId();

                        $sql = 'UPDATE `order` SET clientid = :clientid WHERE idorder = :idorder';
                        $stmt = Db::getInstance()->prepare($sql);
                        $stmt->bindValue('clientid', $idClient);
                        $stmt->bindValue('idorder', $orderid);
                        $stmt->execute();

                        $sql = 'INSERT INTO clientdata SET
									firstname = AES_ENCRYPT(:firstname, :encryptionKey),
									surname = AES_ENCRYPT(:surname, :encryptionKey),
									email = AES_ENCRYPT(:email, :encryptionKey),
									phone = AES_ENCRYPT(:phone, :encryptionKey),
									phone2 = AES_ENCRYPT(:phone2, :encryptionKey),
									description = AES_ENCRYPT(:description, :encryptionKey),
									clientgroupid = 10,
									clientid = :clientid';
                        $stmt = Db::getInstance()->prepare($sql);
                        $stmt->bindValue('clientid', $idClient);
                        $stmt->bindValue('firstname', $firstname);
                        $stmt->bindValue('surname', $surname);
                        $stmt->bindValue('email', $email);
                        $stmt->bindValue('phone', $phone);
                        $stmt->bindValue('phone2', $phone2);
                        $stmt->bindValue('description', 'Klient zarejestrowany automatycznie z Allegro');
                        $stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());

                        try{
                            $stmt->execute();
                        }
                        catch (Exception $e){
                            throw new FrontendException($e->getMessage());
                        }

                        $sql = 'INSERT INTO clientaddress SET
									clientid	= :clientid,
									main		= :main,
									firstname 	= AES_ENCRYPT(:firstname, :encryptionKey),
									surname   	= AES_ENCRYPT(:surname, :encryptionKey),
									companyname	= AES_ENCRYPT(:companyname, :encryptionKey),
									street		= AES_ENCRYPT(:street, :encryptionKey),
									streetno	= AES_ENCRYPT(:streetno, :encryptionKey),
									placeno		= AES_ENCRYPT(:placeno, :encryptionKey),
									postcode	= AES_ENCRYPT(:postcode, :encryptionKey),
									nip		= AES_ENCRYPT(:nip, :encryptionKey),
									placename	= AES_ENCRYPT(:placename, :encryptionKey),
									countryid	= :countryid
								ON DUPLICATE KEY UPDATE
									firstname 	= AES_ENCRYPT(:firstname, :encryptionKey),
									surname   	= AES_ENCRYPT(:surname, :encryptionKey),
									companyname	= AES_ENCRYPT(:companyname, :encryptionKey),
									street		= AES_ENCRYPT(:street, :encryptionKey),
									streetno	= AES_ENCRYPT(:streetno, :encryptionKey),
									placeno		= AES_ENCRYPT(:placeno, :encryptionKey),
									postcode	= AES_ENCRYPT(:postcode, :encryptionKey),
									nip		= AES_ENCRYPT(:nip, :encryptionKey),
									placename	= AES_ENCRYPT(:placename, :encryptionKey),
									countryid	= :countryid';
                        $stmt = Db::getInstance()->prepare($sql);
                        $stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
                        $stmt->bindValue('clientid', $idClient);
                        $stmt->bindValue('main', 1);
                        $stmt->bindValue('firstname', $firstname);
                        $stmt->bindValue('surname', $surname);
                        $stmt->bindValue('companyname', $order['post-buy-form-shipment-address']['post-buy-form-adr-company']);
                        $stmt->bindValue('street', $order['post-buy-form-shipment-address']['post-buy-form-adr-street']);
                        $stmt->bindValue('streetno', '');
                        $stmt->bindValue('postcode', $order['post-buy-form-shipment-address']['post-buy-form-adr-postcode']);
                        $stmt->bindValue('placeno', '');
                        $stmt->bindValue('nip', $order['post-buy-form-shipment-address']['post-buy-form-adr-nip']);
                        $stmt->bindValue('placename', $order['post-buy-form-shipment-address']['post-buy-form-adr-city']);
                        $stmt->bindValue('countryid', 261);
                        try{
                            $stmt->execute();
                        }
                        catch (Exception $e){
                            throw new FrontendException($e->getMessage());
                        }

                        $sql = 'INSERT INTO clientaddress SET
									clientid	= :clientid,
									main		= :main,
									firstname 	= AES_ENCRYPT(:firstname, :encryptionKey),
									surname   	= AES_ENCRYPT(:surname, :encryptionKey),
									companyname	= AES_ENCRYPT(:companyname, :encryptionKey),
									street		= AES_ENCRYPT(:street, :encryptionKey),
									streetno	= AES_ENCRYPT(:streetno, :encryptionKey),
									placeno		= AES_ENCRYPT(:placeno, :encryptionKey),
									postcode	= AES_ENCRYPT(:postcode, :encryptionKey),
									nip		= AES_ENCRYPT(:nip, :encryptionKey),
									placename	= AES_ENCRYPT(:placename, :encryptionKey),
									countryid	= :countryid
								ON DUPLICATE KEY UPDATE
									firstname 	= AES_ENCRYPT(:firstname, :encryptionKey),
									surname   	= AES_ENCRYPT(:surname, :encryptionKey),
									companyname	= AES_ENCRYPT(:companyname, :encryptionKey),
									street		= AES_ENCRYPT(:street, :encryptionKey),
									streetno	= AES_ENCRYPT(:streetno, :encryptionKey),
									placeno		= AES_ENCRYPT(:placeno, :encryptionKey),
									postcode	= AES_ENCRYPT(:postcode, :encryptionKey),
									nip		= AES_ENCRYPT(:nip, :encryptionKey),
									placename	= AES_ENCRYPT(:placename, :encryptionKey),
									countryid	= :countryid';
                        $stmt = Db::getInstance()->prepare($sql);
                        $stmt->bindValue('encryptionKey', App::getContainer()->get('session')->getActiveEncryptionKeyValue());
                        $stmt->bindValue('clientid', $idClient);
                        $stmt->bindValue('main', 0);
                        $stmt->bindValue('firstname', $firstname);
                        $stmt->bindValue('surname', $surname);
                        $stmt->bindValue('companyname', $order['post-buy-form-shipment-address']['post-buy-form-adr-company']);
                        $stmt->bindValue('street', $order['post-buy-form-shipment-address']['post-buy-form-adr-street']);
                        $stmt->bindValue('streetno', '');
                        $stmt->bindValue('postcode', $order['post-buy-form-shipment-address']['post-buy-form-adr-postcode']);
                        $stmt->bindValue('placeno', '');
                        $stmt->bindValue('nip', $order['post-buy-form-shipment-address']['post-buy-form-adr-nip']);
                        $stmt->bindValue('placename', $order['post-buy-form-shipment-address']['post-buy-form-adr-city']);
                        $stmt->bindValue('countryid', 261);
                        try{
                            $stmt->execute();
                        }
                        catch (Exception $e){
                            throw new FrontendException($e->getMessage());
                        }
                    }

                    foreach ($products as $product){
                        $sql = 'INSERT INTO orderproduct(name,productattributesetid, price, qty, qtyprice, orderid, productid, vat, pricenetto, photoid, ean)
								VALUES (:name,:productattributesetid, :price, :qty, :qtyprice, :orderid, :productid, :vat, :pricenetto, :photoid, :ean)';
                        $stmt = Db::getInstance()->prepare($sql);
                        $stmt->bindValue('name', $product['auction']['name']);
                        $stmt->bindValue('price', $product['product']['post-buy-form-it-price']);
                        $stmt->bindValue('qty', $product['product']['post-buy-form-it-quantity']);
                        $stmt->bindValue('qtyprice', $product['product']['post-buy-form-it-amount']);
                        $stmt->bindValue('orderid', $orderid);
                        $stmt->bindValue('productid', $product['auction']['idproduct']);
                        $stmt->bindValue('vat', $product['auction']['vatvalue']);
                        $stmt->bindValue('pricenetto', $product['product']['post-buy-form-it-price'] / (1 + ($product['auction']['vatvalue'] / 100)));
                        $stmt->bindValue('photoid', NULL);
                        $stmt->bindValue('ean', NULL);
                        $stmt->bindValue('productattributesetid', $product['auction']['variant']);
                        try{
                            $stmt->execute();
                        }
                        catch (Exception $e){
                            throw new Exception($e->getMessage());
                        }
                    }

                    $sql = 'INSERT INTO allegroorder SET
								allegropostbuyformid = :allegropostbuyformid,
								orderid = :orderid';
                    $stmt = Db::getInstance()->prepare($sql);
                    $stmt->bindValue('allegropostbuyformid', $order['post-buy-form-id']);
                    $stmt->bindValue('orderid', $orderid);
                    $stmt->execute();
                }
            }
        }
        if (isset($orderid)){
            return $orderid;
        }
    }

    public function updateAuctionStatus ()
    {
        $this->allegro = new AllegroApi($this->registry);

        $statuses = Array();

        $future = $this->allegro->doGetMyFutureItems();
        foreach ($future['future-items-list'] as $auction){
            $statuses[(string) $auction['item-id']] = 'future';
        }

        $sell = $this->allegro->doGetMySellItems();
        foreach ($sell['sell-items-list'] as $auction){
            $statuses[(string) $auction['item-id']] = 'sell';
        }

        $sold = $this->allegro->doGetMySoldItems();
        foreach ($sold['sold-items-list'] as $auction){
            $statuses[(string) $auction['item-id']] = 'sold';
        }

        $notsold = $this->allegro->doGetMyNotSoldItems();
        foreach ($notsold['not-sold-items-list'] as $auction){
            $statuses[(string) $auction['item-id']] = 'notsold';
        }

        Db::getInstance()->beginTransaction();
        foreach ($statuses as $itemid => $status){
            $sql = 'UPDATE auction SET status = :status WHERE itemid = :itemid';
            $stmt = Db::getInstance()->prepare($sql);
            $stmt->bindValue('itemid', $itemid);
            $stmt->bindValue('status', $status);
            $stmt->execute();
        }
        Db::getInstance()->commit();
    }
}
