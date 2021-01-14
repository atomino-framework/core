<?php namespace Atomino\Entity\Field;

use Atomino\Entity\Field\Attributes\FieldDescriptor;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

#[FieldDescriptor('int', null)]
class IntField extends Field{
	#[Pure] public function build(mixed $value){ return is_null($value) ? null : intval($value); }
	#[Pure] public function import(mixed $value){ return is_null($value) ? null : intval($value); }
	/** @param \Atomino\Database\Descriptor\Field\NumericField $field */
	static function getValidators($field):array{
		$validators = parent::getValidators( $field);
		if(!$field->isSigned())	$validators[] = [PositiveOrZero::class];
		return $validators;
	}
}