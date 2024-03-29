<?php
namespace GeditLab\Module;


class Siteinfo extends \GeditLab\Web\Controller {

	function init() {
		if (argv(1) ===  'json') {
			$data = get_site_info();
			json_return_and_die($data);
		}
	}
	
	
	
	function get() {
	
		if(! get_config('system','hidden_version_siteinfo')) {
			$version = sprintf( t('Version %s'), \GeditLab\Lib\System::get_project_version());
			if(@is_dir('.git') && function_exists('shell_exec')) {
				$commit = @shell_exec('git log -1 --format="%h"');
				$tag = \GeditLab\Lib\System::get_std_version(); // @shell_exec('git describe --tags --abbrev=0');
			}
			if(! isset($commit) || strlen($commit) > 16)
				$commit = '';
		}
		else {
		        $version = $commit = '';
		}

		$plugins_list = implode(', ',visible_plugin_list());

		if($plugins_list)
	        $plugins_text = t('Installed plugins/addons/apps:');
		else
			$plugins_text = t('No installed plugins/addons/apps');
	
		$txt = get_config('system','admininfo');
		$admininfo = bbcode($txt);
	
		if(file_exists('doc/site_donate.html'))
			$donate .= file_get_contents('doc/site_donate.html');
	
		if(function_exists('sys_getloadavg'))
			$loadavg = sys_getloadavg();
	
		$o = replace_macros(get_markup_template('siteinfo.tpl'), array(
			'$title' => t('$Projectname'),
			'$description' => t('This is a hub of $Projectname - a global cooperative network of decentralized privacy enhanced websites.'),
			'$version' => $version,
			'$tag_txt' => t('Tag: '),
			'$tag' => $tag,
			'$polled' => t('Last background fetch: '),
			'$lastpoll' => get_poller_runtime(),
                        // remove load average that is a information not need to be release in Public.
			// '$load_average' => t('Current load average: '),
			// '$loadavg_all' => $loadavg[0] . ', ' . $loadavg[1] . ', ' . $loadavg[2],		
			'$commit' => $commit,
			'$web_location' => t('Running at web location') . ' ' . z_root(),
			'$visit' => t('Please visit <a href="https://coleganet.org">Coleganet.org</a> to learn more about $Projectname.'),
			'$bug_text' => t('Bug reports and issues: please visit'),
			'$bug_link_url' => 'https://geditlab.com/coleganet/coleganet-addons/issues',
			'$bug_link_text' => t('$projectname issues'),
			'$contact' => t('Suggestions, praise, etc. - please email "coleganet" at coleganet - dot com'),
			'$donate' => $donate,
			'$adminlabel' => t('Site Administrators'),
			'$admininfo' => $admininfo,
			'$plugins_text' => $plugins_text,
			'$plugins_list' => $plugins_list
	        ));
	
		call_hooks('about_hook', $o); 	
	
		return $o;
	
	}
	
}
