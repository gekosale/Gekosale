<?php

namespace Gekosale\Core\Component;
use Gekosale\Core\Component;
use FormEngine;

class Form extends Component
{

    protected $populateData;

    public function setPopulateData ($Data)
    {
        $this->populateData = $Data;
    }
}