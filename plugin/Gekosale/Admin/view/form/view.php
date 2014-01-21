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
 * $Revision: 619 $
 * $Author: gekosale $
 * $Date: 2011-12-19 22:09:00 +0100 (Pn, 19 gru 2011) $
 * $Id: news.php 619 2011-12-19 21:09:00Z gekosale $
 */
namespace Gekosale;
use FormEngine;

class ViewForm extends Component\Form
{

    protected $registry;

    protected $populateData;

    public function __construct ($registry)
    {
        parent::__construct($registry);
        $this->registry = $registry;
    }

    public function setPopulateData ($Data)
    {
        $this->populateData = $Data;
    }

    public function initForm ()
    {
        $form = new FormEngine\Elements\Form(Array(
            'name' => 'view',
            'action' => '',
            'method' => 'post'
        ));
        
        $requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'required_data',
            'label' => $this->trans('TXT_MAIN_DATA')
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'name',
            'label' => $this->trans('TXT_NAME'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_NAME'))
            )
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\Hidden(Array(
            'name' => 'namespace'
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p align="center">Wybierz domyślny szablon sklepu. Szablonami możesz zarządzać na stronie <a href="' . App::getURLAdressWithAdminPane() . 'templateeditor' . '" target="_blank">Szablony stylów &raquo; Biblioteka szablonów</a>.</p>',
            'direction' => FormEngine\Elements\Tip::DOWN
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'pageschemeid',
            'label' => $this->trans('TXT_PAGESCHEME'),
            'options' => FormEngine\Option::Make($this->registry->core->getDefaultValueToSelect() + App::getModel('pagescheme')->getPageschemeAllToSelect()),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PAGESCHEME'))
            )
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p align="center">Wybierz firmę/podmiot obsługujący sklep. Firmami możesz zarządzać na stronie <a href="' . App::getURLAdressWithAdminPane() . 'store' . '" target="_blank">Konfiguracja &raquo; Zarządzanie sklepem &raquo; Lista firm</a>.</p>',
            'direction' => FormEngine\Elements\Tip::DOWN
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'store',
            'label' => $this->trans('TXT_STORE'),
            'options' => FormEngine\Option::Make($this->registry->core->getDefaultValueToSelect() + App::getModel('store')->getStoreToSelect()),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_STORE'))
            )
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'currencyid',
            'label' => $this->trans('TXT_DEFAULT_VIEW_CURRENCY'),
            'options' => FormEngine\Option::Make(App::getModel('currencieslist')->getCurrencyForSelect()),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_KIND_OF_CURRENCY'))
            )
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p align="center">Wybierz domyślny kontakt dla sklepu. Kontaktami możesz zarządzać na stronie <a href="' . App::getURLAdressWithAdminPane() . 'contact' . '" target="_blank">Konfiguracja &raquo; Zarządzanie sklepem &raquo; Lista kontaktów</a>.</p>',
            'direction' => FormEngine\Elements\Tip::DOWN
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'contactid',
            'label' => $this->trans('TXT_DEFAULT_CONTACT'),
            'options' => FormEngine\Option::Make($this->registry->core->getDefaultValueToSelect() + App::getModel('contact')->getContactToSelect())
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'showtax',
            'label' => $this->trans('TXT_SHOW_TAX_VALUE'),
            'options' => FormEngine\Option::Make(App::getModel('suffix/suffix')->getPrice()),
            'default' => 1
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'defaultvatid',
            'label' => $this->trans('TXT_DEFAULT_VAT'),
            'options' => FormEngine\Option::Make(App::getModel('vat')->getVATAll()),
            'default' => 2
        )));
        
        $offline = $requiredData->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'offline',
            'label' => $this->trans('TXT_SHOP_OFFLINE'),
            'comment' => $this->trans('TXT_OFFLINE_INSTRUCTION')
        )));
        
        $requiredData->AddChild(new FormEngine\Elements\Textarea(Array(
            'name' => 'offlinetext',
            'label' => $this->trans('TXT_OFFLINE_MESSAGE'),
            'comment' => $this->trans('TXT_MAX_LENGTH') . ' 5000',
            'rows' => 50,
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $offline, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            )
        )));
        
        $policyData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'policy_data',
            'label' => 'Polityka cookies'
        )));
        
        $policyData->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'privacypolicyenabled',
            'label' => 'Włącz pasek informacyjny',
            'comment' => 'W sklepie wyświetlany będzie pasek informujący o polityce cookies'
        )));
        
        $policyData->AddChild(new FormEngine\Elements\Tip(Array(
            'direction' => FormEngine\Elements\Tip::DOWN,
            'tip' => '<p>Wybierz stronę informacyjną z polityką cookies.</p>
					  <p><strong>Więcej szczegółów znajdziecie w dokumentach do pobrania:</strong><br>
1.&nbsp;<a href="http://mail.freshmail.pl/c/3t511nldxi/u7afifp9hy/" target="_blank">Wytyczne oraz opis działań</a><br>
2.&nbsp;<a href="http://mail.freshmail.pl/c/ipb5dhvqtz/u7afifp9hy/" target="_blank">Wzorcowa polityka cookies</a></p>'
        )));
        
        $policyData->AddChild(new FormEngine\Elements\Tree(Array(
            'name' => 'privacypolicyid',
            'label' => $this->trans('TXT_CATEGORY'),
            'choosable' => true,
            'selectable' => false,
            'sortable' => false,
            'clickable' => false,
            'items' => App::getModel('contentcategory')->getContentCategoryALL()
        )));
        
        $metaData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'meta_data',
            'label' => $this->trans('TXT_META_INFORMATION')
        )));
        
        $languageData = $metaData->AddChild(new FormEngine\Elements\FieldsetLanguage(Array(
            'name' => 'language_data',
            'label' => $this->trans('TXT_LANGUAGE_DATA')
        )));
        
        $languageData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'keyword_title',
            'label' => $this->trans('TXT_KEYWORD_TITLE')
        )));
        
        $languageData->AddChild(new FormEngine\Elements\Textarea(Array(
            'name' => 'keyword_description',
            'label' => $this->trans('TXT_KEYWORD_DESCRIPTION')
        )));
        
        $languageData->AddChild(new FormEngine\Elements\Textarea(Array(
            'name' => 'keyword',
            'label' => $this->trans('TXT_KEYWORDS'),
            'comment' => $this->trans('TXT_KEYWORDS_HELP')
        )));
        
        $languageData->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p align="center">Możesz dodać dowolne znaczniki do sekcji HEAD szablonu sklepu. Wykorzystaj to pole np. do przeprowadzenia weryfikacji domeny dla Google Apps.</p>',
            'direction' => FormEngine\Elements\Tip::DOWN
        )));
        
        $languageData->AddChild(new FormEngine\Elements\Textarea(Array(
            'name' => 'additionalmeta',
            'label' => $this->trans('TXT_ADDITIONAL_META'),
            'rows' => 10
        )));
        
        $url = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'url_pane',
            'label' => $this->trans('TXT_WWW')
        )));
        
        $url->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p>Wybierz domenę główną dla sklepu. Jeżeli chcesz dodać nową domenę, zapoznaj się z naszym <a target="_blank" href="http://wellcommerce.pl/zasoby/konfiguracja/jak-dodac-wlasna-domene/">poradnikiem</a> a następnie kliknij przycisk obok listy wyboru i podaj jej nazwę.</p>'
        )));
        
        $url->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'url',
            'label' => $this->trans('TXT_URL'),
            'addable' => true,
            'onAdd' => 'xajax_AddDomain',
            'add_item_prompt' => 'Podaj nazwę domeny',
            'options' => FormEngine\Option::Make(App::getModel('view')->getUrlAddressesForInstance())
        )));
        
        $url->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p>Po zaznaczeniu pola użytkownik wchodząc z <b>http://www.adres_sklepu.pl</b> zostanie przekierowany na <b>http://adres_sklepu.pl</b>. Przy odznaczeniu pola zasada działania jest odwrotna.</p>'
        )));
        
        $url->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'wwwredirection',
            'label' => 'Przekierowanie WWW'
        )));
        
        $categoryPane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'category_pane',
            'label' => $this->trans('TXT_CATEGORY')
        )));
        
        $categories = App::getModel('view')->getChildCategories();
        
        if (count($categories) > 0){
            $categoryPane->AddChild(new FormEngine\Elements\StaticText(Array(
                'text' => '<p>' . $this->trans('TXT_VIEW_CATEGORY_INSTRUCTION') . '</p>'
            )));
        }
        else{
            $categoryPane->AddChild(new FormEngine\Elements\StaticText(Array(
                'text' => '<p>' . $this->trans('TXT_VIEW_CATEGORY_EMPTY_INSTRUCTION') . '</p>'
            )));
        }
        $category = $categoryPane->AddChild(new FormEngine\Elements\Tree(Array(
            'name' => 'category',
            'label' => $this->trans('TXT_CATEGORY'),
            'sortable' => false,
            'selectable' => true,
            'clickable' => false,
            'items' => $categories,
            'load_children' => Array(
                App::getModel('view'),
                'getChildCategories'
            )
        )));
        
        $dispatchmethodPane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'dispatchmethod_pane',
            'label' => $this->trans('TXT_DISPATCHMETHOD_PANE')
        )));
        
        $dispatchmethodPane->AddChild(new FormEngine\Elements\MultiSelect(Array(
            'name' => 'dispatchmethod',
            'label' => $this->trans('TXT_DISPATCHMETHOD'),
            'options' => FormEngine\Option::Make(App::getModel('dispatchmethod')->getDispatchmethodToSelect())
        )));
        
        $paymentmethodPane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'paymentmethod_pane',
            'label' => $this->trans('TXT_PAYMENTMETHOD_PANE')
        )));
        
        $paymentmethodPane->AddChild(new FormEngine\Elements\MultiSelect(Array(
            'name' => 'paymentmethod',
            'label' => $this->trans('TXT_PAYMENTMETHOD'),
            'options' => FormEngine\Option::Make(App::getModel('paymentmethod')->getPaymentmethodToSelect())
        )));
        
        $giftwrapPane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'giftwrap_pane',
            'label' => $this->trans('TXT_GIFTWRAP')
        )));
        
        $enableGiftwrap = $giftwrapPane->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'enablegiftwrap',
            'label' => $this->trans('TXT_ENABLE_GIFTWRAP')
        )));
        
        $giftwrapPane->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p>' . $this->trans('TXT_GIFTWRAP_PRODUCT') . '</p>'
        )));
        
        $giftwrapPane->AddChild(new FormEngine\Elements\ProductSelect(Array(
            'name' => 'giftwrapproduct',
            'repeat_min' => 1,
            'repeat_max' => 1
        )));
        
        $assignToGroupData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'assigntogroup_data',
            'label' => $this->trans('TXT_AUTOMATICLY_ASSIGN_TO_GROUP')
        )));
        
        $assignToGroupData->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p>Automatyczny awans umożliwia przechodzenie klientom Twojego sklepu do wyższych grup rabatowych w zależności od tego ile zakupów zrealizują w określonym czasie. Przynależność do danej grupy weryfikowana jest za każdym zalogowaniem się klienta do sklepu. Wtedy też następuje jej ewentualna zmiana.</p>',
            'direction' => FormEngine\Elements\Tip::DOWN
        )));
        
        $assignToGroupData->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'taxes',
            'label' => $this->trans('TXT_TAKE_THE_VALUE'),
            'options' => Array(
                new FormEngine\Option('0', $this->trans('TXT_NETTO')),
                new FormEngine\Option('1', $this->trans('TXT_PRICE_GROSS'))
            ),
            'suffix' => $this->trans('TXT_CLIENT_ORDERS')
        )));
        
        $assignToGroupData->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'periodid',
            'label' => $this->trans('TXT_PERIOD'),
            'options' => FormEngine\Option::Make(App::getModel('period/period')->getPeriod())
        )));
        
        $assignToGroupData->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'orderstatusgroupsid',
            'label' => $this->trans('TXT_ORDER_STATUS_GROUPS'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_ORDER_STATUS_GROUPS'))
            ),
            'options' => FormEngine\Option::Make(App::getModel('orderstatusgroups/orderstatusgroups')->getOrderStatusGroupsAllToSelect())
        )));
        
        $assignToGroupData->AddChild(new FormEngine\Elements\RangeEditor(Array(
            'name' => 'table',
            'label' => $this->trans('TXT_DISPATCHMETHOD_TABLE_PRICE'),
            'suffix' => $this->trans('TXT_CURRENCY'),
            'range_suffix' => $this->trans('TXT_CURRENCY'),
            'options' => FormEngine\Option::Make(App::getModel('clientgroup')->getClientGroupToRangeEditor())
        )));
        
        $googleappsData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'googleapps_data',
            'label' => $this->trans('TXT_GOOGLEAPPS_DATA')
        )));
        
        $googleappsData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'googleappstag',
            'label' => $this->trans('TXT_GOOGLEAPPS_TAG')
        )));
        $googleappsData->AddChild(new FormEngine\Elements\LocalFile(Array(
            'name' => 'googleappsfile',
            'label' => $this->trans('TXT_GOOGLEAPPS_FILE'),
            'file_source' => 'upload/',
            'file_types' => Array(
                'html'
            )
        )));
        
        $registrationData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'registration_data',
            'label' => $this->trans('TXT_REGISTRATION_SETTINGS')
        )));
        
        $registrationData->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'forcelogin',
            'label' => $this->trans('TXT_FORCE_CLIENT_LOGIN'),
            'comment' => $this->trans('TXT_FORCE_CLIENT_LOGIN_HELP')
        )));
        
        $registrationData->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'enableregistration',
            'label' => $this->trans('TXT_ENABLE_REGISTRATION'),
            'comment' => $this->trans('TXT_ENABLE_REGISTRATION_HELP')
        )));
        
        $registrationData->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'confirmregistration',
            'label' => $this->trans('TXT_REGISTRATION_CONFIRM'),
            'comment' => $this->trans('TXT_REGISTRATION_CONFIRM_HELP')
        )));
        
        $cartData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'cart_data',
            'label' => $this->trans('TXT_CART_SETTINGS')
        )));
        
        $cartData->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'cartredirect',
            'label' => $this->trans('TXT_CART_REDIRECT')
        )));
        
        $cartData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'minimumordervalue',
            'label' => $this->trans('TXT_MINIMUM_ORDER_VALUE'),
            'rules' => Array(
                new FormEngine\Rules\Format($this->trans('ERR_NUMERIC_INVALID'), '/[0-9]{1,}/')
            ),
            'default' => 0
        )));
        
        $photosPane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'logo',
            'label' => $this->trans('TXT_LOGO')
        )));
        
        $photosPane->AddChild(new FormEngine\Elements\LocalFile(Array(
            'name' => 'photo',
            'label' => $this->trans('TXT_LOGO'),
            'file_source' => 'design/_images_frontend/core/logos/',
            'file_types' => Array(
                'png',
                'jpg',
                'gif'
            )
        )));
        
        $photosPane->AddChild(new FormEngine\Elements\LocalFile(Array(
            'name' => 'favicon',
            'label' => $this->trans('TXT_FAVICON'),
            'file_source' => 'design/_images_frontend/core/logos/',
            'file_types' => Array(
                'ico'
            )
        )));
        
        $photosPane->AddChild(new FormEngine\Elements\LocalFile(Array(
            'name' => 'watermark',
            'label' => $this->trans('TXT_WATERMARK'),
            'file_source' => 'design/_images_frontend/core/logos/',
            'file_types' => Array(
                'png'
            )
        )));
        
        $mailerdata = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'mailer_data',
            'label' => $this->trans('TXT_MAIL_SETTINGS')
        )));
        
        $mailerType = $mailerdata->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'mailer',
            'label' => $this->trans('TXT_MAIL_TYPE'),
            'options' => FormEngine\Option::Make(Array(
                'mail' => 'mail',
                'sendmail' => 'sendmail',
                'smtp' => 'smtp'
            ))
        )));
        
        $mailerdata->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'server',
            'label' => $this->trans('TXT_MAIL_SERVER'),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $mailerType, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('smtp')))
            ),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_MAIL_SERVER'))
            )
        )));
        
        $mailerdata->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'port',
            'label' => $this->trans('TXT_MAIL_SERVER_PORT'),
            'default' => 587,
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $mailerType, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('smtp')))
            ),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_MAIL_SERVER_PORT'))
            )
        )));
        
        $mailerdata->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'smtpsecure',
            'label' => $this->trans('TXT_MAIL_SMTP_SECURE'),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $mailerType, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('smtp')))
            ),
            'options' => FormEngine\Option::Make(Array(
                '' => 'brak',
                'ssl' => 'ssl',
                'tls' => 'tls'
            )),
            'value' => ''
        )));
        
        $mailerdata->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'smtpauth',
            'label' => $this->trans('TXT_MAIL_SMTP_AUTH'),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $mailerType, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('smtp')))
            )
        )));
        
        $mailerdata->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'smtpusername',
            'label' => $this->trans('TXT_MAIL_SMTP_USERNAME'),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $mailerType, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('smtp')))
            )
        )));
        
        $mailerdata->AddChild(new FormEngine\Elements\Password(Array(
            'name' => 'smtppassword',
            'label' => $this->trans('TXT_MAIL_SMTP_PASSWORD'),
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $mailerType, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('smtp')))
            )
        )));
        
        $mailerdata->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'fromname',
            'label' => $this->trans('TXT_MAIL_FROMNAME'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_MAIL_FROMNAME'))
            )
        )));
        
        $mailerdata->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'fromemail',
            'label' => $this->trans('TXT_MAIL_FROMEMAIL'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_MAIL_FROMEMAIL'))
            )
        )));
        
        $orderUploaderData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'order_uploader_data',
            'label' => $this->trans('TXT_ORDER_UPLOADER_DATA')
        )));
        
        $uploaderenabled = $orderUploaderData->AddChild(new FormEngine\Elements\Checkbox(Array(
            'name' => 'uploaderenabled',
            'label' => $this->trans('TXT_ORDER_UPLOADER_ENABLED')
        )));
        
        $orderUploaderData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'uploadmaxfilesize',
            'label' => $this->trans('TXT_ORDER_UPLOADER_MAX_FILESIZE'),
            'rules' => Array(
                new FormEngine\Rules\Format($this->trans('ERR_NUMERIC_INVALID'), '/[0-9]{1,}/')
            ),
            'suffix' => 'mb',
            'default' => 10,
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $uploaderenabled, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            )
        )));
        
        $orderUploaderData->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'uploadchunksize',
            'label' => $this->trans('TXT_ORDER_UPLOADER_CHUNKSIZE'),
            'rules' => Array(
                new FormEngine\Rules\Format($this->trans('ERR_NUMERIC_INVALID'), '/[0-9]{1,}/')
            ),
            'suffix' => 'kb',
            'default' => 100,
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $uploaderenabled, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            )
        )));
        
        $allowedExtensions = array(
            'csv',
            'xml',
            'png',
            'gif',
            'jpg',
            'jpeg',
            'txt',
            'doc',
            'xls',
            'mpp',
            'pdf',
            'vsd',
            'ppt',
            'docx',
            'xlsx',
            'pptx',
            'tif',
            'zip',
            'tgz',
            'html'
        );
        
        natsort($allowedExtensions);
        
        foreach ($allowedExtensions as $key){
            $tmp[] = new FormEngine\Option($key, $key);
        }
        
        $orderUploaderData->AddChild(new FormEngine\Elements\MultiSelect(Array(
            'name' => 'uploadextensions',
            'label' => $this->trans('TXT_ORDER_UPLOADER_ALLOWED_EXTENSIONS'),
            'options' => $tmp,
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $uploaderenabled, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            )
        )));
        
        $orderUploaderData->AddChild(new FormEngine\Elements\StaticText(Array(
            'text' => '<a href="#" id="select-extensions" style="position: relative;left: 195px;top: -5px;">' . $this->trans('TXT_SELECT_ALL') . '</a>',
            'dependencies' => Array(
                new FormEngine\Dependency(FormEngine\Dependency::HIDE, $uploaderenabled, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
            )
        )));
        
        $invoicedata = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'invoice_data',
            'label' => $this->trans('TXT_INVOICE')
        )));
        
        $invoicedata->AddChild(new FormEngine\Elements\Select(Array(
            'name' => 'invoicenumerationkind',
            'label' => $this->trans('TXT_INVOICE_NUMERATION'),
            'options' => FormEngine\Option::Make(App::getModel('invoice')->getInvoiceNumerationTypes())
        )));
        
        $invoicedata->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'invoicedefaultpaymentdue',
            'label' => $this->trans('TXT_INVOICE_DEFAULT_PAYMENT_DUE'),
            'suffix' => $this->trans('TXT_DAYS'),
            'rules' => Array(
                new FormEngine\Rules\Format($this->trans('ERR_NUMERIC_INVALID'), '/[0-9]{1,}/')
            )
        )));
        
        $Data = Event::dispatch($this, 'admin.view.initForm', Array(
            'form' => $form,
            'id' => (int) $this->registry->core->getParam(),
            'data' => $this->populateData
        ));
        
        if (! empty($Data)){
            $form->Populate($Data);
        }
        
        return $form;
    }
}