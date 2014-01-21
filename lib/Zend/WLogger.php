<?php
namespace Zend;
/**
 * Finally, a light, permissions-checking logging class. Originally written as KLogger for use with wpSearch by Kenny Katzgrau.
 *
 * Usage:
 * $log = new \Zend\WLogger('/var/log/', 'test'); //or just $log = new \Zend\WLogger();
 * $log->dump($x = 5); //Prints to the log file
 * $log->log('Returned a million search results'); //Prints to the log file
 * $log->startTime('big loop'); // starts timing task
 * sleep(1);
 * $log->endTime('big loop'); // ends timing task and prints out elapsed time
 *
 * @version 0.3.0
 *
 * Changes, version 0.3.0:
 * - new: Zend Debug::dump for debug()
 * - new: option to add file name suffix
 * - added Timer 
 * - added file is_resource check
 * - added option to split log file when it's too big
 * - remved all logX methods, instead: put()
 * - updated permissions for created files
 */

class WLogger
{
    const STATUS_LOG_OPEN    = 1;
    const STATUS_OPEN_FAILED = 2;
    const STATUS_LOG_CLOSED  = 3;

    /**
     * We need a default argument value in order to add the ability to easily
     * print out objects etc. But we can't use NULL, 0, FALSE, etc, because those
     * are often the values the developers will test for. So we'll make one up.
     */
    const NO_ARGUMENTS = '\Zend\WLogger::NO_ARGUMENTS';

    const DEFAULT_ALIAS = "LOG";

    private $_logStatus         = self::STATUS_LOG_CLOSED;
    private $_messageQueue      = array();
    private $_logFilePath       = NULL;

    private $_timingTasks       = NULL;

    private static $_dateFormat         = 'Y-m-d G:i:s';
    private static $_defaultPermissions = 0777;
    private static $instances           = array();
    private $_fileHandle        = NULL;
    private $_maxFileSize       = 50000000;
    
    protected static $_sapi = NULL;

    private $_messages = array(
        'writefail'   => 'The file could not be written to. Check that appropriate permissions have been set.',
        'opensuccess' => 'The log file was opened successfully.',
        'openfail'    => 'The file could not be opened. Check permissions.',
    );

    public static function instance($logDirectory = FALSE, $fileName = '')
    {
        if ($logDirectory === FALSE) 
        {
            if (count(self::$instances) > 0) 
            {
                return current(self::$instances);
            } 
            else 
            {
                $logDirectory = dirname(__FILE__);
            }
        }

        if (in_array($logDirectory, array_keys(self::$instances))) 
        {
            return self::$instances[$logDirectory];
        }

        self::$instances[$logDirectory] = new self($logDirectory, $fileName);

        return self::$instances[$logDirectory];
    }

    public function __construct($logDirectory, $fileName = '')
    {
        $logDirectory = rtrim($logDirectory, '\\/');

        $fileNo = 0;  
        while($this->_logFilePath == NULL || (file_exists($this->_logFilePath) && filesize($this->_logFilePath) > $this->_maxFileSize))  
        {
             $this->_logFilePath = $logDirectory
                 . DIRECTORY_SEPARATOR
                 . 'log_'
                . date('Y-m-d')
                . $fileName
                . $fileNo++
                . '.txt';
        }

        if (!file_exists($logDirectory)) 
        {
            mkdir($logDirectory, self::$_defaultPermissions, TRUE);
        }

        if (file_exists($this->_logFilePath) && !is_writable($this->_logFilePath)) 
        {
            $this->_logStatus = self::STATUS_OPEN_FAILED;
            $this->_messageQueue[] = $this->_messages['writefail'];
            return;
        }

        if (($this->_fileHandle = fopen($this->_logFilePath, 'a')) && is_resource($this->_fileHandle)) 
        {
            chmod($this->_logFilePath, self::$_defaultPermissions);
            $this->_logStatus = self::STATUS_LOG_OPEN;
            $this->_messageQueue[] = $this->_messages['opensuccess'];
        } 
        else 
        {
            $this->_logStatus = self::STATUS_OPEN_FAILED;
            $this->_messageQueue[] = $this->_messages['openfail'];
        }
    }


    public function __destruct()
    {
        if ($this->_fileHandle) 
        {
            fclose($this->_fileHandle);
        }
    }

    public function startTime($task = "default") 
    {
        $this->_timingTasks[$task] = microtime(TRUE);
        
        if ($task != "default") 
        {
            $this->log(sprintf("Started timing %s", $task));
        }
    }
  
    public function endTime($task = "default") 
    {
        $startTime = $this->_timingTasks[$task];
        if (isset($startTime)) 
        {
            $end_time = microtime(TRUE) - $startTime;
            //convert to millseconds (most common)
            $end_time *= 1000;
            
            if ($task != "default") 
            {
                $this->log(sprintf("Finished %s in %.3f milliseconds", $task, $end_time));
            } 
            else 
            {
                $this->log(sprintf("Finished in %.3f milliseconds", $end_time));
            }
        }
    }
  
    /**
     * Returns (and removes) the last message from the queue.
     * @return string
     */
    public function getMessage()
    {
        return array_pop($this->_messageQueue);
    }

    public function getMessages()
    {
        return $this->_messageQueue;
    }

    public function clearMessages()
    {
        $this->_messageQueue = array();
    }

    public static function setDateFormat($dateFormat)
    {
        self::$_dateFormat = $dateFormat;
    }

    public function dump($args = self::NO_ARGUMENTS, $line = NULL)
    {
        $this->log($line, $args);
    }

    public function log($line, $args = self::NO_ARGUMENTS)
    {
        if ($line == NULL)
        {
            $line = 'var_dump';
        }
        $time = date(self::$_dateFormat);
        $line = "$time ---> $line";

        if($args !== self::NO_ARGUMENTS) 
        {
            $line .= '; '.self::vdump($args);
        }

        $this->writeFreeFormLine($line . PHP_EOL);
    }

    public function writeFreeFormLine($line)
    {
        if ($this->_logStatus == self::STATUS_LOG_OPEN) 
        {
            if (fwrite($this->_fileHandle, $line) === FALSE) 
            {
                $this->_messageQueue[] = $this->_messages['writefail'];
            }
        }
    }

    public static function getSapi()
    {
        if (self::$_sapi === NULL) {
            self::$_sapi = PHP_SAPI;
        }
        return self::$_sapi;
    }

    public static function setSapi($sapi)
    {
        self::$_sapi = $sapi;
    }

    public static function vdump($var, $echo = FALSE)
    {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();

        // neaten the newlines and indents
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        if (self::getSapi() == 'cli') 
        {
            $output = PHP_EOL
                    . PHP_EOL . $output
                    . PHP_EOL;
        } 
        else 
        {
            if(!extension_loaded('xdebug') AND $echo) 
            {
                $output = htmlspecialchars($output, ENT_QUOTES);
            }
        }

        if ($echo) 
        {
            echo("<pre>".$output."</pre>");
        }
        return trim($output);
    }
}