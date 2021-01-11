<?php namespace Atomino\EntityPlugins\Attachment\Module;

use Atomino\Core\Application;
use Atomino\EntityPlugins\Attachment\Module\Img\Img;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @property-read string $url
 * @property-read string $path
 * @property-read string $mimetype
 * @property-read string $filename
 * @property-read string $title
 * @property-read int $size
 * @property-read File $file
 * @property-read \Atomino\EntityPlugins\Attachment\Module\Storage $storage
 * @property-read bool $isImage
 * @property-read \Atomino\EntityPlugins\Attachment\Module\Img\Img $image
 */
class Attachment implements \JsonSerializable{

	public function __construct(
		private Storage $storage,
		private string $filename,
		private int $size,
		private string $mimetype,
		private string $title = '',
		private array $properties = []
	){
	}

	public function __isset(string $name): bool{ return in_array($name, ['mimetype', 'filename', 'title', 'size', 'url', 'path', 'storage', 'isImage', 'file', 'image']); }

	public function __get(string $name){
		return match ( $name ) {
			'mimetype' => $this->mimetype,
			'filename' => $this->filename,
			'title' => $this->title,
			'size' => $this->size,
			'url' => $this->storage->url . $this->filename,
			'path' => $this->storage->path . $this->filename,
			'storage' => $this->storage,
			'isImage' => str_starts_with($this->mimetype, 'image/'),
			'file' => new File($this->path),
			'image' => new Img($this),
			default => null
		};
	}

	public function delete(){
		$this->deleteImages();
		$this->storage->delete($this->filename);
	}

	public function deleteImages(){
		var_dump(Application::DIC()->get(Config::class)->imgPath.'/*.*.'.str_replace('/','',$this->storage->subPath).'.*.*');
		$files = glob(Application::DIC()->get(Config::class)->imgPath.'/*.*.'.str_replace('/','',$this->storage->subPath).'.*.*');
		foreach ($files as $file) unlink($file);
	}

	public function rename($newName){ $this->storage->rename($this->filename, $newName); }


	#region property get / set
	public function getProperties(): array{ return $this->properties; }
	public function getProperty(string $name): string|null{ return array_key_exists($name, $this->properties) ? $this->properties[$name] : null; }
	public function setProperty(string $name, string|null $value){
		if (!is_null($value)) $this->properties[$name] = $value;
		elseif (array_key_exists($name, $this->properties)) unset($this->properties[$name]);
		$this->storage->persist();
	}
	#endregion

	public function jsonSerialize(){
		return [
			'size'       => $this->size,
			'mimetype'   => $this->mimetype,
			'title'      => $this->title,
			'properties' => $this->properties,
		];
	}
}