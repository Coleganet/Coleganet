<?php
namespace GeditLab\Module;
/**
 * @file mod/cloud.php
 * @brief Initialize Coleganet's cloud (SabreDAV).
 *
 * Module for accessing the DAV storage area.
 */

use Sabre\DAV as SDAV;
use \GeditLab\Storage;

// composer autoloader for SabreDAV
require_once('vendor/autoload.php');

require_once('include/attach.php');


/**
 * @brief Fires up the SabreDAV server.
 *
 * @param App &$a
 */


class Cloud extends \GeditLab\Web\Controller {

	function init() {
	
		if (! is_dir('store'))
			os_mkdir('store', STORAGE_DEFAULT_PERMISSIONS, false);
	
		$which = null;
		if (argc() > 1)
			$which = argv(1);
	
		$profile = 0;
	
		\App::$page['htmlhead'] .= '<link rel="alternate" type="application/atom+xml" href="' . z_root() . '/feed/' . $which . '" />' . "\r\n";
	
		if ($which)
			profile_load( $which, $profile);
	
		$auth = new \GeditLab\Storage\BasicAuth();
	
		$ob_hash = get_observer_hash();
	
		if ($ob_hash) {
			if (local_channel()) {
				$channel = \App::get_channel();
				$auth->setCurrentUser($channel['channel_address']);
				$auth->channel_id = $channel['channel_id'];
				$auth->channel_hash = $channel['channel_hash'];
				$auth->channel_account_id = $channel['channel_account_id'];
				if($channel['channel_timezone'])
					$auth->setTimezone($channel['channel_timezone']);
			}
			$auth->observer = $ob_hash;
		}
	
		if ($_GET['davguest'])
			$_SESSION['davguest'] = true;
	
		$_SERVER['QUERY_STRING'] = str_replace(array('?f=', '&f='), array('', ''), $_SERVER['QUERY_STRING']);
		$_SERVER['QUERY_STRING'] = strip_zids($_SERVER['QUERY_STRING']);
		$_SERVER['QUERY_STRING'] = preg_replace('/[\?&]davguest=(.*?)([\?&]|$)/ism', '', $_SERVER['QUERY_STRING']);
	
		$_SERVER['REQUEST_URI'] = str_replace(array('?f=', '&f='), array('', ''), $_SERVER['REQUEST_URI']);
		$_SERVER['REQUEST_URI'] = strip_zids($_SERVER['REQUEST_URI']);
		$_SERVER['REQUEST_URI'] = preg_replace('/[\?&]davguest=(.*?)([\?&]|$)/ism', '', $_SERVER['REQUEST_URI']);
	
		$rootDirectory = new \GeditLab\Storage\Directory('/', $auth);
	
		// A SabreDAV server-object
		$server = new SDAV\Server($rootDirectory);
		// prevent overwriting changes each other with a lock backend
		$lockBackend = new SDAV\Locks\Backend\File('store/[data]/locks');
		$lockPlugin = new SDAV\Locks\Plugin($lockBackend);
	
		$server->addPlugin($lockPlugin);
	
		$is_readable = false;
	
		// provide a directory view for the cloud in Coleganet
		$browser = new \GeditLab\Storage\Browser($auth);
		$auth->setBrowserPlugin($browser);
	
		$server->addPlugin($browser);
	
		// Experimental QuotaPlugin
	//	require_once('\GeditLab\Storage/QuotaPlugin.php');
	//	$server->addPlugin(new \GeditLab\Storage\\QuotaPlugin($auth));
	
		ob_start();
		// All we need to do now, is to fire up the server
		$server->exec();

		ob_end_flush();

		killme();
	}
	
}
