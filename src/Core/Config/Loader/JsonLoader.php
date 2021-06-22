<?php namespace Atomino\Core\Config\Loader;

use Atomino\Core\Config\ProviderInterface;
use Atomino\Neutrons\DotNotation;

class JsonLoader extends AbstractLoader {
	public function getExtension(): string { return 'json'; }
	public function load($file): array {
		return json_decode(file_get_contents($file), true);
	}
}