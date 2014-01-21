<?php

namespace Gekosale\Tests;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\Finder\Finder;

abstract class WebTestCase extends \PHPUnit_Framework_TestCase
{

    protected static function createClient ()
    {
        return new Client();
    }
}