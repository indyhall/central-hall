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

		$ajaxBase = admin_url('admin-ajax.php');
		$loginUrl = $plugin->prefixKey('login');
		$guestUrl = $plugin->prefixKey('guest');
		$guestPassword = $plugin->getOption('guest_password', $plugin::DEFAULT_GUEST_PASSWORD);

		?>

		<div class="wrap">

			<?php if (isset($_REQUEST['settings-updated']) && 'true' == $_REQUEST['settings-updated']): ?>
				<div class="updated"><?php \_e('Settings saved.'); ?></div>
			<?php endif; ?>

			<h2><?php echo $plugin->translate('Central Hall'); ?></h2>

			<p>The current guest password is <strong><?=htmlspecialchars($guestPassword)?></strong></p>

			<h3>API Endpoints</h3>

			<p>The API is meant to work with the companion portal app, but if you'd rather build your
				own or are debugging a modified version, check out:</p>

			<ul class="ul-disc">
				<li>Member login: <a href="<?=$ajaxBase?>?action=<?=$loginUrl?>"><?=$ajaxBase?>?action=<?=$loginUrl?></a></li>
				<li>Guest login: <a href="<?=$ajaxBase?>?action=<?=$guestUrl?>"><?=$ajaxBase?>?action=<?=$guestUrl?></a></li>
			</ul>

			<h3>Constants</h3>

			<p>These constants override any options set in the settings panel.  That is, once there is a settings panel.
				Right now, this is how you configure the plugin.  Either set them in your <code>wp-config.php</code> file, or
				using a separate plugin.</p>

			<p><code><?=strtoupper($plugin->prefixKey('guest_password'))?></code> — default guest password.</p>

			<h3>Actions &amp; Filters</h3>

			<p>Sorry in advanced for excessively long action and filter names.  We try to namespace everything
				as much as possible.</p>

			<h4>Filters</h4>

			<p><code><?=$plugin->prefixKey('login_result')?></code> — filter the results of a login action.</p>
			<p><code><?=$plugin->prefixKey('guest_login_result')?></code> — filter the results of a guest login action—particularly
				useful if you need a more advanced guest password system.</p>

			<hr />

			<p><small>Make this plugin easier to use—<em>a settings GUI would be fantastic</em>—by
				<a href="https://github.com/indyhall/central-hall" target="_blank">contributing
				on Github</a>.</small></p>

			<? /*
			<form method="POST" action="options.php">
				<?php \settings_fields($plugin->prefixKey('settings')); ?>
				<?php \do_settings_sections($plugin->prefixKey('settings')); ?>
				<?php \submit_button(); ?>
			</form>
            */ ?>
		</div>

	<?php
	}
}