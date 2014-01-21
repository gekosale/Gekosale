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
 * $Revision: 547 $
 * $Author: gekosale $
 * $Date: 2011-09-27 08:51:30 +0200 (Wt, 27 wrz 2011) $
 * $Id: subpagelayout.php 547 2011-09-27 06:51:30Z gekosale $ 
 */
namespace Gekosale;
use FormEngine;
use sfEvent;

class subpagelayoutController extends Component\Controller\Admin
{

    public function index ()
    {
        $subpages = $this->model->getSubpageTree();
        
        if ($this->id == '' && count($subpages) > 0){
            if (Helper::getViewId() > 0){
                App::redirect(__ADMINPANE__ . '/subpagelayout/edit/' . $this->registry->loader->getParam('pageschemeid'));
            }
            else{
                App::redirect(__ADMINPANE__ . '/subpagelayout/edit/' . current(array_keys($subpages)));
            }
        }
    }

    public function edit ()
    {
        App::getModel('contextmenu')->add($this->trans('TXT_TEMPLATE_LIBRARY'), $this->getRouter()->url('admin', 'templateeditor'));
        
        $subpages = $this->model->getSubpageTree();
        $pageschemeid = $this->registry->core->getParam(0);
        if (Helper::getViewId() > 0 && ! in_array($pageschemeid, array_keys($subpages))){
            App::redirect(__ADMINPANE__ . '/subpagelayout/edit/' . current(array_keys($subpages)));
        }
        $templateMainInfo = App::getModel('pagescheme')->getTemplateNameToEdit($pageschemeid);
        $id = $this->registry->core->getParam(1);
        if ((int) $id == 0){
            App::redirect(__ADMINPANE__ . '/subpagelayout/edit/' . $pageschemeid . ',' . $this->model->getFirstPageScheme($pageschemeid));
        }
        
        $subpageLayout = App::getModel('subpagelayout')->getSubPageLayoutAll($id);
        
        if (! isset($subpageLayout[0]['name'])){
            App::redirect(__ADMINPANE__ . '/subpagelayout');
        }
        
        $tree = new FormEngine\Elements\Form(Array(
            'name' => 'scheme_tree',
            'action' => '',
            'method' => 'post'
        ));
        
        $tree->AddChild(new FormEngine\Elements\Tree(Array(
            'name' => 'pagescheme',
            'sortable' => false,
            'retractable' => false,
            'selectable' => false,
            'clickable' => true,
            'deletable' => false,
            'addable' => false,
            'items' => $subpages,
            'onClick' => 'openSubpageEditor',
            'active' => $pageschemeid . ',' . $id
        )));
        
        $form = new FormEngine\Elements\Form(Array(
            'name' => 'subpagelayout',
            'action' => '',
            'method' => 'post'
        ));
        
        $columnsEdit = $form->AddChild(new FormEngine\Elements\Fieldset(Array(
            'name' => 'columns',
            'label' => $this->trans('TXT_SUBPAGE_COLUMNS')
        )));
        
        $subpages = App::getModel('subpagelayout')->getSubPageLayoutAllToSelect($id);
        
        $subpagelayoutid = $columnsEdit->AddChild(new FormEngine\Elements\Constant(Array(
            'name' => 'subpagelayout_subpage',
            'label' => $this->trans('Podstrona')
        )));
        
        $subpagelayoutid = $columnsEdit->AddChild(new FormEngine\Elements\Hidden(Array(
            'name' => 'subpagelayoutid'
        )));
        
        $columnsDataEdit = $columnsEdit->AddChild(new FormEngine\Elements\FieldsetRepeatable(Array(
            'name' => 'columns_data',
            'label' => $this->trans('TXT_COLUMNS_DATA'),
            'repeat_min' => 1,
            'repeat_max' => FormEngine\FE::INFINITE
        )));
        
        $columnsDataEdit->AddChild(new FormEngine\Elements\Tip(Array(
            'tip' => '<p>Aby kolumna rozciągnęła się na całą, pozostałą część strony, jako jej szerokość podaj wartość <strong>0</strong>.</p><p>Jeśli kilka kolumn będzie miało szerokość 0, wówczas zostaną im automatycznie przyznane równe części pozostałego miejsca.</p>',
            'retractable' => false
        )));
        
        $columnsDataEdit->AddChild(new FormEngine\Elements\TextField(Array(
            'name' => 'columns_width',
            'label' => $this->trans('TXT_WIDTH'),
            'rules' => Array(
                new FormEngine\Rules\Required($this->trans('ERR_EMPTY_WIDTH'))
            )
        )));
        
        $boxDataEdit = $columnsDataEdit->AddChild(new FormEngine\Elements\LayoutBoxesList(Array(
            'name' => 'layout_boxes',
            'label' => 'Wybierz boksy',
            'boxes' => FormEngine\Option::Make(App::getModel('subpagelayout')->getBoxesAllToSelect($subpageLayout[0]['name'], $pageschemeid))
        )));
        
        $form->AddFilter(new FormEngine\Filters\Trim());
        $form->AddFilter(new FormEngine\Filters\Secure());
        
        $subpagelayoutcolumn = App::getModel('subpagelayout')->getSubPageLayoutColumn($id);
        $populate = Array();
        
        if (is_array($subpagelayoutcolumn) && count($subpagelayoutcolumn) > 0){
            if (isset($subpagelayoutcolumn['subpagelayoutid']) && $subpagelayoutcolumn['subpagelayoutid'] > 0){
                $populate['columns']['subpagelayout_subpage'] = $subpages[$subpagelayoutcolumn['subpagelayoutid']];
                $populate['columns']['subpagelayoutid'] = $subpagelayoutcolumn['subpagelayoutid'];
            }
            if (isset($subpagelayoutcolumn['columns']) && count($subpagelayoutcolumn['columns']) > 0){
                foreach ($subpagelayoutcolumn['columns'] as $column){
                    $populate['columns']['columns_data'][$column['idsubpagelayoutcolumn']] = Array(
                        'columns_width' => $column['width']
                    );
                    if (count($column['subpagelayoutcolumnbox']) > 0){
                        foreach ($column['subpagelayoutcolumnbox'] as $boxes){
                            $populate['columns']['columns_data'][$column['idsubpagelayoutcolumn']]['layout_boxes'][$boxes['order']] = Array(
                                'box' => $boxes['layoutboxid'],
                                'span' => $boxes['colspan'],
                                'collapsed' => (int) $boxes['collapsed']
                            );
                        }
                    }
                }
            }
            $form->Populate($populate);
        }
        
        if ($form->Validate(FormEngine\FE::SubmittedData())){
            try{
                App::getModel('subpagelayout')->editSubpageLayout($form->getSubmitValues(), $id);
                App::getModel('subpagelayout')->flushCache($subpages[$subpagelayoutcolumn['subpagelayoutid']]);
                App::redirect(__ADMINPANE__ . '/subpagelayout/edit/' . $pageschemeid . ',' . $id);
            }
            catch (Exception $e){
                App::getContainer()->get('session')->setVolatileSubpageLayoutAdd(1, false);
            }
        }
        
        $error = App::getContainer()->get('session')->getVolatileSubpageLayoutAdd();
        if ($error[0] == 1){
            $this->registry->template->assign('error', $e->getMessage());
        }
        
        $this->registry->xajaxInterface->registerFunction(Array(
            'DeleteSubpageLayout',
            $this,
            'DeleteSubpageLayoutForAjax'
        ));
        
        $this->renderLayout(array(
            'subpageLayout' => $subpageLayout[0],
            'form' => $form->Render(),
            'tree' => $tree->Render()
        ));
    }

    public function DeleteSubpageLayoutForAjax ($request)
    {
        return Array(
            'status' => App::getModel('subpagelayout')->DeleteSubpageLayout($request['idsubpagelayout'])
        );
    }
}