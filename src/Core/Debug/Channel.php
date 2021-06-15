<?php namespace Atomino\Core\Debug;

use Atomino\Neutrons\Attr;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD)]
class Channel extends Attr {
	public $channels;
	public function __construct(string ...$channels) { $this->channels = $channels; }
}