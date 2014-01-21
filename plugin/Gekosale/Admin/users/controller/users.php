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
 * $Revision: 625 $
 * $Author: gekosale $
 * $Date: 2012-01-20 20:57:36 +0100 (Pt, 20 sty 2012) $
 * $Id: users.php 625 2012-01-20 19:57:36Z gekosale $ 
 */
namespace Gekosale;

use FormEngine;

class UsersController extends Component\Controller\Admin
{

	public function __construct ($registry)
	{
		parent::__construct($registry);
		$this->layer = $this->registry->loader->getCurrentLayer();
	}

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'doDeleteUser',
			App::getModel('users'),
			'doAJAXDeleteUser'
		));
		$this->registry->xajax->registerFunction(array(
			'LoadAllUser',
			App::getModel('users'),
			'getUsersForAjax'
		));
		$this->registry->xajax->registerFunction(array(
			'disableUser',
			App::getModel('users'),
			'doAJAXDisableUser'
		));
		$this->registry->xajax->registerFunction(array(
			'enableUser',
			App::getModel('users'),
			'doAJAXEnableUser'
		));
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->template->assign('datagrid_filter', App::getModel('users')->getDatagridFilterData());
		$this->registry->template->display($this->loadTemplate('index.tpl'));
	}

	public function add ()
	{
		$this->registry->template->assign('groups', App::getModel('groups')->getGroupsAll());
		$groups = App::getModel('groups/groups')->getGroupsAllToSelect();
		
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'user',
			'action' => '',
			'method' => 'post'
		));
		
		$personalData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'personal_data',
			'label' => $this->trans('TXT_PERSONAL_DATA')
		)));
		
		$firstname = $personalData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'firstname',
			'label' => $this->trans('TXT_FIRSTNAME'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_FIRSTNAME'))
			)
		)));
		
		$personalData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'surname',
			'label' => $this->trans('TXT_SURNAME'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_SURNAME'))
			)
		)));
		
		$personalData->AddChild(new FormEngine\Elements\Tip(Array(
			'tip' => '<p>' . $this->trans('TXT_USER_PASSWORD_INFO') . '</p>'
		)));
		
		$personalData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'email',
			'label' => $this->trans('TXT_EMAIL'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_EMAIL')),
				new FormEngine\Rules\Email($this->trans('ERR_WRONG_EMAIL')),
				new FormEngine\Rules\Unique($this->trans('ERR_EMAIL_ALREADY_EXISTS'), 'userdata', 'email')
			)
		)));
		
		$rightsData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'rights_data',
			'label' => $this->trans('TXT_RIGHTS')
		)));
		
		$isglobal = App::getModel('users')->checkActiveUserIsGlobal();
		
		if ($isglobal == 1){
			
			$global = $rightsData->AddChild(new FormEngine\Elements\Checkbox(Array(
				'name' => 'global',
				'label' => $this->trans('TXT_GLOBAL_USER'),
				'default' => '1'
			)));
			
			$rightsData->AddChild(new FormEngine\Elements\Select(Array(
				'name' => 'group',
				'label' => $this->trans('TXT_GROUPS'),
				'options' => FormEngine\Option::Make(App::getModel('groups/groups')->getGroupsAllToSelect()),
				'rules' => Array(
					new FormEngine\Rules\Required($this->trans('ERR_EMPTY_GROUP'))
				),
				'dependencies' => Array(
					new FormEngine\Dependency(FormEngine\Dependency::HIDE, $global, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
				)
			)));
			
			$layers = App::getModel('users')->getLayersAll();
			
			foreach ($layers as $key => $store){
				$storeRightsData[$store['id']] = $rightsData->AddChild(new FormEngine\Elements\Fieldset(Array(
					'name' => 'store_' . $store['id'],
					'label' => $this->trans('TXT_RIGHTS') . ' dla ' . $store['name'],
					'dependencies' => Array(
						new FormEngine\Dependency(FormEngine\Dependency::SHOW, $global, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
					)
				)));
				
				foreach ($store['views'] as $v => $view){
					
					$storeRightsData[$store['id']]->AddChild(new FormEngine\Elements\Select(Array(
						'name' => 'view_' . $view['id'],
						'label' => $view['name'],
						'options' => FormEngine\Option::Make($this->registry->core->getDefaultValueToSelect() + App::getModel('groups/groups')->getGroupsAllToSelect())
					)));
				}
			}
		}
		else{
			
			$layers = App::getModel('users')->getLayersAll();
			
			foreach ($layers as $key => $store){
				$storeRightsData[$store['id']] = $rightsData->AddChild(new FormEngine\Elements\Fieldset(Array(
					'name' => 'store_' . $store['id'],
					'label' => $this->trans('TXT_RIGHTS') . ' dla ' . $store['name']
				)));
				
				foreach ($store['views'] as $v => $view){
					
					$storeRightsData[$store['id']]->AddChild(new FormEngine\Elements\Select(Array(
						'name' => 'view_' . $view['id'],
						'label' => $view['name'],
						'options' => FormEngine\Option::Make($this->registry->core->getDefaultValueToSelect() + App::getModel('groups/groups')->getGroupsAllToSelect($view['id']))
					)));
				}
			}
		}
		
		$additionalData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'additional_data',
			'label' => $this->trans('TXT_ADDITIONAL_DATA')
		)));
		
		$additionalData->AddChild(new FormEngine\Elements\Textarea(Array(
			'name' => 'description',
			'label' => $this->trans('TXT_DESCRIPTION'),
			'comment' => $this->trans('TXT_MAX_LENGTH') . ' 3000',
			'max_length' => 3000
		)));
		
		$photosPane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'photos_pane',
			'label' => $this->trans('TXT_PHOTO')
		)));
		
		$photosPane->AddChild(new FormEngine\Elements\Image(Array(
			'name' => 'photo',
			'label' => $this->trans('TXT_PHOTO'),
			'repeat_min' => 0,
			'repeat_max' => 1,
			'upload_url' => App::getURLAdressWithAdminPane() . 'files/add'
		)));
		
		$form->AddFilter(new FormEngine\Filters\NoCode());
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\Secure());
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			
			$password = Core::passwordGenerate();
			$users = $form->getSubmitValues();
			
			$totalGroups = 0;
			
			if ((int) $users['rights_data']['global'] == 0){
				foreach ($users['rights_data'] as $k => $v){
					if (substr($k, 0, 5) == 'store'){
						foreach ($v as $viewid => $group){
							if ($group > 0){
								$totalGroups ++;
							}
						}
					}
				}
				if ($totalGroups == 0){
					App::getContainer()->get('session')->setVolatileMessage("Nie powiodło się dodanie użytkownika. Każdy użytkownik musi posiadać wybraną przynajmniej jedną grupę.");
					App::redirect(__ADMINPANE__ . '/users/add');
				}
			}
			App::getModel('users')->addNewUser($users, $password);
			$this->registry->template->assign('password', $password);
			$this->registry->template->assign('users', $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			
			App::getModel('mailer')->sendEmail(Array(
				'template' => 'newPasswordForUser',
				'email' => Array(
					$users['personal_data']['email']
				),
				'bcc' => false,
				'subject' => $this->trans('TXT_NEW_USER'),
				'viewid' => Helper::getViewId()
			));
			
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/users/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/users');
			}
		}
		
		$this->registry->template->assign('form', $form->Render());
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->template->display($this->loadTemplate('add.tpl'));
	}

	public function edit ()
	{
		$rawUserData = App::getModel('users')->getUserById($this->registry->core->getParam());
		
		if (empty($rawUserData)){
			App::redirect(__ADMINPANE__ . '/users');
		}
		
		$layers = App::getModel('users')->getLayersAll();
		
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'user',
			'action' => '',
			'method' => 'post'
		));
		
		$personalData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'personal_data',
			'label' => $this->trans('TXT_PERSONAL_DATA')
		)));
		
		$personalData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'firstname',
			'label' => $this->trans('TXT_FIRSTNAME'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_FIRSTNAME'))
			)
		)));
		
		$personalData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'surname',
			'label' => $this->trans('TXT_SURNAME'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_SURNAME'))
			)
		)));
		
		$personalData->AddChild(new FormEngine\Elements\TextField(Array(
			'name' => 'email',
			'label' => $this->trans('TXT_EMAIL'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('ERR_EMPTY_EMAIL')),
				new FormEngine\Rules\Email($this->trans('ERR_WRONG_EMAIL')),
				new FormEngine\Rules\Unique($this->trans('ERR_EMAIL_ALREADY_EXISTS'), 'userdata', 'email', null, Array(
					'column' => 'userid',
					'values' => $this->registry->core->getParam()
				))
			)
		)));
		
		$changePassword = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'change_password',
			'label' => $this->trans('TXT_CHANGE_USERS_PASSWORD')
		)));
		
		$userid = App::getContainer()->get('session')->getActiveUserid();
		$edituserid = $this->registry->core->getParam();
		
		if ($userid == $edituserid){
			
			$newPasswordChange = $changePassword->AddChild(new FormEngine\Elements\Checkbox(Array(
				'name' => 'changepassword',
				'label' => $this->trans('TXT_CHANGE_PASS')
			)));
			
			$oldPassword = $changePassword->AddChild(new FormEngine\Elements\Password(Array(
				'name' => 'oldpasswd',
				'label' => $this->trans('TXT_PASSWORD_OLD'),
				'dependencies' => Array(
					new FormEngine\Dependency(FormEngine\Dependency::SHOW, $newPasswordChange, new FormEngine\Conditions\Equals('1'))
				)
			)));
			
			$newPassword = $changePassword->AddChild(new FormEngine\Elements\Password(Array(
				'name' => 'newppasswd',
				'label' => $this->trans('TXT_PASSWORD_NEW'),
				'rules' => Array(
					new FormEngine\Rules\Required($this->trans('ERR_EMPTY_PASSWORD')),
					new FormEngine\Rules\Format($this->trans('ERR_PASSWORD_NEW_INVALID'), '/^.{6,}$/')
				),
				'dependencies' => Array(
					new FormEngine\Dependency(FormEngine\Dependency::SHOW, $newPasswordChange, new FormEngine\Conditions\Equals('1'))
				)
			)));
			
			$changePassword->AddChild(new FormEngine\Elements\Password(Array(
				'name' => 'newpasswdrep',
				'label' => $this->trans('TXT_PASSWORD_REPEAT'),
				'rules' => Array(
					new FormEngine\Rules\Compare($this->trans('ERR_PASSWORDS_NOT_COMPATIBILE'), $newPassword)
				),
				'dependencies' => Array(
					new FormEngine\Dependency(FormEngine\Dependency::SHOW, $newPasswordChange, new FormEngine\Conditions\Equals('1'))
				)
			)));
		}
		else{
			
			$changePassword->AddChild(new FormEngine\Elements\StaticText(Array(
				'text' => '<p>' . $this->trans('TXT_PASSWORD_CHANGE_INSTRUCTION') . '</p>'
			)));
			
			$newPassword = $changePassword->AddChild(new FormEngine\Elements\Checkbox(Array(
				'name' => 'newpassword',
				'label' => $this->trans('TXT_PASSWORD_NEW')
			)));
		}
		
		$isglobal = App::getModel('users')->checkActiveUserIsGlobal();
		
		if ($isglobal == 1){
			
			$rightsData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
				'name' => 'rights_data',
				'label' => $this->trans('TXT_RIGHTS')
			)));
			
			$rightsData->AddChild(new FormEngine\Elements\StaticText(Array(
				'text' => '<p>' . $this->trans('TXT_SET_USER_LAYER_RIGHTS') . '</p>'
			)));
			
			$global = $rightsData->AddChild(new FormEngine\Elements\Checkbox(Array(
				'name' => 'global',
				'label' => $this->trans('TXT_GLOBAL_USER')
			)));
			
			$rightsData->AddChild(new FormEngine\Elements\Select(Array(
				'name' => 'group',
				'label' => $this->trans('TXT_GROUPS'),
				'options' => FormEngine\Option::Make(App::getModel('groups/groups')->getGroupsAllToSelect()),
				'rules' => Array(
					new FormEngine\Rules\Required($this->trans('ERR_EMPTY_GROUP'))
				),
				'dependencies' => Array(
					new FormEngine\Dependency(FormEngine\Dependency::HIDE, $global, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
				)
			)));
			
			$layers = App::getModel('users')->getLayersAll();
			
			foreach ($layers as $key => $store){
				$storeRightsData[$store['id']] = $rightsData->AddChild(new FormEngine\Elements\Fieldset(Array(
					'name' => 'store_' . $store['id'],
					'label' => $this->trans('TXT_RIGHTS') . ' dla ' . $store['name'],
					'dependencies' => Array(
						new FormEngine\Dependency(FormEngine\Dependency::SHOW, $global, new FormEngine\Conditions\Not(new FormEngine\Conditions\Equals('1')))
					)
				)));
				
				foreach ($store['views'] as $v => $view){
					
					$storeRightsData[$store['id']]->AddChild(new FormEngine\Elements\Select(Array(
						'name' => 'view_' . $view['id'],
						'label' => $view['name'],
						'options' => FormEngine\Option::Make($this->registry->core->getDefaultValueToSelect() + App::getModel('groups/groups')->getGroupsAllToSelect())
					)));
				}
			}
		}
		
		$additionalData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'additional_data',
			'label' => $this->trans('TXT_ADDITIONAL_DATA')
		)));
		
		$additionalData->AddChild(new FormEngine\Elements\Textarea(Array(
			'name' => 'description',
			'label' => $this->trans('TXT_DESCRIPTION'),
			'comment' => $this->trans('TXT_MAX_LENGTH') . ' 3000',
			'max_length' => 3000
		)));
		
		$additionalData->AddChild(new FormEngine\Elements\Checkbox(Array(
			'name' => 'active',
			'label' => $this->trans('TXT_ENABLE_USER')
		)));
		
		$photosPane = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'photos_pane',
			'label' => $this->trans('TXT_PHOTO')
		)));
		
		$photosPane->AddChild(new FormEngine\Elements\Image(Array(
			'name' => 'photo',
			'label' => $this->trans('TXT_PHOTO'),
			'repeat_min' => 0,
			'repeat_max' => 1,
			'upload_url' => App::getURLAdressWithAdminPane() . 'files/add'
		)));
		
		$form->AddFilter(new FormEngine\Filters\NoCode());
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\Secure());
		
		$userData = Array(
			'personal_data' => Array(
				'firstname' => $rawUserData['firstname'],
				'surname' => $rawUserData['surname'],
				'email' => $rawUserData['email']
			),
			'additional_data' => Array(
				'description' => $rawUserData['description'],
				'active' => $rawUserData['active']
			),
			'photos_pane' => Array(
				'photo' => $rawUserData['photo']
			),
			'rights_data' => Array(
				'global' => $rawUserData['globaluser'],
				'group' => $rawUserData['idgroup']
			)
		);
		foreach ($rawUserData['layer'] as $key => $layer){
			$userData['rights_data']['store_' . $layer['store']][] = Array(
				'view_' . $layer['view'] => $layer['group']
			);
		}
		
		$form->Populate($userData);
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				
				$edituser = $form->getSubmitValues();
				App::getModel('users')->updateUser($edituser, $this->registry->core->getParam());
				if ($userid == $edituserid){
					if ($edituser['change_password']['changepassword'] == 1){
						$editpassword = $edituser['change_password']['newppasswd'];
						$changPassword = App::getModel('users')->updateUserPassword($edituser['change_password']['newppasswd']);
						App::getContainer()->get('session')->setActiveUserFirstname($edituser['personal_data']['firstname']);
						App::getContainer()->get('session')->setActiveUserSurname($edituser['personal_data']['surname']);
						App::getContainer()->get('session')->setActiveUserEmail($edituser['personal_data']['email']);
					}
				}
				else{
					if ($edituser['change_password']['newpassword'] == 1){
						$editpassword = Core::passwordGenerate();
						$changPassword = App::getModel('users')->updateUserPassword($editpassword);
						
						if ($changPassword == true){
							$password = Core::passwordGenerate();
							$this->registry->template->assign('password', $editpassword);
							
							App::getModel('mailer')->sendEmail(Array(
								'template' => 'newPasswordForUser',
								'email' => Array(
									$edituser['personal_data']['email']
								),
								'bcc' => false,
								'subject' => $this->trans('TXT_EDIT_PASSWORD_USER'),
								'viewid' => Helper::getViewId()
							));
						}
					}
				}
			}
			catch (Exception $e){
				App::getContainer()->get('session')->setVolatileUsereditError(1, false);
			}
			App::redirect(__ADMINPANE__ . '/users');
		}
		
		$error = App::getContainer()->get('session')->getVolatileUsereditError();
		if ($error[0] == 1){
			$this->registry->template->assign('error', $e->getMessage());
		}
		
		$this->registry->template->assign('form', $form->Render());
		$this->registry->xajax->processRequest();
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->template->display($this->loadTemplate('edit.tpl'));
	}
}