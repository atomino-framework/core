<?php namespace Atomino\Responder\SmartResponder\Attributes;

use Attribute;
use Twig\Loader\FilesystemLoader;


#[Attribute(Attribute::TARGET_CLASS)]
class Args{
	public function __construct(
		protected string $title = 'Atomino',
		protected string $language = 'HU',
		protected string $bodyClass='',
		protected string $favicon = '/~favicon/'
	){}

	public function set(&$args){
		if (!array_key_exists('title', $args)) $args['title'] = $this->title;
		if (!array_key_exists('language', $args)) $args['language'] = $this->language;
		if (!array_key_exists('bodyClass', $args)) $args['bodyClass'] = $this->bodyClass;
		if (!array_key_exists('favicon', $args)) $args['favicon'] = $this->favicon;
	}

}
