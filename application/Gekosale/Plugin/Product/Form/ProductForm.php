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
namespace Gekosale\Plugin\Product\Form;

use Gekosale\Core\Form;
use Gekosale\Plugin\Product\Event\ProductFormEvent;

/**
 * Class ProductForm
 *
 * @package Gekosale\Plugin\Product\Form
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class ProductForm extends Form
{
    public function init($productData = [])
    {
        $languages = $this->getLanguages();

        $this->getXajaxManager()->registerFunctions([
            'AddProducer'  => [$this->get('producer.repository'), 'addSimpleProducer'],
            'AddDeliverer' => [$this->get('deliverer.repository'), 'addSimpleDeliverer'],
            'AddTax'       => [$this->get('tax.repository'), 'addSimpleTax']
        ]);

        $form = $this->addForm([
            'name' => 'product'
        ]);

        $basicPane = $form->addChild($this->addFieldset([
            'name'  => 'basic_pane',
            'label' => $this->trans('Basic settings')
        ]));

        $basicLanguageData = $basicPane->addChild($this->addFieldsetLanguage([
            'name'      => 'language_data',
            'label'     => $this->trans('Translations'),
            'languages' => $languages
        ]));

        $basicLanguageData->addChild($this->addTextField([
            'name'  => 'name',
            'label' => $this->trans('Name'),
            'rules' => [
                $this->addRuleRequired($this->trans('Name is required'))
            ]
        ]));

        $basicLanguageData->addChild($this->addTextField([
            'name'  => 'slug',
            'label' => $this->trans('Slug'),
            'rules' => [
                $this->addRuleRequired($this->trans('Slug is required')),
                $this->addRuleUnique($this->trans('Slug already exists'),
                    [
                        'table'   => 'product_translation',
                        'column'  => 'slug',
                        'exclude' => [
                            'column' => 'product_id',
                            'values' => $this->getParam('id')
                        ]
                    ]
                ),
                $this->addRuleFormat($this->trans('Only alphanumeric characters are allowed'), '/^[A-Za-z0-9-_\",\'\s]+$/')
            ]
        ]));

        $basicPane->addChild($this->addCheckbox([
            'name'    => 'enabled',
            'label'   => $this->trans('Enabled'),
            'default' => '0'
        ]));

        $basicPane->addChild($this->addTextField([
            'name'  => 'ean',
            'label' => $this->trans('EAN')
        ]));

        $basicPane->addChild($this->addTextField([
            'name'  => 'sku',
            'label' => $this->trans('SKU')
        ]));

        $basicPane->addChild($this->addSelect([
            'name'            => 'producer_id',
            'label'           => $this->trans('Producer'),
            'addable'         => true,
            'onAdd'           => 'xajax_AddProducer',
            'add_item_prompt' => $this->trans('Enter producer name'),
            'options'         => $this->makeOptions($this->get('producer.repository')->getAllProducerToSelect(), true)
        ]));

        $basicPane->addChild($this->addMultiSelect([
            'name'            => 'deliverers',
            'label'           => $this->trans('Deliverers'),
            'addable'         => true,
            'onAdd'           => 'xajax_AddDeliverer',
            'add_item_prompt' => $this->trans('Enter deliverer name'),
            'options'         => $this->makeOptions($this->get('deliverer.repository')->getAllDelivererToSelect())
        ]));

        $metaData = $form->addChild($this->addFieldset([
            'name'  => 'meta_data',
            'label' => $this->trans('Meta settings')
        ]));

        $languageData = $metaData->addChild($this->addFieldsetLanguage([
            'name'      => 'language_data',
            'label'     => $this->trans('Translations'),
            'languages' => $languages
        ]));

        $languageData->addChild($this->addTextField([
            'name'  => 'meta_title',
            'label' => $this->trans('Title')
        ]));

        $languageData->addChild($this->addTextArea([
            'name'  => 'meta_keywords',
            'label' => $this->trans('Keywords'),
        ]));

        $languageData->addChild($this->addTextArea([
            'name'  => 'description',
            'label' => $this->trans('Description'),
        ]));

        $stockPane = $form->addChild($this->addFieldset([
            'name'  => 'stock_pane',
            'label' => $this->trans('Stock settings')
        ]));

        $stockPane->addChild($this->addTextField([
            'name'    => 'stock',
            'label'   => $this->trans('Stock'),
            'rules'   => [
                $this->addRuleRequired($this->trans('Stock is required')),
                $this->addRuleFormat($this->trans('Only numeric characters are allowed'), '/[0-9]{1,}/')
            ],
            'suffix'  => $this->trans('pcs'),
            'default' => 0
        ]));

        $stockPane->addChild($this->addCheckbox([
            'name'  => 'track_stock',
            'label' => $this->trans('Track stock')
        ]));

        $categoryPane = $form->addChild($this->addFieldset([
            'name'  => 'category_pane',
            'label' => $this->trans('Categories')
        ]));

        $categoryPane->addChild($this->addTree([
            'name'       => 'category',
            'label'      => $this->trans('Categories'),
            'choosable'  => false,
            'selectable' => true,
            'sortable'   => false,
            'clickable'  => false,
            'items'      => $this->get('category.repository')->getCategoriesTree()
        ]));

        $pricePane = $form->addChild($this->addFieldset([
            'name'  => 'price_pane',
            'label' => $this->trans('Price settings')
        ]));

        $vat = $pricePane->addChild($this->addSelect([
            'name'            => 'tax_id',
            'label'           => $this->trans('Tax'),
            'options'         => $this->makeOptions($this->get('tax.repository')->getAllTaxToSelect(), true),
            'addable'         => true,
            'onAdd'           => 'xajax_AddTax',
            'add_item_prompt' => $this->trans('Enter tax value')
        ]));

        $currencies = $this->get('currency.repository')->getAllCurrencyToSelect();

        $pricePane->addChild($this->addSelect([
            'name'    => 'sell_currency_id',
            'label'   => $this->trans('Currency for sell prices'),
            'options' => $this->makeOptions($currencies)
        ]));

        $pricePane->addChild($this->addSelect([
            'name'    => 'buy_currency_id',
            'label'   => $this->trans('Currency for buy prices'),
            'options' => $this->makeOptions($currencies)
        ]));

        $pricePane->addChild($this->addPrice([
            'name'      => 'buy_price',
            'label'     => $this->trans('Buy price'),
            'rules'     => [
                $this->addRuleRequired($this->trans('Buy price is required')),
                $this->addRuleFormat($this->trans('Only numeric characters are allowed'), '/[0-9]{1,}/')
            ],
            'filters'   => [
                $this->addFilterCommaToDotChanger()
            ],
            'vat_field' => $vat
        ]));

        $standardPrice = $pricePane->addChild($this->addFieldset([
            'name'  => 'standard_price',
            'label' => $this->trans('Standard sell price'),
            'class' => 'priceGroup'
        ]));

        $standardPrice->addChild($this->addPrice([
            'name'      => 'sell_price',
            'label'     => $this->trans('Sell price'),
            'rules'     => [
                $this->addRuleRequired($this->trans('Sell price is required')),
                $this->addRuleFormat($this->trans('Only numeric characters are allowed'), '/[0-9]{1,}/')
            ],
            'filters'   => [
                $this->addFilterCommaToDotChanger()
            ],
            'vat_field' => $vat
        ]));

        $measurementsPane = $form->addChild($this->addFieldset([
            'name'  => 'measurements_pane',
            'label' => $this->trans('Measurements')
        ]));

        $measurementsPane->addChild($this->addTextField([
            'name'    => 'weight',
            'label'   => $this->trans('Weight'),
            'rules'   => [
                $this->addRuleRequired($this->trans('Weight is required')),
                $this->addRuleFormat($this->trans('Only numeric characters are allowed'), '/[0-9]{1,}/')
            ],
            'filters' => [
                $this->addFilterCommaToDotChanger()
            ],
            'default' => 0
        ]));

        $measurementsPane->addChild($this->addTextField([
            'name'    => 'width',
            'label'   => $this->trans('Width'),
            'filters' => [
                $this->addFilterCommaToDotChanger()
            ]
        ]));

        $measurementsPane->addChild($this->addTextField([
            'name'    => 'height',
            'label'   => $this->trans('Height'),
            'filters' => [
                $this->addFilterCommaToDotChanger()
            ]
        ]));

        $measurementsPane->addChild($this->addTextField([
            'name'    => 'depth',
            'label'   => $this->trans('Depth'),
            'suffix'  => 'cm',
            'filters' => [
                $this->addFilterCommaToDotChanger()
            ]
        ]));

        $measurementsPane->addChild($this->addTextField([
            'name'    => 'package_size',
            'label'   => $this->trans('Package size'),
            'rules'   => [
                $this->addRuleRequired($this->trans('Package size is required')),
                $this->addRuleFormat($this->trans('Only numeric characters are allowed'), '/[0-9]{1,}/')
            ],
            'filters' => [
                $this->addFilterCommaToDotChanger()
            ],
            'default' => 1
        ]));

        $descriptionPane = $form->addChild($this->addFieldset([
            'name'  => 'description_pane',
            'label' => $this->trans('Product descriptions')
        ]));

        $descriptionLanguageData = $descriptionPane->addChild($this->addFieldsetLanguage([
            'name'      => 'language_data',
            'label'     => $this->trans('Translations'),
            'languages' => $languages
        ]));

        $descriptionLanguageData->addChild($this->addRichTextEditor([
            'name'  => 'short_description',
            'label' => $this->trans('Short description'),
            'rows'  => 20
        ]));

        $descriptionLanguageData->addChild($this->addRichTextEditor([
            'name'  => 'description',
            'label' => $this->trans('Description'),
            'rows'  => 30
        ]));

        $descriptionLanguageData->addChild($this->addRichTextEditor([
            'name'  => 'long_description',
            'label' => $this->trans('Long description'),
            'rows'  => 30
        ]));

        $photosPane = $form->addChild($this->addFieldset([
            'name'  => 'photos_pane',
            'label' => $this->trans('Photos')
        ]));

        $photosPane->addChild($this->addTip([
            'tip'       => '<p align="center">' . $this->trans('Please choose files from library or upload them from disk') . '</p>',
            'direction' => Form\Elements\Tip::DOWN
        ]));

        $photosPane->addChild($this->addImage([
            'name'       => 'photo',
            'label'      => $this->trans('Photos'),
            'repeat_min' => 0,
            'repeat_max' => Form\Elements\ElementInterface::INFINITE,
            'limit'      => 1000,
            'upload_url' => $this->generateUrl('admin.file.add'),
            'main_id'    => isset($productData['photos_pane']['main']) ? $productData['photos_pane']['main'] : ''
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
            $this->addFilterTrim(),
            $this->addFilterSecure()
        ]);

        $event = new ProductFormEvent($form, $productData);

        $this->getDispatcher()->dispatch(ProductFormEvent::FORM_INIT_EVENT, $event);

        $form->populate($event->getPopulateData());

        return $form;
    }
}
