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

class GlobalSettingsForm extends Component\Form
{
    protected $populateData;

    public function setPopulateData($Data)
    {
        $this->populateData = $Data;
    }

    public function initForm()
    {
        $form = new FormEngine\Elements\Form(Array(
            'name'   => 'globalsettings',
            'action' => '',
            'method' => 'post'
        ));

        $requiredData = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
                'name'  => 'config_data',
                'label' => $this->trans('TXT_SYSTEM_CONFIGURATION')
            ))
        );

        $requiredData->AddChild(new FormEngine\Elements\Tip(Array(
                'tip'       => '<p>' . $this->trans('TXT_ATTENTION_AFTER_CHANGING_ADMINLINK') . '</p>',
                'direction' => FormEngine\Elements\Tip::DOWN
            ))
        );

        $requiredData->AddChild(new FormEngine\Elements\TextField(Array(
                'name'    => 'admin_panel_link',
                'label'   => $this->trans('TXT_ADMIN_PANEL_LINK'),
                'rules'   => Array(
                    new FormEngine\Rules\Required($this->trans('ERR_EMPTY_ADMIN_PANEL_LINK'))
                ),
                'default' => __ADMINPANE__
            ))
        );

        $requiredData->AddChild(new FormEngine\Elements\Tip(Array(
                'tip'       => '<p>' . $this->trans('TXT_SSL_HELP') . '</p>',
                'direction' => FormEngine\Elements\Tip::DOWN
            ))
        );

        $requiredData->AddChild(new FormEngine\Elements\Checkbox(Array(
                'name'  => 'ssl',
                'label' => $this->trans('TXT_ENABLE_SSL')
            ))
        );

        $cdnSettings = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
                'name'  => 'cdnsettings_data',
                'label' => $this->trans('TXT_CDN_SETTINGS')
            ))
        );

        $cdnSettings->AddChild(new FormEngine\Elements\Checkbox(Array(
                'name'  => 'enable',
                'label' => $this->trans('TXT_ENABLE_CDN')
            ))
        );

        $gallerySettings = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
                'name'  => 'gallerysettings_data',
                'label' => $this->trans('TXT_GALLERY_SETTINGS')
            ))
        );

        $gallerySettings->AddChild(new FormEngine\Elements\ColourSchemePicker(Array(
                'name'          => 'colour',
                'label'         => $this->trans('TXT_GALLERY_BGCOLOUR'),
                'type_colour'   => true,
                'type_gradient' => false,
                'type_image'    => false,
                'file_source'   => 'upload/'
            ))
        );

        $gallerySettings->AddChild(new FormEngine\Elements\Tip(Array(
                'tip'       => '<p>' . $this->trans('TXT_GALLERY_SMALLEST_IMAGE_SETTINGS') . '</p>',
                'direction' => FormEngine\Elements\Tip::DOWN
            ))
        );

        $gallerySettings->AddChild(new FormEngine\Elements\TextField(Array(
                'name'   => 'smallest_width',
                'label'  => $this->trans('TXT_WIDTH'),
                'rules'  => Array(
                    new FormEngine\Rules\Required($this->trans('ERR_EMPTY_MAIL_FROMEMAIL'))
                ),
                'suffix' => 'px'
            ))
        );

        $gallerySettings->AddChild(new FormEngine\Elements\TextField(Array(
                'name'   => 'smallest_height',
                'label'  => $this->trans('TXT_HEIGHT'),
                'rules'  => Array(
                    new FormEngine\Rules\Required($this->trans('ERR_EMPTY_MAIL_FROMEMAIL'))
                ),
                'suffix' => 'px'
            ))
        );

        $gallerySettings->AddChild(new FormEngine\Elements\Checkbox(Array(
                'name'  => 'smallest_keepproportion',
                'label' => $this->trans('TXT_KEEP_PROPORTION')
            ))
        );

        $gallerySettings->AddChild(new FormEngine\Elements\Tip(Array(
                'tip'       => '<p>' . $this->trans('TXT_GALLERY_SMALL_IMAGE_SETTINGS') . '</p>',
                'direction' => FormEngine\Elements\Tip::DOWN
            ))
        );

        $gallerySettings->AddChild(new FormEngine\Elements\TextField(Array(
                'name'   => 'small_width',
                'label'  => $this->trans('TXT_WIDTH'),
                'rules'  => Array(
                    new FormEngine\Rules\Required($this->trans('ERR_EMPTY_MAIL_FROMEMAIL'))
                ),
                'suffix' => 'px'
            ))
        );

        $gallerySettings->AddChild(new FormEngine\Elements\TextField(Array(
                'name'   => 'small_height',
                'label'  => $this->trans('TXT_HEIGHT'),
                'rules'  => Array(
                    new FormEngine\Rules\Required($this->trans('ERR_EMPTY_MAIL_FROMEMAIL'))
                ),
                'suffix' => 'px'
            ))
        );

        $gallerySettings->AddChild(new FormEngine\Elements\Checkbox(Array(
                'name'  => 'small_keepproportion',
                'label' => $this->trans('TXT_KEEP_PROPORTION')
            ))
        );

        $gallerySettings->AddChild(new FormEngine\Elements\Tip(Array(
                'tip'       => '<p>' . $this->trans('TXT_GALLERY_MEDIUM_IMAGE_SETTINGS') . '</p>',
                'direction' => FormEngine\Elements\Tip::DOWN
            ))
        );

        $gallerySettings->AddChild(new FormEngine\Elements\TextField(Array(
                'name'   => 'medium_width',
                'label'  => $this->trans('TXT_WIDTH'),
                'rules'  => Array(
                    new FormEngine\Rules\Required($this->trans('ERR_EMPTY_MAIL_FROMEMAIL'))
                ),
                'suffix' => 'px'
            ))
        );

        $gallerySettings->AddChild(new FormEngine\Elements\TextField(Array(
                'name'   => 'medium_height',
                'label'  => $this->trans('TXT_HEIGHT'),
                'rules'  => Array(
                    new FormEngine\Rules\Required($this->trans('ERR_EMPTY_MAIL_FROMEMAIL'))
                ),
                'suffix' => 'px'
            ))
        );

        $gallerySettings->AddChild(new FormEngine\Elements\Checkbox(Array(
                'name'  => 'medium_keepproportion',
                'label' => $this->trans('TXT_KEEP_PROPORTION')
            ))
        );

        $gallerySettings->AddChild(new FormEngine\Elements\Tip(Array(
                'tip'       => '<p>' . $this->trans('TXT_GALLERY_NORMAL_IMAGE_SETTINGS') . '</p>',
                'direction' => FormEngine\Elements\Tip::DOWN
            ))
        );

        $gallerySettings->AddChild(new FormEngine\Elements\TextField(Array(
                'name'   => 'normal_width',
                'label'  => $this->trans('TXT_WIDTH'),
                'rules'  => Array(
                    new FormEngine\Rules\Required($this->trans('ERR_EMPTY_MAIL_FROMEMAIL'))
                ),
                'suffix' => 'px'
            ))
        );

        $gallerySettings->AddChild(new FormEngine\Elements\TextField(Array(
                'name'   => 'normal_height',
                'label'  => $this->trans('TXT_HEIGHT'),
                'rules'  => Array(
                    new FormEngine\Rules\Required($this->trans('ERR_EMPTY_MAIL_FROMEMAIL'))
                ),
                'suffix' => 'px'
            ))
        );

        $gallerySettings->AddChild(new FormEngine\Elements\Checkbox(Array(
                'name'  => 'normal_keepproportion',
                'label' => $this->trans('TXT_KEEP_PROPORTION')
            ))
        );

        $gallerySettings->AddChild(new FormEngine\Elements\Tip(Array(
                'tip'       => '<p>' . $this->trans('TXT_GALLERY_LARGE_IMAGE_SETTINGS') . '</p>',
                'direction' => FormEngine\Elements\Tip::DOWN
            ))
        );

        $gallerySettings->AddChild(new FormEngine\Elements\TextField(Array(
                'name'   => 'large_width',
                'label'  => $this->trans('TXT_WIDTH'),
                'rules'  => Array(
                    new FormEngine\Rules\Required($this->trans('ERR_EMPTY_MAIL_FROMEMAIL'))
                ),
                'suffix' => 'px'
            ))
        );

        $gallerySettings->AddChild(new FormEngine\Elements\TextField(Array(
                'name'   => 'large_height',
                'label'  => $this->trans('TXT_HEIGHT'),
                'rules'  => Array(
                    new FormEngine\Rules\Required($this->trans('ERR_EMPTY_MAIL_FROMEMAIL'))
                ),
                'suffix' => 'px'
            ))
        );

        $gallerySettings->AddChild(new FormEngine\Elements\Checkbox(Array(
                'name'  => 'large_keepproportion',
                'label' => $this->trans('TXT_KEEP_PROPORTION')
            ))
        );

        $interfaceSettings = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
                'name'  => 'interface',
                'label' => $this->trans('TXT_INTERFACE_SETTINGS')
            ))
        );

        $interfaceSettings->AddChild(new FormEngine\Elements\TextField(Array(
                'name'    => 'datagrid_rows_per_page',
                'label'   => $this->trans('TXT_DATAGRID_ROWS_PER_PAGE'),
                'rules'   => Array(
                    new FormEngine\Rules\Required($this->trans('ERR_EMPTY_DATAGRID_ROWS_PER_PAGE'))
                ),
                'default' => 50
            ))
        );

        $interfaceSettings->AddChild(new FormEngine\Elements\Select(Array(
                'name'    => 'datagrid_click_row_action',
                'label'   => $this->trans('TXT_DATAGRID_CLICK_ROW_ACTION'),
                'options' => FormEngine\Option::Make(Array(
                          'edit'        => $this->trans('TXT_DATAGRID_EDIT_ROW'),
                          'contextmenu' => $this->trans('TXT_SHOW_CONTEXT_MENU')
                      )
                  )
            ))
        );

        $robotsSettings = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
                'name'  => 'robots',
                'label' => $this->trans('TXT_ROBOTS')
            ))
        );

        $robotsSettings->AddChild(new FormEngine\Elements\Textarea(Array(
                'name'    => 'content',
                'label'   => $this->trans('TXT_ROBOTS_CONTENT'),
                'rows' => 20
            ))
        );

        $Data = Event::dispatch($this, 'admin.globalsettings.initForm', Array(
                'form' => $form,
                'id'   => (int) $this->registry->core->getParam(),
                'data' => $this->populateData
            )
        );

        if (!empty($Data)) {
            $form->Populate($Data);
        }

        $form->AddFilter(new FormEngine\Filters\NoCode());
        $form->AddFilter(new FormEngine\Filters\Trim());
        $form->AddFilter(new FormEngine\Filters\Secure());

        return $form;
    }
}