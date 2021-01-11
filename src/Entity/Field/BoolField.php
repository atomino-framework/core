<?php namespace Atomino\Entity\Field;

use Atomino\Entity\Field\Attributes\FieldDescriptor;
use Symfony\Component\Validator\Constraints\Type;

#[FieldDescriptor('bool', null)]
class BoolField extends Field{
	public function build(mixed $value){ return is_null($value) ? null : (bool)$value; }
	public function store(mixed $value){ return is_null($value) ? null : (int)$value; }
	public function import(mixed $value){ return is_null($value) ? null : (bool)$value; }

}