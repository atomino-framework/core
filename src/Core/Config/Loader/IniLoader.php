<?php namespace Atomino\Core\Config\Loader;

use Atomino\Core\Config\ProviderInterface;
use Atomino\Neutrons\DotNotation;

class IniLoader extends AbstractLoader {
	public function getExtension(): string { return 'ini'; }
	public function load($file): array {
		return DotNotation::extract(parse_ini_file($file, false, INI_SCANNER_TYPED));
	}
}