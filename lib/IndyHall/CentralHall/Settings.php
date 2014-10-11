<?php

namespace IndyHall\CentralHall;

class Settings
{
	protected $_plugin;

	/**
	 * Constructor
	 *
	 * @param Plugin $plugin
	 */
	public function __construct(Plugin $plugin)
	{
		$this->_plugin = $plugin;

		\add_action('admin_init', array($this, 'registerSettings'));
		\add_action('admin_menu', array($this, 'registerOptionsPage'));
		\add_filter('plugin_action_links_' . $plugin->basename(), array($this, 'addSettingsLink'));
	}

	public function registerOptionsPage()
	{
		$plugin = $this->_plugin;
		$settingsTitle = $plugin->translate('Central Hall');
		$menuTitle = $plugin->translate('Central Hall');
		$slug = $plugin->prefixKey('settings');
		\add_options_page($settingsTitle, $menuTitle, 'manage_options', $slug, array($this, 'settingsPage'));
	}

	public function registerSettings()
	{
		$plugin = $this->_plugin;

		// FIXME
	}

	public function addSettingsLink($links)
	{
		$plugin = $this->_plugin;
		$page = $plugin->prefixKey('settings');
		$title = $plugin->translate('Settings');
		$links[] = sprintf('<a href="options-general.php?page=%s">%s</a>', $page, $title);
		return $links;
	}

	public function settingsPage()
	{
		$plugin = $this->_plugin;
		?>

		<div class="wrap">

			<?php if ('true' == $_REQUEST['settings-updated']): ?>
				<div class="updated"><?php \_e('Settings saved.'); ?></div>
			<?php endif; ?>

			<h2><?php echo $plugin->translate('Central Hall'); ?></h2>

			<form method="POST" action="options.php">
				<?php \settings_fields($plugin->prefixKey('settings')); ?>
				<?php \do_settings_sections($plugin->prefixKey('settings')); ?>
				<?php \submit_button(); ?>
			</form>
		</div>

	<?php
	}
}