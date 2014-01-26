<?php

namespace Gekosale\Component\Dashboard\Controller\Admin;

use Gekosale\Core\Component\Controller\Admin;
use FormEngine;

class Dashboard extends Admin
{

    public function index()
    {
        return Array(
            'from'=> date('Y/m/1')

        );
//        $this->registry->xajax->processRequest();
//        $this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
//        $this->registry->template->assign('from', date('Y/m/1'));
//        $this->registry->template->assign('to', date('Y/m/d'));
//        $this->registry->template->assign('summaryStats', $this->model->getSummaryStats());
//        $this->registry->template->assign('topten', $this->model->getTopTen());
//        $this->registry->template->assign('opinions', $this->model->getOpinions());
//        $this->registry->template->assign('mostsearch', $this->model->getMostSearch());
//        $this->registry->template->assign('lastorder', $this->model->getLastOrder());
//        $this->registry->template->assign('newclient', $this->model->getNewClient());
//        $this->registry->template->assign('clientOnline', $this->model->getClientOnline());
//        $this->renderLayout();
    }
}