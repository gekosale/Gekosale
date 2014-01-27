<?php

namespace Gekosale\Component\Productnews\Controller\Admin;
use Gekosale\Core\Component\Controller\Admin;
use FormEngine;

class News extends Admin
{

	public function index ()
	{
		$this->registry->xajaxInterface->registerFunction(array(
			'LoadCategoryChildren',
			App::getModel('product'),
			'loadCategoryChildren'
		));

		$this->registry->xajax->registerFunction(array(
			'LoadAllProductNews',
			$this->model,
			'getProductNewsForAjax'
		));

		$this->registry->xajax->registerFunction(array(
			'GetNameSuggestions',
			$this->model,
			'getNameForAjax'
		));

		$this->registry->xajax->registerFunction(array(
			'doDeleteProductNews',
			$this->model,
			'doAJAXDeleteProductNews'
		));

		$this->registry->xajax->registerFunction(array(
			'disableProductNews',
			$this->model,
			'doAJAXDisableProductNews'
		));

		$this->registry->xajax->registerFunction(array(
			'enableProductNews',
			$this->model,
			'doAJAXEnableProductNews'
		));

		$this->renderLayout(Array(
			'datagrid_filter' => $this->model->getDatagridFilterData()
		));
	}

	public function add ()
	{
		$form = new FormEngine\Elements\Form(Array(
			'name' => 'productnews',
			'action' => '',
			'method' => 'post'
		));

		$requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'required_data',
			'label' => $this->trans('TXT_SELECT_PRODUCTS')
		)));

		$productid = $requiredData->AddChild(new FormEngine\Elements\ProductSelect(Array(
			'name' => 'productid',
			'label' => $this->trans('TXT_SELECT_PRODUCTS'),
			'rules' => Array(
				new FormEngine\Rules\Required($this->trans('TXT_SELECT_PRODUCTS'))
			),
			'exclude' => $this->model->getExcludeProducts(),
			'repeat_min' => 1,
			'repeat_max' => FormEngine\FE::INFINITE
		)));

		$newData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
			'name' => 'new_data',
			'label' => $this->trans('TXT_NEW_DATA')
		)));

		$newData->AddChild(new FormEngine\Elements\Date(Array(
			'name' => 'adddate',
			'label' => $this->trans('TXT_ADDDATE')
		)));

		$newData->AddChild(new FormEngine\Elements\Date(Array(
			'name' => 'startnew',
			'label' => $this->trans('TXT_START_DATE')
		)));

		$newData->AddChild(new FormEngine\Elements\Date(Array(
			'name' => 'endnew',
			'label' => $this->trans('TXT_END_DATE')
		)));

		$form->AddFilter(new FormEngine\Filters\NoCode());
		$form->AddFilter(new FormEngine\Filters\Trim());
		$form->AddFilter(new FormEngine\Filters\Secure());

		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$Data = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);
			$this->model->addProductNews($Data);
			App::redirect(__ADMINPANE__ . '/productnews');
		}

		$this->registry->template->assign('form', $form->Render());
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->xajax->processRequest();
		$this->registry->template->display($this->loadTemplate('add.tpl'));
	}
}