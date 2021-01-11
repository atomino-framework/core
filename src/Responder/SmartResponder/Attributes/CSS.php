<?php namespace Atomino\Responder\SmartResponder\Attributes;

use Attribute;


#[Attribute( Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE )]
class CSS{
	private array $stylesheets;
	public function __construct(string ...$stylesheets){ $this->stylesheets = $stylesheets; }
	public function set(&$args){ foreach ($this->stylesheets as $stylesheet) $args['css'][$stylesheet] = $stylesheet; }
}