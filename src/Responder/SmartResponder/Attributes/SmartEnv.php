<?php namespace Atomino\Responder\SmartResponder\Attributes;

use Atomino\Core\Application;
use Atomino\Responder\SmartResponder\SmartResponderEnv;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class SmartEnv{
	public function __construct(private string $name){	}
	public function getEnv():array{return Application::DIC()->get(SmartResponderEnv::class)->getEnv($this->name);}
}
