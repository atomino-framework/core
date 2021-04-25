<?php namespace Atomino\RequestPipeline\FileServer;

use Atomino\RequestPipeline\Pipeline\Handler;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mime\MimeTypes;

class FileServer extends Handler {
	public function handle(Request $request): Response {

		$file = $request->attributes->get('file');
		if (!file_exists($file)) return new Response(null, 404);

		BinaryFileResponse::trustXSendfileTypeHeader();
		$file = new File($file);
		$response = new BinaryFileResponse($file);
		$response->headers->set('Content-Disposition', $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $file->getFilename()));
		if (is_array($mimetypes = (new MimeTypes())->getMimeTypes($file->getExtension())) && count($mimetypes)) $response->headers->set('Content-Type', $mimetypes[0]);

		return $response;
	}
}