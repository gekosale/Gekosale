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
namespace Gekosale\Plugin\Producer\Form;

use Gekosale\Core\Form;
use Gekosale\Plugin\Producer\Event\ProducerFormEvent;

/**
 * Class ProducerForm
 *
 * @package Gekosale\Plugin\Producer\Form
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ProducerForm extends Form
{

    public function init($producerData = [])
    {
        $form = $this->addForm([
            'name' => 'producer'
        ]);

        $requiredData = $form->addChild($this->addFieldset([
            'name'  => 'required_data',
            'label' => $this->trans('Basic settings')
        ]));

        $languageData = $requiredData->addChild($this->addFieldsetLanguage([
            'name'      => 'language_data',
            'label'     => $this->trans('Language settings'),
            'languages' => $this->getLanguages()
        ]));

        $languageData->addChild($this->addTextField([
            'name'  => 'name',
            'label' => $this->trans('Name'),
            'rules' => [
                $this->addRuleRequired($this->trans('Name is required')),
                $this->addRuleUnique($this->trans('Producer already exists'),
                    [
                        'table'   => 'producer_translation',
                        'column'  => 'name',
                        'exclude' => [
                            'column' => 'producer_id',
                            'values' => $this->getParam('id')
                        ]
                    ]
                )
            ]
        ]));

        $languageData->addChild($this->addTextField([
            'name'  => 'slug',
            'label' => $this->trans('Slug'),
            'rules' => [
                $this->addRuleRequired($this->trans('Slug is required')),
                $this->addRuleUnique($this->trans('Slug already exists'),
                    [
                        'table'   => 'producer_translation',
                        'column'  => 'slug',
                        'exclude' => [
                            'column' => 'producer_id',
                            'values' => $this->getParam('id')
                        ]
                    ]
                )
            ]
        ]));

        $requiredData->addChild($this->addMultiSelect([
            'name'    => 'deliverers',
            'label'   => 'Deliverers',
            'options' => $this->makeOptions($this->get('deliverer.repository')->getAllDelivererToSelect())
        ]));

        $requiredData = $form->addChild($this->addFieldset([
            'name'  => 'description_data',
            'label' => $this->trans('Description')
        ]));

        $languageData = $requiredData->addChild($this->addFieldsetLanguage([
            'name'      => 'language_data',
            'label'     => $this->trans('Language settings'),
            'languages' => $this->getLanguages()
        ]));

        $languageData->addChild($this->addRichTextEditor([
            'name'  => 'short_description',
            'label' => $this->trans('Short description'),
        ]));

        $languageData->addChild($this->addRichTextEditor([
            'name'  => 'description',
            'label' => $this->trans('Description'),
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

        $shopData = $form->addChild($this->addFieldset([
            'name'  => 'shop_data',
            'label' => $this->trans('Shops')
        ]));

        $shopData->addChild($this->addShopSelector([
            'name'   => 'shops',
            'label'  => $this->trans('Shops'),
            'stores' => $this->get('company.repository')->getShopsTree()
        ]));

        $form->addFilters([
            $this->addFilterNoCode(),
            $this->addFilterTrim(),
            $this->addFilterSecure()
        ]);

        $event = new ProducerFormEvent($form, $producerData);

        $this->getDispatcher()->dispatch(ProducerFormEvent::FORM_INIT_EVENT, $event);

        $form->populate($event->getPopulateData());

        return $form;
    }
}
