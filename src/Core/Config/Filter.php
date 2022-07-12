<?php

namespace Atomino\Core\Config;

class Filter {
	public function __construct(private array $files) { }
	public function exclude(string $exclude){
		$this->files = array_filter($this->files, fn($item) => !str_contains($item, $exclude));
		return $this;
	}
	public function localOverride($local = "@local.php"){
		$this->files = array_filter($this->files, function ($item) use ($local) {
			$filename = pathinfo($item, PATHINFO_DIRNAME) . '/' . pathinfo($item, PATHINFO_FILENAME);
			return ((array_search($filename . $local, $this->files)) !== false) ? false : true;
		});
		return $this;
	}
	public function files():array{return $this->files;}
}