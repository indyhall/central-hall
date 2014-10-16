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
		\register_activation_hook($bootstrap, array(self, 'activate'));
		\register_deactivation_hook($bootstrap, array(self, 'deactivate'));

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
	 * Get option
	 *
	 * @param String $key Option key
	 * @param bool $default Default to return if option is not set
	 * @return mixed|void
	 */
	public function getOption($key, $default = false)
	{
		return \get_option($this->prefixKey($key), $default);
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

	/**
	 * Activation hook
	 */
	public static function activate()
	{
		// May want this down the road
	}

	/**
	 * Deactivation hook
	 */
	public static function deactivate()
	{
		// Also may need this
	}
}