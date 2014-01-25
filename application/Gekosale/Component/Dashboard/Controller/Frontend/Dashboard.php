<?php

namespace Gekosale\Component\Dashboard\Controller\Frontend;
use Gekosale\Core\Component\Controller\Frontend;

class Dashboard extends Frontend
{

    public function index ()
    {
        return Array(
            'foo' => 'bar'
        );
    }
}