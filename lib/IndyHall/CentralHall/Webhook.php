<?php

namespace IndyHall\CentralHall;

class Webhook
{
	protected $_plugin;

	public function __construct(Plugin $plugin)
	{
		$this->_plugin = $plugin;

		// Login Requests
		\add_action('wp_ajax_' . $plugin->prefixKey('login'), array($this, 'login'));
		\add_action('wp_ajax_nopriv_' . $plugin->prefixKey('login'), array($this, 'login'));

		// Guest Login
		\add_action('wp_ajax_' . $plugin->prefixKey('guest'), array($this, 'guest'));
		\add_action('wp_ajax_nopriv_' . $plugin->prefixKey('guest'), array($this, 'guest'));
	}

	public function login()
	{
		// FIXME
		$result = $_REQUEST;
		\wp_send_json($result);
	}

	public function guest()
	{
		// FIXME
		$result = $_REQUEST;
		\wp_send_json($result);
	}
}