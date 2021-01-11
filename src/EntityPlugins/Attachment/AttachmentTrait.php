<?php namespace Atomino\EntityPlugins\Attachment;

use Atomino\Entity\Attributes\EventHandler;
use Atomino\Entity\Entity;
use Atomino\EntityPlugins\Attachment\Module\Collection;
use Atomino\EntityPlugins\Attachment\Module\Storage;

trait AttachmentTrait{

	private Storage|null $AttachmentPlugin_storage = null;
	private array $AttachmentPlugin_stored_attachments = ['files' => [], 'collections' => []];

	#[EventHandler( Entity::EVENT_ON_LOAD )]
	protected function AttachmentPlugin_onLoad(){
		$this->AttachmentPlugin_stored_attachments = $this->{AttachmentPlugin::fetch(static::model())->field};
	}

	#[EventHandler( Entity::EVENT_BEFORE_UPDATE, Entity::EVENT_BEFORE_INSERT )]
	protected function AttachmentPlugin_BeforeSave(){
		$this->{AttachmentPlugin::fetch(static::model())->field} = is_null($this->AttachmentPlugin_storage) ? $this->AttachmentPlugin_stored_attachments : $this->AttachmentPlugin_storage->jsonSerialize();
	}

	#[EventHandler( Entity::EVENT_BEFORE_DELETE)]
	protected function AttachmentPlugin_BeforeDelete(){
		$this->getAttachmentStorage()->purge();
	}

	protected function getAttachmentCollection(string $name): Collection|null{
		if (is_null($this->id)) return null;
		return $this->getAttachmentStorage()->getCollection($name);
	}

	public function getAttachmentStorage(): Storage{
		/** @var Entity $entity */
		$entity = $this;
		return
			is_null($this->AttachmentPlugin_storage) ?
				$this->AttachmentPlugin_storage = new Storage($entity, $this->AttachmentPlugin_stored_attachments) :
				$this->AttachmentPlugin_storage;
	}
}