<?php namespace Atomino\Responder\SmartResponder\Attributes;

use Attribute;


#[Attribute( Attribute::TARGET_CLASS )]
class Template{
	public function __construct(protected string $template){ }
	public function set(&$template){ if (is_null($template)) $template = $this->template; }
}