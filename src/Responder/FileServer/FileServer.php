<?php namespace Atomino\Responder\FileServer;

use Atomino\Responder\Responder;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class FileServer extends Responder{
	protected function respond(Response $response): Response{
		$file = $this->getAttributesBag()->get('file');
		if(!file_exists($file)){
			$response->setStatusCode(404);
			return $response;
		}
		BinaryFileResponse::trustXSendfileTypeHeader();
		$file = new File($file);
		$disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $file->getFilename());
		$response = new BinaryFileResponse($file);
		$response->headers->set('Content-Disposition', $disposition);
		$response->headers->set('Content-Type', $file->getMimeType());
		return $response;
	}
}