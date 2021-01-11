<?php namespace Atomino\Entity\Attributes;

use Atomino\Molecule\Attr;
use Attribute;


#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class RequiredField extends Attr{
	public function __construct(
		public string $field,
		public string $type
	){}
}
