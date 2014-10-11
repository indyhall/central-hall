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
		$plugin = $this->_plugin;

		$username = $_REQUEST['username'];
		$password = $_REQUEST['password'];
		$macAddress = $_REQUEST['mac_address'];
		$deviceName = $_REQUEST['device_name'];

		$user = \wp_authenticate($username, $password);
		if ($user instanceof \WP_User) {
			// Load list of existing MAC addresses
			$macMetaKey = $plugin->prefixKey('mac_addresses');
			$currentMacAddresses = array();
			if (isset($user->$macMetaKey)) {
				$currentMacAddresses = $user->$macMetaKey;
			}

			// Add this device to the list
			$currentMacAddresses[$macAddress] = $deviceName;

			// Save user
			\wp_update_user($user);

			// Set result
			$result = array(
				'ok' => true,
				'user' => array(
					'id' => $user->ID,
					'display_name' => $user->display_name,
					'mac_addresses' => $currentMacAddresses
				);
			);
		} else {
			// Unable to log in
			$result = array(
				'ok' => false,
				'message' => $plugin->translate('Unable to log in with the username and password you provided.')
			);
		}

		\wp_send_json($result);
	}

	public function guest()
	{
		// FIXME
		$result = array(
			'ok' => false,
			'message' => $plugin->translate('Not implemented.')
		);
		\wp_send_json($result);
	}
}