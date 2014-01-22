<?php

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2011 Gekosale. Zabronione jest usuwanie informacji o
 * licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 *
 * $Revision: 279 $
 * $Author: gekosale $
 * $Date: 2011-07-28 23:13:43 +0200 (Cz, 28 lip 2011) $
 * $Id: product.php 279 2011-07-28 21:13:43Z gekosale $
 */
namespace Gekosale\Plugin;

class TemplateDataModel extends Component\Model
{

    public function __construct ($registry, $modelFile)
    {
        parent::__construct($registry, $modelFile);
        $this->layer = $this->registry->loader->getCurrentLayer();
        $this->languages = App::getModel('language')->getLanguages();
        $this->templateData = Array();
        $this->setSuperVariables();
        if ($this->registry->router->getAdministrativeMode() == 0){
            $this->setFrontendVariables();
        }
        else{
            $this->setAdminVariables();
        }
    }

    public function getTemplateData ()
    {
        $Data = Array();
        foreach ($this->templateData as $key => $templateVar){
            if (is_array($templateVar)){
                foreach ($templateVar as $varName => $varValue){
                    $Data[$varName] = $varValue;
                }
            }
        }
        return $Data;
    }

    protected function setSuperVariables ()
    {
        $link = ($this->registry->router->getAdministrativeMode() == 0) ? $this->_adminPane = '' : $this->_adminPane = __ADMINPANE__ . '/';
        $theme = App::getRegistry()->loader->getParam('theme');
        
        $this->templateData[] = Array(
            'SSLNAME' => (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') ? 'https://' : 'http://',
            'URL' => App::getURLAdress() . $link,
            'CURRENT_URL' => App::getCurrentURLAdress(),
            'DESIGNPATH' => DESIGNPATH,
            'ASSETSPATH' => App::getURLForAssetDirectory() . $theme . '/assets/',
            'THEMESPATH' => App::getURLForAssetDirectory(),
            'CURRENT_CONTROLLER' => $this->registry->router->getCurrentController(),
            'CURRENT_ACTION' => $this->registry->router->getAction(),
            'CURRENT_PARAM' => $this->registry->core->getParam(),
            'SHOP_NAME' => App::getContainer()->get('session')->getActiveShopName(),
            'language' => App::getContainer()->get('session')->getActiveLanguageId(),
            'view' => Helper::getViewId(),
            'viewid' => Helper::getViewId(),
        );
        
        $message = App::getContainer()->get('session')->getVolatileMessage();
        if (isset($message) && ! empty($message)){
            $this->templateData[] = Array(
                'message' => $message[0]
            );
        }
    }

    protected function setFrontendVariables ()
    {
        $cartModel = App::getModel('cart');
        $client = App::getModel('client')->getClient();
        $productCart = $cartModel->getShortCartList();
        $productCart = $cartModel->getProductCartPhotos($productCart);
        
        $compareProducts = App::getModel('productcompare')->getProducts();
        $compareIds = Array();
        foreach ($compareProducts as $product){
            $compareIds[] = $product['id'];
        }
        
        $shippingCountryId = ((int) App::getContainer()->get('session')->getActiveDeliveryCountry() == 0) ? $this->registry->loader->getParam('countryid') : App::getContainer()->get('session')->getActiveDeliveryCountry();
        
        
        $this->templateData[] = Array(
            'SHOP_LOGO' => $this->layer['photoid'],
            'FAVICON' => $this->layer['favicon'],
            'enableregistration' => $this->layer['enableregistration'],
            'client' => $client,
            'clientdata' => $client,
            'compareproductsids' => $compareIds,
            'compareproducts' => $compareProducts,
            'showtax' => $this->layer['showtax'],
            'currencySymbol' => App::getContainer()->get('session')->getActiveCurrencySymbol(),
            'count' => $cartModel->getProductAllCount(),
            'globalPrice' => $cartModel->getGlobalPrice(),
            'productCart' => $productCart,
            'productCartCombinations' => $cartModel->getProductCartCombinations(),
            'languageCode' => App::getContainer()->get('session')->getActiveLanguage(),
            'languageFlag' => $this->languages,
            'currencies' => App::getModel('language')->getAllCurrenciesForView(),
            'breadcrumb' => App::getModel('breadcrumb')->getPageLinks(),
            'contentcategory' => App::getModel('staticcontent')->getContentCategoriesTree(),
            'producers' => App::getModel('producerlist')->getProducerAll(),
            'defaultcontact' => App::getModel('contact')->getContactById($this->layer['contactid']),
            'gacode' => $this->layer['gacode'],
            'newsletterButton' => App::getModel('newsletter')->isNewsletterButton(),
            'gapages' => $this->layer['gapages'],
            'gatransactions' => $this->layer['gatransactions'],
            'cartpreview' => $cartModel->getCartPreviewTemplate(),
            'cartredirect' => ($this->layer['cartredirect'] != '') ? App::getURLAdress() . Seo::getSeo($this->layer['cartredirect']) : '',
            'modulesettings' => $this->registry->core->getModuleSettingsForView(),
            'footerJS' => App::getModel('staticcontent')->renderPolicyJS(),
            'categories' => App::getModel('CategoriesBox')->getCategoriesTree(2),
            'layerData' => $this->layer,
            'newsletterCookie' => (isset($_COOKIE['newsletter']) ? $_COOKIE['newsletter'] : 0),
            'countries' => App::getModel('lists')->getCountryForSelect(),
            'countrySelected' => $shippingCountryId,
            'header_path' => App::getModel('categoriesbox')->getCurrentCategoryPath($this->getParam()),
            'analyticsjs' => App::getModel('googleanalytics')->getGoogleAnalyticsJs()
        );
    }

    protected function setAdminVariables ()
    {
        $this->templateData[] = Array(
            'user_name' => App::getModel('users')->getUserFullName(),
            'user_id' => App::getModel('users')->getActiveUserid(),
            'daysremaining' => App::getContainer()->get('session')->getActiveAccountDaysRemaining(),
            'languages' => json_encode($this->languages),
            'globalsettings' => App::getContainer()->get('session')->getActiveGlobalSettings(),
            'views' => App::getModel('view')->getViews(),
            'vatvalues' => json_encode(App::getModel('vat/vat')->getVATValuesAll()),
            'FRONTEND_URL' => (App::getContainer()->get('session')->getActiveShopUrl() != '') ? 'http://' . App::getContainer()->get('session')->getActiveShopUrl() : App::getURLAdress(),
            'appversion' => App::getContainer()->get('session')->getActiveAppVersion()
        );
    }

    public function getXajaxMethods ()
    {
        return ($this->registry->router->getAdministrativeMode() == 0) ? $this->getXajaxMethodsForFrontend() : $this->getXajaxMethodsForAdmin();
    }

    protected function getXajaxMethodsForFrontend ()
    {
        $Data = Array(
            'changeLanguage' => Array(
                'model' => 'language',
                'method' => 'changeAJAXLanguageAboutView'
            ),
            'changeCurrency' => Array(
                'model' => 'language',
                'method' => 'changeAJAXCurrencyView'
            ),
            'updateCartPreview' => Array(
                'model' => 'cart',
                'method' => 'updateCartPreview'
            ),
            'deleteProductFromCart' => Array(
                'model' => 'cart',
                'method' => 'deleteAJAXProductFromCart'
            ),
            'deleteCombinationFromCart' => Array(
                'model' => 'cart',
                'method' => 'deleteAJAXsCombinationFromCart'
            ),
            'addNewsletter' => Array(
                'model' => 'newsletter',
                'method' => 'addAJAXClientAboutNewsletter'
            ),
            'doQuickLogin' => Array(
                'model' => 'clientlogin',
                'method' => 'authProccessQuick'
            ),
            'doQuickAddCart' => Array(
                'model' => 'cart',
                'method' => 'doQuickAddCart'
            ),
            'doSearchQuery' => Array(
                'model' => 'productsearch',
                'method' => 'doSearchQuery'
            ),
            'addProductToCart' => Array(
                'model' => 'cart',
                'method' => 'addAJAXProductToCart'
            ),
            'addProductToWishList' => Array(
                'model' => 'wishlist',
                'method' => 'addAjaxProductToWishlist'
            ),
            'deleteProductFromCompare' => Array(
                'model' => 'productcompare',
                'method' => 'ajaxDeleteProduct'
            ),
            'deleteAllProductsFromCompare' => Array(
                'model' => 'productcompare',
                'method' => 'ajaxDeleteAllProducts'
            ),
            'compareProducts' => Array(
                'model' => 'productcompare',
                'method' => 'ajaxCompareProducts'
            ),
            'addProductToCompare' => Array(
                'model' => 'productcompare',
                'method' => 'ajaxAddProduct'
            ),
            'setDispatchmethodCountry' => Array(
                'model' => 'delivery',
                'method' => 'setDispatchmethodCountry'
            )
        );
        
        return $Data;
    }

    protected function getXajaxMethodsForAdmin ()
    {
        $Data = Array(
            'ChangeInterfaceLanguage' => Array(
                'model' => 'language',
                'method' => 'changeLanguage'
            ),
            'ChangeActiveView' => Array(
                'model' => 'view',
                'method' => 'changeActiveView'
            ),
            'doAJAXCreateSeo' => Array(
                'model' => 'seo',
                'method' => 'doAJAXCreateSeo'
            )
        );
        
        return $Data;
    }
}