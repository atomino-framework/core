<?php namespace Atomino\Entity\Attributes;

use Atomino\Entity\Entity;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class BelongsTo extends Relation{

	public function fetch(Entity $item){
		return ($this->entity)::pick($item->{$this->field});
	}

}
