<?php namespace Atomino\EntityPlugins\Updated;

use Atomino\Entity\Attributes\EventHandler;
use Atomino\Entity\Entity;

trait UpdatedTrait{
	#[EventHandler(Entity::EVENT_BEFORE_INSERT, Entity::EVENT_BEFORE_UPDATE)]
	protected function UpdatedPlugin_BeforeInsert($event, $data){
		$this->{UpdatedPlugin::fetch(static::model())->field} = new \DateTime();
	}
}