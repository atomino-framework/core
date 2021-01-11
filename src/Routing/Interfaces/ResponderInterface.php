<?php
namespace Atomino\Routing\Interfaces;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


interface ResponderInterface{
	public function exec(Response $response, Request $request, ParameterBag $hostBag, ParameterBag $pathBag, ParameterBag $argsBag):Response;
}