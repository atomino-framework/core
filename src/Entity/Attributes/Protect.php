<?php namespace Atomino\Entity\Attributes;

use Atomino\Molecule\Attr;
use Attribute;


#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class Protect extends Attr{
	public function __construct(
		public string $field,
		public bool $get = true,
		public bool $set = false
	){}
}
