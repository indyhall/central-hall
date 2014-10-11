<?php
/*
Plugin Name: Central Hall
Plugin URI: http://www.indyhall.org/
Description: Central Hall is a Wordpress plugin and PFSense "captive portal" solution to manage access to a network via Wordpress login.
Version: 1.0.0
Author: Chris Morrell
Author URI: http://cmorrell.com
License: GPL2
*/

require_once __DIR__ . '/vendor/autoload.php';
new \IndyHall\CentralHall\Plugin(__FILE__);