<?php
/*
Plugin Name: Central Hall
Plugin URI: http://www.indyhall.org/
Description: Central Hall is a Wordpress plugin and PFSense "captive portal" solution to manage access to a network via Wordpress login.
Version: 1.1.2
Author: Chris Morrell
Author URI: http://cmorrell.com
License: GPL2
GitHub Plugin URI: indyhall/central-hall
*/

// See https://github.com/afragen/github-updater for automatic updates

require_once __DIR__ . '/vendor/autoload.php';
new \IndyHall\CentralHall\Plugin(__FILE__);