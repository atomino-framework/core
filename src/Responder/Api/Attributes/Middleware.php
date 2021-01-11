<?php namespace Atomino\Responder\Api\Attributes;

use Attribute;


#[Attribute( Attribute::TARGET_METHOD|Attribute::IS_REPEATABLE)]
class Middleware{
	public function __construct(public string $class, public array $args = []){}
}