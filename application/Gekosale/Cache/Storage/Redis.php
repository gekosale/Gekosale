<?php

namespace Gekosale\Cache\Storage;
use Gekosale\Helper;
use Predis;

class Redis
{

    protected $prefix;

    protected $settings;

    protected $client;

    public function __construct (Array $settings)
    {
        $this->settings = $settings;
        
        $this->ttl = 900;
        
        $this->client = new Predis\Client($settings['redis'], array(
            'prefix' => 'cache:' . $config['database']['dbname'] . ':'
        ));
    }

    protected function getPrefix ($key)
    {
        return sprintf('%s:%s:%s:%s:%s:', 'cache', $this->settings['database']['dbname'], Helper::getViewId(), Helper::getLanguageId(), $key);
    }

    public function save ($key, $value, $ttl = NULL)
    {
        $this->client->setex($key, ($ttl == NULL) ? $this->ttl : $ttl, $value);
    }

    public function load ($key)
    {
        if ($data = $this->client->get($key)){
            return $data;
        }
        else{
            return FALSE;
        }
    }

    public function delete ($key)
    {
        $this->client->del($key);
    }

    public function deleteAll ()
    {
        $this->client->flushdb();
    }
}
