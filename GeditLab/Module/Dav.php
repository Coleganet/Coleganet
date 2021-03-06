<?php

namespace GeditLab\Module;
/**
 * @file mod/dav.php
 * @brief Initialize Coleganet's cloud (SabreDAV).
 *
 * Module for accessing the DAV storage area from a DAV client.
 */

use \Sabre\DAV as SDAV;
use \GeditLab\Storage;

// composer autoloader for SabreDAV
require_once('vendor/autoload.php');

require_once('include/attach.php');

/**
 * @brief Fires up the SabreDAV server.
 *
 * @param App &$a
 */

class Dav extends \GeditLab\Web\Controller {

	function init() {
	
		// workaround for HTTP-auth in CGI mode
		if (x($_SERVER, 'REDIRECT_REMOTE_USER')) {
 			$userpass = base64_decode(substr($_SERVER["REDIRECT_REMOTE_USER"], 6)) ;
			if(strlen($userpass)) {
			 	list($name, $password) = explode(':', $userpass);
				$_SERVER['PHP_AUTH_USER'] = $name;
				$_SERVER['PHP_AUTH_PW'] = $password;
			}
		}

		if (x($_SERVER, 'HTTP_AUTHORIZATION')) {
			$userpass = base64_decode(substr($_SERVER["HTTP_AUTHORIZATION"], 6)) ;
			if(strlen($userpass)) {
				list($name, $password) = explode(':', $userpass);
				$_SERVER['PHP_AUTH_USER'] = $name;
				$_SERVER['PHP_AUTH_PW'] = $password;
			}
		}

		if (! is_dir('store'))
			os_mkdir('store', STORAGE_DEFAULT_PERMISSIONS, false);
	
		if (argc() > 1)
			profile_load(argv(1),0);
	

		$auth = new \GeditLab\Storage\BasicAuth();
		$auth->setRealm(ucfirst(\GeditLab\Lib\System::get_platform_name()) . ' ' . 'WebDAV');

		$rootDirectory = new \GeditLab\Storage\Directory('/', $auth);
	
		// A SabreDAV server-object
		$server = new SDAV\Server($rootDirectory);


		$authPlugin = new \Sabre\DAV\Auth\Plugin($auth);
		$server->addPlugin($authPlugin);


		// prevent overwriting changes each other with a lock backend
		$lockBackend = new SDAV\Locks\Backend\File('store/[data]/locks');
		$lockPlugin = new SDAV\Locks\Plugin($lockBackend);
	
		$server->addPlugin($lockPlugin);
	
		// provide a directory view for the cloud in Coleganet
		$browser = new \GeditLab\Storage\Browser($auth);
		$auth->setBrowserPlugin($browser);
	
		// Experimental QuotaPlugin
		// require_once('GeditLab/Storage/QuotaPlugin.php');
		// $server->addPlugin(new \GeditLab\Storage\QuotaPlugin($auth));
	
		// All we need to do now, is to fire up the server
		$server->exec();
	
		killme();
	}
	
}
