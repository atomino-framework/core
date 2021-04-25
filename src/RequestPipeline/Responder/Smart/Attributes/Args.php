<?php namespace Atomino\RequestPipeline\Responder\Smart\Attributes;

use Atomino\Neutrons\Attr;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Args extends Attr {
	public function __construct(
		protected string $title = 'Atomino',
		protected string $language = 'HU',
		protected string $class = '',
		protected string $favicon = '/~favicon/'
	) {
	}

	public function set(&$args) {
		if (is_null($args['title'])) $args['title'] = $this->title;
		if (is_null($args['language'])) $args['language'] = $this->language;
		if (is_null($args['class'])) $args['class'] = $this->class;
		if (is_null($args['favicon'])) $args['favicon'] = $this->favicon;
	}

}
