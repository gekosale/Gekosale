<?php
/*
 * Gekosale Open-Source E-Commerce Platform
 *
 * This file is part of the Gekosale package.
 *
 * (c) Adam Piotrowski <adam@gekosale.com>
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 */
namespace Gekosale\Plugin\Company\Form;

use Gekosale\Core\Form;
use Gekosale\Plugin\Company\Event\CompanyFormEvent;

/**
 * Class CompanyForm
 *
 * @package Gekosale\Plugin\Company\Form
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class CompanyForm extends Form
{
    /**
     * Initializes CompanyForm
     *
     * @param array $companyData
     *
     * @return Form\Elements\Form
     */
    public function init($companyData = [])
    {
        $form = $this->addForm([
            'name' => 'company',
        ]);

        $requiredData = $form->addChild($this->addFieldset([
            'name'  => 'required_data',
            'label' => $this->trans('Required data')
        ]));

        $requiredData->addChild($this->addTextField([
            'name'  => 'name',
            'label' => $this->trans('Name'),
            'rules' => [
                $this->addRuleRequired($this->trans('Name is required')),
            ]
        ]));

        $requiredData->addChild($this->addTextField([
            'name'  => 'short_name',
            'label' => $this->trans('Short name'),
            'rules' => [
                $this->addRuleRequired($this->trans('Short name is required')),
            ]
        ]));

        $addressData = $form->addChild($this->addFieldset([
            'name'  => 'address_data',
            'label' => $this->trans('Address data')
        ]));

        $addressData->addChild($this->addTextField([
            'name'  => 'street',
            'label' => $this->trans('Street'),
        ]));

        $addressData->addChild($this->addTextField([
            'name'  => 'streetno',
            'label' => $this->trans('Street number'),
        ]));

        $addressData->addChild($this->addTextField([
            'name'  => 'flatno',
            'label' => $this->trans('Flat number'),
        ]));

        $addressData->addChild($this->addTextField([
            'name'  => 'province',
            'label' => $this->trans('Province'),
        ]));

        $addressData->addChild($this->addTextField([
            'name'  => 'postcode',
            'label' => $this->trans('Post code'),
        ]));

        $addressData->addChild($this->addTextField([
            'name'  => 'city',
            'label' => $this->trans('City'),
        ]));

        $addressData->addChild($this->addSelect([
            'name'    => 'country',
            'label'   => $this->trans('Country'),
            'options' => $this->makeOptions($this->get('country.repository')->all())
        ]));

        $form->addFilter($this->addFilterNoCode());

        $form->addFilter($this->addFilterTrim());

        $form->addFilter($this->addFilterSecure());

        $event = new CompanyFormEvent($form, $companyData);

        $this->getDispatcher()->dispatch(CompanyFormEvent::FORM_INIT_EVENT, $event);

        $form->populate($event->getPopulateData());

        return $form;
    }
}
