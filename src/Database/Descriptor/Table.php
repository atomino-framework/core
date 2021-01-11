<?php namespace Atomino\Database\Descriptor;

use Atomino\Database\Connection;
use Atomino\Database\Descriptor\Field\Field;

class Table{

	/** @var Field[] */
	private array $fields = [];
	private ?Field $primary = null;

	public function __construct(private Connection $connection, private string $name, private string $type, private string $database){
		$fieldList = $this->connection
			->query("SELECT * FROM information_schema.columns WHERE table_name = '" . $name . "' AND table_schema = '" . $database . "'")
			->fetchAll(\PDO::FETCH_ASSOC);
		foreach ($fieldList as $descriptor){
			$field = Field::create($descriptor);
			if (!is_null($field) && $field->isPrimary()) $this->primary = $field;
			$this->fields[$descriptor["COLUMN_NAME"]] = $field;
		}
	}

	public function isView(){ return $this->type === 'VIEW'; }
	public function isTable(){ return $this->type !== 'VIEW'; }
	public function getName(): string{ return $this->name; }
	/** @return \Atomino\Database\Descriptor\Field\Field[] */
	public function getFields(): array{ return $this->fields; }
	public function getField($field): ?Field{ return array_key_exists($field, $this->fields) ? $this->fields[$field] : null; }
	public function getPrimary(): ?Field{ return $this->primary; }

}
