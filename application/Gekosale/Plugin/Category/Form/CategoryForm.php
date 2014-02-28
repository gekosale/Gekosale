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
namespace Gekosale\Plugin\Category\Form;

use Gekosale\Core\Form;
use Gekosale\Plugin\Category\Event\CategoryFormEvent;

/**
 * Class CategoryForm
 *
 * @package Gekosale\Plugin\Category\Form
 * @author  Adam Piotrowski <adam@gekosale.com>
 */
class CategoryForm extends Form
{

    public function init($categoryData = Array())
    {
        $form = new FormEngine\Elements\Form([
            'name' => 'category',
        ]);

        $requiredData = $form->addChild($this->addFieldset([
            'name'  => 'required_data',
            'label' => $this->trans('Required data')
        ]));

        $languageData = $requiredData->addChild($this->addFieldsetLanguage([
            'name'  => 'language_data',
            'label' => $this->trans('Translations')
        ]));

        $languageData->addChild($this->addTextField([
            'name'  => 'name',
            'label' => $this->trans('Name'),
            'rules' => [
                $this->addRuleRequired('Name is required')
            ]
        ]));

        $languageData->addChild($this->addTextField([
            'name'  => 'slug',
            'label' => $this->trans('Slug'),
            'rules' => [
                $this->addRuleRequired($this->trans('Slug is required')),
                $this->addRuleFormat($this->trans('Only alphanumeric characters are allowed'), '/^[A-Za-z0-9-_\/\",\'\s]+$/'),
                $this->addRuleLanguageUnique($this->trans('Slug already exists'),
                    [
                        'table'   => 'category_translation',
                        'column'  => 'slug',
                        'exclude' => [
                            'column' => 'category_id',
                            'values' => $this->getParam('id')
                        ]
                    ]
                )
            ]
        ]));

        $requiredData->addChild($this->addCheckBox([
            'name'    => 'is_enabled',
            'label'   => $this->trans('Enabled'),
            'default' => '1'
        ]));

        $requiredData->addChild($this->addTextField([
            'name'  => 'hierarchy',
            'label' => $this->trans('Hierarchy')
        ]));

        $requiredData->addChild($this->addStaticText([
            'text' => '<p>' . $this->trans('Parent category') . '</p>'
        ]));

        if ($this->populateData['required_data']['categoryid']) {
            $active = $this->populateData['required_data']['categoryid'];
        } else {
            $active = $this->registry->core->getParam();
        }

        $requiredData->addChild(new FormEngine\Elements\Tree(Array(
            'name'          => 'categoryid',
            'label'         => _('TXT_CATEGORY'),
            'comment'       => _('TXT_PARENT_CATEGORY_EXAMPLE'),
            'choosable'     => true,
            'selectable'    => false,
            'sortable'      => false,
            'clickable'     => false,
            'items'         => App::getModel('category')->getChildCategories(0, Array(
                    $active
                )),
            'restrict'      => (int)$this->registry->core->getParam(),
            'load_children' => Array(
                App::getModel('category'),
                'getChildCategories'
            ),
            'rules'         => Array(
                new FormEngine\Rules\Custom(_('ERR_BIND_SELF_PARENT_INVALID'), Array(
                    App::getModel('category'),
                    'checkParentValue'
                ), Array(
                    'categoryid' => (int)$this->registry->core->getParam()
                ))
            )
        )));

        $metaData = $form->addChild(new FormEngine\Elements\Fieldset(Array(
            'name'  => 'meta_data',
            'label' => _('TXT_META_INFORMATION')
        )));

        $metaData->addChild(new FormEngine\Elements\Tip(Array(
            'tip'       => '<p align="center">W przypadku braku informacji META system wygeneruje je automatycznie. W każdej chwili możesz je zmienić edytując dane poniżej.</p>',
            'direction' => FormEngine\Elements\Tip::DOWN
        )));

        $languageData = $metaData->addChild(new FormEngine\Elements\FieldsetLanguage(Array(
            'name'  => 'language_data',
            'label' => _('TXT_LANGUAGE_DATA')
        )));

        $languageData->addChild(new FormEngine\Elements\TextField(Array(
            'name'  => 'keywordtitle',
            'label' => _('TXT_KEYWORD_TITLE')
        )));

        $languageData->addChild(new FormEngine\Elements\Textarea(Array(
            'name'       => 'keyworddescription',
            'label'      => _('TXT_KEYWORD_DESCRIPTION'),
            'comment'    => _('TXT_MAX_LENGTH') . ' 1000',
            'max_length' => 1000
        )));

        $languageData->addChild(new FormEngine\Elements\Textarea(Array(
            'name'       => 'keyword',
            'label'      => _('TXT_KEYWORDS'),
            'comment'    => _('TXT_KEYWORDS_HELP'),
            'max_length' => 1000
        )));

        $descriptionPane = $form->addChild(new FormEngine\Elements\Fieldset(Array(
            'name'  => 'description_pane',
            'label' => _('TXT_DESCRIPTION')
        )));

        $descriptionLanguageData = $descriptionPane->addChild(new FormEngine\Elements\FieldsetLanguage(Array(
            'name'  => 'language_data',
            'label' => _('TXT_LANGUAGE_DATA')
        )));

        $descriptionLanguageData->addChild(new FormEngine\Elements\RichTextEditor(Array(
            'name'       => 'shortdescription',
            'label'      => _('TXT_SHORTDESCRIPTION'),
            'comment'    => _('TXT_MAX_LENGTH') . ' 1000',
            'max_length' => 3000,
            'rows'       => 20
        )));

        $descriptionLanguageData->addChild(new FormEngine\Elements\RichTextEditor(Array(
            'name'       => 'description',
            'label'      => _('TXT_DESCRIPTION'),
            'comment'    => _('TXT_MAX_LENGTH') . ' 5000',
            'max_length' => 3000,
            'rows'       => 30
        )));

        $form->addFilter($this->addFilterNoCode());

        $form->addFilter($this->addFilterSecure());

        $event = new CategoryFormEvent($form, $categoryData);

        $this->getDispatcher()->dispatch(CategoryFormEvent::FORM_INIT_EVENT, $event);

        $form->populate($event->getPopulateData());

        return $form;
    }
}
