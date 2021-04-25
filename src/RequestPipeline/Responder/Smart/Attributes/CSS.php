<?php namespace Atomino\RequestPipeline\Responder\Smart\Attributes;

use Atomino\Neutrons\Attr;
use Attribute;


#[Attribute( Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE )]
class CSS extends Attr{
	private array $stylesheets;
	public function __construct(string ...$stylesheets){ $this->stylesheets = $stylesheets; }
	public function set(&$args){ foreach ($this->stylesheets as $stylesheet) $args['css'][$stylesheet] = $stylesheet; }
}