<?php namespace Atomino\EntityPlugins\Attachment\Module;
/**
 * @property-read int|null $id
 * @property array $attachments
 * @method Collection|null getAttachmentCollection(string $name)
 */
interface EntityAttachmentInterface{
	const EVENT_ATTACHMENT_ADDED = 'EVENT_ATTACHMENT_ADDED';
}