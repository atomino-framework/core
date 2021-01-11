<?php namespace Atomino\Entity\Field;

use Atomino\Entity\Field\Attributes\FieldDescriptor;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

#[FieldDescriptor('int', null)]
class IntField extends Field{
	public function build(mixed $value){ return is_null($value) ? null : intval($value); }
	public function import(mixed $value){ return is_null($value) ? null : intval($value); }
	/** @param \Atomino\Database\Descriptor\Field\NumericField $field */
	static function getValidators($field):array{
		$validators = parent::getValidators( $field);
		if(!$field->isSigned())	$validators[] = [PositiveOrZero::class];
		return $validators;
	}
}