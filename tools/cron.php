<?php

namespace Gekosale\Tools;

use Gekosale\App as App;
use PDO;
use PDOException;
use Exception;

/**
 * applications/Gekosale/App.php:254
 *      self::$config = include_once ROOTPATH . 'config' . DS . 'settings.php';
 */
$config = eval('?>' . file_get_contents(ROOTPATH . DS . 'config' . DS . 'settings.php'));

$config = $config['database'];

try {
	$db = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'], $config['user'], $config['password'], array(
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
	));
	$stmt = $db->query('SELECT url FROM viewurl WHERE viewid=3');
	$host = $stmt->fetchColumn();
	$db = NULL;
}
catch(PDOException $e) {
	echo $e->getMessage();
	exit(-1);
}

$_SERVER['HTTP_HOST'] = $host;
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SCRIPT_FILENAME'] = __FILE__;
$_SERVER['SERVER_PORT'] = 80;

$__LOCAL_CATALOG = '';
$__SERVER_DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
if (substr($_SERVER['DOCUMENT_ROOT'], - 1) == '/'){
	$__SERVER_DOCUMENT_ROOT = substr($_SERVER['DOCUMENT_ROOT'], 0, - 1);
}
else{
	$__SERVER_DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
}

$__SCRIPT_FILENAME = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : str_replace($__SERVER_DOCUMENT_ROOT, '', $_SERVER['SCRIPT_FILENAME']);
if (($indexPosition = strpos($__SCRIPT_FILENAME, '/index.php')) > 0){
	$__LOCAL_CATALOG = substr($_SERVER['REQUEST_URI'], 0, $indexPosition);
	if (strpos($__LOCAL_CATALOG, '/') == 0){
		$__LOCAL_CATALOG = substr($__LOCAL_CATALOG, 1);
	}
}

if (strlen($__LOCAL_CATALOG) > 0){
	if (substr($__LOCAL_CATALOG, - 2) == '//'){
		$__LOCAL_CATALOG = substr($__LOCAL_CATALOG, 0, - 1);
	}
}
DEFINE('LOCAL_CATALOG', $__LOCAL_CATALOG);

set_include_path(ROOTPATH . 'lib' . DS . PATH_SEPARATOR . get_include_path());
include_once (ROOTPATH . 'lib' . DS . 'xajax' . DS . 'xajax_core' . DS . 'xajax.inc.php');

require_once ('bootstrap.php');

App::getRegistry()->router->setVariables();
DEFINE('DESIGNPATH', App::getURLForDesignDirectory());

App::getRegistry()->xajax = new \Xajax();
App::getRegistry()->xajaxInterface = new \Gekosale\XajaxInterface();

App::getRegistry()->template = new \Gekosale\Template(App::getRegistry(), App::getRegistry()->router->getMode());
App::getRegistry()->template->setStaticTemplateVariables();

DEFINE('URL', App::getHost(1) . '/' . LOCAL_CATALOG);

class Cron extends \Gekosale\Tools\Tool
{

	/**
	 * Application logic goes here
	 */
	public function run ()
	{
		$model = App::getModel('exchangexml');

		if ($model->isLocked()) {
			$this->log('Niektóre operacje są w trakcie wykonywania');
			return;
		}

		$this->log('Import / Export');

		foreach ($model->getOperations() as $operation)	{
			// periodically
			if (in_array($operation['status'], array(-1,0,1)) && !empty($operation['periodically']) && strtotime($operation['lastdate']) < time()+$operation['interval']) {
				if ($model->runOperation($operation['idexchange'])) {
					$this->log('Periodically ' .$operation['name']);
				}
			}
			else if (in_array($operation['status'], array(-1,1)) && empty($operation['periodically'])) {
				if ($model->runOperation($operation['idexchange'])) {
					$this->log($operation['name']);
				}
			}
		}
	}
}