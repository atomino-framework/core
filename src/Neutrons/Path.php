<?php namespace Atomino\Neutrons;


class Path {
	public function __construct(private string $path) { $this->path = ltrim($path, '/'); }
	public function __toString(): string { return getenv("@root") . '/' . $this->path; }
	public static function __set_state($array) { return new static($array['path']); }
	public function getRelative() { return $this->path; }
}