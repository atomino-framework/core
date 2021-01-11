<?php namespace Atomino\EntityPlugins\Guid;

use Atomino\Entity\Generator\CodeWriter;
use Atomino\Entity\Plugin\Plugin;

#[\Attribute(\Attribute::TARGET_CLASS)]
class GuidPlugin extends Plugin{
	public function __construct(public string $field = "guid"){ }
	public function generate(\ReflectionClass $ENTITY, CodeWriter $codeWriter){
		$codeWriter->addAnnotation("#[Immutable( '" . $this->field . "', true )]");
		$codeWriter->addAnnotation("#[Protect( '" . $this->field . "', true, false )]");
		$codeWriter->addAnnotation("#[RequiredField('" . $this->field . "', StringField::class)]");
	}
	public function getTrait(): string|null{ return GuidTrait::class; }
}