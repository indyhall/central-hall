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

		// key => required
		$validated = $this->_fields(array(
			'username' => true,
			'password' => true,
			'mac_address' => true,
			'device_name' => false
		));

		$result = false;
		if ($validated instanceof \WP_Error) {
			$result = array(
				'ok' => false,
				'message' => $validated->get_error_message(),
				'data' => $validated->get_error_data()
			);
		}

		if (!$result) {
			extract($validated);

			$user = \wp_authenticate($username, $password);
			if ($user instanceof \WP_User) {
				// Load list of existing MAC addresses
				$macMetaKey = $plugin->prefixKey('mac_addresses');
				$currentMacAddresses = array();
				if (isset($user->$macMetaKey)) {
					$currentMacAddresses = $user->$macMetaKey;
				}

				// Add this device to the list
				$currentMacAddresses[$mac_address] = $device_name;

				// Save user
				\update_user_meta($user->ID, $macMetaKey, $currentMacAddresses);

				if ($plugin->logConnection($mac_address, 'connected')) {
					// Set result
					$result = array(
						'ok' => true,
						'user' => array(
							'id' => $user->ID,
							'display_name' => $user->display_name,
							'roles' => $user->roles,
							'mac_addresses' => $currentMacAddresses
						)
					);
				} else {
					$result = array(
						'ok' => false,
						'message' => 'Unable to log you in due to an internal error.  Please find a staff member and
							provide them with this: <strong>' . $mac_address . '</strong>'
					);
				}
			} else {
				// Unable to log in
				$result = array(
					'ok' => false,
					'message' => $plugin->translate('Unable to log in with the username and password you provided.')
				);
			}
		}

		$result = $plugin->filter('login_result', $result, $validated);
		$this->_respondAndExit($result);
	}

	public function guest()
	{
		$plugin = $this->_plugin;
		$guestPassword = $plugin->getOption('guest_password', $plugin::DEFAULT_GUEST_PASSWORD);

		$validated = $this->_fields(array(
			'name' => true,
			'password' => true,
			'mac_address' => true,
			'host' => false
		));

		if ($validated instanceof \WP_Error) {
			$result = array(
				'ok' => false,
				'message' => $validated->get_error_message(),
				'data' => $validated->get_error_data()
			);
		} else {
			extract($validated);

			if ($password == $guestPassword) {
				if ($plugin->logGuest($mac_address, $name, $host) && $plugin->logConnection($mac_address, 'connected')) {
					$result = array(
						'ok' => true,
						'guest' => $validated
					);
				} else {
					$result = array(
						'ok' => false,
						'message' => 'Unable to log you in due to an internal error.  Please find a staff member and
							provide them with this: <strong>' . $mac_address . '</strong>'
					);
				}
			} else {
				$result = array(
					'ok' => false,
					'message' => 'Invalid guest password.'
				);
			}
		}

		// Filter & send result to client
		$result = $plugin->filter('guest_login_result', $result, $validated);
		$this->_respondAndExit($result);
	}

	protected function _fields($schema)
	{
		$validated = array();

		foreach ($schema as $key => $required) {
			// Determine label
			$label = str_replace('_', ' ', $key);

			// Get value
			$val = null;
			if (isset($_REQUEST[$key])) {
				$val = $_REQUEST[$key];
			}

			// Validate
			if ($required && empty($val)) {
				return new \WP_Error('missing', ucfirst($label) . ' is required.', $key);
			}

			// Special validations
			switch ($key) {
				case 'mac_address':
					if ('$CLIENT_MAC$' == $val) {
						$val = 'DEBUG0000000';
					} else {
						$val = strtoupper(str_replace(':', '', $val));
						if (12 !== strlen($val) || !ctype_xdigit($val)) {
							return new \WP_Error('invalid', 'That is not a valid MAC address.', $key);
						}
					}
					break;
			}

			$validated[$key] = $val;
		}

		return $validated;
	}

	protected function _respondAndExit($data = array())
	{
		header('Content-Type: application/javascript; charset=utf-8');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
		
		// $data = array_merge(array('ok' => $ok), $data);

		$callback = (isset($_REQUEST['callback']) ? $_REQUEST['callback'] : null);
		if (null == $callback || !preg_match('/^[$a-z_][a-z0-9$_]*(\.[$a-z_][a-z0-9$_]*)*$/i', $callback)) {
			echo '(function() { console && console.log && console.log("Invalid callback:", ' . json_encode($callback) . '); }());';
			exit;
		}
		
		echo "$callback && $callback(";
		echo json_encode($data);
		echo ');';
		exit;
	}
}