<?php namespace Atomino\Entity\Plugin;

use Atomino\Entity\Generator\CodeWriter;
use Atomino\Entity\Model;
use Atomino\Neutrons\Attr;

class Plugin extends Attr{
	public static function fetch(Model $model): static{
		$plugin = $model->getPlugin(static::class);
		$plugin->init($model);
		return $plugin;
	}
	protected function init(Model $model){}
	public function generate(\ReflectionClass $ENTITY, CodeWriter $codeWriter){}
	public function getTrait(): string|null{return null;}
}