<?php


namespace Atomino\Core\Config\Loader\Plugin;


use Atomino\Neutrons\DotNotation;

class PluginHandler {

	private const PLUGIN_MARK = '@';

	/** @var AbstractPlugin[] */
	private array $plugins;

	public function __construct(array $plugins) {
		$pluginSet = [];
		foreach ($plugins as $plugin) $this->plugins[self::PLUGIN_MARK . $plugin->getCode()] = $plugin;
	}

	public function convert(array $array): array {
		$flattened = DotNotation::flatten($array);
		foreach ($flattened as $key => $value) {
			if (preg_match_all('/' . self::PLUGIN_MARK . '[\p{L}\p{M}0-9-_]+/', $key, $matches)) {
				foreach ($matches[0] as $match) $value = $this->plugins[$match]?->process($value);
				$newKey = preg_replace('/' . self::PLUGIN_MARK . '[\p{L}\p{M}0-9-_]+/', '', $key);
				$newKey = preg_replace('~\.+~', '.', $newKey);
				$newKey = trim($newKey, '.');
				unset($flattened[$key]);
				$flattened[$newKey] = $value;
			}
		}
		return DotNotation::extract($flattened);
	}
}