<?php

namespace Gekosale\Tools;

use \Gekosale\App as App;
use \Gekosale\Db as Db;
use Exception;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

require_once (ROOTPATH . 'tools' . DS . 'bootstrap.php');
class Reset extends \Gekosale\Tools\Tool
{

	/**
	 * Application logic goes here
	 */
	public function run ()
	{
		if ($this->getParam('force', false, false)){
			$this->forceReset();
			exit();
		}

		$tables = Array(
			'answervolunteered',
			'productreview',
			'productrange',
			'productfile',
			'client',
			'clientnewsletter',
			'coupons',
			'integrationwhitelist',
			'invoice',
			'log',
			'missingcart',
			'mostsearch',
			'order',
			'sessionhandler',
			'actionphoto',
			'actionproduct',
			'allegroorder',
			'allegrofavouritecategory',
			'allegrorelatedcategories',
			'allegrocategories',
			'allegrooptionstemplate',
			'auction',
			'shipments'
		);

		foreach ($tables as $table){
			$sql = 'DELETE FROM `' . $table . '`';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->execute();
			$this->log('Truncate table ' . $table . ': Done.');
		}

		$sql = 'UPDATE mailer SET
					mailer = :mailer,
					fromname = :fromname,
					fromemail = :fromemail,
					server = :server,
					port = :port,
					smtpsecure = :smtpsecure,
					smtpauth = :smtpauth,
					smtpusername = :smtpusername,
					smtppassword = :smtppassword
				WHERE idmailer > 0
		';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('mailer', 'mail');
		$stmt->bindValue('fromname', 'Przykładowy sklep');
		$stmt->bindValue('fromemail', 'przyklad@wellcommerce.pl');
		$stmt->bindValue('server', '');
		$stmt->bindValue('port', 25);
		$stmt->bindValue('smtpsecure', '');
		$stmt->bindValue('smtpauth', '');
		$stmt->bindValue('smtpusername', '');
		$stmt->bindValue('smtppassword', '');
		try{
			$stmt->execute();
			$this->log('Reset mailer settings: Done.');
		}
		catch (\Exception $e){
			throw new \Exception($e->getMessage());
		}

		$sql = 'UPDATE view SET namespace = :namespace, minimumordervalue = 0
				WHERE idview > 0
		';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('namespace', 'gekosale');
		try{
			$stmt->execute();
			$this->log('Reset view settings: Done.');
		}
		catch (\Exception $e){
			throw new \Exception($e->getMessage());
		}

		$sql = 'UPDATE productattributeset SET availablityid = NULL, photoid = NULL';
		$stmt = Db::getInstance()->prepare($sql);
		try{
			$stmt->execute();
			$this->log('Reset attribute settings: Done.');
		}
		catch (\Exception $e){
			throw new \Exception($e->getMessage());
		}

		$sql = 'DELETE FROM view WHERE idview > 3';
		$stmt = Db::getInstance()->prepare($sql);
		try{
			$stmt->execute();
			$this->log('Delete other views: Done.');
		}
		catch (\Exception $e){
			throw new \Exception($e->getMessage());
		}

		$modules = Array(
			'wizard',
			'infakt',
			'transferuj',
			'kurjerzy',
			'wfirma',
			'action',
			'smsapi',
			'subiektgt',
			'sendingo',
			'lookmash',
			'kodyrabatowe',
			'ceneo',
			'inpost',
			'hotprice',
			'allegro',
			'furgonetka',
			'dpd',
			'elektronicznynadawca',
			'shopgate',
			'platformaratalna',
		);

		foreach ($modules as $module){
			$this->cleanModuleSettings($module);
		}
	}

	protected function cleanModuleSettings ($module)
	{
		$sql = 'DELETE FROM modulesettings WHERE module = :module';
		$stmt = Db::getInstance()->prepare($sql);
		$stmt->bindValue('module', $module);
		try{
			$stmt->execute();
			$this->log('Reset ' . $module . ' settings: Done.');
		}
		catch (\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}

	protected function runlink ($dir)
	{
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::CHILD_FIRST);
		foreach ($files as $file){
			if (in_array($file->getBasename(), array(
				'.',
				'..'
			))){
				continue;
			}

			if ($file->isDir()){
				if (! rmdir($file->getPathname())){
					throw new Exception('Can\'t remove: ' . $file->getPathname());
				}
				continue;
			}

			if (! unlink($file->getPathname())){
				throw new Exception('Can\'t remove: ' . $file->getPathname());
			}
		}
	}

	public function forceReset ()
	{
		$this->log('Force reset');

		$dirs = array(
			'serialization',
			'cache'
		);

		foreach ($dirs as $dir){
			$dir = ROOTPATH . $dir;
			$this->log('Removing ' . $dir);

			$this->runlink($dir);
			if (! is_dir($dir)){
				mkdir($dir);
			}
		}

		$config = App::getConfig();
		$config = array_map('escapeshellarg', $config['database']);

		$this->log('Importing database');
		$cmd = vsprintf('mysql -h %s -u %s %s %s < %s', array(
			$config['host'],
			$config['user'],
			$config['password'] != '""' ? '-p' . $config['password'] : '',
			$config['dbname'],
			escapeshellarg(ROOTPATH . 'sql' . DS . 'install.sql')
		));
		passthru($cmd);

		$url = $this->getParam('url', false, false);
		if ($url){
			$sql = "UPDATE viewurl SET url = :url";

			$stmt = Db::getInstance()->prepare($sql);
			$stmt->bindValue('url', $url);
			try{
				$stmt->execute();
			}
			catch (Exception $e){
				$this->log('Error: ' . $e->getMessage());
			}
		}

		$this->log('Running migrate');
		shell_exec('php tool.php migrate');
		$this->log('Done');
	}
}