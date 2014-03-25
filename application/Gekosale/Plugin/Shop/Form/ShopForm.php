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
namespace Gekosale\Plugin\Shop\Form;

use Gekosale\Core\Form;
use Gekosale\Plugin\Shop\Event\ShopFormEvent;

/**
 * Class ShopForm
 *
 * @package Gekosale\Plugin\Shop\Form
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ShopForm extends Form
{
    /**
     * Initializes ShopForm
     *
     * @param array $shopData
     *
     * @return Form\Elements\Form
     */
    public function init($shopData = [])
    {
        $form = $this->addForm([
            'name' => 'shop',
        ]);

        $requiredData = $form->addChild($this->addFieldset([
            'name'  => 'required_data',
            'label' => $this->trans('Required data')
        ]));

        $languageData = $requiredData->addChild($this->addFieldsetLanguage([
            'name'      => 'language_data',
            'languages' => $this->getLanguages()
        ]));

        $languageData->addChild($this->addTextField([
            'name'  => 'name',
            'label' => $this->trans('Name'),
            'rules' => [
                $this->addRuleRequired($this->trans('Name is required')),
            ]
        ]));

        $requiredData->addChild($this->addTextField([
            'name'    => 'url',
            'label'   => $this->trans('Url'),
            'comment' => $this->trans('Enter shop URL address'),
            'rules'   => [
                $this->addRuleRequired($this->trans('Url is required')),
                $this->addRuleCustom($this->trans('Url address is not valid'), function ($value) {
                    return filter_var($value, FILTER_VALIDATE_URL);
                })
            ]
        ]));

        $requiredData->addChild($this->addCheckBox([
            'name'    => 'offline',
            'label'   => $this->trans('Offline mode'),
            'comment' => $this->trans('Turn shop into offline mode.')
        ]));

        $requiredData->addChild($this->addSelect([
            'name'    => 'company_id',
            'label'   => $this->trans('Company'),
            'options' => $this->makeOptions($this->get('company.repository')->getAllCompanyToSelect()),
            'rules'   => [
                $this->addRuleRequired($this->trans('Company is required'))
            ]
        ]));

        $requiredData->addChild($this->addSelect([
            'name'    => 'layout_theme_id',
            'label'   => $this->trans('Theme'),
            'options' => $this->makeOptions($this->get('layout_theme.repository')->getAllLayoutThemeToSelect()),
            'rules'   => [
                $this->addRuleRequired($this->trans('Theme is required'))
            ]
        ]));

        $metaData = $form->addChild($this->addFieldset([
            'name'  => 'meta_data',
            'label' => $this->trans('Seo settings')
        ]));

        $languageData = $metaData->addChild($this->addFieldsetLanguage([
            'name'      => 'language_data',
            'languages' => $this->getLanguages()
        ]));

        $languageData->addChild($this->addTextField([
            'name'  => 'meta_title',
            'label' => $this->trans('Meta title'),
        ]));

        $languageData->addChild($this->addTextArea([
            'name'  => 'meta_keywords',
            'label' => $this->trans('Meta keywords'),
        ]));

        $languageData->addChild($this->addTextArea([
            'name'  => 'meta_description',
            'label' => $this->trans('Meta description'),
        ]));

        $form->addFilter($this->addFilterNoCode());

        $form->addFilter($this->addFilterTrim());

        $form->addFilter($this->addFilterSecure());

        $event = new ShopFormEvent($form, $shopData);

        $this->getDispatcher()->dispatch(ShopFormEvent::FORM_INIT_EVENT, $event);

        $form->populate($event->getPopulateData());

        return $form;
    }
}
