<?php

use Gekosale\App;
use Gekosale\Db;
use Gekosale\Session;
use Gekosale\Helper;

class WellCommerceShopgatePlugin extends ShopgatePlugin
{

    public function startup ()
    {
        $this->config = new ShopgateConfigWellCommerce();
    }

    public function getClientAddress ($id, $main)
    {
        $sql = "SELECT
					CA.idclientaddress,
					AES_DECRYPT(CA.firstname, :encryptionkey) AS firstname,
					AES_DECRYPT(CA.surname, :encryptionkey) AS surname,
					AES_DECRYPT(CA.companyname, :encryptionkey) AS companyname,
					AES_DECRYPT(CA.nip, :encryptionkey) AS nip,
					AES_DECRYPT(CA.street, :encryptionkey) AS street,
					AES_DECRYPT(CA.streetno, :encryptionkey) AS streetno,
					AES_DECRYPT(CA.postcode, :encryptionkey) AS postcode,
					AES_DECRYPT(CA.placename, :encryptionkey) AS placename,
					AES_DECRYPT(CA.placeno, :encryptionkey) AS placeno,
					CA.countryid,
					CA.clienttype,
        			CO.name as countryname
				FROM clientaddress CA
        		LEFT JOIN country CO ON CO.idcountry = CA.countryid
				WHERE clientid=:clientid AND main = :main";
        $Data = Array(
            'idclientaddress' => 0,
            'firstname' => '',
            'surname' => '',
            'companyname' => '',
            'nip' => '',
            'street' => '',
            'streetno' => '',
            'placeno' => '',
            'placename' => '',
            'postcode' => '',
            'countryname' => ''
        );
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('clientid', $id);
        $stmt->bindValue('viewid', Helper::getViewId());
        $stmt->bindValue('main', $main);
        $stmt->bindValue('encryptionkey', Session::getActiveEncryptionKeyValue());
        $stmt->execute();
        $rs = $stmt->fetch();
        try{
            if ($rs){
                $Data = Array(
                    'idclientaddress' => $rs['idclientaddress'],
                    'firstname' => $rs['firstname'],
                    'surname' => $rs['surname'],
                    'companyname' => $rs['companyname'],
                    'nip' => $rs['nip'],
                    'street' => $rs['street'],
                    'streetno' => $rs['streetno'],
                    'placeno' => $rs['placeno'],
                    'placename' => $rs['placename'],
                    'postcode' => $rs['postcode'],
                    'countryid' => $rs['countryid'],
                    'clienttype' => $rs['clienttype'],
                    'countryname' => $rs['countryname']
                );
            }
        }
        catch (Exception $e){
            throw new FrontendException(_('ERR_CLIENT_NO_EXIST'));
        }
        return $Data;
    }

    public function getCustomer ($user, $pass)
    {
        $result = App::getModel('ClientLogin')->authProccess($user, $pass);
        if (! $result){
            throw new ShopgateLibraryException(ShopgateLibraryException::PLUGIN_WRONG_USERNAME_OR_PASSWORD, 'Username or password is incorrect');
        }
        
        $sql = "SELECT
					C.`idclient`,
					AES_DECRYPT(CD.surname, :encryptionkey) AS surname,
					AES_DECRYPT(CD.firstname, :encryptionkey) AS firstname,
					AES_DECRYPT(CD.phone, :encryptionkey) AS phone,
					AES_DECRYPT(CD.phone2, :encryptionkey) AS phone2,
					AES_DECRYPT(CD.email, :encryptionkey) AS email,
					CD.`clientgroupid`,
					CGT.`name` AS clientgrouptranslation,
					CN.active AS newsletter
				FROM clientdata CD
				LEFT JOIN client C ON C.idclient=CD.clientid
				LEFT JOIN clientgrouptranslation CGT ON CD.`clientgroupid` = CGT.`clientgroupid`
				LEFT JOIN clientnewsletter AS CN ON CN.email = AES_DECRYPT(CD.email, :encryptionkey)
				WHERE C.idclient= :clientid AND C.viewid = :viewid";
        
        $stmt = \Gekosale\Db::getInstance()->prepare($sql);
        $stmt->bindValue('clientid', $result);
        $stmt->bindValue('viewid', \Gekosale\Helper::getViewId());
        $stmt->bindValue('encryptionkey', \Gekosale\Session::getActiveEncryptionKeyValue());
        $stmt->execute();
        $rs = $stmt->fetch();
        if ($rs){
            $shopgateCustomer = new ShopgateCustomer();
            $shopgateCustomer->setCustomerId($rs['idclient']);
            $shopgateCustomer->setCustomerNumber($rs['idclient']);
            $shopgateCustomer->setCustomerGroup($rs['clientgrouptranslation']);
            $shopgateCustomer->setCustomerGroupId($rs['clientgroupid']);
            $shopgateCustomer->setFirstName($rs['firstname']);
            $shopgateCustomer->setLastName($rs['surname']);
            $shopgateCustomer->setGender(null);
            $shopgateCustomer->setPhone($rs['phone']);
            $shopgateCustomer->setMail($rs['email']);
            $shopgateCustomer->setNewsletterSubscription($rs['newsletter']);
            
            $addresses = array();
            
            $address = new ShopgateAddress();
            
            $billingAddress = $this->getClientAddress($rs['idclient'], 1);
            
            $address->setId($billingAddress['idclientaddress']);
            $address->setFirstName($billingAddress['firstname']);
            $address->setLastName($billingAddress['surname']);
            $address->setCompany($billingAddress['companyname']);
            $address->setStreet1($billingAddress['street'] . ' ' . $billingAddress['streetno']);
            $address->setStreet2($billingAddress['placeno']);
            $address->setCity($billingAddress['placename']);
            $address->setZipcode($billingAddress['postcode']);
            $address->setCountry($billingAddress['countryname']);
            $address->setState(NULL);
            $address->setPhone($rs['phone2']);
            $address->setMobile($rs['phone']);
            $address->setIsInvoiceAddress(1);
            $address->setIsDeliveryAddress(0);
            
            array_push($addresses, $address);
            
            $shippingAddress = $this->getClientAddress($rs['idclient'], 0);
            
            $address->setId($shippingAddress['idclientaddress']);
            $address->setFirstName($shippingAddress['firstname']);
            $address->setLastName($shippingAddress['surname']);
            $address->setCompany($shippingAddress['companyname']);
            $address->setStreet1($shippingAddress['street'] . ' ' . $shippingAddress['streetno']);
            $address->setStreet2($shippingAddress['placeno']);
            $address->setCity($shippingAddress['placename']);
            $address->setZipcode($shippingAddress['postcode']);
            $address->setCountry($shippingAddress['countryname']);
            $address->setState(NULL);
            $address->setPhone($rs['phone2']);
            $address->setMobile($rs['phone']);
            $address->setIsInvoiceAddress(0);
            $address->setIsDeliveryAddress(1);
            
            array_push($addresses, $address);
            
            $shopgateCustomer->setAddresses($addresses);
            
            return $shopgateCustomer;
        }
    }

    public function addOrder (ShopgateOrder $order)
    {
    }

    public function cron ($jobname, $params, &$message, &$errorcount)
    {
        return;
    }

    public function updateOrder (ShopgateOrder $order)
    {
    }

    protected function formatPriceNumber ($price, $digits = 2, $decimalPoint = ".", $thousandPoints = "")
    {
        $price = round($price, $digits);
        $price = number_format($price, $digits, $decimalPoint, $thousandPoints);
        return $price;
    }

    public function getSimilarProductView ($id)
    {
        $sql = "SELECT
					P.idproduct,
					PT.name AS relatedproduct
				FROM similarproduct CS
				LEFT JOIN product P ON P.idproduct= CS.relatedproductid
				LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
				WHERE CS.productid=:id";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $Data[] = Array(
                'relatedproduct' => $rs['relatedproduct'],
                'relatedproductid' => $rs['idproduct']
            );
        }
        return $Data;
    }

    public function getUpsellProductView ($id)
    {
        $sql = "SELECT
					P.idproduct,
					PT.name AS relatedproduct
					FROM upsell CS
					LEFT JOIN product P ON P.idproduct= CS.relatedproductid
					LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
				WHERE CS.productid=:id";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $Data[] = Array(
                'relatedproduct' => $rs['relatedproduct'],
                'relatedproductid' => $rs['idproduct']
            );
        }
        return $Data;
    }

    public function getCrossSellProductView ($id)
    {
        $sql = "SELECT
					P.idproduct,
					PT.name AS relatedproduct
					FROM crosssell CS
					LEFT JOIN product P ON P.idproduct= CS.relatedproductid
					LEFT JOIN producttranslation PT ON P.idproduct = PT.productid AND PT.languageid = :languageid
					WHERE CS.productid=:id";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->bindValue('languageid', Helper::getLanguageId());
        $stmt->execute();
        $Data = Array();
        while ($rs = $stmt->fetch()){
            $Data[] = Array(
                'relatedproduct' => $rs['relatedproduct'],
                'relatedproductid' => $rs['idproduct']
            );
        }
        return $Data;
    }

    public function createItemsCsv ($test = true)
    {
        $products = Array();
        
        $sql = "SELECT idproduct from product";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->execute();
        $Data = $stmt->fetchAll();
        $productsModel = App::getModel('product');
        $items = array();
        foreach ($Data as $item){
            $items[] = $productsModel->getProductAndAttributesById($item['idproduct']);
        }
        
        foreach ($items as $item){
            
            $row = $this->buildDefaultItemRow();
            
            $defaultData['item_number'] = $item['idproduct'];
            $defaultData['item_name'] = $item['productname'];
            if ($item['discountprice'] != $item['price']){
                $defaultData['unit_amount'] = $this->formatPriceNumber($item['discountprice']);
            }
            else{
                $defaultData['unit_amount'] = $this->formatPriceNumber($item['price']);
            }
            
            $defaultData['currency'] = $item['currencysymbol'];
            $defaultData['tax_percent'] = $item['vatvalue'];
            $defaultData['description'] = trim(preg_replace('/\s+/', ' ', $item['shortdescription']));
            $defaultData['category_numbers'] = $item['categoryid'];
            $defaultData['is_available'] = ($item['enable'] == 1) ? 'active' : 'inactive';
            $defaultData['available_text'] = $item['availablityname'];
            $defaultData['manufacturer'] = $item['producername'];
            $defaultData['manufacturer_item_number'] = $item['ean'];
            $url = str_replace('shopgate.php/', '', App::getRegistry()->router->generate('frontend.productcart', true, Array(
                'param' => $item['seo']
            )));
            $defaultData['url_deeplink'] = $url;
            $defaultData['item_number_public'] = $item['ean'];
            if ($item['discountprice'] != $item['price']){
                $defaultData['old_unit_amount'] = $this->formatPriceNumber($item['price']);
            }
            else{
                $defaultData['old_unit_amount'] = '';
            }
            
            $properties = $productsModel->GetTechnicalDataForProduct($item['idproduct']);
            $out = '';
            
            if (! empty($properties[0]["attributes"])){
                foreach ($properties[0]["attributes"] as $properties_item){
                    $out .= $properties_item['name'] . '=>' . $properties_item['value'] . '||';
                }
            }
            
            $defaultData['properties'] = $out;
            
            $defaultData['msrp'] = '0.00';
            $defaultData['is_free_shipping'] = '0';
            $defaultData['use_stock'] = $item['trackstock'];
            $defaultData['stock_quantity'] = $item['stock'];
            $defaultData['active_status'] = $item['enable'];
            $defaultData['minimum_order_quantity'] = '1';
            $defaultData['maximum_order_quantity'] = $item['stock'];
            $defaultData['minimum_order_amount'] = '0.00';
            $defaultData['ean'] = $item['ean'];
            $defaultData['last_update'] = date('Y-m-d');
            $defaultData['sort_order'] = '0';
            $defaultData['is_highlight'] = '0';
            $similar = $this->getSimilarProductView($item['idproduct']);
            $upsell = $this->getUpsellProductView($item['idproduct']);
            $crosssell = $this->getCrossSellProductView($item['idproduct']);
            $related = array();
            foreach ($similar as $similar_item){
                $related[] = $similar_item['relatedproductid'];
            }
            
            foreach ($upsell as $upsell_item){
                $related[] = $upsell_item['relatedproductid'];
            }
            
            foreach ($crosssell as $crosssell_item){
                $related[] = $crosssell_item['relatedproductid'];
            }
            
            $related = array_unique($related);
            
            $defaultData['related_shop_item_numbers'] = implode('||', $related);
            
            $defaultData['weight'] = $item['weight'];
            $defaultData['has_children'] = '0';
            $defaultData['parent_item_number'] = '';
            $photos = '';
            $productsModel->getPhotos($item);
            $productsModel->getOtherPhotos($item);
            
            if (! empty($item['photo']['large'])){
                $photos = $item['photo']['large'][0];
            }
            
            if (! empty($item['otherphoto']))
                foreach ($item['otherphoto']['large'] as $photolarge){
                    $photos .= "||" . $photolarge;
                }
            
            $defaultData['urls_images'] = $photos;
            
            $attributeConnector = array();
            
            $getProductAttributeGroups = $productsModel->getProductAttributeGroups($item);
            
            if (! empty($item['attributes'])){
                $defaultData['has_children'] = '1';
                $defaultData['parent_item_number'] = '';
                $i = 0;
                foreach ($getProductAttributeGroups as $key => $productAttributeGroup){
                    $insert = $i + 1;
                    $defaultData['attribute_' . $insert] = $productAttributeGroup['name'];
                    $attributeConnector[$key] = $insert;
                    $i ++;
                }
            }
            $row = $defaultData;
            $products[] = $row;
            $this->addItemRow($row);
            
            if (! empty($item['attributes'])){
                $defaultData['has_children'] = '0';
                $defaultData['parent_item_number'] = $item['idproduct'];
                
                $i = 0;
                $last_idproductattributeset = NULL;
                
                foreach ($item['attributes'] as $attribute){
                    if ($last_idproductattributeset === NULL){
                        $last_idproductattributeset = $attribute['attributegroupid'];
                    }
                    $defaultData['item_number'] = $item['idproduct'] . '_' . $attribute['idproductattributevalueset'];
                    $insert = $attributeConnector[$attribute['attributegroupid']];
                    $defaultData['attribute_' . $insert] = $attribute['attributename'];
                    $defaultData['unit_amount'] = $this->formatPriceNumber($item['price']);
                    
                    if (count($getProductAttributeGroups) == 1){
                        $row = $defaultData;
                        $products[] = $row;
                        $this->addItemRow($row);
                    }
                    
                    $i ++;
                }
            }
        }
        
        if ($test){
            return $products;
        }
    }

    public function createCategoriesCsv ($test = false)
    {
        $categories = Array();
        
        $sql = "SELECT 
				c.idcategory AS category_number, 
				ct.name AS category_name, 
				c.categoryid as parent_id,
				c.photoid as url_image,
				c.distinction AS order_index, 
				c.enable AS is_active,
				ct.seo AS url_deeplink
				FROM `category` c
				LEFT JOIN `categorytranslation` ct ON ct.categoryid = c.idcategory
			";
        
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->execute();
        $Data = $stmt->fetchAll();
        
        foreach ($Data as $category){
            $row = $this->buildDefaultCategoryRow();
            $row['category_number'] = $category['category_number'];
            $row['category_name'] = $category['category_name'];
            $row['parent_id'] = (empty($category['parent_id'])) ? "" : $category['parent_id'];
            $row['url_image'] = $this->getLargestCategoryImage($category['url_image']);
            $row['order_index'] = 1000 - $category['order_index'];
            $row['is_active'] = $category['is_active'];
            $row['url_deeplink'] = $this->generateCategoryUrl($category['url_deeplink']);
            $this->addCategoryRow($row);
            $categories[] = $row;
        }
        
        if ($test){
            return $categories;
        }
    }

    public function getLargestCategoryImage ($photoid = NULL)
    {
        if (! empty($photoid)){
            return App::getModel('gallery')->getImagePath(App::getModel('gallery')->getOrginalImageById($photoid));
        }
        
        return NULL;
    }

    protected function generateCategoryUrl ($seo)
    {
        return str_replace('shopgate.php/', '', App::getRegistry()->router->generate('frontend.categorylist', true, Array(
            'param' => $seo
        )));
    }

    public function createReviewsCsv ($test = false)
    {
        $reviews = Array();
        
        $sql = "SELECT
					pr.productid AS item_number,
					pr.`idproductreview` AS update_review_id,
					IF(AVG(pra.value) IS NULL, 0, ROUND(AVG(pra.value))) AS score,
					pr.nick AS name,
					pr.adddate AS date,
					'' AS title,
					pr.review AS text
					FROM `productreview` pr
					LEFT JOIN `productrange` pra ON pra.productreviewid = pr.idproductreview
					WHERE pr.enable = 1";
        $stmt = Db::getInstance()->prepare($sql);
        $stmt->execute();
        $Data = $stmt->fetchAll();
        foreach ($Data as $review){
            $row = $this->buildDefaultReviewRow();
            $row['item_number'] = $review['item_number'];
            $row['update_review_id'] = $review['update_review_id'];
            $row['score'] = $review['score'];
            $row['name'] = $review['name'];
            $row['date'] = date('Y-m-d', strtotime($review['date']));
            $row['title'] = $review['title'];
            $row['text'] = $review['text'];
            $reviews[] = $row;
            $this->addReviewRow($row);
        }
        
        if ($test){
            return $reviews;
        }
    }

    public function checkCart (ShopgateCart $shopgateCart)
    {
    }

    public function redeemCoupons (ShopgateCart $shopgateCart)
    {
    }

    public function getRedirect ()
    {
        return $this->builder->buildRedirect();
    }
}