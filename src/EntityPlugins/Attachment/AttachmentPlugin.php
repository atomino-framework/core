<?php namespace Atomino\EntityPlugins\Attachment;

use Atomino\Entity\Generator\CodeWriter;
use Atomino\Entity\Model;
use Atomino\Entity\Plugin\Plugin;
use Atomino\EntityPlugins\Attachment\Module\EntityAttachmentInterface;

#[\Attribute( \Attribute::TARGET_CLASS )]
class AttachmentPlugin extends Plugin{

	/** @var \Atomino\EntityPlugins\Attachment\AttachmentCollection[] */
	private array $collections;
	public function __construct(public string $field = 'attachments'){ }

	protected function init(Model $model){
		$this->collections = AttachmentCollection::all($model->getEntityReflection());
	}

	public function generate(\ReflectionClass $ENTITY, CodeWriter $codeWriter){
		$collections = AttachmentCollection::all($ENTITY);
		$codeWriter->addInterface(EntityAttachmentInterface::class);
		$codeWriter->addAttribute("#[Immutable( '" . $this->field . "', true )]");
		$codeWriter->addAttribute("#[Protect( '" . $this->field . "', false, false )]");
		$codeWriter->addAttribute("#[RequiredField( '" . $this->field . "', \Atomino\Entity\Field\JsonField::class )]");

		foreach ($collections as $collection){
			$codeWriter->addAnnotation("@property-read \Atomino\EntityPlugins\Attachment\Module\Collection \$" . $collection->field);
			$codeWriter->addCode("protected final function __get" . ucfirst($collection->field) . '(){return $this->getAttachmentCollection("' . $collection->field . '");}');
		}
	}
	public function getTrait(): string|null{ return AttachmentTrait::class; }

	/** @return \Atomino\EntityPlugins\Attachment\AttachmentCollection[] */
	public function getCollections(): array{ return $this->collections; }
}