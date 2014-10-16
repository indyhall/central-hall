<?php

namespace IndyHall\CentralHall;

/**
 * Main Plugin Class
 *
 * Handles:
 *   - Init
 *   - Sub-component management/dependency injection
 *   - Activation/deactivation
 *   - Naming consistency
 *   - Helper functionality
 *
 * @package IndyHall\CentralHall
 */
class Plugin
{
	const SLUG = 'indyhall-central-hall';
	const PREFIX = 'indyhall_central_hall_';
	const DB_SCHEMA_VERSION = 1;
	const DEFAULT_GUEST_PASSWORD = 'cowork with me';

	/**
	 * @var String The location of the plugin's bootstrap file
	 */
	protected $_bootstrapFile;

	/**
	 * @var string Root directory of plugin
	 */
	protected $_pluginDir;

	/**
	 * @var string Plugin base name
	 */
	protected $_baseName;

	/**
	 * Constructor
	 *
	 * @param String $bootstrap The filename of the plugin's bootstrap file
	 */
	public function __construct($bootstrap)
	{
		// Setup plugin
		$this->_bootstrapFile = $bootstrap;
		$this->_pluginDir = dirname($bootstrap);
		$this->_baseName = \plugin_basename($bootstrap);

		// Register hooks
		\register_activation_hook($bootstrap, array($this, 'activate'));
		\register_deactivation_hook($bootstrap, array($this, 'deactivate'));
		\add_action('plugins_loaded', array($this, 'checkDatabaseSchema'));

		$this->doAction('pre_init');

		// Load components
		new Settings($this);
		new Webhook($this);

		$this->doAction('post_init');
	}

	/**
	 * Get the plugin base name
	 *
	 * @return string
	 */
	public function basename()
	{
		return $this->_baseName;
	}

	/**
	 * Run through Wordpress' l10n system, but with namespace applied
	 *
	 * @param String $text Text to translate
	 * @return string Translated text
	 */
	public function translate($text)
	{
		return \translate($text, self::SLUG);
	}

	/**
	 * Get the path to a file relative to the plugin root
	 *
	 * @param String $filename Relative path to file
	 * @return string Resolved path to file
	 */
	public function pathToFile($filename) {
		return $this->_pluginDir . DIRECTORY_SEPARATOR . $filename;
	}

	/**
	 * Get the URL to a file relative to the plugin root
	 *
	 * @param String $filename Relative path to file
	 * @return string URL to file
	 */
	public function urlToFile($filename) {
		return \plugins_url($filename, $this->_bootstrapFile);
	}

	/**
	 * Prefix a string for namespaced use
	 *
	 * @param String $key Any string
	 * @return string Namespaced (prefixed with self::PREFIX) string
	 */
	public function prefixKey($key)
	{
		return self::PREFIX . $key;
	}

	/**
	 * Get the name of a table, keeping in mind the WPDB prefix and our namespace
	 *
	 * @param String $name Name of table
	 * @return string Namespaced & prefixed name of table
	 */
	public function getTable($name)
	{
		global $wpdb;
		return $wpdb->prefix . $this->prefixKey($name);
	}

	/**
	 * Get option
	 *
	 * @param String $key Option key
	 * @param bool $default Default to return if option is not set
	 * @return mixed|void
	 */
	public function getOption($key, $default = false)
	{
		$key = $this->prefixKey($key);
		if (defined(strtoupper($key))) {
			return constant(strtoupper($key));
		}
		return \get_option($key, $default);
	}

	/**
	 * Set option
	 *
	 * @param String $key Option key
	 * @param mixed $value Option value
	 * @return bool
	 */
	public function setOption($key, $value)
	{
		return \update_option($this->prefixKey($key), $value);
	}

	/**
	 * Filter using Wordpress' filter API, but namespaced
	 *
	 * @param string $tag Filter tag
	 * @param mixed $value What to filter
	 * @return mixed Filtered version of $value
	 */
	public function filter($tag, $value)
	{
		$args = func_get_args();
		$args[0] = $this->prefixKey($tag);
		return call_user_func_array('\apply_filters', $args);
	}

	/**
	 * Hook into an internal filter
	 *
	 * @param string $tag Filter tag
	 * @param callable $callback Function that handles filter
	 * @param int $priority Priority of filer
	 * @param int $accepted_args Number of arguments accepted
	 * @return bool|void
	 */
	public function addFilter($tag, $callback, $priority = 10, $accepted_args = 1)
	{
		return \add_filter($this->prefixKey($tag), $callback, $priority, $accepted_args);
	}

	/**
	 * Wordpress plugin hook, but namespaced
	 *
	 * @param String $tag Hook name
	 * @param mixed $arg Arguments...
	 * @return mixed
	 */
	public function doAction($tag, $arg = '')
	{
		$args = func_get_args();
		$args[0] = $this->prefixKey($tag);
		return call_user_func_array('\do_action', $args);
	}

	public function logConnection($mac, $event)
	{
		global $wpdb;

		$sql = 'INSERT INTO ' . $this->getTable('connection_log') . ' (`log_date`, `mac_address`, `connection_event`)
				VALUES (NOW(), %s, %s)';
		$query = $wpdb->prepare($sql, $mac, $event);
		$id = $wpdb->query($query);

		return $id;
	}

	public function logGuest($mac, $name, $host)
	{
		global $wpdb;

		$sql = 'INSERT INTO ' . $this->getTable('guest_log') . ' (`log_date`, `guest_name`, `host_name`, `mac_address`)
				VALUES (NOW(), %s, %s, %s)';
		$query = $wpdb->prepare($sql, $name, $host, $mac);
		$id = $wpdb->query($query);

		return $id;
	}

	/**
	 * Activation hook
	 */
	public function activate()
	{
		$this->_setupDb();
	}

	public function checkDatabaseSchema()
	{
		if ($this->getOption('db_schema_version') != self::DB_SCHEMA_VERSION) {
			$this->_setupDb();
		}
	}

	/**
	 * Sets up database tables
	 */
	protected function _setupDb()
	{
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$sql = array();
		$sql[] = 'CREATE TABLE ' . $this->getTable('guest_log') . ' (
					ID int(11) unsigned NOT NULL AUTO_INCREMENT,
					log_date datetime NOT NULL DEFAULT "0000-00-00 00:00:00",
					guest_name varchar(55) NOT NULL DEFAULT "",
					host_name varchar(55) DEFAULT NULL,
					mac_address char(12) NOT NULL DEFAULT "00000000",
					PRIMARY KEY (ID)
				);';
		$sql[] = 'CREATE TABLE ' . $this->getTable('connection_log') . ' (
					ID int(11) unsigned NOT NULL AUTO_INCREMENT,
					log_date datetime NOT NULL DEFAULT "0000-00-00 00:00:00",
					mac_address char(12) NOT NULL DEFAULT "00000000",
					connection_event varchar(15) NOT NULL DEFAULT "connected",
					PRIMARY KEY (ID)
				);';


		foreach($sql as $query) {
			\dbDelta($query);
		}

		$this->setOption('db_schema_version', self::DB_SCHEMA_VERSION);
	}

	/**
	 * Deactivation hook
	 */
	public function deactivate()
	{
		// TODO: Add an option to delete tables on deactivation
	}
}