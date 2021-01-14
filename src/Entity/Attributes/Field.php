<?php namespace Atomino\Entity\Attributes;

use Atomino\Neutrons\Attr;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class Field extends Attr{
	public function __construct(
		public string $field,
		public string $fieldClass,
		... $arguments
	){}
}
