<?php namespace Atomino\Entity\Attributes;

use Atomino\Molecule\Attr;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute( Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE )]
class Validator extends Attr{
	public Constraint $validator;
	public function __construct(public string $field, string $validatorClass, ...$arguments){
		$this->validator = new $validatorClass(...$arguments);
	}
}
