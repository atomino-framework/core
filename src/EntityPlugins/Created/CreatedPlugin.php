<?php namespace Atomino\EntityPlugins\Created;

use Atomino\Entity\Generator\CodeWriter;
use Atomino\Entity\Plugin\Plugin;

#[\Attribute(\Attribute::TARGET_CLASS)]
class CreatedPlugin extends Plugin{
	public function __construct(public $field = 'created'){ }
	public function generate(\ReflectionClass $ENTITY, CodeWriter $codeWriter){
		$codeWriter->addAttribute('#[Immutable("'.$this->field.'", true)]');
		$codeWriter->addAttribute('#[Protect("'.$this->field.'", true, false)]');
		$codeWriter->addAttribute('#[RequiredField("'.$this->field.'", \Atomino\Entity\Field\DateTimeField::class)]');
	}
	public function getTrait():string|null{ return CreatedTrait::class;}
}