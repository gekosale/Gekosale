<?php

namespace Gekosale\Session\Storage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Gekosale\Session\SessionInterface;
use Gekosale\Db;
use Gekosale\App;
use Predis;

class Redis implements SessionInterface
{

    protected $container;

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
        $this->ttl = $this->container->getParameter('session.session_gc_maxlifetime');
        $this->client = new Predis\Client($this->container->getParameter('redis'), array(
            'prefix' => 'session:' . $config['database']['dbname'] . ':'
        ));
    }

    public function open ()
    {
        return true;
    }

    public function close ()
    {
        return true;
    }

    public function read ($sessionid)
    {
        if ($data = $this->client->get($sessionid)){
            return $data;
        }
        return '';
    }

    public function write ($sessionid, $sessioncontent)
    {
        return $this->client->setex($sessionid, $this->ttl, $sessioncontent);
    }

    public function destroy ($sessionid)
    {
        return $this->client->del($sessionid);
    }

    public function gc ($lifeTime)
    {
        return true;
    }
}