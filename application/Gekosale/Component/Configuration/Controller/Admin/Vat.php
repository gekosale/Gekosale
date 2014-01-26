<?php

namespace Gekosale\Component\Vat\Controller\Admin;
use Gekosale\Core\Component\Controller\Admin;
use FormEngine;

class Vat extends Admin
{

	public function index ()
	{
		$this->registry->xajax->registerFunction(array(
			'doDeleteVAT',
			$this->model,
			'doAJAXDeleteVAT'
		));
		
		$this->registry->xajax->registerFunction(array(
			'LoadAllVAT',
			$this->model,
			'getVATForAjax'
		));
		
		$this->registry->xajax->registerFunction(array(
			'GetValueSuggestions',
			$this->model,
			'getValueForAjax'
		));
		
		$this->renderLayout();
	}

	public function add ()
	{
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			$this->model->addNewVAT($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT));
			
			if (FormEngine\FE::IsAction('next')){
				App::redirect(__ADMINPANE__ . '/vat/add');
			}
			else{
				App::redirect(__ADMINPANE__ . '/vat');
			}
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}

	public function edit ()
	{
		
		$rawVatData = $this->model->getVATView($this->id);
		
		if (empty($rawVatData)){
			App::redirect(__ADMINPANE__ . '/vat');
		}
		
		$populateData = Array(
			'required_data' => Array(
				'value' => $rawVatData['value'],
				'language_data' => $rawVatData['language']
			)
		);
		
		$this->formModel->setPopulateData($populateData);
		
		$form = $this->formModel->initForm();
		
		if ($form->Validate(FormEngine\FE::SubmittedData())){
			try{
				$this->model->editVAT($form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT), $this->id);
			}
			catch (Exception $e){
				$this->registry->template->assign('error', $e->getMessage());
			}
			App::redirect(__ADMINPANE__ . '/vat');
		}
		
		$this->renderLayout(Array(
			'form' => $form->Render()
		));
	}
}