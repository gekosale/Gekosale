<?php

namespace Gekosale\Profiler;
use Gekosale\Profiler;
use Doctrine\DBAL\Logging\SQLLogger;

class ProfileSQLLogger implements SQLLogger
{

    public $start = null;

    private $query;

    public function startQuery ($sql, array $params = null, array $types = null)
    {
        $this->start = microtime(true);
        $this->query = $sql;
    }

    public function stopQuery ()
    {
        Profiler::addQuery($this->query, microtime(true) - $this->start);
    }
}