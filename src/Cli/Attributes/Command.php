<?php namespace Atomino\Cli\Attributes;

use Atomino\Neutrons\Attr;
use Attribute;

#[Attribute( Attribute::TARGET_METHOD )]
class Command extends Attr{

	public function __construct(private string $name, private string|null $alias = null, private string|null $description = null){

	}

	public function getName(): string{ return $this->name; }
	public function getAlias(): ?string{ return $this->alias; }
	public function getDescription(): ?string{ return $this->description; }

}