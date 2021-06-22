<?php namespace Atomino\Core\Config;

use Atomino\Core\Config\Loader\Plugin\AbstractPlugin;
use Atomino\Core\Config\Loader\Plugin\PluginHandler;
use Atomino\Neutrons\DotNotation;

class Builder {
	public function __construct(private array $values, AbstractPlugin ...$plugins) {
		if (count($plugins)) $this->values = (new PluginHandler($plugins))->convert($values);
	}
	public function __invoke(){return $this->values;}
}