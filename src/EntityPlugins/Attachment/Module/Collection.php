<?php namespace Atomino\EntityPlugins\Attachment\Module;

use Atomino\EntityPlugins\Attachment\AttachmentCollection;
use Exception;
use JetBrains\PhpStorm\Deprecated;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @property-read string[] $files
 * @property-read Storage $storage
 * @property-read string $name
 * @property-read \Atomino\EntityPlugins\Attachment\Module\Attachment|null $first
 * @property-read int $count
 */
class Collection implements \Countable, \IteratorAggregate, \ArrayAccess{

	private string $name;
	public function __toString(): string{ return $this->name; }

	public function __construct(private AttachmentCollection $descriptor, private Storage $storage, private array &$files){
		$this->name = $descriptor->field;
	}

	public function __isset(string $name): bool{ return in_array($name, ['files', 'storage', 'name', 'count', 'first']); }
	public function __get(string $name){
		return match ( $name ) {
			'files' => $this->files,
			'storage' => $this->storage,
			'name' => $this->descriptor->field,
			'count' => $this->count(),
			'first' => $this->get(),
			default => null
		};
	}

	protected function persist(){ $this->storage->persist(); }
	protected function begin(){ $this->storage->begin(); }
	protected function commit(){ $this->storage->commit(); }

	public function addFile(File $file): string{
		$this->begin();
		$filename = $this->storage->addFile($file);
		$this->add($filename);
		$this->commit();
		return $filename;
	}
	public function add(string $filename){
		if ($this->storage->has($filename) && !in_array($filename, $this->files)){
			$file = $this->storage->getAttachment($filename);
			if ($this->descriptor->maxSize !== 0 && $file->size > $this->descriptor->maxSize) throw new Exception("File size too big. Max allowed: " . $this->descriptor->maxSize);
			if ($this->descriptor->maxCount !== 0 && count($this->files) >= $this->descriptor->maxCount) throw new Exception("Collection can store only " . $this->descriptor->maxCount . " files");
			if ($this->descriptor->mimetype !== null && !preg_match($this->descriptor->mimetype, $file->mimetype)) throw new Exception("File mimetype mismatch");
			$this->files[] = $filename;
			$this->persist();
		}
	}
	public function remove(string $filename){
		if (( $index = array_search($filename, $this->files) ) !== false){
			array_splice($this->files, $index, 1);
			$this->persist();
		}
	}
	public function order(string $filename, int $serial){
		if (!$this->storage->has($filename)) return;
		$this->begin();
		if ($serial < 0) $serial = 0;
		if ($serial > count($this->files)) $serial = count($this->files);
		$this->remove($filename);
		array_splice($this->files, $serial, 0, $filename);
		$this->commit();
	}

	/**
	 * @param string $pattern as glob pattern
	 * @return \Atomino\EntityPlugins\Attachment\Module\Attachment[]
	 */
	public function find(string $pattern):array{
		$result = [];
		foreach ($this->files as $filename){
			if (fnmatch($pattern, $filename)) $result[] = $this->storage->getAttachment($filename);
		}
		return $result;
	}

	/**
	 * @param string $mimetype as glob pattern
	 * @return \Atomino\EntityPlugins\Attachment\Module\Attachment[]
	 */
	public function filter(string $mimetype):array{
		$result = [];
		foreach ($this->files as $filename){
			if (fnmatch($mimetype, ( $file = $this->storage->getAttachment($filename))->mimetype)) $result[] = $file;
		}
		return $result;
	}

	#[Pure] public function get(string|null $filename = null): Attachment|null{
		if (!count($this->files)) return null;
		if (is_null($filename)) return $this->storage->getAttachment($this->files[0]);
		if (in_array($filename, $this->files)) return $this->storage->getAttachment($filename);
		return null;
	}

	// Countable
	#[Pure] public function count(): int{ return count($this->files); }

	// IteratorAggregate
	public function getIterator(): CollectionIterator{ return new CollectionIterator($this); }

	// ArrayAccess
	public function offsetGet(mixed $offset): Attachment|null{
		if (is_numeric($offset)) return $this->storage->getAttachment($this->files[$offset]);
		return $this->get($offset);
		//if(is_numeric($offset)) return $this->lazyLoad() ?: $this->attachments[$offset];
		//return $this->__get($offset);
	}

	#[Pure] public function offsetExists(mixed $offset): bool{
		if (is_numeric($offset)) return array_key_exists($offset, $this->files);
		return in_array($offset, $this->files);
	}

	#[Deprecated( 'OUT OF ORDER' )] public function offsetSet($offset, $value){ }
	#[Deprecated( 'OUT OF ORDER' )] public function offsetUnset($offset){ }
}