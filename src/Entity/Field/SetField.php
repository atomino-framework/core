<?php namespace Atomino\Entity\Field;

use Atomino\Entity\Field\Attributes\FieldDescriptor;
use Symfony\Component\Validator\Constraints\Choice;

#[FieldDescriptor('array', [], true)]
class SetField extends Field{
	public function build(mixed $value){ return is_null($value) ? [] : explode(',', $value); }
	public function store(mixed $value){ return join(',', $value); }
	public function import(mixed $value){ return is_null($value) ? null : intval($value); }

	/** @param \Atomino\Database\Descriptor\Field\EnumField $field */
	static function getValidators(\Atomino\Database\Descriptor\Field\Field $field):array{
		$validators = parent::getValidators($field);
		$validators[] = [Choice::class, ['multiple' => true, 'choices' => $field->getOptions()]];
		return $validators;
	}
}