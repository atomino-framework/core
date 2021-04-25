<?php namespace Atomino\RequestPipeline\Pipeline;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface Nextable {
	public function next(Request $request): Response|null;
}