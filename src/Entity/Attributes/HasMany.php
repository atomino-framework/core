<?php namespace Atomino\Entity\Attributes;

use Atomino\Database\Finder\Filter;
use Atomino\Entity\Entity;
use Atomino\Entity\Field\JsonField;
use Atomino\Entity\Finder;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class HasMany extends Relation{
	public function fetch(Entity $item):Finder{
		if($item::model()->getField($this->field) instanceof JsonField){
			return ($this->entity)::search(Filter::where('JSON_CONTAINS(`'.$this->field.'`, $1, "$")', $item->id));
		}else{
			return ($this->entity)::search(Filter::where($this->field.'=$1', $item->id));
		}
	}
}
