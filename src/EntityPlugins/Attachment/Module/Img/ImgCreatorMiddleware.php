<?php namespace Atomino\EntityPlugins\Attachment\Module\Img;

use Atomino\Core\Application;
use Atomino\EntityPlugins\Attachment\Module\Config;
use Atomino\Responder\Middleware;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Response;

class ImgCreatorMiddleware extends Middleware{

	public function __construct(private ImgCreatorInterface $creator){ }

	protected function respond(Response $response): Response{

		$config = Application::DIC()->get(Config::class);
		if (!is_dir($config->imgPath)) mkdir($config->imgPath, 0777, true);

		$uri = explode('/', $this->getRequest()->getRequestUri());
		$uri = urldecode(array_pop($uri));
		$target = $config->imgPath . '/' . $uri;
		if (file_exists($target)) return $this->next($response);

		#region parse uri
		$parts = explode('.', $uri);
		$ext = array_pop($parts);
		$hash = array_pop($parts);
		$path = $pathId = array_pop($parts);
		$jpegQuality = ( $ext === 'jpg' || $ext === 'webp' ) ? array_pop($parts) : null;
		$opCode = array_pop($parts);
		$op = $this->parseOp($opCode);
		#endregion

		#region source file path
		$file = join('.', $parts);
		$path = substr_replace($path, '/', -6, 0);
		$path = substr_replace($path, '/', -4, 0);
		$path = substr_replace($path, '/', -2, 0);
		$source = realpath($config->path . '/' . $path . '/' . $file);
		if (!file_exists($source)) return $this->notfound($response);
		#endregion

		#region check hash
		$url = $file . '.' . $opCode . ( ( $jpegQuality ) ? ( '.' . $jpegQuality ) : ( '' ) ) . '.' . $pathId . '.' . $ext;
		$newHash = base_convert(crc32($url . $config->imgSecret), 10, 32);
		if ($newHash != $hash) return $this->notfound($response);
		#endregion

		if ( !match ( $op['op'] ) {
			'c' => $this->creator->crop($op['width'], $op['height'], $source, $target, $jpegQuality),
			'h' => $this->creator->height($op['width'], $op['height'], $source, $target, $jpegQuality),
			'w' => $this->creator->width($op['width'], $op['height'], $source, $target, $jpegQuality),
			's' => $this->creator->scale($op['width'], $op['height'], $source, $target, $jpegQuality),
			'b' => $this->creator->box($op['width'], $op['height'], $source, $target, $jpegQuality),
			default => null,
		}) return $this->notfound($response);

		return $this->next($response);
	}

	#[ArrayShape( ['op' => "string", 'width' => "int", 'height' => "int"] )]
	private function parseOp(string $op): array{
		$operation = substr($op, 0, 1);
		$op = substr($op, 1);
		$argLength = strlen($op) / 2;
		return [
			'op'     => $operation,
			'width'  => (int)base_convert(substr($op, 0, $argLength), 32, 10),
			'height' => (int)base_convert(substr($op, $argLength), 32, 10),
		];
	}

	private function notfound(Response $response){
		$response->setStatusCode(404);
		return $response;
	}

}