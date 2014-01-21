<?php

namespace Gekosale;

class Profiler
{
	private static $instance = NULL;
	private static $queries;
	private static $query_times;
	private static $start;
	private static $stop;

	public static function start ()
	{
		self::$start = microtime(true);
	}

	public static function stop ()
	{
		self::$stop = microtime(true) - self::$start;
	}

	public static function getInfo ()
	{
		$table = '';
		$totaltime = 0;
		foreach (self::$queries as $query){
			$totaltime += $query['time'];
			$table .= "<pre><h4>{$query['time']}</h4><br />{$query['query']}</pre>";
		}
		$total = count(self::$queries);
		$init = self::$stop;
		$header = "
		<pre>
		<h5>Total DB time: {$totaltime}</h5>
		<h5>Total queries: {$total}</h5>
		<h5>Total init: {$init}</h5>
		</pre>";
		return $header . $table;
	}

	public static function addQuery ($query, $time)
	{
		self::$queries[] = Array(
			'query' => $query,
			'time' => $time
		);
	}
}