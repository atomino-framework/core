<?php namespace Atomino\RequestPipeline\Responder\Api\Attributes;

use Atomino\Molecules\Module\Authenticator\Authenticator;
use Atomino\Neutrons\Attr;
use Attribute;
use JetBrains\PhpStorm\Pure;
use function Atomino\dic;

#[Attribute( Attribute::TARGET_METHOD )]
class Auth extends Attr{

	#[Pure] public function __construct(public string|false|null $role = null){ }
	public function authCheck(): bool{ return $this->role === false  || dic()->get(Authenticator::class)->isAuthenticated(); }
	public function roleCheck(): bool{
		$authenticator = dic()->get(Authenticator::class);
		return
			is_null($this->role) ||
			( $this->role === $authenticator->isAuthenticated() ) ||
			(dic()->get(Authenticator::class)->get()->hasRole($this->role))
		;
	}

}