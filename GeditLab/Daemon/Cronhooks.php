<?php /** @file */

namespace GeditLab\Daemon;

class Cronhooks {

	static public function run($argc,$argv){

		logger('cronhooks: start');
	
		$d = datetime_convert();

		call_hooks('cron', $d);

		return;
	}
}
