<?php namespace Atomino\Entity\Field;

use Atomino\Entity\Field\Attributes\FieldDescriptor;

#[FieldDescriptor('array', [])]
class JsonField extends Field{
	public function build(mixed $value){ return json_decode($value, true); }
	public function store(mixed $value){ return json_encode($value); }
}