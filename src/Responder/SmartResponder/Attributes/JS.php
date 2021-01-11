<?php namespace Atomino\Responder\SmartResponder\Attributes;

use Attribute;


#[Attribute( Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE )]
class JS{
	private array $scripts;
	public function __construct(string ...$scripts){ $this->scripts = $scripts; }
	public function set(&$args){ foreach ($this->scripts as $script) $args['js'][$script] = $script; }
}