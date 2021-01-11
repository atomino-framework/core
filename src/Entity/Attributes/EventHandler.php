<?php namespace Atomino\Entity\Attributes;

use Atomino\Molecule\Attr;
use Attribute;


#[Attribute(Attribute::TARGET_METHOD)]
class EventHandler extends Attr{
	public array $events = [];
	public function __construct(string ...$events){
		$this->events = $events;
	}
}
