<?php namespace Atomino\EntityPlugins\Updated;

use Atomino\Entity\Generator\CodeWriter;
use Atomino\Entity\Plugin\Plugin;

#[\Attribute(\Attribute::TARGET_CLASS)]
class UpdatedPlugin extends Plugin{
	public function __construct(public $field = 'updated'){ }
	public function generate(\ReflectionClass $ENTITY, CodeWriter $codeWriter){
		$codeWriter->addAttribute('#[Protect("'.$this->field.'", true, false)]');
		$codeWriter->addAttribute('#[RequiredField("'.$this->field.'", \Atomino\Entity\Field\DateTimeField::class)]');
	}
	public function getTrait():string|null{ return UpdatedTrait::class;}
}