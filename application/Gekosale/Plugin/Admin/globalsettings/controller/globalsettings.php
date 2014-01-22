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
 * $Revision: 111 $
 * $Author: gekosale $
 * $Date: 2011-05-06 21:54:00 +0200 (Pt, 06 maj 2011) $
 * $Id: store.php 111 2011-05-06 19:54:00Z gekosale $
 */
namespace Gekosale\Plugin;

use FormEngine;
use sfEvent;

class GlobalsettingsController extends Component\Controller\Admin
{

    public function index()
    {
        App::getModel('contextmenu')->add($this->trans('TXT_STORE_SETTINGS'), $this->getRouter()->url('admin', 'view'));

        $Config = App::getConfig();

        $configData = Array(
            'config_data'          => Array(
                'admin_panel_link' => __ADMINPANE__,
                'ssl'              => (string) (isset($Config['ssl']) && $Config['ssl'] == 1) ? 1 : 0
            ),
            'robots'               => Array(
                'content' => $this->model->getRobotsFile()
            ),
            'gallerysettings_data' => $this->model->getGallerySettings()
        );

        $settingsData = $this->model->getSettings();
        $colour       = $settingsData['gallerysettings_data']['colour'];
        unset($settingsData['gallerysettings_data']);

        $settingsData['gallerysettings_data']['colour'] = array(
            'type'  => 1,
            'start' => $colour
        );

        $this->formModel->setPopulateData(array_merge_recursive($configData, $settingsData));

        $form = $this->formModel->initForm();

        if ($form->Validate(FormEngine\FE::SubmittedData())) {
            try {
                $Data     = $form->getSubmitValues(FormEngine\Elements\Form::FORMAT_FLAT);
                $Settings = $form->getSubmitValues();
                Event::notify($this, 'admin.globalsettings.model.save', Array(
                        'id'   => 1,
                        'data' => $Data
                    )
                );
                $this->model->updateGallerySettings($Data);

                $this->model->updateGlobalSettings($Settings['interface'], 'interface');

                $this->model->updateGlobalSettings(array(
                        'colour' => $Settings['gallerysettings_data']['colour']['start']
                    ), 'gallerysettings_data'
                );

                $this->model->updateGlobalSettings(array(
                        'enable' => $Settings['cdnsettings_data']['enable']
                    ), 'cdnsettings_data'
                );

                App::getContainer()->get('session')->setActiveGlobalSettings(null);
                $this->model->configWriter($Data);
                if (__ADMINPANE__ != $Data['admin_panel_link']) {
                    App::getContainer()->get('session')->flush();
                    App::redirect('');
                } else {
                    $sUrl = __ADMINPANE__ . '/globalsettings';
                    echo "<script>window.location.href({$sUrl});</script>";
                }

                $this->model->setRobotsFile($Settings['robots']['content']);

            } catch (Exception $e) {
                $this->registry->template->assign('error', $e->getMessage());
            }
        }

        $this->renderLayout(array(
                'form' => $form->Render()
            )
        );
    }
}