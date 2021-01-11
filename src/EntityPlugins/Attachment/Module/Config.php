<?php namespace Atomino\EntityPlugins\Attachment\Module;

use Atomino\Core\Application;

class Config{
	public function __construct(
		public string $path,
		public string $url,
		public string $imgUrl,
		public string $imgPath,
		public string $imgSecret,
		public int $imgJpegQuality,
	){
		$this->path = realpath(Application::ENV()->getRoot() . '/' . $this->path);
		$this->imgPath = realpath(Application::ENV()->getRoot() . '/' . $this->imgPath);
	}
}