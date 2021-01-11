<?php namespace Atomino\Entity\Field;

use Atomino\Entity\Field\Attributes\FieldDescriptor;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;

#[FieldDescriptor('string', null, true)]
class EnumField extends Field{

	public function build(mixed $value){ return is_null($value) ? null : strval($value); }
	public function import(mixed $value){ return is_null($value) ? null : strval($value); }

	/** @param \Atomino\Database\Descriptor\Field\EnumField $field */
	static function getValidators(\Atomino\Database\Descriptor\Field\Field $field):array{
		$validators = parent::getValidators($field);
		$validators[] = [Choice::class, ['multiple' => false, 'choices' => $field->getOptions()]];
		return $validators;
	}
}