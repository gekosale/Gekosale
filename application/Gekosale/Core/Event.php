<?php

namespace Gekosale\Core;

use sfEvent;
use sfEventDispatcher;

class Event
{
	private static $events = Array();
	private static $dispatcher = NULL;

	public static function register ()
	{
		if (empty(self::$events)){
			self::load();
		}
		if (NULL === self::$dispatcher){
			self::$dispatcher = new sfEventDispatcher();
		}
		foreach (self::$events as $key => $event){
			$model = App::getModel($event['model']); 			
			if ($model == null)
				continue;
			
			self::$dispatcher->connect($event['name'], array(
				$model,
				$event['method']
			));
		}
	}

	protected static function load ()
	{
		if ((self::$events = App::getContainer()->get('cache')->load('events')) === FALSE){
			$sql = 'SELECT * FROM event';
			$stmt = Db::getInstance()->prepare($sql);
			$stmt->execute();
			while ($rs = $stmt->fetch()){
				self::$events[] = Array(
					'name' => $rs['name'],
					'model' => $rs['model'],
					'method' => $rs['method'],
					'mode' => $rs['mode']
				);
			}
			App::getContainer()->get('cache')->save('events', self::$events);
		}
	}

	public static function dispatch ($object, $eventName, $values = Array())
	{
		if (! is_object(self::$dispatcher) || ! (self::$dispatcher instanceof sfEventDispatcher)){
			self::register();
		}
		$event = new sfEvent($object, $eventName, $values);
		self::$dispatcher->filter($event, $values);
		
		$eventData = $event->getReturnValues();
		if (! is_array(@$values['data']))
			$values['data'] = array();
		
		foreach ($eventData as $Data){
			$values['data'] = Arr::merge($values['data'], $Data);
		}
		
		return $values['data'];
	}

	public static function notify ($object, $eventName, $values = Array(), $param = NULL)
	{
		if (! is_object(self::$dispatcher) || ! (self::$dispatcher instanceof sfEventDispatcher)){
			self::register();
		}
		return self::$dispatcher->notify(new sfEvent($object, $eventName, $values));
	}

	public static function filter ($object, $eventName, $values = Array(), $param = NULL)
	{
		if (! is_object(self::$dispatcher) || ! (self::$dispatcher instanceof sfEventDispatcher)){
			self::register();
		}
		
		$event = new sfEvent($object, $eventName, $values);
		self::$dispatcher->filter($event, $param);
		return $event->getReturnValues();
	}
}