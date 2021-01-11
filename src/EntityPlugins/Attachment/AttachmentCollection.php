<?php namespace Atomino\EntityPlugins\Attachment;

use Atomino\Entity\Plugin\PluginAttributeInterface;
use Atomino\Molecule\Attr;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class AttachmentCollection extends Attr implements PluginAttributeInterface{
	public function __construct(
		public string $field,
		public int $maxCount = 0,
		public int $maxSize = 0,
		public string|null $mimetype = null,
	){}
}
