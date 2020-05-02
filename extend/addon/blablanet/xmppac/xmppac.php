<?php

/**
 * Name: xmppac Account Free 
 * Description: xmppac Accounts Free 
 * Version: 1.0
 * Author: Jacob M.
 */


function xmppac_load() {
    register_hook('app_menu', 'addon/xmppac/xmppac.php', 'xmppac_app_menu');
}

function xmppac_unload() {
    unregister_hook('app_menu', 'addon/xmppac/xmppac.php', 'xmppac_app_menu');

}

function xmppac_app_menu($a,&$b) {
    $b['app_menu'][] = '<div class="app-title"><a href="xmppac">Latest xmppac</a></div>';
}


function xmppac_module() {}


function xmppac_content($a) {

$baseurl = z_root() . '/addon/xmppac';

$o .= <<< EOT

<br><br>

<p align="left">

<object width="975" height="1500" data="https://coleganet.com/registration/"></object>

<br><br>

<b>Create a Free XMPP account in Coleganet.eu</b><br>

</p>



</p>

EOT;


call_hooks('xmppac_plugin',$o);
    return $o;

}
