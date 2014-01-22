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
 * $Id: cartbox.php 612 2011-11-28 20:02:10Z gekosale $
 */
namespace Gekosale\Plugin;
use SimpleForm;
use xajaxResponse;

class CheckoutBoxController extends Component\Controller\Box
{

    public function __construct ($registry, $box)
    {
        parent::__construct($registry, $box);
        $this->clientModel = App::getModel('client');
    }

    public function index ()
    {
        $clientorder = App::getModel('finalization')->setClientOrder();
        
        if (empty($clientorder['cart'])){
            App::redirectUrl($this->registry->router->generate('frontend.cart', true));
        }
        
        if (App::getContainer()->get('session')->getActiveClientid() == NULL){
            
            $formLogin = App::getFormModel('clientlogin')->initForm();
            
            if ($formLogin->Validate()){
                $formLoginData = $formLogin->getSubmitValues();
                $result = App::getModel('clientlogin')->authProccess($formLoginData['login'], $formLoginData['password']);
                if ($result > 0){
                    App::getContainer()->get('session')->setActiveClientid($result);
                    App::getModel('clientlogin')->checkClientGroup();
                    App::getModel('clientlogin')->setLoginTime();
                    $this->clientModel->saveClientData();
                    $misingCart = App::getModel('missingcart')->checkMissingCartForClient(App::getContainer()->get('session')->getActiveClientid());
                    if (is_array($misingCart) && $misingCart != 0){
                        App::getModel('cart')->addProductsToCartFromMissingCart($misingCart);
                        App::getModel('missingcart')->cleanMissingCart(App::getContainer()->get('session')->getActiveClientid());
                    }
                    if (($this->Cart = App::getContainer()->get('session')->getActiveCart()) != NULL){
                        App::redirectUrl($this->registry->router->generate('frontend.checkout', true));
                    }
                    else{
                        App::redirectUrl($this->registry->router->generate('frontend.home', true));
                    }
                }
                elseif ($result < 0){
                    App::getContainer()->get('session')->setVolatileUserLoginError(2, false);
                }
                else{
                    App::getContainer()->get('session')->setVolatileUserLoginError(1, false);
                }
            }
            
            $error = App::getContainer()->get('session')->getVolatileUserLoginError();
            if ($error[0] == 1){
                $this->registry->template->assign('loginerror', $this->trans('ERR_BAD_LOGIN_OR_PASSWORD'));
            }
            elseif ($error[0] == 2){
                $this->registry->template->assign('loginerror', $this->trans('TXT_BLOKED_USER'));
            }
            $this->registry->template->assign('formLogin', $formLogin->getForm());
        }
        
        $form = new SimpleForm\Form(Array(
            'name' => 'order',
            'action' => '',
            'method' => 'post'
        ));
        
        $billingClientType = $form->AddChild(new SimpleForm\Elements\Radio(Array(
            'name' => 'billing_clienttype',
            'label' => $this->trans('TXT_CLIENT_TYPE'),
            'options' => Array(
                '1' => $this->trans('TXT_INDIVIDUAL_CLIENT'),
                '2' => $this->trans('TXT_COMPANY_CLIENT')
            )
        )));
        
        $form->AddChild(new SimpleForm\Elements\TextField(Array(
            'name' => 'billing_firstname',
            'label' => $this->trans('TXT_FIRSTNAME'),
            'rules' => Array(
                new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_FIRSTNAME'))
            )
        )));
        
        $form->AddChild(new SimpleForm\Elements\TextField(Array(
            'name' => 'billing_surname',
            'label' => $this->trans('TXT_SURNAME'),
            'rules' => Array(
                new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_SURNAME'))
            )
        )));
        
        $form->AddChild(new SimpleForm\Elements\TextField(Array(
            'name' => 'billing_companyname',
            'label' => $this->trans('TXT_COMPANYNAME'),
            'rules' => Array(
                new SimpleForm\Rules\RequiredDependency($this->trans('ERR_EMPTY_COMPANYNAME'), $billingClientType, new SimpleForm\Conditions\Equals('2'))
            )
        )));
        
        $form->AddChild(new SimpleForm\Elements\TextField(Array(
            'name' => 'billing_nip',
            'label' => $this->trans('TXT_NIP'),
            'rules' => Array(
                new SimpleForm\Rules\RequiredDependency($this->trans('ERR_EMPTY_NIP'), $billingClientType, new SimpleForm\Conditions\Equals('2'))
            )
        )));
        
        $form->AddChild(new SimpleForm\Elements\TextField(Array(
            'name' => 'billing_street',
            'label' => $this->trans('TXT_STREET'),
            'rules' => Array(
                new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_STREET'))
            )
        )));
        
        $form->AddChild(new SimpleForm\Elements\TextField(Array(
            'name' => 'billing_streetno',
            'label' => $this->trans('TXT_STREETNO'),
            'rules' => Array(
                new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_STREETNO'))
            )
        )));
        
        $form->AddChild(new SimpleForm\Elements\TextField(Array(
            'name' => 'billing_placeno',
            'label' => $this->trans('TXT_PLACENO')
        )));
        
        $form->AddChild(new SimpleForm\Elements\TextField(Array(
            'name' => 'billing_placename',
            'label' => $this->trans('TXT_PLACE'),
            'rules' => Array(
                new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_PLACE'))
            )
        )));
        
        $form->AddChild(new SimpleForm\Elements\TextField(Array(
            'name' => 'billing_postcode',
            'label' => $this->trans('TXT_POSTCODE'),
            'rules' => Array(
                new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_POSTCODE'))
            )
        )));
        
        $form->AddChild(new SimpleForm\Elements\Select(Array(
            'name' => 'billing_country',
            'label' => $this->trans('TXT_NAME_OF_COUNTRY'),
            'options' => App::getModel('lists')->getCountryForSelect(),
            'rules' => Array(
                new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_NAME_OF_COUNTRY'))
            )
        )));
        
        $otherAddress = $form->AddChild(new SimpleForm\Elements\Checkbox(Array(
            'name' => 'other_address',
            'label' => $this->trans('TXT_OTHER_DELIVERY_ADRESS'),
            'default' => 0
        )));
        
        $form->AddChild(new SimpleForm\Elements\TextField(Array(
            'name' => 'shipping_firstname',
            'label' => $this->trans('TXT_FIRSTNAME'),
            'rules' => Array(
                new SimpleForm\Rules\RequiredDependency($this->trans('ERR_EMPTY_FIRSTNAME'), $otherAddress, new SimpleForm\Conditions\Equals('1'))
            )
        )));
        
        $form->AddChild(new SimpleForm\Elements\TextField(Array(
            'name' => 'shipping_surname',
            'label' => $this->trans('TXT_SURNAME'),
            'rules' => Array(
                new SimpleForm\Rules\RequiredDependency($this->trans('ERR_EMPTY_SURNAME'), $otherAddress, new SimpleForm\Conditions\Equals('1'))
            )
        )));
        
        $form->AddChild(new SimpleForm\Elements\TextField(Array(
            'name' => 'shipping_companyname',
            'label' => $this->trans('TXT_COMPANYNAME')
        )));
        
        $form->AddChild(new SimpleForm\Elements\TextField(Array(
            'name' => 'shipping_street',
            'label' => $this->trans('TXT_STREET'),
            'rules' => Array(
                new SimpleForm\Rules\RequiredDependency($this->trans('ERR_EMPTY_STREET'), $otherAddress, new SimpleForm\Conditions\Equals('0'))
            )
        )));
        
        $form->AddChild(new SimpleForm\Elements\TextField(Array(
            'name' => 'shipping_streetno',
            'label' => $this->trans('TXT_STREETNO'),
            'rules' => Array(
                new SimpleForm\Rules\RequiredDependency($this->trans('ERR_EMPTY_STREETNO'), $otherAddress, new SimpleForm\Conditions\Equals('0'))
            )
        )));
        
        $form->AddChild(new SimpleForm\Elements\TextField(Array(
            'name' => 'shipping_placeno',
            'label' => $this->trans('TXT_PLACENO')
        )));
        
        $form->AddChild(new SimpleForm\Elements\TextField(Array(
            'name' => 'shipping_placename',
            'label' => $this->trans('TXT_PLACE'),
            'rules' => Array(
                new SimpleForm\Rules\RequiredDependency($this->trans('ERR_EMPTY_PLACE'), $otherAddress, new SimpleForm\Conditions\Equals('0'))
            )
        )));
        
        $form->AddChild(new SimpleForm\Elements\TextField(Array(
            'name' => 'shipping_postcode',
            'label' => $this->trans('TXT_POSTCODE'),
            'rules' => Array(
                new SimpleForm\Rules\RequiredDependency($this->trans('ERR_EMPTY_POSTCODE'), $otherAddress, new SimpleForm\Conditions\Equals('0'))
            )
        )));
        
        $form->AddChild(new SimpleForm\Elements\Select(Array(
            'name' => 'shipping_country',
            'label' => $this->trans('TXT_NAME_OF_COUNTRY'),
            'options' => App::getModel('lists')->getCountryForSelect(),
            'rules' => Array(
                new SimpleForm\Rules\RequiredDependency($this->trans('ERR_EMPTY_NAME_OF_COUNTRY'), $otherAddress, new SimpleForm\Conditions\Equals('0'))
            )
        )));
        
        $form->AddChild(new SimpleForm\Elements\TextField(Array(
            'name' => 'phone',
            'label' => $this->trans('TXT_PHONE'),
            'rules' => Array(
                new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_PHONE'))
            )
        )));
        
        $form->AddChild(new SimpleForm\Elements\TextField(Array(
            'name' => 'phone2',
            'label' => $this->trans('TXT_ADDITIONAL_PHONE')
        )));
        
        $form->AddChild(new SimpleForm\Elements\TextField(Array(
            'name' => 'email',
            'label' => $this->trans('TXT_EMAIL'),
            'rules' => Array(
                new SimpleForm\Rules\Required($this->trans('ERR_EMPTY_EMAIL')),
                new SimpleForm\Rules\Email($this->trans('ERR_WRONG_EMAIL'))
            )
        )));
        
        if ((int) App::getContainer()->get('session')->getActiveClientid() == 0){
            
            $createAccount = $form->AddChild(new SimpleForm\Elements\Checkbox(Array(
                'name' => 'create_account',
                'label' => $this->trans('TXT_CHECKOUT_CREATE_ACCOUNT'),
                'default' => $this->layer['enableregistration']
            )));
            
            $newPassword = $form->AddChild(new SimpleForm\Elements\Password(Array(
                'name' => 'password',
                'label' => $this->trans('TXT_PASSWORD'),
                'rules' => Array(
                    new SimpleForm\Rules\RequiredDependency($this->trans('ERR_EMPTY_PASSWORD'), $createAccount, new SimpleForm\Conditions\Equals('1'))
                )
            )));
            
            $form->AddChild(new SimpleForm\Elements\Password(Array(
                'name' => 'confirmpassword',
                'label' => $this->trans('TXT_PASSWORD_REPEAT'),
                'rules' => Array(
                    new SimpleForm\Rules\RequiredDependency($this->trans('ERR_EMPTY_CONFIRM_PASSWORD'), $createAccount, new SimpleForm\Conditions\Equals('1')),
                    new SimpleForm\Rules\Compare($this->trans('ERR_PASSWORDS_NOT_COMPATIBILE'), $newPassword)
                )
            )));
            
            $form->AddChild(new SimpleForm\Elements\Checkbox(Array(
                'name' => 'confirmterms',
                'label' => sprintf($this->trans('TXT_ACCEPT_TERMS_AND_POLICY_OF_PRIVATE'), App::getModel('staticcontent')->getConditionsLink(), App::getContainer()->get('session')->getActiveShopName()),
                'rules' => Array(
                    new SimpleForm\Rules\Required($this->trans('ERR_TERMS_NOT_AGREED'))
                ),
                'default' => 0
            )));
            
            $form->AddChild(new SimpleForm\Elements\Checkbox(Array(
                'name' => 'newsletter',
                'label' => $this->trans('TXT_NEWSLETTER_SIGNUP'),
                'default' => 0
            )));
        }
        
        $clientData = $this->clientModel->getClient();
        $clientBillingAddress = $this->clientModel->getClientAddress(1);
        $clientShippingAddress = $this->clientModel->getClientAddress(0);
        
        $form->Populate(Array(
            'billing_clienttype' => $clientBillingAddress['clienttype'],
            'other_address' => 0,
            'create_account' => 1,
            'phone' => isset($clientData['phone']) ? $clientData['phone'] : '',
            'phone2' => isset($clientData['phone2']) ? $clientData['phone2'] : '',
            'email' => isset($clientData['email']) ? $clientData['email'] : '',
            'billing_firstname' => $clientBillingAddress['firstname'],
            'billing_surname' => $clientBillingAddress['surname'],
            'billing_companyname' => $clientBillingAddress['companyname'],
            'billing_nip' => $clientBillingAddress['nip'],
            'billing_street' => $clientBillingAddress['street'],
            'billing_streetno' => $clientBillingAddress['streetno'],
            'billing_placeno' => $clientBillingAddress['placeno'],
            'billing_placename' => $clientBillingAddress['placename'],
            'billing_postcode' => $clientBillingAddress['postcode'],
            'billing_country' => $clientBillingAddress['countryid'],
            'shipping_firstname' => $clientShippingAddress['firstname'],
            'shipping_surname' => $clientShippingAddress['surname'],
            'shipping_companyname' => $clientShippingAddress['companyname'],
            'shipping_nip' => $clientShippingAddress['nip'],
            'shipping_street' => $clientShippingAddress['street'],
            'shipping_streetno' => $clientShippingAddress['streetno'],
            'shipping_placeno' => $clientShippingAddress['placeno'],
            'shipping_placename' => $clientShippingAddress['placename'],
            'shipping_postcode' => $clientShippingAddress['postcode'],
            'shipping_country' => $clientShippingAddress['countryid']
        ));
        
        if ($form->Validate()){
            $formData = $form->getSubmitValues();
            
            $Data['clientaddress'] = Array(
                'firstname' => $formData['billing_firstname'],
                'surname' => $formData['billing_surname'],
                'companyname' => ($formData['billing_clienttype'] == 2) ? $formData['billing_companyname'] : '',
                'nip' => ($formData['billing_clienttype'] == 2) ? $formData['billing_nip'] : '',
                'street' => $formData['billing_street'],
                'streetno' => $formData['billing_streetno'],
                'placeno' => $formData['billing_placeno'],
                'placename' => $formData['billing_placename'],
                'postcode' => $formData['billing_postcode'],
                'countryid' => $formData['billing_country'],
                'clienttype' => $formData['billing_clienttype']
            );
            
            if ($formData['other_address'] == 1){
                $Data['deliveryAddress'] = Array(
                    'firstname' => $formData['shipping_firstname'],
                    'surname' => $formData['shipping_surname'],
                    'companyname' => $formData['shipping_companyname'],
                    'street' => $formData['shipping_street'],
                    'streetno' => $formData['shipping_streetno'],
                    'placeno' => $formData['shipping_placeno'],
                    'placename' => $formData['shipping_placename'],
                    'postcode' => $formData['shipping_postcode'],
                    'countryid' => $formData['shipping_country']
                );
            }
            else{
                $Data['deliveryAddress'] = $Data['clientaddress'];
            }
            
            $recurMail = 0;
            if (isset($formData['create_account']) && $formData['create_account'] == 1){
                $recurMail = $this->clientModel->checkClientNewMail($formData);
                if ($recurMail == 0){
                    $clientData = $Data['clientaddress'];
                    $clientData['email'] = $formData['email'];
                    $clientData['password'] = $formData['password'];
                    $clientData['newsletter'] = $formData['newsletter'];
                    $clientData['phone'] = $formData['phone'];
                    $clientData['phone2'] = $formData['phone2'];
                    $clientId = $this->clientModel->addNewClient($clientData);
                    $result = App::getModel('clientlogin')->authProccess($formData['email'], $formData['password']);
                    if ($result > 0){
                        App::getContainer()->get('session')->setActiveClientid($result);
                        App::getModel('clientlogin')->checkClientGroup();
                        $this->clientModel->saveClientData();
                        $this->clientModel->updateClientAddress($Data['clientaddress'], 1);
                        $this->clientModel->updateClientAddress($Data['deliveryAddress'], 0);
                    }
                }
                else{
                    $result = App::getModel('clientlogin')->authProccess($formData['email'], $formData['password']);
                    if ($result > 0){
                        App::getContainer()->get('session')->setActiveClientid($result);
                        App::getModel('clientlogin')->checkClientGroup();
                        App::getModel('clientlogin')->setLoginTime();
                        App::getModel('client')->saveClientData();
                        $misingCart = App::getModel('missingcart')->checkMissingCartForClient($result);
                        if (is_array($misingCart) && ! empty($misingCart)){
                            App::getModel('cart')->addProductsToCartFromMissingCart($misingCart);
                            App::getModel('missingcart')->cleanMissingCart(App::getContainer()->get('session')->getActiveClientid());
                        }
                        $recurMail = 0;
                    }
                    else{
                        $recurMail = - 1;
                    }
                }
            }
            
            if ((int) App::getContainer()->get('session')->getActiveClientid() > 0){
                $this->clientModel->updateClientAddress($Data['clientaddress'], 1);
                $this->clientModel->updateClientAddress($Data['deliveryAddress'], 0);
            }
            
            if ($recurMail == - 1){
                $this->registry->template->assign('error', 'Podany adres e-mail jest już przypisany do innego konta użytkownika. Proszę skorzystaj z opcji <a href="' . $this->registry->router->generate('frontend.forgotpassword', true) . '" style="font-size: inherit">przypomnienia hasła</a> jeśli chcesz odzyskać dostęp do konta.');
            }
            else 
                if ($recurMail == 1){
                    $this->registry->template->assign('error', $this->trans('ERR_DUPLICATE_EMAIL'));
                }
                else 
                    if (! $recurMail){
                        App::getContainer()->get('session')->setActiveOrderClientAddress($Data['clientaddress']);
                        App::getContainer()->get('session')->setActiveOrderDeliveryAddress($Data['deliveryAddress']);
                        App::getContainer()->get('session')->setActiveOrderContactData(Array(
                            'phone' => $formData['phone'],
                            'phone2' => $formData['phone2'],
                            'email' => $formData['email']
                        ));
                        
                        $orderData = App::getModel('finalization')->setClientOrder();
                        $countryIds = array_filter($orderData['dispatchmethod']['countryids']);
                        if (count($countryIds) > 0){
                            if (in_array($orderData['deliveryAddress']['countryid'], $countryIds)){
                                App::redirectUrl($this->registry->router->generate('frontend.finalization', true));
                            }
                            else{
                                App::getContainer()->get('session')->setActiveDispatchmethodChecked(0);
                                App::getContainer()->get('session')->setVolatileMessage('Wybrana wcześniej metoda wysyłki nie jest dostępna dla Twojego kraju dostawy. Wybierz nową formę dostawy w koszyku.');
                                App::redirectUrl($this->registry->router->generate('frontend.cart', true));
                            }
                        }
                        else{
                            App::redirectUrl($this->registry->router->generate('frontend.finalization', true));
                        }
                    }
        }
        
        $assignData = Array(
            'form' => $form->getForm()
        );
        
        foreach ($assignData as $key => $assign){
            $this->registry->template->assign($key, $assign);
        }
        
        if (App::getContainer()->get('session')->getActiveClientid() > 0){
            return $this->registry->template->fetch($this->loadTemplate('client.tpl'));
        }
        else{
            return $this->registry->template->fetch($this->loadTemplate('guest.tpl'));
        }
    }
}
